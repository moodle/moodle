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
 * Statement base object for xAPI structure checking and usage.
 *
 * @package    core_xapi
 * @copyright  2020 Ferran Recio
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_xapi\local\statement;
use stdClass;
use JsonSerializable;
use core_xapi\iri;

defined('MOODLE_INTERNAL') || die();

/**
 * Item class used for xAPI statement elements without validation.
 *
 * @copyright  2020 Ferran Recio
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class item implements JsonSerializable {

    /** @var stdClass the item structure. */
    protected $data;

    /**
     * Item constructor.
     *
     * @param stdClass $data from the specific xAPI element
     */
    protected function __construct(stdClass $data) {
        $this->data = $data;
    }

    /**
     * Function to create an item from part of the xAPI statement.
     *
     * @param stdClass $data the original xAPI element
     * @return item the xAPI item generated
     */
    public static function create_from_data(stdClass $data): item {
        return new self($data);
    }

    /**
     * Return the data to serialize in case JSON statement is needed.
     *
     * @return stdClass the original data structure
     */
    public function jsonSerialize(): stdClass {
        return $this->get_data();
    }

    /**
     * Return the original data from this item.
     *
     * @return stdClass the original data structure
     */
    public function get_data(): stdClass {
        return $this->data;
    }
}
