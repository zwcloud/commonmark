<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * Original code based on the CommonMark JS reference parser (https://bitly.com/commonmark-js)
 *  - (c) John MacFarlane
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Parser;

use League\CommonMark\Environment\EnvironmentInterface;
use League\CommonMark\Event\DocumentParsedEvent;
use League\CommonMark\Event\DocumentPreParsedEvent;
use League\CommonMark\Input\MarkdownInput;
use League\CommonMark\Node\Block\Document;
use League\CommonMark\Node\Block\Paragraph;
use League\CommonMark\Parser\Block\BlockParserFactoryInterface;
use League\CommonMark\Parser\Block\BlockParserInterface;
use League\CommonMark\Parser\Block\BlockStart;
use League\CommonMark\Parser\Block\DocumentBlockParser;
use League\CommonMark\Parser\Block\DocParserState;
use League\CommonMark\Parser\Block\ParagraphParser;
use League\CommonMark\Reference\ReferenceInterface;
use League\CommonMark\Reference\ReferenceMap;
use League\CommonMark\Util\RegexHelper;

final class DocParser implements DocParserInterface
{
    /** @var EnvironmentInterface */
    private $environment;

    /** @var int|float */
    private $maxNestingLevel;

    /** @var ReferenceMap */
    private $referenceMap;

    /** @var Cursor */
    private $cursor;

    /** @var array<int, BlockParserInterface> */
    private $allBlockParsers = [];

    /** @var array<int, BlockParserInterface> */
    private $activeBlockParsers = [];

    /**
     * @param EnvironmentInterface $environment
     */
    public function __construct(EnvironmentInterface $environment)
    {
        $this->environment = $environment;
        $this->maxNestingLevel = $environment->getConfig('max_nesting_level', \INF);
    }

    private function initialize(): void
    {
        $this->referenceMap = new ReferenceMap();
        $this->allBlockParsers = [];
        $this->activeBlockParsers = [];
    }

    /**
     * @param string $input
     *
     * @throws \RuntimeException
     *
     * @return Document
     */
    public function parse(string $input): Document
    {
        $this->initialize();

        $documentParser = new DocumentBlockParser($this->referenceMap);
        $this->activateBlockParser($documentParser);

        $preParsedEvent = new DocumentPreParsedEvent($documentParser->getBlock(), new MarkdownInput($input));
        $this->environment->dispatch($preParsedEvent);
        $markdown = $preParsedEvent->getMarkdown();

        foreach ($markdown->getLines() as $line) {
            $this->incorporateLine($line);
        }

        $this->finalizeBlocks($this->getActiveBlockParsers());
        $this->processInlines();

        $this->environment->dispatch(new DocumentParsedEvent($documentParser->getBlock()));

        return $documentParser->getBlock();
    }

    /**
     * Analyze a line of text and update the document appropriately. We parse markdown text by calling this on each
     * line of input, then finalizing the document.
     *
     * @param string $line
     */
    private function incorporateLine(string $line): void
    {
        $this->cursor = new Cursor($line);

        $matches = 1;
        foreach ($this->getActiveBlockParsers(1) as $blockParser) {
            $blockContinue = $blockParser->tryContinue($this->cursor, $this->getActiveBlockParser());
            if ($blockContinue === null) {
                break;
            }

            if ($blockContinue->isFinalize()) {
                $this->finalizeAndClose($blockParser);

                return;
            }

            $matches++;
        }

        $unmatchedBlockParsers = $this->getActiveBlockParsers($matches);
        $lastMatchedBlockParser = $this->getActiveBlockParsers()[$matches - 1];
        $blockParser = $lastMatchedBlockParser;
        $allClosed = empty($unmatchedBlockParsers);

        // Unless last matched container is a code block, try new container starts
        $tryBlockStarts = $blockParser->getBlock() instanceof Paragraph || $blockParser->isContainer();
        while ($tryBlockStarts) {
            // this is a little performance optimization
            if ($this->cursor->isBlank()) {
                $this->cursor->advanceToEnd();
                break;
            } elseif (!$this->cursor->isIndented() && RegexHelper::isLetter($this->cursor->getNextNonSpaceCharacter())) {
                $this->cursor->advanceToNextNonSpaceOrTab();
                break;
            }

            if ($blockParser->getBlock()->getDepth() >= $this->maxNestingLevel) {
                break;
            }

            $blockStart = $this->findBlockStart($blockParser);
            if ($blockStart === null) {
                $this->cursor->advanceToNextNonSpaceOrTab();
                break;
            }

            if (!$allClosed) {
                $this->finalizeBlocks($unmatchedBlockParsers);
                $allClosed = true;
            }

            if ($blockStart->isReplaceActiveBlockParser()) {
                $this->prepareActiveBlockParserForReplacement();
            }

            foreach ($blockStart->getBlockParsers() as $newBlockParser) {
                $blockParser = $this->addChild($newBlockParser);
                $tryBlockStarts = $newBlockParser->isContainer();
            }
        }

        // What remains ath the offset is a text line. Add the text to the appropriate block.

        // First check for a lazy paragraph continuation:
        if (!$allClosed && !$this->cursor->isBlank() && $this->getActiveBlockParser()->canHaveLazyContinuationLines()) {
            $this->getActiveBlockParser()->addLine($this->cursor->getRemainder());
        } else {
            // finalize any blocks not matched
            if (!$allClosed) {
                $this->finalizeBlocks($unmatchedBlockParsers);
            }

            if (!$blockParser->isContainer()) {
                $this->getActiveBlockParser()->addLine($this->cursor->getRemainder());
            } elseif (!$this->cursor->isBlank()) {
                $this->addChild(new ParagraphParser());
                $this->getActiveBlockParser()->addLine($this->cursor->getRemainder());
            }
        }
    }

    private function findBlockStart(BlockParserInterface $lastMatchedBlockParser): ?BlockStart
    {
        $matchedBlockParser = new DocParserState($this->getActiveBlockParser(), $lastMatchedBlockParser);

        /** @var BlockParserFactoryInterface $blockParserFactory */
        foreach ($this->environment->getBlockParserFactories() as $blockParserFactory) {
            if (($result = $blockParserFactory->tryStart($this->cursor, $matchedBlockParser)) !== null) {
                return $result;
            }
        }

        return null;
    }

    /**
     * @param array<int, BlockParserInterface> $blockParsers
     */
    private function finalizeBlocks(array $blockParsers): void
    {
        foreach (\array_reverse($blockParsers) as $blockParser) {
            $this->finalizeAndClose($blockParser);
        }
    }

    /**
     * Finalize a block. Close it and do any necessary postprocessing, e.g. creating string_content from strings,
     * setting the 'tight' or 'loose' status of a list, and parsing the beginnings of paragraphs for reference
     * definitions.
     */
    private function finalizeAndClose(BlockParserInterface $blockParser): void
    {
        if ($this->getActiveBlockParser() === $blockParser) {
            $this->deactivateBlockParser();
        }

        if ($blockParser instanceof ParagraphParser) {
            $this->updateReferenceMap($blockParser->getReferences());
        }

        $blockParser->closeBlock();
    }

    /**
     * Walk through a block & children recursively, parsing string content into inline content where appropriate.
     */
    private function processInlines(): void
    {
        $p = new InlineParserEngine($this->environment, $this->referenceMap);

        /** @var BlockParserInterface $blockParser */
        foreach ($this->allBlockParsers as $blockParser) {
            $blockParser->parseInlines($p);
        }
    }

    /**
     * Add block of type tag as a child of the tip. If the tip can't accept children, close and finalize it and try
     * its parent, and so on til we find a block that can accept children.
     */
    private function addChild(BlockParserInterface $blockParser): BlockParserInterface
    {
        while (!$this->getActiveBlockParser()->canContain($blockParser->getBlock())) {
            $this->finalizeAndClose($this->getActiveBlockParser());
        }

        $this->getActiveBlockParser()->getBlock()->appendChild($blockParser->getBlock());
        $this->activateBlockParser($blockParser);

        return $blockParser;
    }

    private function activateBlockParser(BlockParserInterface $blockParser): void
    {
        $this->activeBlockParsers[] = $blockParser;
        $this->allBlockParsers[] = $blockParser;
    }

    private function deactivateBlockParser(): BlockParserInterface
    {
        return \array_pop($this->activeBlockParsers);
    }

    private function prepareActiveBlockParserForReplacement(): void
    {
        $old = $this->deactivateBlockParser();
        $key = \array_search($old, $this->allBlockParsers, true);
        unset($this->allBlockParsers[$key]);

        if ($old instanceof ParagraphParser) {
            $this->updateReferenceMap($old->getReferences());
        }

        $old->getBlock()->detach();
    }

    /**
     * @param ReferenceInterface[] $references
     */
    private function updateReferenceMap(iterable $references): void
    {
        foreach ($references as $reference) {
            if (!$this->referenceMap->contains($reference->getLabel())) {
                $this->referenceMap->add($reference);
            }
        }
    }

    /**
     * @param int|null $offset
     *
     * @return array<int, BlockParserInterface>
     */
    private function getActiveBlockParsers(?int $offset = 0): array
    {
        if (\is_int($offset)) {
            return \array_slice($this->activeBlockParsers, $offset);
        }

        return $this->activeBlockParsers;
    }

    public function getActiveBlockParser(): BlockParserInterface
    {
        $active = \end($this->activeBlockParsers);
        if ($active === false) {
            throw new \RuntimeException('No active block parsers are available');
        }

        return $active;
    }
}
