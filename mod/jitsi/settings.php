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
 * Settings for Jitsi instances
 * @package   mod_jitsi
 * @copyright  2019 Sergio Comerón (sergiocomeron@icloud.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

global $DB;

if ($ADMIN->fulltree) {
    require_once($CFG->dirroot.'/mod/jitsi/lib.php');
    $settings->add(new admin_setting_configtext('jitsi_domain', 'Domain', 'Domain Jitsi Server', 'meet.jit.si'));
    $settings->add(new admin_setting_configtext('jitsi_domain', get_string('domain', 'jitsi'),
        get_string('domainex', 'jitsi'), 'meet.jit.si'));
    $settings->add(new admin_setting_confightmleditor('jitsi_help', get_string('help', 'jitsi'),
        get_string('helpex', 'jitsi'), null));
    $options = ['username' => get_string('username', 'jitsi'),
        'nameandsurname' => get_string('nameandsurname', 'jitsi'),
        'alias' => get_string('alias', 'jitsi')];
    $settings->add(new admin_setting_configselect('jitsi_id', get_string('identification', 'jitsi'),
        get_string('identificationex', 'jitsi'), null, $options));
    $sessionoptions = ['Course Shortname', 'Session ID', 'Session Name'];
    $sessionoptionsdefault = [0, 1, 2];

    $optionsseparator = ['.', '-', '_', 'empty'];
    $settings->add(new admin_setting_configselect('jitsi_separator',
        get_string('separator', 'jitsi'), get_string('separatorex', 'jitsi'), '.', $optionsseparator));
    $settings->add(new admin_setting_configmultiselect('jitsi_sesionname',
        get_string('sessionnamefields', 'jitsi'), get_string('sessionnamefieldsex', 'jitsi'),
        $sessionoptionsdefault, $sessionoptions));
    $settings->add(new admin_setting_configcheckbox('jitsi_invitebuttons', get_string('invitebutton', 'jitsi'),
        get_string('invitebuttonex', 'jitsi'), 1));

    $settings->add(new admin_setting_configcheckbox('jitsi_blurbutton', get_string('blurbutton', 'jitsi'),
        get_string('blurbuttonex', 'jitsi'), 1));
    $settings->add(new admin_setting_configcheckbox('jitsi_shareyoutube', get_string('youtubebutton', 'jitsi'),
        get_string('youtubebuttonex', 'jitsi'), 1));
    $settings->add(new admin_setting_configcheckbox('jitsi_finishandreturn', get_string('finishandreturn', 'jitsi'),
        get_string('finishandreturnex', 'jitsi'), 1));
    $settings->add(new admin_setting_configcheckbox('jitsi_deeplink', get_string('deeplink', 'jitsi'),
        get_string('deeplinkex', 'jitsi'), 1));

    $settings->add(new admin_setting_configpasswordunmask('jitsi_password', get_string('password', 'jitsi'),
        get_string('passwordex', 'jitsi'), ''));
    $settings->add(new admin_setting_configcheckbox('jitsi_securitybutton', get_string('securitybutton', 'jitsi'),
        get_string('securitybuttonex', 'jitsi'), 0));

    $settings->add(new admin_setting_configcheckbox('jitsi_showavatars', get_string('showavatars', 'jitsi'),
        get_string('showavatarsex', 'jitsi'), 1));

    $settings->add(new admin_setting_configcheckbox('jitsi_record', get_string('record', 'jitsi'),
        get_string('recordex', 'jitsi'), 1));

    $settings->add(new admin_setting_configcheckbox('jitsi_participantspane', get_string('participantspane', 'jitsi'),
        get_string('participantspaneex', 'jitsi'), 1));

    $settings->add(new admin_setting_configcheckbox('jitsi_raisehand', get_string('raisehand', 'jitsi'),
        get_string('raisehandex', 'jitsi'), 1));

    $settings->add(new admin_setting_configcheckbox('jitsi_reactions', get_string('reactions', 'jitsi'),
        get_string('reactionsex', 'jitsi'), 1));

    $settings->add(new admin_setting_heading('jitsistreaming',
            get_string('streamingconfig', 'jitsi'), get_string('streamingconfigex', 'jitsi')));
    $settings->add(new admin_setting_configcheckbox('jitsi_livebutton', get_string('streamingbutton', 'jitsi'),
            get_string('streamingbuttonex', 'jitsi'), 1));

    $streamingoptions = ['0' => get_string('jitsiinterface', 'jitsi'), '1' => get_string('integrated', 'jitsi')];
    $settings->add(new admin_setting_configselect('jitsi_streamingoption', get_string('streamingoption', 'jitsi'),
        get_string('streamingoptionex', 'jitsi'), null, $streamingoptions));

    $settings->add(new admin_setting_configtext('jitsi_oauth_id', get_string('oauthid', 'jitsi'),
            get_string('oauthidex', 'jitsi', $CFG->wwwroot.'/mod/jitsi/auth.php'), ''));

    $settings->add(new admin_setting_configpasswordunmask('jitsi_oauth_secret', get_string('oauthsecret', 'jitsi'),
            get_string('oauthsecretex', 'jitsi'), ''));

    $link = new moodle_url('/mod/jitsi/adminaccounts.php');
    $settings->add(new admin_setting_heading('jitsi_loginoutyoutube', '', '<a href='.$link.' >'.
    get_string('accounts', 'jitsi').'</a>'));

    $link = new moodle_url('/mod/jitsi/adminrecord.php');
    $settings->add(new admin_setting_heading('jitsi_records_admin', '', '<a href='.$link.' >'.
            get_string('deletesources', 'jitsi').'</a>'));

    // Jitsi Token Section.
    $settings->add(new admin_setting_heading('jitsitoken',
        get_string('tokennconfig', 'jitsi'), get_string('tokenconfigurationex', 'jitsi')));

    $tokenoptions = ['0' => 'Server without token', '1' => 'Self-hosted with appid and secret', '2' => '8x8 servers'];
    $settings->add(new admin_setting_configselect('jitsi/tokentype', 'Server type', null, '0', $tokenoptions));

    // Self-hosted servers with appid and secret.
    $settings->add(new admin_setting_configtext('jitsi_app_id', get_string('appid', 'jitsi'),
        get_string('appidex', 'jitsi'), ''));
    $settings->hide_if('jitsi_app_id', 'jitsi/tokentype', 'in', '2|0');

    $settings->add(new admin_setting_configpasswordunmask('jitsi_secret', get_string('secret', 'jitsi'),
        get_string('secretex', 'jitsi'), ''));
    $settings->hide_if('jitsi_secret', 'jitsi/tokentype', 'in', '2|0');

    // 8x8 servers token configuration
    $settings->add(new admin_setting_configtextarea('jitsi/privatykey', get_string('privatekey', 'jitsi'),
        get_string('privatekeyex', 'jitsi'), '', PARAM_TEXT));
    $settings->hide_if('jitsi/privatykey', 'jitsi/tokentype', 'in', '1|0');

    $settings->add(new admin_setting_configtext('jitsi/8x8app_id', get_string('appid', 'jitsi'),
        get_string('appidex', 'jitsi'), null));
    $settings->hide_if('jitsi/8x8app_id', 'jitsi/tokentype', 'in', '1|0');

    $settings->add(new admin_setting_configtext('jitsi/8x8apikey_id', get_string('apikeyid8x8', 'jitsi'),
        get_string('apikeyid8x8ex', 'jitsi'), null));
    $settings->hide_if('jitsi/8x8apikey_id', 'jitsi/tokentype', 'in', '1|0');


    // Experimental Section.
    $settings->add(new admin_setting_heading('jitsiexperimental', get_string('experimental', 'jitsi'),
        get_string('experimentalex', 'jitsi')));

    $settings->add(new admin_setting_configcheckbox('jitsi_privatesessions', get_string('privatesessions', 'jitsi'),
        get_string('privatesessionsex', 'jitsi'), 0));

    // Deprecated Section.
    $settings->add(new admin_setting_heading('deprecated', get_string('deprecated', 'jitsi'),
        get_string('deprecatedex', 'jitsi')));

    $settings->add(new admin_setting_configtext('jitsi_watermarklink', get_string('watermarklink', 'jitsi'),
        get_string('watermarklinkex', 'jitsi'), 'https://jitsi.org'));

    $settings->add(new admin_setting_configtext('jitsi_channellastcam', get_string('simultaneouscameras', 'jitsi'),
        get_string('simultaneouscamerasex', 'jitsi'), '15', PARAM_INT, 1));
}
