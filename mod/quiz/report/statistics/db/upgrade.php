<?php  // $Id$

function xmldb_quizreport_statistics_upgrade($oldversion=0) {

    global $DB;
    
    $dbman = $DB->get_manager();

    $result = true;

//===== 1.9.0 upgrade line ======//

    if ($result && $oldversion < 2008072401) {
        //register cron to run every 5 hours.
        $result = $result && $DB->set_field('quiz_report', 'cron', HOURSECS*5, array('name'=>'statistics'));
    }
    if ($result && $oldversion < 2008072500) {

    /// Define field s to be added to quiz_question_statistics
        $table = new xmldb_table('quiz_question_statistics');
        $field = new xmldb_field('s', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0', 'subquestion');

    /// Conditionally launch add field s
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

    }
    if ($result && $oldversion < 2008072800) {

    /// Define field maxgrade to be added to quiz_question_statistics
        $table = new xmldb_table('quiz_question_statistics');
        $field = new xmldb_field('maxgrade', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null, 'subquestions');

    /// Conditionally launch add field maxgrade
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

    }

    if ($result && $oldversion < 2008072801) {

    /// Define field positions to be added to quiz_question_statistics
        $table = new xmldb_table('quiz_question_statistics');
        $field = new xmldb_field('positions', XMLDB_TYPE_TEXT, 'medium', null, null, null, null, null, null, 'maxgrade');

    /// Conditionally launch add field positions
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

    }
    
    return $result;
}

?>
