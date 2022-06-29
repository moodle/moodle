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
 * Tool task test data generator class
 *
 * @package tool_task
 * @copyright 2020 Mikhail Golenkov <golenkovm@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Tool task test data generator class
 *
 * @package tool_task
 * @copyright 2020 Mikhail Golenkov <golenkovm@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_task_generator extends testing_module_generator {

    /**
     * Mark a scheduled task as running.
     *
     * @param array $data Scheduled task properties
     * @throws dml_exception
     */
    public function create_scheduled_tasks($data) {
        global $DB;
        $conditions = ['classname' => $data['classname']];
        $record = $DB->get_record('task_scheduled', $conditions, '*', MUST_EXIST);
        $record->timestarted = time() - $data['seconds'];
        $record->hostname = $data['hostname'];
        $record->pid = $data['pid'];
        $DB->update_record('task_scheduled', $record);
    }

    /**
     * Mark an adhoc task as running.
     *
     * @param array $data Adhoc task properties
     * @throws dml_exception
     */
    public function create_adhoc_tasks($data) {
        global $DB;
        $adhoctask = (object)[
            'classname' => $data['classname'],
            'nextruntime' => 0,
            'timestarted' => time() - $data['seconds'],
            'hostname' => $data['hostname'],
            'pid' => $data['pid'],
        ];
        $DB->insert_record('task_adhoc', $adhoctask);
    }
}
