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

namespace qbank_tagquestion\external;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/webservice/tests/helpers.php');

/**
 * Question external functions tests.
 *
 * @package    qbank_tagquestion
 * @copyright  2016 Pau Ferrer <pau@moodle.com>
 * @author     2021 Safat Shahin <safatshahin@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class submit_tags_test extends \externallib_advanced_testcase {

    /**
     * Set up for every test
     */
    public function setUp(): void {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();

        // Setup test data.
        $this->course = $this->getDataGenerator()->create_course();

        // Create users.
        $this->student = self::getDataGenerator()->create_user();

        // Users enrolments.
        $this->studentrole = $DB->get_record('role', ['shortname' => 'student']);
        $this->getDataGenerator()->enrol_user($this->student->id, $this->course->id, $this->studentrole->id, 'manual');
    }

    /**
     * submit_tags_form should throw an exception when the question id doesn't match
     * a question.
     */
    public function test_submit_tags_form_incorrect_question_id() {
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        list ($category, $course, $qcat, $questions) = $questiongenerator->setup_course_and_questions();
        $questioncontext = \context::instance_by_id($qcat->contextid);
        $editingcontext = $questioncontext;
        $question = $questions[0];
        // Generate an id for a question that doesn't exist.
        $missingquestionid = $questions[1]->id * 2;
        $question->id = $missingquestionid;
        $formdata = $this->generate_encoded_submit_tags_form_string($question, $qcat, $questioncontext, [], []);

        // We should receive an exception if the question doesn't exist.
        $this->expectException('moodle_exception');
        submit_tags::execute($missingquestionid, $editingcontext->id, $formdata);
    }

    /**
     * submit_tags_form should throw an exception when the context id doesn't match
     * a context.
     */
    public function test_submit_tags_form_incorrect_context_id() {
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        list ($category, $course, $qcat, $questions) = $questiongenerator->setup_course_and_questions();
        $questioncontext = \context::instance_by_id($qcat->contextid);
        $editingcontext = $questioncontext;
        $question = $questions[0];
        // Generate an id for a context that doesn't exist.
        $missingcontextid = $editingcontext->id * 200;
        $formdata = $this->generate_encoded_submit_tags_form_string($question, $qcat, $questioncontext, [], []);

        // We should receive an exception if the question doesn't exist.
        $this->expectException('moodle_exception');
        submit_tags::execute($question->id, $missingcontextid, $formdata);
    }

    /**
     * submit_tags_form should return false when tags are disabled.
     */
    public function test_submit_tags_form_tags_disabled() {
        global $CFG;

        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        list ($category, $course, $qcat, $questions) = $questiongenerator->setup_course_and_questions();
        $questioncontext = \context::instance_by_id($qcat->contextid);
        $editingcontext = $questioncontext;
        $question = $questions[0];
        $user = $this->create_user_can_tag($course);
        $formdata = $this->generate_encoded_submit_tags_form_string($question, $qcat, $questioncontext, [], []);

        $this->setUser($user);
        $CFG->usetags = false;
        $result = submit_tags::execute($question->id, $editingcontext->id, $formdata);
        $CFG->usetags = true;

        $this->assertFalse($result['status']);
    }

    /**
     * submit_tags_form should return false if the user does not have any capability
     * to tag the question.
     */
    public function test_submit_tags_form_no_tag_permissions() {
        global $DB;

        $generator = $this->getDataGenerator();
        $user = $generator->create_user();
        $teacherrole = $DB->get_record('role', ['shortname' => 'editingteacher']);
        $questiongenerator = $generator->get_plugin_generator('core_question');
        list ($category, $course, $qcat, $questions) = $questiongenerator->setup_course_and_questions();
        $questioncontext = \context::instance_by_id($qcat->contextid);
        $editingcontext = $questioncontext;
        $question = $questions[0];
        $formdata = $this->generate_encoded_submit_tags_form_string(
                $question,
                $qcat,
                $questioncontext,
                ['foo'],
                ['bar']
        );

        // Prohibit all of the tag capabilities.
        assign_capability('moodle/question:tagmine', CAP_PROHIBIT, $teacherrole->id, $questioncontext->id);
        assign_capability('moodle/question:tagall', CAP_PROHIBIT, $teacherrole->id, $questioncontext->id);

        $generator->enrol_user($user->id, $course->id, $teacherrole->id, 'manual');
        $user->ignoresesskey = true;
        $this->setUser($user);

        $result = submit_tags::execute($question->id, $editingcontext->id, $formdata);

        $this->assertFalse($result['status']);
    }

    /**
     * submit_tags_form should return false if the user only has the capability to
     * tag their own questions and the question is not theirs.
     */
    public function test_submit_tags_form_tagmine_permission_non_owner_question() {
        global $DB;

        $generator = $this->getDataGenerator();
        $user = $generator->create_user();
        $teacherrole = $DB->get_record('role', ['shortname' => 'editingteacher']);
        $questiongenerator = $generator->get_plugin_generator('core_question');
        list ($category, $course, $qcat, $questions) = $questiongenerator->setup_course_and_questions();
        $questioncontext = \context::instance_by_id($qcat->contextid);
        $editingcontext = $questioncontext;
        $question = $questions[0];
        $formdata = $this->generate_encoded_submit_tags_form_string(
                $question,
                $qcat,
                $questioncontext,
                ['foo'],
                ['bar']
        );

        // Make sure the question isn't created by the user.
        $question->createdby = $user->id + 1;

        // Prohibit all of the tag capabilities.
        assign_capability('moodle/question:tagmine', CAP_ALLOW, $teacherrole->id, $questioncontext->id);
        assign_capability('moodle/question:tagall', CAP_PROHIBIT, $teacherrole->id, $questioncontext->id);

        $generator->enrol_user($user->id, $course->id, $teacherrole->id, 'manual');
        $user->ignoresesskey = true;
        $this->setUser($user);

        $result = submit_tags::execute($question->id, $editingcontext->id, $formdata);

        $this->assertFalse($result['status']);
    }

    /**
     * Data provided for the submit_tags_form test to check that course tags are
     * only created in the correct editing and question context combinations.
     *
     * @return array Test cases
     */
    public function get_submit_tags_form_testcases() {
        return [
                'course - course' => [
                        'editingcontext' => 'course',
                        'questioncontext' => 'course',
                        'questiontags' => ['foo'],
                        'coursetags' => ['bar'],
                        'expectcoursetags' => false
                ],
                'course - course - empty tags' => [
                        'editingcontext' => 'course',
                        'questioncontext' => 'course',
                        'questiontags' => [],
                        'coursetags' => ['bar'],
                        'expectcoursetags' => false
                ],
                'course - course category' => [
                        'editingcontext' => 'course',
                        'questioncontext' => 'category',
                        'questiontags' => ['foo'],
                        'coursetags' => ['bar'],
                        'expectcoursetags' => true
                ],
                'course - system' => [
                        'editingcontext' => 'course',
                        'questioncontext' => 'system',
                        'questiontags' => ['foo'],
                        'coursetags' => ['bar'],
                        'expectcoursetags' => true
                ],
                'course category - course' => [
                        'editingcontext' => 'category',
                        'questioncontext' => 'course',
                        'questiontags' => ['foo'],
                        'coursetags' => ['bar'],
                        'expectcoursetags' => false
                ],
                'course category - course category' => [
                        'editingcontext' => 'category',
                        'questioncontext' => 'category',
                        'questiontags' => ['foo'],
                        'coursetags' => ['bar'],
                        'expectcoursetags' => false
                ],
                'course category - system' => [
                        'editingcontext' => 'category',
                        'questioncontext' => 'system',
                        'questiontags' => ['foo'],
                        'coursetags' => ['bar'],
                        'expectcoursetags' => false
                ],
                'system - course' => [
                        'editingcontext' => 'system',
                        'questioncontext' => 'course',
                        'questiontags' => ['foo'],
                        'coursetags' => ['bar'],
                        'expectcoursetags' => false
                ],
                'system - course category' => [
                        'editingcontext' => 'system',
                        'questioncontext' => 'category',
                        'questiontags' => ['foo'],
                        'coursetags' => ['bar'],
                        'expectcoursetags' => false
                ],
                'system - system' => [
                        'editingcontext' => 'system',
                        'questioncontext' => 'system',
                        'questiontags' => ['foo'],
                        'coursetags' => ['bar'],
                        'expectcoursetags' => false
                ],
        ];
    }

    /**
     * Tests that submit_tags_form only creates course tags when the correct combination
     * of editing context and question context is provided.
     *
     * Course tags can only be set on a course category or system context question that
     * is being editing in a course context.
     *
     * @dataProvider get_submit_tags_form_testcases()
     * @param string $editingcontext The type of the context the question is being edited in
     * @param string $questioncontext The type of the context the question belongs to
     * @param string[] $questiontags The tag names to set as question tags
     * @param string[] $coursetags The tag names to set as course tags
     * @param bool $expectcoursetags If the given course tags should have been set or not
     */
    public function test_submit_tags_form_context_combinations(
            $editingcontext,
            $questioncontext,
            $questiontags,
            $coursetags,
            $expectcoursetags
    ) {
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        list ($category, $course, $qcat, $questions) = $questiongenerator->setup_course_and_questions($questioncontext);
        $coursecontext = \context_course::instance($course->id);
        $questioncontext = \context::instance_by_id($qcat->contextid);

        switch($editingcontext) {
            case 'system':
                $editingcontext = \context_system::instance();
                break;

            case 'category':
                $editingcontext = \context_coursecat::instance($category->id);
                break;

            default:
                $editingcontext = \context_course::instance($course->id);
        }

        $user = $this->create_user_can_tag($course);
        $question = $questions[0];
        $formdata = $this->generate_encoded_submit_tags_form_string(
                $question,
                $qcat,
                $questioncontext,
                $questiontags, // Question tags.
                $coursetags // Course tags.
        );

        $this->setUser($user);

        $result = submit_tags::execute($question->id, $editingcontext->id, $formdata);

        $this->assertTrue($result['status']);

        $tagobjects = \core_tag_tag::get_item_tags('core_question', 'question', $question->id);
        $coursetagobjects = [];
        $questiontagobjects = [];

        if ($expectcoursetags) {
            // If the use case is expecting course tags to be created then split
            // the tags into course tags and question tags and ensure we have
            // the correct number of course tags.

            while ($tagobject = array_shift($tagobjects)) {
                if ($tagobject->taginstancecontextid == $questioncontext->id) {
                    $questiontagobjects[] = $tagobject;
                } else if ($tagobject->taginstancecontextid == $coursecontext->id) {
                    $coursetagobjects[] = $tagobject;
                }
            }

            $this->assertCount(count($coursetags), $coursetagobjects);
        } else {
            $questiontagobjects = $tagobjects;
        }

        // Ensure the expected number of question tags was created.
        $this->assertCount(count($questiontags), $questiontagobjects);

        foreach ($questiontagobjects as $tagobject) {
            // If we have any question tags then make sure they are in the list
            // of expected tags and have the correct context.
            $this->assertContains($tagobject->name, $questiontags);
            $this->assertEquals($questioncontext->id, $tagobject->taginstancecontextid);
        }

        foreach ($coursetagobjects as $tagobject) {
            // If we have any course tags then make sure they are in the list
            // of expected course tags and have the correct context.
            $this->assertContains($tagobject->name, $coursetags);
            $this->assertEquals($coursecontext->id, $tagobject->taginstancecontextid);
        }
    }

    /**
     * Build the encoded form data expected by the submit_tags_form external function.
     *
     * @param  \stdClass $question         The question record
     * @param  \stdClass $questioncategory The question category record
     * @param  \context  $questioncontext  Context for the question category
     * @param  array  $tags               A list of tag names for the question
     * @param  array  $coursetags         A list of course tag names for the question
     * @return string                    HTML encoded string of the data
     */
    protected function generate_encoded_submit_tags_form_string($question, $questioncategory,
            $questioncontext, $tags = [], $coursetags = []) {

        $data = [
                'id' => $question->id,
                'categoryid' => $questioncategory->id,
                'contextid' => $questioncontext->id,
                'questionname' => $question->name,
                'questioncategory' => $questioncategory->name,
                'context' => $questioncontext->get_context_name(false),
                'tags' => $tags,
                'coursetags' => $coursetags
        ];
        $data = \qbank_tagquestion\form\tags_form::mock_generate_submit_keys($data);

        return http_build_query($data, '', '&');
    }

    /**
     * Create a user, enrol them in the course, and give them the capability to
     * tag all questions in the system context.
     *
     * @param  \stdClass $course The course record to enrol in
     * @return \stdClass         The user record
     */
    protected function create_user_can_tag($course) {
        global $DB;

        $generator = $this->getDataGenerator();
        $user = $generator->create_user();
        $roleid = $generator->create_role();
        $teacherrole = $DB->get_record('role', ['shortname' => 'editingteacher']);
        $systemcontext = \context_system::instance();

        $generator->role_assign($roleid, $user->id, $systemcontext->id);
        $generator->enrol_user($user->id, $course->id, $teacherrole->id, 'manual');

        // Give the user global ability to tag questions.
        assign_capability('moodle/question:tagall', CAP_ALLOW, $roleid, $systemcontext, true);
        // Allow the user to submit form data.
        $user->ignoresesskey = true;

        return $user;
    }

}
