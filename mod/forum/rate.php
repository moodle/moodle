<?php // $Id$

//  Collect ratings, store them, then return to where we came from

/// TODO: Centralise duplicate code in rate.php and rate_ajax.php

    require_once('../../config.php');
    require_once('lib.php');

    $forumid = required_param('forumid', PARAM_INT); // The forum the rated posts are from

    if (!$forum = get_record('forum', 'id', $forumid)) {
        error("Forum ID was incorrect");
    }

    if (!$course = get_record('course', 'id', $forum->course)) {
        error("Course ID was incorrect");
    }

    if (!$cm = get_coursemodule_from_instance('forum', $forum->id)) {
        error("Course Module ID was incorrect");
    } else {
        $forum->cmidnumber = $cm->id; //MDL-12961
        }

    require_login($course, false, $cm);

    if (isguestuser()) {
        error("Guests are not allowed to rate entries.");
    }

    if (!$forum->assessed) {
        error("Rating of items not allowed!");
    }

    $context = get_context_instance(CONTEXT_MODULE, $cm->id);
    require_capability('mod/forum:rate', $context);

    if ($data = data_submitted() and confirm_sesskey()) {

        $discussionid = false;

    /// Calculate scale values
        $scale_values = make_grades_menu($forum->scale);

        foreach ((array)$data as $postid => $rating) {
            if (!is_numeric($postid)) {
                continue;
            }

            // following query validates the submitted postid too
            $sql = "SELECT fp.*
                      FROM {$CFG->prefix}forum_posts fp, {$CFG->prefix}forum_discussions fd
                     WHERE fp.id = '$postid' AND fp.discussion = fd.id AND fd.forum = $forum->id";

            if (!$post = get_record_sql($sql)) {
                error("Incorrect postid - $postid");
            }

            $discussionid = $post->discussion;

            if ($forum->assesstimestart and $forum->assesstimefinish) {
                if ($post->created < $forum->assesstimestart or $post->created > $forum->assesstimefinish) {
                    // we can not rate this, ignore it - this should not happen anyway unless teacher changes setting
                    continue;
                }
            }

        /// Check rate is valid for for that forum scale values
            if (!array_key_exists($rating, $scale_values) && $rating != FORUM_UNSET_POST_RATING) {
                print_error('invalidrate', 'forum', '', $rating);
            }

            if ($rating == FORUM_UNSET_POST_RATING) {
                delete_records('forum_ratings', 'post', $postid, 'userid', $USER->id);
                forum_update_grades($forum, $post->userid);

            } else if ($oldrating = get_record('forum_ratings', 'userid', $USER->id, 'post', $post->id)) {
                if ($rating != $oldrating->rating) {
                    $oldrating->rating = $rating;
                    $oldrating->time   = time();
                    if (! update_record('forum_ratings', $oldrating)) {
                        error("Could not update an old rating ($post->id = $rating)");
                    }
                    forum_update_grades($forum, $post->userid);
                }

            } else {
                $newrating = new object();
                $newrating->userid = $USER->id;
                $newrating->time   = time();
                $newrating->post   = $post->id;
                $newrating->rating = $rating;

                if (! insert_record('forum_ratings', $newrating)) {
                    error("Could not insert a new rating ($postid = $rating)");
                }
                forum_update_grades($forum, $post->userid);
            }
        }

        if ($forum->type == 'single' or !$discussionid) {
            redirect("$CFG->wwwroot/mod/forum/view.php?id=$cm->id", get_string('ratingssaved', 'forum'));
        } else {
            redirect("$CFG->wwwroot/mod/forum/discuss.php?d=$discussionid", get_string('ratingssaved', 'forum'));
        }

    } else {
        error("This page was not accessed correctly");
    }

?>
