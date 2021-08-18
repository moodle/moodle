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
 * Atto text editor import Microsoft Word files - uninstall.
 *
 * @package    atto_wordimport
 * @copyright  2015 Eoin Campbell
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @license    see original copyright notice below
 */

defined('MOODLE_INTERNAL') || die();


/**
 * Remove Wordimport plugin button from the files group on uninstall
 *
 * @return void
 */
function xmldb_atto_wordimport_uninstall() {
    // Remove 'wordimport' from the toolbar editor_atto config variable.
    $toolbar = get_config('editor_atto', 'toolbar');
    if (strpos($toolbar, 'wordimport') !== false) {
        $newgroups = array();
        $groups = explode("\n", $toolbar);
        foreach ($groups as $group) {
            if (strpos($group, 'wordimport') !== false) {
                // Remove the 'wordimport' item from the group.
                $parts = explode('=', $group);
                $items = explode(',', $parts[1]);
                $newitems = array();
                foreach ($items as $item) {
                    if (trim($item) != 'wordimport') {
                        $newitems[] = $item;
                    }
                }
                if (!empty($newitems)) {
                    $parts[1] = implode(',', $newitems);
                    $newgroups[] = implode('=', $parts);
                }
            } else {
                $newgroups[] = $group;
            }
        }
        $toolbar = implode("\n", $newgroups);
        set_config('toolbar', $toolbar, 'editor_atto');
    }
}