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
 * Defines the task which looks for H5P updates.
 *
 * @package    mod_hvp
 * @copyright  2016 Joubel AS <contact@joubel.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_hvp\task;

defined('MOODLE_INTERNAL') || die();

/**
 * The mod_hvp look for updates task class
 *
 * @package    mod_hvp
 * @copyright  2016 Joubel AS <contact@joubel.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class remove_old_log_entries extends \core\task\scheduled_task {
    public function get_name() {
        return get_string('removeoldlogentries', 'mod_hvp');
    }

    public function execute() {
        global $DB;

        require_once(__DIR__ . '/../../autoloader.php');
        // @codingStandardsIgnoreLine
        $olderthan = (time() - \H5PEventBase::$log_time);
        $DB->execute("DELETE FROM {hvp_events} WHERE created_at < {$olderthan}");
    }
}
