<?PHP // $Id$

//  Subscribe to or unsubscribe from a forum.

    require("../../config.php");
    require("lib.php");

    require_variable($id);      // The forum to subscribe or unsubscribe to
    optional_variable($force);  // Force everyone to be subscribed to this forum?

    if (isguest()) {
        error("Guests are not allowed to subscribe to posts.", $HTTP_REFERER);
    }

    if (! $forum = get_record("forum", "id", $id)) {
        error("Forum ID was incorrect");
    }

    if (! $course = get_record("course", "id", $forum->course)) {
        error("Forum doesn't belong to a course!");
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

    $returnto = go_back_to("index.php?id=$course->id");

    if ($force and isteacher($course->id)) {
        if (forum_is_forcesubscribed($forum->id)) {
            forum_forcesubscribe($forum->id, 0);
            redirect($returnto, "Everyone can choose their own subscription to this forum", 1);
        } else {
            forum_forcesubscribe($forum->id, 1);
            redirect($returnto, "Everyone is now subscribed to this forum", 1);
        }
    }

    if (forum_is_forcesubscribed($forum->id)) {
        redirect($returnto, "Everyone is subscribed to this forum", 1);
    }

    if ( forum_is_subscribed($USER->id, $forum->id) ) {
        if (forum_unsubscribe($USER->id, $forum->id) ) {
            add_to_log($course->id, "forum", "unsubscribe", "view.php?f=$forum->id", "$forum->id");
            redirect($returnto, "You are now NOT subscribed to receive '$forum->name' by email.", 1);
        } else {
            error("Could not unsubscribe you from that forum", "$HTTP_REFERER");
        }
        
    } else { // subscribe
        if (forum_subscribe($USER->id, $forum->id) ) {
            add_to_log($course->id, "forum", "subscribe", "view.php?f=$forum->id", "$forum->id");
            redirect($returnto, "You are now subscribed to recieve '$forum->name' by email.", 1);
        } else {
            error("Could not subscribe you to that forum", "$HTTP_REFERER");
        }
    }

?>
