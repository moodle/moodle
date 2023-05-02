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
 * @package filter_oembed
 * @author Mike Churchward <mike.churchward@poetgroup.org>
 * @author Erich M. Wappis <erich.wappis@uni-graz.at>
 * @author Guy Thomas <brudinie@googlemail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright 2016 The POET Group
 */

namespace filter_oembed\provider;

defined('MOODLE_INTERNAL') || die();

/**
 * Base class for oembed providers and plugins. Plugins should extend this class.
 * If "filter" is provided, there is nothing else a plugin needs to implement.
 * Plugins can instead / additionally override "get_oembed_request", "oembed_response" and "endpoints_regex".
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright 2016 The POET Group
 */
class provider {

    /**
     * @var int
     */
    protected $id;

    /**
     * @var boolean
     */
    protected $enabled;

    /**
     * @var string
     */
    protected $providername = '';

    /**
     * @var string
     */
    protected $providerurl = '';

    /**
     * @var endpoints
     */
    protected $endpoints = [];

    /**
     * @var source
     */
    protected $source = '';

    /**
     * @var Class constant descriptions.
     */
    const PROVIDER_SOURCE_LOCAL = 'local::';
    const PROVIDER_SOURCE_DOWNLOAD = 'download::';
    const PROVIDER_SOURCE_PLUGIN = 'plugin::';

    /**
     * Constructor.
     * Note - provider data is expcted to come from the moodle data (db) which excludes
     * "_" in variable names. Providers coming directly from oembed (http://oembed.com/providers.json),
     * include "_" in variable names, which violates the Moodle coding standard. Currently,
     * this is managed by the update processes to ensure compatibility.
     *
     * @param $data JSON decoded array or a data object containing all provider data.
     */
    public function __construct($data = null) {
        if (is_object($data)) {
            $data = (array)$data;
        }
        if (!empty($data)) {
            $this->id = isset($data['id']) ? $data['id'] : 0;
            $this->enabled = isset($data['enabled']) ? $data['enabled'] : false;
            $this->providername = $data['providername'];
            $this->providerurl = $data['providerurl'];

            // If the endpoint data is a string, assume its a json encoded string.
            if (is_string($data['endpoints'])) {
                $data['endpoints'] = json_decode($data['endpoints'], true);
            }
            if (is_array($data['endpoints'])) {
                foreach ($data['endpoints'] as $endpoint) {
                    $this->endpoints[] = new endpoint($endpoint);
                }
            } else {
                throw new \coding_exception('"endpoint" data must be an array for '.get_class($this));
            }

            $this->source = isset($data['source']) ? $data['source'] : '';
        }
    }

    /**
     * Main filter function. This should only be used by subplugins, and it is preferable
     * to not use it even then. Ideally, a provider plugin should provide a JSON oembed provider
     * response (http://oembed.com/#section2.3) and let the main filter handle the HTML. Use this
     * only if the HTML must be determined by the plugin. If implemented, ensure FALSE is returned
     * if no filtering occurred.
     *
     * @param string $text Incoming text.
     * @return string Filtered text, or false for no changes.
     */
    public function filter($text) {
        return false;
    }

    /**
     * Return the JSON decoded provider implementation info as in http://oembed.com/providers.json.
     *
     * @return array JSON decoded implemenation info.
     */
    public function implementation() {
        $implarr = [
            'provider_name' => $this->providername,
            'provider_url' => $this->providerurl,
            'endpoints' => [],
        ];
        foreach ($this->endpoints as $endpoint) {
            $implarr['endpoints'][] = [
                'schemes' => $endpoint->schemes,
                'url' => $endpoint->url,
                'discovery' => $endpoint->discovery,
                'formats' => $endpoint->formats,
            ];
        }
        return $implarr;
    }
    /**
     * If a matching endpoint scheme is found in the passed text, return a consumer request URL.
     *
     * @param string $text The text to look for an URL resource using provider's schemes.
     * @return string Consumer request URL.
     */
    public function get_oembed_request($text) {
        $requesturl = '';
        // For each endpoint, look for a matching scheme.
        foreach ($this->endpoints as $endpoint) {
            // Get the regex arrauy to look for matching schemes.
            $regexarr = $this->endpoints_regex($endpoint);
            foreach ($regexarr as $regex) {
                if (preg_match($regex, $text)) {
                    // If {format} is in the URL, replace it with the actual format.
                    // At the moment, we're only supporting JSON, so this must be JSON.
                    $requesturl = str_replace('{format}', 'json', $endpoint->url) .
                           '?url=' . urlencode($text) . '&format=json';
                    break 2; // Done, break out of all loops.
                }
            }
        }

        return $requesturl;
    }

    /**
     * Make a consumer oembed request and return the JSON provider response.
     *
     * @param string $url The consumer request URL.
     * @return array JSON decoded array.
     */
    public function oembed_response($url) {
        $ret = download_file_content($url, null, null, true, 300, 20, false, null, false);
        return json_decode($ret->results, true);
    }

    /**
     * Return the type of the provider source parameter (download, local, plugin).
     *
     * @param string $source The source value to get the type for.
     * @return string One of 'download::', 'local::', 'plugin::'.
     */
    public static function source_type($source) {
        $sourcetype = substr($source, 0, strpos($source, '::'));
        if (empty($sourcetype)) {
            $sourcetype = self::PROVIDER_SOURCE_LOCAL;
        } else {
            $sourcetype .= '::';
        }
        return $sourcetype;
    }

    /**
     * Return a regular expression that can be used to search text for an endpoint's schemes.
     *
     * @param endpoint $endpoint
     * @return array Array of regular expressions matching all endpoints and schemes.
     */
    protected function endpoints_regex(endpoint $endpoint) {
        $schemes = $endpoint->schemes;
        if (empty($schemes)) {
            $schemes = [$this->providerurl];
        }

        foreach ($schemes as $scheme) {
            // An "http[s]:" may not be present, so flag it as a non-capturing subpattern with "(?:".
            $url1 = preg_split('/((?:https?:)?\/\/)/', $scheme);
            $url2 = preg_split('/\//', $url1[1]);
            $regexarr = [];
            foreach ($url2 as $url) {
                $find = ['.', '*'];
                $replace = ['\.', '.*?'];
                $url = str_replace($find, $replace, $url);
                $regexarr[] = '('.$url.')';
            }

            $regex[] = '/(https?:\/\/)'.implode('\/', $regexarr).'/';
        }
        return $regex;
    }

    /**
     * Magic method for getting properties.
     * @param string $name
     * @return mixed
     * @throws \coding_exception
     */
    public function __get($name) {
        $allowed = ['id', 'enabled', 'providername', 'providerurl', 'endpoints', 'source'];
        if (in_array($name, $allowed)) {
            return $this->$name;
        } else {
            throw new \coding_exception($name.' is not a publicly accessible property of '.get_class($this));
        }
    }

    /**
     * Set enabled?
     * @param boolean $enabled
     */
    public function set_enabled($enabled) {
        $this->enabled = $enabled;
    }
}
