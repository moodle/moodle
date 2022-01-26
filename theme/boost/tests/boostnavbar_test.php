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

namespace theme_boost;

/**
 * Test the boostnavbar file
 *
 * @package    theme_boost
 * @copyright  2021 Peter Dias
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class boostnavbar_test extends \advanced_testcase {
    /**
     * Provider for test_remove_no_link_items
     * The setup and expected arrays are defined as an array of 'nodekey' => $hasaction
     *
     * @return array
     */
    public function remove_no_link_items_provider(): array {
        return [
            'All nodes have links links including leaf node' => [
                [
                    'node1' => true,
                    'node2' => true,
                    'node3' => true,
                ],
                [
                    'Home' => true,
                    'Courses' => true,
                    'tc_1' => true,
                    'node1' => true,
                    'node2' => true,
                    'node3' => true,
                ]
            ],
            'Only some parent nodes have links. Leaf node has a link.' => [
                [
                    'node1' => false,
                    'node2' => true,
                    'node3' => true,
                ],
                [
                    'Home' => true,
                    'Courses' => true,
                    'tc_1' => true,
                    'node2' => true,
                    'node3' => true,
                ]
            ],
            'All parent nodes do not have links. Leaf node has a link.' => [
                [
                    'node1' => false,
                    'node2' => false,
                    'node3' => true,
                ],
                [
                    'Home' => true,
                    'Courses' => true,
                    'tc_1' => true,
                    'node3' => true,
                ]
            ],
            'All parent nodes have links. Leaf node does not has a link.' => [
                [
                    'node1' => true,
                    'node2' => true,
                    'node3' => false,
                ],
                [
                    'Home' => true,
                    'Courses' => true,
                    'tc_1' => true,
                    'node1' => true,
                    'node2' => true,
                    'node3' => false,
                ]
            ],
            'All parent nodes do not have links. Leaf node does not has a link.' => [
                [
                    'node1' => false,
                    'node2' => false,
                    'node3' => false,
                ],
                [
                    'Home' => true,
                    'Courses' => true,
                    'tc_1' => true,
                    'node3' => false,
                ]
            ],
            'Some parent nodes do not have links. Leaf node does not has a link.' => [
                [
                    'node1' => true,
                    'node2' => false,
                    'node3' => false,
                ],
                [
                    'Home' => true,
                    'Courses' => true,
                    'tc_1' => true,
                    'node1' => true,
                    'node3' => false,
                ]
            ]
        ];
    }
    /**
     * Test the remove_no_link_items function
     *
     * @dataProvider remove_no_link_items_provider
     * @param array $setup
     * @param array $expected
     * @throws \ReflectionException
     */
    public function test_remove_no_link_items(array $setup, array $expected) {
        global $PAGE;

        $this->resetAfterTest();
        // Unfortunate hack needed because people use global $PAGE around the place.
        $PAGE->set_url('/');
        $course = $this->getDataGenerator()->create_course();
        $page = new \moodle_page();
        $page->set_course($course);
        $page->set_url(new \moodle_url('/course/view.php', array('id' => $course->id)));
        // A dummy url to use. We don't care where it's pointing to.
        $url = new \moodle_url('/');
        foreach ($setup as $node => $hasaction) {
            $page->navbar->add($node, $hasaction ? $url : null);
        }

        $boostnavbar = $this->getMockBuilder(boostnavbar::class)
            ->disableOriginalConstructor()
            ->onlyMethods([])
            ->getMock();

        $rc = new \ReflectionClass(boostnavbar::class);
        $rcp = $rc->getProperty('items');
        $rcp->setAccessible(true);
        $rcp->setValue($boostnavbar, $page->navbar->get_items());

        // Make the call to the function.
        $rcm = $rc->getMethod('remove_no_link_items');
        $rcm->setAccessible(true);
        $rcm->invoke($boostnavbar);

        // Get the value for the class variable that the function modifies.
        $values = $rcp->getValue($boostnavbar);
        $actual = [];
        foreach ($values as $value) {
            $actual[$value->text] = $value->has_action();
        }
        $this->assertEquals($expected, $actual);
    }
}
