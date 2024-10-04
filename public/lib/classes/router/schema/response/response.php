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

namespace core\router\schema\response;

use core\exception\coding_exception;
use core\router\schema\openapi_base;
use core\router\schema\response\content\media_type;
use core\router\schema\specification;
use core\router\schema\response\content\payload_response_type;
use Psr\Http\Message\ResponseInterface;

/**
 * An OpenAPI Response.
 *
 * https://spec.openapis.org/oas/v3.1.0#response-object
 *
 * @package    core
 * @copyright  2023 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class response extends openapi_base {
    /**
     * Create a new described response.
     *
     * @param int $statuscode The status code for this response
     * @param string $description A description of this response
     * @param array $headers The headers associated with this response
     * @param array|payload_response_type $content The content of this response
     * @param mixed ...$extra Any extra data to store
     * @throws coding_exception
     */
    public function __construct(
        /** @var int The status code for this response */
        public readonly int $statuscode = 200,
        /** @var string A description of this response */
        public readonly string $description = '',
        /** @var array The headers associated with this response */
        private readonly array $headers = [],
        /** @var array|payload_response_type The content of this response */
        public readonly array|payload_response_type $content = [],
        ...$extra,
    ) {
        if (is_array($content)) {
            foreach ($content as $contentitem) {
                if (!$contentitem instanceof media_type) {
                    throw new coding_exception('Content must be an array of payload response types');
                }
            }
        }

        parent::__construct(...$extra);
    }

    /**
     * Validate the response.
     *
     * @param ResponseInterface $response The response to validate
     */
    public function validate(
        ResponseInterface $response,
    ): void {
        $response;
    }

    /**
     * Get the description for this response.
     *
     * @return string
     */
    protected function get_description(): string {
        if ($this->description !== '') {
            return $this->description;
        }

        return match ($this->statuscode) {
            200 => 'OK',
            default => '',
        };
    }

    #[\Override]
    public function get_openapi_description(
        specification $api,
        ?string $path = null,
    ): ?\stdClass {
        $data = (object) [
            'description' => $this->get_description(),
        ];

        if (count($this->headers)) {
            foreach ($this->headers as $header) {
                $data->headers[$header->get_name()] = $header->get_openapi_schema(
                    api: $api,
                );
            }
        }

        if ($this->content instanceof content\payload_response_type) {
            $data->content = $this->content->get_openapi_schema(
                api: $api,
            );
        } else if (count($this->content)) {
            foreach ($this->content as $body) {
                $data->content[$body->get_mimetype()] = $body->get_openapi_schema(
                    api: $api,
                );
            }
        }

        return $data;
    }

    /**
     * Get the status code for this response.
     *
     * @return int
     */
    public function get_status_code(): int {
        return $this->statuscode;
    }
}
