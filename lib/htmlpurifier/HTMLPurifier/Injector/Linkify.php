<?php

require_once 'HTMLPurifier/Injector.php';

HTMLPurifier_ConfigSchema::define(
    'AutoFormat', 'Linkify', false, 'bool', '
<p>
  This directive turns on linkification, auto-linking http, ftp and
  https URLs. <code>a</code> tags with the <code>href</code> attribute
  must be allowed. This directive has been available since 2.0.1.
</p>
');

/**
 * Injector that converts http, https and ftp text URLs to actual links.
 */
class HTMLPurifier_Injector_Linkify extends HTMLPurifier_Injector
{
    
    var $name = 'Linkify';
    var $needed = array('a' => array('href'));
    
    function handleText(&$token) {
        if (!$this->allowsElement('a')) return;
        
        if (strpos($token->data, '://') === false) {
            // our really quick heuristic failed, abort
            // this may not work so well if we want to match things like
            // "google.com", but then again, most people don't
            return;
        }
        
        // there is/are URL(s). Let's split the string:
        // Note: this regex is extremely permissive
        $bits = preg_split('#((?:https?|ftp)://[^\s\'"<>()]+)#S', $token->data, -1, PREG_SPLIT_DELIM_CAPTURE);
        
        $token = array();
        
        // $i = index
        // $c = count
        // $l = is link
        for ($i = 0, $c = count($bits), $l = false; $i < $c; $i++, $l = !$l) {
            if (!$l) {
                if ($bits[$i] === '') continue;
                $token[] = new HTMLPurifier_Token_Text($bits[$i]);
            } else {
                $token[] = new HTMLPurifier_Token_Start('a', array('href' => $bits[$i]));
                $token[] = new HTMLPurifier_Token_Text($bits[$i]);
                $token[] = new HTMLPurifier_Token_End('a');
            }
        }
        
    }
    
}

