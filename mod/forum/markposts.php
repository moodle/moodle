<?php // $Id$

      //  Set tracking option for the forum.

    require_once("../../config.php");
    require_once("lib.php");

    $f          = required_param('f',PARAM_INT); // The forum to mark
    $mark       = required_param('mark',PARAM_ALPHA); // Read or unread?
    $d          = optional_param('d',0,PARAM_INT); // Discussion to mark.
    $returnpage = optional_param('returnpage', 'index.php', PARAM_FILE);    // Page to return to.

    if (! $forum = get_record("forum", "id", $f)) {
        error("Forum ID was incorrect");
    }

    if (! $course = get_record("course", "id", $forum->course)) {
        error("Forum doesn't belong to a course!");
    }

    if (!$cm = get_coursemodule_from_instance("forum", $forum->id, $course->id)) {
        error("Incorrect cm!");
    }

    $user = $USER;

    require_course_login($course, false, $cm);

    if ($returnpage == 'index.php') {
        $returnto = forum_go_back_to($returnpage.'?id='.$course->id);
    } else {
        $returnto = forum_go_back_to($returnpage.'?f='.$forum->id);
    }

    if (isguest()) {   // Guests can't change forum
        $wwwroot = $CFG->wwwroot.'/login/index.php';
        if (!empty($CFG->loginhttps)) {
            $wwwroot = str_replace('http:','https:', $wwwroot);
        }

        $navigation = build_navigation('', $cm);
        print_header($course->shortname, $course->fullname, $navigation, '', '', true, "", navmenu($course, $cm));
        notice_yesno(get_string('noguesttracking', 'forum').'<br /><br />'.get_string('liketologin'),
                     $wwwroot, $returnto);
        print_footer($course);
        exit;
    }

    $info = new object();
    $info->name  = fullname($user);
    $info->forum = format_string($forum->name);

    if ($mark == 'read') {
        if (!empty($d)) {
            if (! $discussion = get_record('forum_discussions', 'id', $d, 'forum', $forum->id)) {
                error("Discussion ID was incorrect");
            }

            if (forum_tp_mark_discussion_read($user, $d)) {
                add_to_log($course->id, "discussion", "mark read", "view.php?f=$forum->id", $d, $cm->id);
            }
        } else {
            if (forum_tp_mark_forum_read($user, $forum->id)) {
                add_to_log($course->id, "forum", "mark read", "view.php?f=$forum->id", $forum->id, $cm->id);
            }
        }

/// FUTURE - Add ability to mark them as unread.
//    } else { // subscribe
//        if (forum_tp_start_tracking($forum->id, $user->id)) {
//            add_to_log($course->id, "forum", "mark unread", "view.php?f=$forum->id", $forum->id, $cm->id);
//            redirect($returnto, get_string("nowtracking", "forum", $info), 1);
//        } else {
//            error("Could not start tracking that forum", $_SERVER["HTTP_REFERER"]);
//        }
    }

    redirect($returnto);

?>
