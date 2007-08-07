<?php //$Id$

/**
 * Library of functions and constants for user filters
 */

require_once($CFG->dirroot . '/user/filters/user_filter_form.php');

/**
 * The base user filter.
 */
class user_filter_type {
    /**
     * The name of this filter instance.
     */
    var $_name;
    /**
     * The label of this filter instance.
     */
    var $_label;
    /**
     * The field database used for filtering data.
     */
    var $_field;
    /** 
     * The value used for filtering data.
     */
    var $_value;
    /**
     * Constructor
     * @param string $name the name of the filter instance
     * @param string $label the label of the filter instance
     * @param string $field the field used for filtering data
     * @param string $value the value used for filtering data
     */
    function user_filter_type($name, $label, $field, $value=null) {
        $this->_name = $name;
        $this->_label = $label;
        $this->_field = $field;
        $this->_value = $value;
    }
    
    /**
     * Returns the condition to be used with SQL where
     * @return string the filtering condition or null if the filter is disabled
     */
    function getSQLFilter() {
        return $this->_field . '="' . $this->_value . '"';
    }
    
    /**
     * Retrieves data from the form data
     * @param object $formdata data submited with the form
     */
    function checkData($formdata) {
        $field = $this->_name;
        $this->_value = (string)@$formdata->$field;
    }

    /**
     * Adds controls specific to this filter in the form.
     * @param object $mform a MoodleForm object to setup
     */
    function setupForm(&$mform) {
        $mform->addElement('text', $this->_name, $this->_label);
        $mform->setDefault($this->_name, $this->_value);
    }
    
    /**
     * Returns a human friendly description of the filter.
     * @return string filter description
     */
    function getDescription() {
        return $this->_label . ' is "' . $this->_value . '"';
    }
}
