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
 * Unit tests for setuplib.php
 *
 * @package   core_phpunit
 * @copyright 2012 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


/**
 * Unit tests for setuplib.php
 *
 * @copyright 2012 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_setuplib_testcase extends basic_testcase {

    /**
     * Test get_docs_url_standard in the normal case when we should link to Moodle docs.
     */
    public function test_get_docs_url_standard() {
        global $CFG;
        if (empty($CFG->docroot)) {
            $docroot = 'http://docs.moodle.org/';
        } else {
            $docroot = $CFG->docroot;
        }
        $this->assertRegExp('~^' . preg_quote($docroot, '') . '/2\d/' . current_language() . '/course/editing$~',
                get_docs_url('course/editing'));
    }

    /**
     * Test get_docs_url_standard in the special case of an absolute HTTP URL.
     */
    public function test_get_docs_url_http() {
        $url = 'http://moodle.org/';
        $this->assertEquals($url, get_docs_url($url));
    }

    /**
     * Test get_docs_url_standard in the special case of an absolute HTTPS URL.
     */
    public function test_get_docs_url_https() {
        $url = 'https://moodle.org/';
        $this->assertEquals($url, get_docs_url($url));
    }

    /**
     * Test get_docs_url_standard in the special case of a link relative to wwwroot.
     */
    public function test_get_docs_url_wwwroot() {
        global $CFG;
        $this->assertEquals($CFG->wwwroot . '/lib/tests/setuplib_test.php',
                get_docs_url('%%WWWROOT%%/lib/tests/setuplib_test.php'));
    }

    /**
     * Test if get_exception_info() removes file system paths
     */
    public function test_exception_info_removes_serverpaths() {
        global $CFG;

        // This doesn't test them all possible ones, but these are set for unit tests.
        $cfgnames = array('dataroot', 'dirroot', 'tempdir', 'cachedir');

        $fixture  = '';
        $expected = '';
        foreach ($cfgnames as $cfgname) {
            if (!empty($CFG->$cfgname)) {
                $fixture  .= $CFG->$cfgname.' ';
                $expected .= "[$cfgname] ";
            }
        }
        $exception     = new moodle_exception('generalexceptionmessage', 'error', '', $fixture, $fixture);
        $exceptioninfo = get_exception_info($exception);

        $this->assertContains($expected, $exceptioninfo->message, 'Exception message does not contain system paths');
        $this->assertContains($expected, $exceptioninfo->debuginfo, 'Exception debug info does not contain system paths');
    }
}
