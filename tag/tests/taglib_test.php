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
 * Tag related unit tests.
 *
 * @package core_tag
 * @category test
 * @copyright 2014 Mark Nelson <markn@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/tag/lib.php');

class core_tag_taglib_testcase extends advanced_testcase {

    /**
     * Test set up.
     *
     * This is executed before running any test in this file.
     */
    public function setUp() {
        $this->resetAfterTest();
    }

    /**
     * Test the tag_set function.
     */
    public function test_tag_set() {
        global $DB;

        // Create a course to tag.
        $course = $this->getDataGenerator()->create_course();

        // Create the tag and tag instance.
        tag_set('course', $course->id, array('A random tag'), 'core', context_course::instance($course->id)->id);

        // Get the tag instance that should have been created.
        $taginstance = $DB->get_record('tag_instance', array('itemtype' => 'course', 'itemid' => $course->id), '*', MUST_EXIST);
        $this->assertEquals('core', $taginstance->component);
        $this->assertEquals(context_course::instance($course->id)->id, $taginstance->contextid);

        // Now call the tag_set function without specifying the component or contextid and
        // ensure the function debugging is called.
        tag_set('course', $course->id, array('Another tag'));
        $this->assertDebuggingCalled();
    }

    /**
     * Test the tag_set_add function.
     */
    public function test_tag_set_add() {
        global $DB;

        // Create a course to tag.
        $course = $this->getDataGenerator()->create_course();

        // Create the tag and tag instance.
        tag_set_add('course', $course->id, 'A random tag', 'core', context_course::instance($course->id)->id);

        // Get the tag instance that should have been created.
        $taginstance = $DB->get_record('tag_instance', array('itemtype' => 'course', 'itemid' => $course->id), '*', MUST_EXIST);
        $this->assertEquals('core', $taginstance->component);
        $this->assertEquals(context_course::instance($course->id)->id, $taginstance->contextid);

        // Remove the tag we just created.
        $tag = $DB->get_record('tag', array('rawname' => 'A random tag'));
        tag_delete($tag->id);

        // Now call the tag_set_add function without specifying the component or
        // contextid and ensure the function debugging is called.
        tag_set_add('course', $course->id, 'Another tag');
        $this->assertDebuggingCalled();
    }

    /**
     * Test the tag_set_delete function.
     */
    public function test_tag_set_delete() {
        global $DB;

        // Create a course to tag.
        $course = $this->getDataGenerator()->create_course();

        // Create the tag and tag instance we are going to delete.
        tag_set_add('course', $course->id, 'A random tag', 'core', context_course::instance($course->id)->id);

        // Call the tag_set_delete function.
        tag_set_delete('course', $course->id, 'a random tag', 'core', context_course::instance($course->id)->id);

        // Now check that there are no tags or tag instances.
        $this->assertEquals(0, $DB->count_records('tag'));
        $this->assertEquals(0, $DB->count_records('tag_instance'));

        // Recreate the tag and tag instance.
        tag_set_add('course', $course->id, 'A random tag', 'core', context_course::instance($course->id)->id);

        // Now call the tag_set_delete function without specifying the component or
        // contextid and ensure the function debugging is called.
        tag_set_delete('course', $course->id, 'A random tag');
        $this->assertDebuggingCalled();
    }

    /**
     * Test the tag_assign function.
     */
    public function test_tag_assign() {
        global $DB;

        // Create a course to tag.
        $course = $this->getDataGenerator()->create_course();

        // Create the tag.
        $tag = $this->getDataGenerator()->create_tag();

        // Tag the course with the tag we created.
        tag_assign('course', $course->id, $tag->id, 0, 0, 'core', context_course::instance($course->id)->id);

        // Get the tag instance that should have been created.
        $taginstance = $DB->get_record('tag_instance', array('itemtype' => 'course', 'itemid' => $course->id), '*', MUST_EXIST);
        $this->assertEquals('core', $taginstance->component);
        $this->assertEquals(context_course::instance($course->id)->id, $taginstance->contextid);

        // Now call the tag_assign function without specifying the component or
        // contextid and ensure the function debugging is called.
        tag_assign('course', $course->id, $tag->id, 0, 0);
        $this->assertDebuggingCalled();
    }

    /**
     * Test the tag cleanup function used by the cron.
     */
    public function test_tag_cleanup() {
        global $DB;

        // Create some users.
        $users = array();
        for ($i = 0; $i < 10; $i++) {
            $users[] = $this->getDataGenerator()->create_user();
        }

        // Create a course to tag.
        $course = $this->getDataGenerator()->create_course();
        $context = context_course::instance($course->id);

        // Test clean up instances with tags that no longer exist.
        $tags = array();
        for ($i = 0; $i < 10; $i++) {
            $tags[] = $this->getDataGenerator()->create_tag(array('userid' => $users[0]->id));
        }
        // Create instances with the tags.
        foreach ($tags as $tag) {
            tag_assign('course', $course->id, $tag->id, 0, 0, 'core', $context->id);
        }
        // We should now have ten tag instances.
        $coursetaginstances = $DB->count_records('tag_instance', array('itemtype' => 'course'));
        $this->assertEquals(10, $coursetaginstances);
        // Delete four tags
        // Manual delete of tags is done as the function will remove the instances as well.
        $DB->delete_records('tag', array('id' => $tags[6]->id));
        $DB->delete_records('tag', array('id' => $tags[7]->id));
        $DB->delete_records('tag', array('id' => $tags[8]->id));
        $DB->delete_records('tag', array('id' => $tags[9]->id));
        // Clean up the tags.
        tag_cleanup();
        // Check that we now only have six tag_instance records left.
        $coursetaginstances = $DB->count_records('tag_instance', array('itemtype' => 'course'));
        $this->assertEquals(6, $coursetaginstances);

        // Test clean up with users that have been deleted.
        // Create a tag for this course.
        foreach ($users as $user) {
            tag_assign('user', $user->id, $tags[0]->id, 0, 0, 'core', $context->id);
        }
        $usertags = $DB->count_records('tag_instance', array('itemtype' => 'user'));
        $this->assertCount($usertags, $users);
        // Remove three students.
        // Using the proper function to delete the user will also remove the tags.
        $DB->update_record('user', array('id' => $users[4]->id, 'deleted' => 1));
        $DB->update_record('user', array('id' => $users[5]->id, 'deleted' => 1));
        $DB->update_record('user', array('id' => $users[6]->id, 'deleted' => 1));
        // Clean up the tags.
        tag_cleanup();
        $usertags = $DB->count_records('tag_instance', array('itemtype' => 'user'));
        $usercount = $DB->count_records('user', array('deleted' => 0));
        // Remove admin and guest from the count.
        $this->assertEquals($usertags, ($usercount - 2));

        // Test clean up where a course has been removed.
        // Delete the course. This also needs to be this way otherwise the tags are removed by using the proper function.
        $DB->delete_records('course', array('id' => $course->id));
        tag_cleanup();
        $coursetags = $DB->count_records('tag_instance', array('itemtype' => 'course'));
        $this->assertEquals(0, $coursetags);

        // Test clean up where a post has been removed.
        // Create default post.
        $post = new stdClass();
        $post->userid = $users[1]->id;
        $post->content = 'test post content text';
        $post->id = $DB->insert_record('post', $post);
        tag_assign('post', $post->id, $tags[0]->id, 0, 0, 'core', $context->id);
        // Add another one with a fake post id to be removed.
        tag_assign('post', 15, $tags[0]->id, 0, 0, 'core', $context->id);
        // Check that there are two tag instances.
        $posttags = $DB->count_records('tag_instance', array('itemtype' => 'post'));
        $this->assertEquals(2, $posttags);
        // Clean up the tags.
        tag_cleanup();
        // We should only have one entry left now.
        $posttags = $DB->count_records('tag_instance', array('itemtype' => 'post'));
        $this->assertEquals(1, $posttags);
    }

    /**
     * Test deleting a group of tag instances.
     */
    public function test_tag_bulk_delete_instances() {
        global $DB;
        // Setup.
        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $context = context_course::instance($course->id);

        // Create some tag instances.
        for ($i = 0; $i < 10; $i++) {
            $tag = $this->getDataGenerator()->create_tag(array('userid' => $user->id));
            tag_assign('course', $course->id, $tag->id, 0, 0, 'core', $context->id);
        }
        // Get tag instances. tag name and rawname are required for the event fired in this function.
        $sql = "SELECT ti.*, t.name, t.rawname
                  FROM {tag_instance} ti
                  JOIN {tag} t ON t.id = ti.tagid";
        $taginstances = $DB->get_records_sql($sql);
        $this->assertCount(10, $taginstances);
        // Run the function.
        tag_bulk_delete_instances($taginstances);
        // Make sure they are gone.
        $instancecount = $DB->count_records('tag_instance');
        $this->assertEquals(0, $instancecount);
    }
}
