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

        $dbman = $DB->get_manager();

        $table = new xmldb_table("testtable");
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, null, null, null, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);
        $this->tables[$table->getName()] = $table;

        $id = $DB->insert_record('testtable', array('name' => 'SuperDuperREcord'));

        $wheresql = "name " . $DB->sql_ilike() . " '%per%'";
        $record = $DB->get_record_select('testtable', $wheresql);
        $this->assertEqual('SuperDuperREcord', $record->name);

        $wheresql = "name " . $DB->sql_ilike() . " 'per'";
        $record = $DB->get_record_select('testtable', $wheresql);
        $this->assertFalse($record);
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
        $field = $DB->get_field_sql("SELECT " . $DB->sql_cast_char2int("'two'"));
        $this->assertFalse($field);
        $field = $DB->get_field_sql("SELECT " . $DB->sql_cast_char2int("'74971.4901'"));
        $this->assertFalse($field);
        $field = $DB->get_field_sql("SELECT " . $DB->sql_cast_char2int("'74971'"));
        $this->assertEqual(74971, $field);
    }
}
?>
