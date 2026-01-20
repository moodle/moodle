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

namespace core\router\parameters;

use core\exception\not_found_exception;
use core\param;
use core\router\schema\example;
use core\router\schema\parameters\mapped_property_parameter;
use core\router\schema\referenced_object;
use Psr\Http\Message\ServerRequestInterface;

/**
 * A Moodle parameter referenced in the path.
 *
 * @package    core
 * @copyright  2026 Amaia Anabitarte <amaia@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class path_module extends \core\router\schema\parameters\path_parameter implements
    mapped_property_parameter,
    referenced_object
{
    /**
     * Create a new path_module parameter.
     *
     * @param string $name The name of the parameter to use for the module identifier
     * @param mixed ...$extra Additional arguments
     */
    public function __construct(
        string $name = 'cm',
        ...$extra,
    ) {
        $extra['name'] = $name;
        $extra['type'] = param::RAW;
        $extra['description'] = <<<EOF
        The module identifier.

        This can be the id of the module.
        EOF;
        $extra['examples'] = [
            new example(
                name: 'A module id',
                value: 54,
            ),
        ];

        parent::__construct(...$extra);
    }

    #[\Override]
    public function add_attributes_for_parameter_value(
        ServerRequestInterface $request,
        string $value,
    ): ServerRequestInterface {
        if (!$cm = get_coursemodule_from_id('', $value)) {
            throw new not_found_exception('course_module', $value);
        }

        return $request
            ->withAttribute($this->name, $cm)
            ->withAttribute("{$this->name}context", \core\context\module::instance($cm->id));
    }
}
