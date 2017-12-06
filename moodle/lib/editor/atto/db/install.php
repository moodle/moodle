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
 * Atto upgrade script.
 *
 * @package    editor_atto
 * @copyright  2014 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Make the Atto the default editor for upgrades from 26.
 *
 * @return bool
 */
function xmldb_editor_atto_install() {
    global $CFG;

    // Make Atto the default.
    $currenteditors = $CFG->texteditors;
    $neweditors = array();

    $list = explode(',', $currenteditors);
    array_push($neweditors, 'atto');
    foreach ($list as $editor) {
        if ($editor != 'atto') {
            array_push($neweditors, $editor);
        }
    }

    set_config('texteditors', implode(',', $neweditors));

    return true;
}
