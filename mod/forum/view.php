<?php  // $Id$

    require_once("../../config.php");
    require_once("lib.php");
    require_once("$CFG->libdir/rsslib.php");

    $id      = optional_param('id', 0, PARAM_INT);        // Course Module ID
    $f       = optional_param('f', 0, PARAM_INT);         // Forum ID
    $mode    = optional_param('mode', 0, PARAM_INT);      // Display mode (for single forum)
    $showall = optional_param('showall', '', PARAM_INT);  // show all discussions on one page
    $group   = optional_param('group', -1, PARAM_INT);    // choose the current group
    $page    = optional_param('page', 0, PARAM_INT);      // which page to show
    $search  = optional_param('search', '');              // search string

    if ($id) {
        if (! $cm = get_record("course_modules", "id", $id)) {
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
            $cm->id = NULL;
            $buttontext = "";
        }

    } else {
        error("Must specify a course module or a forum ID");
    }

    if (!$buttontext) {
        $buttontext = forum_print_search_form($course, $search, true, "plain");
    }

    if ($CFG->forcelogin) {
        require_login();
    }

    if ($course->category) {
        require_login($course->id);
    }
    $navigation = "<a href=\"index.php?id=$course->id\">$strforums</a> ->";

    if ($forum->type == "teacher") {
        if (!isteacher($course->id)) {
            error("You must be a $course->teacher to view this forum");
        }
    }

    if ($cm) {
        add_to_log($course->id, "forum", "view forum", "view.php?id=$cm->id", "$forum->id", $cm->id);
    } else {
        add_to_log($course->id, "forum", "view forum", "view.php?f=$forum->id", "$forum->id");
    }

    print_header_simple("$forum->name", "",
                 "$navigation $forum->name", "", "", true, $buttontext, navmenu($course, $cm));

    echo '<div id="forum-view" class="forum">';  // forum-view wrapper start

    if (!$cm->visible and !isteacher($course->id)) {
        notice(get_string("activityiscurrentlyhidden"));
    }


/// Check to see if groups are being used in this forum
/// and if so, set $currentgroup to reflect the current group

    $changegroup = isset($_GET['group']) ? $_GET['group'] : -1;  // Group change requested?

    if ($forum->type == "teacher") {
        $groupmode = NOGROUPS;
    } else {
        $groupmode = groupmode($course, $cm);   // Groups are being used
    }
    $currentgroup = get_and_set_current_group($course, $groupmode, $changegroup);

    if ($groupmode and ($currentgroup === false) and !isteacheredit($course->id)) {
        print_heading(get_string("notingroup", "forum"));
        print_footer($course);
        exit;
    }


/// Print settings and things in a table across the top

    echo '<table width="100%" border="0" cellpadding="3" cellspacing="0"><tr valign="top">';

    if ($groupmode == VISIBLEGROUPS or ($groupmode and isteacheredit($course->id))) {
        if ($groups = get_records_menu("groups", "courseid", $course->id, "name ASC", "id,name")) {
            echo '<td>';
            print_group_menu($groups, $groupmode, $currentgroup, "view.php?id=$cm->id");
            echo '</td>';
        }
    }


    if (!empty($USER->id)) {
        echo '<td align="right">';
        $SESSION->fromdiscussion = "$FULLME";
        if (forum_is_forcesubscribed($forum->id)) {
            $streveryoneissubscribed = get_string("everyoneissubscribed", "forum");
            $strallowchoice = get_string("allowchoice", "forum");
            helpbutton("subscription", $streveryoneissubscribed, "forum");
            echo "<font size=\"1\">";
            if (isteacher($course->id)) {
                echo "<a title=\"$strallowchoice\" href=\"subscribe.php?id=$forum->id&amp;force=no\">$streveryoneissubscribed</a>";
            } else {
                echo $streveryoneissubscribed;
            }
            echo "</font>";

        } else {
            $streveryonecanchoose = get_string("everyonecanchoose", "forum");
            $strforcesubscribe = get_string("forcesubscribe", "forum");
            $strshowsubscribers = get_string("showsubscribers", "forum");

            helpbutton("subscription", $streveryonecanchoose, "forum");
            echo "<font size=\"1\">";

            if (isteacher($course->id)) {
                echo "<a title=\"$strforcesubscribe\" href=\"subscribe.php?id=$forum->id&amp;force=yes\">$streveryonecanchoose</a>";
                echo "</font><br /><font size=\"1\">";
                echo "<a href=\"subscribers.php?id=$forum->id\">$strshowsubscribers</a>";
            } else {
                echo $streveryonecanchoose;
            }
            echo "</font>";

            if (forum_is_subscribed($USER->id, $forum->id)) {
                $subtexttitle = get_string("subscribestop", "forum");
                $subtext = get_string("unsubscribe", "forum");
            } else {
                $subtexttitle = get_string("subscribestart", "forum");
                $subtext = get_string("subscribe", "forum");
            }
            echo "<br />";
            echo "<font size=\"1\"><a title=\"$subtexttitle\" href=\"subscribe.php?id=$forum->id\">$subtext</a></font>";
        }

        echo '</td>';
    }

    //If rss are activated at site and forum level and this forum has rss defined, show link
    if (isset($CFG->enablerssfeeds) && isset($CFG->forum_enablerssfeeds) &&
        $CFG->enablerssfeeds && $CFG->forum_enablerssfeeds && $forum->rsstype and $forum->rssarticles) {
        echo '</tr><tr><td align="right">';
        if ($forum->rsstype == 1) {
            $tooltiptext = get_string("rsssubscriberssdiscussions","forum",$forum->name);
        } else {
            $tooltiptext = get_string("rsssubscriberssposts","forum",$forum->name);
        }
        if (empty($USER->id)) {
            $userid = 0;
        } else {
            $userid = $USER->id;
        }
        rss_print_link($course->id, $userid, "forum", $forum->id, $tooltiptext);
        echo '</td>';
    }

    echo '</tr></table>';

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
            forum_print_discussion($course, $forum, $discussion, $post, $displaymode);
            break;

        case 'eachuser':
            if (!empty($forum->intro)) {
                print_simple_box(format_text($forum->intro), 'center');
            }
            echo '<p align="center">';
            if (forum_user_can_post_discussion($forum)) {
                print_string("allowsdiscussions", "forum");
            } else {
                echo '&nbsp';
            }
            echo '</p>';
            if (!empty($showall)) {
                forum_print_latest_discussions($forum->id, 0, 'header', '', $currentgroup, $groupmode);
            } else {
                forum_print_latest_discussions($forum->id, $CFG->forum_manydiscussions, 'header', '', $currentgroup, $groupmode, $page);
            }
            break;

        case 'teacher':
            if (!empty($showall)) {
                forum_print_latest_discussions($forum->id, 0, 'header', '', $currentgroup, $groupmode);
            } else {
                forum_print_latest_discussions($forum->id, $CFG->forum_manydiscussions, 'header', '', $currentgroup, $groupmode, $page);
            }
            break;

        default:
            if (!empty($forum->intro)) {
                print_simple_box(format_text($forum->intro), 'center');
            }
            echo '<br />';
            if (!empty($showall)) {
                forum_print_latest_discussions($forum->id, 0, 'header', '', $currentgroup, $groupmode);
            } else {
                forum_print_latest_discussions($forum->id, $CFG->forum_manydiscussions, 'header', '', $currentgroup, $groupmode, $page);
            }
            break;
    }

    echo '</div>';  // forum-view wrapper end

    print_footer($course);

?>
