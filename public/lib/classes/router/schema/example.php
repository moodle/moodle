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

use core\exception\coding_exception;

/**
 * A Response Example Object.
 *
 * https://spec.openapis.org/oas/v3.1.0#example-object
 *
 * @package    core
 * @copyright  2023 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class example extends openapi_base {
    /**
     * Create a new example.
     *
     * @param string $name The name of the example.
     * @param string|null $summary A summary of the example.
     * @param string|null $description A long description fo the example. CommonMark syntax may be used.
     * @param mixed $value Embedded literal example.
     * @param string|null $externalvalue A URI that points to the literal example.
     * @param mixed ...$extra
     * @throws coding_exception if both the value and externalvalue are null
     */
    public function __construct(
        /** @var string The name of the example */
        protected string $name,
        /** @var string|null A summary of the example */
        protected ?string $summary = null,
        /** @var string|null A long description fo the example. CommonMark syntax may be used */
        protected ?string $description = null,
        /**
         * Embedded literal example.
         *
         * The value field and externalValue field are mutually exclusive.
         * To represent examples of media types that cannot naturally represented in JSON or YAML,
         * use a string value to contain the example, escaping where necessary.
         *
         * @var mixed
         */
        protected mixed $value = null,
        /**
         * A URI that points to the literal example.
         *
         * This provides the capability to reference examples that cannot easily be included in JSON or YAML documents.
         * The value field and externalValue field are mutually exclusive. See the rules for resolving Relative References.
         *
         * @var string|null
         */
        protected ?string $externalvalue = null,
        ...$extra,
    ) {
        if (!($value === null || $externalvalue === null)) {
            throw new coding_exception('Only one of value or externalvalue can be specified.');
        }

        parent::__construct(...$extra);
    }

    /**
     * Get the name of this example.
     *
     * @return string
     */
    public function get_name(): string {
        return $this->name;
    }

    #[\Override]
    public function get_openapi_description(
        specification $api,
        ?string $path = null,
    ): ?\stdClass {
        $data = (object) [];

        if ($this->summary !== null) {
            $data->summary = $this->summary;
        }

        if ($this->description !== null) {
            $data->description = $this->description;
        }

        if ($this->value !== null) {
            $data->value = $this->value;
        } else if ($this->externalvalue !== null) {
            $data->externalValue = $this->externalvalue;
        }

        return $data;
    }
}
