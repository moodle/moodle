<?php //$Id$

require_once($CFG->dirroot . '/user/filters/lib.php'); 

/**
 * User filter based on roles in a course identified by its shortname.
 */
class user_filter_courserole extends user_filter_type {
    /**
     * User role (0 = any role)
     */
    var $_roleid;
    /**
     * Course category in which to search the course (0 = all categories).
     */
    var $_categoryid;
    /**
     * Constructor
     * @param string $name the name of the filter instance
     * @param string $label the label of the filter instance
     * @param string $field the field used for filtering data
     * @param string $value the shortname of the course (used for filtering data)
     * @param int $categoryid id of the category
     * @param int $roleid id of the role
     */
    function user_filter_courserole($name, $label, $field='id', $value=null, $categoryid=0, $roleid=0) {
        parent::user_filter_type($name, $label, $field, $value);
        $this->_roleid = $roleid;
        $this->_categoryid = $categoryid;
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
     * Returns an array of course categories
     * @return array of course categories
     */
    function getCourseCategories() {
        $displaylist = array();
        $parentlist = array();
        make_categories_list($displaylist, $parentlist);
        return array_merge(array(0=> get_string('anycategory', 'filters')), $displaylist);
    }

    /**
     * Adds controls specific to this filter in the form.
     * @param object $mform a MoodleForm object to setup
     */
    function setupForm(&$mform) {
        $objs = array();
        $objs[] =& $mform->createElement('select', $this->_name . '_rl', null, $this->getRoles());
        $objs[] =& $mform->createElement('select', $this->_name . '_ct', null, $this->getCourseCategories());
        $objs[] =& $mform->createElement('text', $this->_name, null);
        $grp =& $mform->addElement('group', $this->_name . '_grp', $this->_label, $objs, '', false);
        $grp->setHelpButton(array('courserole','','filters'));
        $mform->setDefault($this->_name, $this->_value);
        $mform->setDefault($this->_name . '_rl', $this->_roleid);
        $mform->setDefault($this->_name . '_ct', $this->_categoryid);
    }
    
    /**
     * Retrieves data from the form data
     * @param object $formdata data submited with the form
     */
    function checkData($formdata) {
        $field = $this->_name;
        $role = $field . '_rl';
        $category = $field . '_ct';
        $this->_value = (string)@$formdata->$field;
        $this->_roleid = (int)@$formdata->$role;
        $this->_categoryid = (int)@$formdata->$category;
    }

    /**
     * Returns the condition to be used with SQL where
     * @return string the filtering condition or null if the filter is disabled
     */
    function getSQLFilter() {
        global $CFG;
        if(empty($this->_value) && empty($this->_roleid) && empty($this->_categoryid)) {
            return null;
        }
        $timenow = time();
        $where = 'WHERE b.contextlevel=50 AND timestart<' . $timenow .' AND (timeend=0 OR timeend>'. $timenow . ')';
        if($this->_roleid) {
            $where.= ' AND roleid='. $this->_roleid;
        }
        if($this->_categoryid) {
            $where .= ' AND category=' . $this->_categoryid;
        }
        if($this->_value) {
            $where .= ' AND shortname="' . $this->_value . '"';
        }
        return $this->_field . " IN (SELECT userid FROM {$CFG->prefix}role_assignments a ".
            "INNER JOIN {$CFG->prefix}context b ON a.contextid=b.id ".
            "INNER JOIN {$CFG->prefix}course c ON b.instanceid=c.id ".
            $where . ')';
    }
    
    /**
     * Returns a human friendly description of the filter.
     * @return string filter description
     */
    function getDescription() {
        if ($this->_roleid) {
            $roles =& $this->getRoles();
            $rolename = '"' . $roles[$this->_roleid]. '"';
        } else {
            $rolename = get_string('anyrole','filters');
        }
        if ($this->_categoryid) {
            $categories=& $this->getCourseCategories();
            $categoryname = '"' . $categories[$this->_categoryid]. '"';
        } else {
            $categoryname = get_string('anycategory', 'filters');
        }
        if ($this->_value) {
            $coursename = '"' . stripslashes($this->_value). '"';
        } else {
            $coursename = get_string('anycourse','filters');
        }
        return $this->_label . ' is ' . $rolename. ' in ' . $coursename . ' from ' . $categoryname;
    }
}
