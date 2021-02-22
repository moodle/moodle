<?php
// This file is part of the Zoom plugin for Moodle - http://moodle.org/
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
 * Settings.
 *
 * @package    mod_zoom
 * @copyright  2015 UC Regents
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/mod/zoom/locallib.php');

if ($ADMIN->fulltree) {
    require_once($CFG->dirroot.'/mod/zoom/locallib.php');
    require_once($CFG->dirroot.'/mod/zoom/classes/webservice.php');

    $settings = new admin_settingpage('modsettingzoom', get_string('pluginname', 'mod_zoom'));

    // Test whether connection works and display result to user.
    if (!CLI_SCRIPT && $PAGE->url == $CFG->wwwroot . '/' . $CFG->admin . '/settings.php?section=modsettingzoom') {
        $status = 'connectionok';
        $notifyclass = 'notifysuccess';
        $errormessage = '';
        try {
            $service = new mod_zoom_webservice();
            $service->get_user($USER->email);
        } catch (moodle_exception $error) {
            $notifyclass = 'notifyproblem';
            $status = 'connectionfailed';
            $errormessage = $error->a;
        }
        $statusmessage = $OUTPUT->notification(get_string('connectionstatus', 'zoom') .
                ': ' . get_string($status, 'zoom') . $errormessage, $notifyclass);
        $connectionstatus = new admin_setting_heading('mod_zoom/connectionstatus', $statusmessage, '');
        $settings->add($connectionstatus);
    }

    $apikey = new admin_setting_configtext('mod_zoom/apikey', get_string('apikey', 'mod_zoom'),
            get_string('apikey_desc', 'mod_zoom'), '', PARAM_ALPHANUMEXT);
    $settings->add($apikey);

    $apisecret = new admin_setting_configpasswordunmask('mod_zoom/apisecret', get_string('apisecret', 'mod_zoom'),
            get_string('apisecret_desc', 'mod_zoom'), '');
    $settings->add($apisecret);

    $zoomurl = new admin_setting_configtext('mod_zoom/zoomurl', get_string('zoomurl', 'mod_zoom'),
            get_string('zoomurl_desc', 'mod_zoom'), '', PARAM_URL);
    $settings->add($zoomurl);

    $jointimechoices = array(0, 5, 10, 15, 20, 30, 45, 60);
    $jointimeselect = array();
    foreach ($jointimechoices as $minutes) {
        $jointimeselect[$minutes] = $minutes . ' ' . get_string('mins');
    }
    $firstabletojoin = new admin_setting_configselect('mod_zoom/firstabletojoin',
            get_string('firstjoin', 'mod_zoom'), get_string('firstjoin_desc', 'mod_zoom'),
            15, $jointimeselect);
    $settings->add($firstabletojoin);

    $licensescount = new admin_setting_configtext('mod_zoom/licensesnumber',
            get_string('licensesnumber', 'mod_zoom'),
            null, 0, PARAM_INT);
    $settings->add($licensescount);

    $utmost = new admin_setting_configcheckbox('mod_zoom/utmost',
            get_string('redefinelicenses', 'mod_zoom'),
            get_string('lowlicenses', 'mod_zoom'), 0, 1);
    $settings->add($utmost);

    $recycleonjoin = new admin_setting_configcheckbox('mod_zoom/recycleonjoin',
            get_string('recycleonjoin', 'mod_zoom'),
            get_string('licenseonjoin', 'mod_zoom'), 0, 1);
    $settings->add($recycleonjoin);

    $maskparticipantdata = new admin_setting_configcheckbox('mod_zoom/maskparticipantdata',
            get_string('maskparticipantdata', 'mod_zoom'),
            get_string('maskparticipantdata_help', 'mod_zoom'), 0, 1);
    $settings->add($maskparticipantdata);

    // Default Zoom settings.
    $settings->add(new admin_setting_heading('defaultsettings',
            get_string('defaultsettings', 'mod_zoom'),
            get_string('defaultsettings_help', 'mod_zoom')));

    $defaultrecurring = new admin_setting_configcheckbox('mod_zoom/defaultrecurring',
            get_string('recurringmeeting', 'zoom'),
            get_string('recurringmeeting_help', 'zoom'), 0, 1, 0);
    $settings->add($defaultrecurring);

    $defaultrequirepasscode = new admin_setting_configcheckbox('zoom/requirepasscode',
            get_string('requirepasscode', 'zoom'),
            get_string('requirepasscode_help', 'zoom'),
            1);
    $defaultrequirepasscode->set_locked_flag_options(admin_setting_flag::ENABLED, true);
    $settings->add($defaultrequirepasscode);

    $defaulthostvideo = new admin_setting_configcheckbox('mod_zoom/defaulthostvideo',
            get_string('option_host_video', 'zoom'),
            '', 0, 1, 0);
    $settings->add($defaulthostvideo);

    $defaultparticipantsvideo = new admin_setting_configcheckbox('mod_zoom/defaultparticipantsvideo',
            get_string('option_participants_video', 'zoom'), '', 0, 1, 0);
    $settings->add($defaultparticipantsvideo);

    $audiochoices = array(ZOOM_AUDIO_TELEPHONY => get_string('audio_telephony', 'zoom'),
                          ZOOM_AUDIO_VOIP => get_string('audio_voip', 'zoom'),
                          ZOOM_AUDIO_BOTH => get_string('audio_both', 'zoom'));
    $defaultaudiooption = new admin_setting_configselect('mod_zoom/defaultaudiooption',
            get_string('option_audio', 'zoom'),
            '', ZOOM_AUDIO_BOTH, $audiochoices);
    $settings->add($defaultaudiooption);

    $defaultjoinbeforehost = new admin_setting_configcheckbox('mod_zoom/defaultjoinbeforehost',
            get_string('option_jbh', 'zoom'),
            '', 0, 1, 0);
    $settings->add($defaultjoinbeforehost);

    $proxyhost = new admin_setting_configtext('mod_zoom/proxyhost',
            get_string('option_proxyhost', 'zoom'),
            get_string('option_proxyhost_desc', 'mod_zoom'), '', '/^[a-zA-Z0-9.-]+:[0-9]+$|^$/');
    $settings->add($proxyhost);

    $defaultwaitingroomoption = new admin_setting_configcheckbox('mod_zoom/defaultwaitingroomoption',
            get_string('option_waiting_room', 'mod_zoom'),
            '', 1, 1, 0);
    $settings->add($defaultwaitingroomoption);

    $defaultauthusersoption = new admin_setting_configcheckbox('mod_zoom/defaultauthusersoption',
            get_string('option_authenticated_users', 'mod_zoom'),
            '', 0, 1, 0);
    $settings->add($defaultauthusersoption);

    $defaultmuteuponentryoption = new admin_setting_configcheckbox('mod_zoom/defaultmuteuponentryoption',
            get_string('option_mute_upon_entry', 'mod_zoom'),
            '', 1, 1, 0);
    $settings->add($defaultmuteuponentryoption);

    $displaypassword = new admin_setting_configcheckbox('mod_zoom/displaypassword',
        get_string('displaypassword', 'zoom'),
        get_string('displaypassword_help', 'zoom'), 0, 1, 0);
    $settings->add($displaypassword);
}
