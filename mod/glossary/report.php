<?PHP   // $Id$
//  For a given entry, shows a report of all the ratings it has

    require_once("../../config.php");
    require_once("lib.php");

    require_variable($id);
    global $USER;
    
    if (! $entry = get_record("glossary_entries", "id", $id)) {
        error("Entry ID was incorrect");
    }

    if (! $glossary = get_record("glossary", "id", $entry->glossaryid)) {
        error("Glossary ID was incorrect");
    }

    if (! $course = get_record("course", "id", $glossary->course)) {
        error("Course ID was incorrect");
    }

    if (!isteacher($course->id) and $USER->id != $entry->userid) {
        error("You can only look at results for your own entries");
    }

    if (!isset($sort)) {
        $sort = "r.time";
    }

    $scalemenu = make_grades_menu($glossary->scale);

    $strratings = get_string("ratings", "glossary");
    $strrating = get_string("rating", "glossary");
    $strname = get_string("name");
    $strtime = get_string("time");

    print_header("$strratings: $entry->subject");

    if (!$ratings = glossary_get_ratings($entry->id, $sort)) {
        error("No ratings for this entry: \"$entry->subject\"");

    } else {
        echo "<table border=0 cellpadding=3 cellspacing=3 class=generalbox width=100%>";
        echo "<tr>";
        echo "<th>&nbsp;</th>";
        echo "<th><a href=report.php?id=$entry->id&sort=u.firstname>$strname</a>";
        echo "<th width=100%><a href=report.php?id=$entry->id&sort=r.rating>$strrating</a>";
        echo "<th><a href=report.php?id=$entry->id&sort=r.time>$strtime</a>";
        foreach ($ratings as $rating) {
            if (isteacher($glossary->course, $rating->id)) {
                echo "<tr bgcolor=\"$THEME->cellcontent2\">";
            } else {
                echo "<tr bgcolor=\"$THEME->cellcontent\">";
            }
            echo "<td>";
            print_user_picture($rating->id, $glossary->course, $rating->picture);
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
