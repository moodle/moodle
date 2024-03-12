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

namespace core\output;

use ReflectionMethod;

/**
 * Primary navigation renderable test
 *
 * @package     core
 * @category    output
 * @copyright   2021 onwards Peter Dias
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class language_menu_test extends \advanced_testcase {
    /**
     * Basic setup to make sure the nav objects gets generated without any issues.
     */
    public function setUp(): void {
        global $PAGE;
        $this->resetAfterTest();
        $PAGE->set_url('/');
    }
    /**
     * Test the get_lang_menu
     *
     * @dataProvider get_lang_menu_provider
     * @param bool $withadditionallangs
     * @param string $language
     * @param array $expected
     */
    public function test_get_lang_menu(bool $withadditionallangs, string $language, array $expected) {
        global $CFG, $PAGE;

        // Mimic multiple langs installed. To trigger responses 'get_list_of_translations'.
        // Note: The text/title of the nodes generated will be 'English(fr), English(de)' but we don't care about this.
        // We are testing whether the nodes gets generated when the lang menu is available.
        if ($withadditionallangs) {
            mkdir("$CFG->dataroot/lang/de", 0777, true);
            mkdir("$CFG->dataroot/lang/fr", 0777, true);
            // Ensure the new langs are picked up and not taken from the cache.
            $stringmanager = get_string_manager();
            $stringmanager->reset_caches(true);
        }

        force_current_language($language);

        $output = new language_menu($PAGE);
        $method = new ReflectionMethod('\core\output\language_menu', 'export_for_template');
        $renderer = $PAGE->get_renderer('core');

        $response = $method->invoke($output, $renderer);

        if ($withadditionallangs) { // If there are multiple languages installed.
            // Assert that the title of the language menu matches the expected one.
            $this->assertEquals($expected['title'], $response['title']);
            // Assert that the number of language menu items matches the number of the expected items.
            $this->assertEquals(count($expected['items']), count($response['items']));
            foreach ($expected['items'] as $expecteditem) {
                $lang = $expecteditem['lang'];
                // We need to manually generate the url key and its value in the expected item array as this cannot
                // be done in the data provider due to the change of the state of $PAGE.
                if ($expecteditem['isactive']) {
                    $expecteditem['url'] = new \moodle_url('#');
                } else {
                    $expecteditem['url'] = new \moodle_url($PAGE->url, ['lang' => $lang]);
                    // When the language menu item is not the current language, it will contain the lang attribute.
                    $expecteditem['attributes'][] = [
                        'key' => 'lang',
                        'value' => $lang
                    ];
                }
                // The lang value is only used to generate the url, so this key can be removed.
                unset($expecteditem['lang']);

                // Assert that the given expected item exists in the returned items.
                $this->assertTrue(in_array($expecteditem, $response['items']));
            }
        } else { // No multiple languages.
            $this->assertEquals($expected, $response);
        }
    }

    /**
     * Provider for test_get_lang_menu
     *
     * @return array
     */
    public function get_lang_menu_provider(): array {
        return [
            'Lang menu with only the current language' => [
                false, 'en', []
            ],
            'Lang menu with only multiple languages installed' => [
                true, 'en', [
                    'title' => 'English ‎(en)‎',
                    'items' => [
                        [
                            'title' => 'English ‎(en)‎',
                            'text' => 'English ‎(en)‎',
                            'link' => true,
                            'isactive' => true,
                            'lang' => 'en'
                        ],
                        [
                            'title' => 'English ‎(de)‎',
                            'text' => 'English ‎(de)‎',
                            'link' => true,
                            'isactive' => false,
                            'lang' => 'de'
                        ],

                        [
                            'title' => 'English ‎(fr)‎',
                            'text' => 'English ‎(fr)‎',
                            'link' => true,
                            'isactive' => false,
                            'lang' => 'fr'
                        ],
                    ],
                ],
            ],
            'Lang menu with only multiple languages installed and other than EN set active.' => [
                true, 'de', [
                    'title' => 'English ‎(de)‎',
                    'items' => [
                        [
                            'title' => 'English ‎(en)‎',
                            'text' => 'English ‎(en)‎',
                            'link' => true,
                            'isactive' => false,
                            'lang' => 'en'
                        ],
                        [
                            'title' => 'English ‎(de)‎',
                            'text' => 'English ‎(de)‎',
                            'link' => true,
                            'isactive' => true,
                            'lang' => 'de'
                        ],
                        [
                            'title' => 'English ‎(fr)‎',
                            'text' => 'English ‎(fr)‎',
                            'link' => true,
                            'isactive' => false,
                            'lang' => 'fr'
                        ],
                    ],
                ],
            ],
        ];
    }
}
