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
 * Report lsusql functions.
 *
 * @package    report_lsusql
 * @author     Jwalit Shah <jwalitshah@catalyst-au.net>
 * @copyright  2021 Catalyst IT
 * @copyright  2022 Louisiana State University
 * @copyright  2022 Robert Russo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Called by pluginfile, to download user generated reports via selected dataformat.
 * Generated reports can also be downloaded via webservice/pluginfile.
 *
 * Example url for download:
 * /pluginfile.php/<contextid>/report_lsusql/download/<reportid>/?dataformat=csv&parameter1=value1&parameter2=value2
 * Example url for download via WS:
 * /webservice/pluginfile.php/<contextid>/report_lsusql/download/<reportid>/?token=<wstoken>&dataformat=csv&parameter1=value1&parameter2=value2
 *
 * Exits if the required permissions are not satisfied.
 *
 * @param stdClass $course course object
 * @param stdClass $cm
 * @param stdClass $context context object
 * @param string $filearea file area
 * @param array $args extra arguments
 * @param bool $forcedownload whether or not force download
 * @param array $options additional options affecting the file serving
 * @return bool false if file not found, does not return if found - just send the file
 */
function report_lsusql_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = array()) {
    global $CFG, $DB, $USER;

    require_once(dirname(__FILE__) . '/locallib.php');

    if ($context->contextlevel != CONTEXT_SYSTEM) {
        return false;
    }

    if ($filearea != 'download') {
        return false;
    }

    $id = (int)array_shift($args);
    $dataformat = required_param('dataformat', PARAM_ALPHA);

    $report = $DB->get_record('report_lsusql_queries', array('id' => $id));
    if (!$report) {
        throw new \moodle_exception('invalidreportid', 'report_lsusql',
                report_lsusql_url('index.php'), $id);
    }

    require_login();
    $context = context_system::instance();
    $permittedusers = !empty($report->userlimit) ? array_map('trim', explode(',', $report->userlimit)) : array($USER->username);

    // Limit webservice and direct downloading of file to those EXPLICITLY allowed to download.
    $url  = ($_SERVER['REQUEST_URI']);
    $ws   = '/webservice/ixs';
    preg_match_all($ws, $url, $wsmatches, PREG_SET_ORDER, 0);
    $isws = count($wsmatches) > 0 ? true : false;

    if (!empty($report->capability)) {
        // The normal requirement.
        require_capability($report->capability, $context);

        // Make sure we have permissions to DL the report.
        if ($isws) {
            // Only allow priveleged named users to download in a webservice context.
            $alloweduser = $report->capability == 'report/lsusql:view'
                       ? has_capability($report->capability, $context)
                           && in_array ($USER->username, $permittedusers)
                       : false;
        } else {
            // Allow all priveleged + named users to download.
            $alloweduser = $report->capability == 'report/lsusql:view'
                       ? (has_capability($report->capability, $context)
                           && in_array ($USER->username, $permittedusers))
                           || is_siteadmin($USER->id)
                       : has_capability($report->capability, $context)
                           || is_siteadmin($USER->id);
        }
        // If the user cannot download, throw an exception.
        if (!$alloweduser) {
        throw new \moodle_exception('noaccess', 'report_lsusql',
                report_lsusql_url('index.php'), $id);
        }
    }

    $queryparams = report_lsusql_get_query_placeholders_and_field_names($report->querysql);
    // Get any query param values that are given in the URL.
    $paramvalues = [];
    foreach ($queryparams as $queryparam => $notused) {
        $value = optional_param($queryparam, null, PARAM_RAW);
        if ($value !== null && $value !== '') {
            $paramvalues[$queryparam] = $value;
        }
        $report->queryparams = serialize($paramvalues);
    }

    // Check timestamp param.
    $csvtimestamp = optional_param('timestamp', null, PARAM_INT);
    if ($csvtimestamp === null) {
        $runtime = time();
        if ($report->runable !== 'manual') {
            $runtime = $report->lastrun;
        }
        $csvtimestamp = \report_lsusql_generate_csv($report, $runtime, $isws);
    }
    list($csvfilename) = report_lsusql_csv_filename($report, $csvtimestamp);

    $handle = fopen($csvfilename, 'r');
    if ($handle === false) {
        throw new \moodle_exception('unknowndownloadfile', 'report_lsusql',
                report_lsusql_url('view.php?id=' . $id));
    }

    $fields = report_lsusql_read_csv_row($handle);

    $rows = new ArrayObject([]);
    while ($row = report_lsusql_read_csv_row($handle)) {
        $rows->append($row);
    }

    fclose($handle);

    $filename = clean_filename($report->displayname);

    $allowedformats = explode(',', get_config('report_lsusql', 'dataformats'));

    if (!in_array ($dataformat, $allowedformats)) {
        throw new \moodle_exception('invalidformat', 'report_lsusql',
                report_lsusql_url('index.php'), $id);
    }

    \core\dataformat::download_data($filename, $dataformat, $fields, $rows->getIterator(), function(array $row) use ($dataformat) {
        // HTML export content will need escaping.
        if (strcasecmp($dataformat, 'html') === 0) {
            $row = array_map(function($cell) {
                return s($cell);
            }, $row);
        }

        return $row;
    });
    die;
}
