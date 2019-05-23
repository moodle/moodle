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
 * External forum API
 *
 * @package    mod_forum
 * @copyright  2012 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once("$CFG->libdir/externallib.php");

use mod_forum\local\exporters\post as post_exporter;
use mod_forum\local\exporters\discussion as discussion_exporter;

class mod_forum_external extends external_api {

    /**
     * Describes the parameters for get_forum.
     *
     * @return external_function_parameters
     * @since Moodle 2.5
     */
    public static function get_forums_by_courses_parameters() {
        return new external_function_parameters (
            array(
                'courseids' => new external_multiple_structure(new external_value(PARAM_INT, 'course ID',
                        VALUE_REQUIRED, '', NULL_NOT_ALLOWED), 'Array of Course IDs', VALUE_DEFAULT, array()),
            )
        );
    }

    /**
     * Returns a list of forums in a provided list of courses,
     * if no list is provided all forums that the user can view
     * will be returned.
     *
     * @param array $courseids the course ids
     * @return array the forum details
     * @since Moodle 2.5
     */
    public static function get_forums_by_courses($courseids = array()) {
        global $CFG;

        require_once($CFG->dirroot . "/mod/forum/lib.php");

        $params = self::validate_parameters(self::get_forums_by_courses_parameters(), array('courseids' => $courseids));

        $courses = array();
        if (empty($params['courseids'])) {
            $courses = enrol_get_my_courses();
            $params['courseids'] = array_keys($courses);
        }

        // Array to store the forums to return.
        $arrforums = array();
        $warnings = array();

        // Ensure there are courseids to loop through.
        if (!empty($params['courseids'])) {

            list($courses, $warnings) = external_util::validate_courses($params['courseids'], $courses);

            // Get the forums in this course. This function checks users visibility permissions.
            $forums = get_all_instances_in_courses("forum", $courses);
            foreach ($forums as $forum) {

                $course = $courses[$forum->course];
                $cm = get_coursemodule_from_instance('forum', $forum->id, $course->id);
                $context = context_module::instance($cm->id);

                // Skip forums we are not allowed to see discussions.
                if (!has_capability('mod/forum:viewdiscussion', $context)) {
                    continue;
                }

                $forum->name = external_format_string($forum->name, $context->id);
                // Format the intro before being returning using the format setting.
                list($forum->intro, $forum->introformat) = external_format_text($forum->intro, $forum->introformat,
                                                                                $context->id, 'mod_forum', 'intro', null);
                $forum->introfiles = external_util::get_area_files($context->id, 'mod_forum', 'intro', false, false);
                // Discussions count. This function does static request cache.
                $forum->numdiscussions = forum_count_discussions($forum, $cm, $course);
                $forum->cmid = $forum->coursemodule;
                $forum->cancreatediscussions = forum_user_can_post_discussion($forum, null, -1, $cm, $context);
                $forum->istracked = forum_tp_is_tracked($forum);
                if ($forum->istracked) {
                    $forum->unreadpostscount = forum_tp_count_forum_unread_posts($cm, $course);
                }

                // Add the forum to the array to return.
                $arrforums[$forum->id] = $forum;
            }
        }

        return $arrforums;
    }

    /**
     * Describes the get_forum return value.
     *
     * @return external_single_structure
     * @since Moodle 2.5
     */
    public static function get_forums_by_courses_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'id' => new external_value(PARAM_INT, 'Forum id'),
                    'course' => new external_value(PARAM_INT, 'Course id'),
                    'type' => new external_value(PARAM_TEXT, 'The forum type'),
                    'name' => new external_value(PARAM_RAW, 'Forum name'),
                    'intro' => new external_value(PARAM_RAW, 'The forum intro'),
                    'introformat' => new external_format_value('intro'),
                    'introfiles' => new external_files('Files in the introduction text', VALUE_OPTIONAL),
                    'duedate' => new external_value(PARAM_INT, 'duedate for the user', VALUE_OPTIONAL),
                    'cutoffdate' => new external_value(PARAM_INT, 'cutoffdate for the user', VALUE_OPTIONAL),
                    'assessed' => new external_value(PARAM_INT, 'Aggregate type'),
                    'assesstimestart' => new external_value(PARAM_INT, 'Assess start time'),
                    'assesstimefinish' => new external_value(PARAM_INT, 'Assess finish time'),
                    'scale' => new external_value(PARAM_INT, 'Scale'),
                    'maxbytes' => new external_value(PARAM_INT, 'Maximum attachment size'),
                    'maxattachments' => new external_value(PARAM_INT, 'Maximum number of attachments'),
                    'forcesubscribe' => new external_value(PARAM_INT, 'Force users to subscribe'),
                    'trackingtype' => new external_value(PARAM_INT, 'Subscription mode'),
                    'rsstype' => new external_value(PARAM_INT, 'RSS feed for this activity'),
                    'rssarticles' => new external_value(PARAM_INT, 'Number of RSS recent articles'),
                    'timemodified' => new external_value(PARAM_INT, 'Time modified'),
                    'warnafter' => new external_value(PARAM_INT, 'Post threshold for warning'),
                    'blockafter' => new external_value(PARAM_INT, 'Post threshold for blocking'),
                    'blockperiod' => new external_value(PARAM_INT, 'Time period for blocking'),
                    'completiondiscussions' => new external_value(PARAM_INT, 'Student must create discussions'),
                    'completionreplies' => new external_value(PARAM_INT, 'Student must post replies'),
                    'completionposts' => new external_value(PARAM_INT, 'Student must post discussions or replies'),
                    'cmid' => new external_value(PARAM_INT, 'Course module id'),
                    'numdiscussions' => new external_value(PARAM_INT, 'Number of discussions in the forum', VALUE_OPTIONAL),
                    'cancreatediscussions' => new external_value(PARAM_BOOL, 'If the user can create discussions', VALUE_OPTIONAL),
                    'lockdiscussionafter' => new external_value(PARAM_INT, 'After what period a discussion is locked', VALUE_OPTIONAL),
                    'istracked' => new external_value(PARAM_BOOL, 'If the user is tracking the forum', VALUE_OPTIONAL),
                    'unreadpostscount' => new external_value(PARAM_INT, 'The number of unread posts for tracked forums',
                        VALUE_OPTIONAL),
                ), 'forum'
            )
        );
    }

    /**
     * Get the forum posts in the specified discussion.
     *
     * @param   int $discussionid
     * @param   string $sortby
     * @param   string $sortdirection
     * @return  array
     */
    public static function get_discussion_posts(int $discussionid, ?string $sortby, ?string $sortdirection) {
        global $USER;
        // Validate the parameter.
        $params = self::validate_parameters(self::get_discussion_posts_parameters(), [
                'discussionid' => $discussionid,
                'sortby' => $sortby,
                'sortdirection' => $sortdirection,
            ]);
        $warnings = [];

        $vaultfactory = mod_forum\local\container::get_vault_factory();

        $discussionvault = $vaultfactory->get_discussion_vault();
        $discussion = $discussionvault->get_from_id($params['discussionid']);

        $forumvault = $vaultfactory->get_forum_vault();
        $forum = $forumvault->get_from_id($discussion->get_forum_id());

        $sortby = $params['sortby'];
        $sortdirection = $params['sortdirection'];
        $sortallowedvalues = ['id', 'created', 'modified'];
        $directionallowedvalues = ['ASC', 'DESC'];

        if (!in_array(strtolower($sortby), $sortallowedvalues)) {
            throw new invalid_parameter_exception('Invalid value for sortby parameter (value: ' . $sortby . '),' .
                'allowed values are: ' . implode(', ', $sortallowedvalues));
        }

        $sortdirection = strtoupper($sortdirection);
        if (!in_array($sortdirection, $directionallowedvalues)) {
            throw new invalid_parameter_exception('Invalid value for sortdirection parameter (value: ' . $sortdirection . '),' .
                'allowed values are: ' . implode(',', $directionallowedvalues));
        }

        $managerfactory = mod_forum\local\container::get_manager_factory();
        $capabilitymanager = $managerfactory->get_capability_manager($forum);

        $postvault = $vaultfactory->get_post_vault();
        $posts = $postvault->get_from_discussion_id(
                $USER,
                $discussion->get_id(),
                $capabilitymanager->can_view_any_private_reply($USER),
                "{$sortby} {$sortdirection}"
            );

        $builderfactory = mod_forum\local\container::get_builder_factory();
        $postbuilder = $builderfactory->get_exported_posts_builder();

        $legacydatamapper = mod_forum\local\container::get_legacy_data_mapper_factory();

        return [
            'posts' => $postbuilder->build($USER, [$forum], [$discussion], $posts),
            'ratinginfo' => \core_rating\external\util::get_rating_info(
                $legacydatamapper->get_forum_data_mapper()->to_legacy_object($forum),
                $forum->get_context(),
                'mod_forum',
                'post',
                $legacydatamapper->get_post_data_mapper()->to_legacy_objects($posts)
            ),
            'warnings' => $warnings,
        ];
    }

    /**
     * Describe the post parameters.
     *
     * @return external_function_parameters
     */
    public static function get_discussion_posts_parameters() {
        return new external_function_parameters ([
            'discussionid' => new external_value(PARAM_INT, 'The ID of the discussion from which to fetch posts.', VALUE_REQUIRED),
            'sortby' => new external_value(PARAM_ALPHA, 'Sort by this element: id, created or modified', VALUE_DEFAULT, 'created'),
            'sortdirection' => new external_value(PARAM_ALPHA, 'Sort direction: ASC or DESC', VALUE_DEFAULT, 'DESC')
        ]);
    }

    /**
     * Describe the post return format.
     *
     * @return external_single_structure
     */
    public static function get_discussion_posts_returns() {
        return new external_single_structure([
            'posts' => new external_multiple_structure(\mod_forum\local\exporters\post::get_read_structure()),
            'ratinginfo' => \core_rating\external\util::external_ratings_structure(),
            'warnings' => new external_warnings()
        ]);
    }

    /**
     * Describes the parameters for get_forum_discussion_posts.
     *
     * @return external_function_parameters
     * @since Moodle 2.7
     */
    public static function get_forum_discussion_posts_parameters() {
        return new external_function_parameters (
            array(
                'discussionid' => new external_value(PARAM_INT, 'discussion ID', VALUE_REQUIRED),
                'sortby' => new external_value(PARAM_ALPHA,
                    'sort by this element: id, created or modified', VALUE_DEFAULT, 'created'),
                'sortdirection' => new external_value(PARAM_ALPHA, 'sort direction: ASC or DESC', VALUE_DEFAULT, 'DESC')
            )
        );
    }

    /**
     * Returns a list of forum posts for a discussion
     *
     * @param int $discussionid the post ids
     * @param string $sortby sort by this element (id, created or modified)
     * @param string $sortdirection sort direction: ASC or DESC
     *
     * @return array the forum post details
     * @since Moodle 2.7
     * @todo MDL-65252 This will be removed in Moodle 4.1
     */
    public static function get_forum_discussion_posts($discussionid, $sortby = "created", $sortdirection = "DESC") {
        global $CFG, $DB, $USER, $PAGE;

        $posts = array();
        $warnings = array();

        // Validate the parameter.
        $params = self::validate_parameters(self::get_forum_discussion_posts_parameters(),
            array(
                'discussionid' => $discussionid,
                'sortby' => $sortby,
                'sortdirection' => $sortdirection));

        // Compact/extract functions are not recommended.
        $discussionid   = $params['discussionid'];
        $sortby         = $params['sortby'];
        $sortdirection  = $params['sortdirection'];

        $sortallowedvalues = array('id', 'created', 'modified');
        if (!in_array($sortby, $sortallowedvalues)) {
            throw new invalid_parameter_exception('Invalid value for sortby parameter (value: ' . $sortby . '),' .
                'allowed values are: ' . implode(',', $sortallowedvalues));
        }

        $sortdirection = strtoupper($sortdirection);
        $directionallowedvalues = array('ASC', 'DESC');
        if (!in_array($sortdirection, $directionallowedvalues)) {
            throw new invalid_parameter_exception('Invalid value for sortdirection parameter (value: ' . $sortdirection . '),' .
                'allowed values are: ' . implode(',', $directionallowedvalues));
        }

        $discussion = $DB->get_record('forum_discussions', array('id' => $discussionid), '*', MUST_EXIST);
        $forum = $DB->get_record('forum', array('id' => $discussion->forum), '*', MUST_EXIST);
        $course = $DB->get_record('course', array('id' => $forum->course), '*', MUST_EXIST);
        $cm = get_coursemodule_from_instance('forum', $forum->id, $course->id, false, MUST_EXIST);

        // Validate the module context. It checks everything that affects the module visibility (including groupings, etc..).
        $modcontext = context_module::instance($cm->id);
        self::validate_context($modcontext);

        // This require must be here, see mod/forum/discuss.php.
        require_once($CFG->dirroot . "/mod/forum/lib.php");

        // Check they have the view forum capability.
        require_capability('mod/forum:viewdiscussion', $modcontext, null, true, 'noviewdiscussionspermission', 'forum');

        if (! $post = forum_get_post_full($discussion->firstpost)) {
            throw new moodle_exception('notexists', 'forum');
        }

        // This function check groups, qanda, timed discussions, etc.
        if (!forum_user_can_see_post($forum, $discussion, $post, null, $cm)) {
            throw new moodle_exception('noviewdiscussionspermission', 'forum');
        }

        $canviewfullname = has_capability('moodle/site:viewfullnames', $modcontext);

        // We will add this field in the response.
        $canreply = forum_user_can_post($forum, $discussion, $USER, $cm, $course, $modcontext);

        $forumtracked = forum_tp_is_tracked($forum);

        $sort = 'p.' . $sortby . ' ' . $sortdirection;
        $allposts = forum_get_all_discussion_posts($discussion->id, $sort, $forumtracked);

        foreach ($allposts as $post) {
            if (!forum_user_can_see_post($forum, $discussion, $post, null, $cm, false)) {
                $warning = array();
                $warning['item'] = 'post';
                $warning['itemid'] = $post->id;
                $warning['warningcode'] = '1';
                $warning['message'] = 'You can\'t see this post';
                $warnings[] = $warning;
                continue;
            }

            // Function forum_get_all_discussion_posts adds postread field.
            // Note that the value returned can be a boolean or an integer. The WS expects a boolean.
            if (empty($post->postread)) {
                $post->postread = false;
            } else {
                $post->postread = true;
            }

            $post->isprivatereply = !empty($post->privatereplyto);

            $post->canreply = $canreply;
            if (!empty($post->children)) {
                $post->children = array_keys($post->children);
            } else {
                $post->children = array();
            }

            if (!forum_user_can_see_post($forum, $discussion, $post, null, $cm)) {
                // The post is available, but has been marked as deleted.
                // It will still be available but filled with a placeholder.
                $post->userid = null;
                $post->userfullname = null;
                $post->userpictureurl = null;

                $post->subject = get_string('privacy:request:delete:post:subject', 'mod_forum');
                $post->message = get_string('privacy:request:delete:post:message', 'mod_forum');

                $post->deleted = true;
                $posts[] = $post;

                continue;
            }
            $post->deleted = false;

            if (forum_is_author_hidden($post, $forum)) {
                $post->userid = null;
                $post->userfullname = null;
                $post->userpictureurl = null;
            } else {
                $user = new stdclass();
                $user->id = $post->userid;
                $user = username_load_fields_from_object($user, $post, null, array('picture', 'imagealt', 'email'));
                $post->userfullname = fullname($user, $canviewfullname);

                $userpicture = new user_picture($user);
                $userpicture->size = 1; // Size f1.
                $post->userpictureurl = $userpicture->get_url($PAGE)->out(false);
            }

            $post->subject = external_format_string($post->subject, $modcontext->id);
            // Rewrite embedded images URLs.
            list($post->message, $post->messageformat) =
                external_format_text($post->message, $post->messageformat, $modcontext->id, 'mod_forum', 'post', $post->id);

            // List attachments.
            if (!empty($post->attachment)) {
                $post->attachments = external_util::get_area_files($modcontext->id, 'mod_forum', 'attachment', $post->id);
            }
            $messageinlinefiles = external_util::get_area_files($modcontext->id, 'mod_forum', 'post', $post->id);
            if (!empty($messageinlinefiles)) {
                $post->messageinlinefiles = $messageinlinefiles;
            }
            // Post tags.
            $post->tags = \core_tag\external\util::get_item_tags('mod_forum', 'forum_posts', $post->id);

            $posts[] = $post;
        }

        $result = array();
        $result['posts'] = $posts;
        $result['ratinginfo'] = \core_rating\external\util::get_rating_info($forum, $modcontext, 'mod_forum', 'post', $posts);
        $result['warnings'] = $warnings;
        return $result;
    }

    /**
     * Describes the get_forum_discussion_posts return value.
     *
     * @return external_single_structure
     * @since Moodle 2.7
     */
    public static function get_forum_discussion_posts_returns() {
        return new external_single_structure(
            array(
                'posts' => new external_multiple_structure(
                        new external_single_structure(
                            array(
                                'id' => new external_value(PARAM_INT, 'Post id'),
                                'discussion' => new external_value(PARAM_INT, 'Discussion id'),
                                'parent' => new external_value(PARAM_INT, 'Parent id'),
                                'userid' => new external_value(PARAM_INT, 'User id'),
                                'created' => new external_value(PARAM_INT, 'Creation time'),
                                'modified' => new external_value(PARAM_INT, 'Time modified'),
                                'mailed' => new external_value(PARAM_INT, 'Mailed?'),
                                'subject' => new external_value(PARAM_TEXT, 'The post subject'),
                                'message' => new external_value(PARAM_RAW, 'The post message'),
                                'messageformat' => new external_format_value('message'),
                                'messagetrust' => new external_value(PARAM_INT, 'Can we trust?'),
                                'messageinlinefiles' => new external_files('post message inline files', VALUE_OPTIONAL),
                                'attachment' => new external_value(PARAM_RAW, 'Has attachments?'),
                                'attachments' => new external_files('attachments', VALUE_OPTIONAL),
                                'totalscore' => new external_value(PARAM_INT, 'The post message total score'),
                                'mailnow' => new external_value(PARAM_INT, 'Mail now?'),
                                'children' => new external_multiple_structure(new external_value(PARAM_INT, 'children post id')),
                                'canreply' => new external_value(PARAM_BOOL, 'The user can reply to posts?'),
                                'postread' => new external_value(PARAM_BOOL, 'The post was read'),
                                'userfullname' => new external_value(PARAM_TEXT, 'Post author full name'),
                                'userpictureurl' => new external_value(PARAM_URL, 'Post author picture.', VALUE_OPTIONAL),
                                'deleted' => new external_value(PARAM_BOOL, 'This post has been removed.'),
                                'isprivatereply' => new external_value(PARAM_BOOL, 'The post is a private reply'),
                                'tags' => new external_multiple_structure(
                                    \core_tag\external\tag_item_exporter::get_read_structure(), 'Tags', VALUE_OPTIONAL
                                ),
                            ), 'post'
                        )
                    ),
                'ratinginfo' => \core_rating\external\util::external_ratings_structure(),
                'warnings' => new external_warnings()
            )
        );
    }

    /**
     * Mark the get_forum_discussion_posts web service as deprecated.
     *
     * @return  bool
     */
    public static function get_forum_discussion_posts_is_deprecated() {
        return true;
    }

    /**
     * Describes the parameters for get_forum_discussions_paginated.
     *
     * @deprecated since 3.7
     * @return external_function_parameters
     * @since Moodle 2.8
     */
    public static function get_forum_discussions_paginated_parameters() {
        return new external_function_parameters (
            array(
                'forumid' => new external_value(PARAM_INT, 'forum instance id', VALUE_REQUIRED),
                'sortby' => new external_value(PARAM_ALPHA,
                    'sort by this element: id, timemodified, timestart or timeend', VALUE_DEFAULT, 'timemodified'),
                'sortdirection' => new external_value(PARAM_ALPHA, 'sort direction: ASC or DESC', VALUE_DEFAULT, 'DESC'),
                'page' => new external_value(PARAM_INT, 'current page', VALUE_DEFAULT, -1),
                'perpage' => new external_value(PARAM_INT, 'items per page', VALUE_DEFAULT, 0),
            )
        );
    }

    /**
     * Returns a list of forum discussions optionally sorted and paginated.
     *
     * @deprecated since 3.7
     * @param int $forumid the forum instance id
     * @param string $sortby sort by this element (id, timemodified, timestart or timeend)
     * @param string $sortdirection sort direction: ASC or DESC
     * @param int $page page number
     * @param int $perpage items per page
     *
     * @return array the forum discussion details including warnings
     * @since Moodle 2.8
     */
    public static function get_forum_discussions_paginated($forumid, $sortby = 'timemodified', $sortdirection = 'DESC',
            $page = -1, $perpage = 0) {
        global $CFG, $DB, $USER, $PAGE;

        require_once($CFG->dirroot . "/mod/forum/lib.php");

        $warnings = array();
        $discussions = array();

        $params = self::validate_parameters(self::get_forum_discussions_paginated_parameters(),
            array(
                'forumid' => $forumid,
                'sortby' => $sortby,
                'sortdirection' => $sortdirection,
                'page' => $page,
                'perpage' => $perpage
            )
        );

        // Compact/extract functions are not recommended.
        $forumid        = $params['forumid'];
        $sortby         = $params['sortby'];
        $sortdirection  = $params['sortdirection'];
        $page           = $params['page'];
        $perpage        = $params['perpage'];

        $sortallowedvalues = array('id', 'timemodified', 'timestart', 'timeend');
        if (!in_array($sortby, $sortallowedvalues)) {
            throw new invalid_parameter_exception('Invalid value for sortby parameter (value: ' . $sortby . '),' .
                'allowed values are: ' . implode(',', $sortallowedvalues));
        }

        $sortdirection = strtoupper($sortdirection);
        $directionallowedvalues = array('ASC', 'DESC');
        if (!in_array($sortdirection, $directionallowedvalues)) {
            throw new invalid_parameter_exception('Invalid value for sortdirection parameter (value: ' . $sortdirection . '),' .
                'allowed values are: ' . implode(',', $directionallowedvalues));
        }

        $forum = $DB->get_record('forum', array('id' => $forumid), '*', MUST_EXIST);
        $course = $DB->get_record('course', array('id' => $forum->course), '*', MUST_EXIST);
        $cm = get_coursemodule_from_instance('forum', $forum->id, $course->id, false, MUST_EXIST);

        // Validate the module context. It checks everything that affects the module visibility (including groupings, etc..).
        $modcontext = context_module::instance($cm->id);
        self::validate_context($modcontext);

        // Check they have the view forum capability.
        require_capability('mod/forum:viewdiscussion', $modcontext, null, true, 'noviewdiscussionspermission', 'forum');

        $sort = 'd.pinned DESC, d.' . $sortby . ' ' . $sortdirection;
        $alldiscussions = forum_get_discussions($cm, $sort, true, -1, -1, true, $page, $perpage, FORUM_POSTS_ALL_USER_GROUPS);

        if ($alldiscussions) {
            $canviewfullname = has_capability('moodle/site:viewfullnames', $modcontext);

            // Get the unreads array, this takes a forum id and returns data for all discussions.
            $unreads = array();
            if ($cantrack = forum_tp_can_track_forums($forum)) {
                if ($forumtracked = forum_tp_is_tracked($forum)) {
                    $unreads = forum_get_discussions_unread($cm);
                }
            }
            // The forum function returns the replies for all the discussions in a given forum.
            $canseeprivatereplies = has_capability('mod/forum:readprivatereplies', $modcontext);
            $canlock = has_capability('moodle/course:manageactivities', $modcontext, $USER);
            $replies = forum_count_discussion_replies($forumid, $sort, -1, $page, $perpage, $canseeprivatereplies);

            foreach ($alldiscussions as $discussion) {

                // This function checks for qanda forums.
                // Note that the forum_get_discussions returns as id the post id, not the discussion id so we need to do this.
                $discussionrec = clone $discussion;
                $discussionrec->id = $discussion->discussion;
                if (!forum_user_can_see_discussion($forum, $discussionrec, $modcontext)) {
                    $warning = array();
                    // Function forum_get_discussions returns forum_posts ids not forum_discussions ones.
                    $warning['item'] = 'post';
                    $warning['itemid'] = $discussion->id;
                    $warning['warningcode'] = '1';
                    $warning['message'] = 'You can\'t see this discussion';
                    $warnings[] = $warning;
                    continue;
                }

                $discussion->numunread = 0;
                if ($cantrack && $forumtracked) {
                    if (isset($unreads[$discussion->discussion])) {
                        $discussion->numunread = (int) $unreads[$discussion->discussion];
                    }
                }

                $discussion->numreplies = 0;
                if (!empty($replies[$discussion->discussion])) {
                    $discussion->numreplies = (int) $replies[$discussion->discussion]->replies;
                }

                $discussion->name = external_format_string($discussion->name, $modcontext->id);
                $discussion->subject = external_format_string($discussion->subject, $modcontext->id);
                // Rewrite embedded images URLs.
                list($discussion->message, $discussion->messageformat) =
                    external_format_text($discussion->message, $discussion->messageformat,
                                            $modcontext->id, 'mod_forum', 'post', $discussion->id);

                // List attachments.
                if (!empty($discussion->attachment)) {
                    $discussion->attachments = external_util::get_area_files($modcontext->id, 'mod_forum', 'attachment',
                                                                                $discussion->id);
                }
                $messageinlinefiles = external_util::get_area_files($modcontext->id, 'mod_forum', 'post', $discussion->id);
                if (!empty($messageinlinefiles)) {
                    $discussion->messageinlinefiles = $messageinlinefiles;
                }

                $discussion->locked = forum_discussion_is_locked($forum, $discussion);
                $discussion->canlock = $canlock;
                $discussion->canreply = forum_user_can_post($forum, $discussion, $USER, $cm, $course, $modcontext);

                if (forum_is_author_hidden($discussion, $forum)) {
                    $discussion->userid = null;
                    $discussion->userfullname = null;
                    $discussion->userpictureurl = null;

                    $discussion->usermodified = null;
                    $discussion->usermodifiedfullname = null;
                    $discussion->usermodifiedpictureurl = null;
                } else {
                    $picturefields = explode(',', user_picture::fields());

                    // Load user objects from the results of the query.
                    $user = new stdclass();
                    $user->id = $discussion->userid;
                    $user = username_load_fields_from_object($user, $discussion, null, $picturefields);
                    // Preserve the id, it can be modified by username_load_fields_from_object.
                    $user->id = $discussion->userid;
                    $discussion->userfullname = fullname($user, $canviewfullname);

                    $userpicture = new user_picture($user);
                    $userpicture->size = 1; // Size f1.
                    $discussion->userpictureurl = $userpicture->get_url($PAGE)->out(false);

                    $usermodified = new stdclass();
                    $usermodified->id = $discussion->usermodified;
                    $usermodified = username_load_fields_from_object($usermodified, $discussion, 'um', $picturefields);
                    // Preserve the id (it can be overwritten due to the prefixed $picturefields).
                    $usermodified->id = $discussion->usermodified;
                    $discussion->usermodifiedfullname = fullname($usermodified, $canviewfullname);

                    $userpicture = new user_picture($usermodified);
                    $userpicture->size = 1; // Size f1.
                    $discussion->usermodifiedpictureurl = $userpicture->get_url($PAGE)->out(false);
                }

                $discussions[] = $discussion;
            }
        }

        $result = array();
        $result['discussions'] = $discussions;
        $result['warnings'] = $warnings;
        return $result;

    }

    /**
     * Describes the get_forum_discussions_paginated return value.
     *
     * @deprecated since 3.7
     * @return external_single_structure
     * @since Moodle 2.8
     */
    public static function get_forum_discussions_paginated_returns() {
        return new external_single_structure(
            array(
                'discussions' => new external_multiple_structure(
                        new external_single_structure(
                            array(
                                'id' => new external_value(PARAM_INT, 'Post id'),
                                'name' => new external_value(PARAM_TEXT, 'Discussion name'),
                                'groupid' => new external_value(PARAM_INT, 'Group id'),
                                'timemodified' => new external_value(PARAM_INT, 'Time modified'),
                                'usermodified' => new external_value(PARAM_INT, 'The id of the user who last modified'),
                                'timestart' => new external_value(PARAM_INT, 'Time discussion can start'),
                                'timeend' => new external_value(PARAM_INT, 'Time discussion ends'),
                                'discussion' => new external_value(PARAM_INT, 'Discussion id'),
                                'parent' => new external_value(PARAM_INT, 'Parent id'),
                                'userid' => new external_value(PARAM_INT, 'User who started the discussion id'),
                                'created' => new external_value(PARAM_INT, 'Creation time'),
                                'modified' => new external_value(PARAM_INT, 'Time modified'),
                                'mailed' => new external_value(PARAM_INT, 'Mailed?'),
                                'subject' => new external_value(PARAM_TEXT, 'The post subject'),
                                'message' => new external_value(PARAM_RAW, 'The post message'),
                                'messageformat' => new external_format_value('message'),
                                'messagetrust' => new external_value(PARAM_INT, 'Can we trust?'),
                                'messageinlinefiles' => new external_files('post message inline files', VALUE_OPTIONAL),
                                'attachment' => new external_value(PARAM_RAW, 'Has attachments?'),
                                'attachments' => new external_files('attachments', VALUE_OPTIONAL),
                                'totalscore' => new external_value(PARAM_INT, 'The post message total score'),
                                'mailnow' => new external_value(PARAM_INT, 'Mail now?'),
                                'userfullname' => new external_value(PARAM_TEXT, 'Post author full name'),
                                'usermodifiedfullname' => new external_value(PARAM_TEXT, 'Post modifier full name'),
                                'userpictureurl' => new external_value(PARAM_URL, 'Post author picture.'),
                                'usermodifiedpictureurl' => new external_value(PARAM_URL, 'Post modifier picture.'),
                                'numreplies' => new external_value(PARAM_INT, 'The number of replies in the discussion'),
                                'numunread' => new external_value(PARAM_INT, 'The number of unread discussions.'),
                                'pinned' => new external_value(PARAM_BOOL, 'Is the discussion pinned'),
                                'locked' => new external_value(PARAM_BOOL, 'Is the discussion locked'),
                                'canreply' => new external_value(PARAM_BOOL, 'Can the user reply to the discussion'),
                                'canlock' => new external_value(PARAM_BOOL, 'Can the user lock the discussion'),
                            ), 'post'
                        )
                    ),
                'warnings' => new external_warnings()
            )
        );
    }

    /**
     * Describes the parameters for get_forum_discussions.
     *
     * @return external_function_parameters
     * @since Moodle 3.7
     */
    public static function get_forum_discussions_parameters() {
        return new external_function_parameters (
            array(
                'forumid' => new external_value(PARAM_INT, 'forum instance id', VALUE_REQUIRED),
                'sortorder' => new external_value(PARAM_INT,
                    'sort by this element: numreplies, , created or timemodified', VALUE_DEFAULT, -1),
                'page' => new external_value(PARAM_INT, 'current page', VALUE_DEFAULT, -1),
                'perpage' => new external_value(PARAM_INT, 'items per page', VALUE_DEFAULT, 0),
                'groupid' => new external_value(PARAM_INT, 'group id', VALUE_DEFAULT, 0),
            )
        );
    }

    /**
     * Returns a list of forum discussions optionally sorted and paginated.
     *
     * @param int $forumid the forum instance id
     * @param int $sortorder The sort order
     * @param int $page page number
     * @param int $perpage items per page
     * @param int $groupid the user course group
     *
     *
     * @return array the forum discussion details including warnings
     * @since Moodle 3.7
     */
    public static function get_forum_discussions(int $forumid, ?int $sortorder = -1, ?int $page = -1,
            ?int $perpage = 0, ?int $groupid = 0) {

        global $CFG, $DB, $USER;

        require_once($CFG->dirroot . "/mod/forum/lib.php");

        $warnings = array();
        $discussions = array();

        $params = self::validate_parameters(self::get_forum_discussions_parameters(),
            array(
                'forumid' => $forumid,
                'sortorder' => $sortorder,
                'page' => $page,
                'perpage' => $perpage,
                'groupid' => $groupid
            )
        );

        // Compact/extract functions are not recommended.
        $forumid        = $params['forumid'];
        $sortorder      = $params['sortorder'];
        $page           = $params['page'];
        $perpage        = $params['perpage'];
        $groupid        = $params['groupid'];

        $vaultfactory = \mod_forum\local\container::get_vault_factory();
        $discussionlistvault = $vaultfactory->get_discussions_in_forum_vault();

        $sortallowedvalues = array(
            $discussionlistvault::SORTORDER_LASTPOST_DESC,
            $discussionlistvault::SORTORDER_LASTPOST_ASC,
            $discussionlistvault::SORTORDER_CREATED_DESC,
            $discussionlistvault::SORTORDER_CREATED_ASC,
            $discussionlistvault::SORTORDER_REPLIES_DESC,
            $discussionlistvault::SORTORDER_REPLIES_ASC
        );

        // If sortorder not defined set a default one.
        if ($sortorder == -1) {
            $sortorder = $discussionlistvault::SORTORDER_LASTPOST_DESC;
        }

        if (!in_array($sortorder, $sortallowedvalues)) {
            throw new invalid_parameter_exception('Invalid value for sortorder parameter (value: ' . $sortorder . '),' .
                ' allowed values are: ' . implode(',', $sortallowedvalues));
        }

        $managerfactory = \mod_forum\local\container::get_manager_factory();
        $urlfactory = \mod_forum\local\container::get_url_factory();
        $legacydatamapperfactory = mod_forum\local\container::get_legacy_data_mapper_factory();

        $forumvault = $vaultfactory->get_forum_vault();
        $forum = $forumvault->get_from_id($forumid);
        if (!$forum) {
            throw new \moodle_exception("Unable to find forum with id {$forumid}");
        }
        $forumdatamapper = $legacydatamapperfactory->get_forum_data_mapper();
        $forumrecord = $forumdatamapper->to_legacy_object($forum);

        $capabilitymanager = $managerfactory->get_capability_manager($forum);

        $course = $DB->get_record('course', array('id' => $forum->get_course_id()), '*', MUST_EXIST);
        $cm = get_coursemodule_from_instance('forum', $forum->get_id(), $course->id, false, MUST_EXIST);

        // Validate the module context. It checks everything that affects the module visibility (including groupings, etc..).
        $modcontext = context_module::instance($cm->id);
        self::validate_context($modcontext);

        $canseeanyprivatereply = $capabilitymanager->can_view_any_private_reply($USER);

        // Check they have the view forum capability.
        if (!$capabilitymanager->can_view_discussions($USER)) {
            throw new moodle_exception('noviewdiscussionspermission', 'forum');
        }

        $alldiscussions = mod_forum_get_discussion_summaries($forum, $USER, $groupid, $sortorder, $page, $perpage);

        if ($alldiscussions) {
            $discussionids = array_keys($alldiscussions);

            $postvault = $vaultfactory->get_post_vault();
            $postdatamapper = $legacydatamapperfactory->get_post_data_mapper();
            // Return the reply count for each discussion in a given forum.
            $replies = $postvault->get_reply_count_for_discussion_ids($USER, $discussionids, $canseeanyprivatereply);
            // Return the first post for each discussion in a given forum.
            $firstposts = $postvault->get_first_post_for_discussion_ids($discussionids);

            // Get the unreads array, this takes a forum id and returns data for all discussions.
            $unreads = array();
            if ($cantrack = forum_tp_can_track_forums($forumrecord)) {
                if ($forumtracked = forum_tp_is_tracked($forumrecord)) {
                    $unreads = $postvault->get_unread_count_for_discussion_ids($USER, $discussionids, $canseeanyprivatereply);
                }
            }

            $canlock = $capabilitymanager->can_manage_forum($USER);

            $usercontext = context_user::instance($USER->id);
            $ufservice = core_favourites\service_factory::get_service_for_user_context($usercontext);

            $canfavourite = has_capability('mod/forum:cantogglefavourite', $modcontext, $USER);

            foreach ($alldiscussions as $discussionsummary) {
                $discussion = $discussionsummary->get_discussion();
                $firstpostauthor = $discussionsummary->get_first_post_author();
                $latestpostauthor = $discussionsummary->get_latest_post_author();

                // This function checks for qanda forums.
                $canviewdiscussion = $capabilitymanager->can_view_discussion($USER, $discussion);
                if (!$canviewdiscussion) {
                    $warning = array();
                    // Function forum_get_discussions returns forum_posts ids not forum_discussions ones.
                    $warning['item'] = 'post';
                    $warning['itemid'] = $discussion->get_id();
                    $warning['warningcode'] = '1';
                    $warning['message'] = 'You can\'t see this discussion';
                    $warnings[] = $warning;
                    continue;
                }

                $firstpost = $firstposts[$discussion->get_first_post_id()];
                $discussionobject = $postdatamapper->to_legacy_object($firstpost);
                // Fix up the types for these properties.
                $discussionobject->mailed = $discussionobject->mailed ? 1 : 0;
                $discussionobject->messagetrust = $discussionobject->messagetrust ? 1 : 0;
                $discussionobject->mailnow = $discussionobject->mailnow ? 1 : 0;
                $discussionobject->groupid = $discussion->get_group_id();
                $discussionobject->timemodified = $discussion->get_time_modified();
                $discussionobject->usermodified = $discussion->get_user_modified();
                $discussionobject->timestart = $discussion->get_time_start();
                $discussionobject->timeend = $discussion->get_time_end();
                $discussionobject->pinned = $discussion->is_pinned();

                $discussionobject->numunread = 0;
                if ($cantrack && $forumtracked) {
                    if (isset($unreads[$discussion->get_id()])) {
                        $discussionobject->numunread = (int) $unreads[$discussion->get_id()];
                    }
                }

                $discussionobject->numreplies = 0;
                if (!empty($replies[$discussion->get_id()])) {
                    $discussionobject->numreplies = (int) $replies[$discussion->get_id()];
                }

                $discussionobject->name = external_format_string($discussion->get_name(), $modcontext->id);
                $discussionobject->subject = external_format_string($discussionobject->subject, $modcontext->id);
                // Rewrite embedded images URLs.
                list($discussionobject->message, $discussionobject->messageformat) =
                    external_format_text($discussionobject->message, $discussionobject->messageformat,
                        $modcontext->id, 'mod_forum', 'post', $discussionobject->id);

                // List attachments.
                if (!empty($discussionobject->attachment)) {
                    $discussionobject->attachments = external_util::get_area_files($modcontext->id, 'mod_forum',
                        'attachment', $discussionobject->id);
                }
                $messageinlinefiles = external_util::get_area_files($modcontext->id, 'mod_forum', 'post',
                    $discussionobject->id);
                if (!empty($messageinlinefiles)) {
                    $discussionobject->messageinlinefiles = $messageinlinefiles;
                }

                $discussionobject->locked = $forum->is_discussion_locked($discussion);
                $discussionobject->canlock = $canlock;
                $discussionobject->starred = !empty($ufservice) ? $ufservice->favourite_exists('mod_forum', 'discussions',
                    $discussion->get_id(), $modcontext) : false;
                $discussionobject->canreply = $capabilitymanager->can_post_in_discussion($USER, $discussion);
                $discussionobject->canfavourite = $canfavourite;

                if (forum_is_author_hidden($discussionobject, $forumrecord)) {
                    $discussionobject->userid = null;
                    $discussionobject->userfullname = null;
                    $discussionobject->userpictureurl = null;

                    $discussionobject->usermodified = null;
                    $discussionobject->usermodifiedfullname = null;
                    $discussionobject->usermodifiedpictureurl = null;

                } else {
                    $discussionobject->userfullname = $firstpostauthor->get_full_name();
                    $discussionobject->userpictureurl = $urlfactory->get_author_profile_image_url($firstpostauthor, null, 2)
                        ->out(false);

                    $discussionobject->usermodifiedfullname = $latestpostauthor->get_full_name();
                    $discussionobject->usermodifiedpictureurl = $urlfactory->get_author_profile_image_url(
                        $latestpostauthor, null, 2)->out(false);
                }

                $discussions[] = (array) $discussionobject;
            }
        }
        $result = array();
        $result['discussions'] = $discussions;
        $result['warnings'] = $warnings;

        return $result;
    }

    /**
     * Describes the get_forum_discussions return value.
     *
     * @return external_single_structure
     * @since Moodle 3.7
     */
    public static function get_forum_discussions_returns() {
        return new external_single_structure(
            array(
                'discussions' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'id' => new external_value(PARAM_INT, 'Post id'),
                            'name' => new external_value(PARAM_TEXT, 'Discussion name'),
                            'groupid' => new external_value(PARAM_INT, 'Group id'),
                            'timemodified' => new external_value(PARAM_INT, 'Time modified'),
                            'usermodified' => new external_value(PARAM_INT, 'The id of the user who last modified'),
                            'timestart' => new external_value(PARAM_INT, 'Time discussion can start'),
                            'timeend' => new external_value(PARAM_INT, 'Time discussion ends'),
                            'discussion' => new external_value(PARAM_INT, 'Discussion id'),
                            'parent' => new external_value(PARAM_INT, 'Parent id'),
                            'userid' => new external_value(PARAM_INT, 'User who started the discussion id'),
                            'created' => new external_value(PARAM_INT, 'Creation time'),
                            'modified' => new external_value(PARAM_INT, 'Time modified'),
                            'mailed' => new external_value(PARAM_INT, 'Mailed?'),
                            'subject' => new external_value(PARAM_TEXT, 'The post subject'),
                            'message' => new external_value(PARAM_RAW, 'The post message'),
                            'messageformat' => new external_format_value('message'),
                            'messagetrust' => new external_value(PARAM_INT, 'Can we trust?'),
                            'messageinlinefiles' => new external_files('post message inline files', VALUE_OPTIONAL),
                            'attachment' => new external_value(PARAM_RAW, 'Has attachments?'),
                            'attachments' => new external_files('attachments', VALUE_OPTIONAL),
                            'totalscore' => new external_value(PARAM_INT, 'The post message total score'),
                            'mailnow' => new external_value(PARAM_INT, 'Mail now?'),
                            'userfullname' => new external_value(PARAM_TEXT, 'Post author full name'),
                            'usermodifiedfullname' => new external_value(PARAM_TEXT, 'Post modifier full name'),
                            'userpictureurl' => new external_value(PARAM_URL, 'Post author picture.'),
                            'usermodifiedpictureurl' => new external_value(PARAM_URL, 'Post modifier picture.'),
                            'numreplies' => new external_value(PARAM_INT, 'The number of replies in the discussion'),
                            'numunread' => new external_value(PARAM_INT, 'The number of unread discussions.'),
                            'pinned' => new external_value(PARAM_BOOL, 'Is the discussion pinned'),
                            'locked' => new external_value(PARAM_BOOL, 'Is the discussion locked'),
                            'starred' => new external_value(PARAM_BOOL, 'Is the discussion starred'),
                            'canreply' => new external_value(PARAM_BOOL, 'Can the user reply to the discussion'),
                            'canlock' => new external_value(PARAM_BOOL, 'Can the user lock the discussion'),
                            'canfavourite' => new external_value(PARAM_BOOL, 'Can the user star the discussion'),
                        ), 'post'
                    )
                ),
                'warnings' => new external_warnings()
            )
        );
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 2.9
     */
    public static function view_forum_parameters() {
        return new external_function_parameters(
            array(
                'forumid' => new external_value(PARAM_INT, 'forum instance id')
            )
        );
    }

    /**
     * Trigger the course module viewed event and update the module completion status.
     *
     * @param int $forumid the forum instance id
     * @return array of warnings and status result
     * @since Moodle 2.9
     * @throws moodle_exception
     */
    public static function view_forum($forumid) {
        global $DB, $CFG;
        require_once($CFG->dirroot . "/mod/forum/lib.php");

        $params = self::validate_parameters(self::view_forum_parameters(),
                                            array(
                                                'forumid' => $forumid
                                            ));
        $warnings = array();

        // Request and permission validation.
        $forum = $DB->get_record('forum', array('id' => $params['forumid']), '*', MUST_EXIST);
        list($course, $cm) = get_course_and_cm_from_instance($forum, 'forum');

        $context = context_module::instance($cm->id);
        self::validate_context($context);

        require_capability('mod/forum:viewdiscussion', $context, null, true, 'noviewdiscussionspermission', 'forum');

        // Call the forum/lib API.
        forum_view($forum, $course, $cm, $context);

        $result = array();
        $result['status'] = true;
        $result['warnings'] = $warnings;
        return $result;
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 2.9
     */
    public static function view_forum_returns() {
        return new external_single_structure(
            array(
                'status' => new external_value(PARAM_BOOL, 'status: true if success'),
                'warnings' => new external_warnings()
            )
        );
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 2.9
     */
    public static function view_forum_discussion_parameters() {
        return new external_function_parameters(
            array(
                'discussionid' => new external_value(PARAM_INT, 'discussion id')
            )
        );
    }

    /**
     * Trigger the discussion viewed event.
     *
     * @param int $discussionid the discussion id
     * @return array of warnings and status result
     * @since Moodle 2.9
     * @throws moodle_exception
     */
    public static function view_forum_discussion($discussionid) {
        global $DB, $CFG, $USER;
        require_once($CFG->dirroot . "/mod/forum/lib.php");

        $params = self::validate_parameters(self::view_forum_discussion_parameters(),
                                            array(
                                                'discussionid' => $discussionid
                                            ));
        $warnings = array();

        $discussion = $DB->get_record('forum_discussions', array('id' => $params['discussionid']), '*', MUST_EXIST);
        $forum = $DB->get_record('forum', array('id' => $discussion->forum), '*', MUST_EXIST);
        list($course, $cm) = get_course_and_cm_from_instance($forum, 'forum');

        // Validate the module context. It checks everything that affects the module visibility (including groupings, etc..).
        $modcontext = context_module::instance($cm->id);
        self::validate_context($modcontext);

        require_capability('mod/forum:viewdiscussion', $modcontext, null, true, 'noviewdiscussionspermission', 'forum');

        // Call the forum/lib API.
        forum_discussion_view($modcontext, $forum, $discussion);

        // Mark as read if required.
        if (!$CFG->forum_usermarksread && forum_tp_is_tracked($forum)) {
            forum_tp_mark_discussion_read($USER, $discussion->id);
        }

        $result = array();
        $result['status'] = true;
        $result['warnings'] = $warnings;
        return $result;
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 2.9
     */
    public static function view_forum_discussion_returns() {
        return new external_single_structure(
            array(
                'status' => new external_value(PARAM_BOOL, 'status: true if success'),
                'warnings' => new external_warnings()
            )
        );
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 3.0
     */
    public static function add_discussion_post_parameters() {
        return new external_function_parameters(
            array(
                'postid' => new external_value(PARAM_INT, 'the post id we are going to reply to
                                                (can be the initial discussion post'),
                'subject' => new external_value(PARAM_TEXT, 'new post subject'),
                'message' => new external_value(PARAM_RAW, 'new post message (html assumed if messageformat is not provided)'),
                'options' => new external_multiple_structure (
                    new external_single_structure(
                        array(
                            'name' => new external_value(PARAM_ALPHANUM,
                                        'The allowed keys (value format) are:
                                        discussionsubscribe (bool); subscribe to the discussion?, default to true
                                        private (bool); make this reply private to the author of the parent post, default to false.
                                        inlineattachmentsid              (int); the draft file area id for inline attachments
                                        attachmentsid       (int); the draft file area id for attachments
                                        topreferredformat (bool); convert the message & messageformat to FORMAT_HTML, defaults to false
                            '),
                            'value' => new external_value(PARAM_RAW, 'the value of the option,
                                                            this param is validated in the external function.'
                        )
                    )
                ), 'Options', VALUE_DEFAULT, array()),
                'messageformat' => new external_format_value('message', VALUE_DEFAULT)
            )
        );
    }

    /**
     * Create new posts into an existing discussion.
     *
     * @param int $postid the post id we are going to reply to
     * @param string $subject new post subject
     * @param string $message new post message (html assumed if messageformat is not provided)
     * @param array $options optional settings
     * @param string $messageformat The format of the message, defaults to FORMAT_HTML for BC
     * @return array of warnings and the new post id
     * @since Moodle 3.0
     * @throws moodle_exception
     */
    public static function add_discussion_post($postid, $subject, $message, $options = array(), $messageformat = FORMAT_HTML) {
        global $CFG, $USER;
        require_once($CFG->dirroot . "/mod/forum/lib.php");

        // Get all the factories that are required.
        $vaultfactory = mod_forum\local\container::get_vault_factory();
        $entityfactory = mod_forum\local\container::get_entity_factory();
        $datamapperfactory = mod_forum\local\container::get_legacy_data_mapper_factory();
        $managerfactory = mod_forum\local\container::get_manager_factory();
        $discussionvault = $vaultfactory->get_discussion_vault();
        $forumvault = $vaultfactory->get_forum_vault();
        $discussiondatamapper = $datamapperfactory->get_discussion_data_mapper();
        $forumdatamapper = $datamapperfactory->get_forum_data_mapper();

        $params = self::validate_parameters(self::add_discussion_post_parameters(),
            array(
                'postid' => $postid,
                'subject' => $subject,
                'message' => $message,
                'options' => $options,
                'messageformat' => $messageformat,
            )
        );

        $warnings = array();

        if (!$parent = forum_get_post_full($params['postid'])) {
            throw new moodle_exception('invalidparentpostid', 'forum');
        }

        if (!$discussion = $discussionvault->get_from_id($parent->discussion)) {
            throw new moodle_exception('notpartofdiscussion', 'forum');
        }

        // Request and permission validation.
        $forum = $forumvault->get_from_id($discussion->get_forum_id());
        $capabilitymanager = $managerfactory->get_capability_manager($forum);
        $course = $forum->get_course_record();
        $cm = $forum->get_course_module_record();

        $discussionrecord = $discussiondatamapper->to_legacy_object($discussion);
        $forumrecord = $forumdatamapper->to_legacy_object($forum);
        $context = context_module::instance($cm->id);
        self::validate_context($context);

        // Validate options.
        $options = array(
            'discussionsubscribe' => true,
            'private'             => false,
            'inlineattachmentsid' => 0,
            'attachmentsid' => null,
            'topreferredformat'   => false
        );
        foreach ($params['options'] as $option) {
            $name = trim($option['name']);
            switch ($name) {
                case 'discussionsubscribe':
                    $value = clean_param($option['value'], PARAM_BOOL);
                    break;
                case 'private':
                    $value = clean_param($option['value'], PARAM_BOOL);
                    break;
                case 'inlineattachmentsid':
                    $value = clean_param($option['value'], PARAM_INT);
                    break;
                case 'attachmentsid':
                    $value = clean_param($option['value'], PARAM_INT);
                    // Ensure that the user has permissions to create attachments.
                    if (!has_capability('mod/forum:createattachment', $context)) {
                        $value = 0;
                    }
                    break;
                case 'topreferredformat':
                    $value = clean_param($option['value'], PARAM_BOOL);
                    break;
                default:
                    throw new moodle_exception('errorinvalidparam', 'webservice', '', $name);
            }
            $options[$name] = $value;
        }

        if (!$capabilitymanager->can_post_in_discussion($USER, $discussion)) {
            throw new moodle_exception('nopostforum', 'forum');
        }

        $thresholdwarning = forum_check_throttling($forumrecord, $cm);
        forum_check_blocking_threshold($thresholdwarning);

        // If we want to force a conversion to the preferred format, let's do it now.
        if ($options['topreferredformat']) {
            // We always are going to honor the preferred format. We are creating a new post.
            $preferredformat = editors_get_preferred_format();
            // If the post is not HTML and the preferred format is HTML, convert to it.
            if ($params['messageformat'] != FORMAT_HTML and $preferredformat == FORMAT_HTML) {
                $params['message'] = format_text($params['message'], $params['messageformat'], ['context' => $context]);
            }
            $params['messageformat'] = $preferredformat;
        }

        // Create the post.
        $post = new stdClass();
        $post->discussion = $discussion->get_id();
        $post->parent = $parent->id;
        $post->subject = $params['subject'];
        $post->message = $params['message'];
        $post->messageformat = $params['messageformat'];
        $post->messagetrust = trusttext_trusted($context);
        $post->itemid = $options['inlineattachmentsid'];
        $post->attachments = $options['attachmentsid'];
        $post->isprivatereply = $options['private'];
        $post->deleted = 0;
        $fakemform = $post->attachments;
        if ($postid = forum_add_new_post($post, $fakemform)) {

            $post->id = $postid;

            // Trigger events and completion.
            $params = array(
                'context' => $context,
                'objectid' => $post->id,
                'other' => array(
                    'discussionid' => $discussion->get_id(),
                    'forumid' => $forum->get_id(),
                    'forumtype' => $forum->get_type(),
                )
            );
            $event = \mod_forum\event\post_created::create($params);
            $event->add_record_snapshot('forum_posts', $post);
            $event->add_record_snapshot('forum_discussions', $discussionrecord);
            $event->trigger();

            // Update completion state.
            $completion = new completion_info($course);
            if ($completion->is_enabled($cm) &&
                    ($forum->get_completion_replies() || $forum->get_completion_posts())) {
                $completion->update_state($cm, COMPLETION_COMPLETE);
            }

            $settings = new stdClass();
            $settings->discussionsubscribe = $options['discussionsubscribe'];
            forum_post_subscription($settings, $forumrecord, $discussionrecord);
        } else {
            throw new moodle_exception('couldnotadd', 'forum');
        }

        $builderfactory = \mod_forum\local\container::get_builder_factory();
        $exportedpostsbuilder = $builderfactory->get_exported_posts_builder();
        $postentity = $entityfactory->get_post_from_stdClass($post);
        $exportedposts = $exportedpostsbuilder->build($USER, [$forum], [$discussion], [$postentity]);
        $exportedpost = $exportedposts[0];

        $message = [];
        $message[] = [
            'type' => 'success',
            'message' => get_string("postaddedsuccess", "forum")
        ];

        $message[] = [
            'type' => 'success',
            'message' => get_string("postaddedtimeleft", "forum", format_time($CFG->maxeditingtime))
        ];

        $result = array();
        $result['postid'] = $postid;
        $result['warnings'] = $warnings;
        $result['post'] = $exportedpost;
        $result['messages'] = $message;
        return $result;
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 3.0
     */
    public static function add_discussion_post_returns() {
        return new external_single_structure(
            array(
                'postid' => new external_value(PARAM_INT, 'new post id'),
                'warnings' => new external_warnings(),
                'post' => post_exporter::get_read_structure(),
                'messages' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'type' => new external_value(PARAM_TEXT, "The classification to be used in the client side", VALUE_REQUIRED),
                            'message' => new external_value(PARAM_TEXT,'untranslated english message to explain the warning', VALUE_REQUIRED)
                        ), 'Messages'), 'list of warnings', VALUE_OPTIONAL
                ),
                //'alertmessage' => new external_value(PARAM_RAW, 'Success message to be displayed to the user.'),
            )
        );
    }

    /**
     * Toggle the favouriting value for the discussion provided
     *
     * @param int $discussionid The discussion we need to favourite
     * @param bool $targetstate The state of the favourite value
     * @return array The exported discussion
     */
    public static function toggle_favourite_state($discussionid, $targetstate) {
        global $DB, $PAGE, $USER;

        $params = self::validate_parameters(self::toggle_favourite_state_parameters(), [
            'discussionid' => $discussionid,
            'targetstate' => $targetstate
        ]);

        $vaultfactory = mod_forum\local\container::get_vault_factory();
        // Get the discussion vault and the corresponding discussion entity.
        $discussionvault = $vaultfactory->get_discussion_vault();
        $discussion = $discussionvault->get_from_id($params['discussionid']);

        $forumvault = $vaultfactory->get_forum_vault();
        $forum = $forumvault->get_from_id($discussion->get_forum_id());
        $forumcontext = $forum->get_context();
        self::validate_context($forumcontext);

        $managerfactory = mod_forum\local\container::get_manager_factory();
        $capabilitymanager = $managerfactory->get_capability_manager($forum);

        // Does the user have the ability to favourite the discussion?
        if (!$capabilitymanager->can_favourite_discussion($USER)) {
            throw new moodle_exception('cannotfavourite', 'forum');
        }
        $usercontext = context_user::instance($USER->id);
        $ufservice = \core_favourites\service_factory::get_service_for_user_context($usercontext);
        $isfavourited = $ufservice->favourite_exists('mod_forum', 'discussions', $discussion->get_id(), $forumcontext);

        $favouritefunction = $targetstate ? 'create_favourite' : 'delete_favourite';
        if ($isfavourited != (bool) $params['targetstate']) {
            $ufservice->{$favouritefunction}('mod_forum', 'discussions', $discussion->get_id(), $forumcontext);
        }

        $exporterfactory = mod_forum\local\container::get_exporter_factory();
        $builder = mod_forum\local\container::get_builder_factory()->get_exported_discussion_builder();
        $favourited = ($builder->is_favourited($discussion, $forumcontext, $USER) ? [$discussion->get_id()] : []);
        $exporter = $exporterfactory->get_discussion_exporter($USER, $forum, $discussion, [], $favourited);
        return $exporter->export($PAGE->get_renderer('mod_forum'));
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 3.0
     */
    public static function toggle_favourite_state_returns() {
        return discussion_exporter::get_read_structure();
    }

    /**
     * Defines the parameters for the toggle_favourite_state method
     *
     * @return external_function_parameters
     */
    public static function toggle_favourite_state_parameters() {
        return new external_function_parameters(
            [
                'discussionid' => new external_value(PARAM_INT, 'The discussion to subscribe or unsubscribe'),
                'targetstate' => new external_value(PARAM_BOOL, 'The target state')
            ]
        );
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 3.0
     */
    public static function add_discussion_parameters() {
        return new external_function_parameters(
            array(
                'forumid' => new external_value(PARAM_INT, 'Forum instance ID'),
                'subject' => new external_value(PARAM_TEXT, 'New Discussion subject'),
                'message' => new external_value(PARAM_RAW, 'New Discussion message (only html format allowed)'),
                'groupid' => new external_value(PARAM_INT, 'The group, default to 0', VALUE_DEFAULT, 0),
                'options' => new external_multiple_structure (
                    new external_single_structure(
                        array(
                            'name' => new external_value(PARAM_ALPHANUM,
                                        'The allowed keys (value format) are:
                                        discussionsubscribe (bool); subscribe to the discussion?, default to true
                                        discussionpinned    (bool); is the discussion pinned, default to false
                                        inlineattachmentsid              (int); the draft file area id for inline attachments
                                        attachmentsid       (int); the draft file area id for attachments
                            '),
                            'value' => new external_value(PARAM_RAW, 'The value of the option,
                                                            This param is validated in the external function.'
                        )
                    )
                ), 'Options', VALUE_DEFAULT, array())
            )
        );
    }

    /**
     * Add a new discussion into an existing forum.
     *
     * @param int $forumid the forum instance id
     * @param string $subject new discussion subject
     * @param string $message new discussion message (only html format allowed)
     * @param int $groupid the user course group
     * @param array $options optional settings
     * @return array of warnings and the new discussion id
     * @since Moodle 3.0
     * @throws moodle_exception
     */
    public static function add_discussion($forumid, $subject, $message, $groupid = 0, $options = array()) {
        global $DB, $CFG;
        require_once($CFG->dirroot . "/mod/forum/lib.php");

        $params = self::validate_parameters(self::add_discussion_parameters(),
                                            array(
                                                'forumid' => $forumid,
                                                'subject' => $subject,
                                                'message' => $message,
                                                'groupid' => $groupid,
                                                'options' => $options
                                            ));

        $warnings = array();

        // Request and permission validation.
        $forum = $DB->get_record('forum', array('id' => $params['forumid']), '*', MUST_EXIST);
        list($course, $cm) = get_course_and_cm_from_instance($forum, 'forum');

        $context = context_module::instance($cm->id);
        self::validate_context($context);

        // Validate options.
        $options = array(
            'discussionsubscribe' => true,
            'discussionpinned' => false,
            'inlineattachmentsid' => 0,
            'attachmentsid' => null
        );
        foreach ($params['options'] as $option) {
            $name = trim($option['name']);
            switch ($name) {
                case 'discussionsubscribe':
                    $value = clean_param($option['value'], PARAM_BOOL);
                    break;
                case 'discussionpinned':
                    $value = clean_param($option['value'], PARAM_BOOL);
                    break;
                case 'inlineattachmentsid':
                    $value = clean_param($option['value'], PARAM_INT);
                    break;
                case 'attachmentsid':
                    $value = clean_param($option['value'], PARAM_INT);
                    // Ensure that the user has permissions to create attachments.
                    if (!has_capability('mod/forum:createattachment', $context)) {
                        $value = 0;
                    }
                    break;
                default:
                    throw new moodle_exception('errorinvalidparam', 'webservice', '', $name);
            }
            $options[$name] = $value;
        }

        // Normalize group.
        if (!groups_get_activity_groupmode($cm)) {
            // Groups not supported, force to -1.
            $groupid = -1;
        } else {
            // Check if we receive the default or and empty value for groupid,
            // in this case, get the group for the user in the activity.
            if (empty($params['groupid'])) {
                $groupid = groups_get_activity_group($cm);
            } else {
                // Here we rely in the group passed, forum_user_can_post_discussion will validate the group.
                $groupid = $params['groupid'];
            }
        }

        if (!forum_user_can_post_discussion($forum, $groupid, -1, $cm, $context)) {
            throw new moodle_exception('cannotcreatediscussion', 'forum');
        }

        $thresholdwarning = forum_check_throttling($forum, $cm);
        forum_check_blocking_threshold($thresholdwarning);

        // Create the discussion.
        $discussion = new stdClass();
        $discussion->course = $course->id;
        $discussion->forum = $forum->id;
        $discussion->message = $params['message'];
        $discussion->messageformat = FORMAT_HTML;   // Force formatting for now.
        $discussion->messagetrust = trusttext_trusted($context);
        $discussion->itemid = $options['inlineattachmentsid'];
        $discussion->groupid = $groupid;
        $discussion->mailnow = 0;
        $discussion->subject = $params['subject'];
        $discussion->name = $discussion->subject;
        $discussion->timestart = 0;
        $discussion->timeend = 0;
        $discussion->timelocked = 0;
        $discussion->attachments = $options['attachmentsid'];

        if (has_capability('mod/forum:pindiscussions', $context) && $options['discussionpinned']) {
            $discussion->pinned = FORUM_DISCUSSION_PINNED;
        } else {
            $discussion->pinned = FORUM_DISCUSSION_UNPINNED;
        }
        $fakemform = $options['attachmentsid'];
        if ($discussionid = forum_add_discussion($discussion, $fakemform)) {

            $discussion->id = $discussionid;

            // Trigger events and completion.

            $params = array(
                'context' => $context,
                'objectid' => $discussion->id,
                'other' => array(
                    'forumid' => $forum->id,
                )
            );
            $event = \mod_forum\event\discussion_created::create($params);
            $event->add_record_snapshot('forum_discussions', $discussion);
            $event->trigger();

            $completion = new completion_info($course);
            if ($completion->is_enabled($cm) &&
                    ($forum->completiondiscussions || $forum->completionposts)) {
                $completion->update_state($cm, COMPLETION_COMPLETE);
            }

            $settings = new stdClass();
            $settings->discussionsubscribe = $options['discussionsubscribe'];
            forum_post_subscription($settings, $forum, $discussion);
        } else {
            throw new moodle_exception('couldnotadd', 'forum');
        }

        $result = array();
        $result['discussionid'] = $discussionid;
        $result['warnings'] = $warnings;
        return $result;
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 3.0
     */
    public static function add_discussion_returns() {
        return new external_single_structure(
            array(
                'discussionid' => new external_value(PARAM_INT, 'New Discussion ID'),
                'warnings' => new external_warnings()
            )
        );
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 3.1
     */
    public static function can_add_discussion_parameters() {
        return new external_function_parameters(
            array(
                'forumid' => new external_value(PARAM_INT, 'Forum instance ID'),
                'groupid' => new external_value(PARAM_INT, 'The group to check, default to active group.
                                                Use -1 to check if the user can post in all the groups.', VALUE_DEFAULT, null)
            )
        );
    }

    /**
     * Check if the current user can add discussions in the given forum (and optionally for the given group).
     *
     * @param int $forumid the forum instance id
     * @param int $groupid the group to check, default to active group. Use -1 to check if the user can post in all the groups.
     * @return array of warnings and the status (true if the user can add discussions)
     * @since Moodle 3.1
     * @throws moodle_exception
     */
    public static function can_add_discussion($forumid, $groupid = null) {
        global $DB, $CFG;
        require_once($CFG->dirroot . "/mod/forum/lib.php");

        $params = self::validate_parameters(self::can_add_discussion_parameters(),
                                            array(
                                                'forumid' => $forumid,
                                                'groupid' => $groupid,
                                            ));
        $warnings = array();

        // Request and permission validation.
        $forum = $DB->get_record('forum', array('id' => $params['forumid']), '*', MUST_EXIST);
        list($course, $cm) = get_course_and_cm_from_instance($forum, 'forum');

        $context = context_module::instance($cm->id);
        self::validate_context($context);

        $status = forum_user_can_post_discussion($forum, $params['groupid'], -1, $cm, $context);

        $result = array();
        $result['status'] = $status;
        $result['canpindiscussions'] = has_capability('mod/forum:pindiscussions', $context);
        $result['cancreateattachment'] = forum_can_create_attachment($forum, $context);
        $result['warnings'] = $warnings;
        return $result;
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 3.1
     */
    public static function can_add_discussion_returns() {
        return new external_single_structure(
            array(
                'status' => new external_value(PARAM_BOOL, 'True if the user can add discussions, false otherwise.'),
                'canpindiscussions' => new external_value(PARAM_BOOL, 'True if the user can pin discussions, false otherwise.',
                    VALUE_OPTIONAL),
                'cancreateattachment' => new external_value(PARAM_BOOL, 'True if the user can add attachments, false otherwise.',
                    VALUE_OPTIONAL),
                'warnings' => new external_warnings()
            )
        );
    }

    /**
     * Describes the parameters for get_forum_access_information.
     *
     * @return external_external_function_parameters
     * @since Moodle 3.7
     */
    public static function get_forum_access_information_parameters() {
        return new external_function_parameters (
            array(
                'forumid' => new external_value(PARAM_INT, 'Forum instance id.')
            )
        );
    }

    /**
     * Return access information for a given forum.
     *
     * @param int $forumid forum instance id
     * @return array of warnings and the access information
     * @since Moodle 3.7
     * @throws  moodle_exception
     */
    public static function get_forum_access_information($forumid) {
        global $DB;

        $params = self::validate_parameters(self::get_forum_access_information_parameters(), array('forumid' => $forumid));

        // Request and permission validation.
        $forum = $DB->get_record('forum', array('id' => $params['forumid']), '*', MUST_EXIST);
        $cm = get_coursemodule_from_instance('forum', $forum->id);

        $context = context_module::instance($cm->id);
        self::validate_context($context);

        $result = array();
        // Return all the available capabilities.
        $capabilities = load_capability_def('mod_forum');
        foreach ($capabilities as $capname => $capdata) {
            // Get fields like cansubmit so it is consistent with the access_information function implemented in other modules.
            $field = 'can' . str_replace('mod/forum:', '', $capname);
            $result[$field] = has_capability($capname, $context);
        }

        $result['warnings'] = array();
        return $result;
    }

    /**
     * Describes the get_forum_access_information return value.
     *
     * @return external_single_structure
     * @since Moodle 3.7
     */
    public static function get_forum_access_information_returns() {

        $structure = array(
            'warnings' => new external_warnings()
        );

        $capabilities = load_capability_def('mod_forum');
        foreach ($capabilities as $capname => $capdata) {
            // Get fields like cansubmit so it is consistent with the access_information function implemented in other modules.
            $field = 'can' . str_replace('mod/forum:', '', $capname);
            $structure[$field] = new external_value(PARAM_BOOL, 'Whether the user has the capability ' . $capname . ' allowed.',
                VALUE_OPTIONAL);
        }

        return new external_single_structure($structure);
    }

    /**
     * Set the subscription state.
     *
     * @param   int     $forumid
     * @param   int     $discussionid
     * @param   bool    $targetstate
     * @return  \stdClass
     */
    public static function set_subscription_state($forumid, $discussionid, $targetstate) {
        global $PAGE, $USER;

        $params = self::validate_parameters(self::set_subscription_state_parameters(), [
            'forumid' => $forumid,
            'discussionid' => $discussionid,
            'targetstate' => $targetstate
        ]);

        $vaultfactory = mod_forum\local\container::get_vault_factory();
        $forumvault = $vaultfactory->get_forum_vault();
        $forum = $forumvault->get_from_id($params['forumid']);
        $coursemodule = $forum->get_course_module_record();
        $context = $forum->get_context();

        self::validate_context($context);

        $discussionvault = $vaultfactory->get_discussion_vault();
        $discussion = $discussionvault->get_from_id($params['discussionid']);
        $legacydatamapperfactory = mod_forum\local\container::get_legacy_data_mapper_factory();

        $forumrecord = $legacydatamapperfactory->get_forum_data_mapper()->to_legacy_object($forum);
        $discussionrecord = $legacydatamapperfactory->get_discussion_data_mapper()->to_legacy_object($discussion);

        if (!\mod_forum\subscriptions::is_subscribable($forumrecord)) {
            // Nothing to do. We won't actually output any content here though.
            throw new \moodle_exception('cannotsubscribe', 'mod_forum');
        }

        $issubscribed = \mod_forum\subscriptions::is_subscribed(
            $USER->id,
            $forumrecord,
            $discussion->get_id(),
            $coursemodule
        );

        // If the current state doesn't equal the desired state then update the current
        // state to the desired state.
        if ($issubscribed != (bool) $params['targetstate']) {
            if ($params['targetstate']) {
                \mod_forum\subscriptions::subscribe_user_to_discussion($USER->id, $discussionrecord, $context);
            } else {
                \mod_forum\subscriptions::unsubscribe_user_from_discussion($USER->id, $discussionrecord, $context);
            }
        }

        $exporterfactory = mod_forum\local\container::get_exporter_factory();
        $exporter = $exporterfactory->get_discussion_exporter($USER, $forum, $discussion);
        return $exporter->export($PAGE->get_renderer('mod_forum'));
    }

    /**
     * Returns description of method parameters.
     *
     * @return external_function_parameters
     */
    public static function set_subscription_state_parameters() {
        return new external_function_parameters(
            [
                'forumid' => new external_value(PARAM_INT, 'Forum that the discussion is in'),
                'discussionid' => new external_value(PARAM_INT, 'The discussion to subscribe or unsubscribe'),
                'targetstate' => new external_value(PARAM_BOOL, 'The target state')
            ]
        );
    }

    /**
     * Returns description of method result value.
     *
     * @return external_description
     */
    public static function set_subscription_state_returns() {
        return discussion_exporter::get_read_structure();
    }

    /**
     * Set the lock state.
     *
     * @param   int     $forumid
     * @param   int     $discussionid
     * @param   string    $targetstate
     * @return  \stdClass
     */
    public static function set_lock_state($forumid, $discussionid, $targetstate) {
        global $DB, $PAGE, $USER;

        $params = self::validate_parameters(self::set_lock_state_parameters(), [
            'forumid' => $forumid,
            'discussionid' => $discussionid,
            'targetstate' => $targetstate
        ]);

        $vaultfactory = mod_forum\local\container::get_vault_factory();
        $forumvault = $vaultfactory->get_forum_vault();
        $forum = $forumvault->get_from_id($params['forumid']);

        $managerfactory = mod_forum\local\container::get_manager_factory();
        $capabilitymanager = $managerfactory->get_capability_manager($forum);
        if (!$capabilitymanager->can_manage_forum($USER)) {
            throw new moodle_exception('errorcannotlock', 'forum');
        }

        // If the targetstate(currentstate) is not 0 then it should be set to the current time.
        $lockedvalue = $targetstate ? 0 : time();
        self::validate_context($forum->get_context());

        $discussionvault = $vaultfactory->get_discussion_vault();
        $discussion = $discussionvault->get_from_id($params['discussionid']);

        // If the current state doesn't equal the desired state then update the current.
        // state to the desired state.
        $discussion->toggle_locked_state($lockedvalue);
        $response = $discussionvault->update_discussion($discussion);
        $discussion = !$response ? $response : $discussion;
        $exporterfactory = mod_forum\local\container::get_exporter_factory();
        $exporter = $exporterfactory->get_discussion_exporter($USER, $forum, $discussion);
        return $exporter->export($PAGE->get_renderer('mod_forum'));
    }

    /**
     * Returns description of method parameters.
     *
     * @return external_function_parameters
     */
    public static function set_lock_state_parameters() {
        return new external_function_parameters(
            [
                'forumid' => new external_value(PARAM_INT, 'Forum that the discussion is in'),
                'discussionid' => new external_value(PARAM_INT, 'The discussion to lock / unlock'),
                'targetstate' => new external_value(PARAM_INT, 'The timestamp for the lock state')
            ]
        );
    }

    /**
     * Returns description of method result value.
     *
     * @return external_description
     */
    public static function set_lock_state_returns() {
        return new external_single_structure([
            'id' => new external_value(PARAM_INT, 'The discussion we are locking.'),
            'locked' => new external_value(PARAM_BOOL, 'The locked state of the discussion.'),
            'times' => new external_single_structure([
                'locked' => new external_value(PARAM_INT, 'The locked time of the discussion.'),
            ])
        ]);
    }

    /**
     * Set the pin state.
     *
     * @param   int     $discussionid
     * @param   bool    $targetstate
     * @return  \stdClass
     */
    public static function set_pin_state($discussionid, $targetstate) {
        global $PAGE, $USER;
        $params = self::validate_parameters(self::set_pin_state_parameters(), [
            'discussionid' => $discussionid,
            'targetstate' => $targetstate,
        ]);
        $vaultfactory = mod_forum\local\container::get_vault_factory();
        $managerfactory = mod_forum\local\container::get_manager_factory();
        $forumvault = $vaultfactory->get_forum_vault();
        $discussionvault = $vaultfactory->get_discussion_vault();
        $discussion = $discussionvault->get_from_id($params['discussionid']);
        $forum = $forumvault->get_from_id($discussion->get_forum_id());
        $capabilitymanager = $managerfactory->get_capability_manager($forum);

        self::validate_context($forum->get_context());

        $legacydatamapperfactory = mod_forum\local\container::get_legacy_data_mapper_factory();
        if (!$capabilitymanager->can_pin_discussions($USER)) {
            // Nothing to do. We won't actually output any content here though.
            throw new \moodle_exception('cannotpindiscussions', 'mod_forum');
        }

        $discussion->set_pinned($targetstate);
        $discussionvault->update_discussion($discussion);

        $exporterfactory = mod_forum\local\container::get_exporter_factory();
        $exporter = $exporterfactory->get_discussion_exporter($USER, $forum, $discussion);
        return $exporter->export($PAGE->get_renderer('mod_forum'));
    }

    /**
     * Returns description of method parameters.
     *
     * @return external_function_parameters
     */
    public static function set_pin_state_parameters() {
        return new external_function_parameters(
            [
                'discussionid' => new external_value(PARAM_INT, 'The discussion to pin or unpin', VALUE_REQUIRED,
                    null, NULL_NOT_ALLOWED),
                'targetstate' => new external_value(PARAM_INT, 'The target state', VALUE_REQUIRED,
                    null, NULL_NOT_ALLOWED),
            ]
        );
    }

    /**
     * Returns description of method result value.
     *
     * @return external_single_structure
     */
    public static function set_pin_state_returns() {
        return discussion_exporter::get_read_structure();
    }
}
