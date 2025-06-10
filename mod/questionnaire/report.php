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
 * The main report page for a questionnaire.
 *
 * @package mod_questionnaire
 * @copyright  2016 Mike Churchward (mike.churchward@poetgroup.org)
 * @author     Mike Churchward
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */
require_once("../../config.php");
require_once($CFG->dirroot.'/mod/questionnaire/questionnaire.class.php');

$instance = optional_param('instance', false, PARAM_INT);   // Questionnaire ID.
$action = optional_param('action', 'vall', PARAM_ALPHA);
$sid = optional_param('sid', null, PARAM_INT);              // Survey id.
$rid = optional_param('rid', false, PARAM_INT);
$type = optional_param('type', '', PARAM_ALPHA);
$byresponse = optional_param('byresponse', false, PARAM_INT);
$individualresponse = optional_param('individualresponse', false, PARAM_INT);
$currentgroupid = optional_param('group', 0, PARAM_INT); // Groupid.
$user = optional_param('user', '', PARAM_INT);
$outputtarget = optional_param('target', 'html', PARAM_ALPHA); // Default 'html'. Could be 'pdf'.

$userid = $USER->id;
switch ($action) {
    case 'vallasort':
        $sort = 'ascending';
        break;
    case 'vallarsort':
        $sort = 'descending';
        break;
    default:
        $sort = 'default';
}

if ($instance === false) {
    if (!empty($SESSION->instance)) {
        $instance = $SESSION->instance;
    } else {
        throw new \moodle_exception('requiredparameter', 'mod_questionnaire');
    }
}
$SESSION->instance = $instance;
$usergraph = get_config('questionnaire', 'usergraph');

if (! $questionnaire = $DB->get_record("questionnaire", array("id" => $instance))) {
    throw new \moodle_exception('incorrectquestionnaire', 'mod_questionnaire');
}
if (! $course = $DB->get_record("course", array("id" => $questionnaire->course))) {
    throw new \moodle_exception('coursemisconf', 'mod_questionnaire');
}
if (! $cm = get_coursemodule_from_instance("questionnaire", $questionnaire->id, $course->id)) {
    throw new \moodle_exception('invalidcoursemodule', 'mod_questionnaire');
}

require_course_login($course, true, $cm);

$questionnaire = new questionnaire($course, $cm, 0, $questionnaire);

// Add renderer and page objects to the questionnaire object for display use.
$questionnaire->add_renderer($PAGE->get_renderer('mod_questionnaire'));
if ($outputtarget == 'pdf') {
    if ($action == 'vresp') {
        $questionnaire->add_page(new \mod_questionnaire\output\responsepagepdf());
    } else {
        $questionnaire->add_page(new \mod_questionnaire\output\reportpagepdf());
    }
} else { // Default to HTML.
    $questionnaire->add_page(new \mod_questionnaire\output\reportpage());
}

// If you can't view the questionnaire, or can't view a specified response, error out.
$context = context_module::instance($cm->id);
if (!$questionnaire->can_view_all_responses() && !$individualresponse) {
    // Should never happen, unless called directly by a snoop...
    throw new \moodle_exception('nopermissions', 'mod_questionnaire');
}

$questionnaire->canviewallgroups = has_capability('moodle/site:accessallgroups', $context);
$sid = $questionnaire->survey->id;

$url = new moodle_url($CFG->wwwroot.'/mod/questionnaire/report.php');
if ($instance) {
    $url->param('instance', $instance);
}

$url->param('action', $action);

if ($type) {
    $url->param('type', $type);
}
if ($byresponse || $individualresponse) {
    $url->param('byresponse', 1);
}
if ($user) {
    $url->param('user', $user);
}
if ($action == 'dresp') {
    $url->param('action', 'dresp');
    $url->param('byresponse', 1);
    $url->param('rid', $rid);
    $url->param('individualresponse', 1);
}
if ($currentgroupid !== null) {
    $url->param('group', $currentgroupid);
}

$PAGE->set_url($url);
$PAGE->set_context($context);
if ($outputtarget == 'print') {
    $PAGE->set_pagelayout('popup');
}

// Tab setup.
if (!isset($SESSION->questionnaire)) {
    $SESSION->questionnaire = new stdClass();
}
$SESSION->questionnaire->current_tab = 'allreport';

// Get all responses for further use in viewbyresp and deleteall etc.
// All participants.
$respsallparticipants = $questionnaire->get_responses();
$SESSION->questionnaire->numrespsallparticipants = count ($respsallparticipants);
$SESSION->questionnaire->numselectedresps = $SESSION->questionnaire->numrespsallparticipants;

// Available group modes (0 = no groups; 1 = separate groups; 2 = visible groups).
$groupmode = groups_get_activity_groupmode($cm, $course);
$questionnairegroups = '';
$groupscount = 0;
$SESSION->questionnaire->respscount = 0;
$SESSION->questionnaire_surveyid = $sid;

if ($groupmode > 0) {
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

switch ($action) {

    case 'dresp':  // Delete individual response? Ask for confirmation.
        require_capability('mod/questionnaire:deleteresponses', $context);

        if (empty($questionnaire->survey)) {
            $id = $questionnaire->survey;
            notify ("questionnaire->survey = /$id/");
            throw new \moodle_exception('surveynotexists', 'mod_questionnaire');
        } else if ($questionnaire->survey->courseid != $course->id) {
            throw new \moodle_exception('surveyowner', 'mod_questionnaire');
        } else if (!$rid || !is_numeric($rid)) {
            throw new \moodle_exception('invalidresponse', 'mod_questionnaire');
        } else if (!($resp = $DB->get_record('questionnaire_response', array('id' => $rid)))) {
            throw new \moodle_exception('invalidresponserecord', 'mod_questionnaire');
        }

        $ruser = false;
        if (!empty($resp->userid)) {
            if ($user = $DB->get_record('user', ['id' => $resp->userid])) {
                $ruser = fullname($user);
            } else {
                $ruser = '- '.get_string('unknown', 'questionnaire').' -';
            }
        } else {
            $ruser = $resp->userid;
        }

        // Print the page header.
        $PAGE->set_title(get_string('deletingresp', 'questionnaire'));
        $PAGE->set_heading(format_string($course->fullname));
        echo $questionnaire->renderer->header();

        // Print the tabs.
        $SESSION->questionnaire->current_tab = 'deleteresp';
        include('tabs.php');

        $timesubmitted = '<br />'.get_string('submitted', 'questionnaire').'&nbsp;'.userdate($resp->submitted);
        if ($questionnaire->respondenttype == 'anonymous') {
            $ruser = '- '.get_string('anonymous', 'questionnaire').' -';
            $timesubmitted = '';
        }

        // Print the confirmation.
        $msg = '<div class="warning centerpara">';
        $msg .= get_string('confirmdelresp', 'questionnaire', $ruser.$timesubmitted);
        $msg .= '</div>';
        $urlyes = new moodle_url('report.php', array('action' => 'dvresp',
            'rid' => $rid, 'individualresponse' => 1, 'instance' => $instance, 'group' => $currentgroupid));
        $urlno = new moodle_url('report.php', array('action' => 'vresp', 'instance' => $instance,
            'rid' => $rid, 'individualresponse' => 1, 'group' => $currentgroupid));
        $buttonyes = new single_button($urlyes, get_string('delete'), 'post');
        $buttonno = new single_button($urlno, get_string('cancel'), 'get');
        $questionnaire->page->add_to_page('notifications', $questionnaire->renderer->confirm($msg, $buttonyes, $buttonno));
        echo $questionnaire->renderer->render($questionnaire->page);
        // Finish the page.
        echo $questionnaire->renderer->footer($course);
        break;

    case 'delallresp': // Delete all responses? Ask for confirmation.
        require_capability('mod/questionnaire:deleteresponses', $context);

        if (!empty($respsallparticipants)) {

            // Print the page header.
            $PAGE->set_title(get_string('deletingresp', 'questionnaire'));
            $PAGE->set_heading(format_string($course->fullname));
            echo $questionnaire->renderer->header();

            // Print the tabs.
            $SESSION->questionnaire->current_tab = 'deleteall';
            include('tabs.php');

            // Print the confirmation.
            $msg = '<div class="warning centerpara">';
            if ($groupmode == 0) {   // No groups or visible groups.
                $msg .= get_string('confirmdelallresp', 'questionnaire');
            } else {                 // Separate groups.
                $msg .= get_string('confirmdelgroupresp', 'questionnaire', $groupname);
            }
            $msg .= '</div>';

            $urlyes = new moodle_url('report.php', array('action' => 'dvallresp', 'sid' => $sid,
                'instance' => $instance, 'group' => $currentgroupid));
            $urlno = new moodle_url('report.php', array('instance' => $instance, 'group' => $currentgroupid));
            $buttonyes = new single_button($urlyes, get_string('delete'), 'post');
            $buttonno = new single_button($urlno, get_string('cancel'), 'get');

            $questionnaire->page->add_to_page('notifications', $questionnaire->renderer->confirm($msg, $buttonyes, $buttonno));
            echo $questionnaire->renderer->render($questionnaire->page);
            // Finish the page.
            echo $questionnaire->renderer->footer($course);
        }
        break;

    case 'dvresp': // Delete single response. Do it!
        require_capability('mod/questionnaire:deleteresponses', $context);

        if (empty($questionnaire->survey)) {
            throw new \moodle_exception('surveynotexists', 'mod_questionnaire');
        } else if ($questionnaire->survey->courseid != $course->id) {
            throw new \moodle_exception('surveyowner', 'mod_questionnaire');
        } else if (!$rid || !is_numeric($rid)) {
            throw new \moodle_exception('invalidresponse', 'mod_questionnaire');
        } else if (!($response = $DB->get_record('questionnaire_response', array('id' => $rid)))) {
            throw new \moodle_exception('invalidresponserecord', 'mod_questionnaire');
        }

        if (questionnaire_delete_response($response, $questionnaire)) {
            if (!$DB->count_records('questionnaire_response', array('questionnaireid' => $questionnaire->id, 'complete' => 'y'))) {
                $redirection = $CFG->wwwroot.'/mod/questionnaire/view.php?id='.$cm->id;
            } else {
                $redirection = $CFG->wwwroot.'/mod/questionnaire/report.php?action=vresp&amp;instance='.
                    $instance.'&amp;byresponse=1';
            }

            // Log this questionnaire delete single response action.
            $params = array('objectid' => $questionnaire->survey->id,
                'context' => $questionnaire->context,
                'courseid' => $questionnaire->course->id,
                'relateduserid' => $response->userid);
            $event = \mod_questionnaire\event\response_deleted::create($params);
            $event->trigger();

            redirect($redirection);
        } else {
            if ($questionnaire->respondenttype == 'anonymous') {
                $ruser = '- '.get_string('anonymous', 'questionnaire').' -';
            } else if (!empty($response->userid)) {
                if ($user = $DB->get_record('user', ['id' => $response->userid])) {
                    $ruser = fullname($user);
                } else {
                    $ruser = '- '.get_string('unknown', 'questionnaire').' -';
                }
            } else {
                $ruser = $response->userid;
            }
            error (get_string('couldnotdelresp', 'questionnaire').$rid.get_string('by', 'questionnaire').$ruser.'?',
                $CFG->wwwroot.'/mod/questionnaire/report.php?action=vresp&amp;sid='.$sid.'&amp;&amp;instance='.
                $instance.'byresponse=1');
        }
        break;

    case 'dvallresp': // Delete all responses in questionnaire (or group). Do it!
        require_capability('mod/questionnaire:deleteresponses', $context);

        if (empty($questionnaire->survey)) {
            throw new \moodle_exception('surveynotexists', 'mod_questionnaire');
        } else if ($questionnaire->survey->courseid != $course->id) {
            throw new \moodle_exception('surveyowner', 'mod_questionnaire');
        }

        // Available group modes (0 = no groups; 1 = separate groups; 2 = visible groups).
        if ($groupmode > 0) {
            switch ($currentgroupid) {
                case 0:     // All participants.
                    $resps = $respsallparticipants;
                    break;
                default:     // Members of a specific group.
                    if (!($resps = $questionnaire->get_responses(false, $currentgroupid))) {
                        $resps = [];
                    }
            }
            if (empty($resps)) {
                $noresponses = true;
            } else {
                if ($rid === false) {
                    $resp = current($resps);
                    $rid = $resp->id;
                } else {
                    $resp = $DB->get_record('questionnaire_response', array('id' => $rid));
                }
                if (!empty($resp->userid)) {
                    if ($user = $DB->get_record('user', ['id' => $resp->userid])) {
                        $ruser = fullname($user);
                    } else {
                        $ruser = '- '.get_string('unknown', 'questionnaire').' -';
                    }
                } else {
                    $ruser = $resp->userid;
                }
            }
        } else {
            $resps = $respsallparticipants;
        }

        if (!empty($resps)) {
            foreach ($resps as $response) {
                questionnaire_delete_response($response, $questionnaire);
            }
            if (!$questionnaire->count_submissions()) {
                $redirection = $CFG->wwwroot.'/mod/questionnaire/view.php?id='.$cm->id;
            } else {
                $redirection = $CFG->wwwroot.'/mod/questionnaire/report.php?action=vall&amp;sid='.$sid.'&amp;instance='.$instance;
            }

            // Log this questionnaire delete all responses action.
            $context = context_module::instance($questionnaire->cm->id);
            $anonymous = $questionnaire->respondenttype == 'anonymous';

            $event = \mod_questionnaire\event\all_responses_deleted::create(array(
                'objectid' => $questionnaire->id,
                'anonymous' => $anonymous,
                'context' => $context
            ));
            $event->trigger();

            redirect($redirection);
        } else {
            error (get_string('couldnotdelresp', 'questionnaire'),
                $CFG->wwwroot.'/mod/questionnaire/report.php?action=vall&amp;sid='.$sid.'&amp;instance='.$instance);
        }
        break;

    case 'dwnpg': // Download page options.
        require_capability('mod/questionnaire:downloadresponses', $context);

        $PAGE->set_title(get_string('questionnairereport', 'questionnaire'));
        $PAGE->set_heading(format_string($course->fullname));
        echo $questionnaire->renderer->header();

        // Print the tabs.
        // Tab setup.
        if (empty($user)) {
            $SESSION->questionnaire->current_tab = 'downloadcsv';
        } else {
            $SESSION->questionnaire->current_tab = 'mydownloadcsv';
        }

        include('tabs.php');

        $groupname = '';
        if ($groupmode > 0) {
            switch ($currentgroupid) {
                case 0:     // All participants.
                    $groupname = get_string('allparticipants');
                    break;
                default:     // Members of a specific group.
                    $groupname = get_string('membersofselectedgroup', 'group').' '.get_string('group').' '.
                        $questionnairegroups[$currentgroupid]->name;
            }
        }
        $output = '';
        $output .= "<br /><br />\n";
        $output .= html_writer::tag('h2', (get_string('downloadtextformat', 'questionnaire'))
                . ':&nbsp;' . get_string('responses', 'questionnaire') . '&nbsp;' .
                $groupname . $questionnaire->renderer->help_icon('downloadtextformat', 'questionnaire'));
        $output .= $questionnaire->renderer->heading(get_string('textdownloadoptions', 'questionnaire'), 3);
        $output .= $questionnaire->renderer->box_start();
        $downloadparams = [
            'instance' => $instance,
            'user' => $user,
            'sid' => $sid,
            'action' => 'dfs',
            'group' => $currentgroupid
        ];
        $extrafields = $questionnaire->renderer->render_from_template('mod_questionnaire/extrafields', []);
        $output .= $questionnaire->renderer->download_dataformat_selector(get_string('downloadtypes', 'questionnaire'),
            'report.php', 'downloadformat', $downloadparams, $extrafields);
        $output .= $questionnaire->renderer->box_end();

        $questionnaire->page->add_to_page('respondentinfo', $output);
        echo $questionnaire->renderer->render($questionnaire->page);

        echo $questionnaire->renderer->footer('none');

        // Log saved as text action.
        $params = array('objectid' => $questionnaire->id,
            'context' => $questionnaire->context,
            'courseid' => $course->id,
            'other' => array('action' => $action, 'instance' => $instance, 'currentgroupid' => $currentgroupid)
        );
        $event = \mod_questionnaire\event\all_responses_saved_as_text::create($params);
        $event->trigger();

        exit();
        break;

    case 'dfs':
        require_capability('mod/questionnaire:downloadresponses', $context);
        // Use the questionnaire name as the file name. Clean it and change any non-filename characters to '_'.
        $name = clean_param($questionnaire->name, PARAM_FILE);
        $name = preg_replace("/[^A-Z0-9]+/i", "_", trim($name));

        $choicecodes = optional_param('choicecodes', '0', PARAM_INT);
        $choicetext = optional_param('choicetext', '0', PARAM_INT);
        $showincompletes = optional_param('complete', '0', PARAM_INT);
        $rankaverages = optional_param('rankaverages', '0', PARAM_INT);
        $dataformat = optional_param('downloadformat', '', PARAM_ALPHA);
        $emailroles = optional_param('emailroles', 0, PARAM_INT);
        $emailextra = optional_param('emailextra', '', PARAM_RAW);

        $output = $questionnaire->generate_csv($currentgroupid, '', $user, $choicecodes, $choicetext, $showincompletes,
            $rankaverages);

        $columns = $output[0];
        unset($output[0]);

        // Check if email report was selected.
        $emailreport = optional_param('emailreport', '', PARAM_ALPHA);
        if (empty($emailreport)) {
            \core\dataformat::download_data($name, $dataformat, $columns, $output);
        } else {
            // Emailreport button selected.
            if (get_config('questionnaire', 'allowemailreporting') && (!empty($emailroles) || !empty($emailextra))) {
                require_once('savefileformat.php');
                $users = !empty($emailroles) ? $questionnaire->get_notifiable_users($USER->id) : [];
                $otheremails = explode(',', $emailextra);
                if (!empty($users) || !empty($otheremails)) {
                    $thisurl = new moodle_url('report.php',
                        ['instance' => $instance, 'action' => 'dwnpg', 'group' => $currentgroupid]);
                    save_as_dataformat($name, $dataformat, $columns, $output, $users, $otheremails, $thisurl);
                }
            } else {
                redirect(new moodle_url('report.php', ['instance' => $instance, 'action' => 'dwnpg', 'group' => $currentgroupid]),
                    get_string('emailsnotspecified', 'questionnaire'));
            }
        }
        exit();
        break;

    case 'vall':         // View all responses.
    case 'vallasort':    // View all responses sorted in ascending order.
    case 'vallarsort':   // View all responses sorted in descending order.
        $PAGE->set_title(get_string('questionnairereport', 'questionnaire'));
        $PAGE->set_heading(format_string($course->fullname));
        if (!$questionnaire->capabilities->readallresponses && !$questionnaire->capabilities->readallresponseanytime) {
            echo $questionnaire->renderer->header();
            // Should never happen, unless called directly by a snoop.
            throw new \moodle_exception('nopermissions', 'mod_questionnaire');
            // Finish the page.
            echo $questionnaire->renderer->footer($course);
            break;
        }

        // Print the tabs.
        switch ($action) {
            case 'vallasort':
                $SESSION->questionnaire->current_tab = 'vallasort';
                break;
            case 'vallarsort':
                $SESSION->questionnaire->current_tab = 'vallarsort';
                break;
            default:
                $SESSION->questionnaire->current_tab = 'valldefault';
        }
        if ($outputtarget != 'print') {
            include('tabs.php');
        }

        $respinfo = '';
        $resps = array();
        // Enable choose_group if there are questionnaire groups and groupmode is not set to "no groups"
        // and if there are more goups than 1 (or if user can view all groups).
        if (is_array($questionnairegroups) && $groupmode > 0) {
            $groupselect = groups_print_activity_menu($cm, $url->out(), true);
            // Count number of responses in each group.
            foreach ($questionnairegroups as $group) {
                $respscount = $questionnaire->count_submissions(false, $group->id);
                $thisgroupname = groups_get_group_name($group->id);
                $escapedgroupname = preg_quote($thisgroupname, '/');
                if (!empty ($respscount)) {
                    // Add number of responses to name of group in the groups select list.
                    $groupselect = preg_replace('/\<option value="'.$group->id.'">'.$escapedgroupname.'<\/option>/',
                        '<option value="'.$group->id.'">'.$thisgroupname.' ('.$respscount.')</option>', $groupselect);
                } else {
                    // Remove groups with no responses from the groups select list.
                    $groupselect = preg_replace('/\<option value="'.$group->id.'">'.$escapedgroupname.
                        '<\/option>/', '', $groupselect);
                }
            }
            $respinfo .= isset($groupselect) ? ($groupselect . ' ') : '';
            $currentgroupid = groups_get_activity_group($cm);
        }
        if ($currentgroupid > 0) {
            $groupname = get_string('group').': <strong>'.groups_get_group_name($currentgroupid).'</strong>';
        } else {
            $groupname = '<strong>'.get_string('allparticipants').'</strong>';
        }

        // Available group modes (0 = no groups; 1 = separate groups; 2 = visible groups).
        if ($groupmode > 0) {
            switch ($currentgroupid) {
                case 0:     // All participants.
                    $resps = $respsallparticipants;
                    break;
                default:     // Members of a specific group.
                    if (!($resps = $questionnaire->get_responses(false, $currentgroupid))) {
                        $resps = '';
                    }
            }
            if (empty($resps)) {
                $noresponses = true;
            }
        } else {
            $resps = $respsallparticipants;
        }
        if (!empty($resps)) {
            // NOTE: response_analysis uses $resps to get the id's of the responses only.
            // Need to figure out what this function does.
            $feedbackmessages = $questionnaire->response_analysis(0, $resps, false, false, true, $currentgroupid);

            if ($feedbackmessages) {
                $msgout = '';
                foreach ($feedbackmessages as $msg) {
                    $msgout .= $msg;
                }
                $questionnaire->page->add_to_page('feedbackmessages', $msgout);
            }
        }

        $params = array('objectid' => $questionnaire->id,
            'context' => $context,
            'courseid' => $course->id,
            'other' => array('action' => $action, 'instance' => $instance, 'groupid' => $currentgroupid)
        );

        if ($outputtarget == 'pdf') {
            $pdf = questionnaire_report_start_pdf();
            if ($currentgroupid > 0) {
                $groupname = get_string('group') . ': <strong>' . groups_get_group_name($currentgroupid) . '</strong>';
            } else {
                $groupname = '<strong>' . get_string('allparticipants') . '</strong>';
            }
            $respinfo = get_string('viewallresponses', 'questionnaire') . '. ' . $groupname . '. ';
            $strsort = get_string('order_' . $sort, 'questionnaire');
            $respinfo .= $strsort;
            $questionnaire->page->add_to_page('respondentinfo', $respinfo);
            $questionnaire->survey_results('', false, true, $currentgroupid, $sort);
            $html = $questionnaire->renderer->render($questionnaire->page);

            // Supress any warnings. There is at least one error in the TCPF library at line 16749 where 'text-align' is
            // not an array.
            $errorreporting = error_reporting(0);
            $pdf->writeHTML($html);
            @$pdf->Output(clean_param($questionnaire->name, PARAM_FILE) . '.pdf', 'D');
            error_reporting($errorreporting);

        } else { // Default to HTML.
            $event = \mod_questionnaire\event\all_responses_viewed::create($params);
            $event->trigger();

            if ($outputtarget != 'print') {
                $linkname = get_string('downloadpdf', 'mod_questionnaire');
                $link = new moodle_url('/mod/questionnaire/report.php',
                    ['action' => 'vall', 'instance' => $instance, 'group' => $currentgroupid, 'target' => 'pdf']);
                $downpdficon = new pix_icon('f/pdf', $linkname);
                $respinfo .= $questionnaire->renderer->action_link($link, null, null, null, $downpdficon);

                $linkname = get_string('print', 'mod_questionnaire');
                $link = new \moodle_url('/mod/questionnaire/report.php',
                    ['action' => 'vall', 'instance' => $instance, 'group' => $currentgroupid, 'target' => 'print']);
                $htmlicon = new pix_icon('t/print', $linkname);
                $options = ['menubar' => true, 'location' => false, 'scrollbars' => true, 'resizable' => true,
                    'height' => 600, 'width' => 800, 'title' => $linkname];
                $name = 'popup';
                $action = new popup_action('click', $link, $name, $options);
                $class = '';
                $respinfo .= $questionnaire->renderer->action_link($link, null, $action,
                        ['class' => $class, 'title' => $linkname], $htmlicon) . '&nbsp;';

                $respinfo .= get_string('viewallresponses', 'questionnaire') . '. ' . $groupname . '. ';
                $strsort = get_string('order_' . $sort, 'questionnaire');
                $respinfo .= $strsort;
                $respinfo .= $questionnaire->renderer->help_icon('orderresponses', 'questionnaire');
                $questionnaire->page->add_to_page('respondentinfo', $respinfo);
            }

            $ret = $questionnaire->survey_results('', false, false, $currentgroupid, $sort);

            echo $questionnaire->renderer->header();
            echo $questionnaire->renderer->render($questionnaire->page);
            echo $questionnaire->renderer->footer($course);
        }
        break;

    case 'vresp': // View by response.
    default:
        if (empty($questionnaire->survey)) {
            throw new \moodle_exception('surveynotexists', 'mod_questionnaire');
        } else if ($questionnaire->survey->courseid != $course->id) {
            throw new \moodle_exception('surveyowner', 'mod_questionnaire');
        }
        $ruser = false;
        $noresponses = false;
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

        if ($byresponse || $rid) {
            // Available group modes (0 = no groups; 1 = separate groups; 2 = visible groups).
            if ($groupmode > 0) {
                switch ($currentgroupid) {
                    case 0:     // All participants.
                        $resps = $respsallparticipants;
                        break;
                    default:     // Members of a specific group.
                        $resps = $questionnaire->get_responses(false, $currentgroupid);
                }
                if (empty($resps)) {
                    $noresponses = true;
                } else {
                    if ($rid === false) {
                        $resp = current($resps);
                        $rid = $resp->id;
                    } else {
                        $resp = $DB->get_record('questionnaire_response', ['id' => $rid]);
                    }
                    if (!empty($resp->userid)) {
                        if ($user = $DB->get_record('user', ['id' => $resp->userid])) {
                            $ruser = fullname($user);
                        } else {
                            $ruser = '- '.get_string('unknown', 'questionnaire').' -';
                        }
                    } else {
                        $ruser = $resp->userid;
                    }
                }
            } else {
                $resps = $respsallparticipants;
            }
        }
        $rids = array_keys($resps);
        if (!$rid && !$noresponses) {
            $rid = $rids[0];
        }

        if ($noresponses) {
            $questionnaire->page->add_to_page('respondentinfo',
                get_string('group') . ' <strong>' . groups_get_group_name($currentgroupid) . '</strong>: ' .
                get_string('noresponses', 'questionnaire'));

        } else if ($outputtarget == 'pdf') {
            $pdf = questionnaire_report_start_pdf();
            if ($currentgroupid > 0) {
                $groupname = get_string('group') . ': <strong>' . groups_get_group_name($currentgroupid) . '</strong>';
            } else {
                $groupname = '<strong>' . get_string('allparticipants') . '</strong>';
            }
            if (!$byresponse) { // Show respondents individual responses.
                $questionnaire->view_response($rid, '', $resps, true, true, false, $currentgroupid, $outputtarget);
            }
            $html = $questionnaire->renderer->render($questionnaire->page);
            // Supress any warnings. There is at least one error in the TCPF library at line 16749 where 'text-align' is
            // not an array.
            $errorreporting = error_reporting(0);
            $pdf->writeHTML($html);
            @$pdf->Output(clean_param($questionnaire->name, PARAM_FILE), 'D');
            error_reporting($errorreporting);

        } else { // Default to HTML.
            // Print the page header.
            $PAGE->set_title(get_string('questionnairereport', 'questionnaire'));
            $PAGE->set_heading(format_string($course->fullname));

            // Print the tabs.
            if ($byresponse) {
                $SESSION->questionnaire->current_tab = 'vrespsummary';
            }
            if ($individualresponse) {
                $SESSION->questionnaire->current_tab = 'individualresp';
            }
            if ($outputtarget == 'html') {
                include('tabs.php');
            }

            // Print the main part of the page.
            // TODO provide option to select how many columns and/or responses per page.

            $groupname = get_string('group').': <strong>'.groups_get_group_name($currentgroupid).'</strong>';
            if ($currentgroupid == 0 ) {
                $groupname = get_string('allparticipants');
            }
            if ($byresponse) {
                $respinfo = '';
                $respinfo .= $questionnaire->renderer->box_start();
                $respinfo .= $questionnaire->renderer->help_icon('viewindividualresponse', 'questionnaire').'&nbsp;';
                $respinfo .= get_string('viewindividualresponse', 'questionnaire').' <strong> : '.$groupname.'</strong>';
                $respinfo .= $questionnaire->renderer->box_end();
                $questionnaire->page->add_to_page('respondentinfo', $respinfo);
            }
            if ($outputtarget == 'html') {
                $questionnaire->survey_results_navbar_alpha($rid, $currentgroupid, $cm, $byresponse);
            }
            if (!$byresponse) { // Show respondents individual responses.
                $questionnaire->view_response($rid, '', $resps, true, true, false, $currentgroupid, $outputtarget);
            }
            echo $questionnaire->renderer->header();
            echo $questionnaire->renderer->render($questionnaire->page);
            echo $questionnaire->renderer->footer($course);
        }
        break;
}

/**
 * Return a pdf object.
 * @return pdf
 */
function questionnaire_report_start_pdf() {
    global $CFG;

    require_once($CFG->libdir . '/pdflib.php');
    $pdf = new pdf();
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Moodle Questionnaire');
    $pdf->SetTitle('All responses');
    $pdf->setPrintHeader(false);
    // Set default monospaced font.
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

    // Set margins.
    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

    // Set auto page breaks.
    $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);

    // Set image scale factor.
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
    // Set background color for headings.
    $pdf->SetFillColor(238, 238, 238);
    $pdf->AddPage('L');
    return $pdf;
}
