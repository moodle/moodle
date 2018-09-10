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
 * Version information
 *
 * @package    mod
 * @subpackage choicegroup
 * @copyright  2013 Université de Lausanne
 * @author     Nicolas Dunand <Nicolas.Dunand@unil.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/** @global int $CHOICEGROUP_COLUMN_HEIGHT */
global $CHOICEGROUP_COLUMN_HEIGHT;
$CHOICEGROUP_COLUMN_HEIGHT = 300;

/** @global int $CHOICEGROUP_COLUMN_WIDTH */
global $CHOICEGROUP_COLUMN_WIDTH;
$CHOICEGROUP_COLUMN_WIDTH = 300;

define('CHOICEGROUP_PUBLISH_ANONYMOUS', '0');
define('CHOICEGROUP_PUBLISH_NAMES',     '1');
define('CHOICEGROUP_PUBLISH_DEFAULT',   '1');

define('CHOICEGROUP_SHOWRESULTS_NOT',          '0');
define('CHOICEGROUP_SHOWRESULTS_AFTER_ANSWER', '1');
define('CHOICEGROUP_SHOWRESULTS_AFTER_CLOSE',  '2');
define('CHOICEGROUP_SHOWRESULTS_ALWAYS',       '3');
define('CHOICEGROUP_SHOWRESULTS_DEFAULT',      '3');

define('CHOICEGROUP_DISPLAY_HORIZONTAL',  '0');
define('CHOICEGROUP_DISPLAY_VERTICAL',    '1');

define('CHOICEGROUP_SORTGROUPS_SYSTEMDEFAULT',    '0');
define('CHOICEGROUP_SORTGROUPS_CREATEDATE',    '1');
define('CHOICEGROUP_SORTGROUPS_NAME',    '2');

/** @global array $CHOICEGROUP_PUBLISH */
global $CHOICEGROUP_PUBLISH;
$CHOICEGROUP_PUBLISH = array (CHOICEGROUP_PUBLISH_ANONYMOUS  => get_string('publishanonymous', 'choicegroup'),
                         CHOICEGROUP_PUBLISH_NAMES      => get_string('publishnames', 'choicegroup'));

/** @global array $CHOICEGROUP_SHOWRESULTS */
global $CHOICEGROUP_SHOWRESULTS;
$CHOICEGROUP_SHOWRESULTS = array (CHOICEGROUP_SHOWRESULTS_NOT          => get_string('publishnot', 'choicegroup'),
                         CHOICEGROUP_SHOWRESULTS_AFTER_ANSWER => get_string('publishafteranswer', 'choicegroup'),
                         CHOICEGROUP_SHOWRESULTS_AFTER_CLOSE  => get_string('publishafterclose', 'choicegroup'),
                         CHOICEGROUP_SHOWRESULTS_ALWAYS       => get_string('publishalways', 'choicegroup'));

/** @global array $CHOICEGROUP_DISPLAY */
global $CHOICEGROUP_DISPLAY;
$CHOICEGROUP_DISPLAY = array (CHOICEGROUP_DISPLAY_HORIZONTAL   => get_string('displayhorizontal', 'choicegroup'),
                         CHOICEGROUP_DISPLAY_VERTICAL     => get_string('displayvertical','choicegroup'));

require_once($CFG->dirroot.'/group/lib.php');

/// Standard functions /////////////////////////////////////////////////////////

/**
 * @global object
 * @param object $course
 * @param object $user
 * @param object $mod
 * @param object $choicegroup
 * @return object|null
 */
function choicegroup_user_outline($course, $user, $mod, $choicegroup) {
    if ($groupmembership = choicegroup_get_user_answer($choicegroup, $user)) { // if user has answered
        $result = new stdClass();
        $result->info = "'".format_string($groupmembership->name)."'";
        $result->time = $groupmembership->timeuseradded;
        return $result;
    }
    return NULL;
}

/**
 *
 */
function choicegroup_get_user_answer($choicegroup, $user, $returnArray = FALSE, $refresh = FALSE) {
    global $DB, $choicegroup_groups;

    static $user_answers = array();

    if (is_numeric($user)) {
        $userid = $user;
    }
    else {
        $userid = $user->id;
    }

    if (!$refresh and isset($user_answers[$userid])) {
        if ($returnArray === TRUE) {
            return $user_answers[$userid];
        } else {
            return $user_answers[$userid][0];
        }
    } else {
        $user_answers = array();
    }
    if(!is_array($choicegroup_groups) || !count($choicegroup_groups)){
            $choicegroup_groups = choicegroup_get_groups($choicegroup);

    }

    $groupids = array();
    foreach ($choicegroup_groups as $group) {
        if (is_numeric($group->id)) {
            $groupids[] = $group->id;
        }
    }
    if ($groupids) {
        $params1 = array($userid);
        list($insql, $params2) = $DB->get_in_or_equal($groupids);
        $params = array_merge($params1, $params2);
        $groupmemberships = $DB->get_records_sql('SELECT * FROM {groups_members} WHERE userid = ? AND groupid '.$insql, $params);
        $groups = array();
        foreach ($groupmemberships as $groupmembership) {
            $group = $choicegroup_groups[$groupmembership->groupid];
            $group->timeuseradded = $groupmembership->timeadded;
            $groups[] = $group;
        }
        if (count($groups) > 0) {
            $user_answers[$userid] = $groups;
            if ($returnArray === TRUE) {
                return $groups;
            } else {
                return $groups[0];
            }
        }
    }
    return false;

}

/**
 * @global object
 * @param object $course
 * @param object $user
 * @param object $mod
 * @param object $choicegroup
 * @return string|void
 */
function choicegroup_user_complete($course, $user, $mod, $choicegroup) {
    if ($groupmembership = choicegroup_get_user_answer($choicegroup, $user)) { // if user has answered
        $result = new stdClass();
        $result->info = "'".format_string($groupmembership->name)."'";
        $result->time = $groupmembership->timeuseradded;
        echo get_string("answered", "choicegroup").": $result->info. ".get_string("updated", '', userdate($result->time));
    } else {
        print_string("notanswered", "choicegroup");
    }
}

/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will create a new instance and return the id number
 * of the new instance.
 *
 * @global object
 * @param object $choicegroup
 * @return int
 */
function choicegroup_add_instance($choicegroup) {
    global $DB;

    $choicegroup->timemodified = time();

    if (empty($choicegroup->timerestrict)) {
        $choicegroup->timeopen = 0;
        $choicegroup->timeclose = 0;
    }

    //insert answers
    $choicegroup->id = $DB->insert_record("choicegroup", $choicegroup);
    
    // deserialize the selected groups
    
    $groupIDs = explode(';', $choicegroup->serializedselectedgroups);
    $groupIDs = array_diff( $groupIDs, array( '' ) );
    
    foreach ($groupIDs as $groupID) {
        $groupID = trim($groupID);
        if (isset($groupID) && $groupID != '') {
            $option = new stdClass();
            $option->groupid = $groupID;
            $option->choicegroupid = $choicegroup->id;
            $property = 'group_' . $groupID . '_limit';
            if (isset($choicegroup->$property)) {
            	$option->maxanswers = $choicegroup->$property;
            }
            $option->timemodified = time();
            $DB->insert_record("choicegroup_options", $option);
        }	
    }

    if (class_exists('\core_completion\api')) {
        $completiontimeexpected = !empty($choicegroup->completionexpected) ? $choicegroup->completionexpected : null;
        \core_completion\api::update_completion_date_event($choicegroup->coursemodule, 'choicegroup', $choicegroup->id, $completiontimeexpected);
    }


    return $choicegroup->id;
}

/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will update an existing instance with new data.
 *
 * @global object
 * @param object $choicegroup
 * @return bool
 */
function choicegroup_update_instance($choicegroup) {
    global $DB;

    $choicegroup->id = $choicegroup->instance;
    $choicegroup->timemodified = time();


    if (empty($choicegroup->timerestrict)) {
        $choicegroup->timeopen = 0;
        $choicegroup->timeclose = 0;
    }

    if (empty($choicegroup->multipleenrollmentspossible)) {
        $choicegroup->multipleenrollmentspossible = 0;
    }
    
    
    // deserialize the selected groups
    
    $groupIDs = explode(';', $choicegroup->serializedselectedgroups);
    $groupIDs = array_diff( $groupIDs, array( '' ) );

    // prepare pre-existing selected groups from database
    
    if (!($preExistingGroups = $DB->get_records("choicegroup_options", array("choicegroupid" => $choicegroup->id), "id"))) {
    	return false;
    }

    // walk through form-selected groups
    foreach ($groupIDs as $groupID) {
    	$groupID = trim($groupID);
    	if (isset($groupID) && $groupID != '') {
    		$option = new stdClass();
    		$option->groupid = $groupID;
    		$option->choicegroupid = $choicegroup->id;
    		$property = 'group_' . $groupID . '_limit';
    		if (isset($choicegroup->$property)) {
    			$option->maxanswers = $choicegroup->$property;
    		}
    		$option->timemodified = time();
    		// Find out if this selection already exists
    		foreach ($preExistingGroups as $key => $preExistingGroup) {
    			if ($option->groupid == $preExistingGroup->groupid) {
    				// match found, so instead of creating a new record we should merely update a pre-existing record
    				$option->id = $preExistingGroup->id;
    				$DB->update_record("choicegroup_options", $option);
    				// remove the element from the array to not deal with it later
    				unset($preExistingGroups[$key]);
    				continue 2; // continue the big loop
    			}
    		}
    		$DB->insert_record("choicegroup_options", $option);	
    	}
    	 
    }
    // remove all remaining pre-existing groups which did not appear in the form (and are thus assumed to have been deleted)
    foreach ($preExistingGroups as $preExistingGroup) {
    	$DB->delete_records("choicegroup_options", array("id"=>$preExistingGroup->id));
    }

    if (class_exists('\core_completion\api')) {
        $completiontimeexpected = !empty($choicegroup->completionexpected) ? $choicegroup->completionexpected : null;
        \core_completion\api::update_completion_date_event($choicegroup->coursemodule, 'choicegroup', $choicegroup->id, $completiontimeexpected);
    }


    return $DB->update_record('choicegroup', $choicegroup);

}

/**
 * @global object
 * @param object $choicegroup
 * @param object $user
 * @param object $coursemodule
 * @param array $allresponses
 * @return array
 */
function choicegroup_prepare_options($choicegroup, $user, $coursemodule, $allresponses) {

    $cdisplay = array('options'=>array());

    $cdisplay['limitanswers'] = true;
    $context = context_module::instance($coursemodule->id);
    $answers = choicegroup_get_user_answer($choicegroup, $user, TRUE, true);

    foreach ($choicegroup->option as $optionid => $text) {
        if (isset($text)) { //make sure there are no dud entries in the db with blank text values.
            $option = new stdClass;
            $option->attributes = new stdClass;
            $option->attributes->value = $optionid;
            $option->groupid = $text;
            $option->maxanswers = $choicegroup->maxanswers[$optionid];
            $option->displaylayout = $choicegroup->display;

            if (isset($allresponses[$text])) {
                $option->countanswers = count($allresponses[$text]);
            } else {
                $option->countanswers = 0;
            }
            if (is_array($answers)) {
                foreach($answers as $answer) {
                    if ($answer && $text == $answer->id) {
                        $option->attributes->checked = true;
                    }
                }
            }
            if ( $choicegroup->limitanswers && ($option->countanswers >= $option->maxanswers) && empty($option->attributes->checked)) {
                $option->attributes->disabled = true;
            }
            $cdisplay['options'][] = $option;
        }
    }

    $cdisplay['hascapability'] = is_enrolled($context, NULL, 'mod/choicegroup:choose'); //only enrolled users are allowed to make a choicegroup

    if ($choicegroup->allowupdate && is_array($answers)) {
        $cdisplay['allowupdate'] = true;
    }

    return $cdisplay;
}

/**
 * @global object
 * @param int $formanswer
 * @param object $choicegroup
 * @param int $userid
 * @param object $course Course object
 * @param object $cm
 */
function choicegroup_user_submit_response($formanswer, $choicegroup, $userid, $course, $cm) {
    global $DB, $CFG;
    require_once($CFG->libdir.'/completionlib.php');

    $context = context_module::instance($cm->id);
    $eventparams = array(
        'context' => $context,
        'objectid' => $choicegroup->id
    );

    $selected_option = $DB->get_record('choicegroup_options', array('id' => $formanswer));

    $current = choicegroup_get_user_answer($choicegroup, $userid);
    if ($current) {
        $currentgroup = $DB->get_record('groups', array('id' => $current->id), 'id,name', MUST_EXIST);
    }
    $selectedgroup = $DB->get_record('groups', array('id' => $selected_option->groupid), 'id,name', MUST_EXIST);

    $countanswers=0;
    groups_add_member($selected_option->groupid, $userid);
    $groupmember_added = true;    
    if ($choicegroup->limitanswers) {
        $groupmember = $DB->get_record('groups_members', array('groupid' => $selected_option->groupid, 'userid'=>$userid));
        $select_count = 'groupid='.$selected_option->groupid.' and id<='.$groupmember->id;
        $countanswers = $DB->count_records_select('groups_members', $select_count);
        $maxans = $choicegroup->maxanswers[$formanswer];
        if ($countanswers > $maxans) {    
           groups_remove_member($selected_option->groupid, $userid);
           $groupmember_added = false;
      }
    }
    if ($groupmember_added) {
        if ($current) {
            if (!($choicegroup->multipleenrollmentspossible == 1)) {
                if ($selected_option->groupid != $current->id) {
                    if (groups_is_member($current->id, $userid)) {
                        groups_remove_member($current->id, $userid);
//                        $eventparams['groupname'] = $currentgroup->name;
                        $event = \mod_choicegroup\event\choice_removed::create($eventparams);
                        $event->add_record_snapshot('course_modules', $cm);
                        $event->add_record_snapshot('course', $course);
                        $event->add_record_snapshot('choicegroup', $choicegroup);
                        $event->trigger();
                    }
                }
            }
        } else {
            // Update completion state
            $completion = new completion_info($course);
            if ($completion->is_enabled($cm) && $choicegroup->completionsubmit) {
                $completion->update_state($cm, COMPLETION_COMPLETE);
            }
//            $eventparams['groupname'] = $selectedgroup->name;
            $event = \mod_choicegroup\event\choice_updated::create($eventparams);
            $event->add_record_snapshot('course_modules', $cm);
            $event->add_record_snapshot('course', $course);
            $event->add_record_snapshot('choicegroup', $choicegroup);
            $event->trigger();
        }
    } else {
        if (!$current || !($current->id==$selected_option->groupid)) { //check to see if current choicegroup already selected - if not display error
            print_error('choicegroupfull', 'choicegroup', $CFG->wwwroot.'/mod/choicegroup/view.php?id='.$cm->id);
        }
    }
}

/**
 * @param object $choicegroup
 * @param array $allresponses
 * @param object $cm
 * @return void Output is echo'd
 */
function choicegroup_show_reportlink($choicegroup, $allresponses, $cm) {
    $responsecount = 0;
    $respondents = array();
    foreach($allresponses as $optionid => $userlist) {
        if ($optionid) {
            $responsecount += count($userlist);
            if ($choicegroup->multipleenrollmentspossible) {
                foreach ($userlist as $user) {
                    if (!in_array($user->id, $respondents)) {
                        $respondents[] = $user->id;
                    }
                }
            }
        }
    }
    echo '<div class="reportlink"><a href="report.php?id='.$cm->id.'">'.get_string("viewallresponses", "choicegroup", $responsecount);
    if ($choicegroup->multipleenrollmentspossible == 1) {
        echo ' ' . get_string("byparticipants", "choicegroup", count($respondents));
    }
    echo '</a></div>';
}

/**
 * @global object
 * @param object $choicegroup
 * @param object $course
 * @param object $coursemodule
 * @param array $allresponses

 *  * @param bool $allresponses
 * @return object
 */
function prepare_choicegroup_show_results($choicegroup, $course, $cm, $allresponses, $forcepublish=false) {
    global $CFG, $FULLSCRIPT, $PAGE, $OUTPUT;

    $display = clone($choicegroup);
    $display->coursemoduleid = $cm->id;
    $display->courseid = $course->id;
//debugging('<pre>'.print_r($choicegroup->option, true).'</pre>', DEBUG_DEVELOPER);
//debugging('<pre>'.print_r($allresponses, true).'</pre>', DEBUG_DEVELOPER);

    //overwrite options value;
    $display->options = array();
    $totaluser = 0;
    foreach ($choicegroup->option as $optionid => $groupid) {
        $display->options[$optionid] = new stdClass;
        $display->options[$optionid]->groupid = $groupid;
        $display->options[$optionid]->maxanswer = $choicegroup->maxanswers[$optionid];

        if (array_key_exists($groupid, $allresponses)) {
            $display->options[$optionid]->user = $allresponses[$groupid];
            foreach ($display->options[$optionid]->user as $user){
                $user->grpsmemberid = array_search(array($groupid, $user->id), $choicegroup->grpmemberid);
            }
            $totaluser += count($allresponses[$groupid]);
        }
    }
    if ($choicegroup->showunanswered) {
        $display->options[0]->user = $allresponses[0];
    }
    unset($display->option);
    unset($display->maxanswers);

    $display->numberofuser = $totaluser;
    $context = context_module::instance($cm->id);
    $display->viewresponsecapability = has_capability('mod/choicegroup:readresponses', $context);
    $display->deleterepsonsecapability = has_capability('mod/choicegroup:deleteresponses',$context);
    $display->fullnamecapability = has_capability('moodle/site:viewfullnames', $context);

    if (empty($allresponses)) {
        echo $OUTPUT->heading(get_string("nousersyet"));
        return false;
    }


    $totalresponsecount = 0;
    foreach ($allresponses as $optionid => $userlist) {
        if ($choicegroup->showunanswered || $optionid) {
            $totalresponsecount += count($userlist);
        }
    }

    $context = context_module::instance($cm->id);

    $hascapfullnames = has_capability('moodle/site:viewfullnames', $context);

    $viewresponses = has_capability('mod/choicegroup:readresponses', $context);
    switch ($forcepublish) {
        case CHOICEGROUP_PUBLISH_NAMES:
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
            if ($choicegroup->showunanswered) {
                $columncount[0] = 0;
                echo "<th class=\"col0 header\" scope=\"col\">";
                print_string('notanswered', 'choicegroup');
                echo "</th>";
            }
            $count = 1;
            foreach ($choicegroup->option as $optionid => $optiontext) {
                $columncount[$optionid] = 0; // init counters
                echo "<th class=\"col$count header\" scope=\"col\">";
                echo format_string($optiontext);
                echo "</th>";
                $count++;
            }
            echo "</tr><tr>";

            if ($choicegroup->showunanswered) {
                echo "<td class=\"col$count data\" >";
                // added empty row so that when the next iteration is empty,
                // we do not get <table></table> error from w3c validator
                // MDL-7861
                echo "<table class=\"choicegroupresponse\"><tr><td></td></tr>";
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
            $count = 1;
            foreach ($choicegroup->option as $optionid => $optiontext) {
                    echo '<td class="col'.$count.' data" >';

                    // added empty row so that when the next iteration is empty,
                    // we do not get <table></table> error from w3c validator
                    // MDL-7861
                    echo '<table class="choicegroupresponse"><tr><td></td></tr>';
                    if (isset($allresponses[$optionid])) {
                        foreach ($allresponses[$optionid] as $user) {
                            $columncount[$optionid] += 1;
                            echo '<tr><td class="attemptcell">';
                            if ($viewresponses and has_capability('mod/choicegroup:deleteresponses',$context)) {
                                echo '<input type="checkbox" name="userid[]" value="'. $user->id. '" />';
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
            $count = 1;

            if ($choicegroup->showunanswered) {
                echo "<td></td>";
            }

            foreach ($choicegroup->option as $optionid => $optiontext) {
                echo "<td align=\"center\" class=\"col$count count\">";
                if ($choicegroup->limitanswers) {
                    echo get_string("taken", "choicegroup").":";
                    echo $columncount[$optionid];
                    echo "<br/>";
                    echo get_string("limit", "choicegroup").":";
                    echo $choicegroup->maxanswers[$optionid];
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
            if ($viewresponses and has_capability('mod/choicegroup:deleteresponses',$context)) {
                echo '<tr><td></td><td>';
                echo '<a href="javascript:select_all_in(\'DIV\',null,\'tablecontainer\');">'.get_string('selectall').'</a> / ';
                echo '<a href="javascript:deselect_all_in(\'DIV\',null,\'tablecontainer\');">'.get_string('deselectall').'</a> ';
                echo '&nbsp;&nbsp;';
                echo html_writer::label(get_string('withselected', 'choicegroup'), 'menuaction');
                echo html_writer::select(array('delete' => get_string('delete')), 'action', '', array(''=>get_string('withselectedusers')), array('id'=>'menuaction'));
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
    }
    return $display;
}

/**
 * @global object
 * @param array $grpsmemberids
 * @param object $choicegroup Choice main table row
 * @param object $cm Course-module object
 * @param object $course Course object
 * @return bool
 */
function choicegroup_delete_responses($grpsmemberids, $choicegroup, $cm, $course) {
    global $CFG, $DB;
    require_once($CFG->libdir.'/completionlib.php');

    if(!is_array($grpsmemberids) || empty($grpsmemberids)) {
        return false;
    }

    foreach($grpsmemberids as $num => $grpsmemberid) {
        if(empty($grpsmemberid)) {
            unset($grpsmemberids[$num]);
        }
    }

    $context = context_module::instance($cm->id);
    $completion = new completion_info($course);
    $eventparams = array(
        'context' => $context,
        'objectid' => $choicegroup->id
    );

    foreach($grpsmemberids as $grpsmemberid) {
        $groupsmember = $DB->get_record('groups_members', array('id'=>$grpsmemberid), '*', MUST_EXIST);
        $userid = $groupsmember->userid;
        $groupid = $groupsmember->groupid;
        $currentgroup = $DB->get_record('groups', array('id' => $groupid), 'id,name', MUST_EXIST);
        if (groups_is_member($groupid, $userid)) {
            groups_remove_member($groupid, $userid);
            $event = \mod_choicegroup\event\choice_removed::create($eventparams);
            $event->add_record_snapshot('course_modules', $cm);
            $event->add_record_snapshot('course', $course);
            $event->add_record_snapshot('choicegroup', $choicegroup);
            $event->trigger();
        }
        // Update completion state
        $current = choicegroup_get_user_answer($choicegroup, $userid, false, true);
        if ($current === false && $completion->is_enabled($cm) && $choicegroup->completionsubmit) {
            $completion->update_state($cm, COMPLETION_INCOMPLETE, $userid);
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
function choicegroup_delete_instance($id) {
    global $DB;

    if (! $choicegroup = $DB->get_record("choicegroup", array("id"=>"$id"))) {
        return false;
    }

    $result = true;

    if (! $DB->delete_records("choicegroup_options", array("choicegroupid"=>"$choicegroup->id"))) {
        $result = false;
    }

    if (! $DB->delete_records("choicegroup", array("id"=>"$choicegroup->id"))) {
        $result = false;
    }

    return $result;
}

/**
 * Returns text string which is the answer that matches the id
 *
 * @global object
 * @param object $choicegroup
 * @param int $id
 * @return string
 */
function choicegroup_get_option_text($choicegroup, $id) {
    global $DB;

    if ($result = $DB->get_record('groups', array('id' => $id))) {
        return $result->name;
    } else {
        return get_string("notanswered", "choicegroup");
    }
}

/*
 * Returns DB records of groups used by the choicegroup activity
 *
 * @global object
 * @param object $choicegroup
 * @return array
 */
function choicegroup_get_groups($choicegroup) {
    global $DB;

    static $groups = array();

    if (count($groups)) {
        return $groups;
    }

    if (is_numeric($choicegroup)) {
        $choicegroupid = $choicegroup;
    }
    else {
        $choicegroupid = $choicegroup->id;
    }

    $groups = array();
    $options = $DB->get_records('choicegroup_options', array('choicegroupid' => $choicegroupid));
    foreach ($options as $option) {
        if ($group = $DB->get_record('groups', array('id' => $option->groupid)))
        $groups[$group->id] = $group;
    }
    return $groups;
}

/**
 * Gets a full choicegroup record
 *
 * @global object
 * @param int $choicegroupid
 * @return object|bool The choicegroup or false
 */
function choicegroup_get_choicegroup($choicegroupid) {
    global $DB;

    if ($choicegroup = $DB->get_record("choicegroup", array("id" => $choicegroupid))) {
        $sortcolumn = choicegroup_get_sort_column($choicegroup);

        $params = array(
            'choicegroupid' => $choicegroupid
        );

        $grpfilter = '';
        if (($groupid = optional_param('group', 0, PARAM_INT)) != 0) {
            $params['groupid'] = $groupid;
            $grpfilter = "AND grp_o.groupid = :groupid";
        }

        $sql = "SELECT grp_m.id grpmemberid, grp_m.userid, grp_o.id, grp_o.groupid, grp_o.maxanswers
                 FROM {groups} grp
                 INNER JOIN {choicegroup_options} grp_o on grp.id = grp_o.groupid
                 LEFT JOIN {groups_members} grp_m on grp_m.groupid = grp_o.groupid
                 WHERE grp_o.choicegroupid = :choicegroupid $grpfilter
                 ORDER BY $sortcolumn ASC";

        $rs = $DB->get_recordset_sql($sql, $params);

        foreach ($rs as $option) {
            $choicegroup->option[$option->id] = $option->groupid;
            $choicegroup->grpmemberid[$option->grpmemberid] = array($option->groupid, $option->userid);
            $choicegroup->maxanswers[$option->id] = $option->maxanswers;
        }

        $rs->close();

        return $choicegroup;
    }
    return false;
}

function choicegroup_get_sort_column($choicegroup) {
    if ($choicegroup->sortgroupsby == CHOICEGROUP_SORTGROUPS_SYSTEMDEFAULT) {
        $sortcolumn = get_config('choicegroup', 'sortgroupsby');
    } else {
        $sortcolumn = $choicegroup->sortgroupsby;
    }

    switch ($sortcolumn) {
        case CHOICEGROUP_SORTGROUPS_CREATEDATE:
            return 'timecreated';
        case CHOICEGROUP_SORTGROUPS_NAME:
            return 'name';
        default:
            return 'timecreated';
    }
}

/**
 * @return array
 */
function choicegroup_get_view_actions() {
    return array('view','view all','report');
}

/**
 * @return array
 */
function choicegroup_get_post_actions() {
    return array('choose','choose again');
}


/**
 * Implementation of the function for printing the form elements that control
 * whether the course reset functionality affects the choicegroup.
 *
 * @param object $mform form passed by reference
 */
function choicegroup_reset_course_form_definition(&$mform) {
    $mform->addElement('header', 'choicegroupheader', get_string('modulenameplural', 'choicegroup'));
    $mform->addElement('advcheckbox', 'reset_choicegroup', get_string('removeresponses','choicegroup'));
}

/**
 * Course reset form defaults.
 *
 * @return array
 */
function choicegroup_reset_course_form_defaults($course) {
    return array('reset_choicegroup'=>1);
}

/**
 * @global object
 * @global object
 * @global object
 * @uses CONTEXT_MODULE
 * @param object $choicegroup
 * @param object $cm
 * @return array
 */
function choicegroup_get_response_data($choicegroup, $cm) {
    // Initialise the returned array, which is a matrix:  $allresponses[responseid][userid] = responseobject.
    static $allresponses = array();

    if (count($allresponses)) {
        return $allresponses;
    }
 
    // First get all the users who have access here.
    // To start with we assume they are all "unanswered" then move them later.
    $ctx = \context_module::instance($cm->id);
    $users = get_enrolled_users($ctx, 'mod/choicegroup:choose', 0, user_picture::fields('u', array('idnumber')), 'u.lastname ASC,u.firstname ASC');
    if ($users) {
        $modinfo = get_fast_modinfo($cm->course);
        $cminfo = $modinfo->get_cm($cm->id);
        $availability = new \core_availability\info_module($cminfo);
        $users = $availability->filter_user_list($users);
    }

    $allresponses[0] = $users;

    $responses = choicegroup_get_responses($choicegroup, $ctx);
    foreach ($responses as $response){
        if (isset($users[$response->userid])) {
            $allresponses[$response->groupid][$response->userid] = clone $users[$response->userid];
            $allresponses[$response->groupid][$response->userid]->timemodified = $response->timeadded;

            unset($allresponses[0][$response->userid]);
        }
    }
   return $allresponses;
}

/* Return an array with the options selected of users of the $choicegroup 
 * 
 * @param object $choicegroup choicegroup record
 * @param object $cm course module object
 * @return array of selected options by all users 
*/
function choicegroup_get_responses($choicegroup, $cm){

    global $DB;

    if (is_numeric($choicegroup)) {
        $choicegroupid = $choicegroup;
    } else {
        $choicegroupid = $choicegroup->id;
    }

    $params1 = array('choicegroupid'=>$choicegroupid);
    list($esql, $params2) = get_enrolled_sql($cm, 'mod/choicegroup:choose', 0);
    $params = array_merge($params1, $params2);

    $sql = 'SELECT gm.* FROM {user} u JOIN ('.$esql.') je ON je.id = u.id
        JOIN {groups_members} gm ON gm.userid = u.id AND groupid IN (
        SELECT groupid FROM {choicegroup_options} WHERE choicegroupid=:choicegroupid)
        WHERE u.deleted = 0 ORDER BY u.lastname ASC,u.firstname ASC';

    return $DB->get_records_sql($sql, $params);
}

/**
 * Returns all other caps used in module
 *
 * @return array
 */
function choicegroup_get_extra_capabilities() {
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
function choicegroup_supports($feature) {
    switch($feature) {
        case FEATURE_GROUPS:                  return true;
        case FEATURE_GROUPINGS:               return true;
        case FEATURE_GROUPMEMBERSONLY:        return true;
        case FEATURE_MOD_INTRO:               return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS: return true;
        case FEATURE_COMPLETION_HAS_RULES:    return true;
        case FEATURE_GRADE_HAS_GRADE:         return false;
        case FEATURE_GRADE_OUTCOMES:          return false;
        case FEATURE_BACKUP_MOODLE2:          return true;
        case FEATURE_SHOW_DESCRIPTION:        return true;

        default: return null;
    }
}

/**
 * Adds module specific settings to the settings block
 *
 * @param settings_navigation $settings The settings navigation object
 * @param navigation_node $choicegroupnode The node to add module settings to
 */
function choicegroup_extend_settings_navigation(settings_navigation $settings, navigation_node $choicegroupnode) {
    global $PAGE;

    if (has_capability('mod/choicegroup:readresponses', $PAGE->cm->context)) {

        $groupmode = groups_get_activity_groupmode($PAGE->cm);
        if ($groupmode) {
            groups_get_activity_group($PAGE->cm, true);
        }
        if (!$choicegroup = choicegroup_get_choicegroup($PAGE->cm->instance)) {
            print_error('invalidcoursemodule');
            return false;
        }
        $allresponses = choicegroup_get_response_data($choicegroup, $PAGE->cm, $groupmode);   // Big function, approx 6 SQL calls per user

        $responsecount = 0;
        $respondents = array();
        foreach($allresponses as $optionid => $userlist) {
            if ($optionid) {
                $responsecount += count($userlist);
                if ($choicegroup->multipleenrollmentspossible) {
                    foreach ($userlist as $user) {
                        if (!in_array($user->id, $respondents)) {
                            $respondents[] = $user->id;
                        }
                    }
                }
            }
        }
        $viewallresponsestext = get_string("viewallresponses", "choicegroup", $responsecount);
        if ($choicegroup->multipleenrollmentspossible == 1) {
            $viewallresponsestext .= ' ' . get_string("byparticipants", "choicegroup", count($respondents));
        }
        $choicegroupnode->add($viewallresponsestext, new moodle_url('/mod/choicegroup/report.php', array('id'=>$PAGE->cm->id)));
    }
}

/**
 * Obtains the automatic completion state for this choicegroup based on any conditions
 * in forum settings.
 *
 * @param object $course Course
 * @param object $cm Course-module
 * @param int $userid User ID
 * @param bool $type Type of comparison (or/and; can be used as return value if no conditions)
 * @return bool True if completed, false if not, $type if conditions not set.
 */
function choicegroup_get_completion_state($course, $cm, $userid, $type) {
    global $DB;

    // Get choicegroup details
    $choicegroup = $DB->get_record('choicegroup', array('id'=>$cm->instance), '*', MUST_EXIST);

    // If completion option is enabled, evaluate it and return true/false
    if($choicegroup->completionsubmit) {
        $useranswer = choicegroup_get_user_answer($choicegroup, $userid);
        return $useranswer !== false;
    } else {
        // Completion option is not enabled so just return $type
        return $type;
    }
}


/**
 * Return a list of page types
 * @param string $pagetype current page type
 * @param stdClass $parentcontext Block's parent context
 * @param stdClass $currentcontext Current context of block
 */
function choicegroup_page_type_list($pagetype, $parentcontext, $currentcontext) {
    $module_pagetype = array('mod-choicegroup-*'=>get_string('page-mod-choicegroup-x', 'choice'));
    return $module_pagetype;
}


function choicegroup_get_sort_options() {
    return array (
        CHOICEGROUP_SORTGROUPS_CREATEDATE => get_string('createdate', 'choicegroup'),
        CHOICEGROUP_SORTGROUPS_NAME => get_string('name', 'choicegroup')
    );
}


/**
 * This function receives a calendar event and returns the action associated with it, or null if there is none.
 *
 * This is used by block_myoverview in order to display the event appropriately. If null is returned then the event
 * is not displayed on the block.
 *
 * @param calendar_event $event
 * @param \core_calendar\action_factory $factory
 * @return \core_calendar\local\event\entities\action_interface|null
 */
function mod_choicegroup_core_calendar_provide_event_action(calendar_event $event,
                                                      \core_calendar\action_factory $factory) {
    $cm = get_fast_modinfo($event->courseid)->instances['choicegroup'][$event->instance];

    $completion = new \completion_info($cm->get_course());

    $completiondata = $completion->get_data($cm, false);

    if ($completiondata->completionstate != COMPLETION_INCOMPLETE) {
        return null;
    }

    return $factory->create_instance(
        get_string('view'),
        new \moodle_url('/mod/choicegroup/view.php', ['id' => $cm->id]),
        1,
        true
    );
}

