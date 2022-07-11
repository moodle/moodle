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

namespace theme_iomad;

/**
 * Test the iomadnavbar file
 *
 * @package    theme_iomad
 * @covers     \theme_iomad\iomadnavbar
 * @copyright  2022 Derick Turner
 * @author    Derick Turner
 * @based on theme_boost by Peter Dias
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class iomadnavbar_test extends \advanced_testcase {
    /**
     * Provider for test_remove_no_link_items
     * The setup and expected arrays are defined as an array of 'nodekey' => $hasaction
     *
     * @return array
     */
    public function remove_no_link_items_provider(): array {
        return [
            'All nodes have links links including leaf node. Set to remove section nodes.' => [
                [
                    'node1' => ['hasaction' => true, 'issection' => false],
                    'node2' => ['hasaction' => true, 'issection' => false],
                    'node3' => ['hasaction' => true, 'issection' => false],
                ],
                true,
                [
                    'Home' => true,
                    'Courses' => true,
                    'tc_1' => true,
                    'node1' => true,
                    'node2' => true,
                    'node3' => true,
                ]
            ],
            'Only some parent nodes have links. Leaf node has a link. Set to remove section nodes.' => [
                [
                    'node1' => ['hasaction' => false, 'issection' => false],
                    'node2' => ['hasaction' => true, 'issection' => false],
                    'node3' => ['hasaction' => true, 'issection' => false],
                ],
                true,
                [
                    'Home' => true,
                    'Courses' => true,
                    'tc_1' => true,
                    'node2' => true,
                    'node3' => true,
                ]
            ],
            'All parent nodes do not have links. Leaf node has a link. Set to remove section nodes.' => [
                [
                    'node1' => ['hasaction' => false, 'issection' => false],
                    'node2' => ['hasaction' => false, 'issection' => false],
                    'node3' => ['hasaction' => true, 'issection' => false],
                ],
                true,
                [
                    'Home' => true,
                    'Courses' => true,
                    'tc_1' => true,
                    'node3' => true,
                ]
            ],
            'All parent nodes have links. Leaf node does not has a link. Set to remove section nodes.' => [
                [
                    'node1' => ['hasaction' => true, 'issection' => false],
                    'node2' => ['hasaction' => true, 'issection' => false],
                    'node3' => ['hasaction' => false, 'issection' => false],
                ],
                true,
                [
                    'Home' => true,
                    'Courses' => true,
                    'tc_1' => true,
                    'node1' => true,
                    'node2' => true,
                    'node3' => false,
                ]
            ],
            'All parent nodes do not have links. Leaf node does not has a link. Set to remove section nodes.' => [
                [
                    'node1' => ['hasaction' => false, 'issection' => false],
                    'node2' => ['hasaction' => false, 'issection' => false],
                    'node3' => ['hasaction' => false, 'issection' => false],
                ],
                true,
                [
                    'Home' => true,
                    'Courses' => true,
                    'tc_1' => true,
                    'node3' => false,
                ]
            ],
            'Some parent nodes do not have links. Leaf node does not has a link. Set to remove section nodes.' => [
                [
                    'node1' => ['hasaction' => true, 'issection' => false],
                    'node2' => ['hasaction' => false, 'issection' => false],
                    'node3' => ['hasaction' => false, 'issection' => false],
                ],
                true,
                [
                    'Home' => true,
                    'Courses' => true,
                    'tc_1' => true,
                    'node1' => true,
                    'node3' => false,
                ]
            ],
            'All nodes have links links including leaf node and section nodes. Set to remove section nodes.' => [
                [
                    'node1' => ['hasaction' => true, 'issection' => false],
                    'node2' => ['hasaction' => true, 'issection' => false],
                    'sectionnode1' => ['hasaction' => true, 'issection' => true],
                    'node3' => ['hasaction' => true, 'issection' => false],
                ],
                true,
                [
                    'Home' => true,
                    'Courses' => true,
                    'tc_1' => true,
                    'node1' => true,
                    'node2' => true,
                    'node3' => true,
                ]
            ],
            'All nodes have links links including leaf node and section nodes. Set to not remove section nodes.' => [
                [
                    'node1' => ['hasaction' => true, 'issection' => false],
                    'node2' => ['hasaction' => true, 'issection' => false],
                    'sectionnode1' => ['hasaction' => true, 'issection' => true],
                    'node3' => ['hasaction' => true, 'issection' => false],
                ],
                false,
                [
                    'Home' => true,
                    'Courses' => true,
                    'tc_1' => true,
                    'node1' => true,
                    'node2' => true,
                    'sectionnode1' => true,
                    'node3' => true,
                ]
            ],
            'Only some parent nodes have links. Section node does not have a link. Set to not remove section nodes.' => [
                [
                    'node1' => ['hasaction' => false, 'issection' => false],
                    'node2' => ['hasaction' => true, 'issection' => false],
                    'sectionnode1' => ['hasaction' => false, 'issection' => true],
                    'node3' => ['hasaction' => true, 'issection' => false],
                ],
                true,
                [
                    'Home' => true,
                    'Courses' => true,
                    'tc_1' => true,
                    'node2' => true,
                    'node3' => true,
                ]
            ]
        ];
    }
    /**
     * Test the remove_no_link_items function
     *
     * @dataProvider remove_no_link_items_provider
     * @param array $setup
     * @param bool $removesectionnodes Whether to remove the section nodes with an associated action.
     * @param array $expected
     * @throws \ReflectionException
     */
    public function test_remove_no_link_items(array $setup, bool $removesectionnodes, array $expected) {
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
        foreach ($setup as $key => $value) {
            $page->navbar->add($key, $value['hasaction'] ? $url : null,
                $value['issection'] ? \navigation_node::TYPE_SECTION : null);
        }

        $iomadnavbar = $this->getMockBuilder(iomadnavbar::class)
            ->disableOriginalConstructor()
            ->onlyMethods([])
            ->getMock();

        $rc = new \ReflectionClass(iomadnavbar::class);
        $rcp = $rc->getProperty('items');
        $rcp->setAccessible(true);
        $rcp->setValue($iomadnavbar, $page->navbar->get_items());

        // Make the call to the function.
        $rcm = $rc->getMethod('remove_no_link_items');
        $rcm->setAccessible(true);
        $rcm->invoke($iomadnavbar, $removesectionnodes);

        // Get the value for the class variable that the function modifies.
        $values = $rcp->getValue($iomadnavbar);
        $actual = [];
        foreach ($values as $value) {
            $actual[$value->text] = $value->has_action();
        }
        $this->assertEquals($expected, $actual);
    }

    /**
     * Provider for test_remove_duplicate_items.
     *
     * @return array
     */
    public function remove_duplicate_items_provider(): array {
        global $CFG;

        return [
            'Breadcrumb items with identical text and action url (actions of same type moodle_url).' => [
                [
                    [
                        'text' => 'Node 1',
                        'action' => new \moodle_url('/page1.php')
                    ],
                    [
                        'text' => 'Node 2',
                        'action' => new \moodle_url('/page2.php', ['id' => 1])
                    ],
                    [
                        'text' => 'Node 4',
                        'action' => new \moodle_url('/page4.php', ['id' => 1])
                    ],
                    [
                        'text' => 'Node 2',
                        'action' => new \moodle_url('/page2.php', ['id' => 1])
                    ],
                ],
                ['Home', 'Node 1', 'Node 4', 'Node 2']
            ],
            'Breadcrumb items with identical text and action url (actions of different type moodle_url/text).' => [
                [
                    [
                        'text' => 'Node 1',
                        'action' => new \moodle_url('/page1.php')
                    ],
                    [
                        'text' => 'Node 2',
                        'action' => new \moodle_url('/page2.php', ['id' => 1])
                    ],
                    [
                        'text' => 'Node 4',
                        'action' => new \moodle_url('/page4.php', ['id' => 1])
                    ],
                    [
                        'text' => 'Node 2',
                        'action' => "{$CFG->wwwroot}/page2.php?id=1"
                    ],
                ],
                ['Home', 'Node 1', 'Node 4', 'Node 2']
            ],
            'Breadcrumb items with identical text and action url (actions of different type moodle_url/action_link).' => [
                [
                    [
                        'text' => 'Node 1',
                        'action' => new \moodle_url('/page1.php')
                    ],
                    [
                        'text' => 'Node 2',
                        'action' => new \moodle_url('/page2.php', ['id' => 1])
                    ],
                    [
                        'text' => 'Node 4',
                        'action' => new \moodle_url('/page4.php', ['id' => 1])
                    ],
                    [
                        'text' => 'Node 2',
                        'action' => new \action_link(new \moodle_url('/page2.php', ['id' => 1]), 'Action link')
                    ],
                ],
                ['Home', 'Node 1', 'Node 4', 'Node 2']
            ],
            'Breadcrumbs items with identical text but not identical action url.' => [
                [
                    [
                        'text' => 'Node 1',
                        'action' => new \moodle_url('/page1.php')
                    ],
                    [
                        'text' => 'Node 2',
                        'action' => new \moodle_url('/page2.php', ['id' => 1])
                    ],
                    [
                        'text' => 'Node 2',
                        'action' => new \moodle_url('/page2.php', ['id' => 2])
                    ],
                    [
                        'text' => 'Node 4',
                        'action' => new \moodle_url('/page4.php', ['id' => 1])
                    ],
                ],
                ['Home', 'Node 1', 'Node 2', 'Node 2', 'Node 4']
            ],
            'Breadcrumb items with identical action url but not identical text.' => [
                [
                    [
                        'text' => 'Node 1',
                        'action' => new \moodle_url('/page1.php')
                    ],
                    [
                        'text' => 'Node 2',
                        'action' => new \moodle_url('/page2.php', ['id' => 1])
                    ],
                    [
                        'text' => 'Node 3',
                        'action' => new \moodle_url('/page2.php', ['id' => 1])
                    ],
                    [
                        'text' => 'Node 4',
                        'action' => new \moodle_url('/page4.php', ['id' => 1])
                    ],
                ],
                ['Home', 'Node 1', 'Node 2', 'Node 3', 'Node 4']
            ],
            'Breadcrumb items without any identical action url or text.' => [
                [
                    [
                        'text' => 'Node 1',
                        'action' => new \moodle_url('/page1.php')
                    ],
                    [
                        'text' => 'Node 2',
                        'action' => new \moodle_url('/page2.php', ['id' => 1])
                    ],
                    [
                        'text' => 'Node 3',
                        'action' => new \moodle_url('/page3.php', ['id' => 1])
                    ],
                    [
                        'text' => 'Node 4',
                        'action' => new \moodle_url('/page4.php', ['id' => 1])
                    ],
                ],
                ['Home', 'Node 1', 'Node 2', 'Node 3', 'Node 4']
            ],
        ];
    }

    /**
     * Test the remove_duplicate_items function.
     *
     * @dataProvider remove_duplicate_items_provider
     * @param array $navbarnodes The array containing the text and action of the nodes to be added to the navbar
     * @param array $expected The array containing the text of the expected navbar nodes
     */
    public function test_remove_duplicate_items(array $navbarnodes, array $expected) {
        $this->resetAfterTest();
        $page = new \moodle_page();
        $page->set_url('/');

        // Add the navbar nodes.
        foreach ($navbarnodes as $node) {
            $page->navbar->add($node['text'], $node['action'], \navigation_node::TYPE_CUSTOM);
        }

        $iomadnavbar = $this->getMockBuilder(iomadnavbar::class)
            ->disableOriginalConstructor()
            ->onlyMethods([])
            ->getMock();

        $rc = new \ReflectionClass(iomadnavbar::class);
        $rcp = $rc->getProperty('items');
        $rcp->setAccessible(true);
        $rcp->setValue($iomadnavbar, $page->navbar->get_items());

        // Make the call to the function.
        $rcm = $rc->getMethod('remove_duplicate_items');
        $rcm->setAccessible(true);
        $rcm->invoke($iomadnavbar);

        // Get the value for the class variable that the function modifies.
        $values = $rcp->getValue($iomadnavbar);
        $actual = [];
        foreach ($values as $value) {
            $actual[] = $value->text;
        }
        $this->assertEquals($expected, $actual);
    }


    /**
     * Provider for test_remove_items_that_exist_in_navigation.
     *
     * @return array
     */
    public function remove_items_that_exist_in_navigation_provider(): array {
        global $CFG;

        return [
            'Item with identical action url and text exists in the primary navigation menu.' => [
                'primary',
                [
                    [
                        'text' => 'Node 1',
                        'action' => new \moodle_url('/page1.php')
                    ],
                ],
                [
                    'Node 1' => new \moodle_url('/page1.php'),
                    'Node 2' => new \moodle_url('/page2.php'),
                    'Node 3' => new \moodle_url('/page1.php'),
                ],
                ['Node 2', 'Node 3']
            ],
            'Item with identical action url and text exists in the secondary navigation menu.' => [
                'secondary',
                [
                    [
                        'text' => 'Node 2',
                        'action' => new \moodle_url('/page2.php')
                    ],
                ],
                [
                    'Node 1' => new \moodle_url('/page1.php'),
                    'Node 2' => new \moodle_url('/page2.php'),
                    'Node 3' => new \moodle_url('/page1.php'),
                ],
                ['Home', 'Node 1', 'Node 3']
            ],
            'Multiple items with identical action url and text exist in the secondary navigation menu.' => [
                'secondary',
                [
                    [
                        'text' => 'Node 2',
                        'action' => new \moodle_url('/page2.php')
                    ],
                    [
                        'text' => 'Node 3',
                        'action' => new \moodle_url('/page3.php')
                    ],
                ],
                [
                    'Node 1' => new \moodle_url('/page1.php'),
                    'Node 2' => "{$CFG->wwwroot}/page2.php",
                    'Node 3' => new \action_link(new \moodle_url('/page3.php'), 'Action link'),
                ],
                ['Home', 'Node 1']
            ],
            'No items with identical action url and text in the secondary navigation menu.' => [
                'secondary',
                [
                    [
                        'text' => 'Node 4',
                        'action' => new \moodle_url('/page4.php')
                    ],
                ],
                [
                    'Node 1' => new \moodle_url('/page1.php'),
                    'Node 2' => new \moodle_url('/page2.php'),
                    'Node 3' => new \moodle_url('/page1.php'),
                ],
                ['Home', 'Node 1', 'Node 2', 'Node 3']
            ],
        ];
    }

    /**
     * Test the remove_items_that_exist_in_navigation function.
     *
     * @dataProvider remove_items_that_exist_in_navigation_provider
     * @param string $navmenu The name of the navigation menu we would like to use (primary or secondary)
     * @param array $navmenunodes The array containing the text and action of the nodes to be added to the navigation menu
     * @param array $navbarnodes Array containing the text => action of the nodes to be added to the navbar
     * @param array $expected Array containing the text of the expected navbar nodes after the filtering
     */
    public function test_remove_items_that_exist_in_navigation(string $navmenu, array $navmenunodes, array $navbarnodes,
            array $expected) {
        global $PAGE;

        // Unfortunate hack needed because people use global $PAGE around the place.
        $PAGE->set_url('/');
        $this->resetAfterTest();
        $page = new \moodle_page();
        $page->set_url('/');

        switch ($navmenu) {
            case 'primary':
                $navigationmenu = new \core\navigation\views\primary($page);
                break;
            case 'secondary':
                $navigationmenu = new \core\navigation\views\secondary($page);
        }

        $navigationmenu->initialise();
        // Add the additional nodes to the navigation menu.
        foreach ($navmenunodes as $navmenunode) {
            $navigationmenu->add($navmenunode['text'], $navmenunode['action'], \navigation_node::TYPE_CUSTOM);
        }

        // Add the additional navbar nodes.
        foreach ($navbarnodes as $text => $action) {
            $page->navbar->add($text, $action, \navigation_node::TYPE_CUSTOM);
        }

        $iomadnavbar = $this->getMockBuilder(iomadnavbar::class)
            ->disableOriginalConstructor()
            ->onlyMethods([])
            ->getMock();

        $rc = new \ReflectionClass(iomadnavbar::class);
        $rcp = $rc->getProperty('items');
        $rcp->setAccessible(true);
        $rcp->setValue($iomadnavbar, $page->navbar->get_items());

        // Make the call to the function.
        $rcm = $rc->getMethod('remove_items_that_exist_in_navigation');
        $rcm->setAccessible(true);
        $rcm->invoke($iomadnavbar, $navigationmenu);

        // Get the value for the class variable that the function modifies.
        $values = $rcp->getValue($iomadnavbar);
        $actual = [];
        foreach ($values as $value) {
            $actual[] = $value->text;
        }
        $this->assertEquals($expected, $actual);
    }
}
