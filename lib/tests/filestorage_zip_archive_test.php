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

namespace core;

use zip_archive;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->libdir . '/filestorage/zip_archive.php');

/**
 * Unit tests for /lib/filestorage/zip_archive.php.
 *
 * @package   core
 * @copyright 2020 Université Rennes 2 {@link https://www.univ-rennes2.fr}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class filestorage_zip_archive_test extends \advanced_testcase {
    /**
     * Test mangle_pathname() method.
     *
     * @dataProvider pathname_provider
     *
     * @param string $string   Parameter sent to mangle_pathname method.
     * @param string $expected Expected return value.
     */
    public function test_mangle_pathname($string, $expected) {
        $ziparchive = new zip_archive();

        $method = new \ReflectionMethod('zip_archive', 'mangle_pathname');

        $result = $method->invoke($ziparchive, $string);
        $this->assertSame($expected, $result);
    }

    /**
     * Provide some tested pathnames and expected results.
     *
     * @return array Array of tested pathnames and expected results.
     */
    public function pathname_provider() {
        return [
            // Test a string.
            ['my file.pdf', 'my file.pdf'],

            // Test a string with MS separator.
            ['c:\temp\my file.pdf', 'c:/temp/my file.pdf'],

            // Test a string with 2 consecutive dots.
            ['my file..pdf', 'my file.pdf'],

            // Test a string with 3 consecutive dots.
            ['my file...pdf', 'my file.pdf'],

            // Test a string beginning with leading slash.
            ['/tmp/my file.pdf', 'tmp/my file.pdf'],

            // Test some path traversal attacks.
            ['../../../../../etc/passwd', 'etc/passwd'],
            ['../', ''],
            ['.../...//', ''],
            ['.', ''],
        ];
    }
}
