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
 * Cohort enrolment plugin.
 *
 * @package   enrol_cohort
 * @copyright 2010 Petr Skoda {@link http://skodak.org}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * Cohort enrolment plugin implementation.
 * @author Petr Skoda
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class enrol_cohort_plugin extends enrol_plugin {
    /**
     * Returns localised name of enrol instance
     *
     * @param object $instance (null is accepted too)
     * @return string
     */
    public function get_instance_name($instance) {
        global $DB;

        if (empty($instance)) {
            $enrol = $this->get_name();
            return get_string('pluginname', 'enrol_'.$enrol);
        } else if (empty($instance->name)) {
            $enrol = $this->get_name();
            if ($role = $DB->get_record('role', array('id'=>$instance->roleid))) {
                $role = role_get_name($role, get_context_instance(CONTEXT_COURSE, $instance->courseid));
            } else {
                $role = get_string('error');
            }

            return get_string('pluginname', 'enrol_'.$enrol) . ' (' . format_string($DB->get_field('cohort', 'name', array('id'=>$instance->customint1))) . ' - ' . $role .')';
        } else {
            return format_string($instance->name);
        }
    }

    /**
     * Returns link to page which may be used to add new instance of enrolment plugin in course.
     * @param int $courseid
     * @return moodle_url page url
     */
    public function get_newinstance_link($courseid) {
        global $DB;

        $coursecontext = get_context_instance(CONTEXT_COURSE, $courseid);
        if (!has_capability('moodle/course:enrolconfig', $coursecontext)) {
            return NULL;
        }

        list($sqlparents, $params) = $DB->get_in_or_equal(get_parent_contexts($coursecontext));
        $sql = "SELECT id, contextid
                  FROM {cohort}
                 WHERE contextid $sqlparents
              ORDER BY name ASC";
        $cohorts = $DB->get_records_sql($sql, $params);
        $found = false;
        foreach ($cohorts as $c) {
            $context = get_context_instance_by_id($c->contextid);
            if (has_capability('moodle/cohort:view', $context)) {
                $found = true;
                break;
            }
        }
        if (!$found) {
            return NULL;
        }
        // multiple instances supported - multiple parent courses linked
        return new moodle_url('/enrol/cohort/addinstance.php', array('id'=>$courseid));
    }

    /**
     * Called for all enabled enrol plugins that returned true from is_cron_required().
     * @return void
     */
    public function cron() {
        global $CFG;

        // purge all roles if cohort sync disabled, those can be recreated later here in cron
        if (!enrol_is_enabled('cohort')) {
            role_unassign_all(array('component'=>'cohort_enrol'));
            return;
        }

        require_once("$CFG->dirroot/enrol/cohort/locallib.php");
        enrol_cohort_sync();
    }

    /**
     * Called after updating/inserting course.
     *
     * @param bool $inserted true if course just inserted
     * @param object $course
     * @param object $data form data
     * @return void
     */
    public function course_updated($inserted, $course, $data) {
        global $CFG;

        if (!$inserted) {
            // sync cohort enrols
            require_once("$CFG->dirroot/enrol/cohort/locallib.php");
            enrol_cohort_sync($course->id);
        } else {
            // cohorts are never inserted automatically
        }

    }
}


