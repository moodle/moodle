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

namespace core_courseformat;

use action_menu;
use renderer_base;
use section_info;
use core_courseformat\stateupdates;
use core_courseformat\output\local\content\section\controlmenu;
use core_courseformat\base as course_format;
use stdClass;

/**
 * Section delegate base class.
 *
 * Plugins using delegate sections must extend this class into
 * their PLUGINNAME\courseformat\sectiondelegate class.
 *
 * @package    core_courseformat
 * @copyright  2023 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class sectiondelegate {

    /**
     * Constructor.
     * @param section_info $sectioninfo
     */
    public function __construct(
        protected section_info $sectioninfo
    ) {
    }

    /**
     * Get the section info instance if available.
     *
     * @param section_info $sectioninfo
     * @return section_info|null
     */
    public static function instance(section_info $sectioninfo): ?self {
        if (empty($sectioninfo->component)) {
            return null;
        }
        $classname = self::get_delegate_class_name($sectioninfo->component);
        if ($classname === null) {
            return null;
        }
        $instance = new $classname($sectioninfo);
        if (!$instance->is_enabled()) {
            return null;
        }
        return $instance;
    }

    /**
     * Return the delgate class name of a plugin, if any.
     * @param string $pluginname
     * @return string|null the delegate class name or null if not found.
     */
    protected static function get_delegate_class_name(string $pluginname): ?string {
        $classname = $pluginname . '\courseformat\sectiondelegate';
        if (!class_exists($classname)) {
            return null;
        }
        return $classname;
    }

    /**
     * Check if a plugin has a delegate class.
     * @param string $pluginname
     * @return bool
     */
    public static function has_delegate_class(string $pluginname): bool {
        return self::get_delegate_class_name($pluginname) !== null;
    }

    /**
     * Check if the delegate is enabled.
     *
     * Usually this happens when the delegate plugin is disabled.
     * @return bool
     */
    public function is_enabled(): bool {
        return true;
    }

    /**
     * Define the section final name.
     *
     * This method can process the section name and return the validated new name.
     *
     * @param section_info $section
     * @param string|null $newname the new name value to store in the database
     * @return string|null the name value to store in the database
     */
    public function preprocess_section_name(section_info $section, ?string $newname): ?string {
        return $newname;
    }

    /**
     * Add extra state updates when put or create a section.
     *
     * This method is called every time the backend sends a delegated section
     * state update to the UI.
     *
     * @param section_info $section the affected section.
     * @param stateupdates $updates the state updates object to notify the UI.
     */
    public function put_section_state_extra_updates(section_info $section, stateupdates $updates): void {
        // By default, do nothing.
    }

    /**
     * Allow delegate plugin to modify the available section menu.
     *
     * @param course_format $format The course format instance.
     * @param controlmenu $controlmenu The control menu instance.
     * @param renderer_base $output The renderer instance.
     * @return action_menu|null The new action menu with the list of edit control items or null if no action menu is available.
     */
    public function get_section_action_menu(
        course_format $format,
        controlmenu $controlmenu,
        renderer_base $output,
    ): ?action_menu {
        return $controlmenu->get_default_action_menu($output);
    }

    /**
     * Get the parent section of the current delegated section if any.
     *
     * @return section_info|null
     */
    public function get_parent_section(): ?section_info {
        return null;
    }

    /**
     * Handler executed when a section has been updated.
     *
     * This method uses a record instead of a section_info object because
     * section updates can be done in batch and the course_info may not be yet updated.
     *
     * This method does not need to recalculate the section_info object.
     *
     * @param stdClass $sectionrecord the new section data
     */
    public function section_updated(stdClass $sectionrecord): void {
        // By default, do nothing.
    }
}
