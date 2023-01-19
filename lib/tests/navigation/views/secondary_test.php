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

namespace core\navigation\views;

use booktool_print\output\renderer;
use navigation_node;
use ReflectionMethod;
use moodle_url;

/**
 * Class core_secondary_testcase
 *
 * Unit test for the secondary nav view.
 *
 * @package     core
 * @category    navigation
 * @copyright   2021 onwards Peter Dias
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class secondary_test extends \advanced_testcase {
    /**
     * Test the get_leaf_nodes function
     * @param float $siteorder The order for the siteadmin node
     * @param float $courseorder The order for the course node
     * @param float $moduleorder The order for the module node
     * @dataProvider leaf_nodes_order_provider
     */
    public function test_get_leaf_nodes(float $siteorder, float $courseorder, float $moduleorder) {
        global $PAGE;

        // Create a secondary navigation and populate with some dummy nodes.
        $secondary = new secondary($PAGE);
        $secondary->add('Site Admin', '#', secondary::TYPE_SETTING, null, 'siteadmin');
        $secondary->add('Course Admin', '#', secondary::TYPE_CUSTOM, null, 'courseadmin');
        $secondary->add('Module Admin', '#', secondary::TYPE_SETTING, null, 'moduleadmin');
        $nodes = [
            navigation_node::TYPE_SETTING => [
                'siteadmin' => $siteorder,
                'moduleadmin' => $courseorder,
            ],
            navigation_node::TYPE_CUSTOM => [
                'courseadmin' => $moduleorder,
            ]
        ];
        $expectednodes = [
            "$siteorder" => 'siteadmin',
            "$courseorder" => 'moduleadmin',
            "$moduleorder" => 'courseadmin',
        ];

        $method = new ReflectionMethod('core\navigation\views\secondary', 'get_leaf_nodes');
        $method->setAccessible(true);
        $sortednodes = $method->invoke($secondary, $secondary, $nodes);
        foreach ($sortednodes as $order => $node) {
            $this->assertEquals($expectednodes[$order], $node->key);
        }
    }

    /**
     * Data provider for test_get_leaf_nodes
     * @return array
     */
    public function leaf_nodes_order_provider(): array {
        return [
            'Initialise the order with whole numbers' => [3, 2, 1],
            'Initialise the order with a mix of whole and float numbers' => [2.1, 2, 1],
        ];
    }

    /**
     * Test the initialise in different contexts
     *
     * @param string $context The context to setup for - course, module, system
     * @param string $expectedfirstnode The expected first node
     * @param string $header The expected string
     * @param string $activenode The expected active node
     * @param string $courseformat The used course format (only applicable in the course and module context).
     * @return void
     * @dataProvider setting_initialise_provider
     */
    public function test_setting_initialise(string $context, string $expectedfirstnode,
            string $header, string $activenode, string $courseformat = 'topics'): void {
        global $PAGE, $SITE;
        $this->resetAfterTest();
        $this->setAdminUser();
        $pagecourse = $SITE;
        $pageurl = '/';
        switch ($context) {
            case 'course':
                $pagecourse = $this->getDataGenerator()->create_course(['format' => $courseformat]);
                $contextrecord = \context_course::instance($pagecourse->id, MUST_EXIST);
                if ($courseformat === 'singleactivity') {
                    $pageurl = new \moodle_url('/course/edit.php', ['id' => $pagecourse->id]);
                } else {
                    $pageurl = new \moodle_url('/course/view.php', ['id' => $pagecourse->id]);
                }
                break;
            case 'module':
                $pagecourse = $this->getDataGenerator()->create_course(['format' => $courseformat]);
                $assign = $this->getDataGenerator()->create_module('assign', ['course' => $pagecourse->id]);
                $cm = get_coursemodule_from_id('assign', $assign->cmid);
                $contextrecord = \context_module::instance($cm->id);
                $pageurl = new \moodle_url('/mod/assign/view.php', ['id' => $cm->id]);
                $PAGE->set_cm($cm);
                break;
            case 'system':
                $contextrecord = \context_system::instance();
                $PAGE->set_pagelayout('admin');
                $pageurl = new \moodle_url('/admin/index.php');

        }
        $PAGE->set_url($pageurl);
        navigation_node::override_active_url($pageurl);
        $PAGE->set_course($pagecourse);
        $PAGE->set_context($contextrecord);

        $node = new secondary($PAGE);
        $node->initialise();
        $children = $node->get_children_key_list();
        $this->assertEquals($expectedfirstnode, $children[0]);
        $this->assertEquals(get_string($header), $node->headertitle);
        $this->assertEquals($activenode, $node->activenode->text);
    }

    /**
     * Data provider for the test_setting_initialise function
     * @return array
     */
    public function setting_initialise_provider(): array {
        return [
            'Testing in a course context' => ['course', 'coursehome', 'courseheader', 'Course'],
            'Testing in a course context using a single activity course format' =>
                ['course', 'course', 'courseheader', 'Course', 'singleactivity'],
            'Testing in a module context' => ['module', 'modulepage', 'activityheader', 'Assignment'],
            'Testing in a module context using a single activity course format' =>
                ['module', 'course', 'activityheader', 'Activity', 'singleactivity'],
            'Testing in a site admin' => ['system', 'siteadminnode', 'homeheader', 'General'],
        ];
    }

    /**
     * Get the nav tree initialised to test active_node_scan.
     *
     * This is to test the secondary nav with navigation_node instance.
     *
     * @param string|null $seturl The url set for $PAGE.
     * @return navigation_node The initialised nav tree.
     */
    private function get_tree_initilised_to_set_activate(?string $seturl = null): navigation_node {
        global $PAGE;

        $node = new secondary($PAGE);

        $node->key = 'mytestnode';
        $node->type = navigation_node::TYPE_SYSTEM;
        $node->add('first child', null, navigation_node::TYPE_CUSTOM, 'firstchld', 'firstchild');
        $child2 = $node->add('second child', null, navigation_node::TYPE_COURSE, 'secondchld', 'secondchild');
        $child3 = $node->add('third child', null, navigation_node::TYPE_CONTAINER, 'thirdchld', 'thirdchild');
        $node->add('fourth child', null, navigation_node::TYPE_ACTIVITY, 'fourthchld', 'fourthchld');
        $node->add('fifth child', '/my', navigation_node::TYPE_CATEGORY, 'fifthchld', 'fifthchild');

        // If seturl is null then set actionurl of child6 to '/'.
        if ($seturl === null) {
            $child6actionurl = new \moodle_url('/');
        } else {
            // If seturl is provided then set actionurl of child6 to '/foo'.
            $child6actionurl = new \moodle_url('/foo');
        }
        $child6 = $child2->add('sixth child', $child6actionurl, navigation_node::TYPE_COURSE, 'sixthchld', 'sixthchild');
        // Activate the sixthchild node.
        $child6->make_active();
        $child2->add('seventh child', null, navigation_node::TYPE_COURSE, 'seventhchld', 'seventhchild');
        $child8 = $child2->add('eighth child', null, navigation_node::TYPE_CUSTOM, 'eighthchld', 'eighthchild');
        $child8->add('nineth child', null, navigation_node::TYPE_CUSTOM, 'ninethchld', 'ninethchild');
        $child3->add('tenth child', null, navigation_node::TYPE_CUSTOM, 'tenthchld', 'tenthchild');

        return $node;
    }

    /**
     * Testing active_node_scan on navigation_node instance.
     *
     * @param string $expectedkey The expected node key.
     * @param string|null $key The key set by user using set_secondary_active_tab.
     * @param string|null $seturl The url set by user.
     * @return void
     * @dataProvider active_node_scan_provider
     */
    public function test_active_node_scan(string $expectedkey, ?string $key = null, ?string $seturl = null): void {
        global $PAGE;

        if ($seturl !== null) {
            navigation_node::override_active_url(new \moodle_url($seturl));
        } else {
            $PAGE->set_url('/');
            navigation_node::override_active_url(new \moodle_url('/'));
        }
        if ($key !== null) {
            $PAGE->set_secondary_active_tab($key);
        }

        $node = $this->get_tree_initilised_to_set_activate($seturl);
        $secondary = new secondary($PAGE);
        $method = new ReflectionMethod('core\navigation\views\secondary', 'active_node_scan');
        $method->setAccessible(true);

        $result = $method->invoke($secondary, $node);

        if ($expectedkey !== '') {
            $this->assertInstanceOf('navigation_node', $result);
            $this->assertEquals($expectedkey, $result->key);
        } else {
            $this->assertNull($result);
        }
    }

    /**
     * Data provider for the active_node_scan_provider
     *
     * @return array
     */
    public function active_node_scan_provider(): array {
        return [
            'Test by activating node adjacent to root node'
                => ['firstchild', 'firstchild'],
            'Activate a grand child node of the root'
                => ['thirdchild', 'tenthchild'],
            'When child node is activated the parent node is activated and returned'
                => ['secondchild', null],
            'Test by setting an empty string as node key to activate' => ['secondchild', ''],
            'Activate a node which does not exist in the tree'
                => ['', 'foobar'],
            'Activate the leaf node of the tree' => ['secondchild', 'ninethchild', null, true],
            'Changing the $PAGE url which is different from action url of child6 and not setting active tab manually'
                => ['', null, '/foobar'],
            'Having $PAGE url and child6 action url same and not setting active tab manually'
                => ['secondchild', null, '/foo'],
        ];
    }

    /**
     * Test the force_nodes_into_more_menu method.
     *
     * @param array $secondarynavnodesdata The array which contains the data used to generate the secondary navigation
     * @param array $defaultmoremenunodes  The array containing the keys of the navigation nodes which should be added
     *                                     to the "more" menu by default
     * @param int|null $maxdisplayednodes  The maximum limit of navigation nodes displayed in the secondary navigation
     * @param array $expecedmoremenunodes  The array containing the keys of the expected navigation nodes which are
     *                                     forced into the "more" menu
     * @dataProvider force_nodes_into_more_menu_provider
     */
    public function test_force_nodes_into_more_menu(array $secondarynavnodesdata, array $defaultmoremenunodes,
            ?int $maxdisplayednodes, array $expecedmoremenunodes) {
        global $PAGE;

        // Create a dummy secondary navigation.
        $secondary = new secondary($PAGE);
        foreach ($secondarynavnodesdata as $nodedata) {
            $secondary->add($nodedata['text'], '#', secondary::TYPE_SETTING, null, $nodedata['key']);
        }

        $method = new ReflectionMethod('core\navigation\views\secondary', 'force_nodes_into_more_menu');
        $method->setAccessible(true);
        $method->invoke($secondary, $defaultmoremenunodes, $maxdisplayednodes);

        $actualmoremenunodes = [];
        foreach ($secondary->children as $node) {
            if ($node->forceintomoremenu) {
                $actualmoremenunodes[] = $node->key;
            }
        }
        // Assert that the actual nodes forced into the "more" menu matches the expected ones.
        $this->assertEquals($expecedmoremenunodes, $actualmoremenunodes);
    }

    /**
     * Data provider for the test_force_nodes_into_more_menu function.
     *
     * @return array
     */
    public function force_nodes_into_more_menu_provider(): array {
        return [
            'The total number of navigation nodes exceeds the max display limit (5); ' .
            'navnode2 and navnode4 are forced into "more" menu by default.' =>
                [
                    [
                        [ 'text' => 'Navigation node 1', 'key'  => 'navnode1'],
                        [ 'text' => 'Navigation node 2', 'key'  => 'navnode2'],
                        [ 'text' => 'Navigation node 3', 'key'  => 'navnode3'],
                        [ 'text' => 'Navigation node 4', 'key'  => 'navnode4'],
                        [ 'text' => 'Navigation node 5', 'key'  => 'navnode5'],
                        [ 'text' => 'Navigation node 6', 'key'  => 'navnode6'],
                        [ 'text' => 'Navigation node 7', 'key'  => 'navnode7'],
                        [ 'text' => 'Navigation node 8', 'key'  => 'navnode8'],
                        [ 'text' => 'Navigation node 9', 'key'  => 'navnode9'],
                    ],
                    [
                        'navnode2',
                        'navnode4',
                    ],
                    5,
                    [
                        'navnode2',
                        'navnode4',
                        'navnode8',
                        'navnode9',
                    ],
                ],
            'The total number of navigation nodes does not exceed the max display limit (5); ' .
            'navnode2 and navnode4 are forced into "more" menu by default.' =>
                [
                    [
                        [ 'text' => 'Navigation node 1', 'key'  => 'navnode1'],
                        [ 'text' => 'Navigation node 2', 'key'  => 'navnode2'],
                        [ 'text' => 'Navigation node 3', 'key'  => 'navnode3'],
                        [ 'text' => 'Navigation node 4', 'key'  => 'navnode4'],
                        [ 'text' => 'Navigation node 5', 'key'  => 'navnode5'],
                    ],
                    [
                        'navnode2',
                        'navnode4',
                    ],
                    5,
                    [
                        'navnode2',
                        'navnode4',
                    ],
                ],
            'The max display limit of navigation nodes is not defined; ' .
            'navnode2 and navnode4 are forced into "more" menu by default.' =>
                [
                    [
                        [ 'text' => 'Navigation node 1', 'key'  => 'navnode1'],
                        [ 'text' => 'Navigation node 2', 'key'  => 'navnode2'],
                        [ 'text' => 'Navigation node 3', 'key'  => 'navnode3'],
                        [ 'text' => 'Navigation node 4', 'key'  => 'navnode4'],
                        [ 'text' => 'Navigation node 5', 'key'  => 'navnode5'],
                    ],
                    [
                        'navnode2',
                        'navnode4',
                    ],
                    null,
                    [
                        'navnode2',
                        'navnode4',
                    ],
                ],
            'The total number of navigation nodes exceeds the max display limit (5); ' .
            'no forced navigation nodes into "more" menu by default.' =>
                [
                    [
                        [ 'text' => 'Navigation node 1', 'key'  => 'navnode1'],
                        [ 'text' => 'Navigation node 2', 'key'  => 'navnode2'],
                        [ 'text' => 'Navigation node 3', 'key'  => 'navnode3'],
                        [ 'text' => 'Navigation node 4', 'key'  => 'navnode4'],
                        [ 'text' => 'Navigation node 5', 'key'  => 'navnode5'],
                        [ 'text' => 'Navigation node 6', 'key'  => 'navnode6'],
                        [ 'text' => 'Navigation node 7', 'key'  => 'navnode7'],
                        [ 'text' => 'Navigation node 8', 'key'  => 'navnode8'],
                    ],
                    [],
                    5,
                    [
                        'navnode6',
                        'navnode7',
                        'navnode8',
                    ],
                ],
            'The total number of navigation nodes does not exceed the max display limit (5); ' .
            'no forced navigation nodes into "more" menu by default.' =>
                [
                    [
                        [ 'text' => 'Navigation node 1', 'key'  => 'navnode1'],
                        [ 'text' => 'Navigation node 2', 'key'  => 'navnode2'],
                        [ 'text' => 'Navigation node 3', 'key'  => 'navnode3'],
                        [ 'text' => 'Navigation node 4', 'key'  => 'navnode4'],
                        [ 'text' => 'Navigation node 5', 'key'  => 'navnode5'],
                        [ 'text' => 'Navigation node 6', 'key'  => 'navnode6'],
                    ],
                    [],
                    5,
                    [
                        'navnode6',
                    ],
                ],
            'The max display limit of navigation nodes is not defined; ' .
            'no forced navigation nodes into "more" menu by default.' =>
                [
                    [
                        [ 'text' => 'Navigation node 1', 'key'  => 'navnode1'],
                        [ 'text' => 'Navigation node 2', 'key'  => 'navnode2'],
                        [ 'text' => 'Navigation node 3', 'key'  => 'navnode3'],
                        [ 'text' => 'Navigation node 4', 'key'  => 'navnode4'],
                        [ 'text' => 'Navigation node 5', 'key'  => 'navnode5'],
                        [ 'text' => 'Navigation node 6', 'key'  => 'navnode6'],
                    ],
                    [],
                    null,
                    [],
                ],
        ];
    }

    /**
     * Recursive call to generate a navigation node given an array definition.
     *
     * @param array $structure
     * @param string $parentkey
     * @return navigation_node
     */
    private function generate_node_tree_construct(array $structure, string $parentkey): navigation_node {
        $node = navigation_node::create($parentkey, null, navigation_node::TYPE_CUSTOM, '', $parentkey);
        foreach ($structure as $key => $value) {
            if (is_array($value)) {
                $children = $value['children'] ?? $value;
                $child = $this->generate_node_tree_construct($children, $key);
                if (isset($value['action'])) {
                    $child->action = new \moodle_url($value['action']);
                }
                $node->add_node($child);
            } else {
                $node->add($key, $value, navigation_node::TYPE_CUSTOM, '', $key);
            }
        }

        return $node;
    }

    /**
     * Test the nodes_match_current_url function.
     *
     * @param string $selectedurl
     * @param string $expectednode
     * @dataProvider nodes_match_current_url_provider
     */
    public function test_nodes_match_current_url(string $selectedurl, string $expectednode) {
        global $PAGE;
        $structure = [
            'parentnode1' => [
                'child1' => '/my',
                'child2' => [
                    'child2.1' => '/view/course.php',
                    'child2.2' => '/view/admin.php',
                ]
            ]
        ];
        $node = $this->generate_node_tree_construct($structure, 'primarynode');
        $node->action = new \moodle_url('/');

        $PAGE->set_url($selectedurl);
        $secondary = new secondary($PAGE);
        $method = new ReflectionMethod('core\navigation\views\secondary', 'nodes_match_current_url');
        $method->setAccessible(true);
        $response = $method->invoke($secondary, $node);

        $this->assertSame($response->key ?? null, $expectednode);
    }

    /**
     * Provider for test_nodes_match_current_url
     *
     * @return \string[][]
     */
    public function nodes_match_current_url_provider(): array {
        return [
            "Match url to a node that is a deep nested" => [
                '/view/course.php',
                'child2.1',
            ],
            "Match url to a parent node with children" => [
                '/', 'primarynode'
            ],
            "Match url to a child node" => [
                '/my', 'child1'
            ],
        ];
    }

    /**
     * Test the get_menu_array function
     *
     * @param string $selected
     * @param array $expected
     * @dataProvider get_menu_array_provider
     */
    public function test_get_menu_array(string $selected, array $expected) {
        global $PAGE;

        // Custom nodes - mimicing nodes added via 3rd party plugins.
        $structure = [
            'parentnode1' => [
                'child1' => '/my',
                'child2' => [
                    'action' => '/test.php',
                    'children' => [
                        'child2.1' => '/view/course.php?child=2',
                        'child2.2' => '/view/admin.php?child=2',
                        'child2.3' => '/test.php',
                    ]
                ],
                'child3' => [
                    'child3.1' => '/view/course.php?child=3',
                    'child3.2' => '/view/admin.php?child=3',
                ]
            ],
            'parentnode2' => "/view/module.php"
        ];

        $secondary = new secondary($PAGE);
        $secondary->add_node($this->generate_node_tree_construct($structure, 'primarynode'));
        $selectednode = $secondary->find($selected, null);
        $response = \core\navigation\views\secondary::create_menu_element([$selectednode]);

        $this->assertSame($expected, $response);
    }

    /**
     * Provider for test_get_menu_array
     *
     * @return array[]
     */
    public function get_menu_array_provider(): array {
        return [
            "Fetch information from a node with action and no children" => [
                'child1',
                [
                    'https://www.example.com/moodle/my' => 'child1'
                ],
            ],
            "Fetch information from a node with no action and children" => [
                'child3',
                [
                    'https://www.example.com/moodle/view/course.php?child=3' => 'child3.1',
                    'https://www.example.com/moodle/view/admin.php?child=3' => 'child3.2'
                ],
            ],
            "Fetch information from a node with children" => [
                'child2',
                [
                    'https://www.example.com/moodle/test.php' => 'child2.3',
                    'https://www.example.com/moodle/view/course.php?child=2' => 'child2.1',
                    'https://www.example.com/moodle/view/admin.php?child=2' => 'child2.2'
                ],
            ],
            "Fetch information from a node with an action and no children" => [
                'parentnode2',
                ['https://www.example.com/moodle/view/module.php' => 'parentnode2'],
            ],
            "Fetch information from a node with an action and multiple nested children" => [
                'parentnode1',
                [
                    [
                        'parentnode1' => [
                            'https://www.example.com/moodle/my' => 'child1'
                        ],
                        'child2' => [
                            'https://www.example.com/moodle/test.php' => 'child2',
                            'https://www.example.com/moodle/view/course.php?child=2' => 'child2.1',
                            'https://www.example.com/moodle/view/admin.php?child=2' => 'child2.2',
                        ],
                        'child3' => [
                            'https://www.example.com/moodle/view/course.php?child=3' => 'child3.1',
                            'https://www.example.com/moodle/view/admin.php?child=3' => 'child3.2'
                        ]
                    ]
                ],
            ],
        ];
    }

    /**
     * Test the get_node_with_first_action function
     *
     * @param string $selectedkey
     * @param string|null $expectedkey
     * @dataProvider get_node_with_first_action_provider
     */
    public function test_get_node_with_first_action(string $selectedkey, ?string $expectedkey) {
        global $PAGE;
        $structure = [
            'parentnode1' => [
                'child1' => [
                    'child1.1' => null
                ],
                'child2' => [
                    'child2.1' => [
                        'child2.1.1' => [
                            'action' => '/test.php',
                            'children' => [
                                'child2.1.1.1' => '/view/course.php?child=2',
                                'child2.1.1.2' => '/view/admin.php?child=2',
                            ]
                        ]
                    ]
                ],
                'child3' => [
                    'child3.1' => '/view/course.php?child=3',
                    'child3.2' => '/view/admin.php?child=3',
                ]
            ],
            'parentnode2' => "/view/module.php"
        ];

        $nodes = $this->generate_node_tree_construct($structure, 'primarynode');
        $selectednode = $nodes->find($selectedkey, null);

        $expected = null;
        // Expected response will be the parent node with the action updated.
        if ($expectedkey) {
            $expectedbasenode = clone $selectednode;
            $actionfromnode = $nodes->find($expectedkey, null);
            $expectedbasenode->action = $actionfromnode->action;
            $expected = $expectedbasenode;
        }

        $secondary = new secondary($PAGE);
        $method = new ReflectionMethod('core\navigation\views\secondary', 'get_node_with_first_action');
        $method->setAccessible(true);
        $response = $method->invoke($secondary, $selectednode, $selectednode);
        $this->assertEquals($expected, $response);
    }

    /**
     * Provider for test_get_node_with_first_action
     *
     * @return array
     */
    public function get_node_with_first_action_provider(): array {
        return [
            "Search for action when parent has no action and multiple children with actions" => [
                "child3",
                "child3.1",
            ],
            "Search for action when parent child is deeply nested." => [
                "child2",
                "child2.1.1"
            ],
            "No navigation node returned when node has no children" => [
                "parentnode2",
                null
            ],
            "No navigation node returned when node has children but no actions available." => [
                "child1",
                null
            ],
        ];
    }

    /**
     * Test the add_external_nodes_to_secondary function.
     *
     * @param array $structure The structure of the navigation node tree to setup with.
     * @param array $expectednodes The expected nodes added to the secondary navigation
     * @param bool $separatenode Whether or not to create a separate node to add nodes to.
     * @dataProvider add_external_nodes_to_secondary_provider
     */
    public function test_add_external_nodes_to_secondary(array $structure, array $expectednodes, bool $separatenode = false) {
        global $PAGE;

        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $context = \context_course::instance($course->id);
        $PAGE->set_context($context);
        $PAGE->set_url('/');

        $node = $this->generate_node_tree_construct($structure, 'parentnode');
        $secondary = new secondary($PAGE);
        $secondary->add_node($node);
        $firstnode = $node->get('parentnode1');
        $customparent = null;
        if ($separatenode) {
            $customparent = navigation_node::create('Custom parent');
        }

        $method = new ReflectionMethod('core\navigation\views\secondary', 'add_external_nodes_to_secondary');
        $method->setAccessible(true);
        $method->invoke($secondary, $firstnode, $firstnode, $customparent);

        $actualnodes = $separatenode ? $customparent->get_children_key_list() : $secondary->get_children_key_list();
        $this->assertEquals($expectednodes, $actualnodes);
    }

    /**
     * Provider for the add_external_nodes_to_secondary function.
     *
     * @return array
     */
    public function add_external_nodes_to_secondary_provider() {
        return [
            "Container node with internal action and external children" => [
                [
                    'parentnode1' => [
                        'action' => '/test.php',
                        'children' => [
                            'child2.1' => 'https://example.org',
                            'child2.2' => 'https://example.net',
                        ]
                    ]
                ],
                ['parentnode', 'parentnode1']
            ],
            "Container node with external action and external children" => [
                [
                    'parentnode1' => [
                        'action' => 'https://example.com',
                        'children' => [
                            'child2.1' => 'https://example.org',
                            'child2.2' => 'https://example.net',
                        ]
                    ]
                ],
                ['parentnode', 'parentnode1', 'child2.1', 'child2.2']
            ],
            "Container node with external action and internal children" => [
                [
                    'parentnode1' => [
                        'action' => 'https://example.org',
                        'children' => [
                            'child2.1' => '/view/course.php',
                            'child2.2' => '/view/admin.php',
                        ]
                    ]
                ],
                ['parentnode', 'parentnode1', 'child2.1', 'child2.2']
            ],
            "Container node with internal actions and internal children" => [
                [
                    'parentnode1' => [
                        'action' => '/test.php',
                        'children' => [
                            'child2.1' => '/course.php',
                            'child2.2' => '/admin.php',
                        ]
                    ]
                ],
                ['parentnode', 'parentnode1']
            ],
            "Container node with internal action and external children adding to custom node" => [
                [
                    'parentnode1' => [
                        'action' => '/test.php',
                        'children' => [
                            'child2.1' => 'https://example.org',
                            'child2.2' => 'https://example.net',
                        ]
                    ]
                ],
                ['parentnode1'], true
            ],
            "Container node with external action and external children adding to custom node" => [
                [
                    'parentnode1' => [
                        'action' => 'https://example.com',
                        'children' => [
                            'child2.1' => 'https://example.org',
                            'child2.2' => 'https://example.net',
                        ]
                    ]
                ],
                ['parentnode1', 'child2.1', 'child2.2'], true
            ],
            "Container node with external action and internal children adding to custom node" => [
                [
                    'parentnode1' => [
                        'action' => 'https://example.org',
                        'children' => [
                            'child2.1' => '/view/course.php',
                            'child2.2' => '/view/admin.php',
                        ]
                    ]
                ],
                ['parentnode1', 'child2.1', 'child2.2'], true
            ],
            "Container node with internal actions and internal children adding to custom node" => [
                [
                    'parentnode1' => [
                        'action' => '/test.php',
                        'children' => [
                            'child2.1' => '/course.php',
                            'child2.2' => '/admin.php',
                        ]
                    ]
                ],
                ['parentnode1'], true
            ],
        ];
    }

    /**
     * Test the get_overflow_menu_data function
     *
     * @param string $selectedurl
     * @param bool $expectednull
     * @param bool $emptynode
     * @dataProvider get_overflow_menu_data_provider
     */
    public function test_get_overflow_menu_data(string $selectedurl, bool $expectednull, bool $emptynode = false) {
        global $PAGE;

        $this->resetAfterTest();
        // Custom nodes - mimicing nodes added via 3rd party plugins.
        $structure = [
            'parentnode1' => [
                'child1' => '/my',
                'child2' => [
                    'action' => '/test.php',
                    'children' => [
                        'child2.1' => '/view/course.php',
                        'child2.2' => '/view/admin.php',
                    ]
                ]
            ],
            'parentnode2' => "/view/module.php"
        ];

        $course = $this->getDataGenerator()->create_course();
        $context = \context_course::instance($course->id);
        $PAGE->set_context($context);

        $PAGE->set_url($selectedurl);
        navigation_node::override_active_url(new \moodle_url($selectedurl));
        $node = $this->generate_node_tree_construct($structure, 'primarynode');
        $node->action = new \moodle_url('/');

        $secondary = new secondary($PAGE);
        $secondary->add_node($node);
        $PAGE->settingsnav->add_node(clone $node);
        $secondary->add('Course home', '/coursehome.php', navigation_node::TYPE_CUSTOM, '', 'coursehome');
        $secondary->add('Course settings', '/course/settings.php', navigation_node::TYPE_CUSTOM, '', 'coursesettings');

        // Test for an empty node without children and action.
        if ($emptynode) {
            $node = $secondary->add('Course management', null, navigation_node::TYPE_CUSTOM, '', 'course');
            $node->make_active();
        } else {
            // Set the correct node as active.
            $method = new ReflectionMethod('core\navigation\views\secondary', 'scan_for_active_node');
            $method->setAccessible(true);
            $method->invoke($secondary, $secondary);
        }

        $method = new ReflectionMethod('core\navigation\views\secondary', 'get_overflow_menu_data');
        $method->setAccessible(true);
        $response = $method->invoke($secondary);
        if ($expectednull) {
            $this->assertNull($response);
        } else {
            $this->assertIsObject($response);
            $this->assertInstanceOf('url_select', $response);
        }
    }

    /**
     * Data provider for test_get_overflow_menu_data
     *
     * @return string[]
     */
    public function get_overflow_menu_data_provider(): array {
        return [
            "Active node is the course home node" => [
                '/coursehome.php',
                true
            ],
            "Active node is one with an action and no children" => [
                '/view/module.php',
                false
            ],
            "Active node is one with an action and children" => [
                '/',
                false
            ],
            "Active node is one without an action and children" => [
                '/',
                true,
                true,
            ],
            "Active node is one with an action and children but is NOT in settingsnav" => [
                '/course/settings.php',
                true
            ],
        ];
    }

    /**
     * Test the course administration settings return an overflow menu.
     *
     * @dataProvider get_overflow_menu_data_course_admin_provider
     * @param string $url Url of the page we are testing.
     * @param string $contextidentifier id or contextid or something similar.
     * @param bool $expected The expected return. True to return the overflow menu otherwise false for nothing.
     */
    public function test_get_overflow_menu_data_course_admin(string $url, string $contextidentifier, bool $expected): void {
        global $PAGE;
        $this->resetAfterTest();
        $this->setAdminUser();

        $pagecourse = $this->getDataGenerator()->create_course();
        $contextrecord = \context_course::instance($pagecourse->id, MUST_EXIST);

        $id = ($contextidentifier == 'contextid') ? $contextrecord->id : $pagecourse->id;

        $pageurl = new \moodle_url($url, [$contextidentifier => $id]);
        $PAGE->set_url($pageurl);
        navigation_node::override_active_url($pageurl);
        $PAGE->set_course($pagecourse);
        $PAGE->set_context($contextrecord);

        $node = new secondary($PAGE);
        $node->initialise();
        $result = $node->get_overflow_menu_data();
        if ($expected) {
            $this->assertInstanceOf('url_select', $result);
            $this->assertTrue($pageurl->compare($result->selected));
        } else {
            $this->assertNull($result);
        }
    }

    /**
     * Data provider for the other half of the method thing
     *
     * @return array Provider information.
     */
    public function get_overflow_menu_data_course_admin_provider(): array {
        return [
            "Backup page returns overflow" => [
                '/backup/backup.php',
                'id',
                true
            ],
            "Restore course page returns overflow" => [
                '/backup/restorefile.php',
                'contextid',
                true
            ],
            "Import course page returns overflow" => [
                '/backup/import.php',
                'id',
                true
            ],
            "Course copy page returns overflow" => [
                '/backup/copy.php',
                'id',
                true
            ],
            "Course reset page returns overflow" => [
                '/course/reset.php',
                'id',
                true
            ],
            // The following pages should not return the overflow menu.
            "Course page returns nothing" => [
                '/course/view.php',
                'id',
                false
            ],
            "Question bank should return nothing" => [
                '/question/edit.php',
                'courseid',
                false
            ],
            "Reports should return nothing" => [
                '/report/log/index.php',
                'id',
                false
            ],
            "Participants page should return nothing" => [
                '/user/index.php',
                'id',
                false
            ]
        ];
    }
}
