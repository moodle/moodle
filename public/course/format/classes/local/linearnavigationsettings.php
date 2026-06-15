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

namespace core_courseformat\local;

use core\lang_string;

/**
 * Class course linear navigation settings.
 *
 * @package    core_courseformat
 * @copyright  2025 Laurent David <laurent.david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class linearnavigationsettings {
    /** @var string Setting name for enabling linear navigation */
    public const SETTING_ENABLE_LINEAR_NAV = 'enablelinearnav';

    /** @var int Default value for enabling linear navigation */
    private const SETTING_ENABLE_LINEAR_NAV_DEFAULT = 1;

    /**
     * Get the default linear navigation value for a format.
     *
     * @param string $formatname The course format name
     * @return int
     */
    private static function get_default_linear_navigation_value(string $formatname): int {
        $formatconfig = get_config('format_' . $formatname);
        if (is_object($formatconfig) && property_exists($formatconfig, self::SETTING_ENABLE_LINEAR_NAV)) {
            return (int) $formatconfig->{self::SETTING_ENABLE_LINEAR_NAV};
        }

        return self::SETTING_ENABLE_LINEAR_NAV_DEFAULT;
    }

    /**
     * Get type and default course format options related to linear navigation.
     *
     * @param string $formatname The course format name
     * @return array
     */
    public static function get_course_format_options_default(string $formatname): array {
        return [
            self::SETTING_ENABLE_LINEAR_NAV => [
                'default' => self::get_default_linear_navigation_value($formatname),
                'type' => PARAM_BOOL,
            ],
        ];
    }

    /**
     * Get edit form for course format options related to linear navigation.
     *
     * @param string $formatname The course format name
     * @return array
     */
    public static function get_course_format_options_edit_form(string $formatname): array {
        $label = get_string_manager()->string_exists('linearnavigationsettings', $formatname) ?
            new lang_string('linearnavigationsettings', $formatname) :
            new lang_string('linearnavigationsettings', 'core_courseformat');
        $helpcomponent = get_string_manager()->string_exists('linearnavigationsettings_help', $formatname) ?
            $formatname : 'core_courseformat';
        return [
            self::SETTING_ENABLE_LINEAR_NAV => [
                'label' => $label,
                'element_type' => 'select',
                'element_attributes' => [
                    [
                        0 => new lang_string('no'),
                        1 => new lang_string('yes'),
                    ],
                ],
                'inline_help' => 'linearnavigationsettings',
                'help_component' => $helpcomponent,
            ],
        ];
    }

    /**
     * Hook to set the default value of the linear navigation setting
     * to enabled when creating a new course.
     *
     * @param \core_course\hook\after_form_definition_after_data $hook The hook object.
     */
    public static function after_form_definition_after_data(
        \core_course\hook\after_form_definition_after_data $hook,
    ): void {
        if (!$hook->mform->elementExists(self::SETTING_ENABLE_LINEAR_NAV)) {
            return;
        }
        $enablelinearnavelement = $hook->mform->getElement(self::SETTING_ENABLE_LINEAR_NAV);
        $course = $hook->formwrapper->get_course();
        $formatvalue = $hook->mform->getElementValue('format');
        $courseformat = null;
        if (is_array($formatvalue) && !empty($formatvalue)) {
            $params = ['format' => $formatvalue[0]];
            if (!empty($course->id)) {
                $params['id'] = $course->id;
            }
            $courseformat = course_get_format((object) $params);
        }
        if ($enablelinearnavelement && empty($course->id) && $courseformat) {
            $enablelinearnavelement->setValue(
                self::get_default_linear_navigation_value($courseformat->get_format())
            );
        }
    }
}
