<?PHP // $Id$

//  Displays a post, and all the posts below it.
//  If no post is given, displays all posts in a discussion

    require_once("../../config.php");
    require_once("lib.php");

    require_variable($d);       // Discussion ID
    optional_variable($parent); // If set, then display this post and all children.
    optional_variable($mode);   // If set, changes the layout of the thread
    optional_variable($move);   // If set, moves this discussion to another forum

    if (! $discussion = get_record("forum_discussions", "id", $d)) {
        error("Discussion ID was incorrect or no longer exists");
    }

    if (! $course = get_record("course", "id", $discussion->course)) {
        error("Course ID is incorrect - discussion is faulty");
    }

    if ($course->category) {
        require_login($course->id);
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
            add_to_log($course->id, "forum", "move discussion", "discuss.php?d=$discussion->id", "$discussion->id");
            $discussionmoved = true;
        } else {
            error("You can't move to that forum - it doesn't exist!");
        }
    }

    if (empty($forum)) {
        if (! $forum = get_record("forum", "id", $discussion->forum)) {
            notify("Bad forum ID stored in this discussion");
        }
    }

    if (!$cm = get_coursemodule_from_instance("forum", $forum->id, $course->id)) {
        //notify("Bad coursemodule for this discussion");  // Only affects navmenu
    }

    $logparameters = "d=$discussion->id";
    if ($parent) {
        $logparameters .= "&parent=$parent";
    }
    add_to_log($course->id, "forum", "view discussion", "discuss.php?$logparameters", "$discussion->id");

    unset($SESSION->fromdiscussion);

    forum_set_display_mode($mode);

    $displaymode = $USER->mode;

    if ($parent) {
        if (abs($USER->mode) == 1) {  // If flat AND parent, then force nested display this time
            $displaymode = 3;
        }
    } else {
        $parent = $discussion->firstpost;
        $navtail = "$discussion->name";
    }

    if (! $post = forum_get_post_full($parent)) {
        error("Discussion no longer exists", "$CFG->wwwroot/mod/forum/view.php?f=$forum->id");
    }

    if (empty($navtail)) {
        $navtail = "<A HREF=\"discuss.php?d=$discussion->id\">$discussion->name</A> -> $post->subject";
    }

    $navmiddle = "<A HREF=\"../forum/index.php?id=$course->id\">".get_string("forums", "forum")."</A> -> <A HREF=\"../forum/view.php?f=$forum->id\">$forum->name</A>";

    $searchform = forum_print_search_form($course, "", true, "plain");

    if ($course->category) {
        print_header("$course->shortname: $discussion->name", "$course->fullname",
                 "<A HREF=../../course/view.php?id=$course->id>$course->shortname</A> ->
                  $navmiddle -> $navtail", "", "", true, $searchform, navmenu($course, $cm));
    } else {
        print_header("$course->shortname: $discussion->name", "$course->fullname",
                 "$navmiddle -> $navtail", "", "", true, $searchform, navmenu($course, $cm));
    }


/// Check to see if groups are being used in this forum
/// If so, make sure the current person is allowed to see this discussion
/// Also, if we know they should be able to reply, then explicitly set $canreply

    $canreply = NULL;   /// No override one way or the other

    $groupmode = groupmode($course, $cm);

    if ($groupmode and !isteacheredit($course->id)) {   // Groups must be kept separate
        if ($groupmode == SEPARATEGROUPS) {
            require_login();

            if (mygroupid($course->id) == $discussion->groupid) {
                $canreply = true;
            } else {
                print_heading("Sorry, you can't see this discussion because you are not in this group");
                print_footer();
                die;
            }

        } else if ($groupmode == VISIBLEGROUPS) {
            $canreply = (mygroupid($course->id) == $discussion->groupid);
        }
    }


/// Print the controls across the top

    echo "<table width=\"100%\"><tr><td width=\"33%\">";

    if ($groupmode == VISIBLEGROUPS or ($groupmode and isteacheredit($course->id))) {
        if ($groups = get_records_menu("groups", "courseid", $course->id, "name ASC", "id,name")) {

            echo '<table><tr><td>';
            if ($groupmode == VISIBLEGROUPS) {
                print_string('groupsvisible');
            } else {
                print_string('groupsseparate');
            }
            echo ':';
            echo '</td><td nowrap="nowrap" align="left" width="50%">';
            popup_form("view.php?id=$cm->id&group=", $groups, 'selectgroup', 
                       $discussion->groupid, "", "", "", false, "self");
            echo '</tr></table>';

            echo '</td>';
        }
    }

    echo "</td><td width=\"33%\">";
    forum_print_mode_form($discussion->id, $displaymode);

    echo "</td><td width=\"33%\">";
    if (isteacher($course->id)) {    // Popup menu to allow discussions to be moved to other forums
        if ($forums = get_all_instances_in_course("forum", $course)) {
            foreach ($forums as $courseforum) {
                if ($courseforum->id != $forum->id) {
                    $url = "discuss.php?d=$discussion->id&move=$courseforum->id";
                    $forummenu[$url] = $courseforum->name;
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
        notify(get_string("discussionmoved", "forum", $forum->name));
    }


/// Print the actual discussion

    forum_print_discussion($course, $forum, $discussion, $post, $displaymode, $canreply);


    print_footer($course);

?>
