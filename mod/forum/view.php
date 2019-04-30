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
 * Displays the list of discussions in a forum.
 *
 * @package   mod_forum
 * @copyright 2019 Andrew Nicols <andrew@nicols.co.uk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');

$managerfactory = mod_forum\local\container::get_manager_factory();
$legacydatamapperfactory = mod_forum\local\container::get_legacy_data_mapper_factory();
$vaultfactory = mod_forum\local\container::get_vault_factory();
$forumvault = $vaultfactory->get_forum_vault();
$discussionvault = $vaultfactory->get_discussion_vault();
$postvault = $vaultfactory->get_post_vault();
$discussionlistvault = $vaultfactory->get_discussions_in_forum_vault();

$cmid = optional_param('id', 0, PARAM_INT);
$forumid = optional_param('f', 0, PARAM_INT);
$mode = optional_param('mode', 0, PARAM_INT);
$showall = optional_param('showall', '', PARAM_INT);
$pageno = optional_param('page', 0, PARAM_INT);
$search = optional_param('search', '', PARAM_CLEAN);
$pageno = optional_param('p', $pageno, PARAM_INT);
$pagesize = optional_param('s', 0, PARAM_INT);
$sortorder = optional_param('o', null, PARAM_INT);

if (!$cmid && !$forumid) {
    print_error('missingparameter');
}

if ($cmid) {
    $forum = $forumvault->get_from_course_module_id($cmid);
    if (empty($forum)) {
        throw new \moodle_exception('Unable to find forum with cmid ' . $cmid);
    }
} else {
    $forum = $forumvault->get_from_id($forumid);
    if (empty($forum)) {
        throw new \moodle_exception('Unable to find forum with id ' . $forumid);
    }
}

if (!empty($showall)) {
    // The user wants to see all discussions.
    $pageno = 0;
    $pagesize = 0;
}

$urlfactory = mod_forum\local\container::get_url_factory();
$capabilitymanager = $managerfactory->get_capability_manager($forum);

$url = $urlfactory->get_forum_view_url_from_forum($forum);
$PAGE->set_url($url);

$course = $forum->get_course_record();
$coursemodule = $forum->get_course_module_record();
$cm = \cm_info::create($coursemodule);

require_course_login($course, true, $cm);

$PAGE->set_context($forum->get_context());
$PAGE->set_title($forum->get_name());
$PAGE->add_body_class('forumtype-' . $forum->get_type());
$PAGE->set_heading($course->fullname);
$PAGE->set_button(forum_search_form($course, $search));

if (empty($cm->visible) && !has_capability('moodle/course:viewhiddenactivities', $forum->get_context())) {
    redirect(
        $urlfactory->get_course_url_from_forum($forum),
        get_string('activityiscurrentlyhidden'),
        null,
        \core\output\notification::NOTIFY_WARNING
    );
}

if (!$capabilitymanager->can_view_discussions($USER)) {
    redirect(
        $urlfactory->get_course_url_from_forum($forum),
        get_string('noviewdiscussionspermission', 'fourm'),
        null,
        \core\output\notification::NOTIFY_WARNING
    );
}

// Mark viewed and trigger the course_module_viewed event.
$forumdatamapper = $legacydatamapperfactory->get_forum_data_mapper();
$forumrecord = $forumdatamapper->to_legacy_object($forum);
forum_view(
    $forumrecord,
    $forum->get_course_record(),
    $forum->get_course_module_record(),
    $forum->get_context()
);

// Return here if we post or set subscription etc.
$SESSION->fromdiscussion = qualified_me();

if (!empty($CFG->enablerssfeeds) && !empty($CFG->forum_enablerssfeeds) && $forum->get_rss_type() && $forum->get_rss_articles()) {
    require_once("{$CFG->libdir}/rsslib.php");

    $rsstitle = format_string($course->shortname, true, [
            'context' => context_course::instance($course->id),
        ]) . ': ' . format_string($forum->get_name());
    rss_add_http_header($forum->get_context(), 'mod_forum', $forumrecord, $rsstitle);
}

echo $OUTPUT->header();
echo $OUTPUT->heading(format_string($forum->get_name()), 2);

if ('single' !== $forum->get_type() && !empty($forum->get_intro())) {
    echo $OUTPUT->box(format_module_intro('forum', $forumrecord, $cm->id), 'generalbox', 'intro');
}

if ($mode) {
    set_user_preference('forum_displaymode', $mode);
}

$displaymode = get_user_preferences('forum_displaymode', $CFG->forum_displaymode);

if ($sortorder) {
    set_user_preference('forum_discussionlistsortorder', $sortorder);
}

$sortorder = get_user_preferences('forum_discussionlistsortorder', $discussionlistvault::SORTORDER_LASTPOST_DESC);

// Fetch the current groupid.
$groupid = groups_get_activity_group($cm, true) ?: null;
$rendererfactory = mod_forum\local\container::get_renderer_factory();
switch ($forum->get_type()) {
    case 'single':
        $discussion = $discussionvault->get_last_discussion_in_forum($forum);
        $discussioncount = $discussionvault->get_count_discussions_in_forum($forum);
        $hasmultiplediscussions = $discussioncount > 1;
        $discussionsrenderer = $rendererfactory->get_single_discussion_list_renderer($forum, $discussion,
            $hasmultiplediscussions, $displaymode);
        $post = $postvault->get_from_id($discussion->get_first_post_id());
        $orderpostsby = $displaymode == FORUM_MODE_FLATNEWEST ? 'created DESC' : 'created ASC';
        $replies = $postvault->get_replies_to_post(
                $USER,
                $post,
                $capabilitymanager->can_view_any_private_reply($USER),
                $orderpostsby
            );
        echo $discussionsrenderer->render($USER, $post, $replies);
        break;
    case 'blog':
        $discussionsrenderer = $rendererfactory->get_blog_discussion_list_renderer($forum);
        // Blog forums always show discussions newest first.
        echo $discussionsrenderer->render($USER, $cm, $groupid, $discussionlistvault::SORTORDER_CREATED_DESC,
            $pageno, $pagesize);
        break;
    default:
        $discussionsrenderer = $rendererfactory->get_discussion_list_renderer($forum);
        echo $discussionsrenderer->render($USER, $cm, $groupid, $sortorder, $pageno, $pagesize);
}

echo $OUTPUT->footer();
