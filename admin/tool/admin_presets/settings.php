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
 * Admin tool presets plugin to load some settings.
 *
 * @package          tool_admin_presets
 * @copyright        2021 Pimenko <support@pimenko.com><pimenko.com>
 * @author           Jordan Kesraoui | Sylvain Revenu | Pimenko based on David Monllaó <david.monllao@urv.cat> code
 * @license          http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {

    $ADMIN->add('root', new admin_externalpage('tool_admin_presets',
    get_string('pluginname', 'tool_admin_presets'),
    new moodle_url('/admin/tool/admin_presets/index.php')));

    $settings = new admin_settingpage('tool_admin_presets_settings', get_string('pluginname', 'tool_admin_presets'));
    $ADMIN->add('tools', $settings);

    $sensiblesettingsdefault = 'recaptchapublickey@@none, recaptchaprivatekey@@none, googlemapkey3@@none, ';
    $sensiblesettingsdefault .= 'secretphrase@@url, cronremotepassword@@none, smtpuser@@none, ';
    $sensiblesettingsdefault .= 'smtppass@none, proxypassword@@none, quizpassword@@quiz, allowedip@@none, blockedip@@none, ';
    $sensiblesettingsdefault .= 'dbpass@@logstore_database, messageinbound_hostpass@@none, ';
    $sensiblesettingsdefault .= 'bind_pw@@auth_cas, pass@@auth_db, bind_pw@@auth_ldap, ';
    $sensiblesettingsdefault .= 'dbpass@@enrol_database, bind_pw@@enrol_ldap, ';
    $sensiblesettingsdefault .= 'server_password@@search_solr, ssl_keypassword@@search_solr, ';
    $sensiblesettingsdefault .= 'alternateserver_password@@search_solr, alternatessl_keypassword@@search_solr, ';
    $sensiblesettingsdefault .= 'test_password@@cachestore_redis, password@@mlbackend_python';

    $settings->add(new admin_setting_configtextarea('tool_admin_presets/sensiblesettings',
             get_string('sensiblesettings', 'tool_admin_presets'),
             get_string('sensiblesettingstext', 'tool_admin_presets'),
             $sensiblesettingsdefault, PARAM_TEXT));

}
