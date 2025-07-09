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

namespace qtype_multianswer\task;

use context_system;
use core\task\stored_progress_task_trait;
use core_question\local\bank\question_version_status;
use question_bank;
use question_engine_data_mapper;

/**
 * Cleanup duplicate subquestions
 *
 * Due to MDL-85721, there may be duplicated subquestions in the database. These have a question bank entry, question version,
 * and question record with a parent, but they are not referred to in that parent's sequence.
 *
 * @package   qtype_multianswer
 * @copyright 2025 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author    Mark Johnson <mark.johnson@catalyst-eu.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cleanup_duplicate_subquestions extends \core\task\adhoc_task {
    use stored_progress_task_trait;

    /**
     * Find questions where there are other questions with identical text, stamp and multianswer parent
     *
     * We may have multiple subquestions with the same stamp but different text or parents due to historical bugs,
     * so this includes the ID field from one of the duplicates to ensure we have a unique first field.
     *
     * @return array
     */
    public function find_duplicated_subquestions(): array {
        global $DB;
        $questiontext = $DB->sql_cast_to_char('subq.questiontext');
        $sequence = $DB->sql_cast_to_char('qm.sequence');
        return $DB->get_records_sql("
            SELECT MIN(subq.id) AS firstid,
                   subq.stamp AS stamp,
                   {$questiontext} AS questiontext,
                   subq.parent,
                   {$sequence} AS sequence,
                   COUNT(1) AS count
              FROM {question} subq
              JOIN {question} q ON q.id = subq.parent
              JOIN {question_multianswer} qm ON q.id = qm.question
             WHERE q.qtype = 'multianswer'
          GROUP BY subq.stamp, {$questiontext}, subq.parent, {$sequence}
            HAVING COUNT(1) > 1
        ");
    }

    #[\Override]
    public function execute() {
        global $CFG, $DB;
        require_once($CFG->libdir . '/questionlib.php');

        $duplicatedsubquestions = $this->find_duplicated_subquestions();

        $duplicatedcount = count($duplicatedsubquestions);

        if ($duplicatedcount === 0) {
            mtrace("No duplicated questions found.");
            return;
        }

        mtrace("Found {$duplicatedcount} subquestions with duplicates.");

        $this->start_stored_progress();
        $progress = $this->get_progress();
        foreach ($duplicatedsubquestions as $subquestion) {
            // Find instances of the subquestion that do not appear in the sequence of the parent.
            [$insql, $inparams] = $DB->get_in_or_equal(explode(',', $subquestion->sequence), equal: false);
            $params = array_merge([$subquestion->parent, $subquestion->stamp], $inparams);
            $duplicates = $DB->get_records_select('question', "parent = ? AND stamp = ? AND id {$insql}", $params);
            $duplicatecount = count($duplicates);
            // Delete each duplicate, with a progress bar.
            mtrace("");
            mtrace("Deleting {$duplicatecount} duplicates:");
            $progress->start_progress($subquestion->stamp, $duplicatecount);
            foreach ($duplicates as $duplicate) {
                // Based on question_delete_question(), without checking for parent usage or deleting children.
                // If the question is being used, just mark it as hidden. Otherwise, delete the question, version and question bank
                // entry.
                $sql = "SELECT qv.id as versionid,
                               qv.version,
                               qbe.id as entryid,
                               qc.id as categoryid,
                               ctx.id as contextid
                          FROM {question} q
                     LEFT JOIN {question_versions} qv ON qv.questionid = q.id
                     LEFT JOIN {question_bank_entries} qbe ON qbe.id = qv.questionbankentryid
                     LEFT JOIN {question_categories} qc ON qc.id = qbe.questioncategoryid
                     LEFT JOIN {context} ctx ON ctx.id = qc.contextid
                         WHERE q.id = ?";
                $questiondata = $DB->get_record_sql($sql, [$duplicate->id]);

                // Do not delete a question if it is used by an activity module. Just mark the version hidden.
                if (questions_in_use([$duplicate->id])) {
                    $DB->set_field(
                        'question_versions',
                        'status',
                        question_version_status::QUESTION_STATUS_HIDDEN,
                        ['questionid' => $duplicate->id]
                    );
                    $progress->increment_progress();
                    continue;
                }

                // This sometimes happens in old sites with bad data.
                if (!$questiondata->contextid) {
                    debugging('Deleting question ' . $duplicate->id . ' which is no longer linked to a context. ' .
                        'Assuming system context to avoid errors, but this may mean that some data like files, ' .
                        'tags, are not cleaned up.');
                    $questiondata->contextid = context_system::instance()->id;
                    $questiondata->categoryid = 0;
                }

                // Delete previews of the question.
                $dm = new question_engine_data_mapper();
                $dm->delete_previews($duplicate->id);

                // Delete questiontype-specific data.
                question_bank::get_qtype($duplicate->qtype, false)->delete_question($duplicate->id, $questiondata->contextid);

                // Finally delete the question record itself.
                $DB->delete_records('question', ['id' => $duplicate->id]);
                $DB->delete_records('question_versions', ['id' => $questiondata->versionid]);
                $DB->delete_records('question_references',
                    [
                        'version' => $questiondata->version,
                        'questionbankentryid' => $questiondata->entryid,
                    ]);
                delete_question_bank_entry($questiondata->entryid);
                question_bank::notify_question_edited($duplicate->id);

                // Log the deletion of this question.
                $duplicate->category = $questiondata->categoryid;
                $duplicate->contextid = $questiondata->contextid;
                $event = \core\event\question_deleted::create_from_question_instance($duplicate);
                $event->add_record_snapshot('question', $duplicate);
                $event->trigger();

                $progress->increment_progress();
            }
            $progress->end_progress();
        }
    }
}
