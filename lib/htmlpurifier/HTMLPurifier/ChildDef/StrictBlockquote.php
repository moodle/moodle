<?php

require_once 'HTMLPurifier/ChildDef/Required.php';

/**
 * Takes the contents of blockquote when in strict and reformats for validation.
 */
class   HTMLPurifier_ChildDef_StrictBlockquote
extends HTMLPurifier_ChildDef_Required
{
    var $real_elements;
    var $fake_elements;
    var $allow_empty = true;
    var $type = 'strictblockquote';
    var $init = false;
    function validateChildren($tokens_of_children, $config, &$context) {
        
        $def = $config->getHTMLDefinition();
        if (!$this->init) {
            // allow all inline elements
            $this->real_elements = $this->elements;
            $this->fake_elements = $def->info_content_sets['Flow'];
            $this->fake_elements['#PCDATA'] = true;
            $this->init = true;
        }
        
        // trick the parent class into thinking it allows more
        $this->elements = $this->fake_elements;
        $result = parent::validateChildren($tokens_of_children, $config, $context);
        $this->elements = $this->real_elements;
        
        if ($result === false) return array();
        if ($result === true) $result = $tokens_of_children;
        
        $block_wrap_start = new HTMLPurifier_Token_Start($def->info_block_wrapper);
        $block_wrap_end   = new HTMLPurifier_Token_End(  $def->info_block_wrapper);
        $is_inline = false;
        $depth = 0;
        $ret = array();
        
        // assuming that there are no comment tokens
        foreach ($result as $i => $token) {
            $token = $result[$i];
            // ifs are nested for readability
            if (!$is_inline) {
                if (!$depth) {
                     if (
                        ($token->type == 'text' && !$token->is_whitespace) ||
                        ($token->type != 'text' && !isset($this->elements[$token->name]))
                     ) {
                        $is_inline = true;
                        $ret[] = $block_wrap_start;
                     }
                }
            } else {
                if (!$depth) {
                    // starting tokens have been inline text / empty
                    if ($token->type == 'start' || $token->type == 'empty') {
                        if (isset($this->elements[$token->name])) {
                            // ended
                            $ret[] = $block_wrap_end;
                            $is_inline = false;
                        }
                    }
                }
            }
            $ret[] = $token;
            if ($token->type == 'start') $depth++;
            if ($token->type == 'end')   $depth--;
        }
        if ($is_inline) $ret[] = $block_wrap_end;
        return $ret;
    }
}

