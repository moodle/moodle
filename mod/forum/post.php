<?php // $Id$

//  Edit and save a new post to a discussion

    require_once('../../config.php');
    require_once('lib.php');

    $reply   = optional_param('reply', 0, PARAM_INT);
    $forum   = optional_param('forum', 0, PARAM_INT);
    $edit    = optional_param('edit', 0, PARAM_INT);
    $delete  = optional_param('delete', 0, PARAM_INT);
    $prune   = optional_param('prune', 0, PARAM_INT);
    $name    = optional_param('name', '', PARAM_CLEAN);
    $confirm = optional_param('confirm', 0, PARAM_INT);
    $groupid = optional_param('groupid', null, PARAM_INT);


    //these page_params will be passed as hidden variables later in the form.
    $page_params = array('reply'=>$reply, 'forum'=>$forum, 'edit'=>$edit);

    $sitecontext = get_context_instance(CONTEXT_SYSTEM);

    if (has_capability('moodle/legacy:guest', $sitecontext, NULL, false)) {

        $wwwroot = $CFG->wwwroot.'/login/index.php';
        if (!empty($CFG->loginhttps)) {
            $wwwroot = str_replace('http:', 'https:', $wwwroot);
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
            error('Could not get the course module for the forum instance.');
        } else {
            $modcontext = get_context_instance(CONTEXT_MODULE, $cm->id);
        }

        if (!get_referer()) {   // No referer - probably coming in via email  See MDL-9052
            require_login();
        }

        $navigation = build_navigation('', $cm);
        print_header($course->shortname, $course->fullname, $navigation, '' , '', true, "", navmenu($course, $cm));

        notice_yesno(get_string('noguestpost', 'forum').'<br /><br />'.get_string('liketologin'),
                     $wwwroot, get_referer(false));
        print_footer($course);
        exit;
    }

    require_login(0, false);   // Script is useless unless they're logged in

    if (!empty($forum)) {      // User is starting a new discussion in a forum
        if (! $forum = get_record("forum", "id", $forum)) {
            error("The forum number was incorrect ($forum)");
        }
        if (! $course = get_record("course", "id", $forum->course)) {
            error("The course number was incorrect ($forum->course)");
        }
        if (! $cm = get_coursemodule_from_instance("forum", $forum->id, $course->id)) {
            error("Incorrect course module");
        }

        $coursecontext = get_context_instance(CONTEXT_COURSE, $course->id);

        if (! forum_user_can_post_discussion($forum, $groupid, -1, $cm)) {
            if (has_capability('moodle/legacy:guest', $coursecontext, NULL, false)) {  // User is a guest here!
                $SESSION->wantsurl = $FULLME;
                $SESSION->enrolcancel = $_SERVER['HTTP_REFERER'];
                redirect($CFG->wwwroot.'/course/enrol.php?id='.$course->id, get_string('youneedtoenrol'));
            } else {
                print_error('nopostforum', 'forum');
            }
        }

        if (!$cm->visible and !has_capability('moodle/course:viewhiddenactivities', $coursecontext)) {
            print_error("activityiscurrentlyhidden");
        }

        if (isset($_SERVER["HTTP_REFERER"])) {
            $SESSION->fromurl = $_SERVER["HTTP_REFERER"];
        } else {
            $SESSION->fromurl = '';
        }


        // Load up the $post variable.

        $post = new object();
        $post->course     = $course->id;
        $post->forum      = $forum->id;
        $post->discussion = 0;           // ie discussion # not defined yet
        $post->parent     = 0;
        $post->subject    = '';
        $post->userid     = $USER->id;
        $post->message    = '';

        if (isset($groupid)) {
            $post->groupid = $groupid;
        } else {
            $post->groupid = groups_get_activity_group($cm);
        }

        forum_set_return();

    } else if (!empty($reply)) {      // User is writing a new reply

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
        if (! $cm = get_coursemodule_from_instance("forum", $forum->id, $course->id)) {
            error("Incorrect cm");
        }

        // call course_setup to use forced language, MDL-6926 
        course_setup($course->id);

        $coursecontext = get_context_instance(CONTEXT_COURSE, $course->id);
        $modcontext    = get_context_instance(CONTEXT_MODULE, $cm->id);

        if (! forum_user_can_post($forum, $discussion, $USER, $cm, $course, $modcontext)) {
            if (has_capability('moodle/legacy:guest', $coursecontext, NULL, false)) {  // User is a guest here!
                $SESSION->wantsurl = $FULLME;
                $SESSION->enrolcancel = $_SERVER['HTTP_REFERER'];
                redirect($CFG->wwwroot.'/course/enrol.php?id='.$course->id, get_string('youneedtoenrol'));
            } else {
                print_error('nopostforum', 'forum');
            }
        }

        // Make sure user can post here
        if (groupmode($course, $cm) == SEPARATEGROUPS and !has_capability('moodle/site:accessallgroups', $modcontext)) {
            if ($discussion->groupid == -1) {
                print_error('nopostforum', 'forum');
            } else {
                if (!groups_is_member($discussion->groupid)) {
                    print_error('nopostforum', 'forum');
                }
            }
        }

        if (!$cm->visible and !has_capability('moodle/course:viewhiddenactivities', $coursecontext)) {
            print_error("activityiscurrentlyhidden");
        }

        // Load up the $post variable.

        $post = new object();
        $post->course      = $course->id;
        $post->forum       = $forum->id;
        $post->discussion  = $parent->discussion;
        $post->parent      = $parent->id;
        $post->subject     = $parent->subject;
        $post->userid      = $USER->id;
        $post->message     = '';

        $post->groupid = ($discussion->groupid == -1) ? 0 : $discussion->groupid;

        $strre = get_string('re', 'forum');
        if (!(substr($post->subject, 0, strlen($strre)) == $strre)) {
            $post->subject = $strre.' '.$post->subject;
        }

        unset($SESSION->fromdiscussion);

    } else if (!empty($edit)) {  // User is editing their own post

        if (! $post = forum_get_post_full($edit)) {
            error("Post ID was incorrect");
        }
        if ($post->parent) {
            if (! $parent = forum_get_post_full($post->parent)) {
                error("Parent post ID was incorrect ($post->parent)");
            }
        }

        if (! $discussion = get_record("forum_discussions", "id", $post->discussion)) {
            error("This post is not part of a discussion! ($edit)");
        }
        if (! $forum = get_record("forum", "id", $discussion->forum)) {
            error("The forum number was incorrect ($discussion->forum)");
        }
        if (! $course = get_record("course", "id", $discussion->course)) {
            error("The course number was incorrect ($discussion->course)");
        }
        if (!$cm = get_coursemodule_from_instance("forum", $forum->id, $course->id)) {
            error('Could not get the course module for the forum instance.');
        } else {
            $modcontext = get_context_instance(CONTEXT_MODULE, $cm->id);
        }
        if (!($forum->type == 'news' && !$post->parent && $discussion->timestart > time())) {
            if (((time() - $post->created) > $CFG->maxeditingtime) and
                        !has_capability('mod/forum:editanypost', $modcontext)) {
                error( get_string("maxtimehaspassed", "forum", format_time($CFG->maxeditingtime)) );
            }
        }
        if (($post->userid <> $USER->id) and
                    !has_capability('mod/forum:editanypost', $modcontext)) {
            error("You can't edit other people's posts!");
        }


        // Load up the $post variable.
        $post->edit   = $edit;
        $post->course = $course->id;
        $post->forum  = $forum->id;
        $post->groupid = ($discussion->groupid == -1) ? 0 : $discussion->groupid;

        trusttext_prepare_edit($post->message, $post->format, can_use_html_editor(), $modcontext);

        unset($SESSION->fromdiscussion);


    }else if (!empty($delete)) {  // User is deleting a post

        if (! $post = forum_get_post_full($delete)) {
            error("Post ID was incorrect");
        }
        if (! $discussion = get_record("forum_discussions", "id", $post->discussion)) {
            error("This post is not part of a discussion!");
        }
        if (! $forum = get_record("forum", "id", $discussion->forum)) {
            error("The forum number was incorrect ($discussion->forum)");
        }
        if (!$cm = get_coursemodule_from_instance("forum", $forum->id, $forum->course)) {
            error('Could not get the course module for the forum instance.');
        }
        if (!$course = get_record('course', 'id', $forum->course)) {
            error('Incorrect course');
        }

        require_login($course, false, $cm);
        $modcontext = get_context_instance(CONTEXT_MODULE, $cm->id);

        if ( !(($post->userid == $USER->id && has_capability('mod/forum:deleteownpost', $modcontext))
                    || has_capability('mod/forum:deleteanypost', $modcontext)) ) {
            error("You can't delete this post!");
        }


        $replycount = forum_count_replies($post);
        
        if (!empty($confirm) && confirm_sesskey()) {    // User has confirmed the delete
            //check user capability to delete post.
            $timepassed = time() - $post->created;
            if (($timepassed > $CFG->maxeditingtime) && !has_capability('mod/forum:deleteanypost', $modcontext)) {
                print_error("cannotdeletepost", "forum",
                          forum_go_back_to("discuss.php?d=$post->discussion"));
            }
            
            if ($post->totalscore) {
                notice(get_string("couldnotdeleteratings", "forum"),
                        forum_go_back_to("discuss.php?d=$post->discussion"));

            } else if ($replycount && !has_capability('mod/forum:deleteanypost', $modcontext)) {
                print_error("couldnotdeletereplies", "forum",
                        forum_go_back_to("discuss.php?d=$post->discussion"));

            } else {
                if (! $post->parent) {  // post is a discussion topic as well, so delete discussion
                    if ($forum->type == 'single') {
                        notice("Sorry, but you are not allowed to delete that discussion!",
                                forum_go_back_to("discuss.php?d=$post->discussion"));
                    }
                    forum_delete_discussion($discussion);

                    add_to_log($discussion->course, "forum", "delete discussion",
                               "view.php?id=$cm->id", "$forum->id", $cm->id);

                    redirect("view.php?f=$discussion->forum");

                } else if (forum_delete_post($post, has_capability('mod/forum:deleteanypost', $modcontext))) {

                    if ($forum->type == 'single') {
                        // Single discussion forums are an exception. We show
                        // the forum itself since it only has one discussion
                        // thread.
                        $discussionurl = "view.php?f=$forum->id";
                    } else {
                        $discussionurl = "discuss.php?d=$post->discussion";
                    }

                    add_to_log($discussion->course, "forum", "delete post", $discussionurl, "$post->id", $cm->id);

                    redirect(forum_go_back_to($discussionurl));
                } else {
                    error("An error occurred while deleting record $post->id");
                }
            }


        } else { // User just asked to delete something

            forum_set_return();

            if ($replycount) {
                if (!has_capability('mod/forum:deleteanypost', $modcontext)) {
                    print_error("couldnotdeletereplies", "forum",
                          forum_go_back_to("discuss.php?d=$post->discussion"));
                }
                print_header();
                notice_yesno(get_string("deletesureplural", "forum", $replycount+1),
                             "post.php?delete=$delete&amp;confirm=$delete&amp;sesskey=".sesskey(),
                             $CFG->wwwroot.'/mod/forum/discuss.php?d='.$post->discussion.'#p'.$post->id);

                forum_print_post($post, $discussion, $forum, $cm, $course, false, false, false);

                if (empty($post->edit)) {
                    $forumtracked = forum_tp_is_tracked($forum);
                    $posts = forum_get_all_discussion_posts($discussion->id, "created ASC", $forumtracked);
                    forum_print_posts_nested($course, $cm, $forum, $discussion, $post, false, false, $forumtracked, $posts);
                }
            } else {
                print_header();
                notice_yesno(get_string("deletesure", "forum", $replycount),
                             "post.php?delete=$delete&amp;confirm=$delete&amp;sesskey=".sesskey(),
                             $CFG->wwwroot.'/mod/forum/discuss.php?d='.$post->discussion.'#p'.$post->id);
                forum_print_post($post, $discussion, $forum, $cm, $course, false, false, false);
            }

        }
        print_footer($course);
        die;


    } else if (!empty($prune)) {  // Pruning

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
        if (!$post->parent) {
            error('This is already the first post in the discussion');
        }
        if (!$cm = get_coursemodule_from_instance("forum", $forum->id, $forum->course)) { // For the logs
            error('Could not get the course module for the forum instance.');
        } else {
            $modcontext = get_context_instance(CONTEXT_MODULE, $cm->id);
        }
        if (!has_capability('mod/forum:splitdiscussions', $modcontext)) {
            error("You can't split discussions!");
        }

        if (!empty($name) && confirm_sesskey()) {    // User has confirmed the prune

            $newdiscussion = new object();
            $newdiscussion->course       = $discussion->course;
            $newdiscussion->forum        = $discussion->forum;
            $newdiscussion->name         = $name;
            $newdiscussion->firstpost    = $post->id;
            $newdiscussion->userid       = $discussion->userid;
            $newdiscussion->groupid      = $discussion->groupid;
            $newdiscussion->assessed     = $discussion->assessed;
            $newdiscussion->usermodified = $post->userid;
            $newdiscussion->timestart    = $discussion->timestart;
            $newdiscussion->timeend      = $discussion->timeend;

            if (!$newid = insert_record('forum_discussions', $newdiscussion)) {
                error('Could not create new discussion');
            }

            $newpost = new object();
            $newpost->id      = $post->id;
            $newpost->parent  = 0;
            $newpost->subject = $name;

            if (!update_record("forum_posts", $newpost)) {
                error('Could not update the original post');
            }

            forum_change_discussionid($post->id, $newid);

            // update last post in each discussion
            forum_discussion_update_last_post($discussion->id);
            forum_discussion_update_last_post($newid);

            add_to_log($discussion->course, "forum", "prune post",
                           "discuss.php?d=$newid", "$post->id", $cm->id);

            redirect(forum_go_back_to("discuss.php?d=$newid"));

        } else { // User just asked to prune something

            $course = get_record('course', 'id', $forum->course);

            $navlinks = array();
            $navlinks[] = array('name' => format_string($post->subject, true), 'link' => "discuss.php?d=$discussion->id", 'type' => 'title');
            $navlinks[] = array('name' => get_string("prune", "forum"), 'link' => '', 'type' => 'title');
            $navigation = build_navigation($navlinks, $cm);
            print_header_simple(format_string($discussion->name).": ".format_string($post->subject), "", $navigation, '', "", true, "", navmenu($course, $cm));

            print_heading(get_string('pruneheading', 'forum'));
            echo '<center>';

            include('prune.html');

            forum_print_post($post, $discussion, $forum, $cm, $course, false, false, false);
            echo '</center>';
        }
        print_footer($course);
        die;
    } else {
        error("No operation specified");

    }

    if (!isset($coursecontext)) {
        // Has not yet been set by post.php.
        $coursecontext = get_context_instance(CONTEXT_COURSE, $forum->course);
    }

    if (!$cm = get_coursemodule_from_instance('forum', $forum->id, $course->id)) { // For the logs
        error('Could not get the course module for the forum instance.');
    }
    $modcontext = get_context_instance(CONTEXT_MODULE, $cm->id);

    // setup course variable to force form language
    // fix for MDL-6926
    course_setup($course->id);
    require_once('post_form.php');

    $mform_post = new mod_forum_post_form('post.php', array('course'=>$course, 'cm'=>$cm, 'coursecontext'=>$coursecontext, 'modcontext'=>$modcontext, 'forum'=>$forum, 'post'=>$post));

    if ($fromform = $mform_post->get_data()) {


        require_login($course, false, $cm);

        if (empty($SESSION->fromurl)) {
            $errordestination = "$CFG->wwwroot/mod/forum/view.php?f=$forum->id";
        } else {
            $errordestination = $SESSION->fromurl;
        }

        // TODO add attachment processing
        //$fromform->attachment = isset($_FILES['attachment']) ? $_FILES['attachment'] : NULL;

        trusttext_after_edit($fromform->message, $modcontext);

        if ($fromform->edit) {           // Updating a post
            unset($fromform->groupid);
            $fromform->id = $fromform->edit;
            $message = '';

            //fix for bug #4314
            if (!$realpost = get_record('forum_posts', 'id', $fromform->id)) {
                $realpost = new object;
                $realpost->userid = -1;
            }


            // if user has edit any post capability
            // or has either startnewdiscussion or reply capability and is editting own post
            // then he can proceed
            // MDL-7066
            if ( !(($realpost->userid == $USER->id && (has_capability('mod/forum:replypost', $modcontext)
                                || has_capability('mod/forum:startdiscussion', $modcontext))) ||
                                has_capability('mod/forum:editanypost', $modcontext)) ) {
                error("You can not update this post");
            }

            $updatepost = $fromform; //realpost
            $updatepost->forum = $forum->id;
            if (!forum_update_post($updatepost, $message)) {
                print_error("couldnotupdate", "forum", $errordestination);
            }

            // MDL-11818
            if (($forum->type == 'single') && ($updatepost->parent == '0')){ // updating first post of single discussion type -> updating forum intro
                $forum->intro = stripslashes($updatepost->message);
                $forum->timemodified = time();
                if (!update_record("forum", addslashes_recursive($forum))) {
                    print_error("couldnotupdate", "forum", $errordestination);
                }
            }

            $timemessage = 2;
            if (!empty($message)) { // if we're printing stuff about the file upload
                $timemessage = 4;
            }
            $message .= '<br />'.get_string("postupdated", "forum");

            if ($subscribemessage = forum_post_subscription($fromform, $forum)) {
                $timemessage = 4;
            }
            if ($forum->type == 'single') {
                // Single discussion forums are an exception. We show
                // the forum itself since it only has one discussion
                // thread.
                $discussionurl = "view.php?f=$forum->id";
            } else {
                $discussionurl = "discuss.php?d=$discussion->id#p$fromform->id";
            }
            add_to_log($course->id, "forum", "update post",
                    "$discussionurl&amp;parent=$fromform->id", "$fromform->id", $cm->id);

            redirect(forum_go_back_to("$discussionurl"), $message.$subscribemessage, $timemessage);

            exit;


        } else if ($fromform->discussion) { // Adding a new post to an existing discussion
            unset($fromform->groupid);
            $message = '';
            $addpost=$fromform;
            $addpost->forum=$forum->id;
            if ($fromform->id = forum_add_new_post($addpost, $message)) {

                $timemessage = 2;
                if (!empty($message)) { // if we're printing stuff about the file upload
                    $timemessage = 4;
                }

                if ($subscribemessage = forum_post_subscription($fromform, $forum)) {
                    $timemessage = 4;
                }

                if (!empty($fromform->mailnow)) {
                    $message .= get_string("postmailnow", "forum");
                    $timemessage = 4;
                } else {
                    $message .= '<p>'.get_string("postaddedsuccess", "forum") . '</p>';
                    $message .= '<p>'.get_string("postaddedtimeleft", "forum", format_time($CFG->maxeditingtime)) . '</p>';
                }

                if ($forum->type == 'single') {
                    // Single discussion forums are an exception. We show
                    // the forum itself since it only has one discussion
                    // thread.
                    $discussionurl = "view.php?f=$forum->id";
                } else {
                    $discussionurl = "discuss.php?d=$discussion->id";
                }
                add_to_log($course->id, "forum", "add post",
                          "$discussionurl&amp;parent=$fromform->id", "$fromform->id", $cm->id);

                redirect(forum_go_back_to("$discussionurl#p$fromform->id"), $message.$subscribemessage, $timemessage);

            } else {
                print_error("couldnotadd", "forum", $errordestination);
            }
            exit;

        } else {                     // Adding a new discussion
            if (!forum_user_can_post_discussion($forum, $fromform->groupid, -1, $cm, $modcontext)) {
                error('Can not add discussion, sorry.');
            }
            if (empty($fromform->groupid)) {
                $fromform->groupid = -1;
            }

            $fromform->mailnow = empty($fromform->mailnow) ? 0 : 1;
            $discussion = $fromform;
            $discussion->name  = $fromform->subject;
            $discussion->intro = $fromform->message;
            $newstopic = false;

            if ($forum->type == 'news' && !$fromform->parent) {
                $newstopic = true;
            }
            $discussion->timestart = $fromform->timestart;
            $discussion->timeend = $fromform->timeend;

            $message = '';
            if ($discussion->id = forum_add_discussion($discussion, $message)) {

                add_to_log($course->id, "forum", "add discussion",
                        "discuss.php?d=$discussion->id", "$discussion->id", $cm->id);

                $timemessage = 2;
                if (!empty($message)) { // if we're printing stuff about the file upload
                    $timemessage = 4;
                }

                if ($fromform->mailnow) {
                    $message .= get_string("postmailnow", "forum");
                    $timemessage = 4;
                } else {
                    $message .= '<p>'.get_string("postaddedsuccess", "forum") . '</p>';
                    $message .= '<p>'.get_string("postaddedtimeleft", "forum", format_time($CFG->maxeditingtime)) . '</p>';
                }

                if ($subscribemessage = forum_post_subscription($discussion, $forum)) {
                    $timemessage = 4;
                }

                redirect(forum_go_back_to("view.php?f=$fromform->forum"), $message.$subscribemessage, $timemessage);

            } else {
                print_error("couldnotadd", "forum", $errordestination);
            }

            exit;
        }
    }



    // To get here they need to edit a post, and the $post
    // variable will be loaded with all the particulars,
    // so bring up the form.

    // $course, $forum are defined.  $discussion is for edit and reply only.

    $cm = get_coursemodule_from_instance("forum", $forum->id, $course->id);

    require_login($course->id, false, $cm);

    $modcontext = get_context_instance(CONTEXT_MODULE, $cm->id);

    if ($post->discussion) {
        if (! $toppost = get_record("forum_posts", "discussion", $post->discussion, "parent", 0)) {
            error("Could not find top parent of post $post->id");
        }
    } else {
        $toppost->subject = ($forum->type == "news") ? get_string("addanewtopic", "forum") :
                                                       get_string("addanewdiscussion", "forum");
    }

    if (empty($post->edit)) {
        $post->edit = '';
    }

    if (empty($discussion->name)) {
        if (empty($discussion)) {
            $discussion = new object;
        }
        $discussion->name = $forum->name;
    }
    if ($forum->type == 'single') {
        // There is only one discussion thread for this forum type. We should
        // not show the discussion name (same as forum name in this case) in
        // the breadcrumbs.
        $strdiscussionname = '';
    } else {
        // Show the discussion name in the breadcrumbs.
        $strdiscussionname = format_string($discussion->name).':';
    }

    $forcefocus = empty($reply) ? NULL : 'message';

    $navlinks = array();
    if ($post->parent) {
        $navlinks[] = array('name' => format_string($toppost->subject, true), 'link' => "discuss.php?d=$discussion->id", 'type' => 'title');
        $navlinks[] = array('name' => get_string('editing', 'forum'), 'link' => '', 'type' => 'title');
    } else {
        $navlinks[] = array('name' => format_string($toppost->subject), 'link' => '', 'type' => 'title');
    }
    $navigation = build_navigation($navlinks, $cm);

    print_header("$course->shortname: $strdiscussionname ".
                  format_string($toppost->subject), $course->fullname,
                  $navigation, $mform_post->focus($forcefocus), "", true, "", navmenu($course, $cm));

// checkup
    if (!empty($parent) && !forum_user_can_see_post($forum, $discussion, $post, null, $cm)) {
        error("You cannot reply to this post");
    }
    if (empty($parent) && empty($edit) && !forum_user_can_post_discussion($forum, $groupid, -1, $cm, $modcontext)) {
        error("You cannot start a new discussion in this forum");
    }

    if ($forum->type == 'qanda'
                && !has_capability('mod/forum:viewqandawithoutposting', $modcontext)
                && !empty($discussion->id)
                && !forum_user_has_posted($forum->id, $discussion->id, $USER->id)) {
        notify(get_string('qandanotify','forum'));
    }

    forum_check_throttling($forum, $cm);

    if (!empty($parent)) {
        if (! $discussion = get_record('forum_discussions', 'id', $parent->discussion)) {
            error('This post is not part of a discussion!');
        }

        forum_print_post($parent, $discussion, $forum, $cm, $course, false, false, false);
        if (empty($post->edit)) {
            if ($forum->type != 'qanda' || forum_user_can_see_discussion($forum, $discussion, $modcontext)) {
                $forumtracked = forum_tp_is_tracked($forum);
                $posts = forum_get_all_discussion_posts($discussion->id, "created ASC", $forumtracked);
                forum_print_posts_threaded($course, $cm, $forum, $discussion, $parent, 0, false, false, $forumtracked, $posts);
            }
        }
        $heading = get_string("yourreply", "forum");
    } else {
        $forum->intro = trim($forum->intro);
        if (!empty($forum->intro)) {
            print_box(format_text($forum->intro), 'generalbox', 'intro');
        }
        if ($forum->type == 'qanda') {
            $heading = get_string('yournewquestion', 'forum');
        } else {
            $heading = get_string('yournewtopic', 'forum');
        }
    }

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

    //load data into form

    if (forum_is_subscribed($USER->id, $forum->id)) {
        $subscribe = true;

    } else if (forum_user_has_posted($forum->id, 0, $USER->id)) {
        $subscribe = false;
        
    } else {
        // user not posted yet - use subscription default specified in profile
        $subscribe = !empty($USER->autosubscribe);
    }

    // HACK ALERT: this is very wrong, the defaults should be always initialized before calling $mform->get_data() !!!
    $mform_post->set_data(array(    'general'=>$heading,
                                        'subject'=>$post->subject,
                                        'message'=>$post->message,
                                        'subscribe'=>$subscribe?1:0,
                                        'mailnow'=>!empty($post->mailnow),
                                        'userid'=>$post->userid,
                                        'parent'=>$post->parent,
                                        'discussion'=>$post->discussion,
                                        'course'=>$course->id)+

                                        $page_params+

                                (isset($post->format)?array(
                                        'format'=>$post->format):
                                    array())+

                                (isset($discussion->timestart)?array(
                                        'timestart'=>$discussion->timestart):
                                    array())+

                                (isset($discussion->timeend)?array(
                                        'timeend'=>$discussion->timeend):
                                    array())+

                                (isset($post->groupid)?array(
                                        'groupid'=>$post->groupid):
                                    array())+

                                (isset($discussion->id)?
                                        array('discussion'=>$discussion->id):
                                        array()));


    $mform_post->display();


    print_footer($course);


?>
