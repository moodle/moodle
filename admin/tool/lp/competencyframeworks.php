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
 * This page lets users to manage site wide competencies.
 *
 * @package    tool_lp
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir.'/adminlib.php');

$pagecontextid = required_param('pagecontextid', PARAM_INT);
$context = context::instance_by_id($pagecontextid);

$url = new moodle_url("/admin/tool/lp/competencyframeworks.php");
$url->param('pagecontextid', $pagecontextid);

require_login(null, false);
\core_competency\api::require_enabled();

if (!\core_competency\competency_framework::can_read_context($context)) {
    throw new required_capability_exception($context, 'moodle/competency:competencyview', 'nopermissions', '');
}

$title = get_string('competencies', 'core_competency');
$pagetitle = get_string('competencyframeworks', 'tool_lp');

// Set up the page.
$PAGE->set_context($context);
$PAGE->set_pagelayout('admin');
$PAGE->set_url($url);

if ($context->contextlevel == CONTEXT_COURSECAT) {
    core_course_category::page_setup();
    // Set the competency frameworks node active in the settings navigation block.
    if ($competencyframeworksnode = $PAGE->settingsnav->find('competencyframeworks', navigation_node::TYPE_SETTING)) {
        $competencyframeworksnode->make_active();
    }
} else if ($context->contextlevel == CONTEXT_SYSTEM) {
    $PAGE->set_heading($SITE->fullname);
} else {
    $PAGE->set_heading($title);
}

$PAGE->set_title($title);
$output = $PAGE->get_renderer('tool_lp');
echo $output->header();
echo $output->heading($pagetitle, 2);

$page = new \tool_lp\output\manage_competency_frameworks_page($context);
echo $output->render($page);

echo $output->footer();
