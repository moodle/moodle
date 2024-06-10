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
 * Functional test for class data_controller.
 *
 * @package    core_customfield
 * @category   test
 * @copyright  2018 Toni Barbera <toni@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \core_customfield\data_controller
 */
final class data_controller_test extends \advanced_testcase {
    /**
     * Get generator.
     *
     * @return core_customfield_generator
     */
    protected function get_generator(): core_customfield_generator {
        return $this->getDataGenerator()->get_plugin_generator('core_customfield');
    }

    /**
     * Test for function data_controller::create()
     */
    public function test_constructor(): void {
        global $DB;
        $this->resetAfterTest();

        // Create a course, fields category and fields.
        $course = $this->getDataGenerator()->create_course();
        $category0 = $this->get_generator()->create_category(['name' => 'aaaa']);

        // Add fields to this category.
        $fielddata                = new \stdClass();
        $fielddata->categoryid    = $category0->get('id');
        $fielddata->configdata    = "{\"required\":\"0\",\"uniquevalues\":\"0\",\"locked\":\"0\",\"visibility\":\"0\",
                                    \"defaultvalue\":\"\",\"displaysize\":0,\"maxlength\":0,\"ispassword\":\"0\",
                                    \"link\":\"\",\"linktarget\":\"\"}";

        $fielddata->type = 'checkbox';
        $field0 = $this->get_generator()->create_field($fielddata);
        $fielddata->type = 'date';
        $field1 = $this->get_generator()->create_field($fielddata);
        $fielddata->type = 'select';
        $field2 = $this->get_generator()->create_field($fielddata);
        $fielddata->type = 'text';
        $field3 = $this->get_generator()->create_field($fielddata);
        $fielddata->type = 'textarea';
        $field4 = $this->get_generator()->create_field($fielddata);

        $params = ['instanceid' => $course->id, 'contextid' => \context_course::instance($course->id)->id];

        // Generate new data_controller records for these fields, specifying field controller or fieldid or both.
        $data0 = data_controller::create(0, (object)$params, $field0);
        $this->assertInstanceOf(customfield_checkbox\data_controller::class, $data0);
        $data1 = data_controller::create(
            0,
            (object)($params + ['fieldid' => $field1->get('id')]),
            $field1
        );
        $this->assertInstanceOf(customfield_date\data_controller::class, $data1);
        $data2 = data_controller::create(
            0,
            (object)($params + ['fieldid' => $field2->get('id')])
        );
        $this->assertInstanceOf(customfield_select\data_controller::class, $data2);
        $data3 = data_controller::create(0, (object)$params, $field3);
        $this->assertInstanceOf(customfield_text\data_controller::class, $data3);
        $data4 = data_controller::create(0, (object)$params, $field4);
        $this->assertInstanceOf(customfield_textarea\data_controller::class, $data4);

        // Save data so we can have ids.
        $data0->save();
        $data1->save();
        $data2->save();
        $data3->save();
        $data4->save();

        // Retrieve data by id.
        $this->assertInstanceOf(customfield_checkbox\data_controller::class, data_controller::create($data0->get('id')));
        $this->assertInstanceOf(customfield_date\data_controller::class, data_controller::create($data1->get('id')));

        // Retrieve data by id and field.
        $this->assertInstanceOf(
            customfield_select\data_controller::class,
            data_controller::create($data2->get('id'), null, $field2)
        );

        // Retrieve data by record without field.
        $datarecord = $DB->get_record(\core_customfield\data::TABLE, ['id' => $data3->get('id')], '*', MUST_EXIST);
        $this->assertInstanceOf(customfield_text\data_controller::class, data_controller::create(0, $datarecord));

        // Retrieve data by record with field.
        $datarecord = $DB->get_record(\core_customfield\data::TABLE, ['id' => $data4->get('id')], '*', MUST_EXIST);
        $this->assertInstanceOf(customfield_textarea\data_controller::class, data_controller::create(0, $datarecord, $field4));
    }

    /**
     * Test for function \core_customfield\field_controller::create() in case of wrong parameters
     */
    public function test_constructor_errors(): void {
        global $DB;
        $this->resetAfterTest();

        // Create a category, field and data.
        $category = $this->get_generator()->create_category();
        $field = $this->get_generator()->create_field(['categoryid' => $category->get('id')]);
        $course = $this->getDataGenerator()->create_course();
        $data = data_controller::create(0, (object)['instanceid' => $course->id,
            'contextid' => \context_course::instance($course->id)->id], $field);
        $data->save();

        $datarecord = $DB->get_record(\core_customfield\data::TABLE, ['id' => $data->get('id')], '*', MUST_EXIST);

        // Both id and record give warning.
        $d = data_controller::create($datarecord->id, $datarecord);
        $debugging = $this->getDebuggingMessages();
        $this->assertEquals(1, count($debugging));
        $this->assertEquals(
            'Too many parameters, either id need to be specified or a record, but not both.',
            $debugging[0]->message
        );
            $this->resetDebugging();
        $this->assertInstanceOf(customfield_text\data_controller::class, $d);

        // Retrieve non-existing data.
        try {
            data_controller::create($datarecord->id + 1);
            $this->fail('Expected exception');
        } catch (\dml_missing_record_exception $e) {
            $this->assertStringMatchesFormat('Can\'t find data record in database table customfield_data%a', $e->getMessage());
        }

        // Missing field id.
        try {
            data_controller::create(0, (object)['instanceid' => $course->id]);
            $this->fail('Expected exception');
        } catch (\coding_exception $e) {
            $this->assertEquals('Coding error detected, it must be fixed by a programmer: Not enough parameters to ' .
                'initialise data_controller - unknown field', $e->getMessage());
        }

        // Mismatching field id.
        try {
            data_controller::create(0, (object)['instanceid' => $course->id, 'fieldid' => $field->get('id') + 1], $field);
            $this->fail('Expected exception');
        } catch (\coding_exception $e) {
            $this->assertEquals('Coding error detected, it must be fixed by a programmer: Field id from the record ' .
                'does not match field from the parameter', $e->getMessage());
        }

        // Nonexisting class.
        try {
            $field->set('type', 'invalid');
            data_controller::create(0, (object)['instanceid' => $course->id], $field);
            $this->fail('Expected exception');
        } catch (\moodle_exception $e) {
            $this->assertEquals('Field type invalid not found', $e->getMessage());
        }
    }
}
