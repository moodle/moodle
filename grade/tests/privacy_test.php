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
 * @package    core_grades
 * @category   test
 * @copyright  2018 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
global $CFG;

use core_privacy\tests\provider_testcase;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\transform;
use core_privacy\local\request\writer;
use core_grades\privacy\provider;

require_once($CFG->libdir . '/gradelib.php');

/**
 * Data provider testcase class.
 *
 * @package    core_grades
 * @category   test
 * @copyright  2018 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_grades_privacy_testcase extends provider_testcase {

    public function setUp() {
        global $PAGE;
        $this->resetAfterTest();
        $PAGE->get_renderer('core');
    }

    public function test_get_contexts_for_userid_gradebook_edits() {
        $dg = $this->getDataGenerator();

        $c1 = $dg->create_course();
        $c2 = $dg->create_course();

        $u1 = $dg->create_user();
        $u2 = $dg->create_user();
        $u3 = $dg->create_user();
        $u4 = $dg->create_user();
        $u5 = $dg->create_user();
        $u6 = $dg->create_user();

        $sysctx = context_system::instance();
        $c1ctx = context_course::instance($c1->id);
        $c2ctx = context_course::instance($c2->id);

        // Create some stuff.
        $gi1a = new grade_item($dg->create_grade_item(['courseid' => $c1->id]), false);
        $gi1b = new grade_item($dg->create_grade_item(['courseid' => $c1->id]), false);
        $gi2a = new grade_item($dg->create_grade_item(['courseid' => $c2->id]), false);
        $gc1a = new grade_category($dg->create_grade_category(['courseid' => $c1->id]), false);
        $gc1b = new grade_category($dg->create_grade_category(['courseid' => $c1->id]), false);
        $gc2a = new grade_category($dg->create_grade_category(['courseid' => $c2->id]), false);
        $go2 = new grade_outcome($dg->create_grade_outcome(['courseid' => $c2->id, 'shortname' => 'go2',
            'fullname' => 'go2']), false);

        // Nothing as of now.
        foreach ([$u1, $u2, $u3, $u4] as $u) {
            $contexts = array_flip(provider::get_contexts_for_userid($u->id)->get_contextids());
            $this->assertEmpty($contexts);
        }

        $go0 = new grade_outcome(['shortname' => 'go0', 'fullname' => 'go0', 'usermodified' => $u1->id]);
        $go0->insert();
        $go1 = new grade_outcome(['shortname' => 'go1', 'fullname' => 'go1', 'courseid' => $c1->id, 'usermodified' => $u1->id]);
        $go1->insert();

        // User 2 creates history.
        $this->setUser($u2);
        $go0->shortname .= ' edited';
        $go0->update();
        $gc1a->fullname .= ' edited';
        $gc1a->update();

        // User 3 creates history.
        $this->setUser($u3);
        $go1->shortname .= ' edited';
        $go1->update();
        $gc2a->fullname .= ' a';
        $gc2a->update();

        // User 4 updates an outcome in course (creates history).
        $this->setUser($u4);
        $go2->shortname .= ' edited';
        $go2->update();

        // User 5 updates an item.
        $this->setUser($u5);
        $gi1a->itemname .= ' edited';
        $gi1a->update();

        // User 6 creates history.
        $this->setUser($u6);
        $gi2a->delete();

        // Assert contexts.
        $contexts = array_flip(provider::get_contexts_for_userid($u1->id)->get_contextids());
        $this->assertCount(2, $contexts);
        $this->assertArrayHasKey($c1ctx->id, $contexts);
        $this->assertArrayHasKey($sysctx->id, $contexts);
        $contexts = array_flip(provider::get_contexts_for_userid($u2->id)->get_contextids());
        $this->assertCount(2, $contexts);
        $this->assertArrayHasKey($sysctx->id, $contexts);
        $this->assertArrayHasKey($c1ctx->id, $contexts);
        $contexts = array_flip(provider::get_contexts_for_userid($u3->id)->get_contextids());
        $this->assertCount(2, $contexts);
        $this->assertArrayHasKey($c1ctx->id, $contexts);
        $this->assertArrayHasKey($c2ctx->id, $contexts);
        $contexts = array_flip(provider::get_contexts_for_userid($u4->id)->get_contextids());
        $this->assertCount(1, $contexts);
        $this->assertArrayHasKey($c2ctx->id, $contexts);
        $contexts = array_flip(provider::get_contexts_for_userid($u5->id)->get_contextids());
        $this->assertCount(1, $contexts);
        $this->assertArrayHasKey($c1ctx->id, $contexts);
        $contexts = array_flip(provider::get_contexts_for_userid($u6->id)->get_contextids());
        $this->assertCount(1, $contexts);
        $this->assertArrayHasKey($c2ctx->id, $contexts);
    }

    public function test_get_contexts_for_userid_grades_and_history() {
        $dg = $this->getDataGenerator();

        $c1 = $dg->create_course();
        $c2 = $dg->create_course();

        $u1 = $dg->create_user();
        $u2 = $dg->create_user();
        $u3 = $dg->create_user();
        $u4 = $dg->create_user();
        $u5 = $dg->create_user();
        $u6 = $dg->create_user();

        $sysctx = context_system::instance();
        $c1ctx = context_course::instance($c1->id);
        $c2ctx = context_course::instance($c2->id);

        // Create some stuff.
        $gi1a = new grade_item($dg->create_grade_item(['courseid' => $c1->id]), false);
        $gi1b = new grade_item($dg->create_grade_item(['courseid' => $c1->id]), false);
        $gi2a = new grade_item($dg->create_grade_item(['courseid' => $c2->id]), false);
        $gi2b = new grade_item($dg->create_grade_item(['courseid' => $c2->id]), false);

        // Nothing as of now.
        foreach ([$u1, $u2, $u3, $u4, $u5, $u6] as $u) {
            $contexts = array_flip(provider::get_contexts_for_userid($u->id)->get_contextids());
            $this->assertEmpty($contexts);
        }

        // User 1 is graded in course 1.
        $gi1a->update_final_grade($u1->id, 1, 'test');

        // User 2 is graded in course 2.
        $gi2a->update_final_grade($u2->id, 10, 'test');

        // User 3 is set as modifier.
        $gi1a->update_final_grade($u1->id, 1, 'test', '', FORMAT_MOODLE, $u3->id);

        // User 4 is set as modifier, and creates history..
        $this->setUser($u4);
        $gi1a->update_final_grade($u2->id, 1, 'test');

        // User 5 creates history, user 6 is the known modifier, and we delete the item.
        $this->setUser($u5);
        $gi2b->update_final_grade($u2->id, 1, 'test', '', FORMAT_PLAIN, $u6->id);
        $gi2b->delete();

        // Assert contexts.
        $contexts = array_flip(provider::get_contexts_for_userid($u1->id)->get_contextids());
        $this->assertCount(1, $contexts);
        $this->assertArrayHasKey($c1ctx->id, $contexts);
        $contexts = array_flip(provider::get_contexts_for_userid($u2->id)->get_contextids());
        $this->assertCount(3, $contexts);
        $this->assertArrayHasKey($c1ctx->id, $contexts);
        $this->assertArrayHasKey($c2ctx->id, $contexts);
        $this->assertArrayHasKey(context_user::instance($u2->id)->id, $contexts);
        $contexts = array_flip(provider::get_contexts_for_userid($u3->id)->get_contextids());
        $this->assertCount(1, $contexts);
        $this->assertArrayHasKey($c1ctx->id, $contexts);
        $contexts = array_flip(provider::get_contexts_for_userid($u4->id)->get_contextids());
        $this->assertCount(1, $contexts);
        $this->assertArrayHasKey($c1ctx->id, $contexts);
        $contexts = array_flip(provider::get_contexts_for_userid($u5->id)->get_contextids());
        $this->assertCount(2, $contexts);
        $this->assertArrayHasKey($c2ctx->id, $contexts);
        $this->assertArrayHasKey(context_user::instance($u2->id)->id, $contexts);
        $contexts = array_flip(provider::get_contexts_for_userid($u6->id)->get_contextids());
        $this->assertCount(1, $contexts);
        $this->assertArrayHasKey(context_user::instance($u2->id)->id, $contexts);
    }

    public function test_delete_data_for_all_users_in_context() {
        global $DB;
        $dg = $this->getDataGenerator();

        $c1 = $dg->create_course();
        $c2 = $dg->create_course();
        $u1 = $dg->create_user();
        $u2 = $dg->create_user();
        $u1ctx = context_user::instance($u1->id);
        $c1ctx = context_course::instance($c1->id);
        $c2ctx = context_course::instance($c2->id);

        // Create some stuff.
        $gi1a = new grade_item($dg->create_grade_item(['courseid' => $c1->id]), false);
        $gi1b = new grade_item($dg->create_grade_item(['courseid' => $c1->id]), false);
        $gi2a = new grade_item($dg->create_grade_item(['courseid' => $c2->id]), false);
        $gi2b = new grade_item($dg->create_grade_item(['courseid' => $c2->id]), false);

        $gi1a->update_final_grade($u1->id, 1, 'test');
        $gi1a->update_final_grade($u2->id, 1, 'test');
        $gi1b->update_final_grade($u1->id, 1, 'test');
        $gi2a->update_final_grade($u1->id, 1, 'test');
        $gi2a->update_final_grade($u2->id, 1, 'test');
        $gi2b->update_final_grade($u1->id, 1, 'test');
        $gi2b->update_final_grade($u2->id, 1, 'test');
        $gi2b->delete();

        $this->assertTrue($DB->record_exists('grade_grades', ['userid' => $u1->id, 'itemid' => $gi1a->id]));
        $this->assertTrue($DB->record_exists('grade_grades', ['userid' => $u2->id, 'itemid' => $gi1a->id]));
        $this->assertTrue($DB->record_exists('grade_grades', ['userid' => $u1->id, 'itemid' => $gi1b->id]));
        $this->assertTrue($DB->record_exists('grade_grades', ['userid' => $u1->id, 'itemid' => $gi2a->id]));
        $this->assertTrue($DB->record_exists('grade_grades', ['userid' => $u2->id, 'itemid' => $gi2a->id]));
        $this->assertTrue($DB->record_exists('grade_grades_history', ['userid' => $u1->id, 'itemid' => $gi2b->id]));
        $this->assertTrue($DB->record_exists('grade_grades_history', ['userid' => $u2->id, 'itemid' => $gi2b->id]));

        provider::delete_data_for_all_users_in_context($c1ctx);
        $this->assertFalse($DB->record_exists('grade_grades', ['userid' => $u1->id, 'itemid' => $gi1a->id]));
        $this->assertFalse($DB->record_exists('grade_grades', ['userid' => $u2->id, 'itemid' => $gi1a->id]));
        $this->assertFalse($DB->record_exists('grade_grades', ['userid' => $u1->id, 'itemid' => $gi1b->id]));
        $this->assertTrue($DB->record_exists('grade_grades', ['userid' => $u1->id, 'itemid' => $gi2a->id]));
        $this->assertTrue($DB->record_exists('grade_grades', ['userid' => $u2->id, 'itemid' => $gi2a->id]));
        $this->assertTrue($DB->record_exists('grade_grades_history', ['userid' => $u1->id, 'itemid' => $gi2b->id]));
        $this->assertTrue($DB->record_exists('grade_grades_history', ['userid' => $u2->id, 'itemid' => $gi2b->id]));

        provider::delete_data_for_all_users_in_context($u1ctx);
        $this->assertTrue($DB->record_exists('grade_grades', ['userid' => $u1->id, 'itemid' => $gi2a->id]));
        $this->assertTrue($DB->record_exists('grade_grades', ['userid' => $u2->id, 'itemid' => $gi2a->id]));
        $this->assertFalse($DB->record_exists('grade_grades_history', ['userid' => $u1->id, 'itemid' => $gi2b->id]));
        $this->assertTrue($DB->record_exists('grade_grades_history', ['userid' => $u2->id, 'itemid' => $gi2b->id]));

        provider::delete_data_for_all_users_in_context($c2ctx);
        $this->assertFalse($DB->record_exists('grade_grades', ['userid' => $u1->id, 'itemid' => $gi2a->id]));
        $this->assertFalse($DB->record_exists('grade_grades', ['userid' => $u2->id, 'itemid' => $gi2a->id]));
        $this->assertTrue($DB->record_exists('grade_grades_history', ['userid' => $u2->id, 'itemid' => $gi2b->id]));
    }

    public function test_delete_data_for_user() {
        global $DB;
        $dg = $this->getDataGenerator();

        $c1 = $dg->create_course();
        $c2 = $dg->create_course();
        $u1 = $dg->create_user();
        $u2 = $dg->create_user();
        $u1ctx = context_user::instance($u1->id);
        $u2ctx = context_user::instance($u2->id);
        $c1ctx = context_course::instance($c1->id);
        $c2ctx = context_course::instance($c2->id);

        // Create some stuff.
        $gi1a = new grade_item($dg->create_grade_item(['courseid' => $c1->id]), false);
        $gi1b = new grade_item($dg->create_grade_item(['courseid' => $c1->id]), false);
        $gi2a = new grade_item($dg->create_grade_item(['courseid' => $c2->id]), false);
        $gi2b = new grade_item($dg->create_grade_item(['courseid' => $c2->id]), false);

        $gi1a->update_final_grade($u1->id, 1, 'test');
        $gi1a->update_final_grade($u2->id, 1, 'test');
        $gi1b->update_final_grade($u1->id, 1, 'test');
        $gi2a->update_final_grade($u1->id, 1, 'test');
        $gi2a->update_final_grade($u2->id, 1, 'test');
        $gi2b->update_final_grade($u1->id, 1, 'test');
        $gi2b->update_final_grade($u2->id, 1, 'test');
        $gi2b->delete();

        $this->assertTrue($DB->record_exists('grade_grades', ['userid' => $u1->id, 'itemid' => $gi1a->id]));
        $this->assertTrue($DB->record_exists('grade_grades', ['userid' => $u2->id, 'itemid' => $gi1a->id]));
        $this->assertTrue($DB->record_exists('grade_grades', ['userid' => $u1->id, 'itemid' => $gi1b->id]));
        $this->assertTrue($DB->record_exists('grade_grades', ['userid' => $u1->id, 'itemid' => $gi2a->id]));
        $this->assertTrue($DB->record_exists('grade_grades', ['userid' => $u2->id, 'itemid' => $gi2a->id]));
        $this->assertTrue($DB->record_exists('grade_grades_history', ['userid' => $u1->id, 'itemid' => $gi2b->id]));
        $this->assertTrue($DB->record_exists('grade_grades_history', ['userid' => $u2->id, 'itemid' => $gi2b->id]));

        provider::delete_data_for_user(new approved_contextlist($u1, 'core_grades', [$c1ctx->id]));
        $this->assertFalse($DB->record_exists('grade_grades', ['userid' => $u1->id, 'itemid' => $gi1a->id]));
        $this->assertTrue($DB->record_exists('grade_grades', ['userid' => $u2->id, 'itemid' => $gi1a->id]));
        $this->assertFalse($DB->record_exists('grade_grades', ['userid' => $u1->id, 'itemid' => $gi1b->id]));
        $this->assertTrue($DB->record_exists('grade_grades', ['userid' => $u1->id, 'itemid' => $gi2a->id]));
        $this->assertTrue($DB->record_exists('grade_grades', ['userid' => $u2->id, 'itemid' => $gi2a->id]));
        $this->assertTrue($DB->record_exists('grade_grades_history', ['userid' => $u1->id, 'itemid' => $gi2b->id]));
        $this->assertTrue($DB->record_exists('grade_grades_history', ['userid' => $u2->id, 'itemid' => $gi2b->id]));

        provider::delete_data_for_user(new approved_contextlist($u1, 'core_grades', [$u1ctx->id]));
        $this->assertFalse($DB->record_exists('grade_grades', ['userid' => $u1->id, 'itemid' => $gi1a->id]));
        $this->assertTrue($DB->record_exists('grade_grades', ['userid' => $u2->id, 'itemid' => $gi1a->id]));
        $this->assertFalse($DB->record_exists('grade_grades', ['userid' => $u1->id, 'itemid' => $gi1b->id]));
        $this->assertTrue($DB->record_exists('grade_grades', ['userid' => $u1->id, 'itemid' => $gi2a->id]));
        $this->assertTrue($DB->record_exists('grade_grades', ['userid' => $u2->id, 'itemid' => $gi2a->id]));
        $this->assertFalse($DB->record_exists('grade_grades_history', ['userid' => $u1->id, 'itemid' => $gi2b->id]));
        $this->assertTrue($DB->record_exists('grade_grades_history', ['userid' => $u2->id, 'itemid' => $gi2b->id]));

        provider::delete_data_for_user(new approved_contextlist($u1, 'core_grades', [$u2ctx->id, $c2ctx->id]));
        $this->assertFalse($DB->record_exists('grade_grades', ['userid' => $u1->id, 'itemid' => $gi1a->id]));
        $this->assertTrue($DB->record_exists('grade_grades', ['userid' => $u2->id, 'itemid' => $gi1a->id]));
        $this->assertFalse($DB->record_exists('grade_grades', ['userid' => $u1->id, 'itemid' => $gi1b->id]));
        $this->assertFalse($DB->record_exists('grade_grades', ['userid' => $u1->id, 'itemid' => $gi2a->id]));
        $this->assertTrue($DB->record_exists('grade_grades', ['userid' => $u2->id, 'itemid' => $gi2a->id]));
        $this->assertFalse($DB->record_exists('grade_grades_history', ['userid' => $u1->id, 'itemid' => $gi2b->id]));
        $this->assertTrue($DB->record_exists('grade_grades_history', ['userid' => $u2->id, 'itemid' => $gi2b->id]));
    }

    public function test_export_data_for_user_about_grades_and_history() {
        global $DB;
        $dg = $this->getDataGenerator();

        $c1 = $dg->create_course();
        $c2 = $dg->create_course();

        // Users being graded.
        $ug1 = $dg->create_user();
        $ug2 = $dg->create_user();
        $ug3 = $dg->create_user();
        // Users performing actions.
        $ua1 = $dg->create_user();
        $ua2 = $dg->create_user();
        $ua3 = $dg->create_user();

        $ug1ctx = context_user::instance($ug1->id);
        $ug2ctx = context_user::instance($ug2->id);
        $c1ctx = context_course::instance($c1->id);
        $c2ctx = context_course::instance($c2->id);

        $rootpath = [get_string('grades', 'core_grades')];
        $relatedtomepath = array_merge($rootpath, [get_string('privacy:path:relatedtome', 'core_grades')]);

        // Create the course minimal stuff.
        grade_category::fetch_course_category($c1->id);
        $ci1 = grade_item::fetch_course_item($c1->id);
        grade_category::fetch_course_category($c2->id);
        $ci2 = grade_item::fetch_course_item($c2->id);

        // Create data that will sit in the user context because we will delete the grate item.
        $gi1 = new grade_item($dg->create_grade_item(['courseid' => $c1->id, 'aggregationcoef2' => 1]), false);
        $gi1->update_final_grade($ug1->id, 100, 'test', 'Well done!', FORMAT_PLAIN, $ua2->id);
        $gi1->update_final_grade($ug1->id, 1, 'test', 'Hi', FORMAT_PLAIN, $ua2->id);
        $gi1->update_final_grade($ug3->id, 12, 'test', 'Hello', FORMAT_PLAIN, $ua2->id);

        // Create another set for another user.
        $gi2a = new grade_item($dg->create_grade_item(['courseid' => $c2->id]), false);
        $gi2a->update_final_grade($ug1->id, 15, 'test', '', FORMAT_PLAIN, $ua2->id);
        $gi2b = new grade_item($dg->create_grade_item(['courseid' => $c2->id]), false);
        $gi2b->update_final_grade($ug1->id, 30, 'test', 'Well played!', FORMAT_PLAIN, $ua2->id);

        // Export action user 1 everywhere.
        provider::export_user_data(new approved_contextlist($ua1, 'core_grades', [$ug1ctx->id, $ug2ctx->id,
            $c1ctx->id, $c2ctx->id]));
        $this->assert_context_has_no_data($ug1ctx);
        $this->assert_context_has_no_data($ug2ctx);
        $this->assert_context_has_no_data($c1ctx);
        $this->assert_context_has_no_data($c2ctx);

        // Export action user 2 in course 1.
        writer::reset();
        provider::export_user_data(new approved_contextlist($ua2, 'core_grades', [$c1ctx->id]));
        $this->assert_context_has_no_data($ug1ctx);
        $this->assert_context_has_no_data($ug2ctx);
        $this->assert_context_has_no_data($c2ctx);
        $data = writer::with_context($c1ctx)->get_data($rootpath);
        $this->assertEmpty($data);

        // Here we are testing the export of grades that we've changed.
        $data = writer::with_context($c1ctx)->get_related_data($relatedtomepath, 'grades');
        $this->assertCount(2, $data->grades);
        $this->assertEquals($gi1->get_name(), $data->grades[0]['item']);
        $this->assertEquals(1, $data->grades[0]['grade']);
        $this->assertEquals('Hi', $data->grades[0]['feedback']);
        $this->assertEquals(transform::yesno(true), $data->grades[0]['created_or_modified_by_you']);
        $this->assertEquals($gi1->get_name(), $data->grades[1]['item']);
        $this->assertEquals(12, $data->grades[1]['grade']);
        $this->assertEquals('Hello', $data->grades[1]['feedback']);
        $this->assertEquals(transform::yesno(true), $data->grades[1]['created_or_modified_by_you']);

        // Here we are testing the export of history of grades that we've changed.
        $data = writer::with_context($c1ctx)->get_related_data($relatedtomepath, 'grades_history');
        $this->assertCount(3, $data->modified_records);
        $grade = $data->modified_records[0];
        $this->assertEquals($ug1->id, $grade['userid']);
        $this->assertEquals($gi1->get_name(), $grade['item']);
        $this->assertEquals(100, $grade['grade']);
        $this->assertEquals('Well done!', $grade['feedback']);
        $this->assertEquals(transform::yesno(false), $grade['logged_in_user_was_you']);
        $this->assertEquals(transform::yesno(true), $grade['author_of_change_was_you']);
        $grade = $data->modified_records[1];
        $this->assertEquals($ug1->id, $grade['userid']);
        $this->assertEquals($gi1->get_name(), $grade['item']);
        $this->assertEquals(1, $grade['grade']);
        $this->assertEquals('Hi', $grade['feedback']);
        $this->assertEquals(transform::yesno(false), $grade['logged_in_user_was_you']);
        $this->assertEquals(transform::yesno(true), $grade['author_of_change_was_you']);
        $grade = $data->modified_records[2];
        $this->assertEquals($ug3->id, $grade['userid']);
        $this->assertEquals($gi1->get_name(), $grade['item']);
        $this->assertEquals(12, $grade['grade']);
        $this->assertEquals('Hello', $grade['feedback']);
        $this->assertEquals(transform::yesno(false), $grade['logged_in_user_was_you']);
        $this->assertEquals(transform::yesno(true), $grade['author_of_change_was_you']);

        // Create a history record with logged user.
        $this->setUser($ua3);
        $gi1->update_final_grade($ug3->id, 50, 'test', '...', FORMAT_PLAIN, $ua2->id);
        writer::reset();
        provider::export_user_data(new approved_contextlist($ua3, 'core_grades', [$c1ctx->id]));
        $data = writer::with_context($c1ctx)->get_related_data($relatedtomepath, 'grades_history');
        $this->assertCount(1, $data->modified_records);
        $grade = $data->modified_records[0];
        $this->assertEquals($ug3->id, $grade['userid']);
        $this->assertEquals($gi1->get_name(), $grade['item']);
        $this->assertEquals(50, $grade['grade']);
        $this->assertEquals('...', $grade['feedback']);
        $this->assertEquals(transform::yesno(true), $grade['logged_in_user_was_you']);
        $this->assertEquals(transform::yesno(false), $grade['author_of_change_was_you']);

        // Test that we export our own grades.
        writer::reset();
        provider::export_user_data(new approved_contextlist($ug1, 'core_grades', [$c1ctx->id]));
        $data = writer::with_context($c1ctx)->get_data($rootpath);
        $this->assert_context_has_no_data($c2ctx);
        $this->assertCount(2, $data->grades);
        $grade = $data->grades[0];
        $this->assertEquals($ci1->get_name(), $grade['item']);
        $this->assertEquals(1, $grade['grade']);
        $grade = $data->grades[1];
        $this->assertEquals($gi1->get_name(), $grade['item']);
        $this->assertEquals(1, $grade['grade']);
        $this->assertEquals('Hi', $grade['feedback']);

        // Test that we export our own grades in two courses.
        writer::reset();
        provider::export_user_data(new approved_contextlist($ug1, 'core_grades', [$ug1ctx->id, $c1ctx->id, $c2ctx->id]));
        $this->assert_context_has_no_data($ug1ctx);
        $data = writer::with_context($c1ctx)->get_data($rootpath);
        $this->assertCount(2, $data->grades);
        $grade = $data->grades[0];
        $this->assertEquals($ci1->get_name(), $grade['item']);
        $this->assertEquals(1, $grade['grade']);
        $grade = $data->grades[1];
        $this->assertEquals($gi1->get_name(), $grade['item']);
        $this->assertEquals(1, $grade['grade']);
        $this->assertEquals('Hi', $grade['feedback']);

        $data = writer::with_context($c2ctx)->get_data($rootpath);
        $this->assertCount(3, $data->grades);
        $grade = $data->grades[0];
        $this->assertEquals($ci2->get_name(), $grade['item']);
        $grade = $data->grades[1];
        $this->assertEquals($gi2a->get_name(), $grade['item']);
        $this->assertEquals(15, $grade['grade']);
        $this->assertEquals('', $grade['feedback']);
        $grade = $data->grades[2];
        $this->assertEquals($gi2b->get_name(), $grade['item']);
        $this->assertEquals(30, $grade['grade']);
        $this->assertEquals('Well played!', $grade['feedback']);

        // Delete a grade item.
        $this->setUser($ua3);
        $gi1->delete();

        // Now, we should find history of grades in our own context.
        writer::reset();
        provider::export_user_data(new approved_contextlist($ug1, 'core_grades', [$ug1ctx->id, $c1ctx->id, $c2ctx->id]));
        $data = writer::with_context($c1ctx)->get_data($rootpath);
        $this->assertCount(1, $data->grades);
        $this->assertEquals($ci1->get_name(), $data->grades[0]['item']);
        $data = writer::with_context($c2ctx)->get_data($rootpath);
        $this->assertCount(3, $data->grades);
        $data = writer::with_context($ug1ctx)->get_related_data($rootpath, 'history');
        $this->assertCount(3, $data->grades);
        $grade = $data->grades[0];
        $this->assertEquals(get_string('privacy:request:unknowndeletedgradeitem', 'core_grades'), $grade['name']);
        $this->assertEquals(100, $grade['grade']);
        $this->assertEquals('Well done!', $grade['feedback']);
        $this->assertEquals(transform::yesno(true), $grade['graded_user_was_you']);
        $this->assertEquals(transform::yesno(false), $grade['logged_in_user_was_you']);
        $this->assertEquals(transform::yesno(false), $grade['author_of_change_was_you']);
        $this->assertEquals(get_string('privacy:request:historyactioninsert', 'core_grades'), $grade['action']);
        $grade = $data->grades[1];
        $this->assertEquals(get_string('privacy:request:unknowndeletedgradeitem', 'core_grades'), $grade['name']);
        $this->assertEquals(1, $grade['grade']);
        $this->assertEquals('Hi', $grade['feedback']);
        $this->assertEquals(transform::yesno(true), $grade['graded_user_was_you']);
        $this->assertEquals(transform::yesno(false), $grade['logged_in_user_was_you']);
        $this->assertEquals(transform::yesno(false), $grade['author_of_change_was_you']);
        $this->assertEquals(get_string('privacy:request:historyactionupdate', 'core_grades'), $grade['action']);
        $grade = $data->grades[2];
        $this->assertEquals(get_string('privacy:request:unknowndeletedgradeitem', 'core_grades'), $grade['name']);
        $this->assertEquals(1, $grade['grade']);
        $this->assertEquals('Hi', $grade['feedback']);
        $this->assertEquals(transform::yesno(true), $grade['graded_user_was_you']);
        $this->assertEquals(transform::yesno(false), $grade['logged_in_user_was_you']);
        $this->assertEquals(transform::yesno(false), $grade['author_of_change_was_you']);
        $this->assertEquals(get_string('privacy:request:historyactiondelete', 'core_grades'), $grade['action']);

        // The action user 3 should have a record of the deletion in the user's context.
        writer::reset();
        provider::export_user_data(new approved_contextlist($ua3, 'core_grades', [$ug1ctx->id]));
        $data = writer::with_context($ug1ctx)->get_related_data($rootpath, 'history');
        $this->assertCount(1, $data->grades);
        $grade = $data->grades[0];
        $this->assertEquals(get_string('privacy:request:unknowndeletedgradeitem', 'core_grades'), $grade['name']);
        $this->assertEquals(1, $grade['grade']);
        $this->assertEquals('Hi', $grade['feedback']);
        $this->assertEquals(transform::yesno(true), $grade['logged_in_user_was_you']);
        $this->assertEquals(transform::yesno(false), $grade['author_of_change_was_you']);
        $this->assertEquals(get_string('privacy:request:historyactiondelete', 'core_grades'), $grade['action']);

        // The action user 2 should have a record of their edits in the user's context.
        writer::reset();
        provider::export_user_data(new approved_contextlist($ua2, 'core_grades', [$ug1ctx->id]));
        $data = writer::with_context($ug1ctx)->get_related_data($rootpath, 'history');
        $this->assertCount(3, $data->grades);
        $grade = $data->grades[0];
        $this->assertEquals(get_string('privacy:request:unknowndeletedgradeitem', 'core_grades'), $grade['name']);
        $this->assertEquals(100, $grade['grade']);
        $this->assertEquals('Well done!', $grade['feedback']);
        $this->assertEquals(transform::yesno(false), $grade['logged_in_user_was_you']);
        $this->assertEquals(transform::yesno(true), $grade['author_of_change_was_you']);
        $this->assertEquals(get_string('privacy:request:historyactioninsert', 'core_grades'), $grade['action']);
        $grade = $data->grades[1];
        $this->assertEquals(get_string('privacy:request:unknowndeletedgradeitem', 'core_grades'), $grade['name']);
        $this->assertEquals(1, $grade['grade']);
        $this->assertEquals('Hi', $grade['feedback']);
        $this->assertEquals(transform::yesno(false), $grade['logged_in_user_was_you']);
        $this->assertEquals(transform::yesno(true), $grade['author_of_change_was_you']);
        $this->assertEquals(get_string('privacy:request:historyactionupdate', 'core_grades'), $grade['action']);
        $grade = $data->grades[2];
        $this->assertEquals(get_string('privacy:request:unknowndeletedgradeitem', 'core_grades'), $grade['name']);
        $this->assertEquals(1, $grade['grade']);
        $this->assertEquals('Hi', $grade['feedback']);
        $this->assertEquals(transform::yesno(false), $grade['logged_in_user_was_you']);
        $this->assertEquals(transform::yesno(true), $grade['author_of_change_was_you']);
        $this->assertEquals(get_string('privacy:request:historyactiondelete', 'core_grades'), $grade['action']);
    }

    public function test_export_data_for_user_with_scale() {
        global $DB;
        $dg = $this->getDataGenerator();
        $c1 = $dg->create_course();
        $scale = $dg->create_scale(['scale' => 'Awesome,OK,Reasonable,Bad']);
        $u1 = $dg->create_user();
        $u2 = $dg->create_user();

        $u1ctx = context_user::instance($u1->id);
        $c1ctx = context_course::instance($c1->id);

        $rootpath = [get_string('grades', 'core_grades')];

        // Create another set for another user.
        $gi1 = new grade_item($dg->create_grade_item(['courseid' => $c1->id, 'scaleid' => $scale->id]), false);
        $gi1->update_final_grade($u1->id, 1, 'test', '', FORMAT_PLAIN, $u2->id);
        $gi2 = new grade_item($dg->create_grade_item(['courseid' => $c1->id, 'scaleid' => $scale->id]), false);
        $gi2->update_final_grade($u1->id, 3, 'test', '', FORMAT_PLAIN, $u2->id);

        // Export user's data.
        writer::reset();
        provider::export_user_data(new approved_contextlist($u1, 'core_grades', [$c1ctx->id]));
        $data = writer::with_context($c1ctx)->get_data($rootpath);
        $this->assertCount(3, $data->grades);
        $this->assertEquals(grade_item::fetch_course_item($c1->id)->get_name(), $data->grades[0]['item']);
        $this->assertEquals($gi1->get_name(), $data->grades[1]['item']);
        $this->assertEquals(1, $data->grades[1]['grade']);
        $this->assertEquals('Awesome', $data->grades[1]['grade_formatted']);
        $this->assertEquals($gi2->get_name(), $data->grades[2]['item']);
        $this->assertEquals(3, $data->grades[2]['grade']);
        $this->assertEquals('Reasonable', $data->grades[2]['grade_formatted']);
    }

    public function test_export_data_for_user_about_gradebook_edits() {
        global $DB;
        $dg = $this->getDataGenerator();
        $c1 = $dg->create_course();
        $c2 = $dg->create_course();
        $u1 = $dg->create_user();
        $u2 = $dg->create_user();
        $u3 = $dg->create_user();
        $u4 = $dg->create_user();
        $u5 = $dg->create_user();
        $u6 = $dg->create_user();

        $sysctx = context_system::instance();
        $u1ctx = context_user::instance($u1->id);
        $u2ctx = context_user::instance($u2->id);
        $u3ctx = context_user::instance($u3->id);
        $u4ctx = context_user::instance($u4->id);
        $u5ctx = context_user::instance($u5->id);
        $u6ctx = context_user::instance($u6->id);
        $c1ctx = context_course::instance($c1->id);
        $c2ctx = context_course::instance($c2->id);

        $rootpath = [get_string('grades', 'core_grades')];
        $relatedtomepath = array_merge($rootpath, [get_string('privacy:path:relatedtome', 'core_grades')]);
        $allcontexts = [$sysctx->id, $c1ctx->id, $c2ctx->id, $u1ctx->id, $u2ctx->id, $u3ctx->id, $u4ctx->id,
            $u5ctx->id, $u6ctx->id];
        $updateactionstr = get_string('privacy:request:historyactionupdate', 'core_grades');

        // Create some stuff.
        $gi1a = new grade_item($dg->create_grade_item(['courseid' => $c1->id]), false);
        $gi1b = new grade_item($dg->create_grade_item(['courseid' => $c1->id]), false);
        $gi2a = new grade_item($dg->create_grade_item(['courseid' => $c2->id]), false);
        $gc1a = new grade_category($dg->create_grade_category(['courseid' => $c1->id]), false);
        $gc1b = new grade_category($dg->create_grade_category(['courseid' => $c1->id]), false);
        $gc2a = new grade_category($dg->create_grade_category(['courseid' => $c2->id]), false);
        $go2 = new grade_outcome($dg->create_grade_outcome(['courseid' => $c2->id, 'shortname' => 'go2',
            'fullname' => 'go2']), false);

        $go0 = new grade_outcome(['shortname' => 'go0', 'fullname' => 'go0', 'usermodified' => $u1->id]);
        $go0->insert();
        $go1 = new grade_outcome(['shortname' => 'go1', 'fullname' => 'go1', 'courseid' => $c1->id, 'usermodified' => $u1->id]);
        $go1->insert();

        // User 2 creates history.
        $this->setUser($u2);
        $go0->shortname .= ' edited';
        $go0->update();
        $gc1a->fullname .= ' edited';
        $gc1a->update();

        // User 3 creates history.
        $this->setUser($u3);
        $go1->shortname .= ' edited';
        $go1->update();
        $gc2a->fullname .= ' a';
        $gc2a->update();

        // User 4 updates an outcome in course (creates history).
        $this->setUser($u4);
        $go2->shortname .= ' edited';
        $go2->update();

        // User 5 updates an item.
        $this->setUser($u5);
        $gi1a->itemname .= ' edited';
        $gi1a->update();

        // User 6 creates history.
        $this->setUser($u6);
        $gi2a->delete();

        $this->setAdminUser();

        // Export data for u1.
        writer::reset();
        provider::export_user_data(new approved_contextlist($u1, 'core_grades', $allcontexts));
        $data = writer::with_context($sysctx)->get_related_data($relatedtomepath, 'outcomes');
        $this->assertCount(1, $data->outcomes);
        $this->assertEquals($go0->shortname, $data->outcomes[0]['shortname']);
        $this->assertEquals($go0->fullname, $data->outcomes[0]['fullname']);
        $this->assertEquals(transform::yesno(true), $data->outcomes[0]['created_or_modified_by_you']);
        $data = writer::with_context($c1ctx)->get_related_data($relatedtomepath, 'outcomes');
        $this->assertCount(1, $data->outcomes);
        $this->assertEquals($go1->shortname, $data->outcomes[0]['shortname']);
        $this->assertEquals($go1->fullname, $data->outcomes[0]['fullname']);
        $this->assertEquals(transform::yesno(true), $data->outcomes[0]['created_or_modified_by_you']);
        $data = writer::with_context($sysctx)->get_related_data($relatedtomepath, 'outcomes_history');
        $this->assertEmpty($data);
        $data = writer::with_context($c1ctx)->get_related_data($relatedtomepath, 'outcomes_history');
        $this->assertEmpty($data);

        // Export data for u2.
        writer::reset();
        provider::export_user_data(new approved_contextlist($u2, 'core_grades', $allcontexts));
        $data = writer::with_context($sysctx)->get_related_data($relatedtomepath, 'outcomes');
        $this->assertEmpty($data);
        $data = writer::with_context($c1ctx)->get_related_data($relatedtomepath, 'outcomes');
        $this->assertEmpty($data);
        $data = writer::with_context($sysctx)->get_related_data($relatedtomepath, 'outcomes_history');
        $this->assertCount(1, $data->modified_records);
        $this->assertEquals($go0->shortname, $data->modified_records[0]['shortname']);
        $this->assertEquals($go0->fullname, $data->modified_records[0]['fullname']);
        $this->assertEquals(transform::yesno(true), $data->modified_records[0]['logged_in_user_was_you']);
        $this->assertEquals($updateactionstr, $data->modified_records[0]['action']);

        $data = writer::with_context($c1ctx)->get_related_data($relatedtomepath, 'categories_history');
        $this->assertCount(1, $data->modified_records);
        $this->assertEquals($gc1a->fullname, $data->modified_records[0]['name']);
        $this->assertEquals(transform::yesno(true), $data->modified_records[0]['logged_in_user_was_you']);
        $this->assertEquals($updateactionstr, $data->modified_records[0]['action']);

        // Export data for u3.
        writer::reset();
        provider::export_user_data(new approved_contextlist($u3, 'core_grades', $allcontexts));
        $data = writer::with_context($c1ctx)->get_related_data($relatedtomepath, 'outcomes_history');
        $this->assertCount(1, $data->modified_records);
        $this->assertEquals($go1->shortname, $data->modified_records[0]['shortname']);
        $this->assertEquals($go1->fullname, $data->modified_records[0]['fullname']);
        $this->assertEquals(transform::yesno(true), $data->modified_records[0]['logged_in_user_was_you']);
        $this->assertEquals($updateactionstr, $data->modified_records[0]['action']);

        $data = writer::with_context($c2ctx)->get_related_data($relatedtomepath, 'categories_history');
        $this->assertCount(1, $data->modified_records);
        $this->assertEquals($gc2a->fullname, $data->modified_records[0]['name']);
        $this->assertEquals(transform::yesno(true), $data->modified_records[0]['logged_in_user_was_you']);
        $this->assertEquals($updateactionstr, $data->modified_records[0]['action']);

        // Export data for u4.
        writer::reset();
        provider::export_user_data(new approved_contextlist($u4, 'core_grades', $allcontexts));
        $data = writer::with_context($c2ctx)->get_related_data($relatedtomepath, 'outcomes_history');
        $this->assertCount(1, $data->modified_records);
        $this->assertEquals($go2->shortname, $data->modified_records[0]['shortname']);
        $this->assertEquals($go2->fullname, $data->modified_records[0]['fullname']);
        $this->assertEquals(transform::yesno(true), $data->modified_records[0]['logged_in_user_was_you']);
        $this->assertEquals($updateactionstr, $data->modified_records[0]['action']);

        // Export data for u5.
        writer::reset();
        provider::export_user_data(new approved_contextlist($u5, 'core_grades', $allcontexts));
        $data = writer::with_context($c1ctx)->get_related_data($relatedtomepath, 'items_history');
        $this->assertCount(1, $data->modified_records);
        $this->assertEquals($gi1a->itemname, $data->modified_records[0]['name']);
        $this->assertEquals(transform::yesno(true), $data->modified_records[0]['logged_in_user_was_you']);
        $this->assertEquals($updateactionstr, $data->modified_records[0]['action']);

        // Export data for u6.
        writer::reset();
        provider::export_user_data(new approved_contextlist($u6, 'core_grades', $allcontexts));
        $data = writer::with_context($c1ctx)->get_related_data($relatedtomepath, 'items_history');
        $this->assertEmpty($data);
        $data = writer::with_context($c2ctx)->get_related_data($relatedtomepath, 'items_history');
        $this->assertCount(1, $data->modified_records);
        $this->assertEquals($gi2a->itemname, $data->modified_records[0]['name']);
        $this->assertEquals(transform::yesno(true), $data->modified_records[0]['logged_in_user_was_you']);
        $this->assertEquals(get_string('privacy:request:historyactiondelete', 'core_grades'),
            $data->modified_records[0]['action']);
    }

    /**
     * Assert there is no grade data in the context.
     *
     * @param context $context The context.
     * @return void
     */
    protected function assert_context_has_no_data(context $context) {
        $rootpath = [get_string('grades', 'core_grades')];
        $relatedtomepath = array_merge($rootpath, [get_string('privacy:path:relatedtome', 'core_grades')]);

        $data = writer::with_context($context)->get_data($rootpath);
        $this->assertEmpty($data);

        $data = writer::with_context($context)->get_related_data($rootpath, 'history');
        $this->assertEmpty($data);

        $files = ['categories_history', 'items_history', 'outcomes', 'outcomes_history', 'grades', 'grades_history', 'history'];
        foreach ($files as $file) {
            $data = writer::with_context($context)->get_related_data($relatedtomepath, $file);
            $this->assertEmpty($data);
        }
    }
}
