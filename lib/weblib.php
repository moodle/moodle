<?php

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

/**
 * Library of functions for web output
 *
 * Library of all general-purpose Moodle PHP functions and constants
 * that produce HTML output
 *
 * Other main libraries:
 * - datalib.php - functions that access the database.
 * - moodlelib.php - general-purpose Moodle functions.
 * @author Martin Dougiamas
 * @version  $Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package moodlecore
 */
 
/// Constants

/// Define text formatting types ... eventually we can add Wiki, BBcode etc

/**
 * Does all sorts of transformations and filtering
 */
define('FORMAT_MOODLE',   '0');   // Does all sorts of transformations and filtering

/**
 * Plain HTML (with some tags stripped)
 */
define('FORMAT_HTML',     '1');   // Plain HTML (with some tags stripped)

/**
 * Plain text (even tags are printed in full)
 */
define('FORMAT_PLAIN',    '2');   // Plain text (even tags are printed in full)

/**
 * Wiki-formatted text
 */
define('FORMAT_WIKI',     '3');   // Wiki-formatted text

/**
 * Markdown-formatted text http://daringfireball.net/projects/markdown/
 */
define('FORMAT_MARKDOWN', '4');   // Markdown-formatted text http://daringfireball.net/projects/markdown/


/**
 * Allowed tags - string of html tags that can be tested against for safe html tags
 * @global string $ALLOWED_TAGS
 */
$ALLOWED_TAGS =
'<p><br><b><i><u><font><table><tbody><span><div><tr><td><th><ol><ul><dl><li><dt><dd><h1><h2><h3><h4><h5><h6><hr><img><a><strong><emphasis><em><sup><sub><address><cite><blockquote><pre><strike><embed><object><param><acronym><nolink><style><lang><tex><algebra><math><mi><mn><mo><mtext><mspace><ms><mrow><mfrac><msqrt><mroot><mstyle><merror><mpadded><mphantom><mfenced><msub><msup><msubsup><munder><mover><munderover><mmultiscripts><mtable><mtr><mtd><maligngroup><malignmark><maction><cn><ci><apply><reln><fn><interval><inverse><sep><condition><declare><lambda><compose><ident><quotient><exp><factorial><divide><max><min><minus><plus><power><rem><times><root><gcd><and><or><xor><not><implies><forall><exists><abs><conjugate><eq><neq><gt><lt><geq><leq><ln><log><int><diff><partialdiff><lowlimit><uplimit><bvar><degree><set><list><union><intersect><in><notin><subset><prsubset><notsubset><notprsubset><setdiff><sum><product><limit><tendsto><mean><sdev><variance><median><mode><moment><vector><matrix><matrixrow><determinant><transpose><selector><annotation><semantics><annotation-xml><tt><code>';

/**
 * Allowed protocols - array of protocols that are safe to use in links and so on
 * @global string $ALLOWED_PROTOCOLS
 */
$ALLOWED_PROTOCOLS = array('http', 'https', 'ftp', 'news', 'mailto', 'rtsp', 'teamspeak', 'gopher', 'color');


/// Functions

/**
 * Add quotes to HTML characters
 *
 * Returns $var with HTML characters (like "<", ">", etc.) properly quoted.
 * This function is very similar to {@link p()}
 *
 * @param string $var the string potentially containing HTML characters
 * @return string
 */
function s($var) {

    if (empty($var)) {
        return '';
    }
    return htmlSpecialChars(stripslashes_safe($var));
}

/**
 * Add quotes to HTML characters
 *
 * Prints $var with HTML characters (like "<", ">", etc.) properly quoted.
 * This function is very similar to {@link s()}
 *
 * @param string $var the string potentially containing HTML characters
 * @return string
 */
function p($var) {

    if (empty($var)) {
        echo '';
    }
    echo htmlSpecialChars(stripslashes_safe($var));
}


/**
 * Ensure that a variable is set
 *
 * Return $var if it is defined, otherwise return $default, 
 * This function is very similar to {@link optional_variable()}
 *
 * @param    mixed $var the variable which may be unset
 * @param    mixed $default the value to return if $var is unset
 * @return   mixed
 */
function nvl(&$var, $default='') {

    return isset($var) ? $var : $default;
}

/**
 * Remove query string from url
 *
 * Takes in a URL and returns it without the querystring portion
 *
 * @param string $url the url which may have a query string attached
 * @return string
 */
 function strip_querystring($url) {

    if ($commapos = strpos($url, '?')) {
        return substr($url, 0, $commapos);
    } else {
        return $url;
    }
}

/**
 * Returns the URL of the HTTP_REFERER, less the querystring portion
 * @return string
 */
function get_referer() {

    return strip_querystring(nvl($_SERVER['HTTP_REFERER']));
}


/**
 * Returns the name of the current script, WITH the querystring portion.
 * this function is necessary because PHP_SELF and REQUEST_URI and SCRIPT_NAME
 * return different things depending on a lot of things like your OS, Web
 * server, and the way PHP is compiled (ie. as a CGI, module, ISAPI, etc.)
 * <b>NOTE:</b> This function returns false if the global variables needed are not set.
 *
 * @return string
 */
 function me() {

    if (!empty($_SERVER['REQUEST_URI'])) {
        return $_SERVER['REQUEST_URI'];

    } else if (!empty($_SERVER['PHP_SELF'])) {
        if (!empty($_SERVER['QUERY_STRING'])) {
            return $_SERVER['PHP_SELF'] .'?'. $_SERVER['QUERY_STRING'];
        }
        return $_SERVER['PHP_SELF'];

    } else if (!empty($_SERVER['SCRIPT_NAME'])) {
        if (!empty($_SERVER['QUERY_STRING'])) {
            return $_SERVER['SCRIPT_NAME'] .'?'. $_SERVER['QUERY_STRING'];
        }
        return $_SERVER['SCRIPT_NAME'];

    } else if (!empty($_SERVER['URL'])) {     // May help IIS (not well tested)
        if (!empty($_SERVER['QUERY_STRING'])) {
            return $_SERVER['URL'] .'?'. $_SERVER['QUERY_STRING'];
        }
        return $_SERVER['URL'];

    } else {
        notify('Warning: Could not find any of these web server variables: $REQUEST_URI, $PHP_SELF, $SCRIPT_NAME or $URL');
        return false;
    }
}

/**
 * Like {@link me()} but returns a full URL
 * @see me()
 * @return string
 */
function qualified_me() {

    if (!empty($_SERVER['SERVER_NAME'])) {
        $hostname = $_SERVER['SERVER_NAME'];
    } else if (!empty($_ENV['SERVER_NAME'])) {
        $hostname = $_ENV['SERVER_NAME'];
    } else if (!empty($_SERVER['HTTP_HOST'])) {
        $hostname = $_SERVER['HTTP_HOST'];
    } else if (!empty($_ENV['HTTP_HOST'])) {
        $hostname = $_ENV['HTTP_HOST'];
    } else {
        notify('Warning: could not find the name of this server!');
        return false;
    }
    if (isset($_SERVER['HTTPS'])) {
        $protocol = ($_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://';
    } else if (isset($_SERVER['SERVER_PORT'])) { # Apache2 does not export $_SERVER['HTTPS']
        $protocol = ($_SERVER['SERVER_PORT'] == '443') ? 'https://' : 'http://';
    } else {
        $protocol = 'http://';
    }

    $url_prefix = $protocol.$hostname;
    return $url_prefix . me();
}

/**
 * Determine if a web referer is valid
 *
 * Returns true if the referer is the same as the goodreferer. If
 * the referer to test is not specified, use {@link qualified_me()}.
 * If the admin has not set secure forms ($CFG->secureforms) then
 * this function returns true regardless of a match.
 *
 * @uses $CFG
 * @param string $goodreferer the url to compare to referer
 * @return boolean
 */
function match_referer($goodreferer = '') {
    global $CFG;

    if (empty($CFG->secureforms)) {    // Don't bother checking referer
        return true;
    }

    if ($goodreferer == 'nomatch') {   // Don't bother checking referer
        return true;
    }

    if (empty($goodreferer)) {
        $goodreferer = qualified_me();
    }

    $referer = get_referer();

    return (($referer == $goodreferer) or ($referer == $CFG->wwwroot .'/'));
}

/**
 * Determine if there is data waiting to be processed from a form
 *
 * Used on most forms in Moodle to check for data
 * Returns the data as an object, if it's found.
 * This object can be used in foreach loops without
 * casting because it's cast to (array) automatically
 * 
 * Checks that submitted POST data exists, and also
 * checks the referer against the given url (it uses
 * the current page if none was specified.
 *
 * @uses $CFG
 * @param string $url the url to compare to referer for secure forms
 * @return boolean
 */
function data_submitted($url='') {


    global $CFG;

    if (empty($_POST)) {
        return false;

    } else {
        if (match_referer($url)) {
            return (object)$_POST;
        } else {
            if ($CFG->debug > 10) {
                notice('The form did not come from this page! (referer = '. get_referer() .')');
            }
            return false;
        }
    }
}

/**
 * Moodle replacement for php stripslashes() function
 *
 * The standard php stripslashes() removes ALL backslashes 
 * even from strings - so  C:\temp becomes C:temp - this isn't good.
 * This function should work as a fairly safe replacement
 * to be called on quoted AND unquoted strings (to be sure)
 *
 * @param string the string to remove unsafe slashes from
 * @return string
 */
function stripslashes_safe($string) {

    $string = str_replace("\\'", "'", $string);
    $string = str_replace('\\"', '"', $string);
    //$string = str_replace('\\\\', '\\', $string);  // why?
    return $string;
}

/**
 * Given some normal text this function will break up any
 * long words to a given size by inserting the given character
 *
 * @param string $string the string to be modified
 * @param int $maxsize maximum length of the string to be returned
 * @param string $cutchar the string used to represent word breaks
 * @return string
 */
function break_up_long_words($string, $maxsize=20, $cutchar=' ') {

    if (in_array(current_language(), array('ja', 'zh_cn', 'zh_tw', 'zh_tw_utf8'))) {  // Multibyte languages
        return $string;
    }

    $output = '';
    $length = strlen($string);
    $wordlength = 0;

    for ($i=0; $i<$length; $i++) {
        $char = $string[$i];
        if ($char == ' ' or $char == "\t" or $char == "\n" or $char == "\r") {
            $wordlength = 0;
        } else {
            $wordlength++;
            if ($wordlength > $maxsize) {
                $output .= $cutchar;
                $wordlength = 0;
            }
        }
        $output .= $char;
    }
    return $output;
}

/**
 * This does a search and replace, ignoring case
 * This function is only used for versions of PHP older than version 5
 * which do not have a native version of this function.
 * Taken from the PHP manual, by bradhuizenga @ softhome.net
 *
 * @param string $find the string to search for
 * @param string $replace the string to replace $find with
 * @param string $string the string to search through
 * return string
 */
if (!function_exists('str_ireplace')) {    /// Only exists in PHP 5
    function str_ireplace($find, $replace, $string) {

        if (!is_array($find)) {
            $find = array($find);
        }

        if(!is_array($replace)) {
            if (!is_array($find)) {
                $replace = array($replace);
            } else {
                // this will duplicate the string into an array the size of $find
                $c = count($find);
                $rString = $replace;
                unset($replace);
                for ($i = 0; $i < $c; $i++) {
                    $replace[$i] = $rString;
                }
            }
        }

        foreach ($find as $fKey => $fItem) {
            $between = explode(strtolower($fItem),strtolower($string));
            $pos = 0;
            foreach($between as $bKey => $bItem) {
                $between[$bKey] = substr($string,$pos,strlen($bItem));
                $pos += strlen($bItem) + strlen($fItem);
            }
            $string = implode($replace[$fKey],$between);
        }
        return ($string);
    }
}

/**
 * Locate the position of a string in another string
 *
 * This function is only used for versions of PHP older than version 5
 * which do not have a native version of this function.
 * Taken from the PHP manual, by dmarsh @ spscc.ctc.edu
 *
 * @param string $haystack The string to be searched
 * @param string $needle The string to search for
 * @param int $offset The position in $haystack where the search should begin.
 */
if (!function_exists('stripos')) {    /// Only exists in PHP 5
    function stripos($haystack, $needle, $offset=0) {

        return strpos(strtoupper($haystack), strtoupper($needle), $offset);
    }
}

/**
 * Load a template from file
 *
 * Returns a (big) string containing the contents of a template file with all
 * the variables interpolated.  all the variables must be in the $var[] array or
 * object (whatever you decide to use).
 *
 * <b>WARNING: do not use this on big files!!</b>
 *
 * @param string $filename Location on the server's filesystem where template can be found.
 * @param mixed $var Passed in by reference. An array or object which will be loaded with data from the template file.
 */
function read_template($filename, &$var) {

    $temp = str_replace("\\", "\\\\", implode(file($filename), ''));
    $temp = str_replace('"', '\"', $temp);
    eval("\$template = \"$temp\";");
    return $template;
}

/**
 * Set a variable's value depending on whether or not it already has a value.
 *
 * If variable is set, set it to the set_value otherwise set it to the 
 * unset_value.  used to handle checkboxes when you are expecting them from
 * a form
 *
 * @param mixed $var Passed in by reference. The variable to check.
 * @param mixed $set_value The value to set $var to if $var already has a value.
 * @param mixed $unset_value The value to set $var to if $var does not already have a value.
 */
function checked(&$var, $set_value = 1, $unset_value = 0) {

    if (empty($var)) {
        $var = $unset_value;
    } else {
        $var = $set_value;
    }
}

/**
 * Prints the word "checked" if a variable is true, otherwise prints nothing,
 * used for printing the word "checked" in a checkbox form element.
 *
 * @param boolean $var Variable to be checked for true value
 * @param string $true_value Value to be printed if $var is true
 * @param string $false_value Value to be printed if $var is false
 */
function frmchecked(&$var, $true_value = 'checked', $false_value = '') {

    if ($var) {
        echo $true_value;
    } else {
        echo $false_value;
    }
}

/**
 * This function will create a HTML link that will work on both
 * Javascript and non-javascript browsers.
 * Relies on the Javascript function openpopup in javascript.php
 *
 * $url must be relative to home page  eg /mod/survey/stuff.php
 * @param string $url Web link relative to home page
 * @param string $name Name to be assigned to the popup window
 * @param string $linkname Text to be displayed as web link
 * @param int $height Height to assign to popup window
 * @param int $width Height to assign to popup window
 * @param string $title Text to be displayed as popup page title
 * @param string $options List of additional options for popup window
 * @todo Add code examples and list of some options that might be used.
 * @param boolean $return Should the link to the popup window be returned as a string (true) or printed immediately (false)?
 * @return string
 * @uses $CFG
 */
function link_to_popup_window ($url, $name='popup', $linkname='click here',
                               $height=400, $width=500, $title='Popup window', $options='none', $return=false) {

    global $CFG;

    if ($options == 'none') {
        $options = 'menubar=0,location=0,scrollbars,resizable,width='. $width .',height='. $height;
    }
    $fullscreen = 0;

    if (!(strpos($url,$CFG->wwwroot) === false)) { // some log url entries contain _SERVER[HTTP_REFERRER] in which case wwwroot is already there.
        $url = substr($url, strlen($CFG->wwwroot)+1);
    }

    $link = '<a target="'. $name .'" title="'. $title .'" href="'. $CFG->wwwroot . $url .'" '.
           "onclick=\"return openpopup('$url', '$name', '$options', $fullscreen);\">$linkname</a>\n";
    if ($return) {
        return $link;
    } else {
        echo $link;
    }
}

/**
 * This function will print a button submit form element
 * that will work on both Javascript and non-javascript browsers.
 * Relies on the Javascript function openpopup in javascript.php
 *
 * $url must be relative to home page  eg /mod/survey/stuff.php
 * @param string $url Web link relative to home page
 * @param string $name Name to be assigned to the popup window
 * @param string $linkname Text to be displayed as web link
 * @param int $height Height to assign to popup window
 * @param int $width Height to assign to popup window
 * @param string $title Text to be displayed as popup page title
 * @param string $options List of additional options for popup window
 * @todo Add code examples and list of some options that might be used.
 * @return string
 * @uses $CFG
 */
function button_to_popup_window ($url, $name='popup', $linkname='click here',
                                 $height=400, $width=500, $title='Popup window', $options='none') {

    global $CFG;

    if ($options == 'none') {
        $options = 'menubar=0,location=0,scrollbars,resizable,width='. $width .',height='. $height;
    }
    $fullscreen = 0;

    echo '<input type="button" name="popupwindow" title="'. $title .'" value="'. $linkname .' ..." '.
         "onClick=\"return openpopup('$url', '$name', '$options', $fullscreen);\" />\n";
}


/**
 * Prints a simple button to close a window
 */
function close_window_button() {

    echo '<center>' . "\n";
    echo '<script type="text/javascript">' . "\n";
    echo '<!--' . "\n";
    echo "document.write('<form>');\n";
    echo "document.write('<input type=\"button\" onClick=\"self.close();\" value=\"".get_string("closewindow")."\" />');\n";
    echo "document.write('</form>');\n";
    echo '-->' . "\n";
    echo '</script>' . "\n";
    echo '<noscript>' . "\n";
    echo '<a href="'. $_SERVER['HTTP_REFERER'] .'"><---</a>' . "\n";
    echo '</noscript>' . "\n";
    echo '</center>' . "\n";
}

/**
 * Given an array of value, creates a popup menu to be part of a form
 * $options["value"]["label"]
 *
 * @param    type description
 * @todo Finish documenting this function
 */
function choose_from_menu ($options, $name, $selected='', $nothing='choose', $script='', $nothingvalue='0', $return=false) {

    if ($nothing == 'choose') {
        $nothing = get_string('choose') .'...';
    }

    if ($script) {
        $javascript = 'onchange="'. $script .'"';
    } else {
        $javascript = '';
    }

    $output = '<select name="'. $name .'" '. $javascript .'>' . "\n";
    if ($nothing) {
        $output .= '   <option value="'. $nothingvalue .'"'. "\n";
        if ($nothingvalue === $selected) {
            $output .= ' selected="selected"';
        }
        $output .= '>'. $nothing .'</option>' . "\n";
    }
    if (!empty($options)) {
        foreach ($options as $value => $label) {
            $output .= '   <option value="'. $value .'"';
            if ($value == $selected) {
                $output .= ' selected="selected"';
            }
            if ($label === '') {
                $output .= '>'. $value .'</option>' . "\n";
            } else {
                $output .= '>'. $label .'</option>' . "\n";
            }
        }
    }
    $output .= '</select>' . "\n";

    if ($return) {
        return $output;
    } else {
        echo $output;
    }
}

/**
 * Implements a complete little popup form
 *
 * @uses $CFG
 * @param string $common  The URL up to the point of the variable that changes
 * @param array $options  Alist of value-label pairs for the popup list
 * @param string $formname Name must be unique on the page
 * @param string $selected The option that is already selected
 * @param string $nothing The label for the "no choice" option
 * @param string $help The name of a help page if help is required
 * @param string $helptext The name of the label for the help button
 * @param boolean $return Indicates whether the function should return the text
 *         as a string or echo it directly to the page being rendered
 * @param string $targetwindow The name of the target page to open the linked page in. 
 * @return string If $return is true then the entire form is returned as a string.
 * @todo Finish documenting this function<br>
 */
function popup_form($common, $options, $formname, $selected='', $nothing='choose', $help='', $helptext='', $return=false, $targetwindow='self') {

    global $CFG; 
    static $go, $choose;   /// Locally cached, in case there's lots on a page

    if (empty($options)) {
        return '';
    }

    if (!isset($go)) {
        $go = get_string('go');
    }

    if ($nothing == 'choose') {
        if (!isset($choose)) {
            $choose = get_string('choose');
        }
        $nothing = $choose.'...';
    }

    $startoutput = '<form action="'.$CFG->wwwroot.'/course/jumpto.php"'.
                        ' method="get"'.
                        ' target="'.$CFG->framename.'"'.
                        ' name="'.$formname.'"'.
                        ' style="display: inline;">';

    $output = '<select name="jump" onchange="'.$targetwindow.'.location=document.'.$formname.
                       '.jump.options[document.'.$formname.'.jump.selectedIndex].value;">'."\n";

    if ($nothing != '') {
        $output .= "   <option value=\"javascript:void(0)\">$nothing</option>\n";
    }

    $inoptgroup = false;
    foreach ($options as $value => $label) {
        if (substr($label,0,2) == '--') {
            if ($inoptgroup) {
                $output .= '   </optgroup>';
            } else {
                $inoptgroup = true;
            }
            $output .= '   <optgroup label="'. substr($label,2) .'">';   // Plain labels
            continue;
        } else {
            $output .= '   <option value="'. $common . $value .'"';
            if ($value == $selected) {
                $output .= ' selected="selected"';
            }
        }
        if ($label) {
            $output .= '>'. $label .'</option>' . "\n";
        } else {
            $output .= '>'. $value .'</option>' . "\n";
        }
    }
    if ($inoptgroup) {
        $output .= '    </optgroup>';
    }
    $output .= '</select>';
    $output .= '<noscript id="noscript'.$formname.'" style="display: inline;">';
    $output .= '<input type="submit" value="'.$go.'" /></noscript>';
    $output .= '<script type="text/javascript">'.
               "\n<!--\n".
               'document.getElementById("noscript'.$formname.'").style.display = "none";'.
               "\n-->\n".'</script>';
    $output .= '</form>' . "\n";

    if ($help) {
        $button = helpbutton($help, $helptext, 'moodle', true, false, '', true);
    } else {
        $button = '';
    }

    if ($return) {
        return $startoutput.$button.$output;
    } else {
        echo $startoutput.$button.$output;
    }
}


/**
 * Prints some red text
 *
 * @param string $error The text to be displayed in red
 */
function formerr($error) {

    if (!empty($error)) {
        echo '<font color="#ff0000">'. $error .'</font>';
    }
}

/**
 * Validates an email to make sure it makes sense.
 *
 * @param string $address The email address to validate.
 * @return boolean
 */
function validate_email($address) {

    return (ereg('^[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+'.
                  '@'.
                  '[-!#$%&\'*+\\/0-9=?A-Z^_`a-z{|}~]+\.'.
                  '[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+$',
                  $address));
}

/**
 * Check for bad characters ?
 *
 * @param string $string ?
 * @param int $allowdots ?
 * @todo Finish documenting this function - more detail needed in description as well as details on arguments
 */
function detect_munged_arguments($string, $allowdots=1) {
    if (substr_count($string, '..') > $allowdots) {   // Sometimes we allow dots in references
        return true;
    }
    if (ereg('[\|\`]', $string)) {  // check for other bad characters
        return true;
    }
    if (empty($string) or $string == '/') {
        return true;
    }

    return false;
}

/**
 * Searches the current environment variables for some slash arguments
 *
 * @param string $file ?
 * @todo Finish documenting this function
 */
function get_slash_arguments($file='file.php') {

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

/**
 * Extracts arguments from "/foo/bar/something"
 * eg http://mysite.com/script.php/foo/bar/something
 *
 * @param string $string ?
 * @param int $i ?
 * @return array|string
 * @todo Finish documenting this function
 */
function parse_slash_arguments($string, $i=0) {

    if (detect_munged_arguments($string)) {
        return false;
    }
    $args = explode('/', $string);

    if ($i) {     // return just the required argument
        return $args[$i];

    } else {      // return the whole array
        array_shift($args);  // get rid of the empty first one
        return $args;
    }
}

/**
 * Just returns an array of text formats suitable for a popup menu
 *
 * @uses FORMAT_MOODLE
 * @uses FORMAT_HTML
 * @uses FORMAT_PLAIN
 * @uses FORMAT_WIKI
 * @uses FORMAT_MARKDOWN
 * @return array
 */
function format_text_menu() {

    return array (FORMAT_MOODLE => get_string('formattext'),
                  FORMAT_HTML   => get_string('formathtml'),
                  FORMAT_PLAIN  => get_string('formatplain'),
                  FORMAT_WIKI   => get_string('formatwiki'),
                  FORMAT_MARKDOWN  => get_string('formatmarkdown'));
}

/**
 * Given text in a variety of format codings, this function returns
 * the text as safe HTML. 
 *
 * @uses $CFG
 * @uses FORMAT_MOODLE
 * @uses FORMAT_HTML
 * @uses FORMAT_PLAIN
 * @uses FORMAT_WIKI
 * @uses FORMAT_MARKDOWN
 * @param string $text The text to be formatted. This is raw text originally from user input.
 * @param int $format Identifier of the text format to be used 
 *            (FORMAT_MOODLE, FORMAT_HTML, FORMAT_PLAIN, FORMAT_WIKI, FORMAT_MARKDOWN)
 * @param  array $options ?
 * @param int $courseid ?
 * @return string
 * @todo Finish documenting this function
 */
function format_text($text, $format=FORMAT_MOODLE, $options=NULL, $courseid=NULL ) {

    global $CFG, $course;

    if (!empty($CFG->cachetext)) {
        $time = time() - $CFG->cachetext;
        $md5key = md5($text);
        if ($cacheitem = get_record_select('cache_text', "md5key = '$md5key' AND timemodified > '$time'")) {
            return $cacheitem->formattedtext;
        }
    }

    if (empty($courseid)) {
        if (!empty($course->id)) {         // An ugly hack for better compatibility
            $courseid = $course->id;
        }
    }

    $CFG->currenttextiscacheable = true;   // Default status - can be changed by any filter

    switch ($format) {
        case FORMAT_HTML:
            replace_smilies($text);
            if (!isset($options->noclean)) {
                $text = clean_text($text, $format);
            }
            $text = filter_text($text, $courseid);
            break;

        case FORMAT_PLAIN:
            $text = htmlentities($text);
            $text = rebuildnolinktag($text);
            $text = str_replace('  ', '&nbsp; ', $text);
            $text = nl2br($text);
            break;

        case FORMAT_WIKI:
            $text = wiki_to_html($text,$courseid);
            $text = rebuildnolinktag($text);
            if (!isset($options->noclean)) {
                $text = clean_text($text, $format);
            }
            $text = filter_text($text, $courseid);
            break;

        case FORMAT_MARKDOWN:
            $text = markdown_to_html($text);
            if (!isset($options->noclean)) {
                $text = clean_text($text, $format);
            }
            replace_smilies($text);
            $text = filter_text($text, $courseid);
            break;

        default:  // FORMAT_MOODLE or anything else
            if (!isset($options->smiley)) {
                $options->smiley=true;
            }
            if (!isset($options->para)) {
                $options->para=true;
            }
            if (!isset($options->newlines)) {
                $options->newlines=true;
            }
            $text = text_to_html($text, $options->smiley, $options->para, $options->newlines);
            if (!isset($options->noclean)) {
                $text = clean_text($text, $format);
            }
            $text = filter_text($text, $courseid);
            break;
    }

    if (!empty($CFG->cachetext) and $CFG->currenttextiscacheable) {
        $newrecord->md5key = $md5key;
        $newrecord->formattedtext = addslashes($text);
        $newrecord->timemodified = time();
        @insert_record('cache_text', $newrecord);
    }

    return $text;
}

/**
 * Given text in a variety of format codings, this function returns
 * the text as plain text suitable for plain email.
 *
 * @uses FORMAT_MOODLE
 * @uses FORMAT_HTML
 * @uses FORMAT_PLAIN
 * @uses FORMAT_WIKI
 * @uses FORMAT_MARKDOWN
 * @param string $text The text to be formatted. This is raw text originally from user input.
 * @param int $format Identifier of the text format to be used 
 *            (FORMAT_MOODLE, FORMAT_HTML, FORMAT_PLAIN, FORMAT_WIKI, FORMAT_MARKDOWN)
 * @return string
 */
function format_text_email($text, $format) {

    switch ($format) {

        case FORMAT_PLAIN:
            return $text;
            break;

        case FORMAT_WIKI:
            $text = wiki_to_html($text);
        /// This expression turns links into something nice in a text format. (Russell Jungwirth)
        /// From: http://php.net/manual/en/function.eregi-replace.php and simplified
            $text = eregi_replace('(<a [^<]*href=["|\']?([^ "\']*)["|\']?[^>]*>([^<]*)</a>)','\\3 [ \\2 ]', $text);
            return strtr(strip_tags($text), array_flip(get_html_translation_table(HTML_ENTITIES)));
            break;

        case FORMAT_HTML:
            return html_to_text($text);
            break;

        case FORMAT_MOODLE:
        case FORMAT_MARKDOWN:
        default:
            $text = eregi_replace('(<a [^<]*href=["|\']?([^ "\']*)["|\']?[^>]*>([^<]*)</a>)','\\3 [ \\2 ]', $text);
            return strtr(strip_tags($text), array_flip(get_html_translation_table(HTML_ENTITIES)));
            break;
    }
}

/**
 * Given some text in HTML format, this function will pass it
 * through any filters that have been defined in $CFG->textfilterx
 * The variable defines a filepath to a file containing the
 * filter function.  The file must contain a variable called
 * $textfilter_function which contains the name of the function
 * with $courseid and $text parameters
 *
 * @param string $text The text to be passed through format filters
 * @param int $courseid ?
 * @return string
 * @todo Finish documenting this function
 */
function filter_text($text, $courseid=NULL) {

    global $CFG;

    if (!empty($CFG->textfilters)) {
        $textfilters = explode(',', $CFG->textfilters);
        foreach ($textfilters as $textfilter) {
            if (is_readable($CFG->dirroot .'/'. $textfilter .'/filter.php')) {
                include_once($CFG->dirroot .'/'. $textfilter .'/filter.php');
                $functionname = basename($textfilter).'_filter';
                if (function_exists($functionname)) {
                    $text = $functionname($courseid, $text);
                }
            }
        }
    }

    return $text;
}

/**
 * Given raw text (eg typed in by a user), this function cleans it up
 * and removes any nasty tags that could mess up Moodle pages.
 *
 * @uses FORMAT_MOODLE
 * @uses FORMAT_PLAIN
 * @uses ALLOWED_TAGS
 * @param string $text The text to be cleaned
 * @param int $format Identifier of the text format to be used 
 *            (FORMAT_MOODLE, FORMAT_HTML, FORMAT_PLAIN, FORMAT_WIKI, FORMAT_MARKDOWN)
 * @return string The cleaned up text
 */
function clean_text($text, $format=FORMAT_MOODLE) {

    global $ALLOWED_TAGS;

    switch ($format) {
        case FORMAT_PLAIN:
            return $text;

        default:

        /// Remove tags that are not allowed
            $text = strip_tags($text, $ALLOWED_TAGS);

        /// Remove script events
            $text = eregi_replace("([^a-z])language([[:space:]]*)=", "\\1Xlanguage=", $text);
            $text = eregi_replace("([^a-z])on([a-z]+)([[:space:]]*)=", "\\1Xon\\2=", $text);

        /// Clean up embedded scripts and , using kses
            $text = cleanAttributes($text);

            return $text;
    }
}

/**
 * This function takes a string and examines it for HTML tags.
 * If tags are detected it passes the string to a helper function {@link cleanAttributes2()}
 *  which checks for attributes and filters them for malicious content
 *         17/08/2004              ::          Eamon DOT Costello AT dcu DOT ie
 *
 * @param string $str The string to be examined for html tags
 * @return string
 */
function cleanAttributes($str){
    $result = preg_replace(
            '%(<[^>]*(>|$)|>)%me', #search for html tags
            "cleanAttributes2('\\1')",
            $str
            );
    return  $result;
}

/**
 * This function takes a string with an html tag and strips out any unallowed
 * protocols e.g. javascript:
 * It calls ancillary functions in kses which are prefixed by kses
*        17/08/2004              ::          Eamon DOT Costello AT dcu DOT ie
 *
 * @param string $htmlTag An html tag to be examined
 * @return string
 */
function cleanAttributes2($htmlTag){

    global $CFG, $ALLOWED_PROTOCOLS;
    require_once($CFG->libdir .'/kses.php');

    $htmlTag = kses_stripslashes($htmlTag);
    if (substr($htmlTag, 0, 1) != '<') {
        return '&gt;';  //a single character ">" detected
    }
    if (!preg_match('%^<\s*(/\s*)?([a-zA-Z0-9]+)([^>]*)>?$%', $htmlTag, $matches)) {
        return ''; // It's seriously malformed
    }
    $slash = trim($matches[1]); //trailing xhtml slash
    $elem = $matches[2];    //the element name
    $attrlist = $matches[3]; // the list of attributes as a string

    $attrArray = kses_hair($attrlist, $ALLOWED_PROTOCOLS);

    $attStr = '';
    foreach ($attrArray as $arreach) {
        $attStr .=  ' '.strtolower($arreach['name']).'="'.$arreach['value'].'" ';
    }
    $xhtml_slash = '';
    if (preg_match('%/\s*$%', $attrlist)) {
        $xhtml_slash = ' /';
    }
    return '<'. $slash . $elem . $attStr . $xhtml_slash .'>';
}

/**
 * Replaces all known smileys in the text with image equivalents
 *
 * @uses $CFG
 * @param string $text Passed by reference. The string to search for smily strings.
 * @return string
 */
function replace_smilies(&$text) {
/// 
    global $CFG;

/// this builds the mapping array only once
    static $runonce = false;
    static $e = array();
    static $img = array();
    static $emoticons = array(
        ':-)'  => 'smiley',
        ':)'   => 'smiley',
        ':-D'  => 'biggrin',
        ';-)'  => 'wink',
        ':-/'  => 'mixed',
        'V-.'  => 'thoughtful',
        ':-P'  => 'tongueout',
        'B-)'  => 'cool',
        '^-)'  => 'approve',
        '8-)'  => 'wideeyes',
        ':o)'  => 'clown',
        ':-('  => 'sad',
        ':('   => 'sad',
        '8-.'  => 'shy',
        ':-I'  => 'blush',
        ':-X'  => 'kiss',
        '8-o'  => 'surprise',
        'P-|'  => 'blackeye',
        '8-['  => 'angry',
        'xx-P' => 'dead',
        '|-.'  => 'sleepy',
        '}-]'  => 'evil',
        );

    if ($runonce == false) {  /// After the first time this is not run again
        foreach ($emoticons as $emoticon => $image){
            $alttext = get_string($image, 'pix');

            $e[] = $emoticon;
            $img[] = '<img alt="'. $alttext .'" width="15" height="15" src="'. $CFG->pixpath .'/s/'. $image .'.gif" />';
        }
        $runonce = true;
    }

    // Exclude from transformations all the code inside <script> tags
    // Needed to solve Bug 1185. Thanks to jouse 2001 detecting it. :-)
    // Based on code from glossary fiter by Williams Castillo.
    //       - Eloy

    // Detect all the <script> zones to take out
    $excludes = array();
    preg_match_all('/<script language(.+?)<\/script>/is',$text,$list_of_excludes);

    // Take out all the <script> zones from text
    foreach (array_unique($list_of_excludes[0]) as $key=>$value) {
        $excludes['<+'.$key.'+>'] = $value;
    }
    if ($excludes) {
        $text = str_replace($excludes,array_keys($excludes),$text);
    }

/// this is the meat of the code - this is run every time
    $text = str_replace($e, $img, $text);

    // Recover all the <script> zones to text
    if ($excludes) {
        $text = str_replace(array_keys($excludes),$excludes,$text);
    }
}

/**
 * Given plain text, makes it into HTML as nicely as possible.
 * May contain HTML tags already
 *
 * @uses $CFG
 * @param string $text The string to convert.
 * @param boolean $smiley Convert any smiley characters to smiley images?
 * @param boolean $para If true then the returned string will be wrapped in paragraph tags
 * @param boolean $newlines If true then lines newline breaks will be converted to HTML newline breaks.
 * @return string
 */

function text_to_html($text, $smiley=true, $para=true, $newlines=true) {
/// 

    global $CFG;

/// Remove any whitespace that may be between HTML tags
    $text = eregi_replace(">([[:space:]]+)<", "><", $text);

/// Remove any returns that precede or follow HTML tags
    $text = eregi_replace("([\n\r])<", " <", $text);
    $text = eregi_replace(">([\n\r])", "> ", $text);

    convert_urls_into_links($text);

/// Make returns into HTML newlines.
    if ($newlines) {
        $text = nl2br($text);
    }

/// Turn smileys into images.
    if ($smiley) {
        replace_smilies($text);
    }

/// Wrap the whole thing in a paragraph tag if required
    if ($para) {
        return '<p>'.$text.'</p>';
    } else {
        return $text;
    }
}

/**
 * Given Wiki formatted text, make it into XHTML using external function
 *
 * @uses $CFG
 * @param string $text The text to be converted.
 * @param int $courseid The id, as found in 'course' table, of the course this text is being placed in.
 * @return string
 */
function wiki_to_html($text, $courseid) {

    global $CFG;

    require_once($CFG->libdir .'/wiki.php');

    $wiki = new Wiki;
    return $wiki->format($text, $courseid);
}

/**
 * Given Markdown formatted text, make it into XHTML using external function
 *
 * @uses $CFG
 * @param string $text The markdown formatted text to be converted.
 * @return string Converted text
 */
function markdown_to_html($text) {
    global $CFG;

    require_once($CFG->libdir .'/markdown.php');

    return Markdown($text);
}

/**
 * Given HTML text, make it into plain text using external function
 *
 * @uses $CFG
 * @param string $html The text to be converted.
 * @return string
 */
function html_to_text($html) {

    global $CFG;

    require_once($CFG->libdir .'/html2text.php');

    return html2text($html);
}

/**
 * Given some text this function converts any URLs it finds into HTML links
 *
 * @param string $text Passed in by reference. The string to be searched for urls.
 */
function convert_urls_into_links(&$text) {
/// Make lone URLs into links.   eg http://moodle.com/
    $text = eregi_replace("([[:space:]]|^|\(|\[)([[:alnum:]]+)://([^[:space:]]*)([[:alnum:]#?/&=])",
                          "\\1<a href=\"\\2://\\3\\4\" target=\"newpage\">\\2://\\3\\4</a>", $text);

/// eg www.moodle.com
    $text = eregi_replace("([[:space:]]|^|\(|\[)www\.([^[:space:]]*)([[:alnum:]#?/&=])",
                          "\\1<a href=\"http://www.\\2\\3\" target=\"newpage\">www.\\2\\3</a>", $text);
}

/**
 * This function will highlight search words in a given string
 * It cares about HTML and will not ruin links.  It's best to use
 * this function after performing any conversions to HTML.
 * Function found here: http://forums.devshed.com/t67822/scdaa2d1c3d4bacb4671d075ad41f0854.html
 *
 * @param string $needle The string to search for
 * @param string $haystack The string to search for $needle in
 * @param int $case ?
 * @return string
 * @todo Finish documenting this function
 */
function highlight($needle, $haystack, $case=0,
                    $left_string='<span class="highlight">', $right_string='</span>') {
    if (empty($needle)) {
        return $haystack;
    }

    $list_of_words = eregi_replace("[^-a-zA-Z0-9&']", " ", $needle);
    $list_array = explode(' ', $list_of_words);
    for ($i=0; $i<sizeof($list_array); $i++) {
        if (strlen($list_array[$i]) == 1) {
            $list_array[$i] = '';
        }
    }
    $list_of_words = implode(' ', $list_array);
    $list_of_words_cp = $list_of_words;
    $final = array();
    preg_match_all('/<(.+?)>/is',$haystack,$list_of_words);

    foreach (array_unique($list_of_words[0]) as $key=>$value) {
        $final['<|'.$key.'|>'] = $value;
    }

    $haystack = str_replace($final,array_keys($final),$haystack);
    $list_of_words_cp = eregi_replace(' +', '|', $list_of_words_cp);

    if ($list_of_words_cp{0}=='|') {
        $list_of_words_cp{0} = '';
    }
    if ($list_of_words_cp{strlen($list_of_words_cp)-1}=='|') {
        $list_of_words_cp{strlen($list_of_words_cp)-1}='';
    }
    $list_of_words_cp = '('. trim($list_of_words_cp) .')';

    if (!$case){
        $haystack = eregi_replace($list_of_words_cp, $left_string ."\\1". $right_string, $haystack);
    } else {
        $haystack = ereg_replace($list_of_words_cp, $left_string ."\\1". $right_string, $haystack);
    }
    $haystack = str_replace(array_keys($final),$final,$haystack);

    return $haystack;
}

/**
 * This function will highlight instances of $needle in $haystack
 * It's faster that the above function and doesn't care about
 * HTML or anything.
 *
 * @param string $needle The string to search for
 * @param string $haystack The string to search for $needle in
 * @return string
 */
function highlightfast($needle, $haystack) {

    $parts = explode(strtolower($needle), strtolower($haystack));

    $pos = 0;

    foreach ($parts as $key => $part) {
        $parts[$key] = substr($haystack, $pos, strlen($part));
        $pos += strlen($part);

        $parts[$key] .= '<span class="highlight">'.substr($haystack, $pos, strlen($needle)).'</span>';
        $pos += strlen($needle);
    }

    return (join('', $parts));
}


/// STANDARD WEB PAGE PARTS ///////////////////////////////////////////////////

/**
 * Print a standard header
 *
 * @uses $USER
 * @uses $CFG
 * @uses $THEME 
 * @uses $SESSION
 * @param string $title Appears at the top of the window
 * @param string $heading Appears at the top of the page
 * @param string $navigation Premade navigation string (for use as breadcrumbs links)
 * @param string $focus Indicates form element to get cursor focus on load eg  inputform.password
 * @param string $meta Meta tags to be added to the header
 * @param boolean $cache Should this page be cacheable?
 * @param string $button HTML code for a button (usually for module editing)
 * @param string $menu HTML code for a popup menu
 * @param boolean $usexml use XML for this page
 * @param string $bodytags This text will be included verbatim in the <body> tag (useful for onload() etc)
 */
function print_header ($title='', $heading='', $navigation='', $focus='', $meta='',
                       $cache=true, $button='&nbsp;', $menu='', $usexml=false, $bodytags='') {

    global $USER, $CFG, $THEME, $SESSION;

    global $course;                // This is a bit of an ugly hack to be gotten rid of later
    if (!empty($course->lang)) {
        $CFG->courselang = $course->lang;
    }

    if (file_exists($CFG->dirroot .'/theme/'. $CFG->theme .'/styles.php')) {
        $styles = $CFG->stylesheet;
    } else {
        $styles = $CFG->wwwroot .'/theme/standard/styles.php';
    }

    if ($navigation == 'home') {
        $home = true;
        $navigation = '';
    } else {
        $home = false;
    }

    if ($button == '') {
        $button = '&nbsp;';
    }

    if (!$menu and $navigation) {
        if (empty($CFG->loginhttps)) {
            $wwwroot = $CFG->wwwroot;
        } else {
            $wwwroot = str_replace('http','https',$CFG->wwwroot);
        }
        if (isset($USER->id)) {
            $menu = '<font size="2"><a target="'. $CFG->framename .'" href="'. $wwwroot .'/login/logout.php">'. get_string('logout') .'</a></font>';
        } else {
            $menu = '<font size="2"><a target="'. $CFG->framename .'" href="'. $wwwroot .'/login/index.php">'. get_string('login') .'</a></font>';
        }
    }

    if (isset($SESSION->justloggedin)) {
        unset($SESSION->justloggedin);
        if (!empty($CFG->displayloginfailures)) {
            if (!empty($USER->username) and !isguest()) {
                if ($count = count_login_failures($CFG->displayloginfailures, $USER->username, $USER->lastlogin)) {
                    $menu .= '&nbsp;<font size="1">';
                    if (empty($count->accounts)) {
                        $menu .= get_string('failedloginattempts', '', $count);
                    } else {
                        $menu .= get_string('failedloginattemptsall', '', $count);
                    }
                    if (isadmin()) {
                        $menu .= ' (<a href="'.$CFG->wwwroot.'/course/log.php'.
                                             '?chooselog=1&amp;id=1&amp;modid=site_errors">'.get_string('logs').'</a>)';
                    }
                    $menu .= '</font>';
                }
            }
        }
    }

    // Add a stylesheet for the HTML editor
    $meta = "<style type=\"text/css\">@import url($CFG->wwwroot/lib/editor/htmlarea.css);</style>\n$meta\n";

    if (!empty($CFG->unicode)) {
        $encoding = 'utf-8';
    } else if (!empty($CFG->courselang)) {
        $encoding = get_string('thischarset');
        moodle_setlocale();
    } else {
        if (!empty($SESSION->encoding)) {
            $encoding = $SESSION->encoding;
        } else {
            $SESSION->encoding = $encoding = get_string('thischarset');
        }
    }
    $meta = '<meta http-equiv="content-type" content="text/html; charset='. $encoding .'" />'. "\n". $meta ."\n";
    if (!$usexml) {
        @header('Content-type: text/html; charset='.$encoding);
    }

    if ( get_string('thisdirection') == 'rtl' ) {
        $direction = ' dir="rtl"';
    } else {
        $direction = ' dir="ltr"';
    }

    if (!$cache) {   // Do everything we can to prevent clients and proxies caching
        @header('Expires: Mon, 20 Aug 1969 09:23:00 GMT');
        @header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        @header('Cache-Control: no-store, no-cache, must-revalidate');
        @header('Cache-Control: post-check=0, pre-check=0', false);
        @header('Pragma: no-cache');

        $meta .= "\n".'<meta http-equiv="pragma" content="no-cache" />';
        $meta .= "\n".'<meta http-equiv="expires" content="0" />';
    }

    if ($usexml) {       // Added by Gustav Delius / Mad Alex for MathML output
                         // Modified by Julian Sedding
        $currentlanguage = current_language();
        $mathplayer = preg_match("/MathPlayer/i", $_SERVER['HTTP_USER_AGENT']);
        if(!$mathplayer) {
            header('Content-Type: application/xhtml+xml');
        }
        echo '<?xml version="1.0" ?>'."\n";
        if (!empty($CFG->xml_stylesheets)) {
            $stylesheets = explode(';', $CFG->xml_stylesheets);
            foreach ($stylesheets as $stylesheet) {
                echo '<?xml-stylesheet type="text/xsl" href="'. $CFG->wwwroot .'/'. $stylesheet .'" ?>' . "\n";
            }
        }
        echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1';
        if (!empty($CFG->xml_doctype_extra)) {
            echo ' plus '. $CFG->xml_doctype_extra;
        }
        echo '//' . strtoupper($currentlanguage) . '" "'. $CFG->xml_dtd .'">'."\n";
        $direction = " xmlns=\"http://www.w3.org/1999/xhtml\"
                       xmlns:math=\"http://www.w3.org/1998/Math/MathML\"
                       xml:lang=\"en\"
                       xmlns:xlink=\"http://www.w3.org/1999/xlink\"
                       $direction";
        if($mathplayer) {
            $meta .= '<object id="mathplayer" classid="clsid:32F66A20-7614-11D4-BD11-00104BD3F987">' . "\n";
            $meta .= '<!--comment required to prevent this becoming an empty tag-->'."\n";
            $meta .= '</object>'."\n";
            $meta .= '<?import namespace="math" implementation="#mathplayer" ?>' . "\n";
        }
    }

    $title = str_replace('"', '&quot;', $title);
    $title = strip_tags($title);

    include ($CFG->dirroot .'/theme/'. $CFG->theme .'/header.html');
}

/**
 * This version of print_header is simpler because the course name does not have to be
 * provided explicitly in the strings. It can be used on the site page as in courses
 * Eventually all print_header could be replaced by print_header_simple
 *
 * @param string $title Appears at the top of the window
 * @param string $heading Appears at the top of the page
 * @param string $navigation Premade navigation string (for use as breadcrumbs links)
 * @param string $focus Indicates form element to get cursor focus on load eg  inputform.password
 * @param string $meta Meta tags to be added to the header
 * @param boolean $cache Should this page be cacheable?
 * @param string $button HTML code for a button (usually for module editing)
 * @param string $menu HTML code for a popup menu
 * @param boolean $usexml use XML for this page
 * @param string $bodytags This text will be included verbatim in the <body> tag (useful for onload() etc)
 */
function print_header_simple($title='', $heading='', $navigation='', $focus='', $meta='',
                       $cache=true, $button='&nbsp;', $menu='', $usexml=false, $bodytags='') {

    global $course;                // The same hack is used in print_header

    $shortname ='';
    if ($course->category) {
        $shortname = '<a href="../../course/view.php?id='. $course->id .'">'. $course->shortname .'</a> ->';
    }

    print_header($course->shortname .': '. $title, $course->fullname .' '. $heading, $shortname .' '. $navigation, $focus, $meta,
                       $cache, $button, $menu, $usexml, $bodytags);
}


/**
 * Can provide a course object to make the footer contain a link to
 * to the course home page, otherwise the link will go to the site home
 *
 * @uses $CFG
 * @uses $USER
 * @uses $THEME
 * @param course $course {@link $COURSE} object containing course information
 * @param ? $usercourse ?
 * @todo Finish documenting this function
 */
function print_footer ($course=NULL, $usercourse=NULL) {
    global $USER, $CFG, $THEME;

/// Course links
    if ($course) {
        if ($course == 'home') {   // special case for site home page - please do not remove
            $homelink  = '<a title="moodle '. $CFG->release .' ('. $CFG->version .')" href="http://moodle.org/" target="_blank">';
            $homelink .= '<br /><img width="100" height="30" src="pix/moodlelogo.gif" border="0" alt="moodlelogo" /></a>';
            $course = get_site();
            $homepage = true;
        } else {
            $homelink = "<a target=\"{$CFG->framename}\" href=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</a>";
        }
    } else {
        $homelink = "<a target=\"{$CFG->framename}\" href=\"$CFG->wwwroot/\">".get_string('home').'</a>';
        $course = get_site();
    }

    if (!$usercourse) {
        $usercourse = $course;
    }

/// User links
    $loggedinas = user_login_string($usercourse, $USER);

    include ($CFG->dirroot .'/theme/'. $CFG->theme .'/footer.html');
}

/**
 * This function is called by stylesheets to set up the header
 * approriately as well as the current path
 *
 * @uses $CFG
 * @param int $lastmodified ?
 * @param int $lifetime ?
 * @param string $thename ?
 * @todo Finish documenting this function
 */
function style_sheet_setup($lastmodified=0, $lifetime=300, $themename='') {

    global $CFG;

    header('Last-Modified: ' . gmdate("D, d M Y H:i:s", $lastmodified) . ' GMT');
    header('Expires: ' . gmdate("D, d M Y H:i:s", time() + $lifetime) . ' GMT');
    header('Cache-control: max_age = '. $lifetime);
    header('Pragma: ');
    header('Content-type: text/css');  // Correct MIME type

    if (!empty($themename)) {
        $CFG->theme = $themename;
    }

    return $CFG->wwwroot .'/theme/'. $CFG->theme;

}

/**
 * Returns text to be displayed to the user which reflects their login status
 *
 * @uses $CFG
 * @uses $USER
 * @param course $course {@link $COURSE} object containing course information
 * @param user $user {@link $USER} object containing user information
 * @return string
 * @todo Finish documenting this function
 */
function user_login_string($course, $user=NULL) {
    global $USER, $CFG;

    if (empty($user)) {
        $user = $USER;
    }

    if (isset($user->realuser)) {
        if ($realuser = get_record('user', 'id', $user->realuser)) {
            $fullname = fullname($realuser, true);
            $realuserinfo = " [<a target=\"{$CFG->framename}\"
            href=\"$CFG->wwwroot/course/loginas.php?id=$course->id&amp;return=$realuser->id\">$fullname</a>] ";
        }
    } else {
        $realuserinfo = '';
    }

    if (empty($CFG->loginhttps)) {
        $wwwroot = $CFG->wwwroot;
    } else {
        $wwwroot = str_replace('http','https',$CFG->wwwroot);
    }

    if (isset($user->id) and $user->id) {
        $fullname = fullname($user, true);
        $username = "<a target=\"{$CFG->framename}\" href=\"$CFG->wwwroot/user/view.php?id=$user->id&amp;course=$course->id\">$fullname</a>";
        if (isguest($user->id)) {
            $loggedinas = $realuserinfo.get_string('loggedinas', 'moodle', $username).
                      " (<a target=\"{$CFG->framename}\" href=\"$wwwroot/login/index.php\">".get_string('login').'</a>)';
        } else {
            $loggedinas = $realuserinfo.get_string('loggedinas', 'moodle', $username).
                      " (<a target=\"{$CFG->framename}\" href=\"$CFG->wwwroot/login/logout.php\">".get_string('logout').'</a>)';
        }
    } else {
        $loggedinas = get_string('loggedinnot', 'moodle').
                      " (<a target=\"{$CFG->framename}\" href=\"$wwwroot/login/index.php\">".get_string('login').'</a>)';
    }
    return $loggedinas;
}

/**
 * Prints breadcrumbs links
 *
 * @uses $CFG
 * @param string $navigation The breadcrumbs string to be printed
 */
function print_navigation ($navigation) {
   global $CFG;

   if ($navigation) {
       if (! $site = get_site()) {
           $site->shortname = get_string('home');;
       }
       $navigation = str_replace('->', '&raquo;', $navigation);
       echo '<a target="'. $CFG->framename .'" href="'. $CFG->wwwroot .'/">'. $site->shortname .'</a> &raquo; '. $navigation;
   }
}

/**
 * Prints a string in a specified size
 *
 * @param string $text The text to be displayed
 * @param int $size The size to set the font for text display.
 */
function print_headline($text, $size=2) {
    echo '<strong><font size="'. $size .'">'. $text .'</font></strong><br />'."\n";
}

/**
 * Prints text in a format for use in headings.
 *
 * @param string $text The text to be displayed
 * @param string $align The alignment of the printed paragraph of text
 * @param int $size The size to set the font for text display.
 */
function print_heading($text, $align='center', $size=3) {
    echo '<p align="'. $align .'"><font size="'. $size .'"><strong>'. stripslashes_safe($text) .'</strong></font></p>';
}

/**
 * Centered heading with attached help button (same title text)
 * and optional icon attached
 *
 * @param string $text The text to be displayed
 * @param string $helppage The help page to link to
 * @param string $module The module whose help should be linked to
 * @param string $icon Image to display if needed
 */
function print_heading_with_help($text, $helppage, $module='moodle', $icon='') {
    echo '<p align="center"><font size="3">'. $icon .'<strong>'. stripslashes_safe($text);
    helpbutton($helppage, $text, $module);
    echo '</strong></font></p>';
}

/**
 * Print a link to continue on to another page.
 *
 * @uses $CFG
 * @param string $link The url to create a link to.
 */
function print_continue($link) {

    global $CFG;

    if (!$link) {
        $link = $_SERVER['HTTP_REFERER'];
    }

    print_heading('<a target="'. $CFG->framename .'" href="'. $link .'">'. get_string('continue').'</a>');
}

/**
 * Print a message in a standard themed box.
 *
 * @param string $message ?
 * @param string $align ?
 * @param string $width ?
 * @param string $color ?
 * @param int $padding ?
 * @param string $class ?
 * @todo Finish documenting this function
 */
function print_simple_box($message, $align='', $width='', $color='#FFFFFF', $padding=5, $class='generalbox') {
    print_simple_box_start($align, $width, $color, $padding, $class);
    echo stripslashes_safe($message);
    print_simple_box_end();
}

/**
 * Print the top portion of a standard themed box.
 *
 * @param string $align ?
 * @param string $width ?
 * @param string $color ?
 * @param int $padding ?
 * @param string $class ?
 * @todo Finish documenting this function
 */
function print_simple_box_start($align='', $width='', $color='#FFFFFF', $padding=5, $class='generalbox') {
    global $THEME;

    if ($align) {
        $align = 'align="'. $align .'"';
    }
    if ($width) {
        $width = 'width="'. $width .'"';
    }
    echo "<table $align $width class=\"$class\" border=\"0\" cellpadding=\"$padding\" cellspacing=\"0\"><tr><td bgcolor=\"$color\" class=\"$class"."content\">";
}

/**
 * Print the end portion of a standard themed box.
 */
function print_simple_box_end() {
    echo '</td></tr></table>';
}

/**
 * Print a self contained form with a single submit button.
 *
 * @param string $link ?
 * @param array $options ?
 * @param string $label ?
 * @param string $method ?
 * @todo Finish documenting this function
 */
function print_single_button($link, $options, $label='OK', $method='get') {
    echo '<form action="'. $link .'" method="'. $method .'">';
    if ($options) {
        foreach ($options as $name => $value) {
            echo '<input type="hidden" name="'. $name .'" value="'. $value .'" />';
        }
    }
    echo '<input type="submit" value="'. $label .'" /></form>';
}

/**
 * Print a spacer image with the option of including a line break.
 *
 * @param int $height ?
 * @param int $width ?
 * @param boolean $br ?
 * @todo Finish documenting this function
 */
function print_spacer($height=1, $width=1, $br=true) {
    global $CFG;
    echo '<img height="'. $height .'" width="'. $width .'" src="'. $CFG->wwwroot .'/pix/spacer.gif" alt="" />';
    if ($br) {
        echo '<br />'."\n";
    }
}

/**
 * Given the path to a picture file in a course, or a URL,
 * this function includes the picture in the page.
 *
 * @param string $path ?
 * @param int $courseid ?
 * @param int $height ?
 * @param int $width ?
 * @param string $link ?
 * @todo Finish documenting this function
 */
function print_file_picture($path, $courseid=0, $height='', $width='', $link='') {
    global $CFG;

    if ($height) {
        $height = 'height="'. $height .'"';
    }
    if ($width) {
        $width = 'width="'. $width .'"';
    }
    if ($link) {
        echo '<a href="'. $link .'">';
    }
    if (substr(strtolower($path), 0, 7) == 'http://') {
        echo '<img border="0" '.$height . $width .' src="'. $path .'" />';

    } else if ($courseid) {
        echo '<img border="0" '. $height . $width .' src="';
        if ($CFG->slasharguments) {        // Use this method if possible for better caching
            echo $CFG->wwwroot .'/file.php/'. $courseid .'/'. $path;
        } else {
            echo $CFG->wwwroot .'/file.php?file=/'. $courseid .'/'. $path;
        }
        echo '" />';
    } else {
        echo 'Error: must pass URL or course';
    }
    if ($link) {
        echo '</a>';
    }
}

/**
 * Print the specified user's avatar.
 *
 * @param int $userid ?
 * @param int $courseid ?
 * @param boolean $picture Print the user picture?
 * @param boolean $large Should the picture be printed at 100 pixels or 35?
 * @param boolean $returnstring If false print picture to current page, otherwise return the output as string
 * @param boolean $link Enclose printed image in a link to view specified course?
 * return string
 * @todo Finish documenting this function
 */
function print_user_picture($userid, $courseid, $picture, $large=false, $returnstring=false, $link=true) {
    global $CFG;

    if ($link) {
        $output = '<a href="'. $CFG->wwwroot .'/user/view.php?id='. $userid .'&amp;course='. $courseid .'">';
    } else {
        $output = '';
    }
    if ($large) {
        $file = 'f1';
        $size = 100;
    } else {
        $file = 'f2';
        $size = 35;
    }
    if ($picture) {  // Print custom user picture
        if ($CFG->slasharguments) {        // Use this method if possible for better caching
            $output .= '<img align="middle" src="'. $CFG->wwwroot .'/user/pix.php/'. $userid .'/'. $file .'.jpg"'.
                       ' border="0" width="'. $size .'" height="'. $size .'" alt="" />';
        } else {
            $output .= '<img align="middle" src="'. $CFG->wwwroot .'/user/pix.php?file=/'. $userid .'/'. $file .'.jpg"'.
                       ' border="0" width="'. $size .'" height="'. $size .'" alt="" />';
        }
    } else {         // Print default user pictures (use theme version if available)
        $output .= "<img align=\"middle\" src=\"$CFG->pixpath/u/$file.png\"".
                   " border=\"0\" width=\"$size\" height=\"$size\" alt=\"\" />";
    }
    if ($link) {
        $output .= '</a>';
    }

    if ($returnstring) {
        return $output;
    } else {
        echo $output;
    }
}

/**
 * Prints a summary of a user in a nice little box.
 *
 * @uses $CFG
 * @uses $USER
 * @param user $user A {@link $USER} object representing a user
 * @param course $course A {@link $COURSE} object representing a course
 */
function print_user($user, $course) {

    global $CFG, $USER;

    static $string;
    static $datestring;
    static $countries;
    static $isteacher;
    static $isadmin;

    if (empty($string)) {     // Cache all the strings for the rest of the page

        $string->email       = get_string('email');
        $string->location    = get_string('location');
        $string->lastaccess  = get_string('lastaccess');
        $string->activity    = get_string('activity');
        $string->unenrol     = get_string('unenrol');
        $string->loginas     = get_string('loginas');
        $string->fullprofile = get_string('fullprofile');
        $string->role        = get_string('role');
        $string->name        = get_string('name');
        $string->never       = get_string('never');

        $datestring->day     = get_string('day');
        $datestring->days    = get_string('days');
        $datestring->hour    = get_string('hour');
        $datestring->hours   = get_string('hours');
        $datestring->min     = get_string('min');
        $datestring->mins    = get_string('mins');
        $datestring->sec     = get_string('sec');
        $datestring->secs    = get_string('secs');

        $countries = get_list_of_countries();

        $isteacher = isteacher($course->id);
        $isadmin   = isadmin();
    }

    echo '<table width="80%" align="center" border="0" cellpadding="10" cellspacing="0" class="userinfobox">';
    echo '<tr>';
    echo '<td width="100" bgcolor="#ffffff" valign="top" class="userinfoboxside">';
    print_user_picture($user->id, $course->id, $user->picture, true);
    echo '</td>';
    echo '<td width="100%" bgcolor="#ffffff" valign="top" class="userinfoboxsummary">';
    echo '<font size="-1">';
    echo '<font size="3"><strong>'.fullname($user, $isteacher).'</strong></font>';
    echo '<p>';
    if (!empty($user->role) and ($user->role <> $course->teacher)) {
        echo $string->role .': '. $user->role .'<br />';
    }
    if ($user->maildisplay == 1 or ($user->maildisplay == 2 and $course->category and !isguest()) or $isteacher) {
        echo $string->email .': <a href="mailto:'. $user->email .'">'. $user->email .'</a><br />';
    }
    if ($user->city or $user->country) {
        echo $string->location .': ';
        if ($user->city) {
            echo $user->city;
        }
        if (!empty($countries[$user->country])) {
            if ($user->city) {
                echo ', ';
            }
            echo $countries[$user->country];
        }
        echo '<br />';
    }
    if ($user->lastaccess) {
        echo $string->lastaccess .': '. userdate($user->lastaccess);
        echo '&nbsp ('. format_time(time() - $user->lastaccess, $datestring) .')';
    } else {
        echo $string->lastaccess .': '. $string->never;
    }
    echo '</td><td valign="bottom" bgcolor="#ffffff" nowrap="nowrap" class="userinfoboxlinkcontent">';

    echo '<font size="1">';
    if ($isteacher) {
        $timemidnight = usergetmidnight(time());
        echo '<a href="'. $CFG->wwwroot .'/course/user.php?id='. $course->id .'&amp;user='. $user->id .'">'. $string->activity .'</a><br />';
        if (!iscreator($user->id) or ($isadmin and !isadmin($user->id))) {  // Includes admins
            if ($course->category and isteacheredit($course->id) and isstudent($course->id, $user->id)) {  // Includes admins
                echo '<a href="'. $CFG->wwwroot .'/course/unenrol.php?id='. $course->id .'&amp;user='. $user->id .'">'. $string->unenrol .'</a><br />';
            }
            if ($USER->id != $user->id) {
                echo '<a href="'. $CFG->wwwroot .'/course/loginas.php?id='. $course->id .'&amp;user='. $user->id .'">'. $string->loginas .'</a><br />';
            }
        }
    }
    echo '<a href="'. $CFG->wwwroot .'/user/view.php?id='. $user->id .'&amp;course='. $course->id .'">'. $string->fullprofile .'...</a>';
    echo '</font>';

    echo '</td></tr></table>';
}

/**
 * Print a specified group's avatar.
 *
 * @param group $group A {@link group} object representing a group
 * @param int $courseid ?
 * @param boolean $large ?
 * @param boolean $returnstring ?
 * @param boolean $link ?
 * @return string
 * @todo Finish documenting this function
 */
function print_group_picture($group, $courseid, $large=false, $returnstring=false, $link=true) {
    global $CFG;

    static $isteacheredit;

    if (!isset($isteacheredit)) {
        $isteacheredit = isteacheredit($courseid);
    }

    if ($group->hidepicture and !$isteacheredit) {
        return '';
    }

    if ($link or $isteacheredit) {
        $output = '<a href="'. $CFG->wwwroot .'/course/group.php?id='. $courseid .'&amp;group='. $group->id .'">';
    } else {
        $output = '';
    }
    if ($large) {
        $file = 'f1';
        $size = 100;
    } else {
        $file = 'f2';
        $size = 35;
    }
    if ($group->picture) {  // Print custom group picture
        if ($CFG->slasharguments) {        // Use this method if possible for better caching
            $output .= "<img align=\"middle\" src=\"$CFG->wwwroot/user/pixgroup.php/$group->id/$file.jpg\"".
                       " border=\"0\" width=\"$size\" height=\"$size\" alt=\"\" title=\"$group->name\"/>";
        } else {
            $output .= "<img align=\"middle\" src=\"$CFG->wwwroot/user/pixgroup.php?file=/$group->id/$file.jpg\"".
                       " border=\"0\" width=\"$size\" height=\"$size\" alt=\"\" title=\"$group->name\"/>";
        }
    }
    if ($link or $isteacheredit) {
        $output .= '</a>';
    }

    if ($returnstring) {
        return $output;
    } else {
        echo $output;
    }
}

/**
 * Print a png image.
 *
 * @param string $url ?
 * @param int $sizex ?
 * @param int $sizey ?
 * @param boolean $returnstring ?
 * @param string $parameters ?
 * @todo Finish documenting this function
 */
function print_png($url, $sizex, $sizey, $returnstring, $parameters='alt=""') {
    global $CFG;
    static $recentIE;

    if (!isset($recentIE)) {
        $recentIE = check_browser_version('MSIE', '5.0');
    }

    if ($recentIE) {  // work around the HORRIBLE bug IE has with alpha transparencies
        $output .= '<img src="'. $CFG->pixpath .'/spacer.gif" width="'. $sizex .'" height="'. $sizey .'"'.
                   ' border="0" style="width: '. $sizex .'px; height: '. $sizey .'px; '.
                   ' filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='.
                   "'$url', sizingMethod='scale') ".
                   ' '. $parameters .' />';
    } else {
        $output .= '<img src="'. $url .'" border="0" width="'. $sizex .'" height="'. $sizey .'" '.
                   ' '. $parameters .' />';
    }

    if ($returnstring) {
        return $output;
    } else {
        echo $output;
    }
}

/**
 * Print a nicely formatted table.
 *
 * @uses $THEME
 * @param array $table is an object with several properties.
 *     <ul<li>$table->head - An array of heading names.
 *     <li>$table->align - An array of column alignments
 *     <li>$table->size  - An array of column sizes
 *     <li>$table->wrap - An array of "nowrap"s or nothing
 *     <li>$table->data[] - An array of arrays containing the data.
 *     <li>$table->width  - A percentage of the page
 *     <li>$table->cellpadding  - Padding on each cell
 *     <li>$table->cellspacing  - Spacing between cells
 * </ul>
 * @return boolean
 * @todo Finish documenting this function
 */
function print_table($table) {

    global $THEME;

    if (isset($table->align)) {
        foreach ($table->align as $key => $aa) {
            if ($aa) {
                $align[$key] = ' align="'. $aa .'"';
            } else {
                $align[$key] = '';
            }
        }
    }
    if (isset($table->size)) {
        foreach ($table->size as $key => $ss) {
            if ($ss) {
                $size[$key] = ' width="'. $ss .'"';
            } else {
                $size[$key] = '';
            }
        }
    }
    if (isset($table->wrap)) {
        foreach ($table->wrap as $key => $ww) {
            if ($ww) {
                $wrap[$key] = ' nowrap="nowrap" ';
            } else {
                $wrap[$key] = '';
            }
        }
    }

    if (empty($table->width)) {
        $table->width = '80%';
    }

    if (empty($table->cellpadding)) {
        $table->cellpadding = '5';
    }

    if (empty($table->cellspacing)) {
        $table->cellspacing = '1';
    }

    print_simple_box_start('center', $table->width, '#ffffff', 0);
    echo '<table width="100%" border="0" align="center" ';
    echo " cellpadding=\"$table->cellpadding\" cellspacing=\"$table->cellspacing\" class=\"generaltable\">\n";

    $countcols = 0;

    if (!empty($table->head)) {
        $countcols = count($table->head);;
        echo '<tr>';
        foreach ($table->head as $key => $heading) {

            if (!isset($size[$key])) {
                $size[$key] = '';
            }
            if (!isset($align[$key])) {
                $align[$key] = '';
            }
            echo '<th valign="top" '. $align[$key].$size[$key] .' nowrap="nowrap" class="generaltableheader">'. $heading .'</th>';
        }
        echo '</tr>'."\n";
    }

    if (!empty($table->data)) {
        foreach ($table->data as $row) {
            echo '<tr valign="top">';
            if ($row == 'hr' and $countcols) {
                echo '<td colspan="'. $countcols .'"><div class="tabledivider"></div></td>';
            } else {  /// it's a normal row of data
                foreach ($row as $key => $item) {
                    if (!isset($size[$key])) {
                        $size[$key] = '';
                    }
                    if (!isset($align[$key])) {
                        $align[$key] = '';
                    }
                    if (!isset($wrap[$key])) {
                        $wrap[$key] = '';
                    }
                    echo '<td '. $align[$key].$size[$key].$wrap[$key] .' class="generaltablecell">'. $item .'</td>';
                }
            }
            echo '</tr>'."\n";
        }
    }
    echo '</table>'."\n";
    print_simple_box_end();

    return true;
}

/**
 * Creates a nicely formatted table and returns it.
 *
 * @param array $table is an object with several properties.
 *     <ul<li>$table->head - An array of heading names.
 *     <li>$table->align - An array of column alignments
 *     <li>$table->size  - An array of column sizes
 *     <li>$table->wrap - An array of "nowrap"s or nothing
 *     <li>$table->data[] - An array of arrays containing the data.
 *     <li>$table->class -  A css class name
 *     <li>$table->fontsize - Is the size of all the text
 *     <li>$table->tablealign  - Align the whole table
 *     <li>$table->width  - A percentage of the page
 *     <li>$table->cellpadding  - Padding on each cell
 *     <li>$table->cellspacing  - Spacing between cells
 * </ul>
 * @return string
 * @todo Finish documenting this function
 */
function make_table($table) {

    if (isset($table->align)) {
        foreach ($table->align as $key => $aa) {
            if ($aa) {
                $align[$key] = ' align="'. $aa .'"';
            } else {
                $align[$key] = '';
            }
        }
    }
    if (isset($table->size)) {
        foreach ($table->size as $key => $ss) {
            if ($ss) {
                $size[$key] = ' width="'. $ss .'"';
            } else {
                $size[$key] = '';
            }
        }
    }
    if (isset($table->wrap)) {
        foreach ($table->wrap as $key => $ww) {
            if ($ww) {
                $wrap[$key] = ' nowrap="nowrap" ';
            } else {
                $wrap[$key] = '';
            }
        }
    }

    if (empty($table->width)) {
        $table->width = '80%';
    }

    if (empty($table->tablealign)) {
        $table->tablealign = 'center';
    }

    if (empty($table->cellpadding)) {
        $table->cellpadding = '5';
    }

    if (empty($table->cellspacing)) {
        $table->cellspacing = '1';
    }

    if (empty($table->class)) {
        $table->class = 'generaltable';
    }

    if (empty($table->fontsize)) {
        $fontsize = '';
    } else {
        $fontsize = '<font size="'. $table->fontsize .'">';
    }

    $output =  '<table width="'. $table->width .'" valign="top" align="'. $table->tablealign .'" ';
    $output .= ' cellpadding="'. $table->cellpadding .'" cellspacing="'. $table->cellspacing .'" class="'. $table->class .'">'."\n";

    if (!empty($table->head)) {
        $output .= '<tr>';
        foreach ($table->head as $key => $heading) {
            if (!isset($size[$key])) {
                $size[$key] = '';
            }
            if (!isset($align[$key])) {
                $align[$key] = '';
            }
            $output .= '<th valign="top" '. $align[$key].$size[$key] .' nowrap="nowrap" class="'. $table->class .'header">'.$fontsize.$heading.'</th>';
        }
        $output .= '</tr>'."\n";
    }

    foreach ($table->data as $row) {
        $output .= '<tr valign="top">';
        foreach ($row as $key => $item) {
            if (!isset($size[$key])) {
                $size[$key] = '';
            }
            if (!isset($align[$key])) {
                $align[$key] = '';
            }
            if (!isset($wrap[$key])) {
                $wrap[$key] = '';
            }
            $output .= '<td '. $align[$key].$size[$key].$wrap[$key] .' class="'. $table->class .'cell">'. $fontsize . $item .'</td>';
        }
        $output .= '</tr>'."\n";
    }
    $output .= '</table>'."\n";

    return $output;
}

/**
 * Prints a basic textarea field.
 *
 * @uses $CFG
 * @param boolean $usehtmleditor ?
 * @param int $rows ?
 * @param int $cols ?
 * @param null $width <b>Legacy field no longer used!</b>
 * @param null $height <b>Legacy field no longer used!</b>
 * @param string $name ?
 * @param string $value ?
 * @param int $courseid ?
 * @todo Finish documenting this function
 */
function print_textarea($usehtmleditor, $rows, $cols, $width, $height, $name, $value='', $courseid=0) {
/// $width and height are legacy fields and no longer used

    global $CFG, $course;

    if (empty($courseid)) {
        if (!empty($course->id)) {  // search for it in global context
            $courseid = $course->id;
        }
    }

    if ($usehtmleditor) {
        if (!empty($courseid) and isteacher($courseid)) {
            echo '<script type="text/javascript" src="'. $CFG->wwwroot .'/lib/editor/htmlarea.php?id='. $courseid .'"></script>'."\n";
        } else {
            echo '<script type="text/javascript" src="'. $CFG->wwwroot .'/lib/editor/htmlarea.php"></script>'."\n";
        }
        echo '<script type="text/javascript" src="'. $CFG->wwwroot .'/lib/editor/dialog.js"></script>'."\n";
        echo '<script type="text/javascript" src="'. $CFG->wwwroot .'/lib/editor/lang/en.php"></script>'."\n";
        echo '<script type="text/javascript" src="'. $CFG->wwwroot .'/lib/editor/popupwin.js"></script>'."\n";

        if ($rows < 10) {
            $rows = 10;
        }
        if ($cols < 65) {
            $cols = 65;
        }
    }

    echo '<textarea id="'. $name .'" name="'. $name .'" rows="'. $rows .'" cols="'. $cols .'">';
    p($value);
    echo '</textarea>'."\n";
}

/**
 * Legacy function, provided for backward compatability. 
 * This method now simply calls {@link use_html_editor()}
 *
 * @deprecated Use {@link use_html_editor()} instead.
 * @param string $name Form element to replace with HTMl editor by name
 * @todo Finish documenting this function
 */
function print_richedit_javascript($form, $name, $source='no') {
    use_html_editor($name);
}

/**
 * Sets up the HTML editor on textareas in the current page.
 * If a field name is provided, then it will only be
 * applied to that field - otherwise it will be used
 * on every textarea in the page.
 *
 * In most cases no arguments need to be supplied
 *
 * @param string $name Form element to replace with HTMl editor by name
 */
function use_html_editor($name='') {
    echo '<script language="javascript" type="text/javascript" defer="defer">'."\n";
    print_editor_config();
    if (empty($name)) {
        echo "\n".'HTMLArea.replaceAll(config);'."\n";
    } else {
        echo "\nHTMLArea.replace('$name', config);\n";
    }
    echo '</script>'."\n";
}

/**
 * Returns a turn edit on/off button for course in a self contained form.
 * Used to be an icon, but it's now a simple form button
 *
 * @uses $CFG
 * @uses $USER
 * @param int $courseid The course  to update by id as found in 'course' table
 * @return string
 */
function update_course_icon($courseid) {
 
    global $CFG, $USER;

    if (isteacheredit($courseid)) {
        if (!empty($USER->editing)) {
            $string = get_string('turneditingoff');
            $edit = 'off';
        } else {
            $string = get_string('turneditingon');
            $edit = 'on';
        }
        return "<form target=\"$CFG->framename\" method=\"get\" action=\"$CFG->wwwroot/course/view.php\">".
               "<input type=\"hidden\" name=\"id\" value=\"$courseid\" />".
               "<input type=\"hidden\" name=\"edit\" value=\"$edit\" />".
               "<input type=\"submit\" value=\"$string\" /></form>";
    }
}

/**
 * Prints the editing button on a module "view" page
 *
 * @uses $CFG
 * @param    type description
 * @todo Finish documenting this function
 */
function update_module_button($moduleid, $courseid, $string) {
    global $CFG;

    if (isteacheredit($courseid)) {
        $string = get_string('updatethis', '', $string);
        return "<form target=\"$CFG->framename\" method=\"get\" action=\"$CFG->wwwroot/course/mod.php\">".
               "<input type=\"hidden\" name=\"update\" value=\"$moduleid\" />".
               "<input type=\"hidden\" name=\"return\" value=\"true\" />".
               "<input type=\"submit\" value=\"$string\" /></form>";
    } else {
        return '';
    }
}

/**
 * Prints the editing button on a category page
 *
 * @uses $CFG
 * @uses $USER
 * @param int $categoryid ?
 * @return string
 * @todo Finish documenting this function
 */
function update_category_button($categoryid) {
    global $CFG, $USER;

    if (iscreator()) {
        if (!empty($USER->categoryediting)) {
            $string = get_string('turneditingoff');
            $edit = 'off';
        } else {
            $string = get_string('turneditingon');
            $edit = 'on';
        }
        return "<form target=\"$CFG->framename\" method=\"get\" action=\"$CFG->wwwroot/course/category.php\">".
               "<input type=\"hidden\" name=\"id\" value=\"$categoryid\" />".
               "<input type=\"hidden\" name=\"edit\" value=\"$edit\" />".
               "<input type=\"submit\" value=\"$string\" /></form>";
    }
}

/**
 * Prints the editing button on categories listing
 *
 * @uses $CFG
 * @uses $USER
 * @return string
 */
function update_categories_button() {
    global $CFG, $USER;

    if (isadmin()) {
        if (!empty($USER->categoriesediting)) {
            $string = get_string('turneditingoff');
            $edit = 'off';
        } else {
            $string = get_string('turneditingon');
            $edit = 'on';
        }
        return "<form target=\"$CFG->framename\" method=\"get\" action=\"$CFG->wwwroot/course/index.php\">".
               '<input type="hidden" name="edit" value="'. $edit .'" />'.
               '<input type="submit" value="'. $string .'" /></form>';
    }
}

/**
 * Prints the editing button on group page
 *
 * @uses $CFG
 * @uses $USER
 * @param int $courseid The course group is associated with
 * @param int $groupid The group to update
 * @return string
 */
function update_group_button($courseid, $groupid) {
    global $CFG, $USER;

    if (isteacheredit($courseid)) {
        $string = get_string('editgroupprofile');
        return "<form target=\"$CFG->framename\" method=\"get\" action=\"$CFG->wwwroot/course/group.php\">".
               '<input type="hidden" name="id" value="'. $courseid .'" />'.
               '<input type="hidden" name="group" value="'. $groupid .'" />'.
               '<input type="hidden" name="edit" value="on" />'.
               '<input type="submit" value="'. $string .'" /></form>';
    }
}

/**
 * Prints the editing button on groups page
 *
 * @uses $CFG
 * @uses $USER
 * @param int $courseid The id of the course to be edited
 * @return string
 * @todo Finish documenting this function
 */
function update_groups_button($courseid) {
    global $CFG, $USER;

    if (isteacheredit($courseid)) {
        if (!empty($USER->groupsediting)) {
            $string = get_string('turneditingoff');
            $edit = 'off';
        } else {
            $string = get_string('turneditingon');
            $edit = 'on';
        }
        return "<form target=\"$CFG->framename\" method=\"get\" action=\"$CFG->wwwroot/course/groups.php\">".
               "<input type=\"hidden\" name=\"id\" value=\"$courseid\" />".
               "<input type=\"hidden\" name=\"edit\" value=\"$edit\" />".
               "<input type=\"submit\" value=\"$string\" /></form>";
    }
}

/**
 * Prints an appropriate group selection menu
 *
 * @uses VISIBLEGROUPS
 * @param array $groups ?
 * @param int $groupmode ?
 * @param string $currentgroup ?
 * @param string $urlroot ?
 * @todo Finish documenting this function
 */
function print_group_menu($groups, $groupmode, $currentgroup, $urlroot) {

/// Add an "All groups" to the start of the menu
    $groupsmenu[0] = get_string('allparticipants');
    foreach ($groups as $key => $groupname) {
        $groupsmenu[$key] = $groupname;
    }

    echo '<table><tr><td align="right">';
    if ($groupmode == VISIBLEGROUPS) {
        print_string('groupsvisible');
    } else {
        print_string('groupsseparate');
    }
    echo ':';
    echo '</td><td nowrap="nowrap" align="left">';
    popup_form($urlroot.'&amp;group=', $groupsmenu, 'selectgroup', $currentgroup, '', '', '', false, 'self');
    echo '</tr></table>';

}

/**
 * Given a course and a (current) coursemodule
 * This function returns a small popup menu with all the
 * course activity modules in it, as a navigation menu
 * The data is taken from the serialised array stored in
 * the course record
 *
 * @param course $course A {@link $COURSE} object.
 * @param course $cm A {@link $COURSE} object.
 * @param string $targetwindow ?
 * @return string
 * @todo Finish documenting this function
 */
function navmenu($course, $cm=NULL, $targetwindow='self') {

    global $CFG;

    if ($cm) {
        $cm = $cm->id;
    }

    if ($course->format == 'weeks') {
        $strsection = get_string('week');
    } else {
        $strsection = get_string('topic');
    }

    if (!$modinfo = unserialize($course->modinfo)) {
        return '';
    }
    $isteacher = isteacher($course->id);
    $section = -1;
    $selected = '';
    $url = '';
    $previousmod = NULL;
    $backmod = NULL;
    $nextmod = NULL;
    $selectmod = NULL;
    $logslink = NULL;
    $flag = false;
    $menu = array();
    $strjumpto = get_string('jumpto');

    $sections = get_records('course_sections','course',$course->id,'section','section,visible,summary');

    foreach ($modinfo as $mod) {
        if ($mod->mod == 'label') {
            continue;
        }

        if ($mod->section > 0 and $section <> $mod->section) {
            $thissection = $sections[$mod->section];

            if ($thissection->visible or !$course->hiddensections or $isteacher) {
                $thissection->summary = strip_tags($thissection->summary);
                if ($course->format == 'weeks' or empty($thissection->summary)) {
                    $menu[] = '-------------- '. $strsection ." ". $mod->section .' --------------';
                } else {
                    if (strlen($thissection->summary) < 47) {
                        $menu[] = '-- '.$thissection->summary;
                    } else {
                        $menu[] = '-- '.substr($thissection->summary, 0, 50).'...';
                    }
                }
            }
        }

        $section = $mod->section;

        //Only add visible or teacher mods to jumpmenu
        if ($mod->visible or $isteacher) {
            $url = $mod->mod .'/view.php?id='. $mod->cm;
            if ($flag) { // the current mod is the "next" mod
                $nextmod = $mod;
                $flag = false;
            }
            if ($cm == $mod->cm) {
                $selected = $url;
                $selectmod = $mod;
                $backmod = $previousmod;
                $flag = true; // set flag so we know to use next mod for "next"
                $mod->name = $strjumpto;
                $strjumpto = '';
            } else {
                $mod->name = strip_tags(urldecode($mod->name));
                if (strlen($mod->name) > 55) {
                    $mod->name = substr($mod->name, 0, 50).'...';
                }
                if (!$mod->visible) {
                    $mod->name = '('.$mod->name.')';
                }
            }
            $menu[$url] = $mod->name;
            $previousmod = $mod;
        }
    }
    if ($selectmod and $isteacher) {
        $logslink = "<td><a target=\"$CFG->framename\" href=".
                    "\"$CFG->wwwroot/course/log.php?chooselog=1&amp;user=0&amp;date=0&amp;id=$course->id&amp;modid=$selectmod->cm\">".
                    "<img border=\"0\" height=\"16\" width=\"16\" src=\"$CFG->pixpath/i/log.gif\" alt=\"\" /></a></td>";

    }
    if ($backmod) {
        $backmod = "<form action=\"$CFG->wwwroot/mod/$backmod->mod/view.php\" target=\"$CFG->framename\">".
                   "<input type=\"hidden\" name=\"id\" value=\"$backmod->cm\" />".
                   "<input type=\"submit\" value=\"&lt;\" /></form>";
    }
    if ($nextmod) {
        $nextmod = "<form action=\"$CFG->wwwroot/mod/$nextmod->mod/view.php\" target=\"$CFG->framename\">".
                   "<input type=\"hidden\" name=\"id\" value=\"$nextmod->cm\" />".
                   "<input type=\"submit\" value=\"&gt;\" /></form>";
    }
    return '<table><tr>'.$logslink .'<td>'. $backmod .'</td><td>' .
            popup_form($CFG->wwwroot .'/mod/', $menu, 'navmenu', $selected, $strjumpto,
                       '', '', true, $targetwindow).
            '</td><td>'. $nextmod .'</td></tr></table>';
}

/**
 * Prints form items with the names $day, $month and $year
 *
 * @param int $day ?
 * @param int $month ?
 * @param int $year ?
 * @param int $currenttime A default timestamp in GMT
 * @todo Finish documenting this function
 */
function print_date_selector($day, $month, $year, $currenttime=0) {

    if (!$currenttime) {
        $currenttime = time();
    }
    $currentdate = usergetdate($currenttime);

    for ($i=1; $i<=31; $i++) {
        $days[$i] = $i;
    }
    for ($i=1; $i<=12; $i++) {
        $months[$i] = userdate(gmmktime(12,0,0,$i,1,2000), "%B");
    }
    for ($i=2000; $i<=2010; $i++) {
        $years[$i] = $i;
    }
    choose_from_menu($days,   $day,   $currentdate['mday'], '');
    choose_from_menu($months, $month, $currentdate['mon'],  '');
    choose_from_menu($years,  $year,  $currentdate['year'], '');
}

/**
 *Prints form items with the names $hour and $minute
 *
 * @param ? $hour ?
 * @param ? $minute ?
 * @param $currenttime A default timestamp in GMT
 * @param int $step ?
 * @todo Finish documenting this function
 */
function print_time_selector($hour, $minute, $currenttime=0, $step=5) {

    if (!$currenttime) {
        $currenttime = time();
    }
    $currentdate = usergetdate($currenttime);
    if ($step != 1) {
        $currentdate['minutes'] = ceil($currentdate['minutes']/$step)*$step;
    }
    for ($i=0; $i<=23; $i++) {
        $hours[$i] = sprintf("%02d",$i);
    }
    for ($i=0; $i<=59; $i+=$step) {
        $minutes[$i] = sprintf("%02d",$i);
    }
    choose_from_menu($hours,   $hour,   $currentdate['hours'],   '');
    choose_from_menu($minutes, $minute, $currentdate['minutes'], '');
}

/**
 * Prints time limit value selector
 *
 * @uses $CFG
 * @param int $timelimit ?
 * @param string $unit ?
 * @todo Finish documenting this function
 */
function print_timer_selector($timelimit = 0, $unit = '') {

    global $CFG;

    if ($unit) {
        $unit = ' '.$unit;
    }

    // Max timelimit is sessiontimeout - 10 minutes.
    $maxvalue = ($CFG->sessiontimeout / 60) - 10;

    for ($i=1; $i<=$maxvalue; $i++) {
        $minutes[$i] = $i.$unit;
    }
    choose_from_menu($minutes, 'timelimit', $timelimit, get_string('none'));
}

/**
 * Prints a grade menu (as part of an existing form) with help
 * Showing all possible numerical grades and scales
 *
 * @uses $CFG
 * @param int $courseid ?
 * @param string $name ?
 * @param string $current ?
 * @param boolean $includenograde ?
 * @todo Finish documenting this function
 */
function print_grade_menu($courseid, $name, $current, $includenograde=true) { 

    global $CFG;

    $strscale = get_string('scale');
    $strscales = get_string('scales');

    $scales = get_scales_menu($courseid);
    foreach ($scales as $i => $scalename) {
        $grades[-$i] = $strscale .': '. $scalename;
    }
    if ($includenograde) {
        $grades[0] = get_string('nograde');
    }
    for ($i=100; $i>=1; $i--) {
        $grades[$i] = $i;
    }
    choose_from_menu($grades, $name, $current, '');

    $helpicon = $CFG->pixpath .'/help.gif';
    $linkobject = "<img align=\"middle\" border=\"0\" height=\"17\" width=\"22\" alt=\"$strscales\" src=\"$helpicon\" />";
    link_to_popup_window ('/course/scales.php?id='. $courseid .'&amp;list=true', 'ratingscales',
                          $linkobject, 400, 500, $strscales);
}

/**
 * Prints a scale menu (as part of an existing form) including help button
 * Just like {@link print_grade_menu()} but without the numeric grades
 *
 * @param int $courseid ?
 * @param string $name ?
 * @param string $current ?
 * @todo Finish documenting this function
 */
function print_scale_menu($courseid, $name, $current) {

    global $CFG;

    $strscales = get_string('scales');
    choose_from_menu(get_scales_menu($courseid), $name, $current, '');
    $helpicon = $CFG->pixpath .'/help.gif';
    $linkobject = '<img align="middle" border="0" height="17" width="22" alt="'. $strscales .'" src="'. $helpicon .'" />';
    link_to_popup_window ('/course/scales.php?id='. $courseid .'&amp;list=true', 'ratingscales',
                          $linkobject, 400, 500, $strscales);
}

/**
 * Prints a help button about a scale
 *
 * @uses $CFG
 * @param id $courseid ?
 * @param object $scale ?
 * @todo Finish documenting this function
 */
function print_scale_menu_helpbutton($courseid, $scale) {

    global $CFG;

    $strscales = get_string('scales');
    $helpicon = $CFG->pixpath .'/help.gif';
    $linkobject = "<img align=\"middle\" border=\"0\" height=\"17\" width=\"22\" alt=\"$scale->name\" src=\"$helpicon\" />";
    link_to_popup_window ('/course/scales.php?id='. $courseid .'&amp;list=true&amp;scale='. $scale->id, 'ratingscale',
                          $linkobject, 400, 500, $scale->name);
}

/**
 * Print an error page displaying an error message.
 *
 * @uses $SESSION
 * @uses $CFG
 * @param string $message The message to display to the user about the error.
 * @param string $link The url where the user will be prompted to continue. If no url is provided the user will be directed to the site index page.
 */
function error ($message, $link='') {
    global $CFG, $SESSION;

    print_header(get_string('error'));
    echo '<br />';

    $message = clean_text($message);   // In case nasties are in here

    print_simple_box($message, 'center', '', '#FFBBBB');

    if (!$link) {
        if ( !empty($SESSION->fromurl) ) {
            $link = $SESSION->fromurl;
            unset($SESSION->fromurl);
        } else {
            $link = $CFG->wwwroot .'/';
        }
    }
    print_continue($link);
    print_footer();
    die;
}

/**
 * Print a help button.
 *
 * @uses $CFG
 * @uses $THEME
 * @param string $page  The keyword that defines a help page
 * @param string $title The title of links, rollover tips, alt tags etc
 * @param string $module Which module is the page defined in
 * @param mixed $image Use a help image for the link?  (true/false/"both")
 * @param string $text If defined then this text is used in the page, and
 *           the $page variable is ignored.
 * @param boolean $return If true then the output is returned as a string, if false it is printed to the current page.
 * @return string
 * @todo Finish documenting this function
 */
function helpbutton ($page, $title='', $module='moodle', $image=true, $linktext=false, $text='', $return=false) {
    global $CFG, $THEME;

    if ($module == '') {
        $module = 'moodle';
    }

    if ($image) {
        $icon = $CFG->pixpath .'/help.gif';
        if ($linktext) {
            $linkobject = "<span style=\"cursor:help;\">$title<img align=\"middle\" border=\"0\" ".
                          " height=\"17\" width=\"22\" alt=\"\" src=\"$icon\" /></span>";
        } else {
            $linkobject = "<img align=\"middle\" border=\"0\" height=\"17\" width=\"22\" ".
                          " alt=\"$title\" style=\"cursor:help;\" src=\"$icon\" />";
        }
    } else {
        $linkobject = '<span style="cursor:help;">'. $title .'</span>';
    }
    if ($text) {
        $url = '/help.php?module='. $module .'&amp;text='. htmlentities(urlencode($text));
    } else {
        $url = '/help.php?module='. $module .'&amp;file='. $page .'.html';
    }

    $link = link_to_popup_window ($url, 'popup', $linkobject, 400, 500, $title, 'none', true);

    if ($return) {
        return $link;
    } else {
        echo $link;
    }
}

/**
 * Print a help button.
 *
 * Prints a special help button that is a link to the "live" emoticon popup
 * @uses $CFG
 * @uses $SESSION
 * @param string $form ?
 * @param string $field ?
 * @todo Finish documenting this function
 */
function emoticonhelpbutton($form, $field) {

    global $CFG, $SESSION;

    $SESSION->inserttextform = $form;
    $SESSION->inserttextfield = $field;
    helpbutton('emoticons', get_string('helpemoticons'), 'moodle', false, true);
    echo '&nbsp;';
    link_to_popup_window ('/help.php?module=moodle&amp;file=emoticons.html', 'popup',
                          '<img src="'. $CFG->pixpath .'/s/smiley.gif" border="0" align="middle" width="15" height="15" alt="" />',
                           400, 500, get_string('helpemoticons'));
    echo '<br />';
}

/**
 * Print a message and exit.
 *
 * @uses $CFG
 * @uses $THEME
 * @param string $message ?
 * @param string $link ?
 * @todo Finish documenting this function
 */
function notice ($message, $link='') {
    global $CFG, $THEME;

    if (!$link) {
        if (!empty($_SERVER['HTTP_REFERER'])) {
            $link = $_SERVER['HTTP_REFERER'];
        } else {
            $link = $CFG->wwwroot .'/';
        }
    }

    echo '<br />';
    print_simple_box($message, 'center', '50%', $THEME->cellheading, '20', 'noticebox');
    print_heading('<a href="'. $link .'">'. get_string('continue') .'</a>');
    print_footer(get_site());
    die;
}

/**
 * Print a message along with "Yes" and "No" links for the user to continue.
 *
 * @uses $THEME
 * @param string $message The text to display
 * @param string $linkyes The link to take the user to if they choose "Yes"
 * @param string $linkno The link to take the user to if they choose "No"
 */
function notice_yesno ($message, $linkyes, $linkno) {
    global $THEME;

    print_simple_box_start('center', '60%', $THEME->cellheading);
    echo '<p align="center"><font size="3">'. $message .'</font></p>';
    echo '<p align="center"><font size="3"><strong>';
    echo '<a href="'. $linkyes .'">'. get_string('yes') .'</a>';
    echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
    echo '<a href="'. $linkno .'">'. get_string('no') .'</a>';
    echo '</strong></font></p>';
    print_simple_box_end();
}

/**
 * Redirects the user to another page, after printing a notice
 *
 * @param string $url The url to take the user to
 * @param string $message The text message to display to the user about the redirect, if any
 * @param string $delay How long before refreshing to the new page at $url?
 * @todo '&' needs to be encoded into '&amp;' for XHTML compliance,
 *      however, this is not true for javascript. Therefore we
 *      first decode all entities in $url (since we cannot rely on)
 *      the correct input) and then encode for where it's needed
 *      echo "<script type='text/javascript'>alert('Redirect $url');</script>";

 */
function redirect($url, $message='', $delay='0') {
    $url = html_entity_decode($url); // for php < 4.3.0 this is defined in moodlelib.php
    $encodedurl = htmlentities($url);
    if (empty($message)) {
        echo '<meta http-equiv="refresh" content="'. $delay .'; url='. $encodedurl .'" />';
        echo '<script type="text/javascript">'. "\n" .'<!--'. "\n". "location.replace('$url');". "\n". '//-->'. "\n". '</script>';   // To cope with Mozilla bug
    } else {
        if (empty($delay)) {
            $delay = 3;  // There's no point having a message with no delay
        }
        print_header('', '', '', '', '<meta http-equiv="refresh" content="'. $delay .'; url='. $encodedurl .'" />');
        echo '<center>';
        echo '<p>'. $message .'</p>';
        echo '<p>( <a href="'. $encodedurl .'">'. get_string('continue') .'</a> )</p>';
        echo '</center>';
        flush();
        sleep($delay);
        echo '<script type="text/javascript">'."\n".'<!--'."\nlocation.replace('$url');\n".'//-->'."\n".'</script>';   // To cope with Mozilla bug
    }
    die;
}

/**
 * Print a bold message in an optional color.
 *
 * @param string $message The message to print out
 * @param string $color Optional color to display message text in
 * @param string $align Paragraph alignment option
 */
function notify ($message, $color='red', $align='center') {
    echo '<p align="'. $align .'"><strong><font color="'. $color .'">'. $message .'</font></strong></p>' . "\n";
}


/**
 * Given an email address, this function will return an obfuscated version of it
 *
 * @param string $email The email address to obfuscate
 * @return string
 */
 function obfuscate_email($email) {

    $i = 0;
    $length = strlen($email);
    $obfuscated = '';
    while ($i < $length) {
        if (rand(0,2)) {
            $obfuscated.='%'.dechex(ord($email{$i}));
        } else {
            $obfuscated.=$email{$i};
        }
        $i++;
    }
    return $obfuscated;
}

/**
 * This function takes some text and replaces about half of the characters
 * with HTML entity equivalents.   Return string is obviously longer.
 *
 * @param string $plaintext The text to be obfuscated
 * @return string
 */
function obfuscate_text($plaintext) {
 
    $i=0;
    $length = strlen($plaintext);
    $obfuscated='';
    $prev_obfuscated = false;
    while ($i < $length) {
        $c = ord($plaintext{$i});
        $numerical = ($c >= ord('0')) && ($c <= ord('9'));
        if ($prev_obfuscated and $numerical ) {
            $obfuscated.='&#'.ord($plaintext{$i}).';';
        } else if (rand(0,2)) {
            $obfuscated.='&#'.ord($plaintext{$i}).';';
            $prev_obfuscated = true;
        } else {
            $obfuscated.=$plaintext{$i};
            $prev_obfuscated = false;
        }
      $i++;
    }
    return $obfuscated;
}

/**
 * This function uses the {@link obfuscate_email()} and {@link obfuscate_text()}
 * to generate a fully obfuscated email link, ready to use.
 *
 * @param string $email The email address to display
 * @param string $label The text to dispalyed as hyperlink to $email
 * @param boolean $dimmed If true then use css class 'dimmed' for hyperlink
 * @return string
 */
function obfuscate_mailto($email, $label='', $dimmed=false) {

    if (empty($label)) {
        $label = $email;
    }
    if ($dimmed) {
        $title = get_string('emaildisable');
        $dimmed = ' class="dimmed"';
    } else {
        $title = '';
        $dimmed = '';
    }
    return sprintf("<a href=\"%s:%s\" $dimmed title=\"$title\">%s</a>",
                    obfuscate_text('mailto'), obfuscate_email($email),
                    obfuscate_text($label));
}

/**
 * Prints a single paging bar to provide access to other pages  (usually in a search)
 *
 * @param int $totalcount ?
 * @param int $page ?
 * @param int $perpage ?
 * @param string $baseurl ?
 * @todo Finish documenting this function
 */
function print_paging_bar($totalcount, $page, $perpage, $baseurl) {

    $maxdisplay = 18;

    if ($totalcount > $perpage) {
        echo '<center>';
        echo '<p>'.get_string('page').':';
        if ($page > 0) {
            $pagenum=$page-1;
            echo '&nbsp;(<a  href="'. $baseurl .'page='. $pagenum .'">'. get_string('previous') .'</a>)&nbsp;';
        }
        $lastpage = ceil($totalcount / $perpage);
        if ($page > 15) {
            $startpage = $page - 10;
            echo '&nbsp<a href="'. $baseurl .'page=0">1</a>&nbsp;...';
        } else {
            $startpage = 0;
        }
        $currpage = $startpage;
        $displaycount = 0;
        while ($displaycount < $maxdisplay and $currpage < $lastpage) {
            $displaypage = $currpage+1;
            if ($page == $currpage) {
                echo '&nbsp;&nbsp;'. $displaypage;
            } else {
                echo '&nbsp;&nbsp;<a href="'. $baseurl .'page='. $currpage .'">'. $displaypage .'</a>';
            }
            $displaycount++;
            $currpage++;
        }
        if ($currpage < $lastpage) {
            $lastpageactual = $lastpage - 1;
            echo '&nbsp;...<a href="'. $baseurl .'page='. $lastpageactual .'">'. $lastpage .'</a>&nbsp;';
        }
        $pagenum = $page + 1;
        if ($pagenum != $displaypage) {
            echo '&nbsp;&nbsp;(<a href="'. $baseurl .'page='. $pagenum .'">'. get_string('next') .'</a>)';
        }
        echo '</p>';
        echo '</center>';
    }
}

/**
 * This function is used to rebuild the <nolink> tag because some formats (PLAIN and WIKI)
 * will transform it to html entities
 *
 * @param string $text Text to search for nolink tag in
 * @return string
 */
function rebuildnolinktag($text) {

    $text = preg_replace('/&lt;(\/*nolink)&gt;/i','<$1>',$text);

    return $text;
}




// ================================================
// THREE FUNCTIONS MOVED HERE FROM course/lib.php
// ================================================

/**
 * Prints a nice side block with an optional header.  The content can either
 * be a block of HTML or a list of text with optional icons.
 *
 * @uses $THEME
 * @param  string $heading ?
 * @param  string $content ?
 * @param  array $list ?
 * @param  array $icons ?
 * @param  string $footer ?
 * @param  array $attributes ?
 * @todo Finish documenting this function. Show example of various attributes, etc.
 */
function print_side_block($heading='', $content='', $list=NULL, $icons=NULL, $footer='', $attributes = array()) { 

    global $THEME;

    print_side_block_start($heading, $attributes);

    if ($content) {
        echo $content;
        if ($footer) {
            echo '<center><font size="-2">'. $footer .'</font></center>';
        }
    } else {
        echo '<table width="100%" border="0" cellspacing="0" cellpadding="2">';
        if ($list) {
            foreach ($list as $key => $string) {
                echo '<tr bgcolor="'. $THEME->cellcontent2 .'">';
                if ($icons) {
                    echo '<td class="sideblocklinks" valign="top" width="16">'. $icons[$key] .'</td>';
                }
                echo '<td class="sideblocklinks" valign="top" width="*"><font size="-1">'. $string .'</font></td>';
                echo '</tr>';
            }
        }
        if ($footer) {
            echo '<tr bgcolor="'. $THEME->cellcontent2 .'">';
            echo '<td class="sideblocklinks" ';
            if ($icons) {
                echo ' colspan="2" ';
            }
            echo '>';
            echo '<center><font size="-2">'. $footer .'</font></center>';
            echo '</td></tr>';
        }
        echo '</table>';
    }

    print_side_block_end();
}

/**
 * Starts a nice side block with an optional header.
 *
 * @uses $THEME
 * @param string $heading ?
 * @param array $attributes ?
 * @todo Finish documenting this function
 */
function print_side_block_start($heading='', $attributes = array()) {
    global $THEME;

    // If there are no special attributes, give a default CSS class
    if(empty($attributes) || !is_array($attributes)) {
        $attributes = array('class' => 'sideblock');
    }
    else if(!isset($attributes['class'])) {
        $attributes['class'] = 'sideblock';
    }
    else if(!strpos($attributes['class'], 'sideblock')) {
        $attributes['class'] .= ' sideblock';
    }
    // OK, the class is surely there and in addition to anything
    // else, it's tagged as a sideblock

    $attrtext = '';
    foreach($attributes as $attr => $val) {
       $attrtext .= ' '.$attr.'="'.$val.'"';
    }

    // [pj] UGLY UGLY UGLY! I hate myself for doing this!
    // When the Lord Moodle 2.0 cometh, his mercy shalt move all this mess
    // to CSS and banish the evil to the abyss from whence it came.
    echo '<table style="width: 100%;" cellspacing="0" cellpadding="5"'.$attrtext.'>';
    if ($heading) {
        echo '<thead><tr><td class="sideblockheading">'.$heading.'</td></tr></thead>';
    }
    echo '<tbody style="background-color: '.$THEME->cellcontent2.';"><tr><td class="sideblockmain">';
}


/**
 * Print table ending tags for a side block box.
 */
function print_side_block_end() {
    echo '</td></tr></tbody></table><br />';
    echo "\n";
}


/**
 * Prints out the HTML editor config.
 *
 * @uses $CFG
 */
 function print_editor_config() {

    global $CFG;

    // print new config
    echo 'var config = new HTMLArea.Config();'."\n";
    echo "config.pageStyle = \"body {";
    if(!(empty($CFG->editorbackgroundcolor))) {
        echo " background-color: $CFG->editorbackgroundcolor;";
    }

    if(!(empty($CFG->editorfontfamily))) {
        echo " font-family: $CFG->editorfontfamily;";
    }

    if(!(empty($CFG->editorfontsize))) {
        echo " font-size: $CFG->editorfontsize;";
    }

    echo " }\";\n";
    echo "config.killWordOnPaste = ";
    echo(empty($CFG->editorkillword)) ? "false":"true";
    echo ';'."\n";
    echo 'config.fontname = {'."\n";

    $fontlist = isset($CFG->editorfontlist) ? explode(';', $CFG->editorfontlist) : array();
    $i = 1;                     // Counter is used to get rid of the last comma.
    $count = count($fontlist);  // Otherwise IE doesn't load the editor.

    foreach($fontlist as $fontline) {
        if(!empty($fontline)) {
            list($fontkey, $fontvalue) = split(':', $fontline);
            echo '"'. $fontkey ."\":\t'". $fontvalue ."'";
            if($i < $count) {
                echo ','."\n";
            }
        }
        $i++;
    }
    echo '};';
    if(!empty($CFG->editorspelling) && !empty($CFG->aspellpath)) {
        print_speller_code($usehtmleditor=true);
    }
}

/**
 * Prints out code needed for spellchecking.
 * Original idea by Ludo (Marc Alier).
 *
 * @uses $CFG
 * @param boolean $usehtmleditor ?
 * @todo Finish documenting this function
 */
function print_speller_code ($usehtmleditor=false) { 
    global $CFG;

    if(!$usehtmleditor) {
        echo "\n".'<script language="javascript" type="text/javascript">'."\n";
        echo 'function openSpellChecker() {'."\n";
        echo "\tvar speller = new spellChecker();\n";
        echo "\tspeller.popUpUrl = \"" . $CFG->wwwroot ."/lib/speller/spellchecker.html\";\n";
        echo "\tspeller.spellCheckScript = \"". $CFG->wwwroot ."/lib/speller/server-scripts/spellchecker.php\";\n";
        echo "\tspeller.spellCheckAll();\n";
        echo '}'."\n";
        echo '</script>'."\n";
    } else {
        echo "\nfunction spellClickHandler(editor, buttonId) {\n";
        echo "\teditor._textArea.value = editor.getHTML();\n";
        echo "\tvar speller = new spellChecker( editor._textArea );\n";
        echo "\tspeller.popUpUrl = \"" . $CFG->wwwroot ."/lib/speller/spellchecker.html\";\n";
        echo "\tspeller.spellCheckScript = \"". $CFG->wwwroot ."/lib/speller/server-scripts/spellchecker.php\";\n";
        echo "\tspeller._moogle_edit=1;\n";
        echo "\tspeller._editor=editor;\n";
        echo "\tspeller.openChecker();\n";
        echo '}'."\n";
    }
}

/**
 * Print button for spellchecking when editor is disabled
 */
function print_speller_button () {
    echo '<input type="button" value="Check spelling" onclick="openSpellChecker();" />'."\n";
}
// vim:autoindent:expandtab:shiftwidth=4:tabstop=4:tw=140:
?>
