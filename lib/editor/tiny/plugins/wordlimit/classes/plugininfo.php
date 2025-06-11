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

namespace tiny_wordlimit;

use context;
use editor_tiny\editor;
use editor_tiny\plugin;
use editor_tiny\plugin_with_configuration;
use tiny_wordlimit\wordlimit;

/**
 * Tiny Wordlimit plugin.
 *
 * @package    tiny_wordlimit
 * @copyright  2023 University of Graz
 * @author     André Menrath <andre.menrath@uni-graz.at>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class plugininfo extends plugin implements plugin_with_configuration {
    /**
     * Whether the plugin is enabled.
     *
     * @param context|null $context The context that the editor is used within
     * @param array $options The options passed in when requesting the editor
     * @param array $fpoptions The filepicker options passed in when requesting the editor
     * @param editor|null $editor The editor instance in which the plugin is initialised
     * @return bool
     */
    public static function is_enabled(
        ?context $context,
        array $options = [],
        array $fpoptions = [],
        ?editor $editor = null
    ): bool {
        // Hack for making neither PHP Code Checker nor PHP Mess Detector not complain.
        unset($context, $options, $fpoptions, $editor);
        // Disabled if:
        // - Not logged in or guest.
        return isloggedin();
    }

    /**
     * Get the editor configuration for the tiny_ordlimit plugin.
     *
     * @param context $context The context that the editor is used within
     * @param array   $options The options passed in when requesting the editor
     * @param array   $fpoptions The filepicker options passed in when requesting the editor
     * @param editor|null  $editor The editor instance in which the plugin is initialised
     * @return array  $configuration object containing the wordlimit(s)
     */
    public static function get_plugin_configuration_for_context(
        context $context,
        array $options,
        array $fpoptions,
        ?editor $editor = null
    ): array {
        // Hack for making neither PHP Code Checker nor PHP Mess Detector not complain.
        unset($options, $fpoptions);

        $configuration = [];

        if ($editor) {
            $wordlimits = wordlimit::detect_wordlimits_on_page($context);
            // Using an object cause the tiny editor register option for arrays doesn't accept indexes other than [0..i].
            $configuration['wordLimits'] = (object) $wordlimits;
        }

        return $configuration;
    }
}
