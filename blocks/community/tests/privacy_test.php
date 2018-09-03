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
 * Unit tests for the block_community implementation of the privacy API.
 *
 * @package    block_community
 * @category   test
 * @copyright  2018 Zig Tan <zig@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use \core_privacy\local\metadata\collection;
use \core_privacy\local\request\writer;
use \core_privacy\local\request\approved_contextlist;
use \block_community\privacy\provider;

/**
 * Unit tests for the block_community implementation of the privacy API.
 *
 * @copyright  2018 Zig Tan <zig@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_community_privacy_testcase extends \core_privacy\tests\provider_testcase {

    /**
     * Overriding setUp() function to always reset after tests.
     */
    public function setUp() {
        $this->resetAfterTest(true);
    }

    /**
     * Test for provider::get_metadata().
     */
    public function test_get_metadata() {
        $collection = new collection('block_community');
        $newcollection = provider::get_metadata($collection);
        $itemcollection = $newcollection->get_collection();
        $this->assertCount(1, $itemcollection);

        $table = reset($itemcollection);
        $this->assertEquals('block_community', $table->get_name());

        $privacyfields = $table->get_privacy_fields();
        $this->assertArrayHasKey('userid', $privacyfields);
        $this->assertArrayHasKey('coursename', $privacyfields);
        $this->assertArrayHasKey('coursedescription', $privacyfields);
        $this->assertArrayHasKey('courseurl', $privacyfields);
        $this->assertArrayHasKey('imageurl', $privacyfields);

        $this->assertEquals('privacy:metadata:block_community', $table->get_summary());
    }

    /**
     * Test for provider::get_contexts_for_userid().
     */
    public function test_get_contexts_for_userid() {
        global $DB;

        // Test setup.
        $teacher = $this->getDataGenerator()->create_user();
        $this->setUser($teacher);

        // Add two community links for the User.
        $community = (object)[
            'userid' => $teacher->id,
            'coursename' => 'Dummy Community Course Name - 1',
            'coursedescription' => 'Dummy Community Course Description - 1',
            'courseurl' => 'https://moodle.org/community_courses/Dummy_Community_Course-1',
            'imageurl' => ''
        ];
        $DB->insert_record('block_community', $community);

        $community = (object)[
            'userid' => $teacher->id,
            'coursename' => 'Dummy Community Course Name - 2',
            'coursedescription' => 'Dummy Community Course Description - 2',
            'courseurl' => 'https://moodle.org/community_courses/Dummy_Community_Course-2',
            'imageurl' => ''
        ];
        $DB->insert_record('block_community', $community);

        // Test the User's retrieved contextlist contains only one context.
        $contextlist = provider::get_contexts_for_userid($teacher->id);
        $contexts = $contextlist->get_contexts();
        $this->assertCount(1, $contexts);

        // Test the User's contexts equal the User's own context.
        $context = reset($contexts);
        $this->assertEquals(CONTEXT_USER, $context->contextlevel);
        $this->assertEquals($teacher->id, $context->instanceid);
    }

    /**
     * Test for provider::export_user_data().
     */
    public function test_export_user_data() {
        global $DB;

        // Test setup.
        $teacher = $this->getDataGenerator()->create_user();
        $this->setUser($teacher);

        // Add 3 community links for the User.
        $nocommunities = 3;
        for ($c = 0; $c < $nocommunities; $c++) {
            $community = (object)[
                'userid' => $teacher->id,
                'coursename' => 'Dummy Community Course Name - ' . $c,
                'coursedescription' => 'Dummy Community Course Description - ' . $c,
                'courseurl' => 'https://moodle.org/community_courses/Dummy_Community_Course-' . $c,
                'imageurl' => ''
            ];
            $DB->insert_record('block_community', $community);
        }

        // Test the created block_community records matches the test number of communities specified.
        $communities = $DB->get_records('block_community', ['userid' => $teacher->id]);
        $this->assertCount($nocommunities, $communities);

        // Test the User's retrieved contextlist contains only one context.
        $contextlist = provider::get_contexts_for_userid($teacher->id);
        $contexts = $contextlist->get_contexts();
        $this->assertCount(1, $contexts);

        // Test the User's contexts equal the User's own context.
        $context = reset($contexts);
        $this->assertEquals(CONTEXT_USER, $context->contextlevel);
        $this->assertEquals($teacher->id, $context->instanceid);

        $approvedcontextlist = new approved_contextlist($teacher, 'block_community', $contextlist->get_contextids());

        // Retrieve Calendar Event and Subscriptions data only for this user.
        provider::export_user_data($approvedcontextlist);

        // Test the block_community data is exported at the User context level.
        $user = $approvedcontextlist->get_user();
        $contextuser = context_user::instance($user->id);
        $writer = writer::with_context($contextuser);
        $this->assertTrue($writer->has_any_data());
    }

    /**
     * Test for provider::delete_data_for_all_users_in_context().
     */
    public function test_delete_data_for_all_users_in_context() {
        global $DB;

        // Test setup.
        $teacher = $this->getDataGenerator()->create_user();
        $this->setUser($teacher);

        // Add a community link for the User.
        $community = (object)[
            'userid' => $teacher->id,
            'coursename' => 'Dummy Community Course Name',
            'coursedescription' => 'Dummy Community Course Description',
            'courseurl' => 'https://moodle.org/community_courses/Dummy_Community_Course',
            'imageurl' => ''
        ];
        $DB->insert_record('block_community', $community);

        // Test the User's retrieved contextlist contains only one context.
        $contextlist = provider::get_contexts_for_userid($teacher->id);
        $contexts = $contextlist->get_contexts();
        $this->assertCount(1, $contexts);

        // Test the User's contexts equal the User's own context.
        $context = reset($contexts);
        $this->assertEquals(CONTEXT_USER, $context->contextlevel);
        $this->assertEquals($teacher->id, $context->instanceid);

        // Test delete all users content by context.
        provider::delete_data_for_all_users_in_context($context);
        $blockcommunity = $DB->get_records('block_community', ['userid' => $teacher->id]);
        $this->assertCount(0, $blockcommunity);
    }

    /**
     * Test for provider::delete_data_for_user().
     */
    public function test_delete_data_for_user() {
        global $DB;

        // Test setup.
        $teacher1 = $this->getDataGenerator()->create_user();
        $teacher2 = $this->getDataGenerator()->create_user();
        $this->setUser($teacher1);

        // Add 3 community links for Teacher 1.
        $nocommunities = 3;
        for ($c = 0; $c < $nocommunities; $c++) {
            $community = (object)[
                'userid' => $teacher1->id,
                'coursename' => 'Dummy Community Course Name - ' . $c,
                'coursedescription' => 'Dummy Community Course Description - ' . $c,
                'courseurl' => 'https://moodle.org/community_courses/Dummy_Community_Course-' . $c,
                'imageurl' => ''
            ];
            $DB->insert_record('block_community', $community);
        }

        // Add 1 community link for Teacher 2.
        $community = (object)[
            'userid' => $teacher2->id,
            'coursename' => 'Dummy Community Course Name - Blah',
            'coursedescription' => 'Dummy Community Course Description - Blah',
            'courseurl' => 'https://moodle.org/community_courses/Dummy_Community_Course-Blah',
            'imageurl' => ''
        ];
        $DB->insert_record('block_community', $community);

        // Test the created block_community records for Teacher 1 equals test number of communities specified.
        $communities = $DB->get_records('block_community', ['userid' => $teacher1->id]);
        $this->assertCount($nocommunities, $communities);

        // Test the created block_community records for Teacher 2 equals 1.
        $communities = $DB->get_records('block_community', ['userid' => $teacher2->id]);
        $this->assertCount(1, $communities);

        // Test the deletion of block_community records for Teacher 1 results in zero records.
        $contextlist = provider::get_contexts_for_userid($teacher1->id);
        $contexts = $contextlist->get_contexts();
        $this->assertCount(1, $contexts);

        // Test the User's contexts equal the User's own context.
        $context = reset($contexts);
        $this->assertEquals(CONTEXT_USER, $context->contextlevel);
        $this->assertEquals($teacher1->id, $context->instanceid);

        $approvedcontextlist = new approved_contextlist($teacher1, 'block_community', $contextlist->get_contextids());
        provider::delete_data_for_user($approvedcontextlist);
        $communities = $DB->get_records('block_community', ['userid' => $teacher1->id]);
        $this->assertCount(0, $communities);


        // Test that Teacher 2's single block_community record still exists.
        $contextlist = provider::get_contexts_for_userid($teacher2->id);
        $contexts = $contextlist->get_contexts();
        $this->assertCount(1, $contexts);

        // Test the User's contexts equal the User's own context.
        $context = reset($contexts);
        $this->assertEquals(CONTEXT_USER, $context->contextlevel);
        $this->assertEquals($teacher2->id, $context->instanceid);

        $communities = $DB->get_records('block_community', ['userid' => $teacher2->id]);
        $this->assertCount(1, $communities);
    }

}
