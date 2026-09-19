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

use core\exception\moodle_exception;
use core\param;
use core\router\schema\objects\scalar_type;
use core\router\schema\objects\schema_object;
use core\router\schema\objects\stacktrace;
use core\router\schema\referenced_object;
use core\router\schema\response\content\problem_payload_response_type;
use core\router\schema\response\exception_payload_response;
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
            content: new problem_payload_response_type(
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
        return new exception_payload_response(
            payload: static::get_response_payload($exception, $request),
            request: $request,
            response: static::add_additional_headers(
                new Response(
                    status: static::get_exception_status_code(),
                    body: $exception->getMessage(),
                    reason: explode("\n", $exception->getMessage())[0],
                ),
                $exception,
            ),
        );
    }

    /**
     * Get the schema for the response.
     *
     * @return schema_object
     */
    protected static function get_response_schema(): schema_object {
        // This schema, and these descriptions, are defined in RFC-9457.
        // See https://www.rfc-editor.org/info/rfc9457/ for further information.
        return new \core\router\schema\objects\problem_response_schema();
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
     * This should not normally be overridden.
     *
     * @return string
     */
    protected static function get_response_description(): string {
        return \core\router\util::get_status_description(static::get_exception_status_code());
    }

    /**
     * Return a URL that identifies the problem type.
     *
     * See {@link https://www.rfc-editor.org/info/rfc9457/#name-type} for further information on valid values.
     *
     * @return null|\core\url
     */
    protected static function get_response_type(): ?\core\url {
        return null;
    }

    /**
     * Get the response payload data.
     *
     * @param \Exception $exception
     * @param ServerRequestInterface $request
     * @return array
     * @since Moodle 6.0
     */
    protected static function get_response_payload(
        \Exception $exception,
        ServerRequestInterface $request,
    ): array {
        // Data for the API Token Exception should meet RFC 9457.
        $data = [
            'status' => static::get_exception_status_code(),
            'title' => static::get_response_description(),
            'detail' => $exception->getMessage(),
            'type' => 'about:blank',
        ];

        $type = static::get_response_type();
        if ($type !== null) {
            $data['type'] = $type->out(false);
        } else {
            // If no type is specified, the RFC requires a value of 'about:blank'.
            $data['type'] = 'about:blank';
        }

        $data['instance'] = $request->getUri()->getPath();

        if (debugging(level: DEBUG_DEVELOPER)) {
            // Append the debuginfo to the detail if available and not already included.
            if (
                $exception instanceof moodle_exception
                && $exception->debuginfo !== null
                && !str_contains($data['detail'], $exception->debuginfo)
            ) {
                $data['detail'] = rtrim($data['detail'], '.') . '. ' . $exception->debuginfo;
            }

            // Add the stacktrace.
            // Do not use $CFG->dirroot because it might not be available in destructors.
            $dirroot = realpath(dirname(__DIR__, 5));
            $data['stacktrace'] = array_map(
                fn($frame): array => array_filter($frame, fn($key) => $key !== 'args', ARRAY_FILTER_USE_KEY),
                array_map(
                    fn($trace): array => array_key_exists('file', $trace)
                        ? array_merge($trace, ['file' => str_replace($dirroot, '', realpath($trace['file']))])
                        : $trace,
                    $exception->getTrace(),
                ),
            );
        }

        // The following are deprecated and will be removed soon.
        $data['message'] = $data['detail'];
        if ($exception instanceof moodle_exception) {
            $data['errorcode'] = $exception->errorcode;
        }

        return $data;
    }

    /**
     * Get the response payload data.
     *
     * @param \Exception $exception
     * @param mixed ...$extra
     * @return array
     * @deprecated Since 6.0
     */
    #[\core\attribute\deprecated('get_response_payload', '6.0', 'Use get_response_payload instead.', 'MDL-89850')]
    protected static function get_payload_data(
        \Exception $exception,
        ...$extra,
    ): array {
        // Data for the API Token Exception should meet RFC 9457.
        $data = [
            'message' => $exception->getMessage(),
            'stacktrace' => [],
        ];

        if (is_a($exception, \moodle_exception::class)) {
            $data['errorcode'] = $exception->errorcode;
        }

        if (debugging(level: DEBUG_DEVELOPER)) {
            // Do not use $CFG->dirroot because it might not be available in destructors.
            $dirroot = realpath(dirname(__DIR__, 5));
            $data['stacktrace'] = array_map(
                fn ($frame): array => array_filter($frame, fn ($key) => $key !== 'args', ARRAY_FILTER_USE_KEY),
                array_map(
                    fn ($trace): array => array_key_exists('file', $trace)
                        ? array_merge($trace, ['file' => str_replace($dirroot, '', realpath($trace['file']))])
                        : $trace,
                    $exception->getTrace(),
                ),
            );
        }

        return $data;
    }

    /**
     * Add additional headers to the response.
     *
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param \Exception $exception
     * @return \Psr\Http\Message\ResponseInterface
     */
    protected static function add_additional_headers(
        \Psr\Http\Message\ResponseInterface $response,
        \Exception $exception,
    ): \Psr\Http\Message\ResponseInterface {
        foreach (static::get_additional_headers($exception) as $name => $value) {
            $response = $response->withAddedHeader($name, $value);
        }

        return $response;
    }

    /**
     * Get any headers to add to the response.
     *
     * @param \Exception $exception The exception for which to get additional headers.
     * @return array<string,string>
     */
    protected static function get_additional_headers(\Exception $exception): array {
        return [];
    }
}
