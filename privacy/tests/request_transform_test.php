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
 * Unit Tests for the request transform helper.
 *
 * @package     core_privacy
 * @category    test
 * @copyright   2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

use \core_privacy\local\request\transform;

/**
 * Tests for the \core_privacy API's request transform helper functionality.
 *
 * @copyright   2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \core_privacy\local\request\transform
 */
class request_transform_test extends advanced_testcase {
    /**
     * Test that user translation currently does nothing.
     *
     * We have not determined if we will do this or not, but we provide the functionality and encourgae people to use
     * it so that it can be retrospectively fitted if required.
     *
     * @covers ::user
     */
    public function test_user() {
        // Note: This test currently sucks, but there's no point creating users just to test this.
        for ($i = 0; $i < 10; $i++) {
            $this->assertEquals($i, transform::user($i));
        }
    }

    /**
     * Test that the datetime is translated into a string.
     *
     * @covers ::datetime
     */
    public function test_datetime() {
        $time = 1;

        $datestr = transform::datetime($time);

        // Assert it is a string.
        $this->assertInternalType('string', $datestr);

        // To prevent failures on MAC where we are returned with a lower-case 'am' we want to convert this to 'AM'.
        $datestr = str_replace('am', 'AM', $datestr);

        // Assert the formatted date is correct.
        $dateobj = new DateTime();
        $dateobj->setTimestamp($time);
        $this->assertEquals($dateobj->format('l, j F Y, g:i A'), $datestr);
    }

    /**
     * Test that the date is translated into a string.
     *
     * @covers ::date
     */
    public function test_date() {
        $time = 1;

        $datestr = transform::date($time);

        // Assert it is a string.
        $this->assertInternalType('string', $datestr);

        // Assert the formatted date is correct.
        $dateobj = new DateTime();
        $dateobj->setTimestamp($time);
        $this->assertEquals($dateobj->format('j F Y'), $datestr);
    }

    /**
     * Ensure that the yesno function translates correctly.
     *
     * @dataProvider yesno_provider
     * @param   mixed   $input The input to test
     * @param   string  $expected The expected value
     * @covers ::yesno
     */
    public function test_yesno($input, $expected) {
        $this->assertEquals($expected, transform::yesno($input));
    }

    /**
     * Data provider for tests of the yesno transformation.
     *
     * @return  array
     */
    public function yesno_provider() {
        return [
            'Bool False' => [
                false,
                get_string('no'),
            ],
            'Bool true' => [
                true,
                get_string('yes'),
            ],
            'Int 0' => [
                0,
                get_string('no'),
            ],
            'Int 1' => [
                1,
                get_string('yes'),
            ],
            'String 0' => [
                '0',
                get_string('no'),
            ],
            'String 1' => [
                '1',
                get_string('yes'),
            ],
        ];
    }
}
