<?php // $Id$

//  Subscribe to or unsubscribe from a forum.

    require_once("../../config.php");
    require_once("lib.php");

    require_variable($id);      // The forum to subscribe or unsubscribe to
    optional_variable($force);  // Force everyone to be subscribed to this forum?
    optional_variable($user);

    if (! $forum = get_record("forum", "id", $id)) {
        error("Forum ID was incorrect");
    }

    if (! $course = get_record("course", "id", $forum->course)) {
        error("Forum doesn't belong to a course!");
    }

    if ($cm = get_coursemodule_from_instance("forum", $forum->id, $course->id)) {
        if (groupmode($course, $cm) and !isteacheredit($course->id)) {   // Make sure user is allowed
            if (! mygroupid($course->id)) {
                error("Sorry, but you must be a group member to subscribe.");
            }
        }
    } else {
        $cm->id = 0;
    }

    if ($user) {
        if (!isteacher($course->id)) {
            error("Only teachers can subscribe/unsubscribe other people!");
        }
        if (! $user = get_record("user", "id", $user)) {
            error("User ID was incorrect");
        }
    } else {
        $user = $USER;
    }

    require_course_login($course, false, $cm);

    if (isguest()) {   // Guests can't subscribe
        $wwwroot = $CFG->wwwroot.'/login/index.php';
        if (!empty($CFG->loginhttps)) {
            $wwwroot = str_replace('http','https', $wwwroot);
        }

        $strforums = get_string('modulenameplural', 'forum');
        if ($course->category) {
            print_header($course->shortname, $course->fullname,
                 "<a href=\"../../course/view.php?id=$course->id\">$course->shortname</a> ->
                  <a href=\"../forum/index.php?id=$course->id\">$strforums</a> -> 
                  <a href=\"view.php?f=$forum->id\">".format_string($forum->name,true)."</a>", '', '', true, "", navmenu($course, $cm));
        } else {
            print_header($course->shortname, $course->fullname,
                 "<a href=\"../forum/index.php?id=$course->id\">$strforums</a> -> 
                  <a href=\"view.php?f=$forum->id\">".format_string($forum->name,true)."</a>", '', '', true, "", navmenu($course, $cm));
        }
        notice_yesno(get_string('noguestsubscribe', 'forum').'<br /><br />'.get_string('liketologin'),
                     $wwwroot, $_SERVER['HTTP_REFERER']);
        print_footer($course);
        exit;
    }

    if ($forum->type == "teacher") {
        if (!isteacher($course->id)) {
            error("You must be a $course->teacher to subscribe to this forum");
        }
    }

    $returnto = forum_go_back_to("index.php?id=$course->id");

    if ($force and isteacher($course->id)) {
        if (forum_is_forcesubscribed($forum->id)) {
            forum_forcesubscribe($forum->id, 0);
            redirect($returnto, get_string("everyonecanchoose", "forum"), 1);
        } else {
            forum_forcesubscribe($forum->id, 1);
            redirect($returnto, get_string("everyoneissubscribed", "forum"), 1);
        }
    }

    if (forum_is_forcesubscribed($forum->id)) {
        redirect($returnto, get_string("everyoneissubscribed", "forum"), 1);
    }

    $info->name  = fullname($user);
    $info->forum = format_string($forum->name);

    if ( forum_is_subscribed($user->id, $forum->id) ) {
        if (forum_unsubscribe($user->id, $forum->id) ) {
            add_to_log($course->id, "forum", "unsubscribe", "view.php?f=$forum->id", $forum->id, $cm->id);
            redirect($returnto, get_string("nownotsubscribed", "forum", $info), 1);
        } else {
            error("Could not unsubscribe you from that forum", $_SERVER["HTTP_REFERER"]);
        }

    } else { // subscribe
        if (forum_subscribe($user->id, $forum->id) ) {
            add_to_log($course->id, "forum", "subscribe", "view.php?f=$forum->id", $forum->id, $cm->id);
            redirect($returnto, get_string("nowsubscribed", "forum", $info), 1);
        } else {
            error("Could not subscribe you to that forum", $_SERVER["HTTP_REFERER"]);
        }
    }

?>
