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
 * Administration settings definitions for mlbackend_python.
 *
 * @package   mlbackend_python
 * @copyright 2019 David Monllaó
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {

    $info = $OUTPUT->notification(get_string('serversettingsinfo', 'mlbackend_python'), 'info');
    $settings->add(new admin_setting_heading('mlbackend_python/serversettingsinfo', '', $info));

    $settings->add(new admin_setting_configcheckbox('mlbackend_python/useserver', get_string('useserver', 'mlbackend_python'),
                       get_string('useserverdesc', 'mlbackend_python'), 0));

    $settings->add(new admin_setting_configtext('mlbackend_python/host', get_string('host', 'mlbackend_python'),
                       get_string('host', 'mlbackend_python'), '', PARAM_HOST));
    $settings->hide_if('mlbackend_python/host', 'mlbackend_python/useserver', 'neq', '1');

    $settings->add(new admin_setting_configtext('mlbackend_python/port', get_string('port', 'mlbackend_python'),
                       get_string('port', 'mlbackend_python'), '', PARAM_INT));
    $settings->hide_if('mlbackend_python/port', 'mlbackend_python/useserver', 'neq', '1');

    $settings->add(new admin_setting_configcheckbox('mlbackend_python/secure', get_string('secure', 'mlbackend_python'),
                       get_string('securedesc', 'mlbackend_python'), 0));
    $settings->hide_if('mlbackend_python/secure', 'mlbackend_python/useserver', 'neq', '1');

    $settings->add(new admin_setting_configtext('mlbackend_python/username', get_string('username', 'mlbackend_python'),
                       get_string('usernamedesc', 'mlbackend_python'), 'default', PARAM_ALPHANUMEXT));
    $settings->hide_if('mlbackend_python/username', 'mlbackend_python/useserver', 'neq', '1');

    $settings->add(new admin_setting_configtext('mlbackend_python/password', get_string('password', 'mlbackend_python'),
                       get_string('passworddesc', 'mlbackend_python'), '', PARAM_ALPHANUMEXT));
    $settings->hide_if('mlbackend_python/password', 'mlbackend_python/useserver', 'neq', '1');
}