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
 * This file contains the unittests for the css optimiser in csslib.php
 *
 * @package   moodlecore
 * @copyright 2011 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}
require_once($CFG->libdir . '/csslib.php');

class css_optimiser_test extends UnitTestCase {

    public function setUp() {
        global $CFG;
        parent::setUp();
        $CFG->includecssstats = false;
    }

    public function test_process() {
        $optimiser = new css_optimiser;

        $this->check_simple_comparisons($optimiser);
        $this->check_invalid_css_handling($optimiser);
        $this->check_optimisation($optimiser);
        $this->check_logic_maintained($optimiser);
        $this->check_bulk_processing($optimiser);
    }

    protected function check_simple_comparisons(css_optimiser $optimiser) {
        $css = '.css{}';
        $this->assertEqual($css, $optimiser->process($css));

        $css = '.css{color:#123456;}';
        $this->assertEqual($css, $optimiser->process($css));

        $css = '#some{color:#123456;}';
        $this->assertEqual($css, $optimiser->process($css));

        $css = 'div{color:#123456;}';
        $this->assertEqual($css, $optimiser->process($css));

        $css = 'div.css{color:#123456;}';
        $this->assertEqual($css, $optimiser->process($css));

        $css = 'div#some{color:#123456;}';
        $this->assertEqual($css, $optimiser->process($css));

        $css = 'div[type=blah]{color:#123456;}';
        $this->assertEqual($css, $optimiser->process($css));

        $css = 'div.css[type=blah]{color:#123456;}';
        $this->assertEqual($css, $optimiser->process($css));

        $css = 'div#some[type=blah]{color:#123456;}';
        $this->assertEqual($css, $optimiser->process($css));

        $css = '#some.css[type=blah]{color:#123456;}';
        $this->assertEqual($css, $optimiser->process($css));

        $css = '#some .css[type=blah]{color:#123456;}';
        $this->assertEqual($css, $optimiser->process($css));

        $cssin  = '.css {width:0}';
        $cssout = '.css{width:0;}';
        $this->assertEqual($cssout, $optimiser->process($cssin));

        $cssin  = '.css {width:0px}';
        $cssout = '.css{width:0;}';
        $this->assertEqual($cssout, $optimiser->process($cssin));

        $cssin  = '.css {width:100px}';
        $cssout = '.css{width:100px;}';
        $this->assertEqual($cssout, $optimiser->process($cssin));

    }

    protected function check_invalid_css_handling(css_optimiser $optimiser) {

        $cssin = array(
            '.one{}',
            '.one {:}',
            '.one {;}',
            '.one {;;;;;}',
            '.one {:;}',
            '.one {:;:;:;:::;;;}',
            '.one {!important}',
            '.one {:!important}',
            '.one {:!important;}',
            '.one {;!important}'
        );
        $cssout = '.one{}';
        foreach ($cssin as $css) {
            $this->assertEqual($cssout, $optimiser->process($css));
        }

        $cssin = array(
            '.one{background-color:red;}',
            '.one {background-color:red;} .one {background-color:}',
            '.one {background-color:red;} .one {background-color;}',
            '.one {background-color:red;} .one {background-color}',
            '.one {background-color:red;} .one {background-color:;}',
            '.one {background-color:red;} .one {:blue;}',
            '.one {background-color:red;} .one {:#00F}',
        );
        $cssout = '.one{background-color:#F00;}';
        foreach ($cssin as $css) {
            $this->assertEqual($cssout, $optimiser->process($css));
        }

        $cssin = '..one {background-color:color:red}';
        $cssout = '..one{background-color:color:red;}';
        $this->assertEqual($cssout, $optimiser->process($cssin));

        $cssin = '#.one {background-color:color:red}';
        $cssout = '#.one{background-color:color:red;}';
        $this->assertEqual($cssout, $optimiser->process($cssin));

        $cssin = '##one {background-color:color:red}';
        $cssout = '##one{background-color:color:red;}';
        $this->assertEqual($cssout, $optimiser->process($cssin));

        $cssin = '.one {background-color:color:red}';
        $cssout = '.one{background-color:color:red;}';
        $this->assertEqual($cssout, $optimiser->process($cssin));

        $cssin = '.one {background-color:red;color;border-color:blue}';
        $cssout = '.one{background-color:#F00;border-color:#00F;}';
        $this->assertEqual($cssout, $optimiser->process($cssin));

        $cssin  = '{background-color:#123456;color:red;}{color:green;}';
        $cssout = "{background-color:#123456;color:#008000;}";
        $this->assertEqual($cssout, $optimiser->process($cssin));

        $cssin  = '.one {color:red;} {color:green;} .one {background-color:blue;}';
        $cssout = ".one{color:#F00;background-color:#00F;}\n{color:#008000;}";
        $this->assertEqual($cssout, $optimiser->process($cssin));
    }

    public function check_optimisation(css_optimiser $optimiser) {
        $cssin = '.one {border:1px solid red;}';
        $cssout = '.one{border:1px solid red;}';
        $this->assertEqual($cssout, $optimiser->process($cssin));

        $cssin = '.one, .two {border:1px solid red;}';
        $cssout = ".one,\n.two{border:1px solid red;}";
        $this->assertEqual($cssout, $optimiser->process($cssin));

        $cssin = '.one {border:1px solid red;} .two {border:1px solid red;}';
        $cssout = ".one,\n.two{border:1px solid red;}";
        $this->assertEqual($cssout, $optimiser->process($cssin));

        $cssin = '.one {border:1px solid red;width:20px;} .two {border:1px solid red;height:20px;}';
        $cssout = ".one{border:1px solid red;width:20px;}\n.two{border:1px solid red;height:20px;}";
        $this->assertEqual($cssout, $optimiser->process($cssin));

        $cssin = '.one {color:red;} .two {color:#F00;}';
        $cssout = ".one,\n.two{color:#F00;}";
        $this->assertEqual($cssout, $optimiser->process($cssin));
    }

    protected function check_logic_maintained(css_optimiser $optimiser) {

        $cssin = '.one {color:#123;color:#321;}';
        $cssout = '.one{color:#321;}';
        $this->assertEqual($cssout, $optimiser->process($cssin));

        $cssin = '.one {color:#123; color : #321 ;}';
        $cssout = '.one{color:#321;}';
        $this->assertEqual($cssout, $optimiser->process($cssin));

        $cssin = '.one {color:#123;} .one {color:#321;}';
        $cssout = '.one{color:#321;}';
        $this->assertEqual($cssout, $optimiser->process($cssin));

        $cssin = '.one {color:#123 !important;color:#321;}';
        $cssout = '.one{color:#123 !important;}';
        $this->assertEqual($cssout, $optimiser->process($cssin));

        $cssin = '.one {color:#123 !important;} .one {color:#321;}';
        $cssout = '.one{color:#123 !important;}';
        $this->assertEqual($cssout, $optimiser->process($cssin));

    }

    protected function check_bulk_processing(css_optimiser $optimiser) {
        $cssin = <<<CSS
.test .one {
    margin:5px;
    border:0;
}
.test .one {
    margin: 10px;
    color: red;
}

.test.one {
    margin: 15px;
}

#test .one {margin:  20px;}
#test #one {margin:  25px;}.test #one {margin:  30px;}
  .test    .one      {     background-color: #123;     }
.test.one{border:1px solid blue}.test.one{border-color:green;}
CSS;
        $cssout = $optimiser->process($cssin);

        $this->assertTrue(preg_match('#\.test\s\.one\{[^\}]*margin:10px;#', $cssout));
        $this->assertTrue(preg_match('#\.test\s\.one\{[^\}]*background\-color:\#123;#', $cssout));

        $this->assertTrue(preg_match('#\.test\.one\{[^\}]*margin:15px;#', $cssout));
        $this->assertTrue(preg_match('#\.test\.one\{[^\}]*border:1px solid blue;#', $cssout));

        $this->assertTrue(preg_match('#\#test \.one\{[^\}]*margin:20px;#', $cssout));
        $this->assertTrue(preg_match('#\#test \#one\{[^\}]*margin:25px;#', $cssout));
        $this->assertTrue(preg_match('#\.test \#one\{[^\}]*margin:30px;#', $cssout));
    }
}