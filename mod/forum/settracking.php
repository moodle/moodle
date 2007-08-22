<?php

//  Set tracking option for the forum.

    require_once("../../config.php");
    require_once("lib.php");

    $id = required_param('id',PARAM_INT);                          // The forum to subscribe or unsubscribe to
    $returnpage = optional_param('returnpage', 'index.php', PARAM_FILE);    // Page to return to.

    if (! $forum = get_record("forum", "id", $id)) {
        error("Forum ID was incorrect");
    }

    if (! $course = get_record("course", "id", $forum->course)) {
        error("Forum doesn't belong to a course!");
    }

    if (!($cm = get_coursemodule_from_instance("forum", $forum->id, $course->id))) {
        $cm->id = NULL;
    }

    $user = $USER;

    require_course_login($course, false, $cm);

    if (isguest()) {   // Guests can't change tracking
        $wwwroot = $CFG->wwwroot.'/login/index.php';
        if (!empty($CFG->loginhttps)) {
            $wwwroot = str_replace('http:','https:', $wwwroot);
        }

        $strforums = get_string('modulenameplural', 'forum');
        if ($course->id != SITEID) {
            print_header($course->shortname, $course->fullname,
                 "<a href=\"../../course/view.php?id=$course->id\">$course->shortname</a> ->
                  <a href=\"../forum/index.php?id=$course->id\">$strforums</a> -> 
                  <a href=\"view.php?f=$forum->id\">".format_string($forum->name,true)."</a>", '', '', true, "", navmenu($course, $cm));
        } else {
            print_header($course->shortname, $course->fullname,
                 "<a href=\"../forum/index.php?id=$course->id\">$strforums</a> -> 
                  <a href=\"view.php?f=$forum->id\">".format_string($forum->name,true)."</a>", '', '', true, "", navmenu($course, $cm));
        }
        notice_yesno(get_string('noguesttracking', 'forum').'<br /><br />'.get_string('liketologin'),
                     $wwwroot, $_SERVER['HTTP_REFERER']);
        print_footer($course);
        exit;
    }

    $returnto = forum_go_back_to($returnpage.'?id='.$course->id.'&f='.$forum->id);

    $info->name  = fullname($user);
    $info->forum = format_string($forum->name);

    if ( forum_tp_is_tracked($forum->id, $user->id) ) {
        if (forum_tp_stop_tracking($forum->id, $user->id)) {
            add_to_log($course->id, "forum", "stop tracking", "view.php?f=$forum->id", $forum->id, $cm->id);
            redirect($returnto, get_string("nownottracking", "forum", $info), 1);
        } else {
            error("Could not stop tracking that forum", $_SERVER["HTTP_REFERER"]);
        }

    } else { // subscribe
        if (forum_tp_start_tracking($forum->id, $user->id)) {
            add_to_log($course->id, "forum", "start tracking", "view.php?f=$forum->id", $forum->id, $cm->id);
            redirect($returnto, get_string("nowtracking", "forum", $info), 1);
        } else {
            error("Could not start tracking that forum", $_SERVER["HTTP_REFERER"]);
        }
    }

?>
