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
\core_competency\api::require_enabled();

$template = \core_competency\api::read_template($id);
$context = $template->get_context();
$canreadtemplate = $template->can_read();
$canmanagetemplate = $template->can_manage();
$duedatereached = $template->get_duedate() > 0 && $template->get_duedate() < time();

if (!$canreadtemplate) {
    throw new required_capability_exception($context, 'moodle/competency:templateview', 'nopermissions', '');
}

// Set up the page.
$url = new moodle_url('/admin/tool/lp/template_cohorts.php', array(
    'id' => $id,
    'pagecontextid' => $pagecontextid
));
list($title, $subtitle) = \tool_lp\page_helper::setup_for_template($pagecontextid, $url, $template,
    get_string('cohortssyncedtotemplate', 'tool_lp'));

// Remove cohort.
if ($canmanagetemplate && ($removecohort = optional_param('removecohort', false, PARAM_INT)) !== false && confirm_sesskey()) {
    \core_competency\api::delete_template_cohort($template, $removecohort);
}

// Capture the form submission.
$form = new \tool_lp\form\template_cohorts($url->out(false), array('pagecontextid' => $pagecontextid));
if ($canmanagetemplate && ($data = $form->get_data()) && !empty($data->cohorts)) {
    $maxtocreate = 50;
    $maxreached = false;
    $i = 0;
    foreach ($data->cohorts as $cohortid) {

        // Create the template/cohort relationship.
        $relation = \core_competency\api::create_template_cohort($template, $cohortid);

        // Create a plan for each member if template visible, and the due date is not reached, and we didn't reach our limit yet.
        if ($template->get_visible() && $i < $maxtocreate && !$duedatereached) {

            // Only create a few plans right now.
            $tocreate = \core_competency\template_cohort::get_missing_plans($template->get_id(), $cohortid);
            if ($i + count($tocreate) <= $maxtocreate) {
                $i += \core_competency\api::create_plans_from_template_cohort($template, $cohortid);
            } else {
                $maxreached = true;
            }
        }
    }
    if ($i == 0) {
        $notification = get_string('noplanswerecreated', 'tool_lp');
    } else if ($i == 1) {
        $notification = get_string('oneplanwascreated', 'tool_lp');
    } else if ($maxreached) {
        $notification = get_string('aplanswerecreatedmoremayrequiresync', 'tool_lp', $i);
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
if ($canmanagetemplate) {
    if ($template->get_visible() == false) {
        // Display message to prevent that cohort will not be synchronzed if the template is hidden.
        echo $output->notify_message(get_string('templatecohortnotsyncedwhilehidden', 'tool_lp'));
    } else if ($duedatereached) {
        echo $output->notify_message(get_string('templatecohortnotsyncedwhileduedateispassed', 'tool_lp'));
    }
    echo $form->display();
}

$page = new \tool_lp\output\template_cohorts_page($template, $url);
echo $output->render($page);
echo $output->footer();
