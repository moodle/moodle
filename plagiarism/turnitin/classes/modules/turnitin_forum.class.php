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

// phpcs:disable moodle.Commenting.TodoComment
// TODO: Split out all module specific code from plagiarism/turnitin/lib.php.

/**
 * Class turnitin_forum
 *
 * @package   plagiarism_turnitin
 * @copyright 2012 iParadigms LLC *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class turnitin_forum {

    /**
     * @var string
     */
    private $modname;
    /**
     * @var string
     */
    public $gradestable;
    /**
     * @var string
     */
    public $filecomponent;

    /**
     * The constructor
     */
    public function __construct() {
        $this->modname = 'forum';
        $this->gradestable = 'grade_grades';
        $this->filecomponent = 'mod_'.$this->modname;
    }

    /**
     * Check whether the user is a tutor
     *
     * @param context $context The context
     * @return bool
     * @throws coding_exception
     */
    public function is_tutor($context) {
        return has_capability($this->get_tutor_capability(), $context);
    }

    /**
     * Whether the user has the capability to view the full report
     *
     * @return string
     */
    public function get_tutor_capability() {
        return 'plagiarism/turnitin:viewfullreport';
    }

    /**
     * Whether the user is enrolled on the course and has the capability to reply to posts
     *
     * @param context $context The context
     * @param int $userid The user id
     * @return bool
     * @throws coding_exception
     */
    public function user_enrolled_on_course($context, $userid) {
        return has_capability('mod/'.$this->modname.':replypost', $context, $userid);
    }

    /**
     * Get the author of the forum post
     *
     * @param int $itemid The item id
     * @return void
     */
    public function get_author($itemid = 0) {
        return;
    }

    /**
     * Set the content of the forum post
     *
     * @param array $linkarray The link array
     * @return mixed
     * @throws dml_exception
     */
    public function set_content($linkarray) {
        global $DB;

        if (empty($linkarray['postid'])) {
            return $linkarray["content"];
        } else {
            $post = $DB->get_record('forum_posts', ['id' => $linkarray['postid']]);
            return $post->message;
        }
    }

    /**
     * Create a file event
     *
     * @param array $params The params
     * @return \core\event\base
     * @throws coding_exception
     */
    public function create_file_event($params) {
        return \mod_forum\event\assessable_uploaded::create($params);
    }

    /**
     * Get the current grade query
     *
     * @param int $userid The user id
     * @param int $moduleid The module id
     * @param int $itemid The item id
     * @return false|mixed|stdClass
     * @throws dml_exception
     */
    public function get_current_gradequery($userid, $moduleid, $itemid = 0) {
        global $DB;

        $currentgradequery = $DB->get_record('grade_grades', ['userid' => $userid, 'itemid' => $itemid]);
        return $currentgradequery;
    }

    /**
     * Initialise the post date for the module
     *
     * @param stdClass $moduledata The module data
     * @return int
     */
    public function initialise_post_date($moduledata) {
        return 0;
    }

    // Get the forum submission id - unfortunately this is rather complex as the db tables are strangely organised.

    /**
     * Get the forum discussion id
     *
     * @param string $forumdata The forum data
     * @return string
     */
    public function get_discussionid($forumdata) {
        global $CFG;
        require_once($CFG->dirroot.'/mod/forum/lib.php');

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
