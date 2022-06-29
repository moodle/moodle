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
 * Clean up task for core antivirus
 *
 * @package    core_antivirus
 * @author     Nathan Nguyen <nathannguyen@catalyst-au.net>
 * @copyright  Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\task;

defined('MOODLE_INTERNAL') || die();

/**
 * Clean up task for core antivirus
 *
 * @package    core_antivirus
 * @author     Nathan Nguyen <nathannguyen@catalyst-au.net>
 * @copyright  Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class antivirus_cleanup_task extends scheduled_task {

    /**
     * Get a descriptive name for this task.
     *
     * @return string
     */
    public function get_name() {
        return get_string('taskcleanup', 'antivirus');
    }

    /**
     * Processes workflows.
     */
    public function execute() {
        $quarantinetime = get_config('antivirus', 'quarantinetime');
        if (empty($quarantinetime)) {
            $quarantinetime = \core\antivirus\quarantine::DEFAULT_QUARANTINE_TIME;
            set_config('quarantinetime', $quarantinetime, 'antivirus');
        }
        $timetocleanup = time() - $quarantinetime;
        \core\antivirus\quarantine::clean_up_quarantine_folder($timetocleanup);
    }

}
