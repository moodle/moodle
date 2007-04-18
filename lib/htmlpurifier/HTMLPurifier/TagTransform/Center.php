<?php

require_once 'HTMLPurifier/TagTransform.php';

/**
 * Transforms CENTER tags into proper version (DIV with text-align CSS)
 * 
 * Takes a CENTER tag, parses the align attribute, and then if it's valid
 * assigns it to the CSS property text-align.
 */
class HTMLPurifier_TagTransform_Center extends HTMLPurifier_TagTransform
{
    var $transform_to = 'div';
    
    function transform($tag, $config, &$context) {
        if ($tag->type == 'end') {
            $new_tag = new HTMLPurifier_Token_End($this->transform_to);
            return $new_tag;
        }
        $attr = $tag->attr;
        $prepend_css = 'text-align:center;';
        if (isset($attr['style'])) {
            $attr['style'] = $prepend_css . $attr['style'];
        } else {
            $attr['style'] = $prepend_css;
        }
        $new_tag = $tag->copy();
        $new_tag->name = $this->transform_to;
        $new_tag->attr = $attr;
        return $new_tag;
    }
}

?>