<?php //$Id$

require_once($CFG->dirroot . '/user/filters/lib.php'); 

/**
 * Generic filter based on a date.
 */
class user_filter_date extends user_filter_type {
    /**
     * the end Unix timestamp (0 if disabled) 
     */
    var $_value2;
    /**
     * the fields available for comparisson
     */
    var $_fields;
    /**
     * Constructor
     * @param string $name the name of the filter instance
     * @param string $label the label of the filter instance
     * @param string $field the field used for filtering data
     * @param int $start the start Unix timestamp (0 if disabled) 
     * @param int $end the end Unix timestamp (0 if disabled)
     */
    function user_filter_date($name, $label, $field, $fields=null, $start=0, $end=0) {
        parent::user_filter_type($name, $label, $field, $start);
        $this->_value2 = $end;
        $this->_fields = $fields;
    }
    
    /**
     * Adds controls specific to this filter in the form.
     * @param object $mform a MoodleForm object to setup
     */
    function setupForm(&$mform) {
        $objs = array();
        if(is_array($this->_fields)) {
            $objs[] =& $mform->createElement('select', $this->_name . '_fld', null, $this->_fields);
        }
        $objs[] =& $mform->createElement('checkbox', $this->_name . '_sck', null, get_string('isafter', 'filters'));
        $objs[] =& $mform->createElement('date_selector', $this->_name . '_sdt', null);
        $objs[] =& $mform->createElement('checkbox', $this->_name . '_eck', null, get_string('isbefore', 'filters'));
        $objs[] =& $mform->createElement('date_selector', $this->_name . '_edt', null);
        $grp =& $mform->addElement('group', $this->_name . '_grp', $this->_label, $objs, '', false);
        $grp->setHelpButton(array('date','','filters'));
        $mform->setDefault($this->_name . '_sck', !empty($this->_value));
        $mform->setDefault($this->_name . '_eck', !empty($this->_value2));
        $mform->setDefault($this->_name . '_sdt', $this->_value);
        $mform->setDefault($this->_name . '_edt', $this->_value2);
        if(is_array($this->_fields)) {
            $mform->setDefault($this->_name . '_fld', $this->_field);
        }
        $mform->disabledIf($this->_name . '_sdt', $this->_name . '_sck');
        $mform->disabledIf($this->_name . '_edt', $this->_name . '_eck');
    }
    
    /**
     * Retrieves data from the form data
     * @param object $formdata data submited with the form
     */
    function checkData($formdata) {
        $fld = $this->_name . '_fld';
        $sdt = $this->_name . '_sdt';
        $edt = $this->_name . '_edt';
        $sck = $this->_name . '_sck';
        $eck = $this->_name . '_eck';
        if(@$formdata->$fld) {
            $this->_field = @$formdata->$fld;
        }
        $this->_value = @$formdata->$sck ? (int)@$formdata->$sdt : 0;
        $this->_value2 = @$formdata->$eck ? (int)@$formdata->$edt : 0;
    }

    /**
     * Returns the condition to be used with SQL where
     * @return string the filtering condition or null if the filter is disabled
     */
    function getSQLFilter() {
        if(empty($this->_value) && empty($this->_value2)) {
            return null;
        }
        $res = $this->_field . '>0' ;
        if($this->_value) {
            $res .= ' AND ' . $this->_field . '>=' . $this->_value;
        }
        if($this->_value2) {
            $res .= ' AND ' . $this->_field . '<=' . $this->_value2;
        }
        return $res;
    }
    
    /**
     * Returns a human friendly description of the filter.
     * @return string filter description
     */
    function getDescription() {
        if(is_array($this->_fields)) {
            $res = $this->_fields[$this->_field] . ' ';
        } else {
            $res = $this->_label . ' ';
        }
        if($this->_value && $this->_value2) {
            $res .= get_string('isbetween', 'filters', array(userdate($this->_value), userdate($this->_value2)));
        } else if($this->_value) {
            $res .= get_string('isafter', 'filters', userdate($this->_value));
        } else {
            $res .= get_string('isbefore', 'filters', userdate($this->_value2));
        }
        return $res;
    }
}
?>