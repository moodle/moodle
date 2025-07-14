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
use core\router\schema\referenced_object;

/**
 * A schema to describe an array of strings.
 *
 * TODO: This should really take a param:: type for validation of both name and value.
 *
 * @package    core
 * @copyright  2023 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class array_of_strings extends array_of_things implements referenced_object {
    /**
     * Create a new array_of_strings schema.
     *
     * @param param $keyparamtype The type of the key parameter
     * @param param $valueparamtype The type of the value parameter
     * @param mixed ...$extra Additional arguments
     */
    public function __construct(
        /** @var param The type param type for the key */
        protected param $keyparamtype = param::RAW,
        /** @var param The type param type for the value */
        protected param $valueparamtype = param::RAW,
        ...$extra,
    ) {
        $extra['thingtype'] = 'string';
        parent::__construct(...$extra);
    }

    #[\Override]
    public function validate_data(mixed $data) {
        foreach ($data as $name => $value) {
            $this->keyparamtype->validate_param(
                param: $name,
                debuginfo: $this->get_debug_info_for_validation_failure($this->keyparamtype, $name),
            );
            $this->valueparamtype->validate_param(
                param: $value,
                debuginfo: $this->get_debug_info_for_validation_failure($this->valueparamtype, $value),
            );
        }
        return $data;
    }

    /**
     * Get the debug info for a validation failure.
     *
     * @param param $type
     * @param string $value
     * @return string
     */
    protected function get_debug_info_for_validation_failure(
        param $type,
        string $value,
    ): string {
        return  "The value '{$value}' was not of type {$type->value}.";
    }
}
