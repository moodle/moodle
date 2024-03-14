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

namespace tool_dataprivacy;

/**
 * Expired contexts tests.
 *
 * @package    tool_dataprivacy
 * @copyright  2018 David Monllao
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class expired_contexts_test extends \advanced_testcase {

    /**
     * Setup the basics with the specified retention period.
     *
     * @param   string  $system Retention policy for the system.
     * @param   string  $user Retention policy for users.
     * @param   string  $course Retention policy for courses.
     * @param   string  $activity Retention policy for activities.
     */
    protected function setup_basics(string $system, string $user, string $course = null, string $activity = null): \stdClass {
        $this->resetAfterTest();

        $purposes = (object) [
            'system' => $this->create_and_set_purpose_for_contextlevel($system, CONTEXT_SYSTEM),
            'user' => $this->create_and_set_purpose_for_contextlevel($user, CONTEXT_USER),
        ];

        if (null !== $course) {
            $purposes->course = $this->create_and_set_purpose_for_contextlevel($course, CONTEXT_COURSE);
        }

        if (null !== $activity) {
            $purposes->activity = $this->create_and_set_purpose_for_contextlevel($activity, CONTEXT_MODULE);
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
    protected function create_and_set_purpose_for_contextlevel(string $retention, int $contextlevel): purpose {
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
                    \context_helper::get_class_for_level($contextlevel)
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
     * Ensure that a user with a lastaccess in the past and no active enrolments is flagged for deletion.
     */
    public function test_flag_user_update_existing() {
        $this->resetAfterTest();

        $this->setup_basics('PT1H', 'PT1H', 'P5Y');

        $user = $this->getDataGenerator()->create_user(['lastaccess' => time() - YEARSECS]);
        $usercontext = \context_user::instance($user->id);

        // Create an existing expired_context.
        $expiredcontext = new expired_context(0, (object) [
                'contextid' => $usercontext->id,
                'defaultexpired' => 0,
                'status' => expired_context::STATUS_EXPIRED,
            ]);
        $expiredcontext->save();
        $this->assertEquals(0, $expiredcontext->get('defaultexpired'));

        // Flag all expired contexts.
        $manager = new \tool_dataprivacy\expired_contexts_manager();
        list($flaggedcourses, $flaggedusers) = $manager->flag_expired_contexts();

        $this->assertEquals(0, $flaggedcourses);
        $this->assertEquals(1, $flaggedusers);

        // The user context will now have expired.
        $updatedcontext = new expired_context($expiredcontext->get('id'));
        $this->assertEquals(1, $updatedcontext->get('defaultexpired'));
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
    public function test_flag_user_past_override_role() {
        global $DB;
        $this->resetAfterTest();

        $purposes = $this->setup_basics('PT1H', 'PT1H', 'PT1H');

        $user = $this->getDataGenerator()->create_user(['lastaccess' => time() - YEARSECS]);
        $usercontext = \context_user::instance($user->id);
        $systemcontext = \context_system::instance();

        $role = $DB->get_record('role', ['shortname' => 'manager']);

        $override = new purpose_override(0, (object) [
                'purposeid' => $purposes->user->get('id'),
                'roleid' => $role->id,
                'retentionperiod' => 'P5Y',
            ]);
        $override->save();
        role_assign($role->id, $user->id, $systemcontext->id);

        // Flag all expired contexts.
        $manager = new \tool_dataprivacy\expired_contexts_manager();
        list($flaggedcourses, $flaggedusers) = $manager->flag_expired_contexts();

        $this->assertEquals(0, $flaggedcourses);
        $this->assertEquals(0, $flaggedusers);

        $expiredrecord = expired_context::get_record(['contextid' => $usercontext->id]);
        $this->assertFalse($expiredrecord);
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

        $purposes = $this->setup_basics('PT1H', 'PT1H', 'PT1H');

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
        $purposes->user->set('retentionperiod', 'P5Y');
        $purposes->user->save();

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

        $this->setup_basics('PT1H', 'PT1H', 'PT1H');

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

        $this->setup_basics('PT1H', 'PT1H', 'PT1H');
        $this->create_and_set_purpose_for_contextlevel('P5Y', CONTEXT_BLOCK);

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
     * Ensure that a course with an end date in the distant past is flagged, taking into account any purpose override
     */
    public function test_flag_course_past_enddate_with_override_unexpired_role() {
        global $DB;
        $this->resetAfterTest();

        $purposes = $this->setup_basics('PT1H', 'PT1H', 'PT1H');

        $role = $DB->get_record('role', ['shortname' => 'editingteacher']);

        $override = new purpose_override(0, (object) [
                'purposeid' => $purposes->course->get('id'),
                'roleid' => $role->id,
                'retentionperiod' => 'P5Y',
            ]);
        $override->save();

        $course = $this->getDataGenerator()->create_course([
                'startdate' => time() - (2 * DAYSECS),
                'enddate' => time() - DAYSECS,
            ]);
        $coursecontext = \context_course::instance($course->id);

        // Flag all expired contexts.
        $manager = new \tool_dataprivacy\expired_contexts_manager();
        list($flaggedcourses, $flaggedusers) = $manager->flag_expired_contexts();

        $this->assertEquals(1, $flaggedcourses);
        $this->assertEquals(0, $flaggedusers);

        $expiredrecord = expired_context::get_record(['contextid' => $coursecontext->id]);
        $this->assertEmpty($expiredrecord->get('expiredroles'));

        $unexpiredroles = $expiredrecord->get('unexpiredroles');
        $this->assertCount(1, $unexpiredroles);
        $this->assertContainsEquals($role->id, $unexpiredroles);
    }

    /**
     * Ensure that a course with an end date in the distant past is flagged, and any expired role is ignored.
     */
    public function test_flag_course_past_enddate_with_override_expired_role() {
        global $DB;
        $this->resetAfterTest();

        $purposes = $this->setup_basics('PT1H', 'PT1H', 'PT1H');

        $role = $DB->get_record('role', ['shortname' => 'student']);

        // The role has a much shorter retention, but both should match.
        $override = new purpose_override(0, (object) [
                'purposeid' => $purposes->course->get('id'),
                'roleid' => $role->id,
                'retentionperiod' => 'PT1M',
            ]);
        $override->save();

        $course = $this->getDataGenerator()->create_course([
                'startdate' => time() - (2 * DAYSECS),
                'enddate' => time() - DAYSECS,
            ]);
        $coursecontext = \context_course::instance($course->id);

        // Flag all expired contexts.
        $manager = new \tool_dataprivacy\expired_contexts_manager();
        list($flaggedcourses, $flaggedusers) = $manager->flag_expired_contexts();

        $this->assertEquals(1, $flaggedcourses);
        $this->assertEquals(0, $flaggedusers);

        $expiredrecord = expired_context::get_record(['contextid' => $coursecontext->id]);
        $this->assertEmpty($expiredrecord->get('expiredroles'));
        $this->assertEmpty($expiredrecord->get('unexpiredroles'));
        $this->assertTrue((bool) $expiredrecord->get('defaultexpired'));
    }

    /**
     * Ensure that where a course has explicitly expired one role, but that role is explicitly not expired in a child
     * context, does not have the parent context role expired.
     */
    public function test_flag_course_override_expiredwith_override_unexpired_on_child() {
        global $DB;
        $this->resetAfterTest();

        $purposes = $this->setup_basics('P1Y', 'P1Y', 'P1Y');

        $role = $DB->get_record('role', ['shortname' => 'editingteacher']);

        (new purpose_override(0, (object) [
                'purposeid' => $purposes->course->get('id'),
                'roleid' => $role->id,
                'retentionperiod' => 'PT1S',
            ]))->save();

        $modpurpose = new purpose(0, (object) [
            'name' => 'Module purpose',
            'retentionperiod' => 'PT1S',
            'lawfulbases' => 'gdpr_art_6_1_a',
        ]);
        $modpurpose->create();

        (new purpose_override(0, (object) [
                'purposeid' => $modpurpose->get('id'),
                'roleid' => $role->id,
                'retentionperiod' => 'P5Y',
            ]))->save();

        $course = $this->getDataGenerator()->create_course([
                'startdate' => time() - (2 * DAYSECS),
                'enddate' => time() - DAYSECS,
            ]);
        $coursecontext = \context_course::instance($course->id);

        $forum = $this->getDataGenerator()->create_module('forum', ['course' => $course->id]);
        $cm = get_coursemodule_from_instance('forum', $forum->id);
        $forumcontext = \context_module::instance($cm->id);

        api::set_context_instance((object) [
                'contextid' => $forumcontext->id,
                'purposeid' => $modpurpose->get('id'),
                'categoryid' => 0,
            ]);

        // Flag all expired contexts.
        $manager = new \tool_dataprivacy\expired_contexts_manager();
        list($flaggedcourses, $flaggedusers) = $manager->flag_expired_contexts();

        $this->assertEquals(1, $flaggedcourses);
        $this->assertEquals(0, $flaggedusers);

        // The course will not be expired as the default expiry has not passed, and the explicit role override has been
        // removed due to the child non-expiry.
        $expiredrecord = expired_context::get_record(['contextid' => $coursecontext->id]);
        $this->assertFalse($expiredrecord);

        // The forum will have an expiry for all _but_ the overridden role.
        $expiredrecord = expired_context::get_record(['contextid' => $forumcontext->id]);
        $this->assertEmpty($expiredrecord->get('expiredroles'));

        // The teacher is not expired.
        $unexpiredroles = $expiredrecord->get('unexpiredroles');
        $this->assertCount(1, $unexpiredroles);
        $this->assertContainsEquals($role->id, $unexpiredroles);
        $this->assertTrue((bool) $expiredrecord->get('defaultexpired'));
    }

    /**
     * Ensure that a user context previously flagged as approved is not removed if the user has any unexpired roles.
     */
    public function test_process_user_context_with_override_unexpired_role() {
        global $DB;
        $this->resetAfterTest();

        $purposes = $this->setup_basics('PT1H', 'PT1H', 'PT1H');

        $user = $this->getDataGenerator()->create_user(['lastaccess' => time() - YEARSECS]);
        $usercontext = \context_user::instance($user->id);
        $systemcontext = \context_system::instance();

        $role = $DB->get_record('role', ['shortname' => 'manager']);

        $override = new purpose_override(0, (object) [
                'purposeid' => $purposes->user->get('id'),
                'roleid' => $role->id,
                'retentionperiod' => 'P5Y',
            ]);
        $override->save();
        role_assign($role->id, $user->id, $systemcontext->id);

        // Create an existing expired_context.
        $expiredcontext = new expired_context(0, (object) [
                'contextid' => $usercontext->id,
                'defaultexpired' => 1,
                'status' => expired_context::STATUS_APPROVED,
            ]);
        $expiredcontext->add_unexpiredroles([$role->id]);
        $expiredcontext->save();

        $mockprivacymanager = $this->getMockBuilder(\core_privacy\manager::class)
            ->onlyMethods([
                'delete_data_for_user',
                'delete_data_for_users_in_context',
                'delete_data_for_all_users_in_context',
            ])
            ->getMock();
        $mockprivacymanager->expects($this->never())->method('delete_data_for_user');
        $mockprivacymanager->expects($this->never())->method('delete_data_for_all_users_in_context');
        $mockprivacymanager->expects($this->never())->method('delete_data_for_users_in_context');

        $manager = $this->getMockBuilder(\tool_dataprivacy\expired_contexts_manager::class)
            ->onlyMethods(['get_privacy_manager'])
            ->getMock();

        $manager->method('get_privacy_manager')->willReturn($mockprivacymanager);
        $manager->set_progress(new \null_progress_trace());
        list($processedcourses, $processedusers) = $manager->process_approved_deletions();

        $this->assertEquals(0, $processedcourses);
        $this->assertEquals(0, $processedusers);

        $this->expectException('dml_missing_record_exception');
        $updatedcontext = new expired_context($expiredcontext->get('id'));
    }

    /**
     * Ensure that a module context previously flagged as approved is removed with appropriate unexpiredroles kept.
     */
    public function test_process_course_context_with_override_unexpired_role() {
        global $DB;
        $this->resetAfterTest();

        $purposes = $this->setup_basics('PT1H', 'PT1H', 'PT1H');

        $role = $DB->get_record('role', ['shortname' => 'editingteacher']);

        $override = new purpose_override(0, (object) [
                'purposeid' => $purposes->course->get('id'),
                'roleid' => $role->id,
                'retentionperiod' => 'P5Y',
            ]);
        $override->save();

        $course = $this->getDataGenerator()->create_course([
                'startdate' => time() - (2 * YEARSECS),
                'enddate' => time() - YEARSECS,
            ]);
        $forum = $this->getDataGenerator()->create_module('forum', ['course' => $course->id]);
        $cm = get_coursemodule_from_instance('forum', $forum->id);
        $forumcontext = \context_module::instance($cm->id);
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_forum');

        $student = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($student->id, $course->id, 'student');
        $generator->create_discussion((object) [
            'course' => $forum->course,
            'forum' => $forum->id,
            'userid' => $student->id,
        ]);

        $teacher = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($teacher->id, $course->id, 'editingteacher');
        $generator->create_discussion((object) [
            'course' => $forum->course,
            'forum' => $forum->id,
            'userid' => $teacher->id,
        ]);

        // Create an existing expired_context.
        $expiredcontext = new expired_context(0, (object) [
                'contextid' => $forumcontext->id,
                'defaultexpired' => 1,
                'status' => expired_context::STATUS_APPROVED,
            ]);
        $expiredcontext->add_unexpiredroles([$role->id]);
        $expiredcontext->save();

        $mockprivacymanager = $this->getMockBuilder(\core_privacy\manager::class)
            ->onlyMethods([
                'delete_data_for_user',
                'delete_data_for_users_in_context',
                'delete_data_for_all_users_in_context',
            ])
            ->getMock();
        $mockprivacymanager->expects($this->never())->method('delete_data_for_user');
        $mockprivacymanager->expects($this->never())->method('delete_data_for_all_users_in_context');
        $mockprivacymanager
            ->expects($this->once())
            ->method('delete_data_for_users_in_context')
            ->with($this->callback(function($userlist) use ($student, $teacher) {
                $forumlist = $userlist->get_userlist_for_component('mod_forum');
                $userids = $forumlist->get_userids();
                $this->assertCount(1, $userids);
                $this->assertContainsEquals($student->id, $userids);
                $this->assertNotContainsEquals($teacher->id, $userids);
                return true;
            }));

        $manager = $this->getMockBuilder(\tool_dataprivacy\expired_contexts_manager::class)
            ->onlyMethods(['get_privacy_manager'])
            ->getMock();

        $manager->method('get_privacy_manager')->willReturn($mockprivacymanager);
        $manager->set_progress(new \null_progress_trace());
        list($processedcourses, $processedusers) = $manager->process_approved_deletions();

        $this->assertEquals(1, $processedcourses);
        $this->assertEquals(0, $processedusers);

        $updatedcontext = new expired_context($expiredcontext->get('id'));
        $this->assertEquals(expired_context::STATUS_CLEANED, $updatedcontext->get('status'));
    }

    /**
     * Ensure that a module context previously flagged as approved is removed with appropriate expiredroles kept.
     */
    public function test_process_course_context_with_override_expired_role() {
        global $DB;
        $this->resetAfterTest();

        $purposes = $this->setup_basics('PT1H', 'PT1H', 'P5Y');

        $role = $DB->get_record('role', ['shortname' => 'student']);

        $override = new purpose_override(0, (object) [
                'purposeid' => $purposes->course->get('id'),
                'roleid' => $role->id,
                'retentionperiod' => 'PT1M',
            ]);
        $override->save();

        $course = $this->getDataGenerator()->create_course([
                'startdate' => time() - (2 * YEARSECS),
                'enddate' => time() - YEARSECS,
            ]);
        $forum = $this->getDataGenerator()->create_module('forum', ['course' => $course->id]);
        $cm = get_coursemodule_from_instance('forum', $forum->id);
        $forumcontext = \context_module::instance($cm->id);
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_forum');

        $student = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($student->id, $course->id, 'student');
        $generator->create_discussion((object) [
            'course' => $forum->course,
            'forum' => $forum->id,
            'userid' => $student->id,
        ]);

        $teacher = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($teacher->id, $course->id, 'editingteacher');
        $generator->create_discussion((object) [
            'course' => $forum->course,
            'forum' => $forum->id,
            'userid' => $teacher->id,
        ]);

        // Create an existing expired_context.
        $expiredcontext = new expired_context(0, (object) [
                'contextid' => $forumcontext->id,
                'defaultexpired' => 0,
                'status' => expired_context::STATUS_APPROVED,
            ]);
        $expiredcontext->add_expiredroles([$role->id]);
        $expiredcontext->save();

        $mockprivacymanager = $this->getMockBuilder(\core_privacy\manager::class)
            ->onlyMethods([
                'delete_data_for_user',
                'delete_data_for_users_in_context',
                'delete_data_for_all_users_in_context',
            ])
            ->getMock();
        $mockprivacymanager->expects($this->never())->method('delete_data_for_user');
        $mockprivacymanager->expects($this->never())->method('delete_data_for_all_users_in_context');
        $mockprivacymanager
            ->expects($this->once())
            ->method('delete_data_for_users_in_context')
            ->with($this->callback(function($userlist) use ($student, $teacher) {
                $forumlist = $userlist->get_userlist_for_component('mod_forum');
                $userids = $forumlist->get_userids();
                $this->assertCount(1, $userids);
                $this->assertContainsEquals($student->id, $userids);
                $this->assertNotContainsEquals($teacher->id, $userids);
                return true;
            }));

        $manager = $this->getMockBuilder(\tool_dataprivacy\expired_contexts_manager::class)
            ->onlyMethods(['get_privacy_manager'])
            ->getMock();

        $manager->method('get_privacy_manager')->willReturn($mockprivacymanager);
        $manager->set_progress(new \null_progress_trace());
        list($processedcourses, $processedusers) = $manager->process_approved_deletions();

        $this->assertEquals(1, $processedcourses);
        $this->assertEquals(0, $processedusers);

        $updatedcontext = new expired_context($expiredcontext->get('id'));
        $this->assertEquals(expired_context::STATUS_CLEANED, $updatedcontext->get('status'));
    }

    /**
     * Ensure that a module context previously flagged as approved is removed with appropriate expiredroles kept.
     */
    public function test_process_course_context_with_user_in_both_lists() {
        global $DB;
        $this->resetAfterTest();

        $purposes = $this->setup_basics('PT1H', 'PT1H', 'P5Y');

        $role = $DB->get_record('role', ['shortname' => 'student']);

        $override = new purpose_override(0, (object) [
                'purposeid' => $purposes->course->get('id'),
                'roleid' => $role->id,
                'retentionperiod' => 'PT1M',
            ]);
        $override->save();

        $course = $this->getDataGenerator()->create_course([
                'startdate' => time() - (2 * YEARSECS),
                'enddate' => time() - YEARSECS,
            ]);
        $forum = $this->getDataGenerator()->create_module('forum', ['course' => $course->id]);
        $cm = get_coursemodule_from_instance('forum', $forum->id);
        $forumcontext = \context_module::instance($cm->id);
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_forum');

        $teacher = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($teacher->id, $course->id, 'editingteacher');
        $this->getDataGenerator()->enrol_user($teacher->id, $course->id, 'student');
        $generator->create_discussion((object) [
            'course' => $forum->course,
            'forum' => $forum->id,
            'userid' => $teacher->id,
        ]);

        $student = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($student->id, $course->id, 'student');
        $generator->create_discussion((object) [
            'course' => $forum->course,
            'forum' => $forum->id,
            'userid' => $student->id,
        ]);

        // Create an existing expired_context.
        $expiredcontext = new expired_context(0, (object) [
                'contextid' => $forumcontext->id,
                'defaultexpired' => 0,
                'status' => expired_context::STATUS_APPROVED,
            ]);
        $expiredcontext->add_expiredroles([$role->id]);
        $expiredcontext->save();

        $mockprivacymanager = $this->getMockBuilder(\core_privacy\manager::class)
            ->onlyMethods([
                'delete_data_for_user',
                'delete_data_for_users_in_context',
                'delete_data_for_all_users_in_context',
            ])
            ->getMock();
        $mockprivacymanager->expects($this->never())->method('delete_data_for_user');
        $mockprivacymanager->expects($this->never())->method('delete_data_for_all_users_in_context');
        $mockprivacymanager
            ->expects($this->once())
            ->method('delete_data_for_users_in_context')
            ->with($this->callback(function($userlist) use ($student, $teacher) {
                $forumlist = $userlist->get_userlist_for_component('mod_forum');
                $userids = $forumlist->get_userids();
                $this->assertCount(1, $userids);
                $this->assertContainsEquals($student->id, $userids);
                $this->assertNotContainsEquals($teacher->id, $userids);
                return true;
            }));

        $manager = $this->getMockBuilder(\tool_dataprivacy\expired_contexts_manager::class)
            ->onlyMethods(['get_privacy_manager'])
            ->getMock();

        $manager->method('get_privacy_manager')->willReturn($mockprivacymanager);
        $manager->set_progress(new \null_progress_trace());
        list($processedcourses, $processedusers) = $manager->process_approved_deletions();

        $this->assertEquals(1, $processedcourses);
        $this->assertEquals(0, $processedusers);

        $updatedcontext = new expired_context($expiredcontext->get('id'));
        $this->assertEquals(expired_context::STATUS_CLEANED, $updatedcontext->get('status'));
    }

    /**
     * Ensure that a module context previously flagged as approved is removed with appropriate expiredroles kept.
     */
    public function test_process_course_context_with_user_in_both_lists_expired() {
        global $DB;
        $this->resetAfterTest();

        $purposes = $this->setup_basics('PT1H', 'PT1H', 'P5Y');

        $studentrole = $DB->get_record('role', ['shortname' => 'student']);
        $override = new purpose_override(0, (object) [
                'purposeid' => $purposes->course->get('id'),
                'roleid' => $studentrole->id,
                'retentionperiod' => 'PT1M',
            ]);
        $override->save();

        $teacherrole = $DB->get_record('role', ['shortname' => 'editingteacher']);
        $override = new purpose_override(0, (object) [
                'purposeid' => $purposes->course->get('id'),
                'roleid' => $teacherrole->id,
                'retentionperiod' => 'PT1M',
            ]);
        $override->save();

        $course = $this->getDataGenerator()->create_course([
                'startdate' => time() - (2 * YEARSECS),
                'enddate' => time() - YEARSECS,
            ]);
        $forum = $this->getDataGenerator()->create_module('forum', ['course' => $course->id]);
        $cm = get_coursemodule_from_instance('forum', $forum->id);
        $forumcontext = \context_module::instance($cm->id);
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_forum');

        $teacher = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($teacher->id, $course->id, 'editingteacher');
        $this->getDataGenerator()->enrol_user($teacher->id, $course->id, 'student');
        $generator->create_discussion((object) [
            'course' => $forum->course,
            'forum' => $forum->id,
            'userid' => $teacher->id,
        ]);

        $student = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($student->id, $course->id, 'student');
        $generator->create_discussion((object) [
            'course' => $forum->course,
            'forum' => $forum->id,
            'userid' => $student->id,
        ]);

        // Create an existing expired_context.
        $expiredcontext = new expired_context(0, (object) [
                'contextid' => $forumcontext->id,
                'defaultexpired' => 0,
                'status' => expired_context::STATUS_APPROVED,
            ]);
        $expiredcontext->add_expiredroles([$studentrole->id, $teacherrole->id]);
        $expiredcontext->save();

        $mockprivacymanager = $this->getMockBuilder(\core_privacy\manager::class)
            ->onlyMethods([
                'delete_data_for_user',
                'delete_data_for_users_in_context',
                'delete_data_for_all_users_in_context',
            ])
            ->getMock();
        $mockprivacymanager->expects($this->never())->method('delete_data_for_user');
        $mockprivacymanager->expects($this->never())->method('delete_data_for_all_users_in_context');
        $mockprivacymanager
            ->expects($this->once())
            ->method('delete_data_for_users_in_context')
            ->with($this->callback(function($userlist) use ($student, $teacher) {
                $forumlist = $userlist->get_userlist_for_component('mod_forum');
                $userids = $forumlist->get_userids();
                $this->assertCount(2, $userids);
                $this->assertContainsEquals($student->id, $userids);
                $this->assertContainsEquals($teacher->id, $userids);
                return true;
            }));

        $manager = $this->getMockBuilder(\tool_dataprivacy\expired_contexts_manager::class)
            ->onlyMethods(['get_privacy_manager'])
            ->getMock();

        $manager->method('get_privacy_manager')->willReturn($mockprivacymanager);
        $manager->set_progress(new \null_progress_trace());
        list($processedcourses, $processedusers) = $manager->process_approved_deletions();

        $this->assertEquals(1, $processedcourses);
        $this->assertEquals(0, $processedusers);

        $updatedcontext = new expired_context($expiredcontext->get('id'));
        $this->assertEquals(expired_context::STATUS_CLEANED, $updatedcontext->get('status'));
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
            ->onlyMethods([
                'delete_data_for_user',
                'delete_data_for_all_users_in_context',
            ])
            ->getMock();
        $mockprivacymanager->expects($this->never())->method('delete_data_for_user');
        $mockprivacymanager->expects($this->never())->method('delete_data_for_all_users_in_context');

        $manager = $this->getMockBuilder(\tool_dataprivacy\expired_contexts_manager::class)
            ->onlyMethods(['get_privacy_manager'])
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
            ->onlyMethods([
                'delete_data_for_user',
                'delete_data_for_all_users_in_context',
            ])
            ->getMock();
        $mockprivacymanager->expects($this->never())->method('delete_data_for_user');
        $mockprivacymanager->expects($this->never())->method('delete_data_for_all_users_in_context');

        $manager = $this->getMockBuilder(\tool_dataprivacy\expired_contexts_manager::class)
            ->onlyMethods(['get_privacy_manager'])
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
            ->onlyMethods([
                'delete_data_for_user',
                'delete_data_for_all_users_in_context',
            ])
            ->getMock();
        $mockprivacymanager->expects($this->never())->method('delete_data_for_user');
        $mockprivacymanager->expects($this->never())->method('delete_data_for_all_users_in_context');

        $manager = $this->getMockBuilder(\tool_dataprivacy\expired_contexts_manager::class)
            ->onlyMethods(['get_privacy_manager'])
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
            ->onlyMethods([
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
            ->onlyMethods(['get_privacy_manager'])
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
            ->onlyMethods([
                'delete_data_for_user',
                'delete_data_for_all_users_in_context',
            ])
            ->getMock();
        $mockprivacymanager->expects($this->never())->method('delete_data_for_user');
        $mockprivacymanager->expects($this->once())->method('delete_data_for_all_users_in_context');

        $manager = $this->getMockBuilder(\tool_dataprivacy\expired_contexts_manager::class)
            ->onlyMethods(['get_privacy_manager'])
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
            ->onlyMethods([
                'delete_data_for_user',
                'delete_data_for_all_users_in_context',
            ])
            ->getMock();
        $mockprivacymanager->expects($this->never())->method('delete_data_for_user');
        $mockprivacymanager->expects($this->never())->method('delete_data_for_all_users_in_context');

        $manager = $this->getMockBuilder(\tool_dataprivacy\expired_contexts_manager::class)
            ->onlyMethods(['get_privacy_manager'])
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
            ->onlyMethods([
                'delete_data_for_user',
                'delete_data_for_all_users_in_context',
            ])
            ->getMock();
        $mockprivacymanager->expects($this->never())->method('delete_data_for_user');
        $mockprivacymanager->expects($this->never())->method('delete_data_for_all_users_in_context');

        $manager = $this->getMockBuilder(\tool_dataprivacy\expired_contexts_manager::class)
            ->onlyMethods(['get_privacy_manager'])
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

        $this->setup_basics('PT1H', 'PT1H', 'PT1H');

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
            ->onlyMethods([
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
            ->onlyMethods(['get_privacy_manager'])
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

        $this->setup_basics('PT1H', 'PT1H', 'PT1H');
        $this->create_and_set_purpose_for_contextlevel('P5Y', CONTEXT_BLOCK);

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
            ->onlyMethods([
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
            ->onlyMethods(['get_privacy_manager'])
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
            ->onlyMethods([
                'delete_data_for_user',
                'delete_data_for_all_users_in_context',
            ])
            ->getMock();
        $mockprivacymanager->expects($this->never())->method('delete_data_for_user');
        $mockprivacymanager->expects($this->never())->method('delete_data_for_all_users_in_context');

        $manager = $this->getMockBuilder(\tool_dataprivacy\expired_contexts_manager::class)
            ->onlyMethods(['get_privacy_manager'])
            ->getMock();
        $manager->set_progress(new \null_progress_trace());
        $manager->method('get_privacy_manager')->willReturn($mockprivacymanager);

        // Changing the retention period to a longer period will remove the expired_context record.
        $purposes->activity->set('retentionperiod', 'P5Y');
        $purposes->activity->save();

        list($processedcourses, $processedusers) = $manager->process_approved_deletions();

        $this->assertEquals(0, $processedcourses);
        $this->assertEquals(0, $processedusers);

        $this->expectException('dml_missing_record_exception');
        $updatedcontext = new expired_context($expiredcontext->get('id'));
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
            ->onlyMethods([
                'delete_data_for_user',
                'delete_data_for_all_users_in_context',
            ])
            ->getMock();
        $mockprivacymanager->expects($this->never())->method('delete_data_for_user');
        $mockprivacymanager->expects($this->never())->method('delete_data_for_all_users_in_context');

        $manager = $this->getMockBuilder(\tool_dataprivacy\expired_contexts_manager::class)
            ->onlyMethods(['get_privacy_manager'])
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
            ->onlyMethods([
                'delete_data_for_user',
                'delete_data_for_all_users_in_context',
            ])
            ->getMock();
        $mockprivacymanager->expects($this->never())->method('delete_data_for_user');
        $mockprivacymanager->expects($this->never())->method('delete_data_for_all_users_in_context');

        $manager = $this->getMockBuilder(\tool_dataprivacy\expired_contexts_manager::class)
            ->onlyMethods(['get_privacy_manager'])
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
            ->onlyMethods([
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
            ->onlyMethods(['get_privacy_manager'])
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
    public function can_process_deletion_provider(): array {
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
    public function is_complete_provider(): array {
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
     * Test that the is_fully_expired function returns expected results.
     *
     * @dataProvider        is_fully_expired_provider
     * @param       array   $record
     * @param       bool    $expected
     */
    public function test_is_fully_expired($record, $expected) {
        $purpose = new expired_context(0, (object) $record);

        $this->assertEquals($expected, $purpose->is_fully_expired());
    }

    /**
     * Data provider for the is_fully_expired tests.
     *
     * @return  array
     */
    public function is_fully_expired_provider(): array {
        return [
            'Fully expired' => [
                [
                    'status' => expired_context::STATUS_APPROVED,
                    'defaultexpired' => 1,
                ],
                true,
            ],
            'Unexpired roles present' => [
                [
                    'status' => expired_context::STATUS_APPROVED,
                    'defaultexpired' => 1,
                    'unexpiredroles' => json_encode([1]),
                ],
                false,
            ],
            'Only some expired roles present' => [
                [
                    'status' => expired_context::STATUS_APPROVED,
                    'defaultexpired' => 0,
                    'expiredroles' => json_encode([1]),
                ],
                false,
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

    /**
     * Test the is_context_expired functions when supplied with the system context.
     */
    public function test_is_context_expired_system() {
        $this->resetAfterTest();
        $this->setup_basics('PT1H', 'PT1H', 'P1D');
        $user = $this->getDataGenerator()->create_user(['lastaccess' => time() - YEARSECS]);

        $this->assertFalse(expired_contexts_manager::is_context_expired(\context_system::instance()));
        $this->assertFalse(
                expired_contexts_manager::is_context_expired_or_unprotected_for_user(\context_system::instance(), $user));
    }

    /**
     * Test the is_context_expired functions when supplied with a block in the user context.
     *
     * Children of a user context always follow the user expiry rather than any context level defaults (e.g. at the
     * block level.
     */
    public function test_is_context_expired_user_block() {
        $this->resetAfterTest();

        $purposes = $this->setup_basics('PT1H', 'PT1H', 'P1D');
        $purposes->block = $this->create_and_set_purpose_for_contextlevel('P5Y', CONTEXT_BLOCK);

        $user = $this->getDataGenerator()->create_user(['lastaccess' => time() - YEARSECS]);
        $this->setUser($user);
        $block = $this->create_user_block('Title', 'Content', FORMAT_PLAIN);
        $blockcontext = \context_block::instance($block->instance->id);
        $this->setUser();

        // Protected flags have no bearing on expiry of user subcontexts.
        $this->assertTrue(expired_contexts_manager::is_context_expired($blockcontext));

        $purposes->block->set('protected', 1)->save();
        $this->assertTrue(expired_contexts_manager::is_context_expired_or_unprotected_for_user($blockcontext, $user));

        $purposes->block->set('protected', 0)->save();
        $this->assertTrue(expired_contexts_manager::is_context_expired_or_unprotected_for_user($blockcontext, $user));
    }

    /**
     * Test the is_context_expired functions when supplied with the front page course.
     */
    public function test_is_context_expired_frontpage() {
        $this->resetAfterTest();

        $purposes = $this->setup_basics('PT1H', 'PT1H', 'P1D');

        $frontcourse = get_site();
        $frontcoursecontext = \context_course::instance($frontcourse->id);

        $sitenews = $this->getDataGenerator()->create_module('forum', ['course' => $frontcourse->id]);
        $cm = get_coursemodule_from_instance('forum', $sitenews->id);
        $sitenewscontext = \context_module::instance($cm->id);

        $user = $this->getDataGenerator()->create_user(['lastaccess' => time() - YEARSECS]);

        $this->assertFalse(expired_contexts_manager::is_context_expired($frontcoursecontext));
        $this->assertFalse(expired_contexts_manager::is_context_expired($sitenewscontext));

        $this->assertTrue(expired_contexts_manager::is_context_expired_or_unprotected_for_user($frontcoursecontext, $user));
        $this->assertTrue(expired_contexts_manager::is_context_expired_or_unprotected_for_user($sitenewscontext, $user));

        // Protecting the course contextlevel does not impact the front page.
        $purposes->course->set('protected', 1)->save();
        $this->assertTrue(expired_contexts_manager::is_context_expired_or_unprotected_for_user($frontcoursecontext, $user));
        $this->assertTrue(expired_contexts_manager::is_context_expired_or_unprotected_for_user($sitenewscontext, $user));

        // Protecting the system contextlevel affects the front page, too.
        $purposes->system->set('protected', 1)->save();
        $this->assertFalse(expired_contexts_manager::is_context_expired_or_unprotected_for_user($frontcoursecontext, $user));
        $this->assertFalse(expired_contexts_manager::is_context_expired_or_unprotected_for_user($sitenewscontext, $user));
    }

    /**
     * Test the is_context_expired functions when supplied with an expired course.
     */
    public function test_is_context_expired_course_expired() {
        $this->resetAfterTest();

        $purposes = $this->setup_basics('PT1H', 'PT1H', 'P1D');

        $user = $this->getDataGenerator()->create_user(['lastaccess' => time() - YEARSECS]);
        $course = $this->getDataGenerator()->create_course(['startdate' => time() - YEARSECS, 'enddate' => time()]);
        $coursecontext = \context_course::instance($course->id);

        $this->getDataGenerator()->enrol_user($user->id, $course->id, 'student');

        $this->assertFalse(expired_contexts_manager::is_context_expired($coursecontext));

        $purposes->course->set('protected', 1)->save();
        $this->assertFalse(expired_contexts_manager::is_context_expired_or_unprotected_for_user($coursecontext, $user));

        $purposes->course->set('protected', 0)->save();
        $this->assertTrue(expired_contexts_manager::is_context_expired_or_unprotected_for_user($coursecontext, $user));
    }

    /**
     * Test the is_context_expired functions when supplied with an unexpired course.
     */
    public function test_is_context_expired_course_unexpired() {
        $this->resetAfterTest();

        $purposes = $this->setup_basics('PT1H', 'PT1H', 'P1D');

        $user = $this->getDataGenerator()->create_user(['lastaccess' => time() - YEARSECS]);
        $course = $this->getDataGenerator()->create_course(['startdate' => time() - YEARSECS, 'enddate' => time() - WEEKSECS]);
        $coursecontext = \context_course::instance($course->id);

        $this->getDataGenerator()->enrol_user($user->id, $course->id, 'student');

        $this->assertTrue(expired_contexts_manager::is_context_expired($coursecontext));

        $purposes->course->set('protected', 1)->save();
        $this->assertTrue(expired_contexts_manager::is_context_expired_or_unprotected_for_user($coursecontext, $user));

        $purposes->course->set('protected', 0)->save();
        $this->assertTrue(expired_contexts_manager::is_context_expired_or_unprotected_for_user($coursecontext, $user));
    }

    /**
     * Test the is_context_expired functions when supplied with an unexpired course and a child context in the course which is protected.
     *
     * When a child context has a specific purpose set, then that purpose should be respected with respect to the
     * course.
     *
     * If the course is still within the expiry period for the child context, then that child's protected flag should be
     * respected, even when the course may have expired.
     */
    public function test_is_child_context_expired_course_unexpired_with_child() {
        $this->resetAfterTest();

        $purposes = $this->setup_basics('PT1H', 'PT1H', 'P1D', 'P1D');
        $purposes->course->set('protected', 0)->save();
        $purposes->activity->set('protected', 1)->save();

        $user = $this->getDataGenerator()->create_user(['lastaccess' => time() - YEARSECS]);
        $course = $this->getDataGenerator()->create_course(['startdate' => time() - YEARSECS, 'enddate' => time() + WEEKSECS]);
        $forum = $this->getDataGenerator()->create_module('forum', ['course' => $course->id]);

        $coursecontext = \context_course::instance($course->id);
        $cm = get_coursemodule_from_instance('forum', $forum->id);
        $forumcontext = \context_module::instance($cm->id);

        $this->getDataGenerator()->enrol_user($user->id, $course->id, 'student');

        $this->assertFalse(expired_contexts_manager::is_context_expired($coursecontext));
        $this->assertFalse(expired_contexts_manager::is_context_expired($forumcontext));

        $this->assertTrue(expired_contexts_manager::is_context_expired_or_unprotected_for_user($coursecontext, $user));
        $this->assertFalse(expired_contexts_manager::is_context_expired_or_unprotected_for_user($forumcontext, $user));

        $purposes->activity->set('protected', 0)->save();
        $this->assertTrue(expired_contexts_manager::is_context_expired_or_unprotected_for_user($forumcontext, $user));
    }

    /**
     * Test the is_context_expired functions when supplied with an expired course which has role overrides.
     */
    public function test_is_context_expired_course_expired_override() {
        global $DB;

        $this->resetAfterTest();

        $purposes = $this->setup_basics('PT1H', 'PT1H', 'PT1H');

        $user = $this->getDataGenerator()->create_user(['lastaccess' => time() - YEARSECS]);
        $course = $this->getDataGenerator()->create_course(['startdate' => time() - YEARSECS, 'enddate' => time() - WEEKSECS]);
        $coursecontext = \context_course::instance($course->id);
        $systemcontext = \context_system::instance();

        $role = $DB->get_record('role', ['shortname' => 'manager']);
        $override = new purpose_override(0, (object) [
                'purposeid' => $purposes->course->get('id'),
                'roleid' => $role->id,
                'retentionperiod' => 'P5Y',
            ]);
        $override->save();
        role_assign($role->id, $user->id, $systemcontext->id);

        $this->getDataGenerator()->enrol_user($user->id, $course->id, 'student');

        $this->assertFalse(expired_contexts_manager::is_context_expired($coursecontext));

        $purposes->course->set('protected', 1)->save();
        $this->assertTrue(expired_contexts_manager::is_context_expired_or_unprotected_for_user($coursecontext, $user));

        $purposes->course->set('protected', 0)->save();
        $this->assertTrue(expired_contexts_manager::is_context_expired_or_unprotected_for_user($coursecontext, $user));
    }

    /**
     * Test the is_context_expired functions when supplied with an expired course which has role overrides.
     */
    public function test_is_context_expired_course_expired_override_parent() {
        global $DB;

        $this->resetAfterTest();

        $purposes = $this->setup_basics('PT1H', 'PT1H');

        $user = $this->getDataGenerator()->create_user(['lastaccess' => time() - YEARSECS]);
        $course = $this->getDataGenerator()->create_course(['startdate' => time() - YEARSECS, 'enddate' => time() - WEEKSECS]);
        $coursecontext = \context_course::instance($course->id);
        $systemcontext = \context_system::instance();

        $role = $DB->get_record('role', ['shortname' => 'manager']);
        $override = new purpose_override(0, (object) [
                'purposeid' => $purposes->system->get('id'),
                'roleid' => $role->id,
                'retentionperiod' => 'P5Y',
            ]);
        $override->save();
        role_assign($role->id, $user->id, $systemcontext->id);

        $this->getDataGenerator()->enrol_user($user->id, $course->id, 'student');

        $this->assertFalse(expired_contexts_manager::is_context_expired($coursecontext));

        // The user override applies to this user. THIs means that the default expiry has no effect.
        $this->assertTrue(expired_contexts_manager::is_context_expired_or_unprotected_for_user($coursecontext, $user));

        $purposes->system->set('protected', 1)->save();
        $this->assertTrue(expired_contexts_manager::is_context_expired_or_unprotected_for_user($coursecontext, $user));

        $purposes->system->set('protected', 0)->save();
        $this->assertTrue(expired_contexts_manager::is_context_expired_or_unprotected_for_user($coursecontext, $user));

        $override->set('protected', 1)->save();
        $this->assertFalse(expired_contexts_manager::is_context_expired_or_unprotected_for_user($coursecontext, $user));

        $purposes->system->set('protected', 1)->save();
        $this->assertFalse(expired_contexts_manager::is_context_expired_or_unprotected_for_user($coursecontext, $user));

        $purposes->system->set('protected', 0)->save();
        $this->assertFalse(expired_contexts_manager::is_context_expired_or_unprotected_for_user($coursecontext, $user));

    }

    /**
     * Test the is_context_expired functions when supplied with an expired course which has role overrides but the user
     * does not hold the role.
     */
    public function test_is_context_expired_course_expired_override_parent_no_role() {
        global $DB;

        $this->resetAfterTest();

        $purposes = $this->setup_basics('PT1H', 'PT1H');

        $user = $this->getDataGenerator()->create_user(['lastaccess' => time() - YEARSECS]);
        $course = $this->getDataGenerator()->create_course(['startdate' => time() - YEARSECS, 'enddate' => time() - WEEKSECS]);
        $coursecontext = \context_course::instance($course->id);
        $systemcontext = \context_system::instance();

        $role = $DB->get_record('role', ['shortname' => 'manager']);
        $override = new purpose_override(0, (object) [
                'purposeid' => $purposes->system->get('id'),
                'roleid' => $role->id,
                'retentionperiod' => 'P5Y',
            ]);
        $override->save();

        $this->getDataGenerator()->enrol_user($user->id, $course->id, 'student');

        // This context is not _fully _ expired.
        $this->assertFalse(expired_contexts_manager::is_context_expired($coursecontext));
    }

    /**
     * Test the is_context_expired functions when supplied with an unexpired course which has role overrides.
     */
    public function test_is_context_expired_course_expired_override_inverse() {
        global $DB;

        $this->resetAfterTest();

        $purposes = $this->setup_basics('P1Y', 'P1Y');

        $user = $this->getDataGenerator()->create_user(['lastaccess' => time() - YEARSECS]);
        $course = $this->getDataGenerator()->create_course(['startdate' => time() - YEARSECS, 'enddate' => time() - WEEKSECS]);
        $coursecontext = \context_course::instance($course->id);
        $systemcontext = \context_system::instance();

        $role = $DB->get_record('role', ['shortname' => 'student']);
        $override = new purpose_override(0, (object) [
                'purposeid' => $purposes->system->get('id'),
                'roleid' => $role->id,
                'retentionperiod' => 'PT1S',
            ]);
        $override->save();

        $this->getDataGenerator()->enrol_user($user->id, $course->id, 'student');

        // This context is not _fully _ expired.
        $this->assertFalse(expired_contexts_manager::is_context_expired($coursecontext));
    }

    /**
     * Test the is_context_expired functions when supplied with an unexpired course which has role overrides.
     */
    public function test_is_context_expired_course_expired_override_inverse_parent() {
        global $DB;

        $this->resetAfterTest();

        $purposes = $this->setup_basics('P1Y', 'P1Y');

        $user = $this->getDataGenerator()->create_user(['lastaccess' => time() - YEARSECS]);
        $course = $this->getDataGenerator()->create_course(['startdate' => time() - YEARSECS, 'enddate' => time() - WEEKSECS]);
        $coursecontext = \context_course::instance($course->id);
        $systemcontext = \context_system::instance();

        $role = $DB->get_record('role', ['shortname' => 'manager']);
        $override = new purpose_override(0, (object) [
                'purposeid' => $purposes->system->get('id'),
                'roleid' => $role->id,
                'retentionperiod' => 'PT1S',
            ]);
        $override->save();

        $this->getDataGenerator()->enrol_user($user->id, $course->id, 'student');
        role_assign($role->id, $user->id, $systemcontext->id);

        $studentrole = $DB->get_record('role', ['shortname' => 'student']);
        role_unassign($studentrole->id, $user->id, $coursecontext->id);

        // This context is not _fully _ expired.
        $this->assertFalse(expired_contexts_manager::is_context_expired($coursecontext));
    }

    /**
     * Test the is_context_expired functions when supplied with an unexpired course which has role overrides.
     */
    public function test_is_context_expired_course_expired_override_inverse_parent_not_assigned() {
        global $DB;

        $this->resetAfterTest();

        $purposes = $this->setup_basics('P1Y', 'P1Y');

        $user = $this->getDataGenerator()->create_user(['lastaccess' => time() - YEARSECS]);
        $course = $this->getDataGenerator()->create_course(['startdate' => time() - YEARSECS, 'enddate' => time() - WEEKSECS]);
        $coursecontext = \context_course::instance($course->id);
        $systemcontext = \context_system::instance();

        $role = $DB->get_record('role', ['shortname' => 'manager']);
        $override = new purpose_override(0, (object) [
                'purposeid' => $purposes->system->get('id'),
                'roleid' => $role->id,
                'retentionperiod' => 'PT1S',
            ]);
        $override->save();

        // Enrol the user in the course without any role.
        $this->getDataGenerator()->enrol_user($user->id, $course->id, 'student');
        $studentrole = $DB->get_record('role', ['shortname' => 'student']);
        role_unassign($studentrole->id, $user->id, $coursecontext->id);

        // This context is not _fully _ expired.
        $this->assertFalse(expired_contexts_manager::is_context_expired($coursecontext));
    }

    /**
     * Ensure that context expired checks for a specific user taken into account roles.
     */
    public function test_is_context_expired_or_unprotected_for_user_role_mixtures_protected() {
        global $DB;

        $this->resetAfterTest();

        $purposes = $this->setup_basics('PT1S', 'PT1S', 'PT1S');

        $course = $this->getDataGenerator()->create_course(['startdate' => time() - YEARSECS, 'enddate' => time() - DAYSECS]);
        $coursecontext = \context_course::instance($course->id);
        $systemcontext = \context_system::instance();

        $roles = $DB->get_records_menu('role', [], 'id', 'shortname, id');
        $override = new purpose_override(0, (object) [
                'purposeid' => $purposes->course->get('id'),
                'roleid' => $roles['manager'],
                'retentionperiod' => 'P1W',
                'protected' => 1,
            ]);
        $override->save();

        $s = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($s->id, $course->id, 'student');

        $t = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($t->id, $course->id, 'teacher');

        $sm = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($sm->id, $course->id, 'student');
        role_assign($roles['manager'], $sm->id, $coursecontext->id);

        $m = $this->getDataGenerator()->create_user();
        role_assign($roles['manager'], $m->id, $coursecontext->id);

        $tm = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($t->id, $course->id, 'teacher');
        role_assign($roles['manager'], $tm->id, $coursecontext->id);

        // The context should only be expired for users who are not a manager.
        $this->assertTrue(expired_contexts_manager::is_context_expired_or_unprotected_for_user($coursecontext, $s));
        $this->assertTrue(expired_contexts_manager::is_context_expired_or_unprotected_for_user($coursecontext, $t));
        $this->assertFalse(expired_contexts_manager::is_context_expired_or_unprotected_for_user($coursecontext, $sm));
        $this->assertFalse(expired_contexts_manager::is_context_expired_or_unprotected_for_user($coursecontext, $tm));
        $this->assertFalse(expired_contexts_manager::is_context_expired_or_unprotected_for_user($coursecontext, $m));

        $override->set('protected', 0)->save();
        $this->assertTrue(expired_contexts_manager::is_context_expired_or_unprotected_for_user($coursecontext, $s));
        $this->assertTrue(expired_contexts_manager::is_context_expired_or_unprotected_for_user($coursecontext, $t));
        $this->assertTrue(expired_contexts_manager::is_context_expired_or_unprotected_for_user($coursecontext, $sm));
        $this->assertTrue(expired_contexts_manager::is_context_expired_or_unprotected_for_user($coursecontext, $tm));
        $this->assertTrue(expired_contexts_manager::is_context_expired_or_unprotected_for_user($coursecontext, $m));
    }

    /**
     * Ensure that context expired checks for a specific user taken into account roles when retention is inversed.
     */
    public function test_is_context_expired_or_unprotected_for_user_role_mixtures_protected_inverse() {
        global $DB;

        $this->resetAfterTest();

        $purposes = $this->setup_basics('P5Y', 'P5Y', 'P5Y');

        $course = $this->getDataGenerator()->create_course(['startdate' => time() - YEARSECS, 'enddate' => time() - DAYSECS]);
        $coursecontext = \context_course::instance($course->id);
        $systemcontext = \context_system::instance();

        $roles = $DB->get_records_menu('role', [], 'id', 'shortname, id');
        $override = new purpose_override(0, (object) [
                'purposeid' => $purposes->course->get('id'),
                'roleid' => $roles['student'],
                'retentionperiod' => 'PT1S',
            ]);
        $override->save();

        $s = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($s->id, $course->id, 'student');

        $t = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($t->id, $course->id, 'teacher');

        $sm = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($sm->id, $course->id, 'student');
        role_assign($roles['manager'], $sm->id, $coursecontext->id);

        $m = $this->getDataGenerator()->create_user();
        role_assign($roles['manager'], $m->id, $coursecontext->id);

        $tm = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($t->id, $course->id, 'teacher');
        role_assign($roles['manager'], $tm->id, $coursecontext->id);

        // The context should only be expired for users who are only a student.
        $purposes->course->set('protected', 1)->save();
        $override->set('protected', 1)->save();
        $this->assertTrue(expired_contexts_manager::is_context_expired_or_unprotected_for_user($coursecontext, $s));
        $this->assertFalse(expired_contexts_manager::is_context_expired_or_unprotected_for_user($coursecontext, $t));
        $this->assertFalse(expired_contexts_manager::is_context_expired_or_unprotected_for_user($coursecontext, $sm));
        $this->assertFalse(expired_contexts_manager::is_context_expired_or_unprotected_for_user($coursecontext, $tm));
        $this->assertFalse(expired_contexts_manager::is_context_expired_or_unprotected_for_user($coursecontext, $m));

        $purposes->course->set('protected', 0)->save();
        $this->assertTrue(expired_contexts_manager::is_context_expired_or_unprotected_for_user($coursecontext, $s));
        $this->assertTrue(expired_contexts_manager::is_context_expired_or_unprotected_for_user($coursecontext, $t));
        $this->assertTrue(expired_contexts_manager::is_context_expired_or_unprotected_for_user($coursecontext, $sm));
        $this->assertTrue(expired_contexts_manager::is_context_expired_or_unprotected_for_user($coursecontext, $tm));
        $this->assertTrue(expired_contexts_manager::is_context_expired_or_unprotected_for_user($coursecontext, $m));
    }
}
