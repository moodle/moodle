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
 * Class badges_adhoc_task
 *
 * @package    core
 * @copyright  2023 Jay Oswald <jayoswald@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class badges_adhoc_task extends adhoc_task {

    /**
     * Sets the name of the badges adhoc task
     *
     * @return void
     */
    public function get_name() {
        return get_string('taskbadgesadhoc', 'admin');
    }

    /**
     * Badge adhoc task to assign a single badge
     *
     * @return void
     */
    public function execute() {
        $data = $this->get_custom_data();
        $badge = new \core_badges\badge($data->badgeid);
        $traceprefix = "Badge $data->badgeid: $badge->name: ";

        try {
            if (!$badge->has_criteria()) {
                mtrace("$traceprefix Badge has no criteria to be processed");
                return;
            }

            $issued = $badge->review_all_criteria();
            mtrace("$traceprefix badge was issued to $issued users.");

        } catch (\moodle_exception $e) {
            $badgeeditlink =
                new \moodle_url('/badges/edit.php', ['id' => $data->badgeid, 'action' => 'badge']);

            switch($e->errorcode){
                case 'invalidcoursemoduleid':
                    $badge->set_status(BADGE_STATUS_INACTIVE);
                    mtrace("$traceprefix has invalid course modules, it has been made inactive $badgeeditlink");
                    break;
                default:
                    mtrace("$traceprefix Error: {$e->getMessage()} $badgeeditlink");
                    throw($e);
            }
        }
    }
}
