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
 * Provides the {@link mod_workshop_privacy_provider_testcase} class.
 *
 * @package     mod_workshop
 * @category    test
 * @copyright   2018 David Mudrák <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

use core_privacy\local\request\writer;

/**
 * Unit tests for the privacy API implementation.
 *
 * @copyright 2018 David Mudrák <david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_workshop_privacy_provider_testcase extends advanced_testcase {

    /** @var testing_data_generator */
    protected $generator;

    /** @var mod_workshop_generator */
    protected $workshopgenerator;

    /** @var stdClass */
    protected $course1;

    /** @var stdClass */
    protected $course2;

    /** @var stdClass */
    protected $student1;

    /** @var stdClass */
    protected $student2;

    /** @var stdClass */
    protected $student3;

    /** @var stdClass */
    protected $teacher4;

    /** @var stdClass first workshop in course1 */
    protected $workshop11;

    /** @var stdClass second workshop in course1 */
    protected $workshop12;

    /** @var stdClass first workshop in course2 */
    protected $workshop21;

    /** @var int ID of the submission in workshop11 by student1 */
    protected $submission111;

    /** @var int ID of the submission in workshop12 by student1 */
    protected $submission121;

    /** @var int ID of the submission in workshop12 by student2 */
    protected $submission122;

    /** @var int ID of the submission in workshop21 by student2 */
    protected $submission212;

    /** @var int ID of the assessment of submission111 by student1 */
    protected $assessment1111;

    /** @var int ID of the assessment of submission111 by student2 */
    protected $assessment1112;

    /** @var int ID of the assessment of submission111 by student3 */
    protected $assessment1113;

    /** @var int ID of the assessment of submission121 by student2 */
    protected $assessment1212;

    /** @var int ID of the assessment of submission212 by student1 */
    protected $assessment2121;

    /**
     * Set up the test environment.
     *
     * course1
     *  |
     *  +--workshop11 (first digit matches the course, second is incremental)
     *  |   |
     *  |   +--submission111 (first two digits match the workshop, last one matches the author)
     *  |       |
     *  |       +--assessment1111 (first three digits match the submission, last one matches the reviewer)
     *  |       +--assessment1112
     *  |       +--assessment1113
     *  |
     *  +--workshop12
     *      |
     *      +--submission121
     *      |   |
     *      |   +--assessment1212
     *      |
     *      +--submission122
     *
     *  etc.
     */
    protected function setUp(): void {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();

        $this->generator = $this->getDataGenerator();
        $this->workshopgenerator = $this->generator->get_plugin_generator('mod_workshop');

        $this->course1 = $this->generator->create_course();
        $this->course2 = $this->generator->create_course();

        $this->workshop11 = $this->generator->create_module('workshop', [
            'course' => $this->course1,
            'name' => 'Workshop11',
        ]);
        $DB->set_field('workshop', 'phase', 50, ['id' => $this->workshop11->id]);

        $this->workshop12 = $this->generator->create_module('workshop', ['course' => $this->course1]);
        $this->workshop21 = $this->generator->create_module('workshop', ['course' => $this->course2]);

        $this->student1 = $this->generator->create_user();
        $this->student2 = $this->generator->create_user();
        $this->student3 = $this->generator->create_user();
        $this->teacher4 = $this->generator->create_user();

        $this->submission111 = $this->workshopgenerator->create_submission($this->workshop11->id, $this->student1->id);
        $this->submission121 = $this->workshopgenerator->create_submission($this->workshop12->id, $this->student1->id,
            ['gradeoverby' => $this->teacher4->id]);
        $this->submission122 = $this->workshopgenerator->create_submission($this->workshop12->id, $this->student2->id);
        $this->submission212 = $this->workshopgenerator->create_submission($this->workshop21->id, $this->student2->id);

        $this->assessment1111 = $this->workshopgenerator->create_assessment($this->submission111, $this->student1->id, [
            'grade' => null,
        ]);
        $this->assessment1112 = $this->workshopgenerator->create_assessment($this->submission111, $this->student2->id, [
            'grade' => 92,
        ]);
        $this->assessment1113 = $this->workshopgenerator->create_assessment($this->submission111, $this->student3->id);

        $this->assessment1212 = $this->workshopgenerator->create_assessment($this->submission121, $this->student2->id, [
            'feedbackauthor' => 'This is what student 2 thinks about submission 121',
            'feedbackreviewer' => 'This is what the teacher thinks about this assessment',
        ]);

        $this->assessment2121 = $this->workshopgenerator->create_assessment($this->submission212, $this->student1->id, [
            'grade' => 68,
            'gradinggradeover' => 80,
            'gradinggradeoverby' => $this->teacher4->id,
            'feedbackauthor' => 'This is what student 1 thinks about submission 212',
            'feedbackreviewer' => 'This is what the teacher thinks about this assessment',
        ]);
    }

    /**
     * Test {@link \mod_workshop\privacy\provider::get_contexts_for_userid()} implementation.
     */
    public function test_get_contexts_for_userid() {

        $cm11 = get_coursemodule_from_instance('workshop', $this->workshop11->id);
        $cm12 = get_coursemodule_from_instance('workshop', $this->workshop12->id);
        $cm21 = get_coursemodule_from_instance('workshop', $this->workshop21->id);

        $context11 = context_module::instance($cm11->id);
        $context12 = context_module::instance($cm12->id);
        $context21 = context_module::instance($cm21->id);

        // Student1 has data in workshop11 (author + self reviewer), workshop12 (author) and workshop21 (reviewer).
        $contextlist = \mod_workshop\privacy\provider::get_contexts_for_userid($this->student1->id);
        $this->assertInstanceOf(\core_privacy\local\request\contextlist::class, $contextlist);
        $this->assertEqualsCanonicalizing([$context11->id, $context12->id, $context21->id], $contextlist->get_contextids());

        // Student2 has data in workshop11 (reviewer), workshop12 (reviewer) and workshop21 (author).
        $contextlist = \mod_workshop\privacy\provider::get_contexts_for_userid($this->student2->id);
        $this->assertEqualsCanonicalizing([$context11->id, $context12->id, $context21->id], $contextlist->get_contextids());

        // Student3 has data in workshop11 (reviewer).
        $contextlist = \mod_workshop\privacy\provider::get_contexts_for_userid($this->student3->id);
        $this->assertEqualsCanonicalizing([$context11->id], $contextlist->get_contextids());

        // Teacher4 has data in workshop12 (gradeoverby) and workshop21 (gradinggradeoverby).
        $contextlist = \mod_workshop\privacy\provider::get_contexts_for_userid($this->teacher4->id);
        $this->assertEqualsCanonicalizing([$context21->id, $context12->id], $contextlist->get_contextids());
    }

    /**
     * Test {@link \mod_workshop\privacy\provider::get_users_in_context()} implementation.
     */
    public function test_get_users_in_context() {

        $cm11 = get_coursemodule_from_instance('workshop', $this->workshop11->id);
        $cm12 = get_coursemodule_from_instance('workshop', $this->workshop12->id);
        $cm21 = get_coursemodule_from_instance('workshop', $this->workshop21->id);

        $context11 = context_module::instance($cm11->id);
        $context12 = context_module::instance($cm12->id);
        $context21 = context_module::instance($cm21->id);

        // Users in the workshop11.
        $userlist11 = new \core_privacy\local\request\userlist($context11, 'mod_workshop');
        \mod_workshop\privacy\provider::get_users_in_context($userlist11);
        $expected11 = [
            $this->student1->id, // Student1 has data in workshop11 (author + self reviewer).
            $this->student2->id, // Student2 has data in workshop11 (reviewer).
            $this->student3->id, // Student3 has data in workshop11 (reviewer).
        ];
        $actual11 = $userlist11->get_userids();
        $this->assertEqualsCanonicalizing($expected11, $actual11);

        // Users in the workshop12.
        $userlist12 = new \core_privacy\local\request\userlist($context12, 'mod_workshop');
        \mod_workshop\privacy\provider::get_users_in_context($userlist12);
        $expected12 = [
            $this->student1->id, // Student1 has data in workshop12 (author).
            $this->student2->id, // Student2 has data in workshop12 (reviewer).
            $this->teacher4->id, // Teacher4 has data in workshop12 (gradeoverby).
        ];
        $actual12 = $userlist12->get_userids();
        $this->assertEqualsCanonicalizing($expected12, $actual12);

        // Users in the workshop21.
        $userlist21 = new \core_privacy\local\request\userlist($context21, 'mod_workshop');
        \mod_workshop\privacy\provider::get_users_in_context($userlist21);
        $expected21 = [
            $this->student1->id, // Student1 has data in workshop21 (reviewer).
            $this->student2->id, // Student2 has data in workshop21 (author).
            $this->teacher4->id, // Teacher4 has data in workshop21 (gradinggradeoverby).
        ];
        $actual21 = $userlist21->get_userids();
        $this->assertEqualsCanonicalizing($expected21, $actual21);
    }

    /**
     * Test {@link \mod_workshop\privacy\provider::export_user_data()} implementation.
     */
    public function test_export_user_data_1() {

        $contextlist = new \core_privacy\local\request\approved_contextlist($this->student1, 'mod_workshop', [
            \context_module::instance($this->workshop11->cmid)->id,
            \context_module::instance($this->workshop12->cmid)->id,
        ]);

        \mod_workshop\privacy\provider::export_user_data($contextlist);

        $writer = writer::with_context(\context_module::instance($this->workshop11->cmid));

        $workshop = $writer->get_data([]);
        $this->assertEquals('Workshop11', $workshop->name);
        $this->assertObjectHasAttribute('phase', $workshop);

        $mysubmission = $writer->get_data([
            get_string('mysubmission', 'mod_workshop'),
        ]);

        $mysubmissionselfassessmentwithoutgrade = $writer->get_data([
            get_string('mysubmission', 'mod_workshop'),
            get_string('assessments', 'mod_workshop'),
            $this->assessment1111,
        ]);
        $this->assertNull($mysubmissionselfassessmentwithoutgrade->grade);
        $this->assertEquals(get_string('yes'), $mysubmissionselfassessmentwithoutgrade->selfassessment);

        $mysubmissionassessmentwithgrade = $writer->get_data([
            get_string('mysubmission', 'mod_workshop'),
            get_string('assessments', 'mod_workshop'),
            $this->assessment1112,
        ]);
        $this->assertEquals(92, $mysubmissionassessmentwithgrade->grade);
        $this->assertEquals(get_string('no'), $mysubmissionassessmentwithgrade->selfassessment);

        $mysubmissionassessmentwithoutgrade = $writer->get_data([
            get_string('mysubmission', 'mod_workshop'),
            get_string('assessments', 'mod_workshop'),
            $this->assessment1113,
        ]);
        $this->assertEquals(null, $mysubmissionassessmentwithoutgrade->grade);
        $this->assertEquals(get_string('no'), $mysubmissionassessmentwithoutgrade->selfassessment);

        $myassessments = $writer->get_data([
            get_string('myassessments', 'mod_workshop'),
        ]);
        $this->assertEmpty($myassessments);
    }

    /**
     * Test {@link \mod_workshop\privacy\provider::export_user_data()} implementation.
     */
    public function test_export_user_data_2() {

        $contextlist = new \core_privacy\local\request\approved_contextlist($this->student2, 'mod_workshop', [
            \context_module::instance($this->workshop11->cmid)->id,
        ]);

        \mod_workshop\privacy\provider::export_user_data($contextlist);

        $writer = writer::with_context(\context_module::instance($this->workshop11->cmid));

        $assessedsubmission = $writer->get_related_data([
            get_string('myassessments', 'mod_workshop'),
            $this->assessment1112,
        ], 'submission');
        $this->assertEquals(get_string('no'), $assessedsubmission->myownsubmission);
    }

    /**
     * Test {@link \mod_workshop\privacy\provider::delete_data_for_all_users_in_context()} implementation.
     */
    public function test_delete_data_for_all_users_in_context() {
        global $DB;

        $this->assertTrue($DB->record_exists('workshop_submissions', ['workshopid' => $this->workshop11->id]));

        // Passing a non-module context does nothing.
        \mod_workshop\privacy\provider::delete_data_for_all_users_in_context(\context_course::instance($this->course1->id));
        $this->assertTrue($DB->record_exists('workshop_submissions', ['workshopid' => $this->workshop11->id]));

        // Passing a workshop context removes all data.
        \mod_workshop\privacy\provider::delete_data_for_all_users_in_context(\context_module::instance($this->workshop11->cmid));
        $this->assertFalse($DB->record_exists('workshop_submissions', ['workshopid' => $this->workshop11->id]));
    }

    /**
     * Test {@link \mod_workshop\privacy\provider::delete_data_for_user()} implementation.
     */
    public function test_delete_data_for_user() {
        global $DB;

        $student1submissions = $DB->get_records('workshop_submissions', [
            'workshopid' => $this->workshop12->id,
            'authorid' => $this->student1->id,
        ]);

        $student2submissions = $DB->get_records('workshop_submissions', [
            'workshopid' => $this->workshop12->id,
            'authorid' => $this->student2->id,
        ]);

        $this->assertNotEmpty($student1submissions);
        $this->assertNotEmpty($student2submissions);

        foreach ($student1submissions as $submission) {
            $this->assertNotEquals(get_string('privacy:request:delete:title', 'mod_workshop'), $submission->title);
        }

        foreach ($student2submissions as $submission) {
            $this->assertNotEquals(get_string('privacy:request:delete:title', 'mod_workshop'), $submission->title);
        }

        $contextlist = new \core_privacy\local\request\approved_contextlist($this->student1, 'mod_workshop', [
            \context_module::instance($this->workshop12->cmid)->id,
            \context_module::instance($this->workshop21->cmid)->id,
        ]);

        \mod_workshop\privacy\provider::delete_data_for_user($contextlist);

        $student1submissions = $DB->get_records('workshop_submissions', [
            'workshopid' => $this->workshop12->id,
            'authorid' => $this->student1->id,
        ]);

        $student2submissions = $DB->get_records('workshop_submissions', [
            'workshopid' => $this->workshop12->id,
            'authorid' => $this->student2->id,
        ]);

        $this->assertNotEmpty($student1submissions);
        $this->assertNotEmpty($student2submissions);

        foreach ($student1submissions as $submission) {
            $this->assertEquals(get_string('privacy:request:delete:title', 'mod_workshop'), $submission->title);
        }

        foreach ($student2submissions as $submission) {
            $this->assertNotEquals(get_string('privacy:request:delete:title', 'mod_workshop'), $submission->title);
        }

        $student1assessments = $DB->get_records('workshop_assessments', [
            'submissionid' => $this->submission212,
            'reviewerid' => $this->student1->id,
        ]);
        $this->assertNotEmpty($student1assessments);

        foreach ($student1assessments as $assessment) {
            // In Moodle, feedback is seen to belong to the recipient user.
            $this->assertNotEquals(get_string('privacy:request:delete:content', 'mod_workshop'), $assessment->feedbackauthor);
            $this->assertEquals(get_string('privacy:request:delete:content', 'mod_workshop'), $assessment->feedbackreviewer);
            // We delete what we can without affecting others' grades.
            $this->assertEquals(68, $assessment->grade);
        }

        $assessments = $DB->get_records_list('workshop_assessments', 'submissionid', array_keys($student1submissions));
        $this->assertNotEmpty($assessments);

        foreach ($assessments as $assessment) {
            if ($assessment->reviewerid == $this->student1->id) {
                $this->assertNotEquals(get_string('privacy:request:delete:content', 'mod_workshop'), $assessment->feedbackauthor);
                $this->assertNotEquals(get_string('privacy:request:delete:content', 'mod_workshop'), $assessment->feedbackreviewer);

            } else {
                $this->assertEquals(get_string('privacy:request:delete:content', 'mod_workshop'), $assessment->feedbackauthor);
                $this->assertNotEquals(get_string('privacy:request:delete:content', 'mod_workshop'), $assessment->feedbackreviewer);
            }
        }
    }

    /**
     * Test {@link \mod_workshop\privacy\provider::delete_data_for_users()} implementation.
     */
    public function test_delete_data_for_users() {
        global $DB;

        // Student1 has submissions in two workshops.
        $this->assertFalse($this->is_submission_erased($this->submission111));
        $this->assertFalse($this->is_submission_erased($this->submission121));

        // Student1 has self-assessed one their submission.
        $this->assertFalse($this->is_given_assessment_erased($this->assessment1111));
        $this->assertFalse($this->is_received_assessment_erased($this->assessment1111));

        // Student2 and student3 peer-assessed student1's submission.
        $this->assertFalse($this->is_given_assessment_erased($this->assessment1112));
        $this->assertFalse($this->is_given_assessment_erased($this->assessment1113));

        // Delete data owned by student1 and student3 in the workshop11.

        $context11 = \context_module::instance($this->workshop11->cmid);

        $approveduserlist = new \core_privacy\local\request\approved_userlist($context11, 'mod_workshop', [
            $this->student1->id,
            $this->student3->id,
        ]);
        \mod_workshop\privacy\provider::delete_data_for_users($approveduserlist);

        // Student1's submission is erased in workshop11 but not in the other workshop12.
        $this->assertTrue($this->is_submission_erased($this->submission111));
        $this->assertFalse($this->is_submission_erased($this->submission121));

        // Student1's self-assessment is erased.
        $this->assertTrue($this->is_given_assessment_erased($this->assessment1111));
        $this->assertTrue($this->is_received_assessment_erased($this->assessment1111));

        // Student1's received peer-assessments are also erased because they are "owned" by the recipient of the assessment.
        $this->assertTrue($this->is_received_assessment_erased($this->assessment1112));
        $this->assertTrue($this->is_received_assessment_erased($this->assessment1113));

        // Student2's owned data in the given assessment are not erased.
        $this->assertFalse($this->is_given_assessment_erased($this->assessment1112));

        // Student3's owned data in the given assessment were erased because she/he was in the userlist.
        $this->assertTrue($this->is_given_assessment_erased($this->assessment1113));

        // Personal data in other contexts are not affected.
        $this->assertFalse($this->is_submission_erased($this->submission121));
        $this->assertFalse($this->is_given_assessment_erased($this->assessment2121));
        $this->assertFalse($this->is_received_assessment_erased($this->assessment2121));
    }

    /**
     * Check if the given submission has the author's personal data erased.
     *
     * @param int $submissionid Identifier of the submission.
     * @return boolean
     */
    protected function is_submission_erased(int $submissionid) {
        global $DB;

        $submission = $DB->get_record('workshop_submissions', ['id' => $submissionid], 'id, title, content', MUST_EXIST);

        $titledeleted = $submission->title === get_string('privacy:request:delete:title', 'mod_workshop');
        $contentdeleted = $submission->content === get_string('privacy:request:delete:content', 'mod_workshop');

        if ($titledeleted && $contentdeleted) {
            return true;

        } else {
            return false;
        }
    }

    /**
     * Check is the received assessment has recipient's (author's) personal data erased.
     *
     * @param int $assessmentid Identifier of the assessment.
     * @return boolean
     */
    protected function is_received_assessment_erased(int $assessmentid) {
        global $DB;

        $assessment = $DB->get_record('workshop_assessments', ['id' => $assessmentid], 'id, feedbackauthor', MUST_EXIST);

        if ($assessment->feedbackauthor === get_string('privacy:request:delete:content', 'mod_workshop')) {
            return true;

        } else {
            return false;
        }
    }

    /**
     * Check is the given assessment has reviewer's personal data erased.
     *
     * @param int $assessmentid Identifier of the assessment.
     * @return boolean
     */
    protected function is_given_assessment_erased(int $assessmentid) {
        global $DB;

        $assessment = $DB->get_record('workshop_assessments', ['id' => $assessmentid], 'id, feedbackreviewer', MUST_EXIST);

        if ($assessment->feedbackreviewer === get_string('privacy:request:delete:content', 'mod_workshop')) {
            return true;

        } else {
            return false;
        }
    }
}
