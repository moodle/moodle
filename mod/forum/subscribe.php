<?PHP // $Id$

//  Subscribe to or unsubscribe from a forum.

    require("../../config.php");
    require("lib.php");

    require_variable($id);  // The forum to subscribe or unsubscribe to

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

    $returnto = go_back_to("index.php?id=$course->id");

    if ( is_subscribed($USER->id, $forum->id) ) {
        if (forum_unsubscribe($USER->id, $forum->id) ) {
            add_to_log($course->id, "forum", "unsubscribe", "index.php?id=$course->id", "$forum->id");
            redirect($returnto, "You are now NOT subscribed to receive '$forum->name' by email.", 1);
        } else {
            error("Could not unsubscribe you from that forum", "$HTTP_REFERER");
        }
        
    } else { // subscribe
        if (forum_subscribe($USER->id, $forum->id) ) {
            add_to_log($course->id, "forum", "subscribe", "index.php?id=$course->id", "$forum->id");
            redirect($returnto, "You are now subscribed to recieve '$forum->name' by email.", 1);
        } else {
            error("Could not subscribe you to that forum", "$HTTP_REFERER");
        }
    }

?>
