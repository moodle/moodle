<?php // $Id$

//  Edit and save a new post to a discussion

    require_once('../../config.php');
    require_once('lib.php');

    $reply = optional_param('reply', 0, PARAM_INT);
    $forum = optional_param('forum', 0, PARAM_INT);
    $edit = optional_param('edit', 0, PARAM_INT);
    $delete = optional_param('delete', 0, PARAM_INT);
    $prune = optional_param('prune',0,PARAM_INT);
    $name = optional_param('name','',PARAM_CLEAN);
    $confirm = optional_param('confirm',0,PARAM_INT);

    if (isguest()) {
        $wwwroot = $CFG->wwwroot.'/login/index.php';
        if (!empty($CFG->loginhttps)) {
            $wwwroot = str_replace('http:','https:', $wwwroot);
        }

        if (!empty($forum)) {      // User is starting a new discussion in a forum
            if (! $forum = get_record('forum', 'id', $forum)) {
                error('The forum number was incorrect');
            }
        } else if (!empty($reply)) {      // User is writing a new reply
            if (! $parent = forum_get_post_full($reply)) {
                error('Parent post ID was incorrect');
            }
            if (! $discussion = get_record('forum_discussions', 'id', $parent->discussion)) {
                error('This post is not part of a discussion!');
            }
            if (! $forum = get_record('forum', 'id', $discussion->forum)) {
                error('The forum number was incorrect');
            }
        }
        if (! $course = get_record('course', 'id', $forum->course)) {
            error('The course number was incorrect');
        }
        if (!$cm = get_coursemodule_from_instance('forum', $forum->id, $course->id)) { // For the logs
            $cm->id = 0;
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
                  <a href=\"view.php?f=$forum->id\">".format_string($forum->name)."</a>", '', '', true, "", navmenu($course, $cm));
        }
        notice_yesno(get_string('noguestpost', 'forum').'<br /><br />'.get_string('liketologin'),
                     $wwwroot, $_SERVER['HTTP_REFERER']);
        print_footer($course);
        exit;
    }

    require_login(0, false);   // Script is useless unless they're logged in

    if ($post = data_submitted()) {
        if (! $forum = get_record('forum', 'id', $forum)) {
            error('The forum number was incorrect');
        }

        if (!$course = get_record('course', 'id', $forum->course)) {
            error('Could not find specified course!');
        }

        require_login($course->id, false);
        $adminedit = (isadmin() and !empty($CFG->admineditalways));

        if (!empty($course->lang)) {           // Override current language
            $CFG->courselang = $course->lang;
        }

        if (empty($SESSION->fromurl)) {
            $errordestination = "$CFG->wwwroot/mod/forum/view.php?f=$forum->id";
        } else {
            $errordestination = $SESSION->fromurl;
        }

        $post->subject = clean_param(strip_tags($post->subject, '<lang><span>'), PARAM_CLEAN); // Strip all tags except multilang

        //$post->message will be cleaned later before display

        $post->attachment = isset($_FILES['attachment']) ? $_FILES['attachment'] : NULL;

        if (!$cm = get_coursemodule_from_instance("forum", $forum->id, $course->id)) { // For the logs
            $cm->id = 0;
        }

        if (($post->subject == '') or ($post->message == '')) {
            $post->error = get_string("emptymessage", "forum");

        } else if ($post->edit) {
/// Updating a post
           if (! $oldpost = forum_get_post_full($post->edit)) {
	            error("Post ID was incorrect");
	        }
	        if (($oldpost->userid <> $USER->id) and !$adminedit) {
	            error("You can't edit other people's posts!");
	        }
	        if (! $discussion = get_record("forum_discussions", "id", $oldpost->discussion)) {
	            error("This post is not part of a discussion!");
	        }
	        if ($discussion->forum != $forum->id) {
	            error("The forum number is incorrect");
	        }
	        if ($discussion->course != $course->id) {
	            error("The course number is incorrect");
	        }
	        if (!($forum->type == 'news' && !$oldpost->parent && $discussion->timestart > time())) {
	            if (((time() - $oldpost->created) > $CFG->maxeditingtime) and !$adminedit) {
	                error( get_string("maxtimehaspassed", "forum", format_time($CFG->maxeditingtime)) );
	            }
	        }

            $updatepost = new object;
            $updatepost->id = $oldpost->id;
            $updatepost->parent = $oldpost->parent;
            $updatepost->forum = $oldpost->forum;
            $updatepost->discussion = $oldpost->discussion;
            $updatepost->userid = $oldpost->userid;

            $updatepost->subject = $post->subject; //already cleaned
            $updatepost->message = $post->message; //cleaning only before display
            $updatepost->format = $post->format;
            $updatepost->attachment = $post->attachment;

            $updatepost->course = $course->id;
            $updatepost->subscribe = optional_param('subscribe', 0, PARAM_BOOL);
            $updatepost->unsubscribe = optional_param('unsubscribe', 0, PARAM_BOOL);

            $message = '';

            if (get_field('forum', 'type', 'id', $forum->id) == 'news' && !$oldpost->parent) {
                $updatediscussion = new object;
                $updatediscussion->id = $oldpost->discussion;
                if (empty($post->timestartdisabled)) {
                    $updatediscussion->timestart = make_timestamp($post->timestartyear, $post->timestartmonth, $post->timestartday);
                } else {
                    $updatediscussion->timestart = 0;
                }
                if (empty($post->timeenddisabled)) {
                    $updatediscussion->timeend = make_timestamp($post->timeendyear, $post->timeendmonth, $post->timeendday);
                } else {
                    $updatediscussion->timeend = 0;
                }
                if (empty($post->timeenddisabled) && $updatediscussion->timeend <= $updatediscussion->timestart) {
                    $post->error = get_string('timestartenderror', 'forum');
                } elseif (!update_record('forum_discussions', $updatediscussion)) {
                    error(get_string("couldnotupdate", "forum"), $errordestination);
                }
            }

            if (!isset($post->error)) {

                if (forum_update_post($updatepost,$message)) {

                    add_to_log($course->id, "forum", "update post",
                            "discuss.php?d=$updatepost->discussion&amp;parent=$updatepost->id", "$updatepost->id", $cm->id);

                    $timemessage = 2;
                    if (!empty($message)) { // if we're printing stuff about the file upload
                        $timemessage = 4;
                    }
                    $message .= '<br />'.get_string("postupdated", "forum");

                    if ($subscribemessage = forum_post_subscription($updatepost)) {
                        $timemessage = 4;
                    }
                    redirect(forum_go_back_to("discuss.php?d=$updatepost->discussion#$updatepost->id"), $message.$subscribemessage, $timemessage);

                } else {
                    error(get_string("couldnotupdate", "forum"), $errordestination);
                }
                exit;

            }
         } else if ($post->discussion) {
/// Adding a new post to an existing discussion
	        if (! $discussion = get_record("forum_discussions", "id", $post->discussion)) {
	            error("This post is not part of a discussion!");
	        }
	        if ($discussion->forum != $forum->id) {
	            error("The forum number is incorrect");
	        }
	        if ($discussion->course != $course->id) {
	            error("The course number is incorrect");
	        }
	        if (! $parent = forum_get_post_full($post->parent)) {
	            error("Parent post does not exist");
	        }
            if ($parent->discussion != $discussion->id) {
	            error("Parent not in this discussion");
            }
            if (! forum_user_can_post($forum)) {
                error("Sorry, but you can not post in this forum.");
            }


            $newpost = new object;
            $newpost->parent = $post->parent;
            $newpost->forum = $forum->id;
            $newpost->discussion = $discussion->id;
            $newpost->parent = $parent->id;

            $newpost->subject = $post->subject; //already cleaned
            $newpost->message = $post->message; //cleaning only before display
            $newpost->format = $post->format;
            $newpost->mailnow = optional_param('mailnow', 0, PARAM_BOOL);

            $newpost->course = $course->id;
            $newpost->subscribe = optional_param('subscribe', 0, PARAM_BOOL);
            $newpost->unsubscribe = optional_param('unsubscribe', 0, PARAM_BOOL);

            $message = '';

            if ($newpost->id = forum_add_new_post($newpost,$message)) {

                add_to_log($course->id, "forum", "add post",
                          "discuss.php?d=$newpost->discussion&amp;parent=$newpost->id", "$newpost->id", $cm->id);

                $timemessage = 2;
                if (!empty($message)) { // if we're printing stuff about the file upload
                    $timemessage = 4;
                }
                $message .= '<br />'.get_string("postadded", "forum", format_time($CFG->maxeditingtime));

                if ($subscribemessage = forum_post_subscription($newpost)) {
                    $timemessage = 4;
                }

                if ($newpost->mailnow) {
                    $message .= get_string("postmailnow", "forum");
                    $timemessage = 4;
                }

                redirect(forum_go_back_to("discuss.php?d=$newpost->discussion#$newpost->id"), $message.$subscribemessage, $timemessage);

            } else {
                error(get_string("couldnotadd", "forum"), $errordestination);
            }
            exit;

        } else {
/// Adding a new discussion
            if (! forum_user_can_post_discussion($forum)) {
                error("Sorry, but you can not post a new discussion in this forum.");
            }

            $discussion = new object;
            $discussion->forum = $forum->id;
            $discussion->course = $course->id;

            $discussion->mailnow = optional_param('mailnow', 0, PARAM_BOOL);
            $discussion->name = $post->subject;
            $discussion->intro = $post->message;
            $discussion->format = $post->format;
            $discussion->groupid = get_current_group($course->id);
            if (isteacheredit($course->id) and $discussion->groupid == 0) {
                $discussion->groupid = -1;
            }

            $discussion->course = $course->id;
            $discussion->subscribe = optional_param('subscribe', 0, PARAM_BOOL);
            $discussion->unsubscribe = optional_param('unsubscribe', 0, PARAM_BOOL);

            if (! forum_user_can_post_discussion($forum)) {
                error("Sorry, but you can not post a new discussion in this forum.");
            }

            $newstopic = false;
            if (get_field('forum', 'type', 'id', $forum->id) == 'news') {
                $newstopic = true;
            }
            if ($newstopic && empty($post->timestartdisabled)) {
                $discussion->timestart = make_timestamp($post->timestartyear, $post->timestartmonth, $post->timestartday);
            } else {
                $discussion->timestart = 0;
            }
            if ($newstopic && empty($post->timeenddisabled)) {
                $discussion->timeend = make_timestamp($post->timeendyear, $post->timeendmonth, $post->timeendday);
            } else {
                $discussion->timeend = 0;
            }
            if ($newstopic && empty($post->timeenddisabled) && $discussion->timeend <= $discussion->timestart) {
                $post->error = get_string('timestartenderror', 'forum');
            } else {
                $message = '';
                if ($discussion->id = forum_add_discussion($discussion,$message)) {

                    add_to_log($course->id, "forum", "add discussion",
                            "discuss.php?d=$discussion->id", "$discussion->id", $cm->id);

                    $timemessage = 2;
                    if (!empty($message)) { // if we're printing stuff about the file upload
                        $timemessage = 4;
                    }
                    $message .= '<br />'.get_string("postadded", "forum", format_time($CFG->maxeditingtime));

                    if ($discussion->mailnow) {
                        $message .= get_string("postmailnow", "forum");
                        $timemessage = 4;
                    }

                    if ($subscribemessage = forum_post_subscription($discussion)) {
                        $timemessage = 4;
                    }

                    redirect(forum_go_back_to("view.php?f=$discussion->forum"), $message.$subscribemessage, $timemessage);

                } else {
                    error(get_string("couldnotadd", "forum"), $errordestination);
                }

                exit;
            }
        }
    }

    if ($usehtmleditor = can_use_html_editor()) {
        $defaultformat = FORMAT_HTML;
    } else {
        $defaultformat = FORMAT_MOODLE;
    }

    if (!empty($post->error)) {
/// User is re-editing a failed posting

        // Set up all the required objects again, and reuse the same $post

        if (! $forum = get_record("forum", "id", $post->forum)) {
            error("The forum number was incorrect ($post->forum)");
        }

        if (! $course = get_record("course", "id", $forum->course)) {
            error("The course number was incorrect ($forum->course)");
        }

        if (!empty($post->parent)) {
            if (! $parent = forum_get_post_full($post->parent)) {
                error("Parent post ID was incorrect ($post->parent)");
            }
        }

        if (!empty($post->discussion)) {
            if (! $discussion = get_record("forum_discussions", "id", $post->discussion)) {
                error("This post is not part of a discussion! ($post->discussion)");
            }
        } else {
            $discussion = new stdClass();
            $newstopic = false;
            if ($forum->type == 'news' && !$post->parent) {
                $newstopic = true;
            }
            if ($newstopic && empty($post->timestartdisabled)) {
                $discussion->timestart = make_timestamp($post->timestartyear, $post->timestartmonth, $post->timestartday);
            } else {
                $discussion->timestart = 0;
            }
            if ($newstopic && empty($post->timeenddisabled)) {
                $discussion->timeend = make_timestamp($post->timeendyear, $post->timeendmonth, $post->timeendday);
            } else {
                $discussion->timeend = 0;
            }
        }

    } else if (!empty($forum)) {
/// User is starting a new discussion in a forum

        if (!empty($_SERVER["HTTP_REFERER"])) {
            $SESSION->fromurl = $_SERVER["HTTP_REFERER"];
        } else {
            $SESSION->fromurl = '';
        }

        if (! $forum = get_record("forum", "id", $forum)) {
            error("The forum number was incorrect ($forum)");
        }
        if (! $course = get_record("course", "id", $forum->course)) {
            error("The course number was incorrect ($forum->course)");
        }

        if (! forum_user_can_post_discussion($forum)) {
            error("Sorry, but you can not post a new discussion in this forum.");
        }

        if ($cm = get_coursemodule_from_instance("forum", $forum->id, $course->id)) {
            if (!$cm->visible and !isteacher($course->id)) {
                error(get_string("activityiscurrentlyhidden"));
            }
        }

        // Load up the $post variable.

        $post->course = $course->id;
        $post->forum  = $forum->id;
        $post->discussion = 0;           // ie discussion # not defined yet
        $post->parent = 0;
        $post->subject = "";
        $post->userid = $USER->id;
        $post->message = "";
        $post->format = $defaultformat;

        forum_set_return();

    } else if (!empty($reply)) {
/// User is writing a new reply

        if (! $parent = forum_get_post_full($reply)) {
            error("Parent post ID was incorrect");
        }
        if (! $discussion = get_record("forum_discussions", "id", $parent->discussion)) {
            error("This post is not part of a discussion!");
        }
        if (! $forum = get_record("forum", "id", $discussion->forum)) {
            error("The forum number was incorrect ($discussion->forum)");
        }
        if (! $course = get_record("course", "id", $discussion->course)) {
            error("The course number was incorrect ($discussion->course)");
        }

        if (! forum_user_can_post($forum)) {
            error("Sorry, but you can not post in this forum.");
        }

        if ($cm = get_coursemodule_from_instance("forum", $forum->id, $course->id)) {
            if (groupmode($course, $cm) and !isteacheredit($course->id)) {   // Make sure user can post here
                $mygroupid = mygroupid($course->id);
                if (!((empty($mygroupid) and $discussion->groupid == -1) || (ismember($discussion->groupid)/*$mygroupid == $discussion->groupid*/))) {
                    error("Sorry, but you can not post in this discussion.");
                }
            }
            if (!$cm->visible and !isteacher($course->id)) {
                error(get_string("activityiscurrentlyhidden"));
            }
        }

        // Load up the $post variable.

        $post->course  = $course->id;
        $post->forum  = $forum->id;
        $post->discussion  = $parent->discussion;
        $post->parent = $parent->id;
        $post->subject = $parent->subject;
        $post->userid = $USER->id;
        $post->message = "";
        $post->format = $defaultformat;

        $strre = get_string('re', 'forum');
        if (!(substr($post->subject, 0, strlen($strre)) == $strre)) {
            $post->subject = $strre.' '.$post->subject;
        }

        unset($SESSION->fromdiscussion);

    } else if (!empty($edit)) {
/// User is editing their own post

        $adminedit = (isadmin() and !empty($CFG->admineditalways));

        if (! $post = forum_get_post_full($edit)) {
            error("Post ID was incorrect");
        }
        if (($post->userid <> $USER->id) and !$adminedit) {
            error("You can't edit other people's posts!");
        }
        if ($post->parent) {
            if (! $parent = forum_get_post_full($post->parent)) {
                error("Parent post ID was incorrect ($post->parent)");
            }
        }
        if (! $discussion = get_record("forum_discussions", "id", $post->discussion)) {
            error("This post is not part of a discussion! ($reply)");
        }
        if (! $forum = get_record("forum", "id", $discussion->forum)) {
            error("The forum number was incorrect ($discussion->forum)");
        }
        if (!($forum->type == 'news' && !$post->parent && $discussion->timestart > time())) {
            if (((time() - $post->created) > $CFG->maxeditingtime) and !$adminedit) {
                error( get_string("maxtimehaspassed", "forum", format_time($CFG->maxeditingtime)),
                       "$CFG->wwwroot/mod/forum/discuss.php?d=$discussion->id#$post->id" );
            }
        }
        if (! $course = get_record("course", "id", $discussion->course)) {
            error("The course number was incorrect ($discussion->course)");
        }

        // Load up the $post variable.

        $post->edit = $edit;

        $post->course  = $course->id;
        $post->forum  = $forum->id;

        unset($SESSION->fromdiscussion);


    } else if (!empty($delete)) {
/// User is deleting a post

        if (! $post = forum_get_post_full($delete)) {
            error("Post ID was incorrect");
        }
        if (! $discussion = get_record("forum_discussions", "id", $post->discussion)) {
            error("This post is not part of a discussion!");
        }
        if (! $forum = get_record("forum", "id", $discussion->forum)) {
            error("The forum number was incorrect ($discussion->forum)");
        }
        if (($post->userid <> $USER->id) and !isteacher($forum->course)) {
            error("You can't delete other people's posts!");
        }
        if (!empty($forum->course)) {
            if ($course = get_record('course', 'id', $forum->course)) {
                if (!empty($course->lang)) {
                    $CFG->courselang = $course->lang;
                }
            }
        }

        $replycount = forum_count_replies($post);

        if (!empty($confirm) and confirm_sesskey()) {    // User has confirmed the delete

            if ($post->totalscore) {
                notice(get_string("couldnotdeleteratings", "forum"),
                        forum_go_back_to("discuss.php?d=$post->discussion"));

            } else if ($replycount && !isteacher($course->id)) {
                error(get_string("couldnotdeletereplies", "forum"),
                        forum_go_back_to("discuss.php?d=$post->discussion"));

            } else {
                if (!$cm = get_coursemodule_from_instance("forum", $forum->id, $forum->course)) { // For the logs
                    $cm->id = 0;
                }
                if (! $post->parent) {  // post is a discussion topic as well, so delete discussion
                    if ($forum->type == "single") {
                        notice("Sorry, but you are not allowed to delete that discussion!",
                                forum_go_back_to("discuss.php?d=$post->discussion"));
                    }
                    forum_delete_discussion($discussion);

                    add_to_log($discussion->course, "forum", "delete discussion",
                               "view.php?id=$cm->id", "$forum->id", $cm->id);

                    redirect("view.php?f=$discussion->forum",
                             get_string("deleteddiscussion", "forum"), 1);

                } else if (forum_delete_post($post, isteacher($course->id))) {

                    add_to_log($discussion->course, "forum", "delete post",
                               "discuss.php?d=$post->discussion", "$post->id", $cm->id);

                    $feedback = $replycount ? get_string('deletedposts', 'forum') : get_string('deletedpost', 'forum');
                    redirect(forum_go_back_to("discuss.php?d=$post->discussion"), $feedback, 1);
                } else {
                    error("An error occurred while deleting record $post->id");
                }
            }


        } else {
// User just asked to delete something

            forum_set_return();

            if ($replycount) {
                if (!isteacher($course->id)) {
                    error(get_string("couldnotdeletereplies", "forum"),
                          forum_go_back_to("discuss.php?d=$post->discussion"));
                }
                print_header();
                notice_yesno(get_string("deletesureplural", "forum", $replycount+1),
                             "post.php?delete=$delete&amp;confirm=$delete&amp;sesskey=".sesskey(),
                             $_SERVER["HTTP_REFERER"]);

                forum_print_post($post, $course->id, $ownpost=false, $reply=false, $link=false);
                if (empty($post->edit)) {
                    if (forum_tp_can_track_forums($forum) && forum_tp_is_tracked($forum)) {
                        $user_read_array = forum_tp_get_discussion_read_records($USER->id, $discussion->id);
                    } else {
                        $user_read_array = array();
                    }
                    forum_print_posts_nested($post->id, $course->id, false, false, $user_read_array, $forum->id);
                }
            } else {
                print_header();
                notice_yesno(get_string("deletesure", "forum", $replycount),
                             "post.php?delete=$delete&amp;confirm=$delete&amp;sesskey=".sesskey(),
                             $_SERVER["HTTP_REFERER"]);
                forum_print_post($post, $forum->course, $ownpost=false, $reply=false, $link=false);
            }

        }
        print_footer($course);
        die;


    } else if (!empty($prune)) {
// Teacher is pruning

        if (!$post = forum_get_post_full($prune)) {
            error("Post ID was incorrect");
        }
        if (!$discussion = get_record("forum_discussions", "id", $post->discussion)) {
            error("This post is not part of a discussion!");
        }
        if (!$forum = get_record("forum", "id", $discussion->forum)) {
            error("The forum number was incorrect ($discussion->forum)");
        }
        if ($forum->type == 'single') {
            error('Discussions from this forum cannot be split');
        }
        if (!isteacher($forum->course)) {
            error("You can't split discussions!");
        }
        if (!$post->parent) {
            error('This is already the first post in the discussion');
        }
        if (!$cm = get_coursemodule_from_instance("forum", $forum->id, $forum->course)) { // For the logs
            $cm->id = 0;
        }

        if (!empty($name) and confirm_sesskey()) {    // User has confirmed the prune

            $newdiscussion = new object;
            $newdiscussion->course = $discussion->course;
            $newdiscussion->forum = $discussion->forum;
            $newdiscussion->name = strip_tags($name, '<lang><span>'); // Strip all tags except multilang
            $newdiscussion->firstpost = $post->id;
            $newdiscussion->userid = $discussion->userid;
            $newdiscussion->groupid = $discussion->groupid;
            $newdiscussion->assessed = $discussion->assessed;
            $newdiscussion->usermodified = $post->userid;
            $newdiscussion->timestart = $discussion->timestart;
            $newdiscussion->timeend = $discussion->timeend;

            if (!$newid = insert_record('forum_discussions', $newdiscussion)) {
                error('Could not create new discussion');
            }

            $newpost->id = $post->id;
            $newpost->parent = 0;
            $newpost->subject = $newdiscussion->name;

            if (!update_record("forum_posts", $newpost)) {
                error('Could not update the original post');
            }

            forum_change_discussionid($post->id, $newid);

            // update last post in each discussion
            forum_discussion_update_last_post($discussion->id);
            forum_discussion_update_last_post($newid);

            add_to_log($discussion->course, "forum", "prune post",
                           "discuss.php?d=$newid", "$post->id", $cm->id);

            redirect(forum_go_back_to("discuss.php?d=$newid"), get_string("prunedpost", "forum"), 1);

        } else { // User just asked to prune something

            $course = get_record('course', 'id', $forum->course);
            $strforums = get_string("modulenameplural", "forum");
            print_header_simple(format_string($discussion->name).": ".format_string($post->subject), "",
                         "<a href=\"../forum/index.php?id=$course->id\">$strforums</a> ->
                          <a href=\"view.php?f=$forum->id\">".format_string($forum->name,true)."</a> ->
                          <a href=\"discuss.php?d=$discussion->id\">".format_string($post->subject,true)."</a> -> ".
                          get_string("prune", "forum"), '', "", true, "", navmenu($course, $cm));

            print_heading(get_string('pruneheading', 'forum'));
            echo '<center>';

            include('prune.html');

            forum_print_post($post, $forum->course, $ownpost=false, $reply=false, $link=false);
            echo '</center>';
        }
        print_footer($course);
        die;


    } else {
        error("No operation specified");

    }


    // To get here they need to edit a post, and the $post
    // variable will be loaded with all the particulars,
    // so bring up the form.

    // $course, $forum are defined.  $discussion is for edit and reply only.

    $cm = get_coursemodule_from_instance("forum", $forum->id, $course->id);

    require_login($course->id, false, $cm);


    if ($post->discussion) {
        if (! $toppost = get_record("forum_posts", "discussion", $post->discussion, "parent", 0)) {
            error("Could not find top parent of post $post->id");
        }
    } else {
        $toppost->subject = ($forum->type == "news") ? get_string("addanewtopic", "forum") :
                                                       get_string("addanewdiscussion", "forum");
    }

    if (empty($post->subject)) {
        $formstart = "theform.subject";
    } else {
        $formstart = "";
    }

    if ($post->parent) {
        $navtail = "<a href=\"discuss.php?d=$discussion->id\">".format_string($toppost->subject,true)."</a> -> ".get_string("editing", "forum");
    } else {
        $navtail = format_string($toppost->subject);
    }

    if (empty($post->edit)) {
        $post->edit = "";
    }

    $strforums = get_string("modulenameplural", "forum");


    $navmiddle = "<a href=\"../forum/index.php?id=$course->id\">$strforums</a> -> <a href=\"view.php?f=$forum->id\">".format_string($forum->name,true)."</a>";

    if (empty($discussion->name)) {
        if (empty($discussion)) {
            $discussion = new object;
        }
        $discussion->name = $forum->name;
    }

    if ($course->category) {
        print_header("$course->shortname: ".format_string($discussion->name).": ".format_string($toppost->subject), "$course->fullname",
                 "<a href=\"../../course/view.php?id=$course->id\">$course->shortname</a> ->
                  $navmiddle -> $navtail", $formstart, "", true, "", navmenu($course, $cm));

    } else {
        print_header("$course->shortname: ".format_string($discussion->name).": ".format_string($toppost->subject), "$course->fullname",
                 "$navmiddle -> $navtail", "$formstart", "", true, "", navmenu($course, $cm));

    }

// checkup
    if (!empty($parent) && !forum_user_can_see_post($forum,$discussion,$post)) {
        error("You cannot reply to this post");
    }
    if (empty($parent) && !forum_user_can_post_discussion($forum, false, '', $edit)) {
        if (!$edit) {
            error("You cannot start a new discussion in this forum");
        } else {
            error("You cannot edit this post");
        }
    }

    if ($forum->type == 'qanda' && !isteacher($forum->course) && !forum_user_has_posted($forum->id,$discussion->id,$USER->id)) {
        notify(get_string('qandanotify','forum'));
    }

    forum_check_throttling($forum);

    if (!empty($parent)) {
        forum_print_post($parent, $course->id, $ownpost=false, $reply=false, $link=false);
        if (empty($post->edit)) {
            if (forum_tp_can_track_forums($forum) && forum_tp_is_tracked($forum)) {
                $user_read_array = forum_tp_get_discussion_read_records($USER->id, $discussion->id);
            } else {
                $user_read_array = array();
            }
            if ($forum->type != 'qanda' || forum_user_can_see_discussion($forum,$discussion)) {
                forum_print_posts_threaded($parent->id, $course->id, 0, false, false, $user_read_array, $discussion->forum);
            }
        }
        print_heading(get_string("yourreply", "forum").':');
    } else {
        $forum->intro = trim($forum->intro);
        if (!empty($forum->intro)) {
            print_simple_box(format_text($forum->intro), 'center');
        }
        if ($forum->type == 'qanda') {
            print_heading(get_string('yournewquestion','forum'));
        } else {
            print_heading(get_string('yournewtopic', 'forum'));
        }
    }
    echo '<center>';
    if (!empty($post->error)) {
        notify($post->error);
    }
    echo '</center>';

    if ($USER->id != $post->userid) {   // Not the original author, so add a message to the end
        $data->date = userdate($post->modified);
        if ($post->format == FORMAT_HTML) {
            $data->name = '<a href="'.$CFG->wwwroot.'/user/view.php?id='.$USER->id.'&course='.$post->course.'">'.
                           fullname($USER).'</a>';
            $post->message .= '<p>(<span class="edited">'.get_string('editedby', 'forum', $data).'</span>)</p>';
        } else {
            $data->name = fullname($USER);
            $post->message .= "\n\n(".get_string('editedby', 'forum', $data).')';
        }
    }

    print_simple_box_start("center");
    require("post.html");
    print_simple_box_end();

    if ($usehtmleditor) {
        use_html_editor("message");
    }

    print_footer($course);


?>
