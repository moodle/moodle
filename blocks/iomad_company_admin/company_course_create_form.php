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
 * Script to let a user create a course for a particular company.
 */

require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->libdir . '/formslib.php');
require_once('lib.php');
require_once(dirname(__FILE__) . '/../../course/lib.php');

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

        // Then show the fields about where this block appears.
        $mform->addElement('header', 'header',
                            get_string('companycourse', 'block_iomad_company_admin'));

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

        // Add custom fields to the form.
        $handler = core_course\customfield\course_handler::create();
        $handler->set_parent_context(context_coursecat::instance($this->companyrec->category)); // For course handler only.
        $handler->instance_form_definition($mform, 0);

        // Create course as self enrolable.
        if (iomad::has_capability('block/iomad_company_admin:edit_licenses', context_system::instance())) {
            $selectarray = array(get_string('selfenrolled', 'block_iomad_company_admin'),
                                 get_string('enrolled', 'block_iomad_company_admin'),
                                 get_string('licensedcourse', 'block_iomad_company_admin'));
        } else {
            $selectarray = array(get_string('selfenrolled', 'block_iomad_company_admin'),
                                 get_string('enrolled', 'block_iomad_company_admin'));
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

$returnurl = optional_param('returnurl', '', PARAM_LOCALURL);
$companyid = optional_param('companyid', 0, PARAM_INTEGER);

$context = context_system::instance();
require_login();
iomad::require_capability('block/iomad_company_admin:createcourse', $context);

$PAGE->set_context($context);

// Correct the navbar.
// Set the name for the page.
$linktext = get_string('createcourse_title', 'block_iomad_company_admin');

// Set the url.
$linkurl = new moodle_url('/blocks/iomad_company_admin/company_course_create_form.php');

// Print the page header.
$PAGE->set_context($context);
$PAGE->set_url($linkurl);
$PAGE->set_pagelayout('admin');
$PAGE->set_title($linktext);

// Set the page heading.
$PAGE->set_heading(get_string('myhome') . " - $linktext");
if (empty($CFG->defaulthomepage)) {
    $PAGE->navbar->add(get_string('dashboard', 'block_iomad_company_admin'), new moodle_url($CFG->wwwroot . '/my'));
}
$PAGE->navbar->add($linktext, $linkurl);

// Set the companyid
$companyid = iomad::get_my_companyid($context);

$urlparams = array('companyid' => $companyid);
if ($returnurl) {
    $urlparams['returnurl'] = $returnurl;
}
$companylist = new moodle_url('/my', $urlparams);

/* next line copied from /course/edit.php */
$editoroptions = array('maxfiles' => EDITOR_UNLIMITED_FILES,
                       'maxbytes' => $CFG->maxbytes,
                       'trusttext' => false,
                       'noclean' => true);

$mform = new course_edit_form($PAGE->url, $companyid, $editoroptions);

if ($mform->is_cancelled()) {
    redirect($companylist);

} else if ($data = $mform->get_data()) {

    $data->userid = $USER->id;

    // Merge data with course defaults.
    $company = $DB->get_record('company', array('id' => $companyid));
    if (!empty($company->category)) {
        $data->category = $company->category;
    } else {
        $data->category = $CFG->defaultrequestcategory;
    }
    $courseconfig = get_config('moodlecourse');
    $mergeddata = (object) array_merge((array) $courseconfig, (array) $data);

    // Turn on restricted modules.
    $mergeddata->restrictmodules = 1;

    if (!$course = create_course($mergeddata, $editoroptions)) {
        $this->verbose("Error inserting a new course in the database!");
        if (!$this->get('ignore_errors')) {
            die();
        }
    }

    // If licensed course, turn off all enrolments apart from license enrolment as
    // default  Moving this to a separate page.
    if ($data->selfenrol == 0 ) {
        if ($instances = $DB->get_records('enrol', array('courseid' => $course->id))) {
            foreach ($instances as $instance) {
                $updateinstance = (array) $instance;
                if ($instance->enrol == 'self') {
                    $updateinstance['status'] = 0;
                } else if ($instance->enrol == 'license') {
                    $updateinstance['status'] = 1;
                } else if ($instance->enrol == 'manual') {
                    $updateinstance['status'] = 0;
                }
                $DB->update_record('enrol', $updateinstance);
            }
        }
    } else if ($data->selfenrol == 1 ) {
        if ($instances = $DB->get_records('enrol', array('courseid' => $course->id))) {
            foreach ($instances as $instance) {
                $updateinstance = (array) $instance;
                if ($instance->enrol == 'self') {
                    $updateinstance['status'] = 1;
                } else if ($instance->enrol == 'license') {
                    $updateinstance['status'] = 1;
                } else if ($instance->enrol == 'manual') {
                    $updateinstance['status'] = 0;
                }
                $DB->update_record('enrol', $updateinstance);
            }
        }
    } else if ($data->selfenrol == 2 ) {
        if ($instances = $DB->get_records('enrol', array('courseid' => $course->id))) {
            foreach ($instances as $instance) {
                $updateinstance = (array) $instance;
                if ($instance->enrol == 'self') {
                    $updateinstance['status'] = 1;
                } else if ($instance->enrol == 'license') {
                    $updateinstance['status'] = 0;
                } else if ($instance->enrol == 'manual') {
                    $updateinstance['status'] = 1;
                }
                $DB->update_record('enrol', $updateinstance);
            }
        }
    }

    // Associate the company with the course.
    $company = new company($companyid);
    // Check if we are a company manager.
    if ($data->selfenrol != 2 && $DB->get_record('company_users', array('companyid' => $companyid,
                                                   'userid' => $USER->id,
                                                   'managertype' => 1))) {
        $company->add_course($course, 0, true);
    } else if ($data->selfenrol == 2) {
        $company->add_course($course, 0, false, true);
    } else {
        $company->add_course($course);
    }

    if (isset($data->submitandviewbutton)) {
        // We are going to the course instead.
        redirect(new moodle_url('/course/view.php', array('id' => $course->id)), get_string('coursecreatedok', 'block_iomad_company_admin'), null, \core\output\notification::NOTIFY_SUCCESS);
    } else {
        redirect($companylist, get_string('coursecreatedok', 'block_iomad_company_admin'), null, \core\output\notification::NOTIFY_SUCCESS);
    }
} else {

    echo $OUTPUT->header();

    $mform->display();

    echo $OUTPUT->footer();
}

