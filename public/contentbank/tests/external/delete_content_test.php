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
 * External function test for delete_content.
 *
 * @package    core_contentbank
 * @category   external
 * @since      Moodle 3.9
 * @copyright  2020 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_contentbank\external;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/contentbank/tests/fixtures/testable_content.php');

use core_external\external_api;

/**
 * External function test for delete_content.
 *
 * @package    core_contentbank
 * @copyright  2020 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class delete_content_test extends \core_external\tests\externallib_testcase {
    /**
     * Test the behaviour of delete_content().
     */
    public function test_delete_content(): void {
        global $DB;
        $this->resetAfterTest();
        $records = [];

        // Create users.
        $user = $this->getDataGenerator()->create_user();
        $roleid = $DB->get_field('role', 'id', array('shortname' => 'manager'));
        $manager = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->role_assign($roleid, $manager->id);

        // Add some content to the content bank.
        $generator = $this->getDataGenerator()->get_plugin_generator('core_contentbank');
        $records[$manager->id] = $generator->generate_contentbank_data('contenttype_testable', 4, $manager->id, null, false);
        $records[$user->id] = $generator->generate_contentbank_data('contenttype_testable', 2, $user->id, null, false);

        // Check the content has been created as expected.
        $this->assertEquals(6, $DB->count_records('contentbank_content'));

        // Check the content is deleted as expected by the user when the content has been created by herself.
        $this->setUser($user);
        $userrecord = array_shift($records[$user->id]);
        $result = delete_content::execute([$userrecord->id]);
        $result = external_api::clean_returnvalue(delete_content::execute_returns(), $result);
        $this->assertTrue($result['result']);
        $this->assertCount(0, $result['warnings']);
        $this->assertEquals(5, $DB->count_records('contentbank_content'));

        // Check the content is not deleted if the user hasn't created it and has only permission to delete her own content.
        $userrecord = array_shift($records[$user->id]);
        $managerrecord1 = array_shift($records[$manager->id]);
        $result = delete_content::execute([$managerrecord1->id, $userrecord->id]);
        $result = external_api::clean_returnvalue(delete_content::execute_returns(), $result);
        $this->assertFalse($result['result']);
        $this->assertCount(1, $result['warnings']);
        $warning = array_shift($result['warnings']);
        $this->assertEquals('nopermissiontodelete', $warning['warningcode']);
        $this->assertEquals($managerrecord1->id, $warning['item']);
        $this->assertEquals(4, $DB->count_records('contentbank_content'));

        // Check the content is deleted as expected by the manager.
        $this->setUser($manager);
        $managerrecord2 = array_shift($records[$manager->id]);
        $result = delete_content::execute([$managerrecord1->id, $managerrecord2->id]);
        $result = external_api::clean_returnvalue(delete_content::execute_returns(), $result);
        $this->assertTrue($result['result']);
        $this->assertCount(0, $result['warnings']);
        $this->assertEquals(2, $DB->count_records('contentbank_content'));

        // Check an exception warning is returned if an unexisting contentid is deleted.
        // Check also the other content is deleted (so the process continues after the exception is thrown).
        $managerrecord3 = array_shift($records[$manager->id]);
        $result = delete_content::execute([$managerrecord1->id, $managerrecord3->id]);
        $result = external_api::clean_returnvalue(delete_content::execute_returns(), $result);
        $this->assertFalse($result['result']);
        $this->assertCount(1, $result['warnings']);
        $warning = array_shift($result['warnings']);
        $this->assertEquals('exception', $warning['warningcode']);
        $this->assertEquals($managerrecord1->id, $warning['item']);
        $this->assertEquals(1, $DB->count_records('contentbank_content'));
    }
}
