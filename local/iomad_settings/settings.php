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
 * @package   local_iomad_settings
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($hassiteconfig) {

    // Basic navigation settings
    require($CFG->dirroot . '/local/iomad/lib/basicsettings.php');

    $settings = new admin_settingpage('local_iomad_settings', get_string('pluginname', 'local_iomad_settings'));
    $ADMIN->add('localplugins', $settings);

    $settings->add(new admin_setting_configcheckbox('iomad_use_email_as_username',
                                                get_string('iomad_use_email_as_username', 'local_iomad_settings'),
                                                get_string('iomad_use_email_as_username_help', 'local_iomad_settings'),
                                                0));

    $settings->add(new admin_setting_configcheckbox('iomad_allow_username',
                                                get_string('iomad_allow_username', 'local_iomad_settings'),
                                                get_string('iomad_allow_username_help', 'local_iomad_settings'),
                                                0));

    $settings->add(new admin_setting_configcheckbox('iomad_sync_institution',
                                                get_string('iomad_sync_institution', 'local_iomad_settings'),
                                                get_string('iomad_sync_institution_help', 'local_iomad_settings'),
                                                1));

    $settings->add(new admin_setting_configcheckbox('iomad_sync_department',
                                                get_string('iomad_sync_department', 'local_iomad_settings'),
                                                get_string('iomad_sync_department', 'local_iomad_settings'),
                                                1));

    $settings->add(new admin_setting_configcheckbox('iomad_autoenrol_managers',
                                                get_string('iomad_autoenrol_managers', 'local_iomad_settings'),
                                                get_string('iomad_autoenrol_managers', 'local_iomad_settings'),
                                                1));

    $settings->add(new admin_setting_configcheckbox('iomad_autoreallocate_licenses',
                                                get_string('iomad_autoreallocate_licenses', 'local_iomad_settings'),
                                                get_string('iomad_autoreallocate_licenses', 'local_iomad_settings'),
                                                0));

    $settings->add(new admin_setting_configcheckbox('iomad_hidevalidcourses',
                                                get_string('iomad_hidevalidcourses', 'local_iomad_settings'),
                                                get_string('iomad_hidevalidcourses', 'local_iomad_settings'),
                                                0));

    $settings->add(new admin_setting_configcheckbox('iomad_showcharts',
                                                get_string('iomad_showcharts', 'local_iomad_settings'),
                                                get_string('iomad_showcharts', 'local_iomad_settings'),
                                                1));

    $settings->add(new admin_setting_configtext('iomad_emaildelay',
                                                get_string('emaildelay', 'local_iomad_settings'),
                                                get_string('emaildelay_help', 'local_iomad_settings'),
                                                0,
                                                PARAM_INT));

    $dateformats = array('Y-m-d' => 'YYYY-MM-DD',
                         'Y/m/d' => 'YYYY/MM/DD',
                         'Y.m.d' => 'YYYY.MM.DD',
                         'Y-d-m' => 'YYYY-DD-MM',
                         'Y/d/m' => 'YYYY/DD/MM',
                         'Y.d.m' => 'YYYY.DD.MM',
                         'd-m-Y' => 'DD-MM-YYYY',
                         'd/m/Y' => 'DD/MM/YYYY',
                         'd.m.Y' => 'DD.MM.YYYY',
                         'm-d-Y' => 'MM-DD-YYYY',
                         'm/d/Y' => 'MM/DD/YYYY',
                         'm.d.Y' => 'MM.DD.YYYY',
                         'jS \of F Y' => 'nth of Month YYYY',
                         'F d, y, ' => 'Month n, YYYY',
                         'jS \of F Y' => 'nth of Mon YYYY',
                         'M d, y, ' => 'Mon n, YYYY');
    $settings->add(new admin_setting_configselect('iomad_date_format', get_string('dateformat', 'local_iomad_settings'), '', 'Y-m-d', $dateformats));

    $settings->add(new admin_setting_configtext('iomad_report_fields',
                                                get_string('iomad_report_fields', 'local_iomad_settings'),
                                                get_string('iomad_report_fields_help', 'local_iomad_settings'),
                                                '',
                                                PARAM_TEXT));

    $settings->add(new admin_setting_configtext('iomad_report_grade_places',
                                                get_string('iomad_report_grade_places', 'local_iomad_settings'),
                                                get_string('iomad_report_grade_places_help', 'local_iomad_settings'),
                                                0,
                                                PARAM_INT));

    $settings->add(new admin_setting_configtext('iomad_max_list_users',
                                                get_string('iomad_max_list_users', 'local_iomad_settings'),
                                                get_string('iomad_max_list_users_help', 'local_iomad_settings'),
                                                30,
                                                PARAM_INT));

    $settings->add(new admin_setting_configtext('iomad_max_list_courses',
                                                get_string('iomad_max_list_courses', 'local_iomad_settings'),
                                                get_string('iomad_max_list_courses_help', 'local_iomad_settings'),
                                                30,
                                                PARAM_INT));

    $settings->add(new admin_setting_configtext('iomad_max_list_templates',
                                                get_string('iomad_max_list_templates', 'local_iomad_settings'),
                                                get_string('iomad_max_list_templates_help', 'local_iomad_settings'),
                                                30,
                                                PARAM_INT));

    $settings->add(new admin_setting_configtext('iomad_max_list_companies',
                                                get_string('iomad_max_list_companies', 'local_iomad_settings'),
                                                get_string('iomad_max_list_companies_help', 'local_iomad_settings'),
                                                30,
                                                PARAM_INT));

    $settings->add(new admin_setting_configtext('iomad_max_list_licenses',
                                                get_string('iomad_max_list_licenses', 'local_iomad_settings'),
                                                get_string('iomad_max_list_licenses_help', 'local_iomad_settings'),
                                                30,
                                                PARAM_INT));

    $settings->add(new admin_setting_configtext('iomad_max_list_classrooms',
                                                get_string('iomad_max_list_classrooms', 'local_iomad_settings'),
                                                get_string('iomad_max_list_classrooms_help', 'local_iomad_settings'),
                                                30,
                                                PARAM_INT));

    $settings->add(new admin_setting_configtext('iomad_max_list_email_templates',
                                                get_string('iomad_max_list_email_templates', 'local_iomad_settings'),
                                                get_string('iomad_max_list_email_templates_help', 'local_iomad_settings'),
                                                30,
                                                PARAM_INT));

    $settings->add(new admin_setting_configtext('iomad_max_list_competencies',
                                                get_string('iomad_max_list_competencies', 'local_iomad_settings'),
                                                get_string('iomad_max_list_competencies_help', 'local_iomad_settings'),
                                                30,
                                                PARAM_INT));

    $settings->add(new admin_setting_configtext('iomad_max_list_frameworks',
                                                get_string('iomad_max_list_frameworks', 'local_iomad_settings'),
                                                get_string('iomad_max_list_frameworks_help', 'local_iomad_settings'),
                                                30,
                                                PARAM_INT));

    $settings->add(new admin_setting_configtext('iomad_max_select_users',
                                                get_string('iomad_max_select_users', 'local_iomad_settings'),
                                                get_string('iomad_max_select_users_help', 'local_iomad_settings'),
                                                100,
                                                PARAM_INT));

    $settings->add(new admin_setting_configtext('iomad_max_select_courses',
                                                get_string('iomad_max_select_courses', 'local_iomad_settings'),
                                                get_string('iomad_max_select_courses_help', 'local_iomad_settings'),
                                                200,
                                                PARAM_INT));

    $settings->add(new admin_setting_configtext('iomad_max_select_templates',
                                                get_string('iomad_max_select_templates', 'local_iomad_settings'),
                                                get_string('iomad_max_select_templates_help', 'local_iomad_settings'),
                                                200,
                                                PARAM_INT));

    $settings->add(new admin_setting_configtext('iomad_max_select_frameworks',
                                                get_string('iomad_max_select_frameworks', 'local_iomad_settings'),
                                                get_string('iomad_max_select_frameworks_help', 'local_iomad_settings'),
                                                200,
                                                PARAM_INT));

    $name = 'local_iomad_settings/iomadcertificate_logo';
    $title = get_string('iomadcertificate_logo', 'local_iomad_settings');
    $description = get_string('iomadcertificate_logodesc', 'local_iomad_settings');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'iomadcertificate_logo', 0,
    array('maxfiles' => 1, 'accepted_types' => array('image')));
    $settings->add($setting);

    $name = 'local_iomad_settings/iomadcertificate_signature';
    $title = get_string('iomadcertificate_signature', 'local_iomad_settings');
    $description = get_string('iomadcertificate_signaturedesc', 'local_iomad_settings');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'iomadcertificate_signature', 0,
    array('maxfiles' => 1, 'accepted_types' => array('image')));
    $settings->add($setting);

    $name = 'local_iomad_settings/iomadcertificate_border';
    $title = get_string('iomadcertificate_border', 'local_iomad_settings');
    $description = get_string('iomadcertificate_borderdesc', 'local_iomad_settings');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'iomadcertificate_border', 0,
    array('maxfiles' => 1, 'accepted_types' => array('image')));
    $settings->add($setting);

    $name = 'local_iomad_settings/iomadcertificate_watermark';
    $title = get_string('iomadcertificate_watermark', 'local_iomad_settings');
    $description = get_string('iomadcertificate_watermarkdesc', 'local_iomad_settings');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'iomadcertificate_watermark', 0,
    array('maxfiles' => 1, 'accepted_types' => array('image')));
    $settings->add($setting);

}

