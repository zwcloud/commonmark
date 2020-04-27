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

use League\CommonMark\Extension\CommonMark\Node\Block\BlockQuote;
use League\CommonMark\Node\Block\AbstractBlock;
use League\CommonMark\Parser\Block\AbstractBlockParser;
use League\CommonMark\Parser\Block\BlockContinue;
use League\CommonMark\Parser\Block\BlockParserInterface;
use League\CommonMark\Parser\Cursor;

final class BlockQuoteParser extends AbstractBlockParser
{
    /** @var BlockQuote */
    private $block;

    public function __construct()
    {
        $this->block = new BlockQuote();
    }

    /**
     * @return BlockQuote
     */
    public function getBlock(): AbstractBlock
    {
        return $this->block;
    }

    public function isContainer(): bool
    {
        return true;
    }

    public function canContain(AbstractBlock $childBlock): bool
    {
        return true;
    }

    public function tryContinue(Cursor $cursor, BlockParserInterface $activeBlockParser): ?BlockContinue
    {
        if (!$cursor->isIndented() && $cursor->getNextNonSpaceCharacter() === '>') {
            $cursor->advanceToNextNonSpaceOrTab();
            $cursor->advanceBy(1);
            $cursor->advanceBySpaceOrTab();

            return BlockContinue::atCursor($cursor);
        }

        return BlockContinue::none();
    }
}
