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
 * Expired contexts tests.
 *
 * @package    tool_dataprivacy
 * @copyright  2018 David Monllao
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use tool_dataprivacy\api;
use tool_dataprivacy\data_registry;
use tool_dataprivacy\expired_context;
use tool_dataprivacy\purpose;
use tool_dataprivacy\category;
use tool_dataprivacy\contextlevel;

defined('MOODLE_INTERNAL') || die();
global $CFG;

/**
 * Expired contexts tests.
 *
 * @package    tool_dataprivacy
 * @copyright  2018 David Monllao
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_dataprivacy_expired_contexts_testcase extends advanced_testcase {

    /**
     * Setup the basics with the specified retention period.
     *
     * @param   string  $system Retention policy for the system.
     * @param   string  $user Retention policy for users.
     * @param   string  $course Retention policy for courses.
     * @param   string  $activity Retention policy for activities.
     */
    protected function setup_basics(string $system, string $user, string $course, string $activity = null) : array {
        $this->resetAfterTest();

        $purposes = [];
        $purposes[] = $this->create_and_set_purpose_for_contextlevel($system, CONTEXT_SYSTEM);
        $purposes[] = $this->create_and_set_purpose_for_contextlevel($user, CONTEXT_USER);
        $purposes[] = $this->create_and_set_purpose_for_contextlevel($course, CONTEXT_COURSE);
        if (null !== $activity) {
            $purposes[] = $this->create_and_set_purpose_for_contextlevel($activity, CONTEXT_MODULE);
        }

        return $purposes;
    }

    /**
     * Create a retention period and set it for the specified context level.
     *
     * @param   string  $retention
     * @param   int     $contextlevel
     * @return  purpose
     */
    protected function create_and_set_purpose_for_contextlevel(string $retention, int $contextlevel) : purpose {
        $purpose = new purpose(0, (object) [
            'name' => 'Test purpose ' . rand(1, 1000),
            'retentionperiod' => $retention,
            'lawfulbases' => 'gdpr_art_6_1_a',
        ]);
        $purpose->create();

        $cat = new category(0, (object) ['name' => 'Test category']);
        $cat->create();

        if ($contextlevel <= CONTEXT_USER) {
            $record = (object) [
                'purposeid'     => $purpose->get('id'),
                'categoryid'    => $cat->get('id'),
                'contextlevel'  => $contextlevel,
            ];
            api::set_contextlevel($record);
        } else {
            list($purposevar, ) = data_registry::var_names_from_context(
                    \context_helper::get_class_for_level(CONTEXT_COURSE)
                );
            set_config($purposevar, $purpose->get('id'), 'tool_dataprivacy');
        }

        return $purpose;
    }

    /**
     * Ensure that a user with no lastaccess is not flagged for deletion.
     */
    public function test_flag_not_setup() {
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();

        $this->setUser($user);
        $block = $this->create_user_block('Title', 'Content', FORMAT_PLAIN);
        $context = \context_block::instance($block->instance->id);
        $this->setUser();

        // Flag all expired contexts.
        $manager = new \tool_dataprivacy\expired_contexts_manager();
        list($flaggedcourses, $flaggedusers) = $manager->flag_expired_contexts();

        $this->assertEquals(0, $flaggedcourses);
        $this->assertEquals(0, $flaggedusers);
    }

    /**
     * Ensure that a user with no lastaccess is not flagged for deletion.
     */
    public function test_flag_user_no_lastaccess() {
        $this->resetAfterTest();

        $this->setup_basics('PT1H', 'PT1H', 'PT1H');

        $user = $this->getDataGenerator()->create_user();

        $this->setUser($user);
        $block = $this->create_user_block('Title', 'Content', FORMAT_PLAIN);
        $context = \context_block::instance($block->instance->id);
        $this->setUser();

        // Flag all expired contexts.
        $manager = new \tool_dataprivacy\expired_contexts_manager();
        list($flaggedcourses, $flaggedusers) = $manager->flag_expired_contexts();

        $this->assertEquals(0, $flaggedcourses);
        $this->assertEquals(0, $flaggedusers);
    }

    /**
     * Ensure that a user with a recent lastaccess is not flagged for deletion.
     */
    public function test_flag_user_recent_lastaccess() {
        $this->resetAfterTest();

        $this->setup_basics('PT1H', 'PT1H', 'PT1H');

        $user = $this->getDataGenerator()->create_user(['lastaccess' => time()]);

        $this->setUser($user);
        $block = $this->create_user_block('Title', 'Content', FORMAT_PLAIN);
        $context = \context_block::instance($block->instance->id);
        $this->setUser();

        // Flag all expired contexts.
        $manager = new \tool_dataprivacy\expired_contexts_manager();
        list($flaggedcourses, $flaggedusers) = $manager->flag_expired_contexts();

        $this->assertEquals(0, $flaggedcourses);
        $this->assertEquals(0, $flaggedusers);
    }

    /**
     * Ensure that a user with a lastaccess in the past is flagged for deletion.
     */
    public function test_flag_user_past_lastaccess() {
        $this->resetAfterTest();

        $this->setup_basics('PT1H', 'PT1H', 'PT1H');

        $user = $this->getDataGenerator()->create_user(['lastaccess' => time() - YEARSECS]);

        $this->setUser($user);
        $block = $this->create_user_block('Title', 'Content', FORMAT_PLAIN);
        $context = \context_block::instance($block->instance->id);
        $this->setUser();

        // Flag all expired contexts.
        $manager = new \tool_dataprivacy\expired_contexts_manager();
        list($flaggedcourses, $flaggedusers) = $manager->flag_expired_contexts();

        // Although there is a block in the user context, everything in the user context is regarded as one.
        $this->assertEquals(0, $flaggedcourses);
        $this->assertEquals(1, $flaggedusers);
    }

    /**
     * Ensure that a user with a lastaccess in the past but active enrolments is not flagged for deletion.
     */
    public function test_flag_user_past_lastaccess_still_enrolled() {
        $this->resetAfterTest();

        $this->setup_basics('PT1H', 'PT1H', 'PT1H');

        $user = $this->getDataGenerator()->create_user(['lastaccess' => time() - YEARSECS]);
        $course = $this->getDataGenerator()->create_course(['startdate' => time(), 'enddate' => time() + YEARSECS]);
        $this->getDataGenerator()->enrol_user($user->id, $course->id, 'student');

        $otheruser = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($otheruser->id, $course->id, 'student');

        $this->setUser($user);
        $block = $this->create_user_block('Title', 'Content', FORMAT_PLAIN);
        $context = \context_block::instance($block->instance->id);
        $this->setUser();

        // Flag all expired contexts.
        $manager = new \tool_dataprivacy\expired_contexts_manager();
        list($flaggedcourses, $flaggedusers) = $manager->flag_expired_contexts();

        $this->assertEquals(0, $flaggedcourses);
        $this->assertEquals(0, $flaggedusers);
    }

    /**
     * Ensure that a user with a lastaccess in the past and expired enrolments.
     */
    public function test_flag_user_past_lastaccess_unexpired_past_enrolment() {
        $this->resetAfterTest();

        $this->setup_basics('PT1H', 'PT1H', 'P1Y');

        $user = $this->getDataGenerator()->create_user(['lastaccess' => time() - YEARSECS]);
        $course = $this->getDataGenerator()->create_course(['startdate' => time() - YEARSECS, 'enddate' => time() - WEEKSECS]);
        $this->getDataGenerator()->enrol_user($user->id, $course->id, 'student');

        $otheruser = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($otheruser->id, $course->id, 'student');

        $this->setUser($user);
        $block = $this->create_user_block('Title', 'Content', FORMAT_PLAIN);
        $context = \context_block::instance($block->instance->id);
        $this->setUser();

        // Flag all expired contexts.
        $manager = new \tool_dataprivacy\expired_contexts_manager();
        list($flaggedcourses, $flaggedusers) = $manager->flag_expired_contexts();

        $this->assertEquals(0, $flaggedcourses);
        $this->assertEquals(0, $flaggedusers);
    }

    /**
     * Ensure that a user with a lastaccess in the past and expired enrolments.
     */
    public function test_flag_user_past_lastaccess_expired_enrolled() {
        $this->resetAfterTest();

        $this->setup_basics('PT1H', 'PT1H', 'PT1H');

        $user = $this->getDataGenerator()->create_user(['lastaccess' => time() - YEARSECS]);
        $course = $this->getDataGenerator()->create_course(['startdate' => time() - YEARSECS, 'enddate' => time() - WEEKSECS]);
        $this->getDataGenerator()->enrol_user($user->id, $course->id, 'student');

        $otheruser = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($otheruser->id, $course->id, 'student');

        $this->setUser($user);
        $block = $this->create_user_block('Title', 'Content', FORMAT_PLAIN);
        $context = \context_block::instance($block->instance->id);
        $this->setUser();

        // Flag all expired contexts.
        $manager = new \tool_dataprivacy\expired_contexts_manager();
        list($flaggedcourses, $flaggedusers) = $manager->flag_expired_contexts();

        $this->assertEquals(1, $flaggedcourses);
        $this->assertEquals(1, $flaggedusers);
    }

    /**
     * Ensure that a user with a lastaccess in the past and enrolments without a course end date are respected
     * correctly.
     */
    public function test_flag_user_past_lastaccess_missing_enddate_required() {
        $this->resetAfterTest();

        $this->setup_basics('PT1H', 'PT1H', 'PT1H');

        $user = $this->getDataGenerator()->create_user(['lastaccess' => time() - YEARSECS]);
        $course = $this->getDataGenerator()->create_course();
        $this->getDataGenerator()->enrol_user($user->id, $course->id, 'student');

        $otheruser = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($otheruser->id, $course->id, 'student');

        $this->setUser($user);
        $block = $this->create_user_block('Title', 'Content', FORMAT_PLAIN);
        $context = \context_block::instance($block->instance->id);
        $this->setUser();

        // Ensure that course end dates are not required.
        set_config('requireallenddatesforuserdeletion', 1, 'tool_dataprivacy');

        // Flag all expired contexts.
        $manager = new \tool_dataprivacy\expired_contexts_manager();
        list($flaggedcourses, $flaggedusers) = $manager->flag_expired_contexts();

        $this->assertEquals(0, $flaggedcourses);
        $this->assertEquals(0, $flaggedusers);
    }

    /**
     * Ensure that a user with a lastaccess in the past and enrolments without a course end date are respected
     * correctly when the end date is not required.
     */
    public function test_flag_user_past_lastaccess_missing_enddate_not_required() {
        $this->resetAfterTest();

        $this->setup_basics('PT1H', 'PT1H', 'PT1H');

        $user = $this->getDataGenerator()->create_user(['lastaccess' => time() - YEARSECS]);
        $course = $this->getDataGenerator()->create_course();
        $this->getDataGenerator()->enrol_user($user->id, $course->id, 'student');

        $otheruser = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($otheruser->id, $course->id, 'student');

        $this->setUser($user);
        $block = $this->create_user_block('Title', 'Content', FORMAT_PLAIN);
        $context = \context_block::instance($block->instance->id);
        $this->setUser();

        // Ensure that course end dates are required.
        set_config('requireallenddatesforuserdeletion', 0, 'tool_dataprivacy');

        // Flag all expired contexts.
        $manager = new \tool_dataprivacy\expired_contexts_manager();
        list($flaggedcourses, $flaggedusers) = $manager->flag_expired_contexts();

        $this->assertEquals(0, $flaggedcourses);
        $this->assertEquals(1, $flaggedusers);
    }

    /**
     * Ensure that a user with a recent lastaccess is not flagged for deletion.
     */
    public function test_flag_user_recent_lastaccess_existing_record() {
        $this->resetAfterTest();

        $this->setup_basics('PT1H', 'PT1H', 'PT1H');

        $user = $this->getDataGenerator()->create_user(['lastaccess' => time()]);
        $usercontext = \context_user::instance($user->id);

        // Create an existing expired_context.
        $expiredcontext = new expired_context(0, (object) [
                'contextid' => $usercontext->id,
                'status' => expired_context::STATUS_EXPIRED,
            ]);
        $expiredcontext->save();

        $this->setUser($user);
        $block = $this->create_user_block('Title', 'Content', FORMAT_PLAIN);
        $context = \context_block::instance($block->instance->id);
        $this->setUser();

        // Flag all expired contexts.
        $manager = new \tool_dataprivacy\expired_contexts_manager();
        list($flaggedcourses, $flaggedusers) = $manager->flag_expired_contexts();

        $this->assertEquals(0, $flaggedcourses);
        $this->assertEquals(0, $flaggedusers);

        $this->expectException('dml_missing_record_exception');
        new expired_context($expiredcontext->get('id'));
    }

    /**
     * Ensure that a user with a recent lastaccess is not flagged for deletion.
     */
    public function test_flag_user_retention_changed() {
        $this->resetAfterTest();

        list($systempurpose, $userpurpose, $coursepurpose) = $this->setup_basics('PT1H', 'PT1H', 'PT1H');

        $user = $this->getDataGenerator()->create_user(['lastaccess' => time() - DAYSECS]);
        $usercontext = \context_user::instance($user->id);

        $this->setUser($user);
        $block = $this->create_user_block('Title', 'Content', FORMAT_PLAIN);
        $context = \context_block::instance($block->instance->id);
        $this->setUser();

        // Flag all expired contexts.
        $manager = new \tool_dataprivacy\expired_contexts_manager();
        list($flaggedcourses, $flaggedusers) = $manager->flag_expired_contexts();

        $this->assertEquals(0, $flaggedcourses);
        $this->assertEquals(1, $flaggedusers);

        $expiredcontext = expired_context::get_record(['contextid' => $usercontext->id]);
        $this->assertNotFalse($expiredcontext);

        // Increase the retention period to 5 years.
        $userpurpose->set('retentionperiod', 'P5Y');
        $userpurpose->save();

        // Re-run the expiry job - the previously flagged user will be removed because the retention period has been increased.
        list($flaggedcourses, $flaggedusers) = $manager->flag_expired_contexts();
        $this->assertEquals(0, $flaggedcourses);
        $this->assertEquals(0, $flaggedusers);

        // The expiry record will now have been removed.
        $this->expectException('dml_missing_record_exception');
        new expired_context($expiredcontext->get('id'));
    }

    /**
     * Ensure that a user with a historically expired expired block record child is cleaned up.
     */
    public function test_flag_user_historic_block_unapproved() {
        $this->resetAfterTest();

        list($systempurpose, $userpurpose, $coursepurpose) = $this->setup_basics('PT1H', 'PT1H', 'PT1H');

        $user = $this->getDataGenerator()->create_user(['lastaccess' => time() - DAYSECS]);
        $usercontext = \context_user::instance($user->id);

        $this->setUser($user);
        $block = $this->create_user_block('Title', 'Content', FORMAT_PLAIN);
        $blockcontext = \context_block::instance($block->instance->id);
        $this->setUser();

        // Create an existing expired_context which has not been approved for the block.
        $expiredcontext = new expired_context(0, (object) [
                'contextid' => $blockcontext->id,
                'status' => expired_context::STATUS_EXPIRED,
            ]);
        $expiredcontext->save();

        // Flag all expired contexts.
        $manager = new \tool_dataprivacy\expired_contexts_manager();
        list($flaggedcourses, $flaggedusers) = $manager->flag_expired_contexts();

        $this->assertEquals(0, $flaggedcourses);
        $this->assertEquals(1, $flaggedusers);

        $expiredblockcontext = expired_context::get_record(['contextid' => $blockcontext->id]);
        $this->assertFalse($expiredblockcontext);

        $expiredusercontext = expired_context::get_record(['contextid' => $usercontext->id]);
        $this->assertNotFalse($expiredusercontext);
    }

    /**
     * Ensure that a user with a block which has a default retention period which has not expired, is still expired.
     */
    public function test_flag_user_historic_unexpired_child() {
        $this->resetAfterTest();

        list($systempurpose, $userpurpose, $coursepurpose) = $this->setup_basics('PT1H', 'PT1H', 'PT1H');
        $blockpurpose = $this->create_and_set_purpose_for_contextlevel('P5Y', CONTEXT_BLOCK);

        $user = $this->getDataGenerator()->create_user(['lastaccess' => time() - DAYSECS]);
        $usercontext = \context_user::instance($user->id);

        $this->setUser($user);
        $block = $this->create_user_block('Title', 'Content', FORMAT_PLAIN);
        $blockcontext = \context_block::instance($block->instance->id);
        $this->setUser();

        // Flag all expired contexts.
        $manager = new \tool_dataprivacy\expired_contexts_manager();
        list($flaggedcourses, $flaggedusers) = $manager->flag_expired_contexts();

        $this->assertEquals(0, $flaggedcourses);
        $this->assertEquals(1, $flaggedusers);

        $expiredcontext = expired_context::get_record(['contextid' => $usercontext->id]);
        $this->assertNotFalse($expiredcontext);
    }

    /**
     * Ensure that a course with no end date is not flagged.
     */
    public function test_flag_course_no_enddate() {
        $this->resetAfterTest();

        $this->setup_basics('PT1H', 'PT1H', 'PT1H', 'PT1H');

        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', ['course' => $course->id]);

        // Flag all expired contexts.
        $manager = new \tool_dataprivacy\expired_contexts_manager();
        list($flaggedcourses, $flaggedusers) = $manager->flag_expired_contexts();

        $this->assertEquals(0, $flaggedcourses);
        $this->assertEquals(0, $flaggedusers);
    }

    /**
     * Ensure that a course with an end date in the distant past, but a child which is unexpired is not flagged.
     */
    public function test_flag_course_past_enddate_future_child() {
        $this->resetAfterTest();

        $this->setup_basics('PT1H', 'PT1H', 'PT1H', 'P5Y');

        $course = $this->getDataGenerator()->create_course([
                'startdate' => time() - (2 * YEARSECS),
                'enddate' => time() - YEARSECS,
            ]);
        $forum = $this->getDataGenerator()->create_module('forum', ['course' => $course->id]);

        // Flag all expired contexts.
        $manager = new \tool_dataprivacy\expired_contexts_manager();
        list($flaggedcourses, $flaggedusers) = $manager->flag_expired_contexts();

        $this->assertEquals(0, $flaggedcourses);
        $this->assertEquals(0, $flaggedusers);
    }

    /**
     * Ensure that a course with an end date in the distant past is flagged.
     */
    public function test_flag_course_past_enddate() {
        $this->resetAfterTest();

        $this->setup_basics('PT1H', 'PT1H', 'PT1H', 'PT1H');

        $course = $this->getDataGenerator()->create_course([
                'startdate' => time() - (2 * YEARSECS),
                'enddate' => time() - YEARSECS,
            ]);
        $forum = $this->getDataGenerator()->create_module('forum', ['course' => $course->id]);

        // Flag all expired contexts.
        $manager = new \tool_dataprivacy\expired_contexts_manager();
        list($flaggedcourses, $flaggedusers) = $manager->flag_expired_contexts();

        $this->assertEquals(2, $flaggedcourses);
        $this->assertEquals(0, $flaggedusers);
    }

    /**
     * Ensure that a course with an end date in the distant past is flagged.
     */
    public function test_flag_course_past_enddate_multiple() {
        $this->resetAfterTest();

        $this->setup_basics('PT1H', 'PT1H', 'PT1H', 'PT1H');

        $course1 = $this->getDataGenerator()->create_course([
                'startdate' => time() - (2 * YEARSECS),
                'enddate' => time() - YEARSECS,
            ]);
        $forum1 = $this->getDataGenerator()->create_module('forum', ['course' => $course1->id]);

        $course2 = $this->getDataGenerator()->create_course([
                'startdate' => time() - (2 * YEARSECS),
                'enddate' => time() - YEARSECS,
            ]);
        $forum2 = $this->getDataGenerator()->create_module('forum', ['course' => $course2->id]);

        // Flag all expired contexts.
        $manager = new \tool_dataprivacy\expired_contexts_manager();
        list($flaggedcourses, $flaggedusers) = $manager->flag_expired_contexts();

        $this->assertEquals(4, $flaggedcourses);
        $this->assertEquals(0, $flaggedusers);
    }

    /**
     * Ensure that a course with an end date in the future is not flagged.
     */
    public function test_flag_course_future_enddate() {
        $this->resetAfterTest();

        $this->setup_basics('PT1H', 'PT1H', 'PT1H', 'PT1H');

        $course = $this->getDataGenerator()->create_course(['enddate' => time() + YEARSECS]);
        $forum = $this->getDataGenerator()->create_module('forum', ['course' => $course->id]);

        // Flag all expired contexts.
        $manager = new \tool_dataprivacy\expired_contexts_manager();
        list($flaggedcourses, $flaggedusers) = $manager->flag_expired_contexts();

        $this->assertEquals(0, $flaggedcourses);
        $this->assertEquals(0, $flaggedusers);
    }

    /**
     * Ensure that a course with an end date in the future is not flagged.
     */
    public function test_flag_course_recent_unexpired_enddate() {
        $this->resetAfterTest();

        $this->setup_basics('PT1H', 'PT1H', 'PT1H', 'PT1H');

        $course = $this->getDataGenerator()->create_course(['enddate' => time() - 1]);

        // Flag all expired contexts.
        $manager = new \tool_dataprivacy\expired_contexts_manager();
        list($flaggedcourses, $flaggedusers) = $manager->flag_expired_contexts();

        $this->assertEquals(0, $flaggedcourses);
        $this->assertEquals(0, $flaggedusers);
    }

    /**
     * Ensure that a site not setup will not process anything.
     */
    public function test_process_not_setup() {
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user(['lastaccess' => time() - YEARSECS]);
        $usercontext = \context_user::instance($user->id);

        // Create an existing expired_context.
        $expiredcontext = new expired_context(0, (object) [
                'contextid' => $usercontext->id,
                'status' => expired_context::STATUS_EXPIRED,
            ]);
        $expiredcontext->save();

        $mockprivacymanager = $this->getMockBuilder(\core_privacy\manager::class)
            ->setMethods([
                'delete_data_for_user',
                'delete_data_for_all_users_in_context',
            ])
            ->getMock();
        $mockprivacymanager->expects($this->never())->method('delete_data_for_user');
        $mockprivacymanager->expects($this->never())->method('delete_data_for_all_users_in_context');

        $manager = $this->getMockBuilder(\tool_dataprivacy\expired_contexts_manager::class)
            ->setMethods(['get_privacy_manager'])
            ->getMock();
        $manager->set_progress(new \null_progress_trace());

        $manager->method('get_privacy_manager')->willReturn($mockprivacymanager);
        list($processedcourses, $processedusers) = $manager->process_approved_deletions();

        $this->assertEquals(0, $processedcourses);
        $this->assertEquals(0, $processedusers);
    }

    /**
     * Ensure that a user with no lastaccess is not flagged for deletion.
     */
    public function test_process_none_approved() {
        $this->resetAfterTest();

        $this->setup_basics('PT1H', 'PT1H', 'PT1H', 'PT1H');

        $user = $this->getDataGenerator()->create_user(['lastaccess' => time() - YEARSECS]);
        $usercontext = \context_user::instance($user->id);

        // Create an existing expired_context.
        $expiredcontext = new expired_context(0, (object) [
                'contextid' => $usercontext->id,
                'status' => expired_context::STATUS_EXPIRED,
            ]);
        $expiredcontext->save();

        $mockprivacymanager = $this->getMockBuilder(\core_privacy\manager::class)
            ->setMethods([
                'delete_data_for_user',
                'delete_data_for_all_users_in_context',
            ])
            ->getMock();
        $mockprivacymanager->expects($this->never())->method('delete_data_for_user');
        $mockprivacymanager->expects($this->never())->method('delete_data_for_all_users_in_context');

        $manager = $this->getMockBuilder(\tool_dataprivacy\expired_contexts_manager::class)
            ->setMethods(['get_privacy_manager'])
            ->getMock();
        $manager->set_progress(new \null_progress_trace());

        $manager->method('get_privacy_manager')->willReturn($mockprivacymanager);
        list($processedcourses, $processedusers) = $manager->process_approved_deletions();

        $this->assertEquals(0, $processedcourses);
        $this->assertEquals(0, $processedusers);
    }

    /**
     * Ensure that a user with no lastaccess is not flagged for deletion.
     */
    public function test_process_no_context() {
        $this->resetAfterTest();

        $this->setup_basics('PT1H', 'PT1H', 'PT1H', 'PT1H');

        // Create an existing expired_context.
        $expiredcontext = new expired_context(0, (object) [
                'contextid' => -1,
                'status' => expired_context::STATUS_APPROVED,
            ]);
        $expiredcontext->save();

        $mockprivacymanager = $this->getMockBuilder(\core_privacy\manager::class)
            ->setMethods([
                'delete_data_for_user',
                'delete_data_for_all_users_in_context',
            ])
            ->getMock();
        $mockprivacymanager->expects($this->never())->method('delete_data_for_user');
        $mockprivacymanager->expects($this->never())->method('delete_data_for_all_users_in_context');

        $manager = $this->getMockBuilder(\tool_dataprivacy\expired_contexts_manager::class)
            ->setMethods(['get_privacy_manager'])
            ->getMock();
        $manager->set_progress(new \null_progress_trace());

        $manager->method('get_privacy_manager')->willReturn($mockprivacymanager);
        list($processedcourses, $processedusers) = $manager->process_approved_deletions();

        $this->assertEquals(0, $processedcourses);
        $this->assertEquals(0, $processedusers);

        $this->expectException('dml_missing_record_exception');
        new expired_context($expiredcontext->get('id'));
    }

    /**
     * Ensure that a user context previously flagged as approved is removed.
     */
    public function test_process_user_context() {
        $this->resetAfterTest();

        $this->setup_basics('PT1H', 'PT1H', 'PT1H');

        $user = $this->getDataGenerator()->create_user(['lastaccess' => time() - YEARSECS]);
        $usercontext = \context_user::instance($user->id);

        $this->setUser($user);
        $block = $this->create_user_block('Title', 'Content', FORMAT_PLAIN);
        $blockcontext = \context_block::instance($block->instance->id);
        $this->setUser();

        // Create an existing expired_context.
        $expiredcontext = new expired_context(0, (object) [
                'contextid' => $usercontext->id,
                'status' => expired_context::STATUS_APPROVED,
            ]);
        $expiredcontext->save();

        $mockprivacymanager = $this->getMockBuilder(\core_privacy\manager::class)
            ->setMethods([
                'delete_data_for_user',
                'delete_data_for_all_users_in_context',
            ])
            ->getMock();
        $mockprivacymanager->expects($this->atLeastOnce())->method('delete_data_for_user');
        $mockprivacymanager->expects($this->exactly(2))
            ->method('delete_data_for_all_users_in_context')
            ->withConsecutive(
                [$blockcontext],
                [$usercontext]
            );

        $manager = $this->getMockBuilder(\tool_dataprivacy\expired_contexts_manager::class)
            ->setMethods(['get_privacy_manager'])
            ->getMock();
        $manager->set_progress(new \null_progress_trace());

        $manager->method('get_privacy_manager')->willReturn($mockprivacymanager);
        list($processedcourses, $processedusers) = $manager->process_approved_deletions();

        $this->assertEquals(0, $processedcourses);
        $this->assertEquals(1, $processedusers);

        $updatedcontext = new expired_context($expiredcontext->get('id'));
        $this->assertEquals(expired_context::STATUS_CLEANED, $updatedcontext->get('status'));

        // Flag all expired contexts again.
        list($flaggedcourses, $flaggedusers) = $manager->flag_expired_contexts();

        $this->assertEquals(0, $flaggedcourses);
        $this->assertEquals(0, $flaggedusers);

        // Ensure that the deleted context record is still present.
        $updatedcontext = new expired_context($expiredcontext->get('id'));
        $this->assertEquals(expired_context::STATUS_CLEANED, $updatedcontext->get('status'));
    }

    /**
     * Ensure that a course context previously flagged as approved is removed.
     */
    public function test_process_course_context() {
        $this->resetAfterTest();

        $this->setup_basics('PT1H', 'PT1H', 'PT1H', 'PT1H');

        $course = $this->getDataGenerator()->create_course([
                'startdate' => time() - (2 * YEARSECS),
                'enddate' => time() - YEARSECS,
            ]);
        $coursecontext = \context_course::instance($course->id);

        // Create an existing expired_context.
        $expiredcontext = new expired_context(0, (object) [
                'contextid' => $coursecontext->id,
                'status' => expired_context::STATUS_APPROVED,
            ]);
        $expiredcontext->save();

        $mockprivacymanager = $this->getMockBuilder(\core_privacy\manager::class)
            ->setMethods([
                'delete_data_for_user',
                'delete_data_for_all_users_in_context',
            ])
            ->getMock();
        $mockprivacymanager->expects($this->never())->method('delete_data_for_user');
        $mockprivacymanager->expects($this->once())->method('delete_data_for_all_users_in_context');

        $manager = $this->getMockBuilder(\tool_dataprivacy\expired_contexts_manager::class)
            ->setMethods(['get_privacy_manager'])
            ->getMock();
        $manager->set_progress(new \null_progress_trace());

        $manager->method('get_privacy_manager')->willReturn($mockprivacymanager);
        list($processedcourses, $processedusers) = $manager->process_approved_deletions();

        $this->assertEquals(1, $processedcourses);
        $this->assertEquals(0, $processedusers);

        $updatedcontext = new expired_context($expiredcontext->get('id'));
        $this->assertEquals(expired_context::STATUS_CLEANED, $updatedcontext->get('status'));
    }

    /**
     * Ensure that a user context previously flagged as approved is not removed if the user then logs in.
     */
    public function test_process_user_context_logged_in_after_approval() {
        $this->resetAfterTest();

        $this->setup_basics('PT1H', 'PT1H', 'PT1H');

        $user = $this->getDataGenerator()->create_user(['lastaccess' => time() - YEARSECS]);
        $usercontext = \context_user::instance($user->id);

        $this->setUser($user);
        $block = $this->create_user_block('Title', 'Content', FORMAT_PLAIN);
        $context = \context_block::instance($block->instance->id);
        $this->setUser();

        // Create an existing expired_context.
        $expiredcontext = new expired_context(0, (object) [
                'contextid' => $usercontext->id,
                'status' => expired_context::STATUS_APPROVED,
            ]);
        $expiredcontext->save();

        // Now bump the user's last login time.
        $this->setUser($user);
        user_accesstime_log();
        $this->setUser();

        $mockprivacymanager = $this->getMockBuilder(\core_privacy\manager::class)
            ->setMethods([
                'delete_data_for_user',
                'delete_data_for_all_users_in_context',
            ])
            ->getMock();
        $mockprivacymanager->expects($this->never())->method('delete_data_for_user');
        $mockprivacymanager->expects($this->never())->method('delete_data_for_all_users_in_context');

        $manager = $this->getMockBuilder(\tool_dataprivacy\expired_contexts_manager::class)
            ->setMethods(['get_privacy_manager'])
            ->getMock();
        $manager->set_progress(new \null_progress_trace());

        $manager->method('get_privacy_manager')->willReturn($mockprivacymanager);
        list($processedcourses, $processedusers) = $manager->process_approved_deletions();

        $this->assertEquals(0, $processedcourses);
        $this->assertEquals(0, $processedusers);

        $this->expectException('dml_missing_record_exception');
        new expired_context($expiredcontext->get('id'));
    }

    /**
     * Ensure that a user context previously flagged as approved is not removed if the purpose has changed.
     */
    public function test_process_user_context_changed_after_approved() {
        $this->resetAfterTest();

        $this->setup_basics('PT1H', 'PT1H', 'PT1H');

        $user = $this->getDataGenerator()->create_user(['lastaccess' => time() - YEARSECS]);
        $usercontext = \context_user::instance($user->id);

        $this->setUser($user);
        $block = $this->create_user_block('Title', 'Content', FORMAT_PLAIN);
        $context = \context_block::instance($block->instance->id);
        $this->setUser();

        // Create an existing expired_context.
        $expiredcontext = new expired_context(0, (object) [
                'contextid' => $usercontext->id,
                'status' => expired_context::STATUS_APPROVED,
            ]);
        $expiredcontext->save();

        // Now make the user a site admin.
        $admins = explode(',', get_config('moodle', 'siteadmins'));
        $admins[] = $user->id;
        set_config('siteadmins', implode(',', $admins));

        $mockprivacymanager = $this->getMockBuilder(\core_privacy\manager::class)
            ->setMethods([
                'delete_data_for_user',
                'delete_data_for_all_users_in_context',
            ])
            ->getMock();
        $mockprivacymanager->expects($this->never())->method('delete_data_for_user');
        $mockprivacymanager->expects($this->never())->method('delete_data_for_all_users_in_context');

        $manager = $this->getMockBuilder(\tool_dataprivacy\expired_contexts_manager::class)
            ->setMethods(['get_privacy_manager'])
            ->getMock();
        $manager->set_progress(new \null_progress_trace());

        $manager->method('get_privacy_manager')->willReturn($mockprivacymanager);
        list($processedcourses, $processedusers) = $manager->process_approved_deletions();

        $this->assertEquals(0, $processedcourses);
        $this->assertEquals(0, $processedusers);

        $this->expectException('dml_missing_record_exception');
        new expired_context($expiredcontext->get('id'));
    }

    /**
     * Ensure that a user with a historically expired expired block record child is cleaned up.
     */
    public function test_process_user_historic_block_unapproved() {
        $this->resetAfterTest();

        list($systempurpose, $userpurpose, $coursepurpose) = $this->setup_basics('PT1H', 'PT1H', 'PT1H');

        $user = $this->getDataGenerator()->create_user(['lastaccess' => time() - DAYSECS]);
        $usercontext = \context_user::instance($user->id);

        $this->setUser($user);
        $block = $this->create_user_block('Title', 'Content', FORMAT_PLAIN);
        $blockcontext = \context_block::instance($block->instance->id);
        $this->setUser();

        // Create an expired_context for the user.
        $expiredusercontext = new expired_context(0, (object) [
                'contextid' => $usercontext->id,
                'status' => expired_context::STATUS_APPROVED,
            ]);
        $expiredusercontext->save();

        // Create an existing expired_context which has not been approved for the block.
        $expiredblockcontext = new expired_context(0, (object) [
                'contextid' => $blockcontext->id,
                'status' => expired_context::STATUS_EXPIRED,
            ]);
        $expiredblockcontext->save();

        $mockprivacymanager = $this->getMockBuilder(\core_privacy\manager::class)
            ->setMethods([
                'delete_data_for_user',
                'delete_data_for_all_users_in_context',
            ])
            ->getMock();
        $mockprivacymanager->expects($this->atLeastOnce())->method('delete_data_for_user');
        $mockprivacymanager->expects($this->exactly(2))
            ->method('delete_data_for_all_users_in_context')
            ->withConsecutive(
                [$blockcontext],
                [$usercontext]
            );

        $manager = $this->getMockBuilder(\tool_dataprivacy\expired_contexts_manager::class)
            ->setMethods(['get_privacy_manager'])
            ->getMock();
        $manager->set_progress(new \null_progress_trace());

        $manager->method('get_privacy_manager')->willReturn($mockprivacymanager);
        list($processedcourses, $processedusers) = $manager->process_approved_deletions();

        $this->assertEquals(0, $processedcourses);
        $this->assertEquals(1, $processedusers);

        $updatedcontext = new expired_context($expiredusercontext->get('id'));
        $this->assertEquals(expired_context::STATUS_CLEANED, $updatedcontext->get('status'));
    }

    /**
     * Ensure that a user with a block which has a default retention period which has not expired, is still expired.
     */
    public function test_process_user_historic_unexpired_child() {
        $this->resetAfterTest();

        list($systempurpose, $userpurpose, $coursepurpose) = $this->setup_basics('PT1H', 'PT1H', 'PT1H');
        $blockpurpose = $this->create_and_set_purpose_for_contextlevel('P5Y', CONTEXT_BLOCK);

        $user = $this->getDataGenerator()->create_user(['lastaccess' => time() - DAYSECS]);
        $usercontext = \context_user::instance($user->id);

        $this->setUser($user);
        $block = $this->create_user_block('Title', 'Content', FORMAT_PLAIN);
        $blockcontext = \context_block::instance($block->instance->id);
        $this->setUser();

        // Create an expired_context for the user.
        $expiredusercontext = new expired_context(0, (object) [
                'contextid' => $usercontext->id,
                'status' => expired_context::STATUS_APPROVED,
            ]);
        $expiredusercontext->save();

        $mockprivacymanager = $this->getMockBuilder(\core_privacy\manager::class)
            ->setMethods([
                'delete_data_for_user',
                'delete_data_for_all_users_in_context',
            ])
            ->getMock();
        $mockprivacymanager->expects($this->atLeastOnce())->method('delete_data_for_user');
        $mockprivacymanager->expects($this->exactly(2))
            ->method('delete_data_for_all_users_in_context')
            ->withConsecutive(
                [$blockcontext],
                [$usercontext]
            );

        $manager = $this->getMockBuilder(\tool_dataprivacy\expired_contexts_manager::class)
            ->setMethods(['get_privacy_manager'])
            ->getMock();
        $manager->set_progress(new \null_progress_trace());

        $manager->method('get_privacy_manager')->willReturn($mockprivacymanager);
        list($processedcourses, $processedusers) = $manager->process_approved_deletions();

        $this->assertEquals(0, $processedcourses);
        $this->assertEquals(1, $processedusers);

        $updatedcontext = new expired_context($expiredusercontext->get('id'));
        $this->assertEquals(expired_context::STATUS_CLEANED, $updatedcontext->get('status'));
    }

    /**
     * Ensure that a course context previously flagged as approved for deletion which now has an unflagged child, is
     * updated.
     */
    public function test_process_course_context_updated() {
        $this->resetAfterTest();

        $purposes = $this->setup_basics('PT1H', 'PT1H', 'PT1H', 'PT1H');

        $course = $this->getDataGenerator()->create_course([
                'startdate' => time() - (2 * YEARSECS),
                'enddate' => time() - YEARSECS,
            ]);
        $coursecontext = \context_course::instance($course->id);
        $forum = $this->getDataGenerator()->create_module('forum', ['course' => $course->id]);

        // Create an existing expired_context.
        $expiredcontext = new expired_context(0, (object) [
                'contextid' => $coursecontext->id,
                'status' => expired_context::STATUS_APPROVED,
            ]);
        $expiredcontext->save();

        $mockprivacymanager = $this->getMockBuilder(\core_privacy\manager::class)
            ->setMethods([
                'delete_data_for_user',
                'delete_data_for_all_users_in_context',
            ])
            ->getMock();
        $mockprivacymanager->expects($this->never())->method('delete_data_for_user');
        $mockprivacymanager->expects($this->never())->method('delete_data_for_all_users_in_context');

        $manager = $this->getMockBuilder(\tool_dataprivacy\expired_contexts_manager::class)
            ->setMethods(['get_privacy_manager'])
            ->getMock();
        $manager->set_progress(new \null_progress_trace());
        $manager->method('get_privacy_manager')->willReturn($mockprivacymanager);

        $coursepurpose = $purposes[2];
        $coursepurpose->set('retentionperiod', 'P5Y');
        $coursepurpose->save();

        list($processedcourses, $processedusers) = $manager->process_approved_deletions();

        $this->assertEquals(0, $processedcourses);
        $this->assertEquals(0, $processedusers);

        $updatedcontext = new expired_context($expiredcontext->get('id'));

        // No change - we just can't process it until the children have finished.
        $this->assertEquals(expired_context::STATUS_APPROVED, $updatedcontext->get('status'));
    }

    /**
     * Ensure that a course context previously flagged as approved for deletion which now has an unflagged child, is
     * updated.
     */
    public function test_process_course_context_outstanding_children() {
        $this->resetAfterTest();

        $this->setup_basics('PT1H', 'PT1H', 'PT1H', 'PT1H');

        $course = $this->getDataGenerator()->create_course([
                'startdate' => time() - (2 * YEARSECS),
                'enddate' => time() - YEARSECS,
            ]);
        $coursecontext = \context_course::instance($course->id);
        $forum = $this->getDataGenerator()->create_module('forum', ['course' => $course->id]);

        // Create an existing expired_context.
        $expiredcontext = new expired_context(0, (object) [
                'contextid' => $coursecontext->id,
                'status' => expired_context::STATUS_APPROVED,
            ]);
        $expiredcontext->save();

        $mockprivacymanager = $this->getMockBuilder(\core_privacy\manager::class)
            ->setMethods([
                'delete_data_for_user',
                'delete_data_for_all_users_in_context',
            ])
            ->getMock();
        $mockprivacymanager->expects($this->never())->method('delete_data_for_user');
        $mockprivacymanager->expects($this->never())->method('delete_data_for_all_users_in_context');

        $manager = $this->getMockBuilder(\tool_dataprivacy\expired_contexts_manager::class)
            ->setMethods(['get_privacy_manager'])
            ->getMock();
        $manager->set_progress(new \null_progress_trace());

        $manager->method('get_privacy_manager')->willReturn($mockprivacymanager);
        list($processedcourses, $processedusers) = $manager->process_approved_deletions();

        $this->assertEquals(0, $processedcourses);
        $this->assertEquals(0, $processedusers);

        $updatedcontext = new expired_context($expiredcontext->get('id'));

        // No change - we just can't process it until the children have finished.
        $this->assertEquals(expired_context::STATUS_APPROVED, $updatedcontext->get('status'));
    }

    /**
     * Ensure that a course context previously flagged as approved for deletion which now has an unflagged child, is
     * updated.
     */
    public function test_process_course_context_pending_children() {
        $this->resetAfterTest();

        $this->setup_basics('PT1H', 'PT1H', 'PT1H', 'PT1H');

        $course = $this->getDataGenerator()->create_course([
                'startdate' => time() - (2 * YEARSECS),
                'enddate' => time() - YEARSECS,
            ]);
        $coursecontext = \context_course::instance($course->id);
        $forum = $this->getDataGenerator()->create_module('forum', ['course' => $course->id]);
        $cm = get_coursemodule_from_instance('forum', $forum->id);
        $forumcontext = \context_module::instance($cm->id);

        // Create an existing expired_context for the course.
        $expiredcoursecontext = new expired_context(0, (object) [
                'contextid' => $coursecontext->id,
                'status' => expired_context::STATUS_APPROVED,
            ]);
        $expiredcoursecontext->save();

        // And for the forum.
        $expiredforumcontext = new expired_context(0, (object) [
                'contextid' => $forumcontext->id,
                'status' => expired_context::STATUS_EXPIRED,
            ]);
        $expiredforumcontext->save();

        $mockprivacymanager = $this->getMockBuilder(\core_privacy\manager::class)
            ->setMethods([
                'delete_data_for_user',
                'delete_data_for_all_users_in_context',
            ])
            ->getMock();
        $mockprivacymanager->expects($this->never())->method('delete_data_for_user');
        $mockprivacymanager->expects($this->never())->method('delete_data_for_all_users_in_context');

        $manager = $this->getMockBuilder(\tool_dataprivacy\expired_contexts_manager::class)
            ->setMethods(['get_privacy_manager'])
            ->getMock();
        $manager->set_progress(new \null_progress_trace());

        $manager->method('get_privacy_manager')->willReturn($mockprivacymanager);
        list($processedcourses, $processedusers) = $manager->process_approved_deletions();

        $this->assertEquals(0, $processedcourses);
        $this->assertEquals(0, $processedusers);

        $updatedcontext = new expired_context($expiredcoursecontext->get('id'));

        // No change - we just can't process it until the children have finished.
        $this->assertEquals(expired_context::STATUS_APPROVED, $updatedcontext->get('status'));
    }

    /**
     * Ensure that a course context previously flagged as approved for deletion which now has an unflagged child, is
     * updated.
     */
    public function test_process_course_context_approved_children() {
        $this->resetAfterTest();

        $this->setup_basics('PT1H', 'PT1H', 'PT1H', 'PT1H');

        $course = $this->getDataGenerator()->create_course([
                'startdate' => time() - (2 * YEARSECS),
                'enddate' => time() - YEARSECS,
            ]);
        $coursecontext = \context_course::instance($course->id);
        $forum = $this->getDataGenerator()->create_module('forum', ['course' => $course->id]);
        $cm = get_coursemodule_from_instance('forum', $forum->id);
        $forumcontext = \context_module::instance($cm->id);

        // Create an existing expired_context for the course.
        $expiredcoursecontext = new expired_context(0, (object) [
                'contextid' => $coursecontext->id,
                'status' => expired_context::STATUS_APPROVED,
            ]);
        $expiredcoursecontext->save();

        // And for the forum.
        $expiredforumcontext = new expired_context(0, (object) [
                'contextid' => $forumcontext->id,
                'status' => expired_context::STATUS_APPROVED,
            ]);
        $expiredforumcontext->save();

        $mockprivacymanager = $this->getMockBuilder(\core_privacy\manager::class)
            ->setMethods([
                'delete_data_for_user',
                'delete_data_for_all_users_in_context',
            ])
            ->getMock();
        $mockprivacymanager->expects($this->never())->method('delete_data_for_user');
        $mockprivacymanager->expects($this->exactly(2))
            ->method('delete_data_for_all_users_in_context')
            ->withConsecutive(
                [$forumcontext],
                [$coursecontext]
            );

        $manager = $this->getMockBuilder(\tool_dataprivacy\expired_contexts_manager::class)
            ->setMethods(['get_privacy_manager'])
            ->getMock();
        $manager->set_progress(new \null_progress_trace());

        $manager->method('get_privacy_manager')->willReturn($mockprivacymanager);

        // Initially only the forum will be processed.
        list($processedcourses, $processedusers) = $manager->process_approved_deletions();

        $this->assertEquals(1, $processedcourses);
        $this->assertEquals(0, $processedusers);

        $updatedcontext = new expired_context($expiredforumcontext->get('id'));
        $this->assertEquals(expired_context::STATUS_CLEANED, $updatedcontext->get('status'));

        // The course won't have been processed yet.
        $updatedcontext = new expired_context($expiredcoursecontext->get('id'));
        $this->assertEquals(expired_context::STATUS_APPROVED, $updatedcontext->get('status'));

        // A subsequent run will cause the course to processed as it is no longer dependent upon the child contexts.
        list($processedcourses, $processedusers) = $manager->process_approved_deletions();

        $this->assertEquals(1, $processedcourses);
        $this->assertEquals(0, $processedusers);
        $updatedcontext = new expired_context($expiredcoursecontext->get('id'));
        $this->assertEquals(expired_context::STATUS_CLEANED, $updatedcontext->get('status'));
    }

    /**
     * Test that the can_process_deletion function returns expected results.
     *
     * @dataProvider    can_process_deletion_provider
     * @param       int     $status
     * @param       bool    $expected
     */
    public function test_can_process_deletion($status, $expected) {
        $purpose = new expired_context(0, (object) [
            'status' => $status,

            'contextid' => \context_system::instance()->id,
        ]);

        $this->assertEquals($expected, $purpose->can_process_deletion());
    }

    /**
     * Data provider for the can_process_deletion tests.
     *
     * @return  array
     */
    public function can_process_deletion_provider() : array {
        return [
            'Pending' => [
                expired_context::STATUS_EXPIRED,
                false,
            ],
            'Approved' => [
                expired_context::STATUS_APPROVED,
                true,
            ],
            'Complete' => [
                expired_context::STATUS_CLEANED,
                false,
            ],
        ];
    }

    /**
     * Test that the is_complete function returns expected results.
     *
     * @dataProvider        is_complete_provider
     * @param       int     $status
     * @param       bool    $expected
     */
    public function test_is_complete($status, $expected) {
        $purpose = new expired_context(0, (object) [
            'status' => $status,
            'contextid' => \context_system::instance()->id,
        ]);

        $this->assertEquals($expected, $purpose->is_complete());
    }

    /**
     * Data provider for the is_complete tests.
     *
     * @return  array
     */
    public function is_complete_provider() : array {
        return [
            'Pending' => [
                expired_context::STATUS_EXPIRED,
                false,
            ],
            'Approved' => [
                expired_context::STATUS_APPROVED,
                false,
            ],
            'Complete' => [
                expired_context::STATUS_CLEANED,
                true,
            ],
        ];
    }

    /**
     * Ensure that any orphaned records are removed once the context has been removed.
     */
    public function test_orphaned_records_are_cleared() {
        $this->resetAfterTest();

        $this->setup_basics('PT1H', 'PT1H', 'PT1H', 'PT1H');

        $course = $this->getDataGenerator()->create_course([
                'startdate' => time() - (2 * YEARSECS),
                'enddate' => time() - YEARSECS,
            ]);
        $context = \context_course::instance($course->id);

        // Flag all expired contexts.
        $manager = new \tool_dataprivacy\expired_contexts_manager();
        $manager->set_progress(new \null_progress_trace());
        list($flaggedcourses, $flaggedusers) = $manager->flag_expired_contexts();

        $this->assertEquals(1, $flaggedcourses);
        $this->assertEquals(0, $flaggedusers);

        // Ensure that the record currently exists.
        $expiredcontext = expired_context::get_record(['contextid' => $context->id]);
        $this->assertNotFalse($expiredcontext);

        // Approve it.
        $expiredcontext->set('status', expired_context::STATUS_APPROVED)->save();

        // Process deletions.
        list($processedcourses, $processedusers) = $manager->process_approved_deletions();

        $this->assertEquals(1, $processedcourses);
        $this->assertEquals(0, $processedusers);

        // Ensure that the record still exists.
        $expiredcontext = expired_context::get_record(['contextid' => $context->id]);
        $this->assertNotFalse($expiredcontext);

        // Remove the actual course.
        delete_course($course->id, false);

        // The record will still exist until we flag it again.
        $expiredcontext = expired_context::get_record(['contextid' => $context->id]);
        $this->assertNotFalse($expiredcontext);

        list($flaggedcourses, $flaggedusers) = $manager->flag_expired_contexts();
        $expiredcontext = expired_context::get_record(['contextid' => $context->id]);
        $this->assertFalse($expiredcontext);
    }

    /**
     * Ensure that the progres tracer works as expected out of the box.
     */
    public function test_progress_tracer_default() {
        $manager = new \tool_dataprivacy\expired_contexts_manager();

        $rc = new \ReflectionClass(\tool_dataprivacy\expired_contexts_manager::class);
        $rcm = $rc->getMethod('get_progress');

        $rcm->setAccessible(true);
        $this->assertInstanceOf(\text_progress_trace::class, $rcm->invoke($manager));
    }

    /**
     * Ensure that the progres tracer works as expected when given a specific traer.
     */
    public function test_progress_tracer_set() {
        $manager = new \tool_dataprivacy\expired_contexts_manager();
        $mytrace = new \null_progress_trace();
        $manager->set_progress($mytrace);

        $rc = new \ReflectionClass(\tool_dataprivacy\expired_contexts_manager::class);
        $rcm = $rc->getMethod('get_progress');

        $rcm->setAccessible(true);
        $this->assertSame($mytrace, $rcm->invoke($manager));
    }

    /**
     * Creates an HTML block on a user.
     *
     * @param   string  $title
     * @param   string  $body
     * @param   string  $format
     * @return  \block_instance
     */
    protected function create_user_block($title, $body, $format) {
        global $USER;

        $configdata = (object) [
            'title' => $title,
            'text' => [
                'itemid' => 19,
                'text' => $body,
                'format' => $format,
            ],
        ];

        $this->create_block($this->construct_user_page($USER));
        $block = $this->get_last_block_on_page($this->construct_user_page($USER));
        $block = block_instance('html', $block->instance);
        $block->instance_config_save((object) $configdata);

        return $block;
    }

    /**
     * Creates an HTML block on a page.
     *
     * @param \page $page Page
     */
    protected function create_block($page) {
        $page->blocks->add_block_at_end_of_default_region('html');
    }

    /**
     * Constructs a Page object for the User Dashboard.
     *
     * @param   \stdClass       $user User to create Dashboard for.
     * @return  \moodle_page
     */
    protected function construct_user_page(\stdClass $user) {
        $page = new \moodle_page();
        $page->set_context(\context_user::instance($user->id));
        $page->set_pagelayout('mydashboard');
        $page->set_pagetype('my-index');
        $page->blocks->load_blocks();
        return $page;
    }

    /**
     * Get the last block on the page.
     *
     * @param \page $page Page
     * @return \block_html Block instance object
     */
    protected function get_last_block_on_page($page) {
        $blocks = $page->blocks->get_blocks_for_region($page->blocks->get_default_region());
        $block = end($blocks);

        return $block;
    }
}
