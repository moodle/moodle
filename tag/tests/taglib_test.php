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

namespace core_tag;

use core_tag_area;
use core_tag_collection;
use core_tag_tag;

/**
 * Tag related unit tests.
 *
 * @package core_tag
 * @category test
 * @copyright 2014 Mark Nelson <markn@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class taglib_test extends \advanced_testcase {

    /**
     * Test set up.
     *
     * This is executed before running any test in this file.
     */
    public function setUp(): void {
        $this->resetAfterTest();
    }

    /**
     * Test that the tag_set function throws an exception.
     * This function was deprecated in 3.1
     */
    public function test_tag_set_get() {
        $this->expectException('coding_exception');
        $this->expectExceptionMessage('tag_set() can not be used anymore. Please use ' .
            'core_tag_tag::set_item_tags().');
        tag_set();
    }

    /**
     * Test that tag_set_add function throws an exception.
     * This function was deprecated in 3.1
     */
    public function test_tag_set_add() {
        $this->expectException('coding_exception');
        $this->expectExceptionMessage('tag_set_add() can not be used anymore. Please use ' .
            'core_tag_tag::add_item_tag().');
        tag_set_add();
    }

    /**
     * Test that tag_set_delete function returns an exception.
     * This function was deprecated in 3.1
     */
    public function test_tag_set_delete() {
        $this->expectException('coding_exception');
        $this->expectExceptionMessage('tag_set_delete() can not be used anymore. Please use ' .
            'core_tag_tag::remove_item_tag().');
        tag_set_delete();
    }

    /**
     * Test the core_tag_tag::add_item_tag() and core_tag_tag::remove_item_tag() functions.
     */
    public function test_add_remove_item_tag() {
        global $DB;

        // Create a course to tag.
        $course = $this->getDataGenerator()->create_course();

        // Create the tag and tag instance we are going to delete.
        core_tag_tag::add_item_tag('core', 'course', $course->id, \context_course::instance($course->id), 'A random tag');

        $this->assertEquals(1, $DB->count_records('tag'));
        $this->assertEquals(1, $DB->count_records('tag_instance'));

        // Call the tag_set_delete function.
        core_tag_tag::remove_item_tag('core', 'course', $course->id, 'A random tag');

        // Now check that there are no tags or tag instances.
        $this->assertEquals(0, $DB->count_records('tag'));
        $this->assertEquals(0, $DB->count_records('tag_instance'));
    }

    /**
     * Test add_item_tag function correctly calculates the ordering for a new tag.
     */
    public function test_add_tag_ordering_calculation() {
        global $DB;

        $user1 = $this->getDataGenerator()->create_user();
        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $book1 = $this->getDataGenerator()->create_module('book', array('course' => $course1->id));
        $now = time();
        $chapter1id = $DB->insert_record('book_chapters', (object) [
            'bookid' => $book1->id,
            'hidden' => 0,
            'timecreated' => $now,
            'timemodified' => $now,
            'importsrc' => '',
            'content' => '',
            'contentformat' => FORMAT_HTML,
        ]);

        // Create a tag (ordering should start at 1).
        $ti1 = core_tag_tag::add_item_tag('core', 'course', $course1->id,
            \context_course::instance($course1->id), 'A random tag for course 1');
        $this->assertEquals(1, $DB->get_field('tag_instance', 'ordering', ['id' => $ti1]));

        // Create another tag with a common component, itemtype and itemid (should increase the ordering by 1).
        $ti2 = core_tag_tag::add_item_tag('core', 'course', $course1->id,
            \context_course::instance($course1->id), 'Another random tag for course 1');
        $this->assertEquals(2, $DB->get_field('tag_instance', 'ordering', ['id' => $ti2]));

        // Create a new tag with the same component and itemtype, but different itemid (should start counting from 1 again).
        $ti3 = core_tag_tag::add_item_tag('core', 'course', $course2->id,
            \context_course::instance($course2->id), 'A random tag for course 2');
        $this->assertEquals(1, $DB->get_field('tag_instance', 'ordering', ['id' => $ti3]));

        // Create a new tag with a different itemtype (should start counting from 1 again).
        $ti4 = core_tag_tag::add_item_tag('core', 'user', $user1->id,
            \context_user::instance($user1->id), 'A random tag for user 1');
        $this->assertEquals(1, $DB->get_field('tag_instance', 'ordering', ['id' => $ti4]));

        // Create a new tag with a different component (should start counting from 1 again).
        $ti5 = core_tag_tag::add_item_tag('mod_book', 'book_chapters', $chapter1id,
            \context_module::instance($book1->cmid), 'A random tag for a book chapter');
        $this->assertEquals(1, $DB->get_field('tag_instance', 'ordering', ['id' => $ti5]));
    }

    /**
     * Test that tag_assign function throws an exception.
     * This function was deprecated in 3.1
     */
    public function test_tag_assign() {
        $this->expectException(\coding_exception::class);
        $this->expectExceptionMessage('tag_assign() can not be used anymore. Please use core_tag_tag::set_item_tags() ' .
            'or core_tag_tag::add_item_tag() instead.');
        tag_assign();
    }

    /**
     * Test the tag cleanup function used by the cron.
     */
    public function test_tag_cleanup() {
        global $DB;

        $task = new \core\task\tag_cron_task();

        // Create some users.
        $users = array();
        for ($i = 0; $i < 10; $i++) {
            $users[] = $this->getDataGenerator()->create_user();
        }

        // Create a course to tag.
        $course = $this->getDataGenerator()->create_course();
        $context = \context_course::instance($course->id);

        // Test clean up instances with tags that no longer exist.
        $tags = array();
        $tagnames = array();
        for ($i = 0; $i < 10; $i++) {
            $tags[] = $tag = $this->getDataGenerator()->create_tag(array('userid' => $users[0]->id));
            $tagnames[] = $tag->rawname;
        }
        // Create instances with the tags.
        core_tag_tag::set_item_tags('core', 'course', $course->id, $context, $tagnames);
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
        $task->cleanup();
        // Check that we now only have six tag_instance records left.
        $coursetaginstances = $DB->count_records('tag_instance', array('itemtype' => 'course'));
        $this->assertEquals(6, $coursetaginstances);

        // Test clean up with users that have been deleted.
        // Create a tag for this course.
        foreach ($users as $user) {
            $context = \context_user::instance($user->id);
            core_tag_tag::set_item_tags('core', 'user', $user->id, $context, array($tags[0]->rawname));
        }
        $usertags = $DB->count_records('tag_instance', array('itemtype' => 'user'));
        $this->assertCount($usertags, $users);
        // Remove three students.
        // Using the proper function to delete the user will also remove the tags.
        $DB->update_record('user', array('id' => $users[4]->id, 'deleted' => 1));
        $DB->update_record('user', array('id' => $users[5]->id, 'deleted' => 1));
        $DB->update_record('user', array('id' => $users[6]->id, 'deleted' => 1));

        // Clean up the tags.
        $task->cleanup();
        $usertags = $DB->count_records('tag_instance', array('itemtype' => 'user'));
        $usercount = $DB->count_records('user', array('deleted' => 0));
        // Remove admin and guest from the count.
        $this->assertEquals($usertags, ($usercount - 2));

        // Test clean up where a course has been removed.
        // Delete the course. This also needs to be this way otherwise the tags are removed by using the proper function.
        $DB->delete_records('course', array('id' => $course->id));
        $task->cleanup();
        $coursetags = $DB->count_records('tag_instance', array('itemtype' => 'course'));
        $this->assertEquals(0, $coursetags);

        // Test clean up where a post has been removed.
        // Create default post.
        $post = new \stdClass();
        $post->userid = $users[1]->id;
        $post->content = 'test post content text';
        $post->id = $DB->insert_record('post', $post);
        $context = \context_system::instance();
        core_tag_tag::set_item_tags('core', 'post', $post->id, $context, array($tags[0]->rawname));

        // Add another one with a fake post id to be removed.
        core_tag_tag::set_item_tags('core', 'post', 15, $context, array($tags[0]->rawname));
        // Check that there are two tag instances.
        $posttags = $DB->count_records('tag_instance', array('itemtype' => 'post'));
        $this->assertEquals(2, $posttags);
        // Clean up the tags.
        $task->cleanup();
        // We should only have one entry left now.
        $posttags = $DB->count_records('tag_instance', array('itemtype' => 'post'));
        $this->assertEquals(1, $posttags);
    }

    /**
     * Test deleting a group of tag instances.
     */
    public function test_tag_bulk_delete_instances() {
        global $DB;
        $task = new \core\task\tag_cron_task();

        // Setup.
        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $context = \context_course::instance($course->id);

        // Create some tag instances.
        for ($i = 0; $i < 10; $i++) {
            $tag = $this->getDataGenerator()->create_tag(array('userid' => $user->id));
            core_tag_tag::add_item_tag('core', 'course', $course->id, $context, $tag->rawname);
        }
        // Get tag instances. tag name and rawname are required for the event fired in this function.
        $sql = "SELECT ti.*, t.name, t.rawname
                  FROM {tag_instance} ti
                  JOIN {tag} t ON t.id = ti.tagid";
        $taginstances = $DB->get_records_sql($sql);
        $this->assertCount(10, $taginstances);
        // Run the function.
        $task->bulk_delete_instances($taginstances);
        // Make sure they are gone.
        $instancecount = $DB->count_records('tag_instance');
        $this->assertEquals(0, $instancecount);
    }

    /**
     * Test that setting a list of tags for "tag" item type throws exception if userid specified
     */
    public function test_set_item_tags_with_invalid_userid(): void {
        $user = $this->getDataGenerator()->create_user();

        $this->expectException(\coding_exception::class);
        $this->expectExceptionMessage('Related tags can not have tag instance userid');
        core_tag_tag::set_item_tags('core', 'tag', 1, \context_system::instance(), ['all', 'night', 'long'], $user->id);
    }

    /**
     * Prepares environment for testing tag correlations
     * @return core_tag_tag[] list of used tags
     */
    protected function prepare_correlated() {
        global $DB;

        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();
        $user4 = $this->getDataGenerator()->create_user();
        $user5 = $this->getDataGenerator()->create_user();
        $user6 = $this->getDataGenerator()->create_user();

        // Several records have both 'cat' and 'cats' tags attached to them.
        // This will make those tags automatically correlated.
        // Same with 'dog', 'dogs' and 'puppy.
        core_tag_tag::set_item_tags('core', 'user', $user1->id, \context_user::instance($user1->id), array('cat', 'cats'));
        core_tag_tag::set_item_tags('core', 'user', $user2->id, \context_user::instance($user2->id), array('cat', 'cats', 'kitten'));
        core_tag_tag::set_item_tags('core', 'user', $user3->id, \context_user::instance($user3->id), array('cat', 'cats'));
        core_tag_tag::set_item_tags('core', 'user', $user4->id, \context_user::instance($user4->id), array('dog', 'dogs', 'puppy'));
        core_tag_tag::set_item_tags('core', 'user', $user5->id, \context_user::instance($user5->id), array('dog', 'dogs', 'puppy'));
        core_tag_tag::set_item_tags('core', 'user', $user6->id, \context_user::instance($user6->id), array('dog', 'dogs', 'puppy'));
        $tags = core_tag_tag::get_by_name_bulk(core_tag_collection::get_default(),
            array('cat', 'cats', 'dog', 'dogs', 'kitten', 'puppy'), '*');

        // Add manual relation between tags 'cat' and 'kitten'.
        core_tag_tag::get($tags['cat']->id)->set_related_tags(array('kitten'));

        return $tags;
    }

    /**
     * Test for function compute_correlations() that is part of tag cron
     */
    public function test_correlations() {
        global $DB;
        $task = new \core\task\tag_cron_task();

        $tags = array_map(function ($t) {
            return $t->id;
        }, $this->prepare_correlated());

        $task->compute_correlations();

        $this->assertEquals($tags['cats'],
            $DB->get_field_select('tag_correlation', 'correlatedtags',
                'tagid = ?', array($tags['cat'])));
        $this->assertEquals($tags['cat'],
            $DB->get_field_select('tag_correlation', 'correlatedtags',
                'tagid = ?', array($tags['cats'])));
        $this->assertEquals($tags['dogs'] . ',' . $tags['puppy'],
            $DB->get_field_select('tag_correlation', 'correlatedtags',
                'tagid = ?', array($tags['dog'])));
        $this->assertEquals($tags['dog'] . ',' . $tags['puppy'],
            $DB->get_field_select('tag_correlation', 'correlatedtags',
                'tagid = ?', array($tags['dogs'])));
        $this->assertEquals($tags['dog'] . ',' . $tags['dogs'],
            $DB->get_field_select('tag_correlation', 'correlatedtags',
                'tagid = ?', array($tags['puppy'])));

        // Make sure get_correlated_tags() returns 'cats' as the only correlated tag to the 'cat'.
        $correlatedtags = array_values(core_tag_tag::get($tags['cat'])->get_correlated_tags(true));
        $this->assertCount(3, $correlatedtags); // This will return all existing instances but they all point to the same tag.
        $this->assertEquals('cats', $correlatedtags[0]->rawname);
        $this->assertEquals('cats', $correlatedtags[1]->rawname);
        $this->assertEquals('cats', $correlatedtags[2]->rawname);

        $correlatedtags = array_values(core_tag_tag::get($tags['cat'])->get_correlated_tags());
        $this->assertCount(1, $correlatedtags); // Duplicates are filtered out here.
        $this->assertEquals('cats', $correlatedtags[0]->rawname);

        // Make sure get_correlated_tags() returns 'dogs' and 'puppy' as the correlated tags to the 'dog'.
        $correlatedtags = core_tag_tag::get($tags['dog'])->get_correlated_tags(true);
        $this->assertCount(6, $correlatedtags); // 2 tags times 3 instances.

        $correlatedtags = array_values(core_tag_tag::get($tags['dog'])->get_correlated_tags());
        $this->assertCount(2, $correlatedtags);
        $this->assertEquals('dogs', $correlatedtags[0]->rawname);
        $this->assertEquals('puppy', $correlatedtags[1]->rawname);

        // Function get_related_tags() will return both related and correlated tags.
        $relatedtags = array_values(core_tag_tag::get($tags['cat'])->get_related_tags());
        $this->assertCount(2, $relatedtags);
        $this->assertEquals('kitten', $relatedtags[0]->rawname);
        $this->assertEquals('cats', $relatedtags[1]->rawname);

        // Also test get_correlated_tags().
        $correlatedtags = array_values(core_tag_tag::get($tags['cat'])->get_correlated_tags(true));
        $this->assertCount(3, $correlatedtags); // This will return all existing instances but they all point to the same tag.
        $this->assertEquals('cats', $correlatedtags[0]->rawname);
        $this->assertEquals('cats', $correlatedtags[1]->rawname);
        $this->assertEquals('cats', $correlatedtags[2]->rawname);

        $correlatedtags = array_values(core_tag_tag::get($tags['cat'])->get_correlated_tags());
        $this->assertCount(1, $correlatedtags); // Duplicates are filtered out here.
        $this->assertEquals('cats', $correlatedtags[0]->rawname);

        $correlatedtags = array_values(core_tag_tag::get($tags['dog'])->get_correlated_tags(true));
        $this->assertCount(6, $correlatedtags); // 2 tags times 3 instances.

        $correlatedtags = array_values(core_tag_tag::get($tags['dog'])->get_correlated_tags());
        $this->assertCount(2, $correlatedtags);
        $this->assertEquals('dogs', $correlatedtags[0]->rawname);
        $this->assertEquals('puppy', $correlatedtags[1]->rawname);

        $relatedtags = array_values(core_tag_tag::get($tags['cat'])->get_related_tags());
        $this->assertCount(2, $relatedtags);
        $this->assertEquals('kitten', $relatedtags[0]->rawname);
        $this->assertEquals('cats', $relatedtags[1]->rawname);
        // End of testing deprecated methods.

        // If we then manually set 'cat' and 'cats' as related, get_related_tags() will filter out duplicates.
        core_tag_tag::get($tags['cat'])->set_related_tags(array('kitten', 'cats'));

        $relatedtags = array_values(core_tag_tag::get($tags['cat'])->get_related_tags());
        $this->assertCount(2, $relatedtags);
        $this->assertEquals('kitten', $relatedtags[0]->rawname);
        $this->assertEquals('cats', $relatedtags[1]->rawname);

        // Make sure core_tag_tag::get_item_tags(), core_tag_tag::get_correlated_tags() return the same set of fields.
        $relatedtags = core_tag_tag::get_item_tags('core', 'tag', $tags['cat']);
        $relatedtag = reset($relatedtags);
        $correlatedtags = core_tag_tag::get($tags['cat'])->get_correlated_tags();
        $correlatedtag = reset($correlatedtags);
        $this->assertEquals(array_keys((array)$relatedtag->to_object()), array_keys((array)$correlatedtag->to_object()));

        $relatedtags = core_tag_tag::get_item_tags(null, 'tag', $tags['cat']);
        $relatedtag = reset($relatedtags);
        $correlatedtags = core_tag_tag::get($tags['cat'])->get_correlated_tags();
        $correlatedtag = reset($correlatedtags);
        $this->assertEquals(array_keys((array)$relatedtag), array_keys((array)$correlatedtag));
    }

    /**
     * Test for function cleanup() that is part of tag cron
     */
    public function test_cleanup() {
        global $DB;
        $task = new \core\task\tag_cron_task();

        $user = $this->getDataGenerator()->create_user();
        $defaultcoll = core_tag_collection::get_default();

        // Setting tags will create non-standard tags 'cat', 'dog' and 'fish'.
        core_tag_tag::set_item_tags('core', 'user', $user->id, \context_user::instance($user->id), array('cat', 'dog', 'fish'));

        $this->assertTrue($DB->record_exists('tag', array('name' => 'cat')));
        $this->assertTrue($DB->record_exists('tag', array('name' => 'dog')));
        $this->assertTrue($DB->record_exists('tag', array('name' => 'fish')));

        // Make tag 'dog' standard.
        $dogtag = core_tag_tag::get_by_name($defaultcoll, 'dog', '*');
        $fishtag = core_tag_tag::get_by_name($defaultcoll, 'fish');
        $dogtag->update(array('isstandard' => 1));

        // Manually remove the instances pointing on tags 'dog' and 'fish'.
        $DB->execute('DELETE FROM {tag_instance} WHERE tagid in (?,?)', array($dogtag->id, $fishtag->id));

        $task->cleanup();

        // Tag 'cat' is still present because it's used. Tag 'dog' is present because it's standard.
        // Tag 'fish' was removed because it is not standard and it is no longer used by anybody.
        $this->assertTrue($DB->record_exists('tag', array('name' => 'cat')));
        $this->assertTrue($DB->record_exists('tag', array('name' => 'dog')));
        $this->assertFalse($DB->record_exists('tag', array('name' => 'fish')));

        // Delete user without using API function.
        $DB->update_record('user', array('id' => $user->id, 'deleted' => 1));

        $task->cleanup();

        // Tag 'cat' was now deleted too.
        $this->assertFalse($DB->record_exists('tag', array('name' => 'cat')));

        // Assign tag to non-existing record. Make sure tag was created in the DB.
        core_tag_tag::set_item_tags('core', 'course', 1231231, \context_system::instance(), array('bird'));
        $this->assertTrue($DB->record_exists('tag', array('name' => 'bird')));

        $task->cleanup();

        // Tag 'bird' was now deleted because the related record does not exist in the DB.
        $this->assertFalse($DB->record_exists('tag', array('name' => 'bird')));

        // Now we have a tag instance pointing on 'sometag' tag.
        $user = $this->getDataGenerator()->create_user();
        core_tag_tag::set_item_tags('core', 'user', $user->id, \context_user::instance($user->id), array('sometag'));
        $sometag = core_tag_tag::get_by_name($defaultcoll, 'sometag');

        $this->assertTrue($DB->record_exists('tag_instance', array('tagid' => $sometag->id)));

        // Some hacker removes the tag without using API.
        $DB->delete_records('tag', array('id' => $sometag->id));

        $task->cleanup();

        // The tag instances were also removed.
        $this->assertFalse($DB->record_exists('tag_instance', array('tagid' => $sometag->id)));
    }

    public function test_guess_tag() {
        global $DB;
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);
        $tag1 = $this->getDataGenerator()->create_tag(array('name' => 'Cat'));
        $tc = core_tag_collection::create((object)array('name' => 'tagcoll'));
        $tag2 = $this->getDataGenerator()->create_tag(array('name' => 'Cat', 'tagcollid' => $tc->id));
        $this->assertEquals(2, count($DB->get_records('tag')));
        $this->assertEquals(2, count(core_tag_tag::guess_by_name('Cat')));
        $this->assertEquals(core_tag_collection::get_default(), core_tag_tag::get_by_name(0, 'Cat')->tagcollid);
    }

    public function test_instances() {
        global $DB;
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        // Create a course to tag.
        $course = $this->getDataGenerator()->create_course();
        $context = \context_course::instance($course->id);

        $initialtagscount = $DB->count_records('tag');

        core_tag_tag::set_item_tags('core', 'course', $course->id, $context, array('Tag 1', 'Tag 2'));
        $tags = core_tag_tag::get_item_tags('core', 'course', $course->id);
        $tagssimple = array_values($tags);
        $this->assertEquals(2, count($tags));
        $this->assertEquals('Tag 1', $tagssimple[0]->rawname);
        $this->assertEquals('Tag 2', $tagssimple[1]->rawname);
        $this->assertEquals($initialtagscount + 2, $DB->count_records('tag'));

        core_tag_tag::set_item_tags('core', 'course', $course->id, $context, array('Tag 3', 'Tag 2', 'Tag 1'));
        $tags = core_tag_tag::get_item_tags('core', 'course', $course->id);
        $tagssimple = array_values($tags);
        $this->assertEquals(3, count($tags));
        $this->assertEquals('Tag 3', $tagssimple[0]->rawname);
        $this->assertEquals('Tag 2', $tagssimple[1]->rawname);
        $this->assertEquals('Tag 1', $tagssimple[2]->rawname);
        $this->assertEquals($initialtagscount + 3, $DB->count_records('tag'));

        core_tag_tag::set_item_tags('core', 'course', $course->id, $context, array('Tag 3'));
        $tags = core_tag_tag::get_item_tags('core', 'course', $course->id);
        $tagssimple = array_values($tags);
        $this->assertEquals(1, count($tags));
        $this->assertEquals('Tag 3', $tagssimple[0]->rawname);

        // Make sure the unused tags were removed from tag table.
        $this->assertEquals($initialtagscount + 1, $DB->count_records('tag'));
    }

    public function test_related_tags() {
        global $DB;
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);
        $tagcollid = core_tag_collection::get_default();
        $tag = $this->getDataGenerator()->create_tag(array('$tagcollid' => $tagcollid, 'rawname' => 'My tag'));
        $tag = core_tag_tag::get($tag->id, '*');

        $tag->set_related_tags(array('Synonym 1', 'Synonym 2'));
        $relatedtags = array_values(core_tag_tag::get_item_tags('core', 'tag', $tag->id));
        $this->assertEquals(2, count($relatedtags));
        $this->assertEquals('Synonym 1', $relatedtags[0]->rawname);
        $this->assertEquals('Synonym 2', $relatedtags[1]->rawname);

        $t1 = core_tag_tag::get_by_name($tagcollid, 'Synonym 1', '*');
        $relatedtags = array_values(core_tag_tag::get_item_tags('core', 'tag', $t1->id));
        $this->assertEquals(1, count($relatedtags));
        $this->assertEquals('My tag', $relatedtags[0]->rawname);

        $t2 = core_tag_tag::get_by_name($tagcollid, 'Synonym 2', '*');
        $relatedtags = array_values(core_tag_tag::get_item_tags('core', 'tag', $t2->id));
        $this->assertEquals(1, count($relatedtags));
        $this->assertEquals('My tag', $relatedtags[0]->rawname);

        $tag->set_related_tags(array('Synonym 3', 'Synonym 2', 'Synonym 1'));
        $relatedtags = array_values(core_tag_tag::get_item_tags('core', 'tag', $tag->id));
        $this->assertEquals(3, count($relatedtags));
        $this->assertEquals('Synonym 1', $relatedtags[0]->rawname);
        $this->assertEquals('Synonym 2', $relatedtags[1]->rawname);
        $this->assertEquals('Synonym 3', $relatedtags[2]->rawname);

        $t3 = core_tag_tag::get_by_name($tagcollid, 'Synonym 3', '*');
        $relatedtags = array_values(core_tag_tag::get_item_tags('core', 'tag', $t3->id));
        $this->assertEquals(1, count($relatedtags));
        $this->assertEquals('My tag', $relatedtags[0]->rawname);

        $tag->set_related_tags(array('Synonym 3', 'Synonym 2'));
        $relatedtags = array_values(core_tag_tag::get_item_tags('core', 'tag', $tag->id));
        $this->assertEquals(2, count($relatedtags));
        $this->assertEquals('Synonym 2', $relatedtags[0]->rawname);
        $this->assertEquals('Synonym 3', $relatedtags[1]->rawname);

        // Assert "Synonym 1" no longer links but is still present (will be removed by cron).
        $relatedtags = array_values(core_tag_tag::get_item_tags('core', 'tag', $t1->id));
        $this->assertEquals(0, count($relatedtags));
    }

    /**
     * Very basic test for create/move/update/delete actions, without any itemtype movements.
     */
    public function test_tag_coll_basic() {
        global $DB;

        // Make sure there is one and only one tag coll that is marked as default.
        $tagcolls = core_tag_collection::get_collections();
        $this->assertEquals(1, count($DB->get_records('tag_coll', array('isdefault' => 1))));
        $defaulttagcoll = core_tag_collection::get_default();

        // Create a new tag coll to store user tags and something else.
        $data = (object)array('name' => 'new tag coll');
        $tagcollid1 = core_tag_collection::create($data)->id;
        $tagcolls = core_tag_collection::get_collections();
        $this->assertEquals('new tag coll', $tagcolls[$tagcollid1]->name);

        // Create a new tag coll to store post tags.
        $data = (object)array('name' => 'posts');
        $tagcollid2 = core_tag_collection::create($data)->id;
        $tagcolls = core_tag_collection::get_collections();
        $this->assertEquals('posts', $tagcolls[$tagcollid2]->name);
        $this->assertEquals($tagcolls[$tagcollid1]->sortorder + 1,
            $tagcolls[$tagcollid2]->sortorder);

        // Illegal tag colls sortorder changing.
        $this->assertFalse(core_tag_collection::change_sortorder($tagcolls[$defaulttagcoll], 1));
        $this->assertFalse(core_tag_collection::change_sortorder($tagcolls[$defaulttagcoll], -1));
        $this->assertFalse(core_tag_collection::change_sortorder($tagcolls[$tagcollid2], 1));

        // Move the very last tag coll one position up.
        $this->assertTrue(core_tag_collection::change_sortorder($tagcolls[$tagcollid2], -1));
        $tagcolls = core_tag_collection::get_collections();
        $this->assertEquals($tagcolls[$tagcollid2]->sortorder + 1,
            $tagcolls[$tagcollid1]->sortorder);

        // Move the second last tag coll one position down.
        $this->assertTrue(core_tag_collection::change_sortorder($tagcolls[$tagcollid2], 1));
        $tagcolls = core_tag_collection::get_collections();
        $this->assertEquals($tagcolls[$tagcollid1]->sortorder + 1,
            $tagcolls[$tagcollid2]->sortorder);

        // Edit tag coll.
        $this->assertTrue(core_tag_collection::update($tagcolls[$tagcollid2],
            (object)array('name' => 'posts2')));
        $tagcolls = core_tag_collection::get_collections();
        $this->assertEquals('posts2', $tagcolls[$tagcollid2]->name);

        // Delete tag coll.
        $count = $DB->count_records('tag_coll');
        $this->assertFalse(core_tag_collection::delete($tagcolls[$defaulttagcoll]));
        $this->assertTrue(core_tag_collection::delete($tagcolls[$tagcollid1]));
        $this->assertEquals($count - 1, $DB->count_records('tag_coll'));
    }

    /**
     * Prepares environment for test_move_tags_* tests
     */
    protected function prepare_move_tags() {
        global $CFG;
        require_once($CFG->dirroot.'/blog/locallib.php');
        $this->setUser($this->getDataGenerator()->create_user());

        $collid1 = core_tag_collection::get_default();
        $collid2 = core_tag_collection::create(array('name' => 'newcoll'))->id;
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $blogpost = new \blog_entry(null, array('subject' => 'test'), null);
        $states = \blog_entry::get_applicable_publish_states();
        $blogpost->publishstate = reset($states);
        $blogpost->add();

        core_tag_tag::set_item_tags('core', 'user', $user1->id, \context_user::instance($user1->id),
                array('Tag1', 'Tag2'));
        core_tag_tag::set_item_tags('core', 'user', $user2->id, \context_user::instance($user2->id),
                array('Tag2', 'Tag3'));
        $this->getDataGenerator()->create_tag(array('rawname' => 'Tag4',
            'tagcollid' => $collid1, 'isstandard' => 1));
        $this->getDataGenerator()->create_tag(array('rawname' => 'Tag5',
            'tagcollid' => $collid2, 'isstandard' => 1));

        return array($collid1, $collid2, $user1, $user2, $blogpost);
    }

    public function test_move_tags_simple() {
        global $DB;
        list($collid1, $collid2, $user1, $user2, $blogpost) = $this->prepare_move_tags();

        // Move 'user' area from collection 1 to collection 2, make sure tags were moved completely.
        $tagarea = $DB->get_record('tag_area', array('itemtype' => 'user', 'component' => 'core'));
        core_tag_area::update($tagarea, array('tagcollid' => $collid2));

        $tagsaftermove = $DB->get_records('tag');
        foreach ($tagsaftermove as $tag) {
            // Confirm that the time modified has not been unset.
            $this->assertNotEmpty($tag->timemodified);
        }

        $this->assertEquals(array('Tag4'),
                $DB->get_fieldset_select('tag', 'rawname', 'tagcollid = ? ORDER BY name', array($collid1)));
        $this->assertEquals(array('Tag1', 'Tag2', 'Tag3', 'Tag5'),
                $DB->get_fieldset_select('tag', 'rawname', 'tagcollid = ? ORDER BY name', array($collid2)));
        $this->assertEquals(array('Tag1', 'Tag2'), array_values(core_tag_tag::get_item_tags_array('core', 'user', $user1->id)));
        $this->assertEquals(array('Tag2', 'Tag3'), array_values(core_tag_tag::get_item_tags_array('core', 'user', $user2->id)));
    }

    public function test_move_tags_split_tag() {
        global $DB;
        list($collid1, $collid2, $user1, $user2, $blogpost) = $this->prepare_move_tags();

        core_tag_tag::set_item_tags('core', 'post', $blogpost->id, \context_system::instance(),
                array('Tag1', 'Tag3'));

        // Move 'user' area from collection 1 to collection 2, make sure tag Tag2 was moved and tags Tag1 and Tag3 were duplicated.
        $tagareauser = $DB->get_record('tag_area', array('itemtype' => 'user', 'component' => 'core'));
        core_tag_area::update($tagareauser, array('tagcollid' => $collid2));

        $tagsaftermove = $DB->get_records('tag');
        foreach ($tagsaftermove as $tag) {
            // Confirm that the time modified has not been unset.
            $this->assertNotEmpty($tag->timemodified);
        }

        $this->assertEquals(array('Tag1', 'Tag3', 'Tag4'),
                $DB->get_fieldset_select('tag', 'rawname', 'tagcollid = ? ORDER BY name', array($collid1)));
        $this->assertEquals(array('Tag1', 'Tag2', 'Tag3', 'Tag5'),
                $DB->get_fieldset_select('tag', 'rawname', 'tagcollid = ? ORDER BY name', array($collid2)));
        $this->assertEquals(array('Tag1', 'Tag2'), array_values(core_tag_tag::get_item_tags_array('core', 'user', $user1->id)));
        $this->assertEquals(array('Tag2', 'Tag3'), array_values(core_tag_tag::get_item_tags_array('core', 'user', $user2->id)));
        $this->assertEquals(array('Tag1', 'Tag3'), array_values(core_tag_tag::get_item_tags_array('core', 'post', $blogpost->id)));
    }

    public function test_move_tags_merge_tag() {
        global $DB;
        list($collid1, $collid2, $user1, $user2, $blogpost) = $this->prepare_move_tags();

        // Set collection for 'post' tag area to be collection 2 and add some tags there.
        $tagareablog = $DB->get_record('tag_area', array('itemtype' => 'post', 'component' => 'core'));
        core_tag_area::update($tagareablog, array('tagcollid' => $collid2));

        core_tag_tag::set_item_tags('core', 'post', $blogpost->id, \context_system::instance(),
                array('TAG1', 'Tag3'));

        // Move 'user' area from collection 1 to collection 2,
        // make sure tag Tag2 was moved and tags Tag1 and Tag3 were merged into existing.
        $tagareauser = $DB->get_record('tag_area', array('itemtype' => 'user', 'component' => 'core'));
        core_tag_area::update($tagareauser, array('tagcollid' => $collid2));

        $tagsaftermove = $DB->get_records('tag');
        foreach ($tagsaftermove as $tag) {
            // Confirm that the time modified has not been unset.
            $this->assertNotEmpty($tag->timemodified);
        }

        $this->assertEquals(array('Tag4'),
                $DB->get_fieldset_select('tag', 'rawname', 'tagcollid = ? ORDER BY name', array($collid1)));
        $this->assertEquals(array('TAG1', 'Tag2', 'Tag3', 'Tag5'),
                $DB->get_fieldset_select('tag', 'rawname', 'tagcollid = ? ORDER BY name', array($collid2)));
        $this->assertEquals(array('TAG1', 'Tag2'), array_values(core_tag_tag::get_item_tags_array('core', 'user', $user1->id)));
        $this->assertEquals(array('Tag2', 'Tag3'), array_values(core_tag_tag::get_item_tags_array('core', 'user', $user2->id)));
        $this->assertEquals(array('TAG1', 'Tag3'), array_values(core_tag_tag::get_item_tags_array('core', 'post', $blogpost->id)));
    }

    public function test_move_tags_with_related() {
        global $DB;
        list($collid1, $collid2, $user1, $user2, $blogpost) = $this->prepare_move_tags();

        // Set Tag1 to be related to Tag2 and Tag4 (in collection 1).
        core_tag_tag::get_by_name($collid1, 'Tag1')->set_related_tags(array('Tag2', 'Tag4'));

        // Set collection for 'post' tag area to be collection 2 and add some tags there.
        $tagareablog = $DB->get_record('tag_area', array('itemtype' => 'post', 'component' => 'core'));
        core_tag_area::update($tagareablog, array('tagcollid' => $collid2));

        core_tag_tag::set_item_tags('core', 'post', $blogpost->id, \context_system::instance(),
                array('TAG1', 'Tag3'));

        // Move 'user' area from collection 1 to collection 2, make sure tags were moved completely.
        $tagarea = $DB->get_record('tag_area', array('itemtype' => 'user', 'component' => 'core'));
        core_tag_area::update($tagarea, array('tagcollid' => $collid2));

        $tagsaftermove = $DB->get_records('tag');
        foreach ($tagsaftermove as $tag) {
            // Confirm that the time modified has not been unset.
            $this->assertNotEmpty($tag->timemodified);
        }

        $this->assertEquals(array('Tag1', 'Tag2', 'Tag4'),
                $DB->get_fieldset_select('tag', 'rawname', 'tagcollid = ? ORDER BY name', array($collid1)));
        $this->assertEquals(array('TAG1', 'Tag2', 'Tag3', 'Tag4', 'Tag5'),
                $DB->get_fieldset_select('tag', 'rawname', 'tagcollid = ? ORDER BY name', array($collid2)));
        $this->assertEquals(array('TAG1', 'Tag2'), array_values(core_tag_tag::get_item_tags_array('core', 'user', $user1->id)));
        $this->assertEquals(array('Tag2', 'Tag3'), array_values(core_tag_tag::get_item_tags_array('core', 'user', $user2->id)));

        $tag11 = core_tag_tag::get_by_name($collid1, 'Tag1');
        $related11 = core_tag_tag::get($tag11->id)->get_manual_related_tags();
        $related11 = array_map('core_tag_tag::make_display_name', $related11);
        sort($related11); // Order of related tags may be random.
        $this->assertEquals('Tag2, Tag4', join(', ', $related11));

        $tag21 = core_tag_tag::get_by_name($collid2, 'TAG1');
        $related21 = core_tag_tag::get($tag21->id)->get_manual_related_tags();
        $related21 = array_map('core_tag_tag::make_display_name', $related21);
        sort($related21); // Order of related tags may be random.
        $this->assertEquals('Tag2, Tag4', join(', ', $related21));
    }

    public function test_move_tags_corrupted() {
        global $DB;
        list($collid1, $collid2, $user1, $user2, $blogpost) = $this->prepare_move_tags();
        $collid3 = core_tag_collection::create(array('name' => 'weirdcoll'))->id;

        // We already have Tag1 in coll1, now let's create it in coll3.
        $extratag1 = $this->getDataGenerator()->create_tag(array('rawname' => 'Tag1',
            'tagcollid' => $collid3, 'isstandard' => 1));

        // Artificially add 'Tag1' from coll3 to user2.
        $DB->insert_record('tag_instance', array('tagid' => $extratag1->id, 'itemtype' => 'user',
            'component' => 'core', 'itemid' => $user2->id, 'ordering' => 3));

        // Now we have corrupted data: both users are tagged with 'Tag1', however these are two tags in different collections.
        $user1tags = array_values(core_tag_tag::get_item_tags('core', 'user', $user1->id));
        $user2tags = array_values(core_tag_tag::get_item_tags('core', 'user', $user2->id));
        $this->assertEquals('Tag1', $user1tags[0]->rawname);
        $this->assertEquals('Tag1', $user2tags[2]->rawname);
        $this->assertNotEquals($user1tags[0]->tagcollid, $user2tags[2]->tagcollid);

        // Move user interests tag area into coll2.
        $tagarea = $DB->get_record('tag_area', array('itemtype' => 'user', 'component' => 'core'));
        core_tag_area::update($tagarea, array('tagcollid' => $collid2));

        $tagsaftermove = $DB->get_records('tag');
        foreach ($tagsaftermove as $tag) {
            // Confirm that the time modified has not been unset.
            $this->assertNotEmpty($tag->timemodified);
        }

        // Now all tags are correctly moved to the new collection and both tags 'Tag1' were merged.
        $user1tags = array_values(core_tag_tag::get_item_tags('core', 'user', $user1->id));
        $user2tags = array_values(core_tag_tag::get_item_tags('core', 'user', $user2->id));
        $this->assertEquals('Tag1', $user1tags[0]->rawname);
        $this->assertEquals('Tag1', $user2tags[2]->rawname);
        $this->assertEquals($collid2, $user1tags[0]->tagcollid);
        $this->assertEquals($collid2, $user2tags[2]->tagcollid);
    }

    /**
     * Tests that tag_normalize function throws an exception.
     * This function was deprecated in 3.1
     */
    public function test_normalize() {
        $this->expectException(\coding_exception::class);
        $this->expectExceptionMessage('tag_normalize() can not be used anymore. Please use ' .
            'core_tag_tag::normalize().');
        tag_normalize();
    }

    /**
     * Test functions core_tag_tag::create_if_missing() and core_tag_tag::get_by_name_bulk().
     */
    public function test_create_get() {
        $tagset = array('Cat', ' Dog  ', '<Mouse', '<>', 'mouse', 'Dog');

        $collid = core_tag_collection::get_default();
        $tags = core_tag_tag::create_if_missing($collid, $tagset);
        $this->assertEquals(array('cat', 'dog', 'mouse'), array_keys($tags));
        $this->assertEquals('Dog', $tags['dog']->rawname);
        $this->assertEquals('mouse', $tags['mouse']->rawname); // Case of the last tag wins.

        $tags2 = core_tag_tag::create_if_missing($collid, array('CAT', 'Elephant'));
        $this->assertEquals(array('cat', 'elephant'), array_keys($tags2));
        $this->assertEquals('Cat', $tags2['cat']->rawname);
        $this->assertEquals('Elephant', $tags2['elephant']->rawname);
        $this->assertEquals($tags['cat']->id, $tags2['cat']->id); // Tag 'cat' already existed and was not created again.

        $tags3 = core_tag_tag::get_by_name_bulk($collid, $tagset);
        $this->assertEquals(array('cat', 'dog', 'mouse'), array_keys($tags3));
        $this->assertEquals('Dog', $tags3['dog']->rawname);
        $this->assertEquals('mouse', $tags3['mouse']->rawname);

    }

    /**
     * Testing function core_tag_tag::combine_tags()
     */
    public function test_combine_tags() {
        $initialtags = array(
            array('Cat', 'Dog'),
            array('Dog', 'Cat'),
            array('Cats', 'Hippo'),
            array('Hippo', 'Cats'),
            array('Cat', 'Mouse', 'Kitten'),
            array('Cats', 'Mouse', 'Kitten'),
            array('Kitten', 'Mouse', 'Cat'),
            array('Kitten', 'Mouse', 'Cats'),
            array('Cats', 'Mouse', 'Kitten'),
            array('Mouse', 'Hippo')
        );

        $finaltags = array(
            array('Cat', 'Dog'),
            array('Dog', 'Cat'),
            array('Cat', 'Hippo'),
            array('Hippo', 'Cat'),
            array('Cat', 'Mouse'),
            array('Cat', 'Mouse'),
            array('Mouse', 'Cat'),
            array('Mouse', 'Cat'),
            array('Cat', 'Mouse'),
            array('Mouse', 'Hippo')
        );

        $collid = core_tag_collection::get_default();
        $context = \context_system::instance();
        foreach ($initialtags as $id => $taglist) {
            core_tag_tag::set_item_tags('core', 'course', $id + 10, $context, $initialtags[$id]);
        }

        core_tag_tag::get_by_name($collid, 'Cats', '*')->update(array('isstandard' => 1));

        // Combine tags 'Cats' and 'Kitten' into 'Cat'.
        $cat = core_tag_tag::get_by_name($collid, 'Cat', '*');
        $cats = core_tag_tag::get_by_name($collid, 'Cats', '*');
        $kitten = core_tag_tag::get_by_name($collid, 'Kitten', '*');
        $cat->combine_tags(array($cats, $kitten));

        foreach ($finaltags as $id => $taglist) {
            $this->assertEquals($taglist,
                array_values(core_tag_tag::get_item_tags_array('core', 'course', $id + 10)),
                    'Original array ('.join(', ', $initialtags[$id]).')');
        }

        // Ensure combined tags are deleted and 'Cat' is now official (because 'Cats' was official).
        $this->assertEmpty(core_tag_tag::get_by_name($collid, 'Cats'));
        $this->assertEmpty(core_tag_tag::get_by_name($collid, 'Kitten'));
        $cattag = core_tag_tag::get_by_name($collid, 'Cat', '*');
        $this->assertEquals(1, $cattag->isstandard);
    }

    /**
     * Testing function core_tag_tag::combine_tags() when related tags are present.
     */
    public function test_combine_tags_with_related() {
        $collid = core_tag_collection::get_default();
        $context = \context_system::instance();
        core_tag_tag::set_item_tags('core', 'course', 10, $context, array('Cat', 'Cats', 'Dog'));
        core_tag_tag::get_by_name($collid, 'Cat', '*')->set_related_tags(array('Kitty'));
        core_tag_tag::get_by_name($collid, 'Cats', '*')->set_related_tags(array('Cat', 'Kitten', 'Kitty'));

        // Combine tags 'Cats' into 'Cat'.
        $cat = core_tag_tag::get_by_name($collid, 'Cat', '*');
        $cats = core_tag_tag::get_by_name($collid, 'Cats', '*');
        $cat->combine_tags(array($cats));

        // Ensure 'Cat' is now related to 'Kitten' and 'Kitty' (order of related tags may be random).
        $relatedtags = array_map(function($t) {return $t->rawname;}, $cat->get_manual_related_tags());
        sort($relatedtags);
        $this->assertEquals(array('Kitten', 'Kitty'), array_values($relatedtags));
    }

    /**
     * Testing function core_tag_tag::combine_tags() when correlated tags are present.
     */
    public function test_combine_tags_with_correlated() {
        $task = new \core\task\tag_cron_task();

        $tags = $this->prepare_correlated();

        $task->compute_correlations();
        // Now 'cat' is correlated with 'cats'.
        // Also 'dog', 'dogs' and 'puppy' are correlated.
        // There is a manual relation between 'cat' and 'kitten'.
        // See function test_correlations() for assertions.

        // Combine tags 'dog' and 'kitten' into 'cat' and make sure that cat is now correlated with dogs and puppy.
        $tags['cat']->combine_tags(array($tags['dog'], $tags['kitten']));

        $correlatedtags = $this->get_correlated_tags_names($tags['cat']);
        $this->assertEquals(['cats', 'dogs', 'puppy'], $correlatedtags);

        $correlatedtags = $this->get_correlated_tags_names($tags['dogs']);
        $this->assertEquals(['cat', 'puppy'], $correlatedtags);

        $correlatedtags = $this->get_correlated_tags_names($tags['puppy']);
        $this->assertEquals(['cat', 'dogs'], $correlatedtags);

        // Add tag that does not have any correlations.
        $user7 = $this->getDataGenerator()->create_user();
        core_tag_tag::set_item_tags('core', 'user', $user7->id, \context_user::instance($user7->id), array('hippo'));
        $tags['hippo'] = core_tag_tag::get_by_name(core_tag_collection::get_default(), 'hippo', '*');

        // Combine tag 'cat' into 'hippo'. Now 'hippo' should have the same correlations 'cat' used to have and also
        // tags 'dogs' and 'puppy' should have 'hippo' in correlations.
        $tags['hippo']->combine_tags(array($tags['cat']));

        $correlatedtags = $this->get_correlated_tags_names($tags['hippo']);
        $this->assertEquals(['cats', 'dogs', 'puppy'], $correlatedtags);

        $correlatedtags = $this->get_correlated_tags_names($tags['dogs']);
        $this->assertEquals(['hippo', 'puppy'], $correlatedtags);

        $correlatedtags = $this->get_correlated_tags_names($tags['puppy']);
        $this->assertEquals(['dogs', 'hippo'], $correlatedtags);
    }

    /**
     * get_tags_by_area_in_contexts should return an empty array if there
     * are no tag instances for the area in the given context.
     */
    public function test_get_tags_by_area_in_contexts_empty() {
        $tagnames = ['foo'];
        $collid = core_tag_collection::get_default();
        $tags = core_tag_tag::create_if_missing($collid, $tagnames);
        $user = $this->getDataGenerator()->create_user();
        $context = \context_user::instance($user->id);
        $component = 'core';
        $itemtype = 'user';

        $result = core_tag_tag::get_tags_by_area_in_contexts($component, $itemtype, [$context]);
        $this->assertEmpty($result);
    }

    /**
     * get_tags_by_area_in_contexts should return an array of tags that
     * have instances in the given context even when there is only a single
     * instance.
     */
    public function test_get_tags_by_area_in_contexts_single_tag_one_context() {
        $tagnames = ['foo'];
        $collid = core_tag_collection::get_default();
        $tags = core_tag_tag::create_if_missing($collid, $tagnames);
        $user = $this->getDataGenerator()->create_user();
        $context = \context_user::instance($user->id);
        $component = 'core';
        $itemtype = 'user';
        core_tag_tag::set_item_tags($component, $itemtype, $user->id, $context, $tagnames);

        $result = core_tag_tag::get_tags_by_area_in_contexts($component, $itemtype, [$context]);
        $expected = array_map(function($t) {
            return $t->id;
        }, $tags);
        $actual = array_map(function($t) {
            return $t->id;
        }, $result);

        sort($expected);
        sort($actual);

        $this->assertEquals($expected, $actual);
    }

    /**
     * get_tags_by_area_in_contexts should return all tags in an array
     * that have tag instances in for the area in the given context and
     * should ignore all tags that don't have an instance.
     */
    public function test_get_tags_by_area_in_contexts_multiple_tags_one_context() {
        $tagnames = ['foo', 'bar', 'baz'];
        $collid = core_tag_collection::get_default();
        $tags = core_tag_tag::create_if_missing($collid, $tagnames);
        $user = $this->getDataGenerator()->create_user();
        $context = \context_user::instance($user->id);
        $component = 'core';
        $itemtype = 'user';
        core_tag_tag::set_item_tags($component, $itemtype, $user->id, $context, array_slice($tagnames, 0, 2));

        $result = core_tag_tag::get_tags_by_area_in_contexts($component, $itemtype, [$context]);
        $expected = ['foo', 'bar'];
        $actual = array_map(function($t) {
            return $t->name;
        }, $result);

        sort($expected);
        sort($actual);

        $this->assertEquals($expected, $actual);
    }

    /**
     * get_tags_by_area_in_contexts should return the unique set of
     * tags for a area in the given contexts. Multiple tag instances of
     * the same tag don't result in duplicates in the result set.
     *
     * Tags with tag instances in the same area with in difference contexts
     * should be ignored.
     */
    public function test_get_tags_by_area_in_contexts_multiple_tags_multiple_contexts() {
        $tagnames = ['foo', 'bar', 'baz', 'bop', 'bam', 'bip'];
        $collid = core_tag_collection::get_default();
        $tags = core_tag_tag::create_if_missing($collid, $tagnames);
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();
        $context1 = \context_user::instance($user1->id);
        $context2 = \context_user::instance($user2->id);
        $context3 = \context_user::instance($user3->id);
        $component = 'core';
        $itemtype = 'user';

        // User 1 tags: 'foo', 'bar'.
        core_tag_tag::set_item_tags($component, $itemtype, $user1->id, $context1, array_slice($tagnames, 0, 2));
        // User 2 tags: 'bar', 'baz'.
        core_tag_tag::set_item_tags($component, $itemtype, $user2->id, $context2, array_slice($tagnames, 1, 2));
        // User 3 tags: 'bop', 'bam'.
        core_tag_tag::set_item_tags($component, $itemtype, $user3->id, $context3, array_slice($tagnames, 3, 2));

        $result = core_tag_tag::get_tags_by_area_in_contexts($component, $itemtype, [$context1, $context2]);
        // Both User 1 and 2 have tagged using 'bar' but we don't
        // expect duplicate tags in the result since they are the same
        // tag.
        //
        // User 3 has tagged 'bop' and 'bam' but we aren't searching in
        // that context so they shouldn't be in the results.
        $expected = ['foo', 'bar', 'baz'];
        $actual = array_map(function($t) {
            return $t->name;
        }, $result);

        sort($expected);
        sort($actual);

        $this->assertEquals($expected, $actual);
    }

    /**
     * get_items_tags should return an empty array if the tag area is disabled.
     */
    public function test_get_items_tags_disabled_component() {
        global $CFG;

        $user1 = $this->getDataGenerator()->create_user();
        $context1 = \context_user::instance($user1->id);
        $component = 'core';
        $itemtype = 'user';
        $itemids = [$user1->id];

        // User 1 tags: 'foo', 'bar'.
        core_tag_tag::set_item_tags($component, $itemtype, $user1->id, $context1, ['foo']);
        // This mimics disabling tags for a component.
        $CFG->usetags = false;
        $result = core_tag_tag::get_items_tags($component, $itemtype, $itemids);
        $this->assertEmpty($result);
    }

    /**
     * get_items_tags should return an empty array if the tag item ids list
     * is empty.
     */
    public function test_get_items_tags_empty_itemids() {
        $user1 = $this->getDataGenerator()->create_user();
        $context1 = \context_user::instance($user1->id);
        $component = 'core';
        $itemtype = 'user';

        // User 1 tags: 'foo', 'bar'.
        core_tag_tag::set_item_tags($component, $itemtype, $user1->id, $context1, ['foo']);
        $result = core_tag_tag::get_items_tags($component, $itemtype, []);
        $this->assertEmpty($result);
    }

    /**
     * get_items_tags should return an array indexed by the item ids with empty
     * arrays as the values when the component or itemtype is unknown.
     */
    public function test_get_items_tags_unknown_component_itemtype() {
        $itemids = [1, 2, 3];
        $result = core_tag_tag::get_items_tags('someunknowncomponent', 'user', $itemids);
        foreach ($itemids as $itemid) {
            // Unknown component should return an array indexed by the item ids
            // with empty arrays as the values.
            $this->assertEmpty($result[$itemid]);
        }

        $result = core_tag_tag::get_items_tags('core', 'someunknownitemtype', $itemids);
        foreach ($itemids as $itemid) {
            // Unknown item type should return an array indexed by the item ids
            // with empty arrays as the values.
            $this->assertEmpty($result[$itemid]);
        }
    }

    /**
     * get_items_tags should return an array indexed by the item ids with empty
     * arrays as the values for any item ids that don't have tag instances.
     *
     * Data setup:
     * Users: 1, 2, 3
     * Tags: user 1 = ['foo', 'bar']
     *       user 2 = ['baz', 'bop']
     *       user 3 = []
     *
     * Expected result:
     * [
     *      1 => [
     *          1 => 'foo',
     *          2 => 'bar'
     *      ],
     *      2 => [
     *          3 => 'baz',
     *          4 => 'bop'
     *      ],
     *      3 => []
     * ]
     */
    public function test_get_items_tags_missing_itemids() {
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();
        $context1 = \context_user::instance($user1->id);
        $context2 = \context_user::instance($user2->id);
        $component = 'core';
        $itemtype = 'user';
        $itemids = [$user1->id, $user2->id, $user3->id];
        $expecteduser1tagnames = ['foo', 'bar'];
        $expecteduser2tagnames = ['baz', 'bop'];
        $expecteduser3tagnames = [];

        // User 1 tags: 'foo', 'bar'.
        core_tag_tag::set_item_tags($component, $itemtype, $user1->id, $context1, $expecteduser1tagnames);
        // User 2 tags: 'bar', 'baz'.
        core_tag_tag::set_item_tags($component, $itemtype, $user2->id, $context2, $expecteduser2tagnames);

        $result = core_tag_tag::get_items_tags($component, $itemtype, $itemids);
        $actualuser1tagnames = array_map(function($taginstance) {
            return $taginstance->name;
        }, $result[$user1->id]);
        $actualuser2tagnames = array_map(function($taginstance) {
            return $taginstance->name;
        }, $result[$user2->id]);
        $actualuser3tagnames = $result[$user3->id];

        sort($expecteduser1tagnames);
        sort($expecteduser2tagnames);
        sort($actualuser1tagnames);
        sort($actualuser2tagnames);

        $this->assertEquals($expecteduser1tagnames, $actualuser1tagnames);
        $this->assertEquals($expecteduser2tagnames, $actualuser2tagnames);
        $this->assertEquals($expecteduser3tagnames, $actualuser3tagnames);
    }

    /**
     * set_item_tags should remove any tags that aren't in the given list and should
     * add any instances that are missing.
     */
    public function test_set_item_tags_no_multiple_context_add_remove_instances() {
        $tagnames = ['foo', 'bar', 'baz', 'bop'];
        $collid = core_tag_collection::get_default();
        $tags = core_tag_tag::create_if_missing($collid, $tagnames);
        $user1 = $this->getDataGenerator()->create_user();
        $context = \context_user::instance($user1->id);
        $component = 'core';
        $itemtype = 'user';
        $itemid = 1;
        $tagareas = core_tag_area::get_areas();
        $tagarea = $tagareas[$itemtype][$component];
        $newtagnames = ['bar', 'baz', 'bop'];

        // Make sure the tag area doesn't allow multiple contexts.
        core_tag_area::update($tagarea, ['multiplecontexts' => false]);

        // Create tag instances in separate contexts.
        $this->add_tag_instance($tags['foo'], $component, $itemtype, $itemid, $context);
        $this->add_tag_instance($tags['bar'], $component, $itemtype, $itemid, $context);

        core_tag_tag::set_item_tags($component, $itemtype, $itemid, $context, $newtagnames);

        $result = core_tag_tag::get_item_tags($component, $itemtype, $itemid);
        $actualtagnames = array_map(function($record) {
            return $record->name;
        }, $result);

        sort($newtagnames);
        sort($actualtagnames);

        // The list of tags should match the $newtagnames which means 'foo'
        // should have been removed while 'baz' and 'bop' were added. 'bar'
        // should remain as it was in the new list of tags.
        $this->assertEquals($newtagnames, $actualtagnames);
    }

    /**
     * set_item_tags should set all of the tag instance context ids to the given
     * context if the tag area for the items doesn't allow multiple contexts for
     * the tag instances.
     */
    public function test_set_item_tags_no_multiple_context_updates_context_of_instances() {
        $tagnames = ['foo', 'bar'];
        $collid = core_tag_collection::get_default();
        $tags = core_tag_tag::create_if_missing($collid, $tagnames);
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $context1 = \context_user::instance($user1->id);
        $context2 = \context_user::instance($user2->id);
        $component = 'core';
        $itemtype = 'user';
        $itemid = 1;
        $tagareas = core_tag_area::get_areas();
        $tagarea = $tagareas[$itemtype][$component];

        // Make sure the tag area doesn't allow multiple contexts.
        core_tag_area::update($tagarea, ['multiplecontexts' => false]);

        // Create tag instances in separate contexts.
        $this->add_tag_instance($tags['foo'], $component, $itemtype, $itemid, $context1);
        $this->add_tag_instance($tags['bar'], $component, $itemtype, $itemid, $context2);

        core_tag_tag::set_item_tags($component, $itemtype, $itemid, $context1, $tagnames);

        $result = core_tag_tag::get_item_tags($component, $itemtype, $itemid);
        $this->assertCount(count($tagnames), $result);

        foreach ($result as $tag) {
            // The core user tag area doesn't allow multiple contexts for tag instances
            // so set_item_tags should have set all of the tag instance context ids
            // to match $context1.
            $this->assertEquals($context1->id, $tag->taginstancecontextid);
        }
    }

    /**
     * set_item_tags should delete all of the tag instances that don't match
     * the new set of tags, regardless of the context that the tag instance
     * is in.
     */
    public function test_set_item_tags_no_multiple_contex_deletes_old_instancest() {
        $tagnames = ['foo', 'bar', 'baz', 'bop'];
        $collid = core_tag_collection::get_default();
        $tags = core_tag_tag::create_if_missing($collid, $tagnames);
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $context1 = \context_user::instance($user1->id);
        $context2 = \context_user::instance($user2->id);
        $component = 'core';
        $itemtype = 'user';
        $itemid = 1;
        $expectedtagnames = ['foo', 'baz'];
        $tagareas = core_tag_area::get_areas();
        $tagarea = $tagareas[$itemtype][$component];

        // Make sure the tag area doesn't allow multiple contexts.
        core_tag_area::update($tagarea, ['multiplecontexts' => false]);

        // Create tag instances in separate contexts.
        $this->add_tag_instance($tags['foo'], $component, $itemtype, $itemid, $context1);
        $this->add_tag_instance($tags['bar'], $component, $itemtype, $itemid, $context1);
        $this->add_tag_instance($tags['baz'], $component, $itemtype, $itemid, $context2);
        $this->add_tag_instance($tags['bop'], $component, $itemtype, $itemid, $context2);

        core_tag_tag::set_item_tags($component, $itemtype, $itemid, $context1, $expectedtagnames);

        $result = core_tag_tag::get_item_tags($component, $itemtype, $itemid);
        $actualtagnames = array_map(function($record) {
            return $record->name;
        }, $result);

        sort($expectedtagnames);
        sort($actualtagnames);

        // The list of tags should match the $expectedtagnames.
        $this->assertEquals($expectedtagnames, $actualtagnames);

        foreach ($result as $tag) {
            // The core user tag area doesn't allow multiple contexts for tag instances
            // so set_item_tags should have set all of the tag instance context ids
            // to match $context1.
            $this->assertEquals($context1->id, $tag->taginstancecontextid);
        }
    }

    /**
     * set_item_tags should not change tag instances in a different context to the one
     * it's opertating on if the tag area allows instances from multiple contexts.
     */
    public function test_set_item_tags_allow_multiple_context_doesnt_update_context() {
        global $DB;
        $tagnames = ['foo', 'bar', 'bop'];
        $collid = core_tag_collection::get_default();
        $tags = core_tag_tag::create_if_missing($collid, $tagnames);
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $context1 = \context_user::instance($user1->id);
        $context2 = \context_user::instance($user2->id);
        $component = 'core';
        $itemtype = 'user';
        $itemid = 1;
        $tagareas = core_tag_area::get_areas();
        $tagarea = $tagareas[$itemtype][$component];

        // Make sure the tag area allows multiple contexts.
        core_tag_area::update($tagarea, ['multiplecontexts' => true]);

        // Create tag instances in separate contexts.
        $this->add_tag_instance($tags['foo'], $component, $itemtype, $itemid, $context1);
        $this->add_tag_instance($tags['bar'], $component, $itemtype, $itemid, $context2);

        // Set the list of tags for $context1. This includes a tag that already exists
        // in that context and a new tag. There is another tag, 'bar', that exists in a
        // different context ($context2) that should be ignored.
        core_tag_tag::set_item_tags($component, $itemtype, $itemid, $context1, ['foo', 'bop']);

        $result = core_tag_tag::get_item_tags($component, $itemtype, $itemid);
        $actualtagnames = array_map(function($record) {
            return $record->name;
        }, $result);

        sort($tagnames);
        sort($actualtagnames);
        // The list of tags should match the $tagnames.
        $this->assertEquals($tagnames, $actualtagnames);

        foreach ($result as $tag) {
            if ($tag->name == 'bar') {
                // The tag instance for 'bar' should have been left untouched
                // because it was in a different context.
                $this->assertEquals($context2->id, $tag->taginstancecontextid);
            } else {
                $this->assertEquals($context1->id, $tag->taginstancecontextid);
            }
        }
    }

    /**
     * set_item_tags should delete all of the tag instances that don't match
     * the new set of tags only in the same context if the tag area allows
     * multiple contexts.
     */
    public function test_set_item_tags_allow_multiple_context_deletes_instances_in_same_context() {
        $tagnames = ['foo', 'bar', 'baz', 'bop'];
        $collid = core_tag_collection::get_default();
        $tags = core_tag_tag::create_if_missing($collid, $tagnames);
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $context1 = \context_user::instance($user1->id);
        $context2 = \context_user::instance($user2->id);
        $component = 'core';
        $itemtype = 'user';
        $itemid = 1;
        $expectedtagnames = ['foo', 'bar', 'bop'];
        $tagareas = core_tag_area::get_areas();
        $tagarea = $tagareas[$itemtype][$component];

        // Make sure the tag area allows multiple contexts.
        core_tag_area::update($tagarea, ['multiplecontexts' => true]);

        // Create tag instances in separate contexts.
        $this->add_tag_instance($tags['foo'], $component, $itemtype, $itemid, $context1);
        $this->add_tag_instance($tags['bar'], $component, $itemtype, $itemid, $context1);
        $this->add_tag_instance($tags['baz'], $component, $itemtype, $itemid, $context1);
        $this->add_tag_instance($tags['bop'], $component, $itemtype, $itemid, $context2);

        core_tag_tag::set_item_tags($component, $itemtype, $itemid, $context1, ['foo', 'bar']);

        $result = core_tag_tag::get_item_tags($component, $itemtype, $itemid);
        $actualtagnames = array_map(function($record) {
            return $record->name;
        }, $result);

        sort($expectedtagnames);
        sort($actualtagnames);

        // The list of tags should match the $expectedtagnames, which includes the
        // tag 'bop' because it was in a different context to the one being set
        // even though it wasn't in the new set of tags.
        $this->assertEquals($expectedtagnames, $actualtagnames);
    }

    /**
     * set_item_tags should allow multiple instances of the same tag in different
     * contexts if the tag area allows multiple contexts.
     */
    public function test_set_item_tags_allow_multiple_context_same_tag_multiple_contexts() {
        $tagnames = ['foo'];
        $collid = core_tag_collection::get_default();
        $tags = core_tag_tag::create_if_missing($collid, $tagnames);
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $context1 = \context_user::instance($user1->id);
        $context2 = \context_user::instance($user2->id);
        $component = 'core';
        $itemtype = 'user';
        $itemid = 1;
        $expectedtagnames = ['foo', 'bar', 'bop'];
        $tagareas = core_tag_area::get_areas();
        $tagarea = $tagareas[$itemtype][$component];

        // Make sure the tag area allows multiple contexts.
        core_tag_area::update($tagarea, ['multiplecontexts' => true]);

        // Create first instance of 'foo' in $context1.
        $this->add_tag_instance($tags['foo'], $component, $itemtype, $itemid, $context1);

        core_tag_tag::set_item_tags($component, $itemtype, $itemid, $context2, ['foo']);

        $result = core_tag_tag::get_item_tags($component, $itemtype, $itemid);
        $tagsbycontext = array_reduce($result, function($carry, $tag) {
            $contextid = $tag->taginstancecontextid;
            if (isset($carry[$contextid])) {
                $carry[$contextid][] = $tag;
            } else {
                $carry[$contextid] = [$tag];
            }
            return $carry;
        }, []);

        // The result should be two tag instances of 'foo' in each of the
        // two contexts, $context1 and $context2.
        $this->assertCount(1, $tagsbycontext[$context1->id]);
        $this->assertCount(1, $tagsbycontext[$context2->id]);
        $this->assertEquals('foo', $tagsbycontext[$context1->id][0]->name);
        $this->assertEquals('foo', $tagsbycontext[$context2->id][0]->name);
    }

    /**
     * delete_instances_as_record with an empty set of instances should do nothing.
     */
    public function test_delete_instances_as_record_empty_set() {
        $user = $this->getDataGenerator()->create_user();
        $context = \context_user::instance($user->id);
        $component = 'core';
        $itemtype = 'user';
        $itemid = 1;

        core_tag_tag::set_item_tags($component, $itemtype, $itemid, $context, ['foo']);
        // This shouldn't error.
        core_tag_tag::delete_instances_as_record([]);

        $tags = core_tag_tag::get_item_tags($component, $itemtype, $itemid);
        // We should still have one tag.
        $this->assertCount(1, $tags);
    }

    /**
     * delete_instances_as_record with an instance that doesn't exist should do
     * nothing.
     */
    public function test_delete_instances_as_record_missing_set() {
        $tagnames = ['foo'];
        $collid = core_tag_collection::get_default();
        $tags = core_tag_tag::create_if_missing($collid, $tagnames);
        $user = $this->getDataGenerator()->create_user();
        $context = \context_user::instance($user->id);
        $component = 'core';
        $itemtype = 'user';
        $itemid = 1;

        $taginstance = $this->add_tag_instance($tags['foo'], $component, $itemtype, $itemid, $context);
        $taginstance->id++;

        // Delete an instance that doesn't exist should do nothing.
        core_tag_tag::delete_instances_as_record([$taginstance]);

        $tags = core_tag_tag::get_item_tags($component, $itemtype, $itemid);
        // We should still have one tag.
        $this->assertCount(1, $tags);
    }

    /**
     * delete_instances_as_record with a list of all tag instances should
     * leave no tags left.
     */
    public function test_delete_instances_as_record_whole_set() {
        $tagnames = ['foo'];
        $collid = core_tag_collection::get_default();
        $tags = core_tag_tag::create_if_missing($collid, $tagnames);
        $user = $this->getDataGenerator()->create_user();
        $context = \context_user::instance($user->id);
        $component = 'core';
        $itemtype = 'user';
        $itemid = 1;

        $taginstance = $this->add_tag_instance($tags['foo'], $component, $itemtype, $itemid, $context);

        core_tag_tag::delete_instances_as_record([$taginstance]);

        $tags = core_tag_tag::get_item_tags($component, $itemtype, $itemid);
        // There should be no tags left.
        $this->assertEmpty($tags);
    }

    /**
     * delete_instances_as_record with a list of only some tag instances should
     * delete only the given tag instances and leave other tag instances.
     */
    public function test_delete_instances_as_record_partial_set() {
        $tagnames = ['foo', 'bar'];
        $collid = core_tag_collection::get_default();
        $tags = core_tag_tag::create_if_missing($collid, $tagnames);
        $user = $this->getDataGenerator()->create_user();
        $context = \context_user::instance($user->id);
        $component = 'core';
        $itemtype = 'user';
        $itemid = 1;

        $taginstance = $this->add_tag_instance($tags['foo'], $component, $itemtype, $itemid, $context);
        $this->add_tag_instance($tags['bar'], $component, $itemtype, $itemid, $context);

        core_tag_tag::delete_instances_as_record([$taginstance]);

        $tags = core_tag_tag::get_item_tags($component, $itemtype, $itemid);
        // We should be left with a single tag, 'bar'.
        $this->assertCount(1, $tags);
        $tag = array_shift($tags);
        $this->assertEquals('bar', $tag->name);
    }

    /**
     * delete_instances_by_id with an empty set of ids should do nothing.
     */
    public function test_delete_instances_by_id_empty_set() {
        $user = $this->getDataGenerator()->create_user();
        $context = \context_user::instance($user->id);
        $component = 'core';
        $itemtype = 'user';
        $itemid = 1;

        core_tag_tag::set_item_tags($component, $itemtype, $itemid, $context, ['foo']);
        // This shouldn't error.
        core_tag_tag::delete_instances_by_id([]);

        $tags = core_tag_tag::get_item_tags($component, $itemtype, $itemid);
        // We should still have one tag.
        $this->assertCount(1, $tags);
    }

    /**
     * delete_instances_by_id with an id that doesn't exist should do
     * nothing.
     */
    public function test_delete_instances_by_id_missing_set() {
        $tagnames = ['foo'];
        $collid = core_tag_collection::get_default();
        $tags = core_tag_tag::create_if_missing($collid, $tagnames);
        $user = $this->getDataGenerator()->create_user();
        $context = \context_user::instance($user->id);
        $component = 'core';
        $itemtype = 'user';
        $itemid = 1;

        $taginstance = $this->add_tag_instance($tags['foo'], $component, $itemtype, $itemid, $context);

        // Delete an instance that doesn't exist should do nothing.
        core_tag_tag::delete_instances_by_id([$taginstance->id + 1]);

        $tags = core_tag_tag::get_item_tags($component, $itemtype, $itemid);
        // We should still have one tag.
        $this->assertCount(1, $tags);
    }

    /**
     * delete_instances_by_id with a list of all tag instance ids should
     * leave no tags left.
     */
    public function test_delete_instances_by_id_whole_set() {
        $tagnames = ['foo'];
        $collid = core_tag_collection::get_default();
        $tags = core_tag_tag::create_if_missing($collid, $tagnames);
        $user = $this->getDataGenerator()->create_user();
        $context = \context_user::instance($user->id);
        $component = 'core';
        $itemtype = 'user';
        $itemid = 1;

        $taginstance = $this->add_tag_instance($tags['foo'], $component, $itemtype, $itemid, $context);

        core_tag_tag::delete_instances_by_id([$taginstance->id]);

        $tags = core_tag_tag::get_item_tags($component, $itemtype, $itemid);
        // There should be no tags left.
        $this->assertEmpty($tags);
    }

    /**
     * delete_instances_by_id with a list of only some tag instance ids should
     * delete only the given tag instance ids and leave other tag instances.
     */
    public function test_delete_instances_by_id_partial_set() {
        $tagnames = ['foo', 'bar'];
        $collid = core_tag_collection::get_default();
        $tags = core_tag_tag::create_if_missing($collid, $tagnames);
        $user = $this->getDataGenerator()->create_user();
        $context = \context_user::instance($user->id);
        $component = 'core';
        $itemtype = 'user';
        $itemid = 1;

        $taginstance = $this->add_tag_instance($tags['foo'], $component, $itemtype, $itemid, $context);
        $this->add_tag_instance($tags['bar'], $component, $itemtype, $itemid, $context);

        core_tag_tag::delete_instances_by_id([$taginstance->id]);

        $tags = core_tag_tag::get_item_tags($component, $itemtype, $itemid);
        // We should be left with a single tag, 'bar'.
        $this->assertCount(1, $tags);
        $tag = array_shift($tags);
        $this->assertEquals('bar', $tag->name);
    }

    /**
     * delete_instances should delete all tag instances for a component if given
     * only the component as a parameter.
     */
    public function test_delete_instances_with_component() {
        global $DB;

        $tagnames = ['foo', 'bar'];
        $collid = core_tag_collection::get_default();
        $tags = core_tag_tag::create_if_missing($collid, $tagnames);
        $user = $this->getDataGenerator()->create_user();
        $context = \context_user::instance($user->id);
        $component = 'core';
        $itemtype1 = 'user';
        $itemtype2 = 'course';
        $itemid = 1;

        // Add 2 tag instances in the same $component but with different item types.
        $this->add_tag_instance($tags['foo'], $component, $itemtype1, $itemid, $context);
        $this->add_tag_instance($tags['bar'], $component, $itemtype2, $itemid, $context);

        // Delete all tag instances for the component.
        core_tag_tag::delete_instances($component);

        $taginstances = $DB->get_records_sql('SELECT * FROM {tag_instance} WHERE component = ?', [$component]);
        // Both tag instances from the $component should have been deleted even though
        // they are in different item types.
        $this->assertEmpty($taginstances);
    }

    /**
     * delete_instances should delete all tag instances for a component if given
     * only the component as a parameter.
     */
    public function test_delete_instances_with_component_and_itemtype() {
        global $DB;

        $tagnames = ['foo', 'bar'];
        $collid = core_tag_collection::get_default();
        $tags = core_tag_tag::create_if_missing($collid, $tagnames);
        $user = $this->getDataGenerator()->create_user();
        $context = \context_user::instance($user->id);
        $component = 'core';
        $itemtype1 = 'user';
        $itemtype2 = 'course';
        $itemid = 1;

        // Add 2 tag instances in the same $component but with different item types.
        $this->add_tag_instance($tags['foo'], $component, $itemtype1, $itemid, $context);
        $this->add_tag_instance($tags['bar'], $component, $itemtype2, $itemid, $context);

        // Delete all tag instances for the component and itemtype.
        core_tag_tag::delete_instances($component, $itemtype1);

        $taginstances = $DB->get_records_sql('SELECT * FROM {tag_instance} WHERE component = ?', [$component]);
        // Only the tag instances for $itemtype1 should have been deleted. We
        // should still be left with the instance for 'bar'.
        $this->assertCount(1, $taginstances);
        $taginstance = array_shift($taginstances);
        $this->assertEquals($itemtype2, $taginstance->itemtype);
        $this->assertEquals($tags['bar']->id, $taginstance->tagid);
    }

    /**
     * delete_instances should delete all tag instances for a component in a context
     * if given both the component and context id as parameters.
     */
    public function test_delete_instances_with_component_and_context() {
        global $DB;

        $tagnames = ['foo', 'bar', 'baz'];
        $collid = core_tag_collection::get_default();
        $tags = core_tag_tag::create_if_missing($collid, $tagnames);
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $context1 = \context_user::instance($user1->id);
        $context2 = \context_user::instance($user2->id);
        $component = 'core';
        $itemtype1 = 'user';
        $itemtype2 = 'course';
        $itemid = 1;

        // Add 3 tag instances in the same $component but with different contexts.
        $this->add_tag_instance($tags['foo'], $component, $itemtype1, $itemid, $context1);
        $this->add_tag_instance($tags['bar'], $component, $itemtype2, $itemid, $context1);
        $this->add_tag_instance($tags['baz'], $component, $itemtype2, $itemid, $context2);

        // Delete all tag instances for the component and context.
        core_tag_tag::delete_instances($component, null, $context1->id);

        $taginstances = $DB->get_records_sql('SELECT * FROM {tag_instance} WHERE component = ?', [$component]);
        // Only the tag instances for $context1 should have been deleted. We
        // should still be left with the instance for 'baz'.
        $this->assertCount(1, $taginstances);
        $taginstance = array_shift($taginstances);
        $this->assertEquals($context2->id, $taginstance->contextid);
        $this->assertEquals($tags['baz']->id, $taginstance->tagid);
    }

    /**
     * delete_instances should delete all tag instances for a component, item type
     * and context if given the component, itemtype, and context id as parameters.
     */
    public function test_delete_instances_with_component_and_itemtype_and_context() {
        global $DB;

        $tagnames = ['foo', 'bar', 'baz'];
        $collid = core_tag_collection::get_default();
        $tags = core_tag_tag::create_if_missing($collid, $tagnames);
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $context1 = \context_user::instance($user1->id);
        $context2 = \context_user::instance($user2->id);
        $component = 'core';
        $itemtype1 = 'user';
        $itemtype2 = 'course';
        $itemid = 1;

        // Add 3 tag instances in the same $component but with different contexts.
        $this->add_tag_instance($tags['foo'], $component, $itemtype1, $itemid, $context1);
        $this->add_tag_instance($tags['bar'], $component, $itemtype2, $itemid, $context1);
        $this->add_tag_instance($tags['baz'], $component, $itemtype2, $itemid, $context2);

        // Delete all tag instances for the component and context.
        core_tag_tag::delete_instances($component, $itemtype2, $context1->id);

        $taginstances = $DB->get_records_sql('SELECT * FROM {tag_instance} WHERE component = ?', [$component]);
        // Only the tag instances for $itemtype2 in $context1 should have been
        // deleted. We should still be left with the instance for 'foo' and 'baz'.
        $this->assertCount(2, $taginstances);
        $fooinstances = array_filter($taginstances, function($instance) use ($tags) {
            return $instance->tagid == $tags['foo']->id;
        });
        $fooinstance = array_shift($fooinstances);
        $bazinstances = array_filter($taginstances, function($instance) use ($tags) {
            return $instance->tagid == $tags['baz']->id;
        });
        $bazinstance = array_shift($bazinstances);
        $this->assertNotEmpty($fooinstance);
        $this->assertNotEmpty($bazinstance);
        $this->assertEquals($context1->id, $fooinstance->contextid);
        $this->assertEquals($context2->id, $bazinstance->contextid);
    }

    /**
     * change_instances_context should not change any existing instance contexts
     * if not given any instance ids.
     */
    public function test_change_instances_context_empty_set() {
        global $DB;

        $tagnames = ['foo'];
        $collid = core_tag_collection::get_default();
        $tags = core_tag_tag::create_if_missing($collid, $tagnames);
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $context1 = \context_user::instance($user1->id);
        $context2 = \context_user::instance($user2->id);
        $component = 'core';
        $itemtype = 'user';
        $itemid = 1;

        $this->add_tag_instance($tags['foo'], $component, $itemtype, $itemid, $context1);

        core_tag_tag::change_instances_context([], $context2);

        $taginstances = $DB->get_records_sql('SELECT * FROM {tag_instance}');
        // The existing tag instance should not have changed.
        $this->assertCount(1, $taginstances);
        $taginstance = array_shift($taginstances);
        $this->assertEquals($context1->id, $taginstance->contextid);
    }

    /**
     * change_instances_context should only change the context of the given ids.
     */
    public function test_change_instances_context_partial_set() {
        global $DB;

        $tagnames = ['foo', 'bar'];
        $collid = core_tag_collection::get_default();
        $tags = core_tag_tag::create_if_missing($collid, $tagnames);
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $context1 = \context_user::instance($user1->id);
        $context2 = \context_user::instance($user2->id);
        $component = 'core';
        $itemtype = 'user';
        $itemid = 1;

        $fooinstance = $this->add_tag_instance($tags['foo'], $component, $itemtype, $itemid, $context1);
        $barinstance = $this->add_tag_instance($tags['bar'], $component, $itemtype, $itemid, $context1);

        core_tag_tag::change_instances_context([$fooinstance->id], $context2);

        // Reload the record.
        $fooinstance = $DB->get_record('tag_instance', ['id' => $fooinstance->id]);
        $barinstance = $DB->get_record('tag_instance', ['id' => $barinstance->id]);
        // Tag 'foo' context should be updated.
        $this->assertEquals($context2->id, $fooinstance->contextid);
        // Tag 'bar' context should not be changed.
        $this->assertEquals($context1->id, $barinstance->contextid);
    }

    /**
     * change_instances_context should change multiple items from multiple contexts.
     */
    public function test_change_instances_context_multiple_contexts() {
        global $DB;

        $tagnames = ['foo', 'bar'];
        $collid = core_tag_collection::get_default();
        $tags = core_tag_tag::create_if_missing($collid, $tagnames);
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();
        $context1 = \context_user::instance($user1->id);
        $context2 = \context_user::instance($user2->id);
        $context3 = \context_user::instance($user3->id);
        $component = 'core';
        $itemtype = 'user';
        $itemid = 1;

        // Two instances in different contexts.
        $fooinstance = $this->add_tag_instance($tags['foo'], $component, $itemtype, $itemid, $context1);
        $barinstance = $this->add_tag_instance($tags['bar'], $component, $itemtype, $itemid, $context2);

        core_tag_tag::change_instances_context([$fooinstance->id, $barinstance->id], $context3);

        // Reload the record.
        $fooinstance = $DB->get_record('tag_instance', ['id' => $fooinstance->id]);
        $barinstance = $DB->get_record('tag_instance', ['id' => $barinstance->id]);
        // Tag 'foo' context should be updated.
        $this->assertEquals($context3->id, $fooinstance->contextid);
        // Tag 'bar' context should be updated.
        $this->assertEquals($context3->id, $barinstance->contextid);
        // There shouldn't be any tag instances left in $context1.
        $context1records = $DB->get_records('tag_instance', ['contextid' => $context1->id]);
        $this->assertEmpty($context1records);
        // There shouldn't be any tag instances left in $context2.
        $context2records = $DB->get_records('tag_instance', ['contextid' => $context2->id]);
        $this->assertEmpty($context2records);
    }

    /**
     * change_instances_context moving an instance from one context into a context
     * that already has an instance of that tag should throw an exception.
     */
    public function test_change_instances_context_conflicting_instances() {
        global $DB;

        $tagnames = ['foo'];
        $collid = core_tag_collection::get_default();
        $tags = core_tag_tag::create_if_missing($collid, $tagnames);
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $context1 = \context_user::instance($user1->id);
        $context2 = \context_user::instance($user2->id);
        $component = 'core';
        $itemtype = 'user';
        $itemid = 1;

        // Two instances of 'foo' in different contexts.
        $fooinstance1 = $this->add_tag_instance($tags['foo'], $component, $itemtype, $itemid, $context1);
        $fooinstance2 = $this->add_tag_instance($tags['foo'], $component, $itemtype, $itemid, $context2);

        // There is already an instance of 'foo' in $context2 so the code
        // should throw an exception when we try to move another instance there.
        $this->expectException('Exception');
        core_tag_tag::change_instances_context([$fooinstance1->id], $context2);
    }

    /**
     * Help method to return sorted array of names of correlated tags to use for assertions
     * @param core_tag $tag
     * @return string
     */
    protected function get_correlated_tags_names($tag) {
        $rv = array_map(function($t) {
            return $t->rawname;
        }, $tag->get_correlated_tags());
        sort($rv);
        return array_values($rv);
    }

    /**
     * Add a tag instance.
     *
     * @param core_tag_tag $tag
     * @param string $component
     * @param string $itemtype
     * @param int $itemid
     * @param context $context
     * @return stdClass
     */
    protected function add_tag_instance(core_tag_tag $tag, $component, $itemtype, $itemid, $context) {
        global $DB;
        $record = (array) $tag->to_object();
        $record['tagid'] = $record['id'];
        $record['component'] = $component;
        $record['itemtype'] = $itemtype;
        $record['itemid'] = $itemid;
        $record['contextid'] = $context->id;
        $record['tiuserid'] = 0;
        $record['ordering'] = 0;
        $record['timecreated'] = time();
        $record['id'] = $DB->insert_record('tag_instance', $record);
        return (object) $record;
    }
}
