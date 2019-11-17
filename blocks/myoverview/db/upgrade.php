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
 * This file keeps track of upgrades to the myoverview block
 *
 * @since 3.8
 * @package block_myoverview
 * @copyright 2019 Jake Dallimore <jrhdallimore@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Upgrade code for the MyOverview block.
 *
 * @param int $oldversion
 */
function xmldb_block_myoverview_upgrade($oldversion) {
    global $DB;

    if ($oldversion < 2019091800) {
        // Remove orphaned course favourites, which weren't being deleted when the course was deleted.
        $sql = 'SELECT f.id
                  FROM {favourite} f
             LEFT JOIN {course} c
                    ON (c.id = f.itemid)
                 WHERE f.component = :component
                   AND f.itemtype = :itemtype
                   AND c.id IS NULL';
        $params = ['component' => 'core_course', 'itemtype' => 'courses'];

        if ($records = $DB->get_fieldset_sql($sql, $params)) {
            $chunks = array_chunk($records, 1000);
            foreach ($chunks as $chunk) {
                list($insql, $inparams) = $DB->get_in_or_equal($chunk);
                $DB->delete_records_select('favourite', "id $insql", $inparams);
            }
        }

        upgrade_block_savepoint(true, 2019091800, 'myoverview', false);
    }

    // Automatically generated Moodle v3.8.0 release upgrade line.
    // Put any upgrade step following this.

    return true;
}
