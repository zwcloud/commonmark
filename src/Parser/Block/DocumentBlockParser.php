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

use League\CommonMark\Node\Block\AbstractBlock;
use League\CommonMark\Node\Block\Document;
use League\CommonMark\Parser\Cursor;
use League\CommonMark\Reference\ReferenceMapInterface;

/**
 * Parser implementation which ensures everything is added to the root-level Document
 */
final class DocumentBlockParser extends AbstractBlockParser
{
    /** @var Document */
    protected $document;

    public function __construct(ReferenceMapInterface $referenceMap)
    {
        $this->document = new Document($referenceMap);
    }

    /**
     * @return Document
     */
    public function getBlock(): AbstractBlock
    {
        return $this->document;
    }

    public function isContainer(): bool
    {
        return true;
    }

    public function canContain(AbstractBlock $childBlock): bool
    {
        return true;
    }

    public function tryContinue(Cursor $cursor, BlockParserInterface $activeBlockParser): ?BlockContinue
    {
        return BlockContinue::atCursor($cursor);
    }
}
