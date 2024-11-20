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

namespace mod_data\local\exporter;

use MoodleODSWorkbook;
use MoodleODSWriter;

/**
 * ODS entries exporter for mod_data.
 *
 * @package    mod_data
 * @copyright  2023 ISB Bayern
 * @author     Philipp Memmel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class ods_entries_exporter extends entries_exporter {

    /**
     * Returns the file extension of this entries exporter.
     *
     * @see \mod_data\local\exporter\entries_exporter::get_export_data_file_extension()
     */
    public function get_export_data_file_extension(): string {
        return 'ods';
    }

    /**
     * Returns the ods data exported by the ODS library for further handling.
     *
     * @see \mod_data\local\exporter\entries_exporter::get_data_file_content()
     */
    public function get_data_file_content(): string {
        global $CFG;
        require_once("$CFG->libdir/odslib.class.php");
        $filearg = '-';
        $workbook = new MoodleODSWorkbook($filearg);
        $worksheet = [];
        $worksheet[0] = $workbook->add_worksheet('');
        $rowno = 0;
        foreach ($this->exportdata as $row) {
            $colno = 0;
            foreach ($row as $col) {
                $worksheet[0]->write($rowno, $colno, $col);
                $colno++;
            }
            $rowno++;
        }
        $writer = new MoodleODSWriter($worksheet);
        return $writer->get_file_content();
    }
}
