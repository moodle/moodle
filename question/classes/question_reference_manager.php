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

use core_question\local\bank\question_version_status;

/**
 * This class should provide an API for managing question_references.
 *
 * Unfortunately, question_references were introduced in the DB structure
 * without an nice API. This class is being added later, and is currently
 * terribly incomplete, but hopefully it can be improved in time.
 *
 * @package    core_question
 * @copyright  2023 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_reference_manager {
    /**
     * Return a list of those questions from the list passed in, which are referenced.
     *
     * A question is referenced if either:
     * - There is a question_reference pointing at exactly that version of that question; or
     * - There is an 'always latest' reference, and the question id is the latest non-draft version
     *   of that question_bank_entry.
     *
     * @param array $questionids a list of question ids to check.
     * @return array a list of the question ids from the input array which are referenced.
     */
    public static function questions_with_references(array $questionids): array {
        global $DB;

        if (empty($questionids)) {
            return [];
        }

        [$qidtest, $params] = $DB->get_in_or_equal($questionids, SQL_PARAMS_NAMED, 'outerqid');
        [$lqidtest, $lparams] = $DB->get_in_or_equal($questionids, SQL_PARAMS_NAMED, 'innerqid');

        return $DB->get_fieldset_sql("
            SELECT qv.questionid

              FROM {question_versions} qv

         -- This is a performant to get the latest non-draft version for each
         -- question_bank_entry that relates to one of our questionids.
         LEFT JOIN (
                       SELECT lqv.questionbankentryid,
                              MAX(lv.version) AS latestusableversion
                         FROM {question_versions} lqv
                         JOIN {question_versions} lv ON lv.questionbankentryid = lqv.questionbankentryid
                        WHERE lqv.questionid $lqidtest
                          AND lv.status <> :draft
                     GROUP BY lqv.questionbankentryid
                   ) latestversions ON latestversions.questionbankentryid = qv.questionbankentryid

              JOIN {question_references} qr ON qr.questionbankentryid = qv.questionbankentryid
                       AND (qr.version = qv.version OR qr.version IS NULL AND qv.version = latestversions.latestusableversion)

             WHERE qv.questionid $qidtest
            ", array_merge($params, $lparams, ['draft' => question_version_status::QUESTION_STATUS_DRAFT]));
    }
}
