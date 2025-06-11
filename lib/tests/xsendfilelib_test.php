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

/**
 * Tests for lib/xsendfilelib.php.
 *
 * Please note that the PHP CLI SAPI used by PHPUnit does not return headers so some tests would be pointless to run.
 *
 * @package    core
 * @category   test
 * @copyright  2024 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers ::xsendfile
 */
final class xsendfilelib_test extends \advanced_testcase {
    public static function setUpBeforeClass(): void {
        global $CFG;
        require_once($CFG->libdir . '/xsendfilelib.php');
        parent::setUpBeforeClass();
    }

    public function test_not_enabled(): void {
        global $CFG;

        $this->resetAfterTest();

        // Ensure it is disabled.
        $CFG->xsendfile = '';

        // Use a file that would otherwise pass.
        $this->assertFalse(xsendfile($CFG->dataroot . '/.htaccess'));
    }

    public function test_file_not_found(): void {
        global $CFG;

        $this->resetAfterTest();

        // Ensure it is disabled.
        $CFG->xsendfile = 'X-Accel-Redirect';

        $this->assertFalse(xsendfile($CFG->dataroot . '/FILE_NOT_FOUND'));
    }

    public function test_file_found_headers_sent(): void {
        global $CFG;

        $this->resetAfterTest();

        // Ensure it is disabled.
        $CFG->xsendfile = 'X-Accel-Redirect';

        // This is a weird ond - we can't explicitly send headers, but we know that phpunit does.
        $this->assertFalse(xsendfile($CFG->dataroot . '/.htaccess'));
    }

    /**
     * Test that a file served from a request dir is not served.
     *
     * @runInSeparateProcess
     */
    public function test_file_found_request_dir(): void {
        global $CFG;

        $this->resetAfterTest();

        // Ensure it is disabled.
        $CFG->xsendfile = 'X-Accel-Redirect';
        $CFG->xsendfilealiases = [
            '/request/' => make_request_directory(),
        ];

        $dir = make_request_directory();
        $file = $dir . '/testfile.txt';
        file_put_contents($file, 'Hello, world!');

        // Use a file that would otherwise pass.
        $this->assertFalse(xsendfile($file));
    }

    /**
     * Test that a file served from an aliased dir is served.
     *
     * @runInSeparateProcess
     */
    public function test_nginx_accelerated(): void {
        global $CFG;

        $this->resetAfterTest();

        // Ensure it is enabled.
        $CFG->xsendfile = 'X-Accel-Redirect';
        $CFG->xsendfilealiases = [
            '/my/moodle/alias/moodledata/' => $CFG->dataroot,
        ];

        $file = $CFG->dataroot . '/testfile.txt';
        file_put_contents($file, 'Hello, world!');

        $this->assertTrue(xsendfile($file));

        // Note: The `headers_list()` method does not work with the CLI SAPI.
        // We can use xdebug if it's enabled.
        // This is mostly to aid debugging as it is not common to have xdebug enabled during CI tests.
        if (extension_loaded('xdebug')) {
            $headers = xdebug_get_headers();
            $this->assertNotEmpty($headers);
            $this->assertContains('X-Accel-Redirect: /my/moodle/alias/moodledata/testfile.txt', $headers);
        }
    }

    /**
     * Test that a file served from an unknown alias is not served.
     *
     * @runInSeparateProcess
     */
    public function test_nginx_no_alias(): void {
        global $CFG;

        $this->resetAfterTest();

        // Ensure it is enabled.
        $CFG->xsendfile = 'X-Accel-Redirect';
        $CFG->xsendfilealiases = [
            '/my/moodle/alias/requestdir/' => make_request_directory(),
        ];

        $file = $CFG->dataroot . '/testfile.txt';
        file_put_contents($file, 'Hello, world!');

        $this->assertFalse(xsendfile($file));
    }

    /**
     * Test that an alias dir which doesn't exist is ignored.
     *
     * @runInSeparateProcess
     */
    public function test_nginx_alias_dir_not_found(): void {
        global $CFG;

        $this->resetAfterTest();

        $filedir = "{$CFG->dataroot}/non/existent/directory";

        // Ensure it is enabled.
        $CFG->xsendfile = 'X-Accel-Redirect';
        $CFG->xsendfilealiases = [
            '/my/moodle/alias/' => $filedir,
        ];

        $file = $CFG->dataroot . '/testfile.txt';
        file_put_contents($file, 'Hello, world!');

        $this->assertFalse(xsendfile($file));
    }
}
