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

namespace aiplacement_courseassistimprove;
use core_ai\aiactions\improve_text;
use core_ai\manager;

/**
 * AI Placement course assist utils.
 *
 * @package    aiplacement_courseassist
 * @copyright  2024 Huong Nguyen <huongnv13@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class utils {
    /**
     * Check if AI Placement course assist is available for the context.
     *
     * @param \context $context The context.
     * @return bool True if AI Placement course assist is available, false otherwise.
     */
    public static function is_course_assist_available(\context $context): bool {
        [$plugintype, $pluginname] = explode('_', \core_component::normalize_componentname('aiplacement_courseassistimprove'), 2);
        $manager = \core_plugin_manager::resolve_plugininfo_class($plugintype);
        if (!$manager::is_plugin_enabled($pluginname)) {
            return false;
        }

        $providers = manager::get_providers_for_actions([improve_text::class], true);
        if (!has_capability('aiplacement/courseassistimprove:improve_text', $context)
            || !manager::is_action_available(improve_text::class)
            || !manager::is_action_enabled('aiplacement_courseassistimprove', improve_text::class)
            || empty($providers[improve_text::class])
        ) {
            return false;
        }

        return true;
    }
}
