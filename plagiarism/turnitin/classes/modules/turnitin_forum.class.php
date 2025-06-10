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
 * @package   plagiarism_turnitin
 * @copyright 2012 iParadigms LLC *
 */

defined('MOODLE_INTERNAL') || die();

// TODO: Split out all module specific code from plagiarism/turnitin/lib.php.
class turnitin_forum {

    private $modname;
    public $gradestable;
    public $filecomponent;

    public function __construct() {
        $this->modname = 'forum';
        $this->gradestable = 'grade_grades';
        $this->filecomponent = 'mod_'.$this->modname;
    }

    public function is_tutor($context) {
        return has_capability($this->get_tutor_capability(), $context);
    }

    public function get_tutor_capability() {
        return 'plagiarism/turnitin:viewfullreport';
    }

    public function user_enrolled_on_course($context, $userid) {
        return has_capability('mod/'.$this->modname.':replypost', $context, $userid);
    }

    public function get_author($itemid = 0) {
        return;
    }

    public function set_content($linkarray) {
        global $DB;

        if (empty($linkarray['postid'])) {
            return $linkarray["content"];
        } else {
            $post = $DB->get_record('forum_posts', array('id' => $linkarray['postid']));
            return $post->message;
        }
    }

    public function create_file_event($params) {
        return \mod_forum\event\assessable_uploaded::create($params);
    }

    public function get_current_gradequery($userid, $moduleid, $itemid = 0) {
        global $DB;

        $currentgradequery = $DB->get_record('grade_grades', array('userid' => $userid, 'itemid' => $itemid));
        return $currentgradequery;
    }

    public function initialise_post_date($moduledata) {
        return 0;
    }

    // Get the forum submission id - unfortunately this is rather complex as the db tables are strangely organised.
    public function get_discussionid($forumdata) {

        list($querystrid, $discussionid, $reply, $edit, $delete) = explode('_', $forumdata);

        if (empty($discussionid)) {
            $parent = '';
            if ($reply != 0) {
                $parent = forum_get_post_full($reply);
            } else if ($edit != 0) {
                $parent = forum_get_post_full($edit);
            } else if ($delete != 0) {
                $parent = forum_get_post_full($delete);
            }

            if (!empty($parent)) {
                $discussionid = $parent->discussion;
            }
        }

        return $discussionid;
    }
}