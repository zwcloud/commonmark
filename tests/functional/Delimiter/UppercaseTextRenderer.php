<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * Additional emphasis processing code based on commonmark-java (https://github.com/atlassian/commonmark-java)
 *  - (c) Atlassian Pty Ltd
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Tests\Functional\Delimiter;

use League\CommonMark\Node\Inline\AbstractInline;
use League\CommonMark\Node\Inline\Text;
use League\CommonMark\Renderer\Inline\InlineRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;

final class UppercaseTextRenderer implements InlineRendererInterface
{
    /**
     * {@inheritdoc}
     */
    public function render(AbstractInline $inline, NodeRendererInterface $htmlRenderer)
    {
        if (!($inline instanceof UppercaseText)) {
            throw new \InvalidArgumentException('Incompatible inline type: ' . \get_class($inline));
        }

        $ret = '';

        foreach ($inline->children() as $child) {
            if ($child instanceof Text) {
                $child->setLiteral(\mb_strtoupper($child->getLiteral()));
            }

            $ret .= $htmlRenderer->renderInline($child);
        }

        return $ret;
    }
}
