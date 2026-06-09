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

namespace core_backup;

use backup;
use core\di;
use core\hook\manager;
use restore_controller;

/**
 * Tests for Moodle 2 restore steplib classes.
 *
 * @package core_backup
 * @copyright 2023 Ferran Recio <ferran@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class restore_stepslib_test extends \advanced_testcase {
    /**
     * Setup to include all libraries.
     */
    public static function setUpBeforeClass(): void {
        global $CFG;
        require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');
        require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');
        require_once($CFG->dirroot . '/backup/moodle2/restore_stepslib.php');
        parent::setUpBeforeClass();
    }

    /**
     * Makes a backup of the course.
     *
     * @param \stdClass $course The course object.
     * @return string Unique identifier for this backup.
     */
    protected function backup_course(\stdClass $course): string {
        global $CFG, $USER;

        // Turn off file logging, otherwise it can't delete the file (Windows).
        $CFG->backup_file_logger_level = backup::LOG_NONE;

        // Do backup with default settings. MODE_IMPORT means it will just
        // create the directory and not zip it.
        $bc = new \backup_controller(
            backup::TYPE_1COURSE,
            $course->id,
            backup::FORMAT_MOODLE,
            backup::INTERACTIVE_NO,
            backup::MODE_IMPORT,
            $USER->id
        );
        $backupid = $bc->get_backupid();
        $bc->execute_plan();
        $bc->destroy();

        return $backupid;
    }

    /**
     * Restores a backup that has been made earlier.
     *
     * @param string $backupid The unique identifier of the backup.
     * @return int The new course id.
     */
    protected function restore_replacing_content(string $backupid): int {
        global $CFG, $USER;

        // Create course to restore into, and a user to do the restore.
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();

        // Turn off file logging, otherwise it can't delete the file (Windows).
        $CFG->backup_file_logger_level = backup::LOG_NONE;

        // Do restore to new course with default settings.
        $rc = new \restore_controller(
            $backupid,
            $course->id,
            backup::INTERACTIVE_NO,
            backup::MODE_GENERAL,
            $USER->id,
            backup::TARGET_EXISTING_DELETING
        );

        $precheck = $rc->execute_precheck();
        $this->assertTrue($precheck);
        $rc->get_plan()->get_setting('role_assignments')->set_value(true);
        $rc->get_plan()->get_setting('permissions')->set_value(true);
        $rc->execute_plan();
        $rc->destroy();

        return $course->id;
    }

    /**
     * Test for delegate section behaviour.
     *
     * @covers \restore_section_structure_step::process_section
     */
    public function test_restore_section_structure_step(): void {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course(['numsections' => 2, 'format' => 'topics']);
        // Section 2 has an existing delegate class for component that is not an activity.
        course_update_section(
            $course,
            $DB->get_record('course_sections', ['course' => $course->id, 'section' => 2]),
            [
                'component' => 'test_component',
                'itemid' => 1,
            ]
        );

        $backupid = $this->backup_course($course);
        $newcourseid = $this->restore_replacing_content($backupid);

        $originalsections = get_fast_modinfo($course->id)->get_section_info_all();
        $restoredsections = get_fast_modinfo($newcourseid)->get_section_info_all();

        // Delegated sections depends on the plugin to be backuped and restored.
        // In this case, the plugin is not backuped and restored, so the section is not restored.
        $this->assertEquals(3, count($originalsections));
        $this->assertEquals(2, count($restoredsections));

        $validatefields = ['name', 'summary', 'summaryformat', 'visible', 'component', 'itemid'];

        $this->assertEquals($originalsections[1]->name, $restoredsections[1]->name);

        foreach ($validatefields as $field) {
            $this->assertEquals($originalsections[0]->$field, $restoredsections[0]->$field);
            $this->assertEquals($originalsections[1]->$field, $restoredsections[1]->$field);
        }
    }

    /**
     * Tests the hooks for restore task  settings definition.
     *
     * @covers \restore_root_task::define_settings
     */
    public function test_restore_hook(): void {
        // Load the callback classes.
        require_once(__DIR__ . '/fixtures/restore_task_hooks.php');

        $this->resetAfterTest();
        $this->setAdminUser();

        // Replace the version of the manager in the DI container with a phpunit one.
        di::set(
            manager::class,
            manager::phpunit_get_instance([
                // Load a list of hooks for `test_plugin1` from the fixture file.
                'test_plugin1' => __DIR__ .
                    '/fixtures/restore_task_hooks.php',
            ]),
        );

        global $CFG, $USER;

        // Create course to restore into, and a user to do the restore.
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();

        $backupid = $this->backup_course($course);

        // Turn off file logging, otherwise it can't delete the file (Windows).
        $CFG->backup_file_logger_level = backup::LOG_NONE;

        $course = $generator->create_course();

        // Do restore to new course with default settings.
        $rc = new restore_controller(
            $backupid,
            $course->id,
            backup::INTERACTIVE_NO,
            backup::MODE_GENERAL,
            $USER->id,
            backup::TARGET_EXISTING_DELETING
        );

        $precheck = $rc->execute_precheck();
        $this->assertTrue($precheck);
        $setting = $rc->get_plan()->get_setting('extra_test');
        $this->assertNotEmpty($setting);
        $rc->execute_plan();
        $rc->destroy();
    }

    /**
     * Data provider for contenthash values - invalid hashes are skipped, valid hashes proceed to processing.
     *
     * @return array
     */
    public static function contenthash_provider(): array {
        return [
            'Invalid - path traversal' => ['../../../../../../../../../../../../etc/passwd', false],
            'Invalid - uppercase hex'  => ['DA39A3EE5E6B4B0D3255BFEF95601890AFD80709', false],
            'Invalid - too short'      => ['da39a3ee5e6b4b0d3255bfef95601890afd807', false],
            'Invalid - non-hex chars'  => ['zzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzz', false],
            'Valid - lowercase sha1'   => ['da39a3ee5e6b4b0d3255bfef95601890afd80709', true],
            'Empty - directory entry'  => ['', true],
        ];
    }

    /**
     * Test contenthash validation when restoring files.
     *
     * - Invalid contenthash values are rejected with a warning logged and processing stopped.
     * - Valid contenthash values allow processing to continue.
     *
     * @param string $hash The contenthash value to validate.
     * @param bool $isvalid Whether the hash is expected to pass validation.
     * @dataProvider contenthash_provider
     * @covers \restore_load_included_files::process_file
     */
    public function test_process_file_contenthash_validation(string $hash, bool $isvalid): void {
        $step = $this->getMockBuilder(\restore_load_included_files::class)
            ->setConstructorArgs(['test', null])
            ->onlyMethods(['log'])
            ->getMock();

        if ($isvalid) {
            // Valid hash: validation is skipped — no warning log should be emitted.
            // Processing may throw due to the missing restore context; absorb it since
            // only the log() assertion matters here.
            $step->expects($this->never())->method('log');
            try {
                $step->process_file(['contenthash' => $hash]);
            } catch (\Throwable $e) {
                // Absorb any exception caused by missing restore context after validation passes.
            }
        } else {
            // Invalid hash: a LOG_WARNING must be emitted before the early return.
            $step->expects($this->once())
                ->method('log')
                ->with($this->stringContains('Skipping file with invalid contenthash during restore'), backup::LOG_WARNING);
            $step->process_file(['contenthash' => $hash]);
        }
    }
}
