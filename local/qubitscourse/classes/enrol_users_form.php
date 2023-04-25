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

class local_qubitscourse_enrol_users_form extends moodleform {

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
        $periodmenu = enrol_get_period_list();
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

        $mform->addElement('header','qubitsmain', 'Qubits Course Enrol user');
        $mform->addElement('header', 'main', get_string('enrolmentoptions', 'enrol'));
        
        $roles = get_assignable_roles($context, ROLENAME_BOTH);
        $roles = array_filter($roles,  array($this, 'filter_roles'), ARRAY_FILTER_USE_BOTH);
        $mform->addElement('select', 'roletoassign', get_string('assignrole', 'enrol_manual'), $roles);
        $mform->setDefault('roletoassign', $instance->roleid);

        $mform->addElement('select', 'coursegrade', get_string('grade'), $CFG->coursegrades);

        $mform->addElement('select', 'coursesections', get_string('sections'), $CFG->coursesections);

        $options = array(
            'ajax' => 'local_qubitsuser/form-potential-user-selector',
            'multiple' => true,
            'courseid' => $course->id,
            'siteid' => $this->_customdata->siteId,
            'enrolid' => $instance->id,
            'perpage' => $CFG->maxusersperpage,
            'userfields' => implode(',', \core_user\fields::get_identity_fields($context, true))
        );
        $mform->addElement('autocomplete', 'userlist', get_string('selectusers', 'enrol_manual'), array(), $options);


        $mform->addElement('hidden', 'id', $course->id);
        $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'action', 'enrol');
        $mform->setType('action', PARAM_ALPHA);
        $mform->addElement('hidden', 'enrolid', $instance->id);
        $mform->setType('enrolid', PARAM_INT);
    }

    private function filter_roles($v, $k){
		return strtolower($v) == 'teacher' || strtolower($v) == 'student'; 
	}

    /**
     * Validate the submitted form data.
     *
     * @param array $data array of ("fieldname"=>value) of submitted data
     * @param array $files array of uploaded files "element_name"=>tmp_file_path
     * @return array of "element_name"=>"error_description" if there are errors,
     *         or an empty array if everything is OK (true allowed for backwards compatibility too).
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);
        if (!empty($data['startdate']) && !empty($data['timeend'])) {
            if ($data['startdate'] >= $data['timeend']) {
                $errors['timeend'] = get_string('enroltimeendinvalid', 'enrol');
            }
        }
        return $errors;
    }
}
