<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Extension\CommonMark\Parser\Block;

use League\CommonMark\Extension\CommonMark\Node\Block\IndentedCode;
use League\CommonMark\Node\Block\AbstractBlock;
use League\CommonMark\Parser\Block\AbstractBlockParser;
use League\CommonMark\Parser\Block\BlockContinue;
use League\CommonMark\Parser\Block\BlockParserInterface;
use League\CommonMark\Parser\Cursor;
use League\CommonMark\Util\ArrayCollection;

final class IndentedCodeParser extends AbstractBlockParser
{
    /** @var IndentedCode */
    private $block;

    /**
     * @var ArrayCollection<int, string>
     */
    protected $strings;

    public function __construct()
    {
        $this->block = new IndentedCode();
        $this->strings = new ArrayCollection();
    }

    /**
     * @return IndentedCode
     */
    public function getBlock(): AbstractBlock
    {
        return $this->block;
    }

    public function tryContinue(Cursor $cursor, BlockParserInterface $activeBlockParser): ?BlockContinue
    {
        if ($cursor->isIndented()) {
            $cursor->advanceBy(Cursor::INDENT_LEVEL, true);

            return BlockContinue::atCursor($cursor);
        }

        if ($cursor->isBlank()) {
            $cursor->advanceToNextNonSpaceOrTab();

            return BlockContinue::atCursor($cursor);
        }

        return BlockContinue::none();
    }

    public function addLine(string $line): void
    {
        $this->strings[] = $line;
    }

    public function closeBlock(): void
    {
        $reversed = \array_reverse($this->strings->toArray(), true);
        foreach ($reversed as $index => $line) {
            if ($line === '' || $line === "\n" || \preg_match('/^(\n *)$/', $line)) {
                unset($reversed[$index]);
            } else {
                break;
            }
        }
        $fixed = \array_reverse($reversed);
        $tmp = \implode("\n", $fixed);
        if (\substr($tmp, -1) !== "\n") {
            $tmp .= "\n";
        }

        $this->block->setLiteral($tmp);
    }
}
