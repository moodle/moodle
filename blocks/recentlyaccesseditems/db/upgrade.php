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
 * This file keeps track of upgrades to the recentlyaccesseditems block
 *
 * Sometimes, changes between versions involve alterations to database structures
 * and other major things that may break installations.
 *
 * The upgrade function in this file will attempt to perform all the necessary
 * actions to upgrade your older installation to the current version.
 *
 * If there's something it cannot do itself, it will tell you what you need to do.
 *
 * The commands in here will all be database-neutral, using the methods of
 * database_manager class
 *
 * Please do not forget to use upgrade_set_timeout()
 * before any action that may take longer time to finish.
 *
 * @package block_recentlyaccesseditems
 * @copyright 2019 Peter Dias
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Upgrade the recentlyaccesseditems db table.
 *
 * @param $oldversion
 * @return bool
 */
function xmldb_block_recentlyaccesseditems_upgrade($oldversion, $block) {
    global $DB;

    // Automatically generated Moodle v3.7.0 release upgrade line.
    // Put any upgrade step following this.
    if ($oldversion < 2019052001) {
        // Query the items to be deleted as a list of IDs. We cannot delete directly from this as a
        // subquery because MySQL does not support delete with subqueries.
        $fordeletion = $DB->get_fieldset_sql("
                SELECT rai.id
                  FROM {block_recentlyaccesseditems} rai
             LEFT JOIN {course} c ON c.id = rai.courseid
             LEFT JOIN {course_modules} cm ON cm.id = rai.cmid
                 WHERE c.id IS NULL OR cm.id IS NULL");

        // Delete the array in chunks of 500 (Oracle does not support more than 1000 parameters,
        // let's leave some leeway, there are likely only one chunk anyway).
        $chunks = array_chunk($fordeletion, 500);
        foreach ($chunks as $chunk) {
            $DB->delete_records_list('block_recentlyaccesseditems', 'id', $chunk);
        }

        upgrade_block_savepoint(true, 2019052001, 'recentlyaccesseditems', false);
    }

    // Automatically generated Moodle v3.8.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v3.9.0 release upgrade line.
    // Put any upgrade step following this.

    return true;
}
