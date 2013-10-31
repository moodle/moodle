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

// This script is run after the dashboard has been installed.

function xmldb_local_iomad_dashboard_uninstall() {

    global $DB;

    // Remove blocks to the dashboard
    // yes, I know this isn't really what this is for!!
    $systemcontext = context_system::instance();
    $contextid = $systemcontext->id;
    $instances = $DB->get_recordset('block_instances',
        array('parentcontextid' => $contextid, 'pagetypepattern' => 'local-iomad-dashboard-index'));
    foreach ($instances as $instance) {
        blocks_delete_instance($instance, true);
    }

    return true;
}
