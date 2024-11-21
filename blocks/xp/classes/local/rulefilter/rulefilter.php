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
 * Filter.
 *
 * @package    block_xp
 * @copyright  2024 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\rulefilter;

use context;
use lang_string;

/**
 * Filter.
 *
 * @package    block_xp
 * @copyright  2024 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface rulefilter {

    /**
     * Get action tester.
     *
     * @param context $effectivecontext The effective context.
     * @param object $config The config.
     * @return action_tester
     */
    public function get_action_tester(context $effectivecontext, object $config): action_tester;

    /**
     * Get compatible context levels.
     *
     * @return int[]
     */
    public function get_compatible_context_levels(): array;

    /**
     * Get display name.
     *
     * @return lang_string
     */
    public function get_display_name(): lang_string;

    /**
     * Get the label for the config.
     *
     * @param object $config The config.
     * @param context|null $effectivecontext The effective context, if not in admin.
     * @return string
     */
    public function get_label_for_config(object $config, context $effectivecontext = null): string;

    /**
     * Get short description.
     *
     * @return lang_string
     */
    public function get_short_description(): lang_string;

    /**
     * Is compatible with admin?
     *
     * @return bool
     */
    public function is_compatible_with_admin(): bool;

    /**
     * Is multiple allowed?
     *
     * @return bool
     */
    public function is_multiple_allowed(): bool;

}
