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

use core\navigation\views\secondary;

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
class core_secondary_testcase extends advanced_testcase {
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
     * @dataProvider test_setting_initialise_provider
     */
    public function test_setting_initialise(string $context, string $expectedfirstnode) {
        global $PAGE, $SITE;
        $this->resetAfterTest();
        $this->setAdminUser();
        $pagecourse = $SITE;
        $pageurl = '/';
        switch ($context) {
            case 'course':
                $pagecourse = $this->getDataGenerator()->create_course();
                $contextrecord = context_course::instance($pagecourse->id, MUST_EXIST);
                $pageurl = new moodle_url('/course/view.php', ['id' => $pagecourse->id]);
                break;
            case 'module':
                $pagecourse = $this->getDataGenerator()->create_course();
                $assign = $this->getDataGenerator()->create_module('assign', ['course' => $pagecourse->id]);
                $cm = get_coursemodule_from_id('assign', $assign->cmid);
                $contextrecord = context_module::instance($cm->id);
                $pageurl = new moodle_url('/mod/assign/view.php', ['id' => $cm->instance]);
                $PAGE->set_cm($cm);
                break;
            case 'system':
                $contextrecord = context_system::instance();
                $PAGE->set_pagelayout('admin');
                $pageurl = new moodle_url('/admin/index.php');

        }
        $PAGE->set_url($pageurl);
        $PAGE->set_course($pagecourse);
        $PAGE->set_context($contextrecord);

        $node = new secondary($PAGE);
        $node->initialise();
        $children = $node->get_children_key_list();
        $this->assertEquals($children[0], $expectedfirstnode);
    }

    /**
     * Data provider for the test_setting_initialise function
     * @return array
     */
    public function test_setting_initialise_provider(): array {
        return [
            'Testing in a course context' => ['course', 'coursehome'],
            'Testing in a module context' => ['module', 'modulepage'],
            'Testing in a site admin' => ['system', 'siteadminnode'],
        ];
    }
}
