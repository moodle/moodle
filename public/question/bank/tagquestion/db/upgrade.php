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
 * Install function for qbank_tagquestion
 *
 * @package   qbank_tagquestion
 * @copyright 2025 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author    Mark Johnson <mark.johnson@catalyst-eu.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Upgrade steps for qbank_tagquestion
 *
 * @param int $oldversion The version we are upgrading from
 */
function xmldb_qbank_tagquestion_upgrade(int $oldversion): bool {
    global $DB;
    if ($oldversion < 2025100601) {
        // Delete orphaned question tags.
        $orphanedtags = $DB->get_records_sql("
            SELECT ti.id
              FROM {tag_instance} ti
         LEFT JOIN {question} q ON q.id = ti.itemid
             WHERE ti.component = 'core_question'
                   AND ti.itemtype = 'question'
                   AND q.id IS NULL
        ");
        if ($orphanedtags) {
            $DB->delete_records_list('tag_instance', 'id', array_keys($orphanedtags));
        }

        upgrade_plugin_savepoint(true, 2025100601, 'qbank', 'tagquestion');
    }
    return true;
}
