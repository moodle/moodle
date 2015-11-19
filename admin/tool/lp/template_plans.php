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

$template = \tool_lp\api::read_template($id);
$context = $template->get_context();
require_capability('tool/lp:templatemanage', $context);

// Set up the page.
$url = new moodle_url('/admin/tool/lp/template_plans.php', array(
    'id' => $id,
    'pagecontextid' => $pagecontextid
));
list($title, $subtitle) = \tool_lp\page_helper::setup_for_template($pagecontextid, $url, $template, get_string('userplans', 'tool_lp'));

// Display the page.
$output = $PAGE->get_renderer('tool_lp');
echo $output->header();
echo $output->heading($title);
echo $output->heading($subtitle, 3);
$page = new \tool_lp\output\template_plans_page($template, $url);
echo $output->render($page);
echo $output->footer();
