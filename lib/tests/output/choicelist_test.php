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

/**
 * Unit tests for core\output\choice class.
 *
 * @package   core
 * @category  test
 * @copyright 2023 Ferran Recio <ferran@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\output;

use advanced_testcase;

/**
 * Unit tests for the `icon_system` class.
 *
 * @coversDefaultClass \core\output\choicelist
 */
class choicelist_test extends advanced_testcase {
    /**
     * Test for a choice without options.
     *
     * @covers ::_construct
     * @covers ::add_option
     * @covers ::export_for_template
     */
    public function test_empty_export(): void {
        $page = new \moodle_page();
        $page->set_url('/user/profile.php');
        $page->set_context(\context_system::instance());
        $renderer = $page->get_renderer('core');

        $choice = new choicelist('Choose an option');
        $export = $choice->export_for_template($renderer);

        $this->assertEquals('Choose an option', $export['description']);
        $this->assertEquals(false, $export['hasoptions']);
        $this->assertEquals([], $export['options']);
    }

    /**
     * Test for a choice with basic options.
     *
     * @covers ::_construct
     * @covers ::add_option
     * @covers ::export_for_template
     */
    public function test_basic_export(): void {
        $page = new \moodle_page();
        $page->set_url('/user/profile.php');
        $page->set_context(\context_system::instance());
        $renderer = $page->get_renderer('core');

        $choice = new choicelist('Choose an option');
        $choice->add_option('option1', 'Option 1');
        $choice->add_option('option2', 'Option 2');

        $export = $choice->export_for_template($renderer);

        $this->assertEquals('Choose an option', $export['description']);
        $this->assertEquals(true, $export['hasoptions']);
        $this->assertCount(2, $export['options']);
        $this->validate_option($export['options'][0], 'option1', 'Option 1', []);
        $this->validate_option($export['options'][1], 'option2', 'Option 2', []);
    }
    /**
     * Test for a choice with extras options definition.
     *
     * @covers ::_construct
     * @covers ::add_option
     * @covers ::set_option_extras
     * @covers ::export_for_template
     */
    public function test_option_defintion_export(): void {
        $page = new \moodle_page();
        $page->set_url('/user/profile.php');
        $page->set_context(\context_system::instance());
        $renderer = $page->get_renderer('core');

        $choice = new choicelist('Choose an option');
        $definition1 = [
            'disabled' => true,
            'description' => 'Description',
            'url' => new \moodle_url('/user/profile.php'),
            'icon' => new \pix_icon('i/grade', 'Grade'),
            'extras' => [
                'data-attribute' => 'value',
            ],
        ];
        $choice->add_option('option1', 'Option 1', $definition1);
        $definition2 = [
            'disabled' => false,
            'description' => null,
            'url' => null,
            'icon' => null,
            'extras' => null,
        ];
        $choice->add_option('option2', 'Option 2', $definition2);

        $export = $choice->export_for_template($renderer);

        $this->assertEquals('Choose an option', $export['description']);
        $this->assertEquals(true, $export['hasoptions']);
        $this->assertCount(2, $export['options']);
        $definition1['iconexport'] = $definition1['icon']->export_for_pix($renderer);
        $this->validate_option($export['options'][0], 'option1', 'Option 1', $definition1);
        $this->validate_option($export['options'][1], 'option2', 'Option 2', $definition2);
    }

    /**
     * Test for a choice with option selected.
     *
     * @covers ::_construct
     * @covers ::add_option
     * @covers ::set_selected_value
     * @covers ::get_selected_value
     * @covers ::export_for_template
     */
    public function test_option_selected_export(): void {
        $page = new \moodle_page();
        $page->set_url('/user/profile.php');
        $page->set_context(\context_system::instance());
        $renderer = $page->get_renderer('core');

        $choice = new choicelist('Choose an option');
        $choice->add_option('option1', 'Option 1');
        $choice->add_option('option2', 'Option 2');
        $choice->set_selected_value('option1');

        $export = $choice->export_for_template($renderer);

        $this->assertEquals('Choose an option', $export['description']);
        $this->assertEquals(true, $export['hasoptions']);
        $this->assertCount(2, $export['options']);
        $this->assertEquals('option1', $choice->get_selected_value());
        $this->validate_option($export['options'][0], 'option1', 'Option 1', [], true);
        $this->validate_option($export['options'][1], 'option2', 'Option 2', []);
    }

    /**
     * Validate a choice option export.
     * @param array $option the option export
     * @param string $value the option value
     * @param string $name the option name
     * @param array|null $definition the option definition
     * @param bool $selected if the option is selected
     */
    private function validate_option(
        array $option,
        string $value,
        string $name,
        ?array $definition,
        bool $selected = false
    ): void {
        $this->assertEquals($value, $option['value']);
        $this->assertEquals($name, $option['name']);
        $this->assertEquals($definition['disabled'] ?? false, $option['disabled']);
        $this->assertEquals($definition['description'] ?? null, $option['description']);
        if (isset($definition['url'])) {
            $this->assertEquals($definition['url']->out(true), $option['url']);
            $this->assertTrue($option['hasurl']);
        }
        if (isset($definition['icon'])) {
            $this->assertEquals($definition['iconexport'], $option['icon']);
            $this->assertTrue($option['hasicon']);
        }
        if ($selected) {
            $this->assertTrue($option['selected']);
        }
        if (isset($definition['extras'])) {
            foreach ($option['extras'] as $extra) {
                $attribute = $extra['attribute'];
                $this->assertEquals($definition['extras'][$attribute], $extra['value']);
            }
        } else {
            $this->assertFalse(isset($option['extras']));
        }
    }

    /**
     * Test for a choice with option selected.
     *
     * @covers ::_construct
     * @covers ::add_option
     * @covers ::set_selected_value
     * @covers ::get_selected_value
     * @covers ::set_allow_empty
     * @covers ::get_allow_empty
     * @covers ::export_for_template
     */
    public function test_set_allow_empty(): void {
        $choice = new choicelist('Choose an option');
        $choice->add_option('option1', 'Option 1');
        $choice->add_option('option2', 'Option 2');

        $choice->set_allow_empty(true);
        $this->assertTrue($choice->get_allow_empty());
        $this->assertNull($choice->get_selected_value());

        $choice->set_allow_empty(false);
        $this->assertFalse($choice->get_allow_empty());
        $this->assertEquals('option1', $choice->get_selected_value());

        // Validate the null selected value is not changed when allow empty is set to true.
        $choice->set_allow_empty(true);
        $this->assertTrue($choice->get_allow_empty());
        $this->assertNull($choice->get_selected_value());

        $choice->set_selected_value('option2');

        $choice->set_allow_empty(false);
        $this->assertFalse($choice->get_allow_empty());
        $this->assertEquals('option2', $choice->get_selected_value());

        $choice->set_allow_empty(true);
        $this->assertTrue($choice->get_allow_empty());
        $this->assertEquals('option2', $choice->get_selected_value());
    }
}
