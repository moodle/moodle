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
 * Utility functions.
 *
 * @package auth_oidc
 * @author James McQuillan <james.mcquillan@remote-learner.net>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2014 onwards Microsoft, Inc. (http://microsoft.com/)
 */

namespace auth_oidc;

use Exception;
use moodle_exception;
use auth_oidc\event\action_failed;

defined('MOODLE_INTERNAL') || die();

/**
 * General purpose utility class.
 */
class utils {
    /**
     * Process an OIDC JSON response.
     *
     * @param string $response The received JSON.
     * @param array $expectedstructure
     * @return array The parsed JSON.
     * @throws moodle_exception
     */
    public static function process_json_response($response, array $expectedstructure = array()) {
        $result = @json_decode($response, true);
        if (empty($result) || !is_array($result)) {
            self::debug('Bad response received', __METHOD__, $response);
            throw new moodle_exception('erroroidccall', 'auth_oidc');
        }

        if (isset($result['error'])) {
            $errmsg = 'Error response received.';
            self::debug($errmsg, __METHOD__, $result);
            if (isset($result['error_description'])) {
                throw new moodle_exception('erroroidccall_message', 'auth_oidc', '', $result['error_description']);
            } else {
                throw new moodle_exception('erroroidccall', 'auth_oidc');
            }
        }

        foreach ($expectedstructure as $key => $val) {
            if (!isset($result[$key])) {
                $errmsg = 'Invalid structure received. No "'.$key.'"';
                self::debug($errmsg, __METHOD__, $result);
                throw new moodle_exception('erroroidccall', 'auth_oidc');
            }

            if ($val !== null && $result[$key] !== $val) {
                $strreceivedval = self::tostring($result[$key]);
                $strval = self::tostring($val);
                $errmsg = 'Invalid structure received. Invalid "'.$key.'". Received "'.$strreceivedval.'", expected "'.$strval.'"';
                self::debug($errmsg, __METHOD__, $result);
                throw new moodle_exception('erroroidccall', 'auth_oidc');
            }
        }
        return $result;
    }

    /**
     * Convert any value into a debuggable string.
     *
     * @param mixed $val The variable to convert.
     * @return string A string representation.
     */
    public static function tostring($val) {
        if (is_scalar($val)) {
            if (is_bool($val)) {
                return '(bool)'.(string)(int)$val;
            } else {
                return '('.gettype($val).')'.(string)$val;
            }
        } else if (is_null($val)) {
            return '(null)';
        } else if ($val instanceof Exception) {
            $valinfo = [
                'file' => $val->getFile(),
                'line' => $val->getLine(),
                'message' => $val->getMessage(),
            ];
            if ($val instanceof moodle_exception) {
                $valinfo['debuginfo'] = $val->debuginfo;
                $valinfo['errorcode'] = $val->errorcode;
                $valinfo['module'] = $val->module;
            }
            return print_r($valinfo, true);
        } else {
            return print_r($val, true);
        }
    }

    /**
     * Record a debug message.
     *
     * @param string $message The debug message to log.
     * @param string $where
     * @param null $debugdata
     */
    public static function debug($message, $where = '', $debugdata = null) {
        $debugmode = (bool)get_config('auth_oidc', 'debugmode');
        if ($debugmode === true) {
            $backtrace = debug_backtrace();
            $otherdata = [
                'other' => [
                    'message' => $message,
                    'where' => $where,
                    'debugdata' => $debugdata,
                    'backtrace' => $backtrace,
                ],
            ];
            $event = action_failed::create($otherdata);
            $event->trigger();
        }
    }

    /**
     * Get the redirect URL that should be set in the identity provider
     *
     * @return string The redirect URL.
     */
    public static function get_redirecturl() {
        $redirecturl = new \moodle_url('/auth/oidc/');
        return $redirecturl->out(false);
    }

    /**
     * Get the front channel logout URL that should be set in the identity provider.
     *
     * @return string The redirect URL.
     */
    public static function get_frontchannellogouturl() {
        $logouturl = new \moodle_url('/auth/oidc/logout.php');
        return $logouturl->out(false);
    }

    /**
     * Get and check existence of OIDC client certificate path.
     *
     * @return string|bool cert path if exists otherwise false
     */
    public static function get_certpath() {
        $clientcertfile = get_config('auth_oidc', 'clientcertfile');
        $certlocation = self::get_openssl_internal_path();
        $certfile = "$certlocation/$clientcertfile";

        if (is_file($certfile) && is_readable($certfile)) {
            return "file://$certfile";
        }

        return false;
    }

    /**
     * Get and check existence of OIDC client key path.
     *
     * @return string|bool key path if exists otherwise false
     */
    public static function get_keypath() {
        $clientprivatekeyfile = get_config('auth_oidc', 'clientprivatekeyfile');
        $keylocation = self::get_openssl_internal_path();
        $keyfile = "$keylocation/$clientprivatekeyfile";

        if (is_file($keyfile) && is_readable($keyfile)) {
            return "file://$keyfile";
        }

        return false;
    }

    /**
     * Get openssl cert base path, which is dataroot/microsoft_certs.
     *
     * @return string base path to put cert files
     */
    public static function get_openssl_internal_path() {
        global $CFG;

        return $CFG->dataroot . '/microsoft_certs';
    }
}
