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
 * @package   core_css
 * @category  phpunit
 * @copyright 2012 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

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
class css_optimiser_testcase extends advanced_testcase {

    protected $optimiser;

    public function get_optimiser() {
        if (!$this->optimiser instanceof css_optimiser) {
            $this->optimiser = new css_optimiser;
        }
        return $this->optimiser;
    }

    /**
     * Sets up the test class
     */
    protected function setUp() {
        global $CFG;
        parent::setUp();
        // We need to disable these if they are enabled to that we can predict
        // the output.
        $CFG->cssoptimiserstats = false;
        $CFG->cssoptimiserpretty = false;

        $this->resetAfterTest(true);
    }

    /**
     * Test the process method
     */
    public function test_process() {
        $optimiser = new css_optimiser;

        $this->try_broken_css_found_in_moodle();
        $this->try_invalid_css_handling();
        $this->try_bulk_processing();
        $this->try_break_things();
        $this->try_media_rules();
        $this->try_keyframe_css_animation();
    }

    /**
     * Background colour tests
     *
     * When testing background styles it is important to understand how the background shorthand works.
     * background shorthand allows the following styles to be specified in a single "background" declaration:
     *   - background-color
     *   - background-image
     *   - background-repeat
     *   - background-attachment
     *   - background-position
     *
     * If the background shorthand is used it can contain one or more of those (preferabbly in that order).
     * Important it does not need to contain all of them.
     * However even if it doesn't contain values for all styles all of the styles will be affected.
     * If a style is missed from the shorthand background style but has an existing value in the rule then the existing value
     * is cleared.
     *
     * For example:
     *      .test {background: url(\'test.png\');background: bottom right;background:#123456;}
     * will result in:
     *      .test {background:#123456;}
     *
     * And:
     *      .test {background-image: url(\'test.png\');background:#123456;}
     * will result in:
     *      .test {background:#123456;}
     *
     * @param css_optimiser $optimiser
     */
    public function test_background() {
        $optimiser = $this->get_optimiser();

        $cssin = '.test {background-color: #123456;}';
        $cssout = '.test{background-color:#123456;}';
        $this->assertEquals($cssout, $optimiser->process($cssin));

        $cssin = '.test {background: #123456;}';
        $cssout = '.test{background:#123456;}';
        $this->assertEquals($cssout, $optimiser->process($cssin));

        $cssin = '.test {background-image: url(\'test.png\');}';
        $cssout = '.test{background-image:url(\'test.png\');}';
        $this->assertEquals($cssout, $optimiser->process($cssin));

        $cssin = '.test {background: #123456 url(\'test.png\') no-repeat top left;}';
        $cssout = '.test{background:#123456 url(\'test.png\') no-repeat top left;}';
        $this->assertEquals($cssout, $optimiser->process($cssin));

        // Check out this for madness, background position and background-repeat have been reversed
        $cssin = '.test {background: #123456 url(\'test.png\') center no-repeat;}';
        $cssout = '.test{background:#123456 url(\'test.png\') no-repeat center;}';
        $this->assertEquals($cssout, $optimiser->process($cssin));

        $cssin = '.test {background: url(\'test.png\') no-repeat top left;}.test{background-position: bottom right}.test {background-color:#123456;}';
        $cssout = '.test{background:#123456 url(\'test.png\') no-repeat bottom right;}';
        $this->assertEquals($cssout, $optimiser->process($cssin));

        $cssin = '.test {background: url(   \'test.png\'    )}.test{background: bottom right}.test {background:#123456;}';
        $cssout = '.test{background:#123456;}';
        $this->assertEquals($cssout, $optimiser->process($cssin));

        $cssin = '.test {background-image: url(\'test.png\');background:#123456;}';
        $cssout = '.test{background:#123456;}';
        $this->assertEquals($cssout, $optimiser->process($cssin));

        $cssin = '.test {background-color: #123456;background-repeat: repeat-x; background-position: 100% 0%;}';
        $cssout = '.test{background-color:#123456;background-repeat:repeat-x;background-position:100% 0%;}';
        $this->assertEquals($cssout, $optimiser->process($cssin));

        $cssin = '.tree_item.branch {background-image: url([[pix:t/expanded]]);background-position: 0 10%;background-repeat: no-repeat;}
                  .tree_item.branch.navigation_node {background-image:none;padding-left:0;}';
        $cssout = '.tree_item.branch{background-image:url([[pix:t/expanded]]);background-position:0 10%;background-repeat:no-repeat;} .tree_item.branch.navigation_node{background-image:none;padding-left:0;}';
        $this->assertEquals($cssout, $optimiser->process($cssin));

        $cssin = '#nextLink{background:url(data:image/gif;base64,AAAA);}';
        $cssout = '#nextLink{background:url(data:image/gif;base64,AAAA);}';
        $this->assertEquals($cssout, $optimiser->process($cssin));

        $cssin = '#nextLink{background-image:url(data:image/gif;base64,AAAA);}';
        $cssout = '#nextLink{background-image:url(data:image/gif;base64,AAAA);}';
        $this->assertEquals($cssout, $optimiser->process($cssin));

        $cssin = '.test {background: #123456 url(data:image/gif;base64,AAAA) no-repeat top left;}';
        $cssout = '.test{background:#123456 url(data:image/gif;base64,AAAA) no-repeat top left;}';
        $this->assertEquals($cssout, $optimiser->process($cssin));

        $cssin = '#test {background-image:none;background-position:right center;background-repeat:no-repeat;}';
        $cssout = '#test{background-image:none;background-position:right center;background-repeat:no-repeat;}';
        $this->assertEquals($cssout, $optimiser->process($cssin));

        $cssin = '.test {background: url([[pix:theme|photos]]) no-repeat 50% 50%;background-size: 40px 40px;-webkit-background-size: 40px 40px;}';
        $cssout = '.test{background:url([[pix:theme|photos]]) no-repeat 50% 50%;background-size:40px 40px;-webkit-background-size:40px 40px;}';
        $this->assertEquals($cssout, $optimiser->process($cssin));

        $cssin = '.test{background-image: -o-linear-gradient(#3c3c3c, #111);background-image: linear-gradient(#3c3c3c, #111);}';
        $cssout = '.test{background-image:-o-linear-gradient(#3c3c3c, #111);background-image:linear-gradient(#3c3c3c, #111);}';
        $this->assertEquals($cssout, $optimiser->process($cssin));

        $cssin = '.test{background-image: -moz-linear-gradient(#3c3c3c, #111);background-image: -webkit-linear-gradient(#3c3c3c, #111);background-image: -o-linear-gradient(#3c3c3c, #111);background-image: linear-gradient(#3c3c3c, #111);background-image: url(/test.png);}';
        $cssout = '.test{background-image:-moz-linear-gradient(#3c3c3c, #111);background-image:-webkit-linear-gradient(#3c3c3c, #111);background-image:-o-linear-gradient(#3c3c3c, #111);background-image:linear-gradient(#3c3c3c, #111);background-image:url(/test.png);}';
        $cssout = '.test{background-image:url(/test.png);background-image:-moz-linear-gradient(#3c3c3c, #111);background-image:-webkit-linear-gradient(#3c3c3c, #111);background-image:-o-linear-gradient(#3c3c3c, #111);background-image:linear-gradient(#3c3c3c, #111);}';
        $this->assertEquals($cssout, $optimiser->process($cssin));

        $cssin = '.test{background:#CCC; background-image: url(test.png);}';
        $cssout = '.test{background:#CCC url(test.png);}';
        $this->assertEquals($cssout, $optimiser->process($cssin));

        $cssin = '.test{background:#CCC; background-image: linear-gradient(#3c3c3c, #111);}';
        $cssout = '.test{background:#CCC;background-image:linear-gradient(#3c3c3c, #111);}';
        $this->assertEquals($cssout, $optimiser->process($cssin));

        $cssin = '.test{background:#CCC; background-image: -o-linear-gradient(#3c3c3c, #111);background-image: linear-gradient(#3c3c3c, #111);}';
        $cssout = '.test{background:#CCC;background-image:-o-linear-gradient(#3c3c3c, #111);background-image:linear-gradient(#3c3c3c, #111);}';
        $this->assertEquals($cssout, $optimiser->process($cssin));

        $cssin = '#newmessageoverlay{font-weight: normal; border: 1px solid #222; background: #444; color: #ddd; text-shadow: 0 -1px 0px #000; background-image: -moz-linear-gradient(top, #333 0%, #333 5%, #444 15%, #444 60%, #222 100%); background-image: -webkit-gradient(linear, center top, center bottom, color-stop(0, #333), color-stop(5%, #333), color-stop(15%, #444), color-stop(60%, #444), color-stop(1, #222)); -ms-filter: "progid:DXImageTransform.Microsoft.gradient(startColorStr=\'#333333\', EndColorStr=\'#222222\')"; padding:20px; padding-left: 0px; padding-right: 10px; position: inherit; z-index: 9999; width: 90%; margin-left: auto; margin-right: auto; height: 100%;}';
        $cssout = '#newmessageoverlay{font-weight:normal;border:1px solid #222;background:#444;color:#DDD;text-shadow:0 -1px 0px #000;-ms-filter:"progid:DXImageTransform.Microsoft.gradient(startColorStr=\'#333333\', EndColorStr=\'#222222\')";padding:20px 10px 20px 0;position:inherit;z-index:9999;width:90%;margin-left:auto;margin-right:auto;height:100%;background-image:-moz-linear-gradient(top, #333 0%, #333 5%, #444 15%, #444 60%, #222 100%);background-image:-webkit-gradient(linear, center top, center bottom, color-stop(0, #333), color-stop(5%, #333), color-stop(15%, #444), color-stop(60%, #444), color-stop(1, #222));}';
        $this->assertEquals($cssout, $optimiser->process($cssin));

        $cssin = '.userenrolment {background-color:inherit !important;background: inherit !important;}';
        $cssout = '.userenrolment{background:inherit !important;}';
        $this->assertEquals($cssout, $optimiser->process($cssin));

        $cssin = '.userenrolment {background-image:url(test.png) !important;background: inherit !important;}';
        $cssout = '.userenrolment{background:inherit !important;}';
        $this->assertEquals($cssout, $optimiser->process($cssin));

        $cssin = '.userenrolment {background: inherit !important;background-image:url(test.png) !important;}';
        $cssout = '.userenrolment{background:inherit url(test.png) !important;}';
        $this->assertEquals($cssout, $optimiser->process($cssin));

        $cssin = '.userenrolment {background: inherit !important;background-image:url(test.png);}';
        $cssout = '.userenrolment{background:inherit !important;}';
        $this->assertEquals($cssout, $optimiser->process($cssin));

        $css = '#filesskin .yui3-widget-hd{background:#CCC;background:-webkit-gradient(linear, left top, left bottom, from(#FFFFFF), to(#CCCCCC));background:-moz-linear-gradient(top, #FFFFFF, #CCCCCC);}';
        $this->assertEquals($css, $optimiser->process($css));

        $css = '.userenrolment{background:-moz-linear-gradient(top, #FFFFFF, #CCCCCC) !important;}';
        $this->assertEquals($css, $optimiser->process($css));

        $css = '.userenrolment{background:#CCC !important;background:-webkit-gradient(linear, left top, left bottom, from(#FFFFFF), to(#CCCCCC)) !important;background:-moz-linear-gradient(top, #FFFFFF, #CCCCCC) !important;}';
        $this->assertEquals($css, $optimiser->process($css));

        $cssin = '.userenrolment{background:-moz-linear-gradient(top, #FFFFFF, #CCCCCC) !important;}.userenrolment {background: #CCCCCC!important;background: -webkit-gradient(linear, left top, left bottom, from(#FFFFFF), to(#CCCCCC))!important;}';
        $cssout = '.userenrolment{background:#CCC !important;background:-moz-linear-gradient(top, #FFFFFF, #CCCCCC) !important;background:-webkit-gradient(linear, left top, left bottom, from(#FFFFFF), to(#CCCCCC)) !important;}';
        $this->assertEquals($cssout, $optimiser->process($cssin));
    }

    /**
     * Border tests
     * @param css_optimiser $optimiser
     */
    public function test_borders() {
        $optimiser = $this->get_optimiser();

        $cssin = '.test {border: 1px solid #654321} .test {border-bottom-color: #123456}';
        $cssout = '.test{border:1px solid;border-color:#654321 #654321 #123456;}';
        $this->assertEquals($cssout, $optimiser->process($cssin));

        $cssin = '.one {border:1px solid red;}';
        $cssout = '.one{border:1px solid #F00;}';
        $this->assertEquals($cssout, $optimiser->process($cssin));

        $cssin = '.one {border:1px solid;} .one {border:2px dotted #DDD;}';
        $cssout = '.one{border:2px dotted #DDD;}';
        $this->assertEquals($cssout, $optimiser->process($cssin));

        $cssin = '.one {border:2px dotted #DDD;}.one {border:1px solid;} ';
        $cssout = '.one{border:1px solid #DDD;}';
        $this->assertEquals($cssout, $optimiser->process($cssin));

        $cssin = '.one, .two {border:1px solid red;}';
        $cssout = ".one, .two{border:1px solid #F00;}";
        $this->assertEquals($cssout, $optimiser->process($cssin));

        $cssin = '.one, .two {border:0px;}';
        $cssout = ".one, .two{border-width:0;}";
        $this->assertEquals($cssout, $optimiser->process($cssin));

        $cssin = '.one, .two {border: thin;}';
        $cssout = ".one, .two{border-width:thin;}";
        $this->assertEquals($cssout, $optimiser->process($cssin));

        $cssin = '.one, .two {border: thin solid black;}';
        $cssout = ".one, .two{border:thin solid #000;}";
        $this->assertEquals($cssout, $optimiser->process($cssin));

        $cssin = '.one, .two {border-top: 5px solid white;}';
        $cssout = ".one, .two{border-top:5px solid #FFF;}";
        $this->assertEquals($cssout, $optimiser->process($cssin));

        $cssin = '.one {border:1px solid red;} .two {border:1px solid red;}';
        $cssout = ".one, .two{border:1px solid #F00;}";
        $this->assertEquals($cssout, $optimiser->process($cssin));

        $cssin = '.one {border:1px solid red;width:20px;} .two {border:1px solid red;height:20px;}';
        $cssout = ".one{border:1px solid #F00;width:20px;} .two{border:1px solid #F00;height:20px;}";
        $this->assertEquals($cssout, $optimiser->process($cssin));

        $cssin = '.test {border: 1px solid #123456;} .test {border-color: #654321}';
        $cssout = '.test{border:1px solid #654321;}';
        $this->assertEquals($cssout, $optimiser->process($cssin));

        $cssin = '.test {border-width: 1px; border-style: solid; border-color: #123456;}';
        $cssout = '.test{border:1px solid #123456;}';
        $this->assertEquals($cssout, $optimiser->process($cssin));

        $cssin = '.test {border:1px solid #123456;border-top:2px dotted #654321;}';
        $cssout = '.test{border:1px solid #123456;border-top:2px dotted #654321;}';
        $this->assertEquals($cssout, $optimiser->process($cssin));

        $cssin = '.test {border:1px solid #123456;border-left:2px dotted #654321;}';
        $cssout = '.test{border:1px solid #123456;border-left:2px dotted #654321;}';
        $this->assertEquals($cssout, $optimiser->process($cssin));

        $cssin = '.test {border-left:2px dotted #654321;border:1px solid #123456;}';
        $cssout = '.test{border:1px solid #123456;}';
        $this->assertEquals($cssout, $optimiser->process($cssin));

        $cssin = '.test {border:1px solid;border-top-color:#123456;}';
        $cssout = '.test{border:1px solid;border-top-color:#123456;}';
        $this->assertEquals($cssout, $optimiser->process($cssin));

        $cssin = '.test {border:1px solid;border-top-color:#111; border-bottom-color: #222;border-left-color: #333;}';
        $cssout = '.test{border:1px solid;border-top-color:#111;border-bottom-color:#222;border-left-color:#333;}';
        $this->assertEquals($cssout, $optimiser->process($cssin));

        $cssin = '.test {border:1px solid;border-top-color:#111; border-bottom-color: #222;border-left-color: #333;border-right-color:#444;}';
        $cssout = '.test{border:1px solid;border-color:#111 #444 #222 #333;}';
        $this->assertEquals($cssout, $optimiser->process($cssin));

        $cssin = '.generaltable .cell {border-color:#EEE;} .generaltable .cell {border-width: 1px;border-style: solid;}';
        $cssout = '.generaltable .cell{border:1px solid #EEE;}';
        $this->assertEquals($cssout, $optimiser->process($cssin));

        $cssin = '#page-admin-roles-override .rolecap {border:none;border-bottom:1px solid #CECECE;}';
        $cssout = '#page-admin-roles-override .rolecap{border-top:0;border-right:0;border-bottom:1px solid #CECECE;border-left:0;}';
        $this->assertEquals($cssout, $optimiser->process($cssin));
    }

    /**
     * Test colour styles
     * @param css_optimiser $optimiser
     */
    public function test_colors() {
        $optimiser = $this->get_optimiser();

        $css = '.css{}';
        $this->assertEquals($css, $optimiser->process($css));

        $css = '.css{color:#123456;}';
        $this->assertEquals($css, $optimiser->process($css));

        $css = '#some{color:#123456;}';
        $this->assertEquals($css, $optimiser->process($css));

        $css = 'div{color:#123456;}';
        $this->assertEquals($css, $optimiser->process($css));

        $css = 'div.css{color:#123456;}';
        $this->assertEquals($css, $optimiser->process($css));

        $css = 'div#some{color:#123456;}';
        $this->assertEquals($css, $optimiser->process($css));

        $css = 'div[type=blah]{color:#123456;}';
        $this->assertEquals($css, $optimiser->process($css));

        $css = 'div.css[type=blah]{color:#123456;}';
        $this->assertEquals($css, $optimiser->process($css));

        $css = 'div#some[type=blah]{color:#123456;}';
        $this->assertEquals($css, $optimiser->process($css));

        $css = '#some.css[type=blah]{color:#123456;}';
        $this->assertEquals($css, $optimiser->process($css));

        $css = '#some .css[type=blah]{color:#123456;}';
        $this->assertEquals($css, $optimiser->process($css));

        $cssin = '.one {color:red;} .two {color:#F00;}';
        $cssout = ".one, .two{color:#F00;}";
        $this->assertEquals($cssout, $optimiser->process($cssin));

        $cssin = '.one {color:#123;color:#321;}';
        $cssout = '.one{color:#321;}';
        $this->assertEquals($cssout, $optimiser->process($cssin));

        $cssin = '.one {color:#123; color : #321 ;}';
        $cssout = '.one{color:#321;}';
        $this->assertEquals($cssout, $optimiser->process($cssin));

        $cssin = '.one {color:#123;} .one {color:#321;}';
        $cssout = '.one{color:#321;}';
        $this->assertEquals($cssout, $optimiser->process($cssin));

        $cssin = '.one {color:#123 !important;color:#321;}';
        $cssout = '.one{color:#123 !important;}';
        $this->assertEquals($cssout, $optimiser->process($cssin));

        $cssin = '.one {color:#123 !important;} .one {color:#321;}';
        $cssout = '.one{color:#123 !important;}';
        $this->assertEquals($cssout, $optimiser->process($cssin));

        $cssin = '.one {color:#123!important;} .one {color:#321;}';
        $cssout = '.one{color:#123 !important;}';
        $this->assertEquals($cssout, $optimiser->process($cssin));

        $cssin = '.one {color:rgb(255, 128, 1)}';
        $cssout = '.one{color:rgb(255, 128, 1);}';
        $this->assertEquals($cssout, $optimiser->process($cssin));

        $cssin = '.one {color:rgba(255, 128, 1, 0.5)}';
        $cssout = '.one{color:rgba(255, 128, 1, 0.5);}';
        $this->assertEquals($cssout, $optimiser->process($cssin));

        $cssin = '.one {color:hsl(120, 65%, 75%)}';
        $cssout = '.one{color:hsl(120, 65%, 75%);}';
        $this->assertEquals($cssout, $optimiser->process($cssin));

        $cssin = '.one {color:hsla(120,65%,75%,0.5)}';
        $cssout = '.one{color:hsla(120,65%,75%,0.5);}';
        $this->assertEquals($cssout, $optimiser->process($cssin));

        // Try some invalid colours to make sure we don't mangle them.
        $css = 'div#some{color:#1;}';
        $this->assertEquals($css, $optimiser->process($css));

        $css = 'div#some{color:#12;}';
        $this->assertEquals($css, $optimiser->process($css));

        $css = 'div#some{color:#1234;}';
        $this->assertEquals($css, $optimiser->process($css));

        $css = 'div#some{color:#12345;}';
        $this->assertEquals($css, $optimiser->process($css));
    }

    public function test_widths() {
        $optimiser = $this->get_optimiser();

        $cssin  = '.css {width:0}';
        $cssout = '.css{width:0;}';
        $this->assertEquals($cssout, $optimiser->process($cssin));

        $cssin  = '.css {width:0px}';
        $cssout = '.css{width:0;}';
        $this->assertEquals($cssout, $optimiser->process($cssin));

        $cssin  = '.css {width:0em}';
        $cssout = '.css{width:0;}';
        $this->assertEquals($cssout, $optimiser->process($cssin));

        $cssin  = '.css {width:0pt}';
        $cssout = '.css{width:0;}';
        $this->assertEquals($cssout, $optimiser->process($cssin));

        $cssin  = '.css {width:0mm}';
        $cssout = '.css{width:0;}';
        $this->assertEquals($cssout, $optimiser->process($cssin));

        $cssin  = '.css {width:100px}';
        $cssout = '.css{width:100px;}';
        $this->assertEquals($cssout, $optimiser->process($cssin));
    }

    /**
     * Test margin styles
     * @param css_optimiser $optimiser
     */
    public function test_margins() {
        $optimiser = $this->get_optimiser();

        $cssin = '.one {margin: 1px 2px 3px 4px}';
        $cssout = '.one{margin:1px 2px 3px 4px;}';
        $this->assertEquals($cssout, $optimiser->process($cssin));

        $cssin = '.one {margin-top:1px; margin-left:4px; margin-right:2px; margin-bottom: 3px;}';
        $cssout = '.one{margin:1px 2px 3px 4px;}';
        $this->assertEquals($cssout, $optimiser->process($cssin));

        $cssin = '.one {margin-top:1px; margin-left:4px;}';
        $cssout = '.one{margin-top:1px;margin-left:4px;}';
        $this->assertEquals($cssout, $optimiser->process($cssin));

        $cssin = '.one {margin:1px; margin-left:4px;}';
        $cssout = '.one{margin:1px 1px 1px 4px;}';
        $this->assertEquals($cssout, $optimiser->process($cssin));

        $cssin = '.one {margin:1px; margin-bottom:4px;}';
        $cssout = '.one{margin:1px 1px 4px;}';
        $this->assertEquals($cssout, $optimiser->process($cssin));

        $cssin = '.one, .two, .one.two, .one .two {margin:0;} .one.two {margin:0 7px;}';
        $cssout = '.one, .two{margin:0;} .one.two{margin:0 7px;} .one .two{margin:0;}';
        $this->assertEquals($cssout, $optimiser->process($cssin));

        $cssin = '.block {margin-top: 0px !important;margin-bottom: 0px !important;}';
        $cssout = '.block{margin-top:0 !important;margin-bottom:0 !important;}';
        $this->assertEquals($cssout, $optimiser->process($cssin));

        $cssin = '.block {margin: 0px !important;margin-bottom: 3px;}';
        $cssout = '.block{margin:0 !important;}';
        $this->assertEquals($cssout, $optimiser->process($cssin));

        $cssin = '.block {margin: 5px;margin-right: 0 !important;}';
        $cssout = '.block{margin:5px;margin-right:0 !important;}';
        $this->assertEquals($cssout, $optimiser->process($cssin));
    }

    /**
     * Test padding styles
     *
     * @param css_optimiser $optimiser
     */
    public function test_padding() {
        $optimiser = $this->get_optimiser();

        $cssin = '.one {padding: 1px 2px 3px 4px}';
        $cssout = '.one{padding:1px 2px 3px 4px;}';
        $this->assertEquals($cssout, $optimiser->process($cssin));

        $cssin = '.one {padding-top:1px; padding-left:4px; padding-right:2px; padding-bottom: 3px;}';
        $cssout = '.one{padding:1px 2px 3px 4px;}';
        $this->assertEquals($cssout, $optimiser->process($cssin));

        $cssin = '.one {padding-top:1px; padding-left:4px;padding-bottom: 3px;}';
        $cssout = '.one{padding-top:1px;padding-left:4px;padding-bottom:3px;}';
        $this->assertEquals($cssout, $optimiser->process($cssin));

        $cssin = '.one {padding-top:1px; padding-left:4px;}';
        $cssout = '.one{padding-top:1px;padding-left:4px;}';
        $this->assertEquals($cssout, $optimiser->process($cssin));

        $cssin = '.one {padding:1px; padding-left:4px;}';
        $cssout = '.one{padding:1px 1px 1px 4px;}';
        $this->assertEquals($cssout, $optimiser->process($cssin));

        $cssin = '.one {padding:1px; padding-bottom:4px;}';
        $cssout = '.one{padding:1px 1px 4px;}';
        $this->assertEquals($cssout, $optimiser->process($cssin));

        $cssin = '.one {padding:0 !important;}';
        $cssout = '.one{padding:0 !important;}';
        $this->assertEquals($cssout, $optimiser->process($cssin));

        $cssin = '.one {padding:0 !important;}';
        $cssout = '.one{padding:0 !important;}';
        $this->assertEquals($cssout, $optimiser->process($cssin));

        $cssin = '.one, .two, .one.two, .one .two {padding:0;} .one.two {padding:0 7px;}';
        $cssout = '.one, .two{padding:0;} .one.two{padding:0 7px;} .one .two{padding:0;}';
        $this->assertEquals($cssout, $optimiser->process($cssin));

        $cssin = '.block {padding-top: 0px !important;padding-bottom: 0px !important;}';
        $cssout = '.block{padding-top:0 !important;padding-bottom:0 !important;}';
        $this->assertEquals($cssout, $optimiser->process($cssin));

        $cssin = '.block {padding: 0px !important;padding-bottom: 3px;}';
        $cssout = '.block{padding:0 !important;}';
        $this->assertEquals($cssout, $optimiser->process($cssin));

        $cssin = '.block {padding: 5px;padding-right: 0 !important;}';
        $cssout = '.block{padding:5px;padding-right:0 !important;}';
        $this->assertEquals($cssout, $optimiser->process($cssin));
    }

    public function test_cursor() {
        $optimiser = $this->get_optimiser();

        // Valid cursor
        $cssin = '.one {cursor: pointer;}';
        $cssout = '.one{cursor:pointer;}';
        $this->assertEquals($cssout, $optimiser->process($cssin));

        // Invalid cursor but tollerated
        $cssin = '.one {cursor: hand;}';
        $cssout = '.one{cursor:hand;}';
        $this->assertEquals($cssout, $optimiser->process($cssin));

        // Valid cursor: url relative
        $cssin = '.one {cursor: mycursor.png;}';
        $cssout = '.one{cursor:mycursor.png;}';
        $this->assertEquals($cssout, $optimiser->process($cssin));

        // Valid cursor: url absolute
        $cssin = '.one {cursor: http://local.host/mycursor.png;}';
        $cssout = '.one{cursor:http://local.host/mycursor.png;}';
        $this->assertEquals($cssout, $optimiser->process($cssin));
    }

    public function test_vertical_align() {
        $optimiser = $this->get_optimiser();

        // Valid vertical aligns
        $cssin = '.one {vertical-align: baseline;}';
        $cssout = '.one{vertical-align:baseline;}';
        $this->assertEquals($cssout, $optimiser->process($cssin));
        $cssin = '.one {vertical-align: middle;}';
        $cssout = '.one{vertical-align:middle;}';
        $this->assertEquals($cssout, $optimiser->process($cssin));
        $cssin = '.one {vertical-align: 0.75em;}';
        $cssout = '.one{vertical-align:0.75em;}';
        $this->assertEquals($cssout, $optimiser->process($cssin));
        $cssin = '.one {vertical-align: 50%;}';
        $cssout = '.one{vertical-align:50%;}';
        $this->assertEquals($cssout, $optimiser->process($cssin));

        // Invalid but tollerated
        $cssin = '.one {vertical-align: center;}';
        $cssout = '.one{vertical-align:center;}';
        $this->assertEquals($cssout, $optimiser->process($cssin));
    }

    public function test_float() {
        $optimiser = $this->get_optimiser();

        // Valid vertical aligns
        $cssin = '.one {float: inherit;}';
        $cssout = '.one{float:inherit;}';
        $this->assertEquals($cssout, $optimiser->process($cssin));
        $cssin = '.one {float: left;}';
        $cssout = '.one{float:left;}';
        $this->assertEquals($cssout, $optimiser->process($cssin));
        $cssin = '.one {float: right;}';
        $cssout = '.one{float:right;}';
        $this->assertEquals($cssout, $optimiser->process($cssin));
        $cssin = '.one {float: none;}';
        $cssout = '.one{float:none;}';
        $this->assertEquals($cssout, $optimiser->process($cssin));

        // Invalid but tollerated
        $cssin = '.one {float: center;}';
        $cssout = '.one{float:center;}';
        $this->assertEquals($cssout, $optimiser->process($cssin));
    }

    /**
     * Test some totally invalid CSS optimisation
     *
     * @param css_optimiser $optimiser
     */
    protected function try_invalid_css_handling() {
        $optimiser = $this->get_optimiser();

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
            $this->assertEquals($cssout, $optimiser->process($css));
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
            $this->assertEquals($cssout, $optimiser->process($css));
        }

        $cssin = '..one {background-color:color:red}';
        $cssout = '..one{background-color:color:red;}';
        $this->assertEquals($cssout, $optimiser->process($cssin));

        $cssin = '#.one {background-color:color:red}';
        $cssout = '#.one{background-color:color:red;}';
        $this->assertEquals($cssout, $optimiser->process($cssin));

        $cssin = '##one {background-color:color:red}';
        $cssout = '##one{background-color:color:red;}';
        $this->assertEquals($cssout, $optimiser->process($cssin));

        $cssin = '.one {background-color:color:red}';
        $cssout = '.one{background-color:color:red;}';
        $this->assertEquals($cssout, $optimiser->process($cssin));

        $cssin = '.one {background-color:red;color;border-color:blue}';
        $cssout = '.one{background-color:#F00;border-color:#00F;}';
        $this->assertEquals($cssout, $optimiser->process($cssin));

        $cssin  = '{background-color:#123456;color:red;}{color:green;}';
        $cssout = "{background-color:#123456;color:#008000;}";
        $this->assertEquals($cssout, $optimiser->process($cssin));

        $cssin  = '.one {color:red;} {color:green;} .one {background-color:blue;}';
        $cssout = ".one{color:#F00;background-color:#00F;} {color:#008000;}";
        $this->assertEquals($cssout, $optimiser->process($cssin));
    }

    /**
     * Try to break some things
     * @param css_optimiser $optimiser
     */
    protected function try_break_things() {
        $optimiser = $this->get_optimiser();

        // Wildcard test
        $cssin  = '* {color: black;}';
        $cssout = '*{color:#000;}';
        $this->assertEquals($cssout, $optimiser->process($cssin));

        // Wildcard test
        $cssin  = '.one * {color: black;}';
        $cssout = '.one *{color:#000;}';
        $this->assertEquals($cssout, $optimiser->process($cssin));

        // Wildcard test
        $cssin  = '* .one * {color: black;}';
        $cssout = '* .one *{color:#000;}';
        $this->assertEquals($cssout, $optimiser->process($cssin));

        // Wildcard test
        $cssin  = '*,* {color: black;}';
        $cssout = '*{color:#000;}';
        $this->assertEquals($cssout, $optimiser->process($cssin));

        // Wildcard test
        $cssin  = '*, * .one {color: black;}';
        $cssout = "*,\n* .one{color:#000;}";
        $this->assertEquals($cssout, $optimiser->process($cssin));

        // Wildcard test
        $cssin  = '*, *.one {color: black;}';
        $cssout = "*,\n*.one{color:#000;}";
        $this->assertEquals($cssout, $optimiser->process($cssin));

        // Psedo test
        $cssin  = '.one:before {color: black;}';
        $cssout = '.one:before{color:#000;}';
        $this->assertEquals($cssout, $optimiser->process($cssin));

        // Psedo test
        $cssin  = '.one:after {color: black;}';
        $cssout = '.one:after{color:#000;}';
        $this->assertEquals($cssout, $optimiser->process($cssin));

        // Psedo test
        $cssin  = '.one:onclick {color: black;}';
        $cssout = '.one:onclick{color:#000;}';
        $this->assertEquals($cssout, $optimiser->process($cssin));

        // Test complex CSS rules that don't really exist but mimic other CSS rules
        $cssin  = '.one {master-of-destruction: explode(\' \', "What madness");}';
        $cssout = '.one{master-of-destruction:explode(\' \', "What madness");}';
        $this->assertEquals($cssout, $optimiser->process($cssin));

        // Test some complex IE css... I couldn't even think of a more complext solution
        // than the CSS they came up with.
        $cssin  = 'a { opacity: 0.5; -ms-filter:"progid:DXImageTransform.Microsoft.Alpha(Opacity=50)"; filter: alpha(opacity=50); }';
        $cssout = 'a{opacity:0.5;-ms-filter:"progid:DXImageTransform.Microsoft.Alpha(Opacity=50)";filter:alpha(opacity=50);}';
        $this->assertEquals($cssout, $optimiser->process($cssin));
    }

    /**
     * A bulk processing test
     * @param css_optimiser $optimiser
     */
    protected function try_bulk_processing() {
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
.test .one{margin:10px;border-width:0;color:#F00;background-color:#123;}
.test.one{margin:15px;border:1px solid #008000;}
#test .one{margin:20px;color:#000;}
#test #one{margin:25px;}
.test #one{margin:30px;}
#new.style{color:#000;}

@media print {
  #test .one{margin:40px;color:#123456;}
  #test #one{margin:45px;}
}
@media print,screen {
  #test .one{color:#654321;}
}
CSS;
        $CFG->cssoptimiserpretty = 1;
        $this->assertEquals($this->get_optimiser()->process($cssin), $cssout);
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
        $this->assertTrue(css_is_width('-1px'));
        $this->assertTrue(css_is_width('auto'));
        $this->assertTrue(css_is_width('inherit'));

        // Valid widths but missing their unit specifier
        $this->assertFalse(css_is_width('0.75'));
        $this->assertFalse(css_is_width('3'));
        $this->assertFalse(css_is_width('-1'));
        // Totally invalid widths
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
    public function try_broken_css_found_in_moodle() {
        $optimiser = $this->get_optimiser();

        // Notice how things are out of order here but that they get corrected
        $cssin = '.test {background:url([[pix:theme|pageheaderbgred]]) top center no-repeat}';
        $cssout = '.test{background:url([[pix:theme|pageheaderbgred]]) no-repeat top center;}';
        $this->assertEquals($cssout, $optimiser->process($cssin));

        // Cursor hand isn't valid
        $cssin  = '.test {cursor: hand;}';
        $cssout = '.test{cursor:hand;}';
        $this->assertEquals($cssout, $optimiser->process($cssin));

        // Zoom property isn't valid
        $cssin  = '.test {zoom: 1;}';
        $cssout = '.test{zoom:1;}';
        $this->assertEquals($cssout, $optimiser->process($cssin));

        // Left isn't a valid position property
        $cssin  = '.test {position: left;}';
        $cssout = '.test{position:left;}';
        $this->assertEquals($cssout, $optimiser->process($cssin));

        // The dark red color isn't a valid HTML color but has a standardised
        // translation of #8B0000
        $cssin  = '.test {color: darkred;}';
        $cssout = '.test{color:#8B0000;}';
        $this->assertEquals($cssout, $optimiser->process($cssin));

        // You can't use argb colours as border colors
        $cssin  = '.test {border-bottom: 1px solid rgba(0,0,0,0.25);}';
        $cssout = '.test{border-bottom:1px solid rgba(0,0,0,0.25);}';
        $this->assertEquals($cssout, $optimiser->process($cssin));

        // Opacity with annoying IE equivilants....
        $cssin  = '.test {opacity: 0.5; -ms-filter:"progid:DXImageTransform.Microsoft.Alpha(Opacity=50)"; filter: alpha(opacity=50);}';
        $cssout = '.test{opacity:0.5;-ms-filter:"progid:DXImageTransform.Microsoft.Alpha(Opacity=50)";filter:alpha(opacity=50);}';
        $this->assertEquals($cssout, $optimiser->process($cssin));
    }

    /**
     * Test keyframe declarations
     * @param css_optimiser $optimiser
     */
    public function try_keyframe_css_animation() {
        $optimiser = $this->get_optimiser();

        $css = '.dndupload-arrow{width:56px;height:47px;position:absolute;animation:mymove 5s infinite;-moz-animation:mymove 5s infinite;-webkit-animation:mymove 5s infinite;background:url(\'[[pix:theme|fp/dnd_arrow]]\') no-repeat center;margin-left:-28px;}';
        $this->assertEquals($css, $optimiser->process($css));

        $css = '@keyframes mymove {0%{top:10px;}12%{top:40px;}30%{top:20px;}65%{top:35px;}100%{top:9px;}}';
        $this->assertEquals($css, $optimiser->process($css));

        $css  = "@keyframes mymove {0%{top:10px;}12%{top:40px;}30%{top:20px;}65%{top:35px;}100%{top:9px;}}\n";
        $css .= "@-moz-keyframes mymove {0%{top:10px;}12%{top:40px;}30%{top:20px;}65%{top:35px;}100%{top:9px;}}\n";
        $css .= "@-webkit-keyframes mymove {0%{top:10px;}12%{top:40px;}30%{top:20px;}65%{top:35px;}100%{top:9px;}}";
        $this->assertEquals($css, $optimiser->process($css));


        $cssin = <<<CSS
.test {color:#FFF;}
.testtwo {color:#FFF;}
@media print {
    .test {background-color:#FFF;}
}
.dndupload-arrow{width:56px;height:47px;position:absolute;animation:mymove 5s infinite;-moz-animation:mymove 5s infinite;-webkit-animation:mymove 5s infinite;background:url('[[pix:theme|fp/dnd_arrow]]') no-repeat center;margin-left:-28px;}
@media print {
    .test {background-color:#000;}
}
@keyframes mymove {0%{top:10px;} 12%{top:40px;} 30%{top:20px} 65%{top:35px;} 100%{top:9px;}}
@-moz-keyframes mymove{0%{top:10px;} 12%{top:40px;} 30%{top:20px} 65%{top:35px;} 100%{top:9px;}}
@-webkit-keyframes mymove {0%{top:10px;} 12%{top:40px;} 30%{top:20px} 65%{top:35px;} 100%{top:9px;}}
@media print {
    .test {background-color:#333;}
}
.test {color:#888;}
.testtwo {color:#888;}
CSS;

        $cssout = <<<CSS
.test,
.testtwo{color:#888;}
.dndupload-arrow{width:56px;height:47px;position:absolute;animation:mymove 5s infinite;-moz-animation:mymove 5s infinite;-webkit-animation:mymove 5s infinite;background:url('[[pix:theme|fp/dnd_arrow]]') no-repeat center;margin-left:-28px;}

@media print {
  .test{background-color:#333;}
}
@keyframes mymove {0%{top:10px;}12%{top:40px;}30%{top:20px;}65%{top:35px;}100%{top:9px;}}
@-moz-keyframes mymove {0%{top:10px;}12%{top:40px;}30%{top:20px;}65%{top:35px;}100%{top:9px;}}
@-webkit-keyframes mymove {0%{top:10px;}12%{top:40px;}30%{top:20px;}65%{top:35px;}100%{top:9px;}}
CSS;
        $this->assertEquals($cssout, $optimiser->process($cssin));



        $cssin = <<<CSS
.dndupload-target {display:none;}
.dndsupported .dndupload-ready .dndupload-target {display:block;}
.dndupload-uploadinprogress {display:none;text-align:center;}
.dndupload-uploading .dndupload-uploadinprogress {display:block;}
.dndupload-arrow {background:url('[[pix:theme|fp/dnd_arrow]]') center no-repeat;width:56px;height:47px;position:absolute;margin-left: -28px;/*right:46%;left:46%;*/animation:mymove 5s infinite;-moz-animation:mymove 5s infinite;-webkit-animation:mymove 5s infinite;}
@keyframes mymove {0%{top:10px;} 12%{top:40px;} 30%{top:20px} 65%{top:35px;} 100%{top:9px;}}@-moz-keyframes mymove{0%{top:10px;} 12%{top:40px;} 30%{top:20px} 65%{top:35px;} 100%{top:9px;}}@-webkit-keyframes mymove {0%{top:10px;} 12%{top:40px;} 30%{top:20px} 65%{top:35px;} 100%{top:9px;}}

/*
 * Select Dialogue (File Manager only)
 */
.filemanager.fp-select .fp-select-loading {display:none;}
.filemanager.fp-select.loading .fp-select-loading {display:block;}
.filemanager.fp-select.loading form {display:none;}
CSS;

        $cssout = <<<CSS
.dndupload-target{display:none;}
.dndsupported .dndupload-ready .dndupload-target{display:block;}
.dndupload-uploadinprogress{display:none;text-align:center;}
.dndupload-uploading .dndupload-uploadinprogress{display:block;}
.dndupload-arrow{background:url('[[pix:theme|fp/dnd_arrow]]') no-repeat center;width:56px;height:47px;position:absolute;margin-left:-28px;animation:mymove 5s infinite;-moz-animation:mymove 5s infinite;-webkit-animation:mymove 5s infinite;}
.filemanager.fp-select .fp-select-loading{display:none;}
.filemanager.fp-select.loading .fp-select-loading{display:block;}
.filemanager.fp-select.loading form{display:none;}

@keyframes mymove {0%{top:10px;}12%{top:40px;}30%{top:20px;}65%{top:35px;}100%{top:9px;}}
@-moz-keyframes mymove {0%{top:10px;}12%{top:40px;}30%{top:20px;}65%{top:35px;}100%{top:9px;}}
@-webkit-keyframes mymove {0%{top:10px;}12%{top:40px;}30%{top:20px;}65%{top:35px;}100%{top:9px;}}
CSS;
        $this->assertEquals($cssout, $optimiser->process($cssin));
    }

    /**
     * Test media declarations
     * @param css_optimiser $optimiser
     */
    public function try_media_rules() {
        $optimiser = $this->get_optimiser();

        $cssin = "@media print {\n  .test{background-color:#333;}\n}";
        $cssout = "@media print {\n  .test{background-color:#333;}\n}";
        $this->assertEquals($cssout, $optimiser->process($cssin));

        $cssin = "@media screen and (min-width:30px) {\n  #region-main-box{left: 30px;float: left;}\n}";
        $cssout = "@media screen and (min-width:30px) {\n  #region-main-box{left:30px;float:left;}\n}";
        $this->assertEquals($cssout, $optimiser->process($cssin));

        $cssin = "@media all and (min-width:500px) {\n  #region-main-box{left:30px;float:left;}\n}";
        $cssout = "@media all and (min-width:500px) {\n  #region-main-box{left:30px;float:left;}\n}";
        $this->assertEquals($cssout, $optimiser->process($cssin));

        $cssin = "@media (min-width:500px) {\n  #region-main-box{left:30px;float:left;}\n}";
        $cssout = "@media (min-width:500px) {\n  #region-main-box{left:30px;float:left;}\n}";
        $this->assertEquals($cssout, $optimiser->process($cssin));

        $cssin = "@media screen and (color), projection and (color) {\n  #region-main-box{left:30px;float:left;}\n}";
        $cssout = "@media screen and (color),projection and (color) {\n  #region-main-box{left:30px;float:left;}\n}";
        $this->assertEquals($cssout, $optimiser->process($cssin));

        $cssin = "@media print {\n  .test{background-color:#000;}\n}@media print {\n  .test{background-color:#FFF;}\n}";
        $cssout = "@media print {\n  .test{background-color:#FFF;}\n}";
        $this->assertEquals($cssout, $optimiser->process($cssin));

        $cssin = "@media screen and (min-width:30px) {\n  #region-main-box{background-color:#000;}\n}\n@media screen and (min-width:30px) {\n  #region-main-box{background-color:#FFF;}\n}";
        $cssout = "@media screen and (min-width:30px) {\n  #region-main-box{background-color:#FFF;}\n}";
        $this->assertEquals($cssout, $optimiser->process($cssin));

        $cssin = "@media screen and (min-width:30px) {\n  #region-main-box{background-color:#000;}\n}\n@media screen and (min-width:31px) {\n  #region-main-box{background-color:#FFF;}\n}";
        $cssout = "@media screen and (min-width:30px) {\n  #region-main-box{background-color:#000;}\n}\n@media screen and (min-width:31px) {\n  #region-main-box{background-color:#FFF;}\n}";
        $this->assertEquals($cssout, $optimiser->process($cssin));
    }


    public function test_css_optimisation_ordering() {
        $optimiser = $this->get_optimiser();

        $css = '.test{display:none;} .dialogue{display:block;} .dialogue-hidden{display:none;}';
        $this->assertEquals($css, $optimiser->process($css));

        $cssin = '.test{display:none;} .dialogue-hidden{display:none;} .dialogue{display:block;}';
        $cssout = '.test, .dialogue-hidden{display:none;} .dialogue{display:block;}';
        $this->assertEquals($cssout, $optimiser->process($cssin));
    }
}