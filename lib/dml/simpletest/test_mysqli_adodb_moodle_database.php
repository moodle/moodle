<?php
/**
 * Unit tests for helper functions of mysqli class
 * @package dml
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}
require_once('dbspecific.php');

class mysqli_adodb_moodle_database_test extends dbspecific_test {
    function test_cast_char2int() {
        $DB = $this->tdb;
        $field = $DB->get_field_sql("SELECT " . $DB->sql_cast_char2int("'two'") . " AS name_int");
        $this->assertEqual(0, $field);
        $field = $DB->get_field_sql("SELECT " . $DB->sql_cast_char2int("'74971.4901'") . " AS name_int");
        $this->assertEqual(74971, $field);
    }

    function test_regex() {
        $DB = $this->tdb;
        $name = 'something or another';

        $sql = "SELECT '$name' ".$DB->sql_regex()." 'th'";
        $this->assertTrue($DB->get_field_sql($sql));

        $sql = "SELECT '$name' ".$DB->sql_regex(false)." 'th'";
        $this->assertFalse($DB->get_field_sql($sql));
    }
}
?>
