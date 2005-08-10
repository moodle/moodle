<?PHP
/////////////////////////////////////////////////////////////////////////////
//                                                                         //
// NOTICE OF COPYRIGHT                                                     //
//                                                                         //
// Moodle - Filter for converting TeX expressions to cached gif images     //
//                                                                         //
// Copyright (C) 2004 Zbigniew Fiedorowicz fiedorow@math.ohio-state.edu    //
// Originally based on code provided by Bruno Vernier bruno@vsbeducation.ca//
// This program is free software; you can redistribute it and/or modify    //
// it under the terms of the GNU General Public License as published by    //
// the Free Software Foundation; either version 2 of the License, or       //
// (at your option) any later version.                                     //
//                                                                         //
// This program is distributed in the hope that it will be useful,         //
// but WITHOUT ANY WARRANTY; without even the implied warranty of          //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the           //
// GNU General Public License for more details:                            //
//                                                                         //
//          http://www.gnu.org/copyleft/gpl.html                           //
//                                                                         //
/////////////////////////////////////////////////////////////////////////////
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

$CFG->texfilterdir = "filter/tex";


function string_file_picture_tex($imagefile, $tex= "", $height="", $width="", $align="middle") {
    // Given the path to a picture file in a course, or a URL,
    // this function includes the picture in the page.
    global $CFG;

    $output = "";
    if ($tex) {
        $tex = str_replace('&','&amp;',$tex);
        $tex = str_replace('<','&lt;',$tex);
        $tex = str_replace('>','&gt;',$tex);
        $tex = str_replace('"','&quot;',$tex);
        $tex = str_replace("\'",'&#39;',$tex);
        $title = "title=\"$tex\"";
    }
    if ($height) {
        $height = "height=\"$height\"";
    }
    if ($width) {
        $width = "width=\"$width\"";
    }
    if ($imagefile) {
        if (!file_exists("$CFG->dataroot/$CFG->texfilterdir/$imagefile") && isadmin()) {
          $output .= "<a href=\"$CFG->wwwroot/$CFG->texfilterdir/texdebug.php\">";
        } else {
          $output .= "<a target=\"popup\" title=\"TeX\" href=";
          $output .= "\"$CFG->wwwroot/$CFG->texfilterdir/displaytex.php?";
          $output .= urlencode($tex) . "\" onclick=\"return openpopup('/$CFG->texfilterdir/displaytex.php?";
          $output .= urlencode($tex) . "', 'popup', 'menubar=0,location=0,scrollbars,";
          $output .= "resizable,width=300,height=240', 0);\">";
        }
        $output .= "<img class=\"texrender\" border=\"0\" $title $height $width alt=\"\" src=\"";
        if ($CFG->slasharguments) {        // Use this method if possible for better caching
            $output .= "$CFG->wwwroot/$CFG->texfilterdir/pix.php/$imagefile";
        } else {
            $output .= "$CFG->wwwroot/$CFG->texfilterdir/pix.php?file=$imagefile";
        }
        $output .= "\" style=\"vertical-align:$align\" />";
        $output .= "</a>";
    } else {
        $output .= "Error: must pass URL or course";
    }
    return $output;
}

function tex_filter ($courseid, $text) {

    global $CFG;

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
#        $discussion = get_record("forum_discussions","id",$parent->discussion);
#    } else if (strstr($scriptname,'discuss.php')) {
#        $discussion = get_record("forum_discussions","id",$_GET['d'] );
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

    //if (isadmin()) { error_reporting (E_ALL); }; //for debugging

    // <tex> TeX expression </tex>
    // or $$ TeX expression $$
    // or \[ TeX expression \]          // original tag of MathType and TeXaide (dlnsk)
    // or [tex] TeX expression [/tex]   // somtime it's more comfortable than <tex> (dlnsk)
    preg_match_all('/<tex>(.+?)<\/tex>|\$\$(.+?)\$\$|\\\\\[(.+?)\\\\\]|\\[tex\\](.+?)\\[\/tex\\]/is', $text, $matches);
    for ($i=0; $i<count($matches[0]); $i++) {
        $texexp = $matches[1][$i] . $matches[2][$i] . $matches[3][$i] . $matches[4][$i];
        $texexp = str_replace('<nolink>','',$texexp);
        $texexp = str_replace('</nolink>','',$texexp);
        $texexp = str_replace('<span class="nolink">','',$texexp);
        $texexp = str_replace('</span>','',$texexp);
        $texexp = eregi_replace("<br[[:space:]]*\/?>", '', $texexp);  //dlnsk
        $align = "middle";
        if (preg_match('/^align=bottom /',$texexp)) {
          $align = "text-bottom";
          $texexp = preg_replace('/^align=bottom /','',$texexp);
        } else if (preg_match('/^align=top /',$texexp)) {
          $align = "text-top";
          $texexp = preg_replace('/^align=top /','',$texexp);
        }
        $md5 = md5($texexp);
        if (! $texcache = get_record("cache_filters","filter","tex", "md5key", $md5)) {
            $texcache->filter = 'tex';
            $texcache->version = 1;
            $texcache->md5key = $md5;
            $texcache->rawtext = addslashes($texexp);
            $texcache->timemodified = time();
            insert_record("cache_filters",$texcache, false);
        }
        $filename = $md5 . ".gif";
        $text = str_replace( $matches[0][$i], string_file_picture_tex($filename, $texexp, '', '', $align), $text);
    }
    return $text; 
}


?>
