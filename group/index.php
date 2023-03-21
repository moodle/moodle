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
 * The main group management user interface.
 *
 * @copyright 2006 The Open University, N.D.Freear AT open.ac.uk, J.White AT open.ac.uk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package   core_group
 */
require_once('../config.php');
require_once('lib.php');

$courseid = required_param('id', PARAM_INT);
$groupid  = optional_param('group', false, PARAM_INT);
$userid   = optional_param('user', false, PARAM_INT);
$action   = groups_param_action();
// Support either single group= parameter, or array groups[]
if ($groupid) {
    $groupids = array($groupid);
} else {
    $groupids = optional_param_array('groups', array(), PARAM_INT);
}
$singlegroup = (count($groupids) == 1);

$returnurl = $CFG->wwwroot.'/group/index.php?id='.$courseid;

// Get the course information so we can print the header and
// check the course id is valid

$course = $DB->get_record('course', array('id'=>$courseid), '*', MUST_EXIST);

$url = new moodle_url('/group/index.php', array('id'=>$courseid));
navigation_node::override_active_url($url);
if ($userid) {
    $url->param('user', $userid);
}
if ($groupid) {
    $url->param('group', $groupid);
}
$PAGE->set_url($url);

// Make sure that the user has permissions to manage groups.
require_login($course);

$context = context_course::instance($course->id);
require_capability('moodle/course:managegroups', $context);

$PAGE->requires->js('/group/clientlib.js', true);
$PAGE->requires->js('/group/module.js', true);

// Check for multiple/no group errors
if (!$singlegroup) {
    switch($action) {
        case 'ajax_getmembersingroup':
        case 'showgroupsettingsform':
        case 'showaddmembersform':
        case 'updatemembers':
            print_error('errorselectone', 'group', $returnurl);
    }
}

switch ($action) {
    case false: //OK, display form.
        break;

    case 'ajax_getmembersingroup':
        $roles = array();

        $userfieldsapi = \core_user\fields::for_identity($context)->with_userpic();
        [
            'selects' => $userfieldsselects,
            'joins' => $userfieldsjoin,
            'params' => $userfieldsparams
        ] = (array)$userfieldsapi->get_sql('u', true, '', '', false);
        $extrafields = $userfieldsapi->get_required_fields([\core_user\fields::PURPOSE_IDENTITY]);
        if ($groupmemberroles = groups_get_members_by_role($groupids[0], $courseid,
                'u.id, ' . $userfieldsselects, null, '', $userfieldsparams, $userfieldsjoin)) {

            $viewfullnames = has_capability('moodle/site:viewfullnames', $context);

            foreach($groupmemberroles as $roleid=>$roledata) {
                $shortroledata = new stdClass();
                $shortroledata->name = html_entity_decode($roledata->name, ENT_QUOTES, 'UTF-8');
                $shortroledata->users = array();
                foreach($roledata->users as $member) {
                    $shortmember = new stdClass();
                    $shortmember->id = $member->id;
                    $shortmember->name = fullname($member, $viewfullnames);
                    if ($extrafields) {
                        $extrafieldsdisplay = [];
                        foreach ($extrafields as $field) {
                            // No escaping here, handled client side in response to AJAX request.
                            $extrafieldsdisplay[] = $member->{$field};
                        }
                        $shortmember->name .= ' (' . implode(', ', $extrafieldsdisplay) . ')';
                    }

                    $shortroledata->users[] = $shortmember;
                }
                $roles[] = $shortroledata;
            }
        }
        echo json_encode($roles);
        die;  // Client side JavaScript takes it from here.

    case 'deletegroup':
        if (count($groupids) == 0) {
            print_error('errorselectsome','group',$returnurl);
        }
        $groupidlist = implode(',', $groupids);
        redirect(new moodle_url('/group/delete.php', array('courseid'=>$courseid, 'groups'=>$groupidlist)));
        break;

    case 'showcreateorphangroupform':
        redirect(new moodle_url('/group/group.php', array('courseid'=>$courseid)));
        break;

    case 'showautocreategroupsform':
        redirect(new moodle_url('/group/autogroup.php', array('courseid'=>$courseid)));
        break;

    case 'showimportgroups':
        redirect(new moodle_url('/group/import.php', array('id'=>$courseid)));
        break;

    case 'showgroupsettingsform':
        redirect(new moodle_url('/group/group.php', array('courseid'=>$courseid, 'id'=>$groupids[0])));
        break;

    case 'updategroups': //Currently reloading.
        break;

    case 'removemembers':
        break;

    case 'showaddmembersform':
        redirect(new moodle_url('/group/members.php', array('group'=>$groupids[0])));
        break;

    case 'updatemembers': //Currently reloading.
        break;

    default: //ERROR.
        print_error('unknowaction', '', $returnurl);
        break;
}

// Print the page and form
$strgroups = get_string('groups');
$strparticipants = get_string('participants');

/// Print header
$PAGE->set_title($strgroups);
$PAGE->set_heading($course->fullname);
$PAGE->set_pagelayout('standard');
echo $OUTPUT->header();

echo $OUTPUT->render_participants_tertiary_nav($course);
echo $OUTPUT->heading(format_string($course->shortname, true, array('context' => $context)) .' '.$strgroups, 3);

$groups = groups_get_all_groups($courseid);
$selectedname = null;
$preventgroupremoval = array();

// Get list of groups to render.
$groupoptions = array();
if ($groups) {
    foreach ($groups as $group) {
        $selected = false;
        $usercount = $DB->count_records('groups_members', array('groupid' => $group->id));
        $groupname = format_string($group->name, true, ['context' => $context, 'escape' => false]) . ' (' . $usercount . ')';
        if (in_array($group->id, $groupids)) {
            $selected = true;
            if ($singlegroup) {
                // Only keep selected name if there is one group selected.
                $selectedname = $groupname;
            }
        }
        if (!empty($group->idnumber) && !has_capability('moodle/course:changeidnumber', $context)) {
            $preventgroupremoval[$group->id] = true;
        }

        $groupoptions[] = (object) [
            'value' => $group->id,
            'selected' => $selected,
            'text' => s($groupname)
        ];
    }
}

// Get list of group members to render if there is a single selected group.
$members = array();
if ($singlegroup) {
    $userfieldsapi = \core_user\fields::for_identity($context)->with_userpic();
    [
        'selects' => $userfieldsselects,
        'joins' => $userfieldsjoin,
        'params' => $userfieldsparams
    ] = (array)$userfieldsapi->get_sql('u', true, '', '', false);
    $extrafields = $userfieldsapi->get_required_fields([\core_user\fields::PURPOSE_IDENTITY]);
    if ($groupmemberroles = groups_get_members_by_role(reset($groupids), $courseid,
            'u.id, ' . $userfieldsselects, null, '', $userfieldsparams, $userfieldsjoin)) {

        $viewfullnames = has_capability('moodle/site:viewfullnames', $context);

        foreach ($groupmemberroles as $roleid => $roledata) {
            $users = array();
            foreach ($roledata->users as $member) {
                $shortmember = new stdClass();
                $shortmember->value = $member->id;
                $shortmember->text = fullname($member, $viewfullnames);
                if ($extrafields) {
                    $extrafieldsdisplay = [];
                    foreach ($extrafields as $field) {
                        $extrafieldsdisplay[] = s($member->{$field});
                    }
                    $shortmember->text .= ' (' . implode(', ', $extrafieldsdisplay) . ')';
                }

                $users[] = $shortmember;
            }

            $members[] = (object)[
                'role' => html_entity_decode($roledata->name, ENT_QUOTES, 'UTF-8'),
                'rolemembers' => $users
            ];
        }
    }
}

$disableaddedit = !$singlegroup;
$disabledelete = !empty($groupids);
$renderable = new \core_group\output\index_page($courseid, $groupoptions, $selectedname, $members, $disableaddedit, $disabledelete,
        $preventgroupremoval);
$output = $PAGE->get_renderer('core_group');
echo $output->render($renderable);

echo $OUTPUT->footer();

/**
 * Returns the first button action with the given prefix, taken from
 * POST or GET, otherwise returns false.
 * @see /lib/moodlelib.php function optional_param().
 * @param string $prefix 'act_' as in 'action'.
 * @return string The action without the prefix, or false if no action found.
 */
function groups_param_action($prefix = 'act_') {
    $action = false;
//($_SERVER['QUERY_STRING'] && preg_match("/$prefix(.+?)=(.+)/", $_SERVER['QUERY_STRING'], $matches)) { //b_(.*?)[&;]{0,1}/

    if ($_POST) {
        $form_vars = $_POST;
    }
    elseif ($_GET) {
        $form_vars = $_GET;
    }
    if ($form_vars) {
        foreach ($form_vars as $key => $value) {
            if (preg_match("/$prefix(.+)/", $key, $matches)) {
                $action = $matches[1];
                break;
            }
        }
    }
    if ($action && !preg_match('/^\w+$/', $action)) {
        $action = false;
        print_error('unknowaction');
    }
    ///if (debugging()) echo 'Debug: '.$action;
    return $action;
}
