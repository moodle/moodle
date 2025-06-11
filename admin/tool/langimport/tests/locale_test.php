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

namespace tool_langimport;

/**
 * Tests for \tool_langimport\locale class.
 *
 * @package    tool_langimport
 * @category   test
 * @covers \tool_langimport\locale
 * @copyright  2018 Université Rennes 2 {@link https://www.univ-rennes2.fr}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class locale_test extends \advanced_testcase {
    /** @var string Locale */
    protected string $locale;

    #[\Override]
    public function setUp(): void {
        parent::setUp();
        $this->locale = \core\locale::get_locale();
    }

    #[\Override]
    public function tearDown(): void {
        parent::tearDown();
        \core\locale::set_locale(LC_ALL, $this->locale);
    }

    /**
     * Test that \tool_langimport\locale::check_locale_availability() works as expected.
     */
    public function test_check_locale_availability(): void {
        // Create a mock of set_locale() method to simulate:
        // - get_locale() call which backup current locale
        // - first set_locale() call which try to set new 'es' locale
        // - second set_locale() call which restore locale.
        $mock = $this->getMockBuilder(locale::class)
            ->onlyMethods([
                'get_locale',
                'set_locale',
            ])
            ->getMock();
        $mock->method('get_locale')->will($this->returnValue('en'));
        $setinvocations = $this->exactly(2);
        $mock
            ->expects($setinvocations)
            ->method('set_locale')->willReturnCallback(fn () => match (self::getInvocationCount($setinvocations)) {
                1 => 'es',
                2 => 'en',
            });

        // Test what happen when locale is available on system.
        $result = $mock->check_locale_availability('en');
        $this->assertTrue($result);

        // Create a mock of set_locale() method to simulate:
        // - get_locale() call which backup current locale
        // - first set_locale() call which fail to set new locale
        // - second set_locale() call which restore locale.
        $mock = $this->getMockBuilder(locale::class)
            ->onlyMethods([
                'get_locale',
                'set_locale',
            ])
            ->getMock();
        $mock->expects($this->exactly(1))->method('get_locale')->will($this->returnValue('en'));
        $setinvocations = $this->exactly(2);
        $mock
            ->expects($setinvocations)
            ->method('set_locale')->willReturnCallback(fn () => match (self::getInvocationCount($setinvocations)) {
                1 => false,
                2 => 'en',
            });

        // Test what happen when locale is not available on system.
        $result = $mock->check_locale_availability('en');
        $this->assertFalse($result);

        // Test an invalid parameter.
        $locale = new locale();
        $this->expectException(\coding_exception::class);
        $locale->check_locale_availability('');
    }
}
