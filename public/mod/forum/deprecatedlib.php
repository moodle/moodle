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
 * @package   mod_forum
 * @copyright 2014 Andrew Robert Nicols <andrew@nicols.co.uk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * This function prints the overview of a discussion in the forum listing.
 * It needs some discussion information and some post information, these
 * happen to be combined for efficiency in the $post parameter by the function
 * that calls this one: forum_print_latest_discussions()
 *
 * @deprecated since Moodle 4.3
 */
function forum_print_discussion_header() {
    throw new \coding_exception('forum_print_discussion_header has been deprecated');
}

/**
 * Get a list of forums not tracked by the user.
 *
 * @param int $userid The id of the user to use.
 * @param int $courseid The id of the course being checked.
 * @return mixed An array indexed by forum id, or false.
 * @deprecated since Moodle 5.1
 * @todo MDL-78076 This function will be deleted in Moodle 6.0.
 */
#[\core\attribute\deprecated(
    replacement: null,
    reason: 'It is no longer used',
    since: '5.1',
    mdl: 'MDL-83893',
)]
function forum_tp_get_untracked_forums($userid, $courseid) {
    global $CFG, $DB;

    \core\deprecation::emit_deprecation(__FUNCTION__);

    if ($CFG->forum_allowforcedreadtracking) {
        $trackingsql = "AND (f.trackingtype = ".FORUM_TRACKING_OFF."
                            OR (f.trackingtype = ".FORUM_TRACKING_OPTIONAL." AND (ft.id IS NOT NULL
                                OR (SELECT trackforums FROM {user} WHERE id = ?) = 0)))";
    } else {
        $trackingsql = "AND (f.trackingtype = ".FORUM_TRACKING_OFF."
                            OR ((f.trackingtype = ".FORUM_TRACKING_OPTIONAL." OR f.trackingtype = ".FORUM_TRACKING_FORCED.")
                                AND (ft.id IS NOT NULL
                                    OR (SELECT trackforums FROM {user} WHERE id = ?) = 0)))";
    }

    $sql = "SELECT f.id
              FROM {forum} f
                   LEFT JOIN {forum_track_prefs} ft ON (ft.forumid = f.id AND ft.userid = ?)
             WHERE f.course = ?
                   $trackingsql";

    if ($forums = $DB->get_records_sql($sql, [$userid, $courseid, $userid])) {
        foreach ($forums as $forum) {
            $forums[$forum->id] = $forum;
        }
        return $forums;

    } else {
        return [];
    }
}
