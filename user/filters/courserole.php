<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Course role filter
 *
 * @package   core_user
 * @category  user
 * @copyright 1999 Martin Dougiamas  http://dougiamas.com
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->dirroot .'/user/filters/lib.php');

/**
 * User filter based on roles in a course identified by its shortname.
 * @copyright 1999 Martin Dougiamas  http://dougiamas.com
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_filter_courserole extends user_filter_type {
    /**
     * Constructor
     * @param string $name the name of the filter instance
     * @param string $label the label of the filter instance
     * @param boolean $advanced advanced form element flag
     */
    public function __construct($name, $label, $advanced) {
        parent::__construct($name, $label, $advanced);
    }

    /**
     * Old syntax of class constructor. Deprecated in PHP7.
     *
     * @deprecated since Moodle 3.1
     */
    public function user_filter_courserole($name, $label, $advanced) {
        debugging('Use of class name as constructor is deprecated', DEBUG_DEVELOPER);
        self::__construct($name, $label, $advanced);
    }

    /**
     * Returns an array of available roles
     * @return array of availble roles
     */
    public function get_roles() {
        $context = context_system::instance();
        $roles = array(0 => get_string('anyrole', 'filters')) + get_default_enrol_roles($context);
        return $roles;
    }

    /**
     * Returns an array of course categories
     * @return array of course categories
     */
    public function get_course_categories() {
        return array(0 => get_string('anycategory', 'filters')) + core_course_category::make_categories_list();
    }

    /**
     * Adds controls specific to this filter in the form.
     * @param moodleform $mform a MoodleForm object to setup
     */
    public function setupForm(&$mform) {
        $objs = array();
        $objs['role'] = $mform->createElement('select', $this->_name .'_rl', null, $this->get_roles());
        $objs['role']->setLabel(get_string('courserole', 'filters'));
        $objs['category'] = $mform->createElement('select', $this->_name .'_ct', null, $this->get_course_categories());
        $objs['category']->setLabel(get_string('coursecategory', 'filters'));
        $objs['value'] = $mform->createElement('text', $this->_name, null);
        $objs['value']->setLabel(get_string('coursevalue', 'filters'));
        $grp =& $mform->addElement('group', $this->_name.'_grp', $this->_label, $objs, '', false);
        $mform->setType($this->_name, PARAM_TEXT);
        if ($this->_advanced) {
            $mform->setAdvanced($this->_name.'_grp');
        }
    }

    /**
     * Retrieves data from the form data
     * @param stdClass $formdata data submited with the form
     * @return mixed array filter data or false when filter not set
     */
    public function check_data($formdata) {
        $field    = $this->_name;
        $role     = $field .'_rl';
        $category = $field .'_ct';

        if (property_exists($formdata, $field)) {
            if (empty($formdata->$field) and empty($formdata->$role) and empty($formdata->$category)) {
                // Nothing selected.
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
     * @return array sql string and $params
     */
    public function get_sql_filter($data) {
        global $CFG, $DB;
        static $counter = 0;
        $pref = 'ex_courserole'.($counter++).'_';

        $value      = $data['value'];
        $roleid     = $data['roleid'];
        $categoryid = $data['categoryid'];

        $params = array();

        if (empty($value) and empty($roleid) and empty($categoryid)) {
            return array('', $params);
        }

        $where = "b.contextlevel=50";
        if ($roleid) {
            $where .= " AND a.roleid = :{$pref}roleid";
            $params[$pref.'roleid'] = $roleid;
        }
        if ($categoryid) {
            $where .= " AND c.category = :{$pref}categoryid";
            $params[$pref.'categoryid'] = $categoryid;
        }
        if ($value) {
            $where .= " AND c.shortname = :{$pref}course";
            $params[$pref.'course'] = $value;
        }
        return array("id IN (SELECT userid
                               FROM {role_assignments} a
                         INNER JOIN {context} b ON a.contextid=b.id
                         INNER JOIN {course} c ON b.instanceid=c.id
                              WHERE $where)", $params);
    }

    /**
     * Returns a human friendly description of the filter used as label.
     * @param array $data filter settings
     * @return string active filter label
     */
    public function get_label($data) {
        global $DB;

        $value      = $data['value'];
        $roleid     = $data['roleid'];
        $categoryid = $data['categoryid'];

        $a = new stdClass();
        $a->label = $this->_label;

        if ($roleid) {
            $role = $DB->get_record('role', array('id' => $roleid));
            $a->rolename = '"'.role_get_name($role).'"';
        } else {
            $a->rolename = get_string('anyrole', 'filters');
        }

        if ($categoryid) {
            $catname = $DB->get_field('course_categories', 'name', array('id' => $categoryid));
            $a->categoryname = '"'.format_string($catname).'"';
        } else {
            $a->categoryname = get_string('anycategory', 'filters');
        }

        if ($value) {
            $a->coursename = '"'.s($value).'"';
            if (!$DB->record_exists('course', array('shortname' => $value))) {
                return '<span class="notifyproblem">'.get_string('courserolelabelerror', 'filters', $a).'</span>';
            }
        } else {
            $a->coursename = get_string('anycourse', 'filters');
        }

        return get_string('courserolelabel', 'filters', $a);
    }
}
