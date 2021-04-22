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

/**
 * Adhoc task to send user forum notifications.
 *
 * @package    mod_forum
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class send_user_notifications extends \core\task\adhoc_task {

    // Use the logging trait to get some nice, juicy, logging.
    use \core\task\logging_trait;

    /**
     * @var \stdClass   A shortcut to $USER.
     */
    protected $recipient;

    /**
     * @var \stdClass[] List of courses the messages are in, indexed by courseid.
     */
    protected $courses = [];

    /**
     * @var \stdClass[] List of forums the messages are in, indexed by courseid.
     */
    protected $forums = [];

    /**
     * @var int[] List of IDs for forums in each course.
     */
    protected $courseforums = [];

    /**
     * @var \stdClass[] List of discussions the messages are in, indexed by forumid.
     */
    protected $discussions = [];

    /**
     * @var \stdClass[] List of IDs for discussions in each forum.
     */
    protected $forumdiscussions = [];

    /**
     * @var \stdClass[] List of posts the messages are in, indexed by discussionid.
     */
    protected $posts = [];

    /**
     * @var bool[] Whether the user can view fullnames for each forum.
     */
    protected $viewfullnames = [];

    /**
     * @var bool[] Whether the user can post in each discussion.
     */
    protected $canpostto = [];

    /**
     * @var \renderer[] The renderers.
     */
    protected $renderers = [];

    /**
     * @var \core\message\inbound\address_manager The inbound message address manager.
     */
    protected $inboundmanager;

    /**
     * Send out messages.
     */
    public function execute() {
        global $CFG;

        // Raise the time limit for each discussion.
        \core_php_time_limit::raise(120);

        $this->recipient = \core_user::get_user($this->get_userid());

        // Create the generic messageinboundgenerator.
        $this->inboundmanager = new \core\message\inbound\address_manager();
        $this->inboundmanager->set_handler('\mod_forum\message\inbound\reply_handler');

        $data = $this->get_custom_data();

        $this->prepare_data((array) $data);

        $markposts = [];
        $errorcount = 0;
        $sentcount = 0;
        $this->log_start("Sending messages to {$this->recipient->username} ({$this->recipient->id})");
        foreach ($this->courses as $course) {
            $coursecontext = \context_course::instance($course->id);
            if (!$course->visible and !has_capability('moodle/course:viewhiddencourses', $coursecontext)) {
                // The course is hidden and the user does not have access to it.
                // Permissions may have changed since it was queued.
                continue;
            }
            foreach ($this->courseforums[$course->id] as $forumid) {
                $forum = $this->forums[$forumid];

                $cm = get_fast_modinfo($course)->instances['forum'][$forumid];
                $modcontext = \context_module::instance($cm->id);

                foreach (array_values($this->forumdiscussions[$forumid]) as $discussionid) {
                    $discussion = $this->discussions[$discussionid];

                    if (!forum_user_can_see_discussion($forum, $discussion, $modcontext, $this->recipient)) {
                        // User cannot see this discussion.
                        // Permissions may have changed since it was queued.
                        continue;
                    }

                    if (!\mod_forum\subscriptions::is_subscribed($this->recipient->id, $forum, $discussionid, $cm)) {
                        // The user does not subscribe to this forum as a whole, or to this specific discussion.
                        continue;
                    }

                    foreach ($this->posts[$discussionid] as $post) {
                        if (!forum_user_can_see_post($forum, $discussion, $post, $this->recipient, $cm)) {
                            // User cannot see this post.
                            // Permissions may have changed since it was queued.
                            continue;
                        }

                        if ($this->send_post($course, $forum, $discussion, $post, $cm, $modcontext)) {
                            $this->log("Post {$post->id} sent", 1);
                            // Mark post as read if forum_usermarksread is set off.
                            if (!$CFG->forum_usermarksread) {
                                $markposts[$post->id] = true;
                            }
                            $sentcount++;
                        } else {
                            $this->log("Failed to send post {$post->id}", 1);
                            $errorcount++;
                        }
                    }
                }
            }
        }

        $this->log_finish("Sent {$sentcount} messages with {$errorcount} failures");
        if (!empty($markposts)) {
            if (get_user_preferences('forum_markasreadonnotification', 1, $this->recipient->id) == 1) {
                $this->log_start("Marking posts as read");
                $count = count($markposts);
                forum_tp_mark_posts_read($this->recipient, array_keys($markposts));
                $this->log_finish("Marked {$count} posts as read");
            }
        }
    }

    /**
     * Prepare all data for this run.
     *
     * Take all post ids, and fetch the relevant authors, discussions, forums, and courses for them.
     *
     * @param   int[]   $postids The list of post IDs
     */
    protected function prepare_data(array $postids) {
        global $DB;

        if (empty($postids)) {
            return;
        }

        list($in, $params) = $DB->get_in_or_equal(array_values($postids));
        $sql = "SELECT p.*, f.id AS forum, f.course
                  FROM {forum_posts} p
            INNER JOIN {forum_discussions} d ON d.id = p.discussion
            INNER JOIN {forum} f ON f.id = d.forum
                 WHERE p.id {$in}";

        $posts = $DB->get_recordset_sql($sql, $params);
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
            return;
        }

        // Fetch all discussions.
        list($in, $params) = $DB->get_in_or_equal(array_values($discussionids));
        $this->discussions = $DB->get_records_select('forum_discussions', "id {$in}", $params);
        foreach ($this->discussions as $discussion) {
            if (empty($this->forumdiscussions[$discussion->forum])) {
                $this->forumdiscussions[$discussion->forum] = [];
            }
            $this->forumdiscussions[$discussion->forum][] = $discussion->id;
        }

        // Fetch all forums.
        list($in, $params) = $DB->get_in_or_equal(array_values($forumids));
        $this->forums = $DB->get_records_select('forum', "id {$in}", $params);
        foreach ($this->forums as $forum) {
            if (empty($this->courseforums[$forum->course])) {
                $this->courseforums[$forum->course] = [];
            }
            $this->courseforums[$forum->course][] = $forum->id;
        }

        // Fetch all courses.
        list($in, $params) = $DB->get_in_or_equal(array_values($courseids));
        $this->courses = $DB->get_records_select('course', "id $in", $params);

        // Fetch all authors.
        list($in, $params) = $DB->get_in_or_equal(array_values($userids));
        $users = $DB->get_recordset_select('user', "id $in", $params);
        foreach ($users as $user) {
            $this->minimise_user_record($user);
            $this->users[$user->id] = $user;
        }
        $users->close();

        // Fill subscription caches for each forum.
        // These are per-user.
        foreach (array_values($forumids) as $id) {
            \mod_forum\subscriptions::fill_subscription_cache($id);
            \mod_forum\subscriptions::fill_discussion_subscription_cache($id);
        }
    }

    /**
     * Send the specified post for the current user.
     *
     * @param   \stdClass   $course
     * @param   \stdClass   $forum
     * @param   \stdClass   $discussion
     * @param   \stdClass   $post
     * @param   \stdClass   $cm
     * @param   \context    $context
     */
    protected function send_post($course, $forum, $discussion, $post, $cm, $context) {
        global $CFG, $PAGE;

        $author = $this->get_post_author($post->userid, $course, $forum, $cm, $context);
        if (empty($author)) {
            return false;
        }

        // Prepare to actually send the post now, and build up the content.
        $cleanforumname = str_replace('"', "'", strip_tags(format_string($forum->name)));

        $shortname = format_string($course->shortname, true, [
                'context' => \context_course::instance($course->id),
            ]);

        // Generate a reply-to address from using the Inbound Message handler.
        $replyaddress = $this->get_reply_address($course, $forum, $discussion, $post, $cm, $context);

        $data = new \mod_forum\output\forum_post_email(
            $course,
            $cm,
            $forum,
            $discussion,
            $post,
            $author,
            $this->recipient,
            $this->can_post($course, $forum, $discussion, $post, $cm, $context)
        );
        $data->viewfullnames = $this->can_view_fullnames($course, $forum, $discussion, $post, $cm, $context);

        // Not all of these variables are used in the default string but are made available to support custom subjects.
        $site = get_site();
        $a = (object) [
            'subject' => $data->get_subject(),
            'forumname' => $cleanforumname,
            'sitefullname' => format_string($site->fullname),
            'siteshortname' => format_string($site->shortname),
            'courseidnumber' => $data->get_courseidnumber(),
            'coursefullname' => $data->get_coursefullname(),
            'courseshortname' => $data->get_coursename(),
        ];
        $postsubject = html_to_text(get_string('postmailsubject', 'forum', $a), 0);

        // Message headers are stored against the message author.
        $author->customheaders = $this->get_message_headers($course, $forum, $discussion, $post, $a, $data);

        $eventdata = new \core\message\message();
        $eventdata->courseid            = $course->id;
        $eventdata->component           = 'mod_forum';
        $eventdata->name                = 'posts';
        $eventdata->userfrom            = $author;
        $eventdata->userto              = $this->recipient;
        $eventdata->subject             = $postsubject;
        $eventdata->fullmessage         = $this->get_renderer()->render($data);
        $eventdata->fullmessageformat   = FORMAT_PLAIN;
        $eventdata->fullmessagehtml     = $this->get_renderer(true)->render($data);
        $eventdata->notification        = 1;
        $eventdata->replyto             = $replyaddress;
        if (!empty($replyaddress)) {
            // Add extra text to email messages if they can reply back.
            $eventdata->set_additional_content('email', [
                    'fullmessage' => [
                        'footer' => "\n\n" . get_string('replytopostbyemail', 'mod_forum'),
                    ],
                    'fullmessagehtml' => [
                        'footer' => \html_writer::tag('p', get_string('replytopostbyemail', 'mod_forum')),
                    ]
                ]);
        }

        $eventdata->smallmessage = get_string('smallmessage', 'forum', (object) [
                'user' => fullname($author),
                'forumname' => "$shortname: " . format_string($forum->name, true) . ": " . $discussion->name,
                'message' => $post->message,
            ]);

        $contexturl = new \moodle_url('/mod/forum/discuss.php', ['d' => $discussion->id], "p{$post->id}");
        $eventdata->contexturl = $contexturl->out();
        $eventdata->contexturlname = $discussion->name;
        // User image.
        $userpicture = new \user_picture($author);
        $userpicture->size = 1; // Use f1 size.
        $userpicture->includetoken = $this->recipient->id; // Generate an out-of-session token for the user receiving the message.
        $eventdata->customdata = [
            'cmid' => $cm->id,
            'instance' => $forum->id,
            'discussionid' => $discussion->id,
            'postid' => $post->id,
            'notificationiconurl' => $userpicture->get_url($PAGE)->out(false),
            'actionbuttons' => [
                'reply' => get_string_manager()->get_string('reply', 'forum', null, $eventdata->userto->lang),
            ],
        ];

        return message_send($eventdata);
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
     * Helper to fetch the required renderer, instantiating as required.
     *
     * @param   bool    $html Whether to fetch the HTML renderer
     * @return  \core_renderer
     */
    protected function get_renderer($html = false) {
        global $PAGE;

        $target = $html ? 'htmlemail' : 'textemail';

        if (!isset($this->renderers[$target])) {
            $this->renderers[$target] = $PAGE->get_renderer('mod_forum', 'email', $target);
        }

        return $this->renderers[$target];
    }

    /**
     * Get the list of message headers.
     *
     * @param   \stdClass   $course
     * @param   \stdClass   $forum
     * @param   \stdClass   $discussion
     * @param   \stdClass   $post
     * @param   \stdClass   $a The list of strings for this  post
     * @param   \core\message\message $message The message to be sent
     * @return  \stdClass
     */
    protected function get_message_headers($course, $forum, $discussion, $post, $a, $message) {
        $cleanforumname = str_replace('"', "'", strip_tags(format_string($forum->name)));
        $viewurl = new \moodle_url('/mod/forum/view.php', ['f' => $forum->id]);

        $headers = [
            // Headers to make emails easier to track.
            'List-Id: "' . $cleanforumname . '" ' . generate_email_messageid('moodleforum' . $forum->id),
            'List-Help: ' . $viewurl->out(),
            'Message-ID: ' . forum_get_email_message_id($post->id, $this->recipient->id),
            'X-Course-Id: ' . $course->id,
            'X-Course-Name: '. format_string($course->fullname, true),

            // Headers to help prevent auto-responders.
            'Precedence: Bulk',
            'X-Auto-Response-Suppress: All',
            'Auto-Submitted: auto-generated',
            'List-Unsubscribe: <' . $message->get_unsubscribediscussionlink() . '>',
        ];

        $rootid = forum_get_email_message_id($discussion->firstpost, $this->recipient->id);

        if ($post->parent) {
            // This post is a reply, so add reply header (RFC 2822).
            $parentid = forum_get_email_message_id($post->parent, $this->recipient->id);
            $headers[] = "In-Reply-To: $parentid";

            // If the post is deeply nested we also reference the parent message id and
            // the root message id (if different) to aid threading when parts of the email
            // conversation have been deleted (RFC1036).
            if ($post->parent != $discussion->firstpost) {
                $headers[] = "References: $rootid $parentid";
            } else {
                $headers[] = "References: $parentid";
            }
        }

        // MS Outlook / Office uses poorly documented and non standard headers, including
        // Thread-Topic which overrides the Subject and shouldn't contain Re: or Fwd: etc.
        $aclone = (object) (array) $a;
        $aclone->subject = $discussion->name;
        $threadtopic = html_to_text(get_string('postmailsubject', 'forum', $aclone), 0);
        $headers[] = "Thread-Topic: $threadtopic";
        $headers[] = "Thread-Index: " . substr($rootid, 1, 28);

        return $headers;
    }

    /**
     * Get a no-reply address for this user to reply to the current post.
     *
     * @param   \stdClass   $course
     * @param   \stdClass   $forum
     * @param   \stdClass   $discussion
     * @param   \stdClass   $post
     * @param   \stdClass   $cm
     * @param   \context    $context
     * @return  string
     */
    protected function get_reply_address($course, $forum, $discussion, $post, $cm, $context) {
        if ($this->can_post($course, $forum, $discussion, $post, $cm, $context)) {
            // Generate a reply-to address from using the Inbound Message handler.
            $this->inboundmanager->set_data($post->id);
            return $this->inboundmanager->generate($this->recipient->id);
        }

        // TODO Check if we can return a string.
        // This will be controlled by the event.
        return null;
    }

    /**
     * Check whether the user can post.
     *
     * @param   \stdClass   $course
     * @param   \stdClass   $forum
     * @param   \stdClass   $discussion
     * @param   \stdClass   $post
     * @param   \stdClass   $cm
     * @param   \context    $context
     * @return  bool
     */
    protected function can_post($course, $forum, $discussion, $post, $cm, $context) {
        if (!isset($this->canpostto[$discussion->id])) {
            $this->canpostto[$discussion->id] = forum_user_can_post($forum, $discussion, $this->recipient, $cm, $course, $context);
        }
        return $this->canpostto[$discussion->id];
    }

    /**
     * Check whether the user can view full names of other users.
     *
     * @param   \stdClass   $course
     * @param   \stdClass   $forum
     * @param   \stdClass   $discussion
     * @param   \stdClass   $post
     * @param   \stdClass   $cm
     * @param   \context    $context
     * @return  bool
     */
    protected function can_view_fullnames($course, $forum, $discussion, $post, $cm, $context) {
        if (!isset($this->viewfullnames[$forum->id])) {
            $this->viewfullnames[$forum->id] = has_capability('moodle/site:viewfullnames', $context, $this->recipient->id);
        }

        return $this->viewfullnames[$forum->id];
    }

    /**
     * Removes properties from user record that are not necessary for sending post notifications.
     *
     * @param   \stdClass   $user
     */
    protected function minimise_user_record(\stdClass $user) {
        // We store large amount of users in one huge array, make sure we do not store info there we do not actually
        // need in mail generation code or messaging.
        unset($user->institution);
        unset($user->department);
        unset($user->address);
        unset($user->city);
        unset($user->currentlogin);
        unset($user->description);
        unset($user->descriptionformat);
    }
}
