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

defined('MOODLE_INTERNAL') || die();

function xmldb_local_iomad_track_upgrade($oldversion) {
    global $CFG, $DB;

    $result = true;
    $dbman = $DB->get_manager();

    if ($oldversion < 2017080800) {

        // Changing type of field finalscore on table local_iomad_track to number.
        $table = new xmldb_table('local_iomad_track');
        $field = new xmldb_field('finalscore', XMLDB_TYPE_NUMBER, '10, 5', null, XMLDB_NOTNULL, null, '0', 'timestarted');

        // Launch change of type for field finalscore.
        $dbman->change_field_type($table, $field);

        // Iomad_track savepoint reached.
        upgrade_plugin_savepoint(true, 2017080800, 'local', 'iomad_track');
    }

    return $result;
}
