<?PHP  // $Id$

    require_once("../../config.php");
    require_once("lib.php");
    require_once("$CFG->dirroot/rss/rsslib.php");

    optional_variable($id);            // Course Module ID
    optional_variable($f);             // Forum ID
    optional_variable($mode);          // Display mode (for single forum)
    optional_variable($search, "");    // search string
    optional_variable($showall, "");   // show all discussions on one page
    optional_variable($group, -1);     // choose the current group

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
        $navigation = "<a href=\"../../course/view.php?id=$course->id\">$course->shortname</a> ->
                       <a href=\"index.php?id=$course->id\">$strforums</a> ->";
    } else {
        $navigation = "<a href=\"index.php?id=$course->id\">$strforums</a> ->";
    }

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

    print_header("$course->shortname: $forum->name", "$course->fullname",
                 "$navigation $forum->name", "", "", true, $buttontext, navmenu($course, $cm));

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


    if ($USER) {
        echo '<td align="right">';
        $SESSION->fromdiscussion = "$FULLME";
        if (forum_is_forcesubscribed($forum->id)) {
            $streveryoneissubscribed = get_string("everyoneissubscribed", "forum");
            $strallowchoice = get_string("allowchoice", "forum");
            helpbutton("subscription", $streveryoneissubscribed, "forum");
            echo "<font size=1>";
            if (isteacher($course->id)) {
                echo "<a title=\"$strallowchoice\" href=\"subscribe.php?id=$forum->id&force=no\">$streveryoneissubscribed</a>";
            } else {
                echo $streveryoneissubscribed;
            }
            echo "</font>";

        } else {
            $streveryonecanchoose = get_string("everyonecanchoose", "forum");
            $strforcesubscribe = get_string("forcesubscribe", "forum");
            $strshowsubscribers = get_string("showsubscribers", "forum");

            helpbutton("subscription", $streveryonecanchoose, "forum");
            echo "<font size=1>";

            if (isteacher($course->id)) {
                echo "<a title=\"$strforcesubscribe\" href=\"subscribe.php?id=$forum->id&force=yes\">$streveryonecanchoose</a>";
                echo "</font><br /><font size=1>";
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
            echo "<font size=1><a title=\"$subtexttitle\" href=\"subscribe.php?id=$forum->id\">$subtext</a></font>";
        }

        //If rss are activated at site and forum level and this forum has rss defined, show link
        if ($CFG->enablerssfeeds && $CFG->forum_enablerssfeeds && $forum->rsstype and $forum->rssarticles) {
            echo "<br />";
            if ($forum->rsstype == 1) {
                $tooltiptext = get_string("rsssubscriberssdiscussions","forum",$forum->name);
            } else {
                $tooltiptext = get_string("rsssubscriberssposts","forum",$forum->name);
            }
            rss_print_link($course->id, $USER->id, "forum", $forum->id, $tooltiptext);
        }

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
                forum_print_latest_discussions($forum->id, $CFG->forum_manydiscussions, 'header', '', $currentgroup, $groupmode);
            }
            break;

        case 'teacher':
            if (!empty($showall)) {
                forum_print_latest_discussions($forum->id, 0, 'header', '', $currentgroup, $groupmode);
            } else {
                forum_print_latest_discussions($forum->id, $CFG->forum_manydiscussions, 'header', '', $currentgroup, $groupmode);
            }
            break;

        default:
            if (!empty($forum->intro)) {
                print_simple_box(format_text($forum->intro), 'center');
            }
            echo '<p>&nbsp;</p>';
            if (!empty($showall)) {
                forum_print_latest_discussions($forum->id, 0, 'header', '', $currentgroup, $groupmode);
            } else {
                forum_print_latest_discussions($forum->id, $CFG->forum_manydiscussions, 'header', '', $currentgroup, $groupmode);
            }
            break;
    }


    print_footer($course);

?>
