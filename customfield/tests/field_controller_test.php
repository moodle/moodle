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

namespace core_customfield;

use core_customfield_generator;
use customfield_checkbox;
use customfield_date;
use customfield_select;
use customfield_text;
use customfield_textarea;

/**
 * Functional test for class \core_customfield\field_controller.
 *
 * @package    core_customfield
 * @category   test
 * @copyright  2018 Ruslan Kabalin
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class field_controller_test extends \advanced_testcase {

    /**
     * Get generator.
     *
     * @return core_customfield_generator
     */
    protected function get_generator(): core_customfield_generator {
        return $this->getDataGenerator()->get_plugin_generator('core_customfield');
    }

    /**
     * Test for function \core_customfield\field_controller::create()
     */
    public function test_constructor() {
        global $DB;
        $this->resetAfterTest();

        // Create the category.
        $category0 = $this->get_generator()->create_category();

        // Initiate objects without id, try with the category object or with category id or with both.
        $field0 = field_controller::create(0, (object)['type' => 'checkbox'], $category0);
        $this->assertInstanceOf(customfield_checkbox\field_controller::class, $field0);
        $field1 = field_controller::create(0, (object)['type' => 'date', 'categoryid' => $category0->get('id')]);
        $this->assertInstanceOf(customfield_date\field_controller::class, $field1);
        $field2 = field_controller::create(0, (object)['type' => 'select', 'categoryid' => $category0->get('id')], $category0);
        $this->assertInstanceOf(customfield_select\field_controller::class, $field2);
        $field3 = field_controller::create(0, (object)['type' => 'text'], $category0);
        $this->assertInstanceOf(customfield_text\field_controller::class, $field3);
        $field4 = field_controller::create(0, (object)['type' => 'textarea'], $category0);
        $this->assertInstanceOf(customfield_textarea\field_controller::class, $field4);

        // Save fields to the db so we have ids.
        \core_customfield\api::save_field_configuration($field0, (object)['name' => 'a', 'shortname' => 'a']);
        \core_customfield\api::save_field_configuration($field1, (object)['name' => 'b', 'shortname' => 'b']);
        \core_customfield\api::save_field_configuration($field2, (object)['name' => 'c', 'shortname' => 'c']);
        \core_customfield\api::save_field_configuration($field3, (object)['name' => 'd', 'shortname' => 'd']);
        \core_customfield\api::save_field_configuration($field4, (object)['name' => 'e', 'shortname' => 'e']);

        // Retrieve fields by id.
        $this->assertInstanceOf(customfield_checkbox\field_controller::class, field_controller::create($field0->get('id')));
        $this->assertInstanceOf(customfield_date\field_controller::class, field_controller::create($field1->get('id')));

        // Retrieve field by id and category.
        $this->assertInstanceOf(customfield_select\field_controller::class,
            field_controller::create($field2->get('id'), null, $category0));

        // Retrieve fields by record without category.
        $fieldrecord = $DB->get_record(\core_customfield\field::TABLE, ['id' => $field3->get('id')], '*', MUST_EXIST);
        $this->assertInstanceOf(customfield_text\field_controller::class, field_controller::create(0, $fieldrecord));

        // Retrieve fields by record with category.
        $fieldrecord = $DB->get_record(\core_customfield\field::TABLE, ['id' => $field4->get('id')], '*', MUST_EXIST);
        $this->assertInstanceOf(customfield_textarea\field_controller::class,
            field_controller::create(0, $fieldrecord, $category0));
    }

    /**
     * Test for function \core_customfield\field_controller::create() in case of wrong parameters
     */
    public function test_constructor_errors() {
        global $DB;
        $this->resetAfterTest();

        // Create a category and a field.
        $category = $this->get_generator()->create_category();
        $field = $this->get_generator()->create_field(['categoryid' => $category->get('id')]);

        $fieldrecord = $DB->get_record(\core_customfield\field::TABLE, ['id' => $field->get('id')], '*', MUST_EXIST);

        // Both id and record give warning.
        $field = field_controller::create($fieldrecord->id, $fieldrecord);
        $debugging = $this->getDebuggingMessages();
        $this->assertEquals(1, count($debugging));
        $this->assertEquals('Too many parameters, either id need to be specified or a record, but not both.',
            $debugging[0]->message);
        $this->resetDebugging();
        $this->assertInstanceOf(customfield_text\field_controller::class, $field);

        // Retrieve non-existing field.
        try {
            field_controller::create($fieldrecord->id + 1);
            $this->fail('Expected exception');
        } catch (\moodle_exception $e) {
            $this->assertEquals('Field not found', $e->getMessage());
            $this->assertEquals(\moodle_exception::class, get_class($e));
        }

        // Retrieve without id and without type.
        try {
            field_controller::create(0, (object)['name' => 'a'], $category);
            $this->fail('Expected exception');
        } catch (\coding_exception $e) {
            $this->assertEquals('Coding error detected, it must be fixed by a programmer: Not enough parameters to ' .
                'initialise field_controller - unknown field type', $e->getMessage());
            $this->assertEquals(\coding_exception::class, get_class($e));
        }

        // Missing category id.
        try {
            field_controller::create(0, (object)['type' => 'text']);
            $this->fail('Expected exception');
        } catch (\coding_exception $e) {
            $this->assertEquals('Coding error detected, it must be fixed by a programmer: Not enough parameters ' .
                'to initialise field_controller - unknown category', $e->getMessage());
            $this->assertEquals(\coding_exception::class, get_class($e));
        }

        // Mismatching category id.
        try {
            field_controller::create(0, (object)['type' => 'text', 'categoryid' => $category->get('id') + 1], $category);
            $this->fail('Expected exception');
        } catch (\coding_exception $e) {
            $this->assertEquals('Coding error detected, it must be fixed by a programmer: Category of the field ' .
                'does not match category from the parameter', $e->getMessage());
            $this->assertEquals(\coding_exception::class, get_class($e));
        }

        // Non-existing type.
        try {
            field_controller::create(0, (object)['type' => 'nonexisting'], $category);
            $this->fail('Expected exception');
        } catch (\moodle_exception $e) {
            $this->assertEquals('Field type nonexisting not found', $e->getMessage());
            $this->assertEquals(\moodle_exception::class, get_class($e));
        }
    }

    /**
     * Tests for behaviour of:
     * \core_customfield\field_controller::save()
     * \core_customfield\field_controller::get()
     * \core_customfield\field_controller::get_category()
     */
    public function test_create_field() {
        global $DB;
        $this->resetAfterTest();

        $lpg = $this->get_generator();
        $category = $lpg->create_category();
        $fields = $DB->get_records(\core_customfield\field::TABLE, ['categoryid' => $category->get('id')]);
        $this->assertCount(0, $fields);

        // Create field.
        $fielddata = new \stdClass();
        $fielddata->name = 'Field';
        $fielddata->shortname = 'field';
        $fielddata->type = 'text';
        $fielddata->categoryid = $category->get('id');
        $field = field_controller::create(0, $fielddata);
        $field->save();

        $fields = $DB->get_records(\core_customfield\field::TABLE, ['categoryid' => $category->get('id')]);
        $this->assertCount(1, $fields);
        $this->assertTrue(\core_customfield\field::record_exists($field->get('id')));
        $this->assertInstanceOf(\customfield_text\field_controller::class, $field);
        $this->assertSame($field->get('name'), $fielddata->name);
        $this->assertSame($field->get('type'), $fielddata->type);
        $this->assertEquals($field->get_category()->get('id'), $category->get('id'));
    }

    /**
     * Tests for \core_customfield\field_controller::delete() behaviour.
     */
    public function test_delete_field() {
        global $DB;
        $this->resetAfterTest();

        $lpg = $this->get_generator();
        $category = $lpg->create_category();
        $fields = $DB->get_records(\core_customfield\field::TABLE, ['categoryid' => $category->get('id')]);
        $this->assertCount(0, $fields);

        // Create field using generator.
        $field1 = $lpg->create_field(array('categoryid' => $category->get('id')));
        $field2 = $lpg->create_field(array('categoryid' => $category->get('id')));
        $fields = $DB->get_records(\core_customfield\field::TABLE, ['categoryid' => $category->get('id')]);
        $this->assertCount(2, $fields);

        // Delete fields.
        $this->assertTrue($field1->delete());
        $this->assertTrue($field2->delete());

        // Check that the fields have been deleted.
        $fields = $DB->get_records(\core_customfield\field::TABLE, ['categoryid' => $category->get('id')]);
        $this->assertCount(0, $fields);
        $this->assertFalse(\core_customfield\field::record_exists($field1->get('id')));
        $this->assertFalse(\core_customfield\field::record_exists($field2->get('id')));
    }

    /**
     * Tests for \core_customfield\field_controller::get_configdata_property() behaviour.
     */
    public function test_get_configdata_property() {
        $this->resetAfterTest();

        $lpg = $this->get_generator();
        $category = $lpg->create_category();
        $configdata = ['a' => 'b', 'c' => ['d', 'e']];
        $field = field_controller::create(0, (object)['type' => 'text',
            'configdata' => json_encode($configdata), 'shortname' => 'a', 'name' => 'a'], $category);
        $field->save();

        // Retrieve field and check configdata.
        $field = field_controller::create($field->get('id'));
        $this->assertEquals($configdata, $field->get('configdata'));
        $this->assertEquals('b', $field->get_configdata_property('a'));
        $this->assertEquals(['d', 'e'], $field->get_configdata_property('c'));
        $this->assertEquals(null, $field->get_configdata_property('x'));
    }
}
