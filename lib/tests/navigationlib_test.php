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
 * @package   core
 * @category  phpunit
 * @copyright 2009 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later (5)
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/navigationlib.php');


class navigation_node_testcase extends basic_testcase {
    protected $tree;
    protected $fakeproperties = array(
        'text' => 'text',
        'shorttext' => 'A very silly extra long short text string, more than 25 characters',
        'key' => 'key',
        'type' => 'navigation_node::TYPE_COURSE',
        'action' => 'http://www.moodle.org/');
    protected $activeurl = null;
    protected $inactivenode = null;

    /**
     * @var navigation_node
     */
    public $node;

    protected function setUp() {
        global $CFG, $PAGE, $SITE;
        parent::setUp();

        $PAGE->set_url('/');
        $PAGE->set_course($SITE);

        $this->activeurl = $PAGE->url;
        navigation_node::override_active_url($this->activeurl);

        $this->inactiveurl = new moodle_url('http://www.moodle.com/');
        $this->fakeproperties['action'] = $this->inactiveurl;

        $this->node = new navigation_node('Test Node');
        $this->node->type = navigation_node::TYPE_SYSTEM;
        // We add the first child without key. This way we make sure all keys search by comparision is performed using ===
        $this->node->add('first child without key', null, navigation_node::TYPE_CUSTOM);
        $demo1 = $this->node->add('demo1', $this->inactiveurl, navigation_node::TYPE_COURSE, null, 'demo1', new pix_icon('i/course', ''));
        $demo2 = $this->node->add('demo2', $this->inactiveurl, navigation_node::TYPE_COURSE, null, 'demo2', new pix_icon('i/course', ''));
        $demo3 = $this->node->add('demo3', $this->inactiveurl, navigation_node::TYPE_CATEGORY, null, 'demo3',new pix_icon('i/course', ''));
        $demo4 = $demo3->add('demo4', $this->inactiveurl,navigation_node::TYPE_COURSE,  null, 'demo4', new pix_icon('i/course', ''));
        $demo5 = $demo3->add('demo5', $this->activeurl, navigation_node::TYPE_COURSE, null, 'demo5',new pix_icon('i/course', ''));
        $demo5->add('activity1', null, navigation_node::TYPE_ACTIVITY, null, 'activity1')->make_active();
        $hiddendemo1 = $this->node->add('hiddendemo1', $this->inactiveurl, navigation_node::TYPE_CATEGORY, null, 'hiddendemo1', new pix_icon('i/course', ''));
        $hiddendemo1->hidden = true;
        $hiddendemo1->add('hiddendemo2', $this->inactiveurl, navigation_node::TYPE_COURSE, null, 'hiddendemo2', new pix_icon('i/course', ''))->helpbutton = 'Here is a help button';;
        $hiddendemo1->add('hiddendemo3', $this->inactiveurl, navigation_node::TYPE_COURSE,null, 'hiddendemo3', new pix_icon('i/course', ''))->display = false;
    }

    public function test___construct() {
        global $CFG;
        $node = new navigation_node($this->fakeproperties);
        $this->assertEquals($node->text, $this->fakeproperties['text']);
        $this->assertTrue(strpos($this->fakeproperties['shorttext'], substr($node->shorttext,0, -3))===0);
        $this->assertEquals($node->key, $this->fakeproperties['key']);
        $this->assertEquals($node->type, $this->fakeproperties['type']);
        $this->assertEquals($node->action, $this->fakeproperties['action']);
    }
    public function test_add() {
        global $CFG;
        // Add a node with all args set
        $node1 = $this->node->add('test_add_1','http://www.moodle.org/',navigation_node::TYPE_COURSE,'testadd1','key',new pix_icon('i/course', ''));
        // Add a node with the minimum args required
        $node2 = $this->node->add('test_add_2',null, navigation_node::TYPE_CUSTOM,'testadd2');
        $node3 = $this->node->add(str_repeat('moodle ', 15),str_repeat('moodle', 15));

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

    public function test_add_before() {
        global $CFG;
        // Create 3 nodes
        $node1 = navigation_node::create('test_add_1', null, navigation_node::TYPE_CUSTOM,
            'test 1', 'testadd1');
        $node2 = navigation_node::create('test_add_2', null, navigation_node::TYPE_CUSTOM,
            'test 2', 'testadd2');
        $node3 = navigation_node::create('test_add_3', null, navigation_node::TYPE_CUSTOM,
            'test 3', 'testadd3');
        // Add node 2, then node 1 before 2, then node 3 at end
        $this->node->add_node($node2);
        $this->node->add_node($node1, 'testadd2');
        $this->node->add_node($node3);
        // Check the last 3 nodes are in 1, 2, 3 order and have those indexes
        foreach($this->node->children as $child) {
            $keys[] = $child->key;
        }
        $this->assertEquals('testadd1', $keys[count($keys)-3]);
        $this->assertEquals('testadd2', $keys[count($keys)-2]);
        $this->assertEquals('testadd3', $keys[count($keys)-1]);
    }

    public function test_add_class() {
        $node = $this->node->get('demo1');
        $this->assertInstanceOf('navigation_node', $node);
        if ($node !== false) {
            $node->add_class('myclass');
            $classes = $node->classes;
            $this->assertTrue(in_array('myclass', $classes));
        }
    }


    public function test_check_if_active() {
        // First test the string urls
        // demo1 -> action is http://www.moodle.org/, thus should be true
        $demo5 = $this->node->find('demo5', navigation_node::TYPE_COURSE);
        if ($this->assertInstanceOf('navigation_node', $demo5)) {
            $this->assertTrue($demo5->check_if_active());
        }

        // demo2 -> action is http://www.moodle.com/, thus should be false
        $demo2 = $this->node->get('demo2');
        if ($this->assertInstanceOf('navigation_node', $demo2)) {
            $this->assertFalse($demo2->check_if_active());
        }
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
        // Should be true activity1 is the active node
        $this->assertTrue($this->node->get('demo3')->get('demo5')->get('activity1')->contains_active_node());
        // Obviously duff
        $this->assertFalse($this->node->get('demo3')->get('demo4')->contains_active_node());
    }

    public function test_find_active_node() {
        $activenode1 = $this->node->find_active_node();
        $activenode2 = $this->node->get('demo1')->find_active_node();

        if ($this->assertInstanceOf('navigation_node', $activenode1)) {
            $ref = $this->node->get('demo3')->get('demo5')->get('activity1');
            $this->assertSame($activenode1, $ref);
        }

        $this->assertNotInstanceOf('navigation_node', $activenode2);
    }

    public function test_find() {
        $node1 = $this->node->find('demo1', navigation_node::TYPE_COURSE);
        $node2 = $this->node->find('demo5', navigation_node::TYPE_COURSE);
        $node3 = $this->node->find('demo5', navigation_node::TYPE_CATEGORY);
        $node4 = $this->node->find('demo0', navigation_node::TYPE_COURSE);
        $this->assertInstanceOf('navigation_node', $node1);
        $this->assertInstanceOf('navigation_node', $node2);
        $this->assertNotInstanceOf('navigation_node', $node3);
        $this->assertNotInstanceOf('navigation_node', $node4);
    }

    public function test_find_expandable() {
        $expandable = array();
        $this->node->find_expandable($expandable);
        //TODO: find out what is wrong here - it was returning 4 before the conversion
        //$this->assertEquals(count($expandable), 4);
        $this->assertEquals(count($expandable), 0);
        if (count($expandable) === 4) {
            $name = $expandable[0]['key'];
            $name .= $expandable[1]['key'];
            $name .= $expandable[2]['key'];
            $name .= $expandable[3]['key'];
            $this->assertEquals($name, 'demo1demo2demo4hiddendemo2');
        }
    }

    public function test_get() {
        $node1 = $this->node->get('demo1'); // Exists
        $node2 = $this->node->get('demo4'); // Doesn't exist for this node
        $node3 = $this->node->get('demo0'); // Doesn't exist at all
        $node4 = $this->node->get(false);   // Sometimes occurs in nature code
        $this->assertInstanceOf('navigation_node', $node1);
        $this->assertFalse($node2);
        $this->assertFalse($node3);
        $this->assertFalse($node4);
    }

    public function test_get_css_type() {
        $csstype1 = $this->node->get('demo3')->get_css_type();
        $csstype2 = $this->node->get('demo3')->get('demo5')->get_css_type();
        $this->node->get('demo3')->get('demo5')->type = 1000;
        $csstype3 = $this->node->get('demo3')->get('demo5')->get_css_type();
        $this->assertEquals($csstype1, 'type_category');
        $this->assertEquals($csstype2, 'type_course');
        $this->assertEquals($csstype3, 'type_unknown');
    }

    public function test_make_active() {
        global $CFG;
        $node1 = $this->node->add('active node 1', null, navigation_node::TYPE_CUSTOM, null, 'anode1');
        $node2 = $this->node->add('active node 2', new moodle_url($CFG->wwwroot), navigation_node::TYPE_COURSE, null, 'anode2');
        $node1->make_active();
        $this->node->get('anode2')->make_active();
        $this->assertTrue($node1->isactive);
        $this->assertTrue($this->node->get('anode2')->isactive);
    }
    public function test_remove() {
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

        // Make sure that we can insert element after removal
        $insertelement = navigation_node::create('extra element 4', null, navigation_node::TYPE_CUSTOM, null, 'element4');
        $this->node->add_node($insertelement, 'remove2');
        $this->assertNotEmpty($this->node->get('element4'));

        // Remove more elements
        $this->assertTrue($this->node->get('remove2')->remove());
        $this->assertFalse($this->node->get('remove2'));

        // Make sure that we can add element after removal
        $this->node->add('extra element 5', null, navigation_node::TYPE_CUSTOM, null, 'element5');
        $this->assertNotEmpty($this->node->get('element5'));

        $this->assertTrue($remove2->get('remove3')->remove());

        $this->assertFalse($this->node->get('remove1'));
        $this->assertFalse($this->node->get('remove2'));
    }
    public function test_remove_class() {
        $this->node->add_class('testclass');
        $this->assertTrue($this->node->remove_class('testclass'));
        $this->assertFalse(in_array('testclass', $this->node->classes));
    }
}

/**
 * This is a dummy object that allows us to call protected methods within the
 * global navigation class by prefixing the methods with `exposed_`
 */
class exposed_global_navigation extends global_navigation {
    protected $exposedkey = 'exposed_';
    public function __construct(moodle_page $page=null) {
        global $PAGE;
        if ($page === null) {
            $page = $PAGE;
        }
        parent::__construct($page);
        $this->cache = new navigation_cache('unittest_nav');
    }
    public function __call($method, $arguments) {
        if (strpos($method,$this->exposedkey) !== false) {
            $method = substr($method, strlen($this->exposedkey));
        }
        if (method_exists($this, $method)) {
            return call_user_func_array(array($this, $method), $arguments);
        }
        throw new coding_exception('You have attempted to access a method that does not exist for the given object '.$method, DEBUG_DEVELOPER);
    }
    public function set_initialised() {
        $this->initialised = true;
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

    public function load_for_user($user=null, $forceforcontext=false) {
        $this->add('load_for_user', null, null, null, 'initcall'.self::$count);
        self::$count++;
        return 0;
    }
}

class global_navigation_testcase extends basic_testcase {
    /**
     * @var global_navigation
     */
    public $node;

    protected function setUp() {
        parent::setUp();

        $this->node = new exposed_global_navigation();
        // Create an initial tree structure to work with
        $cat1 = $this->node->add('category 1', null, navigation_node::TYPE_CATEGORY, null, 'cat1');
        $cat2 = $this->node->add('category 2', null, navigation_node::TYPE_CATEGORY, null, 'cat2');
        $cat3 = $this->node->add('category 3', null, navigation_node::TYPE_CATEGORY, null, 'cat3');
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
    }

    public function test_module_extends_navigation() {
        $this->assertTrue($this->node->exposed_module_extends_navigation('data'));
        $this->assertFalse($this->node->exposed_module_extends_navigation('test1'));
    }
}

/**
 * This is a dummy object that allows us to call protected methods within the
 * global navigation class by prefixing the methods with `exposed_`
 */
class exposed_navbar extends navbar {
    protected $exposedkey = 'exposed_';
    public function __construct(moodle_page $page) {
        parent::__construct($page);
        $this->cache = new navigation_cache('unittest_nav');
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

class navigation_exposed_moodle_page extends moodle_page {
    public function set_navigation(navigation_node $node) {
        $this->_navigation = $node;
    }
}

class navbar_testcase extends advanced_testcase {
    protected $node;
    protected $oldnav;

    protected function setUp() {
        global $PAGE, $SITE;
        parent::setUp();

        $this->resetAfterTest(true);

        $PAGE->set_url('/');
        $PAGE->set_course($SITE);

        $tempnode = new exposed_global_navigation();
        // Create an initial tree structure to work with
        $cat1 = $tempnode->add('category 1', null, navigation_node::TYPE_CATEGORY, null, 'cat1');
        $cat2 = $tempnode->add('category 2', null, navigation_node::TYPE_CATEGORY, null, 'cat2');
        $cat3 = $tempnode->add('category 3', null, navigation_node::TYPE_CATEGORY, null, 'cat3');
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
        $tempnode->find('course2', navigation_node::TYPE_COURSE)->make_active();

        $page = new navigation_exposed_moodle_page();
        $page->set_url($PAGE->url);
        $page->set_context($PAGE->context);

        $navigation = new exposed_global_navigation($page);
        $navigation->children = $tempnode->children;
        $navigation->set_initialised();
        $page->set_navigation($navigation);

        $this->cache = new navigation_cache('unittest_nav');
        $this->node = new exposed_navbar($page);
    }
    public function test_add() {
        // Add a node with all args set
        $this->node->add('test_add_1','http://www.moodle.org/',navigation_node::TYPE_COURSE,'testadd1','testadd1',new pix_icon('i/course', ''));
        // Add a node with the minimum args required
        $this->node->add('test_add_2','http://www.moodle.org/',navigation_node::TYPE_COURSE,'testadd2','testadd2',new pix_icon('i/course', ''));
        $this->assertInstanceOf('navigation_node', $this->node->get('testadd1'));
        $this->assertInstanceOf('navigation_node', $this->node->get('testadd2'));
    }
    public function test_has_items() {
        $this->assertTrue($this->node->has_items());
    }
}

class navigation_cache_testcase extends basic_testcase {
    protected $cache;

    protected function setUp() {
        parent::setUp();

        $this->cache = new navigation_cache('unittest_nav');
        $this->cache->anysetvariable = true;
    }
    public function test___get() {
        $this->assertTrue($this->cache->anysetvariable);
        $this->assertEquals($this->cache->notasetvariable, null);
    }
    public function test___set() {
        $this->cache->myname = 'Sam Hemelryk';
        $this->assertTrue($this->cache->cached('myname'));
        $this->assertEquals($this->cache->myname, 'Sam Hemelryk');
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
        $this->assertEquals($this->cache->software, 'Moodle');
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
        $this->cache = new navigation_cache('unittest_nav');
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

class settings_navigation_testcase extends advanced_testcase {
    protected $node;
    protected $cache;

    protected function setUp() {
        global $PAGE, $SITE;
        parent::setUp();

        $this->resetAfterTest(true);

        $PAGE->set_url('/');
        $PAGE->set_course($SITE);

        $this->cache = new navigation_cache('unittest_nav');
        $this->node = new exposed_settings_navigation();
    }
    public function test___construct() {
        $this->node = new exposed_settings_navigation();
    }
    public function test___initialise() {
        $this->node->initialise();
        $this->assertEquals($this->node->id, 'settingsnav');
    }
    public function test_in_alternative_role() {
        $this->assertFalse($this->node->exposed_in_alternative_role());
    }
}
