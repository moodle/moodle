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
 * A scheduled task for forum cron.
 *
 * @package    mod_forum
 * @copyright  2014 Dan Poltawski <dan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_forum\task;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/forum/lib.php');

/**
 * The main scheduled task for the forum.
 *
 * @package    mod_forum
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cron_task extends \core\task\scheduled_task {

    // Use the logging trait to get some nice, juicy, logging.
    use \core\task\logging_trait;

    /**
     * @var The list of courses which contain posts to be sent.
     */
    protected $courses = [];

    /**
     * @var The list of forums which contain posts to be sent.
     */
    protected $forums = [];

    /**
     * @var The list of discussions which contain posts to be sent.
     */
    protected $discussions = [];

    /**
     * @var The list of posts to be sent.
     */
    protected $posts = [];

    /**
     * @var The list of post authors.
     */
    protected $users = [];

    /**
     * @var The list of subscribed users.
     */
    protected $subscribedusers = [];

    /**
     * @var The list of digest users.
     */
    protected $digestusers = [];

    /**
     * @var The list of adhoc data for sending.
     */
    protected $adhocdata = [];

    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name() {
        return get_string('crontask', 'mod_forum');
    }

    /**
     * Execute the scheduled task.
     */
    public function execute() {
        global $CFG, $DB;

        $timenow = time();

        // Delete any really old posts in the digest queue.
        $weekago = $timenow - (7 * 24 * 3600);
        $this->log_start("Removing old digest records from 7 days ago.");
        $DB->delete_records_select('forum_queue', "timemodified < ?", array($weekago));
        $this->log_finish("Removed all old digest records.");

        $endtime   = $timenow - $CFG->maxeditingtime;
        $starttime = $endtime - (2 * DAYSECS);
        $this->log_start("Fetching unmailed posts.");
        if (!$posts = $this->get_unmailed_posts($starttime, $endtime, $timenow)) {
            $this->log_finish("No posts found.", 1);
            return false;
        }
        $this->log_finish("Done");

        // Process post data and turn into adhoc tasks.
        $this->process_post_data($posts);

        // Mark posts as read.
        list($in, $params) = $DB->get_in_or_equal(array_keys($posts));
        $DB->set_field_select('forum_posts', 'mailed', 1, "id {$in}", $params);
    }

    /**
     * Process all posts and convert to appropriated hoc tasks.
     *
     * @param   \stdClass[] $posts
     */
    protected function process_post_data($posts) {
        $discussionids = [];
        $forumids = [];
        $courseids = [];

        $this->log_start("Processing post information");

        $start = microtime(true);
        foreach ($posts as $id => $post) {
            $discussionids[$post->discussion] = true;
            $forumids[$post->forum] = true;
            $courseids[$post->course] = true;
            $this->add_data_for_post($post);
            $this->posts[$id] = $post;
        }
        $this->log_finish(sprintf("Processed %s posts", count($this->posts)));

        if (empty($this->posts)) {
            $this->log("No posts found. Returning early.");
            return;
        }

        // Please note, this order is intentional.
        // The forum cache makes use of the course.
        $this->log_start("Filling caches");

        $start = microtime(true);
        $this->log_start("Filling course cache", 1);
        $this->fill_course_cache(array_keys($courseids));
        $this->log_finish("Done", 1);

        $this->log_start("Filling forum cache", 1);
        $this->fill_forum_cache(array_keys($forumids));
        $this->log_finish("Done", 1);

        $this->log_start("Filling discussion cache", 1);
        $this->fill_discussion_cache(array_keys($discussionids));
        $this->log_finish("Done", 1);

        $this->log_start("Filling user subscription cache", 1);
        $this->fill_user_subscription_cache();
        $this->log_finish("Done", 1);

        $this->log_start("Filling digest cache", 1);
        $this->fill_digest_cache();
        $this->log_finish("Done", 1);

        $this->log_finish("All caches filled");

        $this->log_start("Queueing user tasks.");
        $this->queue_user_tasks();
        $this->log_finish("All tasks queued.");
    }

    /**
     * Fill the course cache.
     *
     * @param   int[]       $courseids
     */
    protected function fill_course_cache($courseids) {
        global $DB;

        list($in, $params) = $DB->get_in_or_equal($courseids);
        $this->courses = $DB->get_records_select('course', "id $in", $params);
    }

    /**
     * Fill the forum cache.
     *
     * @param   int[]       $forumids
     */
    protected function fill_forum_cache($forumids) {
        global $DB;

        $requiredfields = [
                'id',
                'course',
                'forcesubscribe',
                'type',
            ];
        list($in, $params) = $DB->get_in_or_equal($forumids);
        $this->forums = $DB->get_records_select('forum', "id $in", $params, '', implode(', ', $requiredfields));
        foreach ($this->forums as $id => $forum) {
            \mod_forum\subscriptions::fill_subscription_cache($id);
            \mod_forum\subscriptions::fill_discussion_subscription_cache($id);
        }
    }

    /**
     * Fill the discussion cache.
     *
     * @param   int[]       $discussionids
     */
    protected function fill_discussion_cache($discussionids) {
        global $DB;

        if (empty($discussionids)) {
            $this->discussion = [];
        } else {

            $requiredfields = [
                    'id',
                    'groupid',
                    'firstpost',
                    'timestart',
                    'timeend',
                ];

            list($in, $params) = $DB->get_in_or_equal($discussionids);
            $this->discussions = $DB->get_records_select(
                    'forum_discussions', "id $in", $params, '', implode(', ', $requiredfields));
        }
    }

    /**
     * Fill the cache of user digest preferences.
     */
    protected function fill_digest_cache() {
        global $DB;

        if (empty($this->users)) {
            return;
        }
        // Get the list of forum subscriptions for per-user per-forum maildigest settings.
        list($in, $params) = $DB->get_in_or_equal(array_keys($this->users));
        $digestspreferences = $DB->get_recordset_select(
                'forum_digests', "userid $in", $params, '', 'id, userid, forum, maildigest');
        foreach ($digestspreferences as $digestpreference) {
            if (!isset($this->digestusers[$digestpreference->forum])) {
                $this->digestusers[$digestpreference->forum] = [];
            }
            $this->digestusers[$digestpreference->forum][$digestpreference->userid] = $digestpreference->maildigest;
        }
        $digestspreferences->close();
    }

    /**
     * Add dsta for the current forum post to the structure of adhoc data.
     *
     * @param   \stdClass   $post
     */
    protected function add_data_for_post($post) {
        if (!isset($this->adhocdata[$post->course])) {
            $this->adhocdata[$post->course] = [];
        }

        if (!isset($this->adhocdata[$post->course][$post->forum])) {
            $this->adhocdata[$post->course][$post->forum] = [];
        }

        if (!isset($this->adhocdata[$post->course][$post->forum][$post->discussion])) {
            $this->adhocdata[$post->course][$post->forum][$post->discussion] = [];
        }

        $this->adhocdata[$post->course][$post->forum][$post->discussion][$post->id] = $post->id;
    }

    /**
     * Fill the cache of user subscriptions.
     */
    protected function fill_user_subscription_cache() {
        foreach ($this->forums as $forum) {
            $cm = get_fast_modinfo($this->courses[$forum->course])->instances['forum'][$forum->id];
            $modcontext = \context_module::instance($cm->id);

            $this->subscribedusers[$forum->id] = [];
            if ($users = \mod_forum\subscriptions::fetch_subscribed_users($forum, 0, $modcontext, 'u.id, u.maildigest', true)) {
                foreach ($users as $user) {
                    // This user is subscribed to this forum.
                    $this->subscribedusers[$forum->id][$user->id] = $user->id;
                    if (!isset($this->users[$user->id])) {
                        // Store minimal user info.
                        $this->users[$user->id] = $user;
                    }
                }
                // Release memory.
                unset($users);
            }
        }
    }

    /**
     * Queue the user tasks.
     */
    protected function queue_user_tasks() {
        global $CFG, $DB;

        $timenow = time();
        $sitetimezone = \core_date::get_server_timezone();
        $counts = [
            'digests' => 0,
            'individuals' => 0,
            'users' => 0,
            'ignored' => 0,
            'messages' => 0,
        ];
        $this->log("Processing " . count($this->users) . " users", 1);
        foreach ($this->users as $user) {
            $usercounts = [
                'digests' => 0,
                'messages' => 0,
            ];

            $send = false;
            // Setup this user so that the capabilities are cached, and environment matches receiving user.
            cron_setup_user($user);

            list($individualpostdata, $digestpostdata) = $this->fetch_posts_for_user($user);

            if (!empty($digestpostdata)) {
                // Insert all of the records for the digest.
                $DB->insert_records('forum_queue', $digestpostdata);
                $digesttime = usergetmidnight($timenow, $sitetimezone) + ($CFG->digestmailtime * 3600);

                $task = new \mod_forum\task\send_user_digests();
                $task->set_userid($user->id);
                $task->set_component('mod_forum');
                $task->set_next_run_time($digesttime);
                \core\task\manager::reschedule_or_queue_adhoc_task($task);
                $usercounts['digests']++;
                $send = true;
            }

            if (!empty($individualpostdata)) {
                $usercounts['messages'] += count($individualpostdata);

                $task = new \mod_forum\task\send_user_notifications();
                $task->set_userid($user->id);
                $task->set_custom_data($individualpostdata);
                $task->set_component('mod_forum');
                \core\task\manager::queue_adhoc_task($task);
                $counts['individuals']++;
                $send = true;
            }

            if ($send) {
                $counts['users']++;
                $counts['messages'] += $usercounts['messages'];
                $counts['digests'] += $usercounts['digests'];
            } else {
                $counts['ignored']++;
            }

            $this->log(sprintf("Queued %d digests and %d messages for %s",
                    $usercounts['digests'],
                    $usercounts['messages'],
                    $user->id
                ), 2);
        }
        $this->log(
            sprintf(
                "Queued %d digests, and %d individual tasks for %d post mails. " .
                "Unique users: %d (%d ignored)",
                $counts['digests'],
                $counts['individuals'],
                $counts['messages'],
                $counts['users'],
                $counts['ignored']
            ), 1);
    }

    /**
     * Fetch posts for this user.
     *
     * @param   \stdClass   $user The user to fetch posts for.
     */
    protected function fetch_posts_for_user($user) {
        // We maintain a mapping of user groups for each forum.
        $usergroups = [];
        $digeststructure = [];

        $poststructure = $this->adhocdata;
        $poststosend = [];
        foreach ($poststructure as $courseid => $forumids) {
            $course = $this->courses[$courseid];
            foreach ($forumids as $forumid => $discussionids) {
                $forum = $this->forums[$forumid];
                $maildigest = forum_get_user_maildigest_bulk($this->digestusers, $user, $forumid);

                if (!isset($this->subscribedusers[$forumid][$user->id])) {
                    // This user has no subscription of any kind to this forum.
                    // Do not send them any posts at all.
                    unset($poststructure[$courseid][$forumid]);
                    continue;
                }

                $subscriptiontime = \mod_forum\subscriptions::fetch_discussion_subscription($forum->id, $user->id);

                $cm = get_fast_modinfo($course)->instances['forum'][$forumid];
                foreach ($discussionids as $discussionid => $postids) {
                    $discussion = $this->discussions[$discussionid];
                    if (!\mod_forum\subscriptions::is_subscribed($user->id, $forum, $discussionid, $cm)) {
                        // The user does not subscribe to this forum as a whole, or to this specific discussion.
                        unset($poststructure[$courseid][$forumid][$discussionid]);
                        continue;
                    }

                    if ($discussion->groupid > 0 and $groupmode = groups_get_activity_groupmode($cm, $course)) {
                        // This discussion has a groupmode set (SEPARATEGROUPS or VISIBLEGROUPS).
                        // Check whether the user can view it based on their groups.
                        if (!isset($usergroups[$forum->id])) {
                            $usergroups[$forum->id] = groups_get_all_groups($courseid, $user->id, $cm->groupingid);
                        }

                        if (!isset($usergroups[$forum->id][$discussion->groupid])) {
                            // This user is not a member of this group, or the group no longer exists.

                            $modcontext = \context_module::instance($cm->id);
                            if (!has_capability('moodle/site:accessallgroups', $modcontext, $user)) {
                                // This user does not have the accessallgroups and is not a member of the group.
                                // Do not send posts from other groups when in SEPARATEGROUPS or VISIBLEGROUPS.
                                unset($poststructure[$courseid][$forumid][$discussionid]);
                                continue;
                            }
                        }
                    }

                    foreach ($postids as $postid) {
                        $post = $this->posts[$postid];
                        if ($subscriptiontime) {
                            // Skip posts if the user subscribed to the discussion after it was created.
                            $subscribedafter = isset($subscriptiontime[$post->discussion]);
                            $subscribedafter = $subscribedafter && ($subscriptiontime[$post->discussion] > $post->created);
                            if ($subscribedafter) {
                                // The user subscribed to the discussion/forum after this post was created.
                                unset($poststructure[$courseid][$forumid][$discussionid][$postid]);
                                continue;
                            }
                        }

                        if ($maildigest > 0) {
                            // This user wants the mails to be in digest form.
                            $digeststructure[] = (object) [
                                'userid' => $user->id,
                                'discussionid' => $discussion->id,
                                'postid' => $post->id,
                                'timemodified' => $post->created,
                            ];
                            unset($poststructure[$courseid][$forumid][$discussionid][$postid]);
                            continue;
                        } else {
                            // Add this post to the list of postids to be sent.
                            $poststosend[] = $postid;
                        }
                    }
                }

                if (empty($poststructure[$courseid][$forumid])) {
                    // This user is not subscribed to any discussions in this forum at all.
                    unset($poststructure[$courseid][$forumid]);
                    continue;
                }
            }
            if (empty($poststructure[$courseid])) {
                // This user is not subscribed to any forums in this course.
                unset($poststructure[$courseid]);
            }
        }

        return [$poststosend, $digeststructure];
    }

    /**
     * Returns a list of all new posts that have not been mailed yet
     *
     * @param int $starttime posts created after this time
     * @param int $endtime posts created before this
     * @param int $now used for timed discussions only
     * @return array
     */
    protected function get_unmailed_posts($starttime, $endtime, $now = null) {
        global $CFG, $DB;

        $params = array();
        $params['mailed'] = FORUM_MAILED_PENDING;
        $params['ptimestart'] = $starttime;
        $params['ptimeend'] = $endtime;
        $params['mailnow'] = 1;

        if (!empty($CFG->forum_enabletimedposts)) {
            if (empty($now)) {
                $now = time();
            }
            $selectsql = "AND (p.created >= :ptimestart OR d.timestart >= :pptimestart)";
            $params['pptimestart'] = $starttime;
            $timedsql = "AND (d.timestart < :dtimestart AND (d.timeend = 0 OR d.timeend > :dtimeend))";
            $params['dtimestart'] = $now;
            $params['dtimeend'] = $now;
        } else {
            $timedsql = "";
            $selectsql = "AND p.created >= :ptimestart";
        }

        return $DB->get_records_sql(
               "SELECT
                    p.id,
                    p.discussion,
                    d.forum,
                    d.course,
                    p.created,
                    p.parent,
                    p.userid
                  FROM {forum_posts} p
                  JOIN {forum_discussions} d ON d.id = p.discussion
                 WHERE p.mailed = :mailed
                $selectsql
                   AND (p.created < :ptimeend OR p.mailnow = :mailnow)
                $timedsql
                 ORDER BY p.modified ASC",
             $params);
    }
}
