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

namespace core_question\test;

use PHPUnit\Framework\MockObject\MockObject;

/**
 * Trait for providing mocked question restore objects
 *
 * @package   core_question
 * @copyright 2025 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author    Mark Johnson <mark.johnson@catalyst-eu.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
trait mock_restore_test_trait {
    /**
     * Include restore class dependencies.
     */
    public static function setUpBeforeClass(): void {
        global $CFG;
        require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');
    }

    /**
     * Return a mocked restore_task that will say we are restoring to the same site.
     *
     * @return MockObject The mocked restore_task.
     */
    protected function get_samesite_task(): MockObject {
        $mock = $this->createMock(\restore_task::class);
        $mock->method('is_samesite')->willReturn(true);
        return $mock;
    }

    /**
     * Return a mocked restore_task that will say we are not restoring to the same site.
     *
     * @return MockObject The mocked restore_task.
     */
    protected function get_not_samesite_task(): MockObject {
        $mock = $this->createMock(\restore_task::class);
        $mock->method('is_samesite')->willReturn(false);
        return $mock;
    }

    /**
     * Restore a mocked restore step that will use the provided task.
     *
     * @param MockObject $task The mocked restore_task.
     * @return MockObject The mocked restore_questions_activity_structure_step.
     */
    protected function get_mock_step(MockObject $task): MockObject {
        $mock = $this->createMock(\restore_questions_activity_structure_step::class);
        $mock->method('get_task')->willReturn($task);
        return $mock;
    }
}
