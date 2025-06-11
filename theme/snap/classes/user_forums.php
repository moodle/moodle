<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

namespace theme_snap;

/**
 * Provides information on all forums a user has access to.
 *
 * @package   theme_snap
 * @copyright Copyright (c) 2015 Open LMS (https://www.openlms.net)
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_forums {

    /**
     * @var stdclass
     */
    protected $user;

    /**
     * @var array
     */
    protected $courses = [];

    /**
     * @var array
     */
    protected $forums = [];

    /**
     * @var array
     */
    protected $forumids = [];

    /**
     * @var array
     */
    protected $hsuforums = [];

    /**
     * @var array
     */
    protected $hsuforumids = [];

    /**
     * @var array
     */
    protected $forumidsallgroups = [];

    /**
     * @var array
     */
    protected $hsuforumidsallgroups = [];

    /**
     * @var int
     */
    public static $forumlimit = 100;

    /**
     * @param bool|stdClass|int $userorid
     * @param bool|int $forumlimit
     */
    public function __construct($userorid = false, $forumlimit = false) {
        $this->user = local::get_user($userorid);
        if (empty($this->user) || empty($this->user->id)) {
            throw new \coding_exception('Failed to get user from '.var_export($userorid, true));
        }
        if (!empty($forumlimit)) {
            self::$forumlimit = $forumlimit;
        }
        $this->populate_forums();
    }

    /**
     * @return array
     */
    public function forums() {
        return $this->forums;
    }

    /**
     * @return array
     */
    public function forumids() {
        return $this->forumids;
    }

    public function hsuforums() {
        return $this->hsuforums;
    }

    /**
     * @return array
     */
    public function hsuforumids() {
        return $this->hsuforumids;
    }

    /**
     * @return array
     */
    public function forumidsallgroups() {
        return $this->forumidsallgroups;
    }

    /**
     * @return array
     */
    public function hsuforumidsallgroups() {
        return $this->hsuforumidsallgroups;
    }

    /**
     * Remove qanda forums from forums array.
     * @param array $forums
     * @return array
     */
    private function purge_qa_forums(Array $forums) {
        if (empty($forums)) {
            return $forums;
        }
        return array_filter($forums, function($forum) {
            return $forum->type !== 'qanda';
        });
    }

    /**
     * Get forumids where current user has accessallgroups capability
     *
     * @param array $forums
     * @param string $type
     * @return array
     */
    private function forumids_accessallgroups(Array $forums, $type = 'forum') {
        $forumidsallgroups = [];

        if (empty($forums)) {
            return $forums;
        }

        foreach ($forums as $forum) {
            $cm = get_coursemodule_from_instance($type, $forum->id);
            if (intval($cm->groupmode) === SEPARATEGROUPS) {
                $cmcontext = \context_module::instance($cm->id);
                $allgroups = has_capability('moodle/site:accessallgroups', $cmcontext);
                if ($allgroups) {
                    $forumidsallgroups[] = $forum->id;
                }
            }
        }
        return $forumidsallgroups;
    }

    /**
     * Forums by lastpost with most recently posted at the top.
     *
     * @param int $limit
     * @return array
     */
    protected function forumids_by_lastpost($forumids, $limit) {
        global $DB;

        $sql = 'SELECT fd.forum, MAX(fd.timemodified) lastpost
              FROM {forum_discussions} fd
             WHERE fd.forum IN '.$forumids.'
          GROUP BY fd.forum
          ORDER BY lastpost desc';

        return $DB->get_records_sql($sql, null, 0, $limit);
    }

    /**
     * Forums by lastpost with most recently posted at the top.
     *
     * @param int $limit
     * @return array
     */
    protected function hsuforumids_by_lastpost($forumids,$limit) {
        global $DB;

        $sql = 'SELECT fd.forum, MAX(fd.timemodified) lastpost
                  FROM {hsuforum_discussions} fd
                 WHERE fd.forum IN '.$forumids.'
              GROUP BY fd.forum
              ORDER BY lastpost desc';

        return $DB->get_records_sql($sql, null, 0, $limit);
    }

    /**
     * Identify and remove stale forums.
     * This is necessary when there are a large number of forums to query - for performance reasons and also because
     * there are query parameter limits in mssql and oracle.
     *
     * @param array $forums
     * @param bool $hsufourm - is this a collection of Open Forums?
     * @return mixed
     */
    protected function process_stale_forums(Array $forums, $hsuforum = false) {

        if (count($forums) > self::$forumlimit) {
            // Get forum ids by postid (ordered by most recently posted).
            $forumids = '(';
            foreach ($forums as $forum) {
                $forumids .= $forum->id.',';
            }
            $forumids = rtrim($forumids, ',');
            $forumids .= ')';
            if (!$hsuforum) {
                $forumidsbypost = $this->forumids_by_lastpost($forumids, self::$forumlimit);
            } else {
                $forumidsbypost = $this->hsuforumids_by_lastpost($forumids, self::$forumlimit);
            }

            $tmpforums = [];

            // Re-order forums by most recently posted.
            if (!empty($forumidsbypost)) {
                foreach ($forumidsbypost as $id => $postdate) {
                    if (isset($forums[$id])) {
                        $tmpforums[$id] = $forums[$id];
                    }
                }
                $forums = $tmpforums;
            }

            // Cut off the less recently active forums (most stale).
            $forums = array_slice($forums, 0, self::$forumlimit, true);
        }

        return $forums;
    }


    /**
     * Populate forum id arrays.
     * @throws \coding_exception
     */
    protected function populate_forums() {
        local::swap_global_user($this->user->id);

        // Note - we don't include the site in the list of courses. This is intentional - we want student engagement to
        // be increased in courses where learning takes place and the front page is unlikely to fit that model.
        // Currently we are using local::swap_global_user as a hack for the following function (MDL-51353).
        $this->courses = enrol_get_my_courses();
        $this->courses = local::remove_hidden_courses($this->courses);

        $forums = [];
        $hsuforums = [];

        foreach ($this->courses as $course) {
            $forums = $forums + forum_get_readable_forums($this->user->id, $course->id);
            if (function_exists('hsuforum_get_readable_forums')) {
                $hsuforums = $hsuforums + hsuforum_get_readable_forums($this->user->id, $course->id, true);
            }
        }

        // Remove Q&A forums from array.
        $forums = $this->purge_qa_forums($forums);
        $hsuforums = $this->purge_qa_forums($hsuforums);

        // Rmove forums in courses not accessed for a long time.
        $forums = $this->process_stale_forums($forums);
        $hsuforums = $this->process_stale_forums($hsuforums, true);

        $this->forums = $forums;
        $this->hsuforums = $hsuforums;
        $this->forumids = array_keys($forums);
        $this->forumidsallgroups = $this->forumids_accessallgroups($forums);
        $this->hsuforumids = array_keys($hsuforums);
        $this->hsuforumidsallgroups = $this->forumids_accessallgroups($hsuforums, 'hsuforum');

        local::swap_global_user(false);
    }
}
