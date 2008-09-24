<?php

/**
 * Decorator that, depending on a token, switches between two definitions.
 */
class HTMLPurifier_AttrDef_Switch
{
    
    var $tag;
    var $withTag, $withoutTag;
    
    /**
     * @param string $tag Tag name to switch upon
     * @param HTMLPurifier_AttrDef $with_tag Call if token matches tag
     * @param HTMLPurifier_AttrDef $without_tag Call if token doesn't match, or there is no token
     */
    function HTMLPurifier_AttrDef_Switch($tag, $with_tag, $without_tag) {
        $this->tag = $tag;
        $this->withTag = $with_tag;
        $this->withoutTag = $without_tag;
    }
    
    function validate($string, $config, $context) {
        $token = $context->get('CurrentToken', true);
        if (!$token || $token->name !== $this->tag) {
            return $this->withoutTag->validate($string, $config, $context);
        } else {
            return $this->withTag->validate($string, $config, $context);
        }
    }
    
}
