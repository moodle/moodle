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
namespace mod_bigbluebuttonbn\local\extension;

use cm_info;

/**
 * A class to deal with completion rules addons in a subplugin
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2023 onwards, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Laurent David (laurent@call-learning.fr)
 */
abstract class custom_completion_addons {
    /** @var cm_info The course module information object. */
    protected $cm;

    /** @var int The user's ID. */
    protected $userid;

    /** @var array The current state of core completion */
    protected $completionstate;

    /**
     * activity_custom_completion constructor.
     *
     * @param cm_info $cm
     * @param int $userid
     * @param array|null $completionstate The current state of the core completion criteria
     */
    public function __construct(cm_info $cm, int $userid, ?array $completionstate = null) {
        $this->cm = $cm;
        $this->userid = $userid;
        $this->completionstate = $completionstate;
    }

    /**
     * Fetches the completion state for a given completion rule.
     *
     * @param string $rule The completion rule.
     * @return int The completion state.
     */
    abstract public function get_state(string $rule): int;

    /**
     * Fetch the list of custom completion rules that this module defines.
     *
     * @return array
     */
    abstract public static function get_defined_custom_rules(): array;

    /**
     * Returns an associative array of the descriptions of custom completion rules.
     *
     * @return array
     */
    abstract public function get_custom_rule_descriptions(): array;

    /**
     * Returns an array of all completion rules, in the order they should be displayed to users.
     *
     * @return array
     */
    abstract public function get_sort_order(): array;
}
