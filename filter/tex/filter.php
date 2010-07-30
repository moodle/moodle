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
 * Moodle - Filter for converting TeX expressions to cached gif images
 * @package    filter
 * @subpackage tex
 * @copyright  2004 Zbigniew Fiedorowicz fiedorow@math.ohio-state.edu
 *             Originally based on code provided by Bruno Vernier bruno@vsbeducation.ca
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

//-------------------------------------------------------------------------
// NOTE: This Moodle text filter converts TeX expressions delimited
// by either $$...$$ or by <tex...>...</tex> tags to gif images using
// mimetex.cgi obtained from http://www.forkosh.com/mimetex.html authored by
// John Forkosh john@forkosh.com.  Several binaries of this areincluded with
// this distribution.
// Note that there may be patent restrictions on the production of gif images
// in Canada and some parts of Western Europe and Japan until July 2004.
//-------------------------------------------------------------------------
/////////////////////////////////////////////////////////////////////////////
//  To activate this filter, add a line like this to your                  //
//  list of filters in your Filter configuration:                          //
//                                                                         //
//       filter/tex/filter.php                                             //
/////////////////////////////////////////////////////////////////////////////

function filter_text_image($imagefile, $tex= "", $height="", $width="", $align="middle", $alt='') {
    global $CFG, $OUTPUT;

    if ($alt==='') {
        $alt = s($tex);
    }

    // Work out any necessary inline style.
    $rules = array();
    if ($align !== 'middle') {
        $rules[] = 'vertical-align:' . $align . ';';
    }
    if ($height) {
        $rules[] = 'height:' . $height . 'px;';
    }
    if ($width) {
        $rules[] = 'width:' . $width . 'px;';
    }
    if (!empty($rules)) {
        $style = ' style="' . implode('', $rules) . '" ';
    } else {
        $style = '';
    }

    // Prepare the title attribute.
    if ($tex) {
        $tex = str_replace('&','&amp;',$tex);
        $tex = str_replace('<','&lt;',$tex);
        $tex = str_replace('>','&gt;',$tex);
        $tex = str_replace('"','&quot;',$tex);
        $tex = str_replace("\'",'&#39;',$tex);
        // Note that we retain the title tag as TeX format rather than using
        // the alt text, even if supplied. The alt text is intended for blind
        // users (to provide a text equivalent to the equation) while the title
        // is there as a convenience for sighted users who want to see the TeX
        // code.
        $title = "title=\"$tex\"";
    }

    // Build the output.
    $output = "";
    if ($imagefile) {
        $anchorcontents = "<img class=\"texrender\" $title alt=\"$alt\" src=\"";
        if ($CFG->slasharguments) {        // Use this method if possible for better caching
            $anchorcontents .= "$CFG->wwwroot/filter/tex/pix.php/$imagefile";
        } else {
            $anchorcontents .= "$CFG->wwwroot/filter/tex/pix.php?file=$imagefile";
        }
        $anchorcontents .= "\" $style/>";

        $link = $action = null;
        if (!file_exists("$CFG->dataroot/filter/tex/$imagefile") && has_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM))) {
            $link = '/filter/tex/texdebug.php';
        } else {
            $link = '/filter/tex/displaytex.php?'.urlencode($tex);
            $action = new popup_action('click', $link, 'popup', array('height'=>300,'width'=>240));
        }
        $output .= $OUTPUT->action_link($link, $anchorcontents, $action, array('title'=>'TeX'));
    } else {
        $output .= "Error: must pass URL or course";
    }
    return $output;
}

class filter_tex extends moodle_text_filter {
    function filter ($text) {

        global $CFG, $DB;

        /// Do a quick check using stripos to avoid unnecessary work
        if (!preg_match('/<tex/i',$text) and !strstr($text,'$$') and !strstr($text,'\\[') and !preg_match('/\[tex/i',$text)) { //added one more tag (dlnsk)
            return $text;
        }

#    //restrict filtering to forum 130 (Maths Tools on moodle.org)
#    $scriptname = $_SERVER['SCRIPT_NAME'];
#    if (!strstr($scriptname,'/forum/')) {
#        return $text;
#    }
#    if (strstr($scriptname,'post.php')) {
#        $parent = forum_get_post_full($_GET['reply']);
#        $discussion = $DB->get_record("forum_discussions", array("id"=>$parent->discussion));
#    } else if (strstr($scriptname,'discuss.php')) {
#        $discussion = $DB->get_record("forum_discussions", array("id"=>$_GET['d']));
#    } else {
#        return $text;
#    }
#    if ($discussion->forum != 130) {
#        return $text;
#    }
        $text .= ' ';
        preg_match_all('/\$(\$\$+?)([^\$])/s',$text,$matches);
        for ($i=0;$i<count($matches[0]);$i++) {
            $replacement = str_replace('$','&#x00024;',$matches[1][$i]).$matches[2][$i];
            $text = str_replace($matches[0][$i],$replacement,$text);
        }

        // <tex> TeX expression </tex>
        // or <tex alt="My alternative text to be used instead of the TeX form"> TeX expression </tex>
        // or $$ TeX expression $$
        // or \[ TeX expression \]          // original tag of MathType and TeXaide (dlnsk)
        // or [tex] TeX expression [/tex]   // somtime it's more comfortable than <tex> (dlnsk)
        preg_match_all('/<tex(?:\s+alt=["\'](.*?)["\'])?>(.+?)<\/tex>|\$\$(.+?)\$\$|\\\\\[(.+?)\\\\\]|\\[tex\\](.+?)\\[\/tex\\]/is', $text, $matches);
        for ($i=0; $i<count($matches[0]); $i++) {
            $texexp = $matches[2][$i] . $matches[3][$i] . $matches[4][$i] . $matches[5][$i];
            $alt = $matches[1][$i];
            $texexp = str_replace('<nolink>','',$texexp);
            $texexp = str_replace('</nolink>','',$texexp);
            $texexp = str_replace('<span class="nolink">','',$texexp);
            $texexp = str_replace('</span>','',$texexp);
            $texexp = preg_replace("/<br[[:space:]]*\/?>/i", '', $texexp);  //dlnsk
            $align = "middle";
            if (preg_match('/^align=bottom /',$texexp)) {
              $align = "text-bottom";
              $texexp = preg_replace('/^align=bottom /','',$texexp);
            } else if (preg_match('/^align=top /',$texexp)) {
              $align = "text-top";
              $texexp = preg_replace('/^align=top /','',$texexp);
            }
            $md5 = md5($texexp);
            if (! $texcache = $DB->get_record("cache_filters", array("filter"=>"tex", "md5key"=>$md5))) {
                $texcache->filter = 'tex';
                $texcache->version = 1;
                $texcache->md5key = $md5;
                $texcache->rawtext = $texexp;
                $texcache->timemodified = time();
                $DB->insert_record("cache_filters", $texcache, false);
            }
            $filename = $md5 . ".{$CFG->filter_tex_convertformat}";
            $text = str_replace( $matches[0][$i], filter_text_image($filename, $texexp, '', '', $align, $alt), $text);
        }
        return $text;
    }
}


