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
use League\CommonMark\Parser\CursorState;

/**
 * Result object for starting parsing of a block; see static methods for constructors
 */
final class BlockStart
{
    /** @var BlockParserInterface[] */
    private $blockParsers = [];
    /** @var CursorState|null */
    private $cursorState = null;
    /** @var bool */
    private $replaceActiveBlockParser = false;

    private function __construct(BlockParserInterface ...$blockParsers)
    {
        $this->blockParsers = $blockParsers;
    }

    /**
     * @return BlockParserInterface[]
     */
    public function getBlockParsers(): iterable
    {
        return $this->blockParsers;
    }

    public function getCursorState(): ?CursorState
    {
        return $this->cursorState;
    }

    public function isReplaceActiveBlockParser(): bool
    {
        return $this->replaceActiveBlockParser;
    }

    /**
     * Signal that we want to parse at the given cursor position
     *
     * @param Cursor $cursor
     *
     * @return $this
     */
    public function at(Cursor $cursor): self
    {
        $this->cursorState = $cursor->saveState();

        return $this;
    }

    /**
     * Signal that we want to replace the active block parser with this one
     *
     * @return $this
     */
    public function replaceActiveBlockParser(): self
    {
        $this->replaceActiveBlockParser = true;

        return $this;
    }

    /**
     * Signal that we cannot parse whatever is here
     *
     * @return null
     */
    public static function none(): ?self
    {
        return null;
    }

    /**
     * Signal that we'd like to register the given parser(s) so they can parse the current block
     *
     * @param BlockParserInterface ...$blockParsers
     *
     * @return self
     */
    public static function of(BlockParserInterface ...$blockParsers): self
    {
        return new self(...$blockParsers);
    }
}
