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
 * pdf data format writer
 *
 * @package    dataformat_pdf
 * @copyright  2019 Shamim Rezaie <shamim@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace dataformat_pdf;

defined('MOODLE_INTERNAL') || die();

/**
 * pdf data format writer
 *
 * @package    dataformat_pdf
 * @copyright  2019 Shamim Rezaie <shamim@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class writer extends \core\dataformat\base {

    public $mimetype = "application/pdf";

    public $extension = ".pdf";

    /**
     * @var \pdf The pdf object that is used to generate the pdf file.
     */
    protected $pdf;

    /**
     * @var float Each column's width in the current sheet.
     */
    protected $colwidth;

    /**
     * @var string[] Title of columns in the current sheet.
     */
    protected $columns;

    /**
     * writer constructor.
     */
    public function __construct() {
        global $CFG;
        require_once($CFG->libdir . '/pdflib.php');

        $this->pdf = new \pdf();
        $this->pdf->setPrintHeader(false);
        $this->pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        // Set background color for headings.
        $this->pdf->SetFillColor(238, 238, 238);
    }

    public function send_http_headers() {
    }

    public function start_output() {
        $this->pdf->AddPage('L');
    }

    public function start_sheet($columns) {
        $margins = $this->pdf->getMargins();
        $pagewidth = $this->pdf->getPageWidth() - $margins['left'] - $margins['right'];

        $this->colwidth = $pagewidth / count($columns);
        $this->columns = $columns;

        $this->print_heading();
    }

    public function write_record($record, $rownum) {
        $rowheight = 0;

        // If $record is an object convert it to an array.
        if (is_object($record)) {
            $record = (array)$record;
        }

        foreach ($record as $cell) {
            $rowheight = max($rowheight, $this->pdf->getStringHeight($this->colwidth, $cell, false, true, '', 1));
        }

        $margins = $this->pdf->getMargins();
        if ($this->pdf->GetY() + $rowheight + $margins['bottom'] > $this->pdf->getPageHeight()) {
            $this->pdf->AddPage('L');
            $this->print_heading();
        }

        // Get the last key for this record.
        end($record);
        $lastkey = key($record);

        // Reset the record pointer.
        reset($record);

        // Loop through each element.
        foreach ($record as $key => $cell) {
            // Determine whether we're at the last element of the record.
            $nextposition = ($lastkey === $key) ? 1 : 0;
            // Write the element.
            $this->pdf->Multicell($this->colwidth, $rowheight, $cell, 1, 'L', false, $nextposition);
        }
    }

    public function close_output() {
        $filename = $this->filename . $this->get_extension();

        $this->pdf->Output($filename, 'D');
    }

    /**
     * Prints the heading row.
     */
    private function print_heading() {
        $fontfamily = $this->pdf->getFontFamily();
        $fontstyle = $this->pdf->getFontStyle();
        $this->pdf->SetFont($fontfamily, 'B');
        $rowheight = 0;
        foreach ($this->columns as $columns) {
            $rowheight = max($rowheight, $this->pdf->getStringHeight($this->colwidth, $columns, false, true, '', 1));
        }

        $total = count($this->columns);
        $counter = 1;
        foreach ($this->columns as $columns) {
            $nextposition = ($counter == $total) ? 1 : 0;
            $this->pdf->Multicell($this->colwidth, $rowheight, $columns, 1, 'C', true, $nextposition);
            $counter++;
        }

        $this->pdf->SetFont($fontfamily, $fontstyle);
    }
}
