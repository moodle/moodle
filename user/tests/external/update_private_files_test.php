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
 * Tests for the \core_user\external\update_private_files class.
 *
 * @package   core_user
 * @category  external
 * @copyright 2024 Juan Leyva
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \core_user\external\update_private_files
 */
final class update_private_files_test extends \advanced_testcase {

    /**
     * Test base cases.
     */
    public function test_execute(): void {
        global $CFG;
        require_once($CFG->dirroot . '/files/externallib.php');

        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();
        $anotheruser = $this->getDataGenerator()->create_user();
        $this->setUser($user);
        $context = \context_user::instance($user->id);

        // Create one file in the user private file area.
        $filename = 'faketxt.txt';
        $filerecordinline = [
            'contextid' => $context->id,
            'component' => 'user',
            'filearea'  => 'private',
            'itemid'    => 0,
            'filepath'  => '/',
            'filename'  => $filename,
        ];
        $fs = get_file_storage();
        $fs->create_file_from_string($filerecordinline, 'fake txt contents.');

        // Retrieve draft area with existing files.
        $result = prepare_private_files_for_edition::execute();
        $result = external_api::clean_returnvalue(prepare_private_files_for_edition::execute_returns(), $result);
        $draftitemid = $result['draftitemid'];

        // Add one file to the draft area reusing previous structure.
        $newfilename = 'newfaketxt.txt';
        $filerecordinline['itemid'] = $draftitemid;
        $filerecordinline['filearea'] = 'draft';
        $filerecordinline['filename'] = $newfilename;
        $fs->create_file_from_string($filerecordinline, 'new fake txt contents.');
        $files = file_get_drafarea_files($draftitemid);
        $this->assertCount(2, $files->list);    // 2 files in the draft area.

        // Update the private files with the new files.
        $result = update_private_files::execute($draftitemid);
        $result = external_api::clean_returnvalue(update_private_files::execute_returns(), $result);
        $this->assertTrue($result['status']);
        $this->assertEmpty($result['warnings']);
        // Check that the new files are in the private file area.
        $files = \core_files_external::get_files($context->id, 'user', 'private', 0, '/', '');
        $this->assertCount(2, $files['files']);    // 2 files in the private area.

        // Now, try deleting one.
        $result = prepare_private_files_for_edition::execute();
        $result = external_api::clean_returnvalue(prepare_private_files_for_edition::execute_returns(), $result);
        $draftitemid = $result['draftitemid'];

        \core_files\external\delete\draft::execute($draftitemid, [
            [
                'filepath' => '/',
                'filename' => $newfilename,
            ],
        ]);
        // Update to force deletion.
        $result = update_private_files::execute($draftitemid);
        $result = external_api::clean_returnvalue(update_private_files::execute_returns(), $result);
        $this->assertTrue($result['status']);
        $this->assertEmpty($result['warnings']);
        // Check we only have one now.
        $files = \core_files_external::get_files($context->id, 'user', 'private', 0, '/', '');
        $this->assertCount(1, $files['files']);
        $this->assertEquals($filename, $files['files'][0]['filename']);

        // Use other's user draft item area.
        $result = prepare_private_files_for_edition::execute();
        $result = external_api::clean_returnvalue(prepare_private_files_for_edition::execute_returns(), $result);
        $draftitemid = $result['draftitemid'];

        $this->setUser($anotheruser);
        $this->expectException('moodle_exception');
        update_private_files::execute($draftitemid);
    }

    /**
     * Test invalid draftitemid.
     */
    public function test_execute_invalid_draftitemid(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $this->expectException('moodle_exception');
        update_private_files::execute(0);
    }

    /**
     * Test quota reached.
     */
    public function test_execute_quota_reached(): void {
        global $CFG;
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);
        $context = \context_user::instance($user->id);

        // Force the quota so we are sure it won't be space to add the new file.
        $fileareainfo = file_get_file_area_info($context->id, 'user', 'private');
        $CFG->userquota = 1;

        $result = prepare_private_files_for_edition::execute();
        $result = external_api::clean_returnvalue(prepare_private_files_for_edition::execute_returns(), $result);
        $draftitemid = $result['draftitemid'];
        $filerecordinline = [
            'contextid' => $context->id,
            'component' => 'user',
            'filearea'  => 'draft',
            'itemid'    => $draftitemid,
            'filepath'  => '/',
            'filename'  => 'faketxt.txt',
        ];
        $fs = get_file_storage();
        $fs->create_file_from_string($filerecordinline, 'new fake txt contents.');

        $result = update_private_files::execute($draftitemid);
        $result = external_api::clean_returnvalue(update_private_files::execute_returns(), $result);
        // We should get a warning as because of quota we can add files.
        $this->assertFalse($result['status']);
        $this->assertNotEmpty($result['warnings']);
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
        $result = update_private_files::execute(0);
    }
}
