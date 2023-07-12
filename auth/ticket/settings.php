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
 * LDAP enrolment plugin settings and presets.
 *
 * @package    auth_ticket
 * @author      Valery Fremaux <valery.fremaux@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL
 * @copyright   (C) 2010 ValEISTI (http://www.valeisti.fr)
 * @copyright   (C) 2012 onwards Valery Fremaux (http://www.mylearningfactory.com)
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {

    $key = 'auth_ticket/encryption';
    $label = get_string('configencryption', 'auth_ticket');
    $desc = get_string('configencryption_desc', 'auth_ticket');
    $default = 'des';
    $encoptions = array('internal' => get_string('internal', 'auth_ticket'));
    if ($CFG->mnet_dispatcher_mode == 'strict') {
        $encoptions['rsa'] = 'RSA (openssl)';
    }
    if ($CFG->dbtype == 'mysqli' || $CFG->dbtype == 'mariadb') {
        $encoptions['des'] = 'AES/DES (Mysql only)';
    }
    $settings->add(new admin_setting_configselect($key, $label, $desc, $default, $encoptions));

    $key = 'auth_ticket/internalseed';
    $label = get_string('configinternalseed', 'auth_ticket');
    $desc = get_string('configinternalseed_desc', 'auth_ticket');
    $settings->add(new admin_setting_configtext($key, $label, $desc, ''));

    $key = 'auth_ticket/shortvaliditydelay';
    $label = get_string('configshortvaliditydelay', 'auth_ticket');
    $desc = get_string('configshortvaliditydelay_desc', 'auth_ticket');
    $settings->add(new admin_setting_configtext($key, $label, $desc, ''));

    $key = 'auth_ticket/longvaliditydelay';
    $label = get_string('configlongvaliditydelay', 'auth_ticket');
    $desc = get_string('configlongvaliditydelay_desc', 'auth_ticket');
    $settings->add(new admin_setting_configtext($key, $label, $desc, ''));

    $key = 'auth_ticket/persistantvaliditydelay';
    $label = get_string('configpersistantvaliditydelay', 'auth_ticket');
    $desc = get_string('configpersistantvaliditydelay_desc', 'auth_ticket');
    $settings->add(new admin_setting_configtext($key, $label, $desc, ''));

}