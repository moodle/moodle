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
 * @package    core
 * @subpackage lib
 * @copyright  1999 onwards Martin Dougiamas {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

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
 * A moodle_url comparison using this flag will return true if the base URLs match, params are ignored
 */
define('URL_MATCH_BASE', 0);
/**
 * A moodle_url comparison using this flag will return true if the base URLs match and the params of url1 are part of url2
 */
define('URL_MATCH_PARAMS', 1);
/**
 * A moodle_url comparison using this flag will return true if the two URLs are identical, except for the order of the params
 */
define('URL_MATCH_EXACT', 2);

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

    if ($var === false) {
        return '0';
    }

    // When we move to PHP 5.4 as a minimum version, change ENT_QUOTES on the
    // next line to ENT_QUOTES | ENT_HTML5 | ENT_SUBSTITUTE, and remove the
    // 'UTF-8' argument. Both bring a speed-increase.
    return preg_replace('/&amp;#(\d+|x[0-9a-f]+);/i', '&#$1;', htmlspecialchars($var, ENT_QUOTES, 'UTF-8'));
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
 * @return string The resulting referer or empty string
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
 * @return mixed String, or false if the global variables needed are not set
 */
function me() {
    global $ME;
    return $ME;
}

/**
 * Guesses the full URL of the current script.
 *
 * This function is using $PAGE->url, but may fall back to $FULLME which
 * is constructed from  PHP_SELF and REQUEST_URI or SCRIPT_NAME
 *
 * @return mixed full page URL string or false if unknown
 */
function qualified_me() {
    global $FULLME, $PAGE, $CFG;

    if (isset($PAGE) and $PAGE->has_set_url()) {
        // this is the only recommended way to find out current page
        return $PAGE->url->out(false);

    } else {
        if ($FULLME === null) {
            // CLI script most probably
            return false;
        }
        if (!empty($CFG->sslproxy)) {
            // return only https links when using SSL proxy
            return preg_replace('/^http:/', 'https:', $FULLME, 1);
        } else {
            return $FULLME;
        }
    }
}

/**
 * Class for creating and manipulating urls.
 *
 * It can be used in moodle pages where config.php has been included without any further includes.
 *
 * It is useful for manipulating urls with long lists of params.
 * One situation where it will be useful is a page which links to itself to perform various actions
 * and / or to process form data. A moodle_url object :
 * can be created for a page to refer to itself with all the proper get params being passed from page call to
 * page call and methods can be used to output a url including all the params, optionally adding and overriding
 * params and can also be used to
 *     - output the url without any get params
 *     - and output the params as hidden fields to be output within a form
 *
 * @link http://docs.moodle.org/dev/lib/weblib.php_moodle_url See short write up here
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package moodlecore
 */
class moodle_url {
    /**
     * Scheme, ex.: http, https
     * @var string
     */
    protected $scheme = '';
    /**
     * hostname
     * @var string
     */
    protected $host = '';
    /**
     * Port number, empty means default 80 or 443 in case of http
     * @var unknown_type
     */
    protected $port = '';
    /**
     * Username for http auth
     * @var string
     */
    protected $user = '';
    /**
     * Password for http auth
     * @var string
     */
    protected $pass = '';
    /**
     * Script path
     * @var string
     */
    protected $path = '';
    /**
     * Optional slash argument value
     * @var string
     */
    protected $slashargument = '';
    /**
     * Anchor, may be also empty, null means none
     * @var string
     */
    protected $anchor = null;
    /**
     * Url parameters as associative array
     * @var array
     */
    protected $params = array(); // Associative array of query string params

    /**
     * Create new instance of moodle_url.
     *
     * @param moodle_url|string $url - moodle_url means make a copy of another
     *      moodle_url and change parameters, string means full url or shortened
     *      form (ex.: '/course/view.php'). It is strongly encouraged to not include
     *      query string because it may result in double encoded values. Use the
     *      $params instead. For admin URLs, just use /admin/script.php, this
     *      class takes care of the $CFG->admin issue.
     * @param array $params these params override current params or add new
     */
    public function __construct($url, array $params = null) {
        global $CFG;

        if ($url instanceof moodle_url) {
            $this->scheme = $url->scheme;
            $this->host = $url->host;
            $this->port = $url->port;
            $this->user = $url->user;
            $this->pass = $url->pass;
            $this->path = $url->path;
            $this->slashargument = $url->slashargument;
            $this->params = $url->params;
            $this->anchor = $url->anchor;

        } else {
            // detect if anchor used
            $apos = strpos($url, '#');
            if ($apos !== false) {
                $anchor = substr($url, $apos);
                $anchor = ltrim($anchor, '#');
                $this->set_anchor($anchor);
                $url = substr($url, 0, $apos);
            }

            // normalise shortened form of our url ex.: '/course/view.php'
            if (strpos($url, '/') === 0) {
                // we must not use httpswwwroot here, because it might be url of other page,
                // devs have to use httpswwwroot explicitly when creating new moodle_url
                $url = $CFG->wwwroot.$url;
            }

            // now fix the admin links if needed, no need to mess with httpswwwroot
            if ($CFG->admin !== 'admin') {
                if (strpos($url, "$CFG->wwwroot/admin/") === 0) {
                    $url = str_replace("$CFG->wwwroot/admin/", "$CFG->wwwroot/$CFG->admin/", $url);
                }
            }

            // parse the $url
            $parts = parse_url($url);
            if ($parts === false) {
                throw new moodle_exception('invalidurl');
            }
            if (isset($parts['query'])) {
                // note: the values may not be correctly decoded,
                //       url parameters should be always passed as array
                parse_str(str_replace('&amp;', '&', $parts['query']), $this->params);
            }
            unset($parts['query']);
            foreach ($parts as $key => $value) {
                $this->$key = $value;
            }

            // detect slashargument value from path - we do not support directory names ending with .php
            $pos = strpos($this->path, '.php/');
            if ($pos !== false) {
                $this->slashargument = substr($this->path, $pos + 4);
                $this->path = substr($this->path, 0, $pos + 4);
            }
        }

        $this->params($params);
    }

    /**
     * Add an array of params to the params for this url.
     *
     * The added params override existing ones if they have the same name.
     *
     * @param array $params Defaults to null. If null then returns all params.
     * @return array Array of Params for url.
     */
    public function params(array $params = null) {
        $params = (array)$params;

        foreach ($params as $key=>$value) {
            if (is_int($key)) {
                throw new coding_exception('Url parameters can not have numeric keys!');
            }
            if (!is_string($value)) {
                if (is_array($value)) {
                    throw new coding_exception('Url parameters values can not be arrays!');
                }
                if (is_object($value) and !method_exists($value, '__toString')) {
                    throw new coding_exception('Url parameters values can not be objects, unless __toString() is defined!');
                }
            }
            $this->params[$key] = (string)$value;
        }
        return $this->params;
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
     * @return array url parameters
     */
    public function remove_params($params = null) {
        if (!is_array($params)) {
            $params = func_get_args();
        }
        foreach ($params as $param) {
            unset($this->params[$param]);
        }
        return $this->params;
    }

    /**
     * Remove all url parameters
     * @param $params
     * @return void
     */
    public function remove_all_params($params = null) {
        $this->params = array();
        $this->slashargument = '';
    }

    /**
     * Add a param to the params for this url.
     *
     * The added param overrides existing one if they have the same name.
     *
     * @param string $paramname name
     * @param string $newvalue Param value. If new value specified current value is overriden or parameter is added
     * @return mixed string parameter value, null if parameter does not exist
     */
    public function param($paramname, $newvalue = '') {
        if (func_num_args() > 1) {
            // set new value
            $this->params(array($paramname=>$newvalue));
        }
        if (isset($this->params[$paramname])) {
            return $this->params[$paramname];
        } else {
            return null;
        }
    }

    /**
     * Merges parameters and validates them
     * @param array $overrideparams
     * @return array merged parameters
     */
    protected function merge_overrideparams(array $overrideparams = null) {
        $overrideparams = (array)$overrideparams;
        $params = $this->params;
        foreach ($overrideparams as $key=>$value) {
            if (is_int($key)) {
                throw new coding_exception('Overridden parameters can not have numeric keys!');
            }
            if (is_array($value)) {
                throw new coding_exception('Overridden parameters values can not be arrays!');
            }
            if (is_object($value) and !method_exists($value, '__toString')) {
                throw new coding_exception('Overridden parameters values can not be objects, unless __toString() is defined!');
            }
            $params[$key] = (string)$value;
        }
        return $params;
    }

    /**
     * Get the params as as a query string.
     * This method should not be used outside of this method.
     *
     * @param boolean $escaped Use &amp; as params separator instead of plain &
     * @param array $overrideparams params to add to the output params, these
     *      override existing ones with the same name.
     * @return string query string that can be added to a url.
     */
    public function get_query_string($escaped = true, array $overrideparams = null) {
        $arr = array();
        if ($overrideparams !== null) {
            $params = $this->merge_overrideparams($overrideparams);
        } else {
            $params = $this->params;
        }
        foreach ($params as $key => $val) {
            if (is_array($val)) {
                foreach ($val as $index => $value) {
                    $arr[] = rawurlencode($key.'['.$index.']')."=".rawurlencode($value);
                }
            } else {
                if (isset($val) && $val !== '') {
                    $arr[] = rawurlencode($key)."=".rawurlencode($val);
                } else {
                    $arr[] = rawurlencode($key);
                }
            }
        }
        if ($escaped) {
            return implode('&amp;', $arr);
        } else {
            return implode('&', $arr);
        }
    }

    /**
     * Shortcut for printing of encoded URL.
     * @return string
     */
    public function __toString() {
        return $this->out(true);
    }

    /**
     * Output url
     *
     * If you use the returned URL in HTML code, you want the escaped ampersands. If you use
     * the returned URL in HTTP headers, you want $escaped=false.
     *
     * @param boolean $escaped Use &amp; as params separator instead of plain &
     * @param array $overrideparams params to add to the output url, these override existing ones with the same name.
     * @return string Resulting URL
     */
    public function out($escaped = true, array $overrideparams = null) {
        if (!is_bool($escaped)) {
            debugging('Escape parameter must be of type boolean, '.gettype($escaped).' given instead.');
        }

        $uri = $this->out_omit_querystring().$this->slashargument;

        $querystring = $this->get_query_string($escaped, $overrideparams);
        if ($querystring !== '') {
            $uri .= '?' . $querystring;
        }
        if (!is_null($this->anchor)) {
            $uri .= '#'.$this->anchor;
        }

        return $uri;
    }

    /**
     * Returns url without parameters, everything before '?'.
     *
     * @param bool $includeanchor if {@link self::anchor} is defined, should it be returned?
     * @return string
     */
    public function out_omit_querystring($includeanchor = false) {

        $uri = $this->scheme ? $this->scheme.':'.((strtolower($this->scheme) == 'mailto') ? '':'//'): '';
        $uri .= $this->user ? $this->user.($this->pass? ':'.$this->pass:'').'@':'';
        $uri .= $this->host ? $this->host : '';
        $uri .= $this->port ? ':'.$this->port : '';
        $uri .= $this->path ? $this->path : '';
        if ($includeanchor and !is_null($this->anchor)) {
            $uri .= '#' . $this->anchor;
        }

        return $uri;
    }

    /**
     * Compares this moodle_url with another
     * See documentation of constants for an explanation of the comparison flags.
     * @param moodle_url $url The moodle_url object to compare
     * @param int $matchtype The type of comparison (URL_MATCH_BASE, URL_MATCH_PARAMS, URL_MATCH_EXACT)
     * @return boolean
     */
    public function compare(moodle_url $url, $matchtype = URL_MATCH_EXACT) {

        $baseself = $this->out_omit_querystring();
        $baseother = $url->out_omit_querystring();

        // Append index.php if there is no specific file
        if (substr($baseself,-1)=='/') {
            $baseself .= 'index.php';
        }
        if (substr($baseother,-1)=='/') {
            $baseother .= 'index.php';
        }

        // Compare the two base URLs
        if ($baseself != $baseother) {
            return false;
        }

        if ($matchtype == URL_MATCH_BASE) {
            return true;
        }

        $urlparams = $url->params();
        foreach ($this->params() as $param => $value) {
            if ($param == 'sesskey') {
                continue;
            }
            if (!array_key_exists($param, $urlparams) || $urlparams[$param] != $value) {
                return false;
            }
        }

        if ($matchtype == URL_MATCH_PARAMS) {
            return true;
        }

        foreach ($urlparams as $param => $value) {
            if ($param == 'sesskey') {
                continue;
            }
            if (!array_key_exists($param, $this->params()) || $this->param($param) != $value) {
                return false;
            }
        }

        return true;
    }

    /**
     * Sets the anchor for the URI (the bit after the hash)
     * @param string $anchor null means remove previous
     */
    public function set_anchor($anchor) {
        if (is_null($anchor)) {
            // remove
            $this->anchor = null;
        } else if ($anchor === '') {
            // special case, used as empty link
            $this->anchor = '';
        } else if (preg_match('|[a-zA-Z\_\:][a-zA-Z0-9\_\-\.\:]*|', $anchor)) {
            // Match the anchor against the NMTOKEN spec
            $this->anchor = $anchor;
        } else {
            // bad luck, no valid anchor found
            $this->anchor = null;
        }
    }

    /**
     * Sets the url slashargument value
     * @param string $path usually file path
     * @param string $parameter name of page parameter if slasharguments not supported
     * @param bool $supported usually null, then it depends on $CFG->slasharguments, use true or false for other servers
     * @return void
     */
    public function set_slashargument($path, $parameter = 'file', $supported = NULL) {
        global $CFG;
        if (is_null($supported)) {
            $supported = $CFG->slasharguments;
        }

        if ($supported) {
            $parts = explode('/', $path);
            $parts = array_map('rawurlencode', $parts);
            $path  = implode('/', $parts);
            $this->slashargument = $path;
            unset($this->params[$parameter]);

        } else {
            $this->slashargument = '';
            $this->params[$parameter] = $path;
        }
    }

    // == static factory methods ==

    /**
     * General moodle file url.
     * @param string $urlbase the script serving the file
     * @param string $path
     * @param bool $forcedownload
     * @return moodle_url
     */
    public static function make_file_url($urlbase, $path, $forcedownload = false) {
        global $CFG;

        $params = array();
        if ($forcedownload) {
            $params['forcedownload'] = 1;
        }

        $url = new moodle_url($urlbase, $params);
        $url->set_slashargument($path);

        return $url;
    }

    /**
     * Factory method for creation of url pointing to plugin file.
     * Please note this method can be used only from the plugins to
     * create urls of own files, it must not be used outside of plugins!
     * @param int $contextid
     * @param string $component
     * @param string $area
     * @param int $itemid
     * @param string $pathname
     * @param string $filename
     * @param bool $forcedownload
     * @return moodle_url
     */
    public static function make_pluginfile_url($contextid, $component, $area, $itemid, $pathname, $filename, $forcedownload = false) {
        global $CFG;
        $urlbase = "$CFG->httpswwwroot/pluginfile.php";
        if ($itemid === NULL) {
            return self::make_file_url($urlbase, "/$contextid/$component/$area".$pathname.$filename, $forcedownload);
        } else {
            return self::make_file_url($urlbase, "/$contextid/$component/$area/$itemid".$pathname.$filename, $forcedownload);
        }
    }

    /**
     * Factory method for creation of url pointing to draft
     * file of current user.
     * @param int $draftid draft item id
     * @param string $pathname
     * @param string $filename
     * @param bool $forcedownload
     * @return moodle_url
     */
    public static function make_draftfile_url($draftid, $pathname, $filename, $forcedownload = false) {
        global $CFG, $USER;
        $urlbase = "$CFG->httpswwwroot/draftfile.php";
        $context = context_user::instance($USER->id);

        return self::make_file_url($urlbase, "/$context->id/user/draft/$draftid".$pathname.$filename, $forcedownload);
    }

    /**
     * Factory method for creating of links to legacy
     * course files.
     * @param int $courseid
     * @param string $filepath
     * @param bool $forcedownload
     * @return moodle_url
     */
    public static function make_legacyfile_url($courseid, $filepath, $forcedownload = false) {
        global $CFG;

        $urlbase = "$CFG->wwwroot/file.php";
        return self::make_file_url($urlbase, '/'.$courseid.'/'.$filepath, $forcedownload);
    }

    /**
     * Returns URL a relative path from $CFG->wwwroot
     *
     * Can be used for passing around urls with the wwwroot stripped
     *
     * @param boolean $escaped Use &amp; as params separator instead of plain &
     * @param array $overrideparams params to add to the output url, these override existing ones with the same name.
     * @return string Resulting URL
     * @throws coding_exception if called on a non-local url
     */
    public function out_as_local_url($escaped = true, array $overrideparams = null) {
        global $CFG;

        $url = $this->out($escaped, $overrideparams);
        $httpswwwroot = str_replace("http://", "https://", $CFG->wwwroot);

        // $url should be equal to wwwroot or httpswwwroot. If not then throw exception.
        if (($url === $CFG->wwwroot) || (strpos($url, $CFG->wwwroot.'/') === 0)) {
            $localurl = substr($url, strlen($CFG->wwwroot));
            return !empty($localurl) ? $localurl : '';
        } else if (($url === $httpswwwroot) || (strpos($url, $httpswwwroot.'/') === 0)) {
            $localurl = substr($url, strlen($httpswwwroot));
            return !empty($localurl) ? $localurl : '';
        } else {
            throw new coding_exception('out_as_local_url called on a non-local URL');
        }
    }

    /**
     * Returns the 'path' portion of a URL. For example, if the URL is
     * http://www.example.org:447/my/file/is/here.txt?really=1 then this will
     * return '/my/file/is/here.txt'.
     *
     * By default the path includes slash-arguments (for example,
     * '/myfile.php/extra/arguments') so it is what you would expect from a
     * URL path. If you don't want this behaviour, you can opt to exclude the
     * slash arguments. (Be careful: if the $CFG variable slasharguments is
     * disabled, these URLs will have a different format and you may need to
     * look at the 'file' parameter too.)
     *
     * @param bool $includeslashargument If true, includes slash arguments
     * @return string Path of URL
     */
    public function get_path($includeslashargument = true) {
        return $this->path . ($includeslashargument ? $this->slashargument : '');
    }

    /**
     * Returns a given parameter value from the URL.
     *
     * @param string $name Name of parameter
     * @return string Value of parameter or null if not set
     */
    public function get_param($name) {
        if (array_key_exists($name, $this->params)) {
            return $this->params[$name];
        } else {
            return null;
        }
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
        return (object)fix_utf8($_POST);
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

/// First of all, save all the tags inside the text to skip them
    $tags = array();
    filter_save_tags($string,$tags);

/// Process the string adding the cut when necessary
    $output = '';
    $length = textlib::strlen($string);
    $wordlength = 0;

    for ($i=0; $i<$length; $i++) {
        $char = textlib::substr($string, $i, 1);
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
    global $PAGE, $OUTPUT;

    if (!$PAGE->headerprinted) {
        $PAGE->set_title(get_string('closewindow'));
        echo $OUTPUT->header();
    } else {
        $OUTPUT->container_end_all(false);
    }

    if ($reloadopener) {
        // Trigger the reload immediately, even if the reload is after a delay.
        $PAGE->requires->js_function_call('window.opener.location.reload', array(true));
    }
    $OUTPUT->notification(get_string('windowclosing'), 'notifysuccess');

    $PAGE->requires->js_function_call('close_window', array(new stdClass()), false, $delay);

    echo $OUTPUT->footer();
    exit;
}

/**
 * Returns a string containing a link to the user documentation for the current
 * page. Also contains an icon by default. Shown to teachers and admin only.
 *
 * @global object
 * @global object
 * @param string $text The text to be displayed for the link
 * @param string $iconpath The path to the icon to be displayed
 * @return string The link to user documentation for this current page
 */
function page_doc_link($text='') {
    global $OUTPUT, $PAGE;
    $path = page_get_doc_link_path($PAGE);
    if (!$path) {
        return '';
    }
    return $OUTPUT->doc_link($path, $text);
}

/**
 * Returns the path to use when constructing a link to the docs.
 *
 * @since 2.5.1 2.6
 * @global stdClass $CFG
 * @param moodle_page $page
 * @return string
 */
function page_get_doc_link_path(moodle_page $page) {
    global $CFG;

    if (empty($CFG->docroot) || during_initial_install()) {
        return '';
    }
    if (!has_capability('moodle/site:doclinks', $page->context)) {
        return '';
    }

    $path = $page->docspath;
    if (!$path) {
        return '';
    }
    return $path;
}


/**
 * Validates an email to make sure it makes sense.
 *
 * @param string $address The email address to validate.
 * @return boolean
 */
function validate_email($address) {

    return (preg_match('#^[-!\#$%&\'*+\\/0-9=?A-Z^_`a-z{|}~]+'.
                 '(\.[-!\#$%&\'*+\\/0-9=?A-Z^_`a-z{|}~]+)*'.
                  '@'.
                  '[-!\#$%&\'*+\\/0-9=?A-Z^_`a-z{|}~]+\.'.
                  '[-!\#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+$#',
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

    if ($relativepath !== false and $relativepath !== '') {
        return $relativepath;
    }
    $relativepath = false;

    // then try extract file from the slasharguments
    if (stripos($_SERVER['SERVER_SOFTWARE'], 'iis') !== false) {
        // NOTE: ISS tends to convert all file paths to single byte DOS encoding,
        //       we can not use other methods because they break unicode chars,
        //       the only way is to use URL rewriting
        if (isset($_SERVER['PATH_INFO']) and $_SERVER['PATH_INFO'] !== '') {
            // check that PATH_INFO works == must not contain the script name
            if (strpos($_SERVER['PATH_INFO'], $SCRIPT) === false) {
                $relativepath = clean_param(urldecode($_SERVER['PATH_INFO']), PARAM_PATH);
            }
        }
    } else {
        // all other apache-like servers depend on PATH_INFO
        if (isset($_SERVER['PATH_INFO'])) {
            if (isset($_SERVER['SCRIPT_NAME']) and strpos($_SERVER['PATH_INFO'], $_SERVER['SCRIPT_NAME']) === 0) {
                $relativepath = substr($_SERVER['PATH_INFO'], strlen($_SERVER['SCRIPT_NAME']));
            } else {
                $relativepath = $_SERVER['PATH_INFO'];
            }
            $relativepath = clean_param($relativepath, PARAM_PATH);
        }
    }


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
 * <pre>
 * Options:
 *      trusted     :   If true the string won't be cleaned. Default false required noclean=true.
 *      noclean     :   If true the string won't be cleaned. Default false required trusted=true.
 *      nocache     :   If true the strign will not be cached and will be formatted every call. Default false.
 *      filter      :   If true the string will be run through applicable filters as well. Default true.
 *      para        :   If true then the returned string will be wrapped in div tags. Default true.
 *      newlines    :   If true then lines newline breaks will be converted to HTML newline breaks. Default true.
 *      context     :   The context that will be used for filtering.
 *      overflowdiv :   If set to true the formatted text will be encased in a div
 *                      with the class no-overflow before being returned. Default false.
 *      allowid     :   If true then id attributes will not be removed, even when
 *                      using htmlpurifier. Default false.
 * </pre>
 *
 * @todo Finish documenting this function
 *
 * @staticvar array $croncache
 * @param string $text The text to be formatted. This is raw text originally from user input.
 * @param int $format Identifier of the text format to be used
 *            [FORMAT_MOODLE, FORMAT_HTML, FORMAT_PLAIN, FORMAT_MARKDOWN]
 * @param object/array $options text formatting options
 * @param int $courseid_do_not_use deprecated course id, use context option instead
 * @return string
 */
function format_text($text, $format = FORMAT_MOODLE, $options = NULL, $courseid_do_not_use = NULL) {
    global $CFG, $COURSE, $DB, $PAGE;
    static $croncache = array();

    if ($text === '' || is_null($text)) {
        return ''; // no need to do any filters and cleaning
    }

    $options = (array)$options; // detach object, we can not modify it

    if (!isset($options['trusted'])) {
        $options['trusted'] = false;
    }
    if (!isset($options['noclean'])) {
        if ($options['trusted'] and trusttext_active()) {
            // no cleaning if text trusted and noclean not specified
            $options['noclean'] = true;
        } else {
            $options['noclean'] = false;
        }
    }
    if (!isset($options['nocache'])) {
        $options['nocache'] = false;
    }
    if (!isset($options['filter'])) {
        $options['filter'] = true;
    }
    if (!isset($options['para'])) {
        $options['para'] = true;
    }
    if (!isset($options['newlines'])) {
        $options['newlines'] = true;
    }
    if (!isset($options['overflowdiv'])) {
        $options['overflowdiv'] = false;
    }

    // Calculate best context
    if (empty($CFG->version) or $CFG->version < 2013051400 or during_initial_install()) {
        // do not filter anything during installation or before upgrade completes
        $context = null;

    } else if (isset($options['context'])) { // first by explicit passed context option
        if (is_object($options['context'])) {
            $context = $options['context'];
        } else {
            $context = context::instance_by_id($options['context']);
        }
    } else if ($courseid_do_not_use) {
        // legacy courseid
        $context = context_course::instance($courseid_do_not_use);
    } else {
        // fallback to $PAGE->context this may be problematic in CLI and other non-standard pages :-(
        $context = $PAGE->context;
    }

    if (!$context) {
        // either install/upgrade or something has gone really wrong because context does not exist (yet?)
        $options['nocache'] = true;
        $options['filter']  = false;
    }

    if ($options['filter']) {
        $filtermanager = filter_manager::instance();
        $filtermanager->setup_page_for_filters($PAGE, $context); // Setup global stuff filters may have.
    } else {
        $filtermanager = new null_filter_manager();
    }

    if (!empty($CFG->cachetext) and empty($options['nocache'])) {
        $hashstr = $text.'-'.$filtermanager->text_filtering_hash($context).'-'.$context->id.'-'.current_language().'-'.
                (int)$format.(int)$options['trusted'].(int)$options['noclean'].
                (int)$options['para'].(int)$options['newlines'];

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
            if (!$options['noclean']) {
                $text = clean_text($text, FORMAT_HTML, $options);
            }
            $text = $filtermanager->filter_text($text, $context, array('originalformat' => FORMAT_HTML, 'noclean' => $options['noclean']));
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
            if (!$options['noclean']) {
                $text = clean_text($text, FORMAT_HTML, $options);
            }
            $text = $filtermanager->filter_text($text, $context, array('originalformat' => FORMAT_MARKDOWN, 'noclean' => $options['noclean']));
            break;

        default:  // FORMAT_MOODLE or anything else
            $text = text_to_html($text, null, $options['para'], $options['newlines']);
            if (!$options['noclean']) {
                $text = clean_text($text, FORMAT_HTML, $options);
            }
            $text = $filtermanager->filter_text($text, $context, array('originalformat' => $format, 'noclean' => $options['noclean']));
            break;
    }
    if ($options['filter']) {
        // at this point there should not be any draftfile links any more,
        // this happens when developers forget to post process the text.
        // The only potential problem is that somebody might try to format
        // the text before storing into database which would be itself big bug.
        $text = str_replace("\"$CFG->httpswwwroot/draftfile.php", "\"$CFG->httpswwwroot/brokenfile.php#", $text);
    }

    // Warn people that we have removed this old mechanism, just in case they
    // were stupid enough to rely on it.
    if (isset($CFG->currenttextiscacheable)) {
        debugging('Once upon a time, Moodle had a truly evil use of global variables ' .
                'called $CFG->currenttextiscacheable. The good news is that this no ' .
                'longer exists. The bad news is that you seem to be using a filter that '.
                'relies on it. Please seek out and destroy that filter code.', DEBUG_DEVELOPER);
    }

    if (!empty($options['overflowdiv'])) {
        $text = html_writer::tag('div', $text, array('class'=>'no-overflow'));
    }

    if (empty($options['nocache']) and !empty($CFG->cachetext)) {
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

        $newcacheitem = new stdClass();
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
 * Resets all data related to filters, called during upgrade or when filter settings change.
 *
 * @param bool $phpunitreset true means called from our PHPUnit integration test reset
 * @return void
 */
function reset_text_filters_cache($phpunitreset = false) {
    global $CFG, $DB;

    if (!$phpunitreset) {
        $DB->delete_records('cache_text');
    }

    $purifdir = $CFG->cachedir.'/htmlpurifier';
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
 * @staticvar bool $strcache
 * @param string $string The string to be filtered. Should be plain text, expect
 * possibly for multilang tags.
 * @param boolean $striplinks To strip any link in the result text.
                              Moodle 1.8 default changed from false to true! MDL-8713
 * @param array $options options array/object or courseid
 * @return string
 */
function format_string($string, $striplinks = true, $options = NULL) {
    global $CFG, $COURSE, $PAGE;

    //We'll use a in-memory cache here to speed up repeated strings
    static $strcache = false;

    if (empty($CFG->version) or $CFG->version < 2013051400 or during_initial_install()) {
        // do not filter anything during installation or before upgrade completes
        return $string = strip_tags($string);
    }

    if ($strcache === false or count($strcache) > 2000) { // this number might need some tuning to limit memory usage in cron
        $strcache = array();
    }

    if (is_numeric($options)) {
        // legacy courseid usage
        $options  = array('context'=>context_course::instance($options));
    } else {
        $options = (array)$options; // detach object, we can not modify it
    }

    if (empty($options['context'])) {
        // fallback to $PAGE->context this may be problematic in CLI and other non-standard pages :-(
        $options['context'] = $PAGE->context;
    } else if (is_numeric($options['context'])) {
        $options['context'] = context::instance_by_id($options['context']);
    }

    if (!$options['context']) {
        // we did not find any context? weird
        return $string = strip_tags($string);
    }

    //Calculate md5
    $md5 = md5($string.'<+>'.$striplinks.'<+>'.$options['context']->id.'<+>'.current_language());

    //Fetch from cache if possible
    if (isset($strcache[$md5])) {
        return $strcache[$md5];
    }

    // First replace all ampersands not followed by html entity code
    // Regular expression moved to its own method for easier unit testing
    $string = replace_ampersands_not_followed_by_entity($string);

    if (!empty($CFG->filterall)) {
        $filtermanager = filter_manager::instance();
        $filtermanager->setup_page_for_filters($PAGE, $options['context']); // Setup global stuff filters may have.
        $string = $filtermanager->filter_string($string, $options['context']);
    }

    // If the site requires it, strip ALL tags from this string
    if (!empty($CFG->formatstringstriptags)) {
        $string = str_replace(array('<', '>'), array('&lt;', '&gt;'), strip_tags($string));

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
            // there should not be any of these any more!
            $text = wikify_links($text);
            return textlib::entities_to_utf8(strip_tags($text), true);
            break;

        case FORMAT_HTML:
            return html_to_text($text);
            break;

        case FORMAT_MOODLE:
        case FORMAT_MARKDOWN:
        default:
            $text = wikify_links($text);
            return textlib::entities_to_utf8(strip_tags($text), true);
            break;
    }
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
    $context = context_module::instance($cmid);
    $options = array('noclean'=>true, 'para'=>false, 'filter'=>$filter, 'context'=>$context, 'overflowdiv'=>true);
    $intro = file_rewrite_pluginfile_urls($activity->intro, 'pluginfile.php', $context->id, 'mod_'.$module, 'intro', null);
    return trim(format_text($intro, $activity->introformat, $options, null));
}

/**
 * Legacy function, used for cleaning of old forum and glossary text only.
 *
 * @global object
 * @param string $text text that may contain legacy TRUSTTEXT marker
 * @return text without legacy TRUSTTEXT marker
 */
function trusttext_strip($text) {
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
 * @return bool
 */
function trusttext_active() {
    global $CFG;

    return !empty($CFG->enabletrusttext);
}

/**
 * Given raw text (eg typed in by a user), this function cleans it up
 * and removes any nasty tags that could mess up Moodle pages through XSS attacks.
 *
 * The result must be used as a HTML text fragment, this function can not cleanup random
 * parts of html tags such as url or src attributes.
 *
 * NOTE: the format parameter was deprecated because we can safely clean only HTML.
 *
 * @param string $text The text to be cleaned
 * @param int|string $format deprecated parameter, should always contain FORMAT_HTML or FORMAT_MOODLE
 * @param array $options Array of options; currently only option supported is 'allowid' (if true,
 *   does not remove id attributes when cleaning)
 * @return string The cleaned up text
 */
function clean_text($text, $format = FORMAT_HTML, $options = array()) {
    $text = (string)$text;

    if ($format != FORMAT_HTML and $format != FORMAT_HTML) {
        // TODO: we need to standardise cleanup of text when loading it into editor first
        //debugging('clean_text() is designed to work only with html');
    }

    if ($format == FORMAT_PLAIN) {
        return $text;
    }

    if (is_purify_html_necessary($text)) {
        $text = purify_html($text, $options);
    }

    // Originally we tried to neutralise some script events here, it was a wrong approach because
    // it was trivial to work around that (for example using style based XSS exploits).
    // We must not give false sense of security here - all developers MUST understand how to use
    // rawurlencode(), htmlentities(), htmlspecialchars(), p(), s(), moodle_url, html_writer and friends!!!

    return $text;
}

/**
 * Is it necessary to use HTMLPurifier?
 * @private
 * @param string $text
 * @return bool false means html is safe and valid, true means use HTMLPurifier
 */
function is_purify_html_necessary($text) {
    if ($text === '') {
        return false;
    }

    if ($text === (string)((int)$text)) {
        return false;
    }

    if (strpos($text, '&') !== false or preg_match('|<[^pesb/]|', $text)) {
        // we need to normalise entities or other tags except p, em, strong and br present
        return true;
    }

    $altered = htmlspecialchars($text, ENT_NOQUOTES, 'UTF-8', true);
    if ($altered === $text) {
        // no < > or other special chars means this must be safe
        return false;
    }

    // let's try to convert back some safe html tags
    $altered = preg_replace('|&lt;p&gt;(.*?)&lt;/p&gt;|m', '<p>$1</p>', $altered);
    if ($altered === $text) {
        return false;
    }
    $altered = preg_replace('|&lt;em&gt;([^<>]+?)&lt;/em&gt;|m', '<em>$1</em>', $altered);
    if ($altered === $text) {
        return false;
    }
    $altered = preg_replace('|&lt;strong&gt;([^<>]+?)&lt;/strong&gt;|m', '<strong>$1</strong>', $altered);
    if ($altered === $text) {
        return false;
    }
    $altered = str_replace('&lt;br /&gt;', '<br />', $altered);
    if ($altered === $text) {
        return false;
    }

    return true;
}

/**
 * KSES replacement cleaning function - uses HTML Purifier.
 *
 * @param string $text The (X)HTML string to purify
 * @param array $options Array of options; currently only option supported is 'allowid' (if set,
 *   does not remove id attributes when cleaning)
 * @return string
 */
function purify_html($text, $options = array()) {
    global $CFG;

    static $purifiers = array();
    static $caches = array();

    $type = !empty($options['allowid']) ? 'allowid' : 'normal';

    if (!array_key_exists($type, $caches)) {
        $caches[$type] = cache::make('core', 'htmlpurifier', array('type' => $type));
    }
    $cache = $caches[$type];

    $filteredtext = $cache->get($text);
    if ($filteredtext !== false) {
        return $filteredtext;
    }

    if (empty($purifiers[$type])) {

        // make sure the serializer dir exists, it should be fine if it disappears later during cache reset
        $cachedir = $CFG->cachedir.'/htmlpurifier';
        check_dir_exists($cachedir);

        require_once $CFG->libdir.'/htmlpurifier/HTMLPurifier.safe-includes.php';
        require_once $CFG->libdir.'/htmlpurifier/locallib.php';
        $config = HTMLPurifier_Config::createDefault();

        $config->set('HTML.DefinitionID', 'moodlehtml');
        $config->set('HTML.DefinitionRev', 2);
        $config->set('Cache.SerializerPath', $cachedir);
        $config->set('Cache.SerializerPermissions', $CFG->directorypermissions);
        $config->set('Core.NormalizeNewlines', false);
        $config->set('Core.ConvertDocumentToFragment', true);
        $config->set('Core.Encoding', 'UTF-8');
        $config->set('HTML.Doctype', 'XHTML 1.0 Transitional');
        $config->set('URI.AllowedSchemes', array('http'=>true, 'https'=>true, 'ftp'=>true, 'irc'=>true, 'nntp'=>true, 'news'=>true, 'rtsp'=>true, 'teamspeak'=>true, 'gopher'=>true, 'mms'=>true, 'mailto'=>true));
        $config->set('Attr.AllowedFrameTargets', array('_blank'));

        if (!empty($CFG->allowobjectembed)) {
            $config->set('HTML.SafeObject', true);
            $config->set('Output.FlashCompat', true);
            $config->set('HTML.SafeEmbed', true);
        }

        if ($type === 'allowid') {
            $config->set('Attr.EnableID', true);
        }

        if ($def = $config->maybeGetRawHTMLDefinition()) {
            $def->addElement('nolink', 'Block', 'Flow', array());                       // skip our filters inside
            $def->addElement('tex', 'Inline', 'Inline', array());                       // tex syntax, equivalent to $$xx$$
            $def->addElement('algebra', 'Inline', 'Inline', array());                   // algebra syntax, equivalent to @@xx@@
            $def->addElement('lang', 'Block', 'Flow', array(), array('lang'=>'CDATA')); // old and future style multilang - only our hacked lang attribute
            $def->addAttribute('span', 'xxxlang', 'CDATA');                             // current problematic multilang
        }

        $purifier = new HTMLPurifier($config);
        $purifiers[$type] = $purifier;
    } else {
        $purifier = $purifiers[$type];
    }

    $multilang = (strpos($text, 'class="multilang"') !== false);

    $filteredtext = $text;
    if ($multilang) {
        $filteredtext = preg_replace('/<span(\s+lang="([a-zA-Z0-9_-]+)"|\s+class="multilang"){2}\s*>/', '<span xxxlang="${2}">', $filteredtext);
    }
    $filteredtext = $purifier->purify($filteredtext);
    if ($multilang) {
        $filteredtext = preg_replace('/<span xxxlang="([a-zA-Z0-9_-]+)">/', '<span lang="${1}" class="multilang">', $filteredtext);
    }
    $cache->set($text, $filteredtext);

    return $filteredtext;
}

/**
 * Given plain text, makes it into HTML as nicely as possible.
 * May contain HTML tags already
 *
 * Do not abuse this function. It is intended as lower level formatting feature used
 * by {@see format_text()} to convert FORMAT_MOODLE to HTML. You are supposed
 * to call format_text() in most of cases.
 *
 * @param string $text The string to convert.
 * @param boolean $smiley_ignored Was used to determine if smiley characters should convert to smiley images, ignored now
 * @param boolean $para If true then the returned string will be wrapped in div tags
 * @param boolean $newlines If true then lines newline breaks will be converted to HTML newline breaks.
 * @return string
 */
function text_to_html($text, $smiley_ignored=null, $para=true, $newlines=true) {
/// Remove any whitespace that may be between HTML tags
    $text = preg_replace("~>([[:space:]]+)<~i", "><", $text);

/// Remove any returns that precede or follow HTML tags
    $text = preg_replace("~([\n\r])<~i", " <", $text);
    $text = preg_replace("~>([\n\r])~i", "> ", $text);

/// Make returns into HTML newlines.
    if ($newlines) {
        $text = nl2br($text);
    }

/// Wrap the whole thing in a div if required
    if ($para) {
        //return '<p>'.$text.'</p>'; //1.9 version
        return '<div class="text_to_html">'.$text.'</div>';
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

    if ($text === '' or $text === NULL) {
        return $text;
    }

    require_once($CFG->libdir .'/markdown.php');

    return Markdown($text);
}

/**
 * Given HTML text, make it into plain text using external function
 *
 * @param string $html The text to be converted.
 * @param integer $width Width to wrap the text at. (optional, default 75 which
 *      is a good value for email. 0 means do not limit line length.)
 * @param boolean $dolinks By default, any links in the HTML are collected, and
 *      printed as a list at the end of the HTML. If you don't want that, set this
 *      argument to false.
 * @return string plain text equivalent of the HTML.
 */
function html_to_text($html, $width = 75, $dolinks = true) {

    global $CFG;

    require_once($CFG->libdir .'/html2text.php');

    $h2t = new html2text($html, false, $dolinks, $width);
    $result = $h2t->get_text();

    return $result;
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

    $parts = explode(textlib::strtolower($needle), textlib::strtolower($haystack));

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
        if (right_to_left()) {
            $direction = ' dir="rtl"';
        } else {
            $direction = ' dir="ltr"';
        }
    }
    //Accessibility: added the 'lang' attribute to $direction, used in theme <html> tag.
    $language = str_replace('_', '-', current_language());
    @header('Content-Language: '.$language);
    return ($direction.' lang="'.$language.'" xml:lang="'.$language.'"');
}


/// STANDARD WEB PAGE PARTS ///////////////////////////////////////////////////

/**
 * Send the HTTP headers that Moodle requires.
 *
 * There is a backwards compatibility hack for legacy code
 * that needs to add custom IE compatibility directive.
 *
 * Example:
 * <code>
 * if (!isset($CFG->additionalhtmlhead)) {
 *     $CFG->additionalhtmlhead = '';
 * }
 * $CFG->additionalhtmlhead .= '<meta http-equiv="X-UA-Compatible" content="IE=8" />';
 * header('X-UA-Compatible: IE=8');
 * echo $OUTPUT->header();
 * </code>
 *
 * Please note the $CFG->additionalhtmlhead alone might not work,
 * you should send the IE compatibility header() too.
 *
 * @param string $contenttype
 * @param bool $cacheable Can this page be cached on back?
 * @return void, sends HTTP headers
 */
function send_headers($contenttype, $cacheable = true) {
    global $CFG;

    @header('Content-Type: ' . $contenttype);
    @header('Content-Script-Type: text/javascript');
    @header('Content-Style-Type: text/css');

    if (empty($CFG->additionalhtmlhead) or stripos($CFG->additionalhtmlhead, 'X-UA-Compatible') === false) {
        @header('X-UA-Compatible: IE=edge');
    }

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

    if (empty($CFG->allowframembedding)) {
        @header('X-Frame-Options: sameorigin');
    }
}

/**
 * Return the right arrow with text ('next'), and optionally embedded in a link.
 *
 * @global object
 * @param string $text HTML/plain text label (set to blank only for breadcrumb separator cases).
 * @param string $url An optional link to use in a surrounding HTML anchor.
 * @param bool $accesshide True if text should be hidden (for screen readers only).
 * @param string $addclass Additional class names for the link, or the arrow character.
 * @return string HTML string.
 */
function link_arrow_right($text, $url='', $accesshide=false, $addclass='') {
    global $OUTPUT; //TODO: move to output renderer
    $arrowclass = 'arrow ';
    if (! $url) {
        $arrowclass .= $addclass;
    }
    $arrow = '<span class="'.$arrowclass.'">'.$OUTPUT->rarrow().'</span>';
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
 *
 * @global object
 * @param string $text HTML/plain text label (set to blank only for breadcrumb separator cases).
 * @param string $url An optional link to use in a surrounding HTML anchor.
 * @param bool $accesshide True if text should be hidden (for screen readers only).
 * @param string $addclass Additional class names for the link, or the arrow character.
 * @return string HTML string.
 */
function link_arrow_left($text, $url='', $accesshide=false, $addclass='') {
    global $OUTPUT; // TODO: move to utput renderer
    $arrowclass = 'arrow ';
    if (! $url) {
        $arrowclass .= $addclass;
    }
    $arrow = '<span class="'.$arrowclass.'">'.$OUTPUT->larrow().'</span>';
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
 * Print (or return) a collapsible region, that has a caption that can
 * be clicked to expand or collapse the region.
 *
 * If JavaScript is off, then the region will always be expanded.
 *
 * @param string $contents the contents of the box.
 * @param string $classes class names added to the div that is output.
 * @param string $id id added to the div that is output. Must not be blank.
 * @param string $caption text displayed at the top. Clicking on this will cause the region to expand or contract.
 * @param string $userpref the name of the user preference that stores the user's preferred default state.
 *      (May be blank if you do not wish the state to be persisted.
 * @param boolean $default Initial collapsed state to use if the user_preference it not set.
 * @param boolean $return if true, return the HTML as a string, rather than printing it.
 * @return string|void If $return is false, returns nothing, otherwise returns a string of HTML.
 */
function print_collapsible_region($contents, $classes, $id, $caption, $userpref = '', $default = false, $return = false) {
    $output  = print_collapsible_region_start($classes, $id, $caption, $userpref, $default, true);
    $output .= $contents;
    $output .= print_collapsible_region_end(true);

    if ($return) {
        return $output;
    } else {
        echo $output;
    }
}

/**
 * Print (or return) the start of a collapsible region, that has a caption that can
 * be clicked to expand or collapse the region. If JavaScript is off, then the region
 * will always be expanded.
 *
 * @param string $classes class names added to the div that is output.
 * @param string $id id added to the div that is output. Must not be blank.
 * @param string $caption text displayed at the top. Clicking on this will cause the region to expand or contract.
 * @param string $userpref the name of the user preference that stores the user's preferred default state.
 *      (May be blank if you do not wish the state to be persisted.
 * @param boolean $default Initial collapsed state to use if the user_preference it not set.
 * @param boolean $return if true, return the HTML as a string, rather than printing it.
 * @return string|void if $return is false, returns nothing, otherwise returns a string of HTML.
 */
function print_collapsible_region_start($classes, $id, $caption, $userpref = '', $default = false, $return = false) {
    global $CFG, $PAGE, $OUTPUT;

    // Work out the initial state.
    if (!empty($userpref) and is_string($userpref)) {
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
    $PAGE->requires->js_init_call('M.util.init_collapsible_region', array($id, $userpref, get_string('clicktohideshow')));

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
 * Print a specified group's avatar.
 *
 * @global object
 * @uses CONTEXT_COURSE
 * @param array|stdClass $group A single {@link group} object OR array of groups.
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

    $context = context_course::instance($courseid);

    // If there is no picture, do nothing
    if (!$group->picture) {
        return '';
    }

    // If picture is hidden, only show to those with course:managegroups
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

    $grouppictureurl = moodle_url::make_pluginfile_url($context->id, 'group', 'icon', $group->id, '/', $file);
    $output .= '<img class="grouppicture" src="'.$grouppictureurl.'"'.
        ' alt="'.s(get_string('group').' '.$group->name).'" title="'.s($group->name).'"/>';

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
        $context = context_system::instance();
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

    global $CFG, $OUTPUT;

    $section = -1;
    $url = '';
    $menu = array();
    $doneheading = false;

    $courseformatoptions = course_get_format($course)->get_format_options();
    $coursecontext = context_course::instance($course->id);

    $menu[] = '<ul class="navmenulist"><li class="jumpto section"><span>'.$strjumpto.'</span><ul>';
    foreach ($modinfo->cms as $mod) {
        if (!$mod->has_view()) {
            // Don't show modules which you can't link to!
            continue;
        }

        // For course formats using 'numsections' do not show extra sections
        if (isset($courseformatoptions['numsections']) && $mod->sectionnum > $courseformatoptions['numsections']) {
            break;
        }

        if (!$mod->uservisible) { // do not icnlude empty sections at all
            continue;
        }

        if ($mod->sectionnum >= 0 and $section != $mod->sectionnum) {
            $thissection = $sections[$mod->sectionnum];

            if ($thissection->visible or
                    (isset($courseformatoptions['hiddensections']) and !$courseformatoptions['hiddensections']) or
                    has_capability('moodle/course:viewhiddensections', $coursecontext)) {
                $thissection->summary = strip_tags(format_string($thissection->summary,true));
                if (!$doneheading) {
                    $menu[] = '</ul></li>';
                }
                if ($course->format == 'weeks' or empty($thissection->summary)) {
                    $item = $strsection ." ". $mod->sectionnum;
                } else {
                    if (textlib::strlen($thissection->summary) < ($width-3)) {
                        $item = $thissection->summary;
                    } else {
                        $item = textlib::substr($thissection->summary, 0, $width).'...';
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
        $mod->name = strip_tags(format_string($mod->name ,true));
        if (textlib::strlen($mod->name) > ($width+5)) {
            $mod->name = textlib::substr($mod->name, 0, $width).'...';
        }
        if (!$mod->visible) {
            $mod->name = '('.$mod->name.')';
        }
        $class = 'activity '.$mod->modname;
        $class .= ($cmid == $mod->id) ? ' selected' : '';
        $menu[] = '<li class="'.$class.'">'.
                  '<img src="'.$OUTPUT->pix_url('icon', $mod->modname) . '" alt="" />'.
                  '<a href="'.$CFG->wwwroot.'/mod/'.$url.'">'.$mod->name.'</a></li>';
    }

    if ($doneheading) {
        $menu[] = '</ul></li>';
    }
    $menu[] = '</ul></li></ul>';

    return implode("\n", $menu);
}

/**
 * Prints a grade menu (as part of an existing form) with help
 * Showing all possible numerical grades and scales
 *
 * @todo Finish documenting this function
 * @todo Deprecate: this is only used in a few contrib modules
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
    $output .= html_writer::select($grades, $name, $current, false);

    $linkobject = '<span class="helplink"><img class="iconhelp" alt="'.$strscales.'" src="'.$OUTPUT->pix_url('help') . '" /></span>';
    $link = new moodle_url('/course/scales.php', array('id'=>$courseid, 'list'=>1));
    $action = new popup_action('click', $link, 'ratingscales', array('height' => 400, 'width' => 500));
    $output .= $OUTPUT->action_link($link, $linkobject, $action, array('title'=>$strscales));

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
 * Print a message and exit.
 *
 * @param string $message The message to print in the notice
 * @param string $link The link to use for the continue button
 * @param object $course A course object
 * @return void This function simply exits
 */
function notice ($message, $link='', $course=NULL) {
    global $CFG, $SITE, $COURSE, $PAGE, $OUTPUT;

    $message = clean_text($message);   // In case nasties are in here

    if (CLI_SCRIPT) {
        echo("!!$message!!\n");
        exit(1); // no success
    }

    if (!$PAGE->headerprinted) {
        //header not yet printed
        $PAGE->set_title(get_string('notice'));
        echo $OUTPUT->header();
    } else {
        echo $OUTPUT->container_end_all(false);
    }

    echo $OUTPUT->box($message, 'generalbox', 'notice');
    echo $OUTPUT->continue_button($link);

    echo $OUTPUT->footer();
    exit(1); // general error code
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
 * @param moodle_url|string $url A moodle_url to redirect to. Strings are not to be trusted!
 * @param string $message The message to display to the user
 * @param int $delay The delay before redirecting
 * @return void - does not return!
 */
function redirect($url, $message='', $delay=-1) {
    global $OUTPUT, $PAGE, $SESSION, $CFG;

    if (CLI_SCRIPT or AJAX_SCRIPT) {
        // this is wrong - developers should not use redirect in these scripts,
        // but it should not be very likely
        throw new moodle_exception('redirecterrordetected', 'error');
    }

    // prevent debug errors - make sure context is properly initialised
    if ($PAGE) {
        $PAGE->set_context(null);
        $PAGE->set_pagelayout('redirect');  // No header and footer needed
    }

    if ($url instanceof moodle_url) {
        $url = $url->out(false);
    }

    $debugdisableredirect = false;
    do {
        if (defined('DEBUGGING_PRINTED')) {
            // some debugging already printed, no need to look more
            $debugdisableredirect = true;
            break;
        }

        if (empty($CFG->debugdisplay) or empty($CFG->debug)) {
            // no errors should be displayed
            break;
        }

        if (!function_exists('error_get_last') or !$lasterror = error_get_last()) {
            break;
        }

        if (!($lasterror['type'] & $CFG->debug)) {
            //last error not interesting
            break;
        }

        // watch out here, @hidden() errors are returned from error_get_last() too
        if (headers_sent()) {
            //we already started printing something - that means errors likely printed
            $debugdisableredirect = true;
            break;
        }

        if (ob_get_level() and ob_get_contents()) {
            // there is something waiting to be printed, hopefully it is the errors,
            // but it might be some error hidden by @ too - such as the timezone mess from setup.php
            $debugdisableredirect = true;
            break;
        }
    } while (false);

    // Technically, HTTP/1.1 requires Location: header to contain the absolute path.
    // (In practice browsers accept relative paths - but still, might as well do it properly.)
    // This code turns relative into absolute.
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

    // Sanitise url - we can not rely on moodle_url or our URL cleaning
    // because they do not support all valid external URLs
    $url = preg_replace('/[\x00-\x1F\x7F]/', '', $url);
    $url = str_replace('"', '%22', $url);
    $encodedurl = preg_replace("/\&(?![a-zA-Z0-9#]{1,8};)/", "&amp;", $url);
    $encodedurl = preg_replace('/^.*href="([^"]*)".*$/', "\\1", clean_text('<a href="'.$encodedurl.'" />', FORMAT_HTML));
    $url = str_replace('&amp;', '&', $encodedurl);

    if (!empty($message)) {
        if ($delay === -1 || !is_numeric($delay)) {
            $delay = 3;
        }
        $message = clean_text($message);
    } else {
        $message = get_string('pageshouldredirect');
        $delay = 0;
    }

    if (defined('MDL_PERF') || (!empty($CFG->perfdebug) and $CFG->perfdebug > 7)) {
        if (defined('MDL_PERFTOLOG') && !function_exists('register_shutdown_function')) {
            $perf = get_performance_info();
            error_log("PERF: " . $perf['txt']);
        }
    }

    if ($delay == 0 && !$debugdisableredirect && !headers_sent()) {
        // workaround for IIS bug http://support.microsoft.com/kb/q176113/
        if (session_id()) {
            session_get_instance()->write_close();
        }

        //302 might not work for POST requests, 303 is ignored by obsolete clients.
        @header($_SERVER['SERVER_PROTOCOL'] . ' 303 See Other');
        @header('Location: '.$url);
        echo bootstrap_renderer::plain_redirect_message($encodedurl);
        exit;
    }

    // Include a redirect message, even with a HTTP redirect, because that is recommended practice.
    if ($PAGE) {
        $CFG->docroot = false; // to prevent the link to moodle docs from being displayed on redirect page.
        echo $OUTPUT->redirect_message($encodedurl, $message, $delay, $debugdisableredirect);
        exit;
    } else {
        echo bootstrap_renderer::early_redirect_message($encodedurl, $message, $delay);
        exit;
    }
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
        if (rand(0,2) && $email{$i}!='@') { //MDL-20619 some browsers have problems unobfuscating @
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
    $length = textlib::strlen($plaintext);
    $obfuscated='';
    $prev_obfuscated = false;
    while ($i < $length) {
        $char = textlib::substr($plaintext, $i, 1);
        $ord = textlib::utf8ord($char);
        $numerical = ($ord >= ord('0')) && ($ord <= ord('9'));
        if ($prev_obfuscated and $numerical ) {
            $obfuscated.='&#'.$ord.';';
        } else if (rand(0,2)) {
            $obfuscated.='&#'.$ord.';';
            $prev_obfuscated = true;
        } else {
            $obfuscated.=$char;
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
 * @param string $label The text to displayed as hyperlink to $email
 * @param boolean $dimmed If true then use css class 'dimmed' for hyperlink
 * @param string $subject The subject of the email in the mailto link
 * @param string $body The content of the email in the mailto link
 * @return string The obfuscated mailto link
 */
function obfuscate_mailto($email, $label='', $dimmed=false, $subject = '', $body = '') {

    if (empty($label)) {
        $label = $email;
    }

    $label = obfuscate_text($label);
    $email = obfuscate_email($email);
    $mailto = obfuscate_text('mailto');
    $url = new moodle_url("mailto:$email");
    $attrs = array();

    if (!empty($subject)) {
        $url->param('subject', format_string($subject));
    }
    if (!empty($body)) {
        $url->param('body', format_string($body));
    }

    // Use the obfuscated mailto
    $url = preg_replace('/^mailto/', $mailto, $url->out());

    if ($dimmed) {
        $attrs['title'] = get_string('emaildisable');
        $attrs['class'] = 'dimmed';
    }

    return html_writer::link($url, $label, $attrs);
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
 * Prints a maintenance message from $CFG->maintenance_message or default if empty
 * @return void
 */
function print_maintenance_message() {
    global $CFG, $SITE, $PAGE, $OUTPUT;

    $PAGE->set_pagetype('maintenance-message');
    $PAGE->set_pagelayout('maintenance');
    $PAGE->set_title(strip_tags($SITE->fullname));
    $PAGE->set_heading($SITE->fullname);
    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('sitemaintenance', 'admin'));
    if (isset($CFG->maintenance_message) and !html_is_blank($CFG->maintenance_message)) {
        echo $OUTPUT->box_start('maintenance_message generalbox boxwidthwide boxaligncenter');
        echo $CFG->maintenance_message;
        echo $OUTPUT->box_end();
    }
    echo $OUTPUT->footer();
    die;
}

/**
 * Returns a string containing a nested list, suitable for formatting into tabs with CSS.
 *
 * It is not recommended to use this function in Moodle 2.5 but it is left for backward
 * compartibility.
 *
 * Example how to print a single line tabs:
 * $rows = array(
 *    new tabobject(...),
 *    new tabobject(...)
 * );
 * echo $OUTPUT->tabtree($rows, $selectedid);
 *
 * Multiple row tabs may not look good on some devices but if you want to use them
 * you can specify ->subtree for the active tabobject.
 *
 * @param array $tabrows An array of rows where each row is an array of tab objects
 * @param string $selected  The id of the selected tab (whatever row it's on)
 * @param array  $inactive  An array of ids of inactive tabs that are not selectable.
 * @param array  $activated An array of ids of other tabs that are currently activated
 * @param bool $return If true output is returned rather then echo'd
 **/
function print_tabs($tabrows, $selected = null, $inactive = null, $activated = null, $return = false) {
    global $OUTPUT;

    $tabrows = array_reverse($tabrows);
    $subtree = array();
    foreach ($tabrows as $row) {
        $tree = array();

        foreach ($row as $tab) {
            $tab->inactive = is_array($inactive) && in_array((string)$tab->id, $inactive);
            $tab->activated = is_array($activated) && in_array((string)$tab->id, $activated);
            $tab->selected = (string)$tab->id == $selected;

            if ($tab->activated || $tab->selected) {
                $tab->subtree = $subtree;
            }
            $tree[] = $tab;
        }
        $subtree = $tree;
    }
    $output = $OUTPUT->tabtree($subtree);
    if ($return) {
        return $output;
    } else {
        print $output;
        return !empty($output);
    }
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
 * 3)  debugging('annoying debug message only for developers', DEBUG_DEVELOPER);
 * 4)  if (debugging()) { perform extra debugging operations (do not use print or echo) }
 *
 * In code blocks controlled by debugging() (such as example 4)
 * any output should be routed via debugging() itself, or the lower-level
 * trigger_error() or error_log(). Using echo or print will break XHTML
 * JS and HTTP headers.
 *
 * It is also possible to define NO_DEBUG_DISPLAY which redirects the message to error_log.
 *
 * @uses DEBUG_NORMAL
 * @param string $message a message to print
 * @param int $level the level at which this debugging statement should show
 * @param array $backtrace use different backtrace
 * @return bool
 */
function debugging($message = '', $level = DEBUG_NORMAL, $backtrace = null) {
    global $CFG, $USER;

    $forcedebug = false;
    if (!empty($CFG->debugusers) && $USER) {
        $debugusers = explode(',', $CFG->debugusers);
        $forcedebug = in_array($USER->id, $debugusers);
    }

    if (!$forcedebug and (empty($CFG->debug) || ($CFG->debug != -1 and $CFG->debug < $level))) {
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
        if (PHPUNIT_TEST) {
            if (phpunit_util::debugging_triggered($message, $level, $from)) {
                // We are inside test, the debug message was logged.
                return true;
            }
        }

        if (NO_DEBUG_DISPLAY) {
            // script does not want any errors or debugging in output,
            // we send the info to error log instead
            error_log('Debugging: ' . $message . $from);

        } else if ($forcedebug or $CFG->debugdisplay) {
            if (!defined('DEBUGGING_PRINTED')) {
                define('DEBUGGING_PRINTED', 1); // indicates we have printed something
            }
            if (CLI_SCRIPT) {
                echo "++ $message ++\n$from";
            } else {
                echo '<div class="notifytiny debuggingmessage">' . $message . $from . '</div>';
            }

        } else {
            trigger_error($message . $from, E_USER_NOTICE);
        }
    }
    return true;
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
 * @return boolean true if the current language is right-to-left (Hebrew, Arabic etc)
 */
function right_to_left() {
    return (get_string('thisdirection', 'langconfig') === 'rtl');
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
 * @todo Use a central function to create the popup calls all over Moodle and
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
 * - call update or update_full() or update() repeatedly
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package moodlecore
 */
class progress_bar {
    /** @var string html id */
    private $html_id;
    /** @var int total width */
    private $width;
    /** @var int last percentage printed */
    private $percent = 0;
    /** @var int time when last printed */
    private $lastupdate = 0;
    /** @var int when did we start printing this */
    private $time_start = 0;

    /**
     * Constructor
     *
     * @param string $html_id
     * @param int $width
     * @param bool $autostart Default to false
     * @return void, prints JS code if $autostart true
     */
    public function __construct($html_id = '', $width = 500, $autostart = false) {
        if (!empty($html_id)) {
            $this->html_id  = $html_id;
        } else {
            $this->html_id  = 'pbar_'.uniqid();
        }

        $this->width = $width;

        if ($autostart){
            $this->create();
        }
    }

    /**
      * Create a new progress bar, this function will output html.
      *
      * @return void Echo's output
      */
    public function create() {
        $this->time_start = microtime(true);
        if (CLI_SCRIPT) {
            return; // temporary solution for cli scripts
        }
        $htmlcode = <<<EOT
        <div style="text-align:center;width:{$this->width}px;clear:both;padding:0;margin:0 auto;">
            <h2 id="status_{$this->html_id}" style="text-align: center;margin:0 auto"></h2>
            <p id="time_{$this->html_id}"></p>
            <div id="bar_{$this->html_id}" style="border-style:solid;border-width:1px;width:500px;height:50px;">
                <div id="progress_{$this->html_id}"
                style="text-align:center;background:#FFCC66;width:4px;border:1px
                solid gray;height:38px; padding-top:10px;">&nbsp;<span id="pt_{$this->html_id}"></span>
                </div>
            </div>
        </div>
EOT;
        flush();
        echo $htmlcode;
        flush();
    }

    /**
     * Update the progress bar
     *
     * @param int $percent from 1-100
     * @param string $msg
     * @return void Echo's output
     */
    private function _update($percent, $msg) {
        if (empty($this->time_start)) {
            throw new coding_exception('You must call create() (or use the $autostart ' .
                    'argument to the constructor) before you try updating the progress bar.');
        }

        if (CLI_SCRIPT) {
            return; // temporary solution for cli scripts
        }

        $es = $this->estimate($percent);

        if ($es === null) {
            // always do the first and last updates
            $es = "?";
        } else if ($es == 0) {
            // always do the last updates
        } else if ($this->lastupdate + 20 < time()) {
            // we must update otherwise browser would time out
        } else if (round($this->percent, 2) === round($percent, 2)) {
            // no significant change, no need to update anything
            return;
        }

        $this->percent = $percent;
        $this->lastupdate = microtime(true);

        $w = ($this->percent/100) * $this->width;
        echo html_writer::script(js_writer::function_call('update_progress_bar', array($this->html_id, $w, $this->percent, $msg, $es)));
        flush();
    }

    /**
      * Estimate how much time it is going to take.
      *
      * @param int $curtime the time call this function
      * @param int $percent from 1-100
      * @return mixed Null (unknown), or int
      */
    private function estimate($pt) {
        if ($this->lastupdate == 0) {
            return null;
        }
        if ($pt < 0.00001) {
            return null; // we do not know yet how long it will take
        }
        if ($pt > 99.99999) {
            return 0; // nearly done, right?
        }
        $consumed = microtime(true) - $this->time_start;
        if ($consumed < 0.001) {
            return null;
        }

        return (100 - $pt) * ($consumed / $pt);
    }

    /**
      * Update progress bar according percent
      *
      * @param int $percent from 1-100
      * @param string $msg the message needed to be shown
      */
    public function update_full($percent, $msg) {
        $percent = max(min($percent, 100), 0);
        $this->_update($percent, $msg);
    }

    /**
      * Update progress bar according the number of tasks
      *
      * @param int $cur current task number
      * @param int $total total task number
      * @param string $msg message
      */
    public function update($cur, $total, $msg) {
        $percent = ($cur / $total) * 100;
        $this->update_full($percent, $msg);
    }

    /**
     * Restart the progress bar.
     */
    public function restart() {
        $this->percent    = 0;
        $this->lastupdate = 0;
        $this->time_start = 0;
    }
}

/**
 * Use this class from long operations where you want to output occasional information about
 * what is going on, but don't know if, or in what format, the output should be.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package moodlecore
 */
abstract class progress_trace {
    /**
     * Output an progress message in whatever format.
     *
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
 * This subclass of progress_trace does not ouput anything.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package moodlecore
 */
class null_progress_trace extends progress_trace {
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
 * This subclass of progress_trace outputs to plain text.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package moodlecore
 */
class text_progress_trace extends progress_trace {
    /**
     * Output the trace message.
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
 * This subclass of progress_trace outputs as HTML.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package moodlecore
 */
class html_progress_trace extends progress_trace {
    /**
     * Output the trace message.
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
class html_list_progress_trace extends progress_trace {
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
 * This subclass of progress_trace outputs to error log.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package moodlecore
 */
class error_log_progress_trace extends progress_trace {
    /** @var string log prefix */
    protected $prefix;

    /**
     * Constructor.
     * @param string $prefix optional log prefix
     */
    public function __construct($prefix = '') {
        $this->prefix = $prefix;
    }

    /**
     * Output the trace message.
     *
     * @param string $message
     * @param int $depth
     * @return void Output is sent to error log.
     */
    public function output($message, $depth = 0) {
        error_log($this->prefix . str_repeat('  ', $depth) . $message);
    }
}

/**
 * Special type of trace that can be used for catching of
 * output of other traces.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package moodlecore
 */
class progress_trace_buffer extends progress_trace {
    /** @var progres_trace */
    protected $trace;
    /** @var bool do we pass output out */
    protected $passthrough;
    /** @var string output buffer */
    protected $buffer;

    /**
     * Constructor.
     *
     * @param progress_trace $trace
     * @param bool $passthrough true means output and buffer, false means just buffer and no output
     */
    public function __construct(progress_trace $trace, $passthrough = true) {
        $this->trace       = $trace;
        $this->passthrough = $passthrough;
        $this->buffer      = '';
    }

    /**
     * Output the trace message.
     *
     * @param string $message the message to output.
     * @param int $depth indent depth for this message.
     * @return void output stored in buffer
     */
    public function output($message, $depth = 0) {
        ob_start();
        $this->trace->output($message, $depth);
        $this->buffer .= ob_get_contents();
        if ($this->passthrough) {
            ob_end_flush();
        } else {
            ob_end_clean();
        }
    }

    /**
     * Called when the processing is finished.
     */
    public function finished() {
        ob_start();
        $this->trace->finished();
        $this->buffer .= ob_get_contents();
        if ($this->passthrough) {
            ob_end_flush();
        } else {
            ob_end_clean();
        }
    }

    /**
     * Reset internal text buffer.
     */
    public function reset_buffer() {
        $this->buffer = '';
    }

    /**
     * Return internal text buffer.
     * @return string buffered plain text
     */
    public function get_buffer() {
        return $this->buffer;
    }
}

/**
 * Special type of trace that can be used for redirecting to multiple
 * other traces.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package moodlecore
 */
class combined_progress_trace extends progress_trace {
    protected $traces;

    /**
     * @param array $traces multiple traces
     */
    public function __construct(array $traces) {
        $this->traces = $traces;
    }

    /**
     * Output an progress message in whatever format.
     *
     * @param string $message the message to output.
     * @param integer $depth indent depth for this message.
     */
    public function output($message, $depth = 0) {
        foreach($this->traces as $trace) {
            $trace->output($message, $depth);
        }
    }

    /**
     * Called when the processing is finished.
     */
    public function finished() {
        foreach($this->traces as $trace) {
            $trace->finished();
        }
    }
}

/**
 * Returns a localized sentence in the current language summarizing the current password policy
 *
 * @todo this should be handled by a function/method in the language pack library once we have a support for it
 * @uses $CFG
 * @return string
 */
function print_password_policy() {
    global $CFG;

    $message = '';
    if (!empty($CFG->passwordpolicy)) {
        $messages = array();
        $messages[] = get_string('informminpasswordlength', 'auth', $CFG->minpasswordlength);
        if (!empty($CFG->minpassworddigits)) {
            $messages[] = get_string('informminpassworddigits', 'auth', $CFG->minpassworddigits);
        }
        if (!empty($CFG->minpasswordlower)) {
            $messages[] = get_string('informminpasswordlower', 'auth', $CFG->minpasswordlower);
        }
        if (!empty($CFG->minpasswordupper)) {
            $messages[] = get_string('informminpasswordupper', 'auth', $CFG->minpasswordupper);
        }
        if (!empty($CFG->minpasswordnonalphanum)) {
            $messages[] = get_string('informminpasswordnonalphanum', 'auth', $CFG->minpasswordnonalphanum);
        }

        $messages = join(', ', $messages); // this is ugly but we do not have anything better yet...
        $message = get_string('informpasswordpolicy', 'auth', $messages);
    }
    return $message;
}

/**
 * Get the value of a help string fully prepared for display in the current language.
 *
 * @param string $identifier The identifier of the string to search for.
 * @param string $component The module the string is associated with.
 * @param boolean $ajax Whether this help is called from an AJAX script.
 *                This is used to influence text formatting and determines
 *                which format to output the doclink in.
 * @return Object An object containing:
 * - heading: Any heading that there may be for this help string.
 * - text: The wiki-formatted help string.
 * - doclink: An object containing a link, the linktext, and any additional
 *            CSS classes to apply to that link. Only present if $ajax = false.
 * - completedoclink: A text representation of the doclink. Only present if $ajax = true.
 */
function get_formatted_help_string($identifier, $component, $ajax = false) {
    global $CFG, $OUTPUT;
    $sm = get_string_manager();

    if (!$sm->string_exists($identifier, $component) ||
        !$sm->string_exists($identifier . '_help', $component)) {
        // Strings in the on-disk cache may be dirty - try to rebuild it and check again.
        $sm->load_component_strings($component, current_language(), true);
    }

    $data = new stdClass();

    if ($sm->string_exists($identifier, $component)) {
        $data->heading = format_string(get_string($identifier, $component));
    } else {
        // Gracefully fall back to an empty string.
        $data->heading = '';
    }

    if ($sm->string_exists($identifier . '_help', $component)) {
        $options = new stdClass();
        $options->trusted = false;
        $options->noclean = false;
        $options->smiley = false;
        $options->filter = false;
        $options->para = true;
        $options->newlines = false;
        $options->overflowdiv = !$ajax;

        // Should be simple wiki only MDL-21695.
        $data->text =  format_text(get_string($identifier.'_help', $component), FORMAT_MARKDOWN, $options);

        $helplink = $identifier . '_link';
        if ($sm->string_exists($helplink, $component)) {  // Link to further info in Moodle docs
            $link = get_string($helplink, $component);
            $linktext = get_string('morehelp');

            $data->doclink = new stdClass();
            $url = new moodle_url(get_docs_url($link));
            if ($ajax) {
                $data->doclink->link = $url->out();
                $data->doclink->linktext = $linktext;
                $data->doclink->class = ($CFG->doctonewwindow) ? 'helplinkpopup' : '';
            } else {
                $data->completedoclink = html_writer::tag('div', $OUTPUT->doc_link($link, $linktext), array('class' => 'helpdoclink'));
            }
        }
    } else {
        $data->text = html_writer::tag('p',
                html_writer::tag('strong', 'TODO') . ": missing help string [{$identifier}_help, {$component}]");
    }
    return $data;
}
