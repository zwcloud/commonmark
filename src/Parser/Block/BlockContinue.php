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
 * Result object for continuing parsing of a block; see static methods for constructors.
 */
final class BlockContinue
{
    /** @var CursorState|null */
    private $cursorState;

    /** @var bool */
    private $finalize;

    private function __construct(?CursorState $cursorState = null, bool $finalize = false)
    {
        $this->cursorState = $cursorState;
        $this->finalize = $finalize;
    }

    public function isFinalize(): bool
    {
        return $this->finalize;
    }

    /**
     * Signal that we cannot continue here
     *
     * @return null
     */
    public static function none(): ?self
    {
        return null;
    }

    /**
     * Signal that we're continuing at the given position
     *
     * @param Cursor $cursor
     *
     * @return self
     *
     * @deprecated
     *
     * @TODO: do we really need this? I don't think we're handling this properly!
     */
    public static function atCursor(Cursor $cursor): self
    {
        return new self($cursor->saveState(), false);
    }

    /**
     * Signal that we want to finalize and close the block
     *
     * @return self
     */
    public static function finished(): self
    {
        return new self(null, true);
    }
}
