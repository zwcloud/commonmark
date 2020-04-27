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

namespace League\CommonMark\Extension\CommonMark;

use League\CommonMark\Environment\ConfigurableEnvironmentInterface;
use League\CommonMark\Extension\CommonMark\Delimiter\Processor\EmphasisDelimiterProcessor;
use League\CommonMark\Extension\ExtensionInterface;
use League\CommonMark\Node as CoreNode;
use League\CommonMark\Parser as CoreParser;
use League\CommonMark\Renderer as CoreRenderer;

final class CommonMarkCoreExtension implements ExtensionInterface
{
    public function register(ConfigurableEnvironmentInterface $environment): void
    {
        $environment
            ->addBlockParserFactory(new Parser\Block\BlockQuoteParserFactory(),     70)
            ->addBlockParserFactory(new Parser\Block\HeadingParserFactory(),        60)
            ->addBlockParserFactory(new Parser\Block\FencedCodeParserFactory(),     50)
            ->addBlockParserFactory(new Parser\Block\HtmlBlockParserFactory(),      40)
            ->addBlockParserFactory(new Parser\Block\ThematicBreakParserFactory(),  20)
            ->addBlockParserFactory(new Parser\Block\ListBlockParserFactory(),      10)
            ->addBlockParserFactory(new Parser\Block\IndentedCodeParserFactory(), -100)

            ->addInlineParser(new CoreParser\Inline\NewlineParser(), 200)
            ->addInlineParser(new Parser\Inline\BacktickParser(),    150)
            ->addInlineParser(new Parser\Inline\EscapableParser(),    80)
            ->addInlineParser(new Parser\Inline\EntityParser(),       70)
            ->addInlineParser(new Parser\Inline\AutolinkParser(),     50)
            ->addInlineParser(new Parser\Inline\HtmlInlineParser(),   40)
            ->addInlineParser(new Parser\Inline\CloseBracketParser(), 30)
            ->addInlineParser(new Parser\Inline\OpenBracketParser(),  20)
            ->addInlineParser(new Parser\Inline\BangParser(),         10)

            ->addBlockRenderer(Node\Block\BlockQuote::class,    new Renderer\Block\BlockQuoteRenderer(),    0)
            ->addBlockRenderer(CoreNode\Block\Document::class,  new CoreRenderer\Block\DocumentRenderer(),  0)
            ->addBlockRenderer(Node\Block\FencedCode::class,    new Renderer\Block\FencedCodeRenderer(),    0)
            ->addBlockRenderer(Node\Block\Heading::class,       new Renderer\Block\HeadingRenderer(),       0)
            ->addBlockRenderer(Node\Block\HtmlBlock::class,     new Renderer\Block\HtmlBlockRenderer(),     0)
            ->addBlockRenderer(Node\Block\IndentedCode::class,  new Renderer\Block\IndentedCodeRenderer(),  0)
            ->addBlockRenderer(Node\Block\ListBlock::class,     new Renderer\Block\ListBlockRenderer(),     0)
            ->addBlockRenderer(Node\Block\ListItem::class,      new Renderer\Block\ListItemRenderer(),      0)
            ->addBlockRenderer(CoreNode\Block\Paragraph::class, new CoreRenderer\Block\ParagraphRenderer(), 0)
            ->addBlockRenderer(Node\Block\ThematicBreak::class, new Renderer\Block\ThematicBreakRenderer(), 0)

            ->addInlineRenderer(Node\Inline\Code::class,        new Renderer\Inline\CodeRenderer(),        0)
            ->addInlineRenderer(Node\Inline\Emphasis::class,    new Renderer\Inline\EmphasisRenderer(),    0)
            ->addInlineRenderer(Node\Inline\HtmlInline::class,  new Renderer\Inline\HtmlInlineRenderer(),  0)
            ->addInlineRenderer(Node\Inline\Image::class,       new Renderer\Inline\ImageRenderer(),       0)
            ->addInlineRenderer(Node\Inline\Link::class,        new Renderer\Inline\LinkRenderer(),        0)
            ->addInlineRenderer(CoreNode\Inline\Newline::class, new CoreRenderer\Inline\NewlineRenderer(), 0)
            ->addInlineRenderer(Node\Inline\Strong::class,      new Renderer\Inline\StrongRenderer(),      0)
            ->addInlineRenderer(CoreNode\Inline\Text::class,    new CoreRenderer\Inline\TextRenderer(),    0)
        ;

        if ($environment->getConfig('use_asterisk', true)) {
            $environment->addDelimiterProcessor(new EmphasisDelimiterProcessor('*'));
        }

        if ($environment->getConfig('use_underscore', true)) {
            $environment->addDelimiterProcessor(new EmphasisDelimiterProcessor('_'));
        }
    }
}
