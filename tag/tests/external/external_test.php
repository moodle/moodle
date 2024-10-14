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
 * Unit tests for WS in tags
 *
 * @package core_tag
 * @category test
 * @copyright 2015 Marina Glancy
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_tag\external;

use externallib_advanced_testcase;
use core_external\external_api;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/webservice/tests/helpers.php');

final class external_test extends externallib_advanced_testcase {
    /**
     * Test update_categories
     */
    public function test_update_tags(): void {
        global $DB;
        $this->resetAfterTest();
        $context = \context_system::instance();

        $originaltag = array(
            'isstandard' => 0,
            'flag' => 1,
            'rawname' => 'test',
            'description' => 'desc'
        );
        $tag = $this->getDataGenerator()->create_tag($originaltag);

        $updatetag = array(
            'id' => $tag->id,
            'description' => 'Trying to change tag description',
            'rawname' => 'Trying to change tag name',
            'flag' => 0,
            'isstandard' => 1,
        );
        $gettag = array(
            'id' => $tag->id,
        );

        // User without any caps can not change anything about a tag but can request [partial] tag data.
        $this->setUser($this->getDataGenerator()->create_user());
        $result = \core_tag_external::update_tags(array($updatetag));
        $result = external_api::clean_returnvalue(\core_tag_external::update_tags_returns(), $result);
        $this->assertEquals($tag->id, $result['warnings'][0]['item']);
        $this->assertEquals('nothingtoupdate', $result['warnings'][0]['warningcode']);
        $this->assertEquals($originaltag['rawname'], $DB->get_field('tag', 'rawname',
            array('id' => $tag->id)));
        $this->assertEquals($originaltag['description'], $DB->get_field('tag', 'description',
            array('id' => $tag->id)));

        $result = \core_tag_external::get_tags(array($gettag));
        $result = external_api::clean_returnvalue(\core_tag_external::get_tags_returns(), $result);
        $this->assertEquals($originaltag['rawname'], $result['tags'][0]['rawname']);
        $this->assertEquals($originaltag['description'], $result['tags'][0]['description']);
        $this->assertNotEmpty($result['tags'][0]['viewurl']);
        $this->assertArrayNotHasKey('changetypeurl', $result['tags'][0]);
        $this->assertArrayNotHasKey('changeflagurl', $result['tags'][0]);
        $this->assertArrayNotHasKey('flag', $result['tags'][0]);
        $this->assertArrayNotHasKey('official', $result['tags'][0]);
        $this->assertArrayNotHasKey('isstandard', $result['tags'][0]);

        // User with editing only capability can change description but not the tag name.
        $roleid = $this->assignUserCapability('moodle/tag:edit', $context->id);
        $result = \core_tag_external::update_tags(array($updatetag));
        $result = external_api::clean_returnvalue(\core_tag_external::update_tags_returns(), $result);
        $this->assertEmpty($result['warnings']);

        $result = \core_tag_external::get_tags(array($gettag));
        $result = external_api::clean_returnvalue(\core_tag_external::get_tags_returns(), $result);
        $this->assertEquals($updatetag['id'], $result['tags'][0]['id']);
        $this->assertEquals($updatetag['description'], $result['tags'][0]['description']);
        $this->assertEquals($originaltag['rawname'], $result['tags'][0]['rawname']);
        $this->assertArrayNotHasKey('flag', $result['tags'][0]); // 'Flag' is not available unless 'moodle/tag:manage' cap exists.
        $this->assertEquals(0, $result['tags'][0]['official']);
        $this->assertEquals(0, $result['tags'][0]['isstandard']);
        $this->assertEquals($originaltag['rawname'], $DB->get_field('tag', 'rawname',
                array('id' => $tag->id)));
        $this->assertEquals($updatetag['description'], $DB->get_field('tag', 'description',
                array('id' => $tag->id)));

        // User with editing and manage cap can also change the tag name,
        // make it standard and reset flag.
        assign_capability('moodle/tag:manage', CAP_ALLOW, $roleid, $context->id);
        $this->assertTrue(has_capability('moodle/tag:manage', $context));
        $result = \core_tag_external::update_tags(array($updatetag));
        $result = external_api::clean_returnvalue(\core_tag_external::update_tags_returns(), $result);
        $this->assertEmpty($result['warnings']);

        $result = \core_tag_external::get_tags(array($gettag));
        $result = external_api::clean_returnvalue(\core_tag_external::get_tags_returns(), $result);
        $this->assertEquals($updatetag['id'], $result['tags'][0]['id']);
        $this->assertEquals($updatetag['rawname'], $result['tags'][0]['rawname']);
        $this->assertEquals(\core_text::strtolower($updatetag['rawname']), $result['tags'][0]['name']);
        $this->assertEquals($updatetag['flag'], $result['tags'][0]['flag']);
        $this->assertEquals($updatetag['isstandard'], $result['tags'][0]['official']);
        $this->assertEquals($updatetag['isstandard'], $result['tags'][0]['isstandard']);
        $this->assertEquals($updatetag['rawname'], $DB->get_field('tag', 'rawname',
                array('id' => $tag->id)));
        $this->assertEquals(1, $DB->get_field('tag', 'isstandard', array('id' => $tag->id)));

        // Updating and getting non-existing tag.
        $nonexistingtag = array(
            'id' => 123,
            'description' => 'test'
        );
        $getnonexistingtag = array(
            'id' => 123,
        );
        $result = \core_tag_external::update_tags(array($nonexistingtag));
        $result = external_api::clean_returnvalue(\core_tag_external::update_tags_returns(), $result);
        $this->assertEquals(123, $result['warnings'][0]['item']);
        $this->assertEquals('tagnotfound', $result['warnings'][0]['warningcode']);

        $result = \core_tag_external::get_tags(array($getnonexistingtag));
        $result = external_api::clean_returnvalue(\core_tag_external::get_tags_returns(), $result);
        $this->assertEmpty($result['tags']);
        $this->assertEquals(123, $result['warnings'][0]['item']);
        $this->assertEquals('tagnotfound', $result['warnings'][0]['warningcode']);

        // Attempt to update a tag to the name that is reserved.
        $anothertag = $this->getDataGenerator()->create_tag(array('rawname' => 'Mytag'));
        $updatetag2 = array('id' => $tag->id, 'rawname' => 'MYTAG');
        $result = \core_tag_external::update_tags(array($updatetag2));
        $result = external_api::clean_returnvalue(\core_tag_external::update_tags_returns(), $result);
        $this->assertEquals($tag->id, $result['warnings'][0]['item']);
        $this->assertEquals('namesalreadybeeingused', $result['warnings'][0]['warningcode']);
    }

    /**
     * Test update_inplace_editable()
     */
    public function test_update_inplace_editable(): void {
        global $CFG, $DB, $PAGE;
        require_once($CFG->dirroot . '/lib/external/externallib.php');

        $this->resetAfterTest(true);
        $tag = $this->getDataGenerator()->create_tag();
        $this->setUser($this->getDataGenerator()->create_user());

        // Call service for core_tag component without necessary permissions.
        try {
            \core_external::update_inplace_editable('core_tag', 'tagname', $tag->id, 'new tag name');
            $this->fail('Exception expected');
        } catch (\moodle_exception $e) {
            $this->assertEquals('Sorry, but you do not currently have permissions to do that (Manage all tags).',
                    $e->getMessage());
        }

        // Change to admin user and make sure that tag name can be updated using web service update_inplace_editable().
        $this->setAdminUser();
        $res = \core_external::update_inplace_editable('core_tag', 'tagname', $tag->id, 'New tag name');
        $res = external_api::clean_returnvalue(\core_external::update_inplace_editable_returns(), $res);
        $this->assertEquals('New tag name', $res['value']);
        $this->assertEquals('New tag name', $DB->get_field('tag', 'rawname', array('id' => $tag->id)));

        // Call callback core_tag_inplace_editable() directly.
        $tmpl = component_callback('core_tag', 'inplace_editable', array('tagname', $tag->id, 'Rename me again'));
        $this->assertInstanceOf('core\output\inplace_editable', $tmpl);
        $res = $tmpl->export_for_template($PAGE->get_renderer('core'));
        $this->assertEquals('Rename me again', $res['value']);
        $this->assertEquals('Rename me again', $DB->get_field('tag', 'rawname', array('id' => $tag->id)));
    }

    /**
     * Test get_tagindex_per_area.
     */
    public function test_get_tagindex_per_area(): void {
        global $USER;
        $this->resetAfterTest(true);

        // Create tags for two user profiles and one course.
        $this->setAdminUser();
        $context = \context_user::instance($USER->id);
        \core_tag_tag::set_item_tags('core', 'user', $USER->id, $context, array('test'));

        $this->setUser($this->getDataGenerator()->create_user());
        $context = \context_user::instance($USER->id);
        \core_tag_tag::set_item_tags('core', 'user', $USER->id, $context, array('test'));

        $course = $this->getDataGenerator()->create_course();
        $context = \context_course::instance($course->id);
        \core_tag_tag::set_item_tags('core', 'course', $course->id, $context, array('test'));

        $tag = \core_tag_tag::get_by_name(0, 'test');

        // First, search by id.
        $result = \core_tag_external::get_tagindex_per_area(array('id' => $tag->id));
        $result = external_api::clean_returnvalue(\core_tag_external::get_tagindex_per_area_returns(), $result);
        $this->assertCount(2, $result); // Two different areas: course and user.
        $this->assertEquals($tag->id, $result[0]['tagid']);
        $this->assertEquals('course', $result[0]['itemtype']);
        $this->assertEquals($tag->id, $result[1]['tagid']);
        $this->assertEquals('user', $result[1]['itemtype']);

        // Now, search by name.
        $result = \core_tag_external::get_tagindex_per_area(array('tag' => 'test'));
        $result = external_api::clean_returnvalue(\core_tag_external::get_tagindex_per_area_returns(), $result);
        $this->assertCount(2, $result); // Two different areas: course and user.
        $this->assertEquals($tag->id, $result[0]['tagid']);
        $this->assertEquals('course', $result[0]['itemtype']);
        $this->assertEquals($tag->id, $result[1]['tagid']);
        $this->assertEquals('user', $result[1]['itemtype']);

        // Filter by tag area.
        $result = \core_tag_external::get_tagindex_per_area(array('tag' => 'test', 'ta' => $result[0]['ta']));
        $result = external_api::clean_returnvalue(\core_tag_external::get_tagindex_per_area_returns(), $result);
        $this->assertCount(1, $result); // Just the given area.
        $this->assertEquals($tag->id, $result[0]['tagid']);
        $this->assertEquals('course', $result[0]['itemtype']);

        // Now, search by tag collection (use default).
        $result = \core_tag_external::get_tagindex_per_area(array('id' => $tag->id, 'tc' => 1));
        $result = external_api::clean_returnvalue(\core_tag_external::get_tagindex_per_area_returns(), $result);
        $this->assertCount(2, $result); // Two different areas: course and user.
    }

    /**
     * Test get_tag_areas.
     */
    public function test_get_tag_areas(): void {
        global $DB;
        $this->resetAfterTest(true);

        $this->setAdminUser();
        $result = \core_tag_external::get_tag_areas();
        $result = external_api::clean_returnvalue(\core_tag_external::get_tag_areas_returns(), $result);
        $areas = $DB->get_records('tag_area');
        $this->assertCount(count($areas), $result['areas']);
        foreach ($result['areas'] as $area) {
            $this->assertEquals($areas[$area['id']]->component, $area['component']);
            $this->assertEquals($areas[$area['id']]->itemtype, $area['itemtype']);
        }
    }

    /**
     * Test get_tag_collections.
     */
    public function test_get_tag_collections(): void {
        global $DB;
        $this->resetAfterTest(true);

        // Create new tag collection.
        $data = (object) array('name' => 'new tag coll');
        \core_tag_collection::create($data);

        $this->setAdminUser();
        $result = \core_tag_external::get_tag_collections();
        $result = external_api::clean_returnvalue(\core_tag_external::get_tag_collections_returns(), $result);

        $collections = $DB->get_records('tag_coll');
        $this->assertCount(count($collections), $result['collections']);
        foreach ($result['collections'] as $collection) {
            $this->assertEquals($collections[$collection['id']]->component, $collection['component']);
            $this->assertEquals($collections[$collection['id']]->name, $collection['name']);
        }
    }

    /**
     * Test get_tag_cloud.
     */
    public function test_get_tag_cloud(): void {
        global $USER, $DB;
        $this->resetAfterTest(true);

        // Create tags for two user profiles, a post and one course.
        $this->setAdminUser();
        $context = \context_user::instance($USER->id);
        \core_tag_tag::set_item_tags('core', 'user', $USER->id, $context, array('Cats', 'Dogs'));

        $this->setUser($this->getDataGenerator()->create_user());
        $context = \context_user::instance($USER->id);
        \core_tag_tag::set_item_tags('core', 'user', $USER->id, $context, array('Mice'));

        $course = $this->getDataGenerator()->create_course();
        $coursecontext = \context_course::instance($course->id);
        \core_tag_tag::set_item_tags('core', 'course', $course->id, $coursecontext, array('Cats'));

        $post = new \stdClass();
        $post->userid = $USER->id;
        $post->content = 'test post content text';
        $post->id = $DB->insert_record('post', $post);
        $context = \context_system::instance();
        \core_tag_tag::set_item_tags('core', 'post', $post->id, $context, array('Horses', 'Cats'));

        // First, retrieve complete cloud.
        $result = \core_tag_external::get_tag_cloud();
        $result = external_api::clean_returnvalue(\core_tag_external::get_tag_cloud_returns(), $result);
        $this->assertCount(4, $result['tags']); // Four different tags: Cats, Dogs, Mice, Horses.
        $this->assertEquals(4, $result['tagscount']);
        $this->assertEquals(4, $result['totalcount']);

        foreach ($result['tags'] as $tag) {
            if ($tag['name'] == 'Cats') {
                $this->assertEquals(3, $tag['count']);
            } else {
                $this->assertEquals(1, $tag['count']);
            }
        }

        // Test filter by collection, pagination and sorting.
        $defaultcoll = \core_tag_collection::get_default();
        $result = \core_tag_external::get_tag_cloud($defaultcoll, false, 2, 'count');
        $result = external_api::clean_returnvalue(\core_tag_external::get_tag_cloud_returns(), $result);
        $this->assertCount(2, $result['tags']); // Only two tags.
        $this->assertEquals(2, $result['tagscount']);
        $this->assertEquals(4, $result['totalcount']);
        $this->assertEquals('Dogs', $result['tags'][0]['name']); // Lower count first.

        // Test search.
        $result = \core_tag_external::get_tag_cloud(0, false, 150, 'name', 'Mice');
        $result = external_api::clean_returnvalue(\core_tag_external::get_tag_cloud_returns(), $result);
        $this->assertCount(1, $result['tags']); // Only the searched tags.
        $this->assertEquals(1, $result['tagscount']);
        $this->assertEquals(1, $result['totalcount']); // When searching, the total is always for the search.
        $this->assertEquals('Mice', $result['tags'][0]['name']);

        $result = \core_tag_external::get_tag_cloud(0, false, 150, 'name', 'Conejo');
        $result = external_api::clean_returnvalue(\core_tag_external::get_tag_cloud_returns(), $result);
        $this->assertCount(0, $result['tags']); // Nothing found.
        $this->assertEquals(0, $result['tagscount']);
        $this->assertEquals(0, $result['totalcount']); // When searching, the total is always for the search.

        // Test standard filtering.
        $micetag = \core_tag_tag::get_by_name($defaultcoll, 'Mice', '*');
        $micetag->update(array('isstandard' => 1));

        $result = \core_tag_external::get_tag_cloud(0, true);
        $result = external_api::clean_returnvalue(\core_tag_external::get_tag_cloud_returns(), $result);
        $this->assertCount(1, $result['tags']);
        $this->assertEquals(1, $result['tagscount']);
        $this->assertEquals(1, $result['totalcount']); // When searching, the total is always for the search.
        $this->assertEquals('Mice', $result['tags'][0]['name']);

        // Test course context filtering.
        $result = \core_tag_external::get_tag_cloud(0, false, 150, 'name', '', 0, $coursecontext->id);
        $result = external_api::clean_returnvalue(\core_tag_external::get_tag_cloud_returns(), $result);
        $this->assertCount(1, $result['tags']);
        $this->assertEquals(1, $result['tagscount']);
        $this->assertEquals(1, $result['totalcount']); // When searching, the total is always for the search.
        $this->assertEquals('Cats', $result['tags'][0]['name']);

        // Complete system context.
        $result = \core_tag_external::get_tag_cloud(0, false, 150, 'name', '', 0, \context_system::instance()->id);
        $result = external_api::clean_returnvalue(\core_tag_external::get_tag_cloud_returns(), $result);
        $this->assertCount(4, $result['tags']);
        $this->assertEquals(4, $result['tagscount']);

        // Just system context - avoid children.
        $result = \core_tag_external::get_tag_cloud(0, false, 150, 'name', '', 0, \context_system::instance()->id, 0);
        $result = external_api::clean_returnvalue(\core_tag_external::get_tag_cloud_returns(), $result);
        $this->assertCount(2, $result['tags']);
        $this->assertEquals(2, $result['tagscount']); // Horses and Cats.
        $this->assertEquals('Cats', $result['tags'][0]['name']);
        $this->assertEquals('Horses', $result['tags'][1]['name']);
    }
}
