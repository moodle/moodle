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
 * The forum module trait with additional generator helpers.
 *
 * @package    mod_forum
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

trait mod_forum_tests_generator_trait {

    /**
     * Helper to create the required number of users in the specified course.
     * Users are enrolled as students by default.
     *
     * @param   stdClass $course The course object
     * @param   integer $count The number of users to create
     * @param   string  $role The role to assign users as
     * @return  array The users created
     */
    protected function helper_create_users($course, $count, $role = null) {
        $users = array();

        for ($i = 0; $i < $count; $i++) {
            $user = $this->getDataGenerator()->create_user();
            $this->getDataGenerator()->enrol_user($user->id, $course->id, $role);
            $users[] = $user;
        }

        return $users;
    }

    /**
     * Create a new discussion and post within the specified forum, as the
     * specified author.
     *
     * @param stdClass $forum The forum to post in
     * @param stdClass $author The author to post as
     * @param array $fields any other fields in discussion (name, message, messageformat, ...)
     * @return array An array containing the discussion object, and the post object
     */
    protected function helper_post_to_forum($forum, $author, $fields = array()) {
        global $DB;
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_forum');

        // Create a discussion in the forum, and then add a post to that discussion.
        $record = (object)$fields;
        $record->course = $forum->course;
        $record->userid = $author->id;
        $record->forum = $forum->id;
        $discussion = $generator->create_discussion($record);

        // Retrieve the post which was created by create_discussion.
        $post = $DB->get_record('forum_posts', array('discussion' => $discussion->id));

        return [$discussion, $post];
    }

    /**
     * Update the post time for the specified post by $factor.
     *
     * @param stdClass $post The post to update
     * @param int $factor The amount to update by
     */
    protected function helper_update_post_time($post, $factor) {
        global $DB;

        // Update the post to have a created in the past.
        $DB->set_field('forum_posts', 'created', $post->created + $factor, array('id' => $post->id));
    }

    /**
     * Update the subscription time for the specified user/discussion by $factor.
     *
     * @param stdClass $user The user to update
     * @param stdClass $discussion The discussion to update for this user
     * @param int $factor The amount to update by
     */
    protected function helper_update_subscription_time($user, $discussion, $factor) {
        global $DB;

        $sub = $DB->get_record('forum_discussion_subs', array('userid' => $user->id, 'discussion' => $discussion->id));

        // Update the subscription to have a preference in the past.
        $DB->set_field('forum_discussion_subs', 'preference', $sub->preference + $factor, array('id' => $sub->id));
    }

    /**
     * Create a new post within an existing discussion, as the specified author.
     *
     * @param stdClass $forum The forum to post in
     * @param stdClass $discussion The discussion to post in
     * @param stdClass $author The author to post as
     * @param array $options Additional options to pass to `create_post`
     * @return stdClass The forum post
     */
    protected function helper_post_to_discussion($forum, $discussion, $author, array $options = []) {
        global $DB;

        $generator = $this->getDataGenerator()->get_plugin_generator('mod_forum');

        // Add a post to the discussion.
        $record = array_merge([
            'course' => $forum->course,
            'subject' => $discussion->subject,
            'userid' => $author->id,
            'forum' => $forum->id,
            'discussion' => $discussion->id,
            'mailnow' => 1,
        ], $options);

        $post = $generator->create_post((object) $record);

        return $post;
    }

    /**
     * Create a new post within an existing discussion, as the specified author.
     *
     * @param stdClass $parent The post being replied to
     * @param stdClass $author The author to post as
     * @param array $options Additional options to pass to `create_post`
     * @return stdClass The forum post
     */
    protected function helper_reply_to_post($parent, $author, array $options = []) {
        global $DB;

        $generator = $this->getDataGenerator()->get_plugin_generator('mod_forum');

        // Add a post to the discussion.
        $record = (object) array_merge([
            'discussion' => $parent->discussion,
            'parent' => $parent->id,
            'userid' => $author->id,
            'mailnow' => 1,
            'subject' => $parent->subject,
        ], $options);

        $post = $generator->create_post($record);

        return $post;
    }

    /**
     * Gets the role id from it's shortname.
     *
     * @param   string $roleshortname
     * @return  int
     */
    protected function get_role_id($roleshortname) {
        global $DB;

        return $DB->get_field('role', 'id', ['shortname' => $roleshortname]);
    }
}
