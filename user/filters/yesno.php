<?php

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

    /**
     * Returns the condition to be used with SQL
     *
     * @param array $data filter settings
     * @return array sql string and $params
     */
    function get_sql_filter($data) {
        static $counter = 0;
        $name = 'ex_yesno'.$counter++;

        $value = $data['value'];
        $field = $this->_field;
        if ($value == '') {
            return array();
        }
        return array("$field=:$name", array($name => $value));
    }
}
