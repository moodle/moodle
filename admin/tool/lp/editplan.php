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
 * Page to edit a plan.
 *
 * @package    tool_lp
 * @copyright  2015 David Monllao
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir.'/adminlib.php');

$userid = optional_param('userid', false, PARAM_INT);
$id = optional_param('id', false, PARAM_INT);

$url = new moodle_url('/admin/tool/lp/editplan.php', array('id' => $id, 'userid' => $userid));

$plan = null;
if (empty($id)) {
    $pagetitle = get_string('addnewplan', 'tool_lp');
    list($title, $subtitle, $returnurl) = \tool_lp\page_helper::setup_for_plan($userid, $url, null, $pagetitle);
} else {
    $plan = \tool_lp\api::read_plan($id);

    // The userid parameter must be the same as the owner of the plan.
    if ($userid != $plan->get_userid()) {
        throw new coding_exception('Inconsistency between the userid parameter and the userid of the plan');
    }

    $pagetitle = get_string('editplan', 'tool_lp');
    list($title, $subtitle, $returnurl) = \tool_lp\page_helper::setup_for_plan($userid, $url, $plan, $pagetitle);
}

$output = $PAGE->get_renderer('tool_lp');

// Custom data to pass to the form.
$customdata = array('userid' => $userid, 'context' => $PAGE->context);

// User can create plan if he can_manage_user with active/complete status
// or if he can_manage_user_draft with draft status.
$cancreate = \tool_lp\plan::can_manage_user_draft($userid) || \tool_lp\plan::can_manage_user($userid);

// If editing plan, check if user has permissions to edit it.
if ($plan != null) {
    if (!$plan->can_manage()) {
        throw new required_capability_exception($PAGE->context, 'tool/lp:planmanage', 'nopermissions', '');
    }
    if (!$plan->can_be_edited()) {
        throw new coding_exception('Completed plan can not be edited');
    }
    $customdata['plan'] = $plan;
} else if (!$cancreate) {
    throw new required_capability_exception($PAGE->context, 'tool/lp:planmanage', 'nopermissions', '');
}

$form = new \tool_lp\form\plan(null, $customdata);
if ($form->is_cancelled()) {
    redirect($returnurl);
}

echo $output->header();
echo $output->heading($title);
if (!empty($subtitle)) {
    echo $output->heading($subtitle, 3);
}

$data = $form->get_data();

if ($data) {
    $data->descriptionformat = $data->description['format'];
    $data->description = $data->description['text'];
    if (empty($data->id)) {
        require_sesskey();
        \tool_lp\api::create_plan($data);
        echo $output->notification(get_string('plancreated', 'tool_lp'), 'notifysuccess');
        echo $output->continue_button('/admin/tool/lp/plans.php?userid=' . $userid);
    } else {
        require_sesskey();
        \tool_lp\api::update_plan($data);
        echo $output->notification(get_string('planupdated', 'tool_lp'), 'notifysuccess');
        echo $output->continue_button('/admin/tool/lp/plans.php?userid=' . $userid);
    }
} else {
    $form->display();
}

echo $output->footer();
