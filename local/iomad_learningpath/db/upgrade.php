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

function xmldb_local_iomad_learningpath_upgrade($oldversion) {
    global $CFG, $DB;

    $result = true;
    $dbman = $DB->get_manager();

    // Add missing learning path id to group table. 
    if ($oldversion < 2018043000) {

        // Define field learningpath to be added to iomad_learningpathgroup.
        $table = new xmldb_table('iomad_learningpathgroup');
        $field = new xmldb_field('learningpath', XMLDB_TYPE_INTEGER, '11', null, XMLDB_NOTNULL, null, null, 'id');

        // Conditionally launch add field learningpath.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define index ix_lp (not unique) to be added to iomad_learningpathgroup.
        $index = new xmldb_index('ix_lp', XMLDB_INDEX_NOTUNIQUE, array('learningpath'));

        // Conditionally launch add index ix_lp.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Iomad_learningpath savepoint reached.
        upgrade_plugin_savepoint(true, 2018043000, 'local', 'iomad_learningpath');
    }

    return $result;
}
