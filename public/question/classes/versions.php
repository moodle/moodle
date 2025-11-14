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
}
