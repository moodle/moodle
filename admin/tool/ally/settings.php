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
 * Admin settings.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2016 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use tool_ally\logging\constants;

// We have to include this so that it's available before an upgrade completes and registers the classes for autoloading.
require_once($CFG->dirroot.'/admin/tool/ally/classes/adminsetting/ally_config_link.php');
require_once($CFG->dirroot.'/admin/tool/ally/classes/adminsetting/ally_configpasswordunmask.php');
require_once($CFG->dirroot.'/admin/tool/ally/classes/adminsetting/ally_pickroles.php');
require_once($CFG->dirroot.'/admin/tool/ally/classes/adminsetting/ally_trim.php');

use tool_ally\adminsetting\ally_config_link;
use tool_ally\adminsetting\ally_configpasswordunmask;
use tool_ally\adminsetting\ally_pickroles;
use tool_ally\adminsetting\ally_trim;

if ($hassiteconfig) {
    // We need to import the library to use a setting update callback in here.
    require_once($CFG->dirroot.'/admin/tool/ally/lib.php');

    $settings = new admin_settingpage('tool_ally', get_string('pluginname', 'tool_ally'));

    $settings->add(new ally_pickroles('tool_ally/roles', new lang_string('contentauthors', 'tool_ally'),
        new lang_string('contentauthorsdesc', 'tool_ally'), ['manager', 'coursecreator', 'editingteacher']));

    $settings->add(new ally_trim('tool_ally/key', new lang_string('key', 'tool_ally'),
        new lang_string('keydesc', 'tool_ally'), '', PARAM_ALPHANUMEXT));

    $settings->add(new ally_configpasswordunmask('tool_ally/secret',
        new lang_string('secret', 'tool_ally'), new lang_string('secretdesc', 'tool_ally'), ''));

    $settings->add(new admin_setting_configtext('tool_ally/adminurl', new lang_string('adminurl', 'tool_ally'),
        new lang_string('adminurldesc', 'tool_ally'), '', PARAM_URL, 60));

    $settings->add(new admin_setting_configtext('tool_ally/pushurl', new lang_string('pushurl', 'tool_ally'),
        new lang_string('pushurldesc', 'tool_ally'), '', PARAM_URL, 60));

    $settings->add(new admin_setting_configtext('tool_ally/clientid', new lang_string('clientid', 'tool_ally'),
        new lang_string('clientiddesc', 'tool_ally'), '', PARAM_INT, 5));

    $settings->add(new ally_config_link('tool_ally/autconf', new lang_string('autoconfigure', 'tool_ally'),
        new moodle_url('/admin/tool/ally/autoconfigws.php')));

    $settings->add(new ally_config_link('tool_ally/allyclientconfig', new lang_string('allyclientconfig', 'tool_ally'),
        new moodle_url('/admin/tool/ally/lti/view.php')));

    $setting = new admin_setting_configcheckbox('tool_ally/excludeunused',
        new lang_string('excludeunused', 'tool_ally'),
        new lang_string('excludeunuseddesc', 'tool_ally'), 0);
    $setting->set_updatedcallback('tool_ally_exclude_setting_changed');
    $settings->add($setting);

    $choices = [
        constants::RANGE_NONE => get_string('loglevel:none', 'tool_ally'),
        constants::RANGE_LIGHT => get_string('loglevel:light', 'tool_ally'),
        constants::RANGE_MEDIUM => get_string('loglevel:medium', 'tool_ally'),
        constants::RANGE_ALL => get_string('loglevel:all', 'tool_ally')
    ];
    $settings->add(new admin_setting_configselect('tool_ally/logrange', new lang_string('logrange', 'tool_ally'),
        null, constants::RANGE_ALL, $choices));

    $settings->add(new admin_setting_configtext('tool_ally/loglifetimedays', new lang_string('loglifetimedays', 'tool_ally'),
        new lang_string('loglifetimedaysdesc', 'tool_ally'), '14', PARAM_INT));

    $config     = get_config('tool_ally');
    $configured = !empty($config) && !empty($config->adminurl) && !empty($config->key) && !empty($config->secret);
    if ($configured) {
        $ADMIN->add('tools', new admin_externalpage('allyclientconfig', get_string('allyclientconfig', 'tool_ally'),
            "$CFG->wwwroot/admin/tool/ally/lti/view.php", 'tool/ally:clientconfig'));
        $ADMIN->add('tools', new admin_externalpage('allylogs', get_string('logs', 'tool_ally'),
            "$CFG->wwwroot/admin/tool/ally/logs.php", 'tool/ally:viewlogs'));
    }

    $settings->add(new admin_setting_configcheckbox('tool_ally/deferredcourseevents',
        new lang_string('deferredcourseevents', 'tool_ally'),
        new lang_string('deferredcourseeventsdesc', 'tool_ally'), 0));

    $ADMIN->add('tools', $settings);
}
