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

// Set up the page.
if (empty($id)) {
    $pagetitle = get_string('addnewplan', 'tool_lp');
} else {
    $pagetitle = get_string('editplan', 'tool_lp');
}

// Default to the current user.
if (!$userid) {
    $userid = $USER->id;
}

$context = context_user::instance($userid);

$params = array('userid' => $userid);
if ($id) {
    $params['id'] = $id;
}

$url = new moodle_url("/admin/tool/lp/editplan.php", $params);
$PAGE->set_context($context);
$PAGE->set_url($url);
$PAGE->set_title($pagetitle);
$PAGE->set_pagelayout('admin');
$PAGE->set_heading($pagetitle);
$output = $PAGE->get_renderer('tool_lp');

$manageplans = has_capability('tool/lp:planmanage', $context);
$owncapabilities = array('tool/lp:plancreatedraft', 'tool/lp:planmanageown');
if ($USER->id === $userid && !has_any_capability($owncapabilities, $context) && !$manageplans) {
    throw new required_capability_exception($context, 'tool/lp:planmanageown', 'nopermissions', '');
} else if (!$manageplans) {
    throw new required_capability_exception($context, 'tool/lp:planmanage', 'nopermissions', '');
}

// Passing the templates list to the form.
$templates = array();
if ($manageplans) {
    $templates = \tool_lp\api::list_templates();
}

$customdata = array('id' => $id, 'userid' => $userid, 'templates' => $templates);
$form = new \tool_lp\form\plan(null, $customdata);
if ($form->is_cancelled()) {
    redirect(new moodle_url('/admin/tool/lp/plans.php?userid=' . $userid));
}

echo $output->header();
echo $output->heading($pagetitle);
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
