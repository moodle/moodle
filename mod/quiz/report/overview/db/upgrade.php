<?php  // $Id$

function xmldb_quizreport_overview_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();
    $result = true;

//===== 1.9.0 upgrade line ======//

    if ($result && $oldversion < 2008062700) {

    /// Define table quiz_question_regrade to be created
        $table = new xmldb_table('quiz_question_regrade');

    /// Adding fields to table quiz_question_regrade
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('questionid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('attemptid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('newgrade', XMLDB_TYPE_NUMBER, '10, 5', null, XMLDB_NOTNULL, null, null);
        $table->add_field('oldgrade', XMLDB_TYPE_NUMBER, '10, 5', null, XMLDB_NOTNULL, null, null);
        $table->add_field('regraded', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);

    /// Adding keys to table quiz_question_regrade
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

    /// Conditionally launch create table for quiz_question_regrade
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }
    }

    if ($result && $oldversion < 2009030500) {
    /// Changing precision of field newgrade on table quiz_question_regrade to (12, 7).
        $table = new xmldb_table('quiz_question_regrade');
        $field = new xmldb_field('newgrade', XMLDB_TYPE_NUMBER, '12, 7', null, XMLDB_NOTNULL, null, null, 'attemptid');

    /// Launch change of precision for field newgrade
        $dbman->change_field_precision($table, $field);

    /// Changing precision of field oldgrade on table quiz_question_regrade to (12, 7).
        $table = new xmldb_table('quiz_question_regrade');
        $field = new xmldb_field('oldgrade', XMLDB_TYPE_NUMBER, '12, 7', null, XMLDB_NOTNULL, null, null, 'newgrade');

    /// Launch change of precision for field newgrade
        $dbman->change_field_precision($table, $field);
    }

    return $result;
}

?>
