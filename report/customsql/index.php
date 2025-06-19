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
 * Users with the report/customsql:definequeries capability can enter custom
 * SQL SELECT statements. If they have report/customsql:managecategories
 * capability can create custom categories for the sql reports.
 * Other users with the moodle/site:viewreports capability
 * can see the list of available queries and run them. Reports are displayed as
 * a table. Every data value is a string, and field names come from the database
 * results set.
 *
 * This page shows the list of categorised queries, with edit icons, an add new button
 * if you have the report/customsql:definequeries capability, and a manage categories button
 * ff you have report/customsql:managecategories capability
 *
 * @package report_customsql
 * @copyright 2009 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../config.php');
require_once(dirname(__FILE__) . '/locallib.php');
require_once($CFG->libdir . '/adminlib.php');

// Start the page.
admin_externalpage_setup('report_customsql');
$context = context_system::instance();
require_capability('report/customsql:view', $context);

$categories = $DB->get_records('report_customsql_categories', null, 'name ASC');
$showcat = optional_param('showcat', 0, PARAM_INT);
$hidecat = optional_param('hidecat', 0, PARAM_INT);
if (!$showcat && count($categories) == 1) {
    $showcat = reset($categories)->id;
}

echo $OUTPUT->header();

$expandcollapsealllink = html_writer::link('#', get_string('expandall'), [
        'class' => 'csql_expandcollapseall',
        'data-expandalltext' => get_string('expandall'),
        'data-collapsealltext' => get_string('collapseall')]);
$expandcollapsealllink = html_writer::div($expandcollapsealllink, 'csql_expandcollapseallcontainer');
echo $expandcollapsealllink;

foreach ($categories as $category) {
    // Are we showing this cat? Default is hidden.
    $show = $category->id == $showcat && $category->id != $hidecat ? 'shown' : 'hidden';

    echo html_writer::start_tag('div', array('class' => 'csql_category csql_category' . $show));
    if ($category->id == $showcat) {
        $params = array('hidecat' => $category->id);
    } else {
        $params = array('showcat' => $category->id);
    }
    $linkhref = new moodle_url('/report/customsql/index.php', $params);
    $link = html_writer::link($linkhref, $category->name, array('class' => 'categoryname'));

    $manualreports = report_customsql_get_reports_for($category->id, 'manual');
    $dailyreports = report_customsql_get_reports_for($category->id, 'daily');
    $weeklyreports = report_customsql_get_reports_for($category->id, 'weekly');
    $monthlyreports = report_customsql_get_reports_for($category->id, 'monthly');

    // Category content.
    $cc = new stdClass();
    $cc->manual = count($manualreports);
    $cc->daily = count($dailyreports);
    $cc->weekly = count($weeklyreports);
    $cc->monthly = count($monthlyreports);
    $reportcounts = get_string('categorycontent', 'report_customsql', $cc);

    $reportcounts = html_writer::tag('span', $reportcounts, array('class' => 'reportcounts'));
    echo $OUTPUT->heading($link . ' ' . $reportcounts);

    echo html_writer::start_tag('div', array('class' => 'csql_category_reports'));
    if (empty($manualreports) && empty($dailyreports) && empty($weeklyreports) && empty($monthlyreports)) {
        echo $OUTPUT->heading(get_string('availablereports', 'report_customsql'), 3).
        html_writer::tag('p', get_string('noreportsavailable', 'report_customsql'));
    } else {
        report_customsql_print_reports_for($manualreports, 'manual');
        report_customsql_print_reports_for($dailyreports, 'daily');
        report_customsql_print_reports_for($weeklyreports, 'weekly');
        report_customsql_print_reports_for($monthlyreports, 'monthly');
    }
    echo html_writer::end_tag('div');
    echo html_writer::end_tag('div');
}

if (count($categories) >= 5) {
    // If there are many categores, show the link again.
    echo $expandcollapsealllink;
}

if (has_capability('report/customsql:definequeries', $context)) {
    echo $OUTPUT->single_button(report_customsql_url('edit.php'),
            get_string('addreport', 'report_customsql'));
}
if (has_capability('report/customsql:managecategories', $context)) {
    echo html_writer::empty_tag('br');
    echo $OUTPUT->single_button(report_customsql_url('manage.php'),
            get_string('managecategories', 'report_customsql'));
}

// Initialise the expand/collapse JavaScript.
$PAGE->requires->js_call_amd('report_customsql/reportcategories', 'init');

echo $OUTPUT->footer();
