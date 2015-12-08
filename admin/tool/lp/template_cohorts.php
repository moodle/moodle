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
 * List cohorts linked to a template.
 *
 * @package    tool_lp
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/../../../config.php');

$id = required_param('id', PARAM_INT);
$pagecontextid = required_param('pagecontextid', PARAM_INT);  // Reference to the context we came from.

require_login(0, false);

$template = \tool_lp\api::read_template($id);
$context = $template->get_context();
require_capability('tool/lp:templatemanage', $context);

// Set up the page.
$url = new moodle_url('/admin/tool/lp/template_cohorts.php', array(
    'id' => $id,
    'pagecontextid' => $pagecontextid
));
list($title, $subtitle) = \tool_lp\page_helper::setup_for_template($pagecontextid, $url, $template,
    get_string('cohortssyncedtotemplate', 'tool_lp'));

// Remove cohort.
if (($removecohort = optional_param('removecohort', false, PARAM_INT)) !== false && confirm_sesskey()) {
    \tool_lp\api::delete_template_cohort($template, $removecohort);
}

// Capture the form submission.
$form = new \tool_lp\form\template_cohorts($url->out(false), array('pagecontextid' => $pagecontextid));
if (($data = $form->get_data()) && !empty($data->cohorts)) {
    $i = 0;
    foreach ($data->cohorts as $cohortid) {

        // Create the template/cohort relationship.
        $relation = \tool_lp\api::create_template_cohort($template, $cohortid);

        // Create a plan for each member if template visible.
        if ($template->get_visible()) {
            $i += \tool_lp\api::create_plans_from_template_cohort($template, $cohortid);
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
if ($template->get_visible() == false) {
    // Display message to prevent that cohort will not be synchronzed if the template is hidden.
    echo $output->notify_message(get_string('templatecohortpagemessage', 'tool_lp'));
}
echo $form->display();
$page = new \tool_lp\output\template_cohorts_page($template, $url);
echo $output->render($page);
echo $output->footer();
