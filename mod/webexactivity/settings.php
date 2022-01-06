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
 * An activity to interface with WebEx.
 *
 * @package    mod_webexactvity
 * @author     Eric Merrill <merrill@oakland.edu>
 * @copyright  2014 Oakland University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Create a settings page object to add to.
$settings = new admin_settingpage($section, get_string('settings', 'mod_webexactivity'), 'moodle/site:config',
        $module->is_enabled() === false);

// Build up the full settings page if we need it.
if ($ADMIN->fulltree) {
    // ---------------------------------------------------
    // API Settings.
    // ---------------------------------------------------
    $settings->add(new admin_setting_heading('apisettings', get_string('apisettings', 'mod_webexactivity'), ''));

    $settings->add(new admin_setting_configtext('webexactivity/sitename', get_string('sitename', 'mod_webexactivity'),
            get_string('sitename_help', 'mod_webexactivity'), ''));

    $settings->add(new admin_setting_configtext('webexactivity/apiusername', get_string('apiusername', 'mod_webexactivity'),
            get_string('apiusername_help', 'mod_webexactivity'), ''));

    $settings->add(new admin_setting_configpasswordunmask('webexactivity/apipassword',
            get_string('apipassword', 'mod_webexactivity'), get_string('apipassword_help', 'mod_webexactivity'), ''));

    $settings->add(new admin_setting_configtext('webexactivity/prefix', get_string('prefix', 'mod_webexactivity'),
            get_string('prefix_help', 'mod_webexactivity'), ''));

    // ---------------------------------------------------
    // Meeting Types.
    // ---------------------------------------------------
    $settings->add(new admin_setting_heading('meetingtypes', get_string('meetingtypes', 'mod_webexactivity'),
            get_string('meetingtypes_desc', 'mod_webexactivity')));

    $typeopts = array(\mod_webexactivity\webex::WEBEXACTIVITY_TYPE_INSTALLED => get_string('typeinstalled', 'mod_webexactivity'),
                    \mod_webexactivity\webex::WEBEXACTIVITY_TYPE_ALL => get_string('typeforall', 'mod_webexactivity'),
                    \mod_webexactivity\webex::WEBEXACTIVITY_TYPE_PASSWORD_REQUIRED => get_string('typepwreq', 'mod_webexactivity')
                    );

    $setting = new admin_setting_configmulticheckbox('webexactivity/typemeetingcenter',
            get_string('typemeetingcenter', 'mod_webexactivity'),
            get_string('typemeetingcenter_desc', 'mod_webexactivity'),
            array(\mod_webexactivity\webex::WEBEXACTIVITY_TYPE_INSTALLED => 1,
                  \mod_webexactivity\webex::WEBEXACTIVITY_TYPE_ALL => 1,
                  \mod_webexactivity\webex::WEBEXACTIVITY_TYPE_PASSWORD_REQUIRED => 1),
            $typeopts);
    $settings->add($setting);

    $settings->add(new admin_setting_configtext('webexactivity/meetingtemplate',
            get_string('meetingtemplate', 'mod_webexactivity'),
            get_string('meetingtemplate_help', 'mod_webexactivity'), ''));

    $setting = new admin_setting_configmulticheckbox('webexactivity/typetrainingcenter',
            get_string('typetrainingcenter', 'mod_webexactivity'),
            get_string('typetrainingcenter_desc', 'mod_webexactivity'),
            array(\mod_webexactivity\webex::WEBEXACTIVITY_TYPE_PASSWORD_REQUIRED => 1),
            $typeopts);
    $settings->add($setting);

    $settings->add(new admin_setting_configtext('webexactivity/trainingtemplate',
            get_string('meetingtemplate', 'mod_webexactivity'),
            get_string('meetingtemplate_help', 'mod_webexactivity'), ''));

    // ---------------------------------------------------
    // Meeting Settings.
    // ---------------------------------------------------
    $settings->add(new admin_setting_heading('meetingsettings', get_string('meetingsettings', 'mod_webexactivity'), ''));

    $settings->add(new admin_setting_configtext('webexactivity/meetingclosegrace',
            get_string('meetingclosegrace', 'mod_webexactivity'),
            get_string('meetingclosegrace_help', 'mod_webexactivity'), '120'));

    $settings->add(new admin_setting_configcheckbox('webexactivity/requiremeetingpassword',
            get_string('requiremeetingpassword', 'mod_webexactivity'),
            get_string('requiremeetingpassword_help', 'mod_webexactivity'), 0));

    $options = array(\mod_webexactivity\webex::WEBEXACTIVITY_TYPE_MEETING => get_string('typemeetingcenter', 'mod_webexactivity'),
            \mod_webexactivity\webex::WEBEXACTIVITY_TYPE_TRAINING => get_string('typetrainingcenter', 'mod_webexactivity'));

    $settings->add(new admin_setting_configselect('webexactivity/defaultmeetingtype',
            get_string('defaultmeetingtype', 'webexactivity'),
            get_string('defaultmeetingtype_help', 'webexactivity'),
            \mod_webexactivity\webex::WEBEXACTIVITY_TYPE_MEETING, $options));

    $settings->add(new admin_setting_configcheckbox('webexactivity/enablecallin',
            get_string('enablecallin', 'mod_webexactivity'),
            get_string('enablecallin_help', 'mod_webexactivity'), 0));

    // ---------------------------------------------------
    // Recording Settings.
    // ---------------------------------------------------
    $settings->add(new admin_setting_heading('recordingsettings', get_string('recordingsettings', 'mod_webexactivity'), ''));

    $settings->add(new admin_setting_configtext('webexactivity/recordingtrashtime',
            get_string('recordingtrashtime', 'mod_webexactivity'),
            get_string('recordingtrashtime_help', 'mod_webexactivity'), '48'));

    $settings->add(new admin_setting_configcheckbox('webexactivity/manageallrecordings',
            get_string('manageallrecordings', 'mod_webexactivity'),
            get_string('manageallrecordings_help', 'mod_webexactivity'), 0));
}

// Add reports.
// Create and add a folder/category.
$ADMIN->add('modsettings', new admin_category('modwebexactivityfolder', new lang_string('pluginname', 'mod_webexactivity'),
        $module->is_enabled() === false));

// Add the settings to a the folder.
$ADMIN->add('modwebexactivityfolder', $settings);

$ADMIN->add("modwebexactivityfolder", new admin_externalpage('modwebexactivityrecordings',
        get_string('page_managerecordings', 'mod_webexactivity'),
        "$CFG->wwwroot/mod/webexactivity/admin_recordings.php", "mod/webexactivity:reports"));

$ADMIN->add("modwebexactivityfolder", new admin_externalpage('modwebexactivityusers',
        get_string('page_manageusers', 'mod_webexactivity'),
        "$CFG->wwwroot/mod/webexactivity/admin_users.php", "mod/webexactivity:reports"));

// Tell core we already added the settings structure.
$settings = null;



