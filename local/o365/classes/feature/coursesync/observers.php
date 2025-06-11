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
 * Observer functions used in the group / team sync feature.
 *
 * @package local_o365
 * @author James McQuillan <james.mcquillan@remote-learner.net>
 * @author Lai Wei <lai.wei@enovation.ie>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2014 onwards Microsoft, Inc. (http://microsoft.com/)
 */

namespace local_o365\feature\coursesync;

use core\event\course_reset_started;
use moodle_exception;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/local/o365/lib.php');

/**
 * A class defining the observer functions used in the group / team sync feature.
 */
class observers {
    /**
     * Observer function that listens for course_reset_started event.
     * Perform Team/group reset actions if configured.
     *
     * @param course_reset_started $event
     * @return bool
     */
    public static function handle_course_reset_started(course_reset_started $event): bool {
        global $CFG, $DB;

        if (!\local_o365\utils::is_connected()) {
            return false;
        }

        if (!utils::is_enabled()) {
            return false;
        }

        $eventdata = $event->get_data();
        $courseid = $eventdata['courseid'];

        if (!$course = $DB->get_record('course', ['id' => $courseid])) {
            return false;
        }

        if (!utils::is_course_sync_enabled($courseid)) {
            return false;
        }

        $apiclient = utils::get_unified_api(__METHOD__);
        if (empty($apiclient)) {
            return false;
        }
        $coursesyncmain = new main($apiclient, false);

        $connectedtoteam = false;
        if (utils::is_course_sync_enabled($courseid)) {
            if (!$o365object =
                $DB->get_record('local_o365_objects', ['type' => 'group', 'subtype' => 'course', 'moodleid' => $courseid])) {
                return false;
            } else {
                // Check if team exists.
                try {
                    [$teamresponse, $teamurl, $lockstatus] = $apiclient->get_team($o365object->objectid);
                    if ($teamresponse) {
                        $connectedtoteam = true;
                    }
                } catch (moodle_exception $e) {
                    // Do nothing.
                    $connectedtoteam = false;
                }
            }
        } else {
            return false;
        }

        // All validation passed. Start processing.
        $siteresetsetting = get_config('local_o365', 'course_reset_teams');
        $coursesyncsetting = get_config('local_o365', 'coursesync');

        switch ($coursesyncsetting) {
            case 'off':
                $siteresetsetting = COURSE_SYNC_RESET_SITE_SETTING_DISCONNECT_ONLY;
                break;
            case 'onall':
                if ($siteresetsetting == COURSE_SYNC_RESET_SITE_SETTING_DISCONNECT_ONLY) {
                    $siteresetsetting = COURSE_SYNC_RESET_SITE_SETTING_DISCONNECT_AND_CREATE_NEW;
                }
                break;
        }

        switch ($siteresetsetting) {
            case COURSE_SYNC_RESET_SITE_SETTING_PER_COURSE:
                // Check course settings.
                if ($DB->record_exists('config_plugins', ['plugin' => 'block_microsoft', 'name' => 'version'])) {
                    // Plugin found.
                    if (file_exists($CFG->dirroot . '/blocks/microsoft/lib.php')) {
                        require_once($CFG->dirroot . '/blocks/microsoft/lib.php');
                        $courseresetsetting = block_microsoft_get_course_reset_setting($courseid);

                        switch ($coursesyncsetting) {
                            case 'off':
                                $courseresetsetting = COURSE_SYNC_RESET_COURSE_SETTING_DISCONNECT_ONLY;
                                break;
                            case 'onall':
                                if ($courseresetsetting == COURSE_SYNC_RESET_COURSE_SETTING_DISCONNECT_ONLY) {
                                    $courseresetsetting = COURSE_SYNC_RESET_COURSE_SETTING_DISCONNECT_AND_CREATE_NEW;
                                }
                                break;
                        }
                        switch ($courseresetsetting) {
                            case COURSE_SYNC_RESET_COURSE_SETTING_DISCONNECT_AND_CREATE_NEW:
                                $coursesyncmain->process_course_reset($course, $o365object, $connectedtoteam);
                                break;
                            case COURSE_SYNC_RESET_COURSE_SETTING_DISCONNECT_ONLY:
                                $coursesyncmain->process_course_reset($course, $o365object, $connectedtoteam, false);
                                utils::set_course_sync_enabled($courseid, false);
                                break;
                        }
                    }
                }

                break;
            case COURSE_SYNC_RESET_SITE_SETTING_DISCONNECT_AND_CREATE_NEW:
                $coursesyncmain->process_course_reset($course, $o365object, $connectedtoteam);

                break;
            case COURSE_SYNC_RESET_SITE_SETTING_DISCONNECT_ONLY:
                $coursesyncmain->process_course_reset($course, $o365object, $connectedtoteam, false);
                utils::set_course_sync_enabled($courseid, false);

                break;
        }

        return true;
    }
}
