<?php // $Id$

/******************************************************************************

 Plug in constants/variables    - See MDL-6798 for details

 Information from Urs Hunkler:


 More flexible themes with CSS constants: An option for Moodle retro themes and easy colour palette variants.
 
 I adopted Shaun Inman's "CSS Server-side Constants" to Moodle: http://www.shauninman.com/post/heap/2005/08/09/css_constants
 
 With setting "cssconstants" to true in "config.php" you activate the CSS constants. If "cssconstants" is missing or set to "false" the
 replacement function is not used.
 
 $THEME->cssconstants = true;
 /// By setting this to true, you will be able to use CSS constants
 
 
 The constant definitions are written into a separate CSS file named like "constants.css" and loaded first in config.php. You can use constants for any CSS properties. The constant definition looks like:
 
@server constants {
  fontColor: #3a2830;
  aLink: #116699;
  aVisited: #AA2200;
  aHover: #779911;
  pageBackground: #FFFFFF;
  backgroundColor: #EEEEEE;
  backgroundSideblockHeader: #a8a4e9;
  fontcolorSideblockHeader: #222222;
  color1: #98818b;
  color2: #bd807b;
  color3: #f9d1d7;
  color4: #e8d4d8;
}



The lines in the CSS files using CSS constants look like:

body {
  font-size: 100%;
  background-color: pageBackground;
  color: fontColor;
  font-family: 'Bitstream Vera Serif', georgia, times, serif;
  margin: 0;
  padding: 0;
}
div#page {
  margin: 0 10px;
  padding-top: 5px;
  border-top-width: 10px;
  border-top-style: solid;
  border-top-color: color3;
}
div.clearer {
  clear: both;
}
a:link {
  color: aLink;
} 
 
******************************************************************************/

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

// vim:autoindent:expandtab:shiftwidth=4:tabstop=4:tw=140:
?>
