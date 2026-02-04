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

namespace core_question;

use core\attribute\deprecated;
use core\di;
use moodle_database;

/**
 * Methods for finding and manipulating question versions
 *
 * @package   core_question
 * @copyright 2025 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author    Mark Johnson <mark.johnson@catalyst-eu.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class versions {
    /**
     * Get the next version number for a question bank entry.
     *
     * This uses the value in the question bank entry record, but if it's not set, it will calculate it based on the
     * current highest version in question_versions.
     *
     * If calling this with $increment = true, it is a good idea to do this inside a transaction which only commits after the
     * new version number has been used to save a new version of the question. This avoids wasting version numbers if an
     * error happens.
     *
     * @param int $questionbankentryid
     * @param bool $increment If true, increment the version number by 1 after it is read.
     * @return int The number of the next version.
     */
    public static function get_next_version(int $questionbankentryid, bool $increment = true): int {
        global $DB;
        $transaction = $DB->start_delegated_transaction();
        $nextversion = $DB->get_field('question_bank_entries', 'nextversion', ['id' => $questionbankentryid]);
        if (is_null($nextversion)) {
            $nextversion = $DB->get_field_sql(
                "SELECT COALESCE(MAX(qv.version), 0) + 1
                   FROM {question_versions} qv
                  WHERE qv.questionbankentryid = :qbeid",
                ['qbeid' => $questionbankentryid],
            );
            $DB->set_field('question_bank_entries', 'nextversion', $nextversion, ['id' => $questionbankentryid]);
        }
        if ($increment) {
            self::increment_next_version($questionbankentryid);
        }
        $transaction->allow_commit();
        return $nextversion;
    }

    /**
     * Increment the next version by 1 for the question bank entry
     *
     * @param int $questionbankentryid
     */
    protected static function increment_next_version(int $questionbankentryid): bool {
        global $DB;
        return $DB->execute(
            "UPDATE {question_bank_entries}
                SET nextversion = nextversion + 1
              WHERE id = :id",
            ['id' => $questionbankentryid],
        );
    }

    /**
     * Given an array of question version records, safely renumber them and any references.
     *
     * To guarantee we will not overlap with existing versions, we first find the highest version number and the count of versions,
     * and pick the higher of the two. We then re-number versions in order starting from that number + 1. This ensures
     * we avoid collisions with any existing version number.
     *
     * This means if we start with versions with the following id => version mapping:
     * 10 => 1, 11 => 1, 12 => 3, 13 => 4, 14 => 4, 15 => 5.
     * We end up with:
     * 10 => 7, 11 => 8, 12 => 9, 13 => 10, 14 => 11, 15 => 12.
     *
     * @param array $versions question_version records (with at least id, version fields) to be renumbered.
     * @return array Objects with oldversion and newversion properties, keyed by version ID.
     * @todo Deprecate in 6.0 MDL-87844 for removal in 7.0 MDL-87845.
     */
    public static function renumber_versions(array $versions) {
        $renumbers = [];
        $nextversion = array_reduce(
            $versions,
            fn($highest, $version) => max($highest, $version->version),
            count($versions),
        );
        foreach ($versions as $version) {
            $nextversion++;
            $renumbers[$version->id] = (object) ['oldversion' => $version->version, 'newversion' => $nextversion];
        }
        return $renumbers;
    }

    /**
     * Find any question bank entries which have multiple versions with the same number, and renumber the versions.
     *
     * @todo Deprecate in 6.0 MDL-87844 for removal in 7.0 MDL-87845.
     */
    public static function resolve_unique_version_violations(): void {
        $db = di::get(moodle_database::class);
        // Find questionbankentryid-version uniqueness violations.
        $violations = $db->get_fieldset_sql("
            SELECT DISTINCT questionbankentryid
              FROM {question_versions}
          GROUP BY questionbankentryid, version
            HAVING COUNT(1) > 1
        ");
        foreach ($violations as $questionbankentryid) {
            // Renumber the entry's versions based on the order in which they were created.
            $versions = $db->get_records_sql(
                "SELECT qv.id, qv.version, q.timecreated
                   FROM {question_versions} qv
                   JOIN {question} q ON qv.questionid = q.id
                  WHERE qv.questionbankentryid = :entryid
               ORDER BY timecreated, id",
                ['entryid' => $questionbankentryid],
            );
            $renumbers = self::renumber_versions($versions);
            // Now update the records in reverse order (highest new version first). This means if there are references to a
            // duplicated version number, those references will end up pointing to the latest version that had that number.
            foreach (array_reverse($renumbers, true) as $id => $renumber) {
                $db->set_field(
                    'question_references',
                    'version',
                    $renumber->newversion,
                    ['questionbankentryid' => $questionbankentryid, 'version' => $renumber->oldversion],
                );
                $db->set_field(
                    'question_versions',
                    'version',
                    $renumber->newversion,
                    ['id' => $id],
                );
            }
            // Reset the nextversion field for the question bank entry based on the new highest version.
            $db->set_field('question_bank_entries', 'nextversion', null, ['id' => $questionbankentryid]);
            self::get_next_version($questionbankentryid, false);
        }
    }
}
