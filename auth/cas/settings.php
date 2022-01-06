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
 * Admin settings and defaults.
 *
 * @package auth_cas
 * @copyright  2017 Stephen Bourget
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {

    if (!function_exists('ldap_connect')) {
        $notify = new \core\output\notification(get_string('auth_casnotinstalled', 'auth_cas'),
            \core\output\notification::NOTIFY_WARNING);
        $settings->add(new admin_setting_heading('auth_casnotinstalled', '', $OUTPUT->render($notify)));
    } else {
        // We use a couple of custom admin settings since we need to massage the data before it is inserted into the DB.
        require_once($CFG->dirroot.'/auth/ldap/classes/admin_setting_special_lowercase_configtext.php');
        require_once($CFG->dirroot.'/auth/ldap/classes/admin_setting_special_contexts_configtext.php');

        // Include needed files.
        require_once($CFG->dirroot.'/auth/cas/auth.php');
        require_once($CFG->dirroot.'/auth/cas/languages.php');

        // Introductory explanation.
        $settings->add(new admin_setting_heading('auth_cas/pluginname', '',
                new lang_string('auth_casdescription', 'auth_cas')));

        // CAS server configuration label.
        $settings->add(new admin_setting_heading('auth_cas/casserversettings',
                new lang_string('auth_cas_server_settings', 'auth_cas'), ''));

        // Authentication method name.
        $settings->add(new admin_setting_configtext('auth_cas/auth_name',
                get_string('auth_cas_auth_name', 'auth_cas'),
                get_string('auth_cas_auth_name_description', 'auth_cas'),
                get_string('auth_cas_auth_service', 'auth_cas'),
                PARAM_RAW_TRIMMED));

        // Authentication method logo.
        $opts = array('accepted_types' => array('.png', '.jpg', '.gif', '.webp', '.tiff', '.svg'));
        $settings->add(new admin_setting_configstoredfile('auth_cas/auth_logo',
                 get_string('auth_cas_auth_logo', 'auth_cas'),
                 get_string('auth_cas_auth_logo_description', 'auth_cas'), 'logo', 0, $opts));


        // Hostname.
        $settings->add(new admin_setting_configtext('auth_cas/hostname',
                get_string('auth_cas_hostname_key', 'auth_cas'),
                get_string('auth_cas_hostname', 'auth_cas'), '', PARAM_RAW_TRIMMED));

        // Base URI.
        $settings->add(new admin_setting_configtext('auth_cas/baseuri',
                get_string('auth_cas_baseuri_key', 'auth_cas'),
                get_string('auth_cas_baseuri', 'auth_cas'), '', PARAM_RAW_TRIMMED));

        // Port.
        $settings->add(new admin_setting_configtext('auth_cas/port',
                get_string('auth_cas_port_key', 'auth_cas'),
                get_string('auth_cas_port', 'auth_cas'), '', PARAM_INT));

        // CAS Version.
        $casversions = array();
        $casversions[CAS_VERSION_1_0] = 'CAS 1.0';
        $casversions[CAS_VERSION_2_0] = 'CAS 2.0';
        $settings->add(new admin_setting_configselect('auth_cas/casversion',
                new lang_string('auth_cas_casversion', 'auth_cas'),
                new lang_string('auth_cas_version', 'auth_cas'), CAS_VERSION_2_0, $casversions));

        // Language.
        if (!isset($CASLANGUAGES) || empty($CASLANGUAGES)) {
            // Prevent warnings on other admin pages.
            // $CASLANGUAGES is defined in /auth/cas/languages.php.
            $CASLANGUAGES = array();
            $CASLANGUAGES[PHPCAS_LANG_ENGLISH] = 'English';
            $CASLANGUAGES[PHPCAS_LANG_FRENCH] = 'French';
        }
        $settings->add(new admin_setting_configselect('auth_cas/language',
                new lang_string('auth_cas_language_key', 'auth_cas'),
                new lang_string('auth_cas_language', 'auth_cas'), PHPCAS_LANG_ENGLISH, $CASLANGUAGES));

        // Proxy.
        $yesno = array(
            new lang_string('no'),
            new lang_string('yes'),
        );
        $settings->add(new admin_setting_configselect('auth_cas/proxycas',
                new lang_string('auth_cas_proxycas_key', 'auth_cas'),
                new lang_string('auth_cas_proxycas', 'auth_cas'), 0 , $yesno));

        // Logout option.
        $settings->add(new admin_setting_configselect('auth_cas/logoutcas',
                new lang_string('auth_cas_logoutcas_key', 'auth_cas'),
                new lang_string('auth_cas_logoutcas', 'auth_cas'), 0 , $yesno));

        // Multi-auth.
        $settings->add(new admin_setting_configselect('auth_cas/multiauth',
                new lang_string('auth_cas_multiauth_key', 'auth_cas'),
                new lang_string('auth_cas_multiauth', 'auth_cas'), 0 , $yesno));

        // Server validation.
        $settings->add(new admin_setting_configselect('auth_cas/certificate_check',
                new lang_string('auth_cas_certificate_check_key', 'auth_cas'),
                new lang_string('auth_cas_certificate_check', 'auth_cas'), 0 , $yesno));

        // Certificate path.
        $settings->add(new admin_setting_configfile('auth_cas/certificate_path',
                get_string('auth_cas_certificate_path_key', 'auth_cas'),
                get_string('auth_cas_certificate_path', 'auth_cas'), ''));

        // CURL SSL version.
        $sslversions = array();
        $sslversions[''] = get_string('auth_cas_curl_ssl_version_default', 'auth_cas');
        if (defined('CURL_SSLVERSION_TLSv1')) {
            $sslversions[CURL_SSLVERSION_TLSv1] = get_string('auth_cas_curl_ssl_version_TLSv1x', 'auth_cas');
        }
        if (defined('CURL_SSLVERSION_TLSv1_0')) {
            $sslversions[CURL_SSLVERSION_TLSv1_0] = get_string('auth_cas_curl_ssl_version_TLSv10', 'auth_cas');
        }
        if (defined('CURL_SSLVERSION_TLSv1_1')) {
            $sslversions[CURL_SSLVERSION_TLSv1_1] = get_string('auth_cas_curl_ssl_version_TLSv11', 'auth_cas');
        }
        if (defined('CURL_SSLVERSION_TLSv1_2')) {
            $sslversions[CURL_SSLVERSION_TLSv1_2] = get_string('auth_cas_curl_ssl_version_TLSv12', 'auth_cas');
        }
        if (defined('CURL_SSLVERSION_SSLv2')) {
            $sslversions[CURL_SSLVERSION_SSLv2] = get_string('auth_cas_curl_ssl_version_SSLv2', 'auth_cas');
        }
        if (defined('CURL_SSLVERSION_SSLv3')) {
            $sslversions[CURL_SSLVERSION_SSLv3] = get_string('auth_cas_curl_ssl_version_SSLv3', 'auth_cas');
        }
        $settings->add(new admin_setting_configselect('auth_cas/curl_ssl_version',
                new lang_string('auth_cas_curl_ssl_version_key', 'auth_cas'),
                new lang_string('auth_cas_curl_ssl_version', 'auth_cas'), '' , $sslversions));

        // Alt Logout URL.
        $settings->add(new admin_setting_configtext('auth_cas/logout_return_url',
                get_string('auth_cas_logout_return_url_key', 'auth_cas'),
                get_string('auth_cas_logout_return_url', 'auth_cas'), '', PARAM_URL));

        // LDAP server settings.
        $settings->add(new admin_setting_heading('auth_cas/ldapserversettings',
                new lang_string('auth_ldap_server_settings', 'auth_ldap'), ''));

        // Host.
        $settings->add(new admin_setting_configtext('auth_cas/host_url',
                get_string('auth_ldap_host_url_key', 'auth_ldap'),
                get_string('auth_ldap_host_url', 'auth_ldap'), '', PARAM_RAW_TRIMMED));

        // Version.
        $versions = array();
        $versions[2] = '2';
        $versions[3] = '3';
        $settings->add(new admin_setting_configselect('auth_cas/ldap_version',
                new lang_string('auth_ldap_version_key', 'auth_ldap'),
                new lang_string('auth_ldap_version', 'auth_ldap'), 3, $versions));

        // Start TLS.
        $settings->add(new admin_setting_configselect('auth_cas/start_tls',
                new lang_string('start_tls_key', 'auth_ldap'),
                new lang_string('start_tls', 'auth_ldap'), 0 , $yesno));


        // Encoding.
        $settings->add(new admin_setting_configtext('auth_cas/ldapencoding',
                get_string('auth_ldap_ldap_encoding_key', 'auth_ldap'),
                get_string('auth_ldap_ldap_encoding', 'auth_ldap'), 'utf-8', PARAM_RAW_TRIMMED));

        // Page Size. (Hide if not available).
        $settings->add(new admin_setting_configtext('auth_cas/pagesize',
                get_string('pagesize_key', 'auth_ldap'),
                get_string('pagesize', 'auth_ldap'), '250', PARAM_INT));

        // Bind settings.
        $settings->add(new admin_setting_heading('auth_cas/ldapbindsettings',
                new lang_string('auth_ldap_bind_settings', 'auth_ldap'), ''));

        // User ID.
        $settings->add(new admin_setting_configtext('auth_cas/bind_dn',
                get_string('auth_ldap_bind_dn_key', 'auth_ldap'),
                get_string('auth_ldap_bind_dn', 'auth_ldap'), '', PARAM_RAW_TRIMMED));

        // Password.
        $settings->add(new admin_setting_configpasswordunmask('auth_cas/bind_pw',
                get_string('auth_ldap_bind_pw_key', 'auth_ldap'),
                get_string('auth_ldap_bind_pw', 'auth_ldap'), ''));

        // User Lookup settings.
        $settings->add(new admin_setting_heading('auth_cas/ldapuserlookup',
                new lang_string('auth_ldap_user_settings', 'auth_ldap'), ''));

        // User Type.
        $settings->add(new admin_setting_configselect('auth_cas/user_type',
                new lang_string('auth_ldap_user_type_key', 'auth_ldap'),
                new lang_string('auth_ldap_user_type', 'auth_ldap'), 'default', ldap_supported_usertypes()));

        // Contexts.
        $settings->add(new auth_ldap_admin_setting_special_contexts_configtext('auth_cas/contexts',
                get_string('auth_ldap_contexts_key', 'auth_ldap'),
                get_string('auth_ldap_contexts', 'auth_ldap'), '', PARAM_RAW_TRIMMED));

        // Search subcontexts.
        $settings->add(new admin_setting_configselect('auth_cas/search_sub',
                new lang_string('auth_ldap_search_sub_key', 'auth_ldap'),
                new lang_string('auth_ldap_search_sub', 'auth_ldap'), 0 , $yesno));

        // Dereference aliases.
        $optderef = array();
        $optderef[LDAP_DEREF_NEVER] = get_string('no');
        $optderef[LDAP_DEREF_ALWAYS] = get_string('yes');

        $settings->add(new admin_setting_configselect('auth_cas/opt_deref',
                new lang_string('auth_ldap_opt_deref_key', 'auth_ldap'),
                new lang_string('auth_ldap_opt_deref', 'auth_ldap'), LDAP_DEREF_NEVER , $optderef));

        // User attribute.
        $settings->add(new auth_ldap_admin_setting_special_lowercase_configtext('auth_cas/user_attribute',
                get_string('auth_ldap_user_attribute_key', 'auth_ldap'),
                get_string('auth_ldap_user_attribute', 'auth_ldap'), '', PARAM_RAW));

        // Member attribute.
        $settings->add(new auth_ldap_admin_setting_special_lowercase_configtext('auth_cas/memberattribute',
                get_string('auth_ldap_memberattribute_key', 'auth_ldap'),
                get_string('auth_ldap_memberattribute', 'auth_ldap'), '', PARAM_RAW));

        // Member attribute uses dn.
        $settings->add(new admin_setting_configselect('auth_cas/memberattribute_isdn',
                get_string('auth_ldap_memberattribute_isdn_key', 'auth_ldap'),
                get_string('auth_ldap_memberattribute_isdn', 'auth_ldap'), 0, $yesno));

        // Object class.
        $settings->add(new admin_setting_configtext('auth_cas/objectclass',
                get_string('auth_ldap_objectclass_key', 'auth_ldap'),
                get_string('auth_ldap_objectclass', 'auth_ldap'), '', PARAM_RAW_TRIMMED));

        // Course Creators Header.
        $settings->add(new admin_setting_heading('auth_cas/coursecreators',
                new lang_string('coursecreators'), ''));

        // Course creators attribute field mapping.
        $settings->add(new admin_setting_configtext('auth_cas/attrcreators',
                get_string('auth_ldap_attrcreators_key', 'auth_ldap'),
                get_string('auth_ldap_attrcreators', 'auth_ldap'), '', PARAM_RAW_TRIMMED));

        // Course creator group field mapping.
        $settings->add(new admin_setting_configtext('auth_cas/groupecreators',
                get_string('auth_ldap_groupecreators_key', 'auth_ldap'),
                get_string('auth_ldap_groupecreators', 'auth_ldap'), '', PARAM_RAW_TRIMMED));

        // User Account Sync.
        $settings->add(new admin_setting_heading('auth_cas/syncusers',
                new lang_string('auth_sync_script', 'auth'), ''));

        // Remove external user.
        $deleteopt = array();
        $deleteopt[AUTH_REMOVEUSER_KEEP] = get_string('auth_remove_keep', 'auth');
        $deleteopt[AUTH_REMOVEUSER_SUSPEND] = get_string('auth_remove_suspend', 'auth');
        $deleteopt[AUTH_REMOVEUSER_FULLDELETE] = get_string('auth_remove_delete', 'auth');

        $settings->add(new admin_setting_configselect('auth_cas/removeuser',
                new lang_string('auth_remove_user_key', 'auth'),
                new lang_string('auth_remove_user', 'auth'), AUTH_REMOVEUSER_KEEP, $deleteopt));
    }

    // Display locking / mapping of profile fields.
    $authplugin = get_auth_plugin('cas');
    $help  = get_string('auth_ldapextrafields', 'auth_ldap');
    $help .= get_string('auth_updatelocal_expl', 'auth');
    $help .= get_string('auth_fieldlock_expl', 'auth');
    $help .= get_string('auth_updateremote_expl', 'auth');
    $help .= '<hr />';
    $help .= get_string('auth_updateremote_ldap', 'auth');
    display_auth_lock_options($settings, $authplugin->authtype, $authplugin->userfields, $help, true, true,
            $authplugin->get_custom_user_profile_fields());

}
