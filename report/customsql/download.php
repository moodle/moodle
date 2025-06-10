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
 * Script to download the CSV version of a SQL report.
 *
 * @package report_customsql
 * @copyright 2009 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../config.php');
require_once(dirname(__FILE__) . '/locallib.php');
require_once($CFG->libdir . '/filelib.php');

$id = required_param('id', PARAM_INT);
$csvtimestamp = required_param('timestamp', PARAM_INT);

$report = $DB->get_record('report_customsql_queries', array('id' => $id));
if (!$report) {
    print_error('invalidreportid', 'report_customsql', report_customsql_url('index.php'), $id);
}

require_login();
$context = context_system::instance();
if (!empty($report->capability)) {
    require_capability($report->capability, $context);
}

list($csvfilename) = report_customsql_csv_filename($report, $csvtimestamp);
if (!is_readable($csvfilename)) {
    print_error('unknowndownloadfile', 'report_customsql',
                report_customsql_url('view.php?id=' . $id));
}

send_file($csvfilename, 'report.csv', 'default' , 0, false, true, 'text/csv; charset=UTF-8');
