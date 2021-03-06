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

namespace League\CommonMark\Tests\Functional;

/**
 * Tests the parser against the CommonMark spec
 */
class RegressionTest extends AbstractSpecTest
{
    protected function getFileName(): string
    {
        return __DIR__ . '/../../vendor/commonmark/commonmark.js/test/regression.txt';
    }
}
