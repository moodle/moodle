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
 * @package    mod
 * @subpackage quiz
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
        foreach (get_plugin_list('qtype') as $qtypename => $path) {
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
        // Define index userid to be dropped form quiz_attempts
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
    // Put any upgrade step following this

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

        // Define field timecheckstate to be added to quiz_attempts
        $table = new xmldb_table('quiz_attempts');
        $field = new xmldb_field('timecheckstate', XMLDB_TYPE_INTEGER, '10', null, null, null, '0', 'timemodified');

        // Conditionally launch add field timecheckstate
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define index state-timecheckstate (not unique) to be added to quiz_attempts
        $table = new xmldb_table('quiz_attempts');
        $index = new xmldb_index('state-timecheckstate', XMLDB_INDEX_NOTUNIQUE, array('state', 'timecheckstate'));

        // Conditionally launch add index state-timecheckstate
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Overdue cron no longer needs these
        unset_config('overduelastrun', 'quiz');
        unset_config('overduedoneto', 'quiz');

        // Update timecheckstate on all open attempts
        require_once($CFG->dirroot . '/mod/quiz/locallib.php');
        quiz_update_open_attempts(array());

        // quiz savepoint reached
        upgrade_mod_savepoint(true, 2012100801, 'quiz');
    }

    // Moodle v2.4.0 release upgrade line
    // Put any upgrade step following this

    if ($oldversion < 2013031900) {
        // Quiz manual grading UI should be controlled by mod/quiz:grade, not :viewreports.
        $DB->set_field('quiz_reports', 'capability', 'mod/quiz:grade', array('name' => 'grading'));

        // Mod quiz savepoint reached.
        upgrade_mod_savepoint(true, 2013031900, 'quiz');
    }

    // Moodle v2.5.0 release upgrade line.
    // Put any upgrade step following this.


    return true;
}

