<?php

/**
 * Moodle - Modular Object-Oriented Dynamic Learning Environment
 *          http://moodle.org
 * Copyright (C) 1999 onwards Martin Dougiamas  http://dougiamas.com
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package    moodle
 * @subpackage lib
 * @author     Dan Poltawski <talktodan@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * Customised version of SimplePie for Moodle
 */

require_once($CFG->libdir.'/filelib.php');

// PLEASE NOTE: we use the simplepie class _unmodified_
// through the joys of OO. Distros are free to use their stock
// version of this file.
require_once($CFG->libdir.'/simplepie/autoloader.php');

/**
 * Moodle Customised version of the SimplePie class
 *
 * This class extends the stock SimplePie class
 * in order to make sensible configuration choices,
 * such as using the Moodle cache directory and
 * curl functions/proxy config for making http
 * requests in line with moodle configuration.
 *
 * @copyright 2009 Dan Poltawski <talktodan@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class moodle_simplepie extends SimplePie\SimplePie {
    /**
     * Constructor - creates an instance of the SimplePie class
     * with Moodle defaults.
     *
     * @param string $feedurl optional URL of the feed
     * @param int $timeout how many seconds requests should wait for server response
     */
    public function __construct($feedurl = null, $timeout = 2) {
        $cachedir = moodle_simplepie::get_cache_directory();
        check_dir_exists($cachedir);

        parent::__construct();

        // Use the Moodle class for http requests
        $registry = $this->get_registry();
        $registry->register(SimplePie\File::class, 'moodle_simplepie_file', true);

        // Use html purifier for text cleaning.
        $registry->register(SimplePie\Sanitize::class, 'moodle_simplepie_sanitize', true);
        $this->sanitize = new moodle_simplepie_sanitize();

        // Match moodle encoding
        $this->set_output_encoding('UTF-8');

        // default to a short timeout as most operations will be interactive
        $this->set_timeout($timeout);

        // 1 hour default cache
        $this->set_cache_location($cachedir);
        $this->set_cache_duration(3600);

        // init the feed url if passed in constructor
        if ($feedurl !== null) {
            $this->set_feed_url($feedurl);
            $this->init();
        }
    }

    /**
     * Get path for feed cache directory
     *
     * @return string absolute path to cache directory
     */
    private static function get_cache_directory() {
        global $CFG;

        return $CFG->cachedir.'/simplepie/';
    }

    /**
     * Reset RSS cache
     *
     * @return boolean success if cache clear or didn't exist
     */
    public static function reset_cache() {

        $cachedir = moodle_simplepie::get_cache_directory();

        return remove_dir($cachedir);
    }
}

/**
 * Moodle Customised version of the SimplePie_File class
 *
 * This class extends the stock SimplePie class
 * in order to utilise Moodles own curl class for making
 * http requests. By using the moodle curl class
 * we ensure that the correct proxy configuration is used.
 */
class moodle_simplepie_file extends SimplePie\File {

    /**
     * The constructor is a copy of the stock simplepie File class which has
     * been modified to add in use the Moodle curl class rather than php curl
     * functions.
     */
    public function __construct($url, $timeout = 10, $redirects = 5, $headers = null, $useragent = null, $force_fsockopen = false) {
        $this->url = $url;
        $this->method = SimplePie\SimplePie::FILE_SOURCE_REMOTE | SimplePie\SimplePie::FILE_SOURCE_CURL;

        $curl = new curl();
        $curl->setopt( array(
                'CURLOPT_HEADER' => true,
                'CURLOPT_TIMEOUT' => $timeout,
                'CURLOPT_CONNECTTIMEOUT' => $timeout ));


        if ($headers !== null) {
            // translate simplepie headers to those class curl expects
            foreach($headers as $headername => $headervalue){
                $headerstr = "{$headername}: {$headervalue}";
                $curl->setHeader($headerstr);
            }
        }

        $this->headers = curl::strip_double_headers($curl->get($url));

        if ($curl->error) {
            $this->error = 'cURL Error: '.$curl->error;
            $this->success = false;
            return false;
        }

        $parser = new SimplePie\HTTP\Parser($this->headers);

        if ($parser->parse()) {
            $this->headers = $parser->headers;
            $this->body = trim($parser->body);
            $this->status_code = $parser->status_code;


            if (($this->status_code == 300 || $this->status_code == 301 || $this->status_code == 302 || $this->status_code == 303
                    || $this->status_code == 307 || $this->status_code > 307 && $this->status_code < 400)
                    && isset($this->headers['location']) && $this->redirects < $redirects) {
                $this->redirects++;
                $location = SimplePie\Misc::absolutize_url($this->headers['location'], $url);
                return $this->__construct($location, $timeout, $redirects, $headers);
            }
        }
    }
}


/**
 * Customised feed sanitization using HTMLPurifier.
 */
class moodle_simplepie_sanitize extends SimplePie\Sanitize {
    public function sanitize($data, $type, $base = '') {
        $data = trim($data);

        if ($data === '') {
            return '';
        }

        if ($type & SimplePie\SimplePie::CONSTRUCT_BASE64){
            $data = base64_decode($data);
        }

        if ($type & SimplePie\SimplePie::CONSTRUCT_MAYBE_HTML) {
            if (preg_match('/(&(#(x[0-9a-fA-F]+|[0-9]+)|[a-zA-Z0-9]+)|<\/[A-Za-z][^\x09\x0A\x0B\x0C\x0D\x20\x2F\x3E]*'
                    . SimplePie\SimplePie::PCRE_HTML_ATTRIBUTE . '>)/', $data)) {
                $type |= SimplePie\SimplePie::CONSTRUCT_HTML;
            } else {
                $type |= SimplePie\SimplePie::CONSTRUCT_TEXT;
            }
        }

        if ($type & SimplePie\SimplePie::CONSTRUCT_IRI) {
            $absolute = $this->registry->call('Misc', 'absolutize_url', array($data, $base));
            if ($absolute !== false) {
                $data = $absolute;
            }
            $data = clean_param($data, PARAM_URL);
        }

        if ($type & (SimplePie\SimplePie::CONSTRUCT_TEXT | SimplePie\SimplePie::CONSTRUCT_IRI)) {
            $data = htmlspecialchars($data, ENT_COMPAT, 'UTF-8');
        }

        $data = purify_html($data);

        if ($this->remove_div) {
            $data = preg_replace('/^<div' . SimplePie\SimplePie::PCRE_XML_ATTRIBUTE . '>/', '', $data);
            $data = preg_replace('/<\/div>$/', '', $data);
        } else {
            $data = preg_replace('/^<div' . SimplePie\SimplePie::PCRE_XML_ATTRIBUTE . '>/', '<div>', $data);
        }

        if ($this->output_encoding !== 'UTF-8') {
            core_text::convert($data, 'UTF-8', $this->output_encoding);
        }

        return $data;
    }
}
