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
 * Contains test cases for testing statement definition class.
 *
 * @package    core_xapi
 * @since      Moodle 3.9
 * @copyright  2020 Ferran Recio
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class item_definition_test extends advanced_testcase {

    /**
     * Test item_definition creation.
     *
     * @dataProvider creation_provider
     * @param string  $interactiontype
     */
    public function test_creation(string  $interactiontype): void {

        // Activity without interactionType.
        $data = (object) [
            'type' => iri::generate('example', 'id'),
        ];

        // Add interactionType.
        if (!empty($interactiontype)) {
            $data->interactionType = $interactiontype;
        }
        $item = item_definition::create_from_data($data);

        $this->assertEquals(json_encode($item), json_encode($data));
        if (empty($interactiontype)) {
            $this->assertNull($item->get_interactiontype());
        } else {
            $this->assertEquals($interactiontype, $item->get_interactiontype());
        }
    }

    /**
     * Data provider for the test_creation tests.
     *
     * @return  array
     */
    public static function creation_provider(): array {
        return [
            'No interactionType' => [''],
            'Choice' => ['choice'],
            'fill-in' => ['fill-in'],
            'long-fill-in' => ['long-fill-in'],
            'true-false' => ['true-false'],
            'matching' => ['matching'],
            'performance' => ['performance'],
            'sequencing' => ['sequencing'],
            'likert' => ['likert'],
            'numeric' => ['numeric'],
            'other' => ['other'],
            'compound' => ['compound'],
        ];
    }

    /**
     * Test for invalid structures.
     */
    public function test_invalid_data(): void {
        // Activity without interactionType.
        $data = (object) [
            'interactionType' => 'Invalid value!',
        ];

        $this->expectException(xapi_exception::class);
        $item = item_definition::create_from_data($data);
    }
}
