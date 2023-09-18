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

namespace core;

use TCPDF_STATIC;

/**
 * Tests for PDFlib
 *
 * @package    core
 * @copyright  2021 Brendan Heywood (brendan@catalyst-au.net)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class pdflib_test extends \advanced_testcase {

    public function test_gettcpdf_producer() {
        global $CFG;
        require_once($CFG->libdir.'/pdflib.php');

        // This is to reduce the information disclosure in PDF metadata.
        // If we upgrade TCPDF keep it just the major version.
        $producer = TCPDF_STATIC::getTCPDFProducer();
        $this->assertEquals('TCPDF (http://www.tcpdf.org)', $producer);
    }

    public function test_qrcode() {
        global $CFG;
        require_once($CFG->libdir.'/pdflib.php');

        $this->resetAfterTest();

        $pdf = new \pdf();
        $pdf->AddPage('P', [500, 500]);
        $pdf->SetMargins(10, 0, 10);

        $style = [
            'border' => 0,
            'vpadding' => 'auto',
            'hpadding' => 'auto',
            'fgcolor' => [0, 0, 0],
            'bgcolor' => [255, 255, 255],
            'module_width' => 1,
            'module_height' => 1
        ];

        $pdf->setCellPaddings(0, 0, 0, 0);
        $pdf->write2DBarcode('https://www.example.com/moodle/admin/search.php',
            'QRCODE,M', null, null, 35, 35, $style, 'N');
        $pdf->SetFillColor(255, 255, 255);
        $res = $pdf->Output('output', 'S');

        $this->assertGreaterThan(100000, strlen($res));
        $this->assertLessThan(120000, strlen($res));
    }

    /**
     * Test get_export_fontlist function.
     *
     * @covers ::get_export_fontlist
     *
     * @return void
     */
    public function test_get_export_fontlist(): void {
        global $CFG;
        require_once($CFG->libdir.'/pdflib.php');

        $this->resetAfterTest();

        $pdf = new \pdf();
        $fontlist = $pdf->get_export_fontlist();
        $this->assertCount(1, $fontlist);
        $this->assertArrayHasKey('freesans', $fontlist);

        $CFG->pdfexportfont = [
            'kozminproregular' => 'Kozmin Pro Regular',
            'stsongstdlight' => 'STSong stdlight',
            'invalidfont' => 'Invalid'
        ];
        $fontlist = $pdf->get_export_fontlist();
        $this->assertCount(2, $fontlist);
        $this->assertArrayNotHasKey('freesans', $fontlist);
        $this->assertArrayHasKey('kozminproregular', $fontlist);
        $this->assertArrayHasKey('stsongstdlight', $fontlist);
        $this->assertArrayNotHasKey('invalidfont', $fontlist);
    }
}
