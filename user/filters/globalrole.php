<?php //$Id$

require_once($CFG->dirroot . '/user/filters/lib.php'); 

/**
 * User filter based on global roles.
 */
class user_filter_globalrole extends user_filter_type {
    /**
     * Constructor
     * @param string $name the name of the filter instance
     * @param string $label the label of the filter instance
     * @param string $field the field used for filtering data
     * @param int $value id of the role (used for filtering data); 0 = any role
     */
    function user_filter_globalrole($name, $label, $field='id', $value=0) {
        parent::user_filter_type($name, $label, $field, $value);
    }
    
    /**
     * Returns an array of available roles
     * @return array of availble roles
     */
    function getRoles() {
        $context =& get_context_instance(CONTEXT_SYSTEM);
        $roles =& array_merge(array(0=> get_string('anyrole','filters')), get_assignable_roles($context));
        return $roles;
    }

    /**
     * Adds controls specific to this filter in the form.
     * @param object $mform a MoodleForm object to setup
     */
    function setupForm(&$mform) {
        $obj =& $mform->addElement('select', $this->_name, $this->_label, $this->getRoles());
        $obj->setHelpButton(array('globalrole','','filters'));
        $mform->setDefault($this->_name, $this->_value);
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
     * Returns the condition to be used with SQL where
     * @return string the filtering condition or null if the filter is disabled
     */
    function getSQLFilter() {
        global $CFG;
        if(empty($this->_value)) {
            return null;
        }
        $timenow = time();
        $where = 'WHERE contextlevel=10 AND roleid='. $this->_value . ' AND timestart<' . $timenow .' AND (timeend=0 OR timeend>'. $timenow . ')';
        return $this->_field . " IN (SELECT userid FROM {$CFG->prefix}role_assignments a ".
            "INNER JOIN {$CFG->prefix}context b ON a.contextid=b.id ".
            $where . ')';
    }
    
    /**
     * Returns a human friendly description of the filter.
     * @return string filter description
     */
    function getDescription() {
        $roles =& $this->getRoles();
        return $this->_label . ' is ' . $roles[$this->_value];
    }
}
