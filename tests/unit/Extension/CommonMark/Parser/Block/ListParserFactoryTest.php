<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Tests\Unit\Extension\CommonMark\Parser\Block;

use League\CommonMark\Configuration\Configuration;
use League\CommonMark\Extension\CommonMark\Node\Block\ListBlock;
use League\CommonMark\Extension\CommonMark\Node\Block\ListItem;
use League\CommonMark\Extension\CommonMark\Parser\Block\ListBlockParserFactory;
use League\CommonMark\Parser\Block\DocParserStateInterface;
use League\CommonMark\Parser\Cursor;
use PHPUnit\Framework\TestCase;

final class ListParserFactoryTest extends TestCase
{
    public function testOrderedListStartingAtOne()
    {
        $cursor = new Cursor('1. Foo');

        $factory = new ListBlockParserFactory();
        $start = $factory->tryStart($cursor, $this->createMock(DocParserStateInterface::class));

        $this->assertNotNull($start);

        $parsers = $start->getBlockParsers();
        $this->assertCount(2, $parsers);

        /** @var ListBlock $block */
        $block = $parsers[0]->getBlock();
        $this->assertInstanceOf(ListBlock::class, $block);

        /** @var ListItem $item */
        $item = $parsers[1]->getBlock();
        $this->assertInstanceOf(ListItem::class, $item);

        $this->assertSame(ListBlock::TYPE_ORDERED, $block->getListData()->type);
        $this->assertSame(1, $block->getListData()->start);

        $this->assertSame(ListBlock::TYPE_ORDERED, $item->getListData()->type);
    }

    public function testOrderedListStartingAtTwo()
    {
        $cursor = new Cursor('2. Foo');

        $factory = new ListBlockParserFactory();
        $start = $factory->tryStart($cursor, $this->createMock(DocParserStateInterface::class));

        $this->assertNotNull($start);

        $parsers = $start->getBlockParsers();
        $this->assertCount(2, $parsers);

        /** @var ListBlock $block */
        $block = $parsers[0]->getBlock();
        $this->assertInstanceOf(ListBlock::class, $block);

        /** @var ListItem $item */
        $item = $parsers[1]->getBlock();
        $this->assertInstanceOf(ListItem::class, $item);

        $this->assertSame(ListBlock::TYPE_ORDERED, $block->getListData()->type);
        $this->assertSame(2, $block->getListData()->start);

        $this->assertSame(ListBlock::TYPE_ORDERED, $item->getListData()->type);
    }

    public function testUnorderedListWithDashMarker()
    {
        $cursor = new Cursor('- Foo');

        $factory = new ListBlockParserFactory();
        $start = $factory->tryStart($cursor, $this->createMock(DocParserStateInterface::class));

        $this->assertNotNull($start);

        $parsers = $start->getBlockParsers();
        $this->assertCount(2, $parsers);

        /** @var ListBlock $block */
        $block = $parsers[0]->getBlock();
        $this->assertInstanceOf(ListBlock::class, $block);

        /** @var ListItem $item */
        $item = $parsers[1]->getBlock();
        $this->assertInstanceOf(ListItem::class, $item);

        $this->assertSame(ListBlock::TYPE_BULLET, $block->getListData()->type);
        $this->assertSame('-', $block->getListData()->bulletChar);

        $this->assertSame(ListBlock::TYPE_BULLET, $item->getListData()->type);
    }

    public function testUnorderedListWithAsteriskMarker()
    {
        $cursor = new Cursor('* Foo');

        $factory = new ListBlockParserFactory();
        $start = $factory->tryStart($cursor, $this->createMock(DocParserStateInterface::class));

        $this->assertNotNull($start);

        $parsers = $start->getBlockParsers();
        $this->assertCount(2, $parsers);

        /** @var ListBlock $block */
        $block = $parsers[0]->getBlock();
        $this->assertInstanceOf(ListBlock::class, $block);

        /** @var ListItem $item */
        $item = $parsers[1]->getBlock();
        $this->assertInstanceOf(ListItem::class, $item);

        $this->assertSame(ListBlock::TYPE_BULLET, $block->getListData()->type);
        $this->assertSame('*', $block->getListData()->bulletChar);

        $this->assertSame(ListBlock::TYPE_BULLET, $item->getListData()->type);
    }

    public function testUnorderedListWithPlusMarker()
    {
        $cursor = new Cursor('+ Foo');

        $factory = new ListBlockParserFactory();
        $start = $factory->tryStart($cursor, $this->createMock(DocParserStateInterface::class));

        $this->assertNotNull($start);

        $parsers = $start->getBlockParsers();
        $this->assertCount(2, $parsers);

        /** @var ListBlock $block */
        $block = $parsers[0]->getBlock();
        $this->assertInstanceOf(ListBlock::class, $block);

        /** @var ListItem $item */
        $item = $parsers[1]->getBlock();
        $this->assertInstanceOf(ListItem::class, $item);

        $this->assertSame(ListBlock::TYPE_BULLET, $block->getListData()->type);
        $this->assertSame('+', $block->getListData()->bulletChar);

        $this->assertSame(ListBlock::TYPE_BULLET, $item->getListData()->type);
    }

    public function testUnorderedListWithCustomMarker()
    {
        $cursor = new Cursor('^ Foo');

        $factory = new ListBlockParserFactory();
        $factory->setConfiguration(new Configuration(['unordered_list_markers' => ['^']]));
        $start = $factory->tryStart($cursor, $this->createMock(DocParserStateInterface::class));

        $this->assertNotNull($start);

        $parsers = $start->getBlockParsers();
        $this->assertCount(2, $parsers);

        /** @var ListBlock $block */
        $block = $parsers[0]->getBlock();
        $this->assertInstanceOf(ListBlock::class, $block);

        /** @var ListItem $item */
        $item = $parsers[1]->getBlock();
        $this->assertInstanceOf(ListItem::class, $item);

        $this->assertSame(ListBlock::TYPE_BULLET, $block->getListData()->type);
        $this->assertSame('^', $block->getListData()->bulletChar);

        $this->assertSame(ListBlock::TYPE_BULLET, $item->getListData()->type);
    }

    public function testUnorderedListWithDisabledMarker()
    {
        $cursor = new Cursor('+ Foo');

        $factory = new ListBlockParserFactory();
        $factory->setConfiguration(new Configuration(['unordered_list_markers' => ['-', '*']]));
        $start = $factory->tryStart($cursor, $this->createMock(DocParserStateInterface::class));

        $this->assertNull($start);
    }

    public function testInvalidListMarkerConfiguration()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Invalid configuration option "unordered_list_markers": value must be an array of strings');

        $cursor = new Cursor('- Foo');

        $factory = new ListBlockParserFactory();
        $factory->setConfiguration(new Configuration(['unordered_list_markers' => '-']));
        $factory->tryStart($cursor, $this->createMock(DocParserStateInterface::class));
    }
}
