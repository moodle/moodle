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

declare(strict_types=1);

namespace customfield_number;

use advanced_testcase;
use core_customfield_generator;
use core_customfield_test_instance_form;

/**
 * Tests for the data controller
 *
 * @package    customfield_number
 * @covers     \customfield_number\data_controller
 * @copyright  2024 Paul Holden <paulh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class data_controller_test extends advanced_testcase {

    /**
     * Test that using base field controller returns our number type
     */
    public function test_create(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();

        /** @var core_customfield_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_customfield');

        $category = $generator->create_category();
        $field = $generator->create_field(['categoryid' => $category->get('id'), 'type' => 'number']);
        $data = $generator->add_instance_data($field, (int) $course->id, 1);

        $this->assertInstanceOf(data_controller::class, \core_customfield\data_controller::create($data->get('id')));
        $this->assertInstanceOf(data_controller::class, \core_customfield\data_controller::create(0, $data->to_record()));
        $this->assertInstanceOf(data_controller::class, \core_customfield\data_controller::create(0, null, $field));
    }

    /**
     * Test validation of field instance form
     */
    public function test_form_validation(): void {
        global $CFG;

        require_once("{$CFG->dirroot}/customfield/tests/fixtures/test_instance_form.php");

        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();

        /** @var core_customfield_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_customfield');

        $category = $generator->create_category();
        $field = $generator->create_field(['categoryid' => $category->get('id'), 'type' => 'number', 'configdata' => [
            'minimumvalue' => 5,
            'maximumvalue' => 10,
        ]]);

        $data = \core_customfield\data_controller::create(0, null, $field);

        // Less than minimum value.
        $formdata = array_merge((array) $course, ['customfield_' . $field->get('shortname') => 2]);
        $this->assertEquals([
            'customfield_' . $field->get('shortname') => 'Value must be greater than or equal to 5',
        ], $data->instance_form_validation($formdata, []));

        // Greater than maximum value.
        $formdata = array_merge((array) $course, ['customfield_' . $field->get('shortname') => 12]);
        $this->assertEquals([
            'customfield_' . $field->get('shortname') => 'Value must be less than or equal to 10',
        ], $data->instance_form_validation($formdata, []));
    }

    /**
     * Test submitting field instance form
     */
    public function test_form_save(): void {
        global $CFG;

        require_once("{$CFG->dirroot}/customfield/tests/fixtures/test_instance_form.php");

        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();

        /** @var core_customfield_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_customfield');

        $category = $generator->create_category();
        $field = $generator->create_field(['categoryid' => $category->get('id'), 'type' => 'number']);

        $formdata = array_merge((array) $course, ['customfield_' . $field->get('shortname') => 42]);
        core_customfield_test_instance_form::mock_submit($formdata);

        $form = new core_customfield_test_instance_form('POST', ['handler' => $category->get_handler(), 'instance' => $course]);
        $this->assertTrue($form->is_validated());

        $formsubmission = $form->get_data();
        $this->assertEquals(42.0, $formsubmission->{'customfield_' . $field->get('shortname')});
        $category->get_handler()->instance_form_save($formsubmission);
    }

    /**
     * Data provider for {@see test_export_value}
     *
     * @return array[]
     */
    public static function export_value_provider(): array {
        $template = '<span class="multilang" lang="en">$ {value}</span><span class="multilang" lang="es">€ {value}</span>';
        $whenzero = '<span class="multilang" lang="en">Unknown</span><span class="multilang" lang="es">Desconocido</span>';
        return [
            'Export float value' => [42, '42.00', [
                'decimalplaces' => 2,
                'display' => '{value}',
                'displaywhenzero' => 0,
            ]],
            'Export value with a prefix' => [10, '$ 10.00', [
                'decimalplaces' => 2,
                'display' => $template,
                'displaywhenzero' => 0,
            ]],
            'Export value when zero' => [0, 'Unknown', [
                'display' => '{value}',
                'displaywhenzero' => $whenzero,
            ]],
            'Export value when not set' => ['', null, [
                'display' => '{value}',
                'displaywhenzero' => $whenzero,
            ]],
            'Export almost zero that rounds to non-zero' => [0.0009, '0.001', [
                'decimalplaces' => 3,
                'display' => '{value}',
                'displaywhenzero' => 'Free',
            ]],
            'Export almost zero that rounds to zero' => [0.0004, 'Free', [
                'decimalplaces' => 3,
                'display' => '{value}',
                'displaywhenzero' => 'Free',
            ]],
            'Export when config not set' => [42, '42', []],
            'Export zero when config not set' => [0, null, []],
        ];
    }

    /**
     * Test exporting instance
     *
     * @param float|string $datavalue
     * @param string|null $expectedvalue
     * @param array $configdata
     *
     * @dataProvider export_value_provider
     */
    public function test_export_value(
        float|string $datavalue,
        string|null $expectedvalue,
        array $configdata,
    ): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        // Enable multilang filter.
        filter_set_global_state('multilang', TEXTFILTER_ON);
        filter_set_applies_to_strings('multilang', true);

        $course = $this->getDataGenerator()->create_course();

        /** @var core_customfield_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_customfield');

        $category = $generator->create_category();
        $field = $generator->create_field([
            'categoryid' => $category->get('id'),
            'type' => 'number',
            'configdata' => $configdata,
        ]);
        $data = $generator->add_instance_data($field, (int) $course->id, $datavalue);

        $result = \core_customfield\data_controller::create($data->get('id'))->export_value();
        $this->assertSame($expectedvalue, $result);
    }
}
