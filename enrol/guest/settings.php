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
 * Guest access plugin settings and presets.
 *
 * @package    enrol
 * @subpackage guest
 * @copyright  2010 Petr Skoda  {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {

    //--- general settings -----------------------------------------------------------------------------------
    $settings->add(new admin_setting_heading('enrol_guest_settings', '', get_string('pluginname_desc', 'enrol_guest')));

    $settings->add(new admin_setting_configcheckbox('enrol_guest/requirepassword',
        get_string('requirepassword', 'enrol_guest'), get_string('requirepassword_desc', 'enrol_guest'), 0));

    $settings->add(new admin_setting_configcheckbox('enrol_guest/usepasswordpolicy',
        get_string('usepasswordpolicy', 'enrol_guest'), get_string('usepasswordpolicy_desc', 'enrol_guest'), 0));

    $settings->add(new admin_setting_configcheckbox('enrol_guest/showhint',
        get_string('showhint', 'enrol_guest'), get_string('showhint_desc', 'enrol_guest'), 0));


    //--- enrol instance defaults ----------------------------------------------------------------------------
    $settings->add(new admin_setting_heading('enrol_guest_defaults',
        get_string('enrolinstancedefaults', 'admin'), get_string('enrolinstancedefaults_desc', 'admin')));

    $settings->add(new admin_setting_configcheckbox('enrol_guest/defaultenrol',
        get_string('defaultenrol', 'enrol'), get_string('defaultenrol_desc', 'enrol'), 1));

    $options = array(ENROL_INSTANCE_ENABLED  => get_string('yes'),
                     ENROL_INSTANCE_DISABLED => get_string('no'));
    $settings->add(new admin_setting_configselect_with_advanced('enrol_guest/status',
        get_string('status', 'enrol_guest'), get_string('status_desc', 'enrol_guest'),
        array('value'=>ENROL_INSTANCE_DISABLED, 'adv'=>false), $options));
}

