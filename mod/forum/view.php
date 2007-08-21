<?php  // $Id$

    require_once('../../config.php');
    require_once('lib.php');
    require_once("$CFG->libdir/rsslib.php");


    $id          = optional_param('id', 0, PARAM_INT);       // Course Module ID
    $f           = optional_param('f', 0, PARAM_INT);        // Forum ID
    $mode        = optional_param('mode', 0, PARAM_INT);     // Display mode (for single forum)
    $showall     = optional_param('showall', '', PARAM_INT); // show all discussions on one page
    $changegroup = optional_param('group', -1, PARAM_INT);   // choose the current group
    $page        = optional_param('page', 0, PARAM_INT);     // which page to show
    $search      = optional_param('search', '');             // search string



    if ($id) {

        if (! $cm = get_coursemodule_from_id('forum', $id)) {
            error("Course Module ID was incorrect");
        }
        if (! $course = get_record("course", "id", $cm->course)) {
            error("Course is misconfigured");
        }
        if (! $forum = get_record("forum", "id", $cm->instance)) {
            error("Forum ID was incorrect");
        }
        $strforums = get_string("modulenameplural", "forum");
        $strforum = get_string("modulename", "forum");
        $buttontext = update_module_button($cm->id, $course->id, $strforum);

    } else if ($f) {

        if (! $forum = get_record("forum", "id", $f)) {
            error("Forum ID was incorrect or no longer exists");
        }
        if (! $course = get_record("course", "id", $forum->course)) {
            error("Forum is misconfigured - don't know what course it's from");
        }

        $strforums = get_string("modulenameplural", "forum");
        $strforum = get_string("modulename", "forum");

        if ($cm = get_coursemodule_from_instance("forum", $forum->id, $course->id)) {
            $buttontext = update_module_button($cm->id, $course->id, $strforum);
        } else {
            $cm->id = 0;
            $cm->visible = 1;
            $cm->course = $course->id;
            $buttontext = "";
        }
    } else {
        error('Must specify a course module or a forum ID');
    }

    if (!$buttontext) {
        $buttontext = forum_search_form($course, $search);
    }


    require_course_login($course, true, $cm);
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);


/// Print header.
    $navlinks = array();
    $navlinks[] = array('name' => $strforums, 'link' => "index.php?id=$course->id", 'type' => 'activity');
    $navlinks[] = array('name' => format_string($forum->name), 'link' => "view.php?f=$forum->id", 'type' => 'activityinstance');
    
    $navigation = build_navigation($navlinks);
    
    print_header_simple(format_string($forum->name), "",
                 $navigation, "", "", true, $buttontext, navmenu($course, $cm));


/// Some capability checks.
    if (empty($cm->visible) and !has_capability('moodle/course:viewhiddenactivities', $context)) {
        notice(get_string("activityiscurrentlyhidden"));
    }
    
    if (!has_capability('mod/forum:viewdiscussion', $context)) {
        notice(get_string('noviewdiscussionspermission', 'forum'));
    }
    
/// find out current groups mode
    groups_print_activity_menu($cm, 'view.php?id=' . $cm->id);
    $currentgroup = groups_get_activity_group($cm);
    $groupmode = groups_get_activity_groupmode($cm);

/// Okay, we can show the discussions. Log the forum view.
    if ($cm->id) {
        add_to_log($course->id, "forum", "view forum", "view.php?id=$cm->id", "$forum->id", $cm->id);
    } else {
        add_to_log($course->id, "forum", "view forum", "view.php?f=$forum->id", "$forum->id");
    }



/// Print settings and things across the top

    // If it's a simple single discussion forum, we need to print the display
    // mode control.
    if ($forum->type == 'single') {
        if (! $discussion = get_record("forum_discussions", "forum", $forum->id)) {
            if ($discussions = get_records("forum_discussions", "forum", $forum->id, "timemodified ASC")) {
                $discussion = array_pop($discussions);
            }
        }
        if ($discussion) {
            if ($mode) {
                set_user_preference("forum_displaymode", $mode);
            }
            $displaymode = get_user_preferences("forum_displaymode", $CFG->forum_displaymode);
            forum_print_mode_form($forum->id, $displaymode, $forum->type);
        }
    }


    print_box_start('forumcontrol');

    print_box_start('subscription');

    if (!empty($USER->id) && !has_capability('moodle/legacy:guest', $context, NULL, false)) {
        $SESSION->fromdiscussion = "$FULLME";
        if (forum_is_forcesubscribed($forum->id)) {
            $streveryoneisnowsubscribed = get_string('everyoneisnowsubscribed', 'forum');
            $strallowchoice = get_string('allowchoice', 'forum');
            echo '<span class="helplink">' . get_string("forcessubscribe", 'forum') . '</span><br />';
            helpbutton("subscription", $strallowchoice, "forum");
            echo '&nbsp;<span class="helplink">';
            if (has_capability('moodle/course:manageactivities', $context)) {
                echo "<a title=\"$strallowchoice\" href=\"subscribe.php?id=$forum->id&amp;force=no\">$strallowchoice</a>";
            } else {
                echo $streveryoneisnowsubscribed;
            }
            echo '</span>';

        } else if ($forum->forcesubscribe == FORUM_DISALLOWSUBSCRIBE) {
            $strsubscriptionsoff = get_string('disallowsubscribe','forum');
            echo $strsubscriptionsoff;
            helpbutton("subscription", $strsubscriptionsoff, "forum");
        } else {
            $streveryonecannowchoose = get_string("everyonecannowchoose", "forum");
            $strforcesubscribe = get_string("forcesubscribe", "forum");
            $strshowsubscribers = get_string("showsubscribers", "forum");
            echo '<span class="helplink">' . get_string("allowsallsubscribe", 'forum') . '</span><br />';
            helpbutton("subscription", $strforcesubscribe, "forum");
            echo '&nbsp;';
            if (has_capability('moodle/course:manageactivities', $context)) {
                echo "<span class=\"helplink\"><a title=\"$strforcesubscribe\" href=\"subscribe.php?id=$forum->id&amp;force=yes\">$strforcesubscribe</a></span>";
                echo "<br />";
                echo "<span class=\"helplink\"><a href=\"subscribers.php?id=$forum->id\">$strshowsubscribers</a></span>";
            } else {
                echo '<span class="helplink">'.$streveryonecannowchoose.'</span>';
            }

            echo '<br /><span class="helplink" id="subscriptionlink">', forum_get_subscribe_link($forum, $context, 
                    array('forcesubscribed' => '', 'cantsubscribe' => '')), '</span>';
        }

        if (forum_tp_can_track_forums($forum) && ($forum->trackingtype == FORUM_TRACKING_OPTIONAL)) {
            if (forum_tp_is_tracked($forum, $USER->id)) {
                $trtitle = get_string('notrackforum', 'forum');
                $trackedlink = '<a title="'.get_string('notrackforum', 'forum').'" href="settracking.php?id='.
                               $forum->id.'&amp;returnpage=view.php">'.get_string('forumtracked', 'forum').'</a>';
            } else {
                $trtitle = get_string('trackforum', 'forum');
                $trackedlink = '<a title="'.get_string('trackforum', 'forum').'" href="settracking.php?id='.
                               $forum->id.'&amp;returnpage=view.php">'.get_string('forumtrackednot', 'forum').'</a>';
            }
            echo '<br />';
            echo "<span class=\"helplink\">$trackedlink</span>";
        }

    }

    /// If rss are activated at site and forum level and this forum has rss defined, show link
    if (isset($CFG->enablerssfeeds) && isset($CFG->forum_enablerssfeeds) &&
        $CFG->enablerssfeeds && $CFG->forum_enablerssfeeds && $forum->rsstype and $forum->rssarticles) {

        if ($forum->rsstype == 1) {
            $tooltiptext = get_string("rsssubscriberssdiscussions","forum",format_string($forum->name));
        } else {
            $tooltiptext = get_string("rsssubscriberssposts","forum",format_string($forum->name));
        }
        if (empty($USER->id)) {
            $userid = 0;
        } else {
            $userid = $USER->id;
        }
        print_box_start('rsslink');
        rss_print_link($course->id, $userid, "forum", $forum->id, $tooltiptext);
        print_box_end(); // subscription

    }
    print_box_end(); // subscription

    print_box_end();  // forumcontrol

    print_box('&nbsp;', 'clearer'); 


    if (!empty($forum->blockafter) && !empty($forum->blockperiod)) {
        $a->blockafter = $forum->blockafter;
        $a->blockperiod = get_string('secondstotime'.$forum->blockperiod);
        notify(get_string('thisforumisthrottled','forum',$a));
    }

    if ($forum->type == 'qanda' && !has_capability('moodle/course:manageactivities', $context)) {
        notify(get_string('qandanotify','forum'));
    }

    $forum->intro = trim($forum->intro);

    switch ($forum->type) {
        case 'single':
            if (! $discussion = get_record("forum_discussions", "forum", $forum->id)) {
                if ($discussions = get_records("forum_discussions", "forum", $forum->id, "timemodified ASC")) {
                    notify("Warning! There is more than one discussion in this forum - using the most recent");
                    $discussion = array_pop($discussions);
                } else {
                    error("Could not find the discussion in this forum");
                }
            }
            if (! $post = forum_get_post_full($discussion->firstpost)) {
                error("Could not find the first post in this forum");
            }
            if ($mode) {
                set_user_preference("forum_displaymode", $mode);
            }
            $displaymode = get_user_preferences("forum_displaymode", $CFG->forum_displaymode);
            $canrate = has_capability('mod/forum:rate', $context);
            forum_print_discussion($course, $forum, $discussion, $post, $displaymode, NULL, $canrate);
            break;

        case 'eachuser':
            if (!empty($forum->intro)) {
                print_box(format_text($forum->intro), 'generalbox', 'intro');
            }
            echo '<p align="center">';
            if (forum_user_can_post_discussion($forum)) {
                print_string("allowsdiscussions", "forum");
            } else {
                echo '&nbsp;';
            }
            echo '</p>';
            if (!empty($showall)) {
                forum_print_latest_discussions($course, $forum, 0, 'header', '', $currentgroup, $groupmode);
            } else {
                forum_print_latest_discussions($course, $forum, $CFG->forum_manydiscussions, 'header', '', $currentgroup, $groupmode, $page);
            }
            break;

        case 'teacher':
            if (!empty($showall)) {
                forum_print_latest_discussions($course, $forum, 0, 'header', '', $currentgroup, $groupmode);
            } else {
                forum_print_latest_discussions($course, $forum, $CFG->forum_manydiscussions, 'header', '', $currentgroup, $groupmode, $page);
            }
            break;

        default:
            if (!empty($forum->intro)) {
                print_box(format_text($forum->intro), 'generalbox', 'intro');
            }
            echo '<br />';
            if (!empty($showall)) {
                forum_print_latest_discussions($course, $forum, 0, 'header', '', $currentgroup, $groupmode);
            } else {
                forum_print_latest_discussions($course, $forum, $CFG->forum_manydiscussions, 'header', '', $currentgroup, $groupmode, $page);
            }
            
            
            break;
    }
    print_footer($course);

?>
