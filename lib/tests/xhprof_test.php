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
 * Unit tests for the xhprof class.
 *
 * @package   core
 * @category  test
 * @copyright 2019 Brendan Heywood <brendan@catalyst-au.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class xhprof_test extends \advanced_testcase {

    /**
     * Data provider for string matches
     *
     * @return  array
     */
    public function profiling_string_matches_provider() {
        return [
            ['/index.php',              '/index.php',           true],
            ['/some/dir/index.php',     '/index.php',           false],
            ['/course/view.php',        '/course/view.php',     true],
            ['/view.php',               '/course/view.php',     false],
            ['/mod/forum',              '/mod/forum/*',         false],
            ['/mod/forum/',             '/mod/forum/*',         true],
            ['/mod/forum/index.php',    '/mod/forum/*',         true],
            ['/mod/forum/foo.php',      '/mod/forum/*',         true],
            ['/mod/forum/view.php',     '/mod/*/view.php',      true],
            ['/mod/one/two/view.php',   '/mod/*/view.php',      true],
            ['/view.php',               '*/view.php',           true],
            ['/mod/one/two/view.php',   '*/view.php',           true],
            ['/foo.php',                '/foo.php,/bar.php',    true],
            ['/bar.php',                '/foo.php,/bar.php',    true],
            ['/foo/bar.php',            "/foo.php,/bar.php",    false],
            ['/foo/bar.php',            "/foo.php,*/bar.php",   true],
            ['/foo/bar.php',            "/foo*.php,/bar.php",   true],
            ['/foo.php',                "/foo.php\n/bar.php",   true],
            ['/bar.php',                "/foo.php\n/bar.php",   true],
            ['/foo/bar.php',            "/foo.php\n/bar.php",   false],
            ['/foo/bar.php',            "/foo.php\n*/bar.php",  true],
            ['/foo/bar.php',            "/foo*.php\n/bar.php",  true],
        ];
    }

    /**
     * Test the matching syntax
     *
     * @dataProvider profiling_string_matches_provider
     * @param   string $string
     * @param   string $patterns
     * @param   bool   $expected
     */
    public function test_profiling_string_matches($string, $patterns, $expected) {

        global $CFG;
        require_once($CFG->libdir . '/xhprof/xhprof_moodle.php');

        $result = profiling_string_matches($string, $patterns);
        $this->assertSame($result, $expected);
    }

}

