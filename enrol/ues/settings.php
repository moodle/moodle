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
 *
 * @package    enrol_ues
 * @copyright  2008 onwards Louisiana State University
 * @copyright  2008 onwards Philip Cali, Adam Zapletal, Chad Mazilly, Robert Russo, Dave Elliott
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {
    require_once(dirname(__FILE__) . '/publiclib.php');

    $plugins = ues::list_plugins();

    $s = ues::gen_str();

    $settings->add(new admin_setting_heading('enrol_ues_settings', '',
        $s('pluginname_desc')));

    // Scheduled Task Status.
    $settings->add(new admin_setting_heading('enrol_ues_task_status',
        $s('task_status'), ues::get_task_status_description()));

    // Add the username and password fields into Moodle so we can drop the credentials server.
    $settings->add(new admin_setting_configtext('enrol_ues/username',
        $s('uesusername'), $s('uesusername_desc'), 'username'));
    $settings->add(new admin_setting_configpasswordunmask('enrol_ues/password',
        $s('uespassword'), $s('uespassword_desc'), 'password'));

    // Internal Links.
    $urls = new stdClass;
    $urls->cleanup_url = $CFG->wwwroot . '/enrol/ues/cleanup.php';
    $urls->failure_url = $CFG->wwwroot . '/enrol/ues/failures.php';
    $urls->ignore_url = $CFG->wwwroot . '/enrol/ues/ignore.php';
    $urls->adhoc_url = $CFG->wwwroot . '/enrol/ues/adhoc.php';

    $settings->add(new admin_setting_heading('enrol_ues_internal_links',
        $s('management'), $s('management_links', $urls)));

    // General Settings.
    $settings->add(new admin_setting_heading('enrol_ues_general_settings',
        $s('general_settings'), ''));

    if (!empty($plugins)) {
        $settings->add(new admin_setting_configselect('enrol_ues/enrollment_provider',
            $s('provider'), $s('provider_desc'), key($plugins), $plugins));
    }

    $settings->add(new admin_setting_configcheckbox('enrol_ues/process_by_department',
        $s('process_by_department'), $s('process_by_department_desc'), 1));

    $settings->add(new admin_setting_configcheckbox('enrol_ues/running',
        $s('running'), $s('running_desc'), 0));

    $settings->add(new admin_setting_configtext('enrol_ues/grace_period',
        $s('grace_period'), $s('grace_period_desc'), 3600));

    $settings->add(new admin_setting_configtext('enrol_ues/sub_days',
        $s('sub_days'), $s('sub_days_desc'), 60));

    $settings->add(new admin_setting_configtext('enrol_ues/error_threshold',
        $s('error_threshold'), $s('error_threshold_desc'), 100));

    $settings->add(new admin_setting_configcheckbox('enrol_ues/email_report',
        $s('email_report'), $s('email_report_desc'), 1));

    // User Creation Settings.
    $settings->add(new admin_setting_heading('enrol_ues_user_settings',
        $s('user_settings'), ''));

    $ueopts = array('un' => $s('un'), 'em' => $s('em'));

    $settings->add(new admin_setting_configselect('enrol_ues/username_email',
        $s('use_username_email'), $s('use_username_email_desc'), 'username', $ueopts));

    $settings->add(new admin_setting_configtext('enrol_ues/user_email',
        $s('user_email'), $s('user_email_desc'), '@example.com'));

    $settings->add(new admin_setting_configtext('enrol_ues/user_email_cleanse',
        $s('user_email_cleanse'), $s('user_email_cleanse_desc'), 'lsumail'));

    $settings->add(new admin_setting_configcheckbox('enrol_ues/user_confirm',
        $s('user_confirm'), $s('user_confirm_desc'), 1));

    $languages = get_string_manager()->get_list_of_translations();
    $settings->add(new admin_setting_configselect('enrol_ues/user_lang',
        get_string('language'), '', $CFG->lang, $languages));

    $auths = core_component::get_plugin_list('auth');
    $authoptions = array();
    foreach ($auths as $auth => $unused) {
        $authoptions[$auth] = get_string('pluginname', "auth_{$auth}");
    }

    $settings->add(new admin_setting_configselect('enrol_ues/user_auth',
        $s('user_auth'), $s('user_auth_desc'), 'manual', $authoptions));

    $settings->add(new admin_setting_configtext('enrol_ues/user_city',
        $s('user_city'), $s('user_city_desc'), ''));

    $countries = get_string_manager()->get_list_of_countries();
    $settings->add(new admin_setting_configselect('enrol_ues/user_country',
        $s('user_country'), $s('user_country_desc'), $CFG->country, $countries));

    // Course Creation Settings.
    $settings->add(new admin_setting_heading('enrol_ues_course_settings',
        $s('course_settings'), ''));

    $settings->add(new admin_setting_configtext('enrol_ues/course_fullname',
        get_string('fullname'), '', $s('course_shortname')));

    $settings->add(new admin_setting_configtext('enrol_ues/course_shortname',
        get_string('shortname'), $s('course_shortname_desc'),
        $s('course_shortname')));

    $settings->add(new admin_setting_configcheckbox('enrol_ues/course_form_replace',
        $s('course_form_replace'), $s('course_form_replace_desc'), 0));

    $fields = array(
        'newsitems' => get_string('newsitemsnumber'),
        'showgrades' => get_string('showgrades'),
        'showreports' => get_string('showreports'),
        'maxbytes' => get_string('maximumupload'),
        'groupmode' => get_string('groupmode'),
        'groupmodeforce' => get_string('groupmodeforce'),
        'lang' => get_string('forcelanguage')
    );

    $defaults = array('groupmode', 'groupmodeforce');

    $settings->add(new admin_setting_configmultiselect('enrol_ues/course_restricted_fields',
        $s('course_restricted_fields'), $s('course_restricted_fields_desc'),
        $defaults, $fields));

    // User Enrollment Settings.
    $settings->add(new admin_setting_heading('enrol_ues_enrol_settings',
        $s('enrol_settings'), ''));

    $roles = role_get_names(null, null, true);

    foreach (array('editingteacher', 'teacher', 'student') as $shortname) {
        $typeid = $DB->get_field('role', 'id', array('shortname' => $shortname));
        $settings->add(new admin_setting_configselect('enrol_ues/'.$shortname.'_role',
            $s($shortname.'_role'), $s($shortname.'_role_desc'), $typeid, $roles));
    }

    $settings->add(new admin_setting_configcheckbox('enrol_ues/recover_grades',
        $s('recover_grades'), $s('recover_grades_desc'), 1));

    $settings->add(new admin_setting_configcheckbox('enrol_ues/suspend_enrollment',
        $s('suspend_enrollment'), $s('suspend_enrollment_desc'), 0));

    // Specific Provider Settings.
    $provider = ues::provider_class();

    if ($provider) {
        try {
            // Attempting to create the provider.
            $testprovider = new $provider();

            $testprovider->settings($settings);

            $works = (
                $testprovider->supports_section_lookups() or
                $testprovider->supports_department_lookups()
            );

            if ($works === false) {
                throw new Exception('enrollment_unsupported');
            }

            $a = new stdClass;
            $a->name = $testprovider->get_name();
            $a->list = '';

            if ($testprovider->supports_department_lookups()) {
                $a->list .= '<li>' . ues::_s('process_by_department') . '</li>';
            }

            if ($testprovider->supports_section_lookups()) {
                $a->list .= '<li>' . ues::_s('process_by_section') . '</li>';
            }

            if ($testprovider->supports_reverse_lookups()) {
                $a->list .= '<li>' . ues::_s('reverse_lookups') . '</li>';
            }

            $settings->add(new admin_setting_heading('provider_information',
                $s('provider_information'), $s('provider_information_desc', $a)));
        } catch (Exception $e) {
            $a = ues::translate_error($e);

            $settings->add(new admin_setting_heading('provider_problem',
                $s('provider_problems'), $s('provider_problems_desc', $a)));
        }
    }
}
