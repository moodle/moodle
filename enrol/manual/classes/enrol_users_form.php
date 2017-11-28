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
 * Enrol users form.
 *
 * Simple form to search for users and add them using a manual enrolment to this course.
 *
 * @package enrol_manual
 * @copyright 2016 Damyon Wiese
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/formslib.php');

class enrol_manual_enrol_users_form extends moodleform {

    /**
     * Form definition.
     * @return void
     */
    public function definition() {
        global $PAGE, $DB, $CFG;


        require_once($CFG->dirroot . '/enrol/locallib.php');

        $context = $this->_customdata->context;

        // Get the course and enrolment instance.
        $coursecontext = $context->get_course_context();
        $course = $DB->get_record('course', ['id' => $coursecontext->instanceid]);
        $manager = new course_enrolment_manager($PAGE, $course);

        $instance = null;
        foreach ($manager->get_enrolment_instances() as $tempinstance) {
            if ($tempinstance->enrol == 'manual') {
                if ($instance === null) {
                    $instance = $tempinstance;
                    break;
                }
            }
        }

        $mform = $this->_form;
        $mform->setDisableShortforms();
        $mform->disable_form_change_checker();
        // Build the list of options for the enrolment period dropdown.
        $unlimitedperiod = get_string('unlimited');
        $periodmenu = array();
        $periodmenu[''] = $unlimitedperiod;
        for ($i=1; $i<=365; $i++) {
            $seconds = $i * 86400;
            $periodmenu[$seconds] = get_string('numdays', '', $i);
        }
        // Work out the apropriate default settings.
        $defaultperiod = $instance->enrolperiod;
        if ($instance->enrolperiod > 0 && !isset($periodmenu[$instance->enrolperiod])) {
            $periodmenu[$instance->enrolperiod] = format_time($instance->enrolperiod);
        }
        if (empty($extendbase)) {
            if (!$extendbase = get_config('enrol_manual', 'enrolstart')) {
                // Default to now if there is no system setting.
                $extendbase = 4;
            }
        }

        // Build the list of options for the starting from dropdown.
        $now = time();
        $today = make_timestamp(date('Y', $now), date('m', $now), date('d', $now), 0, 0, 0);
        $dateformat = get_string('strftimedatefullshort');

        // Enrolment start.
        $basemenu = array();
        if ($course->startdate > 0) {
            $basemenu[2] = get_string('coursestart') . ' (' . userdate($course->startdate, $dateformat) . ')';
        }
        $basemenu[3] = get_string('today') . ' (' . userdate($today, $dateformat) . ')';
        $basemenu[4] = get_string('now', 'enrol_manual') . ' (' . userdate($now, get_string('strftimedatetimeshort')) . ')';

        $mform->addElement('header', 'main', get_string('enrolmentoptions', 'enrol'));
        $options = array(
            'ajax' => 'enrol_manual/form-potential-user-selector',
            'multiple' => true,
            'courseid' => $course->id,
            'enrolid' => $instance->id
        );
        $mform->addElement('autocomplete', 'userlist', get_string('selectusers', 'enrol_manual'), array(), $options);

        // Confirm the user can search for cohorts before displaying select.
        if (has_capability('moodle/cohort:manage', $context) || has_capability('moodle/cohort:view', $context)) {
            // Check to ensure there is at least one visible cohort before displaying the select box.
            // Ideally it would be better to call external_api::call_external_function('core_cohort_search_cohorts')
            // (which is used to populate the select box) instead of duplicating logic but there is an issue with globals
            // being borked (in this case $PAGE) when combining the usage of fragments and call_external_function().
            require_once($CFG->dirroot . '/cohort/lib.php');
            $availablecohorts = cohort_get_cohorts($context->id, 0, 1, '');
            $availablecohorts = $availablecohorts['cohorts'];
            if (!($context instanceof context_system)) {
                $availablecohorts = array_merge($availablecohorts,
                    cohort_get_available_cohorts($context, COHORT_ALL, 0, 1, ''));
            }
            if (!empty($availablecohorts)) {
                $options = ['contextid' => $context->id, 'multiple' => true];
                $mform->addElement('cohort', 'cohortlist', get_string('selectcohorts', 'enrol_manual'), $options);
            }
        }

        $roles = get_assignable_roles($context);
        $mform->addElement('select', 'roletoassign', get_string('assignrole', 'enrol_manual'), $roles);
        $keys = array_keys($roles);
        $defaultrole = end($keys);
        $mform->setDefault('roletoassign', $defaultrole);

        $mform->addAdvancedStatusElement('main');

        $mform->addElement('checkbox', 'recovergrades', get_string('recovergrades', 'enrol'));
        $mform->setAdvanced('recovergrades');
        $mform->addElement('select', 'duration', get_string('defaultperiod', 'enrol_manual'), $periodmenu);
        $mform->setDefault('duration', $defaultperiod);
        $mform->setAdvanced('duration');
        $mform->addElement('select', 'startdate', get_string('startingfrom'), $basemenu);
        $mform->setDefault('startdate', $extendbase);
        $mform->setAdvanced('startdate');

        $mform->addElement('hidden', 'id', $course->id);
        $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'action', 'enrol');
        $mform->setType('action', PARAM_ALPHA);
        $mform->addElement('hidden', 'enrolid', $instance->id);
        $mform->setType('enrolid', PARAM_INT);
    }
}
