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
}
