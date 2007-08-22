<?php //$Id$

require_once($CFG->dirroot . '/user/filters/lib.php'); 

/**
 * Generic filter with radio buttons for integer fields.
 */
class user_filter_radios extends user_filter_type {
    /**
     * options for the radio buttons
     */
    var $_options;
    /**
     * id of the option which disables the filter
     */
    var $_offoption;
    /**
     * Constructor
     * @param string $name the name of the filter instance
     * @param string $label the label of the filter instance
     * @param string $field the field used for filtering data
     * @param array $options associative array used to generate the radio buttons
     * @param boolean $offoption true if a "don't care" option should be generated
     * @param int $value the value used for filtering data
     */
    function user_filter_radios($name, $label, $field, $options, $offoption=true, $value=null) {
        parent::user_filter_type($name, $label, $field, $value);
        $this->_options = $options;
        if($offoption) {
            $this->_offoption = @min(array_keys($options)) - 1; 
        } else {
            $this->_offoption = null;
        }
    }
    
    /**
     * Adds controls specific to this filter in the form.
     * @param object $mform a MoodleForm object to setup
     */
    function setupForm(&$mform) {
        $objs = array();
        if(!is_null($this->_offoption)) {
            $objs[] =& $mform->createElement('radio', $this->_name, null, get_string('anyvalue', 'filters'), $this->_offoption);
        }
        foreach($this->_options as $k=>$v) {
            $objs[] =& $mform->createElement('radio', $this->_name, null, $v, $k);
        }
        $grp =& $mform->addElement('group', $this->_name . '_grp', $this->_label, $objs, '', false);
        $mform->setDefault($this->_name, $this->_value);
        $grp->setHelpButton(array('radios','','filters'));
    }
    
    /**
     * Retrieves data from the form data
     * @param object $formdata data submited with the form
     */
    function checkData($formdata) {
        $field = $this->_name;
        $this->_value = (int)@$formdata->$field;
    }

    /**
     * Returns the condition to be used with SQL where
     * @return string the filtering condition or null if the filter is disabled
     */
    function getSQLFilter() {
        if($this->_value === $this->_offoption) {
            return null;
        }
        return $this->_field . '=' . $this->_value;
    }
    
    /**
     * Returns a human friendly description of the filter.
     * @return string filter description
     */
    function getDescription() {
        return $this->_label . ' is ' . $this->_options[$this->_value];
    }
}
