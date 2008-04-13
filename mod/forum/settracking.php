<?php

//  Set tracking option for the forum.

    require_once("../../config.php");
    require_once("lib.php");

    $id         = required_param('id',PARAM_INT);                           // The forum to subscribe or unsubscribe to
    $returnpage = optional_param('returnpage', 'index.php', PARAM_FILE);    // Page to return to.

    if (! $forum = get_record("forum", "id", $id)) {
        error("Forum ID was incorrect");
    }

    if (! $course = get_record("course", "id", $forum->course)) {
        error("Forum doesn't belong to a course!");
    }

    if (! $cm = get_coursemodule_from_instance("forum", $forum->id, $course->id)) {
        error("Incorrect cm");
    }

    require_course_login($course, false, $cm);

    $returnto = forum_go_back_to($returnpage.'?id='.$course->id.'&f='.$forum->id);

    if (!forum_tp_can_track_forums($forum)) {
        redirect($returnto);
    }

    $info = new object();
    $info->name  = fullname($USER);
    $info->forum = format_string($forum->name);
    if (forum_tp_is_tracked($forum) ) {
        if (forum_tp_stop_tracking($forum->id)) {
            add_to_log($course->id, "forum", "stop tracking", "view.php?f=$forum->id", $forum->id, $cm->id);
            redirect($returnto, get_string("nownottracking", "forum", $info), 1);
        } else {
            error("Could not stop tracking that forum", $_SERVER["HTTP_REFERER"]);
        }

    } else { // subscribe
        if (forum_tp_start_tracking($forum->id)) {
            add_to_log($course->id, "forum", "start tracking", "view.php?f=$forum->id", $forum->id, $cm->id);
            redirect($returnto, get_string("nowtracking", "forum", $info), 1);
        } else {
            error("Could not start tracking that forum", $_SERVER["HTTP_REFERER"]);
        }
    }

?>
