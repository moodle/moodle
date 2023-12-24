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

namespace mod_bigbluebuttonbn\local\proxy;

use mod_bigbluebuttonbn\extension;
use mod_bigbluebuttonbn\local\config;
use mod_bigbluebuttonbn\local\exceptions\bigbluebutton_exception;
use mod_bigbluebuttonbn\local\exceptions\server_not_available_exception;
use mod_bigbluebuttonbn\plugin;
use moodle_url;

/**
 * The abstract proxy base class.
 *
 * This class provides common and shared functions used when interacting with
 * the BigBlueButton API server.
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2010 onwards, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Jesus Federico  (jesus [at] blindsidenetworks [dt] com)
 */
abstract class proxy_base {

    /**
     * Sometimes the server sends back some error and errorKeys that
     * can be converted to Moodle error messages
     */
    const BBB_TO_MOODLE_ERROR_CODE = [
        'checksumError' => 'index_error_checksum',
        'notFound' => 'general_error_not_found',
        'maxConcurrent' => 'view_error_max_concurrent',
    ];

    /**
     * Returns the right URL for the action specified.
     *
     * @param string $action
     * @param array $data
     * @param array $metadata
     * @param int|null $instanceid
     * @return string
     */
    protected static function action_url(
        string $action = '',
        array $data = [],
        array $metadata = [],
        ?int $instanceid = null
    ): string {
        $baseurl = self::sanitized_url() . $action . '?';
        ['data' => $additionaldata, 'metadata' => $additionalmetadata] =
            extension::action_url_addons($action, $data, $metadata, $instanceid);
        $data = array_merge($data, $additionaldata ?? []);
        $metadata = array_merge($metadata, $additionalmetadata ?? []);

        $metadata = array_combine(array_map(function($k) {
            return 'meta_' . $k;
        }, array_keys($metadata)), $metadata);
        $params = http_build_query($data + $metadata, '', '&');
        $checksum = self::get_checksum($action, $params);
        return $baseurl . $params . '&checksum=' . $checksum;
    }

    /**
     * Makes sure the url used doesn't is in the format required.
     *
     * @return string
     */
    protected static function sanitized_url(): string {
        $serverurl = trim(config::get('server_url'));
        if (PHPUNIT_TEST) {
            $serverurl = (new moodle_url(TEST_MOD_BIGBLUEBUTTONBN_MOCK_SERVER))->out(false);
        }
        if (substr($serverurl, -1) == '/') {
            $serverurl = rtrim($serverurl, '/');
        }
        if (substr($serverurl, -4) == '/api') {
            $serverurl = rtrim($serverurl, '/api');
        }
        return $serverurl . '/api/';
    }

    /**
     * Makes sure the shared_secret used doesn't have trailing white characters.
     *
     * @return string
     */
    protected static function sanitized_secret(): string {
        return trim(config::get('shared_secret'));
    }

    /**
     * Throw an exception if there is a problem in the returned XML value
     *
     * @param \SimpleXMLElement|bool $xml
     * @param array|null $additionaldetails
     * @throws bigbluebutton_exception
     * @throws server_not_available_exception
     */
    protected static function assert_returned_xml($xml, ?array $additionaldetails = null): void {
        $messagekey = '';
        if (!empty($xml)) {
            $messagekey = (string) ($xml->messageKey ?? '');
        }
        if (empty($xml) || static::is_known_server_unavailable_errorcode($messagekey)) {
            $errorcode = self::get_errorcode_from_xml_messagekey($messagekey);
            throw new server_not_available_exception(
                $errorcode,
                plugin::COMPONENT,
                (new moodle_url('/admin/settings.php?section=modsettingbigbluebuttonbn'))->out(),
            );
        }
        // If it is a checksum error, this is equivalent to the server not being available.
        // So we treat it the same way as if there is not answer.
        if (is_bool($xml) && $xml) {
            // Nothing to do here, this might be a post returning that everything went well.
            return;
        }
        if ((string) $xml->returncode == 'FAILED') {
            $errorcode = self::get_errorcode_from_xml_messagekey($messagekey);
            if (!$additionaldetails) {
                $additionaldetails = [];
            }
            $additionaldetails['xmlmessage'] = (string) $xml->message ?? '';

            throw new bigbluebutton_exception($errorcode, json_encode($additionaldetails));
        }
    }

    /**
     * Get Moodle error code from returned Message Key
     *
     * @param string $messagekey
     * @return string
     */
    private static function get_errorcode_from_xml_messagekey(string $messagekey): string {
        $errorcode = 'general_error_no_answer';
        if ($messagekey) {
            $errorcode = self::BBB_TO_MOODLE_ERROR_CODE[$messagekey] ?? $errorcode;
        }
        return $errorcode;
    }

    /**
     * Get Moodle error code from returned Message Key
     *
     * @param string $messagekey
     * @return string
     */
    private static function is_known_server_unavailable_errorcode(string $messagekey): string {
        // For now, only checksumError is supposed to mean that the server is unavailable.
        // Other errors are recoverable.
        return in_array($messagekey, ['checksumError']);
    }

    /**
     * Fetch the XML from an endpoint and test for success.
     *
     * If the result could not be loaded, or the returncode was not 'SUCCESS', a null value is returned.
     *
     * @param string $action
     * @param array $data
     * @param array $metadata
     * @param int|null $instanceid
     * @return null|bool|\SimpleXMLElement
     */
    protected static function fetch_endpoint_xml(
        string $action,
        array $data = [],
        array $metadata = [],
        ?int $instanceid = null
    ) {
        if (PHPUNIT_TEST && !defined('TEST_MOD_BIGBLUEBUTTONBN_MOCK_SERVER')) {
            return true; // In case we still use fetch and mock server is not defined, this prevents
            // an error. This can happen if a function from lib.php is called in test from other modules
            // for example.
        }
        $curl = new curl();
        return $curl->get(self::action_url($action, $data, $metadata, $instanceid));
    }

    /**
     * Get checksum
     *
     * @param string $action
     * @param string $params
     * @return string
     */
    public static function get_checksum(string $action, string $params): string {
        return hash(config::get('checksum_algorithm'), $action . $params . self::sanitized_secret());
    }
}
