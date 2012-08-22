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
 * @package    enrol
 * @subpackage cohort
 * @copyright  2010 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

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
                return get_string('pluginname', 'enrol_'.$enrol) . ' (' . format_string($DB->get_field('cohort', 'name', array('id'=>$instance->customint1))) . ' - ' . $role .')';
            } else {
                return get_string('pluginname', 'enrol_'.$enrol) . ' (' . format_string($DB->get_field('cohort', 'name', array('id'=>$instance->customint1))) . ')';
            }

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
        if (!$this->can_add_new_instances($courseid)) {
            return NULL;
        }
        // multiple instances supported - multiple parent courses linked
        return new moodle_url('/enrol/cohort/addinstance.php', array('id'=>$courseid));
    }

    /**
     * Given a courseid this function returns true if the user is able to enrol or configure cohorts
     * AND there are cohorts that the user can view.
     *
     * @param int $courseid
     * @return bool
     */
    protected function can_add_new_instances($courseid) {
        global $DB;

        $coursecontext = get_context_instance(CONTEXT_COURSE, $courseid);
        if (!has_capability('moodle/course:enrolconfig', $coursecontext) or !has_capability('enrol/cohort:config', $coursecontext)) {
            return false;
        }
        list($sqlparents, $params) = $DB->get_in_or_equal(get_parent_contexts($coursecontext));
        $sql = "SELECT id, contextid
                  FROM {cohort}
                 WHERE contextid $sqlparents
              ORDER BY name ASC";
        $cohorts = $DB->get_records_sql($sql, $params);
        foreach ($cohorts as $c) {
            $context = get_context_instance_by_id($c->contextid);
            if (has_capability('moodle/cohort:view', $context)) {
                return true;
            }
        }
        return false;
    }


    /**
     * Called for all enabled enrol plugins that returned true from is_cron_required().
     * @return void
     */
    public function cron() {
        global $CFG;

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
        // It turns out there is no need for cohorts to deal with this hook, see MDL-34870.
    }

    /**
     * Update instance status
     *
     * @param stdClass $instance
     * @param int $newstatus ENROL_INSTANCE_ENABLED, ENROL_INSTANCE_DISABLED
     * @return void
     */
    public function update_status($instance, $newstatus) {
        global $CFG;

        parent::update_status($instance, $newstatus);

        require_once("$CFG->dirroot/enrol/cohort/locallib.php");
        enrol_cohort_sync($instance->courseid);
    }

    /**
     * Does this plugin allow manual unenrolment of a specific user?
     * Yes, but only if user suspended...
     *
     * @param stdClass $instance course enrol instance
     * @param stdClass $ue record from user_enrolments table
     *
     * @return bool - true means user with 'enrol/xxx:unenrol' may unenrol this user, false means nobody may touch this user enrolment
     */
    public function allow_unenrol_user(stdClass $instance, stdClass $ue) {
        if ($ue->status == ENROL_USER_SUSPENDED) {
            return true;
        }

        return false;
    }

    /**
     * Gets an array of the user enrolment actions
     *
     * @param course_enrolment_manager $manager
     * @param stdClass $ue A user enrolment object
     * @return array An array of user_enrolment_actions
     */
    public function get_user_enrolment_actions(course_enrolment_manager $manager, $ue) {
        $actions = array();
        $context = $manager->get_context();
        $instance = $ue->enrolmentinstance;
        $params = $manager->get_moodlepage()->url->params();
        $params['ue'] = $ue->id;
        if ($this->allow_unenrol_user($instance, $ue) && has_capability('enrol/cohort:unenrol', $context)) {
            $url = new moodle_url('/enrol/unenroluser.php', $params);
            $actions[] = new user_enrolment_action(new pix_icon('t/delete', ''), get_string('unenrol', 'enrol'), $url, array('class'=>'unenrollink', 'rel'=>$ue->id));
        }
        return $actions;
    }

    /**
     * Returns a button to enrol a cohort or its users through the manual enrolment plugin.
     *
     * This function also adds a quickenrolment JS ui to the page so that users can be enrolled
     * via AJAX.
     *
     * @param course_enrolment_manager $manager
     * @return enrol_user_button
     */
    public function get_manual_enrol_button(course_enrolment_manager $manager) {
        $course = $manager->get_course();
        if (!$this->can_add_new_instances($course->id)) {
            return false;
        }

        $cohorturl = new moodle_url('/enrol/cohort/addinstance.php', array('id' => $course->id));
        $button = new enrol_user_button($cohorturl, get_string('enrolcohort', 'enrol'), 'get');
        $button->class .= ' enrol_cohort_plugin';

        $button->strings_for_js(array(
            'enrol',
            'synced',
            'enrolcohort',
            'enrolcohortusers',
            ), 'enrol');
        $button->strings_for_js(array(
            'ajaxmore',
            'cohortsearch',
            ), 'enrol_cohort');
        $button->strings_for_js('assignroles', 'role');
        $button->strings_for_js('cohort', 'cohort');
        $button->strings_for_js('users', 'moodle');

        // No point showing this at all if the user cant manually enrol users
        $hasmanualinstance = has_capability('enrol/manual:enrol', $manager->get_context()) && $manager->has_instance('manual');

        $modules = array('moodle-enrol_cohort-quickenrolment', 'moodle-enrol_cohort-quickenrolment-skin');
        $function = 'M.enrol_cohort.quickenrolment.init';
        $arguments = array(
            'courseid'        => $course->id,
            'ajaxurl'         => '/enrol/cohort/ajax.php',
            'url'             => $manager->get_moodlepage()->url->out(false),
            'manualEnrolment' => $hasmanualinstance);
        $button->require_yui_module($modules, $function, array($arguments));

        return $button;
    }
}


