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

use navigation_node;
use ReflectionMethod;

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
     * @dataProvider test_setting_initialise_provider
     */
    public function test_setting_initialise(string $context, string $expectedfirstnode,
            string $header, string $activenode) {
        global $PAGE, $SITE;
        $this->resetAfterTest();
        $this->setAdminUser();
        $pagecourse = $SITE;
        $pageurl = '/';
        switch ($context) {
            case 'course':
                $pagecourse = $this->getDataGenerator()->create_course();
                $contextrecord = \context_course::instance($pagecourse->id, MUST_EXIST);
                $pageurl = new \moodle_url('/course/view.php', ['id' => $pagecourse->id]);
                break;
            case 'module':
                $pagecourse = $this->getDataGenerator()->create_course();
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
    public function test_setting_initialise_provider(): array {
        return [
            'Testing in a course context' => ['course', 'coursehome', 'courseheader', 'Course page'],
            'Testing in a module context' => ['module', 'modulepage', 'activityheader', 'Activity'],
            'Testing in a site admin' => ['system', 'siteadminnode', 'homeheader', 'Site administration'],
        ];
    }

    /**
     * Test the force_nodes_into_more_menu method.
     *
     * @param array $secondarynavnodesdata The array which contains the data used to generate the secondary navigation
     * @param array $defaultmoremenunodes  The array containing the keys of the navigation nodes which should be added
     *                                     to the "more" menu by default
     * @param array $expecedmoremenunodes  The array containing the keys of the expected navigation nodes which are
     *                                     forced into the "more" menu
     * @dataProvider test_force_nodes_into_more_menu_provider
     */
    public function test_force_nodes_into_more_menu(array $secondarynavnodesdata, array $defaultmoremenunodes,
            array $expecedmoremenunodes) {
        global $PAGE;

        // Create a dummy secondary navigation.
        $secondary = new secondary($PAGE);
        foreach ($secondarynavnodesdata as $nodedata) {
            $secondary->add($nodedata['text'], '#', secondary::TYPE_SETTING, null, $nodedata['key']);
        }

        $method = new ReflectionMethod('core\navigation\views\secondary', 'force_nodes_into_more_menu');
        $method->setAccessible(true);
        $method->invoke($secondary, $defaultmoremenunodes);

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
    public function test_force_nodes_into_more_menu_provider(): array {
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
                    [
                        'navnode6',
                    ],
                ],
        ];
    }
}
