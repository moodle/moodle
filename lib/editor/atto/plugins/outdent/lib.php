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
 * Atto text editor integration version file.
 *
 * @package    atto_outdent
 * @copyright  2013 Damyon Wiese  <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Initialise this plugin
 * @param string $elementid
 */
function atto_outdent_init_editor($elementid) {
    global $PAGE, $OUTPUT;

    $icon = array('e/decrease_indent', 'editor_atto');

    $PAGE->requires->yui_module('moodle-atto_outdent-button',
                                'M.atto_outdent.init',
                                array(array('elementid'=>$elementid, 'icon'=>$icon, 'group'=>'list')));

}

/**
 * Return the order this plugin should be displayed in the toolbar
 * @return int the absolute position within the toolbar
 */
function atto_outdent_sort_order() {
    return 4;
}
