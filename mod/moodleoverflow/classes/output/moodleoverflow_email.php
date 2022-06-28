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
 * Moodleoverflow post renderable for e-mail.
 *
 * @package   mod_moodleoverflow
 * @copyright 2017 Kennet Winter <k_wint10@uni-muenster.de>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_moodleoverflow\output;

use mod_moodleoverflow\anonymous;

/**
 * Moodleoverflow email renderable for use in e-mail.
 *
 * @package   mod_moodleoverflow
 * @copyright 2017 Kennet Winter <k_wint10@uni-muenster.de>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class moodleoverflow_email implements \renderable, \templatable {

    /**
     * The course that the moodleoverflow post is in.
     *
     * @var object $course
     */
    protected $course = null;

    /**
     * The course module for the moodleoverflow.
     *
     * @var object $cm
     */
    protected $cm = null;

    /**
     * The moodleoverflow that the post is in.
     *
     * @var object $moodleoverflow
     */
    protected $moodleoverflow = null;

    /**
     * The discussion that the moodleoverflow post is in.
     *
     * @var object $discussion
     */
    protected $discussion = null;

    /**
     * The moodleoverflow post being displayed.
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
        'viewfullnames' => true,
    );

    /**
     * Builds a renderable moodleoverflow mail.
     *
     * @param object $course         Course of the moodleoverflow
     * @param object $cm             Course Module of the moodleoverflow
     * @param object $moodleoverflow The moodleoverflow of the post
     * @param object $discussion     Discussion thread in which the post appears
     * @param object $post           The post
     * @param object $author         Author of the post
     * @param object $recipient      Recipient of the email
     * @param bool   $canreply       whether the user can reply to the post
     */
    public function __construct($course, $cm, $moodleoverflow, $discussion, $post, $author, $recipient, $canreply) {
        $this->course         = $course;
        $this->cm             = $cm;
        $this->moodleoverflow = $moodleoverflow;
        $this->discussion     = $discussion;
        $this->post           = $post;
        $this->author         = $author;
        $this->userto         = $recipient;
        $this->canreply       = $canreply;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param \renderer_base $renderer  The render to be used for formatting the message
     * @param bool                         $plaintext Whether the target is a plaintext target
     *
     * @return mixed Data ready for use in a mustache template
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
     * @param \mod_moodleoverflow_renderer $renderer The render to be used for formatting the message
     *
     * @return array Data ready for use in a mustache template
     */
    protected function export_for_template_text(\mod_moodleoverflow_renderer $renderer) {

        return array(
            'id'                            => html_entity_decode($this->post->id),
            'coursename'                    => html_entity_decode($this->get_coursename()),
            'courselink'                    => html_entity_decode($this->get_courselink()),
            'moodleoverflowname'            => html_entity_decode($this->get_moodleoverflowname()),
            'showdiscussionname'            => html_entity_decode($this->has_showdiscussionname()),
            'discussionname'                => html_entity_decode($this->get_discussionname()),
            'subject'                       => html_entity_decode($this->get_subject()),
            'authorfullname'                => html_entity_decode($this->get_author_fullname()),
            'postdate'                      => html_entity_decode($this->get_postdate()),
            'firstpost'                     => $this->is_firstpost(),
            'canreply'                      => $this->canreply,
            'permalink'                     => $this->get_permalink(),
            'moodleoverflowindexlink'       => $this->get_moodleoverflowindexlink(),
            'replylink'                     => $this->get_replylink(),
            'authorpicture'                 => $this->get_author_picture(),
            'unsubscribemoodleoverflowlink' => $this->get_unsubscribemoodleoverflowlink(),
            'parentpostlink'                => $this->get_parentpostlink(),
            'unsubscribediscussionlink'     => $this->get_unsubscribediscussionlink(),
            'moodleoverflowviewlink'        => $this->get_moodleoverflowviewlink(),
            'discussionlink'                => $this->get_discussionlink(),
            'authorlink'                    => $this->get_authorlink(),
            'grouppicture'                  => $this->get_group_picture(),

            // Format some components according to the renderer.
            'message'                       => html_entity_decode($renderer->format_message_text($this->cm, $this->post)),
        );
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param \mod_moodleoverflow_renderer $renderer The render to be used for formatting the message and attachments
     *
     * @return stdClass Data ready for use in a mustache template
     */
    protected function export_for_template_html(\mod_moodleoverflow_renderer $renderer) {
        return array(
            'id'                            => $this->post->id,
            'coursename'                    => $this->get_coursename(),
            'courselink'                    => $this->get_courselink(),
            'moodleoverflowname'            => $this->get_moodleoverflowname(),
            'showdiscussionname'            => $this->has_showdiscussionname(),
            'discussionname'                => $this->get_discussionname(),
            'subject'                       => $this->get_subject(),
            'authorfullname'                => $this->get_author_fullname(),
            'postdate'                      => $this->get_postdate(),
            'canreply'                      => $this->canreply,
            'permalink'                     => $this->get_permalink(),
            'firstpost'                     => $this->is_firstpost(),
            'replylink'                     => $this->get_replylink(),
            'unsubscribediscussionlink'     => $this->get_unsubscribediscussionlink(),
            'unsubscribemoodleoverflowlink' => $this->get_unsubscribemoodleoverflowlink(),
            'parentpostlink'                => $this->get_parentpostlink(),
            'moodleoverflowindexlink'       => $this->get_moodleoverflowindexlink(),
            'moodleoverflowviewlink'        => $this->get_moodleoverflowviewlink(),
            'discussionlink'                => $this->get_discussionlink(),
            'authorlink'                    => $this->get_authorlink(),
            'authorpicture'                 => $this->get_author_picture(),
            'grouppicture'                  => $this->get_group_picture(),

            // Format some components according to the renderer.
            'message'                       => $renderer->format_message_text($this->cm, $this->post),
        );
    }

    /**
     * Magically sets a property against this object.
     *
     * @param string $name
     * @param mixed  $value
     */
    public function __set($name, $value) {

        // First attempt to use the setter function.
        $methodname = 'set_' . $name;
        if (method_exists($this, $methodname)) {
            return $this->{$methodname}($value);
        }

        // Fall back to the writable keys list.
        if (isset($this->writablekeys[$name]) && $this->writablekeys[$name]) {
            return $this->{$name} = $value;
        }

        // Throw an error rather than fail silently.
        throw new \coding_exception('Tried to set unknown property "' . $name . '"');
    }

    /**
     * Get the link to unsubscribe from the discussion.
     *
     * @return null|string
     */
    public function get_unsubscribediscussionlink() {

        // Check whether the moodleoverflow is subscribable.
        $subscribable = \mod_moodleoverflow\subscriptions::is_subscribable($this->moodleoverflow);
        if (!$subscribable) {
            return null;
        }

        // Prepare information.
        $id  = $this->moodleoverflow->id;
        $d   = $this->discussion->id;
        $url = '/mod/moodleoverflow/subscribe.php';

        // Generate a link to unsubscribe from the discussion.
        $link = new \moodle_url($url, array('id' => $id, 'd' => $d));

        return $link->out(false);
    }

    /**
     * The formatted subject for the current post.
     *
     * @return string
     */
    public function get_subject() {
        return format_string($this->discussion->name, true);
    }

    /**
     * ID number of the course that the moodleoverflow is in.
     *
     * @return string
     */
    public function get_courseidnumber() {
        return s($this->course->idnumber);
    }

    /**
     * The full name of the course that the moodleoverflow is in.
     *
     * @return string
     */
    public function get_coursefullname() {
        return format_string($this->course->fullname, true, array(
            'context' => \context_course::instance($this->course->id),
        ));
    }

    /**
     * The name of the course that the moodleoverflow is in.
     *
     * @return string
     */
    public function get_coursename() {
        return format_string($this->course->shortname, true, array(
            'context' => \context_course::instance($this->course->id),
        ));
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
                'id' => $this->course->id,
            )
        );

        return $link->out(false);
    }

    /**
     * The name of the moodleoverflow.
     *
     * @return string
     */
    public function get_moodleoverflowname() {
        return format_string($this->moodleoverflow->name, true);
    }

    /**
     * Whether to show the discussion name.
     * If the moodleoverflow name matches the discussion name, the discussion name is not typically displayed.
     *
     * @return boolean
     */
    public function has_showdiscussionname() {
        return ($this->moodleoverflow->name !== $this->discussion->name);
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
     * The fullname of the post author.
     *
     * @return string
     */
    public function get_author_fullname() {
        if (anonymous::is_post_anonymous($this->discussion, $this->moodleoverflow, $this->author->id)) {
            return get_string('privacy:anonym_user_name', 'mod_moodleoverflow');
        } else {
            return fullname($this->author, $this->viewfullnames);
        }
    }

    /**
     * The date of the post, formatted according to the postto user's preferences.
     *
     * @return string.
     */
    public function get_postdate() {

        // Get the date.
        $postmodified = $this->post->modified;

        return userdate($postmodified, "", \core_date::get_user_timezone($this->get_postto()));
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
     * Get the link to the current post, including post anchor.
     *
     * @return string
     */
    public function get_permalink() {
        $link = $this->get_discussionurl();
        $link->set_anchor($this->get_postanchor());

        return $link->out(false);
    }

    /**
     * Whether this is the first post.
     *
     * @return boolean
     */
    public function is_firstpost() {
        return empty($this->post->parent);
    }

    /**
     * Get the link to reply to the current post.
     *
     * @return string
     */
    public function get_replylink() {
        return new \moodle_url(
            '/mod/moodleoverflow/post.php', array(
                'reply' => $this->post->id,
            )
        );
    }

    /**
     * Get the link to unsubscribe from the moodleoverflow.
     *
     * @return string
     */
    public function get_unsubscribemoodleoverflowlink() {
        if (!\mod_moodleoverflow\subscriptions::is_subscribable($this->moodleoverflow)) {
            return null;
        }
        $link = new \moodle_url(
            '/mod/moodleoverflow/subscribe.php', array(
                'id' => $this->moodleoverflow->id,
            )
        );

        return $link->out(false);
    }

    /**
     * Get the link to the parent post.
     *
     * @return string
     */
    public function get_parentpostlink() {
        $link = $this->get_discussionurl();
        $link->param('parent', $this->post->parent);

        return $link->out(false);
    }

    /**
     * Get the link to the current discussion.
     *
     * @return string
     */
    protected function get_discussionurl() {
        return new \moodle_url(
        // Posts are viewed on the topic.
            '/mod/moodleoverflow/discussion.php', array(
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
        $link = $this->get_discussionurl();

        return $link->out(false);
    }

    /**
     * Get the link to the moodleoverflow index for this course.
     *
     * @return string
     */
    public function get_moodleoverflowindexlink() {
        $link = new \moodle_url(
        // Posts are viewed on the topic.
            '/mod/moodleoverflow/index.php', array(
                'id' => $this->course->id,
            )
        );

        return $link->out(false);
    }

    /**
     * Get the link to the view page for this moodleoverflow.
     *
     * @return string
     */
    public function get_moodleoverflowviewlink() {
        $link = new \moodle_url(
        // Posts are viewed on the topic.
            '/mod/moodleoverflow/view.php', array(
                'm' => $this->moodleoverflow->id,
            )
        );

        return $link->out(false);
    }

    /**
     * Get the link to the author's profile page.
     *
     * @return string
     */
    public function get_authorlink() {
        if (anonymous::is_post_anonymous($this->discussion, $this->moodleoverflow, $this->author->id)) {
            return null;
        }

        $link = new \moodle_url(
            '/user/view.php', array(
                'id'     => $this->post->userid,
                'course' => $this->course->id,
            )
        );

        return $link->out(false);
    }

    /**
     * The HTML for the author's user picture.
     *
     * @return string
     */
    public function get_author_picture() {
        global $OUTPUT;
        if (anonymous::is_post_anonymous($this->discussion, $this->moodleoverflow, $this->author->id)) {
            return '';
        }

        return $OUTPUT->user_picture($this->author, array('courseid' => $this->course->id));
    }

    /**
     * The HTML for a group picture.
     *
     * @return string
     */
    public function get_group_picture() {
        if (anonymous::is_post_anonymous($this->discussion, $this->moodleoverflow, $this->author->id)) {
            return '';
        }

        if (isset($this->userfrom->groups)) {
            $groups = $this->userfrom->groups[$this->moodleoverflow->id];
        } else {
            $groups = groups_get_all_groups($this->course->id, $this->author->id, $this->cm->groupingid);
        }

        if ($this->is_firstpost()) {
            return print_group_picture($groups, $this->course->id, false, true, true);
        }
    }

    /**
     * The plaintext anchor id for the current post.
     *
     * @return string
     */
    public function get_postanchor() {
        return 'p' . $this->post->id;
    }
}
