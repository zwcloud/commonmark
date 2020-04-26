<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * Original code based on the CommonMark JS reference parser (https://bitly.com/commonmark-js)
 *  - (c) John MacFarlane
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Parser\Block;

use League\CommonMark\Node\Block\AbstractBlock;
use League\CommonMark\Parser\Cursor;
use League\CommonMark\Parser\InlineParserEngineInterface;

/**
 * Interface for a block parser
 *
 * A block parser can only parse a single block instance. The current block being parsed is stored within this parser and
 * can be returned once parsing has completed. If you need to parse multiple blocks, instantiate a new block parser for each one.
 */
interface BlockParserInterface
{
    /**
     * Return whether we are parsing a container block
     *
     * @return bool
     */
    public function isContainer(): bool;

    /**
     * Return whether we are interested in possibly lazily parsing any subsequent lines
     *
     * @return bool
     */
    public function canHaveLazyContinuationLines(): bool;

    /**
     * Determine whether the current block being parsed can contain the given child block
     *
     * @param AbstractBlock $childBlock
     *
     * @return bool
     */
    public function canContain(AbstractBlock $childBlock): bool;

    /**
     * Return the current block being parsed by this parser
     *
     * @return AbstractBlock
     */
    public function getBlock(): AbstractBlock;

    /**
     * Attempt to parse the given line
     *
     * @param Cursor               $cursor
     * @param BlockParserInterface $activeBlockParser
     *
     * @return BlockContinue|null
     */
    public function tryContinue(Cursor $cursor, BlockParserInterface $activeBlockParser): ?BlockContinue;

    /**
     * Add the given line of text to the current block
     *
     * @param string $line
     */
    public function addLine(string $line): void;

    /**
     * Close and finalize the current block
     */
    public function closeBlock(): void;

    /**
     * Parse any inlines inside of the current block
     *
     * @param InlineParserEngineInterface $inlineParser
     */
    public function parseInlines(InlineParserEngineInterface $inlineParser): void;
}
