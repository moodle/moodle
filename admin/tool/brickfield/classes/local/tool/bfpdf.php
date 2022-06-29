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
 * tool_brickfield bfpdf
 *
 * @package    tool_brickfield
 * @copyright  2020 Brickfield Education Labs, https://brickfield.ie
 * @author     Max Larkin <max@brickfieldlabs.ie>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_brickfield\local\tool;

use tool_brickfield\accessibility;

defined('MOODLE_INTERNAL') || die;
require_once($CFG->libdir . '/pdflib.php');

/**
 * tool_brickfield bfpdf
 *
 * @package    tool_brickfield
 * @copyright  2020 Brickfield Education Labs, https://brickfield.ie
 * @author     Max Larkin <max@brickfieldlabs.ie>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class bfpdf extends \pdf {
    /**
     * Overriding the footer function in TCPDF.
     */
    public function Footer() {
        $this->SetY(-25);
        $this->SetFont('helvetica', 'I', 10);

        $this->write(10, 'Powered by ', '', 0, 'C', true, 0, false, false, 0);
        $this->image(accessibility::get_file_path('/pix/pdf/logo-black.png'),
            '', '', 34, '', 'PNG', '', '', false, 300, 'C');
    }
}
