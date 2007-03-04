<?php  // $Id$

require_once($CFG->libdir.'/filelib.php');

/// CONSTANTS ///////////////////////////////////////////////////////////

define('FORUM_MODE_FLATOLDEST', 1);
define('FORUM_MODE_FLATNEWEST', -1);
define('FORUM_MODE_THREADED', 2);
define('FORUM_MODE_NESTED', 3);

define('FORUM_FORCESUBSCRIBE', 1);
define('FORUM_INITIALSUBSCRIBE', 2);
define('FORUM_DISALLOWSUBSCRIBE',3);

define('FORUM_TRACKING_OFF', 0);
define('FORUM_TRACKING_OPTIONAL', 1);
define('FORUM_TRACKING_ON', 2);

define('FORUM_UNSET_POST_RATING', -999);

$FORUM_LAYOUT_MODES = array ( FORUM_MODE_FLATOLDEST => get_string('modeflatoldestfirst', 'forum'),
                              FORUM_MODE_FLATNEWEST => get_string('modeflatnewestfirst', 'forum'),
                              FORUM_MODE_THREADED   => get_string('modethreaded', 'forum'),
                              FORUM_MODE_NESTED     => get_string('modenested', 'forum') );

// These are course content forums that can be added to the course manually
$FORUM_TYPES   = array ('general'    => get_string('generalforum', 'forum'),
                        'eachuser'   => get_string('eachuserforum', 'forum'),
                        'single'     => get_string('singleforum', 'forum'),
                        'qanda'      => get_string('qandaforum', 'forum')
                        );

$FORUM_OPEN_MODES   = array ('2' => get_string('openmode2', 'forum'),
                             '1' => get_string('openmode1', 'forum'),
                             '0' => get_string('openmode0', 'forum') );

if (!isset($CFG->forum_displaymode)) {
    set_config('forum_displaymode', FORUM_MODE_NESTED);
}

if (!isset($CFG->forum_shortpost)) {
    set_config('forum_shortpost', 300);  // Less non-HTML characters than this is short
}

if (!isset($CFG->forum_longpost)) {
    set_config('forum_longpost', 600);  // More non-HTML characters than this is long
}

if (!isset($CFG->forum_manydiscussions)) {
    set_config('forum_manydiscussions', 100);  // Number of discussions on a page
}

if (!isset($CFG->forum_maxbytes)) {
    set_config('forum_maxbytes', 512000);  // Default maximum size for all forums
}

if (!isset($CFG->forum_trackreadposts)) {
    set_config('forum_trackreadposts', true);  // Default whether user needs to mark a post as read
}

if (!isset($CFG->forum_oldpostdays)) {
    set_config('forum_oldpostdays', 14);  // Default number of days that a post is considered old
}

if (!isset($CFG->forum_usermarksread)) {
    set_config('forum_usermarksread', false);  // Default whether user needs to mark a post as read
}

if (!isset($CFG->forum_cleanreadtime)) {
    set_config('forum_cleanreadtime', 2);  // Default time (hour) to execute 'clean_read_records' cron
}

if (!isset($CFG->forum_replytouser)) {
    set_config('forum_replytouser', true);  // Default maximum size for all forums
}

if (empty($USER->id) or isguest()) {
    $CFG->forum_trackreadposts = false;  // This feature never works when a user isn't logged in
}

if (!isset($CFG->forum_enabletimedposts)) {   // Newish feature that is not quite ready for production in 1.6
    $CFG->forum_enabletimedposts = false;
}

if (!isset($CFG->forum_enablerssfeeds)) {   // Disable forum RSS feeds by default
    $CFG->forum_enablerssfeeds = false;
}

/// STANDARD FUNCTIONS ///////////////////////////////////////////////////////////

function forum_add_instance($forum) {
// Given an object containing all the necessary data,
// (defined by the form in mod.html) this function
// will create a new instance and return the id number
// of the new instance.

    global $CFG;

    $forum->timemodified = time();

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

    //sanitize given values a bit
    $forum->warnafter = clean_param($forum->warnafter, PARAM_INT);
    $forum->blockafter = clean_param($forum->blockafter, PARAM_INT);

    if (! $forum->id = insert_record('forum', $forum)) {
        return false;
    }

    if ($forum->type == 'single') {  // Create related discussion.
        $discussion->course   = $forum->course;
        $discussion->forum    = $forum->id;
        $discussion->name     = $forum->name;
        $discussion->intro    = $forum->intro;
        $discussion->assessed = $forum->assessed;
        $discussion->format   = $forum->format;
        $discussion->mailnow  = false;

        if (! forum_add_discussion($discussion, $discussion->intro)) {
            error('Could not add the discussion for this forum');
        }
    }

    if ($forum->forcesubscribe == FORUM_INITIALSUBSCRIBE) { // all users should be subscribed initially
        $users = get_course_users($forum->course);
        foreach ($users as $user) {
            forum_subscribe($user->id, $forum->id);
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

    if (empty($forum->userating)) {
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

    if ($forum->type == 'single') {  // Update related discussion and post.
        if (! $discussion = get_record('forum_discussions', 'forum', $forum->id)) {
            if ($discussions = get_records('forum_discussions', 'forum', $forum->id, 'timemodified ASC')) {
                notify('Warning! There is more than one discussion in this forum - using the most recent');
                $discussion = array_pop($discussions);
            } else {
                error('Could not find the discussion in this forum');
            }
        }
        if (! $post = get_record('forum_posts', 'id', $discussion->firstpost)) {
            error('Could not find the first post in this forum discussion');
        }

        $post->subject  = $forum->name;
        $post->message  = $forum->intro;
        $post->modified = $forum->timemodified;

        if (! update_record('forum_posts', $post)) {
            error('Could not update the first post');
        }

        $discussion->name = $forum->name;

        if (! update_record('forum_discussions', $discussion)) {
            error('Could not update the discussion');
        }
    }

    return update_record('forum', $forum);
}


function forum_delete_instance($id) {
// Given an ID of an instance of this module,
// this function will permanently delete the instance
// and any data that depends on it.

    if (! $forum = get_record('forum', 'id', $id)) {
        return false;
    }

    $result = true;

    if ($discussions = get_records('forum_discussions', 'forum', $forum->id)) {
        foreach ($discussions as $discussion) {
            if (! forum_delete_discussion($discussion, true)) {
                $result = false;
            }
        }
    }

    if (! delete_records('forum_subscriptions', 'forum', $forum->id)) {
        $result = false;
    }

    forum_tp_delete_read_records(-1, -1, -1, $forum->id);

    if (! delete_records('forum', 'id', $forum->id)) {
        $result = false;
    }

    return $result;
}


function forum_cron () {
/// Function to be run periodically according to the moodle cron
/// Finds all posts that have yet to be mailed out, and mails them
/// out to all subscribers

    global $CFG, $USER;
    static $strforums = NULL;

    if ($strforums === NULL) {
        $strforums = get_string('forums', 'forum');
    }

    if (!empty($USER->id)) { // Remember real USER account if necessary
        $realuser = clone($USER); //PHP5 compatibility
    } else {
        $realuser = false;
    }

    /// Posts older than 2 days will not be mailed.  This is to avoid the problem where
    /// cron has not been running for a long time, and then suddenly people are flooded
    /// with mail from the past few weeks or months

    $timenow   = time();
    $endtime   = $timenow - $CFG->maxeditingtime;
    $starttime = $endtime - 48 * 3600;   /// Two days earlier

    if ($posts = forum_get_unmailed_posts($starttime, $endtime)) {

        /// Mark them all now as being mailed.  It's unlikely but possible there
        /// might be an error later so that a post is NOT actually mailed out,
        /// but since mail isn't crucial, we can accept this risk.  Doing it now
        /// prevents the risk of duplicated mails, which is a worse problem.

        if (!forum_mark_old_posts_as_mailed($endtime)) {
            mtrace('Errors occurred while trying to mark some posts as being mailed.');
            return false;  // Don't continue trying to mail them, in case we are in a cron loop
        }

        @set_time_limit(0);   /// so that script does not get timed out when posting to many users

        $urlinfo = parse_url($CFG->wwwroot);
        $hostname = $urlinfo['host'];

        foreach ($posts as $post) {

            mtrace(get_string('processingpost', 'forum', $post->id), '');

            if (! $userfrom = get_record('user', 'id', $post->userid)) {
                mtrace('Could not find user '.$post->userid);
                continue;
            }



            if (! $discussion = get_record("forum_discussions", "id", "$post->discussion")) {
                mtrace("Could not find discussion $post->discussion");
                continue;
            }

            if (! $forum = get_record("forum", "id", "$discussion->forum")) {
                mtrace("Could not find forum $discussion->forum");
                continue;
            }

            if (! $course = get_record("course", "id", "$forum->course")) {
                mtrace("Could not find course $forum->course");
                continue;
            }

            $cleanforumname = str_replace('"', "'", strip_tags($forum->name));
            $userfrom->customheaders = array (  // Headers to make emails easier to track
                       'Precedence: Bulk',
                       'List-Id: "'.$cleanforumname.'" <moodleforum'.$forum->id.'@'.$hostname.'>',
                       'List-Help: '.$CFG->wwwroot.'/mod/forum/view.php?f='.$forum->id,
                       'Message-Id: <moodlepost'.$post->id.'@'.$hostname.'>',
                       'In-Reply-To: <moodlepost'.$post->parent.'@'.$hostname.'>',
                       'References: <moodlepost'.$post->parent.'@'.$hostname.'>',
                       'X-Course-Id: '.$course->id,
                       'X-Course-Name: '.strip_tags($course->fullname)
            );


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
                    
                    // make sure we're allowed to see it...
                    if (!forum_user_can_see_post($forum,$discussion,$post,$userto)) {
                        continue;
                    }

                    if ($userto->maildigest > 0) {
                        // This user wants the mails to be in digest form
                        $queue = New stdClass;
                        $queue->userid = $userto->id;
                        $queue->discussionid = $discussion->id;
                        $queue->postid = $post->id;
                        if(!insert_record('forum_queue', $queue)) {
                            mtrace("Error: mod/forum/cron.php: Could not queue for digest mail for id $post->id to user $userto->id ($userto->email) .. not trying again.");
                        }
                        continue;
                    }

                    /// Override the language and timezone of the "current" user, so that
                    /// mail is customised for the receiver.
                    $USER->lang     = $userto->lang;
                    $USER->timezone = $userto->timezone;

                    $postsubject = "$course->shortname: ".format_string($post->subject,true);
                    $posttext = forum_make_mail_text($course, $forum, $discussion, $post, $userfrom, $userto);
                    $posthtml = forum_make_mail_html($course, $forum, $discussion, $post, $userfrom, $userto);

                    if (!$mailresult = email_to_user($userto, $userfrom, $postsubject, $posttext,
                                                     $posthtml, '', '', $CFG->forum_replytouser)) {
                        mtrace("Error: mod/forum/cron.php: Could not send out mail for id $post->id to user $userto->id".
                             " ($userto->email) .. not trying again.");
                        add_to_log($course->id, 'forum', 'mail error', "discuss.php?d=$discussion->id#$post->id",
                                   substr(format_string($post->subject,true),0,30), $cm->id, $userto->id);
                        $errorcount++;
                    } else if ($mailresult === 'emailstop') {
                        add_to_log($course->id, 'forum', 'mail blocked', "discuss.php?d=$discussion->id#$post->id",
                                   substr(format_string($post->subject,true),0,30), $cm->id, $userto->id);
                    } else {
                        $mailcount++;

                    /// Mark post as read if forum_usermarksread is set off
                        if (!$CFG->forum_usermarksread && forum_tp_can_track_forums($forum, $userto) && 
                            forum_tp_is_tracked($forum, $userto->id)) {
                            if (!forum_tp_mark_post_read($userto->id, $post, $forum->id)) {
                                mtrace("Error: mod/forum/cron.php: Could not mark post $post->id read for user $userto->id".
                                     " while sending email.");
                            }
                        }
                    }
                }

                mtrace(".... mailed to $mailcount users.");
                if ($errorcount) {
                    set_field("forum_posts", "mailed", "2", "id", "$post->id");
                }
            }
        }
    }

    unset($CFG->courselang);

    if (!empty($realuser)) {   // Restore real USER timezone if necessary
        $sitetimezone = $realuser->timezone;
        $USER->lang   = $realuser->lang;
    } else {
        $sitetimezone = $CFG->timezone;
        $USER->lang   = $CFG->lang;
    }

    /// Now see if there are any digest mails waiting to be sent, and if we should send them

    if (!isset($CFG->digestmailtimelast)) {    // To catch the first time
        set_config('digestmailtimelast', 0);
    }

    $timenow = time();
    $digesttime = usergetmidnight($timenow, $sitetimezone) + ($CFG->digestmailtime * 3600);

    if ($CFG->digestmailtimelast < $digesttime and $timenow > $digesttime) {

        set_config('digestmailtimelast', $timenow);

        mtrace('Sending forum digests: '.userdate($timenow, '', $sitetimezone));

        $digestposts = get_records('forum_queue');
        if(!empty($digestposts)) {

            @set_time_limit(0);   /// so that script does not get timed out when posting to many users

            // We have work to do
            $usermailcount = 0;
            $site = get_site();

            $users = array();
            $posts = array();
            $discussions = array();
            $discussionposts = array();
            $userdiscussions = array();
            foreach($digestposts as $digestpost) {
                if(!isset($users[$digestpost->userid])) {
                    $users[$digestpost->userid] = get_record('user', 'id', $digestpost->userid);
                }
                if(!isset($discussions[$digestpost->discussionid])) {
                    $discussions[$digestpost->discussionid] = get_record('forum_discussions', 'id', $digestpost->discussionid);
                }
                if(!isset($posts[$digestpost->postid])) {
                    $posts[$digestpost->postid] = get_record('forum_posts', 'id', $digestpost->postid);
                }
                $userdiscussions[$digestpost->userid][$digestpost->discussionid] = $digestpost->discussionid;
                $discussionposts[$digestpost->discussionid][$digestpost->postid] = $digestpost->postid;
            }

            // Data collected, start sending out emails to each user

            foreach($userdiscussions as $userid => $thesediscussions) {

                mtrace(get_string('processingdigest', 'forum', $userid),'... ');

                // First of all delete all the queue entries for this user
                delete_records('forum_queue', 'userid', $userid);
                $userto = $users[$userid];

                /// Override the language and timezone of the "current" user, so that
                /// mail is customised for the receiver.
                $USER->lang     = $userto->lang;
                $USER->timezone = $userto->timezone;


                $postsubject = get_string('digestmailsubject', 'forum', $site->shortname);
                $headerdata = New stdClass;
                $headerdata->sitename = $site->fullname;
                $headerdata->userprefs = $CFG->wwwroot.'/user/edit.php?id='.$userid.'&amp;course='.$site->id;

                $posttext = get_string('digestmailheader', 'forum', $headerdata)."\n\n";
                $headerdata->userprefs = '<a target="_blank" href="'.$headerdata->userprefs.'">'.get_string('digestmailprefs', 'forum').'</a>';

                $posthtml = "<head>";
                foreach ($CFG->stylesheets as $stylesheet) {
                    $posthtml .= '<link rel="stylesheet" type="text/css" href="'.$stylesheet.'" />'."\n";
                }
                $posthtml .= "</head>\n<body>\n";
                $posthtml .= '<p>'.get_string('digestmailheader', 'forum', $headerdata).'</p><br /><hr size="1" noshade="noshade" />';

                foreach($thesediscussions as $discussionid) {
                    $discussion = $discussions[$discussionid];
                    if(empty($discussion)) {
                        mtrace("Error: Could not find discussion $discussionid");
                        continue;
                    }

                    if (! $forum = get_record("forum", "id", "$discussion->forum")) {
                        mtrace("Could not find forum $discussion->forum");
                        continue;
                    }
                    if (! $course = get_record("course", "id", "$forum->course")) {
                        mtrace("Could not find course $forum->course");
                        continue;
                    }

                    $canunsubscribe = ! forum_is_forcesubscribed($forum->id);
                    $canreply = forum_user_can_post($forum, $userto);


                    $posttext .= "\n \n";
                    $posttext .= '=====================================================================';
                    $posttext .= "\n \n";
                    $posttext .= "$course->shortname -> $strforums -> ".format_string($forum->name,true);
                    if ($discussion->name != $forum->name) {
                        $posttext  .= " -> ".format_string($discussion->name,true);
                    }
                    $posttext .= "\n";

                    $posthtml .= "<p><font face=\"sans-serif\">".
                    "<a target=\"_blank\" href=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</a> -> ".
                    "<a target=\"_blank\" href=\"$CFG->wwwroot/mod/forum/index.php?id=$course->id\">$strforums</a> -> ".
                    "<a target=\"_blank\" href=\"$CFG->wwwroot/mod/forum/view.php?f=$forum->id\">".format_string($forum->name,true)."</a>";
                    if ($discussion->name == $forum->name) {
                        $posthtml .= "</font></p>";
                    } else {
                        $posthtml .= " -> <a target=\"_blank\" href=\"$CFG->wwwroot/mod/forum/discuss.php?d=$discussion->id\">".format_string($discussion->name,true)."</a></font></p>";
                    }
                    $posthtml .= '<p>';

                    $postsarray = $discussionposts[$discussionid];
                    sort($postsarray);

                /// Create an empty array to use for marking read posts.
                /// (I'm sure there's already a structure I can use here, but I can't be sure.)
                    $markread = array();

                    foreach ($postsarray as $postid) {
                        if (! $post = get_record("forum_posts", "id", "$postid")) {
                            mtrace("Error: Could not find post $postid");
                            continue;
                        }
                        if (! $userfrom = get_record("user", "id", "$post->userid")) {
                            mtrace("Error: Could not find user $post->userid");
                            continue;
                        }

                        $userfrom->customheaders = array ("Precedence: Bulk");

                        if ($userto->maildigest == 2) {
                            // Subjects only
                            $by = New stdClass;
                            $by->name = fullname($userfrom);
                            $by->date = userdate($post->modified);
                            $posttext .= "\n".format_string($post->subject,true).' '.get_string("bynameondate", "forum", $by);
                            $posttext .= "\n---------------------------------------------------------------------";

                            $by->name = "<a target=\"_blank\" href=\"$CFG->wwwroot/user/view.php?id=$userfrom->id&amp;course=$course->id\">$by->name</a>";
                            $posthtml .= '<div><a target="_blank" href="'.$CFG->wwwroot.'/mod/forum/discuss.php?d='.$discussion->id.'#'.$post->id.'">'.format_string($post->subject,true).'</a> '.get_string("bynameondate", "forum", $by).'</div>';

                        } else {
                            // The full treatment
                            $posttext .= forum_make_mail_text($course, $forum, $discussion, $post, $userfrom, $userto, true);
                            $posthtml .= forum_make_mail_post($post, $userfrom, $userto, $course, false, $canreply, true, false);

                        /// Create an array of postid's for this user to mark as read.
                            if (!$CFG->forum_usermarksread && 
                                forum_tp_can_track_forums($forum, $userto) && 
                                forum_tp_is_tracked($forum, $userto->id)) {
                                $markread[$post->id]->post = $post;
                                $markread[$post->id]->forumid = $forum->id;
                            }
                        }
                    }
                    if ($canunsubscribe) {
                        $posthtml .= "\n<div align=\"right\"><font size=\"1\"><a href=\"$CFG->wwwroot/mod/forum/subscribe.php?id=$forum->id\">".get_string("unsubscribe", "forum")."</a></font></div>";
                    } else {
                        $posthtml .= "\n<div align=\"right\"><font size=\"1\">".get_string("everyoneissubscribed", "forum")."</font></div>";
                    }
                    $posthtml .= '<hr size="1" noshade="noshade" /></p>';
                }
                $posthtml .= '</body>';

                if($userto->mailformat != 1) {
                    // This user DOESN'T want to receive HTML
                    $posthtml = '';
                }

                if (!$mailresult =  email_to_user($userto, $site->shortname, $postsubject, $posttext, $posthtml,
                                                  '', '', $CFG->forum_replytouser)) {
                    mtrace("ERROR!");
                    echo "Error: mod/forum/cron.php: Could not send out digest mail to user $userto->id ($userto->email)... not trying again.\n";
                    add_to_log($course->id, 'forum', 'mail digest error', '', '', $cm->id, $userto->id);
                } else if ($mailresult === 'emailstop') {
                    add_to_log($course->id, 'forum', 'mail digest blocked', '', '', $cm->id, $userto->id);
                } else {
                    mtrace("success.");
                    $usermailcount++;

                /// Mark post as read if forum_usermarksread is set off
                    if (!$CFG->forum_usermarksread && 
                        forum_tp_can_track_forums($forum->id, $userto) && 
                        forum_tp_is_tracked($forum->id, $userto->id)) {
                        foreach ($markread as $postinfo) {
                            if (!forum_tp_mark_post_read($userto->id, $postinfo->post, $postinfo->forumid)) {
                                mtrace("Error: mod/forum/cron.php: Could not mark post $postid read for user $userto->id".
                                     " while sending digest email.");
                            }
                        }
                    }
                }
            }
        }
    }

    if(!empty($usermailcount)) {
        mtrace(get_string('digestsentusers', 'forum', $usermailcount));
    }

    if (!empty($realuser)) {   // Restore real USER if necessary
        $USER = $realuser;
    }

    if (!empty($CFG->forum_lastreadclean)) {
        $timenow = time();
        if ($CFG->forum_lastreadclean + (24*3600) < $timenow) {
            set_config('forum_lastreadclean', $timenow);
            forum_tp_clean_read_records();
        }
    } else {
        set_config('forum_lastreadclean', time());
    }


    return true;
}

function forum_make_mail_text($course, $forum, $discussion, $post, $userfrom, $userto, $bare = false) {
    global $CFG;

    $by = New stdClass;
    $by->name = fullname($userfrom, isteacher($course->id, $userto->id));
    $by->date = userdate($post->modified, "", $userto->timezone);

    $strbynameondate = get_string('bynameondate', 'forum', $by);

    $strforums = get_string('forums', 'forum');

    $canunsubscribe = ! forum_is_forcesubscribed($forum->id);
    $canreply = forum_user_can_post($forum, $userto);

    $posttext = '';

    if(!$bare) {
        $posttext  = "$course->shortname -> $strforums -> ".format_string($forum->name,true);

        if ($discussion->name != $forum->name) {
            $posttext  .= " -> ".format_string($discussion->name,true);
        }
    }

    $posttext .= "\n---------------------------------------------------------------------\n";
    $posttext .= format_string($post->subject,true);
    if($bare) {
        $posttext .= " ($CFG->wwwroot/mod/forum/discuss.php?d=$discussion->id#$post->id)";
    }
    $posttext .= "\n".$strbynameondate."\n";
    $posttext .= "---------------------------------------------------------------------\n";
    $posttext .= format_text_email($post->message, $post->format);
    $posttext .= "\n\n";
    if ($post->attachment) {
        $post->course = $course->id;
        $post->forum = $forum->id;
        $posttext .= forum_print_attachments($post, "text");
    }
    if (!$bare && $canreply) {
        $posttext .= "---------------------------------------------------------------------\n";
        $posttext .= get_string("postmailinfo", "forum", $course->shortname)."\n";
        $posttext .= "$CFG->wwwroot/mod/forum/post.php?reply=$post->id\n";
    }
    if (!$bare && $canunsubscribe) {
        $posttext .= "\n---------------------------------------------------------------------\n";
        $posttext .= get_string("unsubscribe", "forum");
        $posttext .= ": $CFG->wwwroot/mod/forum/subscribe.php?id=$forum->id\n";
    }

    return $posttext;
}

function forum_make_mail_html($course, $forum, $discussion, $post, $userfrom, $userto) {
    global $CFG;

    if ($userto->mailformat != 1) {  // Needs to be HTML
        return '';
    }

    $strforums = get_string('forums', 'forum');
    $canreply = forum_user_can_post($forum, $userto);
    $canunsubscribe = ! forum_is_forcesubscribed($forum->id);

    $posthtml = '<head>';
    foreach ($CFG->stylesheets as $stylesheet) {
        $posthtml .= '<link rel="stylesheet" type="text/css" href="'.$stylesheet.'" />'."\n";
    }
    $posthtml .= '</head>';
    $posthtml .= "\n<body id=\"email\">\n\n";

    $posthtml .= '<div class="navbar">'.
    '<a target="_blank" href="'.$CFG->wwwroot.'/course/view.php?id='.$course->id.'">'.$course->shortname.'</a> &raquo; '.
    '<a target="_blank" href="'.$CFG->wwwroot.'/mod/forum/index.php?id='.$course->id.'">'.$strforums.'</a> &raquo; '.
    '<a target="_blank" href="'.$CFG->wwwroot.'/mod/forum/view.php?f='.$forum->id.'">'.format_string($forum->name,true).'</a>';
    if ($discussion->name == $forum->name) {
        $posthtml .= '</div>';
    } else {
        $posthtml .= ' &raquo; <a target="_blank" href="'.$CFG->wwwroot.'/mod/forum/discuss.php?d='.$discussion->id.'">'.
                     format_string($discussion->name,true).'</a></div>';
    }
    $posthtml .= forum_make_mail_post($post, $userfrom, $userto, $course, false, $canreply, true, false);

    if ($canunsubscribe) {
        $posthtml .= '<br /><div class="unsubscribelink"><a href="'.$CFG->wwwroot.'/mod/forum/subscribe.php?id='.$forum->id.'">'.
                     get_string('unsubscribe', 'forum').'</a></div>';
    }

    $posthtml .= '</body>';

    return $posthtml;
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

            $post->forum = $forum->id;
            forum_print_post($post, $course->id, $ownpost=false, $reply=false, $link=false, $rate=false);
        }

    } else {
        echo "<p>".get_string("noposts", "forum")."</p>";
    }
}

function forum_print_overview($courses,&$htmlarray) {
    global $USER, $CFG;

    if (empty($courses) || !is_array($courses) || count($courses) == 0) {
        return array();
    }

    if (!$forums = get_all_instances_in_courses('forum',$courses)) {
        return;
    }


    // get all forum logs in ONE query (much better!)
    $sql = "SELECT instance,cmid,l.course,COUNT(l.id) as count FROM {$CFG->prefix}log l "
        ." JOIN {$CFG->prefix}course_modules cm ON cm.id = cmid "
        ." WHERE (";
    foreach ($courses as $course) {
        $sql .= '(l.course = '.$course->id.' AND l.time > '.$course->lastaccess.') OR ';
    }
    $sql = substr($sql,0,-3); // take off the last OR

    $sql .= ") AND l.module = 'forum' AND action LIKE 'add post%' "
        ." AND userid != ".$USER->id." GROUP BY cmid,l.course,instance";
    
    if (!$new = get_records_sql($sql)) {
        $new = array(); // avoid warnings
    }
    
    // also get all forum tracking stuff ONCE.
    $trackingforums = array();
    foreach ($forums as $forum) {
        if (forum_tp_can_track_forums($forum)) {
            $trackingforums[$forum->id] = $forum;
        }
    }
    
    if (count($trackingforums) > 0) {
        $cutoffdate = isset($CFG->forum_oldpostdays) ? (time() - ($CFG->forum_oldpostdays*24*60*60)) : 0;
        $sql = 'SELECT d.forum,d.course,COUNT(p.id) AS count '.
            ' FROM '.$CFG->prefix.'forum_posts p '.
            ' JOIN '.$CFG->prefix.'forum_discussions d ON p.discussion = d.id '.
            ' LEFT JOIN '.$CFG->prefix.'forum_read r ON r.postid = p.id AND r.userid = '.$USER->id.' WHERE (';
        foreach ($trackingforums as $track) {
            $sql .= '(d.forum = '.$track->id.' AND (d.groupid = -1 OR d.groupid = 0 OR d.groupid = '.get_current_group($track->course,false).')) OR ';
        }
        $sql = substr($sql,0,-3); // take off the last OR
        $sql .= ') AND p.modified >= '.$cutoffdate.' AND r.id is NULL GROUP BY d.forum,d.course';

        if (!$unread = get_records_sql($sql)) {
            $unread = array();
        }
    } else {
        $unread = array();
    }

    if (empty($unread) and empty($new)) {
        return;
    }

    $strforum = get_string('modulename','forum');
    $strnumunread = get_string('overviewnumunread','forum');
    $strnumpostssince = get_string('overviewnumpostssince','forum');

    foreach ($forums as $forum) {
        $str = '';
        $count = 0;
        $thisunread = 0;
        $showunread = false;
        // either we have something from logs, or trackposts, or nothing.
        if (array_key_exists($forum->id, $new) && !empty($new[$forum->id])) {
            $count = $new[$forum->id]->count;
        }
        if (array_key_exists($forum->id,$unread)) {
            $thisunread = $unread[$forum->id]->count;
            $showunread = true;
        }
        if ($count > 0 || $thisunread > 0) {
            $str .= '<div class="overview forum"><div class="name">'.$strforum.': <a title="'.$strforum.'" href="'.$CFG->wwwroot.'/mod/forum/view.php?f='.$forum->id.'">'.
                $forum->name.'</a></div>';
            $str .= '<div class="info">';
            $str .= $count.' '.$strnumpostssince;
            if (!empty($showunread)) {
                $str .= '<br />'.$thisunread .' '.$strnumunread;
            }
            $str .= '</div></div>';
        }
        if (!empty($str)) { 
            if (!array_key_exists($forum->course,$htmlarray)) {
                $htmlarray[$forum->course] = array();
            }
            if (!array_key_exists('forum',$htmlarray[$forum->course])) {
                $htmlarray[$forum->course]['forum'] = ''; // initialize, avoid warnings
            }
            $htmlarray[$forum->course]['forum'] .= $str;
        }
    }    
}

function forum_print_recent_activity($course, $isteacher, $timestart) {
/// Given a course and a date, prints a summary of all the new
/// messages posted in the course since that date

    global $CFG;

    $heading = false;
    $content = false;

    if (!$logs = get_records_select('log', 'time > \''.$timestart.'\' AND '.
                                           'course = \''.$course->id.'\' AND '.
                                           'module = \'forum\' AND '.
                                           'action LIKE \'add %\' ', 'time ASC')){
        return false;
    }

    $strftimerecent = get_string('strftimerecent');

    $isteacheredit = isteacheredit($course->id);
    $mygroupid     = mygroupid($course->id);

    $groupmode = array();   /// To cache group modes

    foreach ($logs as $log) {
        //Get post info, I'll need it later
        if ($post = forum_get_post_from_log($log)) {
            //Create a temp valid module structure (course,id)
            $tempmod->course = $log->course;
            $tempmod->id = $post->forum;
            //Obtain the visible property from the instance
            $modvisible = instance_is_visible('forum', $tempmod);
        }

        //Only if the post exists and mod is visible
        if ($post && $modvisible) {
            /// Check whether this is for teachers only
            $teacheronly = '';
            if ($post->forumtype == 'teacher') {
                if ($isteacher) {
                    $teacheronly = 'class=\'teacheronly\'';
                } else {
                    continue;
                }
            }
            /// Check whether this is belongs to a discussion in a group that
            /// should NOT be accessible to the current user

            if (!$isteacheredit and $post->groupid != -1) {   /// Editing teachers or open discussions
                if (!isset($cm[$post->forum])) {
                    $cm[$post->forum] = get_coursemodule_from_instance('forum', $post->forum, $course->id);
                    $groupmode[$post->forum] = groupmode($course, $cm[$post->forum]);
                }
                if ($groupmode[$post->forum]) {
                    //hope i didn't break anything
                    if (!@in_array($mygroupid, $post->groupid))/*$mygroupid != $post->groupid*/{
                        continue;
                    }
                }
            }

            if (! $heading) {
                print_headline(get_string('newforumposts', 'forum').':');
                $heading = true;
                $content = true;
            }
            $date = userdate($post->modified, $strftimerecent);

            $subjectclass = ($log->action == 'add discussion') ? ' bold' : '';

            echo '<div class="head'.$teacheronly.'">'.
                   '<div class="date">'.$date.'</div>'.
                   '<div class="name">'.fullname($post, $isteacher).'</div>'.
                 '</div>';
            echo '<div class="info'.$subjectclass.'">';
            echo '"<a href="'.$CFG->wwwroot.'/mod/forum/'.str_replace('&', '&amp;', $log->url).'">';
            $post->subject = break_up_long_words(format_string($post->subject,true));
            echo $post->subject;
            echo '</a>"</div>';
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
    $st_subscriptions = get_records_sql("SELECT DISTINCT u.id, u.id
                                         FROM {$CFG->prefix}user u,
                                              {$CFG->prefix}forum_subscriptions s
                                         WHERE s.forum = '$forumid' and
                                               u.id = s.userid");
    //Get students from forum_posts
    $st_posts = get_records_sql("SELECT DISTINCT u.id, u.id
                                 FROM {$CFG->prefix}user u,
                                      {$CFG->prefix}forum_discussions d,
                                      {$CFG->prefix}forum_posts p
                                 WHERE d.forum = '$forumid' and
                                       p.discussion = d.id and
                                       u.id = p.userid");

    //Get students from forum_ratings
    $st_ratings = get_records_sql("SELECT DISTINCT u.id, u.id
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

    if (!empty($rec) && !empty($scaleid)) {
        $return = true;
    }

    return $return;
}

/// SQL FUNCTIONS ///////////////////////////////////////////////////////////

function forum_get_post_full($postid) {
/// Gets a post with all info ready for forum_print_post
/// Most of these joins are just to get the forum id
    global $CFG;

    return get_record_sql("SELECT p.*, d.forum, u.firstname, u.lastname, u.email, u.picture
                            FROM {$CFG->prefix}forum_posts p
                       LEFT JOIN {$CFG->prefix}forum_discussions d ON p.discussion = d.id
                       LEFT JOIN {$CFG->prefix}user u ON p.userid = u.id
                           WHERE p.id = '$postid'");
}

function forum_get_discussion_posts($discussion, $sort, $forumid) {
/// Gets posts with all info ready for forum_print_post
/// We pass forumid in because we always know it so no need to make a 
/// complicated join to find it out.
    global $CFG;

    return get_records_sql("SELECT p.*, $forumid AS forum, u.firstname, u.lastname, u.email, u.picture
                              FROM {$CFG->prefix}forum_posts p
                         LEFT JOIN {$CFG->prefix}user u ON p.userid = u.id
                             WHERE p.discussion = $discussion
                               AND p.parent > 0 $sort");
}

function forum_get_child_posts($parent, $forumid) {
/// Gets posts with all info ready for forum_print_post
/// We pass forumid in because we always know it so no need to make a 
/// complicated join to find it out.
    global $CFG;

    return get_records_sql("SELECT p.*, $forumid AS forum, u.firstname, u.lastname, u.email, u.picture
                              FROM {$CFG->prefix}forum_posts p
                         LEFT JOIN {$CFG->prefix}user u ON p.userid = u.id
                             WHERE p.parent = '$parent'
                          ORDER BY p.created ASC");
}


function forum_search_posts($searchterms, $courseid, $page=0, $recordsperpage=50, &$totalcount, $sepgroups=0, $extrasql='') {
/// Returns a list of posts found using an array of search terms
/// eg   word  +word -word
///
    global $CFG, $USER;
    require_once($CFG->libdir.'/searchlib.php');

    if (!isteacher($courseid)) {
        $notteacherforum = "AND f.type <> 'teacher'";
        $forummodule = get_record("modules", "name", "forum");
        $onlyvisible = "AND d.forum = f.id AND f.id = cm.instance AND cm.visible = 1 AND cm.module = $forummodule->id";
        $onlyvisibletable = ", {$CFG->prefix}course_modules cm, {$CFG->prefix}forum f";
        if (!empty($sepgroups)) {
            $separategroups = SEPARATEGROUPS;
            $selectgroup = " AND ( NOT (cm.groupmode='$separategroups'".
                                      " OR (c.groupmode='$separategroups' AND c.groupmodeforce='1') )";//.
            $selectgroup .= " OR d.groupid = '-1'"; //search inside discussions for all groups too
            foreach ($sepgroups as $sepgroup){
                $selectgroup .= " OR d.groupid = '$sepgroup->id'";
            }
            $selectgroup .= ")";

                               //  " OR d.groupid = '$groupid')";
            $selectcourse = " AND d.course = '$courseid' AND c.id='$courseid'";
            $coursetable = ", {$CFG->prefix}course c";
        } else {
            $selectgroup = '';
            $selectcourse = " AND d.course = '$courseid'";
            $coursetable = '';
        }
    } else {
        $notteacherforum = "";
        $selectgroup = '';
        $onlyvisible = "";
        $onlyvisibletable = "";
        $coursetable = '';
        if ($courseid == SITEID && isadmin()) {
            $selectcourse = '';
        } else {
            $selectcourse = " AND d.course = '$courseid'";
        }
    }

    $timelimit = '';
    if (!empty($CFG->forum_enabletimedposts) && (!((isadmin() and !empty($CFG->admineditalways)) || isteacher($courseid)))) {
        $now = time();
        $timelimit = " AND (d.userid = $USER->id OR ((d.timestart = 0 OR d.timestart <= $now) AND (d.timeend = 0 OR d.timeend > $now)))";
    }

    $limit = sql_paging_limit($page, $recordsperpage);

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
    $searchstring = "";
    // Need to concat these back together for parser to work.
    foreach($searchterms as $searchterm){
        if ($searchstring != "") {
            $searchstring .= " ";
        }
        $searchstring .= $searchterm;
    }

    // We need to allow quoted strings for the search. The quotes *should* be stripped
    // by the parser, but this should be examined carefully for security implications.
    $searchstring = str_replace("\\\"","\"",$searchstring);
    $parser = new search_parser();
    $lexer = new search_lexer($parser);

    if ($lexer->parse($searchstring)) {
        $parsearray = $parser->get_parsed_array();
        $messagesearch = search_generate_SQL($parsearray,'p.message','p.subject','p.userid','u.id','u.firstname','u.lastname','p.modified', 'd.forum');
    }

    $selectsql = "{$CFG->prefix}forum_posts p,
                  {$CFG->prefix}forum_discussions d,
                  {$CFG->prefix}user u $onlyvisibletable $coursetable
             WHERE ($messagesearch)
               AND p.userid = u.id
               AND p.discussion = d.id $selectcourse $notteacherforum $onlyvisible $selectgroup $timelimit $extrasql";

    $totalcount = count_records_sql("SELECT COUNT(*) FROM $selectsql");

    return get_records_sql("SELECT p.*,d.forum, u.firstname,u.lastname,u.email,u.picture FROM
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

function forum_get_unmailed_posts($starttime, $endtime) {
/// Returns a list of all new posts that have not been mailed yet
    global $CFG;
    $now = time();
    return get_records_sql("SELECT p.*, d.course
                              FROM {$CFG->prefix}forum_posts p,
                                   {$CFG->prefix}forum_discussions d
                             WHERE p.mailed = 0
                               AND (p.created >= '$starttime' OR d.timestart > 0)
                               AND (p.created < '$endtime' OR p.mailnow = 1)
                               AND p.discussion = d.id
                               AND ((d.timestart = 0 OR d.timestart <= '$now')
                               AND (d.timeend = 0 OR d.timeend > '$now'))
                          ORDER BY p.modified ASC");
}

function forum_mark_old_posts_as_mailed($endtime) {
/// Marks posts before a certain time as being mailed already
    global $CFG;
/// Find out posts those are not showing immediately so we can exclude them
    $now = time();
    $delayed_posts = get_records_sql("SELECT p.id, p.discussion
                                        FROM {$CFG->prefix}forum_posts p,
                                             {$CFG->prefix}forum_discussions d
                                       WHERE p.mailed = 0
                                         AND p.discussion = d.id
                                         AND d.timestart > '$now'");
    $delayed_ids = array();
    if ($delayed_posts) {
        foreach ($delayed_posts as $post) {
            $delayed_ids[] = $post->id;
        }
    } else {
        $delayed_ids[] = 0;
    }
    return execute_sql("UPDATE {$CFG->prefix}forum_posts
                           SET mailed = '1'
                         WHERE id NOT IN (".implode(',',$delayed_ids).")
                           AND (created < '$endtime' OR mailnow = 1)
                           AND mailed ='0'", false);
}

function forum_get_user_posts($forumid, $userid) {
/// Get all the posts for a user in a forum suitable for forum_print_post
    global $CFG;

    return get_records_sql("SELECT p.*, d.forum, u.firstname, u.lastname, u.email, u.picture
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

        return get_record_sql("SELECT p.*, f.type AS forumtype, d.forum, d.groupid, 
                                           u.firstname, u.lastname, u.email, u.picture
                                 FROM {$CFG->prefix}forum_discussions d,
                                      {$CFG->prefix}forum_posts p,
                                      {$CFG->prefix}forum f,
                                      {$CFG->prefix}user u
                                WHERE p.id = '$log->info'
                                  AND d.id = p.discussion
                                  AND p.userid = u.id
                                  AND u.deleted <> '1'
                                  AND f.id = d.forum");


    } else if ($log->action == "add discussion") {

        return get_record_sql("SELECT p.*, f.type AS forumtype, d.forum, d.groupid, 
                                           u.firstname, u.lastname, u.email, u.picture
                                 FROM {$CFG->prefix}forum_discussions d,
                                      {$CFG->prefix}forum_posts p,
                                      {$CFG->prefix}forum f,
                                      {$CFG->prefix}user u
                                WHERE d.id = '$log->info'
                                  AND d.firstpost = p.id
                                  AND p.userid = u.id
                                  AND u.deleted <> '1'
                                  AND f.id = d.forum");
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


function forum_count_discussion_replies($forum='0', $course='0', $user='0') {
// Returns an array of counts of replies to each discussion (optionally in one forum or course and/or user)
    global $CFG;

    $forumselect = $courseselect = $userselect = '';

    if ($forum) {
        $forumselect = " AND d.forum = '$forum'";
    }
    if ($course) {
        $courseselect = " AND d.course = '$course'";
    }
    if ($user) {
        $userselect = " AND d.userid = '$user'";
    }
    return get_records_sql("SELECT p.discussion, (count(*)) as replies, max(p.id) as lastpostid
                              FROM {$CFG->prefix}forum_posts p,
                                   {$CFG->prefix}forum_discussions d
                             WHERE p.parent > 0 $forumselect $courseselect $userselect
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
                               $user=0, $fullpost=true, $visiblegroups=-1, $limit=0, $userlastmodified=false) {
/// Get all discussions in a forum
    global $CFG, $USER;

    $timelimit = '';

    if (!empty($CFG->forum_enabletimedposts)) {
        if (!((isadmin() and !empty($CFG->admineditalways)) || isteacher(get_field('forum', 'course', 'id', $forum)))) {
            $now = time();
            $timelimit = " AND ((d.timestart = 0 OR d.timestart <= '$now') AND (d.timeend = 0 OR d.timeend > '$now')";
            if (!empty($USER->id)) {
                $timelimit .= " OR d.userid = '$USER->id'";
            }
            $timelimit .= ')';
        }
    }

    if ($user) {
        $userselect = " AND u.id = '$user' ";
    } else {
        $userselect = "";
    }

    if ($limit) {
        $limit = " LIMIT $limit ";
    } else {
        $limit = "";
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

    if (empty($userlastmodified)) {  // We don't need to know this
        $umfields = '';
        $umtable = '';
    } else {
        $umfields = ', um.firstname AS umfirstname, um.lastname AS umlastname';
        $umtable = ' LEFT JOIN '.$CFG->prefix.'user um on (d.usermodified = um.id)';
    }

    //TODO: there must be a nice way to do this that keeps both postgres and mysql 3.2x happy but I can't find it right now.
    if ($CFG->dbtype == 'postgres7') {
        return get_records_sql("SELECT $postdata, d.name, d.timemodified, d.usermodified, d.groupid,
                                   u.firstname, u.lastname, u.email, u.picture $umfields
                              FROM {$CFG->prefix}forum_discussions d
                              JOIN {$CFG->prefix}forum_posts p ON p.discussion = d.id
                              JOIN {$CFG->prefix}user u ON p.userid = u.id
                                   $umtable
                             WHERE d.forum = '$forum'
                               AND p.parent = 0
                                   $timelimit $groupselect $userselect 
                          ORDER BY $forumsort $limit");
    } else {
        return get_records_sql("SELECT $postdata, d.name, d.timemodified, d.usermodified, d.groupid,
                                   u.firstname, u.lastname, u.email, u.picture $umfields
                              FROM ({$CFG->prefix}forum_posts p,
                                   {$CFG->prefix}user u,
                                   {$CFG->prefix}forum_discussions d)
                                   $umtable
                             WHERE d.forum = '$forum'
                               AND p.discussion = d.id
                               AND p.parent = 0
                               AND p.userid = u.id $timelimit $groupselect $userselect
                          ORDER BY $forumsort $limit");
    }
}



function forum_get_user_discussions($courseid, $userid, $groupid=0) {
/// Get all discussions started by a particular user in a course (or group)
/// This function no longer used ...
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

    if (forum_is_forcesubscribed($forum->id)) {
        if ($forum->type == "teacher") {
            return get_course_teachers($course->id);  // Only teachers can be subscribed to teacher forums
        } else {
            return get_course_users($course->id);     // Otherwise get everyone in the course
        }
    }
    return get_records_sql("SELECT u.id, u.username, u.firstname, u.lastname, u.maildisplay, u.mailformat, u.maildigest, u.emailstop,
                                   u.email, u.city, u.country, u.lastaccess, u.lastlogin, u.picture, u.timezone, u.lang, u.trackforums
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
            $forum->forcesubscribe = FORUM_FORCESUBSCRIBE;
            $forum->open = 1;   // 0 - no, 1 - posts only, 2 - discuss and post
            $forum->assessed = 0;
            if ($courseid == SITEID) {
                $forum->name  = get_string("sitenews");
                $forum->forcesubscribe = 0;
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

    global $CFG;

    static $formattedtext;        // Cached version of formatted text for a post
    static $formattedtextid;      // The ID number of the post

    if (empty($formattedtextid) or $formattedtextid != $post->id) {    // Recalculate the formatting
        $options = new Object;
        $options->para = true;
        $formattedtext = format_text($post->message, $post->format, $options, $course->id);
        $formattedtextid = $post->id;
    }

    $output = '<table border="0" cellpadding="3" cellspacing="0" class="forumpost">';

    $output .= '<tr class="header"><td width="35" valign="top" class="picture left">';
    $output .= print_user_picture($user->id, $course->id, $user->picture, false, true);
    $output .= '</td>';

    if ($post->parent) {
        $output .= '<td class="topic">';
    } else {
        $output .= '<td class="topic starter">';
    }
    $output .= '<div class="subject">'.format_string($post->subject).'</div>';

    $fullname = fullname($user, isteacher($course->id));
    $by->name = '<a href="'.$CFG->wwwroot.'/user/view.php?id='.$user->id.'&amp;course='.$course->id.'">'.$fullname.'</a>';
    $by->date = userdate($post->modified, '', $touser->timezone);
    $output .= '<div class="author">'.get_string('bynameondate', 'forum', $by).'</div>';

    $output .= '</td></tr>';

    $output .= '<tr><td class="left side" valign="top">';
    if ($group = user_group($course->id, $user->id)) {
        $output .= print_group_picture($group, $course->id, false, true, true);
    } else {
        $output .= '&nbsp;';
    }

    $output .= '</td><td class="content">';

    if ($post->attachment) {
        $post->course = $course->id;
        $post->forum = get_field('forum_discussions', 'forum', 'id', $post->discussion);
        $output .= '<div class="attachments">';
        $output .= forum_print_attachments($post, 'html');
        $output .= "</div>";
    }

    $output .= $formattedtext;


/// Commands

    $commands = array();

    if ($post->parent) {
        $commands[] = '<a target="_blank" href="'.$CFG->wwwroot.'/mod/forum/discuss.php?d='.
                      $post->discussion.'&amp;parent='.$post->parent.'">'.get_string('parent', 'forum').'</a>';
    }

    if ($ownpost) {
        $commands[] = '<a target="_blank" href="'.$CFG->wwwroot.'/mod/forum/post.php?delete='.$post->id.'">'.
                      get_string('delete', 'forum').'</a>';
    }

    if ($reply) {
        $commands[] = '<a target="_blank" href="'.$CFG->wwwroot.'/mod/forum/post.php?reply='.$post->id.'">'.
                      get_string('reply', 'forum').'</a>';
    }

    $output .= '<div class="commands">';
    $output .= implode(' | ', $commands);
    $output .= '</div>';


/// Context link to post if required

    if ($link) {
        $output .= '<div class="link">';
        $output .= '<a target="_blank" href="'.$CFG->wwwroot.'/mod/forum/discuss.php?d='.$post->discussion.'#'.$post->id.'">'.
                     get_string('postincontext', 'forum').'</a>';   
        $output .= '</div>';
    }

    if ($footer) {
        $output .= '<div class="footer">'.$footer.'</div>';
    }
    $output .= '</td></tr></table>'."\n\n";


    return $output;
}


function forum_print_post(&$post, $courseid, $ownpost=false, $reply=false, $link=false,
                          $ratings=NULL, $footer="", $highlight="", $post_read=-99) {

    global $USER, $CFG, $SESSION;

    static $stredit, $strdelete, $strreply, $strparent, $strprune, $strpruneheading;
    static $threadedmode, $isteacher, $adminedit;
    static $strmarkread, $strmarkunread, $istracked;

    if (!forum_user_can_see_post($post->forum,$post->discussion,$post)) {
        if (empty($SESSION->forum_search)) {
            // just viewing, return
            return;
        } 
        echo '<a name="'.$post->id.'"></a>';
        echo '<table cellspacing="0" class="forumpost">';
        echo '<tr class="header"><td class="picture left">';
        //        print_user_picture($post->userid, $courseid, $post->picture);
        echo '</td>';
        if ($post->parent) {
            echo '<td class="topic">';
        } else {
            echo '<td class="topic starter">';
        }
        echo '<div class="subject">'.get_string('forumsubjecthidden','forum').'</div>';
        echo '<div class="author">';
        print_string('forumauthorhidden','forum');
        echo '</div></td></tr>';
        
        echo '<tr><td class="left side">';
        echo '&nbsp;';
        
        /// Actual content
        
        echo '</td><td class="content">'."\n";
        echo get_string('forumbodyhidden','forum');
        echo '</td></tr></table>';
        return;
    }

    if (empty($stredit)) {
        $stredit = get_string('edit', 'forum');
        $strdelete = get_string('delete', 'forum');
        $strreply = get_string('reply', 'forum');
        $strparent = get_string('parent', 'forum');
        $strpruneheading = get_string('pruneheading', 'forum');
        $strprune = get_string('prune', 'forum');
        $threadedmode = (!empty($USER->mode) and ($USER->mode == FORUM_MODE_THREADED));
        $isteacher = isteacher($courseid);
        $adminedit = (isadmin() and !empty($CFG->admineditalways));
        $strmarkread = get_string('markread', 'forum');
        $strmarkunread = get_string('markunread', 'forum');

        if (!empty($post->forum)) {
            $istracked = (forum_tp_can_track_forums($post->forum) && 
                          forum_tp_is_tracked($post->forum));
        } else {
            $istracked = false;
        }
    }

    if ($istracked) {
        if ($post_read == -99) {    // If we don't know yet...
        /// The front page can display a news item post to non-logged in users. This should
        /// always appear as 'read'.
            $post_read = empty($USER) || forum_tp_is_post_read($USER->id, $post);
        }
        if ($post_read) {
            $read_style = ' read';
        } else {
            $read_style = ' unread';
            echo '<a name="unread"></a>';
        }
    } else {
        $read_style = '';
    }

    echo '<a name="'.$post->id.'"></a>';
    echo '<table cellspacing="0" class="forumpost'.$read_style.'">';

    echo '<tr class="header"><td class="picture left">';
    print_user_picture($post->userid, $courseid, $post->picture);
    echo '</td>';

    if ($post->parent) {
        echo '<td class="topic">';
    } else {
        echo '<td class="topic starter">';
    }

    echo '<div class="subject">'.$post->subject.'</div>';

    echo '<div class="author">';
    $fullname = fullname($post, $isteacher);
    $by->name = '<a href="'.$CFG->wwwroot.'/user/view.php?id='.
                $post->userid.'&amp;course='.$courseid.'">'.$fullname.'</a>';
    $by->date = userdate($post->modified);
    print_string('bynameondate', 'forum', $by);
    echo '</div></td></tr>';

    echo '<tr><td class="left side">';
    if ($group = user_group($courseid, $post->userid)) {
        print_group_picture($group, $courseid, false, false, true);
    } else {
        echo '&nbsp;';
    }

/// Actual content

    echo '</td><td class="content">'."\n";

    if ($post->attachment) {
        $post->course = $courseid;
        $post->forum = get_field('forum_discussions', 'forum', 'id', $post->discussion);
        echo '<div class="attachments">';
        $attachedimages = forum_print_attachments($post);
        echo '</div>';
    } else {
        $attachedimages = '';
    }


    $options = new Object;
    $options->para = false;
    if ($link and (strlen(strip_tags($post->message)) > $CFG->forum_longpost)) {
        // Print shortened version
        echo format_text(forum_shorten_post($post->message), $post->format, $options, $courseid);
        $numwords = count_words(strip_tags($post->message));
        echo '<p><a href="'.$CFG->wwwroot.'/mod/forum/discuss.php?d='.$post->discussion.'">';
        echo get_string('readtherest', 'forum');
        echo '</a> ('.get_string('numwords', '', $numwords).')...</p>';
    } else {
        // Print whole message
        if ($highlight) {
            echo highlight($highlight, format_text($post->message, $post->format, $options, $courseid));
        } else {
            echo format_text($post->message, $post->format, $options, $courseid);
        }
        echo $attachedimages;
    }


/// Commands

    $commands = array();

    if ($istracked) {
        /// SPECIAL CASE: The front page can display a news item post to non-logged in users.
        /// Don't display the mark read / unread controls in this case.
        if ($CFG->forum_usermarksread && !empty($USER)) {
            if ($post_read) {
                $mcmd = '&amp;mark=unread&amp;postid='.$post->id;
                $mtxt = $strmarkunread;
            } else {
                $mcmd = '&amp;mark=read&amp;postid='.$post->id;
                $mtxt = $strmarkread;
            }
            if ($threadedmode) {
                $commands[] = '<a href="'.$CFG->wwwroot.'/mod/forum/discuss.php?d='.
                              $post->discussion.'&amp;parent='.$post->id.$mcmd.'">'.$mtxt.'</a>';
            } else {
                $commands[] = '<a href="'.$CFG->wwwroot.'/mod/forum/discuss.php?d='.
                              $post->discussion.$mcmd.'#'.$post->id.'">'.$mtxt.'</a>';
            }
        }
    }

    if ($post->parent) {
        if ($threadedmode) {
            $commands[] = '<a href="'.$CFG->wwwroot.'/mod/forum/discuss.php?d='.
                          $post->discussion.'&amp;parent='.$post->parent.'">'.$strparent.'</a>';
        } else {
            $commands[] = '<a href="'.$CFG->wwwroot.'/mod/forum/discuss.php?d='.
                          $post->discussion.'#'.$post->parent.'">'.$strparent.'</a>';
        }
    }

    $forumtype = get_field('forum', 'type', 'id', $post->forum);
    
    $age = time() - $post->created;
    /// Hack for allow to edit news posts those are not displayed yet until they are displayed
    if (!$post->parent
        && $forumtype == 'news'
        && get_field_sql("SELECT id FROM {$CFG->prefix}forum_discussions WHERE id = $post->discussion AND timestart > ".time())) {
        $age = 0;
    }
    if ($ownpost or $adminedit) {
        if (($age < $CFG->maxeditingtime) or $adminedit) {
            $commands[] =  '<a href="'.$CFG->wwwroot.'/mod/forum/post.php?edit='.$post->id.'">'.$stredit.'</a>';
        }
    }

    if (isteacheredit($courseid) && $post->parent && $forumtype != 'single') {
        $commands[] = '<a href="'.$CFG->wwwroot.'/mod/forum/post.php?prune='.$post->id.
                      '" title="'.$strpruneheading.'">'.$strprune.'</a>';
    }

    if (($ownpost and $age < $CFG->maxeditingtime) or $isteacher) {
        $commands[] = '<a href="'.$CFG->wwwroot.'/mod/forum/post.php?delete='.$post->id.'">'.$strdelete.'</a>';
    }

    if ($reply) {
        $commands[] = '<a href="'.$CFG->wwwroot.'/mod/forum/post.php?reply='.$post->id.'">'.$strreply.'</a>';
    }

    echo '<div class="commands">';
    echo implode(' | ', $commands);
    echo '</div>';


/// Ratings

    $ratingsmenuused = false;
    if (!empty($ratings) and !empty($USER->id)) {
        echo '<div class="ratings">';
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
                    echo '&nbsp;';
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
        echo '</div>';
    }

/// Link to post if required

    if ($link) {
        echo '<div class="link">';
        if ($post->replies == 1) {
            $replystring = get_string('repliesone', 'forum', $post->replies);
        } else {
            $replystring = get_string('repliesmany', 'forum', $post->replies);
        }
        echo '<a href="'.$CFG->wwwroot.'/mod/forum/discuss.php?d='.$post->discussion.'">'.
             get_string('discussthistopic', 'forum').'</a>&nbsp;('.$replystring.')';
        echo '</div>';
    }

    if ($footer) {
        echo '<div class="footer">'.$footer.'</div>';
    }
    echo '</td></tr></table>'."\n\n";

    if ($istracked && !$CFG->forum_usermarksread && !empty($post->forum)) {
        forum_tp_mark_post_read($USER->id, $post, $post->forum);
    }

    return $ratingsmenuused;
}
/**
* This function prints the overview of a discussion in the forum listing.
* It needs some discussion information and some post information, these
* happen to be combined for efficiency in the $post parameter by the function
* that calls this one: forum_print_latest_discussions()
* 
* @param object $post The post object (passed by reference for speed).
* @param object $forum The forum object.
* @param int $group Current group.
* @param string $datestring Format to use for the dates.
* @param boolean $cantrack Is tracking enabled for this forum.
* @param boolean $forumtracked Is the user tracking this forum.
*/
function forum_print_discussion_header(&$post, $forum, $group=-1, $datestring="", $cantrack=true, $forumtracked=true) {

    global $USER, $CFG;

    static $rowcount;
    static $strmarkalldread;

    if (!isset($rowcount)) {
        $rowcount = 0;
        $strmarkalldread = get_string('markalldread', 'forum');
    } else {
        $rowcount = ($rowcount + 1) % 2;
    }

    $post->subject = format_string($post->subject,true);

    echo "\n\n";
    echo '<tr class="discussion r'.$rowcount.'">';

    // Topic
    echo '<td class="topic starter">';
    echo '<a href="'.$CFG->wwwroot.'/mod/forum/discuss.php?d='.$post->discussion.'">'.$post->subject.'</a>';
    echo "</td>\n";

    // Picture
    echo '<td class="picture">';
    print_user_picture($post->userid, $forum->course, $post->picture);
    echo "</td>\n";

    // User name
    $fullname = fullname($post, isteacher($forum->course));
    echo '<td class="author">';
    echo '<a href="'.$CFG->wwwroot.'/user/view.php?id='.$post->userid.'&amp;course='.$forum->course.'">'.$fullname.'</a>';
    echo "</td>\n";

    // Group picture
    if ($group !== -1) {  // Groups are active - group is a group data object or NULL
        echo '<td class="picture group">';
        if (!empty($group->picture) and empty($group->hidepicture)) {
            print_group_picture($group, $forum->course, false, false, true);
        } else if (isset($group->id)) {
            echo '<a href="'.$CFG->wwwroot.'/user/index.php?id='.$forum->course.'&amp;group='.$group->id.'">'.$group->name.'</a>';
        }
        echo "</td>\n";
    }

    if ($forum->open or $forum->type == 'teacher') {   // Show the column with replies
        echo '<td class="replies">';
        echo '<a href="'.$CFG->wwwroot.'/mod/forum/discuss.php?d='.$post->discussion.'">';
        echo $post->replies.'</a>';
        echo "</td>\n";

        if ($cantrack) {
            echo '<td class="replies">';
            if ($forumtracked) {
                if ($post->unread > 0) {
                    echo '<span class="unread">';
                    echo '<a href="'.$CFG->wwwroot.'/mod/forum/discuss.php?d='.$post->discussion.'#unread">';
                    echo $post->unread;
                    echo '</a>';
                    echo '<a title="'.$strmarkalldread.'" href="'.$CFG->wwwroot.'/mod/forum/markposts.php?f='.
                         $forum->id.'&amp;d='.$post->discussion.'&amp;mark=read&amp;returnpage=view.php">' .
                         '<img src="'.$CFG->pixpath.'/t/clear.gif" height="11" width="11" /></a>';
                    echo '</span>';
                } else {
                    echo '<span class="read">';
                    echo '<a href="'.$CFG->wwwroot.'/mod/forum/discuss.php?d='.$post->discussion.'#unread">';
                    echo $post->unread;
                    echo '</a>';
                    echo '</span>';
                }
            } else {
                echo '<span class="read">';
                echo '-';
                echo '</span>';
            }
            echo "</td>\n";
        }
    }

    echo '<td class="lastpost">';
    $usedate = (empty($post->timemodified)) ? $post->modified : $post->timemodified;  // Just in case
    $parenturl = (empty($post->lastpostid)) ? '' : '&amp;parent='.$post->lastpostid;
    $usermodified->id        = $post->usermodified;
    $usermodified->firstname = $post->umfirstname;
    $usermodified->lastname  = $post->umlastname;
    echo '<a href="'.$CFG->wwwroot.'/user/view.php?id='.$post->usermodified.'&amp;course='.$forum->course.'">'.
         fullname($usermodified).'</a><br />';
    echo '<a href="'.$CFG->wwwroot.'/mod/forum/discuss.php?d='.$post->discussion.$parenturl.'">'.
          userdate($usedate, $datestring).'</a>';
    echo "</td>\n";

    echo "</tr>\n\n";

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
        $rating->rating = FORUM_UNSET_POST_RATING;
    }

    if (empty($strrate)) {
        $strrate = get_string("rate", "forum");
    }
    $scale = array(FORUM_UNSET_POST_RATING => $strrate.'...') + $scale;
    choose_from_menu($scale, $postid, $rating->rating, '');
}

function forum_print_mode_form($discussion, $mode) {
    GLOBAL $FORUM_LAYOUT_MODES;

    echo "<div align=\"center\">";
    popup_form("discuss.php?d=$discussion&amp;mode=", $FORUM_LAYOUT_MODES, "mode", $mode, "");
    echo "</div>\n";
}

function forum_search_form($course, $search='') {
    global $CFG;

    $output  = '<div class="forumsearchform">';
    $output .= '<form name="search" action="'.$CFG->wwwroot.'/mod/forum/search.php" style="display:inline">';
    $output .= '<input name="search" type="text" size="18" value="'.$search.'" alt="search" />';
    $output .= '<input value="'.get_string('searchforums', 'forum').'" type="submit" />';
    $output .= '<input name="id" type="hidden" value="'.$course->id.'" />';
    $output .= '</form>';
    $output .= helpbutton('search', get_string('search'), 'moodle', true, false, '', true);
    $output .= '</div>';

    return $output;
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

    require_once($CFG->dirroot.'/lib/uploadlib.php');

    $return = true;

    if ($posts = get_records_select("forum_posts", "discussion = '$discussion->id' AND attachment <> ''")) {
        foreach ($posts as $oldpost) {
            $oldpost->course = $discussion->course;
            $oldpost->forum = $discussion->forum;
            $oldpostdir = "$CFG->dataroot/".forum_file_area_name($oldpost);
            if (is_dir($oldpostdir)) {
                $newpost = $oldpost;
                $newpost->forum = $forumid;
                $newpostdir = forum_file_area_name($newpost);
                // take off the last directory because otherwise we're renaming to a directory that already exists
                // and this is unhappy in certain situations, eg over an nfs mount and potentially on windows too.
                make_upload_directory(substr($newpostdir,0,strrpos($newpostdir,'/')));
                $newpostdir = $CFG->dataroot.'/'.forum_file_area_name($newpost);
                $files = get_directory_list($oldpostdir); // get it before we rename it.
                if (! @rename($oldpostdir, $newpostdir)) {
                    $return = false;
                }
                foreach ($files as $file) {
                    clam_change_log($oldpostdir.'/'.$file,$newpostdir.'/'.$file);
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
            foreach ($files as $file) {
                $icon = mimeinfo("icon", $file);
                if ($CFG->slasharguments) {
                    $ffurl = "$CFG->wwwroot/file.php/$filearea/$file";
                } else {
                    $ffurl = "$CFG->wwwroot/file.php?file=/$filearea/$file";
                }
                $image = "<img border=\"0\" src=\"$CFG->pixpath/f/$icon\" height=\"16\" width=\"16\" alt=\"\" />";

                if ($return == "html") {
                    $output .= "<a href=\"$ffurl\">$image</a> ";
                    $output .= "<a href=\"$ffurl\">$file</a><br />";

                } else if ($return == "text") {
                    $output .= "$strattachment $file:\n$ffurl\n";

                } else {
                    if ($icon == "image.gif") {    // Image attachments don't get printed as links
                        $imagereturn .= "<br /><img src=\"$ffurl\" alt=\"\" />";
                    } else {
                        echo "<a href=\"$ffurl\">$image</a> ";
                        echo filter_text("<a href=\"$ffurl\">$file</a><br />");
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
/**
 * If successful, this function returns the name of the file
 * @param $post is a full post record, including course and forum
 * @param $newfile is a full upload array from $_FILES
 * @param $message is a string to hold the messages.
 */

function forum_add_attachment($post, $inputname,&$message) {

    global $CFG;

    if (!$forum = get_record("forum", "id", $post->forum)) {
        return "";
    }

    if (!$course = get_record("course", "id", $forum->course)) {
        return "";
    }

    require_once($CFG->dirroot.'/lib/uploadlib.php');
    $um = new upload_manager($inputname,true,false,$course,false,$forum->maxbytes,true,true);
    $dir = forum_file_area_name($post);
    if ($um->process_file_uploads($dir)) {
        $message .= $um->get_errors();
        return $um->get_new_filename();
    }
    $message .= $um->get_errors();
}

function forum_add_new_post($post,&$message) {

    global $USER, $CFG;

    $post->created = $post->modified = time();
    $post->mailed = "0";
    $post->userid = $USER->id;
    $post->attachment = "";

    if (! $post->id = insert_record("forum_posts", $post)) {
        return false;
    }

    if ($post->attachment = forum_add_attachment($post, 'attachment',$message)) {
        set_field("forum_posts", "attachment", $post->attachment, "id", $post->id);
    }

    // Update discussion modified date
    set_field("forum_discussions", "timemodified", $post->modified, "id", $post->discussion);
    set_field("forum_discussions", "usermodified", $post->userid, "id", $post->discussion);

    if (forum_tp_can_track_forums($post->forum) && forum_tp_is_tracked($post->forum)) {
        forum_tp_mark_post_read($post->userid, $post, $post->forum);
    }

    return $post->id;
}

function forum_update_post($post,&$message) {

    global $USER, $CFG;

    $post->modified = time();

    if (!$post->parent) {   // Post is a discussion starter - update discussion title too
        set_field("forum_discussions", "name", $post->subject, "id", $post->discussion);
    }

    if ($newfilename = forum_add_attachment($post, 'attachment',$message)) {
        $post->attachment = $newfilename;
    } else {
        unset($post->attachment);
    }

    // Update discussion modified date
    set_field("forum_discussions", "timemodified", $post->modified, "id", $post->discussion);
    set_field("forum_discussions", "usermodified", $post->userid, "id", $post->discussion);

    if (forum_tp_can_track_forums($post->forum) && forum_tp_is_tracked($post->forum)) {
        forum_tp_mark_post_read($post->userid, $post, $post->forum);
    }

    return update_record("forum_posts", $post);
}

function forum_add_discussion($discussion,&$message) {
// Given an object containing all the necessary data,
// create a new discussion and return the id

    GLOBAL $USER, $CFG;

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
    $post->mailnow     = $discussion->mailnow;

    if (! $post->id = insert_record("forum_posts", $post) ) {
        return 0;
    }

    if ($post->attachment = forum_add_attachment($post, 'attachment',$message)) {
        set_field("forum_posts", "attachment", $post->attachment, "id", $post->id); //ignore errors
    }

    // Now do the main entry for the discussion,
    // linking to this first post

    $discussion->firstpost    = $post->id;
    $discussion->timemodified = $timenow;
    $discussion->usermodified = $post->userid;
    $discussion->userid = $USER->id;

    if (! $post->discussion = insert_record("forum_discussions", $discussion) ) {
        delete_records("forum_posts", "id", $post->id);
        return 0;
    }

    // Finally, set the pointer on the post.
    if (! set_field("forum_posts", "discussion", $post->discussion, "id", $post->id)) {
        delete_records("forum_posts", "id", $post->id);
        delete_records("forum_discussions", "id", $post->discussion);
        return 0;
    }

    if (forum_tp_can_track_forums($post->forum) && forum_tp_is_tracked($post->forum)) {
        forum_tp_mark_post_read($post->userid, $post, $post->forum);
    }

    return $post->discussion;
}


function forum_delete_discussion($discussion, $fulldelete=false) {
// $discussion is a discussion record object

    $result = true;

    if ($posts = get_records("forum_posts", "discussion", $discussion->id)) {
        foreach ($posts as $post) {
            $post->course = $discussion->course;
            $post->forum  = $discussion->forum;
            if (! delete_records("forum_ratings", "post", "$post->id")) {
                $result = false;
            }
            if (! forum_delete_post($post, $fulldelete)) {
                $result = false;
            }
        }
    }

    forum_tp_delete_read_records(-1, -1, $discussion->id);

    if (! delete_records("forum_discussions", "id", "$discussion->id")) {
        $result = false;
    }

    return $result;
}


function forum_delete_post($post, $children=false) {
   if ($childposts = get_records('forum_posts', 'parent', $post->id)) {
       if ($children) {
           foreach ($childposts as $childpost) {
               forum_delete_post($childpost, true);
           }
       } else {
           return false;
       }
   }
   if (delete_records("forum_posts", "id", $post->id)) {
       delete_records("forum_ratings", "post", $post->id);  // Just in case

       forum_tp_delete_read_records(-1, $post->id);

       if ($post->attachment) {
           $discussion = get_record("forum_discussions", "id", $post->discussion);
           $post->course = $discussion->course;
           $post->forum  = $discussion->forum;
           forum_delete_old_attachments($post);
       }

   /// Just in case we are deleting the last post
       forum_discussion_update_last_post($post->discussion);

       return true;
   }
   return false;
}

function forum_count_replies($post, $children=true) {
    $count = 0;

    if ($children) {
        if ($childposts = get_records('forum_posts', 'parent', $post->id)) {
           foreach ($childposts as $childpost) {
               $count ++;                   // For this child
               $count += forum_count_replies($childpost, true);
           }
        }
    } else {
        $count += count_records('forum_posts', 'parent', $post->id);
    }

    return $count;
}


function forum_forcesubscribe($forumid, $value=1) {
    return set_field("forum", "forcesubscribe", $value, "id", $forumid);
}

function forum_is_forcesubscribed($forumid) {
    return (get_field("forum", "forcesubscribe", "id", $forumid) == 1);
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

    $info->name  = fullname($USER);
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

function forum_user_has_posted($forumid,$did,$userid) {
    return record_exists('forum_posts','discussion',$did,'userid',$userid);
}

function forum_user_can_post_discussion($forum, $currentgroup=false, $groupmode='', $edit=0) {
// $forum is an object
    global $CFG, $USER, $SESSION;
    if ($forum->type == "eachuser") {
        if ($edit) {
            $post = get_record('forum_posts','id',$edit);
            return ($post->userid == $USER->id || isadmin() && $CFG->admineditalways);
        } else {
            return (! forum_user_has_posted_discussion($forum->id, $USER->id));
        }
    } else if ($forum->type == 'qanda') {
        return isteacher($forum->course);
    } else if ($forum->type == "teacher") {
        return isteacher($forum->course);
    } else if ($currentgroup) {
        return (isteacheredit($forum->course) or (ismember($currentgroup) and $forum->open == 2));
    } else if (isteacher($forum->course)) {
        return true;
    } else {
        //else it might be group 0 in visible mode
        if ($groupmode == VISIBLEGROUPS){
            return ($forum->open == 2 AND ismember($currentgroup));
        }
        else {
            return ($forum->open == 2);
        }
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

//checks to see if a user can view a particular post
function forum_user_can_view_post($post, $course, $cm, $forum, $discussion, $user=NULL){

    global $CFG, $USER;

    if (!$user){
        $user = $USER;
    }

    if (isteacheredit($course->id)) {
        return true;   
    }

    if ($forum->type == 'teacher'){    //teacher type forum
        return isteacher($course->id);
    }

/// Make sure the user is allowed in the course
    if (!(isstudent($course->id) or 
          isteacher($course->id) or 
          ($course->id == SITEID && !$CFG->forcelogin) or 
          (isguest() && $course->guest) )){
        return false;
    }

/// If it's a grouped discussion, make sure the user is a member
    if ($discussion->groupid > 0) {
        if ($cm->groupmode == SEPARATEGROUPS) {
            return ismember($discussion->groupid);
        }
    }
    return true;
}

function forum_user_can_see_discussion($forum,$discussion,$user=NULL) {
    global $USER;

    if (empty($user) || empty($user->id)) {
        $user = $USER;
    }

    // retrive objects (yuk)
    if (is_numeric($forum)) {
        if (!$forum = get_record('forum','id',$forum)) {
            return false;
        }
    }
    if (is_numeric($discussion)) {
        if (!$discussion = get_record('forum_discussions','id',$discussion)) {
            return false;
        }
    }
    if ($forum->type == 'qanda') {
        return (forum_user_has_posted($forum->id,$discussion->id,$user->id) || isteacher($forum->course));
    }
    return true;
}
    

function forum_user_can_see_post($forum,$discussion,$post,$user=NULL) {
    global $USER;

    if (empty($user) || empty($user->id)) {
        $user = $USER;
    }

    // retrive objects (yuk)
    if (is_numeric($forum)) {
        if (!$forum = get_record('forum','id',$forum)) {
            return false;
        }
    }
    if (isteacher($forum->course)) {
        return true;
    }
    if (is_numeric($discussion)) {
        if (!$discussion = get_record('forum_discussions','id',$discussion)) { 
            return false;
        }
    }
    if (is_numeric($post)) {
        if (!$post = get_record('forum_posts','id',$post)) {
            return false;
        }
    }
    
    if (!isset($post->id) && isset($post->parent)) {
        $post->id = $post->parent;
    }
    
    if ($forum->type == 'qanda') {
        $firstpost = forum_get_firstpost_from_discussion($discussion->id);
        return (forum_user_has_posted($forum->id,$discussion->id,$user->id) || $firstpost->id == $post->id || isteacher($forum->course));
    }
    return true;
}


/**
* Prints the discussion view screen for a forum.
* 
* @param object $course The current course object.
* @param object $forum Forum to be printed.
* @param int $maxdiscussions The maximum number of discussions per page(optional).
* @param string $displayformat The display format to use (optional). 
* @param string $sort Sort arguments for database query (optional).
* @param int $currentgroup Group to display discussions for (optional).
* @param int $groupmode Group mode of the forum (optional).
* @param int $page Page mode, page to display (optional).
* 
*/
function forum_print_latest_discussions($course, $forum, $maxdiscussions=5, $displayformat='plain', $sort='',
                                        $currentgroup=-1, $groupmode=-1, $page=-1) {
    global $CFG, $USER;

/// Sort out some defaults

    if ((!$maxdiscussions) && ($displayformat == 'plain')) {
        $displayformat = 'header';  // Abbreviate display by default
    }

    $fullpost = false;
    if ($displayformat == 'plain') {
        $fullpost = true;
    }


/// Decide if current user is allowed to see ALL the current discussions or not

/// First check the group stuff

    if ($groupmode == -1) {    /// We need to reconstruct groupmode because none was given
        if ($cm = get_coursemodule_from_instance('forum', $forum->id, $course->id)) {
            $groupmode = groupmode($course, $cm);
        } else {
            $groupmode = SEPARATEGROUPS;
        }
    }

    if ($currentgroup == -1) {    /// We need to reconstruct currentgroup because none was given
        $currentgroup = get_current_group($course->id);
    }

    if (!$currentgroup and ($groupmode != SEPARATEGROUPS or isteacheredit($course->id)) ) {
        $visiblegroups = -1;
    } else {
        $visiblegroups = $currentgroup;
    }

/// If the user can post discussions, then this is a good place to put the button for it
    //add group mode in there, to test for visible group
    if (forum_user_can_post_discussion($forum, $currentgroup, $groupmode)) {
        echo '<div class="singlebutton forumaddnew">';
        echo "<form name=\"newdiscussionform\" method=\"get\" action=\"$CFG->wwwroot/mod/forum/post.php\">";
        echo "<input type=\"hidden\" name=\"forum\" value=\"$forum->id\" />";
        echo '<input type="submit" value="';
        echo ($forum->type == 'news') ? get_string('addanewtopic', 'forum') 
            : (($forum->type == 'qanda') 
               ? get_string('addanewquestion','forum') 
               : get_string('addanewdiscussion', 'forum'));
        echo '" />';
        echo '</form>';
        echo "</div>\n";
    }


/// Get all the recent discussions we're allowed to see

    $getuserlastmodified = ($displayformat == 'header');

    if (! $discussions = forum_get_discussions($forum->id, $sort, 0, $fullpost, $visiblegroups,0,$getuserlastmodified) ) {
        echo '<div class="forumnodiscuss">';
        if ($forum->type == 'news') {
            echo '('.get_string('nonews', 'forum').')';
        } else if ($forum->type == 'qanda') {
            echo '('.get_string('noquestions','forum').')';
        } else {
            echo '('.get_string('nodiscussions', 'forum').')';
        }
        echo "</div>\n";
        return;
    }

/// If no discussions then don't use paging (to avoid some divide by 0 errors)

    if ($maxdiscussions <= 0) {
        $page = -1;
        $maxdiscussions = 0;
    }

/// If we want paging

    if ($page != -1) {
        ///Get the number of discussions found
        $numdiscussions = count($discussions);

        ///Show the paging bar
        print_paging_bar($numdiscussions, $page, $maxdiscussions, "view.php?f=$forum->id&");

        //Calculate the page "window"
        $pagestart = ($page * $maxdiscussions) + 1;
        $pageend  = $pagestart + $maxdiscussions - 1;
    }


    $replies = forum_count_discussion_replies($forum->id);

    $canreply = forum_user_can_post($forum);


    $discussioncount = 0;
    $olddiscussionlink = false;
    $strdatestring = get_string('strftimerecentfull');

    /// Check if the forum is tracked.
    if ($cantrack = forum_tp_can_track_forums($forum)) {
        $forumtracked = forum_tp_is_tracked($forum);
    } else {
        $forumtracked = false;
    }

    if ($displayformat == 'header') {
        echo '<table cellspacing="0" class="forumheaderlist">';
        echo '<thead>';
        echo '<tr>';
        echo '<th class="header topic">'.get_string('discussion', 'forum').'</th>';
        echo '<th class="header author" colspan="2">'.get_string('startedby', 'forum').'</th>';
        if ($groupmode > 0) {
            echo '<th class="header group">'.get_string('group').'</th>';
        }
        if ($forum->open or $forum->type == 'teacher') {
            echo '<th class="header replies">'.get_string('replies', 'forum').'</th>';
            /// If the forum can be tracked, display the unread column.
            if ($cantrack) {
                echo '<th class="header replies">'.get_string('unread', 'forum');
                if ($forumtracked) {
                    echo '&nbsp;<a title="'.get_string('markallread', 'forum').
                         '" href="'.$CFG->wwwroot.'/mod/forum/markposts.php?f='.
                         $forum->id.'&amp;mark=read&amp;returnpage=view.php">'.
                         '<img src="'.$CFG->pixpath.'/t/clear.gif" height="11" width="11" border="0" /></a>';
                }
                echo '</th>';
            }
        }
        echo '<th class="header lastpost">'.get_string('lastpost', 'forum').'</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
    }

    foreach ($discussions as $discussion) {
        $discussioncount++;

        if ($page != -1) {     // We are using paging
            if ($discussioncount < $pagestart) {  // Not there yet
                continue;
            }
            if ($discussioncount > $pageend) {    // All done, finish the loop
                break;
            }
        //Without paging, old approach
        } else if ($maxdiscussions && ($discussioncount > $maxdiscussions)) {
            $olddiscussionlink = true;
            break;
        }

        if (!empty($replies[$discussion->discussion])) {
            $discussion->replies = $replies[$discussion->discussion]->replies;
            $discussion->lastpostid = $replies[$discussion->discussion]->lastpostid;
        } else {
            $discussion->replies = 0;
        }

        /// SPECIAL CASE: The front page can display a news item post to non-logged in users.
        /// All posts are read in this case.
        if (!$forumtracked) {
            $discussion->unread = '-';
        } else if (empty($USER)) {
            $discussion->unread = 0;
        } else {
            $discussion->unread = forum_tp_count_discussion_unread_posts($USER->id, $discussion->discussion);
        }

        if (!empty($USER->id)) {
            $ownpost = ($discussion->userid == $USER->id);
        } else {
            $ownpost=false;
        }
        // Use discussion name instead of subject of first post
        $discussion->subject = $discussion->name;

        switch ($displayformat) {
            case 'header':
                if ($groupmode > 0) {
                    if (isset($groups[$discussion->groupid])) {
                        $group = $groups[$discussion->groupid];
                    } else {
                        $group = $groups[$discussion->groupid] = get_record('groups', 'id', $discussion->groupid);
                    }
                } else {
                    $group = -1;
                }
                forum_print_discussion_header($discussion, $forum, $group, $strdatestring, $cantrack, $forumtracked);
            break;
            default:
                if ($canreply or $discussion->replies) {
                    $link = true;
                } else {
                    $link = false;
                }

                $discussion->forum = $forum->id;

                forum_print_post($discussion, $course->id, $ownpost, $reply=0, $link, $assessed=false);
            break;
        }
    }

    if ($displayformat == "header") {
        echo '</tbody>';
        echo '</table>';
    }

    if ($olddiscussionlink) {
        echo '<div class="forumolddiscuss">';
        echo '<a href="'.$CFG->wwwroot.'/mod/forum/view.php?f='.$forum->id.'&amp;showall=1">';
        echo get_string('olderdiscussions', 'forum').'</a> ...</div>';
    }

    if ($page != -1) { ///Show the paging bar
        print_paging_bar($numdiscussions, $page, $maxdiscussions, "view.php?f=$forum->id&");
    }
}


function forum_print_discussion($course, $forum, $discussion, $post, $mode, $canreply=NULL) {

    global $USER, $CFG;

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
            $ratings->allow = (($forum->assessed != 2 or isteacher($course->id)) && !isguest());

            if ($ratings->allow) {
                echo '<form name="form" method="post" action="rate.php">';
                echo '<input type="hidden" name="id" value="'.$course->id.'" />';
            }
        }
    }

    $post->forum = $forum->id;   // Add the forum id to the post object, later used by forum_print_post
    $post->forumtype = $forum->type;

    $post->subject = format_string($post->subject);

    if (forum_tp_can_track_forums($forum)) {
        if ($forumtracked = forum_tp_is_tracked($forum)) {
            $user_read_array = forum_tp_get_discussion_read_records($USER->id, $post->discussion);
        } else {
            $user_read_array = array();
        }
    } else {
        $forumtracked = false;
        $user_read_array = array();
    }

    if (forum_print_post($post, $course->id, $ownpost, $reply, $link=false, $ratings,
                         '', '', (!$forumtracked || isset($user_read_array[$post->id]) || forum_tp_is_post_old($post)))) {
        $ratingsmenuused = true;
    }

    switch ($mode) {
        case FORUM_MODE_FLATOLDEST :
        case FORUM_MODE_FLATNEWEST :
        default:
            if (forum_print_posts_flat($post->discussion, $course->id, $mode, $ratings, $reply,
                                       $user_read_array, $post->forum)) {
                $ratingsmenuused = true;
            }
            break;

        case FORUM_MODE_THREADED :
            if (forum_print_posts_threaded($post->id, $course->id, 0, $ratings, $reply,
                                           $user_read_array, $post->forum)) {
                $ratingsmenuused = true;
            }
            break;

        case FORUM_MODE_NESTED :
            if (forum_print_posts_nested($post->id, $course->id, $ratings, $reply,
                                         $user_read_array, $post->forum)) {
                $ratingsmenuused = true;
            }
            break;
    }

    if ($ratingsmenuused) {
        echo "<center><input type=\"submit\" value=\"".get_string("sendinratings", "forum")."\" />";
        if ($forum->scale < 0) {
            if ($scale = get_record("scale", "id", abs($forum->scale))) {
                print_scale_menu_helpbutton($course->id, $scale );
            }
        }
        echo "</center>";
        echo "</form>";
    }
}


function forum_print_posts_flat($discussion, $courseid, $direction, $ratings, $reply, &$user_read_array, $forumid=0) {
    global $USER, $CFG;

    $link  = false;
    $ratingsmenuused = false;

    if ($direction < 0) {
        $sort = "ORDER BY created DESC";
    } else {
        $sort = "ORDER BY created ASC";
    }

    if ($posts = forum_get_discussion_posts($discussion, $sort, $forumid)) {
        foreach ($posts as $post) {

            $post->subject = format_string($post->subject);

            $ownpost = ($USER->id == $post->userid);
            if (forum_print_post($post, $courseid, $ownpost, $reply, $link, $ratings,
                                 '', '', (isset($user_read_array[$post->id]) || forum_tp_is_post_old($post)))) {
                $ratingsmenuused = true;
            }
        }
    }

    return $ratingsmenuused;
}


function forum_print_posts_threaded($parent, $courseid, $depth, $ratings, $reply, &$user_read_array, $forumid=0) {
    global $USER, $CFG;

    $link  = false;
    $ratingsmenuused = false;

    $istracking = forum_tp_can_track_forums($forumid) && forum_tp_is_tracked($forumid);

    if ($posts = forum_get_child_posts($parent, $forumid)) {
        foreach ($posts as $post) {

            echo '<div class="indent">';
            if ($depth > 0) {
                $ownpost = ($USER->id == $post->userid);

                $post->subject = format_string($post->subject);

                if (forum_print_post($post, $courseid, $ownpost, $reply, $link, $ratings,
                                     '', '', (isset($user_read_array[$post->id]) || forum_tp_is_post_old($post)))) {
                    $ratingsmenuused = true;
                }
            } else {
                if (!forum_user_can_see_post($post->forum,$post->discussion,$post)) {
                    continue;
                }
                $by->name = fullname($post, isteacher($courseid));
                $by->date = userdate($post->modified);

                if ($istracking) {
                    if (isset($user_read_array[$post->id]) || forum_tp_is_post_old($post)) {
                        $style = '<span class="forumthread read">';
                    } else {
                        $style = '<span class="forumthread unread">';
                    }
                } else {
                    $style = '<span class="forumthread">';
                }
                echo $style."<a name=\"$post->id\"></a>".
                     "<a href=\"discuss.php?d=$post->discussion&amp;parent=$post->id\">".format_string($post->subject,true)."</a> ";
                print_string("bynameondate", "forum", $by);
                echo "</span>";
            }

            if (forum_print_posts_threaded($post->id, $courseid, $depth-1, $ratings, $reply,
                                           $user_read_array, $forumid)) {
                $ratingsmenuused = true;
            }
            echo "</div>\n";
        }
    }
    return $ratingsmenuused;
}

function forum_print_posts_nested($parent, $courseid, $ratings, $reply, &$user_read_array, $forumid=0) {
    global $USER, $CFG;

    $link  = false;
    $ratingsmenuused = false;

    if ($posts = forum_get_child_posts($parent, $forumid)) {
        foreach ($posts as $post) {

            echo '<div class="indent">';
            if (empty($USER->id)) {
                $ownpost = false;
            } else {
                $ownpost = ($USER->id == $post->userid);
            }
 
            $post->subject = format_string($post->subject);

            if (forum_print_post($post, $courseid, $ownpost, $reply, $link, $ratings,
                                 '', '', (isset($user_read_array[$post->id]) || forum_tp_is_post_old($post)))) {
                $ratingsmenuused = true;
            }
            if (forum_print_posts_nested($post->id, $courseid, $ratings, $reply, $user_read_array, $forumid)) {
                $ratingsmenuused = true;
            }
            echo "</div>\n";
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
                              ORDER BY p.discussion ASC,p.created ASC");

    if (empty($posts)) {
        return;
    }

    $isteacheredit = isteacheredit($courseid);

    foreach ($posts as $post) {

        if ($groupid and ($post->groupid != -1 and $groupid != $post->groupid and !$isteacheredit)) {
            continue;
        }

        $tmpactivity = new Object;

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

    echo "<tr><td class=\"forumpostpicture\" width=\"35\" valign=\"top\">";
    print_user_picture($activity->user->userid, $course, $activity->user->picture);
    echo "</td><td>$openformat";

    if ($detail) {
        echo "<img src=\"$CFG->modpixpath/$activity->type/icon.gif\" ".
             "height=\"16\" width=\"16\" alt=\"".strip_tags(format_string($activity->name,true))."\" />  ";
    }
    echo "<a href=\"$CFG->wwwroot/mod/forum/discuss.php?d=" . $activity->content->discussion
         . "#" . $activity->content->id . "\">";

    echo format_string($activity->content->subject,true);
    echo "</a>$closeformat";

    echo "<br /><font size=\"2\">";
    echo "<a href=\"$CFG->wwwroot/user/view.php?id=" . $activity->user->userid . "&amp;course=" . "$course\">"
         . $activity->user->fullname . "</a>";
    echo " - " . userdate($activity->timestamp) . "</font></td></tr>";
    echo "</table>";

    return;
}

function forum_change_discussionid($postid, $discussionid) {
/// recursively sets the discussion field to $discussionid on $postid and all its children
/// used when pruning a post
    set_field('forum_posts', 'discussion', $discussionid, 'id', $postid);
    if ($posts = get_records('forum_posts', 'parent', $postid)) {
        foreach ($posts as $post) {
            forum_change_discussionid($post->id, $discussionid);
        }
    }
    return true;
}

function forum_update_subscriptions_button($courseid, $forumid) {
// Prints the editing button on subscribers page
    global $CFG, $USER;

    if (isteacher($courseid)) {
        if (!empty($USER->subscriptionsediting)) {
            $string = get_string("turneditingoff");
            $edit = "off";
        } else {
            $string = get_string("turneditingon");
            $edit = "on";
        }
        return "<form target=\"$CFG->framename\" method=\"get\" action=\"$CFG->wwwroot/mod/forum/subscribers.php\">".
               "<input type=\"hidden\" name=\"id\" value=\"$forumid\" />".
               "<input type=\"hidden\" name=\"edit\" value=\"$edit\" />".
               "<input type=\"submit\" value=\"$string\" /></form>";
    }
}

function forum_add_user($userid, $courseid) {
/// Add subscriptions for new users
    if ($forums = get_records_select('forum', "course = '$courseid' AND forcesubscribe = '".FORUM_INITIALSUBSCRIBE."'")) {
        foreach ($forums as $forum) {
            forum_subscribe($userid, $forum->id);
        }
    }
    if ($forums = get_records_select('forum', "course = '".SITEID."' AND forcesubscribe = '".FORUM_INITIALSUBSCRIBE."'")) {
        foreach ($forums as $forum) {
            forum_subscribe($userid, $forum->id);
        }
    }
}

/// Functions to do with read tracking.

function forum_tp_add_read_record($userid, $postid, $discussionid=-1, $forumid=-1) {
    if (($readrecord = forum_tp_get_read_records($userid, $postid)) === false) {
        /// New read record
        unset($readrecord);
        $readrecord->userid = $userid;
        $readrecord->postid = $postid;
        $readrecord->discussionid = $discussionid;
        $readrecord->forumid = $forumid;
        $readrecord->firstread = time();
        $readrecord->lastread = $readrecord->firstread;
        return insert_record('forum_read', $readrecord, true);

    } else {
        /// Update read record
        $readrecord = reset($readrecord);
        $readrecord->lastread = time();

        $update = NULL;
        $update->id = $readrecord->id;
        $update->lastread = $readrecord->lastread;

        /// This shouldn't happen, but just in case...
        if (!$readrecord->firstread) {
            /// Update the 'firstread' field.
            $update->firstread = $readrecord->lastread;
        }
        if ($discussionid > -1) {
            /// Update the 'discussionid' field.
            $update->discussionid = $discussionid;
        }
        if ($forumid > -1) {
            /// Update the 'forumid' field.
            $update->forumid = $forumid;
        }

        return update_record('forum_read', $update);
    }
}

function forum_tp_get_read_records($userid=-1, $postid=-1, $discussionid=-1, $forumid=-1) {
    /// Returns all records in the 'forum_read' table matching the passed keys, indexed
    /// by userid.
    $select = '';
    if ($userid > -1) {
        if ($select != '') $select .= ' AND ';
        $select .= 'userid = \''.$userid.'\'';
    }
    if ($postid > -1) {
        if ($select != '') $select .= ' AND ';
        $select .= 'postid = \''.$postid.'\'';
    }
    if ($discussionid > -1) {
        if ($select != '') $select .= ' AND ';
        $select .= 'discussionid = \''.$discussionid.'\'';
    }
    if ($forumid > -1) {
        if ($select != '') $select .= ' AND ';
        $select .= 'forumid = \''.$forumid.'\'';
    }

    return get_records_select('forum_read', $select);
}

function forum_tp_get_discussion_read_records($userid, $discussionid) {
    /// Returns all read records for the provided user and discussion, indexed by postid.
    $select = 'userid = \''.$userid.'\' AND discussionid = \''.$discussionid.'\'';
    $fields = 'postid, firstread, lastread';
    return get_records_select('forum_read', $select, '', $fields);
}

function forum_tp_mark_post_read($userid, &$post, $forumid) {
/// If its an old post, do nothing. If the record exists, the maintenance will clear it up later.
    if (!forum_tp_is_post_old($post)) {
        return forum_tp_add_read_record($userid, $post->id, $post->discussion, $forumid);
    } else {
        return true;
    }
}

function forum_tp_mark_forum_read($userid, $forumid, $groupid=false) {
/// Marks a whole forum as read, for a given user
    global $CFG;

    $cutoffdate = isset($CFG->forum_oldpostdays) ? (time() - ($CFG->forum_oldpostdays*24*60*60)) : 0;

    $groupsel = '';
    if ($groupid !== false) {
        $groupsel = ' AND (d.groupid = '.$groupid.' OR d.groupid = -1)';
    }

    $sql = 'SELECT p.id as postid, d.id as discussionid, d.forum as forumid '.
           'FROM '.$CFG->prefix.'forum_posts p '.
           'LEFT JOIN '.$CFG->prefix.'forum_discussions d ON p.discussion = d.id '.
           'LEFT JOIN '.$CFG->prefix.'forum_read r ON r.postid = p.id AND r.userid = '.$userid.' '.
           'WHERE d.forum = '.$forumid.$groupsel.
                ' AND p.modified >= '.$cutoffdate.' AND r.id is NULL';

    if ($posts = get_records_sql($sql)) {
        foreach ($posts as $post) {
            forum_tp_add_read_record($userid, $post->postid, $post->discussionid, $post->forumid);
        }
        return true;
    }
}

function forum_tp_mark_discussion_read($userid, $discussionid, $forumid) {
/// Marks a whole discussion as read, for a given user
    global $CFG;

    $cutoffdate = isset($CFG->forum_oldpostdays) ? (time() - ($CFG->forum_oldpostdays*24*60*60)) : 0;

    $sql = 'SELECT p.id as postid, p.discussion as discussionid '.
           'FROM '.$CFG->prefix.'forum_posts p '.
           'LEFT JOIN '.$CFG->prefix.'forum_read r ON r.postid = p.id AND r.userid = '.$userid.' '.
           'WHERE p.discussion = '.$discussionid.' '.
                'AND p.modified >= '.$cutoffdate.' AND r.id is NULL';

    if ($posts = get_records_sql($sql)) {
        foreach ($posts as $post) {
            forum_tp_add_read_record($userid, $post->postid, $post->discussionid, $forumid);
        }
        return true;
    }
}

function forum_tp_is_post_read($userid, &$post) {
    return (forum_tp_is_post_old($post) ||
            (get_record('forum_read', 'userid', $userid, 'postid', $post->id) !== false));
}

function forum_tp_is_post_old(&$post, $time=null) {
    global $CFG;

    if (is_null($time)) $time = time();
    return ($post->modified < ($time - ($CFG->forum_oldpostdays * 24 * 3600)));
}

function forum_tp_count_discussion_read_records($userid, $discussionid) {
    /// Returns the count of records for the provided user and discussion.
    global $CFG;

    $cutoffdate = isset($CFG->forum_oldpostdays) ? (time() - ($CFG->forum_oldpostdays*24*60*60)) : 0;

    $sql = 'SELECT COUNT(DISTINCT p.id) '.
           'FROM '.$CFG->prefix.'forum_discussions d '.
           'LEFT JOIN '.$CFG->prefix.'forum_read r ON d.id = r.discussionid AND r.userid = '.$userid.' '.
           'LEFT JOIN '.$CFG->prefix.'forum_posts p ON p.discussion = d.id '.
                'AND (p.modified < '.$cutoffdate.' OR p.id = r.postid) '.
           'WHERE d.id = '.$discussionid;

    return (count_records_sql($sql));
}

function forum_tp_count_discussion_unread_posts($userid, $discussionid) {
    /// Returns the count of records for the provided user and discussion.
    global $CFG;

    $cutoffdate = isset($CFG->forum_oldpostdays) ? (time() - ($CFG->forum_oldpostdays*24*60*60)) : 0;

    $sql = 'SELECT COUNT(p.id) '.
           'FROM '.$CFG->prefix.'forum_posts p '.
           'LEFT JOIN '.$CFG->prefix.'forum_read r ON r.postid = p.id AND r.userid = '.$userid.' '.
           'WHERE p.discussion = '.$discussionid.' '.
                'AND p.modified >= '.$cutoffdate.' AND r.id is NULL';

    return (count_records_sql($sql));
}

function forum_tp_count_forum_posts($forumid, $groupid=false) {
    /// Returns the count of posts for the provided forum and [optionally] group.
    global $CFG;

    $sql = 'SELECT COUNT(*) '.
           'FROM '.$CFG->prefix.'forum_posts fp,'.$CFG->prefix.'forum_discussions fd '.
           'WHERE fd.forum = '.$forumid.' AND fp.discussion = fd.id';
    if ($groupid !== false) {
        $sql .= ' AND (fd.groupid = '.$groupid.' OR fd.groupid = -1)';
    }
    $count = count_records_sql($sql);


    return $count;
}

function forum_tp_count_forum_read_records($userid, $forumid, $groupid=false) {
    /// Returns the count of records for the provided user and forum and [optionally] group.
    global $CFG;

    $cutoffdate = isset($CFG->forum_oldpostdays) ? (time() - ($CFG->forum_oldpostdays*24*60*60)) : 0;

    $groupsel = '';
    if ($groupid !== false) {
        $groupsel = ' AND (d.groupid = '.$groupid.' OR d.groupid = -1)';
    }

    if ($CFG->dbtype === 'postgres7') {
        // this query takes 20ms, vs several minutes for the one below
        $sql = " SELECT COUNT (DISTINCT u.id ) " 
            .  " FROM ( "
            .  "   SELECT  p.id " 
            .  "   FROM  {$CFG->prefix}forum_posts p "
            .  "       JOIN {$CFG->prefix}forum_discussions d ON p.discussion = d.id "
            .  "       JOIN {$CFG->prefix}forum_read r ON p.id = r.postid"
            .  "   WHERE d.forum = $forumid $groupsel "
            .  "         AND r.userid= $userid"
            .  "   UNION"
            .  "   SELECT  p.id"
            .  "   FROM  {$CFG->prefix}forum_posts p "
            .  "         JOIN {$CFG->prefix}forum_discussions d ON p.discussion = d.id "
            .  "   WHERE d.forum = $forumid $groupsel "
            .  "         AND p.modified < $cutoffdate"
            .  ") u";
   } else {
        $sql = 'SELECT COUNT(DISTINCT p.id) '.
            'FROM '.$CFG->prefix.'forum_posts p,'.$CFG->prefix.'forum_read r,'.$CFG->prefix.'forum_discussions d '.
            'WHERE d.forum = '.$forumid.$groupsel.' AND p.discussion = d.id AND '.
            '((p.id = r.postid AND r.userid = '.$userid.') OR p.modified < '.$cutoffdate.' ) ';
    }
    return (count_records_sql($sql));
}

function forum_tp_count_forum_unread_posts($userid, $forumid, $groupid=false) {
    /// Returns the count of records for the provided user and forum and [optionally] group.
    global $CFG;

    $cutoffdate = isset($CFG->forum_oldpostdays) ? (time() - ($CFG->forum_oldpostdays*24*60*60)) : 0;

    $groupsel = '';
    if ($groupid !== false) {
        $groupsel = ' AND (d.groupid = '.$groupid.' OR d.groupid = -1)';
    }

    $sql = 'SELECT COUNT(p.id) '.
           'FROM '.$CFG->prefix.'forum_posts p '.
           'LEFT JOIN '.$CFG->prefix.'forum_discussions d ON p.discussion = d.id '.
           'LEFT JOIN '.$CFG->prefix.'forum_read r ON r.postid = p.id AND r.userid = '.$userid.' '.
           'WHERE d.forum = '.$forumid.$groupsel.
                ' AND p.modified >= '.$cutoffdate.' AND r.id is NULL';

    return (count_records_sql($sql));
}

function forum_tp_delete_read_records($userid=-1, $postid=-1, $discussionid=-1, $forumid=-1) {
/// Deletes read records for the specified index. At least one parameter must be specified.
    $select = '';
    if ($userid > -1) {
        if ($select != '') $select .= ' AND ';
        $select .= 'userid = \''.$userid.'\'';
    }
    if ($postid > -1) {
        if ($select != '') $select .= ' AND ';
        $select .= 'postid = \''.$postid.'\'';
    }
    if ($discussionid > -1) {
        if ($select != '') $select .= ' AND ';
        $select .= 'discussionid = \''.$discussionid.'\'';
    }
    if ($forumid > -1) {
        if ($select != '') $select .= ' AND ';
        $select .= 'forumid = \''.$forumid.'\'';
    }
    if ($select == '') {
        return false;
    }
    else {
        return delete_records_select('forum_read', $select);
    }
}
/**
* Get a list of forums not tracked by the user.
* 
* @param int $userid The id of the user to use.
* @param int $courseid The id of the course being checked (optional).
* @return mixed An array indexed by forum id, or false.
*/
function forum_tp_get_untracked_forums($userid, $courseid=false) {
    global $CFG;

    /// If a course is specified, get the forums with tracking turned off.
    if ($courseid !== false) {
        $select = 'course = '.$courseid.' AND trackingtype = '.FORUM_TRACKING_OFF;
        $forced = get_records_select('forum', $select, '', 'id,course');
    } else {
        $forced = false;
    }

    /// Get the forums that the user has turned off.
    $sql = 'SELECT ft.forumid, ft.userid '.
           'FROM '.$CFG->prefix.'forum_track_prefs ft, '.$CFG->prefix.'forum f '.
           'WHERE ft.userid = '.$userid.' AND f.id = ft.forumid ' .
                'AND f.trackingtype != '.FORUM_TRACKING_ON;
    $useroff = get_records_sql($sql);
    if (!$forced) {
        return $useroff;
    } else if (!$useroff) {
        return $forced;
    } else {
        return ($useroff + $forced);
    }
}

/**
* Determine if a user can track forums and optionally a particular forum.
* Checks the site settings, the user settings and the forum settings (if
* requested).
*
* @param mixed $forum The forum object to test, or the int id (optional).
* @param mixed $userid The user object to check for (optional).
* @return boolean
*/
function forum_tp_can_track_forums($forum=false, $user=false) {
    global $USER, $CFG;

    // if possible, avoid expensive
    // queries
    if (empty($CFG->forum_trackreadposts)) {
        return false;
    }

    if ($user === false) {
        /// Must be logged in and not a guest.
        $isauser = isloggedin() && !isguest();
        $user = $USER;
    } else {
        $isauser = true;
    }

    if ($forum === false) {
        $forumallows = true;
        $forumforced = false;
    } else {
        /// Work toward always passing an object...
        if (is_numeric($forum)) {
            $forum = get_record('forum', 'id', $forum, '','','','', 'id,trackingtype');
        }

        $forumallows = ($forum->trackingtype == FORUM_TRACKING_OPTIONAL);
        $forumforced = ($forum->trackingtype == FORUM_TRACKING_ON);
    }

    return ($isauser && ($forumforced || ($forumallows && !empty($user->trackforums))));
}

/**
* Tells whether a specific forum is tracked by the user. A user can optionally
* be specified. If not specified, the current user is assumed.
* 
* @param mixed $forum If int, the id of the forum being checked; if object, the forum object
* @param int $userid The id of the user being checked (optional).
* @return boolean
*/
function forum_tp_is_tracked($forum, $userid=false) {
    global $USER, $CFG;

    if ($userid === false) {
        if (empty($USER->id)) {
            return false;
        }
        $userid = $USER->id;
    }

    /// Work toward always passing an object...
    if (is_numeric($forum)) {
        $forum = get_record('forum', 'id', $forum);
    }

    return (($forum->trackingtype == FORUM_TRACKING_ON) ||
            ($forum->trackingtype == FORUM_TRACKING_OPTIONAL &&
             get_record('forum_track_prefs', 'userid', $userid, 'forumid', $forum->id) === false));
}

function forum_tp_start_tracking($forumid, $userid=false) {
    global $USER;

    if ($userid === false) {
        $userid = $USER->id;
    }

    return delete_records('forum_track_prefs', 'userid', $userid, 'forumid', $forumid);
}

function forum_tp_stop_tracking($forumid, $userid=false) {
    global $USER;

    if ($userid === false) {
        $userid = $USER->id;
    }

    $track_prefs = new stdClass;
    $track_prefs->userid = $userid;
    $track_prefs->forumid = $forumid;
    if (insert_record('forum_track_prefs', $track_prefs)) {
        return forum_tp_delete_read_records($userid, -1, -1, $forumid);
    } else {
        return false;
    }
}


/// Clean old records from the forum_read table.
function forum_tp_clean_read_records() {
    global $CFG;

/// Look for records older than the cutoffdate that are still in the forum_read table.
    $cutoffdate = isset($CFG->forum_oldpostdays) ? (time() - ($CFG->forum_oldpostdays*24*60*60)) : 0;
    $sql = 'SELECT fr.id, fr.userid, fr.postid '.
           'FROM '.$CFG->prefix.'forum_posts fp, '.$CFG->prefix.'forum_read fr '.
           'WHERE fp.modified < '.$cutoffdate.' AND fp.id = fr.postid';
    if (($oldreadposts = get_records_sql($sql))) {
        foreach($oldreadposts as $oldreadpost) {
            delete_records('forum_read', 'id', $oldreadpost->id);
        }
    }
}    

/**
 * Sets the last post for a given discussion
 **/
function forum_discussion_update_last_post($discussionid) {
    global $CFG, $db;

/// Check the given discussion exists
    if (!record_exists('forum_discussions', 'id', $discussionid)) {
        return false;
    }
    
/// Use SQL to find the last post for this discussion
    $sql = 'SELECT id, userid, modified '.
           'FROM '.$CFG->prefix.'forum_posts '.
           'WHERE discussion='.$discussionid.' '.
           'ORDER BY modified DESC ';

/// Lets go find the last post
    if (($lastpost = get_record_sql($sql, true))) {
        $discussionobject = new Object;
        $discussionobject->id = $discussionid;
        $discussionobject->usermodified = $lastpost->userid;
        $discussionobject->timemodified = $lastpost->modified;
        if (update_record('forum_discussions', $discussionobject)) {
            return $lastpost->id;
        }
    }

/// To get here either we couldn't find a post for the discussion (weird)
/// or we couldn't update the discussion record (weird x2)
    return false;
}


function forum_get_view_actions() {
    return array('view discussion','search','forum','forums','subscribers');
}

function forum_get_post_actions() {
    return array('add discussion','add post','delete discussion','delete post','move discussion','prune post','update post');
}

///this function returns all the separate forum ids, given a courseid
//@ param int $courseid
//@ return array
function forum_get_separate_modules($courseid) {

    global $CFG,$db;
    $forummodule = get_record("modules", "name", "forum");
    
    $sql = 'SELECT f.id, f.id FROM '.$CFG->prefix.'forum f, '.$CFG->prefix.'course_modules cm WHERE
           f.id = cm.instance AND cm.module ='.$forummodule->id.' AND cm.visible = 1 AND cm.course = '.$courseid.'
           AND cm.groupmode ='.SEPARATEGROUPS;

    return get_records_sql($sql);

}

function forum_check_throttling($forum) {
    global $USER,$CFG;

    if (is_numeric($forum)) {
        $forum = get_record('forum','id',$forum);
    }
    if (!is_object($forum)) {
        return false;  // this is broken.
    }
    
    if (empty($forum->blockafter)) {
        return true;
    }
    
    if (empty($forum->blockperiod)) {
        return true;
    }
    
    if (isteacher($forum->course)) {
        return true;
    }

    // get the number of posts in the last period we care about
    $timenow = time();
    $timeafter = $timenow - $forum->blockperiod;
    
    $numposts = count_records_sql('SELECT COUNT(p.id) FROM '.$CFG->prefix.'forum_posts p'
                                  .' JOIN '.$CFG->prefix.'forum_discussions d'
                                  .' ON p.discussion = d.id WHERE d.forum = '.$forum->id
                                  .' AND p.userid = '.$USER->id.' AND p.created > '.$timeafter);

    $a->blockafter = $forum->blockafter;
    $a->numposts = $numposts;
    $a->blockperiod = get_string('secondstotime'.$forum->blockperiod);
    
    if ($forum->blockafter <= $numposts) {
        error(get_string('forumblockingtoomanyposts','error',$a),$CFG->wwwroot.'/mod/forum/view.php?f='.$forum->id);
    }
    if ($forum->warnafter <= $numposts) {
        notify(get_string('forumblockingalmosttoomanyposts','forum',$a));
    }

    
}


//This function is used by the remove_course_userdata function in moodlelib.
//If this function exists, remove_course_userdata will execute it.
//This function will remove all posts from the specified forum.
function forum_delete_userdata($data, $showfeedback=true) {
    global $CFG;

    if ($CFG->dbtype == 'postgres7') {
        echo "Forum reset not yet supported for PostgreSQL";
        return; 
    }

    $sql = 'DELETE FROM '.$CFG->prefix.'forum_posts
                  USING '.$CFG->prefix.'forum_discussions fd, 
                        '.$CFG->prefix.'forum_posts fp, 
                        '.$CFG->prefix.'forum f
            WHERE fp.discussion=fd.id and f.course='.$data->courseid.' AND f.id=fd.forum';

    $strreset = get_string('reset');

    if (!empty($data->reset_forum_news)) {
        $select = "$sql and f.type = 'news' ";
        if (execute_sql($select, false) and $showfeedback) {
            notify($strreset.': '.get_string('namenews','forum'), 'notifysuccess');
        }
    }
    if (!empty($data->reset_forum_teacher)) {
        $select = "$sql and f.type = 'teacher' ";
        if (execute_sql($select, false) and $showfeedback) {
            notify($strreset.': '.get_string('nameteacher','forum'), 'notifysuccess');
        }
    }
    if (!empty($data->reset_forum_single)) {
        $select = "$sql and f.type = 'single' and fp.parent > 0 ";
        if (execute_sql($select, false) and $showfeedback) {
            notify($strreset.': '.get_string('singleforum','forum'), 'notifysuccess');
        }
    }
    if (!empty($data->reset_forum_eachuser)) {
        $select = "$sql and f.type = 'eachuser' ";
        if (execute_sql($select, false) and $showfeedback) {
            notify($strreset.': '.get_string('eachuserforum','forum'), 'notifysuccess');
        }
    }
    if (!empty($data->reset_forum_general)) {
        $select = "$sql and f.type = 'general' ";
        if (execute_sql($select, false) and $showfeedback) {
            notify($strreset.': '.get_string('generalforum','forum'), 'notifysuccess');
        }
    }
    if (!empty($data->reset_forum_subscriptions)) {
        $subscripsql = "DELETE FROM {$CFG->prefix}forum_subscriptions
                              USING {$CFG->prefix}forum_subscriptions fs,
                                    {$CFG->prefix}forum f
                              WHERE fs.forum = f.id
                                AND f.course = {$data->courseid}";

        if (execute_sql($subscripsql, false) and $showfeedback) {
            notify($strreset.': '.get_string('resetsubscriptions','forum'), 'notifysuccess');
        }
    }
}

// Called by course/reset.php

function forum_reset_course_form($course) {
    global $CFG;

    if ($CFG->dbtype == 'postgres7') {
        echo "Forum reset not yet supported for PostgreSQL";
        return; 
    }

    echo get_string('resetforums', 'forum'); echo ':<br />';
    print_checkbox('reset_forum_news', 1, true, get_string('namenews','forum'), '', '');  echo '<br />';
    print_checkbox('reset_forum_teacher', 1, true, get_string('nameteacher','forum'), '', '');  echo '<br />';
    print_checkbox('reset_forum_single', 1, true, get_string('singleforum','forum'), '', '');  echo '<br />';
    print_checkbox('reset_forum_eachuser', 1, true, get_string('eachuserforum','forum'), '', '');  echo '<br />';
    print_checkbox('reset_forum_general', 1, true, get_string('generalforum','forum'), '', '');  echo '<br />';
    echo '<p>';
    print_checkbox('reset_forum_subscriptions', 1, true, get_string('resetsubscriptions','forum'), '', '');
    echo '</p>';
}

?>