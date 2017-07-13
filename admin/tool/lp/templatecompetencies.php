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
 * This page lets users to manage template competencies.
 *
 * @package    tool_lp
 * @copyright  2015 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir.'/adminlib.php');

$templateid = required_param('templateid', PARAM_INT);
$pagecontextid = required_param('pagecontextid', PARAM_INT);  // Reference to the context we came from.

require_login(0, false);
\core_competency\api::require_enabled();

$pagecontext = context::instance_by_id($pagecontextid);
$template = \core_competency\api::read_template($templateid);
$context = $template->get_context();
if (!$template->can_read()) {
    throw new required_capability_exception($context, 'moodle/competency:templateview', 'nopermissions', '');
}

// Trigger a template viewed event.
\core_competency\api::template_viewed($template);

// Set up the page.
$url = new moodle_url('/admin/tool/lp/templatecompetencies.php', array('templateid' => $template->get('id'),
    'pagecontextid' => $pagecontextid));
list($title, $subtitle) = \tool_lp\page_helper::setup_for_template($pagecontextid, $url, $template);

// Display the page.
$output = $PAGE->get_renderer('tool_lp');
echo $output->header();
$page = new \tool_lp\output\template_competencies_page($template, $pagecontext);
echo $output->render($page);
echo $output->footer();
