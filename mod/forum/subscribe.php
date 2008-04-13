<?php // $Id$

//  Subscribe to or unsubscribe from a forum.

    require_once("../../config.php");
    require_once("lib.php");

    $id = required_param('id',PARAM_INT);      // The forum to subscribe or unsubscribe to
    $force = optional_param('force','',PARAM_ALPHA);  // Force everyone to be subscribed to this forum?
    $user = optional_param('user',0,PARAM_INT);

    if (! $forum = get_record("forum", "id", $id)) {
        error("Forum ID was incorrect");
    }

    if (! $course = get_record("course", "id", $forum->course)) {
        error("Forum doesn't belong to a course!");
    }

    if ($cm = get_coursemodule_from_instance("forum", $forum->id, $course->id)) {
        $context = get_context_instance(CONTEXT_MODULE, $cm->id);
    } else {
        $cm->id = 0;
        $context = get_context_instance(CONTEXT_MODULE, $cm->id);
    }

    if ($user) {
        if (!has_capability('mod/forum:managesubscriptions', $context)) {
            error('You do not have the permission to subscribe/unsubscribe other people!');
        }
        if (!$user = get_record("user", "id", $user)) {
            error("User ID was incorrect");
        }
    } else {
        $user = $USER;
    }

    if (groupmode($course, $cm)
                and !forum_is_subscribed($user->id, $forum)
                and !has_capability('moodle/site:accessallgroups', $context)) {
        if (!mygroupid($course->id)) {
            error('Sorry, but you must be a group member to subscribe.');
        }
    }

    require_login($course->id, false, $cm);

    if (isguest()) {   // Guests can't subscribe
        $wwwroot = $CFG->wwwroot.'/login/index.php';
        if (!empty($CFG->loginhttps)) {
            $wwwroot = str_replace('http:','https:', $wwwroot);
        }
        
        $navigation = build_navigation('', $cm);
        print_header($course->shortname, $course->fullname, $navigation, '', '', true, "", navmenu($course, $cm));
        
        notice_yesno(get_string('noguestsubscribe', 'forum').'<br /><br />'.get_string('liketologin'),
                     $wwwroot, $_SERVER['HTTP_REFERER']);
        print_footer($course);
        exit;
    }

    $returnto = optional_param('backtoindex',0,PARAM_INT) 
        ? "index.php?id=".$course->id 
        : "view.php?f=$id";

    if ($force and has_capability('mod/forum:managesubscriptions', $context)) {
        if (forum_is_forcesubscribed($forum)) {
            forum_forcesubscribe($forum->id, 0);
            redirect($returnto, get_string("everyonecannowchoose", "forum"), 1);
        } else {
            forum_forcesubscribe($forum->id, 1);
            redirect($returnto, get_string("everyoneisnowsubscribed", "forum"), 1);
        }
    }

    if (forum_is_forcesubscribed($forum)) {
        redirect($returnto, get_string("everyoneisnowsubscribed", "forum"), 1);
    }

    $info->name  = fullname($user);
    $info->forum = format_string($forum->name);

    if (forum_is_subscribed($user->id, $forum->id)) {
        if (forum_unsubscribe($user->id, $forum->id)) {
            add_to_log($course->id, "forum", "unsubscribe", "view.php?f=$forum->id", $forum->id, $cm->id);
            redirect($returnto, get_string("nownotsubscribed", "forum", $info), 1);
        } else {
            error("Could not unsubscribe you from that forum", $_SERVER["HTTP_REFERER"]);
        }

    } else {  // subscribe
        if ($forum->forcesubscribe == FORUM_DISALLOWSUBSCRIBE &&
                    !has_capability('mod/forum:managesubscriptions', $context)) {
            print_error('disallowsubscribe', 'forum', $_SERVER["HTTP_REFERER"]);
        }
        if (!has_capability('mod/forum:viewdiscussion', $context)) {
            error("Could not subscribe you to that forum", $_SERVER["HTTP_REFERER"]);
        }
        if (forum_subscribe($user->id, $forum->id) ) {
            add_to_log($course->id, "forum", "subscribe", "view.php?f=$forum->id", $forum->id, $cm->id);
            redirect($returnto, get_string("nowsubscribed", "forum", $info), 1);
        } else {
            error("Could not subscribe you to that forum", $_SERVER["HTTP_REFERER"]);
        }
    }

?>
