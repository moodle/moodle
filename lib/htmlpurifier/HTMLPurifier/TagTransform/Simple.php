<?php

require_once 'HTMLPurifier/TagTransform.php';

/**
 * Simple transformation, just change tag name to something else,
 * and possibly add some styling. This will cover most of the deprecated
 * tag cases.
 */
class HTMLPurifier_TagTransform_Simple extends HTMLPurifier_TagTransform
{
    
    var $style;
    
    /**
     * @param $transform_to Tag name to transform to.
     * @param $style CSS style to add to the tag
     */
    function HTMLPurifier_TagTransform_Simple($transform_to, $style = null) {
        $this->transform_to = $transform_to;
        $this->style = $style;
    }
    
    function transform($tag, $config, &$context) {
        $new_tag = $tag->copy();
        $new_tag->name = $this->transform_to;
        if (!is_null($this->style) &&
            ($new_tag->type == 'start' || $new_tag->type == 'empty')
        ) {
            $this->prependCSS($new_tag->attr, $this->style);
        }
        return $new_tag;
    }
    
}

