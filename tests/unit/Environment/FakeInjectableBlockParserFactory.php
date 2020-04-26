<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Tests\Unit\Environment;

use League\CommonMark\Configuration\ConfigurationAwareInterface;
use League\CommonMark\Environment\EnvironmentAwareInterface;
use League\CommonMark\Parser\Block\BlockParserFactoryInterface;
use League\CommonMark\Parser\Block\BlockStart;
use League\CommonMark\Parser\Block\DocParserStateInterface;
use League\CommonMark\Parser\Cursor;

final class FakeInjectableBlockParserFactory implements BlockParserFactoryInterface, ConfigurationAwareInterface, EnvironmentAwareInterface
{
    use FakeInjectableTrait;

    public function tryStart(Cursor $cursor, DocParserStateInterface $parserState): ?BlockStart
    {
        return BlockStart::none();
    }
}
