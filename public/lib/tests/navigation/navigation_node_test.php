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

namespace core\navigation;

use core\output\action_link;
use core\output\actions\popup_action;
use core\output\pix_icon;
use core\tests\navigation\navigation_testcase;

/**
 * Tests for navigation_node.
 *
 * @package    core
 * @category   test
 * @copyright  2025 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(navigation_node::class)]
final class navigation_node_test extends navigation_testcase {
    public function test_node__construct(): void {
        $node = $this->setup_node();

        $fakeproperties = [
            'text' => 'text',
            'shorttext' => 'A very silly extra long short text string, more than 25 characters',
            'key' => 'key',
            'type' => 'navigation_node::TYPE_COURSE',
            'action' => new \moodle_url('http://www.moodle.org/')];

        $node = new navigation_node($fakeproperties);
        $this->assertSame($fakeproperties['text'], $node->text);
        $this->assertTrue(strpos($fakeproperties['shorttext'], substr($node->shorttext, 0, -3)) === 0);
        $this->assertSame($fakeproperties['key'], $node->key);
        $this->assertSame($fakeproperties['type'], $node->type);
        $this->assertSame($fakeproperties['action'], $node->action);
    }

    public function test_node_add(): void {
        $node = $this->setup_node();

        // Add a node with all args set.
        $node1 = $node->add(
            'test_add_1',
            'http://www.moodle.org/',
            navigation_node::TYPE_COURSE,
            'testadd1',
            'key',
            new pix_icon('i/course', ''),
        );
        // Add a node with the minimum args required.
        $node2 = $node->add('test_add_2', null, navigation_node::TYPE_CUSTOM, 'testadd2');
        $node3 = $node->add(str_repeat('moodle ', 15), str_repeat('moodle', 15));

        $this->assertInstanceOf(navigation_node::class, $node1);
        $this->assertInstanceOf(navigation_node::class, $node2);
        $this->assertInstanceOf(navigation_node::class, $node3);

        $ref = $node->get('key');
        $this->assertSame($node1, $ref);

        $ref = $node->get($node2->key);
        $this->assertSame($node2, $ref);

        $ref = $node->get($node2->key, $node2->type);
        $this->assertSame($node2, $ref);

        $ref = $node->get($node3->key, $node3->type);
        $this->assertSame($node3, $ref);
    }

    public function test_node_add_before(): void {
        $node = $this->setup_node();

        // Create 3 nodes.
        $node1 = navigation_node::create(
            'test_add_1',
            null,
            navigation_node::TYPE_CUSTOM,
            'test 1',
            'testadd1'
        );
        $node2 = navigation_node::create(
            'test_add_2',
            null,
            navigation_node::TYPE_CUSTOM,
            'test 2',
            'testadd2'
        );
        $node3 = navigation_node::create(
            'test_add_3',
            null,
            navigation_node::TYPE_CUSTOM,
            'test 3',
            'testadd3'
        );
        // Add node 2, then node 1 before 2, then node 3 at end.
        $node->add_node($node2);
        $node->add_node($node1, 'testadd2');
        $node->add_node($node3);
        // Check the last 3 nodes are in 1, 2, 3 order and have those indexes.
        foreach ($node->children as $child) {
            $keys[] = $child->key;
        }
        $this->assertSame('testadd1', $keys[count($keys) - 3]);
        $this->assertSame('testadd2', $keys[count($keys) - 2]);
        $this->assertSame('testadd3', $keys[count($keys) - 1]);
    }

    public function test_node_add_class(): void {
        $node = $this->setup_node();

        $node = $node->get('demo1');
        $this->assertInstanceOf(navigation_node::class, $node);
        if ($node !== false) {
            $node->add_class('myclass');
            $classes = $node->classes;
            $this->assertContains('myclass', $classes);
        }
    }

    /**
     * Test the add_attribute method.
     * @covers \navigation_node::add_attribute
     */
    public function test_node_add_attribute(): void {
        $node = $this->setup_node();

        $node = $node->get('demo1');
        $this->assertInstanceOf(navigation_node::class, $node);
        if ($node !== false) {
            $node->add_attribute('data-foo', 'bar');
            $attribute = reset($node->attributes);
            $this->assertEqualsCanonicalizing(['name' => 'data-foo', 'value' => 'bar'], $attribute);
        }
    }

    public function test_node_check_if_active(): void {
        $node = $this->setup_node();

        // First test the string urls
        // Demo1 -> action is http://www.moodle.org/, thus should be true.
        $demo5 = $node->find('demo5', navigation_node::TYPE_COURSE);
        if ($this->assertInstanceOf(navigation_node::class, $demo5)) {
            $this->assertTrue($demo5->check_if_active());
        }

        // Demo2 -> action is http://www.moodle.com/, thus should be false.
        $demo2 = $node->get('demo2');
        if ($this->assertInstanceOf(navigation_node::class, $demo2)) {
            $this->assertFalse($demo2->check_if_active());
        }
    }

    public function test_node_contains_active_node(): void {
        $node = $this->setup_node();

        // Demo5, and activity1 were set to active during setup.
        // Should be true as it contains all nodes.
        $this->assertTrue($node->contains_active_node());
        // Should be true as demo5 is a child of demo3.
        $this->assertTrue($node->get('demo3')->contains_active_node());
        // Obviously duff.
        $this->assertFalse($node->get('demo1')->contains_active_node());
        // Should be true as demo5 contains activity1.
        $this->assertTrue($node->get('demo3')->get('demo5')->contains_active_node());
        // Should be true activity1 is the active node.
        $this->assertTrue($node->get('demo3')->get('demo5')->get('activity1')->contains_active_node());
        // Obviously duff.
        $this->assertFalse($node->get('demo3')->get('demo4')->contains_active_node());
    }

    public function test_node_find_active_node(): void {
        $node = $this->setup_node();

        $activenode1 = $node->find_active_node();
        $activenode2 = $node->get('demo1')->find_active_node();

        if ($this->assertInstanceOf(navigation_node::class, $activenode1)) {
            $ref = $node->get('demo3')->get('demo5')->get('activity1');
            $this->assertSame($activenode1, $ref);
        }

        $this->assertNotInstanceOf(navigation_node::class, $activenode2);
    }

    public function test_node_find(): void {
        $node = $this->setup_node();

        $node1 = $node->find('demo1', navigation_node::TYPE_COURSE);
        $node2 = $node->find('demo5', navigation_node::TYPE_COURSE);
        $node3 = $node->find('demo5', navigation_node::TYPE_CATEGORY);
        $node4 = $node->find('demo0', navigation_node::TYPE_COURSE);
        $this->assertInstanceOf(navigation_node::class, $node1);
        $this->assertInstanceOf(navigation_node::class, $node2);
        $this->assertNotInstanceOf(navigation_node::class, $node3);
        $this->assertNotInstanceOf(navigation_node::class, $node4);
    }

    public function test_node_find_expandable(): void {
        $node = $this->setup_node();

        $expandable = [];
        $node->find_expandable($expandable);

        $this->assertCount(0, $expandable);
        if (count($expandable) === 4) {
            $name = $expandable[0]['key'];
            $name .= $expandable[1]['key'];
            $name .= $expandable[2]['key'];
            $name .= $expandable[3]['key'];
            $this->assertSame('demo1demo2demo4hiddendemo2', $name);
        }
    }

    public function test_node_get(): void {
        $node = $this->setup_node();

        $node1 = $node->get('demo1'); // Exists.
        $node2 = $node->get('demo4'); // Doesn't exist for this node.
        $node3 = $node->get('demo0'); // Doesn't exist at all.
        $node4 = $node->get(false);   // Sometimes occurs in nature code.
        $this->assertInstanceOf(navigation_node::class, $node1);
        $this->assertFalse($node2);
        $this->assertFalse($node3);
        $this->assertFalse($node4);
    }

    public function test_node_get_css_type(): void {
        $node = $this->setup_node();

        $csstype1 = $node->get('demo3')->get_css_type();
        $csstype2 = $node->get('demo3')->get('demo5')->get_css_type();
        $node->get('demo3')->get('demo5')->type = 1000;
        $csstype3 = $node->get('demo3')->get('demo5')->get_css_type();
        $csstype4 = $node->get('demo3')->get('demo6')->get_css_type();
        $this->assertSame('type_category', $csstype1);
        $this->assertSame('type_course', $csstype2);
        $this->assertSame('type_unknown', $csstype3);
        $this->assertSame('type_container', $csstype4);
    }

    public function test_node_make_active(): void {
        global $CFG;
        $node = $this->setup_node();

        $node1 = $node->add('active node 1', null, navigation_node::TYPE_CUSTOM, null, 'anode1');
        $node2 = $node->add('active node 2', new \moodle_url($CFG->wwwroot), navigation_node::TYPE_COURSE, null, 'anode2');
        $node1->make_active();
        $node->get('anode2')->make_active();
        $this->assertTrue($node1->isactive);
        $this->assertTrue($node->get('anode2')->isactive);
    }

    public function test_node_remove(): void {
        $node = $this->setup_node();

        $remove1 = $node->add('child to remove 1', null, navigation_node::TYPE_CUSTOM, null, 'remove1');
        $remove2 = $node->add('child to remove 2', null, navigation_node::TYPE_CUSTOM, null, 'remove2');
        $remove3 = $remove2->add('child to remove 3', null, navigation_node::TYPE_CUSTOM, null, 'remove3');

        $this->assertInstanceOf(navigation_node::class, $remove1);
        $this->assertInstanceOf(navigation_node::class, $remove2);
        $this->assertInstanceOf(navigation_node::class, $remove3);

        $this->assertInstanceOf(navigation_node::class, $node->get('remove1'));
        $this->assertInstanceOf(navigation_node::class, $node->get('remove2'));
        $this->assertInstanceOf(navigation_node::class, $remove2->get('remove3'));

        // Remove element and make sure this is no longer a child.
        $this->assertTrue($remove1->remove());
        $this->assertFalse($node->get('remove1'));
        $this->assertFalse(in_array('remove1', $node->get_children_key_list(), true));

        // Make sure that we can insert element after removal.
        $insertelement = navigation_node::create('extra element 4', null, navigation_node::TYPE_CUSTOM, null, 'element4');
        $node->add_node($insertelement, 'remove2');
        $this->assertNotEmpty($node->get('element4'));

        // Remove more elements.
        $this->assertTrue($node->get('remove2')->remove());
        $this->assertFalse($node->get('remove2'));

        // Make sure that we can add element after removal.
        $node->add('extra element 5', null, navigation_node::TYPE_CUSTOM, null, 'element5');
        $this->assertNotEmpty($node->get('element5'));

        $this->assertTrue($remove2->get('remove3')->remove());

        $this->assertFalse($node->get('remove1'));
        $this->assertFalse($node->get('remove2'));
    }

    public function test_node_remove_class(): void {
        $node = $this->setup_node();

        $node->add_class('testclass');
        $this->assertTrue($node->remove_class('testclass'));
        $this->assertNotContains('testclass', $node->classes);
    }


    /**
     * Test the set_force_into_more_menu method.
     *
     * @param bool $haschildren       Whether the navigation node has children nodes
     * @param bool $forceintomoremenu Whether to force the navigation node and its children into the "more" menu
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('set_force_into_more_menu_provider')]
    public function test_set_force_into_more_menu(bool $haschildren, bool $forceintomoremenu): void {
        // Create a navigation node.
        $node = new navigation_node(['text' => 'Navigation node', 'key' => 'navnode']);

        // If required, add some children nodes to the navigation node.
        if ($haschildren) {
            for ($i = 1; $i <= 3; $i++) {
                $node->add("Child navigation node {$i}");
            }
        }

        $node->set_force_into_more_menu($forceintomoremenu);
        // Assert that the expected value has been assigned to the 'forceintomoremenu' property
        // in the navigation node and its children.
        $this->assertEquals($forceintomoremenu, $node->forceintomoremenu);
        foreach ($node->children as $child) {
            $this->assertEquals($forceintomoremenu, $child->forceintomoremenu);
        }
    }

    /**
     * Data provider for the test_set_force_into_more_menu function.
     *
     * @return array
     */
    public static function set_force_into_more_menu_provider(): array {
        return [
            'Navigation node without any children nodes; Force into "more" menu => true.' => [
                false,
                true,
            ],
            'Navigation node with children nodes; Force into "more" menu => true.' => [
                true,
                true,
            ],
            'Navigation node with children nodes; Force into "more" menu => false.' => [
                true,
                false,
            ],
        ];
    }

    /**
     * Test the is_action_link method.
     *
     * @param navigation_node $node The sample navigation node
     * @param bool $expected Whether the navigation node contains an action link
     * @dataProvider is_action_link_provider
     * @covers navigation_node::is_action_link
     */
    public function test_is_action_link(navigation_node $node, bool $expected): void {
        $this->assertEquals($node->is_action_link(), $expected);
    }

    /**
     * Data provider for the test_is_action_link function.
     *
     * @return array
     */
    public static function is_action_link_provider(): array {
        return [
            'The navigation node has an action link.' =>
                [
                    navigation_node::create('Node', new action_link(
                        new \moodle_url('/'),
                        '',
                        new popup_action('click', new \moodle_url('/'))
                    ), navigation_node::TYPE_SETTING),
                    true,
                ],

            'The navigation node does not have an action link.' =>
                [
                    navigation_node::create('Node', new \moodle_url('/'), navigation_node::TYPE_SETTING),
                    false,
                ],
        ];
    }

    /**
     * Test the action_link_actions method.
     *
     * @param navigation_node $node The sample navigation node
     * @dataProvider action_link_actions_provider
     * @covers navigation_node::action_link_actions
     */
    public function test_action_link_actions(navigation_node $node): void {
        // Get the formatted array of action link actions.
        $data = $node->action_link_actions();
        // The navigation node has an action link.
        if ($node->action instanceof action_link) {
            if (!empty($node->action->actions)) { // There are actions added to the action link.
                $this->assertArrayHasKey('actions', $data);
                $this->assertCount(1, $data['actions']);
                $expected = (object)[
                    'id' => $node->action->attributes['id'],
                    'event' => $node->action->actions[0]->event,
                    'jsfunction' => $node->action->actions[0]->jsfunction,
                    'jsfunctionargs' => json_encode($node->action->actions[0]->jsfunctionargs),
                ];
                $this->assertEquals($expected, $data['actions'][0]);
            } else { // There are no actions added to the action link.
                $this->assertArrayHasKey('actions', $data);
                $this->assertEmpty($data['actions']);
            }
        } else { // The navigation node does not have an action link.
            $this->assertEmpty($data);
        }
    }

    /**
     * Data provider for the test_action_link_actions function.
     *
     * @return array
     */
    public static function action_link_actions_provider(): array {
        return [
            'The navigation node has an action link with an action attached.' =>
                [
                    navigation_node::create('Node', new action_link(
                        new \moodle_url('/'),
                        '',
                        new popup_action('click', new \moodle_url('/'))
                    ), navigation_node::TYPE_SETTING),
                ],
            'The navigation node has an action link without an action.' =>
                [
                    navigation_node::create(
                        'Node',
                        new action_link(new \moodle_url('/'), '', null),
                        navigation_node::TYPE_SETTING
                    ),
                ],
            'The navigation node does not have an action link.' =>
                [
                    navigation_node::create('Node', new \moodle_url('/'), navigation_node::TYPE_SETTING),
                ],
        ];
    }
}
