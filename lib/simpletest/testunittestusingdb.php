<?php
if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

class UnitTestCaseUsingDatabase_test extends UnitTestCaseUsingDatabase {

    function test_stuff() {
        global $CFG, $DB;
        $dbman = $this->testdb->get_manager();

        $this->assertFalse($dbman->table_exists('quiz_attempts'));
        $this->assertFalse($dbman->table_exists('quiz'));
        $this->create_test_table('quiz_attempts', 'mod/quiz');
        $this->assertTrue($dbman->table_exists('quiz_attempts'));
        $this->assertFalse($dbman->table_exists('quiz'));

        $this->load_test_data('quiz_attempts',
                array('quiz', 'uniqueid', 'attempt', 'preview', 'layout'), array(
                array(    1 ,         1 ,        1 ,        0 , '1,2,3,0'),
                array(    1 ,         2 ,        2 ,        1 , '2,3,1,0')));

        $this->switch_to_test_db();
        require_once($CFG->dirroot . '/mod/quiz/locallib.php');
        $this->assertTrue(quiz_has_attempts(1));
        $this->revert_to_real_db();

        $this->drop_test_table('quiz_attempts');
        $this->assertFalse($dbman->table_exists('quiz_attempts'));
    }

}

