<?php //$Id$

require_once($CFG->dirroot . '/user/filters/radios.php'); 

/**
 * Generic yes/no filter with radio buttons for integer fields.
 */
class user_filter_yesno extends user_filter_radios {
    /**
     * Constructor
     * @param string $name the name of the filter instance
     * @param string $label the label of the filter instance
     * @param string $field the field used for filtering data
     * @param int $value the value used for filtering data
     */
    function user_filter_yesno($name, $label, $field, $value=-1) {
        parent::user_filter_radios($name, $label, $field, array(0=>get_string('no'), 1=>get_string('yes')), true, $value);
    }
}
