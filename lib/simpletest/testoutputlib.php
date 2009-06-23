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
 * Unit tests for (some of) ../outputlib.php.
 *
 * @package   moodlecore
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later (5)
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}
require_once($CFG->libdir . '/outputlib.php');


// TODO this is needed until MDL-16438 is committed.
function get_plugin_dir($module) {
    global $CFG;
    return $CFG->dirroot;
}


/**
 * Subclass of renderer_factory_base for testing. Implement abstract method and
 * count calls, so we can test caching behaviour.
 */
class testable_renderer_factory extends renderer_factory_base {
    public $createcalls = array();

    public function __construct() {
        parent::__construct(null, null);
    }

    public function create_renderer($module) {
        $this->createcalls[] = $module;
        return new moodle_core_renderer(new xhtml_container_stack());
    }

    public function standard_renderer_class_for_module($module) {
        return parent::standard_renderer_class_for_module($module);
    }
}


/**
 * Renderer class for testing.
 */
class moodle_test_renderer extends moodle_core_renderer {
    public function __construct($containerstack) {
        parent::__construct($containerstack);
    }

    public function greeting($name = 'world') {
        return '<h1>Hello ' . $name . '!</h1>';
    }

    public function box($content, $id = '') {
        return box_start($id) . $content . box_end();
    }

    public function box_start($id = '') {
        if ($id) {
            $id = ' id="' . $id . '"';
        }
        $this->containerstack->push('box', '</div>');
        return '<div' . $id . '>';
    }

    public function box_end() {
        return $this->containerstack->pop('box');
    }
}


/**
 * Unit tests for the requriement_base base class.
 *
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class renderer_factory_base_test extends UnitTestCase {
    public function test_get_calls_create() {
        // Set up.
        $factory = new testable_renderer_factory();
        // Exercise SUT.
        $renderer = $factory->get_renderer('modulename');
        // Verify outcome
        $this->assertEqual(array('modulename'), $factory->createcalls);
    }

    public function test_get_caches_repeat_calls() {
        // Set up.
        $factory = new testable_renderer_factory();
        // Exercise SUT.
        $renderer1 = $factory->get_renderer('modulename');
        $renderer2 = $factory->get_renderer('modulename');
        // Verify outcome
        $this->assertEqual(array('modulename'), $factory->createcalls);
        $this->assertIdentical($renderer1, $renderer2);
    }

    public function test_standard_renderer_class_for_module_core() {
        // Set up.
        $factory = new testable_renderer_factory();
        // Exercise SUT.
        $classname = $factory->standard_renderer_class_for_module('core');
        // Verify outcome
        $this->assertEqual('moodle_core_renderer', $classname);
    }

    public function test_standard_renderer_class_for_module_test() {
        // Set up.
        $factory = new testable_renderer_factory();
        // Exercise SUT.
        $classname = $factory->standard_renderer_class_for_module('test');
        // Verify outcome
        $this->assertEqual('moodle_test_renderer', $classname);
    }

    public function test_standard_renderer_class_for_module_unknown() {
        // Set up.
        $factory = new testable_renderer_factory();
        $this->expectException();
        // Exercise SUT.
        $classname = $factory->standard_renderer_class_for_module('something_that_does_not_exist');
    }
}


/**
 * Unit tests for the standard_renderer_factory class.
 *
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class standard_renderer_factory_test extends UnitTestCase {
    protected $factory;

    public function setUp() {
        parent::setUp();
        $page = new stdClass;
        $page->opencontainers = new xhtml_container_stack();
        $this->factory = new standard_renderer_factory(null, $page);
    }

    public function tearDown() {
        $this->factory = null;
        parent::tearDown();
    }

    public function test_get_core_renderer() {
        $renderer = $this->factory->get_renderer('core');
        $this->assertIsA($renderer, 'moodle_core_renderer');
    }

    public function test_get_test_renderer() {
        $renderer = $this->factory->get_renderer('test');
        $this->assertIsA($renderer, 'moodle_test_renderer');
    }
}


/**
 * Unit tests for the custom_corners_renderer_factory class.
 *
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class custom_corners_renderer_factory_test extends UnitTestCase {
    protected $factory;

    public function setUp() {
        parent::setUp();
        $page = new stdClass;
        $page->opencontainers = new xhtml_container_stack();
        $this->factory = new custom_corners_renderer_factory(null, $page);
    }

    public function tearDown() {
        $this->factory = null;
        parent::tearDown();
    }

    public function test_get_core_renderer() {
        $renderer = $this->factory->get_renderer('core');
        $this->assertIsA($renderer, 'custom_corners_core_renderer');
    }

    public function test_get_test_renderer() {
        $renderer = $this->factory->get_renderer('test');
        $this->assertIsA($renderer, 'moodle_test_renderer');
    }
}


/**
 * Test-specific subclass that implements a getter for $prefixes.
 *
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class testable_theme_overridden_renderer_factory extends theme_overridden_renderer_factory {
    public function get_prefixes() {
        return $this->prefixes;
    }
}


/**
 * Unit tests for the theme_overridden_renderer_factory class.
 *
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class theme_overridden_renderer_factory_test extends UnitTestCase {
    protected $originalcfgthemedir;
    protected $workspace;
    protected $page;
    protected $foldertocleanup = null;

    public function setUp() {
        global $CFG;
        parent::setUp();
        $this->originalcfgthemedir = $CFG->themedir;

        $this->workspace = 'temp/theme_overridden_renderer_factory_fixtures';
        make_upload_directory($this->workspace);
        $CFG->themedir = $CFG->dataroot . '/' . $this->workspace;
        $this->foldertocleanup = $CFG->themedir;

        $this->page = new stdClass;
        $this->page->opencontainers = new xhtml_container_stack();
    }

    public function tearDown() {
        global $CFG;
        if (!empty($this->foldertocleanup)) {
            fulldelete($this->foldertocleanup);
            $this->foldertocleanup = null;
        }
        $CFG->themedir = $this->originalcfgthemedir;
        parent::tearDown();
    }

    protected function make_theme($name) {
        global $CFG;
        $theme = new stdClass;
        $theme->name = $name;
        $theme->dir = $CFG->themedir . '/' . $name;
        make_upload_directory($this->workspace . '/' . $name);
        return $theme;
    }

    protected function write_renderers_file($theme, $code) {
        $filename = $theme->dir . '/renderers.php';
        file_put_contents($filename, "<?php\n" . $code);
    }

    public function test_constructor_theme_with_renderes() {
        // Set up.
        $theme = $this->make_theme('mytheme');
        $this->write_renderers_file($theme, '');

        // Exercise SUT.
        $factory = new testable_theme_overridden_renderer_factory($theme, $this->page);

        // Verify outcome
        $this->assertEqual(array('mytheme_'), $factory->get_prefixes());
    }

    public function test_constructor_theme_without_renderes() {
        // Set up.
        $theme = $this->make_theme('mytheme');

        // Exercise SUT.
        $factory = new testable_theme_overridden_renderer_factory($theme, $this->page);

        // Verify outcome
        $this->assertEqual(array(), $factory->get_prefixes());
    }

    public function test_constructor_theme_with_parent() {
        // Set up.
        $theme = $this->make_theme('mytheme');
        $theme->parent = 'parenttheme';
        $parenttheme = $this->make_theme('parenttheme');
        $this->write_renderers_file($parenttheme, '');

        // Exercise SUT.
        $factory = new testable_theme_overridden_renderer_factory($theme, $this->page);

        // Verify outcome
        $this->assertEqual(array('parenttheme_'), $factory->get_prefixes());
    }

    public function test_get_renderer_not_overridden() {
        // Set up.
        $theme = $this->make_theme('mytheme');
        $this->write_renderers_file($theme, '');
        $factory = new testable_theme_overridden_renderer_factory($theme, $this->page);

        // Exercise SUT.
        $renderer = $factory->get_renderer('test');

        // Verify outcome
        $this->assertIsA($renderer, 'moodle_test_renderer');
    }

    public function test_get_renderer_overridden() {
        // Set up - be very careful because the class under test uses require-once. Pick a unique theme name.
        $theme = $this->make_theme('testrenderertheme');
        $this->write_renderers_file($theme, '
        class testrenderertheme_test_renderer extends moodle_test_renderer {
        }');
        $factory = new testable_theme_overridden_renderer_factory($theme, $this->page);

        // Exercise SUT.
        $renderer = $factory->get_renderer('test');

        // Verify outcome
        $this->assertIsA($renderer, 'testrenderertheme_test_renderer');
    }

    public function test_get_renderer_overridden_in_parent() {
        // Set up.
        $theme = $this->make_theme('childtheme');
        $theme->parent = 'parentrenderertheme';
        $parenttheme = $this->make_theme('parentrenderertheme');
        $this->write_renderers_file($theme, '');
        $this->write_renderers_file($parenttheme, '
        class parentrenderertheme_core_renderer extends moodle_core_renderer {
        }');
        $factory = new testable_theme_overridden_renderer_factory($theme, $this->page);

        // Exercise SUT.
        $renderer = $factory->get_renderer('core');

        // Verify outcome
        $this->assertIsA($renderer, 'parentrenderertheme_core_renderer');
    }

    public function test_get_renderer_overridden_in_both() {
        // Set up.
        $theme = $this->make_theme('ctheme');
        $theme->parent = 'ptheme';
        $parenttheme = $this->make_theme('ptheme');
        $this->write_renderers_file($theme, '
        class ctheme_core_renderer extends moodle_core_renderer {
        }');
        $this->write_renderers_file($parenttheme, '
        class ptheme_core_renderer extends moodle_core_renderer {
        }');
        $factory = new testable_theme_overridden_renderer_factory($theme, $this->page);

        // Exercise SUT.
        $renderer = $factory->get_renderer('core');

        // Verify outcome
        $this->assertIsA($renderer, 'ctheme_core_renderer');
    }
}


/**
 * Test-specific subclass that implements a getter for $searchpaths.
 *
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class testable_template_renderer_factory extends template_renderer_factory {
    public function get_search_paths() {
        return $this->searchpaths;
    }
}


/**
 * Unit tests for the template_renderer_factory class.
 *
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class template_renderer_factory_test extends UnitTestCase {
    protected $originalcfgthemedir;
    protected $workspace;
    protected $page;
    protected $foldertocleanup = null;

    public function setUp() {
        global $CFG;
        parent::setUp();
        $this->originalcfgthemedir = $CFG->themedir;

        $this->workspace = 'temp/template_renderer_factory_fixtures';
        make_upload_directory($this->workspace);
        $CFG->themedir = $CFG->dataroot . '/' . $this->workspace;
        $this->foldertocleanup = $CFG->themedir;

        $this->page = new stdClass;
        $this->page->opencontainers = new xhtml_container_stack();
    }

    public function tearDown() {
        global $CFG;
        if (!empty($this->foldertocleanup)) {
            fulldelete($this->foldertocleanup);
            $this->foldertocleanup = null;
        }
        $CFG->themedir = $this->originalcfgthemedir;
        parent::tearDown();
    }

    protected function make_theme($name) {
        global $CFG;
        $theme = new stdClass;
        $theme->name = $name;
        $theme->dir = $CFG->themedir . '/' . $name;
        make_upload_directory($this->workspace . '/' . $name);
        return $theme;
    }

    protected function make_theme_template_dir($name, $module = '') {
        $path = $this->workspace . '/' . $name . '/templates';
        if ($module) {
            $path .= '/' . $module;
        }
        make_upload_directory($path);
    }

    public function test_constructor_standardtemplate() {
        global $CFG;
        // Set up.
        $theme = $this->make_theme('standardtemplate');
        $this->make_theme_template_dir('standardtemplate');

        // Exercise SUT.
        $factory = new testable_template_renderer_factory($theme, $this->page);

        // Verify outcome
        $this->assertEqual(array($CFG->themedir . '/standardtemplate/templates'),
                $factory->get_search_paths());
    }

    public function test_constructor_mytheme() {
        global $CFG;
        // Set up.
        $theme = $this->make_theme('mytheme');
        $this->make_theme_template_dir('mytheme');
        $this->make_theme_template_dir('standardtemplate');

        // Exercise SUT.
        $factory = new testable_template_renderer_factory($theme, $this->page);

        // Verify outcome
        $this->assertEqual(array(
                $CFG->themedir . '/mytheme/templates',
                $CFG->themedir . '/standardtemplate/templates'),
                $factory->get_search_paths());
    }

    public function test_constructor_mytheme_no_templates() {
        global $CFG;
        // Set up.
        $theme = $this->make_theme('mytheme');
        $this->make_theme_template_dir('standardtemplate');

        // Exercise SUT.
        $factory = new testable_template_renderer_factory($theme, $this->page);

        // Verify outcome
        $this->assertEqual(array($CFG->themedir . '/standardtemplate/templates'),
                $factory->get_search_paths());
    }

    public function test_constructor_mytheme_with_parent() {
        global $CFG;
        // Set up.
        $theme = $this->make_theme('mytheme');
        $theme->parent = 'parenttheme';
        $this->make_theme_template_dir('mytheme');
        $this->make_theme_template_dir('parenttheme');
        $this->make_theme_template_dir('standardtemplate');
                
        // Exercise SUT.
        $factory = new testable_template_renderer_factory($theme, $this->page);

        // Verify outcome
        $this->assertEqual(array(
                $CFG->themedir . '/mytheme/templates',
                $CFG->themedir . '/parenttheme/templates',
                $CFG->themedir . '/standardtemplate/templates'),
                $factory->get_search_paths());
    }

    public function test_constructor_mytheme_with_parent_no_templates() {
        global $CFG;
        // Set up.
        $theme = $this->make_theme('mytheme');
        $theme->parent = 'parenttheme';
        $this->make_theme_template_dir('mytheme');
        $this->make_theme_template_dir('standardtemplate');
                
        // Exercise SUT.
        $factory = new testable_template_renderer_factory($theme, $this->page);

        // Verify outcome
        $this->assertEqual(array(
                $CFG->themedir . '/mytheme/templates',
                $CFG->themedir . '/standardtemplate/templates'),
                $factory->get_search_paths());
    }

    public function test_get_renderer() {
        global $CFG;
        // Set up.
        $theme = $this->make_theme('mytheme');
        $theme->parent = 'parenttheme';
        $this->make_theme_template_dir('mytheme', 'core');
        $this->make_theme_template_dir('parenttheme', 'test');
        $this->make_theme_template_dir('standardtemplate', 'test');
        $factory = new testable_template_renderer_factory($theme, $this->page);

        // Exercise SUT.
        $renderer = $factory->get_renderer('test');

        // Verify outcome
        $this->assertEqual('moodle_test_renderer', $renderer->get_copied_class());
        $this->assertEqual(array(
                $CFG->themedir . '/parenttheme/templates/test',
                $CFG->themedir . '/standardtemplate/templates/test'),
                $renderer->get_search_paths());
    }
}


/**
 * Unit tests for the xhtml_container_stack class.
 *
 * These tests assume that developer debug mode is on, which, at the time of
 * writing, is true. admin/report/unittest/index.php forces it on.
 *
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class xhtml_container_stack_test extends UnitTestCase {
    protected function start_capture() {
        ob_start();
    }

    protected function end_capture() {
        $result = ob_get_contents();
        ob_end_clean();
        return $result;
    }

    public function test_push_then_pop() {
        // Set up.
        $stack = new xhtml_container_stack();
        // Exercise SUT.
        $this->start_capture();
        $stack->push('testtype', '</div>');
        $html = $stack->pop('testtype');
        $errors = $this->end_capture();
        // Verify outcome
        $this->assertEqual('</div>', $html);
        $this->assertEqual('', $errors);
    }

    public function test_mismatched_pop_prints_warning() {
        // Set up.
        $stack = new xhtml_container_stack();
        $stack->push('testtype', '</div>');
        // Exercise SUT.
        $this->start_capture();
        $html = $stack->pop('mismatch');
        $errors = $this->end_capture();
        // Verify outcome
        $this->assertEqual('</div>', $html);
        $this->assertNotEqual('', $errors);
    }

    public function test_pop_when_empty_prints_warning() {
        // Set up.
        $stack = new xhtml_container_stack();
        // Exercise SUT.
        $this->start_capture();
        $html = $stack->pop('testtype');
        $errors = $this->end_capture();
        // Verify outcome
        $this->assertEqual('', $html);
        $this->assertNotEqual('', $errors);
    }

    public function test_correct_nesting() {
        // Set up.
        $stack = new xhtml_container_stack();
        // Exercise SUT.
        $this->start_capture();
        $stack->push('testdiv', '</div>');
        $stack->push('testp', '</p>');
        $html2 = $stack->pop('testp');
        $html1 = $stack->pop('testdiv');
        $errors = $this->end_capture();
        // Verify outcome
        $this->assertEqual('</p>', $html2);
        $this->assertEqual('</div>', $html1);
        $this->assertEqual('', $errors);
    }

    public function test_pop_all_but_last() {
        // Set up.
        $stack = new xhtml_container_stack();
        $stack->push('test1', '</h1>');
        $stack->push('test2', '</h2>');
        $stack->push('test3', '</h3>');
        // Exercise SUT.
        $this->start_capture();
        $html = $stack->pop_all_but_last();
        $errors = $this->end_capture();
        // Verify outcome
        $this->assertEqual('</h3></h2>', $html);
        $this->assertEqual('', $errors);
        // Tear down.
        $stack->discard();
    }

    public function test_pop_all_but_last_only_one() {
        // Set up.
        $stack = new xhtml_container_stack();
        $stack->push('test1', '</h1>');
        // Exercise SUT.
        $this->start_capture();
        $html = $stack->pop_all_but_last();
        $errors = $this->end_capture();
        // Verify outcome
        $this->assertEqual('', $html);
        $this->assertEqual('', $errors);
        // Tear down.
        $stack->discard();
    }

    public function test_pop_all_but_last_empty() {
        // Set up.
        $stack = new xhtml_container_stack();
        // Exercise SUT.
        $this->start_capture();
        $html = $stack->pop_all_but_last();
        $errors = $this->end_capture();
        // Verify outcome
        $this->assertEqual('', $html);
        $this->assertEqual('', $errors);
    }

    public function test_destruct() {
        // Set up.
        $stack = new xhtml_container_stack();
        $stack->push('test1', '</somethingdistinctive>');
        // Exercise SUT.
        $this->start_capture();
        $stack = null;
        $errors = $this->end_capture();
        // Verify outcome
        $this->assertPattern('/<\/somethingdistinctive>/', $errors);
    }

    public function test_destruct_empty() {
        // Set up.
        $stack = new xhtml_container_stack();
        // Exercise SUT.
        $this->start_capture();
        $stack = null;
        $errors = $this->end_capture();
        // Verify outcome
        $this->assertEqual('', $errors);
    }

    public function test_discard() {
        // Set up.
        $stack = new xhtml_container_stack();
        $stack->push('test1', '</somethingdistinctive>');
        $stack->discard();
        // Exercise SUT.
        $this->start_capture();
        $stack = null;
        $errors = $this->end_capture();
        // Verify outcome
        $this->assertEqual('', $errors);
    }
}


/**
 * Unit tests for the template_renderer class.
 *
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class template_renderer_test extends UnitTestCase {
    protected $renderer;
    protected $templatefolder;
    protected $savedtemplates;

    public function setUp() {
        global $CFG;
        parent::setUp();
        $this->templatefolder = $CFG->dataroot . '/temp/template_renderer_fixtures/test';
        make_upload_directory('temp/template_renderer_fixtures/test');
        $this->renderer = new template_renderer('moodle_test_renderer',
                array($this->templatefolder), new xhtml_container_stack());
        $this->savedtemplates = array();
    }

    public function tearDown() {
        $this->renderer = null;
        foreach ($this->savedtemplates as $template) {
            unlink($template);
        }
        $this->savedtemplates = array();
        parent::tearDown();
    }

    protected function save_template($name, $contents) {
        $filename = $this->templatefolder . '/' . $name . '.php';
        $this->savedtemplates[] = $filename;
        file_put_contents($filename, $contents);
    }

    public function test_simple_template() {
        $this->save_template('greeting', '<p>Hello <?php echo $name ?>!</p>');

        $html = $this->renderer->greeting('Moodle');
        $this->assertEqual('<p>Hello Moodle!</p>', $html);
    }

    public function test_simple_template_default_argument_value() {
        $this->save_template('greeting', '<p>Hello <?php echo $name ?>!</p>');

        $html = $this->renderer->greeting();
        $this->assertEqual('<p>Hello world!</p>', $html);
    }

    public function test_box_template() {
        $this->save_template('box', '<div class="box"<?php echo $id ?>><?php echo $content ?></div>');

        $html = $this->renderer->box('This is a message in a box', 'messagediv');
        $this->assertEqual('<div class="box"messagediv>This is a message in a box</div>', $html);
    }

    public function test_box_start_end_templates() {
        $this->save_template('box', '<div class="box"<?php echo $id ?>><?php echo $content ?></div>');

        $html = $this->renderer->box_start('messagediv');
        $this->assertEqual('<div class="box"messagediv>', $html);

        $html = $this->renderer->box_end();
        $this->assertEqual('</div>', $html);
    }
}


/**
 * Unit tests for the moodle_core_renderer class.
 *
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class moodle_core_renderer_test extends UnitTestCase {
    protected $containerstack;
    protected $renderer;

    public function setUp() {
        parent::setUp();
        $this->containerstack = new xhtml_container_stack();
        $this->renderer = new moodle_core_renderer($this->containerstack);
    }

    public function test_select_menu_simple() {
        $selectmenu = moodle_select_menu::make(array(10 => 'ten', 'c2' => 'two'), 'mymenu');
        $html = $this->renderer->select_menu($selectmenu);
        $this->assert(new ContainsTagWithAttribute('select', 'class', 'menumymenu select'), $html);
        $this->assert(new ContainsTagWithAttribute('select', 'name', 'mymenu'), $html);
        $this->assert(new ContainsTagWithAttribute('select', 'id', 'menumymenu'), $html);
        $this->assert(new ContainsTagWithContents('option', 'ten'), $html);
        $this->assert(new ContainsTagWithAttribute('option', 'value', '10'), $html);
        $this->assert(new ContainsTagWithContents('option', 'two'), $html);
        $this->assert(new ContainsTagWithAttribute('option', 'value', 'c2'), $html);
    }

    public function test_error_text() {
        $html = $this->renderer->error_text('message');
        $this->assert(new ContainsTagWithContents('span', 'message'), $html);
        $this->assert(new ContainsTagWithAttribute('span', 'class', 'error'), $html);
    }

    public function test_error_text_blank() {
        $html = $this->renderer->error_text('');
        $this->assertEqual('', $html);
    }
}
