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
define("FORMAT_MOODLE", "0");   // Does all sorts of transformations and filtering
define("FORMAT_HTML",   "1");   // Plain HTML (with some tags stripped)
define("FORMAT_PLAIN",  "2");   // Plain text (even tags are printed in full)
define("FORMAT_WIKI",   "3");   // Wiki-formatted text

$JAVASCRIPT_TAGS = array("javascript:", "onclick=", "ondblclick=", "onkeydown=", "onkeypress=", "onkeyup=", 
                         "onmouseover=", "onmouseout=", "onmousedown=", "onmouseup=",
                         "onblur=", "onfocus=", "onload=", "onselect=");

$ALLOWED_TAGS = "<p><br><b><i><u><font><table><tbody><span><div><tr><td><ol><ul><dl><li><dt><dd><h1><h2><h3><h4><h5><h6><hr><img><a><strong><emphasis><sup><sub><address><cite><blockquote><pre><strike><embed><object><param>";


/// Functions

function s($var="") {
/// returns $var with HTML characters (like "<", ">", etc.) properly quoted,

    return htmlSpecialChars(stripslashes_safe($var));
}

function p($var="") {
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
        return false;
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
        return false;
    }

    $protocol = (isset($_SERVER["HTTPS"]) and $_SERVER["HTTPS"] == "on") ? "https://" : "http://";
    $url_prefix = $protocol.$hostname;
    return $url_prefix . me();
}


function match_referer($goodreferer = "") {
/// returns true if the referer is the same as the goodreferer.  If
/// goodreferer is not specified, use qualified_me as the goodreferer 
    global $CFG;

    if (empty($CFG->secureforms)) {    // Don't bother checking referer
        return true;
    }

    if ($goodreferer == "nomatch") {   // Don't bother checking referer
        return true;
    }

    if (empty($goodreferer)) { 
        $goodreferer = qualified_me(); 
    }
    return $goodreferer == get_referer();
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

    echo "\n<script language=\"javascript\">";
    echo "\n<!--";
    echo "\ndocument.write('<a title=\"$title\" href=javascript:openpopup(\"$url\",\"$name\",\"$height\",\"$width\") >".addslashes($linkname)."</A>');";
    echo "\n//-->";
    echo "\n</script>";
    echo "\n<noscript>\n<a target=\"$name\" title=\"$title\" href=\"$CFG->wwwroot/$url\">$linkname</a>\n</noscript>\n";

}

function close_window_button() {
/// Prints a simple button to close a window

    echo "<form><center>";
    echo "<input type=button onClick=\"self.close();\" value=\"".get_string("closewindow")."\">";
    echo "</center></form>";
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

    $output = "<select name=$name $javascript>\n";
    if ($nothing) {
        $output .= "   <option value=\"$nothingvalue\"\n";
        if ($nothingvalue == $selected) {
            $output .= " selected";
        }
        $output .= ">$nothing</option>\n";
    }
    if (!empty($options)) {
        foreach ($options as $value => $label) {
            $output .= "   <option value=\"$value\"";
            if ($value == $selected) {
                $output .= " selected";
            }
            if ($label) {
                $output .= ">$label</option>\n";
            } else {
                $output .= ">$value</option>\n";
            }
        }
    }
    $output .= "</select>\n";

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

    global $CFG;

    if ($nothing == "choose") {
        $nothing = get_string("choose")."...";
    }

    $startoutput = "<form target=\"{$CFG->framename}\" name=$formname>";
    $output = "<select name=popup onchange=\"top.location=document.$formname.popup.options[document.$formname.popup.selectedIndex].value\">\n";

    if ($nothing != "") {
        $output .= "   <option value=\"javascript:void(0)\">$nothing</option>\n";
    }

    foreach ($options as $value => $label) {
        if (substr($label,0,1) == "-") {
            $output .= "   <option value=\"\"";
        } else {
            $output .= "   <option value=\"$common$value\"";
            if ($value == $selected) {
                $output .= " selected";
            }
        }
        if ($label) {
            $output .= ">$label</option>\n";
        } else {
            $output .= ">$value</option>\n";
        }
    }
    $output .= "</select>";
    $output .= "</form>\n";

    if ($return) {
        return $startoutput.$output;
    } else {
        echo $startoutput;
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

function detect_munged_arguments($string) {
    if (ereg('\.\.', $string)) { // check for parent URLs
        return true;
    }
    if (ereg('[\|\`]', $string)) {  // check for other bad characters
        return true;
    }
    return false;
}

function get_slash_arguments($file="file.php") {
/// Searches the current environment variables for some slash arguments

    if (!$string = me()) {
        return false;
    }

    $pathinfo = explode($file, $string);
    
    if (!empty($pathinfo[1])) {
        return $pathinfo[1];
    } else {
        return false;
    }
}

function parse_slash_arguments($string, $i=0) {
/// Extracts arguments from "/foo/bar/something"
/// eg http://mysite.com/script.php/foo/bar/something

    if (detect_munged_arguments($string)) {
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
                  FORMAT_HTML   => get_string("formathtml"),
                  FORMAT_PLAIN  => get_string("formatplain"),
                  FORMAT_WIKI   => get_string("formatwiki"));
}

function format_text($text, $format=FORMAT_MOODLE, $options=NULL) {
/// Given text in a variety of format codings, this function returns 
/// the text as safe HTML.
///
/// $text is raw text (originally from a user)
/// $format is one of the format constants, defined above

    switch ($format) {
        case FORMAT_HTML:
            replace_smilies($text);
            return $text;
            break;

        case FORMAT_PLAIN:
            $text = htmlentities($text);
            replace_smilies($text);
            convert_urls_into_links($text);
            $text = nl2br($text);
            return $text;
            break;

        case FORMAT_WIKI:
            $text = wiki_to_html($text);
            replace_smilies($text);
            return $text;
            break;


        default:  // FORMAT_MOODLE or anything else
            if (!isset($options->smiley)) {
                $options->smiley=true;
            }
            if (!isset($options->para)) {
                $options->para=true;
            }
            return text_to_html($text, $options->smiley, $options->para);
            break;
    }
}

function format_text_email($text, $format) {
/// Given text in a variety of format codings, this function returns 
/// the text as plain text suitable for plain email.
///
/// $text is raw text (originally from a user)
/// $format is one of the format constants, defined above

    switch ($format) {

        case FORMAT_PLAIN:
            return $text;
            break;

        case FORMAT_WIKI:
            $text = wiki_to_html($text);
            return strip_tags($text);
            break;

        default:  // FORMAT_MOODLE or anything else
        // Need to add something in here to create a text-friendly way of presenting URLs
            return strip_tags($text);
            break;
    }
}

function clean_text($text, $format) {
/// Given raw text (eg typed in by a user), this function cleans it up 
/// and removes any nasty tags that could mess up Moodle pages.

    global $JAVASCRIPT_TAGS, $ALLOWED_TAGS;

    switch ($format) { 
        case FORMAT_MOODLE:
        case FORMAT_HTML:
        case FORMAT_WIKI:
            $text = strip_tags($text, $ALLOWED_TAGS);
            foreach ($JAVASCRIPT_TAGS as $tag) {
                $text = stri_replace($tag, "", $text);
            }
            return $text;

        case FORMAT_PLAIN:
            return $text;
    }
}

function replace_smilies(&$text) {
/// Replaces all known smileys in the text with image equivalents
    global $CFG;

    static $runonce = false;
    static $e = array();
    static $img = array();
    static $emoticons = array(
        ':-)'  => 'smiley.gif',
        ':)'   => 'smiley.gif',
        ':-D'  => 'biggrin.gif',
        ';-)'  => 'wink.gif',
        ':-/'  => 'mixed.gif',
        'V-.'  => 'thoughtful.gif',
        ':-P'  => 'tongueout.gif',
        'B-)'  => 'cool.gif',
        '^-)'  => 'approve.gif',
        '8-)'  => 'wideeyes.gif',
        ':o)'  => 'clown.gif',
        ':-('  => 'sad.gif',
        ':('   => 'sad.gif',
        '8-.'  => 'shy.gif',
        ':-I'  => 'blush.gif',
        ':-X'  => 'kiss.gif',
        '8-o'  => 'surprise.gif',
        'P-|'  => 'blackeye.gif',
        '8-['  => 'angry.gif',
        'xx-P' => 'dead.gif',
        '|-.'  => 'sleepy.gif',
        '}-]'  => 'evil.gif',
        );

    if ($runonce == false){
        foreach ($emoticons as $emoticon => $image){
            $e[] = $emoticon;
            $img[] = "<img alt=\"$emoticon\" width=15 height=15 src=\"$CFG->wwwroot/pix/s/$image\">";
        }
        $runonce = true;
    }

    $text = str_replace($e, $img, $text);
}

function text_to_html($text, $smiley=true, $para=true) {
/// Given plain text, makes it into HTML as nicely as possible.
/// May contain HTML tags already

/// Remove any whitespace that may be between HTML tags
    $text = eregi_replace(">([[:space:]]+)<", "><", $text);

/// Remove any returns that precede or follow HTML tags
    $text = eregi_replace("([\n\r])<", " <", $text);
    $text = eregi_replace(">([\n\r])", "> ", $text);

    convert_urls_into_links($text);

/// Make returns into HTML newlines.
    $text = nl2br($text);

/// Turn smileys into images.
    if ($smiley) {
        replace_smilies($text);
    }

/// Wrap the whole thing in a paragraph tag if required
    if ($para) {
        return "<p>".$text."</p>";
    } else {
        return $text;
    }
}

function wiki_to_html($text) {
/// Given Wiki formatted text, make it into XHTML using external function

    require_once('wiki.php');

    $wiki = new Wiki;
    return $wiki->format($text);
}

function convert_urls_into_links(&$text) {
/// Given some text, it converts any URLs it finds into HTML links.

/// Make lone URLs into links.   eg http://moodle.com/
    $text = eregi_replace("([[:space:]]|^|\(|\[|\<)([[:alnum:]]+)://([^[:space:]]*)([[:alnum:]#?/&=])",
                          "\\1<a href=\"\\2://\\3\\4\" TARGET=\"newpage\">\\2://\\3\\4</a>", $text);

/// eg www.moodle.com
    $text = eregi_replace("([[:space:]]|^|\(|\[|\<)www\.([^[:space:]]*)([[:alnum:]#?/&=])", 
                          "\\1<a href=\"http://www.\\2\\3\" TARGET=\"newpage\">www.\\2\\3</a>", $text);
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
    global $USER, $CFG, $THEME, $SESSION;

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
            $menu = "<font size=2><a target=\"$CFG->framename\" href=\"$CFG->wwwroot/login/logout.php\">".get_string("logout")."</a></font>";
        } else {
            $menu = "<font size=2><a target=\"$CFG->framename\" href=\"$CFG->wwwroot/login/index.php\">".get_string("login")."</a></font>";
        }
    }

    // Specify character set ... default is iso-8859-1 but some languages might need something else
    // Could be optimised by carrying the charset variable around in $USER
    if (current_language() == "en") {
        $meta = "<meta http-equiv=\"content-type\" content=\"text/html; charset=iso-8859-1\">\n$meta\n";
    } else {
        $meta = "<meta http-equiv=\"content-type\" content=\"text/html; charset=".get_string("thischarset")."\">\n$meta\n";
    }

    if ($CFG->langdir == "RTL") {
        $direction = " dir=\"rtl\"";
    } else {
        $direction = " dir=\"ltr\"";
    }
 
    if (!$cache) {   // Do everything we can to prevent clients and proxies caching
        @header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        @header("Pragma: no-cache");
        $meta .= "\n<meta http-equiv=\"pragma\" content=\"no-cache\">";
        $meta .= "\n<meta http-equiv=\"expires\" content=\"0\">";
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
            $homelink  = "<p align=\"center\"><a title=\"moodle $CFG->release ($CFG->version)\" href=\"http://moodle.org/\" target=\"_blank\">";
            $homelink .= "<br><img width=\"130\" height=\"19\" src=\"pix/madewithmoodle2.gif\" border=0></a></p>";
            $course = get_site();
            $homepage = true;
        } else {
            $homelink = "<a target=\"{$CFG->framename}\" href=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</a>";
        }
    } else {
        $homelink = "<a target=\"{$CFG->framename}\" href=\"$CFG->wwwroot\">".get_string("home")."</a>";
        $course = get_site();
    }

/// User links
    $loggedinas = user_login_string($course, $USER);

    include ("$CFG->dirroot/theme/$CFG->theme/footer.html");
}


function user_login_string($course, $user=NULL) {
    global $USER, $CFG;

    if (!$user) {
        $user = $USER;
    }

    if (isset($user->realuser)) {
        if ($realuser = get_record("user", "id", $user->realuser)) {
            $realuserinfo = " [<a target=\"{$CFG->framename}\" href=\"$CFG->wwwroot/course/loginas.php?id=$course->id&return=$realuser->id\">$realuser->firstname $realuser->lastname</A>] ";
        }
    } else {
        $realuserinfo = "";
    }

    if (isset($user->id) and $user->id) {
        $username = "<a target=\"{$CFG->framename}\" href=\"$CFG->wwwroot/user/view.php?id=$user->id&course=$course->id\">$user->firstname $user->lastname</a>";
        $loggedinas = $realuserinfo.get_string("loggedinas", "moodle", "$username").
                      " (<a target=\"{$CFG->framename}\" href=\"$CFG->wwwroot/login/logout.php\">".get_string("logout")."</a>)";
    } else {
        $loggedinas = get_string("loggedinnot", "moodle").
                      " (<a target=\"{$CFG->framename}\" href=\"$CFG->wwwroot/login/index.php\">".get_string("login")."</a>)";
    }
    return $loggedinas;
}


function print_navigation ($navigation) {
   global $CFG;

   if ($navigation) {
       if (! $site = get_site()) {
           $site->shortname = get_string("home");;
       }
       echo "<a target=\"{$CFG->framename}\" href=\"$CFG->wwwroot/\">$site->shortname</a> -> $navigation";
   }
}

function print_heading($text, $align="center", $size=3) {
    echo "<p align=\"$align\"><font size=\"$size\"><b>".stripslashes_safe($text)."</b></font></p>";
}

function print_heading_with_help($text, $helppage, $module="moodle") {
// Centered heading with attached help button (same title text)
    echo "<p align=\"center\"><font size=\"3\"><b>".stripslashes_safe($text);
    helpbutton($helppage, $text, $module);
    echo "</b></font></p>";
}
    
function print_continue($link) {

    if (!$link) {
        $link = $_SERVER["HTTP_REFERER"];
    }

    print_heading("<a href=\"$link\">".get_string("continue")."</a>");
}


function print_simple_box($message, $align="", $width="", $color="#FFFFFF", $padding=5, $class="generalbox") {
    print_simple_box_start($align, $width, $color, $padding, $class);
    echo stripslashes_safe($message);
    print_simple_box_end();
}

function print_simple_box_start($align="", $width="", $color="#FFFFFF", $padding=5, $class="generalbox") {
    global $THEME;

    if ($align) {
        $align = "align=\"$align\"";
    }
    if ($width) {
        $width = "width=\"$width\"";
    }
    echo "<table $align $width class=\"$class\" border=\"0\" cellpadding=\"$padding\" cellspacing=\"0\"><tr><td bgcolor=\"$color\" class=\"$class"."content\">";
}

function print_simple_box_end() {
    echo "</td></tr></table>";
}

function print_single_button($link, $options, $label="OK", $method="get") {
    echo "<form action=\"$link\" method=\"$method\">";
    if ($options) {
        foreach ($options as $name => $value) {
            echo "<input type=hidden name=\"$name\" value=\"$value\">";
        }
    }
    echo "<input type=submit value=\"$label\"></form>";
}

function print_spacer($height=1, $width=1, $br=true) {
    global $CFG;
    echo "<img height=\"$height\" width=\"$width\" src=\"$CFG->wwwroot/pix/spacer.gif\" alt=\"\">";
    if ($br) {
        echo "<br />\n";
    }
}

function print_file_picture($path, $courseid=0, $height="", $width="", $link="") {
// Given the path to a picture file in a course, or a URL,
// this function includes the picture in the page.
    global $CFG;

    if ($height) {
        $height = "height=\"$height\"";
    }
    if ($width) {
        $width = "width=\"$width\"";
    }
    if ($link) {
        echo "<a href=\"$link\">";
    }
    if (substr(strtolower($path), 0, 7) == "http://") {
        echo "<img border=0 $height $width src=\"$path\">";

    } else if ($courseid) {
        echo "<img border=0 $height $width src=\"";
        if ($CFG->slasharguments) {        // Use this method if possible for better caching
            echo "$CFG->wwwroot/file.php/$courseid/$path";
        } else {
            echo "$CFG->wwwroot/file.php?file=/$courseid/$path";
        }
        echo "\">";
    } else {
        echo "Error: must pass URL or course";
    }
    if ($link) {
        echo "</a>";
    }
}

function print_user_picture($userid, $courseid, $picture, $large=false, $returnstring=false, $link=true) {
    global $CFG;

    if ($link) {
        $output = "<a href=\"$CFG->wwwroot/user/view.php?id=$userid&course=$courseid\">";
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
            $output .= "<img src=\"$CFG->wwwroot/user/pix.php/$userid/$file\" border=0 width=$size height=$size alt=\"\">";
        } else {
            $output .= "<img src=\"$CFG->wwwroot/user/pix.php?file=/$userid/$file\" border=0 width=$size height=$size alt=\"\">";
        }
    } else {
        $output .= "<img src=\"$CFG->wwwroot/user/default/$file\" border=0 width=$size height=$size alt=\"\">";
    }
    if ($link) {
        $output .= "</a>";
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
//     $table->wrap      is an array of "nowrap"s or nothing
//     $table->data[]    is an array of arrays containing the data.
//     $table->width     is an percentage of the page
//     $table->cellpadding    padding on each cell
//     $table->cellspacing    spacing between cells

    if (isset($table->align)) {
        foreach ($table->align as $key => $aa) {
            if ($aa) {
                $align[$key] = " align=\"$aa\"";
            } else {
                $align[$key] = "";
            }
        }
    }
    if (isset($table->size)) {
        foreach ($table->size as $key => $ss) {
            if ($ss) {
                $size[$key] = " width=\"$ss\"";
            } else {
                $size[$key] = "";
            }
        }
    }
    if (isset($table->wrap)) {
        foreach ($table->wrap as $key => $ww) {
            if ($ww) {
                $wrap[$key] = " nowrap ";
            } else {
                $wrap[$key] = "";
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

    print_simple_box_start("center", "$table->width", "#ffffff", 0);
    echo "<table width=100% border=0 valign=top align=center ";
    echo " cellpadding=\"$table->cellpadding\" cellspacing=\"$table->cellspacing\" class=\"generaltable\">\n";

    if (!empty($table->head)) {
        echo "<tr>";
        foreach ($table->head as $key => $heading) {
            if (!isset($size[$key])) {
                $size[$key] = "";
            } 
            if (!isset($align[$key])) {
                $align[$key] = "";
            } 
            echo "<th valign=top ".$align[$key].$size[$key]." nowrap class=\"generaltableheader\">$heading</th>";
        }
        echo "</TR>\n";
    }

    foreach ($table->data as $row) {
        echo "<tr valign=top>";
        foreach ($row as $key => $item) {
            if (!isset($size[$key])) {
                $size[$key] = "";
            } 
            if (!isset($align[$key])) {
                $align[$key] = "";
            } 
            if (!isset($wrap[$key])) {
                $wrap[$key] = "";
            } 
            echo "<td ".$align[$key].$size[$key].$wrap[$key]." class=\"generaltablecell\">$item</td>";
        }
        echo "</tr>\n";
    }
    echo "</table>\n";
    print_simple_box_end();

    return true;
}

function print_editing_switch($courseid) {
    global $CFG, $USER;

    if (isteacher($courseid)) {
        if ($USER->editing) {
            echo "<a href=\"$CFG->wwwroot/course/view.php?id=$courseid&edit=off\">turn editing off</a>";
        } else {
            echo "<a href=\"$CFG->wwwroot/course/view.php?id=$courseid&edit=on\">turn editing on</a>";
        }
    }
}

function print_textarea($richedit, $rows, $cols, $width, $height, $name, $value="") {
/// Prints a richtext field or a normal textarea
    global $CFG, $THEME;

    if ($richedit) {
        echo "<object id=richedit style=\"background-color: buttonface\"";
        echo " data=\"$CFG->wwwroot/lib/rte/richedit.html\"";
        echo " width=\"$width\" height=\"$height\" ";
        echo " type=\"text/x-scriptlet\" VIEWASTEXT></object>\n";
        echo "<textarea style=\"display:none\" name=\"$name\" rows=1 cols=1>";
        p($value);
        echo "</textarea>\n";
    } else {
        echo "<textarea name=\"$name\" rows=\"$rows\" cols=\"$cols\" wrap=virtual>";
        p($value);
        echo "</textarea>\n";
    }
}

function print_richedit_javascript($form, $name, $source="no") {
    echo "<script language=\"javascript\" event=\"onload\" for=\"window\">\n";
    echo "   document.richedit.options = \"history=no;source=$source\";";
    echo "   document.richedit.docHtml = $form.$name.innerText;";
    echo "</script>";
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
        return "<form target=_parent method=get action=\"$CFG->wwwroot/course/view.php\">".
               "<input type=hidden name=id value=\"$courseid\">".
               "<input type=hidden name=edit value=\"$edit\">".
               "<input type=submit value=\"$string\"></form>";
    }
}

function update_module_button($moduleid, $courseid, $string) {
// Prints the editing button on a module "view" page
    global $CFG;

    if (isteacher($courseid)) {
        $string = get_string("updatethis", "", $string);
        return "<form target=_parent method=get action=\"$CFG->wwwroot/course/mod.php\">".
               "<input type=hidden name=update value=\"$moduleid\">".
               "<input type=hidden name=return value=\"true\">".
               "<input type=submit value=\"$string\"></form>";
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
        //Only add visible or teacher mods to jumpmenu
        if ($mod->visible or isteacher($course->id)) {
            $url = "$mod->mod/view.php?id=$mod->cm";
            if ($cm == $mod->cm) {
                $selected = $url;
            }
            $mod->name = urldecode($mod->name);
            if (strlen($mod->name) > 55) {
                $mod->name = substr($mod->name, 0, 50)."...";
            }
            if (!$mod->visible) {
                $mod->name = "(".$mod->name.")";
            }
            $menu[$url] = $mod->name; 
        }
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
    global $CFG, $THEME;

    if ($module == "") {
        $module = "moodle";
    }

    if (empty($THEME->custompix)) {
        $icon = "$CFG->wwwroot/pix/help.gif";
    } else {
        $icon = "$CFG->wwwroot/theme/$CFG->theme/pix/help.gif";
    }

    if ($image) {
        if ($linktext) {
            $linkobject = "$title<img align=\"absmiddle\" border=0 height=17 width=22 alt=\"\" src=\"$icon\">";
        } else {
            $linkobject = "<img align=\"absmiddle\" border=0 height=17 width=22 alt=\"$title\" src=\"$icon\">";
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

function emoticonhelpbutton($form, $field) {
/// Prints a special help button that is a link to the "live" emoticon popup
    global $CFG, $SESSION;

    $SESSION->inserttextform = $form;
    $SESSION->inserttextfield = $field;
    helpbutton("emoticons", get_string("helpemoticons"), "moodle", false, true);
    echo "&nbsp;<img src=\"$CFG->wwwroot/pix/s/smiley.gif\" align=\"absmiddle\" width=15 height=15></a>";
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

    echo "<br />";
    print_simple_box($message, "center", "50%", "$THEME->cellheading", "20", "noticebox");
    print_heading("<a href=\"$link\">".get_string("continue")."</a>");
    print_footer(get_site());
    die;
}

function notice_yesno ($message, $linkyes, $linkno) {
    global $THEME;

    print_simple_box_start("center", "60%", "$THEME->cellheading");
    echo "<p align=center><font size=3>$message</font></p>";
    echo "<p align=center><font size=3><b>";
    echo "<a href=\"$linkyes\">".get_string("yes")."</a>";
    echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
    echo "<a href=\"$linkno\">".get_string("no")."</a>";
    echo "</b></font></p>";
    print_simple_box_end();
}

function redirect($url, $message="", $delay=0) {
// Uses META tags to redirect the user, after printing a notice

    if (empty($message)) {
        echo "<meta http-equiv='refresh' content='$delay; url=$url'>";
    } else {
        if (! $delay) {  
            $delay = 3;  // There's no point having a message with no delay
        }
        echo "<meta http-equiv='refresh' content='$delay; url=$url'>";
        print_header();
        echo "<center>";
        echo "<p>$message</p>";
        echo "<p>( <a href=\"$url\">".get_string("continue")."</a> )</p>";
        echo "</center>";
    }
    die; 
}

function notify ($message, $color="red", $align="center") {
    echo "<p align=\"$align\"><b><font color=\"$color\">$message</font></b></p>\n";
}


// vim:autoindent:expandtab:shiftwidth=4:tabstop=4:tw=140:
?>
