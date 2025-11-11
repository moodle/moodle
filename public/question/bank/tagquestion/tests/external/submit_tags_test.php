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

/**
 * Question external functions tests.
 *
 * @package    qbank_tagquestion
 * @copyright  2016 Pau Ferrer <pau@moodle.com>
 * @author     2021 Safat Shahin <safatshahin@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class submit_tags_test extends \core_external\tests\externallib_testcase {
    /** @var \stdClass course record. */
    protected $course;

    /** @var \stdClass user record. */
    protected $student;

    /** @var \stdClass user role record. */
    protected $studentrole;

    /**
     * Set up for every test
     */
    public function setUp(): void {
        global $DB;
        parent::setUp();
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
    public function test_submit_tags_form_incorrect_question_id(): void {
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
    public function test_submit_tags_form_incorrect_context_id(): void {
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
    public function test_submit_tags_form_tags_disabled(): void {
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
    public function test_submit_tags_form_no_tag_permissions(): void {
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
    public function test_submit_tags_form_tagmine_permission_non_owner_question(): void {
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

        return http_build_query($data);
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
