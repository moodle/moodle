<?php // $Id$

//  For a given post, shows a report of all the ratings it has

    require_once("../../config.php");
    require_once("lib.php");

    $id   = required_param('id', PARAM_INT);
    $sort = optional_param('sort', '', PARAM_ALPHA);

    if (!$record = get_record('data_records', 'id', $id)) {
        error("Record ID is incorrect");
    }

    if (!$data = get_record('data', 'id', $record->dataid)) {
        error("Data ID is incorrect");
    }

    if (!$course = get_record('course', 'id', $data->course)) {
        error("Course is misconfigured");
    }

    if (!$cm = get_coursemodule_from_instance('data', $data->id, $course->id)) {
        error("Course Module ID was incorrect");
    }

    require_login($course->id, false, $cm);
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);

    if (!$data->assessed) {
        error("This activity does not use ratings");
    }

    if (!data_isowner($record->id) and !has_capability('mod/data:viewrating', $context) and !has_capability('mod/data:rate', $context)) {
        error("You can not view ratings");
    }

    switch ($sort) {
        case 'firstname': $sqlsort = "u.firstname ASC"; break;
        case 'rating':    $sqlsort = "r.rating ASC"; break;
        default:          $sqlsort = "r.id ASC";
    }

    $scalemenu = make_grades_menu($data->scale);

    $strratings = get_string('ratings', 'data');
    $strrating  = get_string('rating', 'data');
    $strname    = get_string('name');

    print_header($strratings);

    if (!$ratings = data_get_ratings($record->id, $sqlsort)) {
        error("No ratings for this record!");

    } else {
        echo "<table border=\"0\" cellpadding=\"3\" cellspacing=\"3\" class=\"generalbox\" style=\"width:100%\">";
        echo "<tr>";
        echo "<th class=\"header\" scope=\"col\">&nbsp;</th>";
        echo "<th class=\"header\" scope=\"col\"><a href=\"report.php?id=$record->id&amp;sort=firstname\">$strname</a></th>";
        echo "<th class=\"header\" scope=\"col\" style=\"width:100%\"><a href=\"report.php?id=$id&amp;sort=rating\">$strrating</a></th>";
        echo "</tr>";
        foreach ($ratings as $rating) {
            if (has_capability('mod/data:manageentries', $context)) {
                echo '<tr class="forumpostheadertopic">';
            } else {
                echo '<tr class="forumpostheader">';
            }
            echo '<td class="picture">';
            print_user_picture($rating->id, $data->course, $rating->picture, false, false, true);
            echo '</td>';
            echo '<td class="author"><a href="'.$CFG->wwwroot.'/user/view.php?id='.$rating->id.'&amp;course='.$data->course.'">'.fullname($rating).'</a></td>';
            echo '<td style="white-space:nowrap" align="center" class="rating">'.$scalemenu[$rating->rating].'</td>';
            echo "</tr>\n";
        }
        echo "</table>";
        echo "<br />";
    }

    close_window_button();
    print_footer('none');
?>
