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

defined('MOODLE_INTERNAL') || die();


/**
 * A scheduled task to remove unneeded random questions.
 *
 * @copyright 2018 Bo Pierce <email.bO.pierce@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class remove_unused_questions extends \core\task\scheduled_task {

    public function get_name() {
        return get_string('taskunusedrandomscleanup', 'qtype_random');
    }

    public function execute() {
        global $DB, $CFG;
        require_once($CFG->libdir . '/questionlib.php');

        // Find potentially unused random questions (up to 10000).
        // Note, because we call question_delete_question below,
        // the question will not actually be deleted if something else
        // is using them, but nothing else in Moodle core uses qtype_random,
        // and not many third-party plugins do.
        $unusedrandomids = $DB->get_records_sql("
                SELECT q.id, 1
                  FROM {question} q
             LEFT JOIN {quiz_slots} qslots ON q.id = qslots.questionid
                 WHERE qslots.questionid IS NULL
                   AND q.qtype = ? AND hidden = ?", ['random', 0], 0, 10000);

        $count = 0;
        foreach ($unusedrandomids as $unusedrandomid => $notused) {
            question_delete_question($unusedrandomid);
            // In case the question was not actually deleted (because it was in use somehow
            // mark it as hidden so the query above will not return it again.
            $DB->set_field('question', 'hidden', 1, ['id' => $unusedrandomid]);
            $count += 1;
        }
        mtrace('Cleaned up ' . $count . ' unused random questions.');
    }
}
