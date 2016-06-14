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
 * This file contains the functions for managing a users comments quicklist.
 *
 * @package   assignfeedback_editpdf
 * @copyright 2012 Davo Smith
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace assignfeedback_editpdf;

/**
 * This class performs crud operations on a users quicklist comments.
 *
 * No capability checks are done - they should be done by the calling class.
 * @package   assignfeedback_editpdf
 * @copyright 2012 Davo Smith
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class comments_quick_list {

    /**
     * Get all comments for the current user.
     * @return array(comment)
     */
    public static function get_comments() {
        global $DB, $USER;

        $comments = array();
        $records = $DB->get_records('assignfeedback_editpdf_quick', array('userid'=>$USER->id));

        return $records;
    }

    /**
     * Add a comment to the quick list.
     * @param string $commenttext
     * @param int $width
     * @param string $colour
     * @return stdClass - the comment record (with new id set)
     */
    public static function add_comment($commenttext, $width, $colour) {
        global $DB, $USER;

        $comment = new \stdClass();
        $comment->userid = $USER->id;
        $comment->rawtext = $commenttext;
        $comment->width = $width;
        $comment->colour = $colour;

        $comment->id = $DB->insert_record('assignfeedback_editpdf_quick', $comment);
        return $comment;
    }

    /**
     * Get a single comment by id.
     * @param int $commentid
     * @return comment or false
     */
    public static function get_comment($commentid) {
        global $DB;

        $record = $DB->get_record('assignfeedback_editpdf_quick', array('id'=>$commentid), '*', IGNORE_MISSING);
        if ($record) {
            return $record;
        }
        return false;
    }

    /**
     * Remove a comment from the quick list.
     * @param int $commentid
     * @return bool
     */
    public static function remove_comment($commentid) {
        global $DB, $USER;
        return $DB->delete_records('assignfeedback_editpdf_quick', array('id'=>$commentid, 'userid'=>$USER->id));
    }
}
