<?php //$Id$

require_once($CFG->dirroot .'/user/filters/lib.php');

/**
 * User filter based on roles in a course identified by its shortname.
 */
class user_filter_courserole extends user_filter_type {
    /**
     * Constructor
     * @param string $name the name of the filter instance
     * @param string $label the label of the filter instance
     * @param boolean $advanced advanced form element flag
     */
    function user_filter_courserole($name, $label, $advanced) {
        parent::user_filter_type($name, $label, $advanced);
    }

    /**
     * Returns an array of available roles
     * @return array of availble roles
     */
    function get_roles() {
        $context = get_context_instance(CONTEXT_SYSTEM);
        $roles = array(0=> get_string('anyrole','filters')) + get_assignable_roles($context);
        return $roles;
    }

    /**
     * Returns an array of course categories
     * @return array of course categories
     */
    function get_course_categories() {
        $displaylist = array();
        $parentlist = array();
        make_categories_list($displaylist, $parentlist);
        return array(0=> get_string('anycategory', 'filters')) + $displaylist;
    }

    /**
     * Adds controls specific to this filter in the form.
     * @param object $mform a MoodleForm object to setup
     */
    function setupForm(&$mform) {
        $objs = array();
        $objs[] =& $mform->createElement('select', $this->_name .'_rl', null, $this->get_roles());
        $objs[] =& $mform->createElement('select', $this->_name .'_ct', null, $this->get_course_categories());
        $objs[] =& $mform->createElement('text', $this->_name, null);
        $grp =& $mform->addElement('group', $this->_name.'_grp', $this->_label, $objs, '', false);
        $grp->setHelpButton(array('courserole', $this->_label, 'filters'));
        if ($this->_advanced) {
            $mform->setAdvanced($this->_name.'_grp');
        }
    }

    /**
     * Retrieves data from the form data
     * @param object $formdata data submited with the form
     * @return mixed array filter data or false when filter not set
     */
    function check_data($formdata) {
        $field    = $this->_name;
        $role     = $field .'_rl';
        $category = $field .'_ct';

        if (array_key_exists($field, $formdata)) {
            if (empty($formdata->$field) and empty($formdata->$role) and empty($formdata->$category)) {
                // nothing selected
                return false;
            }
            return array('value'      => (string)$formdata->$field,
                         'roleid'     => (int)$formdata->$role,
                         'categoryid' => (int)$formdata->$category);
        }
        return false;
    }

    /**
     * Returns the condition to be used with SQL where
     * @param array $data filter settings
     * @return string the filtering condition or null if the filter is disabled
     */
    function get_sql_filter($data) {
        global $CFG;
        $value      = addslashes($data['value']);
        $roleid     = $data['roleid'];
        $categoryid = $data['categoryid'];

        if (empty($value) and empty($roleid) and empty($categoryid)) {
            return '';
        }

        $timenow = round(time(), 100); // rounding - enable sql caching
        $where = "b.contextlevel=50 AND a.timestart<$timenow AND (a.timeend=0 OR a.timeend>$timenow)";
        if ($roleid) {
            $where .= " AND a.roleid=$roleid";
        }
        if ($categoryid) {
            $where .= " AND c.category=$categoryid";
        }
        if ($value) {
            $where .= " AND c.shortname ".sql_ilike()." '$value'";
        }
        return "id IN (SELECT userid
                         FROM {$CFG->prefix}role_assignments a
                   INNER JOIN {$CFG->prefix}context b ON a.contextid=b.id
                   INNER JOIN {$CFG->prefix}course c ON b.instanceid=c.id
                        WHERE $where)";
    }

    /**
     * Returns a human friendly description of the filter used as label.
     * @param array $data filter settings
     * @return string active filter label
     */
    function get_label($data) {
        $value      = $data['value'];
        $roleid     = $data['roleid'];
        $categoryid = $data['categoryid'];

        $a = new object();
        $a->label = $this->_label;

        if ($roleid) {
            $rolename = get_field('role', 'name', 'id', $roleid);
            $a->rolename = '"'.format_string($rolename).'"';
        } else {
            $a->rolename = get_string('anyrole', 'filters');
        }

        if ($categoryid) {
            $catname = get_field('course_categories', 'name', 'id', $categoryid);
            $a->categoryname = '"'.format_string($catname).'"';
        } else {
            $a->categoryname = get_string('anycategory', 'filters');
        }

        if ($value) {
            $a->coursename = '"'.s($value).'"';
            if (!record_exists('course', 'shortname', addslashes($value))) {
                return '<span class="notifyproblem">'.get_string('courserolelabelerror', 'filters', $a).'</span>';
            }
        } else {
            $a->coursename = get_string('anycourse', 'filters');
        }

        return get_string('courserolelabel', 'filters', $a);
    }
}
