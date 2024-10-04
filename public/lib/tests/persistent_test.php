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

use advanced_testcase;
use coding_exception;
use dml_missing_record_exception;
use lang_string;
use xmldb_table;

/**
 * Persistent testcase.
 *
 * @package    core
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \core\persistent
 */
final class persistent_test extends advanced_testcase {

    public function setUp(): void {
        parent::setUp();
        $this->make_persistent_table();
        $this->make_second_persistent_table();
        $this->resetAfterTest();
    }

    /**
     * Make the table for the persistent.
     */
    protected function make_persistent_table() {
        global $DB;
        $dbman = $DB->get_manager();

        $table = new xmldb_table(core_testable_persistent::TABLE);
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('shortname', XMLDB_TYPE_CHAR, '100', null, null, null, null);
        $table->add_field('idnumber', XMLDB_TYPE_CHAR, '100', null, null, null, null);
        $table->add_field('description', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('descriptionformat', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('parentid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('path', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('sortorder', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('scaleid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('usermodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');

        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        $dbman->create_table($table);
    }

    /**
     * Make the second table for the persistent.
     */
    protected function make_second_persistent_table() {
        global $DB;
        $dbman = $DB->get_manager();

        $table = new xmldb_table(core_testable_second_persistent::TABLE);
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('someint', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('intnull', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('somefloat', XMLDB_TYPE_FLOAT, '10,5', null, null, null, null);
        $table->add_field('sometext', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('someraw', XMLDB_TYPE_CHAR, '100', null, null, null, null);
        $table->add_field('booltrue', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('boolfalse', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('usermodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');

        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        $dbman->create_table($table);
    }

    public function test_properties_definition(): void {
        $expected = array(
            'shortname' => array(
                'type' => PARAM_TEXT,
                'default' => '',
                'null' => NULL_NOT_ALLOWED
            ),
            'idnumber' => array(
                'type' => PARAM_TEXT,
                'null' => NULL_NOT_ALLOWED
            ),
            'description' => array(
                'type' => PARAM_TEXT,
                'default' => '',
                'null' => NULL_NOT_ALLOWED
            ),
            'descriptionformat' => array(
                'choices' => array(FORMAT_HTML, FORMAT_MOODLE, FORMAT_PLAIN, FORMAT_MARKDOWN),
                'type' => PARAM_INT,
                'default' => FORMAT_HTML,
                'null' => NULL_NOT_ALLOWED
            ),
            'parentid' => array(
                'type' => PARAM_INT,
                'default' => 0,
                'null' => NULL_NOT_ALLOWED
            ),
            'path' => array(
                'type' => PARAM_RAW,
                'default' => '',
                'null' => NULL_NOT_ALLOWED
            ),
            'sortorder' => array(
                'type' => PARAM_INT,
                'message' => new lang_string('invalidrequest', 'error'),
                'null' => NULL_NOT_ALLOWED
            ),
            'scaleid' => array(
                'default' => null,
                'type' => PARAM_INT,
                'null' => NULL_ALLOWED
            ),
            'id' => array(
                'default' => 0,
                'type' => PARAM_INT,
                'null' => NULL_NOT_ALLOWED
            ),
            'timecreated' => array(
                'default' => 0,
                'type' => PARAM_INT,
                'null' => NULL_NOT_ALLOWED
            ),
            'timemodified' => array(
                'default' => 0,
                'type' => PARAM_INT,
                'null' => NULL_NOT_ALLOWED
            ),
            'usermodified' => array(
                'default' => 0,
                'type' => PARAM_INT,
                'null' => NULL_NOT_ALLOWED
            ),
        );
        $this->assertEquals($expected, core_testable_persistent::properties_definition());
    }

    /**
     * Test filtering record properties returns only those defined by the persistent
     */
    public function test_properties_filter(): void {
        $result = core_testable_persistent::properties_filter((object) [
            'idnumber' => '123',
            'sortorder' => 1,
            'invalidparam' => 'abc',
        ]);

        // We should get back all data except invalid param.
        $this->assertEquals([
            'idnumber' => '123',
            'sortorder' => 1,
        ], $result);
    }

    /**
     * Test creating persistent instance by specifying record ID in constructor
     */
    public function test_constructor(): void {
        $persistent = (new core_testable_persistent(0, (object) [
            'idnumber' => '123',
            'sortorder' => 1,
        ]))->create();

        // Now create a new instance, passing the original instance ID in the constructor.
        $another = new core_testable_persistent($persistent->get('id'));
        $this->assertEquals($another->to_record(), $persistent->to_record());
    }

    /**
     * Test creating persistent instance by specifying non-existing record ID in constructor throws appropriate exception
     */
    public function test_constructor_invalid(): void {
        $this->expectException(dml_missing_record_exception::class);
        $this->expectExceptionMessage('Can\'t find data record in database table phpunit_persistent.');
        new core_testable_persistent(42);
    }

    public function test_to_record(): void {
        $p = new core_testable_persistent();
        $expected = (object) array(
            'shortname' => '',
            'idnumber' => null,
            'description' => '',
            'descriptionformat' => FORMAT_HTML,
            'parentid' => 0,
            'path' => '',
            'sortorder' => null,
            'id' => 0,
            'timecreated' => 0,
            'timemodified' => 0,
            'usermodified' => 0,
            'scaleid' => null,
        );
        $this->assertEquals($expected, $p->to_record());
    }

    public function test_from_record(): void {
        $p = new core_testable_persistent();
        $data = (object) array(
            'shortname' => 'ddd',
            'idnumber' => 'abc',
            'description' => 'xyz',
            'descriptionformat' => FORMAT_PLAIN,
            'parentid' => 999,
            'path' => '/a/b/c',
            'sortorder' => 12,
            'id' => 1,
            'timecreated' => 2,
            'timemodified' => 3,
            'usermodified' => 4,
            'scaleid' => null,
        );
        $p->from_record($data);
        $this->assertEquals($data, $p->to_record());
    }

    public function test_from_record_invalid_param(): void {
        $p = new core_testable_persistent();
        $data = (object) array(
            'shortname' => 'ddd',
            'idnumber' => 'abc',
            'description' => 'xyz',
            'descriptionformat' => FORMAT_PLAIN,
            'parentid' => 999,
            'path' => '/a/b/c',
            'sortorder' => 12,
            'id' => 1,
            'timecreated' => 2,
            'timemodified' => 3,
            'usermodified' => 4,
            'scaleid' => null,
            'invalidparam' => 'abc'
        );

        $p->from_record($data);

        // Previous call should succeed, assert we get back all data except invalid param.
        unset($data->invalidparam);
        $this->assertEquals($data, $p->to_record());
    }

    public function test_validate(): void {
        $data = (object) array(
            'idnumber' => 'abc',
            'sortorder' => 0
        );
        $p = new core_testable_persistent(0, $data);
        $this->assertFalse(isset($p->beforevalidate));
        $this->assertTrue($p->validate());
        $this->assertTrue(isset($p->beforevalidate));
        $this->assertTrue($p->is_valid());
        $this->assertEquals(array(), $p->get_errors());
        $p->set('descriptionformat', -100);

        $expected = array(
            'descriptionformat' => new lang_string('invaliddata', 'error'),
        );
        $this->assertEquals($expected, $p->validate());
        $this->assertFalse($p->is_valid());
        $this->assertEquals($expected, $p->get_errors());
    }

    public function test_validation_required(): void {
        $data = (object) array(
            'idnumber' => 'abc'
        );
        $p = new core_testable_persistent(0, $data);
        $expected = array(
            'sortorder' => new lang_string('requiredelement', 'form'),
        );
        $this->assertFalse($p->is_valid());
        $this->assertEquals($expected, $p->get_errors());
    }

    public function test_validation_custom(): void {
        $data = (object) array(
            'idnumber' => 'abc',
            'sortorder' => 10,
        );
        $p = new core_testable_persistent(0, $data);
        $expected = array(
            'sortorder' => new lang_string('invalidkey', 'error'),
        );
        $this->assertFalse($p->is_valid());
        $this->assertEquals($expected, $p->get_errors());
    }

    public function test_validation_custom_message(): void {
        $data = (object) array(
            'idnumber' => 'abc',
            'sortorder' => 'abc',
        );
        $p = new core_testable_persistent(0, $data);
        $expected = array(
            'sortorder' => new lang_string('invalidrequest', 'error'),
        );
        $this->assertFalse($p->is_valid());
        $this->assertEquals($expected, $p->get_errors());
    }

    public function test_validation_choices(): void {
        $data = (object) array(
            'idnumber' => 'abc',
            'sortorder' => 0,
            'descriptionformat' => -100
        );
        $p = new core_testable_persistent(0, $data);
        $expected = array(
            'descriptionformat' => new lang_string('invaliddata', 'error'),
        );
        $this->assertFalse($p->is_valid());
        $this->assertEquals($expected, $p->get_errors());
    }

    public function test_validation_type(): void {
        $data = (object) array(
            'idnumber' => 'abc',
            'sortorder' => 'NaN'
        );
        $p = new core_testable_persistent(0, $data);
        $this->assertFalse($p->is_valid());
        $this->assertArrayHasKey('sortorder', $p->get_errors());
    }

    public function test_validation_null(): void {
        $data = (object) array(
            'idnumber' => null,
            'sortorder' => 0,
            'scaleid' => 'bad!'
        );
        $p = new core_testable_persistent(0, $data);
        $this->assertFalse($p->is_valid());
        $this->assertArrayHasKey('idnumber', $p->get_errors());
        $this->assertArrayHasKey('scaleid', $p->get_errors());
        $p->set('idnumber', 'abc');
        $this->assertFalse($p->is_valid());
        $this->assertArrayNotHasKey('idnumber', $p->get_errors());
        $this->assertArrayHasKey('scaleid', $p->get_errors());
        $p->set('scaleid', null);
        $this->assertTrue($p->is_valid());
        $this->assertArrayNotHasKey('scaleid', $p->get_errors());
    }

    public function test_create(): void {
        global $DB;
        $p = new core_testable_persistent(0, (object) array('sortorder' => 123, 'idnumber' => 'abc'));
        $this->assertFalse(isset($p->beforecreate));
        $this->assertFalse(isset($p->aftercreate));
        $p->create();
        $record = $DB->get_record(core_testable_persistent::TABLE, array('id' => $p->get('id')), '*', MUST_EXIST);
        $expected = $p->to_record();
        $this->assertTrue(isset($p->beforecreate));
        $this->assertTrue(isset($p->aftercreate));
        $this->assertEquals($expected->sortorder, $record->sortorder);
        $this->assertEquals($expected->idnumber, $record->idnumber);
        $this->assertEquals($expected->id, $record->id);
        $this->assertTrue($p->is_valid()); // Should always be valid after a create.
    }

    public function test_update(): void {
        global $DB;
        $p = new core_testable_persistent(0, (object) array('sortorder' => 123, 'idnumber' => 'abc'));
        $p->create();
        $id = $p->get('id');
        $p->set('sortorder', 456);
        $p->from_record((object) array('idnumber' => 'def'));
        $this->assertFalse(isset($p->beforeupdate));
        $this->assertFalse(isset($p->afterupdate));
        $p->update();

        $expected = $p->to_record();
        $record = $DB->get_record(core_testable_persistent::TABLE, array('id' => $p->get('id')), '*', MUST_EXIST);
        $this->assertTrue(isset($p->beforeupdate));
        $this->assertTrue(isset($p->afterupdate));
        $this->assertEquals($id, $record->id);
        $this->assertEquals(456, $record->sortorder);
        $this->assertEquals('def', $record->idnumber);
        $this->assertTrue($p->is_valid()); // Should always be valid after an update.
    }

    /**
     * Test set_many prior to updating the persistent
     */
    public function test_set_many_update(): void {
        global $DB;

        $persistent = (new core_testable_persistent(0, (object) [
            'idnumber' => 'test',
            'sortorder' => 2
        ]))->create();

        // Set multiple properties, and update.
        $persistent->set_many([
            'idnumber' => 'test2',
            'sortorder' => 1,
        ])->update();

        // Confirm our persistent was updated.
        $record = $DB->get_record(core_testable_persistent::TABLE, ['id' => $persistent->get('id')], '*', MUST_EXIST);
        $this->assertEquals('test2', $record->idnumber);
        $this->assertEquals(1, $record->sortorder);
    }

    public function test_save(): void {
        global $DB;
        $p = new core_testable_persistent(0, (object) array('sortorder' => 123, 'idnumber' => 'abc'));
        $this->assertFalse(isset($p->beforecreate));
        $this->assertFalse(isset($p->aftercreate));
        $this->assertFalse(isset($p->beforeupdate));
        $this->assertFalse(isset($p->beforeupdate));
        $p->save();
        $record = $DB->get_record(core_testable_persistent::TABLE, array('id' => $p->get('id')), '*', MUST_EXIST);
        $expected = $p->to_record();
        $this->assertTrue(isset($p->beforecreate));
        $this->assertTrue(isset($p->aftercreate));
        $this->assertFalse(isset($p->beforeupdate));
        $this->assertFalse(isset($p->beforeupdate));
        $this->assertEquals($expected->sortorder, $record->sortorder);
        $this->assertEquals($expected->idnumber, $record->idnumber);
        $this->assertEquals($expected->id, $record->id);
        $this->assertTrue($p->is_valid()); // Should always be valid after a save/create.

        $p->set('idnumber', 'abcd');
        $p->save();
        $record = $DB->get_record(core_testable_persistent::TABLE, array('id' => $p->get('id')), '*', MUST_EXIST);
        $expected = $p->to_record();
        $this->assertTrue(isset($p->beforeupdate));
        $this->assertTrue(isset($p->beforeupdate));
        $this->assertEquals($expected->sortorder, $record->sortorder);
        $this->assertEquals($expected->idnumber, $record->idnumber);
        $this->assertEquals($expected->id, $record->id);
        $this->assertTrue($p->is_valid()); // Should always be valid after a save/update.
    }

    /**
     * Test set_many prior to saving the persistent
     */
    public function test_set_many_save(): void {
        global $DB;

        $persistent = (new core_testable_persistent(0, (object) [
            'idnumber' => 'test',
            'sortorder' => 2
        ]));

        // Set multiple properties, and save.
        $persistent->set_many([
            'idnumber' => 'test2',
            'sortorder' => 1,
        ])->save();

        // Confirm our persistent was saved.
        $record = $DB->get_record(core_testable_persistent::TABLE, ['id' => $persistent->get('id')], '*', MUST_EXIST);
        $this->assertEquals('test2', $record->idnumber);
        $this->assertEquals(1, $record->sortorder);
    }

    /**
     * Test set_many with empty array should not modify the persistent
     */
    public function test_set_many_empty(): void {
        global $DB;

        $persistent = (new core_testable_persistent(0, (object) [
            'idnumber' => 'test',
            'sortorder' => 2
        ]))->create();

        // Set empty properties, and update.
        $persistent->set_many([])->update();

        // Confirm our persistent was not updated.
        $record = $DB->get_record(core_testable_persistent::TABLE, ['id' => $persistent->get('id')], '*', MUST_EXIST);
        $this->assertEquals('test', $record->idnumber);
        $this->assertEquals(2, $record->sortorder);
    }

    /**
     * Test set with invalid property
     */
    public function test_set_invalid_property(): void {
        $persistent = (new core_testable_persistent(0, (object) [
            'idnumber' => 'test',
            'sortorder' => 2
        ]));

        $this->expectException(coding_exception::class);
        $this->expectExceptionMessage('Unexpected property \'invalid\' requested');
        $persistent->set('invalid', 'stuff');
    }

    /**
     * Test set_many with invalid property
     */
    public function test_set_many_invalid_property(): void {
        $persistent = (new core_testable_persistent(0, (object) [
            'idnumber' => 'test',
            'sortorder' => 2
        ]));

        $this->expectException(coding_exception::class);
        $this->expectExceptionMessage('Unexpected property \'invalid\' requested');
        $persistent->set_many(['invalid' => 'stuff']);
    }

    public function test_read(): void {
        $p = new core_testable_persistent(0, (object) array('sortorder' => 123, 'idnumber' => 'abc'));
        $p->create();
        unset($p->beforevalidate);
        unset($p->beforecreate);
        unset($p->aftercreate);

        $p2 = new core_testable_persistent($p->get('id'));
        $this->assertEquals($p, $p2);

        $p3 = new core_testable_persistent();
        $p3->set('id', $p->get('id'));
        $p3->read();
        $this->assertEquals($p, $p3);
    }

    public function test_delete(): void {
        global $DB;

        $p = new core_testable_persistent(0, (object) array('sortorder' => 123, 'idnumber' => 'abc'));
        $p->create();
        $this->assertNotEquals(0, $p->get('id'));
        $this->assertTrue($DB->record_exists_select(core_testable_persistent::TABLE, 'id = ?', array($p->get('id'))));
        $this->assertFalse(isset($p->beforedelete));
        $this->assertFalse(isset($p->afterdelete));

        $p->delete();
        $this->assertFalse($DB->record_exists_select(core_testable_persistent::TABLE, 'id = ?', array($p->get('id'))));
        $this->assertEquals(0, $p->get('id'));
        $this->assertEquals(true, $p->beforedelete);
        $this->assertEquals(true, $p->afterdelete);
    }

    public function test_has_property(): void {
        $this->assertFalse(core_testable_persistent::has_property('unknown'));
        $this->assertTrue(core_testable_persistent::has_property('idnumber'));
    }

    public function test_custom_setter_getter(): void {
        global $DB;

        $path = array(1, 2, 3);
        $json = json_encode($path);

        $p = new core_testable_persistent(0, (object) array('sortorder' => 0, 'idnumber' => 'abc'));
        $p->set('path', $path);
        $this->assertEquals($path, $p->get('path'));
        $this->assertEquals($json, $p->to_record()->path);

        $p->create();
        $record = $DB->get_record(core_testable_persistent::TABLE, array('id' => $p->get('id')), 'id, path', MUST_EXIST);
        $this->assertEquals($json, $record->path);
    }

    /**
     * Test get_record method for creating persistent instance
     */
    public function test_get_record(): void {
        $persistent = (new core_testable_persistent(0, (object) [
            'idnumber' => '123',
            'sortorder' => 1,
        ]))->create();

        $another = core_testable_persistent::get_record(['id' => $persistent->get('id')]);

        // Assert we got back a persistent instance, and it matches original.
        $this->assertInstanceOf(core_testable_persistent::class, $another);
        $this->assertEquals($another->to_record(), $persistent->to_record());
    }

    /**
     * Test get_record method for creating persistent instance, ignoring a non-existing record
     */
    public function test_get_record_ignore_missing(): void {
        $persistent = core_testable_persistent::get_record(['id' => 42]);
        $this->assertFalse($persistent);
    }

    /**
     * Test get_record method for creating persistent instance, throws appropriate exception for non-existing record
     */
    public function test_get_record_must_exist(): void {
        $this->expectException(dml_missing_record_exception::class);
        $this->expectExceptionMessage('Can\'t find data record in database table phpunit_persistent.');
        core_testable_persistent::get_record(['id' => 42], MUST_EXIST);
    }

    public function test_record_exists(): void {
        global $DB;
        $this->assertFalse($DB->record_exists(core_testable_persistent::TABLE, array('idnumber' => 'abc')));
        $p = new core_testable_persistent(0, (object) array('sortorder' => 123, 'idnumber' => 'abc'));
        $p->create();
        $id = $p->get('id');
        $this->assertTrue(core_testable_persistent::record_exists($id));
        $this->assertTrue($DB->record_exists(core_testable_persistent::TABLE, array('idnumber' => 'abc')));
        $p->delete();
        $this->assertFalse(core_testable_persistent::record_exists($id));
    }

    public function test_get_sql_fields(): void {
        $expected = '' .
            'c.id AS prefix_id, ' .
            'c.shortname AS prefix_shortname, ' .
            'c.idnumber AS prefix_idnumber, ' .
            'c.description AS prefix_description, ' .
            'c.descriptionformat AS prefix_descriptionformat, ' .
            'c.parentid AS prefix_parentid, ' .
            'c.path AS prefix_path, ' .
            'c.sortorder AS prefix_sortorder, ' .
            'c.scaleid AS prefix_scaleid, ' .
            'c.timecreated AS prefix_timecreated, ' .
            'c.timemodified AS prefix_timemodified, ' .
            'c.usermodified AS prefix_usermodified';
        $this->assertEquals($expected, core_testable_persistent::get_sql_fields('c', 'prefix_'));
    }

    public function test_get_sql_fields_too_long(): void {
        $this->expectException(coding_exception::class);
        $this->expectExceptionMessageMatches('/The alias .+ exceeds 30 characters/');
        core_testable_persistent::get_sql_fields('c');
    }

    public function test_get(): void {
        $data = [
            'someint' => 123,
            'intnull' => null,
            'somefloat' => 33.44,
            'sometext' => 'Hello',
            'someraw' => '/dev/hello',
            'booltrue' => true,
            'boolfalse' => false,
        ];
        $p = new core_testable_second_persistent(0, (object)$data);
        $p->create();

        $this->assertSame($data['intnull'], $p->get('intnull'));
        $this->assertSame($data['someint'], $p->get('someint'));
        $this->assertIsFloat($p->get('somefloat')); // Avoid === comparisons on floats, verify type and value separated.
        $this->assertEqualsWithDelta($data['somefloat'], $p->get('somefloat'), 0.00001);
        $this->assertSame($data['sometext'], $p->get('sometext'));
        $this->assertSame($data['someraw'], $p->get('someraw'));
        $this->assertSame($data['booltrue'], $p->get('booltrue'));
        $this->assertSame($data['boolfalse'], $p->get('boolfalse'));

        // Ensure that types are correct after reloading data from database.
        $p->read();

        $this->assertSame($data['someint'], $p->get('someint'));
        $this->assertSame($data['intnull'], $p->get('intnull'));
        $this->assertIsFloat($p->get('somefloat')); // Avoid === comparisons on floats, verify type and value separated.
        $this->assertEqualsWithDelta($data['somefloat'], $p->get('somefloat'), 0.00001);
        $this->assertSame($data['sometext'], $p->get('sometext'));
        $this->assertSame($data['someraw'], $p->get('someraw'));
        $this->assertSame($data['booltrue'], $p->get('booltrue'));
        $this->assertSame($data['boolfalse'], $p->get('boolfalse'));
    }
}

/**
 * Example persistent class.
 *
 * @package    core
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_testable_persistent extends persistent {

    const TABLE = 'phpunit_persistent';

    /** @var bool before validate status. */
    public ?bool $beforevalidate;

    /** @var bool before create status. */
    public ?bool $beforecreate;

    /** @var bool before update status. */
    public ?bool $beforeupdate;

    /** @var bool before delete status. */
    public ?bool $beforedelete;

    /** @var bool after create status. */
    public ?bool $aftercreate;

    /** @var bool after update status. */
    public ?bool $afterupdate;

    /** @var bool after delete status. */
    public ?bool $afterdelete;

    protected static function define_properties() {
        return array(
            'shortname' => array(
                'type' => PARAM_TEXT,
                'default' => ''
            ),
            'idnumber' => array(
                'type' => PARAM_TEXT,
            ),
            'description' => array(
                'type' => PARAM_TEXT,
                'default' => ''
            ),
            'descriptionformat' => array(
                'choices' => array(FORMAT_HTML, FORMAT_MOODLE, FORMAT_PLAIN, FORMAT_MARKDOWN),
                'type' => PARAM_INT,
                'default' => FORMAT_HTML
            ),
            'parentid' => array(
                'type' => PARAM_INT,
                'default' => 0
            ),
            'path' => array(
                'type' => PARAM_RAW,
                'default' => ''
            ),
            'sortorder' => array(
                'type' => PARAM_INT,
                'message' => new lang_string('invalidrequest', 'error')
            ),
            'scaleid' => array(
                'type' => PARAM_INT,
                'default' => null,
                'null' => NULL_ALLOWED
            )
        );
    }

    protected function before_validate() {
        $this->beforevalidate = true;
    }

    protected function before_create() {
        $this->beforecreate = true;
    }

    protected function before_update() {
        $this->beforeupdate = true;
    }

    protected function before_delete() {
        $this->beforedelete = true;
    }

    protected function after_create() {
        $this->aftercreate = true;
    }

    protected function after_update($result) {
        $this->afterupdate = true;
    }

    protected function after_delete($result) {
        $this->afterdelete = true;
    }

    protected function get_path() {
        $value = $this->raw_get('path');
        if (!empty($value)) {
            $value = json_decode($value);
        }
        return $value;
    }

    protected function set_path($value) {
        if (!empty($value)) {
            $value = json_encode($value);
        }
        $this->raw_set('path', $value);
    }

    protected function validate_sortorder($value) {
        if ($value == 10) {
            return new lang_string('invalidkey', 'error');
        }
        return true;
    }

}

/**
 * Example persistent class to test types.
 *
 * @package    core
 * @copyright  2021 David Matamoros <davidmc@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_testable_second_persistent extends persistent {

    /** Table name for the persistent. */
    const TABLE = 'phpunit_second_persistent';

    /**
     * Return the list of properties.
     *
     * @return array
     */
    protected static function define_properties(): array {
        return [
            'someint' => [
                'type' => PARAM_INT,
            ],
            'intnull' => [
                'type' => PARAM_INT,
                'null' => NULL_ALLOWED,
                'default' => null,
            ],
            'somefloat' => [
                'type' => PARAM_FLOAT,
            ],
            'sometext' => [
                'type' => PARAM_TEXT,
                'default' => ''
            ],
            'someraw' => [
                'type' => PARAM_RAW,
                'default' => ''
            ],
            'booltrue' => [
                'type' => PARAM_BOOL,
            ],
            'boolfalse' => [
                'type' => PARAM_BOOL,
            ]
        ];
    }
}
