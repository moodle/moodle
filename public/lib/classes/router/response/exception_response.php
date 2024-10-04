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

namespace core\router\response;

use core\param;
use core\router\schema\objects\scalar_type;
use core\router\schema\objects\schema_object;
use core\router\schema\objects\stacktrace;
use core\router\schema\referenced_object;
use core\router\schema\response\content\payload_response_type;
use core\router\schema\response\payload_response;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface;

/**
 * A standard response for user preferences.
 *
 * @package    core
 * @copyright  2023 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class exception_response extends \core\router\schema\response\response implements
    referenced_object
{
    /**
     * Constructor for a new exception-related response.
     */
    public function __construct() {
        parent::__construct(
            statuscode: static::get_exception_status_code(),
            description: static::get_response_description(),
            content: new payload_response_type(
                schema: static::get_response_schema(),
            ),
        );
    }

    /**
     * Get the response for the exception.
     *
     * @param ServerRequestInterface $request
     * @param \Exception $exception
     * @param mixed[] ...$extra
     * @return payload_response
     */
    public static function get_response(
        ServerRequestInterface $request,
        \Exception $exception,
        ...$extra,
    ): payload_response {
        return new payload_response(
            payload: static::get_payload_data($exception, ...$extra),
            request: $request,
            response: new Response(
                status: static::get_exception_status_code(),
                body: $exception->getMessage(),
                reason: explode("\n", $exception->getMessage())[0],
            ),
        );
    }

    /**
     * Get the schema for the response.
     *
     * @return schema_object
     */
    protected static function get_response_schema(): schema_object {
        return new schema_object(
            content: [
                'message' => new scalar_type(
                    type: param::ALPHANUMEXT,
                    description: 'The message of the exception.',
                ),
                'errorcode' => new scalar_type(
                    type: param::ALPHANUMEXT,
                    description: 'The error code of the exception.',
                ),
                'stacktrace' => new stacktrace(),
            ],
        );
    }

    /**
     * The status code that this exception should return.
     *
     * @return int
     */
    protected static function get_exception_status_code(): int {
        return 500;
    }

    /**
     * Get the description of this response.
     *
     * @return string
     */
    abstract protected static function get_response_description(): string;

    /**
     * Get the response payload data.
     *
     * @param \Exception $exception
     * @param mixed ...$extra
     * @return array
     */
    protected static function get_payload_data(
        \Exception $exception,
        ...$extra,
    ): array {
        $data = [
            'message' => $exception->getMessage(),
            'stacktrace' => array_map(
                fn ($frame): array => array_filter($frame, fn ($key) => $key !== 'args', ARRAY_FILTER_USE_KEY),
                $exception->getTrace(),
            ),
        ];

        if (is_a($exception, \moodle_exception::class)) {
            $data['errorcode'] = $exception->errorcode;
        }

        return $data;
    }
}
