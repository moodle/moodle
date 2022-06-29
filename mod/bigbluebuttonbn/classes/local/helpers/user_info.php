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
namespace mod_bigbluebuttonbn\local\helpers;

use cm_info;
use mod_bigbluebuttonbn\instance;
use mod_bigbluebuttonbn\logger;
use stdClass;

/**
 * Utility class for all user information
 *
 * Used mainly in user_outline and user_complete
 *
 * @package mod_bigbluebuttonbn
 * @copyright 2022 onwards, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Laurent David  (laurent [at] call-learning [dt] fr)
 */
class user_info {
    /**
     * Event to watch for.
     */
    const EVENT_TO_WATCH = [
        'join' => logger::EVENT_JOIN,
        'play_recording' => logger::EVENT_PLAYED
    ];

    /**
     * Get user outline and complete info
     *
     * @param stdClass $course
     * @param stdClass $user
     * @param cm_info $mod
     * @return array[] an array of infos and timestamps (latest timestamp)
     */
    public static function get_user_info_outline(stdClass $course, stdClass $user, cm_info $mod): array {
        $completion = new \completion_info($course);
        $cdata = $completion->get_data($mod, false, $user->id);
        $logtimestamps = [];
        $infos = [];
        if (!empty($cdata->viewed) && $cdata->viewed) {
            $infos[] = get_string('report_room_view', 'mod_bigbluebuttonbn');
            $logtimestamps[] = $cdata->timemodified;
        }
        $instance = instance::get_from_cmid($mod->id);
        foreach (self::EVENT_TO_WATCH as $eventtype => $logtype) {
            $logs = logger::get_user_completion_logs($instance, $user->id, [$logtype]);
            if ($logs) {
                $infos[] = get_string("report_{$eventtype}_info", 'mod_bigbluebuttonbn', count($logs));
                $latesttime = array_reduce($logs,
                    function($acc, $log) {
                        return ($acc > $log->timecreated) ? $acc : $log->timecreated;
                    }, 0);
                $logtimestamps[] = $latesttime;
            }
        }
        return [$infos, $logtimestamps];
    }
}
