<?php

require_once 'HTMLPurifier/Token.php';

/**
 * Defines a mutation of an obsolete tag into a valid tag.
 */
class HTMLPurifier_TagTransform
{
    
    /**
     * Tag name to transform the tag to.
     * @public
     */
    var $transform_to;
    
    /**
     * Transforms the obsolete tag into the valid tag.
     * @param $tag Tag to be transformed.
     * @param $config Mandatory HTMLPurifier_Config object
     * @param $context Mandatory HTMLPurifier_Context object
     */
    function transform($tag, $config, &$context) {
        trigger_error('Call to abstract function', E_USER_ERROR);
    }
    
}

?>