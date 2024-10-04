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
use core_customfield\field_config_form;

/**
 * Tests for the field controller
 *
 * @package    customfield_number
 * @covers     \customfield_number\field_controller
 * @copyright  2024 Paul Holden <paulh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class field_controller_test extends advanced_testcase {

    /**
     * Test that using base field controller returns our number type
     */
    public function test_create(): void {
        $this->resetAfterTest();

        /** @var core_customfield_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_customfield');

        $category = $generator->create_category();
        $field = $generator->create_field(['categoryid' => $category->get('id'), 'type' => 'number']);

        $this->assertInstanceOf(field_controller::class, \core_customfield\field_controller::create((int) $field->get('id')));
        $this->assertInstanceOf(field_controller::class, \core_customfield\field_controller::create(0, $field->to_record()));
    }

    /**
     * Data provider for {@see test_form_definition}
     *
     * @return array[]
     */
    public static function form_definition_provider(): array {
        return [
            'Defaults' => ['', '', '', '{value}', true],
            'Minimum greater than maximum' => ['', 12, 10, '{value}', false],
            'Default value less than minimum' => [1, 10, 12, '{value}', false],
            'Default value greater than maximum' => [13, 10, 12, '{value}', false],
            'Valid' => [11, 10, 12, '{value}', true],
            'Display valid single placeholder' => ['', '', '', '{value}', true],
            'Display invalid single placeholder' => ['', '', '', '111', false],
        ];
    }

    /**
     * Test submitting field definition form
     *
     * @param float|string $defaultvalue
     * @param float|string $minimumvalue
     * @param float|string $maximumvalue
     * @param string $display
     * @param bool $expected
     *
     * @dataProvider form_definition_provider
     */
    public function test_form_definition(
        float|string $defaultvalue,
        float|string $minimumvalue,
        float|string $maximumvalue,
        string $display,
        bool $expected,
    ): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        /** @var core_customfield_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_customfield');

        $category = $generator->create_category();
        $field = $generator->create_field(['categoryid' => $category->get('id'), 'type' => 'number']);

        $submitdata = (array) $field->to_record();
        $submitdata['configdata'] = array_merge($field->get('configdata'), [
            'defaultvalue' => $defaultvalue,
            'minimumvalue' => $minimumvalue,
            'maximumvalue' => $maximumvalue,
            'display' => $display,
        ]);

        $formdata = field_config_form::mock_ajax_submit($submitdata);
        $form = new field_config_form(null, null, 'post', '', null, true, $formdata, true);

        $form->set_data_for_dynamic_submission();
        $this->assertEquals($expected, $form->is_validated());
        if ($expected) {
            $form->process_dynamic_submission();
        }
    }
}
