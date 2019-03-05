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

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->libdir . '/externallib.php');
require_once($CFG->dirroot . '/webservice/tests/helpers.php');

class core_tag_external_testcase extends externallib_advanced_testcase {
    /**
     * Test update_categories
     */
    public function test_update_tags() {
        global $DB;
        $this->resetAfterTest();
        $context = context_system::instance();

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
        $result = core_tag_external::update_tags(array($updatetag));
        $result = external_api::clean_returnvalue(core_tag_external::update_tags_returns(), $result);
        $this->assertEquals($tag->id, $result['warnings'][0]['item']);
        $this->assertEquals('nothingtoupdate', $result['warnings'][0]['warningcode']);
        $this->assertEquals($originaltag['rawname'], $DB->get_field('tag', 'rawname',
            array('id' => $tag->id)));
        $this->assertEquals($originaltag['description'], $DB->get_field('tag', 'description',
            array('id' => $tag->id)));

        $result = core_tag_external::get_tags(array($gettag));
        $result = external_api::clean_returnvalue(core_tag_external::get_tags_returns(), $result);
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
        $result = core_tag_external::update_tags(array($updatetag));
        $result = external_api::clean_returnvalue(core_tag_external::update_tags_returns(), $result);
        $this->assertEmpty($result['warnings']);

        $result = core_tag_external::get_tags(array($gettag));
        $result = external_api::clean_returnvalue(core_tag_external::get_tags_returns(), $result);
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
        $context->mark_dirty();
        $this->assertTrue(has_capability('moodle/tag:manage', $context));
        $result = core_tag_external::update_tags(array($updatetag));
        $result = external_api::clean_returnvalue(core_tag_external::update_tags_returns(), $result);
        $this->assertEmpty($result['warnings']);

        $result = core_tag_external::get_tags(array($gettag));
        $result = external_api::clean_returnvalue(core_tag_external::get_tags_returns(), $result);
        $this->assertEquals($updatetag['id'], $result['tags'][0]['id']);
        $this->assertEquals($updatetag['rawname'], $result['tags'][0]['rawname']);
        $this->assertEquals(core_text::strtolower($updatetag['rawname']), $result['tags'][0]['name']);
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
        $result = core_tag_external::update_tags(array($nonexistingtag));
        $result = external_api::clean_returnvalue(core_tag_external::update_tags_returns(), $result);
        $this->assertEquals(123, $result['warnings'][0]['item']);
        $this->assertEquals('tagnotfound', $result['warnings'][0]['warningcode']);

        $result = core_tag_external::get_tags(array($getnonexistingtag));
        $result = external_api::clean_returnvalue(core_tag_external::get_tags_returns(), $result);
        $this->assertEmpty($result['tags']);
        $this->assertEquals(123, $result['warnings'][0]['item']);
        $this->assertEquals('tagnotfound', $result['warnings'][0]['warningcode']);

        // Attempt to update a tag to the name that is reserved.
        $anothertag = $this->getDataGenerator()->create_tag(array('rawname' => 'Mytag'));
        $updatetag2 = array('id' => $tag->id, 'rawname' => 'MYTAG');
        $result = core_tag_external::update_tags(array($updatetag2));
        $result = external_api::clean_returnvalue(core_tag_external::update_tags_returns(), $result);
        $this->assertEquals($tag->id, $result['warnings'][0]['item']);
        $this->assertEquals('namesalreadybeeingused', $result['warnings'][0]['warningcode']);
    }

    /**
     * Test update_inplace_editable()
     */
    public function test_update_inplace_editable() {
        global $CFG, $DB, $PAGE;
        require_once($CFG->dirroot . '/lib/external/externallib.php');

        $this->resetAfterTest(true);
        $tag = $this->getDataGenerator()->create_tag();
        $this->setUser($this->getDataGenerator()->create_user());

        // Call service for core_tag component without necessary permissions.
        try {
            core_external::update_inplace_editable('core_tag', 'tagname', $tag->id, 'new tag name');
            $this->fail('Exception expected');
        } catch (moodle_exception $e) {
            $this->assertEquals('Sorry, but you do not currently have permissions to do that (Manage all tags).',
                    $e->getMessage());
        }

        // Change to admin user and make sure that tag name can be updated using web service update_inplace_editable().
        $this->setAdminUser();
        $res = core_external::update_inplace_editable('core_tag', 'tagname', $tag->id, 'New tag name');
        $res = external_api::clean_returnvalue(core_external::update_inplace_editable_returns(), $res);
        $this->assertEquals('New tag name', $res['value']);
        $this->assertEquals('New tag name', $DB->get_field('tag', 'rawname', array('id' => $tag->id)));

        // Call callback core_tag_inplace_editable() directly.
        $tmpl = component_callback('core_tag', 'inplace_editable', array('tagname', $tag->id, 'Rename me again'));
        $this->assertInstanceOf('core\output\inplace_editable', $tmpl);
        $res = $tmpl->export_for_template($PAGE->get_renderer('core'));
        $this->assertEquals('Rename me again', $res['value']);
        $this->assertEquals('Rename me again', $DB->get_field('tag', 'rawname', array('id' => $tag->id)));
    }
}
