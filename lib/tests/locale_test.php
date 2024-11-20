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
 * Tests for \core\locale class.
 *
 * @package    core
 * @category   test
 * @copyright  2018 Université Rennes 2 {@link https://www.univ-rennes2.fr}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \core\locale
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
     * Test \tool_langimport\locale::set_locale() own logic.
     *
     * We have to explicitly test set_locale() own logic and results,
     * that effectively sets the current locale, so we need to restore
     * the original locale after every test (ugly, from a purist unit test
     * point of view, but needed).
     *
     * @dataProvider set_locale_provider
     * @param string $set locale string to be set.
     * @param string $ret expected results returned after setting the locale.
     */
    public function test_set_locale(string $set, string $ret): void {
        // Capture current locale for later restore (funnily, using the set_locale() method itself.
        $originallocale = locale::set_locale(LC_ALL, 0);

        // Assert we get the locale defined as expected.
        $this->assertEquals($ret, locale::set_locale(LC_ALL, $set));
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
    public static function set_locale_provider(): array {
        // Let's list the allowed categories by OS.
        $bsdallowed = ['LC_COLLATE', 'LC_CTYPE', 'LC_MESSAGES', 'LC_MONETARY', 'LC_NUMERIC', 'LC_TIME'];
        $winallowed = ['LC_COLLATE', 'LC_CTYPE', 'LC_MONETARY', 'LC_NUMERIC', 'LC_TIME'];
        $linuxallowed = [
            'LC_COLLATE', 'LC_CTYPE', 'LC_MESSAGES', 'LC_MONETARY', 'LC_NUMERIC', 'LC_TIME',
            'LC_PAPER', 'LC_NAME', 'LC_ADDRESS', 'LC_TELEPHONE', 'LC_MEASUREMENT', 'LC_IDENTIFICATION',
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
