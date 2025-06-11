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
 * Configure colors for scales values.
 *
 * @package    report_lpmonitoring
 * @author     Issam Taboubi <issam.taboubi@umontreal.ca>
 * @copyright  2016 Université de Montréal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

$pagecontextid = required_param('pagecontextid', PARAM_INT);
$context = context::instance_by_id($pagecontextid);

require_login();
\core_competency\api::require_enabled();

if (!\core_competency\competency_framework::can_manage_context($context)) {
    throw new required_capability_exception($context, 'moodle/competency:competencymanage', 'nopermissions', '');
}
$urlparams = ['pagecontextid' => $pagecontextid];

$url = new moodle_url('/report/lpmonitoring/scalecolorconfiguration.php', $urlparams);
$title = get_string('colorconfiguration', 'report_lpmonitoring');

if ($context->contextlevel == CONTEXT_SYSTEM) {
    $heading = $SITE->fullname;
} else if ($context->contextlevel == CONTEXT_COURSECAT) {
    $heading = $context->get_context_name();
} else {
    throw new coding_exception('Unexpected context!');
}

// Set the cess.
$PAGE->set_context($context);
$PAGE->set_url($url);
$PAGE->set_title($title);
$PAGE->set_heading($heading);
$PAGE->set_pagelayout('admin');

$output = $PAGE->get_renderer('report_lpmonitoring');

echo $output->header();
echo $output->heading($title);

$page = new \report_lpmonitoring\output\scalecolorconfiguration_page($context);
echo $output->render($page);
echo $output->footer();
