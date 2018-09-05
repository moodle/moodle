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
 * Forum post renderable.
 *
 * @package    mod_forum
 * @copyright  2015 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_forum\output;

defined('MOODLE_INTERNAL') || die();

/**
 * Forum post renderable.
 *
 * @copyright  2015 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @property boolean $viewfullnames Whether to override fullname()
 */
class forum_post implements \renderable, \templatable {

    /**
     * The course that the forum post is in.
     *
     * @var object $course
     */
    protected $course = null;

    /**
     * The course module for the forum.
     *
     * @var object $cm
     */
    protected $cm = null;

    /**
     * The forum that the post is in.
     *
     * @var object $forum
     */
    protected $forum = null;

    /**
     * The discussion that the forum post is in.
     *
     * @var object $discussion
     */
    protected $discussion = null;

    /**
     * The forum post being displayed.
     *
     * @var object $post
     */
    protected $post = null;

    /**
     * Whether the user can reply to this post.
     *
     * @var boolean $canreply
     */
    protected $canreply = false;

    /**
     * Whether to override forum display when displaying usernames.
     * @var boolean $viewfullnames
     */
    protected $viewfullnames = false;

    /**
     * The user that is reading the post.
     *
     * @var object $userto
     */
    protected $userto = null;

    /**
     * The user that wrote the post.
     *
     * @var object $author
     */
    protected $author = null;

    /**
     * An associative array indicating which keys on this object should be writeable.
     *
     * @var array $writablekeys
     */
    protected $writablekeys = array(
        'viewfullnames'    => true,
    );

    /**
     * Builds a renderable forum post
     *
     * @param object $course Course of the forum
     * @param object $cm Course Module of the forum
     * @param object $forum The forum of the post
     * @param object $discussion Discussion thread in which the post appears
     * @param object $post The post
     * @param object $author Author of the post
     * @param object $recipient Recipient of the email
     * @param bool $canreply True if the user can reply to the post
     */
    public function __construct($course, $cm, $forum, $discussion, $post, $author, $recipient, $canreply) {
        $this->course = $course;
        $this->cm = $cm;
        $this->forum = $forum;
        $this->discussion = $discussion;
        $this->post = $post;
        $this->author = $author;
        $this->userto = $recipient;
        $this->canreply = $canreply;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param \mod_forum_renderer $renderer The render to be used for formatting the message and attachments
     * @param bool $plaintext Whethe the target is a plaintext target
     * @return stdClass Data ready for use in a mustache template
     */
    public function export_for_template(\renderer_base $renderer, $plaintext = false) {
        if ($plaintext) {
            return $this->export_for_template_text($renderer);
        } else {
            return $this->export_for_template_html($renderer);
        }
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param \mod_forum_renderer $renderer The render to be used for formatting the message and attachments
     * @return stdClass Data ready for use in a mustache template
     */
    protected function export_for_template_text(\mod_forum_renderer $renderer) {
        return array(
            'id'                            => html_entity_decode($this->post->id),
            'coursename'                    => html_entity_decode($this->get_coursename()),
            'courselink'                    => html_entity_decode($this->get_courselink()),
            'forumname'                     => html_entity_decode($this->get_forumname()),
            'showdiscussionname'            => html_entity_decode($this->get_showdiscussionname()),
            'discussionname'                => html_entity_decode($this->get_discussionname()),
            'subject'                       => html_entity_decode($this->get_subject()),
            'authorfullname'                => html_entity_decode($this->get_author_fullname()),
            'postdate'                      => html_entity_decode($this->get_postdate()),

            // Format some components according to the renderer.
            'message'                       => html_entity_decode($renderer->format_message_text($this->cm, $this->post)),
            'attachments'                   => html_entity_decode($renderer->format_message_attachments($this->cm, $this->post)),

            'canreply'                      => $this->canreply,
            'permalink'                     => $this->get_permalink(),
            'firstpost'                     => $this->get_is_firstpost(),
            'replylink'                     => $this->get_replylink(),
            'unsubscribediscussionlink'     => $this->get_unsubscribediscussionlink(),
            'unsubscribeforumlink'          => $this->get_unsubscribeforumlink(),
            'parentpostlink'                => $this->get_parentpostlink(),

            'forumindexlink'                => $this->get_forumindexlink(),
            'forumviewlink'                 => $this->get_forumviewlink(),
            'discussionlink'                => $this->get_discussionlink(),

            'authorlink'                    => $this->get_authorlink(),
            'authorpicture'                 => $this->get_author_picture(),

            'grouppicture'                  => $this->get_group_picture(),
        );
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param \mod_forum_renderer $renderer The render to be used for formatting the message and attachments
     * @return stdClass Data ready for use in a mustache template
     */
    protected function export_for_template_html(\mod_forum_renderer $renderer) {
        return array(
            'id'                            => $this->post->id,
            'coursename'                    => $this->get_coursename(),
            'courselink'                    => $this->get_courselink(),
            'forumname'                     => $this->get_forumname(),
            'showdiscussionname'            => $this->get_showdiscussionname(),
            'discussionname'                => $this->get_discussionname(),
            'subject'                       => $this->get_subject(),
            'authorfullname'                => $this->get_author_fullname(),
            'postdate'                      => $this->get_postdate(),

            // Format some components according to the renderer.
            'message'                       => $renderer->format_message_text($this->cm, $this->post),
            'attachments'                   => $renderer->format_message_attachments($this->cm, $this->post),

            'canreply'                      => $this->canreply,
            'permalink'                     => $this->get_permalink(),
            'firstpost'                     => $this->get_is_firstpost(),
            'replylink'                     => $this->get_replylink(),
            'unsubscribediscussionlink'     => $this->get_unsubscribediscussionlink(),
            'unsubscribeforumlink'          => $this->get_unsubscribeforumlink(),
            'parentpostlink'                => $this->get_parentpostlink(),

            'forumindexlink'                => $this->get_forumindexlink(),
            'forumviewlink'                 => $this->get_forumviewlink(),
            'discussionlink'                => $this->get_discussionlink(),

            'authorlink'                    => $this->get_authorlink(),
            'authorpicture'                 => $this->get_author_picture(),

            'grouppicture'                  => $this->get_group_picture(),
        );
    }

    /**
     * Magically sets a property against this object.
     *
     * @param string $key
     * @param mixed $value
     */
    public function __set($key, $value) {
        // First attempt to use the setter function.
        $methodname = 'set_' . $key;
        if (method_exists($this, $methodname)) {
            return $this->{$methodname}($value);
        }

        // Fall back to the writable keys list.
        if (isset($this->writablekeys[$key]) && $this->writablekeys[$key]) {
            return $this->{$key} = $value;
        }

        // Throw an error rather than fail silently.
        throw new \coding_exception('Tried to set unknown property "' . $key . '"');
    }

    /**
     * Whether this is the first post.
     *
     * @return boolean
     */
    public function get_is_firstpost() {
        return empty($this->post->parent);
    }

    /**
     * Get the link to the course.
     *
     * @return string
     */
    public function get_courselink() {
        $link = new \moodle_url(
            // Posts are viewed on the topic.
            '/course/view.php', array(
                'id'    => $this->course->id,
            )
        );

        return $link->out(false);
    }

    /**
     * Get the link to the forum index for this course.
     *
     * @return string
     */
    public function get_forumindexlink() {
        $link = new \moodle_url(
            // Posts are viewed on the topic.
            '/mod/forum/index.php', array(
                'id'    => $this->course->id,
            )
        );

        return $link->out(false);
    }

    /**
     * Get the link to the view page for this forum.
     *
     * @return string
     */
    public function get_forumviewlink() {
        $link = new \moodle_url(
            // Posts are viewed on the topic.
            '/mod/forum/view.php', array(
                'f' => $this->forum->id,
            )
        );

        return $link->out(false);
    }

    /**
     * Get the link to the current discussion.
     *
     * @return string
     */
    protected function _get_discussionlink() {
        return new \moodle_url(
            // Posts are viewed on the topic.
            '/mod/forum/discuss.php', array(
                // Within a discussion.
                'd' => $this->discussion->id,
            )
        );
    }

    /**
     * Get the link to the current discussion.
     *
     * @return string
     */
    public function get_discussionlink() {
        $link = $this->_get_discussionlink();

        return $link->out(false);
    }

    /**
     * Get the link to the current post, including post anchor.
     *
     * @return string
     */
    public function get_permalink() {
        $link = $this->_get_discussionlink();
        $link->set_anchor($this->get_postanchor());

        return $link->out(false);
    }

    /**
     * Get the link to the parent post.
     *
     * @return string
     */
    public function get_parentpostlink() {
        $link = $this->_get_discussionlink();
        $link->param('parent', $this->post->parent);

        return $link->out(false);
    }

    /**
     * Get the link to the author's profile page.
     *
     * @return string
     */
    public function get_authorlink() {
        $link = new \moodle_url(
            '/user/view.php', array(
                'id' => $this->post->userid,
                'course' => $this->course->id,
            )
        );

        return $link->out(false);
    }

    /**
     * Get the link to unsubscribe from the forum.
     *
     * @return string
     */
    public function get_unsubscribeforumlink() {
        if (!\mod_forum\subscriptions::is_subscribable($this->forum)) {
            return null;
        }
        $link = new \moodle_url(
            '/mod/forum/subscribe.php', array(
                'id' => $this->forum->id,
            )
        );

        return $link->out(false);
    }

    /**
     * Get the link to unsubscribe from the discussion.
     *
     * @return string
     */
    public function get_unsubscribediscussionlink() {
        if (!\mod_forum\subscriptions::is_subscribable($this->forum)) {
            return null;
        }
        $link = new \moodle_url(
            '/mod/forum/subscribe.php', array(
                'id'  => $this->forum->id,
                'd'   => $this->discussion->id,
            )
        );

        return $link->out(false);
    }

    /**
     * Get the link to reply to the current post.
     *
     * @return string
     */
    public function get_replylink() {
        return new \moodle_url(
            '/mod/forum/post.php', array(
                'reply' => $this->post->id,
            )
        );
    }

    /**
     * The formatted subject for the current post.
     *
     * @return string
     */
    public function get_subject() {
        return format_string($this->post->subject, true);
    }

    /**
     * The plaintext anchor id for the current post.
     *
     * @return string
     */
    public function get_postanchor() {
        return 'p' . $this->post->id;
    }

    /**
     * ID number of the course that the forum is in.
     *
     * @return string
     */
    public function get_courseidnumber() {
        return s($this->course->idnumber);
    }

    /**
     * The full name of the course that the forum is in.
     *
     * @return string
     */
    public function get_coursefullname() {
        return format_string($this->course->fullname, true, array(
            'context' => \context_course::instance($this->course->id),
        ));
    }

    /**
     * The name of the course that the forum is in.
     *
     * @return string
     */
    public function get_coursename() {
        return format_string($this->course->shortname, true, array(
            'context' => \context_course::instance($this->course->id),
        ));
    }

    /**
     * The name of the forum.
     *
     * @return string
     */
    public function get_forumname() {
        return format_string($this->forum->name, true);
    }

    /**
     * The name of the current discussion.
     *
     * @return string
     */
    public function get_discussionname() {
        return format_string($this->discussion->name, true);
    }

    /**
     * Whether to show the discussion name.
     * If the forum name matches the discussion name, the discussion name
     * is not typically displayed.
     *
     * @return boolean
     */
    public function get_showdiscussionname() {
        return ($this->forum->name !== $this->discussion->name);
    }

    /**
     * The fullname of the post author.
     *
     * @return string
     */
    public function get_author_fullname() {
        return fullname($this->author, $this->viewfullnames);
    }

    /**
     * The recipient of the post.
     *
     * @return string
     */
    protected function get_postto() {
        global $USER;
        if (null === $this->userto) {
            return $USER;
        }

        return $this->userto;
    }

    /**
     * The date of the post, formatted according to the postto user's
     * preferences.
     *
     * @return string.
     */
    public function get_postdate() {
        global $CFG;

        $postmodified = $this->post->modified;
        if (!empty($CFG->forum_enabletimedposts) && ($this->discussion->timestart > $postmodified)) {
            $postmodified = $this->discussion->timestart;
        }

        return userdate($postmodified, "", \core_date::get_user_timezone($this->get_postto()));
    }

    /**
     * The HTML for the author's user picture.
     *
     * @return string
     */
    public function get_author_picture() {
        global $OUTPUT;

        return $OUTPUT->user_picture($this->author, array('courseid' => $this->course->id));
    }

    /**
     * The HTML for a group picture.
     *
     * @return string
     */
    public function get_group_picture() {
        if (isset($this->userfrom->groups)) {
            $groups = $this->userfrom->groups[$this->forum->id];
        } else {
            $groups = groups_get_all_groups($this->course->id, $this->author->id, $this->cm->groupingid);
        }

        if ($this->get_is_firstpost()) {
            return print_group_picture($groups, $this->course->id, false, true, true);
        }
    }
}
