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

// phpcs:disable moodle.PHPUnit.TestCaseNames.Missing
/**
 * Just a wrapper to access protected apis for testing.
 *
 * Note: This is deprecated. Please use Reflection instead.
 *
 * @package    core
 * @subpackage phpunit
 * @copyright  2009 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class test_exernal_api extends \core_external\external_api {
    public static function get_context_wrapper($params) {
        debugging(
            'test_exernal_api::get_context_wrapper() is deprecated. Please use Reflection instead.',
            DEBUG_DEVELOPER
        );
        return self::get_context_from_params($params);
    }
}

/**
 * Test external API functions.
 *
 * @package core
 * @subpackage phpunit
 */
class core_externallib_test extends \advanced_testcase {
    /**
     * Test the get_context_wrapper helper.
     *
     * @covers \core\test_exernal_api::get_context_wrapper
     */
    public function test_get_context_wrapper(): void {
        $this->assertEquals(
            \context_system::instance(),
            \core\test_exernal_api::get_context_wrapper(['contextid' => \context_system::instance()->id])
        );
        $this->assertDebuggingCalled(
            'test_exernal_api::get_context_wrapper() is deprecated. Please use Reflection instead.'
        );
    }
}
