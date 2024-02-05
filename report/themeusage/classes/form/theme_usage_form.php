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

namespace report_themeusage\form;

use moodleform;
use core\output\theme_usage;

/**
 * Defines the form for generating theme usage report data.
 *
 * @package    report_themeusage
 * @copyright  2023 David Woloszyn <david.woloszyn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL Juv3 or later
 */
class theme_usage_form extends moodleform {

    /**
     * Build the form definition.
     */
    protected function definition() {
        $mform = $this->_form;

        // Theme choices (e.g. boost, classic).
        $themechoice = $this->_customdata['themechoice'];
        $themechoices = array_merge(['' => get_string('select') . '...'], self::get_theme_choices());
        $mform->addElement('select', 'themechoice', get_string('themename', 'report_themeusage'), $themechoices);
        $mform->setType('themechoice', PARAM_TEXT);
        $mform->addRule('themechoice', get_string('required'), 'required', null, 'client');
        if (!empty($themechoice)) {
            $mform->setDefault('themechoice', $themechoice);
        }

        // Theme usage types (e.g. user, course, cohort, category).
        $typechoices = self::get_type_choices();
        $mform->addElement('select', 'typechoice', get_string('usagetype', 'report_themeusage'), $typechoices);
        $mform->setType('typechoice', PARAM_TEXT);
        $mform->addRule('typechoice', get_string('required'), 'required', null, 'client');
        $mform->setDefault(theme_usage::THEME_USAGE_TYPE_ALL, $themechoice);

        // Submit button.
        $mform->addElement('submit', 'submit', get_string('getreport', 'report_themeusage'));
    }

    /**
     * Get a list of available theme usage types.
     *
     * @return array
     */
    public static function get_type_choices(): array {
        return [
            theme_usage::THEME_USAGE_TYPE_ALL => get_string('all'),
            theme_usage::THEME_USAGE_TYPE_USER => get_string('user'),
            theme_usage::THEME_USAGE_TYPE_COURSE => get_string('course'),
            theme_usage::THEME_USAGE_TYPE_COHORT => get_string('cohort', 'cohort'),
            theme_usage::THEME_USAGE_TYPE_CATEGORY => get_string('category'),
        ];
    }

    /**
     * Get a list of available themes.
     *
     * @return array
     */
    public static function get_theme_choices(): array {
        $themes = \core_component::get_plugin_list('theme');
        foreach ($themes as $themename => $themedir) {
            $themechoices[$themename] = get_string('pluginname', 'theme_'.$themename);
        }
        return $themechoices;
    }

    /**
     * Check the requested theme is in a list of available themes.
     *
     * @param string $themechoice The theme name.
     * @return bool
     */
    public static function validate_theme_choice_param(string $themechoice): bool {
        if (!empty($themechoice) && !array_key_exists($themechoice, self::get_theme_choices())) {
            return false;
        }
        return true;
    }
}
