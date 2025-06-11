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
 * Edit and save a new post to a discussion
 *
 * @package   mod_forum
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once('lib.php');
require_once($CFG->libdir.'/completionlib.php');

$reply   = optional_param('reply', 0, PARAM_INT);
$forum   = optional_param('forum', 0, PARAM_INT);
$edit    = optional_param('edit', 0, PARAM_INT);
$delete  = optional_param('delete', 0, PARAM_INT);
$prune   = optional_param('prune', 0, PARAM_INT);
$name    = optional_param('name', '', PARAM_CLEAN);
$confirm = optional_param('confirm', 0, PARAM_INT);
$groupid = optional_param('groupid', null, PARAM_INT);
$subject = optional_param('subject', '', PARAM_TEXT);

// Values posted via the inpage reply form.
$prefilledpost = optional_param('post', '', PARAM_TEXT);
$prefilledpostformat = optional_param('postformat', FORMAT_MOODLE, PARAM_INT);
$prefilledprivatereply = optional_param('privatereply', false, PARAM_BOOL);

$PAGE->set_url('/mod/forum/post.php', array(
    'reply' => $reply,
    'forum' => $forum,
    'edit'  => $edit,
    'delete' => $delete,
    'prune' => $prune,
    'name'  => $name,
    'confirm' => $confirm,
    'groupid' => $groupid,
));
// These page_params will be passed as hidden variables later in the form.
$pageparams = array('reply' => $reply, 'forum' => $forum, 'edit' => $edit);

$sitecontext = context_system::instance();

$entityfactory = mod_forum\local\container::get_entity_factory();
$vaultfactory = mod_forum\local\container::get_vault_factory();
$managerfactory = mod_forum\local\container::get_manager_factory();
$legacydatamapperfactory = mod_forum\local\container::get_legacy_data_mapper_factory();
$urlfactory = mod_forum\local\container::get_url_factory();

$forumvault = $vaultfactory->get_forum_vault();
$forumdatamapper = $legacydatamapperfactory->get_forum_data_mapper();

$discussionvault = $vaultfactory->get_discussion_vault();
$discussiondatamapper = $legacydatamapperfactory->get_discussion_data_mapper();

$postvault = $vaultfactory->get_post_vault();
$postdatamapper = $legacydatamapperfactory->get_post_data_mapper();

if (!isloggedin() or isguestuser()) {
    if (!isloggedin() and !get_local_referer()) {
        // No referer+not logged in - probably coming in via email  See MDL-9052.
        require_login();
    }

    if (!empty($forum)) {
        // User is starting a new discussion in a forum.
        $forumentity = $forumvault->get_from_id($forum);
        if (empty($forumentity)) {
            throw new \moodle_exception('invalidforumid', 'forum');
        }
    } else if (!empty($reply)) {
        // User is writing a new reply.
        $forumentity = $forumvault->get_from_post_id($reply);
        if (empty($forumentity)) {
            throw new \moodle_exception('invalidparentpostid', 'forum');
        }
    }

    $forum = $forumdatamapper->to_legacy_object($forumentity);
    $modcontext = $forumentity->get_context();
    $course = $forumentity->get_course_record();
    if (!$cm = get_coursemodule_from_instance("forum", $forum->id, $course->id)) {
        throw new \moodle_exception("invalidcoursemodule");
    }

    $PAGE->set_cm($cm, $course, $forum);
    $PAGE->set_context($modcontext);
    $PAGE->set_title($course->shortname);
    $PAGE->set_heading($course->fullname);
    $referer = get_local_referer(false);

    echo $OUTPUT->header();
    echo $OUTPUT->confirm(get_string('noguestpost', 'forum'), get_login_url(), $referer, [
        'confirmtitle' => get_string('noguestpost:title', 'forum'),
        'continuestr' => get_string('login')
    ]);
    echo $OUTPUT->footer();
    exit;
}

require_login(0, false);   // Script is useless unless they're logged in.

$canreplyprivately = false;

if (!empty($forum)) {
    // User is starting a new discussion in a forum.
    $forumentity = $forumvault->get_from_id($forum);
    if (empty($forumentity)) {
        throw new \moodle_exception('invalidforumid', 'forum');
    }

    $capabilitymanager = $managerfactory->get_capability_manager($forumentity);
    $forum = $forumdatamapper->to_legacy_object($forumentity);
    $course = $forumentity->get_course_record();
    if (!$cm = get_coursemodule_from_instance("forum", $forum->id, $course->id)) {
        throw new \moodle_exception("invalidcoursemodule");
    }

    // Retrieve the contexts.
    $modcontext = $forumentity->get_context();
    $coursecontext = context_course::instance($course->id);

    if ($forumentity->is_in_group_mode() && null === $groupid) {
        $groupid = groups_get_activity_group($cm);
    }

    if (!$capabilitymanager->can_create_discussions($USER, $groupid)) {
        if (!isguestuser()) {
            if (!is_enrolled($coursecontext)) {
                if (enrol_selfenrol_available($course->id)) {
                    $SESSION->wantsurl = qualified_me();
                    $SESSION->enrolcancel = get_local_referer(false);
                    redirect(new moodle_url('/enrol/index.php', array('id' => $course->id,
                        'returnurl' => '/mod/forum/view.php?f=' . $forum->id)),
                        get_string('youneedtoenrol'));
                }
            }
        }
        throw new \moodle_exception('nopostforum', 'forum');
    }

    if (!$cm->visible and !has_capability('moodle/course:viewhiddenactivities', $modcontext)) {
        redirect(
                $urlfactory->get_course_url_from_forum($forumentity),
                get_string('activityiscurrentlyhidden'),
                null,
                \core\output\notification::NOTIFY_ERROR
            );
    }

    // Load up the $post variable.

    $post = new stdClass();
    $post->course        = $course->id;
    $post->forum         = $forum->id;
    $post->discussion    = 0;           // Ie discussion # not defined yet.
    $post->parent        = 0;
    $post->subject       = $subject;
    $post->userid        = $USER->id;
    $post->message       = $prefilledpost;
    $post->messageformat = editors_get_preferred_format();
    $post->messagetrust  = 0;
    $post->groupid = $groupid;

    // Unsetting this will allow the correct return URL to be calculated later.
    unset($SESSION->fromdiscussion);

} else if (!empty($reply)) {
    // User is writing a new reply.

    $parententity = $postvault->get_from_id($reply);
    if (empty($parententity)) {
        throw new \moodle_exception('invalidparentpostid', 'forum');
    }

    $discussionentity = $discussionvault->get_from_id($parententity->get_discussion_id());
    if (empty($discussionentity)) {
        throw new \moodle_exception('notpartofdiscussion', 'forum');
    }

    $forumentity = $forumvault->get_from_id($discussionentity->get_forum_id());
    if (empty($forumentity)) {
        throw new \moodle_exception('invalidforumid', 'forum');
    }

    $capabilitymanager = $managerfactory->get_capability_manager($forumentity);
    $parent = $postdatamapper->to_legacy_object($parententity);
    $discussion = $discussiondatamapper->to_legacy_object($discussionentity);
    $forum = $forumdatamapper->to_legacy_object($forumentity);
    $course = $forumentity->get_course_record();
    $modcontext = $forumentity->get_context();
    $coursecontext = context_course::instance($course->id);

    if (!$cm = get_coursemodule_from_instance("forum", $forum->id, $course->id)) {
        throw new \moodle_exception('invalidcoursemodule');
    }

    // Ensure lang, theme, etc. is set up properly. MDL-6926.
    $PAGE->set_cm($cm, $course, $forum);

    if (!$capabilitymanager->can_reply_to_post($USER, $discussionentity, $parententity)) {
        if (!isguestuser()) {
            if (!is_enrolled($coursecontext)) {  // User is a guest here!
                $SESSION->wantsurl = qualified_me();
                $SESSION->enrolcancel = get_local_referer(false);
                redirect(new moodle_url('/enrol/index.php', array('id' => $course->id,
                    'returnurl' => '/mod/forum/view.php?f=' . $forum->id)),
                    get_string('youneedtoenrol'));
            }

            // The forum has been locked. Just redirect back to the discussion page.
            if (forum_discussion_is_locked($forum, $discussion)) {
                redirect(new moodle_url('/mod/forum/discuss.php', array('d' => $discussion->id)));
            }
        }
        throw new \moodle_exception('nopostforum', 'forum');
    }

    // Make sure user can post here.
    if (isset($cm->groupmode) && empty($course->groupmodeforce)) {
        $groupmode = $cm->groupmode;
    } else {
        $groupmode = $course->groupmode;
    }
    if ($groupmode == SEPARATEGROUPS and !has_capability('moodle/site:accessallgroups', $modcontext)) {
        if ($discussion->groupid == -1) {
            throw new \moodle_exception('nopostforum', 'forum');
        } else {
            if (!groups_is_member($discussion->groupid)) {
                throw new \moodle_exception('nopostforum', 'forum');
            }
        }
    }

    if (!$cm->visible and !has_capability('moodle/course:viewhiddenactivities', $modcontext)) {
        throw new \moodle_exception("activityiscurrentlyhidden");
    }

    if ($parententity->is_private_reply()) {
        throw new \moodle_exception('cannotreplytoprivatereply', 'forum');
    }

    // We always are going to honor the preferred format. We are creating a new post.
    $preferredformat = editors_get_preferred_format();

    // Only if there are prefilled contents coming.
    if (!empty($prefilledpost)) {
        // If the prefilled post is not HTML and the preferred format is HTML, convert to it.
        if ($prefilledpostformat != FORMAT_HTML and $preferredformat == FORMAT_HTML) {
            $prefilledpost = format_text($prefilledpost, $prefilledpostformat, ['context' => $modcontext]);
        }
    }

    // Load up the $post variable.
    $post = new stdClass();
    $post->course      = $course->id;
    $post->forum       = $forum->id;
    $post->discussion  = $parent->discussion;
    $post->parent      = $parent->id;
    $post->subject     = $subject ? $subject : $parent->subject;
    $post->userid      = $USER->id;
    $post->parentpostauthor = $parent->userid;
    $post->message     = $prefilledpost;
    $post->messageformat  = $preferredformat;
    $post->isprivatereply = $prefilledprivatereply;
    $canreplyprivately = $capabilitymanager->can_reply_privately_to_post($USER, $parententity);

    $post->groupid = ($discussion->groupid == -1) ? 0 : $discussion->groupid;

    $strre = get_string('re', 'forum');
    if (!(substr($post->subject, 0, strlen($strre)) == $strre)) {
        $post->subject = $strre.' '.$post->subject;
    }

    // Unsetting this will allow the correct return URL to be calculated later.
    unset($SESSION->fromdiscussion);

} else if (!empty($edit)) {
    // User is editing their own post.

    $postentity = $postvault->get_from_id($edit);
    if (empty($postentity)) {
        throw new \moodle_exception('invalidpostid', 'forum');
    }
    if ($postentity->has_parent()) {
        $parententity = $postvault->get_from_id($postentity->get_parent_id());
        $parent = $postdatamapper->to_legacy_object($parententity);
    }

    $discussionentity = $discussionvault->get_from_id($postentity->get_discussion_id());
    if (empty($discussionentity)) {
        throw new \moodle_exception('notpartofdiscussion', 'forum');
    }

    $forumentity = $forumvault->get_from_id($discussionentity->get_forum_id());
    if (empty($forumentity)) {
        throw new \moodle_exception('invalidforumid', 'forum');
    }

    $capabilitymanager = $managerfactory->get_capability_manager($forumentity);
    $post = $postdatamapper->to_legacy_object($postentity);
    $discussion = $discussiondatamapper->to_legacy_object($discussionentity);
    $forum = $forumdatamapper->to_legacy_object($forumentity);
    $course = $forumentity->get_course_record();
    $modcontext = $forumentity->get_context();
    $coursecontext = context_course::instance($course->id);

    if (!$cm = get_coursemodule_from_instance("forum", $forum->id, $course->id)) {
        throw new \moodle_exception('invalidcoursemodule');
    }

    $PAGE->set_cm($cm, $course, $forum);

    if (!($forum->type == 'news' && !$post->parent && $discussion->timestart > time())) {
        if (((time() - $post->created) > $CFG->maxeditingtime) and
            !has_capability('mod/forum:editanypost', $modcontext)) {
            throw new \moodle_exception('maxtimehaspassed', 'forum', '', format_time($CFG->maxeditingtime));
        }
    }
    if (($post->userid <> $USER->id) and
        !has_capability('mod/forum:editanypost', $modcontext)) {
        throw new \moodle_exception('cannoteditposts', 'forum');
    }

    // Load up the $post variable.
    $post->edit   = $edit;
    $post->course = $course->id;
    $post->forum  = $forum->id;
    $post->groupid = ($discussion->groupid == -1) ? 0 : $discussion->groupid;
    if ($postentity->has_parent()) {
        $canreplyprivately = forum_user_can_reply_privately($modcontext, $parent);
    }

    $post = trusttext_pre_edit($post, 'message', $modcontext);

    // Unsetting this will allow the correct return URL to be calculated later.
    unset($SESSION->fromdiscussion);

} else if (!empty($delete)) {
    // User is deleting a post.

    $postentity = $postvault->get_from_id($delete);
    if (empty($postentity)) {
        throw new \moodle_exception('invalidpostid', 'forum');
    }

    $discussionentity = $discussionvault->get_from_id($postentity->get_discussion_id());
    if (empty($discussionentity)) {
        throw new \moodle_exception('notpartofdiscussion', 'forum');
    }

    $forumentity = $forumvault->get_from_id($discussionentity->get_forum_id());
    if (empty($forumentity)) {
        throw new \moodle_exception('invalidforumid', 'forum');
    }

    $capabilitymanager = $managerfactory->get_capability_manager($forumentity);
    $course = $forumentity->get_course_record();
    $cm = $forumentity->get_course_module_record();
    $modcontext = $forumentity->get_context();

    require_login($course, false, $cm);

    $replycount = $postvault->get_reply_count_for_post_id_in_discussion_id(
        $USER, $postentity->get_id(), $discussionentity->get_id(), true);

    if (!empty($confirm) && confirm_sesskey()) {
        // Do further checks and delete the post.
        $hasreplies = $replycount > 0;

        try {
            $capabilitymanager->validate_delete_post($USER, $discussionentity, $postentity, $hasreplies);

            if (!$postentity->has_parent()) {
                forum_delete_discussion(
                    $discussiondatamapper->to_legacy_object($discussionentity),
                    false,
                    $forumentity->get_course_record(),
                    $forumentity->get_course_module_record(),
                    $forumdatamapper->to_legacy_object($forumentity)
                );

                redirect(
                    $urlfactory->get_forum_view_url_from_forum($forumentity),
                    get_string('eventdiscussiondeleted', 'forum'),
                    null,
                    \core\output\notification::NOTIFY_SUCCESS
                );
            } else {
                forum_delete_post(
                    $postdatamapper->to_legacy_object($postentity),
                    has_capability('mod/forum:deleteanypost', $modcontext),
                    $forumentity->get_course_record(),
                    $forumentity->get_course_module_record(),
                    $forumdatamapper->to_legacy_object($forumentity)
                );

                if ($forumentity->get_type() == 'single') {
                    // Single discussion forums are an exception.
                    // We show the forum itself since it only has one discussion thread.
                    $discussionurl = $urlfactory->get_forum_view_url_from_forum($forumentity);
                } else {
                    $discussionurl = $urlfactory->get_discussion_view_url_from_discussion($discussionentity);
                }

                redirect(
                    forum_go_back_to($discussionurl),
                    get_string('eventpostdeleted', 'forum'),
                    null,
                    \core\output\notification::NOTIFY_SUCCESS
                );
            }
        } catch (Exception $e) {
            redirect(
                $urlfactory->get_discussion_view_url_from_discussion($discussionentity),
                $e->getMessage(),
                null,
                \core\output\notification::NOTIFY_ERROR
            );
        }

    } else {

        if (!$capabilitymanager->can_delete_post($USER, $discussionentity, $postentity)) {
            redirect(
                    $urlfactory->get_discussion_view_url_from_discussion($discussionentity),
                    get_string('cannotdeletepost', 'forum'),
                    null,
                    \core\output\notification::NOTIFY_ERROR
                );
        }

        $post = $postdatamapper->to_legacy_object($postentity);
        $forum = $forumdatamapper->to_legacy_object($forumentity);

        // User just asked to delete something.
        forum_set_return();
        $PAGE->navbar->add(get_string('delete', 'forum'));
        $PAGE->set_title($course->shortname);
        $PAGE->set_heading($course->fullname);
        $PAGE->set_secondary_active_tab('modulepage');
        $PAGE->activityheader->disable();

        if ($replycount) {
            if (!has_capability('mod/forum:deleteanypost', $modcontext)) {
                redirect(
                        forum_go_back_to($urlfactory->get_view_post_url_from_post($postentity)),
                        get_string('couldnotdeletereplies', 'forum'),
                        null,
                        \core\output\notification::NOTIFY_ERROR
                    );
            }

            echo $OUTPUT->header();
            if (!$PAGE->has_secondary_navigation()) {
                echo $OUTPUT->heading(format_string($forum->name), 2);
            }
            echo $OUTPUT->confirm(get_string("deletesureplural", "forum", $replycount + 1),
                "post.php?delete=$delete&confirm=$delete",
                $CFG->wwwroot.'/mod/forum/discuss.php?d='.$post->discussion.'#p'.$post->id);

            $postentities = [$postentity];
            if (empty($post->edit)) {
                $postvault = $vaultfactory->get_post_vault();
                $replies = $postvault->get_replies_to_post(
                        $USER,
                        $postentity,
                        // Note: All replies are fetched here as the user has deleteanypost.
                        true,
                        'created ASC'
                    );
                $postentities = array_merge($postentities, $replies);
            }

            $rendererfactory = mod_forum\local\container::get_renderer_factory();
            $postsrenderer = $rendererfactory->get_single_discussion_posts_renderer(FORUM_MODE_NESTED, true);
            echo $postsrenderer->render($USER, [$forumentity], [$discussionentity], $postentities);
        } else {
            echo $OUTPUT->header();
            if (!$PAGE->has_secondary_navigation()) {
                echo $OUTPUT->heading(format_string($forum->name), 2);
            }
            echo $OUTPUT->confirm(get_string("deletesure", "forum", $replycount),
                "post.php?delete=$delete&confirm=$delete",
                $CFG->wwwroot.'/mod/forum/discuss.php?d='.$post->discussion.'#p'.$post->id);

            $rendererfactory = mod_forum\local\container::get_renderer_factory();
            $postsrenderer = $rendererfactory->get_single_discussion_posts_renderer(null, true);
            echo $postsrenderer->render($USER, [$forumentity], [$discussionentity], [$postentity]);
        }

    }
    echo $OUTPUT->footer();
    die;

} else if (!empty($prune)) {
    // Pruning.

    $postentity = $postvault->get_from_id($prune);
    if (empty($postentity)) {
        throw new \moodle_exception('invalidpostid', 'forum');
    }

    $discussionentity = $discussionvault->get_from_id($postentity->get_discussion_id());
    if (empty($discussionentity)) {
        throw new \moodle_exception('notpartofdiscussion', 'forum');
    }

    $forumentity = $forumvault->get_from_id($discussionentity->get_forum_id());
    if (empty($forumentity)) {
        throw new \moodle_exception('invalidforumid', 'forum');
    }

    $capabilitymanager = $managerfactory->get_capability_manager($forumentity);
    $post = $postdatamapper->to_legacy_object($postentity);
    $discussion = $discussiondatamapper->to_legacy_object($discussionentity);
    $forum = $forumdatamapper->to_legacy_object($forumentity);
    $course = $forumentity->get_course_record();
    $modcontext = $forumentity->get_context();
    $coursecontext = context_course::instance($course->id);

    if (!$cm = get_coursemodule_from_instance("forum", $forum->id, $course->id)) {
        throw new \moodle_exception('invalidcoursemodule');
    }

    if (!$postentity->has_parent()) {
        redirect(
                $urlfactory->get_discussion_view_url_from_discussion($discussionentity),
                get_string('alreadyfirstpost', 'forum'),
                null,
                \core\output\notification::NOTIFY_ERROR
            );
    }
    if (!$capabilitymanager->can_split_post($USER, $discussionentity, $postentity)) {
        redirect(
                $urlfactory->get_discussion_view_url_from_discussion($discussionentity),
                get_string('cannotsplit', 'forum'),
                null,
                \core\output\notification::NOTIFY_ERROR
            );
    }

    $PAGE->set_cm($cm);
    $PAGE->set_context($modcontext);
    $PAGE->set_secondary_active_tab('modulepage');
    $PAGE->activityheader->disable();

    $prunemform = new mod_forum_prune_form(null, array('prune' => $prune, 'confirm' => $prune));

    if ($prunemform->is_cancelled()) {
        redirect(forum_go_back_to($urlfactory->get_discussion_view_url_from_discussion($discussionentity)));
    } else if ($fromform = $prunemform->get_data()) {
        // User submits the data.
        $newdiscussion = new stdClass();
        $newdiscussion->course       = $discussion->course;
        $newdiscussion->forum        = $discussion->forum;
        $newdiscussion->name         = $name;
        $newdiscussion->firstpost    = $post->id;
        $newdiscussion->userid       = $post->userid;
        $newdiscussion->groupid      = $discussion->groupid;
        $newdiscussion->assessed     = $discussion->assessed;
        $newdiscussion->usermodified = $post->userid;
        $newdiscussion->timestart    = $discussion->timestart;
        $newdiscussion->timeend      = $discussion->timeend;

        $newid = $DB->insert_record('forum_discussions', $newdiscussion);

        $newpost = new stdClass();
        $newpost->id      = $post->id;
        $newpost->parent  = 0;
        $newpost->subject = $name;

        $DB->update_record("forum_posts", $newpost);
        $postentity = $postvault->get_from_id($postentity->get_id());

        forum_change_discussionid($post->id, $newid);

        // Update last post in each discussion.
        forum_discussion_update_last_post($discussion->id);
        forum_discussion_update_last_post($newid);

        // Fire events to reflect the split..
        $params = array(
            'context' => $modcontext,
            'objectid' => $discussion->id,
            'other' => array(
                'forumid' => $forum->id,
            )
        );
        $event = \mod_forum\event\discussion_updated::create($params);
        $event->trigger();

        $params = array(
            'context' => $modcontext,
            'objectid' => $newid,
            'other' => array(
                'forumid' => $forum->id,
            )
        );
        $event = \mod_forum\event\discussion_created::create($params);
        $event->trigger();

        $params = array(
            'context' => $modcontext,
            'objectid' => $post->id,
            'other' => array(
                'discussionid' => $newid,
                'forumid' => $forum->id,
                'forumtype' => $forum->type,
            )
        );
        $event = \mod_forum\event\post_updated::create($params);
        $event->add_record_snapshot('forum_discussions', $discussion);
        $event->trigger();

        redirect(
            forum_go_back_to($urlfactory->get_discussion_view_url_from_post($postentity)),
            get_string('discussionsplit', 'forum'),
            null,
            \core\output\notification::NOTIFY_SUCCESS
        );
    } else {
        // Display the prune form.
        $course = $DB->get_record('course', array('id' => $forum->course));
        $subjectstr = format_string($post->subject, true);
        $PAGE->navbar->add($subjectstr, new moodle_url('/mod/forum/discuss.php', array('d' => $discussion->id)));
        $PAGE->navbar->add(get_string("prunediscussion", "forum"));
        $PAGE->set_title(format_string($discussion->name).": ".format_string($post->subject));
        $PAGE->set_heading($course->fullname);
        echo $OUTPUT->header();
        if (!$PAGE->has_secondary_navigation()) {
            echo $OUTPUT->heading(format_string($forum->name), 2);
        }
        echo $OUTPUT->heading(get_string('pruneheading', 'forum'), 3);

        $prunemform->display();

        $postentity = $entityfactory->get_post_from_stdclass($post);
        $discussionentity = $entityfactory->get_discussion_from_stdclass($discussion);
        $forumentity = $entityfactory->get_forum_from_stdclass($forum, $modcontext, $cm, $course);
        $rendererfactory = mod_forum\local\container::get_renderer_factory();
        $postsrenderer = $rendererfactory->get_single_discussion_posts_renderer(null, true);
        echo $postsrenderer->render($USER, [$forumentity], [$discussionentity], [$postentity]);
    }

    echo $OUTPUT->footer();
    die;
} else {
    throw new \moodle_exception('unknowaction');

}

// From now on user must be logged on properly.

require_login($course, false, $cm);

if (isguestuser()) {
    // Just in case.
    throw new \moodle_exception('noguest');
}

$thresholdwarning = forum_check_throttling($forum, $cm);
$mformpost = new mod_forum_post_form('post.php', [
        'course' => $course,
        'cm' => $cm,
        'coursecontext' => $coursecontext,
        'modcontext' => $modcontext,
        'forum' => $forum,
        'post' => $post,
        'subscribe' => \mod_forum\subscriptions::is_subscribed($USER->id, $forum, null, $cm),
        'thresholdwarning' => $thresholdwarning,
        'edit' => $edit,
        'canreplyprivately' => $canreplyprivately,
    ], 'post', '', array('id' => 'mformforum'));

$draftitemid = file_get_submitted_draft_itemid('attachments');
$postid = empty($post->id) ? null : $post->id;
$attachoptions = mod_forum_post_form::attachment_options($forum);
file_prepare_draft_area($draftitemid, $modcontext->id, 'mod_forum', 'attachment', $postid, $attachoptions);

// Load data into form NOW!

if ($USER->id != $post->userid) {   // Not the original author, so add a message to the end.
    $data = new stdClass();
    $data->date = userdate($post->created);
    if ($post->messageformat == FORMAT_HTML) {
        $data->name = '<a href="'.$CFG->wwwroot.'/user/view.php?id='.$USER->id.'&course='.$post->course.'">'.
            fullname($USER).'</a>';
        $post->message .= '<p><span class="edited">('.get_string('editedby', 'forum', $data).')</span></p>';
    } else {
        $data->name = fullname($USER);
        $post->message .= "\n\n(".get_string('editedby', 'forum', $data).')';
    }
    unset($data);
}

$formheading = '';
if (!empty($parent)) {
    $heading = get_string("yourreply", "forum");
    $formheading = get_string('reply', 'forum');
} else {
    if ($forum->type == 'qanda') {
        $heading = get_string('yournewquestion', 'forum');
    } else {
        $heading = get_string('yournewtopic', 'forum');
    }
}

$postid = empty($post->id) ? null : $post->id;
$draftideditor = file_get_submitted_draft_itemid('message');
$editoropts = mod_forum_post_form::editor_options($modcontext, $postid);
$currenttext = file_prepare_draft_area($draftideditor, $modcontext->id, 'mod_forum', 'post', $postid, $editoropts, $post->message);
$discussionid = isset($discussion) ? $discussion->id : null;
$discussionsubscribe = \mod_forum\subscriptions::get_user_default_subscription($forum, $coursecontext, $cm, $discussionid);

$mformpost->set_data(
    array(
        'attachments' => $draftitemid,
        'general' => $heading,
        'subject' => $post->subject,
        'message' => array(
            'text' => $currenttext,
            'format' => !isset($post->messageformat) || !is_numeric($post->messageformat) ?
                editors_get_preferred_format() : $post->messageformat,
            'itemid' => $draftideditor
        ),
        'discussionsubscribe' => $discussionsubscribe,
        'mailnow' => !empty($post->mailnow),
        'userid' => $post->userid,
        'parent' => $post->parent,
        'discussion' => $post->discussion,
        'course' => $course->id,
        'isprivatereply' => $post->isprivatereply ?? false
    ) +

    $pageparams +

    (isset($post->format) ? array('format' => $post->format) : array()) +

    (isset($discussion->timestart) ? array('timestart' => $discussion->timestart) : array()) +

    (isset($discussion->timeend) ? array('timeend' => $discussion->timeend) : array()) +

    (isset($discussion->pinned) ? array('pinned' => $discussion->pinned) : array()) +

    (isset($post->groupid) ? array('groupid' => $post->groupid) : array()) +

    (isset($discussion->id) ? array('discussion' => $discussion->id) : array())
);

// If we are being redirected via a no_submit_button press OR if the message is being prefilled.
// then set the initial 'dirty' state.
// - A prefilled post will exist when being redirected from the inpage reply form.
// - A no_submit_button press occurs when being redirected from the inpage add new discussion post form.
$dirty = $prefilledpost ? true : false;
if ($mformpost->no_submit_button_pressed()) {
    $data = $mformpost->get_submitted_data();

    // If a no submit button has been pressed but the default values haven't been then reset the form change.
    if (!$dirty && isset($data->message['text']) && !empty(trim($data->message['text']))) {
        $dirty = true;
    }

    if (!$dirty && isset($data->message['message']) && !empty(trim($data->message['message']))) {
        $dirty = true;
    }
}
$mformpost->set_initial_dirty_state($dirty);

if ($mformpost->is_cancelled()) {
    if (!isset($discussion->id) || $forum->type === 'single') {
        // Single forums don't have a discussion page.
        redirect($urlfactory->get_forum_view_url_from_forum($forumentity));
    } else {
        redirect($urlfactory->get_discussion_view_url_from_discussion($discussionentity));
    }
} else if ($mformpost->is_submitted() && !$mformpost->no_submit_button_pressed() && $fromform = $mformpost->get_data()) {

    $errordestination = get_local_referer(false) ?: $urlfactory->get_forum_view_url_from_forum($forumentity);

    $fromform->itemid        = $fromform->message['itemid'];
    $fromform->messageformat = $fromform->message['format'];
    $fromform->message       = $fromform->message['text'];
    // WARNING: the $fromform->message array has been overwritten, do not use it anymore!
    $fromform->messagetrust  = trusttext_trusted($modcontext);

    // Do not clean text here, text cleaning can be done only after conversion to HTML.
    // Word counting now uses text formatting, there is no need to abuse trusttext_pre_edit() here.

    if ($fromform->edit) {
        // Updating a post.
        unset($fromform->groupid);
        $fromform->id = $fromform->edit;
        $message = '';

        if (!$capabilitymanager->can_edit_post($USER, $discussionentity, $postentity)) {
            redirect(
                    $urlfactory->get_view_post_url_from_post($postentity),
                    get_string('cannotupdatepost', 'forum'),
                    null,
                    \core\output\notification::ERROR
                );
        }

        if (isset($fromform->groupinfo) && $capabilitymanager->can_move_discussions($USER)) {
            // If the user has access to all groups and they are changing the group, then update the post.
            if (empty($fromform->groupinfo)) {
                $fromform->groupinfo = -1;
            }

            if (!$capabilitymanager->can_create_discussions($USER, $fromform->groupinfo)) {
                redirect(
                        $urlfactory->get_view_post_url_from_post($postentity),
                        get_string('cannotupdatepost', 'forum'),
                        null,
                        \core\output\notification::ERROR
                    );
            }

            if ($discussionentity->get_group_id() != $fromform->groupinfo) {
                $DB->set_field('forum_discussions', 'groupid', $fromform->groupinfo, array('firstpost' => $fromform->id));
            }
        }

        // When editing first post/discussion.
        if (!$postentity->has_parent()) {
            if ($capabilitymanager->can_pin_discussions($USER)) {
                // Can change pinned if we have capability.
                $fromform->pinned = !empty($fromform->pinned) ? FORUM_DISCUSSION_PINNED : FORUM_DISCUSSION_UNPINNED;
            } else {
                // We don't have the capability to change so keep to previous value.
                unset($fromform->pinned);
            }
        }
        $updatepost = $fromform;
        $updatepost->forum = $forum->id;
        if (!forum_update_post($updatepost, $mformpost)) {
            throw new \moodle_exception("couldnotupdate", "forum", $errordestination);
        }

        forum_trigger_post_updated_event($post, $discussion, $modcontext, $forum);

        if ($USER->id === $postentity->get_author_id()) {
            $message .= get_string("postupdated", "forum");
        } else {
            $realuser = \core_user::get_user($postentity->get_author_id());
            $message .= get_string("editedpostupdated", "forum", fullname($realuser));
        }

        $subscribemessage = forum_post_subscription($fromform, $forum, $discussion);
        if ('single' == $forumentity->get_type()) {
            // Single discussion forums are an exception.
            // We show the forum itself since it only has one discussion thread.
            $discussionurl = $urlfactory->get_forum_view_url_from_forum($forumentity);
        } else {
            $discussionurl = $urlfactory->get_view_post_url_from_post($postentity);
        }

        redirect(
            forum_go_back_to($discussionurl),
            $message . $subscribemessage,
            null,
            \core\output\notification::NOTIFY_SUCCESS
        );

    } else if ($fromform->discussion) {
        // Adding a new post to an existing discussion
        // Before we add this we must check that the user will not exceed the blocking threshold.
        forum_check_blocking_threshold($thresholdwarning);

        unset($fromform->groupid);
        $message = '';
        $addpost = $fromform;
        $addpost->forum = $forum->id;
        if ($fromform->id = forum_add_new_post($addpost, $mformpost)) {
            $postentity = $postvault->get_from_id($fromform->id);
            $fromform->deleted = 0;
            $subscribemessage = forum_post_subscription($fromform, $forum, $discussion);

            if (!empty($fromform->mailnow)) {
                $message .= get_string("postmailnow", "forum");
            } else {
                $message .= '<p>'.get_string("postaddedsuccess", "forum") . '</p>';
                $message .= '<p>'.get_string("postaddedtimeleft", "forum", format_time($CFG->maxeditingtime)) . '</p>';
            }

            if ($forum->type == 'single') {
                // Single discussion forums are an exception.
                // We show the forum itself since it only has one discussion thread.
                $discussionurl = $urlfactory->get_forum_view_url_from_forum($forumentity);
            } else {
                $discussionurl = $urlfactory->get_view_post_url_from_post($postentity);
            }

            $params = array(
                'context' => $modcontext,
                'objectid' => $fromform->id,
                'other' => array(
                    'discussionid' => $discussion->id,
                    'forumid' => $forum->id,
                    'forumtype' => $forum->type,
                )
            );
            $event = \mod_forum\event\post_created::create($params);
            $event->add_record_snapshot('forum_posts', $fromform);
            $event->add_record_snapshot('forum_discussions', $discussion);
            $event->trigger();

            // Update completion state.
            $completion = new completion_info($course);
            if ($completion->is_enabled($cm) &&
                ($forum->completionreplies || $forum->completionposts)) {
                $completion->update_state($cm, COMPLETION_COMPLETE);
            }

            redirect(
                forum_go_back_to($discussionurl),
                $message . $subscribemessage,
                null,
                \core\output\notification::NOTIFY_SUCCESS
            );

        } else {
            throw new \moodle_exception("couldnotadd", "forum", $errordestination);
        }
        exit;

    } else {
        // Adding a new discussion.
        // The location to redirect to after successfully posting.
        $redirectto = new moodle_url('/mod/forum/view.php', array('f' => $fromform->forum));

        $fromform->mailnow = empty($fromform->mailnow) ? 0 : 1;

        $discussion = $fromform;
        $discussion->name = $fromform->subject;
        $discussion->timelocked = 0;

        $newstopic = false;
        if ($forum->type == 'news' && !$fromform->parent) {
            $newstopic = true;
        }

        if (!empty($fromform->pinned) && $capabilitymanager->can_pin_discussions($USER)) {
            $discussion->pinned = FORUM_DISCUSSION_PINNED;
        } else {
            $discussion->pinned = FORUM_DISCUSSION_UNPINNED;
        }

        $allowedgroups = array();
        $groupstopostto = array();

        // If we are posting a copy to all groups the user has access to.
        if (isset($fromform->posttomygroups)) {
            // Post to each of my groups.
            require_capability('mod/forum:canposttomygroups', $modcontext);

            // Fetch all of this user's groups.
            // Note: all groups are returned when in visible groups mode so we must manually filter.
            $allowedgroups = groups_get_activity_allowed_groups($cm);
            foreach ($allowedgroups as $groupid => $group) {
                if ($capabilitymanager->can_create_discussions($USER, $groupid)) {
                    $groupstopostto[] = $groupid;
                }
            }
        } else if (isset($fromform->groupinfo)) {
            // Use the value provided in the dropdown group selection.
            $groupstopostto[] = $fromform->groupinfo;
            $redirectto->param('group', $fromform->groupinfo);
        } else if (isset($fromform->groupid) && !empty($fromform->groupid)) {
            // Use the value provided in the hidden form element instead.
            $groupstopostto[] = $fromform->groupid;
            $redirectto->param('group', $fromform->groupid);
        } else {
            // Use the value for all participants instead.
            $groupstopostto[] = -1;
        }

        // Before we post this we must check that the user will not exceed the blocking threshold.
        forum_check_blocking_threshold($thresholdwarning);

        foreach ($groupstopostto as $group) {
            if (!$capabilitymanager->can_create_discussions($USER, $group)) {
                throw new \moodle_exception('cannotcreatediscussion', 'forum');
            }

            $discussion->groupid = $group;
            $message = '';
            if ($discussion->id = forum_add_discussion($discussion, $mformpost)) {

                $params = array(
                    'context' => $modcontext,
                    'objectid' => $discussion->id,
                    'other' => array(
                        'forumid' => $forum->id,
                    )
                );
                $event = \mod_forum\event\discussion_created::create($params);
                $event->add_record_snapshot('forum_discussions', $discussion);
                $event->trigger();

                if ($fromform->mailnow) {
                    $message .= get_string("postmailnow", "forum");
                } else {
                    $message .= '<p>'.get_string("postaddedsuccess", "forum") . '</p>';
                    $message .= '<p>'.get_string("postaddedtimeleft", "forum", format_time($CFG->maxeditingtime)) . '</p>';
                }

                $subscribemessage = forum_post_subscription($fromform, $forum, $discussion);
            } else {
                throw new \moodle_exception("couldnotadd", "forum", $errordestination);
            }
        }

        // Update completion status.
        $completion = new completion_info($course);
        if ($completion->is_enabled($cm) &&
            ($forum->completiondiscussions || $forum->completionposts)) {
            $completion->update_state($cm, COMPLETION_COMPLETE);
        }

        // Redirect back to the discussion.
        redirect(
            forum_go_back_to($redirectto->out()),
            $message . $subscribemessage,
            null,
            \core\output\notification::NOTIFY_SUCCESS
        );
    }
}


// This section is only shown after all checks are in place, and the forumentity and any relevant discussion and post
// entity are available.

if (!empty($discussionentity)) {
    $titlesubject = format_string($discussionentity->get_name(), true);
} else if ('news' == $forumentity->get_type()) {
    $titlesubject = get_string("addanewtopic", "forum");
} else {
    $titlesubject = get_string("addanewdiscussion", "forum");
}

if (empty($post->edit)) {
    $post->edit = '';
}

if (empty($discussion->name)) {
    if (empty($discussion)) {
        $discussion = new stdClass();
    }
    $discussion->name = $forum->name;
}

$strdiscussionname = '';
if ('single' == $forumentity->get_type()) {
    // There is only one discussion thread for this forum type. We should
    // not show the discussion name (same as forum name in this case) in
    // the breadcrumbs.
    $strdiscussionname = '';
} else if (!empty($discussionentity)) {
    // Show the discussion name in the breadcrumbs.
    $strdiscussionname = format_string($discussionentity->get_name()) . ': ';
}

$forcefocus = empty($reply) ? null : 'message';

if (!empty($discussion->id)) {
    $PAGE->navbar->add($titlesubject, $urlfactory->get_discussion_view_url_from_discussion($discussionentity));
}

if ($edit) {
    $PAGE->navbar->add(get_string('editdiscussiontopic', 'forum'), $PAGE->url);
} else if ($reply) {
    $PAGE->navbar->add(get_string('addreply', 'forum'));
} else {
    $PAGE->navbar->add(get_string('addanewdiscussion', 'forum'), $PAGE->url);
}

$PAGE->set_title("{$course->shortname}: {$strdiscussionname}{$titlesubject}");
$PAGE->set_heading($course->fullname);
$PAGE->set_secondary_active_tab("modulepage");
$activityheaderconfig['hidecompletion'] = true;
$activityheaderconfig['description'] = '';

// Remove the activity description.
$PAGE->activityheader->set_attrs($activityheaderconfig);
echo $OUTPUT->header();

if ($edit) {
    echo $OUTPUT->heading(get_string('editdiscussiontopic', 'forum'), 2);
} else if ($reply) {
    echo $OUTPUT->heading(get_string('replypostdiscussion', 'forum'), 2);
} else {
    echo $OUTPUT->heading(get_string('addanewdiscussion', 'forum'), 2);
}

// Checkup.
if (!empty($parententity) && !$capabilitymanager->can_view_post($USER, $discussionentity, $parententity)) {
    throw new \moodle_exception('cannotreply', 'forum');
}

if (empty($parententity) && empty($edit) && !$capabilitymanager->can_create_discussions($USER, $groupid)) {
    throw new \moodle_exception('cannotcreatediscussion', 'forum');
}

if (!empty($discussionentity) && 'qanda' == $forumentity->get_type()) {
    $displaywarning = $capabilitymanager->must_post_before_viewing_discussion($USER, $discussionentity);
    $displaywarning = $displaywarning && !forum_user_has_posted($forumentity->get_id(), $discussionentity->get_id(), $USER->id);
    if ($displaywarning) {
        echo $OUTPUT->notification(get_string('qandanotify', 'forum'));
    }
}

// If there is a warning message and we are not editing a post we need to handle the warning.
if (!empty($thresholdwarning) && !$edit) {
    // Here we want to throw an exception if they are no longer allowed to post.
    forum_check_blocking_threshold($thresholdwarning);
}

if (!empty($parententity)) {
    $postentities = [$parententity];

    if (empty($post->edit)) {
        if ('qanda' != $forumentity->get_type() || forum_user_can_see_discussion($forum, $discussion, $modcontext)) {
            $replies = $postvault->get_replies_to_post(
                    $USER,
                    $parententity,
                    $capabilitymanager->can_view_any_private_reply($USER),
                    'created ASC'
                );
            $postentities = array_merge($postentities, $replies);
        }
    }

    $rendererfactory = mod_forum\local\container::get_renderer_factory();
    $postsrenderer = $rendererfactory->get_single_discussion_posts_renderer(FORUM_MODE_THREADED, true);
    echo $postsrenderer->render($USER, [$forumentity], [$discussionentity], $postentities);
}

// Call print disclosure for enabled plagiarism plugins.
if (!empty($CFG->enableplagiarism)) {
    require_once($CFG->libdir.'/plagiarismlib.php');
    echo plagiarism_print_disclosure($cm->id);
}

if (!empty($formheading)) {
    echo $OUTPUT->heading($formheading, 2, array('class' => 'accesshide'));
}

if (!empty($postentity)) {
    $data = (object) [
        'tags' => core_tag_tag::get_item_tags_array('mod_forum', 'forum_posts', $postentity->get_id())
    ];
    $mformpost->set_data($data);
}

$mformpost->display();

echo $OUTPUT->footer();
