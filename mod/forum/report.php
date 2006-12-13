<?php // $Id$

//  For a given post, shows a report of all the ratings it has

    require_once("../../config.php");
    require_once("lib.php");

    $id = required_param('id',PARAM_INT);

    if (! $post = get_record("forum_posts", "id", $id)) {
        error("Post ID was incorrect");
    }

    if (! $discussion = get_record("forum_discussions", "id", $post->discussion)) {
        error("Discussion ID was incorrect");
    }

    if (! $forum = get_record("forum", "id", $discussion->forum)) {
        error("Forum ID was incorrect");
    }

    if (! $course = get_record("course", "id", $forum->course)) {
        error("Course ID was incorrect");
    }
    
    if (! $cm = get_coursemodule_from_instance('forum', $forum->id, $course->id)) {
        error('Course Module ID was incorrect');
    }
    
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);
    
    if (!has_capability('mod/forum:viewrating', $context)) {
        error('You do not have the capability to view post ratings');
    }
    if (!has_capability('mod/forum:viewanyrating', $context) and $USER->id != $post->userid) {
        error("You can only look at results for posts that you made");
    }

    if (!isset($sort)) {
        $sort = "r.time";
    }

    $scalemenu = make_grades_menu($forum->scale);

    $strratings = get_string("ratings", "forum");
    $strrating = get_string("rating", "forum");
    $strname = get_string("name");
    $strtime = get_string("time");

    print_header("$strratings: ".format_string($post->subject));

    if (!$ratings = forum_get_ratings($post->id, $sort)) {
        error("No ratings for this post: \"".format_string($post->subject)."\"");

    } else {
        echo "<table border=\"0\" cellpadding=\"3\" cellspacing=\"3\" class=\"generalbox\" width=\"100%\">";
        echo "<tr>";
        echo "<th scope=\"col\">&nbsp;</th>";
        echo "<th scope=\"col\"><a href=\"report.php?id=$post->id&amp;sort=u.firstname\">$strname</a>";
        echo "<th width=\"100%\" scope=\"col\"><a href=\"report.php?id=$post->id&amp;sort=r.rating\">$strrating</a>";
        echo "<th scope=\"col\"><a href=\"report.php?id=$post->id&amp;sort=r.time\">$strtime</a>";
        foreach ($ratings as $rating) {
            echo '<tr class="forumpostheader">';
            echo "<td>";
            print_user_picture($rating->id, $forum->course, $rating->picture);
            echo '<td nowrap="nowrap"><p><font size="-1">'.fullname($rating).'</p>';
            echo '<td nowrap="nowrap" align="center"><p><font size="-1">'.$scalemenu[$rating->rating]."</p>";
            echo '<td nowrap="nowrap" align="center"><p><font size="-1">'.userdate($rating->time)."</p>";
            echo "</tr>\n";
        }
        echo "</table>";
        echo "<br />";
    }

    close_window_button();

?>
