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
 * Type.
 *
 * @package    block_xp
 * @copyright  2024 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\ruletype;

use block_xp\local\action\action;
use block_xp\local\reason\reason;
use lang_string;

/**
 * Type.
 *
 * @package    block_xp
 * @copyright  2024 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface ruletype {

    /** Unlimited. */
    const WINDOW_NONE = null;
    /** Once only. */
    const WINDOW_ONCE = 'once';
    /** Daily repeat. */
    const WINDOW_DAILY = '1d';
    /** Hourly repeat. */
    const WINDOW_HOURLY = '1h';

    /**
     * Get the list of compatible rule filters.
     *
     * @return string[] The name of the filters.
     */
    public function get_compatible_filters(): array;

    /**
     * Get the display name.
     *
     * @return lang_string
     */
    public function get_display_name(): lang_string;

    /**
     * Get the repeat window.
     *
     * @return null|string One of the WINDOW_* const, or other arbitrary string.
     */
    public function get_repeat_window(): ?string;

    /**
     * Get the short description.
     *
     * @return lang_string
     */
    public function get_short_description(): lang_string;

    /**
     * Whether an action is compatible.
     *
     * @param action $action The action.
     * @return bool
     */
    public function is_action_compatible(action $action): bool;

    /**
     * Whether the action is satisfying the requirements.
     *
     * @param action $action The action.
     * @return bool
     */
    public function is_action_satisfying_requirements(action $action): bool;

    /**
     * Make a reason.
     *
     * @param action $action The action.
     * @return reason
     */
    public function make_reason(action $action): reason;

}
