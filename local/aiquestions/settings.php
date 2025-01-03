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
 * Plugin administration pages are defined here.
 *
 * @package     local_aiquestions
 * @category    admin
 * @copyright   2023 Ruthy Salomon <ruthy.salomon@gmail.com> , Yedidia Klein <yedidia@openapp.co.il>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $settings = new admin_settingpage('local_aiquestions_settings', new lang_string('pluginname', 'local_aiquestions'));

    // OpenAI key.
    $settings->add( new admin_setting_configpasswordunmask(
        'local_aiquestions/key',
        get_string('openaikey', 'local_aiquestions'),
        get_string('openaikeydesc', 'local_aiquestions'),
        '', PARAM_TEXT, 50
    ));

    // Number of tries.
    $settings->add( new admin_setting_configtext(
        'local_aiquestions/numoftries',
        get_string('numoftriesset', 'local_aiquestions'),
        get_string('numoftriesdesc', 'local_aiquestions'),
        10, PARAM_INT, 10
    ));

    // Language.
    $languages = get_string_manager()->get_list_of_languages();
    asort($languages);
    $settings->add(new admin_setting_configselect(
        'local_aiquestions/language',
        get_string('language', 'local_aiquestions'),
        get_string('languagedesc', 'local_aiquestions'),
        'en', $languages
    ));


    $ADMIN->add('localplugins', $settings);

    // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedIf
    if ($ADMIN->fulltree) {
        // TODO: Define actual plugin settings page and add it to the tree - {@link https://docs.moodle.org/dev/Admin_settings}.
    }
}
