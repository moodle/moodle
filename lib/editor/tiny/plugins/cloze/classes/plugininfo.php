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
 * Tiny Cloze Editor plugin for Moodle.
 *
 * @package     tiny_cloze
 * @copyright   2023 MoodleDACH
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tiny_cloze;

use context;
use editor_tiny\editor;
use editor_tiny\plugin;
use editor_tiny\plugin_with_buttons;
use editor_tiny\plugin_with_configuration;
use editor_tiny\plugin_with_menuitems;
use question_bank;

defined('MOODLE_INTERNAL') || die;

require_once(__DIR__ . '/../../../../../behat/classes/util.php');

/**
 * The capabilities of the plugin, in this case there is one toolbar button and one menu item.
 */
class plugininfo extends plugin implements plugin_with_buttons, plugin_with_menuitems, plugin_with_configuration {

    /**
     * Get the internal name of the toolbar button.
     * @return string[]
     */
    public static function get_available_buttons(): array {
        return [
            'tiny_cloze',
        ];
    }

    /**
     * Get the internal name of the menu item.
     * @return string[]
     */
    public static function get_available_menuitems(): array {
        return [
            'tiny_cloze',
        ];
    }

    /**
     * Returns the configuration values the plugin needs to take into consideration.
     *
     * @param context $context
     * @param array $options
     * @param array $fpoptions
     * @param editor|null $editor
     * @return array
     */
    public static function get_plugin_configuration_for_context(
        context $context,
        array $options,
        array $fpoptions,
        ?editor $editor = null
    ): array {
        global $CFG;

        // When on the test site, check that the simulation config for an existing regex question type is set.
        if (\behat_util::is_test_site()) {
            return [
                'testsite' => true,
                'multianswerrgx' => (bool)get_config('tiny_cloze', 'simulate_multianswerrgx'),
            ];
        }

        $config = [
            'testsite' => false,
            'multianswerrgx' => false,
        ];

        // The class question_bank is not found at times. Therefore check and include the file.
        if (!class_exists('question_bank')) {
            require_once($CFG->dirroot . '/question/engine/bank.php');
        }

        try {
            // Check if the multianswerrgx question type is available.
            $instance = question_bank::get_qtype('multianswerrgx');
            $config['multianswerrgx'] = is_object($instance);
            return $config;
        } catch (\exception $e) {
            // The multianswerrgx question type is not available.
            return $config;
        }
        return $config;
    }

}
