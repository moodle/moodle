<?PHP  // $Id$

/// CONSTANTS ///////////////////////////////////////////////////////////

$FORUM_DEFAULT_DISPLAY_MODE = 3; 

$FORUM_DISCUSS_MODES = array ( "1"  => "Display replies flat, with oldest first",
                               "-1" => "Display replies flat, wth newest first",
                               "2"  => "Display replies in threaded form",
                               "3"  => "Display replies in nested form");

// These are course content forums that can be added to the course manually
$FORUM_TYPE   = array ("general"    => "General forum",
                       "eachuser"   => "Each $student posts one discussion",
                       "single"     => "A single simple discussion");

$FORUM_POST_RATINGS = array ("3" => "Outstanding", 
                             "2" => "Satisfactory", 
                             "1" => "Not satisfactory");

$FORUM_LONG_POST = 600;


/// FUNCTIONS ///////////////////////////////////////////////////////////


function forum_get_course_forum($courseid, $type) {
// How to set up special 1-per-course forums
    if ($forum = get_record_sql("SELECT * from forum WHERE course = '$courseid' AND type = '$type'")) {
        return $forum;
    } else {
        // Doesn't exist, so create one now.
        $forum->course = $courseid;
        $forum->type = "$type";
        switch ($forum->type) {
            case "news":
                $forum->name = "News";
                $forum->intro= "General news about this course";
                $forum->open = 0;
                $forum->assessed = 0;
                $forum->forcesubscribe = 1;
                break;
            case "social":
                $forum->name = "Social";
                $forum->intro= "A forum for general socialising. Talk about anything you like!";
                $forum->open = 1;
                $forum->assessed = 0;
                $forum->forcesubscribe = 0;
                break;
            case "teacher":
                $forum->name = "Teacher Forum";
                $forum->intro= "For teacher-only notes and discussion";
                $forum->open = 0;
                $forum->assessed = 0;
                $forum->forcesubscribe = 0;
                break;
            default:
                notify("That forum type doesn't exist!");
                return false;
                break;

        }
        $forum->timemodified = time();
        $forum->id = insert_record("forum", $forum);
        return get_record_sql("SELECT * from forum WHERE id = '$forum->id'");
    }
}


function forum_make_mail_post(&$post, $user, $touser, $course, 
                              $ownpost=false, $reply=false, $link=false, $rate=false, $footer="") {
// Given the data about a posting, builds up the HTML to display it and 
// returns the HTML in a string.  This is designed for sending via HTML email.

    global $THEME, $CFG;

    $output = "";

    if ($post->parent) {
        $output .= "<TABLE BORDER=0 CELLPADDING=1 CELLSPACING=1><TR><TD BGCOLOR=#888888>";
        $output .= "<TABLE BORDER=0 CELLPADDING=3 CELLSPACING=0>";
    } else {
        $output .= "<TABLE BORDER=0 CELLPADDING=1 CELLSPACING=1 WIDTH=100%><TR><TD BGCOLOR=#888888>";
        $output .= "<TABLE BORDER=0 CELLPADDING=3 CELLSPACING=0 WIDTH=100%>";
    }

    $output .= "<TR><TD BGCOLOR=\"$THEME->body\" WIDTH=35 VALIGN=TOP>";
    $output .= print_user_picture($user->id, $course->id, $user->picture, false, true);
    $output .= "</TD>";

    if ($post->parent) {
        $output .= "<TD NOWRAP BGCOLOR=\"$THEME->cellheading\">";
    } else {
        $output .= "<TD NOWRAP BGCOLOR=\"$THEME->cellheading2\">";
    }
    $output .= "<P>";
    $output .= "<FONT SIZE=3><B>$post->subject</B></FONT><BR>";
    $output .= "<FONT SIZE=2>by <A HREF=\"$CFG->wwwroot/user/view.php?id=$user->id&course=$course->id\">$user->firstname $user->lastname</A>";
    $output .= " on ".userdate($post->created, "", $touser->timezone);
    $output .= "</FONT></P></TD></TR>";
    $output .= "<TR><TD BGCOLOR=\"$THEME->body\" WIDTH=10>";
    $output .= "&nbsp;";
    $output .= "</TD><TD BGCOLOR=\"#FFFFFF\">\n";

    $output .= text_to_html($post->message);

    $output .= "<P ALIGN=right><FONT SIZE=-1>";

    $age = time() - $post->created;
    if ($ownpost) {
        $output .= "<A HREF=\"$CFG->wwwroot/mod/forum/post.php?delete=$post->id\">Delete</A>";
        if ($reply) {
            $output .= "| <A HREF=\"$CFG->wwwroot/mod/forum/post.php?reply=$post->id\">Reply</A>";
        }
        $output .= "&nbsp;&nbsp;";
    } else {
        if ($reply) {
            $output .= "<A HREF=\"$CFG->wwwroot/mod/forum/post.php?reply=$post->id\">Reply</A>&nbsp;&nbsp;";
        }
    }

    $output .= "<DIV ALIGN=right><P ALIGN=right>";
    
    if ($link) {
        if ($post->replies == 1) {
            $replystring = "reply";
        } else {
            $replystring = "replies";
        }
        $output .= "<A HREF=\"$CFG->wwwroot/mod/forum/discuss.php?d=$post->discussion\"><B>Discuss this topic</B></A> ($post->replies $replystring so far)&nbsp;&nbsp;";
    }
    $output .= "</P></DIV>";
    if ($footer) {
        $output .= "<P>$footer</P>";
    }
    $output .= "</TD></TR></TABLE>\n";
    $output .= "</TD></TR></TABLE>\n\n";

    return $output;
}


function forum_print_post(&$post, $courseid, $ownpost=false, $reply=false, $link=false, $rate=false, $footer="") {
    global $THEME, $USER, $CFG, $FORUM_LONG_POST;

    if ($post->parent) {
        echo "<TABLE BORDER=0 CELLPADDING=1 CELLSPACING=1><TR><TD BGCOLOR=#888888>";
        echo "<TABLE BORDER=0 CELLPADDING=3 CELLSPACING=0>";
    } else {
        echo "<TABLE BORDER=0 CELLPADDING=1 CELLSPACING=1 WIDTH=100%><TR><TD BGCOLOR=#888888>";
        echo "<TABLE BORDER=0 CELLPADDING=3 CELLSPACING=0 WIDTH=100%>";
    }

    echo "<TR><TD BGCOLOR=\"$THEME->body\" WIDTH=35 VALIGN=TOP>";
    print_user_picture($post->userid, $courseid, $post->picture);
    echo "</TD>";

    if ($post->parent) {
        echo "<TD NOWRAP BGCOLOR=\"$THEME->cellheading\">";
    } else {
        echo "<TD NOWRAP BGCOLOR=\"$THEME->cellheading2\">";
    }
    echo "<P>";
    echo "<FONT SIZE=3><B>$post->subject</B></FONT><BR>";
    echo "<FONT SIZE=2>by <A HREF=\"$CFG->wwwroot/user/view.php?id=$post->userid&course=$courseid\">$post->firstname $post->lastname</A>";
    echo " on ".userdate($post->created);
    echo "</FONT></P></TD></TR>";
    echo "<TR><TD BGCOLOR=\"$THEME->body\" WIDTH=10>";
    echo "&nbsp;";
    echo "</TD><TD BGCOLOR=\"#FFFFFF\">\n";

    if ($link && (strlen($post->message) > $FORUM_LONG_POST)) {
        // Print shortened version
        echo text_to_html(forum_shorten_post($post->message));
        $numwords = count_words($post->message);
        echo "<A HREF=\"$CFG->wwwroot/mod/forum/discuss.php?d=$post->discussion\">";
        echo "Read the rest of this topic</A> ($numwords words)...";
    } else {
        // Print whole message
        echo text_to_html($post->message);
    }

    echo "<P ALIGN=right><FONT SIZE=-1>";

    $age = time() - $post->created;
    if ($ownpost) {
        if ($age < $CFG->maxeditingtime) {
            echo "<A HREF=\"$CFG->wwwroot/mod/forum/post.php?edit=$post->id\">Edit</A> | ";
        }
        echo "<A HREF=\"$CFG->wwwroot/mod/forum/post.php?delete=$post->id\">Delete</A>";
        if ($reply) {
            echo "| <A HREF=\"$CFG->wwwroot/mod/forum/post.php?reply=$post->id\">Reply</A>";
        }
        echo "&nbsp;&nbsp;";
    } else {
        if ($reply) {
            echo "<A HREF=\"$CFG->wwwroot/mod/forum/post.php?reply=$post->id\">Reply</A>&nbsp;&nbsp;";
        }
    }


    echo "<DIV ALIGN=right><P ALIGN=right>";
    if ($rate && $USER->id) {
        if ($USER->id == $post->userid) {
            print_forum_ratings($post->id);
        } else {
            print_forum_rating($post->id, $USER->id);
        }
    }
    
    if ($link) {
        if ($post->replies == 1) {
            $replystring = "reply";
        } else {
            $replystring = "replies";
        }
        echo "<A HREF=\"$CFG->wwwroot/mod/forum/discuss.php?d=$post->discussion\"><B>Discuss this topic</B></A> ($post->replies $replystring so far)&nbsp;&nbsp;";
    }
    echo "</P>";
    if ($footer) {
        echo "<P>$footer</P>";
    }
    echo "</DIV>";
    echo "</TD></TR></TABLE>";
    echo "</TD></TR>\n</TABLE>\n\n";
}

function forum_shorten_post($message) {
    global $FORUM_LONG_POST;

    if (strlen($message) > $FORUM_LONG_POST) {
        // Look for the first return between 50 and $FORUM_LONG_POST
        $shortmessage = substr($message, 50, $FORUM_LONG_POST);
        if ($pos = strpos($shortmessage, "\n")) {
            return substr($message, 0, 50 + $pos). " ...";
        } else {
            return substr($message, 0, $FORUM_LONG_POST). "...";
        }
    } else {
        return $message;
    }
}


function print_forum_ratings($post) {

    global $CFG, $PHPSESSID;

    $notsatisfactory = 0;
    $satisfactory = 0;
    $outstanding = 0;
    if ($ratings = get_records_sql("SELECT * from forum_ratings WHERE post='$post'")) {
        foreach ($ratings as $rating) {
            switch ($rating->rating) {
                case 1: $notsatisfactory++; break;
                case 2: $satisfactory++; break;
                case 3: $outstanding++; break;
            }
        }
        $summary = "$outstanding/$satisfactory/$notsatisfactory";

        echo "Ratings: ";
        link_to_popup_window ("/mod/forum/report.php?id=$post", "ratings", $summary, 400, 550);

    } else {
        echo "";
    }
}

function print_forum_rating($post, $user) {
    global $FORUM_POST_RATINGS;

    if ($rs = get_record_sql("SELECT rating from forum_ratings WHERE user='$user' AND post='$post'")) {
        echo "<FONT SIZE=-1>You rated this: <FONT COLOR=green>";
        if ($FORUM_POST_RATINGS[$rs->rating]) {
            echo $FORUM_POST_RATINGS[$rs->rating];
        } else {
            echo "Error";
        }
        echo "</FONT></FONT>";

    } else {
        choose_from_menu($FORUM_POST_RATINGS, $post, "", "Rate...");
    }
}

function print_forum_mode_form($discussion, $mode) {
    GLOBAL $FORUM_DISCUSS_MODES;

    echo "<CENTER><P>";
    popup_form("discuss.php?d=$discussion&mode=", $FORUM_DISCUSS_MODES, "mode", $mode, "");
    echo "</P></CENTER>\n";
}

function print_forum_search_form($course, $search="") {
    global $CFG;

    echo "<TABLE BORDER=0 CELLPADDING=10 CELLSPACING=0><TR><TD ALIGN=CENTER>";
    echo "<FORM NAME=search ACTION=\"$CFG->wwwroot/mod/forum/search.php\">";
    echo "<INPUT NAME=search TYPE=text SIZE=15 VALUE=\"$search\"><BR>";
    echo "<INPUT VALUE=\"".get_string("search", "forum")."\" TYPE=submit>";
    echo "<INPUT NAME=id TYPE=hidden VALUE=\"$course->id\">";
    echo "</FORM>";
    echo "</TD></TR></TABLE>";
}


function forum_count_discussion_replies($forum="0") {
    if ($forum) {
        $forumselect = " AND d.forum = '$forum'";
    }
    return get_records_sql("SELECT p.discussion, (count(*)) as replies
                            FROM forum_posts p, forum_discussions d
                            WHERE p.parent > 0 AND p.discussion = d.id 
                            GROUP BY p.discussion");
}


function forum_set_return() {
    global $SESSION, $HTTP_REFERER;

    if (! $SESSION->fromdiscussion) {
        $SESSION->fromdiscussion = $HTTP_REFERER;
    }
}


function forum_go_back_to($default) {
    global $SESSION;

    if ($SESSION->fromdiscussion) {
        $returnto = $SESSION->fromdiscussion;
        unset($SESSION->fromdiscussion);
        return $returnto;
    } else {
        return $default;
    }
}

function forum_get_post_full($postid) {
    return get_record_sql("SELECT p.*, u.firstname, u.lastname, 
                                  u.email, u.picture, u.id as userid
                           FROM forum_posts p, user u 
                           WHERE p.id = '$postid' AND p.user = u.id");
}


function forum_add_new_post($post) {

    $timenow = time();
    $post->created = $timenow;
    $post->modified = $timenow;
    $post->mailed = "0";

    return insert_record("forum_posts", $post);
}

function forum_update_post($post) {
    global $db;

    $timenow = time();

    $rs = $db->Execute("UPDATE forum_posts 
                        SET message='$post->message', subject='$post->subject', modified='$timenow' 
                        WHERE id = '$post->id'");
    return $rs;
}

function forum_add_discussion($discussion) {
// Given an object containing all the necessary data, 
// create a new discussion and return the id

    GLOBAL $USER;

    $timenow = time();

    // The first post is stored as a real post, and linked 
    // to from the discuss entry.

    $post->discussion  = 0;
    $post->parent      = 0;
    $post->user        = $USER->id;
    $post->created     = $timenow;
    $post->modified    = $timenow;
    $post->mailed      = 0;
    $post->subject     = $discussion->name;
    $post->message     = $discussion->intro;

    if (! $post->id = insert_record("forum_posts", $post) ) {
        return 0;
    }

    // Now do the real module entry

    $discussion->firstpost    = $post->id;
    $discussion->timemodified = $timenow;

    if (! $discussion->id = insert_record("forum_discussions", $discussion) ) {
        return 0;
    }

    // Finally, set the pointer on the post.
    if (! set_field("forum_posts", "discussion", $discussion->id, "id", $post->id)) {
        return 0;
    }

    return $discussion->id;
}


function forum_delete_discussion($discussion) {
// $discussion is a discussion record object

    $result = true;

    if ($posts = get_records("forum_posts", "discussion", $discussion->id)) {
        foreach ($posts as $post) {
            if (! delete_records("forum_ratings", "post", "$post->id")) {
                $result = false;
            }
        }
    }

    if (! delete_records("forum_posts", "discussion", "$discussion->id")) {
        $result = false;
    }

    if (! delete_records("forum_discussions", "id", "$discussion->id")) {
        $result = false;
    }

    return $result;
}



function forum_print_user_discussions($courseid, $userid) {
    global $USER;

    $discussions = get_records_sql("SELECT p.*, u.firstname, u.lastname, u.email, u.picture, 
                                           u.id as userid, f.type as forumtype
                                    FROM forum_discussions d, forum_posts p, user u, forum f
                                    WHERE d.course = '$courseid' AND p.discussion = d.id AND 
                                          p.parent = 0 AND p.user = u.id AND u.id = '$userid' AND
                                          d.forum = f.id
                                    ORDER BY p.created ASC");
    
    if ($discussions) {
        $user = get_record("user", "id", $userid);
        echo "<HR>";
        print_heading("Discussions started by $user->firstname $user->lastname");
        $replies = forum_count_discussion_replies();
        foreach ($discussions as $discussion) {
            if (($discussion->forumtype == "teacher") and !isteacher($courseid)) {
                continue;
            }
            if ($replies[$discussion->discussion]) {
                $discussion->replies = $replies[$discussion->discussion]->replies;
            } else {
                $discussion->replies = 0;
            }
            $ownpost = ($discussion->userid == $USER->id);
            forum_print_post($discussion, $course->id, $ownpost, $reply=0, $link=1, $assessed=false);
            echo "<BR>\n";
        }
    }
}


function forum_user_summary($course, $user, $mod, $forum) {
    global $CFG;
}


function forum_user_outline($course, $user, $mod, $forum) {

    global $CFG;
    if ($posts = get_records_sql("SELECT p.*, u.id as userid, u.firstname, u.lastname, u.email, u.picture
                                  FROM forum f, forum_discussions d, forum_posts p, user u 
                                  WHERE f.id = '$forum->id' AND d.forum = f.id AND p.discussion = d.id
                                  AND p.user = '$user->id' AND p.user = u.id
                                  ORDER BY p.modified ASC")) {

        $result->info = count($posts)." posts";

        $lastpost = array_pop($posts);
        $result->time = $lastpost->modified;
        return $result;
    }
    return NULL;
}


function forum_user_complete($course, $user, $mod, $forum) {
    global $CFG;

    if ($posts = get_records_sql("SELECT p.*, u.id as userid, u.firstname, u.lastname, u.email, u.picture
                                  FROM forum f, forum_discussions d, forum_posts p, user u 
                                  WHERE f.id = '$forum->id' AND d.forum = f.id AND p.discussion = d.id
                                  AND p.user = '$user->id' AND p.user = u.id
                                  ORDER BY p.modified ASC")) {

        foreach ($posts as $post) {
            if ($post->parent) {
                $footer = "<A HREF=\"$CFG->wwwroot/mod/forum/discuss.php?d=$post->discussion&parent=$post->parent\">Parent of this post</A>";
            } else {
                $footer = "";
            }

            pirint_post($post, $course->id, $ownpost=false, $reply=false, $link=false, $rate=false, $footer);
        }

    } else {
        echo "<P>No posts</P>";
    }

}

function forum_cron () {
// Function to be run periodically according to the moodle cron
// Finds all posts that have yet to be mailed out, and mails them

    global $CFG;

    echo "Processing posts...\n";

    $cutofftime = time() - $CFG->maxeditingtime;

    if ($posts = get_records_sql("SELECT p.*, d.course FROM forum_posts p, forum_discussions d
                                  WHERE p.mailed = '0' AND p.created < '$cutofftime' AND p.discussion = d.id")) {

        $timenow = time();

        foreach ($posts as $post) {

            echo "Processing post $post->id\n";

            if (! $userfrom = get_record("user", "id", "$post->user")) {
                echo "Could not find user $post->user\n";
                continue;
            }

            if (! $course = get_record("course", "id", "$post->course")) {
                echo "Could not find course $post->course\n";
                continue;
            }

            if (! $discussion = get_record("forum_discussions", "id", "$post->discussion")) {
                echo "Could not find discussion $post->discussion\n";
                continue;
            }

            if (! $forum = get_record("forum", "id", "$discussion->forum")) {
                echo "Could not find forum $discussion->forum\n";
                continue;
            }


            if ($users = get_records_sql("SELECT u.* FROM user u, forum_subscriptions s
                                          WHERE s.user = u.id AND s.forum = '$discussion->forum'")) {

                foreach ($users as $userto) {
                    $postsubject = "$course->shortname: $post->subject";
                    $posttext  = "$course->shortname -> Forums -> $forum->name -> $discussion->name\n";
                    $posttext .= "---------------------------------------------------------------------\n";
                    $posttext .= "$post->subject\n";
                    $posttext .= "by $userfrom->firstname $userfrom->lastname, on ".userdate($post->created, "", $userto->timezone)."\n";
                    $posttext .= "---------------------------------------------------------------------\n";
                    $posttext .= strip_tags($post->message);
                    $posttext .= "\n\n";
                    $posttext .= "---------------------------------------------------------------------\n";
                    $posttext .= "This is a copy of a message posted on the $course->shortname website.\n";
                    $posttext .= "To add your reply via the website, click on this link:\n";
                    $posttext .= "$CFG->wwwroot/mod/forum/post.php?reply=$post->id";

                    if ($userto->mailformat == 1) {  // HTML
                        $posthtml = "<P><FONT FACE=sans-serif>".
                      "<A HREF=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</A> ->".
                      "<A HREF=\"$CFG->wwwroot/mod/forum/index.php?id=$course->id\">Forums</A> ->".
                      "<A HREF=\"$CFG->wwwroot/mod/forum/view.php?f=$forum->id\">$forum->name</A> ->".
                      "<A HREF=\"$CFG->wwwroot/mod/forum/discuss.php?d=$discussion->id\">$discussion->name</A></FONT></P>";
                      $posthtml .= forum_make_mail_post($post, $userfrom, $userto, $course, false, true, false, false);
                    } else {
                      $posthtml = "";
                    }

                    if (! email_to_user($userto, $userfrom, $postsubject, $posttext, $posthtml)) {
                        echo "Error: mod/forum/cron.php: Could not send out mail for id $post->id to user $userto->id ($userto->email)\n";
                    }
                }
            }

            if (! set_field("forum_posts", "mailed", "1", "id", "$post->id")) {
                echo "Could not update the mailed field for id $post->id\n";
            }
        }
    }

    return true;
}


function forum_forcesubscribe($forumid, $value=1) {
    return set_field("forum", "forcesubscribe", $value, "id", $forumid);
}

function forum_is_forcesubscribed($forumid) {
    return get_field("forum", "forcesubscribe", "id", $forumid);
}

function forum_is_subscribed($userid, $forumid) {
    if (forum_is_forcesubscribed($forumid)) {
        return true;
    }
    return record_exists_sql("SELECT * FROM forum_subscriptions WHERE user='$userid' AND forum='$forumid'");
}

function forum_subscribe($userid, $forumid) {
    global $db;

    return $db->Execute("INSERT INTO forum_subscriptions SET user = '$userid', forum = '$forumid'");
}

function forum_unsubscribe($userid, $forumid) {
    global $db;

    return $db->Execute("DELETE FROM forum_subscriptions WHERE user = '$userid' AND forum = '$forumid'");
}


function forum_user_has_posted_discussion($forumid, $userid) {
    if ($discussions = forum_get_discussions($forumid, "DESC", $userid)) {
        return true;
    } else {
        return false;
    }
}

function forum_user_can_post_discussion($forum) {
// $forum is an object
    global $USER;

    if ($forum->type == "eachuser") {
        return (! forum_user_has_posted_discussion($forum->id, $USER->id));
    } else if ($forum->type == "teacher") {
        return isteacher($forum->course);
    } else if (isteacher($forum->course)) {
        return true;
    } else {
        return $forum->open;
    }
}


function forum_get_discussions($forum="0", $forum_sort="DESC", $user=0) {
    if ($user) {
        $userselect = " AND u.id = '$user' ";
    } else {
        $userselect = "";
    }
    return get_records_sql("SELECT p.*, u.firstname, u.lastname, u.email, u.picture, u.id as userid
                            FROM forum_discussions d, forum_posts p, user u 
                            WHERE d.forum = '$forum' AND p.discussion = d.id AND 
                                  p.parent= 0 AND p.user = u.id $userselect
                            ORDER BY p.created $forum_sort");
}



function forum_print_latest_discussions($forum_id=0, $forum_numdiscussions=5, $forum_style="plain", $forum_sort="DESC") {
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
        if (! $forum = forum_get_course_news_forum($course->id)) {
            error("Could not find or create a main forum in this course (id $course->id)");
        }
    }

    if (forum_user_can_post_discussion($forum)) {
        echo "<P ALIGN=right>";
        echo "<A HREF=\"$CFG->wwwroot/mod/forum/post.php?forum=$forum->id\">Add a new discussion topic...</A>";
        echo "</P>\n";
    }

    if (! $discussions = forum_get_discussions($forum->id, $forum_sort) ) {
        echo "<P ALIGN=CENTER><B>There are no discussion topics yet in this forum.</B></P>";

    } else {

        $replies = forum_count_discussion_replies($forum->id);

        $discussioncount = 0;

        foreach ($discussions as $discussion) {
            $discussioncount++;

            if ($forum_numdiscussions && ($discussioncount > $forum_numdiscussions)) {
                echo "<P ALIGN=right><A HREF=\"$CFG->wwwroot/mod/forum/view.php?f=$forum->id\">Older discussions</A> ...</P>";
                break;
            }
            if ($replies[$discussion->discussion]) {
                $discussion->replies = $replies[$discussion->discussion]->replies;
            } else {
                $discussion->replies = 0;
            }
            $ownpost = ($discussion->userid == $USER->id);
            switch ($forum_style) {
                case "minimal":
                    echo "<P><FONT COLOR=#555555>".userdate($discussion->modified, "%e %b, %H:%M")." - $discussion->firstname</FONT>";
                    echo "<BR>$discussion->subject ";
                    echo "<A HREF=\"$CFG->wwwroot/mod/forum/discuss.php?d=$discussion->discussion\">more...</A>";
                    echo "</P>\n";
                break;
                default:
                    forum_print_post($discussion, $forum->course, $ownpost, $reply=0, $link=1, $assessed=false);
                    echo "<BR>\n";
                break;
            }
        }
    }
}

function forum_print_discussion($course, $discussion, $post, $mode) {

    global $USER;

    $ownpost = ($USER->id == $post->user);

    forum_print_post($post, $course->id, $ownpost, $reply=true, $link=false, $rate=false);

    print_forum_mode_form($discussion->id, $mode);

    if ($discussion->assessed && $USER->id) {
        echo "<FORM NAME=form METHOD=POST ACTION=rate.php>";
        echo "<INPUT TYPE=hidden NAME=id VALUE=\"$course->id\">";
    }

    switch ($mode) {
        case 1 :   // Flat ascending
        case -1 :  // Flat descending
        default:   
            echo "<UL>";
            forum_print_posts_flat($post->discussion, $course->id, $mode, $discussion->assessed);
            echo "</UL>";
            break;

        case 2 :   // Threaded 
            forum_print_posts_threaded($post->id, $course->id, 0, $discussion->assessed);
            break;

        case 3 :   // Nested
            forum_print_posts_nested($post->id, $course->id, $discussion->assessed);
            break;
    }

    if ($discussion->assessed && $USER->id) {
        echo "<CENTER><P ALIGN=center><INPUT TYPE=submit VALUE=\"Send in my latest ratings\"></P></CENTER>";
        echo "</FORM>";
    }
}

function forum_print_posts_flat($discussion, $course, $direction, $assessed) { 
    global $USER;

    $reply = true;
    $link  = false;

    if ($direction < 0) {
        $sort = "ORDER BY created DESC";
    } else {
        $sort = "ORDER BY created ASC";
    }

    if ($posts = get_records_sql("SELECT p.*, u.id as userid, u.firstname, u.lastname, u.email, u.picture
                                  FROM forum_posts p, user u
                                  WHERE p.discussion = $discussion AND p.parent > 0 AND p.user = u.id $sort")) {

        foreach ($posts as $post) {
            $ownpost = ($USER->id == $post->user);
            forum_print_post($post, $course, $ownpost, $reply, $link, $assessed);
        }
    } else {
        return;
    }
}

function forum_print_posts_threaded($parent, $course, $depth, $assessed) { 
    global $USER;

    $reply = true;
    $link  = false;

    if ($posts = get_records_sql("SELECT p.*, u.id as userid, u.firstname, u.lastname, u.email, u.picture
                                  FROM forum_posts p, user u
                                  WHERE p.parent = '$parent' AND p.user = u.id")) {

        foreach ($posts as $post) {

            echo "<UL>";
            if ($depth > 0) {
                $ownpost = ($USER->id == $post->user);
                forum_print_post($post, $course, $ownpost, $reply, $link, $assessed);  // link=true?
                echo "<BR>";
            } else {
                echo "<LI><P><B><A HREF=\"discuss.php?d=$post->discussion&parent=$post->id\">$post->subject</A></B> by $post->firstname $post->lastname, ".userdate($post->created)."</P>";
            }

            forum_print_posts_threaded($post->id, $course, $depth-1, $assessed);
            echo "</UL>\n";
        }
    } else {
        return;
    }
}

function forum_print_posts_nested($parent, $course, $assessed) { 
    global $USER;

    $reply = true;
    $link  = false;

    if ($posts = get_records_sql("SELECT p.*, u.id as userid, u.firstname, u.lastname, u.email, u.picture
                                  FROM forum_posts p, user u
                                  WHERE p.parent = $parent AND p.user = u.id
                                  ORDER BY p.created ASC ")) {

        foreach ($posts as $post) {

            $ownpost = ($USER->id == $post->user);

            echo "<UL>";
            forum_print_post($post, $course, $ownpost, $reply, $link, $assessed);
            echo "<BR>";
            forum_print_posts_nested($post->id, $course, $assessed);
            echo "</UL>\n";
        }
    } else {
        return;
    }
}

function forum_set_display_mode($mode=0) {
    global $USER;

    if ($mode) {
        $USER->mode = $mode;
    } else if (!$USER->mode) {
        $USER->mode = $FORUM_DEFAULT_DISPLAY_MODE;
    }
}

?>
