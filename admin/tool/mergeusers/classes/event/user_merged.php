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
 * The user_merged event.
 *
 * The base class for merge user accounts related actions.
 *
 * @package tool_mergeusers
 * @subpackage mergeusers
 * @author Gerard Cuello Adell <gerard.urv@gmail.com>
 * @copyright 2016 Servei de Recursos Educatius (http://www.sre.urv.cat)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_mergeusers\event;

/**
 * The user_merged abstract event class.
 *
 * @property-read array $other {
 *      Extra information about event.
 *
 *      - array usersinvolved: associative array with:
 *              'toid'   => int userid,
 *              'fromid' => int userid.
 *      - int logid: the log id with the whole detail of the merge.
 * }
 *
 * @since Moodle 3.0.2+
 * @package   tool_mergeusers
 * @author    Gerard Cuello Adell <gerard.urv@gmail.com>
 * @copyright 2016 onwards to Universitat Rovira i Virgili (https://www.urv.cat)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class user_merged extends \core\event\base {
    /**
     * Initializes the base event for merged users.
     *
     * @return void
     */
    protected function init() {
        $this->data['crud'] = 'u'; // Usually we perform update db queries, so 'u' is ok!
        $this->data['level'] = self::LEVEL_OTHER; // Fixing backwards compatibility.
        $this->data['edulevel'] = self::LEVEL_OTHER;
    }

    /**
     * Human-readable detail of this event, given the result of the merge.
     *
     * @return string
     */
    protected function get_description_as(string $result) {
        return "The user {$this->userid} merged with {$result} all user-related data
            from '{$this->get_old_user_id()}' into '{$this->get_new_user_id()}', with its logs in id {$this->get_log_id()}.";
    }

    /**
     * Tells the log id from the tool_mergeusers table.
     *
     * @return int
     */
    public function get_log_id(): int {
        return $this->other['logid'] ?? 0;
    }

    /**
     * Informs the user.id from the user to keep.
     *
     * @return int
     */
    public function get_new_user_id(): int {
        return $this->other['usersinvolved']['toid'] ?? 0;
    }

    /**
     * Informs the user.id from the user to remove.
     * @return int
     */
    public function get_old_user_id(): int {
        return $this->other['usersinvolved']['fromid'] ?? 0;
    }
}
