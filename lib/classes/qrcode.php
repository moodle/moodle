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
 * Class for generating QR codes. Wrapper class that extends TCPDF.
 *
 * @package    core
 * @copyright  2020 Moodle Pty Ltd.
 * @author     Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/tcpdf/tcpdf_barcodes_2d.php');

/**
 * Class for generating QR codes. Wrapper class that extends TCPDF.
 *
 * @copyright  2020 Moodle Pty Ltd.
 */
class core_qrcode extends TCPDF2DBarcode {

    /**
     * Overrided constructor to force QR codes.
     *
     * @param string $data the data to generate the code
     */
    public function __construct($data) {

        parent::__construct($data, 'QRCODE');
    }
}
