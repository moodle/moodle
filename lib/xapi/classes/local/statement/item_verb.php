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
 * Statement verb object for xAPI structure checking and usage.
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
 * Verb xAPI statement item.
 *
 * Verbs represent the interaction a user/group made inside a xAPI
 * compatible plugin. Internally a xAPI verb must be representad as
 * in a valid IRI format (which is a less restrictive version of a
 * regular URL so a moodle_url out is completelly fine). To make it
 * easy for plugins to generate valid IRI, a simple string van be
 * provided and the class will convert into a valid IRI internally.
 *
 * @copyright  2020 Ferran Recio
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class item_verb extends item {

    /** @var string The statement. */
    protected $id;

    /**
     * An xAPI verb constructor based on xAPI data structure.
     *
     * @param stdClass $data from the specific xAPI element
     */
    protected function __construct(stdClass $data) {
        parent::__construct($data);
        $this->id = iri::extract($data->id, 'verb');
    }

    /**
     * Function to create an item from part of the xAPI statement.
     *
     * @param stdClass $data the original xAPI element
     * @return item item_verb xAPI generated
     */
    public static function create_from_data(stdClass $data): item {
        if (empty($data->id)) {
            throw new xapi_exception("missing verb id");
        }
        if (!iri::check($data->id)) {
            throw new xapi_exception("verb id $data->id is not a valid IRI");
        }
        return new self($data);
    }

    /**
     * Create a valid item_verb from a simple verb string.
     *
     * @param string $id string to convert to a valid IRI (or a valid IRI)
     * @return item_verb the resulting item_verb
     */
    public static function create_from_id(string $id): item_verb {

        $data = new stdClass();
        $data->id = iri::generate($id, 'verb');

        return new self($data);
    }

    /**
     * Return the id used in this item.
     *
     * Id will be extracted from the provided IRI. If it's a valid IRI
     * it will return all IRI value but if it is generate by the iri helper
     * from this library it will extract the original value.
     *
     * @return string the ID (extracted from IRI value)
     */
    public function get_id(): string {
        return $this->id;
    }
}
