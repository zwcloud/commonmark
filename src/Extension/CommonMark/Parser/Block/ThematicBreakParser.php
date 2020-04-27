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

use League\CommonMark\Extension\CommonMark\Node\Block\ThematicBreak;
use League\CommonMark\Node\Block\AbstractBlock;
use League\CommonMark\Parser\Block\AbstractBlockParser;
use League\CommonMark\Parser\Block\BlockContinue;
use League\CommonMark\Parser\Block\BlockParserInterface;
use League\CommonMark\Parser\Cursor;

final class ThematicBreakParser extends AbstractBlockParser
{
    /** @var ThematicBreak */
    private $block;

    public function __construct()
    {
        $this->block = new ThematicBreak();
    }

    /**
     * @return ThematicBreak
     */
    public function getBlock(): AbstractBlock
    {
        return $this->block;
    }

    public function tryContinue(Cursor $cursor, BlockParserInterface $activeBlockParser): ?BlockContinue
    {
        // a horizontal rule can never container > 1 line, so fail to match
        return BlockContinue::none();
    }
}
