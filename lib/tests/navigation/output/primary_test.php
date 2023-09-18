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
     * @dataProvider primary_export_provider
     * @param bool $withcustom Setup with custom menu
     * @param bool $withlang Setup with langs
     * @param string $userloggedin The type of user ('admin' or 'guest') if creating setup with logged in user,
     *                             otherwise consider the user as non-logged in
     * @param array $expecteditems An array of nodes expected with content in them.
     */
    public function test_primary_export(bool $withcustom, bool $withlang, string $userloggedin, array $expecteditems) {
        global $PAGE, $CFG;
        if ($withcustom) {
            $CFG->custommenuitems = "Course search|/course/search.php
                Google|https://google.com.au/
                Netflix|https://netflix.com/au";
        }
        if ($userloggedin === 'admin') {
            $this->setAdminUser();
        } else if ($userloggedin === 'guest') {
            $this->setGuestUser();
        } else {
            $this->setUser(0);
        }

        // Mimic multiple langs installed. To trigger responses 'get_list_of_translations'.
        // Note: The text/title of the nodes generated will be 'English(fr), English(de)' but we don't care about this.
        // We are testing whether the nodes gets generated when the lang menu is available.
        if ($withlang) {
            mkdir("$CFG->dataroot/lang/de", 0777, true);
            mkdir("$CFG->dataroot/lang/fr", 0777, true);
            // Ensure the new langs are picked up and not taken from the cache.
            $stringmanager = get_string_manager();
            $stringmanager->reset_caches(true);
        }

        $primary = new primary($PAGE);
        $renderer = $PAGE->get_renderer('core');
        $data = array_filter($primary->export_for_template($renderer));

        // Assert that the number of returned menu items equals the expected result.
        $this->assertCount(count($expecteditems), $data);
        // Assert that returned menu items match the expected items.
        foreach ($data as $menutype => $value) {
            $this->assertTrue(in_array($menutype, $expecteditems));
        }
        // When the user is logged in (excluding guest access), assert that lang menu is included as a part of the
        // user menu when multiple languages are installed.
        if (isloggedin() && !isguestuser()) {
            // Look for a language menu item within the user menu items.
            $usermenulang = array_filter($data['user']['items'], function($usermenuitem) {
                return $usermenuitem->itemtype !== 'divider' && $usermenuitem->title === get_string('language');
            });
            if ($withlang) { // If multiple languages are installed.
                // Assert that the language menu exists within the user menu.
                $this->assertNotEmpty($usermenulang);
            } else { // If the aren't any additional installed languages.
                $this->assertEmpty($usermenulang);
            }
        } else { // Otherwise assert that the user menu does not contain any items.
            $this->assertArrayNotHasKey('items', $data['user']);
        }
    }

    /**
     * Provider for the test_primary_export function.
     *
     * @return array
     */
    public function primary_export_provider(): array {
        return [
            "Export the menu data when: custom menu exists; multiple langs installed; user is not logged in." => [
                true, true, '', ['mobileprimarynav', 'moremenu', 'lang', 'user']
            ],
            "Export the menu data when: custom menu exists; langs not installed; user is not logged in." => [
                true, false, '', ['mobileprimarynav', 'moremenu', 'user']
            ],
            "Export the menu data when: custom menu exists; multiple langs installed; logged in as admin." => [
                true, true, 'admin', ['mobileprimarynav', 'moremenu', 'user']
            ],
            "Export the menu data when: custom menu exists; langs not installed; logged in as admin." => [
                true, false, 'admin', ['mobileprimarynav', 'moremenu', 'user']
            ],
            "Export the menu data when: custom menu exists; multiple langs installed; logged in as guest." => [
                true, true, 'guest', ['mobileprimarynav', 'moremenu', 'lang', 'user']
            ],
            "Export the menu data when: custom menu exists; langs not installed; logged in as guest." => [
                true, false, 'guest', ['mobileprimarynav', 'moremenu', 'user']
            ],
            "Export the menu data when: custom menu does not exist; multiple langs installed; logged in as guest." => [
                false, true, 'guest', ['mobileprimarynav', 'moremenu', 'lang', 'user']
            ],
            "Export the menu data when: custom menu does not exist; multiple langs installed; logged in as admin." => [
                false, true, 'admin', ['mobileprimarynav', 'moremenu', 'user']
            ],
            "Export the menu data when: custom menu does not exist; langs not installed; user is not logged in." => [
                false, false, '', ['mobileprimarynav', 'moremenu', 'user']
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
        $actual = $this->get_custom_menu($config);
        $this->assertEquals($expected, $actual);
    }

    /**
     * Helper method to get the template data for the custommenuitem that is set here via parameter.
     * @param string $config
     * @return array
     * @throws \ReflectionException
     */
    protected function get_custom_menu(string $config): array {
        global $CFG, $PAGE;
        $CFG->custommenuitems = $config;
        $output = new primary($PAGE);
        $method = new ReflectionMethod('core\navigation\output\primary', 'get_custom_menu');
        $method->setAccessible(true);
        $renderer = $PAGE->get_renderer('core');

        // We can't assert the value of each menuitem "moremenuid" property (because it's random).
        $custommenufilter = static function(array $custommenu) use (&$custommenufilter): void {
            foreach ($custommenu as $menuitem) {
                unset($menuitem->moremenuid);
                // Recursively move through child items.
                $custommenufilter($menuitem->children);
            }
        };

        $actual = $method->invoke($output, $renderer);
        $custommenufilter($actual);
        return $actual;
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

    /**
     * Test the merge_primary_and_custom and the eval_is_active method. Merge  primary and custom menu with different
     * page urls and check that the correct nodes are active and open, depending on the data for each menu.
     *
     * @covers \core\navigation\output\primary::merge_primary_and_custom
     * @covers \core\navigation\output\primary::flag_active_nodes
     * @return void
     * @throws \ReflectionException
     * @throws \moodle_exception
     */
    public function test_merge_primary_and_custom() {
        global $PAGE;

        $menu = $this->merge_and_render_menus();

        $this->assertEquals(4, count(\array_keys($menu)));
        $msg = 'No active nodes for page ' . $PAGE->url;
        $this->assertEmpty($this->get_menu_item_names_by_type($menu, 'isactive'), $msg);
        $this->assertEmpty($this->get_menu_item_names_by_type($menu, 'isopen'), str_replace('active', 'open', $msg));

        $msg = 'Active nodes desktop for /course/search.php';
        $menu = $this->merge_and_render_menus('/course/search.php');
        $isactive = $this->get_menu_item_names_by_type($menu, 'isactive');
        $this->assertEquals(['Courses', 'Course search'], $isactive, $msg);
        $this->assertEmpty($this->get_menu_item_names_by_type($menu, 'isopem'), str_replace('Active', 'Open', $msg));

        $msg = 'Active nodes mobile for /course/search.php';
        $menu = $this->merge_and_render_menus('/course/search.php', true);
        $isactive = $this->get_menu_item_names_by_type($menu, 'isactive');
        $this->assertEquals(['Course search'], $isactive, $msg);
        $isopen = $this->get_menu_item_names_by_type($menu, 'isopen');
        $this->assertEquals(['Courses'], $isopen, str_replace('Active', 'Open', $msg));

        $msg = 'Active nodes desktop for /course/search.php?areaids=core_course-course&q=test';
        $menu = $this->merge_and_render_menus('/course/search.php?areaids=core_course-course&q=test');
        $isactive = $this->get_menu_item_names_by_type($menu, 'isactive');
        $this->assertEquals(['Courses', 'Course search'], $isactive, $msg);

        $msg = 'Active nodes desktop for /?theme=boost';
        $menu = $this->merge_and_render_menus('/?theme=boost');
        $isactive = $this->get_menu_item_names_by_type($menu, 'isactive');
        $this->assertEquals(['Theme', 'Boost'], $isactive, $msg);
    }

    /**
     * Internal function to get an array of top menu items from the primary and the custom menu. The latter is defined
     * in this function.
     * @param string|null $url
     * @param bool|null $ismobile
     * @return array
     * @throws \ReflectionException
     * @throws \coding_exception
     */
    protected function merge_and_render_menus(?string $url = null, ?bool $ismobile = false): array {
        global $PAGE, $FULLME;

        if ($url !== null) {
            $PAGE->set_url($url);
            $FULLME = $PAGE->url->out();
        }
        $primary = new primary($PAGE);

        $method = new ReflectionMethod('core\navigation\output\primary', 'get_primary_nav');
        $method->setAccessible(true);
        $dataprimary = $method->invoke($primary);

        // Take this custom menu that would come from the  setting custommenitems.
        $custommenuitems = <<< ENDMENU
        Theme
        -Boost|/?theme=boost
        -Classic|/?theme=classic
        -Purge Cache|/admin/purgecaches.php
        Courses
        -All courses|/course/
        -Course search|/course/search.php
        -###
        -FAQ|https://example.org/faq
        -My Important Course|/course/view.php?id=4
        Mobile app|https://example.org/app|Download our app
        ENDMENU;

        $datacustom = $this->get_custom_menu($custommenuitems);
        $method = new ReflectionMethod('core\navigation\output\primary', 'merge_primary_and_custom');
        $method->setAccessible(true);
        $menucomplete = $method->invoke($primary, $dataprimary, $datacustom, $ismobile);
        return $menucomplete;
    }

    /**
     * Traverse the menu array structure (all nodes recursively) and fetch the node texts from the menu nodes that are
     * active/open (determined via param $nodetype that can be "inactive" or "isopen"). The returned array contains a
     * list of nade names that match this criterion.
     * @param array $menu
     * @param string $nodetype
     * @return array
     */
    protected function get_menu_item_names_by_type(array $menu, string $nodetype): array {
        $matchednodes = [];
        foreach ($menu as $menuitem) {
            // Either the node is an array.
            if (is_array($menuitem)) {
                if ($menuitem[$nodetype] ?? false) {
                    $matchednodes[] = $menuitem['text'];
                }
                // Recursively move through child items.
                if (array_key_exists('children', $menuitem) && count($menuitem['children'])) {
                    $matchednodes = array_merge($matchednodes, $this->get_menu_item_names_by_type($menuitem['children'], $nodetype));
                }
            } else {
                // Otherwise the node is a standard object.
                if (isset($menuitem->{$nodetype}) && $menuitem->{$nodetype} === true) {
                    $matchednodes[] = $menuitem->text;
                }
                // Recursively move through child items.
                if (isset($menuitem->children) && is_array($menuitem->children) && !empty($menuitem->children)) {
                    $matchednodes = array_merge($matchednodes, $this->get_menu_item_names_by_type($menuitem->children, $nodetype));
                }
            }
        }
        return $matchednodes;
    }
}
