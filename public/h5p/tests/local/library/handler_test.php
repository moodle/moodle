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
 * Testing the H5P library handler.
 *
 * @package    core_h5p
 * @category   test
 * @copyright  2020 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_h5p\local\library;

use advanced_testcase;

/**
 * Test class covering the H5P library handler.
 *
 * @package    core_h5p
 * @copyright  2020 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class handler_test extends advanced_testcase {

    /**
     * Test the behaviour of get_h5p_string().
     *
     * @dataProvider get_h5p_string_provider
     * @param  string $identifier      The key identifier for the localized string.
     * @param  string $expectedresult  Expected result.
     * @param  string $lang            Language to get the localized string.
     */
    public function test_get_h5p_string(string $identifier, ?string $expectedresult, ?string $lang = 'en'): void {
        $result = autoloader::get_h5p_string($identifier, $lang);
        $this->assertEquals($expectedresult, $result);
    }

    /**
     * Data provider for test_get_h5p_string().
     *
     * @return array
     */
    public static function get_h5p_string_provider(): array {
        return [
            'Existing string in h5plib plugin' => [
                'editor:add',
                'Add',
            ],
            'Unexisting translation for an existing string in h5plib plugin (es)' => [
                'editor:add',
                null,
                'es',
            ],
            'Unexisting string in h5plib plugin' => [
                'unexistingstring',
                null,
            ],
            'Unexisting translation for an unexisting string in h5plib plugin (es)' => [
                'unexistingstring',
                null,
                'es',
            ],
        ];
    }
}
