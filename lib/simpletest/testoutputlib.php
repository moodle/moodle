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

//TODO: MDL-21361
return;

$e = <<<EOT

/**
 * Subclass of renderer_factory_base for testing. Implement abstract method and
 * count calls, so we can test caching behaviour.
 *
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class testable_renderer_factory extends renderer_factory_base {
    public $createcalls = array();

    public function __construct() {
        parent::__construct(null);
    }

    public function get_renderer($module, $page, $subtype=null) {
        if (!in_array(array($module, $subtype), $this->createcalls)) {
            $this->createcalls[] = array($module, $subtype);
        }
        return new core_renderer($page);
    }

    public function standard_renderer_class_for_plugin($module, $subtype=null) {
        return parent::standard_renderer_class_for_plugin($module, $subtype);
    }
}


/**
 * Renderer class for testing.
 *
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_test_renderer extends core_renderer {
    public function __construct($containerstack, $page) {
        parent::__construct($containerstack, $page, null);
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
 * Renderer class for testing subrendering feature
 *
 * @copyright 2009 David Mudrak
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_test_subtype_renderer extends core_renderer {
    public function __construct($containerstack, $page) {
        parent::__construct($containerstack, $page, null);
    }

    public function signature($user = 'Administrator') {
        return '<div class="signature">Best regards, ' . $user . '</div>';
    }
}


/**
 * Unit tests for the requriement_base base class.
 *
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class renderer_factory_base_test extends UnitTestCase {

    public static $includecoverage = array('lib/outputlib.php');

    public function test_get_calls_create() {
        // Set up.
        $factory = new testable_renderer_factory();
        // Exercise SUT.
        $renderer    = $factory->get_renderer('modulename', new moodle_page);
        $subrenderer = $factory->get_renderer('modulename', new moodle_page, 'subtype');
        $cached      = $factory->get_renderer('modulename', new moodle_page);
        // Verify outcome
        $this->assertEqual(array(array('modulename', null), array('modulename', 'subtype')), $factory->createcalls);

    }

    public function test_standard_renderer_class_for_plugin_core() {
        // Set up.
        $factory = new testable_renderer_factory();
        // Exercise SUT.
        $classname = $factory->standard_renderer_class_for_plugin('core');
        // Verify outcome
        $this->assertEqual('core_renderer', $classname);
    }

    public function test_standard_renderer_class_for_plugin_test() {
        // Set up.
        $factory = new testable_renderer_factory();
        // Exercise SUT.
        $classname = $factory->standard_renderer_class_for_plugin('mod_test');
        // Verify outcome
        $this->assertEqual('mod_test_renderer', $classname);
    }

    public function test_standard_renderer_class_for_plugin_test_with_subtype() {
        // Set up.
        $factory = new testable_renderer_factory();
        // Exercise SUT.
        $classname = $factory->standard_renderer_class_for_plugin('mod_test', 'subtype');
        // Verify outcome
        $this->assertEqual('mod_test_subtype_renderer', $classname);
    }

    public function test_standard_renderer_class_for_plugin_unknown() {
        // Set up.
        $factory = new testable_renderer_factory();
        $this->expectException();
        // Exercise SUT.
        $classname = $factory->standard_renderer_class_for_plugin('something_that_does_not_exist');
    }
}


/**
 * Unit tests for the standard_renderer_factory class.
 *
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class standard_renderer_factory_test extends UnitTestCase {

    public static $includecoverage = array('lib/outputrenderers.php', 'lib/outputcomponents.php');
    protected $factory;

    public function setUp() {
        parent::setUp();
        $this->factory = new standard_renderer_factory(null, null);
    }

    public function tearDown() {
        $this->factory = null;
        parent::tearDown();
    }

    public function test_get_core_renderer() {
        $renderer = $this->factory->get_renderer('core', new moodle_page);
        $this->assertIsA($renderer, 'core_renderer');
    }

    public function test_get_test_renderer() {
        $renderer = $this->factory->get_renderer('mod_test', new moodle_page);
        $this->assertIsA($renderer, 'mod_test_renderer');
    }

    public function test_get_test_subtype_renderer() {
        $renderer = $this->factory->get_renderer('mod_test', new moodle_page, 'subtype');
        $this->assertIsA($renderer, 'mod_test_subtype_renderer');
    }
}


/**
 * Test-specific subclass that implements a getter for $prefixes.
 *
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class testable_theme_overridden_renderer_factory extends theme_overridden_renderer_factory {

    public static $includecoverage = array('lib/outputlib.php');
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
/*class theme_overridden_renderer_factory_test extends UnitTestCase { // TODO: MDL-21138 rewrite theme unit tests

    public static $includecoverage = array('lib/outputlib.php');
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
        $filename = $theme->dir . '/lib.php';
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
        $renderer    = $factory->get_renderer('mod_test', new moodle_page);
        $subrenderer = $factory->get_renderer('mod_test', new moodle_page, 'subtype');

        // Verify outcome
        $this->assertIsA($renderer, 'mod_test_renderer');
        $this->assertIsA($subrenderer, 'mod_test_subtype_renderer');
    }

    public function test_get_renderer_overridden() {
        // Set up - be very careful because the class under test uses require-once. Pick a unique theme name.
        $theme = $this->make_theme('testrenderertheme');
        $this->write_renderers_file($theme, '
        class testrenderertheme_mod_test_renderer extends mod_test_renderer {
        }');
        $factory = new testable_theme_overridden_renderer_factory($theme, $this->page);

        // Exercise SUT.
        $renderer    = $factory->get_renderer('mod_test', new moodle_page);
        $subrenderer = $factory->get_renderer('mod_test', new moodle_page, 'subtype');

        // Verify outcome
        $this->assertIsA($renderer, 'testrenderertheme_mod_test_renderer');
        $this->assertIsA($subrenderer, 'mod_test_subtype_renderer');
    }

    public function test_get_renderer_overridden_in_parent() {
        // Set up.
        $theme = $this->make_theme('childtheme');
        $theme->parent = 'parentrenderertheme';
        $parenttheme = $this->make_theme('parentrenderertheme');
        $this->write_renderers_file($theme, '');
        $this->write_renderers_file($parenttheme, '
        class parentrenderertheme_core_renderer extends core_renderer {
        }');
        $factory = new testable_theme_overridden_renderer_factory($theme, $this->page);

        // Exercise SUT.
        $renderer = $factory->get_renderer('core', new moodle_page);

        // Verify outcome
        $this->assertIsA($renderer, 'parentrenderertheme_core_renderer');
    }

    public function test_get_renderer_overridden_in_both() {
        // Set up.
        $theme = $this->make_theme('ctheme');
        $theme->parent = 'ptheme';
        $parenttheme = $this->make_theme('ptheme');
        $this->write_renderers_file($theme, '
        class ctheme_core_renderer extends core_renderer {
        }');
        $this->write_renderers_file($parenttheme, '
        class ptheme_core_renderer extends core_renderer {
        }');
        $factory = new testable_theme_overridden_renderer_factory($theme, $this->page);

        // Exercise SUT.
        $renderer = $factory->get_renderer('core', new moodle_page);

        // Verify outcome
        $this->assertIsA($renderer, 'ctheme_core_renderer');
    }
}*/

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

    public static $includecoverage = array('lib/outputlib.php');
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
 * Unit tests for the core_renderer class.
 *
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_renderer_test extends UnitTestCase {

    public static $includecoverage = array('lib/outputrenderers.php', 'lib/outputcomponents.php');
    protected $renderer;

    public function setUp() {
        parent::setUp();
        $this->renderer = new core_renderer(new moodle_page);
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

    public function test_paging_bar() {
        global $CFG;

        $totalcount = 5;
        $perpage = 4;
        $page = 1;
        $baseurl = new moodle_url('/index.php');
        $pagevar = 'mypage';

        $pagingbar = new moodle_paging_bar();
        $pagingbar->totalcount = $totalcount;
        $pagingbar->page = $page;
        $pagingbar->perpage = $perpage;
        $pagingbar->baseurl = $baseurl;
        $pagingbar->pagevar = $pagevar;

        $originalbar = clone($pagingbar);

        $html = $this->renderer->paging_bar($pagingbar);

        $this->assert(new ContainsTagWithAttribute('div', 'class', 'paging'), $html);
        // the 'Previous' link
        $this->assert(new ContainsTagWithAttributes('a', array('class' => 'previous', 'href' => $baseurl->out().'?mypage=0')), $html);
        // the numeric link to the previous page '1' (does not have the 'previous' class)
        $this->assert(new ContainsTagWithAttributes('a', array('href' => $baseurl->out().'?mypage=0'), array('class' => 'previous')), $html);
        // no link to the current page, it's the last page
        $expectation = new ContainsTagWithAttributes('a', array('href' => $baseurl->out().'?mypage=1'), array());
        $this->assertFalse($expectation->test($html));

        // TODO test with more different parameters
    }

    public function test_html_list() {
        $htmllist = new html_list();
        $data = array('item1', 'item2', array('item1-1', 'item1-2'));
        $htmllist->load_data($data);
        $htmllist->items[2]->type = 'ordered';
        $html = $this->renderer->htmllist($htmllist);
    }

    public function test_userpicture() {
        global $CFG;
        // Set up the user with the required fields
        $user = new stdClass();
        $user->firstname = 'Test';
        $user->lastname = 'User';
        $user->picture = false;
        $user->imagealt = false;
        $user->id = 1;
        $userpic = new user_picture();
        $userpic->user = $user;
        $userpic->courseid = 1;
        $userpic->url = true;
        // Setting popup to true adds JS for the link to open in a popup
        $userpic->popup = true;
        $html = $this->renderer->user_picture($userpic);
        $this->assert(new ContainsTagWithAttributes('a', array('title' => 'Test User', 'href' => $CFG->wwwroot.'/user/view.php?id=1&course=1')), $html);
    }

    public function test_heading_with_help() {
        $originalicon = new help_icon();
        $originalicon->page = 'myhelppage';
        $originalicon->text = 'Cool help text';

        $helpicon = clone($originalicon);
        $html = $this->renderer->heading_with_help($helpicon);
        $this->assert(new ContainsTagWithAttribute('div', 'class', 'heading-with-help'), $html);
        $this->assert(new ContainsTagWithAttribute('span', 'class', 'helplink'), $html);
        $this->assert(new ContainsTagWithAttribute('h2', 'class', 'main help'), $html);
        $this->assert(new ContainsTagWithAttributes('img', array('class' => 'iconhelp image', 'src' => $this->renderer->pix_url('help'))), $html);
        $this->assert(new ContainsTagWithContents('h2', 'Cool help text'), $html);

        $helpicon = clone($originalicon);
        $helpicon->image = false;

        $html = $this->renderer->heading_with_help($helpicon);
        $this->assert(new ContainsTagWithAttribute('div', 'class', 'heading-with-help'), $html);
        $this->assert(new ContainsTagWithAttribute('span', 'class', 'helplink'), $html);
        $this->assert(new ContainsTagWithAttribute('h2', 'class', 'main help'), $html);
        $this->assert(new ContainsTagWithAttributes('img', array('class' => 'iconhelp image', 'src' => $this->renderer->pix_url('help'))), $html);
        $this->assert(new ContainsTagWithContents('h2', 'Cool help text'), $html);
    }
}

EOT;
