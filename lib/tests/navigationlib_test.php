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

namespace core;

use action_link;
use global_navigation;
use navbar;
use navigation_cache;
use navigation_node;
use navigation_node_collection;
use pix_icon;
use popup_action;
use settings_navigation;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/navigationlib.php');

/**
 * Unit tests for lib/navigationlib.php
 *
 * @package   core
 * @category  test
 * @copyright 2009 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later (5)
 */
class navigationlib_test extends \advanced_testcase {
    /**
     * @var navigation_node
     */
    public $node;

    protected function setup_node() {
        global $PAGE, $SITE;

        $PAGE->set_url('/');
        $PAGE->set_course($SITE);

        $activeurl = $PAGE->url;
        $inactiveurl = new \moodle_url('http://www.moodle.com/');

        navigation_node::override_active_url($PAGE->url);

        $this->node = new navigation_node('Test Node');
        $this->node->type = navigation_node::TYPE_SYSTEM;
        // We add the first child without key. This way we make sure all keys search by comparison is performed using ===.
        $this->node->add('first child without key', null, navigation_node::TYPE_CUSTOM);
        $demo1 = $this->node->add('demo1', $inactiveurl, navigation_node::TYPE_COURSE, null, 'demo1', new pix_icon('i/course', ''));
        $demo2 = $this->node->add('demo2', $inactiveurl, navigation_node::TYPE_COURSE, null, 'demo2', new pix_icon('i/course', ''));
        $demo3 = $this->node->add('demo3', $inactiveurl, navigation_node::TYPE_CATEGORY, null, 'demo3', new pix_icon('i/course', ''));
        $demo4 = $demo3->add('demo4', $inactiveurl, navigation_node::TYPE_COURSE,  null, 'demo4', new pix_icon('i/course', ''));
        $demo5 = $demo3->add('demo5', $activeurl, navigation_node::TYPE_COURSE, null, 'demo5', new pix_icon('i/course', ''));
        $demo5->add('activity1', null, navigation_node::TYPE_ACTIVITY, null, 'activity1')->make_active();
        $demo6 = $demo3->add('demo6', null, navigation_node::TYPE_CONTAINER, 'container node test', 'demo6');
        $hiddendemo1 = $this->node->add('hiddendemo1', $inactiveurl, navigation_node::TYPE_CATEGORY, null, 'hiddendemo1', new pix_icon('i/course', ''));
        $hiddendemo1->hidden = true;
        $hiddendemo1->add('hiddendemo2', $inactiveurl, navigation_node::TYPE_COURSE, null, 'hiddendemo2', new pix_icon('i/course', ''))->helpbutton = 'Here is a help button';
        $hiddendemo1->add('hiddendemo3', $inactiveurl, navigation_node::TYPE_COURSE, null, 'hiddendemo3', new pix_icon('i/course', ''))->display = false;
    }

    public function test_node__construct() {
        $this->setup_node();

        $fakeproperties = array(
            'text' => 'text',
            'shorttext' => 'A very silly extra long short text string, more than 25 characters',
            'key' => 'key',
            'type' => 'navigation_node::TYPE_COURSE',
            'action' => new \moodle_url('http://www.moodle.org/'));

        $node = new navigation_node($fakeproperties);
        $this->assertSame($fakeproperties['text'], $node->text);
        $this->assertTrue(strpos($fakeproperties['shorttext'], substr($node->shorttext, 0, -3)) === 0);
        $this->assertSame($fakeproperties['key'], $node->key);
        $this->assertSame($fakeproperties['type'], $node->type);
        $this->assertSame($fakeproperties['action'], $node->action);
    }

    public function test_node_add() {
        $this->setup_node();

        // Add a node with all args set.
        $node1 = $this->node->add('test_add_1', 'http://www.moodle.org/', navigation_node::TYPE_COURSE, 'testadd1', 'key', new pix_icon('i/course', ''));
        // Add a node with the minimum args required.
        $node2 = $this->node->add('test_add_2', null, navigation_node::TYPE_CUSTOM, 'testadd2');
        $node3 = $this->node->add(str_repeat('moodle ', 15), str_repeat('moodle', 15));

        $this->assertInstanceOf('navigation_node', $node1);
        $this->assertInstanceOf('navigation_node', $node2);
        $this->assertInstanceOf('navigation_node', $node3);

        $ref = $this->node->get('key');
        $this->assertSame($node1, $ref);

        $ref = $this->node->get($node2->key);
        $this->assertSame($node2, $ref);

        $ref = $this->node->get($node2->key, $node2->type);
        $this->assertSame($node2, $ref);

        $ref = $this->node->get($node3->key, $node3->type);
        $this->assertSame($node3, $ref);
    }

    public function test_node_add_before() {
        $this->setup_node();

        // Create 3 nodes.
        $node1 = navigation_node::create('test_add_1', null, navigation_node::TYPE_CUSTOM,
            'test 1', 'testadd1');
        $node2 = navigation_node::create('test_add_2', null, navigation_node::TYPE_CUSTOM,
            'test 2', 'testadd2');
        $node3 = navigation_node::create('test_add_3', null, navigation_node::TYPE_CUSTOM,
            'test 3', 'testadd3');
        // Add node 2, then node 1 before 2, then node 3 at end.
        $this->node->add_node($node2);
        $this->node->add_node($node1, 'testadd2');
        $this->node->add_node($node3);
        // Check the last 3 nodes are in 1, 2, 3 order and have those indexes.
        foreach ($this->node->children as $child) {
            $keys[] = $child->key;
        }
        $this->assertSame('testadd1', $keys[count($keys)-3]);
        $this->assertSame('testadd2', $keys[count($keys)-2]);
        $this->assertSame('testadd3', $keys[count($keys)-1]);
    }

    public function test_node_add_class() {
        $this->setup_node();

        $node = $this->node->get('demo1');
        $this->assertInstanceOf('navigation_node', $node);
        if ($node !== false) {
            $node->add_class('myclass');
            $classes = $node->classes;
            $this->assertContains('myclass', $classes);
        }
    }

    public function test_node_check_if_active() {
        $this->setup_node();

        // First test the string urls
        // Demo1 -> action is http://www.moodle.org/, thus should be true.
        $demo5 = $this->node->find('demo5', navigation_node::TYPE_COURSE);
        if ($this->assertInstanceOf('navigation_node', $demo5)) {
            $this->assertTrue($demo5->check_if_active());
        }

        // Demo2 -> action is http://www.moodle.com/, thus should be false.
        $demo2 = $this->node->get('demo2');
        if ($this->assertInstanceOf('navigation_node', $demo2)) {
            $this->assertFalse($demo2->check_if_active());
        }
    }

    public function test_node_contains_active_node() {
        $this->setup_node();

        // Demo5, and activity1 were set to active during setup.
        // Should be true as it contains all nodes.
        $this->assertTrue($this->node->contains_active_node());
        // Should be true as demo5 is a child of demo3.
        $this->assertTrue($this->node->get('demo3')->contains_active_node());
        // Obviously duff.
        $this->assertFalse($this->node->get('demo1')->contains_active_node());
        // Should be true as demo5 contains activity1.
        $this->assertTrue($this->node->get('demo3')->get('demo5')->contains_active_node());
        // Should be true activity1 is the active node.
        $this->assertTrue($this->node->get('demo3')->get('demo5')->get('activity1')->contains_active_node());
        // Obviously duff.
        $this->assertFalse($this->node->get('demo3')->get('demo4')->contains_active_node());
    }

    public function test_node_find_active_node() {
        $this->setup_node();

        $activenode1 = $this->node->find_active_node();
        $activenode2 = $this->node->get('demo1')->find_active_node();

        if ($this->assertInstanceOf('navigation_node', $activenode1)) {
            $ref = $this->node->get('demo3')->get('demo5')->get('activity1');
            $this->assertSame($activenode1, $ref);
        }

        $this->assertNotInstanceOf('navigation_node', $activenode2);
    }

    public function test_node_find() {
        $this->setup_node();

        $node1 = $this->node->find('demo1', navigation_node::TYPE_COURSE);
        $node2 = $this->node->find('demo5', navigation_node::TYPE_COURSE);
        $node3 = $this->node->find('demo5', navigation_node::TYPE_CATEGORY);
        $node4 = $this->node->find('demo0', navigation_node::TYPE_COURSE);
        $this->assertInstanceOf('navigation_node', $node1);
        $this->assertInstanceOf('navigation_node', $node2);
        $this->assertNotInstanceOf('navigation_node', $node3);
        $this->assertNotInstanceOf('navigation_node', $node4);
    }

    public function test_node_find_expandable() {
        $this->setup_node();

        $expandable = array();
        $this->node->find_expandable($expandable);

        $this->assertCount(0, $expandable);
        if (count($expandable) === 4) {
            $name = $expandable[0]['key'];
            $name .= $expandable[1]['key'];
            $name .= $expandable[2]['key'];
            $name .= $expandable[3]['key'];
            $this->assertSame('demo1demo2demo4hiddendemo2', $name);
        }
    }

    public function test_node_get() {
        $this->setup_node();

        $node1 = $this->node->get('demo1'); // Exists.
        $node2 = $this->node->get('demo4'); // Doesn't exist for this node.
        $node3 = $this->node->get('demo0'); // Doesn't exist at all.
        $node4 = $this->node->get(false);   // Sometimes occurs in nature code.
        $this->assertInstanceOf('navigation_node', $node1);
        $this->assertFalse($node2);
        $this->assertFalse($node3);
        $this->assertFalse($node4);
    }

    public function test_node_get_css_type() {
        $this->setup_node();

        $csstype1 = $this->node->get('demo3')->get_css_type();
        $csstype2 = $this->node->get('demo3')->get('demo5')->get_css_type();
        $this->node->get('demo3')->get('demo5')->type = 1000;
        $csstype3 = $this->node->get('demo3')->get('demo5')->get_css_type();
        $csstype4 = $this->node->get('demo3')->get('demo6')->get_css_type();
        $this->assertSame('type_category', $csstype1);
        $this->assertSame('type_course', $csstype2);
        $this->assertSame('type_unknown', $csstype3);
        $this->assertSame('type_container', $csstype4);
    }

    public function test_node_make_active() {
        global $CFG;
        $this->setup_node();

        $node1 = $this->node->add('active node 1', null, navigation_node::TYPE_CUSTOM, null, 'anode1');
        $node2 = $this->node->add('active node 2', new \moodle_url($CFG->wwwroot), navigation_node::TYPE_COURSE, null, 'anode2');
        $node1->make_active();
        $this->node->get('anode2')->make_active();
        $this->assertTrue($node1->isactive);
        $this->assertTrue($this->node->get('anode2')->isactive);
    }

    public function test_node_remove() {
        $this->setup_node();

        $remove1 = $this->node->add('child to remove 1', null, navigation_node::TYPE_CUSTOM, null, 'remove1');
        $remove2 = $this->node->add('child to remove 2', null, navigation_node::TYPE_CUSTOM, null, 'remove2');
        $remove3 = $remove2->add('child to remove 3', null, navigation_node::TYPE_CUSTOM, null, 'remove3');

        $this->assertInstanceOf('navigation_node', $remove1);
        $this->assertInstanceOf('navigation_node', $remove2);
        $this->assertInstanceOf('navigation_node', $remove3);

        $this->assertInstanceOf('navigation_node', $this->node->get('remove1'));
        $this->assertInstanceOf('navigation_node', $this->node->get('remove2'));
        $this->assertInstanceOf('navigation_node', $remove2->get('remove3'));

        // Remove element and make sure this is no longer a child.
        $this->assertTrue($remove1->remove());
        $this->assertFalse($this->node->get('remove1'));
        $this->assertFalse(in_array('remove1', $this->node->get_children_key_list(), true));

        // Make sure that we can insert element after removal.
        $insertelement = navigation_node::create('extra element 4', null, navigation_node::TYPE_CUSTOM, null, 'element4');
        $this->node->add_node($insertelement, 'remove2');
        $this->assertNotEmpty($this->node->get('element4'));

        // Remove more elements.
        $this->assertTrue($this->node->get('remove2')->remove());
        $this->assertFalse($this->node->get('remove2'));

        // Make sure that we can add element after removal.
        $this->node->add('extra element 5', null, navigation_node::TYPE_CUSTOM, null, 'element5');
        $this->assertNotEmpty($this->node->get('element5'));

        $this->assertTrue($remove2->get('remove3')->remove());

        $this->assertFalse($this->node->get('remove1'));
        $this->assertFalse($this->node->get('remove2'));
    }

    public function test_node_remove_class() {
        $this->setup_node();

        $this->node->add_class('testclass');
        $this->assertTrue($this->node->remove_class('testclass'));
        $this->assertNotContains('testclass', $this->node->classes);
    }

    public function test_module_extends_navigation() {
        $node = new exposed_global_navigation();
        // Create an initial tree structure to work with.
        $cat1 = $node->add('category 1', null, navigation_node::TYPE_CATEGORY, null, 'cat1');
        $cat2 = $node->add('category 2', null, navigation_node::TYPE_CATEGORY, null, 'cat2');
        $cat3 = $node->add('category 3', null, navigation_node::TYPE_CATEGORY, null, 'cat3');
        $sub1 = $cat2->add('sub category 1', null, navigation_node::TYPE_CATEGORY, null, 'sub1');
        $sub2 = $cat2->add('sub category 2', null, navigation_node::TYPE_CATEGORY, null, 'sub2');
        $sub3 = $cat2->add('sub category 3', null, navigation_node::TYPE_CATEGORY, null, 'sub3');
        $course1 = $sub2->add('course 1', null, navigation_node::TYPE_COURSE, null, 'course1');
        $course2 = $sub2->add('course 2', null, navigation_node::TYPE_COURSE, null, 'course2');
        $course3 = $sub2->add('course 3', null, navigation_node::TYPE_COURSE, null, 'course3');
        $section1 = $course2->add('section 1', null, navigation_node::TYPE_SECTION, null, 'sec1');
        $section2 = $course2->add('section 2', null, navigation_node::TYPE_SECTION, null, 'sec2');
        $section3 = $course2->add('section 3', null, navigation_node::TYPE_SECTION, null, 'sec3');
        $act1 = $section2->add('activity 1', null, navigation_node::TYPE_ACTIVITY, null, 'act1');
        $act2 = $section2->add('activity 2', null, navigation_node::TYPE_ACTIVITY, null, 'act2');
        $act3 = $section2->add('activity 3', null, navigation_node::TYPE_ACTIVITY, null, 'act3');
        $res1 = $section2->add('resource 1', null, navigation_node::TYPE_RESOURCE, null, 'res1');
        $res2 = $section2->add('resource 2', null, navigation_node::TYPE_RESOURCE, null, 'res2');
        $res3 = $section2->add('resource 3', null, navigation_node::TYPE_RESOURCE, null, 'res3');

        $this->assertTrue($node->exposed_module_extends_navigation('data'));
        $this->assertFalse($node->exposed_module_extends_navigation('test1'));
    }

    public function test_navbar_prepend_and_add() {
        global $PAGE;
        // Unfortunate hack needed because people use global $PAGE around the place.
        $PAGE->set_url('/');

        // We need to reset after this test because we using the generator.
        $this->resetAfterTest();

        $generator = self::getDataGenerator();
        $cat1 = $generator->create_category();
        $cat2 = $generator->create_category(array('parent' => $cat1->id));
        $course = $generator->create_course(array('category' => $cat2->id));

        $page = new \moodle_page();
        $page->set_course($course);
        $page->set_url(new \moodle_url('/course/view.php', array('id' => $course->id)));
        $page->navbar->prepend('test 1');
        $page->navbar->prepend('test 2');
        $page->navbar->add('test 3');
        $page->navbar->add('test 4');

        $items = $page->navbar->get_items();
        foreach ($items as $item) {
            $this->assertInstanceOf('navigation_node', $item);
        }

        $i = 0;
        $this->assertSame('test 1', $items[$i++]->text);
        $this->assertSame('test 2', $items[$i++]->text);
        $this->assertSame('home', $items[$i++]->key);
        $this->assertSame('courses', $items[$i++]->key);
        $this->assertSame($cat1->id, $items[$i++]->key);
        $this->assertSame($cat2->id, $items[$i++]->key);
        $this->assertSame($course->id, $items[$i++]->key);
        $this->assertSame('test 3', $items[$i++]->text);
        $this->assertSame('test 4', $items[$i++]->text);

        return $page;
    }

    /**
     * @depends test_navbar_prepend_and_add
     * @param $node
     */
    public function test_navbar_has_items(\moodle_page $page) {
        $this->resetAfterTest();

        $this->assertTrue($page->navbar->has_items());
    }

    public function test_cache__get() {
        $cache = new navigation_cache('unittest_nav');
        $cache->anysetvariable = true;

        $this->assertTrue($cache->anysetvariable);
        $this->assertEquals($cache->notasetvariable, null);
    }

    public function test_cache__set() {
        $cache = new navigation_cache('unittest_nav');
        $cache->anysetvariable = true;

        $cache->myname = 'Sam Hemelryk';
        $this->assertTrue($cache->cached('myname'));
        $this->assertSame('Sam Hemelryk', $cache->myname);
    }

    public function test_cache_cached() {
        $cache = new navigation_cache('unittest_nav');
        $cache->anysetvariable = true;

        $this->assertTrue($cache->cached('anysetvariable'));
        $this->assertFalse($cache->cached('notasetvariable'));
    }

    public function test_cache_clear() {
        $cache = new navigation_cache('unittest_nav');
        $cache->anysetvariable = true;

        $cache = clone($cache);
        $this->assertTrue($cache->cached('anysetvariable'));
        $cache->clear();
        $this->assertFalse($cache->cached('anysetvariable'));
    }

    public function test_cache_set() {
        $cache = new navigation_cache('unittest_nav');
        $cache->anysetvariable = true;

        $cache->set('software', 'Moodle');
        $this->assertTrue($cache->cached('software'));
        $this->assertEquals($cache->software, 'Moodle');
    }

    public function test_setting___construct() {
        global $PAGE, $SITE;

        $this->resetAfterTest(false);

        $PAGE->set_url('/');
        $PAGE->set_course($SITE);

        $node = new exposed_settings_navigation();

        return $node;
    }

    /**
     * @depends test_setting___construct
     * @param mixed $node
     * @return mixed
     */
    public function test_setting__initialise($node) {
        $this->resetAfterTest(false);

        $node->initialise();
        $this->assertEquals($node->id, 'settingsnav');

        return $node;
    }

    /**
     * Test that users with the correct permissions can view the preferences page.
     */
    public function test_can_view_user_preferences() {
        global $PAGE, $DB, $SITE;
        $this->resetAfterTest();

        $persontoview = $this->getDataGenerator()->create_user();
        $persondoingtheviewing = $this->getDataGenerator()->create_user();

        $PAGE->set_url('/');
        $PAGE->set_course($SITE);

        // Check that a standard user can not view the preferences page.
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $this->getDataGenerator()->role_assign($studentrole->id, $persondoingtheviewing->id);
        $this->setUser($persondoingtheviewing);
        $settingsnav = new exposed_settings_navigation();
        $settingsnav->initialise();
        $settingsnav->extend_for_user($persontoview->id);
        $this->assertFalse($settingsnav->can_view_user_preferences($persontoview->id));

        // Set persondoingtheviewing as a manager.
        $managerrole = $DB->get_record('role', array('shortname' => 'manager'));
        $this->getDataGenerator()->role_assign($managerrole->id, $persondoingtheviewing->id);
        $settingsnav = new exposed_settings_navigation();
        $settingsnav->initialise();
        $settingsnav->extend_for_user($persontoview->id);
        $this->assertTrue($settingsnav->can_view_user_preferences($persontoview->id));

        // Check that the admin can view the preferences page.
        $this->setAdminUser();
        $settingsnav = new exposed_settings_navigation();
        $settingsnav->initialise();
        $settingsnav->extend_for_user($persontoview->id);
        $preferencenode = $settingsnav->find('userviewingsettings' . $persontoview->id, null);
        $this->assertTrue($settingsnav->can_view_user_preferences($persontoview->id));
    }

    /**
     * @depends test_setting__initialise
     * @param mixed $node
     * @return mixed
     */
    public function test_setting_in_alternative_role($node) {
        $this->resetAfterTest();

        $this->assertFalse($node->exposed_in_alternative_role());
    }


    public function test_navigation_node_collection_remove_with_no_type() {
        $navigationnodecollection = new navigation_node_collection();
        $this->setup_node();
        $this->node->key = 100;

        // Test it's empty
        $this->assertEquals(0, count($navigationnodecollection->get_key_list()));

        // Add a node
        $navigationnodecollection->add($this->node);

        // Test it's not empty
        $this->assertEquals(1, count($navigationnodecollection->get_key_list()));

        // Remove a node - passing key only!
        $this->assertTrue($navigationnodecollection->remove(100));

        // Test it's empty again!
        $this->assertEquals(0, count($navigationnodecollection->get_key_list()));
    }

    public function test_navigation_node_collection_remove_with_type() {
        $navigationnodecollection = new navigation_node_collection();
        $this->setup_node();
        $this->node->key = 100;

        // Test it's empty
        $this->assertEquals(0, count($navigationnodecollection->get_key_list()));

        // Add a node
        $navigationnodecollection->add($this->node);

        // Test it's not empty
        $this->assertEquals(1, count($navigationnodecollection->get_key_list()));

        // Remove a node - passing type
        $this->assertTrue($navigationnodecollection->remove(100, 1));

        // Test it's empty again!
        $this->assertEquals(0, count($navigationnodecollection->get_key_list()));
    }

    /**
     * Test the set_force_into_more_menu method.
     *
     * @param bool $haschildren       Whether the navigation node has children nodes
     * @param bool $forceintomoremenu Whether to force the navigation node and its children into the "more" menu
     * @dataProvider set_force_into_more_menu_provider
     */
    public function test_set_force_into_more_menu(bool $haschildren, bool $forceintomoremenu) {
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
    public function set_force_into_more_menu_provider(): array {
        return [
            'Navigation node without any children nodes; Force into "more" menu => true.' =>
                [
                    false,
                    true,
                ],
            'Navigation node with children nodes; Force into "more" menu => true.' =>
                [
                    true,
                    true,
                ],
            'Navigation node with children nodes; Force into "more" menu => false.' =>
                [
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
    public function test_is_action_link(navigation_node $node, bool $expected) {
        $this->assertEquals($node->is_action_link(), $expected);
    }

    /**
     * Data provider for the test_is_action_link function.
     *
     * @return array
     */
    public function is_action_link_provider(): array {
        return [
            'The navigation node has an action link.' =>
                [
                    navigation_node::create('Node', new action_link(new \moodle_url('/'), '',
                        new popup_action('click', new \moodle_url('/'))), navigation_node::TYPE_SETTING),
                    true
                ],

            'The navigation node does not have an action link.' =>
                [
                    navigation_node::create('Node', new \moodle_url('/'), navigation_node::TYPE_SETTING),
                    false
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
    public function test_action_link_actions(navigation_node $node) {
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
                    'jsfunctionargs' => json_encode($node->action->actions[0]->jsfunctionargs)
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
    public function action_link_actions_provider(): array {
        return [
            'The navigation node has an action link with an action attached.' =>
                [
                    navigation_node::create('Node', new action_link(new \moodle_url('/'), '',
                        new popup_action('click', new \moodle_url('/'))), navigation_node::TYPE_SETTING),
                ],
            'The navigation node has an action link without an action.' =>
                [
                    navigation_node::create('Node', new action_link(new \moodle_url('/'), '', null),
                        navigation_node::TYPE_SETTING),
                ],
            'The navigation node does not have an action link.' =>
                [
                    navigation_node::create('Node', new \moodle_url('/'), navigation_node::TYPE_SETTING),
                ],
        ];
    }
}


/**
 * This is a dummy object that allows us to call protected methods within the
 * global navigation class by prefixing the methods with `exposed_`
 */
class exposed_global_navigation extends global_navigation {
    protected $exposedkey = 'exposed_';
    public function __construct(\moodle_page $page=null) {
        global $PAGE;
        if ($page === null) {
            $page = $PAGE;
        }
        parent::__construct($page);
        $this->cache = new navigation_cache('unittest_nav');
    }
    public function __call($method, $arguments) {
        if (strpos($method, $this->exposedkey) !== false) {
            $method = substr($method, strlen($this->exposedkey));
        }
        if (method_exists($this, $method)) {
            return call_user_func_array(array($this, $method), $arguments);
        }
        throw new \coding_exception('You have attempted to access a method that does not exist for the given object '.$method, DEBUG_DEVELOPER);
    }
    public function set_initialised() {
        $this->initialised = true;
    }
}


class mock_initialise_global_navigation extends global_navigation {

    protected static $count = 1;

    public function load_for_category() {
        $this->add('load_for_category', null, null, null, 'initcall'.self::$count);
        self::$count++;
        return 0;
    }

    public function load_for_course() {
        $this->add('load_for_course', null, null, null, 'initcall'.self::$count);
        self::$count++;
        return 0;
    }

    public function load_for_activity() {
        $this->add('load_for_activity', null, null, null, 'initcall'.self::$count);
        self::$count++;
        return 0;
    }

    public function load_for_user($user=null, $forceforcontext=false) {
        $this->add('load_for_user', null, null, null, 'initcall'.self::$count);
        self::$count++;
        return 0;
    }
}

/**
 * This is a dummy object that allows us to call protected methods within the
 * global navigation class by prefixing the methods with `exposed_`.
 */
class exposed_navbar extends navbar {
    protected $exposedkey = 'exposed_';
    public function __construct(\moodle_page $page) {
        parent::__construct($page);
        $this->cache = new navigation_cache('unittest_nav');
    }
    public function __call($method, $arguments) {
        if (strpos($method, $this->exposedkey) !== false) {
            $method = substr($method, strlen($this->exposedkey));
        }
        if (method_exists($this, $method)) {
            return call_user_func_array(array($this, $method), $arguments);
        }
        throw new \coding_exception('You have attempted to access a method that does not exist for the given object '.$method, DEBUG_DEVELOPER);
    }
}

class navigation_exposed_moodle_page extends \moodle_page {
    public function set_navigation(navigation_node $node) {
        $this->_navigation = $node;
    }
}

/**
 * This is a dummy object that allows us to call protected methods within the
 * global navigation class by prefixing the methods with `exposed_`.
 */
class exposed_settings_navigation extends settings_navigation {
    protected $exposedkey = 'exposed_';
    public function __construct() {
        global $PAGE;
        parent::__construct($PAGE);
        $this->cache = new navigation_cache('unittest_nav');
    }
    public function __call($method, $arguments) {
        if (strpos($method, $this->exposedkey) !== false) {
            $method = substr($method, strlen($this->exposedkey));
        }
        if (method_exists($this, $method)) {
            return call_user_func_array(array($this, $method), $arguments);
        }
        throw new \coding_exception('You have attempted to access a method that does not exist for the given object '.$method, DEBUG_DEVELOPER);
    }
}
