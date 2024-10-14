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

declare(strict_types=1);

namespace core\output;

/**
 * Unit tests for the mustache_clean_string_helper class.
 *
 * @package   core
 * @category  test
 * @copyright 2021 Shamim Rezaie <shamim@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \core\output\mustache_clean_string_helper
 */
final class mustache_clean_string_helper_test extends \basic_testcase {

    /**
     * Test the get_lang_menu
     *
     * @covers ::cleanstr
     */
    function test_cleanstr(): void {
        $engine = new \Mustache_Engine();
        $context = new \Mustache_Context();
        $lambdahelper = new \Mustache_LambdaHelper($engine, $context);

        $cleanstringhelper = new mustache_clean_string_helper();

        // Simple string.
        $this->assertEquals('Log in', $cleanstringhelper->cleanstr('login, core', $lambdahelper));

        // Quotes in string.
        $this->assertEquals('Today&#039;s logs', $cleanstringhelper->cleanstr('todaylogs, core', $lambdahelper));

        // Quotes in string with parameter.
        $this->assertEquals('After &quot;test&quot;', $cleanstringhelper->cleanstr('movecontentafter, core, test', $lambdahelper));

        // Quotes in parameter.
        $this->assertEquals('Add a new &quot;&#039;&amp;', $cleanstringhelper->cleanstr('addnew, core, "\'&', $lambdahelper));
    }
}
