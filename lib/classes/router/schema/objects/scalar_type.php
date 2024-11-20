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

namespace core\router\schema\objects;

use core\param;
use core\router\schema\specification;

/**
 * A scalar type.
 *
 * @package    core
 * @copyright  2023 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class scalar_type extends type_base {
    /**
     * Instantiate a new Scalar Type
     *
     * @param param $type The Moodle PARAM_ type
     * @param bool $required Whether the value is required or not
     * @param mixed $default The value used if none was supplied (request bodies only)
     * @param mixed[] ...$extra
     */
    public function __construct(
        /** @var param The type of the parameter content */
        protected param $type,
        /** @var bool Whether the value is required or not */
        protected bool $required = false,
        /** @var mixed $default The value used if none was supplied (request bodies only) */
        protected mixed $default = null,
        ...$extra,
    ) {
        parent::__construct(...$extra);
    }

    #[\Override]
    public function get_openapi_description(
        specification $api,
        ?string $path = null,
    ): ?\stdClass {
        return $this->get_schema_from_type($this->type);
    }

    #[\Override]
    public function validate_data(mixed $data) {
        return $this->type->validate_param(
            param: $data,
            allownull: $this->required ? NULL_NOT_ALLOWED : NULL_ALLOWED,
        );
    }
}
