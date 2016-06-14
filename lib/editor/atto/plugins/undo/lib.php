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
 * Atto text editor undo plugin lib.
 *
 * @package    atto_undo
 * @copyright  2014 Jerome Mouneyrac
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Initialise the strings required for JS.
 *
 * @return void
 */
function atto_undo_strings_for_js() {
    global $PAGE;

    // In order to prevent extra strings to be imported, comment/uncomment the characters
    // which are enabled in the JavaScript part of this plugin.
    $PAGE->requires->strings_for_js(
        array(
            'redo',
            'undo'
        ),
        'atto_undo'
    );
}
