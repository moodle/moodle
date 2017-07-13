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
 * Event observer.
 *
 * @package    block_recent_activity
 * @copyright  2014 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Event observer.
 * Stores all actions about modules create/update/delete in plugin own's table.
 * This allows the block to avoid expensive queries to the log table.
 *
 * @package    block_recent_activity
 * @copyright  2014 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_recent_activity_observer {

    /** @var int indicates that course module was created */
    const CM_CREATED = 0;
    /** @var int indicates that course module was udpated */
    const CM_UPDATED = 1;
    /** @var int indicates that course module was deleted */
    const CM_DELETED = 2;

    /**
     * Store all actions about modules create/update/delete in own table.
     *
     * @param \core\event\base $event
     */
    public static function store(\core\event\base $event) {
        global $DB;
        $eventdata = new \stdClass();
        switch ($event->eventname) {
            case '\core\event\course_module_created':
                $eventdata->action = self::CM_CREATED;
                break;
            case '\core\event\course_module_updated':
                $eventdata->action = self::CM_UPDATED;
                break;
            case '\core\event\course_module_deleted':
                $eventdata->action = self::CM_DELETED;
                $eventdata->modname = $event->other['modulename'];
                break;
            default:
                return;
        }
        $eventdata->timecreated = $event->timecreated;
        $eventdata->courseid = $event->courseid;
        $eventdata->cmid = $event->objectid;
        $eventdata->userid = $event->userid;
        $DB->insert_record('block_recent_activity', $eventdata);
    }
}
