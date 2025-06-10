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
 * This page shows the list of queries in a category, with edit icons, an add new button
 * if you have the report/lsusql:definequeries capability
 *
 * @package report_lsusql
 * @copyright 2021 The Open University
 * @copyright 2022 Louisiana State University
 * @copyright 2022 Robert Russo
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../config.php');
require_once(dirname(__FILE__) . '/locallib.php');
require_once($CFG->libdir . '/adminlib.php');

// Start the page.
admin_externalpage_setup('report_lsusql');
$context = context_system::instance();
require_capability('report/lsusql:view', $context);

$categoryid = required_param('id', PARAM_INT);
$record = $DB->get_record('report_lsusql_categories', ['id' => $categoryid], '*', MUST_EXIST);
$queries = $DB->get_records('report_lsusql_queries', ['categoryid' => $categoryid], 'displayname, id');

$category = new \report_lsusql\local\category($record);
$category->load_queries_data($queries);
$widget = new \report_lsusql\output\category($category, $context);

$PAGE->set_title(format_string($category->get_name()));
$PAGE->navbar->add(format_string($category->get_name()));
$output = $PAGE->get_renderer('report_lsusql');

echo $OUTPUT->header();

echo $output->render($widget);

echo $OUTPUT->footer();
