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
 * Class core_primary_testcase
 *
 * Unit test for the primary nav view.
 *
 * @package     core
 * @category    navigation
 * @copyright   2021 onwards Peter Dias
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class primary_test extends \advanced_testcase {
    /**
     * Test the initialise in different contexts
     *
     * @param string $usertype The user to setup for - admin, guest, regular user
     * @param string $expected The expected nodes
     * @dataProvider setting_initialise_provider
     */
    public function test_setting_initialise($usertype, $expected): void {
        global $PAGE;
        $PAGE->set_url("/");
        $this->resetAfterTest();
        if ($usertype == 'admin') {
            $this->setAdminUser();
        } else if ($usertype == 'guest') {
            $this->setGuestUser();
        } else {
            $user = $this->getDataGenerator()->create_user();
            $this->setUser($user);
        }

        $node = new primary($PAGE);
        $node->initialise();
        $children = $node->get_children_key_list();
        $this->assertEquals($expected, $children);
    }

    /**
     * Data provider for the test_setting_initialise function
     */
    public static function setting_initialise_provider(): array {
        return [
            'Testing as a guest user' => ['guest', ['home']],
            'Testing as an admin' => ['admin', ['home', 'myhome', 'mycourses', 'siteadminnode']],
            'Testing as a regular user' => ['user', ['home', 'myhome', 'mycourses']]
        ];
    }

    /**
     * Get the nav tree initialised to test search_and_set_active_node.
     *
     * @param string|null $seturl The url set for $PAGE.
     * @return navigation_node The initialised nav tree.
     */
    private function get_tree_initilised_to_set_activate(?string $seturl = null): navigation_node {
        $node = new navigation_node('My test node');
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
     * Testing search_and_set_active_node.
     *
     * @param string $expectedkey Expected key of the node, if set.
     * @param string|null $key The key of the node to activate.
     * @param string|null $seturl Set the url for $PAGE.
     * @return void
     * @dataProvider search_and_set_active_node_provider
     */
    public function test_search_and_set_active_node(string $expectedkey, ?string $key = null, ?string $seturl = null): void {
        global $PAGE;

        if ($seturl !== null) {
            navigation_node::override_active_url(new \moodle_url($seturl));
        } else {
            $PAGE->set_url('/');
            navigation_node::override_active_url(new \moodle_url('/'));
        }
        if ($key !== null) {
            $PAGE->set_primary_active_tab($key);
        }

        $node = $this->get_tree_initilised_to_set_activate($seturl);

        $primary = new primary($PAGE);
        $method = new ReflectionMethod('core\navigation\views\primary', 'search_and_set_active_node');

        $result = $method->invoke($primary, $node);

        $sixthchildnode = $node->find('sixthchild', navigation_node::TYPE_COURSE);
        if ($expectedkey !== '') {
            $this->assertInstanceOf('navigation_node', $result);
            $this->assertEquals($result->isactive, true);
            $this->assertEquals($result->key, $expectedkey);

            // Test the state of sixthchild, based on $expectedkey.
            if ($expectedkey !== 'sixthchild') {
                $this->assertFalse($sixthchildnode->isactive);
            } else {
                $this->assertTrue($sixthchildnode->isactive);
            }
        } else {
            $this->assertNull($result);
            $this->assertTrue($sixthchildnode->isactive);
        }
    }

    /**
     * Data provider for test_search_and_set_active_node
     *
     * @return array
     */
    public static function search_and_set_active_node_provider(): array {
        return [
            'Test by activating node which is part of the tree'
                => ['tenthchild', 'tenthchild'],
            'Do not change the state of any nodes of the tree'
                => ['sixthchild'],
            'Test by setting an empty string as node key to activate' => ['sixthchild', ''],
            'Activate a node which does not exist in the tree'
                => ['', 'foobar'],
            'Activate the leaf node of the tree' => ['ninethchild', 'ninethchild'],
            'Test by changing the $PAGE url which is different from action url of child6'
                => ['', null, '/foobar'],
            'Test by having $PAGE url and child6 action url same'
                => ['sixthchild', null, '/foo'],
        ];
    }
}
