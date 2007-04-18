<?php

require_once 'HTMLPurifier/Strategy.php';
require_once 'HTMLPurifier/HTMLDefinition.php';
require_once 'HTMLPurifier/Generator.php';
require_once 'HTMLPurifier/TagTransform.php';

HTMLPurifier_ConfigSchema::define(
    'Core', 'RemoveInvalidImg', true, 'bool',
    'This directive enables pre-emptive URI checking in <code>img</code> '.
    'tags, as the attribute validation strategy is not authorized to '.
    'remove elements from the document.  This directive has been available '.
    'since 1.3.0, revert to pre-1.3.0 behavior by setting to false.'
);

/**
 * Removes all unrecognized tags from the list of tokens.
 * 
 * This strategy iterates through all the tokens and removes unrecognized
 * tokens. If a token is not recognized but a TagTransform is defined for
 * that element, the element will be transformed accordingly.
 */

class HTMLPurifier_Strategy_RemoveForeignElements extends HTMLPurifier_Strategy
{
    
    function execute($tokens, $config, &$context) {
        $definition = $config->getHTMLDefinition();
        $generator = new HTMLPurifier_Generator();
        $result = array();
        $escape_invalid_tags = $config->get('Core', 'EscapeInvalidTags');
        foreach($tokens as $token) {
            if (!empty( $token->is_tag )) {
                // DEFINITION CALL
                if (isset($definition->info[$token->name])) {
                    // leave untouched, except for a few special cases:
                    
                    // hard-coded image special case, pre-emptively drop
                    // if not available. Probably not abstract-able
                    if ( $token->name == 'img' ) {
                        if (!isset($token->attr['src'])) {
                            continue;
                        }
                        if (!isset($definition->info['img']->attr['src'])) {
                            continue;
                        }
                        $token->attr['src'] =
                            $definition->
                                info['img']->
                                    attr['src']->
                                        validate($token->attr['src'],
                                            $config, $context);
                        if ($token->attr['src'] === false) continue;
                    }
                    
                } elseif (
                    isset($definition->info_tag_transform[$token->name])
                ) {
                    // there is a transformation for this tag
                    // DEFINITION CALL
                    $token = $definition->
                                info_tag_transform[$token->name]->
                                    transform($token, $config, $context);
                } elseif ($escape_invalid_tags) {
                    // invalid tag, generate HTML and insert in
                    $token = new HTMLPurifier_Token_Text(
                        $generator->generateFromToken($token, $config, $context)
                    );
                } else {
                    continue;
                }
            } elseif ($token->type == 'comment') {
                // strip comments
                continue;
            } elseif ($token->type == 'text') {
            } else {
                continue;
            }
            $result[] = $token;
        }
        return $result;
    }
    
}

?>