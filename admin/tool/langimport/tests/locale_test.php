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
 * @coversDefaultClass \tool_langimport\locale
 * @copyright  2018 Université Rennes 2 {@link https://www.univ-rennes2.fr}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class locale_test extends \advanced_testcase {
    /**
     * Test that \tool_langimport\locale::check_locale_availability() works as expected.
     *
     * @covers ::check_locale_availability
     * @return void
     */
    public function test_check_locale_availability() {
        // Create a mock of set_locale() method to simulate :
        // - first setlocale() call which backup current locale
        // - second setlocale() call which try to set new 'es' locale
        // - third setlocale() call which restore locale.
        $mock = $this->getMockBuilder(locale::class)
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
        $mock = $this->getMockBuilder(locale::class)
            ->onlyMethods(['set_locale'])
            ->getMock();
        $mock->method('set_locale')->will($this->onConsecutiveCalls('en', false, 'en'));

        // Test what happen when locale is not available on system.
        $result = $mock->check_locale_availability('en');
        $this->assertFalse($result);

        // Test an invalid parameter.
        $locale = new locale();
        $this->expectException(\coding_exception::class);
        $locale->check_locale_availability('');
    }

    /**
     * Test \tool_langimport\locale::set_locale() own logic.
     *
     * We have to explicitly test set_locale() own logic and results,
     * that effectively sets the current locale, so we need to restore
     * the original locale after every test (ugly, from a purist unit test
     * point of view, but needed).
     *
     * @dataProvider set_locale_provider
     * @covers ::set_locale
     *
     * @param string $set locale string to be set.
     * @param string $ret expected results returned after setting the locale.
     */
    public function test_set_locale(string $set, string $ret) {
        // Make set_locale() public.
        $loc = new locale();
        $rc = new \ReflectionClass(locale::class);
        $rm = $rc->getMethod('set_locale');

        // Capture current locale for later restore (funnily, using the set_locale() method itself.
        $originallocale = $rm->invokeArgs($loc, [LC_ALL, 0]);

        // Assert we get the locale defined as expected.
        $this->assertEquals($ret, $rm->invokeArgs($loc, [LC_ALL, $set]));

        // We have finished, restore the original locale, so this doesn't affect other tests at distance.
        // (again, funnily, using the very same set_locale() method).
        $rm->invokeArgs($loc, [LC_ALL, $originallocale]);

    }

    /**
     * Data provider for test_set_locale().
     *
     * Provides a locale to be set (as 'set') and a expected return value (as 'ret'). Note that
     * some of the locales are OS dependent, so only the ones matching the OS will be provided.
     *
     * We make extensive use of the en_AU.UTF-8/English_Australia.1252 locale that is mandatory to
     * be installed in any system running PHPUnit tests.
     */
    public function set_locale_provider(): array {
        // Let's list the allowed categories by OS.
        $bsdallowed = ['LC_COLLATE', 'LC_CTYPE', 'LC_MESSAGES', 'LC_MONETARY', 'LC_NUMERIC', 'LC_TIME'];
        $winallowed = ['LC_COLLATE', 'LC_CTYPE', 'LC_MONETARY', 'LC_NUMERIC', 'LC_TIME'];
        $linuxallowed = [
            'LC_COLLATE', 'LC_CTYPE', 'LC_MESSAGES', 'LC_MONETARY', 'LC_NUMERIC', 'LC_TIME',
            'LC_PAPER', 'LC_NAME', 'LC_ADDRESS', 'LC_TELEPHONE', 'LC_MEASUREMENT', 'LC_IDENTIFICATION'
        ];

        // The base locale name is also OS dependent.
        $baselocale = get_string('locale', 'langconfig');
        if (PHP_OS_FAMILY === 'Windows') {
            $baselocale = get_string('localewin', 'langconfig');
        }

        // Here we'll go accumulating cases to be provided.
        $cases = [];

        // First, the simplest case, just pass a locale name, without categories.
        $cases['rawlocale'] = [
            'set' => $baselocale,
            'ret' => $baselocale,
        ];

        // Now, let's fill ALL LC categories, we should get back the locale name if all them are set with same value.
        // Note that this case is the one that, under Linux only, covers the changes performed to the set_locale() method.
        // Pick the correct categories depending on the OS.
        $oscategories = $bsdallowed; // Default to BSD/Dawrwin ones because they are the standard 6 supported by PHP.
        if (PHP_OS_FAMILY === 'Windows') {
            $oscategories = $winallowed;
        } else if (PHP_OS_FAMILY === 'Linux') {
            $oscategories = $linuxallowed;
        }

        $localestr = '';
        foreach ($oscategories as $category) {
            // Format is different by OS too, so let build the string conditionally.
            if (PHP_OS_FAMILY === 'BSD' || PHP_OS_FAMILY === 'Darwin') {
                // BSD uses slashes (/) separated list of the 6 values in exact order.
                $localestr .= '/' . $baselocale;
            } else {
                // Linux/Windows use semicolon (;) separated list of category=value pairs.
                $localestr .= ';' . $category . '=' . $baselocale;
            }
        }
        $cases['allcategories'] = [
            'set' => trim($localestr, ';/'),
            'ret' => $baselocale,
        ];

        // Return all the built cases.
        return $cases;
    }
}
