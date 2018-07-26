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
 * Feedback module external functions tests
 *
 * @package    mod_feedback
 * @category   external
 * @copyright  2017 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.3
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/webservice/tests/helpers.php');
require_once($CFG->dirroot . '/mod/feedback/lib.php');

use mod_feedback\external\feedback_summary_exporter;

/**
 * Feedback module external functions tests
 *
 * @package    mod_feedback
 * @category   external
 * @copyright  2017 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.3
 */
class mod_feedback_external_testcase extends externallib_advanced_testcase {

    /**
     * Set up for every test
     */
    public function setUp() {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();

        // Setup test data.
        $this->course = $this->getDataGenerator()->create_course();
        $this->feedback = $this->getDataGenerator()->create_module('feedback', array('course' => $this->course->id));
        $this->context = context_module::instance($this->feedback->cmid);
        $this->cm = get_coursemodule_from_instance('feedback', $this->feedback->id);

        // Create users.
        $this->student = self::getDataGenerator()->create_user();
        $this->teacher = self::getDataGenerator()->create_user();

        // Users enrolments.
        $this->studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $this->teacherrole = $DB->get_record('role', array('shortname' => 'editingteacher'));
        $this->getDataGenerator()->enrol_user($this->student->id, $this->course->id, $this->studentrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($this->teacher->id, $this->course->id, $this->teacherrole->id, 'manual');
    }

    /**
     * Helper method to add items to an existing feedback.
     *
     * @param stdClass  $feedback feedback instance
     * @param integer $pagescount the number of pages we want in the feedback
     * @return array list of items created
     */
    public function populate_feedback($feedback, $pagescount = 1) {
        $feedbackgenerator = $this->getDataGenerator()->get_plugin_generator('mod_feedback');
        $itemscreated = [];

        // Create at least one page.
        $itemscreated[] = $feedbackgenerator->create_item_label($feedback);
        $itemscreated[] = $feedbackgenerator->create_item_info($feedback);
        $itemscreated[] = $feedbackgenerator->create_item_numeric($feedback);

        // Check if we want more pages.
        for ($i = 1; $i < $pagescount; $i++) {
            $itemscreated[] = $feedbackgenerator->create_item_pagebreak($feedback);
            $itemscreated[] = $feedbackgenerator->create_item_multichoice($feedback);
            $itemscreated[] = $feedbackgenerator->create_item_multichoicerated($feedback);
            $itemscreated[] = $feedbackgenerator->create_item_textarea($feedback);
            $itemscreated[] = $feedbackgenerator->create_item_textfield($feedback);
            $itemscreated[] = $feedbackgenerator->create_item_numeric($feedback);
        }
        return $itemscreated;
    }


    /**
     * Test test_mod_feedback_get_feedbacks_by_courses
     */
    public function test_mod_feedback_get_feedbacks_by_courses() {
        global $DB;

        // Create additional course.
        $course2 = self::getDataGenerator()->create_course();

        // Second feedback.
        $record = new stdClass();
        $record->course = $course2->id;
        $feedback2 = self::getDataGenerator()->create_module('feedback', $record);

        // Execute real Moodle enrolment as we'll call unenrol() method on the instance later.
        $enrol = enrol_get_plugin('manual');
        $enrolinstances = enrol_get_instances($course2->id, true);
        foreach ($enrolinstances as $courseenrolinstance) {
            if ($courseenrolinstance->enrol == "manual") {
                $instance2 = $courseenrolinstance;
                break;
            }
        }
        $enrol->enrol_user($instance2, $this->student->id, $this->studentrole->id);

        self::setUser($this->student);

        $returndescription = mod_feedback_external::get_feedbacks_by_courses_returns();

        // Create what we expect to be returned when querying the two courses.
        // First for the student user.
        $expectedfields = array('id', 'coursemodule', 'course', 'name', 'intro', 'introformat', 'introfiles', 'anonymous',
            'multiple_submit', 'autonumbering', 'page_after_submitformat', 'publish_stats', 'completionsubmit');

        $properties = feedback_summary_exporter::read_properties_definition();

        // Add expected coursemodule and data.
        $feedback1 = $this->feedback;
        $feedback1->coursemodule = $feedback1->cmid;
        $feedback1->introformat = 1;
        $feedback1->introfiles = [];

        $feedback2->coursemodule = $feedback2->cmid;
        $feedback2->introformat = 1;
        $feedback2->introfiles = [];

        foreach ($expectedfields as $field) {
            if (!empty($properties[$field]) && $properties[$field]['type'] == PARAM_BOOL) {
                $feedback1->{$field} = (bool) $feedback1->{$field};
                $feedback2->{$field} = (bool) $feedback2->{$field};
            }
            $expected1[$field] = $feedback1->{$field};
            $expected2[$field] = $feedback2->{$field};
        }

        $expectedfeedbacks = array($expected2, $expected1);

        // Call the external function passing course ids.
        $result = mod_feedback_external::get_feedbacks_by_courses(array($course2->id, $this->course->id));
        $result = external_api::clean_returnvalue($returndescription, $result);

        $this->assertEquals($expectedfeedbacks, $result['feedbacks']);
        $this->assertCount(0, $result['warnings']);

        // Call the external function without passing course id.
        $result = mod_feedback_external::get_feedbacks_by_courses();
        $result = external_api::clean_returnvalue($returndescription, $result);
        $this->assertEquals($expectedfeedbacks, $result['feedbacks']);
        $this->assertCount(0, $result['warnings']);

        // Unenrol user from second course and alter expected feedbacks.
        $enrol->unenrol_user($instance2, $this->student->id);
        array_shift($expectedfeedbacks);

        // Call the external function without passing course id.
        $result = mod_feedback_external::get_feedbacks_by_courses();
        $result = external_api::clean_returnvalue($returndescription, $result);
        $this->assertEquals($expectedfeedbacks, $result['feedbacks']);

        // Call for the second course we unenrolled the user from, expected warning.
        $result = mod_feedback_external::get_feedbacks_by_courses(array($course2->id));
        $this->assertCount(1, $result['warnings']);
        $this->assertEquals('1', $result['warnings'][0]['warningcode']);
        $this->assertEquals($course2->id, $result['warnings'][0]['itemid']);

        // Now, try as a teacher for getting all the additional fields.
        self::setUser($this->teacher);

        $additionalfields = array('email_notification', 'site_after_submit', 'page_after_submit', 'timeopen', 'timeclose',
            'timemodified', 'pageaftersubmitfiles');

        $feedback1->pageaftersubmitfiles = [];

        foreach ($additionalfields as $field) {
            if (!empty($properties[$field]) && $properties[$field]['type'] == PARAM_BOOL) {
                $feedback1->{$field} = (bool) $feedback1->{$field};
            }
            $expectedfeedbacks[0][$field] = $feedback1->{$field};
        }
        $expectedfeedbacks[0]['page_after_submitformat'] = 1;

        $result = mod_feedback_external::get_feedbacks_by_courses();
        $result = external_api::clean_returnvalue($returndescription, $result);
        $this->assertEquals($expectedfeedbacks, $result['feedbacks']);

        // Admin also should get all the information.
        self::setAdminUser();

        $result = mod_feedback_external::get_feedbacks_by_courses(array($this->course->id));
        $result = external_api::clean_returnvalue($returndescription, $result);
        $this->assertEquals($expectedfeedbacks, $result['feedbacks']);
    }

    /**
     * Test get_feedback_access_information function with basic defaults for student.
     */
    public function test_get_feedback_access_information_student() {

        self::setUser($this->student);
        $result = mod_feedback_external::get_feedback_access_information($this->feedback->id);
        $result = external_api::clean_returnvalue(mod_feedback_external::get_feedback_access_information_returns(), $result);

        $this->assertFalse($result['canviewanalysis']);
        $this->assertFalse($result['candeletesubmissions']);
        $this->assertFalse($result['canviewreports']);
        $this->assertFalse($result['canedititems']);
        $this->assertTrue($result['cancomplete']);
        $this->assertTrue($result['cansubmit']);
        $this->assertTrue($result['isempty']);
        $this->assertTrue($result['isopen']);
        $this->assertTrue($result['isanonymous']);
        $this->assertFalse($result['isalreadysubmitted']);
    }

    /**
     * Test get_feedback_access_information function with basic defaults for teacher.
     */
    public function test_get_feedback_access_information_teacher() {

        self::setUser($this->teacher);
        $result = mod_feedback_external::get_feedback_access_information($this->feedback->id);
        $result = external_api::clean_returnvalue(mod_feedback_external::get_feedback_access_information_returns(), $result);

        $this->assertTrue($result['canviewanalysis']);
        $this->assertTrue($result['canviewreports']);
        $this->assertTrue($result['canedititems']);
        $this->assertTrue($result['candeletesubmissions']);
        $this->assertFalse($result['cancomplete']);
        $this->assertTrue($result['cansubmit']);
        $this->assertTrue($result['isempty']);
        $this->assertTrue($result['isopen']);
        $this->assertTrue($result['isanonymous']);
        $this->assertFalse($result['isalreadysubmitted']);

        // Add some items to the feedback and check is not empty any more.
        self::populate_feedback($this->feedback);
        $result = mod_feedback_external::get_feedback_access_information($this->feedback->id);
        $result = external_api::clean_returnvalue(mod_feedback_external::get_feedback_access_information_returns(), $result);
        $this->assertFalse($result['isempty']);
    }

    /**
     * Test view_feedback invalid id.
     */
    public function test_view_feedback_invalid_id() {
        // Test invalid instance id.
        $this->expectException('moodle_exception');
        mod_feedback_external::view_feedback(0);
    }
    /**
     * Test view_feedback not enrolled user.
     */
    public function test_view_feedback_not_enrolled_user() {
        $usernotenrolled = self::getDataGenerator()->create_user();
        $this->setUser($usernotenrolled);
        $this->expectException('moodle_exception');
        mod_feedback_external::view_feedback(0);
    }
    /**
     * Test view_feedback no capabilities.
     */
    public function test_view_feedback_no_capabilities() {
        // Test user with no capabilities.
        // We need a explicit prohibit since this capability is allowed for students by default.
        assign_capability('mod/feedback:view', CAP_PROHIBIT, $this->studentrole->id, $this->context->id);
        accesslib_clear_all_caches_for_unit_testing();
        $this->expectException('moodle_exception');
        mod_feedback_external::view_feedback(0);
    }
    /**
     * Test view_feedback.
     */
    public function test_view_feedback() {
        // Test user with full capabilities.
        $this->setUser($this->student);
        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $result = mod_feedback_external::view_feedback($this->feedback->id);
        $result = external_api::clean_returnvalue(mod_feedback_external::view_feedback_returns(), $result);
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = array_shift($events);
        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_feedback\event\course_module_viewed', $event);
        $this->assertEquals($this->context, $event->get_context());
        $moodledata = new \moodle_url('/mod/feedback/view.php', array('id' => $this->cm->id));
        $this->assertEquals($moodledata, $event->get_url());
        $this->assertEventContextNotUsed($event);
        $this->assertNotEmpty($event->get_name());
    }

    /**
     * Test get_current_completed_tmp.
     */
    public function test_get_current_completed_tmp() {
        global $DB;

        // Force non anonymous.
        $DB->set_field('feedback', 'anonymous', FEEDBACK_ANONYMOUS_NO, array('id' => $this->feedback->id));
        // Add a completed_tmp record.
        $record = [
            'feedback' => $this->feedback->id,
            'userid' => $this->student->id,
            'guestid' => '',
            'timemodified' => time() - DAYSECS,
            'random_response' => 0,
            'anonymous_response' => FEEDBACK_ANONYMOUS_NO,
            'courseid' => $this->course->id,
        ];
        $record['id'] = $DB->insert_record('feedback_completedtmp', (object) $record);

        // Test user with full capabilities.
        $this->setUser($this->student);

        $result = mod_feedback_external::get_current_completed_tmp($this->feedback->id);
        $result = external_api::clean_returnvalue(mod_feedback_external::get_current_completed_tmp_returns(), $result);
        $this->assertEquals($record['id'], $result['feedback']['id']);
    }

    /**
     * Test get_items.
     */
    public function test_get_items() {
        // Test user with full capabilities.
        $this->setUser($this->student);

        // Add questions to the feedback, we are adding 2 pages of questions.
        $itemscreated = self::populate_feedback($this->feedback, 2);

        $result = mod_feedback_external::get_items($this->feedback->id);
        $result = external_api::clean_returnvalue(mod_feedback_external::get_items_returns(), $result);
        $this->assertCount(count($itemscreated), $result['items']);
        $index = 1;
        foreach ($result['items'] as $key => $item) {
            if (is_numeric($itemscreated[$key])) {
                continue; // Page break.
            }
            // Cannot compare directly the exporter and the db object (exporter have more fields).
            $this->assertEquals($itemscreated[$key]->id, $item['id']);
            $this->assertEquals($itemscreated[$key]->typ, $item['typ']);
            $this->assertEquals($itemscreated[$key]->name, $item['name']);
            $this->assertEquals($itemscreated[$key]->label, $item['label']);

            if ($item['hasvalue']) {
                $this->assertEquals($index, $item['itemnumber']);
                $index++;
            }
        }
    }

    /**
     * Test launch_feedback.
     */
    public function test_launch_feedback() {
        global $DB;

        // Test user with full capabilities.
        $this->setUser($this->student);

        // Add questions to the feedback, we are adding 2 pages of questions.
        $itemscreated = self::populate_feedback($this->feedback, 2);

        // First try a feedback we didn't attempt.
        $result = mod_feedback_external::launch_feedback($this->feedback->id);
        $result = external_api::clean_returnvalue(mod_feedback_external::launch_feedback_returns(), $result);
        $this->assertEquals(0, $result['gopage']);

        // Now, try a feedback that we attempted.
        // Force non anonymous.
        $DB->set_field('feedback', 'anonymous', FEEDBACK_ANONYMOUS_NO, array('id' => $this->feedback->id));
        // Add a completed_tmp record.
        $record = [
            'feedback' => $this->feedback->id,
            'userid' => $this->student->id,
            'guestid' => '',
            'timemodified' => time() - DAYSECS,
            'random_response' => 0,
            'anonymous_response' => FEEDBACK_ANONYMOUS_NO,
            'courseid' => $this->course->id,
        ];
        $record['id'] = $DB->insert_record('feedback_completedtmp', (object) $record);

        // Add a response to the feedback for each question type with possible values.
        $response = [
            'course_id' => $this->course->id,
            'item' => $itemscreated[1]->id, // First item is the info question.
            'completed' => $record['id'],
            'tmp_completed' => $record['id'],
            'value' => 'A',
        ];
        $DB->insert_record('feedback_valuetmp', (object) $response);
        $response = [
            'course_id' => $this->course->id,
            'item' => $itemscreated[2]->id, // Second item is the numeric question.
            'completed' => $record['id'],
            'tmp_completed' => $record['id'],
            'value' => 5,
        ];
        $DB->insert_record('feedback_valuetmp', (object) $response);

        $result = mod_feedback_external::launch_feedback($this->feedback->id);
        $result = external_api::clean_returnvalue(mod_feedback_external::launch_feedback_returns(), $result);
        $this->assertEquals(1, $result['gopage']);
    }

    /**
     * Test get_page_items.
     */
    public function test_get_page_items() {
        // Test user with full capabilities.
        $this->setUser($this->student);

        // Add questions to the feedback, we are adding 2 pages of questions.
        $itemscreated = self::populate_feedback($this->feedback, 2);

        // Retrieve first page.
        $result = mod_feedback_external::get_page_items($this->feedback->id, 0);
        $result = external_api::clean_returnvalue(mod_feedback_external::get_page_items_returns(), $result);
        $this->assertCount(3, $result['items']);    // The first page has 3 items.
        $this->assertTrue($result['hasnextpage']);
        $this->assertFalse($result['hasprevpage']);

        // Retrieve second page.
        $result = mod_feedback_external::get_page_items($this->feedback->id, 1);
        $result = external_api::clean_returnvalue(mod_feedback_external::get_page_items_returns(), $result);
        $this->assertCount(5, $result['items']);    // The second page has 5 items (page break doesn't count).
        $this->assertFalse($result['hasnextpage']);
        $this->assertTrue($result['hasprevpage']);
    }

    /**
     * Test process_page.
     */
    public function test_process_page() {
        global $DB;

        // Test user with full capabilities.
        $this->setUser($this->student);
        $pagecontents = 'You finished it!';
        $DB->set_field('feedback', 'page_after_submit', $pagecontents, array('id' => $this->feedback->id));

        // Add questions to the feedback, we are adding 2 pages of questions.
        $itemscreated = self::populate_feedback($this->feedback, 2);

        $data = [];
        foreach ($itemscreated as $item) {

            if (empty($item->hasvalue)) {
                continue;
            }

            switch ($item->typ) {
                case 'textarea':
                case 'textfield':
                    $value = 'Lorem ipsum';
                    break;
                case 'numeric':
                    $value = 5;
                    break;
                case 'multichoice':
                    $value = '1';
                    break;
                case 'multichoicerated':
                    $value = '1';
                    break;
                case 'info':
                    $value = format_string($this->course->shortname, true, array('context' => $this->context));
                    break;
                default:
                    $value = '';
            }
            $data[] = ['name' => $item->typ . '_' . $item->id, 'value' => $value];
        }

        // Process first page.
        $firstpagedata = [$data[0], $data[1]];
        $result = mod_feedback_external::process_page($this->feedback->id, 0, $firstpagedata);
        $result = external_api::clean_returnvalue(mod_feedback_external::process_page_returns(), $result);
        $this->assertEquals(1, $result['jumpto']);
        $this->assertFalse($result['completed']);

        // Now, process the second page. But first we are going back to the first page.
        $secondpagedata = [$data[2], $data[3], $data[4], $data[5], $data[6]];
        $result = mod_feedback_external::process_page($this->feedback->id, 1, $secondpagedata, true);
        $result = external_api::clean_returnvalue(mod_feedback_external::process_page_returns(), $result);
        $this->assertFalse($result['completed']);
        $this->assertEquals(0, $result['jumpto']);  // We jumped to the first page.
        // Check the values were correctly saved.
        $tmpitems = $DB->get_records('feedback_valuetmp');
        $this->assertCount(7, $tmpitems);   // 2 from the first page + 5 from the second page.

        // Go forward again (sending the same data).
        $result = mod_feedback_external::process_page($this->feedback->id, 0, $firstpagedata);
        $result = external_api::clean_returnvalue(mod_feedback_external::process_page_returns(), $result);
        $this->assertEquals(1, $result['jumpto']);
        $this->assertFalse($result['completed']);
        $tmpitems = $DB->get_records('feedback_valuetmp');
        $this->assertCount(7, $tmpitems);   // 2 from the first page + 5 from the second page.

        // And finally, save everything! We are going to modify one previous recorded value.
        $data[2]['value'] = 2; // 2 is value of the option 'b'.
        $secondpagedata = [$data[2], $data[3], $data[4], $data[5], $data[6]];
        $result = mod_feedback_external::process_page($this->feedback->id, 1, $secondpagedata);
        $result = external_api::clean_returnvalue(mod_feedback_external::process_page_returns(), $result);
        $this->assertTrue($result['completed']);
        $this->assertTrue(strpos($result['completionpagecontents'], $pagecontents) !== false);
        // Check all the items were saved.
        $items = $DB->get_records('feedback_value');
        $this->assertCount(7, $items);
        // Check if the one we modified was correctly saved.
        $itemid = $itemscreated[4]->id;
        $itemsaved = $DB->get_field('feedback_value', 'value', array('item' => $itemid));
        $mcitem = new feedback_item_multichoice();
        $itemval = $mcitem->get_printval($itemscreated[4], (object) ['value' => $itemsaved]);
        $this->assertEquals('b', $itemval);

        // Check that the answers are saved for course 0.
        foreach ($items as $item) {
            $this->assertEquals(0, $item->course_id);
        }
        $completed = $DB->get_record('feedback_completed', []);
        $this->assertEquals(0, $completed->courseid);
    }

    /**
     * Test process_page for a site feedback.
     */
    public function test_process_page_site_feedback() {
        global $DB;
        $pagecontents = 'You finished it!';
        $this->feedback = $this->getDataGenerator()->create_module('feedback',
            array('course' => SITEID, 'page_after_submit' => $pagecontents));

        // Test user with full capabilities.
        $this->setUser($this->student);

        // Add questions to the feedback, we are adding 2 pages of questions.
        $itemscreated = self::populate_feedback($this->feedback, 2);

        $data = [];
        foreach ($itemscreated as $item) {

            if (empty($item->hasvalue)) {
                continue;
            }

            switch ($item->typ) {
                case 'textarea':
                case 'textfield':
                    $value = 'Lorem ipsum';
                    break;
                case 'numeric':
                    $value = 5;
                    break;
                case 'multichoice':
                    $value = '1';
                    break;
                case 'multichoicerated':
                    $value = '1';
                    break;
                case 'info':
                    $value = format_string($this->course->shortname, true, array('context' => $this->context));
                    break;
                default:
                    $value = '';
            }
            $data[] = ['name' => $item->typ . '_' . $item->id, 'value' => $value];
        }

        // Process first page.
        $firstpagedata = [$data[0], $data[1]];
        $result = mod_feedback_external::process_page($this->feedback->id, 0, $firstpagedata, false, $this->course->id);
        $result = external_api::clean_returnvalue(mod_feedback_external::process_page_returns(), $result);
        $this->assertEquals(1, $result['jumpto']);
        $this->assertFalse($result['completed']);

        // Process second page.
        $data[2]['value'] = 2; // 2 is value of the option 'b';
        $secondpagedata = [$data[2], $data[3], $data[4], $data[5], $data[6]];
        $result = mod_feedback_external::process_page($this->feedback->id, 1, $secondpagedata, false, $this->course->id);
        $result = external_api::clean_returnvalue(mod_feedback_external::process_page_returns(), $result);
        $this->assertTrue($result['completed']);
        $this->assertTrue(strpos($result['completionpagecontents'], $pagecontents) !== false);
        // Check all the items were saved.
        $items = $DB->get_records('feedback_value');
        $this->assertCount(7, $items);
        // Check if the one we modified was correctly saved.
        $itemid = $itemscreated[4]->id;
        $itemsaved = $DB->get_field('feedback_value', 'value', array('item' => $itemid));
        $mcitem = new feedback_item_multichoice();
        $itemval = $mcitem->get_printval($itemscreated[4], (object) ['value' => $itemsaved]);
        $this->assertEquals('b', $itemval);

        // Check that the answers are saved for the correct course.
        foreach ($items as $item) {
            $this->assertEquals($this->course->id, $item->course_id);
        }
        $completed = $DB->get_record('feedback_completed', []);
        $this->assertEquals($this->course->id, $completed->courseid);
    }

    /**
     * Test get_analysis.
     */
    public function test_get_analysis() {
        // Test user with full capabilities.
        $this->setUser($this->student);

        // Create a very simple feedback.
        $feedbackgenerator = $this->getDataGenerator()->get_plugin_generator('mod_feedback');
        $numericitem = $feedbackgenerator->create_item_numeric($this->feedback);
        $textfielditem = $feedbackgenerator->create_item_textfield($this->feedback);

        $pagedata = [
            ['name' => $numericitem->typ .'_'. $numericitem->id, 'value' => 5],
            ['name' => $textfielditem->typ .'_'. $textfielditem->id, 'value' => 'abc'],
        ];
        // Process the feedback, there is only one page so the feedback will be completed.
        $result = mod_feedback_external::process_page($this->feedback->id, 0, $pagedata);
        $result = external_api::clean_returnvalue(mod_feedback_external::process_page_returns(), $result);
        $this->assertTrue($result['completed']);

        // Retrieve analysis.
        $this->setUser($this->teacher);
        $result = mod_feedback_external::get_analysis($this->feedback->id);
        $result = external_api::clean_returnvalue(mod_feedback_external::get_analysis_returns(), $result);
        $this->assertEquals(1, $result['completedcount']);  // 1 feedback completed.
        $this->assertEquals(2, $result['itemscount']);  // 2 items in the feedback.
        $this->assertCount(2, $result['itemsdata']);
        $this->assertCount(1, $result['itemsdata'][0]['data']); // There are 1 response per item.
        $this->assertCount(1, $result['itemsdata'][1]['data']);
        // Check we receive the info the students filled.
        foreach ($result['itemsdata'] as $data) {
            if ($data['item']['id'] == $numericitem->id) {
                $this->assertEquals(5, $data['data'][0]);
            } else {
                $this->assertEquals('abc', $data['data'][0]);
            }
        }

        // Create another user / response.
        $anotherstudent = self::getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($anotherstudent->id, $this->course->id, $this->studentrole->id, 'manual');
        $this->setUser($anotherstudent);

        // Process the feedback, there is only one page so the feedback will be completed.
        $result = mod_feedback_external::process_page($this->feedback->id, 0, $pagedata);
        $result = external_api::clean_returnvalue(mod_feedback_external::process_page_returns(), $result);
        $this->assertTrue($result['completed']);

        // Retrieve analysis.
        $this->setUser($this->teacher);
        $result = mod_feedback_external::get_analysis($this->feedback->id);
        $result = external_api::clean_returnvalue(mod_feedback_external::get_analysis_returns(), $result);
        $this->assertEquals(2, $result['completedcount']);  // 2 feedback completed.
        $this->assertEquals(2, $result['itemscount']);
        $this->assertCount(2, $result['itemsdata'][0]['data']); // There are 2 responses per item.
        $this->assertCount(2, $result['itemsdata'][1]['data']);
    }

    /**
     * Test get_unfinished_responses.
     */
    public function test_get_unfinished_responses() {
        // Test user with full capabilities.
        $this->setUser($this->student);

        // Create a very simple feedback.
        $feedbackgenerator = $this->getDataGenerator()->get_plugin_generator('mod_feedback');
        $numericitem = $feedbackgenerator->create_item_numeric($this->feedback);
        $textfielditem = $feedbackgenerator->create_item_textfield($this->feedback);
        $feedbackgenerator->create_item_pagebreak($this->feedback);
        $labelitem = $feedbackgenerator->create_item_label($this->feedback);
        $numericitem2 = $feedbackgenerator->create_item_numeric($this->feedback);

        $pagedata = [
            ['name' => $numericitem->typ .'_'. $numericitem->id, 'value' => 5],
            ['name' => $textfielditem->typ .'_'. $textfielditem->id, 'value' => 'abc'],
        ];
        // Process the feedback, there are two pages so the feedback will be unfinished yet.
        $result = mod_feedback_external::process_page($this->feedback->id, 0, $pagedata);
        $result = external_api::clean_returnvalue(mod_feedback_external::process_page_returns(), $result);
        $this->assertFalse($result['completed']);

        // Retrieve the unfinished responses.
        $result = mod_feedback_external::get_unfinished_responses($this->feedback->id);
        $result = external_api::clean_returnvalue(mod_feedback_external::get_unfinished_responses_returns(), $result);
        // Check that ids and responses match.
        foreach ($result['responses'] as $r) {
            if ($r['item'] == $numericitem->id) {
                $this->assertEquals(5, $r['value']);
            } else {
                $this->assertEquals($textfielditem->id, $r['item']);
                $this->assertEquals('abc', $r['value']);
            }
        }
    }

    /**
     * Test get_finished_responses.
     */
    public function test_get_finished_responses() {
        // Test user with full capabilities.
        $this->setUser($this->student);

        // Create a very simple feedback.
        $feedbackgenerator = $this->getDataGenerator()->get_plugin_generator('mod_feedback');
        $numericitem = $feedbackgenerator->create_item_numeric($this->feedback);
        $textfielditem = $feedbackgenerator->create_item_textfield($this->feedback);

        $pagedata = [
            ['name' => $numericitem->typ .'_'. $numericitem->id, 'value' => 5],
            ['name' => $textfielditem->typ .'_'. $textfielditem->id, 'value' => 'abc'],
        ];

        // Process the feedback, there is only one page so the feedback will be completed.
        $result = mod_feedback_external::process_page($this->feedback->id, 0, $pagedata);
        $result = external_api::clean_returnvalue(mod_feedback_external::process_page_returns(), $result);
        $this->assertTrue($result['completed']);

        // Retrieve the responses.
        $result = mod_feedback_external::get_finished_responses($this->feedback->id);
        $result = external_api::clean_returnvalue(mod_feedback_external::get_finished_responses_returns(), $result);
        // Check that ids and responses match.
        foreach ($result['responses'] as $r) {
            if ($r['item'] == $numericitem->id) {
                $this->assertEquals(5, $r['value']);
            } else {
                $this->assertEquals($textfielditem->id, $r['item']);
                $this->assertEquals('abc', $r['value']);
            }
        }
    }

    /**
     * Test get_non_respondents (student trying to get this information).
     */
    public function test_get_non_respondents_no_permissions() {
        $this->setUser($this->student);
        $this->expectException('moodle_exception');
        mod_feedback_external::get_non_respondents($this->feedback->id);
    }

    /**
     * Test get_non_respondents from an anonymous feedback.
     */
    public function test_get_non_respondents_from_anonymous_feedback() {
        $this->setUser($this->student);
        $this->expectException('moodle_exception');
        $this->expectExceptionMessage(get_string('anonymous', 'feedback'));
        mod_feedback_external::get_non_respondents($this->feedback->id);
    }

    /**
     * Test get_non_respondents.
     */
    public function test_get_non_respondents() {
        global $DB;

        // Force non anonymous.
        $DB->set_field('feedback', 'anonymous', FEEDBACK_ANONYMOUS_NO, array('id' => $this->feedback->id));

        // Create another student.
        $anotherstudent = self::getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($anotherstudent->id, $this->course->id, $this->studentrole->id, 'manual');
        $this->setUser($anotherstudent);

        // Test user with full capabilities.
        $this->setUser($this->student);

        // Create a very simple feedback.
        $feedbackgenerator = $this->getDataGenerator()->get_plugin_generator('mod_feedback');
        $numericitem = $feedbackgenerator->create_item_numeric($this->feedback);

        $pagedata = [
            ['name' => $numericitem->typ .'_'. $numericitem->id, 'value' => 5],
        ];

        // Process the feedback, there is only one page so the feedback will be completed.
        $result = mod_feedback_external::process_page($this->feedback->id, 0, $pagedata);
        $result = external_api::clean_returnvalue(mod_feedback_external::process_page_returns(), $result);
        $this->assertTrue($result['completed']);

        // Retrieve the non-respondent users.
        $this->setUser($this->teacher);
        $result = mod_feedback_external::get_non_respondents($this->feedback->id);
        $result = external_api::clean_returnvalue(mod_feedback_external::get_non_respondents_returns(), $result);
        $this->assertCount(0, $result['warnings']);
        $this->assertCount(1, $result['users']);
        $this->assertEquals($anotherstudent->id, $result['users'][0]['userid']);

        // Create another student.
        $anotherstudent2 = self::getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($anotherstudent2->id, $this->course->id, $this->studentrole->id, 'manual');
        $this->setUser($anotherstudent2);
        $this->setUser($this->teacher);
        $result = mod_feedback_external::get_non_respondents($this->feedback->id);
        $result = external_api::clean_returnvalue(mod_feedback_external::get_non_respondents_returns(), $result);
        $this->assertCount(0, $result['warnings']);
        $this->assertCount(2, $result['users']);

        // Test pagination.
        $result = mod_feedback_external::get_non_respondents($this->feedback->id, 0, 'lastaccess', 0, 1);
        $result = external_api::clean_returnvalue(mod_feedback_external::get_non_respondents_returns(), $result);
        $this->assertCount(0, $result['warnings']);
        $this->assertCount(1, $result['users']);
    }

    /**
     * Helper function that completes the feedback for two students.
     */
    protected function complete_basic_feedback() {
        global $DB;

        $generator = $this->getDataGenerator();
        // Create separated groups.
        $DB->set_field('course', 'groupmode', SEPARATEGROUPS);
        $DB->set_field('course', 'groupmodeforce', 1);
        assign_capability('moodle/site:accessallgroups', CAP_PROHIBIT, $this->teacherrole->id, $this->context);
        accesslib_clear_all_caches_for_unit_testing();

        $group1 = $generator->create_group(array('courseid' => $this->course->id));
        $group2 = $generator->create_group(array('courseid' => $this->course->id));

        // Create another students.
        $anotherstudent1 = self::getDataGenerator()->create_user();
        $anotherstudent2 = self::getDataGenerator()->create_user();
        $generator->enrol_user($anotherstudent1->id, $this->course->id, $this->studentrole->id, 'manual');
        $generator->enrol_user($anotherstudent2->id, $this->course->id, $this->studentrole->id, 'manual');

        $generator->create_group_member(array('groupid' => $group1->id, 'userid' => $this->student->id));
        $generator->create_group_member(array('groupid' => $group1->id, 'userid' => $this->teacher->id));
        $generator->create_group_member(array('groupid' => $group1->id, 'userid' => $anotherstudent1->id));
        $generator->create_group_member(array('groupid' => $group2->id, 'userid' => $anotherstudent2->id));

        // Test user with full capabilities.
        $this->setUser($this->student);

        // Create a very simple feedback.
        $feedbackgenerator = $generator->get_plugin_generator('mod_feedback');
        $numericitem = $feedbackgenerator->create_item_numeric($this->feedback);
        $textfielditem = $feedbackgenerator->create_item_textfield($this->feedback);

        $pagedata = [
            ['name' => $numericitem->typ .'_'. $numericitem->id, 'value' => 5],
            ['name' => $textfielditem->typ .'_'. $textfielditem->id, 'value' => 'abc'],
        ];

        // Process the feedback, there is only one page so the feedback will be completed.
        $result = mod_feedback_external::process_page($this->feedback->id, 0, $pagedata);
        $result = external_api::clean_returnvalue(mod_feedback_external::process_page_returns(), $result);
        $this->assertTrue($result['completed']);

        $this->setUser($anotherstudent1);

        $pagedata = [
            ['name' => $numericitem->typ .'_'. $numericitem->id, 'value' => 10],
            ['name' => $textfielditem->typ .'_'. $textfielditem->id, 'value' => 'def'],
        ];

        $result = mod_feedback_external::process_page($this->feedback->id, 0, $pagedata);
        $result = external_api::clean_returnvalue(mod_feedback_external::process_page_returns(), $result);
        $this->assertTrue($result['completed']);

        $this->setUser($anotherstudent2);

        $pagedata = [
            ['name' => $numericitem->typ .'_'. $numericitem->id, 'value' => 10],
            ['name' => $textfielditem->typ .'_'. $textfielditem->id, 'value' => 'def'],
        ];

        $result = mod_feedback_external::process_page($this->feedback->id, 0, $pagedata);
        $result = external_api::clean_returnvalue(mod_feedback_external::process_page_returns(), $result);
        $this->assertTrue($result['completed']);
    }

    /**
     * Test get_responses_analysis for anonymous feedback.
     */
    public function test_get_responses_analysis_anonymous() {
        self::complete_basic_feedback();

        // Retrieve the responses analysis.
        $this->setUser($this->teacher);
        $result = mod_feedback_external::get_responses_analysis($this->feedback->id);
        $result = external_api::clean_returnvalue(mod_feedback_external::get_responses_analysis_returns(), $result);
        $this->assertCount(0, $result['warnings']);
        $this->assertEquals(0, $result['totalattempts']);
        $this->assertEquals(2, $result['totalanonattempts']);   // Only see my groups.

        foreach ($result['attempts'] as $attempt) {
            $this->assertEmpty($attempt['userid']); // Is anonymous.
        }
    }

    /**
     * Test get_responses_analysis for non-anonymous feedback.
     */
    public function test_get_responses_analysis_non_anonymous() {
        global $DB;

        // Force non anonymous.
        $DB->set_field('feedback', 'anonymous', FEEDBACK_ANONYMOUS_NO, array('id' => $this->feedback->id));

        self::complete_basic_feedback();
        // Retrieve the responses analysis.
        $this->setUser($this->teacher);
        $result = mod_feedback_external::get_responses_analysis($this->feedback->id);
        $result = external_api::clean_returnvalue(mod_feedback_external::get_responses_analysis_returns(), $result);
        $this->assertCount(0, $result['warnings']);
        $this->assertEquals(2, $result['totalattempts']);
        $this->assertEquals(0, $result['totalanonattempts']);   // Only see my groups.

        foreach ($result['attempts'] as $attempt) {
            $this->assertNotEmpty($attempt['userid']);  // Is not anonymous.
        }
    }

    /**
     * Test get_last_completed for feedback anonymous not completed.
     */
    public function test_get_last_completed_anonymous_not_completed() {
        global $DB;

        // Force anonymous.
        $DB->set_field('feedback', 'anonymous', FEEDBACK_ANONYMOUS_YES, array('id' => $this->feedback->id));

        // Test user with full capabilities that didn't complete the feedback.
        $this->setUser($this->student);

        $this->expectExceptionMessage(get_string('anonymous', 'feedback'));
        $this->expectException('moodle_exception');
        mod_feedback_external::get_last_completed($this->feedback->id);
    }

    /**
     * Test get_last_completed for feedback anonymous and completed.
     */
    public function test_get_last_completed_anonymous_completed() {
        global $DB;

        // Force anonymous.
        $DB->set_field('feedback', 'anonymous', FEEDBACK_ANONYMOUS_YES, array('id' => $this->feedback->id));
        // Add one completion record..
        $record = [
            'feedback' => $this->feedback->id,
            'userid' => $this->student->id,
            'timemodified' => time() - DAYSECS,
            'random_response' => 0,
            'anonymous_response' => FEEDBACK_ANONYMOUS_YES,
            'courseid' => $this->course->id,
        ];
        $record['id'] = $DB->insert_record('feedback_completed', (object) $record);

        // Test user with full capabilities.
        $this->setUser($this->student);

        $this->expectExceptionMessage(get_string('anonymous', 'feedback'));
        $this->expectException('moodle_exception');
        mod_feedback_external::get_last_completed($this->feedback->id);
    }

    /**
     * Test get_last_completed for feedback not anonymous and completed.
     */
    public function test_get_last_completed_not_anonymous_completed() {
        global $DB;

        // Force non anonymous.
        $DB->set_field('feedback', 'anonymous', FEEDBACK_ANONYMOUS_NO, array('id' => $this->feedback->id));
        // Add one completion record..
        $record = [
            'feedback' => $this->feedback->id,
            'userid' => $this->student->id,
            'timemodified' => time() - DAYSECS,
            'random_response' => 0,
            'anonymous_response' => FEEDBACK_ANONYMOUS_NO,
            'courseid' => $this->course->id,
        ];
        $record['id'] = $DB->insert_record('feedback_completed', (object) $record);

        // Test user with full capabilities.
        $this->setUser($this->student);
        $result = mod_feedback_external::get_last_completed($this->feedback->id);
        $result = external_api::clean_returnvalue(mod_feedback_external::get_last_completed_returns(), $result);
        $this->assertEquals($record, $result['completed']);
    }

    /**
     * Test get_last_completed for feedback not anonymous and not completed.
     */
    public function test_get_last_completed_not_anonymous_not_completed() {
        global $DB;

        // Force anonymous.
        $DB->set_field('feedback', 'anonymous', FEEDBACK_ANONYMOUS_NO, array('id' => $this->feedback->id));

        // Test user with full capabilities that didn't complete the feedback.
        $this->setUser($this->student);

        $this->expectExceptionMessage(get_string('not_completed_yet', 'feedback'));
        $this->expectException('moodle_exception');
        mod_feedback_external::get_last_completed($this->feedback->id);
    }

    /**
     * Test get_feedback_access_information for site feedback.
     */
    public function test_get_feedback_access_information_for_site_feedback() {

        $sitefeedback = $this->getDataGenerator()->create_module('feedback', array('course' => SITEID));
        $this->setUser($this->student);
        // Access the site feedback via the site activity.
        $result = mod_feedback_external::get_feedback_access_information($sitefeedback->id);
        $result = external_api::clean_returnvalue(mod_feedback_external::get_feedback_access_information_returns(), $result);
        $this->assertTrue($result['cancomplete']);
        $this->assertTrue($result['cansubmit']);

        // Access the site feedback via course where I'm enrolled.
        $result = mod_feedback_external::get_feedback_access_information($sitefeedback->id, $this->course->id);
        $result = external_api::clean_returnvalue(mod_feedback_external::get_feedback_access_information_returns(), $result);
        $this->assertTrue($result['cancomplete']);
        $this->assertTrue($result['cansubmit']);

        // Access the site feedback via course where I'm not enrolled.
        $othercourse = $this->getDataGenerator()->create_course();

        $this->expectException('moodle_exception');
        mod_feedback_external::get_feedback_access_information($sitefeedback->id, $othercourse->id);
    }

    /**
     * Test get_feedback_access_information for site feedback mapped.
     */
    public function test_get_feedback_access_information_for_site_feedback_mapped() {
        global $DB;

        $sitefeedback = $this->getDataGenerator()->create_module('feedback', array('course' => SITEID));
        $this->setUser($this->student);
        $DB->insert_record('feedback_sitecourse_map', array('feedbackid' => $sitefeedback->id, 'courseid' => $this->course->id));

        // Access the site feedback via course where I'm enrolled and mapped.
        $result = mod_feedback_external::get_feedback_access_information($sitefeedback->id, $this->course->id);
        $result = external_api::clean_returnvalue(mod_feedback_external::get_feedback_access_information_returns(), $result);
        $this->assertTrue($result['cancomplete']);
        $this->assertTrue($result['cansubmit']);

        // Access the site feedback via course where I'm enrolled but not mapped.
        $othercourse = $this->getDataGenerator()->create_course();
        $this->getDataGenerator()->enrol_user($this->student->id, $othercourse->id, $this->studentrole->id, 'manual');

        $this->expectException('moodle_exception');
        $this->expectExceptionMessage(get_string('cannotaccess', 'mod_feedback'));
        mod_feedback_external::get_feedback_access_information($sitefeedback->id, $othercourse->id);
    }
}
