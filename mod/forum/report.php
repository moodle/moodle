<?PHP // $Id$

//  For a given post, shows a report of all the ratings it has

    require_once("../../config.php");
    require_once("lib.php");

    require_variable($id);

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

    if (!isteacher($course->id) and $USER->id != $post->userid) {
        error("You can only look at results for posts you own");
    }

    if (!isset($sort)) {
        $sort = "r.time";
    }

    $scalemenu = make_grades_menu($forum->scale);

    $strratings = get_string("ratings", "forum");
    $strrating = get_string("rating", "forum");
    $strname = get_string("name");
    $strtime = get_string("time");

    print_header("$strratings: $post->subject");

    if (!$ratings = forum_get_ratings($post->id, $sort)) {
        error("No ratings for this post: \"$post->subject\"");

    } else {
        echo "<table border=0 cellpadding=3 cellspacing=3 class=generalbox width=100%>";
        echo "<tr>";
        echo "<th>&nbsp;</th>";
        echo "<th><a href=report.php?id=$post->id&sort=u.firstname>$strname</a>";
        echo "<th width=100%><a href=report.php?id=$post->id&sort=r.rating>$strrating</a>";
        echo "<th><a href=report.php?id=$post->id&sort=r.time>$strtime</a>";
        foreach ($ratings as $rating) {
            if (isteacher($discussion->course, $rating->id)) {
                echo "<tr bgcolor=\"$THEME->cellcontent2\">";
            } else {
                echo "<tr bgcolor=\"$THEME->cellcontent\">";
            }
            echo "<td>";
            print_user_picture($rating->id, $forum->course, $rating->picture);
            echo "<td nowrap><p><font size=-1>$rating->firstname $rating->lastname</p>";
            echo "<td nowrap align=center><p><font size=-1>".$scalemenu[$rating->rating]."</p>";
            echo "<td nowrap align=center><p><font size=-1>".userdate($rating->time)."</p>";
            echo "</tr>\n";
        }
        echo "</table>";
        echo "<br />";
    }

    close_window_button();

?>
