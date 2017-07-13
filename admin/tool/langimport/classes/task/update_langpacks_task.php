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
 * A scheduled task for updating langpacks.
 *
 * @package    tool_langimport
 * @copyright  2014 Dan Poltawski <dan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_langimport\task;

/**
 * A scheduled task for updating langpacks.
 *
 * @package    tool_langimport
 * @copyright  2014 Dan Poltawski <dan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class update_langpacks_task extends \core\task\scheduled_task {

    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name() {
        return get_string('updatelangs', 'tool_langimport');
    }

    /**
     * Run langpack update
     */
    public function execute() {
        global $CFG;

        if (!empty($CFG->skiplangupgrade)) {
            mtrace('Langpack update skipped. ($CFG->skiplangupgrade set)');

            return;
        }

        $controller = new \tool_langimport\controller();
        if ($controller->update_all_installed_languages()) {
            foreach ($controller->info as $message) {
                mtrace($message);
            }
            return true;
        } else {
            foreach ($controller->errors as $message) {
                mtrace($message);
            }
            return false;
        }

    }

}
