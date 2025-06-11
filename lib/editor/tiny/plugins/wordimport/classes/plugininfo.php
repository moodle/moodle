<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Tiny Microsoft Word File Import (TinyMCE) plugin for Moodle.
 *
 * @package     tiny_wordimport
 * @copyright   2024 University of Graz
 * @author      André Menrath <andre.menrath@uni-graz.at>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tiny_wordimport;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/repository/lib.php');

use context;
use editor_tiny\plugin;
use editor_tiny\plugin_with_buttons;
use editor_tiny\plugin_with_menuitems;
use editor_tiny\plugin_with_configuration;

/**
 * Pluginfo class which handles all information which is passed to the TinyMCE editor.
 */
class plugininfo extends plugin implements plugin_with_configuration, plugin_with_buttons, plugin_with_menuitems {
    /**
     * Add buttons to Tiny-Editor.
     *
     * @return array
     */
    public static function get_available_buttons(): array {
        return [
            'tiny_wordimport/plugin',
        ];
    }

    /**
     * Add Menu entry to Tiny-Editor.
     *
     * @return array
     */
    public static function get_available_menuitems(): array {
        return [
            'tiny_wordimport/plugin',
        ];
    }

    /**
     * Is the plugin enabled? This is the case when the capabilities are met.
     *
     * @param context $context The context that the editor is used within
     * @param array $options The options passed in when requesting the editor
     * @param array $fpoptions The filepicker options passed in when requesting the editor
     * @param \editor_tiny\editor|null $editor The editor instance in which the plugin is initialised
     * @return boolean
     */
    public static function is_enabled(
        context $context,
        array $options,
        array $fpoptions,
        ?\editor_tiny\editor $editor = null
    ): bool {
        // Hack to make neither PHP Code Checker nor PHP Mess Detector complain.
        unset($options, $fpoptions, $editor);
        return has_capability('tiny/wordimport:add', $context);
    }

    /**
     * Pass options to the WordImport editor plugin.
     *
     * @param context $context
     * @param array $options
     * @param array $fpoptions
     * @param \editor_tiny\editor|null $editor
     * @return array
     */
    public static function get_plugin_configuration_for_context(
        context $context,
        array $options,
        array $fpoptions,
        ?\editor_tiny\editor $editor = null
    ): array {
        // Hack to make neither PHP Code Checker nor PHP Mess Detector complain.
        unset($options, $fpoptions, $editor);
        // Need these three to filter repositories list.
        $args = new \stdClass();
        $args->accepted_types = ['.docx'];
        $args->return_types = (FILE_INTERNAL | FILE_EXTERNAL);
        $args->context = $context;
        $args->env = 'filepicker';
        $filepicker = initialise_filepicker($args);
        $filepicker->context = $context;
        $filepicker->client_id = uniqid();
        $filepicker->env = 'editor';
        return [
            // Your values go here.
            // These will be mapped to a namespaced EditorOption in Tiny.
            'heading1StyleLevel' => 'TODO Calculate your values here',
            'wordFilePickerOption' => $filepicker,
        ];
    }
}
