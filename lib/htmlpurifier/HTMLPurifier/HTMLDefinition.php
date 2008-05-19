<?php

require_once 'HTMLPurifier/Definition.php';
require_once 'HTMLPurifier/HTMLModuleManager.php';

// this definition and its modules MUST NOT define configuration directives
// outside of the HTML or Attr namespaces

HTMLPurifier_ConfigSchema::define(
    'HTML', 'DefinitionID', null, 'string/null', '
<p>
    Unique identifier for a custom-built HTML definition. If you edit
    the raw version of the HTMLDefinition, introducing changes that the
    configuration object does not reflect, you must specify this variable.
    If you change your custom edits, you should change this directive, or
    clear your cache. Example:
</p>
<pre>
$config = HTMLPurifier_Config::createDefault();
$config->set(\'HTML\', \'DefinitionID\', \'1\');
$def = $config->getHTMLDefinition();
$def->addAttribute(\'a\', \'tabindex\', \'Number\');
</pre>
<p>
    In the above example, the configuration is still at the defaults, but
    using the advanced API, an extra attribute has been added. The
    configuration object normally has no way of knowing that this change
    has taken place, so it needs an extra directive: %HTML.DefinitionID.
    If someone else attempts to use the default configuration, these two
    pieces of code will not clobber each other in the cache, since one has
    an extra directive attached to it.
</p>
<p>
    This directive has been available since 2.0.0, and in that version or
    later you <em>must</em> specify a value to this directive to use the
    advanced API features.
</p>
');

HTMLPurifier_ConfigSchema::define(
    'HTML', 'DefinitionRev', 1, 'int', '
<p>
    Revision identifier for your custom definition specified in
    %HTML.DefinitionID.  This serves the same purpose: uniquely identifying
    your custom definition, but this one does so in a chronological
    context: revision 3 is more up-to-date then revision 2.  Thus, when
    this gets incremented, the cache handling is smart enough to clean
    up any older revisions of your definition as well as flush the
    cache.  This directive has been available since 2.0.0.
</p>
');

HTMLPurifier_ConfigSchema::define(
    'HTML', 'BlockWrapper', 'p', 'string', '
<p>
    String name of element to wrap inline elements that are inside a block
    context.  This only occurs in the children of blockquote in strict mode.
</p>
<p>
    Example: by default value,
    <code>&lt;blockquote&gt;Foo&lt;/blockquote&gt;</code> would become
    <code>&lt;blockquote&gt;&lt;p&gt;Foo&lt;/p&gt;&lt;/blockquote&gt;</code>.
    The <code>&lt;p&gt;</code> tags can be replaced with whatever you desire,
    as long as it is a block level element. This directive has been available
    since 1.3.0.
</p>
');

HTMLPurifier_ConfigSchema::define(
    'HTML', 'Parent', 'div', 'string', '
<p>
    String name of element that HTML fragment passed to library will be 
    inserted in.  An interesting variation would be using span as the 
    parent element, meaning that only inline tags would be allowed. 
    This directive has been available since 1.3.0.
</p>
');

HTMLPurifier_ConfigSchema::define(
    'HTML', 'AllowedElements', null, 'lookup/null', '
<p>
    If HTML Purifier\'s tag set is unsatisfactory for your needs, you 
    can overload it with your own list of tags to allow.  Note that this 
    method is subtractive: it does its job by taking away from HTML Purifier 
    usual feature set, so you cannot add a tag that HTML Purifier never 
    supported in the first place (like embed, form or head).  If you 
    change this, you probably also want to change %HTML.AllowedAttributes. 
</p>
<p>
    <strong>Warning:</strong> If another directive conflicts with the 
    elements here, <em>that</em> directive will win and override. 
    This directive has been available since 1.3.0.
</p>
');

HTMLPurifier_ConfigSchema::define(
    'HTML', 'AllowedAttributes', null, 'lookup/null', '
<p>
    If HTML Purifier\'s attribute set is unsatisfactory, overload it! 
    The syntax is "tag.attr" or "*.attr" for the global attributes 
    (style, id, class, dir, lang, xml:lang).
</p>
<p>
    <strong>Warning:</strong> If another directive conflicts with the 
    elements here, <em>that</em> directive will win and override. For 
    example, %HTML.EnableAttrID will take precedence over *.id in this 
    directive.  You must set that directive to true before you can use 
    IDs at all. This directive has been available since 1.3.0.
</p>
');

HTMLPurifier_ConfigSchema::define(
    'HTML', 'Allowed', null, 'itext/null', '
<p>
    This is a convenience directive that rolls the functionality of
    %HTML.AllowedElements and %HTML.AllowedAttributes into one directive.
    Specify elements and attributes that are allowed using:
    <code>element1[attr1|attr2],element2...</code>. You can also use
    newlines instead of commas to separate elements.
</p>
<p>
    <strong>Warning</strong>:
    All of the constraints on the component directives are still enforced.
    The syntax is a <em>subset</em> of TinyMCE\'s <code>valid_elements</code>
    whitelist: directly copy-pasting it here will probably result in
    broken whitelists. If %HTML.AllowedElements or %HTML.AllowedAttributes
    are set, this directive has no effect.
    This directive has been available since 2.0.0.
</p>
');

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
 * @note This class is inspected by Printer_HTMLDefinition; please
 *       update that class if things here change.
 */
class HTMLPurifier_HTMLDefinition extends HTMLPurifier_Definition
{
    
    // FULLY-PUBLIC VARIABLES ---------------------------------------------
    
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
    
    /**
     * Doctype object
     */
    var $doctype;
    
    
    
    // RAW CUSTOMIZATION STUFF --------------------------------------------
    
    /**
     * Adds a custom attribute to a pre-existing element
     * @note This is strictly convenience, and does not have a corresponding
     *       method in HTMLPurifier_HTMLModule
     * @param $element_name String element name to add attribute to
     * @param $attr_name String name of attribute
     * @param $def Attribute definition, can be string or object, see
     *             HTMLPurifier_AttrTypes for details
     */
    function addAttribute($element_name, $attr_name, $def) {
        $module =& $this->getAnonymousModule();
        if (!isset($module->info[$element_name])) {
            $element =& $module->addBlankElement($element_name);
        } else {
            $element =& $module->info[$element_name];
        }
        $element->attr[$attr_name] = $def;
    }
    
    /**
     * Adds a custom element to your HTML definition
     * @note See HTMLPurifier_HTMLModule::addElement for detailed 
     *       parameter and return value descriptions.
     */
    function &addElement($element_name, $type, $contents, $attr_collections, $attributes) {
        $module =& $this->getAnonymousModule();
        // assume that if the user is calling this, the element
        // is safe. This may not be a good idea
        $element =& $module->addElement($element_name, true, $type, $contents, $attr_collections, $attributes);
        return $element;
    }
    
    /**
     * Adds a blank element to your HTML definition, for overriding
     * existing behavior
     * @note See HTMLPurifier_HTMLModule::addBlankElement for detailed
     *       parameter and return value descriptions.
     */
    function &addBlankElement($element_name) {
        $module  =& $this->getAnonymousModule();
        $element =& $module->addBlankElement($element_name);
        return $element;
    }
    
    /**
     * Retrieves a reference to the anonymous module, so you can
     * bust out advanced features without having to make your own
     * module.
     */
    function &getAnonymousModule() {
        if (!$this->_anonModule) {
            $this->_anonModule = new HTMLPurifier_HTMLModule();
            $this->_anonModule->name = 'Anonymous';
        }
        return $this->_anonModule;
    }
    
    var $_anonModule;
    
    
    // PUBLIC BUT INTERNAL VARIABLES --------------------------------------
    
    var $type = 'HTML';
    var $manager; /**< Instance of HTMLPurifier_HTMLModuleManager */
    
    /**
     * Performs low-cost, preliminary initialization.
     */
    function HTMLPurifier_HTMLDefinition() {
        $this->manager = new HTMLPurifier_HTMLModuleManager();
    }
    
    function doSetup($config) {
        $this->processModules($config);
        $this->setupConfigStuff($config);
        unset($this->manager);
        
        // cleanup some of the element definitions
        foreach ($this->info as $k => $v) {
            unset($this->info[$k]->content_model);
            unset($this->info[$k]->content_model_type);
        }
    }
    
    /**
     * Extract out the information from the manager
     */
    function processModules($config) {
        
        if ($this->_anonModule) {
            // for user specific changes
            // this is late-loaded so we don't have to deal with PHP4
            // reference wonky-ness
            $this->manager->addModule($this->_anonModule);
            unset($this->_anonModule);
        }
        
        $this->manager->setup($config);
        $this->doctype = $this->manager->doctype;
        
        foreach ($this->manager->modules as $module) {
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
        
        $this->info = $this->manager->getElements();
        $this->info_content_sets = $this->manager->contentSets->lookup;
        
    }
    
    /**
     * Sets up stuff based on config. We need a better way of doing this.
     */
    function setupConfigStuff($config) {
        
        $block_wrapper = $config->get('HTML', 'BlockWrapper');
        if (isset($this->info_content_sets['Block'][$block_wrapper])) {
            $this->info_block_wrapper = $block_wrapper;
        } else {
            trigger_error('Cannot use non-block element as block wrapper',
                E_USER_ERROR);
        }
        
        $parent = $config->get('HTML', 'Parent');
        $def = $this->manager->getElement($parent, true);
        if ($def) {
            $this->info_parent = $parent;
            $this->info_parent_def = $def;
        } else {
            trigger_error('Cannot use unrecognized element as parent',
                E_USER_ERROR);
            $this->info_parent_def = $this->manager->getElement($this->info_parent, true);
        }
        
        // support template text
        $support = "(for information on implementing this, see the ".
                   "support forums) ";
        
        // setup allowed elements
        
        $allowed_elements = $config->get('HTML', 'AllowedElements');
        $allowed_attributes = $config->get('HTML', 'AllowedAttributes');
        
        if (!is_array($allowed_elements) && !is_array($allowed_attributes)) {
            $allowed = $config->get('HTML', 'Allowed');
            if (is_string($allowed)) {
                list($allowed_elements, $allowed_attributes) = $this->parseTinyMCEAllowedList($allowed);
            }
        }
        
        if (is_array($allowed_elements)) {
            foreach ($this->info as $name => $d) {
                if(!isset($allowed_elements[$name])) unset($this->info[$name]);
                unset($allowed_elements[$name]);
            }
            // emit errors
            foreach ($allowed_elements as $element => $d) {
                $element = htmlspecialchars($element);
                trigger_error("Element '$element' is not supported $support", E_USER_WARNING);
            }
        }
        
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
                $element = htmlspecialchars($element);
                $attribute = htmlspecialchars($attribute);
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
    
    /**
     * Parses a TinyMCE-flavored Allowed Elements and Attributes list into
     * separate lists for processing. Format is element[attr1|attr2],element2...
     * @warning Although it's largely drawn from TinyMCE's implementation,
     *      it is different, and you'll probably have to modify your lists
     * @param $list String list to parse
     * @param array($allowed_elements, $allowed_attributes)
     */
    function parseTinyMCEAllowedList($list) {
        
        $elements = array();
        $attributes = array();
        
        $chunks = preg_split('/(,|[\n\r]+)/', $list);
        foreach ($chunks as $chunk) {
            if (empty($chunk)) continue;
            // remove TinyMCE element control characters
            if (!strpos($chunk, '[')) {
                $element = $chunk;
                $attr = false;
            } else {
                list($element, $attr) = explode('[', $chunk);
            }
            if ($element !== '*') $elements[$element] = true;
            if (!$attr) continue;
            $attr = substr($attr, 0, strlen($attr) - 1); // remove trailing ]
            $attr = explode('|', $attr);
            foreach ($attr as $key) {
                $attributes["$element.$key"] = true;
            }
        }
        
        return array($elements, $attributes);
        
    }
    
    
}


