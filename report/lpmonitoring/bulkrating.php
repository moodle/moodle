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
 * This page allows teachers to rate all students in template.
 *
 * @package    report_lpmonitoring
 * @copyright  2019 Université de Montréal
 * @author     Issam Taboubi <issam.taboubi@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
global $USER;

require_login();
\core_competency\api::require_enabled();

$templateid = optional_param('templateid', null, PARAM_INT);
$pagecontextid = required_param('pagecontextid', PARAM_INT);
$context = context::instance_by_id($pagecontextid);
$urlparams = ['pagecontextid' => $pagecontextid];
if (!empty($templateid)) {
    $template = \core_competency\api::read_template($templateid);
}

$url = new moodle_url('/report/lpmonitoring/bulkrating.php', $urlparams);

$title = get_string('bulkdefaultrating', 'report_lpmonitoring');

if ($context->contextlevel == CONTEXT_SYSTEM) {
    $heading = $SITE->fullname;
} else if ($context->contextlevel == CONTEXT_COURSECAT) {
    $heading = $context->get_context_name();
} else {
    throw new coding_exception('Unexpected context!');
}

$PAGE->set_context($context);
$PAGE->set_url($url);
$PAGE->set_title($title);
$PAGE->set_heading($heading);
$PAGE->set_pagelayout('admin');

$templateselector = $PAGE->get_renderer('report_lpmonitoring');
$bulkratingrenderer = $PAGE->get_renderer('report_lpmonitoring');
echo $templateselector->header();
echo $templateselector->heading($title);
$selctorpage = new \report_lpmonitoring\output\template_selector($context, $templateid);
echo $templateselector->render($selctorpage);
if (!empty($templateid)) {
    $bulkrating = new \report_lpmonitoring\output\bulkrating($templateid);
    echo $bulkratingrenderer->render($bulkrating);
}

echo $templateselector->footer();
