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
 * Plug in constants/variables    - See MDL-6798 for details
 * 
 * Information from Urs Hunkler:
 * 
 * 
 * More flexible themes with CSS constants: An option for Moodle retro themes and easy colour palette variants.
 * 
 * I adopted Shaun Inman's "CSS Server-side Constants" to Moodle: http://www.shauninman.com/post/heap/2005/08/09/css_constants
 *  
 * With setting "cssconstants" to true in "config.php" you activate the CSS constants. If "cssconstants" is missing or set to "false" the
 * replacement function is not used.
 *  
 * $THEME->cssconstants = true;
 * By setting this to true, you will be able to use CSS constants
 *   
 * The constant definitions are written into a separate CSS file named like "constants.css" and loaded first in config.php. You can use constants for any CSS properties. The constant definition looks like:
 * <code>
 * \@server constants {
 *   fontColor: #3a2830;
 *   aLink: #116699;
 *   aVisited: #AA2200;
 *   aHover: #779911;
 *   pageBackground: #FFFFFF;
 *   backgroundColor: #EEEEEE;
 *   backgroundSideblockHeader: #a8a4e9;
 *   fontcolorSideblockHeader: #222222;
 *   color1: #98818b;
 *   color2: #bd807b;
 *   color3: #f9d1d7;
 *   color4: #e8d4d8;
 * }
 * </code>
 * 
 * The lines in the CSS files using CSS constants look like:
 * <code>
 * body {
 *   font-size: 100%;
 *   background-color: pageBackground;
 *   color: fontColor;
 *   font-family: 'Bitstream Vera Serif', georgia, times, serif;
 *   margin: 0;
 *   padding: 0;
 * }
 * div#page {
 *   margin: 0 10px;
 *   padding-top: 5px;
 *   border-top-width: 10px;
 *   border-top-style: solid;
 *   border-top-color: color3;
 * }
 * div.clearer {
 *   clear: both;
 * }
 * a:link {
 *   color: aLink;
 * } 
 * </code>
 *
 * @package   moodlecore
 * @copyright 1999 onwards Martin Dougiamas  http://dougiamas.com
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 /** 
  * Replaces CSS Constants within CSS string
  *
  * @param string $css
  * @return string
  */
function replace_cssconstants($css) {
    if (preg_match_all("/@server\s+(?:variables|constants)\s*\{\s*([^\}]+)\s*\}\s*/i",$css,$matches)) {
        $variables  = array();
        foreach ($matches[0] as $key=>$server) {
            $css = str_replace($server,'',$css);
            preg_match_all("/([^:\}\s]+)\s*:\s*([^;\}]+);/",$matches[1][$key],$vars);
            foreach ($vars[1] as $var=>$value) {
                $variables[$value] = $vars[2][$var];
                }
            }
        $css = str_replace(array_keys($variables),array_values($variables),$css);
        }
    return ($css);
}

?>
