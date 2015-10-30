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
 * core_minify related tests.
 *
 * @package    core
 * @category   phpunit
 * @copyright  2013 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


/**
 * Class core_minify_testcase.
 */
class core_minify_testcase extends advanced_testcase {
    public function test_css() {
        $css = "
body {
background: #fff;
margin: 0;
padding: 0;
color: #281f18;
}";

        $this->assertSame("body{background:#fff;margin:0;padding:0;color:#281f18}", core_minify::css($css));
    }

    public function test_css_files() {
        global $CFG;

        $testfile1 = "$CFG->tempdir/test1.css";
        $testfile2 = "$CFG->tempdir/test2.css";
        $testfile3 = "$CFG->tempdir/test3.css";

        $css1 = "
body {
background: #fff;
margin: 0;
padding: 0;
color: #281f18;
}";

        $css2 = "body{}";

        file_put_contents($testfile1, $css1);
        file_put_contents($testfile2, $css2);

        $files = array($testfile1, $testfile2);

        $this->assertSame("body{background:#fff;margin:0;padding:0;color:#281f18}\nbody{}", core_minify::css_files($files));


        $files = array($testfile1, $testfile2, $testfile3);

        $this->assertStringStartsWith("body{background:#fff;margin:0;padding:0;color:#281f18}\nbody{}\n\n\n/* Cannot read CSS file ", @core_minify::css_files($files));

        unlink($testfile1);
        unlink($testfile2);
    }

    public function test_js() {
        $js = "
function hm()
{
}
";

        $this->assertSame("function hm(){}", core_minify::js($js));

        $js = "function hm{}";
        $result = core_minify::js($js);
        $this->assertStringStartsWith("\ntry {console.log('Error: Minimisation of JavaScript failed!');} catch (e) {}", $result);
        $this->assertContains($js, $result);
    }

    public function test_js_files() {
        global $CFG;

        $testfile1 = "$CFG->tempdir/test1.js";
        $testfile2 = "$CFG->tempdir/test2.js";
        $testfile3 = "$CFG->tempdir/test3.js";

        $js1 = "
function hm()
{
}
";

        $js2 = "function oh(){}";

        file_put_contents($testfile1, $js1);
        file_put_contents($testfile2, $js2);

        $files = array($testfile1, $testfile2);

        $this->assertSame("function hm(){};\nfunction oh(){}", core_minify::js_files($files));

        $files = array($testfile1, $testfile2, $testfile3);

        $this->assertStringStartsWith("function hm(){};\nfunction oh(){};\n\n\n// Cannot read JS file ", @core_minify::js_files($files));

        unlink($testfile1);
        unlink($testfile2);
    }
}
