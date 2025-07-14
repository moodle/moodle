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

namespace core_user\external;

use core_external\external_api;

/**
 * Tests for the prepare_private_files_for_edition class.
 *
 * @package   core_user
 * @category  external
 * @copyright 2024 Juan Leyva
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \core_user\external\prepare_private_files_for_edition
 */
final class prepare_private_files_for_edition_test extends \advanced_testcase {

    public function test_execute(): void {
        global $USER;
        $this->resetAfterTest();
        $this->setAdminUser();

        // Create some files in the user private file area.
        $filename = 'faketxt.txt';
        $filerecordinline = [
            'contextid' => \context_user::instance($USER->id)->id,
            'component' => 'user',
            'filearea'  => 'private',
            'itemid'    => 0,
            'filepath'  => '/',
            'filename'  => $filename,
        ];
        $fs = get_file_storage();
        $fs->create_file_from_string($filerecordinline, 'fake txt contents.');

        $result = prepare_private_files_for_edition::execute();

        $result = external_api::clean_returnvalue(prepare_private_files_for_edition::execute_returns(), $result);
        $draftitemid = $result['draftitemid'];
        $this->assertGreaterThan(0, $draftitemid);
        $this->assertCount(5, $result['areaoptions']);
        $this->assertEmpty($result['warnings']);

        // Check we get the expected user private files in the draft area.
        $files = file_get_drafarea_files($draftitemid);
        $this->assertCount(1, $files->list);
        $this->assertEquals($filename, $files->list[0]->filename);
    }

    /**
     * Test missing capabilities.
     */
    public function test_execute_missing_capabilities(): void {
        global $CFG, $DB;
        $this->resetAfterTest();

        $authrole = $DB->get_record('role', ['id' => $CFG->defaultuserroleid]);
        unassign_capability('moodle/user:manageownfiles', $authrole->id);
        accesslib_clear_all_caches_for_unit_testing();

        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        $this->expectException('moodle_exception');
        $result = prepare_private_files_for_edition::execute(0);
    }
}
