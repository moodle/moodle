<?php // $Id$

$COLUMN_HEIGHT = 300;

define('CHOICE_PUBLISH_ANONYMOUS', '0');
define('CHOICE_PUBLISH_NAMES',     '1');

define('CHOICE_SHOWRESULTS_NOT',          '0');
define('CHOICE_SHOWRESULTS_AFTER_ANSWER', '1');
define('CHOICE_SHOWRESULTS_AFTER_CLOSE',  '2');
define('CHOICE_SHOWRESULTS_ALWAYS',       '3');

define('CHOICE_DISPLAY_HORIZONTAL',  '0');
define('CHOICE_DISPLAY_VERTICAL',    '1');

$CHOICE_PUBLISH = array (CHOICE_PUBLISH_ANONYMOUS  => get_string('publishanonymous', 'choice'),
                         CHOICE_PUBLISH_NAMES      => get_string('publishnames', 'choice'));

$CHOICE_SHOWRESULTS = array (CHOICE_SHOWRESULTS_NOT          => get_string('publishnot', 'choice'),
                         CHOICE_SHOWRESULTS_AFTER_ANSWER => get_string('publishafteranswer', 'choice'),
                         CHOICE_SHOWRESULTS_AFTER_CLOSE  => get_string('publishafterclose', 'choice'),
                         CHOICE_SHOWRESULTS_ALWAYS       => get_string('publishalways', 'choice'));

$CHOICE_DISPLAY = array (CHOICE_DISPLAY_HORIZONTAL   => get_string('displayhorizontal', 'choice'),
                         CHOICE_DISPLAY_VERTICAL     => get_string('displayvertical','choice'));

/// Standard functions /////////////////////////////////////////////////////////

function choice_user_outline($course, $user, $mod, $choice) {
    if ($answer = get_record('choice_answers', 'choiceid', $choice->id, 'userid', $user->id)) {
        $result->info = "'".format_string(choice_get_option_text($choice, $answer->optionid))."'";
        $result->time = $answer->timemodified;
        return $result;
    }
    return NULL;
}


function choice_user_complete($course, $user, $mod, $choice) {
    if ($answer = get_record('choice_answers', "choiceid", $choice->id, "userid", $user->id)) {
        $result->info = "'".format_string(choice_get_option_text($choice, $answer->optionid))."'";
        $result->time = $answer->timemodified;
        echo get_string("answered", "choice").": $result->info. ".get_string("updated", '', userdate($result->time));
    } else {
        print_string("notanswered", "choice");
    }
}


function choice_add_instance($choice) {
// Given an object containing all the necessary data,
// (defined by the form in mod.html) this function
// will create a new instance and return the id number
// of the new instance.

    $choice->timemodified = time();

    if (empty($choice->timerestrict)) {
        $choice->timeopen = 0;
        $choice->timeclose = 0;
    }

    //insert answers
    if ($choice->id = insert_record("choice", $choice)) {
        foreach ($choice->option as $key => $value) {
            $value = trim($value);
            if (isset($value) && $value <> '') {
                $option = new object();
                $option->text = $value;
                $option->choiceid = $choice->id;
                if (isset($choice->limit[$key])) {
                    $option->maxanswers = $choice->limit[$key];
                }
                $option->timemodified = time();
                insert_record("choice_options", $option);
            }
        }
    }
    return $choice->id;
}


function choice_update_instance($choice) {
// Given an object containing all the necessary data,
// (defined by the form in mod.html) this function
// will update an existing instance with new data.

    $choice->id = $choice->instance;
    $choice->timemodified = time();


    if (empty($choice->timerestrict)) {
        $choice->timeopen = 0;
        $choice->timeclose = 0;
    }

    //update, delete or insert answers
    foreach ($choice->option as $key => $value) {
        $value = trim($value);
        $option = new object();
        $option->text = $value;
        $option->choiceid = $choice->id;
        if (isset($choice->limit[$key])) {
            $option->maxanswers = $choice->limit[$key];
        }
        $option->timemodified = time();
        if (isset($choice->optionid[$key]) && !empty($choice->optionid[$key])){//existing choice record
            $option->id=$choice->optionid[$key];
            if (isset($value) && $value <> '') {
                update_record("choice_options", $option);
            } else { //empty old option - needs to be deleted.
                delete_records("choice_options", "id", $option->id);
            }
        } else {
            if (isset($value) && $value <> '') {
                insert_record("choice_options", $option);
            }
        }
    }

    return update_record('choice', $choice);

}

function choice_show_form($choice, $user, $cm) {

//$cdisplay is an array of the display info for a choice $cdisplay[$optionid]->text  - text name of option.
//                                                                            ->maxanswers -maxanswers for this option
//                                                                            ->full - whether this option is full or not. 0=not full, 1=full
    $cdisplay = array();

    $aid = 0;
    foreach ($choice->option as $optionid => $text) {
        if (isset($text)) { //make sure there are no dud entries in the db with blank text values.
            $countanswers = (get_records("choice_answers", "optionid", $optionid));
            $countans = 0;
            $context = get_context_instance(CONTEXT_MODULE, $cm->id);
            if (!empty($countanswers)) {
                foreach ($countanswers as $ca) { //only return enrolled users.
                    if (has_capability('mod/choice:choose', $context)) {
                        $countans = $countans+1;
                    }
                }
            }
            if ($countanswers) {
                $countanswers = count($countanswers);
            } else {
                $countanswers = 0;
            }
            $maxans = $choice->maxanswers[$optionid];

            $cdisplay[$aid]->optionid = $optionid;
            $cdisplay[$aid]->text = $text;
            $cdisplay[$aid]->maxanswers = $maxans;
            $cdisplay[$aid]->countanswers = $countans;

            if ($current = get_record('choice_answers', 'choiceid', $choice->id, 'userid', $user->id, 'optionid', $optionid)) {
                $cdisplay[$aid]->checked = ' checked="checked" ';
            } else {
                $cdisplay[$aid]->checked = '';
            }
            if ($choice->limitanswers && ($countans >= $maxans) && (empty($cdisplay[$aid]->checked)) ) {
                $cdisplay[$aid]->disabled = ' disabled="disabled" ';
            } else {
                $cdisplay[$aid]->disabled = '';
            }
            $aid++;
        }
    }

    switch ($choice->display) {
        case CHOICE_DISPLAY_HORIZONTAL:
            echo "<table cellpadding=\"20\" cellspacing=\"20\" class=\"boxaligncenter\"><tr>";

            foreach ($cdisplay as $cd) {
                echo "<td align=\"center\" valign=\"top\">";
                echo "<input type=\"radio\" name=\"answer\" value=\"".$cd->optionid."\" alt=\"".strip_tags(format_text($cd->text))."\"". $cd->checked.$cd->disabled." />";
                if (!empty($cd->disabled)) {
                    echo format_text($cd->text."<br /><strong>".get_string('full', 'choice')."</strong>");
                } else {
                    echo format_text($cd->text);
                }
                echo "</td>";
            }
            echo "</tr>";
            echo "</table>";
            break;

        case CHOICE_DISPLAY_VERTICAL:
            $displayoptions->para = false;
            echo "<table cellpadding=\"10\" cellspacing=\"10\" class=\"boxaligncenter\">";
            foreach ($cdisplay as $cd) {
                echo "<tr><td align=\"left\">";
                echo "<input type=\"radio\" name=\"answer\" value=\"".$cd->optionid."\" alt=\"".strip_tags(format_text($cd->text))."\"". $cd->checked.$cd->disabled." />";

                echo format_text($cd->text. ' ', FORMAT_MOODLE, $displayoptions); //display text for option.

                if ($choice->limitanswers && ($choice->showresults==CHOICE_SHOWRESULTS_ALWAYS) ){ //if limit is enabled, and show results always has been selected, display info beside each choice.
                    echo "</td><td>";

                    if (!empty($cd->disabled)) {
                        echo get_string('full', 'choice');
                    } elseif(!empty($cd->checked)) {
                                //currently do nothing - maybe some text could be added here to signfy that the choice has been 'selected'
                    } elseif ($cd->maxanswers-$cd->countanswers==1) {
                        echo ($cd->maxanswers - $cd->countanswers);
                        echo " ".get_string('spaceleft', 'choice');
                    } else {
                        echo ($cd->maxanswers - $cd->countanswers);
                        echo " ".get_string('spacesleft', 'choice');
                    }
                    echo "</td>";
                } else if ($choice->limitanswers && ($cd->countanswers >= $cd->maxanswers)) {  //if limitanswers and answers exceeded, display "full" beside the choice.
                    echo " <strong>".get_string('full', 'choice')."</strong>";
                }
                echo "</td>";
                echo "</tr>";
            }
        echo "</table>";
        break;
    }
    //show save choice button
    echo '<div class="button">';
    echo "<input type=\"hidden\" name=\"id\" value=\"$cm->id\" />";
    if (!isguest()) { //don't show save button if the logged in user is the guest user.
        echo "<input type=\"submit\" value=\"".get_string("savemychoice","choice")."\" />";
        
        if ($choice->allowupdate && $aaa = get_record('choice_answers', 'choiceid', $choice->id, 'userid', $user->id)) {
            echo "<br /><a href='view.php?id=".$cm->id."&action=delchoice'>".get_string("removemychoice","choice")."</a>";
        }
        
    } else {
        print_string('havetologin', 'choice');
    }
    echo "</div>";
}

function choice_user_submit_response($formanswer, $choice, $userid, $courseid, $cm) {

    $current = get_record('choice_answers', 'choiceid', $choice->id, 'userid', $userid);
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);
    $countanswers = get_records("choice_answers", "optionid", $formanswer);
    if ($countanswers) {
        $countans = 0;
        foreach ($countanswers as $ca) { //only return enrolled users.
            if (has_capability('mod/choice:choose', $context)) {
                $countans = $countans+1;
            }
        }

        $countanswers = $countans;
    } else {
        $countanswers = 0;
    }
    $maxans = $choice->maxanswers[$formanswer];

    if (!($choice->limitanswers && ($countanswers >= $maxans) )) {
        if ($current) {

            $newanswer = $current;
            $newanswer->optionid = $formanswer;
            $newanswer->timemodified = time();
            if (! update_record("choice_answers", $newanswer)) {
                error("Could not update your choice because of a database error");
            }
            add_to_log($courseid, "choice", "choose again", "view.php?id=$cm->id", $choice->id, $cm->id);
        } else {
            $newanswer = NULL;
            $newanswer->choiceid = $choice->id;
            $newanswer->userid = $userid;
            $newanswer->optionid = $formanswer;
            $newanswer->timemodified = time();
            if (! insert_record("choice_answers", $newanswer)) {
                error("Could not save your choice");
            }
            add_to_log($courseid, "choice", "choose", "view.php?id=$cm->id", $choice->id, $cm->id);
        }
    } else {
        if (!($current->optionid==$formanswer)) { //check to see if current choice already selected - if not display error
            error("this choice is full!");
        }
    }
}


function choice_show_reportlink($choice, $courseid, $cmid, $groupmode) {
    //TODO: rewrite with SQL
    $currentgroup = get_current_group($courseid);
    if ($allanswers = get_records("choice_answers", "choiceid", $choice->id)) {
        $responsecount = 0;
        foreach ($allanswers as $aa) {
            if ($groupmode and $currentgroup) {
                if (groups_is_member($currentgroup, $aa->userid)) {
                    $responsecount++;
                }
            } else {
                $responsecount++;
            }
        }
    } else {
        $responsecount = 0;
    }
    echo '<div class="reportlink">';
    echo "<a href=\"report.php?id=$cmid\">".get_string("viewallresponses", "choice", $responsecount)."</a>";
    echo '</div>';
}

function choice_show_results($choice, $course, $cm, $forcepublish='') {

    global $CFG, $COLUMN_HEIGHT, $USER;
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);

    print_heading(get_string("responses", "choice"));

    if (empty($forcepublish)) { //alow the publish setting to be overridden
        $forcepublish = $choice->publish;
    }

    $groupmode = groups_get_activity_groupmode($cm);

    if ($groupmode > 0) {
        $currentgroup = groups_get_activity_group($cm);
    } else {
        $currentgroup = 0;
    }

    $users = get_users_by_capability($context, 'mod/choice:choose', 'u.id, u.picture, u.firstname, u.lastname, u.idnumber', 'u.firstname ASC', '', '', $currentgroup, '', false);

    if (!empty($CFG->enablegroupings) && !empty($cm->groupingid) && !empty($users)) {
        $groupingusers = groups_get_grouping_members($cm->groupingid, 'u.id', 'u.id');
        foreach($users as $key => $user) {
            if (!isset($groupingusers[$user->id])) {
                unset($users[$key]);
            }
        }
    }

    if (!$users) {
        print_heading(get_string("nousersyet"));
    }

    $answers = array () ;
    if ($allresponses = get_records("choice_answers", "choiceid", $choice->id)) {
        foreach ($allresponses as $aa) {
            //TODO: rewrite with SQL
            if ($groupmode and $currentgroup) {
                if (groups_is_member($currentgroup, $aa->userid)) {
                    $answers[$aa->userid] = $aa;
                }
            } else {
                $answers[$aa->userid] = $aa;
            }
        }
    }

    $timenow = time();

    foreach ($choice->option as $optionid => $text) {
        $useranswer[$optionid] = array();
    }
    if (!empty($users)) {
        foreach ($users as $user) {
            if (!empty($user->id) and !empty($answers[$user->id])) {
                $answer = $answers[$user->id];
                $useranswer[(int)$answer->optionid][] = $user;
            } else {
                $useranswer[0][] = $user;
            }
        }
    }
    foreach ($choice->option as $optionid => $text) {
        if (!$choice->option[$optionid]) {
            unset($useranswer[$optionid]);     // Throw away any data that doesn't apply
        }
    }
    ksort($useranswer);
    switch ($forcepublish) {
        case CHOICE_PUBLISH_NAMES:

            $tablewidth = (int) (100.0 / count($useranswer));
            if (has_capability('mod/choice:readresponses', $context)) {
                echo '<div id="tablecontainer">';
                echo '<form id="attemptsform" method="post" action="'.$_SERVER['PHP_SELF'].'" onsubmit="var menu = document.getElementById(\'menuaction\'); return (menu.options[menu.selectedIndex].value == \'delete\' ? \''.addslashes(get_string('deleteattemptcheck','quiz')).'\' : true);">';
                echo '<div>';
                echo '<input type="hidden" name="id" value="'.$cm->id.'" />';
                echo '<input type="hidden" name="mode" value="overview" />';
            }

            echo "<table cellpadding=\"5\" cellspacing=\"10\" class=\"results names\">";
            echo "<tr>";
            $count = 0;
            $columncount = array(); // number of votes in each column
            foreach ($useranswer as $optionid => $userlist) {
                $columncount[$optionid] = 0; // init counters
                if ($optionid) {
                    echo "<th class=\"col$count header\" style=\"width:$tablewidth%\" scope=\"col\">";
                } else if ($choice->showunanswered) {
                    echo "<th class=\"col$count header\" style=\"width:$tablewidth%\" scope=\"col\">";
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
                    echo "<td class=\"col$count data\" style=\"width:$tablewidth%;\">";
                } else if ($choice->showunanswered) {
                    echo "<td class=\"col$count data\" style=\"width:$tablewidth%;\">";
                } else {
                    continue;
                }

                // added empty row so that when the next iteration is empty,
                // we do not get <table></table> erro from w3c validator
                // MDL-7861
                echo "<table class=\"choiceresponse\"><tr><td></td></tr>";
                foreach ($userlist as $user) {
                    if ($optionid!=0 or has_capability('mod/choice:choose', $context, $user->id, false)) {
                        $columncount[$optionid] += 1;
                        echo "<tr>";
                        if (has_capability('mod/choice:readresponses', $context) && $optionid!=0) {
                            echo '<td class="attemptcell"><input type="checkbox" name="attemptid[]" value="'. $answers[$user->id]->id. '" /></td>';
                        }
                        echo "<td class=\"picture\">";
                        print_user_picture($user->id, $course->id, $user->picture);
                        echo "</td><td class=\"fullname\">";
                        echo "<a href=\"$CFG->wwwroot/user/view.php?id=$user->id&amp;course=$course->id\">";
                        echo fullname($user, has_capability('moodle/site:viewfullnames', $context));
                        echo "</a>";
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
                if ($choice->limitanswers && !$optionid==0) {
                    echo get_string("taken", "choice").":";
                    echo $columncount[$optionid];
                    echo "<br/>";
                    echo get_string("limit", "choice").":";
                    $choice_option = get_record("choice_options", "id", $optionid);
                    echo $choice_option->maxanswers;
                }
                echo "</td>";
                $count++;
            }
            echo "</tr>";

            /// Print "Select all" etc.
            if (has_capability('mod/choice:readresponses', $context)) {
                echo '<tr><td></td><td>';
                echo '<a href="javascript:select_all_in(\'DIV\',null,\'tablecontainer\');">'.get_string('selectall', 'quiz').'</a> / ';
                echo '<a href="javascript:deselect_all_in(\'DIV\',null,\'tablecontainer\');">'.get_string('selectnone', 'quiz').'</a> ';
                echo '&nbsp;&nbsp;';
                $options = array('delete' => get_string('delete'));
                echo choose_from_menu($options, 'action', '', get_string('withselected', 'quiz'), 'if(this.selectedIndex > 0) submitFormById(\'attemptsform\');', '', true);
                echo '<noscript id="noscriptmenuaction" style="display: inline;">';
                echo '<div>';
                echo '<input type="submit" value="'.get_string('go').'" /></div></noscript>';
                echo '<script type="text/javascript">'."\n<!--\n".'document.getElementById("noscriptmenuaction").style.display = "none";'."\n-->\n".'</script>';
                echo '</td><td></td></tr>';
            }

            echo "</table>";
            if (has_capability('mod/choice:readresponses', $context)) {
                echo "</div></form></div>";
            }
            break;


        case CHOICE_PUBLISH_ANONYMOUS:

            $tablewidth = (int) (100.0 / count($useranswer));

            echo "<table cellpadding=\"5\" cellspacing=\"0\" class=\"results anonymous\">";
            echo "<tr>";
            $count = 0;
            foreach ($useranswer as $optionid => $userlist) {
                if ($optionid) {
                    echo "<th style=\"width:$tablewidth%\" class=\"col$count header\" scope=\"col\">";
                } else if ($choice->showunanswered) {
                    echo "<th style=\"width:$tablewidth%\" class=\"col$count header\" scope=\"col\">";
                } else {
                    continue;
                }
                echo format_string(choice_get_option_text($choice, $optionid));
                echo "</th>";
                $count++;
            }
            echo "</tr>";

            $maxcolumn = 0;
            foreach ($useranswer as $optionid => $userlist) {
                if (!$optionid and !$choice->showunanswered) {
                    continue;
                }
                $column[$optionid] = 0;
                foreach ($userlist as $user) {
                    if ($optionid!=0 or has_capability('mod/choice:choose', $context, $user->id, false)) {
                         $column[$optionid]++;
                    }
                }
                if ($column[$optionid] > $maxcolumn) {
                    $maxcolumn = $column[$optionid];
                }
            }

            echo "<tr>";
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
                    echo "<br/>";
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


function choice_delete_responses($attemptids) {

    if(!is_array($attemptids) || empty($attemptids)) {
        return false;
    }

    foreach($attemptids as $num => $attemptid) {
        if(empty($attemptid)) {
            unset($attemptids[$num]);
        }
    }

    foreach($attemptids as $attemptid) {
        if ($todelete = get_record('choice_answers', 'id', $attemptid)) {
             delete_records('choice_answers', 'id', $attemptid);
        }
    }
    return true;
}


function choice_delete_instance($id) {
// Given an ID of an instance of this module,
// this function will permanently delete the instance
// and any data that depends on it.

    if (! $choice = get_record("choice", "id", "$id")) {
        return false;
    }

    $result = true;

    if (! delete_records("choice_answers", "choiceid", "$choice->id")) {
        $result = false;
    }

    if (! delete_records("choice_options", "choiceid", "$choice->id")) {
        $result = false;
    }

    if (! delete_records("choice", "id", "$choice->id")) {
        $result = false;
    }

    return $result;
}

function choice_get_participants($choiceid) {
//Returns the users with data in one choice
//(users with records in choice_responses, students)

    global $CFG;

    //Get students
    $students = get_records_sql("SELECT DISTINCT u.id, u.id
                                 FROM {$CFG->prefix}user u,
                                      {$CFG->prefix}choice_answers a
                                 WHERE a.choiceid = '$choiceid' and
                                       u.id = a.userid");

    //Return students array (it contains an array of unique users)
    return ($students);
}


function choice_get_option_text($choice, $id) {
// Returns text string which is the answer that matches the id
    if ($result = get_record("choice_options", "id", $id)) {
        return $result->text;
    } else {
        return get_string("notanswered", "choice");
    }
}

function choice_get_choice($choiceid) {
// Gets a full choice record

    if ($choice = get_record("choice", "id", $choiceid)) {
        if ($options = get_records("choice_options", "choiceid", $choiceid, "id")) {
            foreach ($options as $option) {
                $choice->option[$option->id] = $option->text;
                $choice->maxanswers[$option->id] = $option->maxanswers;
            }
            return $choice;
        }
    }
    return false;
}

function choice_get_view_actions() {
    return array('view','view all','report');
}

function choice_get_post_actions() {
    return array('choose','choose again');
}

?>
