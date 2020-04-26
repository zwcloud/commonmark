<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Parser\Block;

/**
 * @internal
 */
final class DocParserState implements DocParserStateInterface
{
    /** @var BlockParserInterface */
    private $activeBlockParser;

    /** @var BlockParserInterface */
    private $lastMatchedBlockParser;

    public function __construct(BlockParserInterface $activeBlockParser, BlockParserInterface $lastMatchedBlockParser)
    {
        $this->activeBlockParser = $activeBlockParser;
        $this->lastMatchedBlockParser = $lastMatchedBlockParser;
    }

    public function getActiveBlockParser(): BlockParserInterface
    {
        return $this->activeBlockParser;
    }

    public function getLastMatchedBlockParser(): BlockParserInterface
    {
        return $this->lastMatchedBlockParser;
    }

    public function getParagraphContent(): ?string
    {
        if (!$this->lastMatchedBlockParser instanceof ParagraphParser) {
            return null;
        }

        /** @var ParagraphParser $paragraphParser */
        $paragraphParser = $this->lastMatchedBlockParser;
        $content = $paragraphParser->getContentString();

        return $content === '' ? null : $content;
    }
}
