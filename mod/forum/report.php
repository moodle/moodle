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

    print_header("Ratings for: $post->subject");

    if (!$ratings = forum_get_ratings($post->id, $sort)) {
        echo "No ratings for this post: \"$post->subject\"";
        die;
    } else {
        echo "<TABLE BORDER=0 CELLPADDING=3>";
        echo "<TR>";
        echo "<TH>&nbsp;</TH>";
        echo "<TH><A HREF=report.php?id=$post->id&sort=u.firstname>Name</A>";
        echo "<TH><A HREF=report.php?id=$post->id&sort=r.rating>Rating</A>";
        echo "<TH><A HREF=report.php?id=$post->id&sort=r.time>Date</A>";
        foreach ($ratings as $rating) {
            if (isteacher($discussion->course, $rating->id)) {
                echo "<TR BGCOLOR=\"$THEME->cellcontent2\">";
            } else {
                echo "<TR BGCOLOR=\"$THEME->cellcontent\">";
            }
            echo "<TD>";
            print_user_picture($rating->id, $forum->course, $rating->picture);
            echo "<TD NOWRAP><P><FONT SIZE=-1>$rating->firstname $rating->lastname</P>";
            echo "<TD NOWRAP><P><FONT SIZE=-1>".$FORUM_POST_RATINGS[$rating->rating]."</P>";
            echo "<TD NOWRAP><P><FONT SIZE=-1>".userdate($rating->time)."</P>";
            echo "</TR>\n";
        }
        echo "</TABLE>";
    }

    close_window_button();

?>
