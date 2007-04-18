<?php

require_once 'HTMLPurifier/AttrTypes.php';
require_once 'HTMLPurifier/AttrDef/Lang.php';

/**
 * Defines common attribute collections that modules reference
 */

class HTMLPurifier_AttrCollections
{
    
    /**
     * Associative array of attribute collections, indexed by name
     * @note Technically, the composition of these is more complicated,
     *       but we bypass it using our own excludes property
     */
    var $info = array();
    
    /**
     * Performs all expansions on internal data for use by other inclusions
     * It also collects all attribute collection extensions from
     * modules
     * @param $attr_types HTMLPurifier_AttrTypes instance
     * @param $modules Hash array of HTMLPurifier_HTMLModule members
     */
    function HTMLPurifier_AttrCollections($attr_types, $modules) {
        $info =& $this->info;
        // load extensions from the modules
        foreach ($modules as $module) {
            foreach ($module->attr_collections as $coll_i => $coll) {
                foreach ($coll as $attr_i => $attr) {
                    if ($attr_i === 0 && isset($info[$coll_i][$attr_i])) {
                        // merge in includes
                        $info[$coll_i][$attr_i] = array_merge(
                            $info[$coll_i][$attr_i], $attr);
                        continue;
                    }
                    $info[$coll_i][$attr_i] = $attr;
                }
            }
        }
        // perform internal expansions and inclusions
        foreach ($info as $name => $attr) {
            // merge attribute collections that include others
            $this->performInclusions($info[$name]);
            // replace string identifiers with actual attribute objects
            $this->expandIdentifiers($info[$name], $attr_types);
        }
    }
    
    /**
     * Takes a reference to an attribute associative array and performs
     * all inclusions specified by the zero index.
     * @param &$attr Reference to attribute array
     */
    function performInclusions(&$attr) {
        if (!isset($attr[0])) return;
        $merge = $attr[0];
        // loop through all the inclusions
        for ($i = 0; isset($merge[$i]); $i++) {
            // foreach attribute of the inclusion, copy it over
            foreach ($this->info[$merge[$i]] as $key => $value) {
                if (isset($attr[$key])) continue; // also catches more inclusions
                $attr[$key] = $value;
            }
            if (isset($info[$merge[$i]][0])) {
                // recursion
                $merge = array_merge($merge, isset($info[$merge[$i]][0]));
            }
        }
        unset($attr[0]);
    }
    
    /**
     * Expands all string identifiers in an attribute array by replacing
     * them with the appropriate values inside HTMLPurifier_AttrTypes
     * @param &$attr Reference to attribute array
     * @param $attr_types HTMLPurifier_AttrTypes instance
     */
    function expandIdentifiers(&$attr, $attr_types) {
        foreach ($attr as $def_i => $def) {
            if ($def_i === 0) continue;
            if (!is_string($def)) continue;
            if ($def === false) {
                unset($attr[$def_i]);
                continue;
            }
            if (isset($attr_types->info[$def])) {
                $attr[$def_i] = $attr_types->info[$def];
            } else {
                trigger_error('Attempted to reference undefined attribute type', E_USER_ERROR);
                unset($attr[$def_i]);
            }
        }
    }
    
}

?>