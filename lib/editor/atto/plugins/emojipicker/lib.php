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
 * Atto text editor emoji picker plugin lib.
 *
 * @package    atto_emojipicker
 * @copyright  2019 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Initialise the strings required for JS.
 *
 * @return void
 */
function atto_emojipicker_strings_for_js() {
    global $PAGE;
    $PAGE->requires->strings_for_js(['emojipicker'], 'atto_emojipicker');
}

/**
 * Sends the parameters to JS module.
 *
 * @return array
 */
function atto_emojipicker_params_for_js() {
    global $CFG;

    return [
        'disabled' => empty($CFG->allowemojipicker) ? true : false
    ];
}
