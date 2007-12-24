<?php

require_once 'HTMLPurifier/Strategy.php';
require_once 'HTMLPurifier/HTMLDefinition.php';
require_once 'HTMLPurifier/Generator.php';
require_once 'HTMLPurifier/TagTransform.php';

require_once 'HTMLPurifier/AttrValidator.php';

HTMLPurifier_ConfigSchema::define(
    'Core', 'RemoveInvalidImg', true, 'bool', '
<p>
  This directive enables pre-emptive URI checking in <code>img</code> 
  tags, as the attribute validation strategy is not authorized to 
  remove elements from the document.  This directive has been available 
  since 1.3.0, revert to pre-1.3.0 behavior by setting to false.
</p>
'
);

HTMLPurifier_ConfigSchema::define(
    'Core', 'RemoveScriptContents', null, 'bool/null', '
<p>
  This directive enables HTML Purifier to remove not only script tags
  but all of their contents. This directive has been deprecated since 2.1.0,
  and when not set the value of %Core.HiddenElements will take
  precedence. This directive has been available since 2.0.0, and can be used to 
  revert to pre-2.0.0 behavior by setting it to false.
</p>
'
);

HTMLPurifier_ConfigSchema::define(
    'Core', 'HiddenElements', array('script' => true, 'style' => true), 'lookup', '
<p>
  This directive is a lookup array of elements which should have their
  contents removed when they are not allowed by the HTML definition.
  For example, the contents of a <code>script</code> tag are not 
  normally shown in a document, so if script tags are to be removed,
  their contents should be removed to. This is opposed to a <code>b</code>
  tag, which defines some presentational changes but does not hide its
  contents.
</p>
'
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
        $remove_invalid_img  = $config->get('Core', 'RemoveInvalidImg');
        
        $remove_script_contents = $config->get('Core', 'RemoveScriptContents');
        $hidden_elements     = $config->get('Core', 'HiddenElements');
        
        // remove script contents compatibility
        if ($remove_script_contents === true) {
            $hidden_elements['script'] = true;
        } elseif ($remove_script_contents === false && isset($hidden_elements['script'])) {
            unset($hidden_elements['script']);
        }
        
        $attr_validator = new HTMLPurifier_AttrValidator();
        
        // removes tokens until it reaches a closing tag with its value
        $remove_until = false;
        
        // converts comments into text tokens when this is equal to a tag name
        $textify_comments = false;
        
        $token = false;
        $context->register('CurrentToken', $token);
        
        $e = false;
        if ($config->get('Core', 'CollectErrors')) {
            $e =& $context->get('ErrorCollector');
        }
        
        foreach($tokens as $token) {
            if ($remove_until) {
                if (empty($token->is_tag) || $token->name !== $remove_until) {
                    continue;
                }
            }
            if (!empty( $token->is_tag )) {
                // DEFINITION CALL
                
                // before any processing, try to transform the element
                if (
                    isset($definition->info_tag_transform[$token->name])
                ) {
                    $original_name = $token->name;
                    // there is a transformation for this tag
                    // DEFINITION CALL
                    $token = $definition->
                                info_tag_transform[$token->name]->
                                    transform($token, $config, $context);
                    if ($e) $e->send(E_NOTICE, 'Strategy_RemoveForeignElements: Tag transform', $original_name);
                }
                
                if (isset($definition->info[$token->name])) {
                    
                    // mostly everything's good, but
                    // we need to make sure required attributes are in order
                    if (
                        ($token->type === 'start' || $token->type === 'empty') &&
                        $definition->info[$token->name]->required_attr &&
                        ($token->name != 'img' || $remove_invalid_img) // ensure config option still works
                    ) {
                        $attr_validator->validateToken($token, $config, $context);
                        $ok = true;
                        foreach ($definition->info[$token->name]->required_attr as $name) {
                            if (!isset($token->attr[$name])) {
                                $ok = false;
                                break;
                            }
                        }
                        if (!$ok) {
                            if ($e) $e->send(E_ERROR, 'Strategy_RemoveForeignElements: Missing required attribute', $name);
                            continue;
                        }
                        $token->armor['ValidateAttributes'] = true;
                    }
                    
                    if (isset($hidden_elements[$token->name]) && $token->type == 'start') {
                        $textify_comments = $token->name;
                    } elseif ($token->name === $textify_comments && $token->type == 'end') {
                        $textify_comments = false;
                    }
                    
                } elseif ($escape_invalid_tags) {
                    // invalid tag, generate HTML representation and insert in
                    if ($e) $e->send(E_WARNING, 'Strategy_RemoveForeignElements: Foreign element to text');
                    $token = new HTMLPurifier_Token_Text(
                        $generator->generateFromToken($token, $config, $context)
                    );
                } else {
                    // check if we need to destroy all of the tag's children
                    // CAN BE GENERICIZED
                    if (isset($hidden_elements[$token->name])) {
                        if ($token->type == 'start') {
                            $remove_until = $token->name;
                        } elseif ($token->type == 'empty') {
                            // do nothing: we're still looking
                        } else {
                            $remove_until = false;
                        }
                        if ($e) $e->send(E_ERROR, 'Strategy_RemoveForeignElements: Foreign meta element removed');
                    } else {
                        if ($e) $e->send(E_ERROR, 'Strategy_RemoveForeignElements: Foreign element removed');
                    }
                    continue;
                }
            } elseif ($token->type == 'comment') {
                // textify comments in script tags when they are allowed
                if ($textify_comments !== false) {
                    $data = $token->data;
                    $token = new HTMLPurifier_Token_Text($data);
                } else {
                    // strip comments
                    if ($e) $e->send(E_NOTICE, 'Strategy_RemoveForeignElements: Comment removed');
                    continue;
                }
            } elseif ($token->type == 'text') {
            } else {
                continue;
            }
            $result[] = $token;
        }
        if ($remove_until && $e) {
            // we removed tokens until the end, throw error
            $e->send(E_ERROR, 'Strategy_RemoveForeignElements: Token removed to end', $remove_until);
        }
        
        $context->destroy('CurrentToken');
        
        return $result;
    }
    
}

