<?php // $Id$

//  Displays a post, and all the posts below it.
//  If no post is given, displays all posts in a discussion

    require_once('../../config.php');

    $d      = required_param('d', PARAM_INT);                // Discussion ID
    $parent = optional_param('parent', 0, PARAM_INT);        // If set, then display this post and all children.
    $mode   = optional_param('mode', 0, PARAM_INT);          // If set, changes the layout of the thread
    $move   = optional_param('move', 0, PARAM_INT);          // If set, moves this discussion to another forum
    $mark   = optional_param('mark', '', PARAM_ALPHA);       // Used for tracking read posts if user initiated.
    $postid = optional_param('postid', 0, PARAM_INT);        // Used for tracking read posts if user initiated.

    if (!$discussion = get_record('forum_discussions', 'id', $d)) {
        error("Discussion ID was incorrect or no longer exists");
    }

    if (!$course = get_record('course', 'id', $discussion->course)) {
        error("Course ID is incorrect - discussion is faulty");
    }

    if (!$forum = get_record('forum', 'id', $discussion->forum)) {
        notify("Bad forum ID stored in this discussion");
    }

    if (!$cm = get_coursemodule_from_instance('forum', $forum->id, $course->id)) {
        error('Course Module ID was incorrect');
    }

    require_course_login($course, true, $cm);

/// Add ajax-related libs
    require_js(array('yui_yahoo', 'yui_event', 'yui_dom', 'yui_connection', 'yui_json'));
    require_js($CFG->wwwroot . '/mod/forum/rate_ajax.js');

    // move this down fix for MDL-6926
    require_once('lib.php');

    $modcontext = get_context_instance(CONTEXT_MODULE, $cm->id);
    require_capability('mod/forum:viewdiscussion', $modcontext, NULL, true, 'noviewdiscussionspermission', 'forum');

    if ($forum->type == 'news') {
        if (!($USER->id == $discussion->userid || (($discussion->timestart == 0
            || $discussion->timestart <= time())
            && ($discussion->timeend == 0 || $discussion->timeend > time())))) {
            error('Discussion ID was incorrect or no longer exists', "$CFG->wwwroot/mod/forum/view.php?f=$forum->id");
        }
    }

/// move discussion if requested
    if ($move > 0 and confirm_sesskey()) {
        $return = $CFG->wwwroot.'/mod/forum/discuss.php?d='.$discussion->id;

        require_capability('mod/forum:movediscussions', $modcontext);

        if ($forum->type == 'single') {
            error('Cannot move discussion from a simple single discussion forum', $return);
        }

        if (!$forumto = get_record('forum', 'id', $move)) {
            error('You can\'t move to that forum - it doesn\'t exist!', $return);
        }

        if (!$cmto = get_coursemodule_from_instance('forum', $forumto->id, $course->id)) {
            error('Target forum not found in this course.', $return);
        }

        if (!coursemodule_visible_for_user($cmto)) {
            error('Forum not visible', $return);
        }

        require_capability('mod/forum:startdiscussion',
            get_context_instance(CONTEXT_MODULE,$cmto->id));

        if (!forum_move_attachments($discussion, $forumto->id)) {
            notify("Errors occurred while moving attachment directories - check your file permissions");
        }
        set_field('forum_discussions', 'forum', $forumto->id, 'id', $discussion->id);
        set_field('forum_read', 'forumid', $forumto->id, 'discussionid', $discussion->id);
        add_to_log($course->id, 'forum', 'move discussion', "discuss.php?d=$discussion->id", $discussion->id, $cmto->id);

        require_once($CFG->libdir.'/rsslib.php');
        require_once('rsslib.php');

        // Delete the RSS files for the 2 forums because we want to force
        // the regeneration of the feeds since the discussions have been
        // moved.
        if (!forum_rss_delete_file($forum) || !forum_rss_delete_file($forumto)) {
            error('Could not purge the cached RSS feeds for the source and/or'.
                   'destination forum(s) - check your file permissionsforums', $return);
        }

        redirect($return.'&amp;moved=-1&amp;sesskey='.sesskey());
    }

    $logparameters = "d=$discussion->id";
    if ($parent) {
        $logparameters .= "&amp;parent=$parent";
    }

    add_to_log($course->id, 'forum', 'view discussion', "discuss.php?$logparameters", $discussion->id, $cm->id);

    unset($SESSION->fromdiscussion);

    if ($mode) {
        set_user_preference('forum_displaymode', $mode);
    }

    $displaymode = get_user_preferences('forum_displaymode', $CFG->forum_displaymode);

    if ($parent) {
        // If flat AND parent, then force nested display this time
        if ($displaymode == FORUM_MODE_FLATOLDEST or $displaymode == FORUM_MODE_FLATNEWEST) {
            $displaymode = FORUM_MODE_NESTED;
        }
    } else {
        $parent = $discussion->firstpost;
    }

    if (! $post = forum_get_post_full($parent)) {
        error("Discussion no longer exists", "$CFG->wwwroot/mod/forum/view.php?f=$forum->id");
    }


    if (!forum_user_can_view_post($post, $course, $cm, $forum, $discussion)) {
        error('You do not have permissions to view this post', "$CFG->wwwroot/mod/forum/view.php?id=$forum->id");
    }

    if ($mark == 'read' or $mark == 'unread') {
        if ($CFG->forum_usermarksread && forum_tp_can_track_forums($forum) && forum_tp_is_tracked($forum)) {
            if ($mark == 'read') {
                forum_tp_add_read_record($USER->id, $postid);
            } else {
                // unread
                forum_tp_delete_read_records($USER->id, $postid);
            }
        }
    }

    $searchform = forum_search_form($course);

    $navlinks = array();
    $navlinks[] = array('name' => format_string($discussion->name), 'link' => "discuss.php?d=$discussion->id", 'type' => 'title');
    if ($parent != $discussion->firstpost) {
        $navlinks[] = array('name' => format_string($post->subject), 'type' => 'title');
    }

    $navigation = build_navigation($navlinks, $cm);
    print_header("$course->shortname: ".format_string($discussion->name), $course->fullname,
                     $navigation, "", "", true, $searchform, navmenu($course, $cm));


/// Check to see if groups are being used in this forum
/// If so, make sure the current person is allowed to see this discussion
/// Also, if we know they should be able to reply, then explicitly set $canreply for performance reasons

    if (isguestuser() or !isloggedin() or has_capability('moodle/legacy:guest', $modcontext, NULL, false)) {
        // allow guests and not-logged-in to see the link - they are prompted to log in after clicking the link
        $canreply = ($forum->type != 'news'); // no reply in news forums

    } else {
        $canreply = forum_user_can_post($forum, $discussion, $USER, $cm, $course, $modcontext);
    }

/// Print the controls across the top

    echo '<table width="100%" class="discussioncontrols"><tr><td>';

    // groups selector not needed here

    echo "</td><td>";
    forum_print_mode_form($discussion->id, $displaymode);
    echo "</td><td>";

    if ($forum->type != 'single'
                && has_capability('mod/forum:movediscussions', $modcontext)) {

        // Popup menu to move discussions to other forums. The discussion in a
        // single discussion forum can't be moved.
        $modinfo = get_fast_modinfo($course);
        if (isset($modinfo->instances['forum'])) {
            if ($course->format == 'weeks') {
                $strsection = get_string("week");
            } else {
                $strsection = get_string("topic");
            }
            $section = -1;
            $forummenu = array();
            foreach ($modinfo->instances['forum'] as $forumcm) {
                if (!$forumcm->uservisible || !has_capability('mod/forum:startdiscussion',
                    get_context_instance(CONTEXT_MODULE,$forumcm->id))) {
                    continue;
                }

                if (!empty($forumcm->sectionnum) and $section != $forumcm->sectionnum) {
                    $forummenu[] = "-------------- $strsection $forumcm->sectionnum --------------";
                }
                $section = $forumcm->sectionnum;
                if ($forumcm->instance != $forum->id) {
                    $url = "discuss.php?d=$discussion->id&amp;move=$forumcm->instance&amp;sesskey=".sesskey();
                    $forummenu[$url] = format_string($forumcm->name);
                }
            }
            if (!empty($forummenu)) {
                echo "<div style=\"float:right;\">";
                echo popup_form("$CFG->wwwroot/mod/forum/", $forummenu, "forummenu", "",
                                 get_string("movethisdiscussionto", "forum"), "", "", true,'self','',NULL,
                                 get_string('move'));
                echo "</div>";
            }
        }
    }
    echo "</td></tr></table>";

    if (!empty($forum->blockafter) && !empty($forum->blockperiod)) {
        $a = new object();
        $a->blockafter  = $forum->blockafter;
        $a->blockperiod = get_string('secondstotime'.$forum->blockperiod);
        notify(get_string('thisforumisthrottled','forum',$a));
    }

    if ($forum->type == 'qanda' && !has_capability('mod/forum:viewqandawithoutposting', $modcontext) &&
                !forum_user_has_posted($forum->id,$discussion->id,$USER->id)) {
        notify(get_string('qandanotify','forum'));
    }

    if ($move == -1 and confirm_sesskey()) {
        notify(get_string('discussionmoved', 'forum', format_string($forum->name,true)));
    }

    $canrate = has_capability('mod/forum:rate', $modcontext);
    forum_print_discussion($course, $cm, $forum, $discussion, $post, $displaymode, $canreply, $canrate);

    print_footer($course);


?>
