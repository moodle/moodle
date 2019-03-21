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
 * Discussion class.
 *
 * @package    mod_forum
 * @copyright  2019 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_forum\local\entities;

defined('MOODLE_INTERNAL') || die();

use mod_forum\local\entities\post as post_entity;

/**
 * Discussion class.
 *
 * @copyright  2019 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class discussion {
    /** @var int $id ID */
    private $id;
    /** @var int $courseid Course id */
    private $courseid;
    /** @var int $forumid Forum id */
    private $forumid;
    /** @var string $name Discussion name */
    private $name;
    /** @var int $firstpostid Id of the first post in the discussion */
    private $firstpostid;
    /** @var int $userid Id of the user that created the discussion */
    private $userid;
    /** @var int $groupid Group id if it's a group dicussion */
    private $groupid;
    /** @var bool $assessed Is the discussion assessed? */
    private $assessed;
    /** @var int $timemodified Timestamp for last modification to the discussion */
    private $timemodified;
    /** @var int $usermodified Id of user that last modified the discussion */
    private $usermodified;
    /** @var int $timestart Start time for the discussion */
    private $timestart;
    /** @var int $timeend End time for the discussion */
    private $timeend;
    /** @var bool $pinned Is the discussion pinned? */
    private $pinned;

    /**
     * Constructor.
     *
     * @param int $id ID
     * @param int $courseid Course id
     * @param int $forumid Forum id
     * @param string $name Discussion name
     * @param int $firstpostid Id of the first post in the discussion
     * @param int $userid Id of the user that created the discussion
     * @param int $groupid Group id if it's a group dicussion
     * @param bool $assessed Is the discussion assessed?
     * @param int $timemodified Timestamp for last modification to the discussion
     * @param int $usermodified Id of user that last modified the discussion
     * @param int $timestart Start time for the discussion
     * @param int $timeend End time for the discussion
     * @param bool $pinned Is the discussion pinned?
     */
    public function __construct(
        int $id,
        int $courseid,
        int $forumid,
        string $name,
        int $firstpostid,
        int $userid,
        int $groupid,
        bool $assessed,
        int $timemodified,
        int $usermodified,
        int $timestart,
        int $timeend,
        bool $pinned
    ) {
        $this->id = $id;
        $this->courseid = $courseid;
        $this->forumid = $forumid;
        $this->name = $name;
        $this->firstpostid = $firstpostid;
        $this->userid = $userid;
        $this->groupid = $groupid;
        $this->assessed = $assessed;
        $this->timemodified = $timemodified;
        $this->usermodified = $usermodified;
        $this->timestart = $timestart;
        $this->timeend = $timeend;
        $this->pinned = $pinned;
    }

    /**
     * Get the discussion id.
     *
     * @return int
     */
    public function get_id() : int {
        return $this->id;
    }

    /**
     * Get the course id.
     *
     * @return int
     */
    public function get_course_id() : int {
        return $this->courseid;
    }

    /**
     * Get the forum id.
     *
     * @return int
     */
    public function get_forum_id() : int {
        return $this->forumid;
    }

    /**
     * Get the name of the discussion.
     *
     * @return string
     */
    public function get_name() : string {
        return $this->name;
    }

    /**
     * Get the id of the fist post in the discussion.
     *
     * @return int
     */
    public function get_first_post_id() : int {
        return $this->firstpostid;
    }

    /**
     * Get the id of the user that created the discussion.
     *
     * @return int
     */
    public function get_user_id() : int {
        return $this->userid;
    }

    /**
     * Get the id of the group that this discussion belongs to.
     *
     * @return int
     */
    public function get_group_id() : int {
        return $this->groupid;
    }

    /**
     * Check if this discussion is assessed.
     *
     * @return bool
     */
    public function is_assessed() : bool {
        return $this->assessed;
    }

    /**
     * Get the timestamp for when this discussion was last modified.
     *
     * @return int
     */
    public function get_time_modified() : int {
        return $this->timemodified;
    }

    /**
     * Get the id of the user that last modified this discussion.
     *
     * @return int
     */
    public function get_user_modified() : int {
        return $this->usermodified;
    }

    /**
     * Get the start time of this discussion. Returns zero if the discussion
     * has no designated start time.
     *
     * @return int
     */
    public function get_time_start() : int {
        return $this->timestart;
    }

    /**
     * Get the end time of this discussion. Returns zero if the discussion
     * has no designated end time.
     *
     * @return int
     */
    public function get_time_end() : int {
        return $this->timeend;
    }

    /**
     * Check if this discussion is pinned.
     *
     * @return bool
     */
    public function is_pinned() : bool {
        return $this->pinned;
    }

    /**
     * Check if the given post is the first post in this discussion.
     *
     * @param post_entity $post The post to check
     * @return bool
     */
    public function is_first_post(post_entity $post) : bool {
        return $this->get_first_post_id() === $post->get_id();
    }

    /**
     * Check if the discussion has started yet.
     *
     * @return bool
     */
    public function has_started() : bool {
        $startime = $this->get_time_start();
        return empty($startime) || $startime < time();
    }

    /**
     * Check if the discussion has ended.
     *
     * @return bool
     */
    public function has_ended() : bool {
        $endtime = $this->get_time_end();
        return !empty($endtime) && $endtime >= time();
    }

    /**
     * Check if the discussion belongs to a group.
     *
     * @return bool
     */
    public function has_group() : bool {
        return $this->get_group_id() > 0;
    }

    /**
     * Check if the discussion is timed.
     *
     * @return bool
     */
    public function is_timed_discussion() : bool {
        global $CFG;

        return !empty($CFG->forum_enabletimedposts) &&
              ($this->get_time_start() || $this->get_time_end());
    }

    /**
     * Check if the timed discussion is visible.
     *
     * @return bool
     */
    public function is_timed_discussion_visible() : bool {
        return !$this->is_timed_discussion() || ($this->has_started() && !$this->has_ended());
    }
}
