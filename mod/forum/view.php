<?PHP  // $Id$

    require("../../config.php");
    require("lib.php");

    optional_variable($id);      // Course Module ID
    optional_variable($f);       // Forum ID
    optional_variable($mode);    // Display mode (for single forum)


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
        $buttontext = update_module_icon($cm->id, $course->id);

    } else if ($f) {
        if (! $forum = get_record("forum", "id", $f)) {
            error("Forum ID was incorrect or no longer exists");
        }
        if (! $course = get_record("course", "id", $forum->course)) {
            error("Forum is misconfigured - don't know what course it's from");
        }
        if ($cm = get_coursemodule_from_instance("forum", $forum->id, $course->id)) {
            $buttontext = update_module_icon($cm->id, $course->id);
        } else {
            $buttontext = "";
        }

    } else {
        error("Must specify a course module or a forum ID");
    }

    if ($course->category) {
        require_login($course->id);
        $navigation = "<A HREF=\"../../course/view.php?id=$course->id\">$course->shortname</A> ->
                       <A HREF=\"index.php?id=$course->id\">Forums</A> ->";
    } else {
        $navigation = "<A HREF=\"index.php?id=$course->id\">Forums</A> ->";
    }

    if ($forum->type == "teacher") {
        if (!isteacher($course->id)) {
            error("You must be a $course->teacher to view this forum");
        }
    }

    add_to_log($course->id, "forum", "view forum", "view.php?f=$forum->id", "$forum->id");

    print_header("$course->shortname: $forum->name", "$course->fullname",
                 "$navigation $forum->name", "", "", true, $buttontext);

    if ($USER) {
        $SESSION->fromdiscussion = "$FULLME";
        if (forum_is_forcesubscribed($forum->id)) {
            $subtext = "Everyone is subscribed to this forum";
            if (isteacher($course->id)) {
                echo "<DIV ALIGN=RIGHT><FONT SIZE=1>";
                echo "<A TITLE=\"Allow people to choose\" HREF=\"subscribe.php?id=$forum->id&force=no\">$subtext</A>";
                echo "</FONT></DIV>";
            } else {
                echo "<DIV ALIGN=RIGHT><FONT SIZE=1>$subtext</FONT></DIV>";
            }

        } else {
            $subtext = "Everyone can choose to be subscribed";
            if (isteacher($course->id)) {
                echo "<DIV ALIGN=RIGHT><FONT SIZE=1>";
                echo "<A TITLE=\"Force everyone to subscribe\" HREF=\"subscribe.php?id=$forum->id&force=yes\">$subtext</A>";
                echo "</FONT></DIV>";
                $subtext = "<A HREF=\"subscribers.php?id=$forum->id\">Show subscribers</A>";
                echo "<DIV ALIGN=RIGHT><FONT SIZE=1>$subtext</FONT></DIV>";
            } else {
                echo "<DIV ALIGN=RIGHT><FONT SIZE=1>$subtext</FONT></DIV>";
            }
            if (forum_is_subscribed($USER->id, $forum->id)) {
                $subtext = "Unsubscribe me";
            } else {
                $subtext = "Subscribe me";
            }
            $subtext = "<A TITLE=\"...this forum only\" HREF=\"subscribe.php?id=$forum->id\">$subtext</A>";
            echo "<DIV ALIGN=RIGHT><FONT SIZE=1>$subtext</FONT></DIV>";
        }
    }


    switch ($forum->type) {
        case "single":
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
            forum_set_display_mode($mode);
            forum_print_discussion($course, $discussion, $post, $USER->mode);
            break;

        case "eachuser":
            print_simple_box(text_to_html($forum->intro), "CENTER");
            echo "<P ALIGN=CENTER>";
            if (forum_user_can_post_discussion($forum)) {
                echo "This forum allows one discussion topic to be posted per person.";
            } else {
                echo "&nbsp";
            }
            echo "</P>";
            forum_print_latest_discussions($forum->id, 0);
            break;

        default:
            print_simple_box(text_to_html($forum->intro), "CENTER");
            echo "<P>&nbsp;</P>";
            forum_print_latest_discussions($forum->id, 0);
            break;
    }


    print_footer($course);

?>
