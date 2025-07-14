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
 * Data provider tests.
 *
 * @package    mod_lesson
 * @category   test
 * @copyright  2018 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_lesson\privacy;

defined('MOODLE_INTERNAL') || die();
global $CFG;

use core_privacy\tests\provider_testcase;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\transform;
use core_privacy\local\request\writer;
use mod_lesson\privacy\provider;

/**
 * Data provider testcase class.
 *
 * @package    mod_lesson
 * @category   test
 * @copyright  2018 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class provider_test extends provider_testcase {

    public function setUp(): void {
        global $PAGE;
        parent::setUp();
        $this->setAdminUser();  // The data generator complains without this.
        $this->resetAfterTest();
        $PAGE->get_renderer('core');
    }

    public function test_get_contexts_for_userid(): void {
        $dg = $this->getDataGenerator();
        $c1 = $dg->create_course();

        $u1 = $dg->create_user();
        $u2 = $dg->create_user();
        $u3 = $dg->create_user();
        $u4 = $dg->create_user();
        $u5 = $dg->create_user();
        $u6 = $dg->create_user();

        $cm1 = $dg->create_module('lesson', ['course' => $c1]);
        $cm2 = $dg->create_module('lesson', ['course' => $c1]);
        $cm3 = $dg->create_module('lesson', ['course' => $c1]);
        $cm1ctx = \context_module::instance($cm1->cmid);
        $cm2ctx = \context_module::instance($cm2->cmid);
        $cm3ctx = \context_module::instance($cm3->cmid);

        $this->create_attempt($cm1, $u1);
        $this->create_grade($cm2, $u2);
        $this->create_timer($cm3, $u3);
        $this->create_branch($cm2, $u4);
        $this->create_override($cm1, $u5);

        $this->create_attempt($cm2, $u6);
        $this->create_grade($cm2, $u6);
        $this->create_timer($cm1, $u6);
        $this->create_branch($cm2, $u6);
        $this->create_override($cm3, $u6);

        $contextids = provider::get_contexts_for_userid($u1->id)->get_contextids();
        $this->assertCount(1, $contextids);
        $this->assertTrue(in_array($cm1ctx->id, $contextids));

        $contextids = provider::get_contexts_for_userid($u2->id)->get_contextids();
        $this->assertCount(1, $contextids);
        $this->assertTrue(in_array($cm2ctx->id, $contextids));

        $contextids = provider::get_contexts_for_userid($u3->id)->get_contextids();
        $this->assertCount(1, $contextids);
        $this->assertTrue(in_array($cm3ctx->id, $contextids));

        $contextids = provider::get_contexts_for_userid($u4->id)->get_contextids();
        $this->assertCount(1, $contextids);
        $this->assertTrue(in_array($cm2ctx->id, $contextids));

        $contextids = provider::get_contexts_for_userid($u5->id)->get_contextids();
        $this->assertCount(1, $contextids);
        $this->assertTrue(in_array($cm1ctx->id, $contextids));

        $contextids = provider::get_contexts_for_userid($u6->id)->get_contextids();
        $this->assertCount(3, $contextids);
        $this->assertTrue(in_array($cm1ctx->id, $contextids));
        $this->assertTrue(in_array($cm2ctx->id, $contextids));
        $this->assertTrue(in_array($cm3ctx->id, $contextids));
    }

    /*
     * Test for provider::get_users_in_context().
     */
    public function test_get_users_in_context(): void {
        $dg = $this->getDataGenerator();
        $c1 = $dg->create_course();
        $component = 'mod_lesson';

        $u1 = $dg->create_user();
        $u2 = $dg->create_user();
        $u3 = $dg->create_user();
        $u4 = $dg->create_user();
        $u5 = $dg->create_user();
        $u6 = $dg->create_user();

        $cm1 = $dg->create_module('lesson', ['course' => $c1]);
        $cm2 = $dg->create_module('lesson', ['course' => $c1]);

        $cm1ctx = \context_module::instance($cm1->cmid);
        $cm2ctx = \context_module::instance($cm2->cmid);

        $this->create_attempt($cm1, $u1);
        $this->create_grade($cm1, $u2);
        $this->create_timer($cm1, $u3);
        $this->create_branch($cm1, $u4);
        $this->create_override($cm1, $u5);

        $this->create_attempt($cm2, $u6);
        $this->create_grade($cm2, $u6);
        $this->create_timer($cm2, $u6);
        $this->create_branch($cm2, $u6);
        $this->create_override($cm2, $u6);

        $context = \context_module::instance($cm1->cmid);
        $userlist = new \core_privacy\local\request\userlist($context, $component);
        provider::get_users_in_context($userlist);
        $userids = $userlist->get_userids();

        $this->assertCount(5, $userids);
        $expected = [$u1->id, $u2->id, $u3->id, $u4->id, $u5->id];
        $actual = $userids;
        sort($expected);
        sort($actual);
        $this->assertEquals($expected, $actual);

        $context = \context_module::instance($cm2->cmid);
        $userlist = new \core_privacy\local\request\userlist($context, $component);
        provider::get_users_in_context($userlist);
        $userids = $userlist->get_userids();

        $this->assertCount(1, $userids);
        $this->assertEquals([$u6->id], $userids);
    }

    public function test_delete_data_for_all_users_in_context(): void {
        global $DB;
        $dg = $this->getDataGenerator();
        $c1 = $dg->create_course();
        $u1 = $dg->create_user();
        $u2 = $dg->create_user();

        $cm1 = $dg->create_module('lesson', ['course' => $c1]);
        $cm2 = $dg->create_module('lesson', ['course' => $c1]);
        $cm3 = $dg->create_module('lesson', ['course' => $c1]);

        $c1ctx = \context_course::instance($c1->id);
        $cm1ctx = \context_module::instance($cm1->cmid);
        $cm2ctx = \context_module::instance($cm2->cmid);
        $cm3ctx = \context_module::instance($cm3->cmid);

        $this->create_attempt($cm1, $u1);
        $this->create_grade($cm1, $u1);
        $this->create_timer($cm1, $u1);
        $this->create_branch($cm1, $u1);
        $this->create_override($cm1, $u1);

        $this->create_attempt($cm1, $u2);
        $this->create_grade($cm1, $u2);
        $this->create_timer($cm1, $u2);
        $this->create_branch($cm1, $u2);
        $this->create_override($cm1, $u2);

        $this->create_attempt($cm2, $u1);
        $this->create_grade($cm2, $u1);
        $this->create_timer($cm2, $u1);
        $this->create_branch($cm2, $u1);
        $this->create_override($cm2, $u1);
        $this->create_attempt($cm2, $u2);
        $this->create_grade($cm2, $u2);
        $this->create_timer($cm2, $u2);
        $this->create_branch($cm2, $u2);
        $this->create_override($cm2, $u2);

        $assertcm1nochange = function() use ($DB, $u1, $u2, $cm1) {
            $this->assertTrue($DB->record_exists('lesson_attempts', ['userid' => $u1->id, 'lessonid' => $cm1->id]));
            $this->assertTrue($DB->record_exists('lesson_grades', ['userid' => $u1->id, 'lessonid' => $cm1->id]));
            $this->assertTrue($DB->record_exists('lesson_timer', ['userid' => $u1->id, 'lessonid' => $cm1->id]));
            $this->assertTrue($DB->record_exists('lesson_branch', ['userid' => $u1->id, 'lessonid' => $cm1->id]));
            $this->assertTrue($DB->record_exists('lesson_overrides', ['userid' => $u1->id, 'lessonid' => $cm1->id]));
            $this->assertTrue($DB->record_exists('lesson_attempts', ['userid' => $u2->id, 'lessonid' => $cm1->id]));
            $this->assertTrue($DB->record_exists('lesson_grades', ['userid' => $u2->id, 'lessonid' => $cm1->id]));
            $this->assertTrue($DB->record_exists('lesson_timer', ['userid' => $u2->id, 'lessonid' => $cm1->id]));
            $this->assertTrue($DB->record_exists('lesson_branch', ['userid' => $u2->id, 'lessonid' => $cm1->id]));
            $this->assertTrue($DB->record_exists('lesson_overrides', ['userid' => $u2->id, 'lessonid' => $cm1->id]));
        };
        $assertcm2nochange = function() use ($DB, $u1, $u2, $cm2) {
            $this->assertTrue($DB->record_exists('lesson_attempts', ['userid' => $u1->id, 'lessonid' => $cm2->id]));
            $this->assertTrue($DB->record_exists('lesson_grades', ['userid' => $u1->id, 'lessonid' => $cm2->id]));
            $this->assertTrue($DB->record_exists('lesson_timer', ['userid' => $u1->id, 'lessonid' => $cm2->id]));
            $this->assertTrue($DB->record_exists('lesson_branch', ['userid' => $u1->id, 'lessonid' => $cm2->id]));
            $this->assertTrue($DB->record_exists('lesson_overrides', ['userid' => $u1->id, 'lessonid' => $cm2->id]));
            $this->assertTrue($DB->record_exists('lesson_attempts', ['userid' => $u2->id, 'lessonid' => $cm2->id]));
            $this->assertTrue($DB->record_exists('lesson_grades', ['userid' => $u2->id, 'lessonid' => $cm2->id]));
            $this->assertTrue($DB->record_exists('lesson_timer', ['userid' => $u2->id, 'lessonid' => $cm2->id]));
            $this->assertTrue($DB->record_exists('lesson_branch', ['userid' => $u2->id, 'lessonid' => $cm2->id]));
            $this->assertTrue($DB->record_exists('lesson_overrides', ['userid' => $u2->id, 'lessonid' => $cm2->id]));
        };

        // Confirm existing state.
        $assertcm1nochange();
        $assertcm2nochange();

        // Delete the course: no change.
        provider::delete_data_for_all_users_in_context(\context_course::instance($c1->id));
        $assertcm1nochange();
        $assertcm2nochange();

        // Delete another module: no change.
        provider::delete_data_for_all_users_in_context(\context_module::instance($cm3->cmid));
        $assertcm1nochange();
        $assertcm2nochange();

        // Delete cm1: no change in cm2.
        provider::delete_data_for_all_users_in_context(\context_module::instance($cm1->cmid));
        $assertcm2nochange();
        $this->assertFalse($DB->record_exists('lesson_attempts', ['userid' => $u1->id, 'lessonid' => $cm1->id]));
        $this->assertFalse($DB->record_exists('lesson_grades', ['userid' => $u1->id, 'lessonid' => $cm1->id]));
        $this->assertFalse($DB->record_exists('lesson_timer', ['userid' => $u1->id, 'lessonid' => $cm1->id]));
        $this->assertFalse($DB->record_exists('lesson_branch', ['userid' => $u1->id, 'lessonid' => $cm1->id]));
        $this->assertFalse($DB->record_exists('lesson_overrides', ['userid' => $u1->id, 'lessonid' => $cm1->id]));
        $this->assertFalse($DB->record_exists('lesson_attempts', ['userid' => $u2->id, 'lessonid' => $cm1->id]));
        $this->assertFalse($DB->record_exists('lesson_grades', ['userid' => $u2->id, 'lessonid' => $cm1->id]));
        $this->assertFalse($DB->record_exists('lesson_timer', ['userid' => $u2->id, 'lessonid' => $cm1->id]));
        $this->assertFalse($DB->record_exists('lesson_branch', ['userid' => $u2->id, 'lessonid' => $cm1->id]));
        $this->assertFalse($DB->record_exists('lesson_overrides', ['userid' => $u2->id, 'lessonid' => $cm1->id]));
    }

    public function test_delete_data_for_user(): void {
        global $DB;
        $dg = $this->getDataGenerator();
        $c1 = $dg->create_course();
        $u1 = $dg->create_user();
        $u2 = $dg->create_user();

        $cm1 = $dg->create_module('lesson', ['course' => $c1]);
        $cm2 = $dg->create_module('lesson', ['course' => $c1]);
        $cm3 = $dg->create_module('lesson', ['course' => $c1]);

        $c1ctx = \context_course::instance($c1->id);
        $cm1ctx = \context_module::instance($cm1->cmid);
        $cm2ctx = \context_module::instance($cm2->cmid);
        $cm3ctx = \context_module::instance($cm3->cmid);

        $this->create_attempt($cm1, $u1);
        $this->create_grade($cm1, $u1);
        $this->create_timer($cm1, $u1);
        $this->create_branch($cm1, $u1);
        $this->create_override($cm1, $u1);
        $this->create_attempt($cm1, $u2);
        $this->create_grade($cm1, $u2);
        $this->create_timer($cm1, $u2);
        $this->create_branch($cm1, $u2);
        $this->create_override($cm1, $u2);

        $this->create_attempt($cm2, $u1);
        $this->create_grade($cm2, $u1);
        $this->create_timer($cm2, $u1);
        $this->create_branch($cm2, $u1);
        $this->create_override($cm2, $u1);
        $this->create_attempt($cm2, $u2);
        $this->create_grade($cm2, $u2);
        $this->create_timer($cm2, $u2);
        $this->create_branch($cm2, $u2);
        $this->create_override($cm2, $u2);

        $assertu1nochange = function() use ($DB, $u1, $cm1, $cm2) {
            $this->assertTrue($DB->record_exists('lesson_attempts', ['userid' => $u1->id, 'lessonid' => $cm1->id]));
            $this->assertTrue($DB->record_exists('lesson_grades', ['userid' => $u1->id, 'lessonid' => $cm1->id]));
            $this->assertTrue($DB->record_exists('lesson_timer', ['userid' => $u1->id, 'lessonid' => $cm1->id]));
            $this->assertTrue($DB->record_exists('lesson_branch', ['userid' => $u1->id, 'lessonid' => $cm1->id]));
            $this->assertTrue($DB->record_exists('lesson_overrides', ['userid' => $u1->id, 'lessonid' => $cm1->id]));
            $this->assertTrue($DB->record_exists('lesson_attempts', ['userid' => $u1->id, 'lessonid' => $cm2->id]));
            $this->assertTrue($DB->record_exists('lesson_grades', ['userid' => $u1->id, 'lessonid' => $cm2->id]));
            $this->assertTrue($DB->record_exists('lesson_timer', ['userid' => $u1->id, 'lessonid' => $cm2->id]));
            $this->assertTrue($DB->record_exists('lesson_branch', ['userid' => $u1->id, 'lessonid' => $cm2->id]));
            $this->assertTrue($DB->record_exists('lesson_overrides', ['userid' => $u1->id, 'lessonid' => $cm2->id]));
        };
        $assertu2nochange = function() use ($DB, $u2, $cm1, $cm2) {
            $this->assertTrue($DB->record_exists('lesson_attempts', ['userid' => $u2->id, 'lessonid' => $cm1->id]));
            $this->assertTrue($DB->record_exists('lesson_grades', ['userid' => $u2->id, 'lessonid' => $cm1->id]));
            $this->assertTrue($DB->record_exists('lesson_timer', ['userid' => $u2->id, 'lessonid' => $cm1->id]));
            $this->assertTrue($DB->record_exists('lesson_branch', ['userid' => $u2->id, 'lessonid' => $cm1->id]));
            $this->assertTrue($DB->record_exists('lesson_overrides', ['userid' => $u2->id, 'lessonid' => $cm1->id]));
            $this->assertTrue($DB->record_exists('lesson_attempts', ['userid' => $u2->id, 'lessonid' => $cm2->id]));
            $this->assertTrue($DB->record_exists('lesson_grades', ['userid' => $u2->id, 'lessonid' => $cm2->id]));
            $this->assertTrue($DB->record_exists('lesson_timer', ['userid' => $u2->id, 'lessonid' => $cm2->id]));
            $this->assertTrue($DB->record_exists('lesson_branch', ['userid' => $u2->id, 'lessonid' => $cm2->id]));
            $this->assertTrue($DB->record_exists('lesson_overrides', ['userid' => $u2->id, 'lessonid' => $cm2->id]));
        };

        // Confirm existing state.
        $assertu1nochange();
        $assertu2nochange();

        // Delete the course: no change.
        provider::delete_data_for_user(new approved_contextlist($u1, 'mod_lesson', [\context_course::instance($c1->id)->id]));
        $assertu1nochange();
        $assertu2nochange();

        // Delete another module: no change.
        provider::delete_data_for_user(new approved_contextlist($u1, 'mod_lesson', [\context_module::instance($cm3->cmid)->id]));
        $assertu1nochange();
        $assertu2nochange();

        // Delete u1 in cm1: no change for u2 and in cm2.
        provider::delete_data_for_user(new approved_contextlist($u1, 'mod_lesson', [\context_module::instance($cm1->cmid)->id]));
        $assertu2nochange();
        $this->assertFalse($DB->record_exists('lesson_attempts', ['userid' => $u1->id, 'lessonid' => $cm1->id]));
        $this->assertFalse($DB->record_exists('lesson_grades', ['userid' => $u1->id, 'lessonid' => $cm1->id]));
        $this->assertFalse($DB->record_exists('lesson_timer', ['userid' => $u1->id, 'lessonid' => $cm1->id]));
        $this->assertFalse($DB->record_exists('lesson_branch', ['userid' => $u1->id, 'lessonid' => $cm1->id]));
        $this->assertFalse($DB->record_exists('lesson_overrides', ['userid' => $u1->id, 'lessonid' => $cm1->id]));
        $this->assertTrue($DB->record_exists('lesson_attempts', ['userid' => $u1->id, 'lessonid' => $cm2->id]));
        $this->assertTrue($DB->record_exists('lesson_grades', ['userid' => $u1->id, 'lessonid' => $cm2->id]));
        $this->assertTrue($DB->record_exists('lesson_timer', ['userid' => $u1->id, 'lessonid' => $cm2->id]));
        $this->assertTrue($DB->record_exists('lesson_branch', ['userid' => $u1->id, 'lessonid' => $cm2->id]));
        $this->assertTrue($DB->record_exists('lesson_overrides', ['userid' => $u1->id, 'lessonid' => $cm2->id]));
    }

    /*
     * Test for provider::delete_data_for_users().
     */
    public function test_delete_data_for_users(): void {
        global $DB;
        $dg = $this->getDataGenerator();
        $c1 = $dg->create_course();
        $u1 = $dg->create_user();
        $u2 = $dg->create_user();

        $cm1 = $dg->create_module('lesson', ['course' => $c1]);
        $cm2 = $dg->create_module('lesson', ['course' => $c1]);
        $cm3 = $dg->create_module('lesson', ['course' => $c1]);
        $context1 = \context_module::instance($cm1->cmid);
        $context3 = \context_module::instance($cm3->cmid);

        $this->create_attempt($cm1, $u1);
        $this->create_grade($cm1, $u1);
        $this->create_timer($cm1, $u1);
        $this->create_branch($cm1, $u1);
        $this->create_override($cm1, $u1);
        $this->create_attempt($cm1, $u2);
        $this->create_grade($cm1, $u2);
        $this->create_timer($cm1, $u2);
        $this->create_branch($cm1, $u2);
        $this->create_override($cm1, $u2);

        $this->create_attempt($cm2, $u1);
        $this->create_grade($cm2, $u1);
        $this->create_timer($cm2, $u1);
        $this->create_branch($cm2, $u1);
        $this->create_override($cm2, $u1);
        $this->create_attempt($cm2, $u2);
        $this->create_grade($cm2, $u2);
        $this->create_timer($cm2, $u2);
        $this->create_branch($cm2, $u2);
        $this->create_override($cm2, $u2);

        $assertnochange = function($user, $cm) use ($DB) {
            $this->assertTrue($DB->record_exists('lesson_attempts', ['userid' => $user->id, 'lessonid' => $cm->id]));
            $this->assertTrue($DB->record_exists('lesson_grades', ['userid' => $user->id, 'lessonid' => $cm->id]));
            $this->assertTrue($DB->record_exists('lesson_timer', ['userid' => $user->id, 'lessonid' => $cm->id]));
            $this->assertTrue($DB->record_exists('lesson_branch', ['userid' => $user->id, 'lessonid' => $cm->id]));
            $this->assertTrue($DB->record_exists('lesson_overrides', ['userid' => $user->id, 'lessonid' => $cm->id]));
        };

        $assertdeleted = function($user, $cm) use ($DB) {
            $this->assertFalse($DB->record_exists('lesson_attempts', ['userid' => $user->id, 'lessonid' => $cm->id]));
            $this->assertFalse($DB->record_exists('lesson_grades', ['userid' => $user->id, 'lessonid' => $cm->id]));
            $this->assertFalse($DB->record_exists('lesson_timer', ['userid' => $user->id, 'lessonid' => $cm->id]));
            $this->assertFalse($DB->record_exists('lesson_branch', ['userid' => $user->id, 'lessonid' => $cm->id]));
            $this->assertFalse($DB->record_exists('lesson_overrides', ['userid' => $user->id, 'lessonid' => $cm->id]));
        };

        // Confirm existing state.
        $assertnochange($u1, $cm1);
        $assertnochange($u1, $cm2);
        $assertnochange($u2, $cm1);
        $assertnochange($u2, $cm2);

        // Delete another module: no change.
        $approveduserlist = new approved_userlist($context3, 'mod_lesson', [$u1->id]);
        provider::delete_data_for_users($approveduserlist);

        $assertnochange($u1, $cm1);
        $assertnochange($u1, $cm2);
        $assertnochange($u2, $cm1);
        $assertnochange($u2, $cm2);

        // Delete cm1 for u1: no change for u2 and in cm2.
        $approveduserlist = new approved_userlist($context1, 'mod_lesson', [$u1->id]);
        provider::delete_data_for_users($approveduserlist);

        $assertdeleted($u1, $cm1);
        $assertnochange($u1, $cm2);
        $assertnochange($u2, $cm1);
        $assertnochange($u2, $cm2);
    }

    public function test_export_data_for_user_overrides(): void {
        $dg = $this->getDataGenerator();
        $c1 = $dg->create_course();
        $u1 = $dg->create_user();
        $u2 = $dg->create_user();

        $cm1 = $dg->create_module('lesson', ['course' => $c1]);
        $cm2 = $dg->create_module('lesson', ['course' => $c1]);
        $cm1ctx = \context_module::instance($cm1->cmid);
        $cm2ctx = \context_module::instance($cm2->cmid);

        $now = time();
        $this->create_override($cm1, $u1); // All null.
        $this->create_override($cm2, $u1, [
            'available' => $now - 3600,
            'deadline' => $now + 3600,
            'timelimit' => 123,
            'review' => 1,
            'maxattempts' => 1,
            'retake' => 0,
            'password' => '1337 5p34k'
        ]);
        $this->create_override($cm1, $u2, [
            'available' => $now - 1230,
            'timelimit' => 456,
            'maxattempts' => 5,
            'retake' => 1,
        ]);

        provider::export_user_data(new approved_contextlist($u1, 'mod_lesson', [$cm1ctx->id, $cm2ctx->id]));
        $data = writer::with_context($cm1ctx)->get_data([]);
        $this->assertNotEmpty($data);
        $data = writer::with_context($cm1ctx)->get_related_data([], 'overrides');
        $this->assertNull($data->available);
        $this->assertNull($data->deadline);
        $this->assertNull($data->timelimit);
        $this->assertNull($data->review);
        $this->assertNull($data->maxattempts);
        $this->assertNull($data->retake);
        $this->assertNull($data->password);

        $data = writer::with_context($cm2ctx)->get_data([]);
        $this->assertNotEmpty($data);
        $data = writer::with_context($cm2ctx)->get_related_data([], 'overrides');
        $this->assertEquals(transform::datetime($now - 3600), $data->available);
        $this->assertEquals(transform::datetime($now + 3600), $data->deadline);
        $this->assertEquals(format_time(123), $data->timelimit);
        $this->assertEquals(transform::yesno(true), $data->review);
        $this->assertEquals(1, $data->maxattempts);
        $this->assertEquals(transform::yesno(false), $data->retake);
        $this->assertEquals('1337 5p34k', $data->password);

        writer::reset();
        provider::export_user_data(new approved_contextlist($u2, 'mod_lesson', [$cm1ctx->id, $cm2ctx->id]));
        $data = writer::with_context($cm1ctx)->get_data([]);
        $this->assertNotEmpty($data);
        $data = writer::with_context($cm1ctx)->get_related_data([], 'overrides');
        $this->assertEquals(transform::datetime($now - 1230), $data->available);
        $this->assertNull($data->deadline);
        $this->assertEquals(format_time(456), $data->timelimit);
        $this->assertNull($data->review);
        $this->assertEquals(5, $data->maxattempts);
        $this->assertEquals(transform::yesno(true), $data->retake);
        $this->assertNull($data->password);

        $data = writer::with_context($cm2ctx)->get_data([]);
        $this->assertNotEmpty($data);
        $data = writer::with_context($cm2ctx)->get_related_data([], 'overrides');
        $this->assertEmpty($data);
    }

    public function test_export_data_for_user_grades(): void {
        $dg = $this->getDataGenerator();
        $c1 = $dg->create_course();
        $u1 = $dg->create_user();
        $u2 = $dg->create_user();

        $cm1 = $dg->create_module('lesson', ['course' => $c1]);
        $cm2 = $dg->create_module('lesson', ['course' => $c1]);
        $cm1ctx = \context_module::instance($cm1->cmid);
        $cm2ctx = \context_module::instance($cm2->cmid);

        $now = time();
        $this->create_grade($cm2, $u1, ['grade' => 33.33, 'completed' => $now - 3600]);
        $this->create_grade($cm2, $u1, ['grade' => 50, 'completed' => $now - 1600]);
        $this->create_grade($cm2, $u1, ['grade' => 81.23, 'completed' => $now - 100]);
        $this->create_grade($cm1, $u2, ['grade' => 99.98, 'completed' => $now - 86400]);

        provider::export_user_data(new approved_contextlist($u1, 'mod_lesson', [$cm1ctx->id, $cm2ctx->id]));
        $data = writer::with_context($cm1ctx)->get_related_data([], 'grades');
        $this->assertEmpty($data);
        $data = writer::with_context($cm2ctx)->get_related_data([], 'grades');
        $this->assertNotEmpty($data);
        $this->assertCount(3, $data->grades);
        $this->assertEquals(33.33, $data->grades[0]->grade);
        $this->assertEquals(50, $data->grades[1]->grade);
        $this->assertEquals(81.23, $data->grades[2]->grade);
        $this->assertEquals(transform::datetime($now - 3600), $data->grades[0]->completed);
        $this->assertEquals(transform::datetime($now - 1600), $data->grades[1]->completed);
        $this->assertEquals(transform::datetime($now - 100), $data->grades[2]->completed);

        writer::reset();
        provider::export_user_data(new approved_contextlist($u2, 'mod_lesson', [$cm1ctx->id, $cm2ctx->id]));
        $data = writer::with_context($cm2ctx)->get_related_data([], 'grades');
        $this->assertEmpty($data);
        $data = writer::with_context($cm1ctx)->get_related_data([], 'grades');
        $this->assertNotEmpty($data);
        $this->assertCount(1, $data->grades);
        $this->assertEquals(99.98, $data->grades[0]->grade);
        $this->assertEquals(transform::datetime($now - 86400), $data->grades[0]->completed);
    }

    public function test_export_data_for_user_timers(): void {
        $dg = $this->getDataGenerator();
        $c1 = $dg->create_course();
        $u1 = $dg->create_user();
        $u2 = $dg->create_user();

        $cm1 = $dg->create_module('lesson', ['course' => $c1]);
        $cm2 = $dg->create_module('lesson', ['course' => $c1]);
        $cm1ctx = \context_module::instance($cm1->cmid);
        $cm2ctx = \context_module::instance($cm2->cmid);

        $now = time();
        $this->create_timer($cm2, $u1, ['starttime' => $now - 2000, 'lessontime' => $now + 3600, 'completed' => 0,
            'timemodifiedoffline' => $now - 7000]);
        $this->create_timer($cm2, $u1, ['starttime' => $now - 1000, 'lessontime' => $now + 1600, 'completed' => 0]);
        $this->create_timer($cm2, $u1, ['starttime' => $now - 500, 'lessontime' => $now + 100, 'completed' => 1]);
        $this->create_timer($cm1, $u2, ['starttime' => $now - 1000, 'lessontime' => $now + 1800, 'completed' => 1]);

        provider::export_user_data(new approved_contextlist($u1, 'mod_lesson', [$cm1ctx->id, $cm2ctx->id]));
        $data = writer::with_context($cm1ctx)->get_related_data([], 'timers');
        $this->assertEmpty($data);
        $data = writer::with_context($cm2ctx)->get_related_data([], 'timers');
        $this->assertNotEmpty($data);
        $this->assertCount(3, $data->timers);
        $this->assertEquals(transform::datetime($now - 2000), $data->timers[0]->starttime);
        $this->assertEquals(transform::datetime($now + 3600), $data->timers[0]->lastactivity);
        $this->assertEquals(transform::yesno(false), $data->timers[0]->completed);
        $this->assertEquals(transform::datetime($now - 7000), $data->timers[0]->timemodifiedoffline);

        $this->assertEquals(transform::datetime($now - 1000), $data->timers[1]->starttime);
        $this->assertEquals(transform::datetime($now + 1600), $data->timers[1]->lastactivity);
        $this->assertEquals(transform::yesno(false), $data->timers[1]->completed);
        $this->assertNull($data->timers[1]->timemodifiedoffline);

        $this->assertEquals(transform::datetime($now - 500), $data->timers[2]->starttime);
        $this->assertEquals(transform::datetime($now + 100), $data->timers[2]->lastactivity);
        $this->assertEquals(transform::yesno(true), $data->timers[2]->completed);
        $this->assertNull($data->timers[2]->timemodifiedoffline);

        writer::reset();
        provider::export_user_data(new approved_contextlist($u2, 'mod_lesson', [$cm1ctx->id, $cm2ctx->id]));
        $data = writer::with_context($cm2ctx)->get_related_data([], 'timers');
        $this->assertEmpty($data);
        $data = writer::with_context($cm1ctx)->get_related_data([], 'timers');
        $this->assertCount(1, $data->timers);
        $this->assertEquals(transform::datetime($now - 1000), $data->timers[0]->starttime);
        $this->assertEquals(transform::datetime($now + 1800), $data->timers[0]->lastactivity);
        $this->assertEquals(transform::yesno(true), $data->timers[0]->completed);
        $this->assertNull($data->timers[0]->timemodifiedoffline);
    }

    public function test_export_data_for_user_attempts(): void {
        global $DB;
        $dg = $this->getDataGenerator();
        $lg = $dg->get_plugin_generator('mod_lesson');

        $c1 = $dg->create_course();
        $u1 = $dg->create_user();
        $u2 = $dg->create_user();

        $cm1 = $dg->create_module('lesson', ['course' => $c1]);
        $cm2 = $dg->create_module('lesson', ['course' => $c1]);
        $cm1ctx = \context_module::instance($cm1->cmid);
        $cm2ctx = \context_module::instance($cm2->cmid);

        $page1 = $lg->create_content($cm1);
        $page2 = $lg->create_question_truefalse($cm1);
        $page3 = $lg->create_question_multichoice($cm1);
        $page4 = $lg->create_question_multichoice($cm1, [
            'qoption' => 1,
            'answer_editor' => [
                ['text' => 'Cats', 'format' => FORMAT_PLAIN, 'score' => 1],
                ['text' => 'Dogs', 'format' => FORMAT_PLAIN, 'score' => 1],
                ['text' => 'Birds', 'format' => FORMAT_PLAIN, 'score' => 0],
            ],
            'jumpto' => [LESSON_NEXTPAGE, LESSON_NEXTPAGE, LESSON_THISPAGE]
        ]);
        $page4answers = array_keys($DB->get_records('lesson_answers', ['pageid' => $page4->id], 'id'));
        $page5 = $lg->create_question_matching($cm1, [
            'answer_editor' => [
                2 => ['text' => 'The plural of cat', 'format' => FORMAT_PLAIN],
                3 => ['text' => 'The plural of dog', 'format' => FORMAT_PLAIN],
                4 => ['text' => 'The plural of bird', 'format' => FORMAT_PLAIN],
            ],
            'response_editor' => [
                2 => 'Cats',
                3 => 'Dogs',
                4 => 'Birds',
            ]
        ]);
        $page6 = $lg->create_question_shortanswer($cm1);
        $page7 = $lg->create_question_numeric($cm1);
        $page8 = $lg->create_question_essay($cm1);
        $page9 = $lg->create_content($cm1);

        $pageb1 = $lg->create_content($cm2);
        $pageb2 = $lg->create_question_truefalse($cm2);
        $pageb3 = $lg->create_question_truefalse($cm2);

        $this->create_branch($cm1, $u1, ['pageid' => $page1->id, 'nextpageid' => $page2->id]);
        $this->create_attempt($cm1, $u1, ['pageid' => $page2->id, 'useranswer' => 'This is true']);
        $this->create_attempt($cm1, $u1, ['pageid' => $page3->id, 'useranswer' => 'A', 'correct' => 1]);
        $this->create_attempt($cm1, $u1, ['pageid' => $page4->id,
            'useranswer' => implode(',', array_slice($page4answers, 0, 2))]);
        $this->create_attempt($cm1, $u1, ['pageid' => $page5->id, 'useranswer' => 'Cats,Birds,Dogs']);
        $this->create_attempt($cm1, $u1, ['pageid' => $page6->id, 'useranswer' => 'Hello world!']);
        $this->create_attempt($cm1, $u1, ['pageid' => $page7->id, 'useranswer' => '1337']);
        $this->create_attempt($cm1, $u1, ['pageid' => $page8->id, 'useranswer' => serialize((object) [
            'sent' => 0, 'graded' => 0, 'score' => 0, 'answer' => 'I like cats', 'answerformat' => FORMAT_PLAIN,
            'response' => 'Me too!', 'responseformat' => FORMAT_PLAIN
        ])]);
        $this->create_branch($cm1, $u1, ['pageid' => $page9->id, 'nextpageid' => 0]);

        provider::export_user_data(new approved_contextlist($u1, 'mod_lesson', [$cm1ctx->id, $cm2ctx->id]));
        $data = writer::with_context($cm2ctx)->get_related_data([], 'attempts');
        $this->assertEmpty($data);
        $data = writer::with_context($cm1ctx)->get_related_data([], 'attempts');
        $this->assertNotEmpty($data);
        $this->assertCount(1, $data->attempts);
        $this->assertEquals(1, $data->attempts[0]->number);
        $this->assertCount(2, $data->attempts[0]->jumps);
        $this->assertCount(7, $data->attempts[0]->answers);
        $jump = $data->attempts[0]->jumps[0];
        $this->assert_attempt_page($page1, $jump);
        $this->assertTrue(strpos($jump['went_to'], $page2->title) !== false);
        $jump = $data->attempts[0]->jumps[1];
        $this->assert_attempt_page($page9, $jump);
        $this->assertEquals(get_string('endoflesson', 'mod_lesson'), $jump['went_to']);
        $answer = $data->attempts[0]->answers[0];
        $this->assert_attempt_page($page2, $answer);
        $this->assertEquals(transform::yesno(false), $answer['correct']);
        $this->assertEquals('This is true', $answer['answer']);
        $answer = $data->attempts[0]->answers[1];
        $this->assert_attempt_page($page3, $answer);
        $this->assertEquals(transform::yesno(true), $answer['correct']);
        $this->assertEquals('A', $answer['answer']);
        $answer = $data->attempts[0]->answers[2];
        $this->assert_attempt_page($page4, $answer);
        $this->assertEquals(transform::yesno(false), $answer['correct']);
        $this->assertCount(2, $answer['answer']);
        $this->assertTrue(in_array('Cats', $answer['answer']));
        $this->assertTrue(in_array('Dogs', $answer['answer']));
        $answer = $data->attempts[0]->answers[3];
        $this->assert_attempt_page($page5, $answer);
        $this->assertEquals(transform::yesno(false), $answer['correct']);
        $this->assertCount(3, $answer['answer']);
        $this->assertEquals('The plural of cat', $answer['answer'][0]['label']);
        $this->assertEquals('Cats', $answer['answer'][0]['matched_with']);
        $this->assertEquals('The plural of dog', $answer['answer'][1]['label']);
        $this->assertEquals('Birds', $answer['answer'][1]['matched_with']);
        $this->assertEquals('The plural of bird', $answer['answer'][2]['label']);
        $this->assertEquals('Dogs', $answer['answer'][2]['matched_with']);
        $answer = $data->attempts[0]->answers[4];
        $this->assert_attempt_page($page6, $answer);
        $this->assertEquals(transform::yesno(false), $answer['correct']);
        $this->assertEquals('Hello world!', $answer['answer']);
        $answer = $data->attempts[0]->answers[5];
        $this->assert_attempt_page($page7, $answer);
        $this->assertEquals(transform::yesno(false), $answer['correct']);
        $this->assertEquals('1337', $answer['answer']);
        $answer = $data->attempts[0]->answers[6];
        $this->assert_attempt_page($page8, $answer);
        $this->assertEquals(transform::yesno(false), $answer['correct']);
        $this->assertEquals('I like cats', $answer['answer']);
        $this->assertEquals('Me too!', $answer['response']);

        writer::reset();
        provider::export_user_data(new approved_contextlist($u2, 'mod_lesson', [$cm1ctx->id, $cm2ctx->id]));
        $data = writer::with_context($cm1ctx)->get_related_data([], 'attempts');
        $this->assertEmpty($data);
        $data = writer::with_context($cm2ctx)->get_related_data([], 'attempts');
        $this->assertEmpty($data);

        // Let's mess with the data by creating an additional attempt for u1, and create data for u1 and u2 in the other cm.
        $this->create_branch($cm1, $u1, ['pageid' => $page1->id, 'nextpageid' => $page3->id, 'retry' => 1]);
        $this->create_attempt($cm1, $u1, ['pageid' => $page3->id, 'useranswer' => 'B', 'retry' => 1]);

        $this->create_branch($cm2, $u1, ['pageid' => $pageb1->id, 'nextpageid' => $pageb2->id]);
        $this->create_attempt($cm2, $u1, ['pageid' => $pageb2->id, 'useranswer' => 'Abc']);

        $this->create_branch($cm2, $u2, ['pageid' => $pageb1->id, 'nextpageid' => $pageb3->id]);
        $this->create_attempt($cm2, $u2, ['pageid' => $pageb3->id, 'useranswer' => 'Def']);

        writer::reset();
        provider::export_user_data(new approved_contextlist($u1, 'mod_lesson', [$cm1ctx->id, $cm2ctx->id]));
        $data = writer::with_context($cm1ctx)->get_related_data([], 'attempts');
        $this->assertNotEmpty($data);
        $this->assertCount(2, $data->attempts);
        $this->assertEquals(1, $data->attempts[0]->number);
        $this->assertCount(2, $data->attempts[0]->jumps);
        $this->assertCount(7, $data->attempts[0]->answers);
        $attempt = $data->attempts[1];
        $this->assertEquals(2, $attempt->number);
        $this->assertCount(1, $attempt->jumps);
        $this->assertCount(1, $attempt->answers);
        $this->assert_attempt_page($page1, $attempt->jumps[0]);
        $this->assertTrue(strpos($attempt->jumps[0]['went_to'], $page3->title) !== false);
        $this->assert_attempt_page($page3, $attempt->answers[0]);
        $this->assertEquals('B', $attempt->answers[0]['answer']);

        $data = writer::with_context($cm2ctx)->get_related_data([], 'attempts');
        $this->assertCount(1, $data->attempts);
        $attempt = $data->attempts[0];
        $this->assertEquals(1, $attempt->number);
        $this->assertCount(1, $attempt->jumps);
        $this->assertCount(1, $attempt->answers);
        $this->assert_attempt_page($pageb1, $attempt->jumps[0]);
        $this->assertTrue(strpos($attempt->jumps[0]['went_to'], $pageb2->title) !== false);
        $this->assert_attempt_page($pageb2, $attempt->answers[0]);
        $this->assertEquals('Abc', $attempt->answers[0]['answer']);

        writer::reset();
        provider::export_user_data(new approved_contextlist($u2, 'mod_lesson', [$cm1ctx->id, $cm2ctx->id]));
        $data = writer::with_context($cm1ctx)->get_related_data([], 'attempts');
        $this->assertEmpty($data);

        $data = writer::with_context($cm2ctx)->get_related_data([], 'attempts');
        $this->assertCount(1, $data->attempts);
        $attempt = $data->attempts[0];
        $this->assertEquals(1, $attempt->number);
        $this->assertCount(1, $attempt->jumps);
        $this->assertCount(1, $attempt->answers);
        $this->assert_attempt_page($pageb1, $attempt->jumps[0]);
        $this->assertTrue(strpos($attempt->jumps[0]['went_to'], $pageb3->title) !== false);
        $this->assert_attempt_page($pageb3, $attempt->answers[0]);
        $this->assertEquals('Def', $attempt->answers[0]['answer']);
    }

    /**
     * Assert the page details of an attempt.
     *
     * @param object $page The expected page info.
     * @param array $attempt The exported attempt details.
     * @return void
     */
    protected function assert_attempt_page($page, $attempt) {
        $this->assertEquals($page->id, $attempt['id']);
        $this->assertEquals($page->title, $attempt['page']);
        $this->assertEquals(format_text($page->contents, $page->contentsformat), $attempt['contents']);
    }

    /**
     * Create an attempt (answer to a question).
     *
     * @param object $lesson The lesson.
     * @param object $user The user.
     * @param array $options Options.
     * @return object
     */
    protected function create_attempt($lesson, $user, array $options = []) {
        global $DB;
        $record = (object) array_merge([
            'lessonid' => $lesson->id,
            'userid' => $user->id,
            'pageid' => 0,
            'answerid' => 0,
            'retry' => 0,
            'correct' => 0,
            'useranswer' => '',
            'timeseen' => time(),
        ], $options);
        $record->id = $DB->insert_record('lesson_attempts', $record);
        return $record;
    }

    /**
     * Create a grade.
     *
     * @param object $lesson The lesson.
     * @param object $user The user.
     * @param array $options Options.
     * @return object
     */
    protected function create_grade($lesson, $user, array $options = []) {
        global $DB;
        $record = (object) array_merge([
            'lessonid' => $lesson->id,
            'userid' => $user->id,
            'late' => 0,
            'grade' => 50.0,
            'completed' => time(),
        ], $options);
        $record->id = $DB->insert_record('lesson_grades', $record);
        return $record;
    }

    /**
     * Create a timer.
     *
     * @param object $lesson The lesson.
     * @param object $user The user.
     * @param array $options Options.
     * @return object
     */
    protected function create_timer($lesson, $user, array $options = []) {
        global $DB;
        $record = (object) array_merge([
            'lessonid' => $lesson->id,
            'userid' => $user->id,
            'starttime' => time() - 600,
            'lessontime' => time(),
            'completed' => 1,
            'timemodifiedoffline' => 0,
        ], $options);
        $record->id = $DB->insert_record('lesson_timer', $record);
        return $record;
    }

    /**
     * Create a branch (choice on page).
     *
     * @param object $lesson The lesson.
     * @param object $user The user.
     * @param array $options Options.
     * @return object
     */
    protected function create_branch($lesson, $user, array $options = []) {
        global $DB;
        $record = (object) array_merge([
            'lessonid' => $lesson->id,
            'userid' => $user->id,
            'pageid' => 0,
            'retry' => 0,
            'flag' => 0,
            'timeseen' => time(),
            'nextpageid' => 0,
        ], $options);
        $record->id = $DB->insert_record('lesson_branch', $record);
        return $record;
    }

    /**
     * Create an override.
     *
     * @param object $lesson The lesson.
     * @param object $user The user.
     * @param array $options Options.
     * @return object
     */
    protected function create_override($lesson, $user, array $options = []) {
        global $DB;
        $record = (object) array_merge([
            'lessonid' => $lesson->id,
            'userid' => $user->id,
        ], $options);
        $record->id = $DB->insert_record('lesson_overrides', $record);
        return $record;
    }
}
