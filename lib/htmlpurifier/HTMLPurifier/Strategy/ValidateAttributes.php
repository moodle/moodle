<?php

require_once 'HTMLPurifier/Strategy.php';
require_once 'HTMLPurifier/HTMLDefinition.php';
require_once 'HTMLPurifier/IDAccumulator.php';

require_once 'HTMLPurifier/AttrValidator.php';

HTMLPurifier_ConfigSchema::define(
    'Attr', 'IDBlacklist', array(), 'list',
    'Array of IDs not allowed in the document.');

/**
 * Validate all attributes in the tokens.
 */

class HTMLPurifier_Strategy_ValidateAttributes extends HTMLPurifier_Strategy
{
    
    function execute($tokens, $config, &$context) {
        
        // setup id_accumulator context
        $id_accumulator = new HTMLPurifier_IDAccumulator();
        $id_accumulator->load($config->get('Attr', 'IDBlacklist'));
        $context->register('IDAccumulator', $id_accumulator);
        
        // setup validator
        $validator = new HTMLPurifier_AttrValidator();
        
        $token = false;
        $context->register('CurrentToken', $token);
        
        foreach ($tokens as $key => $token) {
            
            // only process tokens that have attributes,
            //   namely start and empty tags
            if ($token->type !== 'start' && $token->type !== 'empty') continue;
            
            // skip tokens that are armored
            if (!empty($token->armor['ValidateAttributes'])) continue;
            
            // note that we have no facilities here for removing tokens
            $validator->validateToken($token, $config, $context);
            
            $tokens[$key] = $token; // for PHP 4
        }
        
        $context->destroy('IDAccumulator');
        $context->destroy('CurrentToken');
        
        return $tokens;
    }
    
}

