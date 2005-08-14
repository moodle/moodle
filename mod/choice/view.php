<?php  // $Id$

    require_once("../../config.php");
    require_once("lib.php");

    $id = required_param('id',PARAM_INT);    // Course Module ID

    if (! $cm = get_record("course_modules", "id", $id)) {
        error("Course Module ID was incorrect");
    }

    if (! $course = get_record("course", "id", $cm->course)) {
        error("Course is misconfigured");
    }

    require_course_login($course, false, $cm);

    if (!$choice = choice_get_choice($cm->instance)) {
        error("Course module is incorrect");
    }

    if ($choice->option) {
        foreach ($choice->option as $optionid => $text) {
            $answerchecked[$optionid] = '';
        }
    }

    if (isset($USER->id) && $current = get_record('choice_answers', 'choiceid', $choice->id, 'userid', $USER->id)) {
        $answerchecked[$current->optionid] = 'checked="checked"';
    } else {
        $current = false;
    }

/// Submit any new data if there is any

    if ($form = data_submitted()) {
        $timenow = time();

        if (empty($form->answer)) {
            redirect("view.php?id=$cm->id", get_string('mustchooseone', 'choice'));

        } else {
            $countanswers = get_records("choice_answers", "optionid", $form->answer);
            if ($countanswers) {
                $countanswers = count($countanswers);
                } else {
                    $countanswers = 0;
                }
            $maxans = $choice->maxanswers[$form->answer];
            if (!($choice->limitanswers && ($countanswers >= $maxans) )) {
                if ($current) {
                    $newanswer = $current;
                    $newanswer->optionid = $form->answer;
                    $newanswer->timemodified = $timenow;
                    if (! update_record("choice_answers", $newanswer)) {
                        error("Could not update your choice because of a database error");
                    }
                    add_to_log($course->id, "choice", "choose again", "view.php?id=$cm->id", $choice->id, $cm->id);
                } else {
                    $newanswer = NULL;
                    $newanswer->choiceid = $choice->id;
                    $newanswer->userid = $USER->id;
                    $newanswer->optionid = $form->answer;
                    $newanswer->timemodified = $timenow;
                    if (! insert_record("choice_answers", $newanswer)) {
                        error("Could not save your choice");
                    }
                    add_to_log($course->id, "choice", "choose", "view.php?id=$cm->id", $choice->id, $cm->id);
                }
            } else {
                error("this choice is full!");
            }
        }
        redirect("view.php?id=$cm->id");
        exit;
    }


/// Display the choice and possibly results

    $strchoice = get_string("modulename", "choice");
    $strchoices = get_string("modulenameplural", "choice");

    add_to_log($course->id, "choice", "view", "view.php?id=$cm->id", $choice->id, $cm->id);

    print_header_simple(format_string($choice->name), "",
                 "<a href=\"index.php?id=$course->id\">$strchoices</a> -> ".format_string($choice->name), "", "", true,
                  update_module_button($cm->id, $course->id, $strchoice), navmenu($course, $cm));

/// Check to see if groups are being used in this choice
    if ($groupmode = groupmode($course, $cm)) {   // Groups are being used
        $currentgroup = setup_and_print_groups($course, $groupmode, "view.php?id=$cm->id");
    } else {
        $currentgroup = false;
    }

    if (isteacher($course->id)) {
        if ( $allanswers = get_records("choice_answers", "choiceid", $choice->id)) {            
            $responsecount = 0;
            foreach ($allanswers as $aa) {
                if (isstudent($course->id, $aa->userid) or isteacher($course->id, $aa->userid)) { //check to make sure user is enrolled in course.
                    $responsecount++;
                }
            }
        } else {
            $responsecount = 0;
        }
        echo '<div class="reportlink">';
        echo "<a href=\"report.php?id=$cm->id\">".get_string("viewallresponses", "choice", $responsecount)."</a>";
        echo '</div>';
    } else if (!$cm->visible) {
        notice(get_string("activityiscurrentlyhidden"));
    }

    if ($choice->text) {
        print_simple_box(format_text($choice->text, $choice->format), 'center', '70%', '', 5, 'generalbox', 'intro');
    }

    //if user has already made a selection, and they are not allowed to update it, show their selected answer.
    if (isset($USER->id) && ($current = get_record('choice_answers', 'choiceid', $choice->id, 'userid', $USER->id)) && !$choice->allowupdate) {
        print_simple_box(get_string("yourselection", "choice", userdate($choice->timeopen)).": ".format_string(choice_get_option_text($choice, $current->optionid)), "center");
    }

/// Print the form

    if ($choice->timeopen > time() ) {
        print_simple_box(get_string("notopenyet", "choice", userdate($choice->timeopen)), "center");
        print_footer($course);
        exit;
    }

    if ( (!$current or $choice->allowupdate) and ($choice->timeclose >= time() or $choice->timeclose == 0) ) {
    // They haven't made their choice yet or updates allowed and choice is open

        echo "<form name=\"form\" method=\"post\" action=\"view.php\">";        

        switch ($choice->display) {
            case CHOICE_DISPLAY_HORIZONTAL:
                echo "<table cellpadding=\"20\" cellspacing=\"20\" align=\"center\"><tr>";
                $aid = 0;
                foreach ($choice->option as $optionid => $text) {
                    $countanswers = (get_records("choice_answers", "optionid", $optionid));
                    if ($countanswers) {
                        $countanswers = count($countanswers);
                    } else {
                        $countanswers = 0;
                    }
                    $maxans = $choice->maxanswers[$optionid];
                    
                    if (isset($text)) { 
                    echo "<td align=\"center\" valign=\"top\">";
                    echo "<input type=\"radio\" name=\"answer\" value=\"".$optionid."\" ".$answerchecked[$optionid]." alt=\"".strip_tags(format_text($text))."\"";
                    if ($choice->limitanswers && ($countanswers >= $maxans) && !($answerchecked[$optionid]) ) {
                        echo ' disabled="disabled"';
                    }                    
                    echo " />";                    
                    if ($choice->limitanswers && ($countanswers >= $maxans) ) {               
                            echo format_text($text."<br /><strong>".get_string('full', 'choice')."</strong>");
                        } else {
                            echo format_text($text);

                        }
                        echo "</td>";
                        $aid++;
                    }
                }
                echo "</tr>";
                echo "</table>";
                break;

            case CHOICE_DISPLAY_VERTICAL:
                $aid = 0;
                $displayoptions->para = false;
                echo "<table cellpadding=\"10\" cellspacing=\"10\" align=\"center\">";
                foreach ($choice->option as $optionid => $text) {
                    if ($text) {
                        echo "<tr><td align=\"left\">";
                        echo "<input type=\"radio\" name=\"answer\" value=\"".$optionid."\" ".$answerchecked[$optionid]." alt=\"".strip_tags(format_text($text, FORMAT_MOODLE, $displayoptions))."\" />";
                        $countanswers = get_records("choice_answers", "optionid", $optionid);
                        if ($countanswers) {
                            $countanswers = count($countanswers);
                        } else {
                            $countanswers = 0;
                        }
                        $maxans = $choice->maxanswers[$optionid];
                        if ($choice->limitanswers && ($countanswers >= $maxans) ) {
                            if (!($answerchecked[$optionid])) {
                                echo "<script type=\"text/javascript\">";
                                echo "document.form.answer[".$aid."].disabled = true;";
                                echo "</script>";
                            }
                        } 
                        echo format_text($text. ' ', FORMAT_MOODLE, $displayoptions);
                        
                         if ($choice->limitanswers && ($choice->release==CHOICE_RELEASE_ALWAYS) ){
                            echo "</td><td>";
                            
                            if ($maxans-$countanswers==0) {
                                echo get_string('full', 'choice');
                            } elseif ($maxans-$countanswers==1) {
                                echo ($maxans - $countanswers);
                                echo " ".get_string('spaceleft', 'choice');
                            } else {
                                echo ($maxans - $countanswers);
                                echo " ".get_string('spacesleft', 'choice');
                            }
                            echo "</td>";
                         } else if ($choice->limitanswers && ($countanswers >= $maxans)) {
                            echo " <strong>".get_string('full', 'choice')."</strong>";
                         }

                        echo "</td>";
                        echo "</tr>";
                        $aid++;
                    }
                }
                echo "</table>";
                break;
        }

        echo "<center>";
        echo "<input type=\"hidden\" name=\"id\" value=\"$cm->id\" />";
        if (isstudent($course->id) or isteacher($course->id, 0)) {
            echo "<input type=\"submit\" value=\"".get_string("savemychoice","choice")."\" />";
        } else {
            print_string('havetologin', 'choice');
        }
        echo "</center>";
        echo "</form>";

    }



    // print the results at the bottom of the screen

    if (  $choice->release == CHOICE_RELEASE_ALWAYS or
        ( $choice->release == CHOICE_RELEASE_AFTER_ANSWER and $current ) or
        ( $choice->release == CHOICE_RELEASE_AFTER_CLOSE and $choice->timeclose <= time() ) )  {

        print_heading(get_string("responses", "choice"));

        if ($currentgroup) {
            $users = get_group_users($currentgroup, "u.firstname ASC", '', 'u.id, u.picture, u.firstname, u.lastname');
        } else {
            $users = get_course_users($course->id, "u.firstname ASC", '', 'u.id, u.picture, u.firstname, u.lastname') + get_admins();
        }


        if (!$users) {
            print_heading(get_string("nousersyet"));
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

        switch ($choice->publish) {
          case CHOICE_PUBLISH_NAMES:

            $isteacher = isteacher($course->id);

            $tablewidth = (int) (100.0 / count($useranswer));

            echo "<table cellpadding=\"5\" cellspacing=\"10\" align=\"center\" class=\"results names\">";
            echo "<tr>";
            $count = 0;
            foreach ($useranswer as $optionid => $userlist) {
                if ($optionid) {
                    echo "<th class=\"col$count header\" width=\"$tablewidth%\">";
                } else if ($choice->showunanswered) {
                    echo "<th class=\"col$count header\" width=\"$tablewidth%\">";
                } else {
                    continue;
                }
                echo format_string(choice_get_option_text($choice, $optionid));
                echo "</th>";
                $count++;
            }
            echo "</tr><tr>";

            $count = 0;
            foreach ($useranswer as $optionid => $userlist) {
                if ($optionid) {
                    echo "<td class=\"col$count data\" width=\"$tablewidth%\" valign=\"top\" nowrap=\"nowrap\">";
                } else if ($choice->showunanswered) {
                    echo "<td class=\"col$count data\" width=\"$tablewidth%\" valign=\"top\" nowrap=\"nowrap\">";
                } else {
                    continue;
                }

                echo "<table width=\"100%\">";
                foreach ($userlist as $user) {
                    if (!($optionid==0 && isadmin($user->id)) && !($optionid==0 && isteacher($course->id, $user->id) && !(isteacheredit($course->id, $user->id)) )  ) { //make sure admins and hidden teachers are not shown in not answered yet column.
                        echo "<tr><td width=\"10\" nowrap=\"nowrap\" class=\"picture\">";
                        print_user_picture($user->id, $course->id, $user->picture);
                        echo "</td><td width=\"100%\" nowrap=\"nowrap\" class=\"fullname\">";
                        echo fullname($user, $isteacher);
                        echo "</td></tr>";
                    }
                }
                $count++;
                echo "</table>";

                echo "</td>";
            }
            echo "</tr><tr>";
            $count = 0;
            foreach ($useranswer as $optionid => $userlist) {
                if (!$optionid and !$choice->showunanswered) {
                    continue;
                }
                echo "<td align=\"center\" class=\"count\">";
                $countanswers = count_records("choice_answers", "optionid", $optionid);
                if ($choice->limitanswers && !$optionid==0) {
                    echo get_string("taken", "choice").":";
                    echo $countanswers;
                    echo "<br>";
                    echo get_string("limit", "choice").":";
                    $choice_option = get_record("choice_options", "id", $optionid);
                    echo $choice_option->maxanswers;
                }
                echo "</td>";
                $count++;
            }
            
            
            
            
            echo "</tr></table>";
            break;


          case CHOICE_PUBLISH_ANONYMOUS:
            $tablewidth = (int) (100.0 / count($useranswer));

            echo "<table cellpadding=\"5\" cellspacing=\"10\" align=\"center\" class=\"results anonymous\">";
            echo "<tr>";
            $count = 0;
            foreach ($useranswer as $optionid => $userlist) {
                if ($optionid) {
                    echo "<th width=\"$tablewidth%\" class=\"col$count header\">";
                } else if ($choice->showunanswered) {
                    echo "<th width=\"$tablewidth%\" class=\"col$count header\">";
                } else {
                    continue;
                }
                echo format_string(choice_get_option_text($choice, $optionid));
                echo "</th>";
                $count++;
            }
            echo "</tr><tr>";

            $maxcolumn = 0;
            foreach ($useranswer as $optionid => $userlist) {
                if (!$optionid and !$choice->showunanswered) {
                    continue;
                }
                $column[$optionid] = 0;
                foreach ($userlist as $user) {
                    if (!($optionid==0 && isadmin($user->id)) && !($optionid==0 && isteacher($course->id, $user->id) && !(isteacheredit($course->id, $user->id)) )  ) { //make sure admins and hidden teachers are not shown in not answered yet column.
                         $column[$optionid]++;
                    }
                }
                if ($column[$optionid] > $maxcolumn) {
                    $maxcolumn = $column[$optionid];
                }
            }

            echo "</tr><tr>";
            $count = 0;
            foreach ($useranswer as $optionid => $userlist) {
                if (!$optionid and !$choice->showunanswered) {
                    continue;
                }
                $height = 0;
                if ($maxcolumn) {
                    $height = $COLUMN_HEIGHT * ((float)$column[$optionid] / (float)$maxcolumn);
                }
                echo "<td valign=\"bottom\" align=\"center\" class=\"col$count data\">";
                echo "<img src=\"column.png\" height=\"$height\" width=\"49\" alt=\"\" />";
                echo "</td>";
                $count++;
            }
            echo "</tr>";

            echo "<tr>";
            $count = 0;
            foreach ($useranswer as $optionid => $userlist) {
                if (!$optionid and !$choice->showunanswered) {
                    continue;
                }
                echo "<td align=\"center\" class=\"col$count count\">";
                if ($choice->limitanswers && !$optionid==0) {
                    echo get_string("taken", "choice").":";
                    echo $column[$optionid];
                    echo "<br>";
                    echo get_string("limit", "choice").":";
                    $choice_option = get_record("choice_options", "id", $optionid);
                    echo $choice_option->maxanswers;
                } else {
                    echo $column[$optionid];
                }
                echo "</td>";
                $count++;
            }
            echo "</tr></table>";

            break;
        }
    }

    print_footer($course);


?>
