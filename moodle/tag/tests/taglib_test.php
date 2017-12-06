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
     * This function was deprecated in 3.1
     */
    public function test_tag_set_get() {
        global $DB;

        // Create a course to tag.
        $course = $this->getDataGenerator()->create_course();

        // Create the tag and tag instance.
        tag_set('course', $course->id, array('A random tag'), 'core', context_course::instance($course->id)->id);
        $this->assertDebuggingCalled();

        // Get the tag instance that should have been created.
        $taginstance = $DB->get_record('tag_instance', array('itemtype' => 'course', 'itemid' => $course->id), '*', MUST_EXIST);
        $this->assertEquals('core', $taginstance->component);
        $this->assertEquals(context_course::instance($course->id)->id, $taginstance->contextid);

        $tagbyname = tag_get('name', 'A random tag');
        $this->assertDebuggingCalled();
        $this->assertEquals('A random tag', $tagbyname->rawname);

        $this->assertEmpty(tag_get('name', 'Non existing tag'));
        $this->assertDebuggingCalled();

        $tagbyid = tag_get('id', $tagbyname->id);
        $this->assertDebuggingCalled();
        $this->assertEquals('A random tag', $tagbyid->rawname);
        $tagid = $tagbyname->id;

        $this->assertEmpty(tag_get('id', $tagid + 1));
        $this->assertDebuggingCalled();

        tag_set('tag', $tagid, array('Some related tag'));
        $this->assertDebuggingCalled();
        $relatedtags = tag_get_related_tags($tagid);
        $this->assertDebuggingCalled();
        $this->assertCount(1, $relatedtags);
        $this->assertEquals('Some related tag', $relatedtags[0]->rawname);

        $tagids = tag_get_id(array('A random tag', 'Some related tag'));
        $this->assertDebuggingCalled();
        $this->assertCount(2, $tagids);
        $this->assertEquals($tagid, $tagids['a random tag']);
        $this->assertEquals($relatedtags[0]->id, $tagids['some related tag']);
    }

    /**
     * Test the tag_set_add function.
     * This function was deprecated in 3.1
     */
    public function test_tag_set_add() {
        global $DB;

        // Create a course to tag.
        $course = $this->getDataGenerator()->create_course();

        // Create the tag and tag instance.
        tag_set_add('course', $course->id, 'A random tag', 'core', context_course::instance($course->id)->id);
        $this->assertDebuggingCalled();

        // Get the tag instance that should have been created.
        $taginstance = $DB->get_record('tag_instance', array('itemtype' => 'course', 'itemid' => $course->id), '*', MUST_EXIST);
        $this->assertEquals('core', $taginstance->component);
        $this->assertEquals(context_course::instance($course->id)->id, $taginstance->contextid);
        $tagid = $taginstance->tagid;

        tag_set_add('tag', $tagid, 'Some related tag');
        $this->assertDebuggingCalled();
        $relatedtags = tag_get_related_tags($tagid);
        $this->assertDebuggingCalled();
        $this->assertCount(1, $relatedtags);
        $this->assertEquals('Some related tag', $relatedtags[0]->rawname);
    }

    /**
     * Test the tag_set_delete function.
     * This function was deprecated in 3.1
     */
    public function test_tag_set_delete() {
        global $DB;

        // Create a course to tag.
        $course = $this->getDataGenerator()->create_course();

        // Create the tag and tag instance we are going to delete.
        tag_set_add('course', $course->id, 'A random tag', 'core', context_course::instance($course->id)->id);
        $this->assertDebuggingCalled();

        // Call the tag_set_delete function.
        tag_set_delete('course', $course->id, 'a random tag', 'core', context_course::instance($course->id)->id);
        $this->assertDebuggingCalled();

        // Now check that there are no tags or tag instances.
        $this->assertEquals(0, $DB->count_records('tag'));
        $this->assertEquals(0, $DB->count_records('tag_instance'));

        // Add tag again, add and remove related tag.
        tag_set_add('course', $course->id, 'A random tag', 'core', context_course::instance($course->id)->id);
        $this->assertDebuggingCalled();
        $taginstance = $DB->get_record('tag_instance', array('itemtype' => 'course', 'itemid' => $course->id), '*', MUST_EXIST);
        $tagid = $taginstance->tagid;
        tag_set_add('tag', $tagid, 'Some related tag');
        $this->assertDebuggingCalled();
        tag_set_delete('tag', $tagid, 'Some related tag');
        $this->assertDebuggingCalled();
        $relatedtags = tag_get_related_tags($tagid);
        $this->assertDebuggingCalled();
        $this->assertCount(0, $relatedtags);
    }

    /**
     * Test the core_tag_tag::add_item_tag() and core_tag_tag::remove_item_tag() functions.
     */
    public function test_add_remove_item_tag() {
        global $DB;

        // Create a course to tag.
        $course = $this->getDataGenerator()->create_course();

        // Create the tag and tag instance we are going to delete.
        core_tag_tag::add_item_tag('core', 'course', $course->id, context_course::instance($course->id), 'A random tag');

        $this->assertEquals(1, $DB->count_records('tag'));
        $this->assertEquals(1, $DB->count_records('tag_instance'));

        // Call the tag_set_delete function.
        core_tag_tag::remove_item_tag('core', 'course', $course->id, 'A random tag');

        // Now check that there are no tags or tag instances.
        $this->assertEquals(0, $DB->count_records('tag'));
        $this->assertEquals(0, $DB->count_records('tag_instance'));
    }

    /**
     * Test the tag_assign function.
     * This function was deprecated in 3.1
     */
    public function test_tag_assign() {
        global $DB;

        // Create a course to tag.
        $course = $this->getDataGenerator()->create_course();

        // Create the tag.
        $tag = $this->getDataGenerator()->create_tag();
        $tag2 = $this->getDataGenerator()->create_tag();

        // Tag the course with the tag we created.
        tag_assign('course', $course->id, $tag->id, 0, 0, 'core', context_course::instance($course->id)->id);
        $this->assertDebuggingCalled();

        // Get the tag instance that should have been created.
        $taginstance = $DB->get_record('tag_instance', array('itemtype' => 'course', 'itemid' => $course->id), '*', MUST_EXIST);
        $this->assertEquals('core', $taginstance->component);
        $this->assertEquals(context_course::instance($course->id)->id, $taginstance->contextid);

        // Now call the tag_assign function without specifying the component or
        // contextid and ensure the function debugging is called twice.
        tag_assign('course', $course->id, $tag2->id, 0, 0);
        $this->assertDebuggingCalled();
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
        $context = context_course::instance($course->id);

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
            $context = context_user::instance($user->id);
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
        $post = new stdClass();
        $post->userid = $users[1]->id;
        $post->content = 'test post content text';
        $post->id = $DB->insert_record('post', $post);
        $context = context_system::instance();
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
        $context = context_course::instance($course->id);

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
        core_tag_tag::set_item_tags('core', 'user', $user1->id, context_user::instance($user1->id), array('cat', 'cats'));
        core_tag_tag::set_item_tags('core', 'user', $user2->id, context_user::instance($user2->id), array('cat', 'cats', 'kitten'));
        core_tag_tag::set_item_tags('core', 'user', $user3->id, context_user::instance($user3->id), array('cat', 'cats'));
        core_tag_tag::set_item_tags('core', 'user', $user4->id, context_user::instance($user4->id), array('dog', 'dogs', 'puppy'));
        core_tag_tag::set_item_tags('core', 'user', $user5->id, context_user::instance($user5->id), array('dog', 'dogs', 'puppy'));
        core_tag_tag::set_item_tags('core', 'user', $user6->id, context_user::instance($user6->id), array('dog', 'dogs', 'puppy'));

        $tags = core_tag_tag::get_by_name_bulk(core_tag_collection::get_default(),
            array('cat', 'cats', 'dog', 'dogs', 'kitten', 'puppy'), '*');

        // Add manual relation between tags 'cat' and 'kitten'.
        core_tag_tag::get($tags['cat']->id)->set_related_tags(array('kitten'));

        return $tags;
    }

    /**
     * Test for function tag_compute_correlations() that is part of tag cron
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

        // Make sure tag_get_correlated() returns 'dogs' and 'puppy' as the correlated tags to the 'dog'.
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

        // Also test deprecated method tag_get_related_tags() and tag_get_correlated().
        $correlatedtags = array_values(tag_get_correlated($tags['cat']));
        $this->assertDebuggingCalled();
        $this->assertCount(3, $correlatedtags); // This will return all existing instances but they all point to the same tag.
        $this->assertEquals('cats', $correlatedtags[0]->rawname);
        $this->assertEquals('cats', $correlatedtags[1]->rawname);
        $this->assertEquals('cats', $correlatedtags[2]->rawname);

        $correlatedtags = array_values(tag_get_related_tags($tags['cat'], TAG_RELATED_CORRELATED));
        $this->assertDebuggingCalled();
        $this->assertCount(1, $correlatedtags); // Duplicates are filtered out here.
        $this->assertEquals('cats', $correlatedtags[0]->rawname);

        $correlatedtags = array_values(tag_get_correlated($tags['dog']));
        $this->assertDebuggingCalled();
        $this->assertCount(6, $correlatedtags); // 2 tags times 3 instances.

        $correlatedtags = array_values(tag_get_related_tags($tags['dog'], TAG_RELATED_CORRELATED));
        $this->assertDebuggingCalled();
        $this->assertCount(2, $correlatedtags);
        $this->assertEquals('dogs', $correlatedtags[0]->rawname);
        $this->assertEquals('puppy', $correlatedtags[1]->rawname);

        $relatedtags = array_values(tag_get_related_tags($tags['cat']));
        $this->assertDebuggingCalled();
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

        // Make sure tag_get_correlated() and tag_get_tags() return the same set of fields.
        // Both functions were deprecated in 3.1.
        $relatedtags = tag_get_tags('tag', $tags['cat']);
        $this->assertDebuggingCalled();
        $relatedtag = reset($relatedtags);
        $correlatedtags = tag_get_correlated($tags['cat']);
        $this->assertDebuggingCalled();
        $correlatedtag = reset($correlatedtags);
        $this->assertEquals(array_keys((array)$relatedtag), array_keys((array)$correlatedtag));
    }

    /**
     * Test for function tag_cleanup() that is part of tag cron
     */
    public function test_cleanup() {
        global $DB;
        $task = new \core\task\tag_cron_task();

        $user = $this->getDataGenerator()->create_user();
        $defaultcoll = core_tag_collection::get_default();

        // Setting tags will create non-standard tags 'cat', 'dog' and 'fish'.
        core_tag_tag::set_item_tags('core', 'user', $user->id, context_user::instance($user->id), array('cat', 'dog', 'fish'));

        $this->assertTrue($DB->record_exists('tag', array('name' => 'cat')));
        $this->assertTrue($DB->record_exists('tag', array('name' => 'dog')));
        $this->assertTrue($DB->record_exists('tag', array('name' => 'fish')));

        // Make tag 'dog' standard.
        $dogtag = core_tag_tag::get_by_name($defaultcoll, 'dog', '*');
        $fishtag = core_tag_tag::get_by_name($defaultcoll, 'fish');
        $dogtag->update(array('isstandard' => 1));

        // Manually remove the instances pointing on tags 'dog' and 'fish'.
        $DB->execute('DELETE FROM {tag_instance} WHERE tagid in (?,?)', array($dogtag->id, $fishtag->id));

        // Call tag_cleanup().
        $task->cleanup();

        // Tag 'cat' is still present because it's used. Tag 'dog' is present because it's standard.
        // Tag 'fish' was removed because it is not standard and it is no longer used by anybody.
        $this->assertTrue($DB->record_exists('tag', array('name' => 'cat')));
        $this->assertTrue($DB->record_exists('tag', array('name' => 'dog')));
        $this->assertFalse($DB->record_exists('tag', array('name' => 'fish')));

        // Delete user without using API function.
        $DB->update_record('user', array('id' => $user->id, 'deleted' => 1));

        // Call tag_cleanup().
        $task->cleanup();

        // Tag 'cat' was now deleted too.
        $this->assertFalse($DB->record_exists('tag', array('name' => 'cat')));

        // Assign tag to non-existing record. Make sure tag was created in the DB.
        core_tag_tag::set_item_tags('core', 'course', 1231231, context_system::instance(), array('bird'));
        $this->assertTrue($DB->record_exists('tag', array('name' => 'bird')));

        // Call tag_cleanup().
        $task->cleanup();

        // Tag 'bird' was now deleted because the related record does not exist in the DB.
        $this->assertFalse($DB->record_exists('tag', array('name' => 'bird')));

        // Now we have a tag instance pointing on 'sometag' tag.
        $user = $this->getDataGenerator()->create_user();
        core_tag_tag::set_item_tags('core', 'user', $user->id, context_user::instance($user->id), array('sometag'));
        $sometag = core_tag_tag::get_by_name($defaultcoll, 'sometag');

        $this->assertTrue($DB->record_exists('tag_instance', array('tagid' => $sometag->id)));

        // Some hacker removes the tag without using API.
        $DB->delete_records('tag', array('id' => $sometag->id));

        // Call tag_cleanup().
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
        $context = context_course::instance($course->id);

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
        $blogpost = new blog_entry(null, array('subject' => 'test'), null);
        $states = blog_entry::get_applicable_publish_states();
        $blogpost->publishstate = reset($states);
        $blogpost->add();

        core_tag_tag::set_item_tags('core', 'user', $user1->id, context_user::instance($user1->id),
                array('Tag1', 'Tag2'));
        core_tag_tag::set_item_tags('core', 'user', $user2->id, context_user::instance($user2->id),
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

        core_tag_tag::set_item_tags('core', 'post', $blogpost->id, context_system::instance(),
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

        core_tag_tag::set_item_tags('core', 'post', $blogpost->id, context_system::instance(),
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

        core_tag_tag::set_item_tags('core', 'post', $blogpost->id, context_system::instance(),
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
        $related11 = tag_get_related_tags($tag11->id, TAG_RELATED_MANUAL);
        $this->assertDebuggingCalled();
        $related11 = array_map('core_tag_tag::make_display_name', $related11);
        sort($related11); // Order of related tags may be random.
        $this->assertEquals('Tag2, Tag4', join(', ', $related11));

        $tag21 = core_tag_tag::get_by_name($collid2, 'TAG1');
        $related21 = tag_get_related_tags($tag21->id, TAG_RELATED_MANUAL);
        $this->assertDebuggingCalled();
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

    public function test_normalize() {
        $tagset = array('Cat', ' Dog  ', '<Mouse', '<>', 'mouse', 'Dog');

        // Test function tag_normalize() that was deprecated in 3.1.
        $this->assertEquals(array('Cat' => 'Cat', 'Dog' => 'Dog', '<Mouse' => 'Mouse', '<>' => '', 'mouse' => 'mouse'),
            tag_normalize($tagset, TAG_CASE_ORIGINAL));
        $this->assertDebuggingCalled();
        $this->assertEquals(array('Cat' => 'cat', 'Dog' => 'dog', '<Mouse' => 'mouse', '<>' => '', 'mouse' => 'mouse'),
            tag_normalize($tagset, TAG_CASE_LOWER));
        $this->assertDebuggingCalled();

        // Test replacement function core_tag_tag::normalize().
        $this->assertEquals(array('Cat' => 'Cat', 'Dog' => 'Dog', '<Mouse' => 'Mouse', '<>' => '', 'mouse' => 'mouse'),
            core_tag_tag::normalize($tagset, false));
        $this->assertEquals(array('Cat' => 'cat', 'Dog' => 'dog', '<Mouse' => 'mouse', '<>' => '', 'mouse' => 'mouse'),
            core_tag_tag::normalize($tagset, true));
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
        $context = context_system::instance();
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
        $context = context_system::instance();
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
        core_tag_tag::set_item_tags('core', 'user', $user7->id, context_user::instance($user7->id), array('hippo'));
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
}
