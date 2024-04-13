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

namespace core_privacy;

defined('MOODLE_INTERNAL') || die();

global $CFG;

use core_privacy\local\metadata\collection;
use core_privacy\local\metadata\types;

/**
 * Tests for the \core_privacy API's collection functionality.
 *
 * @package     core_privacy
 * @category    test
 * @copyright   2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \core_privacy\local\metadata\collection
 */
class collection_test extends \advanced_testcase {

    /**
     * Test that adding an unknown type causes the type to be added to the collection.
     *
     * @covers ::add_type
     */
    public function test_add_type_generic_type(): void {
        $collection = new collection('core_privacy');

        // Mock a new types\type.
        $mockedtype = $this->createMock(types\type::class);
        $collection->add_type($mockedtype);

        $items = $collection->get_collection();
        $this->assertCount(1, $items);
        $this->assertEquals($mockedtype, reset($items));
    }

    /**
     * Test that adding a known type works as anticipated.
     *
     * @covers ::add_type
     */
    public function test_add_type_known_type(): void {
        $collection = new collection('core_privacy');

        $linked = new types\subsystem_link('example', [], 'langstring');
        $collection->add_type($linked);

        $items = $collection->get_collection();
        $this->assertCount(1, $items);
        $this->assertEquals($linked, reset($items));
    }

    /**
     * Test that adding multiple types returns them all.
     *
     * @covers ::add_type
     */
    public function test_add_type_multiple(): void {
        $collection = new collection('core_privacy');

        $a = new types\subsystem_link('example', [], 'langstring');
        $collection->add_type($a);

        $b = new types\subsystem_link('example', [], 'langstring');
        $collection->add_type($b);

        $items = $collection->get_collection();
        $this->assertCount(2, $items);
    }

    /**
     * Test that the add_database_table function adds a database table.
     *
     * @covers ::add_database_table
     */
    public function test_add_database_table(): void {
        $collection = new collection('core_privacy');

        $name = 'example';
        $fields = ['field' => 'description'];
        $summary = 'summarisation';

        $collection->add_database_table($name, $fields, $summary);

        $items = $collection->get_collection();
        $this->assertCount(1, $items);
        $item = reset($items);
        $this->assertInstanceOf(types\database_table::class, $item);
        $this->assertEquals($name, $item->get_name());
        $this->assertEquals($fields, $item->get_privacy_fields());
        $this->assertEquals($summary, $item->get_summary());
    }

    /**
     * Test that the add_user_preference function adds a single user preference.
     *
     * @covers ::add_user_preference
     */
    public function test_add_user_preference(): void {
        $collection = new collection('core_privacy');

        $name = 'example';
        $summary = 'summarisation';

        $collection->add_user_preference($name, $summary);

        $items = $collection->get_collection();
        $this->assertCount(1, $items);
        $item = reset($items);
        $this->assertInstanceOf(types\user_preference::class, $item);
        $this->assertEquals($name, $item->get_name());
        $this->assertEquals($summary, $item->get_summary());
    }

    /**
     * Test that the link_external_location function links an external location.
     *
     * @covers ::link_external_location
     */
    public function test_link_external_location(): void {
        $collection = new collection('core_privacy');

        $name = 'example';
        $fields = ['field' => 'description'];
        $summary = 'summarisation';

        $collection->link_external_location($name, $fields, $summary);

        $items = $collection->get_collection();
        $this->assertCount(1, $items);
        $item = reset($items);
        $this->assertInstanceOf(types\external_location::class, $item);
        $this->assertEquals($name, $item->get_name());
        $this->assertEquals($fields, $item->get_privacy_fields());
        $this->assertEquals($summary, $item->get_summary());
    }

    /**
     * Test that the link_subsystem function links the subsystem.
     *
     * @covers ::link_subsystem
     */
    public function test_link_subsystem(): void {
        $collection = new collection('core_privacy');

        $name = 'example';
        $summary = 'summarisation';

        $collection->link_subsystem($name, $summary);

        $items = $collection->get_collection();
        $this->assertCount(1, $items);
        $item = reset($items);
        $this->assertInstanceOf(types\subsystem_link::class, $item);
        $this->assertEquals($name, $item->get_name());
        $this->assertEquals($summary, $item->get_summary());
    }

    /**
     * Test that the link_plugintype function links the plugin.
     *
     * @covers ::link_plugintype
     */
    public function test_link_plugintype(): void {
        $collection = new collection('core_privacy');

        $name = 'example';
        $summary = 'summarisation';

        $collection->link_plugintype($name, $summary);

        $items = $collection->get_collection();
        $this->assertCount(1, $items);
        $item = reset($items);
        $this->assertInstanceOf(types\plugintype_link::class, $item);
        $this->assertEquals($name, $item->get_name());
        $this->assertEquals($summary, $item->get_summary());
    }

    /**
     * Data provider to supply a list of valid components.
     *
     * @return  array
     */
    public static function component_list_provider(): array {
        return [
            ['core_privacy'],
            ['mod_forum'],
        ];
    }

    /**
     * Test that we can get the component correctly.
     *
     * The component will be used for string translations.
     *
     * @dataProvider component_list_provider
     * @param   string  $component The component to test
     * @covers ::get_component
     */
    public function test_get_component($component): void {
        $collection = new collection($component);

        $this->assertEquals($component, $collection->get_component());
    }
}
