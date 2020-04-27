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

class HeadingParserFactory implements BlockParserFactoryInterface
{
    public function tryStart(Cursor $cursor, DocParserStateInterface $parserState): ?BlockStart
    {
        $cursor = clone $cursor;
        if ($cursor->isIndented()) {
            return BlockStart::none();
        }

        $cursor->advanceToNextNonSpaceOrTab();

        if ($atxHeading = self::getAtxHeader($cursor)) {
            return BlockStart::of($atxHeading)->at($cursor);
        }

        $setextHeadingLevel = self::getSetextHeadingLevel($cursor);
        if ($setextHeadingLevel > 0) {
            $content = $parserState->getParagraphContent();
            if ($content !== null) {
                $cursor->advanceToEnd();

                return BlockStart::of(new HeadingParser($setextHeadingLevel, $content))
                    ->at($cursor)
                    ->replaceActiveBlockParser();
            }
        }

        return BlockStart::none();
    }

    private static function getAtxHeader(Cursor $cursor): ?HeadingParser
    {
        $match = RegexHelper::matchAll('/^#{1,6}(?:[ \t]+|$)/', $cursor->getLine(), $cursor->getNextNonSpacePosition());
        if (!$match) {
            return null;
        }

        $cursor->advanceToNextNonSpaceOrTab();
        $cursor->advanceBy(\strlen($match[0]));

        $level = \strlen(\trim($match[0]));
        $str = $cursor->getRemainder();
        /** @var string $str */
        $str = \preg_replace('/^[ \t]*#+[ \t]*$/', '', $str);
        /** @var string $str */
        $str = \preg_replace('/[ \t]+#+[ \t]*$/', '', $str);

        return new HeadingParser($level, $str);
    }

    private static function getSetextHeadingLevel(Cursor $cursor): int
    {
        $match = RegexHelper::matchAll('/^(?:=+|-+)[ \t]*$/', $cursor->getLine(), $cursor->getNextNonSpacePosition());
        if ($match === null) {
            return 0;
        }

        return $match[0][0] === '=' ? 1 : 2;
    }
}
