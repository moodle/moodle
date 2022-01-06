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
 * A custom renderer class that extends the plugin_renderer_base and is used by the editpdfplus feedback plugin.
 *
 * @package   assignfeedback_editpdfplus
 * @copyright  2016 Université de Lausanne
 * The code is based on mod/assign/feedback/editpdf/classes/widget.php by Davo Smith.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

class assignfeedback_editpdfplus_widget implements renderable {

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

    /** @var bool $readonly */
    public $readonly = true;

    /** @var tool[] $toolbars */
    public $customToolbars = array();

    /** @var tool[] $toolbars */
    public $genericToolbar = array();

    /** @var axis[] $toolbars */
    public $axis = array();

    /**
     * Constructor
     * 
     * @param array $args Parameters in order to initialize a widget. Should contain : 
     * int $assignment - Assignment instance id
     * int $userid - The user id we are grading
     * int $attemptnumber - The attempt number we are grading
     * moodle_url $downloadurl - A url to download the current generated pdf.
     * string $downloadfilename - Name of the generated pdf.
     * bool $readonly - Show the readonly interface (no tools).
     * tool[] customToolbars - the different tool to display
     * tool[] genericToolbar - the generics tools
     * axis[] $axis - the different axis to display
     */
    public function __construct($args) {
        $this->assignment = $args["assignment"];
        $this->userid = $args["userid"];
        $this->attemptnumber = $args["attemptnumber"];
        $this->downloadurl = $args["downloadurl"];
        $this->downloadfilename = $args["downloadfilename"];
        $this->readonly = $args["readonly"];
        $this->customToolbars = $args["customToolbars"];
        $this->genericToolbar = $args["genericToolbar"];
        $this->axis = $args["axis"];
    }

}
