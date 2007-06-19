<?php // $Id$

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

/// We are going to uses filterlib functions here
require_once("$CFG->libdir/filterlib.php");

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
 * Deprecated: left here just to note that '3' is not used (at the moment)
 * and to catch any latent wiki-like text (which generates an error)
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
'<p><br><b><i><u><font><table><tbody><span><div><tr><td><th><ol><ul><dl><li><dt><dd><h1><h2><h3><h4><h5><h6><hr><img><a><strong><emphasis><em><sup><sub><address><cite><blockquote><pre><strike><param><acronym><nolink><lang><tex><algebra><math><mi><mn><mo><mtext><mspace><ms><mrow><mfrac><msqrt><mroot><mstyle><merror><mpadded><mphantom><mfenced><msub><msup><msubsup><munder><mover><munderover><mmultiscripts><mtable><mtr><mtd><maligngroup><malignmark><maction><cn><ci><apply><reln><fn><interval><inverse><sep><condition><declare><lambda><compose><ident><quotient><exp><factorial><divide><max><min><minus><plus><power><rem><times><root><gcd><and><or><xor><not><implies><forall><exists><abs><conjugate><eq><neq><gt><lt><geq><leq><ln><log><int><diff><partialdiff><lowlimit><uplimit><bvar><degree><set><list><union><intersect><in><notin><subset><prsubset><notsubset><notprsubset><setdiff><sum><product><limit><tendsto><mean><sdev><variance><median><mode><moment><vector><matrix><matrixrow><determinant><transpose><selector><annotation><semantics><annotation-xml><tt><code>';

/**
 * Allowed protocols - array of protocols that are safe to use in links and so on
 * @global string $ALLOWED_PROTOCOLS
 */
$ALLOWED_PROTOCOLS = array('http', 'https', 'ftp', 'news', 'mailto', 'rtsp', 'teamspeak', 'gopher', 'mms',
                           'color', 'callto', 'cursor', 'text-align', 'font-size', 'font-weight', 'font-style',
                           'border', 'margin', 'padding', 'background');   // CSS as well to get through kses


/// Functions

/**
 * Add quotes to HTML characters
 *
 * Returns $var with HTML characters (like "<", ">", etc.) properly quoted.
 * This function is very similar to {@link p()}
 *
 * @param string $var the string potentially containing HTML characters
 * @param boolean $strip to decide if we want to strip slashes or no. Default to false.
 *                true should be used to print data from forms and false for data from DB.
 * @return string
 */
function s($var, $strip=false) {

    if ($var == '0') {  // for integer 0, boolean false, string '0'
        return '0';
    }

    if ($strip) {
        return preg_replace("/&amp;(#\d+);/i", "&$1;", htmlspecialchars(stripslashes_safe($var)));
    } else {
        return preg_replace("/&amp;(#\d+);/i", "&$1;", htmlspecialchars($var));
    }
}

/**
 * Add quotes to HTML characters
 *
 * Prints $var with HTML characters (like "<", ">", etc.) properly quoted.
 * This function is very similar to {@link s()}
 *
 * @param string $var the string potentially containing HTML characters
 * @param boolean $strip to decide if we want to strip slashes or no. Default to false.
 *                true should be used to print data from forms and false for data from DB.
 * @return string
 */
function p($var, $strip=false) {
    echo s($var, $strip);
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
    global $CFG;

    if (!empty($CFG->disableglobalshack)) {
      error( "The nvl() function is deprecated ($var, $default)." );
    }
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

    global $CFG;

    if (!empty($CFG->wwwroot)) {
        $url = parse_url($CFG->wwwroot);
    }

    if (!empty($url['host'])) {
        $hostname = $url['host'];
    } else if (!empty($_SERVER['SERVER_NAME'])) {
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

    if (!empty($url['port'])) {
        $hostname .= ':'.$url['port'];
    } else if (!empty($_SERVER['SERVER_PORT'])) {
        if ($_SERVER['SERVER_PORT'] != 80 && $_SERVER['SERVER_PORT'] != 443) {
            $hostname .= ':'.$_SERVER['SERVER_PORT'];
        }
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
        // try to remove everything after ? because POST url may contain GET parameters (SID rewrite, etc.)
        $pos = strpos($goodreferer, '?');
        if ($pos !== FALSE) {
            $goodreferer = substr($goodreferer, 0, $pos);
        }
    }

    $referer = get_referer();

    return (($referer == $goodreferer) or ($referer == $CFG->wwwroot .'/') or ($referer == $CFG->wwwroot .'/index.php'));
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
 * Moodle replacement for php stripslashes() function,
 * works also for objects and arrays.
 *
 * The standard php stripslashes() removes ALL backslashes
 * even from strings - so  C:\temp becomes C:temp - this isn't good.
 * This function should work as a fairly safe replacement
 * to be called on quoted AND unquoted strings (to be sure)
 *
 * @param mixed something to remove unsafe slashes from
 * @return mixed
 */
function stripslashes_safe($mixed) {
    // there is no need to remove slashes from int, float and bool types
    if (empty($mixed)) {
        //nothing to do...
    } else if (is_string($mixed)) {
        $mixed = str_replace("\\'", "'", $mixed);
        $mixed = str_replace('\\"', '"', $mixed);
        $mixed = str_replace('\\\\', '\\', $mixed);
    } else if (is_array($mixed)) {
        foreach ($mixed as $key => $value) {
            $mixed[$key] = stripslashes_safe($value);
        }
    } else if (is_object($mixed)) {
        $vars = get_object_vars($mixed);
        foreach ($vars as $key => $value) {
            $mixed->$key = stripslashes_safe($value);
        }
    }

    return $mixed;
}

/**
 * Recursive implementation of stripslashes()
 *
 * This function will allow you to strip the slashes from a variable.
 * If the variable is an array or object, slashes will be stripped
 * from the items (or properties) it contains, even if they are arrays
 * or objects themselves.
 *
 * @param mixed the variable to remove slashes from
 * @return mixed
 */
function stripslashes_recursive($var) {
    if(is_object($var)) {
        $properties = get_object_vars($var);
        foreach($properties as $property => $value) {
            $var->$property = stripslashes_recursive($value);
        }
    }
    else if(is_array($var)) {
        foreach($var as $property => $value) {
            $var[$property] = stripslashes_recursive($value);
        }
    }
    else if(is_string($var)) {
        $var = stripslashes($var);
    }
    return $var;
}

/**
 * Given some normal text this function will break up any
 * long words to a given size by inserting the given character
 *
 * It's multibyte savvy and doesn't change anything inside html tags.
 *
 * @param string $string the string to be modified
 * @param int $maxsize maximum length of the string to be returned
 * @param string $cutchar the string used to represent word breaks
 * @return string
 */
function break_up_long_words($string, $maxsize=20, $cutchar=' ') {

/// Loading the textlib singleton instance. We are going to need it.
    $textlib = textlib_get_instance();

/// First of all, save all the tags inside the text to skip them
    $tags = array();
    filter_save_tags($string,$tags);

/// Process the string adding the cut when necessary
    $output = '';
    $length = $textlib->strlen($string, current_charset());
    $wordlength = 0;

    for ($i=0; $i<$length; $i++) {
        $char = $textlib->substr($string, $i, 1, current_charset());
        if ($char == ' ' or $char == "\t" or $char == "\n" or $char == "\r" or $char == "<" or $char == ">") {
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

/// Finally load the tags back again
    if (!empty($tags)) {
        $output = str_replace(array_keys($tags), $tags, $output);
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
        $url = substr($url, strlen($CFG->wwwroot));
    }

    $link = '<a target="'. $name .'" title="'. $title .'" href="'. $CFG->wwwroot . $url .'" '.
           "onclick=\"return openpopup('$url', '$name', '$options', $fullscreen);\">$linkname</a>";
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
 * @param string $return If true, return as a string, otherwise print
 * @return string
 * @uses $CFG
 */
function button_to_popup_window ($url, $name='popup', $linkname='click here',
                                 $height=400, $width=500, $title='Popup window', $options='none', $return=false,
                                 $id='', $class='') {

    global $CFG;

    if ($options == 'none') {
        $options = 'menubar=0,location=0,scrollbars,resizable,width='. $width .',height='. $height;
    }

    if ($id) {
        $id = ' id="'.$id.'" ';
    }
    if ($class) {
        $class = ' class="'.$class.'" ';
    }
    $fullscreen = 0;

    $button = '<input type="button" name="'.$name.'" title="'. $title .'" value="'. $linkname .' ..." '.$id.$class.
              "onclick=\"return openpopup('$url', '$name', '$options', $fullscreen);\" />\n";
    if ($return) {
        return $button;
    } else {
        echo $button;
    }
}


/**
 * Prints a simple button to close a window
 */
function close_window_button($name='closewindow') {

    echo '<center>' . "\n";
    echo '<script type="text/javascript">' . "\n";
    echo '<!--' . "\n";
    echo "document.write('<form>');\n";
    echo "document.write('<input type=\"button\" onclick=\"self.close();\" value=\"".get_string("closewindow")."\" />');\n";
    echo "document.write('<\/form>');\n";
    echo '-->' . "\n";
    echo '</script>' . "\n";
    echo '<noscript>' . "\n";
    print_string($name);
    echo '</noscript>' . "\n";
    echo '</center>' . "\n";
}

/*
 * Try and close the current window immediately using Javascript
 */
function close_window($delay=0) {
?>
<script type="text/javascript">
<!--
    function close_this_window() {
        self.close();
    }
    setTimeout("close_this_window()", <?php echo $delay * 1000 ?>);
-->
</script>
<noscript><center>
<?php print_string('pleaseclose') ?>
</center></noscript>
<?php
    die;
}


/**
 * Given an array of value, creates a popup menu to be part of a form
 * $options["value"]["label"]
 *
 * @param    type description
 * @todo Finish documenting this function
 */
function choose_from_menu ($options, $name, $selected='', $nothing='choose', $script='',
                           $nothingvalue='0', $return=false, $disabled=false, $tabindex=0) {

    if ($nothing == 'choose') {
        $nothing = get_string('choose') .'...';
    }

    $attributes = ($script) ? 'onchange="'. $script .'"' : '';
    if ($disabled) {
        $attributes .= ' disabled="disabled"';
    }

    if ($tabindex) {
        $attributes .= ' tabindex="'.$tabindex.'"';
    }

    $output = '<select id="menu'.$name.'" name="'. $name .'" '. $attributes .'>' . "\n";
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
            if ((string)$value == (string)$selected) {
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
 * Just like choose_from_menu, but takes a nested array (2 levels) and makes a dropdown menu
 * including option headings with the first level.
 */
function choose_from_menu_nested($options,$name,$selected='',$nothing='choose',$script = '',
                                 $nothingvalue=0,$return=false,$disabled=false,$tabindex=0) {

   if ($nothing == 'choose') {
        $nothing = get_string('choose') .'...';
    }

    $attributes = ($script) ? 'onchange="'. $script .'"' : '';
    if ($disabled) {
        $attributes .= ' disabled="disabled"';
    }

    if ($tabindex) {
        $attributes .= ' tabindex="'.$tabindex.'"';
    }

    $output = '<select id="menu'.$name.'" name="'. $name .'" '. $attributes .'>' . "\n";
    if ($nothing) {
        $output .= '   <option value="'. $nothingvalue .'"'. "\n";
        if ($nothingvalue === $selected) {
            $output .= ' selected="selected"';
        }
        $output .= '>'. $nothing .'</option>' . "\n";
    }
    if (!empty($options)) {
        foreach ($options as $section => $values) {
            $output .= '   <optgroup label="'.$section.'">'."\n";
            foreach ($values as $value => $label) {
                $output .= '   <option value="'. $value .'"';
                if ((string)$value == (string)$selected) {
                    $output .= ' selected="selected"';
                }
                if ($label === '') {
                    $output .= '>'. $value .'</option>' . "\n";
                } else {
                    $output .= '>'. $label .'</option>' . "\n";
                }
            }
            $output .= '   </optgroup>'."\n";
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
 * Given an array of values, creates a group of radio buttons to be part of a form
 *
 * @param array  $options  An array of value-label pairs for the radio group (values as keys)
 * @param string $name     Name of the radiogroup (unique in the form)
 * @param string $checked  The value that is already checked
 */
function choose_from_radio ($options, $name, $checked='') {

    static $idcounter = 0;

    if (!$name) {
        $name = 'unnamed';
    }

    $output = '<span class="radiogroup '.$name."\">\n";

    if (!empty($options)) {
        $currentradio = 0;
        foreach ($options as $value => $label) {
            $htmlid = 'auto-rb'.sprintf('%04d', ++$idcounter);
            $output .= ' <span class="radioelement '.$name.' rb'.$currentradio."\">";
            $output .= '<input name="'.$name.'" id="'.$htmlid.'" type="radio" value="'.$value.'"';
            if ($value == $checked) {
                $output .= ' checked="checked"';
            }
            if ($label === '') {
                $output .= ' /> <label for="'.$htmlid.'">'.  $value .'</label></span>' .  "\n";
            } else {
                $output .= ' /> <label for="'.$htmlid.'">'.  $label .'</label></span>' .  "\n";
            }
            $currentradio = ($currentradio + 1) % 2;
        }
    }

    $output .= '</span>' .  "\n";

    echo $output;
}

/** Display an standard html checkbox with an optional label
 *
 * @param string  $name    The name of the checkbox
 * @param string  $value   The valus that the checkbox will pass when checked
 * @param boolean $checked The flag to tell the checkbox initial state
 * @param string  $label   The label to be showed near the checkbox
 * @param string  $alt     The info to be inserted in the alt tag
 */
function print_checkbox ($name, $value, $checked = true, $label = '', $alt = '', $script='',$return=false) {

    static $idcounter = 0;

    if (!$name) {
        $name = 'unnamed';
    }

    if (!$alt) {
        $alt = 'checkbox';
    }

    if ($checked) {
        $strchecked = ' checked="checked"';
    } else {
        $strchecked = '';
    }

    $htmlid = 'auto-cb'.sprintf('%04d', ++$idcounter);
    $output  = '<span class="checkbox '.$name."\">";
    $output .= '<input name="'.$name.'" id="'.$htmlid.'" type="checkbox" value="'.$value.'" alt="'.$alt.'"'.$strchecked.' '.((!empty($script)) ? ' onClick="'.$script.'" ' : '').' />';
    if(!empty($label)) {
        $output .= ' <label for="'.$htmlid.'">'.$label.'</label>';
    }
    $output .= '</span>'."\n";

    if (empty($return)) {
        echo $output;
    } else {
        return $output;
    }

}

/** Display an standard html text field with an optional label
 *
 * @param string  $name    The name of the text field
 * @param string  $value   The value of the text field
 * @param string  $label   The label to be showed near the text field
 * @param string  $alt     The info to be inserted in the alt tag
 */
function print_textfield ($name, $value, $alt = '',$size=50,$maxlength= 0,$return=false) {

    static $idcounter = 0;

    if (empty($name)) {
        $name = 'unnamed';
    }

    if (empty($alt)) {
        $alt = 'textfield';
    }

    if (!empty($maxlength)) {
        $maxlength = ' maxlength="'.$maxlength.'" ';
    }

    $htmlid = 'auto-tf'.sprintf('%04d', ++$idcounter);
    $output  = '<span class="textfield '.$name."\">";
    $output .= '<input name="'.$name.'" id="'.$htmlid.'" type="text" value="'.$value.'" size="'.$size.'" '.$maxlength.' alt="'.$alt.'" />';

    $output .= '</span>'."\n";

    if (empty($return)) {
        echo $output;
    } else {
        return $output;
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
                        ' class="popupform">';

    $output = '<select name="jump" onchange="'.$targetwindow.'.location=document.'.$formname.
                       '.jump.options[document.'.$formname.'.jump.selectedIndex].value;">'."\n";

    if ($nothing != '') {
        $output .= "   <option value=\"javascript:void(0)\">$nothing</option>\n";
    }

    $inoptgroup = false;
    foreach ($options as $value => $label) {

        if (substr($label,0,2) == '--') { /// we are starting a new optgroup

            /// Check to see if we already have a valid open optgroup
            /// XHTML demands that there be at least 1 option within an optgroup
            if ($inoptgroup and (count($optgr) > 1) ) {
                $output .= implode('', $optgr);
                $output .= '   </optgroup>';
            }

            unset($optgr);
            $optgr = array();

            $optgr[]  = '   <optgroup label="'. substr($label,2) .'">';   // Plain labels

            $inoptgroup = true; /// everything following will be in an optgroup
            continue;

        } else {
           if (!empty($CFG->usesid) && !isset($_COOKIE[session_name()]))
            {
                $url=sid_process_url( $common . $value );
            } else
            {
                $url=$common . $value;
            }
            $optstr = '   <option value="' . $url . '"';

            if ($value == $selected) {
                $optstr .= ' selected="selected"';
            }

            if ($label) {
                $optstr .= '>'. $label .'</option>' . "\n";
            } else {
                $optstr .= '>'. $value .'</option>' . "\n";
            }

            if ($inoptgroup) {
                $optgr[] = $optstr;
            } else {
                $output .= $optstr;
            }
        }

    }

    /// catch the final group if not closed
    if ($inoptgroup and count($optgr) > 1) {
        $output .= implode('', $optgr);
        $output .= '    </optgroup>';
    }

    $output .= '</select>';
    $output .= '<input type="hidden" name="sesskey" value="'.sesskey().'" />';
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

    return (ereg('^[-!#$%&\'*+\\/0-9=?A-Z^_`a-z{|}~]+'.
                 '(\.[-!#$%&\'*+\\/0-9=?A-Z^_`a-z{|}~]+)*'.
                  '@'.
                  '[-!#$%&\'*+\\/0-9=?A-Z^_`a-z{|}~]+\.'.
                  '[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+$',
                  $address));
}

/**
 * Extracts file argument either from file parameter or PATH_INFO
 *
 * @param string $scriptname name of the calling script
 * @return string file path (only safe characters)
 */
function get_file_argument($scriptname) {
    global $_SERVER;

    $relativepath = FALSE;

    // first try normal parameter (compatible method == no relative links!)
    $relativepath = optional_param('file', FALSE, PARAM_PATH);
    if ($relativepath === '/testslasharguments') {
        echo 'test -1      : Incorrect use - try "file.php/testslasharguments" instead'; //indicate fopen/fread works for health center
        die;
    }

    // then try extract file from PATH_INFO (slasharguments method)
    if (!$relativepath and !empty($_SERVER['PATH_INFO'])) {
        $path_info = $_SERVER['PATH_INFO'];
        // check that PATH_INFO works == must not contain the script name
        if (!strpos($path_info, $scriptname)) {
            $relativepath = clean_param(rawurldecode($path_info), PARAM_PATH);
            if ($relativepath === '/testslasharguments') {
                echo 'test 1      : Slasharguments test passed. Server confguration is compatible with file.php/1/pic.jpg slashargument setting.'; //indicate ok for health center
                die;
            }
        }
    }

    // now if both fail try the old way
    // (for compatibility with misconfigured or older buggy php implementations)
    if (!$relativepath) {
        $arr = explode($scriptname, me());
        if (!empty($arr[1])) {
            $path_info = strip_querystring($arr[1]);
            $relativepath = clean_param(rawurldecode($path_info), PARAM_PATH);
            if ($relativepath === '/testslasharguments') {
                echo 'test 2      : Slasharguments test passed (compatibility hack). Server confguration may be compatible with file.php/1/pic.jpg slashargument setting'; //indicate ok for health center
                die;
            }
        }
    }

    return $relativepath;
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
        return addslashes($pathinfo[1]);
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
 * @uses FORMAT_MARKDOWN
 * @return array
 */
function format_text_menu() {

    return array (FORMAT_MOODLE => get_string('formattext'),
                  FORMAT_HTML   => get_string('formathtml'),
                  FORMAT_PLAIN  => get_string('formatplain'),
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

    if (!isset($options->noclean)) {
        $options->noclean=false;
    }
    if (!isset($options->smiley)) {
        $options->smiley=true;
    }
    if (!isset($options->filter)) {
        $options->filter=true;
    }
    if (!isset($options->para)) {
        $options->para=true;
    }
    if (!isset($options->newlines)) {
        $options->newlines=true;
    }

    if (empty($courseid)) {
        if (!empty($course->id)) {         // An ugly hack for better compatibility
            $courseid = $course->id;
        }
    }

    if (!empty($CFG->cachetext)) {
        $time = time() - $CFG->cachetext;
        $md5key = md5($text.'-'.$courseid.$options->noclean.$options->smiley.$options->filter.$options->para.$options->newlines.$format.current_language().$courseid);
        if ($oldcacheitem = get_record_sql('SELECT * FROM '.$CFG->prefix.'cache_text WHERE md5key = \''.$md5key.'\'', true)) {
            if ($oldcacheitem->timemodified >= $time) {
                return $oldcacheitem->formattedtext;
            }
        }
    }

    $CFG->currenttextiscacheable = true;   // Default status - can be changed by any filter

    switch ($format) {
        case FORMAT_HTML:
            if (!empty($options->smiley)) {
                replace_smilies($text);
            }
            if (empty($options->noclean)) {
                $text = clean_text($text, $format);
            }
            if (!empty($options->filter)) {
                $text = filter_text($text, $courseid);
            }
            break;

        case FORMAT_PLAIN:
            $text = s($text);
            $text = rebuildnolinktag($text);
            $text = str_replace('  ', '&nbsp; ', $text);
            $text = nl2br($text);
            break;

        case FORMAT_WIKI:
            // this format is deprecated
            $text = '<p>NOTICE: Wiki-like formatting has been removed from Moodle.  You should not be seeing
                     this message as all texts should have been converted to Markdown format instead.
                     Please post a bug report to http://moodle.org/bugs with information about where you
                     saw this message.</p>'.s($text);
            break;

        case FORMAT_MARKDOWN:
            $text = markdown_to_html($text);
            if (!empty($options->smiley)) {
                replace_smilies($text);
            }
            if (empty($options->noclean)) {
                $text = clean_text($text, $format);
            }

            if (!empty($options->filter)) {
                $text = filter_text($text, $courseid);
            }
            break;

        default:  // FORMAT_MOODLE or anything else
            $text = text_to_html($text, $options->smiley, $options->para, $options->newlines);
            if (empty($options->noclean)) {
                $text = clean_text($text, $format);
            }

            if (!empty($options->filter)) {
                $text = filter_text($text, $courseid);
            }
            break;
    }

    if (!empty($CFG->cachetext) and $CFG->currenttextiscacheable) {
        $newcacheitem->md5key = $md5key;
        $newcacheitem->formattedtext = addslashes($text);
        $newcacheitem->timemodified = time();
        if ($oldcacheitem) {                               // See bug 4677 for discussion
            $newcacheitem->id = $oldcacheitem->id;
            @update_record('cache_text', $newcacheitem);   // Update existing record in the cache table
                                                           // It's unlikely that the cron cache cleaner could have
                                                           // deleted this entry in the meantime, as it allows
                                                           // some extra time to cover these cases.
        } else {
            @insert_record('cache_text', $newcacheitem);   // Insert a new record in the cache table
                                                           // Again, it's possible that another user has caused this
                                                           // record to be created already in the time that it took
                                                           // to traverse this function.  That's OK too, as the
                                                           // call above handles duplicate entries, and eventually
                                                           // the cron cleaner will delete them.
        }
    }

    return $text;
}

/** Converts the text format from the value to the 'internal'
 *  name or vice versa. $key can either be the value or the name
 *  and you get the other back.
 *
 *  @param mixed int 0-4 or string one of 'moodle','html','plain','markdown'
 *  @return mixed as above but the other way around!
 */
function text_format_name( $key ) {
  $lookup = array();
  $lookup[FORMAT_MOODLE] = 'moodle';
  $lookup[FORMAT_HTML] = 'html';
  $lookup[FORMAT_PLAIN] = 'plain';
  $lookup[FORMAT_MARKDOWN] = 'markdown';
  $value = "error";
  if (!is_numeric($key)) {
    $key = strtolower( $key );
    $value = array_search( $key, $lookup );
  }
  else {
    if (isset( $lookup[$key] )) {
      $value =  $lookup[ $key ];
    }
  }
  return $value;
}

/** Given a simple string, this function returns the string
 *  processed by enabled filters if $CFG->filterall is enabled
 *
 *  @param string  $string     The string to be filtered.
 *  @param boolean $striplinks To strip any link in the result text.
 *  @param int     $courseid   Current course as filters can, potentially, use it
 *  @return string
 */
function format_string ($string, $striplinks = false, $courseid=NULL ) {

    global $CFG, $course;

    //We'll use a in-memory cache here to speed up repeated strings
    static $strcache;

    //Calculate md5
    $md5 = md5($string.'<+>'.$striplinks);

    //Fetch from cache if possible
    if(isset($strcache[$md5])) {
        return $strcache[$md5];
    }

    if (empty($courseid)) {
        if (!empty($course->id)) {         // An ugly hack for better compatibility
            $courseid = $course->id;       // (copied from format_text)
        }
    }

    if (!empty($CFG->filterall)) {
        $string = filter_text($string, $courseid);
    }

    if ($striplinks) {  //strip links in string
        $string = preg_replace('/(<a[^>]+?>)(.+?)(<\/a>)/is','$2',$string);
    }

    //Store to cache
    $strcache[$md5] = $string;

    return $string;
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

    require_once($CFG->libdir.'/filterlib.php');
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

    /// <nolink> tags removed for XHTML compatibility
    $text = str_replace('<nolink>', '', $text);
    $text = str_replace('</nolink>', '', $text);

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

        /// Fix non standard entity notations
            $text = preg_replace('/(&#[0-9]+)(;?)/', "\\1;", $text);
            $text = preg_replace('/(&#x[0-9a-fA-F]+)(;?)/', "\\1;", $text);

        /// Remove tags that are not allowed
            $text = strip_tags($text, $ALLOWED_TAGS);

        /// Clean up embedded scripts and , using kses
            $text = cleanAttributes($text);

        /// Remove script events
            $text = eregi_replace("([^a-z])language([[:space:]]*)=", "\\1Xlanguage=", $text);
            $text = eregi_replace("([^a-z])on([a-z]+)([[:space:]]*)=", "\\1Xon\\2=", $text);

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
    $result = preg_replace_callback(
            '%(<[^>]*(>|$)|>)%m', #search for html tags
            "cleanAttributes2",
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
 * @param array $htmlArray An array from {@link cleanAttributes()}, containing in its 1st
 *              element the html to be cleared
 * @return string
 */
function cleanAttributes2($htmlArray){

    global $CFG, $ALLOWED_PROTOCOLS;
    require_once($CFG->libdir .'/kses.php');

    $htmlTag = $htmlArray[1];
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
        $arreach['name'] = strtolower($arreach['name']);
        if ($arreach['name'] == 'style') {
            $value = $arreach['value'];
            while (true) {
                $prevvalue = $value;
                $value = kses_no_null($value);
                $value = preg_replace("/\/\*.*\*\//Us", '', $value);
                $value = kses_decode_entities($value);
                $value = preg_replace('/(&#[0-9]+)(;?)/', "\\1;", $value);
                $value = preg_replace('/(&#x[0-9a-fA-F]+)(;?)/', "\\1;", $value);
                if ($value === $prevvalue) {
                    $arreach['value'] = $value;
                    break;
                }
            }
            $arreach['value'] = preg_replace("/j\s*a\s*v\s*a\s*s\s*c\s*r\s*i\s*p\s*t/i", "Xjavascript", $arreach['value']);
            $arreach['value'] = preg_replace("/e\s*x\s*p\s*r\s*e\s*s\s*s\s*i\s*o\s*n/i", "Xexpression", $arreach['value']);
        } else if ($arreach['name'] == 'href') {
            //Adobe Acrobat Reader XSS protection
            $arreach['value'] = preg_replace('/(\.(pdf|fdf|xfdf|xdp|xfd))[^a-z0-9_\.\-].*$/i', '$1', $arreach['value']);
        }
        $attStr .=  ' '.$arreach['name'].'="'.$arreach['value'].'" ';
    }

    // Remove last space from attribute list
    $attStr = rtrim($attStr);

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
                          "\\1<a href=\"\\2://\\3\\4\" target=\"_blank\">\\2://\\3\\4</a>", $text);

/// eg www.moodle.com
    $text = eregi_replace("([[:space:]]|^|\(|\[)www\.([^[:space:]]*)([[:alnum:]#?/&=])",
                          "\\1<a href=\"http://www.\\2\\3\" target=\"_blank\">www.\\2\\3</a>", $text);
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

    //$list_of_words = eregi_replace("[^-a-zA-Z0-9&.']", " ", $needle);  // bug 3101
    $list_of_words = $needle;
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

    $list_of_words_cp = trim($list_of_words_cp);

    if ($list_of_words_cp) {

      $list_of_words_cp = "(". $list_of_words_cp .")";

      if (!$case){
        $haystack = eregi_replace("$list_of_words_cp", "$left_string"."\\1"."$right_string", $haystack);
      } else {
        $haystack = ereg_replace("$list_of_words_cp", "$left_string"."\\1"."$right_string", $haystack);
      }
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

    global $USER, $CFG, $THEME, $SESSION, $ME, $SITE, $HTTPSPAGEREQUIRED;

/// This makes sure that the header is never repeated twice on a page
    if (defined('HEADER_PRINTED')) {
        if ($CFG->debug > 7) {
            notify('print_header() was called more than once - this should not happen.  Please check the code for this page closely. Note: error() and redirect() are now safe to call after print_header().');
        }
        return;
    }
    define('HEADER_PRINTED', 'true');


    global $course, $COURSE;
    if (!empty($COURSE->lang)) {
        $CFG->courselang = $COURSE->lang;
        moodle_setlocale();
    } else if (!empty($course->lang)) { // ugly backwards compatibility hack
        $CFG->courselang = $course->lang;
        moodle_setlocale();
    }
    if (!empty($COURSE->theme)) {
        if (!empty($CFG->allowcoursethemes)) {
            $CFG->coursetheme = $COURSE->theme;
            theme_setup();
        }
    } else if (!empty($course->theme)) { // ugly backwards compatibility hack
        if (!empty($CFG->allowcoursethemes)) {
            $CFG->coursetheme = $course->theme;
            theme_setup();
        }
    }

/// We have to change some URLs in styles if we are in a $HTTPSPAGEREQUIRED page
    if (!empty($HTTPSPAGEREQUIRED)) {
        $CFG->themewww = str_replace('http:', 'https:', $CFG->themewww);
        $CFG->pixpath = str_replace('http:', 'https:', $CFG->pixpath);
        $CFG->modpixpath = str_replace('http:', 'https:', $CFG->modpixpath);
        foreach ($CFG->stylesheets as $key => $stylesheet) {
            $CFG->stylesheets[$key] = str_replace('http:', 'https:', $stylesheet);
        }
    }

/// Add the required stylesheets
    $stylesheetshtml = '';
    foreach ($CFG->stylesheets as $stylesheet) {
        $stylesheetshtml .= '<link rel="stylesheet" type="text/css" href="'.$stylesheet.'" />'."\n";
    }
    $meta = $stylesheetshtml.$meta;


    if ($navigation == 'home') {
        $home = true;
        $navigation = '';
    } else {
        $home = false;
    }

/// This is another ugly hack to make navigation elements available to print_footer later
    $THEME->title      = $title;
    $THEME->heading    = $heading;
    $THEME->navigation = $navigation;
    $THEME->button     = $button;
    $THEME->menu       = $menu;
    $navmenulist = isset($THEME->navmenulist) ? $THEME->navmenulist : '';

    if ($button == '') {
        $button = '&nbsp;';
    }

    if (!$menu and $navigation) {
        if (empty($CFG->loginhttps)) {
            $wwwroot = $CFG->wwwroot;
        } else {
            $wwwroot = str_replace('http:','https:',$CFG->wwwroot);
        }
        if (isset($course->id)) {
            $menu = user_login_string($course);
        } else {
            $menu = user_login_string($SITE);
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
                        $menu .= ' (<a href="'.$CFG->wwwroot.'/course/report/log/index.php'.
                                             '?chooselog=1&amp;id=1&amp;modid=site_errors">'.get_string('logs').'</a>)';
                    }
                    $menu .= '</font>';
                }
            }
        }
    }


    $encoding = current_charset();

    $meta = '<meta http-equiv="content-type" content="text/html; charset='. $encoding .'" />'. "\n". $meta ."\n";
    if (!$usexml) {
        @header('Content-type: text/html; charset='.$encoding);
    }

    if ( get_string('thisdirection') == 'rtl' ) {
        $direction = ' dir="rtl"';
    } else {
        $direction = ' dir="ltr"';
    }
    //Accessibility: added the 'lang' attribute to $direction, used in theme <html> tag.
    $language = str_replace('_utf8','',$CFG->lang);
    $direction .= ' lang="'.$language.'" xml:lang="'.$language.'"';

    if ($cache) {  // Allow caching on "back" (but not on normal clicks)
        @header('Cache-Control: private, pre-check=0, post-check=0, max-age=0');
        @header('Pragma: no-cache');
        @header('Expires: ');
    } else {       // Do everything we can to always prevent clients and proxies caching
        @header('Cache-Control: no-store, no-cache, must-revalidate');
        @header('Cache-Control: post-check=0, pre-check=0', false);
        @header('Pragma: no-cache');
        @header('Expires: Mon, 20 Aug 1969 09:23:00 GMT');
        @header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');

        $meta .= "\n<meta http-equiv=\"pragma\" content=\"no-cache\" />";
        $meta .= "\n<meta http-equiv=\"expires\" content=\"0\" />";
    }
    @header('Accept-Ranges: none');

    $currentlanguage = current_language();

    if ($usexml) {       // Added by Gustav Delius / Mad Alex for MathML output
                         // Modified by Julian Sedding
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
                       xmlns:xlink=\"http://www.w3.org/1999/xlink\"
                       $direction";
        if($mathplayer) {
            $meta .= '<object id="mathplayer" classid="clsid:32F66A20-7614-11D4-BD11-00104BD3F987">' . "\n";
            $meta .= '<!--comment required to prevent this becoming an empty tag-->'."\n";
            $meta .= '</object>'."\n";
            $meta .= '<?import namespace="math" implementation="#mathplayer" ?>' . "\n";
        }
    }

    // Clean up the title

    $title = str_replace('"', '&quot;', $title);
    $title = strip_tags($title);

    // Create class and id for this page

    page_id_and_class($pageid, $pageclass);

    if (isset($course->id)) {
        $pageclass .= ' course-'.$course->id;
    } else {
        $pageclass .= ' course-'.SITEID;
    }

    if (!empty($USER->editing)) {
        $pageclass .= ' editing';
    }

    $pageclass .= ' lang-'.$currentlanguage;

    $bodytags .= ' class="'.$pageclass.'" id="'.$pageid.'"';

    include ($CFG->themedir.current_theme().'/header.html');

    if (!empty($CFG->messaging)) {
        echo message_popup_window();
    }
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

    global $course,$CFG;                // The same hack is used in print_header

    $shortname ='';
    if ($course->category) {
        $shortname = '<a href="'.$CFG->wwwroot.'/course/view.php?id='. $course->id .'">'. $course->shortname .'</a> ->';
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
 * @param course $course {@link $COURSE} object containing course information
 * @param ? $usercourse ?
 * @todo Finish documenting this function
 */
function print_footer($course=NULL, $usercourse=NULL) {
    global $USER, $CFG, $THEME;

/// Course links
    if ($course) {
        if (is_string($course) && $course == 'none') {          // Don't print any links etc
            $homelink = '';
            $loggedinas = '';
            $home  = false;
        } else if (is_string($course) && $course == 'home') {   // special case for site home page - please do not remove
            $course = get_site();
            $homelink  = '<div class="sitelink">'.
               '<a title="moodle '. $CFG->release .' ('. $CFG->version .')" href="http://moodle.org/" target="_blank">'.
               '<br /><img width="100" height="30" src="pix/moodlelogo.gif" border="0" alt="moodlelogo" /></a></div>';
            $home  = true;
        } else {
            $homelink = '<div class="homelink"><a target="'.$CFG->framename.'" href="'.$CFG->wwwroot.
                        '/course/view.php?id='.$course->id.'">'.$course->shortname.'</a></div>';
            $home  = false;
        }
    } else {
        $course = get_site();  // Set course as site course by default
        $homelink = '<div class="homelink"><a target="'.$CFG->framename.'" href="'.$CFG->wwwroot.'/">'.get_string('home').'</a></div>';
        $home  = false;
    }

/// Set up some other navigation links (passed from print_header by ugly hack)
    $menu        = isset($THEME->menu) ? str_replace('navmenu', 'navmenufooter', $THEME->menu) : '';
    $title       = isset($THEME->title) ? $THEME->title : '';
    $button      = isset($THEME->button) ? $THEME->button : '';
    $heading     = isset($THEME->heading) ? $THEME->heading : '';
    $navigation  = isset($THEME->navigation) ? $THEME->navigation : '';
    $navmenulist = isset($THEME->navmenulist) ? $THEME->navmenulist : '';


/// Set the user link if necessary
    if (!$usercourse and is_object($course)) {
        $usercourse = $course;
    }

    if (!isset($loggedinas)) {
        $loggedinas = user_login_string($usercourse, $USER);
    }

    if ($loggedinas == $menu) {
        $menu = '';
    }

/// Provide some performance info if required
    $performanceinfo = '';
    if (defined('MDL_PERF') || $CFG->perfdebug > 7) {
        $perf = get_performance_info();
        if (defined('MDL_PERFTOLOG')) {
            error_log("PERF: " . $perf['txt']);
        }
        if (defined('MDL_PERFTOFOOT') || $CFG->debug > 7 || $CFG->perfdebug > 7) {
            $performanceinfo = $perf['html'];
        }
    }


/// Include the actual footer file

    include ($CFG->themedir.current_theme().'/footer.html');

}

/**
 * Returns the name of the current theme
 *
 * @uses $CFG
 * @param $USER
 * @param $SESSION
 * @return string
 */
function current_theme() {
    global $CFG, $USER, $SESSION, $course;

    if (!empty($CFG->pagetheme)) {  // Page theme is for special page-only themes set by code
        return $CFG->pagetheme;

    } else if (!empty($CFG->coursetheme) and !empty($CFG->allowcoursethemes)) {  // Course themes override others
        return $CFG->coursetheme;

    } else if (!empty($SESSION->theme)) {    // Session theme can override other settings
        return $SESSION->theme;

    } else if (!empty($USER->theme) and !empty($CFG->allowuserthemes)) {    // User theme can override site theme
        return $USER->theme;

    } else {
        return $CFG->theme;
    }
}


/**
 * This function is called by stylesheets to set up the header
 * approriately as well as the current path
 *
 * @uses $CFG
 * @param int $lastmodified ?
 * @param int $lifetime ?
 * @param string $thename ?
 */
function style_sheet_setup($lastmodified=0, $lifetime=300, $themename='', $forceconfig='', $lang='') {

    global $CFG, $THEME;

    // Fix for IE6 caching - we don't want the filemtime('styles.php'), instead use now.
    $lastmodified = time();

    header('Last-Modified: ' . gmdate("D, d M Y H:i:s", $lastmodified) . ' GMT');
    header('Expires: ' . gmdate("D, d M Y H:i:s", time() + $lifetime) . ' GMT');
    header('Cache-Control: max-age='. $lifetime);
    header('Pragma: ');
    header('Content-type: text/css');  // Correct MIME type

    $DEFAULT_SHEET_LIST = array('styles_layout', 'styles_fonts', 'styles_color');

    if (empty($themename)) {
        $themename = current_theme();  // So we have something.  Normally not needed.
    } else {
        $themename = clean_param($themename, PARAM_SAFEDIR);
    }

    if (!empty($forceconfig)) {        // Page wants to use the config from this theme instead
        unset($THEME);
        include($CFG->themedir.$forceconfig.'/'.'config.php');
    }

/// If this is the standard theme calling us, then find out what sheets we need

    if ($themename == 'standard') {
        if (!isset($THEME->standardsheets) or $THEME->standardsheets === true) { // Use all the sheets we have
            $THEME->sheets = $DEFAULT_SHEET_LIST;
        } else if (empty($THEME->standardsheets)) {                              // We can stop right now!
            echo "/***** Nothing required from this stylesheet by main theme *****/\n\n";
            exit;
        } else {                                                                 // Use the provided subset only
            $THEME->sheets = $THEME->standardsheets;
        }

/// If we are a parent theme, then check for parent definitions

    } else if (!empty($THEME->parent) && $themename == $THEME->parent) {
        if (!isset($THEME->parentsheets) or $THEME->parentsheets === true) {     // Use all the sheets we have
            $THEME->sheets = $DEFAULT_SHEET_LIST;
        } else if (empty($THEME->parentsheets)) {                                // We can stop right now!
            echo "/***** Nothing required from this stylesheet by main theme *****/\n\n";
            exit;
        } else {                                                                 // Use the provided subset only
            $THEME->sheets = $THEME->parentsheets;
        }
    }

/// Work out the last modified date for this theme

    foreach ($THEME->sheets as $sheet) {
        if (file_exists($CFG->themedir.$themename.'/'.$sheet.'.css')) {
            $sheetmodified = filemtime($CFG->themedir.$themename.'/'.$sheet.'.css');
            if ($sheetmodified > $lastmodified) {
                $lastmodified = $sheetmodified;
            }
        }
    }


/// Get a list of all the files we want to include
    $files = array();

    foreach ($THEME->sheets as $sheet) {
        $files[] = array($CFG->themedir, $themename.'/'.$sheet.'.css');
    }

    if ($themename == 'standard') {          // Add any standard styles included in any modules
        if (!empty($THEME->modsheets)) {     // Search for styles.php within activity modules
            if ($mods = get_list_of_plugins('mod')) {
                foreach ($mods as $mod) {
                    if (file_exists($CFG->dirroot.'/mod/'.$mod.'/styles.php')) {
                        $files[] = array($CFG->dirroot, '/mod/'.$mod.'/styles.php');
                    }
                }
            }
        }

        if (!empty($THEME->blocksheets)) {     // Search for styles.php within block modules
            if ($mods = get_list_of_plugins('blocks')) {
                foreach ($mods as $mod) {
                    if (file_exists($CFG->dirroot.'/blocks/'.$mod.'/styles.php')) {
                        $files[] = array($CFG->dirroot, '/blocks/'.$mod.'/styles.php');
                    }
                }
            }
        }

        if (!empty($THEME->langsheets)) {     // Search for styles.php within the current language
            if (file_exists($CFG->dirroot.'/lang/'.$lang.'/styles.php')) {
                $files[] = array($CFG->dirroot, '/lang/'.$lang.'/styles.php');
            }
        }
    }


    if ($files) {
    /// Produce a list of all the files first
        echo '/**************************************'."\n";
        echo ' * THEME NAME: '.$themename."\n *\n";
        echo ' * Files included in this sheet:'."\n *\n";
        foreach ($files as $file) {
            echo ' *   '.$file[1]."\n";
        }
        echo ' **************************************/'."\n\n";


    /// Actually output all the files in order.
        foreach ($files as $file) {
            echo '/***** '.$file[1].' start *****/'."\n\n";
            @include_once($file[0].$file[1]);
            echo '/***** '.$file[1].' end *****/'."\n\n";
        }
    }

    return $CFG->themewww.$themename;   // Only to help old themes (1.4 and earlier)
}


function theme_setup($theme = '', $params=NULL) {
/// Sets up global variables related to themes

    global $CFG, $THEME, $SESSION, $USER;

    if (empty($theme)) {
        $theme = current_theme();
    }

/// If the theme doesn't exist for some reason then revert to standardwhite
    if (!file_exists($CFG->themedir. $theme .'/config.php')) {
        $CFG->theme = $theme = 'standardwhite';
    }

/// Load up the theme config
    $THEME = NULL;   // Just to be sure
    include($CFG->themedir. $theme .'/config.php');  // Main config for current theme

/// Put together the parameters
    if (!$params) {
        $params = array();
    }
    if ($theme != $CFG->theme) {
        $params[] = 'forceconfig='.$theme;
    }

/// Force language too if required
    if (!empty($THEME->langsheets)) {
        $params[] = 'lang='.current_language();
    }

/// Convert params to string
    if ($params) {
        $paramstring = '?'.implode('&', $params);
    } else {
        $paramstring = '';
    }

/// Set up image paths
    if (empty($THEME->custompix)) {    // Could be set in the above file
        $CFG->pixpath = $CFG->wwwroot .'/pix';
        $CFG->modpixpath = $CFG->wwwroot .'/mod';
    } else {
        $CFG->pixpath = $CFG->themewww . $theme .'/pix';
        $CFG->modpixpath = $CFG->themewww . $theme .'/pix/mod';
    }

/// Header and footer paths
    $CFG->header = $CFG->themedir . $theme .'/header.html';
    $CFG->footer = $CFG->themedir . $theme .'/footer.html';

/// Define stylesheet loading order
    $CFG->stylesheets = array();
    if ($theme != 'standard') {    /// The standard sheet is always loaded first
        $CFG->stylesheets[] = $CFG->themewww.'standard/styles.php'.$paramstring;
    }
    if (!empty($THEME->parent)) {  /// Parent stylesheets are loaded next
        $CFG->stylesheets[] = $CFG->themewww.$THEME->parent.'/styles.php'.$paramstring;
    }
    $CFG->stylesheets[] = $CFG->themewww.$theme.'/styles.php'.$paramstring;

}


/**
 * Returns text to be displayed to the user which reflects their login status
 *
 * @uses $CFG
 * @uses $USER
 * @param course $course {@link $COURSE} object containing course information
 * @param user $user {@link $USER} object containing user information
 * @return string
 */
function user_login_string($course=NULL, $user=NULL) {
    global $USER, $CFG, $SITE;

    if (empty($user) and isset($USER->id)) {
        $user = $USER;
    }

    if (empty($course)) {
        $course = $SITE;
    }

    if (isset($user->realuser)) {
        if ($realuser = get_record('user', 'id', $user->realuser)) {
            $fullname = fullname($realuser, true);
            $realuserinfo = " [<a target=\"{$CFG->framename}\"
            href=\"$CFG->wwwroot/course/loginas.php?id=$course->id&amp;return=1\">$fullname</a>] ";
        }
    } else {
        $realuserinfo = '';
    }

    if (empty($CFG->loginhttps)) {
        $wwwroot = $CFG->wwwroot;
    } else {
        $wwwroot = str_replace('http:','https:',$CFG->wwwroot);
    }

    if (isset($user->id) and $user->id) {
        $fullname = fullname($user, true);
        $username = "<a target=\"{$CFG->framename}\" href=\"$CFG->wwwroot/user/view.php?id=$user->id&amp;course=$course->id\">$fullname</a>";
        $instudentview = (!empty($USER->studentview)) ? get_string('instudentview') : '';
        if (isguest($user->id)) {
            $loggedinas = $realuserinfo.get_string('loggedinasguest').
                      " (<a target=\"{$CFG->framename}\" href=\"$wwwroot/login/index.php\">".get_string('login').'</a>)';
        } else {
            $loggedinas = $realuserinfo.get_string('loggedinas', 'moodle', $username).' '.$instudentview.
                      " (<a target=\"{$CFG->framename}\" href=\"$CFG->wwwroot/login/logout.php?sesskey=".sesskey()."\">".get_string('logout').'</a>)';
        }
    } else {
        $loggedinas = get_string('loggedinnot', 'moodle').
                      " (<a target=\"{$CFG->framename}\" href=\"$wwwroot/login/index.php\">".get_string('login').'</a>)';
    }
    return '<div class="logininfo">'.$loggedinas.'</div>';
}

/**
 * Prints breadcrumbs links
 *
 * @uses $CFG
 * @param string $navigation The breadcrumbs string to be printed
 */
function print_navigation ($navigation) {
   global $CFG, $USER;

   if ($navigation) {
       //Accessibility: breadcrumb links now in a list, &raquo; replaced with image.
       $nav_text = get_string('youarehere','access');
       echo '<h2 class="accesshide">'.$nav_text.",</h2><ul>\n";
       if (! $site = get_site()) {
           $site->shortname = get_string('home');
       }
       $navigation = '<li title="'.$nav_text.'"><img src="'.$CFG->pixpath.'/a/r_breadcrumb.gif" class="resize" alt="" /> '
           .str_replace('->', '</li><li title="'.$nav_text.'"><img src="'.$CFG->pixpath.'/a/r_breadcrumb.gif" class="resize" alt="" /> ', $navigation)."</li>\n";
       echo '<li class="first"><a target="'. $CFG->framename .'" href="'. $CFG->wwwroot.((!isadmin() && !empty($USER->id) && !empty($CFG->mymoodleredirect) && !isguest())
                                                                       ? '/my' : '') .'/">'. $site->shortname ."</a></li>\n". $navigation;
       echo "</ul>\n";
   }
}

/**
 * Prints a string in a specified size  (retained for backward compatibility)
 *
 * @param string $text The text to be displayed
 * @param int $size The size to set the font for text display.
 */
function print_headline($text, $size=2) {
    print_heading($text, 'left', $size);
}

/**
 * Prints text in a format for use in headings.
 *
 * @param string $text The text to be displayed
 * @param string $align The alignment of the printed paragraph of text
 * @param int $size The size to set the font for text display.
 */
function print_heading($text, $align='', $size=2, $class='main') {
    if ($align) {
        $align = ' align="'.$align.'"';
    }
    if ($class) {
        $class = ' class="'.$class.'"';
    }
    echo "<h$size $align $class>".stripslashes_safe($text)."</h$size>";
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
    echo '<h2 class="main help">'.$icon.stripslashes_safe($text);
    helpbutton($helppage, $text, $module);
    echo '</h2>';
}


function print_heading_block($heading, $class='') {
    //Accessibility: 'headingblock' is now H1, see theme/standard/styles_*.css: ??
    echo '<h2 class="headingblock header '.$class.'">'.stripslashes($heading).'</h2>';
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

    echo '<div class="continuebutton">';
    print_single_button($link, NULL, get_string('continue'), 'post', $CFG->framename);
    echo '</div>'."\n";
}

/**
 * Print a message in a standard themed box.
 * See, {@link print_simple_box_start}.
 *
 * @param string $align string, alignment of the box, not the text (default center, left, right).
 * @param string $width string, width of the box, including units %, for example '100%'.
 * @param string $color string, background colour of the box, for example '#eee'.
 * @param int $padding integer, padding in pixels, specified without units.
 * @param string $class string, space-separated class names.
 * @todo Finish documenting this function
 */
function print_simple_box($message, $align='', $width='', $color='', $padding=5, $class='generalbox', $id='') {
    print_simple_box_start($align, $width, $color, $padding, $class, $id);
    echo stripslashes_safe($message);
    print_simple_box_end();
}

/**
 * Print the top portion of a standard themed box using a TABLE.  Yes, we know.
 * See bug 4943 for details on some accessibility work regarding this that didn't make it into 1.6.
 *
 * @param string $align string, alignment of the box, not the text (default center, left, right).
 * @param string $width string, width of the box, including % units, for example '100%'.
 * @param string $color string, background colour of the box, for example '#eee'.
 * @param int $padding integer, padding in pixels, specified without units.
 * @param string $class string, space-separated class names.
 */
function print_simple_box_start($align='', $width='', $color='', $padding=5, $class='generalbox', $id='') {

    if ($color) {
        $color = 'bgcolor="'. $color .'"';
    }
    if ($align) {
        $align = 'align="'. $align .'"';
    }
    if ($width) {
        $width = 'width="'. $width .'"';
    }
    if ($id) {
        $id = 'id="'. $id .'"';
    }
    echo "<table $align $width $id class=\"$class\" border=\"0\" cellpadding=\"$padding\" cellspacing=\"0\">".
         "<tr><td $color class=\"$class"."content\">";
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
function print_single_button($link, $options, $label='OK', $method='get', $target='_self') {
    echo '<div class="singlebutton">';
    echo '<form action="'. $link .'" method="'. $method .'" target="'.$target.'">';
    if ($options) {
        foreach ($options as $name => $value) {
            echo '<input type="hidden" name="'. $name .'" value="'. $value .'" />';
        }
    }
    echo '<input type="submit" value="'. $label .'" /></form></div>';
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
    echo '<img class="spacer" height="'. $height .'" width="'. $width .'" src="'. $CFG->wwwroot .'/pix/spacer.gif" alt="" />';
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
 * @param int $size Size in pixels.  Special values are (true/1 = 100px) and (false/0 = 35px) for backward compatability
 * @param boolean $returnstring If false print picture to current page, otherwise return the output as string
 * @param boolean $link Enclose printed image in a link to view specified course?
 * return string
 * @todo Finish documenting this function
 */
function print_user_picture($userid, $courseid, $picture, $size=0, $returnstring=false, $link=true, $target='') {
    global $CFG;

    if ($link) {
        if ($target) {
            $target=' target="_blank"';
        }
        $output = '<a '.$target.' href="'. $CFG->wwwroot .'/user/view.php?id='. $userid .'&amp;course='. $courseid .'">';
    } else {
        $output = '';
    }
    if (empty($size)) {
        $file = 'f2';
        $size = 35;
    } else if ($size === true or $size == 1) {
        $file = 'f1';
        $size = 100;
    } else if ($size >= 50) {
        $file = 'f1';
    } else {
        $file = 'f2';
    }
    $class = "userpicture";
    if ($picture) {  // Print custom user picture
        if ($CFG->slasharguments) {        // Use this method if possible for better caching
            $src =  $CFG->wwwroot .'/user/pix.php/'. $userid .'/'. $file .'.jpg"';
        } else {
            $src =  $CFG->wwwroot .'/user/pix.php?file=/'. $userid .'/'. $file .'.jpg"';
        }
    } else {         // Print default user pictures (use theme version if available)
        $class .= " defaultuserpic";
        $src =  "$CFG->pixpath/u/$file.png\"";
    }
    $output .= "<img class=\"$class\" align=\"middle\" src=\"$src".
                   " border=\"0\" width=\"$size\" height=\"$size\" alt=\"\" />";
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
function print_user($user, $course, $messageselect=false) {

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

/// Get the hidden field list
    if ($isteacher || $isadmin) {
        $hiddenfields = array();
    } else {
        $hiddenfields = array_flip(explode(',', $CFG->hiddenuserfields));
    }

    echo '<table class="userinfobox">';
    echo '<tr>';
    echo '<td class="left side">';
    print_user_picture($user->id, $course->id, $user->picture, true);
    echo '</td>';
    echo '<td class="content">';
    echo '<div class="username">'.fullname($user, $isteacher).'</div>';
    echo '<div class="info">';
    if (!empty($user->role) and ($user->role <> $course->teacher)) {
        echo $string->role .': '. $user->role .'<br />';
    }
    if ($user->maildisplay == 1 or ($user->maildisplay == 2 and $course->category and !isguest()) or $isteacher) {
        echo $string->email .': <a href="mailto:'. $user->email .'">'. $user->email .'</a><br />';
    }
    if (($user->city or $user->country) and (!isset($hiddenfields['city']) or !isset($hiddenfields['country']))) {
        echo $string->location .': ';
        if ($user->city && !isset($hiddenfields['city'])) {
            echo $user->city;
        }
        if (!empty($countries[$user->country]) && !isset($hiddenfields['country'])) {
            if ($user->city && !isset($hiddenfields['city'])) {
                echo ', ';
            }
            echo $countries[$user->country];
        }
        echo '<br />';
    }

    if (!isset($hiddenfields['lastaccess'])) {
        if ($user->lastaccess) {
            echo $string->lastaccess .': '. userdate($user->lastaccess);
            echo '&nbsp ('. format_time(time() - $user->lastaccess, $datestring) .')';
        } else {
            echo $string->lastaccess .': '. $string->never;
        }
    }
    echo '</div></td><td class="links">';
    //link to blogs
    if ($CFG->bloglevel > 0) {
        echo '<a href="'.$CFG->wwwroot.'/blog/index.php?userid='.$user->id.'">'.get_string('blogs','blog').'</a><br />';
    }

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

    if (!empty($messageselect) && $isteacher) {
        echo '<br /><input type="checkbox" name="';
        if (isteacher($course->id, $user->id)) {
            echo 'teacher';
        } else {
            echo 'user';
        }
        echo $user->id.'" /> ';
    }

    echo '</td></tr></table>';
}

/**
 * Print a specified group's avatar.
 *
 * @param group $group A {@link group} object representing a group or array of groups
 * @param int $courseid ?
 * @param boolean $large ?
 * @param boolean $returnstring ?
 * @param boolean $link ?
 * @return string
 * @todo Finish documenting this function
 */
function print_group_picture($group, $courseid, $large=false, $returnstring=false, $link=true) {
    global $CFG;

    if (is_array($group)) {
        $output = '';
        foreach($group as $g) {
            $output .= print_group_picture($g, $courseid, $large, true, $link);
        }
        if ($returnstring) {
            return $output;
        } else {
            echo $output;
            return;
        }
    }

    static $isteacheredit;

    if (!isset($isteacheredit)) {
        $isteacheredit = isteacheredit($courseid);
    }

    if ($group->hidepicture and !$isteacheredit) {
        return '';
    }

    if ($link or $isteacheredit) {
        $output = '<a href="'. $CFG->wwwroot .'/user/index.php?id='. $courseid .'&amp;group='. $group->id .'">';
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
            $output .= '<img class="grouppicture" align="middle" src="'.$CFG->wwwroot.'/user/pixgroup.php/'.$group->id.'/'.$file.'.jpg"'.
                       ' border="0" width="'.$size.'" height="'.$size.'" alt="" title="'.s($group->name).'"/>';
        } else {
            $output .= '<img class="grouppicture" align="middle" src="'.$CFG->wwwroot.'/user/pixgroup.php?file=/'.$group->id.'/'.$file.'.jpg"'.
                       ' border="0" width="'.$size.'" height="'.$size.'" alt="" title="'.s($group->name).'"/>';
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
                   ' border="0" class="png" style="width: '. $sizex .'px; height: '. $sizey .'px; '.
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
 * @param array $table is an object with several properties.
 *     <ul<li>$table->head - An array of heading names.
 *     <li>$table->align - An array of column alignments
 *     <li>$table->size  - An array of column sizes
 *     <li>$table->wrap - An array of "nowrap"s or nothing
 *     <li>$table->data[] - An array of arrays containing the data.
 *     <li>$table->width  - A percentage of the page
 *     <li>$table->tablealign  - Align the whole table
 *     <li>$table->cellpadding  - Padding on each cell
 *     <li>$table->cellspacing  - Spacing between cells
 * </ul>
 * @return boolean
 * @todo Finish documenting this function
 */
function print_table($table) {

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

    $tableid = empty($table->id) ? '' : 'id="'.$table->id.'"';

    echo '<table width="'.$table->width.'" border="0" align="'.$table->tablealign.'" ';
    echo " cellpadding=\"$table->cellpadding\" cellspacing=\"$table->cellspacing\" class=\"$table->class\" $tableid>\n";

    $countcols = 0;

    if (!empty($table->head)) {
        $countcols = count($table->head);
        echo '<tr>';
        foreach ($table->head as $key => $heading) {

            if (!isset($size[$key])) {
                $size[$key] = '';
            }
            if (!isset($align[$key])) {
                $align[$key] = '';
            }
            echo '<th valign="top" '. $align[$key].$size[$key] .' nowrap="nowrap" class="header c'.$key.'">'. $heading .'</th>';
        }
        echo '</tr>'."\n";
    }

    if (!empty($table->data)) {
        $oddeven = 1;
        foreach ($table->data as $key => $row) {
            $oddeven = $oddeven ? 0 : 1;
            echo '<tr class="r'.$oddeven.'">'."\n";
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
                    echo '<td '. $align[$key].$size[$key].$wrap[$key] .' class="cell c'.$key.'">'. $item .'</td>';
                }
            }
            echo '</tr>'."\n";
        }
    }
    echo '</table>'."\n";

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

    $output =  '<table width="'. $table->width .'" align="'. $table->tablealign .'" ';
    $output .= ' cellpadding="'. $table->cellpadding .'" cellspacing="'. $table->cellspacing .'" class="'. $table->class .'">'."\n";

    if (!empty($table->head)) {
        $output .= '<tr valign="top">';
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

function print_recent_activity_note($time, $user, $isteacher, $text, $link) {
    static $strftimerecent;

    if (empty($strftimerecent)) {
        $strftimerecent = get_string('strftimerecent');
    }

    $date = userdate($time, $strftimerecent);
    $name = fullname($user, $isteacher);

    echo '<div class="head">';
    echo '<div class="date">'.$date.'</div> '.
         '<div class="name">'.fullname($user, $isteacher).'</div>';
    echo '</div>';
    echo '<div class="info"><a href="'.$link.'">'.format_string($text,true).'</a></div>';
}


/**
 * Prints a basic textarea field.
 *
 * @uses $CFG
 * @param boolean $usehtmleditor ?
 * @param int $rows ?
 * @param int $cols ?
 * @param null $width <b>Legacy field no longer used!</b>  Set to zero to get control over mincols
 * @param null $height <b>Legacy field no longer used!</b>  Set to zero to get control over minrows
 * @param string $name ?
 * @param string $value ?
 * @param int $courseid ?
 * @todo Finish documenting this function
 */
function print_textarea($usehtmleditor, $rows, $cols, $width, $height, $name, $value='', $courseid=0, $return=false) {
/// $width and height are legacy fields and no longer used as pixels like they used to be.
/// However, you can set them to zero to override the mincols and minrows values below.

    global $CFG, $course;
    static $scriptcount; // For loading the htmlarea script only once.

    $mincols = 65;
    $minrows = 10;
    $str = '';

    if ( empty($CFG->editorsrc) ) { // for backward compatibility.
        if (empty($courseid)) {
            if (!empty($course->id)) {  // search for it in global context
                $courseid = $course->id;
            }
        }

        if (empty($scriptcount)) {
            $scriptcount = 0;
        }

        if ($usehtmleditor) {

            if (!empty($courseid) and isteacher($courseid)) {
                $str .= ($scriptcount < 1) ? '<script type="text/javascript" src="'.
                $CFG->wwwroot .'/lib/editor/htmlarea/htmlarea.php?id='. $courseid .'"></script>'."\n" : '';
            } else {
                $str .= ($scriptcount < 1) ? '<script type="text/javascript" src="'.
                $CFG->wwwroot .'/lib/editor/htmlarea/htmlarea.php"></script>'."\n" : '';
            }
            $str .= ($scriptcount < 1) ? '<script type="text/javascript" src="'.
            $CFG->wwwroot .'/lib/editor/htmlarea/lang/en.php"></script>'."\n" : '';
            $scriptcount++;

            if ($height) {    // Usually with legacy calls
                if ($rows < $minrows) {
                    $rows = $minrows;
                }
            }
            if ($width) {    // Usually with legacy calls
                if ($cols < $mincols) {
                    $cols = $mincols;
                }
            }
        }
    }
    $str .= '<textarea id="edit-'. $name .'" name="'. $name .'" rows="'. $rows .'" cols="'. $cols .'">';
    if ($usehtmleditor) {
        $str .= htmlspecialchars($value); // needed for editing of cleaned text!
    } else {
        $str .= s($value);
    }
    $str .= '</textarea>'."\n";

    if ($return) {
        return $str;
    }
    echo $str;
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
function use_html_editor($name='', $editorhidebuttons='') {
    $editor = 'editor_'.md5($name); //name might contain illegal characters
    echo '<script language="javascript" type="text/javascript" defer="defer">'."\n";
    echo "$editor = new HTMLArea('edit-$name');\n";
    echo "var config = $editor.config;\n";

    echo print_editor_config($editorhidebuttons);

    if (empty($name)) {
        echo "\nHTMLArea.replaceAll($editor.config);\n";
    } else {
        echo "\n$editor.generate();\n";
    }
    echo '</script>'."\n";
}

function print_editor_config($editorhidebuttons='', $return=false) {
    global $CFG;

    $str = "config.pageStyle = \"body {";

    if (!(empty($CFG->editorbackgroundcolor))) {
        $str .= " background-color: $CFG->editorbackgroundcolor;";
    }

    if (!(empty($CFG->editorfontfamily))) {
        $str .= " font-family: $CFG->editorfontfamily;";
    }

    if (!(empty($CFG->editorfontsize))) {
        $str .= " font-size: $CFG->editorfontsize;";
    }

    $str .= " }\";\n";
    $str .= "config.killWordOnPaste = ";
    $str .= (empty($CFG->editorkillword)) ? "false":"true";
    $str .= ';'."\n";
    $str .= 'config.fontname = {'."\n";

    $fontlist = isset($CFG->editorfontlist) ? explode(';', $CFG->editorfontlist) : array();
    $i = 1;                     // Counter is used to get rid of the last comma.

    foreach ($fontlist as $fontline) {
        if (!empty($fontline)) {
            if ($i > 1) {
                $str .= ','."\n";
            }
            list($fontkey, $fontvalue) = split(':', $fontline);
            $str .= '"'. $fontkey ."\":\t'". $fontvalue ."'";

            $i++;
        }
    }
    $str .= '};';

    if (!empty($editorhidebuttons)) {
        $str .= "\nconfig.hideSomeButtons(\" ". $editorhidebuttons ." \");\n";
    } else if (!empty($CFG->editorhidebuttons)) {
        $str .= "\nconfig.hideSomeButtons(\" ". $CFG->editorhidebuttons ." \");\n";
    }

    if (!empty($CFG->editorspelling) && !empty($CFG->aspellpath)) {
        $str .= print_speller_code($usehtmleditor=true, true);
    }

    if ($return) {
        return $str;
    }
    echo $str;
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
            $edit = '0';
        } else {
            $string = get_string('turneditingon');
            $edit = '1';
        }
        return "<form target=\"$CFG->framename\" method=\"get\" action=\"$CFG->wwwroot/course/view.php\">".
            "<input type=\"hidden\" name=\"id\" value=\"$courseid\" />".
            "<input type=\"hidden\" name=\"edit\" value=\"$edit\" />".
            "<input type=\"hidden\" name=\"sesskey\" value=\"".sesskey()."\" />".
            "<input type=\"submit\" value=\"$string\" /></form>";
    }
}

/**
 * Returns a turn student view on/off button for course in a self contained form.
 *
 * @uses $CFG
 * @uses $USER
 * @param int $courseid The course  to update by id as found in 'course' table
 * @return string
 */
function update_studentview_button($courseid) {

    global $CFG, $USER;

    if (isteacheredit($courseid,0,true)) {
        if (!empty($USER->studentview)) {
            $svstring = get_string('studentviewoff');
            $svedit = 'off';
        } else {
            $svstring = get_string('studentviewon');
            $svedit = 'on';
        }
        $button = "<form target=\"$CFG->framename\" method=\"get\" action=\"$CFG->wwwroot/course/view.php\">".
            "<input type=\"hidden\" name=\"id\" value=\"$courseid\" />".
            "<input type=\"hidden\" name=\"studentview\" value=\"$svedit\" />".
            "<input type=\"hidden\" name=\"sesskey\" value=\"".sesskey()."\" />".
            "<input type=\"submit\" value=\"$svstring\" /></form>";
        return $button;
    }
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
function update_mymoodle_icon() {

    global $CFG, $USER;

    if (!empty($USER->editing)) {
        $string = get_string('updatemymoodleoff');
        $edit = '0';
    } else {
        $string = get_string('updatemymoodleon');
        $edit = '1';
    }
    return "<form target=\"$CFG->framename\" method=\"get\" action=\"$CFG->wwwroot/my/index.php\">".
        "<input type=\"hidden\" name=\"edit\" value=\"$edit\" />".
        "<input type=\"submit\" value=\"$string\" /></form>";
}

/**
 * Prints the editing button on a module "view" page
 *
 * @uses $CFG
 * @param    type description
 * @todo Finish documenting this function
 */
function update_module_button($moduleid, $courseid, $string) {
    global $CFG, $USER;

    // do not display if studentview is on
    if (!empty($USER->studentview)) {
        return '';
    }

    if (isteacheredit($courseid)) {
        $string = get_string('updatethis', '', $string);
        return "<form target=\"$CFG->framename\" method=\"get\" action=\"$CFG->wwwroot/course/mod.php\">".
               "<input type=\"hidden\" name=\"update\" value=\"$moduleid\" />".
               "<input type=\"hidden\" name=\"return\" value=\"true\" />".
               "<input type=\"hidden\" name=\"sesskey\" value=\"".sesskey()."\" />".
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
               "<input type=\"hidden\" name=\"sesskey\" value=\"$USER->sesskey\" />".
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
        if (!empty($USER->categoryediting)) {
            $string = get_string('turneditingoff');
            $edit = 'off';
        } else {
            $string = get_string('turneditingon');
            $edit = 'on';
        }
        return "<form target=\"$CFG->framename\" method=\"get\" action=\"$CFG->wwwroot/course/index.php\">".
               '<input type="hidden" name="edit" value="'. $edit .'" />'.
               '<input type="hidden" name="sesskey" value="'.$USER->sesskey.'" />'.
               '<input type="submit" value="'. $string .'" /></form>';
    }
}

/**
 * Prints the editing button on search results listing
 * For bulk move courses to another category
 */

function update_categories_search_button($search,$page,$perpage) {
    global $CFG, $USER;

    if (isadmin()) {
        if (!empty($USER->categoryediting)) {
            $string = get_string("turneditingoff");
            $edit = "off";
            $perpage = 30;
        } else {
            $string = get_string("turneditingon");
            $edit = "on";
        }
        return "<form target=\"$CFG->framename\" method=\"get\" action=\"$CFG->wwwroot/course/search.php\">".
               "<input type=\"hidden\" name=\"edit\" value=\"$edit\" />".
               "<input type=\"hidden\" name=\"sesskey\" value=\"$USER->sesskey\" />".
               "<input type=\"hidden\" name=\"search\" value=\"".s($search, true)."\" />".
               "<input type=\"hidden\" name=\"page\" value=\"$page\" />".
               "<input type=\"hidden\" name=\"perpage\" value=\"$perpage\" />".
               "<input type=\"submit\" value=\"".s($string)."\" /></form>";
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
 * @param boolean $showall: if set to 0, it is a student in separate groups, do not display all participants
 * @todo Finish documenting this function
 */
function print_group_menu($groups, $groupmode, $currentgroup, $urlroot, $showall=1) {

/// Add an "All groups" to the start of the menu
    if ($showall){
        $groupsmenu[0] = get_string('allparticipants');
    }
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
    echo '</td></tr></table>';

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

    global $CFG, $THEME;

    if (empty($THEME->navmenuwidth)) {
        $width = 50;
    } else {
        $width = $THEME->navmenuwidth;
    }

    if ($cm) {
        $cm = $cm->id;
    }

    if ($course->format == 'weeks') {
        $strsection = get_string('week');
    } else {
        $strsection = get_string('topic');
    }
    $strjumpto = get_string('jumpto');

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

    $sections = get_records('course_sections','course',$course->id,'section','section,visible,summary');

    if (!empty($THEME->makenavmenulist)) {   /// A hack to produce an XHTML navmenu list for use in themes
        $THEME->navmenulist = navmenulist($course, $sections, $modinfo,
                                          $isteacher, $strsection, $strjumpto, $width, $cm);
    }

    foreach ($modinfo as $mod) {
        if ($mod->mod == 'label') {
            continue;
        }

        if ($mod->section > $course->numsections) {   /// Don't show excess hidden sections
            break;
        }

        if ($mod->section > 0 and $section <> $mod->section) {
            $thissection = $sections[$mod->section];

            if ($thissection->visible or !$course->hiddensections or $isteacher) {
                $thissection->summary = strip_tags(format_string($thissection->summary,true));
                if ($course->format == 'weeks' or empty($thissection->summary)) {
                    $menu[] = '-------------- '. $strsection ." ". $mod->section .' --------------';
                } else {
                    if (strlen($thissection->summary) < ($width-3)) {
                        $menu[] = '-- '.$thissection->summary;
                    } else {
                        $menu[] = '-- '.substr($thissection->summary, 0, $width).'...';
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
                $mod->name = strip_tags(format_string(urldecode($mod->name),true));
                if (strlen($mod->name) > ($width+5)) {
                    $mod->name = substr($mod->name, 0, $width).'...';
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
                    "\"$CFG->wwwroot/course/report/log/index.php?chooselog=1&amp;user=0&amp;date=0&amp;id=$course->id&amp;modid=$selectmod->cm\">".
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
 * Given a course
 * This function returns a small popup menu with all the
 * course activity modules in it, as a navigation menu
 * outputs a simple list structure in XHTML
 * The data is taken from the serialised array stored in
 * the course record
 *
 * @param course $course A {@link $COURSE} object.
 * @return string
 * @todo Finish documenting this function
 */
function navmenulist($course, $sections, $modinfo, $isteacher, $strsection, $strjumpto, $width=50, $cmid=0) {

    global $CFG;

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

    $menu[] = '<ul class="navmenulist"><li class="jumpto section"><span>'.$strjumpto.'</span><ul>';
    foreach ($modinfo as $mod) {
        if ($mod->mod == 'label') {
            continue;
        }

        if ($mod->section > $course->numsections) {   /// Don't show excess hidden sections
            break;
        }

        if ($mod->section >= 0 and $section <> $mod->section) {
            $thissection = $sections[$mod->section];

            if ($thissection->visible or !$course->hiddensections or $isteacher) {
                $thissection->summary = strip_tags(format_string($thissection->summary,true));
                if (!empty($doneheading)) {
                    $menu[] = '</ul></li>';
                }
                if ($course->format == 'weeks' or empty($thissection->summary)) {
                    $item = $strsection ." ". $mod->section;
                } else {
                    if (strlen($thissection->summary) < ($width-3)) {
                        $item = $thissection->summary;
                    } else {
                        $item = substr($thissection->summary, 0, $width).'...';
                    }
                }
                $menu[] = '<li class="section"><span>'.$item.'</span>';
                $menu[] = '<ul>';
                $doneheading = true;
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
            $mod->name = strip_tags(format_string(urldecode($mod->name),true));
            if (strlen($mod->name) > ($width+5)) {
                $mod->name = substr($mod->name, 0, $width).'...';
            }
            if (!$mod->visible) {
                $mod->name = '('.$mod->name.')';
            }
            $class = 'activity '.$mod->mod;
            $class .= ($cmid == $mod->cm) ? ' selected' : '';
            $menu[] = '<li class="'.$class.'">'.
                      '<img src="'.$CFG->modpixpath.'/'.$mod->mod.'/icon.gif" border="0" />'.
                      '<a href="'.$CFG->wwwroot.'/mod/'.$url.'">'.$mod->name.'</a></li>';
            $previousmod = $mod;
        }
    }
    if ($doneheading) {
        $menu[] = '</ul></li>';
    }
    $menu[] = '</ul></li></ul>';

    return implode("\n", $menu);
}

/**
 * Prints form items with the names $day, $month and $year
 *
 * @param string $day   fieldname
 * @param string $month  fieldname
 * @param string $year  fieldname
 * @param int $currenttime A default timestamp in GMT
 * @param boolean $return
 */
function print_date_selector($day, $month, $year, $currenttime=0, $return=false) {

    if (!$currenttime) {
        $currenttime = time();
    }
    $currentdate = usergetdate($currenttime);

    for ($i=1; $i<=31; $i++) {
        $days[$i] = $i;
    }
    for ($i=1; $i<=12; $i++) {
        $months[$i] = userdate(gmmktime(12,0,0,$i,15,2000), "%B");
    }
    for ($i=1970; $i<=2020; $i++) {
        $years[$i] = $i;
    }
    return choose_from_menu($days,   $day,   $currentdate['mday'], '', '', '0', $return)
          .choose_from_menu($months, $month, $currentdate['mon'],  '', '', '0', $return)
          .choose_from_menu($years,  $year,  $currentdate['year'], '', '', '0', $return);

}

/**
 *Prints form items with the names $hour and $minute
 *
 * @param string $hour  fieldname
 * @param string ? $minute  fieldname
 * @param $currenttime A default timestamp in GMT
 * @param int $step minute spacing
 * @param boolean $return
 */
function print_time_selector($hour, $minute, $currenttime=0, $step=5, $return=false) {

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

    return choose_from_menu($hours,   $hour,   $currentdate['hours'],   '','','0',$return)
          .choose_from_menu($minutes, $minute, $currentdate['minutes'], '','','0',$return);
}

/**
 * Prints time limit value selector
 *
 * @uses $CFG
 * @param int $timelimit default
 * @param string $unit
 * @param string $name
 * @param boolean $return
 */
function print_timer_selector($timelimit = 0, $unit = '', $name = 'timelimit', $return=false) {

    global $CFG;

    if ($unit) {
        $unit = ' '.$unit;
    }

    // Max timelimit is sessiontimeout - 10 minutes.
    $maxvalue = ($CFG->sessiontimeout / 60) - 10;

    for ($i=1; $i<=$maxvalue; $i++) {
        $minutes[$i] = $i.$unit;
    }
    return choose_from_menu($minutes, $name, $timelimit, get_string('none'), '','','0',$return);
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

    $linkobject = '<span class="helplink"><img height="17" width="17" alt="'.$strscales.'" src="'.$CFG->pixpath .'/help.gif" /></span>';
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

    $linkobject = '<span class="helplink"><img height="17" width="17" alt="'.$strscales.'" src="'.$CFG->pixpath .'/help.gif" /></span>';
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

    $linkobject = '<span class="helplink"><img height="17" width="17" alt="'.$scale->name.'" src="'.$CFG->pixpath .'/help.gif" /></span>';
    link_to_popup_window ('/course/scales.php?id='. $courseid .'&amp;list=true&amp;scaleid='. $scale->id, 'ratingscale',
                          $linkobject, 400, 500, $scale->name);
}

/**
 * Print an error page displaying an error message.
 * Old method, don't call directly in new code - use print_error instead.
 *
 *
 * @uses $SESSION
 * @uses $CFG
 * @param string $message The message to display to the user about the error.
 * @param string $link The url where the user will be prompted to continue. If no url is provided the user will be directed to the site index page.
 */
function error ($message, $link='') {
    global $CFG, $SESSION;

    if (! defined('HEADER_PRINTED')) {
        //header not yet printed
        @header('HTTP/1.0 404 Not Found');
        print_header(get_string('error'));
    }

    echo '<br />';

    $message = clean_text($message);   // In case nasties are in here

    print_simple_box($message, '', '', '', '', 'errorbox');

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
    for ($i=0;$i<512;$i++) {  // Padding to help IE work with 404
        echo ' ';
    }
    die;
}

/**
 * Print an error page displaying an error message.  New method - use this for new code.
 *
 * @uses $SESSION
 * @uses $CFG
 * @param string $errorcode The name of the string from error.php to print
 * @param string $link The url where the user will be prompted to continue. If no url is provided the user will be directed to the site index page.
 * @param object $a Extra words and phrases that might be required in the error string
 */
function print_error ($errorcode, $module='', $link='', $a=NULL) {

    global $CFG;

    if (empty($module) || $module == 'moodle' || $module == 'core') {
        $module = 'error';
        $modulelink = 'moodle';
    } else {
        $modulelink = $module;
    }

    if (!empty($CFG->errordocroot)) {
        $errordocroot = $CFG->errordocroot;
    } else if (!empty($CFG->docroot)) {
        $errordocroot = $CFG->docroot;
    } else {
        $errordocroot = 'http://docs.moodle.org';
    }

    $message = '<p class="errormessage">'.get_string($errorcode, $module, $a).'</p>'.
               '<p class="errorcode">'.
               '<a href="'.$errordocroot.'/en/error/'.$modulelink.'/'.$errorcode.'">'.
                 get_string('moreinformation').'</a></p>';
    error($message, $link);
}


/**
 * Print a help button.
 *
 * @uses $CFG
 * @param string $page  The keyword that defines a help page
 * @param string $title The title of links, rollover tips, alt tags etc
 *           'Help with' (or the language equivalent) will be prefixed and '...' will be stripped.
 * @param string $module Which module is the page defined in
 * @param mixed $image Use a help image for the link?  (true/false/"both")
 * @param string $text If defined then this text is used in the page, and
 *           the $page variable is ignored.
 * @param boolean $return If true then the output is returned as a string, if false it is printed to the current page.
 * @param string $imagetext The full text for the helpbutton icon. If empty use default help.gif
 * @return string
 * @todo Finish documenting this function
 */
function helpbutton ($page, $title='', $module='moodle', $image=true, $linktext=false, $text='', $return=false,
                     $imagetext='') {
    global $CFG;

    if ($module == '') {
        $module = 'moodle';
    }


    //Accessibility: prefix the alt text/title with 'Help with', strip distracting dots '...'
    // PLEASE DO NOT CHANGE. ('...' is VERY distracting for non-visual users)
    $tooltip = get_string('helpprefix', '', trim($title, ". \t"));

    $linkobject = '';

    if ($image) {
        if ($imagetext == '') {
            $imagetext = '<img alt="'.$tooltip.'" src="'.
                          $CFG->pixpath .'/help.gif" />';
        }
        if ($linktext) {
            $linkobject .= $title.'&nbsp;';
        }

        $linkobject .= $imagetext;

    } else {
        $linkobject .= $tooltip;
    }

    if ($text) {
        $url = '/help.php?module='. $module .'&amp;text='. s(urlencode($text));
    } else {
        $url = '/help.php?module='. $module .'&amp;file='. $page .'.html';
    }

    $link = '<span class="helplink">'.
            link_to_popup_window ($url, 'popup', $linkobject, 400, 500, $tooltip, 'none', true).
            '</span>';

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
    $imagetext = '<img src="' . $CFG->pixpath . '/s/smiley.gif" border="0" align="middle" width="15" height="15" alt=""
    class="emoticon" style="margin-left: 7px" />';

    helpbutton('emoticons', get_string('helpemoticons'), 'moodle', true, true, '', false, $imagetext);
}

/**
 * Print a message and exit.
 *
 * @uses $CFG
 * @param string $message ?
 * @param string $link ?
 * @todo Finish documenting this function
 */
function notice ($message, $link='') {
    global $CFG;

    $message = clean_text($message);
    $link    = clean_text($link);

    if (!$link) {
        if (!empty($_SERVER['HTTP_REFERER'])) {
            $link = $_SERVER['HTTP_REFERER'];
        } else {
            $link = $CFG->wwwroot .'/';
        }
    }

    echo '<br />';
    print_simple_box($message, 'center', '50%', '', '20', 'generalbox', 'notice');
    print_continue($link);
    print_footer(get_site());
    die;
}

/**
 * Print a message along with "Yes" and "No" links for the user to continue.
 *
 * @param string $message The text to display
 * @param string $linkyes The link to take the user to if they choose "Yes"
 * @param string $linkno The link to take the user to if they choose "No"
 */
function notice_yesno ($message, $linkyes, $linkno) {

    global $CFG;

    $message = clean_text($message);
    $linkyes = clean_text($linkyes);
    $linkno = clean_text($linkno);

    print_simple_box_start('center', '60%', '', 5, 'generalbox', 'notice');
    echo '<p align="center">'. $message .'</p>';
    echo '<table align="center" cellpadding="20"><tr><td>';
    print_single_button($linkyes, NULL, get_string('yes'), 'post', $CFG->framename);
    echo '</td><td>';
    print_single_button($linkno, NULL, get_string('no'), 'post', $CFG->framename);
    echo '</td></tr></table>';
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
function redirect($url, $message='', $delay=-1) {

    global $CFG;

    //$url     = clean_text($url);
    if (!empty($CFG->usesid) && !isset($_COOKIE[session_name()])) {
       $url = sid_process_url($url);
    }

    $message = clean_text($message);

    $url = html_entity_decode($url); // for php < 4.3.0 this is defined in moodlelib.php
    $url = str_replace(array("\n", "\r"), '', $url); // some more cleaning
    $encodedurl = htmlentities($url);
    $tmpstr = clean_text('<a href="'.$encodedurl.'" />'); //clean encoded URL
    $encodedurl = substr($tmpstr, 9, strlen($tmpstr)-13);
    $url = addslashes(html_entity_decode($encodedurl));

/// when no message and header printed yet, try to redirect
    if (empty($message) and !defined('HEADER_PRINTED')) {
        if ($delay == -1) {
            $delay = 0;
        }
        echo '<meta http-equiv="refresh" content="'. $delay .'; url='. $encodedurl .'" />';
        echo '<script type="text/javascript">'. "\n" .'<!--'. "\n". "location.replace('$url');". "\n". '//-->'. "\n". '</script>';   // To cope with Mozilla bug
        die;
    }

    if ($delay == -1) {
        $delay = 3;  // if no delay specified wait 3 seconds
    }
    if (! defined('HEADER_PRINTED')) {
        print_header('', '', '', '', '<meta http-equiv="refresh" content="'. $delay .'; url='. $encodedurl .'" />');
    }
    echo '<center>';
    echo '<p>'. $message .'</p>';
    echo '<p>( <a href="'. $encodedurl .'">'. get_string('continue') .'</a> )</p>';
    echo '</center>';

?>
<script type="text/javascript">
<!--

  function redirect() {
      document.location.replace('<?php echo $url ?>');
  }
  setTimeout("redirect()", <?php echo ($delay * 1000) ?>);
-->
</script>
<?php
    die;
}

/**
 * Print a bold message in an optional color.
 *
 * @param string $message The message to print out
 * @param string $style Optional style to display message text in
 * @param string $align Alignment option
 * @param bool $return whether to return an output string or echo now
 */
function notify($message, $style='notifyproblem', $align='center', $return=false) {
    if ($style == 'green') {
        $style = 'notifysuccess';  // backward compatible with old color system
    }

    $message = clean_text($message);

    $output = '<div class="'.$style.'" align="'. $align .'">'. $message .'</div>'."<br />\n";

    if ($return) {
        return $output;
    }
    echo $output;
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
 * @param int $totalcount Thetotal number of entries available to be paged through
 * @param int $page The page you are currently viewing
 * @param int $perpage The number of entries that should be shown per page
 * @param string $baseurl The url which will be used to create page numbered links. Each page will consist of the base url appended by the page
var an equal sign, then the page number.
 * @param string $pagevar This is the variable name that you use for the page number in your code (ie. 'tablepage', 'blogpage', etc)
 */
function print_paging_bar($totalcount, $page, $perpage, $baseurl, $pagevar='page',$nocurr=false) {

    $maxdisplay = 18;

    if ($totalcount > $perpage) {
        echo '<div class="paging">';
        echo get_string('page') .':';
        if ($page > 0) {
            $pagenum = $page - 1;
            echo '&nbsp;(<a  href="'. $baseurl . $pagevar .'='. $pagenum .'">'. get_string('previous') .'</a>)&nbsp;';
        }
        $lastpage = ceil($totalcount / $perpage);
        if ($page > 15) {
            $startpage = $page - 10;
            echo '&nbsp;<a href="'. $baseurl . $pagevar .'=0">1</a>&nbsp;...';
        } else {
            $startpage = 0;
        }
        $currpage = $startpage;
        $displaycount = 0;
        while ($displaycount < $maxdisplay and $currpage < $lastpage) {
            $displaypage = $currpage+1;
            if ($page == $currpage && empty($nocurr)) {
                echo '&nbsp;&nbsp;'. $displaypage;
            } else {
                echo '&nbsp;&nbsp;<a href="'. $baseurl . $pagevar .'='. $currpage .'">'. $displaypage .'</a>';
            }
            $displaycount++;
            $currpage++;
        }
        if ($currpage < $lastpage) {
            $lastpageactual = $lastpage - 1;
            echo '&nbsp;...<a href="'. $baseurl . $pagevar .'='. $lastpageactual .'">'. $lastpage .'</a>&nbsp;';
        }
        $pagenum = $page + 1;
        if ($pagenum != $displaypage) {
            echo '&nbsp;&nbsp;(<a href="'. $baseurl . $pagevar .'='. $pagenum .'">'. get_string('next') .'</a>)';
        }
        echo '</div>';
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

/**
 * Prints a nice side block with an optional header.  The content can either
 * be a block of HTML or a list of text with optional icons.
 *
 * @param  string $heading ?
 * @param  string $content ?
 * @param  array $list ?
 * @param  array $icons ?
 * @param  string $footer ?
 * @param  array $attributes ?
 * @todo Finish documenting this function. Show example of various attributes, etc.
 */
function print_side_block($heading='', $content='', $list=NULL, $icons=NULL, $footer='', $attributes = array()) {

    //Accessibility: skip block link, with $block_id to differentiate links.
    static $block_id = 0;
    $block_id++;
    $skip_text = get_string('skipblock','access').' '.$block_id;
    $skip_link = '<a href="#sb-'.$block_id.'" class="skip-block" title="'.$skip_text.'"><span class="accesshide">'.$skip_text.'</span></a>';
    $skip_dest = '<span id="sb-'.$block_id.'" class="skip-block-to"></span>';
    if (! empty($heading)) {
        $heading .= $skip_link;
    } else {
        echo $skip_link;
    }

    print_side_block_start($heading, $attributes);

    if ($content) {
        echo $content;
        if ($footer) {
            echo '<div class="footer">'. $footer .'</div>';
        }
    } else {
        if ($list) {
            $row = 0;
            //Accessibility: replaced unnecessary table with list, see themes/standard/styles_layout.css
            echo "\n<ul class='list'>\n";
            foreach ($list as $key => $string) {
                echo '<li class="r'. $row .'">';
                if ($icons) {
                    echo '<span class="icon c0">'. $icons[$key] .'</span>';
                }
                echo '<span class="c1">'. $string .'</span>';
                echo "</li>\n";
                $row = $row ? 0:1;
            }
            echo "</ul>\n";
        }
        if ($footer) {
            echo '<div class="footer">'. $footer .'</div>';
        }

    }

    print_side_block_end($attributes);
    echo $skip_dest;
}

/**
 * Starts a nice side block with an optional header.
 *
 * @param string $heading ?
 * @param array $attributes ?
 * @todo Finish documenting this function
 */
function print_side_block_start($heading='', $attributes = array()) {

    global $CFG;

    // If there are no special attributes, give a default CSS class
    if (empty($attributes) || !is_array($attributes)) {
        $attributes = array('class' => 'sideblock');

    } else if(!isset($attributes['class'])) {
        $attributes['class'] = 'sideblock';

    } else if(!strpos($attributes['class'], 'sideblock')) {
        $attributes['class'] .= ' sideblock';
    }

    // OK, the class is surely there and in addition to anything
    // else, it's tagged as a sideblock

    /*

    // IE misery: if I do it this way, blocks which start hidden cannot be "unhidden"

    // If there is a cookie to hide this thing, start it hidden
    if (!empty($attributes['id']) && isset($_COOKIE['hide:'.$attributes['id']])) {
        $attributes['class'] = 'hidden '.$attributes['class'];
    }
    */

    $attrtext = '';
    foreach ($attributes as $attr => $val) {
        $attrtext .= ' '.$attr.'="'.$val.'"';
    }

    echo '<div '.$attrtext.'>';
    if ($heading) {
        //Accessibility: replaced <div> with H2; no, H2 more appropriate in moodleblock.class.php: _title_html.
        echo '<div class="header">'.$heading.'</div>';
    }
    echo '<div class="content">';

}


/**
 * Print table ending tags for a side block box.
 */
function print_side_block_end($attributes = array()) {
    global $CFG;

    echo '</div></div>';

    // IE workaround: if I do it THIS way, it works! WTF?
    if (!empty($CFG->allowuserblockhiding) && isset($attributes['id'])) {
        echo '<script type="text/javascript"><!-- '."\n".'elementCookieHide("'.$attributes['id'].'"); '."\n".'--></script>';
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
function print_speller_code ($usehtmleditor=false, $return=false) {
    global $CFG;
    $str = '';

    if(!$usehtmleditor) {
        $str .= "\n".'<script language="javascript" type="text/javascript">'."\n";
        $str .= 'function openSpellChecker() {'."\n";
        $str .= "\tvar speller = new spellChecker();\n";
        $str .= "\tspeller.popUpUrl = \"" . $CFG->wwwroot ."/lib/speller/spellchecker.html\";\n";
        $str .= "\tspeller.spellCheckScript = \"". $CFG->wwwroot ."/lib/speller/server-scripts/spellchecker.php\";\n";
        $str .= "\tspeller.spellCheckAll();\n";
        $str .= '}'."\n";
        $str .= '</script>'."\n";
    } else {
        $str .= "\nfunction spellClickHandler(editor, buttonId) {\n";
        $str .= "\teditor._textArea.value = editor.getHTML();\n";
        $str .= "\tvar speller = new spellChecker( editor._textArea );\n";
        $str .= "\tspeller.popUpUrl = \"" . $CFG->wwwroot ."/lib/speller/spellchecker.html\";\n";
        $str .= "\tspeller.spellCheckScript = \"". $CFG->wwwroot ."/lib/speller/server-scripts/spellchecker.php\";\n";
        $str .= "\tspeller._moogle_edit=1;\n";
        $str .= "\tspeller._editor=editor;\n";
        $str .= "\tspeller.openChecker();\n";
        $str .= '}'."\n";
    }
    if ($return) {
        return $str;
    }
    echo $str;
}

/**
 * Print button for spellchecking when editor is disabled
 */
function print_speller_button () {
    echo '<input type="button" value="Check spelling" onclick="openSpellChecker();" />'."\n";
}


function page_id_and_class(&$getid, &$getclass) {
    // Create class and id for this page
    global $CFG, $ME;

    static $class = NULL;
    static $id    = NULL;

    if (empty($CFG->pagepath)) {
        $CFG->pagepath = $ME;
    }

    if (empty($class) || empty($id)) {
        $path = str_replace($CFG->httpswwwroot.'/', '', $CFG->pagepath);  //Because the page could be HTTPSPAGEREQUIRED
        $path = str_replace('.php', '', $path);
        if (substr($path, -1) == '/') {
            $path .= 'index';
        }
        if (empty($path) || $path == 'index') {
            $id    = 'site-index';
            $class = 'course';
        } else if (substr($path, 0, 5) == 'admin') {
            $id    = str_replace('/', '-', $path);
            $class = 'admin';
        } else {
            $id    = str_replace('/', '-', $path);
            $class = explode('-', $id);
            array_pop($class);
            $class = implode('-', $class);
        }
    }

    $getid    = $id;
    $getclass = $class;
}

/**
 * Prints a maintenance message from /maintenance.html
 */
function print_maintenance_message () {
    global $CFG, $SITE;

    print_header(strip_tags($SITE->fullname), $SITE->fullname, 'home');
    print_simple_box_start('center');
    print_heading(get_string('sitemaintenance', 'admin'));
    @include($CFG->dataroot.'/1/maintenance.html');
    print_simple_box_end();
    print_footer();
}

/**
 * Adjust the list of allowed tags based on $CFG->allowobjectembed and user roles (admin)
 */
function adjust_allowed_tags() {

    global $CFG, $ALLOWED_TAGS;

    if (!empty($CFG->allowobjectembed)) {
        $ALLOWED_TAGS .= '<embed><object>';
    }
}

/// Some code to print tabs

/// A class for tabs
class tabobject {
    var $id;
    var $link;
    var $text;
    var $linkedwhenselected;

    /// A constructor just because I like constructors
    function tabobject ($id, $link='', $text='', $title='', $linkedwhenselected=false) {
        $this->id   = $id;
        $this->link = $link;
        $this->text = $text;
        $this->title = $title ? $title : $text;
        $this->linkedwhenselected = $linkedwhenselected;
    }


    /// a method to look after the messy business of setting up a tab cell
    /// with all the appropriate classes and things
    function createtab ($selected=false, $inactive=false, $activetwo=false, $last=false) {
        $str  = '';
        $astr = '';
        $cstr = '';

    /// The text and anchor for this tab
        if ($inactive || $activetwo || ($selected && !$this->linkedwhenselected)) {
            $astr .= $this->text;
        } else {
            $astr .= '<a href="'.$this->link.'" title="'.$this->title.'">'.$this->text.'</a>';
        }

    /// There's an IE bug with background images in <a> tags
    /// so we put a div around so that we can add a background image
        $astr = '<div class="tablink">'.$astr.'</div>';

    /// Set the class for inactive cells
        if ($inactive) {
            $cstr .= ' inactive';

        /// Set the class for active cells in the second row
            if ($activetwo) {
                $cstr .= ' activetwo';
            }

    /// Set the class for the selected cell
        } else if ($selected) {
            $cstr .= ' selected';

    /// Set the standard class for a cell
        } else {
            $cstr .= ' active';
        }


    /// Are we on the last tab in this row?
        if ($last) {
            $astr = '<div class="last">'.$astr.'</div>';
        }

    /// Lets set up the tab cell
        $str .= '<td';
        if (!empty($cstr)) {
            $str .= ' class="'.ltrim($cstr).'"';
        }
        $str .= '>';
        $str .= $astr;
        $str .= '</td>';

        return $str;
    }

}



/**
 * Returns a string containing a table with tabs inside and formatted with
 * CSS styles.
 *
 * @param array $tabrows An array of rows where each row is an array of tab objects
 * @param string $selected  The id of the selected tab
 * @param array $inactive  Ids of inactive tabs
**/
function print_tabs($tabrows, $selected=NULL, $inactive=NULL, $activetwo=NULL, $return=false) {
    global $CFG;

/// Bring the row with the selected tab to the front
    if (!empty($CFG->tabselectedtofront) and ($selected !== NULL) ) {
        $found = false;
        $frontrows = array();
        $rearrows  = array();
        foreach ($tabrows as $row) {
            if ($found) {
                $rearrows[] = $row;
            } else {
                foreach ($row as $tab) {
                    if ($found) {
                        continue;
                    }
                    $found = ($selected == $tab->id);
                }
                $frontrows[] = $row;
            }
        }
        $tabrows = array_merge($rearrows,$frontrows);
    }

/// $inactive must be an array
    if (!is_array($inactive)) {
        $inactive = array();
    }

/// $activetwo must be an array
    if (!is_array($activetwo)) {
        $activetwo = array();
    }

/// A table to encapsulate the tabs
    $str = '<table class="tabs" cellspacing="0">';
    $str .= '<tr><td class="left side"></td><td>';

    $rowcount = count($tabrows);
/// Cycle through the tab rows
    foreach ($tabrows as $row) {

        $rowcount--;

        $str .= '<table class="tabrow r'.$rowcount.'" cellspacing="0">';
        $str .= '<tr>';

        $numberoftabs = count($row);
        $currenttab   = 0;
        $cstr         = '';
        $astr         = '';


    /// Cycle through the tabs
        foreach ($row as $tab) {
            $currenttab++;

            $str .= $tab->createtab( ($selected == $tab->id), (in_array($tab->id, $inactive)), (in_array($tab->id, $activetwo)), ($currenttab == $numberoftabs) );
        }

        $str .= '</tr>';
        $str .= '</table>';
    }
    $str .= '</td><td class="right side"></td></tr>';
    $str .= '</table>';

    if ($return) {
        return $str;
    }
    echo $str;
}
/**
 * Returns a string containing a link to the user documentation for the current
 * page. Also contains an icon by default. Shown to teachers and admin only.
 *
 * @param string $text      The text to be displayed for the link
 * @param string $iconpath  The path to the icon to be displayed
 */
function page_doc_link($text='', $iconpath='') {
    global $ME, $CFG;

    if (empty($CFG->pagepath)) {
        $CFG->pagepath = $ME;
    }

    $path = str_replace($CFG->httpswwwroot.'/','', $CFG->pagepath);  // Because the page could be HTTPSPAGEREQUIRED
    $path = str_replace('.php', '', $path);

    if (empty($path)) {   // Not for home page
        return '';
    }
    return doc_link($path, $text, $iconpath);
}

/**
 * Returns a string containing a link to the user documentation.
 * Also contains an icon by default. Shown to teachers and admin only.
 *
 * @param string $path      The relative link
 * @param string $path      The page link after doc root and language, no
 *                              leading slash.
 * @param string $iconpath  The path to the icon to be displayed
 */
function doc_link($path='', $text='', $iconpath='') {
    global $CFG;

    if (empty($CFG->docroot) ||  !isteacherinanycourse()) {
        return '';
    }

    $target = '';
    if (!empty($CFG->doctonewwindow)) {
        $target = ' target="_blank"';
    }

    $lang = str_replace('_utf8', '', current_language());

    $str = '<a href="' .$CFG->docroot. '/' .$lang. '/' .$path. '"' .$target. '>';

    if (empty($iconpath)) {
        $iconpath = $CFG->httpswwwroot . '/pix/docs.gif';
    }

    // alt left blank intentionally to prevent repetition in screenreaders
    $str .= '<img class="iconhelp" src="' .$iconpath. '" alt="" />' .$text. '</a>';

    return $str;
}



// vim:autoindent:expandtab:shiftwidth=4:tabstop=4:tw=140:
?>
