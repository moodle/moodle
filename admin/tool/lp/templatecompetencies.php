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

$template = \tool_lp\api::read_template($templateid);

admin_externalpage_setup('toollplearningplans');

// Set up the page.
$url = new moodle_url('/admin/tool/lp/templatecompetencies.php', array('templateid' => $template->get_id()));
$title = get_string('templatecompetencies', 'tool_lp');
$templatename = format_text($template->get_shortname());
$PAGE->set_url($url);
$PAGE->set_title($title);
$PAGE->set_heading($templatename);
$PAGE->navbar->add($templatename, $url);

// Display the page.
$output = $PAGE->get_renderer('tool_lp');
echo $output->header();
echo $output->heading($title);
$page = new \tool_lp\output\template_competencies_page($template->get_id());
echo $output->render($page);
echo $output->footer();
