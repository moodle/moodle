<?php

/**
 +-----------------------------------------------------------------------+
 | This file is part of the Roundcube Webmail client                     |
 |                                                                       |
 | Copyright (C) The Roundcube Dev Team                                  |
 | Copyright (C) Kolab Systems AG                                        |
 |                                                                       |
 | Licensed under the GNU General Public License version 3 or            |
 | any later version with exceptions for skins & plugins.                |
 | See the README file for a full license statement.                     |
 |                                                                       |
 | PURPOSE:                                                              |
 |   Utility class providing common functions                            |
 +-----------------------------------------------------------------------+
 | Author: Thomas Bruederli <roundcube@gmail.com>                        |
 | Author: Aleksander Machniak <alec@alec.pl>                            |
 +-----------------------------------------------------------------------+
*/

/**
 * Utility class providing common functions
 *
 * @package    Framework
 * @subpackage Utils
 */
class rcube_utils
{
    // define constants for input reading
    const INPUT_GET    = 1;
    const INPUT_POST   = 2;
    const INPUT_COOKIE = 4;
    const INPUT_GP     = 3; // GET + POST
    const INPUT_GPC    = 7; // GET + POST + COOKIE


    /**
     * A wrapper for PHP's explode() that does not throw a warning
     * when the separator does not exist in the string
     *
     * @param string $separator Separator string
     * @param string $string    The string to explode
     *
     * @return array Exploded string. Still an array if there's no separator in the string
     */
    public static function explode($separator, $string)
    {
        if (strpos($string, $separator) !== false) {
            return explode($separator, $string);
        }

        return [$string, null];
    }

    /**
     * Helper method to set a cookie with the current path and host settings
     *
     * @param string $name      Cookie name
     * @param string $value     Cookie value
     * @param int    $exp       Expiration time
     * @param bool   $http_only HTTP Only
     */
    public static function setcookie($name, $value, $exp = 0, $http_only = true)
    {
        if (headers_sent()) {
            return;
        }

        $attrib             = session_get_cookie_params();
        $attrib['expires']  = $exp;
        $attrib['secure']   = $attrib['secure'] || self::https_check();
        $attrib['httponly'] = $http_only;

        // session_get_cookie_params() return includes 'lifetime' but setcookie() does not use it, instead it uses 'expires'
        unset($attrib['lifetime']);

        setcookie($name, $value, $attrib);
    }

    /**
     * E-mail address validation.
     *
     * @param string $email     Email address
     * @param bool   $dns_check True to check dns
     *
     * @return bool True on success, False if address is invalid
     */
    public static function check_email($email, $dns_check = true)
    {
        // Check for invalid (control) characters
        if (preg_match('/\p{Cc}/u', $email)) {
            return false;
        }

        // Check for length limit specified by RFC 5321 (#1486453)
        if (strlen($email) > 254) {
            return false;
        }

        $pos = strrpos($email, '@');
        if (!$pos) {
            return false;
        }

        $domain_part = substr($email, $pos + 1);
        $local_part  = substr($email, 0, $pos);

        // quoted-string, make sure all backslashes and quotes are
        // escaped
        if (substr($local_part, 0, 1) == '"') {
            $local_quoted = preg_replace('/\\\\(\\\\|\")/','', substr($local_part, 1, -1));
            if (preg_match('/\\\\|"/', $local_quoted)) {
                return false;
            }
        }
        // dot-atom portion, make sure there's no prohibited characters
        else if (preg_match('/(^\.|\.\.|\.$)/', $local_part)
            || preg_match('/[\\ ",:;<>@]/', $local_part)
        ) {
            return false;
        }

        // Validate domain part
        if (preg_match('/^\[((IPv6:[0-9a-f:.]+)|([0-9.]+))\]$/i', $domain_part, $matches)) {
            return self::check_ip(preg_replace('/^IPv6:/i', '', $matches[1])); // valid IPv4 or IPv6 address
        }
        else {
            // If not an IP address
            $domain_array = explode('.', $domain_part);
            // Not enough parts to be a valid domain
            if (count($domain_array) < 2) {
                return false;
            }

            foreach ($domain_array as $part) {
                if (!preg_match('/^((xn--)?([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|([A-Za-z0-9]))$/', $part)) {
                    return false;
                }
            }

            // last domain part (allow extended TLD)
            $last_part = array_pop($domain_array);
            if (strpos($last_part, 'xn--') !== 0
                && (preg_match('/[^a-zA-Z0-9]/', $last_part) || preg_match('/^[0-9]+$/', $last_part))
            ) {
                return false;
            }

            $rcube = rcube::get_instance();

            if (!$dns_check || !function_exists('checkdnsrr') || !$rcube->config->get('email_dns_check')) {
                return true;
            }

            // Check DNS record(s)
            // Note: We can't use ANY (#6581)
            foreach (['A', 'MX', 'CNAME', 'AAAA'] as $type) {
                if (checkdnsrr($domain_part, $type)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Validates IPv4 or IPv6 address
     *
     * @param string $ip IP address in v4 or v6 format
     *
     * @return bool True if the address is valid
     */
    public static function check_ip($ip)
    {
        return filter_var($ip, FILTER_VALIDATE_IP) !== false;
    }

    /**
     * Replacing specials characters to a specific encoding type
     *
     * @param string $str      Input string
     * @param string $enctype  Encoding type: text|html|xml|js|url
     * @param string $mode     Replace mode for tags: show|remove|strict
     * @param bool   $newlines Convert newlines
     *
     * @return string The quoted string
     */
    public static function rep_specialchars_output($str, $enctype = '', $mode = '', $newlines = true)
    {
        static $html_encode_arr = false;
        static $js_rep_table    = false;
        static $xml_rep_table   = false;

        if (!is_string($str)) {
            $str = strval($str);
        }

        // encode for HTML output
        if ($enctype == 'html') {
            if (!$html_encode_arr) {
                $html_encode_arr = get_html_translation_table(HTML_SPECIALCHARS);
                unset($html_encode_arr['?']);
            }

            $encode_arr = $html_encode_arr;

            if ($mode == 'remove') {
                $str = strip_tags($str);
            }
            else if ($mode != 'strict') {
                // don't replace quotes and html tags
                $ltpos = strpos($str, '<');
                if ($ltpos !== false && strpos($str, '>', $ltpos) !== false) {
                    unset($encode_arr['"']);
                    unset($encode_arr['<']);
                    unset($encode_arr['>']);
                    unset($encode_arr['&']);
                }
            }

            $out = strtr($str, $encode_arr);

            return $newlines ? nl2br($out) : $out;
        }

        // if the replace tables for XML and JS are not yet defined
        if ($js_rep_table === false) {
            $js_rep_table = $xml_rep_table = [];
            $xml_rep_table['&'] = '&amp;';

            // can be increased to support more charsets
            for ($c=160; $c<256; $c++) {
                $xml_rep_table[chr($c)] = "&#$c;";
            }

            $xml_rep_table['"'] = '&quot;';
            $js_rep_table['"']  = '\\"';
            $js_rep_table["'"]  = "\\'";
            $js_rep_table["\\"] = "\\\\";
            // Unicode line and paragraph separators (#1486310)
            $js_rep_table[chr(hexdec('E2')).chr(hexdec('80')).chr(hexdec('A8'))] = '&#8232;';
            $js_rep_table[chr(hexdec('E2')).chr(hexdec('80')).chr(hexdec('A9'))] = '&#8233;';
        }

        // encode for javascript use
        if ($enctype == 'js') {
            return preg_replace(["/\r?\n/", "/\r/", '/<\\//'], ['\n', '\n', '<\\/'], strtr($str, $js_rep_table));
        }

        // encode for plaintext
        if ($enctype == 'text') {
            return str_replace("\r\n", "\n", $mode == 'remove' ? strip_tags($str) : $str);
        }

        if ($enctype == 'url') {
            return rawurlencode($str);
        }

        // encode for XML
        if ($enctype == 'xml') {
            return strtr($str, $xml_rep_table);
        }

        // no encoding given -> return original string
        return $str;
    }

    /**
     * Read input value and make sure it is a string.
     *
     * @param string $fname      Field name to read
     * @param int    $source     Source to get value from (see self::INPUT_*)
     * @param bool   $allow_html Allow HTML tags in field value
     * @param string $charset    Charset to convert into
     *
     * @return string Request parameter value
     * @see self::get_input_value()
     */
    public static function get_input_string($fname, $source, $allow_html = false, $charset = null)
    {
        $value = self::get_input_value($fname, $source, $allow_html, $charset);

        return is_string($value) ? $value : '';
    }

    /**
     * Read request parameter value and convert it for internal use
     * Performs stripslashes() and charset conversion if necessary
     *
     * @param string $fname      Field name to read
     * @param int    $source     Source to get value from (see self::INPUT_*)
     * @param bool   $allow_html Allow HTML tags in field value
     * @param string $charset    Charset to convert into
     *
     * @return string|array|null Request parameter value or NULL if not set
     */
    public static function get_input_value($fname, $source, $allow_html = false, $charset = null)
    {
        $value = null;

        if (($source & self::INPUT_GET) && isset($_GET[$fname])) {
            $value = $_GET[$fname];
        }

        if (($source & self::INPUT_POST) && isset($_POST[$fname])) {
            $value = $_POST[$fname];
        }

        if (($source & self::INPUT_COOKIE) && isset($_COOKIE[$fname])) {
            $value = $_COOKIE[$fname];
        }

        return self::parse_input_value($value, $allow_html, $charset);
    }

    /**
     * Parse/validate input value. See self::get_input_value()
     * Performs stripslashes() and charset conversion if necessary
     *
     * @param string $value      Input value
     * @param bool   $allow_html Allow HTML tags in field value
     * @param string $charset    Charset to convert into
     *
     * @return string Parsed value
     */
    public static function parse_input_value($value, $allow_html = false, $charset = null)
    {
        if (empty($value)) {
            return $value;
        }

        if (is_array($value)) {
            foreach ($value as $idx => $val) {
                $value[$idx] = self::parse_input_value($val, $allow_html, $charset);
            }

            return $value;
        }

        // remove HTML tags if not allowed
        if (!$allow_html) {
            $value = strip_tags($value);
        }

        $rcube          = rcube::get_instance();
        $output_charset = is_object($rcube->output) ? $rcube->output->get_charset() : null;

        // remove invalid characters (#1488124)
        if ($output_charset == 'UTF-8') {
            $value = rcube_charset::clean($value);
        }

        // convert to internal charset
        if ($charset && $output_charset) {
            $value = rcube_charset::convert($value, $output_charset, $charset);
        }

        return $value;
    }

    /**
     * Convert array of request parameters (prefixed with _)
     * to a regular array with non-prefixed keys.
     *
     * @param int    $mode       Source to get value from (GPC)
     * @param string $ignore     PCRE expression to skip parameters by name
     * @param bool   $allow_html Allow HTML tags in field value
     *
     * @return array Hash array with all request parameters
     */
    public static function request2param($mode = null, $ignore = 'task|action', $allow_html = false)
    {
        $out = [];
        $src = $mode == self::INPUT_GET ? $_GET : ($mode == self::INPUT_POST ? $_POST : $_REQUEST);

        foreach (array_keys($src) as $key) {
            $fname = $key[0] == '_' ? substr($key, 1) : $key;
            if ($ignore && !preg_match('/^(' . $ignore . ')$/', $fname)) {
                $out[$fname] = self::get_input_value($key, $mode, $allow_html);
            }
        }

        return $out;
    }

    /**
     * Convert the given string into a valid HTML identifier
     * Same functionality as done in app.js with rcube_webmail.html_identifier()
     *
     * @param string $str    String input
     * @param bool   $encode Use base64 encoding
     *
     * @return string Valid HTML identifier
     */
    public static function html_identifier($str, $encode = false)
    {
        if ($encode) {
            return rtrim(strtr(base64_encode($str), '+/', '-_'), '=');
        }

        return asciiwords($str, true, '_');
    }

    /**
     * Replace all css definitions with #container [def]
     * and remove css-inlined scripting, make position style safe
     *
     * @param string $source       CSS source code
     * @param string $container_id Container ID to use as prefix
     * @param bool   $allow_remote Allow remote content
     * @param string $prefix       Prefix to be added to id/class identifier
     *
     * @return string Modified CSS source
     */
    public static function mod_css_styles($source, $container_id, $allow_remote = false, $prefix = '')
    {
        $source   = self::xss_entity_decode($source);

        // No @import allowed
        // TODO: We should just remove it, not invalidate the whole content
        if (stripos($source, '@import') !== false) {
            return '/* evil! */';
        }

        // Incomplete style expression
        if (strpos($source, '{') === false) {
            return '/* invalid! */';
        }

        // To prevent from a double-escaping tricks we consider a script with
        // any escape sequences (after de-escaping them above) an evil script.
        // This probably catches many valid scripts, but we\'re on the safe side.
        if (preg_match('/\\\[0-9a-fA-F]{2}/', $source)) {
            return '/* evil! */';
        }

        // remove html comments
        $source = preg_replace('/(^\s*<\!--)|(-->\s*$)/m', '', $source);

        $url_callback = static function ($url) use ($allow_remote) {
            if (strpos($url, 'data:image') === 0) {
                return $url;
            }
            if ($allow_remote && preg_match('|^https?://[a-z0-9/._+-]+$|i', $url)) {
                return $url;
            }
        };

        $last_pos = 0;
        $replacements = new rcube_string_replacer();

        // cut out all contents between { and }
        while (($pos = strpos($source, '{', $last_pos)) && ($pos2 = strpos($source, '}', $pos))) {
            $nested = strpos($source, '{', $pos+1);
            if ($nested && $nested < $pos2) { // when dealing with nested blocks (e.g. @media), take the inner one
                $pos = $nested;
            }
            $length = $pos2 - $pos - 1;
            $styles = substr($source, $pos+1, $length);
            $styles = self::sanitize_css_block($styles, $url_callback);

            $key      = $replacements->add(strlen($styles) ? " {$styles} " : '');
            $repl     = $replacements->get_replacement($key);
            $source   = substr_replace($source, $repl, $pos+1, $length);
            $last_pos = $pos2 - ($length - strlen($repl));
        }

        // add #container to each tag selector and prefix to id/class identifiers
        if ($container_id || $prefix) {
            // Exclude rcube_string_replacer pattern matches, this is needed
            // for cases like @media { body { position: fixed; } } (#5811)
            $excl     = '(?!' . substr($replacements->pattern, 1, -1) . ')';
            $regexp   = '/(^\s*|,\s*|\}\s*|\{\s*)(' . $excl . ':?[a-z0-9\._#\*\[][a-z0-9\._:\(\)#=~ \[\]"\|\>\+\$\^-]*)/im';
            $callback = function($matches) use ($container_id, $prefix) {
                $replace = $matches[2];

                if (stripos($replace, ':root') === 0) {
                    $replace = substr($replace, 5);
                }

                if ($prefix) {
                    $replace = str_replace(['.', '#'], [".$prefix", "#$prefix"], $replace);
                }

                if ($container_id) {
                    $replace = "#$container_id " . $replace;
                }

                // Remove redundant spaces (for simpler testing)
                $replace = preg_replace('/\s+/', ' ', $replace);

                return str_replace($matches[2], $replace, $matches[0]);
            };

            $source = preg_replace_callback($regexp, $callback, $source);
        }

        // replace body definition because we also stripped off the <body> tag
        if ($container_id) {
            $regexp = '/#' . preg_quote($container_id, '/') . '\s+body/i';
            $source = preg_replace($regexp, "#$container_id", $source);
        }

        // put block contents back in
        $source = $replacements->resolve($source);

        return $source;
    }

    /**
     * Parse and sanitize single CSS block
     *
     * @param string    $styles       CSS styles block
     * @param ?callable $url_callback URL validator callback
     *
     * @return string
     */
    public static function sanitize_css_block($styles, $url_callback = null)
    {
        $output = [];

        // check every css rule in the style block...
        foreach (self::parse_css_block($styles) as $rule) {
            $property = $rule[0];
            $value = $rule[1];

            if ($property == 'page') {
                // Remove 'page' attributes (#7604)
                continue;
            } elseif ($property == 'position' && strcasecmp($value, 'fixed') === 0) {
                // Convert position:fixed to position:absolute (#5264)
                $value = 'absolute';
            } elseif (preg_match('/expression|image-set/i', $value)) {
                continue;
            } else {
                $value = '';
                foreach (self::explode_css_property_block($rule[1]) as $val) {
                    if ($url_callback && preg_match('/^url\s*\(/i', $val)) {
                        if (preg_match('/^url\s*\(\s*[\'"]?([^\'"\)]*)[\'"]?\s*\)/iu', $val, $match)) {
                            if ($url = $url_callback($match[1])) {
                                $value .= ' url(' . $url . ')';
                            }
                        }
                    } else {
                        // whitelist ?
                        $value .= ' ' . $val;

                        // #1488535: Fix size units, so width:800 would be changed to width:800px
                        if ($val
                            && preg_match('/^(left|right|top|bottom|width|height)/i', $property)
                            && preg_match('/^[0-9]+$/', $val)
                        ) {
                            $value .= 'px';
                        }
                    }
                }
            }

            if (strlen($value)) {
                $output[] = $property . ': ' . trim($value);
            }
        }

        return count($output) > 0 ? implode('; ', $output) . ';' : '';
    }

    /**
     * Explode css style. Property names will be lower-cased and trimmed.
     * Values will be trimmed. Invalid entries will be skipped.
     *
     * @param string $style CSS style
     *
     * @return array List of CSS rule pairs, e.g. [['color', 'red'], ['top', '0']]
     */
    public static function parse_css_block($style)
    {
        $pos = 0;

        // first remove comments
        while (($pos = strpos($style, '/*', $pos)) !== false) {
            $end = strpos($style, '*/', $pos+2);

            if ($end === false) {
                $style = substr($style, 0, $pos);
            }
            else {
                $style = substr_replace($style, '', $pos, $end - $pos + 2);
            }
        }

        // Replace new lines with spaces
        $style = preg_replace('/[\r\n]+/', ' ', $style);

        $style  = trim($style);
        $length = strlen($style);
        $result = [];
        $pos    = 0;

        while ($pos < $length && ($colon_pos = strpos($style, ':', $pos))) {
            // Property name
            $name = strtolower(trim(substr($style, $pos, $colon_pos - $pos)));

            // get the property value
            $q = $s = false;
            for ($i = $colon_pos + 1; $i < $length; $i++) {
                if (($style[$i] == "\"" || $style[$i] == "'") && ($i == 0 || $style[$i-1] != "\\")) {
                    if ($q == $style[$i]) {
                        $q = false;
                    }
                    else if ($q === false) {
                        $q = $style[$i];
                    }
                }
                else if ($style[$i] == "(" && !$q && ($i == 0 || $style[$i-1] != "\\")) {
                    $q = "(";
                }
                else if ($style[$i] == ")" && $q == "(" && $style[$i-1] != "\\") {
                    $q = false;
                }

                if ($q === false && (($s = $style[$i] == ';') || $i == $length - 1)) {
                    break;
                }
            }

            $value_length = $i - $colon_pos - ($s ? 1 : 0);
            $value        = trim(substr($style, $colon_pos + 1, $value_length));

            if (strlen($name) && !preg_match('/[^a-z-]/', $name) && strlen($value) && $value !== ';') {
                $result[] = [$name, $value];
            }

            $pos = $i + 1;
        }

        return $result;
    }

    /**
     * Explode css style value
     *
     * @param string $style CSS style
     *
     * @return array List of CSS values
     */
    public static function explode_css_property_block($style)
    {
        $style = preg_replace('/\s+/', ' ', $style);
        $result = [];
        $strlen = strlen($style);
        $q = false;

        // explode value
        for ($p = $i = 0; $i < $strlen; $i++) {
            if (($style[$i] == '"' || $style[$i] == "'") && ($i == 0 || $style[$i - 1] != '\\')) {
                if ($q == $style[$i]) {
                    $q = false;
                } elseif (!$q) {
                    $q = $style[$i];
                }
            }

            if (!$q && $style[$i] == ' ' && ($i == 0 || !preg_match('/[,\(]/', $style[$i - 1]))) {
                $result[] = substr($style, $p, $i - $p);
                $p = $i + 1;
            }
        }

        $result[] = (string) substr($style, $p);

        return $result;
    }

    /**
     * Generate CSS classes from mimetype and filename extension
     *
     * @param string $mimetype Mimetype
     * @param string $filename Filename
     *
     * @return string CSS classes separated by space
     */
    public static function file2class($mimetype, $filename)
    {
        $mimetype = strtolower($mimetype);
        $filename = strtolower($filename);

        list($primary, $secondary) = rcube_utils::explode('/', $mimetype);

        $classes = [$primary ?: 'unknown'];

        if (!empty($secondary)) {
            $classes[] = $secondary;
        }

        if (preg_match('/\.([a-z0-9]+)$/', $filename, $m)) {
            if (!in_array($m[1], $classes)) {
                $classes[] = $m[1];
            }
        }

        return implode(' ', $classes);
    }

    /**
     * Decode escaped entities used by known XSS exploits.
     * See http://downloads.securityfocus.com/vulnerabilities/exploits/26800.eml for examples
     *
     * @param string $content CSS content to decode
     *
     * @return string Decoded string
     */
    public static function xss_entity_decode($content)
    {
        $callback = function($matches) { return chr(hexdec($matches[1])); };

        $out = html_entity_decode(html_entity_decode($content));
        $out = trim(preg_replace('/(^<!--|-->$)/', '', trim($out)));
        $out = preg_replace_callback('/\\\([0-9a-f]{2,6})\s*/i', $callback, $out);
        $out = preg_replace('/\\\([^0-9a-f])/i', '\\1', $out);
        $out = preg_replace('#/\*.*\*/#Ums', '', $out);
        $out = strip_tags($out);

        return $out;
    }

    /**
     * Check if we can process not exceeding memory_limit
     *
     * @param int $need Required amount of memory
     *
     * @return bool True if memory won't be exceeded, False otherwise
     */
    public static function mem_check($need)
    {
        $mem_limit = parse_bytes(ini_get('memory_limit'));
        $memory    = function_exists('memory_get_usage') ? memory_get_usage() : 16*1024*1024; // safe value: 16MB

        return $mem_limit > 0 && $memory + $need > $mem_limit ? false : true;
    }

    /**
     * Check if working in SSL mode
     *
     * @param int  $port      HTTPS port number
     * @param bool $use_https Enables 'use_https' option checking
     *
     * @return bool True in SSL mode, False otherwise
     */
    public static function https_check($port = null, $use_https = true)
    {
        if (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) != 'off') {
            return true;
        }

        if (!empty($_SERVER['HTTP_X_FORWARDED_PROTO'])
            && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) == 'https'
            && self::check_proxy_whitelist_ip()
        ) {
            return true;
        }

        if ($port && isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == $port) {
            return true;
        }

        if ($use_https && rcube::get_instance()->config->get('use_https')) {
            return true;
        }

        return false;
    }

    /**
     * Check if the reported REMOTE_ADDR is in the 'proxy_whitelist' config option
     */
    public static function check_proxy_whitelist_ip() {
        return in_array($_SERVER['REMOTE_ADDR'], (array) rcube::get_instance()->config->get('proxy_whitelist', []));
    }

    /**
     * Replaces hostname variables.
     *
     * @param string $name Hostname
     * @param string $host Optional IMAP hostname
     *
     * @return string Hostname
     */
    public static function parse_host($name, $host = '')
    {
        if (!is_string($name)) {
            return $name;
        }

        // %n - host
        $n = self::server_name();
        // %t - host name without first part, e.g. %n=mail.domain.tld, %t=domain.tld
        // If %n=domain.tld then %t=domain.tld as well (remains valid)
        $t = preg_replace('/^[^.]+\.(?![^.]+$)/', '', $n);
        // %d - domain name without first part (up to domain.tld)
        $d = preg_replace('/^[^.]+\.(?![^.]+$)/', '', self::server_name('HTTP_HOST'));
        // %h - IMAP host
        $h = !empty($_SESSION['storage_host']) ? $_SESSION['storage_host'] : $host;
        // %z - IMAP domain without first part, e.g. %h=imap.domain.tld, %z=domain.tld
        // If %h=domain.tld then %z=domain.tld as well (remains valid)
        $z = preg_replace('/^[^.]+\.(?![^.]+$)/', '', $h);
        // %s - domain name after the '@' from e-mail address provided at login screen.
        //      Returns FALSE if an invalid email is provided
        $s = '';
        if (strpos($name, '%s') !== false) {
            $user_email = self::idn_to_ascii(self::get_input_value('_user', self::INPUT_POST));
            $matches    = preg_match('/(.*)@([a-z0-9\.\-\[\]\:]+)/i', $user_email, $s);
            if ($matches < 1 || filter_var($s[1]."@".$s[2], FILTER_VALIDATE_EMAIL) === false) {
                return false;
            }
            $s = $s[2];
        }

        return str_replace(['%n', '%t', '%d', '%h', '%z', '%s'], [$n, $t, $d, $h, $z, $s], $name);
    }

    /**
     * Parse host specification URI.
     *
     * @param string $host       Host URI
     * @param int    $plain_port Plain port number
     * @param int    $ssl_port   SSL port number
     *
     * @return array An array with three elements (hostname, scheme, port)
     */
    public static function parse_host_uri($host, $plain_port = null, $ssl_port = null)
    {
        if (preg_match('#^(unix|ldapi)://#i', $host, $matches)) {
            return [$host, $matches[1], -1];
        }

        $url    = parse_url($host);
        $port   = $plain_port;
        $scheme = null;

        if (!empty($url['host'])) {
            $host   = $url['host'];
            $scheme = $url['scheme'] ?? null;

            if (!empty($url['port'])) {
                $port = $url['port'];
            }
            else if (
                $scheme
                && $ssl_port
                && ($scheme === 'ssl' || ($scheme != 'tls' && $scheme[strlen($scheme) - 1] === 's'))
            ) {
                // assign SSL port to ssl://, imaps://, ldaps://, but not tls://
                $port = $ssl_port;
            }
        }

        return [$host, $scheme, $port];
    }

    /**
     * Returns the server name after checking it against trusted hostname patterns.
     *
     * Returns 'localhost' and logs a warning when the hostname is not trusted.
     *
     * @param string $type       The $_SERVER key, e.g. 'HTTP_HOST', Default: 'SERVER_NAME'.
     * @param bool   $strip_port Strip port from the host name
     *
     * @return string Server name
     */
    public static function server_name($type = null, $strip_port = true)
    {
        if (!$type) {
            $type = 'SERVER_NAME';
        }

        $name     = $_SERVER[$type] ?? '';
        $rcube    = rcube::get_instance();
        $patterns = (array) $rcube->config->get('trusted_host_patterns');

        if (!empty($name)) {
            if ($strip_port) {
                $name = preg_replace('/:\d+$/', '', $name);
            }

            if (empty($patterns)) {
                return $name;
            }

            foreach ($patterns as $pattern) {
                // the pattern might be a regular expression or just a host/domain name
                if (preg_match('/[^a-zA-Z0-9.:-]/', $pattern)) {
                    if (preg_match("/$pattern/", $name)) {
                        return $name;
                    }
                }
                else if (strtolower($name) === strtolower($pattern)) {
                    return $name;
                }
            }

            $rcube->raise_error([
                    'file' => __FILE__, 'line' => __LINE__,
                    'message' => "Specified host is not trusted. Using 'localhost'."
                ]
                , true, false
            );
        }

        return 'localhost';
    }

    /**
     * Returns remote IP address and forwarded addresses if found
     *
     * @return string Remote IP address(es)
     */
    public static function remote_ip()
    {
        $address = $_SERVER['REMOTE_ADDR'] ?? '';

        // append the NGINX X-Real-IP header, if set
        if (!empty($_SERVER['HTTP_X_REAL_IP']) && $_SERVER['HTTP_X_REAL_IP'] != $address) {
            $remote_ip[] = 'X-Real-IP: ' . $_SERVER['HTTP_X_REAL_IP'];
        }

        // append the X-Forwarded-For header, if set
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $remote_ip[] = 'X-Forwarded-For: ' . $_SERVER['HTTP_X_FORWARDED_FOR'];
        }

        if (!empty($remote_ip)) {
            $address .= ' (' . implode(',', $remote_ip) . ')';
        }

        return $address;
    }

    /**
     * Returns the real remote IP address
     *
     * @return string Remote IP address
     */
    public static function remote_addr()
    {
        // Check if any of the headers are set first to improve performance
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']) || !empty($_SERVER['HTTP_X_REAL_IP'])) {
            $proxy_whitelist = (array) rcube::get_instance()->config->get('proxy_whitelist', []);
            if (in_array($_SERVER['REMOTE_ADDR'], $proxy_whitelist)) {
                if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                    foreach (array_reverse(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])) as $forwarded_ip) {
                        $forwarded_ip = trim($forwarded_ip);
                        if (!in_array($forwarded_ip, $proxy_whitelist)) {
                            return $forwarded_ip;
                        }
                    }
                }

                if (!empty($_SERVER['HTTP_X_REAL_IP'])) {
                    return $_SERVER['HTTP_X_REAL_IP'];
                }
            }
        }

        if (!empty($_SERVER['REMOTE_ADDR'])) {
            return $_SERVER['REMOTE_ADDR'];
        }

        return '';
    }

    /**
     * Read a specific HTTP request header.
     *
     * @param string $name Header name
     *
     * @return string|null Header value or null if not available
     */
    public static function request_header($name)
    {
        if (function_exists('apache_request_headers')) {
            $headers = apache_request_headers();
            $key     = strtoupper($name);
        }
        else {
            $headers = $_SERVER;
            $key     = 'HTTP_' . strtoupper(strtr($name, '-', '_'));
        }

        if (!empty($headers)) {
            $headers = array_change_key_case($headers, CASE_UPPER);

            return $headers[$key] ?? null;
        }
    }

    /**
     * Explode quoted string
     *
     * @param string $delimiter Delimiter expression string for preg_match()
     * @param string $string    Input string
     *
     * @return array String items
     */
    public static function explode_quoted_string($delimiter, $string)
    {
        $result = [];
        $strlen = strlen($string);

        for ($q=$p=$i=0; $i < $strlen; $i++) {
            if ($string[$i] == "\"" && (!isset($string[$i-1]) || $string[$i-1] != "\\")) {
                $q = $q ? false : true;
            }
            else if (!$q && preg_match("/$delimiter/", $string[$i])) {
                $result[] = substr($string, $p, $i - $p);
                $p = $i + 1;
            }
        }

        $result[] = (string) substr($string, $p);

        return $result;
    }

    /**
     * Improved equivalent to strtotime()
     *
     * @param string       $date     Date string
     * @param DateTimeZone $timezone Timezone to use for DateTime object
     *
     * @return int Unix timestamp
     */
    public static function strtotime($date, $timezone = null)
    {
        $date   = self::clean_datestr($date);
        $tzname = $timezone ? ' ' . $timezone->getName() : '';

        // unix timestamp
        if (is_numeric($date)) {
            return (int) $date;
        }

        // It can be very slow when provided string is not a date and very long
        if (strlen($date) > 128) {
            $date = substr($date, 0, 128);
        }

        // if date parsing fails, we have a date in non-rfc format.
        // remove token from the end and try again
        while (($ts = @strtotime($date . $tzname)) === false || $ts < 0) {
            if (($pos = strrpos($date, ' ')) === false) {
                break;
            }

            $date = rtrim(substr($date, 0, $pos));
        }

        return (int) $ts;
    }

    /**
     * Date parsing function that turns the given value into a DateTime object
     *
     * @param string       $date     Date string
     * @param DateTimeZone $timezone Timezone to use for DateTime object
     *
     * @return DateTime|false DateTime object or False on failure
     */
    public static function anytodatetime($date, $timezone = null)
    {
        if ($date instanceof DateTime) {
            return $date;
        }

        $dt   = false;
        $date = self::clean_datestr($date);

        // try to parse string with DateTime first
        if (!empty($date)) {
            try {
                $_date = preg_match('/^[0-9]+$/', $date) ? "@$date" : $date;
                $dt    = $timezone ? new DateTime($_date, $timezone) : new DateTime($_date);
            }
            catch (Exception $e) {
                // ignore
            }
        }

        // try our advanced strtotime() method
        if (!$dt && ($timestamp = self::strtotime($date, $timezone))) {
            try {
                $dt = new DateTime("@".$timestamp);
                if ($timezone) {
                    $dt->setTimezone($timezone);
                }
            }
            catch (Exception $e) {
                // ignore
            }
        }

        return $dt;
    }

    /**
     * Clean up date string for strtotime() input
     *
     * @param string $date Date string
     *
     * @return string Date string
     */
    public static function clean_datestr($date)
    {
        $date = trim((string) $date);

        // check for MS Outlook vCard date format YYYYMMDD
        if (preg_match('/^([12][90]\d\d)([01]\d)([0123]\d)$/', $date, $m)) {
            return sprintf('%04d-%02d-%02d 00:00:00', intval($m[1]), intval($m[2]), intval($m[3]));
        }

        // Clean malformed data
        $date = preg_replace(
            [
                '/\(.*\)/',                                 // remove RFC comments
                '/GMT\s*([+-][0-9]+)/',                     // support non-standard "GMTXXXX" literal
                '/[^a-z0-9\x20\x09:\/\.+-]/i',              // remove any invalid characters
                '/\s*(Mon|Tue|Wed|Thu|Fri|Sat|Sun)\s*/i',   // remove weekday names
            ],
            [
                '',
                '\\1',
                '',
                '',
            ],
            $date
        );

        $date = trim($date);

        // try to fix dd/mm vs. mm/dd discrepancy, we can't do more here
        if (preg_match('/^(\d{1,2})[.\/-](\d{1,2})[.\/-](\d{4})(\s.*)?$/', $date, $m)) {
            $mdy   = $m[2] > 12 && $m[1] <= 12;
            $day   = $mdy ? $m[2] : $m[1];
            $month = $mdy ? $m[1] : $m[2];
            $date  = sprintf('%04d-%02d-%02d%s', $m[3], $month, $day, $m[4] ?? ' 00:00:00');
        }
        // I've found that YYYY.MM.DD is recognized wrong, so here's a fix
        else if (preg_match('/^(\d{4})\.(\d{1,2})\.(\d{1,2})(\s.*)?$/', $date, $m)) {
            $date  = sprintf('%04d-%02d-%02d%s', $m[1], $m[2], $m[3], $m[4] ?? ' 00:00:00');
        }

        return $date;
    }

    /**
     * Turns the given date-only string in defined format into YYYY-MM-DD format.
     *
     * Supported formats: 'Y/m/d', 'Y.m.d', 'd-m-Y', 'd/m/Y', 'd.m.Y', 'j.n.Y'
     *
     * @param string $date   Date string
     * @param string $format Input date format
     *
     * @return string Date string in YYYY-MM-DD format, or the original string
     *                if format is not supported
     */
    public static function format_datestr($date, $format)
    {
        $format_items = preg_split('/[.-\/\\\\]/', $format);
        $date_items   = preg_split('/[.-\/\\\\]/', $date);
        $iso_format   = '%04d-%02d-%02d';

        if (count($format_items) == 3 && count($date_items) == 3) {
            if ($format_items[0] == 'Y') {
                $date = sprintf($iso_format, $date_items[0], $date_items[1], $date_items[2]);
            }
            else if (strpos('dj', $format_items[0]) !== false) {
                $date = sprintf($iso_format, $date_items[2], $date_items[1], $date_items[0]);
            }
            else if (strpos('mn', $format_items[0]) !== false) {
                $date = sprintf($iso_format, $date_items[2], $date_items[0], $date_items[1]);
            }
        }

        return $date;
    }

    /**
     * Wrapper for idn_to_ascii with support for e-mail address.
     *
     * Warning: Domain names may be lowercase'd.
     * Warning: An empty string may be returned on invalid domain.
     *
     * @param string $str Decoded e-mail address
     *
     * @return string Encoded e-mail address
     */
    public static function idn_to_ascii($str)
    {
        return self::idn_convert($str, true);
    }

    /**
     * Wrapper for idn_to_utf8 with support for e-mail address
     *
     * @param string $str Decoded e-mail address
     *
     * @return string Encoded e-mail address
     */
    public static function idn_to_utf8($str)
    {
        return self::idn_convert($str, false);
    }

    /**
     * Convert a string to ascii or utf8 (using IDNA standard)
     *
     * @param string $input  Decoded e-mail address
     * @param bool   $is_utf Convert by idn_to_ascii if true and idn_to_utf8 if false
     *
     * @return string Encoded e-mail address
     */
    public static function idn_convert($input, $is_utf = false)
    {
        if ($at = strpos($input, '@')) {
            $user   = substr($input, 0, $at);
            $domain = substr($input, $at + 1);
        }
        else {
            $user   = '';
            $domain = $input;
        }

        // Note that in PHP 7.2/7.3 calling idn_to_* functions with default arguments
        // throws a warning, so we have to set the variant explicitly (#6075)
        $variant = INTL_IDNA_VARIANT_UTS46;
        $options = 0;

        // Because php-intl extension lowercases domains and return false
        // on invalid input (#6224), we skip conversion when not needed

        if ($is_utf) {
            if (preg_match('/[^\x20-\x7E]/', $domain)) {
                $options = IDNA_NONTRANSITIONAL_TO_ASCII;
                $domain  = idn_to_ascii($domain, $options, $variant);
            }
        }
        else if (preg_match('/(^|\.)xn--/i', $domain)) {
            $options = IDNA_NONTRANSITIONAL_TO_UNICODE;
            $domain  = idn_to_utf8($domain, $options, $variant);
        }

        if ($domain === false) {
            return '';
        }

        return $at ? $user . '@' . $domain : $domain;
    }

    /**
     * Split the given string into word tokens
     *
     * @param string $str     Input to tokenize
     * @param int    $minlen  Minimum length of a single token
     *
     * @return array List of tokens
     */
    public static function tokenize_string($str, $minlen = 2)
    {
        if (!is_string($str)) {
            return [];
        }

        $expr = ['/[\s;,"\'\/+-]+/ui', '/(\d)[-.\s]+(\d)/u'];
        $repl = [' ', '\\1\\2'];

        if ($minlen > 1) {
            $minlen--;
            $expr[] = "/(^|\s+)\w{1,$minlen}(\s+|$)/u";
            $repl[] = ' ';
        }

        $str = preg_replace($expr, $repl, $str);

        return is_string($str) ? array_filter(explode(" ", $str)) : [];
    }

    /**
     * Normalize the given string for fulltext search.
     * Currently only optimized for ISO-8859-1 and ISO-8859-2 characters; to be extended
     *
     * @param string $str      Input string (UTF-8)
     * @param bool   $as_array True to return list of words as array
     * @param int    $minlen   Minimum length of tokens
     *
     * @return string|array Normalized string or a list of normalized tokens
     */
    public static function normalize_string($str, $as_array = false, $minlen = 2)
    {
        // replace 4-byte unicode characters with '?' character,
        // these are not supported in default utf-8 charset on mysql,
        // the chance we'd need them in searching is very low
        $str = preg_replace('/('
            . '\xF0[\x90-\xBF][\x80-\xBF]{2}'
            . '|[\xF1-\xF3][\x80-\xBF]{3}'
            . '|\xF4[\x80-\x8F][\x80-\xBF]{2}'
            . ')/', '?', $str);

        // split by words
        $arr = self::tokenize_string($str, $minlen);

        // detect character set
        if (rcube_charset::convert(rcube_charset::convert($str, 'UTF-8', 'ISO-8859-1'), 'ISO-8859-1', 'UTF-8') == $str)  {
            // ISO-8859-1 (or ASCII)
            preg_match_all('/./u', 'äâàåáãæçéêëèïîìíñöôòøõóüûùúýÿ', $keys);
            preg_match_all('/./',  'aaaaaaaceeeeiiiinoooooouuuuyy', $values);

            $mapping = array_combine($keys[0], $values[0]);
            $mapping = array_merge($mapping, ['ß' => 'ss', 'ae' => 'a', 'oe' => 'o', 'ue' => 'u']);
        }
        else if (rcube_charset::convert(rcube_charset::convert($str, 'UTF-8', 'ISO-8859-2'), 'ISO-8859-2', 'UTF-8') == $str) {
            // ISO-8859-2
            preg_match_all('/./u', 'ąáâäćçčéęëěíîłľĺńňóôöŕřśšşťţůúűüźžżý', $keys);
            preg_match_all('/./',  'aaaaccceeeeiilllnnooorrsssttuuuuzzzy', $values);

            $mapping = array_combine($keys[0], $values[0]);
            $mapping = array_merge($mapping, ['ß' => 'ss', 'ae' => 'a', 'oe' => 'o', 'ue' => 'u']);
        }

        foreach ($arr as $i => $part) {
            $part = mb_strtolower($part);

            if (!empty($mapping)) {
                $part = strtr($part, $mapping);
            }

            $arr[$i] = $part;
        }

        return $as_array ? $arr : implode(' ', $arr);
    }

    /**
     * Compare two strings for matching words (order not relevant)
     *
     * @param string $haystack Haystack
     * @param string $needle   Needle
     *
     * @return bool True if match, False otherwise
     */
    public static function words_match($haystack, $needle)
    {
        $a_needle  = self::tokenize_string($needle, 1);
        $_haystack = implode(' ', self::tokenize_string($haystack, 1));
        $valid     = strlen($_haystack) > 0;
        $hits      = 0;

        foreach ($a_needle as $w) {
            if ($valid) {
                if (stripos($_haystack, $w) !== false) {
                    $hits++;
                }
            }
            else if (stripos($haystack, $w) !== false) {
                $hits++;
            }
        }

        return $hits >= count($a_needle);
    }

    /**
     * Parse commandline arguments into a hash array
     *
     * @param array $aliases Argument alias names
     *
     * @return array Argument values hash
     */
    public static function get_opt($aliases = [])
    {
        $args = [];
        $bool = [];

        // find boolean (no value) options
        foreach ($aliases as $key => $alias) {
            if ($pos = strpos($alias, ':')) {
                $aliases[$key] = substr($alias, 0, $pos);
                $bool[] = $key;
                $bool[] = $aliases[$key];
            }
        }

        for ($i=1; $i < count($_SERVER['argv']); $i++) {
            $arg   = $_SERVER['argv'][$i];
            $value = true;
            $key   = null;

            if (strlen($arg) && $arg[0] == '-') {
                $key = preg_replace('/^-+/', '', $arg);
                $sp  = strpos($arg, '=');

                if ($sp > 0) {
                    $key   = substr($key, 0, $sp - 2);
                    $value = substr($arg, $sp+1);
                }
                else if (in_array($key, $bool)) {
                    $value = true;
                }
                else if (
                    isset($_SERVER['argv'][$i + 1])
                    && strlen($_SERVER['argv'][$i + 1])
                    && $_SERVER['argv'][$i + 1][0] != '-'
                ) {
                    $value = $_SERVER['argv'][++$i];
                }

                $args[$key] = is_string($value) ? preg_replace(['/^["\']/', '/["\']$/'], '', $value) : $value;
            }
            else {
                $args[] = $arg;
            }

            if (!empty($aliases[$key])) {
                $alias = $aliases[$key];
                $args[$alias] = $args[$key];
            }
        }

        return $args;
    }

    /**
     * Safe password prompt for command line
     * from http://blogs.sitepoint.com/2009/05/01/interactive-cli-password-prompt-in-php/
     *
     * @param string $prompt Prompt text
     *
     * @return string Password
     */
    public static function prompt_silent($prompt = "Password:")
    {
        if (preg_match('/^win/i', PHP_OS)) {
            $vbscript  = sys_get_temp_dir() . 'prompt_password.vbs';
            $vbcontent = 'wscript.echo(InputBox("' . addslashes($prompt) . '", "", "password here"))';
            file_put_contents($vbscript, $vbcontent);

            $command  = "cscript //nologo " . escapeshellarg($vbscript);
            $password = rtrim(shell_exec($command));
            unlink($vbscript);

            return $password;
        }

        $command = "/usr/bin/env bash -c 'echo OK'";

        if (rtrim(shell_exec($command)) !== 'OK') {
            echo $prompt;
            $pass = trim(fgets(STDIN));
            echo chr(8)."\r" . $prompt . str_repeat("*", strlen($pass))."\n";

            return $pass;
        }

        $command  = "/usr/bin/env bash -c 'read -s -p \"" . addslashes($prompt) . "\" mypassword && echo \$mypassword'";
        $password = rtrim(shell_exec($command));
        echo "\n";

        return $password;
    }

    /**
     * Find out if the string content means true or false
     *
     * @param string $str Input value
     *
     * @return bool Boolean value
     */
    public static function get_boolean($str)
    {
        $str = strtolower((string) $str);

        return !in_array($str, ['false', '0', 'no', 'off', 'nein', ''], true);
    }

    /**
     * OS-dependent absolute path detection
     *
     * @param string $path File path
     *
     * @return bool True if the path is absolute, False otherwise
     */
    public static function is_absolute_path($path)
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN') {
            return (bool) preg_match('!^[a-z]:[\\\\/]!i', $path);
        }

        return isset($path[0]) && $path[0] == '/';
    }

    /**
     * Resolve relative URL
     *
     * @param string $url Relative URL
     *
     * @return string Absolute URL
     */
    public static function resolve_url($url)
    {
        // prepend protocol://hostname:port
        if (!preg_match('|^https?://|', $url)) {
            $schema       = 'http';
            $default_port = 80;

            if (self::https_check()) {
                $schema       = 'https';
                $default_port = 443;
            }

            $host = $_SERVER['HTTP_HOST'] ?? '';
            $port = $_SERVER['SERVER_PORT'] ?? 0;

            $prefix = $schema . '://' . preg_replace('/:\d+$/', '', $host);
            if ($port && $port != $default_port && $port != 80) {
                $prefix .= ':' . $port;
            }

            $url = $prefix . ($url[0] == '/' ? '' : '/') . $url;
        }

        return $url;
    }

    /**
     * Generate a random string
     *
     * @param int  $length String length
     * @param bool $raw    Return RAW data instead of ascii
     *
     * @return string The generated random string
     */
    public static function random_bytes($length, $raw = false)
    {
        // Use PHP7 true random generator
        if ($raw) {
            return random_bytes($length);
        }

        $hextab  = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $tabsize = strlen($hextab);

        $result = '';
        while ($length-- > 0) {
            $result .= $hextab[random_int(0, $tabsize - 1)];
        }

        return $result;
    }

    /**
     * Convert binary data into readable form (containing a-zA-Z0-9 characters)
     *
     * @param string $input Binary input
     *
     * @return string Readable output (Base62)
     * @deprecated since 1.3.1
     */
    public static function bin2ascii($input)
    {
        $hextab = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $result = '';

        for ($x = 0; $x < strlen($input); $x++) {
            $result .= $hextab[ord($input[$x]) % 62];
        }

        return $result;
    }

    /**
     * Format current date according to specified format.
     * This method supports microseconds (u).
     *
     * @param string $format Date format (default: 'd-M-Y H:i:s O')
     *
     * @return string Formatted date
     */
    public static function date_format($format = null)
    {
        if (empty($format)) {
            $format = 'd-M-Y H:i:s O';
        }

        if (strpos($format, 'u') !== false) {
            $dt = number_format(microtime(true), 6, '.', '');

            try {
                $date = date_create_from_format('U.u', $dt);
                $date->setTimeZone(new DateTimeZone(date_default_timezone_get()));

                return $date->format($format);
            }
            catch (Exception $e) {
                // ignore, fallback to date()
            }
        }

        return date($format);
    }

    /**
     * Parses socket options and returns options for specified hostname.
     *
     * @param array  &$options Configured socket options
     * @param string $host     Hostname
     */
    public static function parse_socket_options(&$options, $host = null)
    {
        if (empty($host) || empty($options)) {
            return;
        }

        // get rid of schema and port from the hostname
        $host_url = parse_url($host);
        if (isset($host_url['host'])) {
            $host = $host_url['host'];
        }

        // find per-host options
        if ($host && array_key_exists($host, $options)) {
            $options = $options[$host];
        }
    }

    /**
     * Get maximum upload size
     *
     * @return int Maximum size in bytes
     */
    public static function max_upload_size()
    {
        // find max filesize value
        $max_filesize = parse_bytes(ini_get('upload_max_filesize'));
        $max_postsize = parse_bytes(ini_get('post_max_size'));

        if ($max_postsize && $max_postsize < $max_filesize) {
            $max_filesize = $max_postsize;
        }

        return $max_filesize;
    }

    /**
     * Detect and log last PREG operation error
     *
     * @param array $error     Error data (line, file, code, message)
     * @param bool  $terminate Stop script execution
     *
     * @return bool True on error, False otherwise
     */
    public static function preg_error($error = [], $terminate = false)
    {
        if (($preg_error = preg_last_error()) != PREG_NO_ERROR) {
            $errstr = "PCRE Error: $preg_error.";

            if (function_exists('preg_last_error_msg')) {
                $errstr .= ' ' . preg_last_error_msg();
            }

            if ($preg_error == PREG_BACKTRACK_LIMIT_ERROR) {
                $errstr .= " Consider raising pcre.backtrack_limit!";
            }
            if ($preg_error == PREG_RECURSION_LIMIT_ERROR) {
                $errstr .= " Consider raising pcre.recursion_limit!";
            }

            $error = array_merge(['code' => 620, 'line' => __LINE__, 'file' => __FILE__], $error);

            if (!empty($error['message'])) {
                $error['message'] .= ' ' . $errstr;
            }
            else {
                $error['message'] = $errstr;
            }

            rcube::raise_error($error, true, $terminate);

            return true;
        }

        return false;
    }

    /**
     * Generate a temporary file path in the Roundcube temp directory
     *
     * @param string $file_name String identifier for the type of temp file
     * @param bool   $unique    Generate unique file names based on $file_name
     * @param bool   $create    Create the temp file or not
     *
     * @return string temporary file path
     */
    public static function temp_filename($file_name, $unique = true, $create = true)
    {
        $temp_dir = rcube::get_instance()->config->get('temp_dir');

        // Fall back to system temp dir if configured dir is not writable
        if (!is_writable($temp_dir)) {
            $temp_dir = sys_get_temp_dir();
        }

        // On Windows tempnam() uses only the first three characters of prefix so use uniqid() and manually add the prefix
        // Full prefix is required for garbage collection to recognise the file
        $temp_file = $unique ? str_replace('.', '', uniqid($file_name, true)) : $file_name;
        $temp_path = unslashify($temp_dir) . '/' . RCUBE_TEMP_FILE_PREFIX . $temp_file;

        // Sanity check for unique file name
        if ($unique && file_exists($temp_path)) {
            return self::temp_filename($file_name, $unique, $create);
        }

        // Create the file to prevent possible race condition like tempnam() does
        if ($create) {
            touch($temp_path);
        }

        return $temp_path;
    }

    /**
     * Clean the subject from reply and forward prefix
     * 
     * @param string $subject Subject to clean
     * @param string $mode Mode of cleaning : reply, forward or both
     * 
     * @return string Cleaned subject
     */
    public static function remove_subject_prefix($subject, $mode = 'both')
    {
        $config = rcmail::get_instance()->config;

        // Clean subject prefix for reply, forward or both
        if ($mode == 'both') {
            $reply_prefixes = $config->get('subject_reply_prefixes', ['Re:']);
            $forward_prefixes = $config->get('subject_forward_prefixes', ['Fwd:', 'Fw:']);
            $prefixes = array_merge($reply_prefixes, $forward_prefixes);
        }
        else if ($mode == 'reply') {
            $prefixes = $config->get('subject_reply_prefixes', ['Re:']);
            // replace (was: ...) (#1489375)
            $subject = preg_replace('/\s*\([wW]as:[^\)]+\)\s*$/', '', $subject);
        }
        else if ($mode == 'forward') {
            $prefixes = $config->get('subject_forward_prefixes', ['Fwd:', 'Fw:']);
        }

        // replace Re:, Re[x]:, Re-x (#1490497)
        $pieces = array_map(function($prefix) {
            $prefix = strtolower(str_replace(':', '', $prefix));
            return "$prefix:|$prefix\[\d\]:|$prefix-\d:";
        }, $prefixes);
        $pattern = '/^('.implode('|', $pieces).')\s*/i';
        do {
            $subject = preg_replace($pattern, '', $subject, -1, $count);
        }
        while ($count);

        return trim($subject);
    }
}
