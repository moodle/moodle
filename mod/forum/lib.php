<?PHP  // $Id$

require_once("$CFG->dirroot/files/mimetypes.php");

/// CONSTANTS ///////////////////////////////////////////////////////////

$FORUM_DEFAULT_DISPLAY_MODE = 3; 

$FORUM_LAYOUT_MODES = array ( "1"  => get_string("modeflatoldestfirst", "forum"),
                              "-1" => get_string("modeflatnewestfirst", "forum"),
                              "2"  => get_string("modethreaded", "forum"),
                              "3"  => get_string("modenested", "forum") );

// These are course content forums that can be added to the course manually
$FORUM_TYPES   = array ("general"    => get_string("generalforum", "forum"),
                        "eachuser"   => get_string("eachuserforum", "forum"),
                        "single"     => get_string("singleforum", "forum") );

$FORUM_POST_RATINGS = array ("3" => get_string("postrating3", "forum"),
                             "2" => get_string("postrating2", "forum"),
                             "1" => get_string("postrating1", "forum") );

$FORUM_OPEN_MODES   = array ("2" => get_string("openmode2", "forum"),
                             "1" => get_string("openmode1", "forum"),
                             "0" => get_string("openmode0", "forum") );


define("FORUM_SHORT_POST", 300);  // Less non-HTML characters than this is short

define("FORUM_LONG_POST", 600);   // More non-HTML characters than this is long

define("FORUM_MANY_DISCUSSIONS", 10);


/// STANDARD FUNCTIONS ///////////////////////////////////////////////////////////

function forum_add_instance($forum) {
// Given an object containing all the necessary data, 
// (defined by the form in mod.html) this function 
// will create a new instance and return the id number 
// of the new instance.

    global $CFG;

    $forum->timemodified = time();

    if (! $forum->id = insert_record("forum", $forum)) {
        return false;
    }

    if ($forum->type == "single") {  // Create related discussion.

        $discussion->course   = $forum->course;
        $discussion->forum    = $forum->id;
        $discussion->name     = $forum->name;
        $discussion->intro    = $forum->intro;
        $discussion->assessed = $forum->assessed;

        if (! forum_add_discussion($discussion)) {
            error("Could not add the discussion for this forum");
        }
    }
    add_to_log($forum->course, "forum", "add", "index.php?f=$forum->id", "$forum->id");

    return $forum->id;
}


function forum_update_instance($forum) {
// Given an object containing all the necessary data, 
// (defined by the form in mod.html) this function 
// will update an existing instance with new data.

    $forum->timemodified = time();
    $forum->id = $forum->instance;

    if ($forum->type == "single") {  // Update related discussion and post.
        if (! $discussion = get_record("forum_discussions", "forum", $forum->id)) {
            if ($discussions = get_records("forum_discussions", "forum", $forum->id, "timemodified ASC")) {
                notify("Warning! There is more than one discussion in this forum - using the most recent");
                $discussion = array_pop($discussions);
            } else {
                error("Could not find the discussion in this forum");
            }
        }
        if (! $post = get_record("forum_posts", "id", $discussion->firstpost)) {
            error("Could not find the first post in this forum discussion");
        }

        $post->subject  = $forum->name;
        $post->message  = $forum->intro;
        $post->modified = $forum->timemodified;

        if (! update_record("forum_posts", $post)) {
            error("Could not update the first post");
        }

        $discussion->name = $forum->name;

        if (! update_record("forum_discussions", $discussion)) {
            error("Could not update the discussion");
        }
    }

    if (update_record("forum", $forum)) {
        add_to_log($forum->course, "forum", "update", "index.php?f=$forum->id", "$forum->id");
        return true;
    } else {
        return false;
    }
}


function forum_delete_instance($id) {
// Given an ID of an instance of this module, 
// this function will permanently delete the instance 
// and any data that depends on it.  

    if (! $forum = get_record("forum", "id", "$id")) {
        return false;
    }

    $result = true;

    if ($discussions = get_records("forum_discussions", "forum", $forum->id)) {
        foreach ($discussions as $discussion) {
            if (! forum_delete_discussion($discussion)) {
                $result = false;
            }
        }
    }

    if (! delete_records("forum_subscriptions", "forum", "$forum->id")) {
        $result = false;
    }

    if (! delete_records("forum", "id", "$forum->id")) {
        $result = false;
    }

    return $result;
}


function forum_cron () {
// Function to be run periodically according to the moodle cron
// Finds all posts that have yet to be mailed out, and mails them

    global $CFG, $USER;

    $cutofftime = time() - $CFG->maxeditingtime;

    if ($posts = forum_get_unmailed_posts($cutofftime)) {

        $timenow = time();

        foreach ($posts as $post) {

            print_string("processingpost", "forum", $post->id);
            echo " ... ";

            if (! $userfrom = get_record("user", "id", "$post->userid")) {
                echo "Could not find user $post->userid\n";
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

            if (! $course = get_record("course", "id", "$forum->course")) {
                echo "Could not find course $forum->course\n";
                continue;
            }

            if ($users = forum_subscribed_users($course, $forum)) {
                $canunsubscribe = ! forum_is_forcesubscribed($forum->id);

                $mailcount=0;
                foreach ($users as $userto) {
                    $USER->lang = $userto->lang;  // Affects the language of get_string
                    $canreply = forum_user_can_post($forum, $userto);


                    $by->name = "$userfrom->firstname $userfrom->lastname";
                    $by->date = userdate($post->created, "", $userto->timezone);
                    $strbynameondate = get_string("bynameondate", "forum", $by);

                    $strforums = get_string("forums", "forum");

                    $postsubject = "$course->shortname: $post->subject";
                    $posttext  = "$course->shortname -> $strforums -> $forum->name";

                    if ($discussion->name == $forum->name) {
                        $posttext  .= "\n";
                    } else {
                        $posttext  .= " -> $discussion->name\n";
                    }
                    $posttext .= "---------------------------------------------------------------------\n";
                    $posttext .= "$post->subject\n";
                    $posttext .= $strbynameondate."\n";
                    $posttext .= "---------------------------------------------------------------------\n";
                    $posttext .= strip_tags($post->message);
                    $posttext .= "\n\n";
                    if ($post->attachment) {
                        $post->course = $course->id;
                        $post->forum = $forum->id;
                        $posttext .= forum_print_attachments($post, "text");
                    }
                    if ($canreply) {
                        $posttext .= "---------------------------------------------------------------------\n";
                        $posttext .= get_string("postmailinfo", "forum", $course->shortname)."\n";
                        $posttext .= "$CFG->wwwroot/mod/forum/post.php?reply=$post->id\n";
                    }
                    if ($canunsubscribe) {
                        $posttext .= "\n---------------------------------------------------------------------\n";
                        $posttext .= get_string("unsubscribe", "forum");
                        $posttext .= ": $CFG->wwwroot/mod/forum/subscribe.php?id=$forum->id\n";
                    }
  
                    if ($userto->mailformat == 1) {  // HTML
                        $posthtml = "<P><FONT FACE=sans-serif>".
                        "<A TARGET=\"_blank\" HREF=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</A> -> ".
                        "<A TARGET=\"_blank\" HREF=\"$CFG->wwwroot/mod/forum/index.php?id=$course->id\">$strforums</A> -> ".
                        "<A TARGET=\"_blank\" HREF=\"$CFG->wwwroot/mod/forum/view.php?f=$forum->id\">$forum->name</A>";
                        if ($discussion->name == $forum->name) {
                            $posthtml .= "</FONT></P>";
                        } else {
                            $posthtml .= " -> <A TARGET=\"_blank\" HREF=\"$CFG->wwwroot/mod/forum/discuss.php?d=$discussion->id\">$discussion->name</A></FONT></P>";
                        }
                        $posthtml .= forum_make_mail_post($post, $userfrom, $userto, $course, false, $canreply, false, false);

                        if ($canunsubscribe) {
                            $posthtml .= "\n<BR><HR SIZE=1 NOSHADE><P ALIGN=RIGHT><FONT SIZE=1><A HREF=\"$CFG->wwwroot/mod/forum/subscribe.php?id=$forum->id\">".get_string("unsubscribe", "forum")."</A></FONT></P>";
                        }

                    } else {
                      $posthtml = "";
                    }
   
                    if (! email_to_user($userto, $userfrom, $postsubject, $posttext, $posthtml)) {
                        echo "Error: mod/forum/cron.php: Could not send out mail for id $post->id to user $userto->id ($userto->email)\n";
                    } else {
                        $mailcount++;
                    }
                }
                echo "mailed to $mailcount users ...";
            }

            if (! set_field("forum_posts", "mailed", "1", "id", "$post->id")) {
                echo "Could not update the mailed field for id $post->id\n";
            }
            echo "\n";
        }
    }

    return true;
}

function forum_user_outline($course, $user, $mod, $forum) {

    if ($posts = forum_get_user_posts($forum->id, $user->id)) {
        $result->info = get_string("numposts", "forum", count($posts));

        $lastpost = array_pop($posts);
        $result->time = $lastpost->modified;
        return $result;
    }
    return NULL;
}


function forum_user_complete($course, $user, $mod, $forum) {
    global $CFG;

    if ($posts = forum_get_user_posts($forum->id, $user->id)) {
        foreach ($posts as $post) {
            if ($post->parent) {
                $footer = "<A HREF=\"$CFG->wwwroot/mod/forum/discuss.php?d=$post->discussion&parent=$post->parent\">".
                           get_string("parentofthispost", "forum")."</A>";
            } else {
                $footer = "";
            }

            forum_print_post($post, $course->id, $ownpost=false, $reply=false, $link=false, $rate=false, $footer);
        }

    } else {
        echo "<P>".get_string("noposts", "forum")."</P>";
    }
}

function forum_print_recent_activity(&$logs, $isteacher=false) {
    global $CFG, $COURSE_TEACHER_COLOR;

    $heading = false;
    $content = false;

    $strftimerecent = get_string("strftimerecent");

    foreach ($logs as $log) {
        if ($log->module == "forum") {

            if ($post = forum_get_post_from_log($log)) {
                $teacherpost = "";
                if ($forum = get_record("forum", "id", $post->forum) ) {
                    if ($forum->type == "teacher") {
                        if ($isteacher) {
                            $teacherpost = "COLOR=$COURSE_TEACHER_COLOR";
                        } else {
                            continue;
                        }
                    }
                }
                if (! $heading) {
                    print_headline(get_string("newforumposts", "forum").":");
                    $heading = true;
                    $content = true;
                }
                $date = userdate($post->modified, $strftimerecent);
                echo "<P><FONT SIZE=1 $teacherpost>$date - $post->firstname $post->lastname<BR>";
                echo "\"<A HREF=\"$CFG->wwwroot/mod/forum/$log->url\">";
                if ($log->action == "add") {
                    echo "<B>$post->subject</B>";
                } else {
                    echo "$post->subject";
                }
                echo "</A>\"</FONT></P>";
            }
        }
    }
    return $content;
}


function forum_grades($forumid) {
/// Must return an array of grades, indexed by user, and a max grade.
    global $FORUM_POST_RATINGS;

    if (!$forum = get_record("forum", "id", $forumid)) {
        return false;
    }
    if (!$forum->assessed) {
        return false;
    }
    if ($ratings = forum_get_user_grades($forumid)) {
        foreach ($ratings as $rating) {
            $u = $rating->userid;
            $r = $rating->rating;
            if (!isset($sumrating[$u])) {
                $sumrating[$u][1] = 0;
                $sumrating[$u][2] = 0;
                $sumrating[$u][3] = 0;
            }
            $sumrating[$u][$r]++;
        }
        foreach ($sumrating as $user => $rating) {
            $return->grades[$user] = $rating[1]."s/".$rating[2]."/".$rating[3]."c";
        }
    } else {
        $return->grades = array();
    }

    $return->maxgrade = "";
    return $return;
}


/// SQL FUNCTIONS ///////////////////////////////////////////////////////////

function forum_get_post_full($postid) {
/// Gets a post with all info ready for forum_print_post
    global $CFG;

    return get_record_sql("SELECT p.*, u.firstname, u.lastname, u.email, u.picture
                            FROM {$CFG->prefix}forum_posts p, 
                                 {$CFG->prefix}user u 
                           WHERE p.id = '$postid' 
                             AND p.userid = u.id");
}

function forum_get_discussion_posts($discussion, $sort) {
/// Gets posts with all info ready for forum_print_post
    global $CFG;

    return get_records_sql("SELECT p.*, u.firstname, u.lastname, u.email, u.picture
                              FROM {$CFG->prefix}forum_posts p, 
                                   {$CFG->prefix}user u 
                             WHERE p.discussion = $discussion 
                               AND p.parent > 0 
                               AND p.userid = u.id $sort");
}

function forum_get_child_posts($parent) {
/// Gets posts with all info ready for forum_print_post
    global $CFG;

    return get_records_sql("SELECT p.*, u.firstname, u.lastname, u.email, u.picture
                              FROM {$CFG->prefix}forum_posts p, 
                                   {$CFG->prefix}user u 
                             WHERE p.parent = '$parent' 
                               AND p.userid = u.id
                          ORDER BY p.created ASC");
}


function forum_search_posts($search, $courseid, $page=0, $recordsperpage=50) {
/// Returns a list of posts that were found
    global $CFG;

    if (!isteacher($courseid)) {
        $notteacherforum = "AND f.type <> 'teacher'";
    } else {
        $notteacherforum = "";
    }

    switch ($CFG->dbtype) {
        case "mysql":
             $limit = "LIMIT $page,$recordsperpage";
             break;
        case "postgres7":
             $limit = "LIMIT $recordsperpage OFFSET ".($page * $recordsperpage);
             break;
        default: 
             $limit = "LIMIT $recordsperpage,$page";
    }

    return get_records_sql("SELECT p.*,u.firstname,u.lastname,u.email,u.picture
                            FROM {$CFG->prefix}forum_posts p,  
                                 {$CFG->prefix}forum_discussions d, 
                                 {$CFG->prefix}user u, 
                                 {$CFG->prefix}forum f
                            WHERE (p.message LIKE '%$search%' OR p.subject LIKE '%$search%')
                              AND p.userid = u.id 
                              AND p.discussion = d.id 
                              AND d.course = '$courseid' 
                              AND d.forum = f.id 
                              $notteacherforum
                         ORDER BY p.modified DESC $limit");
}

function forum_get_ratings($postid, $sort="u.firstname ASC") {
/// Returns a list of ratings for a particular post - sorted.
    global $CFG;
    return get_records_sql("SELECT u.*, r.rating, r.time 
                              FROM {$CFG->prefix}forum_ratings r, 
                                   {$CFG->prefix}user u
                             WHERE r.post = '$postid' 
                               AND r.userid = u.id 
                             ORDER BY $sort");
}

function forum_get_unmailed_posts($cutofftime) {
/// Returns a list of all new posts that have not been mailed yet
    global $CFG;
    return get_records_sql("SELECT p.*, d.course 
                              FROM {$CFG->prefix}forum_posts p, 
                                   {$CFG->prefix}forum_discussions d
                             WHERE p.mailed = 0 
                               AND p.created < '$cutofftime' 
                               AND p.discussion = d.id");
}

function forum_get_user_posts($forumid, $userid) {
/// Get all the posts for a user in a forum suitable for forum_print_post
    global $CFG;

    return get_records_sql("SELECT p.*, u.firstname, u.lastname, u.email, u.picture
                              FROM {$CFG->prefix}forum f, 
                                   {$CFG->prefix}forum_discussions d, 
                                   {$CFG->prefix}forum_posts p, 
                                   {$CFG->prefix}user u 
                             WHERE f.id = '$forumid' 
                               AND d.forum = f.id 
                               AND p.discussion = d.id
                               AND p.userid = '$userid' 
                               AND p.userid = u.id
                          ORDER BY p.modified ASC");
}

function forum_get_post_from_log($log) {
/// Given a log entry, return the forum post details for it.
    global $CFG;

    if ($log->action == "add post") {

        return get_record_sql("SELECT p.*, d.forum, u.firstname, u.lastname, u.email, u.picture
                                 FROM {$CFG->prefix}forum_discussions d, 
                                      {$CFG->prefix}forum_posts p, 
                                      {$CFG->prefix}user u 
                                WHERE p.id = '$log->info' 
                                  AND d.id = p.discussion 
                                  AND p.userid = u.id 
                                  AND u.deleted <> '1'");


    } else if ($log->action == "add discussion") {

        return get_record_sql("SELECT p.*, d.forum, u.firstname, u.lastname, u.email, u.picture
                                 FROM {$CFG->prefix}forum_discussions d, 
                                      {$CFG->prefix}forum_posts p, 
                                      {$CFG->prefix}user u 
                                WHERE d.id = '$log->info' 
                                  AND d.firstpost = p.id 
                                  AND p.userid = u.id 
                                  AND u.deleted <> '1'");
    }
    return NULL;
}


function forum_get_user_grades($forumid) {
/// Get all user grades for a forum
    global $CFG;

    return get_records_sql("SELECT r.id, p.userid, r.rating
                              FROM {$CFG->prefix}forum_discussions d, 
                                   {$CFG->prefix}forum_posts p, 
                                   {$CFG->prefix}forum_ratings r
                             WHERE d.forum = '$forumid' 
                               AND p.discussion = d.id
                               AND r.post = p.id");
}


function forum_count_discussion_replies($forum="0") {
// Returns an array of counts of replies to each discussion (optionally in one forum)
    global $CFG;

    if ($forum) {
        $forumselect = " AND d.forum = '$forum'";
    }
    return get_records_sql("SELECT p.discussion, (count(*)) as replies
                              FROM {$CFG->prefix}forum_posts p, 
                                   {$CFG->prefix}forum_discussions d
                             WHERE p.parent > 0 
                               AND p.discussion = d.id 
                          GROUP BY p.discussion");
}

function forum_count_unrated_posts($discussionid, $userid) {
// How many unrated posts are in the given discussion for a given user?
    global $CFG;
    if ($posts = get_record_sql("SELECT count(*) as num
                                   FROM {$CFG->prefix}forum_posts
                                  WHERE parent > 0 
                                    AND discussion = '$discussionid' 
                                    AND userid <> '$userid' ")) {

        if ($rated = get_record_sql("SELECT count(*) as num 
                                       FROM {$CFG->prefix}forum_posts p, 
                                            {$CFG->prefix}forum_ratings r
                                      WHERE p.discussion = '$discussionid'
                                        AND p.id = r.post 
                                        AND r.userid = '$userid'")) {
            $difference = $posts->num - $rated->num;
            if ($difference > 0) {
                return $difference;
            } else {
                return 0;    // Just in case there was a counting error
            }
        } else {
            return $posts->num;
        }
    } else {
        return 0;
    }
}

function forum_get_discussions($forum="0", $forum_sort="DESC", $user=0) {
/// Get all discussions in a forum
    global $CFG;

    if ($user) {
        $userselect = " AND u.id = '$user' ";
    } else {
        $userselect = "";
    }
    return get_records_sql("SELECT p.*, u.firstname, u.lastname, u.email, u.picture
                              FROM {$CFG->prefix}forum_discussions d, 
                                   {$CFG->prefix}forum_posts p, 
                                   {$CFG->prefix}user u 
                             WHERE d.forum = '$forum' 
                               AND p.discussion = d.id 
                               AND p.parent= 0 
                               AND p.userid = u.id $userselect
                          ORDER BY p.created $forum_sort");
}



function forum_get_user_discussions($courseid, $userid) {
/// Get all discussions started by a particular user in a course
    global $CFG;

    return get_records_sql("SELECT p.*, u.firstname, u.lastname, u.email, u.picture, 
                                   f.type as forumtype, f.name as forumname, f.id as forumid
                              FROM {$CFG->prefix}forum_discussions d, 
                                   {$CFG->prefix}forum_posts p, 
                                   {$CFG->prefix}user u, 
                                   {$CFG->prefix}forum f
                             WHERE d.course = '$courseid' 
                               AND p.discussion = d.id 
                               AND p.parent = 0 
                               AND p.userid = u.id 
                               AND u.id = '$userid' 
                               AND d.forum = f.id
                          ORDER BY p.created ASC");
}


function forum_subscribed_users($course, $forum) {
/// Returns list of user objects that are subscribed to this forum
    global $CFG;

    if ($forum->forcesubscribe) {
        if ($course->category) {
            return get_course_users($course->id);
        } else {
            return get_site_users();
        }
    }
    return get_records_sql("SELECT u.* 
                              FROM {$CFG->prefix}user u, 
                                   {$CFG->prefix}forum_subscriptions s
                             WHERE s.forum = '$forum->id'
                               AND s.userid = u.id 
                               AND u.deleted <> 1");
}


/// OTHER FUNCTIONS ///////////////////////////////////////////////////////////


function forum_get_course_forum($courseid, $type) {
// How to set up special 1-per-course forums
    global $CFG;

    if ($forum = get_record("forum", "course", $courseid, "type", $type)) {
        // Already exists - return it
        return $forum;

    } else if (count_records("forum", "course", $courseid, "type", $type) > 1) {
        // It MIGHT have failed because there is more than one - freaky but has happened
        // In this case, just return the oldest one (lowest ID).
        if ($forums = get_records_sql("SELECT * FROM {$CFG->prefix}forum 
                                           WHERE course = '$courseid'
                                             AND type   = '$type'
                                        ORDER BY id ASC")) {
            foreach ($forums as $forum) {
                return $forum;   // ie the first one
            }
        }
    }

    // Doesn't exist, so create one now.
    $forum->course = $courseid;
    $forum->type = "$type";
    switch ($forum->type) {
        case "news":
            $forum->name  = get_string("namenews", "forum");
            $forum->forcesubscribe = 1;
            $forum->intro = get_string("intronews", "forum");
            $forum->open = 1;   // 0 - no, 1 - posts only, 2 - discuss and post
            $forum->assessed = 0;
            if ($site = get_site()) {
                if ($courseid == $site->id) {
                    $forum->name  = get_string("sitenews");
                    $forum->forcesubscribe = 0;
                }
            }
            break;
        case "social":
            $forum->name  = get_string("namesocial", "forum");
            $forum->intro = get_string("introsocial", "forum");
            $forum->open = 2;   // 0 - no, 1 - posts only, 2 - discuss and post
            $forum->assessed = 0;
            $forum->forcesubscribe = 0;
            break;
        case "teacher":
            $forum->name  = get_string("nameteacher", "forum");
            $forum->intro = get_string("introteacher", "forum");
            $forum->open = 0;   // 0 - no, 1 - posts only, 2 - discuss and post
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

    if ($forum->type != "teacher") {
        if (! $module = get_record("modules", "name", "forum")) {
            notify("Could not find forum module!!");
            return false;
        }
        $mod->course = $courseid;
        $mod->module = $module->id;
        $mod->instance = $forum->id;
        $mod->section = 0;
        if (! $mod->coursemodule = add_course_module($mod) ) {   // assumes course/lib.php is loaded
            notify("Could not add a new course module to the course '$course->fullname'");
            return false;
        }
        if (! $sectionid = add_mod_to_section($mod) ) {   // assumes course/lib.php is loaded
            notify("Could not add the new course module to that section");
            return false;
        }
        if (! set_field("course_modules", "section", $sectionid, "id", $mod->coursemodule)) {
            notify("Could not update the course module with the correct section");
            return false;
        }
        include_once("$CFG->dirroot/course/lib.php");
        $modinfo = serialize(get_array_of_activities($courseid));
        if (!set_field("course", "modinfo", $modinfo, "id", $courseid)) {
            error("Could not cache module information!");
        }
    }
        
    return get_record("forum", "id", "$forum->id");
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

    $output .= "<TR><TD BGCOLOR=\"$THEME->cellcontent2\" WIDTH=35 VALIGN=TOP>";
    $output .= print_user_picture($user->id, $course->id, $user->picture, false, true);
    $output .= "</TD>";

    if ($post->parent) {
        $output .= "<TD NOWRAP BGCOLOR=\"$THEME->cellheading\">";
    } else {
        $output .= "<TD NOWRAP BGCOLOR=\"$THEME->cellheading2\">";
    }
    $output .= "<P>";
    $output .= "<FONT SIZE=3><B>$post->subject</B></FONT><BR>";
    $output .= "<FONT SIZE=2>";
    $by->name = "<A HREF=\"$CFG->wwwroot/user/view.php?id=$user->id&course=$course->id\">$user->firstname $user->lastname</A>";
    $by->date = userdate($post->created, "", $touser->timezone);
    $output .= get_string("bynameondate", "forum", $by);
    $output .= "</FONT></P></TD></TR>";
    $output .= "<TR><TD BGCOLOR=\"$THEME->cellcontent2\" WIDTH=10>";
    $output .= "&nbsp;";
    $output .= "</TD><TD BGCOLOR=\"$THEME->cellcontent\">\n";

    if ($post->attachment) {
        $post->course = $course->id;
        $post->forum = get_field("forum_discussions", "forum", "id", $post->discussion);
        $output .= "<DIV ALIGN=right>";
        $output .= forum_print_attachments($post, "html");
        $output .= "</DIV>";
    }

    $output .= format_text($post->message, $post->format);

    $output .= "<P ALIGN=right><FONT SIZE=-1>";

    $age = time() - $post->created;
    if ($ownpost) {
        $output .= "<A HREF=\"$CFG->wwwroot/mod/forum/post.php?delete=$post->id\">".get_string("delete", "forum")."</A>";
        if ($reply) {
            $output .= " | <A TARGET=\"_blank\" HREF=\"$CFG->wwwroot/mod/forum/post.php?reply=$post->id\">".get_string("reply", "forum")."</A>";
        }
        $output .= "&nbsp;&nbsp;";
    } else {
        if ($reply) {
            $output .= "<A TARGET=\"_blank\" HREF=\"$CFG->wwwroot/mod/forum/post.php?reply=$post->id\">".get_string("reply", "forum")."</A>&nbsp;&nbsp;";
        }
    }

    $output .= "</P>";
    $output .= "<DIV ALIGN=right><P ALIGN=right>";
    
    if ($link) {
        if ($post->replies == 1) {
            $replystring = get_string("repliesone", "forum", $post->replies);
        } else {
            $replystring = get_string("repliesmany", "forum", $post->replies);
        }
        $output .= "<A HREF=\"$CFG->wwwroot/mod/forum/discuss.php?d=$post->discussion\"><B>".get_string("discussthistopic", "forum")."</B></A> ($replystring)&nbsp;&nbsp;";
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
    global $THEME, $USER, $CFG;

    if ($post->parent) {
        echo "<TABLE BORDER=0 CELLPADDING=3 CELLSPACING=0 CLASS=\"forumpost\">";
    } else {
        echo "<TABLE BORDER=0 CELLPADDING=3 CELLSPACING=0 CLASS=\"forumpost\" WIDTH=100%>";
    }

    echo "<TR><TD BGCOLOR=\"$THEME->cellcontent2\" CLASS=\"forumpostpicture\" WIDTH=35 VALIGN=TOP>";
    print_user_picture($post->userid, $courseid, $post->picture);
    echo "</TD>";

    if ($post->parent) {
        echo "<TD NOWRAP BGCOLOR=\"$THEME->cellheading\" CLASS=\"forumpostheader\" WIDTH=\"100%\">";
    } else {
        echo "<TD NOWRAP BGCOLOR=\"$THEME->cellheading2\" CLASS=\"forumpostheadertopic\" WIDTH=\"100%*\">";
    }
    echo "<P>";
    echo "<FONT SIZE=3><B>$post->subject</B></FONT><BR>";
    echo "<FONT SIZE=2>";
    $by->name = "<A HREF=\"$CFG->wwwroot/user/view.php?id=$post->userid&course=$courseid\">$post->firstname $post->lastname</A>";
    $by->date = userdate($post->created);
    print_string("bynameondate", "forum", $by);
    echo "</FONT></P></TD></TR>";
    echo "<TR><TD BGCOLOR=\"$THEME->cellcontent2\" CLASS=\"forumpostside\" WIDTH=10>";
    echo "&nbsp;";
    echo "</TD><TD BGCOLOR=\"$THEME->cellcontent\" CLASS=\"forumpostmessage\">\n";

    if ($post->attachment) {
        $post->course = $courseid;
        $post->forum = get_field("forum_discussions", "forum", "id", $post->discussion);
        echo "<DIV ALIGN=right>";
        forum_print_attachments($post);
        echo "</DIV>";
    }

    if ($link and (strlen(strip_tags($post->message)) > FORUM_LONG_POST)) {
        // Print shortened version
        echo format_text(forum_shorten_post($post->message), $post->format);
        $numwords = count_words(strip_tags($post->message));
        echo "<P><A HREF=\"$CFG->wwwroot/mod/forum/discuss.php?d=$post->discussion\">";
        echo get_string("readtherest", "forum");
        echo "</A> (".get_string("numwords", "", $numwords).")...</P>";
    } else {
        // Print whole message
        echo format_text($post->message, $post->format);
    }

    echo "<P ALIGN=right><FONT SIZE=-1>";

    $age = time() - $post->created;
    if ($ownpost) {
        if ($age < $CFG->maxeditingtime) {
            echo "<A HREF=\"$CFG->wwwroot/mod/forum/post.php?edit=$post->id\">".get_string("edit", "forum")."</A> | ";
        }
    }
    if ($ownpost or isteacher($courseid)) {
        echo "<A HREF=\"$CFG->wwwroot/mod/forum/post.php?delete=$post->id\">".get_string("delete", "forum")."</A>";
        if ($reply) {
            echo "| ";
        } else {
            echo "&nbsp;&nbsp;";
        }
    }
    if ($reply) {
        echo "<A HREF=\"$CFG->wwwroot/mod/forum/post.php?reply=$post->id\">".get_string("reply", "forum")."</A>";
        echo "&nbsp;&nbsp;";
    }
    echo "</P>";

    echo "<DIV ALIGN=right><P ALIGN=right>";
    if ($rate && $USER->id) {
        if (isteacher($courseid)) {
            forum_print_ratings($post->id);
            if ($USER->id != $post->userid) {
                forum_print_rating($post->id, $USER->id);
            }
        } else if ($USER->id == $post->userid) {
            forum_print_ratings($post->id);
        } else {
            forum_print_rating($post->id, $USER->id);
        }
    }
    
    if ($link) {
        if ($post->replies == 1) {
            $replystring = get_string("repliesone", "forum", $post->replies);
        } else {
            $replystring = get_string("repliesmany", "forum", $post->replies);
        }
        echo "<A HREF=\"$CFG->wwwroot/mod/forum/discuss.php?d=$post->discussion\"><B>".get_string("discussthistopic", "forum")."</B></A> ($replystring)&nbsp;&nbsp;";
    }
    echo "</P>";
    if ($footer) {
        echo "<P>$footer</P>";
    }
    echo "</DIV>";
    echo "</TD></TR>\n</TABLE>\n\n";
}


function forum_print_post_header(&$post, $courseid, $ownpost=false, $reply=false, $link=false, $rate=false, $footer="") {
    global $THEME, $USER, $CFG;

    if ($post->parent) {
        echo "<TABLE BORDER=0 CELLPADDING=3 CELLSPACING=0 CLASS=\"forumpost\">";
    } else {
        echo "<TABLE BORDER=0 CELLPADDING=3 CELLSPACING=0 CLASS=\"forumpost\" WIDTH=100%>";
    }

    echo "<TR><TD BGCOLOR=\"$THEME->cellcontent2\" CLASS=\"forumpostpicture\" WIDTH=35 VALIGN=TOP>";
    print_user_picture($post->userid, $courseid, $post->picture);
    echo "</TD>";

    if ($post->parent) {
        echo "<TD NOWRAP BGCOLOR=\"$THEME->cellheading\" CLASS=\"forumpostheader\">";
    } else {
        echo "<TD NOWRAP BGCOLOR=\"$THEME->cellheading2\" CLASS=\"forumpostheadertopic\">";
    }
    echo "<P>";
    echo "<FONT SIZE=3><B>$post->subject</B></FONT><BR>";
    echo "<FONT SIZE=2>";
    $by->name = "<A HREF=\"$CFG->wwwroot/user/view.php?id=$post->userid&course=$courseid\">$post->firstname $post->lastname</A>";
    $by->date = userdate($post->created);
    print_string("bynameondate", "forum", $by);
    echo "</FONT></P></TD>";

    if ($post->parent) {
        echo "<TD VALIGN=BOTTOM BGCOLOR=\"$THEME->cellheading\" CLASS=\"forumpostheader\">";
    } else {
        echo "<TD VALIGN=BOTTOM BGCOLOR=\"$THEME->cellheading2\" CLASS=\"forumpostheadertopic\">";
    }
    echo "<P ALIGN=right><FONT SIZE=-1>";

    if ($link) {
        if ($post->replies == 1) {
            $replystring = get_string("repliesone", "forum", $post->replies);
        } else {
            $replystring = get_string("repliesmany", "forum", $post->replies);
        }
        echo "<A HREF=\"$CFG->wwwroot/mod/forum/discuss.php?d=$post->discussion\"><B>".get_string("discussthistopic", "forum")."</B></A> ($replystring)&nbsp;&nbsp;";
    }
    echo "</P>";
    echo "</TD></TR>\n</TABLE>\n\n";
}


function forum_shorten_post($message) {
// Given a post object that we already know has a long message
// this function truncates the message nicely to the first 
// sane place between FORUM_LONG_POST and FORUM_SHORT_POST

   $i = 0;
   $tag = false;
   $length = strlen($message);
   $count = 0;
   $stopzone = false;
   $truncate = 0;

   for ($i=0; $i<$length; $i++) {
       $char = $message[$i];

       switch ($char) {
           case "<": 
               $tag = true;
               break;
           case ">": 
               $tag = false;
               break;
           default:
               if (!$tag) {
                   if ($stopzone) {
                       if ($char == ".") {
                           $truncate = $i+1;
                           break 2;
                       }
                   }
                   $count++;
               }
               break;
       }
       if (!$stopzone) {
           if ($count > FORUM_SHORT_POST) {
               $stopzone = true;
           }
       }
   }

   if (!$truncate) {
       $truncate = $i;
   }

   return substr($message, 0, $truncate);
}


function forum_print_ratings($post) {
    if ($ratings = get_records("forum_ratings", "post", $post)) {
        $sumrating[1] = 0;
        $sumrating[2] = 0;
        $sumrating[3] = 0;
        foreach ($ratings as $rating) {
            $sumrating[$rating->rating]++;
        }
        $summary = $sumrating[1]."s/".$sumrating[2]."/".$sumrating[3]."c";

        echo get_string("ratings", "forum").": ";
        link_to_popup_window ("/mod/forum/report.php?id=$post", "ratings", $summary, 400, 550);
    }
}

function forum_print_rating($post, $user) {
    global $FORUM_POST_RATINGS;

    if ($rating = get_record("forum_ratings", "userid", $user, "post", $post)) {
        if ($FORUM_POST_RATINGS[$rating->rating]) {
            echo "<FONT SIZE=-1>".get_string("youratedthis", "forum").": <FONT COLOR=green>";
            echo $FORUM_POST_RATINGS[$rating->rating];
            echo "</FONT></FONT>";
            return;
        }
    }
    choose_from_menu($FORUM_POST_RATINGS, $post, "", get_string("rate", "forum")."...");
}

function forum_print_mode_form($discussion, $mode) {
    GLOBAL $FORUM_LAYOUT_MODES;

    echo "<CENTER><P>";
    popup_form("discuss.php?d=$discussion&mode=", $FORUM_LAYOUT_MODES, "mode", $mode, "");
    echo "</P></CENTER>\n";
}

function forum_print_search_form($course, $search="", $return=false, $type="") {
    global $CFG;

    if ($type == "plain") {
        $output = "<TABLE BORDER=0 CELLPADDING=0 CELLSPACING=0><TR><TD NOWRAP>";
        $output .= "<FORM NAME=search ACTION=\"$CFG->wwwroot/mod/forum/search.php\">";
        $output .= "<FONT SIZE=\"-1\">";
        $output .= "<INPUT NAME=search TYPE=text SIZE=15 VALUE=\"$search\">";
        $output .= "<INPUT VALUE=\"".get_string("searchforums", "forum")."\" TYPE=submit>";
        $output .= "</FONT>";
        $output .= "<INPUT NAME=id TYPE=hidden VALUE=\"$course->id\">";
        $output .= "</FORM>";
        $output .= "</TD></TR></TABLE>";
    } else {
        $output = "<TABLE BORDER=0 CELLPADDING=10 CELLSPACING=0><TR><TD ALIGN=CENTER>";
        $output .= "<FORM NAME=search ACTION=\"$CFG->wwwroot/mod/forum/search.php\">";
        $output .= "<FONT SIZE=\"-1\">";
        $output .= "<INPUT NAME=search TYPE=text SIZE=15 VALUE=\"$search\"><BR>";
        $output .= "<INPUT VALUE=\"".get_string("searchforums", "forum")."\" TYPE=submit>";
        $output .= "</FONT>";
        $output .= "<INPUT NAME=id TYPE=hidden VALUE=\"$course->id\">";
        $output .= "</FORM>";
        $output .= "</TD></TR></TABLE>";
    }

    if ($return) {
        return $output;
    }
    echo $output;
}


function forum_set_return() {
    global $CFG, $SESSION;

    if (! isset($SESSION->fromdiscussion)) {
        if (!empty($_SERVER['HTTP_REFERER'])) {
            $referer = $_SERVER['HTTP_REFERER'];
        } else {
            $referer = "";
        }
        // If the referer is NOT a login screen then save it.
        if (! strncasecmp("$CFG->wwwroot/login", $referer, 300)) {
            $SESSION->fromdiscussion = $_SERVER["HTTP_REFERER"];
        }
    }
}


function forum_go_back_to($default) {
    global $SESSION;

    if (!empty($SESSION->fromdiscussion)) {
        $returnto = $SESSION->fromdiscussion;
        unset($SESSION->fromdiscussion);
        return $returnto;
    } else {
        return $default;
    }
}

function forum_file_area_name($post) {
//  Creates a directory file name, suitable for make_upload_directory()
    global $CFG;

    return "$post->course/$CFG->moddata/forum/$post->forum/$post->id";
}

function forum_file_area($post) {
    return make_upload_directory( forum_file_area_name($post) );
}

function forum_delete_old_attachments($post, $exception="") {
// Deletes all the user files in the attachments area for a post
// EXCEPT for any file named $exception

    if ($basedir = forum_file_area($post)) {
        if ($files = get_directory_list($basedir)) {
            foreach ($files as $file) {
                if ($file != $exception) {
                    unlink("$basedir/$file");
                    notify("Existing file '$file' has been deleted!");
                }
            }
        }
        if (!$exception) {  // Delete directory as well, if empty
            rmdir("$basedir");
        }
    }
}

function forum_print_attachments($post, $return=NULL) {
// if return=html, then return a html string.
// if return=text, then return a text-only string.
// otherwise, print HTML

    global $CFG;

    $filearea = forum_file_area_name($post);

    if ($basedir = forum_file_area($post)) {
        if ($files = get_directory_list($basedir)) {
            $strattachment = get_string("attachment", "forum");
            foreach ($files as $file) {
                $icon = mimeinfo("icon", $file);
                if ($CFG->slasharguments) {
                    $ffurl = "file.php/$filearea/$file";
                } else {
                    $ffurl = "file.php?file=/$filearea/$file";
                }
                $image = "<IMG BORDER=0 SRC=\"$CFG->wwwroot/files/pix/$icon\" HEIGHT=16 WIDTH=16 ALT=\"File\">";

                if ($return == "html") {
                    $output .= "<A HREF=\"$CFG->wwwroot/$ffurl\">$image</A> ";
                    $output .= "<A HREF=\"$CFG->wwwroot/$ffurl\">$file</A><BR>";

                } else if ($return == "text") {
                    $output .= "$strattachment $file:\n$CFG->wwwroot/$ffurl\n";

                } else {
                    link_to_popup_window("/$ffurl", "attachment", $image, 500, 500, $strattachment);
                    echo "<A HREF=\"$CFG->wwwroot/$ffurl\">$file</A>";
                    echo "<BR>";
                }
            }
        }
    }
    if ($return) {
        return $output;
    }
}

function forum_add_attachment($post, $newfile) {
// $post is a full post record, including course and forum
// $newfile is a full upload array from $_FILES
// If successful, this function returns the name of the file

    if (empty($newfile['name'])) {
        return "";
    }

    $newfile_name = clean_filename($newfile['name']);

    if (valid_uploaded_file($newfile)) {
        if (! $newfile_name) {
            notify("This file had a wierd filename and couldn't be uploaded");

        } else if (! $dir = forum_file_area($post)) {
            notify("Attachment could not be stored");
            $newfile_name = "";

        } else {
            if (move_uploaded_file($newfile['tmp_name'], "$dir/$newfile_name")) {
                forum_delete_old_attachments($post, $newfile_name);
            } else {
                notify("An error happened while saving the file on the server");
                $newfile_name = "";
            }
        }
    } else {
        $newfile_name = "";
    }

    return $newfile_name;
}

function forum_add_new_post($post) {

    $post->created = $post->modified = time();
    $post->mailed = "0";

    $newfile = $post->attachment;
    $post->attachment = "";

    if (! $post->id = insert_record("forum_posts", $post)) { 
        return false;
    }

    if ($post->attachment = forum_add_attachment($post, $newfile)) {
        set_field("forum_posts", "attachment", $post->attachment, "id", $post->id);
    }
    
    return $post->id;
}

function forum_update_post($post) {

    $post->modified = time();

    if (!$post->parent) {   // Post is a discussion starter - update discussion title too
        set_field("forum_discussions", "name", $post->subject, "id", $post->discussion);
    }
    if ($newfilename = forum_add_attachment($post, $post->attachment)) {
        $post->attachment = $newfilename;
    } else {
        unset($post->attachment);
    }
    return update_record("forum_posts", $post);
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
    $post->userid      = $USER->id;
    $post->created     = $timenow;
    $post->modified    = $timenow;
    $post->mailed      = 0;
    $post->subject     = $discussion->name;
    $post->message     = $discussion->intro;
    $post->attachment  = "";
    $post->forum       = $discussion->forum;
    $post->course      = $discussion->course;

    if (! $post->id = insert_record("forum_posts", $post) ) {
        return 0;
    }

    if ($post->attachment = forum_add_attachment($post, $discussion->attachment)) {
        set_field("forum_posts", "attachment", $post->attachment, "id", $post->id); //ignore errors
    }

    // Now do the real module entry

    $discussion->firstpost    = $post->id;
    $discussion->timemodified = $timenow;

    if (! $discussion->id = insert_record("forum_discussions", $discussion) ) {
        delete_records("forum_posts", "id", $post->id);
        return 0;
    }

    // Finally, set the pointer on the post.
    if (! set_field("forum_posts", "discussion", $discussion->id, "id", $post->id)) {
        delete_records("forum_posts", "id", $post->id);
        delete_records("forum_discussions", "id", $discussion->id);
        return 0;
    }

    return $discussion->id;
}


function forum_delete_discussion($discussion) {
// $discussion is a discussion record object

    $result = true;

    if ($posts = get_records("forum_posts", "discussion", $discussion->id)) {
        foreach ($posts as $post) {
            $post->course = $discussion->course;
            $post->forum  = $discussion->forum;
            if (! delete_records("forum_ratings", "post", "$post->id")) {
                $result = false;
            }
            if (! forum_delete_post($post)) {
                $result = false;
            }
        }
    }

    if (! delete_records("forum_discussions", "id", "$discussion->id")) {
        $result = false;
    }

    return $result;
}


function forum_delete_post($post) {
   if (delete_records("forum_posts", "id", $post->id)) {
       delete_records("forum_ratings", "post", $post->id);  // Just in case
       if ($post->attachment) {
           $discussion = get_record("forum_discussions", "id", $post->discussion);
           $post->course = $discussion->course;
           $post->forum  = $discussion->forum;
           forum_delete_old_attachments($post);
       }
       return true;
   }
   return false;
}


function forum_print_user_discussions($courseid, $userid) {
    global $CFG, $USER;


    if ($discussions = forum_get_user_discussions($courseid, $userid)) {
        $user = get_record("user", "id", $userid);
        echo "<HR>";
        print_heading( get_string("discussionsstartedby", "forum", "$user->firstname $user->lastname") );
        $replies = forum_count_discussion_replies();
        foreach ($discussions as $discussion) {
            if (($discussion->forumtype == "teacher") and !isteacher($courseid)) {
                continue;
            }
            if (!empty($replies[$discussion->discussion])) {
                $discussion->replies = $replies[$discussion->discussion]->replies;
            } else {
                $discussion->replies = 0;
            }
            $inforum = get_string("inforum", "forum", "<A HREF=\"$CFG->wwwroot/mod/forum/view.php?f=$discussion->forumid\">$discussion->forumname</A>");
            $discussion->subject .= " ($inforum)";
            if (!empty($USER->id)) {
                $ownpost = ($discussion->userid == $USER->id);
            } else {
                $ownpost = false;
            }
            forum_print_post($discussion, $courseid, $ownpost, $reply=0, $link=1, $assessed=false);
            echo "<BR>\n";
        }
    }
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
    return record_exists("forum_subscriptions", "userid", $userid, "forum", $forumid);
}

function forum_subscribe($userid, $forumid) {
/// Adds user to the subscriber list

    $sub->userid  = $userid;
    $sub->forum = $forumid;

    return insert_record("forum_subscriptions", $sub);
}

function forum_unsubscribe($userid, $forumid) {
/// Removes user from the subscriber list
    return delete_records("forum_subscriptions", "userid", $userid, "forum", $forumid);
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
        return ($forum->open == 2);
    }
}

function forum_user_can_post($forum, $user=NULL) {
// $forum, $user are objects

    if ($user) {
        $isteacher = isteacher($forum->course, $user->id);
    } else {
        $isteacher = isteacher($forum->course);
    }

    if ($forum->type == "teacher") {
        return $isteacher;
    } else if ($isteacher) {
        return true;
    } else {
        return $forum->open;
    }
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
        if (! $forum = forum_get_course_forum($course->id, "news")) {
            error("Could not find or create a main forum in this course (id $course->id)");
        }
    }

    if (forum_user_can_post_discussion($forum)) {
        echo "<P ALIGN=CENTER>";
        echo "<A HREF=\"$CFG->wwwroot/mod/forum/post.php?forum=$forum->id\">";
        echo get_string("addanewdiscussion", "forum")."</A>...";
        echo "</P>\n";
    }

    if (! $discussions = forum_get_discussions($forum->id, $forum_sort) ) {
        echo "<P ALIGN=CENTER><B>(".get_string("nodiscussions", "forum").")</B></P>";
        return;

    }
    
    if ((!$forum_numdiscussions) && ($forum_style == "plain") && (count($discussions) > FORUM_MANY_DISCUSSIONS) ) { 
        $forum_style = "header";  // Abbreviate display if it's going to be long.
    }

    $replies = forum_count_discussion_replies($forum->id);

    $canreply = forum_user_can_post($forum);

    $discussioncount = 0;

    if ($forum_style == "minimal") {
        $strftimerecent = get_string("strftimerecent");
        $strmore = get_string("more", "forum");
    }

    foreach ($discussions as $discussion) {
        $discussioncount++;

        if ($forum_numdiscussions && ($discussioncount > $forum_numdiscussions)) {
            echo "<P ALIGN=right><A HREF=\"$CFG->wwwroot/mod/forum/view.php?f=$forum->id\">";
            echo get_string("olderdiscussions", "forum")."</A> ...</P>";
            break;
        }
        if (!empty($replies[$discussion->discussion])) {
            $discussion->replies = $replies[$discussion->discussion]->replies;
        } else {
            $discussion->replies = 0;
        }
        if (!empty($USER->id)) {
            $ownpost = ($discussion->userid == $USER->id);
        } else {
            $ownpost=false;
        }
        switch ($forum_style) {
            case "minimal":
                echo "<P><FONT COLOR=#555555>".userdate($discussion->modified, $strftimerecent)." - $discussion->firstname</FONT>";
                echo "<BR>$discussion->subject ";
                echo "<A HREF=\"$CFG->wwwroot/mod/forum/discuss.php?d=$discussion->discussion\">";
                echo $strmore."...</A>";
                echo "</P>\n";
            break;
            case "header":
                forum_print_post_header($discussion, $forum->course, $ownpost, $reply=0, $link=1, $assessed=false);
            break;
            default:
                if ($canreply or $discussion->replies) {
                    $link = true;
                } else {
                    $link = false;
                }
                forum_print_post($discussion, $forum->course, $ownpost, $reply=0, $link, $assessed=false);
                echo "<BR>\n";
            break;
        }
    }
}

function forum_print_discussion($course, $forum, $discussion, $post, $mode) {

    global $USER;

    if (!empty($USER->id)) {
        $ownpost = ($USER->id == $post->userid);
    } else {
        $ownpost = false;
    }
    $reply   = forum_user_can_post($forum);

    forum_print_post($post, $course->id, $ownpost, $reply, $link=false, $rate=false);

    forum_print_mode_form($discussion->id, $mode);

    $ratingform = false;
    if ($forum->assessed and !empty($USER->id)) {
        $unrated = forum_count_unrated_posts($discussion->id, $USER->id);
        if ($unrated > 0) {
            $ratingform = true;
        }
    }

    if ($ratingform) {
        echo "<FORM NAME=form METHOD=POST ACTION=rate.php>";
        echo "<INPUT TYPE=hidden NAME=id VALUE=\"$course->id\">";
    }

    switch ($mode) {
        case 1 :   // Flat ascending
        case -1 :  // Flat descending
        default:   
            echo "<UL>";
            forum_print_posts_flat($post->discussion, $course->id, $mode, $forum->assessed, $reply);
            echo "</UL>";
            break;

        case 2 :   // Threaded 
            forum_print_posts_threaded($post->id, $course->id, 0, $forum->assessed, $reply);
            break;

        case 3 :   // Nested
            forum_print_posts_nested($post->id, $course->id, $forum->assessed, $reply);
            break;
    }

    if ($ratingform) {
        echo "<CENTER><P ALIGN=center><INPUT TYPE=submit VALUE=\"".get_string("sendinratings", "forum")."\">";
        helpbutton("ratings", get_string("separateandconnected"), "forum");
        echo "</P></CENTER>";
        echo "</FORM>";
    }
}

function forum_print_posts_flat($discussion, $course, $direction, $assessed, $reply) { 
    global $USER;

    $link  = false;

    if ($direction < 0) {
        $sort = "ORDER BY created DESC";
    } else {
        $sort = "ORDER BY created ASC";
    }

    if ($posts = forum_get_discussion_posts($discussion, $sort)) {
        foreach ($posts as $post) {
            $ownpost = ($USER->id == $post->userid);
            forum_print_post($post, $course, $ownpost, $reply, $link, $assessed);
        }
    } else {
        return;
    }
}

function forum_print_posts_threaded($parent, $course, $depth, $assessed, $reply) { 
    global $USER;

    $link  = false;

    if ($posts = forum_get_child_posts($parent)) {
        foreach ($posts as $post) {

            echo "<UL>";
            if ($depth > 0) {
                $ownpost = ($USER->id == $post->userid);
                forum_print_post($post, $course, $ownpost, $reply, $link, $assessed);  // link=true?
                echo "<BR>";
            } else {
                $by->name = "$post->firstname $post->lastname";
                $by->date = userdate($post->created);
                echo "<LI><P><FONT SIZE=-1><B><A HREF=\"discuss.php?d=$post->discussion&parent=$post->id\">$post->subject</A></B> ";
                print_string("bynameondate", "forum", $by);
                echo "</FONT></P></LI>";
            }

            forum_print_posts_threaded($post->id, $course, $depth-1, $assessed, $reply);
            echo "</UL>\n";
        }
    } else {
        return;
    }
}

function forum_print_posts_nested($parent, $course, $assessed, $reply) { 
    global $USER;

    $link  = false;

    if ($posts = forum_get_child_posts($parent)) {
        foreach ($posts as $post) {

            if (empty($USER->id)) {
                $ownpost = false;
            } else {
                $ownpost = ($USER->id == $post->userid);
            }

            echo "<UL>";
            forum_print_post($post, $course, $ownpost, $reply, $link, $assessed);
            echo "<BR>";
            forum_print_posts_nested($post->id, $course, $assessed, $reply);
            echo "</UL>\n";
        }
    } else {
        return;
    }
}

function forum_set_display_mode($mode=0) {
    global $USER, $FORUM_DEFAULT_DISPLAY_MODE;

    if ($mode) {
        $USER->mode = $mode;
    } else if (empty($USER->mode)) {
        $USER->mode = $FORUM_DEFAULT_DISPLAY_MODE;
    }
}


?>
