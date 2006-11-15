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

    if (!empty($choice->timerestrict) and $choice->timerestrict) {
        $choice->timeopen = make_timestamp($choice->openyear, $choice->openmonth, $choice->openday,
                                     $choice->openhour, $choice->openminute, 0);
        $choice->timeclose = make_timestamp($choice->closeyear, $choice->closemonth, $choice->closeday,
                                      $choice->closehour, $choice->closeminute, 0);
    } else {
        $choice->timeopen = 0;
        $choice->timeclose = 0;
    }

    //insert answers    
    if ($choice->id = insert_record("choice", $choice)) {
        foreach ($choice as $name => $value) {        
            if (strstr($name, "newoption")) {   /// New option
                $value = trim($value);
                if (isset($value) && $value <> '') {
                    $option = NULL;
                    $option->text = $value;
                    $option->choiceid = $choice->id;
                    if (isset($choice->{'newlimit'.substr($name, 9)})) {
                        $option->maxanswers = $choice->{'newlimit'.substr($name, 9)};
                    }
                    $option->timemodified = time();
                    insert_record("choice_options", $option);                
                }
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


    if (!empty($choice->timerestrict) and $choice->timerestrict) {
        $choice->timeopen = make_timestamp($choice->openyear, $choice->openmonth, $choice->openday,
                                     $choice->openhour, $choice->openminute, 0);
        $choice->timeclose = make_timestamp($choice->closeyear, $choice->closemonth, $choice->closeday,
                                      $choice->closehour, $choice->closeminute, 0);
    } else {
        $choice->timeopen = 0;
        $choice->timeclose = 0;
    }
    
    //update answers
    
    foreach ($choice as $name => $value) {        
        $value = trim($value);

        if (strstr($name, "oldoption")) {  // Old option
            if (isset($value) && $value <> '') {
                $option = NULL;
                $option->id = substr($name, 9); // Get the ID of the answer that needs to be updated.
                $option->text = $value;
                $option->choiceid = $choice->id;
                if (isset($choice->{'oldlimit'.substr($name, 9)})) {
                    $option->maxanswers = $choice->{'oldlimit'.substr($name, 9)};
                }
                $option->timemodified = time();
                update_record("choice_options", $option);
            } else { //empty old option - needs to be deleted.
                delete_records("choice_options", "id", substr($name, 9));
            }
        } else if (strstr($name, "newoption")) {   /// New option
            if (isset($value)&& $value <> '') {
                $option = NULL;
                $option->text = $value;
                $option->choiceid = $choice->id;
                if (isset($choice->{'newlimit'.substr($name, 9)})) {
                    $option->maxanswers = $choice->{'newlimit'.substr($name, 9)};
                }
                $option->timemodified = time();
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
            echo "<table cellpadding=\"20\" cellspacing=\"20\" align=\"center\"><tr>";
                                    
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
            echo "<table cellpadding=\"10\" cellspacing=\"10\" align=\"center\">";
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
    echo "<center>";
    echo "<input type=\"hidden\" name=\"id\" value=\"$cm->id\" />";
    if (!isguest()) { //don't show save button if the logged in user is the guest user.
        echo "<input type=\"submit\" value=\"".get_string("savemychoice","choice")."\" />";
    } else {
        print_string('havetologin', 'choice');
    }
    echo "</center>";
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


function choice_show_reportlink($choice, $courseid, $cmid) {
    $context = get_context_instance(CONTEXT_MODULE, $cmid);
    if ( $allanswers = get_records("choice_answers", "choiceid", $choice->id)) {
        $responsecount = 0;
        foreach ($allanswers as $aa) {
            if (has_capability('mod/choice:readresponses', $context)) {
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
                
        /// Check to see if groups are being used in this choice
    if ($groupmode = groupmode($course, $cm)) {   // Groups are being used
        $currentgroup = setup_and_print_groups($course, $groupmode, $_SERVER['PHP_SELF']."?id=$cm->id");
    } else {
        $currentgroup = false;
    }

    if ($currentgroup) {
        $users = get_group_users($currentgroup, "u.firstname ASC", '', 'u.id, u.picture, u.firstname, u.lastname, u.idnumber');
    } else {
        $users = get_users_by_capability($context, 'mod/choice:choose', 'u.id, u.picture, u.firstname, u.lastname, u.idnumber', 'u.firstname ASC');
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
                echo '<input type="hidden" name="id" value="'.$cm->id.'" />';
                echo '<input type="hidden" name="mode" value="overview" />';
            }
            
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
                    // this needs to be fixed
                    // hide admin/editting teacher (users with editting privilages)
                    // show users without? I could be wrong.
                    if (!($optionid==0 && has_capability('mod/choice:readresponses', $context, $user->id))) { // make sure admins and hidden teachers are not shown in not answered yet column.
                        echo "<tr>";
                        if (has_capability('mod/choice:readresponses', $context) && $optionid!=0) {
                            echo '<td width=\"5\" nowrap=\"nowrap\"><input type="checkbox" name="attemptid[]" value="'. $answers[$user->id]->id. '" /></td>';
                        }
                        echo "<td width=\"10\" nowrap=\"nowrap\" class=\"picture\">";
                        print_user_picture($user->id, $course->id, $user->picture);
                        echo "</td><td width=\"100%\" nowrap=\"nowrap\" class=\"fullname\">";
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
                $countanswers = get_records("choice_answers", "optionid", $optionid);                
                $countans = 0;  
                if (!empty($countanswers)) {              
                    foreach ($countanswers as $ca) { //only return enrolled users.                            
                        if (has_capability('mod/choice:choose', get_context_instance(CONTEXT_MODULE, $cm->id))) {                        
                           $countans = $countans+1;
                        }                   
                    }
                }
                if ($choice->limitanswers && !$optionid==0) {
                    echo get_string("taken", "choice").":";
                    echo $countans;
                    echo "<br>";
                    echo get_string("limit", "choice").":";
                    $choice_option = get_record("choice_options", "id", $optionid);
                    echo $choice_option->maxanswers;
                }
                echo "</td>";
                $count++;
            }
            
            /// Print "Select all" etc.
            if (has_capability('mod/choice:readresponses', $context)) {
                echo '<tr><td><p>';
                echo '<tr><td>';
                echo '<a href="javascript:select_all_in(\'DIV\',null,\'tablecontainer\');">'.get_string('selectall', 'quiz').'</a> / ';
                echo '<a href="javascript:deselect_all_in(\'DIV\',null,\'tablecontainer\');">'.get_string('selectnone', 'quiz').'</a> ';
                echo '&nbsp;&nbsp;';
                $options = array('delete' => get_string('delete'));
                echo choose_from_menu($options, 'action', '', get_string('withselected', 'quiz'), 'if(this.selectedIndex > 0) submitFormById(\'attemptsform\');', '', true);
                echo '<noscript id="noscriptmenuaction" style="display: inline;">';
                echo '<input type="submit" value="'.get_string('go').'" /></noscript>';
                echo '<script type="text/javascript">'."\n<!--\n".'document.getElementById("noscriptmenuaction").style.display = "none";'."\n-->\n".'</script>';
                echo '</p></td></tr>';
            }
                   
            echo "</tr></table>";
            if (has_capability('mod/choice:readresponses', $context)) {
                echo "</form></div>";
            }
            break;


        case CHOICE_PUBLISH_ANONYMOUS:
          
            $tablewidth = (int) (100.0 / count($useranswer));

            echo "<table cellpadding=\"5\" cellspacing=\"0\" align=\"center\" class=\"results anonymous\">";
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
                    if (!($optionid==0 && has_capability('mod/choice:readresponses', $context, $user->id))) { //make sure admins and hidden teachers are not shown in not answered yet column.
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
