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

use League\CommonMark\Parser\Cursor;

/**
 * Parser factory for a block node for determining when a block starts.
 */
interface BlockParserFactoryInterface
{
    /**
     * Check whether we should handle the block at the current position
     *
     * @param Cursor                  $cursor
     * @param DocParserStateInterface $parserState
     *
     * @return BlockStart|null
     */
    public function tryStart(Cursor $cursor, DocParserStateInterface $parserState): ?BlockStart;
}
