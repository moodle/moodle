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
 * Statement agent (user) object for xAPI structure checking and usage.
 *
 * @package    core_xapi
 * @copyright  2020 Ferran Recio
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_xapi\local\statement;

use core_xapi\xapi_exception;
use core_user;
use stdClass;

defined('MOODLE_INTERNAL') || die();

/**
 * Agent xAPI statement element representing a Moodle user.
 *
 * Agents can be used either as actor or object in a statement.
 *
 * @copyright  2020 Ferran Recio
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class item_agent extends item_actor {

    /** @var stdClass The user record of this actor. */
    protected $user;

    /**
     * Function to create an agent (user) from part of the xAPI statement.
     *
     * @param stdClass $data the original xAPI element
     * @param stdClass $user user record
     */
    protected function __construct(stdClass $data, stdClass $user) {
        parent::__construct($data);
        $this->user = $user;
    }

    /**
     * Function to create an item from part of the xAPI statement.
     *
     * @param stdClass $data the original xAPI element
     * @return item item_agentxAPI generated
     */
    public static function create_from_data(stdClass $data): item {
        global $CFG;
        if (!isset($data->objectType)) {
            throw new xapi_exception('Missing agent objectType');
        }
        if ($data->objectType != 'Agent') {
            throw new xapi_exception("Agent objectType must be 'Agent'");
        }
        if (isset($data->account) && isset($data->mbox)) {
            throw new xapi_exception("Agent cannot have more than one identifier");
        }
        $user = null;
        if (!empty($data->account)) {
            if ($data->account->homePage != $CFG->wwwroot) {
                throw new xapi_exception("Invalid agent homePage '{$data->account->homePage}'");
            }
            if (!is_numeric($data->account->name)) {
                throw new xapi_exception("Agent account name must be integer '{$data->account->name}' found");
            }
            $user = core_user::get_user($data->account->name);
            if (empty($user)) {
                throw new xapi_exception("Inexistent agent '{$data->account->name}'");
            }
        }
        if (!empty($data->mbox)) {
            $mbox = str_replace('mailto:', '', $data->mbox);
            $user = core_user::get_user_by_email($mbox);
            if (empty($user)) {
                throw new xapi_exception("Inexistent agent '{$data->mbox}'");
            }
        }
        if (empty($user)) {
            throw new xapi_exception("Unsupported agent definition");
        }
        return new self($data, $user);
    }

    /**
     * Create a item_agent from a existing user.
     *
     * @param stdClass $user A user record.
     * @return item_agent
     */
    public static function create_from_user(stdClass $user): item_agent {
        global $CFG;

        if (!isset($user->id)) {
            throw new xapi_exception("Missing user id");
        }
        $data = (object) [
            'objectType' => 'Agent',
            'account' => (object) [
                'homePage' => $CFG->wwwroot,
                'name' => $user->id,
            ],
        ];
        return new self($data, $user);
    }

    /**
     * Returns the moodle user represented by this item.
     *
     * @return stdClass user record
     */
    public function get_user(): stdClass {
        return $this->user;
    }

    /**
     * Return all users represented by this item.
     *
     * In this case the item is an agent so a single element array
     * will be returned always.
     *
     * @return array list of users
     */
    public function get_all_users(): array {
        return [$this->user->id => $this->user];
    }
}
