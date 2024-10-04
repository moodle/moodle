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

namespace core;

/**
 * Tests for {@link decompose_update_into_safe_changes()} and
 * {@link update_field_with_unique_index()}.
 *
 * @package   core
 * @copyright 2014 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class datalib_update_with_unique_index_test extends \advanced_testcase {

    public function test_decompose_update_into_safe_changes_identity(): void {
        $this->assertEquals(array(), decompose_update_into_safe_changes(
                array(1 => 1, 2 => 2), -1));
    }

    public function test_decompose_update_into_safe_changes_no_overlap(): void {
        $this->assertEquals(array(array(1, 3), array(2, 4)), decompose_update_into_safe_changes(
                array(1 => 3, 2 => 4), -1));
    }

    public function test_decompose_update_into_safe_changes_shift(): void {
        $this->assertSame(array(array(3, 4), array(2, 3), array(1, 2)), decompose_update_into_safe_changes(
                array(1 => 2, 2 => 3, 3 => 4), -1));
    }

    public function test_decompose_decompose_update_into_safe_changes_simple_swap(): void {
        $this->assertEquals(array(array(1, -1), array(2, 1), array(-1, 2)), decompose_update_into_safe_changes(
                array(1 => 2, 2 => 1), -1));
    }

    public function test_decompose_update_into_safe_changes_cycle(): void {
        $this->assertEquals(array(array(1, -2), array(3, 1), array(2, 3), array(-2, 2)),
                decompose_update_into_safe_changes(
                array(1 => 2, 2 => 3 , 3 => 1), -2));
    }

    public function test_decompose_update_into_safe_changes_complex(): void {
        $this->assertEquals(array(array(9, 10), array(8, 9),
                array(1, -1), array(5, 1), array(7, 5), array(-1, 7),
                array(4, -1), array(6, 4), array(-1, 6)), decompose_update_into_safe_changes(
                array(1 => 7, 2 => 2, 3 => 3, 4 => 6, 5 => 1, 6 => 4, 7 => 5, 8 => 9, 9 => 10), -1));
    }

    public function test_decompose_update_into_safe_changes_unused_value_id_used(): void {
        try {
            decompose_update_into_safe_changes(array(1 => 1), 1);
            $this->fail('Expected exception was not thrown');
        } catch (\coding_exception $e) {
            $this->assertEquals('Supposedly unused value 1 is actually used!', $e->a);
        }
    }

    public function test_decompose_update_into_safe_changes_string_values(): void {
        // Sometimes this happens when data has been loaded from the database.
        $this->assertEquals(array(array(1, -1), array(2, 1),
                    array(3, 2), array(4, 3), array(-1, 4)),
                decompose_update_into_safe_changes(
                    array(1 => '4', 2 => '1', 3 => '2', 4 => '3'), -1));
    }

    public function test_reorder_rows(): void {
        global $DB;
        $dbman = $DB->get_manager();
        $this->resetAfterTest();

        $table = new \xmldb_table('test_table');
        $table->setComment("This is a test'n drop table. You can drop it safely");
        $tablename = $table->getName();

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('otherid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('sortorder', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('otherdata', XMLDB_TYPE_TEXT, 'big', null, null, null);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('unique', XMLDB_KEY_UNIQUE, array('otherid', 'sortorder'));
        $dbman->create_table($table);

        // Rows intentionally added in a slightly 'random' order.
        // Note we are testing hat the otherid = 1 rows don't get messed up,
        // as well as testing that the otherid = 2 rows are updated correctly.
        $DB->insert_record($tablename, array('otherid' => 2, 'sortorder' => 1, 'otherdata' => 'To become 4'));
        $DB->insert_record($tablename, array('otherid' => 2, 'sortorder' => 2, 'otherdata' => 'To become 1'));
        $DB->insert_record($tablename, array('otherid' => 1, 'sortorder' => 1, 'otherdata' => 'Other 1'));
        $DB->insert_record($tablename, array('otherid' => 1, 'sortorder' => 2, 'otherdata' => 'Other 2'));
        $DB->insert_record($tablename, array('otherid' => 2, 'sortorder' => 3, 'otherdata' => 'To stay at 3'));
        $DB->insert_record($tablename, array('otherid' => 2, 'sortorder' => 4, 'otherdata' => 'To become 2'));

        update_field_with_unique_index($tablename, 'sortorder',
                array(1 => 4, 2 => 1, 3 => 3, 4 => 2), array('otherid' => 2));

        $this->assertEquals(array(
                3 => (object) array('id' => 3, 'otherid' => 1, 'sortorder' => 1, 'otherdata' => 'Other 1'),
                4 => (object) array('id' => 4, 'otherid' => 1, 'sortorder' => 2, 'otherdata' => 'Other 2'),
            ), $DB->get_records($tablename, array('otherid' => 1), 'sortorder'));
        $this->assertEquals(array(
                2 => (object) array('id' => 2, 'otherid' => 2, 'sortorder' => 1, 'otherdata' => 'To become 1'),
                6 => (object) array('id' => 6, 'otherid' => 2, 'sortorder' => 2, 'otherdata' => 'To become 2'),
                5 => (object) array('id' => 5, 'otherid' => 2, 'sortorder' => 3, 'otherdata' => 'To stay at 3'),
                1 => (object) array('id' => 1, 'otherid' => 2, 'sortorder' => 4, 'otherdata' => 'To become 4'),
            ), $DB->get_records($tablename, array('otherid' => 2), 'sortorder'));
    }
}
