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
 * List learningpaths linked to a template.
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
$duedatereached = $template->get('duedate') > 0 && $template->get('duedate') < time();

if (!$canreadtemplate) {
    throw new required_capability_exception($context, 'moodle/competency:templateview', 'nopermissions', '');
}

// Get my company id.
$companyid = \iomad::get_my_companyid(context_system::instance(), false);

// Set up the page.
$url = new moodle_url('/admin/tool/lp/template_learningpaths.php', array(
    'id' => $id,
    'pagecontextid' => $pagecontextid
));
list($title, $subtitle) = \tool_lp\page_helper::setup_for_template($pagecontextid, $url, $template,
    get_string('learningpathssyncedtotemplate', 'block_iomad_learningpath'));

// Remove Learning path.
if ($canmanagetemplate && ($removelearningpath = optional_param('removelearningpath', false, PARAM_INT)) !== false && confirm_sesskey()) {
    \core_competency\api::delete_template_learningpath($template, $removelearningpath);
}

// Capture the form submission.
$existinglearningpathsql =
    'SELECT c.id
       FROM {' . \core_competency\template_learningpath::TABLE . '} tc
       JOIN {iomad_learningpath} c ON c.id = tc.learningpathid
      WHERE tc.templateid = :templateid
       AND c.company = :companyid';

$existinglearningpaths = $DB->get_records_sql_menu($existinglearningpathsql, ['templateid' => $template->get('id'), 'companyid' => $companyid]);

$form = new \tool_lp\form\template_learningpaths($url->out(false), [
    'pagecontextid' => $pagecontextid,
    'excludelearningpaths' => array_keys($existinglearningpaths),
]);

if ($canmanagetemplate && ($data = $form->get_data()) && !empty($data->learningpaths)) {
    $maxtocreate = 50;
    $maxreached = false;
    $i = 0;
    foreach ($data->learningpaths as $learningpathid) {

        // Create the template/learningpath relationship.
        $relation = \core_competency\api::create_template_learningpath($template, $learningpathid);

        // Create a plan for each member if template visible, and the due date is not reached, and we didn't reach our limit yet.
        if ($template->get('visible') && $i < $maxtocreate && !$duedatereached) {

            // Only create a few plans right now.
            $tocreate = \core_competency\template_learningpath::get_missing_plans($template->get('id'), $learningpathid);
            if ($i + count($tocreate) <= $maxtocreate) {
                $i += \core_competency\api::create_plans_from_template_learningpath($template, $learningpathid);
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
    if ($template->get('visible') == false) {
        // Display message to prevent that learningpath will not be synchronzed if the template is hidden.
        echo $output->notify_message(get_string('templatelearningpathnotsyncedwhilehidden', 'block_iomad_learningpath'));
    } else if ($duedatereached) {
        echo $output->notify_message(get_string('templatelearningpathnotsyncedwhileduedateispassed', 'block_iomad_learningpath'));
    }
    echo $form->display();
}

$page = new \tool_lp\output\template_learningpaths_page($template, $url);
echo $output->render($page);
echo $output->footer();
