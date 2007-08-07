<?php //$Id$

require_once($CFG->dirroot . '/user/filters/lib.php'); 

/**
 * Generic filter for text fields.
 */
class user_filter_text extends user_filter_type {
    /**
     * operator used for comparison of data
     */
    var $_operator;
    /**
     * Constructor
     * @param string $name the name of the filter instance
     * @param string $label the label of the filter instance
     * @param string $field the field used for filtering data
     * @param string $value the value used for filtering data
     * @param int $operator code of the comparison operator
     */
    function user_filter_text($name, $label, $field, $value=null, $operator=0) {
        parent::user_filter_type($name, $label, $field, $value);
        $this->_operator = $operator;
    }
    
    /**
     * Returns an array of comparison operators
     * @return array of comparison operators
     */
    function getOperators() {
        return array(
            get_string('contains', 'filters'),
            get_string('doesnotcontain','filters'),
            get_string('isequalto','filters'),
            get_string('startswith','filters'),
            get_string('endswith','filters'),
            get_string('isempty','filters'),
        );
    }

    /**
     * Adds controls specific to this filter in the form.
     * @param object $mform a MoodleForm object to setup
     */
    function setupForm(&$mform) {
        $objs = array();
        $objs[] =& $mform->createElement('select', $this->_name . '_op', null, $this->getOperators());
        $objs[] =& $mform->createElement('text', $this->_name, null);
        $grp =& $mform->addElement('group', $this->_name . '_grp', $this->_label, $objs, '', false);
        $grp->setHelpButton(array('text','','filters'));
        $mform->setDefault($this->_name, $this->_value);
        $mform->setDefault($this->_name . '_op', $this->_operator);
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
        case 0: // contains
            if(empty($this->_value)) {
                return null;
            }
            $res = ' ' . sql_ilike(). ' "%' . $this->_value . '%"';
            break;
        case 1: // does not contain
            if(empty($this->_value)) {
                return null;
            }
            $res = ' NOT ' . sql_ilike(). ' "%' . $this->_value . '%"';
            break;
        case 2: // equal to
            if(empty($this->_value)) {
                return null;
            }
            $res = '="' . $this->_value . '"';
            break;
        case 3: // starts with
            if(empty($this->_value)) {
                return null;
            }
            $res = ' ' . sql_ilike(). ' "' . $this->_value . '%"';
            break;
        case 4: // ends with 
            if(empty($this->_value)) {
                return null;
            }
            $res = ' ' . sql_ilike(). ' "%' . $this->_value . '"';
            break;
        case 5: // empty
            if(empty($this->_value)) {
                return null;
            }
            $res = '=""';
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
        case 0: // contains
        case 1: // doesn't contain
        case 2: // equal to
        case 3: // starts with
        case 4: // ends with 
            $res = $operators[$this->_operator]. ' "' . stripslashes($this->_value) . '"';
            break;
        case 5: // empty
            $res = $operators[$this->_operator];
            break;
        }
        return $this->_label . ' ' . $res;
    }
}
