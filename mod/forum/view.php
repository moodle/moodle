<?PHP  // $Id$

    require("../../config.php");
    require("lib.php");

    optional_variable($id);      // Course Module ID
    optional_variable($f);       // Forum ID


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
            error("Forum ID was incorrect");
        }
        if (! $course = get_record("course", "id", $forum->course)) {
            error("Forum is misconfigured - don't know what course it's from");
        }
        $buttontext = "";

    } else {
        error("Must specify a course module or a forum ID");
    }

    if ($course->category) {
        require_login($course->id);
        $navigation = "<A HREF=\"../../course/view.php?id=$course->id\">$course->shortname</A> ->
                       <A HREF=\"index.php?id=$course->id\">Forums</A> ->";
    }

    add_to_log($course->id, "forum", "view forum", "view.php?f=$forum->id", "$forum->id");

    print_header("$course->shortname: $forum->name", "$course->fullname",
                 "$navigation $forum->name", "", "", true, $buttontext);

    if ($USER) {
        $SESSION->fromdiscuss = "$FULLME";
        if (is_subscribed($USER->id, $forum->id)) {
            $subtext = "Unsubscribe from this forum";
        } else {
            $subtext = "Subscribe to this forum";
        }
        echo "<DIV ALIGN=RIGHT><FONT SIZE=1><A HREF=\"subscribe.php?id=$forum->id\">$subtext</A></FONT></DIV>";
    }

    print_simple_box(text_to_html($forum->intro), "CENTER");

    switch ($forum->type) {
        case "eachuser":
            echo "<P ALIGN=CENTER>";
            if (user_can_post_discussion($forum)) {
                echo "This forum allows one discussion topic to be posted per person.  Click here to <A HREF=\"../discuss/post.php?forum=$forum->id\">post your topic</A>.";
            } else {
                echo "&nbsp";
            }
            echo "</P>";
            break;

        default:
            echo "<P>&nbsp;</P>";
            break;
    }

    print_forum_latest_topics($forum->id, 0);

    print_footer($course);

?>
