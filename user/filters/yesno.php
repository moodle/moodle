<?php //$Id$

/**
 * Generic yes/no filter with radio buttons for integer fields.
 */
class user_filter_yesno extends user_filter_simpleselect {

    /**
     * Constructor
     * @param string $name the name of the filter instance
     * @param string $label the label of the filter instance
     * @param boolean $advanced advanced form element flag
     * @param string $field user table filed name
     */
    function user_filter_yesno($name, $label, $advanced, $field) {
        parent::user_filter_simpleselect($name, $label, $advanced, $field, array(0=>get_string('no'), 1=>get_string('yes')));
    }
}
