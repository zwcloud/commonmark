<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Environment;

use League\CommonMark\Delimiter\Processor\DelimiterProcessorInterface;
use League\CommonMark\Extension\ExtensionInterface;
use League\CommonMark\Parser\Block\BlockParserFactoryInterface;
use League\CommonMark\Parser\Inline\InlineParserInterface;
use League\CommonMark\Renderer\Block\BlockRendererInterface;
use League\CommonMark\Renderer\Inline\InlineRendererInterface;

/**
 * Interface for an Environment which can be configured with config settings, parsers, processors, and renderers
 */
interface ConfigurableEnvironmentInterface extends EnvironmentInterface
{
    /**
     * @param array<string, mixed> $config
     *
     * @return void
     */
    public function mergeConfig(array $config = []): void;

    /**
     * @param array<string, mixed> $config
     *
     * @return void
     */
    public function setConfig(array $config = []): void;

    /**
     * Registers the given extension with the Environment
     *
     * @param ExtensionInterface $extension
     *
     * @return ConfigurableEnvironmentInterface
     */
    public function addExtension(ExtensionInterface $extension): ConfigurableEnvironmentInterface;

    /**
     * Registers the given block parser factory with the Environment
     *
     * @param BlockParserFactoryInterface $factory  Block parser instance
     * @param int                         $priority Priority (a higher number will be executed earlier)
     *
     * @return self
     */
    public function addBlockParserFactory(BlockParserFactoryInterface $factory, int $priority = 0): ConfigurableEnvironmentInterface;

    /**
     * Registers the given inline parser with the Environment
     *
     * @param InlineParserInterface $parser   Inline parser instance
     * @param int                   $priority Priority (a higher number will be executed earlier)
     *
     * @return self
     */
    public function addInlineParser(InlineParserInterface $parser, int $priority = 0): ConfigurableEnvironmentInterface;

    /**
     * Registers the given delimiter processor with the Environment
     *
     * @param DelimiterProcessorInterface $processor Delimiter processors instance
     *
     * @return ConfigurableEnvironmentInterface
     */
    public function addDelimiterProcessor(DelimiterProcessorInterface $processor): ConfigurableEnvironmentInterface;

    /**
     * @param string                 $blockClass    The fully-qualified block element class name the renderer below should handle
     * @param BlockRendererInterface $blockRenderer The renderer responsible for rendering the type of element given above
     * @param int                    $priority      Priority (a higher number will be executed earlier)
     *
     * @return self
     */
    public function addBlockRenderer($blockClass, BlockRendererInterface $blockRenderer, int $priority = 0): ConfigurableEnvironmentInterface;

    /**
     * Registers the given inline renderer with the Environment
     *
     * @param string                  $inlineClass The fully-qualified inline element class name the renderer below should handle
     * @param InlineRendererInterface $renderer    The renderer responsible for rendering the type of element given above
     * @param int                     $priority    Priority (a higher number will be executed earlier)
     *
     * @return self
     */
    public function addInlineRenderer(string $inlineClass, InlineRendererInterface $renderer, int $priority = 0): ConfigurableEnvironmentInterface;

    /**
     * Registers the given event listener
     *
     * @param string   $eventClass Fully-qualified class name of the event this listener should respond to
     * @param callable $listener   Listener to be executed
     * @param int      $priority   Priority (a higher number will be executed earlier)
     *
     * @return self
     */
    public function addEventListener(string $eventClass, callable $listener, int $priority = 0): ConfigurableEnvironmentInterface;
}
