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

namespace core\navigation\output;

use ReflectionMethod;

/**
 * Primary navigation renderable test
 *
 * @package     core
 * @category    navigation
 * @copyright   2021 onwards Peter Dias
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class primary_test extends \advanced_testcase {
    /**
     * Basic setup to make sure the nav objects gets generated without any issues.
     */
    public function setUp(): void {
        global $PAGE;
        $this->resetAfterTest();
        $pagecourse = $this->getDataGenerator()->create_course();
        $assign = $this->getDataGenerator()->create_module('assign', ['course' => $pagecourse->id]);
        $cm = get_coursemodule_from_id('assign', $assign->cmid);
        $contextrecord = \context_module::instance($cm->id);
        $pageurl = new \moodle_url('/mod/assign/view.php', ['id' => $cm->instance]);
        $PAGE->set_cm($cm);
        $PAGE->set_url($pageurl);
        $PAGE->set_course($pagecourse);
        $PAGE->set_context($contextrecord);
    }

    /**
     * Test the primary export to confirm we are getting the nodes
     *
     * @dataProvider test_primary_export_provider
     * @param bool $withcustom Setup with custom menu
     * @param bool $withlang Setup with langs
     * @param array $expecteditems An array of nodes expected with content in them.
     */
    public function test_primary_export(bool $withcustom, bool $withlang, array $expecteditems) {
        global $PAGE, $CFG;
        if ($withcustom) {
            $CFG->custommenuitems = "Course search|/course/search.php
                Google|https://google.com.au/
                Netflix|https://netflix.com/au";
        }
        $this->setAdminUser();

        // Mimic multiple langs installed. To trigger responses 'get_list_of_translations'.
        // Note: The text/title of the nodes generated will be 'English(fr), English(de)' but we don't care about this.
        // We are testing whether the nodes gets generated when the lang menu is available.
        if ($withlang) {
            mkdir("$CFG->dataroot/lang/de", 0777, true);
            mkdir("$CFG->dataroot/lang/fr", 0777, true);
        }

        $primary = new primary($PAGE);
        $renderer = $PAGE->get_renderer('core');
        $data = $primary->export_for_template($renderer);
        foreach ($data as $menutype => $value) {
            if ($value) {
                $this->assertTrue(in_array($menutype, $expecteditems));
            }
        }
    }

    /**
     * Provider for the test_primary_export function.
     *
     * @return array
     */
    public function test_primary_export_provider(): array {
        return [
            "Export the menu data with custom and lang menu" => [
                true, true, ['primary', 'custom', 'lang', 'user']
            ],
            "Export the menu data with custom menu" => [
                true, false, ['primary', 'custom', 'user']
            ],
            "Export the menu data with lang menu" => [
                false, true, ['primary', 'lang', 'user']
            ],
            "Export the menu data without the custom and lang menu" => [
                false, false, ['primary', 'user']
            ],
        ];
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

        force_current_language($language);

        // Mimic multiple langs installed. To trigger responses 'get_list_of_translations'.
        // Note: The text/title of the nodes generated will be 'English(fr), English(de)' but we don't care about this.
        // We are testing whether the nodes gets generated when the lang menu is available.
        if ($withadditionallangs) {
            mkdir("$CFG->dataroot/lang/de", 0777, true);
            mkdir("$CFG->dataroot/lang/fr", 0777, true);
        }

        $output = new primary($PAGE);
        $method = new ReflectionMethod('core\navigation\output\primary', 'get_lang_menu');
        $method->setAccessible(true);
        $renderer = $PAGE->get_renderer('core');

        $response = $method->invoke($output, $renderer);
        if (!$withadditionallangs) {
            $this->assertEquals($expected, $response);
        }

        $expectedurls = array_map(function ($value) use ($PAGE) {
            $url = new \moodle_url($PAGE->url, ['lang' => $value]);
            return $url->out();
        }, array_keys($expected));
        $expectedurls[] = "#";

        // Make sure the urls match up.
        foreach ($response as $lang) {
            $this->assertTrue(in_array($lang['url']->out(), $expectedurls));
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
                    'de' => 'English ‎(de)‎',
                    'fr' => 'English ‎(fr)‎',
                ]
            ],
            'Lang menu with only multiple languages installed and other than EN set active.' => [
                true, 'de', [
                    'en' => 'English ‎(en)‎',
                    'fr' => 'English ‎(fr)‎',
                ]
            ],
        ];
    }

    /**
     * Test the custom menu getter to confirm the nodes gets generated and are returned correctly.
     *
     * @dataProvider custom_menu_provider
     * @param string $config
     * @param array $expected
     */
    public function test_get_custom_menu(string $config, array $expected) {
        global $CFG, $PAGE;
        $CFG->custommenuitems = $config;
        $output = new primary($PAGE);
        $method = new ReflectionMethod('core\navigation\output\primary', 'get_custom_menu');
        $method->setAccessible(true);
        $renderer = $PAGE->get_renderer('core');
        $this->assertEquals($expected, $method->invoke($output, $renderer));
    }

    /**
     * Provider for test_get_custom_menu
     *
     * @return array
     */
    public function custom_menu_provider(): array {
        return [
            'Simple custom menu' => [
                "Course search|/course/search.php
                Google|https://google.com.au/
                Netflix|https://netflix.com/au", [
                    (object) [
                        'text' => 'Course search',
                        'url' => 'https://www.example.com/moodle/course/search.php',
                        'title' => '',
                        'sort' => 1,
                        'children' => [],
                        'haschildren' => false,
                    ],
                    (object) [
                        'text' => 'Google',
                        'url' => 'https://google.com.au/',
                        'title' => '',
                        'sort' => 2,
                        'children' => [],
                        'haschildren' => false,
                    ],
                    (object) [
                        'text' => 'Netflix',
                        'url' => 'https://netflix.com/au',
                        'title' => '',
                        'sort' => 3,
                        'children' => [],
                        'haschildren' => false,
                    ],
                ]
            ],
            'Complex, nested custom menu' => [
                "Moodle community|http://moodle.org
                -Moodle free support|http://moodle.org/support
                -Moodle development|http://moodle.org/development
                --Moodle Tracker|http://tracker.moodle.org
                --Moodle Docs|https://docs.moodle.org
                -Moodle News|http://moodle.org/news
                Moodle company
                -Moodle commercial hosting|http://moodle.com/hosting
                -Moodle commercial support|http://moodle.com/support", [
                    (object) [
                        'text' => 'Moodle community',
                        'url' => 'http://moodle.org',
                        'title' => '',
                        'sort' => 1,
                        'children' => [
                            (object) [
                                'text' => 'Moodle free support',
                                'url' => 'http://moodle.org/support',
                                'title' => '',
                                'sort' => 2,
                                'children' => [],
                                'haschildren' => false,
                            ],
                            (object) [
                                'text' => 'Moodle development',
                                'url' => 'http://moodle.org/development',
                                'title' => '',
                                'sort' => 3,
                                'children' => [
                                    (object) [
                                        'text' => 'Moodle Tracker',
                                        'url' => 'http://tracker.moodle.org',
                                        'title' => '',
                                        'sort' => 4,
                                        'children' => [],
                                        'haschildren' => false,
                                    ],
                                    (object) [
                                        'text' => 'Moodle Docs',
                                        'url' => 'https://docs.moodle.org',
                                        'title' => '',
                                        'sort' => 5,
                                        'children' => [],
                                        'haschildren' => false,
                                    ],
                                ],
                                'haschildren' => true,
                            ],
                            (object) [
                                'text' => 'Moodle News',
                                'url' => 'http://moodle.org/news',
                                'title' => '',
                                'sort' => 6,
                                'children' => [],
                                'haschildren' => false,
                            ],
                        ],
                        'haschildren' => true,
                    ],
                    (object) [
                        'text' => 'Moodle company',
                        'url' => null,
                        'title' => '',
                        'sort' => 7,
                        'children' => [
                            (object) [
                                'text' => 'Moodle commercial hosting',
                                'url' => 'http://moodle.com/hosting',
                                'title' => '',
                                'sort' => 8,
                                'children' => [],
                                'haschildren' => false,
                            ],
                            (object) [
                                'text' => 'Moodle commercial support',
                                'url' => 'http://moodle.com/support',
                                'title' => '',
                                'sort' => 9,
                                'children' => [],
                                'haschildren' => false,
                            ],
                        ],
                        'haschildren' => true,
                    ],
                ]
            ]
        ];
    }
}
