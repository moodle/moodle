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
 * Attendance plugin settings
 *
 * @package    mod_attendance
 * @copyright  2013 Netspot, Tim Lock.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    require_once(dirname(__FILE__).'/lib.php');
    require_once(dirname(__FILE__).'/locallib.php');
    require_once($CFG->dirroot . '/user/profile/lib.php');

    $tabmenu = attendance_print_settings_tabs();

    $settings->add(new admin_setting_heading('attendance_header', '', $tabmenu));

    $plugininfos = core_plugin_manager::instance()->get_plugins_of_type('local');

    // Paging options.
    $options = array(
          0 => get_string('donotusepaging', 'attendance'),
         25 => 25,
         50 => 50,
         75 => 75,
         100 => 100,
         250 => 250,
         500 => 500,
         1000 => 1000,
    );

    $settings->add(new admin_setting_configselect('attendance/resultsperpage',
        get_string('resultsperpage', 'attendance'), get_string('resultsperpage_desc', 'attendance'), 25, $options));

    $settings->add(new admin_setting_configcheckbox('attendance/studentscanmark',
        get_string('studentscanmark', 'attendance'), get_string('studentscanmark_desc', 'attendance'), 1));

    $settings->add(new admin_setting_configtext('attendance/rotateqrcodeinterval',
        get_string('rotateqrcodeinterval', 'attendance'),
        get_string('rotateqrcodeinterval_desc', 'attendance'), '15', PARAM_INT));

    $settings->add(new admin_setting_configtext('attendance/rotateqrcodeexpirymargin',
            get_string('rotateqrcodeexpirymargin', 'attendance'),
            get_string('rotateqrcodeexpirymargin_desc', 'attendance'), '2', PARAM_INT));

    $settings->add(new admin_setting_configcheckbox('attendance/studentscanmarksessiontime',
        get_string('studentscanmarksessiontime', 'attendance'),
        get_string('studentscanmarksessiontime_desc', 'attendance'), 1));

    $settings->add(new admin_setting_configtext('attendance/studentscanmarksessiontimeend',
        get_string('studentscanmarksessiontimeend', 'attendance'),
        get_string('studentscanmarksessiontimeend_desc', 'attendance'), '60', PARAM_INT));

    $settings->add(new admin_setting_configcheckbox('attendance/subnetactivitylevel',
        get_string('subnetactivitylevel', 'attendance'),
        get_string('subnetactivitylevel_desc', 'attendance'), 1));

    $options = array(
        ATT_VIEW_ALL => get_string('all', 'attendance'),
        ATT_VIEW_ALLPAST => get_string('allpast', 'attendance'),
        ATT_VIEW_NOTPRESENT => get_string('below', 'attendance', 'X'),
        ATT_VIEW_MONTHS => get_string('months', 'attendance'),
        ATT_VIEW_WEEKS => get_string('weeks', 'attendance'),
        ATT_VIEW_DAYS => get_string('days', 'attendance')
    );

    $settings->add(new admin_setting_configselect('attendance/defaultview',
        get_string('defaultview', 'attendance'),
            get_string('defaultview_desc', 'attendance'), ATT_VIEW_WEEKS, $options));

    $settings->add(new admin_setting_configcheckbox('attendance/multisessionexpanded',
        get_string('multisessionexpanded', 'attendance'),
        get_string('multisessionexpanded_desc', 'attendance'), 0));

    $settings->add(new admin_setting_configcheckbox('attendance/showsessiondescriptiononreport',
        get_string('showsessiondescriptiononreport', 'attendance'),
        get_string('showsessiondescriptiononreport_desc', 'attendance'), 0));

    $settings->add(new admin_setting_configcheckbox('attendance/studentrecordingexpanded',
        get_string('studentrecordingexpanded', 'attendance'),
        get_string('studentrecordingexpanded_desc', 'attendance'), 1));

    $settings->add(new admin_setting_configcheckbox('attendance/enablecalendar',
        get_string('enablecalendar', 'attendance'),
        get_string('enablecalendar_desc', 'attendance'), 1));

    $settings->add(new admin_setting_configcheckbox('attendance/enablewarnings',
        get_string('enablewarnings', 'attendance'),
        get_string('enablewarnings_desc', 'attendance'), 0));

    $fields = array('id' => get_string('studentid', 'attendance'));
    $customfields = profile_get_custom_fields();
    foreach ($customfields as $field) {
        $fields[$field->shortname] = format_string($field->name);
    }

    $settings->add(new admin_setting_configmultiselect('attendance/customexportfields',
            new lang_string('customexportfields', 'attendance'),
            new lang_string('customexportfields_help', 'attendance'),
            array('id'), $fields)
    );

    $name = new lang_string('mobilesettings', 'mod_attendance');
    $description = new lang_string('mobilesettings_help', 'mod_attendance');
    $settings->add(new admin_setting_heading('mobilesettings', $name, $description));

    $settings->add(new admin_setting_configduration('attendance/mobilesessionfrom',
        get_string('mobilesessionfrom', 'attendance'), get_string('mobilesessionfrom_help', 'attendance'),
         6 * HOURSECS, PARAM_RAW));

    $settings->add(new admin_setting_configduration('attendance/mobilesessionto',
        get_string('mobilesessionto', 'attendance'), get_string('mobilesessionto_help', 'attendance'),
        24 * HOURSECS, PARAM_RAW));

    $name = new lang_string('defaultsettings', 'mod_attendance');
    $description = new lang_string('defaultsettings_help', 'mod_attendance');
    $settings->add(new admin_setting_heading('defaultsettings', $name, $description));

    $settings->add(new admin_setting_configtext('attendance/subnet',
        get_string('requiresubnet', 'attendance'), get_string('requiresubnet_help', 'attendance'), '', PARAM_RAW));

    $name = new lang_string('defaultsessionsettings', 'mod_attendance');
    $description = new lang_string('defaultsessionsettings_help', 'mod_attendance');
    $settings->add(new admin_setting_heading('defaultsessionsettings', $name, $description));

    $settings->add(new admin_setting_configcheckbox('attendance/calendarevent_default',
        get_string('calendarevent', 'attendance'), '', 1));

    $settings->add(new admin_setting_configcheckbox('attendance/absenteereport_default',
        get_string('includeabsentee', 'attendance'), '', 1));

    $settings->add(new admin_setting_configcheckbox('attendance/studentscanmark_default',
        get_string('studentscanmark', 'attendance'), '', 0));

    $options = attendance_get_automarkoptions();

    $settings->add(new admin_setting_configselect('attendance/automark_default',
        get_string('automark', 'attendance'), '', 0, $options));

    $settings->add(new admin_setting_configcheckbox('attendance/randompassword_default',
        get_string('randompassword', 'attendance'), '', 0));

    $settings->add(new admin_setting_configcheckbox('attendance/includeqrcode_default',
        get_string('includeqrcode', 'attendance'), '', 0));

    $settings->add(new admin_setting_configcheckbox('attendance/rotateqrcode_default',
        get_string('rotateqrcode', 'attendance'), '', 0));

    $settings->add(new admin_setting_configcheckbox('attendance/autoassignstatus',
        get_string('autoassignstatus', 'attendance'), '', 0));

    $options = attendance_get_sharedipoptions();
    $settings->add(new admin_setting_configselect('attendance/preventsharedip',
        get_string('preventsharedip', 'attendance'),
        '', ATTENDANCE_SHAREDIP_DISABLED, $options));

    $settings->add(new admin_setting_configtext('attendance/preventsharediptime',
        get_string('preventsharediptime', 'attendance'), get_string('preventsharediptime_help', 'attendance'), '', PARAM_RAW));

    $name = new lang_string('defaultwarningsettings', 'mod_attendance');
    $description = new lang_string('defaultwarningsettings_help', 'mod_attendance');
    $settings->add(new admin_setting_heading('defaultwarningsettings', $name, $description));

    $options = array();
    for ($i = 1; $i <= 100; $i++) {
        $options[$i] = "$i%";
    }
    $settings->add(new admin_setting_configselect('attendance/warningpercent',
        get_string('warningpercent', 'attendance'), get_string('warningpercent_help', 'attendance'), 70, $options));

    $options = array();
    for ($i = 1; $i <= 50; $i++) {
        $options[$i] = "$i";
    }
    $settings->add(new admin_setting_configselect('attendance/warnafter',
        get_string('warnafter', 'attendance'), get_string('warnafter_help', 'attendance'), 5, $options));

    $settings->add(new admin_setting_configselect('attendance/maxwarn',
        get_string('maxwarn', 'attendance'), get_string('maxwarn_help', 'attendance'), 1, $options));

    $settings->add(new admin_setting_configcheckbox('attendance/emailuser',
        get_string('emailuser', 'attendance'), get_string('emailuser_help', 'attendance'), 1));

    $settings->add(new admin_setting_configtext('attendance/emailsubject',
        get_string('emailsubject', 'attendance'), get_string('emailsubject_help', 'attendance'),
        get_string('emailsubject_default', 'attendance'), PARAM_RAW));


    $settings->add(new admin_setting_configtextarea('attendance/emailcontent',
        get_string('emailcontent', 'attendance'), get_string('emailcontent_help', 'attendance'),
        get_string('emailcontent_default', 'attendance'), PARAM_RAW));
}
