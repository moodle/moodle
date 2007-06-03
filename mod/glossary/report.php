<?php   // $Id$

//  For a given entry, shows a report of all the ratings it has

    require_once("../../config.php");
    require_once("lib.php");

    $id   = required_param('id', PARAM_INT);
    $sort = optional_param('sort', '', PARAM_ALPHA);

    if (! $entry = get_record('glossary_entries', 'id', $id)) {
        error("Entry ID was incorrect");
    }

    if (! $glossary = get_record('glossary', 'id', $entry->glossaryid)) {
        error("Glossary ID was incorrect");
    }

    if (! $course = get_record('course', 'id', $glossary->course)) {
        error("Course ID was incorrect");
    }

    if (! $cm = get_coursemodule_from_instance('glossary', $glossary->id, $course->id)) {
        error("Course Module ID was incorrect");
    }

    require_login($course, false, $cm);
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);

    if (!$glossary->assessed) {
        error("This activity does not use ratings");
    }

    if (!has_capability('mod/glossary:manageentries', $context) and $USER->id != $entry->userid) {
        error("You can only look at results for your own entries");
    }

    switch ($sort) {
        case 'firstname': $sqlsort = "u.firstname ASC"; break;
        case 'rating':    $sqlsort = "r.rating ASC"; break;
        default:          $sqlsort = "r.time ASC";
    }

    $scalemenu = make_grades_menu($glossary->scale);

    $strratings = get_string('ratings', 'glossary');
    $strrating  = get_string('rating', 'glossary');
    $strname    = get_string('name');
    $strtime    = get_string('time');

    print_header("$strratings: $entry->concept");

    if (!$ratings = glossary_get_ratings($entry->id, $sqlsort)) {
        error("No ratings for this entry: \"$entry->concept\"");

    } else {
        echo "<table border=\"0\" cellpadding=\"3\" cellspacing=\"3\" class=\"generalbox\" style=\"width:100%\">";
        echo "<tr>";
        echo "<th class=\"header\" scope=\"col\">&nbsp;</th>";
        echo "<th class=\"header\" scope=\"col\"><a href=\"report.php?id=$entry->id&amp;sort=firstname\">$strname</a></th>";
        echo "<th class=\"header\" scope=\"col\" style=\"width:100%\"><a href=\"report.php?id=$entry->id&amp;sort=rating\">$strrating</a></th>";
        echo "<th class=\"header\" scope=\"col\"><a href=\"report.php?id=$entry->id&amp;sort=time\">$strtime</a></th>";
        echo "</tr>";
        foreach ($ratings as $rating) {
            if (has_capability('mod/glossary:manageentries', $context)) {
                echo '<tr class="teacher">';
            } else {
                echo '<tr>';
            }
            echo '<td class="picture">';
            print_user_picture($rating->id, $glossary->course, $rating->picture, false, false, true);
            echo '</td>';
            echo '<td class="author"><a href="'.$CFG->wwwroot.'/user/view.php?id='.$rating->id.'&amp;course='.$glossary->course.'">'.fullname($rating).'</a></td>';
            echo '<td style="white-space:nowrap" align="center" class="rating">'.$scalemenu[$rating->rating].'</td>';
            echo '<td style="white-space:nowrap" align="center" class="time">'.userdate($rating->time).'</td>';
            echo "</tr>\n";
        }
        echo "</table>";
        echo "<br />";
    }

    close_window_button();
    print_footer('none');
?>
