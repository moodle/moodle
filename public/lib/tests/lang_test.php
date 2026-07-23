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
 * Unit tests for \core\lang::match_lang_from_browser_header().
 *
 * @package   core
 * @category  test
 * @copyright 2026 Brendan Heywood <brendan@catalyst-au.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/fixtures/testable_string_manager_for_current_language_tests.php');

/**
 * Unit tests for \core\lang::match_lang_from_browser_header().
 *
 * @copyright 2026 Brendan Heywood <brendan@catalyst-au.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers    \core\lang::match_lang_from_browser_header
 */
final class lang_test extends \advanced_testcase {
    protected function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
    }

    protected function tearDown(): void {
        testable_string_manager_for_current_language_tests::reset_installed_languages_override();
        parent::tearDown();
    }

    /**
     * Data provider for {@see test_match_lang_from_browser_header}.
     *
     * Each case is: [ header value (null = absent), installed langs map, expected result ]
     *
     * @return array
     */
    public static function match_lang_from_browser_provider(): array {
        return [
            'no header returns null' => [
                null,
                ['en' => 'English'],
                null,
            ],
            'simple exact match' => [
                'fr',
                ['en' => 'English', 'fr' => 'French'],
                'fr',
            ],
            'exact match with region, hyphen normalised to underscore' => [
                'en-AU',
                ['en' => 'English', 'en_au' => 'English (Australian)'],
                'en_au',
            ],
            'first listed wins when no q-values' => [
                'fr,de,en',
                ['en' => 'English', 'de' => 'German', 'fr' => 'French'],
                'fr',
            ],
            'higher explicit q-value wins' => [
                'fr;q=0.5,de;q=0.9',
                ['en' => 'English', 'fr' => 'French', 'de' => 'German'],
                'de',
            ],
            'implicit q=1.0 beats explicit lower q-value' => [
                'fr,en;q=0.9',
                ['en' => 'English', 'fr' => 'French'],
                'fr',
            ],
            'falls back to base language when region variant not installed' => [
                'en-US',
                ['en' => 'English'],
                'en',
            ],
            'falls back to same-base family variant when base not installed' => [
                'en-CA',
                ['en_us' => 'English (US)'],
                'en_us',
            ],
            'no match returns null' => [
                'zh-TW',
                ['en' => 'English', 'fr' => 'French'],
                null,
            ],
            // BCP 47 three-part tag (script + region): zh-Hans-CN should exactly match zh_hans_cn.
            'zh-Hans-CN exact match on zh_hans_cn' => [
                'zh-Hans-CN',
                ['zh_hans_cn' => 'Chinese Simplified (China)'],
                'zh_hans_cn',
            ],
            // Lang zh-Hans-CN with only the bare zh installed: base-language pass should return zh.
            'zh-Hans-CN falls back to base zh' => [
                'zh-Hans-CN',
                ['en' => 'English', 'zh' => 'Chinese Simplified'],
                'zh',
            ],
            // Lang zh-Hans-CN with only zh_cn installed: base is zh for both, so family fallback matches.
            'zh-Hans-CN falls back to zh_cn via same-base family match' => [
                'zh-Hans-CN',
                ['en' => 'English', 'zh_cn' => 'Chinese Simplified (China)'],
                'zh_cn',
            ],
            // Equal q-values: first entry in header order must win (RFC 7231 tie-break by sequence).
            'equal explicit q-values preserve header order' => [
                'en_gb;q=0.9,en_us;q=0.9',
                ['en_gb' => 'English (UK)', 'en_us' => 'English (US)'],
                'en_gb',
            ],
            // Explicit q=1 must not overwrite an implicit q=1 that appears earlier in the header.
            'explicit q=1 does not overwrite earlier implicit q=1' => [
                'en,fr;q=1',
                ['en' => 'English', 'fr' => 'French'],
                'en',
            ],
            // A higher-priority language with only a family match must beat a lower-priority
            // language with an exact match. Per-language relaxation must be applied before
            // moving to the next candidate, not as three global sweeps.
            'higher-priority family match beats lower-priority exact match' => [
                'en-AU,fr;q=0.5',
                ['en_us' => 'English (US)', 'fr' => 'French'],
                'en_us',
            ],
        ];
    }

    /**
     * Test \core\lang::match_lang_from_browser_header() across all scenarios.
     *
     * @dataProvider match_lang_from_browser_provider
     * @param string|null $header    value of the Accept-Language header, or null to simulate absence
     * @param array       $installed map of lang code => human name to pretend are installed
     * @param string|null $expected  expected return value
     */
    public function test_match_lang_from_browser_header(?string $header, array $installed, ?string $expected): void {
        testable_string_manager_for_current_language_tests::set_fake_list_of_installed_languages($installed);
        $this->assertSame($expected, lang::match_lang_from_browser_header($header));
    }
}
