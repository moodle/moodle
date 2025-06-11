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
 * Tiny Panopto LTI Video plugin.
 *
 * @package    tiny_panoptoltibutton
 * @copyright  2023 Panopto
 * @author     Panopto
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tiny_panoptoltibutton;

use context;
use editor_tiny\editor;
use editor_tiny\plugin;
use editor_tiny\plugin_with_buttons;
use editor_tiny\plugin_with_menuitems;
use editor_tiny\plugin_with_configuration;

require_once(dirname(__FILE__) . '/../../../../../../config.php');
require_once($CFG->dirroot . '/blocks/panopto/lib/lti/panoptoblock_lti_utility.php');

/**
 * Tiny Panopto LTI Video plugin.
 *
 * @package    tiny_panoptoltibutton
 * @copyright  2023 Panopto
 * @author     Panopto
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class plugininfo extends plugin implements
    plugin_with_buttons,
    plugin_with_menuitems,
    plugin_with_configuration {

    /**
     * Get a list of the buttons provided by this plugin.
     *
     * @return string[]
     */
    public static function get_available_buttons(): array {
        return [
            'tiny_panoptoltibutton/panoptoltibutton',
        ];
    }

    /**
     * Get a list of the menu items provided by this plugin.
     *
     * @return string[]
     */
    public static function get_available_menuitems(): array {
        return [
            'tiny_panoptoltibutton/panoptoltibutton',
        ];
    }

    /**
     * Get an array of options provided by this plugin.
     *
     * @param context $context The context that the editor is used within
     * @param array $options The options passed in when requesting the editor
     * @param array $fpoptions The filepicker options passed in when requesting the editor
     * @param editor|null $editor The editor instance in which the plugin is initialised
     * @return array
     */
    public static function get_plugin_configuration_for_context(
        context $context,
        array $options,
        array $fpoptions,
        ?\editor_tiny\editor $editor = null
    ): array {
        global $COURSE, $CFG, $PAGE;

        $ltitool = \panoptoblock_lti_utility::get_course_tool($COURSE->id);

        // Remove sensitive info from $config.
        unset($ltitool->config['password'], $ltitool->config['servicesalt']);

        $resourcebase = sha1(
            $PAGE->url->__toString() . '&' . $PAGE->course->sortorder
                . '&' . $PAGE->course->timecreated
        );

        return [
            // These will be mapped to a namespaced EditorOption in Tiny.
            'courseid' => $COURSE->id,
            'tool' => !empty($ltitool) ? $ltitool : "",
            'wwwroot' => $CFG->wwwroot,
            'contentitempath' => '/lib/editor/tiny/plugins/panoptoltibutton/contentitem.php',
            'resourcebase' => $resourcebase,
            'panoptoltibuttondescription' => get_string('panopto_button_description', 'tiny_panoptoltibutton'),
            'panoptoltibuttonlongdescription' => get_string('panopto_button_long_description', 'tiny_panoptoltibutton'),
            'unprovisionederror' => get_string('panopto_button_unprovisioned_error', 'tiny_panoptoltibutton')
        ];
    }
}
