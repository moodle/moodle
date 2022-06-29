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
 * Book module upgrade code
 *
 * @package    mod_book
 * @copyright  2009-2011 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * Book module upgrade task
 *
 * @param int $oldversion the version we are upgrading from
 * @return bool always true
 */
function xmldb_book_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    // Automatically generated Moodle v3.6.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v3.7.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v3.8.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v3.9.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2021052501) {
        $table = new xmldb_table('book_chapters');
        $index = new xmldb_index('bookid', XMLDB_INDEX_NOTUNIQUE, ['bookid']);
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }
        upgrade_mod_savepoint(true, 2021052501, 'book');
    }

    // Automatically generated Moodle v4.0.0 release upgrade line.
    // Put any upgrade step following this.

    return true;
}
