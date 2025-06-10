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
 * Unit tests for the \local_onenote\api\base class
 *
 * @package    local_onenote
 * @author Vinayak (Vin) Bhalerao (v-vibhal@microsoft.com)
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright  Microsoft, Inc.
 */

/**
 * Unit tests for the \local_onenote\api\base class.
 * In order to run these tests, you need to do the following:
 * 1) Create a file phpu_config_data.json and place it in the same folder as this file.
 * 2) The file should contain config data for running these unit tests:
 * {
 *   "client_id": "valid client id for the Microsoft application you want to use for testing",
 *   "client_secret": "valid client secret for the Microsoft application you want to use for testing",
 *   "refresh_tokens": [
 *       "valid refresh token for the first Microsoft Account user you want to use for testing",
 *       "valid refresh token for the second Microsoft Account user you want to use for testing"
 *    ]
 *  }
 *  3) Run the unit tests using the standard process for running PHP Unit tests for Moodle.
 */

defined('MOODLE_INTERNAL') || die();
global $CFG;
require_once($CFG->dirroot . '/mod/assign/tests/base_test.php');

/**
 * Class microsoft_onenote_testcase
 *
 * @group local_onenote
 * @group office365
 */
class local_onenote_onenoteapi_testcase extends advanced_testcase {
    /** @var \onenoteapi */
    private $onenoteapi;
    /** @var \user */
    protected $user;
    /** @var \user1 */
    protected $user1;
    /** @var \course1 */
    protected $course1;
    /** @var \course2 */
    protected $course2;
    /** @var \cm */
    protected $cm;
    /** @var \cm1 */
    protected $cm1;
    /** @var \context */
    protected $context;
    /** @var \context1 */
    protected $context1;
    /** @var \assign */
    protected $assign;
    /** @var \assign1 */
    protected $assign1;
    /** @var \config */
    protected $config;

    /**
     * Create basic setup for test cases
     *
     * @return bool
     */
    public function setup() : void {
        global $CFG;
        return; // Need to update tests to not contact external services.
        $this->resetAfterTest(true);

        $this->user = $this->getDataGenerator()->create_user();
        $this->user1 = $this->getDataGenerator()->create_user();
        $this->course1 = $this->getDataGenerator()->create_course();
        $this->course2 = $this->getDataGenerator()->create_course();

        // Setting user and enrolling to the courses created with teacher role.
        $this->setUser($this->user->id);
        $c1ctx = context_course::instance($this->course1->id);
        $c2ctx = context_course::instance($this->course2->id);
        $this->getDataGenerator()->enrol_user($this->user->id, $this->course1->id, 3);
        $this->getDataGenerator()->enrol_user($this->user->id, $this->course2->id, 3);
        $this->assertCount(2, enrol_get_my_courses());
        $courses = enrol_get_my_courses();

        // Student enrollment.
        $this->setUser($this->user1->id);
        $this->getDataGenerator()->enrol_user($this->user1->id, $this->course1->id, 5);
        $this->getDataGenerator()->enrol_user($this->user1->id, $this->course2->id, 5);

        $this->assertCount(2, get_enrolled_users($c1ctx));
    }

    /**
     * Set client id and client secret for tests
     */
    public function set_test_config() {
        // Read settings from config.json.
        $configdata = file_get_contents($CFG->dirroot . '/local/onenote/tests/phpu_config_data.json');
        if (!$configdata) {
            echo 'Please provide PHPUnit testing configs in a config.json file';
            return false;
        }

        $this->config = json_decode($configdata, false);
        set_config('clientid', $this->config->client_id, 'local_msaccount');
        set_config('clientsecret', $this->config->client_secret, 'local_msaccount');
        $this->onenoteapi = \local_onenote\api\base::getinstance();
    }

    /**
     * Set current user
     *
     * @param int $index
     */
    public function set_user($index) {
        if ($index == 0) {
            $this->setUser($this->user->id);
        } else {
            $this->setUser($this->user1->id);
        }
        $this->onenoteapi->get_msaccount_api()->store_refresh_token($this->config->refresh_tokens[$index]);
        $this->assertEquals(true, $this->onenoteapi->get_msaccount_api()->refresh_token());
        $this->assertEquals(true, $this->onenoteapi->get_msaccount_api()->is_logged_in());
    }

    /**
     * Test for checking create_temp_folder
     */
    public function test_createtempfolder() {
        return true; // Need to update test to not require config data.
        $this->set_test_config();
        $this->set_user(0);

        $this->assertNotNull($this->onenoteapi->create_temp_folder(), 'Unable to create temp folder');
    }

    /**
     * Test for onenote action button
     */
    public function test_renderactionbutton() {
        return true; // Need to update test to not require config data.
        $this->set_test_config();
        $this->set_user(0);
        global $CFG;

        $generator = $this->getDataGenerator()->get_plugin_generator('mod_assign');
        $params['course'] = $this->course1->id;
        $instance = $generator->create_instance($params);
        $this->cm = get_coursemodule_from_instance('assign', $instance->id);

        $expected = '<a onclick="window.open(this.href,\'_blank\'); return false;"';
        $expected .= ' href="' . $CFG->wwwroot . '/local/onenote/onenote_actions.php?';
        $expected .= 'action=openpage&cmid=1&wantfeedback&isteacher&submissionuserid&submissionid&gradeid"';
        $expected .= ' class="local_onenote_linkbutton">Onenote</a>';
        $button = $this->onenoteapi->render_action_button('Onenote', $this->cm->id);
        $this->assertEquals($expected, $button, 'Invalid action button');
    }

    /**
     * Test for checking if the user is teacher
     */
    public function test_isteacher() {
        return true; // Need to update test to not require config data.
        $this->set_test_config();
        $this->set_user(0);
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_assign');
        $params['course'] = $this->course1->id;
        $instance = $generator->create_instance($params);
        $this->cm = get_coursemodule_from_instance('assign', $instance->id);
        $this->assertTrue($this->onenoteapi->is_teacher($this->cm->id, $this->user->id), "user is not teacher");
    }

    /**
     * Test for getitemlist api
     */
    public function test_getitemlist() {
        return true; // Need to update test to not require config data.
        $this->set_test_config();
        $this->set_user(0);

        $itemlist = $this->onenoteapi->get_items_list();
        $notesectionnames = [];
        $course1 = $this->course1->fullname;
        $course2 = $this->course2->fullname;
        $expectednames = ['Moodle Notebook', $course1, $course2];

        foreach ($itemlist as $item) {
            if ($item['title'] == "Moodle Notebook") {
                array_push($notesectionnames, "Moodle Notebook");
                $itemlist = $this->onenoteapi->get_items_list($item['path']);
                foreach ($itemlist as $item) {
                    array_push($notesectionnames, $item['title']);
                }
            }
        }
        $this->assertTrue(in_array("Moodle Notebook", $notesectionnames), "Moodle Notebook not present");
        $this->assertTrue(in_array($course1, $notesectionnames), "Test course1 is not present");
        $this->assertTrue(in_array($course2, $notesectionnames), "Test course2 is  not present");
        $this->assertTrue(count($expectednames) == count(array_intersect($expectednames, $notesectionnames)),
            "Same elements are not present");
        $this->assertNotEmpty($itemlist, "No value");
    }

    /**
     * Test for checking if assignment submission size is greater than than assignment limit.
     */
    public function test_sizelimits() {
        return true; // Need to update test to not require config data.
        $this->set_test_config();
        $this->set_user(0);

        // Creating a testable assignment.
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_assign');
        $params['course'] = $this->course1->id;
        $params['intro'] = '<h3>Heading 1</h3><br><h4>Heading 2</h4><br><h5>Heading 3</h5>ï¿¼';
        $instance = $generator->create_instance($params);
        $this->cm = get_coursemodule_from_instance('assign', $instance->id);
        $this->context = context_module::instance($this->cm->id);
        $this->assign = new testable_assign($this->context, $this->cm, $this->course1);

        // To get the notebooks of student.
        $this->set_user(1);

        // Student submission to onenote.
        $createsubmission = $this->create_submission_feedback($this->cm, false, false, null, null, null);
        $this->submission = $this->assign->get_user_submission($this->user1->id, true);

        // Saving the assignment.
        $data = new stdClass();
        $assignsubmission = new assign_submission_onenote($this->assign, '');

        // Set submission size limit.
        $assignsubmission->set_config('maxsubmissionsizebytes', '10');
        $saveassign = $assignsubmission->save($this->submission, $data);

        $this->assertFalse($saveassign, 'Submission limit check fails');

        // Set course size limit.
        $this->course1->maxbytes = 10;

        // Creating feedback for submission.
        $this->set_user(0);

        // Saving the grade.
        $this->grade = $this->assign->get_user_grade($this->user1->id, true);
        $gradeassign = new assign_feedback_onenote($this->assign, '');
        $gradeassign = $gradeassign->save($this->grade, $data);

        $this->assertFalse($gradeassign, 'Feedback limit check fails');

    }

    /**
     * Test for checking html processing.
     */
    public function test_downloadpagehtml() {
        return true; // Need to update test to not require config data.
        global $DB;
        $this->set_test_config();
        $this->set_user(0);

        // Creating a testable assignment.
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_assign');
        $params['course'] = $this->course1->id;
        $params['intro'] = '<h3>Heading 1</h3><p>This is test assignment.</p><br>';
        $instance = $generator->create_instance($params);
        $this->cm = get_coursemodule_from_instance('assign', $instance->id);
        $this->context = context_module::instance($this->cm->id);
        $this->assign = new testable_assign($this->context, $this->cm, $this->course1);

        // To get the notebooks of student.
        $this->set_user(1);

        // Student submission to onenote.
        $createsubmission = $this->create_submission_feedback($this->cm, false, false, null, null, null);
        $this->submission = $this->assign->get_user_submission($this->user1->id, true);

        $record = $DB->get_record('local_onenote_assign_pages',
            ["assign_id" => $this->submission->assignment, "user_id" => $this->submission->userid]);

        $tempfolder = $this->onenoteapi->create_temp_folder();
        $tempfile = join(DIRECTORY_SEPARATOR, [rtrim($tempfolder, DIRECTORY_SEPARATOR), uniqid('asg_')]) . '.zip';
        $info = $this->onenoteapi->download_page($record->submission_student_page_id, $tempfile);

        $zip = new ZipArchive;
        $res = $zip->open($info['path']);
        if ($res === true) {
            $zip->extractTo($tempfolder);
            $zip->close();
        }
        $folder = join(DIRECTORY_SEPARATOR, [rtrim($tempfolder, DIRECTORY_SEPARATOR), '0']);
        $pagefile = join(DIRECTORY_SEPARATOR, [rtrim($folder, DIRECTORY_SEPARATOR), 'page.html']);

        $htmldom = new DomDocument;
        $htmldom->loadHTMLFile($pagefile);
        $htmldom->preservewhitespace = false;

        $domclone = new DOMDocument;
        $domclone->preservewhitespace = false;
        $doc = $htmldom->getElementsByTagName("div")->item(0);

        foreach ($doc->childNodes as $child) {
            $domclone->appendChild($domclone->importNode($child, true));
        }

        $output = $domclone->saveHTML();

        $expectedhtml = '<h3 style="font-size:12pt;color:#5b9bd5;margin-top:11pt;margin-bottom:11pt">';
        $expectedhtml .= '<span style="font-family:Helvetica;font-size:13.5pt;color:#333333">Heading 1</span></h3> ';
        $expectedhtml .= '<p><span style="font-family:Helvetica;font-size:10.5pt;color:#333333">This is test assignment.</span></p> ';
        $expectedhtml .= '<p><span style="font-family:Helvetica;font-size:10.5pt;color:#333333">&nbsp;</span></p>';

        $output = trim(preg_replace('/\s+/', ' ', $output));

        $this->assertContains($expectedhtml, $output, 'Html does not match');
    }

    /**
     * Test for getpage method.
     */
    public function test_getpage() {
        return true; // Need to update test to not require config data.
        $this->set_test_config();
        $this->set_user(0);

        // Creating a testable assignment.
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_assign');
        $params['course'] = $this->course1->id;
        $params['intro'] = '<h3>Heading 1</h3><br><h4>Heading 2</h4><br><h5>Heading 3</h5>ï¿¼';
        $instance = $generator->create_instance($params);
        $this->cm = get_coursemodule_from_instance('assign', $instance->id);
        $this->context = context_module::instance($this->cm->id);
        $this->assign = new testable_assign($this->context, $this->cm, $this->course1);
        $assigndetails = $this->assign->get_instance();
        $assignid = $assigndetails->id;

        // To get the notebooks of student.
        $this->set_user(1);

        $itemlist = $this->onenoteapi->get_items_list();

        // Student submission to onenote.
        $createsubmission = $this->create_submission_feedback($this->cm, false, false, null, null, null);
        $this->submission = $this->assign->get_user_submission($this->user1->id, true);

        // Saving the assignment.
        $data = new stdClass();
        $saveassign = new assign_submission_onenote($this->assign, '');
        $saveassign = $saveassign->save($this->submission, $data);

        // Creating feedback for submission.
        $this->set_user(0);

        // Saving the grade.
        $this->grade = $this->assign->get_user_grade($this->user1->id, true);
        $gradeassign = new assign_feedback_onenote($this->assign, '');
        $gradeassign = $gradeassign->save($this->grade, $data);
        $gradeid = $this->grade->grade;
        $createfeedback =
            $this->create_submission_feedback($this->cm, true, true, $this->user1->id, $this->submission->id, $gradeid);

        if (filter_var($createsubmission, FILTER_VALIDATE_URL)) {
            if (strpos($this->course1->fullname, urldecode($createsubmission))) {
                $this->assertTrue("The value is present");
            }
        }

        if (filter_var($createfeedback, FILTER_VALIDATE_URL)) {
            if (strpos($this->course1->fullname, urldecode($createfeedback))) {
                $this->assertTrue("The value is present");
            }
        }
    }

    /**
     * Test for download page method.
     */
    public function test_downloadpage() {
        return true; // Need to update test to not require config data.
        $this->set_test_config();
        $this->set_user(0);

        $generator = $this->getDataGenerator()->get_plugin_generator('mod_assign');
        $params['course'] = $this->course2->id;
        $instance = $generator->create_instance($params);
        $this->cm = get_coursemodule_from_instance('assign', $instance->id);
        $this->context = context_module::instance($this->cm->id);
        $this->assign = new testable_assign($this->context, $this->cm, $this->course2);
        $assigndetails = $this->assign->get_instance();
        $assignid = $assigndetails->id;

        // To get the notebooks of student.
        $this->set_user(1);

        $this->create_submission_feedback($this->cm, false, false, null, null, null);
        $this->create_submission_feedback($this->cm, false, false, null, null, null);
        $this->submission = $this->assign->get_user_submission($this->user1->id, true);
        // Saving the assignment.
        $data = new stdClass();
        $saveassign = new assign_submission_onenote($this->assign, '');
        $saveassign = $saveassign->save($this->submission, $data);

        $this->assertNotEmpty($saveassign, "File has not created");
    }

    /**
     * Method for creating submission page.
     *
     * @param object $cm
     * @param bool $wantfeedbackpage
     * @param bool $isteacher
     * @param null $submissionuserid
     * @param null $submissionid
     * @param null $gradeid
     * @return mixed
     */
    public function create_submission_feedback($cm, $wantfeedbackpage = false, $isteacher = false, $submissionuserid = null,
        $submissionid = null, $gradeid = null) {
        $submissionfeedback =
            $this->onenoteapi->get_page($cm->id, $wantfeedbackpage, $isteacher, $submissionuserid, $submissionid, $gradeid);
        return $submissionfeedback;
    }
}
