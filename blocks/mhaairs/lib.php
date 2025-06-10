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
 * ZEND Web Services Plugin for block MHAAIRS
 *
 * @package     block_mhaairs
 * @copyright   2014 Itamar Tzadok <itamar@substantialmethods.com>
 * @copyright   2013 Moodlerooms inc.
 * @author      Teresa Hardy <thardy@moodlerooms.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Class for the mhaairs connect requests.
 *
 * @package     block_mhaairs
 * @copyright   2014 Itamar Tzadok <itamar@substantialmethods.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_mhaairs_connect {

    /**
     * Returns help urls from Tegrity.
     * If not forcing fetch, tries to retrieve from cache.
     * Fetched data is cached.
     *
     * @param bool $forcefetch
     * @return bool|array|string
     */
    public static function get_help_urls($forcefetch = false) {
        // Must have customer number.
        if (!$customernumber = self::get_customer_number()) {
            return false;
        }

        // Return cache if exists.
        if (!$forcefetch) {
            $cached = self::get_cache('help');
            if ($cached !== null) {
                return $cached;
            }
        }

        // Fetch data from connect.
        $resultdata = self::request($customernumber, 'GetHelpLinks');

        // Cache.
        self::set_cache('help', $resultdata);

        return $resultdata;
    }

    /**
     * Returns available service from Tegrity.
     * If not forcing fetch, tries to retrieve from cache.
     * Fetched data is cached.
     *
     * @param bool $forcefetch
     * @return bool|array|string
     */
    public static function get_services($forcefetch = false) {
        // Must have customer number.
        if (!$customernumber = self::get_customer_number()) {
            return false;
        }

        // Return cache if exists.
        if (!$forcefetch) {
            $cached = self::get_cache('services');
            if ($cached !== null) {
                return $cached;
            }
        }

        // Fetch data from connect.
        $resultdata = self::request($customernumber, 'GetCustomerAvailableTools');

        // Cache.
        self::set_cache('services', $resultdata);

        return $resultdata;
    }

    /**
     *
     */
    protected static function request($customernumber, $endpoint) {
        static $zendready = false;

        if (!$zendready) {
            $dir = get_config('core', 'dirroot');
            set_include_path(get_include_path().PATH_SEPARATOR.$dir.'/blocks/mhaairs/lib');
            $zendready = true;
        }

        // @codingStandardsIgnoreStart
        require_once('Zend/Json.php');
        require_once('Zend/Oauth/Consumer.php');
        require_once('Zend/Oauth/Client.php');
        // @codingStandardsIgnoreEnd

        $baseurl = self::get_endpoint_base_url();
        $url = $baseurl.$customernumber.'/'.$endpoint;

        $aconfig = array(
                'requestScheme'   => Zend_Oauth::REQUEST_SCHEME_QUERYSTRING,
                'requestMethod'   => Zend_Oauth::GET,
                'signatureMethod' => 'HMAC-SHA1',
                'consumerKey'     => 'SSOConfig',
                'consumerSecret'  => '3DC9C384'
        );

        $resultdata = false;
        try {
            $tacc = new Zend_Oauth_Token_Access();
            $client = $tacc->getHttpClient($aconfig, $url);
            $client->setMethod(Zend_Oauth_Client::GET);
            $client->setEncType(Zend_Oauth_Client::ENC_URLENCODED);

            $response    = $client->request();
            $resultdata = $response->getBody();

            // Get content type.
            $resulttype = $response->getHeader(Zend_Oauth_Client::CONTENT_TYPE);

            // Is this Json encoded data?
            if (stripos($resulttype, 'application/json') !== false) {
                $resultdata = Zend_Json::decode($resultdata);
            }

            // By default set the status to the HTTP response status.
            $status      = $response->getStatus();
            $description = $response->getMessage();
            if ($status != 200) {
                $resultdata = false;
            }
        } catch (Exception $e) {
            $status      = (string)$e->getCode();
            $description = $e->getMessage();
        }

        $logmsg = $status . ": " . $description;
        // TODO
        // There used to be add_to_log here recording the requested endpoint and log message.
        // This should be replaced with events system as soon as we decide what should be
        // logged.

        return $resultdata;
    }

    /**
     *
     */
    protected static function get_cache($name) {
        $cachename = "block_mhaairs_cache{$name}";
        $cached = get_config('core', $cachename);
        if ($cached !== false) {
            $result = unserialize($cached);
            return $result;
        }
        return null;
    }

    /**
     *
     */
    protected static function set_cache($name, $data) {
        $cachename = "block_mhaairs_cache{$name}";

        $tostore = serialize($data);
        set_config($cachename, $tostore);
    }

    /**
     *
     */
    protected static function get_customer_number() {
        global $CFG;

        if (empty($CFG->block_mhaairs_customer_number)) {
            return false;
        }
        return $CFG->block_mhaairs_customer_number;
    }

    /**
     * Returns the configured end point base url or default if not configured.
     * @return string
     */
    protected static function get_endpoint_base_url() {
        global $CFG;

        if (!empty($CFG->block_mhaairs_endpoint_url)) {
            return $CFG->block_mhaairs_endpoint_url;
        }
        return 'http://mhaairs.tegrity.com/v1/Config/';
    }
}
