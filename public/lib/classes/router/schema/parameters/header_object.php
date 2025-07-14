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

namespace core\router\schema\parameters;

use core\exception\invalid_parameter_exception;
use core\param;
use core\router\schema\parameter;
use Psr\Http\Message\ServerRequestInterface;

/**
 * A Header Object.
 *
 * https://spec.openapis.org/oas/v3.1.0#headerObject
 *
 * The Header Object follows the structure of the Parameter Object with the following changes:
 *
 * - name MUST NOT be specified, it is given in the corresponding headers map.
 * - in MUST NOT be specified, it is implicitly in header.
 * - All traits that are affected by the location MUST be applicable to a location of header (for example, style).
 *
 * @package    core
 * @copyright  2023 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class header_object extends parameter {
    /**
     * Create a new header object.
     *
     * @param bool $multiple Whether this parameter can be specified multiple times.
     * @param mixed ...$extra Header arguments to pass to the parameter constructor.
     */
    public function __construct(
        /** @var bool Whether multiple instances of this header are supported */
        protected bool $multiple = false,
        ...$extra,
    ) {
        $extra['in'] = parameter::IN_HEADER;
        parent::__construct(...$extra);
    }

    /**
     * Validate the parameter.
     *
     * @param ServerRequestInterface $request The request to validate.
     * @return ServerRequestInterface The request with the validated parameter.
     * @throws invalid_parameter_exception If the parameter is invalid.
     */
    public function validate(
        ServerRequestInterface $request,
    ): ServerRequestInterface {
        if ($request->hasHeader($this->name)) {
            $headervalues = $request->getHeader($this->name);

            if (!$this->multiple && count($headervalues) > 1) {
                throw new invalid_parameter_exception(
                    "The parameter {$this->name} was specified multiple times, but it can only be specified once",
                );
            }

            // This parameter was specified. Validate it.
            if ($this->get_type() === param::BOOL) {
                $headervalues = array_map(fn ($headervalue) => match ($headervalue) {
                    'true' => 1,
                    'false' => 0,
                    default => throw new \ValueError('Invalid boolean value.'),
                }, $headervalues);
                return $request->withHeader($this->name, $headervalues);
            }

            foreach ($headervalues as $headervalue) {
                $this->type->validate_param($headervalue);
            }

            return $request;
        }

        if ($this->required) {
            throw new invalid_parameter_exception(
                "A required parameter {$this->name} was not provided and must be specified",
            );
        }

        if ($this->default !== null) {
            // This parameter is optional. Fill the default.
            return $request->withHeader($this->name, $this->default);
        }

        // This parameter is optional and there is no default.
        // Fill a null value.
        return $request->withHeader($this->name, null);
    }
}
