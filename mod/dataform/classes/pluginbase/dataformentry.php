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

namespace mod_dataform\pluginbase;

/**
 * @package dataformentry
 * @copyright 2013 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class dataformentry {

    /**
     *
     * @return stdClass
     */
    public static function blank_instance($df, $data = null) {
        global $USER, $CFG;

        $currentuserid = empty($USER->id) ? $CFG->siteguest : $USER->id;
        $now = time();

        $entry = new \stdClass;
        $entry->dataid = $df->id;
        $entry->userid = !empty($data->userid) ? $data->userid : $currentuserid;
        $entry->groupid = !empty($data->groupid) ? $data->groupid : $df->currentgroup;
        $entry->timecreated = !empty($data->timecreated) ? $data->timecreated : $now;
        $entry->timemodified = !empty($data->timemodified) ? $data->timemodified : $now;
        $entry->state = !empty($data->state) ? $data->state : 0;

        return $entry;
    }

    /**
     *
     * @return bool
     */
    public static function is_own($entry, $userid = null) {
        global $USER;

        if (!isloggedin() or isguestuser()) {
            return false;
        }

        if (empty($entry->userid)) {
            return false;
        }

        if (empty($userid)) {
            $userid = $USER->id;
        }
        return ($entry->userid == $userid);
    }

    /**
     *
     * @return bool
     */
    public static function is_grouped($entry) {
        return !empty($entry->groupid);
    }

    /**
     *
     * @return bool
     */
    public static function is_anonymous($entry) {
        global $CFG;

        // Call isguestuser to make sure the $CFG->siteguest is set.
        isguestuser();
        if (!empty($entry->userid) and $entry->userid == $CFG->siteguest) {
            return true;
        }

        return (empty($entry->userid) and empty($entry->groupid));
    }

    /**
     *
     * @return bool
     */
    public static function is_others($entry, $userid = null) {
        global $USER;

        if (empty($entry->userid)) {
            return true;
        }

        if (empty($USER->id)) {
            return true;
        }

        if (empty($userid)) {
            $userid = $USER->id;
        }
        return ($entry->userid != $userid);
    }
}
