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
// require_once(dirname(__FILE__) . '/locallib.php');
require_once($CFG->libdir . '/filelib.php');


$courseid = required_param('id', PARAM_INT);
$timestamp = required_param('timestamp', PARAM_RAW);

$downloadurl = $CFG->wwwroot.'/report/coursesize/download.php';

$path = 'temp/admin_coursesize_report/'.$courseid;
$csvfilename = $CFG->dataroot . '/' . $path . '/' . 
    core_date::strftime('%Y%m%d-%H%M%S', (int)$timestamp) . '.csv';
        
if (!$courseid) {
    moodle_exception('invalid course id', 'report_coursesize', 'blip blip', 'Kabloo-eeeee');
}

require_login();
$context = context_system::instance();

if (!is_readable($csvfilename)) {
    moodle_exception('unknowndownloadfile', 'report_coursesize', $downloadurl);
}

send_file($csvfilename, $courseid.'_'.$timestamp.'_report.csv', 'default' , 0, false, true, 'text/csv; charset=UTF-8');
