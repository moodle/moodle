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
 * This file defines an adhoc task to send notifications.
 *
 * @package    mod_forum
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_forum\task;

defined('MOODLE_INTERNAL') || die();

use html_writer;
require_once($CFG->dirroot . '/mod/forum/lib.php');

/**
 * Adhoc task to send moodle forum digests for the specified user.
 *
 * @package    mod_forum
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class send_user_digests extends \core\task\adhoc_task {

    // Use the logging trait to get some nice, juicy, logging.
    use \core\task\logging_trait;

    /**
     * @var \stdClass   A shortcut to $USER.
     */
    protected $recipient;

    /**
     * @var bool[]  Whether the user can view fullnames for each forum.
     */
    protected $viewfullnames = [];

    /**
     * @var bool[]  Whether the user can post in each forum.
     */
    protected $canpostto = [];

    /**
     * @var \stdClass[] Courses with posts them.
     */
    protected $courses = [];

    /**
     * @var \stdClass[] Forums with posts them.
     */
    protected $forums = [];

    /**
     * @var \stdClass[] Discussions with posts them.
     */
    protected $discussions = [];

    /**
     * @var \stdClass[] The posts to be sent.
     */
    protected $posts = [];

    /**
     * @var \stdClass[] The various authors.
     */
    protected $users = [];

    /**
     * @var \stdClass[] A list of any per-forum digest preference that this user holds.
     */
    protected $forumdigesttypes = [];

    /**
     * @var bool    Whether the user has requested HTML or not.
     */
    protected $allowhtml = true;

    /**
     * @var string  The subject of the message.
     */
    protected $postsubject = '';

    /**
     * @var string  The plaintext content of the whole message.
     */
    protected $notificationtext = '';

    /**
     * @var string  The HTML content of the whole message.
     */
    protected $notificationhtml = '';

    /**
     * @var string  The plaintext content for the current discussion being processed.
     */
    protected $discussiontext = '';

    /**
     * @var string  The HTML content for the current discussion being processed.
     */
    protected $discussionhtml = '';

    /**
     * @var int     The number of messages sent in this digest.
     */
    protected $sentcount = 0;

    /**
     * @var \renderer[][] A cache of the different types of renderer, stored both by target (HTML, or Text), and type.
     */
    protected $renderers = [
        'html' => [],
        'text' => [],
    ];

    /**
     * @var int[] A list of post IDs to be marked as read for this user.
     */
    protected $markpostsasread = [];

    /**
     * Send out messages.
     * @throws \moodle_exception
     */
    public function execute() {
        $starttime = time();

        $this->recipient = \core_user::get_user($this->get_userid());
        $this->log_start("Sending forum digests for {$this->recipient->username} ({$this->recipient->id})");

        if (empty($this->recipient->mailformat) || $this->recipient->mailformat != 1) {
            // This user does not want to receive HTML.
            $this->allowhtml = false;
        }

        // Fetch all of the data we need to mail these posts.
        $this->prepare_data($starttime);

        if (empty($this->posts) || empty($this->discussions) || empty($this->forums)) {
            $this->log_finish("No messages found to send.");
            return;
        }

        // Add the message headers.
        $this->add_message_header();

        foreach ($this->discussions as $discussion) {
            // Raise the time limit for each discussion.
            \core_php_time_limit::raise(120);

            // Grab the data pertaining to this discussion.
            $forum = $this->forums[$discussion->forum];
            $course = $this->courses[$forum->course];
            $cm = get_fast_modinfo($course)->instances['forum'][$forum->id];
            $modcontext = \context_module::instance($cm->id);
            $coursecontext = \context_course::instance($course->id);

            if (empty($this->posts[$discussion->id])) {
                // Somehow there are no posts.
                // This should not happen but better safe than sorry.
                continue;
            }

            if (!$course->visible and !has_capability('moodle/course:viewhiddencourses', $coursecontext)) {
                // The course is hidden and the user does not have access to it.
                // Permissions may have changed since it was queued.
                continue;
            }

            if (!forum_user_can_see_discussion($forum, $discussion, $modcontext, $this->recipient)) {
                // User cannot see this discussion.
                // Permissions may have changed since it was queued.
                continue;
            }

            if (!\mod_forum\subscriptions::is_subscribed($this->recipient->id, $forum, $discussion->id, $cm)) {
                // The user does not subscribe to this forum as a whole, or to this specific discussion.
                continue;
            }

            // Fetch additional values relating to this forum.
            if (!isset($this->canpostto[$discussion->id])) {
                $this->canpostto[$discussion->id] = forum_user_can_post(
                        $forum, $discussion, $this->recipient, $cm, $course, $modcontext);
            }

            if (!isset($this->viewfullnames[$forum->id])) {
                $this->viewfullnames[$forum->id] = has_capability('moodle/site:viewfullnames', $modcontext, $this->recipient->id);
            }

            // Set the discussion storage values.
            $discussionpostcount = 0;
            $this->discussiontext = '';
            $this->discussionhtml = '';

            // Add the header for this discussion.
            $this->add_discussion_header($discussion, $forum, $course);
            $this->log_start("Adding messages in discussion {$discussion->id} (forum {$forum->id})", 1);

            // Add all posts in this forum.
            foreach ($this->posts[$discussion->id] as $post) {
                $author = $this->get_post_author($post->userid, $course, $forum, $cm, $modcontext);
                if (empty($author)) {
                    // Unable to find the author. Skip to avoid errors.
                    continue;
                }

                if (!forum_user_can_see_post($forum, $discussion, $post, $this->recipient, $cm)) {
                    // User cannot see this post.
                    // Permissions may have changed since it was queued.
                    continue;
                }

                $this->add_post_body($author, $post, $discussion, $forum, $cm, $course);
                $discussionpostcount++;
            }

            // Add the forum footer.
            $this->add_discussion_footer($discussion, $forum, $course);

            // Add the data for this discussion to the notification body.
            if ($discussionpostcount) {
                $this->sentcount += $discussionpostcount;
                $this->notificationtext .= $this->discussiontext;
                $this->notificationhtml .= $this->discussionhtml;
                $this->log_finish("Added {$discussionpostcount} messages to discussion {$discussion->id}", 1);
            } else {
                $this->log_finish("No messages found in discussion {$discussion->id} - skipped.", 1);
            }
        }

        if ($this->sentcount) {
            // This digest has at least one post and should therefore be sent.
            if ($this->send_mail()) {
                $this->log_finish("Digest sent with {$this->sentcount} messages.");
                if (get_user_preferences('forum_markasreadonnotification', 1, $this->recipient->id) == 1) {
                    forum_tp_mark_posts_read($this->recipient, $this->markpostsasread);
                }
            } else {
                $this->log_finish("Issue sending digest. Skipping.");
                throw new \moodle_exception("Issue sending digest. Skipping.");
            }
        } else {
            $this->log_finish("No messages found to send.");
        }

        // Empty the queue only if successful.
        $this->empty_queue($this->recipient->id, $starttime);

        // We have finishied all digest emails, update $CFG->digestmailtimelast.
        set_config('digestmailtimelast', $starttime);
    }

    /**
     * Prepare the data for this run.
     *
     * Note: This will also remove posts from the queue.
     *
     * @param   int     $timenow
     */
    protected function prepare_data(int $timenow) {
        global $DB;

        $sql = "SELECT p.*, f.id AS forum, f.course
                  FROM {forum_queue} q
            INNER JOIN {forum_posts} p ON p.id = q.postid
            INNER JOIN {forum_discussions} d ON d.id = p.discussion
            INNER JOIN {forum} f ON f.id = d.forum
                 WHERE q.userid = :userid
                   AND q.timemodified < :timemodified
              ORDER BY d.id, q.timemodified ASC";

        $queueparams = [
                'userid' => $this->recipient->id,
                'timemodified' => $timenow,
            ];

        $posts = $DB->get_recordset_sql($sql, $queueparams);
        $discussionids = [];
        $forumids = [];
        $courseids = [];
        $userids = [];
        foreach ($posts as $post) {
            $discussionids[] = $post->discussion;
            $forumids[] = $post->forum;
            $courseids[] = $post->course;
            $userids[] = $post->userid;
            unset($post->forum);
            if (!isset($this->posts[$post->discussion])) {
                $this->posts[$post->discussion] = [];
            }
            $this->posts[$post->discussion][$post->id] = $post;
        }
        $posts->close();

        if (empty($discussionids)) {
            // All posts have been removed since the task was queued.
            $this->empty_queue($this->recipient->id, $timenow);
            return;
        }

        list($in, $params) = $DB->get_in_or_equal($discussionids);
        $this->discussions = $DB->get_records_select('forum_discussions', "id {$in}", $params);

        list($in, $params) = $DB->get_in_or_equal($forumids);
        $this->forums = $DB->get_records_select('forum', "id {$in}", $params);

        list($in, $params) = $DB->get_in_or_equal($courseids);
        $this->courses = $DB->get_records_select('course', "id $in", $params);

        list($in, $params) = $DB->get_in_or_equal($userids);
        $this->users = $DB->get_records_select('user', "id $in", $params);

        $this->fill_digest_cache();
    }

    /**
     * Empty the queue of posts for this user.
     *
     * @param int $userid user id which queue elements are going to be removed.
     * @param int $timemodified up time limit of the queue elements to be removed.
     */
    protected function empty_queue(int $userid, int $timemodified) : void {
        global $DB;

        $DB->delete_records_select('forum_queue', "userid = :userid AND timemodified < :timemodified", [
                'userid' => $userid,
                'timemodified' => $timemodified,
            ]);
    }

    /**
     * Fill the cron digest cache.
     */
    protected function fill_digest_cache() {
        global $DB;

        $this->forumdigesttypes = $DB->get_records_menu('forum_digests', [
                'userid' => $this->recipient->id,
            ], '', 'forum, maildigest');
    }

    /**
     * Fetch and initialise the post author.
     *
     * @param   int         $userid The id of the user to fetch
     * @param   \stdClass   $course
     * @param   \stdClass   $forum
     * @param   \stdClass   $cm
     * @param   \context    $context
     * @return  \stdClass
     */
    protected function get_post_author($userid, $course, $forum, $cm, $context) {
        if (!isset($this->users[$userid])) {
            // This user no longer exists.
            return false;
        }

        $user = $this->users[$userid];

        if (!isset($user->groups)) {
            // Initialise the groups list.
            $user->groups = [];
        }

        if (!isset($user->groups[$forum->id])) {
            $user->groups[$forum->id] = groups_get_all_groups($course->id, $user->id, $cm->groupingid);
        }

        // Clone the user object to prevent leaks between messages.
        return (object) (array) $user;
    }

    /**
     * Add the header to this message.
     */
    protected function add_message_header() {
        $site = get_site();

        // Set the subject of the message.
        $this->postsubject = get_string('digestmailsubject', 'forum', format_string($site->shortname, true));

        // And the content of the header in body.
        $headerdata = (object) [
            'sitename' => format_string($site->fullname, true),
            'userprefs' => (new \moodle_url('/user/forum.php', [
                    'id' => $this->recipient->id,
                    'course' => $site->id,
                ]))->out(false),
            ];

        $this->notificationtext .= get_string('digestmailheader', 'forum', $headerdata) . "\n";

        if ($this->allowhtml) {
            $headerdata->userprefs = html_writer::link($headerdata->userprefs, get_string('digestmailprefs', 'forum'), [
                    'target' => '_blank',
                ]);

            $this->notificationhtml .= html_writer::tag('p', get_string('digestmailheader', 'forum', $headerdata));
            $this->notificationhtml .= html_writer::empty_tag('br');
            $this->notificationhtml .= html_writer::empty_tag('hr', [
                    'size' => 1,
                    'noshade' => 'noshade',
                ]);
        }
    }

    /**
     * Add the header for this discussion.
     *
     * @param   \stdClass   $discussion The discussion to add the footer for
     * @param   \stdClass   $forum The forum that the discussion belongs to
     * @param   \stdClass   $course The course that the forum belongs to
     */
    protected function add_discussion_header($discussion, $forum, $course) {
        global $CFG;

        $shortname = format_string($course->shortname, true, [
                'context' => \context_course::instance($course->id),
            ]);

        $strforums = get_string('forums', 'forum');

        $this->discussiontext .= "\n=====================================================================\n\n";
        $this->discussiontext .= "$shortname -> $strforums -> " . format_string($forum->name, true);
        if ($discussion->name != $forum->name) {
            $this->discussiontext  .= " -> " . format_string($discussion->name, true);
        }
        $this->discussiontext .= "\n";
        $this->discussiontext .= new \moodle_url('/mod/forum/discuss.php', [
                'd' => $discussion->id,
            ]);
        $this->discussiontext .= "\n";

        if ($this->allowhtml) {
            $this->discussionhtml .= "<p><font face=\"sans-serif\">".
                "<a target=\"_blank\" href=\"$CFG->wwwroot/course/view.php?id=$course->id\">$shortname</a> -> ".
                "<a target=\"_blank\" href=\"$CFG->wwwroot/mod/forum/index.php?id=$course->id\">$strforums</a> -> ".
                "<a target=\"_blank\" href=\"$CFG->wwwroot/mod/forum/view.php?f=$forum->id\">" .
                        format_string($forum->name, true)."</a>";
            if ($discussion->name == $forum->name) {
                $this->discussionhtml .= "</font></p>";
            } else {
                $this->discussionhtml .=
                        " -> <a target=\"_blank\" href=\"$CFG->wwwroot/mod/forum/discuss.php?d=$discussion->id\">" .
                        format_string($discussion->name, true)."</a></font></p>";
            }
            $this->discussionhtml .= '<p>';
        }

    }

    /**
     * Add the body of this post.
     *
     * @param   \stdClass   $author The author of the post
     * @param   \stdClass   $post The post being sent
     * @param   \stdClass   $discussion The discussion that the post is in
     * @param   \stdClass   $forum The forum that the discussion belongs to
     * @param   \cminfo     $cm The cminfo object for the forum
     * @param   \stdClass   $course The course that the forum belongs to
     */
    protected function add_post_body($author, $post, $discussion, $forum, $cm, $course) {
        global $CFG;

        $canreply = $this->canpostto[$discussion->id];

        $data = new \mod_forum\output\forum_post_email(
            $course,
            $cm,
            $forum,
            $discussion,
            $post,
            $author,
            $this->recipient,
            $canreply
        );

        // Override the viewfullnames value.
        $data->viewfullnames = $this->viewfullnames[$forum->id];

        // Determine the type of digest being sent.
        $maildigest = $this->get_maildigest($forum->id);

        $textrenderer = $this->get_renderer($maildigest);
        $this->discussiontext .= $textrenderer->render($data);
        $this->discussiontext .= "\n";
        if ($this->allowhtml) {
            $htmlrenderer = $this->get_renderer($maildigest, true);
            $this->discussionhtml .= $htmlrenderer->render($data);
            $this->log("Adding post {$post->id} in format {$maildigest} with HTML", 2);
        } else {
            $this->log("Adding post {$post->id} in format {$maildigest} without HTML", 2);
        }

        if ($maildigest == 1 && !$CFG->forum_usermarksread) {
            // Create an array of postid's for this user to mark as read.
            $this->markpostsasread[] = $post->id;
        }

    }

    /**
     * Add the footer for this discussion.
     *
     * @param   \stdClass   $discussion The discussion to add the footer for
     */
    protected function add_discussion_footer($discussion) {
        global $CFG;

        if ($this->allowhtml) {
            $footerlinks = [];

            $forum = $this->forums[$discussion->forum];
            if (\mod_forum\subscriptions::is_forcesubscribed($forum)) {
                // This forum is force subscribed. The user cannot unsubscribe.
                $footerlinks[] = get_string("everyoneissubscribed", "forum");
            } else {
                $footerlinks[] = "<a href=\"$CFG->wwwroot/mod/forum/subscribe.php?id=$forum->id\">" .
                    get_string("unsubscribe", "forum") . "</a>";
            }
            $footerlinks[] = "<a href='{$CFG->wwwroot}/mod/forum/index.php?id={$forum->course}'>" .
                    get_string("digestmailpost", "forum") . '</a>';

            $this->discussionhtml .= "\n<div class='mdl-right'><font size=\"1\">" .
                    implode('&nbsp;', $footerlinks) . '</font></div>';
            $this->discussionhtml .= '<hr size="1" noshade="noshade" /></p>';
        }
    }

    /**
     * Get the forum digest type for the specified forum, failing back to
     * the default setting for the current user if not specified.
     *
     * @param   int     $forumid
     * @return  int
     */
    protected function get_maildigest($forumid) {
        $maildigest = -1;

        if (isset($this->forumdigesttypes[$forumid])) {
            $maildigest = $this->forumdigesttypes[$forumid];
        }

        if ($maildigest === -1 && !empty($this->recipient->maildigest)) {
            $maildigest = $this->recipient->maildigest;
        }

        if ($maildigest === -1) {
            // There is no maildigest type right now.
            $maildigest = 1;
        }

        return $maildigest;
    }

    /**
     * Send the composed message to the user.
     */
    protected function send_mail() {
        // Headers to help prevent auto-responders.
        $userfrom = \core_user::get_noreply_user();
        $userfrom->customheaders = array(
            "Precedence: Bulk",
            'X-Auto-Response-Suppress: All',
            'Auto-Submitted: auto-generated',
        );

        $eventdata = new \core\message\message();
        $eventdata->courseid = SITEID;
        $eventdata->component = 'mod_forum';
        $eventdata->name = 'digests';
        $eventdata->userfrom = $userfrom;
        $eventdata->userto = $this->recipient;
        $eventdata->subject = $this->postsubject;
        $eventdata->fullmessage = $this->notificationtext;
        $eventdata->fullmessageformat = FORMAT_PLAIN;
        $eventdata->fullmessagehtml = $this->notificationhtml;
        $eventdata->notification = 1;
        $eventdata->smallmessage = get_string('smallmessagedigest', 'forum', $this->sentcount);

        return message_send($eventdata);
    }

    /**
     * Helper to fetch the required renderer, instantiating as required.
     *
     * @param   int     $maildigest The type of mail digest being sent
     * @param   bool    $html Whether to fetch the HTML renderer
     * @return  \core_renderer
     */
    protected function get_renderer($maildigest, $html = false) {
        global $PAGE;

        $type = $maildigest == 2 ? 'emaildigestbasic' : 'emaildigestfull';
        $target = $html ? 'htmlemail' : 'textemail';

        if (!isset($this->renderers[$target][$type])) {
            $this->renderers[$target][$type] = $PAGE->get_renderer('mod_forum', $type, $target);
        }

        return $this->renderers[$target][$type];
    }
}
