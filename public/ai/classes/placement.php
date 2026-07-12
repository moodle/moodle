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

namespace core_ai;

/**
 * Class placement.
 *
 * @package    core_ai
 * @copyright  2024 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class placement {
    /**
     * Get the actions that this placement supports.
     *
     * Returns an array of action class names.
     *
     * @return array An array of action class names.
     */
    abstract public static function get_action_list(): array;

    /**
     * Check whether this placement is available in a context.
     *
     * Placement plugins should override this method when they provide actions
     * for a context. The default keeps existing placements compatible until
     * they opt in to the context-aware placement API.
     *
     * @param \context $context The context to check.
     * @return bool Whether the placement is available in the context.
     */
    public static function is_available_in_context(\context $context): bool {
        return false;
    }

    /**
     * Get the available actions for this placement in a context.
     *
     * @param \context $context The context.
     * @param bool $checkcontext Whether to check the action is enabled in the context.
     * @return array The available actions.
     */
    public static function get_actions_available(\context $context, bool $checkcontext = true): array {
        return [];
    }

    /**
     * Given an action class name.
     *
     * Returns an array of sub actions that this placement supports.
     *
     * @param string $classname The action class name.
     * @return array An array of supported sub actions.
     */
    public function get_sub_actions(string $classname): array {
        return [];
    }
}
