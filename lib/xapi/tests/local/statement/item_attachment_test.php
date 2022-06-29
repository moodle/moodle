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
use core_xapi\iri;
use core_xapi\xapi_exception;

/**
 * Contains test cases for testing statement attachment class.
 *
 * @package    core_xapi
 * @since      Moodle 3.9
 * @copyright  2020 Ferran Recio
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class item_attachment_test extends advanced_testcase {

    /**
     * Test item creation.
     */
    public function test_create() {

        $data = $this->get_generic_data();
        $item = item_attachment::create_from_data($data);

        $this->assertEquals(json_encode($item), json_encode($data));
    }

    /**
     * return a generic data to create a valid item.
     *
     * @return sdtClass the creation data
     */
    private function get_generic_data(): \stdClass {
        return (object) [
            'usageType' => iri::generate('example', 'attachment'),
            'display' => (object) [
                'en-US' => 'Example',
            ],
            'description' => (object) [
                'en-US' => 'Description example',
            ],
            "contentType" => "image/jpg",
            "length" => 1234,
            "sha2" => "b94c0f1cffb77475c6f1899111a0181efe1d6177"
        ];
    }

    /**
     * Test for invalid values.
     *
     * @dataProvider invalid_values_data
     * @param string $attr attribute to modify
     * @param mixed $newvalue new value (null means unset)
     */
    public function test_invalid_values(string $attr, $newvalue): void {

        $data = $this->get_generic_data();
        if ($newvalue === null) {
            unset($data->$attr);
        } else {
            $data->$attr = $newvalue;
        }

        $this->expectException(xapi_exception::class);
        $item = item_attachment::create_from_data($data);
    }

    /**
     * Data provider for the test_invalid_values tests.
     *
     * @return  array
     */
    public function invalid_values_data() : array {
        return [
            'No usageType attachment' => [
                'usageType', null
            ],
            'Invalid usageType attachment' => [
                'usageType', 'Invalid IRI'
            ],
            'No display attachment' => [
                'display', null
            ],
            'No contentType attachment' => [
                'contentType', null
            ],
            'No length attachment' => [
                'length', null
            ],
            'Invalid length attachment' => [
                'length', 'Invalid'
            ],
            'No sha2 attachment' => [
                'sha2', null
            ],
        ];
    }
}
