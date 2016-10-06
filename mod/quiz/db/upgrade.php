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
 * Upgrade script for the quiz module.
 *
 * @package    mod_quiz
 * @copyright  2006 Eloy Lafuente (stronk7)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * Quiz module upgrade function.
 * @param string $oldversion the version we are upgrading from.
 */
function xmldb_quiz_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    // Moodle v2.2.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2011120700) {

        // Define field lastcron to be dropped from quiz_reports.
        $table = new xmldb_table('quiz_reports');
        $field = new xmldb_field('lastcron');

        // Conditionally launch drop field lastcron.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Quiz savepoint reached.
        upgrade_mod_savepoint(true, 2011120700, 'quiz');
    }

    if ($oldversion < 2011120701) {

        // Define field cron to be dropped from quiz_reports.
        $table = new xmldb_table('quiz_reports');
        $field = new xmldb_field('cron');

        // Conditionally launch drop field cron.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Quiz savepoint reached.
        upgrade_mod_savepoint(true, 2011120701, 'quiz');
    }

    if ($oldversion < 2011120703) {
        // Track page of quiz attempts.
        $table = new xmldb_table('quiz_attempts');

        $field = new xmldb_field('currentpage', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, 0);

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        upgrade_mod_savepoint(true, 2011120703, 'quiz');
    }

    if ($oldversion < 2012030901) {
        // Configuration option for navigation method.
        $table = new xmldb_table('quiz');

        $field = new xmldb_field('navmethod', XMLDB_TYPE_CHAR, '16', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, 'free');

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        upgrade_mod_savepoint(true, 2012030901, 'quiz');
    }

    if ($oldversion < 2012040198) {
        // This step was added later. In MDL-32727, it was found that adding the
        // unique index on quiz-userid-attempt sometimes failed because of
        // duplicate entries {quizid}-{userid}-{attempt}. We do two things to
        // prevent these problems. First, here, we delete all preview attempts.

        // This code is an approximate copy-and-paste from
        // question_engine_data_mapper::delete_questions_usage_by_activities
        // Note that, for simplicity, the MySQL performance hack has been removed.
        // Since this code is for upgrade only, performance in not so critical,
        // where as simplicity of testing the code is.

        // Note that there is a limit to how far I am prepared to go in eliminating
        // all calls to library functions in this upgrade code. The only library
        // function still being used in question_engine::get_all_response_file_areas();
        // I think it is pretty safe not to inline it here.

        // Get a list of response variables that have files.
        require_once($CFG->dirroot . '/question/type/questiontypebase.php');
        $variables = array();
        foreach (core_component::get_plugin_list('qtype') as $qtypename => $path) {
            $file = $path . '/questiontype.php';
            if (!is_readable($file)) {
                continue;
            }
            include_once($file);
            $class = 'qtype_' . $qtypename;
            if (!class_exists($class)) {
                continue;
            }
            $qtype = new $class();
            if (!method_exists($qtype, 'response_file_areas')) {
                continue;
            }
            $variables += $qtype->response_file_areas();
        }

        // Conver that to a list of actual file area names.
        $fileareas = array();
        foreach (array_unique($variables) as $variable) {
            $fileareas[] = 'response_' . $variable;
        }
        // No point checking if this is empty as an optimisation, because essay
        // has response file areas, so the array will never be empty.

        // Get all the contexts where there are previews.
        $contextids = $DB->get_records_sql_menu("
                SELECT DISTINCT qu.contextid, 1
                  FROM {question_usages} qu
                  JOIN {quiz_attempts} quiza ON quiza.uniqueid = qu.id
                 WHERE quiza.preview = 1");

        // Loop over contexts and files areas, deleting all files.
        $fs = get_file_storage();
        foreach ($contextids as $contextid => $notused) {
            foreach ($fileareas as $filearea) {
                upgrade_set_timeout(300);
                $fs->delete_area_files_select($contextid, 'question', $filearea,
                        "IN (SELECT qas.id
                               FROM {question_attempt_steps} qas
                               JOIN {question_attempts} qa ON qa.id = qas.questionattemptid
                               JOIN {quiz_attempts} quiza ON quiza.uniqueid = qa.questionusageid
                              WHERE quiza.preview = 1)");
            }
        }

        // Now delete the question data associated with the previews.
        $DB->delete_records_select('question_attempt_step_data', "attemptstepid IN (
                SELECT qas.id
                  FROM {question_attempt_steps} qas
                  JOIN {question_attempts} qa ON qa.id = qas.questionattemptid
                  JOIN {quiz_attempts} quiza ON quiza.uniqueid = qa.questionusageid
                 WHERE quiza.preview = 1)");

        $DB->delete_records_select('question_attempt_steps', "questionattemptid IN (
                SELECT qa.id
                  FROM {question_attempts} qa
                  JOIN {quiz_attempts} quiza ON quiza.uniqueid = qa.questionusageid
                 WHERE quiza.preview = 1)");

        $DB->delete_records_select('question_attempts', "{question_attempts}.questionusageid IN (
                SELECT uniqueid FROM {quiz_attempts} WHERE preview = 1)");

        $DB->delete_records_select('question_usages', "{question_usages}.id IN (
                SELECT uniqueid FROM {quiz_attempts} WHERE preview = 1)");

        // Finally delete the previews.
        $DB->delete_records('quiz_attempts', array('preview' => 1));

        // Quiz savepoint reached.
        upgrade_mod_savepoint(true, 2012040198, 'quiz');
    }

    if ($oldversion < 2012040199) {
        // This step was added later. In MDL-32727, it was found that adding the
        // unique index on quiz-userid-attempt sometimes failed because of
        // duplicate entries {quizid}-{userid}-{attempt}.
        // Here, if there are still duplicate entires, we renumber the values in
        // the attempt column.

        // Load all the problem quiz attempts.
        $problems = $DB->get_recordset_sql('
                SELECT qa.id, qa.quiz, qa.userid, qa.attempt
                  FROM {quiz_attempts} qa
                  JOIN (
                          SELECT DISTINCT quiz, userid
                            FROM {quiz_attempts}
                        GROUP BY quiz, userid, attempt
                          HAVING COUNT(1) > 1
                       ) problems_view ON problems_view.quiz = qa.quiz AND
                                          problems_view.userid = qa.userid
              ORDER BY qa.quiz, qa.userid, qa.attempt, qa.id');

        // Renumber them.
        $currentquiz = null;
        $currentuserid = null;
        $attempt = 1;
        foreach ($problems as $problem) {
            if ($problem->quiz !== $currentquiz || $problem->userid !== $currentuserid) {
                $currentquiz = $problem->quiz;
                $currentuserid = $problem->userid;
                $attempt = 1;
            }
            if ($attempt != $problem->attempt) {
                $DB->set_field('quiz_attempts', 'attempt', $attempt, array('id' => $problem->id));
            }
            $attempt += 1;
        }

        $problems->close();

        // Quiz savepoint reached.
        upgrade_mod_savepoint(true, 2012040199, 'quiz');
    }

    if ($oldversion < 2012040200) {
        // Define index userid to be dropped form quiz_attempts.
        $table = new xmldb_table('quiz_attempts');
        $index = new xmldb_index('userid', XMLDB_INDEX_NOTUNIQUE, array('userid'));

        // Conditionally launch drop index quiz-userid-attempt.
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }

        // Quiz savepoint reached.
        upgrade_mod_savepoint(true, 2012040200, 'quiz');
    }

    if ($oldversion < 2012040201) {

        // Define key userid (foreign) to be added to quiz_attempts.
        $table = new xmldb_table('quiz_attempts');
        $key = new xmldb_key('userid', XMLDB_KEY_FOREIGN, array('userid'), 'user', array('id'));

        // Launch add key userid.
        $dbman->add_key($table, $key);

        // Quiz savepoint reached.
        upgrade_mod_savepoint(true, 2012040201, 'quiz');
    }

    if ($oldversion < 2012040202) {

        // Define index quiz-userid-attempt (unique) to be added to quiz_attempts.
        $table = new xmldb_table('quiz_attempts');
        $index = new xmldb_index('quiz-userid-attempt', XMLDB_INDEX_UNIQUE, array('quiz', 'userid', 'attempt'));

        // Conditionally launch add index quiz-userid-attempt.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Quiz savepoint reached.
        upgrade_mod_savepoint(true, 2012040202, 'quiz');
    }

    if ($oldversion < 2012040203) {

        // Define field state to be added to quiz_attempts.
        $table = new xmldb_table('quiz_attempts');
        $field = new xmldb_field('state', XMLDB_TYPE_CHAR, '16', null, XMLDB_NOTNULL, null, 'inprogress', 'preview');

        // Conditionally launch add field state.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Quiz savepoint reached.
        upgrade_mod_savepoint(true, 2012040203, 'quiz');
    }

    if ($oldversion < 2012040204) {

        // Update quiz_attempts.state for finished attempts.
        $DB->set_field_select('quiz_attempts', 'state', 'finished', 'timefinish > 0');

        // Other, more complex transitions (basically abandoned attempts), will
        // be handled by cron later.

        // Quiz savepoint reached.
        upgrade_mod_savepoint(true, 2012040204, 'quiz');
    }

    if ($oldversion < 2012040205) {

        // Define field overduehandling to be added to quiz.
        $table = new xmldb_table('quiz');
        $field = new xmldb_field('overduehandling', XMLDB_TYPE_CHAR, '16', null, XMLDB_NOTNULL, null, 'autoabandon', 'timelimit');

        // Conditionally launch add field overduehandling.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Quiz savepoint reached.
        upgrade_mod_savepoint(true, 2012040205, 'quiz');
    }

    if ($oldversion < 2012040206) {

        // Define field graceperiod to be added to quiz.
        $table = new xmldb_table('quiz');
        $field = new xmldb_field('graceperiod', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'overduehandling');

        // Conditionally launch add field graceperiod.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Quiz savepoint reached.
        upgrade_mod_savepoint(true, 2012040206, 'quiz');
    }

    // Moodle v2.3.0 release upgrade line
    // Put any upgrade step following this.

    if ($oldversion < 2012061702) {

        // MDL-32791 somebody reported having nonsense rows in their
        // quiz_question_instances which caused various problems. These rows
        // are meaningless, hence this upgrade step to clean them up.
        $DB->delete_records('quiz_question_instances', array('question' => 0));

        // Quiz savepoint reached.
        upgrade_mod_savepoint(true, 2012061702, 'quiz');
    }

    if ($oldversion < 2012061703) {

        // MDL-34702 the questiondecimalpoints column was created with default -2
        // when it should have been -1, and no-one has noticed in the last 2+ years!

        // Changing the default of field questiondecimalpoints on table quiz to -1.
        $table = new xmldb_table('quiz');
        $field = new xmldb_field('questiondecimalpoints', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '-1', 'decimalpoints');

        // Launch change of default for field questiondecimalpoints.
        $dbman->change_field_default($table, $field);

        // Correct any wrong values.
        $DB->set_field('quiz', 'questiondecimalpoints', -1, array('questiondecimalpoints' => -2));

        // Quiz savepoint reached.
        upgrade_mod_savepoint(true, 2012061703, 'quiz');
    }

    if ($oldversion < 2012100801) {

        // Define field timecheckstate to be added to quiz_attempts.
        $table = new xmldb_table('quiz_attempts');
        $field = new xmldb_field('timecheckstate', XMLDB_TYPE_INTEGER, '10', null, null, null, '0', 'timemodified');

        // Conditionally launch add field timecheckstate.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define index state-timecheckstate (not unique) to be added to quiz_attempts.
        $table = new xmldb_table('quiz_attempts');
        $index = new xmldb_index('state-timecheckstate', XMLDB_INDEX_NOTUNIQUE, array('state', 'timecheckstate'));

        // Conditionally launch add index state-timecheckstate.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Overdue cron no longer needs these.
        unset_config('overduelastrun', 'quiz');
        unset_config('overduedoneto', 'quiz');

        // Update timecheckstate on all open attempts.
        require_once($CFG->dirroot . '/mod/quiz/locallib.php');
        quiz_update_open_attempts(array());

        // quiz savepoint reached
        upgrade_mod_savepoint(true, 2012100801, 'quiz');
    }

    // Moodle v2.4.0 release upgrade line
    // Put any upgrade step following this.

    if ($oldversion < 2013031900) {
        // Quiz manual grading UI should be controlled by mod/quiz:grade, not :viewreports.
        $DB->set_field('quiz_reports', 'capability', 'mod/quiz:grade', array('name' => 'grading'));

        // Mod quiz savepoint reached.
        upgrade_mod_savepoint(true, 2013031900, 'quiz');
    }

    // Moodle v2.5.0 release upgrade line.
    // Put any upgrade step following this.

    // Moodle v2.6.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2014011300) {

        // Define key quiz (foreign) to be dropped form quiz_question_instances.
        $table = new xmldb_table('quiz_question_instances');
        $key = new xmldb_key('quiz', XMLDB_KEY_FOREIGN, array('quiz'), 'quiz', array('id'));

        // Launch drop key quiz.
        $dbman->drop_key($table, $key);

        // Quiz savepoint reached.
        upgrade_mod_savepoint(true, 2014011300, 'quiz');
    }

    if ($oldversion < 2014011301) {

        // Rename field quiz on table quiz_question_instances to quizid.
        $table = new xmldb_table('quiz_question_instances');
        $field = new xmldb_field('quiz', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'id');

        // Launch rename field quiz.
        $dbman->rename_field($table, $field, 'quizid');

        // Quiz savepoint reached.
        upgrade_mod_savepoint(true, 2014011301, 'quiz');
    }

    if ($oldversion < 2014011302) {

        // Define key quizid (foreign) to be added to quiz_question_instances.
        $table = new xmldb_table('quiz_question_instances');
        $key = new xmldb_key('quizid', XMLDB_KEY_FOREIGN, array('quizid'), 'quiz', array('id'));

        // Launch add key quizid.
        $dbman->add_key($table, $key);

        // Quiz savepoint reached.
        upgrade_mod_savepoint(true, 2014011302, 'quiz');
    }

    if ($oldversion < 2014011303) {

        // Define key question (foreign) to be dropped form quiz_question_instances.
        $table = new xmldb_table('quiz_question_instances');
        $key = new xmldb_key('question', XMLDB_KEY_FOREIGN, array('question'), 'question', array('id'));

        // Launch drop key question.
        $dbman->drop_key($table, $key);

        // Quiz savepoint reached.
        upgrade_mod_savepoint(true, 2014011303, 'quiz');
    }

    if ($oldversion < 2014011304) {

        // Rename field question on table quiz_question_instances to questionid.
        $table = new xmldb_table('quiz_question_instances');
        $field = new xmldb_field('question', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'quiz');

        // Launch rename field question.
        $dbman->rename_field($table, $field, 'questionid');

        // Quiz savepoint reached.
        upgrade_mod_savepoint(true, 2014011304, 'quiz');
    }

    if ($oldversion < 2014011305) {

        // Define key questionid (foreign) to be added to quiz_question_instances.
        $table = new xmldb_table('quiz_question_instances');
        $key = new xmldb_key('questionid', XMLDB_KEY_FOREIGN, array('questionid'), 'question', array('id'));

        // Launch add key questionid.
        $dbman->add_key($table, $key);

        // Quiz savepoint reached.
        upgrade_mod_savepoint(true, 2014011305, 'quiz');
    }

    if ($oldversion < 2014011306) {

        // Rename field grade on table quiz_question_instances to maxmark.
        $table = new xmldb_table('quiz_question_instances');
        $field = new xmldb_field('grade', XMLDB_TYPE_NUMBER, '12, 7', null, XMLDB_NOTNULL, null, '0', 'question');

        // Launch rename field grade.
        $dbman->rename_field($table, $field, 'maxmark');

        // Quiz savepoint reached.
        upgrade_mod_savepoint(true, 2014011306, 'quiz');
    }

    if ($oldversion < 2014021300) {

        // Define field needsupgradetonewqe to be dropped from quiz_attempts.
        $table = new xmldb_table('quiz_attempts');
        $field = new xmldb_field('needsupgradetonewqe');

        // Conditionally launch drop field needsupgradetonewqe.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Quiz savepoint reached.
        upgrade_mod_savepoint(true, 2014021300, 'quiz');
    }

    if ($oldversion < 2014022000) {

        // Define table quiz_question_instances to be renamed to quiz_slots.
        $table = new xmldb_table('quiz_question_instances');

        // Launch rename table for quiz_question_instances.
        $dbman->rename_table($table, 'quiz_slots');

        // Quiz savepoint reached.
        upgrade_mod_savepoint(true, 2014022000, 'quiz');
    }

    if ($oldversion < 2014022001) {

        // Define field slot to be added to quiz_slots.
        $table = new xmldb_table('quiz_slots');
        $field = new xmldb_field('slot', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'id');

        // Conditionally launch add field slot.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Quiz savepoint reached.
        upgrade_mod_savepoint(true, 2014022001, 'quiz');
    }

    if ($oldversion < 2014022002) {

        // Define field page to be added to quiz_slots.
        $table = new xmldb_table('quiz_slots');
        $field = new xmldb_field('page', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'quizid');

        // Conditionally launch add field page.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Quiz savepoint reached.
        upgrade_mod_savepoint(true, 2014022002, 'quiz');
    }

    if ($oldversion < 2014022003) {

        // Use the information in the old quiz.questions column to fill in the
        // new slot and page columns.
        $numquizzes = $DB->count_records('quiz');
        if ($numquizzes > 0) {
            $pbar = new progress_bar('quizquestionstoslots', 500, true);
            $transaction = $DB->start_delegated_transaction();

            $numberdone = 0;
            $quizzes = $DB->get_recordset('quiz', null, 'id', 'id,questions,sumgrades');
            foreach ($quizzes as $quiz) {
                if ($quiz->questions === '') {
                    $questionsinorder = array();
                } else {
                    $questionsinorder = explode(',', $quiz->questions);
                }

                $questionidtoslotrowid = $DB->get_records_menu('quiz_slots',
                        array('quizid' => $quiz->id), '', 'id, questionid');

                $problemfound = false;
                $currentpage = 1;
                $slot = 1;
                foreach ($questionsinorder as $questionid) {
                    if ($questionid === '0') {
                        // Page break.
                        $currentpage++;
                        continue;
                    }

                    if ($questionid === '') {
                        // This can happen as the result of old restore bugs.
                        // There can be a missing number in the list of ids.
                        // All we can do about this is ignore it, which is what
                        // the quiz system used to do. See MDL-45321.
                        continue;
                    }

                    $key = array_search($questionid, $questionidtoslotrowid);
                    if ($key !== false) {
                        // Normal case. quiz_slots entry is present.
                        // Just need to add slot and page.
                        $quizslot = new stdClass();
                        $quizslot->id   = $key;
                        $quizslot->slot = $slot;
                        $quizslot->page = $currentpage;
                        $DB->update_record('quiz_slots', $quizslot);

                        unset($questionidtoslotrowid[$key]); // So we can do a sanity check later.
                        $slot++;
                        continue;

                    } else {
                        // This should not happen. The question was listed in
                        // quiz.questions, but there was not an entry for it in
                        // quiz_slots (formerly quiz_question_instances).
                        // Previously, if such question ids were found, then
                        // starting an attempt at the quiz would throw an exception.
                        // Here, we try to add the missing data.
                        $problemfound = true;
                        $defaultmark = $DB->get_field('question', 'defaultmark',
                                array('id' => $questionid), IGNORE_MISSING);
                        if ($defaultmark === false) {
                            debugging('During upgrade, detected that question ' .
                                    $questionid . ' was listed as being part of quiz ' .
                                    $quiz->id . ' but this question no longer exists. Ignoring it.', DEBUG_NORMAL);

                            // Non-existent question. Ignore it completely.
                            continue;
                        }

                        debugging('During upgrade, detected that question ' .
                                $questionid . ' was listed as being part of quiz ' .
                                $quiz->id . ' but there was not entry for it in ' .
                                'quiz_question_instances/quiz_slots. Creating an entry with default mark.', DEBUG_NORMAL);
                        $quizslot = new stdClass();
                        $quizslot->quizid     = $quiz->id;
                        $quizslot->slot       = $slot;
                        $quizslot->page       = $currentpage;
                        $quizslot->questionid = $questionid;
                        $quizslot->maxmark    = $defaultmark;
                        $DB->insert_record('quiz_slots', $quizslot);

                        $slot++;
                        continue;
                    }

                }

                // Now, as a sanity check, ensure we have done all the
                // quiz_slots rows linked to this quiz.
                if (!empty($questionidtoslotrowid)) {
                    debugging('During upgrade, detected that questions ' .
                            implode(', ', array_values($questionidtoslotrowid)) .
                            ' had instances in quiz ' . $quiz->id . ' but were not actually used. ' .
                            'The instances have been removed.', DEBUG_NORMAL);

                    $DB->delete_records_list('quiz_slots', 'id', array_keys($questionidtoslotrowid));
                    $problemfound = true;
                }

                // If there were problems found, we probably need to re-compute
                // quiz.sumgrades.
                if ($problemfound) {
                    // C/f the quiz_update_sumgrades function in locallib.php,
                    // but note that what we do here is a bit simpler.
                    $newsumgrades = $DB->get_field_sql(
                            "SELECT SUM(maxmark)
                               FROM {quiz_slots}
                              WHERE quizid = ?",
                            array($quiz->id));
                    if (!$newsumgrades) {
                        $newsumgrades = 0;
                    }
                    if (abs($newsumgrades - $quiz->sumgrades) > 0.000005) {
                        debugging('Because of the previously mentioned problems, ' .
                                'sumgrades for quiz ' . $quiz->id .
                                ' was changed from ' . $quiz->sumgrades . ' to ' .
                                $newsumgrades . ' You should probably check that this quiz is still working: ' .
                                $CFG->wwwroot . '/mod/quiz/view.php?q=' . $quiz->id . '.', DEBUG_NORMAL);
                        $DB->set_field('quiz', 'sumgrades', $newsumgrades, array('id' => $quiz->id));
                    }
                }

                // Done with this quiz. Update progress bar.
                $numberdone++;
                $pbar->update($numberdone, $numquizzes,
                        "Upgrading quiz structure - {$numberdone}/{$numquizzes}.");
            }

            $transaction->allow_commit();
        }

        // Quiz savepoint reached.
        upgrade_mod_savepoint(true, 2014022003, 'quiz');
    }

    if ($oldversion < 2014022004) {

        // If, for any reason, there were any quiz_slots missed, then try
        // to do something about that now before we add the NOT NULL constraints.
        // In fact, becuase of the sanity check at the end of the above check,
        // any such quiz_slots rows must refer to a non-existent quiz id, so
        // delete them.
        $DB->delete_records_select('quiz_slots',
                'NOT EXISTS (SELECT 1 FROM {quiz} q WHERE q.id = quizid)');

        // Quiz savepoint reached.
        upgrade_mod_savepoint(true, 2014022004, 'quiz');

        // Now, if any quiz_slots rows are left with slot or page NULL, something
        // is badly wrong.
        if ($DB->record_exists_select('quiz_slots', 'slot IS NULL OR page IS NULL')) {
            throw new coding_exception('Something went wrong in the quiz upgrade step for MDL-43749. ' .
                    'Some quiz_slots still contain NULLs which will break the NOT NULL constraints we need to add. ' .
                    'Please report this problem at http://tracker.moodle.org/ so that it can be investigated. Thank you.');
        }
    }

    if ($oldversion < 2014022005) {

        // Changing nullability of field slot on table quiz_slots to not null.
        $table = new xmldb_table('quiz_slots');
        $field = new xmldb_field('slot', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'id');

        // Launch change of nullability for field slot.
        $dbman->change_field_notnull($table, $field);

        // Quiz savepoint reached.
        upgrade_mod_savepoint(true, 2014022005, 'quiz');
    }

    if ($oldversion < 2014022006) {

        // Changing nullability of field page on table quiz_slots to not null.
        $table = new xmldb_table('quiz_slots');
        $field = new xmldb_field('page', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'quizid');

        // Launch change of nullability for field page.
        $dbman->change_field_notnull($table, $field);

        // Quiz savepoint reached.
        upgrade_mod_savepoint(true, 2014022006, 'quiz');
    }

    if ($oldversion < 2014022007) {

        // Define index quizid-slot (unique) to be added to quiz_slots.
        $table = new xmldb_table('quiz_slots');
        $index = new xmldb_index('quizid-slot', XMLDB_INDEX_UNIQUE, array('quizid', 'slot'));

        // Conditionally launch add index quizid-slot.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Quiz savepoint reached.
        upgrade_mod_savepoint(true, 2014022007, 'quiz');
    }

    if ($oldversion < 2014022008) {

        // Define field questions to be dropped from quiz.
        $table = new xmldb_table('quiz');
        $field = new xmldb_field('questions');

        // Conditionally launch drop field questions.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Quiz savepoint reached.
        upgrade_mod_savepoint(true, 2014022008, 'quiz');
    }

    // Moodle v2.7.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2014052800) {

        // Define field completionattemptsexhausted to be added to quiz.
        $table = new xmldb_table('quiz');
        $field = new xmldb_field('completionattemptsexhausted', XMLDB_TYPE_INTEGER, '1', null, null, null, '0', 'showblocks');

        // Conditionally launch add field completionattemptsexhausted.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        // Quiz savepoint reached.
        upgrade_mod_savepoint(true, 2014052800, 'quiz');
    }

    if ($oldversion < 2014052801) {
        // Define field completionpass to be added to quiz.
        $table = new xmldb_table('quiz');
        $field = new xmldb_field('completionpass', XMLDB_TYPE_INTEGER, '1', null, null, null, 0, 'completionattemptsexhausted');

        // Conditionally launch add field completionpass.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Quiz savepoint reached.
        upgrade_mod_savepoint(true, 2014052801, 'quiz');
    }

    // Moodle v2.8.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2015030500) {
        // Define field requireprevious to be added to quiz_slots.
        $table = new xmldb_table('quiz_slots');
        $field = new xmldb_field('requireprevious', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, 0, 'page');

        // Conditionally launch add field page.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Quiz savepoint reached.
        upgrade_mod_savepoint(true, 2015030500, 'quiz');
    }

    if ($oldversion < 2015030900) {
        // Define field canredoquestions to be added to quiz.
        $table = new xmldb_table('quiz');
        $field = new xmldb_field('canredoquestions', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, 0, 'preferredbehaviour');

        // Conditionally launch add field completionpass.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Quiz savepoint reached.
        upgrade_mod_savepoint(true, 2015030900, 'quiz');
    }

    if ($oldversion < 2015032300) {

        // Define table quiz_sections to be created.
        $table = new xmldb_table('quiz_sections');

        // Adding fields to table quiz_sections.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('quizid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('firstslot', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('heading', XMLDB_TYPE_CHAR, '1333', null, null, null, null);
        $table->add_field('shufflequestions', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '0');

        // Adding keys to table quiz_sections.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('quizid', XMLDB_KEY_FOREIGN, array('quizid'), 'quiz', array('id'));

        // Adding indexes to table quiz_sections.
        $table->add_index('quizid-firstslot', XMLDB_INDEX_UNIQUE, array('quizid', 'firstslot'));

        // Conditionally launch create table for quiz_sections.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Quiz savepoint reached.
        upgrade_mod_savepoint(true, 2015032300, 'quiz');
    }

    if ($oldversion < 2015032301) {

        // Create a section for each quiz.
        $DB->execute("
                INSERT INTO {quiz_sections}
                            (quizid, firstslot, heading, shufflequestions)
                     SELECT  id,     1,         ?,       shufflequestions
                       FROM {quiz}
                ", array(''));

        // Quiz savepoint reached.
        upgrade_mod_savepoint(true, 2015032301, 'quiz');
    }

    if ($oldversion < 2015032302) {

        // Define field shufflequestions to be dropped from quiz.
        $table = new xmldb_table('quiz');
        $field = new xmldb_field('shufflequestions');

        // Conditionally launch drop field shufflequestions.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Quiz savepoint reached.
        upgrade_mod_savepoint(true, 2015032302, 'quiz');
    }

    if ($oldversion < 2015032303) {

        // Drop corresponding admin settings.
        unset_config('shufflequestions', 'quiz');
        unset_config('shufflequestions_adv', 'quiz');

        // Quiz savepoint reached.
        upgrade_mod_savepoint(true, 2015032303, 'quiz');
    }

    // Moodle v2.9.0 release upgrade line.
    // Put any upgrade step following this.

    // Moodle v3.0.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2015111601) {
        // Update quiz_sections to repair quizzes what were broken by MDL-53507.
        $problemquizzes = $DB->get_records_sql("
                SELECT quizid, MIN(firstslot) AS firstsectionfirstslot
                FROM {quiz_sections}
                GROUP BY quizid
                HAVING MIN(firstslot) > 1");

        if ($problemquizzes) {
            $pbar = new progress_bar('upgradequizfirstsection', 500, true);
            $total = count($problemquizzes);
            $done = 0;
            foreach ($problemquizzes as $problemquiz) {
                $DB->set_field('quiz_sections', 'firstslot', 1,
                        array('quizid' => $problemquiz->quizid,
                        'firstslot' => $problemquiz->firstsectionfirstslot));
                $done += 1;
                $pbar->update($done, $total, "Fixing quiz layouts - {$done}/{$total}.");
            }
        }

        // Quiz savepoint reached.
        upgrade_mod_savepoint(true, 2015111601, 'quiz');
    }

    if ($oldversion < 2015111602) {
        // Find quizzes with the combination of require passing grade and grade to pass 0.
        $gradeitems = $DB->get_records_sql("
            SELECT gi.id, gi.itemnumber, cm.id AS cmid
              FROM {quiz} q
        INNER JOIN {course_modules} cm ON q.id = cm.instance
        INNER JOIN {grade_items} gi ON q.id = gi.iteminstance
        INNER JOIN {modules} m ON m.id = cm.module
             WHERE q.completionpass = 1
               AND gi.gradepass = 0
               AND cm.completiongradeitemnumber IS NULL
               AND gi.itemmodule = m.name
               AND gi.itemtype = ?
               AND m.name = ?", array('mod', 'quiz'));

        foreach ($gradeitems as $gradeitem) {
            $DB->execute("UPDATE {course_modules}
                             SET completiongradeitemnumber = :itemnumber
                           WHERE id = :cmid",
                array('itemnumber' => $gradeitem->itemnumber, 'cmid' => $gradeitem->cmid));
        }
        // Quiz savepoint reached.
        upgrade_mod_savepoint(true, 2015111602, 'quiz');
    }

    return true;
}
