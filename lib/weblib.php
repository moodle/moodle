<?PHP // $Id$

// weblib.php
//
// Library of useful PHP functions and constants related to web pages.
//
//

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

$ALLOWED_TAGS = "<b><i><u><font><table><tbody><span><div><tr><td><ol><ul><dl><li><dt><dd><h1><h2><h3><hr><img><a>";


/// Functions

function s($var) {
/// returns $var with HTML characters (like "<", ">", etc.) properly quoted,
/// or if $var is empty, will return an empty string. 

	return empty($var) ? "" : htmlSpecialChars(stripslashes($var));
}

function p($var) {
/// prints $var with HTML characters (like "<", ">", etc.) properly quoted,
/// or if $var is empty, will print an empty string. 

	echo empty($var) ? "" : htmlSpecialChars(stripslashes($var));
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

    global $HTTP_REFERER;

    return strip_querystring(nvl($HTTP_REFERER));
}


function me() {
/// returns the name of the current script, WITH the querystring portion.
/// this function is necessary because PHP_SELF and REQUEST_URI and PATH_INFO
/// return different things depending on a lot of things like your OS, Web
/// server, and the way PHP is compiled (ie. as a CGI, module, ISAPI, etc.) 

    global $REQUEST_URI, $PATH_INFO, $PHP_SELF;

	if ($REQUEST_URI) {
		return $REQUEST_URI;

	} else if ($PATH_INFO) {
		return $PATH_INFO;

	} else if ($PHP_SELF) {
		return $PHP_SELF;

	} else {
        notify("Error: Could not find any of these web server variables: \$REQUEST_URI, \$PATH_INFO or \$PHP_SELF");
    }
}


function qualified_me() {
/// like me() but returns a full URL 

    global $HTTPS, $HTTP_HOST;

    if (!$HTTP_HOST) {
        notify("Error: could not find web server variable: \$HTTP_HOST");
    }

	$protocol = (isset($HTTPS) && $HTTPS == "on") ? "https://" : "http://";
	$url_prefix = "$protocol$HTTP_HOST";
	return $url_prefix . me();
}


function match_referer($good_referer = "") {
/// returns true if the referer is the same as the good_referer.  If
/// good_refer is not specified, use qualified_me as the good_referer 

	if ($good_referer == "") { 
        $good_referer = qualified_me(); 
    }
	return $good_referer == get_referer();
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
    echo "<INPUT TYPE=button onClick=\"self.close();\" VALUE=\"Close this window\">";
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
    }
    $output = "<SELECT NAME=$name $javascript>\n";
    if ($nothing) {
        $output .= "   <OPTION VALUE=\"$nothingvalue\"\n";
        if ($nothingvalue == $selected) {
            $output .= " SELECTED";
        }
        $output .= ">$nothing</OPTION>\n";
    }
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
    $output .= "</SELECT>\n";

    if ($return) {
        return $output;
    } else {
        echo $output;
    }
}   

function popup_form ($common, $options, $formname, $selected="", $nothing="choose") {
///  Implements a complete little popup form
///  $common   = the URL up to the point of the variable that changes
///  $options  = A list of value-label pairs for the popup list
///  $formname = name must be unique on the page
///  $selected = the option that is already selected
///  $nothing  = The label for the "no choice" option

    if ($nothing == "choose") {
        $nothing = get_string("choose")."...";
    }

    echo "<FORM NAME=$formname>";
    echo "<SELECT NAME=popup onChange=\"window.location=document.$formname.popup.options[document.$formname.popup.selectedIndex].value\">\n";

    if ($nothing != "") {
        echo "   <OPTION VALUE=\"javascript:void(0)\">$nothing</OPTION>\n";
    }

    foreach ($options as $value => $label) {
        echo "   <OPTION VALUE=\"$common$value\"";
        if ($value == $selected) {
            echo " SELECTED";
        }
        if ($label) {
            echo ">$label</OPTION>\n";
        } else {
            echo ">$value</OPTION>\n";
        }
    }
    echo "</SELECT></FORM>\n";
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


function get_slash_arguments($i=0) {
/// Extracts arguments from "/foo/bar/something"
/// eg http://mysite.com/script.php/foo/bar/something
/// Might only work on Apache

    global $PATH_INFO;

    if (!isset($PATH_INFO)) {
        return false;
    }

    if (strpos($PATH_INFO, "..")) {  // check for funny business
        return false;
    }

    $args = explode("/", $PATH_INFO);

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

function format_text($text, $format, $options=NULL) {
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
    $text = eregi_replace("([[:space:]])www.([^[:space:]]*)([[:alnum:]#?/&=])", 
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


?>
