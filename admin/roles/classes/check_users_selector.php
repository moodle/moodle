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
 * User selector.
 *
 * @package    core_role
 * @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/user/selector/lib.php');

/**
 * User selector subclass for the selection of users in the check permissions page.
 *
 * @copyright 2012 Petr Skoda {@link http://skodak.org}
 */
class core_role_check_users_selector extends user_selector_base {
    /** @var bool limit listing of users to enrolled only */
    protected $onlyenrolled;

    /**
     * Constructor.
     *
     * @param string $name the control name/id for use in the HTML.
     * @param array $options other options needed to construct this selector.
     * You must be able to clone a userselector by doing new get_class($us)($us->get_name(), $us->get_options());
     */
    public function __construct($name, $options) {
        if (!isset($options['multiselect'])) {
            $options['multiselect'] = false;
        }
        parent::__construct($name, $options);

        $coursecontext = $this->accesscontext->get_course_context(false);
        if ($coursecontext and $coursecontext->id != SITEID and !has_capability('moodle/role:manage', $coursecontext)) {
            // Prevent normal teachers from looking up all users.
            $this->onlyenrolled = true;
        } else {
            $this->onlyenrolled = false;
        }
    }

    public function find_users($search) {
        global $DB;

        list($wherecondition, $params) = $this->search_sql($search, 'u');

        $fields      = 'SELECT ' . $this->required_fields_sql('u');
        $countfields = 'SELECT COUNT(1)';

        $coursecontext = $this->accesscontext->get_course_context(false);

        if ($coursecontext and $coursecontext != SITEID) {
            $sql1 = " FROM {user} u
                      JOIN {user_enrolments} ue ON (ue.userid = u.id)
                      JOIN {enrol} e ON (e.id = ue.enrolid AND e.courseid = :courseid1)
                     WHERE $wherecondition";
            $params['courseid1'] = $coursecontext->instanceid;

            if ($this->onlyenrolled) {
                $sql2 = null;
            } else {
                $sql2 = " FROM {user} u
                     LEFT JOIN ({user_enrolments} ue
                                JOIN {enrol} e ON (e.id = ue.enrolid AND e.courseid = :courseid2)) ON (ue.userid = u.id)
                         WHERE $wherecondition
                               AND ue.id IS NULL";
                $params['courseid2'] = $coursecontext->instanceid;
            }

        } else {
            if ($this->onlyenrolled) {
                // Bad luck, current user may not view only enrolled users.
                return array();
            }
            $sql1 = null;
            $sql2 = " FROM {user} u
                     WHERE $wherecondition";
        }

        $params['contextid'] = $this->accesscontext->id;

        list($sort, $sortparams) = users_order_by_sql('u', $search, $this->accesscontext);
        $order = ' ORDER BY ' . $sort;

        $result = array();

        if ($search) {
            $groupname1 = get_string('enrolledusersmatching', 'enrol', $search);
            $groupname2 = get_string('potusersmatching', 'core_role', $search);
        } else {
            $groupname1 = get_string('enrolledusers', 'enrol');
            $groupname2 = get_string('potusers', 'core_role');
        }

        if ($sql1) {
            $enrolleduserscount = $DB->count_records_sql($countfields . $sql1, $params);
            if (!$this->is_validating() and $enrolleduserscount > $this->maxusersperpage) {
                $result[$groupname1] = array();
                $toomany = $this->too_many_results($search, $enrolleduserscount);
                $result[implode(' - ', array_keys($toomany))] = array();

            } else {
                $enrolledusers = $DB->get_records_sql($fields . $sql1 . $order, array_merge($params, $sortparams));
                if ($enrolledusers) {
                    $result[$groupname1] = $enrolledusers;
                }
            }
            if ($sql2) {
                $result[''] = array();
            }
        }
        if ($sql2) {
            $otheruserscount = $DB->count_records_sql($countfields . $sql2, $params);
            if (!$this->is_validating() and $otheruserscount > $this->maxusersperpage) {
                $result[$groupname2] = array();
                $toomany = $this->too_many_results($search, $otheruserscount);
                $result[implode(' - ', array_keys($toomany))] = array();
            } else {
                $otherusers = $DB->get_records_sql($fields . $sql2 . $order, array_merge($params, $sortparams));
                if ($otherusers) {
                    $result[$groupname2] = $otherusers;
                }
            }
        }

        return $result;
    }

    protected function get_options() {
        global $CFG;
        $options = parent::get_options();
        $options['file'] = $CFG->admin . '/roles/lib.php';
        return $options;
    }
}
