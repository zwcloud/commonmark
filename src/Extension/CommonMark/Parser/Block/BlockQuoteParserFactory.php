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

use League\CommonMark\Parser\Block\BlockParserFactoryInterface;
use League\CommonMark\Parser\Block\BlockStart;
use League\CommonMark\Parser\Block\DocParserStateInterface;
use League\CommonMark\Parser\Cursor;

final class BlockQuoteParserFactory implements BlockParserFactoryInterface
{
    public function tryStart(Cursor $cursor, DocParserStateInterface $parserState): ?BlockStart
    {
        if ($cursor->isIndented()) {
            return BlockStart::none();
        }

        if ($cursor->getNextNonSpaceCharacter() !== '>') {
            return BlockStart::none();
        }

        $cursor->advanceToNextNonSpaceOrTab();
        $cursor->advanceBy(1);
        $cursor->advanceBySpaceOrTab();

        return BlockStart::of(new BlockQuoteParser())->at($cursor);
    }
}
