<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Node;

final class StringContainerHelper
{
    /**
     * Extract text literals from all descendant nodes
     *
     * @param Node $node Parent node
     *
     * @return string Concatenated literals
     */
    public static function getChildText(Node $node): string
    {
        $text = '';

        $walker = $node->walker();
        while ($event = $walker->next()) {
            if ($event->isEntering() && ($child = $event->getNode()) instanceof StringContainerInterface) {
                $text .= $child->getLiteral();
            }
        }

        return $text;
    }
}
