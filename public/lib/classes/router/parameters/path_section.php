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
 * A parameter representing a section in the path.
 *
 * @package    core
 * @copyright  2026 Amaia Anabitarte <amaia@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class path_section extends \core\router\schema\parameters\path_parameter implements
    mapped_property_parameter,
    referenced_object
{
    /**
     * Create a new path_section parameter.
     *
     * @param string $name The name of the parameter to use for the section identifier
     * @param mixed ...$extra Additional arguments
     */
    public function __construct(
        string $name = 'section',
        ...$extra,
    ) {
        $extra['name'] = $name;
        $extra['type'] = param::RAW;
        $extra['description'] = <<<EOF
        The section identifier.

        This can be the id of the section.
        EOF;
        $extra['examples'] = [
            new example(
                name: 'A section id',
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
        $section = $this->get_section_for_value($value);
        return $request
            ->withAttribute($this->name, $section)
            ->withAttribute("coursecontext", \context_course::instance($section->course));
    }

    /**
     * Get the section object for the given identifier.
     *
     * @param string $value A section id
     * @return object
     * @throws not_found_exception If the section cannot be found
     */
    protected function get_section_for_value(string $value): mixed {
        global $DB;

        $data = false;

        if (is_numeric($value)) {
            $data = $DB->get_record('course_sections', [
                'id' => $value,
            ]);
        }
        if ($data) {
            return $data;
        }

        throw new not_found_exception('course_sections', $value);
    }
}
