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

    //===== 1.9.0 upgrade line ======//

    if ($oldversion < 2008062000) {

        // Define table quiz_report to be created
        $table = new xmldb_table('quiz_report');

        // Adding fields to table quiz_report
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED,
                XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null,
                null, null, null);
        $table->add_field('displayorder', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED,
                XMLDB_NOTNULL, null, null);

        // Adding keys to table quiz_report
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for quiz_report
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        upgrade_mod_savepoint(true, 2008062000, 'quiz');
    }

    if ($oldversion < 2008062001) {
        $reporttoinsert = new stdClass();
        $reporttoinsert->name = 'overview';
        $reporttoinsert->displayorder = 10000;
        $DB->insert_record('quiz_report', $reporttoinsert);

        $reporttoinsert = new stdClass();
        $reporttoinsert->name = 'responses';
        $reporttoinsert->displayorder = 9000;
        $DB->insert_record('quiz_report', $reporttoinsert);

        $reporttoinsert = new stdClass();
        $reporttoinsert->name = 'regrade';
        $reporttoinsert->displayorder = 7000;
        $DB->insert_record('quiz_report', $reporttoinsert);

        $reporttoinsert = new stdClass();
        $reporttoinsert->name = 'grading';
        $reporttoinsert->displayorder = 6000;
        $DB->insert_record('quiz_report', $reporttoinsert);

        upgrade_mod_savepoint(true, 2008062001, 'quiz');
    }

    if ($oldversion < 2008072402) {

        // Define field lastcron to be added to quiz_report
        $table = new xmldb_table('quiz_report');
        $field = new xmldb_field('lastcron', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED,
                XMLDB_NOTNULL, null, '0', 'displayorder');

        // Conditionally launch add field lastcron
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field cron to be added to quiz_report
        $field = new xmldb_field('cron', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED,
                XMLDB_NOTNULL, null, '0', 'lastcron');

        // Conditionally launch add field cron
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // quiz savepoint reached
        upgrade_mod_savepoint(true, 2008072402, 'quiz');
    }

    if ($oldversion < 2008072900) {
        // Delete the regrade report - it is now part of the overview report.
        $DB->delete_records('quiz_report', array('name' => 'regrade'));

        // quiz savepoint reached
        upgrade_mod_savepoint(true, 2008072900, 'quiz');
    }

    if ($oldversion < 2008081500) {
        // Define table quiz_question_versions to be dropped
        $table = new xmldb_table('quiz_question_versions');

        // Launch drop table for quiz_question_versions
        $dbman->drop_table($table);

        // quiz savepoint reached
        upgrade_mod_savepoint(true, 2008081500, 'quiz');
    }

    // Changing the type of all the columns that store grades to be NUMBER(10, 5) or similar.
    if ($oldversion < 2008081501) {
        // First set all quiz.sumgrades to 0 if they are null. This should never
        // happen however some users have encountered a null value there.
        $DB->execute('UPDATE {quiz} SET sumgrades=0 WHERE sumgrades IS NULL');
        $table = new xmldb_table('quiz');
        $field = new xmldb_field('sumgrades', XMLDB_TYPE_NUMBER, '10, 5', null,
                XMLDB_NOTNULL, null, '0', 'questions');
        $dbman->change_field_type($table, $field);
        upgrade_mod_savepoint(true, 2008081501, 'quiz');
    }

    if ($oldversion < 2008081502) {
        $table = new xmldb_table('quiz');
        $field = new xmldb_field('grade', XMLDB_TYPE_NUMBER, '10, 5', null,
                XMLDB_NOTNULL, null, '0', 'sumgrades');
        $dbman->change_field_type($table, $field);
        upgrade_mod_savepoint(true, 2008081502, 'quiz');
    }

    if ($oldversion < 2008081503) {
        // First set all quiz.sumgrades to 0 if they are null. This should never
        // happen however some users have encountered a null value there.
        $DB->execute('UPDATE {quiz_attempts} SET sumgrades=0 WHERE sumgrades IS NULL');
        $table = new xmldb_table('quiz_attempts');
        $field = new xmldb_field('sumgrades', XMLDB_TYPE_NUMBER, '10, 5', null,
                XMLDB_NOTNULL, null, '0', 'attempt');
        $dbman->change_field_type($table, $field);
        upgrade_mod_savepoint(true, 2008081503, 'quiz');
    }

    if ($oldversion < 2008081504) {
        $table = new xmldb_table('quiz_feedback');
        $field = new xmldb_field('mingrade', XMLDB_TYPE_NUMBER, '10, 5', null,
                XMLDB_NOTNULL, null, '0', 'feedbacktext');
        $dbman->change_field_type($table, $field);
        upgrade_mod_savepoint(true, 2008081504, 'quiz');
    }

    if ($oldversion < 2008081505) {
        $table = new xmldb_table('quiz_feedback');
        $field = new xmldb_field('maxgrade', XMLDB_TYPE_NUMBER, '10, 5', null,
                XMLDB_NOTNULL, null, '0', 'mingrade');
        $dbman->change_field_type($table, $field);
        upgrade_mod_savepoint(true, 2008081505, 'quiz');
    }

    if ($oldversion < 2008081506) {
        $table = new xmldb_table('quiz_grades');
        $field = new xmldb_field('grade', XMLDB_TYPE_NUMBER, '10, 5', null,
                XMLDB_NOTNULL, null, '0', 'userid');
        $dbman->change_field_type($table, $field);
        upgrade_mod_savepoint(true, 2008081506, 'quiz');
    }

    if ($oldversion < 2008081507) {
        $table = new xmldb_table('quiz_question_instances');
        $field = new xmldb_field('grade', XMLDB_TYPE_NUMBER, '12, 7', null,
                XMLDB_NOTNULL, null, '0', 'question');
        $dbman->change_field_type($table, $field);
        upgrade_mod_savepoint(true, 2008081507, 'quiz');
    }

    // Move all of the quiz config settings from $CFG to the config_plugins table.
    if ($oldversion < 2008082200) {
        foreach (get_object_vars($CFG) as $name => $value) {
            if (strpos($name, 'quiz_') === 0) {
                $shortname = substr($name, 5);
                if ($shortname == 'fix_adaptive') {
                    // Special case - remove old inconsistency.
                    $shortname == 'fix_optionflags';
                }
                set_config($shortname, $value, 'quiz');
                unset_config($name);
            }
        }
        upgrade_mod_savepoint(true, 2008082200, 'quiz');
    }

    // Now that the quiz is no longer responsible for creating all the question
    // bank tables, and some of the tables are now the responsibility of the
    // datasetdependent question type, which did not have a version.php file before,
    // we need to say that these tables are already installed, otherwise XMLDB
    // will try to create them again and give an error.
    if ($oldversion < 2008082600) {
        // Since MDL-16505 was fixed, and we eliminated the datasetdependent
        // question type, this is now a no-op.
        upgrade_mod_savepoint(true, 2008082600, 'quiz');
    }

    if ($oldversion < 2008112101) {

        // Define field lastcron to be added to quiz_report
        $table = new xmldb_table('quiz_report');
        $field = new xmldb_field('capability', XMLDB_TYPE_CHAR, '255', null,
                null, null, null, 'cron');

        // Conditionally launch add field lastcron
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // quiz savepoint reached
        upgrade_mod_savepoint(true, 2008112101, 'quiz');
    }

    if ($oldversion < 2009010700) {

        // Define field showuserpicture to be added to quiz
        $table = new xmldb_table('quiz');
        $field = new xmldb_field('showuserpicture', XMLDB_TYPE_INTEGER, '4', null,
                XMLDB_NOTNULL, null, '0', 'delay2');

        // Conditionally launch add field showuserpicture
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // quiz savepoint reached
        upgrade_mod_savepoint(true, 2009010700, 'quiz');
    }

    if ($oldversion < 2009030900) {
        // If there are no quiz settings set to advanced yet, the set up the default
        // advanced fields from Moodle 2.0.
        $quizconfig = get_config('quiz');
        $arealreadyadvanced = false;
        foreach (array($quizconfig) as $name => $value) {
            if (strpos($name, 'fix_') === 0 && !empty($value)) {
                $arealreadyadvanced = true;
                break;
            }
        }

        if (!$arealreadyadvanced) {
            set_config('fix_penaltyscheme', 1, 'quiz');
            set_config('fix_attemptonlast', 1, 'quiz');
            set_config('fix_questiondecimalpoints', 1, 'quiz');
            set_config('fix_password', 1, 'quiz');
            set_config('fix_subnet', 1, 'quiz');
            set_config('fix_delay1', 1, 'quiz');
            set_config('fix_delay2', 1, 'quiz');
            set_config('fix_popup', 1, 'quiz');
        }

        // quiz savepoint reached
        upgrade_mod_savepoint(true, 2009030900, 'quiz');
    }

    if ($oldversion < 2009031000) {
        // Add new questiondecimaldigits setting, separate form the overall decimaldigits one.
        $table = new xmldb_table('quiz');
        $field = new xmldb_field('questiondecimalpoints', XMLDB_TYPE_INTEGER, '4', null,
                XMLDB_NOTNULL, null, '-2', 'decimalpoints');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // quiz savepoint reached
        upgrade_mod_savepoint(true, 2009031000, 'quiz');
    }

    if ($oldversion < 2009031001) {
        // Convert quiz.timelimit from minutes to seconds.
        $DB->execute('UPDATE {quiz} SET timelimit = timelimit * 60');
        $default = get_config('quiz', 'timelimit');
        set_config('timelimit', 60 * $default, 'quiz');

        // quiz savepoint reached
        upgrade_mod_savepoint(true, 2009031001, 'quiz');
    }

    if ($oldversion < 2009042000) {

        // Define field introformat to be added to quiz
        $table = new xmldb_table('quiz');
        $field = new xmldb_field('introformat', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED,
                XMLDB_NOTNULL, null, '0', 'intro');

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // conditionally migrate to html format in intro
        if ($CFG->texteditors !== 'textarea') {
            $rs = $DB->get_recordset('quiz', array('introformat' => FORMAT_MOODLE),
                    '', 'id, intro, introformat');
            foreach ($rs as $q) {
                $q->intro       = text_to_html($q->intro, false, false, true);
                $q->introformat = FORMAT_HTML;
                $DB->update_record('quiz', $q);
                upgrade_set_timeout();
            }
            $rs->close();
        }

        // quiz savepoint reached
        upgrade_mod_savepoint(true, 2009042000, 'quiz');
    }

    if ($oldversion < 2010030501) {
        // Define table quiz_overrides to be created
        $table = new xmldb_table('quiz_overrides');

        // Adding fields to table quiz_overrides
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED,
                XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('quiz', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED,
                XMLDB_NOTNULL, null, '0');
        $table->add_field('groupid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED,
                null, null, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED,
                null, null, null);
        $table->add_field('timeopen', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED,
                null, null, null);
        $table->add_field('timeclose', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED,
                null, null, null);
        $table->add_field('timelimit', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED,
                null, null, null);
        $table->add_field('attempts', XMLDB_TYPE_INTEGER, '6', XMLDB_UNSIGNED,
                null, null, null);
        $table->add_field('password', XMLDB_TYPE_CHAR, '255', null, null, null, null);

        // Adding keys to table quiz_overrides
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('quiz', XMLDB_KEY_FOREIGN, array('quiz'), 'quiz', array('id'));
        $table->add_key('groupid', XMLDB_KEY_FOREIGN, array('groupid'), 'groups', array('id'));
        $table->add_key('userid', XMLDB_KEY_FOREIGN, array('userid'), 'user', array('id'));

        // Conditionally launch create table for quiz_overrides
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // quiz savepoint reached
        upgrade_mod_savepoint(true, 2010030501, 'quiz');
    }

    if ($oldversion < 2010051800) {

        // Define field showblocks to be added to quiz
        $table = new xmldb_table('quiz');
        $field = new xmldb_field('showblocks', XMLDB_TYPE_INTEGER, '4', null,
                XMLDB_NOTNULL, null, '0', 'showuserpicture');

        // Conditionally launch add field showblocks
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // quiz savepoint reached
        upgrade_mod_savepoint(true, 2010051800, 'quiz');
    }

    if ($oldversion < 2010080600) {

        // Define field feedbacktextformat to be added to quiz_feedback
        $table = new xmldb_table('quiz_feedback');
        $field = new xmldb_field('feedbacktextformat', XMLDB_TYPE_INTEGER, '2', null,
                XMLDB_NOTNULL, null, '0', 'feedbacktext');

        // Conditionally launch add field feedbacktextformat
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // This column defaults to FORMAT_MOODLE, which is correct.

        // quiz savepoint reached
        upgrade_mod_savepoint(true, 2010080600, 'quiz');
    }

    if ($oldversion < 2010102000) {

        // Define field showblocks to be added to quiz
        // Repeat this step, because the column was missing from install.xml for a time.
        $table = new xmldb_table('quiz');
        $field = new xmldb_field('showblocks', XMLDB_TYPE_INTEGER, '4', null,
                XMLDB_NOTNULL, null, '0', 'showuserpicture');

        // Conditionally launch add field showblocks
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // quiz savepoint reached
        upgrade_mod_savepoint(true, 2010102000, 'quiz');
    }

    if ($oldversion < 2010122300) {
        // Fix quiz in the post table after upgrade from 1.9
        $table = new xmldb_table('quiz');
        $columns = $DB->get_columns('quiz');

        // quiz.questiondecimalpoints should be int (4) not null default -2
        if (array_key_exists('questiondecimalpoints', $columns) &&
                $columns['questiondecimalpoints']->default_value != '-2') {
            $field = new xmldb_field('questiondecimalpoints', XMLDB_TYPE_INTEGER, '4', null,
                    XMLDB_NOTNULL, null, -2, 'decimalpoints');
            if ($dbman->field_exists($table, $field)) {
                $dbman->change_field_default($table, $field);
            }
        }

        // quiz.sumgrades should be decimal(10, 5) not null default 0
        if (array_key_exists('sumgrades', $columns) && empty($columns['sumgrades']->not_null)) {
            // First set all quiz.sumgrades to 0 if they are null. This should never
            // happen however some users have encountered a null value there.
            $DB->execute('UPDATE {quiz} SET sumgrades=0 WHERE sumgrades IS NULL');

            $field = new xmldb_field('sumgrades', XMLDB_TYPE_NUMBER, '10, 5', null,
                    XMLDB_NOTNULL, null, '0', 'questions');
            if ($dbman->field_exists($table, $field)) {
                $dbman->change_field_default($table, $field);
            }
        }

        // quiz.grade should be decimal(10, 5) not null default 0
        if (array_key_exists('grade', $columns) && empty($columns['grade']->not_null)) {
            $field = new xmldb_field('grade', XMLDB_TYPE_NUMBER, '10, 5', null,
                    XMLDB_NOTNULL, null, '0', 'sumgrades');
            if ($dbman->field_exists($table, $field)) {
                $dbman->change_field_default($table, $field);
            }
        }

        upgrade_mod_savepoint(true, 2010122300, 'quiz');
    }

    if ($oldversion < 2010122301) {
        // Fix quiz_attempts in the post table after upgrade from 1.9
        $table = new xmldb_table('quiz_attempts');
        $columns = $DB->get_columns('quiz_attempts');

        // quiz_attempts.sumgrades should be decimal(10, 5) not null default 0
        if (array_key_exists('sumgrades', $columns) && empty($columns['sumgrades']->not_null)) {
            // First set all quiz.sumgrades to 0 if they are null. This should never
            // happen however some users have encountered a null value there.
            $DB->execute('UPDATE {quiz_attempts} SET sumgrades=0 WHERE sumgrades IS NULL');

            $field = new xmldb_field('sumgrades', XMLDB_TYPE_NUMBER, '10, 5', null,
                    XMLDB_NOTNULL, null, '0', 'attempt');
            if ($dbman->field_exists($table, $field)) {
                $dbman->change_field_default($table, $field);
            }
        }

        upgrade_mod_savepoint(true, 2010122301, 'quiz');
    }

    if ($oldversion < 2010122302) {
        // Fix quiz_feedback in the post table after upgrade from 1.9
        $table = new xmldb_table('quiz_feedback');
        $columns = $DB->get_columns('quiz_feedback');

        // quiz_feedback.mingrade should be decimal(10, 5) not null default 0
        if (array_key_exists('mingrade', $columns) && empty($columns['mingrade']->not_null)) {
            $field = new xmldb_field('mingrade', XMLDB_TYPE_NUMBER, '10, 5', null,
                    XMLDB_NOTNULL, null, '0', 'feedbacktextformat');
            if ($dbman->field_exists($table, $field)) {
                $dbman->change_field_default($table, $field);
            }
        }

        // quiz_feedback.maxgrade should be decimal(10, 5) not null default 0
        if (array_key_exists('maxgrade', $columns) && empty($columns['maxgrade']->not_null)) {
            // Fixed in earlier upgrade code
            $field = new xmldb_field('maxgrade', XMLDB_TYPE_NUMBER, '10, 5', null,
                    XMLDB_NOTNULL, null, '0', 'mingrade');
            if ($dbman->field_exists($table, $field)) {
                $dbman->change_field_default($table, $field);
            }
        }

        upgrade_mod_savepoint(true, 2010122302, 'quiz');
    }

    if ($oldversion < 2010122303) {
        // Fix quiz_grades in the post table after upgrade from 1.9
        $table = new xmldb_table('quiz_grades');
        $columns = $DB->get_columns('quiz_grades');

        // quiz_grades.grade should be decimal(10, 5) not null default 0
        if (array_key_exists('grade', $columns) && empty($columns['grade']->not_null)) {
            $field = new xmldb_field('grade', XMLDB_TYPE_NUMBER, '10, 5', null,
                    XMLDB_NOTNULL, null, '0', 'userid');
            if ($dbman->field_exists($table, $field)) {
                $dbman->change_field_default($table, $field);
            }
        }

        upgrade_mod_savepoint(true, 2010122303, 'quiz');
    }

    if ($oldversion < 2010122304) {
        // Fix quiz_question_instances in the post table after upgrade from 1.9
        $table = new xmldb_table('quiz_question_instances');
        $columns = $DB->get_columns('quiz_question_instances');

        // quiz_question_instances.grade should be decimal(12, 7) not null default 0
        if (array_key_exists('grade', $columns) && empty($columns['grade']->not_null)) {
            $field = new xmldb_field('grade', XMLDB_TYPE_NUMBER, '12, 7', null,
                    XMLDB_NOTNULL, null, '0', 'question');
            if ($dbman->field_exists($table, $field)) {
                $dbman->change_field_default($table, $field);
            }
        }

        upgrade_mod_savepoint(true, 2010122304, 'quiz');
    }

    //===== 2.1.0 upgrade line ======//

    // Complete any old upgrade from 1.5 that was never finished.
    if ($oldversion < 2011051199) {
        $table = new xmldb_table('question_states');
        if ($dbman->table_exists($table)) {
            $transaction = $DB->start_delegated_transaction();

            $oldattempts = $DB->get_records_sql('
                SELECT *
                  FROM {quiz_attempts} quiza
                 WHERE uniqueid IN (
                    SELECT DISTINCT qst.attempt
                      FROM {question_states} qst
                      LEFT JOIN {question_sessions} qsess ON
                            qst.question = qsess.questionid AND qst.attempt = qsess.attemptid
                     WHERE qsess.id IS NULL
                )
            ');

            if ($oldattempts) {
                require_once($CFG->dirroot . '/mod/quiz/db/upgradelib.php');

                $pbar = new progress_bar('q15upgrade');
                $pbar->create();
                $a = new stdClass();
                $a->outof = count($oldattempts);
                $a->done = 0;
                $pbar->update($a->done, $a->outof,
                        get_string('upgradingveryoldquizattempts', 'quiz', $a));

                foreach ($oldattempts as $oldattempt) {
                    quiz_upgrade_very_old_question_sessions($oldattempt);

                    $a->done += 1;
                    $pbar->update($a->done, $a->outof,
                            get_string('upgradingveryoldquizattempts', 'quiz', $a));
                }
            }

            $transaction->allow_commit();
        }

        // quiz savepoint reached
        upgrade_mod_savepoint(true, 2011051199, 'quiz');
    }

    // Add new preferredbehaviour column to the quiz table.
    if ($oldversion < 2011051200) {
        $table = new xmldb_table('quiz');
        $field = new xmldb_field('preferredbehaviour');
        $field->set_attributes(XMLDB_TYPE_CHAR, '32', null,
                null, null, null, 'timeclose');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // quiz savepoint reached
        upgrade_mod_savepoint(true, 2011051200, 'quiz');
    }

    // Populate preferredbehaviour column based on old optionflags column.
    if ($oldversion < 2011051201) {
        if ($dbman->field_exists('quiz', 'optionflags')) {
            $DB->set_field_select('quiz', 'preferredbehaviour', 'deferredfeedback',
                    'optionflags = 0');
            $DB->set_field_select('quiz', 'preferredbehaviour', 'adaptive',
                    'optionflags <> 0 AND penaltyscheme <> 0');
            $DB->set_field_select('quiz', 'preferredbehaviour', 'adaptivenopenalty',
                    'optionflags <> 0 AND penaltyscheme = 0');

            set_config('preferredbehaviour', 'deferredfeedback', 'quiz');
            set_config('fix_preferredbehaviour', 0, 'quiz');
        }

        // quiz savepoint reached
        upgrade_mod_savepoint(true, 2011051201, 'quiz');
    }

    // Add a not-NULL constraint to the preferredmodel field now that it is populated.
    if ($oldversion < 2011051202) {
        $table = new xmldb_table('quiz');
        $field = new xmldb_field('preferredbehaviour');
        $field->set_attributes(XMLDB_TYPE_CHAR, '32', null,
                XMLDB_NOTNULL, null, null, 'timeclose');

        $dbman->change_field_notnull($table, $field);

        // quiz savepoint reached
        upgrade_mod_savepoint(true, 2011051202, 'quiz');
    }

    // Drop the old optionflags field.
    if ($oldversion < 2011051203) {
        $table = new xmldb_table('quiz');
        $field = new xmldb_field('optionflags');

        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        unset_config('optionflags', 'quiz');
        unset_config('fix_optionflags', 'quiz');

        // quiz savepoint reached
        upgrade_mod_savepoint(true, 2011051203, 'quiz');
    }

    // Drop the old penaltyscheme field.
    if ($oldversion < 2011051204) {
        $table = new xmldb_table('quiz');
        $field = new xmldb_field('penaltyscheme');

        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        unset_config('penaltyscheme', 'quiz');
        unset_config('fix_penaltyscheme', 'quiz');

        // quiz savepoint reached
        upgrade_mod_savepoint(true, 2011051204, 'quiz');
    }

    if ($oldversion < 2011051205) {

        // Changing nullability of field sumgrades on table quiz_attempts to null
        $table = new xmldb_table('quiz_attempts');
        $field = new xmldb_field('sumgrades');
        $field->set_attributes(XMLDB_TYPE_NUMBER, '10, 5', null,
                null, null, null, 'attempt');

        // Launch change of nullability for field sumgrades
        $dbman->change_field_notnull($table, $field);

        // Launch change of default for field sumgrades
        $dbman->change_field_default($table, $field);

        // quiz savepoint reached
        upgrade_mod_savepoint(true, 2011051205, 'quiz');
    }

    if ($oldversion < 2011051207) {

        // Define field reviewattempt to be added to quiz
        $table = new xmldb_table('quiz');
        $field = new xmldb_field('reviewattempt');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '6', XMLDB_UNSIGNED,
                XMLDB_NOTNULL, null, '0', 'review');

        // Launch add field reviewattempt
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // quiz savepoint reached
        upgrade_mod_savepoint(true, 2011051207, 'quiz');
    }

    if ($oldversion < 2011051208) {

        // Define field reviewattempt to be added to quiz
        $table = new xmldb_table('quiz');
        $field = new xmldb_field('reviewcorrectness');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '6', XMLDB_UNSIGNED,
                XMLDB_NOTNULL, null, '0', 'reviewattempt');

        // Launch add field reviewattempt
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // quiz savepoint reached
        upgrade_mod_savepoint(true, 2011051208, 'quiz');
    }

    if ($oldversion < 2011051209) {

        // Define field reviewattempt to be added to quiz
        $table = new xmldb_table('quiz');
        $field = new xmldb_field('reviewmarks');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '6', XMLDB_UNSIGNED,
                XMLDB_NOTNULL, null, '0', 'reviewcorrectness');

        // Launch add field reviewattempt
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // quiz savepoint reached
        upgrade_mod_savepoint(true, 2011051209, 'quiz');
    }

    if ($oldversion < 2011051210) {

        // Define field reviewattempt to be added to quiz
        $table = new xmldb_table('quiz');
        $field = new xmldb_field('reviewspecificfeedback');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '6', XMLDB_UNSIGNED,
                XMLDB_NOTNULL, null, '0', 'reviewmarks');

        // Launch add field reviewattempt
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // quiz savepoint reached
        upgrade_mod_savepoint(true, 2011051210, 'quiz');
    }

    if ($oldversion < 2011051211) {

        // Define field reviewattempt to be added to quiz
        $table = new xmldb_table('quiz');
        $field = new xmldb_field('reviewgeneralfeedback');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '6', XMLDB_UNSIGNED,
                XMLDB_NOTNULL, null, '0', 'reviewspecificfeedback');

        // Launch add field reviewattempt
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // quiz savepoint reached
        upgrade_mod_savepoint(true, 2011051211, 'quiz');
    }

    if ($oldversion < 2011051212) {

        // Define field reviewattempt to be added to quiz
        $table = new xmldb_table('quiz');
        $field = new xmldb_field('reviewrightanswer');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '6', XMLDB_UNSIGNED,
                XMLDB_NOTNULL, null, '0', 'reviewgeneralfeedback');

        // Launch add field reviewattempt
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // quiz savepoint reached
        upgrade_mod_savepoint(true, 2011051212, 'quiz');
    }

    if ($oldversion < 2011051213) {

        // Define field reviewattempt to be added to quiz
        $table = new xmldb_table('quiz');
        $field = new xmldb_field('reviewoverallfeedback');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '6', XMLDB_UNSIGNED,
                XMLDB_NOTNULL, null, '0', 'reviewrightanswer');

        // Launch add field reviewattempt
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // quiz savepoint reached
        upgrade_mod_savepoint(true, 2011051213, 'quiz');
    }

    define('QUIZ_NEW_DURING',            0x10000);
    define('QUIZ_NEW_IMMEDIATELY_AFTER', 0x01000);
    define('QUIZ_NEW_LATER_WHILE_OPEN',  0x00100);
    define('QUIZ_NEW_AFTER_CLOSE',       0x00010);

    define('QUIZ_OLD_IMMEDIATELY', 0x3c003f);
    define('QUIZ_OLD_OPEN',        0x3c00fc0);
    define('QUIZ_OLD_CLOSED',      0x3c03f000);

    define('QUIZ_OLD_RESPONSES',        1*0x1041); // Show responses
    define('QUIZ_OLD_SCORES',           2*0x1041); // Show scores
    define('QUIZ_OLD_FEEDBACK',         4*0x1041); // Show question feedback
    define('QUIZ_OLD_ANSWERS',          8*0x1041); // Show correct answers
    define('QUIZ_OLD_SOLUTIONS',       16*0x1041); // Show solutions
    define('QUIZ_OLD_GENERALFEEDBACK', 32*0x1041); // Show question general feedback
    define('QUIZ_OLD_OVERALLFEEDBACK',  1*0x4440000); // Show quiz overall feedback

    // Copy the old review settings
    if ($oldversion < 2011051214) {
        if ($dbman->field_exists('quiz', 'review')) {
            $DB->execute("
            UPDATE {quiz}
            SET reviewattempt = " . $DB->sql_bitor($DB->sql_bitor(
                QUIZ_NEW_DURING,
                'CASE WHEN ' . $DB->sql_bitand('review', QUIZ_OLD_IMMEDIATELY & QUIZ_OLD_RESPONSES) .
                    ' <> 0 THEN ' . QUIZ_NEW_IMMEDIATELY_AFTER . ' ELSE 0 END'), $DB->sql_bitor(
                'CASE WHEN ' . $DB->sql_bitand('review', QUIZ_OLD_OPEN & QUIZ_OLD_RESPONSES) .
                    ' <> 0 THEN ' . QUIZ_NEW_LATER_WHILE_OPEN . ' ELSE 0 END',
                'CASE WHEN ' . $DB->sql_bitand('review', QUIZ_OLD_CLOSED & QUIZ_OLD_RESPONSES) .
                    ' <> 0 THEN ' . QUIZ_NEW_AFTER_CLOSE . ' ELSE 0 END')) . "
            ");
        }

        // quiz savepoint reached
        upgrade_mod_savepoint(true, 2011051214, 'quiz');
    }

    if ($oldversion < 2011051215) {
        if ($dbman->field_exists('quiz', 'review')) {
            $DB->execute("
            UPDATE {quiz}
            SET reviewcorrectness = " . $DB->sql_bitor($DB->sql_bitor(
                QUIZ_NEW_DURING,
                'CASE WHEN ' . $DB->sql_bitand('review', QUIZ_OLD_IMMEDIATELY & QUIZ_OLD_SCORES) .
                    ' <> 0 THEN ' . QUIZ_NEW_IMMEDIATELY_AFTER . ' ELSE 0 END'), $DB->sql_bitor(
                'CASE WHEN ' . $DB->sql_bitand('review', QUIZ_OLD_OPEN & QUIZ_OLD_SCORES) .
                    ' <> 0 THEN ' . QUIZ_NEW_LATER_WHILE_OPEN . ' ELSE 0 END',
                'CASE WHEN ' . $DB->sql_bitand('review', QUIZ_OLD_CLOSED & QUIZ_OLD_SCORES) .
                    ' <> 0 THEN ' . QUIZ_NEW_AFTER_CLOSE . ' ELSE 0 END')) . "
            ");
        }

        // quiz savepoint reached
        upgrade_mod_savepoint(true, 2011051215, 'quiz');
    }

    if ($oldversion < 2011051216) {
        if ($dbman->field_exists('quiz', 'review')) {
            $DB->execute("
            UPDATE {quiz}
            SET reviewmarks = " . $DB->sql_bitor($DB->sql_bitor(
                QUIZ_NEW_DURING,
                'CASE WHEN ' . $DB->sql_bitand('review', QUIZ_OLD_IMMEDIATELY & QUIZ_OLD_SCORES) .
                    ' <> 0 THEN ' . QUIZ_NEW_IMMEDIATELY_AFTER . ' ELSE 0 END'), $DB->sql_bitor(
                'CASE WHEN ' . $DB->sql_bitand('review', QUIZ_OLD_OPEN & QUIZ_OLD_SCORES) .
                    ' <> 0 THEN ' . QUIZ_NEW_LATER_WHILE_OPEN . ' ELSE 0 END',
                'CASE WHEN ' . $DB->sql_bitand('review', QUIZ_OLD_CLOSED & QUIZ_OLD_SCORES) .
                    ' <> 0 THEN ' . QUIZ_NEW_AFTER_CLOSE . ' ELSE 0 END')) . "
            ");
        }

        // quiz savepoint reached
        upgrade_mod_savepoint(true, 2011051216, 'quiz');
    }

    if ($oldversion < 2011051217) {
        if ($dbman->field_exists('quiz', 'review')) {
            $DB->execute("
            UPDATE {quiz}
            SET reviewspecificfeedback = " . $DB->sql_bitor($DB->sql_bitor(
                'CASE WHEN ' . $DB->sql_bitand('review', QUIZ_OLD_IMMEDIATELY & QUIZ_OLD_FEEDBACK) .
                    ' <> 0 THEN ' . QUIZ_NEW_DURING . ' ELSE 0 END',
                'CASE WHEN ' . $DB->sql_bitand('review', QUIZ_OLD_IMMEDIATELY & QUIZ_OLD_FEEDBACK) .
                    ' <> 0 THEN ' . QUIZ_NEW_IMMEDIATELY_AFTER . ' ELSE 0 END'), $DB->sql_bitor(
                'CASE WHEN ' . $DB->sql_bitand('review', QUIZ_OLD_OPEN & QUIZ_OLD_FEEDBACK) .
                    ' <> 0 THEN ' . QUIZ_NEW_LATER_WHILE_OPEN . ' ELSE 0 END',
                'CASE WHEN ' . $DB->sql_bitand('review', QUIZ_OLD_CLOSED & QUIZ_OLD_FEEDBACK) .
                    ' <> 0 THEN ' . QUIZ_NEW_AFTER_CLOSE . ' ELSE 0 END')) . "
            ");
        }

        // quiz savepoint reached
        upgrade_mod_savepoint(true, 2011051217, 'quiz');
    }

    if ($oldversion < 2011051218) {
        if ($dbman->field_exists('quiz', 'review')) {
            $DB->execute("
            UPDATE {quiz}
            SET reviewgeneralfeedback = " . $DB->sql_bitor($DB->sql_bitor(
                'CASE WHEN ' . $DB->sql_bitand('review', QUIZ_OLD_IMMEDIATELY & QUIZ_OLD_GENERALFEEDBACK) .
                    ' <> 0 THEN ' . QUIZ_NEW_DURING . ' ELSE 0 END',
                'CASE WHEN ' . $DB->sql_bitand('review', QUIZ_OLD_IMMEDIATELY & QUIZ_OLD_GENERALFEEDBACK) .
                    ' <> 0 THEN ' . QUIZ_NEW_IMMEDIATELY_AFTER . ' ELSE 0 END'), $DB->sql_bitor(
                'CASE WHEN ' . $DB->sql_bitand('review', QUIZ_OLD_OPEN & QUIZ_OLD_GENERALFEEDBACK) .
                    ' <> 0 THEN ' . QUIZ_NEW_LATER_WHILE_OPEN . ' ELSE 0 END',
                'CASE WHEN ' . $DB->sql_bitand('review', QUIZ_OLD_CLOSED & QUIZ_OLD_GENERALFEEDBACK) .
                    ' <> 0 THEN ' . QUIZ_NEW_AFTER_CLOSE . ' ELSE 0 END')) . "
            ");
        }

        // quiz savepoint reached
        upgrade_mod_savepoint(true, 2011051218, 'quiz');
    }

    if ($oldversion < 2011051219) {
        if ($dbman->field_exists('quiz', 'review')) {
            $DB->execute("
            UPDATE {quiz}
            SET reviewrightanswer = " . $DB->sql_bitor($DB->sql_bitor(
                'CASE WHEN ' . $DB->sql_bitand('review', QUIZ_OLD_IMMEDIATELY & QUIZ_OLD_ANSWERS) .
                    ' <> 0 THEN ' . QUIZ_NEW_DURING . ' ELSE 0 END',
                'CASE WHEN ' . $DB->sql_bitand('review', QUIZ_OLD_IMMEDIATELY & QUIZ_OLD_ANSWERS) .
                    ' <> 0 THEN ' . QUIZ_NEW_IMMEDIATELY_AFTER . ' ELSE 0 END'), $DB->sql_bitor(
                'CASE WHEN ' . $DB->sql_bitand('review', QUIZ_OLD_OPEN & QUIZ_OLD_ANSWERS) .
                    ' <> 0 THEN ' . QUIZ_NEW_LATER_WHILE_OPEN . ' ELSE 0 END',
                'CASE WHEN ' . $DB->sql_bitand('review', QUIZ_OLD_CLOSED & QUIZ_OLD_ANSWERS) .
                    ' <> 0 THEN ' . QUIZ_NEW_AFTER_CLOSE . ' ELSE 0 END')) . "
            ");
        }

        // quiz savepoint reached
        upgrade_mod_savepoint(true, 2011051219, 'quiz');
    }

    if ($oldversion < 2011051220) {
        if ($dbman->field_exists('quiz', 'review')) {
            $DB->execute("
            UPDATE {quiz}
            SET reviewoverallfeedback = " . $DB->sql_bitor($DB->sql_bitor(
                0,
                'CASE WHEN ' . $DB->sql_bitand('review', QUIZ_OLD_IMMEDIATELY & QUIZ_OLD_OVERALLFEEDBACK) .
                    ' <> 0 THEN ' . QUIZ_NEW_IMMEDIATELY_AFTER . ' ELSE 0 END'), $DB->sql_bitor(
                'CASE WHEN ' . $DB->sql_bitand('review', QUIZ_OLD_OPEN & QUIZ_OLD_OVERALLFEEDBACK) .
                    ' <> 0 THEN ' . QUIZ_NEW_LATER_WHILE_OPEN . ' ELSE 0 END',
                'CASE WHEN ' . $DB->sql_bitand('review', QUIZ_OLD_CLOSED & QUIZ_OLD_OVERALLFEEDBACK) .
                    ' <> 0 THEN ' . QUIZ_NEW_AFTER_CLOSE . ' ELSE 0 END')) . "
            ");
        }

        // quiz savepoint reached
        upgrade_mod_savepoint(true, 2011051220, 'quiz');
    }

    // And, do the same for the defaults
    if ($oldversion < 2011051221) {
        $quizrevew = get_config('quiz', 'review');
        if (!empty($quizrevew)) {

            set_config('reviewattempt',
                    QUIZ_NEW_DURING |
                    ($quizrevew & QUIZ_OLD_IMMEDIATELY & QUIZ_OLD_RESPONSES ? QUIZ_NEW_IMMEDIATELY_AFTER : 0) |
                    ($quizrevew & QUIZ_OLD_OPEN & QUIZ_OLD_RESPONSES ? QUIZ_NEW_LATER_WHILE_OPEN : 0) |
                    ($quizrevew & QUIZ_OLD_CLOSED & QUIZ_OLD_RESPONSES ? QUIZ_NEW_AFTER_CLOSE : 0),
                    'quiz');

            set_config('reviewcorrectness',
                    QUIZ_NEW_DURING |
                    ($quizrevew & QUIZ_OLD_IMMEDIATELY & QUIZ_OLD_SCORES ? QUIZ_NEW_IMMEDIATELY_AFTER : 0) |
                    ($quizrevew & QUIZ_OLD_OPEN & QUIZ_OLD_SCORES ? QUIZ_NEW_LATER_WHILE_OPEN : 0) |
                    ($quizrevew & QUIZ_OLD_CLOSED & QUIZ_OLD_SCORES ? QUIZ_NEW_AFTER_CLOSE : 0),
                    'quiz');

            set_config('reviewmarks',
                    QUIZ_NEW_DURING |
                    ($quizrevew & QUIZ_OLD_IMMEDIATELY & QUIZ_OLD_SCORES ? QUIZ_NEW_IMMEDIATELY_AFTER : 0) |
                    ($quizrevew & QUIZ_OLD_OPEN & QUIZ_OLD_SCORES ? QUIZ_NEW_LATER_WHILE_OPEN : 0) |
                    ($quizrevew & QUIZ_OLD_CLOSED & QUIZ_OLD_SCORES ? QUIZ_NEW_AFTER_CLOSE : 0),
                    'quiz');

            set_config('reviewspecificfeedback',
                    ($quizrevew & QUIZ_OLD_IMMEDIATELY & QUIZ_OLD_FEEDBACK ? QUIZ_NEW_DURING : 0) |
                    ($quizrevew & QUIZ_OLD_IMMEDIATELY & QUIZ_OLD_FEEDBACK ? QUIZ_NEW_IMMEDIATELY_AFTER : 0) |
                    ($quizrevew & QUIZ_OLD_OPEN & QUIZ_OLD_FEEDBACK ? QUIZ_NEW_LATER_WHILE_OPEN : 0) |
                    ($quizrevew & QUIZ_OLD_CLOSED & QUIZ_OLD_FEEDBACK ? QUIZ_NEW_AFTER_CLOSE : 0),
                    'quiz');

            set_config('reviewgeneralfeedback',
                    ($quizrevew & QUIZ_OLD_IMMEDIATELY & QUIZ_OLD_GENERALFEEDBACK ? QUIZ_NEW_DURING : 0) |
                    ($quizrevew & QUIZ_OLD_IMMEDIATELY & QUIZ_OLD_GENERALFEEDBACK ? QUIZ_NEW_IMMEDIATELY_AFTER : 0) |
                    ($quizrevew & QUIZ_OLD_OPEN & QUIZ_OLD_GENERALFEEDBACK ? QUIZ_NEW_LATER_WHILE_OPEN : 0) |
                    ($quizrevew & QUIZ_OLD_CLOSED & QUIZ_OLD_GENERALFEEDBACK ? QUIZ_NEW_AFTER_CLOSE : 0),
                    'quiz');

            set_config('reviewrightanswer',
                    ($quizrevew & QUIZ_OLD_IMMEDIATELY & QUIZ_OLD_ANSWERS ? QUIZ_NEW_DURING : 0) |
                    ($quizrevew & QUIZ_OLD_IMMEDIATELY & QUIZ_OLD_ANSWERS ? QUIZ_NEW_IMMEDIATELY_AFTER : 0) |
                    ($quizrevew & QUIZ_OLD_OPEN & QUIZ_OLD_ANSWERS ? QUIZ_NEW_LATER_WHILE_OPEN : 0) |
                    ($quizrevew & QUIZ_OLD_CLOSED & QUIZ_OLD_ANSWERS ? QUIZ_NEW_AFTER_CLOSE : 0),
                    'quiz');

            set_config('reviewoverallfeedback',
                    0 |
                    ($quizrevew & QUIZ_OLD_IMMEDIATELY & QUIZ_OLD_OVERALLFEEDBACK ? QUIZ_NEW_IMMEDIATELY_AFTER : 0) |
                    ($quizrevew & QUIZ_OLD_OPEN & QUIZ_OLD_OVERALLFEEDBACK ? QUIZ_NEW_LATER_WHILE_OPEN : 0) |
                    ($quizrevew & QUIZ_OLD_CLOSED & QUIZ_OLD_OVERALLFEEDBACK ? QUIZ_NEW_AFTER_CLOSE : 0),
                    'quiz');
        }

        // quiz savepoint reached
        upgrade_mod_savepoint(true, 2011051221, 'quiz');
    }

    // Finally drop the old column
    if ($oldversion < 2011051222) {
        // Define field review to be dropped from quiz
        $table = new xmldb_table('quiz');
        $field = new xmldb_field('review');

        // Launch drop field review
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // quiz savepoint reached
        upgrade_mod_savepoint(true, 2011051222, 'quiz');
    }

    if ($oldversion < 2011051223) {
        unset_config('review', 'quiz');

        // quiz savepoint reached
        upgrade_mod_savepoint(true, 2011051223, 'quiz');
    }

    if ($oldversion < 2011051225) {
        // Define table quiz_report to be renamed to quiz_reports
        $table = new xmldb_table('quiz_report');

        // Launch rename table for quiz_reports
        if ($dbman->table_exists($table)) {
            $dbman->rename_table($table, 'quiz_reports');
        }

        upgrade_mod_savepoint(true, 2011051225, 'quiz');
    }

    if ($oldversion < 2011051226) {
        // Define index name (unique) to be added to quiz_reports
        $table = new xmldb_table('quiz_reports');
        $index = new xmldb_index('name', XMLDB_INDEX_UNIQUE, array('name'));

        // Conditionally launch add index name
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        upgrade_mod_savepoint(true, 2011051226, 'quiz');
    }

    if ($oldversion < 2011051227) {

        // Changing nullability of field sumgrades on table quiz_attempts to null
        $table = new xmldb_table('quiz_attempts');
        $field = new xmldb_field('sumgrades', XMLDB_TYPE_NUMBER, '10, 5', null,
                null, null, null, 'attempt');

        // Launch change of nullability for field sumgrades
        $dbman->change_field_notnull($table, $field);

        // quiz savepoint reached
        upgrade_mod_savepoint(true, 2011051227, 'quiz');
    }

    if ($oldversion < 2011051228) {
        // Define field needsupgradetonewqe to be added to quiz_attempts
        $table = new xmldb_table('quiz_attempts');
        $field = new xmldb_field('needsupgradetonewqe', XMLDB_TYPE_INTEGER, '3', XMLDB_UNSIGNED,
                XMLDB_NOTNULL, null, '0', 'preview');

        // Launch add field needsupgradetonewqe
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $DB->set_field('quiz_attempts', 'needsupgradetonewqe', 1);

        // quiz savepoint reached
        upgrade_mod_savepoint(true, 2011051228, 'quiz');
    }

    if ($oldversion < 2011051229) {
        $table = new xmldb_table('question_states');
        if ($dbman->table_exists($table)) {
            // First delete all data from preview attempts.
            $DB->delete_records_select('question_states',
                    "attempt IN (SELECT uniqueid FROM {quiz_attempts} WHERE preview = 1)");
            $DB->delete_records_select('question_sessions',
                    "attemptid IN (SELECT uniqueid FROM {quiz_attempts} WHERE preview = 1)");
            $DB->delete_records('quiz_attempts', array('preview' => 1));

            // Now update all the old attempt data.
            $oldrcachesetting = $CFG->rcache;
            $CFG->rcache = false;

            require_once($CFG->dirroot . '/question/engine/upgrade/upgradelib.php');
            $upgrader = new question_engine_attempt_upgrader();
            $upgrader->convert_all_quiz_attempts();

            $CFG->rcache = $oldrcachesetting;
        }

        // quiz savepoint reached
        upgrade_mod_savepoint(true, 2011051229, 'quiz');
    }

    // Moodle v2.1.0 release upgrade line
    // Put any upgrade step following this

    return true;
}

