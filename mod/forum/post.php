<?PHP // $Id$

//  Edit and save a new post to a discussion


    require("../../config.php");
    require("lib.php");

    if (isguest()) {
        error("Guests are not allowed to post.", $HTTP_REFERER);
    }

    if (match_referer() && isset($HTTP_POST_VARS)) {    // form submitted
        $post = (object)$HTTP_POST_VARS;

        $post->subject = strip_tags($post->subject);  // Strip all tags
        $post->message = cleantext($post->message);   // Clean up any bad tags

        require_login();

        if ($post->edit) {           // Updating a post
            $post->id = $post->edit;
            if (update_post_in_database($post) ) {
                add_to_log($post->course, "forum", "update post", "discuss.php?d=$post->discussion&parent=$post->id", "$post->id");
                redirect(go_back_to("discuss.php?d=$post->discussion"), "Your post was updated", 1);
            } else {
                error("Could not update your post due to an unknown error"); 
            }
        } else if ($post->discussion) { // Adding a new post to an existing discussion
            if ($post->id = add_new_post_to_database($post)) {
                if ( ! forum_is_subscribed($USER->id, $post->forum) ) {
                    forum_subscribe($USER->id, $post->forum);
                }

                add_to_log($post->course, "forum", "add post", "discuss.php?d=$post->discussion&parent=$post->id", "$post->id");
                redirect(go_back_to("discuss.php?d=$post->discussion"), 
                         "Your post was successfully added.<P>You have ".format_time($CFG->maxeditingtime)." to edit it if you want to make any changes.", 3);
            } else {
                error("Could not add the post due to an unknown error"); 
            }
        } else {                     // Adding a new discussion
            $discussion = $post;
            $discussion->name  = $post->subject;
            $discussion->intro = $post->message;
            if ($discussion->id = forum_add_discussion($discussion)) {
                if ( ! forum_is_subscribed($USER->id, $post->forum) ) {
                    forum_subscribe($USER->id, $post->forum);
                }
                add_to_log($post->course, "forum", "add discussion", "discuss.php?d=$discussion->id", "$discussion->id");
                redirect(go_back_to("view.php?f=$post->forum"), 
                         "Your post was successfully added.<P>You have ".format_time($CFG->maxeditingtime)." to edit it if you want to make any changes.", 5);
            } else {
                error("Could not insert the new discussion.");
            }
        }
        die;
    }



    if (isset($forum)) {      // User is starting a new discussion in a forum

        $SESSION->fromurl = $HTTP_REFERER;

        if (! $forum = get_record("forum", "id", $forum)) {
            error("The forum number was incorrect ($forum)");
        }
        if (! $course = get_record("course", "id", $forum->course)) {
            error("The course number was incorrect ($forum)");
        }

        if (! user_can_post_discussion($forum)) {
            error("Sorry, but you can not post a new discussion in this forum.");
        }

        // Load up the $post variable.

        $post->course = $course->id;
        $post->forum  = $forum->id;
        $post->discussion = 0;           // ie discussion # not defined yet
        $post->parent = 0;
        $post->subject = "";
        $post->user = $USER->id;
        $post->message = "";

        set_fromdiscussion();
    
    } else if (isset($reply)) {      // User is writing a new reply

        if (! $parent = get_forum_post_full($reply)) {
            error("Parent post ID was incorrect ($reply)");
        }
        if (! $discussion = get_record("forum_discussions", "id", $parent->discussion)) {
            error("This post is not part of a discussion! ($reply)");
        }
        if (! $forum = get_record("forum", "id", $discussion->forum)) {
            error("The forum number was incorrect ($discussion->forum)");
        }
        if (! $course = get_record("course", "id", $discussion->course)) {
            error("The course number was incorrect ($discussion->course)");
        }
        // Load up the $post variable.

        $post->course  = $course->id;
        $post->forum  = $forum->id;
        $post->discussion  = $parent->discussion;
        $post->parent = $parent->id;
        $post->subject = $parent->subject;
        $post->user = $USER->id;
        $post->message = "";

        if (!(substr($post->subject, 0, 3) == "Re:")) {
            $post->subject = "Re: ".$post->subject;
        }

        set_fromdiscussion();

    } else if (isset($edit)) {  // User is editing their own post

        if (! $post = get_forum_post_full($edit)) {
            error("Post ID was incorrect");
        }
        if ($post->user <> $USER->id) {
            error("You can't edit other people's posts!");
        }
        if ((time() - $post->created) > $CFG->maxeditingtime) {
            error("Sorry, but the maximum time for editing this post (".format_time($CFG->maxeditingtime).") has passed!");
        }
        if ($post->parent) {
            if (! $parent = get_forum_post_full($post->parent)) {
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

        set_fromdiscussion();


    } else if (isset($delete)) {  // User is deleting a post

        if (! $post = get_forum_post_full($delete)) {
            error("Post ID was incorrect");
        }
        if ($post->user <> $USER->id) {
            error("You can't delete other people's posts!");
        }
        if (! $discussion = get_record("forum_discussions", "id", $post->discussion)) {
            error("This post is not part of a discussion!");
        }

        if (isset($confirm)) {    // User has confirmed the delete

            if ($post->totalscore) {
                notice("Sorry, that cannot be deleted as people have already rated it", 
                        go_back_to("discuss.php?d=$post->discussion"));

            } else if (record_exists("forum_posts", "parent", $delete)) {
                error("Sorry, that cannot be deleted as people have 
                        already responded to it", 
                        go_back_to("discuss.php?id=$post->discussion"));

            } else {
                if (! $post->parent) {  // post is a discussion topic as well, so delete discussion
                    forum_delete_discussion($discussion);

                    add_to_log($discussion->course, "forum", "delete discussion", "view.php?id=$discussion->forum", "$post->id");
                    redirect("view.php?f=$discussion->forum", 
                             "Your discussion topic was deleted", 1);

                } else if (delete_records("forum_posts", "id", $post->id)) {

                    add_to_log($discussion->course, "forum", "delete post", "discuss.php?d=$post->discussion", "$post->id");
                    redirect(go_back_to("discuss.php?d=$post->discussion"), 
                             "Your post was deleted", 1);
                } else {
                    error("An error occurred while deleting record $post->id");
                }
            }


        } else { // User just asked to delete something

            set_fromdiscussion();

            print_header();
            notice_yesno("Are you sure you want to delete this post?", 
                         "post.php?delete=$delete&confirm=$delete",
                         $HTTP_REFERER);
                         
            echo "<CENTER><HR>";
            print_post($post, 0, $ownpost=false, $reply=false, $link=false);

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
        if (! $toppost = get_record_sql("SELECT * FROM forum_posts 
                                         WHERE discussion='$post->discussion' 
                                         AND parent = 0")) {
            error("Could not find top parent of post $post->id");
        }
    } else {
        $toppost->subject = "New discussion topic";
    }

    if ($post->subject) {
        $formstart = "form.message";
    } else {
        $formstart = "form.subject";
    }

    if ($post->parent) {
        $navtail = "<A HREF=\"discuss.php?d=$discussion->id\">$toppost->subject</A> -> Editing";
    } else {
        $navtail = "$toppost->subject";
    }

    $navmiddle = "<A HREF=\"../forum/index.php?id=$course->id\">Forums</A> -> <A HREF=\"view.php?f=$forum->id\">$forum->name</A>";

    if ($course->category) {
        print_header("$course->shortname: $discussion->name: $toppost->subject", "$course->fullname",
                 "<A HREF=../../course/view.php?id=$course->id>$course->shortname</A> ->
                  $navmiddle -> $navtail", "$forumstart");
    } else {
        print_header("$course->shortname: $discussion->name: $toppost->subject", "$course->fullname",
                 "$navmiddle -> $navtail", "");

    }

    echo "<CENTER>";
    if (isset($parent)) {
        print_post($parent, $course->id, $ownpost=false, $reply=false, $link=false);
        echo "<H2>Your reply:</H2>";
    } else {
        echo "<H2>Your new discussion topic:</H2>";
    }
    echo "</CENTER>";

    print_simple_box_start("center", "", "$THEME->cellheading");
    require("post.html");
    print_simple_box_end();

    print_footer($course);


?>
