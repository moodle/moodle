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
 * @package    block_pu
 * @copyright  2021 onwards LSU Online & Continuing Education
 * @copyright  2021 onwards Robert Russo, David Lowe
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function xmldb_block_pu_upgrade($oldversion) {

    global $DB;
    $dbman = $DB->get_manager();

    if ($oldversion < 2023080400) {

        if (!$dbman->table_exists('block_pu_file')) {
            $pufile = new xmldb_table('block_pu_file');
            $pufile->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
            $pufile->add_field('fileid', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');
            $pufile->add_field('filename', XMLDB_TYPE_CHAR, '255', null, null, null, '0');
            $pufile->add_field('itemid', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');
            $pufile->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');
            $pufile->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');
            
            // Adding keys to table block_quickmail_drafts.
            $pufile->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

            $dbman->create_table($pufile);
        }
    }
    return true;    
}
