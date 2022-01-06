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
 * Statement attachment for xAPI structure checking and usage.
 *
 * @package    core_xapi
 * @copyright  2020 Ferran Recio
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_xapi\local\statement;

use core_xapi\xapi_exception;
use core_xapi\iri;
use stdClass;

/**
 * Abstract xAPI attachment class.
 *
 * @copyright  2020 Ferran Recio
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class item_attachment extends item {

    /**
     * Function to create an item from part of the xAPI statement.
     *
     * @param stdClass $data the original xAPI element
     * @return item item_attachment xAPI generated
     */
    public static function create_from_data(stdClass $data): item {

        if (empty($data->usageType)) {
            throw new xapi_exception("missing attachment usageType");
        }
        if (!iri::check($data->usageType)) {
            throw new xapi_exception("attachment usageType $data->usageType is not a valid IRI");
        }
        if (empty($data->display)) {
            throw new xapi_exception("missing attachment display");
        }
        if (empty($data->contentType)) {
            throw new xapi_exception("missing attachment contentType");
        }
        if (empty($data->length)) {
            throw new xapi_exception("missing attachment length");
        }
        if (!is_numeric($data->length)) {
            throw new xapi_exception("invalid attachment length format");
        }
        if (empty($data->sha2)) {
            throw new xapi_exception("missing attachment sha2");
        }

        // More required property checks will appear here in the future.

        return new self($data);
    }
}
