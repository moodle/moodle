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
 * Tests for the core_rtlcss class.
 *
 * The core_rtlcss class extends \MoodleHQ\RTLCSS\RTLCSS library which depends on sabberworm/php-css-parser library.
 * This test verifies that css parsing works as expected should any of the above change.
 *
 * @package    core
 * @category   phpunit
 * @copyright  2019 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

use Sabberworm\CSS\Parser;
use Sabberworm\CSS\OutputFormat;

/**
 * Class rtlcss_test.
 */
class rtlcss_test extends basic_testcase {
    /**
     * Data provider.
     * @return array
     */
    public function background_image_provider() {
        return [
            /* Not supported by MoodleHQ/RTLCSS yet.
            [[
                'should' => 'Should process string map in url (processUrls:true)',
                'expected' => 'div { background-image: url(images/rtl.png), url(images/right.png);}',
                'input'    => 'div { background-image: url(images/ltr.png), url(images/left.png);}',
                'reversable' => true,
                'options' => [ 'processUrls' => true ],
                'skip' => true
            ]],
            [[
                'should' => 'Should not negate color value for linear gradient',
                'expected' => 'div { background-image: linear-gradient(rgba(255, 255, 255, 0.3) 0%, #ff8 100%);}',
                'input'    => 'div { background-image: linear-gradient(rgba(255, 255, 255, 0.3) 0%, #ff8 100%);}',
                'reversable' => true,
                'skip' => true
            ]],
            [[
                'should' => 'Should not negate color value for linear gradient with calc',
                'expected' => 'div { background-image: linear-gradient(rgba(255, 255, calc((125 * 2) + 5), 0.3) 0%, #ff8 100%);}',
                'input'    => 'div { background-image: linear-gradient(rgba(255, 255, calc((125 * 2) + 5), 0.3) 0%, #ff8 100%);}',
                'reversable' => true,
                'skip' => true
            ]],
            [[
                'should' => 'Should negate angle value for linear gradient',
                'expected' => 'div { background-image: linear-gradient(13.25deg, rgba(255, 255, 255, .15) 25%, transparent 25%);}',
                'input'    => 'div { background-image: linear-gradient(-13.25deg, rgba(255, 255, 255, .15) 25%, transparent 25%);}',
                'reversable' => true,
                'skip' => true
            ]]
            */
        ];
    }

    /**
     * Data provider.
     * @return array
     */
    public function background_position_provider() {
        return [
            [[
                'should' => 'Should complement percentage horizontal position ',
                'expected' => 'div {background-position:100% 75%;}',
                'input' => 'div {background-position:0 75%;}',
                'reversable' => false
            ]],
            /* Not supported by MoodleHQ/RTLCSS yet.
            [[
                'should' => 'Should complement percentage horizontal position with calc',
                'expected' => 'div {background-position:calc(100% - (30% + 50px)) 75%;}',
                'input' => 'div {background-position:calc(30% + 50px) 75%;}',
                'reversable' => false,
                'skip' => true
            ]],
            [[
                'should' => 'Should complement percentage horizontal position ',
                'expected' => 'div {background-position:81.25% 75%, 10.75% top;}',
                'input' => 'div {background-position:18.75% 75%, 89.25% top;}',
                'reversable' => true,
                'skip' => true
            ]],
            [[
                'should' => 'Should complement percentage horizontal position with calc',
                'expected' => 'div {background-position:calc(100% - (30% + 50px)) calc(30% + 50px), 10.75% top;}',
                'input' => 'div {background-position:calc(30% + 50px) calc(30% + 50px), 89.25% top;}',
                'reversable' => false,
                'skip' => true
            ]],
            [[
                'should' => 'Should swap left with right',
                'expected' => 'div {background-position:right 75%, left top;}',
                'input' => 'div {background-position:left 75%, right top;}',
                'reversable' => true,
                'skip' => true
            ]],
            [[
                'should' => 'Should swap left with right wit calc',
                'expected' => 'div {background-position:right -ms-calc(30% + 50px), left top;}',
                'input' => 'div {background-position:left -ms-calc(30% + 50px), right top;}',
                'reversable' => true,
                'skip' => true
            ]],
            */
            [[
                'should' => 'Should complement percentage: position-x (treat 0 as 0%)',
                'expected' => 'div {background-position-x:100%, 0%;}',
                'input' => 'div {background-position-x:0, 100%;}',
                'reversable' => false
            ]],
            [[
                'should' => 'Should complement percentage: position-x',
                'expected' => 'div {background-position-x:81.75%, 11%;}',
                'input' => 'div {background-position-x:18.25%, 89%;}',
                'reversable' => true
            ]],
            /* Not supported by MoodleHQ/RTLCSS yet.
            [[
                'should' => 'Should complement percentage with calc: position-x',
                'expected' => 'div {background-position-x:calc(100% - (30% + 50px)), -webkit-calc(100% - (30% + 50px));}',
                'input' => 'div {background-position-x:calc(30% + 50px), -webkit-calc(30% + 50px);}',
                'reversable' => false,
                'skip' => true
            ]],
            */
            [[
                'should' => 'Should swap left with right: position-x',
                'expected' => 'div {background-position-x:right, left;}',
                'input' => 'div {background-position-x:left, right;}',
                'reversable' => true
            ]],
            [[
                'should' => 'Should keep as is: position-x',
                'expected' => 'div {background-position-x:100px, 0px;}',
                'input' => 'div {background-position-x:100px, 0px;}',
                'reversable' => true
            ]],

            [[
                'should' => 'Should flip when using 3 positions',
                'expected' => 'div {background-position:center right 1px;}',
                'input' => 'div {background-position:center left 1px;}',
                'reversable' => true
            ]],
            [[
                'should' => 'Should flip when using 4 positions',
                'expected' => 'div {background-position:center 2px right 1px;}',
                'input' => 'div {background-position:center 2px left 1px;}',
                'reversable' => true
            ]],
            [[
                'should' => 'Should flip when using 4 positions mixed',
                'expected' => 'div {background-position:right 2px bottom 1px;}',
                'input' => 'div {background-position:left 2px bottom 1px;}',
                'reversable' => true
            ]]
        ];
    }

    /**
     * Data provider.
     * @return array
     */
    public function background_provider() {
        return [
            [[
                'should' => 'Should treat 0 as 0%',
                'expected' => '.banner { background: 100% top url("topbanner.png") #00d repeat-y fixed; }',
                'input' => '.banner { background: 0 top url("topbanner.png") #00d repeat-y fixed; }',
                'reversable' => false
            ]],
            [[
                'should' => 'Should complement percentage horizontal position',
                'expected' => '.banner { background: 81% top url("topbanner.png") #00d repeat-y fixed; }',
                'input' => '.banner { background: 19% top url("topbanner.png") #00d repeat-y fixed; }',
                'reversable' => true
            ]],
            /* Not supported by MoodleHQ/RTLCSS yet.
            [[
                'should' => 'Should complement calc horizontal position',
                'expected' => '.banner { background: calc(100% - (19% + 2px)) top url(topbanner.png) #00d repeat-y fixed; }',
                'input' => '.banner { background: calc(19% + 2px) top url(topbanner.png) #00d repeat-y fixed; }',
                'reversable' => false,
                'skip' => true
            ]],
            */
            [[
                'should' => 'Should mirror keyword horizontal position',
                'expected' => '.banner { background: right top url("topbanner.png") #00d repeat-y fixed; }',
                'input' => '.banner { background: left top url("topbanner.png") #00d repeat-y fixed; }',
                'reversable' => true
            ]],
            [[
                'should' => 'Should not process string map in url (default)',
                'expected' => '.banner { background: 10px top url("ltr-top-right-banner.png") #00d repeat-y fixed; }',
                'input' => '.banner { background: 10px top url("ltr-top-right-banner.png") #00d repeat-y fixed; }',
                'reversable' => true
            ]],
            /* Not supported by MoodleHQ/RTLCSS yet.
            [[
                'should' => 'Should process string map in url (processUrls:true)',
                'expected' => '.banner { background: 10px top url(rtl-top-left-banner.png) #00d repeat-y fixed; }',
                'input' => '.banner { background: 10px top url(ltr-top-right-banner.png) #00d repeat-y fixed; }',
                'reversable' => true,
                'options' => [ 'processUrls' => true ],
                'skip' => true
            ]],
            [[
                'should' => 'Should process string map in url (processUrls:{decl:true})',
                'expected' => '.banner { background: 10px top url(rtl-top-left-banner.png) #00d repeat-y fixed; }',
                'input' => '.banner { background: 10px top url(ltr-top-right-banner.png) #00d repeat-y fixed; }',
                'reversable' => true,
                'options' => [ 'processUrls' => [ 'decl' => true ] ],
                'skip' => true
            ]],
            */
            [[
                'should' => 'Should not process string map in url (processUrls:{atrule:true})',
                'expected' => '.banner { background: 10px top url("ltr-top-right-banner.png") #00d repeat-y fixed; }',
                'input' => '.banner { background: 10px top url("ltr-top-right-banner.png") #00d repeat-y fixed; }',
                'reversable' => true,
                'options' => [ 'processUrls' => [ 'atrule' => true ] ]
            ]],
            [[
                'should' => 'Should not swap bright:bleft, ultra:urtla',
                'expected' => '.banner { background: 10px top url("ultra/bright.png") #00d repeat-y fixed; }',
                'input' => '.banner { background: 10px top url("ultra/bright.png") #00d repeat-y fixed; }',
                'reversable' => true
            ]],
            /* Not supported by MoodleHQ/RTLCSS yet.
            [[
                'should' => 'Should swap bright:bleft, ultra:urtla (processUrls: true, greedy)',
                'expected' => '.banner { background: 10px top url("urtla/bleft.png") #00d repeat-y fixed; }',
                'input' => '.banner { background: 10px top url("ultra/bright.png") #00d repeat-y fixed; }',
                'reversable' => true,
                'options' => [ 'processUrls' => true, 'greedy' => true ],
                'skip' => true
            ]],
            */
            [[
                'should' => 'Should not flip hex colors ',
                'expected' => '.banner { background: #ff0; }',
                'input' => '.banner { background: #ff0; }',
                'reversable' => true
            ]]
        ];
    }

    /**
     * Data provider.
     * @return array
     */
    public function directives_provider() {
        return [
            [[
                'should' => 'Should ignore flipping - rule level (default)',
                'expected' => 'div {left:10px;text-align:right;}',
                'input' => '/*rtl:ignore*/div { left:10px; text-align:right;}',
                'reversable' => false
            ]],
            [[
                'should' => 'Should ignore flipping - rule level (!important comment)',
                'expected' => 'div {left:10px;text-align:right;}',
                'input' => '/*!rtl:ignore*/div { left:10px; text-align:right;}',
                'reversable' => false,
            ]],
            // Not supported by MoodleHQ/RTLCSS yet.
            //[[
            //    'should' => 'Should ignore flipping - decl. level (default)',
            //    'expected' => 'div {left:10px;text-align:left;}',
            //    'input' => 'div {left:10px/*rtl:ignore*/;text-align:right;}',
            //    'reversable' => false,
            //    'skip' => true
            //]],
            [[
                'should' => 'Should add raw css rules',
                'expected' => 'div {left:10px;text-align:right;} a {display:block;}',
                'input' => '/*rtl:raw: div { left:10px;text-align:right;}*/ a {display:block;}',
                'reversable' => false
            ]],
            [[
                'should' => 'Should add raw css declarations',
                'expected' => 'div {font-family:"Droid Kufi Arabic";right:10px;text-align:left;}',
                'input' => 'div { /*rtl:raw: font-family: "Droid Kufi Arabic";*/ left:10px;text-align:right;}',
                'reversable' => false
            ]],
            [[
                'should' => 'Should support block-style',
                'expected' => 'div {left:10px;text-align:right;}',
                'input' => ' div {/*rtl:begin:ignore*/left:10px;/*rtl:end:ignore*/ text-align:left;}',
                'reversable' => false
            ]],
            [[
                'should' => 'Should support none block-style',
                'expected' => 'div {left:10px;text-align:left;}',
                'input' => ' /*rtl:ignore*/div {left:10px; text-align:left;}',
                'reversable' => false
            ]],
            [[
                'should' => 'Should remove rules (block-style)',
                'expected' => 'b {float:right;}',
                'input' => ' /*rtl:begin:remove*/div {left:10px; text-align:left;} a { display:block;} /*rtl:end:remove*/ b{float:left;}',
                'reversable' => false
            ]],
            [[
                'should' => 'Should remove rules',
                'expected' => 'a {display:block;} b {float:right;}',
                'input' => ' /*rtl:remove*/div {left:10px; text-align:left;} a { display:block;} b{float:left;}',
                'reversable' => false
            ]],
            [[
                'should' => 'Should remove declarations',
                'expected' => 'div {text-align:right;}',
                'input' => 'div {/*rtl:remove*/left:10px; text-align:left;}',
                'reversable' => false
            ]],
            [[
                'should' => 'Should remove declarations (block-style)',
                'expected' => 'div {display:inline;}',
                'input' => 'div {/*rtl:begin:remove*/left:10px; text-align:left;/*rtl:end:remove*/ display:inline;}',
                'reversable' => false
            ]],
            // Not supported by MoodleHQ/RTLCSS yet.
            //[[
            //    'should' => 'Final/trailing comment ignored bug (block style): note a tag rules are NOT flipped as they should be',
            //    'expected' => 'div {left:10px;text-align:left;} a {right:10px;}',
            //    'input' => 'div {/*rtl:begin:ignore*/left:10px; text-align:left;/*rtl:end:ignore*/} a {left:10px;}',
            //    'reversable' => false,
            //    'skip' => true
            //]]
        ];
    }

    /**
     * Data provider.
     * @return array
     */
    public function properties_provider() {
        return [
            [[
                'should' => 'Should mirror property name: border-top-right-radius',
                'expected' => 'div { border-top-left-radius:15px; }',
                'input' => 'div { border-top-right-radius:15px; }',
                'reversable' => true
            ]],
            [[
                'should' => 'Should mirror property name: border-bottom-right-radius',
                'expected' => 'div { border-bottom-left-radius:15px; }',
                'input' => 'div { border-bottom-right-radius:15px; }',
                'reversable' => true
            ]],
            [[
                'should' => 'Should mirror property name: border-left',
                'expected' => 'div { border-right:1px solid black; }',
                'input' => 'div { border-left:1px solid black; }',
                'reversable' => true
            ]],
            [[
                'should' => 'Should mirror property name: border-left-color',
                'expected' => 'div { border-right-color:black; }',
                'input' => 'div { border-left-color:black; }',
                'reversable' => true
            ]],
            [[
                'should' => 'Should mirror property name: border-left-style',
                'expected' => 'div { border-right-style:solid; }',
                'input' => 'div { border-left-style:solid; }',
                'reversable' => true
            ]],
            [[
                'should' => 'Should mirror property name: border-left-width',
                'expected' => 'div { border-right-width:1em; }',
                'input' => 'div { border-left-width:1em; }',
                'reversable' => true
            ]],
            [[
                'should' => 'Should mirror property name: left',
                'expected' => 'div { right:auto; }',
                'input' => 'div { left:auto; }',
                'reversable' => true
            ]],
            [[
                'should' => 'Should mirror property name: margin-left',
                'expected' => 'div { margin-right:2em; }',
                'input' => 'div { margin-left:2em; }',
                'reversable' => true
            ]],
            [[
                'should' => 'Should mirror property name: padding-left',
                'expected' => 'div { padding-right:2em; }',
                'input' => 'div { padding-left:2em; }',
                'reversable' => true
            ]]
        ];
    }

    /**
     * Data provider.
     * @return array
     */
    public function special_provider() {
        return [
            /* Not supported by MoodleHQ/RTLCSS yet.
            [[
                'should' => 'Should not negate tokens',
                'expected' => 'div { box-shadow: rgba(0, 128, 128, .98) inset -5em 1em 0;}',
                'input' => 'div { box-shadow: rgba(0, 128, 128, .98) inset 5em 1em 0;}',
                'reversable' => true,
                'skip' => true,
            ]]
            */
        ];
    }

    /**
     * Data provider.
     * @return array
     */
    public function transform_origin_provider() {
        return [
            [[
                'should' => 'Should mirror (x-offset: 0 means 0%)',
                'expected' => 'div { transform-origin:100%; }',
                'input' => 'div { transform-origin:0; }',
                'reversable' => false
            ]],
            [[
                'should' => 'Should mirror (x-offset)',
                'expected' => 'div { transform-origin:90.25%; }',
                'input' => 'div { transform-origin:9.75%; }',
                'reversable' => true
            ]],
            /* Not supported by MoodleHQ/RTLCSS yet.
            [[
                'should' => 'Should mirror calc (x-offset)',
                'expected' => 'div { transform-origin: -moz-calc(100% - (((25%/2) * 10px))); }',
                'input' => 'div { transform-origin: -moz-calc(((25%/2) * 10px)); }',
                'reversable' => false,
                'skip' => true
            ]],
            */
            [[
                'should' => 'Should not mirror (x-offset: not percent, not calc)',
                'expected' => 'div { transform-origin:10.75px; }',
                'input' => 'div { transform-origin:10.75px; }',
                'reversable' => false
            ]],
            [[
                'should' => 'Should mirror (offset-keyword)',
                'expected' => 'div { transform-origin:right; }',
                'input' => 'div { transform-origin:left; }',
                'reversable' => true
            ]],
            [[
                'should' => 'Should mirror (x-offset y-offset: 0 means 0%)',
                'expected' => 'div { transform-origin:100% 0; }',
                'input' => 'div { transform-origin:0 0; }',
                'reversable' => false
            ]],
            /* Not supported by MoodleHQ/RTLCSS yet.
            [[
                'should' => 'Should mirror with y being calc (x-offset y-offset: 0 means 0%)',
                'expected' => 'div { transform-origin:100% -webkit-calc(15% * (3/2)); }',
                'input' => 'div { transform-origin:0 -webkit-calc(15% * (3/2)); }',
                'reversable' => false,
                'skip' => true
            ]],
            */
            [[
                'should' => 'Should mirror percent (x-offset y-offset)',
                'expected' => 'div { transform-origin:30.25% 10%; }',
                'input' => 'div { transform-origin:69.75% 10%; }',
                'reversable' => true
            ]],
            /* Not supported by MoodleHQ/RTLCSS yet.
            [[
                'should' => 'Should mirror with x being calc (x-offset y-offset)',
                'expected' => 'div { transform-origin: -webkit-calc(100% - (15% * (3/2))) 30.25%; }',
                'input' => 'div { transform-origin: -webkit-calc(15% * (3/2)) 30.25%; }',
                'reversable' => false,
                'skip' => true
            ]],
            [[
                'should' => 'Should mirror with y being calc (x-offset y-offset)',
                'expected' => 'div { transform-origin:30.25% calc(15% * (3/2)); }',
                'input' => 'div { transform-origin:69.75% calc(15% * (3/2)); }',
                'reversable' => true,
                'skip' => true
            ]],
            */
            [[
                'should' => 'Should mirror (y-offset x-offset-keyword)',
                'expected' => 'div { transform-origin:70% right; }',
                'input' => 'div { transform-origin:70% left; }',
                'reversable' => true
            ]],
            /* Not supported by MoodleHQ/RTLCSS yet.
            [[
                'should' => 'Should mirror with calc (y-offset x-offset-keyword)',
                'expected' => 'div { transform-origin:-ms-calc(140%/2) right; }',
                'input' => 'div { transform-origin:-ms-calc(140%/2) left; }',
                'reversable' => true,
                'skip' => true
            ]],
            */
            [[
                'should' => 'Should mirror (x-offset-keyword y-offset)',
                'expected' => 'div { transform-origin:right 70%; }',
                'input' => 'div { transform-origin:left 70%; }',
                'reversable' => true
            ]],
            /* Not supported by MoodleHQ/RTLCSS yet.
            [[
                'should' => 'Should mirror with calc (x-offset-keyword y-offset)',
                'expected' => 'div { transform-origin:right -moz-calc(((140%/2))); }',
                'input' => 'div { transform-origin:left -moz-calc(((140%/2))); }',
                'reversable' => true,
                'skip' => true
            ]],
            */
            [[
                'should' => 'Should mirror (y-offset-keyword x-offset)',
                'expected' => 'div { transform-origin:top 30.25%; }',
                'input' => 'div { transform-origin:top 69.75%; }',
                'reversable' => true
            ]],
            /* Not supported by MoodleHQ/RTLCSS yet.
            [[
                'should' => 'Should not mirror with x being calc (y-offset-keyword x-offset)',
                'expected' => 'div { transform-origin:top calc(100% - (((140%/2)))); }',
                'input' => 'div { transform-origin:top calc(((140%/2))); }',
                'reversable' => false,
                'skip' => true
            ]],
            */
            [[
                'should' => 'Should mirror (x-offset-keyword y-offset-keyword)',
                'expected' => 'div { transform-origin:right top; }',
                'input' => 'div { transform-origin:left top; }',
                'reversable' => true
            ]],
            [[
                'should' => 'Should mirror (y-offset-keyword x-offset-keyword)',
                'expected' => 'div { transform-origin:top right; }',
                'input' => 'div { transform-origin:top left; }',
                'reversable' => true
            ]],
            [[
                'should' => 'Should mirror (x-offset y-offset z-offset)',
                'expected' => 'div { transform-origin:80.25% 30% 10%; }',
                'input' => 'div { transform-origin:19.75% 30% 10%; }',
                'reversable' => true
            ]],
            /* Not supported by MoodleHQ/RTLCSS yet.
            [[
                'should' => 'Should mirror with x being calc (x-offset y-offset z-offset)',
                'expected' => 'div { transform-origin: calc(100% - (25% * 3 + 20px)) 30% 10%; }',
                'input' => 'div { transform-origin: calc(25% * 3 + 20px) 30% 10%; }',
                'reversable' => false,
                'skip' => true
            ]],
            */
            [[
                'should' => 'Should mirror (y-offset x-offset-keyword z-offset)',
                'expected' => 'div { transform-origin:20% right 10%; }',
                'input' => 'div { transform-origin:20% left 10%; }',
                'reversable' => true
            ]],
            [[
                'should' => 'Should mirror (x-offset-keyword y-offset z-offset)',
                'expected' => 'div { transform-origin:left 20% 10%; }',
                'input' => 'div { transform-origin:right 20% 10%; }',
                'reversable' => true
            ]],
            [[
                'should' => 'Should mirror (x-offset-keyword y-offset-keyword z-offset)',
                'expected' => 'div { transform-origin:left bottom 10%; }',
                'input' => 'div { transform-origin:right bottom 10%; }',
                'reversable' => true
            ]],
            [[
                'should' => 'Should mirror (y-offset-keyword x-offset-keyword z-offset)',
                'expected' => 'div { transform-origin:bottom left 10%; }',
                'input' => 'div { transform-origin:bottom right 10%; }',
                'reversable' => true
            ]]
        ];
    }

    /**
     * Data provider.
     * @return array
     */
    public function transforms_provider() {
        return [
            /* Not supported by MoodleHQ/RTLCSS yet.
            [[
                'should' => 'Should mirror transform : matrix',
                'expected' => 'div { transform: matrix(2, 0.1, 20.75, 2, 2, 2); }',
                'input' => 'div { transform: matrix(2, -0.1, -20.75, 2, -2, 2); }',
                'reversable' => true,
                'skip' => true
            ]],
            [[
                'should' => 'Should mirror transform (with no digits before dot): matrix',
                'expected' => 'div { transform: matrix(2, 0.1, 0.75, 2, 2, 2); }',
                'input' => 'div { transform: matrix(2, -0.1, -.75, 2, -2, 2); }',
                'reversable' => false,
                'skip' => true
            ]],
            [[
                'should' => 'Should mirror transform with calc: matrix',
                'expected' => 'div { transform: matrix( -moz-calc(((25%/2) * 10px)), calc(-1*(((25%/2) * 10px))), 20.75, 2, 2, 2 ); }',
                'input' => 'div { transform: matrix( -moz-calc(((25%/2) * 10px)), calc(((25%/2) * 10px)), -20.75, 2, -2, 2 ); }',
                'reversable' => false,
                'skip' => true
            ]],
            [[
                'should' => 'Should mirror transform : matrix3d',
                'expected' => 'div { transform:matrix3d(0.227114470162179, 0.127248412323519, 0, 0.000811630714323203, 0.113139853456515, 1.53997196559414, 0, 0.000596368270149729, 0, 0, 1, 0, -165, 67, 0, 1); }',
                'input' => 'div { transform:matrix3d(0.227114470162179, -0.127248412323519, 0, -0.000811630714323203, -0.113139853456515, 1.53997196559414, 0, 0.000596368270149729, 0, 0, 1, 0, 165, 67, 0, 1); }',
                'reversable' => true,
                'skip' => true
            ]],
            [[
                'should' => 'Should mirror transform (with no digits before dot): matrix3d',
                'expected' => 'div { transform:matrix3d(0.227114470162179, 0.127248412323519, 0, 0.000811630714323203, 0.113139853456515, 1.53997196559414, 0, 0.000596368270149729, 0, 0, 1, 0, -165, 67, 0, 1); }',
                'input' => 'div { transform:matrix3d(0.227114470162179, -.127248412323519, 0, -0.000811630714323203, -0.113139853456515, 1.53997196559414, 0, 0.000596368270149729, 0, 0, 1, 0, 165, 67, 0, 1); }',
                'reversable' => false,
                'skip' => true
            ]],
            [[
                'should' => 'Should mirror transform with calc : matrix3d',
                'expected' => 'div { transform:matrix3d(0.227114470162179, 0.127248412323519, 0, 0.000811630714323203, 0.113139853456515, 1.53997196559414, 0, 0.000596368270149729, 0, 0, 1, 0, calc(-1*(((25%/2) * 10px))), 67, 0, 1); }',
                'input' => 'div { transform:matrix3d(0.227114470162179, -0.127248412323519, 0, -0.000811630714323203, -0.113139853456515, 1.53997196559414, 0, 0.000596368270149729, 0, 0, 1, 0, calc(((25%/2) * 10px)), 67, 0, 1); }',
                'reversable' => false,
                'skip' => true
            ]],
            [[
                'should' => 'Should mirror transform : translate',
                'expected' => 'div { transform: translate(-10.75px); }',
                'input' => 'div { transform: translate(10.75px); }',
                'reversable' => true,
                'skip' => true
            ]],
            [[
                'should' => 'Should mirror transform (with no digits before dot): translate',
                'expected' => 'div { transform: translate(-0.75px); }',
                'input' => 'div { transform: translate(.75px); }',
                'reversable' => false,
                'skip' => true
            ]],
            [[
                'should' => 'Should mirror transform with calc: translate',
                'expected' => 'div { transform: translate(-moz-calc(-1*(((25%/2) * 10px)))); }',
                'input' => 'div { transform: translate(-moz-calc(((25%/2) * 10px))); }',
                'reversable' => false,
                'skip' => true
            ]],
            [[
                'should' => 'Should mirror transform : translateX',
                'expected' => 'div { transform: translateX(-50.25px); }',
                'input' => 'div { transform: translateX(50.25px); }',
                'reversable' => true,
                'skip' => true
            ]],
            [[
                'should' => 'Should mirror transform (with no digits before dot): translateX',
                'expected' => 'div { transform: translateX(-0.25px); }',
                'input' => 'div { transform: translateX(.25px); }',
                'reversable' => false,
                'skip' => true
            ]],
            [[
                'should' => 'Should mirror transform with calc : translateX',
                'expected' => 'div { transform: translateX(-ms-calc(-1*(((25%/2) * 10px))))); }',
                'input' => 'div { transform: translateX(-ms-calc(((25%/2) * 10px)))); }',
                'reversable' => false,
                'skip' => true
            ]],
            [[
                'should' => 'Should mirror transform : translate3d',
                'expected' => 'div { transform: translate3d(-12.75px, 50%, 3em); }',
                'input' => 'div { transform: translate3d(12.75px, 50%, 3em); }',
                'reversable' => true,
                'skip' => true
            ]],
            [[
                'should' => 'Should mirror transform (with no digits before dot): translate3d',
                'expected' => 'div { transform: translate3d(-0.75px, 50%, 3em); }',
                'input' => 'div { transform: translate3d(.75px, 50%, 3em); }',
                'reversable' => false,
                'skip' => true
            ]],
            [[
                'should' => 'Should mirror transform with calc: translate3d',
                'expected' => 'div { transform: translate3d(-webkit-calc(-1*(((25%/2) * 10px))))), 50%, calc(((25%/2) * 10px))))); }',
                'input' => 'div { transform: translate3d(-webkit-calc(((25%/2) * 10px)))), 50%, calc(((25%/2) * 10px))))); }',
                'reversable' => false,
                'skip' => true
            ]],
            [[
                'should' => 'Should mirror transform : rotate',
                'expected' => 'div { transform: rotate(-20.75deg); }',
                'input' => 'div { transform: rotate(20.75deg); }',
                'reversable' => true,
                'skip' => true
            ]],
            [[
                'should' => 'Should mirror transform (with no digits before dot): rotate',
                'expected' => 'div { transform: rotate(-0.75deg); }',
                'input' => 'div { transform: rotate(.75deg); }',
                'reversable' => false,
                'skip' => true
            ]],
            [[
                'should' => 'Should mirror transform with calc: rotate',
                'expected' => 'div { transform: rotate(calc(-1*(((25%/2) * 10deg)))); }',
                'input' => 'div { transform: rotate(calc(((25%/2) * 10deg))); }',
                'reversable' => false,
                'skip' => true
            ]],
            [[
                'should' => 'Should mirror transform : rotate3d',
                'expected' => 'div { transform: rotate3d(10, -20.15, 10, -45.14deg); }',
                'input' => 'div { transform: rotate3d(10, 20.15, 10, 45.14deg); }',
                'reversable' => true,
                'skip' => true
            ]],
            [[
                'should' => 'Should mirror transform (with no digits before dot): rotate3d',
                'expected' => 'div { transform: rotate3d(10, -20, 10, -0.14deg); }',
                'input' => 'div { transform: rotate3d(10, 20, 10, .14deg); }',
                'reversable' => false,
                'skip' => true
            ]],
            [[
                'should' => 'Should mirror transform with calc: rotate3d',
                'expected' => 'div { transform: rotate3d(10, -20.15, 10, calc(-1*(((25%/2) * 10deg)))); }',
                'input' => 'div { transform: rotate3d(10, 20.15, 10, calc(((25%/2) * 10deg))); }',
                'reversable' => false,
                'skip' => true
            ]],
            [[
                'should' => 'Should not mirror transform : rotateX',
                'expected' => 'div { transform: rotateX(45deg); }',
                'input' => 'div { transform: rotateX(45deg); }',
                'reversable' => false,
                'skip' => true
            ]],
            [[
                'should' => 'Should not mirror transform with calc: rotateX',
                'expected' => 'div { transform: rotateX(calc(((25%/2) * 10deg))); }',
                'input' => 'div { transform: rotateX(calc(((25%/2) * 10deg))); }',
                'reversable' => false,
                'skip' => true
            ]],
            [[
                'should' => 'Should not mirror transform : rotateY',
                'expected' => 'div { transform: rotateY(45deg); }',
                'input' => 'div { transform: rotateY(45deg); }',
                'reversable' => false,
                'skip' => true
            ]],
            [[
                'should' => 'Should not mirror transform with calc: rotateY',
                'expected' => 'div { transform: rotateY(calc(((25%/2) * 10deg))); }',
                'input' => 'div { transform: rotateY(calc(((25%/2) * 10deg))); }',
                'reversable' => false,
                'skip' => true
            ]],
            [[
                'should' => 'Should mirror transform : rotateZ',
                'expected' => 'div { transform: rotateZ(-45.75deg); }',
                'input' => 'div { transform: rotateZ(45.75deg); }',
                'reversable' => true,
                'skip' => true
            ]],
            [[
                'should' => 'Should mirror transform (with no digits before dot): rotateZ',
                'expected' => 'div { transform: rotateZ(-0.75deg); }',
                'input' => 'div { transform: rotateZ(.75deg); }',
                'reversable' => false,
                'skip' => true
            ]],
            [[
                'should' => 'Should mirror transform with calc: rotateZ',
                'expected' => 'div { transform: rotateZ(-ms-calc(-1*(((25%/2) * 10deg)))); }',
                'input' => 'div { transform: rotateZ(-ms-calc(((25%/2) * 10deg))); }',
                'reversable' => false,
                'skip' => true
            ]],
            [[
                'should' => 'Should mirror transform : skew',
                'expected' => 'div { transform: skew(-20.25rad,-30deg); }',
                'input' => 'div { transform: skew(20.25rad,30deg); }',
                'reversable' => true,
                'skip' => true
            ]],
            [[
                'should' => 'Should mirror transform (with no digits before dot): skew',
                'expected' => 'div { transform: skew(-0.25rad,-30deg); }',
                'input' => 'div { transform: skew(.25rad,30deg); }',
                'reversable' => false,
                'skip' => true
            ]],
            [[
                'should' => 'Should mirror transform with calc: skew',
                'expected' => 'div { transform: skew(calc(-1*(((25%/2) * 10rad))),calc(-1*(((25%/2) * 10deg)))); }',
                'input' => 'div { transform: skew(calc(((25%/2) * 10rad)),calc(((25%/2) * 10deg))); }',
                'reversable' => false,
                'skip' => true
            ]],
            [[
                'should' => 'Should mirror transform : skewX',
                'expected' => 'div { transform: skewX(-20.75rad); }',
                'input' => 'div { transform: skewX(20.75rad); }',
                'reversable' => true,
                'skip' => true
            ]],
            [[
                'should' => 'Should mirror transform (with no digits before dot): skewX',
                'expected' => 'div { transform: skewX(-0.75rad); }',
                'input' => 'div { transform: skewX(.75rad); }',
                'reversable' => false,
                'skip' => true
            ]],
            [[
                'should' => 'Should mirror transform with calc: skewX',
                'expected' => 'div { transform: skewX(-moz-calc(-1*(((25%/2) * 10rad)))); }',
                'input' => 'div { transform: skewX(-moz-calc(((25%/2) * 10rad))); }',
                'reversable' => false,
                'skip' => true
            ]],
            [[
                'should' => 'Should mirror transform : skewY',
                'expected' => 'div { transform: skewY(-10.75grad); }',
                'input' => 'div { transform: skewY(10.75grad); }',
                'reversable' => true,
                'skip' => true
            ]],
            [[
                'should' => 'Should mirror transform (with no digits before dot): skewY',
                'expected' => 'div { transform: skewY(-0.75grad); }',
                'input' => 'div { transform: skewY(.75grad); }',
                'reversable' => false,
                'skip' => true
            ]],
            [[
                'should' => 'Should mirror transform with calc: skewY',
                'expected' => 'div { transform: skewY(calc(-1*(((25%/2) * 10grad)))); }',
                'input' => 'div { transform: skewY(calc(((25%/2) * 10grad))); }',
                'reversable' => false,
                'skip' => true
            ]],
            [[
                'should' => 'Should mirror multiple transforms : translateX translateY Rotate',
                'expected' => 'div { transform: translateX(-50.25px) translateY(50.25px) rotate(-20.75deg); }',
                'input' => 'div { transform: translateX(50.25px) translateY(50.25px) rotate(20.75deg); }',
                'reversable' => true,
                'skip' => true
            ]],
            [[
                'should' => 'Should mirror multiple transforms with calc : translateX translateY Rotate',
                'expected' => 'div { transform: translateX(-ms-calc(-1*(((25%/2) * 10px)))) translateY(-moz-calc(((25%/2) * 10rad))) rotate(calc(-1*(((25%/2) * 10grad)))); }',
                'input' => 'div { transform: translateX(-ms-calc(((25%/2) * 10px))) translateY(-moz-calc(((25%/2) * 10rad))) rotate(calc(((25%/2) * 10grad))); }',
                'reversable' => false,
                'skip' => true
            ]]
            */
        ];
    }

    /**
     * Data provider.
     * @return array
     */
    public function values_nsyntax_provider() {
        return [
            [[
                'should' => 'Should mirror property value: border-radius (4 values)',
                'expected' => 'div { border-radius: 40.25px 10.5px 10.75px 40.3px; }',
                'input' => 'div { border-radius: 10.5px 40.25px 40.3px 10.75px; }',
                'reversable' => true
            ]],
            [[
                'should' => 'Should mirror property value: border-radius (3 values)',
                'expected' => 'div { border-radius: 40.75px 10.75px 40.75px 40.3px; }',
                'input' => 'div { border-radius: 10.75px 40.75px 40.3px; }',
                'reversable' => false
            ]],
            [[
                'should' => 'Should mirror property value: border-radius (2 values)',
                'expected' => 'div { border-radius: 40.25px 10.75px; }',
                'input' => 'div { border-radius: 10.75px 40.25px; }',
                'reversable' => true
            ]],
            /* Not supported by MoodleHQ/RTLCSS yet.
            [[
                'should' => 'Should mirror property value: border-radius (4 values - double)',
                'expected' => 'div { border-radius: 40.25px 10.75px .5px 40.75px / .4em 1em 1em 4.5em; }',
                'input' => 'div { border-radius: 10.75px 40.25px 40.75px .5px / 1em .4em 4.5em 1em; }',
                'reversable' => true,
                'skip' => true
            ]],
            [[
                'should' => 'Should mirror property value: border-radius (3 values - double)',
                'expected' => 'div { border-radius: .40px 10.5px .40px 40px / 4em 1em 4em 3em; }',
                'input' => 'div { border-radius: 10.5px .40px 40px / 1em 4em 3em; }',
                'reversable' => false,
                'skip' => true
            ]],
            [[
                'should' => 'Should mirror property value: border-radius (2 values- double)',
                'expected' => 'div { border-radius: 40px 10px / 2.5em .75em; }',
                'input' => 'div { border-radius: 10px 40px / .75em 2.5em; }',
                'reversable' => true,
                'skip' => true
            ]],
            */
            [[
                'should' => 'Should mirror property value: border-width',
                'expected' => 'div { border-width: 1px 4px .3em 2.5em; }',
                'input' => 'div { border-width: 1px 2.5em .3em 4px; }',
                'reversable' => true
            ]],
            [[
                'should' => 'Should mirror property value: border-width (none length)',
                'expected' => 'div { border-width: thin medium thick none; }',
                'input' => 'div { border-width: thin none thick medium; }',
                'reversable' => true
            ]],
            [[
                'should' => 'Should mirror property value: border-style (4 values)',
                'expected' => 'div { border-style: none dashed dotted solid; }',
                'input' => 'div { border-style: none solid dotted dashed; }',
                'reversable' => true
            ]],
            [[
                'should' => 'Should mirror property value: border-color (4 values)',
                'expected' => 'div { border-color: rgba(255, 255, 255, 1) rgb(0, 0, 0) rgb(0, 0, 0) hsla(0, 100%, 50%, 1); }',
                'input' => 'div { border-color: rgba(255, 255, 255, 1) hsla(0, 100%, 50%, 1) rgb(0, 0, 0) rgb(0, 0, 0); }',
                'reversable' => true
            ]],
            [[
                'should' => 'Should not mirror property value: border-color (3 values)',
                'expected' => 'div { border-color: rgb(0, 0, 0) rgb(0, 0, 0) hsla(0, 100%, 50%, 1); }',
                'input' => 'div { border-color: #000 rgb(0, 0, 0) hsla(0, 100%, 50%, 1); }',
                'reversable' => false
            ]],
            [[
                'should' => 'Should not mirror property value: border-color (2 values)',
                'expected' => 'div { border-color: rgb(0, 0, 0) hsla(0, 100%, 50%, 1); }',
                'input' => 'div { border-color: rgb(0, 0, 0) hsla(0, 100%, 50%, 1); }',
                'reversable' => false
            ]],
            [[
                'should' => 'Should mirror property value: margin',
                'expected' => 'div { margin: .1em auto 3.5rem 2px; }',
                'input' => 'div { margin: .1em 2px 3.5rem auto; }',
                'reversable' => true
            ]],
            [[
                'should' => 'Should mirror property value: padding',
                'expected' => 'div { padding: 1px 4px .3rem 2.5em; }',
                'input' => 'div { padding: 1px 2.5em .3rem 4px; }',
                'reversable' => true
            ]],
            /* Not supported by MoodleHQ/RTLCSS yet.
            [[
                'should' => 'Should mirror property value: box-shadow',
                'expected' => 'div { box-shadow: -60px -16px rgba(0, 128, 128, 0.98), -10.25px 5px 5px #ff0, inset -0.5em 1em 0 white; }',
                'input' => 'div { box-shadow: 60px -16px rgba(0, 128, 128, 0.98), 10.25px 5px 5px #ff0, inset 0.5em 1em 0 white; }',
                'reversable' => true,
                'skip' => true
            ]],
            [[
                'should' => 'Should mirror property value: text-shadow',
                'expected' => 'div { text-shadow: -60px -16px rgba(0, 128, 128, 0.98), -10.25px 5px 5px #ff0, inset -0.5em 1em 0 white; }',
                'input' => 'div { text-shadow: 60px -16px rgba(0, 128, 128, 0.98), 10.25px 5px 5px #ff0, inset 0.5em 1em 0 white; }',
                'reversable' => true,
                'skip' => true
            ]],
            [[
                'should' => 'Should mirror property value (no digit before the dot): box-shadow, text-shadow',
                'expected' => 'div { box-shadow: inset -0.5em 1em 0 white; text-shadow: inset -0.5em 1em 0 white; }',
                'input' => 'div { box-shadow: inset .5em 1em 0 white; text-shadow: inset .5em 1em 0 white; }',
                'reversable' => false,
                'skip' => true
            ]]
            */
        ];
    }

    /**
     * Data provider.
     * @return array
     */
    public function values_provider() {
        return [
            [[
                'should' => 'Should mirror property value: clear',
                'expected' => 'div { clear:right; }',
                'input' => 'div { clear:left; }',
                'reversable' => true
            ]],
            [[
                'should' => 'Should mirror property value: direction',
                'expected' => 'div { direction:ltr; }',
                'input' => 'div { direction:rtl; }',
                'reversable' => true
            ]],
            [[
                'should' => 'Should mirror property value: float',
                'expected' => 'div { float:right; }',
                'input' => 'div { float:left; }',
                'reversable' => true
            ]],
            [[
                'should' => 'Should mirror property value: text-align',
                'expected' => 'div { text-align:right; }',
                'input' => 'div { text-align:left; }',
                'reversable' => true
            ]],
            [[
                'should' => 'Should mirror property value: cursor nw',
                'expected' => 'div { cursor:nw-resize; }',
                'input' => 'div { cursor:ne-resize; }',
                'reversable' => true
            ]],
            [[
                'should' => 'Should mirror property value: cursor sw',
                'expected' => 'div { cursor:sw-resize; }',
                'input' => 'div { cursor:se-resize; }',
                'reversable' => true
            ]],
            [[
                'should' => 'Should mirror property value: cursor nesw',
                'expected' => 'div { cursor:nesw-resize; }',
                'input' => 'div { cursor:nwse-resize; }',
                'reversable' => true
            ]],
            [[
                'should' => 'Should keep property value as is: cursor ew',
                'expected' => 'div { cursor:ew-resize; }',
                'input' => 'div { cursor:ew-resize; }',
                'reversable' => false
            ]],
            /* Not supported by MoodleHQ/RTLCSS yet.
            [[
                'should' => 'Should process string map in url: cursor (processUrls: true)',
                'expected' => '.foo { cursor: url(right.cur), url(rtl.cur), se-resize, auto }',
                'input' => '.foo { cursor: url(left.cur), url(ltr.cur), sw-resize, auto }',
                'reversable' => true,
                'options' => [ 'processUrls' => true ],
                'skip' => true
            ]],
            */
            [[
                'should' => 'Should mirror property value: transition',
                'expected' => '.foo { transition:right .3s ease .1s,left .3s ease .1s,margin-right .3s ease,margin-left .3s ease,padding-right .3s ease,padding-left .3s ease; }',
                'input' => '.foo { transition:left .3s ease .1s,right .3s ease .1s,margin-left .3s ease,margin-right .3s ease,padding-left .3s ease,padding-right .3s ease; }',
                'reversable' => true
            ]],
            [[
                'should' => 'Should mirror property value: transition-property',
                'expected' => '.foo { transition-property:right; }',
                'input' => '.foo { transition-property:left; }',
                'reversable' => true
            ]]
        ];
    }

    /**
     * Assert that the provided data flips.
     *
     * @param string $expected The expected output.
     * @param string $input The input.
     * @param string $description The description of the assertion.
     * @param OutputFormat $output The output format to use.
     */
    protected function assert_flips($expected, $input, $description, $output = null) {
        $parser = new Parser($input);
        $tree = $parser->parse();
        $rtlcss = new core_rtlcss($tree);
        $flipped = $rtlcss->flip();
        $this->assertEquals($expected, $flipped->render($output), $description);
    }

    /**
     * Assert data.
     *
     * @param array $data With the keys: 'input', 'expected', 'reversable', 'should', and 'skip'.
     * @param OutputFormat $output The output format to use.
     */
    protected function assert_sample($data, $output = null) {
        if (!empty($data['skip'])) {
            $this->markTestSkipped('Not yet supported!');
        }
        $this->assert_flips($data['expected'], $data['input'], $data['should'], $output);
        if (!empty($data['reversable'])) {
            $this->assert_flips($data['input'], $data['expected'], $data['should'] . ' (reversed)', $output);
        }
    }

    /**
     * Test background images.
     * @param array $data the provider data.
     * @dataProvider background_image_provider
     */
    /* Not supported by MoodleHQ/RTLCSS yet.
    public function test_background_image($data) {
        $output = new OutputFormat();
        $this->assert_sample($data, $output);
    }
    */

    /**
     * Test background position.
     * @param array $data the provider data.
     * @dataProvider background_position_provider
     */
    public function test_background_position($data) {
        $output = new OutputFormat();
        $output->set('SpaceAfterRuleName', '');
        $output->set('SpaceAfterListArgumentSeparator', array('default' => '', ',' => ' '));
        $this->assert_sample($data, $output);
    }

    /**
     * Test background.
     * @param array $data the provider data.
     * @dataProvider background_provider
     */
    public function test_background($data) {
        $output = new OutputFormat();
        $output->set('SpaceAfterRuleName', ' ');
        $output->set('SpaceBeforeRules', ' ');
        $output->set('SpaceAfterRules', ' ');
        $output->set('SpaceAfterListArgumentSeparator', array('default' => '', ',' => ' '));
        $this->assert_sample($data, $output);
    }

    /**
     * Test directives.
     * @param array $data the provider data.
     * @dataProvider directives_provider
     */
    public function test_directives($data) {
        $output = new OutputFormat();
        $output->set('SpaceAfterRuleName', '');
        $output->set('SpaceBeforeRules', '');
        $output->set('SpaceAfterRules', '');
        $output->set('SpaceBetweenRules', '');
        $output->set('SpaceBetweenBlocks', ' ');
        $output->set('SpaceAfterListArgumentSeparator', array('default' => '', ',' => ' '));
        $this->assert_sample($data, $output);
    }

    /**
     * Test properties.
     * @param array $data the provider data.
     * @dataProvider properties_provider
     */
    public function test_properties($data) {
        $output = new OutputFormat();
        $output->set('SpaceAfterRuleName', '');
        $output->set('SpaceBeforeRules', ' ');
        $output->set('SpaceAfterRules', ' ');
        $output->set('SpaceAfterListArgumentSeparator', array('default' => '', ',' => ' '));
        $this->assert_sample($data, $output);
    }

    /**
     * Test special.
     * @param array $data the provider data.
     * @dataProvider special_provider
     */
    /* Not supported by MoodleHQ/RTLCSS yet.
    public function test_special($data) {
        $output = new OutputFormat();
        $output->set('SpaceBeforeRules', ' ');
        $output->set('SpaceAfterListArgumentSeparator', array('default' => '', ',' => ' '));
        $this->assert_sample($data, $output);
    }
    */

    /**
     * Test transform original.
     * @param array $data the provider data.
     * @dataProvider transform_origin_provider
     */
    public function test_transform_origin($data) {
        $output = new OutputFormat();
        $output->set('SpaceAfterRuleName', '');
        $output->set('SpaceBeforeRules', ' ');
        $output->set('SpaceAfterRules', ' ');
        $output->set('SpaceAfterListArgumentSeparator', array('default' => '', ',' => ' '));
        $this->assert_sample($data, $output);
    }


    /**
     * Test transform.
     * @param array $data the provider data.
     * @dataProvider transforms_provider
     */
    /* Not supported by MoodleHQ/RTLCSS yet.
    public function test_transforms($data) {
        $output = new OutputFormat();
        $output->set('SpaceBeforeRules', ' ');
        $output->set('SpaceAfterRules', ' ');
        $output->set('SpaceAfterListArgumentSeparator', array('default' => '', ',' => ' '));
        $this->assert_sample($data, $output);
    }
    */

    /**
     * Test values n-syntax.
     * @param array $data the provider data.
     * @dataProvider values_nsyntax_provider
     */
    public function test_values_nsyntax($data) {
        $output = new OutputFormat();
        $output->set('SpaceBeforeRules', ' ');
        $output->set('SpaceAfterRules', ' ');
        $output->set('RGBHashNotation', false);
        $output->set('SpaceAfterListArgumentSeparator', array('default' => '', ',' => ' '));
        $this->assert_sample($data, $output);
    }

    /**
     * Test values.
     * @param array $data the provider data.
     * @dataProvider values_provider
     */
    public function test_values($data) {
        $output = new OutputFormat();
        $output->set('SpaceAfterRuleName', '');
        $output->set('SpaceBeforeRules', ' ');
        $output->set('SpaceAfterRules', ' ');
        $output->set('SpaceAfterListArgumentSeparator', array('default' => '', ',' => ' '));
        $this->assert_sample($data, $output);
    }
}
