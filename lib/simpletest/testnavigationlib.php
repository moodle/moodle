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
 * Unit tests for lib/navigationlib.php
 *
 * @package   moodlecore
 * @copyright 2009 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later (5)
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}
require_once($CFG->libdir . '/navigationlib.php');

class navigation_node_test extends UnitTestCase {
    protected $tree;
    public static $includecoverage = array('./lib/navigationlib.php');
    public static $excludecoverage = array();
    protected $fakeproperties = array(
        'text' => 'text',
        'shorttext' => 'A very silly extra long short text string, more than 25 characters',
        'key' => 'key',
        'type' => 'navigation_node::TYPE_COURSE',
        'action' => 'http://www.moodle.org/');
    protected $activeurl = null;
    protected $inactivenode = null;

    public function setUp() {
        global $CFG, $PAGE;
        parent::setUp();

        $this->activeurl = $PAGE->url;
        navigation_node::override_active_url($this->activeurl);
        
        $this->inactiveurl = new moodle_url('http://www.moodle.com/');
        $this->fakeproperties['action'] = $this->inactiveurl;

        $this->node = new navigation_node('Test Node');
        $this->node->type = navigation_node::TYPE_SYSTEM;
        $this->node->add('demo1', $this->inactiveurl, navigation_node::TYPE_COURSE, null, 'demo1', new pix_icon('i/course', ''));
        $this->node->add('demo2', $this->inactiveurl, navigation_node::TYPE_COURSE, null, 'demo2', new pix_icon('i/course', ''));
        $this->node->add('demo3', $this->inactiveurl, navigation_node::TYPE_CATEGORY, null, 'demo3',new pix_icon('i/course', ''));
        $this->node->get('demo3')->add('demo4', $this->inactiveurl,navigation_node::TYPE_COURSE,  null, 'demo4', new pix_icon('i/course', ''));
        $this->node->get('demo3')->add('demo5', $this->activeurl, navigation_node::TYPE_COURSE, null, 'demo5',new pix_icon('i/course', ''));
        $this->node->get('demo3')->get('demo5')->add('activity1', null, navigation_node::TYPE_ACTIVITY, null, 'activity1');
        $this->node->get('demo3')->get('demo5')->get('activity1')->make_active();
        $this->node->add('hiddendemo1', $this->inactiveurl, navigation_node::TYPE_CATEGORY, null, 'hiddendemo1', new pix_icon('i/course', ''));
        $this->node->get('hiddendemo1')->hidden = true;
        $this->node->get('hiddendemo1')->add('hiddendemo2', $this->inactiveurl, navigation_node::TYPE_COURSE, null, 'hiddendemo2', new pix_icon('i/course', ''));
        $this->node->get('hiddendemo1')->add('hiddendemo3', $this->inactiveurl, navigation_node::TYPE_COURSE,null, 'hiddendemo3', new pix_icon('i/course', ''));
        $this->node->get('hiddendemo1')->get('hiddendemo2')->helpbutton = 'Here is a help button';
        $this->node->get('hiddendemo1')->get('hiddendemo3')->display = false;
    }

    public function test___construct() {
        global $CFG;
        $node = new navigation_node($this->fakeproperties);
        $this->assertEqual($node->text, $this->fakeproperties['text']);
        $this->assertEqual($node->title, $this->fakeproperties['text']);
        $this->assertTrue(strpos($this->fakeproperties['shorttext'], substr($node->shorttext,0, -3))===0);
        $this->assertEqual($node->key, $this->fakeproperties['key']);
        $this->assertEqual($node->type, $this->fakeproperties['type']);
        $this->assertEqual($node->action, $this->fakeproperties['action']);
    }
    public function test_add() {
        global $CFG;
        // Add a node with all args set
        $key1 = $this->node->add('test_add_1','http://www.moodle.org/',navigation_node::TYPE_COURSE,'testadd1','key',new pix_icon('i/course', ''));
        // Add a node with the minimum args required
        $key2 = $this->node->add('test_add_2',null, navigation_node::TYPE_CUSTOM,'testadd2');
        $key3 = $this->node->add(str_repeat('moodle ', 15),str_repeat('moodle', 15));
        $this->assertEqual('key:'.navigation_node::TYPE_COURSE,$key1);
        $this->assertEqual($key2, $this->node->get($key2)->key);
        $this->assertEqual($key3, $this->node->get($key3)->key);
        $this->assertIsA($this->node->get('key'), 'navigation_node');
        $this->assertIsA($this->node->get($key2), 'navigation_node');
        $this->assertIsA($this->node->get($key3), 'navigation_node');
    }

    public function test_add_class() {
        $node = $this->node->get('demo1');
        $this->assertIsA($node, 'navigation_node');
        if ($node !== false) {
            $node->add_class('myclass');
            $classes = $node->classes;
            $this->assertTrue(in_array('myclass', $classes));
        }
    }

    public function test_add_to_path() {
        global $CFG;
        $path = array('demo3','demo5');
        $key1 = $this->node->add_to_path($path,'testatp1', 'Test add to path 1', 'testatp1',  navigation_node::TYPE_COURSE, 'http://www.moodle.org/', new pix_icon('i/course', ''));
        $this->assertEqual($key1, 'testatp1:'.navigation_node::TYPE_COURSE);

        // This should generate an exception as we have not provided any text for
        // the node
        $this->expectException();
        $key3 = $this->node->add_to_path(array('demo3','dud1','dud2'), 'text', 'shorttext');
        $this->assertFalse($key3);

        // This should generate an exception as we have not provided any text for
        // the node
        $this->expectException(new coding_exception('You must set the text for the node when you create it.'));
        $key2 = $this->node->add_to_path($path);
    }

    public function test_check_if_active() {
        // First test the string urls
        // demo1 -> action is http://www.moodle.org/, thus should be true
        $this->assertTrue($this->node->get('demo3')->get('demo5')->check_if_active());
        // demo2 -> action is http://www.moodle.com/, thus should be false
        $this->assertFalse($this->node->get('demo2')->check_if_active());
    }

    public function test_contains_active_node() {
        // demo5, and activity1 were set to active during setup
        // Should be true as it contains all nodes
        $this->assertTrue($this->node->contains_active_node());
        // Should be true as demo5 is a child of demo3
        $this->assertTrue($this->node->get('demo3')->contains_active_node());
        // Obviously duff
        $this->assertFalse($this->node->get('demo1')->contains_active_node());
        // Should be true as demo5 contains activity1
        $this->assertTrue($this->node->get('demo3')->get('demo5')->contains_active_node());
        // Should be false activity1 doesnt contain the active node... it is the active node
        $this->assertFalse($this->node->get('demo3')->get('demo5')->get('activity1')->contains_active_node());
        // Obviously duff
        $this->assertFalse($this->node->get('demo3')->get('demo4')->contains_active_node());
    }

    public function test_content() {
        $this->node->get('demo3')->get('demo5')->action = null;
        $this->node->get('demo3')->get('demo5')->title('This is a title');
        $this->node->get('demo3')->get('demo5')->hidden = true;
        $this->node->get('demo3')->get('demo5')->icon = null;
        $this->node->get('demo3')->get('demo5')->helpbutton = 'A fake help button';
        $content1 = $this->node->get('demo1')->content();
        $content2 = $this->node->get('demo3')->content();
        $content3 = $this->node->get('demo3')->get('demo5')->content();
        $content4 = $this->node->get('hiddendemo1')->get('hiddendemo2')->content();
        $content5 = $this->node->get('hiddendemo1')->get('hiddendemo3')->content();
        $this->assert(new ContainsTagWithAttribute('a','href',$this->node->get('demo1')->action->out()), $content1);
        $this->assert(new ContainsTagWithAttribute('a','href',$this->node->get('demo3')->action->out()), $content2);
        $this->assert(new ContainsTagWithAttribute('span','class','dimmed_text'), $content3);
        #$this->assertEqual($content3, 'A fake help button<span class="clearhelpbutton"><span class="dimmed_text" title="This is a title">demo5</span></span>');
        $this->assert(new ContainsTagWithAttribute('a','href',$this->node->get('hiddendemo1')->get('hiddendemo2')->action->out()), $content4);
        $this->assertTrue(empty($content5));
    }

    public function test_get_active_node() {
        $node1 = $this->node->get_active_node();
        $node2 = $this->node->get('demo3')->get_active_node();
        $this->assertFalse($node1);
        $this->assertIsA($node2, 'navigation_node');
    }

    public function test_find_active_node() {
        $activenode1 = $this->node->find_active_node();
        $activenode2 = $this->node->find_active_node(navigation_node::TYPE_COURSE);
        $activenode3 = $this->node->find_active_node(navigation_node::TYPE_CATEGORY);
        $activenode4 = $this->node->get('demo1')->find_active_node(navigation_node::TYPE_COURSE);
        $this->assertIsA($activenode1, 'navigation_node');
        if ($activenode1 instanceof navigation_node) {
            $this->assertEqual($activenode1, $this->node->get('demo3')->get('demo5')->get('activity1'));
        }
        $this->assertIsA($activenode2, 'navigation_node');
        if ($activenode1 instanceof navigation_node) {
            $this->assertEqual($activenode2, $this->node->get('demo3')->get('demo5'));
        }
        $this->assertIsA($activenode3, 'navigation_node');
        if ($activenode1 instanceof navigation_node) {
            $this->assertEqual($activenode3, $this->node->get('demo3'));
        }
        $this->assertNotA($activenode4, 'navigation_node');
    }

    public function test_find_child() {
        $node1 = $this->node->find_child('demo1', navigation_node::TYPE_COURSE);
        $node2 = $this->node->find_child('demo5', navigation_node::TYPE_COURSE);
        $node3 = $this->node->find_child('demo5', navigation_node::TYPE_CATEGORY);
        $node4 = $this->node->find_child('demo0', navigation_node::TYPE_COURSE);
        $this->assertIsA($node1, 'navigation_node');
        $this->assertIsA($node2, 'navigation_node');
        $this->assertNotA($node3, 'navigation_node');
        $this->assertNotA($node4, 'navigation_node');
    }

    public function test_find_child_depth() {
        $depth1 = $this->node->find_child_depth('demo1',navigation_node::TYPE_COURSE);
        $depth2 = $this->node->find_child_depth('demo5',navigation_node::TYPE_COURSE);
        $depth3 = $this->node->find_child_depth('demo5',navigation_node::TYPE_CATEGORY);
        $depth4 = $this->node->find_child_depth('demo0',navigation_node::TYPE_COURSE);
        $this->assertEqual(1, $depth1);
        $this->assertEqual(1, $depth2);
        $this->assertFalse($depth3);
        $this->assertFalse($depth4);
    }

    public function test_find_expandable() {
        $expandable = array();
        $this->node->find_expandable($expandable);
        $this->assertEqual(count($expandable), 5);
        if (count($expandable) === 5) {
            $name = $expandable[0]['branchid'];
            $name .= $expandable[1]['branchid'];
            $name .= $expandable[2]['branchid'];
            $name .= $expandable[3]['branchid'];
            $name .= $expandable[4]['branchid'];
            $this->assertEqual($name, 'demo1:20demo2:20demo4:20hiddendemo2:20hiddendemo3:20');
        }
    }

    public function test_get() {
        $node1 = $this->node->get('demo1'); // Exists
        $node2 = $this->node->get('demo4'); // Doesn't exist for this node
        $node3 = $this->node->get('demo0'); // Doesn't exist at all
        $node4 = $this->node->get(false);   // Sometimes occurs in nature code
        $this->assertIsA($node1, 'navigation_node');
        $this->assertFalse($node2);
        $this->assertFalse($node3);
        $this->assertFalse($node4);
    }

    public function test_get_by_path() {
        $node1 = $this->node->get_by_path(array('demo3', 'demo4')); // This path exists and should return a node
        $node2 = $this->node->get_by_path(array('demo1', 'demo2')); // Both elements exist but demo2 is not a child of demo1
        $node3 = $this->node->get_by_path(array('demo0', 'demo6')); // This path is totally bogus
        $this->assertIsA($node1, 'navigation_node');
        $this->assertFalse($node2);
        $this->assertFalse($node3);
    }

    public function test_get_css_type() {
        $csstype1 = $this->node->get('demo3')->get_css_type();
        $csstype2 = $this->node->get('demo3')->get('demo5')->get_css_type();
        $this->node->get('demo3')->get('demo5')->type = 1000;
        $csstype3 = $this->node->get('demo3')->get('demo5')->get_css_type();
        $this->assertEqual($csstype1, 'type_category');
        $this->assertEqual($csstype2, 'type_course');
        $this->assertEqual($csstype3, 'type_unknown');
    }

    public function test_make_active() {
        global $CFG;
        $key1 = $this->node->add('active node 1', null, navigation_node::TYPE_CUSTOM, null, 'anode1');
        $key2 = $this->node->add('active node 2', new moodle_url($CFG->wwwroot), navigation_node::TYPE_COURSE, null, 'anode2');
        $this->node->get($key1)->make_active();
        $this->node->get($key2)->make_active();
        $this->assertTrue($this->node->get($key1)->isactive);
        $this->assertTrue($this->node->get($key2)->isactive);
    }
    public function test_remove_child() {
        $this->node->add('child to remove 1', null, navigation_node::TYPE_CUSTOM, null, 'remove1');
        $this->node->add('child to remove 2', null, navigation_node::TYPE_CUSTOM, null, 'remove2');
        $this->node->get('remove2')->add('child to remove 3', null, navigation_node::TYPE_CUSTOM, null, 'remove3');
        $this->assertIsA($this->node->get('remove1'), 'navigation_node');
        $this->assertTrue($this->node->remove_child('remove1'));
        $this->assertFalse($this->node->remove_child('remove3'));
        $this->assertFalse($this->node->remove_child('remove0'));
        $this->assertTrue($this->node->remove_child('remove2'));
    }
    public function test_remove_class() {
        $this->node->add_class('testclass');
        $this->assertTrue($this->node->remove_class('testclass'));
        $this->assertFalse(in_array('testclass', $this->node->classes));
    }
    public function test_respect_forced_open() {
        $this->node->respect_forced_open();
        $this->assertTrue($this->node->forceopen);
    }
    public function test_toggle_type_display() {
        $this->node->toggle_type_display(navigation_node::TYPE_CATEGORY);
        $this->assertFalse($this->node->get('demo1')->display);
        $this->assertFalse($this->node->get('demo3')->get('demo5')->display);
        $this->assertTrue($this->node->get('demo3')->display);
        $this->node->toggle_type_display(navigation_node::TYPE_CATEGORY, true);
    }
}

/**
 * This is a dummy object that allows us to call protected methods within the
 * global navigation class by prefixing the methods with `exposed_`
 */
class exposed_global_navigation extends global_navigation {
    protected $exposedkey = 'exposed_';
    function __construct() {
        parent::__construct();
        $this->cache = new navigation_cache('simpletest_nav');
    }
    function __call($method, $arguments) {
        if (strpos($method,$this->exposedkey) !== false) {
            $method = substr($method, strlen($this->exposedkey));
        }
        if (method_exists($this, $method)) {
            return call_user_func_array(array($this, $method), $arguments);
        }
        throw new coding_exception('You have attempted to access a method that does not exist for the given object '.$method, DEBUG_DEVELOPER);
    }
}

class mock_initialise_global_navigation extends global_navigation {

    static $count = 1;

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

    public function load_for_user() {
        $this->add('load_for_user', null, null, null, 'initcall'.self::$count);
        self::$count++;
        return 0;
    }
}

class global_navigation_test extends UnitTestCase {
    /**
     * @var global_navigation
     */
    public $node;
    protected $cache;
    protected $modinfo5 = 'O:6:"object":6:{s:8:"courseid";s:1:"5";s:6:"userid";s:1:"2";s:8:"sections";a:1:{i:0;a:1:{i:0;s:3:"288";}}s:3:"cms";a:1:{i:288;O:6:"object":17:{s:2:"id";s:3:"288";s:8:"instance";s:2:"19";s:6:"course";s:1:"5";s:7:"modname";s:5:"forum";s:4:"name";s:10:"News forum";s:7:"visible";s:1:"1";s:10:"sectionnum";s:1:"0";s:9:"groupmode";s:1:"0";s:10:"groupingid";s:1:"0";s:16:"groupmembersonly";s:1:"0";s:6:"indent";s:1:"0";s:10:"completion";s:1:"0";s:5:"extra";s:0:"";s:4:"icon";s:0:"";s:11:"uservisible";b:1;s:9:"modplural";s:6:"Forums";s:9:"available";b:1;}}s:9:"instances";a:1:{s:5:"forum";a:1:{i:19;R:8;}}s:6:"groups";N;}';
    protected $coursesections5 = 'a:5:{i:0;O:8:"stdClass":6:{s:7:"section";s:1:"0";s:2:"id";s:2:"14";s:6:"course";s:1:"5";s:7:"summary";N;s:8:"sequence";s:3:"288";s:7:"visible";s:1:"1";}i:1;O:8:"stdClass":6:{s:7:"section";s:1:"1";s:2:"id";s:2:"97";s:6:"course";s:1:"5";s:7:"summary";s:0:"";s:8:"sequence";N;s:7:"visible";s:1:"1";}i:2;O:8:"stdClass":6:{s:7:"section";s:1:"2";s:2:"id";s:2:"98";s:6:"course";s:1:"5";s:7:"summary";s:0:"";s:8:"sequence";N;s:7:"visible";s:1:"1";}i:3;O:8:"stdClass":6:{s:7:"section";s:1:"3";s:2:"id";s:2:"99";s:6:"course";s:1:"5";s:7:"summary";s:0:"";s:8:"sequence";N;s:7:"visible";s:1:"1";}i:4;O:8:"stdClass":6:{s:7:"section";s:1:"4";s:2:"id";s:3:"100";s:6:"course";s:1:"5";s:7:"summary";s:0:"";s:8:"sequence";N;s:7:"visible";s:1:"1";}}';
    public static $includecoverage = array('./lib/navigationlib.php');
    public static $excludecoverage = array();

    public function setUp() {
        $this->cache = new navigation_cache('simpletest_nav');
        $this->node = new exposed_global_navigation();
        // Create an initial tree structure to work with
        $this->node->add('category 1', null, navigation_node::TYPE_CATEGORY, null, 'cat1');
        $this->node->add('category 2', null, navigation_node::TYPE_CATEGORY, null, 'cat2');
        $this->node->add('category 3', null, navigation_node::TYPE_CATEGORY, null, 'cat3');
        $this->node->get('cat2')->add('sub category 1', null, navigation_node::TYPE_CATEGORY, null, 'sub1');
        $this->node->get('cat2')->add('sub category 2', null, navigation_node::TYPE_CATEGORY, null, 'sub2');
        $this->node->get('cat2')->add('sub category 3', null, navigation_node::TYPE_CATEGORY, null, 'sub3');
        $this->node->get('cat2')->get('sub2')->add('course 1', null, navigation_node::TYPE_COURSE, null, 'course1');
        $this->node->get('cat2')->get('sub2')->add('course 2', null, navigation_node::TYPE_COURSE, null, 'course2');
        $this->node->get('cat2')->get('sub2')->add('course 3', null, navigation_node::TYPE_COURSE, null, 'course3');
        $this->node->get('cat2')->get('sub2')->get('course2')->add('section 1', null, navigation_node::TYPE_COURSE, null, 'sec1');
        $this->node->get('cat2')->get('sub2')->get('course2')->add('section 2', null, navigation_node::TYPE_COURSE, null, 'sec2');
        $this->node->get('cat2')->get('sub2')->get('course2')->add('section 3', null, navigation_node::TYPE_COURSE, null, 'sec3');
        $this->node->get('cat2')->get('sub2')->get('course2')->get('sec2')->add('activity 1', null, navigation_node::TYPE_ACTIVITY, null, 'act1');
        $this->node->get('cat2')->get('sub2')->get('course2')->get('sec2')->add('activity 2', null, navigation_node::TYPE_ACTIVITY, null, 'act2');
        $this->node->get('cat2')->get('sub2')->get('course2')->get('sec2')->add('activity 3', null, navigation_node::TYPE_ACTIVITY, null, 'act3');
        $this->node->get('cat2')->get('sub2')->get('course2')->get('sec2')->add('resource 1', null, navigation_node::TYPE_RESOURCE, null, 'res1');
        $this->node->get('cat2')->get('sub2')->get('course2')->get('sec2')->add('resource 2', null, navigation_node::TYPE_RESOURCE, null, 'res2');
        $this->node->get('cat2')->get('sub2')->get('course2')->get('sec2')->add('resource 3', null, navigation_node::TYPE_RESOURCE, null, 'res3');

        $this->cache->clear();
        $this->cache->modinfo5 = unserialize($this->modinfo5);
        $this->cache->coursesections5 = unserialize($this->coursesections5);
        $this->cache->canviewhiddenactivities = true;
        $this->cache->canviewhiddensections = true;
        $this->cache->canviewhiddencourses = true;
        $this->node->get('cat2')->get('sub2')->add('Test Course 5', new moodle_url('http://moodle.org'),navigation_node::TYPE_COURSE,null,'5');
    }
    public function test_add_categories() {
        $categories = array();
        for ($i=0;$i<3;$i++) {
            $categories[$i] = new stdClass;
            $categories[$i]->id = 'sub4_'.$i;
            $categories[$i]->name = 'add_categories '.$i;
        }
        $this->node->exposed_add_categories(array('cat3'), $categories);
        $this->assertEqual(count($this->node->get('cat3')->children), 3);
        $this->assertIsA($this->node->get('cat3')->get('sub4_1'), 'navigation_node');
        $this->node->get('cat3')->children = array();
    }
    public function test_add_course_section_generic() {
        $keys = array('cat2', 'sub2', '5');
        $course = new stdClass;
        $course->id = '5';
        $course->numsections = 10;
        $course->modinfo = $this->modinfo5;
        $this->node->add_course_section_generic($keys, $course, 'topic', 'topic');
        $this->assertEqual(count($this->node->get_by_path($keys)->children),4);
    }
    public function test_add_category_by_path() {
        $category = new stdClass;
        $category->id = 'sub3';
        $category->name = 'Sub category 3';
        $category->path = '/cat2/sub3';
        $this->node->exposed_add_category_by_path($category);
        $this->assertIsA($this->node->get('cat2')->get('sub3'), 'navigation_node');
    }

    public function test_load_course_categories() {
        global $PAGE;
        $originalcategories = $PAGE->categories;
        $PAGE->categories = array();
        $PAGE->categories[0] = new stdClass;
        $PAGE->categories[0]->id = 130;
        $PAGE->categories[0]->name = 'category1';
        $PAGE->categories[1] = new stdClass;
        $PAGE->categories[1]->id = 131;
        $PAGE->categories[1]->name = 'category0';
        $test = new exposed_global_navigation();
        $keys = array();
        $keys[] = $test->add('base level', null, null, null, 'base');
        $catcount = $test->exposed_load_course_categories($keys);
        $this->assertIsA($test->get('base'), 'navigation_node');
        $this->assertIsA($test->get('base')->get(131), 'navigation_node');
        $this->assertIsA($test->get('base')->get(131)->get(130), 'navigation_node');
        $PAGE->categories = $originalcategories;
    }

    public function test_init() {
        global $PAGE, $SITE;
        $originalcontext = $PAGE->context;
        $test = new mock_initialise_global_navigation();
        // System
        $PAGE->context->contextlevel = CONTEXT_SYSTEM;
        $node1 = clone($test);
        $node1->initialise();
        $this->assertIsA($node1->get('initcall1'), 'navigation_node');
        if ($node1->get('initcall1')) {
            $this->assertEqual($node1->get('initcall1')->text, 'load_for_category');
        }
        // Course category
        $PAGE->context->contextlevel = CONTEXT_COURSECAT;
        $node2 = clone($test);
        $node2->initialise();
        $this->assertIsA($node2->get('initcall3'), 'navigation_node');
        if ($node2->get('initcall3')) {
            $this->assertEqual($node2->get('initcall3')->text, 'load_for_category');
        }
        $PAGE->context->contextlevel = CONTEXT_COURSE;
        // For course (we need to adjust the site id so we look like a normal course
        $SITE->id++;
        $node3 = clone($test);
        $node3->initialise();
        $this->assertIsA($node3->get('initcall5'), 'navigation_node');
        if ($node3->get('initcall5')) {
            $this->assertEqual($node3->get('initcall5')->text, 'load_for_course');
        }
        $SITE->id--;
        // Course is site
        $node4 = clone($test);
        $node4->initialise();
        $this->assertIsA($node4->get('initcall7'), 'navigation_node');
        if ($node4->get('initcall7')) {
            $this->assertEqual($node4->get('initcall7')->text, 'load_for_category');
        }
        $PAGE->context->contextlevel = CONTEXT_MODULE;
        $node5 = clone($test);
        $node5->initialise();
        $this->assertIsA($node5->get('initcall9'), 'navigation_node');
        if ($node5->get('initcall9')) {
            $this->assertEqual($node5->get('initcall9')->text, 'load_for_activity');
        }
        $PAGE->context->contextlevel = CONTEXT_BLOCK;
        $node6 = clone($test);
        $node6->initialise();
        $this->assertIsA($node6->get('initcall11'), 'navigation_node');
        if ($node6->get('initcall11')) {
            $this->assertEqual($node6->get('initcall11')->text, 'load_for_course');
        }
        $PAGE->context->contextlevel = CONTEXT_USER;
        $node7 = clone($test);
        $node7->initialise();
        $this->assertIsA($node7->get('initcall13'), 'navigation_node');
        if ($node7->get('initcall13')) {
            $this->assertEqual($node7->get('initcall13')->text, 'load_for_user');
        }
        $PAGE->context = $originalcontext;
    }

    public function test_add_courses() {
        $courses = array();
        for ($i=0;$i<5;$i++) {
            $course = new stdClass;
            $course->id = $i;
            $course->visible = true;
            $course->category = 'cat3';
            $course->fullname = "Test Course $i";
            $course->shortname = "tcourse$i";
            $course->numsections = 10;
            $course->modinfo = $this->modinfo5;
            $courses[$i] = $course;
        }

        $this->node->add_courses($courses);
        $this->assertIsA($this->node->get('cat3')->get(0), 'navigation_node');
        $this->assertIsA($this->node->get('cat3')->get(1), 'navigation_node');
        $this->assertIsA($this->node->get('cat3')->get(2), 'navigation_node');
        $this->assertIsA($this->node->get('cat3')->get(3), 'navigation_node');
        $this->assertIsA($this->node->get('cat3')->get(4), 'navigation_node');
        $this->node->get('cat3')->children = array();
    }
    public function test_can_display_type() {
        $this->node->expansionlimit = navigation_node::TYPE_COURSE;
        $this->assertTrue($this->node->exposed_can_display_type(navigation_node::TYPE_CATEGORY));
        $this->assertTrue($this->node->exposed_can_display_type(navigation_node::TYPE_COURSE));
        $this->assertFalse($this->node->exposed_can_display_type(navigation_node::TYPE_SECTION));
        $this->node->expansionlimit = null;
    }
    public function test_content() {
        $html1 = $this->node->content();
        $this->node->expansionlimit = navigation_node::TYPE_CATEGORY;
        $html2 = $this->node->content();
        $this->node->expansionlimit = null;
        $this->assert(new ContainsTagWithAttribute('a','href',$this->node->action->out()), $html1);
        $this->assert(new ContainsTagWithAttribute('a','href',$this->node->action->out()), $html2);
    }
    public function test_format_display_course_content() {
        $this->assertTrue($this->node->exposed_format_display_course_content('topic'));
        $this->assertFalse($this->node->exposed_format_display_course_content('scorm'));
        $this->assertTrue($this->node->exposed_format_display_course_content('dummy'));
    }
    public function test_load_course_activities() {
        $keys = array('cat2', 'sub2', '5');
        $course = new stdClass;
        $course->id = '5';
        $course->numsections = 10;
        $modinfo = $this->cache->modinfo5;
        $modinfo->cms[290] = clone($modinfo->cms[288]);
        $modinfo->cms[290]->id = 290;
        $modinfo->cms[290]->modname = 'resource';
        $modinfo->cms[290]->instance = 21;
        $modinfo->instances['resource'] = array();
        $modinfo->instances['resource'][21] = clone($modinfo->instances['forum'][19]);
        $modinfo->instances['resource'][21]->id = 21;
        $this->cache->modinfo5 = $modinfo;
        $course->modinfo = serialize($modinfo);
        $this->node->exposed_load_course_activities($keys, $course);

        $this->assertIsA($this->node->get_by_path(array_merge($keys, array(288))), 'navigation_node');
        $this->assertEqual($this->node->get_by_path(array_merge($keys, array(288)))->type, navigation_node::TYPE_ACTIVITY);
        $this->assertIsA($this->node->get_by_path(array_merge($keys, array(290))), 'navigation_node');
        $this->assertEqual($this->node->get_by_path(array_merge($keys, array(290)))->type, navigation_node::TYPE_ACTIVITY);
    }
    public function test_load_course_sections() {
        $keys = array('cat2', 'sub2', '5');
        $course = new stdClass;
        $course->id = '5';
        $course->format = 'topics';
        $course->numsections = 10;
        $course->modinfo = $this->modinfo5;
        $coursechildren = $this->node->get_by_path($keys)->children;

        $this->node->get_by_path(array('cat2', 'sub2', '5'))->children = array();
        $this->node->exposed_load_course_sections($keys, $course);

        $course->format = 'topics';
        $this->node->get_by_path(array('cat2', 'sub2', '5'))->children = array();
        $this->node->exposed_load_course_sections($keys, $course);

        $course->format = 'scorm';
        $this->node->get_by_path(array('cat2', 'sub2', '5'))->children = array();
        $this->node->exposed_load_course_sections($keys, $course);

        $course->format = 'sillywilly';
        $this->node->get_by_path(array('cat2', 'sub2', '5'))->children = array();
        $this->node->exposed_load_course_sections($keys, $course);

        $this->node->get_by_path($keys)->children = $coursechildren;
    }
    public function test_load_for_user() {
        $this->node->exposed_load_for_user();
    }
    public function test_load_section_activities() {
        $keys = array('cat2', 'sub2', '5');
        $course = new stdClass;
        $course->id = '5';
        $course->numsections = 10;
        $this->node->get_by_path($keys)->add('Test Section 1', null, navigation_node::TYPE_SECTION, null, $this->cache->coursesections5[1]->id);
        $modinfo = $this->cache->modinfo5;
        $modinfo->sections[1] = array(289, 290);
        $modinfo->cms[289] = clone($modinfo->cms[288]);
        $modinfo->cms[289]->id = 289;
        $modinfo->cms[289]->sectionnum = 1;
        $modinfo->cms[290]->modname = 'forum';
        $modinfo->cms[289]->instance = 20;
        $modinfo->cms[290] = clone($modinfo->cms[288]);
        $modinfo->cms[290]->id = 290;
        $modinfo->cms[290]->modname = 'resource';
        $modinfo->cms[290]->sectionnum = 1;
        $modinfo->cms[290]->instance = 21;
        $modinfo->instances['forum'][20] = clone($modinfo->instances['forum'][19]);
        $modinfo->instances['forum'][20]->id = 20;
        $modinfo->instances['resource'] = array();
        $modinfo->instances['resource'][21] = clone($modinfo->instances['forum'][19]);
        $modinfo->instances['resource'][21]->id = 21;
        $this->cache->modinfo5 = $modinfo;
        $course->modinfo = serialize($modinfo);
        $this->node->exposed_load_section_activities($keys, 1, $course);
        $keys[] = 97;
        $this->assertIsA($this->node->get_by_path(array_merge($keys, array(289))),'navigation_node');
        $this->assertEqual($this->node->get_by_path(array_merge($keys, array(289)))->type, navigation_node::TYPE_ACTIVITY);
        $this->assertIsA($this->node->get_by_path(array_merge($keys, array(290))),'navigation_node');
        $this->assertEqual($this->node->get_by_path(array_merge($keys, array(290)))->type, navigation_node::TYPE_ACTIVITY);
    }
    public function test_module_extends_navigation() {
        $this->cache->test1_extends_navigation = true;
        $this->cache->test2_extends_navigation = false;
        $this->assertTrue($this->node->exposed_module_extends_navigation('forum'));
        $this->assertTrue($this->node->exposed_module_extends_navigation('test1'));
        $this->assertFalse($this->node->exposed_module_extends_navigation('test2'));
        $this->assertFalse($this->node->exposed_module_extends_navigation('test3'));
    }
}

/**
 * This is a dummy object that allows us to call protected methods within the
 * global navigation class by prefixing the methods with `exposed_`
 */
class exposed_navbar extends navbar {
    protected $exposedkey = 'exposed_';
    function __construct() {
        global $PAGE;
        parent::__construct($PAGE);
        $this->cache = new navigation_cache('simpletest_nav');
    }
    function __call($method, $arguments) {
        if (strpos($method,$this->exposedkey) !== false) {
            $method = substr($method, strlen($this->exposedkey));
        }
        if (method_exists($this, $method)) {
            return call_user_func_array(array($this, $method), $arguments);
        }
        throw new coding_exception('You have attempted to access a method that does not exist for the given object '.$method, DEBUG_DEVELOPER);
    }
}

class navbar_test extends UnitTestCase {
    protected $node;
    protected $oldnav;

    public static $includecoverage = array('./lib/navigationlib.php');
    public static $excludecoverage = array();

    public function setUp() {
        global $PAGE;
        $this->oldnav = $PAGE->navigation;
        $this->cache = new navigation_cache('simpletest_nav');
        $this->node = new exposed_navbar();
        $temptree = new global_navigation_test();
        $temptree->setUp();
        $temptree->node->get_by_path(array('cat2','sub2', 'course2'))->make_active();
        $PAGE->navigation = $temptree->node;
    }
    public function tearDown() {
        global $PAGE;
        $PAGE->navigation = $this->oldnav;
    }
    public function test_add() {
        global $CFG;
        // Add a node with all args set
        $this->node->add('test_add_1','http://www.moodle.org/',navigation_node::TYPE_COURSE,'testadd1','testadd1',new pix_icon('i/course', ''));
        // Add a node with the minimum args required
        $key2 = $this->node->add('test_add_2');
        $this->assertIsA($this->node->get('testadd1'), 'navigation_node');
        $this->assertIsA($this->node->get('testadd1')->get($key2), 'navigation_node');
    }
    public function test_content() {
        $this->assertTrue( (strpos($this->node->content(), $this->node->action->out()) !== false) );
    }
    public function test_has_items() {
        global $PAGE;
        $this->assertTrue($this->node->has_items());
    }
    public function test_parse_branch_to_html() {
        global $CFG;
        $key = $this->node->add('test_add_1','http://www.moodle.org/',navigation_node::TYPE_COURSE,'testadd1','testadd1',new pix_icon('i/course', ''));
        $this->node->get($key)->make_active();
        $html = $this->node->exposed_parse_branch_to_html($this->node->children, true, true);
        $this->assertTrue( (strpos($html, $this->node->action->out()) !== false) );
    }
}

class navigation_cache_test extends UnitTestCase {
    protected $cache;

    public static $includecoverage = array('./lib/navigationlib.php');
    public static $excludecoverage = array();

    public function setUp() {
        $this->cache = new navigation_cache('simpletest_nav');
        $this->cache->anysetvariable = true;
    }
    public function test___get() {
        $this->assertTrue($this->cache->anysetvariable);
        $this->assertEqual($this->cache->notasetvariable, null);
    }
    public function test___set() {
        $this->cache->myname = 'Sam Hemelryk';
        $this->assertTrue($this->cache->cached('myname'));
        $this->assertEqual($this->cache->myname, 'Sam Hemelryk');
    }
    public function test_cached() {
        $this->assertTrue($this->cache->cached('anysetvariable'));
        $this->assertFalse($this->cache->cached('notasetvariable'));
    }
    public function test_clear() {
        $cache = clone($this->cache);
        $this->assertTrue($cache->cached('anysetvariable'));
        $cache->clear();
        $this->assertFalse($cache->cached('anysetvariable'));
    }
    public function test_set() {
        $this->cache->set('software', 'Moodle');
        $this->assertTrue($this->cache->cached('software'));
        $this->assertEqual($this->cache->software, 'Moodle');
    }
}

/**
 * This is a dummy object that allows us to call protected methods within the
 * global navigation class by prefixing the methods with `exposed_`
 */
class exposed_settings_navigation extends settings_navigation {
    protected $exposedkey = 'exposed_';
    function __construct() {
        global $PAGE;
        parent::__construct($PAGE);
        $this->cache = new navigation_cache('simpletest_nav');
    }
    function __call($method, $arguments) {
        if (strpos($method,$this->exposedkey) !== false) {
            $method = substr($method, strlen($this->exposedkey));
        }
        if (method_exists($this, $method)) {
            return call_user_func_array(array($this, $method), $arguments);
        }
        throw new coding_exception('You have attempted to access a method that does not exist for the given object '.$method, DEBUG_DEVELOPER);
    }
}

class settings_navigation_test extends UnitTestCase {
    protected $node;
    protected $cache;

    public static $includecoverage = array('./lib/navigationlib.php');
    public static $excludecoverage = array();

    public function setUp() {
        global $PAGE;
        $this->cache = new navigation_cache('simpletest_nav');
        $this->node = new exposed_settings_navigation();
    }
    public function test___construct() {
        $this->node = new exposed_settings_navigation();
    }
    public function test___initialise() {
        $this->node->initialise();
        $this->assertEqual($this->node->id, 'settingsnav');
    }
    public function test_load_front_page_settings() {
        $this->node->exposed_load_front_page_settings();
        $settings = false;
        foreach ($this->node->children as $child) {
            if ($child->id === 'frontpagesettings') {
                $settings = $child;
            }
        }
        $this->assertIsA($settings, 'navigation_node');
    }
    public function test_in_alternative_role() {
        $this->assertFalse($this->node->exposed_in_alternative_role());
    }
    public function test_remove_empty_root_branches() {
        $this->node->add('rootbranch1', null, navigation_node::TYPE_SETTING, null, 'rootbranch1');
        $this->node->add('rootbranch2', null, navigation_node::TYPE_SETTING, null, 'rootbranch2');
        $this->node->add('rootbranch3', null, navigation_node::TYPE_SETTING, null, 'rootbranch3');
        $this->node->get('rootbranch2')->add('something', null, navigation_node::TYPE_SETTING);
        $this->node->remove_empty_root_branches();
        $this->assertFalse($this->node->get('rootbranch1'));
        $this->assertIsA($this->node->get('rootbranch2'), 'navigation_node');
        $this->assertFalse($this->node->get('rootbranch3'));
    }
}
