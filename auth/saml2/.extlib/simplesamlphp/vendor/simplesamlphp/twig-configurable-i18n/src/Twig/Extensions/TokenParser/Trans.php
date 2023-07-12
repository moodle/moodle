<?php

/**
 * A class implementing a token parser for translation nodes.
 *
 * @author Jaime Pérez Crespo
 */

namespace SimpleSAML\TwigConfigurableI18n\Twig\Extensions\TokenParser;

use SimpleSAML\TwigConfigurableI18n\Twig\Extensions\Node\Trans as NodeTrans;
use Twig\Node\Node;
use Twig\Token;

class Trans extends \Twig\Extensions\TokenParser\TransTokenParser
{
    /**
     * Parses a token and returns a node.
     *
     * @param \Twig\Token $token A \Twig\Token instance
     *
     * @return \Twig\Node\Node A \Twig\Node\Node instance
     */
    public function parse(Token $token): Node
    {
        $parsed = parent::parse($token);
        $body = $parsed->getNode('body');
        $plural = ($parsed->hasNode('plural')) ? $parsed->getNode('plural') : null;

        /** @var \Twig\Node\Expression\AbstractExpression|null */
        $count = ($parsed->hasNode('count')) ? $parsed->getNode('count') : null;
        $notes = ($parsed->hasNode('notes')) ? $parsed->getNode('notes') : null;

        /** @var \Twig\Node\Node */
        return new NodeTrans($body, $plural, $count, $notes, $parsed->getTemplateLine(), $parsed->getNodeTag());
    }
}
