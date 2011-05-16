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
 * Returns an array of reports to which are currently readable.
 * @package   scorm_basic
 * @author    Ankit Kumar Agarwal
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

function scorm_report_list($context) {
    global $DB;
    static $reportlist = null;
    if (!is_null($reportlist)) {
        return $reportlist;
    }
    $reportdirs = get_plugin_list('scorm');
    $reportcaps = array();
    foreach ($reportdirs as $reportname => $notused) {
        $pluginfile = 'report/'.$reportname.'/report.php';
        if (is_readable($pluginfile)) {
            include_once($pluginfile);
            $reportclassname = "scorm_{$reportname}_report";
            if (class_exists($reportclassname)) {
                $reportcaps[] = $reportname;
            }
        }
    }
    return $reportcaps;
}
