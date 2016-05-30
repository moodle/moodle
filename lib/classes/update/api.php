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
 * The class \core\update\api is defined here.
 *
 * @package     core
 * @copyright   2015 David Mudrak <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\update;

use curl;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/filelib.php');

/**
 * General purpose client for https://download.moodle.org/api/
 *
 * The API provides proxy access to public information about plugins available
 * in the Moodle Plugins directory. It is used when we are checking for
 * updates, resolving missing dependecies or installing a plugin. This client
 * can be used to:
 *
 * - obtain information about particular plugin version
 * - locate the most suitable plugin version for the given Moodle branch
 *
 * TODO:
 *
 * - Convert \core\update\checker to use this client too, so that we have a
 *   single access point for all the API services.
 * - Implement client method for pluglist.php even if it is not actually
 *   used by the Moodle core.
 *
 * @copyright 2015 David Mudrak <david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class api {

    /** The root of the standard API provider */
    const APIROOT = 'https://download.moodle.org/api';

    /** The API version to be used by this client */
    const APIVER = '1.3';

    /**
     * Factory method returning an instance of the class.
     *
     * @return \core\update\api client instance
     */
    public static function client() {
        return new static();
    }

    /**
     * Constructor is protected, use the factory method.
     */
    protected function __construct() {
    }

    /**
     * Returns info about the particular plugin version in the plugins directory.
     *
     * Uses pluginfo.php end-point to find the given plugin version in the
     * Moodle plugins directory. This is typically used to handle the
     * installation request coming from the plugins directory (aka clicking the
     * "Install" button there).
     *
     * If a plugin with the given component name is found, data about the
     * plugin are returned as an object. The ->version property of the object
     * contains the information about the requested plugin version.  The
     * ->version property is false if the requested version of the plugin was
     * not found (yet the plugin itself is known).
     *
     * @param string $component frankenstyle name of the plugin
     * @param int $version plugin version as declared via $plugin->version in its version.php
     * @return \core\update\remote_info|bool
     */
    public function get_plugin_info($component, $version) {

        $params = array(
            'plugin' => $component.'@'.$version,
            'format' => 'json',
        );

        return $this->call_pluginfo_service($params);
    }

    /**
     * Locate the given plugin in the plugin directory.
     *
     * Uses pluginfo.php end-point to find a plugin with the given component
     * name, that suits best for the given Moodle core branch. Minimal required
     * plugin version can be specified. This is typically used for resolving
     * dependencies.
     *
     * False is returned on error, or if there is no plugin with such component
     * name found in the plugins directory via the API.
     *
     * If a plugin with the given component name is found, data about the
     * plugin are returned as an object. The ->version property of the object
     * contains the information about the particular plugin version that
     * matches best the given critera. The ->version property is false if no
     * suitable version of the plugin was found (yet the plugin itself is
     * known).
     *
     * @param string $component frankenstyle name of the plugin
     * @param string|int $reqversion minimal required version of the plugin, defaults to ANY_VERSION
     * @param int $branch moodle core branch such as 29, 30, 31 etc, defaults to $CFG->branch
     * @return \core\update\remote_info|bool
     */
    public function find_plugin($component, $reqversion=ANY_VERSION, $branch=null) {
        global $CFG;

        $params = array(
            'plugin' => $component,
            'format' => 'json',
        );

        if ($reqversion === ANY_VERSION) {
            $params['minversion'] = 0;
        } else {
            $params['minversion'] = $reqversion;
        }

        if ($branch === null) {
            $branch = $CFG->branch;
        }

        $params['branch'] = $this->convert_branch_numbering_format($branch);

        return $this->call_pluginfo_service($params);
    }

    /**
     * Makes sure the given data format match the expected output of the pluginfo service.
     *
     * Object validated by this method is guaranteed to contain all the data
     * provided by the pluginfo.php version this client works with (self::APIVER).
     *
     * @param stdClass $data
     * @return \core\update\remote_info|bool false if data are not valid, original data otherwise
     */
    public function validate_pluginfo_format($data) {

        if (empty($data) or !is_object($data)) {
            return false;
        }

        $output = new remote_info();

        $rootproperties = array('id' => 1, 'name' => 1, 'component' => 1, 'source' => 0, 'doc' => 0,
            'bugs' => 0, 'discussion' => 0, 'version' => 0);
        foreach ($rootproperties as $property => $required) {
            if (!property_exists($data, $property)) {
                return false;
            }
            if ($required and empty($data->$property)) {
                return false;
            }
            $output->$property = $data->$property;
        }

        if (!empty($data->version)) {
            if (!is_object($data->version)) {
                return false;
            }
            $versionproperties = array('id' => 1, 'version' => 1, 'release' => 0, 'maturity' => 0,
                'downloadurl' => 1, 'downloadmd5' => 1, 'vcssystem' => 0, 'vcssystemother' => 0,
                'vcsrepositoryurl' => 0, 'vcsbranch' => 0, 'vcstag' => 0, 'supportedmoodles' => 0);
            foreach ($versionproperties as $property => $required) {
                if (!property_exists($data->version, $property)) {
                    return false;
                }
                if ($required and empty($data->version->$property)) {
                    return false;
                }
            }
            if (!preg_match('|^https?://|i', $data->version->downloadurl)) {
                return false;
            }

            if (!empty($data->version->supportedmoodles)) {
                if (!is_array($data->version->supportedmoodles)) {
                    return false;
                }
                foreach ($data->version->supportedmoodles as $supportedmoodle) {
                    if (!is_object($supportedmoodle)) {
                        return false;
                    }
                    if (empty($supportedmoodle->version) or empty($supportedmoodle->release)) {
                        return false;
                    }
                }
            }
        }

        return $output;
    }

    /**
     * Calls the pluginfo.php end-point with given parameters.
     *
     * @param array $params
     * @return \core\update\remote_info|bool
     */
    protected function call_pluginfo_service(array $params) {

        $serviceurl = $this->get_serviceurl_pluginfo();
        $response = $this->call_service($serviceurl, $params);

        if ($response) {
            if ($response->info['http_code'] == 404) {
                // There is no such plugin found in the plugins directory.
                return false;

            } else if ($response->info['http_code'] == 200 and isset($response->data->status)
                    and $response->data->status === 'OK' and $response->data->apiver == self::APIVER
                    and isset($response->data->pluginfo)) {
                    return $this->validate_pluginfo_format($response->data->pluginfo);

            } else {
                debugging('cURL: Unexpected response', DEBUG_DEVELOPER);
                return false;
            }
        }

        return false;
    }

    /**
     * Calls the given end-point service with the given parameters.
     *
     * Returns false on cURL error and/or SSL verification failure. Otherwise
     * an object with the response, cURL info and HTTP status message is
     * returned.
     *
     * @param string $serviceurl
     * @param array $params
     * @return stdClass|bool
     */
    protected function call_service($serviceurl, array $params=array()) {

        $response = (object)array(
            'data' => null,
            'info' => null,
            'status' => null,
        );

        $curl = new curl();

        $response->data = json_decode($curl->get($serviceurl, $params, array(
            'CURLOPT_SSL_VERIFYHOST' => 2,
            'CURLOPT_SSL_VERIFYPEER' => true,
        )));

        $curlerrno = $curl->get_errno();

        if (!empty($curlerrno)) {
            debugging('cURL: Error '.$curlerrno.' when calling '.$serviceurl, DEBUG_DEVELOPER);
            return false;
        }

        $response->info = $curl->get_info();

        if (isset($response->info['ssl_verify_result']) and $response->info['ssl_verify_result'] != 0) {
            debugging('cURL/SSL: Unable to verify remote service response when calling '.$serviceurl, DEBUG_DEVELOPER);
            return false;
        }

        // The first response header with the HTTP status code and reason phrase.
        $response->status = array_shift($curl->response);

        return $response;
    }

    /**
     * Converts the given branch from XY format to the X.Y format
     *
     * The syntax of $CFG->branch uses the XY format that suits the Moodle docs
     * versioning and stable branches numbering scheme. The API at
     * download.moodle.org uses the X.Y numbering scheme.
     *
     * @param int $branch moodle branch in the XY format (e.g. 29, 30, 31 etc)
     * @return string moodle branch in the X.Y format (e.g. 2.9, 3.0, 3.1 etc)
     */
    protected function convert_branch_numbering_format($branch) {

        $branch = (string)$branch;

        if (strpos($branch, '.') === false) {
            $branch = substr($branch, 0, -1).'.'.substr($branch, -1);
        }

        return $branch;
    }

    /**
     * Returns URL of the pluginfo.php API end-point.
     *
     * @return string
     */
    protected function get_serviceurl_pluginfo() {
        global $CFG;

        if (!empty($CFG->config_php_settings['alternativepluginfoserviceurl'])) {
            return $CFG->config_php_settings['alternativepluginfoserviceurl'];
        } else {
            return self::APIROOT.'/'.self::APIVER.'/pluginfo.php';
        }
    }
}
