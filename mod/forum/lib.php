<?PHP  // $Id$

require_once("$CFG->dirroot/files/mimetypes.php");

/// CONSTANTS ///////////////////////////////////////////////////////////

define("FORUM_MODE_FLATOLDEST", 1);
define("FORUM_MODE_FLATNEWEST", -1);
define("FORUM_MODE_THREADED", 2);
define("FORUM_MODE_NESTED", 3);

$FORUM_LAYOUT_MODES = array ( FORUM_MODE_FLATOLDEST => get_string("modeflatoldestfirst", "forum"),
                              FORUM_MODE_FLATNEWEST => get_string("modeflatnewestfirst", "forum"),
                              FORUM_MODE_THREADED   => get_string("modethreaded", "forum"),
                              FORUM_MODE_NESTED     => get_string("modenested", "forum") );

// These are course content forums that can be added to the course manually
$FORUM_TYPES   = array ("general"    => get_string("generalforum", "forum"),
                        "eachuser"   => get_string("eachuserforum", "forum"),
                        "single"     => get_string("singleforum", "forum") );

$FORUM_OPEN_MODES   = array ("2" => get_string("openmode2", "forum"),
                             "1" => get_string("openmode1", "forum"),
                             "0" => get_string("openmode0", "forum") );

if (!isset($CFG->forum_displaymode)) {
    set_config("forum_displaymode", FORUM_MODE_NESTED);
}

if (!isset($CFG->forum_shortpost)) {
    set_config("forum_shortpost", 300);  // Less non-HTML characters than this is short
}

if (!isset($CFG->forum_longpost)) {
    set_config("forum_longpost", 600);  // More non-HTML characters than this is long
}

if (!isset($CFG->forum_manydiscussions)) {
    set_config("forum_manydiscussions", 100);  // Number of discussions on a page
}

if (!isset($CFG->forum_maxbytes)) {
    set_config("forum_maxbytes", 512000);  // Default maximum size for all forums
}



/// STANDARD FUNCTIONS ///////////////////////////////////////////////////////////

function forum_add_instance($forum) {
// Given an object containing all the necessary data,
// (defined by the form in mod.html) this function
// will create a new instance and return the id number
// of the new instance.

    global $CFG;

    $forum->timemodified = time();
    $forum->intro = clean_text($forum->intro);

    if (!$forum->userating) {
        $forum->assessed = 0;
    }

    if (!empty($forum->ratingtime)) {
        $forum->assesstimestart  = make_timestamp($forum->startyear, $forum->startmonth, $forum->startday,
                                                  $forum->starthour, $forum->startminute, 0);
        $forum->assesstimefinish = make_timestamp($forum->finishyear, $forum->finishmonth, $forum->finishday,
                                                  $forum->finishhour, $forum->finishminute, 0);
    } else {
        $forum->assesstimestart  = 0;
        $forum->assesstimefinish = 0;
    }

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

    return $forum->id;
}


function forum_update_instance($forum) {
// Given an object containing all the necessary data,
// (defined by the form in mod.html) this function
// will update an existing instance with new data.

    $forum->timemodified = time();
    $forum->id = $forum->instance;

    if (!$forum->userating) {
        $forum->assessed = 0;
    }

    if (!empty($forum->ratingtime)) {
        $forum->assesstimestart  = make_timestamp($forum->startyear, $forum->startmonth, $forum->startday,
                                                  $forum->starthour, $forum->startminute, 0);
        $forum->assesstimefinish = make_timestamp($forum->finishyear, $forum->finishmonth, $forum->finishday,
                                                  $forum->finishhour, $forum->finishminute, 0);
    } else {
        $forum->assesstimestart  = 0;
        $forum->assesstimefinish = 0;
    }

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

    return update_record("forum", $forum);
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
/// Function to be run periodically according to the moodle cron
/// Finds all posts that have yet to be mailed out, and mails them
/// out to all subscribers

    global $CFG, $USER;

    if (!empty($USER)) { // Remember real USER account if necessary
        $realuser = $USER;
    }

    $cutofftime = time() - $CFG->maxeditingtime;

    if ($posts = forum_get_unmailed_posts($cutofftime)) {

        /// Mark them all now as being mailed.  It's unlikely but possible there
        /// might be an error later so that a post is NOT actually mailed out,
        /// but since mail isn't crucial, we can accept this risk.  Doing it now
        /// prevents the risk of duplicated mails, which is a worse problem.

        foreach ($posts as $key => $post) {
            if (! set_field("forum_posts", "mailed", "1", "id", "$post->id")) {
                echo "Error marking post id post->id as being mailed.  This post will not be mailed.\n";
                unset($posts[$key]);
            }
        }

        $timenow = time();

        foreach ($posts as $post) {

            echo "\n";
            print_string("processingpost", "forum", $post->id);
            echo "\n";

            if (! $userfrom = get_record("user", "id", "$post->userid")) {
                echo "Could not find user $post->userid\n";
                continue;
            }

            $userfrom->precedence = "bulk";   // This gets added to the email header

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

            if (!empty($course->lang)) {
                $CFG->courselang = $course->lang;
            } else {
                unset($CFG->courselang);
            }

            $groupmode = false;
            if ($cm = get_coursemodule_from_instance("forum", $forum->id, $course->id)) {
                if ($groupmode = groupmode($course, $cm) and $discussion->groupid > 0) {   // Groups are being used
                    if (!$group = get_record("groups", "id", $discussion->groupid)) {   // Can't find group
                        continue;                                            // Be safe and don't send it to anyone
                    }
                }
            } else {
                $cm->id = 0;
            }


            if ($users = forum_subscribed_users($course, $forum)) {
                $canunsubscribe = ! forum_is_forcesubscribed($forum->id);

                $mailcount=0;
                $errorcount=0;
                foreach ($users as $userto) {
                    if ($groupmode) {    // Look for a reason not to send this email
                        if (!isteacheredit($course->id, $userto->id)) {
                            if (!empty($group->id)) {
                                if (!ismember($group->id, $userto->id)) {
                                    continue;
                                }
                            }
                        }
                    }

                    /// GWD: reset timelimit so that script does not get timed out when posting to many users
                    @set_time_limit(0);

                    /// Override the language and timezone of the "current" user, so that
                    /// mail is customised for the receiver.
                    $USER->lang     = $userto->lang;
                    $USER->timezone = $userto->timezone;

                    $canreply = forum_user_can_post($forum, $userto);

                    $by->name = fullname($userfrom, isteacher($course->id, $userto->id));
                    $by->date = userdate($post->modified, "", $userto->timezone);
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
                    $posttext .= format_text_email($post->message, $post->format);
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
                        $posthtml = "<p><font face=\"sans-serif\">".
                        "<a target=\"_blank\" href=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</a> -> ".
                        "<a target=\"_blank\" href=\"$CFG->wwwroot/mod/forum/index.php?id=$course->id\">$strforums</a> -> ".
                        "<a target=\"_blank\" href=\"$CFG->wwwroot/mod/forum/view.php?f=$forum->id\">$forum->name</a>";
                        if ($discussion->name == $forum->name) {
                            $posthtml .= "</font></p>";
                        } else {
                            $posthtml .= " -> <a target=\"_blank\" href=\"$CFG->wwwroot/mod/forum/discuss.php?d=$discussion->id\">$discussion->name</a></font></p>";
                        }
                        $posthtml .= forum_make_mail_post($post, $userfrom, $userto, $course, false, $canreply, false, false);

                        if ($canunsubscribe) {
                            $posthtml .= "\n<br /><hr size=\"1\" noshade /><p align=\"right\"><font size=\"1\"><a href=\"$CFG->wwwroot/mod/forum/subscribe.php?id=$forum->id\">".get_string("unsubscribe", "forum")."</a></font></p>";
                        }

                    } else {
                      $posthtml = "";
                    }

                    if (! email_to_user($userto, $userfrom, $postsubject, $posttext, $posthtml)) {
                        echo "Error: mod/forum/cron.php: Could not send out mail for id $post->id to user $userto->id ($userto->email) .. not trying again.\n";
                        add_to_log($course->id, 'forum', 'mail error', "discuss.php?d=$discussion->id#$post->id", substr($post->subject,0,15), $cm->id, $userto->id);
                        $errorcount++;
                    } else {
                        $mailcount++;
                    }
                }

                echo ".... mailed to $mailcount users.\n";
                if ($errorcount) {
                    set_field("forum_posts", "mailed", "2", "id", "$post->id");
                }
            }
        }
    }

    if (!empty($realuser)) {   // Restore real USER if necessary
        $USER = $realuser;
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
            forum_print_post($post, $course->id, $ownpost=false, $reply=false, $link=false, $rate=false);
        }

    } else {
        echo "<P>".get_string("noposts", "forum")."</P>";
    }
}

function forum_print_recent_activity($course, $isteacher, $timestart) {
/// Given a course and a date, prints a summary of all the new
/// messages posted in the course since that date

    global $CFG;

    $heading = false;
    $content = false;

    if (!$logs = get_records_select("log", "time > '$timestart' AND ".
                                           "course = '$course->id' AND ".
                                           "module = 'forum' AND ".
                                           "action LIKE 'add %' ", "time ASC")){
        return false;
    }

    $strftimerecent = get_string("strftimerecent");

    $isteacheredit = isteacheredit($course->id);
    $mygroupid     = mygroupid($course->id);

    $groupmode = array();   /// To cache group modes

    foreach ($logs as $log) {
        //Get post info, I'll need it later
        $post = forum_get_post_from_log($log);

        //Create a temp valid module structure (course,id)
        $tempmod->course = $log->course;
        $tempmod->id = $post->forum;
        //Obtain the visible property from the instance
        $modvisible = instance_is_visible($log->module, $tempmod);

        //Only if the mod is visible
        if ($modvisible) {
            if ($post) {
                /// Check whether this is for teachers only
                $teacheronly = "";
                if ($forum = get_record("forum", "id", $post->forum)) {
                    if ($forum->type == "teacher") {
                        if ($isteacher) {
                            $teacheronly = "class=\"teacheronly\"";
                        } else {
                            continue;
                        }
                    }
                }
                /// Check whether this is belongs to a discussion in a group that
                /// should NOT be accessible to the current user

                if (!$isteacheredit and $post->groupid != -1) {   /// Editing teachers or open discussions
                    if (!isset($cm[$post->forum])) {
                        $cm[$forum->id] = get_coursemodule_from_instance("forum", $forum->id, $course->id);
                        $groupmode[$forum->id] = groupmode($course, $cm[$forum->id]);
                    }
                    if ($groupmode[$forum->id]) {
                        if ($mygroupid != $post->groupid) {
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
                $fullname = fullname($post, $isteacher);
                echo "<p $teacheronly><font size=1>$date - $fullname<br>";
                echo "\"<a href=\"$CFG->wwwroot/mod/forum/$log->url\">";
                if (!empty($CFG->filterall)) {
                    $post->subject = filter_text("<nolink>$post->subject</nolink>", $course->id);
                }
                if ($log->action == "add discussion") {
                    echo "<b>$post->subject</b>";
                } else {
                    echo "$post->subject";
                }
                echo "</a>\"</font></p>";
            }
        }
    }

    return $content;
}


function forum_grades($forumid) {
/// Must return an array of grades, indexed by user, and a max grade.

    if (!$forum = get_record("forum", "id", $forumid)) {
        return false;
    }
    if (!$forum->assessed) {
        return false;
    }
    $scalemenu = make_grades_menu($forum->scale);

    $currentuser = 0;
    $ratingsuser = array();

    if ($ratings = forum_get_user_grades($forumid)) {
        foreach ($ratings as $rating) {     // Ordered by user
            if ($currentuser and $rating->userid != $currentuser) {
                if (!empty($ratingsuser)) {
                    if ($forum->scale < 0) {
                        $return->grades[$currentuser] = forum_get_ratings_mean(0, $scalemenu, $ratingsuser);
                        $return->grades[$currentuser] .= "<br />".forum_get_ratings_summary(0, $scalemenu, $ratingsuser);
                    } else {
                        $total = 0;
                        $count = 0;
                        foreach ($ratingsuser as $ra) {
                            $total += $ra;
                            $count ++;
                        }
                        $return->grades[$currentuser] = format_float($total/$count, 2);
                    }
                } else {
                    $return->grades[$currentuser] = "";
                }
                $ratingsuser = array();
            }
            $ratingsuser[] = $rating->rating;
            $currentuser = $rating->userid;
        }
        if (!empty($ratingsuser)) {
            if ($forum->scale < 0) {
                $return->grades[$currentuser] = forum_get_ratings_mean(0, $scalemenu, $ratingsuser);
                $return->grades[$currentuser] .= "<br />".forum_get_ratings_summary(0, $scalemenu, $ratingsuser);
            } else {
                $total = 0;
                $count = 0;
                foreach ($ratingsuser as $ra) {
                    $total += $ra;
                    $count ++;
                }
                $return->grades[$currentuser] = format_float((float)$total/(float)$count, 2);
            }
        } else {
            $return->grades[$currentuser] = "";
        }
    } else {
        $return->grades = array();
    }

    if ($forum->scale < 0) {
        $return->maxgrade = "";
    } else {
        $return->maxgrade = $forum->scale;
    }
    return $return;
}

function forum_get_participants($forumid) {
//Returns the users with data in one forum
//(users with records in forum_subscriptions, forum_posts and forum_ratings, students)

    global $CFG;

    //Get students from forum_subscriptions
    $st_subscriptions = get_records_sql("SELECT DISTINCT u.*
                                         FROM {$CFG->prefix}user u,
                                              {$CFG->prefix}forum_subscriptions s
                                         WHERE s.forum = '$forumid' and
                                               u.id = s.userid");
    //Get students from forum_posts
    $st_posts = get_records_sql("SELECT DISTINCT u.*
                                 FROM {$CFG->prefix}user u,
                                      {$CFG->prefix}forum_discussions d,
                                      {$CFG->prefix}forum_posts p
                                 WHERE d.forum = '$forumid' and
                                       p.discussion = d.id and
                                       u.id = p.userid");

    //Get students from forum_ratings
    $st_ratings = get_records_sql("SELECT DISTINCT u.*
                                   FROM {$CFG->prefix}user u,
                                        {$CFG->prefix}forum_discussions d,
                                        {$CFG->prefix}forum_posts p,
                                        {$CFG->prefix}forum_ratings r
                                   WHERE d.forum = '$forumid' and
                                         p.discussion = d.id and
                                         r.post = p.id and
                                         u.id = r.userid");

    //Add st_posts to st_subscriptions
    if ($st_posts) {
        foreach ($st_posts as $st_post) {
            $st_subscriptions[$st_post->id] = $st_post;
        }
    }
    //Add st_ratings to st_subscriptions
    if ($st_ratings) {
        foreach ($st_ratings as $st_rating) {
            $st_subscriptions[$st_rating->id] = $st_rating;
        }
    }
    //Return st_subscriptions array (it contains an array of unique users)
    return ($st_subscriptions);
}

function forum_scale_used ($forumid,$scaleid) {
//This function returns if a scale is being used by one forum

    $return = false;

    $rec = get_record("forum","id","$forumid","scale","-$scaleid");

    if (!empty($rec)  && !empty($scaleid)) {
        $return = true;
    }

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


function forum_search_posts($searchterms, $courseid, $page=0, $recordsperpage=50, &$totalcount) {
/// Returns a list of posts found using an array of search terms
/// eg   word  +word -word
///

    global $CFG;

    if (!isteacher($courseid)) {
        $notteacherforum = "AND f.type <> 'teacher'";

        $forummodule = get_record("modules", "name", "forum");
        $onlyvisible = " AND f.id = cm.instance AND cm.visible = 1 AND cm.module = $forummodule->id";
        $onlyvisibletable = ", {$CFG->prefix}course_modules cm";
    } else {
        $notteacherforum = "";

        $onlyvisible = "";
        $onlyvisibletable = "";
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

    /// Some differences in syntax for PostgreSQL
    if ($CFG->dbtype == "postgres7") {
        $LIKE = "ILIKE";   // case-insensitive
        $NOTLIKE = "NOT ILIKE";   // case-insensitive
        $REGEXP = "~*";
        $NOTREGEXP = "!~*";
    } else {
        $LIKE = "LIKE";
        $NOTLIKE = "NOT LIKE";
        $REGEXP = "REGEXP";
        $NOTREGEXP = "NOT REGEXP";
    }

    $messagesearch = "";
    $subjectsearch = "";


    foreach ($searchterms as $searchterm) {
        if (strlen($searchterm) < 2) {
            continue;
        }
        if ($messagesearch) {
            $messagesearch .= " AND ";
        }
        if ($subjectsearch) {
            $subjectsearch .= " AND ";
        }

        if (substr($searchterm,0,1) == "+") {
            $searchterm = substr($searchterm,1);
            $messagesearch .= " p.message $REGEXP '(^|[^a-zA-Z0-9])$searchterm([^a-zA-Z0-9]|$)' ";
            $subjectsearch .= " p.subject $REGEXP '(^|[^a-zA-Z0-9])$searchterm([^a-zA-Z0-9]|$)' ";
        } else if (substr($searchterm,0,1) == "-") {
            $searchterm = substr($searchterm,1);
            $messagesearch .= " p.message $NOTREGEXP '(^|[^a-zA-Z0-9])$searchterm([^a-zA-Z0-9]|$)' ";
            $subjectsearch .= " p.subject $NOTREGEXP '(^|[^a-zA-Z0-9])$searchterm([^a-zA-Z0-9]|$)' ";
        } else {
            $messagesearch .= " p.message $LIKE '%$searchterm%' ";
            $subjectsearch .= " p.subject $LIKE '%$searchterm%' ";
        }
    }

    $selectsql = "{$CFG->prefix}forum_posts p,
                  {$CFG->prefix}forum_discussions d,
                  {$CFG->prefix}user u,
                  {$CFG->prefix}forum f $onlyvisibletable
             WHERE ($messagesearch OR $subjectsearch)
               AND p.userid = u.id
               AND p.discussion = d.id
               AND d.course = '$courseid'
               AND d.forum = f.id $notteacherforum $onlyvisible";

    $totalcount = count_records_sql("SELECT COUNT(*) FROM $selectsql");

    return get_records_sql("SELECT p.*,u.firstname,u.lastname,u.email,u.picture FROM
                            $selectsql ORDER BY p.modified DESC $limit");
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

        return get_record_sql("SELECT p.*, d.forum, d.groupid, u.firstname, u.lastname, u.email, u.picture
                                 FROM {$CFG->prefix}forum_discussions d,
                                      {$CFG->prefix}forum_posts p,
                                      {$CFG->prefix}user u
                                WHERE p.id = '$log->info'
                                  AND d.id = p.discussion
                                  AND p.userid = u.id
                                  AND u.deleted <> '1'");


    } else if ($log->action == "add discussion") {

        return get_record_sql("SELECT p.*, d.forum, d.groupid, u.firstname, u.lastname, u.email, u.picture
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

function forum_get_firstpost_from_discussion($discussionid) {
/// Given a discussion id, return the first post from the discussion
    global $CFG;

    return get_record_sql("SELECT p.*
                             FROM {$CFG->prefix}forum_discussions d,
                                  {$CFG->prefix}forum_posts p
                            WHERE d.id = '$discussionid'
                              AND d.firstpost = p.id ");
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
                               AND r.post = p.id
                             ORDER by p.userid ");
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

function forum_get_discussions($forum="0", $forumsort="d.timemodified DESC",
                               $user=0, $fullpost=true, $visiblegroups=-1) {
/// Get all discussions in a forum
    global $CFG;

    if ($user) {
        $userselect = " AND u.id = '$user' ";
    } else {
        $userselect = "";
    }

    
    if ($visiblegroups == -1) {
        $groupselect = "";
    } else  {
        $groupselect = " AND (d.groupid = '$visiblegroups' OR d.groupid = '-1') ";
    }

    if (empty($forumsort)) {
        $forumsort = "d.timemodified DESC";
    }
    if (empty($fullpost)) {
        $postdata = "p.id,p.subject,p.modified,p.discussion,p.userid";
    } else {
        $postdata = "p.*";
    }

    return get_records_sql("SELECT $postdata, d.timemodified, d.usermodified,
                                   u.firstname, u.lastname, u.email, u.picture
                              FROM {$CFG->prefix}forum_discussions d,
                                   {$CFG->prefix}forum_posts p,
                                   {$CFG->prefix}user u
                             WHERE d.forum = '$forum'
                               AND p.discussion = d.id
                               AND p.parent = 0
                               AND p.userid = u.id $groupselect $userselect
                          ORDER BY $forumsort");
}



function forum_get_user_discussions($courseid, $userid, $groupid=0) {
/// Get all discussions started by a particular user in a course (or group)
    global $CFG;

    if ($groupid) {
        $groupselect = " AND d.groupid = '$groupid' ";
    } else  {
        $groupselect = "";
    }

    return get_records_sql("SELECT p.*, d.groupid, u.firstname, u.lastname, u.email, u.picture,
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
                               AND d.forum = f.id $groupselect
                          ORDER BY p.created DESC");
}


function forum_subscribed_users($course, $forum, $groupid=0) {
/// Returns list of user objects that are subscribed to this forum
    global $CFG;

    if ($groupid) {
        $grouptables = ", {$CFG->prefix}groups_members g";
        $groupselect = " AND g.groupid = '$groupid' AND u.id = g.userid";
    } else  {
        $grouptables = "";
        $groupselect = "";
    }

    if ($forum->forcesubscribe) {
        if ($course->category) {
            if ($forum->type == "teacher") {
                return get_course_teachers($course->id);  // Only teachers can be subscribed to teacher forums
            } else {
                return get_course_users($course->id);     // Otherwise get everyone in the course
            }
        } else {
            return get_site_users();
        }
    }
    return get_records_sql("SELECT u.id, u.username, u.firstname, u.lastname, u.maildisplay, u.mailformat, u.emailstop,
                                   u.email, u.city, u.country, u.lastaccess, u.lastlogin, u.picture, u.timezone, u.lang
                              FROM {$CFG->prefix}user u,
                                   {$CFG->prefix}forum_subscriptions s $grouptables
                             WHERE s.forum = '$forum->id'
                               AND s.userid = u.id
                               AND u.deleted <> 1  $groupselect
                          ORDER BY u.email ASC");
}

/// OTHER FUNCTIONS ///////////////////////////////////////////////////////////


function forum_get_course_forum($courseid, $type) {
// How to set up special 1-per-course forums
    global $CFG;

    if ($forums = get_records_select("forum", "course = '$courseid' AND type = '$type'", "id ASC")) {
        // There should always only be ONE, but with the right combination of
        // errors there might be more.  In this case, just return the oldest one (lowest ID).
        foreach ($forums as $forum) {
            return $forum;   // ie the first one
        }
    }

    // Doesn't exist, so create one now.
    $forum->course = $courseid;
    $forum->type = "$type";
    switch ($forum->type) {
        case "news":
            $forum->name  = addslashes(get_string("namenews", "forum"));
            $forum->intro = addslashes(get_string("intronews", "forum"));
            $forum->forcesubscribe = 1;
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
            $forum->name  = addslashes(get_string("namesocial", "forum"));
            $forum->intro = addslashes(get_string("introsocial", "forum"));
            $forum->open = 2;   // 0 - no, 1 - posts only, 2 - discuss and post
            $forum->assessed = 0;
            $forum->forcesubscribe = 0;
            break;
        case "teacher":
            $forum->name  = addslashes(get_string("nameteacher", "forum"));
            $forum->intro = addslashes(get_string("introteacher", "forum"));
            $forum->open = 2;   // 0 - no, 1 - posts only, 2 - discuss and post
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
        rebuild_course_cache($courseid);
    }

    return get_record("forum", "id", "$forum->id");
}


function forum_make_mail_post(&$post, $user, $touser, $course,
                              $ownpost=false, $reply=false, $link=false, $rate=false, $footer="") {
// Given the data about a posting, builds up the HTML to display it and
// returns the HTML in a string.  This is designed for sending via HTML email.

    global $THEME, $CFG;

    static $formattedtext;        // Cached version of formatted text for a post
    static $formattedtextid;      // The ID number of the post

    if (empty($formattedtextid) or $formattedtextid != $post->id) {    // Recalculate the formatting
        $formattedtext = format_text($post->message, $post->format, NULL, $course->id);
        $formattedtextid = $post->id;
    }

    $output = "";

    $output .= "<style> <!--";       /// Styles for autolinks
    $output .= "a.autolink:link {text-decoration: none; color: black; background-color: $THEME->autolink}\n";
    $output .= "a.autolink:visited {text-decoration: none; color: black; background-color: $THEME->autolink}\n";
    $output .= "a.autolink:hover {text-decoration: underline; color: red}\n";
    $output .= "--> </style>\n\n";

    $output .= '<table border="0" cellpadding="1" cellspacing="1"><tr><td bgcolor="'.$THEME->borders.'">';
    $output .= '<table border="0" cellpadding="3" cellspacing="0">';

    $output .= "<tr><td bgcolor=\"$THEME->cellcontent2\" width=\"35\" valign=\"top\">";
    $output .= print_user_picture($user->id, $course->id, $user->picture, false, true);
    $output .= "</td>";

    if ($post->parent) {
        $output .= "<td nowrap bgcolor=\"$THEME->cellheading\">";
    } else {
        $output .= "<td nowrap bgcolor=\"$THEME->cellheading2\">";
    }
    $output .= "<p>";
    $output .= "<font size=3><b>$post->subject</b></font><br />";
    $output .= "<font size=2>";

    $fullname = fullname($user, isteacher($course->id));
    $by->name = "<a href=\"$CFG->wwwroot/user/view.php?id=$user->id&course=$course->id\">$fullname</a>";
    $by->date = userdate($post->modified, "", $touser->timezone);
    $output .= get_string("bynameondate", "forum", $by);

    $output .= "</font></p></td></tr>";
    $output .= "<tr><td bgcolor=\"$THEME->cellcontent2\" width=10>";
    $output .= "&nbsp;";
    $output .= "</td><td bgcolor=\"$THEME->cellcontent\">\n";

    if ($post->attachment) {
        $post->course = $course->id;
        $post->forum = get_field("forum_discussions", "forum", "id", $post->discussion);
        $output .= "<div align=right>";
        $output .= forum_print_attachments($post, "html");
        $output .= "</div>";
    }

    $output .= $formattedtext;

    $output .= "<p align=right><font size=-1>";

    if ($post->parent) {
        $output .= "<a href=\"$CFG->wwwroot/mod/forum/discuss.php?d=$post->discussion&parent=$post->parent\">".get_string("parent", "forum")."</a> | ";
    }

    $age = time() - $post->created;
    if ($ownpost) {
        $output .= "<a href=\"$CFG->wwwroot/mod/forum/post.php?delete=$post->id\">".get_string("delete", "forum")."</a>";
        if ($reply) {
            $output .= " | <a target=\"_blank\" href=\"$CFG->wwwroot/mod/forum/post.php?reply=$post->id\">".get_string("replyforum", "forum")."</a>";
        }
        $output .= "&nbsp;&nbsp;";
    } else {
        if ($reply) {
            $output .= "<a target=\"_blank\" href=\"$CFG->wwwroot/mod/forum/post.php?reply=$post->id\">".get_string("replyforum", "forum")."</a>&nbsp;&nbsp;";
        }
    }

    $output .= "</p>";
    $output .= "<div align=right><p align=right>";

    if ($link) {
        if ($post->replies == 1) {
            $replystring = get_string("repliesone", "forum", $post->replies);
        } else {
            $replystring = get_string("repliesmany", "forum", $post->replies);
        }
        $output .= "<a href=\"$CFG->wwwroot/mod/forum/discuss.php?d=$post->discussion\"><B>".get_string("discussthistopic", "forum")."</b></a> ($replystring)&nbsp;&nbsp;";
    }
    $output .= "</p></div>";
    if ($footer) {
        $output .= "<p>$footer</p>";
    }
    $output .= "</td></tr></table>\n";
    $output .= "</td></tr></table>\n\n";

    return $output;
}


function forum_print_post(&$post, $courseid, $ownpost=false, $reply=false, $link=false,
                          $ratings=NULL, $footer="", $highlight="") {

    global $THEME, $USER, $CFG;

    static $stredit, $strdelete, $strreply, $strparent, $threadedmode, $isteacher, $adminedit;

    if (empty($stredit)) {
        $stredit = get_string("edit", "forum");
        $strdelete = get_string("delete", "forum");
        $strreply = get_string("reply", "forum");
        $strparent = get_string("parent", "forum");
        $threadedmode = (!empty($USER->mode) and ($USER->mode == FORUM_MODE_THREADED));
        $isteacher = isteacher($courseid);
        $adminedit = (isadmin() and !empty($CFG->admineditalways));
    }

    echo "<a name=\"$post->id\"></a>";
    if ($post->parent) {
        echo '<table border="0" cellpadding="3" cellspacing="0" class="forumpost">';
    } else {
        echo '<table border="0" cellpadding="3" cellspacing="0" class="forumpost" width="100%">';
    }

    echo "<tr><td bgcolor=\"$THEME->cellcontent2\" class=\"forumpostpicture\" width=\"35\" valign=\"top\">";
    print_user_picture($post->userid, $courseid, $post->picture);
    echo "</td>";

    if ($post->parent) {
        echo "<td bgcolor=\"$THEME->cellheading\" class=\"forumpostheader\" width=\"100%\">";
    } else {
        echo "<td bgcolor=\"$THEME->cellheading2\" class=\"forumpostheadertopic\" width=\"100%\">";
    }

    if (!empty($CFG->filterall)) {      /// Put the subject through the filters
        $post->subject = filter_text("<nolink>$post->subject</nolink>", $courseid);
    }
    echo "<p>";
    echo "<font size=3><b>$post->subject</b></font><br />";
    echo "<font size=2>";

    $fullname = fullname($post, $isteacher);
    $by->name = "<a href=\"$CFG->wwwroot/user/view.php?id=$post->userid&course=$courseid\">$fullname</a>";
    $by->date = userdate($post->modified);
    print_string("bynameondate", "forum", $by);

    echo "</font></p></td></tr>";
    echo "<tr><td bgcolor=\"$THEME->cellcontent2\" valign=\"top\" class=\"forumpostside\" width=\"10\">";
    if ($group = user_group($courseid, $post->userid)) {
        print_group_picture($group, $courseid, false, false, false);
    } else {
        echo "&nbsp;";
    }
    echo "</td><td bgcolor=\"$THEME->cellcontent\" class=\"forumpostmessage\">\n";

    if ($post->attachment) {
        $post->course = $courseid;
        $post->forum = get_field("forum_discussions", "forum", "id", $post->discussion);
        echo "<div align=\"right\">";
        $attachedimages = forum_print_attachments($post);
        echo "</div>";
    } else {
        $attachedimages = "";
    }

    if ($link and (strlen(strip_tags($post->message)) > $CFG->forum_longpost)) {
        // Print shortened version
        echo format_text(forum_shorten_post($post->message), $post->format, NULL, $courseid);
        $numwords = count_words(strip_tags($post->message));
        echo "<p><a href=\"$CFG->wwwroot/mod/forum/discuss.php?d=$post->discussion\">";
        echo get_string("readtherest", "forum");
        echo "</a> (".get_string("numwords", "", $numwords).")...</p>";
    } else {
        // Print whole message
        if ($highlight) {
            echo highlight($highlight, format_text($post->message, $post->format, NULL, $courseid));
        } else {
            echo format_text($post->message, $post->format, NULL, $courseid);
        }
        echo $attachedimages;
    }

    echo "<p align=right><font size=-1>";

    if ($post->parent) {
        if ($threadedmode) {
            echo "<a href=\"$CFG->wwwroot/mod/forum/discuss.php?d=$post->discussion&parent=$post->parent\">$strparent</a> | ";
        } else {
            echo "<a href=\"$CFG->wwwroot/mod/forum/discuss.php?d=$post->discussion#$post->parent\">$strparent</a> | ";
        }
    }

    $age = time() - $post->created;
    if ($ownpost or $adminedit) {
        if (($age < $CFG->maxeditingtime) or $adminedit) {
            echo "<a href=\"$CFG->wwwroot/mod/forum/post.php?edit=$post->id\">$stredit</a> | ";
        }
    }
    if ($ownpost or $isteacher) {
        echo "<a href=\"$CFG->wwwroot/mod/forum/post.php?delete=$post->id\">$strdelete</a>";
        if ($reply) {
            echo "| ";
        } else {
            echo "&nbsp;&nbsp;";
        }
    }
    if ($reply) {
        echo "<a href=\"$CFG->wwwroot/mod/forum/post.php?reply=$post->id\">$strreply</a>";
        echo "&nbsp;&nbsp;";
    }
    echo "</p>";

    echo "<div align=right><p align=right>";

    $ratingsmenuused = false;
    if (!empty($ratings) and !empty($USER->id)) {
        $useratings = true;
        if ($ratings->assesstimestart and $ratings->assesstimefinish) {
            if ($post->created < $ratings->assesstimestart or $post->created > $ratings->assesstimefinish) {
                $useratings = false;
            }
        }
        if ($useratings) {
            $mypost = ($USER->id == $post->userid);

            if (($isteacher or $ratings->assesspublic) and !$mypost) {
                forum_print_ratings_mean($post->id, $ratings->scale, $isteacher);
                if (!empty($ratings->allow)) {
                    forum_print_rating_menu($post->id, $USER->id, $ratings->scale);
                    $ratingsmenuused = true;
                }

            } else if ($mypost) {
                forum_print_ratings_mean($post->id, $ratings->scale, true);

            } else if (!empty($ratings->allow) ) {
                forum_print_rating_menu($post->id, $USER->id, $ratings->scale);
                $ratingsmenuused = true;
            }
        }
    }

    if ($link) {
        if ($post->replies == 1) {
            $replystring = get_string("repliesone", "forum", $post->replies);
        } else {
            $replystring = get_string("repliesmany", "forum", $post->replies);
        }
        echo "<a href=\"$CFG->wwwroot/mod/forum/discuss.php?d=$post->discussion\"><b>".
             get_string("discussthistopic", "forum")."</b></a> ($replystring)&nbsp;&nbsp;";
    }
    echo "</p>";
    if ($footer) {
        echo "<p>$footer</p>";
    }
    echo "</div>";
    echo "</td></tr>\n</table>\n\n";

    return $ratingsmenuused;
}


function forum_print_discussion_header(&$post, $forum, $datestring="") {
    global $THEME, $USER, $CFG;

    if (!empty($CFG->filterall)) {
        $post->subject = filter_text("<nolink>$post->subject</nolink>", $forum->course);
    }

    echo "<tr class=\"forumpostheader\">";

    // Topic
    echo "<td bgcolor=\"$THEME->cellheading2\" class=\"forumpostheadertopic\" width=\"100%\">";
    echo "<a href=\"$CFG->wwwroot/mod/forum/discuss.php?d=$post->discussion\">$post->subject</a>";
    echo "</td>\n";

    // Picture
    echo "<td bgcolor=\"$THEME->cellcontent2\" class=\"forumpostheaderpicture\" width=35>";
    print_user_picture($post->userid, $forum->course, $post->picture);
    echo "</td>\n";

    // User name
    $fullname = fullname($post, isteacher($forum->course));
    echo "<td bgcolor=\"$THEME->cellcontent2\" class=\"forumpostheadername\" align=left nowrap>";
    echo "<a href=\"$CFG->wwwroot/user/view.php?id=$post->userid&course=$forum->course\">$fullname</a>";
    echo "</td>\n";

    if ($forum->open or $forum->type == "teacher") {   // Show the column with replies
        echo "<td bgcolor=\"$THEME->cellcontent2\" class=\"forumpostheaderreplies\" align=center nowrap>";
        echo "<a href=\"$CFG->wwwroot/mod/forum/discuss.php?d=$post->discussion\">$post->replies</a>";
        echo "</td>\n";
    }

    echo "<td bgcolor=\"$THEME->cellcontent2\" class=\"forumpostheaderdate\" align=right nowrap>";
    if (!empty($post->timemodified)) {
        echo userdate($post->timemodified, $datestring);
    } else {
        echo userdate($post->modified, $datestring);
    }
    echo "</td>\n";

    echo "</tr>\n";

}


function forum_shorten_post($message) {
// Given a post object that we already know has a long message
// this function truncates the message nicely to the first
// sane place between $CFG->forum_longpost and $CFG->forum_shortpost

   global $CFG;

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
           if ($count > $CFG->forum_shortpost) {
               $stopzone = true;
           }
       }
   }

   if (!$truncate) {
       $truncate = $i;
   }

   return substr($message, 0, $truncate);
}


function forum_print_ratings_mean($postid, $scale, $link=true) {
/// Print the multiple ratings on a post given to the current user by others.
/// Scale is an array of ratings

    static $strrate;

    $mean = forum_get_ratings_mean($postid, $scale);

    if ($mean !== "") {

        if (empty($strratings)) {
            $strratings = get_string("ratings", "forum");
        }

        echo "$strratings: ";
        if ($link) {
            link_to_popup_window ("/mod/forum/report.php?id=$postid", "ratings", $mean, 400, 600);
        } else {
            echo "$mean ";
        }
    }
}


function forum_get_ratings_mean($postid, $scale, $ratings=NULL) {
/// Return the mean rating of a post given to the current user by others.
/// Scale is an array of possible ratings in the scale
/// Ratings is an optional simple array of actual ratings (just integers)

    if (!$ratings) {
        $ratings = array();
        if ($rates = get_records("forum_ratings", "post", $postid)) {
            foreach ($rates as $rate) {
                $ratings[] = $rate->rating;
            }
        }
    }

    $count = count($ratings);

    if ($count == 0) {
        return "";

    } else if ($count == 1) {
        return $scale[$ratings[0]];

    } else {
        $total = 0;
        foreach ($ratings as $rating) {
            $total += $rating;
        }
        $mean = round( ((float)$total/(float)$count) + 0.001);  // Little fudge factor so that 0.5 goes UP

        if (isset($scale[$mean])) {
            return $scale[$mean]." ($count)";
        } else {
            return "$mean ($count)";    // Should never happen, hopefully
        }
    }
}

function forum_get_ratings_summary($postid, $scale, $ratings=NULL) {
/// Return a summary of post ratings given to the current user by others.
/// Scale is an array of possible ratings in the scale
/// Ratings is an optional simple array of actual ratings (just integers)

    if (!$ratings) {
        $ratings = array();
        if ($rates = get_records("forum_ratings", "post", $postid)) {
            foreach ($rates as $rate) {
                $rating[] = $rate->rating;
            }
        }
    }


    if (!$count = count($ratings)) {
        return "";
    }


    foreach ($scale as $key => $scaleitem) {
        $sumrating[$key] = 0;
    }

    foreach ($ratings as $rating) {
        $sumrating[$rating]++;
    }

    $summary = "";
    foreach ($scale as $key => $scaleitem) {
        $summary = $sumrating[$key].$summary;
        if ($key > 1) {
            $summary = "/$summary";
        }
    }
    return $summary;
}

function forum_print_rating_menu($postid, $userid, $scale) {
/// Print the menu of ratings as part of a larger form.
/// If the post has already been - set that value.
/// Scale is an array of ratings

    static $strrate;

    if (!$rating = get_record("forum_ratings", "userid", $userid, "post", $postid)) {
        $rating->rating = 0;
    }

    if (empty($strrate)) {
        $strrate = get_string("rate", "forum");
    }

    choose_from_menu($scale, $postid, $rating->rating, "$strrate...");
}

function forum_print_mode_form($discussion, $mode) {
    GLOBAL $FORUM_LAYOUT_MODES;

    echo "<center><p>";
    popup_form("discuss.php?d=$discussion&mode=", $FORUM_LAYOUT_MODES, "mode", $mode, "");
    echo "</p></center>\n";
}

function forum_print_search_form($course, $search="", $return=false, $type="") {
    global $CFG;

    if ($type == "plain") {
        $output = "<table border=0 cellpadding=0 cellspacing=0><tr><td nowrap>";
        $output .= "<form name=search action=\"$CFG->wwwroot/mod/forum/search.php\">";
        $output .= "<font size=\"-1\">";
        $output .= "<input name=search type=text size=15 value=\"$search\">";
        $output .= "<input value=\"".get_string("searchforums", "forum")."\" type=submit>";
        $output .= "</font>";
        $output .= "<input name=id type=hidden value=\"$course->id\">";
        $output .= "</form>";
        $output .= "</td></tr></table>";
    } else {
        $output = "<table border=0 cellpadding=10 cellspacing=0><tr><td align=center>";
        $output .= "<form name=search action=\"$CFG->wwwroot/mod/forum/search.php\">";
        $output .= "<font size=\"-1\">";
        $output .= "<input name=search type=text size=15 value=\"$search\"><br>";
        $output .= "<input value=\"".get_string("searchforums", "forum")."\" type=submit>";
        $output .= "</font>";
        $output .= "<input name=id type=hidden value=\"$course->id\">";
        $output .= "</form>";
        $output .= "</td></tr></table>";
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

function forum_move_attachments($discussion, $forumid) {
/// Given a discussion object that is being moved to forumid,
/// this function checks all posts in that discussion
/// for attachments, and if any are found, these are
/// moved to the new forum directory.

    global $CFG;

    $return = true;

    if ($posts = get_records_select("forum_posts", "discussion = '$discussion->id' AND attachment <> ''")) {
        foreach ($posts as $oldpost) {
            $oldpost->course = $discussion->course;
            $oldpost->forum = $discussion->forum;
            $oldpostdir = "$CFG->dataroot/".forum_file_area_name($oldpost);
            if (is_dir($oldpostdir)) {
                $newpost = $oldpost;
                $newpost->forum = $forumid;
                $newpostdir = forum_file_area($newpost);
                if (! @rename($oldpostdir, $newpostdir)) {
                    $return = false;
                }
            }
        }
    }
    return $return;
}

function forum_print_attachments($post, $return=NULL) {
// if return=html, then return a html string.
// if return=text, then return a text-only string.
// otherwise, print HTML for non-images, and return image HTML

    global $CFG;

    $filearea = forum_file_area_name($post);

    $imagereturn = "";
    $output = "";

    if ($basedir = forum_file_area($post)) {
        if ($files = get_directory_list($basedir)) {
            $strattachment = get_string("attachment", "forum");
            $strpopupwindow = get_string("popupwindow");
            foreach ($files as $file) {
                $icon = mimeinfo("icon", $file);
                if ($CFG->slasharguments) {
                    $ffurl = "file.php/$filearea/$file";
                } else {
                    $ffurl = "file.php?file=/$filearea/$file";
                }
                $image = "<img border=\"0\" src=\"$CFG->pixpath/f/$icon\" height=\"16\" width=\"16\" alt=\"$strpopupwindow\">";

                if ($return == "html") {
                    $output .= "<a href=\"$CFG->wwwroot/$ffurl\">$image</a> ";
                    $output .= "<a href=\"$CFG->wwwroot/$ffurl\">$file</a><br />";

                } else if ($return == "text") {
                    $output .= "$strattachment $file:\n$CFG->wwwroot/$ffurl\n";

                } else {
                    if ($icon == "image.gif") {    // Image attachments don't get printed as links
                        $imagereturn .= "<br /><img src=\"$CFG->wwwroot/$ffurl\">";
                    } else {
                        link_to_popup_window("/$ffurl", "attachment", $image, 500, 500, $strattachment);
                        echo "<a href=\"$CFG->wwwroot/$ffurl\">$file</a>";
                        echo "<br />";
                    }
                }
            }
        }
    }

    if ($return) {
        return $output;
    }

    return $imagereturn;
}

function forum_add_attachment($post, $newfile) {
// $post is a full post record, including course and forum
// $newfile is a full upload array from $_FILES
// If successful, this function returns the name of the file

    global $CFG;

    if (empty($newfile['name'])) {
        return "";
    }

    if (!$forum = get_record("forum", "id", $post->forum)) {
        return "";
    }

    if (!$course = get_record("course", "id", $forum->course)) {
        return "";
    }

    $maxbytes = get_max_upload_file_size($CFG->maxbytes, $course->maxbytes, $forum->maxbytes);

    $newfile_name = clean_filename($newfile['name']);

    if (valid_uploaded_file($newfile)) {
        if ($maxbytes and $newfile['size'] > $maxbytes) {
            return "";
        }
        if (! $newfile_name) {
            notify("This file had a wierd filename and couldn't be uploaded");

        } else if (! $dir = forum_file_area($post)) {
            notify("Attachment could not be stored");
            $newfile_name = "";

        } else {
            if (move_uploaded_file($newfile['tmp_name'], "$dir/$newfile_name")) {
                chmod("$dir/$newfile_name", $CFG->directorypermissions);
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

    // Update discussion modified date
    set_field("forum_discussions", "timemodified", $post->modified, "id", $post->discussion);
    set_field("forum_discussions", "usermodified", $post->userid, "id", $post->discussion);

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

    // Update discussion modified date
    set_field("forum_discussions", "timemodified", $post->modified, "id", $post->discussion);
    set_field("forum_discussions", "usermodified", $post->userid, "id", $post->discussion);

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
    $post->format      = $discussion->format;

    if (! $post->id = insert_record("forum_posts", $post) ) {
        return 0;
    }

    if ($post->attachment = forum_add_attachment($post, $discussion->attachment)) {
        set_field("forum_posts", "attachment", $post->attachment, "id", $post->id); //ignore errors
    }

    // Now do the main entry for the discussion,
    // linking to this first post

    $discussion->firstpost    = $post->id;
    $discussion->timemodified = $timenow;
    $discussion->usermodified = $post->userid;

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


function forum_print_user_discussions($courseid, $userid, $groupid=0) {
    global $CFG, $USER;

    $maxdiscussions = 10;
    $countdiscussions = 0;

    $visible = array();

    $course = get_record("course", "id", $courseid);

    $currentgroup = get_current_group($courseid);
    $isteacheredit = isteacheredit($courseid);

    if ($discussions = forum_get_user_discussions($courseid, $userid, $groupid=0)) {

        $user    = get_record("user", "id", $userid);
        $fullname = fullname($user, isteacher($courseid));

        $replies = forum_count_discussion_replies();

        echo "<hr />";

        print_heading( get_string("discussionsstartedbyrecent", "forum", $fullname) );

        foreach ($discussions as $discussion) {
            $countdiscussions++;
            if ($countdiscussions > $maxdiscussions) {
                break;
            }
            if (($discussion->forumtype == "teacher") and !isteacher($courseid)) {
                continue;
            }
            if(!isset($visible[$discussion->forumid])) {
                $mod = New stdClass;
                $mod->course = $courseid;
                $mod->id = $discussion->forumid;
                $visible[$discussion->forumid] = instance_is_visible('forum', $mod);
            }
            if(!$visible[$discussion->forumid] && !isteacheredit($courseid, $USER->id)) {
                continue;
            }

            /// Check whether this is belongs to a discussion in a group that
            /// should NOT be accessible to the current user

            if (!$isteacheredit and $discussion->groupid != -1) {   /// Editing teachers or open discussions
                if (!isset($cm[$discussion->forum])) {
                    $cm[$discussion->forum] = get_coursemodule_from_instance("forum", $discussion->forum, $courseid);
                    $groupmode[$discussion->forum] = groupmode($course, $cm[$discussion->forum]);
                }
                if ($groupmode[$discussion->forum] == SEPARATEGROUPS) {
                    if ($currentgroup != $discussion->groupid) {
                        continue;
                    }
                }
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

    if (record_exists("forum_subscriptions", "userid", $userid, "forum", $forumid)) {
        return true;
    }

    $sub->userid  = $userid;
    $sub->forum = $forumid;

    return insert_record("forum_subscriptions", $sub);
}

function forum_unsubscribe($userid, $forumid) {
/// Removes user from the subscriber list
    return delete_records("forum_subscriptions", "userid", $userid, "forum", $forumid);
}

function forum_post_subscription($post) {
/// Given a new post, subscribes or unsubscribes as appropriate.
/// Returns some text which describes what happened.

    global $USER;

    if (empty($post->subscribe) and empty($post->unsubscribe)) {
        return "";
    }

    if (!$forum = get_record("forum", "id", $post->forum)) {
        return "";
    }

    $info->name  = "$USER->firstname $USER->lastname";
    $info->forum = $forum->name;

    if (!empty($post->subscribe)) {
        forum_subscribe($USER->id, $post->forum);
        return "<p>".get_string("nowsubscribed", "forum", $info)."</p>";
    }

    forum_unsubscribe($USER->id, $post->forum);
    return "<p>".get_string("nownotsubscribed", "forum", $info)."</p>";
}


function forum_user_has_posted_discussion($forumid, $userid) {
    if ($discussions = forum_get_discussions($forumid, "", $userid)) {
        return true;
    } else {
        return false;
    }
}

function forum_user_can_post_discussion($forum, $currentgroup=false) {
// $forum is an object
    global $USER;

    if ($forum->type == "eachuser") {
        return (! forum_user_has_posted_discussion($forum->id, $USER->id));
    } else if ($forum->type == "teacher") {
        return isteacher($forum->course);
    } else if ($currentgroup) {
        return (isteacheredit($forum->course) or (ismember($currentgroup) and $forum->open == 2));
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


function forum_print_latest_discussions($forum_id=0, $forum_numdiscussions=5,
                                        $forum_style="plain", $forum_sort="",
                                        $currentgroup=0, $groupmode=-1) {
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

    if ($groupmode == -1) {    /// We need to reconstruct groupmode because none was given
        if ($cm = get_coursemodule_from_instance("forum", $forum->id, $course->id)) {
            $groupmode = groupmode($course, $cm);
        } else {
            $groupmode = SEPARATEGROUPS;
        }
    }
    

    if (forum_user_can_post_discussion($forum, $currentgroup)) {
        echo "<p align=center>";
        echo "<a href=\"$CFG->wwwroot/mod/forum/post.php?forum=$forum->id\">";
        if ($forum->type == "news") {
            echo get_string("addanewtopic", "forum")."</a>...";
        } else {
            echo get_string("addanewdiscussion", "forum")."</a>...";
        }
        echo "</p>\n";
    }

    if ((!$forum_numdiscussions) && ($forum_style == "plain")) {
        $forum_style = "header";  // Abbreviate display by default
    }

    if ($forum_style == "minimal") {
        $forum_sort = "p.modified DESC";
    }

    $fullpost = false;
    if ($forum_style == "plain") {
        $fullpost = true;
    }


/// Decides if current user is allowed to see ALL the current discussions or not

    if (!$currentgroup and ($groupmode != SEPARATEGROUPS or isteacheredit($forum->course)) ) {
        $visiblegroups = -1;
    } else {
        $visiblegroups = $currentgroup;
    }

/// Get all the recent discussions we're allowed to see

    if (! $discussions = forum_get_discussions($forum->id, $forum_sort, 0, $fullpost, $visiblegroups) ) {
        if ($forum->type == "news") {
            echo "<p align=center><b>(".get_string("nonews", "forum").")</b></p>";
        } else {
            echo "<p align=center><b>(".get_string("nodiscussions", "forum").")</b></p>";
        }
        return;
    }

    $replies = forum_count_discussion_replies($forum->id);

    $canreply = forum_user_can_post($forum);

    $discussioncount = 0;
    $olddiscussionlink = false;
    $strdatestring = get_string("strftimedaydatetime");

    if ($forum_style == "minimal") {
        $strftimerecent = get_string("strftimerecent");
        $strmore = get_string("more", "forum");
    }

    if ($forum_style == "header") {
        echo "<table width=\"100%\" border=0 cellpadding=3 cellspacing=1 class=\"forumheaderlist\">";
        echo "<tr class=\"forumpostheader\">";
        echo "<th>".get_string("discussion", "forum")."</th>";
        echo "<th colspan=2>".get_string("startedby", "forum")."</th>";
        if ($forum->open or $forum->type == "teacher") {
            echo "<th>".get_string("replies", "forum")."</th>";
        }
        echo "<th>".get_string("lastpost", "forum")."</th>";
        echo "</tr>";
    }

    foreach ($discussions as $discussion) {
        $discussioncount++;

        if ($forum_numdiscussions && ($discussioncount > $forum_numdiscussions)) {
            $olddiscussionlink = true;
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
                if (!empty($CFG->filterall)) {
                    $discussion->subject = filter_text($discussion->subject, $forum->course);
                }
                echo "<p><span class=\"smallinfohead\">".
                     userdate($discussion->modified, $strftimerecent).
                     " - ".
                     fullname($discussion).
                     "</span><br />";
                echo "<span class=\"smallinfo\">$discussion->subject ";
                echo "<a href=\"$CFG->wwwroot/mod/forum/discuss.php?d=$discussion->discussion\">";
                echo $strmore."...</a></span>";
                echo "</p>\n";
            break;
            case "header":
                forum_print_discussion_header($discussion, $forum, $strdatestring);
            break;
            default:
                if ($canreply or $discussion->replies) {
                    $link = true;
                } else {
                    $link = false;
                }
                forum_print_post($discussion, $forum->course, $ownpost, $reply=0, $link, $assessed=false);
                echo "<br>\n";
            break;
        }
    }

    if ($forum_style == "header") {
        echo "</table>";
    }

    if ($olddiscussionlink) {
        if ($forum_style == "minimal") {
            echo '<p align="center">';
        } else {
            echo '<p align="right">';
        }
        echo "<a href=\"$CFG->wwwroot/mod/forum/view.php?f=$forum->id&showall=1\">";
        echo get_string("olderdiscussions", "forum")."</a> ...</p>";
    }
}

function forum_print_discussion($course, $forum, $discussion, $post, $mode, $canreply=NULL) {

    global $USER;

    if (!empty($USER->id)) {
        $ownpost = ($USER->id == $post->userid);
    } else {
        $ownpost = false;
    }
    if ($canreply === NULL) {
        $reply = forum_user_can_post($forum);
    } else {
        $reply = $canreply;
    }

    $ratings = NULL;
    $ratingsmenuused = false;
    if ($forum->assessed and !empty($USER->id)) {
        if ($ratings->scale = make_grades_menu($forum->scale)) {
            $ratings->assesspublic = $forum->assesspublic;
            $ratings->assesstimestart = $forum->assesstimestart;
            $ratings->assesstimefinish = $forum->assesstimefinish;
            $ratings->allow = ($forum->assessed != 2 or isteacher($course->id));

            echo "<form name=form method=post action=rate.php>";
            echo "<input type=hidden name=id value=\"$course->id\">";
        }
    }

    if (forum_print_post($post, $course->id, $ownpost, $reply, $link=false, $ratings)) {
        $ratingsmenuused = true;
    }

    switch ($mode) {
        case FORUM_MODE_FLATOLDEST :
        case FORUM_MODE_FLATNEWEST :
        default:
            echo "<ul>";
            if (forum_print_posts_flat($post->discussion, $course->id, $mode, $ratings, $reply)) {
                $ratingsmenuused = true;
            }
            echo "</ul>";
            break;

        case FORUM_MODE_THREADED :
            if (forum_print_posts_threaded($post->id, $course->id, 0, $ratings, $reply)) {
                $ratingsmenuused = true;
            }
            break;

        case FORUM_MODE_NESTED :
            if (forum_print_posts_nested($post->id, $course->id, $ratings, $reply)) {
                $ratingsmenuused = true;
            }
            break;
    }

    if ($ratingsmenuused) {
        echo "<center><input type=\"submit\" value=\"".get_string("sendinratings", "forum")."\">";
        if ($forum->scale < 0) {
            if ($scale = get_record("scale", "id", abs($forum->scale))) {
                print_scale_menu_helpbutton($course->id, $scale );
            }
        }
        echo "</center>";
        echo "</form>";
    }
}

function forum_print_posts_flat($discussion, $course, $direction, $ratings, $reply) {
    global $USER;

    $link  = false;
    $ratingsmenuused = false;

    if ($direction < 0) {
        $sort = "ORDER BY created DESC";
    } else {
        $sort = "ORDER BY created ASC";
    }

    if ($posts = forum_get_discussion_posts($discussion, $sort)) {
        foreach ($posts as $post) {
            $ownpost = ($USER->id == $post->userid);
            if (forum_print_post($post, $course, $ownpost, $reply, $link, $ratings)) {
                $ratingsmenuused = true;
            }
        }
    }

    return $ratingsmenuused;
}

function forum_print_posts_threaded($parent, $course, $depth, $ratings, $reply) {
    global $USER;

    $link  = false;
    $ratingsmenuused = false;

    if ($posts = forum_get_child_posts($parent)) {
        foreach ($posts as $post) {

            echo "<ul>";
            if ($depth > 0) {
                $ownpost = ($USER->id == $post->userid);
                if (forum_print_post($post, $course, $ownpost, $reply, $link, $ratings)) {
                    $ratingsmenuused = true;
                }
                echo "<br />";
            } else {
                $by->name = fullname($post, isteacher($course->id));
                $by->date = userdate($post->modified);
                echo "<li><p><a name=\"$post->id\"></a><font size=-1><b><a href=\"discuss.php?d=$post->discussion&parent=$post->id\">$post->subject</a></b> ";
                print_string("bynameondate", "forum", $by);
                echo "</font></p></li>";
            }

            if (forum_print_posts_threaded($post->id, $course, $depth-1, $ratings, $reply)) {
                $ratingsmenuused = true;
            }
            echo "</ul>\n";
        }
    }
    return $ratingsmenuused;
}

function forum_print_posts_nested($parent, $course, $ratings, $reply) {
    global $USER;

    $link  = false;
    $ratingsmenuused = false;

    if ($posts = forum_get_child_posts($parent)) {
        foreach ($posts as $post) {

            if (empty($USER->id)) {
                $ownpost = false;
            } else {
                $ownpost = ($USER->id == $post->userid);
            }

            echo "<ul>";
            if (forum_print_post($post, $course, $ownpost, $reply, $link, $ratings)) {
                $ratingsmenuused = true;
            }
            echo "<br />";
            if (forum_print_posts_nested($post->id, $course, $ratings, $reply)) {
                $ratingsmenuused = true;
            }
            echo "</ul>\n";
        }
    }
    return $ratingsmenuused;
}

function forum_get_recent_mod_activity(&$activities, &$index, $sincetime, $courseid, $cmid="0", $user="", $groupid="") {
// Returns all forum posts since a given time.  If forum is specified then
// this restricts the results

    global $CFG;

    if ($cmid) {
        $forumselect = " AND cm.id = '$cmid'";
    } else {
        $forumselect = "";
    }

    if ($user) {
        $userselect = " AND u.id = '$user'";
    } else {
        $userselect = "";
    }

    $posts = get_records_sql("SELECT p.*, d.name, u.firstname, u.lastname,
                                     u.picture, d.groupid, cm.instance, f.name, cm.section
                               FROM {$CFG->prefix}forum_posts p,
                                    {$CFG->prefix}forum_discussions d,
                                    {$CFG->prefix}user u,
                                    {$CFG->prefix}course_modules cm,
                                    {$CFG->prefix}forum f
                              WHERE p.modified > '$sincetime' $forumselect
                                AND p.userid = u.id $userselect
                                AND d.course = '$courseid'
                                AND p.discussion = d.id 
                                AND cm.instance = f.id
                                AND cm.course = d.course
                                AND cm.course = f.course
                                AND f.id = d.forum
                              ORDER BY d.id");

    if (empty($posts)) {
        return;
    }

    $isteacheredit = isteacheredit($courseid);

    foreach ($posts as $post) {

        if ($groupid and ($post->groupid != -1 and $groupid != $post->groupid and !$isteacheredit)) {
            continue;
        }

        $tmpactivity->type = "forum";
        $tmpactivity->defaultindex = $index;
        $tmpactivity->instance = $post->instance;
        $tmpactivity->name = $post->name;
        $tmpactivity->section = $post->section;

        $tmpactivity->content->id = $post->id;
        $tmpactivity->content->discussion = $post->discussion;
        $tmpactivity->content->subject = $post->subject;
        $tmpactivity->content->parent = $post->parent;

        $tmpactivity->user->userid = $post->userid;
        $tmpactivity->user->fullname = fullname($post);
        $tmpactivity->user->picture = $post->picture;

        $tmpactivity->timestamp = $post->modified;
        $activities[] = $tmpactivity;

        $index++;
    }

    return;
}

function forum_print_recent_mod_activity($activity, $course, $detail=false) {

    global $CFG;

    echo '<table border="0" cellpadding="3" cellspacing="0">';

    if ($activity->content->parent) {
        $openformat = "<font size=\"2\"><i>";
        $closeformat = "</i></font>";
    } else {
        $openformat = "<b>";
        $closeformat = "</b>";
    }

    echo "<tr><td bgcolor=\"$THEME->cellcontent2\" class=\"forumpostpicture\" width=\"35\" valign=\"top\">";
    print_user_picture($activity->user->userid, $course, $activity->user->picture);
    echo "</td><td>$openformat";

    if ($detail) {
        echo "<img src=\"$CFG->modpixpath/$activity->type/icon.gif\" ".
             "height=16 width=16 alt=\"$activity->name\">  ";
    }
    echo "<a href=\"$CFG->wwwroot/mod/forum/discuss.php?d=" . $activity->content->discussion
         . "#" . $activity->content->id . "\">";

    echo $activity->content->subject;
    echo "</a>$closeformat";

    echo "<br><font size=\"2\">";
    echo "<a href=\"$CFG->wwwroot/user/view.php?id=" . $activity->user->userid . "&course=" . "$course\">"
         . $activity->user->fullname . "</a>";
    echo " - " . userdate($activity->timestamp) . "</font></td></tr>";
    echo "</table>";

    return;
}

?>
