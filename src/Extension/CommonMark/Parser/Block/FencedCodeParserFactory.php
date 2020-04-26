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

final class FencedCodeParserFactory implements BlockParserFactoryInterface
{
    public function tryStart(Cursor $cursor, DocParserStateInterface $parserState): ?BlockStart
    {
        if ($cursor->isIndented()) {
            return BlockStart::none();
        }

        $c = $cursor->getCharacter();
        if ($c !== ' ' && $c !== "\t" && $c !== '`' && $c !== '~') {
            return BlockStart::none();
        }

        $indent = $cursor->getIndent();
        $fence = $cursor->match('/^[ \t]*(?:`{3,}(?!.*`)|^~{3,})/');
        if ($fence === null) {
            return BlockStart::none();
        }

        // fenced code block
        $fence = \ltrim($fence, " \t");
        $fenceLength = \strlen($fence);

        return BlockStart::of(new FencedCodeParser($fenceLength, $fence[0], $indent))->at($cursor);
    }
}
