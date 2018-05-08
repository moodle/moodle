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
 * Privacy tests for core_tag.
 *
 * @package    core_comment
 * @category   test
 * @copyright  2018 Zig Tan <zig@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
global $CFG;

require_once($CFG->dirroot . '/tag/lib.php');

use \core_privacy\tests\provider_testcase;
use \core_privacy\local\request\writer;
use \core_tag\privacy\provider;

/**
 * Unit tests for tag/classes/privacy/policy
 *
 * @copyright  2018 Zig Tan <zig@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_tag_privacy_testcase extends provider_testcase {

    /**
     * Check the exporting of tags for a user id in a context.
     */
    public function test_export_tags() {
        global $DB;

        $this->resetAfterTest(true);

        // Create a user to perform tagging.
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        // Create a course to tag.
        $course = $this->getDataGenerator()->create_course();
        $context = context_course::instance($course->id);
        $subcontext = [];

        // Create three dummy tags and tag instances.
        $dummytags = [ 'Tag 1', 'Tag 2', 'Tag 3' ];
        core_tag_tag::set_item_tags('core_course', 'course', $course->id, context_course::instance($course->id),
                                    $dummytags, $user->id);

        // Get the tag instances that should have been created.
        $taginstances = $DB->get_records('tag_instance', array('itemtype' => 'course', 'itemid' => $course->id));
        $this->assertCount(count($dummytags), $taginstances);

        // Check tag instances match the component and context.
        foreach ($taginstances as $taginstance) {
            $this->assertEquals('core_course', $taginstance->component);
            $this->assertEquals(context_course::instance($course->id)->id, $taginstance->contextid);
        }

        // Retrieve tags only for this user.
        provider::export_item_tags($user->id, $context, $subcontext, 'core_course', 'course', $course->id, true);

        $writer = writer::with_context($context);
        $this->assertTrue($writer->has_any_data());

        $exportedtags = $writer->get_related_data($subcontext, 'tags');
        $this->assertCount(count($dummytags), $exportedtags);

        // Check the exported tag's rawname is found in the initial dummy tags.
        foreach ($exportedtags as $exportedtag) {
            $this->assertContains($exportedtag, $dummytags);
        }
    }

    /**
     * Test method delete_item_tags().
     */
    public function test_delete_item_tags() {
        global $DB;

        $this->resetAfterTest(true);

        // Create a course to tag.
        $course1 = $this->getDataGenerator()->create_course();
        $context1 = context_course::instance($course1->id);
        $course2 = $this->getDataGenerator()->create_course();
        $context2 = context_course::instance($course2->id);

        // Tag courses.
        core_tag_tag::set_item_tags('core_course', 'course', $course1->id, $context1, ['Tag 1', 'Tag 2', 'Tag 3']);
        core_tag_tag::set_item_tags('core_course', 'course', $course2->id, $context2, ['Tag 1', 'Tag 2']);

        $expectedtagcount = $DB->count_records('tag_instance');
        // Delete tags for course1.
        core_tag\privacy\provider::delete_item_tags($context1, 'core_course', 'course');
        $expectedtagcount -= 3;
        $this->assertEquals($expectedtagcount, $DB->count_records('tag_instance'));

        // Delete tags for course2. Use wrong itemid.
        core_tag\privacy\provider::delete_item_tags($context2, 'core_course', 'course', $course1->id);
        $this->assertEquals($expectedtagcount, $DB->count_records('tag_instance'));

        // Use correct itemid.
        core_tag\privacy\provider::delete_item_tags($context2, 'core_course', 'course', $course2->id);
        $expectedtagcount -= 2;
        $this->assertEquals($expectedtagcount, $DB->count_records('tag_instance'));
    }

    /**
     * Test method delete_item_tags_select().
     */
    public function test_delete_item_tags_select() {
        global $DB;

        $this->resetAfterTest(true);

        // Create a course to tag.
        $course1 = $this->getDataGenerator()->create_course();
        $context1 = context_course::instance($course1->id);
        $course2 = $this->getDataGenerator()->create_course();
        $context2 = context_course::instance($course2->id);

        // Tag courses.
        core_tag_tag::set_item_tags('core_course', 'course', $course1->id, $context1, ['Tag 1', 'Tag 2', 'Tag 3']);
        core_tag_tag::set_item_tags('core_course', 'course', $course2->id, $context2, ['Tag 1', 'Tag 2']);

        $expectedtagcount = $DB->count_records('tag_instance');
        // Delete tags for course1.
        list($sql, $params) = $DB->get_in_or_equal([$course1->id, $course2->id], SQL_PARAMS_NAMED);
        core_tag\privacy\provider::delete_item_tags_select($context1, 'core_course', 'course', $sql, $params);
        $expectedtagcount -= 3;
        $this->assertEquals($expectedtagcount, $DB->count_records('tag_instance'));

        // Delete tags for course2.
        core_tag\privacy\provider::delete_item_tags_select($context2, 'core_course', 'course', $sql, $params);
        $expectedtagcount -= 2;
        $this->assertEquals($expectedtagcount, $DB->count_records('tag_instance'));
    }

    protected function set_up_tags() {
        global $CFG;
        require_once($CFG->dirroot.'/user/editlib.php');

        $this->resetAfterTest(true);

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        $this->setUser($user1);
        useredit_update_interests($user1, ['Birdwatching', 'Computers']);

        $this->setUser($user2);
        useredit_update_interests($user2, ['computers']);

        $this->setAdminUser();

        $tag = core_tag_tag::get_by_name(0, 'computers', '*');
        $tag->update(['description' => '<img src="@@PLUGINFILE@@/computer.jpg">']);
        get_file_storage()->create_file_from_string([
            'contextid' => context_system::instance()->id,
            'component' => 'tag',
            'filearea' => 'description',
            'itemid' => $tag->id,
            'filepath' => '/',
            'filename' => 'computer.jpg'
        ], "jpg:image");

        return [$user1, $user2];
    }

    public function test_export_item_tags() {
        list($user1, $user2) = $this->set_up_tags();
        $this->assertEquals([context_system::instance()->id],
            provider::get_contexts_for_userid($user1->id)->get_contextids());
        $this->assertEmpty(provider::get_contexts_for_userid($user2->id)->get_contextids());
    }

    public function test_delete_data_for_user() {
        global $DB;
        list($user1, $user2) = $this->set_up_tags();
        $context = context_system::instance();
        $this->assertEquals(2, $DB->count_records('tag', []));
        $this->assertEquals(0, $DB->count_records('tag', ['userid' => 0]));
        provider::delete_data_for_user(new \core_privacy\local\request\approved_contextlist($user2, 'core_tag', [$context->id]));
        $this->assertEquals(2, $DB->count_records('tag', []));
        $this->assertEquals(0, $DB->count_records('tag', ['userid' => 0]));
        provider::delete_data_for_user(new \core_privacy\local\request\approved_contextlist($user1, 'core_tag', [$context->id]));
        $this->assertEquals(2, $DB->count_records('tag', []));
        $this->assertEquals(2, $DB->count_records('tag', ['userid' => 0]));
    }

    public function test_delete_data_for_all_users_in_context() {
        global $DB;
        $course = $this->getDataGenerator()->create_course();
        list($user1, $user2) = $this->set_up_tags();
        $this->assertEquals(2, $DB->count_records('tag', []));
        $this->assertEquals(3, $DB->count_records('tag_instance', []));
        provider::delete_data_for_all_users_in_context(context_course::instance($course->id));
        $this->assertEquals(2, $DB->count_records('tag', []));
        $this->assertEquals(3, $DB->count_records('tag_instance', []));
        provider::delete_data_for_all_users_in_context(context_system::instance());
        $this->assertEquals(0, $DB->count_records('tag', []));
        $this->assertEquals(0, $DB->count_records('tag_instance', []));
    }

    public function test_export_data_for_user() {
        global $DB;
        list($user1, $user2) = $this->set_up_tags();
        $context = context_system::instance();
        provider::export_user_data(new \core_privacy\local\request\approved_contextlist($user2, 'core_tag', [$context->id]));
        $this->assertFalse(writer::with_context($context)->has_any_data());

        $tagids = array_values(array_map(function($tag) {
            return $tag->id;
        }, core_tag_tag::get_by_name_bulk(core_tag_collection::get_default(), ['Birdwatching', 'Computers'])));

        provider::export_user_data(new \core_privacy\local\request\approved_contextlist($user1, 'core_tag', [$context->id]));
        $writer = writer::with_context($context);

        $data = $writer->get_data(['Tags', $tagids[0]]);
        $files = $writer->get_files(['Tags', $tagids[0]]);
        $this->assertEquals('Birdwatching', $data->rawname);
        $this->assertEmpty($files);

        $data = $writer->get_data(['Tags', $tagids[1]]);
        $files = $writer->get_files(['Tags', $tagids[1]]);
        $this->assertEquals('Computers', $data->rawname);
        $this->assertEquals(['computer.jpg'], array_keys($files));
    }
}
