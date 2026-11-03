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

namespace tool_mobile\output;

/**
 * Tests for the mobile subscription page output.
 *
 * @package   tool_mobile
 * @copyright 2026 Moodle Pty Ltd
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(subscription::class)]
final class subscription_test extends \advanced_testcase {
    /**
     * Test splitting a setting into lines on any line ending.
     *
     * @param string $config raw setting value
     * @param string[] $expected expected lines
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('get_lines_provider')]
    public function test_get_lines(string $config, array $expected): void {
        $method = new \ReflectionMethod(subscription::class, 'get_lines');
        $this->assertSame($expected, $method->invoke(null, $config));
    }

    /**
     * Data provider for test_get_lines.
     *
     * @return \Generator
     */
    public static function get_lines_provider(): \Generator {
        yield 'empty setting' => ['', ['']];
        yield 'mixed line endings' => [
            "first\r\nsecond\rthird\nfourth",
            ['first', 'second', 'third', 'fourth'],
        ];
        yield 'values are not trimmed' => [" first \n", [' first ', '']];
    }

    /**
     * Test counting the custom menu items as the app does.
     *
     * @param string $config raw setting value
     * @param int $expected expected count
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('count_custom_menu_items_provider')]
    public function test_count_custom_menu_items(string $config, int $expected): void {
        $method = new \ReflectionMethod(subscription::class, 'count_custom_menu_items');
        $this->assertSame($expected, $method->invoke(null, $config));
    }

    /**
     * Data provider for test_count_custom_menu_items.
     *
     * @return \Generator
     */
    public static function count_custom_menu_items_provider(): \Generator {
        yield 'empty setting' => ['', 0];
        yield 'label, url and type are required' => [
            implode("\n", [
                'Grades|https://example.com/grades',
                '|https://example.com/a|browser',
                'B||browser',
                'C|https://example.com/c| ',
            ]),
            0,
        ];
        yield 'blank lines between items' => [
            implode("\n", [
                'Grades|https://example.com/grades|embedded',
                '',
                '  ',
                'Help|https://example.com/help|browser',
                '',
            ]),
            2,
        ];
        yield 'type is not validated' => ['Grades|https://example.com/grades|whatever', 1];
        yield 'same url and type in several languages count once' => [
            implode("\n", [
                'My item|https://moodle.org|browser|en',
                'Mi item|https://moodle.org|browser|es',
                'El meu item|https://moodle.org|browser|ca',
            ]),
            1,
        ];
        yield 'same url with a different type counts twice' => [
            implode("\n", [
                'My item|https://moodle.org|browser|en',
                'My item|https://moodle.org|inappbrowser|en',
            ]),
            2,
        ];
        yield 'values are trimmed and type is case insensitive' => [
            implode("\n", [
                'My item | https://moodle.org | Browser |en',
                'Other label|https://moodle.org|browser',
            ]),
            1,
        ];
        yield 'mixed line endings' => [
            "A|https://example.com/a|browser\r\nB|https://example.com/b|browser\rC|https://example.com/c|browser\n",
            3,
        ];
        yield 'generic items plus the language with most language specific items' => [
            implode("\n", [
                'G1|https://example.com/g1|browser',
                'G2|https://example.com/g2|browser|en',
                'G3|https://example.com/g3|browser|es',
                'E1|https://example.com/e1|browser|en_only',
                'E2|https://example.com/e2|browser|en_only',
                'S1|https://example.com/s1|browser|es_only',
                'S2|https://example.com/s2|browser|es_only',
                'S3|https://example.com/s3|browser|es_only',
                'S4|https://example.com/s4|browser|es_only',
            ]),
            7,
        ];
        yield 'item with a language specific and a generic label is shown in every language' => [
            implode("\n", [
                'A|https://example.com/a|browser|en_only',
                'A|https://example.com/a|browser|es',
            ]),
            1,
        ];
        yield 'item with labels for several languages counts in each of them' => [
            implode("\n", [
                'A|https://example.com/a|browser|en_only',
                'A|https://example.com/a|browser|es_only',
                'B|https://example.com/b|browser|es_only',
            ]),
            2,
        ];
        yield 'language is trimmed' => [
            implode("\n", [
                'G|https://example.com/g|browser',
                'E|https://example.com/e|browser| en_only ',
                'S|https://example.com/s|browser|es_only',
            ]),
            2,
        ];
        yield 'language codes are normalised' => [
            implode("\n", [
                'A|https://example.com/a|browser|pt_br_only',
                'B|https://example.com/b|browser|pt-BR_only',
            ]),
            2,
        ];
    }

    /**
     * Test counting the custom language strings as the app does.
     *
     * @param string $config raw setting value
     * @param int $expected expected count
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('count_custom_lang_strings_provider')]
    public function test_count_custom_lang_strings(string $config, int $expected): void {
        $method = new \ReflectionMethod(subscription::class, 'count_custom_lang_strings');
        $this->assertSame($expected, $method->invoke(null, $config));
    }

    /**
     * Data provider for test_count_custom_lang_strings.
     *
     * @return \Generator
     */
    public static function count_custom_lang_strings_provider(): \Generator {
        yield 'empty setting' => ['', 0];
        yield 'highest count in a single language' => [
            implode("\n", [
                'a|A|en',
                'b|B|en',
                'c|C|en',
                'd|D|en',
                'e|E|en',
                'a|A|es',
                'b|B|es',
                'c|C|es',
                'd|D|es',
                'e|E|es',
                'f|F|es',
                'g|G|es',
            ]),
            7,
        ];
        yield 'duplicates are counted' => [
            implode("\n", [
                'core.user.support|Help|en',
                'core.user.support|Help|en',
            ]),
            2,
        ];
        yield 'lines with less than 3 values are ignored' => [
            implode("\n", [
                '',
                '   ',
                'core.login|Log in',
                'just text',
                '|',
            ]),
            0,
        ];
        yield 'empty values are counted' => ["||\n||", 2];
        yield 'language codes are normalised' => [
            implode("\n", [
                'a|A|pt_br',
                'b|B|pt-br',
            ]),
            2,
        ];
        yield 'language codes are trimmed but not lowercased' => [
            implode("\n", [
                'a|A|en',
                'b|B| en ',
                'c|C|EN',
            ]),
            2,
        ];
        yield 'mixed line endings' => ["a|A|en\r\nb|B|en\rc|C|en\n", 3];
    }

    /**
     * Export the subscription page and return the status calculated for a feature.
     *
     * @param string $featurename feature name as returned by the Apps Portal
     * @return int
     */
    private function get_feature_status(string $featurename): int {
        global $PAGE;

        $subscriptiondata = [
            'subscription' => [
                'name' => 'Free',
                'plan' => 'free',
                'features' => [
                    ['name' => 'custommenuitems', 'limit' => 10, 'limitstr' => '10', 'enabled' => true],
                    ['name' => 'customlanguagestrings', 'limit' => 10, 'limitstr' => '10', 'enabled' => true],
                ],
            ],
            'availableplans' => [],
        ];

        $data = (new subscription($subscriptiondata))->export_for_template($PAGE->get_renderer('tool_mobile'));
        foreach ($data['subscription']['features'] as $feature) {
            if ($feature['name'] === $featurename) {
                return $feature['status'];
            }
        }
        $this->fail("Feature {$featurename} not found in exported data");
    }

    /**
     * Test the custom menu items count shown on the subscription page.
     *
     * @param string $menuitems tool_mobile/custommenuitems value
     * @param string $usermenuitems tool_mobile/customusermenuitems value
     * @param int $expected expected status
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('custom_menu_items_provider')]
    public function test_export_custom_menu_items_count(string $menuitems, string $usermenuitems, int $expected): void {
        $this->resetAfterTest();
        $this->setAdminUser();
        set_config('custommenuitems', $menuitems, 'tool_mobile');
        set_config('customusermenuitems', $usermenuitems, 'tool_mobile');

        $this->assertSame($expected, $this->get_feature_status('custommenuitems'));
    }

    /**
     * Data provider for test_export_custom_menu_items_count.
     *
     * @return \Generator
     */
    public static function custom_menu_items_provider(): \Generator {
        yield 'empty settings' => ['', '', 0];
        yield 'the same item in both lists counts twice' => [
            'Moodle|https://moodle.org|browser',
            'Moodle|https://moodle.org|browser',
            2,
        ];
        yield 'language specific items are counted independently in each list' => [
            implode("\n", [
                'G|https://example.com/g|browser',
                'E|https://example.com/e|browser|en_only',
            ]),
            implode("\n", [
                'S1|https://example.com/s1|browser|es_only',
                'S2|https://example.com/s2|browser|es_only',
            ]),
            4,
        ];
    }

    /**
     * Test the custom language strings count shown on the subscription page.
     *
     * @param string $langstrings tool_mobile/customlangstrings value
     * @param int $expected expected status
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('custom_lang_strings_provider')]
    public function test_export_custom_lang_strings_count(string $langstrings, int $expected): void {
        $this->resetAfterTest();
        $this->setAdminUser();
        set_config('customlangstrings', $langstrings, 'tool_mobile');

        $this->assertSame($expected, $this->get_feature_status('customlanguagestrings'));
    }

    /**
     * Data provider for test_export_custom_lang_strings_count.
     *
     * @return \Generator
     */
    public static function custom_lang_strings_provider(): \Generator {
        yield 'empty setting' => ['', 0];
        yield 'strings in several languages count the highest language' => [
            implode("\n", [
                'core.login|Log in|en',
                'core.login|Entrar|es',
                'core.logout|Salir|es',
            ]),
            2,
        ];
    }
}
