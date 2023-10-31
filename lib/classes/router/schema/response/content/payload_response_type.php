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

namespace core\router\schema\response\content;

use core\router\schema\openapi_base;
use core\router\schema\specification;

/**
 * A standard Moodle response for all supported payload types.
 *
 * @package    core
 * @copyright  2023 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class payload_response_type extends openapi_base {
    /** @var array Arguments to pass the media instantiator */
    protected array $args;

    /**
     * Crate a new payload response type.
     *
     * @param bool $required Whether this query parameter is required.
     * @param array ...$args Extra args for future compatibility.
     */
    public function __construct(
        /** @var bool Whether a payload response is required */
        protected bool $required = false,
        ...$args,
    ) {
        parent::__construct();
        $this->args = $args;
    }

    /**
     * Get the supported content types.
     *
     * @return \class-string<media_type>[]
     */
    public function get_supported_content_types(): array {
        return [
            json_media_type::class,
        ];
    }

    /**
     * Get a media type instance for the given mimetype.
     *
     * @param string|null $mimetype   The mimetype to get the instance for.
     * @param string|null $classname  The classname to get the instance for.
     * @param bool $required   Whether the media type is required.
     * @return media_type|null
     */
    public function get_media_type_instance(
        ?string $mimetype = null,
        ?string $classname = null,
        bool $required = false,
    ): ?media_type {
        if ($classname) {
            return new $classname(...$this->args);
        }

        foreach ($this->get_supported_content_types() as $contenttypeclass) {
            if (empty($mimetype) || $contenttypeclass::get_encoding() === $mimetype) {
                $args = $this->args;
                $args['required'] = $required;
                return new $contenttypeclass(...$args);
            }
        }

        return null; // @codeCoverageIgnore
    }

    #[\Override]
    public function get_openapi_description(
        specification $api,
        ?string $path = null,
    ): ?\stdClass {
        $content = (object) [];

        foreach ($this->get_supported_content_types() as $contenttypeclass) {
            $contenttype = new $contenttypeclass(...$this->args);
            $content->{$contenttype->get_mimetype()} = $contenttype->get_openapi_schema(
                api: $api,
            );
        }

        return $content;
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
