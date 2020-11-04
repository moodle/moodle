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

    /**
     * Start output to file, note that the actual writing of the file is done in {@see close_output_to_file()}
     */
    public function start_output_to_file(): void {
        $this->start_output();
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

    /**
     * Method to define whether the dataformat supports export of HTML
     *
     * @return bool
     */
    public function supports_html(): bool {
        return true;
    }

    /**
     * When exporting images, we need to return their Base64 encoded content. Otherwise TCPDF will create a HTTP
     * request for them, which will lead to the login page (i.e. not the image it expects) and throw an exception
     *
     * Note: ideally we would copy the file to a temp location and return it's path, but a bug in TCPDF currently
     * prevents that
     *
     * @param \stored_file $file
     * @return string|null
     */
    protected function export_html_image_source(\stored_file $file): ?string {
        // Set upper dimensions for embedded images.
        $resizedimage = $file->resize_image(400, 300);

        return '@' . base64_encode($resizedimage);
    }

    /**
     * Write a single record
     *
     * @param array $record
     * @param int $rownum
     */
    public function write_record($record, $rownum) {
        $rowheight = 0;

        $record = $this->format_record($record);
        foreach ($record as $cell) {
            // We need to calculate the row height (accounting for any content). Unfortunately TCPDF doesn't provide an easy
            // method to do that, so we create a second PDF inside a transaction, add cell content and use the largest cell by
            // height. Solution similar to that at https://stackoverflow.com/a/1943096.
            $pdf2 = clone $this->pdf;
            $pdf2->startTransaction();
            $numpages = $pdf2->getNumPages();
            $pdf2->AddPage('L');
            $pdf2->writeHTMLCell($this->colwidth, 0, '', '', $cell, 1, 1, false, true, 'L');
            $pagesadded = $pdf2->getNumPages() - $numpages;
            $pageheight = $pdf2->getPageHeight() - $pdf2->getMargins()['top'] - $pdf2->getMargins()['bottom'];
            $cellheight = ($pagesadded - 1) * $pageheight + $pdf2->getLastH();
            $rowheight = max($rowheight, $cellheight);
            $pdf2->rollbackTransaction();
        }

        $margins = $this->pdf->getMargins();
        if ($this->pdf->getNumPages() > 1 &&
                ($this->pdf->GetY() + $rowheight + $margins['bottom'] > $this->pdf->getPageHeight())) {
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
            $this->pdf->writeHTMLCell($this->colwidth, $rowheight, '', '', $cell, 1, $nextposition, false, true, 'L');
        }
    }

    public function close_output() {
        $filename = $this->filename . $this->get_extension();

        $this->pdf->Output($filename, 'D');
    }

    /**
     * Write data to disk
     *
     * @return bool
     */
    public function close_output_to_file(): bool {
        $this->pdf->Output($this->filepath, 'F');

        return true;
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
