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
 * Do convert plan related set up
 */
class convert_create_and_clean_temp_stuff extends convert_execution_step {

    protected function define_execution() {
        backup_controller_dbops::create_backup_ids_temp_table($this->get_convertid()); // Create ids temp table
    }
}

/**
 * Do convert plan related tear down
 */
class convert_drop_and_clean_temp_stuff extends convert_execution_step {

    protected function define_execution() {
        // We want to run after execution
    }

    public function execute_after_convert() {
        backup_controller_dbops::drop_backup_ids_temp_table($this->get_convertid()); // Drop ids temp table
    }


}
