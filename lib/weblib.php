<?PHP // $Id$

///////////////////////////////////////////////////////////////////////////
// weblib.php - functions for web output
//
// Library of all general-purpose Moodle PHP functions and constants
// that produce HTML output
//
///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.com                                            //
//                                                                       //
// Copyright (C) 2001-2003  Martin Dougiamas  http://dougiamas.com       //
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

/// Constants

/// Define text formatting types ... eventually we can add Wiki, BBcode etc
define("FORMAT_MOODLE", "0");
define("FORMAT_HTML", "1");

$SMILEY_TEXT[]  = ":-)";
$SMILEY_IMAGE[] = "<IMG ALT=\":-)\" WIDTH=15 HEIGHT=15 SRC=\"$CFG->wwwroot/pix/s/smiley.gif\">";
$SMILEY_TEXT[]  = ":)";
$SMILEY_IMAGE[] = "<IMG ALT=\":-)\" WIDTH=15 HEIGHT=15 SRC=\"$CFG->wwwroot/pix/s/smiley.gif\">";
$SMILEY_TEXT[]  = ":-D";
$SMILEY_IMAGE[] = "<IMG ALT=\":-D\" WIDTH=15 HEIGHT=15 SRC=\"$CFG->wwwroot/pix/s/biggrin.gif\">";
$SMILEY_TEXT[]  = ";-)";
$SMILEY_IMAGE[] = "<IMG ALT=\";-)\" WIDTH=15 HEIGHT=15 SRC=\"$CFG->wwwroot/pix/s/wink.gif\">";
$SMILEY_TEXT[]  = ":-/";
$SMILEY_IMAGE[] = "<IMG ALT=\":-/\" WIDTH=15 HEIGHT=15 SRC=\"$CFG->wwwroot/pix/s/mixed.gif\">";
$SMILEY_TEXT[]  = "V-.";
$SMILEY_IMAGE[] = "<IMG ALT=\"V-.\" WIDTH=15 HEIGHT=15 SRC=\"$CFG->wwwroot/pix/s/thoughtful.gif\">";
$SMILEY_TEXT[]  = ":-P";
$SMILEY_IMAGE[] = "<IMG ALT=\":-P\" WIDTH=15 HEIGHT=15 SRC=\"$CFG->wwwroot/pix/s/tongueout.gif\">";
$SMILEY_TEXT[]  = "B-)";
$SMILEY_IMAGE[] = "<IMG ALT=\"B-)\" WIDTH=15 HEIGHT=15 SRC=\"$CFG->wwwroot/pix/s/cool.gif\">";
$SMILEY_TEXT[]  = "^-)";
$SMILEY_IMAGE[] = "<IMG ALT=\"^-)\" WIDTH=15 HEIGHT=15 SRC=\"$CFG->wwwroot/pix/s/approve.gif\">";
$SMILEY_TEXT[]  = "8-)";
$SMILEY_IMAGE[] = "<IMG ALT=\"8-)\" WIDTH=15 HEIGHT=15 SRC=\"$CFG->wwwroot/pix/s/wideeyes.gif\">";
$SMILEY_TEXT[]  = ":o)";
$SMILEY_IMAGE[] = "<IMG ALT=\":o)\" WIDTH=15 HEIGHT=15 SRC=\"$CFG->wwwroot/pix/s/clown.gif\">";
$SMILEY_TEXT[]  = ":-(";
$SMILEY_IMAGE[] = "<IMG ALT=\":-(\" WIDTH=15 HEIGHT=15 SRC=\"$CFG->wwwroot/pix/s/sad.gif\">";
$SMILEY_TEXT[]  = ":(";
$SMILEY_IMAGE[] = "<IMG ALT=\":-(\" WIDTH=15 HEIGHT=15 SRC=\"$CFG->wwwroot/pix/s/sad.gif\">";
$SMILEY_TEXT[]  = "8-.";
$SMILEY_IMAGE[] = "<IMG ALT=\"8-.\" WIDTH=15 HEIGHT=15 SRC=\"$CFG->wwwroot/pix/s/shy.gif\">";
$SMILEY_TEXT[]  = ":-I";
$SMILEY_IMAGE[] = "<IMG ALT=\":-I\" WIDTH=15 HEIGHT=15 SRC=\"$CFG->wwwroot/pix/s/blush.gif\">";
$SMILEY_TEXT[]  = ":-X";
$SMILEY_IMAGE[] = "<IMG ALT=\":-X\" WIDTH=15 HEIGHT=15 SRC=\"$CFG->wwwroot/pix/s/kiss.gif\">";
$SMILEY_TEXT[]  = "8-o";
$SMILEY_IMAGE[] = "<IMG ALT=\"8-o\" WIDTH=15 HEIGHT=15 SRC=\"$CFG->wwwroot/pix/s/surprise.gif\">";
$SMILEY_TEXT[]  = "P-|";
$SMILEY_IMAGE[] = "<IMG ALT=\"P-|\" WIDTH=15 HEIGHT=15 SRC=\"$CFG->wwwroot/pix/s/blackeye.gif\">";
$SMILEY_TEXT[]  = "8-[";
$SMILEY_IMAGE[] = "<IMG ALT=\"8-[\" WIDTH=15 HEIGHT=15 SRC=\"$CFG->wwwroot/pix/s/angry.gif\">";
$SMILEY_TEXT[]  = "xx-P";
$SMILEY_IMAGE[] = "<IMG ALT=\"xx-P\" WIDTH=15 HEIGHT=15 SRC=\"$CFG->wwwroot/pix/s/dead.gif\">";
$SMILEY_TEXT[]  = "|-.";
$SMILEY_IMAGE[] = "<IMG ALT=\"|-.\" WIDTH=15 HEIGHT=15 SRC=\"$CFG->wwwroot/pix/s/sleepy.gif\">";
$SMILEY_TEXT[]  = "}-]";
$SMILEY_IMAGE[] = "<IMG ALT=\"}-]\" WIDTH=15 HEIGHT=15 SRC=\"$CFG->wwwroot/pix/s/evil.gif\">";

$JAVASCRIPT_TAGS = array("javascript:", "onclick=", "ondblclick=", "onkeydown=", "onkeypress=", "onkeyup=", 
                         "onmouseover=", "onmouseout=", "onmousedown=", "onmouseup=",
                         "onblur=", "onfocus=", "onload=", "onselect=");

$ALLOWED_TAGS = "<p><br><b><i><u><font><table><tbody><span><div><tr><td><ol><ul><dl><li><dt><dd><h1><h2><h3><h4><h5><h6><hr><img><a><strong><emphasis><sup><sub><address><cite><blockquote><pre><strike><embed><object><param>";


/// Functions

function s($var) {
/// returns $var with HTML characters (like "<", ">", etc.) properly quoted,

    return htmlSpecialChars(stripslashes_safe($var));
}

function p($var) {
/// prints $var with HTML characters (like "<", ">", etc.) properly quoted,

    echo htmlSpecialChars(stripslashes_safe($var));
}

function nvl(&$var, $default="") {
/// if $var is undefined, return $default, otherwise return $var 

    return isset($var) ? $var : $default;
}

function strip_querystring($url) {
/// takes a URL and returns it without the querystring portion 

    if ($commapos = strpos($url, '?')) {
        return substr($url, 0, $commapos);
    } else {
        return $url;
    }
}

function get_referer() {
/// returns the URL of the HTTP_REFERER, less the querystring portion 

    return strip_querystring(nvl($_SERVER["HTTP_REFERER"]));
}


function me() {
/// returns the name of the current script, WITH the querystring portion.
/// this function is necessary because PHP_SELF and REQUEST_URI and SCRIPT_NAME
/// return different things depending on a lot of things like your OS, Web
/// server, and the way PHP is compiled (ie. as a CGI, module, ISAPI, etc.) 

    if (!empty($_SERVER["REQUEST_URI"])) {
        return $_SERVER["REQUEST_URI"];

    } else if (!empty($_SERVER["PHP_SELF"])) {
        if (!empty($_SERVER["QUERY_STRING"])) {
            return $_SERVER["PHP_SELF"]."?".$_SERVER["QUERY_STRING"];
        }
        return $_SERVER["PHP_SELF"];

    } else if (!empty($_SERVER["SCRIPT_NAME"])) {
        if (!empty($_SERVER["QUERY_STRING"])) {
            return $_SERVER["SCRIPT_NAME"]."?".$_SERVER["QUERY_STRING"];
        }
        return $_SERVER["SCRIPT_NAME"];

    } else {
        notify("Warning: Could not find any of these web server variables: \$REQUEST_URI, \$PHP_SELF or \$SCRIPT_NAME");
    }
}


function qualified_me() {
/// like me() but returns a full URL 

    if (!empty($_SERVER["HTTP_HOST"])) {
        $hostname = $_SERVER["HTTP_HOST"];
    } else if (!empty($_ENV["HTTP_HOST"])) {
        $hostname = $_ENV["HTTP_HOST"];
    } else if (!empty($_ENV["SERVER_NAME"])) {
        $hostname = $_ENV["SERVER_NAME"];
    } else {
        notify("Warning: could not find the name of this server!");
    }

    $protocol = (isset($_SERVER["HTTPS"]) and $_SERVER["HTTPS"] == "on") ? "https://" : "http://";
    $url_prefix = $protocol.$hostname;
    return $url_prefix . me();
}


function match_referer($good_referer = "") {
/// returns true if the referer is the same as the good_referer.  If
/// good_referer is not specified, use qualified_me as the good_referer 
    global $CFG;

    if (!empty($CFG->buggy_referer)) {
        return true;
    }

    if (empty($good_referer)) { 
        $good_referer = qualified_me(); 
    }
    return $good_referer == get_referer();
}

function data_submitted($url="") {
/// Used on most forms in Moodle to check for data
/// Returns the data as an object, if it's found.
/// This object can be used in foreach loops without
/// casting because it's cast to (array) automatically
/// 
/// Checks that submitted POST data exists, and also 
/// checks the referer against the given url (it uses 
/// the current page if none was specified.

    global $CFG;

    if (empty($_POST)) {
        return false;

    } else {
        if (match_referer($url)) {
            return (object)$_POST;
        } else {
            if ($CFG->debug > 10) {
                notice("The form did not come from this page! (referer = ".get_referer().")");
            }
            return false;
        }
    }
}

function stripslashes_safe($string) {
/// stripslashes() removes ALL backslashes even from strings 
/// so  C:\temp becomes C:temp  ... this isn't good.
/// The following should work as a fairly safe replacement 
/// to be called on quoted AND unquoted strings (to be sure)

    $string = str_replace("\\'", "'", $string);
    $string = str_replace('\\"', '"', $string);
    $string = str_replace('\\\\', '\\', $string);
    return $string;
}

function stri_replace($find, $replace, $string ) {
/// This does a search and replace, ignoring case
/// This function is only here because one doesn't exist yet in PHP
/// Unlike str_replace(), this only works on single values (not arrays)

    $parts = explode(strtolower($find), strtolower($string));

    $pos = 0;

    foreach ($parts as $key => $part) {
        $parts[$key] = substr($string, $pos, strlen($part));
        $pos += strlen($part) + strlen($find);
    }

    return (join($replace, $parts));
}

function read_template($filename, &$var) {
/// return a (big) string containing the contents of a template file with all
/// the variables interpolated.  all the variables must be in the $var[] array or
/// object (whatever you decide to use).
///
/// WARNING: do not use this on big files!! 

    $temp = str_replace("\\", "\\\\", implode(file($filename), ""));
    $temp = str_replace('"', '\"', $temp);
    eval("\$template = \"$temp\";");
    return $template;
}

function checked(&$var, $set_value = 1, $unset_value = 0) {
/// if variable is set, set it to the set_value otherwise set it to the
/// unset_value.  used to handle checkboxes when you are expecting them from
/// a form 

    if (empty($var)) {
        $var = $unset_value;
    } else {
        $var = $set_value;
    }
}

function frmchecked(&$var, $true_value = "checked", $false_value = "") {
/// prints the word "checked" if a variable is true, otherwise prints nothing,
/// used for printing the word "checked" in a checkbox form input 

    if ($var) {
        echo $true_value;
    } else {
        echo $false_value;
    }
}


function link_to_popup_window ($url, $name="popup", $linkname="click here", $height=400, $width=500, $title="Popup window") {
/// This will create a HTML link that will work on both 
/// Javascript and non-javascript browsers.
/// Relies on the Javascript function openpopup in javascript.php
/// $url must be relative to home page  eg /mod/survey/stuff.php

    global $CFG;

    echo "\n<SCRIPT language=\"Javascript\">";
    echo "\n<!--";
    echo "\ndocument.write('<A TITLE=\"$title\" HREF=javascript:openpopup(\"$url\",\"$name\",\"$height\",\"$width\") >$linkname</A>');";
    echo "\n//-->";
    echo "\n</SCRIPT>";
    echo "\n<NOSCRIPT>\n<A TARGET=\"$name\" TITLE=\"$title\" HREF=\"$CFG->wwwroot/$url\">$linkname</A>\n</NOSCRIPT>\n";

}

function close_window_button() {
/// Prints a simple button to close a window

    echo "<FORM><CENTER>";
    echo "<INPUT TYPE=button onClick=\"self.close();\" VALUE=\"".get_string("closewindow")."\">";
    echo "</CENTER></FORM>";
}


function choose_from_menu ($options, $name, $selected="", $nothing="choose", $script="", $nothingvalue="0", $return=false) {
/// Given an array of value, creates a popup menu to be part of a form
/// $options["value"]["label"]
    
    if ($nothing == "choose") {
        $nothing = get_string("choose")."...";
    }

    if ($script) {
        $javascript = "onChange=\"$script\"";
    } else {
        $javascript = "";
    }

    $output = "<SELECT NAME=$name $javascript>\n";
    if ($nothing) {
        $output .= "   <OPTION VALUE=\"$nothingvalue\"\n";
        if ($nothingvalue == $selected) {
            $output .= " SELECTED";
        }
        $output .= ">$nothing</OPTION>\n";
    }
    if (!empty($options)) {
        foreach ($options as $value => $label) {
            $output .= "   <OPTION VALUE=\"$value\"";
            if ($value == $selected) {
                $output .= " SELECTED";
            }
            if ($label) {
                $output .= ">$label</OPTION>\n";
            } else {
                $output .= ">$value</OPTION>\n";
            }
        }
    }
    $output .= "</SELECT>\n";

    if ($return) {
        return $output;
    } else {
        echo $output;
    }
}   

function popup_form ($common, $options, $formname, $selected="", $nothing="choose", $help="", $helptext="", $return=false) {
///  Implements a complete little popup form
///  $common   = the URL up to the point of the variable that changes
///  $options  = A list of value-label pairs for the popup list
///  $formname = name must be unique on the page
///  $selected = the option that is already selected
///  $nothing  = The label for the "no choice" option
///  $help     = The name of a help page if help is required
///  $helptext  = The name of the label for the help button

    if ($nothing == "choose") {
        $nothing = get_string("choose")."...";
    }

    $output = "<FORM TARGET=_top NAME=$formname>";
    $output .= "<SELECT NAME=popup onChange=\"top.location=document.$formname.popup.options[document.$formname.popup.selectedIndex].value\">\n";

    if ($nothing != "") {
        $output .= "   <OPTION VALUE=\"javascript:void(0)\">$nothing</OPTION>\n";
    }

    foreach ($options as $value => $label) {
        if (substr($label,0,1) == "-") {
            $output .= "   <OPTION VALUE=\"\"";
        } else {
            $output .= "   <OPTION VALUE=\"$common$value\"";
            if ($value == $selected) {
                $output .= " SELECTED";
            }
        }
        if ($label) {
            $output .= ">$label</OPTION>\n";
        } else {
            $output .= ">$value</OPTION>\n";
        }
    }
    $output .= "</SELECT>";
    $output .= "</FORM>\n";

    if ($return) {
        return $output;
    } else {
        if ($help) {
            helpbutton($help, $helptext);
        }
        echo $output;
    }
}



function formerr($error) {
/// Prints some red text
    if (!empty($error)) {
        echo "<font color=#ff0000>$error</font>";
    }
}


function validate_email ($address) {
/// Validates an email to make it makes sense.
    return (ereg('^[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+'.
                  '@'.
                  '[-!#$%&\'*+\\/0-9=?A-Z^_`a-z{|}~]+\.'.
                  '[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+$',
                  $address));
}

function get_slash_arguments($file="file.php") {
/// Searches the current environment variables for some slash arguments

    if (!$string = me()) {
        return false;
    }

    $pathinfo = explode($file, $string);
    
    if (!empty($path_info[1])) {
        return $path_info[1];
    } else {
        return false;
    }
}

function parse_slash_arguments($string, $i=0) {
/// Extracts arguments from "/foo/bar/something"
/// eg http://mysite.com/script.php/foo/bar/something

    if (strpos($string, "..")) { // check for parent URLs
        return false;
    }
    if (strpos($string, "|")) {  // check for pipes
        return false;
    }
    if (strpos($string, "`")) {  // check for backquotes
        return false;
    }

    $args = explode("/", $string);

    if ($i) {     // return just the required argument
        return $args[$i];

    } else {      // return the whole array
        array_shift($args);  // get rid of the empty first one
        return $args;
    }
}

function format_text_menu() {
/// Just returns an array of formats suitable for a popup menu
    return array (FORMAT_MOODLE => get_string("formattext"), 
                  FORMAT_HTML   => get_string("formathtml") );
}

function format_text($text, $format=FORMAT_MOODLE, $options=NULL) {
/// Given text in a variety of format codings, this function returns 
/// the text as safe HTML.
///
/// $text is raw text (originally from a user)
/// $format is one of the format constants, defined above

    switch ($format) {
        case FORMAT_MOODLE:
            if (!isset($options->smiley)) {
                $options->smiley=true;
            }
            if (!isset($options->para)) {
                $options->para=true;
            }
            return text_to_html($text, $options->smiley, $options->para);
            break;

        case FORMAT_HTML:
            $text = replace_smilies($text);
            return $text;
            break;
    }
}


function clean_text($text, $format) {
/// Given raw text (eg typed in by a user), this function cleans it up 
/// and removes any nasty tags that could mess up Moodle pages.

    global $JAVASCRIPT_TAGS, $ALLOWED_TAGS;

    switch ($format) {   // Does the same thing, currently, but it's nice to have the option
        case FORMAT_MOODLE:
            $text = strip_tags($text, $ALLOWED_TAGS);
            foreach ($JAVASCRIPT_TAGS as $tag) {
                $text = stri_replace($tag, "", $text);
            }
            return $text;

        case FORMAT_HTML:
            $text = strip_tags($text, $ALLOWED_TAGS);
            foreach ($JAVASCRIPT_TAGS as $tag) {
                $text = stri_replace($tag, "", $text);
            }
            return $text;
    }
}

function replace_smilies($text) {
/// Replaces all known smileys in the text with image equivalents

    global $CFG, $SMILEY_TEXT, $SMILEY_IMAGE;

    return str_replace($SMILEY_TEXT, $SMILEY_IMAGE, $text);
}

function text_to_html($text, $smiley=true, $para=true) {
/// Given plain text, makes it into HTML as nicely as possible.
/// May contain HTML tags already

/// Remove any whitespace that may be between HTML tags
    $text = eregi_replace(">([[:space:]]+)<", "><", $text);

/// Remove any returns that precede or follow HTML tags
    $text = eregi_replace("([\n\r])<", " <", $text);
    $text = eregi_replace(">([\n\r])", "> ", $text);

/// Make lone URLs into links.   eg http://moodle.com/
    $text = eregi_replace("([\n\r ([])([[:alnum:]]+)://([^[:space:]]*)([[:alnum:]#?/&=])", 
                          "\\1<A HREF=\"\\2://\\3\\4\" TARGET=\"newpage\">\\2://\\3\\4</A>", $text);

/// eg www.moodle.com
    $text = eregi_replace("([[:space:]])www\.([^[:space:]]*)([[:alnum:]#?/&=])", 
                          "\\1<A HREF=\"http://www.\\2\\3\" TARGET=\"newpage\">www.\\2\\3</A>", $text);

/// Make returns into HTML newlines.
    $text = nl2br($text);

/// Turn smileys into images.
    if ($smiley) {
        $text = replace_smilies($text);
    }

/// Wrap the whole thing in a paragraph tag if required
    if ($para) {
        return "<P>".$text."</P>";
    } else {
        return $text;
    }
}

function highlight($needle, $haystack) {
/// This function will highlight instances of $needle in $haystack

    $parts = explode(strtolower($needle), strtolower($haystack));

    $pos = 0;

    foreach ($parts as $key => $part) {
        $parts[$key] = substr($haystack, $pos, strlen($part));
        $pos += strlen($part);

        $parts[$key] .= "<SPAN CLASS=highlight>".substr($haystack, $pos, strlen($needle))."</SPAN>";
        $pos += strlen($needle);
    }   

    return (join('', $parts));
}



/// STANDARD WEB PAGE PARTS ///////////////////////////////////////////////////

function print_header ($title="", $heading="", $navigation="", $focus="", $meta="", $cache=true, $button="&nbsp;", $menu="") {
// $title - appears top of window
// $heading - appears top of page
// $navigation - premade navigation string
// $focus - indicates form element eg  inputform.password
// $meta - meta tags in the header
// $cache - should this page be cacheable?
// $button - HTML code for a button (usually for module editing)
// $menu - HTML code for a popup menu 
    global $USER, $CFG, $THEME;

    if (file_exists("$CFG->dirroot/theme/$CFG->theme/styles.php")) {
        $styles = $CFG->stylesheet;
    } else {
        $styles = "$CFG->wwwroot/theme/standard/styles.php";
    }

    if ($navigation == "home") {
        $home = true;
        $navigation = "";
    } else {
        $home = false;
    }

    if ($button == "") {
        $button = "&nbsp;";
    }

    if (!$menu and $navigation) {
        if (isset($USER->id)) {
            $menu = "<FONT SIZE=2><A TARGET=_parent HREF=\"$CFG->wwwroot/login/logout.php\">".get_string("logout")."</A></FONT>";
        } else {
            $menu = "<FONT SIZE=2><A TARGET=_parent HREF=\"$CFG->wwwroot/login/index.php\">".get_string("login")."</A></FONT>";
        }
    }

    // Specify character set ... default is iso-8859-1 but some languages might need something else
    // Could be optimised by carrying the charset variable around in $USER
    if (current_language() == "en") {
        $meta = "<META HTTP-EQUIV=\"Content-Type\" CONTENT=\"text/html; charset=iso-8859-1\">\n$meta\n";
    } else {
        $meta = "<META HTTP-EQUIV=\"Content-Type\" CONTENT=\"text/html; charset=".get_string("thischarset")."\">\n$meta\n";
    }

    if ($CFG->langdir == "RTL") {
        $direction = " DIR=\"RTL\"";
    } else {
        $direction = " DIR=\"LTR\"";
    }
 
    if (!$cache) {   // Do everything we can to prevent clients and proxies caching
        @header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        @header("Pragma: no-cache");
        $meta .= "\n<META HTTP-EQUIV=\"Pragma\" CONTENT=\"no-cache\">";
        $meta .= "\n<META HTTP-EQUIV=\"Expires\" CONTENT=\"0\">";
    }

    include ("$CFG->dirroot/theme/$CFG->theme/header.html");
}

function print_footer ($course=NULL) {
// Can provide a course object to make the footer contain a link to 
// to the course home page, otherwise the link will go to the site home
    global $USER, $CFG, $THEME;


/// Course links
    if ($course) {
        if ($course == "home") {   // special case for site home page - please do not remove
            $homelink  = "<P ALIGN=center><A TITLE=\"Moodle $CFG->release ($CFG->version)\" HREF=\"http://moodle.com/\">";
            $homelink .= "<BR><IMG WIDTH=130 HEIGHT=19 SRC=\"pix/madewithmoodle2.gif\" BORDER=0></A></P>";
            $course = get_site();
            $homepage = true;
        } else {
            $homelink = "<A TARGET=_top HREF=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</A>";
        }
    } else {
        $homelink = "<A TARGET=_top HREF=\"$CFG->wwwroot\">".get_string("home")."</A>";
        $course = get_site();
    }

/// User links
    if (isset($USER->realuser)) {
        if ($realuser = get_record("user", "id", $USER->realuser)) {
            $realuserinfo = " [<A HREF=\"$CFG->wwwroot/course/loginas.php?id=$course->id&return=$realuser->id\">$realuser->firstname $realuser->lastname</A>] ";
        }
    } else {
        $realuserinfo = "";
    }

    if (isset($USER->id) and $USER->id) {
        $username = "<A HREF=\"$CFG->wwwroot/user/view.php?id=$USER->id&course=$course->id\">$USER->firstname $USER->lastname</A>";
        $loggedinas = $realuserinfo.get_string("loggedinas", "moodle", "$username").
                      " (<A HREF=\"$CFG->wwwroot/login/logout.php\">".get_string("logout")."</A>)";
    } else {
        $loggedinas = get_string("loggedinnot", "moodle").
                      " (<A HREF=\"$CFG->wwwroot/login/index.php\">".get_string("login")."</A>)";
    }

    include ("$CFG->dirroot/theme/$CFG->theme/footer.html");
}



function print_navigation ($navigation) {
   global $CFG;

   if ($navigation) {
       if (! $site = get_site()) {
           $site->shortname = get_string("home");;
       }
       echo "<A TARGET=_top HREF=\"$CFG->wwwroot/\">$site->shortname</A> -> $navigation";
   }
}

function print_heading($text, $align="CENTER", $size=3) {
    echo "<P ALIGN=\"$align\"><FONT SIZE=\"$size\"><B>".stripslashes_safe($text)."</B></FONT></P>";
}

function print_heading_with_help($text, $helppage, $module="moodle") {
// Centered heading with attached help button (same title text)
    echo "<P ALIGN=\"CENTER\"><FONT SIZE=\"3\"><B>".stripslashes_safe($text);
    helpbutton($helppage, $text, $module);
    echo "</B></FONT></P>";
}
    
function print_continue($link) {

    if (!$link) {
        $link = $_SERVER["HTTP_REFERER"];
    }

    print_heading("<A HREF=\"$link\">".get_string("continue")."</A>");
}


function print_simple_box($message, $align="", $width="", $color="#FFFFFF", $padding=5, $class="generalbox") {
    print_simple_box_start($align, $width, $color, $padding, $class);
    echo stripslashes_safe($message);
    print_simple_box_end();
}

function print_simple_box_start($align="", $width="", $color="#FFFFFF", $padding=5, $class="generalbox") {
    global $THEME;

    if ($align) {
        $align = "ALIGN=\"$align\"";
    }
    if ($width) {
        $width = "WIDTH=\"$width\"";
    }
    echo "<table $align $width class=\"$class\" border=\"0\" cellpadding=\"$padding\" cellspacing=\"0\"><tr><td bgcolor=\"$color\" class=\"$class"."content\">";
}

function print_simple_box_end() {
    echo "</td></tr></table>";
}

function print_single_button($link, $options, $label="OK") {
    echo "<FORM ACTION=\"$link\" METHOD=GET>";
    if ($options) {
        foreach ($options as $name => $value) {
            echo "<INPUT TYPE=hidden NAME=\"$name\" VALUE=\"$value\">";
        }
    }
    echo "<INPUT TYPE=submit VALUE=\"$label\"></FORM>";
}

function print_spacer($height=1, $width=1, $br=true) {
    global $CFG;
    echo "<IMG HEIGHT=\"$height\" WIDTH=\"$width\" SRC=\"$CFG->wwwroot/pix/spacer.gif\" ALT=\"\">";
    if ($br) {
        echo "<BR \>\n";
    }
}

function print_file_picture($path, $courseid=0, $height="", $width="", $link="") {
// Given the path to a picture file in a course, or a URL,
// this function includes the picture in the page.
    global $CFG;

    if ($height) {
        $height = "HEIGHT=\"$height\"";
    }
    if ($width) {
        $width = "WIDTH=\"$width\"";
    }
    if ($link) {
        echo "<A HREF=\"$link\">";
    }
    if (substr(strtolower($path), 0, 7) == "http://") {
        echo "<IMG BORDER=0 $height $width SRC=\"$path\">";

    } else if ($courseid) {
        echo "<IMG BORDER=0 $height $width SRC=\"";
        if ($CFG->slasharguments) {        // Use this method if possible for better caching
            echo "$CFG->wwwroot/file.php/$courseid/$path";
        } else {
            echo "$CFG->wwwroot/file.php?file=$courseid/$path";
        }
        echo "\">";
    } else {
        echo "Error: must pass URL or course";
    }
    if ($link) {
        echo "</A>";
    }
}

function print_user_picture($userid, $courseid, $picture, $large=false, $returnstring=false, $link=true) {
    global $CFG;

    if ($link) {
        $output = "<A HREF=\"$CFG->wwwroot/user/view.php?id=$userid&course=$courseid\">";
    } else {
        $output = "";
    }
    if ($large) {
        $file = "f1.jpg";
        $size = 100;
    } else {
        $file = "f2.jpg";
        $size = 35;
    }
    if ($picture) {
        if ($CFG->slasharguments) {        // Use this method if possible for better caching
            $output .= "<IMG SRC=\"$CFG->wwwroot/user/pix.php/$userid/$file\" BORDER=0 WIDTH=$size HEIGHT=$size ALT=\"\">";
        } else {
            $output .= "<IMG SRC=\"$CFG->wwwroot/user/pix.php?file=/$userid/$file\" BORDER=0 WIDTH=$size HEIGHT=$size ALT=\"\">";
        }
    } else {
        $output .= "<IMG SRC=\"$CFG->wwwroot/user/default/$file\" BORDER=0 WIDTH=$size HEIGHT=$size ALT=\"\">";
    }
    if ($link) {
        $output .= "</A>";
    }

    if ($returnstring) {
        return $output;
    } else {
        echo $output;
    }
}

function print_table($table) {
// Prints a nicely formatted table.
// $table is an object with several properties.
//     $table->head      is an array of heading names.
//     $table->align     is an array of column alignments
//     $table->size      is an array of column sizes
//     $table->data[]    is an array of arrays containing the data.
//     $table->width     is an percentage of the page
//     $table->cellpadding    padding on each cell
//     $table->cellspacing    spacing between cells

    if (isset($table->align)) {
        foreach ($table->align as $key => $aa) {
            if ($aa) {
                $align[$key] = " ALIGN=\"$aa\"";
            } else {
                $align[$key] = "";
            }
        }
    }
    if (isset($table->size)) {
        foreach ($table->size as $key => $ss) {
            if ($ss) {
                $size[$key] = " WIDTH=\"$ss\"";
            } else {
                $size[$key] = "";
            }
        }
    }

    if (empty($table->width)) {
        $table->width = "80%";
    }

    if (empty($table->cellpadding)) {
        $table->cellpadding = "5";
    }

    if (empty($table->cellspacing)) {
        $table->cellspacing = "1";
    }

    print_simple_box_start("CENTER", "$table->width", "#FFFFFF", 0);
    echo "<TABLE WIDTH=100% BORDER=0 valign=top align=center ";
    echo " cellpadding=\"$table->cellpadding\" cellspacing=\"$table->cellspacing\" class=\"generaltable\">\n";

    if (!empty($table->head)) {
        echo "<TR>";
        foreach ($table->head as $key => $heading) {
            if (!isset($size[$key])) {
                $size[$key] = "";
            } 
            if (!isset($align[$key])) {
                $align[$key] = "";
            } 
            echo "<TH VALIGN=top ".$align[$key].$size[$key]." NOWRAP class=\"generaltableheader\">$heading</TH>";
        }
        echo "</TR>\n";
    }

    foreach ($table->data as $row) {
        echo "<TR VALIGN=TOP>";
        foreach ($row as $key => $item) {
            if (!isset($size[$key])) {
                $size[$key] = "";
            } 
            if (!isset($align[$key])) {
                $align[$key] = "";
            } 
            echo "<TD ".$align[$key].$size[$key]." class=\"generaltablecell\">$item</TD>";
        }
        echo "</TR>\n";
    }
    echo "</TABLE>\n";
    print_simple_box_end();

    return true;
}

function print_editing_switch($courseid) {
    global $CFG, $USER;

    if (isteacher($courseid)) {
        if ($USER->editing) {
            echo "<A HREF=\"$CFG->wwwroot/course/view.php?id=$courseid&edit=off\">Turn editing off</A>";
        } else {
            echo "<A HREF=\"$CFG->wwwroot/course/view.php?id=$courseid&edit=on\">Turn editing on</A>";
        }
    }
}

function print_textarea($richedit, $rows, $cols, $width, $height, $name, $value="") {
/// Prints a richtext field or a normal textarea
    global $CFG, $THEME;

    if ($richedit) {
        echo "<object id=richedit style=\"BACKGROUND-COLOR: buttonface\"";
        echo " data=\"$CFG->wwwroot/lib/rte/richedit.html\"";
        echo " width=\"$width\" height=\"$height\" ";
        echo " type=\"text/x-scriptlet\" VIEWASTEXT></object>\n";
        echo "<TEXTAREA style=\"display:none\" NAME=\"$name\" ROWS=1 COLS=1>";
        p($value);
        echo "</TEXTAREA>\n";
    } else {
        echo "<TEXTAREA name=\"$name\" rows=\"$rows\" cols=\"$cols\" wrap=virtual>";
        p($value);
        echo "</TEXTAREA>\n";
    }
}

function print_richedit_javascript($form, $name, $source="no") {
    echo "<SCRIPT language=\"JavaScript\" event=\"onload\" for=\"window\">\n";
    echo "   document.richedit.options = \"history=no;source=$source\";";
    echo "   document.richedit.docHtml = $form.$name.innerText;";
    echo "</SCRIPT>";
}


function update_course_icon($courseid) {
// Used to be an icon, but it's now a simple form button
    global $CFG, $USER;

    if (isteacher($courseid)) {
        if (!empty($USER->editing)) {
            $string = get_string("turneditingoff");
            $edit = "off";
        } else {
            $string = get_string("turneditingon");
            $edit = "on";
        }
        return "<FORM TARGET=_parent METHOD=GET ACTION=\"$CFG->wwwroot/course/view.php\">".
               "<INPUT TYPE=hidden NAME=id VALUE=\"$courseid\">".
               "<INPUT TYPE=hidden NAME=edit VALUE=\"$edit\">".
               "<INPUT TYPE=submit VALUE=\"$string\"></FORM>";
    }
}

function update_module_button($moduleid, $courseid, $string) {
// Prints the editing button on a module "view" page
    global $CFG;

    if (isteacher($courseid)) {
        $string = get_string("updatethis", "", $string);
        return "<FORM TARGET=_parent METHOD=GET ACTION=\"$CFG->wwwroot/course/mod.php\">".
               "<INPUT TYPE=hidden NAME=update VALUE=\"$moduleid\">".
               "<INPUT TYPE=hidden NAME=return VALUE=\"true\">".
               "<INPUT TYPE=submit VALUE=\"$string\"></FORM>";
    }
}


function navmenu($course, $cm=NULL) {
// Given a course and a (current) coursemodule
// This function returns a small popup menu with all the 
// course activity modules in it, as a navigation menu
// The data is taken from the serialised array stored in 
// the course record

    global $CFG;

    if ($cm) {
       $cm = $cm->id;
    }

    if ($course->format == 'weeks') {
        $strsection = get_string("week");
    } else {
        $strsection = get_string("topic");
    }

    if (!$modinfo = unserialize($course->modinfo)) {
        return "";
    }
    $section = -1;
    $selected = "";
    foreach ($modinfo as $mod) {
        if ($mod->section > 0 and $section <> $mod->section) {
            $menu[] = "-------------- $strsection $mod->section --------------";
        }
        $section = $mod->section;
        $url = "$mod->mod/view.php?id=$mod->cm";
        if ($cm == $mod->cm) {
            $selected = $url;
        }
        $mod->name = urldecode($mod->name);
        if (strlen($mod->name) > 55) {
            $mod->name = substr($mod->name, 0, 50)."...";
        }
        $menu[$url] = $mod->name; 
    }

    return popup_form("$CFG->wwwroot/mod/", $menu, "navmenu", $selected, get_string("jumpto"), "", "", true);
}   



function print_date_selector($day, $month, $year, $currenttime=0) {
// Currenttime is a default timestamp in GMT
// Prints form items with the names $day, $month and $year

    if (!$currenttime) {
        $currenttime = time();
    }
    $currentdate = usergetdate($currenttime);

    for ($i=1; $i<=31; $i++) {
        $days[$i] = "$i";
    }
    for ($i=1; $i<=12; $i++) {
        $months[$i] = userdate(gmmktime(12,0,0,$i,1,2000), "%B");
    }
    for ($i=2000; $i<=2010; $i++) {
        $years[$i] = $i;
    }
    choose_from_menu($days,   $day,   $currentdate['mday'], "");
    choose_from_menu($months, $month, $currentdate['mon'],  "");
    choose_from_menu($years,  $year,  $currentdate['year'], "");
}

function print_time_selector($hour, $minute, $currenttime=0) {
// Currenttime is a default timestamp in GMT
// Prints form items with the names $hour and $minute

    if (!$currenttime) {
        $currenttime = time();
    }
    $currentdate = usergetdate($currenttime);
    for ($i=0; $i<=23; $i++) {
        $hours[$i] = sprintf("%02d",$i);
    }
    for ($i=0; $i<=59; $i++) {
        $minutes[$i] = sprintf("%02d",$i);
    }
    choose_from_menu($hours,   $hour,   $currentdate['hours'],   "");
    choose_from_menu($minutes, $minute, $currentdate['minutes'], "");
}

function error ($message, $link="") {
    global $CFG, $SESSION;

    print_header(get_string("error"));
    echo "<BR>";
    print_simple_box($message, "center", "", "#FFBBBB");
   
    if (!$link) {
        if ( !empty($SESSION->fromurl) ) {
            $link = "$SESSION->fromurl";
            unset($SESSION->fromurl);
            save_session("SESSION");
        } else {
            $link = "$CFG->wwwroot";
        }
    }
    print_continue($link);
    print_footer();
    die;
}

function helpbutton ($page, $title="", $module="moodle", $image=true, $linktext=false, $text="") {
    // $page = the keyword that defines a help page
    // $title = the title of links, rollover tips, alt tags etc
    // $module = which module is the page defined in
    // $image = use a help image for the link?  (true/false/"both")
    // $text = if defined then this text is used in the page, and 
    //         the $page variable is ignored.
    global $CFG;

    if ($module == "") {
        $module = "moodle";
    }

    if ($image) {
        if ($linktext) {
            $linkobject = "$title<IMG align=\"absmiddle\" BORDER=0 HEIGHT=17 WIDTH=22 ALT=\"\" SRC=\"$CFG->wwwroot/pix/help.gif\">";
        } else {
            $linkobject = "<IMG align=\"absmiddle\" BORDER=0 HEIGHT=17 WIDTH=22 ALT=\"$title\" SRC=\"$CFG->wwwroot/pix/help.gif\">";
        }
    } else {
        $linkobject = $title;
    }
    if ($text) {
        $url = "/help.php?module=$module&text=".htmlentities(urlencode($text));
    } else {
        $url = "/help.php?module=$module&file=$page.html";
    }
    link_to_popup_window ($url, "popup", $linkobject, 400, 500, $title);
}

function notice ($message, $link="") {
    global $CFG, $THEME;

    if (!$link) {
        if (!empty($_SERVER["HTTP_REFERER"])) {
            $link = $_SERVER["HTTP_REFERER"];
        } else {
            $link = $CFG->wwwroot;
        }
    }

    echo "<BR>";
    print_simple_box($message, "center", "", "$THEME->cellheading");
    print_heading("<A HREF=\"$link\">".get_string("continue")."</A>");
    print_footer(get_site());
    die;
}

function notice_yesno ($message, $linkyes, $linkno) {
    global $THEME;

    print_simple_box_start("center", "", "$THEME->cellheading");
    echo "<P ALIGN=CENTER><FONT SIZE=3>$message</FONT></P>";
    echo "<P ALIGN=CENTER><FONT SIZE=3><B>";
    echo "<A HREF=\"$linkyes\">".get_string("yes")."</A>";
    echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
    echo "<A HREF=\"$linkno\">".get_string("no")."</A>";
    echo "</B></FONT></P>";
    print_simple_box_end();
}

function redirect($url, $message="", $delay=0) {
// Uses META tags to redirect the user, after printing a notice

    echo "<META HTTP-EQUIV='Refresh' CONTENT='$delay; URL=$url'>";

    if (!empty($message)) {
        print_header();
        echo "<CENTER>";
        echo "<P>$message</P>";
        echo "<P>( <A HREF=\"$url\">".get_string("continue")."</A> )</P>";
        echo "</CENTER>";
    }
    die; 
}

function notify ($message, $color="red", $align="center") {
    echo "<p align=\"$align\"><b><font color=\"$color\">$message</font></b></p>\n";
}


?>
