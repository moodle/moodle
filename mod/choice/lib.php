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
 * @package   mod_choice
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/** @global int $CHOICE_COLUMN_HEIGHT */
global $CHOICE_COLUMN_HEIGHT;
$CHOICE_COLUMN_HEIGHT = 300;

/** @global int $CHOICE_COLUMN_WIDTH */
global $CHOICE_COLUMN_WIDTH;
$CHOICE_COLUMN_WIDTH = 300;

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
        $result = new stdClass();
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
        $result = new stdClass();
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
    $choice->id = $DB->insert_record("choice", $choice);
    foreach ($choice->option as $key => $value) {
        $value = trim($value);
        if (isset($value) && $value <> '') {
            $option = new stdClass();
            $option->text = $value;
            $option->choiceid = $choice->id;
            if (isset($choice->limit[$key])) {
                $option->maxanswers = $choice->limit[$key];
            }
            $option->timemodified = time();
            $DB->insert_record("choice_options", $option);
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
        $option = new stdClass();
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
 * @param object $coursemodule
 * @param array $allresponses
 * @return array
 */
function choice_prepare_options($choice, $user, $coursemodule, $allresponses) {
    global $DB;

    $cdisplay = array('options'=>array());

    $cdisplay['limitanswers'] = true;
    $context = context_module::instance($coursemodule->id);

    foreach ($choice->option as $optionid => $text) {
        if (isset($text)) { //make sure there are no dud entries in the db with blank text values.
            $option = new stdClass;
            $option->attributes = new stdClass;
            $option->attributes->value = $optionid;
            $option->text = format_string($text);
            $option->maxanswers = $choice->maxanswers[$optionid];
            $option->displaylayout = $choice->display;

            if (isset($allresponses[$optionid])) {
                $option->countanswers = count($allresponses[$optionid]);
            } else {
                $option->countanswers = 0;
            }
            if ($DB->record_exists('choice_answers', array('choiceid' => $choice->id, 'userid' => $user->id, 'optionid' => $optionid))) {
                $option->attributes->checked = true;
            }
            if ( $choice->limitanswers && ($option->countanswers >= $option->maxanswers) && empty($option->attributes->checked)) {
                $option->attributes->disabled = true;
            }
            $cdisplay['options'][] = $option;
        }
    }

    $cdisplay['hascapability'] = is_enrolled($context, NULL, 'mod/choice:choose'); //only enrolled users are allowed to make a choice

    if ($choice->allowupdate && $DB->record_exists('choice_answers', array('choiceid'=> $choice->id, 'userid'=> $user->id))) {
        $cdisplay['allowupdate'] = true;
    }

    if ($choice->showpreview && $choice->timeopen > time()) {
        $cdisplay['previewonly'] = true;
    }

    return $cdisplay;
}

/**
 * Process user submitted answers for a choice,
 * and either updating them or saving new answers.
 *
 * @param int $formanswer users submitted answers.
 * @param object $choice the selected choice.
 * @param int $userid user identifier.
 * @param object $course current course.
 * @param object $cm course context.
 * @return void
 */
function choice_user_submit_response($formanswer, $choice, $userid, $course, $cm) {
    global $DB, $CFG;
    require_once($CFG->libdir.'/completionlib.php');

    $continueurl = new moodle_url('/mod/choice/view.php', array('id' => $cm->id));

    if (empty($formanswer)) {
        print_error('atleastoneoption', 'choice', $continueurl);
    }

    if (is_array($formanswer)) {
        if (!$choice->allowmultiple) {
            print_error('multiplenotallowederror', 'choice', $continueurl);
        }
        $formanswers = $formanswer;
    } else {
        $formanswers = array($formanswer);
    }

    // Start lock to prevent synchronous access to the same data
    // before it's updated, if using limits.
    if ($choice->limitanswers) {
        $timeout = 10;
        $locktype = 'mod_choice_choice_user_submit_response';
        // Limiting access to this choice.
        $resouce = 'choiceid:' . $choice->id;
        $lockfactory = \core\lock\lock_config::get_lock_factory($locktype);

        // Opening the lock.
        $choicelock = $lockfactory->get_lock($resouce, $timeout);
        if (!$choicelock) {
            print_error('cannotsubmit', 'choice', $continueurl);
        }
    }

    $current = $DB->get_records('choice_answers', array('choiceid' => $choice->id, 'userid' => $userid));
    $context = context_module::instance($cm->id);

    $choicesexceeded = false;
    $countanswers = array();
    foreach ($formanswers as $val) {
        $countanswers[$val] = 0;
    }
    if($choice->limitanswers) {
        // Find out whether groups are being used and enabled
        if (groups_get_activity_groupmode($cm) > 0) {
            $currentgroup = groups_get_activity_group($cm);
        } else {
            $currentgroup = 0;
        }

        list ($insql, $params) = $DB->get_in_or_equal($formanswers, SQL_PARAMS_NAMED);

        if($currentgroup) {
            // If groups are being used, retrieve responses only for users in
            // current group
            global $CFG;

            $params['groupid'] = $currentgroup;
            $sql = "SELECT ca.*
                      FROM {choice_answers} ca
                INNER JOIN {groups_members} gm ON ca.userid=gm.userid
                     WHERE optionid $insql
                       AND gm.groupid= :groupid";
        } else {
            // Groups are not used, retrieve all answers for this option ID
            $sql = "SELECT ca.*
                      FROM {choice_answers} ca
                     WHERE optionid $insql";
        }

        $answers = $DB->get_records_sql($sql, $params);
        if ($answers) {
            foreach ($answers as $a) { //only return enrolled users.
                if (is_enrolled($context, $a->userid, 'mod/choice:choose')) {
                    $countanswers[$a->optionid]++;
                }
            }
        }
        foreach ($countanswers as $opt => $count) {
            if ($count >= $choice->maxanswers[$opt]) {
                $choicesexceeded = true;
                break;
            }
        }
    }

    // Check the user hasn't exceeded the maximum selections for the choice(s) they have selected.
    if (!($choice->limitanswers && $choicesexceeded)) {
        $answersnapshots = array();
        if ($current) {
            // Update an existing answer.
            $existingchoices = array();
            foreach ($current as $c) {
                if (in_array($c->optionid, $formanswers)) {
                    $existingchoices[] = $c->optionid;
                    $DB->set_field('choice_answers', 'timemodified', time(), array('id' => $c->id));
                    $answersnapshots[] = $c;
                } else {
                    $DB->delete_records('choice_answers', array('id' => $c->id));
                }
            }

            // Add new ones.
            foreach ($formanswers as $f) {
                if (!in_array($f, $existingchoices)) {
                    $newanswer = new stdClass();
                    $newanswer->optionid = $f;
                    $newanswer->choiceid = $choice->id;
                    $newanswer->userid = $userid;
                    $newanswer->timemodified = time();
                    $newanswer->id = $DB->insert_record("choice_answers", $newanswer);
                    $answersnapshots[] = $newanswer;
                }
            }

            // Initialised as true, meaning we updated the answer.
            $answerupdated = true;
        } else {
            // Add new answer.
            foreach ($formanswers as $answer) {
                $newanswer = new stdClass();
                $newanswer->choiceid = $choice->id;
                $newanswer->userid = $userid;
                $newanswer->optionid = $answer;
                $newanswer->timemodified = time();
                $newanswer->id = $DB->insert_record("choice_answers", $newanswer);
                $answersnapshots[] = $newanswer;
            }

            // Update completion state
            $completion = new completion_info($course);
            if ($completion->is_enabled($cm) && $choice->completionsubmit) {
                $completion->update_state($cm, COMPLETION_COMPLETE);
            }

            // Initalised as false, meaning we submitted a new answer.
            $answerupdated = false;
        }
    } else {
        // Check to see if current choice already selected - if not display error.
        $currentids = array_keys($current);

        if (array_diff($currentids, $formanswers) || array_diff($formanswers, $currentids) ) {
            // Release lock before error.
            $choicelock->release();
            print_error('choicefull', 'choice', $continueurl);
        }
    }

    // Release lock.
    if (isset($choicelock)) {
        $choicelock->release();
    }

    // Now record completed event.
    if (isset($answerupdated)) {
        $eventdata = array();
        $eventdata['context'] = $context;
        $eventdata['objectid'] = $choice->id;
        $eventdata['userid'] = $userid;
        $eventdata['courseid'] = $course->id;
        $eventdata['other'] = array();
        $eventdata['other']['choiceid'] = $choice->id;

        if ($answerupdated) {
            $eventdata['other']['optionid'] = $formanswer;
            $event = \mod_choice\event\answer_updated::create($eventdata);
        } else {
            $eventdata['other']['optionid'] = $formanswers;
            $event = \mod_choice\event\answer_submitted::create($eventdata);
        }
        $event->add_record_snapshot('course', $course);
        $event->add_record_snapshot('course_modules', $cm);
        $event->add_record_snapshot('choice', $choice);
        foreach ($answersnapshots as $record) {
            $event->add_record_snapshot('choice_answers', $record);
        }
        $event->trigger();
    }
}

/**
 * @param array $user
 * @param object $cm
 * @return void Output is echo'd
 */
function choice_show_reportlink($user, $cm) {
    $userschosen = array();
    foreach($user as $optionid => $userlist) {
        if ($optionid) {
            $userschosen = array_merge($userschosen, array_keys($userlist));
        }
    }
    $responsecount = count(array_unique($userschosen));

    echo '<div class="reportlink">';
    echo "<a href=\"report.php?id=$cm->id\">".get_string("viewallresponses", "choice", $responsecount)."</a>";
    echo '</div>';
}

/**
 * @global object
 * @param object $choice
 * @param object $course
 * @param object $coursemodule
 * @param array $allresponses

 *  * @param bool $allresponses
 * @return object
 */
function prepare_choice_show_results($choice, $course, $cm, $allresponses) {
    global $OUTPUT;

    $display = clone($choice);
    $display->coursemoduleid = $cm->id;
    $display->courseid = $course->id;

    //overwrite options value;
    $display->options = array();
    $totaluser = 0;
    foreach ($choice->option as $optionid => $optiontext) {
        $display->options[$optionid] = new stdClass;
        $display->options[$optionid]->text = $optiontext;
        $display->options[$optionid]->maxanswer = $choice->maxanswers[$optionid];

        if (array_key_exists($optionid, $allresponses)) {
            $display->options[$optionid]->user = $allresponses[$optionid];
            $totaluser += count($allresponses[$optionid]);
        }
    }
    unset($display->option);
    unset($display->maxanswers);

    $display->numberofuser = $totaluser;
    $context = context_module::instance($cm->id);
    $display->viewresponsecapability = has_capability('mod/choice:readresponses', $context);
    $display->deleterepsonsecapability = has_capability('mod/choice:deleteresponses',$context);
    $display->fullnamecapability = has_capability('moodle/site:viewfullnames', $context);

    if (empty($allresponses)) {
        echo $OUTPUT->heading(get_string("nousersyet"), 3, null);
        return false;
    }

    return $display;
}

/**
 * @global object
 * @param array $attemptids
 * @param object $choice Choice main table row
 * @param object $cm Course-module object
 * @param object $course Course object
 * @return bool
 */
function choice_delete_responses($attemptids, $choice, $cm, $course) {
    global $DB, $CFG;
    require_once($CFG->libdir.'/completionlib.php');

    if(!is_array($attemptids) || empty($attemptids)) {
        return false;
    }

    foreach($attemptids as $num => $attemptid) {
        if(empty($attemptid)) {
            unset($attemptids[$num]);
        }
    }

    $completion = new completion_info($course);
    foreach($attemptids as $attemptid) {
        if ($todelete = $DB->get_record('choice_answers', array('choiceid' => $choice->id, 'id' => $attemptid))) {
            $DB->delete_records('choice_answers', array('choiceid' => $choice->id, 'id' => $attemptid));
            // Update completion state
            if ($completion->is_enabled($cm) && $choice->completionsubmit) {
                $completion->update_state($cm, COMPLETION_INCOMPLETE, $attemptid);
            }
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
 * List the actions that correspond to a view of this module.
 * This is used by the participation report.
 *
 * Note: This is not used by new logging system. Event with
 *       crud = 'r' and edulevel = LEVEL_PARTICIPATING will
 *       be considered as view action.
 *
 * @return array
 */
function choice_get_view_actions() {
    return array('view','view all','report');
}

/**
 * List the actions that correspond to a post of this module.
 * This is used by the participation report.
 *
 * Note: This is not used by new logging system. Event with
 *       crud = ('c' || 'u' || 'd') and edulevel = LEVEL_PARTICIPATING
 *       will be considered as post action.
 *
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
 * Actual implementation of the reset course functionality, delete all the
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

    $context = context_module::instance($cm->id);

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
    $allresponses[0] = get_enrolled_users($context, 'mod/choice:choose', $currentgroup, user_picture::fields('u', array('idnumber')));

/// Get all the recorded responses for this choice
    $rawresponses = $DB->get_records('choice_answers', array('choiceid' => $choice->id));

/// Use the responses to move users into the correct column

    if ($rawresponses) {
        $answeredusers = array();
        foreach ($rawresponses as $response) {
            if (isset($allresponses[0][$response->userid])) {   // This person is enrolled and in correct group
                $allresponses[0][$response->userid]->timemodified = $response->timemodified;
                $allresponses[$response->optionid][$response->userid] = clone($allresponses[0][$response->userid]);
                $allresponses[$response->optionid][$response->userid]->answerid = $response->id;
                $answeredusers[] = $response->userid;
            }
        }
        foreach ($answeredusers as $answereduser) {
            unset($allresponses[0][$answereduser]);
        }
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

/**
 * Obtains the automatic completion state for this choice based on any conditions
 * in forum settings.
 *
 * @param object $course Course
 * @param object $cm Course-module
 * @param int $userid User ID
 * @param bool $type Type of comparison (or/and; can be used as return value if no conditions)
 * @return bool True if completed, false if not, $type if conditions not set.
 */
function choice_get_completion_state($course, $cm, $userid, $type) {
    global $CFG,$DB;

    // Get choice details
    $choice = $DB->get_record('choice', array('id'=>$cm->instance), '*',
            MUST_EXIST);

    // If completion option is enabled, evaluate it and return true/false
    if($choice->completionsubmit) {
        return $DB->record_exists('choice_answers', array(
                'choiceid'=>$choice->id, 'userid'=>$userid));
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
function choice_page_type_list($pagetype, $parentcontext, $currentcontext) {
    $module_pagetype = array('mod-choice-*'=>get_string('page-mod-choice-x', 'choice'));
    return $module_pagetype;
}

/**
 * Prints choice summaries on MyMoodle Page
 *
 * Prints choice name, due date and attempt information on
 * choice activities that have a deadline that has not already passed
 * and it is available for completing.
 * @uses CONTEXT_MODULE
 * @param array $courses An array of course objects to get choice instances from.
 * @param array $htmlarray Store overview output array( course ID => 'choice' => HTML output )
 */
function choice_print_overview($courses, &$htmlarray) {
    global $USER, $DB, $OUTPUT;

    if (empty($courses) || !is_array($courses) || count($courses) == 0) {
        return;
    }
    if (!$choices = get_all_instances_in_courses('choice', $courses)) {
        return;
    }

    $now = time();
    foreach ($choices as $choice) {
        if ($choice->timeclose != 0                                      // If this choice is scheduled.
            and $choice->timeclose >= $now                               // And the deadline has not passed.
            and ($choice->timeopen == 0 or $choice->timeopen <= $now)) { // And the choice is available.

            // Visibility.
            $class = (!$choice->visible) ? 'dimmed' : '';

            // Link to activity.
            $url = new moodle_url('/mod/choice/view.php', array('id' => $choice->coursemodule));
            $url = html_writer::link($url, format_string($choice->name), array('class' => $class));
            $str = $OUTPUT->box(get_string('choiceactivityname', 'choice', $url), 'name');

             // Deadline.
            $str .= $OUTPUT->box(get_string('choicecloseson', 'choice', userdate($choice->timeclose)), 'info');

            // Display relevant info based on permissions.
            if (has_capability('mod/choice:readresponses', context_module::instance($choice->coursemodule))) {
                $attempts = $DB->count_records('choice_answers', array('choiceid' => $choice->id));
                $str .= $OUTPUT->box(get_string('viewallresponses', 'choice', $attempts), 'info');

            } else if (has_capability('mod/choice:choose', context_module::instance($choice->coursemodule))) {
                // See if the user has submitted anything.
                $answers = $DB->count_records('choice_answers', array('choiceid' => $choice->id, 'userid' => $USER->id));
                if ($answers > 0) {
                    // User has already selected an answer, nothing to show.
                    $str = '';
                } else {
                    // User has not made a selection yet.
                    $str .= $OUTPUT->box(get_string('notanswered', 'choice'), 'info');
                }
            } else {
                // Does not have permission to do anything on this choice activity.
                $str = '';
            }

            // Make sure we have something to display.
            if (!empty($str)) {
                // Generate the containing div.
                $str = $OUTPUT->box($str, 'choice overview');

                if (empty($htmlarray[$choice->course]['choice'])) {
                    $htmlarray[$choice->course]['choice'] = $str;
                } else {
                    $htmlarray[$choice->course]['choice'] .= $str;
                }
            }
        }
    }
    return;
}
