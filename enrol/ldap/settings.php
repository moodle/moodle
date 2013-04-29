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
 * @package    enrol_ldap
 * @author     Iñaki Arenaza
 * @copyright  2010 Iñaki Arenaza <iarenaza@eps.mondragon.edu>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {

    //--- heading ---
    $settings->add(new admin_setting_heading('enrol_ldap_settings', '', get_string('pluginname_desc', 'enrol_ldap')));

    if (!function_exists('ldap_connect')) {
        $settings->add(new admin_setting_heading('enrol_phpldap_noextension', '', get_string('phpldap_noextension', 'enrol_ldap')));
    } else {
        require_once($CFG->dirroot.'/enrol/ldap/settingslib.php');
        require_once($CFG->libdir.'/ldaplib.php');

        $yesno = array(get_string('no'), get_string('yes'));

        //--- connection settings ---
        $settings->add(new admin_setting_heading('enrol_ldap_server_settings', get_string('server_settings', 'enrol_ldap'), ''));
        $settings->add(new admin_setting_configtext_trim_lower('enrol_ldap/host_url', get_string('host_url_key', 'enrol_ldap'), get_string('host_url', 'enrol_ldap'), ''));
        $settings->add(new admin_setting_configselect('enrol_ldap/start_tls', get_string('start_tls_key', 'auth_ldap'), get_string('start_tls', 'auth_ldap'), 0, $yesno));
        // Set LDAPv3 as the default. Nowadays all the servers support it and it gives us some real benefits.
        $options = array(3=>'3', 2=>'2');
        $settings->add(new admin_setting_configselect('enrol_ldap/ldap_version', get_string('version_key', 'enrol_ldap'), get_string('version', 'enrol_ldap'), 3, $options));
        $settings->add(new admin_setting_configtext_trim_lower('enrol_ldap/ldapencoding', get_string('ldap_encoding_key', 'enrol_ldap'), get_string('ldap_encoding', 'enrol_ldap'), 'utf-8'));
        $settings->add(new admin_setting_configtext_trim_lower('enrol_ldap/pagesize', get_string('pagesize_key', 'auth_ldap'), get_string('pagesize', 'auth_ldap'), LDAP_DEFAULT_PAGESIZE, true));

        //--- binding settings ---
        $settings->add(new admin_setting_heading('enrol_ldap_bind_settings', get_string('bind_settings', 'enrol_ldap'), ''));
        $settings->add(new admin_setting_configtext_trim_lower('enrol_ldap/bind_dn', get_string('bind_dn_key', 'enrol_ldap'), get_string('bind_dn', 'enrol_ldap'), ''));
        $settings->add(new admin_setting_configpasswordunmask('enrol_ldap/bind_pw', get_string('bind_pw_key', 'enrol_ldap'), get_string('bind_pw', 'enrol_ldap'), ''));

        //--- role mapping settings ---
        $settings->add(new admin_setting_heading('enrol_ldap_roles', get_string('roles', 'enrol_ldap'), ''));
        if (!during_initial_install()) {
            $settings->add(new admin_setting_ldap_rolemapping('enrol_ldap/role_mapping', get_string ('role_mapping_key', 'enrol_ldap'), get_string ('role_mapping', 'enrol_ldap'), ''));
        }
        $options = $yesno;
        $settings->add(new admin_setting_configselect('enrol_ldap/course_search_sub', get_string('course_search_sub_key', 'enrol_ldap'), get_string('course_search_sub', 'enrol_ldap'), 0, $options));
        $options = $yesno;
        $settings->add(new admin_setting_configselect('enrol_ldap/memberattribute_isdn', get_string('memberattribute_isdn_key', 'enrol_ldap'), get_string('memberattribute_isdn', 'enrol_ldap'), 0, $options));
        $settings->add(new admin_setting_configtext_trim_lower('enrol_ldap/user_contexts', get_string('user_contexts_key', 'enrol_ldap'), get_string('user_contexts', 'enrol_ldap'), ''));
        $options = $yesno;
        $settings->add(new admin_setting_configselect('enrol_ldap/user_search_sub', get_string('user_search_sub_key', 'enrol_ldap'), get_string('user_search_sub', 'enrol_ldap'), 0, $options));
        $options = ldap_supported_usertypes();
        $settings->add(new admin_setting_configselect('enrol_ldap/user_type', get_string('user_type_key', 'enrol_ldap'), get_string('user_type', 'enrol_ldap'), 'default', $options));
        $options = array();
        $options[LDAP_DEREF_NEVER] = get_string('no');
        $options[LDAP_DEREF_ALWAYS] = get_string('yes');
        $settings->add(new admin_setting_configselect('enrol_ldap/opt_deref', get_string('opt_deref_key', 'enrol_ldap'), get_string('opt_deref', 'enrol_ldap'), 0, $options));
        $settings->add(new admin_setting_configtext_trim_lower('enrol_ldap/idnumber_attribute', get_string('idnumber_attribute_key', 'enrol_ldap'), get_string('idnumber_attribute', 'enrol_ldap'), '', true, true));

        //--- course mapping settings ---
        $settings->add(new admin_setting_heading('enrol_ldap_course_settings', get_string('course_settings', 'enrol_ldap'), ''));
        $settings->add(new admin_setting_configtext_trim_lower('enrol_ldap/objectclass', get_string('objectclass_key', 'enrol_ldap'), get_string('objectclass', 'enrol_ldap'), ''));
        $coursefields = array ('idnumber', 'shortname', 'fullname', 'summary');
        foreach ($coursefields as $field) {
            $settings->add(new admin_setting_configtext_trim_lower('enrol_ldap/course_'.$field, get_string('course_'.$field.'_key', 'enrol_ldap'), get_string('course_'.$field, 'enrol_ldap'), '', true, true));
        }
        $settings->add(new admin_setting_configcheckbox('enrol_ldap/ignorehiddencourses', get_string('ignorehiddencourses', 'enrol_database'), get_string('ignorehiddencourses_desc', 'enrol_database'), 0));
        $options = array(ENROL_EXT_REMOVED_UNENROL        => get_string('extremovedunenrol', 'enrol'),
                         ENROL_EXT_REMOVED_KEEP           => get_string('extremovedkeep', 'enrol'),
                         ENROL_EXT_REMOVED_SUSPEND        => get_string('extremovedsuspend', 'enrol'),
                         ENROL_EXT_REMOVED_SUSPENDNOROLES => get_string('extremovedsuspendnoroles', 'enrol'));
        $settings->add(new admin_setting_configselect('enrol_ldap/unenrolaction', get_string('extremovedaction', 'enrol'), get_string('extremovedaction_help', 'enrol'), ENROL_EXT_REMOVED_UNENROL, $options));

        //--- course creation settings ---
        $settings->add(new admin_setting_heading('enrol_ldap_autocreation_settings', get_string('autocreation_settings', 'enrol_ldap'), ''));
        $options = $yesno;
        $settings->add(new admin_setting_configselect('enrol_ldap/autocreate', get_string('autocreate_key', 'enrol_ldap'), get_string('autocreate', 'enrol_ldap'), 0, $options));
        if (!during_initial_install()) {
            $options = make_categories_options();
            $settings->add(new admin_setting_configselect('enrol_ldap/category', get_string('category_key', 'enrol_ldap'), get_string('category', 'enrol_ldap'), key($options), $options));
        }
        $settings->add(new admin_setting_configtext_trim_lower('enrol_ldap/template', get_string('template_key', 'enrol_ldap'), get_string('template', 'enrol_ldap'), ''));

        //--- nested groups settings ---
        $settings->add(new admin_setting_heading('enrol_ldap_nested_groups_settings', get_string('nested_groups_settings', 'enrol_ldap'), ''));
        $options = $yesno;
        $settings->add(new admin_setting_configselect('enrol_ldap/nested_groups', get_string('nested_groups_key', 'enrol_ldap'), get_string('nested_groups', 'enrol_ldap'), 0, $options));
        $settings->add(new admin_setting_configtext_trim_lower('enrol_ldap/group_memberofattribute', get_string('group_memberofattribute_key', 'enrol_ldap'), get_string('group_memberofattribute', 'enrol_ldap'), '', true, true));
    }
}
