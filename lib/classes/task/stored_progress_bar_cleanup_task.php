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

namespace core\task;

/**
 * Scheduled task to clean up old stored_progress bar records.
 *
 * @package    core
 * @copyright  2023 onwards Catalyst IT {@link http://www.catalyst-eu.net/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Conn Warwicker <conn.warwicker@catalyst-eu.net>
 */
class stored_progress_bar_cleanup_task extends scheduled_task {

    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name() {
        return get_string('storedprogressbarcleanuptask', 'admin');
    }

    /**
     * Delete all the old stored progress bar records.
     * By default this runs once per day at 1AM.
     *
     * @return void
     */
    public function execute(): void {
        global $DB;

        $twentyfourhoursago = time() - DAYSECS;

        $DB->delete_records_select('stored_progress', 'lastupdate < :ago', ['ago' => $twentyfourhoursago]);

        mtrace('Deleted old stored_progress records');
    }
}
