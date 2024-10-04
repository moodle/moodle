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
 * Statement object (activity, user or group) for xAPI structure checking and usage.
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
 * Abstract object item used in xAPI statements.
 *
 * Object represents the object in which a xAPI verb is applied. There
 * are 3 types of objects supported: agent (user), group (of users) and
 * activity (defined by every plugin).
 *
 * @copyright  2020 Ferran Recio
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class item_object extends item {

    /**
     * Create a xAPI object compatible from data (Agent, Group or Activity).
     *
     * @param stdClass $data data structure from statement object
     * @return item item_group|item_agent|item_activity resulting object
     */
    public static function create_from_data(stdClass $data): item {
        if (!isset($data->objectType)) {
            $data->objectType = 'Activity';
        }
        switch ($data->objectType) {
            case 'Agent':
                return item_agent::create_from_data($data);
                break;
            case 'Group':
                return item_group::create_from_data($data);
                break;
            case 'Activity':
                return item_activity::create_from_data($data);
                break;
            default:
                throw new xapi_exception("Unknown Object type '{$data->objectType}'");
        }
    }
}
