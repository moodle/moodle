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

namespace aiplacement_courseassist\output;

use aiplacement_courseassist\utils;
use core\hook\output\after_http_headers;
use core\hook\output\before_footer_html_generation;

/**
 * Output handler for the course assist AI Placement.
 *
 * @package    aiplacement_courseassist
 * @copyright  2024 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class assist_ui {
    /**
     * Bootstrap the course assist UI.
     *
     * @param before_footer_html_generation $hook
     */
    public static function load_assist_ui(before_footer_html_generation $hook): void {
        global $PAGE, $OUTPUT, $USER;

        // Preflight checks.
        if (!self::preflight_checks()) {
            return;
        }

        // Load the markup for the assist interface.
        $params = [
            'userid' => $USER->id,
            'contextid' => $PAGE->context->id,
        ];
        $html = $OUTPUT->render_from_template('aiplacement_courseassist/drawer', $params);
        $hook->add_html($html);
    }

    /**
     * Determine if we should be loading a single button or a dropdown.
     *
     * @param after_http_headers $hook
     */
    public static function action_buttons_handler(after_http_headers $hook): void {
        global $PAGE, $OUTPUT;

        // Preflight checks.
        if (!self::preflight_checks()) {
            return;
        }

        $actions['actions'] = utils::get_actions_available($PAGE->context);

        // No actions available.
        if (empty($actions['actions'])) {
            return;
        }

        if (count($actions['actions']) > 1) {
            $actions['isdropdown'] = true;
        }

        $html = $OUTPUT->render_from_template('aiplacement_courseassist/actions', $actions);
        $hook->add_html($html);
    }

    /**
     * Preflight checks to determine if the assist UI should be loaded.
     *
     * @return bool
     */
    private static function preflight_checks(): bool {
        global $PAGE;
        if (during_initial_install()) {
            return false;
        }
        if (!get_config('aiplacement_courseassist', 'version')) {
            return false;
        }
        if (in_array($PAGE->pagelayout, ['maintenance', 'print', 'redirect', 'embedded'])) {
            // Do not try to show assist UI inside iframe, in maintenance mode,
            // when printing, or during redirects.
            return false;
        }
        // Check we are in the right context, exit if not activity.
        if ($PAGE->context->contextlevel != CONTEXT_MODULE) {
            return false;
        }

        // Check if the user has permission to use the AI service.
        return utils::is_course_assist_available($PAGE->context);
    }
}
