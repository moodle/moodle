<?php   // $Id$
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

    print_header("$strratings: $entry->concept");

    if (!$ratings = glossary_get_ratings($entry->id, $sort)) {
        error("No ratings for this entry: \"$entry->concept\"");

    } else {
        echo "<table border=\"0\" cellpadding=\"3\" cellspacing=\"3\" class=\"generalbox\" width=\"100%\">";
        echo "<tr>";
        echo "<th>&nbsp;</th>";
        echo "<th><a href=\"report.php?id=$entry->id&amp;sort=u.firstname\">$strname</a></th>";
        echo "<th width=\"100%\"><a href=\"report.php?id=$entry->id&amp;sort=r.rating\">$strrating</a></th>";
        echo "<th><a href=\"report.php?id=$entry->id&amp;sort=r.time\">$strtime</a></th>";
        foreach ($ratings as $rating) {
            if (isteacher($glossary->course, $rating->id)) {
                echo '<tr class="teacher">';
            } else {
                echo '<tr>';
            }
            echo '<td class="picture">';
            print_user_picture($rating->id, $glossary->course, $rating->picture);
            echo '</td>';
            echo '<td nowrap="nowrap" class="author">'.fullname($rating).'</td>';
            echo '<td nowrap="nowrap" align="center" class="author">'.$scalemenu[$rating->rating].'</td>';
            echo '<td nowrap="nowrap" align="center" class="author">'.userdate($rating->time).'</td>';
            echo "</tr>\n";
        }
        echo "</table>";
        echo "<br />";
    }

    close_window_button();

?>
