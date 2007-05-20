<?php

// components
require_once 'HTMLPurifier/HTMLModuleManager.php';

// this definition and its modules MUST NOT define configuration directives
// outside of the HTML or Attr namespaces

// will be superceded by more accurate doctype declaration schemes
HTMLPurifier_ConfigSchema::define(
    'HTML', 'Strict', false, 'bool',
    'Determines whether or not to use Transitional (loose) or Strict rulesets. '.
    'This directive has been available since 1.3.0.'
);

HTMLPurifier_ConfigSchema::define(
    'HTML', 'BlockWrapper', 'p', 'string',
    'String name of element to wrap inline elements that are inside a block '.
    'context.  This only occurs in the children of blockquote in strict mode. '.
    'Example: by default value, <code>&lt;blockquote&gt;Foo&lt;/blockquote&gt;</code> '.
    'would become <code>&lt;blockquote&gt;&lt;p&gt;Foo&lt;/p&gt;&lt;/blockquote&gt;</code>. The '.
    '<code>&lt;p&gt;</code> tags can be replaced '.
    'with whatever you desire, as long as it is a block level element. '.
    'This directive has been available since 1.3.0.'
);

HTMLPurifier_ConfigSchema::define(
    'HTML', 'Parent', 'div', 'string',
    'String name of element that HTML fragment passed to library will be '.
    'inserted in.  An interesting variation would be using span as the '.
    'parent element, meaning that only inline tags would be allowed. '.
    'This directive has been available since 1.3.0.'
);

HTMLPurifier_ConfigSchema::define(
    'HTML', 'AllowedElements', null, 'lookup/null',
    'If HTML Purifier\'s tag set is unsatisfactory for your needs, you '.
    'can overload it with your own list of tags to allow.  Note that this '.
    'method is subtractive: it does its job by taking away from HTML Purifier '.
    'usual feature set, so you cannot add a tag that HTML Purifier never '.
    'supported in the first place (like embed, form or head).  If you change this, you '.
    'probably also want to change %HTML.AllowedAttributes. '.
    '<strong>Warning:</strong> If another directive conflicts with the '.
    'elements here, <em>that</em> directive will win and override. '.
    'This directive has been available since 1.3.0.'
);

HTMLPurifier_ConfigSchema::define(
    'HTML', 'AllowedAttributes', null, 'lookup/null',
    'IF HTML Purifier\'s attribute set is unsatisfactory, overload it! '.
    'The syntax is \'tag.attr\' or \'*.attr\' for the global attributes '.
    '(style, id, class, dir, lang, xml:lang).'.
    '<strong>Warning:</strong> If another directive conflicts with the '.
    'elements here, <em>that</em> directive will win and override. For '.
    'example, %HTML.EnableAttrID will take precedence over *.id in this '.
    'directive.  You must set that directive to true before you can use '.
    'IDs at all. This directive has been available since 1.3.0.'
);

/**
 * Definition of the purified HTML that describes allowed children,
 * attributes, and many other things.
 * 
 * Conventions:
 * 
 * All member variables that are prefixed with info
 * (including the main $info array) are used by HTML Purifier internals
 * and should not be directly edited when customizing the HTMLDefinition.
 * They can usually be set via configuration directives or custom
 * modules.
 * 
 * On the other hand, member variables without the info prefix are used
 * internally by the HTMLDefinition and MUST NOT be used by other HTML
 * Purifier internals. Many of them, however, are public, and may be
 * edited by userspace code to tweak the behavior of HTMLDefinition.
 * 
 * HTMLPurifier_Printer_HTMLDefinition is a notable exception to this
 * rule: in the interest of comprehensiveness, it will sniff everything.
 */
class HTMLPurifier_HTMLDefinition
{
    
    /** FULLY-PUBLIC VARIABLES */
    
    /**
     * Associative array of element names to HTMLPurifier_ElementDef
     * @public
     */
    var $info = array();
    
    /**
     * Associative array of global attribute name to attribute definition.
     * @public
     */
    var $info_global_attr = array();
    
    /**
     * String name of parent element HTML will be going into.
     * @public
     */
    var $info_parent = 'div';
    
    /**
     * Definition for parent element, allows parent element to be a
     * tag that's not allowed inside the HTML fragment.
     * @public
     */
    var $info_parent_def;
    
    /**
     * String name of element used to wrap inline elements in block context
     * @note This is rarely used except for BLOCKQUOTEs in strict mode
     * @public
     */
    var $info_block_wrapper = 'p';
    
    /**
     * Associative array of deprecated tag name to HTMLPurifier_TagTransform
     * @public
     */
    var $info_tag_transform = array();
    
    /**
     * Indexed list of HTMLPurifier_AttrTransform to be performed before validation.
     * @public
     */
    var $info_attr_transform_pre = array();
    
    /**
     * Indexed list of HTMLPurifier_AttrTransform to be performed after validation.
     * @public
     */
    var $info_attr_transform_post = array();
    
    /**
     * Nested lookup array of content set name (Block, Inline) to
     * element name to whether or not it belongs in that content set.
     * @public
     */
    var $info_content_sets = array();
    
    
    
    /** PUBLIC BUT INTERNAL VARIABLES */
    
    var $setup = false; /**< Has setup() been called yet? */
    var $config; /**< Temporary instance of HTMLPurifier_Config */
    
    var $manager; /**< Instance of HTMLPurifier_HTMLModuleManager */
    
    /**
     * Performs low-cost, preliminary initialization.
     * @param $config Instance of HTMLPurifier_Config
     */
    function HTMLPurifier_HTMLDefinition(&$config) {
        $this->config =& $config;
        $this->manager = new HTMLPurifier_HTMLModuleManager();
    }
    
    /**
     * Processes internals into form usable by HTMLPurifier internals. 
     * Modifying the definition after calling this function should not
     * be done.
     */
    function setup() {
        
        // multiple call guard
        if ($this->setup) {return;} else {$this->setup = true;}
        
        $this->processModules();
        $this->setupConfigStuff();
        
        unset($this->config);
        unset($this->manager);
        
    }
    
    /**
     * Extract out the information from the manager
     */
    function processModules() {
        
        $this->manager->setup($this->config);
        
        foreach ($this->manager->activeModules as $module) {
            foreach($module->info_tag_transform         as $k => $v) {
                if ($v === false) unset($this->info_tag_transform[$k]);
                else $this->info_tag_transform[$k] = $v;
            }
            foreach($module->info_attr_transform_pre    as $k => $v) {
                if ($v === false) unset($this->info_attr_transform_pre[$k]);
                else $this->info_attr_transform_pre[$k] = $v;
            }
            foreach($module->info_attr_transform_post   as $k => $v) {
                if ($v === false) unset($this->info_attr_transform_post[$k]);
                else $this->info_attr_transform_post[$k] = $v;
            }
        }
        
        $this->info = $this->manager->getElements($this->config);
        $this->info_content_sets = $this->manager->contentSets->lookup;
        
    }
    
    /**
     * Sets up stuff based on config. We need a better way of doing this.
     */
    function setupConfigStuff() {
        
        $block_wrapper = $this->config->get('HTML', 'BlockWrapper');
        if (isset($this->info_content_sets['Block'][$block_wrapper])) {
            $this->info_block_wrapper = $block_wrapper;
        } else {
            trigger_error('Cannot use non-block element as block wrapper.',
                E_USER_ERROR);
        }
        
        $parent = $this->config->get('HTML', 'Parent');
        $def = $this->manager->getElement($parent, $this->config);
        if ($def) {
            $this->info_parent = $parent;
            $this->info_parent_def = $def;
        } else {
            trigger_error('Cannot use unrecognized element as parent.',
                E_USER_ERROR);
            $this->info_parent_def = $this->manager->getElement(
                $this->info_parent, $this->config);
        }
        
        // support template text
        $support = "(for information on implementing this, see the ".
                   "support forums) ";
        
        // setup allowed elements, SubtractiveWhitelist module
        $allowed_elements = $this->config->get('HTML', 'AllowedElements');
        if (is_array($allowed_elements)) {
            foreach ($this->info as $name => $d) {
                if(!isset($allowed_elements[$name])) unset($this->info[$name]);
                unset($allowed_elements[$name]);
            }
            // emit errors
            foreach ($allowed_elements as $element => $d) {
                trigger_error("Element '$element' is not supported $support", E_USER_WARNING);
            }
        }
        
        $allowed_attributes = $this->config->get('HTML', 'AllowedAttributes');
        $allowed_attributes_mutable = $allowed_attributes; // by copy!
        if (is_array($allowed_attributes)) {
            foreach ($this->info_global_attr as $attr_key => $info) {
                if (!isset($allowed_attributes["*.$attr_key"])) {
                    unset($this->info_global_attr[$attr_key]);
                } elseif (isset($allowed_attributes_mutable["*.$attr_key"])) {
                    unset($allowed_attributes_mutable["*.$attr_key"]);
                }
            }
            foreach ($this->info as $tag => $info) {
                foreach ($info->attr as $attr => $attr_info) {
                    if (!isset($allowed_attributes["$tag.$attr"]) &&
                        !isset($allowed_attributes["*.$attr"])) {
                        unset($this->info[$tag]->attr[$attr]);
                    } else {
                        if (isset($allowed_attributes_mutable["$tag.$attr"])) {
                            unset($allowed_attributes_mutable["$tag.$attr"]);
                        } elseif (isset($allowed_attributes_mutable["*.$attr"])) {
                            unset($allowed_attributes_mutable["*.$attr"]);
                        }
                    }
                }
            }
            // emit errors
            foreach ($allowed_attributes_mutable as $elattr => $d) {
                list($element, $attribute) = explode('.', $elattr);
                if ($element == '*') {
                    trigger_error("Global attribute '$attribute' is not ".
                        "supported in any elements $support",
                        E_USER_WARNING);
                } else {
                    trigger_error("Attribute '$attribute' in element '$element' not supported $support",
                        E_USER_WARNING);
                }
            }
        }
        
    }
    
    
}

?>
