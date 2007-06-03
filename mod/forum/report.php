<?php // $Id$

//  For a given post, shows a report of all the ratings it has

    require_once("../../config.php");
    require_once("lib.php");

    $id   = required_param('id', PARAM_INT);
    $sort = optional_param('sort', '', PARAM_ALPHA);

    if (! $post = get_record('forum_posts', 'id', $id)) {
        error("Post ID was incorrect");
    }

    if (! $discussion = get_record('forum_discussions', 'id', $post->discussion)) {
        error("Discussion ID was incorrect");
    }

    if (! $forum = get_record('forum', 'id', $discussion->forum)) {
        error("Forum ID was incorrect");
    }

    if (! $course = get_record('course', 'id', $forum->course)) {
        error("Course ID was incorrect");
    }

    if (! $cm = get_coursemodule_from_instance('forum', $forum->id, $course->id)) {
        error("Course Module ID was incorrect");
    }

    require_login($course, false, $cm);
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);

    if (!$forum->assessed) {
        error("This activity does not use ratings");
    }

    if (!has_capability('mod/forum:viewrating', $context)) {
        error("You do not have the capability to view post ratings");
    }
    if (!has_capability('mod/forum:viewanyrating', $context) and $USER->id != $post->userid) {
        error("You can only look at results for posts that you made");
    }

    switch ($sort) {
        case 'firstname': $sqlsort = "u.firstname ASC"; break;
        case 'rating':    $sqlsort = "r.rating ASC"; break;
        default:          $sqlsort = "r.time ASC";
    }

    $scalemenu = make_grades_menu($forum->scale);

    $strratings = get_string('ratings', 'forum');
    $strrating  = get_string('rating', 'forum');
    $strname    = get_string('name');
    $strtime    = get_string('time');

    print_header("$strratings: ".format_string($post->subject));

    if (!$ratings = forum_get_ratings($post->id, $sqlsort)) {
        error("No ratings for this post: \"".format_string($post->subject)."\"");

    } else {
        echo "<table border=\"0\" cellpadding=\"3\" cellspacing=\"3\" class=\"generalbox\" style=\"width:100%\">";
        echo "<tr>";
        echo "<th class=\"header\" scope=\"col\">&nbsp;</th>";
        echo "<th class=\"header\" scope=\"col\"><a href=\"report.php?id=$post->id&amp;sort=firstname\">$strname</a></th>";
        echo "<th class=\"header\" scope=\"col\" style=\"width:100%\"><a href=\"report.php?id=$post->id&amp;sort=rating\">$strrating</a></th>";
        echo "<th class=\"header\" scope=\"col\"><a href=\"report.php?id=$post->id&amp;sort=time\">$strtime</a></th>";
        echo "</tr>";
        foreach ($ratings as $rating) {
            echo '<tr class="forumpostheader">';
            echo "<td>";
            print_user_picture($rating->id, $forum->course, $rating->picture);
            echo '</td><td>'.fullname($rating).'</td>';
            echo '<td style="white-space:nowrap" align="center" class="rating">'.$scalemenu[$rating->rating]."</td>";
            echo '<td style="white-space:nowrap" align="center" class="time">'.userdate($rating->time)."</td>";
            echo "</tr>\n";
        }
        echo "</table>";
        echo "<br />";
    }

    close_window_button();
    print_footer('none');
?>
