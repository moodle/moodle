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
 * Statement actor (user or group) object for xAPI structure checking and usage.
 *
 * @package    core_xapi
 * @copyright  2020 Ferran Recio
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_xapi\local\statement;

use core_xapi\xapi_exception;
use stdClass;

defined('MOODLE_INTERNAL') || die();

/**
 * Abstract xAPI actor class.
 *
 * This class extends from item_object instead of basic item
 * because both actors (agent and group) could be used as
 * statement actor or object.
 *
 * @copyright  2020 Ferran Recio
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class item_actor extends item_object {

    /**
     * Function to create an actor from part of the xAPI statement.
     *
     * @param stdClass $data the original xAPI element
     * @return item item_agent|item_grou|item_activity xAPI generated
     */
    public static function create_from_data(stdClass $data): item {
        if (!isset($data->objectType)) {
            $data->objectType = 'Agent';
        }
        switch ($data->objectType) {
            case 'Agent':
                return item_agent::create_from_data($data);
                break;
            case 'Group':
                return item_group::create_from_data($data);
                break;
            default:
                throw new xapi_exception("Unknown Actor type '{$data->objectType}'");
        }
    }

    /**
     * Returns the moodle user represented by this item.
     *
     * @return stdClass user record
     */
    abstract public function get_user(): stdClass;

    /**
     * Return all moodle users represented by this item.
     *
     * @return array user records
     */
    abstract public function get_all_users(): array;
}
