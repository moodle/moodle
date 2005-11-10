<?php // $Id$

//  Displays a post, and all the posts below it.
//  If no post is given, displays all posts in a discussion

    require_once("../../config.php");
    require_once("lib.php");

    $d = required_param('d',PARAM_INT);       // Discussion ID
    $parent = optional_param('parent',0,PARAM_INT); // If set, then display this post and all children.
    $mode = optional_param('mode',0,PARAM_INT);   // If set, changes the layout of the thread
    $move = optional_param('move',0,PARAM_INT);   // If set, moves this discussion to another forum
    $mark = optional_param('mark',0,PARAM_INT);   // Used for tracking read posts if user initiated.
    $postid = optional_param('postid',0,PARAM_INT); // Used for tracking read posts if user initiated.

    if (! $discussion = get_record("forum_discussions", "id", $d)) {
        error("Discussion ID was incorrect or no longer exists");
    }

    if (! $course = get_record("course", "id", $discussion->course)) {
        error("Course ID is incorrect - discussion is faulty");
    }

    if (! $forum = get_record("forum", "id", $discussion->forum)) {
        notify("Bad forum ID stored in this discussion");
    }

    if ($forum->type == "teacher") {
        require_login($course->id);

        if (!isteacher($course->id)) {
            error("You must be a $course->teacher to view this forum");
        }

    } elseif ($forum->type == "news") {
        if (!((isadmin() and !empty($CFG->admineditalways))
            || isteacher($course->id)
            || $USER->id == $discussion->userid
            || (($discussion->timestart == 0 || $discussion->timestart <= time())
            && ($discussion->timeend == 0 || $discussion->timeend > time())))) {
            error('Discussion ID was incorrect or no longer exists', "$CFG->wwwroot/mod/forum/view.php?f=$forum->id");
        }

    } else {
        if (! $cm = get_coursemodule_from_instance("forum", $discussion->forum, $course->id)) {
            error("Course Module ID was incorrect");
        }
        require_course_login($course, false, $cm);
    }


    if (!empty($move)) {
        if (!isteacher($course->id)) {
            error("Only teachers can do that!");
        }
        if ($forum = get_record("forum", "id", $move)) {
            if (!forum_move_attachments($discussion, $move)) {
                notify("Errors occurred while moving attachment directories - check your file permissions");
            }
            set_field("forum_discussions", "forum", $forum->id, "id", $discussion->id);
            $discussion->forum = $forum->id;
            if ($cm = get_coursemodule_from_instance("forum", $forum->id, $course->id)) {
                add_to_log($course->id, "forum", "move discussion", "discuss.php?d=$discussion->id", "$discussion->id",
                           $cm->id);
            } else {
                add_to_log($course->id, "forum", "move discussion", "discuss.php?d=$discussion->id", "$discussion->id");
            }
            $discussionmoved = true;
        } else {
            error("You can't move to that forum - it doesn't exist!");
        }
    }

    $logparameters = "d=$discussion->id";
    if ($parent) {
        $logparameters .= "&amp;parent=$parent";
    }

    if ($cm = get_coursemodule_from_instance("forum", $forum->id, $course->id)) {
        add_to_log($course->id, "forum", "view discussion", "discuss.php?$logparameters", "$discussion->id", $cm->id);
    } else {
        add_to_log($course->id, "forum", "view discussion", "discuss.php?$logparameters", "$discussion->id");
    }

    unset($SESSION->fromdiscussion);

    if ($mode) {
        if (isguest()) {
            $USER->preference['forum_displaymode'] = $mode;  // don't save it in database
        } else {
            set_user_preference('forum_displaymode', $mode);
        }
    }

    $displaymode = get_user_preferences('forum_displaymode', $CFG->forum_displaymode);

    if ($parent) {
        if (abs($displaymode) == 1) {  // If flat AND parent, then force nested display this time
            $displaymode = 3;
        }
    } else {
        $parent = $discussion->firstpost;
        $navtail = format_string($discussion->name);
    }

    if (! $post = forum_get_post_full($parent)) {
        error("Discussion no longer exists", "$CFG->wwwroot/mod/forum/view.php?f=$forum->id");
    }

    if (forum_tp_can_track_forums($forum) && forum_tp_is_tracked($forum) && 
        $CFG->forum_usermarksread) {
        if ($mark == 'read') {
            forum_tp_add_read_record($USER->id, $postid, $discussion->id, $forum->id);
        } else if ($mark == 'unread') {
            forum_tp_delete_read_records($USER->id, $postid);
        }
    }

    if (empty($navtail)) {
        $navtail = "<a href=\"discuss.php?d=$discussion->id\">".format_string($discussion->name,true)."</a> -> ".format_string($post->subject);
    }

    $navmiddle = "<a href=\"../forum/index.php?id=$course->id\">".get_string("forums", "forum")."</a> -> <a href=\"../forum/view.php?f=$forum->id\">".format_string($forum->name,true)."</a>";

    $searchform = forum_search_form($course);

    if ($course->category) {
        print_header("$course->shortname: ".format_string($discussion->name), "$course->fullname",
                 "<a href=\"../../course/view.php?id=$course->id\">$course->shortname</a> ->
                  $navmiddle -> $navtail", "", "", true, $searchform, navmenu($course, $cm));
    } else {
        print_header("$course->shortname: ".format_string($discussion->name), "$course->fullname",
                 "$navmiddle -> $navtail", "", "", true, $searchform, navmenu($course, $cm));
    }


/// Check to see if groups are being used in this forum
/// If so, make sure the current person is allowed to see this discussion
/// Also, if we know they should be able to reply, then explicitly set $canreply

    $canreply = NULL;   /// No override one way or the other

    if ($forum->type == "teacher") {
        $groupmode = NOGROUPS;
    } else {
        $groupmode = groupmode($course, $cm);
    }

    if ($groupmode and !isteacheredit($course->id)) {   // Groups must be kept separate
        //change this to ismember
        $mygroupid = mygroupid($course->id);//only useful if 0, otherwise it's an array now
        if ($groupmode == SEPARATEGROUPS) {
            require_login();

            if ((empty($mygroupid) and $discussion->groupid == -1) || (ismember($discussion->groupid) || $mygroupid == $discussion->groupid)) {
                $canreply = true;
            } elseif ($discussion->groupid == -1) {
                $canreply = false;
            } else {
                print_heading("Sorry, you can't see this discussion because you are not in this group");
                print_footer($course);
                die;
            }

        } else if ($groupmode == VISIBLEGROUPS) {
            $canreply = ((empty($mygroupid) and $discussion->groupid == -1) || (ismember($discussion->groupid) || $mygroupid == $discussion->groupid));
        }
    }


/// Print the controls across the top

    echo '<table width="100%"><tr><td width="33%">';

    if ($groupmode == VISIBLEGROUPS or ($groupmode and isteacheredit($course->id))) {
        if ($groups = get_records_menu('groups', 'courseid', $course->id, 'name ASC', 'id,name')) {
            print_group_menu($groups, $groupmode, $discussion->groupid, "view.php?id=$cm->id&amp;group=");
        }
    }

    echo "</td><td width=\"33%\">";
    forum_print_mode_form($discussion->id, $displaymode);

    echo "</td><td width=\"33%\">";
    if (isteacher($course->id) && $forum->type != "teacher") { // Popup menu to move discussions to other forums
        if ($forums = get_all_instances_in_course("forum", $course)) {
            if ($course->format == 'weeks') {
                $strsection = get_string("week");
            } else {
                $strsection = get_string("topic");
            }
            $section = -1;
            foreach ($forums as $courseforum) {
                if (!empty($courseforum->section) and $section != $courseforum->section) {
                    $forummenu[] = "-------------- $strsection $courseforum->section --------------";
                }
                $section = $courseforum->section;
                if ($courseforum->id != $forum->id) {
                    $url = "discuss.php?d=$discussion->id&amp;move=$courseforum->id";
                    $forummenu[$url] = format_string($courseforum->name,true);
                }
            }
            if (!empty($forummenu)) {
                echo "<div align=\"right\">";
                echo popup_form("$CFG->wwwroot/mod/forum/", $forummenu, "forummenu", "",
                                 get_string("movethisdiscussionto", "forum"), "", "", true);
                echo "</div>";
            }
        }
    }
    echo "</td></tr></table>";

    if (isset($discussionmoved)) {
        notify(get_string("discussionmoved", "forum", format_string($forum->name,true)));
    }


/// Print the actual discussion

    forum_print_discussion($course, $forum, $discussion, $post, $displaymode, $canreply);

    print_footer($course);

?>
