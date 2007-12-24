<?php

require_once 'HTMLPurifier/Injector.php';

HTMLPurifier_ConfigSchema::define(
    'AutoFormat', 'AutoParagraph', false, 'bool', '
<p>
  This directive turns on auto-paragraphing, where double newlines are
  converted in to paragraphs whenever possible. Auto-paragraphing:
</p>
<ul>
  <li>Always applies to inline elements or text in the root node,</li>
  <li>Applies to inline elements or text with double newlines in nodes
      that allow paragraph tags,</li>
  <li>Applies to double newlines in paragraph tags</li>
</ul>
<p>
  <code>p</code> tags must be allowed for this directive to take effect.
  We do not use <code>br</code> tags for paragraphing, as that is
  semantically incorrect.
</p>
<p>
  To prevent auto-paragraphing as a content-producer, refrain from using
  double-newlines except to specify a new paragraph or in contexts where
  it has special meaning (whitespace usually has no meaning except in
  tags like <code>pre</code>, so this should not be difficult.) To prevent
  the paragraphing of inline text adjacent to block elements, wrap them
  in <code>div</code> tags (the behavior is slightly different outside of
  the root node.)
</p>
<p>
  This directive has been available since 2.0.1.
</p>
');

/**
 * Injector that auto paragraphs text in the root node based on
 * double-spacing.
 */
class HTMLPurifier_Injector_AutoParagraph extends HTMLPurifier_Injector
{
    
    var $name = 'AutoParagraph';
    var $needed = array('p');
    
    function _pStart() {
        $par = new HTMLPurifier_Token_Start('p');
        $par->armor['MakeWellFormed_TagClosedError'] = true;
        return $par;
    }
    
    function handleText(&$token) {
        $text = $token->data;
        if (empty($this->currentNesting)) {
            if (!$this->allowsElement('p')) return;
            // case 1: we're in root node (and it allows paragraphs)
            $token = array($this->_pStart());
            $this->_splitText($text, $token);
        } elseif ($this->currentNesting[count($this->currentNesting)-1]->name == 'p') {
            // case 2: we're in a paragraph
            $token = array();
            $this->_splitText($text, $token);
        } elseif ($this->allowsElement('p')) {
            // case 3: we're in an element that allows paragraphs
            if (strpos($text, "\n\n") !== false) {
                // case 3.1: this text node has a double-newline
                $token = array($this->_pStart());
                $this->_splitText($text, $token);
            } else {
                $ok = false;
                // test if up-coming tokens are either block or have
                // a double newline in them
                $nesting = 0;
                for ($i = $this->inputIndex + 1; isset($this->inputTokens[$i]); $i++) {
                    if ($this->inputTokens[$i]->type == 'start'){
                        if (!$this->_isInline($this->inputTokens[$i])) {
                            // we haven't found a double-newline, and
                            // we've hit a block element, so don't paragraph
                            $ok = false;
                            break;
                        }
                        $nesting++;
                    }
                    if ($this->inputTokens[$i]->type == 'end') {
                        if ($nesting <= 0) break;
                        $nesting--;
                    }
                    if ($this->inputTokens[$i]->type == 'text') {
                        // found it!
                        if (strpos($this->inputTokens[$i]->data, "\n\n") !== false) {
                            $ok = true;
                            break;
                        }
                    }
                }
                if ($ok) {
                    // case 3.2: this text node is next to another node
                    // that will start a paragraph
                    $token = array($this->_pStart(), $token);
                }
            }
        }
        
    }
    
    function handleElement(&$token) {
        // check if we're inside a tag already
        if (!empty($this->currentNesting)) {
            if ($this->allowsElement('p')) {
                // special case: we're in an element that allows paragraphs
                
                // this token is already paragraph, abort
                if ($token->name == 'p') return;
                
                // this token is a block level, abort
                if (!$this->_isInline($token)) return;
                
                // check if this token is adjacent to the parent token
                $prev = $this->inputTokens[$this->inputIndex - 1];
                if ($prev->type != 'start') {
                    // not adjacent, we can abort early
                    // add lead paragraph tag if our token is inline
                    // and the previous tag was an end paragraph
                    if (
                        $prev->name == 'p' && $prev->type == 'end' &&
                        $this->_isInline($token)
                    ) {
                        $token = array($this->_pStart(), $token);
                    }
                    return;
                }
                
                // this token is the first child of the element that allows
                // paragraph. We have to peek ahead and see whether or not
                // there is anything inside that suggests that a paragraph
                // will be needed
                $ok = false;
                // maintain a mini-nesting counter, this lets us bail out
                // early if possible
                $j = 1; // current nesting, one is due to parent (we recalculate current token)
                for ($i = $this->inputIndex; isset($this->inputTokens[$i]); $i++) {
                    if ($this->inputTokens[$i]->type == 'start') $j++;
                    if ($this->inputTokens[$i]->type == 'end') $j--;
                    if ($this->inputTokens[$i]->type == 'text') {
                        if (strpos($this->inputTokens[$i]->data, "\n\n") !== false) {
                            $ok = true;
                            break;
                        }
                    }
                    if ($j <= 0) break;
                }
                if ($ok) {
                    $token = array($this->_pStart(), $token);
                }
            }
            return;
        }
        
        // check if the start tag counts as a "block" element
        if (!$this->_isInline($token)) return;
        
        // append a paragraph tag before the token
        $token = array($this->_pStart(), $token);
    }
    
    /**
     * Splits up a text in paragraph tokens and appends them
     * to the result stream that will replace the original
     * @param $data String text data that will be processed
     *    into paragraphs
     * @param $result Reference to array of tokens that the
     *    tags will be appended onto
     * @param $config Instance of HTMLPurifier_Config
     * @param $context Instance of HTMLPurifier_Context
     * @private
     */
    function _splitText($data, &$result) {
        $raw_paragraphs = explode("\n\n", $data);
        
        // remove empty paragraphs
        $paragraphs = array();
        $needs_start = false;
        $needs_end   = false;
        
        $c = count($raw_paragraphs);
        if ($c == 1) {
            // there were no double-newlines, abort quickly
            $result[] = new HTMLPurifier_Token_Text($data);
            return;
        }
        
        for ($i = 0; $i < $c; $i++) {
            $par = $raw_paragraphs[$i];
            if (trim($par) !== '') {
                $paragraphs[] = $par;
                continue;
            }
            if ($i == 0 && empty($result)) {
                // The empty result indicates that the AutoParagraph
                // injector did not add any start paragraph tokens.
                // The fact that the first paragraph is empty indicates
                // that there was a double-newline at the start of the
                // data.
                // Combined together, this means that we are in a paragraph,
                // and the newline means we should start a new one.
                $result[] = new HTMLPurifier_Token_End('p');
                // However, the start token should only be added if 
                // there is more processing to be done (i.e. there are
                // real paragraphs in here). If there are none, the
                // next start paragraph tag will be handled by the
                // next run-around the injector
                $needs_start = true;
            } elseif ($i + 1 == $c) {
                // a double-paragraph at the end indicates that
                // there is an overriding need to start a new paragraph
                // for the next section. This has no effect until
                // we've processed all of the other paragraphs though
                $needs_end = true;
            }
        }
        
        // check if there are no "real" paragraphs to be processed
        if (empty($paragraphs)) {
            return;
        }
        
        // add a start tag if an end tag was added while processing
        // the raw paragraphs (that happens if there's a leading double
        // newline)
        if ($needs_start) $result[] = $this->_pStart();
        
        // append the paragraphs onto the result
        foreach ($paragraphs as $par) {
            $result[] = new HTMLPurifier_Token_Text($par);
            $result[] = new HTMLPurifier_Token_End('p');
            $result[] = $this->_pStart();
        }
        
        // remove trailing start token, if one is needed, it will
        // be handled the next time this injector is called
        array_pop($result);
        
        // check the outside to determine whether or not the
        // end paragraph tag should be removed. It should be removed
        // unless the next non-whitespace token is a paragraph
        // or a block element.
        $remove_paragraph_end = true;
        
        if (!$needs_end) {
            // Start of the checks one after the current token's index
            for ($i = $this->inputIndex + 1; isset($this->inputTokens[$i]); $i++) {
                if ($this->inputTokens[$i]->type == 'start' || $this->inputTokens[$i]->type == 'empty') {
                    $remove_paragraph_end = $this->_isInline($this->inputTokens[$i]);
                }
                // check if we can abort early (whitespace means we carry-on!)
                if ($this->inputTokens[$i]->type == 'text' && !$this->inputTokens[$i]->is_whitespace) break;
                // end tags will automatically be handled by MakeWellFormed,
                // so we don't have to worry about them
                if ($this->inputTokens[$i]->type == 'end') break;
            }
        } else {
            $remove_paragraph_end = false;
        }
        
        // check the outside to determine whether or not the
        // end paragraph tag should be removed
        if ($remove_paragraph_end) {
            array_pop($result);
        }
        
    }
    
    /**
     * Returns true if passed token is inline (and, ergo, allowed in
     * paragraph tags)
     * @private
     */
    function _isInline($token) {
        return isset($this->htmlDefinition->info['p']->child->elements[$token->name]);
    }
    
}

