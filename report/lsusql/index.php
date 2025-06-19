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
 * Custom SQL report.
 *
 * Users with the report/lsusql:definequeries capability can enter custom
 * SQL SELECT statements. If they have report/lsusql:managecategories
 * capability can create custom categories for the sql reports.
 * Other users with the moodle/site:viewreports capability
 * can see the list of available queries and run them. Reports are displayed as
 * a table. Every data value is a string, and field names come from the database
 * results set.
 *
 * This page shows the list of categorised queries, with edit icons, an add new button
 * if you have the report/lsusql:definequeries capability, and a manage categories button
 * ff you have report/lsusql:managecategories capability
 *
 * @package report_lsusql
 * @copyright 2009 The Open University
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

$categories = $DB->get_records('report_lsusql_categories', null, 'name, id');
$queries = $DB->get_records('report_lsusql_queries', null, 'displayname, id');
$showcat = optional_param('showcat', 0, PARAM_INT);
$hidecat = optional_param('hidecat', 0, PARAM_INT);
$returnurl = report_lsusql_url('index.php');

$widget = new \report_lsusql\output\index_page($categories, $queries, $context, $returnurl, $showcat, $hidecat);
$output = $PAGE->get_renderer('report_lsusql');

echo $OUTPUT->header();

echo $output->render($widget);

echo $OUTPUT->footer();
