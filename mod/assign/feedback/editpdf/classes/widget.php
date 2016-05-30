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
 * This file contains the definition for the library class for edit PDF renderer.
 *
 * @package   assignfeedback_editpdf
 * @copyright 2012 Davo Smith
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * A custom renderer class that extends the plugin_renderer_base and is used by the editpdf feedback plugin.
 *
 * @package assignfeedback_editpdf
 * @copyright 2013 Davo Smith
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class assignfeedback_editpdf_widget implements renderable {

    /** @var int $assignment - Assignment instance id */
    public $assignment = 0;
    /** @var int $userid - The user id we are grading */
    public $userid = 0;
    /** @var mixed $attemptnumber - The attempt number we are grading */
    public $attemptnumber = 0;
    /** @var moodle_url $downloadurl */
    public $downloadurl = null;
    /** @var string $downloadfilename */
    public $downloadfilename = null;
    /** @var string[] $stampfiles */
    public $stampfiles = array();
    /** @var bool $readonly */
    public $readonly = true;
    /** @var integer $pagetotal */
    public $pagetotal = 0;

    /**
     * Constructor
     * @param int $assignment - Assignment instance id
     * @param int $userid - The user id we are grading
     * @param int $attemptnumber - The attempt number we are grading
     * @param moodle_url $downloadurl - A url to download the current generated pdf.
     * @param string $downloadfilename - Name of the generated pdf.
     * @param string[] $stampfiles - The file names of the stamps.
     * @param bool $readonly - Show the readonly interface (no tools).
     * @param integer $pagetotal - The total number of pages.
     */
    public function __construct($assignment, $userid, $attemptnumber, $downloadurl,
                                $downloadfilename, $stampfiles, $readonly, $pagetotal) {
        $this->assignment = $assignment;
        $this->userid = $userid;
        $this->attemptnumber = $attemptnumber;
        $this->downloadurl = $downloadurl;
        $this->downloadfilename = $downloadfilename;
        $this->stampfiles = $stampfiles;
        $this->readonly = $readonly;
        $this->pagetotal = $pagetotal;
    }
}
