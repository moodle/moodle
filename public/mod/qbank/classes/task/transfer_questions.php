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

namespace mod_qbank\task;

use core\context;
use core\task\adhoc_task;

/**
 * Move all the question files and tags under a given question category to a new context.
 *
 * An instance of this task will be created for each category moved to a new context in
 * {@see transfer_question_categories}, allowing the heavy lifting of moving the data for each
 * question to be parallelised.
 *
 * @package    mod_qbank
 * @copyright  2025 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author     Mark Johnson <mark.johnson@catalyst-eu.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class transfer_questions extends adhoc_task {

    /**
     * Find the questions in the category, move their files and tags to the new context.
     *
     * @return void
     */
    public function execute() {
        global $DB, $CFG;

        require_once($CFG->dirroot . '/question/engine/lib.php');

        $data = $this->get_custom_data();

        $newcontextid = $DB->get_field('question_categories', 'contextid', ['id' => $data->categoryid]);

        if (!$newcontextid) {
            mtrace("Could not find a category record for id {$data->categoryid}. Terminating task.");
            return;
        }

        $newcontext = context::instance_by_id($newcontextid);

        $sql = "SELECT q.id, q.qtype
                  FROM {question} q
                  JOIN {question_versions} qv ON qv.questionid = q.id
                  JOIN {question_bank_entries} qbe ON qbe.id = qv.questionbankentryid
                 WHERE qbe.questioncategoryid = ?";

        $questions = $DB->get_records_sql($sql, [$data->categoryid]);
        $questioncount = count($questions);
        mtrace("Moving files and tags for {$questioncount} questions in category {$data->categoryid}.");
        $transaction = $DB->start_delegated_transaction();
        foreach ($questions as $question) {
            \question_bank::get_qtype($question->qtype, false)->move_files($question->id, $data->contextid, $newcontext->id);
            // Purge this question from the cache.
            \question_bank::notify_question_edited($question->id);
        }

        question_move_question_tags_to_new_context($questions, $newcontext);
        $transaction->allow_commit();
    }
}
