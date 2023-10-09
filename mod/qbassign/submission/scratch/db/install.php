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
 * Post-install code for the submission_scratch module.
 *
 * @package qbassignsubmission_scratch
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();


/**
 * Code run after the qbassignsubmission_scratch module database tables have been created.
 * Moves the plugin to the top of the list (of 3)
 * @return bool
 */
function xmldb_qbassignsubmission_scratch_install() {
    global $CFG,$DB;

    // Set the correct initial order for the plugins.
    require_once($CFG->dirroot . '/mod/qbassign/adminlib.php');
    $pluginmanager = new qbassign_plugin_manager('qbassignsubmission');

    $pluginmanager->move_plugin('scratch', 'up');
    $pluginmanager->move_plugin('scratch', 'up');

    $dbman = $DB->get_manager();

    // Changing type of field scratch on table qbassignsubmission_scratch to binary.
    $table = new xmldb_table('qbassignsubmission_scratch');
    $field = new xmldb_field('scratch', XMLDB_TYPE_BINARY, null, null, null, null, null, 'submission');

    // Launch change of type for field scratch.
    $dbman->change_field_type($table, $field);
    
    $table = new xmldb_table('qbassignsubmission_scratch');
    $field = new xmldb_field('explanation', XMLDB_TYPE_TEXT, null, null, null, null, null, 'scratch');


    // Conditionally launch add field forcedownload.
    if (!$dbman->field_exists($table, $field)) {
        $dbman->add_field($table, $field);
    }

    return true;
}
