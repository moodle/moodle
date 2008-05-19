<?php

HTMLPurifier_ConfigSchema::define(
    'Attr', 'IDBlacklist', array(), 'list',
    'Array of IDs not allowed in the document.'
);

/**
 * Component of HTMLPurifier_AttrContext that accumulates IDs to prevent dupes
 * @note In Slashdot-speak, dupe means duplicate.
 * @note The default constructor does not accept $config or $context objects:
 *       use must use the static build() factory method to perform initialization.
 */
class HTMLPurifier_IDAccumulator
{
    
    /**
     * Lookup table of IDs we've accumulated.
     * @public
     */
    var $ids = array();
    
    /**
     * Builds an IDAccumulator, also initializing the default blacklist
     * @param $config Instance of HTMLPurifier_Config
     * @param $context Instance of HTMLPurifier_Context
     * @return Fully initialized HTMLPurifier_IDAccumulator
     * @static
     */
    function build($config, &$context) {
        $acc = new HTMLPurifier_IDAccumulator();
        $acc->load($config->get('Attr', 'IDBlacklist'));
        return $acc;
    }
    
    /**
     * Add an ID to the lookup table.
     * @param $id ID to be added.
     * @return Bool status, true if success, false if there's a dupe
     */
    function add($id) {
        if (isset($this->ids[$id])) return false;
        return $this->ids[$id] = true;
    }
    
    /**
     * Load a list of IDs into the lookup table
     * @param $array_of_ids Array of IDs to load
     * @note This function doesn't care about duplicates
     */
    function load($array_of_ids) {
        foreach ($array_of_ids as $id) {
            $this->ids[$id] = true;
        }
    }
    
}

