<?php

// common defs that we'll support by default
require_once 'HTMLPurifier/ChildDef.php';
require_once 'HTMLPurifier/ChildDef/Empty.php';
require_once 'HTMLPurifier/ChildDef/Required.php';
require_once 'HTMLPurifier/ChildDef/Optional.php';
require_once 'HTMLPurifier/ChildDef/Custom.php';

// NOT UNIT TESTED!!!

class HTMLPurifier_ContentSets
{
    
    /**
     * List of content set strings (pipe seperators) indexed by name.
     * @public
     */
    var $info = array();
    
    /**
     * List of content set lookups (element => true) indexed by name.
     * @note This is in HTMLPurifier_HTMLDefinition->info_content_sets
     * @public
     */
    var $lookup = array();
    
    /**
     * Synchronized list of defined content sets (keys of info)
     */
    var $keys = array();
    /**
     * Synchronized list of defined content values (values of info)
     */
    var $values = array();
    
    /**
     * Merges in module's content sets, expands identifiers in the content
     * sets and populates the keys, values and lookup member variables.
     * @param $modules List of HTMLPurifier_HTMLModule
     */
    function HTMLPurifier_ContentSets($modules) {
        if (!is_array($modules)) $modules = array($modules);
        // populate content_sets based on module hints
        // sorry, no way of overloading
        foreach ($modules as $module_i => $module) {
            foreach ($module->content_sets as $key => $value) {
                if (isset($this->info[$key])) {
                    // add it into the existing content set
                    $this->info[$key] = $this->info[$key] . ' | ' . $value;
                } else {
                    $this->info[$key] = $value;
                }
            }
        }
        // perform content_set expansions
        $this->keys = array_keys($this->info);
        foreach ($this->info as $i => $set) {
            // only performed once, so infinite recursion is not
            // a problem
            $this->info[$i] =
                str_replace(
                    $this->keys,
                    // must be recalculated each time due to
                    // changing substitutions
                    array_values($this->info),
                $set);
        }
        $this->values = array_values($this->info);
        
        // generate lookup tables
        foreach ($this->info as $name => $set) {
            $this->lookup[$name] = $this->convertToLookup($set);
        }
    }
    
    /**
     * Accepts a definition; generates and assigns a ChildDef for it
     * @param $def HTMLPurifier_ElementDef reference
     * @param $module Module that defined the ElementDef
     */
    function generateChildDef(&$def, $module) {
        if (!empty($def->child)) return; // already done!
        $content_model = $def->content_model;
        if (is_string($content_model)) {
            $def->content_model = str_replace(
                $this->keys, $this->values, $content_model);
        }
        $def->child = $this->getChildDef($def, $module);
    }
    
    /**
     * Instantiates a ChildDef based on content_model and content_model_type
     * member variables in HTMLPurifier_ElementDef
     * @note This will also defer to modules for custom HTMLPurifier_ChildDef
     *       subclasses that need content set expansion
     * @param $def HTMLPurifier_ElementDef to have ChildDef extracted
     * @return HTMLPurifier_ChildDef corresponding to ElementDef
     */
    function getChildDef($def, $module) {
        $value = $def->content_model;
        if (is_object($value)) {
            trigger_error(
                'Literal object child definitions should be stored in '.
                'ElementDef->child not ElementDef->content_model',
                E_USER_NOTICE
            );
            return $value;
        }
        switch ($def->content_model_type) {
            case 'required':
                return new HTMLPurifier_ChildDef_Required($value);
            case 'optional':
                return new HTMLPurifier_ChildDef_Optional($value);
            case 'empty':
                return new HTMLPurifier_ChildDef_Empty();
            case 'custom':
                return new HTMLPurifier_ChildDef_Custom($value);
        }
        // defer to its module
        $return = false;
        if ($module->defines_child_def) { // save a func call
            $return = $module->getChildDef($def);
        }
        if ($return !== false) return $return;
        // error-out
        trigger_error(
            'Could not determine which ChildDef class to instantiate',
            E_USER_ERROR
        );
        return false;
    }
    
    /**
     * Converts a string list of elements separated by pipes into
     * a lookup array.
     * @param $string List of elements
     * @return Lookup array of elements
     */
    function convertToLookup($string) {
        $array = explode('|', str_replace(' ', '', $string));
        $ret = array();
        foreach ($array as $i => $k) {
            $ret[$k] = true;
        }
        return $ret;
    }
    
}

