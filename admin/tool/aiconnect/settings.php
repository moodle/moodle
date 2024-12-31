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

/**
 * Settings page
 *
 * @package    tool_aiconnect
 * @copyright  2024 Marcus Green
 * @author     Marcus Green
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $settings = new admin_settingpage('tool_aiconnect', get_string('pluginname', 'tool_aiconnect'));

    $name = new lang_string('openaisettings', 'tool_aiconnect');
    $description = new lang_string('openaisettings_help', 'tool_aiconnect');
    $settings->add(new admin_setting_heading('xopenaisettings', $name, $description));


    $settings->add(new admin_setting_configtext(
        'tool_aiconnect/apikey',
        get_string('apikey', 'tool_aiconnect'),
        get_string('apikey_desc', 'tool_aiconnect'),
        ''
    ));

    $settings->add(new admin_setting_configtextarea(
        'tool_aiconnect/source_of_truth',
        get_string('sourceoftruth', 'tool_aiconnect'),
        get_string('sourceoftruth_desc', 'tool_aiconnect'),
        ''
    ));

    $settings->add(new admin_setting_configtext(
        'tool_aiconnect/endpoint',
        get_string('endpoint', 'tool_aiconnect'),
        get_string('endpoint_desc', 'tool_aiconnect'),
        'https://api.openai.com/v1/chat/completions',
    ));

    $settings->add(new admin_setting_configtext(
        'tool_aiconnect/model',
        get_string('model', 'tool_aiconnect'),
        get_string('model_desc', 'tool_aiconnect'),
        'gpt-4o,gpt-4-turbo,gpt-4,gpt-3.5-turbo',
    ));

    $settings->add(new admin_setting_configtext(
        'tool_aiconnect/temperature',
        get_string('temperature', 'tool_aiconnect'),
        get_string('temperature_desc', 'tool_aiconnect'),
        0,
        PARAM_FLOAT
    ));
    $settings->add(new admin_setting_configcheckbox(
        'tool_aiconnect/log_requests',
        get_string('log_requests', 'tool_aiconnect'),
        get_string('log_requests_text', 'tool_aiconnect') , 0));

    $settings->add(new admin_setting_configcheckbox(
        'tool_aiconnect/json_format',
        get_string('json_format', 'tool_aiconnect'),
        get_string('json_format_text', 'tool_aiconnect') , 0));

    $settings->add(new admin_setting_configtext(
        'tool_aiconnect/top_p',
        get_string('top_p', 'tool_aiconnect'),
        get_string('top_p_desc', 'tool_aiconnect'),
        ''
    ));

    $settings->add(new admin_setting_configtext(
        'tool_aiconnect/frequency_penalty',
        get_string('frequency_penalty', 'tool_aiconnect'),
        get_string('frequency_penalty_desc', 'tool_aiconnect'),
        ''
    ));

    $settings->add(new admin_setting_configtext(
        'tool_aiconnect/presence_penalty',
        get_string('presence_penalty', 'tool_aiconnect'),
        get_string('presence_penalty_desc', 'tool_aiconnect'),
        ''
    ));

    $url = new moodle_url('../admin/tool/aiconnect/test.php');
    $link = html_writer::link($url, get_string('testaiservices', 'tool_aiconnect'));
    $settings->add(new admin_setting_heading('testaiconfiguration', new lang_string('testaiconfiguration', 'tool_aiconnect'),
        new lang_string('testoutgoingmaildetail', 'admin', $link)));
    $ADMIN->add('tools', $settings);


}
