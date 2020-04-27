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

namespace League\CommonMark\Tests\Unit\Node;

use League\CommonMark\Node\Block\Document;
use League\CommonMark\Node\Block\Paragraph;
use League\CommonMark\Node\Inline\Text;
use PHPUnit\Framework\TestCase;

class NodeWalkerTest extends TestCase
{
    public function testWalkEmptyBlockNode()
    {
        $node = new Document();
        $walker = $node->walker();

        $event = $walker->next();
        $this->assertSame($node, $event->getNode());
        $this->assertTrue($event->isEntering());

        $event = $walker->next();
        $this->assertSame($node, $event->getNode());
        $this->assertFalse($event->isEntering());

        $event = $walker->next();
        $this->assertNull($event);

        $event = $walker->next();
        $this->assertNull($event);
    }

    public function testWalkEmptyInlineNode()
    {
        $node = new Text();
        $walker = $node->walker();

        $event = $walker->next();
        $this->assertSame($node, $event->getNode());
        $this->assertTrue($event->isEntering());

        $event = $walker->next();
        $this->assertNull($event);

        $event = $walker->next();
        $this->assertNull($event);
    }

    public function testWalkNestedNodes()
    {
        $document = new Document();
        $document->appendChild($paragraph = new Paragraph());
        $paragraph->appendChild($text = new Text());
        $walker = $document->walker();

        $event = $walker->next();
        $this->assertSame($document, $event->getNode());
        $this->assertTrue($event->isEntering());

        $event = $walker->next();
        $this->assertSame($paragraph, $event->getNode());
        $this->assertTrue($event->isEntering());

        $event = $walker->next();
        $this->assertSame($text, $event->getNode());
        $this->assertTrue($event->isEntering());

        $event = $walker->next();
        $this->assertSame($paragraph, $event->getNode());
        $this->assertFalse($event->isEntering());

        $event = $walker->next();
        $this->assertSame($document, $event->getNode());
        $this->assertFalse($event->isEntering());

        $event = $walker->next();
        $this->assertNull($event);

        $event = $walker->next();
        $this->assertNull($event);
    }

    public function testResumeAt()
    {
        $document = new Document();
        $document->appendChild($paragraph = new Paragraph());
        $paragraph->appendChild($text = new Text());
        $walker = $document->walker();

        $walker->next();
        $walker->next();

        $event = $walker->next();
        $this->assertSame($text, $event->getNode());
        $this->assertTrue($event->isEntering());

        $walker->resumeAt($text);
        $event = $walker->next();
        $this->assertSame($text, $event->getNode());
        $this->assertTrue($event->isEntering());

        $walker->resumeAt($paragraph, true);
        $event = $walker->next();
        $this->assertSame($paragraph, $event->getNode());
        $this->assertTrue($event->isEntering());

        $event = $walker->next();
        $this->assertSame($text, $event->getNode());
        $this->assertTrue($event->isEntering());
    }
}
