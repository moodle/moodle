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
 * Privacy provider tests
 *
 * @package   mod_checklist
 * @copyright 2018 Davo Smith
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core_privacy\local\metadata\collection;
use mod_checklist\privacy\provider;

defined('MOODLE_INTERNAL') || die();

class mod_checklist_privacy_provider_testcase extends \core_privacy\tests\provider_testcase {
    /** @var stdClass The student object. */
    protected $student;

    /** @var stdClass[] The checklist objects. */
    protected $checklists = [];

    /** @var stdClass The course object. */
    protected $course;

    /**
     * {@inheritdoc}
     */
    protected function setUp() {
        $this->resetAfterTest();

        global $DB;
        $gen = self::getDataGenerator();
        $this->course = $gen->create_course();

        // Create 4 checklists.
        /** @var mod_checklist_generator $plugingen */
        $plugingen = $gen->get_plugin_generator('mod_checklist');
        $params = [
            'course' => $this->course->id,
        ];
        $this->checklists = [];
        $this->checklists[] = $plugingen->create_instance($params);
        $this->checklists[] = $plugingen->create_instance($params);
        $this->checklists[] = $plugingen->create_instance($params);
        $this->checklists[] = $plugingen->create_instance($params);

        $itemtexts = ['First item', 'Second item', 'Third item'];
        foreach ($this->checklists as $checklist) {
            $position = 1;
            foreach ($itemtexts as $text) {
                $item = new \mod_checklist\local\checklist_item([], false);
                $item->checklist = $checklist->id;
                $item->userid = 0;
                $item->displaytext = $text;
                $item->position = $position++;
                $item->insert();
            }
        }

        // Create a student who will add data to these checklists.
        $this->student = $gen->create_user();
        $studentrole = $DB->get_record('role', ['shortname' => 'student']);
        $gen->enrol_user($this->student->id, $this->course->id, $studentrole->id);

        // The first checklist includes checkmarks from the student.
        /** @var \mod_checklist\local\checklist_item[] $items */
        $items = \mod_checklist\local\checklist_item::fetch_all(['checklist' => $this->checklists[0]->id], true);
        $items = array_values($items);
        $items[0]->set_checked_student($this->student->id, true);
        $items[2]->set_checked_student($this->student->id, true);

        // The second checklist includes custom items created by the student.
        $item = new \mod_checklist\local\checklist_item([], false);
        $item->checklist = $this->checklists[1]->id;
        $item->displaytext = 'Student private item';
        $item->position = 1;
        $item->userid = $this->student->id;
        $item->insert();

        // The third checklist includes comments made by a teacher about the student.
        $items = \mod_checklist\local\checklist_item::fetch_all(['checklist' => $this->checklists[2]->id], true);
        $items = array_values($items);
        $comment = new \mod_checklist\local\checklist_comment([], false);
        $comment->itemid = $items[1]->id;
        $comment->userid = $this->student->id;
        $comment->text = 'A comment added about a student';
        $comment->insert();

        // The fourth checklist does not include any user data for the given student.
    }

    /**
     * Test for provider::get_metadata().
     */
    public function test_get_metadata() {
        $collection = new collection('mod_checklist');
        $newcollection = provider::get_metadata($collection);
        $itemcollection = $newcollection->get_collection();
        $this->assertCount(3, $itemcollection);

        $table = array_shift($itemcollection);
        $this->assertEquals('checklist_item', $table->get_name());
        $privacyfields = $table->get_privacy_fields();
        $this->assertArrayHasKey('checklist', $privacyfields);
        $this->assertArrayHasKey('userid', $privacyfields);
        $this->assertArrayHasKey('displaytext', $privacyfields);
        $this->assertEquals('privacy:metadata:checklist_item', $table->get_summary());

        $table = array_shift($itemcollection);
        $this->assertEquals('checklist_check', $table->get_name());
        $privacyfields = $table->get_privacy_fields();
        $this->assertArrayHasKey('item', $privacyfields);
        $this->assertArrayHasKey('userid', $privacyfields);
        $this->assertArrayHasKey('usertimestamp', $privacyfields);
        $this->assertArrayHasKey('teachermark', $privacyfields);
        $this->assertArrayHasKey('teachertimestamp', $privacyfields);
        $this->assertArrayHasKey('teacherid', $privacyfields);
        $this->assertEquals('privacy:metadata:checklist_check', $table->get_summary());

        $table = array_shift($itemcollection);
        $this->assertEquals('checklist_comment', $table->get_name());
        $privacyfields = $table->get_privacy_fields();
        $this->assertArrayHasKey('itemid', $privacyfields);
        $this->assertArrayHasKey('userid', $privacyfields);
        $this->assertArrayHasKey('commentby', $privacyfields);
        $this->assertArrayHasKey('text', $privacyfields);
        $this->assertEquals('privacy:metadata:checklist_comment', $table->get_summary());
    }

    /**
     * Test for provider::get_contexts_for_userid().
     */
    public function test_get_contexts_for_userid() {
        $cms = [
            get_coursemodule_from_instance('checklist', $this->checklists[0]->id),
            get_coursemodule_from_instance('checklist', $this->checklists[1]->id),
            get_coursemodule_from_instance('checklist', $this->checklists[2]->id),
            get_coursemodule_from_instance('checklist', $this->checklists[3]->id),
        ];
        $expectedctxs = [
            context_module::instance($cms[0]->id),
            context_module::instance($cms[1]->id),
            context_module::instance($cms[2]->id),
        ];
        $expectedctxids = [];
        foreach ($expectedctxs as $ctx) {
            $expectedctxids[] = $ctx->id;
        }
        $contextlist = provider::get_contexts_for_userid($this->student->id);
        $this->assertCount(3, $contextlist);
        $uctxids = [];
        foreach ($contextlist as $uctx) {
            $uctxids[] = $uctx->id;
        }
        $this->assertEmpty(array_diff($expectedctxids, $uctxids));
        $this->assertEmpty(array_diff($uctxids, $expectedctxids));
    }

    /**
     * Test for provider::export_user_data().
     */
    public function test_export_for_context() {
        $cms = [
            get_coursemodule_from_instance('checklist', $this->checklists[0]->id),
            get_coursemodule_from_instance('checklist', $this->checklists[1]->id),
            get_coursemodule_from_instance('checklist', $this->checklists[2]->id),
            get_coursemodule_from_instance('checklist', $this->checklists[3]->id),
        ];
        $ctxs = [
            context_module::instance($cms[0]->id),
            context_module::instance($cms[1]->id),
            context_module::instance($cms[2]->id),
            context_module::instance($cms[3]->id),
        ];

        // Export all of the data for the context.
        $this->export_context_data_for_user($this->student->id, $ctxs[0], 'mod_checklist');
        $writer = \core_privacy\local\request\writer::with_context($ctxs[0]);
        $this->assertTrue($writer->has_any_data());

        $this->export_context_data_for_user($this->student->id, $ctxs[1], 'mod_checklist');
        $writer = \core_privacy\local\request\writer::with_context($ctxs[1]);
        $this->assertTrue($writer->has_any_data());

        $this->export_context_data_for_user($this->student->id, $ctxs[2], 'mod_checklist');
        $writer = \core_privacy\local\request\writer::with_context($ctxs[2]);
        $this->assertTrue($writer->has_any_data());

        $this->export_context_data_for_user($this->student->id, $ctxs[3], 'mod_checklist');
        $writer = \core_privacy\local\request\writer::with_context($ctxs[3]);
        $this->assertFalse($writer->has_any_data());
    }

    /**
     * Test for provider::delete_data_for_all_users_in_context().
     */
    public function test_delete_data_for_all_users_in_context() {
        global $DB;

        $gen = self::getDataGenerator();

        // Create another student who will check-off some items in the second checklist.
        $student = $gen->create_user();
        $studentrole = $DB->get_record('role', ['shortname' => 'student']);
        $gen->enrol_user($student->id, $this->course->id, $studentrole->id);

        /** @var \mod_checklist\local\checklist_item[] $items */
        $items = \mod_checklist\local\checklist_item::fetch_all(['checklist' => $this->checklists[1]->id]);
        $items = array_values($items);
        $items[1]->set_checked_student($student->id, true);

        // Before deletion, we should have 3 checked off items, 1 custom item, 1 comment and 13 total items.
        $this->assertEquals(3, $DB->count_records_select('checklist_check', 'usertimestamp > 0'));
        $this->assertEquals(1, $DB->count_records_select('checklist_item', 'userid <> 0'));
        $this->assertEquals(1, $DB->count_records('checklist_comment', []));
        $this->assertEquals(13, $DB->count_records('checklist_item', []));

        // Delete data from the first checklist.
        $cm = get_coursemodule_from_instance('checklist', $this->checklists[0]->id);
        $cmcontext = context_module::instance($cm->id);
        provider::delete_data_for_all_users_in_context($cmcontext);
        // After deletion, there should be 1 item checked-off, 1 custom item, 1 comment and 13 total items.
        $this->assertEquals(1, $DB->count_records_select('checklist_check', 'usertimestamp > 0'));
        $this->assertEquals(1, $DB->count_records_select('checklist_item', 'userid <> 0'));
        $this->assertEquals(1, $DB->count_records('checklist_comment', []));
        $this->assertEquals(13, $DB->count_records('checklist_item', []));

        // Delete data from the second checklist.
        $cm = get_coursemodule_from_instance('checklist', $this->checklists[1]->id);
        $cmcontext = context_module::instance($cm->id);
        provider::delete_data_for_all_users_in_context($cmcontext);
        // After deletion, there should be 0 items checked-off, 0 custom items, 1 comment and 12 total items.
        $this->assertEquals(0, $DB->count_records_select('checklist_check', 'usertimestamp > 0'));
        $this->assertEquals(0, $DB->count_records_select('checklist_item', 'userid <> 0'));
        $this->assertEquals(1, $DB->count_records('checklist_comment', []));
        $this->assertEquals(12, $DB->count_records('checklist_item', []));

        // Delete data from the third checklist.
        $cm = get_coursemodule_from_instance('checklist', $this->checklists[2]->id);
        $cmcontext = context_module::instance($cm->id);
        provider::delete_data_for_all_users_in_context($cmcontext);
        // After deletion, there should be 0 items checked-off, 0 custom items, 0 comment and 12 total items.
        $this->assertEquals(0, $DB->count_records_select('checklist_check', 'usertimestamp > 0'));
        $this->assertEquals(0, $DB->count_records_select('checklist_item', 'userid <> 0'));
        $this->assertEquals(0, $DB->count_records('checklist_comment', []));
        $this->assertEquals(12, $DB->count_records('checklist_item', []));
    }

    /**
     * Test for provider::delete_data_for_user().
     */
    public function test_delete_data_for_user() {
        global $DB;

        $gen = self::getDataGenerator();
        $cms = [
            get_coursemodule_from_instance('checklist', $this->checklists[0]->id),
            get_coursemodule_from_instance('checklist', $this->checklists[1]->id),
            get_coursemodule_from_instance('checklist', $this->checklists[2]->id),
            get_coursemodule_from_instance('checklist', $this->checklists[3]->id),
        ];
        $ctxs = [];
        foreach ($cms as $cm) {
            $ctxs[] = context_module::instance($cm->id);
        }

        // Create a second student who will gain some check-off, custom item + comment data in the first checklist.
        $student = $gen->create_user();
        $studentrole = $DB->get_record('role', ['shortname' => 'student']);
        $gen->enrol_user($this->student->id, $this->course->id, $studentrole->id);

        /** @var \mod_checklist\local\checklist_item[] $items */
        $items = \mod_checklist\local\checklist_item::fetch_all(['checklist' => $this->checklists[0]->id], true);
        $items = array_values($items);
        $items[1]->set_checked_student($student->id, true);

        $item = new \mod_checklist\local\checklist_item([], false);
        $item->checklist = $this->checklists[1]->id;
        $item->displaytext = 'Student 2\'s private item';
        $item->position = 2;
        $item->userid = $student->id;
        $item->insert();

        $comment = new \mod_checklist\local\checklist_comment([], false);
        $comment->itemid = $items[1]->id;
        $comment->userid = $student->id;
        $comment->text = 'A comment added about student 2';
        $comment->insert();

        // Before deletion, we should have 3 checked off items, 2 custom items, 2 comments and 14 total items.
        $this->assertEquals(3, $DB->count_records_select('checklist_check', 'usertimestamp > 0'));
        $this->assertEquals(2, $DB->count_records_select('checklist_item', 'userid <> 0'));
        $this->assertEquals(2, $DB->count_records('checklist_comment', []));
        $this->assertEquals(14, $DB->count_records('checklist_item', []));

        // Delete the data for the first student, but only for the first checklist.
        $contextlist = new \core_privacy\local\request\approved_contextlist($this->student, 'checklist',
                                                                            [$ctxs[0]->id]);
        provider::delete_data_for_user($contextlist);

        // After deletion, we should have 1 checked off item, 2 custom items, 2 comments and 14 total items.
        $this->assertEquals(1, $DB->count_records_select('checklist_check', 'usertimestamp > 0'));
        $this->assertEquals(2, $DB->count_records_select('checklist_item', 'userid <> 0'));
        $this->assertEquals(2, $DB->count_records('checklist_comment', []));
        $this->assertEquals(14, $DB->count_records('checklist_item', []));
        // Confirm the remaining checked-off item is for the second student.
        $this->assertEquals($student->id, $DB->get_field('checklist_check', 'userid', []));

        // Delete the data for the first student, for all checklists.
        $contextids = [$ctxs[0]->id, $ctxs[1]->id, $ctxs[2]->id, $ctxs[3]->id];
        $contextlist = new \core_privacy\local\request\approved_contextlist($this->student, 'checklist', $contextids);
        provider::delete_data_for_user($contextlist);

        // After deletion, we should have 1 checked off item, 1 custom item, 1 comment and 13 total items.
        $this->assertEquals(1, $DB->count_records_select('checklist_check', 'usertimestamp > 0'));
        $this->assertEquals(1, $DB->count_records_select('checklist_item', 'userid <> 0'));
        $this->assertEquals(1, $DB->count_records('checklist_comment', []));
        $this->assertEquals(13, $DB->count_records('checklist_item', []));
        // Confirm the remaining data is for the second student.
        $this->assertEquals($student->id, $DB->get_field('checklist_check', 'userid', []));
        $this->assertEquals($student->id, $DB->get_field_select('checklist_item', 'userid', 'userid <> 0'));
        $this->assertEquals($student->id, $DB->get_field('checklist_comment', 'userid', []));
    }
}