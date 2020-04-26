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
use League\CommonMark\Util\RegexHelper;

final class ThematicBreakParserFactory implements BlockParserFactoryInterface
{
    public function tryStart(Cursor $cursor, DocParserStateInterface $parserState): ?BlockStart
    {
        if ($cursor->isIndented()) {
            return BlockStart::none();
        }

        $match = RegexHelper::matchAt(RegexHelper::REGEX_THEMATIC_BREAK, $cursor->getLine(), $cursor->getNextNonSpacePosition());
        if ($match === null) {
            return BlockStart::none();
        }

        // Advance to the end of the string, consuming the entire line (of the thematic break)
        $cursor->advanceToEnd();

        return BlockStart::of(new ThematicBreakParser())->at($cursor);
    }
}
