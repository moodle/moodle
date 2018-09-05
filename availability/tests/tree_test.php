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

/**
 * Unit tests for the condition tree class and related logic.
 *
 * @package core_availability
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core_availability\capability_checker;
use \core_availability\tree;

defined('MOODLE_INTERNAL') || die();

/**
 * Unit tests for the condition tree class and related logic.
 *
 * @package core_availability
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tree_testcase extends \advanced_testcase {
    public function setUp() {
        // Load the mock classes so they can be used.
        require_once(__DIR__ . '/fixtures/mock_condition.php');
        require_once(__DIR__ . '/fixtures/mock_info.php');
    }

    /**
     * Tests constructing a tree with errors.
     */
    public function test_construct_errors() {
        try {
            new tree('frog');
            $this->fail();
        } catch (coding_exception $e) {
            $this->assertContains('not object', $e->getMessage());
        }
        try {
            new tree((object)array());
            $this->fail();
        } catch (coding_exception $e) {
            $this->assertContains('missing ->op', $e->getMessage());
        }
        try {
            new tree((object)array('op' => '*'));
            $this->fail();
        } catch (coding_exception $e) {
            $this->assertContains('unknown ->op', $e->getMessage());
        }
        try {
            new tree((object)array('op' => '|'));
            $this->fail();
        } catch (coding_exception $e) {
            $this->assertContains('missing ->show', $e->getMessage());
        }
        try {
            new tree((object)array('op' => '|', 'show' => 0));
            $this->fail();
        } catch (coding_exception $e) {
            $this->assertContains('->show not bool', $e->getMessage());
        }
        try {
            new tree((object)array('op' => '&'));
            $this->fail();
        } catch (coding_exception $e) {
            $this->assertContains('missing ->showc', $e->getMessage());
        }
        try {
            new tree((object)array('op' => '&', 'showc' => 0));
            $this->fail();
        } catch (coding_exception $e) {
            $this->assertContains('->showc not array', $e->getMessage());
        }
        try {
            new tree((object)array('op' => '&', 'showc' => array(0)));
            $this->fail();
        } catch (coding_exception $e) {
            $this->assertContains('->showc value not bool', $e->getMessage());
        }
        try {
            new tree((object)array('op' => '|', 'show' => true));
            $this->fail();
        } catch (coding_exception $e) {
            $this->assertContains('missing ->c', $e->getMessage());
        }
        try {
            new tree((object)array('op' => '|', 'show' => true,
                    'c' => 'side'));
            $this->fail();
        } catch (coding_exception $e) {
            $this->assertContains('->c not array', $e->getMessage());
        }
        try {
            new tree((object)array('op' => '|', 'show' => true,
                    'c' => array(3)));
            $this->fail();
        } catch (coding_exception $e) {
            $this->assertContains('child not object', $e->getMessage());
        }
        try {
            new tree((object)array('op' => '|', 'show' => true,
                    'c' => array((object)array('type' => 'doesnotexist'))));
            $this->fail();
        } catch (coding_exception $e) {
            $this->assertContains('Unknown condition type: doesnotexist', $e->getMessage());
        }
        try {
            new tree((object)array('op' => '|', 'show' => true,
                    'c' => array((object)array())));
            $this->fail();
        } catch (coding_exception $e) {
            $this->assertContains('missing ->op', $e->getMessage());
        }
        try {
            new tree((object)array('op' => '&',
                    'c' => array((object)array('op' => '&', 'c' => array())),
                    'showc' => array(true, true)
                    ));
            $this->fail();
        } catch (coding_exception $e) {
            $this->assertContains('->c, ->showc mismatch', $e->getMessage());
        }
    }

    /**
     * Tests constructing a tree with plugin that does not exist (ignored).
     */
    public function test_construct_ignore_missing_plugin() {
        // Construct a tree with & combination of one condition that doesn't exist.
        $tree = new tree(tree::get_root_json(array(
                (object)array('type' => 'doesnotexist')), tree::OP_OR), true);
        // Expected result is an empty tree with | condition, shown.
        $this->assertEquals('+|()', (string)$tree);
    }

    /**
     * Tests constructing a tree with subtrees using all available operators.
     */
    public function test_construct_just_trees() {
        $structure = tree::get_root_json(array(
                tree::get_nested_json(array(), tree::OP_OR),
                tree::get_nested_json(array(
                    tree::get_nested_json(array(), tree::OP_NOT_OR)), tree::OP_NOT_AND)),
                tree::OP_AND, array(true, true));
        $tree = new tree($structure);
        $this->assertEquals('&(+|(),+!&(!|()))', (string)$tree);
    }

    /**
     * Tests constructing tree using the mock plugin.
     */
    public function test_construct_with_mock_plugin() {
        $structure = tree::get_root_json(array(
                self::mock(array('a' => true, 'm' => ''))), tree::OP_OR);
        $tree = new tree($structure);
        $this->assertEquals('+|({mock:y,})', (string)$tree);
    }

    /**
     * Tests the check_available and get_result_information functions.
     */
    public function test_check_available() {
        global $USER;

        // Setup.
        $this->resetAfterTest();
        $info = new \core_availability\mock_info();
        $this->setAdminUser();
        $information = '';

        // No conditions.
        $structure = tree::get_root_json(array(), tree::OP_OR);
        list ($available, $information) = $this->get_available_results(
                $structure, $info, $USER->id);
        $this->assertTrue($available);

        // One condition set to yes.
        $structure->c = array(
                self::mock(array('a' => true)));
        list ($available, $information) = $this->get_available_results(
                $structure, $info, $USER->id);
        $this->assertTrue($available);

        // One condition set to no.
        $structure->c = array(
                self::mock(array('a' => false, 'm' => 'no')));
        list ($available, $information) = $this->get_available_results(
                $structure, $info, $USER->id);
        $this->assertFalse($available);
        $this->assertEquals('SA: no', $information);

        // Two conditions, OR, resolving as true.
        $structure->c = array(
                self::mock(array('a' => false, 'm' => 'no')),
                self::mock(array('a' => true)));
        list ($available, $information) = $this->get_available_results(
                $structure, $info, $USER->id);
        $this->assertTrue($available);
        $this->assertEquals('', $information);

        // Two conditions, OR, resolving as false.
        $structure->c = array(
                self::mock(array('a' => false, 'm' => 'no')),
                self::mock(array('a' => false, 'm' => 'way')));
        list ($available, $information) = $this->get_available_results(
                $structure, $info, $USER->id);
        $this->assertFalse($available);
        $this->assertRegExp('~any of.*no.*way~', $information);

        // Two conditions, OR, resolving as false, no display.
        $structure->show = false;
        list ($available, $information) = $this->get_available_results(
                $structure, $info, $USER->id);
        $this->assertFalse($available);
        $this->assertEquals('', $information);

        // Two conditions, AND, resolving as true.
        $structure->op = '&';
        unset($structure->show);
        $structure->showc = array(true, true);
        $structure->c = array(
                self::mock(array('a' => true)),
                self::mock(array('a' => true)));
        list ($available, $information) = $this->get_available_results(
                $structure, $info, $USER->id);
        $this->assertTrue($available);

        // Two conditions, AND, one false.
        $structure->c = array(
                self::mock(array('a' => false, 'm' => 'wom')),
                self::mock(array('a' => true, 'm' => '')));
        list ($available, $information) = $this->get_available_results(
                $structure, $info, $USER->id);
        $this->assertFalse($available);
        $this->assertEquals('SA: wom', $information);

        // Two conditions, AND, both false.
        $structure->c = array(
                self::mock(array('a' => false, 'm' => 'wom')),
                self::mock(array('a' => false, 'm' => 'bat')));
        list ($available, $information) = $this->get_available_results(
                $structure, $info, $USER->id);
        $this->assertFalse($available);
        $this->assertRegExp('~wom.*bat~', $information);

        // Two conditions, AND, both false, show turned off for one. When
        // show is turned off, that means if you don't have that condition
        // you don't get to see anything at all.
        $structure->showc[0] = false;
        list ($available, $information) = $this->get_available_results(
                $structure, $info, $USER->id);
        $this->assertFalse($available);
        $this->assertEquals('', $information);
        $structure->showc[0] = true;

        // Two conditions, NOT OR, both false.
        $structure->op = '!|';
        list ($available, $information) = $this->get_available_results(
                $structure, $info, $USER->id);
        $this->assertTrue($available);

        // Two conditions, NOT OR, one true.
        $structure->c[0]->a = true;
        list ($available, $information) = $this->get_available_results(
                $structure, $info, $USER->id);
        $this->assertFalse($available);
        $this->assertEquals('SA: !wom', $information);

        // Two conditions, NOT OR, both true.
        $structure->c[1]->a = true;
        list ($available, $information) = $this->get_available_results(
                $structure, $info, $USER->id);
        $this->assertFalse($available);
        $this->assertRegExp('~!wom.*!bat~', $information);

        // Two conditions, NOT AND, both true.
        $structure->op = '!&';
        unset($structure->showc);
        $structure->show = true;
        list ($available, $information) = $this->get_available_results(
                $structure, $info, $USER->id);
        $this->assertFalse($available);
        $this->assertRegExp('~any of.*!wom.*!bat~', $information);

        // Two conditions, NOT AND, one true.
        $structure->c[1]->a = false;
        list ($available, $information) = $this->get_available_results(
                $structure, $info, $USER->id);
        $this->assertTrue($available);

        // Nested NOT conditions; true.
        $structure->c = array(
                tree::get_nested_json(array(
                    self::mock(array('a' => true, 'm' => 'no'))), tree::OP_NOT_AND));
        list ($available, $information) = $this->get_available_results(
                $structure, $info, $USER->id);
        $this->assertTrue($available);

        // Nested NOT conditions; false (note no ! in message).
        $structure->c[0]->c[0]->a = false;
        list ($available, $information) = $this->get_available_results(
                $structure, $info, $USER->id);
        $this->assertFalse($available);
        $this->assertEquals('SA: no', $information);

        // Nested condition groups, message test.
        $structure->op = '|';
        $structure->c = array(
                tree::get_nested_json(array(
                    self::mock(array('a' => false, 'm' => '1')),
                    self::mock(array('a' => false, 'm' => '2'))
                    ), tree::OP_AND),
                self::mock(array('a' => false, 'm' => 3)));
        list ($available, $information) = $this->get_available_results(
                $structure, $info, $USER->id);
        $this->assertFalse($available);
        $this->assertRegExp('~<ul.*<ul.*<li.*1.*<li.*2.*</ul>.*<li.*3~', $information);
    }

    /**
     * Shortcut function to check availability and also get information.
     *
     * @param stdClass $structure Tree structure
     * @param \core_availability\info $info Location info
     * @param int $userid User id
     */
    protected function get_available_results($structure, \core_availability\info $info, $userid) {
        global $PAGE;
        $tree = new tree($structure);
        $result = $tree->check_available(false, $info, true, $userid);
        $information = $tree->get_result_information($info, $result);
        if (!is_string($information)) {
            $renderer = $PAGE->get_renderer('core', 'availability');
            $information = $renderer->render($information);
        }
        return array($result->is_available(), $information);
    }

    /**
     * Tests the is_available_for_all() function.
     */
    public function test_is_available_for_all() {
        // Empty tree is always available.
        $structure = tree::get_root_json(array(), tree::OP_OR);
        $tree = new tree($structure);
        $this->assertTrue($tree->is_available_for_all());

        // Tree with normal item in it, not always available.
        $structure->c[0] = (object)array('type' => 'mock');
        $tree = new tree($structure);
        $this->assertFalse($tree->is_available_for_all());

        // OR tree with one always-available item.
        $structure->c[1] = self::mock(array('all' => true));
        $tree = new tree($structure);
        $this->assertTrue($tree->is_available_for_all());

        // AND tree with one always-available and one not.
        $structure->op = '&';
        $structure->showc = array(true, true);
        unset($structure->show);
        $tree = new tree($structure);
        $this->assertFalse($tree->is_available_for_all());

        // Test NOT conditions (items not always-available).
        $structure->op = '!&';
        $structure->show = true;
        unset($structure->showc);
        $tree = new tree($structure);
        $this->assertFalse($tree->is_available_for_all());

        // Test again with one item always-available for NOT mode.
        $structure->c[1]->allnot = true;
        $tree = new tree($structure);
        $this->assertTrue($tree->is_available_for_all());
    }

    /**
     * Tests the get_full_information() function.
     */
    public function test_get_full_information() {
        global $PAGE;
        $renderer = $PAGE->get_renderer('core', 'availability');
        // Setup.
        $info = new \core_availability\mock_info();

        // No conditions.
        $structure = tree::get_root_json(array(), tree::OP_OR);
        $tree = new tree($structure);
        $this->assertEquals('', $tree->get_full_information($info));

        // Condition (normal and NOT).
        $structure->c = array(
                self::mock(array('m' => 'thing')));
        $tree = new tree($structure);
        $this->assertEquals('SA: [FULL]thing',
                $tree->get_full_information($info));
        $structure->op = '!&';
        $tree = new tree($structure);
        $this->assertEquals('SA: ![FULL]thing',
                $tree->get_full_information($info));

        // Complex structure.
        $structure->op = '|';
        $structure->c = array(
                tree::get_nested_json(array(
                    self::mock(array('m' => '1')),
                    self::mock(array('m' => '2'))), tree::OP_AND),
                self::mock(array('m' => 3)));
        $tree = new tree($structure);
        $this->assertRegExp('~<ul.*<ul.*<li.*1.*<li.*2.*</ul>.*<li.*3~',
                $renderer->render($tree->get_full_information($info)));

        // Test intro messages before list. First, OR message.
        $structure->c = array(
                self::mock(array('m' => '1')),
                self::mock(array('m' => '2'))
        );
        $tree = new tree($structure);
        $this->assertRegExp('~Not available unless any of:.*<ul>~',
                $renderer->render($tree->get_full_information($info)));

        // Now, OR message when not shown.
        $structure->show = false;
        $tree = new tree($structure);
        $this->assertRegExp('~hidden.*<ul>~',
                $renderer->render($tree->get_full_information($info)));

        // AND message.
        $structure->op = '&';
        unset($structure->show);
        $structure->showc = array(false, false);
        $tree = new tree($structure);
        $this->assertRegExp('~Not available unless:.*<ul>~',
                $renderer->render($tree->get_full_information($info)));

        // Hidden markers on items.
        $this->assertRegExp('~1.*hidden.*2.*hidden~',
                $renderer->render($tree->get_full_information($info)));

        // Hidden markers on child tree and items.
        $structure->c[1] = tree::get_nested_json(array(
                self::mock(array('m' => '2')),
                self::mock(array('m' => '3'))), tree::OP_AND);
        $tree = new tree($structure);
        $this->assertRegExp('~1.*hidden.*All of \(hidden.*2.*3~',
                $renderer->render($tree->get_full_information($info)));
        $structure->c[1]->op = '|';
        $tree = new tree($structure);
        $this->assertRegExp('~1.*hidden.*Any of \(hidden.*2.*3~',
                $renderer->render($tree->get_full_information($info)));

        // Hidden markers on single-item display, AND and OR.
        $structure->showc = array(false);
        $structure->c = array(
                self::mock(array('m' => '1'))
        );
        $tree = new tree($structure);
        $this->assertRegExp('~1.*hidden~',
                $tree->get_full_information($info));

        unset($structure->showc);
        $structure->show = false;
        $structure->op = '|';
        $tree = new tree($structure);
        $this->assertRegExp('~1.*hidden~',
                $tree->get_full_information($info));

        // Hidden marker if single item is tree.
        $structure->c[0] = tree::get_nested_json(array(
                self::mock(array('m' => '1')),
                self::mock(array('m' => '2'))), tree::OP_AND);
        $tree = new tree($structure);
        $this->assertRegExp('~Not available \(hidden.*1.*2~',
                $renderer->render($tree->get_full_information($info)));

        // Single item tree containing single item.
        unset($structure->c[0]->c[1]);
        $tree = new tree($structure);
        $this->assertRegExp('~SA.*1.*hidden~',
                $tree->get_full_information($info));
    }

    /**
     * Tests the is_empty() function.
     */
    public function test_is_empty() {
        // Tree with nothing in should be empty.
        $structure = tree::get_root_json(array(), tree::OP_OR);
        $tree = new tree($structure);
        $this->assertTrue($tree->is_empty());

        // Tree with something in is not empty.
        $structure = tree::get_root_json(array(self::mock(array('m' => '1'))), tree::OP_OR);
        $tree = new tree($structure);
        $this->assertFalse($tree->is_empty());
    }

    /**
     * Tests the get_all_children() function.
     */
    public function test_get_all_children() {
        // Create a tree with nothing in.
        $structure = tree::get_root_json(array(), tree::OP_OR);
        $tree1 = new tree($structure);

        // Create second tree with complex structure.
        $structure->c = array(
                tree::get_nested_json(array(
                    self::mock(array('m' => '1')),
                    self::mock(array('m' => '2'))
                ), tree::OP_OR),
                self::mock(array('m' => 3)));
        $tree2 = new tree($structure);

        // Check list of conditions from both trees.
        $this->assertEquals(array(), $tree1->get_all_children('core_availability\condition'));
        $result = $tree2->get_all_children('core_availability\condition');
        $this->assertEquals(3, count($result));
        $this->assertEquals('{mock:n,1}', (string)$result[0]);
        $this->assertEquals('{mock:n,2}', (string)$result[1]);
        $this->assertEquals('{mock:n,3}', (string)$result[2]);

        // Check specific type, should give same results.
        $result2 = $tree2->get_all_children('availability_mock\condition');
        $this->assertEquals($result, $result2);
    }

    /**
     * Tests the update_dependency_id() function.
     */
    public function test_update_dependency_id() {
        // Create tree with structure of 3 mocks.
        $structure = tree::get_root_json(array(
                tree::get_nested_json(array(
                    self::mock(array('table' => 'frogs', 'id' => 9)),
                    self::mock(array('table' => 'zombies', 'id' => 9))
                )),
                self::mock(array('table' => 'frogs', 'id' => 9))));

        // Get 'before' value.
        $tree = new tree($structure);
        $before = $tree->save();

        // Try replacing a table or id that isn't used.
        $this->assertFalse($tree->update_dependency_id('toads', 9, 13));
        $this->assertFalse($tree->update_dependency_id('frogs', 7, 8));
        $this->assertEquals($before, $tree->save());

        // Replace the zombies one.
        $this->assertTrue($tree->update_dependency_id('zombies', 9, 666));
        $after = $tree->save();
        $this->assertEquals(666, $after->c[0]->c[1]->id);

        // And the frogs one.
        $this->assertTrue($tree->update_dependency_id('frogs', 9, 3));
        $after = $tree->save();
        $this->assertEquals(3, $after->c[0]->c[0]->id);
        $this->assertEquals(3, $after->c[1]->id);
    }

    /**
     * Tests the filter_users function.
     */
    public function test_filter_users() {
        $info = new \core_availability\mock_info();
        $checker = new capability_checker($info->get_context());

        // Don't need to create real users in database, just use these ids.
        $users = array(1 => null, 2 => null, 3 => null);

        // Test basic tree with one condition that doesn't filter.
        $structure = tree::get_root_json(array(self::mock(array())));
        $tree = new tree($structure);
        $result = $tree->filter_user_list($users, false, $info, $checker);
        ksort($result);
        $this->assertEquals(array(1, 2, 3), array_keys($result));

        // Now a tree with one condition that filters.
        $structure = tree::get_root_json(array(self::mock(array('filter' => array(2, 3)))));
        $tree = new tree($structure);
        $result = $tree->filter_user_list($users, false, $info, $checker);
        ksort($result);
        $this->assertEquals(array(2, 3), array_keys($result));

        // Tree with two conditions that both filter (|).
        $structure = tree::get_root_json(array(
                self::mock(array('filter' => array(3))),
                self::mock(array('filter' => array(1)))), tree::OP_OR);
        $tree = new tree($structure);
        $result = $tree->filter_user_list($users, false, $info, $checker);
        ksort($result);
        $this->assertEquals(array(1, 3), array_keys($result));

        // Tree with OR condition one of which doesn't filter.
        $structure = tree::get_root_json(array(
                self::mock(array('filter' => array(3))),
                self::mock(array())), tree::OP_OR);
        $tree = new tree($structure);
        $result = $tree->filter_user_list($users, false, $info, $checker);
        ksort($result);
        $this->assertEquals(array(1, 2, 3), array_keys($result));

        // Tree with two condition that both filter (&).
        $structure = tree::get_root_json(array(
                self::mock(array('filter' => array(2, 3))),
                self::mock(array('filter' => array(1, 2)))));
        $tree = new tree($structure);
        $result = $tree->filter_user_list($users, false, $info, $checker);
        ksort($result);
        $this->assertEquals(array(2), array_keys($result));

        // Tree with child tree with NOT condition.
        $structure = tree::get_root_json(array(
                tree::get_nested_json(array(
                    self::mock(array('filter' => array(1)))), tree::OP_NOT_AND)));
        $tree = new tree($structure);
        $result = $tree->filter_user_list($users, false, $info, $checker);
        ksort($result);
        $this->assertEquals(array(2, 3), array_keys($result));
    }

    /**
     * Tests the get_json methods in tree (which are mainly for use in testing
     * but might be used elsewhere).
     */
    public function test_get_json() {
        // Create a simple child object (fake).
        $child = (object)array('type' => 'fake');
        $childstr = json_encode($child);

        // Minimal case.
        $this->assertEquals(
                (object)array('op' => '&', 'c' => array()),
                tree::get_nested_json(array()));
        // Children and different operator.
        $this->assertEquals(
                (object)array('op' => '|', 'c' => array($child, $child)),
                tree::get_nested_json(array($child, $child), tree::OP_OR));

        // Root empty.
        $this->assertEquals('{"op":"&","c":[],"showc":[]}',
                json_encode(tree::get_root_json(array(), tree::OP_AND)));
        // Root with children (multi-show operator).
        $this->assertEquals('{"op":"&","c":[' . $childstr . ',' . $childstr .
                    '],"showc":[true,true]}',
                json_encode(tree::get_root_json(array($child, $child), tree::OP_AND)));
        // Root with children (single-show operator).
        $this->assertEquals('{"op":"|","c":[' . $childstr . ',' . $childstr .
                    '],"show":true}',
                json_encode(tree::get_root_json(array($child, $child), tree::OP_OR)));
        // Root with children (specified show boolean).
        $this->assertEquals('{"op":"&","c":[' . $childstr . ',' . $childstr .
                    '],"showc":[false,false]}',
                json_encode(tree::get_root_json(array($child, $child), tree::OP_AND, false)));
        // Root with children (specified show array).
        $this->assertEquals('{"op":"&","c":[' . $childstr . ',' . $childstr .
                    '],"showc":[true,false]}',
                json_encode(tree::get_root_json(array($child, $child), tree::OP_AND, array(true, false))));
    }

    /**
     * Tests the behaviour of the counter in unique_sql_parameter().
     *
     * There was a problem with static counters used to implement a sequence of
     * parameter placeholders (MDL-53481). As always with static variables, it
     * is a bit tricky to unit test the behaviour reliably as it depends on the
     * actual tests executed and also their order.
     *
     * To minimise risk of false expected behaviour, this test method should be
     * first one where {@link core_availability\tree::get_user_list_sql()} is
     * used. We also use higher number of condition instances to increase the
     * risk of the counter collision, should there remain a problem.
     */
    public function test_unique_sql_parameter_behaviour() {
        global $DB;
        $this->resetAfterTest();
        $generator = $this->getDataGenerator();

        // Create a test course with multiple groupings and groups and a student in each of them.
        $course = $generator->create_course();
        $user = $generator->create_user();
        $studentroleid = $DB->get_field('role', 'id', array('shortname' => 'student'));
        $generator->enrol_user($user->id, $course->id, $studentroleid);
        // The total number of groupings and groups must not be greater than 61.
        // There is a limit in MySQL on the max number of joined tables.
        $groups = [];
        for ($i = 0; $i < 25; $i++) {
            $group = $generator->create_group(array('courseid' => $course->id));
            groups_add_member($group, $user);
            $groups[] = $group;
        }
        $groupings = [];
        for ($i = 0; $i < 25; $i++) {
            $groupings[] = $generator->create_grouping(array('courseid' => $course->id));
        }
        foreach ($groupings as $grouping) {
            foreach ($groups as $group) {
                groups_assign_grouping($grouping->id, $group->id);
            }
        }
        $info = new \core_availability\mock_info($course);

        // Make a huge tree with 'AND' of all groups and groupings conditions.
        $conditions = [];
        foreach ($groups as $group) {
            $conditions[] = \availability_group\condition::get_json($group->id);
        }
        foreach ($groupings as $groupingid) {
            $conditions[] = \availability_grouping\condition::get_json($grouping->id);
        }
        shuffle($conditions);
        $tree = new tree(tree::get_root_json($conditions));
        list($sql, $params) = $tree->get_user_list_sql(false, $info, false);
        // This must not throw exception.
        $DB->fix_sql_params($sql, $params);
    }

    /**
     * Tests get_user_list_sql.
     */
    public function test_get_user_list_sql() {
        global $DB;
        $this->resetAfterTest();
        $generator = $this->getDataGenerator();

        // Create a test course with 2 groups and users in each combination of them.
        $course = $generator->create_course();
        $group1 = $generator->create_group(array('courseid' => $course->id));
        $group2 = $generator->create_group(array('courseid' => $course->id));
        $userin1 = $generator->create_user();
        $userin2 = $generator->create_user();
        $userinboth = $generator->create_user();
        $userinneither = $generator->create_user();
        $studentroleid = $DB->get_field('role', 'id', array('shortname' => 'student'));
        foreach (array($userin1, $userin2, $userinboth, $userinneither) as $user) {
            $generator->enrol_user($user->id, $course->id, $studentroleid);
        }
        groups_add_member($group1, $userin1);
        groups_add_member($group2, $userin2);
        groups_add_member($group1, $userinboth);
        groups_add_member($group2, $userinboth);
        $info = new \core_availability\mock_info($course);

        // Tree with single group condition.
        $tree = new tree(tree::get_root_json(array(
            \availability_group\condition::get_json($group1->id)
            )));
        list($sql, $params) = $tree->get_user_list_sql(false, $info, false);
        $result = $DB->get_fieldset_sql($sql, $params);
        sort($result);
        $this->assertEquals(array($userin1->id, $userinboth->id), $result);

        // Tree with 'AND' of both group conditions.
        $tree = new tree(tree::get_root_json(array(
            \availability_group\condition::get_json($group1->id),
            \availability_group\condition::get_json($group2->id)
        )));
        list($sql, $params) = $tree->get_user_list_sql(false, $info, false);
        $result = $DB->get_fieldset_sql($sql, $params);
        sort($result);
        $this->assertEquals(array($userinboth->id), $result);

        // Tree with 'AND' of both group conditions.
        $tree = new tree(tree::get_root_json(array(
            \availability_group\condition::get_json($group1->id),
            \availability_group\condition::get_json($group2->id)
        ), tree::OP_OR));
        list($sql, $params) = $tree->get_user_list_sql(false, $info, false);
        $result = $DB->get_fieldset_sql($sql, $params);
        sort($result);
        $this->assertEquals(array($userin1->id, $userin2->id, $userinboth->id), $result);

        // Check with flipped logic (NOT above level of tree).
        list($sql, $params) = $tree->get_user_list_sql(true, $info, false);
        $result = $DB->get_fieldset_sql($sql, $params);
        sort($result);
        $this->assertEquals(array($userinneither->id), $result);

        // Tree with 'OR' of group conditions and a non-filtering condition.
        // The non-filtering condition should mean that ALL users are included.
        $tree = new tree(tree::get_root_json(array(
            \availability_group\condition::get_json($group1->id),
            \availability_date\condition::get_json(\availability_date\condition::DIRECTION_UNTIL, 3)
        ), tree::OP_OR));
        list($sql, $params) = $tree->get_user_list_sql(false, $info, false);
        $this->assertEquals('', $sql);
        $this->assertEquals(array(), $params);
    }

    /**
     * Utility function to build the PHP structure representing a mock condition.
     *
     * @param array $params Mock parameters
     * @return \stdClass Structure object
     */
    protected static function mock(array $params) {
        $params['type'] = 'mock';
        return (object)$params;
    }
}
