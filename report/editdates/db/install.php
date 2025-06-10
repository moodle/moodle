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
 * Post installation and migration code.
 *
 * @package   report_editdates
 * @copyright 2012 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

function xmldb_report_editdates_install() {
    global $DB;

    // This is a hack to copy the permission from the old place, if they were present.
    // If this report is installed into a new Moodle, we just do what it says in access.php
    // and clone the permissions from moodle/site:viewreports, but if we are upgrading
    // a Moodle that had the old course report plugin installed, then we get rid of the
    // new cloned capabilities, and transfer the old permissions.
    if ($DB->record_exists('role_capabilities', array('capability' => 'coursereport/editdates:view'))) {
        $DB->delete_records('role_capabilities', array('capability' => 'report/editdates:view'));
        $DB->set_field('role_capabilities', 'capability', 'report/editdates:view',
                array('capability' => 'coursereport/editdates:view'));
    }

    // This is a hack which is needed for cleanup of original coursereport_completion stuff.
    unset_all_config_for_plugin('coursereport_editdates');
    capabilities_cleanup('coursereport_editdates');

    // Update existing block page patterns.
    $DB->set_field('block_instances', 'pagetypepattern', 'report-editdates-index',
            array('pagetypepattern' => 'course-report-editdates-index'));
}

