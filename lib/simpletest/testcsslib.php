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
 * @package core_css
 * @category css
 * @copyright 2012 Sam Hemelryk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

global $CFG;

require_once($CFG->libdir . '/csslib.php');


/**
 * CSS optimiser test class
 *
 * @package core_css
 * @category css
 * @copyright 2012 Sam Hemelryk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class css_optimiser_test extends UnitTestCase {

    /**
     * Sets up the test class
     */
    public function setUp() {
        global $CFG;
        parent::setUp();
        // We need to disable these if they are enabled to that we can predict
        // the output.
        $CFG->cssoptimiserstats = false;
        $CFG->cssoptimiserpretty = false;
    }

    /**
     * Test the process method
     */
    public function test_process() {
        $optimiser = new css_optimiser;

        $this->check_background($optimiser);
        $this->check_borders($optimiser);
        $this->check_colors($optimiser);
        $this->check_margins($optimiser);
        $this->check_padding($optimiser);
        $this->check_widths($optimiser);

        $this->try_broken_css_found_in_moodle($optimiser);
        $this->try_invalid_css_handling($optimiser);
        $this->try_bulk_processing($optimiser);
        $this->try_break_things($optimiser);
    }

    /**
     * Background colour tests
     * @param css_optimiser $optimiser
     */
    protected function check_background(css_optimiser $optimiser) {

        $cssin = '.test {background-color: #123456;}';
        $cssout = '.test{background:#123456;}';
        $this->assertEqual($cssout, $optimiser->process($cssin));

        $cssin = '.test {background-image: url(\'test.png\');}';
        $cssout = '.test{background-image:url(\'test.png\');}';
        $this->assertEqual($cssout, $optimiser->process($cssin));

        $cssin = '.test {background: #123456 url(\'test.png\') no-repeat top left;}';
        $cssout = '.test{background:#123456 url(\'test.png\') no-repeat top left;}';
        $this->assertEqual($cssout, $optimiser->process($cssin));

        $cssin = '.test {background: url(\'test.png\') no-repeat top left;}.test{background-position: bottom right}.test {background-color:#123456;}';
        $cssout = '.test{background:#123456 url(\'test.png\') no-repeat bottom right;}';
        $this->assertEqual($cssout, $optimiser->process($cssin));

        $cssin = '.test {background: url(   \'test.png\'    )}.test{background: bottom right}.test {background:#123456;}';
        $cssout = '.test{background-image:url(\'test.png\');background-position:bottom right;background-color:#123456;}';
        $this->assertEqual($cssout, $optimiser->process($cssin));

        $cssin = '.test {background-color: #123456;background-repeat: repeat-x; background-position: 100% 0%;}';
        $cssout = '.test{background-color:#123456;background-repeat:repeat-x;background-position:100% 0%;}';
        $this->assertEqual($cssout, $optimiser->process($cssin));

        $cssin = '.tree_item.branch {background-image: url([[pix:t/expanded]]);background-position: 0 10%;background-repeat: no-repeat;}
                  .tree_item.branch.navigation_node {background-image:none;padding-left:0;}';
        $cssout = '.tree_item.branch{background:url([[pix:t/expanded]]) no-repeat 0 10%;} .tree_item.branch.navigation_node{background-image:none;padding-left:0;}';
        $this->assertEqual($cssout, $optimiser->process($cssin));

        $cssin = '.block_tree .tree_item.emptybranch {background-image: url([[pix:t/collapsed_empty]]);background-position: 0% 5%;background-repeat: no-repeat;}
                  .block_tree .collapsed .tree_item.branch {background-image: url([[pix:t/collapsed]]);}';
        $cssout = '.block_tree .tree_item.emptybranch{background:url([[pix:t/collapsed_empty]]) no-repeat 0% 5%;} .block_tree .collapsed .tree_item.branch{background-image:url([[pix:t/collapsed]]);}';
        $this->assertEqual($cssout, $optimiser->process($cssin));

        $cssin = '#nextLink{background:url(data:image/gif;base64,AAAA);}';
        $cssout = '#nextLink{background-image:url(data:image/gif;base64,AAAA);}';
        $this->assertEqual($cssout, $optimiser->process($cssin));

        $cssin = '#nextLink{background-image:url(data:image/gif;base64,AAAA);}';
        $cssout = '#nextLink{background-image:url(data:image/gif;base64,AAAA);}';
        $this->assertEqual($cssout, $optimiser->process($cssin));

        $cssin = '.test {background: #123456 url(data:image/gif;base64,AAAA) no-repeat top left;}';
        $cssout = '.test{background:#123456 url(data:image/gif;base64,AAAA) no-repeat top left;}';
        $this->assertEqual($cssout, $optimiser->process($cssin));
    }

    /**
     * Border tests
     * @param css_optimiser $optimiser
     */
    protected function check_borders(css_optimiser $optimiser) {
        $cssin = '.test {border: 1px solid #654321} .test {border-bottom-color: #123456}';
        $cssout = '.test{border:1px solid;border-color:#654321 #654321 #123456;}';
        $this->assertEqual($cssout, $optimiser->process($cssin));

        $cssin = '.one {border:1px solid red;}';
        $cssout = '.one{border:1px solid #FF0000;}';
        $this->assertEqual($cssout, $optimiser->process($cssin));

        $cssin = '.one {border:1px solid;} .one {border:2px dotted #DDD;}';
        $cssout = '.one{border:2px dotted #DDDDDD;}';
        $this->assertEqual($cssout, $optimiser->process($cssin));

        $cssin = '.one {border:2px dotted #DDD;}.one {border:1px solid;} ';
        $cssout = '.one{border:1px solid #DDDDDD;}';
        $this->assertEqual($cssout, $optimiser->process($cssin));

        $cssin = '.one, .two {border:1px solid red;}';
        $cssout = ".one, .two{border:1px solid #FF0000;}";
        $this->assertEqual($cssout, $optimiser->process($cssin));

        $cssin = '.one, .two {border:0px;}';
        $cssout = ".one, .two{border-width:0;}";
        $this->assertEqual($cssout, $optimiser->process($cssin));

        $cssin = '.one, .two {border-top: 5px solid white;}';
        $cssout = ".one, .two{border-top:5px solid #FFFFFF;}";
        $this->assertEqual($cssout, $optimiser->process($cssin));

        $cssin = '.one {border:1px solid red;} .two {border:1px solid red;}';
        $cssout = ".one, .two{border:1px solid #FF0000;}";
        $this->assertEqual($cssout, $optimiser->process($cssin));

        $cssin = '.one {border:1px solid red;width:20px;} .two {border:1px solid red;height:20px;}';
        $cssout = ".one{width:20px;border:1px solid #FF0000;} .two{height:20px;border:1px solid #FF0000;}";
        $this->assertEqual($cssout, $optimiser->process($cssin));

        $cssin = '.test {border: 1px solid #123456;} .test {border-color: #654321}';
        $cssout = '.test{border:1px solid #654321;}';
        $this->assertEqual($cssout, $optimiser->process($cssin));

        $cssin = '.test {border-width: 1px; border-style: solid; border-color: #123456;}';
        $cssout = '.test{border:1px solid #123456;}';
        $this->assertEqual($cssout, $optimiser->process($cssin));

        $cssin = '.test {border:1px solid #123456;border-top:2px dotted #654321;}';
        $cssout = '.test{border:1px solid #123456;border-top:2px dotted #654321;}';
        $this->assertEqual($cssout, $optimiser->process($cssin));

        $cssin = '.test {border:1px solid #123456;border-left:2px dotted #654321;}';
        $cssout = '.test{border:1px solid #123456;border-left:2px dotted #654321;}';
        $this->assertEqual($cssout, $optimiser->process($cssin));

        $cssin = '.test {border-left:2px dotted #654321;border:1px solid #123456;}';
        $cssout = '.test{border:1px solid #123456;}';
        $this->assertEqual($cssout, $optimiser->process($cssin));

        $cssin = '.test {border:1px solid;border-top-color:#123456;}';
        $cssout = '.test{border:1px solid;border-top-color:#123456;}';
        $this->assertEqual($cssout, $optimiser->process($cssin));

        $cssin = '.test {border:1px solid;border-top-color:#111; border-bottom-color: #222;border-left-color: #333;}';
        $cssout = '.test{border:1px solid;border-top-color:#111;border-bottom-color:#222;border-left-color:#333;}';
        $this->assertEqual($cssout, $optimiser->process($cssin));

        $cssin = '.test {border:1px solid;border-top-color:#111; border-bottom-color: #222;border-left-color: #333;border-right-color:#444;}';
        $cssout = '.test{border:1px solid;border-color:#111 #444 #222 #333;}';
        $this->assertEqual($cssout, $optimiser->process($cssin));

        $cssin = '.generaltable .cell {border-color:#EEEEEE;} .generaltable .cell {border-width: 1px;border-style: solid;}';
        $cssout = '.generaltable .cell{border:1px solid #EEEEEE;}';
        $this->assertEqual($cssout, $optimiser->process($cssin));

        $cssin = '#page-admin-roles-override .rolecap {border:none;border-bottom:1px solid #CECECE;}';
        $cssout = '#page-admin-roles-override .rolecap{border-top:0;border-right:0;border-bottom:1px solid #CECECE;border-left:0;}';
        $this->assertEqual($cssout, $optimiser->process($cssin));
    }

    /**
     * Test colour styles
     * @param css_optimiser $optimiser
     */
    protected function check_colors(css_optimiser $optimiser) {
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

        $cssin = '.one {color:red;} .two {color:#F00;}';
        $cssout = ".one, .two{color:#F00;}";
        $this->assertEqual($cssout, $optimiser->process($cssin));

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

        $cssin = '.one {color:rgb(255, 128, 1)}';
        $cssout = '.one{color:rgb(255, 128, 1);}';
        $this->assertEqual($cssout, $optimiser->process($cssin));

        $cssin = '.one {color:rgba(255, 128, 1, 0.5)}';
        $cssout = '.one{color:rgba(255, 128, 1, 0.5);}';
        $this->assertEqual($cssout, $optimiser->process($cssin));

        $cssin = '.one {color:hsl(120, 65%, 75%)}';
        $cssout = '.one{color:hsl(120, 65%, 75%);}';
        $this->assertEqual($cssout, $optimiser->process($cssin));

        $cssin = '.one {color:hsla(120,65%,75%,0.5)}';
        $cssout = '.one{color:hsla(120,65%,75%,0.5);}';
        $this->assertEqual($cssout, $optimiser->process($cssin));

        // Try some invalid colours to make sure we don't mangle them.
        $css = 'div#some{color:#1;}';
        $this->assertEqual($css, $optimiser->process($css));

        $css = 'div#some{color:#12;}';
        $this->assertEqual($css, $optimiser->process($css));

        $css = 'div#some{color:#1234;}';
        $this->assertEqual($css, $optimiser->process($css));

        $css = 'div#some{color:#12345;}';
        $this->assertEqual($css, $optimiser->process($css));
    }

    protected function check_widths(css_optimiser $optimiser) {
        $cssin  = '.css {width:0}';
        $cssout = '.css{width:0;}';
        $this->assertEqual($cssout, $optimiser->process($cssin));

        $cssin  = '.css {width:0px}';
        $cssout = '.css{width:0;}';
        $this->assertEqual($cssout, $optimiser->process($cssin));

        $cssin  = '.css {width:0em}';
        $cssout = '.css{width:0;}';
        $this->assertEqual($cssout, $optimiser->process($cssin));

        $cssin  = '.css {width:0pt}';
        $cssout = '.css{width:0;}';
        $this->assertEqual($cssout, $optimiser->process($cssin));

        $cssin  = '.css {width:0mm}';
        $cssout = '.css{width:0;}';
        $this->assertEqual($cssout, $optimiser->process($cssin));

        $cssin  = '.css {width:100px}';
        $cssout = '.css{width:100px;}';
        $this->assertEqual($cssout, $optimiser->process($cssin));
    }

    /**
     * Test margin styles
     * @param css_optimiser $optimiser
     */
    protected function check_margins(css_optimiser $optimiser) {
        $cssin = '.one {margin: 1px 2px 3px 4px}';
        $cssout = '.one{margin:1px 2px 3px 4px;}';
        $this->assertEqual($cssout, $optimiser->process($cssin));

        $cssin = '.one {margin-top:1px; margin-left:4px; margin-right:2px; margin-bottom: 3px;}';
        $cssout = '.one{margin:1px 2px 3px 4px;}';
        $this->assertEqual($cssout, $optimiser->process($cssin));

        $cssin = '.one {margin-top:1px; margin-left:4px;}';
        $cssout = '.one{margin-top:1px;margin-left:4px;}';
        $this->assertEqual($cssout, $optimiser->process($cssin));

        $cssin = '.one {margin:1px; margin-left:4px;}';
        $cssout = '.one{margin:1px 1px 1px 4px;}';
        $this->assertEqual($cssout, $optimiser->process($cssin));

        $cssin = '.one {margin:1px; margin-bottom:4px;}';
        $cssout = '.one{margin:1px 1px 4px;}';
        $this->assertEqual($cssout, $optimiser->process($cssin));

        $cssin = '.one, .two, .one.two, .one .two {margin:0;} .one.two {margin:0 7px;}';
        $cssout = '.one, .two, .one .two{margin:0;} .one.two{margin:0 7px;}';
        $this->assertEqual($cssout, $optimiser->process($cssin));
    }

    /**
     * Test padding styles
     *
     * @param css_optimiser $optimiser
     */
    protected function check_padding(css_optimiser $optimiser) {
        $cssin = '.one {margin: 1px 2px 3px 4px}';
        $cssout = '.one{margin:1px 2px 3px 4px;}';
        $this->assertEqual($cssout, $optimiser->process($cssin));

        $cssin = '.one {margin-top:1px; margin-left:4px; margin-right:2px; margin-bottom: 3px;}';
        $cssout = '.one{margin:1px 2px 3px 4px;}';
        $this->assertEqual($cssout, $optimiser->process($cssin));

        $cssin = '.one {margin-top:1px; margin-left:4px;}';
        $cssout = '.one{margin-top:1px;margin-left:4px;}';
        $this->assertEqual($cssout, $optimiser->process($cssin));

        $cssin = '.one {margin:1px; margin-left:4px;}';
        $cssout = '.one{margin:1px 1px 1px 4px;}';
        $this->assertEqual($cssout, $optimiser->process($cssin));

        $cssin = '.one {margin:1px; margin-bottom:4px;}';
        $cssout = '.one{margin:1px 1px 4px;}';
        $this->assertEqual($cssout, $optimiser->process($cssin));

        $cssin = '.one {margin:0 !important;}';
        $cssout = '.one{margin:0 !important;}';
        $this->assertEqual($cssout, $optimiser->process($cssin));

        $cssin = '.one {padding:0 !important;}';
        $cssout = '.one{padding:0 !important;}';
        $this->assertEqual($cssout, $optimiser->process($cssin));

        $cssin = '.one, .two, .one.two, .one .two {margin:0;} .one.two {margin:0 7px;}';
        $cssout = '.one, .two, .one .two{margin:0;} .one.two{margin:0 7px;}';
        $this->assertEqual($cssout, $optimiser->process($cssin));
    }

    /**
     * Test some totally invalid CSS optimisation
     *
     * @param css_optimiser $optimiser
     */
    protected function try_invalid_css_handling(css_optimiser $optimiser) {

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
        $cssout = '.one{background:#F00;}';
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
        $cssout = '.one{background:#F00;border-color:#00F;}';
        $this->assertEqual($cssout, $optimiser->process($cssin));

        $cssin  = '{background-color:#123456;color:red;}{color:green;}';
        $cssout = "{color:#008000;background:#123456;}";
        $this->assertEqual($cssout, $optimiser->process($cssin));

        $cssin  = '.one {color:red;} {color:green;} .one {background-color:blue;}';
        $cssout = ".one{color:#F00;background:#00F;} {color:#008000;}";
        $this->assertEqual($cssout, $optimiser->process($cssin));
    }

    /**
     * Try to break some things
     * @param css_optimiser $optimiser
     */
    protected function try_break_things(css_optimiser $optimiser) {
        // Wildcard test
        $cssin  = '* {color: black;}';
        $cssout = '*{color:#000;}';
        $this->assertEqual($cssout, $optimiser->process($cssin));

        // Wildcard test
        $cssin  = '.one * {color: black;}';
        $cssout = '.one *{color:#000;}';
        $this->assertEqual($cssout, $optimiser->process($cssin));

        // Wildcard test
        $cssin  = '* .one * {color: black;}';
        $cssout = '* .one *{color:#000;}';
        $this->assertEqual($cssout, $optimiser->process($cssin));

        // Wildcard test
        $cssin  = '*,* {color: black;}';
        $cssout = '*{color:#000;}';
        $this->assertEqual($cssout, $optimiser->process($cssin));

        // Wildcard test
        $cssin  = '*, * .one {color: black;}';
        $cssout = "*,\n* .one{color:#000;}";
        $this->assertEqual($cssout, $optimiser->process($cssin));

        // Wildcard test
        $cssin  = '*, *.one {color: black;}';
        $cssout = "*,\n*.one{color:#000;}";
        $this->assertEqual($cssout, $optimiser->process($cssin));

        // Psedo test
        $cssin  = '.one:before {color: black;}';
        $cssout = '.one:before{color:#000;}';
        $this->assertEqual($cssout, $optimiser->process($cssin));

        // Psedo test
        $cssin  = '.one:after {color: black;}';
        $cssout = '.one:after{color:#000;}';
        $this->assertEqual($cssout, $optimiser->process($cssin));

        // Psedo test
        $cssin  = '.one:onclick {color: black;}';
        $cssout = '.one:onclick{color:#000;}';
        $this->assertEqual($cssout, $optimiser->process($cssin));

        // Test complex CSS rules that don't really exist but mimic other CSS rules
        $cssin  = '.one {master-of-destruction: explode(\' \', "What madness");}';
        $cssout = '.one{master-of-destruction:explode(\' \', "What madness");}';
        $this->assertEqual($cssout, $optimiser->process($cssin));

        // Test some complex IE css... I couldn't even think of a more complext solution
        // than the CSS they came up with.
        $cssin  = 'a { opacity: 0.5; -ms-filter:"progid:DXImageTransform.Microsoft.Alpha(Opacity=50)"; filter: alpha(opacity=50); }';
        $cssout = 'a{opacity:0.5;-ms-filter:"progid:DXImageTransform.Microsoft.Alpha(Opacity=50)";filter:alpha(opacity=50);}';
        $this->assertEqual($cssout, $optimiser->process($cssin));
    }

    /**
     * A bulk processing test
     * @param css_optimiser $optimiser
     */
    protected function try_bulk_processing(css_optimiser $optimiser) {
        global $CFG;
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

@media print {
    #test .one {margin: 35px;}
}

@media print {
    #test .one {margin: 40px;color: #123456;}
    #test #one {margin: 45px;}
}

@media print,screen {
    #test .one {color: #654321;}
}

#test .one,
#new.style {color:#000;}
CSS;

        $cssout = <<<CSS
.test .one{color:#F00;margin:10px;border-width:0;background:#123;}
.test.one{margin:15px;border:1px solid #008000;}
#test .one{color:#000;margin:20px;}
#test #one{margin:25px;}
.test #one{margin:30px;}
#new.style{color:#000;}


@media print {
  #test .one{color:#123456;margin:40px;}
  #test #one{margin:45px;}
}

@media print,screen {
  #test .one{color:#654321;}
}
CSS;
        $CFG->cssoptimiserpretty = 1;
        $this->assertEqual($optimiser->process($cssin), $cssout);
    }

    /**
     * Test CSS colour matching
     */
    public function test_css_is_colour() {
        // First lets test hex colours
        $this->assertTrue(css_is_colour('#123456'));
        $this->assertTrue(css_is_colour('#123'));
        $this->assertTrue(css_is_colour('#ABCDEF'));
        $this->assertTrue(css_is_colour('#ABC'));
        $this->assertTrue(css_is_colour('#abcdef'));
        $this->assertTrue(css_is_colour('#abc'));
        $this->assertTrue(css_is_colour('#aBcDeF'));
        $this->assertTrue(css_is_colour('#aBc'));
        $this->assertTrue(css_is_colour('#1a2Bc3'));
        $this->assertTrue(css_is_colour('#1Ac'));

        // Note the following two colour's arn't really colours but browsers process
        // them still.
        $this->assertTrue(css_is_colour('#A'));
        $this->assertTrue(css_is_colour('#12'));
        // Having four or five characters however are not valid colours and
        // browsers don't parse them. They need to fail so that broken CSS
        // stays broken after optimisation.
        $this->assertFalse(css_is_colour('#1234'));
        $this->assertFalse(css_is_colour('#12345'));

        $this->assertFalse(css_is_colour('#BCDEFG'));
        $this->assertFalse(css_is_colour('#'));
        $this->assertFalse(css_is_colour('#0000000'));
        $this->assertFalse(css_is_colour('#132-245'));
        $this->assertFalse(css_is_colour('#13 23 43'));
        $this->assertFalse(css_is_colour('123456'));

        // Next lets test real browser mapped colours
        $this->assertTrue(css_is_colour('black'));
        $this->assertTrue(css_is_colour('blue'));
        $this->assertTrue(css_is_colour('BLACK'));
        $this->assertTrue(css_is_colour('Black'));
        $this->assertTrue(css_is_colour('bLACK'));
        $this->assertTrue(css_is_colour('mediumaquamarine'));
        $this->assertTrue(css_is_colour('mediumAquamarine'));
        $this->assertFalse(css_is_colour('monkey'));
        $this->assertFalse(css_is_colour(''));
        $this->assertFalse(css_is_colour('not a colour'));

        // Next lets test rgb(a) colours
        $this->assertTrue(css_is_colour('rgb(255,255,255)'));
        $this->assertTrue(css_is_colour('rgb(0, 0, 0)'));
        $this->assertTrue(css_is_colour('RGB (255, 255   ,    255)'));
        $this->assertTrue(css_is_colour('rgba(0,0,0,0)'));
        $this->assertTrue(css_is_colour('RGBA(255,255,255,1)'));
        $this->assertTrue(css_is_colour('rgbA(255,255,255,0.5)'));
        $this->assertFalse(css_is_colour('rgb(-255,-255,-255)'));
        $this->assertFalse(css_is_colour('rgb(256,-256,256)'));

        // Now lets test HSL colours
        $this->assertTrue(css_is_colour('hsl(0,0%,100%)'));
        $this->assertTrue(css_is_colour('hsl(180, 0%, 10%)'));
        $this->assertTrue(css_is_colour('hsl (360, 100%   ,    95%)'));

        // Finally test the special values
        $this->assertTrue(css_is_colour('inherit'));
    }

    /**
     * Test the css_is_width function
     */
    public function test_css_is_width() {

        $this->assertTrue(css_is_width('0'));
        $this->assertTrue(css_is_width('0px'));
        $this->assertTrue(css_is_width('0em'));
        $this->assertTrue(css_is_width('199px'));
        $this->assertTrue(css_is_width('199em'));
        $this->assertTrue(css_is_width('199%'));
        $this->assertTrue(css_is_width('-1'));
        $this->assertTrue(css_is_width('-1px'));
        $this->assertTrue(css_is_width('auto'));
        $this->assertTrue(css_is_width('inherit'));

        $this->assertFalse(css_is_width('-'));
        $this->assertFalse(css_is_width('bananas'));
        $this->assertFalse(css_is_width(''));
        $this->assertFalse(css_is_width('top'));
    }

    /**
     * This function tests some of the broken crazy CSS we have in Moodle.
     * For each of these things the value needs to be corrected if we can be 100%
     * certain what is going wrong, Or it needs to be left as is.
     *
     * @param css_optimiser $optimiser
     */
    public function try_broken_css_found_in_moodle(css_optimiser $optimiser) {
        // Notice how things are out of order here but that they get corrected
        $cssin = '.test {background:url([[pix:theme|pageheaderbgred]]) top center no-repeat}';
        $cssout = '.test{background:url([[pix:theme|pageheaderbgred]]) no-repeat top center;}';
        $this->assertEqual($cssout, $optimiser->process($cssin));

        // Cursor hand isn't valid
        $cssin  = '.test {cursor: hand;}';
        $cssout = '.test{cursor:hand;}';
        $this->assertEqual($cssout, $optimiser->process($cssin));

        // Zoom property isn't valid
        $cssin  = '.test {zoom: 1;}';
        $cssout = '.test{zoom:1;}';
        $this->assertEqual($cssout, $optimiser->process($cssin));

        // Left isn't a valid position property
        $cssin  = '.test {position: left;}';
        $cssout = '.test{position:left;}';
        $this->assertEqual($cssout, $optimiser->process($cssin));

        // The dark red color isn't a valid HTML color but has a standardised
        // translation of #8B0000
        $cssin  = '.test {color: darkred;}';
        $cssout = '.test{color:#8B0000;}';
        $this->assertEqual($cssout, $optimiser->process($cssin));

        // You can't use argb colours as border colors
        $cssin  = '.test {border-bottom: 1px solid rgba(0,0,0,0.25);}';
        $cssout = '.test{border-bottom:1px solid rgba(0,0,0,0.25);}';
        $this->assertEqual($cssout, $optimiser->process($cssin));

        // Opacity with annoying IE equivilants....
        $cssin  = '.test {opacity: 0.5; -ms-filter:"progid:DXImageTransform.Microsoft.Alpha(Opacity=50)"; filter: alpha(opacity=50);}';
        $cssout = '.test{opacity:0.5;-ms-filter:"progid:DXImageTransform.Microsoft.Alpha(Opacity=50)";filter:alpha(opacity=50);}';
        $this->assertEqual($cssout, $optimiser->process($cssin));
    }
}