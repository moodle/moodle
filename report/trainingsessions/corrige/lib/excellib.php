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
 * Excel writer abstraction layer.
 * Extends some missing format writers.
 *
 * @copyright  (C) 2018 Valery Fremaux (valery.fremaux@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package    core
 */

defined('MOODLE_INTERNAL') || die();
require_once($CFG->libdir.'/excellib.class.php');

class MoodleExcelWorkbookTS extends MoodleExcelWorkbook {
    /**
     * Create one Moodle Worksheet
     *
     * @param string $name Name of the sheet
     * @return MoodleExcelWorksheet
     */
    public function add_worksheet($name = '') {
        return new MoodleExcelWorksheetTS($name, $this->objPHPExcel);
    }
}

class MoodleExcelWorksheetTS extends MoodleExcelWorksheet {

    /**
     * Write one date somewhere in the worksheet.
     * @param integer $row    Zero indexed row
     * @param integer $col    Zero indexed column
     * @param string  $date   The date to write in UNIX timestamp format
     * @param mixed   $format The XF format for the cell
     */
    public function write_time($row, $col, $duration, $format = null) {
        $this->worksheet->setCellValueByColumnAndRow($col, $row+1, $duration);
        $this->worksheet->getStyleByColumnAndRow($col, $row+1)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_TIME4);
        $this->apply_format($row, $col, $format);
    }
}