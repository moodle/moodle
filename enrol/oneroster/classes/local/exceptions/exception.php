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
 * One Roster Enrolment Client.
 *
 * @package    enrol_oneroster
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace enrol_oneroster\local\exceptions;

use \Exception as base_exception;
use moodle_url;

/**
 * Generic OneRoster Exception.
 *
 * @package    enrol_oneroster
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class exception extends base_exception {

    /** @var moodle_url The URL that was requested */
    protected $url;

    /**
     * Set the URL that this exception relates to.
     *
     * @param   moodle_url $url
     */
    protected function set_url(moodle_url $url): void {
        $this->url = $url;
    }

    /**
     * Get the URL that this exception relates to.
     *
     * @return  moodle_url|null
     */
    public function get_url(): ?moodle_url {
        return $this->url;
    }

    /**
     * Create a new IMSx Error of the appropriate type.
     *
     * @param   string $body
     * @param   array $info
     * @param   moodle_url $url
     * @return  null|exception
     */
    public static function create_from_imsx_error(string $body, array $info, moodle_url $url): ?exception {
        $decodedbody = json_decode($body);
        if (empty($decodedbody)) {
            return null;
        }

        if (!is_object($decodedbody)) {
            return null;
        }

        if (!property_exists($decodedbody, 'statusInfoSet')) {
            return null;
        }

        if (empty($decodedbody->statusInfoSet)) {
            return null;
        }

        $firstfailure = reset($decodedbody->statusInfoSet);

        if (!property_exists($firstfailure, 'imsx_codeMajor')) {
            return null;
        }

        if ($firstfailure->imsx_codeMajor !== 'failure') {
            return null;
        }

        return new failure($firstfailure, $info, $url);
    }

    /**
     * Create a new Error from an HTTP Code.
     *
     * @param   string $body
     * @param   array $info
     * @param   moodle_url $url
     * @return  null|exception
     */
    protected static function create_from_http_response(
        string $body,
        array $info,
        moodle_url $url
    ): ?exception {
        $args = func_get_args();
        switch ($info['http_code']) {
            case 400:
                // Bad Request - the Request was invalid and cannot be served.
                return new bad_request(...$args);
            case 401:
                // Unauthorized - the Request requires authorization.
                return new unauthorized(...$args);
            case 403:
                // Introduced in OneRoster v1p1.
                // Forbidden - to indicate that the server can be reached and process the request but refuses to take
                // any further action.
                return new forbidden(...$args);
            case 404:
                // Not Found - there is no resource behind the URI.
                return new not_found(...$args);
            case 422:
                // Entity cannot be processed - used where the server cannot validate an incoming entity.
                return new entity_cannot_be_processed(...$args);
            case 429:
                // Introduced in OneRoster v1p1.
                // The server is receiving too many requests. Retry at a later time.
                return new retry_later(...$args);
            case 500:
                // Internal Server Error.
                return new internal_server_error(...$args);
            default:
                return self::create_from_imsx_error(...$args);
        }

        return null;
    }

    /**
     * Check for any exception and throw if found.
     *
     * @param   string $body
     * @param   array $info
     * @param   moodle_url $url
     * @return  null|exception
     */
    public static function check_and_throw_from_http_response(
        string $body,
        array $info,
        moodle_url $url
    ): void {
        if ($exception = self::create_from_http_response($body, $info, $url)) {
            throw $exception;
        }
    }
}
