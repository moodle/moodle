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
 * Condition main class.
 *
 * @package availability_company
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace availability_company;

use iomad;
use company;

defined('MOODLE_INTERNAL') || die();

/**
 * Condition main class.
 *
 * @package availability_company
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class condition extends \core_availability\condition {
    /** @var array Array from company id => name */
    protected static $companynames = array();

    /** @var int ID of company that this condition requires, or 0 = any company */
    protected $companyid;

    /**
     * Constructor.
     *
     * @param \stdClass $structure Data structure from JSON decode
     * @throws \coding_exception If invalid data structure.
     */
    public function __construct($structure) {
        // Get company id.
        if (!property_exists($structure, 'id')) {
            $this->companyid = 0;
        } else if (is_int($structure->id)) {
            $this->companyid = $structure->id;
        } else {
            throw new \coding_exception('Invalid ->id for company condition');
        }
    }

    public function save() {
        $result = (object)array('type' => 'company');
        if ($this->companyid) {
            $result->id = $this->companyid;
        }
        return $result;
    }

    public function is_available($not, \core_availability\info $info, $grabthelot, $userid) {
        global $DB;

        $course = $info->get_course();
        $context = \context_course::instance($course->id);
        $allow = true;
        if (!iomad::has_capability('block/iomad_company_admin:company_view_all', $context, $userid)) {
            // Get all companys the user belongs to.
            $companys = $DB->get_records_sql("SELECT DISTINCT companyid FROM {company_users} WHERE userid = :userid", ['userid' => $userid]);
            if ($this->companyid) {
                $allow = array_key_exists($this->companyid, $companys);
            } else {
                // No specific company. Allow if they belong to any company at all.
                $allow = $companys ? true : false;
            }

            // The NOT condition applies before accessallcompanys (i.e. if you
            // set something to be available to those NOT in company X,
            // people with accessallcompanys can still access it even if
            // they are in company X).
            if ($not) {
                $allow = !$allow;
            }
        }
        return $allow;
    }

    public function get_description($full, $not, \core_availability\info $info) {
        global $DB;

        if ($this->companyid) {
            // Need to get the name for the company. Unfortunately this requires
            // a database query. To save queries, get all companys for course at
            // once in a static cache.
            if (!array_key_exists($this->companyid, self::$companynames)) {
                $allcompanys = company::get_companies_select();
                foreach ($allcompanys as $id => $name) {
                    self::$companynames[$id] = $name;
                }
            }

            // If it still doesn't exist, it must have been misplaced.
            if (!array_key_exists($this->companyid, self::$companynames)) {
                $name = get_string('missing', 'availability_company');
            } else {
                // Not safe to call format_string here; use the special function to call it later.
                $name = self::description_format_string(self::$companynames[$this->companyid]);
            }
        } else {
            return get_string($not ? 'requires_notanycompany' : 'requires_anycompany',
                    'availability_company');
        }

        return get_string($not ? 'requires_notcompany' : 'requires_company',
                'availability_company', $name);
    }

    protected function get_debug_string() {
        return $this->companyid ? '#' . $this->companyid : 'any';
    }

    /**
     * Include this condition only if we are including companys in restore, or
     * if it's a generic 'same activity' one.
     *
     * @param int $restoreid The restore Id.
     * @param int $courseid The ID of the course.
     * @param base_logger $logger The logger being used.
     * @param string $name Name of item being restored.
     * @param base_task $task The task being performed.
     *
     * @return Integer companyid
     */
    public function include_after_restore($restoreid, $courseid, \base_logger $logger,
            $name, \base_task $task) {
        return !$this->companyid || $task->get_setting_value('companys');
    }

    public function update_after_restore($restoreid, $courseid, \base_logger $logger, $name) {
        global $DB;
        if (!$this->companyid) {
            return false;
        }
        $rec = \restore_dbops::get_backup_ids_record($restoreid, 'company', $this->companyid);
        if (!$rec || !$rec->newitemid) {
            // If we are on the same course (e.g. duplicate) then we can just
            // use the existing one.
            if ($DB->record_exists('companys',
                    array('id' => $this->companyid, 'courseid' => $courseid))) {
                return false;
            }
            // Otherwise it's a warning.
            $this->companyid = -1;
            $logger->process('Restored item (' . $name .
                    ') has availability condition on company that was not restored',
                    \backup::LOG_WARNING);
        } else {
            $this->companyid = (int)$rec->newitemid;
        }
        return true;
    }

    public function update_dependency_id($table, $oldid, $newid) {
        if ($table === 'companys' && (int)$this->companyid === (int)$oldid) {
            $this->companyid = $newid;
            return true;
        } else {
            return false;
        }
    }

    /**
     * Wipes the static cache used to store companying names.
     */
    public static function wipe_static_cache() {
        self::$companynames = array();
    }

    public function is_applied_to_user_lists() {
        // Group conditions are assumed to be 'permanent', so they affect the
        // display of user lists for activities.
        return true;
    }

    public function filter_user_list(array $users, $not, \core_availability\info $info,
            \core_availability\capability_checker $checker) {
        global $CFG, $DB;

        // If the array is empty already, just return it.
        if (!$users) {
            return $users;
        }

        $course = $info->get_course();

        // List users for this course who match the condition.
        if ($this->companyid) {
            $companyusers = $DB->get_records_sql("
                    SELECT DISTINCT cu.userid
                      FROM {company_users} cu
                      JOIN {user_enrolments} ue ON ue.userid = cu.userid
                      JOIN {enrol} e ON ue.enrolid = e.id
                     WHERE e.courseid = :courseid
                     AND cu.companyid = :companyid",
                    ['courseid' => $course->id,
                     'companyid' => $this->companyid]);
        } else {
            $companyusers = $DB->get_records_sql("
                    SELECT DISTINCT cu.userid
                      FROM {company_users} cu
                      JOIN {user_enrolments} ue ON ue.userid = cu.userid
                      JOIN {enrol} e ON ue.enrolid = e.id
                     WHERE e.courseid = :courseid",
                    ['courseid' => $course->id]);
        }

        // List users who have access all companys.
        $aagusers = $checker->get_users_by_capability('moodle/site:accessallcompanys');

        // Filter the user list.
        $result = array();
        foreach ($users as $id => $user) {
            // Always include users with access all companys.
            if (array_key_exists($id, $aagusers)) {
                $result[$id] = $user;
                continue;
            }
            // Other users are included or not based on company membership.
            $allow = array_key_exists($id, $companyusers);
            if ($not) {
                $allow = !$allow;
            }
            if ($allow) {
                $result[$id] = $user;
            }
        }
        return $result;
    }

    /**
     * Returns a JSON object which corresponds to a condition of this type.
     *
     * Intended for unit testing, as normally the JSON values are constructed
     * by JavaScript code.
     *
     * @param int $companyid Required company id (0 = any company)
     * @return stdClass Object representing condition
     */
    public static function get_json($companyid = 0) {
        $result = (object)array('type' => 'company');
        // Id is only included if set.
        if ($companyid) {
            $result->id = (int)$companyid;
        }
        return $result;
    }

    public function get_user_list_sql($not, \core_availability\info $info, $onlyactive) {
        global $DB;

        // Get enrolled users with access all companys. These always are allowed.
        list($aagsql, $aagparams) = get_enrolled_sql(
                $info->get_context(), 'moodle/site:accessallcompanys', 0, $onlyactive);

        // Get all enrolled users.
        list ($enrolsql, $enrolparams) =
                get_enrolled_sql($info->get_context(), '', 0, $onlyactive);

        // Condition for specified or any company.
        $matchparams = array();
        if ($this->companyid) {
            $matchsql = "SELECT 1
                           FROM {companys_members} gm
                          WHERE gm.userid = userids.id
                                AND gm.companyid = " .
                    self::unique_sql_parameter($matchparams, $this->companyid);
        } else {
            $matchsql = "SELECT 1
                           FROM {companys_members} gm
                           JOIN {companys} g ON g.id = gm.companyid
                          WHERE gm.userid = userids.id
                                AND g.courseid = " .
                    self::unique_sql_parameter($matchparams, $info->get_course()->id);
        }

        // Overall query combines all this.
        $condition = $not ? 'NOT' : '';
        $sql = "SELECT userids.id
                  FROM ($enrolsql) userids
                 WHERE (userids.id IN ($aagsql)) OR $condition EXISTS ($matchsql)";
        return array($sql, array_merge($enrolparams, $aagparams, $matchparams));
    }
}
