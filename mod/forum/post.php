<?PHP // $Id$

//  Edit and save a new post to a discussion


    require_once("../../config.php");
    require_once("lib.php");

    if (isguest()) {
        error(get_string("noguestpost", "forum"), $_SERVER["HTTP_REFERER"]);
    }

    require_login(0, false);   // Script is useless unless they're logged in

    if ($post = data_submitted()) {
        if (!empty($post->course)) {
            if ($course = get_record('course', 'id', $post->course)) {
                if (!empty($course->lang)) {
                    $CFG->courselang = $course->lang;
                }
            }
        }

        if (empty($SESSION->fromurl)) {
            $errordestination = "$CFG->wwwroot/mod/forum/view.php?f=$post->forum";
        } else {
            $errordestination = $SESSION->fromurl;
        }

        $post->subject = strip_tags($post->subject, '<lang>');        // Strip all tags except lang
        $post->subject = break_up_long_words($post->subject);

        //$post->message = clean_text($post->message, $post->format);   // Clean up any bad tags

        $post->attachment = isset($_FILES['attachment']) ? $_FILES['attachment'] : NULL;

        if (!$cm = get_coursemodule_from_instance("forum", $post->forum, $post->course)) { // For the logs
            $cm->id = 0;
        }

        if (!$post->subject or !$post->message) {
            $post->error = get_string("emptymessage", "forum");

        } else if ($post->edit) {           // Updating a post
            $post->id = $post->edit;
            if (forum_update_post($post)) {

                add_to_log($post->course, "forum", "update post", 
                          "discuss.php?d=$post->discussion&parent=$post->id", "$post->id", $cm->id);

                $message = get_string("postupdated", "forum");
                $timemessage = 1;

                if ($subscribemessage = forum_post_subscription($post)) {
                    $timemessage = 2;
                }
                redirect(forum_go_back_to("discuss.php?d=$post->discussion#$post->id"), $message.$subscribemessage, $timemessage);

            } else {
                error(get_string("couldnotupdate", "forum"), $errordestination); 
            }
            exit;

        } else if ($post->discussion) { // Adding a new post to an existing discussion
            if ($post->id = forum_add_new_post($post)) {

                add_to_log($post->course, "forum", "add post", 
                          "discuss.php?d=$post->discussion&parent=$post->id", "$post->id", $cm->id);

                $message = get_string("postadded", "forum", format_time($CFG->maxeditingtime));
                $timemessage = 2;

                if ($subscribemessage = forum_post_subscription($post)) {
                    $timemessage = 4;
                }

                redirect(forum_go_back_to("discuss.php?d=$post->discussion#$post->id"), $message.$subscribemessage, $timemessage);

            } else {
                error(get_string("couldnotadd", "forum"), $errordestination); 
            }
            exit;

        } else {                     // Adding a new discussion
            $discussion = $post;
            $discussion->name  = $post->subject;
            $discussion->intro = $post->message;
            if ($discussion->id = forum_add_discussion($discussion)) {

                add_to_log($post->course, "forum", "add discussion", 
                           "discuss.php?d=$discussion->id", "$discussion->id", $cm->id);

                $message = get_string("postadded", "forum", format_time($CFG->maxeditingtime));
                $timemessage = 2;

                if ($subscribemessage = forum_post_subscription($discussion)) {
                    $timemessage = 4;
                }

                redirect(forum_go_back_to("view.php?f=$post->forum"), $message.$subscribemessage, $timemessage);

            } else {
                error(get_string("couldnotadd", "forum"), $errordestination); 
            }
            exit;
        }
    }

    if ($usehtmleditor = can_use_richtext_editor()) {
        $defaultformat = FORMAT_HTML;
    } else {
        $defaultformat = FORMAT_MOODLE;
    }


    if (isset($post->error)) {     // User is re-editing a failed posting

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
        }

    } else if (isset($forum)) {      // User is starting a new discussion in a forum

        $SESSION->fromurl = $_SERVER["HTTP_REFERER"];

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

        $post->groupid = get_current_group($course->id);
        if (isteacheredit($course->id) and $post->groupid == 0) {
            $post->groupid = -1;
        }

        forum_set_return();

    } else if (isset($reply)) {      // User is writing a new reply

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
                if (mygroupid($course->id) != $discussion->groupid) {
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

    } else if (isset($edit)) {  // User is editing their own post

        $adminedit = (isadmin() and !empty($CFG->admineditalways));

        if (! $post = forum_get_post_full($edit)) {
            error("Post ID was incorrect");
        }
        if (($post->userid <> $USER->id) and !$adminedit) {
            error("You can't edit other people's posts!");
        }
        if (((time() - $post->created) > $CFG->maxeditingtime) and !$adminedit) {
            error( get_string("maxtimehaspassed", "forum", format_time($CFG->maxeditingtime)) );
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
        if (! $course = get_record("course", "id", $discussion->course)) {
            error("The course number was incorrect ($discussion->course)");
        }

        // Load up the $post variable.

        $post->edit = $edit;

        $post->course  = $course->id;
        $post->forum  = $forum->id;

        unset($SESSION->fromdiscussion);


    } else if (isset($delete)) {  // User is deleting a post

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

        if (isset($confirm)) {    // User has confirmed the delete

            if ($post->totalscore) {
                notice(get_string("couldnotdeleteratings", "forum"), 
                        forum_go_back_to("discuss.php?d=$post->discussion"));

            } else if (record_exists("forum_posts", "parent", $delete)) {
                error(get_string("couldnotdeletereplies", "forum"),
                        forum_go_back_to("discuss.php?id=$post->discussion"));

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

                } else if (forum_delete_post($post)) {

                    add_to_log($discussion->course, "forum", "delete post", 
                               "discuss.php?d=$post->discussion", "$post->id", $cm->id);

                    redirect(forum_go_back_to("discuss.php?d=$post->discussion"), 
                             get_string("deletedpost", "forum"), 1);
                } else {
                    error("An error occurred while deleting record $post->id");
                }
            }


        } else { // User just asked to delete something

            forum_set_return();

            print_header();
            notice_yesno(get_string("deletesure", "forum"), 
                         "post.php?delete=$delete&confirm=$delete",
                         $_SERVER["HTTP_REFERER"]);
                         
            echo "<CENTER><HR>";
            forum_print_post($post, $forum->course, $ownpost=false, $reply=false, $link=false);
        }

        die;


    } else if (isset($prune)) {  // Teacher is pruning
        
        if (! $post = forum_get_post_full($prune)) {
            error("Post ID was incorrect");
        }
        if (! $discussion = get_record("forum_discussions", "id", $post->discussion)) {
            error("This post is not part of a discussion!");
        }
        if (! $forum = get_record("forum", "id", $discussion->forum)) {
            error("The forum number was incorrect ($discussion->forum)");
        }
        if (!isteacher($forum->course)) {
            error("You can't split discussions!");
        }
        if (!$post->parent) {
            error('This is already the first post in the discussion');
        }

        if (isset($_REQUEST['name'])) {    // User has confirmed the prune
            
            $newdiscussion->course = $discussion->course;
            $newdiscussion->forum = $discussion->forum;
            $newdiscussion->name = $name;
            $newdiscussion->firstpost = $post->id;
            $newdiscussion->userid = $discussion->userid;
            $newdiscussion->groupid = $discussion->groupid;
            $newdiscussion->assessed = $discussion->assessed;
            $newdiscussion->usermodified = $post->userid;
            
            if (!$newid = insert_record('forum_discussions', $newdiscussion)) {
                error('Could not create new discussion');
            }
            
            $newpost->id = $post->id;
            $newpost->parent = 0;
            $newpost->subject = $name;

            if (!update_record("forum_posts", $newpost)) {
                error('Could not update the original post');
            }

            forum_change_discussionid($post->id, $newid);
            
            // set timemodified to time of last post in each discussion
            $lastpost = get_record_sql("SELECT MAX(modified) AS time
                                              FROM {$CFG->prefix}forum_posts 
                                              WHERE discussion = '$discussion->id'");
            set_field('forum_discussions', 'timemodified', $lastpost->time, 'id', $discussion->id);
            $lastpost = get_record_sql("SELECT MAX(modified) AS time
                                              FROM {$CFG->prefix}forum_posts 
                                              WHERE discussion = '$newid'");
            set_field('forum_discussions', 'timemodified', $lastpost->time, 'id', $newid);            


            if (!$cm = get_coursemodule_from_instance("forum", $forum->id, $forum->course)) { // For the logs
                $cm->id = 0;
            }
            add_to_log($discussion->course, "forum", "prune post", 
                           "discuss.php?d=$newid", "$post->id", $cm->id);

            redirect(forum_go_back_to("discuss.php?d=$newid"), get_string("prunedpost", "forum"), 1);

        } else { // User just asked to prune something

            $course = get_record('course', 'id', $forum->course);
            $strforums = get_string("modulenameplural", "forum");
            print_header("$course->shortname: $discussion->name: $post->subject", "$course->fullname",
                         "<a href=../../course/view.php?id=$course->id>$course->shortname</a> ->
                          <a href=\"../forum/index.php?id=$course->id\">$strforums</a> -> 
                          <a href=\"view.php?f=$forum->id\">$forum->name</a> -> 
                          <a href=\"discuss.php?d=$discussion->id\">$post->subject</a> -> ".
                          get_string("prune", "forum"), '', "", true, "", navmenu($course, $cm));
            
            print_heading(get_string('pruneheading', 'forum'));
            echo '<center>';
            
            include('prune.html');
                         
            forum_print_post($post, $forum->course, $ownpost=false, $reply=false, $link=false);
        }

        die;


    } else {
        error("No operation specified");

    }


    // To get here they need to edit a post, and the $post 
    // variable will be loaded with all the particulars,
    // so bring up the form.

    // $course, $forum are defined.  $discussion is for edit and reply only.

    require_login($course->id);


    if ($post->discussion) {
        if (! $toppost = get_record("forum_posts", "discussion", $post->discussion, "parent", 0)) {
            error("Could not find top parent of post $post->id");
        }
    } else {
        $toppost->subject = get_string("yournewtopic", "forum");
    }

    if (empty($post->subject)) {
        $formstart = "theform.subject";
    } else {
        $formstart = "";
    }

    if ($post->parent) {
        $navtail = "<A HREF=\"discuss.php?d=$discussion->id\">$toppost->subject</A> -> ".get_string("editing", "forum");
    } else {
        $navtail = "$toppost->subject";
    }

    if (empty($post->edit)) {
        $post->edit = "";
    }

    $strforums = get_string("modulenameplural", "forum");


    $navmiddle = "<A HREF=\"../forum/index.php?id=$course->id\">$strforums</A> -> <A HREF=\"view.php?f=$forum->id\">$forum->name</A>";

    $cm = get_coursemodule_from_instance("forum", $forum->id, $course->id);

    if (empty($discussion->name)) {
        $discussion->name = $forum->name;
    }

    if ($course->category) {
        print_header("$course->shortname: $discussion->name: $toppost->subject", "$course->fullname",
                 "<A HREF=../../course/view.php?id=$course->id>$course->shortname</A> ->
                  $navmiddle -> $navtail", $formstart, "", true, "", navmenu($course, $cm));
    } else {
        print_header("$course->shortname: $discussion->name: $toppost->subject", "$course->fullname",
                 "$navmiddle -> $navtail", "$formstart", "", true, "", navmenu($course, $cm));

    }

    if (!empty($parent)) {
        forum_print_post($parent, $course->id, $ownpost=false, $reply=false, $link=false);
        if (empty($post->edit)) {
            forum_print_posts_threaded($parent->id, $course, 0, false, false);
        }
        echo "<center>";
        echo "<H2>".get_string("yourreply", "forum").":</H2>";
    } else {
        echo "<center>";
        echo "<H2>".get_string("yournewtopic", "forum")."</H2>";
    }
    if (!empty($post->error)) {
        notify($post->error);
    }
    echo "</center>";

    print_simple_box_start("center", "", "$THEME->cellheading");
    require("post.html");
    print_simple_box_end();

    if ($usehtmleditor) {
        use_html_editor("message");
    }

    print_footer($course);


?>
