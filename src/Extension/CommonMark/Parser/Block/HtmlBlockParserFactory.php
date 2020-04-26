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

use League\CommonMark\Node\Block\Paragraph;
use League\CommonMark\Parser\Block\BlockParserFactoryInterface;
use League\CommonMark\Parser\Block\BlockStart;
use League\CommonMark\Parser\Block\DocParserStateInterface;
use League\CommonMark\Parser\Cursor;
use League\CommonMark\Util\RegexHelper;

final class HtmlBlockParserFactory implements BlockParserFactoryInterface
{
    public function tryStart(Cursor $cursor, DocParserStateInterface $parserState): ?BlockStart
    {
        if ($cursor->isIndented() || $cursor->getNextNonSpaceCharacter() !== '<') {
            return BlockStart::none();
        }

        $cursor = clone $cursor;

        $cursor->advanceToNextNonSpaceOrTab();
        $line = $cursor->getRemainder();

        for ($blockType = 1; $blockType <= 7; $blockType++) {
            $match = RegexHelper::matchAt(
                RegexHelper::getHtmlBlockOpenRegex($blockType),
                $line
            );

            if ($match !== null && ($blockType < 7 || !($parserState->getLastMatchedBlockParser()->getBlock() instanceof Paragraph))) {
                return BlockStart::of(new HtmlBlockParser($blockType))->at($cursor);
            }
        }

        return BlockStart::none();
    }
}
