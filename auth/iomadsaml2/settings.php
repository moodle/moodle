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
 * Admin config settings page
 *
 * @package    auth_iomadsaml2
 * @copyright  Brendan Heywood <brendan@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use auth_iomadsaml2\admin\iomadsaml2_settings;
use auth_iomadsaml2\admin\setting_button;
use auth_iomadsaml2\admin\setting_textonly;
use auth_iomadsaml2\ssl_algorithms;
use auth_iomadsaml2\user_fields;

defined('MOODLE_INTERNAL') || die;

global $CFG;

if ($ADMIN->fulltree) {
    require_once($CFG->dirroot.'/auth/iomadsaml2/locallib.php');

    // IOMAD
    require_once($CFG->dirroot . '/local/iomad/lib/company.php');
    $postfix = "";
    $companyid = iomad::get_my_companyid(context_system::instance(), false);
    if (!empty($companyid)) {
        $postfix = "_$companyid";
    }


    $yesno = array(
            new lang_string('no'),
            new lang_string('yes'),
    );

    // Introductory explanation.
    $settings->add(new admin_setting_heading('auth_iomadsaml2/pluginname', '',
        new lang_string('auth_iomadsaml2description', 'auth_iomadsaml2')));

    // IDP Metadata.
    $idpmetadata = new \auth_iomadsaml2\admin\setting_idpmetadata($postfix);
    $idpmetadata->set_updatedcallback('auth_iomadsaml2_update_idp_metadata');
    $settings->add($idpmetadata);

    // IDP name.
    $settings->add(new admin_setting_configtext(
            'auth_iomadsaml2/idpname'. $postfix,
            get_string('idpname', 'auth_iomadsaml2'),
            get_string('idpname_help', 'auth_iomadsaml2'),
            get_string('idpnamedefault', 'auth_iomadsaml2'),
            PARAM_TEXT));

    // Manage available IdPs.
    $settings->add(new setting_button(
        'auth_iomadsaml2/availableidps'. $postfix,
        get_string('availableidps', 'auth_iomadsaml2'),
        get_string('availableidps_help', 'auth_iomadsaml2'),
        get_string('availableidps', 'auth_iomadsaml2'),
        $CFG->wwwroot . '/auth/iomadsaml2/availableidps.php'
        ));

    // Display IDP Link.
    $settings->add(new admin_setting_configselect(
            'auth_iomadsaml2/showidplink'. $postfix,
            get_string('showidplink', 'auth_iomadsaml2'),
            get_string('showidplink_help', 'auth_iomadsaml2'),
            1, $yesno));

    // IDP Metadata refresh.
    $settings->add(new admin_setting_configselect(
            'auth_iomadsaml2/idpmetadatarefresh'. $postfix,
            get_string('idpmetadatarefresh', 'auth_iomadsaml2'),
            get_string('idpmetadatarefresh_help', 'auth_iomadsaml2'),
            1, $yesno));

    // Debugging.
    $settings->add(new admin_setting_configselect(
            'auth_iomadsaml2/debug'. $postfix,
            get_string('debug', 'auth_iomadsaml2'),
            get_string('debug_help', 'auth_iomadsaml2', $CFG->wwwroot . '/auth/iomadsaml2/debug.php'),
            0, $yesno));

    // Logging.
    $settings->add(new admin_setting_configselect(
            'auth_iomadsaml2/logtofile'. $postfix,
            get_string('logtofile', 'auth_iomadsaml2'),
            get_string('logtofile_help', 'auth_iomadsaml2'),
            0, $yesno));
    $settings->add(new admin_setting_configtext(
            'auth_iomadsaml2/logdir'. $postfix,
            get_string('logdir', 'auth_iomadsaml2'),
            get_string('logdir_help', 'auth_iomadsaml2'),
            get_string('logdirdefault', 'auth_iomadsaml2'),
            PARAM_TEXT));

    // See section 8.3 from http://docs.oasis-open.org/security/saml/v2.0/saml-core-2.0-os.pdf for more information.
    $nameidlist = [
        'urn:oasis:names:tc:SAML:1.1:nameid-format:unspecified',
        'urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress',
        'urn:oasis:names:tc:SAML:1.1:nameid-format:X509SubjectName',
        'urn:oasis:names:tc:SAML:1.1:nameid-format:WindowsDomainQualifiedName',
        'urn:oasis:names:tc:SAML:2.0:nameid-format:kerberos',
        'urn:oasis:names:tc:SAML:2.0:nameid-format:entity',
        'urn:oasis:names:tc:SAML:2.0:nameid-format:persistent',
        'urn:oasis:names:tc:SAML:2.0:nameid-format:transient',
    ];
    $nameidpolicy = new admin_setting_configselect(
        'auth_iomadsaml2/nameidpolicy'. $postfix,
        get_string('nameidpolicy', 'auth_iomadsaml2'),
        get_string('nameidpolicy_help', 'auth_iomadsaml2'),
        'urn:oasis:names:tc:SAML:1.1:nameid-format:unspecified',
        array_combine($nameidlist, $nameidlist));
    $nameidpolicy->set_updatedcallback('auth_iomadsaml2_update_sp_metadata');
    $settings->add($nameidpolicy);

    // Add NameID as attribute.
    $settings->add(new admin_setting_configselect(
            'auth_iomadsaml2/nameidasattrib'. $postfix,
            get_string('nameidasattrib', 'auth_iomadsaml2'),
            get_string('nameidasattrib_help', 'auth_iomadsaml2'),
            0, $yesno));

    // Lock certificate.
    $settings->add(new setting_button(
            'auth_iomadsaml2/certificatelock'. $postfix,
            get_string('certificatelock', 'auth_iomadsaml2'),
            get_string('certificatelock_help', 'auth_iomadsaml2'),
            get_string('certificatelock', 'auth_iomadsaml2'),
            $CFG->wwwroot . '/auth/iomadsaml2/certificatelock.php'
            ));

    // Regenerate certificate.
    $settings->add(new setting_button(
            'auth_iomadsaml2/certificate'. $postfix,
            get_string('certificate', 'auth_iomadsaml2'),
            get_string('certificate_help', 'auth_iomadsaml2', $CFG->wwwroot . '/auth/iomadsaml2/cert.php'),
            get_string('certificate', 'auth_iomadsaml2'),
            $CFG->wwwroot . '/auth/iomadsaml2/regenerate.php'
            ));

    $settings->add(new admin_setting_configpasswordunmask(
        'auth_iomadsaml2/privatekeypass'. $postfix,
        get_string('privatekeypass', 'auth_iomadsaml2'),
        get_string('privatekeypass_help', 'auth_iomadsaml2'),
        get_site_identifier(),
        PARAM_TEXT));

    // SP Metadata.
    $settings->add(new setting_textonly(
           'auth_iomadsaml2/spmetadata'. $postfix,
           get_string('spmetadata', 'auth_iomadsaml2'),
           get_string('spmetadata_help', 'auth_iomadsaml2', $CFG->wwwroot . '/auth/iomadsaml2/sp/metadata.php')
           ));

    // SP Metadata signature.
    $spmetadatasign = new admin_setting_configselect(
            'auth_iomadsaml2/spmetadatasign'. $postfix,
            get_string('spmetadatasign', 'auth_iomadsaml2'),
            get_string('spmetadatasign_help', 'auth_iomadsaml2'),
            0, $yesno);
    $spmetadatasign->set_updatedcallback('auth_iomadsaml2_update_sp_metadata');
    $settings->add($spmetadatasign);

    $entityid = new admin_setting_configtext(
        'auth_iomadsaml2/spentityid'. $postfix,
        get_string('spentityid', 'auth_iomadsaml2'),
        get_string('spentityid_help', 'auth_iomadsaml2'),
        ''
    );
    $entityid->set_updatedcallback('auth_iomadsaml2_update_sp_metadata');
    $settings->add($entityid);

    $wantassertionssigned = new admin_setting_configselect(
        'auth_iomadsaml2/wantassertionssigned'. $postfix,
        get_string('wantassertionssigned', 'auth_iomadsaml2'),
        get_string('wantassertionssigned_help', 'auth_iomadsaml2'),
        0, $yesno
    );
    $wantassertionssigned->set_updatedcallback('auth_iomadsaml2_update_sp_metadata');
    $settings->add($wantassertionssigned);

    $assertionsconsumerservices = [
        'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST' => 'HTTP Post',
        'urn:oasis:names:tc:SAML:1.0:profiles:browser-post' => 'Browser post profile',
        'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Artifact' => 'HTTP Artifact',
        'urn:oasis:names:tc:SAML:1.0:profiles:artifact-01' => 'Artifact 01 profile',
        'urn:oasis:names:tc:SAML:2.0:profiles:holder-of-key:SSO:browser' => 'Holder-of-Key Web Browser SSO',
    ];

    $acssetting = new admin_setting_configmultiselect(
        'auth_iomadsaml2/assertionsconsumerservices'. $postfix,
        get_string('assertionsconsumerservices', 'auth_iomadsaml2'),
        get_string('assertionsconsumerservices_help', 'auth_iomadsaml2'),
        array(),
        $assertionsconsumerservices
    );
    $acssetting->set_updatedcallback('auth_iomadsaml2_update_sp_metadata');
    $settings->add($acssetting);

    $settings->add(new admin_setting_configselect(
        'auth_iomadsaml2/allowcreate'. $postfix,
        get_string('allowcreate', 'auth_iomadsaml2'),
        get_string('allowcreate_help', 'auth_iomadsaml2'),
        0, $yesno
    ));

    $settings->add(new admin_setting_configtext(
        'auth_iomadsaml2/authncontext'. $postfix,
        get_string('authncontext', 'auth_iomadsaml2'),
        get_string('authncontext_help', 'auth_iomadsaml2'),
        '', PARAM_TEXT
    ));

    $settings->add(new admin_setting_configselect(
        'auth_iomadsaml2/signaturealgorithm'. $postfix,
        get_string('signaturealgorithm', 'auth_iomadsaml2'),
        get_string('signaturealgorithm_help', 'auth_iomadsaml2'),
        ssl_algorithms::get_default_saml_signature_algorithm(),
        ssl_algorithms::get_valid_saml_signature_algorithms()));

    // Dual Login.
    $dualloginoptions = [
        iomadsaml2_settings::OPTION_DUAL_LOGIN_NO      => get_string('no'),
        iomadsaml2_settings::OPTION_DUAL_LOGIN_YES     => get_string('yes'),
        iomadsaml2_settings::OPTION_DUAL_LOGIN_PASSIVE => get_string('passivemode', 'auth_iomadsaml2'),
        iomadsaml2_settings::OPTION_DUAL_LOGIN_TEST    => get_string('test_idp_conn', 'auth_iomadsaml2'),
    ];
    $settings->add(new admin_setting_configselect(
            'auth_iomadsaml2/duallogin'. $postfix,
            get_string('duallogin', 'auth_iomadsaml2'),
            get_string('duallogin_help', 'auth_iomadsaml2'),
            iomadsaml2_settings::OPTION_DUAL_LOGIN_YES,
            $dualloginoptions));

    if (get_config('auth_iomadsaml2', 'duallogin'. $postfix) == iomadsaml2_settings::OPTION_DUAL_LOGIN_TEST) {
        $settings->add(new admin_setting_configtext('auth_iomadsaml2/testendpoint'. $postfix,
            get_string('test_endpoint', 'auth_iomadsaml2'),
            get_string('test_endpoint_desc', 'auth_iomadsaml2'),
            'https://example.com',
            PARAM_URL
        ));
    }

    // Auto login.
    $autologinoptions = [
        iomadsaml2_settings::OPTION_AUTO_LOGIN_NO => get_string('no'),
        iomadsaml2_settings::OPTION_AUTO_LOGIN_SESSION => get_string('autologinbysession', 'auth_iomadsaml2'),
        iomadsaml2_settings::OPTION_AUTO_LOGIN_COOKIE => get_string('autologinbycookie', 'auth_iomadsaml2'),
    ];
    $settings->add(new admin_setting_configselect(
            'auth_iomadsaml2/autologin'. $postfix,
            get_string('autologin', 'auth_iomadsaml2'),
            get_string('autologin_help', 'auth_iomadsaml2'),
            iomadsaml2_settings::OPTION_AUTO_LOGIN_NO,
            $autologinoptions));
    $settings->add(new admin_setting_configtext(
            'auth_iomadsaml2/autologincookie'. $postfix,
            get_string('autologincookie', 'auth_iomadsaml2'),
            get_string('autologincookie_help', 'auth_iomadsaml2'),
            '', PARAM_TEXT));

    // Allow any auth type.
    $settings->add(new admin_setting_configselect(
            'auth_iomadsaml2/anyauth'. $postfix,
            get_string('anyauth', 'auth_iomadsaml2'),
            get_string('anyauth_help', 'auth_iomadsaml2'),
            0, $yesno));

    // Simplify attributes.
    $settings->add(new admin_setting_configselect(
            'auth_iomadsaml2/attrsimple'. $postfix,
            get_string('attrsimple', 'auth_iomadsaml2'),
            get_string('attrsimple_help', 'auth_iomadsaml2'),
            1, $yesno));

    // IDP to Moodle mapping.
    // IDP attribute.
    $settings->add(new admin_setting_configtext(
            'auth_iomadsaml2/idpattr'. $postfix,
            get_string('idpattr', 'auth_iomadsaml2'),
            get_string('idpattr_help', 'auth_iomadsaml2'),
            'uid', PARAM_TEXT));

    // Moodle Field.
    $settings->add(new admin_setting_configselect(
            'auth_iomadsaml2/mdlattr'. $postfix,
            get_string('mdlattr', 'auth_iomadsaml2'),
            get_string('mdlattr_help', 'auth_iomadsaml2'),
            'username', user_fields::get_supported_fields()));

    // Lowercase.
    $toloweroptions = [
        iomadsaml2_settings::OPTION_TOLOWER_EXACT => get_string('tolower:exact', 'auth_iomadsaml2'),
        iomadsaml2_settings::OPTION_TOLOWER_LOWER_CASE => get_string('tolower:lowercase', 'auth_iomadsaml2'),
        iomadsaml2_settings::OPTION_TOLOWER_CASE_INSENSITIVE => get_string('tolower:caseinsensitive', 'auth_iomadsaml2'),
        iomadsaml2_settings::OPTION_TOLOWER_CASE_AND_ACCENT_INSENSITIVE => get_string('tolower:caseandaccentinsensitive', 'auth_iomadsaml2'),
    ];
    $settings->add(new admin_setting_configselect(
            'auth_iomadsaml2/tolower'. $postfix,
            get_string('tolower', 'auth_iomadsaml2'),
            get_string('tolower_help', 'auth_iomadsaml2'),
            iomadsaml2_settings::OPTION_TOLOWER_EXACT,
            $toloweroptions));

    // Requested Attributes.
    $settings->add(new admin_setting_configtextarea(
        'auth_iomadsaml2/requestedattributes'. $postfix,
        get_string('requestedattributes', 'auth_iomadsaml2'),
        get_string('requestedattributes_help', 'auth_iomadsaml2', ['example' => "<pre>
urn:mace:dir:attribute-def:eduPersonPrincipalName
urn:mace:dir:attribute-def:mail *</pre>"]),
        '',
        PARAM_TEXT));

    // Autocreate Users.
    $settings->add(new admin_setting_configselect(
            'auth_iomadsaml2/autocreate'. $postfix,
            get_string('autocreate', 'auth_iomadsaml2'),
            get_string('autocreate_help', 'auth_iomadsaml2'),
            0, $yesno));

    // Group access rules.
    $settings->add(new admin_setting_configtextarea(
        'auth_iomadsaml2/grouprules'. $postfix,
        get_string('grouprules', 'auth_iomadsaml2'),
        get_string('grouprules_help', 'auth_iomadsaml2'),
        '',
        PARAM_TEXT));

    // Alternative Logout URL.
    $settings->add(new admin_setting_configtext(
            'auth_iomadsaml2/alterlogout'. $postfix,
            get_string('alterlogout', 'auth_iomadsaml2'),
            get_string('alterlogout_help', 'auth_iomadsaml2'),
            '',
            PARAM_URL));

    // Multi IdP display type.
    $multiidpdisplayoptions = [
        iomadsaml2_settings::OPTION_MULTI_IDP_DISPLAY_DROPDOWN => get_string('multiidpdropdown', 'auth_iomadsaml2'),
        iomadsaml2_settings::OPTION_MULTI_IDP_DISPLAY_BUTTONS => get_string('multiidpbuttons', 'auth_iomadsaml2')
    ];
    $settings->add(new admin_setting_configselect(
        'auth_iomadsaml2/multiidpdisplay'. $postfix,
        get_string('multiidpdisplay', 'auth_iomadsaml2'),
        get_string('multiidpdisplay_help', 'auth_iomadsaml2'),
        iomadsaml2_settings::OPTION_MULTI_IDP_DISPLAY_DROPDOWN,
        $multiidpdisplayoptions));

    // Attempt Single Sign out.
    $settings->add(new admin_setting_configselect(
        'auth_iomadsaml2/attemptsignout'. $postfix,
        get_string('attemptsignout', 'auth_iomadsaml2'),
        get_string('attemptsignout_help', 'auth_iomadsaml2'),
        1,
        $yesno));

    // SAMLPHP version.
    $authplugin = get_auth_plugin('iomadsaml2');
    $settings->add(new setting_textonly(
            'auth_iomadsaml2/sspversion'. $postfix,
            get_string('sspversion', 'auth_iomadsaml2'),
            $authplugin->get_ssp_version()
            ));


    // Display locking / mapping of profile fields.
    $help = get_string('auth_updatelocal_expl', 'auth');
    $help .= get_string('auth_fieldlock_expl', 'auth');
    $help .= get_string('auth_updateremote_expl', 'auth');

    // User block and redirect feature setting section.
    $settings->add(new admin_setting_heading('auth_iomadsaml2/blockredirectheading', get_string('blockredirectheading', 'auth_iomadsaml2'),
        new lang_string('auth_iomadsaml2blockredirectdescription', 'auth_iomadsaml2')));

    // Flagged login response options.
    $flaggedloginresponseoptions = [
        iomadsaml2_settings::OPTION_FLAGGED_LOGIN_MESSAGE => get_string('flaggedresponsetypemessage', 'auth_iomadsaml2'),
        iomadsaml2_settings::OPTION_FLAGGED_LOGIN_REDIRECT => get_string('flaggedresponsetyperedirect', 'auth_iomadsaml2')
    ];

    // Flagged login response options selector.
    $settings->add(new admin_setting_configselect(
        'auth_iomadsaml2/flagresponsetype'. $postfix,
        get_string('flagresponsetype', 'auth_iomadsaml2'),
        get_string('flagresponsetype_help', 'auth_iomadsaml2'),
        iomadsaml2_settings::OPTION_FLAGGED_LOGIN_REDIRECT,
        $flaggedloginresponseoptions));


    // Set the http OR https fully qualified scheme domain name redirect destination for flagged accounts.
    $settings->add(new admin_setting_configtext(
        'auth_iomadsaml2/flagredirecturl'. $postfix,
        get_string('flagredirecturl', 'auth_iomadsaml2'),
        get_string('flagredirecturl_help', 'auth_iomadsaml2'),
        '',
        PARAM_URL));

    // Set the displayed message for flagged accounts.
    $settings->add(new admin_setting_configtextarea(
        'auth_iomadsaml2/flagmessage'. $postfix,
        get_string('flagmessage', 'auth_iomadsaml2'),
        get_string('flagmessage_help', 'auth_iomadsaml2'),
        get_string('flagmessage_default', 'auth_iomadsaml2'),
        PARAM_TEXT,
        50,
        3));

    if (moodle_major_version() < '3.3') {
        auth_iomadsaml2_display_auth_lock_options($settings, $authplugin->authtype, $authplugin->userfields, $help, true, true,
            $authplugin->get_custom_user_profile_fields(), $postfix);
    } else {
        display_auth_lock_options($settings, $authplugin->authtype, $authplugin->userfields, $help, true, true,
            $authplugin->get_custom_user_profile_fields(), $postfix);
    }

    // The field delimiter to use for multiple value fields from IdP.
    $settings->add(new admin_setting_configtext(
            'auth_iomadsaml2/fielddelimiter' . $postfix,
            get_string('fielddelimiter', 'auth_iomadsaml2'),
            get_string('fielddelimiter_help', 'auth_iomadsaml2'),
            ',',
            PARAM_TEXT,
            5));
}
