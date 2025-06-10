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
 * @package    local_kalpanmaps
 * @copyright  2021 onwards LSUOnline & Continuing Education
 * @copyright  2021 onwards Robert Russo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function xmldb_local_kalpanmaps_upgrade($oldversion) {
    global $DB;

    $result = true;

    $dbman = $DB->get_manager();

    if ($oldversion < 2021061600) {

        // Define the new table..
        $table = new xmldb_table('local_kalpanmaps');

        // Conditionally launch add field id.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '9', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null);
        $table->add_field('kaltura_id', XMLDB_TYPE_CHAR, '32', null, XMLDB_NOTNULL, null, null, 'id');
        $table->add_field('panopto_id', XMLDB_TYPE_CHAR, '64', null, XMLDB_NOTNULL, null, null, 'kaltura_id');

        // Define the keys.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Define the indexes.
        $table->add_index('kalid_ix', XMLDB_INDEX_NOTUNIQUE, ['kaltura_id']);
        $table->add_index('panid_ix', XMLDB_INDEX_NOTUNIQUE, ['panopto_id']);

        // Create the new table..
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Kalpanmaps savepoint reached.
        upgrade_plugin_savepoint(true, 2021061600, 'local', 'kalpanmaps');
    }

    return $result;
}
