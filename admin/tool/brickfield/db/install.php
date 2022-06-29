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
 * Install code for Brickfield accessibility local plugin.
 *
 * @package    tool_brickfield
 * @copyright  2020 Brickfield Education Labs, https://www.brickfield.ie - Author: Karen Holland
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * Perform the post-install procedures.
 */
function xmldb_tool_brickfield_install() {
    global $DB, $CFG;

    $filepath = $CFG->dirroot . "/admin/tool/brickfield/json/checks.json";
    $checksjson = file_get_contents($filepath);
    $records = json_decode($checksjson);

    foreach ($records->checks as $record) {
        // Convert checkgroup names into integer values where necessary.
        if (isset($record->checkgroup) && !is_numeric($record->checkgroup)) {
            $record->checkgroup = \tool_brickfield\area_base::checkgroup_value($record->checkgroup);
        }
        $DB->insert_record('tool_brickfield_checks', $record, false);
    }
}
