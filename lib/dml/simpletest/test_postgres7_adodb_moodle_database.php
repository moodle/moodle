<?php
/**
 * Unit tests for helper functions of mysqli class
 * @package dml
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}
require_once('dbspecific.php');

class postgres7_adodb_moodle_database_test extends dbspecific_test {
    function test_ilike() {
        $DB = $this->tdb;

        $sql = "SELECT 'SuperDuperRecord' " . $DB->sql_ilike() . " '%per%' AS result";
        $record = $DB->get_record_sql($sql);
        $this->assertEqual('t', $record->result);

        $sql = "SELECT 'SuperDuperRecord' " . $DB->sql_ilike() . " 'per' AS result";
        $record = $DB->get_record_sql($sql);
        $this->assertEqual('f',$record->result);
    }

    function test_concat() {
        $DB = $this->tdb;
        $sql = "SELECT " . $DB->sql_concat("'name'", "'name2'", "'name3'") . " AS fullname";
        $this->assertEqual("namename2name3", $DB->get_field_sql($sql));
    }

    function test_bitxor() {
        $DB = $this->tdb;
        $sql = "SELECT " . $DB->sql_bitxor(23,53);
        $this->assertEqual(34, $DB->get_field_sql($sql));
    }

    function test_cast_char2int() {
        $DB = $this->tdb;
        $field = $DB->get_field_sql("SELECT " . $DB->sql_cast_char2int("'74971'"));
        $this->assertEqual(74971, $field);
    }

    function test_cast_char2real() {
        $DB = $this->tdb;
        $field = $DB->get_field_sql("SELECT " . $DB->sql_cast_char2real("'74971.55'"));
        $this->assertEqual(74971.5, $field);
        $field = $DB->get_field_sql("SELECT " . $DB->sql_cast_char2real("'74971.59'"));
        $this->assertEqual(74971.6, $field);
    }

    function test_regex() {
        $DB = $this->tdb;
        $name = 'something or another';

        $sql = "SELECT '$name' ".$DB->sql_regex()." 'th'";
        $this->assertEqual('t', $DB->get_field_sql($sql));

        $sql = "SELECT '$name' ".$DB->sql_regex(false)." 'th'";
        $this->assertEqual('f', $DB->get_field_sql($sql));
    }
}
?>
