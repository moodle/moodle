<?php

require_once 'HTMLPurifier/Strategy.php';
require_once 'HTMLPurifier/HTMLDefinition.php';
require_once 'HTMLPurifier/IDAccumulator.php';

HTMLPurifier_ConfigSchema::define(
    'Attr', 'IDBlacklist', array(), 'list',
    'Array of IDs not allowed in the document.');

/**
 * Validate all attributes in the tokens.
 */

class HTMLPurifier_Strategy_ValidateAttributes extends HTMLPurifier_Strategy
{
    
    function execute($tokens, $config, &$context) {
        
        $definition = $config->getHTMLDefinition();
        
        // setup id_accumulator context
        $id_accumulator = new HTMLPurifier_IDAccumulator();
        $id_accumulator->load($config->get('Attr', 'IDBlacklist'));
        $context->register('IDAccumulator', $id_accumulator);
        
        // create alias to global definition array, see also $defs
        // DEFINITION CALL
        $d_defs = $definition->info_global_attr;
        
        foreach ($tokens as $key => $token) {
            
            // only process tokens that have attributes,
            //   namely start and empty tags
            if ($token->type !== 'start' && $token->type !== 'empty') continue;
            
            // copy out attributes for easy manipulation
            $attr = $token->attr;
            
            // do global transformations (pre)
            // nothing currently utilizes this
            foreach ($definition->info_attr_transform_pre as $transform) {
                $attr = $transform->transform($attr, $config, $context);
            }
            
            // do local transformations only applicable to this element (pre)
            // ex. <p align="right"> to <p style="text-align:right;">
            foreach ($definition->info[$token->name]->attr_transform_pre
                as $transform
            ) {
                $attr = $transform->transform($attr, $config, $context);
            }
            
            // create alias to this element's attribute definition array, see
            // also $d_defs (global attribute definition array)
            // DEFINITION CALL
            $defs = $definition->info[$token->name]->attr;
            
            // iterate through all the attribute keypairs
            // Watch out for name collisions: $key has previously been used
            foreach ($attr as $attr_key => $value) {
                
                // call the definition
                if ( isset($defs[$attr_key]) ) {
                    // there is a local definition defined
                    if ($defs[$attr_key] === false) {
                        // We've explicitly been told not to allow this element.
                        // This is usually when there's a global definition
                        // that must be overridden.
                        // Theoretically speaking, we could have a
                        // AttrDef_DenyAll, but this is faster!
                        $result = false;
                    } else {
                        // validate according to the element's definition
                        $result = $defs[$attr_key]->validate(
                                        $value, $config, $context
                                   );
                    }
                } elseif ( isset($d_defs[$attr_key]) ) {
                    // there is a global definition defined, validate according
                    // to the global definition
                    $result = $d_defs[$attr_key]->validate(
                                    $value, $config, $context
                               );
                } else {
                    // system never heard of the attribute? DELETE!
                    $result = false;
                }
                
                // put the results into effect
                if ($result === false || $result === null) {
                    // remove the attribute
                    unset($attr[$attr_key]);
                } elseif (is_string($result)) {
                    // simple substitution
                    $attr[$attr_key] = $result;
                }
                
                // we'd also want slightly more complicated substitution
                // involving an array as the return value,
                // although we're not sure how colliding attributes would
                // resolve (certain ones would be completely overriden,
                // others would prepend themselves).
            }
            
            // post transforms
            
            // ex. <x lang="fr"> to <x lang="fr" xml:lang="fr">
            foreach ($definition->info_attr_transform_post as $transform) {
                $attr = $transform->transform($attr, $config, $context);
            }
            
            // ex. <bdo> to <bdo dir="ltr">
            foreach ($definition->info[$token->name]->attr_transform_post as $transform) {
                $attr = $transform->transform($attr, $config, $context);
            }
            
            // commit changes
            // could interfere with flyweight implementation
            $tokens[$key]->attr = $attr;
        }
        $context->destroy('IDAccumulator');
        
        return $tokens;
    }
    
}

?>