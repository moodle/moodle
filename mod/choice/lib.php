<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @package   mod-choice
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/** @global int $CHOICE_COLUMN_HEIGHT */
global $CHOICE_COLUMN_HEIGHT;
$CHOICE_COLUMN_HEIGHT = 300;

define('CHOICE_PUBLISH_ANONYMOUS', '0');
define('CHOICE_PUBLISH_NAMES',     '1');

define('CHOICE_SHOWRESULTS_NOT',          '0');
define('CHOICE_SHOWRESULTS_AFTER_ANSWER', '1');
define('CHOICE_SHOWRESULTS_AFTER_CLOSE',  '2');
define('CHOICE_SHOWRESULTS_ALWAYS',       '3');

define('CHOICE_DISPLAY_HORIZONTAL',  '0');
define('CHOICE_DISPLAY_VERTICAL',    '1');

/** @global array $CHOICE_PUBLISH */
global $CHOICE_PUBLISH;
$CHOICE_PUBLISH = array (CHOICE_PUBLISH_ANONYMOUS  => get_string('publishanonymous', 'choice'),
                         CHOICE_PUBLISH_NAMES      => get_string('publishnames', 'choice'));

/** @global array $CHOICE_SHOWRESULTS */
global $CHOICE_SHOWRESULTS;
$CHOICE_SHOWRESULTS = array (CHOICE_SHOWRESULTS_NOT          => get_string('publishnot', 'choice'),
                         CHOICE_SHOWRESULTS_AFTER_ANSWER => get_string('publishafteranswer', 'choice'),
                         CHOICE_SHOWRESULTS_AFTER_CLOSE  => get_string('publishafterclose', 'choice'),
                         CHOICE_SHOWRESULTS_ALWAYS       => get_string('publishalways', 'choice'));

/** @global array $CHOICE_DISPLAY */
global $CHOICE_DISPLAY;
$CHOICE_DISPLAY = array (CHOICE_DISPLAY_HORIZONTAL   => get_string('displayhorizontal', 'choice'),
                         CHOICE_DISPLAY_VERTICAL     => get_string('displayvertical','choice'));

/// Standard functions /////////////////////////////////////////////////////////

/**
 * @global object
 * @param object $course
 * @param object $user
 * @param object $mod
 * @param object $choice
 * @return object|null
 */
function choice_user_outline($course, $user, $mod, $choice) {
    global $DB;
    if ($answer = $DB->get_record('choice_answers', array('choiceid' => $choice->id, 'userid' => $user->id))) {
        $result->info = "'".format_string(choice_get_option_text($choice, $answer->optionid))."'";
        $result->time = $answer->timemodified;
        return $result;
    }
    return NULL;
}

/**
 * @global object
 * @param object $course
 * @param object $user
 * @param object $mod
 * @param object $choice
 * @return string|void
 */
function choice_user_complete($course, $user, $mod, $choice) {
    global $DB;
    if ($answer = $DB->get_record('choice_answers', array("choiceid" => $choice->id, "userid" => $user->id))) {
        $result->info = "'".format_string(choice_get_option_text($choice, $answer->optionid))."'";
        $result->time = $answer->timemodified;
        echo get_string("answered", "choice").": $result->info. ".get_string("updated", '', userdate($result->time));
    } else {
        print_string("notanswered", "choice");
    }
}

/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will create a new instance and return the id number
 * of the new instance.
 *
 * @global object
 * @param object $choice
 * @return int
 */
function choice_add_instance($choice) {
    global $DB;

    $choice->timemodified = time();

    if (empty($choice->timerestrict)) {
        $choice->timeopen = 0;
        $choice->timeclose = 0;
    }

    //insert answers
    if ($choice->id = $DB->insert_record("choice", $choice)) {
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
                $DB->insert_record("choice_options", $option);
            }
        }
    }
    return $choice->id;
}

/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will update an existing instance with new data.
 *
 * @global object
 * @param object $choice
 * @return bool
 */
function choice_update_instance($choice) {
    global $DB;

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
                $DB->update_record("choice_options", $option);
            } else { //empty old option - needs to be deleted.
                $DB->delete_records("choice_options", array("id"=>$option->id));
            }
        } else {
            if (isset($value) && $value <> '') {
                $DB->insert_record("choice_options", $option);
            }
        }
    }

    return $DB->update_record('choice', $choice);

}

/**
 * @global object
 * @param object $choice
 * @param object $user
 * @param object $cm
 * @param array $allresponses
 * @return void output is echo'd
 */
function choice_show_form($choice, $user, $cm, $allresponses) {
    global $DB;
//$cdisplay is an array of the display info for a choice $cdisplay[$optionid]->text  - text name of option.
//                                                                            ->maxanswers -maxanswers for this option
//                                                                            ->full - whether this option is full or not. 0=not full, 1=full
    $cdisplay = array();

    $aid = 0;
    $choicefull = false;
    $cdisplay = array();

    if ($choice->limitanswers) { //set choicefull to true by default if limitanswers.
        $choicefull = true;
    }

    $context = get_context_instance(CONTEXT_MODULE, $cm->id);

    foreach ($choice->option as $optionid => $text) {
        if (isset($text)) { //make sure there are no dud entries in the db with blank text values.
            $cdisplay[$aid]->optionid = $optionid;
            $cdisplay[$aid]->text = $text;
            $cdisplay[$aid]->maxanswers = $choice->maxanswers[$optionid];
            if (isset($allresponses[$optionid])) {
                $cdisplay[$aid]->countanswers = count($allresponses[$optionid]);
            } else {
                $cdisplay[$aid]->countanswers = 0;
            }
            if ($current = $DB->get_record('choice_answers', array('choiceid' => $choice->id, 'userid' => $user->id, 'optionid' => $optionid))) {
                $cdisplay[$aid]->checked = ' checked="checked" ';
            } else {
                $cdisplay[$aid]->checked = '';
            }
            if ( $choice->limitanswers &&
                ($cdisplay[$aid]->countanswers >= $cdisplay[$aid]->maxanswers) &&
                (empty($cdisplay[$aid]->checked)) ) {
                $cdisplay[$aid]->disabled = ' disabled="disabled" ';
            } else {
                $cdisplay[$aid]->disabled = '';
                if ($choice->limitanswers && ($cdisplay[$aid]->countanswers < $cdisplay[$aid]->maxanswers)) {
                    $choicefull = false; //set $choicefull to false - as the above condition hasn't been set.
                }
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
    echo "<input type=\"hidden\" name=\"sesskey\" value=\"".sesskey()."\" />";
    if (is_enrolled($context, NULL, 'mod/choice:choose')) { //only enrolled users are allowed to make a choice
        if ($choicefull) {
            print_string('choicefull', 'choice');
            echo "</br>";
        } else {
            echo "<input type=\"submit\" value=\"".get_string("savemychoice","choice")."\" />";
        }
        if ($choice->allowupdate && $aaa = $DB->get_record('choice_answers', 'choiceid', $choice->id, 'userid', $user->id)) {
            echo "<br /><a href='view.php?id=".$cm->id."&amp;action=delchoice&amp;sesskey=".sesskey()."'>".get_string("removemychoice","choice")."</a>";
        }
    } else {
        print_string('havetologin', 'choice');
    }
    echo "</div>";
}

/**
 * @global object
 * @param int $formanswer
 * @param object $choice
 * @param int $userid
 * @param int $courseid
 * @param object $cm
 */
function choice_user_submit_response($formanswer, $choice, $userid, $courseid, $cm) {
    global $DB;
    $current = $DB->get_record('choice_answers', array('choiceid' => $choice->id, 'userid' => $userid));
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);

    $countanswers=0;
    if($choice->limitanswers) {
        // Find out whether groups are being used and enabled
        if (groups_get_activity_groupmode($cm) > 0) {
            $currentgroup = groups_get_activity_group($cm);
        } else {
            $currentgroup = 0;
        }
        if($currentgroup) {
            // If groups are being used, retrieve responses only for users in
            // current group
            global $CFG;
            $answers = $DB->get_records_sql("
SELECT
    ca.*
FROM
    {choice_answers} ca
    INNER JOIN {groups_members} gm ON ca.userid=gm.userid
WHERE
    optionid=?
    AND gm.groupid=?", array($formanswer, $currentgroup));
        } else {
            // Groups are not used, retrieve all answers for this option ID
            $answers = $DB->get_records("choice_answers", array("optionid" => $formanswer));
        }

        if ($answers) {
            foreach ($answers as $a) { //only return enrolled users.
                if (is_enrolled($context, $a->userid, 'mod/choice:choose')) {
                    $countanswers++;
                }
            }
        }
        $maxans = $choice->maxanswers[$formanswer];
    }

    if (!($choice->limitanswers && ($countanswers >= $maxans) )) {
        if ($current) {

            $newanswer = $current;
            $newanswer->optionid = $formanswer;
            $newanswer->timemodified = time();
            $DB->update_record("choice_answers", $newanswer);
            add_to_log($courseid, "choice", "choose again", "view.php?id=$cm->id", $choice->id, $cm->id);
        } else {
            $newanswer = NULL;
            $newanswer->choiceid = $choice->id;
            $newanswer->userid = $userid;
            $newanswer->optionid = $formanswer;
            $newanswer->timemodified = time();
            $DB->insert_record("choice_answers", $newanswer);
            add_to_log($courseid, "choice", "choose", "view.php?id=$cm->id", $choice->id, $cm->id);
        }
    } else {
        if (!($current->optionid==$formanswer)) { //check to see if current choice already selected - if not display error
            print_error('choicefull', 'choice');
        }
    }
}

/**
 * @param array $user
 * @param object $cm
 * @return void Output is echo'd
 */
function choice_show_reportlink($user, $cm) {
    $responsecount =0;
    foreach($user as $optionid => $userlist) {
        if ($optionid) {
            $responsecount += count($userlist);
        }
    }

    echo '<div class="reportlink">';
    echo "<a href=\"report.php?id=$cm->id\">".get_string("viewallresponses", "choice", $responsecount)."</a>";
    echo '</div>';
}

/**
 * @global object
 * @global int
 * @global string
 * @global object
 * @uses CONTEXT_MODULE
 * @uses CHOICE_PUBLISH_NAMES
 * @uses CHOICE_PUBLISH_ANONYMOUS
 * @param object $choice
 * @param object $course
 * @param object $cm
 * @param array $allresponses
 * @param int $forcepublish
 * @return void Output is echo'd
 */
function choice_show_results($choice, $course, $cm, $allresponses, $forcepublish='') {
    global $CFG, $CHOICE_COLUMN_HEIGHT, $FULLSCRIPT, $PAGE, $OUTPUT, $DB;

    echo $OUTPUT->heading(get_string("responses", "choice"));
    if (empty($forcepublish)) { //alow the publish setting to be overridden
        $forcepublish = $choice->publish;
    }

    if (empty($allresponses)) {
        echo $OUTPUT->heading(get_string("nousersyet"));
        return false;
    }

    $totalresponsecount = 0;
    foreach ($allresponses as $optionid => $userlist) {
        if ($choice->showunanswered || $optionid) {
            $totalresponsecount += count($userlist);
        }
    }

    $context = get_context_instance(CONTEXT_MODULE, $cm->id);

    $hascapfullnames = has_capability('moodle/site:viewfullnames', $context);

    $viewresponses = has_capability('mod/choice:readresponses', $context);
    switch ($forcepublish) {
        case CHOICE_PUBLISH_NAMES:
            echo '<div id="tablecontainer">';
            if ($viewresponses) {
                echo '<form id="attemptsform" method="post" action="'.$FULLSCRIPT.'" onsubmit="var menu = document.getElementById(\'menuaction\'); return (menu.options[menu.selectedIndex].value == \'delete\' ? \''.addslashes_js(get_string('deleteattemptcheck','quiz')).'\' : true);">';
                echo '<div>';
                echo '<input type="hidden" name="id" value="'.$cm->id.'" />';
                echo '<input type="hidden" name="sesskey" value="'.sesskey().'" />';
                echo '<input type="hidden" name="mode" value="overview" />';
            }

            echo "<table cellpadding=\"5\" cellspacing=\"10\" class=\"results names\">";
            echo "<tr>";

            $columncount = array(); // number of votes in each column
            if ($choice->showunanswered) {
                $columncount[0] = 0;
                echo "<th class=\"col0 header\" scope=\"col\">";
                print_string('notanswered', 'choice');
                echo "</th>";
            }
            $count = 1;
            foreach ($choice->option as $optionid => $optiontext) {
                $columncount[$optionid] = 0; // init counters
                echo "<th class=\"col$count header\" scope=\"col\">";
                echo format_string($optiontext);
                echo "</th>";
                $count++;
            }
            echo "</tr><tr>";

            if ($choice->showunanswered) {
                echo "<td class=\"col$count data\" >";
                // added empty row so that when the next iteration is empty,
                // we do not get <table></table> erro from w3c validator
                // MDL-7861
                echo "<table class=\"choiceresponse\"><tr><td></td></tr>";
                if (!empty($allresponses[0])) {
                    foreach ($allresponses[0] as $user) {
                        echo "<tr>";
                        echo "<td class=\"picture\">";
                        echo $OUTPUT->user_picture($user, array('courseid'=>$course->id));
                        echo "</td><td class=\"fullname\">";
                        echo "<a href=\"$CFG->wwwroot/user/view.php?id=$user->id&amp;course=$course->id\">";
                        echo fullname($user, $hascapfullnames);
                        echo "</a>";
                        echo "</td></tr>";
                    }
                }
                echo "</table></td>";
            }
            $count = 0;
            foreach ($choice->option as $optionid => $optiontext) {
                    echo '<td class="col'.$count.' data" >';

                    // added empty row so that when the next iteration is empty,
                    // we do not get <table></table> erro from w3c validator
                    // MDL-7861
                    echo '<table class="choiceresponse"><tr><td></td></tr>';
                    if (isset($allresponses[$optionid])) {
                        foreach ($allresponses[$optionid] as $user) {
                            $columncount[$optionid] += 1;
                            echo '<tr><td class="attemptcell">';
                            if ($viewresponses and has_capability('mod/choice:deleteresponses',$context)) {
                                echo '<input type="checkbox" name="attemptid[]" value="'. $user->id. '" />';
                            }
                            echo '</td><td class="picture">';
                            echo $OUTPUT->user_picture($user, array('courseid'=>$course->id));
                            echo '</td><td class="fullname">';
                            echo "<a href=\"$CFG->wwwroot/user/view.php?id=$user->id&amp;course=$course->id\">";
                            echo fullname($user, $hascapfullnames);
                            echo '</a>';
                            echo '</td></tr>';
                       }
                    }
                    $count++;
                    echo '</table></td>';
            }
            echo "</tr><tr>";
            $count = 0;

            if ($choice->showunanswered) {
                echo "<td></td>";
            }

            foreach ($choice->option as $optionid => $optiontext) {
                echo "<td align=\"center\" class=\"count\">";
                if ($choice->limitanswers) {
                    echo get_string("taken", "choice").":";
                    echo $columncount[$optionid];
                    echo "<br/>";
                    echo get_string("limit", "choice").":";
                    $choice_option = $DB->get_record("choice_options", array("id" => $optionid));
                    echo $choice_option->maxanswers;
                } else {
                    if (isset($columncount[$optionid])) {
                        echo $columncount[$optionid];
                    }
                }
                echo "</td>";
                $count++;
            }
            echo "</tr>";

            /// Print "Select all" etc.
            if ($viewresponses and has_capability('mod/choice:deleteresponses',$context)) {
                echo '<tr><td></td><td>';
                echo '<a href="javascript:select_all_in(\'DIV\',null,\'tablecontainer\');">'.get_string('selectall', 'quiz').'</a> / ';
                echo '<a href="javascript:deselect_all_in(\'DIV\',null,\'tablecontainer\');">'.get_string('selectnone', 'quiz').'</a> ';
                echo '&nbsp;&nbsp;';
                echo html_writer::tag('label', get_string('withselected', 'quiz'), array('for'=>'menuaction')); 
                echo html_writer::select(array('delete' => get_string('delete')), 'action', '', array(''=>get_string('moveselectedcoursesto')), array('id'=>'menuaction'));
                $PAGE->requires->js_init_call('M.util.init_select_autosubmit', array('attemptsform', 'menuaction', ''));
                echo '<noscript id="noscriptmenuaction" style="display:inline">';
                echo '<div>';
                echo '<input type="submit" value="'.get_string('go').'" /></div></noscript>';
                echo '</td><td></td></tr>';
            }

            echo "</table></div>";
            if ($viewresponses) {
                echo "</form></div>";
            }
            break;


        case CHOICE_PUBLISH_ANONYMOUS:

            echo "<table cellpadding=\"5\" cellspacing=\"0\" class=\"results anonymous\">";
            echo "<tr>";
            $maxcolumn = 0;
            if ($choice->showunanswered) {
                echo "<th  class=\"col0 header\" scope=\"col\">";
                print_string('notanswered', 'choice');
                echo "</th>";
                $column[0] = 0;
                foreach ($allresponses[0] as $user) {
                    $column[0]++;
                }
                $maxcolumn = $column[0];
            }
            $count = 1;

            foreach ($choice->option as $optionid => $optiontext) {
                echo "<th class=\"col$count header\" scope=\"col\">";
                echo format_string($optiontext);
                echo "</th>";

                $column[$optionid] = 0;
                if (isset($allresponses[$optionid])) {
                    $column[$optionid] = count($allresponses[$optionid]);
                    if ($column[$optionid] > $maxcolumn) {
                        $maxcolumn = $column[$optionid];
                    }
                } else {
                    $column[$optionid] = 0;
                }
            }
            echo "</tr><tr>";

            $height = 0;

            if ($choice->showunanswered) {
                if ($maxcolumn) {
                    $height = $CHOICE_COLUMN_HEIGHT * ((float)$column[0] / (float)$maxcolumn);
                }
                echo "<td style=\"vertical-align:bottom\" align=\"center\" class=\"col0 data\">";
                echo "<img src=\"column.png\" height=\"$height\" width=\"49\" alt=\"\" />";
                echo "</td>";
            }
            $count = 1;
            foreach ($choice->option as $optionid => $optiontext) {
                if ($maxcolumn) {
                    $height = $CHOICE_COLUMN_HEIGHT * ((float)$column[$optionid] / (float)$maxcolumn);
                }
                echo "<td style=\"vertical-align:bottom\" align=\"center\" class=\"col$count data\">";
                echo "<img src=\"column.png\" height=\"$height\" width=\"49\" alt=\"\" />";
                echo "</td>";
                $count++;
            }
            echo "</tr><tr>";


            if ($choice->showunanswered) {
                echo '<td align="center" class="col0 count">';
                if (!$choice->limitanswers) {
                    echo $column[0];
                    echo '<br />('.format_float(((float)$column[0]/(float)$totalresponsecount)*100.0,1).'%)';
                }
                echo '</td>';
            }
            $count = 1;
            foreach ($choice->option as $optionid => $optiontext) {
                echo "<td align=\"center\" class=\"col$count count\">";
                if ($choice->limitanswers) {
                    echo get_string("taken", "choice").":";
                    echo $column[$optionid].'<br />';
                    echo get_string("limit", "choice").":";
                    $choice_option = $DB->get_record("choice_options", array("id" => $optionid));
                    echo $choice_option->maxanswers;
                } else {
                    echo $column[$optionid];
                    echo '<br />('.format_float(((float)$column[$optionid]/(float)$totalresponsecount)*100.0,1).'%)';
                }
                echo "</td>";
                $count++;
            }
            echo "</tr></table>";

            break;
    }
}

/**
 * @global object
 * @param array $attemptids
 * @param int $choiceid
 * @return bool
 */
function choice_delete_responses($attemptids, $choiceid) {
    global $DB;
    if(!is_array($attemptids) || empty($attemptids)) {
        return false;
    }

    foreach($attemptids as $num => $attemptid) {
        if(empty($attemptid)) {
            unset($attemptids[$num]);
        }
    }

    foreach($attemptids as $attemptid) {
        if ($todelete = $DB->get_record('choice_answers', array('choiceid' => $choiceid, 'userid' => $attemptid))) {
            $DB->delete_records('choice_answers', array('choiceid' => $choiceid, 'userid' => $attemptid));
        }
    }
    return true;
}


/**
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * and any data that depends on it.
 *
 * @global object
 * @param int $id
 * @return bool
 */
function choice_delete_instance($id) {
    global $DB;

    if (! $choice = $DB->get_record("choice", array("id"=>"$id"))) {
        return false;
    }

    $result = true;

    if (! $DB->delete_records("choice_answers", array("choiceid"=>"$choice->id"))) {
        $result = false;
    }

    if (! $DB->delete_records("choice_options", array("choiceid"=>"$choice->id"))) {
        $result = false;
    }

    if (! $DB->delete_records("choice", array("id"=>"$choice->id"))) {
        $result = false;
    }

    return $result;
}

/**
 * Returns the users with data in one choice
 * (users with records in choice_responses, students)
 *
 * @param int $choiceid
 * @return array
 */
function choice_get_participants($choiceid) {
    global $DB;

    //Get students
    $students = $DB->get_records_sql("SELECT DISTINCT u.id, u.id
                                 FROM {user} u,
                                      {choice_answers} a
                                 WHERE a.choiceid = ? AND
                                       u.id = a.userid", array($choiceid));

    //Return students array (it contains an array of unique users)
    return ($students);
}

/**
 * Returns text string which is the answer that matches the id
 *
 * @global object
 * @param object $choice
 * @param int $id
 * @return string
 */
function choice_get_option_text($choice, $id) {
    global $DB;

    if ($result = $DB->get_record("choice_options", array("id" => $id))) {
        return $result->text;
    } else {
        return get_string("notanswered", "choice");
    }
}

/**
 * Gets a full choice record
 *
 * @global object
 * @param int $choiceid
 * @return object|bool The choice or false
 */
function choice_get_choice($choiceid) {
    global $DB;

    if ($choice = $DB->get_record("choice", array("id" => $choiceid))) {
        if ($options = $DB->get_records("choice_options", array("choiceid" => $choiceid), "id")) {
            foreach ($options as $option) {
                $choice->option[$option->id] = $option->text;
                $choice->maxanswers[$option->id] = $option->maxanswers;
            }
            return $choice;
        }
    }
    return false;
}

/**
 * @return array
 */
function choice_get_view_actions() {
    return array('view','view all','report');
}

/**
 * @return array
 */
function choice_get_post_actions() {
    return array('choose','choose again');
}


/**
 * Implementation of the function for printing the form elements that control
 * whether the course reset functionality affects the choice.
 *
 * @param object $mform form passed by reference
 */
function choice_reset_course_form_definition(&$mform) {
    $mform->addElement('header', 'choiceheader', get_string('modulenameplural', 'choice'));
    $mform->addElement('advcheckbox', 'reset_choice', get_string('removeresponses','choice'));
}

/**
 * Course reset form defaults.
 *
 * @return array
 */
function choice_reset_course_form_defaults($course) {
    return array('reset_choice'=>1);
}

/**
 * Actual implementation of the rest coures functionality, delete all the
 * choice responses for course $data->courseid.
 *
 * @global object
 * @global object
 * @param object $data the data submitted from the reset course.
 * @return array status array
 */
function choice_reset_userdata($data) {
    global $CFG, $DB;

    $componentstr = get_string('modulenameplural', 'choice');
    $status = array();

    if (!empty($data->reset_choice)) {
        $choicessql = "SELECT ch.id
                         FROM {choice} ch
                        WHERE ch.course=?";

        $DB->delete_records_select('choice_answers', "choiceid IN ($choicessql)", array($data->courseid));
        $status[] = array('component'=>$componentstr, 'item'=>get_string('removeresponses', 'choice'), 'error'=>false);
    }

    /// updating dates - shift may be negative too
    if ($data->timeshift) {
        shift_course_mod_dates('choice', array('timeopen', 'timeclose'), $data->timeshift, $data->courseid);
        $status[] = array('component'=>$componentstr, 'item'=>get_string('datechanged'), 'error'=>false);
    }

    return $status;
}

/**
 * @global object
 * @global object
 * @global object
 * @uses CONTEXT_MODULE
 * @param object $choice
 * @param object $cm
 * @param int $groupmode
 * @return array
 */
function choice_get_response_data($choice, $cm, $groupmode) {
    global $CFG, $USER, $DB;

    $context = get_context_instance(CONTEXT_MODULE, $cm->id);

/// Get the current group
    if ($groupmode > 0) {
        $currentgroup = groups_get_activity_group($cm);
    } else {
        $currentgroup = 0;
    }

/// Initialise the returned array, which is a matrix:  $allresponses[responseid][userid] = responseobject
    $allresponses = array();

/// First get all the users who have access here
/// To start with we assume they are all "unanswered" then move them later
    $allresponses[0] = get_enrolled_users($context, 'mod/choice:choose', $currentgroup, 'u.id, u.picture, u.imagealt, u.firstname, u.lastname, u.idnumber', 'u.lastname ASC,u.firstname ASC');

/// Get all the recorded responses for this choice
    $rawresponses = $DB->get_records('choice_answers', array('choiceid' => $choice->id));

/// Use the responses to move users into the correct column

    if ($rawresponses) {
        foreach ($rawresponses as $response) {
            if (isset($allresponses[0][$response->userid])) {   // This person is enrolled and in correct group
                $allresponses[0][$response->userid]->timemodified = $response->timemodified;
                $allresponses[$response->optionid][$response->userid] = clone($allresponses[0][$response->userid]);
                unset($allresponses[0][$response->userid]);   // Remove from unanswered column
            }
        }
    }
    if (empty($allresponses[0])) {
        unset($allresponses[0]);
    }
    return $allresponses;
}

/**
 * Returns all other caps used in module
 *
 * @return array
 */
function choice_get_extra_capabilities() {
    return array('moodle/site:accessallgroups');
}

/**
 * @uses FEATURE_GROUPS
 * @uses FEATURE_GROUPINGS
 * @uses FEATURE_GROUPMEMBERSONLY
 * @uses FEATURE_MOD_INTRO
 * @uses FEATURE_COMPLETION_TRACKS_VIEWS
 * @uses FEATURE_GRADE_HAS_GRADE
 * @uses FEATURE_GRADE_OUTCOMES
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed True if module supports feature, null if doesn't know
 */
function choice_supports($feature) {
    switch($feature) {
        case FEATURE_GROUPS:                  return true;
        case FEATURE_GROUPINGS:               return true;
        case FEATURE_GROUPMEMBERSONLY:        return true;
        case FEATURE_MOD_INTRO:               return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS: return true;
        case FEATURE_GRADE_HAS_GRADE:         return false;
        case FEATURE_GRADE_OUTCOMES:          return false;

        default: return null;
    }
}

/**
 * Adds module specific settings to the settings block
 *
 * @param settings_navigation $settings The settings navigation object
 * @param navigation_node $choicenode The node to add module settings to
 */
function choice_extend_settings_navigation(settings_navigation $settings, navigation_node $choicenode) {
    global $PAGE;

    if (has_capability('mod/choice:readresponses', $PAGE->cm->context)) {

        $groupmode = groups_get_activity_groupmode($PAGE->cm);
        if ($groupmode) {
            groups_get_activity_group($PAGE->cm, true);
        }
        // We only actually need the choice id here
        $choice = new stdClass;
        $choice->id = $PAGE->cm->instance;
        $allresponses = choice_get_response_data($choice, $PAGE->cm, $groupmode);   // Big function, approx 6 SQL calls per user

        $responsecount =0;
        foreach($allresponses as $optionid => $userlist) {
            if ($optionid) {
                $responsecount += count($userlist);
            }
        }
        $choicenode->add(get_string("viewallresponses", "choice", $responsecount), new moodle_url('/mod/choice/report.php', array('id'=>$PAGE->cm->id)));
    }
}