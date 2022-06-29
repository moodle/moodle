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
 * Adhoc task handling fixing of events that have had their userid lost.
 *
 * @package    core
 * @copyright  2021 onwards Simey Lameze <simey@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\task;

defined('MOODLE_INTERNAL') || die();

/**
 * Class handling fixing of events that have had their userid lost.
 *
 * @package    core
 * @copyright  2021 onwards Simey Lameze <simey@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class calendar_fix_orphaned_events extends adhoc_task {

    /**
     * Run the task to recover the correct userid from the event.
     *
     * If the maximum number of records are updated, the task re-queues itself,
     * as there may be more events to be fixed.
     */
    public function execute() {

        // Check for problematic upgrade steps and fix orphaned records.
        if ($this->update_events_wrong_userid_remaining()) {
            // There are orphaned events to be fixed.
            // The task will re-queue itself until all orphaned calendar events have been fixed.
            \core\task\manager::queue_adhoc_task(new calendar_fix_orphaned_events());
        }
    }

    /**
     * Execute the recovery of events that have been set with userid to zero.
     *
     * @return bool Whether there are more events to be fixed.
     */
    protected function update_events_wrong_userid_remaining(): bool {
        global $CFG;

        require_once($CFG->libdir . '/db/upgradelib.php');

        // Default the max runtime to 60 seconds, unless overridden in config.php.
        $maxseconds = $CFG->calendareventsmaxseconds ?? MINSECS;

        // Orphaned events found, get those events so it can be recovered.
        $eventsinfo = upgrade_calendar_events_status();

        // Fix the orphaned events and returns if there are more events to be fixed.
        return upgrade_calendar_events_fix_remaining($eventsinfo, true, $maxseconds);
    }
}
