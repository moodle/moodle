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
 * Library of functions for web output
 *
 * Library of all general-purpose Moodle PHP functions and constants
 * that produce HTML output
 *
 * Other main libraries:
 * - datalib.php - functions that access the database.
 * - moodlelib.php - general-purpose Moodle functions.
 *
 * @package moodlecore
 * @copyright 1999 onwards Martin Dougiamas {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
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
 * Deprecated: left here just to note that '3' is not used (at the moment)
 * and to catch any latent wiki-like text (which generates an error)
 */
define('FORMAT_WIKI',     '3');   // Wiki-formatted text

/**
 * Markdown-formatted text http://daringfireball.net/projects/markdown/
 */
define('FORMAT_MARKDOWN', '4');   // Markdown-formatted text http://daringfireball.net/projects/markdown/

/**
 * TRUSTTEXT marker - if present in text, text cleaning should be bypassed
 */
define('TRUSTTEXT', '#####TRUSTTEXT#####');


/**
 * Allowed tags - string of html tags that can be tested against for safe html tags
 * @global string $ALLOWED_TAGS
 * @name $ALLOWED_TAGS
 */
global $ALLOWED_TAGS;
$ALLOWED_TAGS =
'<p><br><b><i><u><font><table><tbody><thead><tfoot><span><div><tr><td><th><ol><ul><dl><li><dt><dd><h1><h2><h3><h4><h5><h6><hr><img><a><strong><emphasis><em><sup><sub><address><cite><blockquote><pre><strike><param><acronym><nolink><lang><tex><algebra><math><mi><mn><mo><mtext><mspace><ms><mrow><mfrac><msqrt><mroot><mstyle><merror><mpadded><mphantom><mfenced><msub><msup><msubsup><munder><mover><munderover><mmultiscripts><mtable><mtr><mtd><maligngroup><malignmark><maction><cn><ci><apply><reln><fn><interval><inverse><sep><condition><declare><lambda><compose><ident><quotient><exp><factorial><divide><max><min><minus><plus><power><rem><times><root><gcd><and><or><xor><not><implies><forall><exists><abs><conjugate><eq><neq><gt><lt><geq><leq><ln><log><int><diff><partialdiff><lowlimit><uplimit><bvar><degree><set><list><union><intersect><in><notin><subset><prsubset><notsubset><notprsubset><setdiff><sum><product><limit><tendsto><mean><sdev><variance><median><mode><moment><vector><matrix><matrixrow><determinant><transpose><selector><annotation><semantics><annotation-xml><tt><code>';

/**
 * Allowed protocols - array of protocols that are safe to use in links and so on
 * @global string $ALLOWED_PROTOCOLS
 * @name $ALLOWED_PROTOCOLS
 */
$ALLOWED_PROTOCOLS = array('http', 'https', 'ftp', 'news', 'mailto', 'rtsp', 'teamspeak', 'gopher', 'mms',
                           'color', 'callto', 'cursor', 'text-align', 'font-size', 'font-weight', 'font-style', 'font-family',
                           'border', 'margin', 'padding', 'background', 'background-color', 'text-decoration');   // CSS as well to get through kses


/// Functions

/**
 * Add quotes to HTML characters
 *
 * Returns $var with HTML characters (like "<", ">", etc.) properly quoted.
 * This function is very similar to {@link p()}
 *
 * @todo Remove obsolete param $obsolete if not used anywhere
 *
 * @param string $var the string potentially containing HTML characters
 * @param boolean $obsolete no longer used.
 * @return string
 */
function s($var, $obsolete = false) {

    if ($var == '0') {  // for integer 0, boolean false, string '0'
        return '0';
    }

    return preg_replace("/&amp;(#\d+);/i", "&$1;", htmlspecialchars($var));
}

/**
 * Add quotes to HTML characters
 *
 * Prints $var with HTML characters (like "<", ">", etc.) properly quoted.
 * This function simply calls {@link s()}
 * @see s()
 *
 * @todo Remove obsolete param $obsolete if not used anywhere
 *
 * @param string $var the string potentially containing HTML characters
 * @param boolean $obsolete no longer used.
 * @return string
 */
function p($var, $obsolete = false) {
    echo s($var, $obsolete);
}

/**
 * Does proper javascript quoting.
 *
 * Do not use addslashes anymore, because it does not work when magic_quotes_sybase is enabled.
 *
 * @param mixed $var String, Array, or Object to add slashes to
 * @return mixed quoted result
 */
function addslashes_js($var) {
    if (is_string($var)) {
        $var = str_replace('\\', '\\\\', $var);
        $var = str_replace(array('\'', '"', "\n", "\r", "\0"), array('\\\'', '\\"', '\\n', '\\r', '\\0'), $var);
        $var = str_replace('</', '<\/', $var);   // XHTML compliance
    } else if (is_array($var)) {
        $var = array_map('addslashes_js', $var);
    } else if (is_object($var)) {
        $a = get_object_vars($var);
        foreach ($a as $key=>$value) {
          $a[$key] = addslashes_js($value);
        }
        $var = (object)$a;
    }
    return $var;
}

/**
 * Remove query string from url
 *
 * Takes in a URL and returns it without the querystring portion
 *
 * @param string $url the url which may have a query string attached
 * @return string The remaining URL
 */
 function strip_querystring($url) {

    if ($commapos = strpos($url, '?')) {
        return substr($url, 0, $commapos);
    } else {
        return $url;
    }
}

/**
 * Returns the URL of the HTTP_REFERER, less the querystring portion if required
 *
 * @uses $_SERVER
 * @param boolean $stripquery if true, also removes the query part of the url.
 * @return string The resulting referer or emtpy string
 */
function get_referer($stripquery=true) {
    if (isset($_SERVER['HTTP_REFERER'])) {
        if ($stripquery) {
            return strip_querystring($_SERVER['HTTP_REFERER']);
        } else {
            return $_SERVER['HTTP_REFERER'];
        }
    } else {
        return '';
    }
}


/**
 * Returns the name of the current script, WITH the querystring portion.
 *
 * This function is necessary because PHP_SELF and REQUEST_URI and SCRIPT_NAME
 * return different things depending on a lot of things like your OS, Web
 * server, and the way PHP is compiled (ie. as a CGI, module, ISAPI, etc.)
 * <b>NOTE:</b> This function returns false if the global variables needed are not set.
 *
 * @global string
 * @return mixed String, or false if the global variables needed are not set
 */
function me() {
    global $ME;
    return $ME;
}

/**
 * Returns the name of the current script, WITH the full URL.
 *
 * This function is necessary because PHP_SELF and REQUEST_URI and SCRIPT_NAME
 * return different things depending on a lot of things like your OS, Web
 * server, and the way PHP is compiled (ie. as a CGI, module, ISAPI, etc.
 * <b>NOTE:</b> This function returns false if the global variables needed are not set.
 *
 * Like {@link me()} but returns a full URL
 * @see me()
 *
 * @global string
 * @return mixed String, or false if the global variables needed are not set
 */
function qualified_me() {
    global $FULLME;
    return $FULLME;
}

/**
 * Class for creating and manipulating urls.
 *
 * It can be used in moodle pages where config.php has been included without any further includes.
 *
 * It is useful for manipulating urls with long lists of params. 
 * One situation where it will be useful is a page which links to itself to perfrom various actions 
 * and / or to process form data. A moodle_url object :
 * can be created for a page to refer to itself with all the proper get params being passed from page call to 
 * page call and methods can be used to output a url including all the params, optionally adding and overriding 
 * params and can also be used to
 *     - output the url without any get params
 *     - and output the params as hidden fields to be output within a form 
 *
 * One important usage note is that data passed to methods out, out_action, get_query_string and
 * hidden_params_out affect what is returned by the function and do not change the data stored in the object. 
 * This is to help with typical usage of these objects where one object is used to output urls 
 * in many places in a page. 
 *
 * @link http://docs.moodle.org/en/Development:lib/weblib.php_moodle_url See short write up here
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package moodlecore
 */
class moodle_url {
    /**
     * @var string
     * @access protected
     */
    protected $scheme = ''; // e.g. http
    protected $host = '';
    protected $port = '';
    protected $user = '';
    protected $pass = '';
    protected $path = '';
    protected $fragment = '';
    /**
     * @var array 
     * @access protected
     */
    protected $params = array(); // Associative array of query string params

    /**
     * Pass no arguments to create a url that refers to this page. 
     * Use empty string to create empty url.
     *
     * @global string
     * @param mixed $url a number of different forms are accespted:
     *      null - create a URL that is the same as the URL used to load this page, but with no query string
     *      '' - and empty URL
     *      string - a URL, will be parsed into it's bits, including query string
     *      array - as returned from the PHP function parse_url
     *      moodle_url - make a copy of another moodle_url
     * @param array $params these params override anything in the query string
     *      where params have the same name.
     */
    public function __construct($url = null, $params = array()) {
        if ($url === '') {
            // Leave URL blank.
        } else if (is_a($url, 'moodle_url')) {
            $this->scheme = $url->scheme;
            $this->host = $url->host;
            $this->port = $url->port;
            $this->user = $url->user;
            $this->pass = $url->pass;
            $this->path = $url->path;
            $this->fragment = $url->fragment;
            $this->params = $url->params;
        } else {
            if ($url === null) {
                global $ME;
                $url = $ME;
            }
            if (is_string($url)) {
                $url = parse_url($url);
            }
            $parts = $url;
            if ($parts === FALSE) {
                throw new moodle_exception('invalidurl');
            }
            if (isset($parts['query'])) {
                parse_str(str_replace('&amp;', '&', $parts['query']), $this->params);
            }
            unset($parts['query']);
            foreach ($parts as $key => $value) {
                $this->$key = $value;
            }
        }
        $this->params($params);
    }

    /**
     * Add an array of params to the params for this page. 
     *
     * The added params override existing ones if they have the same name.
     *
     * @param array $params Defaults to null. If null then return value of param 'name'.
     * @return array Array of Params for url.
     */
    public function params($params = null) {
        if (!is_null($params)) {
            return $this->params = $params + $this->params;
        } else {
            return $this->params;
        }
    }

    /**
     * Remove all params if no arguments passed. 
     * Remove selected params if arguments are passed. 
     *
     * Can be called as either remove_params('param1', 'param2')
     * or remove_params(array('param1', 'param2')).
     *
     * @param mixed $params either an array of param names, or a string param name,
     * @param string $params,... any number of additional param names.
     */
    public function remove_params($params = NULL) {
        if (empty($params)) {
            $this->params = array();
            return;
        }
        if (!is_array($params)) {
            $params = func_get_args();
        }
        foreach ($params as $param) {
            if (isset($this->params[$param])) {
                unset($this->params[$param]);
            }
        }
    }

    /**
     * Add a param to the params for this page. 
     *
     * The added param overrides existing one if theyhave the same name.
     *
     * @param string $paramname name
     * @param string $param Param value. Defaults to null. If null then return value of param 'name'
     * @return void|string If $param was null then the value of $paramname was returned
     *      (null is returned if that param does not exist).
     */
    public function param($paramname, $param = null) {
        if (!is_null($param)) {
            $this->params = array($paramname => $param) + $this->params;
        } else if (array_key_exists($paramname, $this->params)) {
            return $this->params[$paramname];
        } else {
            return null;
        }
    }

    /**
     * Get the params as as a query string.
     *
     * @param array $overrideparams params to add to the output params, these
     *      override existing ones with the same name.
     * @param boolean $escaped Use &amp; as params separator instead of plain &
     * @return string query string that can be added to a url.
     */
    public function get_query_string($overrideparams = array(), $escaped = true) {
        $arr = array();
        $params = $overrideparams + $this->params;
        foreach ($params as $key => $val) {
           $arr[] = urlencode($key)."=".urlencode($val);
        }
        if ($escaped) {
            return implode('&amp;', $arr);
        } else {
            return implode('&', $arr);
        }
    }

    /**
     * Outputs params as hidden form elements.
     *
     * @param array $exclude params to ignore
     * @param integer $indent indentation
     * @param array $overrideparams params to add to the output params, these
     *      override existing ones with the same name.
     * @return string html for form elements.
     */
    public function hidden_params_out($exclude = array(), $indent = 0, $overrideparams=array()) {
        $tabindent = str_repeat("\t", $indent);
        $str = '';
        $params = $overrideparams + $this->params;
        foreach ($params as $key => $val) {
            if (FALSE === array_search($key, $exclude)) {
                $val = s($val);
                $str.= "$tabindent<input type=\"hidden\" name=\"$key\" value=\"$val\" />\n";
            }
        }
        return $str;
    }

    /**
     * Output url
     *
     * If you use the returned URL in HTML code, you want the escaped ampersands. If you use
     * the returned URL in HTTP headers, you want $escaped=false.
     *
     * @param boolean $omitquerystring whether to output page params as a query string in the url.
     * @param array $overrideparams params to add to the output url, these override existing ones with the same name.
     * @param boolean $escaped Use &amp; as params separator instead of plain &
     * @return string Resulting URL
     */
    public function out($omitquerystring = false, $overrideparams = array(), $escaped = true) {
        $uri = $this->scheme ? $this->scheme.':'.((strtolower($this->scheme) == 'mailto') ? '':'//'): '';
        $uri .= $this->user ? $this->user.($this->pass? ':'.$this->pass:'').'@':'';
        $uri .= $this->host ? $this->host : '';
        $uri .= $this->port ? ':'.$this->port : '';
        $uri .= $this->path ? $this->path : '';
        if (!$omitquerystring) {
            $querystring = $this->get_query_string($overrideparams, $escaped);
            if ($querystring) {
                $uri .= '?' . $querystring;
            }
        }
        $uri .= $this->fragment ? '#'.$this->fragment : '';
        return $uri;
    }

    /**
     * Output action url with sesskey
     *
     * Adds sesskey and overriderparams then calls {@link out()}
     * @see out()
     *
     * @param array $overrideparams Allows you to override params
     * @return string url
     */
    public function out_action($overrideparams = array()) {
        $overrideparams = array('sesskey'=> sesskey()) + $overrideparams;
        return $this->out(false, $overrideparams);
    }
}

/**
 * Determine if there is data waiting to be processed from a form
 *
 * Used on most forms in Moodle to check for data
 * Returns the data as an object, if it's found.
 * This object can be used in foreach loops without
 * casting because it's cast to (array) automatically
 *
 * Checks that submitted POST data exists and returns it as object.
 *
 * @uses $_POST
 * @return mixed false or object
 */
function data_submitted() {

    if (empty($_POST)) {
        return false;
    } else {
        return (object)$_POST;
    }
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
    $length = $textlib->strlen($string);
    $wordlength = 0;

    for ($i=0; $i<$length; $i++) {
        $char = $textlib->substr($string, $i, 1);
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
 * This function will print a button/link/etc. form element
 * that will work on both Javascript and non-javascript browsers.
 *
 * Relies on the Javascript function openpopup in javascript.php
 * All parameters default to null, only $type and $url are mandatory.
 *
 * $url must be relative to home page  eg /mod/survey/stuff.php
 *
 * @global object
 * @param string $type Can be 'button' or 'link'
 * @param string $url Web link. Either relative to $CFG->wwwroot, or a full URL.
 * @param string $name Name to be assigned to the popup window (this is used by
 *   client-side scripts to "talk" to the popup window)
 * @param string $linkname Text to be displayed as web link
 * @param int $height Height to assign to popup window
 * @param int $width Height to assign to popup window
 * @param string $title Text to be displayed as popup page title
 * @param string $options List of additional options for popup window
 * @param bool $return If true, return as a string, otherwise print
 * @param string $id id added to the element
 * @param string $class class added to the element
 * @return string
 */
function element_to_popup_window ($type=null, $url=null, $name=null, $linkname=null,
                                  $height=400, $width=500, $title=null,
                                  $options=null, $return=false, $id=null, $class=null) {

    if (is_null($url)) {
        debugging('You must give the url to display in the popup. URL is missing - can\'t create popup window.', DEBUG_DEVELOPER);
    }

    global $CFG;

    if ($options == 'none') { // 'none' is legacy, should be removed in v2.0
        $options = null;
    }

    // add some sane default options for popup windows
    if (!$options) {
        $options = 'menubar=0,location=0,scrollbars,resizable';
    }
    if ($width) {
        $options .= ',width='. $width;
    }
    if ($height) {
        $options .= ',height='. $height;
    }
    if ($id) {
        $id = ' id="'.$id.'" ';
    }
    if ($class) {
        $class = ' class="'.$class.'" ';
    }
    if ($name) {
        $_name = $name;
        if (($name = preg_replace("/\s/", '_', $name)) != $_name) {
            debugging('The $name of a popup window shouldn\'t contain spaces - string modified. '. $_name .' changed to '. $name, DEBUG_DEVELOPER);
        }
    } else {
        $name = 'popup';
    }

    // get some default string, using the localized version of legacy defaults
    if (is_null($linkname) || $linkname === '') {
        $linkname = get_string('clickhere');
    }
    if (!$title) {
        $title = get_string('popupwindowname');
    }

    $fullscreen = 0; // must be passed to openpopup
    $element = '';

    switch ($type) {
        case 'button':
            $element = '<input type="button" name="'. $name .'" title="'. $title .'" value="'. $linkname .'" '. $id . $class .
                       "onclick=\"return openpopup('$url', '$name', '$options', $fullscreen);\" />\n";
            break;
        case 'link':
            // Add wwwroot only if the URL does not already start with http:// or https://
            if (!preg_match('|https?://|', $url)) {
                $url = $CFG->wwwroot . $url;
            }
            $element = '<a title="'. s(strip_tags($title)) .'" href="'. $url .'" '.
                       "onclick=\"this.target='$name'; return openpopup('$url', '$name', '$options', $fullscreen);\">$linkname</a>";
            break;
        default :
            print_error('cannotcreatepopupwin');
            break;
    }

    if ($return) {
        return $element;
    } else {
        echo $element;
    }
}

/**
 * Creates and displays (or returns) a link to a popup window, using element_to_popup_window function.
 *
 * Simply calls {@link element_to_popup_window()} with type set to 'link'
 * @see element_to_popup_window()
 *
 * @param string $url Web link. Either relative to $CFG->wwwroot, or a full URL.
 * @param string $name Name to be assigned to the popup window (this is used by
 *   client-side scripts to "talk" to the popup window)
 * @param string $linkname Text to be displayed as web link
 * @param int $height Height to assign to popup window
 * @param int $width Height to assign to popup window
 * @param string $title Text to be displayed as popup page title
 * @param string $options List of additional options for popup window
 * @param bool $return If true, return as a string, otherwise print
 * @param string $id id added to the element
 * @param string $class class added to the element
 * @return string html code to display a link to a popup window.
 */
function link_to_popup_window ($url, $name=null, $linkname=null,
                               $height=400, $width=500, $title=null,
                               $options=null, $return=false) {

    return element_to_popup_window('link', $url, $name, $linkname, $height, $width, $title, $options, $return, null, null);
}

/**
 * Creates and displays (or returns) a buttons to a popup window, using element_to_popup_window function.
 *
 * Simply calls {@link element_to_popup_window()} with type set to 'button'
 * @see element_to_popup_window()
 *
 * @param string $url Web link. Either relative to $CFG->wwwroot, or a full URL.
 * @param string $name Name to be assigned to the popup window (this is used by
 *   client-side scripts to "talk" to the popup window)
 * @param string $linkname Text to be displayed as web link
 * @param int $height Height to assign to popup window
 * @param int $width Height to assign to popup window
 * @param string $title Text to be displayed as popup page title
 * @param string $options List of additional options for popup window
 * @param bool $return If true, return as a string, otherwise print
 * @param string $id id added to the element
 * @param string $class class added to the element
 * @return string html code to display a link to a popup window.
 */
function button_to_popup_window ($url, $name=null, $linkname=null,
                                 $height=400, $width=500, $title=null, $options=null, $return=false,
                                 $id=null, $class=null) {

    return element_to_popup_window('button', $url, $name, $linkname, $height, $width, $title, $options, $return, $id, $class);
}


/**
 * Prints a simple button to close a window
 *
 * @global object
 * @param string $name Name of the window to close
 * @param boolean $return whether this function should return a string or output it.
 * @param boolean $reloadopener if true, clicking the button will also reload
 *      the page that opend this popup window.
 * @return string|void if $return is true, void otherwise
 */
function close_window_button($name='closewindow', $return=false, $reloadopener = false) {
    global $CFG;

    $js = 'self.close();';
    if ($reloadopener) {
        $js = 'window.opener.location.reload(1);' . $js;
    }

    $output = '';

    $output .= '<div class="closewindow">' . "\n";
    $output .= '<form action="#"><div>';
    $output .= '<input type="button" onclick="' . $js . '" value="'.get_string($name).'" />';
    $output .= '</div></form>';
    $output .= '</div>' . "\n";

    if ($return) {
        return $output;
    } else {
        echo $output;
    }
}

/*
 * Try and close the current window using JavaScript, either immediately, or after a delay.
 *
 * Echo's out the resulting XHTML & javascript
 *
 * @global object
 * @global object
 * @param integer $delay a delay in seconds before closing the window. Default 0.
 * @param boolean $reloadopener if true, we will see if this window was a pop-up, and try
 *      to reload the parent window before this one closes.
 */
function close_window($delay = 0, $reloadopener = false) {
    global $THEME, $PAGE;

    if (!$PAGE->headerprinted) {
        print_header(get_string('closewindow'));
    } else {
        print_container_end_all(false, $THEME->open_header_containers);
    }

    if ($reloadopener) {
        $function = 'close_window_reloading_opener';
    } else {
        $function = 'close_window';
    }
    echo '<p class="centerpara">' . get_string('windowclosing') . '</p>';

    $PAGE->requires->js_function_call($function)->after_delay($delay);

    print_footer('empty');
    exit;
}

/**
 * Given an array of values, output the HTML for a select element with those options.
 *
 * Normally, you only need to use the first few parameters.
 *
 * @param array $options The options to offer. An array of the form
 *      $options[{value}] = {text displayed for that option};
 * @param string $name the name of this form control, as in &lt;select name="..." ...
 * @param string $selected the option to select initially, default none.
 * @param string $nothing The label for the 'nothing is selected' option. Defaults to get_string('choose').
 *      Set this to '' if you don't want a 'nothing is selected' option.
 * @param string $script if not '', then this is added to the &lt;select> element as an onchange handler.
 * @param string $nothingvalue The value corresponding to the $nothing option. Defaults to 0.
 * @param boolean $return if false (the default) the the output is printed directly, If true, the
 *      generated HTML is returned as a string.
 * @param boolean $disabled if true, the select is generated in a disabled state. Default, false.
 * @param int $tabindex if give, sets the tabindex attribute on the &lt;select> element. Default none.
 * @param string $id value to use for the id attribute of the &lt;select> element. If none is given,
 *      then a suitable one is constructed.
 * @param mixed $listbox if false, display as a dropdown menu. If true, display as a list box.
 *      By default, the list box will have a number of rows equal to min(10, count($options)), but if
 *      $listbox is an integer, that number is used for size instead.
 * @param boolean $multiple if true, enable multiple selections, else only 1 item can be selected. Used
 *      when $listbox display is enabled
 * @param string $class value to use for the class attribute of the &lt;select> element. If none is given,
 *      then a suitable one is constructed.
 * @return string|void If $return=true returns string, else echo's and returns void
 */
function choose_from_menu ($options, $name, $selected='', $nothing='choose', $script='',
                           $nothingvalue='0', $return=false, $disabled=false, $tabindex=0,
                           $id='', $listbox=false, $multiple=false, $class='') {

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

    if ($id ==='') {
        $id = 'menu'.$name;
         // name may contaion [], which would make an invalid id. e.g. numeric question type editing form, assignment quickgrading
        $id = str_replace('[', '', $id);
        $id = str_replace(']', '', $id);
    }

    if ($class ==='') {
        $class = 'menu'.$name;
         // name may contaion [], which would make an invalid class. e.g. numeric question type editing form, assignment quickgrading
        $class = str_replace('[', '', $class);
        $class = str_replace(']', '', $class);
    }
    $class = 'select ' . $class; /// Add 'select' selector always

    if ($listbox) {
        if (is_integer($listbox)) {
            $size = $listbox;
        } else {
            $numchoices = count($options);
            if ($nothing) {
                $numchoices += 1;
            }
            $size = min(10, $numchoices);
        }
        $attributes .= ' size="' . $size . '"';
        if ($multiple) {
            $attributes .= ' multiple="multiple"';
        }
    }

    $output = '<select id="'. $id .'" class="'. $class .'" name="'. $name .'" '. $attributes .'>' . "\n";
    if ($nothing) {
        $output .= '   <option value="'. s($nothingvalue) .'"'. "\n";
        if ($nothingvalue === $selected) {
            $output .= ' selected="selected"';
        }
        $output .= '>'. $nothing .'</option>' . "\n";
    }

    if (!empty($options)) {
        foreach ($options as $value => $label) {
            $output .= '   <option value="'. s($value) .'"';
            if ((string)$value == (string)$selected ||
                    (is_array($selected) && in_array($value, $selected))) {
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
 * Choose value 0 or 1 from a menu with options 'No' and 'Yes'.
 * Other options like choose_from_menu.
 *
 * Calls {@link choose_from_menu()} with preset arguments
 * @see choose_from_menu()
 *
 * @param string $name the name of this form control, as in &lt;select name="..." ...
 * @param string $selected the option to select initially, default none.
 * @param string $script if not '', then this is added to the &lt;select> element as an onchange handler.
 * @param boolean $return Whether this function should return a string or output it (defaults to false)
 * @param boolean $disabled (defaults to false)
 * @param int $tabindex
 * @return string|void If $return=true returns string, else echo's and returns void
 */
function choose_from_menu_yesno($name, $selected, $script = '',
        $return = false, $disabled = false, $tabindex = 0) {
    return choose_from_menu(array(get_string('no'), get_string('yes')), $name,
            $selected, '', $script, '0', $return, $disabled, $tabindex);
}

/**
 * Just like choose_from_menu, but takes a nested array (2 levels) and makes a dropdown menu
 * including option headings with the first level.
 *
 * This function is very similar to {@link choose_from_menu_yesno()} 
 * and {@link choose_from_menu()}
 *
 * @todo Add datatype handling to make sure $options is an array
 *
 * @param array $options An array of objects to choose from
 * @param string $name The XHTML field name
 * @param string $selected The value to select by default
 * @param string $nothing The label for the 'nothing is selected' option. 
 *                        Defaults to get_string('choose').
 * @param string $script If not '', then this is added to the &lt;select> element 
 *                       as an onchange handler.
 * @param string $nothingvalue The value for the first `nothing` option if $nothing is set
 * @param bool $return Whether this function should return a string or output 
 *                     it (defaults to false)
 * @param bool $disabled Is the field disabled by default
 * @param int|string $tabindex Override the tabindex attribute [numeric]
 * @return string|void If $return=true returns string, else echo's and returns void
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

            $output .= '   <optgroup label="'. s(format_string($section)) .'">'."\n";
            foreach ($values as $value => $label) {
                $output .= '   <option value="'. format_string($value) .'"';
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
 * @staticvar int $idcounter
 * @param array  $options  An array of value-label pairs for the radio group (values as keys)
 * @param string $name     Name of the radiogroup (unique in the form)
 * @param string $checked  The value that is already checked
 * @param bool $return Whether this function should return a string or output 
 *                     it (defaults to false)
 * @return string|void If $return=true returns string, else echo's and returns void
 */
function choose_from_radio ($options, $name, $checked='', $return=false) {

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

    if ($return) {
        return $output;
    } else {
        echo $output;
    }
}

/** 
 * Display an standard html checkbox with an optional label
 *
 * @staticvar int $idcounter
 * @param string $name    The name of the checkbox
 * @param string $value   The valus that the checkbox will pass when checked
 * @param bool $checked The flag to tell the checkbox initial state
 * @param string $label   The label to be showed near the checkbox
 * @param string $alt     The info to be inserted in the alt tag
 * @param string $script If not '', then this is added to the checkbox element 
 *                       as an onchange handler.
 * @param bool $return Whether this function should return a string or output 
 *                     it (defaults to false)
 * @return string|void If $return=true returns string, else echo's and returns void
 */
function print_checkbox ($name, $value, $checked = true, $label = '', $alt = '', $script='',$return=false) {

    static $idcounter = 0;

    if (!$name) {
        $name = 'unnamed';
    }

    if ($alt) {
        $alt = strip_tags($alt);
    } else {
        $alt = 'checkbox';
    }

    if ($checked) {
        $strchecked = ' checked="checked"';
    } else {
        $strchecked = '';
    }

    $htmlid = 'auto-cb'.sprintf('%04d', ++$idcounter);
    $output  = '<span class="checkbox '.$name."\">";
    $output .= '<input name="'.$name.'" id="'.$htmlid.'" type="checkbox" value="'.$value.'" alt="'.$alt.'"'.$strchecked.' '.((!empty($script)) ? ' onclick="'.$script.'" ' : '').' />';
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

/**
 * Display an standard html text field with an optional label
 *
 * @param string $name    The name of the text field
 * @param string $value   The value of the text field
 * @param string $alt     The info to be inserted in the alt tag
 * @param int $size Sets the size attribute of the field. Defaults to 50
 * @param int $maxlength Sets the maxlength attribute of the field. Not set by default
 * @param bool $return Whether this function should return a string or output 
 *                     it (defaults to false)
 * @return string|void If $return=true returns string, else echo's and returns void
 */
function print_textfield ($name, $value, $alt = '',$size=50,$maxlength=0, $return=false) {

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
 * Implements a complete little form with a dropdown menu. 
 *
 * When JavaScript is on selecting an option from the dropdown automatically 
 * submits the form (while avoiding the usual acessibility problems with this appoach). 
 * With JavaScript off, a 'Go' button is printed.
 *
 * @todo Finish documenting this function
 *
 * @global object
 * @global object
 * @param string $baseurl The target URL up to the point of the variable that changes
 * @param array $options A list of value-label pairs for the popup list
 * @param string $formid id for the control. Must be unique on the page. Used in the HTML.
 * @param string $selected The option that is initially selected
 * @param string $nothing The label for the "no choice" option
 * @param string $help The name of a help page if help is required
 * @param string $helptext The name of the label for the help button
 * @param boolean $return Indicates whether the function should return the HTML
 *         as a string or echo it directly to the page being rendered
 * @param string $targetwindow The name of the target page to open the linked page in.
 * @param string $selectlabel Text to place in a [label] element - preferred for accessibility.
 * @param array $optionsextra an array with the same keys as $options. The values are added within the corresponding <option ...> tag.
 * @param string $submitvalue Optional label for the 'Go' button. Defaults to get_string('go').
 * @param boolean $disabled If true, the menu will be displayed disabled.
 * @param boolean $showbutton If true, the button will always be shown even if JavaScript is available
 * @return string|void If $return=true returns string, else echo's and returns void
 */
function popup_form($baseurl, $options, $formid, $selected='', $nothing='choose', $help='', $helptext='', $return=false,
    $targetwindow='self', $selectlabel='', $optionsextra=NULL, $submitvalue='', $disabled=false, $showbutton=false) {
    global $CFG, $SESSION, $PAGE;
    static $go, $choose;   /// Locally cached, in case there's lots on a page

    if (empty($options)) {
        return '';
    }

    if (empty($submitvalue)){
        if (!isset($go)) {
            $go = get_string('go');
            $submitvalue=$go;
        }
    }
    if ($nothing == 'choose') {
        if (!isset($choose)) {
            $choose = get_string('choose');
        }
        $nothing = $choose.'...';
    }
    if ($disabled) {
        $disabled = ' disabled="disabled"';
    } else {
        $disabled = '';
    }

    // changed reference to document.getElementById('id_abc') instead of document.abc
    // MDL-7861
    $output = '<form action="'.$CFG->wwwroot.'/course/jumpto.php"'.
                        ' method="get" '.
                         $CFG->frametarget.
                        ' id="'.$formid.'"'.
                        ' class="popupform">';
    if ($help) {
        $button = helpbutton($help, $helptext, 'moodle', true, false, '', true);
    } else {
        $button = '';
    }

    if ($selectlabel) {
        $selectlabel = '<label for="'.$formid.'_jump">'.$selectlabel.'</label>';
    }

    if ($showbutton) {
        // Using the no-JavaScript version
        $javascript = '';
    } else if (check_browser_version('MSIE') || (check_browser_version('Opera') && !check_browser_operating_system("Linux"))) {
        //IE and Opera fire the onchange when ever you move into a dropdown list with the keyboard.
        //onfocus will call a function inside dropdown.js. It fixes this IE/Opera behavior.
        //Note: There is a bug on Opera+Linux with the javascript code (first mouse selection is inactive),
        //so we do not fix the Opera behavior on Linux
        $javascript = ' onfocus="initSelect(\''.$formid.'\','.$targetwindow.')"';
    } else {
        //Other browser
        $javascript = ' onchange="'.$targetwindow.
          '.location=document.getElementById(\''.$formid.
          '\').jump.options[document.getElementById(\''.
          $formid.'\').jump.selectedIndex].value;"';
    }

    $output .= '<div style="white-space:nowrap">'.$selectlabel.$button.'<select id="'.$formid.'_jump" name="jump"'.$javascript.$disabled.'>'."\n";

    if ($nothing != '') {
        $selectlabeloption = '';
        if ($selected=='') {
            $selectlabeloption = ' selected="selected"';
        }
        foreach ($options as $value => $label) {  //if one of the options is the empty value, don't make this the default
            if ($value == '') {
                $selected = '';
            }
        }
        $output .= "   <option value=\"javascript:void(0)\"$selectlabeloption>$nothing</option>\n";
    }

    $inoptgroup = false;

    foreach ($options as $value => $label) {

        if ($label == '--') { /// we are ending previous optgroup
            /// Check to see if we already have a valid open optgroup
            /// XHTML demands that there be at least 1 option within an optgroup
            if ($inoptgroup and (count($optgr) > 1) ) {
                $output .= implode('', $optgr);
                $output .= '   </optgroup>';
            }
            $optgr = array();
            $inoptgroup = false;
            continue;
        } else if (substr($label,0,2) == '--') { /// we are starting a new optgroup

            /// Check to see if we already have a valid open optgroup
            /// XHTML demands that there be at least 1 option within an optgroup
            if ($inoptgroup and (count($optgr) > 1) ) {
                $output .= implode('', $optgr);
                $output .= '   </optgroup>';
            }

            unset($optgr);
            $optgr = array();

            $optgr[]  = '   <optgroup label="'. s(format_string(substr($label,2))) .'">';   // Plain labels

            $inoptgroup = true; /// everything following will be in an optgroup
            continue;

        } else {
           if (!empty($CFG->usesid) && !isset($_COOKIE[session_name()]))
            {
                $url = $SESSION->sid_process_url( $baseurl . $value );
            } else
            {
                $url=$baseurl . $value;
            }
            $optstr = '   <option value="' . $url . '"';

            if ($value == $selected) {
                $optstr .= ' selected="selected"';
            }

            if (!empty($optionsextra[$value])) {
                $optstr .= ' '.$optionsextra[$value];
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
    if (!$showbutton) {
        $output .= '<div id="noscript'.$formid.'" style="display: inline;">';
    }
    $output .= '<input type="submit" value="'.$submitvalue.'" '.$disabled.' />';
    if (!$showbutton) {
        $output .= $PAGE->requires->js_function_call('hide_item', Array('noscript'.$formid))->asap();
        $output .= '</div>';
    }
    $output .= '</div></form>';

    if ($return) {
        return $output;
    } else {
        echo $output;
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
 * Note: $scriptname parameter is not needed anymore
 *
 * @global string
 * @uses $_SERVER
 * @uses PARAM_PATH
 * @return string file path (only safe characters)
 */
function get_file_argument() {
    global $SCRIPT;

    $relativepath = optional_param('file', FALSE, PARAM_PATH);

    // then try extract file from PATH_INFO (slasharguments method)
    if ($relativepath === false and isset($_SERVER['PATH_INFO']) and $_SERVER['PATH_INFO'] !== '') {
        // check that PATH_INFO works == must not contain the script name
        if (strpos($_SERVER['PATH_INFO'], $SCRIPT) === false) {
            $relativepath = clean_param(urldecode($_SERVER['PATH_INFO']), PARAM_PATH);
        }
    }

    // note: we are not using any other way because they are not compatible with unicode file names ;-)

    return $relativepath;
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
 * This function should mainly be used for long strings like posts,
 * answers, glossary items etc. For short strings @see format_string().
 *
 * @todo Finish documenting this function
 *
 * @global object
 * @global object
 * @global object
 * @global object
 * @uses FORMAT_MOODLE
 * @uses FORMAT_HTML
 * @uses FORMAT_PLAIN
 * @uses FORMAT_WIKI
 * @uses FORMAT_MARKDOWN
 * @uses CLI_SCRIPT
 * @staticvar array $croncache
 * @param string $text The text to be formatted. This is raw text originally from user input.
 * @param int $format Identifier of the text format to be used
 *            [FORMAT_MOODLE, FORMAT_HTML, FORMAT_PLAIN, FORMAT_WIKI, FORMAT_MARKDOWN]
 * @param object $options ?
 * @param int $courseid The courseid to use, defaults to $COURSE->courseid
 * @return string
 */
function format_text($text, $format=FORMAT_MOODLE, $options=NULL, $courseid=NULL) {
    global $CFG, $COURSE, $DB, $PAGE;

    static $croncache = array();

    $hashstr = '';

    if ($text === '') {
        return ''; // no need to do any filters and cleaning
    }

    if (!isset($options->trusted)) {
        $options->trusted = false;
    }
    if (!isset($options->noclean)) {
        if ($options->trusted and trusttext_active()) {
            // no cleaning if text trusted and noclean not specified
            $options->noclean=true;
        } else {
            $options->noclean=false;
        }
    }
    if (!isset($options->nocache)) {
        $options->nocache=false;
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
        $courseid = $COURSE->id;
    }

    if ($options->filter) {
        $filtermanager = filter_manager::instance();
    } else {
        $filtermanager = new null_filter_manager();
    }
    $context = $PAGE->context;

    if (!empty($CFG->cachetext) and empty($options->nocache)) {
        $hashstr .= $text.'-'.$filtermanager->text_filtering_hash($context, $courseid).'-'.(int)$courseid.'-'.current_language().'-'.
                (int)$format.(int)$options->trusted.(int)$options->noclean.(int)$options->smiley.
                (int)$options->filter.(int)$options->para.(int)$options->newlines;

        $time = time() - $CFG->cachetext;
        $md5key = md5($hashstr);
        if (CLI_SCRIPT) {
            if (isset($croncache[$md5key])) {
                return $croncache[$md5key];
            }
        }

        if ($oldcacheitem = $DB->get_record('cache_text', array('md5key'=>$md5key), '*', IGNORE_MULTIPLE)) {
            if ($oldcacheitem->timemodified >= $time) {
                if (CLI_SCRIPT) {
                    if (count($croncache) > 150) {
                        reset($croncache);
                        $key = key($croncache);
                        unset($croncache[$key]);
                    }
                    $croncache[$md5key] = $oldcacheitem->formattedtext;
                }
                return $oldcacheitem->formattedtext;
            }
        }
    }

    switch ($format) {
        case FORMAT_HTML:
            if ($options->smiley) {
                replace_smilies($text);
            }
            if (!$options->noclean) {
                $text = clean_text($text, FORMAT_HTML);
            }
            $text = $filtermanager->filter_text($text, $context, $courseid);
            break;

        case FORMAT_PLAIN:
            $text = s($text); // cleans dangerous JS
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
            if ($options->smiley) {
                replace_smilies($text);
            }
            if (!$options->noclean) {
                $text = clean_text($text, FORMAT_HTML);
            }
            $text = $filtermanager->filter_text($text, $context, $courseid);
            break;

        default:  // FORMAT_MOODLE or anything else
            $text = text_to_html($text, $options->smiley, $options->para, $options->newlines);
            if (!$options->noclean) {
                $text = clean_text($text, FORMAT_HTML);
            }
            $text = $filtermanager->filter_text($text, $context, $courseid);
            break;
    }

    // Warn people that we have removed this old mechanism, just in case they
    // were stupid enough to rely on it.
    if (isset($CFG->currenttextiscacheable)) {
        debugging('Once upon a time, Moodle had a truly evil use of global variables ' .
                'called $CFG->currenttextiscacheable. The good news is that this no ' .
                'longer exists. The bad news is that you seem to be using a filter that '.
                'relies on it. Please seek out and destroy that filter code.', DEBUG_DEVELOPER);
    }

    if (empty($options->nocache) and !empty($CFG->cachetext)) {
        if (CLI_SCRIPT) {
            // special static cron cache - no need to store it in db if its not already there
            if (count($croncache) > 150) {
                reset($croncache);
                $key = key($croncache);
                unset($croncache[$key]);
            }
            $croncache[$md5key] = $text;
            return $text;
        }

        $newcacheitem = new object();
        $newcacheitem->md5key = $md5key;
        $newcacheitem->formattedtext = $text;
        $newcacheitem->timemodified = time();
        if ($oldcacheitem) {                               // See bug 4677 for discussion
            $newcacheitem->id = $oldcacheitem->id;
            try {
                $DB->update_record('cache_text', $newcacheitem);   // Update existing record in the cache table
            } catch (dml_exception $e) {
               // It's unlikely that the cron cache cleaner could have
               // deleted this entry in the meantime, as it allows
               // some extra time to cover these cases.
            }
        } else {
            try {
                $DB->insert_record('cache_text', $newcacheitem);   // Insert a new record in the cache table
            } catch (dml_exception $e) {
               // Again, it's possible that another user has caused this
               // record to be created already in the time that it took
               // to traverse this function.  That's OK too, as the
               // call above handles duplicate entries, and eventually
               // the cron cleaner will delete them.
            }
        }
    }

    return $text;
}

/**
 * Converts the text format from the value to the 'internal'
 * name or vice versa. 
 *
 * $key can either be the value or the name and you get the other back.
 *
 * @uses FORMAT_MOODLE
 * @uses FORMAT_HTML
 * @uses FORMAT_PLAIN
 * @uses FORMAT_MARKDOWN
 * @param mixed $key int 0-4 or string one of 'moodle','html','plain','markdown'
 * @return mixed as above but the other way around!
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

/**
 * Resets all data related to filters, called during upgrade or when filter settings change.
 *
 * @global object
 * @global object
 * @return void
 */
function reset_text_filters_cache() {
    global $CFG, $DB;

    $DB->delete_records('cache_text');
    $purifdir = $CFG->dataroot.'/cache/htmlpurifier';
    remove_dir($purifdir, true);
}

/** 
 * Given a simple string, this function returns the string
 * processed by enabled string filters if $CFG->filterall is enabled
 *
 * This function should be used to print short strings (non html) that
 * need filter processing e.g. activity titles, post subjects,
 * glossary concepts.
 *
 * @global object
 * @global object
 * @global object
 * @staticvar bool $strcache
 * @param string $string The string to be filtered.
 * @param boolean $striplinks To strip any link in the result text.
                              Moodle 1.8 default changed from false to true! MDL-8713
 * @param int $courseid Current course as filters can, potentially, use it
 * @return string
 */
function format_string($string, $striplinks=true, $courseid=NULL ) {
    global $CFG, $COURSE, $PAGE;

    //We'll use a in-memory cache here to speed up repeated strings
    static $strcache = false;

    if ($strcache === false or count($strcache) > 2000 ) { // this number might need some tuning to limit memory usage in cron
        $strcache = array();
    }

    //init course id
    if (empty($courseid)) {
        $courseid = $COURSE->id;
    }

    //Calculate md5
    $md5 = md5($string.'<+>'.$striplinks.'<+>'.$courseid.'<+>'.current_language());

    //Fetch from cache if possible
    if (isset($strcache[$md5])) {
        return $strcache[$md5];
    }

    // First replace all ampersands not followed by html entity code
    // Regular expression moved to its own method for easier unit testing
    $string = replace_ampersands_not_followed_by_entity($string);

    if (!empty($CFG->filterall) && $CFG->version >= 2009040600) { // Avoid errors during the upgrade to the new system.
        $context = $PAGE->context;
        $string = filter_manager::instance()->filter_string($string, $context, $courseid);
    }

    // If the site requires it, strip ALL tags from this string
    if (!empty($CFG->formatstringstriptags)) {
        $string = strip_tags($string);

    } else {
        // Otherwise strip just links if that is required (default)
        if ($striplinks) {  //strip links in string
            $string = strip_links($string);
        }
        $string = clean_text($string);
    }

    //Store to cache
    $strcache[$md5] = $string;

    return $string;
}

/**
 * Given a string, performs a negative lookahead looking for any ampersand character
 * that is not followed by a proper HTML entity. If any is found, it is replaced
 * by &amp;. The string is then returned.
 *
 * @param string $string
 * @return string
 */
function replace_ampersands_not_followed_by_entity($string) {
    return preg_replace("/\&(?![a-zA-Z0-9#]{1,8};)/", "&amp;", $string);
}

/**
 * Given a string, replaces all <a>.*</a> by .* and returns the string.
 * 
 * @param string $string
 * @return string
 */
function strip_links($string) {
    return preg_replace('/(<a\s[^>]+?>)(.+?)(<\/a>)/is','$2',$string);
}

/**
 * This expression turns links into something nice in a text format. (Russell Jungwirth)
 *
 * @param string $string
 * @return string
 */
function wikify_links($string) {
    return preg_replace('~(<a [^<]*href=["|\']?([^ "\']*)["|\']?[^>]*>([^<]*)</a>)~i','$3 [ $2 ]', $string);
}

/**
 * Replaces non-standard HTML entities
 * 
 * @param string $string
 * @return string
 */
function fix_non_standard_entities($string) {
    $text = preg_replace('/(&#[0-9]+)(;?)/', '$1;', $string);
    $text = preg_replace('/(&#x[0-9a-fA-F]+)(;?)/', '$1;', $text); 
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
 *            [FORMAT_MOODLE, FORMAT_HTML, FORMAT_PLAIN, FORMAT_WIKI, FORMAT_MARKDOWN]
 * @return string
 */
function format_text_email($text, $format) {

    switch ($format) {

        case FORMAT_PLAIN:
            return $text;
            break;

        case FORMAT_WIKI:
            $text = wiki_to_html($text);
            $text = wikify_links($text);
            return strtr(strip_tags($text), array_flip(get_html_translation_table(HTML_ENTITIES)));
            break;

        case FORMAT_HTML:
            return html_to_text($text);
            break;

        case FORMAT_MOODLE:
        case FORMAT_MARKDOWN:
        default:
            $text = wikify_links($text);
            return strtr(strip_tags($text), array_flip(get_html_translation_table(HTML_ENTITIES)));
            break;
    }
}

/**
 * Given some text in HTML format, this function will pass it
 * through any filters that have been configured for this context.
 *
 * @global object
 * @global object
 * @global object
 * @param string $text The text to be passed through format filters
 * @param int $courseid The current course.
 * @return string the filtered string.
 */
function filter_text($text, $courseid=NULL) {
    global $CFG, $COURSE, $PAGE;

    if (empty($courseid)) {
        $courseid = $COURSE->id;       // (copied from format_text)
    }

    $context = $PAGE->context;

    return filter_manager::instance()->filter_text($text, $context, $courseid);
}
/**
 * Formats activity intro text
 *
 * @global object
 * @uses CONTEXT_MODULE
 * @param string $module name of module
 * @param object $activity instance of activity
 * @param int $cmid course module id
 * @param bool $filter filter resulting html text
 * @return text
 */
function format_module_intro($module, $activity, $cmid, $filter=true) {
    global $CFG;
    require_once("$CFG->libdir/filelib.php");
    $options = (object)array('noclean'=>true, 'para'=>false, 'filter'=>false);
    $context = get_context_instance(CONTEXT_MODULE, $cmid);
    $intro = file_rewrite_pluginfile_urls($activity->intro, 'pluginfile.php', $context->id, $module.'_intro', null);
    return trim(format_text($intro, $activity->introformat, $options));
}

/**
 * Legacy function, used for cleaning of old forum and glossary text only.
 *
 * @global object
 * @param string $text text that may contain TRUSTTEXT marker
 * @return text without any TRUSTTEXT marker
 */
function trusttext_strip($text) {
    global $CFG;

    while (true) { //removing nested TRUSTTEXT
        $orig = $text;
        $text = str_replace('#####TRUSTTEXT#####', '', $text);
        if (strcmp($orig, $text) === 0) {
            return $text;
        }
    }
}

/**
 * Must be called before editing of all texts
 * with trust flag. Removes all XSS nasties
 * from texts stored in database if needed.
 *
 * @param object $object data object with xxx, xxxformat and xxxtrust fields
 * @param string $field name of text field
 * @param object $context active context
 * @return object updated $object
 */
function trusttext_pre_edit($object, $field, $context) {
    $trustfield  = $field.'trust';
    $formatfield = $field.'format'; 
    
    if (!$object->$trustfield or !trusttext_trusted($context)) {
        $object->$field = clean_text($object->$field, $object->$formatfield);
    }

    return $object;
}

/**
 * Is current user trusted to enter no dangerous XSS in this context?
 *
 * Please note the user must be in fact trusted everywhere on this server!!
 *
 * @param object $context
 * @return bool true if user trusted
 */
function trusttext_trusted($context) {
    return (trusttext_active() and has_capability('moodle/site:trustcontent', $context)); 
}

/**
 * Is trusttext feature active?
 *
 * @global object
 * @param object $context
 * @return bool
 */
function trusttext_active() {
    global $CFG;

    return !empty($CFG->enabletrusttext); 
}

/**
 * Given raw text (eg typed in by a user), this function cleans it up
 * and removes any nasty tags that could mess up Moodle pages.
 *
 * @uses FORMAT_MOODLE
 * @uses FORMAT_PLAIN
 * @global string
 * @global object
 * @param string $text The text to be cleaned
 * @param int $format Identifier of the text format to be used
 *            [FORMAT_MOODLE, FORMAT_HTML, FORMAT_PLAIN, FORMAT_WIKI, FORMAT_MARKDOWN]
 * @return string The cleaned up text
 */
function clean_text($text, $format=FORMAT_MOODLE) {

    global $ALLOWED_TAGS, $CFG;

    if (empty($text) or is_numeric($text)) {
       return (string)$text;
    }

    switch ($format) {
        case FORMAT_PLAIN:
        case FORMAT_MARKDOWN:
            return $text;

        default:

            if (!empty($CFG->enablehtmlpurifier)) {
                $text = purify_html($text);
            } else {
            /// Fix non standard entity notations
                $text = fix_non_standard_entities($text);

            /// Remove tags that are not allowed
                $text = strip_tags($text, $ALLOWED_TAGS);

            /// Clean up embedded scripts and , using kses
                $text = cleanAttributes($text);

            /// Again remove tags that are not allowed
                $text = strip_tags($text, $ALLOWED_TAGS);

            }

        /// Remove potential script events - some extra protection for undiscovered bugs in our code
            $text = preg_replace("~([^a-z])language([[:space:]]*)=~i", "$1Xlanguage=", $text);
            $text = preg_replace("~([^a-z])on([a-z]+)([[:space:]]*)=~i", "$1Xon$2=", $text);

            return $text;
    }
}

/**
 * KSES replacement cleaning function - uses HTML Purifier.
 *
 * @global object
 * @param string $text The (X)HTML string to purify
 */
function purify_html($text) {
    global $CFG;

    // this can not be done only once because we sometimes need to reset the cache
    $cachedir = $CFG->dataroot.'/cache/htmlpurifier';
    $status = check_dir_exists($cachedir, true, true);

    static $purifier = false;
    static $config;
    if ($purifier === false) {
        require_once $CFG->libdir.'/htmlpurifier/HTMLPurifier.safe-includes.php';
        $config = HTMLPurifier_Config::createDefault();
        $config->set('Core', 'ConvertDocumentToFragment', true);
        $config->set('Core', 'Encoding', 'UTF-8');
        $config->set('HTML', 'Doctype', 'XHTML 1.0 Transitional');
        $config->set('Cache', 'SerializerPath', $cachedir);
        $config->set('URI', 'AllowedSchemes', array('http'=>1, 'https'=>1, 'ftp'=>1, 'irc'=>1, 'nntp'=>1, 'news'=>1, 'rtsp'=>1, 'teamspeak'=>1, 'gopher'=>1, 'mms'=>1));
        $config->set('Attr', 'AllowedFrameTargets', array('_blank'));
        $purifier = new HTMLPurifier($config);
    }
    return $purifier->purify($text);
}

/**
 * This function takes a string and examines it for HTML tags.
 *
 * If tags are detected it passes the string to a helper function {@link cleanAttributes2()}
 * which checks for attributes and filters them for malicious content
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
 *
 * It calls ancillary functions in kses which are prefixed by kses
 *
 * @global object
 * @global string
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
            $arreach['value'] = preg_replace("/b\s*i\s*n\s*d\s*i\s*n\s*g/i", "Xbinding", $arreach['value']);
        } else if ($arreach['name'] == 'href') {
            //Adobe Acrobat Reader XSS protection
            $arreach['value'] = preg_replace('/(\.(pdf|fdf|xfdf|xdp|xfd)[^#]*)#.*$/i', '$1', $arreach['value']);
        }
        $attStr .=  ' '.$arreach['name'].'="'.$arreach['value'].'"';
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
 * @global object
 * @staticvar array $e
 * @staticvar array $img
 * @staticvar array $emoticons
 * @param string $text Passed by reference. The string to search for smily strings.
 * @return string
 */
function replace_smilies(&$text) {
    global $CFG, $OUTPUT;

    if (empty($CFG->emoticons)) { /// No emoticons defined, nothing to process here
        return;
    }

    $lang = current_language();
    $emoticonstring = $CFG->emoticons;
    static $e = array();
    static $img = array();
    static $emoticons = null;

    if (is_null($emoticons)) {
        $emoticons = array();
        if ($emoticonstring) {
            $items = explode('{;}', $CFG->emoticons);
            foreach ($items as $item) {
               $item = explode('{:}', $item);
              $emoticons[$item[0]] = $item[1];
            }
        }
    }

    if (empty($img[$lang])) {  /// After the first time this is not run again
        $e[$lang] = array();
        $img[$lang] = array();
        foreach ($emoticons as $emoticon => $image){
            $alttext = get_string($image, 'pix');
            $alttext = preg_replace('/^\[\[(.*)\]\]$/', '$1', $alttext); /// Clean alttext in case there isn't lang string for it.
            $e[$lang][] = $emoticon;
            $img[$lang][] = '<img alt="'. $alttext .'" width="15" height="15" src="'. $OUTPUT->old_icon_url('s/' . $image) . '" />';
        }
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
    $text = str_replace($e[$lang], $img[$lang], $text);

    // Recover all the <script> zones to text
    if ($excludes) {
        $text = str_replace(array_keys($excludes),$excludes,$text);
    }
}

/**
 * This code is called from help.php to inject a list of smilies into the
 * emoticons help file.
 *
 * @global object
 * @global object
 * @return string HTML for a list of smilies.
 */
function get_emoticons_list_for_help_file() {
    global $CFG, $SESSION, $PAGE, $OUTPUT;
    if (empty($CFG->emoticons)) {
        return '';
    }

    $items = explode('{;}', $CFG->emoticons);
    $output = '<ul id="emoticonlist">';
    foreach ($items as $item) {
        $item = explode('{:}', $item);
        $output .= '<li><img src="' . $OUTPUT->old_icon_url('s/' . $item[1]) . '" alt="' .
                $item[0] . '" /><code>' . $item[0] . '</code></li>';
    }
    $output .= '</ul>';
    if (!empty($SESSION->inserttextform)) {
        $formname = $SESSION->inserttextform;
        $fieldname = $SESSION->inserttextfield;
    } else {
        $formname = 'theform';
        $fieldname = 'message';
    }

    $PAGE->requires->yui_lib('event');
    $PAGE->requires->js_function_call('emoticons_help.init', array($formname, $fieldname, 'emoticonlist'));
    return $output;

}

/**
 * Given plain text, makes it into HTML as nicely as possible.
 * May contain HTML tags already
 *
 * @global object
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
    $text = preg_replace("~>([[:space:]]+)<~i", "><", $text);

/// Remove any returns that precede or follow HTML tags
    $text = preg_replace("~([\n\r])<~i", " <", $text);
    $text = preg_replace("~>([\n\r])~i", "> ", $text);

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
 * @global object
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
 * @global object
 * @param string $html The text to be converted.
 * @return string
 */
function html_to_text($html) {

    global $CFG;

    require_once($CFG->libdir .'/html2text.php');

    $h2t = new html2text($html);
    $result = $h2t->get_text();

    return $result;
}

/**
 * Given some text this function converts any URLs it finds into HTML links
 *
 * @param string $text Passed in by reference. The string to be searched for urls.
 */
function convert_urls_into_links(&$text) {
/// Make lone URLs into links.   eg http://moodle.com/
    $text = preg_replace("~([[:space:]]|^|\(|\[)([[:alnum:]]+)://([^[:space:]]*)([[:alnum:]#?/&=])~i",
                          '$1<a href="$2://$3$4">$2://$3$4</a>', $text);

/// eg www.moodle.com
    $text = preg_replace("~([[:space:]]|^|\(|\[)www\.([^[:space:]]*)([[:alnum:]#?/&=])~i",
                          '$1<a href="http://www.$2$3">www.$2$3</a>', $text);
}

/**
 * This function will highlight search words in a given string
 *
 * It cares about HTML and will not ruin links.  It's best to use
 * this function after performing any conversions to HTML.
 *
 * @param string $needle The search string. Syntax like "word1 +word2 -word3" is dealt with correctly.
 * @param string $haystack The string (HTML) within which to highlight the search terms.
 * @param boolean $matchcase whether to do case-sensitive. Default case-insensitive.
 * @param string $prefix the string to put before each search term found.
 * @param string $suffix the string to put after each search term found.
 * @return string The highlighted HTML.
 */
function highlight($needle, $haystack, $matchcase = false,
        $prefix = '<span class="highlight">', $suffix = '</span>') {

/// Quick bail-out in trivial cases.
    if (empty($needle) or empty($haystack)) {
        return $haystack;
    }

/// Break up the search term into words, discard any -words and build a regexp.
    $words = preg_split('/ +/', trim($needle));
    foreach ($words as $index => $word) {
        if (strpos($word, '-') === 0) {
            unset($words[$index]);
        } else if (strpos($word, '+') === 0) {
            $words[$index] = '\b' . preg_quote(ltrim($word, '+'), '/') . '\b'; // Match only as a complete word.
        } else {
            $words[$index] = preg_quote($word, '/');
        }
    }
    $regexp = '/(' . implode('|', $words) . ')/u'; // u is do UTF-8 matching.
    if (!$matchcase) {
        $regexp .= 'i';
    }

/// Another chance to bail-out if $search was only -words
    if (empty($words)) {
        return $haystack;
    }

/// Find all the HTML tags in the input, and store them in a placeholders array.
    $placeholders = array();
    $matches = array();
    preg_match_all('/<[^>]*>/', $haystack, $matches);
    foreach (array_unique($matches[0]) as $key => $htmltag) {
        $placeholders['<|' . $key . '|>'] = $htmltag;
    }

/// In $hastack, replace each HTML tag with the corresponding placeholder.
    $haystack = str_replace($placeholders, array_keys($placeholders), $haystack);

/// In the resulting string, Do the highlighting.
    $haystack = preg_replace($regexp, $prefix . '$1' . $suffix, $haystack);

/// Turn the placeholders back into HTML tags.
    $haystack = str_replace(array_keys($placeholders), $placeholders, $haystack);

    return $haystack;
}

/**
 * This function will highlight instances of $needle in $haystack
 *
 * It's faster that the above function {@link highlight()} and doesn't care about
 * HTML or anything.
 *
 * @param string $needle The string to search for
 * @param string $haystack The string to search for $needle in
 * @return string The highlighted HTML
 */
function highlightfast($needle, $haystack) {

    if (empty($needle) or empty($haystack)) {
        return $haystack;
    }

    $parts = explode(moodle_strtolower($needle), moodle_strtolower($haystack));

    if (count($parts) === 1) {
        return $haystack;
    }

    $pos = 0;

    foreach ($parts as $key => $part) {
        $parts[$key] = substr($haystack, $pos, strlen($part));
        $pos += strlen($part);

        $parts[$key] .= '<span class="highlight">'.substr($haystack, $pos, strlen($needle)).'</span>';
        $pos += strlen($needle);
    }

    return str_replace('<span class="highlight"></span>', '', join('', $parts));
}

/**
 * Return a string containing 'lang', xml:lang and optionally 'dir' HTML attributes.
 * Internationalisation, for print_header and backup/restorelib.
 *
 * @param bool $dir Default false
 * @return string Attributes
 */
function get_html_lang($dir = false) {
    $direction = '';
    if ($dir) {
        if (get_string('thisdirection') == 'rtl') {
            $direction = ' dir="rtl"';
        } else {
            $direction = ' dir="ltr"';
        }
    }
    //Accessibility: added the 'lang' attribute to $direction, used in theme <html> tag.
    $language = str_replace('_', '-', str_replace('_utf8', '', current_language()));
    @header('Content-Language: '.$language);
    return ($direction.' lang="'.$language.'" xml:lang="'.$language.'"');
}


/// STANDARD WEB PAGE PARTS ///////////////////////////////////////////////////

/**
 * Send the HTTP headers that Moodle requires.
 * @param $cacheable Can this page be cached on back?
 */
function send_headers($contenttype, $cacheable = true) {
    @header('Content-Type: ' . $contenttype);
    @header('Content-Script-Type: text/javascript');
    @header('Content-Style-Type: text/css');

    if ($cacheable) {
        // Allow caching on "back" (but not on normal clicks)
        @header('Cache-Control: private, pre-check=0, post-check=0, max-age=0');
        @header('Pragma: no-cache');
        @header('Expires: ');
    } else {
        // Do everything we can to always prevent clients and proxies caching
        @header('Cache-Control: no-store, no-cache, must-revalidate');
        @header('Cache-Control: post-check=0, pre-check=0', false);
        @header('Pragma: no-cache');
        @header('Expires: Mon, 20 Aug 1969 09:23:00 GMT');
        @header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
    }
    @header('Accept-Ranges: none');
}

/**
 * This version of print_header is simpler because the course name does not have to be
 * provided explicitly in the strings. It can be used on the site page as in courses
 * Eventually all print_header could be replaced by print_header_simple
 *
 * @global object
 * @global object
 * @uses SITEID
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
 * @param bool   $return If true, return the visible elements of the header instead of echoing them.
 * @return string|void If $return=true the return string else nothing
 */
function print_header_simple($title='', $heading='', $navigation='', $focus='', $meta='',
                       $cache=true, $button='&nbsp;', $menu='', $usexml=false, $bodytags='', $return=false) {

    global $COURSE, $CFG;

    // if we have no navigation specified, build it
    if( empty($navigation) ){
       $navigation = build_navigation('');
    }

    // If old style nav prepend course short name otherwise leave $navigation object alone
    if (!is_newnav($navigation)) {
        if ($COURSE->id != SITEID) {
            $shortname = '<a href="'.$CFG->wwwroot.'/course/view.php?id='. $COURSE->id .'">'. $COURSE->shortname .'</a> ->';
            $navigation = $shortname.' '.$navigation;
        }
    }

    $output = print_header($COURSE->shortname .': '. $title, $COURSE->fullname .' '. $heading, $navigation, $focus, $meta,
                           $cache, $button, $menu, $usexml, $bodytags, true);

    if ($return) {
        return $output;
    } else {
        echo $output;
    }
}

/**
 * Returns text to be displayed to the user which reflects their login status
 *
 * @global object
 * @global object
 * @global object
 * @global object
 * @uses CONTEXT_COURSE
 * @param course $course {@link $COURSE} object containing course information
 * @param user $user {@link $USER} object containing user information
 * @return string HTML
 */
function user_login_string($course=NULL, $user=NULL) {
    global $USER, $CFG, $SITE, $DB;

    if (during_initial_install()) {
        return '';
    }

    if (empty($user) and !empty($USER->id)) {
        $user = $USER;
    }

    if (empty($course)) {
        $course = $SITE;
    }

    if (session_is_loggedinas()) {
        $realuser = session_get_realuser();
        $fullname = fullname($realuser, true);
        $realuserinfo = " [<a $CFG->frametarget
        href=\"$CFG->wwwroot/course/loginas.php?id=$course->id&amp;return=1&amp;sesskey=".sesskey()."\">$fullname</a>] ";
    } else {
        $realuserinfo = '';
    }

    $loginurl = get_login_url();

    if (empty($course->id)) {
        // $course->id is not defined during installation
        return '';
    } else if (!empty($user->id)) {
        $context = get_context_instance(CONTEXT_COURSE, $course->id);

        $fullname = fullname($user, true);
        $username = "<a $CFG->frametarget href=\"$CFG->wwwroot/user/view.php?id=$user->id&amp;course=$course->id\">$fullname</a>";
        if (is_mnet_remote_user($user) and $idprovider = $DB->get_record('mnet_host', array('id'=>$user->mnethostid))) {
            $username .= " from <a $CFG->frametarget href=\"{$idprovider->wwwroot}\">{$idprovider->name}</a>";
        }
        if (isset($user->username) && $user->username == 'guest') {
            $loggedinas = $realuserinfo.get_string('loggedinasguest').
                      " (<a $CFG->frametarget href=\"$loginurl\">".get_string('login').'</a>)';
        } else if (!empty($user->access['rsw'][$context->path])) {
            $rolename = '';
            if ($role = $DB->get_record('role', array('id'=>$user->access['rsw'][$context->path]))) {
                $rolename = ': '.format_string($role->name);
            }
            $loggedinas = get_string('loggedinas', 'moodle', $username).$rolename.
                      " (<a $CFG->frametarget
                      href=\"$CFG->wwwroot/course/view.php?id=$course->id&amp;switchrole=0&amp;sesskey=".sesskey()."\">".get_string('switchrolereturn').'</a>)';
        } else {
            $loggedinas = $realuserinfo.get_string('loggedinas', 'moodle', $username).' '.
                      " (<a $CFG->frametarget href=\"$CFG->wwwroot/login/logout.php?sesskey=".sesskey()."\">".get_string('logout').'</a>)';
        }
    } else {
        $loggedinas = get_string('loggedinnot', 'moodle').
                      " (<a $CFG->frametarget href=\"$loginurl\">".get_string('login').'</a>)';
    }

    $loggedinas = '<div class="logininfo">'.$loggedinas.'</div>';

    if (isset($SESSION->justloggedin)) {
        unset($SESSION->justloggedin);
        if (!empty($CFG->displayloginfailures)) {
            if (!empty($USER->username) and $USER->username != 'guest') {
                if ($count = count_login_failures($CFG->displayloginfailures, $USER->username, $USER->lastlogin)) {
                    $loggedinas .= '&nbsp;<div class="loginfailures">';
                    if (empty($count->accounts)) {
                        $loggedinas .= get_string('failedloginattempts', '', $count);
                    } else {
                        $loggedinas .= get_string('failedloginattemptsall', '', $count);
                    }
                    if (has_capability('coursereport/log:view', get_context_instance(CONTEXT_SYSTEM))) {
                        $loggedinas .= ' (<a href="'.$CFG->wwwroot.'/course/report/log/index.php'.
                                             '?chooselog=1&amp;id=1&amp;modid=site_errors">'.get_string('logs').'</a>)';
                    }
                    $loggedinas .= '</div>';
                }
            }
        }
    }

    return $loggedinas;
}

/**
 * Tests whether $THEME->rarrow, $THEME->larrow have been set (theme/-/config.php).
 * If not it applies sensible defaults.
 *
 * Accessibility: right and left arrow Unicode characters for breadcrumb, calendar,
 * search forum block, etc. Important: these are 'silent' in a screen-reader
 * (unlike &gt; &raquo;), and must be accompanied by text.
 *
 * @global object
 * @uses $_SERVER
 */
function check_theme_arrows() {
    global $THEME;

    if (!isset($THEME->rarrow) and !isset($THEME->larrow)) {
        // Default, looks good in Win XP/IE 6, Win/Firefox 1.5, Win/Netscape 8...
        // Also OK in Win 9x/2K/IE 5.x
        $THEME->rarrow = '&#x25BA;';
        $THEME->larrow = '&#x25C4;';
        if (empty($_SERVER['HTTP_USER_AGENT'])) {
            $uagent = '';
        } else {
            $uagent = $_SERVER['HTTP_USER_AGENT'];
        }
        if (false !== strpos($uagent, 'Opera')
            || false !== strpos($uagent, 'Mac')) {
            // Looks good in Win XP/Mac/Opera 8/9, Mac/Firefox 2, Camino, Safari.
            // Not broken in Mac/IE 5, Mac/Netscape 7 (?).
            $THEME->rarrow = '&#x25B6;';
            $THEME->larrow = '&#x25C0;';
        }
        elseif (false !== strpos($uagent, 'Konqueror')) {
            $THEME->rarrow = '&rarr;';
            $THEME->larrow = '&larr;';
        }
        elseif (isset($_SERVER['HTTP_ACCEPT_CHARSET'])
            && false === stripos($_SERVER['HTTP_ACCEPT_CHARSET'], 'utf-8')) {
            // (Win/IE 5 doesn't set ACCEPT_CHARSET, but handles Unicode.)
            // To be safe, non-Unicode browsers!
            $THEME->rarrow = '&gt;';
            $THEME->larrow = '&lt;';
        }

    /// RTL support - in RTL languages, swap r and l arrows
        if (right_to_left()) {
            $t = $THEME->rarrow;
            $THEME->rarrow = $THEME->larrow;
            $THEME->larrow = $t;
        }
    }
}


/**
 * Return the right arrow with text ('next'), and optionally embedded in a link.
 * See function above, check_theme_arrows.
 *
 * @global object
 * @param string $text HTML/plain text label (set to blank only for breadcrumb separator cases).
 * @param string $url An optional link to use in a surrounding HTML anchor.
 * @param bool $accesshide True if text should be hidden (for screen readers only).
 * @param string $addclass Additional class names for the link, or the arrow character.
 * @return string HTML string.
 */
function link_arrow_right($text, $url='', $accesshide=false, $addclass='') {
    global $THEME;
    check_theme_arrows();
    $arrowclass = 'arrow ';
    if (! $url) {
        $arrowclass .= $addclass;
    }
    $arrow = '<span class="'.$arrowclass.'">'.$THEME->rarrow.'</span>';
    $htmltext = '';
    if ($text) {
        $htmltext = '<span class="arrow_text">'.$text.'</span>&nbsp;';
        if ($accesshide) {
            $htmltext = get_accesshide($htmltext);
        }
    }
    if ($url) {
        $class = 'arrow_link';
        if ($addclass) {
            $class .= ' '.$addclass;
        }
        return '<a class="'.$class.'" href="'.$url.'" title="'.preg_replace('/<.*?>/','',$text).'">'.$htmltext.$arrow.'</a>';
    }
    return $htmltext.$arrow;
}

/**
 * Return the left arrow with text ('previous'), and optionally embedded in a link.
 * See function above, check_theme_arrows.
 *
 * @global object
 * @param string $text HTML/plain text label (set to blank only for breadcrumb separator cases).
 * @param string $url An optional link to use in a surrounding HTML anchor.
 * @param bool $accesshide True if text should be hidden (for screen readers only).
 * @param string $addclass Additional class names for the link, or the arrow character.
 * @return string HTML string.
 */
function link_arrow_left($text, $url='', $accesshide=false, $addclass='') {
    global $THEME;
    check_theme_arrows();
    $arrowclass = 'arrow ';
    if (! $url) {
        $arrowclass .= $addclass;
    }
    $arrow = '<span class="'.$arrowclass.'">'.$THEME->larrow.'</span>';
    $htmltext = '';
    if ($text) {
        $htmltext = '&nbsp;<span class="arrow_text">'.$text.'</span>';
        if ($accesshide) {
            $htmltext = get_accesshide($htmltext);
        }
    }
    if ($url) {
        $class = 'arrow_link';
        if ($addclass) {
            $class .= ' '.$addclass;
        }
        return '<a class="'.$class.'" href="'.$url.'" title="'.preg_replace('/<.*?>/','',$text).'">'.$arrow.$htmltext.'</a>';
    }
    return $arrow.$htmltext;
}

/**
 * Return a HTML element with the class "accesshide", for accessibility.
 * Please use cautiously - where possible, text should be visible!
 *
 * @param string $text Plain text.
 * @param string $elem Lowercase element name, default "span".
 * @param string $class Additional classes for the element.
 * @param string $attrs Additional attributes string in the form, "name='value' name2='value2'"
 * @return string HTML string.
 */
function get_accesshide($text, $elem='span', $class='', $attrs='') {
    return "<$elem class=\"accesshide $class\" $attrs>$text</$elem>";
}

/**
 * Return the breadcrumb trail navigation separator.
 *
 * @return string HTML string.
 */
function get_separator() {
    //Accessibility: the 'hidden' slash is preferred for screen readers.
    return ' '.link_arrow_right($text='/', $url='', $accesshide=true, 'sep').' ';
}

/**
 * Prints breadcrumb trail of links, called in theme/-/header.html
 *
 * @global object
 * @global object
 * @global object
 * @uses CONTEXT_SYSTEM
 * @param mixed $navigation The breadcrumb navigation string to be printed
 * @param string $separator OBSOLETE, mostly not used any more. See build_navigation instead.
 * @param boolean $return False to echo the breadcrumb string (default), true to return it.
 * @return string|void String or null, depending on $return.
 */
function print_navigation ($navigation, $separator=0, $return=false) {
    global $CFG, $THEME, $SITE;
    $output = '';

    if (0 === $separator) {
        $separator = get_separator();
    }
    else {
        $separator = '<span class="sep">'. $separator .'</span>';
    }

    if ($navigation) {

        if (is_newnav($navigation)) {
            if ($return) {
                return($navigation['navlinks']);
            } else {
                echo $navigation['navlinks'];
                return;
            }
        } else {
            debugging('Navigation needs to be updated to use build_navigation()', DEBUG_DEVELOPER);
        }

        if (!is_array($navigation)) {
            $ar = explode('->', $navigation);
            $navigation = array();

            foreach ($ar as $a) {
                if (strpos($a, '</a>') === false) {
                    $navigation[] = array('title' => $a, 'url' => '');
                } else {
                    if (preg_match('/<a.*href="([^"]*)">(.*)<\/a>/', $a, $matches)) {
                        $navigation[] = array('title' => $matches[2], 'url' => $matches[1]);
                    }
                }
            }
        }

        if (!$SITE) {
            $site = new object();
            $site->shortname = get_string('home');
        } else {
            $site = $SITE;
        }

        //Accessibility: breadcrumb links now in a list, &raquo; replaced with a 'silent' character.
        $output .= get_accesshide(get_string('youarehere','access'), 'h2')."<ul>\n";

        $output .= '<li class="first">'."\n".'<a '.$CFG->frametarget.' onclick="this.target=\''.$CFG->framename.'\'" href="'
               .$CFG->wwwroot.((!has_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM))
                                 && !empty($USER->id) && !empty($CFG->mymoodleredirect) && !isguest())
                                 ? '/my' : '') .'/">'. format_string($site->shortname) ."</a>\n</li>\n";


        foreach ($navigation as $navitem) {
            $title = trim(strip_tags(format_string($navitem['title'], false)));
            $url   = $navitem['url'];

            if (empty($url)) {
                $output .= '<li>'."$separator $title</li>\n";
            } else {
                $output .= '<li>'."$separator\n<a ".$CFG->frametarget.' onclick="this.target=\''.$CFG->framename.'\'" href="'
                           .$url.'">'."$title</a>\n</li>\n";
            }
        }

        $output .= "</ul>\n";
    }

    if ($return) {
        return $output;
    } else {
        echo $output;
    }
}

/**
 * This function will build the navigation string to be used by print_header
 * and others.
 *
 * It automatically generates the site and course level (if appropriate) links.
 *
 * If you pass in a $cm object, the method will also generate the activity (e.g. 'Forums')
 * and activityinstances (e.g. 'General Developer Forum') navigation levels.
 *
 * If you want to add any further navigation links after the ones this function generates,
 * the pass an array of extra link arrays like this:
 * array(
 *     array('name' => $linktext1, 'link' => $url1, 'type' => $linktype1),
 *     array('name' => $linktext2, 'link' => $url2, 'type' => $linktype2)
 * )
 * The normal case is to just add one further link, for example 'Editing forum' after
 * 'General Developer Forum', with no link.
 * To do that, you need to pass
 * array(array('name' => $linktext, 'link' => '', 'type' => 'title'))
 * However, becuase this is a very common case, you can use a shortcut syntax, and just
 * pass the string 'Editing forum', instead of an array as $extranavlinks.
 *
 * At the moment, the link types only have limited significance. Type 'activity' is
 * recognised in order to implement the $CFG->hideactivitytypenavlink feature. Types
 * that are known to appear are 'home', 'course', 'activity', 'activityinstance' and 'title'.
 * This really needs to be documented better. In the mean time, try to be consistent, it will
 * enable people to customise the navigation more in future.
 *
 * When passing a $cm object, the fields used are $cm->modname, $cm->name and $cm->course.
 * If you get the $cm object using the function get_coursemodule_from_instance or
 * get_coursemodule_from_id (as recommended) then this will be done for you automatically.
 * If you don't have $cm->modname or $cm->name, this fuction will attempt to find them using
 * the $cm->module and $cm->instance fields, but this takes extra database queries, so a
 * warning is printed in developer debug mode.
 *
 * @global object
 * @global object
 * @global object
 * @global object
 * @uses SITEID
 * @uses DEBUG_DEVELOPER
 * @uses CONTEXT_SYSTEM
 * @param mixed $extranavlinks - Normally an array of arrays, keys: name, link, type. If you
 *      only want one extra item with no link, you can pass a string instead. If you don't want
 *      any extra links, pass an empty string.
 * @param mixed $cm - optionally the $cm object, if you want this function to generate the
 *      activity and activityinstance levels of navigation too.
 * @return array Navigation array
 */
function build_navigation($extranavlinks, $cm = null) {
    global $CFG, $COURSE, $DB, $SITE;

    if (is_string($extranavlinks)) {
        if ($extranavlinks == '') {
            $extranavlinks = array();
        } else {
            $extranavlinks = array(array('name' => $extranavlinks, 'link' => '', 'type' => 'title'));
        }
    }

    $navlinks = array();

    //Site name
    if (!empty($SITE->shortname)) {
        $navlinks[] = array(
                'name' => format_string($SITE->shortname),
                'link' => "$CFG->wwwroot/",
                'type' => 'home');
    }

    // Course name, if appropriate.
    if (isset($COURSE) && $COURSE->id != SITEID) {
        $navlinks[] = array(
                'name' => format_string($COURSE->shortname),
                'link' => "$CFG->wwwroot/course/view.php?id=$COURSE->id",
                'type' => 'course');
    }

    // Activity type and instance, if appropriate.
    if (is_object($cm)) {
        if (!isset($cm->modname)) {
            debugging('The field $cm->modname should be set if you call build_navigation with '.
                    'a $cm parameter. If you get $cm using get_coursemodule_from_instance or '.
                    'get_coursemodule_from_id, this will be done automatically.', DEBUG_DEVELOPER);
            if (!$cm->modname = $DB->get_field('modules', 'name', array('id'=>$cm->module))) {
                print_error('cannotmoduletype');
            }
        }
        if (!isset($cm->name)) {
            debugging('The field $cm->name should be set if you call build_navigation with '.
                    'a $cm parameter. If you get $cm using get_coursemodule_from_instance or '.
                    'get_coursemodule_from_id, this will be done automatically.', DEBUG_DEVELOPER);
            if (!$cm->name = $DB->get_field($cm->modname, 'name', array('id'=>$cm->instance))) {
                print_error('cannotmodulename');
            }
        }
        $navlinks[] = array(
                'name' => get_string('modulenameplural', $cm->modname),
                'link' => $CFG->wwwroot . '/mod/' . $cm->modname . '/index.php?id=' . $cm->course,
                'type' => 'activity');
        $navlinks[] = array(
                'name' => format_string($cm->name),
                'link' => $CFG->wwwroot . '/mod/' . $cm->modname . '/view.php?id=' . $cm->id,
                'type' => 'activityinstance');
    }

    //Merge in extra navigation links
    $navlinks = array_merge($navlinks, $extranavlinks);

    // Work out whether we should be showing the activity (e.g. Forums) link.
    // Note: build_navigation() is called from many places --
    // install & upgrade for example -- where we cannot count on the
    // roles infrastructure to be defined. Hence the during_initial_install() check.
    if (!isset($CFG->hideactivitytypenavlink)) {
        $CFG->hideactivitytypenavlink = 0;
    }
    if ($CFG->hideactivitytypenavlink == 2) {
        $hideactivitylink = true;
    } else if ($CFG->hideactivitytypenavlink == 1 && !during_initial_install() &&
            !empty($COURSE->id) && $COURSE->id != SITEID) {
        if (!isset($COURSE->context)) {
            $COURSE->context = get_context_instance(CONTEXT_COURSE, $COURSE->id);
        }
        $hideactivitylink = !has_capability('moodle/course:manageactivities', $COURSE->context);
    } else {
        $hideactivitylink = false;
    }

    //Construct an unordered list from $navlinks
    //Accessibility: heading hidden from visual browsers by default.
    $navigation = get_accesshide(get_string('youarehere','access'), 'h2')." <ul>\n";
    $lastindex = count($navlinks) - 1;
    $i = -1; // Used to count the times, so we know when we get to the last item.
    $first = true;
    foreach ($navlinks as $navlink) {
        $i++;
        $last = ($i == $lastindex);
        if (!is_array($navlink)) {
            continue;
        }
        if (!empty($navlink['type']) && $navlink['type'] == 'activity' && !$last && $hideactivitylink) {
            continue;
        }
        if ($first) {
            $navigation .= '<li class="first">';
        } else {
            $navigation .= '<li>';
        }
        if (!$first) {
            $navigation .= get_separator();
        }
        if ((!empty($navlink['link'])) && !$last) {
            $navigation .= "<a onclick=\"this.target='$CFG->framename'\" href=\"{$navlink['link']}\">";
        }
        $navigation .= "{$navlink['name']}";
        if ((!empty($navlink['link'])) && !$last) {
            $navigation .= "</a>";
        }

        $navigation .= "</li>";
        $first = false;
    }
    $navigation .= "</ul>";

    return(array('newnav' => true, 'navlinks' => $navigation));
}

/**
 * Centered heading with attached help button (same title text)
 * and optional icon attached
 *
 * @param string $text The text to be displayed
 * @param string $helppage The help page to link to
 * @param string $module The module whose help should be linked to
 * @param string $icon Image to display if needed
 * @param bool $return If set to true output is returned rather than echoed, default false
 * @return string|void String if return=true nothing otherwise
 */
function print_heading_with_help($text, $helppage, $module='moodle', $icon='', $return=false) {
    $output = '<div class="heading-with-help">';
    $output .= '<h2 class="main help">'.$icon.$text.'</h2>';
    $output .= helpbutton($helppage, $text, $module, true, false, '', true);
    $output .= '</div>';

    if ($return) {
        return $output;
    } else {
        echo $output;
    }
}

/**
 * Print (or return) a collapisble region, that has a caption that can
 * be clicked to expand or collapse the region.
 * 
 * If JavaScript is off, then the region will always be exanded.
 *
 * @param string $contents the contents of the box.
 * @param string $classes class names added to the div that is output.
 * @param string $id id added to the div that is output. Must not be blank.
 * @param string $caption text displayed at the top. Clicking on this will cause the region to expand or contract.
 * @param string $userpref the name of the user preference that stores the user's preferred deafault state.
 *      (May be blank if you do not wish the state to be persisted.
 * @param boolean $default Inital collapsed state to use if the user_preference it not set.
 * @param boolean $return if true, return the HTML as a string, rather than printing it.
 * @return string|void If $return is false, returns nothing, otherwise returns a string of HTML.
 */
function print_collapsible_region($contents, $classes, $id, $caption, $userpref = '', $default = false, $return = false) {
    $output  = print_collapsible_region_start($classes, $id, $caption, $userpref, true);
    $output .= $contents;
    $output .= print_collapsible_region_end(true);

    if ($return) {
        return $output;
    } else {
        echo $output;
    }
}

/**
 * Print (or return) the start of a collapisble region, that has a caption that can
 * be clicked to expand or collapse the region. If JavaScript is off, then the region
 * will always be exanded.
 *
 * @global object
 * @param string $classes class names added to the div that is output.
 * @param string $id id added to the div that is output. Must not be blank.
 * @param string $caption text displayed at the top. Clicking on this will cause the region to expand or contract.
 * @param boolean $userpref the name of the user preference that stores the user's preferred deafault state.
 *      (May be blank if you do not wish the state to be persisted.
 * @param boolean $default Inital collapsed state to use if the user_preference it not set.
 * @param boolean $return if true, return the HTML as a string, rather than printing it.
 * @return string|void if $return is false, returns nothing, otherwise returns a string of HTML.
 */
function print_collapsible_region_start($classes, $id, $caption, $userpref = false, $default = false, $return = false) {
    global $CFG, $PAGE, $OUTPUT;

    // Include required JavaScript libraries.
    $PAGE->requires->yui_lib('animation');

    // Work out the initial state.
    if (is_string($userpref)) {
        user_preference_allow_ajax_update($userpref, PARAM_BOOL);
        $collapsed = get_user_preferences($userpref, $default);
    } else {
        $collapsed = $default;
        $userpref = false;
    }

    if ($collapsed) {
        $classes .= ' collapsed';
    }

    $output = '';
    $output .= '<div id="' . $id . '" class="collapsibleregion ' . $classes . '">';
    $output .= '<div id="' . $id . '_sizer">';
    $output .= '<div id="' . $id . '_caption" class="collapsibleregioncaption">';
    $output .= $caption . ' ';
    $output .= '</div><div id="' . $id . '_inner" class="collapsibleregioninner">';
    $PAGE->requires->js_function_call('new collapsible_region',
            array($id, $userpref, get_string('clicktohideshow'),
            $OUTPUT->old_icon_url('t/collapsed'), $OUTPUT->old_icon_url('t/expanded')));

    if ($return) {
        return $output;
    } else {
        echo $output;
    }
}

/**
 * Close a region started with print_collapsible_region_start.
 *
 * @param boolean $return if true, return the HTML as a string, rather than printing it.
 * @return string|void if $return is false, returns nothing, otherwise returns a string of HTML.
 */
function print_collapsible_region_end($return = false) {
    $output = '</div></div></div>';

    if ($return) {
        return $output;
    } else {
        echo $output;
    }
}

/**
 * Returns number of currently open containers
 *
 * @global object
 * @return int number of open containers
 */
function open_containers() {
    global $THEME;

    if (!isset($THEME->open_containers)) {
        $THEME->open_containers = array();
    }

    return count($THEME->open_containers);
}

/**
 * Force closing of open containers
 *
 * @param boolean $return, return as string or just print it
 * @param int $keep number of containers to be kept open - usually theme or page containers
 * @return mixed string or void
 */
function print_container_end_all($return=false, $keep=0) {
    $output = '';
    while (open_containers() > $keep) {
        $output .= print_container_end($return);
    }

    if ($return) {
        return $output;
    } else {
        echo $output;
    }
}

/**
 * Internal function - do not use directly!
 * Starting part of the surrounding divs for custom corners
 *
 * @param boolean $clearfix, add CLASS "clearfix" to the inner div against collapsing
 * @param string $classes
 * @param mixed $idbase, optionally, define one idbase to be added to all the elements in the corners
 * @return string
 */
function _print_custom_corners_start($clearfix=false, $classes='', $idbase='') {
/// Analise if we want ids for the custom corner elements
    $id = '';
    $idbt = '';
    $idi1 = '';
    $idi2 = '';
    $idi3 = '';

    if ($idbase) {
        $id   = 'id="'.$idbase.'" ';
        $idbt = 'id="'.$idbase.'-bt" ';
        $idi1 = 'id="'.$idbase.'-i1" ';
        $idi2 = 'id="'.$idbase.'-i2" ';
        $idi3 = 'id="'.$idbase.'-i3" ';
    }

/// Calculate current level
    $level = open_containers();

/// Output begins
    $output = '<div '.$id.'class="wrap wraplevel'.$level.' '.$classes.'">'."\n";
    $output .= '<div '.$idbt.'class="bt"><div>&nbsp;</div></div>';
    $output .= "\n";
    $output .= '<div '.$idi1.'class="i1"><div '.$idi2.'class="i2">';
    $output .= (!empty($clearfix)) ? '<div '.$idi3.'class="i3 clearfix">' : '<div '.$idi3.'class="i3">';

    return $output;
}


/**
 * Internal function - do not use directly!
 * Ending part of the surrounding divs for custom corners
 *
 * @param string $idbase
 * @return string HTML sttring
 */
function _print_custom_corners_end($idbase) {
/// Analise if we want ids for the custom corner elements
    $idbb = '';

    if ($idbase) {
        $idbb = 'id="' . $idbase . '-bb" ';
    }

/// Output begins
    $output = '</div></div></div>';
    $output .= "\n";
    $output .= '<div '.$idbb.'class="bb"><div>&nbsp;</div></div>'."\n";
    $output .= '</div>';

    return $output;
}


/**
 * Print a self contained form with a single submit button.
 *
 * @param string $link used as the action attribute on the form, so the URL that will be hit if the button is clicked.
 * @param array $options these become hidden form fields, so these options get passed to the script at $link.
 * @param string $label the caption that appears on the button.
 * @param string $method HTTP method used on the request of the button is clicked. 'get' or 'post'.
 * @param string $notusedanymore no longer used.
 * @param boolean $return if false, output the form directly, otherwise return the HTML as a string.
 * @param string $tooltip a tooltip to add to the button as a title attribute.
 * @param boolean $disabled if true, the button will be disabled.
 * @param string $jsconfirmmessage if not empty then display a confirm dialogue with this string as the question.
 * @param string $formid The id attribute to use for the form
 * @return string|void Depending on the $return paramter.
 */
function print_single_button($link, $options, $label='OK', $method='get', $notusedanymore='',
        $return=false, $tooltip='', $disabled = false, $jsconfirmmessage='', $formid = '') {
    $output = '';
    if ($formid) {
        $formid = ' id="' . s($formid) . '"';
    }
    $link = str_replace('"', '&quot;', $link); //basic XSS protection
    $output .= '<div class="singlebutton">';
    // taking target out, will need to add later target="'.$target.'"
    $output .= '<form action="'. $link .'" method="'. $method .'"' . $formid . '>';
    $output .= '<div>';
    if ($options) {
        foreach ($options as $name => $value) {
            $output .= '<input type="hidden" name="'. $name .'" value="'. s($value) .'" />';
        }
    }
    if ($tooltip) {
        $tooltip = 'title="' . s($tooltip) . '"';
    } else {
        $tooltip = '';
    }
    if ($disabled) {
        $disabled = 'disabled="disabled"';
    } else {
        $disabled = '';
    }
    if ($jsconfirmmessage){
        $jsconfirmmessage = addslashes_js($jsconfirmmessage);
        $jsconfirmmessage = 'onclick="return confirm(\''. $jsconfirmmessage .'\');" ';
    }
    $output .= '<input type="submit" value="'. s($label) ."\" $tooltip $disabled $jsconfirmmessage/></div></form></div>";

    if ($return) {
        return $output;
    } else {
        echo $output;
    }
}


/**
 * Print a spacer image with the option of including a line break.
 *
 * @global object
 * @param int $height The height in pixels to make the spacer
 * @param int $width The width in pixels to make the spacer
 * @param boolean $br If set to true a BR is written after the spacer
 */
function print_spacer($height=1, $width=1, $br=true, $return=false) {
    global $CFG;
    $output = '';

    $output .= '<img class="spacer" height="'. $height .'" width="'. $width .'" src="'. $CFG->wwwroot .'/pix/spacer.gif" alt="" />';
    if ($br) {
        $output .= '<br />'."\n";
    }

    if ($return) {
        return $output;
    } else {
        echo $output;
    }
}

/**
 * Given the path to a picture file in a course, or a URL,
 * this function includes the picture in the page.
 *
 * @global object
 * @param string $path The path the to the picture
 * @param int $courseid The courseid the picture is associated with if any
 * @param int $height The height of the picture in pixels if known
 * @param int $width The width of the picture in pixels if known
 * @param string $link If set the image is wrapped with this link
 * @param bool $return If true the HTML is returned rather than being echo'd
 * @return string|void Depending on $return
 */
function print_file_picture($path, $courseid=0, $height='', $width='', $link='', $return=false) {
    global $CFG;
    $output = '';

    if ($height) {
        $height = 'height="'. $height .'"';
    }
    if ($width) {
        $width = 'width="'. $width .'"';
    }
    if ($link) {
        $output .= '<a href="'. $link .'">';
    }
    if (substr(strtolower($path), 0, 7) == 'http://') {
        $output .= '<img style="height:'.$height.'px;width:'.$width.'px;" src="'. $path .'" />';

    } else if ($courseid) {
        $output .= '<img style="height:'.$height.'px;width:'.$width.'px;" src="';
        require_once($CFG->libdir.'/filelib.php');
        $output .= get_file_url("$courseid/$path");
        $output .= '" />';
    } else {
        $output .= 'Error: must pass URL or course';
    }
    if ($link) {
        $output .= '</a>';
    }

    if ($return) {
        return $output;
    } else {
        echo $output;
    }
}

/**
 * Print the specified user's avatar.
 *
 * @global object
 * @global object
 * @param mixed $user Should be a $user object with at least fields id, picture, imagealt, firstname, lastname
 *      If any of these are missing, or if a userid is passed, the the database is queried. Avoid this
 *      if at all possible, particularly for reports. It is very bad for performance.
 * @param int $courseid The course id. Used when constructing the link to the user's profile.
 * @param boolean $picture The picture to print. By default (or if NULL is passed) $user->picture is used.
 * @param int $size Size in pixels. Special values are (true/1 = 100px) and (false/0 = 35px) for backward compatability
 * @param boolean $return If false print picture to current page, otherwise return the output as string
 * @param boolean $link enclose printed image in a link the user's profile (default true).
 * @param string $target link target attribute. Makes the profile open in a popup window.
 * @param boolean $alttext add non-blank alt-text to the image. (Default true, set to false for purely
 *      decorative images, or where the username will be printed anyway.)
 * @return string|void String or nothing, depending on $return.
 */
function print_user_picture($user, $courseid, $picture=NULL, $size=0, $return=false, $link=true, $target='', $alttext=true) {
    global $CFG, $DB, $OUTPUT;

    $needrec = false;
    // only touch the DB if we are missing data...
    if (is_object($user)) {
        // Note - both picture and imagealt _can_ be empty
        // what we are trying to see here is if they have been fetched
        // from the DB. We should use isset() _except_ that some installs
        // have those fields as nullable, and isset() will return false
        // on null. The only safe thing is to ask array_key_exists()
        // which works on objects. property_exists() isn't quite
        // what we want here...
        if (! (array_key_exists('picture', $user)
               && ($alttext && array_key_exists('imagealt', $user)
                   || (isset($user->firstname) && isset($user->lastname)))) ) {
            $needrec = true;
            $user = $user->id;
        }
    } else {
        if ($alttext) {
            // we need firstname, lastname, imagealt, can't escape...
            $needrec = true;
        } else {
            $userobj = new StdClass; // fake it to save DB traffic
            $userobj->id = $user;
            $userobj->picture = $picture;
            $user = clone($userobj);
            unset($userobj);
        }
    }
    if ($needrec) {
        $user = $DB->get_record('user', array('id'=>$user), 'id,firstname,lastname,imagealt');
    }

    if ($link) {
        $url = '/user/view.php?id='. $user->id .'&amp;course='. $courseid ;
        if ($target) {
            $target='onclick="return openpopup(\''.$url.'\');"';
        }
        $output = '<a '.$target.' href="'. $CFG->wwwroot . $url .'">';
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

    if (is_null($picture)) {
        $picture = $user->picture;
    }

    if ($picture) {  // Print custom user picture
        require_once($CFG->libdir.'/filelib.php');
        $src = get_file_url($user->id.'/'.$file.'.jpg', null, 'user');
    } else {         // Print default user pictures (use theme version if available)
        $class .= " defaultuserpic";
        $src =  $OUTPUT->old_icon_url('u/' . $file);
    }
    $imagealt = '';
    if ($alttext) {
        if (!empty($user->imagealt)) {
            $imagealt = $user->imagealt;
        } else {
            $imagealt = get_string('pictureof','',fullname($user));
        }
    }

    $output .= "<img class=\"$class\" src=\"$src\" height=\"$size\" width=\"$size\" alt=\"".s($imagealt).'"  />';
    if ($link) {
        $output .= '</a>';
    }

    if ($return) {
        return $output;
    } else {
        echo $output;
    }
}

/**
 * Prints a summary of a user in a nice little box.
 *
 * @global object
 * @global object
 * @staticvar object $string
 * @staticvar object $datestring
 * @staticvar array $countries
 * @uses CONTEXT_COURSE
 * @uses CONTEXT_USER
 * @uses SITEID
 * @param object $user A {@link $USER} object representing a user
 * @param object $course A {@link $COURSE} object representing a course
 * @param bool $messageselect 
 * @param bool $return If set to true then the HTML is returned rather than echo'd
 * @return string|void Depending on the setting of $return
 */
function print_user($user, $course, $messageselect=false, $return=false) {

    global $CFG, $USER;

    $output = '';

    static $string;
    static $datestring;
    static $countries;

    $context = get_context_instance(CONTEXT_COURSE, $course->id);
    if (isset($user->context->id)) {
        $usercontext = $user->context;
    } else {
        $usercontext = get_context_instance(CONTEXT_USER, $user->id);
    }

    if (empty($string)) {     // Cache all the strings for the rest of the page

        $string->email       = get_string('email');
        $string->city = get_string('city');
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
        $datestring->year    = get_string('year');
        $datestring->years   = get_string('years');

        $countries = get_list_of_countries();
    }

/// Get the hidden field list
    if (has_capability('moodle/course:viewhiddenuserfields', $context)) {
        $hiddenfields = array();
    } else {
        $hiddenfields = array_flip(explode(',', $CFG->hiddenuserfields));
    }

    $output .= '<table class="userinfobox">';
    $output .= '<tr>';
    $output .= '<td class="left side">';
    $output .= print_user_picture($user, $course->id, $user->picture, true, true);
    $output .= '</td>';
    $output .= '<td class="content">';
    $output .= '<div class="username">'.fullname($user, has_capability('moodle/site:viewfullnames', $context)).'</div>';
    $output .= '<div class="info">';
    if (!empty($user->role)) {
        $output .= $string->role .': '. $user->role .'<br />';
    }
    if ($user->maildisplay == 1 or ($user->maildisplay == 2 and ($course->id != SITEID) and !isguest()) or
has_capability('moodle/course:viewhiddenuserfields', $context)) {
        $output .= $string->email .': <a href="mailto:'. $user->email .'">'. $user->email .'</a><br />';
    }
    if (($user->city or $user->country) and (!isset($hiddenfields['city']) or !isset($hiddenfields['country']))) {
        $output .= $string->city .': ';
        if ($user->city && !isset($hiddenfields['city'])) {
            $output .= $user->city;
        }
        if (!empty($countries[$user->country]) && !isset($hiddenfields['country'])) {
            if ($user->city && !isset($hiddenfields['city'])) {
                $output .= ', ';
            }
            $output .= $countries[$user->country];
        }
        $output .= '<br />';
    }

    if (!isset($hiddenfields['lastaccess'])) {
        if ($user->lastaccess) {
            $output .= $string->lastaccess .': '. userdate($user->lastaccess);
            $output .= '&nbsp; ('. format_time(time() - $user->lastaccess, $datestring) .')';
        } else {
            $output .= $string->lastaccess .': '. $string->never;
        }
    }
    $output .= '</div></td><td class="links">';
    //link to blogs
    if ($CFG->bloglevel > 0) {
        $output .= '<a href="'.$CFG->wwwroot.'/blog/index.php?userid='.$user->id.'">'.get_string('blogs','blog').'</a><br />';
    }
    //link to notes
    if (!empty($CFG->enablenotes) and (has_capability('moodle/notes:manage', $context) || has_capability('moodle/notes:view', $context))) {
        $output .= '<a href="'.$CFG->wwwroot.'/notes/index.php?course=' . $course->id. '&amp;user='.$user->id.'">'.get_string('notes','notes').'</a><br />';
    }

    if (has_capability('moodle/site:viewreports', $context) or has_capability('moodle/user:viewuseractivitiesreport', $usercontext)) {
        $output .= '<a href="'. $CFG->wwwroot .'/course/user.php?id='. $course->id .'&amp;user='. $user->id .'">'. $string->activity .'</a><br />';
    }
    if (has_capability('moodle/role:assign', $context) and get_user_roles($context, $user->id, false)) {  // I can unassing and user has some role
        $output .= '<a href="'. $CFG->wwwroot .'/course/unenrol.php?id='. $course->id .'&amp;user='. $user->id .'">'. $string->unenrol .'</a><br />';
    }
    if ($USER->id != $user->id && !session_is_loggedinas() && has_capability('moodle/user:loginas', $context) &&
                                 ! has_capability('moodle/site:doanything', $context, $user->id, false)) {
        $output .= '<a href="'. $CFG->wwwroot .'/course/loginas.php?id='. $course->id .'&amp;user='. $user->id .'&amp;sesskey='. sesskey() .'">'. $string->loginas .'</a><br />';
    }
    $output .= '<a href="'. $CFG->wwwroot .'/user/view.php?id='. $user->id .'&amp;course='. $course->id .'">'. $string->fullprofile .'...</a>';

    if (!empty($messageselect)) {
        $output .= '<br /><input type="checkbox" name="user'.$user->id.'" /> ';
    }

    $output .= '</td></tr></table>';

    if ($return) {
        return $output;
    } else {
        echo $output;
    }
}

/**
 * Print a specified group's avatar.
 *
 * @global object
 * @uses CONTEXT_COURSE
 * @param array $group A single {@link group} object OR array of groups.
 * @param int $courseid The course ID.
 * @param boolean $large Default small picture, or large.
 * @param boolean $return If false print picture, otherwise return the output as string
 * @param boolean $link Enclose image in a link to view specified course?
 * @return string|void Depending on the setting of $return
 */
function print_group_picture($group, $courseid, $large=false, $return=false, $link=true) {
    global $CFG;

    if (is_array($group)) {
        $output = '';
        foreach($group as $g) {
            $output .= print_group_picture($g, $courseid, $large, true, $link);
        }
        if ($return) {
            return $output;
        } else {
            echo $output;
            return;
        }
    }

    $context = get_context_instance(CONTEXT_COURSE, $courseid);

    if ($group->hidepicture and !has_capability('moodle/course:managegroups', $context)) {
        return '';
    }

    if ($link or has_capability('moodle/site:accessallgroups', $context)) {
        $output = '<a href="'. $CFG->wwwroot .'/user/index.php?id='. $courseid .'&amp;group='. $group->id .'">';
    } else {
        $output = '';
    }
    if ($large) {
        $file = 'f1';
    } else {
        $file = 'f2';
    }
    if ($group->picture) {  // Print custom group picture
        require_once($CFG->libdir.'/filelib.php');
        $grouppictureurl = get_file_url($group->id.'/'.$file.'.jpg', null, 'usergroup');
        $output .= '<img class="grouppicture" src="'.$grouppictureurl.'"'.
            ' alt="'.s(get_string('group').' '.$group->name).'" title="'.s($group->name).'"/>';
    }
    if ($link or has_capability('moodle/site:accessallgroups', $context)) {
        $output .= '</a>';
    }

    if ($return) {
        return $output;
    } else {
        echo $output;
    }
}

/**
 * Print a png image.
 *
 * @global object
 * @staticvar bool $recentIE
 * @param string $url The URL of the image to display
 * @param int $sizex The width of the image in pixels
 * @param int $sizey The height of the image in pixels
 * @param boolean $return If true the HTML is returned rather than echo'd
 * @param string $parameters Additional HTML attributes to set
 * @return string|bool Depending on $return
 */
function print_png($url, $sizex, $sizey, $return, $parameters='alt=""') {
    global $OUTPUT;
    static $recentIE;

    if (!isset($recentIE)) {
        $recentIE = check_browser_version('MSIE', '5.0');
    }

    if ($recentIE) {  // work around the HORRIBLE bug IE has with alpha transparencies
        $output .= '<img src="'. $OUTPUT->old_icon_url('spacer') . '" width="'. $sizex .'" height="'. $sizey .'"'.
                   ' class="png" style="width: '. $sizex .'px; height: '. $sizey .'px; '.
                   ' filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='.
                   "'$url', sizingMethod='scale') ".
                   ' '. $parameters .' />';
    } else {
        $output .= '<img src="'. $url .'" style="width: '. $sizex .'px; height: '. $sizey .'px; '. $parameters .' />';
    }

    if ($return) {
        return $output;
    } else {
        echo $output;
    }
}

/**
 * Print a nicely formatted table.
 *
 * @param array $table is an object with several properties.
 * <ul>
 *     <li>$table->head - An array of heading names.
 *     <li>$table->align - An array of column alignments
 *     <li>$table->size  - An array of column sizes
 *     <li>$table->wrap - An array of "nowrap"s or nothing
 *     <li>$table->data[] - An array of arrays containing the data.
 *     <li>$table->width  - A percentage of the page
 *     <li>$table->tablealign  - Align the whole table
 *     <li>$table->cellpadding  - Padding on each cell
 *     <li>$table->cellspacing  - Spacing between cells
 *     <li>$table->class - class attribute to put on the table
 *     <li>$table->id - id attribute to put on the table.
 *     <li>$table->rowclass[] - classes to add to particular rows. (space-separated string)
 *     <li>$table->colclass[] - classes to add to every cell in a particular colummn. (space-separated string)
 *     <li>$table->summary - Description of the contents for screen readers.
 *     <li>$table->headspan can be used to make a heading span multiple columns.
 *     <li>$table->rotateheaders - Causes the contents of the heading cells to be rotated 90 degrees.
 * </ul>
 * @param bool $return whether to return an output string or echo now
 * @return boolean|string depending on $return
 */
function print_table($table, $return=false) {
    $output = '';

    if (isset($table->align)) {
        foreach ($table->align as $key => $aa) {
            if ($aa) {
                $align[$key] = ' text-align:'. fix_align_rtl($aa) .';';  // Fix for RTL languages
            } else {
                $align[$key] = '';
            }
        }
    }
    if (isset($table->size)) {
        foreach ($table->size as $key => $ss) {
            if ($ss) {
                $size[$key] = ' width:'. $ss .';';
            } else {
                $size[$key] = '';
            }
        }
    }
    if (isset($table->wrap)) {
        foreach ($table->wrap as $key => $ww) {
            if ($ww) {
                $wrap[$key] = ' white-space:nowrap;';
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

    if (!isset($table->cellpadding)) {
        $table->cellpadding = '5';
    }

    if (!isset($table->cellspacing)) {
        $table->cellspacing = '1';
    }

    if (empty($table->class)) {
        $table->class = 'generaltable';
    }
    if (!empty($table->rotateheaders)) {
        $table->class .= ' rotateheaders';
    } else {
        $table->rotateheaders = false; // Makes life easier later.
    }

    $tableid = empty($table->id) ? '' : 'id="'.$table->id.'"';

    $output .= '<table width="'.$table->width.'" ';
    if (!empty($table->summary)) {
        $output .= " summary=\"$table->summary\"";
    }
    $output .= " cellpadding=\"$table->cellpadding\" cellspacing=\"$table->cellspacing\" class=\"$table->class boxalign$table->tablealign\" $tableid>\n";

    $countcols = 0;

    if (!empty($table->head)) {
        $countcols = count($table->head);
        $output .= '<tr>';
        $keys = array_keys($table->head);
        $lastkey = end($keys);
        foreach ($table->head as $key => $heading) {
            $classes = array('header', 'c' . $key);
            if (!isset($size[$key])) {
                $size[$key] = '';
            }
            if (!isset($align[$key])) {
                $align[$key] = '';
            }
            if (isset($table->headspan[$key]) && $table->headspan[$key] > 1) {
                $colspan = ' colspan="' . $table->headspan[$key] . '"';
            } else {
                $colspan = '';
            }
            if ($key == $lastkey) {
                $classes[] = 'lastcol';
            }
            if (isset($table->colclasses[$key])) {
                $classes[] = $table->colclasses[$key];
            }
            if ($table->rotateheaders) {
                $wrapperstart = '<span>';
                $wrapperend = '</span>';
            } else {
                $wrapperstart = '';
                $wrapperend = '';
            }

            $output .= '<th style="'. $align[$key].$size[$key] .
                    ';white-space:nowrap;" class="'.implode(' ', $classes).'" scope="col"' . $colspan . '>'.
                    $wrapperstart . $heading . $wrapperend . '</th>';
        }
        $output .= '</tr>'."\n";
    }

    if (!empty($table->data)) {
        $oddeven = 1;
        $keys=array_keys($table->data);
        $lastrowkey = end($keys);
        foreach ($table->data as $key => $row) {
            $oddeven = $oddeven ? 0 : 1;
            if (!isset($table->rowclass[$key])) {
                $table->rowclass[$key] = '';
            }
            if ($key == $lastrowkey) {
                $table->rowclass[$key] .= ' lastrow';
            }
            $output .= '<tr class="r'.$oddeven.' '.$table->rowclass[$key].'">'."\n";
            if ($row == 'hr' and $countcols) {
                $output .= '<td colspan="'. $countcols .'"><div class="tabledivider"></div></td>';
            } else {  /// it's a normal row of data
                $keys2 = array_keys($row);
                $lastkey = end($keys2);
                foreach ($row as $key => $item) {
                    $classes = array('cell', 'c' . $key);
                    if (!isset($size[$key])) {
                        $size[$key] = '';
                    }
                    if (!isset($align[$key])) {
                        $align[$key] = '';
                    }
                    if (!isset($wrap[$key])) {
                        $wrap[$key] = '';
                    }
                    if ($key == $lastkey) {
                        $classes[] = 'lastcol';
                    }
                    if (isset($table->colclasses[$key])) {
                        $classes[] = $table->colclasses[$key];
                    }
                    $output .= '<td style="'. $align[$key].$size[$key].$wrap[$key] .'" class="'.implode(' ', $classes).'">'. $item .'</td>';
                }
            }
            $output .= '</tr>'."\n";
        }
    }
    $output .= '</table>'."\n";

    if ($table->rotateheaders && can_use_rotated_text()) {
        $PAGE->requires->yui_lib('event');
        $PAGE->requires->js('course/report/progress/textrotate.js');
    }

    if ($return) {
        return $output;
    }

    echo $output;
    return true;
}

/**
 * Display a recent activity note
 * 
 * @uses CONTEXT_SYSTEM
 * @staticvar string $strftimerecent
 * @param object A time object
 * @param object A user object
 * @param string $text Text for display for the note
 * @param string $link The link to wrap around the text
 * @param bool $return If set to true the HTML is returned rather than echo'd
 * @param string $viewfullnames
 */
function print_recent_activity_note($time, $user, $text, $link, $return=false, $viewfullnames=null) {
    static $strftimerecent = null;
    $output = '';

    if (is_null($viewfullnames)) {
        $context = get_context_instance(CONTEXT_SYSTEM);
        $viewfullnames = has_capability('moodle/site:viewfullnames', $context);
    }

    if (is_null($strftimerecent)) {
        $strftimerecent = get_string('strftimerecent');
    }

    $output .= '<div class="head">';
    $output .= '<div class="date">'.userdate($time, $strftimerecent).'</div>';
    $output .= '<div class="name">'.fullname($user, $viewfullnames).'</div>';
    $output .= '</div>';
    $output .= '<div class="info"><a href="'.$link.'">'.format_string($text,true).'</a></div>';

    if ($return) {
        return $output;
    } else {
        echo $output;
    }
}


/**
 * Prints a basic textarea field.
 *
 * When using this function, you should
 *
 * @global object
 * @param bool $usehtmleditor Enables the use of the htmleditor for this field.
 * @param int $rows Number of rows to display  (minimum of 10 when $height is non-null)
 * @param int $cols Number of columns to display (minimum of 65 when $width is non-null)
 * @param null $width (Deprecated) Width of the element; if a value is passed, the minimum value for $cols will be 65. Value is otherwise ignored.
 * @param null $height (Deprecated) Height of the element; if a value is passe, the minimum value for $rows will be 10. Value is otherwise ignored.
 * @param string $name Name to use for the textarea element.
 * @param string $value Initial content to display in the textarea.
 * @param int $obsolete deprecated
 * @param bool $return If false, will output string. If true, will return string value.
 * @param string $id CSS ID to add to the textarea element.
 * @return string|void depending on the value of $return
 */
function print_textarea($usehtmleditor, $rows, $cols, $width, $height, $name, $value='', $obsolete=0, $return=false, $id='') {
    /// $width and height are legacy fields and no longer used as pixels like they used to be.
    /// However, you can set them to zero to override the mincols and minrows values below.

    global $CFG;

    $mincols = 65;
    $minrows = 10;
    $str = '';

    if ($id === '') {
        $id = 'edit-'.$name;
    }

    if ($usehtmleditor) {
        if ($height && ($rows < $minrows)) {
            $rows = $minrows;
        }
        if ($width && ($cols < $mincols)) {
            $cols = $mincols;
        }
    }

    if ($usehtmleditor) {
        editors_head_setup();
        $editor = get_preferred_texteditor(FORMAT_HTML);
        $editor->use_editor($id, array('legacy'=>true));
    } else {
        $editorclass = '';
    }

    $str .= "\n".'<textarea class="form-textarea" id="'. $id .'" name="'. $name .'" rows="'. $rows .'" cols="'. $cols .'">'."\n";
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
 * Returns a turn edit on/off button for course in a self contained form.
 * Used to be an icon, but it's now a simple form button
 *
 * Note that the caller is responsible for capchecks.
 *
 * @global object
 * @global object
 * @param int $courseid The course  to update by id as found in 'course' table
 * @return string
 */
function update_course_icon($courseid) {
    global $CFG, $USER;

    if (!empty($USER->editing)) {
        $string = get_string('turneditingoff');
        $edit = '0';
    } else {
        $string = get_string('turneditingon');
        $edit = '1';
    }

    return '<form '.$CFG->frametarget.' method="get" action="'.$CFG->wwwroot.'/course/view.php">'.
           '<div>'.
           '<input type="hidden" name="id" value="'.$courseid.'" />'.
           '<input type="hidden" name="edit" value="'.$edit.'" />'.
           '<input type="hidden" name="sesskey" value="'.sesskey().'" />'.
           '<input type="submit" value="'.$string.'" />'.
           '</div></form>';
}

/**
 * Returns a little popup menu for switching roles
 *
 * @global object
 * @global object
 * @uses CONTEXT_COURSE
 * @param int $courseid The course  to update by id as found in 'course' table
 * @return string
 */
function switchroles_form($courseid) {

    global $CFG, $USER;


    if (!$context = get_context_instance(CONTEXT_COURSE, $courseid)) {
        return '';
    }

    if (!empty($USER->access['rsw'][$context->path])){  // Just a button to return to normal
        $options = array();
        $options['id'] = $courseid;
        $options['sesskey'] = sesskey();
        $options['switchrole'] = 0;

        return print_single_button($CFG->wwwroot.'/course/view.php', $options,
                                   get_string('switchrolereturn'), 'post', '_self', true);
    }

    if (has_capability('moodle/role:switchroles', $context)) {
        if (!$roles = get_switchable_roles($context)) {
            return '';   // Nothing to show!
        }
        // unset default user role - it would not work
        unset($roles[$CFG->guestroleid]);
        return popup_form($CFG->wwwroot.'/course/view.php?id='.$courseid.'&amp;sesskey='.sesskey().'&amp;switchrole=',
                          $roles, 'switchrole', '', get_string('switchroleto'), 'switchrole', get_string('switchroleto'), true);
    }

    return '';
}


/**
 * Returns a turn edit on/off button for course in a self contained form.
 * Used to be an icon, but it's now a simple form button
 *
 * @global object
 * @global object
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

    return "<form $CFG->frametarget method=\"get\" action=\"$CFG->wwwroot/my/index.php\">".
           "<div>".
           "<input type=\"hidden\" name=\"edit\" value=\"$edit\" />".
           "<input type=\"submit\" value=\"$string\" /></div></form>";
}

/**
 * Returns a turn edit on/off button for tag in a self contained form.
 *
 * @global object
 * @global object
 * @param string $tagid The ID attribute
 * @return string
 */
function update_tag_button($tagid) {

    global $CFG, $USER;

    if (!empty($USER->editing)) {
        $string = get_string('turneditingoff');
        $edit = '0';
    } else {
        $string = get_string('turneditingon');
        $edit = '1';
    }

    return "<form $CFG->frametarget method=\"get\" action=\"$CFG->wwwroot/tag/index.php\">".
           "<div>".
           "<input type=\"hidden\" name=\"edit\" value=\"$edit\" />".
           "<input type=\"hidden\" name=\"id\" value=\"$tagid\" />".
           "<input type=\"submit\" value=\"$string\" /></div></form>";
}

/**
 * Prints the 'update this xxx' button that appears on module pages.
 *
 * @global object
 * @global object
 * @uses CONTEXT_MODULE
 * @param string $cmid the course_module id.
 * @param string $ignored not used any more. (Used to be courseid.)
 * @param string $string the module name - get_string('modulename', 'xxx')
 * @return string the HTML for the button, if this user has permission to edit it, else an empty string.
 */
function update_module_button($cmid, $ignored, $string) {
    global $CFG, $USER;

    if (has_capability('moodle/course:manageactivities', get_context_instance(CONTEXT_MODULE, $cmid))) {
        $string = get_string('updatethis', '', $string);

        return "<form $CFG->frametarget method=\"get\" action=\"$CFG->wwwroot/course/mod.php\" onsubmit=\"this.target='{$CFG->framename}'; return true\">".//hack to allow edit on framed resources
               "<div>".
               "<input type=\"hidden\" name=\"update\" value=\"$cmid\" />".
               "<input type=\"hidden\" name=\"return\" value=\"true\" />".
               "<input type=\"hidden\" name=\"sesskey\" value=\"".sesskey()."\" />".
               "<input type=\"submit\" value=\"$string\" /></div></form>";
    } else {
        return '';
    }
}

/**
 * Prints the editing button on search results listing
 * For bulk move courses to another category
 *
 * @global object
 * @global object
 * @param string $search  The search string
 * @param string $page
 * @param string $perpage
 * @return string HTML form element
 */
function update_categories_search_button($search,$page,$perpage) {
    global $CFG, $PAGE;

    // not sure if this capability is the best  here
    if (has_capability('moodle/category:manage', get_context_instance(CONTEXT_SYSTEM))) {
        if ($PAGE->user_is_editing()) {
            $string = get_string("turneditingoff");
            $edit = "off";
            $perpage = 30;
        } else {
            $string = get_string("turneditingon");
            $edit = "on";
        }

        return "<form $CFG->frametarget method=\"get\" action=\"$CFG->wwwroot/course/search.php\">".
               '<div>'.
               "<input type=\"hidden\" name=\"edit\" value=\"$edit\" />".
               "<input type=\"hidden\" name=\"sesskey\" value=\"".sesskey()."\" />".
               "<input type=\"hidden\" name=\"search\" value=\"".s($search, true)."\" />".
               "<input type=\"hidden\" name=\"page\" value=\"$page\" />".
               "<input type=\"hidden\" name=\"perpage\" value=\"$perpage\" />".
               "<input type=\"submit\" value=\"".s($string)."\" /></div></form>";
    }
}

/**
 * Returns a small popup menu of course activity modules
 *
 * Given a course and a (current) coursemodule
 * his function returns a small popup menu with all the
 * course activity modules in it, as a navigation menu
 * The data is taken from the serialised array stored in
 * the course record
 *
 * @global object
 * @global object
 * @global object
 * @global object
 * @uses CONTEXT_COURSE
 * @param object $course A {@link $COURSE} object.
 * @param object $cm A {@link $COURSE} object.
 * @param string $targetwindow The target window attribute to us
 * @return string
 */
function navmenu($course, $cm=NULL, $targetwindow='self') {
    global $CFG, $THEME, $USER, $DB, $OUTPUT;
    require_once($CFG->dirroot . '/course/lib.php'); // Required for get_fast_modinfo

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

    $modinfo = get_fast_modinfo($course);
    $context = get_context_instance(CONTEXT_COURSE, $course->id);

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
    $menustyle = array();

    $sections = $DB->get_records('course_sections', array('course'=>$course->id), 'section', 'section,visible,summary');

    if (!empty($THEME->makenavmenulist)) {   /// A hack to produce an XHTML navmenu list for use in themes
        $THEME->navmenulist = navmenulist($course, $sections, $modinfo, $strsection, $strjumpto, $width, $cm);
    }

    foreach ($modinfo->cms as $mod) {
        if ($mod->modname == 'label') {
            continue;
        }

        if ($mod->sectionnum > $course->numsections) {   /// Don't show excess hidden sections
            break;
        }

        if (!$mod->uservisible) { // do not icnlude empty sections at all
            continue;
        }

        if ($mod->sectionnum > 0 and $section != $mod->sectionnum) {
            $thissection = $sections[$mod->sectionnum];

            if ($thissection->visible or !$course->hiddensections or
                has_capability('moodle/course:viewhiddensections', $context)) {
                $thissection->summary = strip_tags(format_string($thissection->summary,true));
                if ($course->format == 'weeks' or empty($thissection->summary)) {
                    $menu[] = '--'.$strsection ." ". $mod->sectionnum;
                } else {
                    if (strlen($thissection->summary) < ($width-3)) {
                        $menu[] = '--'.$thissection->summary;
                    } else {
                        $menu[] = '--'.substr($thissection->summary, 0, $width).'...';
                    }
                }
                $section = $mod->sectionnum;
            } else {
                // no activities from this hidden section shown
                continue;
            }
        }

        $url = $mod->modname.'/view.php?id='. $mod->id;
        if ($flag) { // the current mod is the "next" mod
            $nextmod = $mod;
            $flag = false;
        }
        $localname = $mod->name;
        if ($cm == $mod->id) {
            $selected = $url;
            $selectmod = $mod;
            $backmod = $previousmod;
            $flag = true; // set flag so we know to use next mod for "next"
            $localname = $strjumpto;
            $strjumpto = '';
        } else {
            $localname = strip_tags(format_string($localname,true));
            $tl=textlib_get_instance();
            if ($tl->strlen($localname) > ($width+5)) {
                $localname = $tl->substr($localname, 0, $width).'...';
            }
            if (!$mod->visible) {
                $localname = '('.$localname.')';
            }
        }
        $menu[$url] = $localname;
        if (empty($THEME->navmenuiconshide)) {
            $menustyle[$url] = 'style="background-image: url('.$OUTPUT->mod_icon_url('icon', $mod->modname) . ');"';  // Unfortunately necessary to do this here
        }
        $previousmod = $mod;
    }
    //Accessibility: added Alt text, replaced &gt; &lt; with 'silent' character and 'accesshide' text.

    if ($selectmod and has_capability('coursereport/log:view', $context)) {
        $logstext = get_string('alllogs');
        $logslink = '<li>'."\n".'<a title="'.$logstext.'" '.
                    $CFG->frametarget.'onclick="this.target=\''.$CFG->framename.'\';"'.' href="'.
                    $CFG->wwwroot.'/course/report/log/index.php?chooselog=1&amp;user=0&amp;date=0&amp;id='.
                       $course->id.'&amp;modid='.$selectmod->id.'">'.
                    '<img class="icon log" src="'.$OUTPUT->old_icon_url('i/log') . '" alt="'.$logstext.'" /></a>'."\n".'</li>';

    }
    if ($backmod) {
        $backtext= get_string('activityprev', 'access');
        $backmod = '<li><form action="'.$CFG->wwwroot.'/mod/'.$backmod->modname.'/view.php" '.
                   'onclick="this.target=\''.$CFG->framename.'\';"'.'><fieldset class="invisiblefieldset">'.
                   '<input type="hidden" name="id" value="'.$backmod->id.'" />'.
                   '<button type="submit" title="'.$backtext.'">'.link_arrow_left($backtext, $url='', $accesshide=true).
                   '</button></fieldset></form></li>';
    }
    if ($nextmod) {
        $nexttext= get_string('activitynext', 'access');
        $nextmod = '<li><form action="'.$CFG->wwwroot.'/mod/'.$nextmod->modname.'/view.php"  '.
                   'onclick="this.target=\''.$CFG->framename.'\';"'.'><fieldset class="invisiblefieldset">'.
                   '<input type="hidden" name="id" value="'.$nextmod->id.'" />'.
                   '<button type="submit" title="'.$nexttext.'">'.link_arrow_right($nexttext, $url='', $accesshide=true).
                   '</button></fieldset></form></li>';
    }

    return '<div class="navigation">'."\n".'<ul>'.$logslink . $backmod .
            '<li>'.popup_form($CFG->wwwroot .'/mod/', $menu, 'navmenupopup', $selected, $strjumpto,
                       '', '', true, $targetwindow, '', $menustyle).'</li>'.
            $nextmod . '</ul>'."\n".'</div>';
}

/**
 * Returns a popup menu with course activity modules
 *
 * Given a course
 * This function returns a small popup menu with all the
 * course activity modules in it, as a navigation menu
 * outputs a simple list structure in XHTML
 * The data is taken from the serialised array stored in
 * the course record
 *
 * @todo Finish documenting this function
 *
 * @global object
 * @uses CONTEXT_COURSE
 * @param course $course A {@link $COURSE} object.
 * @param string $sections
 * @param string $modinfo
 * @param string $strsection
 * @param string $strjumpto
 * @param int $width
 * @param string $cmid
 * @return string The HTML block
 */
function navmenulist($course, $sections, $modinfo, $strsection, $strjumpto, $width=50, $cmid=0) {

    global $CFG;

    $section = -1;
    $url = '';
    $menu = array();
    $doneheading = false;

    $coursecontext = get_context_instance(CONTEXT_COURSE, $course->id);

    $menu[] = '<ul class="navmenulist"><li class="jumpto section"><span>'.$strjumpto.'</span><ul>';
    foreach ($modinfo->cms as $mod) {
        if ($mod->modname == 'label') {
            continue;
        }

        if ($mod->sectionnum > $course->numsections) {   /// Don't show excess hidden sections
            break;
        }

        if (!$mod->uservisible) { // do not icnlude empty sections at all
            continue;
        }

        if ($mod->sectionnum >= 0 and $section != $mod->sectionnum) {
            $thissection = $sections[$mod->sectionnum];

            if ($thissection->visible or !$course->hiddensections or
                      has_capability('moodle/course:viewhiddensections', $coursecontext)) {
                $thissection->summary = strip_tags(format_string($thissection->summary,true));
                if (!$doneheading) {
                    $menu[] = '</ul></li>';
                }
                if ($course->format == 'weeks' or empty($thissection->summary)) {
                    $item = $strsection ." ". $mod->sectionnum;
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

                $section = $mod->sectionnum;
            } else {
                // no activities from this hidden section shown
                continue;
            }
        }

        $url = $mod->modname .'/view.php?id='. $mod->id;
        $mod->name = strip_tags(format_string(urldecode($mod->name),true));
        if (strlen($mod->name) > ($width+5)) {
            $mod->name = substr($mod->name, 0, $width).'...';
        }
        if (!$mod->visible) {
            $mod->name = '('.$mod->name.')';
        }
        $class = 'activity '.$mod->modname;
        $class .= ($cmid == $mod->id) ? ' selected' : '';
        $menu[] = '<li class="'.$class.'">'.
                  '<img src="'.$OUTPUT->mod_icon_url('icon', $mod->modname) . '" alt="" />'.
                  '<a href="'.$CFG->wwwroot.'/mod/'.$url.'">'.$mod->name.'</a></li>';
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
 * @param boolean $return If set to true returns rather than echo's
 * @return string|bool Depending on value of $return
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

    // Build or print result
    $result='';
    // Note: There should probably be a fieldset around these fields as they are
    // clearly grouped. However this causes problems with display. See Mozilla
    // bug 474415
    $result.='<label class="accesshide" for="menu'.$day.'">'.get_string('day','form').'</label>';
    $result.=choose_from_menu($days,   $day,   $currentdate['mday'], '', '', '0', true);
    $result.='<label class="accesshide" for="menu'.$month.'">'.get_string('month','form').'</label>';
    $result.=choose_from_menu($months, $month, $currentdate['mon'],  '', '', '0', true);
    $result.='<label class="accesshide" for="menu'.$year.'">'.get_string('year','form').'</label>';
    $result.=choose_from_menu($years,  $year,  $currentdate['year'], '', '', '0', true);

    if ($return) {
        return $result;
    } else {
        echo $result;
    }
}

/**
 * Prints form items with the names $hour and $minute
 *
 * @param string $hour  fieldname
 * @param string $minute  fieldname
 * @param int $currenttime A default timestamp in GMT
 * @param int $step minute spacing
 * @param boolean $return If set to true returns rather than echo's
 * @return string|bool Depending on value of $return 
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

    // Build or print result
    $result='';
    // Note: There should probably be a fieldset around these fields as they are
    // clearly grouped. However this causes problems with display. See Mozilla
    // bug 474415
    $result.='<label class="accesshide" for="menu'.$hour.'">'.get_string('hour','form').'</label>';
    $result.=choose_from_menu($hours,   $hour,   $currentdate['hours'],   '','','0',true);
    $result.='<label class="accesshide" for="menu'.$minute.'">'.get_string('minute','form').'</label>';
    $result.=choose_from_menu($minutes, $minute, $currentdate['minutes'], '','','0',true);

    if ($return) {
        return $result;
    } else {
        echo $result;
    }
}

/**
 * Prints time limit value selector
 *
 * Uses {@link choose_from_menu()} to generate HTML
 * @see choose_from_menu()
 *
 * @global object
 * @param int $timelimit default
 * @param string $unit
 * @param string $name
 * @param boolean $return If set to true returns rather than echo's
 * @return string|bool Depending on value of $return
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
 * @todo Finish documenting this function
 *
 * @global object
 * @param int $courseid The course ID
 * @param string $name 
 * @param string $current 
 * @param boolean $includenograde Include those with no grades
 * @param boolean $return If set to true returns rather than echo's
 * @return string|bool Depending on value of $return
 */
function print_grade_menu($courseid, $name, $current, $includenograde=true, $return=false) {

    global $CFG, $OUTPUT;

    $output = '';
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
    $output .= choose_from_menu($grades, $name, $current, '', '', 0, true);

    $linkobject = '<span class="helplink"><img class="iconhelp" alt="'.$strscales.'" src="'.$OUTPUT->old_icon_url('help') . '" /></span>';
    $output .= link_to_popup_window ('/course/scales.php?id='. $courseid .'&amp;list=true', 'ratingscales',
                                     $linkobject, 400, 500, $strscales, 'none', true);

    if ($return) {
        return $output;
    } else {
        echo $output;
    }
}

/**
 * Prints a scale menu (as part of an existing form) including help button
 * Just like {@link print_grade_menu()} but without the numeric grades
 *
 * @global object
 * @param int $courseid ?
 * @param string $name ?
 * @param string $current ?
 * @param boolean $return If set to true returns rather than echo's
 * @return string|bool Depending on value of $return
 */
function print_scale_menu($courseid, $name, $current, $return=false) {

    global $CFG, $OUTPUT;

    $output = '';
    $strscales = get_string('scales');
    $output .= choose_from_menu(get_scales_menu($courseid), $name, $current, '', '', 0, true);

    $linkobject = '<span class="helplink"><img class="iconhelp" alt="'.$strscales.'" src="'.$OUTPUT->old_icon_url('help') . '" /></span>';
    $output .= link_to_popup_window ('/course/scales.php?id='. $courseid .'&amp;list=true', 'ratingscales',
                                     $linkobject, 400, 500, $strscales, 'none', true);
    if ($return) {
        return $output;
    } else {
        echo $output;
    }
}

/**
 * Prints a help button about a scale
 *
 * @global object
 * @param id $courseid 
 * @param object $scale
 * @param boolean $return If set to true returns rather than echo's
 * @return string|bool Depending on value of $return
 */
function print_scale_menu_helpbutton($courseid, $scale, $return=false) {

    global $OUTPUT;

    $output = '';
    $strscales = get_string('scales');

    $linkobject = '<span class="helplink"><img class="iconhelp" alt="'.$scale->name.'" src="'.$OUTPUT->old_icon_url('help') . '" /></span>';
    $output .= link_to_popup_window ('/course/scales.php?id='. $courseid .'&amp;list=true&amp;scaleid='. $scale->id, 'ratingscale',
                                     $linkobject, 400, 500, $scale->name, 'none', true);
    if ($return) {
        return $output;
    } else {
        echo $output;
    }
}

/**
 * Print an error to STDOUT and exit with a non-zero code. For commandline scripts.
 * Default errorcode is 1.
 *
 * Very useful for perl-like error-handling:
 *
 * do_somethting() or mdie("Something went wrong");
 *
 * @param string  $msg       Error message
 * @param integer $errorcode Error code to emit
 */
function mdie($msg='', $errorcode=1) {
    trigger_error($msg);
    exit($errorcode);
}

/**
 * Returns a string of html with an image of a help icon linked to a help page on a number of help topics.
 * Should be used only with htmleditor or textarea.
 *
 * @global object
 * @global object
 * @param mixed $helptopics variable amount of params accepted. Each param may be a string or an array of arguments for
 *                  helpbutton.
 * @return string Link to help button
 */
function editorhelpbutton(){
    global $CFG, $SESSION, $OUTPUT;
    $items = func_get_args();
    $i = 1;
    $urlparams = array();
    $titles = array();
    foreach ($items as $item){
        if (is_array($item)){
            $urlparams[] = "keyword$i=".urlencode($item[0]);
            $urlparams[] = "title$i=".urlencode($item[1]);
            if (isset($item[2])){
                $urlparams[] = "module$i=".urlencode($item[2]);
            }
            $titles[] = trim($item[1], ". \t");
        } else if (is_string($item)) {
            $urlparams[] = "button$i=".urlencode($item);
            switch ($item) {
                case 'reading' :
                    $titles[] = get_string("helpreading");
                    break;
                case 'writing' :
                    $titles[] = get_string("helpwriting");
                    break;
                case 'questions' :
                    $titles[] = get_string("helpquestions");
                    break;
                case 'emoticons2' :
                    $titles[] = get_string("helpemoticons");
                    break;
                case 'richtext2' :
                    $titles[] = get_string('helprichtext');
                    break;
                case 'text2' :
                    $titles[] = get_string('helptext');
                    break;
                default :
                    print_error('unknownhelp', '', '', $item);
            }
        }
        $i++;
    }
    if (count($titles)>1){
        //join last two items with an 'and'
        $a = new object();
        $a->one = $titles[count($titles) - 2];
        $a->two = $titles[count($titles) - 1];
        $titles[count($titles) - 2] = get_string('and', '', $a);
        unset($titles[count($titles) - 1]);
    }
    $alttag = join (', ', $titles);

    $paramstring = join('&', $urlparams);
    $linkobject = '<img alt="'.$alttag.'" class="iconhelp" src="'.$OUTPUT->old_icon_url('help') . '" />';
    return link_to_popup_window(s('/lib/form/editorhelp.php?'.$paramstring), 'popup', $linkobject, 400, 500, $alttag, 'none', true);
}

/**
 * Print a help button.
 *
 * @global object
 * @global object
 * @uses DEBUG_DEVELOPER
 * @param string $page  The keyword that defines a help page
 * @param string $title The title of links, rollover tips, alt tags etc
 *           'Help with' (or the language equivalent) will be prefixed and '...' will be stripped.
 * @param string $module Which module is the page defined in
 * @param mixed $image Use a help image for the link?  (true/false/"both")
 * @param boolean $linktext If true, display the title next to the help icon.
 * @param string $text If defined then this text is used in the page, and
 *           the $page variable is ignored.
 * @param boolean $return If true then the output is returned as a string, if false it is printed to the current page.
 * @param string $imagetext The full text for the helpbutton icon. If empty use default help.gif
 * @return string|void Depending on value of $return
 */
function helpbutton($page, $title, $module='moodle', $image=true, $linktext=false, $text='', $return=false,
                     $imagetext='') {
    global $CFG, $COURSE, $OUTPUT;

    //warning if ever $text parameter is used
    //$text option won't work properly because the text needs to be always cleaned and,
    // when cleaned... html tags always break, so it's unusable.
    if ( isset($text) && $text!='') {
        debugging('Warning: it\'s not recommended to use $text parameter in helpbutton ($page=' . $page . ', $module=' . $module . ') function', DEBUG_DEVELOPER);
    }

    // Catch references to the old text.html and emoticons.html help files that
    // were renamed in MDL-13233.
    if (in_array($page, array('text', 'emoticons', 'richtext'))) {
        $oldname = $page;
        $page .= '2';
        debugging("You are referring to the old help file '$oldname'. " .
                "This was renamed to '$page' becuase of MDL-13233. " .
                "Please update your code.", DEBUG_DEVELOPER);
    }

    if ($module == '') {
        $module = 'moodle';
    }

    if ($title == '' && $linktext == '') {
        debugging('Error in call to helpbutton function: at least one of $title and $linktext is required');
    }

    // Warn users about new window for Accessibility
    $tooltip = get_string('helpprefix2', '', trim($title, ". \t")) .' ('.get_string('newwindow').')';

    $linkobject = '';

    if ($image) {
        if ($linktext) {
            // MDL-7469 If text link is displayed with help icon, change to alt to "help with this".
            $linkobject .= $title.'&nbsp;';
            $tooltip = get_string('helpwiththis');
        }
        if ($imagetext) {
            $linkobject .= $imagetext;
        } else {
            $linkobject .= '<img class="iconhelp" alt="'.s(strip_tags($tooltip)).'" src="'.
                $OUTPUT->old_icon_url('help') . '" />';
        }
    } else {
        $linkobject .= $tooltip;
    }

    // fix for MDL-7734
    if ($text) {
        $url = '/help.php?text='. s(urlencode($text));
    } else {
        $url = '/help.php?module='. $module .'&amp;file='. $page .'.html';
        // fix for MDL-7734
        if (!empty($COURSE->lang)) {
            $url .= '&amp;forcelang=' . $COURSE->lang;
        }
    }

    $link = '<span class="helplink">' . link_to_popup_window($url, 'popup',
            $linkobject, 400, 500, $tooltip, 'none', true) . '</span>';

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
 *
 * @todo Finish documenting this function
 *
 * @global object
 * @global object
 * @param string $form ?
 * @param string $field ?
 * @param boolean $return If true then the output is returned as a string, if false it is printed to the current page.
 * @return string|void Depending on value of $return
 */
function emoticonhelpbutton($form, $field, $return = false) {

    global $SESSION, $OUTPUT;

    $SESSION->inserttextform = $form;
    $SESSION->inserttextfield = $field;
    $imagetext = '<img src="' . $OUTPUT->old_icon_url('s/smiley') . '" alt="" class="emoticon" style="margin-left:3px; padding-right:1px;width:15px;height:15px;" />';
    $help = helpbutton('emoticons2', get_string('helpemoticons'), 'moodle', true, true, '', true, $imagetext);
    if (!$return){
        echo $help;
    } else {
        return $help;
    }
}

/**
 * Print a help button.
 *
 * Prints a special help button for html editors (htmlarea in this case)
 *
 * @todo Write code into this function! detect current editor and print correct info
 * @global object
 * @return string Only returns an empty string at the moment
 */
function editorshortcutshelpbutton() {

    global $CFG;
    //TODO: detect current editor and print correct info
/*    $imagetext = '<img src="' . $CFG->httpswwwroot . '/lib/editor/htmlarea/images/kbhelp.gif" alt="'.
        get_string('editorshortcutkeys').'" class="iconkbhelp" />';

    return helpbutton('editorshortcuts', get_string('editorshortcutkeys'), 'moodle', true, false, '', true, $imagetext);*/
    return '';
}

/**
 * Print a message and exit.
 *
 * @global object
 * @global object Apparently not used in this function
 * @global object
 * @global object
 * @global object
 * @uses CLI_SCRIPT
 * @param string $message The message to print in the notice
 * @param string $link The link to use for the continue button
 * @param object $course A course object
 * @return void This function simply exits
 */
function notice ($message, $link='', $course=NULL) {
    global $CFG, $SITE, $THEME, $COURSE, $PAGE;

    $message = clean_text($message);   // In case nasties are in here

    if (CLI_SCRIPT) {
        echo("!!$message!!\n");
        exit(1); // no success
    }

    if (!$PAGE->headerprinted) {
        //header not yet printed
        print_header(get_string('notice'));
    } else {
        print_container_end_all(false, $THEME->open_header_containers);
    }

    print_box($message, 'generalbox', 'notice');
    print_continue($link);

    if (empty($course)) {
        print_footer($COURSE);
    } else {
        print_footer($course);
    }
    exit(1); // general error code
}

/**
 * Print a message along with "Yes" and "No" links for the user to continue.
 *
 * @global object
 * @param string $message The text to display
 * @param string $linkyes The link to take the user to if they choose "Yes"
 * @param string $linkno The link to take the user to if they choose "No"
 * @param string $optionyes The yes option to show on the notice
 * @param string $optionsno The no option to show
 * @param string $methodyes Form action method to use if yes [post, get]
 * @param string $methodno Form action method to use if no [post, get]
 * @return void Output is echo'd
 */
function notice_yesno ($message, $linkyes, $linkno, $optionsyes=NULL, $optionsno=NULL, $methodyes='post', $methodno='post') {

    global $CFG;

    $message = clean_text($message);
    $linkyes = clean_text($linkyes);
    $linkno = clean_text($linkno);

    print_box_start('generalbox', 'notice');
    echo '<p>'. $message .'</p>';
    echo '<div class="buttons">';
    print_single_button($linkyes, $optionsyes, get_string('yes'), $methodyes, $CFG->framename);
    print_single_button($linkno,  $optionsno,  get_string('no'),  $methodno,  $CFG->framename);
    echo '</div>';
    print_box_end();
}

/**
 * Redirects the user to another page, after printing a notice
 *
 * This function calls the OUTPUT redirect method, echo's the output
 * and then dies to ensure nothing else happens.
 *
 * <strong>Good practice:</strong> You should call this method before starting page
 * output by using any of the OUTPUT methods.
 *
 * @global object
 * @global object
 * @global object
 * @uses $_COOKIE
 * @uses DEBUG_DEVELOPER
 * @uses DEBUG_ALL
 * @param string $url The URL to redirect to
 * @param string $message The message to display to the user
 * @param int $delay The delay before redirecting
 * @return void
 */
function redirect($url, $message='', $delay=-1) {
    global $OUTPUT, $SESSION, $CFG;

    if (!empty($CFG->usesid) && !isset($_COOKIE[session_name()])) {
       $url = $SESSION->sid_process_url($url);
    }

    $lasterror = error_get_last();
    $debugdisableredirect = defined('DEBUGGING_PRINTED') ||
            (!empty($CFG->debugdisplay) && !empty($lasterror) && ($lasterror['type'] & DEBUG_DEVELOPER));

    $usingmsg = false;
    if (!empty($message)) {
        if ($delay === -1 || !is_numeric($delay)) {
            $delay = 3;
        }
        $message = clean_text($message);
    } else {
        $message = get_string('pageshouldredirect');
        $delay = 0;
        // We are going to try to use a HTTP redirect, so we need a full URL.
        if (!preg_match('|^[a-z]+:|', $url)) {
            // Get host name http://www.wherever.com
            $hostpart = preg_replace('|^(.*?[^:/])/.*$|', '$1', $CFG->wwwroot);
            if (preg_match('|^/|', $url)) {
                // URLs beginning with / are relative to web server root so we just add them in
                $url = $hostpart.$url;
            } else {
                // URLs not beginning with / are relative to path of current script, so add that on.
                $url = $hostpart.preg_replace('|\?.*$|','',me()).'/../'.$url;
            }
            // Replace all ..s
            while (true) {
                $newurl = preg_replace('|/(?!\.\.)[^/]*/\.\./|', '/', $url);
                if ($newurl == $url) {
                    break;
                }
                $url = $newurl;
            }
        }
    }

    if (defined('MDL_PERF') || (!empty($CFG->perfdebug) and $CFG->perfdebug > 7)) {
        if (defined('MDL_PERFTOLOG') && !function_exists('register_shutdown_function')) {
            $perf = get_performance_info();
            error_log("PERF: " . $perf['txt']);
        }
    }

    $encodedurl = preg_replace("/\&(?![a-zA-Z0-9#]{1,8};)/", "&amp;", $url);
    $encodedurl = preg_replace('/^.*href="([^"]*)".*$/', "\\1", clean_text('<a href="'.$encodedurl.'" />'));

    if ($delay == 0 && !$debugdisableredirect && !headers_sent()) {
        //302 might not work for POST requests, 303 is ignored by obsolete clients.
        @header($_SERVER['SERVER_PROTOCOL'] . ' 303 See Other');
        @header('Location: '.$url);
    }

    // Include a redirect message, even with a HTTP redirect, because that is recommended practice.
    $CFG->docroot = false; // to prevent the link to moodle docs from being displayed on redirect page.
    echo $OUTPUT->redirect_message($encodedurl, $message, $delay, $debugdisableredirect);
    exit;
}

/**
 * Given an email address, this function will return an obfuscated version of it
 *
 * @param string $email The email address to obfuscate
 * @return string The obfuscated email address
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
 * @return string The obfuscated text
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
 * @return string The obfuscated mailto link
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
 * @param mixed $baseurl If this  is a string then it is the url which will be appended with $pagevar, an equals sign and the page number.
 *                          If this is a moodle_url object then the pagevar param will be replaced by the page no, for each page.
 * @param string $pagevar This is the variable name that you use for the page number in your code (ie. 'tablepage', 'blogpage', etc)
 * @param bool $nocurr do not display the current page as a link
 * @param bool $return whether to return an output string or echo now
 * @return bool|string depending on $result
 */
function print_paging_bar($totalcount, $page, $perpage, $baseurl, $pagevar='page',$nocurr=false, $return=false) {
    $maxdisplay = 18;
    $output = '';

    if ($totalcount > $perpage) {
        $output .= '<div class="paging">';
        $output .= get_string('page') .':';
        if ($page > 0) {
            $pagenum = $page - 1;
            if (!is_a($baseurl, 'moodle_url')){
                $output .= '&nbsp;(<a class="previous" href="'. $baseurl . $pagevar .'='. $pagenum .'">'. get_string('previous') .'</a>)&nbsp;';
            } else {
                $output .= '&nbsp;(<a class="previous" href="'. $baseurl->out(false, array($pagevar => $pagenum)).'">'. get_string('previous') .'</a>)&nbsp;';
            }
        }
        if ($perpage > 0) {
            $lastpage = ceil($totalcount / $perpage);
        } else {
            $lastpage = 1;
        }
        if ($page > 15) {
            $startpage = $page - 10;
            if (!is_a($baseurl, 'moodle_url')){
                $output .= '&nbsp;<a href="'. $baseurl . $pagevar .'=0">1</a>&nbsp;...';
            } else {
                $output .= '&nbsp;<a href="'. $baseurl->out(false, array($pagevar => 0)).'">1</a>&nbsp;...';
            }
        } else {
            $startpage = 0;
        }
        $currpage = $startpage;
        $displaycount = $displaypage = 0;
        while ($displaycount < $maxdisplay and $currpage < $lastpage) {
            $displaypage = $currpage+1;
            if ($page == $currpage && empty($nocurr)) {
                $output .= '&nbsp;&nbsp;'. $displaypage;
            } else {
                if (!is_a($baseurl, 'moodle_url')){
                    $output .= '&nbsp;&nbsp;<a href="'. $baseurl . $pagevar .'='. $currpage .'">'. $displaypage .'</a>';
                } else {
                    $output .= '&nbsp;&nbsp;<a href="'. $baseurl->out(false, array($pagevar => $currpage)).'">'. $displaypage .'</a>';
                }

            }
            $displaycount++;
            $currpage++;
        }
        if ($currpage < $lastpage) {
            $lastpageactual = $lastpage - 1;
            if (!is_a($baseurl, 'moodle_url')){
                $output .= '&nbsp;...<a href="'. $baseurl . $pagevar .'='. $lastpageactual .'">'. $lastpage .'</a>&nbsp;';
            } else {
                $output .= '&nbsp;...<a href="'. $baseurl->out(false, array($pagevar => $lastpageactual)).'">'. $lastpage .'</a>&nbsp;';
            }
        }
        $pagenum = $page + 1;
        if ($pagenum != $displaypage) {
            if (!is_a($baseurl, 'moodle_url')){
                $output .= '&nbsp;&nbsp;(<a class="next" href="'. $baseurl . $pagevar .'='. $pagenum .'">'. get_string('next') .'</a>)';
            } else {
                $output .= '&nbsp;&nbsp;(<a class="next" href="'. $baseurl->out(false, array($pagevar => $pagenum)) .'">'. get_string('next') .'</a>)';
            }
        }
        $output .= '</div>';
    }

    if ($return) {
        return $output;
    }

    echo $output;
    return true;
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
 * @todo Remove this deprecated function when no longer used
 * @deprecated since Moodle 2.0 - use $PAGE->pagetype instead of the .
 *
 * @global object
 * @param string $getid used to return $PAGE->pagetype.
 * @param string $getclass used to return $PAGE->legacyclass.
 */
function page_id_and_class(&$getid, &$getclass) {
    global $PAGE;
    debugging('Call to deprecated function page_id_and_class. Please use $PAGE->pagetype instead.', DEBUG_DEVELOPER);
    $getid = $PAGE->pagetype;
    $getclass = $PAGE->legacyclass;
}

/**
 * Prints a maintenance message from $CFG->maintenance_message or default if empty
 * @return void 
 */
function print_maintenance_message() {
    global $CFG, $SITE, $PAGE;

    $PAGE->set_pagetype('maintenance-message');
    print_header(strip_tags($SITE->fullname), $SITE->fullname, 'home');
    print_heading(get_string('sitemaintenance', 'admin'));
    if (isset($CFG->maintenance_message) and !html_is_blank($CFG->maintenance_message)) {
        print_box_start('maintenance_message generalbox boxwidthwide boxaligncenter');
        echo $CFG->maintenance_message;
        print_box_end();
    }
    print_footer();
    die;
}

/**
 * Adjust the list of allowed tags based on $CFG->allowobjectembed and user roles (admin)
 *
 * @global object
 * @global string
 * @return void
 */
function adjust_allowed_tags() {

    global $CFG, $ALLOWED_TAGS;

    if (!empty($CFG->allowobjectembed)) {
        $ALLOWED_TAGS .= '<embed><object>';
    }
}

/**
 * A class for tabs, Some code to print tabs
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package moodlecore
 */
class tabobject {
    /**
     * @var string
     */
    var $id;
    var $link;
    var $text;
    /**
     * @var bool
     */
    var $linkedwhenselected;

    /** 
     * A constructor just because I like constructors
     * 
     * @param string $id
     * @param string $link
     * @param string $text
     * @param string $title
     * @param bool $linkedwhenselected
     */
    function tabobject ($id, $link='', $text='', $title='', $linkedwhenselected=false) {
        $this->id   = $id;
        $this->link = $link;
        $this->text = $text;
        $this->title = $title ? $title : $text;
        $this->linkedwhenselected = $linkedwhenselected;
    }
}



/**
 * Returns a string containing a nested list, suitable for formatting into tabs with CSS.
 *
 * @global object
 * @param array $tabrows An array of rows where each row is an array of tab objects
 * @param string $selected  The id of the selected tab (whatever row it's on)
 * @param array  $inactive  An array of ids of inactive tabs that are not selectable.
 * @param array  $activated An array of ids of other tabs that are currently activated
 * @param bool $return If true output is returned rather then echo'd
 **/
function print_tabs($tabrows, $selected=NULL, $inactive=NULL, $activated=NULL, $return=false) {
    global $CFG;

/// $inactive must be an array
    if (!is_array($inactive)) {
        $inactive = array();
    }

/// $activated must be an array
    if (!is_array($activated)) {
        $activated = array();
    }

/// Convert the tab rows into a tree that's easier to process
    if (!$tree = convert_tabrows_to_tree($tabrows, $selected, $inactive, $activated)) {
        return false;
    }

/// Print out the current tree of tabs (this function is recursive)

    $output = convert_tree_to_html($tree);

    $output = "\n\n".'<div class="tabtree">'.$output.'</div><div class="clearer"> </div>'."\n\n";

/// We're done!

    if ($return) {
        return $output;
    }
    echo $output;
}

/**
 * Converts a nested array tree into HTML ul:li [recursive]
 *
 * @param array $tree A tree array to convert
 * @param int $row Used in identifing the iteration level and in ul classes
 * @return string HTML structure
 */
function convert_tree_to_html($tree, $row=0) {

    $str = "\n".'<ul class="tabrow'.$row.'">'."\n";

    $first = true;
    $count = count($tree);

    foreach ($tree as $tab) {
        $count--;   // countdown to zero

        $liclass = '';

        if ($first && ($count == 0)) {   // Just one in the row
            $liclass = 'first last';
            $first = false;
        } else if ($first) {
            $liclass = 'first';
            $first = false;
        } else if ($count == 0) {
            $liclass = 'last';
        }

        if ((empty($tab->subtree)) && (!empty($tab->selected))) {
            $liclass .= (empty($liclass)) ? 'onerow' : ' onerow';
        }

        if ($tab->inactive || $tab->active || $tab->selected) {
            if ($tab->selected) {
                $liclass .= (empty($liclass)) ? 'here selected' : ' here selected';
            } else if ($tab->active) {
                $liclass .= (empty($liclass)) ? 'here active' : ' here active';
            }
        }

        $str .= (!empty($liclass)) ? '<li class="'.$liclass.'">' : '<li>';

        if ($tab->inactive || $tab->active || ($tab->selected && !$tab->linkedwhenselected)) {
            // The a tag is used for styling
            $str .= '<a class="nolink"><span>'.$tab->text.'</span></a>';
        } else {
            $str .= '<a href="'.$tab->link.'" title="'.$tab->title.'"><span>'.$tab->text.'</span></a>';
        }

        if (!empty($tab->subtree)) {
            $str .= convert_tree_to_html($tab->subtree, $row+1);
        } else if ($tab->selected) {
            $str .= '<div class="tabrow'.($row+1).' empty">&nbsp;</div>'."\n";
        }

        $str .= ' </li>'."\n";
    }
    $str .= '</ul>'."\n";

    return $str;
}

/**
 * Convert nested tabrows to a nested array
 *
 * @param array $tabrows A [nested] array of tab row objects
 * @param string $selected The tabrow to select (by id)
 * @param array $inactive An array of tabrow id's to make inactive
 * @param array $activated An array of tabrow id's to make active
 * @return array The nested array 
 */
function convert_tabrows_to_tree($tabrows, $selected, $inactive, $activated) {

/// Work backwards through the rows (bottom to top) collecting the tree as we go.

    $tabrows = array_reverse($tabrows);

    $subtree = array();

    foreach ($tabrows as $row) {
        $tree = array();

        foreach ($row as $tab) {
            $tab->inactive = in_array((string)$tab->id, $inactive);
            $tab->active = in_array((string)$tab->id, $activated);
            $tab->selected = (string)$tab->id == $selected;

            if ($tab->active || $tab->selected) {
                if ($subtree) {
                    $tab->subtree = $subtree;
                }
            }
            $tree[] = $tab;
        }
        $subtree = $tree;
    }

    return $subtree;
}

/**
 * Returns the Moodle Docs URL in the users language
 *
 * @global object
 * @param string $path the end of the URL.
 * @return string The MoodleDocs URL in the user's language. for example {@link http://docs.moodle.org/en/ http://docs.moodle.org/en/$path}
 */
function get_docs_url($path) {
    global $CFG;
    return $CFG->docroot . '/' . str_replace('_utf8', '', current_language()) . '/' . $path;
}

/**
 * Returns a string containing a link to the user documentation.
 * Also contains an icon by default. Shown to teachers and admin only.
 *
 * @global object
 * @param string $path The page link after doc root and language, no leading slash.
 * @param string $text The text to be displayed for the link
 * @param string $iconpath The path to the icon to be displayed
 * @return string Either the link or an empty string
 */
function doc_link($path='', $text='', $iconpath='') {
    global $CFG;

    if (empty($CFG->docroot)) {
        return '';
    }

    $url = get_docs_url($path);

    $target = '';
    if (!empty($CFG->doctonewwindow)) {
        $target = " onclick=\"window.open('$url'); return false;\"";
    }

    $str = "<a href=\"$url\"$target>";

    if (empty($iconpath)) {
        $iconpath = $CFG->httpswwwroot . '/pix/docs.gif';
    }

    // alt left blank intentionally to prevent repetition in screenreaders
    $str .= '<img class="iconhelp" src="' .$iconpath. '" alt="" />' .$text. '</a>';

    return $str;
}


/**
 * Standard Debugging Function
 *
 * Returns true if the current site debugging settings are equal or above specified level.
 * If passed a parameter it will emit a debugging notice similar to trigger_error(). The
 * routing of notices is controlled by $CFG->debugdisplay
 * eg use like this:
 *
 * 1)  debugging('a normal debug notice');
 * 2)  debugging('something really picky', DEBUG_ALL);
 * 3)  debugging('annoying debug message only for develpers', DEBUG_DEVELOPER);
 * 4)  if (debugging()) { perform extra debugging operations (do not use print or echo) }
 *
 * In code blocks controlled by debugging() (such as example 4)
 * any output should be routed via debugging() itself, or the lower-level
 * trigger_error() or error_log(). Using echo or print will break XHTML
 * JS and HTTP headers.
 *
 *
 * @global object
 * @uses DEBUG_NORMAL
 * @param string $message a message to print
 * @param int $level the level at which this debugging statement should show
 * @param array $backtrace use different backtrace
 * @return bool
 */
function debugging($message = '', $level = DEBUG_NORMAL, $backtrace = null) {
    global $CFG;

    if (empty($CFG->debug) || $CFG->debug < $level) {
        return false;
    }

    if (!isset($CFG->debugdisplay)) {
        $CFG->debugdisplay = ini_get_bool('display_errors');
    }

    if ($message) {
        if (!$backtrace) {
            $backtrace = debug_backtrace();
        }
        $from = format_backtrace($backtrace, CLI_SCRIPT);
        if ($CFG->debugdisplay) {
            if (!defined('DEBUGGING_PRINTED')) {
                define('DEBUGGING_PRINTED', 1); // indicates we have printed something
            }
            notify($message . $from, 'notifytiny');
        } else {
            trigger_error($message . $from, E_USER_NOTICE);
        }
    }
    return true;
}

/**
 * Disable debug messages from debugging(), while keeping PHP error reporting level as is.
 *
 * @global object
 */
function disable_debugging() {
    global $CFG;
    $CFG->debug = $CFG->debug | 0x80000000; // switch the sign bit in integer number ;-)
}


/**
 *  Returns string to add a frame attribute, if required
 *
 * @global object
 * @return bool
 */
function frametarget() {
    global $CFG;

    if (empty($CFG->framename) or ($CFG->framename == '_top')) {
        return '';
    } else {
        return ' target="'.$CFG->framename.'" ';
    }
}

/**
* Outputs a HTML comment to the browser. This is used for those hard-to-debug
* pages that use bits from many different files in very confusing ways (e.g. blocks).
*
* <code>print_location_comment(__FILE__, __LINE__);</code>
*
* @param string $file
* @param integer $line
* @param boolean $return Whether to return or print the comment
* @return string|void Void unless true given as third parameter
*/
function print_location_comment($file, $line, $return = false)
{
    if ($return) {
        return "<!-- $file at line $line -->\n";
    } else {
        echo "<!-- $file at line $line -->\n";
    }
}


/**
 * Returns an image of an up or down arrow, used for column sorting. To avoid unnecessary DB accesses, please
 * provide this function with the language strings for sortasc and sortdesc.
 *
 * If no sort string is associated with the direction, an arrow with no alt text will be printed/returned.
 *
 * @global object
 * @param string $direction 'up' or 'down'
 * @param string $strsort The language string used for the alt attribute of this image
 * @param bool $return Whether to print directly or return the html string
 * @return string|void depending on $return
 *
 */
function print_arrow($direction='up', $strsort=null, $return=false) {
    global $OUTPUT;

    if (!in_array($direction, array('up', 'down', 'right', 'left', 'move'))) {
        return null;
    }

    $return = null;

    switch ($direction) {
        case 'up':
            $sortdir = 'asc';
            break;
        case 'down':
            $sortdir = 'desc';
            break;
        case 'move':
            $sortdir = 'asc';
            break;
        default:
            $sortdir = null;
            break;
    }

    // Prepare language string
    $strsort = '';
    if (empty($strsort) && !empty($sortdir)) {
        $strsort  = get_string('sort' . $sortdir, 'grades');
    }

    $return = ' <img src="'.$OUTPUT->old_icon_url('t/' . $direction) . '" alt="'.$strsort.'" /> ';

    if ($return) {
        return $return;
    } else {
        echo $return;
    }
}

/**
 * @return boolean true if the current language is right-to-left (Hebrew, Arabic etc)
 */
function right_to_left() {
    static $result;

    if (!isset($result)) {
        $result = get_string('thisdirection') == 'rtl';
    }
    return $result;
}


/**
 * Returns swapped left<=>right if in RTL environment.
 * part of RTL support
 *
 * @param string $align align to check
 * @return string
 */
function fix_align_rtl($align) {
    if (!right_to_left()) {
        return $align;
    }
    if ($align=='left')  { return 'right'; }
    if ($align=='right') { return 'left'; }
    return $align;
}


/**
 * Returns true if the page is displayed in a popup window.
 * Gets the information from the URL parameter inpopup.
 *
 * @todo Use a central function to create the popup calls allover Moodle and
 * In the moment only works with resources and probably questions.
 *
 * @return boolean
 */
function is_in_popup() {
    $inpopup = optional_param('inpopup', '', PARAM_BOOL);

    return ($inpopup);
}

/**
 * To use this class.
 * - construct
 * - call create (or use the 3rd param to the constructor)
 * - call update or update_full repeatedly
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package moodlecore
 */
class progress_bar {
    /**
     * @var str
     */
    private $html_id;
    /**
     * @var int
     */
    private $percent;
    private $width;
    /**
     * @var object
     */
    private $clr;
    /**
     * @var int
     */
    private $lastcall;
    private $time_start;
    private $minimum_time = 2; //min time between updates.
    /**
     * Contructor
     *
     * @param string $html_id
     * @param int $width
     * @param bool $autostart Default to false
     */
    function __construct($html_id = 'pid', $width = 500, $autostart = false){
        $this->html_id  = $html_id;
        $this->clr      = new stdClass;
        $this->clr->done    = 'green';
        $this->clr->process = '#FFCC66';
        $this->width = $width;
        $this->restart();
        if($autostart){
            $this->create();
        }
    }
    /**
      * set progress bar color, call before $this->create
      *
      * Usage:
      *     $clr->done = 'red';
      *     $clr->process = 'blue';
      *     $pb->setclr($clr);
      *     $pb->create();
      *     ......
      *
      * @param object $clr
      */
    function setclr($clr){
        foreach($clr as $n=>$v) {
            $this->clr->$n = $v;
        }
    }
    /**
      * Create a new progress bar, this function will output html.
      * 
      * @return void Echo's output
      */
    function create(){
            flush();
            $this->lastcall->pt = 0;
            $this->lastcall->time = microtime(true);
            if (CLI_SCRIPT) {
                return; // temporary solution for cli scripts
            }
            $htmlcode = <<<EOT
            <script type="text/javascript">
            Number.prototype.fixed=function(n){
                with(Math)
                    return round(Number(this)*pow(10,n))/pow(10,n);
            }
            function up_{$this->html_id} (id, width, pt, msg, es){
                percent = pt*100;
                document.getElementById("status_"+id).innerHTML = msg;
                document.getElementById("pt_"+id).innerHTML =
                    percent.fixed(2) + '%';
                if(percent == 100) {
                    document.getElementById("progress_"+id).style.background
                        = "{$this->clr->done}";
                    document.getElementById("time_"+id).style.display
                            = "none";
                } else {
                    document.getElementById("progress_"+id).style.background
                        = "{$this->clr->process}";
                    if (es == Infinity){
                        document.getElementById("time_"+id).innerHTML =
                            "Initializing...";
                    }else {
                        document.getElementById("time_"+id).innerHTML =
                            es.fixed(2)+" sec";
                        document.getElementById("time_"+id).style.display
                            = "block";
                    }
                }
                document.getElementById("progress_"+id).style.width
                    = width + "px";

            }

            </script>
            <div style="text-align:center;width:{$this->width}px;clear:both;padding:0;margin:0 auto;">
                <h2 id="status_{$this->html_id}" style="text-align: center;margin:0 auto"></h2>
                <p id="time_{$this->html_id}"></p>
                <div id="bar_{$this->html_id}" style="border-style:solid;border-width:1px;width:500px;height:50px;">
                    <div id="progress_{$this->html_id}"
                    style="text-align:center;background:{$this->clr->process};width:4px;border:1px
                    solid gray;height:38px; padding-top:10px;">&nbsp;<span id="pt_{$this->html_id}"></span>
                    </div>
                </div>
            </div>
EOT;
            echo $htmlcode;
            flush();
    }
    /**
     * Update the progress bar
     *
     * @param int $percent from 1-100
     * @param string $msg
     * @param mixed $es
     * @return void Echo's output
     */
    function _update($percent, $msg, $es){
        global $PAGE;
        if(empty($this->time_start)){
            $this->time_start = microtime(true);
        }
        $this->percent = $percent;
        $this->lastcall->time = microtime(true);
        $this->lastcall->pt   = $percent;
        $w = $this->percent * $this->width;
        if (CLI_SCRIPT) {
            return; // temporary solution for cli scripts
        }
        if ($es === null){
            $es = "Infinity";
        }
        echo $PAGE->requires->js_function_call("up_".$this->html_id, Array($this->html_id, $w, $this->percent, $msg, $es))->asap();
        flush();
    }
    /**
      * estimate time
      *
      * @param int $curtime the time call this function
      * @param int $percent from 1-100
      * @return mixed Null, or int
      */
    function estimate($curtime, $pt){
        $consume = $curtime - $this->time_start;
        $one = $curtime - $this->lastcall->time;
        $this->percent = $pt;
        $percent = $pt - $this->lastcall->pt;
        if ($percent != 0) {
            $left = ($one / $percent) - $consume;
        } else {
            return null;
        }
        if($left < 0) {
            return 0;
        } else {
            return $left;
        }
    }
    /**
      * Update progress bar according percent
      *
      * @param int $percent from 1-100
      * @param string $msg the message needed to be shown
      */
    function update_full($percent, $msg){
        $percent = max(min($percent, 100), 0);
        if ($percent != 100 && ($this->lastcall->time + $this->minimum_time) > microtime(true)){
            return;
        }
        $this->_update($percent/100, $msg);
    }
    /**
      * Update progress bar according the nubmer of tasks
      *
      * @param int $cur current task number
      * @param int $total total task number
      * @param string $msg message
      */
    function update($cur, $total, $msg){
        $cur = max($cur, 0);
        if ($cur >= $total){
            $percent = 1;
        } else {
            $percent = $cur / $total;
        }
        /**
        if ($percent != 1 && ($this->lastcall->time + $this->minimum_time) > microtime(true)){
            return;
        }
        */
        $es = $this->estimate(microtime(true), $percent);
        $this->_update($percent, $msg, $es);
    }
    /**
     * Restart the progress bar.
     */
    function restart(){
        $this->percent  = 0;
        $this->lastcall = new stdClass;
        $this->lastcall->pt = 0;
        $this->lastcall->time = microtime(true);
        $this->time_start  = 0;
    }
}

/**
 * Use this class from long operations where you want to output occasional information about
 * what is going on, but don't know if, or in what format, the output should be.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package moodlecore
 */
abstract class moodle_progress_trace {
    /**
     * Ouput an progress message in whatever format.
     * @param string $message the message to output.
     * @param integer $depth indent depth for this message.
     */
    abstract public function output($message, $depth = 0);

    /**
     * Called when the processing is finished.
     */
    public function finished() {
    }
}

/**
 * This subclass of moodle_progress_trace does not ouput anything.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package moodlecore
 */
class null_progress_trace extends moodle_progress_trace {
    /**
     * Does Nothing
     *
     * @param string $message
     * @param int $depth
     * @return void Does Nothing
     */
    public function output($message, $depth = 0) {
    }
}

/**
 * This subclass of moodle_progress_trace outputs to plain text.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package moodlecore
 */
class text_progress_trace extends moodle_progress_trace {
    /**
     * Output the trace message
     *
     * @param string $message
     * @param int $depth
     * @return void Output is echo'd
     */
    public function output($message, $depth = 0) {
        echo str_repeat('  ', $depth), $message, "\n";
        flush();
    }
}

/**
 * This subclass of moodle_progress_trace outputs as HTML.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package moodlecore
 */
class html_progress_trace extends moodle_progress_trace {
    /**
     * Output the trace message
     *
     * @param string $message
     * @param int $depth
     * @return void Output is echo'd
     */
    public function output($message, $depth = 0) {
        echo '<p>', str_repeat('&#160;&#160;', $depth), htmlspecialchars($message), "</p>\n";
        flush();
    }
}

/**
 * HTML List Progress Tree
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package moodlecore
 */
class html_list_progress_trace extends moodle_progress_trace {
    /** @var int */
    protected $currentdepth = -1;

    /**
     * Echo out the list
     *
     * @param string $message The message to display
     * @param int $depth
     * @return void Output is echoed
     */
    public function output($message, $depth = 0) {
        $samedepth = true;
        while ($this->currentdepth > $depth) {
            echo "</li>\n</ul>\n";
            $this->currentdepth -= 1;
            if ($this->currentdepth == $depth) {
                echo '<li>';
            }
            $samedepth = false;
        }
        while ($this->currentdepth < $depth) {
            echo "<ul>\n<li>";
            $this->currentdepth += 1;
            $samedepth = false;
        }
        if ($samedepth) {
            echo "</li>\n<li>";
        }
        echo htmlspecialchars($message);
        flush();
    }

    /**
     * Called when the processing is finished.
     */
    public function finished() {
        while ($this->currentdepth >= 0) {
            echo "</li>\n</ul>\n";
            $this->currentdepth -= 1;
        }
    }
}

/**
 * Return the authentication plugin title
 *
 * @param string $authtype plugin type
 * @return string
 */
function auth_get_plugin_title($authtype) {
    $authtitle = get_string("auth_{$authtype}title", "auth");
    if ($authtitle == "[[auth_{$authtype}title]]") {
        $authtitle = get_string("auth_{$authtype}title", "auth_{$authtype}");
    }
    return $authtitle;
}

