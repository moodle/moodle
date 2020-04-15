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

/**
 * Statement definition object for xAPI structure checking and usage.
 *
 * @package    core_xapi
 * @copyright  2020 Ferran Recio
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_xapi\local\statement;

use core_xapi\xapi_exception;
use core_xapi\iri;
use stdClass;

defined('MOODLE_INTERNAL') || die();

/**
 * Validation and usage of xAPI definition.
 *
 * Definition contains extra information about user interaction with
 * questions and other activities inside a xAPI statement. For now
 * it performs a basic validation on the provided data.
 *
 * @copyright  2020 Ferran Recio
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class item_definition extends item {

    /** @var string The statement. */
    protected $interactiontype;

    /**
     * Function to create a definition from part of the xAPI statement.
     *
     * @param stdClass $data the original xAPI element.
     */
    protected function __construct(stdClass $data) {
        parent::__construct($data);
        $this->interactiontype = $data->interactionType ?? null;
    }

    /**
     * Function to create an item from part of the xAPI statement.
     *
     * @param stdClass $data the original xAPI element
     * @return item item_definition xAPI generated
     */
    public static function create_from_data(stdClass $data): item {
        // Interaction Type is a optopnal param.
        if (!empty($data->interactionType)) {
            $posiblevalues = [
                'choice' => true,
                'fill-in' => true,
                'long-fill-in' => true,
                'true-false' => true,
                'matching' => true,
                'performance' => true,
                'sequencing' => true,
                'likert' => true,
                'numeric' => true,
                'other' => true,
                'compound' => true,
            ];
            if (!isset($posiblevalues[$data->interactionType])) {
                throw new xapi_exception("Invalid definition \"{$data->interactionType}\"");
            }
        }
        return new self($data);
    }

    /**
     * Return the definition interaction type.
     */
    public function get_interactiontype(): ?string {
        return $this->interactiontype;
    }
}
