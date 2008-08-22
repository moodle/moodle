<?php // $Id$

//  Collect ratings, store them, then return to where we came from


    require_once('../../config.php');
    require_once('lib.php');

    $forumid = required_param('forumid', PARAM_INT); // The forum the rated posts are from

    if (!$forum = $DB->get_record('forum', array('id' => $forumid))) {
        print_error('invalidforumid', 'forum');
    }

    if (!$course = $DB->get_record('course', array('id' => $forum->course))) {
        print_error('invalidcourseid');
    }

    if (!$cm = get_coursemodule_from_instance('forum', $forum->id)) {
        print_error('invalidcoursemodule');
    } else {
        $forum->cmidnumber = $cm->id; //MDL-12961
        }

    require_login($course, false, $cm);

    if (isguestuser()) {
        print_error('noguestrate', 'forum');
    }

    if (!$forum->assessed) {
        print_error('norate', 'forum');
    }

    $context = get_context_instance(CONTEXT_MODULE, $cm->id);
    require_capability('mod/forum:rate', $context);

    if ($data = data_submitted()) {

        $discussionid = false;

        foreach ((array)$data as $postid => $rating) {
            if (!is_numeric($postid)) {
                continue;
            }

            // following query validates the submitted postid too
            $sql = "SELECT fp.*
                      FROM {forum_posts} fp, {forum_discussions} fd
                     WHERE fp.id = ? AND fp.discussion = fd.id AND fd.forum = ?";

            if (!$post = $DB->get_record_sql($sql, array($postid, $forum->id))) {
                print_error('invalidpostid', 'forum', '', $postid);
            }

            $discussionid = $post->discussion;

            if ($forum->assesstimestart and $forum->assesstimefinish) {
                if ($post->created < $forum->assesstimestart or $post->created > $forum->assesstimefinish) {
                    // we can not rate this, ignore it - this should not happen anyway unless teacher changes setting
                    continue;
                }
            }

            if ($rating == FORUM_UNSET_POST_RATING) {
                $DB->delete_records('forum_ratings', array('post' => $postid, 'userid' => $USER->id));
                forum_update_grades($forum, $post->userid);

            } else if ($oldrating = $DB->get_record('forum_ratings', array('userid' => $USER->id, 'post' => $post->id))) {
                if ($rating != $oldrating->rating) {
                    $oldrating->rating = $rating;
                    $oldrating->time   = time();
                    if (!$DB->update_record('forum_ratings', $oldrating)) {
                        print_error('cannotupdaterate', 'error', '', (object)array('id'=>$post->id, 'rating'=>$rating));
                    }
                    forum_update_grades($forum, $post->userid);
                }

            } else {
                $newrating = new object();
                $newrating->userid = $USER->id;
                $newrating->time   = time();
                $newrating->post   = $post->id;
                $newrating->rating = $rating;

                if (! $DB->insert_record('forum_ratings', $newrating)) {
                    print_error('cannotinsertrate', 'error', '', (object)array('id'=>$postid, 'rating'=>$rating));
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
        print_error('invalidaccess', 'forum');
    }

?>
