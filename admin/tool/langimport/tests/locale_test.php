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
 * Tests for \tool_langimport\locale class.
 *
 * @package    tool_langimport
 * @copyright  2018 Université Rennes 2 {@link https://www.univ-rennes2.fr}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_langimport;

/**
 * Tests for \tool_langimport\locale class.
 *
 * @package    tool_langimport
 * @category   test
 * @copyright  2018 Université Rennes 2 {@link https://www.univ-rennes2.fr}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class locale_test extends \advanced_testcase {
    /**
     * Test that \tool_langimport\locale::check_locale_availability() works as expected.
     *
     * @return void
     */
    public function test_check_locale_availability() {
        // Create a mock of set_locale() method to simulate :
        // - first setlocale() call which backup current locale
        // - second setlocale() call which try to set new 'es' locale
        // - third setlocale() call which restore locale.
        $mock = $this->getMockBuilder(\tool_langimport\locale::class)
            ->onlyMethods(['set_locale'])
            ->getMock();
        $mock->method('set_locale')->will($this->onConsecutiveCalls('en', 'es', 'en'));

        // Test what happen when locale is available on system.
        $result = $mock->check_locale_availability('en');
        $this->assertTrue($result);

        // Create a mock of set_locale() method to simulate :
        // - first setlocale() call which backup current locale
        // - second setlocale() call which fail to set new locale
        // - third setlocale() call which restore locale.
        $mock = $this->getMockBuilder(\tool_langimport\locale::class)
            ->onlyMethods(['set_locale'])
            ->getMock();
        $mock->method('set_locale')->will($this->onConsecutiveCalls('en', false, 'en'));

        // Test what happen when locale is not available on system.
        $result = $mock->check_locale_availability('en');
        $this->assertFalse($result);

        // Test an invalid parameter.
        $locale = new \tool_langimport\locale();
        $this->expectException(\coding_exception::class);
        $locale->check_locale_availability('');
    }
}
