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

namespace League\CommonMark\Node\Inline;

use League\CommonMark\Node\StringContainerInterface;

class AbstractStringContainer extends AbstractInline implements StringContainerInterface
{
    /**
     * @var string
     */
    protected $literal = '';

    /**
     * @param string               $contents
     * @param array<string, mixed> $data
     */
    public function __construct(string $contents = '', array $data = [])
    {
        $this->literal = $contents;
        $this->data = $data;
    }

    public function getLiteral(): string
    {
        return $this->literal;
    }

    public function setLiteral(string $contents): void
    {
        $this->literal = $contents;
    }
}