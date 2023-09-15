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
 * Adds security related settings links for security category to admin tree.
 *
 * @copyright  1999 Martin Dougiamas  http://dougiamas.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use core_admin\local\settings\filesize;

if ($hassiteconfig) { // speedup for non-admins, add all caps used on this page

    // "ip blocker" settingpage
    $temp = new admin_settingpage('ipblocker', new lang_string('ipblocker', 'admin'));
    $temp->add(new admin_setting_configcheckbox('allowbeforeblock', new lang_string('allowbeforeblock', 'admin'), new lang_string('allowbeforeblockdesc', 'admin'), 0));
    $temp->add(new admin_setting_configiplist('allowedip', new lang_string('allowediplist', 'admin'),
                                                new lang_string('ipblockersyntax', 'admin'), ''));
    $temp->add(new admin_setting_configiplist('blockedip', new lang_string('blockediplist', 'admin'),
                                                new lang_string('ipblockersyntax', 'admin'), ''));
    $ADMIN->add('security', $temp);

    // "sitepolicies" settingpage
    $temp = new admin_settingpage('sitepolicies', new lang_string('sitepolicies', 'admin'));
    $temp->add(new admin_setting_configcheckbox('protectusernames', new lang_string('protectusernames', 'admin'), new lang_string('configprotectusernames', 'admin'), 1));
    $temp->add(new admin_setting_configcheckbox('forcelogin', new lang_string('forcelogin', 'admin'), new lang_string('configforcelogin', 'admin'), 0));
    $temp->add(new admin_setting_configcheckbox('forceloginforprofiles', new lang_string('forceloginforprofiles', 'admin'), new lang_string('configforceloginforprofiles', 'admin'), 1));
    $temp->add(new admin_setting_configcheckbox('forceloginforprofileimage', new lang_string('forceloginforprofileimage', 'admin'), new lang_string('forceloginforprofileimage_help', 'admin'), 0));
    $temp->add(new admin_setting_configcheckbox('opentowebcrawlers', new lang_string('opentowebcrawlers', 'admin'), new lang_string('configopentowebcrawlers', 'admin'), 0));
    $temp->add(new admin_setting_configselect('allowindexing', new lang_string('allowindexing', 'admin'), new lang_string('allowindexing_desc', 'admin'),
        0,
        array(0 => new lang_string('allowindexingexceptlogin', 'admin'),
              1 => new lang_string('allowindexingeverywhere', 'admin'),
              2 => new lang_string('allowindexingnowhere', 'admin'))));
    $temp->add(new admin_setting_pickroles('profileroles',
        new lang_string('profileroles','admin'),
        new lang_string('configprofileroles', 'admin'),
        array('student', 'teacher', 'editingteacher')));

    $maxbytes = 0;
    if (!empty($CFG->maxbytes)) {
        $maxbytes = $CFG->maxbytes;
    }
    $max_upload_choices = get_max_upload_sizes(0, 0, 0, $maxbytes);
    // maxbytes set to 0 will allow the maximum server limit for uploads
    $temp->add(new admin_setting_configselect('maxbytes', new lang_string('maxbytes', 'admin'), new lang_string('configmaxbytes', 'admin'), 0, $max_upload_choices));
    // 100MB
    $defaultuserquota = 100 * filesize::UNIT_MB;
    $temp->add(new filesize('userquota', new lang_string('userquota', 'admin'),
            new lang_string('userquota_desc', 'admin'), $defaultuserquota));

    $temp->add(new admin_setting_configcheckbox('allowobjectembed', new lang_string('allowobjectembed', 'admin'), new lang_string('configallowobjectembed', 'admin'), 0));
    $temp->add(new admin_setting_configcheckbox('enabletrusttext', new lang_string('enabletrusttext', 'admin'), new lang_string('configenabletrusttext', 'admin'), 0));
    $temp->add(new admin_setting_configselect('maxeditingtime', new lang_string('maxeditingtime','admin'), new lang_string('configmaxeditingtime','admin'), 1800,
                 array(60 => new lang_string('numminutes', '', 1),
                       300 => new lang_string('numminutes', '', 5),
                       900 => new lang_string('numminutes', '', 15),
                       1800 => new lang_string('numminutes', '', 30),
                       2700 => new lang_string('numminutes', '', 45),
                       3600 => new lang_string('numminutes', '', 60))));

    $temp->add(new admin_setting_configcheckbox('extendedusernamechars', new lang_string('extendedusernamechars', 'admin'), new lang_string('configextendedusernamechars', 'admin'), 0));

    $temp->add(new admin_setting_configcheckbox('extendedusernamechars', new lang_string('extendedusernamechars', 'admin'), new lang_string('configextendedusernamechars', 'admin'), 0));
    $temp->add(new admin_setting_configcheckbox('keeptagnamecase', new lang_string('keeptagnamecase','admin'),new lang_string('configkeeptagnamecase', 'admin'),'1'));

    $temp->add(new admin_setting_configcheckbox('profilesforenrolledusersonly', new lang_string('profilesforenrolledusersonly','admin'),new lang_string('configprofilesforenrolledusersonly', 'admin'),'1'));

    $temp->add(new admin_setting_configcheckbox('cronclionly', new lang_string('cronclionly', 'admin'), new lang_string
            ('configcronclionly', 'admin'), 1));
    $temp->add(new admin_setting_configpasswordunmask('cronremotepassword', new lang_string('cronremotepassword', 'admin'), new lang_string('configcronremotepassword', 'admin'), ''));
    $temp->add(new admin_setting_configcheckbox('tool_task/enablerunnow', new lang_string('enablerunnow', 'tool_task'),
            new lang_string('enablerunnow_desc', 'tool_task'), 1));

    $options = array(0=>get_string('no'), 3=>3, 5=>5, 7=>7, 10=>10, 20=>20, 30=>30, 50=>50, 100=>100);
    $temp->add(new admin_setting_configselect('lockoutthreshold', new lang_string('lockoutthreshold', 'admin'), new lang_string('lockoutthreshold_desc', 'admin'), 0, $options));
    $temp->add(new admin_setting_configduration('lockoutwindow', new lang_string('lockoutwindow', 'admin'), new lang_string('lockoutwindow_desc', 'admin'), 60*30));
    $temp->add(new admin_setting_configduration('lockoutduration', new lang_string('lockoutduration', 'admin'), new lang_string('lockoutduration_desc', 'admin'), 60*30));

    $temp->add(new admin_setting_configcheckbox('passwordpolicy', new lang_string('passwordpolicy', 'admin'), new lang_string('configpasswordpolicy', 'admin'), 1));
    $temp->add(new admin_setting_configtext('minpasswordlength', new lang_string('minpasswordlength', 'admin'), new lang_string('configminpasswordlength', 'admin'), 8, PARAM_INT));
    $temp->add(new admin_setting_configtext('minpassworddigits', new lang_string('minpassworddigits', 'admin'), new lang_string('configminpassworddigits', 'admin'), 1, PARAM_INT));
    $temp->add(new admin_setting_configtext('minpasswordlower', new lang_string('minpasswordlower', 'admin'), new lang_string('configminpasswordlower', 'admin'), 1, PARAM_INT));
    $temp->add(new admin_setting_configtext('minpasswordupper', new lang_string('minpasswordupper', 'admin'), new lang_string('configminpasswordupper', 'admin'), 1, PARAM_INT));
    $temp->add(new admin_setting_configtext('minpasswordnonalphanum', new lang_string('minpasswordnonalphanum', 'admin'), new lang_string('configminpasswordnonalphanum', 'admin'), 1, PARAM_INT));
    $temp->add(new admin_setting_configtext('maxconsecutiveidentchars', new lang_string('maxconsecutiveidentchars', 'admin'), new lang_string('configmaxconsecutiveidentchars', 'admin'), 0, PARAM_INT));
    $temp->add(new admin_setting_configcheckbox('passwordpolicycheckonlogin',
        new lang_string('passwordpolicycheckonlogin', 'admin'),
        new lang_string('configpasswordpolicycheckonlogin', 'admin'), 0));

    $temp->add(new admin_setting_configtext('passwordreuselimit',
        new lang_string('passwordreuselimit', 'admin'),
        new lang_string('passwordreuselimit_desc', 'admin'), 0, PARAM_INT));

    $pwresetoptions = array(
        300 => new lang_string('numminutes', '', 5),
        900 => new lang_string('numminutes', '', 15),
        1800 => new lang_string('numminutes', '', 30),
        2700 => new lang_string('numminutes', '', 45),
        3600 => new lang_string('numminutes', '', 60),
        7200 => new lang_string('numminutes', '', 120),
        14400 => new lang_string('numminutes', '', 240)
    );
    $adminsetting = new admin_setting_configselect(
            'pwresettime',
            new lang_string('passwordresettime','admin'),
            new lang_string('configpasswordresettime','admin'),
            1800,
            $pwresetoptions);
    $temp->add($adminsetting);
    $temp->add(new admin_setting_configcheckbox('passwordchangelogout',
        new lang_string('passwordchangelogout', 'admin'),
        new lang_string('passwordchangelogout_desc', 'admin'), 1));

    $temp->add(new admin_setting_configcheckbox('passwordchangetokendeletion',
        new lang_string('passwordchangetokendeletion', 'admin'),
        new lang_string('passwordchangetokendeletion_desc', 'admin'), 0));

    $temp->add(new admin_setting_configduration('tokenduration',
        new lang_string('tokenduration', 'admin'),
        new lang_string('tokenduration_desc', 'admin'), 12 * WEEKSECS, WEEKSECS));

    $temp->add(new admin_setting_configcheckbox('groupenrolmentkeypolicy', new lang_string('groupenrolmentkeypolicy', 'admin'), new lang_string('groupenrolmentkeypolicy_desc', 'admin'), 1));
    $temp->add(new admin_setting_configcheckbox('disableuserimages', new lang_string('disableuserimages', 'admin'), new lang_string('configdisableuserimages', 'admin'), 0));
    $temp->add(new admin_setting_configcheckbox('emailchangeconfirmation', new lang_string('emailchangeconfirmation', 'admin'), new lang_string('configemailchangeconfirmation', 'admin'), 1));
    $temp->add(new admin_setting_configselect('rememberusername', new lang_string('rememberusername','admin'), new lang_string('rememberusername_desc','admin'), 2, array(1=>new lang_string('yes'), 0=>new lang_string('no'), 2=>new lang_string('optional'))));
    $temp->add(new admin_setting_configcheckbox('strictformsrequired', new lang_string('strictformsrequired', 'admin'), new lang_string('configstrictformsrequired', 'admin'), 0));

    $temp->add(new admin_setting_heading('adminpresets', new lang_string('siteadminpresetspluginname', 'core_adminpresets'), ''));
    $sensiblesettingsdefault = 'recaptchapublickey@@none, recaptchaprivatekey@@none, googlemapkey3@@none, ';
    $sensiblesettingsdefault .= 'secretphrase@@url, cronremotepassword@@none, smtpuser@@none, ';
    $sensiblesettingsdefault .= 'smtppass@@none, proxypassword@@none, quizpassword@@quiz, allowedip@@none, blockedip@@none, ';
    $sensiblesettingsdefault .= 'dbpass@@logstore_database, messageinbound_hostpass@@none, ';
    $sensiblesettingsdefault .= 'bind_pw@@auth_cas, pass@@auth_db, bind_pw@@auth_ldap, ';
    $sensiblesettingsdefault .= 'dbpass@@enrol_database, bind_pw@@enrol_ldap, ';
    $sensiblesettingsdefault .= 'server_password@@search_solr, ssl_keypassword@@search_solr, ';
    $sensiblesettingsdefault .= 'alternateserver_password@@search_solr, alternatessl_keypassword@@search_solr, ';
    $sensiblesettingsdefault .= 'test_password@@cachestore_redis, password@@mlbackend_python, ';
    $sensiblesettingsdefault .= 'badges_badgesalt@@none, calendar_exportsalt@@none, ';
    $sensiblesettingsdefault .= 'bigbluebuttonbn_shared_secret@@none, apikey@@tiny_premium, ';
    $sensiblesettingsdefault .= 'matrixaccesstoken@@communication_matrix, api_secret@@factor_sms';
    $temp->add(new admin_setting_configtextarea('adminpresets/sensiblesettings',
            get_string('sensiblesettings', 'core_adminpresets'),
            get_string('sensiblesettingstext', 'core_adminpresets'),
            $sensiblesettingsdefault, PARAM_TEXT));

    $ADMIN->add('security', $temp);

    // "httpsecurity" settingpage
    $temp = new admin_settingpage('httpsecurity', new lang_string('httpsecurity', 'admin'));

    $temp->add(new admin_setting_configcheckbox('cookiesecure', new lang_string('cookiesecure', 'admin'), new lang_string('configcookiesecure', 'admin'), 1));
    $temp->add(new admin_setting_configcheckbox('allowframembedding', new lang_string('allowframembedding', 'admin'), new lang_string('allowframembedding_help', 'admin'), 0));

    // Settings elements used by the \core\files\curl_security_helper class.
    $blockedhostsdefault = [
        '127.0.0.0/8',
        '192.168.0.0/16',
        '10.0.0.0/8',
        '172.16.0.0/12',
        '0.0.0.0',
        'localhost',
        '169.254.169.254',
        '0000::1',
    ];
    $allowedportsdefault = ['443', '80'];

    // By default, block various common internal network or cloud provider hosts.
    $temp->add(new admin_setting_configmixedhostiplist('curlsecurityblockedhosts',
        new lang_string('curlsecurityblockedhosts', 'admin'),
        new lang_string('curlsecurityblockedhostssyntax', 'admin'), implode(PHP_EOL, $blockedhostsdefault)));

    // By default, only allow web ports.
    $temp->add(new admin_setting_configportlist('curlsecurityallowedport',
        new lang_string('curlsecurityallowedport', 'admin'),
        new lang_string('curlsecurityallowedportsyntax', 'admin'), implode(PHP_EOL, $allowedportsdefault)));

    // HTTP Header referrer policy settings.
    $referreroptions = [
        'default' => get_string('referrernone', 'admin'),
        'no-referrer' => 'no-referrer',
        'no-referrer-when-downgrade' => 'no-referrer-when-downgrade',
        'origin' => 'origin',
        'origin-when-cross-origin' => 'origin-when-cross-origin',
        'same-origin' => 'same-origin',
        'strict-origin' => 'strict-origin',
        'strict-origin-when-cross-origin' => 'strict-origin-when-cross-origin',
        'unsafe-url' => 'unsafe-url',
    ];
    $temp->add(new admin_setting_configselect('referrerpolicy',
            new lang_string('referrerpolicy', 'admin'),
            new lang_string('referrerpolicydesc', 'admin'), 'default', $referreroptions));

    $ADMIN->add('security', $temp);

    // "notifications" settingpage
    $temp = new admin_settingpage('notifications', new lang_string('notifications', 'admin'));
    $temp->add(new admin_setting_configcheckbox('displayloginfailures', new lang_string('displayloginfailures', 'admin'),
            new lang_string('configdisplayloginfailures', 'admin'), 0));
    $temp->add(new admin_setting_users_with_capability('notifyloginfailures', new lang_string('notifyloginfailures', 'admin'), new lang_string('confignotifyloginfailures', 'admin'), array(), 'moodle/site:config'));
    $options = array();
    for ($i = 1; $i <= 100; $i++) {
        $options[$i] = $i;
    }
    $temp->add(new admin_setting_configselect('notifyloginthreshold', new lang_string('notifyloginthreshold', 'admin'), new lang_string('confignotifyloginthreshold', 'admin'), '10', $options));
    $ADMIN->add('security', $temp);
} // end of speedup
