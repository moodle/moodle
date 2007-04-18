<?php

require_once 'HTMLPurifier/TagTransform.php';

/**
 * Simple transformation, just change tag name to something else.
 */
class HTMLPurifier_TagTransform_Simple extends HTMLPurifier_TagTransform
{
    
    /**
     * @param $transform_to Tag name to transform to.
     */
    function HTMLPurifier_TagTransform_Simple($transform_to) {
        $this->transform_to = $transform_to;
    }
    
    function transform($tag, $config, &$context) {
        $new_tag = $tag->copy();
        $new_tag->name = $this->transform_to;
        return $new_tag;
    }
    
}

?>