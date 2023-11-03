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
 * LearnerScript plugin uninstallation.
 *
 * @package    block_learnerscript
 * @copyright  2017 Arun Kumar Mukka
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function xmldb_block_learnerscript_uninstall() {
    global $CFG, $DB;
    $DB->delete_records('logstore_standard_log',
                            array('objecttable' => 'block_learnerscript'));

    $usertours = $CFG->dirroot . '/blocks/learnerscript/usertours/';
    $usertoursjson = glob($usertours . '*.json');

    foreach ($usertoursjson as $usertour) {
        $data = file_get_contents($usertour);
        $tourconfig = json_decode($data);
        $DB->delete_records('tool_usertours_tours', array('name' => $tourconfig->name));
    }

    return true;
}