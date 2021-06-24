<?php
// This file is part of the Certificate module for Moodle - http://moodle.org/
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
 * Creates a link to the upload form on the settings page.
 *
 * @package    mod_customcert
 * @copyright  2013 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$url = $CFG->wwwroot . '/mod/customcert/verify_certificate.php';

$ADMIN->add('modsettings', new admin_category('customcert', get_string('pluginname', 'mod_customcert')));
$settings = new admin_settingpage('modsettingcustomcert', new lang_string('customcertsettings', 'mod_customcert'));

$settings->add(new admin_setting_configcheckbox('customcert/verifyallcertificates',
    get_string('verifyallcertificates', 'customcert'),
    get_string('verifyallcertificates_desc', 'customcert', $url),
    0));

$settings->add(new admin_setting_configcheckbox('customcert/showposxy',
    get_string('showposxy', 'customcert'),
    get_string('showposxy_desc', 'customcert'),
    0));

$settings->add(new \mod_customcert\admin_setting_link('customcert/verifycertificate',
    get_string('verifycertificate', 'customcert'), get_string('verifycertificatedesc', 'customcert'),
    get_string('verifycertificate', 'customcert'), new moodle_url('/mod/customcert/verify_certificate.php'), ''));

$settings->add(new \mod_customcert\admin_setting_link('customcert/managetemplates',
    get_string('managetemplates', 'customcert'), get_string('managetemplatesdesc', 'customcert'),
    get_string('managetemplates', 'customcert'), new moodle_url('/mod/customcert/manage_templates.php'), ''));

$settings->add(new \mod_customcert\admin_setting_link('customcert/uploadimage',
    get_string('uploadimage', 'customcert'), get_string('uploadimagedesc', 'customcert'),
    get_string('uploadimage', 'customcert'), new moodle_url('/mod/customcert/upload_image.php'), ''));

$settings->add(new admin_setting_heading('defaults',
    get_string('modeditdefaults', 'admin'), get_string('condifmodeditdefaults', 'admin')));

$yesnooptions = [
    0 => get_string('no'),
    1 => get_string('yes'),
];

$settings->add(new admin_setting_configselect('customcert/emailstudents',
    get_string('emailstudents', 'customcert'), get_string('emailstudents_help', 'customcert'), 0, $yesnooptions));
$settings->add(new admin_setting_configselect('customcert/emailteachers',
    get_string('emailteachers', 'customcert'), get_string('emailteachers_help', 'customcert'), 0, $yesnooptions));
$settings->add(new admin_setting_configtext('customcert/emailothers',
    get_string('emailothers', 'customcert'), get_string('emailothers_help', 'customcert'), '', PARAM_TEXT));
$settings->add(new admin_setting_configselect('customcert/verifyany',
    get_string('verifycertificateanyone', 'customcert'), get_string('verifycertificateanyone_help', 'customcert'),
    0, $yesnooptions));
$settings->add(new admin_setting_configtext('customcert/requiredtime',
    get_string('coursetimereq', 'customcert'), get_string('coursetimereq_help', 'customcert'), 0, PARAM_INT));
$settings->add(new admin_setting_configcheckbox('customcert/protection_print',
    get_string('preventprint', 'customcert'),
    get_string('preventprint_desc', 'customcert'),
    0));
$settings->add(new admin_setting_configcheckbox('customcert/protection_modify',
    get_string('preventmodify', 'customcert'),
    get_string('preventmodify_desc', 'customcert'),
    0));
$settings->add(new admin_setting_configcheckbox('customcert/protection_copy',
    get_string('preventcopy', 'customcert'),
    get_string('preventcopy_desc', 'customcert'),
    0));

$ADMIN->add('customcert', $settings);

// Element plugin settings.
$ADMIN->add('customcert', new admin_category('customcertelements', get_string('elementplugins', 'customcert')));
$plugins = \core_plugin_manager::instance()->get_plugins_of_type('customcertelement');
foreach ($plugins as $plugin) {
    $plugin->load_settings($ADMIN, 'customcertelements', $hassiteconfig);
}

// Tell core we already added the settings structure.
$settings = null;
