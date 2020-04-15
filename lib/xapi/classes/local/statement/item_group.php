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
 * Statement group object for xAPI structure checking and usage.
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
 * Group item inside a xAPI statement.
 *
 * Only named groups are accepted (all groups must be real groups in the
 * platform) so anonymous groups will be rejected on creation. Groups can
 * be used as actor or as object inside a xAPI statement.
 *
 * @copyright  2020 Ferran Recio
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class item_group extends item_actor {

    /** @var timestamp The statement timestamp. */
    protected $users;

    /** @var timestamp The statement timestamp. */
    protected $group;

    /**
     * Function to create an group from part of the xAPI statement.
     *
     * @param stdClass $data the original xAPI element
     * @param stdClass $group group record
     */
    protected function __construct(stdClass $data, stdClass $group) {
        parent::__construct($data);
        $this->group = $group;
        $this->users = groups_get_members($group->id);
        if (!$this->users) {
            $this->users = [];
        }
    }

    /**
     * Function to create an item from part of the xAPI statement.
     *
     * @param stdClass $data the original xAPI element
     * @return item item_group xAPI item generated
     */
    public static function create_from_data(stdClass $data): item {
        global $CFG;
        if (!isset($data->objectType)) {
            throw new xapi_exception('Missing group objectType');
        }
        if ($data->objectType != 'Group') {
            throw new xapi_exception("Group objectType must be 'Group'");
        }
        if (!isset($data->account)) {
            throw new xapi_exception("Missing Group account");
        }
        if ($data->account->homePage != $CFG->wwwroot) {
            throw new xapi_exception("Invalid group homePage '{$data->account->homePage}'");
        }
        if (!is_numeric($data->account->name)) {
            throw new xapi_exception("Agent account name must be integer '{$data->account->name}' found");
        }
        $group = groups_get_group($data->account->name);
        if (empty($group)) {
            throw new xapi_exception("Inexistent group '{$data->account->name}'");
        }
        return new self($data, $group);
    }

    /**
     * Create a item_group from a existing group.
     *
     * @param stdClass $group A group record.
     * @return item_group
     */
    public static function create_from_group(stdClass $group): item_group {
        global $CFG;

        if (!isset($group->id)) {
            throw new xapi_exception("Missing group id");
        }
        $data = (object) [
            'objectType' => 'Group',
            'account' => (object) [
                'homePage' => $CFG->wwwroot,
                'name' => $group->id,
            ],
        ];
        return new self($data, $group);
    }

    /**
     * Returns the moodle user represented by this item.
     *
     * This is a group item. To avoid security problems this method
     * thorws an exception when is called from a item_group class.
     *
     * @throws xapi_exception get_user must not be called from an item_group
     * @return stdClass user record
     */
    public function get_user(): stdClass {
        throw new xapi_exception("Group statements cannot be used as a individual user");
    }

    /**
     * Return all users from the group represented by this item.
     *
     * @return array group users
     */
    public function get_all_users(): array {
        return $this->users;
    }

    /**
     * Return the moodle group represented by this item.
     *
     * @return stdClass a group record
     */
    public function get_group(): stdClass {
        return $this->group;
    }
}
