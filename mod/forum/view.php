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
        $buttontext = update_module_icon($cm->id);

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

    add_to_log($course->id, "forum", "view forum", "index.php?f=$forum->id", "$forum->id");

    print_header("$course->shortname: $forum->name", "$course->fullname",
                 "$navigation $forum->name", "", "", true, $buttontext);

    if ($USER) {
        $SESSION->fromdiscuss = "$FULLME";
        if (is_subscribed($USER->id, $forum->id)) {
            $subtext = "Unsubscribe from this forum";
        } else {
            $subtext = "Subscribe to this forum";
        }
        echo "<DIV ALIGN=RIGHT><FONT SIZE=1><A HREF=\"subscribe.php?id=$forum->id\">$subtext</A></FONT></P>";
    }

    forum_latest_topics($forum->id, 0);

    print_footer($course);

?>
