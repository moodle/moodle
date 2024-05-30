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

use core_qrcode;

/**
 * A set of tests for some of the QR code functionality within Moodle.
 *
 * @package    core
 * @copyright  Moodle Pty Ltd
 * @author     <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qrcode_test extends \basic_testcase {

    /**
     * Basic test to generate a QR code and check that the library is not broken.
     */
    public function test_generate_basic_qr(): void {
        // The QR code generator library apply masks by random order, this is why everytime a QR code is generated the resultant
        // binary file can be different. This is why tests are limited.

        $text = 'abc';
        $color = 'black';
        $qrcode = new core_qrcode($text, $color);
        $svgdata = $qrcode->getBarcodeSVGcode(1, 1);

        // Just check the SVG was generated.
        $this->assertStringContainsString('<desc>' . $text . '</desc>', $svgdata);
        $this->assertStringContainsString('fill="' . $color . '"', $svgdata);
    }
}
