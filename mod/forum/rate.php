<?php // $Id$

//  Collect ratings, store them, then return to where we came from


    require_once("../../config.php");
    require_once("lib.php");
    

    $id = required_param('id',PARAM_INT);           // The course these ratings are part of
    $forumid = required_param('forumid',PARAM_INT); // The forum the rated posts are from
    
    if (! $cm = get_coursemodule_from_instance('forum', $forumid, $id)) {
        error('Course Module ID was incorrect');
    }

    if (!$forum = get_record('forum', 'id', $forumid)) {
        error("Forum ID was incorrect");
    }
    
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);
    
    if (!has_capability('mod/forum:rate', $context)) {
        error('You do not have the permission to rate this post');
    }
    
    
    if (! $course = get_record("course", "id", $id)) {
        error("Course ID was incorrect");
    }

    require_login($course->id);

    $returntoview = false;

    if (!$data = data_submitted("$CFG->wwwroot/mod/forum/discuss.php")) {    // form submitted
        if ($data = data_submitted("$CFG->wwwroot/mod/forum/view.php")) {    // Single forums are special case
            $returntoview = true;
        }
    }

    if ($data and confirm_sesskey()) {

        $lastpostid = 0;

    /// Calculate scale values
        $scale_values = make_grades_menu($forum->scale);

        foreach ((array)$data as $postid => $rating) {
            if (!is_numeric($postid)) {
                continue;
            }

            $postid = (int)$postid;
            $lastpostid = $postid;

        /// Check rate is valid for for that forum scale values
            if (!array_key_exists($rating, $scale_values) && $rating != FORUM_UNSET_POST_RATING) {
                print_error('invalidrate', 'forum', '', $rating);
            }

            if ($rating == FORUM_UNSET_POST_RATING) {
                delete_records('forum_ratings', 'post', $postid, 'userid', $USER->id);

            } else if ($oldrating = get_record("forum_ratings", "userid", $USER->id, "post", $postid)) {
                if ($rating != $oldrating->rating) {
                    $oldrating->rating = $rating;
                    $oldrating->time = time();
                    if (! update_record("forum_ratings", $oldrating)) {
                        error("Could not update an old rating ($postid = $rating)");
                    }
                }
            } else {
                unset($newrating);
                $newrating->userid = $USER->id;
                $newrating->time = time();
                $newrating->post = $postid;
                $newrating->rating = $rating;

                if (! insert_record("forum_ratings", $newrating)) {
                    error("Could not insert a new rating ($postid = $rating)");
                }
            }
        }
        if ($post = get_record('forum_posts', 'id', $lastpostid)) {    // To find discussion we're in
            if ($returntoview and ($discussion = get_record('forum_discussions', 'id', $post->discussion))) {
                redirect("$CFG->wwwroot/mod/forum/view.php?f=$discussion->forum", get_string("ratingssaved", "forum"));
            } else {
                redirect("$CFG->wwwroot/mod/forum/discuss.php?d=$post->discussion", get_string("ratingssaved", "forum"));
            }
        } else {
            redirect($_SERVER["HTTP_REFERER"], get_string("ratingssaved", "forum"));
        }

    } else {
        error("This page was not accessed correctly");
    }

?>
