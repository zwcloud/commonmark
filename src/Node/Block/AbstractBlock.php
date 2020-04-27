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

namespace League\CommonMark\Node\Block;

use League\CommonMark\Node\Node;

/**
 * Block-level element
 *
 * @method parent() ?AbstractBlock
 */
abstract class AbstractBlock extends Node
{
    /**
     * Used for storage of arbitrary data.
     *
     * @var array<string, mixed>
     */
    public $data = [];

    /**
     * @var int
     */
    protected $startLine;

    /**
     * @var int
     */
    protected $endLine;

    protected function setParent(Node $node = null): void
    {
        if ($node && !$node instanceof self) {
            throw new \InvalidArgumentException('Parent of block must also be block (can not be inline)');
        }

        parent::setParent($node);
    }

    /**
     * @param int $startLine
     *
     * @return $this
     */
    public function setStartLine(int $startLine)
    {
        $this->startLine = $startLine;
        if (empty($this->endLine)) {
            $this->endLine = $startLine;
        }

        return $this;
    }

    /**
     * @return int
     */
    public function getStartLine(): int
    {
        return $this->startLine;
    }

    /**
     * @param int $endLine
     *
     * @return $this
     */
    public function setEndLine(int $endLine)
    {
        $this->endLine = $endLine;

        return $this;
    }

    /**
     * @return int
     */
    public function getEndLine(): int
    {
        return $this->endLine;
    }

    /**
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getData(string $key, $default = null)
    {
        return \array_key_exists($key, $this->data) ? $this->data[$key] : $default;
    }
}
