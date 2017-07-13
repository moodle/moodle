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
 * uploadlib.php - This class handles all aspects of fileuploading
 *
 * @package    core
 * @subpackage file
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * This class handles all aspects of fileuploading
 *
 * @deprecated since 2.7 - use new file pickers instead
 *
 * @package   moodlecore
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class upload_manager {

    /**
     * Constructor, sets up configuration stuff so we know how to act.
     *
     * Note: destination not taken as parameter as some modules want to use the insertid in the path and we need to check the other stuff first.
     *
     * @deprecated since 2.7 - use new file pickers instead
     *
     */
    function __construct($inputname='', $deleteothers=false, $handlecollisions=false, $course=null, $recoverifmultiple=false, $modbytes=0, $silent=false, $allownull=false, $allownullmultiple=true) {
        throw new coding_exception('upload_manager class can not be used any more, please use file picker instead');
    }
}
