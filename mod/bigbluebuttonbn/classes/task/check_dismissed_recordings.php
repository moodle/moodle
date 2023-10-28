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

use core\task\scheduled_task;
use mod_bigbluebuttonbn\recording;

/**
 * Synchronise pending and dismissed recordings from the server.
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2022 Laurent David Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class check_dismissed_recordings extends scheduled_task {
    /**
     * Run the migration task.
     */
    public function execute() {
        recording::sync_pending_recordings_from_server(true);
    }

    /**
     * Get the name of the task for use in the interface.
     *
     * @return string
     */
    public function get_name(): string {
        return get_string('taskname:check_dismissed_recordings', 'mod_bigbluebuttonbn');
    }
}
