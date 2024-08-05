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

namespace core\router\schema;

use core\router\schema\response\content\media_type;
use core\router\schema\response\content\payload_response_type;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Routing request body for validation.
 *
 * https://spec.openapis.org/oas/v3.1.0#request-body-object
 *
 * @package    core
 * @copyright  2023 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class request_body extends openapi_base {
    /**
     * Create a new request body.
     *
     * @param string $description A brief description of the request body.
     * @param payload_response_type|payload_response_type[] $content The content of the request body.
     * @param bool $required Whether the request body is required
     * @param mixed ...$args Extra args for future compatibility.
     * @throws \coding_exception if the content is not an instance of media_type.
     */
    public function __construct(
        /**
         * A brief description of the request body.
         *
         * This could contain examples of use. CommonMark syntax MAY be used for rich text representation.
         * @var string
         */
        protected string $description = '',

        /**
         * The content of the request body.
         *
         * @var payload_response_type|media_type[]
         */
        protected array|payload_response_type $content = [],

        /** @var bool Whether the request body is required */
        protected bool $required = false,
        ...$args,
    ) {
        if (!empty($content)) {
            if (is_array($content)) {
                foreach ($content as $contentitem) {
                    if (!($contentitem instanceof media_type)) {
                        throw new \coding_exception('Content must be an instance of media_type.');
                    }
                }
            }
        }
        parent::__construct(...$args);
    }

    #[\Override]
    public function get_openapi_description(
        specification $api,
        ?string $path = null,
    ): ?\stdClass {
        $data = (object) [
            'description' => $this->description,
            'required' => $this->required,
            'content' => [],
        ];

        if ($this->content instanceof response\content\payload_response_type) {
            $data->content = $this->content->get_openapi_schema(
                api: $api,
            );
            return $data;
        }

        foreach ($this->content as $content) {
            $data->content[$content->get_encoding()] = $content->get_openapi_schema(
                api: $api,
            );
        }

        return $data;
    }

    /**
     * Get the relevant body for the specified request.
     *
     * Request bodies can be different for different content-types, as noted in the request.
     *
     * @param ServerRequestInterface $request
     * @return media_type
     * @throws \invalid_parameter_exception
     */
    public function get_body_for_request(
        ServerRequestInterface $request,
    ): media_type {
        if ($this->content instanceof payload_response_type) {
            $content = $this->content->get_media_type_instance(
                mimetype: $request->getHeaderLine('Content-Type'),
                required: $this->is_required(),
            );

            if ($content) {
                return $content;
            }
        } else {
            foreach ($this->content as $content) {
                if ($content::get_encoding() === $request->getHeaderLine('Content-Type')) {
                    return $content;
                }
            }
        }

        throw new \invalid_parameter_exception('No matching content type found.');
    }

    /**
     * Whether this query parameter is required.
     *
     * @return bool
     */
    public function is_required(): bool {
        return $this->required;
    }
}
