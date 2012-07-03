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

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    require_once($CFG->dirroot . '/mod/scorm/locallib.php');
    $yesno = array(0 => get_string('no'),
                   1 => get_string('yes'));

    //default display settings
    $settings->add(new admin_setting_heading('scorm/displaysettings', get_string('defaultdisplaysettings', 'scorm'), ''));

    $settings->add(new admin_setting_configselect_with_advanced('scorm/displaycoursestructure',
        get_string('displaycoursestructure', 'scorm'), get_string('displaycoursestructuredesc', 'scorm'),
        array('value' => 0, 'adv' => false), $yesno));

    $settings->add(new admin_setting_configselect_with_advanced('scorm/popup',
        get_string('display', 'scorm'), get_string('displaydesc', 'scorm'),
        array('value' => 0, 'adv' => false), scorm_get_popup_display_array()));

    $settings->add(new admin_setting_configtext_with_advanced('scorm/framewidth',
        get_string('width', 'scorm'), get_string('framewidth', 'scorm'),
        array('value' => '100', 'adv' => true)));

    $settings->add(new admin_setting_configtext_with_advanced('scorm/frameheight',
        get_string('height', 'scorm'), get_string('frameheight', 'scorm'),
        array('value' => '500', 'adv' => true)));

    $settings->add(new admin_setting_configcheckbox('scorm/winoptgrp_adv',
         get_string('optionsadv', 'scorm'), get_string('optionsadv_desc', 'scorm'), 1));

    foreach (scorm_get_popup_options_array() as $key => $value) {
        $settings->add(new admin_setting_configcheckbox('scorm/'.$key,
            get_string($key, 'scorm'), '', $value));
    }

    $settings->add(new admin_setting_configselect_with_advanced('scorm/skipview',
        get_string('skipview', 'scorm'), get_string('skipviewdesc', 'scorm'),
        array('value' => 0, 'adv' => true), scorm_get_skip_view_array()));

    $settings->add(new admin_setting_configselect_with_advanced('scorm/hidebrowse',
        get_string('hidebrowse', 'scorm'), get_string('hidebrowsedesc', 'scorm'),
        array('value' => 0, 'adv' => true), $yesno));

    $settings->add(new admin_setting_configselect_with_advanced('scorm/hidetoc',
        get_string('hidetoc', 'scorm'), get_string('hidetocdesc', 'scorm'),
        array('value' => 0, 'adv' => true), scorm_get_hidetoc_array()));

    $settings->add(new admin_setting_configselect_with_advanced('scorm/hidenav',
        get_string('hidenav', 'scorm'), get_string('hidenavdesc', 'scorm'),
        array('value' => 0, 'adv' => false), $yesno));


    //default grade settings
    $settings->add(new admin_setting_heading('scorm/gradesettings', get_string('defaultgradesettings', 'scorm'), ''));
    $settings->add(new admin_setting_configselect_with_advanced('scorm/grademethod',
        get_string('grademethod', 'scorm'), get_string('grademethoddesc', 'scorm'),
        array('value' => GRADEHIGHEST, 'adv' => false), scorm_get_grade_method_array()));

    for ($i=0; $i<=100; $i++) {
        $grades[$i] = "$i";
    }

    $settings->add(new admin_setting_configselect_with_advanced('scorm/maxgrade',
        get_string('maximumgrade'), get_string('maximumgradedesc', 'scorm'),
        array('value' => 100, 'adv' => false), $grades));

    $settings->add(new admin_setting_heading('scorm/othersettings', get_string('defaultothersettings', 'scorm'), ''));

    //default attempts settings.
    $settings->add(new admin_setting_configselect_with_advanced('scorm/maxattempt',
        get_string('maximumattempts', 'scorm'), '',
        array('value' => '0', 'adv' => false), scorm_get_attempts_array()));

    $settings->add(new admin_setting_configselect_with_advanced('scorm/whatgrade',
        get_string('whatgrade', 'scorm'), get_string('whatgradedesc', 'scorm'),
        array('value' => HIGHESTATTEMPT, 'adv' => false), scorm_get_what_grade_array()));

    $settings->add(new admin_setting_configselect_with_advanced('scorm/displayattemptstatus',
        get_string('displayattemptstatus', 'scorm'), get_string('displayattemptstatusdesc', 'scorm'),
        array('value' => 1, 'adv' => false), $yesno));

    $settings->add(new admin_setting_configselect_with_advanced('scorm/forcecompleted',
        get_string('forcecompleted', 'scorm'), get_string('forcecompleteddesc', 'scorm'),
        array('value' => 0, 'adv' => true), $yesno));

    $settings->add(new admin_setting_configselect_with_advanced('scorm/forcenewattempt',
        get_string('forcenewattempt', 'scorm'), get_string('forcenewattemptdesc', 'scorm'),
        array('value' => 0, 'adv' => true), $yesno));

    $settings->add(new admin_setting_configselect_with_advanced('scorm/lastattemptlock',
        get_string('lastattemptlock', 'scorm'), get_string('lastattemptlockdesc', 'scorm'),
        array('value' => 0, 'adv' => true), $yesno));

    $settings->add(new admin_setting_configselect_with_advanced('scorm/auto',
        get_string('autocontinue', 'scorm'), get_string('autocontinuedesc', 'scorm'),
        array('value' => 0, 'adv' => true), $yesno));

    $settings->add(new admin_setting_configselect_with_advanced('scorm/updatefreq',
        get_string('updatefreq', 'scorm'), get_string('updatefreqdesc', 'scorm'),
        array('value' => 0, 'adv' => true), scorm_get_updatefreq_array()));

    //admin level settings.
    $settings->add(new admin_setting_heading('scorm/adminsettings', get_string('adminsettings', 'scorm'), ''));

    $settings->add(new admin_setting_configcheckbox('scorm/allowtypeexternal', get_string('allowtypeexternal', 'scorm'), '', 0));

    $settings->add(new admin_setting_configcheckbox('scorm/allowtypelocalsync', get_string('allowtypelocalsync', 'scorm'), '', 0));

    $settings->add(new admin_setting_configcheckbox('scorm/allowtypeimsrepository', get_string('allowtypeimsrepository', 'scorm'), '', 0));

    $settings->add(new admin_setting_configcheckbox('scorm/allowtypeexternalaicc', get_string('allowtypeexternalaicc', 'scorm'), get_string('allowtypeexternalaicc_desc', 'scorm'), 0));

    $settings->add(new admin_setting_configcheckbox('scorm/allowaicchacp', get_string('allowtypeaicchacp', 'scorm'), get_string('allowtypeaicchacp_desc', 'scorm'), 0));

    $settings->add(new admin_setting_configtext('scorm/aicchacptimeout',
        get_string('aicchacptimeout', 'scorm'), get_string('aicchacptimeout_desc', 'scorm'),
        30, PARAM_INT));

    $settings->add(new admin_setting_configtext('scorm/aicchacpkeepsessiondata',
        get_string('aicchacpkeepsessiondata', 'scorm'), get_string('aicchacpkeepsessiondata_desc', 'scorm'),
        1, PARAM_INT));

    $settings->add(new admin_setting_configcheckbox('scorm/forcejavascript', get_string('forcejavascript', 'scorm'), get_string('forcejavascript_desc', 'scorm'), 1));

    $settings->add(new admin_setting_configcheckbox('scorm/allowapidebug', get_string('allowapidebug', 'scorm'), '', 0));

    $settings->add(new admin_setting_configtext('scorm/apidebugmask', get_string('apidebugmask', 'scorm'), '', '.*'));

}
