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
 * main grading page for the Panopto Student Submission module
 *
 * @package mod_panoptosubmission
 * @copyright  Panopto 2021
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(dirname(dirname(dirname(__FILE__))).'/course/moodleform_mod.php');
require_once(dirname(__FILE__).'/locallib.php');
require_once($CFG->dirroot . '/grade/grading/lib.php');
require_once($CFG->libdir.'/formslib.php');

/**
 * This class contains the settings used to determine how the submissions will be graded
 *
 * @package mod_panoptosubmission
 * @copyright  Panopto 2021
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class panoptosubmission_gradepreferences_form extends moodleform {
    /**
     * This function defines all of the elements displayed on the grade preferences form.
     */
    public function definition() {
        global $CFG, $COURSE, $USER;

        $mform =& $this->_form;

        $mform->addElement('hidden', 'cmid', $this->_customdata['cmid']);
        $mform->setType('cmid', PARAM_INT);

        $mform->addElement('header', 'panopto_submission_header', get_string('optionalsettings', 'panoptosubmission'));

        $context = context_module::instance($this->_customdata['cmid']);

        $groupopt = [];
        $groups = [];

        // If the user doesn't have access to all group print the groups they have access to.
        if (!has_capability('moodle/site:accessallgroups', $context)) {

            // Determine the groups mode.
            switch($this->_customdata['groupmode']) {
                case NOGROUPS:
                    // No groups, do nothing.
                    break;
                case SEPARATEGROUPS:
                    $groups = groups_get_all_groups($COURSE->id, $USER->id);
                    break;
                case VISIBLEGROUPS:
                    $groups = groups_get_all_groups($COURSE->id);
                    break;
            }

            $groupopt[0] = get_string('all', 'mod_panoptosubmission');

            foreach ($groups as $groupobj) {
                $groupopt[$groupobj->id] = $groupobj->name;
            }

        } else {
            $groups = groups_get_all_groups($COURSE->id);

            $groupopt[0] = get_string('all', 'mod_panoptosubmission');

            foreach ($groups as $groupobj) {
                $groupopt[$groupobj->id] = $groupobj->name;
            }
        }

        $gradingmanager = get_grading_manager($context, 'mod_panoptosubmission', 'submissions');
        $controller = $gradingmanager->get_active_controller();
        $showquickgrading = empty($controller);

        if ($showquickgrading) {
            $mform->addElement('checkbox', 'quickgrade', get_string('quickgrade', 'panoptosubmission'));
            $mform->setDefault('quickgrade', '');
            $mform->addHelpButton('quickgrade', 'quickgrade', 'panoptosubmission');
        }

        $mform->addElement('select', 'group_filter', get_string('group_filter', 'mod_panoptosubmission'), $groupopt);

        $filters = [
            PANOPTOSUBMISSION_ALL => get_string('all', 'panoptosubmission'),
            PANOPTOSUBMISSION_REQ_GRADING => get_string('reqgrading', 'panoptosubmission'),
            PANOPTOSUBMISSION_SUBMITTED => get_string('submitted', 'panoptosubmission'),
            PANOPTOSUBMISSION_NOT_SUBMITTED => get_string('not_submitted', 'panoptosubmission'),
        ];

        $mform->addElement('select', 'filter', get_string('show'), $filters);
        $mform->addHelpButton('filter', 'show', 'panoptosubmission');

        $mform->addElement('text', 'perpage', get_string('pagesize', 'panoptosubmission'), ['size' => 3, 'maxlength' => 3]);
        $mform->setType('perpage', PARAM_INT);
        $mform->addHelpButton('perpage', 'pagesize', 'panoptosubmission');

        $savepref = get_string('savepref', 'panoptosubmission');

        $mform->addElement('submit', 'savepref', $savepref);
    }

    /**
     * This funciton validates te submitted data.
     * @param array $data array of ("fieldname"=>value) of submitted data
     * @param array $files array of uploaded files "element_name"=>tmp_file_path
     * @return array of "element_name"=>"error_description" if there are errors,
     *         or an empty array if everything is OK (true allowed for backwards compatibility too).
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        if (0 == (int) $data['perpage']) {
            $errors['perpage'] = get_string('invalidperpage', 'panoptosubmission');
        }

        return $errors;
    }
}
