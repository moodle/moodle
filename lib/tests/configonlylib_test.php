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

defined('MOODLE_INTERNAL') || die();

// Global $CFG not used here intentionally to make sure it is not required inside the lib.
require_once(__DIR__ . '/../configonlylib.php');

/**
 * Unit tests for config only library functions.
 *
 * @package    core
 * @category   test
 * @copyright  2012 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class configonlylib_test extends \advanced_testcase {

    /**
     * Test cleaning of invalid utf-8 entities.
     */
    public function test_min_fix_utf8(): void {
        $this->assertSame('abc', min_fix_utf8('abc'));
        $this->assertSame("žlutý koníček přeskočil potůček \n\t\r", min_fix_utf8("žlutý koníček přeskočil potůček \n\t\r\0"));
        $this->assertSame('aš', min_fix_utf8('a'.chr(130).'š'), 'This fails with buggy iconv() when mbstring extenstion is not available as fallback.');
    }

    /**
     * Test minimalistic parameter cleaning.
     */
    public function test_min_clean_param(): void {
        $this->assertSame('foo', min_clean_param('foo', 'RAW'));
        $this->assertSame('aš', min_clean_param('a'.chr(130).'š', 'RAW'));

        $this->assertSame(1, min_clean_param('1', 'INT'));
        $this->assertSame(1, min_clean_param('1aa', 'INT'));

        $this->assertSame('1abc-d_f', min_clean_param('/.1ačž"b?;c-d{}\\_f.', 'SAFEDIR'));
        $this->assertSame(1, min_clean_param('1aa', 'INT'));

        $this->assertSame('/a/b/./c5', min_clean_param('/a*?$//b/.../c5', 'SAFEPATH'));
        $this->assertSame(1, min_clean_param('1aa', 'INT'));
    }

    /**
     * Test minimalistic getting of page parameters.
     */
    public function test_min_optional_param(): void {
        $this->resetAfterTest();

        $_GET['foo'] = 'bar';
        $_GET['num'] = '1';
        $_GET['xnum'] = '1aa';

        $_POST['foo'] = 'rebar';
        $_POST['oof'] = 'rab';

        $this->assertSame('bar', min_optional_param('foo', null, 'RAW'));
        $this->assertSame(null, min_optional_param('foo2', null, 'RAW'));
        $this->assertSame('rab', min_optional_param('oof', null, 'RAW'));

        $this->assertSame(1, min_optional_param('num', null, 'INT'));
        $this->assertSame(1, min_optional_param('xnum', null, 'INT'));
    }

    /**
     * Test fail-safe minimalistic slashargument processing.
     */
    public function test_min_get_slash_argument(): void {
        global $CFG;

        $this->resetAfterTest();
        $this->assertEquals('https://www.example.com/moodle', $CFG->wwwroot);

        $_SERVER = array();
        $_SERVER['SERVER_SOFTWARE'] = 'Apache/2.2.22 (Unix)';
        $_SERVER['QUERY_STRING'] = 'theme=standard&component=core&rev=5&image=u/f1';
        $_SERVER['REQUEST_URI'] = '/moodle/theme/image.php?theme=standard&component=core&rev=5&image=u/f1';
        $_SERVER['SCRIPT_NAME'] = '/moodle/theme/image.php';
        $this->assertSame('', min_get_slash_argument());

        $_SERVER = array();
        $_SERVER['SERVER_SOFTWARE'] = 'Apache/2.2.22 (Unix)';
        $_SERVER['QUERY_STRING'] = '';
        $_SERVER['REQUEST_URI'] = '/moodle/theme/image.php/standard/core/5/u/f1';
        $_SERVER['PATH_INFO'] = '/standard/core/5/u/f1';
        $_SERVER['SCRIPT_NAME'] = '/moodle/theme/image.php';
        $_GET = array();
        $this->assertSame('/standard/core/5/u/f1', min_get_slash_argument());

        // IIS no url rewriting.
        $_SERVER = array();
        $_SERVER['SERVER_SOFTWARE'] = 'Microsoft-IIS/7.0';
        $_SERVER['QUERY_STRING'] = '';
        $_SERVER['REQUEST_URI'] = '/moodle/theme/image.php/standard/core/5/u/f1';
        $_SERVER['PATH_INFO'] = '/standard/core/5/u/f1';
        $_SERVER['SCRIPT_NAME'] = '/moodle/theme/image.php';
        $_GET = array();
        $this->assertSame('/standard/core/5/u/f1', min_get_slash_argument());

        // IIS with url rewriting.
        $_SERVER = array();
        $_SERVER['SERVER_SOFTWARE'] = 'Microsoft-IIS/7.0';
        $_SERVER['QUERY_STRING'] = 'file=/standard/core/5/u/f1';
        $_SERVER['REQUEST_URI'] = '/moodle/theme/image.php/standard/core/5/u/f1';
        $_SERVER['PATH_INFO'] = '/';
        $_SERVER['SCRIPT_NAME'] = '/moodle/theme/image.php';
        $_GET = array();
        $_GET['file'] = '/standard/core/5/u/f1';
        $this->assertSame('/standard/core/5/u/f1', min_get_slash_argument());

        $_SERVER = array();
        $_SERVER['SERVER_SOFTWARE'] = 'Weird server';
        $_SERVER['QUERY_STRING'] = '';
        $_SERVER['REQUEST_URI'] = '/moodle/theme/image.php/standard/core/5/u/f1';
        $_SERVER['PATH_INFO'] = '/moodle/theme/image.php/standard/core/5/u/f1';
        $_SERVER['SCRIPT_NAME'] = '/moodle/theme/image.php';
        $_GET = array();
        $this->assertSame('/standard/core/5/u/f1', min_get_slash_argument());

        $_SERVER = array();
        $_SERVER['SERVER_SOFTWARE'] = 'Hacker server';
        $_SERVER['QUERY_STRING'] = '';
        $_SERVER['REQUEST_URI'] = '/moodle/theme/image.php/standard/core/5/u/f1';
        $_SERVER['PATH_INFO'] = '/moodle/theme/image.php/standard\\core/..\\../5/u/f1';
        $_SERVER['SCRIPT_NAME'] = '/moodle/theme/image.php';
        $_GET = array();
        // Windows dir separators are removed, multiple ... gets collapsed to one .
        $this->assertSame('/standardcore/./5/u/f1', min_get_slash_argument());
    }

    /**
     * Test the min_get_minimum_version function.
     *
     * @covers ::min_get_minimum_revision
     */
    public function test_min_get_minimum_version(): void {
        // This is fairly hard to write a test for, but we can at least check that it returns a number
        // greater than the version when the feature was first introduced.
        $firstintroduced = 1693612800; // Equivalent to 20230902 00:00:00 GMT.
        // Deduct our two day tolerance.
        $firstintroduced = $firstintroduced - (DAYSECS * 2);
        $this->assertGreaterThanOrEqual($firstintroduced, min_get_minimum_revision());
    }

    /**
     * Test the min_get_maximum_version function.
     *
     * @covers ::min_get_maximum_revision
     */
    public function test_min_get_maximum_version(): void {
        // The maximum version should be set to a time in the near future.
        // This is currently defined as "in the next minute".
        // Note: We use a 65 second window to allow for slow test runners.
        $this->assertGreaterThan(time(), min_get_maximum_revision());
        $this->assertLessThanOrEqual(time() + 65, min_get_maximum_revision());
    }

    /**
     * Test the min_is_revision_valid_and_current function.
     *
     * @covers ::min_is_revision_valid_and_current
     * @dataProvider min_is_revision_valid_and_current_provider
     */
    public function test_min_is_revision_valid_and_current(int $revision, bool $expected): void {
        $this->assertEquals($expected, min_is_revision_valid_and_current($revision));
    }

    /**
     * Data provider for the min_is_revision_valid_and_current tests.
     *
     * @return array
     */
    public static function min_is_revision_valid_and_current_provider(): array {
        return [
            'Negative value' => [-1, false],
            'Empty value' => [0, false],
            'A time before the minimum accepted value' => [min_get_minimum_revision() - 1, false],
            'The minimum accepted value' => [min_get_minimum_revision(), true],
            'The current time' => [time(), true],
            // Note: We have to be careful using time values because the data provider is run at the start of the phpunit run,
            // but the test may not be run for some time.
            // On a slower machine and/or database, this could be several hours.
            // For a more specific time we must have a specific test function.
            'A time in the future' => [time() + DAYSECS, false]
        ];
    }


    /**
     * Test the min_is_revision_valid_and_current function with close times.
     *
     * Note: These tests are incompatible with data providers.
     *
     * @covers ::min_is_revision_valid_and_current
     */
    public function test_min_is_revision_valid_and_current_close_proximity(): void {
        // A time in the near future.
        $this->assertTrue(min_is_revision_valid_and_current(time() + 55));

        // A time in the too-far future.
        $this->assertFalse(min_is_revision_valid_and_current(time() + 70));

    }
}
