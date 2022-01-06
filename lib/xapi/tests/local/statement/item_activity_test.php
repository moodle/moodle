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
 * This file contains unit test related to xAPI library.
 *
 * @package    core_xapi
 * @copyright  2020 Ferran Recio
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_xapi\local\statement;

use advanced_testcase;
use core_xapi\xapi_exception;
use core_xapi\iri;

defined('MOODLE_INTERNAL') || die();

/**
 * Contains test cases for testing statement activity class.
 *
 * @package    core_xapi
 * @since      Moodle 3.9
 * @copyright  2020 Ferran Recio
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class item_activity_testcase extends advanced_testcase {

    /**
     * Test item creation.
     */
    public function test_creation(): void {

        // Activity without definition.
        $data = (object) [
            'objectType' => 'Activity',
            'id' => iri::generate('paella', 'activity'),
        ];
        $item = item_activity::create_from_data($data);

        $this->assertEquals(json_encode($item), json_encode($data));
        $this->assertEquals($item->get_id(), 'paella');
        $this->assertNull($item->get_definition());

        // Add optional objectType.
        $data->objectType = 'Activity';
        $item = item_activity::create_from_data($data);
        $this->assertEquals(json_encode($item), json_encode($data));

        // Add definition.
        $data->definition = (object) [
            'interactionType' => 'choice',
        ];
        $item = item_activity::create_from_data($data);

        $this->assertEquals(json_encode($item), json_encode($data));
        $this->assertNotNull($item->get_definition());
    }

    /**
     * Test item creation from string.
     *
     * @dataProvider create_from_id_provider
     * @param string $id Object string ID (IRI or not)
     * @param bool $usedefinition if a valir definition must be attached or not
     */
    public function test_create_from_id(string $id, bool $usedefinition): void {

        $definition = null;
        if ($usedefinition) {
            $data = (object) [
                'type' => iri::generate('example', 'id'),
                'interactionType' => 'choice'
            ];
            $definition = item_definition::create_from_data($data);
        }

        $item = item_activity::create_from_id($id, $definition);

        $this->assertEquals($id, $item->get_id());
        $itemdefinition = $item->get_definition();
        if ($usedefinition) {
            $this->assertEquals('choice', $itemdefinition->get_interactiontype());
        } else {
            $this->assertNull($itemdefinition);
        }

        // Check generated data.
        $data = $item->get_data();
        $this->assertEquals('Activity', $data->objectType);
        $this->assertEquals(iri::generate($id, 'activity'), $data->id);
        if ($usedefinition) {
            $this->assertEquals('choice', $data->definition->interactionType);
        }
    }

    /**
     * Data provider for the test_create_from_id tests.
     *
     * @return  array
     */
    public function create_from_id_provider() : array {
        return [
            'Fake IRI with no definition' => [
                'paella', false,
            ],
            'Fake IRI with definition' => [
                'paella', true,
            ],
            'Real IRI with no definition' => [
                'http://adlnet.gov/expapi/activities/example', false,
            ],
            'Real IRI with definition' => [
                'http://adlnet.gov/expapi/activities/example', true,
            ],
        ];
    }

    /**
     * Test for invalid structures.
     *
     * @dataProvider invalid_data_provider
     * @param string  $type objectType attribute
     * @param string  $id activity ID
     */
    public function test_invalid_data(string $type, string $id): void {

        $data = (object) [
            'objectType' => $type,
        ];
        if (!empty($id)) {
            $data->id = $id;
        }

        $this->expectException(xapi_exception::class);
        $item = item_activity::create_from_data($data);
    }

    /**
     * Data provider for the test_invalid_data tests.
     *
     * @return  array
     */
    public function invalid_data_provider() : array {
        return [
            'Invalid Avtivity objectType' => [
                'Invalid Type!', iri::generate('paella', 'activity'),
            ],
            'Invalid id value' => [
                'Activity', 'Invalid_iri_value',
            ],
            'Non-existent id value' => [
                'Activity', '',
            ],
        ];
    }

    /**
     * Test for missing object type.
     */
    public function test_missing_object_type(): void {
        $data = (object) ['id' => 42];
        $this->expectException(xapi_exception::class);
        $item = item_activity::create_from_data($data);
    }

    /**
     * Test for invalid activity objectType.
     */
    public function test_inexistent_agent(): void {
        global $CFG;
        $data = (object) [
            'objectType' => 'Invalid',
            'id' => -1,
        ];
        $this->expectException(xapi_exception::class);
        $item = item_activity::create_from_data($data);
    }
}
