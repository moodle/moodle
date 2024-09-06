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
 * @package     aiprovider_ollama
 * @copyright   2024 Matt Porritt <matt.porritt@moodle.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core_ai\admin\admin_settingspage_provider;

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    // Provider specific settings heading.
    $settings = new admin_settingspage_provider('aiprovider_ollama',
        new lang_string('pluginname', 'aiprovider_ollama'), 'moodle/site:config', true);

    $settings->add(new admin_setting_heading('aiprovider_ollama/general',
        new lang_string('providersettings', 'core_ai'),
        new lang_string('providersettings_desc', 'core_ai')));

    // Setting to store Ollama API URL endpoint.
    $settings->add(new admin_setting_configtext('aiprovider_ollama/endpoint',
        new lang_string('endpoint', 'aiprovider_ollama'),
        new lang_string('endpoint_desc', 'aiprovider_ollama'),
        'http://localhost:11434',
        PARAM_URL));

    // Checkbox to enable basic auth settings.
    $settings->add(new admin_setting_configcheckbox('aiprovider_ollama/enablebasicauth',
        new lang_string('enablebasicauth', 'aiprovider_ollama'),
        new lang_string('enablebasicauth_desc', 'aiprovider_ollama'),
        0));

    // Username for basic auth.
    $settings->add(new admin_setting_configtext('aiprovider_ollama/username',
        new lang_string('username', 'aiprovider_ollama'),
        new lang_string('username_desc', 'aiprovider_ollama'),
        '',
        PARAM_TEXT));
    $settings->hide_if('aiprovider_ollama/username', 'aiprovider_ollama/enablebasicauth', 'eq', 0);

    // Password for basic auth.
    $settings->add(new admin_setting_configpasswordunmask('aiprovider_ollama/password',
        new lang_string('password', 'aiprovider_ollama'),
        new lang_string('password_desc', 'aiprovider_ollama'),
        ''
    ));
    $settings->hide_if('aiprovider_ollama/password', 'aiprovider_ollama/enablebasicauth', 'eq', 0);

    // Setting to enable/disable global rate limiting.
    $settings->add(new admin_setting_configcheckbox('aiprovider_ollama/enableglobalratelimit',
        new lang_string('enableglobalratelimit', 'aiprovider_ollama'),
        new lang_string('enableglobalratelimit_desc', 'aiprovider_ollama'),
        0));

    // Setting to set how many requests per hour are allowed for the global rate limit.
    // Should only be enabled when global rate limiting is enabled.
    $settings->add(new admin_setting_configtext('aiprovider_ollama/globalratelimit',
        new lang_string('globalratelimit', 'aiprovider_ollama'),
        new lang_string('globalratelimit_desc', 'aiprovider_ollama'),
        100,
        PARAM_INT));
    $settings->hide_if('aiprovider_ollama/globalratelimit', 'aiprovider_ollama/enableglobalratelimit', 'eq', 0);

    // Setting to enable/disable user rate limiting.
    $settings->add(new admin_setting_configcheckbox('aiprovider_ollama/enableuserratelimit',
        new lang_string('enableuserratelimit', 'aiprovider_ollama'),
        new lang_string('enableuserratelimit_desc', 'aiprovider_ollama'),
        0));

    // Setting to set how many requests per hour are allowed for the user rate limit.
    // Should only be enabled when user rate limiting is enabled.
    $settings->add(new admin_setting_configtext('aiprovider_ollama/userratelimit',
        new lang_string('userratelimit', 'aiprovider_ollama'),
        new lang_string('userratelimit_desc', 'aiprovider_ollama'),
        10,
        PARAM_INT));
    $settings->hide_if('aiprovider_ollama/userratelimit', 'aiprovider_ollama/enableuserratelimit', 'eq', 0);
}
