<?PHP  // $Id$


include("$CFG->dirroot/mod/discuss/lib.php");

// These are non-special forum types ie the ones that aren't automatically created
$FORUM_TYPE   = array ("general"    => "General Forum",
                       "eachuser"   => "Each $student posts a topic");

function is_subscribed($user, $forum) {
    global $db;

    return record_exists_sql("SELECT * FROM forum_subscriptions WHERE user='$user' AND forum='$forum'");
}

function forum_subscribe($user, $forum) {
    global $db;

    return $db->Execute("INSERT INTO forum_subscriptions SET user = '$user', forum = '$forum'");
}

function forum_unsubscribe($user, $forum) {
    global $db;

    return $db->Execute("DELETE FROM forum_subscriptions WHERE user = '$user' AND forum = '$forum'");
}


function user_has_posted_discussion($forumid, $userid) {
    if ($topics = get_all_topics($forumid, "DESC", $userid)) {
        return true;
    } else {
        return false;
    }
}

function user_can_post_discussion($forum) {
// $forum is an object
    global $USER;

    if ($forum->type == "eachuser") {
        return (! user_has_posted_discussion($forum->id, $USER->id));
    } else if (isteacher($forum->course)) {
        return true;
    } else {
        return $forum->open;
    }
}


function get_all_topics($forum="0", $forum_sort="DESC", $user=0) {
    if ($user) {
        $userselect = " AND u.id = '$user' ";
    } else {
        $userselect = "";
    }
    return get_records_sql("SELECT p.*, u.firstname, u.lastname, u.email, u.picture, u.id as userid
                            FROM discuss d, discuss_posts p, user u 
                            WHERE d.forum = '$forum' AND p.discuss = d.id AND 
                                  p.parent= 0 AND p.user = u.id $userselect
                            ORDER BY p.created $forum_sort");
}


function get_course_news_forum($courseid) {
    if ($forum = get_record_sql("SELECT * from forum WHERE course = '$courseid' AND type = 'news'")) {
        return $forum;
    } else {
        // Doesn't exist, so create one now.
        $forum->course = $courseid;
        $forum->type = "news";
        $forum->name = "News";
        $forum->intro= "General news about this course";
        $forum->open = 0;
        $forum->assessed = 0;
        $forum->timemodified = time();
        $forum->id = insert_record("forum", $forum);
        return get_record_sql("SELECT * from forum WHERE id = '$forum->id'");
    }
}

function get_course_social_forum($courseid) {
    if ($forum = get_record_sql("SELECT * from forum WHERE course = '$courseid' AND type = 'social'")) {
        return $forum;
    } else {
        // Doesn't exist, so create one now.
        $forum->course = $courseid;
        $forum->type = "social";
        $forum->name = "Social";
        $forum->intro= "A forum to socialise and talk about anything you like";
        $forum->open = 1;
        $forum->assessed = 0;
        $forum->timemodified = time();
        $forum->id = insert_record("forum", $forum);
        return get_record_sql("SELECT * from forum WHERE id = '$forum->id'");
    }
}

function get_course_discussion_forum($courseid) {
    if ($forum = get_record_sql("SELECT * from forum WHERE course = '$courseid' AND type = 'discussion'")) {
        return $forum;
    } else {
        // Doesn't exist, so create one now.
        $forum->course = $courseid;
        $forum->type = "discussion";
        $forum->name = "Course Discussion";
        $forum->intro= "Discussions about course content";
        $forum->open = 0;
        $forum->assessed = 1;
        $forum->timemodified = time();
        $forum->id = insert_record("forum", $forum);
        return get_record_sql("SELECT * from forum WHERE id = '$forum->id'");
    }
}


function print_forum_latest_topics($forum_id=0, $forum_numtopics=5, $forum_style="plain", $forum_sort="DESC") {
    global $CFG, $USER;
    
    if ($forum_id) {
        if (! $forum = get_record("forum", "id", $forum_id)) {
            error("Forum ID was incorrect");
        }
        if (! $course = get_record("course", "id", $forum->course)) {
            error("Could not find the course this forum belongs to!");
        }

        if ($course->category) {
            require_login($course->id);
        }

    } else {
        if (! $course = get_record("course", "category", 0)) {
            error("Could not find a top-level course!");
        }
        if (! $forum = get_course_news_forum($course->id)) {
            error("Could not find or create a main forum in this course (id $course->id)");
        }
    }

    if (! $topics = get_all_topics($forum->id, $forum_sort) ) {
        echo "<P ALIGN=CENTER><B>There are no discussion topics yet in this forum.</B></P>";

    } else {

        $replies = count_discussion_replies($forum->id);

        $topiccount = 0;

        foreach ($topics as $topic) {
            $topiccount++;

            if ($forum_numtopics && ($topiccount > $forum_numtopics)) {
                echo "<P ALIGN=right><A HREF=\"$CFG->wwwroot/mod/discuss/index.php?forum=$forum->id\">Older topics</A> ...</P>";
                break;
            }
            if ($replies[$topic->discuss]) {
                $topic->replies = $replies[$topic->discuss]->replies;
            } else {
                $topic->replies = 0;
            }
            $ownpost = ($topic->userid == $USER->id);
            switch ($forum_style) {
                case "minimal":
                    echo "<P><FONT COLOR=#555555>".userdate($topic->modified, "j M H:i")."</FONT>";
                    echo "<BR>$topic->subject ";
                    echo "<A HREF=\"$CFG->wwwroot/mod/discuss/view.php?d=$topic->discuss\">more...</A>";
                    echo "</P>\n";
                break;
                default:
                    print_post($topic, $forum->course, $ownpost, $reply=0, $link=1, $assessed=false);
                    echo "<BR>\n";
                break;
            }
        }
    }
    if (user_can_post_discussion($forum)) {
        echo "<P ALIGN=right>";
        echo "<A HREF=\"$CFG->wwwroot/mod/discuss/post.php?forum=$forum->id\">Add a new topic...</A>";
        echo "</P>";
    }

}


?>
