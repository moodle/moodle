<?php

function xmldb_local_qedatabase_install() {
    global $DB;
    $dbman = $DB->get_manager();

    // Bit of a hack to prevent errors like "Cannot downgrade local_qedatabase from ... to ...".
    $oldversion = 2008000000;
    $DB->set_field('config_plugins', 'value', $oldversion,
            array('plugin' => 'local_qedatabase', 'name' => 'version'));

    // Add new preferredbehaviour column to the quiz table.
    if ($oldversion < 2008000100) {
        $table = new xmldb_table('quiz');
        $field = new xmldb_field('preferredbehaviour');
        $field->set_attributes(XMLDB_TYPE_CHAR, '32', null, null, null, null, 'timeclose');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // quiz savepoint reached
        upgrade_plugin_savepoint(true, 2008000100, 'local', 'qedatabase');
    }

    // Populate preferredbehaviour column based on old optionflags column.
    if ($oldversion < 2008000101) {
        $DB->set_field_select('quiz', 'preferredbehaviour', 'deferredfeedback',
                'optionflags = 0');
        $DB->set_field_select('quiz', 'preferredbehaviour', 'adaptive',
                'optionflags <> 0 AND penaltyscheme <> 0');
        $DB->set_field_select('quiz', 'preferredbehaviour', 'adaptivenopenalty',
                'optionflags <> 0 AND penaltyscheme = 0');

        set_config('preferredbehaviour', 'deferredfeedback', 'quiz');
        set_config('fix_preferredbehaviour', 0, 'quiz');

        // quiz savepoint reached
        upgrade_plugin_savepoint(true, 2008000101, 'local', 'qedatabase');
    }

    // Add a not-NULL constraint to the preferredmodel field now that it is populated.
    if ($oldversion < 2008000102) {
        $table = new xmldb_table('quiz');
        $field = new xmldb_field('preferredbehaviour');
        $field->set_attributes(XMLDB_TYPE_CHAR, '32', null, XMLDB_NOTNULL, null, null, 'timeclose');

        $dbman->change_field_notnull($table, $field);

        // quiz savepoint reached
        upgrade_plugin_savepoint(true, 2008000102, 'local', 'qedatabase');
    }

    // Drop the old optionflags field.
    if ($oldversion < 2008000103) {
        $table = new xmldb_table('quiz');
        $field = new xmldb_field('optionflags');
        $dbman->drop_field($table, $field);

        unset_config('optionflags', 'quiz');
        unset_config('fix_optionflags', 'quiz');

        // quiz savepoint reached
        upgrade_plugin_savepoint(true, 2008000103, 'local', 'qedatabase');
    }

    // Drop the old penaltyscheme field.
    if ($oldversion < 2008000104) {
        $table = new xmldb_table('quiz');
        $field = new xmldb_field('penaltyscheme');
        $dbman->drop_field($table, $field);

        unset_config('penaltyscheme', 'quiz');
        unset_config('fix_penaltyscheme', 'quiz');

        // quiz savepoint reached
        upgrade_plugin_savepoint(true, 2008000104, 'local', 'qedatabase');
    }

    if ($oldversion < 2008000110) {

        // Changing nullability of field sumgrades on table quiz_attempts to null
        $table = new xmldb_table('quiz_attempts');
        $field = new xmldb_field('sumgrades');
        $field->set_attributes(XMLDB_TYPE_NUMBER, '10, 5', null, null, null, null, 'attempt');

        // Launch change of nullability for field sumgrades
        $dbman->change_field_notnull($table, $field);

        // Launch change of default for field sumgrades
        $dbman->change_field_default($table, $field);

        // quiz savepoint reached
        upgrade_plugin_savepoint(true, 2008000110, 'local', 'qedatabase');
    }

    if ($oldversion < 2008000111) {

        // Changing the default of field penalty on table question to 0.3333333
        $table = new xmldb_table('question');
        $field = new xmldb_field('penalty');
        $field->set_attributes(XMLDB_TYPE_FLOAT, null, null, XMLDB_NOTNULL, null, '0.3333333', 'defaultgrade');

        // Launch change of default for field penalty
        $dbman->change_field_default($table, $field);

        // quiz savepoint reached
        upgrade_plugin_savepoint(true, 2008000111, 'local', 'qedatabase');
    }

// Update the quiz from the old single review column to seven new columns.

    if ($oldversion < 2008000200) {

        // Define field reviewattempt to be added to quiz
        $table = new xmldb_table('quiz');
        $field = new xmldb_field('reviewattempt');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '6', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'review');

        // Launch add field reviewattempt
        $dbman->add_field($table, $field);

        // quiz savepoint reached
        upgrade_plugin_savepoint(true, 2008000200, 'local', 'qedatabase');
    }

    if ($oldversion < 2008000201) {

        // Define field reviewattempt to be added to quiz
        $table = new xmldb_table('quiz');
        $field = new xmldb_field('reviewcorrectness');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '6', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'reviewattempt');

        // Launch add field reviewattempt
        $dbman->add_field($table, $field);

        // quiz savepoint reached
        upgrade_plugin_savepoint(true, 2008000201, 'local', 'qedatabase');
    }

    if ($oldversion < 2008000202) {

        // Define field reviewattempt to be added to quiz
        $table = new xmldb_table('quiz');
        $field = new xmldb_field('reviewmarks');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '6', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'reviewcorrectness');

        // Launch add field reviewattempt
        $dbman->add_field($table, $field);

        // quiz savepoint reached
        upgrade_plugin_savepoint(true, 2008000202, 'local', 'qedatabase');
    }

    if ($oldversion < 2008000203) {

        // Define field reviewattempt to be added to quiz
        $table = new xmldb_table('quiz');
        $field = new xmldb_field('reviewspecificfeedback');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '6', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'reviewmarks');

        // Launch add field reviewattempt
        $dbman->add_field($table, $field);

        // quiz savepoint reached
        upgrade_plugin_savepoint(true, 2008000203, 'local', 'qedatabase');
    }

    if ($oldversion < 2008000204) {

        // Define field reviewattempt to be added to quiz
        $table = new xmldb_table('quiz');
        $field = new xmldb_field('reviewgeneralfeedback');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '6', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'reviewspecificfeedback');

        // Launch add field reviewattempt
        $dbman->add_field($table, $field);

        // quiz savepoint reached
        upgrade_plugin_savepoint(true, 2008000204, 'local', 'qedatabase');
    }

    if ($oldversion < 2008000205) {

        // Define field reviewattempt to be added to quiz
        $table = new xmldb_table('quiz');
        $field = new xmldb_field('reviewrightanswer');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '6', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'reviewgeneralfeedback');

        // Launch add field reviewattempt
        $dbman->add_field($table, $field);

        // quiz savepoint reached
        upgrade_plugin_savepoint(true, 2008000205, 'local', 'qedatabase');
    }

    if ($oldversion < 2008000206) {

        // Define field reviewattempt to be added to quiz
        $table = new xmldb_table('quiz');
        $field = new xmldb_field('reviewoverallfeedback');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '6', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'reviewrightanswer');

        // Launch add field reviewattempt
        $dbman->add_field($table, $field);

        // quiz savepoint reached
        upgrade_plugin_savepoint(true, 2008000206, 'local', 'qedatabase');
    }

    define('QUIZ_NEW_DURING',            0x10000);
    define('QUIZ_NEW_IMMEDIATELY_AFTER', 0x01000);
    define('QUIZ_NEW_LATER_WHILE_OPEN',  0x00100);
    define('QUIZ_NEW_AFTER_CLOSE',       0x00010);

    define('QUIZ_OLD_IMMEDIATELY', 0x3c003f);
    define('QUIZ_OLD_OPEN',        0x3c00fc0);
    define('QUIZ_OLD_CLOSED',      0x3c03f000);

    define('QUIZ_OLD_RESPONSES',       1*0x1041); // Show responses
    define('QUIZ_OLD_SCORES',          2*0x1041); // Show scores
    define('QUIZ_OLD_FEEDBACK',        4*0x1041); // Show question feedback
    define('QUIZ_OLD_ANSWERS',         8*0x1041); // Show correct answers
    define('QUIZ_OLD_SOLUTIONS',      16*0x1041); // Show solutions
    define('QUIZ_OLD_GENERALFEEDBACK',32*0x1041); // Show question general feedback
    define('QUIZ_OLD_OVERALLFEEDBACK', 1*0x4440000); // Show quiz overall feedback

    // Copy the old review settings
    if ($oldversion < 2008000210) {
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

        // quiz savepoint reached
        upgrade_plugin_savepoint(true, 2008000210, 'local', 'qedatabase');
    }

    if ($oldversion < 2008000211) {
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

        // quiz savepoint reached
        upgrade_plugin_savepoint(true, 2008000211, 'local', 'qedatabase');
    }

    if ($oldversion < 2008000212) {
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

        // quiz savepoint reached
        upgrade_plugin_savepoint(true, 2008000212, 'local', 'qedatabase');
    }

    if ($oldversion < 2008000213) {
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

        // quiz savepoint reached
        upgrade_plugin_savepoint(true, 2008000213, 'local', 'qedatabase');
    }

    if ($oldversion < 2008000214) {
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

        // quiz savepoint reached
        upgrade_plugin_savepoint(true, 2008000214, 'local', 'qedatabase');
    }

    if ($oldversion < 2008000215) {
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

        // quiz savepoint reached
        upgrade_plugin_savepoint(true, 2008000215, 'local', 'qedatabase');
    }

    if ($oldversion < 2008000216) {
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

        // quiz savepoint reached
        upgrade_plugin_savepoint(true, 2008000216, 'local', 'qedatabase');
    }

    // And, do the same for the defaults
    if ($oldversion < 2008000217) {
        if (empty($CFG->quiz_review)) {
            $CFG->quiz_review = 0;
        }

        set_config('reviewattempt',
                QUIZ_NEW_DURING |
                ($CFG->quiz_review & QUIZ_OLD_IMMEDIATELY & QUIZ_OLD_RESPONSES ? QUIZ_NEW_IMMEDIATELY_AFTER : 0) |
                ($CFG->quiz_review & QUIZ_OLD_OPEN & QUIZ_OLD_RESPONSES ? QUIZ_NEW_LATER_WHILE_OPEN : 0) |
                ($CFG->quiz_review & QUIZ_OLD_CLOSED & QUIZ_OLD_RESPONSES ? QUIZ_NEW_AFTER_CLOSE : 0),
                'quiz');

        set_config('reviewcorrectness',
                QUIZ_NEW_DURING |
                ($CFG->quiz_review & QUIZ_OLD_IMMEDIATELY & QUIZ_OLD_SCORES ? QUIZ_NEW_IMMEDIATELY_AFTER : 0) |
                ($CFG->quiz_review & QUIZ_OLD_OPEN & QUIZ_OLD_SCORES ? QUIZ_NEW_LATER_WHILE_OPEN : 0) |
                ($CFG->quiz_review & QUIZ_OLD_CLOSED & QUIZ_OLD_SCORES ? QUIZ_NEW_AFTER_CLOSE : 0),
                'quiz');

        set_config('reviewmarks',
                QUIZ_NEW_DURING |
                ($CFG->quiz_review & QUIZ_OLD_IMMEDIATELY & QUIZ_OLD_SCORES ? QUIZ_NEW_IMMEDIATELY_AFTER : 0) |
                ($CFG->quiz_review & QUIZ_OLD_OPEN & QUIZ_OLD_SCORES ? QUIZ_NEW_LATER_WHILE_OPEN : 0) |
                ($CFG->quiz_review & QUIZ_OLD_CLOSED & QUIZ_OLD_SCORES ? QUIZ_NEW_AFTER_CLOSE : 0),
                'quiz');

        set_config('reviewspecificfeedback',
                ($CFG->quiz_review & QUIZ_OLD_IMMEDIATELY & QUIZ_OLD_FEEDBACK ? QUIZ_NEW_DURING : 0) |
                ($CFG->quiz_review & QUIZ_OLD_IMMEDIATELY & QUIZ_OLD_FEEDBACK ? QUIZ_NEW_IMMEDIATELY_AFTER : 0) |
                ($CFG->quiz_review & QUIZ_OLD_OPEN & QUIZ_OLD_FEEDBACK ? QUIZ_NEW_LATER_WHILE_OPEN : 0) |
                ($CFG->quiz_review & QUIZ_OLD_CLOSED & QUIZ_OLD_FEEDBACK ? QUIZ_NEW_AFTER_CLOSE : 0),
                'quiz');

        set_config('reviewgeneralfeedback',
                ($CFG->quiz_review & QUIZ_OLD_IMMEDIATELY & QUIZ_OLD_GENERALFEEDBACK ? QUIZ_NEW_DURING : 0) |
                ($CFG->quiz_review & QUIZ_OLD_IMMEDIATELY & QUIZ_OLD_GENERALFEEDBACK ? QUIZ_NEW_IMMEDIATELY_AFTER : 0) |
                ($CFG->quiz_review & QUIZ_OLD_OPEN & QUIZ_OLD_GENERALFEEDBACK ? QUIZ_NEW_LATER_WHILE_OPEN : 0) |
                ($CFG->quiz_review & QUIZ_OLD_CLOSED & QUIZ_OLD_GENERALFEEDBACK ? QUIZ_NEW_AFTER_CLOSE : 0),
                'quiz');

        set_config('reviewrightanswer',
                ($CFG->quiz_review & QUIZ_OLD_IMMEDIATELY & QUIZ_OLD_ANSWERS ? QUIZ_NEW_DURING : 0) |
                ($CFG->quiz_review & QUIZ_OLD_IMMEDIATELY & QUIZ_OLD_ANSWERS ? QUIZ_NEW_IMMEDIATELY_AFTER : 0) |
                ($CFG->quiz_review & QUIZ_OLD_OPEN & QUIZ_OLD_ANSWERS ? QUIZ_NEW_LATER_WHILE_OPEN : 0) |
                ($CFG->quiz_review & QUIZ_OLD_CLOSED & QUIZ_OLD_ANSWERS ? QUIZ_NEW_AFTER_CLOSE : 0),
                'quiz');

        set_config('reviewoverallfeedback',
                0 |
                ($CFG->quiz_review & QUIZ_OLD_IMMEDIATELY & QUIZ_OLD_OVERALLFEEDBACK ? QUIZ_NEW_IMMEDIATELY_AFTER : 0) |
                ($CFG->quiz_review & QUIZ_OLD_OPEN & QUIZ_OLD_OVERALLFEEDBACK ? QUIZ_NEW_LATER_WHILE_OPEN : 0) |
                ($CFG->quiz_review & QUIZ_OLD_CLOSED & QUIZ_OLD_OVERALLFEEDBACK ? QUIZ_NEW_AFTER_CLOSE : 0),
                'quiz');

        // quiz savepoint reached
        upgrade_plugin_savepoint(true, 2008000217, 'local', 'qedatabase');
    }

    // Finally drop the old column
    if ($oldversion < 2008000220) {
        // Define field review to be dropped from quiz
        $table = new xmldb_table('quiz');
        $field = new xmldb_field('review');

        // Launch drop field review
        $dbman->drop_field($table, $field);

        // quiz savepoint reached
        upgrade_plugin_savepoint(true, 2008000220, 'local', 'qedatabase');
    }

    if ($oldversion < 2008000221) {
        unset_config('review', 'quiz');

        // quiz savepoint reached
        upgrade_plugin_savepoint(true, 2008000221, 'local', 'qedatabase');
    }

    if ($oldversion < 2008000501) {

        // Rename field defaultgrade on table question to defaultmark
        $table = new xmldb_table('question');
        $field = new xmldb_field('defaultgrade');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '1', 'generalfeedback');

        // Launch rename field defaultmark
        $dbman->rename_field($table, $field, 'defaultmark');

        // quiz savepoint reached
        upgrade_plugin_savepoint(true, 2008000501, 'local', 'qedatabase');
    }

    if ($oldversion < 2008000505) {

        // Rename the question_attempts table to question_usages.
        $table = new xmldb_table('question_attempts');
        if ($dbman->table_exists($table)) {
            $dbman->rename_table($table, 'question_usages');
        }

        // quiz savepoint reached
        upgrade_plugin_savepoint(true, 2008000505, 'local', 'qedatabase');
    }

    if ($oldversion < 2008000507) {

        // Rename the modulename field to component ...
        $table = new xmldb_table('question_usages');
        $field = new xmldb_field('modulename');
        $field->set_attributes(XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'contextid');
        $dbman->rename_field($table, $field, 'component');

        // ... and update its contents.
        $DB->set_field('question_usages', 'component', 'mod_quiz', array('component' => 'quiz'));

        // Add the contextid field.
        $field = new xmldb_field('contextid');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, 'id');
        $dbman->add_field($table, $field);

        // And populate it.
        $quizmoduleid = $DB->get_field('modules', 'id', array('name' => 'quiz'));
        $DB->execute("
            UPDATE {question_usages} SET contextid = (
                SELECT ctx.id
                FROM {context} ctx
                JOIN {course_modules} cm ON cm.id = ctx.instanceid AND cm.module = $quizmoduleid
                JOIN {quiz_attempts} quiza ON quiza.quiz = cm.instance
                WHERE ctx.contextlevel = " . CONTEXT_MODULE . "
                AND quiza.uniqueid = {question_usages}.id
            )
        ");

        // Then make it NOT NULL.
        $field = new xmldb_field('contextid');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, 'id');
        $dbman->change_field_notnull($table, $field);

        // Add the preferredbehaviour column. Populate it with a dummy value
        // for now. We will fill in the appropriate behaviour name when
        // updating all the rest of the attempt data.
        $field = new xmldb_field('preferredbehaviour');
        $field->set_attributes(XMLDB_TYPE_CHAR, '32', null, XMLDB_NOTNULL, null, 'to_be_set_later', 'component');
        $dbman->add_field($table, $field);

        // Then remove the default value, now the column is populated.
        $field = new xmldb_field('preferredbehaviour');
        $field->set_attributes(XMLDB_TYPE_CHAR, '32', null, XMLDB_NOTNULL, null, null, 'component');
        $dbman->change_field_default($table, $field);

        // quiz savepoint reached
        upgrade_plugin_savepoint(true, 2008000507, 'local', 'qedatabase');
    }

    if ($oldversion < 2008000513) {

        // Define key contextid (foreign) to be added to question_usages
        $table = new xmldb_table('question_usages');
        $key = new XMLDBKey('contextid');
        $key->set_attributes(XMLDB_KEY_FOREIGN, array('contextid'), 'context', array('id'));

        // Launch add key contextid
        $dbman->add_key($table, $key);

        // quiz savepoint reached
        upgrade_plugin_savepoint(true, 2008000513, 'local', 'qedatabase');
    }

    if ($oldversion < 2008000514) {

        // Changing precision of field component on table question_usages to (255)
        // This was missed during the upgrade from old versions.
        $table = new xmldb_table('question_usages');
        $field = new xmldb_field('component');
        $field->set_attributes(XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'contextid');

        // Launch change of precision for field component
        $dbman->change_field_precision($table, $field);

        // quiz savepoint reached
        upgrade_plugin_savepoint(true, 2008000514, 'local', 'qedatabase');
    }

    if ($oldversion < 2008000520) {

        // Define table question_attempts to be created
        $table = new xmldb_table('question_attempts');
        if (!$dbman->table_exists($table)) {

            // Adding fields to table question_attempts
            $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
            $table->add_field('questionusageid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
            $table->add_field('slot', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
            $table->add_field('behaviour', XMLDB_TYPE_CHAR, '32', null, XMLDB_NOTNULL, null, null);
            $table->add_field('questionid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
            $table->add_field('maxmark', XMLDB_TYPE_NUMBER, '12, 7', null, XMLDB_NOTNULL, null, null);
            $table->add_field('minfraction', XMLDB_TYPE_NUMBER, '12, 7', null, XMLDB_NOTNULL, null, null);
            $table->add_field('flagged', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
            $table->add_field('questionsummary', XMLDB_TYPE_TEXT, 'small', null, null, null, null);
            $table->add_field('rightanswer', XMLDB_TYPE_TEXT, 'small', null, null, null, null);
            $table->add_field('responsesummary', XMLDB_TYPE_TEXT, 'small', null, null, null, null);
            $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);

            // Adding keys to table question_attempts
            $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
            $table->add_key('questionid', XMLDB_KEY_FOREIGN, array('questionid'), 'question', array('id'));
            $table->add_key('questionusageid', XMLDB_KEY_FOREIGN, array('questionusageid'), 'question_usages', array('id'));

            // Adding indexes to table question_attempts
            $table->add_index('questionusageid-slot', XMLDB_INDEX_UNIQUE, array('questionusageid', 'slot'));

            // Launch create table for question_attempts
            $dbman->create_table($table);
        }

        // quiz savepoint reached
        upgrade_plugin_savepoint(true, 2008000520, 'local', 'qedatabase');
    }

    if ($oldversion < 2008000521) {

        // Define table question_attempt_steps to be created
        $table = new xmldb_table('question_attempt_steps');
        if (!$dbman->table_exists($table)) {

            // Adding fields to table question_attempt_steps
            $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
            $table->add_field('questionattemptid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
            $table->add_field('sequencenumber', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
            $table->add_field('state', XMLDB_TYPE_CHAR, '13', null, XMLDB_NOTNULL, null, null);
            $table->add_field('fraction', XMLDB_TYPE_NUMBER, '12, 7', null, null, null, null);
            $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
            $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null);

            // Adding keys to table question_attempt_steps
            $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
            $table->add_key('questionattemptid', XMLDB_KEY_FOREIGN, array('questionattemptid'), 'question_attempts_new', array('id'));
            $table->add_key('userid', XMLDB_KEY_FOREIGN, array('userid'), 'user', array('id'));

            // Adding indexes to table question_attempt_steps
            $table->add_index('questionattemptid-sequencenumber', XMLDB_INDEX_UNIQUE, array('questionattemptid', 'sequencenumber'));

            // Launch create table for question_attempt_steps
            $dbman->create_table($table);
        }

        // quiz savepoint reached
        upgrade_plugin_savepoint(true, 2008000521, 'local', 'qedatabase');
    }

    if ($oldversion < 2008000522) {

        // Define table question_attempt_step_data to be created
        $table = new xmldb_table('question_attempt_step_data');
        if (!$dbman->table_exists($table)) {

            // Adding fields to table question_attempt_step_data
            $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
            $table->add_field('attemptstepid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
            $table->add_field('name', XMLDB_TYPE_CHAR, '32', null, XMLDB_NOTNULL, null, null);
            $table->add_field('value', XMLDB_TYPE_TEXT, 'small', null, null, null, null);

            // Adding keys to table question_attempt_step_data
            $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
            $table->add_key('attemptstepid', XMLDB_KEY_FOREIGN, array('attemptstepid'), 'question_attempt_steps', array('id'));

            // Adding indexes to table question_attempt_step_data
            $table->add_index('attemptstepid-name', XMLDB_INDEX_UNIQUE, array('attemptstepid', 'name'));

            // Launch create table for question_attempt_step_data
            $dbman->create_table($table);
        }

        // quiz savepoint reached
        upgrade_plugin_savepoint(true, 2008000522, 'local', 'qedatabase');
    }

    if ($oldversion < 2008000600) {

        // Define table question_hints to be created
        $table = new xmldb_table('question_hints');

        // Adding fields to table question_hints
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('questionid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('hint', XMLDB_TYPE_TEXT, 'small', null, XMLDB_NOTNULL, null, null);
        $table->add_field('hintformat', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('shownumcorrect', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, null, null, null);
        $table->add_field('clearwrong', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, null, null, null);
        $table->add_field('options', XMLDB_TYPE_CHAR, '255', null, null, null, null);

        // Adding keys to table question_hints
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('questionid', XMLDB_KEY_FOREIGN, array('questionid'), 'question', array('id'));

        // Conditionally launch create table for question_hints
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // quiz savepoint reached
        upgrade_plugin_savepoint(true, 2008000600, 'local', 'qedatabase');
    }

    if ($oldversion < 2008000601) {

        // In the past, question_answer fractions were stored with rather
        // sloppy rounding. Now update them to the new standard of 7 d.p.
        $changes = array(
            '-0.66666'  => '-0.6666667',
            '-0.33333'  => '-0.3333333',
            '-0.16666'  => '-0.1666667',
            '-0.142857' => '-0.1428571',
             '0.11111'  =>  '0.1111111',
             '0.142857' =>  '0.1428571',
             '0.16666'  =>  '0.1666667',
             '0.33333'  =>  '0.3333333',
             '0.333333' =>  '0.3333333',
             '0.66666'  =>  '0.6666667',
        );
        foreach ($changes as $from => $to) {
            $DB->set_field('question_answers',
                    'fraction', $to, array('fraction' => $from));
        }

        // quiz savepoint reached
        upgrade_plugin_savepoint(true, 2008000601, 'local', 'qedatabase');
    }

    if ($oldversion < 2008000602) {

        // In the past, question penalties were stored with rather
        // sloppy rounding. Now update them to the new standard of 7 d.p.
        $DB->set_field('question',
                'penalty', 0.3333333, array('penalty' => 33.3));
        $DB->set_field_select('question',
                'penalty', 0.3333333, 'penalty >= 0.33 AND penalty <= 0.34');
        $DB->set_field_select('question',
                'penalty', 0.6666667, 'penalty >= 0.66 AND penalty <= 0.67');
        $DB->set_field_select('question',
                'penalty', 1, 'penalty > 1');

        // quiz savepoint reached
        upgrade_plugin_savepoint(true, 2008000602, 'local', 'qedatabase');
    }
}
