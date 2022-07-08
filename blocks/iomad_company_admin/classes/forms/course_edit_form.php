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
 * @package   block_iomad_company_admin
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_iomad_company_admin\forms;

defined('MOODLE_INTERNAL') || die;

use \iomad;
use \company;
use \moodle_url;
use \moodleform;
use \context_system;
use \context_coursecat;
use \DateTime;
use \core_course;

class course_edit_form extends moodleform {
    protected $title = '';
    protected $description = '';
    protected $selectedcompany = 0;
    protected $context = null;

    public function __construct($actionurl, $companyid, $editoroptions) {
        global $CFG, $DB;

        $this->selectedcompany = $companyid;
        $this->context = context_coursecat::instance($CFG->defaultrequestcategory);
        $this->editoroptions = $editoroptions;
        $this->companyrec = $DB->get_record('company', array('id' => $companyid));

        parent::__construct($actionurl);
    }

    public function definition() {
        global $CFG;

        $mform =& $this->_form;

        $mform->addElement('text', 'fullname', get_string('fullnamecourse'),
                            'maxlength="254" size="50"');
        $mform->addHelpButton('fullname', 'fullnamecourse');
        $mform->addRule('fullname', get_string('missingfullname'), 'required', null, 'client');
        $mform->setType('fullname', PARAM_MULTILANG);

        $mform->addElement('text', 'shortname', get_string('shortnamecourse'),
                            'maxlength="100" size="20"');
        $mform->addHelpButton('shortname', 'shortnamecourse');
        $mform->addRule('shortname', get_string('missingshortname'), 'required', null, 'client');
        $mform->setType('shortname', PARAM_MULTILANG);

        $selectarray = array();
        $plugins = enrol_get_plugins(true);
        if (!empty($plugins['self'])) {
            $selectarray[0] = get_string('selfenrolled', 'block_iomad_company_admin');
        }
        $selectarray[1] = get_string('enrolled', 'block_iomad_company_admin');

        // Create course as self enrolable.
        if (iomad::has_capability('block/iomad_company_admin:edit_licenses', context_system::instance())) {
            $selectarray[2] = get_string('licensedcourse', 'block_iomad_company_admin');
        }
        $select = &$mform->addElement('select', 'selfenrol',
                            get_string('enrolcoursetype', 'block_iomad_company_admin'),
                            $selectarray);
        $mform->addHelpButton('selfenrol', 'enrolcourse', 'block_iomad_company_admin');
        $select->setSelected('no');

        $mform->addElement('editor', 'summary_editor',
                            get_string('coursesummary'), null, $this->editoroptions);
        $mform->addHelpButton('summary_editor', 'coursesummary');
        $mform->setType('summary_editor', PARAM_RAW);

        $mform->addElement('date_time_selector', 'startdate', get_string('startdate'));
        $mform->addHelpButton('startdate', 'startdate');
        $date = (new DateTime())->setTimestamp(usergetmidnight(time()));
        $date->modify('+1 day');
        $mform->setDefault('startdate', $date->getTimestamp());

        $mform->addElement('date_time_selector', 'enddate', get_string('enddate'), array('optional' => true));
        $mform->addHelpButton('enddate', 'enddate');

        // Add custom fields to the form.
        $handler = core_course\customfield\course_handler::create();
        $handler->set_parent_context(context_coursecat::instance($this->companyrec->category)); // For course handler only.
        $handler->instance_form_definition($mform, 0);

        // Add action buttons.
        $buttonarray = array();
        $buttonarray[] = &$mform->createElement('submit', 'submitbutton',
                            get_string('createcourse', 'block_iomad_company_admin'));
        $buttonarray[] = &$mform->createElement('submit', 'submitandviewbutton',
                            get_string('createandvisitcourse', 'block_iomad_company_admin'));
        $buttonarray[] = &$mform->createElement('cancel');
        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
        $mform->closeHeaderBefore('buttonar');

    }

    public function get_data() {
        $data = parent::get_data();
        if ($data) {
            $data->title = '';
            $data->description = '';

            if ($this->title) {
                $data->title = $this->title;
            }

            if ($this->description) {
                $data->description = $this->description;
            }
        }
        return $data;
    }

    // Perform some extra moodle validation.
    public function validation($data, $files) {
        global $DB, $CFG;

        $errors = parent::validation($data, $files);
        if ($foundcourses = $DB->get_records('course', array('shortname' => $data['shortname']))) {
            if (!empty($data['id'])) {
                unset($foundcourses[$data['id']]);
            }
            if (!empty($foundcourses)) {
                foreach ($foundcourses as $foundcourse) {
                    $foundcoursenames[] = $foundcourse->fullname;
                }
                $foundcoursenamestring = implode(',', $foundcoursenames);
                $errors['shortname'] = get_string('shortnametaken', '', $foundcoursenamestring);
            }
        }

        return $errors;
    }
}
