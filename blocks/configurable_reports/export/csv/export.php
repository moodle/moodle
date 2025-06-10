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
 * Configurable Reports a Moodle block for creating customizable reports
 *
 * @copyright  2020 Juan Leyva <juan@moodle.com>
 * @package    block_configurable_reports
 * @author     Juan leyva <http://www.twitter.com/jleyvadelgado>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * export_report
 *
 * @param object $report
 * @return void
 */
function export_report($report) {
    global $CFG;
    require_once($CFG->libdir . '/csvlib.class.php');

    $table = $report->table;

    $matrix = [];
    $filename = format_string($report->name) ?? 'report';

    if (!empty($table->head)) {
        foreach ($table->head as $key => $heading) {
            $matrix[0][$key] = str_replace("\n", ' ', htmlspecialchars_decode(strip_tags(nl2br(format_string($heading)))));
        }
    }

    if (!empty($table->data)) {
        foreach ($table->data as $rkey => $row) {
            foreach ($row as $key => $item) {
                $matrix[$rkey + 1][$key] = str_replace("\n", ' ', htmlspecialchars_decode(strip_tags(nl2br(format_string($item)))));
            }
        }
    }

    $csvdelimiter = get_config('block_configurable_reports', 'csvdelimiter');
    $csvexport = new csv_export_writer("$csvdelimiter", '"', 'application/download', true);
    $csvexport->set_filename($filename);

    foreach ($matrix as $ri => $col) {
        $csvexport->add_data($col);
    }
    $csvexport->download_file();
    exit;
}
