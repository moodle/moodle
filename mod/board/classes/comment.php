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
 * The board comments class functions.
 * @package     mod_board
 * @author      Bas Brands <bas@sonsbeekmedia.nl>
 * @copyright   2022 Brickfield Education Labs <https://www.brickfield.ie/>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_board;

/**
 * Define comment class.
 */
class comment {
    /**
     * @var $id
     */
    public $id = 0;

    /**
     * @var $noteid
     */
    public $noteid = 0;

    /**
     * @var $content
     */
    public $content = '';

    /**
     * @var $userid
     */
    public $userid = 0;

    /**
     * @var $timecreated
     */
    public $courseid = 0;

    /**
     * @var $timecreated
     */
    public $timecreated = 0;

    /**
     * @var $timemodified
     */
    public $timemodified = 0;

    /**
     * @var $context
     */
    private $context;

    /**
     * Create a comment.
     *
     * @param array $attrs parameter for creating a comment indexed by attriute names.
     */
    public function __construct($attrs = array()) {
        global $DB, $USER;

        $commentid = $attrs['commentid'];
        if (isset($commentid)) {

            $comment = $DB->get_record('board_comments', ['id' => $commentid], '*', MUST_EXIST);
            if ($comment) {
                $this->id = $comment->id;
                $this->noteid = $comment->noteid;
                $this->content = $comment->content;
                $this->userid = $comment->userid;
                $this->courseid = $comment->courseid;
            }
        }

        foreach ($attrs as $key => $value) {
            $this->$key = $value;
        }

        if (empty($this->timecreated)) {
            $this->timecreated = time();
        }

        if (empty($this->userid)) {
            $this->userid = $USER->id;
        }
        $this->get_context();
    }

    /**
     * Get the context this comment is posted.
     *
     * @return \context
     */
    public function get_context() {
        global $DB;

        if (isset($this->context)) {
            return $this->context;
        }

        $note = $DB->get_record('board_notes', array('id' => $this->noteid), '*', MUST_EXIST);
        $column = $DB->get_record('board_columns', array('id' => $note->columnid), '*', MUST_EXIST);
        $board = $DB->get_record('board', array('id' => $column->boardid), '*', MUST_EXIST);

        $cm = get_coursemodule_from_instance('board', $board->id, $board->course, false, MUST_EXIST);

        $this->context = \context_module::instance($cm->id);
        return $this->context;
    }

    /**
     * Checks if the user is allowed to post a comment on a note.
     *
     * @param context $context the context, the user might post.
     * @return boolean true if user can create a comment.
     */
    public static function can_create($context) {
        global $USER;

        if (!has_capability('mod/board:postcomment', $context)) {
            return false;
        }

        return true;
    }

    /**
     * Checks if the user is allowed to delete a comment.
     *
     * @return boolean true if user can delete a comment.
     */
    public function can_delete() {
        global $USER;

        $context = $this->get_context();
        if ($this->userid == $USER->id && has_capability('mod/board:postcomment', $context)) {
            return true;
        }

        if (has_capability('mod/board:deleteallcomments', $context)) {
            return true;
        }

        return false;
    }

    /**
     * Create or update this post.
     *
     * @return \block_socialcomments\local\comment
     */
    public function save() {
        global $DB, $USER;

        $this->timemodified = time();
        $this->content = html_to_text($this->content, 5000, false);

        if ($this->id > 0) {
            $DB->update_record('board_comments', $this);
        } else {
            $this->userid = $USER->id;
            $this->timecreated = $this->timemodified;
            $this->id = $DB->insert_record('board_comments', $this);
        }
        self::board_add_comment_log($this->id, $this->context, $this->noteid, $this->content);
        return $this;
    }

    /**
     * Delete this comment.
     *
     * @return bool true if comment is deleted.
     */
    public function delete() {
        global $DB;

        if (!$this->can_delete()) {
            return false;
        }
        self::board_delete_comment_log($this->id, $this->context, $this->noteid);
        return $DB->update_record('board_comments', ['id' => $this->id, 'deleted' => 1]);
    }

    /**
     * Triggers the add comment log.
     *
     * @param int $commentid
     * @param object $context
     * @param int $noteid
     * @param string $content
     * @return void
     */
    public static function board_add_comment_log($commentid, $context, $noteid, $content) {
        if (!get_config('mod_board', 'addcommenttolog')) {
            $content = '';
        }
        $event = \mod_board\event\add_comment::create(array(
            'objectid' => $commentid,
            'context' => $context,
            'other' => ['noteid' => $noteid, 'content' => $content]
        ));
        $event->trigger();
    }

    /**
     * Triggers the delete comment log.
     *
     * @param int $commentid
     * @param object $context
     * @param int $noteid
     * @return void
     */
    public static function board_delete_comment_log($commentid, $context, $noteid) {
        $event = \mod_board\event\delete_comment::create(array(
            'objectid' => $commentid,
            'context' => $context,
            'other' => ['noteid' => $noteid]
        ));
        $event->trigger();
    }
}
