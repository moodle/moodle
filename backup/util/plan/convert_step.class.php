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
 * @package    core
 * @subpackage backup-convert
 * @copyright  2011 Mark Nielsen <mark@moodlerooms.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Convert step
 *
 * @throws backup_exception
 */
abstract class convert_step extends base_step {

    public function __construct($name, convert_task $task = null) {
        parent::__construct($name, $task);
    }

    protected function get_convertid() {
        if (!$this->task instanceof convert_task) {
            throw new backup_exception('not_specified_convert_task'); // @todo Define string
        }
        return $this->task->get_convertid();
    }

    /**
     * @throws backup_exception
     * @return plan_converter
     */
    protected function get_converter() {
        if (!$this->task instanceof convert_task) {
            throw new backup_exception('not_specified_convert_task'); // @todo Define string
        }
        return $this->task->get_converter();
    }

    public function execute_after_convert() {
        // Default nothing
    }
}
