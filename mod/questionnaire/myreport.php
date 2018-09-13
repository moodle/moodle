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

// This page shows results of a questionnaire to a student.

require_once("../../config.php");
require_once($CFG->dirroot.'/mod/questionnaire/questionnaire.class.php');

$instance = required_param('instance', PARAM_INT);   // Questionnaire ID.
$userid = optional_param('user', $USER->id, PARAM_INT);
$rid = optional_param('rid', null, PARAM_INT);
$byresponse = optional_param('byresponse', 0, PARAM_INT);
$action = optional_param('action', 'summary', PARAM_ALPHA);
$currentgroupid = optional_param('group', 0, PARAM_INT); // Groupid.

if (! $questionnaire = $DB->get_record("questionnaire", array("id" => $instance))) {
    print_error('incorrectquestionnaire', 'questionnaire');
}
if (! $course = $DB->get_record("course", array("id" => $questionnaire->course))) {
    print_error('coursemisconf');
}
if (! $cm = get_coursemodule_from_instance("questionnaire", $questionnaire->id, $course->id)) {
    print_error('invalidcoursemodule');
}

require_course_login($course, true, $cm);
$context = context_module::instance($cm->id);
$questionnaire->canviewallgroups = has_capability('moodle/site:accessallgroups', $context);
// Should never happen, unless called directly by a snoop...
if ( !has_capability('mod/questionnaire:readownresponses', $context)
    || $userid != $USER->id) {
    print_error('Permission denied');
}
$url = new moodle_url($CFG->wwwroot.'/mod/questionnaire/myreport.php', array('instance' => $instance));
if (isset($userid)) {
    $url->param('userid', $userid);
}
if (isset($byresponse)) {
    $url->param('byresponse', $byresponse);
}

if (isset($currentgroupid)) {
    $url->param('group', $currentgroupid);
}

if (isset($action)) {
    $url->param('action', $action);
}

$PAGE->set_url($url);
$PAGE->set_context($context);
$PAGE->set_title(get_string('questionnairereport', 'questionnaire'));
$PAGE->set_heading(format_string($course->fullname));

$questionnaire = new questionnaire(0, $questionnaire, $course, $cm);
// Add renderer and page objects to the questionnaire object for display use.
$questionnaire->add_renderer($PAGE->get_renderer('mod_questionnaire'));
$questionnaire->add_page(new \mod_questionnaire\output\reportpage());

$sid = $questionnaire->survey->id;
$courseid = $course->id;

// Tab setup.
if (!isset($SESSION->questionnaire)) {
    $SESSION->questionnaire = new stdClass();
}
$SESSION->questionnaire->current_tab = 'myreport';

switch ($action) {
    case 'summary':
        if (empty($questionnaire->survey)) {
            print_error('surveynotexists', 'questionnaire');
        }
        $SESSION->questionnaire->current_tab = 'mysummary';
        $params = ['survey_id' => $questionnaire->sid, 'userid' => $userid, 'complete' => 'y'];
        $resps = $DB->get_records('questionnaire_response', $params);
        $rids = array_keys($resps);
        if (count($resps) > 1) {
            $titletext = get_string('myresponsetitle', 'questionnaire', count($resps));
        } else {
            $titletext = get_string('yourresponse', 'questionnaire');
        }

        // Print the page header.
        echo $questionnaire->renderer->header();

        // Print the tabs.
        include('tabs.php');

        $questionnaire->page->add_to_page('myheaders', $titletext);
        $questionnaire->survey_results(1, 1, '', '', $rids, $USER->id);

        echo $questionnaire->renderer->render($questionnaire->page);

        // Finish the page.
        echo $questionnaire->renderer->footer($course);
        break;

    case 'vall':
        if (empty($questionnaire->survey)) {
            print_error('surveynotexists', 'questionnaire');
        }
        $SESSION->questionnaire->current_tab = 'myvall';
        $params = ['survey_id' => $questionnaire->sid, 'userid' => $userid, 'complete' => 'y'];
        $resps = $DB->get_records('questionnaire_response', $params, 'submitted ASC');
        $titletext = get_string('myresponses', 'questionnaire');

        // Print the page header.
        echo $questionnaire->renderer->header();

        // Print the tabs.
        include('tabs.php');

        $questionnaire->page->add_to_page('myheaders', $titletext);
        $questionnaire->view_all_responses($resps);
        echo $questionnaire->renderer->render($questionnaire->page);
        // Finish the page.
        echo $questionnaire->renderer->footer($course);
        break;

    case 'vresp':
        if (empty($questionnaire->survey)) {
            print_error('surveynotexists', 'questionnaire');
        }
        $SESSION->questionnaire->current_tab = 'mybyresponse';
        $usergraph = get_config('questionnaire', 'usergraph');
        if ($usergraph) {
            $charttype = $questionnaire->survey->chart_type;
            if ($charttype) {
                $PAGE->requires->js('/mod/questionnaire/javascript/RGraph/RGraph.common.core.js');

                switch ($charttype) {
                    case 'bipolar':
                        $PAGE->requires->js('/mod/questionnaire/javascript/RGraph/RGraph.bipolar.js');
                        break;
                    case 'hbar':
                        $PAGE->requires->js('/mod/questionnaire/javascript/RGraph/RGraph.hbar.js');
                        break;
                    case 'radar':
                        $PAGE->requires->js('/mod/questionnaire/javascript/RGraph/RGraph.radar.js');
                        break;
                    case 'rose':
                        $PAGE->requires->js('/mod/questionnaire/javascript/RGraph/RGraph.rose.js');
                        break;
                    case 'vprogress':
                        $PAGE->requires->js('/mod/questionnaire/javascript/RGraph/RGraph.vprogress.js');
                        break;
                }
            }
        }
        $params = ['survey_id' => $questionnaire->sid, 'userid' => $userid, 'complete' => 'y'];
        $resps = $DB->get_records('questionnaire_response', $params, 'submitted ASC');

        // All participants.
        $params = ['survey_id' => $sid, 'complete' => 'y'];
        $fields = 'id,survey_id,submitted,userid';
        $respsallparticipants = $DB->get_records('questionnaire_response', $params, 'id', $fields);

        $params = ['survey_id' => $questionnaire->sid, 'userid' => $userid, 'complete' => 'y'];
        $fields = 'id,survey_id,submitted,userid';
        $respsuser = $DB->get_records('questionnaire_response', $params, '', $fields);

        $SESSION->questionnaire->numrespsallparticipants = count($respsallparticipants);
        $SESSION->questionnaire->numselectedresps = $SESSION->questionnaire->numrespsallparticipants;
        $iscurrentgroupmember = false;

        // Available group modes (0 = no groups; 1 = separate groups; 2 = visible groups).
        $groupmode = groups_get_activity_groupmode($cm, $course);
        if ($groupmode > 0) {
            // Check if current user is member of any group.
            $usergroups = groups_get_user_groups($courseid, $userid);
            $isgroupmember = count($usergroups[0]) > 0;
            // Check if current user is member of current group.
            $iscurrentgroupmember = groups_is_member($currentgroupid, $userid);

            if ($groupmode == 1) {
                $questionnairegroups = groups_get_all_groups($course->id, $userid);
            }
            if ($groupmode == 2 || $questionnaire->canviewallgroups) {
                $questionnairegroups = groups_get_all_groups($course->id);
            }

            if (!empty($questionnairegroups)) {
                $groupscount = count($questionnairegroups);
                foreach ($questionnairegroups as $key) {
                    $firstgroupid = $key->id;
                    break;
                }
                if ($groupscount === 0 && $groupmode == 1) {
                    $currentgroupid = 0;
                }
                if ($groupmode == 1 && !$questionnaire->canviewallgroups && $currentgroupid == 0) {
                    $currentgroupid = $firstgroupid;
                }
                // If currentgroup is All Participants, current user is of course member of that "group"!
                if ($currentgroupid == 0) {
                    $iscurrentgroupmember = true;
                }
                // Current group members.
                $sql = 'SELECT r.id, r.survey_id, r.submitted, r.userid '.
                       'FROM {questionnaire_response} r, {groups_members} gm '.
                       'WHERE r.survey_id = ? AND r.complete = \'y\' AND gm.groupid = ? AND r.userid = gm.userid '.
                       'ORDER BY r.id';
                $currentgroupresps = $DB->get_records_sql($sql, [$sid, $currentgroupid]);

            } else {
                // Groupmode = separate groups but user is not member of any group
                // and does not have moodle/site:accessallgroups capability -> refuse view responses.
                if (!$questionnaire->canviewallgroups) {
                    $currentgroupid = 0;
                }
            }

            if ($currentgroupid > 0) {
                $groupname = get_string('group').' <strong>'.groups_get_group_name($currentgroupid).'</strong>';
            } else {
                $groupname = '<strong>'.get_string('allparticipants').'</strong>';
            }
        }

        $rids = array_keys($resps);
        if (!$rid) {
            // If more than one response for this respondent, display most recent response.
            $rid = end($rids);
        }
        $numresp = count($rids);
        if ($numresp > 1) {
            $titletext = get_string('myresponsetitle', 'questionnaire', $numresp);
        } else {
            $titletext = get_string('yourresponse', 'questionnaire');
        }

        $compare = false;
        // Print the page header.
        echo $questionnaire->renderer->header();

        // Print the tabs.
        include('tabs.php');
        $questionnaire->page->add_to_page('myheaders', $titletext);

        if (count($resps) > 1) {
            $userresps = $resps;
            $questionnaire->survey_results_navbar_student ($rid, $userid, $instance, $userresps);
        }
        $resps = array();
        // Determine here which "global" responses should get displayed for comparison with current user.
        // Current user is viewing his own group's results.
        if (isset($currentgroupresps)) {
            $resps = $currentgroupresps;
        }

        // Current user is viewing another group's results so we must add their own results to that group's results.

        if (!$iscurrentgroupmember) {
            $resps += $respsuser;
        }
        // No groups.
        if ($groupmode == 0 || $currentgroupid == 0) {
            $resps = $respsallparticipants;
        }
        $compare = true;
        $questionnaire->view_response($rid, null, null, $resps, $compare, $iscurrentgroupmember, false, $currentgroupid);
        // Finish the page.
        echo $questionnaire->renderer->render($questionnaire->page);
        echo $questionnaire->renderer->footer($course);
        break;

    case get_string('return', 'questionnaire'):
    default:
        redirect('view.php?id='.$cm->id);
}
