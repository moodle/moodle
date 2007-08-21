<?php

/**
 * Component of HTMLPurifier_AttrContext that accumulates IDs to prevent dupes
 * @note In Slashdot-speak, dupe means duplicate.
 * @note This class does not accept $config or $context, thus, it is the
 *       burden of the callee to register the appropriate errors or
 *       configuration.
 */
class HTMLPurifier_IDAccumulator
{
    
    /**
     * Lookup table of IDs we've accumulated.
     * @public
     */
    var $ids = array();
    
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

