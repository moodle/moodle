<?php

require_once 'HTMLPurifier/HTMLModule.php';
require_once 'HTMLPurifier/AttrDef/HTML/LinkTypes.php';

/**
 * XHTML 1.1 Hypertext Module, defines hypertext links. Core Module.
 */
class HTMLPurifier_HTMLModule_Hypertext extends HTMLPurifier_HTMLModule
{
    
    var $name = 'Hypertext';
    var $elements = array('a');
    var $content_sets = array('Inline' => 'a');
    
    function HTMLPurifier_HTMLModule_Hypertext() {
        $this->info['a'] = new HTMLPurifier_ElementDef();
        $this->info['a']->attr = array(
            0 => array('Common'),
            // 'accesskey' => 'Character',
            // 'charset' => 'Charset',
            'href' => 'URI',
            //'hreflang' => 'LanguageCode',
            'rel' => new HTMLPurifier_AttrDef_HTML_LinkTypes('rel'),
            'rev' => new HTMLPurifier_AttrDef_HTML_LinkTypes('rev'),
            //'tabindex' => 'Number',
            //'type' => 'ContentType',
        );
        $this->info['a']->content_model = '#PCDATA | Inline';
        $this->info['a']->content_model_type = 'optional';
        $this->info['a']->excludes = array('a' => true);
    }
    
}

?>