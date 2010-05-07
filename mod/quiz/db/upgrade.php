<?php

// This file keeps track of upgrades to
// the quiz module
//
// Sometimes, changes between versions involve
// alterations to database structures and other
// major things that may break installations.
//
// The upgrade function in this file will attempt
// to perform all the necessary actions to upgrade
// your older installtion to the current version.
//
// If there's something it cannot do itself, it
// will tell you what you need to do.
//
// The commands in here will all be database-neutral,
// using the methods of database_manager class
//
// Please do not forget to use upgrade_set_timeout()
// before any action that may take longer time to finish.

function xmldb_quiz_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();
    $result = true;

//===== 1.9.0 upgrade line ======//

    if ($result && $oldversion < 2008062000) {

    /// Define table quiz_report to be created
        $table = new xmldb_table('quiz_report');

    /// Adding fields to table quiz_report
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('displayorder', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);

    /// Adding keys to table quiz_report
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

    /// Conditionally launch create table for quiz_report
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        upgrade_mod_savepoint($result, 2008062000, 'quiz');
    }

    if ($result && $oldversion < 2008062001) {
        $reporttoinsert = new object();
        $reporttoinsert->name = 'overview';
        $reporttoinsert->displayorder = 10000;
        $result = $result && $DB->insert_record('quiz_report', $reporttoinsert);

        $reporttoinsert = new object();
        $reporttoinsert->name = 'responses';
        $reporttoinsert->displayorder = 9000;
        $result = $result && $DB->insert_record('quiz_report', $reporttoinsert);

        $reporttoinsert = new object();
        $reporttoinsert->name = 'regrade';
        $reporttoinsert->displayorder = 7000;
        $result = $result && $DB->insert_record('quiz_report', $reporttoinsert);

        $reporttoinsert = new object();
        $reporttoinsert->name = 'grading';
        $reporttoinsert->displayorder = 6000;
        $result = $result && $DB->insert_record('quiz_report', $reporttoinsert);

        upgrade_mod_savepoint($result, 2008062001, 'quiz');
    }

    if ($result && $oldversion < 2008072402) {

    /// Define field lastcron to be added to quiz_report
        $table = new xmldb_table('quiz_report');
        $field = new xmldb_field('lastcron', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'displayorder');

    /// Conditionally launch add field lastcron
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

    /// Define field cron to be added to quiz_report
        $field = new xmldb_field('cron', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'lastcron');

    /// Conditionally launch add field cron
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

    /// quiz savepoint reached
        upgrade_mod_savepoint($result, 2008072402, 'quiz');
    }

    if ($result && $oldversion < 2008072900) {
    /// Delete the regrade report - it is now part of the overview report.
        $result = $result && $DB->delete_records('quiz_report', array('name' => 'regrade'));

    /// quiz savepoint reached
        upgrade_mod_savepoint($result, 2008072900, 'quiz');
    }

    if ($result && $oldversion < 2008081500) {
    /// Define table quiz_question_versions to be dropped
        $table = new xmldb_table('quiz_question_versions');

    /// Launch drop table for quiz_question_versions
        $dbman->drop_table($table);

    /// quiz savepoint reached
        upgrade_mod_savepoint($result, 2008081500, 'quiz');
    }

    /// Changing the type of all the columns that store grades to be NUMBER(10, 5) or similar.
    if ($result && $oldversion < 2008081501) {
        $table = new xmldb_table('quiz');
        $field = new xmldb_field('sumgrades', XMLDB_TYPE_NUMBER, '10, 5', null, null, null, null, 'questions');
        $dbman->change_field_type($table, $field);
        upgrade_mod_savepoint($result, 2008081501, 'quiz');
    }

    if ($result && $oldversion < 2008081502) {
        $table = new xmldb_table('quiz');
        $field = new xmldb_field('grade', XMLDB_TYPE_NUMBER, '10, 5', null, null, null, null, 'sumgrades');
        $dbman->change_field_type($table, $field);
        upgrade_mod_savepoint($result, 2008081502, 'quiz');
    }

    if ($result && $oldversion < 2008081503) {
        $table = new xmldb_table('quiz_attempts');
        $field = new xmldb_field('sumgrades', XMLDB_TYPE_NUMBER, '10, 5', null, null, null, null, 'attempt');
        $dbman->change_field_type($table, $field);
        upgrade_mod_savepoint($result, 2008081503, 'quiz');
    }

    if ($result && $oldversion < 2008081504) {
        $table = new xmldb_table('quiz_feedback');
        $field = new xmldb_field('mingrade', XMLDB_TYPE_NUMBER, '10, 5', null, null, null, null, 'feedbacktext');
        $dbman->change_field_type($table, $field);
        upgrade_mod_savepoint($result, 2008081504, 'quiz');
    }

    if ($result && $oldversion < 2008081505) {
        $table = new xmldb_table('quiz_feedback');
        $field = new xmldb_field('maxgrade', XMLDB_TYPE_NUMBER, '10, 5', null, null, null, null, 'mingrade');
        $dbman->change_field_type($table, $field);
        upgrade_mod_savepoint($result, 2008081505, 'quiz');
    }

    if ($result && $oldversion < 2008081506) {
        $table = new xmldb_table('quiz_grades');
        $field = new xmldb_field('grade', XMLDB_TYPE_NUMBER, '10, 5', null, null, null, null, 'userid');
        $dbman->change_field_type($table, $field);
        upgrade_mod_savepoint($result, 2008081506, 'quiz');
    }

    if ($result && $oldversion < 2008081507) {
        $table = new xmldb_table('quiz_question_instances');
        $field = new xmldb_field('grade', XMLDB_TYPE_NUMBER, '12, 7', null, null, null, null, 'question');
        $dbman->change_field_type($table, $field);
        upgrade_mod_savepoint($result, 2008081507, 'quiz');
    }

    /// Move all of the quiz config settings from $CFG to the config_plugins table.
    if ($result && $oldversion < 2008082200) {
        foreach (get_object_vars($CFG) as $name => $value) {
            if (strpos($name, 'quiz_') === 0) {
                $shortname = substr($name, 5);
                if ($shortname == 'fix_adaptive') {
                    // Special case - remove old inconsistency.
                    $shortname == 'fix_optionflags';
                }
                $result = $result && set_config($shortname, $value, 'quiz');
                $result = $result && unset_config($name);
            }
        }
        upgrade_mod_savepoint($result, 2008082200, 'quiz');
    }

    /// Now that the quiz is no longer responsible for creating all the question
    /// bank tables, and some of the tables are now the responsibility of the
    /// datasetdependent question type, which did not have a version.php file before,
    /// we need to say that these tables are already installed, otherwise XMLDB
    /// will try to create them again and give an error.
    if ($result && $oldversion < 2008082600) {
        // Since MDL-16505 was fixed, and we eliminated the datasetdependent
        // question type, this is now a no-op.
        upgrade_mod_savepoint($result, 2008082600, 'quiz');
    }

    if ($result && $oldversion < 2008112101) {

    /// Define field lastcron to be added to quiz_report
        $table = new xmldb_table('quiz_report');
        $field = new xmldb_field('capability', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'cron');

    /// Conditionally launch add field lastcron
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

    /// quiz savepoint reached
        upgrade_mod_savepoint($result, 2008112101, 'quiz');
    }

    if ($result && $oldversion < 2009010700) {

    /// Define field showuserpicture to be added to quiz
        $table = new xmldb_table('quiz');
        $field = new xmldb_field('showuserpicture', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '0', 'delay2');

    /// Conditionally launch add field showuserpicture
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

    /// quiz savepoint reached
        upgrade_mod_savepoint($result, 2009010700, 'quiz');
    }

    if ($result && $oldversion < 2009030900) {
    /// If there are no quiz settings set to advanced yet, the set up the default
    /// advanced fields from Moodle 2.0.
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

    /// quiz savepoint reached
        upgrade_mod_savepoint($result, 2009030900, 'quiz');
    }

    if ($result && $oldversion < 2009031000) {
    /// Add new questiondecimaldigits setting, separate form the overall decimaldigits one.
        $table = new xmldb_table('quiz');
        $field = new xmldb_field('questiondecimalpoints', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '2', 'decimalpoints');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

    /// quiz savepoint reached
        upgrade_mod_savepoint($result, 2009031000, 'quiz');
    }

    if ($result && $oldversion < 2009031001) {
    /// Convert quiz.timelimit from minutes to seconds.
        $DB->execute('UPDATE {quiz} SET timelimit = timelimit * 60');
        $default = get_config('quiz', 'timelimit');
        set_config('timelimit', 60 * $default, 'quiz');

    /// quiz savepoint reached
        upgrade_mod_savepoint($result, 2009031001, 'quiz');
    }

    if ($result && $oldversion < 2009042000) {

    /// Define field introformat to be added to quiz
        $table = new xmldb_table('quiz');
        $field = new xmldb_field('introformat', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'intro');

    /// Launch add field introformat
        $dbman->add_field($table, $field);

    /// set format to current
        $DB->set_field('quiz', 'introformat', FORMAT_MOODLE, array());

    /// quiz savepoint reached
        upgrade_mod_savepoint($result, 2009042000, 'quiz');
    }

    if ($result && $oldversion < 2010030501) {
    /// fix log actions
        update_log_display_entry('quiz', 'edit override', 'quiz', 'name');
        update_log_display_entry('quiz', 'delete override', 'quiz', 'name');

    /// Define table quiz_overrides to be created
        $table = new xmldb_table('quiz_overrides');

    /// Adding fields to table quiz_overrides
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('quiz', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('groupid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null);
        $table->add_field('timeopen', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null);
        $table->add_field('timeclose', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null);
        $table->add_field('timelimit', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null);
        $table->add_field('attempts', XMLDB_TYPE_INTEGER, '6', XMLDB_UNSIGNED, null, null, null);
        $table->add_field('password', XMLDB_TYPE_CHAR, '255', null, null, null, null);

    /// Adding keys to table quiz_overrides
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('quiz', XMLDB_KEY_FOREIGN, array('quiz'), 'quiz', array('id'));
        $table->add_key('groupid', XMLDB_KEY_FOREIGN, array('groupid'), 'groups', array('id'));
        $table->add_key('userid', XMLDB_KEY_FOREIGN, array('userid'), 'user', array('id'));

    /// Conditionally launch create table for quiz_overrides
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

    /// quiz savepoint reached
        upgrade_mod_savepoint($result, 2010030501, 'quiz');
    }

    return $result;
}

