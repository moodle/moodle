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
 * @package dataformfield
 * @subpackage commentmdl
 * @copyright 2012 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("$CFG->dirroot/comment/lib.php");

class dataformfield_commentmdl_commentmdl extends mod_dataform\pluginbase\dataformfield_nocontent {
    /**
     * Override parent to adjust comment records
     */
    public function update($data) {
        global $DB, $OUTPUT;

        $oldname = $this->name;
        if (parent::update($data)) {
            // Adjust comment area in comment records.
            if ($oldname != $this->name) {
                $context = $this->df->context;
                if ($comments = $DB->get_records('comments', array('contextid' => $context->id, 'commentarea' => $oldname))) {
                    foreach ($comments as $comment) {
                        $DB->set_field('comments', 'commentarea', $this->name, array('id' => $comment->id));
                    }
                }
            }
        }

        return true;
    }

    /**
     * Delete a field completely
     */
    public function delete() {
        // Delete the comments.
        $params = array(
            'contextid' => $this->df->context->id,
            'commentarea' => $this->name
        );
        comment::delete_comments($params);

        return parent::delete();
    }

    /**
     *
     */
    public function get_sort_sql($element = null) {
        return '';
    }

    /**
     *
     */
    public function permissions($params) {
        if (has_capability('mod/dataform:managecomments', $this->df->context) or ($params->commentarea == $this->name)) {
            return array('post' => true, 'view' => true);
        }
        return array('post' => false, 'view' => false);
    }

    /**
     *
     */
    public function validation($params) {
        global $DB, $USER;

        // Validate context.
        if (empty($params->context) or $params->context->id != $this->df->context->id) {
            throw new comment_exception('invalidcontextid', 'dataform');
        }

        // Validate course.
        if ($params->courseid != $this->df->course->id) {
            throw new comment_exception('invalidcourseid', 'dataform');
        }

        // Validate cm.
        if ($params->cm->id != $this->df->cm->id) {
            throw new comment_exception('invalidcmid', 'dataform');
        }

        // Validate comment area
        // if ($params->commentarea != $this->name) {
            // throw new comment_exception('invalidcommentarea');
        // }.

        // Validation for non-comment-managers.
        if (!has_capability('mod/dataform:managecomments', $this->df->context)) {

            // Non-comment-managers can add/view comments on their own entries
            // but require df->comments for add/view on other entries (excluding grading entries).

            // Validate entry.
            if (!$entry = $DB->get_record('dataform_entries', array('id' => $params->itemid))) {
                throw new comment_exception('invalidcommentitemid');
            }

            // Group access.
            if ($entry->groupid) {
                $groupmode = groups_get_activity_groupmode($this->df->cm, $this->df->course);
                if ($groupmode == SEPARATEGROUPS and !has_capability('moodle/site:accessallgroups', $this->df->context)) {
                    if (!groups_is_member($entry->groupid)) {
                        throw new comment_exception('notmemberofgroup');
                    }
                }
            }
        }

        // Validation for comment deletion.
        if (!empty($params->commentid)) {
            if ($comment = $DB->get_record('comments', array('id' => $params->commentid))) {
                if ($comment->commentarea != $this->name) {
                    throw new comment_exception('invalidcommentarea');
                }
                if ($comment->contextid != $params->context->id) {
                    throw new comment_exception('invalidcontext');
                }
                if ($comment->itemid != $params->itemid) {
                    throw new comment_exception('invalidcommentitemid');
                }
            } else {
                throw new comment_exception('invalidcommentid');
            }
        }

        return true;
    }

    /**
     * Overriding parent to return no sort/search options.
     *
     * @return array
     */
    public function get_sort_options_menu() {
        return array();
    }
}
