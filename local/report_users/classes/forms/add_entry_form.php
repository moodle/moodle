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

namespace local_report_users\forms;

defined('MOODLE_INTERNAL') || die;

use \iomad;
use \company;
use \moodle_url;
use \moodleform;

class add_entry_form extends moodleform {

    public function definition() {
        global $company, $DB, $CFG;

        $mform =& $this->_form;

        $companycourses = $company->get_menu_courses(true);
        $mform->addElement('select', 'courseid', get_string('course'), $companycourses);
        $mform->addElement('date_selector', 'licenseallocated', get_string('licensedateallocated', 'block_iomad_company_admin'), array('optional' => true));
        $mform->addElement('text', 'licensename', get_string('licensename', 'block_iomad_company_admin'));
        $mform->addElement('date_selector', 'timeenrolled', get_string('datestarted', 'local_report_completion'));
        $mform->addElement('date_selector', 'timecompleted', get_string('datecompleted', 'local_report_completion'));
        $mform->addElement('text', 'finalscore', get_string('grade'));
        $mform->addRule('courseid', null, 'required');
        $mform->setType('finalscore', PARAM_FLOAT);
        $mform->setType('licensename', PARAM_CLEAN);
        $mform->addRule('finalscore', null, 'required');
        $mform->addRule('finalscore', get_string('invalidentry', 'error'), 'numeric');
        $mform->hideif('licensename', 'licenseallocated[enabled]', 'notchecked');

        $this->add_action_buttons(true);
    }

    public function validation($data, $files) {
        global $CFG, $DB;

        $errors = array();

        if ($data['timecompleted'] < $data['timeenrolled']) {
            $errors['timecompleted'] = get_string('timecompletedbeforetimeenrollederror', 'block_iomad_company_admin');
        }

        if (!empty($data['licenseallocated']) && empty($data['licensename'])) {
            $errors['licensename'] = get_string('required');
        }
        if (!empty($data['licenseallocated']) &&
            ($data['timecompleted'] < $data['licenseallocated'] ||
            $data['timeenrolled'] < $data['licenseallocated'])) {
            $errors['licenseallocated'] = get_string('licenseallocatedoutofordererror', 'block_iomad_company_admin');
        }

        if ($DB->get_record('iomad_courses', array('courseid' => $data['courseid'], 'licensed' => 1)) &&
            empty($data['licenseallocated'])) {
            $errors['licenseallocated'] = get_string('courseislicensedrequired', 'block_iomad_company_admin');
        }

        if ($data['finalscore'] < 0 ||
            $data['finalscore'] > 100 ) {
            $errors['finalscore'] = get_string('invalidgrade', 'block_iomad_company_admin');
        }

        return $errors;
    }
}