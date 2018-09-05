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
 * List plans derived from the template.
 *
 * @package    tool_lp
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/../../../config.php');

$id = required_param('id', PARAM_INT);
$pagecontextid = required_param('pagecontextid', PARAM_INT);  // Reference to the context we came from.

require_login(0, false);
\core_competency\api::require_enabled();

$template = \core_competency\api::read_template($id);
$context = $template->get_context();
$canreadtemplate = $template->can_read();
$canmanagetemplate = $template->can_manage();
if (!$canreadtemplate) {
    throw new required_capability_exception($context, 'moodle/competency:templateview', 'nopermissions', '');
}

// Set up the page.
$url = new moodle_url('/admin/tool/lp/template_plans.php', array(
    'id' => $id,
    'pagecontextid' => $pagecontextid
));
list($title, $subtitle) = \tool_lp\page_helper::setup_for_template($pagecontextid, $url, $template,
    get_string('userplans', 'core_competency'));

// Capture the form submission.
$form = new \tool_lp\form\template_plans($url->out(false));
if ($canmanagetemplate && ($data = $form->get_data()) && !empty($data->users)) {
    $i = 0;
    foreach ($data->users as $userid) {
        $result = \core_competency\api::create_plan_from_template($template->get('id'), $userid);
        if ($result) {
            $i++;
        }
    }
    if ($i == 0) {
        $notification = get_string('noplanswerecreated', 'tool_lp');
    } else if ($i == 1) {
        $notification = get_string('oneplanwascreated', 'tool_lp');
    } else {
        $notification = get_string('aplanswerecreated', 'tool_lp', $i);
    }
    redirect($url, $notification);
}

// Display the page.
$output = $PAGE->get_renderer('tool_lp');
echo $output->header();
echo $output->heading($title);
echo $output->heading($subtitle, 3);

// Do not display form when the template is hidden.
if ($canmanagetemplate) {
    if (!$template->get('visible')) {
        // Display message that plan can not be created if the template is hidden.
        echo $output->notify_message(get_string('cannotcreateuserplanswhentemplatehidden', 'tool_lp'));
    } else if ($template->get('duedate') > 0 && $template->get('duedate') < time() + 900) {
        // Prevent the user from creating plans when the due date is passed, or in less than 15 minutes.
        echo $output->notify_message(get_string('cannotcreateuserplanswhentemplateduedateispassed', 'tool_lp'));
    } else {
        echo $form->display();
    }
}

$page = new \tool_lp\output\template_plans_page($template, $url);
echo $output->render($page);
echo $output->footer();
