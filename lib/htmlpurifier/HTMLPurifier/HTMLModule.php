<?php

/**
 * Represents an XHTML 1.1 module, with information on elements, tags
 * and attributes.
 * @note Even though this is technically XHTML 1.1, it is also used for
 *       regular HTML parsing. We are using modulization as a convenient
 *       way to represent the internals of HTMLDefinition, and our
 *       implementation is by no means conforming and does not directly
 *       use the normative DTDs or XML schemas.
 * @note The public variables in a module should almost directly
 *       correspond to the variables in HTMLPurifier_HTMLDefinition.
 *       However, the prefix info carries no special meaning in these
 *       objects (include it anyway if that's the correspondence though).
 */

class HTMLPurifier_HTMLModule
{
    /**
     * Short unique string identifier of the module
     */
    var $name;
    
    /**
     * Dynamically set integer that specifies when the module was loaded in.
     */
    var $order;
    
    /**
     * Informally, a list of elements this module changes. Not used in
     * any significant way.
     * @protected
     */
    var $elements = array();
    
    /**
     * Associative array of element names to element definitions.
     * Some definitions may be incomplete, to be merged in later
     * with the full definition.
     * @public
     */
    var $info = array();
    
    /**
     * Associative array of content set names to content set additions.
     * This is commonly used to, say, add an A element to the Inline
     * content set. This corresponds to an internal variable $content_sets
     * and NOT info_content_sets member variable of HTMLDefinition.
     * @public
     */
    var $content_sets = array();
    
    /**
     * Associative array of attribute collection names to attribute
     * collection additions. More rarely used for adding attributes to
     * the global collections. Example is the StyleAttribute module adding
     * the style attribute to the Core. Corresponds to HTMLDefinition's
     * attr_collections->info, since the object's data is only info,
     * with extra behavior associated with it.
     * @public
     */
    var $attr_collections = array();
    
    /**
     * Associative array of deprecated tag name to HTMLPurifier_TagTransform
     * @public
     */
    var $info_tag_transform = array();
    
    /**
     * List of HTMLPurifier_AttrTransform to be performed before validation.
     * @public
     */
    var $info_attr_transform_pre = array();
    
    /**
     * List of HTMLPurifier_AttrTransform to be performed after validation.
     * @public
     */
    var $info_attr_transform_post = array();
    
    /**
     * Boolean flag that indicates whether or not getChildDef is implemented.
     * For optimization reasons: may save a call to a function. Be sure
     * to set it if you do implement getChildDef(), otherwise it will have
     * no effect!
     * @public
     */
    var $defines_child_def = false;
    
    /**
     * Retrieves a proper HTMLPurifier_ChildDef subclass based on 
     * content_model and content_model_type member variables of
     * the HTMLPurifier_ElementDef class. There is a similar function
     * in HTMLPurifier_HTMLDefinition.
     * @param $def HTMLPurifier_ElementDef instance
     * @return HTMLPurifier_ChildDef subclass
     * @public
     */
    function getChildDef($def) {return false;}
    
    /**
     * Hook method that lets module perform arbitrary operations on
     * HTMLPurifier_HTMLDefinition before the module gets processed.
     * @param $definition Reference to HTMLDefinition being setup
     */
    function preProcess(&$definition) {}
    
    /**
     * Hook method that lets module perform arbitrary operations
     * on HTMLPurifier_HTMLDefinition after the module gets processed.
     * @param $definition Reference to HTMLDefinition being setup
     */
    function postProcess(&$definition) {}
    
    /**
     * Hook method that is called when a module gets registered to
     * the definition.
     * @param $definition Reference to HTMLDefinition being setup
     */
    function setup(&$definition) {}
    
}

?>