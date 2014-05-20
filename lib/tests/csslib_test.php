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
 * CSS optimiser test class.
 *
 * @package core_css
 * @category phpunit
 * @copyright 2012 Sam Hemelryk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_csslib_testcase extends advanced_testcase {

    /**
     * Returns a CSS optimiser
     *
     * @return css_optimiser
     */
    protected function get_optimiser() {
        return new css_optimiser();
    }

    /**
     * Background colour tests.
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
     */
    public function test_background() {
        $optimiser = new css_optimiser();

        $cssin = '.test {background-color: #123456;}';
        $cssout = '.test{background-color:#123456;}';
        $this->assertSame($cssout, $optimiser->process($cssin));

        $cssin = '.test {background: #123456;}';
        $cssout = '.test{background:#123456;}';
        $this->assertSame($cssout, $optimiser->process($cssin));

        $cssin = '.test {background-image: url(\'test.png\');}';
        $cssout = '.test{background-image:url(\'test.png\');}';
        $this->assertSame($cssout, $optimiser->process($cssin));

        $cssin = '.test {background: #123456 url(\'test.png\') no-repeat top left;}';
        $cssout = '.test{background:#123456 url(\'test.png\') no-repeat top left;}';
        $this->assertSame($cssout, $optimiser->process($cssin));

        // Check out this for madness, background position and background-repeat have been reversed.
        $cssin = '.test {background: #123456 url(\'test.png\') center no-repeat;}';
        $cssout = '.test{background:#123456 url(\'test.png\') no-repeat center;}';
        $this->assertSame($cssout, $optimiser->process($cssin));

        $cssin = '.test {background: url(\'test.png\') no-repeat top left;}.test{background-position: bottom right}.test {background-color:#123456;}';
        $cssout = '.test{background:#123456 url(\'test.png\') no-repeat bottom right;}';
        $this->assertSame($cssout, $optimiser->process($cssin));

        $cssin = '.test {background: url(   \'test.png\'    )}.test{background: bottom right}.test {background:#123456;}';
        $cssout = '.test{background:#123456;}';
        $this->assertSame($cssout, $optimiser->process($cssin));

        $cssin = '.test {background-image: url(\'test.png\');background:#123456;}';
        $cssout = '.test{background:#123456;}';
        $this->assertSame($cssout, $optimiser->process($cssin));

        $cssin = '.test {background-color: #123456;background-repeat: repeat-x; background-position: 100% 0%;}';
        $cssout = '.test{background-color:#123456;background-repeat:repeat-x;background-position:100% 0%;}';
        $this->assertSame($cssout, $optimiser->process($cssin));

        $cssin = '.tree_item.branch {background-image: url([[pix:t/expanded]]);background-position: 0 10%;background-repeat: no-repeat;}
                  .tree_item.branch.navigation_node {background-image:none;padding-left:0;}';
        $cssout = '.tree_item.branch{background-image:url([[pix:t/expanded]]);background-position:0 10%;background-repeat:no-repeat;} .tree_item.branch.navigation_node{background-image:none;padding-left:0;}';
        $this->assertSame($cssout, $optimiser->process($cssin));

        $cssin = '#nextLink{background:url(data:image/gif;base64,AAAA);}';
        $cssout = '#nextLink{background:url(data:image/gif;base64,AAAA);}';
        $this->assertSame($cssout, $optimiser->process($cssin));

        $cssin = '#nextLink{background-image:url(data:image/gif;base64,AAAA);}';
        $cssout = '#nextLink{background-image:url(data:image/gif;base64,AAAA);}';
        $this->assertSame($cssout, $optimiser->process($cssin));

        $cssin = '.test {background: #123456 url(data:image/gif;base64,AAAA) no-repeat top left;}';
        $cssout = '.test{background:#123456 url(data:image/gif;base64,AAAA) no-repeat top left;}';
        $this->assertSame($cssout, $optimiser->process($cssin));

        $cssin = '#test {background-image:none;background-position:right center;background-repeat:no-repeat;}';
        $cssout = '#test{background-image:none;background-position:right center;background-repeat:no-repeat;}';
        $this->assertSame($cssout, $optimiser->process($cssin));

        $cssin = '.test {background: url([[pix:theme|photos]]) no-repeat 50% 50%;background-size: 40px 40px;-webkit-background-size: 40px 40px;}';
        $cssout = '.test{background:url([[pix:theme|photos]]) no-repeat 50% 50%;background-size:40px 40px;-webkit-background-size:40px 40px;}';
        $this->assertSame($cssout, $optimiser->process($cssin));

        $cssin = '.test{background-image: -o-linear-gradient(#3c3c3c, #111);background-image: linear-gradient(#3c3c3c, #111);}';
        $cssout = '.test{background-image:-o-linear-gradient(#3c3c3c, #111);background-image:linear-gradient(#3c3c3c, #111);}';
        $this->assertSame($cssout, $optimiser->process($cssin));

        $cssin = '.test{background-image: -moz-linear-gradient(#3c3c3c, #111);background-image: -webkit-linear-gradient(#3c3c3c, #111);background-image: -o-linear-gradient(#3c3c3c, #111);background-image: linear-gradient(#3c3c3c, #111);background-image: url(/test.png);}';
        $cssout = '.test{background-image:url(/test.png);background-image:-moz-linear-gradient(#3c3c3c, #111);background-image:-webkit-linear-gradient(#3c3c3c, #111);background-image:-o-linear-gradient(#3c3c3c, #111);background-image:linear-gradient(#3c3c3c, #111);}';
        $this->assertSame($cssout, $optimiser->process($cssin));

        $cssin = '.test{background:#CCC; background-image: url(test.png);}';
        $cssout = '.test{background:#CCC url(test.png);}';
        $this->assertSame($cssout, $optimiser->process($cssin));

        $cssin = '.test{background:#CCC; background-image: linear-gradient(#3c3c3c, #111);}';
        $cssout = '.test{background:#CCC;background-image:linear-gradient(#3c3c3c, #111);}';
        $this->assertSame($cssout, $optimiser->process($cssin));

        $cssin = '.test{background:#CCC; background-image: -o-linear-gradient(#3c3c3c, #111);background-image: linear-gradient(#3c3c3c, #111);}';
        $cssout = '.test{background:#CCC;background-image:-o-linear-gradient(#3c3c3c, #111);background-image:linear-gradient(#3c3c3c, #111);}';
        $this->assertSame($cssout, $optimiser->process($cssin));

        $cssin = '#newmessageoverlay{font-weight: normal; border: 1px solid #222; background: #444; color: #ddd; text-shadow: 0 -1px 0px #000; background-image: -moz-linear-gradient(top, #333 0%, #333 5%, #444 15%, #444 60%, #222 100%); background-image: -webkit-gradient(linear, center top, center bottom, color-stop(0, #333), color-stop(5%, #333), color-stop(15%, #444), color-stop(60%, #444), color-stop(1, #222)); -ms-filter: "progid:DXImageTransform.Microsoft.gradient(startColorStr=\'#333333\', EndColorStr=\'#222222\')"; padding:20px; padding-left: 0px; padding-right: 10px; position: inherit; z-index: 9999; width: 90%; margin-left: auto; margin-right: auto; height: 100%;}';
        $cssout = '#newmessageoverlay{font-weight:normal;border:1px solid #222;background:#444;color:#DDD;text-shadow:0 -1px 0px #000;-ms-filter:"progid:DXImageTransform.Microsoft.gradient(startColorStr=\'#333333\', EndColorStr=\'#222222\')";padding:20px 10px 20px 0;position:inherit;z-index:9999;width:90%;margin-left:auto;margin-right:auto;height:100%;background-image:-moz-linear-gradient(top, #333 0%, #333 5%, #444 15%, #444 60%, #222 100%);background-image:-webkit-gradient(linear, center top, center bottom, color-stop(0, #333), color-stop(5%, #333), color-stop(15%, #444), color-stop(60%, #444), color-stop(1, #222));}';
        $this->assertSame($cssout, $optimiser->process($cssin));

        $cssin = '.userenrolment {background-color:inherit !important;background: inherit !important;}';
        $cssout = '.userenrolment{background:inherit !important;}';
        $this->assertSame($cssout, $optimiser->process($cssin));

        $cssin = '.userenrolment {background-image:url(test.png) !important;background: inherit !important;}';
        $cssout = '.userenrolment{background:inherit !important;}';
        $this->assertSame($cssout, $optimiser->process($cssin));

        $cssin = '.userenrolment {background: inherit !important;background-image:url(test.png) !important;}';
        $cssout = '.userenrolment{background:inherit url(test.png) !important;}';
        $this->assertSame($cssout, $optimiser->process($cssin));

        $cssin = '.userenrolment {background: inherit !important;background-image:url(test.png);}';
        $cssout = '.userenrolment{background:inherit !important;}';
        $this->assertSame($cssout, $optimiser->process($cssin));

        $css = '#filesskin .yui3-widget-hd{background:#CCC;background:-webkit-gradient(linear, left top, left bottom, from(#FFFFFF), to(#CCCCCC));background:-moz-linear-gradient(top, #FFFFFF, #CCCCCC);}';
        $this->assertSame($css, $optimiser->process($css));

        $css = '.userenrolment{background:-moz-linear-gradient(top, #FFFFFF, #CCCCCC) !important;}';
        $this->assertSame($css, $optimiser->process($css));

        $css = '.userenrolment{background:#CCC !important;background:-webkit-gradient(linear, left top, left bottom, from(#FFFFFF), to(#CCCCCC)) !important;background:-moz-linear-gradient(top, #FFFFFF, #CCCCCC) !important;}';
        $this->assertSame($css, $optimiser->process($css));

        $cssin = '.userenrolment{background:-moz-linear-gradient(top, #FFFFFF, #CCCCCC) !important;}.userenrolment {background: #CCCCCC!important;background: -webkit-gradient(linear, left top, left bottom, from(#FFFFFF), to(#CCCCCC))!important;}';
        $cssout = '.userenrolment{background:#CCC !important;background:-moz-linear-gradient(top, #FFFFFF, #CCCCCC) !important;background:-webkit-gradient(linear, left top, left bottom, from(#FFFFFF), to(#CCCCCC)) !important;}';
        $this->assertSame($cssout, $optimiser->process($cssin));
    }

    /**
     * Border tests.
     */
    public function test_borders() {
        $optimiser = new css_optimiser();

        $cssin = '.test {border: 1px solid #654321} .test {border-bottom-color: #123456}';
        $cssout = '.test{border:1px solid;border-color:#654321 #654321 #123456;}';
        $this->assertSame($cssout, $optimiser->process($cssin));

        $cssin = '.one {border:1px solid red;}';
        $cssout = '.one{border:1px solid #F00;}';
        $this->assertSame($cssout, $optimiser->process($cssin));

        $cssin = '.one {border:1px solid;} .one {border:2px dotted #DDD;}';
        $cssout = '.one{border:2px dotted #DDD;}';
        $this->assertSame($cssout, $optimiser->process($cssin));

        $cssin = '.one {border:2px dotted #DDD;}.one {border:1px solid;} ';
        $cssout = '.one{border:1px solid #DDD;}';
        $this->assertSame($cssout, $optimiser->process($cssin));

        $cssin = '.one, .two {border:1px solid red;}';
        $cssout = ".one, .two{border:1px solid #F00;}";
        $this->assertSame($cssout, $optimiser->process($cssin));

        $cssin = '.one, .two {border:0px;}';
        $cssout = ".one, .two{border-width:0;}";
        $this->assertSame($cssout, $optimiser->process($cssin));

        $cssin = '.one, .two {border: thin;}';
        $cssout = ".one, .two{border-width:thin;}";
        $this->assertSame($cssout, $optimiser->process($cssin));

        $cssin = '.one, .two {border: thin solid black;}';
        $cssout = ".one, .two{border:thin solid #000;}";
        $this->assertSame($cssout, $optimiser->process($cssin));

        $cssin = '.one, .two {border-top: 5px solid white;}';
        $cssout = ".one, .two{border-top:5px solid #FFF;}";
        $this->assertSame($cssout, $optimiser->process($cssin));

        $cssin = '.one {border:1px solid red;} .two {border:1px solid red;}';
        $cssout = ".one, .two{border:1px solid #F00;}";
        $this->assertSame($cssout, $optimiser->process($cssin));

        $cssin = '.one {border:1px solid red;width:20px;} .two {border:1px solid red;height:20px;}';
        $cssout = ".one{border:1px solid #F00;width:20px;} .two{border:1px solid #F00;height:20px;}";
        $this->assertSame($cssout, $optimiser->process($cssin));

        $cssin = '.test {border: 1px solid #123456;} .test {border-color: #654321}';
        $cssout = '.test{border:1px solid #654321;}';
        $this->assertSame($cssout, $optimiser->process($cssin));

        $cssin = '.test {border-width: 1px; border-style: solid; border-color: #123456;}';
        $cssout = '.test{border:1px solid #123456;}';
        $this->assertSame($cssout, $optimiser->process($cssin));

        $cssin = '.test {border:1px solid #123456;border-top:2px dotted #654321;}';
        $cssout = '.test{border:1px solid #123456;border-top:2px dotted #654321;}';
        $this->assertSame($cssout, $optimiser->process($cssin));

        $cssin = '.test {border:1px solid #123456;border-left:2px dotted #654321;}';
        $cssout = '.test{border:1px solid #123456;border-left:2px dotted #654321;}';
        $this->assertSame($cssout, $optimiser->process($cssin));

        $cssin = '.test {border-left:2px dotted #654321;border:1px solid #123456;}';
        $cssout = '.test{border:1px solid #123456;}';
        $this->assertSame($cssout, $optimiser->process($cssin));

        $cssin = '.test {border:1px solid;border-top-color:#123456;}';
        $cssout = '.test{border:1px solid;border-top-color:#123456;}';
        $this->assertSame($cssout, $optimiser->process($cssin));

        $cssin = '.test {border:1px solid;border-top-color:#111; border-bottom-color: #222;border-left-color: #333;}';
        $cssout = '.test{border:1px solid;border-top-color:#111;border-bottom-color:#222;border-left-color:#333;}';
        $this->assertSame($cssout, $optimiser->process($cssin));

        $cssin = '.test {border:1px solid;border-top-color:#111; border-bottom-color: #222;border-left-color: #333;border-right-color:#444;}';
        $cssout = '.test{border:1px solid;border-color:#111 #444 #222 #333;}';
        $this->assertSame($cssout, $optimiser->process($cssin));

        $cssin = '.generaltable .cell {border-color:#EEE;} .generaltable .cell {border-width: 1px;border-style: solid;}';
        $cssout = '.generaltable .cell{border:1px solid #EEE;}';
        $this->assertSame($cssout, $optimiser->process($cssin));

        $cssin = '#page-admin-roles-override .rolecap {border:none;border-bottom:1px solid #CECECE;}';
        $cssout = '#page-admin-roles-override .rolecap{border-top:0;border-right:0;border-bottom:1px solid #CECECE;border-left:0;}';
        $this->assertSame($cssout, $optimiser->process($cssin));
    }

    /**
     * Test colour styles.
     */
    public function test_colors() {
        $optimiser = new css_optimiser();

        $css = '.css{}';
        $this->assertSame($css, $optimiser->process($css));

        $css = '.css{color:#123456;}';
        $this->assertSame($css, $optimiser->process($css));

        $css = '#some{color:#123456;}';
        $this->assertSame($css, $optimiser->process($css));

        $css = 'div{color:#123456;}';
        $this->assertSame($css, $optimiser->process($css));

        $css = 'div.css{color:#123456;}';
        $this->assertSame($css, $optimiser->process($css));

        $css = 'div#some{color:#123456;}';
        $this->assertSame($css, $optimiser->process($css));

        $css = 'div[type=blah]{color:#123456;}';
        $this->assertSame($css, $optimiser->process($css));

        $css = 'div.css[type=blah]{color:#123456;}';
        $this->assertSame($css, $optimiser->process($css));

        $css = 'div#some[type=blah]{color:#123456;}';
        $this->assertSame($css, $optimiser->process($css));

        $css = '#some.css[type=blah]{color:#123456;}';
        $this->assertSame($css, $optimiser->process($css));

        $css = '#some .css[type=blah]{color:#123456;}';
        $this->assertSame($css, $optimiser->process($css));

        $cssin = '.one {color:red;} .two {color:#F00;}';
        $cssout = ".one, .two{color:#F00;}";
        $this->assertSame($cssout, $optimiser->process($cssin));

        $cssin = '.one {color:#123;color:#321;}';
        $cssout = '.one{color:#321;}';
        $this->assertSame($cssout, $optimiser->process($cssin));

        $cssin = '.one {color:#123; color : #321 ;}';
        $cssout = '.one{color:#321;}';
        $this->assertSame($cssout, $optimiser->process($cssin));

        $cssin = '.one {color:#123;} .one {color:#321;}';
        $cssout = '.one{color:#321;}';
        $this->assertSame($cssout, $optimiser->process($cssin));

        $cssin = '.one {color:#123 !important;color:#321;}';
        $cssout = '.one{color:#123 !important;}';
        $this->assertSame($cssout, $optimiser->process($cssin));

        $cssin = '.one {color:#123 !important;} .one {color:#321;}';
        $cssout = '.one{color:#123 !important;}';
        $this->assertSame($cssout, $optimiser->process($cssin));

        $cssin = '.one {color:#123!important;} .one {color:#321;}';
        $cssout = '.one{color:#123 !important;}';
        $this->assertSame($cssout, $optimiser->process($cssin));

        $cssin = '.one {color:rgb(255, 128, 1)}';
        $cssout = '.one{color:rgb(255, 128, 1);}';
        $this->assertSame($cssout, $optimiser->process($cssin));

        $cssin = '.one {color:rgba(255, 128, 1, 0.5)}';
        $cssout = '.one{color:rgba(255, 128, 1, 0.5);}';
        $this->assertSame($cssout, $optimiser->process($cssin));

        $cssin = '.one {color:hsl(120, 65%, 75%)}';
        $cssout = '.one{color:hsl(120, 65%, 75%);}';
        $this->assertSame($cssout, $optimiser->process($cssin));

        $cssin = '.one {color:hsla(120,65%,75%,0.5)}';
        $cssout = '.one{color:hsla(120,65%,75%,0.5);}';
        $this->assertSame($cssout, $optimiser->process($cssin));

        // Try some invalid colours to make sure we don't mangle them.
        $css = 'div#some{color:#1;}';
        $this->assertSame($css, $optimiser->process($css));

        $css = 'div#some{color:#12;}';
        $this->assertSame($css, $optimiser->process($css));

        $css = 'div#some{color:#1234;}';
        $this->assertSame($css, $optimiser->process($css));

        $css = 'div#some{color:#12345;}';
        $this->assertSame($css, $optimiser->process($css));
    }

    /**
     * Test widths.
     */
    public function test_widths() {
        $optimiser = new css_optimiser();

        $cssin  = '.css {width:0}';
        $cssout = '.css{width:0;}';
        $this->assertSame($cssout, $optimiser->process($cssin));

        $cssin  = '.css {width:0px}';
        $cssout = '.css{width:0;}';
        $this->assertSame($cssout, $optimiser->process($cssin));

        $cssin  = '.css {width:0em}';
        $cssout = '.css{width:0;}';
        $this->assertSame($cssout, $optimiser->process($cssin));

        $cssin  = '.css {width:0pt}';
        $cssout = '.css{width:0;}';
        $this->assertSame($cssout, $optimiser->process($cssin));

        $cssin  = '.css {width:0mm}';
        $cssout = '.css{width:0;}';
        $this->assertSame($cssout, $optimiser->process($cssin));

        $cssin  = '.css {width:100px}';
        $cssout = '.css{width:100px;}';
        $this->assertSame($cssout, $optimiser->process($cssin));
    }

    /**
     * Test margin styles.
     */
    public function test_margins() {
        $optimiser = new css_optimiser();

        $cssin = '.one {margin: 1px 2px 3px 4px}';
        $cssout = '.one{margin:1px 2px 3px 4px;}';
        $this->assertSame($cssout, $optimiser->process($cssin));

        $cssin = '.one {margin-top:1px; margin-left:4px; margin-right:2px; margin-bottom: 3px;}';
        $cssout = '.one{margin:1px 2px 3px 4px;}';
        $this->assertSame($cssout, $optimiser->process($cssin));

        $cssin = '.one {margin-top:1px; margin-left:4px;}';
        $cssout = '.one{margin-top:1px;margin-left:4px;}';
        $this->assertSame($cssout, $optimiser->process($cssin));

        $cssin = '.one {margin:1px; margin-left:4px;}';
        $cssout = '.one{margin:1px 1px 1px 4px;}';
        $this->assertSame($cssout, $optimiser->process($cssin));

        $cssin = '.one {margin:1px; margin-bottom:4px;}';
        $cssout = '.one{margin:1px 1px 4px;}';
        $this->assertSame($cssout, $optimiser->process($cssin));

        $cssin = '.one, .two, .one.two, .one .two {margin:0;} .one.two {margin:0 7px;}';
        $cssout = '.one, .two{margin:0;} .one.two{margin:0 7px;} .one .two{margin:0;}';
        $this->assertSame($cssout, $optimiser->process($cssin));

        $cssin = '.block {margin-top: 0px !important;margin-bottom: 0px !important;}';
        $cssout = '.block{margin-top:0 !important;margin-bottom:0 !important;}';
        $this->assertSame($cssout, $optimiser->process($cssin));

        $cssin = '.block {margin: 0px !important;margin-bottom: 3px;}';
        $cssout = '.block{margin:0 !important;}';
        $this->assertSame($cssout, $optimiser->process($cssin));

        $cssin = '.block {margin: 5px;margin-right: 0 !important;}';
        $cssout = '.block{margin:5px;margin-right:0 !important;}';
        $this->assertSame($cssout, $optimiser->process($cssin));
    }

    /**
     * Test padding styles.
     */
    public function test_padding() {
        $optimiser = new css_optimiser();

        $cssin = '.one {padding: 1px 2px 3px 4px}';
        $cssout = '.one{padding:1px 2px 3px 4px;}';
        $this->assertSame($cssout, $optimiser->process($cssin));

        $cssin = '.one {padding-top:1px; padding-left:4px; padding-right:2px; padding-bottom: 3px;}';
        $cssout = '.one{padding:1px 2px 3px 4px;}';
        $this->assertSame($cssout, $optimiser->process($cssin));

        $cssin = '.one {padding-top:1px; padding-left:4px;padding-bottom: 3px;}';
        $cssout = '.one{padding-top:1px;padding-left:4px;padding-bottom:3px;}';
        $this->assertSame($cssout, $optimiser->process($cssin));

        $cssin = '.one {padding-top:1px; padding-left:4px;}';
        $cssout = '.one{padding-top:1px;padding-left:4px;}';
        $this->assertSame($cssout, $optimiser->process($cssin));

        $cssin = '.one {padding:1px; padding-left:4px;}';
        $cssout = '.one{padding:1px 1px 1px 4px;}';
        $this->assertSame($cssout, $optimiser->process($cssin));

        $cssin = '.one {padding:1px; padding-bottom:4px;}';
        $cssout = '.one{padding:1px 1px 4px;}';
        $this->assertSame($cssout, $optimiser->process($cssin));

        $cssin = '.one {padding:0 !important;}';
        $cssout = '.one{padding:0 !important;}';
        $this->assertSame($cssout, $optimiser->process($cssin));

        $cssin = '.one {padding:0 !important;}';
        $cssout = '.one{padding:0 !important;}';
        $this->assertSame($cssout, $optimiser->process($cssin));

        $cssin = '.one, .two, .one.two, .one .two {padding:0;} .one.two {padding:0 7px;}';
        $cssout = '.one, .two{padding:0;} .one.two{padding:0 7px;} .one .two{padding:0;}';
        $this->assertSame($cssout, $optimiser->process($cssin));

        $cssin = '.block {padding-top: 0px !important;padding-bottom: 0px !important;}';
        $cssout = '.block{padding-top:0 !important;padding-bottom:0 !important;}';
        $this->assertSame($cssout, $optimiser->process($cssin));

        $cssin = '.block {padding: 0px !important;padding-bottom: 3px;}';
        $cssout = '.block{padding:0 !important;}';
        $this->assertSame($cssout, $optimiser->process($cssin));

        $cssin = '.block {padding: 5px;padding-right: 0 !important;}';
        $cssout = '.block{padding:5px;padding-right:0 !important;}';
        $this->assertSame($cssout, $optimiser->process($cssin));
    }

    /**
     * Test cursor optimisations
     */
    public function test_cursor() {
        $optimiser = new css_optimiser();

        // Valid cursor.
        $cssin = '.one {cursor: pointer;}';
        $cssout = '.one{cursor:pointer;}';
        $this->assertSame($cssout, $optimiser->process($cssin));

        // Invalid cursor but tolerated.
        $cssin = '.one {cursor: hand;}';
        $cssout = '.one{cursor:hand;}';
        $this->assertSame($cssout, $optimiser->process($cssin));

        // Valid cursor: url relative.
        $cssin = '.one {cursor: mycursor.png;}';
        $cssout = '.one{cursor:mycursor.png;}';
        $this->assertSame($cssout, $optimiser->process($cssin));

        // Valid cursor: url absolute.
        $cssin = '.one {cursor: http://local.host/mycursor.png;}';
        $cssout = '.one{cursor:http://local.host/mycursor.png;}';
        $this->assertSame($cssout, $optimiser->process($cssin));
    }

    /**
     * Test vertical align optimisations
     */
    public function test_vertical_align() {
        $optimiser = new css_optimiser();

        // Valid vertical aligns.
        $cssin = '.one {vertical-align: baseline;}';
        $cssout = '.one{vertical-align:baseline;}';
        $this->assertSame($cssout, $optimiser->process($cssin));
        $cssin = '.one {vertical-align: middle;}';
        $cssout = '.one{vertical-align:middle;}';
        $this->assertSame($cssout, $optimiser->process($cssin));
        $cssin = '.one {vertical-align: 0.75em;}';
        $cssout = '.one{vertical-align:0.75em;}';
        $this->assertSame($cssout, $optimiser->process($cssin));
        $cssin = '.one {vertical-align: 50%;}';
        $cssout = '.one{vertical-align:50%;}';
        $this->assertSame($cssout, $optimiser->process($cssin));

        // Invalid but tolerated.
        $cssin = '.one {vertical-align: center;}';
        $cssout = '.one{vertical-align:center;}';
        $this->assertSame($cssout, $optimiser->process($cssin));
    }

    /**
     * Test float optimisations
     */
    public function test_float() {
        $optimiser = new css_optimiser();

        // Valid vertical aligns.
        $cssin = '.one {float: inherit;}';
        $cssout = '.one{float:inherit;}';
        $this->assertSame($cssout, $optimiser->process($cssin));
        $cssin = '.one {float: left;}';
        $cssout = '.one{float:left;}';
        $this->assertSame($cssout, $optimiser->process($cssin));
        $cssin = '.one {float: right;}';
        $cssout = '.one{float:right;}';
        $this->assertSame($cssout, $optimiser->process($cssin));
        $cssin = '.one {float: none;}';
        $cssout = '.one{float:none;}';
        $this->assertSame($cssout, $optimiser->process($cssin));

        // Invalid but tolerated.
        $cssin = '.one {float: center;}';
        $cssout = '.one{float:center;}';
        $this->assertSame($cssout, $optimiser->process($cssin));
    }

    /**
     * Test some totally invalid CSS optimisation.
     */
    public function test_invalid_css_handling() {
        $optimiser = new css_optimiser();

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
            $this->assertSame($cssout, $optimiser->process($css));
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
            $this->assertSame($cssout, $optimiser->process($css));
        }

        $cssin = '..one {background-color:color:red}';
        $cssout = '..one{background-color:color:red;}';
        $this->assertSame($cssout, $optimiser->process($cssin));

        $cssin = '#.one {background-color:color:red}';
        $cssout = '#.one{background-color:color:red;}';
        $this->assertSame($cssout, $optimiser->process($cssin));

        $cssin = '##one {background-color:color:red}';
        $cssout = '##one{background-color:color:red;}';
        $this->assertSame($cssout, $optimiser->process($cssin));

        $cssin = '.one {background-color:color:red}';
        $cssout = '.one{background-color:color:red;}';
        $this->assertSame($cssout, $optimiser->process($cssin));

        $cssin = '.one {background-color:red;color;border-color:blue}';
        $cssout = '.one{background-color:#F00;border-color:#00F;}';
        $this->assertSame($cssout, $optimiser->process($cssin));

        $cssin  = '{background-color:#123456;color:red;}{color:green;}';
        $cssout = "{background-color:#123456;color:#008000;}";
        $this->assertSame($cssout, $optimiser->process($cssin));

        $cssin  = '.one {color:red;} {color:green;} .one {background-color:blue;}';
        $cssout = ".one{color:#F00;background-color:#00F;} {color:#008000;}";
        $this->assertSame($cssout, $optimiser->process($cssin));
    }

    /**
     * Try to break some things.
     */
    public function test_break_things() {
        $optimiser = new css_optimiser();

        // Wildcard test.
        $cssin  = '* {color: black;}';
        $cssout = '*{color:#000;}';
        $this->assertSame($cssout, $optimiser->process($cssin));

        // Wildcard test.
        $cssin  = '.one * {color: black;}';
        $cssout = '.one *{color:#000;}';
        $this->assertSame($cssout, $optimiser->process($cssin));

        // Wildcard test.
        $cssin  = '* .one * {color: black;}';
        $cssout = '* .one *{color:#000;}';
        $this->assertSame($cssout, $optimiser->process($cssin));

        // Wildcard test.
        $cssin  = '*,* {color: black;}';
        $cssout = '*{color:#000;}';
        $this->assertSame($cssout, $optimiser->process($cssin));

        // Wildcard test.
        $cssin  = '*, * .one {color: black;}';
        $cssout = "*, * .one{color:#000;}";
        $this->assertSame($cssout, $optimiser->process($cssin));

        // Wildcard test.
        $cssin  = '*, *.one {color: black;}';
        $cssout = "*, *.one{color:#000;}";
        $this->assertSame($cssout, $optimiser->process($cssin));

        // Psedo test.
        $cssin  = '.one:before {color: black;}';
        $cssout = '.one:before{color:#000;}';
        $this->assertSame($cssout, $optimiser->process($cssin));

        // Psedo test.
        $cssin  = '.one:after {color: black;}';
        $cssout = '.one:after{color:#000;}';
        $this->assertSame($cssout, $optimiser->process($cssin));

        // Psedo test.
        $cssin  = '.one:onclick {color: black;}';
        $cssout = '.one:onclick{color:#000;}';
        $this->assertSame($cssout, $optimiser->process($cssin));

        // Test complex CSS rules that don't really exist but mimic other CSS rules.
        $cssin  = '.one {master-of-destruction: explode(\' \', "What madness");}';
        $cssout = '.one{master-of-destruction:explode(\' \', "What madness");}';
        $this->assertSame($cssout, $optimiser->process($cssin));

        // Test some complex IE css... I couldn't even think of a more complext solution
        // than the CSS they came up with.
        $cssin  = 'a { opacity: 0.5;-ms-filter:"progid:DXImageTransform.Microsoft.Alpha(Opacity=50)"; filter: alpha(opacity=50); }';
        $cssout = 'a{opacity:0.5;-ms-filter:"progid:DXImageTransform.Microsoft.Alpha(Opacity=50)";filter:alpha(opacity=50);}';
        $this->assertSame($cssout, $optimiser->process($cssin));
    }

    /**
     * A bulk processing test.
     */
    public function test_bulk_processing() {
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
        $CFG->cssoptimiserpretty = true;
        $optimiser = new css_optimiser();
        $this->assertSame($optimiser->process($cssin), $cssout);
        unset($CFG->cssoptimiserpretty);
    }

    /**
     * Test CSS colour matching.
     */
    public function test_css_is_colour() {
        // First lets test hex colours.
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

        // Note the following two colour's aren't really colours but browsers process
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

        // Next lets test real browser mapped colours.
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

        // Next lets test rgb(a) colours.
        $this->assertTrue(css_is_colour('rgb(255,255,255)'));
        $this->assertTrue(css_is_colour('rgb(0, 0, 0)'));
        $this->assertTrue(css_is_colour('RGB (255, 255   ,    255)'));
        $this->assertTrue(css_is_colour('rgba(0,0,0,0)'));
        $this->assertTrue(css_is_colour('RGBA(255,255,255,1)'));
        $this->assertTrue(css_is_colour('rgbA(255,255,255,0.5)'));
        $this->assertFalse(css_is_colour('rgb(-255,-255,-255)'));
        $this->assertFalse(css_is_colour('rgb(256,-256,256)'));

        // Now lets test HSL colours.
        $this->assertTrue(css_is_colour('hsl(0,0%,100%)'));
        $this->assertTrue(css_is_colour('hsl(180, 0%, 10%)'));
        $this->assertTrue(css_is_colour('hsl (360, 100%   ,    95%)'));

        // Finally test the special values.
        $this->assertTrue(css_is_colour('inherit'));
    }

    /**
     * Test the css_is_width function.
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

        // Valid widths but missing their unit specifier.
        $this->assertFalse(css_is_width('0.75'));
        $this->assertFalse(css_is_width('3'));
        $this->assertFalse(css_is_width('-1'));

        // Totally invalid widths.
        $this->assertFalse(css_is_width('-'));
        $this->assertFalse(css_is_width('bananas'));
        $this->assertFalse(css_is_width(''));
        $this->assertFalse(css_is_width('top'));
    }

    /**
     * This function tests some of the broken crazy CSS we have in Moodle.
     * For each of these things the value needs to be corrected if we can be 100%
     * certain what is going wrong, Or it needs to be left as is.
     */
    public function test_broken_css_found_in_moodle() {
        $optimiser = new css_optimiser();

        // Notice how things are out of order here but that they get corrected.
        $cssin = '.test {background:url([[pix:theme|pageheaderbgred]]) top center no-repeat}';
        $cssout = '.test{background:url([[pix:theme|pageheaderbgred]]) no-repeat top center;}';
        $this->assertSame($cssout, $optimiser->process($cssin));

        // Cursor hand isn't valid.
        $cssin  = '.test {cursor: hand;}';
        $cssout = '.test{cursor:hand;}';
        $this->assertSame($cssout, $optimiser->process($cssin));

        // Zoom property isn't valid.
        $cssin  = '.test {zoom: 1;}';
        $cssout = '.test{zoom:1;}';
        $this->assertSame($cssout, $optimiser->process($cssin));

        // Left isn't a valid position property.
        $cssin  = '.test {position: left;}';
        $cssout = '.test{position:left;}';
        $this->assertSame($cssout, $optimiser->process($cssin));

        // The dark red color isn't a valid HTML color but has a standardised
        // translation of #8B0000.
        $cssin  = '.test {color: darkred;}';
        $cssout = '.test{color:#8B0000;}';
        $this->assertSame($cssout, $optimiser->process($cssin));

        // You can't use argb colours as border colors.
        $cssin  = '.test {border-bottom: 1px solid rgba(0,0,0,0.25);}';
        $cssout = '.test{border-bottom:1px solid rgba(0,0,0,0.25);}';
        $this->assertSame($cssout, $optimiser->process($cssin));

        // Opacity with annoying IE equivalents....
        $cssin  = '.test {opacity: 0.5; -ms-filter:"progid:DXImageTransform.Microsoft.Alpha(Opacity=50)"; filter: alpha(opacity=50);}';
        $cssout = '.test{opacity:0.5;-ms-filter:"progid:DXImageTransform.Microsoft.Alpha(Opacity=50)";filter:alpha(opacity=50);}';
        $this->assertSame($cssout, $optimiser->process($cssin));
    }

    /**
     * Test keyframe declarations.
     */
    public function test_keyframe_css_animation() {
        global $CFG;
        $optimiser = new css_optimiser();

        $css = '.dndupload-arrow{width:56px;height:47px;position:absolute;animation:mymove 5s infinite;-moz-animation:mymove 5s infinite;-webkit-animation:mymove 5s infinite;background:url(\'[[pix:theme|fp/dnd_arrow]]\') no-repeat center;margin-left:-28px;}';
        $this->assertSame($css, $optimiser->process($css));

        $css = '@keyframes mymove {0%{top:10px;}12%{top:40px;}30%{top:20px;}65%{top:35px;}100%{top:9px;}}';
        $this->assertSame($css, $optimiser->process($css));

        $css  = "@keyframes mymove {0%{top:10px;}12%{top:40px;}30%{top:20px;}65%{top:35px;}100%{top:9px;}}\n";
        $css .= "@-moz-keyframes mymove {0%{top:10px;}12%{top:40px;}30%{top:20px;}65%{top:35px;}100%{top:9px;}}\n";
        $css .= "@-webkit-keyframes mymove {0%{top:10px;}12%{top:40px;}30%{top:20px;}65%{top:35px;}100%{top:9px;}}";
        $this->assertSame($css, $optimiser->process($css));

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
        $CFG->cssoptimiserpretty = true;
        $this->assertSame($cssout, $optimiser->process($cssin));
        unset($CFG->cssoptimiserpretty);

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
        $CFG->cssoptimiserpretty = true;
        $this->assertSame($cssout, $optimiser->process($cssin));
        unset($CFG->cssoptimiserpretty);
    }

    /**
     * Test media declarations.
     */
    public function test_media_rules() {
        $optimiser = new css_optimiser();

        $cssin = "@media print {\n  .test{background-color:#333;}\n}";
        $cssout = "@media print { .test{background-color:#333;} }";
        $this->assertSame($cssout, $optimiser->process($cssin));

        $cssin = "@media screen and (min-width:30px) {\n  #region-main-box{left: 30px;float: left;}\n}";
        $cssout = "@media screen and (min-width:30px) { #region-main-box{left:30px;float:left;} }";
        $this->assertSame($cssout, $optimiser->process($cssin));

        $cssin = "@media all and (min-width:500px) {\n  #region-main-box{left:30px;float:left;}\n}";
        $cssout = "@media all and (min-width:500px) { #region-main-box{left:30px;float:left;} }";
        $this->assertSame($cssout, $optimiser->process($cssin));

        $cssin = "@media (min-width:500px) {\n  #region-main-box{left:30px;float:left;}\n}";
        $cssout = "@media (min-width:500px) { #region-main-box{left:30px;float:left;} }";
        $this->assertSame($cssout, $optimiser->process($cssin));

        $cssin = "@media screen and (color), projection and (color) {\n  #region-main-box{left:30px;float:left;}\n}";
        $cssout = "@media screen and (color),projection and (color) { #region-main-box{left:30px;float:left;} }";
        $this->assertSame($cssout, $optimiser->process($cssin));

        $cssin = "@media print {\n  .test{background-color:#000;}\n}@media print {\n  .test{background-color:#FFF;}\n}";
        $cssout = "@media print { .test{background-color:#FFF;} }";
        $this->assertSame($cssout, $optimiser->process($cssin));

        $cssin = "@media screen and (min-width:30px) {\n  #region-main-box{background-color:#000;}\n}\n@media screen and (min-width:30px) {\n  #region-main-box{background-color:#FFF;}\n}";
        $cssout = "@media screen and (min-width:30px) { #region-main-box{background-color:#FFF;} }";
        $this->assertSame($cssout, $optimiser->process($cssin));

        $cssin = "@media screen and (min-width:30px) {\n  #region-main-box{background-color:#000;}\n}\n@media screen and (min-width:31px) {\n  #region-main-box{background-color:#FFF;}\n}";
        $cssout = "@media screen and (min-width:30px) { #region-main-box{background-color:#000;} }\n@media screen and (min-width:31px) { #region-main-box{background-color:#FFF;} }";
        $this->assertSame($cssout, $optimiser->process($cssin));

        $cssin = "@media (min-width: 768px) and (max-width: 979px) {\n*{*zoom:1;}}";
        $cssout = "@media (min-width: 768px) and (max-width: 979px) { *{*zoom:1;} }";
        $this->assertSame($cssout, $optimiser->process($cssin));

        $cssin = "#test {min-width:1200px;}@media (min-width: 768px) {#test {min-width: 1024px;}}";
        $cssout = "#test{min-width:1200px;} \n@media (min-width: 768px) { #test{min-width:1024px;} }";
        $this->assertSame($cssout, $optimiser->process($cssin));

        $cssin = "@media(min-width:768px){#page-calender-view .container fluid{min-width:1024px}}.section_add_menus{text-align:right}";
        $cssout = ".section_add_menus{text-align:right;} \n@media (min-width:768px) { #page-calender-view .container fluid{min-width:1024px;} }";
        $this->assertSame($cssout, $optimiser->process($cssin));

        $cssin = "@-ms-keyframes progress-bar-stripes{from{background-position:40px 0}to{background-position:0 0}}";
        $cssout = "@-ms-keyframes progress-bar-stripes {from{background-position:40px 0;}to{background-position:0 0;}}";
        $this->assertSame($cssout, $optimiser->process($cssin));
    }

    /**
     * Test the ordering of CSS optimisationss
     */
    public function test_css_optimisation_ordering() {
        $optimiser = $this->get_optimiser();

        $css = '.test{display:none;} .dialogue{display:block;} .dialogue-hidden{display:none;}';
        $this->assertSame($css, $optimiser->process($css));

        $cssin = '.test{display:none;} .dialogue-hidden{display:none;} .dialogue{display:block;}';
        $cssout = '.test, .dialogue-hidden{display:none;} .dialogue{display:block;}';
        $this->assertSame($cssout, $optimiser->process($cssin));
    }

    /**
     * Test CSS chunking
     */
    public function test_css_chunking() {
        // Test with an even number of styles.
        $css = 'a{}b{}c{}d{}e{}f{}';
        $chunks = css_chunk_by_selector_count($css, 'styles.php?type=test', 2);
        $this->assertInternalType('array', $chunks);
        $this->assertCount(3, $chunks);
        $this->assertArrayHasKey(0, $chunks);
        $this->assertArrayHasKey(1, $chunks);
        $this->assertArrayHasKey(2, $chunks);
        $this->assertSame('a{}b{}', $chunks[0]);
        $this->assertSame('c{}d{}', $chunks[1]);
        $this->assertSame("@import url(styles.php?type=test&chunk=1);\n@import url(styles.php?type=test&chunk=2);\ne{}f{}", $chunks[2]);

        // Test with an odd number of styles.
        $css = 'a{}b{}c{}d{}e{}';
        $chunks = css_chunk_by_selector_count($css, 'styles.php?type=test', 2);
        $this->assertInternalType('array', $chunks);
        $this->assertCount(3, $chunks);
        $this->assertArrayHasKey(0, $chunks);
        $this->assertArrayHasKey(1, $chunks);
        $this->assertArrayHasKey(2, $chunks);
        $this->assertSame('a{}b{}', $chunks[0]);
        $this->assertSame('c{}d{}', $chunks[1]);
        $this->assertSame("@import url(styles.php?type=test&chunk=1);\n@import url(styles.php?type=test&chunk=2);\ne{}", $chunks[2]);

        // Test well placed commas.
        $css = 'a,b{}c,d{}e,f{}';
        $chunks = css_chunk_by_selector_count($css, 'styles.php?type=test', 2);
        $this->assertInternalType('array', $chunks);
        $this->assertCount(3, $chunks);
        $this->assertArrayHasKey(0, $chunks);
        $this->assertArrayHasKey(1, $chunks);
        $this->assertArrayHasKey(2, $chunks);
        $this->assertSame('a,b{}', $chunks[0]);
        $this->assertSame('c,d{}', $chunks[1]);
        $this->assertSame("@import url(styles.php?type=test&chunk=1);\n@import url(styles.php?type=test&chunk=2);\ne,f{}", $chunks[2]);

        // Test unfortunately placed commas.
        $css = 'a{}b,c{color:red;}d{}e{}f{}';
        $chunks = css_chunk_by_selector_count($css, 'styles.php?type=test', 2);
        $this->assertInternalType('array', $chunks);
        $this->assertCount(4, $chunks);
        $this->assertArrayHasKey(0, $chunks);
        $this->assertArrayHasKey(1, $chunks);
        $this->assertArrayHasKey(2, $chunks);
        $this->assertArrayHasKey(3, $chunks);
        $this->assertSame('a{}', $chunks[0]);
        $this->assertSame('b,c{color:red;}', $chunks[1]);
        $this->assertSame('d{}e{}', $chunks[2]);
        $this->assertSame("@import url(styles.php?type=test&chunk=1);\n@import url(styles.php?type=test&chunk=2);\n@import url(styles.php?type=test&chunk=3);\nf{}", $chunks[3]);

        // Test unfortunate CSS.
        $css = 'a,b,c,d,e,f{color:red;}';
        $chunks = css_chunk_by_selector_count($css, 'styles.php?type=test', 2, 0);
        $this->assertInternalType('array', $chunks);
        $this->assertCount(1, $chunks);
        $this->assertArrayHasKey(0, $chunks);
        $this->assertSame('a,b,c,d,e,f{color:red;}', $chunks[0]);
        $this->assertDebuggingCalled('Could not find a safe place to split at offset(s): 6. Those were ignored.');

        // Test to make sure invalid CSS isn't totally ruined.
        $css = 'a{},,,e{},';
        $chunks = css_chunk_by_selector_count($css, 'styles.php?type=test', 2);
        // Believe it or not we want to care what comes out here as this will be parsed correctly
        // by a browser.
        $this->assertInternalType('array', $chunks);
        $this->assertCount(3, $chunks);
        $this->assertArrayHasKey(0, $chunks);
        $this->assertArrayHasKey(1, $chunks);
        $this->assertArrayHasKey(2, $chunks);
        $this->assertSame('a{}', $chunks[0]);
        $this->assertSame(',,,e{}', $chunks[1]);
        $this->assertSame("@import url(styles.php?type=test&chunk=1);\n@import url(styles.php?type=test&chunk=2);\n,", $chunks[2]);
        $this->assertDebuggingCalled('Could not find a safe place to split at offset(s): 6. Those were ignored.');

        // Test utter crap CSS to make sure we don't loop to our deaths.
        $css = 'a,b,c,d,e,f';
        $chunks = css_chunk_by_selector_count($css, 'styles.php?type=test', 2);
        $this->assertInternalType('array', $chunks);
        $this->assertCount(1, $chunks);
        $this->assertArrayHasKey(0, $chunks);
        $this->assertSame($css, $chunks[0]);
        $this->assertDebuggingCalled('Could not find a safe place to split at offset(s): 6. Those were ignored.');

        // Test another death situation to make sure we're invincible.
        $css = 'a,,,,,e';
        $chunks = css_chunk_by_selector_count($css, 'styles.php?type=test', 2);
        $this->assertInternalType('array', $chunks);
        $this->assertDebuggingCalled('Could not find a safe place to split at offset(s): 4. Those were ignored.');
        // I don't care what the outcome is, I just want to make sure it doesn't die.

        // Test media queries.
        $css = '@media (min-width: 980px) { .a,.b{} }';
        $chunks = css_chunk_by_selector_count($css, 'styles.php?type=test', 2);
        $this->assertCount(1, $chunks);
        $this->assertSame('@media (min-width: 980px) { .a,.b{} }', $chunks[0]);

        // Test media queries, with commas.
        $css = '.a{} @media (min-width: 700px), handheld and (orientation: landscape) { .b{} }';
        $chunks = css_chunk_by_selector_count($css, 'styles.php?type=test', 2);
        $this->assertCount(1, $chunks);
        $this->assertSame($css, $chunks[0]);

        // Test special rules.
        $css = 'a,b{ background-image: linear-gradient(to bottom, #ffffff, #cccccc);}d,e{}';
        $chunks = css_chunk_by_selector_count($css, 'styles.php?type=test', 2);
        $this->assertCount(2, $chunks);
        $this->assertSame('a,b{ background-image: linear-gradient(to bottom, #ffffff, #cccccc);}', $chunks[0]);
        $this->assertSame("@import url(styles.php?type=test&chunk=1);\nd,e{}", $chunks[1]);

        // Test media queries with too many selectors.
        $css = '@media (min-width: 980px) { a,b,c,d{} }';
        $chunks = css_chunk_by_selector_count($css, 'styles.php?type=test', 2);
        $this->assertCount(1, $chunks);
        $this->assertSame('@media (min-width: 980px) { a,b,c,d{} }', $chunks[0]);
        $this->assertDebuggingCalled('Could not find a safe place to split at offset(s): 34. Those were ignored.');

        // Complex test.
        $css = '@media (a) {b{}} c{} d,e{} f,g,h{} i,j{x:a,b,c} k,l{} @media(x){l,m{ y: a,b,c}} n{}';
        $chunks = css_chunk_by_selector_count($css, 'styles.php?type=test', 3);
        $this->assertCount(6, $chunks);
        $this->assertSame('@media (a) {b{}} c{}', $chunks[0]);
        $this->assertSame(' d,e{}', $chunks[1]);
        $this->assertSame(' f,g,h{}', $chunks[2]);
        $this->assertSame(' i,j{x:a,b,c}', $chunks[3]);
        $this->assertSame(' k,l{}', $chunks[4]);
        $this->assertSame("@import url(styles.php?type=test&chunk=1);\n@import url(styles.php?type=test&chunk=2);\n@import url(styles.php?type=test&chunk=3);\n@import url(styles.php?type=test&chunk=4);\n@import url(styles.php?type=test&chunk=5);\n @media(x){l,m{ y: a,b,c}} n{}", $chunks[5]);

        // Multiple offset errors.
        $css = 'a,b,c{} d,e,f{}';
        $chunks = css_chunk_by_selector_count($css, 'styles.php?type=test', 2);
        $this->assertCount(2, $chunks);
        $this->assertSame('a,b,c{}', $chunks[0]);
        $this->assertSame("@import url(styles.php?type=test&chunk=1);\n d,e,f{}", $chunks[1]);
        $this->assertDebuggingCalled('Could not find a safe place to split at offset(s): 6, 14. Those were ignored.');

        // Test the split according to IE.
        $css = str_repeat('a{}', 4100);
        $chunks = css_chunk_by_selector_count($css, 'styles.php?type=test');
        $this->assertCount(2, $chunks);
        $this->assertSame(str_repeat('a{}', 4095), $chunks[0]);
        $this->assertSame("@import url(styles.php?type=test&chunk=1);\n" . str_repeat('a{}', 5), $chunks[1]);

        // Test strip out comments.
        $css = ".a {/** a\nb\nc */} /** a\nb\nc */ .b{} /** .c,.d{} */ e{}";
        $chunks = css_chunk_by_selector_count($css, 'styles.php?type=test', 2);
        $this->assertCount(2, $chunks);
        $this->assertSame('.a {}  .b{}', $chunks[0]);
        $this->assertSame("@import url(styles.php?type=test&chunk=1);\n  e{}", $chunks[1]);

        // Test something with unicode characters.
        $css = 'a,b{} nav a:hover:after { content: "↓"; } b{ color:test;}';
        $chunks = css_chunk_by_selector_count($css, 'styles.php?type=test', 2);
        $this->assertCount(2, $chunks);
        $this->assertSame('a,b{}', $chunks[0]);
        $this->assertSame("@import url(styles.php?type=test&chunk=1);\n nav a:hover:after { content: \"↓\"; } b{ color:test;}", $chunks[1]);

        // Test that if there is broken CSS with too many close brace symbols,
        // media rules after that point are still kept together.
        $mediarule = '@media (width=480) {a{}b{}}';
        $css = 'c{}}' . $mediarule . 'd{}';
        $chunks = css_chunk_by_selector_count($css, 'styles.php?type=test', 2);
        $this->assertCount(3, $chunks);
        $this->assertEquals($mediarule, $chunks[1]);

        // Test that this still works even with too many close brace symbols
        // inside a media query (note: that broken media query may be split
        // after the break, but any following ones should not be).
        $brokenmediarule = '@media (width=480) {c{}}d{}}';
        $css = $brokenmediarule . 'e{}' . $mediarule . 'f{}';
        $chunks = css_chunk_by_selector_count($css, 'styles.php?type=test', 2);
        $this->assertCount(4, $chunks);
        $this->assertEquals($mediarule, $chunks[2]);
    }

    /**
     * Test CSS3.
     */
    public function test_css3() {
        $optimiser = $this->get_optimiser();

        $css = '.test > .test{display:inline-block;}';
        $this->assertSame($css, $optimiser->process($css));

        $css = '*{display:inline-block;}';
        $this->assertSame($css, $optimiser->process($css));

        $css = 'div > *{display:inline-block;}';
        $this->assertSame($css, $optimiser->process($css));

        $css = 'div:nth-child(3){display:inline-block;}';
        $this->assertSame($css, $optimiser->process($css));

        $css = '.test:nth-child(3){display:inline-block;}';
        $this->assertSame($css, $optimiser->process($css));

        $css = '*:nth-child(3){display:inline-block;}';
        $this->assertSame($css, $optimiser->process($css));

        $css = '*[id]{display:inline-block;}';
        $this->assertSame($css, $optimiser->process($css));

        $css = '*[id=blah]{display:inline-block;}';
        $this->assertSame($css, $optimiser->process($css));

        $css = '*[*id=blah]{display:inline-block;}';
        $this->assertSame($css, $optimiser->process($css));

        $css = '*[*id=blah_]{display:inline-block;}';
        $this->assertSame($css, $optimiser->process($css));

        $css = '*[id^=blah*d]{display:inline-block;}';
        $this->assertSame($css, $optimiser->process($css));

        $css = '.test{-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box;}';
        $this->assertSame($css, $optimiser->process($css));

        $css = '#test{box-shadow:inset 0 1px 0 rgba(255, 255, 255, 0.2), 0 1px 2px rgba(0, 0, 0, 0.05);}';
        $this->assertSame($css, $optimiser->process($css));
    }

    /**
     * Test browser hacks here.
     */
    public function test_browser_hacks() {
        $optimiser = $this->get_optimiser();

        $css = '#test{*zoom:1;}';
        $this->assertSame($css, $optimiser->process($css));

        $css = '.test{width:75%;*width:76%;}';
        $this->assertSame($css, $optimiser->process($css));

        $css = '#test{*zoom:1;*display:inline;}';
        $this->assertSame($css, $optimiser->process($css));

        $css = '.test{width:75%;*width:76%;width:76%}';
        $this->assertSame('.test{width:76%;*width:76%;}', $optimiser->process($css));

        $css = '.test{width:75%;*width:76%;*width:75%}';
        $this->assertSame('.test{width:75%;*width:75%;}', $optimiser->process($css));
    }
}