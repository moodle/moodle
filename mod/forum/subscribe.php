<?PHP // $Id$

//  Subscribe to or unsubscribe from a forum.

    require_once("../../config.php");
    require_once("lib.php");

    require_variable($id);      // The forum to subscribe or unsubscribe to
    optional_variable($force);  // Force everyone to be subscribed to this forum?
    optional_variable($user);  // Force everyone to be subscribed to this forum?

    if (isguest()) {
        error("Guests are not allowed to subscribe to posts.", $_SERVER["HTTP_REFERER"]);
    }

    if (! $forum = get_record("forum", "id", $id)) {
        error("Forum ID was incorrect");
    }

    if (! $course = get_record("course", "id", $forum->course)) {
        error("Forum doesn't belong to a course!");
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

    if ($course->category) {
        require_login($forum->course);
    } else {
        require_login();
    }

    if ($forum->type == "teacher") {
        if (!isteacher($course->id)) {
            error("You must be a $course->teacher to subscribe to this forum");
        }
    }

    if ($cm = get_coursemodule_from_instance("forum", $forum->id, $course->id)) {
        if (groupmode($course, $cm) and !isteacheredit($course->id)) {   // Make sure user is allowed
            if (! mygroupid($course->id)) {
                error("Sorry, but you must be a group member to subscribe.");
            }
        }
    } else {
        $cm->id = NULL;
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
    $info->forum = $forum->name;

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
