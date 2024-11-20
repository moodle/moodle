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
 * Unit test for the filter_algebra
 *
 * @package    filter_algebra
 * @copyright  2012 Tim Hunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace filter_algebra;

use core\context\system as context_system;

/**
 * Unit tests for filter_algebra.
 *
 * Note that this only tests some of the filter logic. It does not actually test
 * the normal case of the filter working, because I cannot make it work on my
 * test server, and if it does not work here, it probably does not also work
 * for other people. A failing test will be irritating noise.
 *
 * @package filter_algebra
 * @copyright  2012 Tim Hunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \filter_algebra\text_filter
 */
final class text_filter_test extends \basic_testcase {
    /** @var text_filter The filter to test */
    protected text_filter $filter;

    protected function setUp(): void {
        parent::setUp();
        $this->filter = new text_filter(context_system::instance(), []);
    }

    public function test_algebra_filter_no_algebra(): void {
        $this->assertEquals(
            '<p>Look no algebra!</p>',
            $this->filter->filter('<p>Look no algebra!</p>')
        );
    }


    public function test_algebra_filter_pluginfile(): void {
        $this->assertEquals(
            '<img src="@@PLUGINFILE@@/photo.jpg">',
            $this->filter->filter('<img src="@@PLUGINFILE@@/photo.jpg">')
        );
    }

    public function test_algebra_filter_draftfile(): void {
        $this->assertEquals(
            '<img src="@@DRAFTFILE@@/photo.jpg">',
            $this->filter->filter('<img src="@@DRAFTFILE@@/photo.jpg">')
        );
    }

    public function test_algebra_filter_unified_diff(): void {
        $diff = '
diff -u -r1.1 Worksheet.php
--- Worksheet.php   26 Sep 2003 04:18:02 -0000  1.1
+++ Worksheet.php   18 Nov 2009 03:58:50 -0000
@@ -1264,10 +1264,10 @@
         }

         // Strip the = or @ sign at the beginning of the formula string
-        if (ereg("^=",$formula)) {
+        if (preg_match("/^=/",$formula)) {
             $formula = preg_replace("/(^=)/","",$formula);
         }
-        elseif(ereg("^@",$formula)) {
+        elseif(preg_match("/^@/",$formula)) {
             $formula = preg_replace("/(^@)/","",$formula);
         }
         else {
';
        $this->assertEquals(
            '<pre>' . $diff . '</pre>',
            $this->filter->filter('<pre>' . $diff . '</pre>')
        );
    }
}
