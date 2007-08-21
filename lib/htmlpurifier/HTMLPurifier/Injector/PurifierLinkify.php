<?php

require_once 'HTMLPurifier/Injector.php';

HTMLPurifier_ConfigSchema::define(
    'AutoFormat', 'PurifierLinkify', false, 'bool', '
<p>
  Internal auto-formatter that converts configuration directives in
  syntax <a>%Namespace.Directive</a> to links. <code>a</code> tags
  with the <code>href</code> attribute must be allowed.
  This directive has been available since 2.0.1.
</p>
');

HTMLPurifier_ConfigSchema::define(
    'AutoFormatParam', 'PurifierLinkifyDocURL', '#%s', 'string', '
<p>
  Location of configuration documentation to link to, let %s substitute
  into the configuration\'s namespace and directive names sans the percent
  sign. This directive has been available since 2.0.1.
</p>
');

/**
 * Injector that converts configuration directive syntax %Namespace.Directive
 * to links
 */
class HTMLPurifier_Injector_PurifierLinkify extends HTMLPurifier_Injector
{
    
    var $name = 'PurifierLinkify';
    var $docURL;
    var $needed = array('a' => array('href'));
    
    function prepare($config, &$context) {
        $this->docURL = $config->get('AutoFormatParam', 'PurifierLinkifyDocURL');
        return parent::prepare($config, $context);
    }
    
    function handleText(&$token) {
        if (!$this->allowsElement('a')) return;
        if (strpos($token->data, '%') === false) return;
        
        $bits = preg_split('#%([a-z0-9]+\.[a-z0-9]+)#Si', $token->data, -1, PREG_SPLIT_DELIM_CAPTURE);
        $token = array();
        
        // $i = index
        // $c = count
        // $l = is link
        for ($i = 0, $c = count($bits), $l = false; $i < $c; $i++, $l = !$l) {
            if (!$l) {
                if ($bits[$i] === '') continue;
                $token[] = new HTMLPurifier_Token_Text($bits[$i]);
            } else {
                $token[] = new HTMLPurifier_Token_Start('a',
                    array('href' => str_replace('%s', $bits[$i], $this->docURL)));
                $token[] = new HTMLPurifier_Token_Text('%' . $bits[$i]);
                $token[] = new HTMLPurifier_Token_End('a');
            }
        }
        
    }
    
}

