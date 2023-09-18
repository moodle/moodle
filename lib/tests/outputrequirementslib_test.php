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

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/outputrequirementslib.php');


/**
 * Unit tests for lib/outputrequirementslibphp.
 *
 * @package   core
 * @category  test
 * @copyright 2012 Petr Škoda
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class outputrequirementslib_test extends \advanced_testcase {
    public function test_string_for_js() {
        $this->resetAfterTest();

        $page = new \moodle_page();
        $page->requires->string_for_js('course', 'moodle', 1);
        $page->requires->string_for_js('course', 'moodle', 1);
        $this->expectException('coding_exception');
        $page->requires->string_for_js('course', 'moodle', 2);

        // Note: we can not switch languages in phpunit yet,
        //       it would be nice to test that the strings are actually fetched in the footer.
    }

    public function test_one_time_output_normal_case() {
        $page = new \moodle_page();
        $this->assertTrue($page->requires->should_create_one_time_item_now('test_item'));
        $this->assertFalse($page->requires->should_create_one_time_item_now('test_item'));
    }

    public function test_one_time_output_repeat_output_throws() {
        $page = new \moodle_page();
        $page->requires->set_one_time_item_created('test_item');
        $this->expectException('coding_exception');
        $page->requires->set_one_time_item_created('test_item');
    }

    public function test_one_time_output_different_pages_independent() {
        $firstpage = new \moodle_page();
        $secondpage = new \moodle_page();
        $this->assertTrue($firstpage->requires->should_create_one_time_item_now('test_item'));
        $this->assertTrue($secondpage->requires->should_create_one_time_item_now('test_item'));
    }

    /**
     * Test for the jquery_plugin method.
     *
     * Test to make sure that backslashes are not generated with either slasharguments set to on or off.
     */
    public function test_jquery_plugin() {
        global $CFG, $PAGE;

        $this->resetAfterTest();

        // With slasharguments on.
        $CFG->slasharguments = 1;

        $page = new \moodle_page();
        $requirements = $page->requires;
        // Assert successful method call.
        $this->assertTrue($requirements->jquery_plugin('jquery'));
        $this->assertTrue($requirements->jquery_plugin('ui'));

        // Get the code containing the required jquery plugins.
        $renderer = $PAGE->get_renderer('core', null, RENDERER_TARGET_MAINTENANCE);
        $requirecode = $requirements->get_top_of_body_code($renderer);
        // Make sure that the generated code does not contain backslashes.
        $this->assertFalse(strpos($requirecode, '\\'), "Output contains backslashes: " . $requirecode);

        // With slasharguments off.
        $CFG->slasharguments = 0;

        $page = new \moodle_page();
        $requirements = $page->requires;
        // Assert successful method call.
        $this->assertTrue($requirements->jquery_plugin('jquery'));
        $this->assertTrue($requirements->jquery_plugin('ui'));

        // Get the code containing the required jquery plugins.
        $requirecode = $requirements->get_top_of_body_code($renderer);
        // Make sure that the generated code does not contain backslashes.
        $this->assertFalse(strpos($requirecode, '\\'), "Output contains backslashes: " . $requirecode);
    }

    /**
     * Test AMD modules loading.
     */
    public function test_js_call_amd() {

        $page = new \moodle_page();

        // Load an AMD module without a function call.
        $page->requires->js_call_amd('theme_foobar/lightbox');

        // Load an AMD module and call its function without parameters.
        $page->requires->js_call_amd('theme_foobar/demo_one', 'init');

        // Load an AMD module and call its function with some parameters.
        $page->requires->js_call_amd('theme_foobar/demo_two', 'init', [
            'foo',
            'keyWillIgnored' => 'baz',
            [42, 'xyz'],
        ]);

        $html = $page->requires->get_end_code();

        $modname = 'theme_foobar/lightbox';
        $this->assertStringContainsString("M.util.js_pending('{$modname}'); require(['{$modname}'], function(amd) {M.util.js_complete('{$modname}');});", $html);

        $modname = 'theme_foobar/demo_one';
        $this->assertStringContainsString("M.util.js_pending('{$modname}'); require(['{$modname}'], function(amd) {amd.init(); M.util.js_complete('{$modname}');});", $html);

        $modname = 'theme_foobar/demo_two';
        $this->assertStringContainsString("M.util.js_pending('{$modname}'); require(['{$modname}'], function(amd) {amd.init(\"foo\", \"baz\", [42,\"xyz\"]); M.util.js_complete('{$modname}');});", $html);
    }

    /**
     * Test the actual URL through which a JavaScript file is served.
     *
     * @param \moodle_url $moodleurl The <u>moodle_url</u> instance pointing to a web resource.
     * @param int $cfgslashargs The value to force $CFG->slasharguments.
     * @param string $expected The expected output URL.
     * @throws ReflectionException if the class does not exist.
     * @see \page_requirements_manager::js_fix_url()
     * @see \moodle_url
     * @covers \page_requirements_manager::js_fix_url
     * @dataProvider js_fix_url_moodle_url_provider
     */
    public function test_js_fix_url_moodle_url(\moodle_url $moodleurl, int $cfgslashargs, string $expected) {
        global $CFG;
        $defaultslashargs = $CFG->slasharguments;

        $CFG->slasharguments = $cfgslashargs;
        $rc = new \ReflectionClass(\page_requirements_manager::class);
        $rcm = $rc->getMethod('js_fix_url');
        $rcm->setAccessible(true);
        $requires = new \page_requirements_manager();
        $actualmoodleurl = $rcm->invokeArgs($requires, [$moodleurl]);
        $this->assertEquals($expected, $actualmoodleurl->out(false));

        $CFG->slasharguments = $defaultslashargs;
    }

    /**
     * Data provider for JavaScript proper Handler using a <u>\moodle_url</url>.
     *
     * @return array
     * @see \page_requirements_manager::js_fix_url()
     * @see \moodle_url
     */
    public function js_fix_url_moodle_url_provider() {
        global $CFG;
        $wwwroot = rtrim($CFG->wwwroot, '/');
        $libdir = rtrim($CFG->libdir, '/');
        $admin = "/{$CFG->admin}/"; // Deprecated, just for coverage purposes.

        // Note: $CFG->slasharguments is enabled by default; it will be a forced setting one day (MDL-62640).
        return [
            'Environment XML file' => [
                new \moodle_url('/admin/environment.xml'),
                0,
                $wwwroot . $admin . 'environment.xml'
            ],
            'Google Maps CDN (HTTPS)' => [
                new \moodle_url('https://maps.googleapis.com/maps/api/js', ['key' => 'googlemapkey3', 'sensor' => 'false']),
                1,
                'https://maps.googleapis.com/maps/api/js?key=googlemapkey3&sensor=false'
            ],
            'Google Maps CDN (HTTP)' => [
                new \moodle_url('http://maps.googleapis.com/maps/api/js', ['key' => 'googlemapkey3', 'sensor' => 'false']),
                0,
                'http://maps.googleapis.com/maps/api/js?key=googlemapkey3&sensor=false'
            ],
            'H5P JS internal resource (slasharguments on)' => [
                new \moodle_url('/h5p/js/embed.js'),
                1,
                $wwwroot . '/lib/javascript.php/1/h5p/js/embed.js'
            ],
            'H5P JS internal resource (slasharguments off)' => [
                new \moodle_url('/h5p/js/embed.js'),
                0,
                $wwwroot . '/lib/javascript.php?rev=1&jsfile=%2Fh5p%2Fjs%2Fembed.js'
            ],
            'A custom Moodle CSS Handler' => [
                new \moodle_url('/mod/data/css.php?d=1234567890'),
                1,
                $wwwroot . '/mod/data/css.php?d=1234567890'
            ],
            'A custom Moodle JS Handler (slasharguments on)' => [
                new \moodle_url('/mod/data/js.php?d=1234567890'),
                1,
                $wwwroot . '/mod/data/js.php?d=1234567890'
            ],
            'A custom Moodle JS Handler (slasharguments off)' => [
                new \moodle_url('/mod/data/js.php?d=1234567890'),
                0,
                $wwwroot . '/mod/data/js.php?d=1234567890'
            ],
        ];
    }

    /**
     * Test the actual url through which a JavaScript file is served.
     *
     * @param string $url The URL pointing to a web resource.
     * @param int $cfgslashargs The value to force $CFG->slasharguments.
     * @param string $expected The expected output URL.
     * @throws ReflectionException if the class does not exist.
     * @see \page_requirements_manager::js_fix_url()
     * @covers \page_requirements_manager::js_fix_url
     * @dataProvider js_fix_url_plain_string_provider
     */
    public function test_js_fix_url_plain_string(string $url, int $cfgslashargs, string $expected) {
        global $CFG;
        $defaultslashargs = $CFG->slasharguments;

        $CFG->slasharguments = $cfgslashargs;
        $rc = new \ReflectionClass(\page_requirements_manager::class);
        $rcm = $rc->getMethod('js_fix_url');
        $rcm->setAccessible(true);
        $requires = new \page_requirements_manager();
        $actualmoodleurl = $rcm->invokeArgs($requires, [$url]);
        $this->assertEquals($expected, $actualmoodleurl->out(false));

        $CFG->slasharguments = $defaultslashargs;
    }

    /**
     * Data provider for JavaScript proper Handler using a plain relative string.
     *
     * @return array
     * @see \page_requirements_manager::js_fix_url()
     */
    public function js_fix_url_plain_string_provider() {
        global $CFG;
        $wwwroot = rtrim($CFG->wwwroot, '/');
        $admin = "/{$CFG->admin}/"; // Deprecated, just for coverage purposes.

        // Note: $CFG->slasharguments is enabled by default; it will be a forced setting one day (MDL-62640).
        return [
            'Environment XML file' => [
                '/admin/environment.xml',
                0,
                $wwwroot . $admin . 'environment.xml'
            ],
            'Course Format JS (slasharguments on)' => [
                '/course/format/topics/format.js',
                1,
                $wwwroot . '/lib/javascript.php/1/course/format/topics/format.js'
            ],
            'Course Format JS (slasharguments off)' => [
                '/course/format/topics/format.js',
                0,
                $wwwroot . '/lib/javascript.php?rev=1&jsfile=%2Fcourse%2Fformat%2Ftopics%2Fformat.js'
            ],
            'Data JS' => [
                '/mod/data/data.js',
                1,
                $wwwroot . '/lib/javascript.php/1/mod/data/data.js'
            ],
            'SCORM Request JS' => [
                '/mod/scorm/request.js',
                1,
                $wwwroot . '/lib/javascript.php/1/mod/scorm/request.js'
            ],
            'Wiki Editors Buttons JS' => [
                '/mod/wiki/editors/wiki/buttons.js',
                1,
                $wwwroot . '/lib/javascript.php/1/mod/wiki/editors/wiki/buttons.js'
            ],
            'A non-JS internal resource' => [
                '/theme/boost/pix/favicon.ico',
                0,
                $wwwroot . '/theme/boost/pix/favicon.ico'
            ],
            'A custom Moodle CSS Handler' => [
                '/mod/data/css.php?d=1234567890',
                1,
                $wwwroot . '/mod/data/css.php?d=1234567890'
            ],
            'A custom Moodle JS Handler (slasharguments on)' => [
                '/mod/data/js.php?d=1234567890',
                1,
                $wwwroot . '/mod/data/js.php?d=1234567890'
            ],
            'A custom Moodle JS Handler (slasharguments off)' => [
                '/mod/data/js.php?d=1234567890',
                0,
                $wwwroot . '/mod/data/js.php?d=1234567890'
            ],
        ];
    }

    /**
     * Test the coding exceptions when trying to get the actual URL through which a JavaScript file is served.
     *
     * @param moodle_url|string|null $url The URL pointing to a web resource.
     * @param string $exmessage The expected output URL.
     * @throws ReflectionException if the class does not exist.
     * @see \page_requirements_manager::js_fix_url()
     * @covers \page_requirements_manager::js_fix_url
     * @dataProvider js_fix_url_coding_exception_provider
     */
    public function test_js_fix_url_coding_exception($url, string $exmessage) {
        $rc = new \ReflectionClass(\page_requirements_manager::class);
        $rcm = $rc->getMethod('js_fix_url');
        $rcm->setAccessible(true);
        $requires = new \page_requirements_manager();
        $this->expectException(\coding_exception::class);
        $this->expectExceptionMessage($exmessage);
        $actualmoodleurl = $rcm->invokeArgs($requires, [$url]);
    }

    /**
     * Data provider for throwing coding exceptions in <u>\page_requirements_manager::js_fix_url()</u>.
     *
     * @return array
     * @see \page_requirements_manager::js_fix_url()
     */
    public function js_fix_url_coding_exception_provider() {
        global $CFG;
        $wwwroot = rtrim($CFG->wwwroot, '/');

        return [
            'Provide a null argument' => [
                null,
                'Coding error detected, it must be fixed by a programmer: '
                    . 'Invalid JS url, it has to be shortened url starting with / or moodle_url instance.'
            ],
            'Provide an internal absolute URL' => [
                $wwwroot . '/lib/javascript.php/1/h5p/js/embed.js',
                'Coding error detected, it must be fixed by a programmer: '
                    . 'Invalid JS url, it has to be shortened url starting with / or moodle_url instance. '
                    . '(' . $wwwroot . '/lib/javascript.php/1/h5p/js/embed.js)'
            ],
            'Provide an external absolute URL' => [
                'https://maps.googleapis.com/maps/api/js?key=googlemapkey3&sensor=false',
                'Coding error detected, it must be fixed by a programmer: '
                    . 'Invalid JS url, it has to be shortened url starting with / or moodle_url instance. '
                    . '(https://maps.googleapis.com/maps/api/js?key=googlemapkey3&sensor=false)'
            ],
            'A non-JS internal resource using an absolute URL' => [
                $wwwroot . '/theme/boost/pix/favicon.ico',
                'Coding error detected, it must be fixed by a programmer: '
                    . 'Invalid JS url, it has to be shortened url starting with / or moodle_url instance. ('
                    . $wwwroot . '/theme/boost/pix/favicon.ico)'
            ],
            'A non-existant internal resource using an absolute URL' => [
                $wwwroot . '/path/to/file.ext',
                'Coding error detected, it must be fixed by a programmer: '
                    . 'Invalid JS url, it has to be shortened url starting with / or moodle_url instance. ('
                    . $wwwroot . '/path/to/file.ext)'
            ],
            'A non-existant internal resource. WARN the developer!' => [
                '/path/to/file1.ext',
                'Coding error detected, it must be fixed by a programmer: '
                    . 'Attempt to require a JavaScript file that does not exist. (/path/to/file1.ext)'
            ],
            'A non-existant internal resource using moodle_url. WARN the developer!' => [
                new \moodle_url('/path/to/file2.ext'),
                'Coding error detected, it must be fixed by a programmer: '
                    . 'Attempt to require a JavaScript file that does not exist. (/path/to/file2.ext)'
            ],
        ];
    }
}
