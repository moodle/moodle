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

namespace core_backup\hook;

use restore_root_task;

/**
 * Hook to allow extra settings to be defined for the course restore process.
 *
 * @package core_backup
 * @copyright 2024 Monash University (https://www.monash.edu)
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\core\attribute\label('Use to add extra elements to the settings tab of the restore process.')]
#[\core\attribute\tags('backup')]
final class after_restore_root_define_settings {

    /** @var restore_root_task */
    public readonly restore_root_task $task;

    /**
     * Constructor.
     *
     * @param restore_root_task $task
     */
    public function __construct(restore_root_task $task) {
        $this->task = $task;
    }
}
