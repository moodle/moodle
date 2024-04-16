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
use backup_controller;
use backup_section_structure_step;
use backup_section_task;

/**
 * Tests for Moodle 2 steplib classes.
 *
 * @package core_backup
 * @copyright 2023 Ferran Recio <ferran@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backup_stepslib_test extends \advanced_testcase {
    /**
     * Setup to include all libraries.
     */
    public static function setUpBeforeClass(): void {
        global $CFG;
        require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');
        require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');
        require_once($CFG->dirroot . '/backup/moodle2/backup_stepslib.php');
    }

    /**
     * Test for the section structure step included elements.
     *
     * @covers \backup_section_structure_step::define_structure
     */
    public function test_backup_section_structure_step(): void {
        global $USER;

        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course(['numsections' => 3, 'format' => 'topics']);
        $this->setAdminUser();

        $step = new backup_section_structure_step('section_commons', 'section.xml');

        // The backup_section_structure_step requires a complex dependency sequence
        // but it does not have an easy dependency injection system.
        // We create a real backup plan to get the task dependency sequence ready.
        $bc = new backup_controller(
            backup::TYPE_1COURSE,
            $course->id,
            backup::FORMAT_MOODLE,
            backup::INTERACTIVE_NO,
            backup::MODE_IMPORT,
            $USER->id);
        $tasks = $bc->get_plan()->get_tasks();
        foreach ($tasks as $task) {
            // We need only the task to backup section 1.
            if ($task instanceof backup_section_task && $task->get_name() == "1") {
                $task->add_step($step);
                break;
            }
        }

        $reflection = new \ReflectionClass($step);
        $method = $reflection->getMethod('define_structure');
        $structure = $method->invoke($step);
        $bc->destroy();

        $elements = $structure->get_final_elements();
        $this->assertArrayHasKey('number', $elements);
        $this->assertArrayHasKey('name', $elements);
        $this->assertArrayHasKey('summary', $elements);
        $this->assertArrayHasKey('summaryformat', $elements);
        $this->assertArrayHasKey('sequence', $elements);
        $this->assertArrayHasKey('visible', $elements);
        $this->assertArrayHasKey('availabilityjson', $elements);
        $this->assertArrayHasKey('component', $elements);
        $this->assertArrayHasKey('itemid', $elements);
        $this->assertArrayHasKey('timemodified', $elements);
    }
}
