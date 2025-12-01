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
 * A scheduled task to remove unneeded random questions.
 *
 * @package   qtype_random
 * @category  task
 * @copyright 2018 Bo Pierce <email.bO.pierce@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace qtype_random\task;

use core\task\manager;


/**
 * A scheduled task to remove unneeded random questions.
 *
 * @copyright 2018 Bo Pierce <email.bO.pierce@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class remove_unused_questions extends \core\task\scheduled_task {
    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name(): string {
        return get_string('taskunusedrandomscleanup', 'qtype_random');
    }

    /**
     * Do the job.
     *
     * @return void
     */
    public function execute() {
        global $DB, $CFG;
        require_once($CFG->libdir . '/questionlib.php');

        // Confirm, that there is no restore in progress to make sure we do not
        // clean up questions that have their quiz slots not restored yet.
        $restoretasks = [
            '\core\task\asynchronous_copy_task',
            '\core\task\asynchronous_restore_task',
        ];

        $running = manager::get_running_tasks();
        foreach ($running as $task) {
            if (in_array($task->classname, $restoretasks)) {
                mtrace('Detected running async restore. Aborting the task.');
                return;
            }
        }

        // Find potentially unused random questions (up to 5000).
        // Note, because we call question_delete_question below,
        // the question will not actually be deleted if something else
        // is using them, but nothing else in Moodle core uses qtype_random,
        // and not many third-party plugins do.
        $unusedrandomids = $DB->get_records_sql(
            "    SELECT DISTINCT q.id, 1
                   FROM {question} q
                   JOIN {question_versions} qv on qv.questionid = q.id
                   JOIN {question_bank_entries} qbe on qbe.id = qv.questionbankentryid
              LEFT JOIN {question_references} qr on qr.questionbankentryid = qbe.id
                  WHERE qr.questionbankentryid IS NULL
                    AND q.qtype = ? AND qv.status <> ?",
            ['random', 'hidden'],
            0,
            5000
        );

        $count = 0;
        foreach ($unusedrandomids as $unusedrandomid => $notused) {
            question_delete_question($unusedrandomid);
            // In case the question was not actually deleted (because it was in use somehow),
            // it will be marked as hidden, so the query above will not return it again.
            $count += 1;
        }
        mtrace('Cleaned up ' . $count . ' unused random questions.');
    }
}
