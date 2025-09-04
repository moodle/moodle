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

namespace aiplacement_editor;

use core_ai\aiactions\generate_image;
use core_ai\aiactions\generate_text;
use core_ai\manager;

/**
 * AI Placement HTML editor utils.
 *
 * @package    aiplacement_editor
 * @copyright  2024 Huong Nguyen <huongnv13@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class utils {

    /**
     * Check if AI Placement HTML editor action is available for the context.
     *
     * @param \context $context The context.
     * @param string $actionname The name of the action.
     * @param string $actionclass The class name of the action.
     * @param bool $checkcontext If true, check the action is available in context.
     * @return bool If the action is accessible, available, and enable.
     */
    public static function is_html_editor_placement_action_available(
        \context $context,
        string $actionname,
        string $actionclass,
        bool $checkcontext = true,
    ): bool {
        if (!self::is_html_editor_placement_available()) {
            return false;
        }

        $aimanager = \core\di::get(manager::class);
        if (
            has_capability("aiplacement/editor:{$actionname}", $context)
            && $aimanager->is_action_available($actionclass)
            && $aimanager->is_action_enabled('aiplacement_editor', $actionclass)
            && (!$checkcontext || $aimanager->is_action_enabled_in_context($context, $actionclass))
        ) {
            return true;
        }

        return false;
    }

    /**
     * Check if AI Placement HTML editor is available.
     *
     * @return bool If the placement is enabled.
     */
    public static function is_html_editor_placement_available(): bool {
        [$plugintype, $pluginname] = explode('_', \core_component::normalize_componentname('aiplacement_editor'), 2);
        $pluginmanager = \core_plugin_manager::resolve_plugininfo_class($plugintype);
        if (!$pluginmanager::is_plugin_enabled($pluginname)) {
            return false;
        }

        return true;
    }

    /**
     * Get all the actions available for HTML editor placement.
     *
     * @param \context $context The context.
     * @param bool $checkcontext If true, check the action is available in context.
     * @return array Return the actions available with data.
     */
    public static function get_actions_available(\context $context, bool $checkcontext = true): array {
        $actions = [];

        // Action generate_text.
        if (self::is_html_editor_placement_action_available($context, 'generate_text', generate_text::class, $checkcontext)) {
            $actions[] = [
                'action' => 'generate_text',
                'buttontext' => get_string('action_generate_text', 'core_ai'),
                'title' => get_string('action_generate_text_desc', 'core_ai'),
            ];
        }

        // Action generate_image.
        if (self::is_html_editor_placement_action_available($context, 'generate_image', generate_image::class, $checkcontext)) {
            $actions[] = [
                'action' => 'generate_image',
                'buttontext' => get_string('action_generate_image', 'core_ai'),
                'title' => get_string('action_generate_image_desc', 'core_ai'),
            ];
        }

        return $actions;
    }
}
