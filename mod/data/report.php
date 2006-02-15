<?php // $Id$

//  For a given post, shows a report of all the ratings it has

    require_once("../../config.php");
    require_once("lib.php");

    $id = required_param('id',PARAM_INT);

    if (! $dataratings= get_record("data_ratings", "id", $id)) {
        error("rating ID was incorrect");
    }

    if (!$record = get_record('data_records','id',$dataratings->recordid)) {
        error("rating ID was incorrect");
    }
    
    if (!$data = get_record('data','id',$record->id)) {
        error("rating ID was incorrect");
    }
    
    if (!isset($sort)) {
        $sort = "r.id";
    }

    $scalemenu = make_grades_menu($data->scale);

    $strratings = get_string("ratings", "data");
    $strrating = get_string("rating", "data");
    $strname = get_string("name");

    print_header("$strratings: ".format_string($post->subject));

    if (!$ratings = data_get_ratings($record->id, $sort)) {
        error("No ratings for this record!");

    } else {
        echo "<table border=\"0\" cellpadding=\"3\" cellspacing=\"3\" class=\"generalbox\" width=\"100%\">";
        echo "<tr>";
        echo "<th>&nbsp;</th>";
        echo "<th><a href=\"report.php?id=$dataratings->id&amp;sort=u.firstname\">$strname</a>";
        echo "<th width=\"100%\"><a href=\"report.php?id=$dataratings->id&amp;sort=r.rating\">$strrating</a>";
        foreach ($ratings as $rating) {
            if (isteacher($data->course)) {
                echo '<tr class="forumpostheadertopic">';
            } else {
                echo '<tr class="forumpostheader">';
            }
            echo "<td>";
            print_user_picture($rating->id, $data->course, $rating->picture);
            echo '<td nowrap="nowrap"><p><font size="-1">'.fullname($rating).'</p>';
            echo '<td nowrap="nowrap" align="center"><p><font size="-1">'.$scalemenu[$rating->rating]."</p>";
            echo "</tr>\n";
        }
        echo "</table>";
        echo "<br />";
    }

    close_window_button();

?>
