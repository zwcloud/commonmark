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

use League\CommonMark\Extension\CommonMark\Node\Block\HtmlBlock;
use League\CommonMark\Node\Block\AbstractBlock;
use League\CommonMark\Parser\Block\AbstractBlockParser;
use League\CommonMark\Parser\Block\BlockContinue;
use League\CommonMark\Parser\Block\BlockParserInterface;
use League\CommonMark\Parser\Cursor;
use League\CommonMark\Util\RegexHelper;

final class HtmlBlockParser extends AbstractBlockParser
{
    /** @var HtmlBlock */
    private $block;

    /**
     * @var string
     */
    private $content = '';

    /** @var bool */
    private $finished = false;

    public function __construct(int $blockType)
    {
        $this->block = new HtmlBlock($blockType);
    }

    /**
     * @return HtmlBlock
     */
    public function getBlock(): AbstractBlock
    {
        return $this->block;
    }

    public function tryContinue(Cursor $cursor, BlockParserInterface $activeBlockParser): ?BlockContinue
    {
        if ($this->finished) {
            return BlockContinue::none();
        }

        if ($cursor->isBlank() && \in_array($this->block->getType(), [HtmlBlock::TYPE_6_BLOCK_ELEMENT, HtmlBlock::TYPE_7_MISC_ELEMENT], true)) {
            return BlockContinue::none();
        }

        return BlockContinue::atCursor($cursor);
    }

    public function addLine(string $line): void
    {
        if ($this->content !== '') {
            $this->content .= "\n";
        }

        $this->content .= $line;

        // Check for end condition
        if ($this->block->getType() >= HtmlBlock::TYPE_1_CODE_CONTAINER && $this->block->getType() <= HtmlBlock::TYPE_5_CDATA) {
            $cursor = new Cursor($line);
            if ($cursor->match(RegexHelper::getHtmlBlockCloseRegex($this->block->getType())) !== null) {
                $this->finished = true;
            }
        }
    }

    public function closeBlock(): void
    {
        $this->block->setLiteral($this->content);
        $this->content = '';
    }
}
