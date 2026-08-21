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
final class primary_test extends \advanced_testcase {
    /**
     * Basic setup to make sure the nav objects gets generated without any issues.
     */
    public function setUp(): void {
        global $PAGE;
        parent::setUp();
        $this->resetAfterTest();
        set_config('enablemyhome', 1);
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
    public function test_primary_export(bool $withcustom, bool $withlang, string $userloggedin, array $expecteditems): void {
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

        // Every provider case expects a more menu, so assert the React props unconditionally.
        $this->assertArrayHasKey('moremenu', $data);
        $this->assertArrayHasKey('reactprops', $data['moremenu']);
        $this->assertIsString($data['moremenu']['reactprops']);
        $reactprops = json_decode($data['moremenu']['reactprops'], true);
        $this->assertIsArray($reactprops);
        $this->assertNotEmpty($reactprops['items']);
        $this->assertSame(get_string('moremenu'), $reactprops['morelabel']);
        // These two must be taken from the more_menu export, so that the React markup and the
        // NonJS fallback markup can never disagree about them.
        $this->assertSame($data['moremenu']['navbarstyle'], $reactprops['navbarstyle']);
        $this->assertSame($data['moremenu']['istablist'], $reactprops['istablist']);
        $this->assertSame('primarynav-measured', $reactprops['measuredclass']);

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
    public static function primary_export_provider(): array {
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
     * Helper to call one of the protected React export methods on a primary renderable.
     *
     * @param string $method The method name.
     * @param array $args The arguments to pass.
     * @return mixed
     */
    protected function call_react_export(string $method, array $args) {
        global $PAGE;

        $reflection = new ReflectionMethod(primary::class, $method);

        return $reflection->invokeArgs(new primary($PAGE), $args);
    }

    /**
     * A primary nav node's url object and key should survive into the React props.
     *
     * @covers \core\navigation\output\primary::export_node_for_react
     * @covers \core\navigation\output\primary::resolve_node_href
     * @covers \core\navigation\output\primary::resolve_node_key
     */
    public function test_export_node_for_react_flattens_a_primary_nav_node(): void {
        $url = new \core\url('/course/index.php', ['id' => 7]);
        $node = $this->call_react_export('export_node_for_react', [
            [
                'key' => 'courses',
                'sort' => 'courses',
                'text' => 'Courses',
                'title' => 'Courses',
                'url' => $url,
                'isactive' => true,
                'haschildren' => 0,
                'children' => [],
            ],
            0,
        ]);

        $this->assertSame('courses', $node['key']);
        $this->assertSame('Courses', $node['text']);
        $this->assertSame($url->out(false), $node['href']);
        $this->assertTrue($node['active']);
        $this->assertFalse($node['divider']);
        $this->assertFalse($node['showchildreninsubmenu']);
        $this->assertSame([], $node['children']);
        // A title identical to the label adds nothing, so it is not emitted.
        $this->assertNull($node['title']);
        // Primary nav nodes are never action links, but the keys are still present so that the
        // payload matches the component's NavNode shape.
        $this->assertNull($node['id']);
        $this->assertSame([], $node['attributes']);
        $this->assertSame([], $node['actions']);
    }

    /**
     * A node with children should be flagged to render them in a submenu.
     *
     * @covers \core\navigation\output\primary::export_node_for_react
     */
    public function test_export_node_for_react_flags_nodes_with_children(): void {
        $node = $this->call_react_export('export_node_for_react', [
            [
                'text' => 'Parent',
                'url' => 'https://example.com/parent.php',
                'haschildren' => 1,
                'children' => [
                    ['text' => 'Child', 'url' => 'https://example.com/child.php', 'sort' => 1],
                ],
            ],
            3,
        ]);

        $this->assertTrue($node['showchildreninsubmenu']);
        $this->assertCount(1, $node['children']);
        $this->assertSame('Child', $node['children'][0]['text']);
        $this->assertSame('https://example.com/child.php', $node['children'][0]['href']);
        // Neither key nor sort is set on the parent, so it falls back to its sibling position.
        $this->assertSame('node-3', $node['key']);
    }

    /**
     * Dividers are a dropdown-only concept: kept for children, dropped at the top level.
     *
     * @covers \core\navigation\output\primary::export_nodes_for_react
     */
    public function test_export_nodes_for_react_only_keeps_dividers_in_submenus(): void {
        $nodes = [
            ['text' => 'One', 'url' => 'https://example.com/one.php'],
            ['divider' => true],
            [
                'text' => 'Two',
                'url' => 'https://example.com/two.php',
                'haschildren' => 1,
                'children' => [
                    ['text' => 'Child one', 'url' => 'https://example.com/c1.php'],
                    ['divider' => true],
                    ['text' => 'Child two', 'url' => 'https://example.com/c2.php'],
                ],
            ],
        ];

        $items = $this->call_react_export('export_nodes_for_react', [$nodes, false]);

        // The top level divider is dropped: the legacy template had no markup for one there.
        $this->assertCount(2, $items);
        $this->assertSame(['One', 'Two'], array_column($items, 'text'));

        // The submenu divider is preserved so DropdownItems can render a dropdown-divider.
        $children = $items[1]['children'];
        $this->assertCount(3, $children);
        $this->assertFalse($children[0]['divider']);
        $this->assertTrue($children[1]['divider']);
        $this->assertFalse($children[2]['divider']);
        // Divider records still carry a key, so React never renders siblings with a duplicate one.
        $this->assertNotSame($children[0]['key'], $children[1]['key']);
    }

    /**
     * Labels arrive already formatted, so they must be decoded before React escapes them again.
     *
     * @covers \core\navigation\output\primary::decode_node_text
     * @covers \core\navigation\output\primary::export_node_for_react
     */
    public function test_export_node_for_react_decodes_formatted_labels(): void {
        global $CFG, $PAGE;

        // The format_string() function encodes the bare ampersand. React escapes its own text
        // nodes, so without decoding the item would render as "Terms &amp; Conditions".
        $CFG->custommenuitems = "Terms & Conditions|/terms.php|Read the terms & conditions";
        $this->setUser(0);

        $primary = new primary($PAGE);
        $data = $primary->export_for_template($PAGE->get_renderer('core'));
        $reactprops = json_decode($data['moremenu']['reactprops'], true);

        $texts = array_column($reactprops['items'], 'text');
        $this->assertContains('Terms & Conditions', $texts);
        $this->assertNotContains('Terms &amp; Conditions', $texts);

        $terms = $reactprops['items'][array_search('Terms & Conditions', $texts, true)];
        // A custom menu title differing from the label is preserved, and decoded the same way.
        $this->assertSame('Read the terms & conditions', $terms['title']);
    }

    /**
     * Custom menu nodes export a stringified url rather than a url object.
     *
     * @covers \core\navigation\output\primary::resolve_node_href
     */
    public function test_resolve_node_href_handles_both_node_shapes(): void {
        $url = new \core\url('/my/courses.php');

        $this->assertSame(
            $url->out(false),
            $this->call_react_export('resolve_node_href', [['url' => $url]]),
        );

        $this->assertSame(
            'https://example.com/custom.php',
            $this->call_react_export('resolve_node_href', [['url' => 'https://example.com/custom.php']]),
        );

        $this->assertNull($this->call_react_export('resolve_node_href', [['url' => null]]));
        $this->assertNull($this->call_react_export('resolve_node_href', [[]]));
    }

    /**
     * A stringified url arrives HTML escaped and must be decoded before React sets it as an href.
     *
     * @covers \core\navigation\output\primary::resolve_node_href
     */
    public function test_resolve_node_href_decodes_an_escaped_custom_menu_url(): void {
        // The url arrives escaped for the legacy template's raw href="{{{url}}}". React sets href
        // with setAttribute(), so left alone the second parameter below would be "amp;mode".
        $this->assertSame(
            'https://example.com/report/log/index.php?id=2&mode=all',
            $this->call_react_export(
                'resolve_node_href',
                [['url' => 'https://example.com/report/log/index.php?id=2&amp;mode=all']],
            ),
        );
    }

    /**
     * The whole custom menu pipeline, from admin setting to React prop, keeps a usable url.
     *
     * @covers \core\navigation\output\primary::export_react_props
     * @covers \core\navigation\output\primary::resolve_node_href
     */
    public function test_export_react_props_keeps_custom_menu_urls_navigable(): void {
        global $CFG, $PAGE;

        $CFG->custommenuitems = "Log report|/report/log/index.php?id=2&mode=all";
        $this->setUser(0);

        $primary = new primary($PAGE);
        $data = $primary->export_for_template($PAGE->get_renderer('core'));
        $reactprops = json_decode($data['moremenu']['reactprops'], true);

        $hrefs = array_column($reactprops['items'], 'href');
        $this->assertContains(
            (new \core\url('/report/log/index.php', ['id' => 2, 'mode' => 'all']))->out(false),
            $hrefs,
        );
        foreach ($hrefs as $href) {
            $this->assertStringNotContainsString('&amp;', (string) $href);
        }
    }

    /**
     * Keys only need to be unique among siblings, and must not depend on the label.
     *
     * @covers \core\navigation\output\primary::resolve_node_key
     */
    public function test_resolve_node_key_prefers_key_then_sort_then_position(): void {
        $this->assertSame(
            'myhome',
            $this->call_react_export('resolve_node_key', [['key' => 'myhome', 'sort' => 'myhome'], 0]),
        );
        // Custom menu items carry only a sort, and its values start at 1.
        $this->assertSame('2', $this->call_react_export('resolve_node_key', [['sort' => 2], 5]));
        // A sort of 0 is still a usable key, so it must not be treated as absent.
        $this->assertSame('0', $this->call_react_export('resolve_node_key', [['sort' => 0], 5]));
        // Nothing identifying at all: fall back to the sibling position.
        $this->assertSame('node-4', $this->call_react_export('resolve_node_key', [['text' => 'Anything'], 4]));
    }

    /**
     * Test the primary export when the home link is disabled.
     *
     * @covers \core\navigation\output\primary::export_for_template
     * @dataProvider primary_export_without_home_provider
     * @param bool $withlang Setup with langs
     * @param array $expecteditems An array of nodes expected with content in them.
     */
    public function test_primary_export_without_home(bool $withlang, array $expecteditems): void {
        global $CFG, $PAGE;

        set_config('enablemyhome', 0);
        $this->setUser(0);

        if ($withlang) {
            mkdir("$CFG->dataroot/lang/de", 0777, true);
            mkdir("$CFG->dataroot/lang/fr", 0777, true);
            $stringmanager = get_string_manager();
            $stringmanager->reset_caches(true);
        }

        $primary = new primary($PAGE);
        $renderer = $PAGE->get_renderer('core');
        $data = array_filter($primary->export_for_template($renderer));

        $this->assertEqualsCanonicalizing($expecteditems, array_keys($data));
    }

    /**
     * Provider for the test_primary_export_without_home function.
     *
     * @return array
     */
    public static function primary_export_without_home_provider(): array {
        return [
            'No home link and no additional languages' => [false, ['user']],
            'No home link and multiple languages available' => [true, ['lang', 'user']],
        ];
    }

    /**
     * Test the custom menu getter to confirm the nodes gets generated and are returned correctly.
     *
     * @dataProvider custom_menu_provider
     * @param string $config
     * @param array $expected
     */
    public function test_get_custom_menu(string $config, array $expected): void {
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
    public static function custom_menu_provider(): array {
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
    public function test_merge_primary_and_custom(): void {
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

        $method = new ReflectionMethod(primary::class, 'get_primary_nav');
        $dataprimary = $method->invoke($primary);

        // Take this custom menu that would come from the  setting custommenitems.
        $custommenuitems = <<< ENDMENU
        Theme
        -Boost|/?theme=boost
        -Custom|/?theme=custom
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
        $method = new ReflectionMethod(primary::class, 'merge_primary_and_custom');
        $menucomplete = $method->invoke($primary, $dataprimary, $datacustom, $ismobile);
        return $menucomplete;
    }

    /**
     * Test that get_user_menu includes userfirstname in the template context.
     *
     * @covers \core\navigation\output\primary::get_user_menu
     */
    public function test_get_user_menu_includes_userfirstname(): void {
        global $PAGE;
        $this->resetAfterTest();

        $PAGE->set_url('/');
        $user = $this->getDataGenerator()->create_user(['firstname' => 'Testfirstname']);
        $this->setUser($user);
        $PAGE->initialise_theme_and_output();

        $primary = new \core\navigation\output\primary($PAGE);
        $renderer = $PAGE->get_renderer('core');
        $usermenu = $primary->get_user_menu($renderer);

        $this->assertArrayHasKey('userfirstname', $usermenu);
        $this->assertEquals('Testfirstname', $usermenu['userfirstname']);
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
