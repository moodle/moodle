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
 * Atto install script. Adds recordrtc to the toolbar.
 *
 * @package    atto_recordrtc
 * @author     Jesus Federico (jesus [at] blindsidenetworks [dt] com)
 * @author     Jacob Prud'homme (jacob [dt] prudhomme [at] blindsidenetworks [dt] com)
 * @copyright  2017 Blindside Networks Inc.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Enable RecordRTC plugin buttons on installation.
 */
function xmldb_atto_recordrtc_install() {
    $toolbar = get_config('editor_atto', 'toolbar');
    if (strpos($toolbar, 'recordrtc') === false) {
        // Newline string changed in one of the latest versions from /n to /r/n.
        $glue = "\r\n";
        if (strpos($toolbar, $glue) === false) {
            $glue = "\n";
        }
        $groups = explode($glue, $toolbar);
        // Try to put recordrtc in files group.
        foreach ($groups as $i => $group) {
            $parts = explode('=', $group);
            if (trim($parts[0]) == 'files') {
                $groups[$i] = 'files = ' . trim($parts[1]) . ', recordrtc';
                // Update config variable.
                $toolbar = implode($glue, $groups);
                set_config('toolbar', $toolbar, 'editor_atto');
                return;
            }
        }
    }
}
