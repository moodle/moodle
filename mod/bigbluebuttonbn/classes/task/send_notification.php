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

namespace mod_bigbluebuttonbn\task;
use core\task\adhoc_task;

/**
 * Class containing the deprecated class for send_notification event in BBB.
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2021 Andrew Lyons <andrew@nicols.co.uk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class send_notification extends adhoc_task {
    /**
     * Execute the task.
     */
    public function execute() {
        // Log the debug message.
        $message = $this->generate_message();
        debugging($message, DEBUG_DEVELOPER);
    }

    /**
     * Output the debug log message.
     *
     * @return string The debug log message.
     */
    public function generate_message() {
        return "Attempted to run deprecated implementation of send_notification task.";
    }
}
