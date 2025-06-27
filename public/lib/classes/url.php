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

namespace core;

use core\context\user as context_user;
use core\exception\coding_exception;
use core\exception\moodle_exception;
use Psr\Http\Message\UriInterface;

/**
 * Class for creating and manipulating urls.
 *
 * It can be used in moodle pages where config.php has been included without any further includes.
 *
 * It is useful for manipulating urls with long lists of params.
 * One situation where it will be useful is a page which links to itself to perform various actions
 * and / or to process form data. A url object:
 * can be created for a page to refer to itself with all the proper get params being passed from page call to
 * page call and methods can be used to output a url including all the params, optionally adding and overriding
 * params and can also be used to
 *     - output the url without any get params
 *     - and output the params as hidden fields to be output within a form
 *
 * @copyright 2007 jamiesensei
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package core
 */
class url {
    /**
     * Scheme, ex.: http, https
     * @var string
     */
    protected $scheme = '';

    /**
     * Hostname.
     * @var string
     */
    protected $host = '';

    /**
     * Port number, empty means default 80 or 443 in case of http.
     * @var int
     */
    protected $port = '';

    /**
     * Username for http auth.
     * @var string
     */
    protected $user = '';

    /**
     * Password for http auth.
     * @var string
     */
    protected $pass = '';

    /**
     * Script path.
     * @var string
     */
    protected $path = '';

    /**
     * Optional slash argument value.
     * @var string
     */
    protected $slashargument = '';

    /**
     * Anchor, may be also empty, null means none.
     * @var string
     */
    protected $anchor = null;

    /**
     * Url parameters as associative array.
     * @var array
     */
    protected $params = [];

    /**
     * Create new instance of url.
     *
     * @param self|string $url - moodle_url means make a copy of another
     *      moodle_url and change parameters, string means full url or shortened
     *      form (ex.: '/course/view.php'). It is strongly encouraged to not include
     *      query string because it may result in double encoded values. Use the
     *      $params instead. For admin URLs, just use /admin/script.php, this
     *      class takes care of the $CFG->admin issue.
     * @param null|array $params these params override current params or add new
     * @param string $anchor The anchor to use as part of the URL if there is one.
     * @throws moodle_exception
     */
    public function __construct(
        $url,
        ?array $params = null,
        $anchor = null,
    ) {
        global $CFG;

        if ($url instanceof self) {
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
            $url = $url ?? '';
            // Detect if anchor used.
            $apos = strpos($url, '#');
            if ($apos !== false) {
                $anchor = substr($url, $apos);
                $anchor = ltrim($anchor, '#');
                $this->set_anchor($anchor);
                $url = substr($url, 0, $apos);
            }

            // Normalise shortened form of our url ex.: '/course/view.php'.
            if (strpos($url, '/') === 0) {
                $url = $CFG->wwwroot . $url;
            }

            if ($CFG->admin !== 'admin') {
                if (strpos($url, "$CFG->wwwroot/admin/") === 0) {
                    $url = str_replace("$CFG->wwwroot/admin/", "$CFG->wwwroot/$CFG->admin/", $url);
                }
            }

            // Parse the $url.
            $parts = parse_url($url);
            if ($parts === false) {
                throw new moodle_exception('invalidurl');
            }
            if (isset($parts['query'])) {
                // Note: the values may not be correctly decoded, url parameters should be always passed as array.
                $out = [];
                parse_str(str_replace('&amp;', '&', $parts['query']), $out);
                $this->params($out);
            }
            unset($parts['query']);
            foreach ($parts as $key => $value) {
                $this->$key = $value;
            }

            // Detect slashargument value from path - we do not support directory names ending with .php.
            $pos = strpos($this->path, '.php/');
            if ($pos !== false) {
                $this->slashargument = substr($this->path, $pos + 4);
                $this->path = substr($this->path, 0, $pos + 4);
            }
        }

        $this->params($params);
        if ($anchor !== null) {
            $this->anchor = (string)$anchor;
        }
    }

    /**
     * Add an array of params to the params for this url.
     *
     * The added params override existing ones if they have the same name.
     *
     * @param null|array $params Array of parameters to add. Note all values that are not arrays are cast to strings.
     * @return array Array of Params for url.
     * @throws coding_exception
     */
    public function params(?array $params = null) {
        $params = (array)$params;
        $params = $this->clean_url_params($params);
        $this->params = array_merge($this->params, $params);
        return $this->params;
    }

    /**
     * Converts given URL parameter values that are not arrays into strings.
     *
     * This is to maintain the same behaviour as the original params() function.
     *
     * @param array $params
     * @return array
     */
    private function clean_url_params(array $params): array {
        // Convert all values to strings.
        // This was the original implementation of the params function,
        // which we have kept for backwards compatibility.
        array_walk_recursive($params, function (&$value) {
            if (is_object($value) && !is_a($value, \Stringable::class)) {
                throw new coding_exception('Url parameters values can not be objects, unless __toString() is defined!');
            }
            $value = (string) $value;
        });
        return $params;
    }

    /**
     * Remove all params if no arguments passed.
     * Remove selected params if arguments are passed.
     *
     * Can be called as either remove_params('param1', 'param2')
     * or remove_params(array('param1', 'param2')).
     *
     * @param string[]|string ...$params either an array of param names, or 1..n string params to remove as args.
     * @return array url parameters
     */
    public function remove_params(...$params) {
        if (empty($params)) {
            return $this->params;
        }

        $firstparam = reset($params);
        if (is_array($firstparam)) {
            $params = $firstparam;
        }

        foreach ($params as $param) {
            unset($this->params[$param]);
        }
        return $this->params;
    }

    /**
     * Remove all url parameters.
     *
     * @param array $unused Unused param
     */
    public function remove_all_params($unused = null) {
        $this->params = [];
        $this->slashargument = '';
    }

    /**
     * Add a param to the params for this url.
     *
     * The added param overrides existing one if they have the same name.
     *
     * @param string $paramname name
     * @param string $newvalue Param value. If new value specified current value is overriden or parameter is added
     * @return array|string|null parameter value, null if parameter does not exist.
     */
    public function param($paramname, $newvalue = '') {
        if (func_num_args() > 1) {
            // Set new value.
            $this->params([$paramname => $newvalue]);
        }
        if (isset($this->params[$paramname])) {
            return $this->params[$paramname];
        } else {
            return null;
        }
    }

    /**
     * Merges parameters.
     *
     * @param null|array $overrideparams
     * @return array merged parameters
     */
    protected function merge_overrideparams(?array $overrideparams = null) {
        $overrideparams = (array) $overrideparams;
        $overrideparams = $this->clean_url_params($overrideparams);
        return array_merge($this->params, $overrideparams);
    }

    /**
     * Recursively transforms the given array of values to query string parts.
     *
     * Example query string parts: a=2, a[0]=2
     *
     * @param array $data Data to encode into query string parts. Can be a multi level array. All end values must be strings.
     * @return array array of query string parts. All parts are rawurlencoded.
     */
    private function recursively_transform_params_to_query_string_parts(array $data): array {
        $stringparams = [];

        // Define a recursive function to encode the array into a set of string params.
        // We need to do this recursively, so that multi level array parameters are properly supported.
        $parsestringparams = function (array $data) use (&$stringparams, &$parsestringparams) {
            foreach ($data as $key => $value) {
                // If this is an array, rewrite the $value keys to track the position in the array.
                // and pass back to this function recursively until the values are no longer arrays.
                // E.g. if $key is 'a' and $value was [0 => true, 1 => false]
                // the new array becomes ['a[0]' => true, 'a[1]' => false].
                if (is_array($value)) {
                    $newvalue = [];
                    foreach ($value as $innerkey => $innervalue) {
                        $newkey = $key . '[' . $innerkey . ']';
                        $newvalue[$newkey] = $innervalue;
                    }
                    $parsestringparams($newvalue);
                } else {
                    // Else no more arrays to traverse - build the final query string part.
                    // We enforce that all end values are strings for consistency.
                    // When params() is used, it will convert all params given to strings.
                    // This will catch out anyone setting the params property directly.
                    if (!is_string($value)) {
                        throw new coding_exception('Unexpected query string value type.
All values that are not arrays should be a string.');
                    }

                    if (isset($value) && $value !== '') {
                        $stringparams[] = rawurlencode($key) . '=' . rawurlencode($value);
                    } else {
                        $stringparams[] = rawurlencode($key);
                    }
                }
            }
        };

        $parsestringparams($data);

        return $stringparams;
    }

    /**
     * Get the params as as a query string.
     *
     * This method should not be used outside of this method.
     *
     * @param bool $escaped Use &amp; as params separator instead of plain &
     * @param null|array $overrideparams params to add to the output params, these
     *      override existing ones with the same name.
     * @return string query string that can be added to a url.
     */
    public function get_query_string($escaped = true, ?array $overrideparams = null) {
        if ($overrideparams !== null) {
            $params = $this->merge_overrideparams($overrideparams);
        } else {
            $params = $this->params;
        }

        $stringparams = $this->recursively_transform_params_to_query_string_parts($params);

        if ($escaped) {
            return implode('&amp;', $stringparams);
        } else {
            return implode('&', $stringparams);
        }
    }

    /**
     * Get the url params as an array of key => value pairs.
     *
     * This helps in handling cases where url params contain arrays.
     *
     * @return array params array for templates.
     */
    public function export_params_for_template(): array {
        $querystringparts = $this->recursively_transform_params_to_query_string_parts($this->params);

        return array_map(function ($value) {
            // First urldecode it, they are encoded by default.
            $value = rawurldecode($value);

            // Now separate the parts into name and value, splitting only on the first '=' sign.
            // There may be more = signs (e.g. base64, encoded urls, etc...) that we don't want to split on.
            $parts = explode('=', $value, 2);

            // Parts must be of length 1 or 2, anything else is an invalid.
            if (count($parts) !== 1 && count($parts) !== 2) {
                throw new coding_exception('Invalid query string construction, unexpected number of parts');
            }

            // There might not always be a '=' e.g. when the value is an empty string.
            // in this case, just fallback to an empty string.
            $name = $parts[0];
            $value = $parts[1] ?? '';

            return ['name' => $name, 'value' => $value];
        }, $querystringparts);
    }

    /**
     * Shortcut for printing of encoded URL.
     *
     * @return string
     */
    public function __toString() {
        return $this->out(true);
    }

    /**
     * Output url.
     *
     * If you use the returned URL in HTML code, you want the escaped ampersands. If you use
     * the returned URL in HTTP headers, you want $escaped=false.
     *
     * @param bool $escaped Use &amp; as params separator instead of plain &
     * @param null|array $overrideparams params to add to the output url, these override existing ones with the same name.
     * @return string Resulting URL
     */
    public function out($escaped = true, ?array $overrideparams = null) {

        global $CFG;

        if (!is_bool($escaped)) {
            debugging('Escape parameter must be of type boolean, ' . gettype($escaped) . ' given instead.');
        }

        $url = $this;

        // Allow url's to be rewritten by a plugin.
        if (isset($CFG->urlrewriteclass) && !isset($CFG->upgraderunning)) {
            $class = $CFG->urlrewriteclass;
            $pluginurl = $class::url_rewrite($url);
            if ($pluginurl instanceof url) {
                $url = $pluginurl;
            }
        }

        return $url->raw_out($escaped, $overrideparams);
    }

    /**
     * Output url without any rewrites
     *
     * This is identical in signature and use to out() but doesn't call the rewrite handler.
     *
     * @param bool $escaped Use &amp; as params separator instead of plain &
     * @param null|array $overrideparams params to add to the output url, these override existing ones with the same name.
     * @return string Resulting URL
     */
    public function raw_out($escaped = true, ?array $overrideparams = null) {
        if (!is_bool($escaped)) {
            debugging('Escape parameter must be of type boolean, ' . gettype($escaped) . ' given instead.');
        }

        $uri = $this->out_omit_querystring() . $this->slashargument;

        $querystring = $this->get_query_string($escaped, $overrideparams);
        if ($querystring !== '') {
            $uri .= '?' . $querystring;
        }

        $uri .= $this->get_encoded_anchor();

        return $uri;
    }

    /**
     * Encode the anchor according to RFC 3986.
     *
     * @return string The encoded anchor
     */
    public function get_encoded_anchor(): string {
        if (is_null($this->anchor)) {
            return '';
        }

        // RFC 3986 allows the following characters in a fragment without them being encoded:
        // pct-encoded: "%" HEXDIG HEXDIG
        // unreserved:  ALPHA / DIGIT / "-" / "." / "_" / "~" /
        // sub-delims:  "!" / "$" / "&" / "'" / "(" / ")" / "*" / "+" / "," / ";" / "=" / ":" / "@"
        // fragment:    "/" / "?"
        //
        // All other characters should be encoded.
        // These should not be encoded in the fragment unless they were already encoded.

        // The following characters are allowed in the fragment without encoding.
        // In addition to this list is pct-encoded, but we can't easily handle this with a regular expression.
        $allowed = 'a-zA-Z0-9\\-._~!$&\'()*+,;=:@\/?';
        $anchor = '#';

        $remainder = $this->anchor;
        do {
            // Split the string on any %.
            $parts = explode('%', $remainder, 2);
            $anchorparts = array_shift($parts);

            // The first part can go through our preg_replace_callback to quote any relevant characters.
            $anchor .= preg_replace_callback(
                '/[^' . $allowed . ']/',
                fn ($matches) => rawurlencode($matches[0]),
                $anchorparts,
            );

            // The second part _might_ be a valid pct-encoded character.
            if (count($parts) === 0) {
                break;
            }

            // If the second part is a valid pct-encoded character, append it to the anchor.
            $remainder = array_shift($parts);
            if (preg_match('/^[a-fA-F0-9]{2}/', $remainder, $matches)) {
                $anchor .= "%{$matches[0]}";
                $remainder = substr($remainder, 2);
            } else {
                // This was not a valid pct-encoded character. Encode the % and continue with the next part.
                $anchor .= rawurlencode('%');
            }
        } while (strlen($remainder) > 0);

        return $anchor;
    }

    /**
     * Returns url without parameters, everything before '?'.
     *
     * @param bool $includeanchor if {@see self::anchor} is defined, should it be returned?
     * @return string
     */
    public function out_omit_querystring($includeanchor = false) {

        $uri = $this->scheme ? $this->scheme . ':' . ((strtolower($this->scheme) == 'mailto') ? '' : '//') : '';
        $uri .= $this->user ? $this->user . ($this->pass ? ':' . $this->pass : '') . '@' : '';
        $uri .= $this->host ? $this->host : '';
        $uri .= $this->port ? ':' . $this->port : '';
        $uri .= $this->path ? $this->path : '';
        if ($includeanchor) {
            $uri .= $this->get_encoded_anchor();
        }

        return $uri;
    }

    /**
     * Compares this url with another.
     *
     * See documentation of constants for an explanation of the comparison flags.
     *
     * @param self $url The moodle_url object to compare
     * @param int $matchtype The type of comparison (URL_MATCH_BASE, URL_MATCH_PARAMS, URL_MATCH_EXACT)
     * @return bool
     */
    public function compare(self $url, $matchtype = URL_MATCH_EXACT) {

        $baseself = $this->out_omit_querystring();
        $baseother = $url->out_omit_querystring();

        // Append index.php if there is no specific file.
        if (substr($baseself, -1) == '/') {
            $baseself .= 'index.php';
        }
        if (substr($baseother, -1) == '/') {
            $baseother .= 'index.php';
        }

        // Compare the two base URLs.
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

        if ($url->anchor !== $this->anchor) {
            return false;
        }

        return true;
    }

    /**
     * Sets the anchor for the URI (the bit after the hash)
     *
     * @param string $anchor null means remove previous
     */
    public function set_anchor($anchor) {
        if (is_null($anchor)) {
            // Remove.
            $this->anchor = null;
        } else {
            $this->anchor = $anchor;
        }
    }

    /**
     * Sets the scheme for the URI (the bit before ://)
     *
     * @param string $scheme
     */
    public function set_scheme($scheme) {
        // See http://www.ietf.org/rfc/rfc3986.txt part 3.1.
        if (preg_match('/^[a-zA-Z][a-zA-Z0-9+.-]*$/', $scheme)) {
            $this->scheme = $scheme;
        } else {
            throw new coding_exception('Bad URL scheme.');
        }
    }

    /**
     * Sets the url slashargument value.
     *
     * @param string $path usually file path
     * @param string $parameter name of page parameter if slasharguments not supported
     * @param bool $supported usually null, then it depends on $CFG->slasharguments, use true or false for other servers
     */
    public function set_slashargument($path, $parameter = 'file', $supported = null) {
        global $CFG;
        if (is_null($supported)) {
            $supported = !empty($CFG->slasharguments);
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

    // Static factory methods.

    /**
     * Create a new url instance from a UriInterface.
     *
     * @param UriInterface $uri
     * @return self
     */
    public static function from_uri(UriInterface $uri): self {
        $url = new self(
            url: $uri->getScheme() . '://' . $uri->getAuthority() . $uri->getPath(),
            anchor: $uri->getFragment() ?: null,
        );

        $params = $uri->getQuery();
        foreach (explode('&', $params) as $param) {
            $url->param(...explode('=', $param, 2));
        }

        return $url;
    }

    /**
     * Create a new moodle_url instance from routed path.
     *
     * @param string $path The routed path
     * @param null|array $params The path parameters
     * @param null|string $anchor The anchor
     * @return self
     */
    public static function routed_path(
        string $path,
        ?array $params = null,
        ?string $anchor = null,
    ): self {
        global $CFG;

        if ($CFG->routerconfigured != true) {
            $path = '/r.php/' . ltrim($path, '/');
        }
        $url = new self($path, $params, $anchor);
        return $url;
    }

    /**
     * General moodle file url.
     *
     * @param string $urlbase the script serving the file
     * @param string $path
     * @param bool $forcedownload
     * @return self
     */
    public static function make_file_url($urlbase, $path, $forcedownload = false) {
        $params = [];
        if ($forcedownload) {
            $params['forcedownload'] = 1;
        }
        $url = new self($urlbase, $params);
        $url->set_slashargument($path);
        return $url;
    }

    /**
     * Factory method for creation of url pointing to plugin file.
     *
     * Please note this method can be used only from the plugins to
     * create urls of own files, it must not be used outside of plugins!
     *
     * @param int $contextid
     * @param string $component
     * @param string $area
     * @param ?int $itemid
     * @param string $pathname
     * @param string $filename
     * @param bool $forcedownload
     * @param mixed $includetoken Whether to use a user token when displaying this group image.
     *                True indicates to generate a token for current user, and integer value indicates to generate a token for the
     *                user whose id is the value indicated.
     *                If the group picture is included in an e-mail or some other location where the audience is a specific
     *                user who will not be logged in when viewing, then we use a token to authenticate the user.
     * @return url
     */
    public static function make_pluginfile_url(
        $contextid,
        $component,
        $area,
        $itemid,
        $pathname,
        $filename,
        $forcedownload = false,
        $includetoken = false
    ) {
        global $CFG, $USER;

        $path = [];

        if ($includetoken) {
            $urlbase = "$CFG->wwwroot/tokenpluginfile.php";
            $userid = $includetoken === true ? $USER->id : $includetoken;
            $token = get_user_key('core_files', $userid);
            if ($CFG->slasharguments) {
                $path[] = $token;
            }
        } else {
            $urlbase = "$CFG->wwwroot/pluginfile.php";
        }
        $path[] = $contextid;
        $path[] = $component;
        $path[] = $area;

        if ($itemid !== null) {
            $path[] = $itemid;
        }

        $path = "/" . implode('/', $path) . "{$pathname}{$filename}";

        $url = self::make_file_url($urlbase, $path, $forcedownload, $includetoken);
        if ($includetoken && empty($CFG->slasharguments)) {
            $url->param('token', $token);
        }
        return $url;
    }

    /**
     * Factory method for creation of url pointing to plugin file.
     * This method is the same that make_pluginfile_url but pointing to the webservice pluginfile.php script.
     * It should be used only in external functions.
     *
     * @since  2.8
     * @param int $contextid
     * @param string $component
     * @param string $area
     * @param int $itemid
     * @param string $pathname
     * @param string $filename
     * @param bool $forcedownload
     * @return url
     */
    public static function make_webservice_pluginfile_url(
        $contextid,
        $component,
        $area,
        $itemid,
        $pathname,
        $filename,
        $forcedownload = false
    ) {
        global $CFG;
        $urlbase = "$CFG->wwwroot/webservice/pluginfile.php";
        if ($itemid === null) {
            return self::make_file_url($urlbase, "/$contextid/$component/$area" . $pathname . $filename, $forcedownload);
        } else {
            return self::make_file_url($urlbase, "/$contextid/$component/$area/$itemid" . $pathname . $filename, $forcedownload);
        }
    }

    /**
     * Factory method for creation of url pointing to draft file of current user.
     *
     * @param int $draftid draft item id
     * @param string $pathname
     * @param string $filename
     * @param bool $forcedownload
     * @return url
     */
    public static function make_draftfile_url($draftid, $pathname, $filename, $forcedownload = false) {
        global $CFG, $USER;
        $urlbase = "$CFG->wwwroot/draftfile.php";
        $context = context_user::instance($USER->id);

        return self::make_file_url($urlbase, "/$context->id/user/draft/$draftid" . $pathname . $filename, $forcedownload);
    }

    /**
     * Factory method for creating of links to legacy course files.
     *
     * @param int $courseid
     * @param string $filepath
     * @param bool $forcedownload
     * @return url
     */
    public static function make_legacyfile_url($courseid, $filepath, $forcedownload = false) {
        global $CFG;

        $urlbase = "$CFG->wwwroot/file.php";
        return self::make_file_url($urlbase, '/' . $courseid . '/' . $filepath, $forcedownload);
    }

    /**
     * Checks if URL is relative to $CFG->wwwroot.
     *
     * @return bool True if URL is relative to $CFG->wwwroot; otherwise, false.
     */
    public function is_local_url(): bool {
        global $CFG;

        $url = $this->out();
        // Does URL start with wwwroot? Otherwise, URL isn't relative to wwwroot.
        return ( ($url === $CFG->wwwroot) || (strpos($url, $CFG->wwwroot . '/') === 0) );
    }

    /**
     * Returns URL as relative path from $CFG->wwwroot
     *
     * Can be used for passing around urls with the wwwroot stripped
     *
     * @param boolean $escaped Use &amp; as params separator instead of plain &
     * @param ?array $overrideparams params to add to the output url, these override existing ones with the same name.
     * @return string Resulting URL
     * @throws coding_exception if called on a non-local url
     */
    public function out_as_local_url($escaped = true, ?array $overrideparams = null) {
        global $CFG;

        // URL should be relative to wwwroot. If not then throw exception.
        if ($this->is_local_url()) {
            $url = $this->out($escaped, $overrideparams);
            $localurl = substr($url, strlen($CFG->wwwroot));
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
     * @return array|string|null Value of parameter or null if not set.
     */
    public function get_param($name) {
        if (array_key_exists($name, $this->params)) {
            return $this->params[$name];
        } else {
            return null;
        }
    }

    /**
     * Returns the 'scheme' portion of a URL. For example, if the URL is
     * http://www.example.org:447/my/file/is/here.txt?really=1 then this will
     * return 'http' (without the colon).
     *
     * @return string Scheme of the URL.
     */
    public function get_scheme() {
        return $this->scheme;
    }

    /**
     * Returns the 'host' portion of a URL. For example, if the URL is
     * http://www.example.org:447/my/file/is/here.txt?really=1 then this will
     * return 'www.example.org'.
     *
     * @return string Host of the URL.
     */
    public function get_host() {
        return $this->host;
    }

    /**
     * Returns the 'port' portion of a URL. For example, if the URL is
     * http://www.example.org:447/my/file/is/here.txt?really=1 then this will
     * return '447'.
     *
     * @return string Port of the URL.
     */
    public function get_port() {
        return $this->port;
    }

    /**
     * Returns the 'slashargument' portion of a URL. For example, if the URL is
     * http://www.example.org.com/pluginfile.php/1/core_admin/logocompact/ then this will
     * return '1/core_admin/logocompact/'.
     *
     * @return string Slash argument as string.
     */
    public function get_slashargument(): string {
        return $this->slashargument;
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(url::class, \moodle_url::class);
