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

namespace tiny_autosave;

use context;
use editor_tiny\plugin;
use editor_tiny\plugin_with_configuration;

/**
 * Tiny autosave plugin for Moodle.
 *
 * @package    tiny_autosave
 * @copyright  2021 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class plugininfo extends plugin implements plugin_with_configuration {

    public static function get_plugin_configuration_for_context(
        context $context,
        array $options,
        array $fpoptions,
        ?\editor_tiny\editor $editor = null
    ): array {
        global $PAGE;

        if (empty($editor) || empty($options['autosave'])) {
            return [
                'autosave' => null,
            ];
        }

        return [
            'pagehash' => sha1($PAGE->url . '<>' . s($editor->get_text())),
            'pageinstance' => bin2hex(random_bytes(16)),
            'backoffTime' => (defined('BEHAT_SITE_RUNNING') && BEHAT_SITE_RUNNING) ? 0 : 500,
        ];
    }
}
