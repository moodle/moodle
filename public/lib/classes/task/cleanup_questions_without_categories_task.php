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

namespace core\task;

use core\local\cli\shutdown;

/**
 * Task to find questions with no category and delete them
 *
 * Due to MDL-86154, there may be questions left in the database after a restore, whose category has been deleted.
 * This will find any questions like that and delete them. These questions will always be unused.
 *
 * Now that we have prevented this occurring, this task is used by the upgrade process to clean up these questions.
 *
 * @package   core
 * @copyright 2026 Martin Gauk <martin.gauk@tu-berlin.de>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cleanup_questions_without_categories_task extends adhoc_task {
    use stored_progress_task_trait;

    #[\Override]
    public function execute() {
        global $CFG, $DB;
        require_once($CFG->libdir . '/questionlib.php');

        $this->start_stored_progress();
        $progress = $this->get_progress();

        $questionids = $DB->get_fieldset_sql('
            SELECT q.id
              FROM {question_bank_entries} qbe
              JOIN {question_versions} qv ON qv.questionbankentryid = qbe.id
              JOIN {question} q ON qv.questionid = q.id
         LEFT JOIN {question_categories} qc ON qbe.questioncategoryid = qc.id
             WHERE qc.id IS NULL
        ');

        $progress->start_progress('', count($questionids));
        $done = 0;

        foreach ($questionids as $questionid) {
            $transaction = $DB->start_delegated_transaction();
            question_delete_question($questionid);
            $transaction->allow_commit();
            $progress->increment_progress();
            $done++;

            // The task might be running for a long time. If a graceful exit is requested, queue this task again.
            if (shutdown::should_gracefully_exit()) {
                $newtask = new cleanup_questions_without_categories_task();
                manager::queue_adhoc_task($newtask);
                mtrace('Graceful exit requested, rescheduled cleanup_questions_without_categories_task');
                break;
            }
        }

        $progress->end_progress();
        mtrace("Cleaned up {$done} questions left over from restores.");
    }
}
