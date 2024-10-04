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

namespace aiplacement_courseassist;

use core_ai\aiactions\explain_text;
use core_ai\aiactions\summarise_text;
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
        [$plugintype, $pluginname] = explode('_', \core_component::normalize_componentname('aiplacement_courseassist'), 2);
        $pluginmanager = \core_plugin_manager::resolve_plugininfo_class($plugintype);
        if (!$pluginmanager::is_plugin_enabled($pluginname)) {
            return false;
        }

        if (empty(self::get_actions_available($context))) {
            return false;
        }

        return true;
    }

    /**
     * Get all the actions available and return action data for template.
     *
     * @param \context $context The context.
     * @return array Return the actions available with data.
     */
    public static function get_actions_available(\context $context): array {
        $actions = [];
        $actionclasses = [
            summarise_text::class,
            explain_text::class,
        ];
        $manager = \core\di::get(manager::class);
        $providers = $manager->get_providers_for_actions($actionclasses, true);

        // Summarise text.
        if (has_capability('aiplacement/courseassist:summarise_text', $context)
            && $manager->is_action_available(summarise_text::class)
            && $manager->is_action_enabled('aiplacement_courseassist', summarise_text::class)
            && !empty($providers[summarise_text::class])
        ) {
            $actions[] = [
                'action' => 'summarise',
                'buttontext' => get_string('summarise', 'aiplacement_courseassist'),
                'title' => get_string('summarise_tooltips', 'aiplacement_courseassist'),
            ];
        }
        // Explain text.
        if (has_capability('aiplacement/courseassist:explain_text', $context)
            && $manager->is_action_available(explain_text::class)
            && $manager->is_action_enabled('aiplacement_courseassist', explain_text::class)
            && !empty($providers[explain_text::class])
        ) {
            $actions[] = [
                'action' => 'explain',
                'buttontext' => get_string('explain', 'aiplacement_courseassist'),
                'title' => get_string('explain_tooltips', 'aiplacement_courseassist'),
            ];
        }

        return $actions;
    }
}
