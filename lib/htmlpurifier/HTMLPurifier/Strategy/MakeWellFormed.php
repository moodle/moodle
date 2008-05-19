<?php

require_once 'HTMLPurifier/Strategy.php';
require_once 'HTMLPurifier/HTMLDefinition.php';
require_once 'HTMLPurifier/Generator.php';

require_once 'HTMLPurifier/Injector/AutoParagraph.php';
require_once 'HTMLPurifier/Injector/Linkify.php';
require_once 'HTMLPurifier/Injector/PurifierLinkify.php';

HTMLPurifier_ConfigSchema::define(
    'AutoFormat', 'Custom', array(), 'list', '
<p>
  This directive can be used to add custom auto-format injectors.
  Specify an array of injector names (class name minus the prefix)
  or concrete implementations. Injector class must exist. This directive
  has been available since 2.0.1.
</p>
'
);

/**
 * Takes tokens makes them well-formed (balance end tags, etc.)
 */
class HTMLPurifier_Strategy_MakeWellFormed extends HTMLPurifier_Strategy
{
    
    /**
     * Locally shared variable references
     * @private
     */
    var $inputTokens, $inputIndex, $outputTokens, $currentNesting,
        $currentInjector, $injectors;
    
    function execute($tokens, $config, &$context) {
        
        $definition = $config->getHTMLDefinition();
        
        // local variables
        $result = array();
        $generator = new HTMLPurifier_Generator();
        $escape_invalid_tags = $config->get('Core', 'EscapeInvalidTags');
        $e =& $context->get('ErrorCollector', true);
        
        // member variables
        $this->currentNesting = array();
        $this->inputIndex     = false;
        $this->inputTokens    =& $tokens;
        $this->outputTokens   =& $result;
        
        // context variables
        $context->register('CurrentNesting', $this->currentNesting);
        $context->register('InputIndex', $this->inputIndex);
        $context->register('InputTokens', $tokens);
        
        // -- begin INJECTOR --
        
        $this->injectors = array();
        
        $injectors = $config->getBatch('AutoFormat');
        $custom_injectors = $injectors['Custom'];
        unset($injectors['Custom']); // special case
        foreach ($injectors as $injector => $b) {
            $injector = "HTMLPurifier_Injector_$injector";
            if (!$b) continue;
            $this->injectors[] = new $injector;
        }
        foreach ($custom_injectors as $injector) {
            if (is_string($injector)) {
                $injector = "HTMLPurifier_Injector_$injector";
                $injector = new $injector;
            }
            $this->injectors[] = $injector;
        }
        
        // array index of the injector that resulted in an array
        // substitution. This enables processTokens() to know which
        // injectors are affected by the added tokens and which are
        // not (namely, the ones after the current injector are not
        // affected)
        $this->currentInjector = false;
        
        // give the injectors references to the definition and context
        // variables for performance reasons
        foreach ($this->injectors as $i => $x) {
            $error = $this->injectors[$i]->prepare($config, $context);
            if (!$error) continue;
            list($injector) = array_splice($this->injectors, $i, 1);
            $name = $injector->name;
            trigger_error("Cannot enable $name injector because $error is not allowed", E_USER_WARNING);
        }
        
        // warning: most foreach loops follow the convention $i => $x.
        // be sure, for PHP4 compatibility, to only perform write operations
        // directly referencing the object using $i: $x is only safe for reads
        
        // -- end INJECTOR --
        
        $token = false;
        $context->register('CurrentToken', $token);
        
        for ($this->inputIndex = 0; isset($tokens[$this->inputIndex]); $this->inputIndex++) {
            
            // if all goes well, this token will be passed through unharmed
            $token = $tokens[$this->inputIndex];
            
            //printTokens($tokens, $this->inputIndex);
            
            foreach ($this->injectors as $i => $x) {
                if ($x->skip > 0) $this->injectors[$i]->skip--;
            }
            
            // quick-check: if it's not a tag, no need to process
            if (empty( $token->is_tag )) {
                if ($token->type === 'text') {
                     // injector handler code; duplicated for performance reasons
                     foreach ($this->injectors as $i => $x) {
                         if (!$x->skip) $this->injectors[$i]->handleText($token);
                         if (is_array($token)) {
                             $this->currentInjector = $i;
                             break;
                         }
                     }
                }
                $this->processToken($token, $config, $context);
                continue;
            }
            
            $info = $definition->info[$token->name]->child;
            
            // quick tag checks: anything that's *not* an end tag
            $ok = false;
            if ($info->type == 'empty' && $token->type == 'start') {
                // test if it claims to be a start tag but is empty
                $token = new HTMLPurifier_Token_Empty($token->name, $token->attr);
                $ok = true;
            } elseif ($info->type != 'empty' && $token->type == 'empty' ) {
                // claims to be empty but really is a start tag
                $token = array(
                    new HTMLPurifier_Token_Start($token->name, $token->attr),
                    new HTMLPurifier_Token_End($token->name)
                );
                $ok = true;
            } elseif ($token->type == 'empty') {
                // real empty token
                $ok = true;
            } elseif ($token->type == 'start') {
                // start tag
                
                // ...unless they also have to close their parent
                if (!empty($this->currentNesting)) {
                    
                    $parent = array_pop($this->currentNesting);
                    $parent_info = $definition->info[$parent->name];
                    
                    // this can be replaced with a more general algorithm:
                    // if the token is not allowed by the parent, auto-close
                    // the parent
                    if (!isset($parent_info->child->elements[$token->name])) {
                        if ($e) $e->send(E_NOTICE, 'Strategy_MakeWellFormed: Tag auto closed', $parent);
                        // close the parent, then re-loop to reprocess token
                        $result[] = new HTMLPurifier_Token_End($parent->name);
                        $this->inputIndex--;
                        continue;
                    }
                    
                    $this->currentNesting[] = $parent; // undo the pop
                }
                $ok = true;
            }
            
            // injector handler code; duplicated for performance reasons
            if ($ok) {
                foreach ($this->injectors as $i => $x) {
                    if (!$x->skip) $this->injectors[$i]->handleElement($token);
                    if (is_array($token)) {
                        $this->currentInjector = $i;
                        break;
                    }
                }
                $this->processToken($token, $config, $context);
                continue;
            }
            
            // sanity check: we should be dealing with a closing tag
            if ($token->type != 'end') continue;
            
            // make sure that we have something open
            if (empty($this->currentNesting)) {
                if ($escape_invalid_tags) {
                    if ($e) $e->send(E_WARNING, 'Strategy_MakeWellFormed: Unnecessary end tag to text');
                    $result[] = new HTMLPurifier_Token_Text(
                        $generator->generateFromToken($token, $config, $context)
                    );
                } elseif ($e) {
                    $e->send(E_WARNING, 'Strategy_MakeWellFormed: Unnecessary end tag removed');
                }
                continue;
            }
            
            // first, check for the simplest case: everything closes neatly
            $current_parent = array_pop($this->currentNesting);
            if ($current_parent->name == $token->name) {
                $result[] = $token;
                foreach ($this->injectors as $i => $x) {
                    $this->injectors[$i]->notifyEnd($token);
                }
                continue;
            }
            
            // okay, so we're trying to close the wrong tag
            
            // undo the pop previous pop
            $this->currentNesting[] = $current_parent;
            
            // scroll back the entire nest, trying to find our tag.
            // (feature could be to specify how far you'd like to go)
            $size = count($this->currentNesting);
            // -2 because -1 is the last element, but we already checked that
            $skipped_tags = false;
            for ($i = $size - 2; $i >= 0; $i--) {
                if ($this->currentNesting[$i]->name == $token->name) {
                    // current nesting is modified
                    $skipped_tags = array_splice($this->currentNesting, $i);
                    break;
                }
            }
            
            // we still didn't find the tag, so remove
            if ($skipped_tags === false) {
                if ($escape_invalid_tags) {
                    $result[] = new HTMLPurifier_Token_Text(
                        $generator->generateFromToken($token, $config, $context)
                    );
                    if ($e) $e->send(E_WARNING, 'Strategy_MakeWellFormed: Stray end tag to text');
                } elseif ($e) {
                    $e->send(E_WARNING, 'Strategy_MakeWellFormed: Stray end tag removed');
                }
                continue;
            }
            
            // okay, we found it, close all the skipped tags
            // note that skipped tags contains the element we need closed
            for ($i = count($skipped_tags) - 1; $i >= 0; $i--) {
                if ($i && $e && !isset($skipped_tags[$i]->armor['MakeWellFormed_TagClosedError'])) {
                    $e->send(E_NOTICE, 'Strategy_MakeWellFormed: Tag closed by element end', $skipped_tags[$i]);
                }
                $result[] = $new_token = new HTMLPurifier_Token_End($skipped_tags[$i]->name);
                foreach ($this->injectors as $j => $x) { // $j, not $i!!!
                    $this->injectors[$j]->notifyEnd($new_token);
                }
            }
            
        }
        
        $context->destroy('CurrentNesting');
        $context->destroy('InputTokens');
        $context->destroy('InputIndex');
        $context->destroy('CurrentToken');
        
        // we're at the end now, fix all still unclosed tags (this is
        // duplicated from the end of the loop with some slight modifications)
        // not using $skipped_tags since it would invariably be all of them
        if (!empty($this->currentNesting)) {
            for ($i = count($this->currentNesting) - 1; $i >= 0; $i--) {
                if ($e && !isset($this->currentNesting[$i]->armor['MakeWellFormed_TagClosedError'])) {
                    $e->send(E_NOTICE, 'Strategy_MakeWellFormed: Tag closed by document end', $this->currentNesting[$i]);
                }
                $result[] = $new_token = new HTMLPurifier_Token_End($this->currentNesting[$i]->name);
                foreach ($this->injectors as $j => $x) { // $j, not $i!!!
                    $this->injectors[$j]->notifyEnd($new_token);
                }
            }
        }
        
        unset($this->outputTokens, $this->injectors, $this->currentInjector,
          $this->currentNesting, $this->inputTokens, $this->inputIndex);
        
        return $result;
    }
    
    function processToken($token, $config, &$context) {
        if (is_array($token)) {
            // the original token was overloaded by an injector, time
            // to some fancy acrobatics
            
            // $this->inputIndex is decremented so that the entire set gets
            // re-processed
            array_splice($this->inputTokens, $this->inputIndex--, 1, $token);
            
            // adjust the injector skips based on the array substitution
            if ($this->injectors) {
                $offset = count($token);
                for ($i = 0; $i <= $this->currentInjector; $i++) {
                    // because of the skip back, we need to add one more
                    // for uninitialized injectors. I'm not exactly
                    // sure why this is the case, but I think it has to
                    // do with the fact that we're decrementing skips
                    // before re-checking text
                    if (!$this->injectors[$i]->skip) $this->injectors[$i]->skip++;
                    $this->injectors[$i]->skip += $offset;
                }
            }
        } elseif ($token) {
            // regular case
            $this->outputTokens[] = $token;
            if ($token->type == 'start') {
                $this->currentNesting[] = $token;
            } elseif ($token->type == 'end') {
                array_pop($this->currentNesting); // not actually used
            }
        }
    }
    
}

