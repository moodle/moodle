<?php

/**
 * Injects tokens into the document while parsing for well-formedness.
 * This enables "formatter-like" functionality such as auto-paragraphing,
 * smiley-ification and linkification to take place.
 * 
 * @todo Allow injectors to request a re-run on their output. This 
 *       would help if an operation is recursive.
 */
class HTMLPurifier_Injector
{
    
    /**
     * Advisory name of injector, this is for friendly error messages
     */
    var $name;
    
    /**
     * Amount of tokens the injector needs to skip + 1. Because
     * the decrement is the first thing that happens, this needs to
     * be one greater than the "real" skip count.
     */
    var $skip = 1;
    
    /**
     * Instance of HTMLPurifier_HTMLDefinition
     */
    var $htmlDefinition;
    
    /**
     * Reference to CurrentNesting variable in Context. This is an array
     * list of tokens that we are currently "inside"
     */
    var $currentNesting;
    
    /**
     * Reference to InputTokens variable in Context. This is an array
     * list of the input tokens that are being processed.
     */
    var $inputTokens;
    
    /**
     * Reference to InputIndex variable in Context. This is an integer
     * array index for $this->inputTokens that indicates what token
     * is currently being processed.
     */
    var $inputIndex;
    
    /**
     * Array of elements and attributes this injector creates and therefore
     * need to be allowed by the definition. Takes form of
     * array('element' => array('attr', 'attr2'), 'element2')
     */
    var $needed = array();
    
    /**
     * Prepares the injector by giving it the config and context objects:
     * this allows references to important variables to be made within
     * the injector. This function also checks if the HTML environment
     * will work with the Injector: if p tags are not allowed, the
     * Auto-Paragraphing injector should not be enabled.
     * @param $config Instance of HTMLPurifier_Config
     * @param $context Instance of HTMLPurifier_Context
     * @return Boolean false if success, string of missing needed element/attribute if failure
     */
    function prepare($config, &$context) {
        $this->htmlDefinition = $config->getHTMLDefinition();
        // perform $needed checks
        foreach ($this->needed as $element => $attributes) {
            if (is_int($element)) $element = $attributes;
            if (!isset($this->htmlDefinition->info[$element])) return $element;
            if (!is_array($attributes)) continue;
            foreach ($attributes as $name) {
                if (!isset($this->htmlDefinition->info[$element]->attr[$name])) return "$element.$name";
            }
        }
        $this->currentNesting =& $context->get('CurrentNesting');
        $this->inputTokens    =& $context->get('InputTokens');
        $this->inputIndex     =& $context->get('InputIndex');
        return false;
    }
    
    /**
     * Tests if the context node allows a certain element
     * @param $name Name of element to test for
     * @return True if element is allowed, false if it is not
     */
    function allowsElement($name) {
        if (!empty($this->currentNesting)) {
            $parent_token = array_pop($this->currentNesting);
            $this->currentNesting[] = $parent_token;
            $parent = $this->htmlDefinition->info[$parent_token->name];
        } else {
            $parent = $this->htmlDefinition->info_parent_def;
        }
        if (!isset($parent->child->elements[$name]) || isset($parent->excludes[$name])) {
            return false;
        }
        return true;
    }
    
    /**
     * Handler that is called when a text token is processed
     */
    function handleText(&$token) {}
    
    /**
     * Handler that is called when a start or empty token is processed
     */
    function handleElement(&$token) {}
    
    /**
     * Notifier that is called when an end token is processed
     * @note This differs from handlers in that the token is read-only
     */
    function notifyEnd($token) {}
    
    
}

