<?php

require_once 'HTMLPurifier/Strategy.php';
require_once 'HTMLPurifier/HTMLDefinition.php';
require_once 'HTMLPurifier/Generator.php';

/**
 * Takes tokens makes them well-formed (balance end tags, etc.)
 */
class HTMLPurifier_Strategy_MakeWellFormed extends HTMLPurifier_Strategy
{
    
    function execute($tokens, $config, &$context) {
        $definition = $config->getHTMLDefinition();
        $generator = new HTMLPurifier_Generator();
        $result = array();
        $current_nesting = array();
        $escape_invalid_tags = $config->get('Core', 'EscapeInvalidTags');
        foreach ($tokens as $token) {
            if (empty( $token->is_tag )) {
                $result[] = $token;
                continue;
            }
            
            // DEFINITION CALL
            $info = $definition->info[$token->name]->child;
            
            // test if it claims to be a start tag but is empty
            if ($info->type == 'empty' &&
                $token->type == 'start' ) {
                
                $result[] = new HTMLPurifier_Token_Empty($token->name,
                                                         $token->attr);
                continue;
            }
            
            // test if it claims to be empty but really is a start tag
            if ($info->type != 'empty' &&
                $token->type == 'empty' ) {
                
                $result[] = new HTMLPurifier_Token_Start($token->name,
                                                         $token->attr);
                $result[] = new HTMLPurifier_Token_End($token->name);
                
                continue;
            }
            
            // automatically insert empty tags
            if ($token->type == 'empty') {
                $result[] = $token;
                continue;
            }
            
            // we give start tags precedence, so automatically accept unless...
            // it's one of those special cases
            if ($token->type == 'start') {
                
                // if there's a parent, check for special case
                if (!empty($current_nesting)) {
                    
                    $parent = array_pop($current_nesting);
                    $parent_name = $parent->name;
                    $parent_info = $definition->info[$parent_name];
                    
                    if (isset($parent_info->auto_close[$token->name])) {
                        $result[] = new HTMLPurifier_Token_End($parent_name);
                        $result[] = $token;
                        $current_nesting[] = $token;
                        continue;
                    }
                    
                    $current_nesting[] = $parent; // undo the pop
                }
                
                $result[] = $token;
                $current_nesting[] = $token;
                continue;
            }
            
            // sanity check
            if ($token->type != 'end') continue;
            
            // okay, we're dealing with a closing tag
            
            // make sure that we have something open
            if (empty($current_nesting)) {
                if ($escape_invalid_tags) {
                    $result[] = new HTMLPurifier_Token_Text(
                        $generator->generateFromToken($token, $config, $context)
                    );
                }
                continue;
            }
            
            // first, check for the simplest case: everything closes neatly
            
            // current_nesting is modified
            $current_parent = array_pop($current_nesting);
            if ($current_parent->name == $token->name) {
                $result[] = $token;
                continue;
            }
            
            // undo the array_pop
            $current_nesting[] = $current_parent;
            
            // okay, so we're trying to close the wrong tag
            
            // scroll back the entire nest, trying to find our tag
            // feature could be to specify how far you'd like to go
            $size = count($current_nesting);
            // -2 because -1 is the last element, but we already checked that
            $skipped_tags = false;
            for ($i = $size - 2; $i >= 0; $i--) {
                if ($current_nesting[$i]->name == $token->name) {
                    // current nesting is modified
                    $skipped_tags = array_splice($current_nesting, $i);
                    break;
                }
            }
            
            // we still didn't find the tag, so translate to text
            if ($skipped_tags === false) {
                if ($escape_invalid_tags) {
                    $result[] = new HTMLPurifier_Token_Text(
                        $generator->generateFromToken($token, $config, $context)
                    );
                }
                continue;
            }
            
            // okay, we found it, close all the skipped tags
            // note that skipped tags contains the element we need closed
            $size = count($skipped_tags);
            for ($i = $size - 1; $i >= 0; $i--) {
                $result[] = new HTMLPurifier_Token_End($skipped_tags[$i]->name);
            }
            
            // done!
            
        }
        
        // we're at the end now, fix all still unclosed tags
        
        if (!empty($current_nesting)) {
            $size = count($current_nesting);
            for ($i = $size - 1; $i >= 0; $i--) {
                $result[] =
                    new HTMLPurifier_Token_End($current_nesting[$i]->name);
            }
        }
        
        return $result;
    }
    
}

?>