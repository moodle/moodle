<?php  // $Id$

    require_once("../../config.php");
    require_once("lib.php");

    require_variable($id);   // course module

    $format = optional_param('format', CHOICE_PUBLISH_NAMES, PARAM_INT);

    if (! $cm = get_record("course_modules", "id", $id)) {
        error("Course Module ID was incorrect");
    }

    if (! $course = get_record("course", "id", $cm->course)) {
        error("Course module is misconfigured");
    }

    require_login($course->id, false, $cm);

    if (!isteacher($course->id)) {
        error("Only teachers can look at this page");
    }

    if (!$choice = choice_get_choice($cm->instance)) {
        error("Course module is incorrect");
    }

    $strchoice = get_string("modulename", "choice");
    $strchoices = get_string("modulenameplural", "choice");
    $strresponses = get_string("responses", "choice");

    add_to_log($course->id, "choice", "report", "report.php?id=$cm->id", "$choice->id");

    print_header_simple(format_string($choice->name).": $strresponses", "",
                 "<a href=\"index.php?id=$course->id\">$strchoices</a> ->
                  <a href=\"view.php?id=$cm->id\">".format_string($choice->name,true)."</a> -> $strresponses", "");

/// Check to see if groups are being used in this choice
    if ($groupmode = groupmode($course, $cm)) {   // Groups are being used
        $currentgroup = setup_and_print_groups($course, $groupmode, "report.php?id=$cm->id");
    } else {
        $currentgroup = false;
    }

    if ($currentgroup) {
        $users = get_group_users($currentgroup, "u.firstname ASC", '', 'u.id, u.picture, u.firstname, u.lastname');
    } else {
        $users = get_course_users($course->id, "u.firstname ASC", '', 'u.id, u.picture, u.firstname, u.lastname');
    }

    if (!$users) {
        print_heading(get_string("nousersyet"));
        print_footer($course);
        exit;
    }

    if ($allresponses = get_records("choice_answers", "choiceid", $choice->id)) {
        foreach ($allresponses as $aa) {
            $answers[$aa->userid] = $aa;
        }
    } else {
        $answers = array () ;
    }

    $timenow = time();

    foreach ($choice->option as $optionid => $text) {
        $useranswer[$optionid] = array();
    }
    foreach ($users as $user) {
        if (!empty($user->id) and !empty($answers[$user->id])) {
            $answer = $answers[$user->id];
            $useranswer[(int)$answer->optionid][] = $user;
        } else {
            $useranswer[0][] = $user;
        }
    }
    foreach ($choice->option as $optionid => $text) {
        if (!$choice->option[$optionid]) {
            unset($useranswer[$optionid]);     // Throw away any data that doesn't apply
        }
    }
    ksort($useranswer);

    switch ($format) {
        case CHOICE_PUBLISH_NAMES:

            $tablewidth = (int) (100.0 / count($useranswer));

            echo "<table cellpadding=\"5\" cellspacing=\"10\" align=\"center\">";
            echo "<tr>";
            foreach ($useranswer as $optionid => $userlist) {
                if ($optionid) {
                    echo "<th class=\"col$optionid\" width=\"$tablewidth%\">";
                } else if ($choice->showunanswered) {
                    echo "<th class=\"col$optionid\" width=\"$tablewidth%\">";
                } else {
                    continue;
                }
                echo format_string(choice_get_option_text($choice, $optionid));
                echo "</th>";
            }
            echo "</tr><tr>";

            foreach ($useranswer as $optionid => $userlist) {
                if ($optionid) {
                    echo "<td class=\"col$optionid\" width=\"$tablewidth%\" valign=\"top\" nowrap=\"nowrap\">";
                } else if ($choice->showunanswered) {
                    echo "<td class=\"col$optionid\" width=\"$tablewidth%\" valign=\"top\" nowrap=\"nowrap\">";
                } else {
                    continue;
                }

                echo "<table width=\"100%\">";
                foreach ($userlist as $user) {
                    echo "<tr><td width=\"10\" nowrap=\"nowrap\">";
                    print_user_picture($user->id, $course->id, $user->picture);
                    echo "</td><td width=\"100%\" nowrap=\"nowrap\">";
                    echo "<p>".fullname($user, true)."</p>";
                    echo "</td></tr>";
                }
                echo "</table>";

                echo "</td>";
            }
            echo "</tr></table>";
            break;


        case CHOICE_PUBLISH_ANONYMOUS:
            $tablewidth = (int) (100.0 / count($useranswer));

            echo "<table cellpadding=\"5\" cellspacing=\"10\" align=\"center\">";
            echo "<tr>";
            foreach ($useranswer as $optionid => $userlist) {
                if ($optionid) {
                    echo "<th width=\"$tablewidth%\">";
                } else if ($choice->showunanswered) {
                    echo "<th width=\"$tablewidth%\">";
                } else {
                    continue;
                }
                echo choice_get_option_text($choice, $optionid);
                echo "</th>";
            }
            echo "</tr>";

            $maxcolumn = 0;
            foreach ($useranswer as $optionid => $userlist) {
                if (!$optionid and !$choice->showunanswered) {
                    continue;
                }
                $column[$optionid] = count($userlist);
                if ($column[$optionid] > $maxcolumn) {
                    $maxcolumn = $column[$optionid];
                }
            }

            echo "<tr>";
            foreach ($useranswer as $optionid => $userlist) {
                if (!$optionid and !$choice->showunanswered) {
                    continue;
                }
                $height = 0;
                if ($maxcolumn) {
                    $height = $COLUMN_HEIGHT * ((float)$column[$optionid] / (float)$maxcolumn);
                }
                echo "<td valign=\"bottom\" align=\"center\">";
                echo "<img src=\"column.png\" height=\"$height\" width=\"49\" alt=\"\" />";
                echo "</td>";
            }
            echo "</tr>";

            echo "<tr>";
            foreach ($useranswer as $optionid => $userlist) {
                if (!$optionid and !$choice->showunanswered) {
                    continue;
                }
                echo "<td align=\"center\">".$column[$optionid]."</td>";
            }
            echo "</tr></table>";

            break;
    }

print_footer($course);


?>
