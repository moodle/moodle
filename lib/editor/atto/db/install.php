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
 * Atto text editor installation steps.
 *
 * @package    editor_atto
 * @copyright  2013 Damyon Wiese  <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Enable this text editor by default.
 *
 * @return bool
 */
function xmldb_editor_atto_install() {
    global $CFG;
    // Get the current list of editors.
    $currentconfig = $CFG->texteditors;
    if (is_null($currentconfig)) {
        $currentconfig = '';
    }
    $editors = explode(',', $currentconfig);
    // Insert atto in the second position.
    array_splice($editors, 1, 0, array('atto'));
    // Remove duplicates.
    $editors = array_unique($editors);
    // Set the new config.
    unset_config('texteditors');
    set_config('texteditors', implode(',', $editors));

    return true;
}
