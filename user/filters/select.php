<?php //$Id$

require_once($CFG->dirroot . '/user/filters/lib.php'); 

/**
 * Generic filter based on a list of values.
 */
class user_filter_select extends user_filter_type {
    /**
     * operator used for comparison of data
     */
    var $_operator;
    /**
     * options for the list values
     */
    var $_options;
    /**
     * Constructor
     * @param string $name the name of the filter instance
     * @param string $label the label of the filter instance
     * @param string $field the field used for filtering data
     * @param string $value the value used for filtering data
     * @param int $operator code of the comparison operator
     */
    function user_filter_select($name, $label, $field, $options, $value=null, $operator=null) {
        parent::user_filter_type($name, $label, $field, $value);
        $this->_operator = $operator;
        $this->_options = $options;
    }
    
    /**
     * Returns an array of comparison operators
     * @return array of comparison operators
     */
    function getOperators() {
        return array(
            get_string('isanyvalue','filters'),
            get_string('isequalto','filters'),
            get_string('isnotequalto','filters'),
        );
    }

    /**
     * Adds controls specific to this filter in the form.
     * @param object $mform a MoodleForm object to setup
     */
    function setupForm(&$mform) {
        $objs = array();
        $objs[] =& $mform->createElement('select', $this->_name . '_op', null, $this->getOperators());
        $objs[] =& $mform->createElement('select', $this->_name, null, $this->_options);
        $grp =& $mform->addElement('group', $this->_name . '_grp', $this->_label, $objs, '', false);
        $grp->setHelpButton(array('select','','filters'));
    }
    
    /**
     * Retrieves data from the form data
     * @param object $formdata data submited with the form
     */
    function checkData($formdata) {
        $field = $this->_name;
        $operator = $field . '_op';
        $this->_value = (string)@$formdata->$field;
        $this->_operator = (int)@$formdata->$operator;
    }

    /**
     * Returns the condition to be used with SQL where
     * @return string the filtering condition or null if the filter is disabled
     */
    function getSQLFilter() {
        switch($this->_operator) {
        default:
            return null;
        case 1: // equal to
            $res = '="' . $this->_value . '"';
            break;
        case 2: // not equal to
            $res = '<>"' . $this->_value . '"';
            break;
        }
        return $this->_field . $res;
    }
    
    /**
     * Returns a human friendly description of the filter.
     * @return string filter description
     */
    function getDescription() {
        $operators = $this->getOperators();
        switch($this->_operator) {
        case 1: // equal to
        case 2: // not equal to
            $res = $this->_label . ' ' . $operators[$this->_operator]. ' "' . $this->_options[stripslashes($this->_value)] . '"';
            break;
        }
        return $res;
    }
}

