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
 * This file contains all reminder plugin settings.
 *
 * @package    local_reminders
 * @author     Isuru Weerarathna <uisurumadushanka89@gmail.com>
 * @copyright  2012 Isuru Madushanka Weerarathna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($hassiteconfig) {

    require_once($CFG->dirroot.'/local/reminders/lib.php');

    $settings = new admin_settingpage('local_reminders', get_string('admintreelabel', 'local_reminders'));
    $ADMIN->add('localplugins', $settings);

    // Load all roles in the moodle.
    $systemcontext = context_system::instance();
    $allroles = role_fix_names(get_all_roles(), $systemcontext, ROLENAME_ORIGINAL);
    $rolesarray = [];
    if (!empty($allroles)) {
        foreach ($allroles as $arole) {
            $rolesarray[$arole->shortname] = ' '.$arole->localname;
        }
    }

    // Default settings for recieving reminders according to role.
    $defaultrolesforcourse = ['student' => 1];
    $defaultrolesforcategory = ['editingteacher' => 1, 'teacher' => 1];
    $defaultrolesforactivity = ['student' => 1, 'editingteacher' => 1];

    // Adds a checkbox to enable/disable sending reminders.
    $settings->add(new admin_setting_configcheckbox('local_reminders_enable',
            get_string('enabled', 'local_reminders'),
            get_string('enableddescription', 'local_reminders'), 1));

    $settings->add(new admin_setting_configcheckbox('local_reminders_timezone',
            get_string('useservertimezone', 'local_reminders'),
            '', 0));

    $settings->add(new admin_setting_configtext('local_reminders_messagetitleprefix',
            get_string('messagetitleprefix', 'local_reminders'),
            get_string('messagetitleprefixdescription', 'local_reminders'), 'Moodle-Reminder'));

    $replychoices = [
        REMINDERS_SEND_AS_ADMIN => get_string('sendasadmin', 'local_reminders'),
        REMINDERS_SEND_AS_NO_REPLY => get_string('sendasnoreply', 'local_reminders'),
    ];

    $settings->add(new admin_setting_configselect('local_reminders_sendas',
        get_string('sendas', 'local_reminders'),
        get_string('sendasdescription', 'local_reminders'),
        REMINDERS_SEND_AS_ADMIN, $replychoices));

    $settings->add(new admin_setting_configtext('local_reminders_sendasname',
        get_string('sendasnametitle', 'local_reminders'),
        get_string('sendasnamedescription', 'local_reminders'), 'No Reply'));

    $choices = [
        REMINDERS_SEND_ALL_EVENTS => get_string('filtereventssendall', 'local_reminders'),
        REMINDERS_SEND_ONLY_VISIBLE => get_string('filtereventsonlyvisible', 'local_reminders'),
    ];

    $settings->add(new admin_setting_configselect('local_reminders_filterevents',
            get_string('filterevents', 'local_reminders'),
            get_string('filtereventsdescription', 'local_reminders'),
            REMINDERS_SEND_ONLY_VISIBLE, $choices));

    $corepluginmanager = core_plugin_manager::instance();
    $formatplugins = $corepluginmanager->get_plugins_of_type('mod');
    $enabledplugins = $corepluginmanager->get_enabled_plugins('mod');
    $excludedoptions = [];
    foreach ($formatplugins as $key => $value) {
        if (in_array($key, $enabledplugins)) {
            $excludedoptions[$key] = $value->displayname;
        }
    }

    $settings->add(new admin_setting_configmultiselect('local_reminders_excludedmodulenames',
            get_string('excludedmodules', 'local_reminders'),
            get_string('excludedmodulesdesc', 'local_reminders'),
            [],
            $excludedoptions));

    // Adds a checkbox to fallback custom schedule for unknown events.
    $settings->add(new admin_setting_configcheckbox('local_reminders_fallback_customsched',
            get_string('customschedulefallback', 'local_reminders'),
            get_string('customschedulefallbackdesc', 'local_reminders'), 1));



    // REMINDER EMAIL CONFIGURATIONS.
    $settings->add(new admin_setting_heading('local_reminders_heading_emailcutomizations',
            get_string('emailconfigsheading', 'local_reminders'), ''));

    $settings->add(new admin_setting_confightmleditor('local_reminders_emailheadercustom',
        get_string('emailheadercustomname', 'local_reminders'),
        get_string('emailheadercustomnamedesc', 'local_reminders'), ''));

    $settings->add(new admin_setting_configcheckbox('local_reminders_emailfooterdefaultenabled',
        get_string('emailfooterdefaultname', 'local_reminders'),
        get_string('emailfooterdefaultnamedesc', 'local_reminders'), 1));

    $settings->add(new admin_setting_confightmleditor('local_reminders_emailfootercustom',
        get_string('emailfootercustomname', 'local_reminders'),
        get_string('emailfootercustomnamedesc', 'local_reminders'), ''));

    // END OF EMAIL CONFIGURATIONS.


    $daysarray = [
        'days7' => ' '.get_string('days7', 'local_reminders'),
        'days3' => ' '.get_string('days3', 'local_reminders'),
        'days1' => ' '.get_string('days1', 'local_reminders'),
    ];

    // Default settings for each event type.
    $defaultsite = ['days7' => 0, 'days3' => 1, 'days1' => 0];
    $defaultuser = ['days7' => 0, 'days3' => 0, 'days1' => 1];
    $defaultcourse = ['days7' => 0, 'days3' => 1, 'days1' => 0];
    $defaultcategory = ['days7' => 0, 'days3' => 1, 'days1' => 0];
    $defaultgroup = ['days7' => 0, 'days3' => 1, 'days1' => 0];
    $defaultdue = ['days7' => 0, 'days3' => 1, 'days1' => 0];
    $defaultdueopen = ['days7' => 0, 'days3' => 1, 'days1' => 0];

    // CALENDAR EVENT CHANGED EVENTS.

    $settings->add(new admin_setting_heading('local_reminders_heading_caleventchanged',
            get_string('caleventchangedheading', 'local_reminders'),
            get_string('caleventchangedheadingdetails', 'local_reminders')));

    $settings->add(new admin_setting_configcheckbox('local_reminders_enable_whenadded',
            get_string('enabledaddedevents', 'local_reminders'),
            get_string('enabledaddedeventsdescription', 'local_reminders'), 0));

    $settings->add(new admin_setting_configcheckbox('local_reminders_enable_whenchanged',
            get_string('enabledchangedevents', 'local_reminders'),
            get_string('enabledchangedeventsdescription', 'local_reminders'), 0));

    $settings->add(new admin_setting_configcheckbox('local_reminders_enable_whenremoved',
            get_string('enabledremovedevents', 'local_reminders'),
            get_string('enabledremovedeventsdescription', 'local_reminders'), 0));

    // SITE EVENT SETTINGS.

    // Add days selection for site events.
    $settings->add(new admin_setting_heading('local_reminders_site_heading',
            get_string('siteheading', 'local_reminders'), ''));

    $settings->add(new admin_setting_configmulticheckbox2('local_reminders_siterdays',
            get_string('reminderdaysahead', 'local_reminders'),
            get_string('explainsiteheading', 'local_reminders'),
            $defaultsite , $daysarray));

    // Added custom day selection for site events.
    $settings->add(new admin_setting_configduration('local_reminders_sitecustom',
            get_string('reminderdaysaheadcustom', 'local_reminders'),
            get_string('reminderdaysaheadcustomdetails', 'local_reminders'),
            0));

    $settings->add(new admin_setting_configcheckbox('local_reminders_enable_siteforcalevents',
            get_string('enabledforcalevents', 'local_reminders'),
            get_string('enabledforcaleventsdescription', 'local_reminders'), 0));

    // USER EVENT SETTINGS.

    // Add days selection for user related events.
    $settings->add(new admin_setting_heading('local_reminders_user_heading',
            get_string('userheading', 'local_reminders'), ''));

    $settings->add(new admin_setting_configmulticheckbox2('local_reminders_userrdays',
            get_string('reminderdaysahead', 'local_reminders'),
            get_string('explainuserheading', 'local_reminders'),
            $defaultuser, $daysarray));

    // Added custom day selection for user events.
    $settings->add(new admin_setting_configduration('local_reminders_usercustom',
            get_string('reminderdaysaheadcustom', 'local_reminders'),
            get_string('reminderdaysaheadcustomdetails', 'local_reminders'),
            0));

    $settings->add(new admin_setting_configcheckbox('local_reminders_enable_userforcalevents',
            get_string('enabledforcalevents', 'local_reminders'),
            get_string('enabledforcaleventsdescription', 'local_reminders'), 0));

    // COURSE EVENT SETTINGS.

    // Add days selection for course related events.
    $settings->add(new admin_setting_heading('local_reminders_course_heading',
            get_string('courseheading', 'local_reminders'), ''));

    $settings->add(new admin_setting_configmulticheckbox2('local_reminders_courserdays',
            get_string('reminderdaysahead', 'local_reminders'),
            get_string('explaincourseheading', 'local_reminders'),
            $defaultcourse, $daysarray));

    // Added custom day selection for course events.
    $settings->add(new admin_setting_configduration('local_reminders_coursecustom',
        get_string('reminderdaysaheadcustom', 'local_reminders'),
        get_string('reminderdaysaheadcustomdetails', 'local_reminders'),
        0));

    $settings->add(new admin_setting_configmulticheckbox2('local_reminders_courseroles',
            get_string('rolesallowedfor', 'local_reminders'),
            get_string('explainrolesallowedfor', 'local_reminders'),
            $defaultrolesforcourse, $rolesarray));

    $settings->add(new admin_setting_configcheckbox('local_reminders_enable_courseforcalevents',
            get_string('enabledforcalevents', 'local_reminders'),
            get_string('enabledforcaleventsdescription', 'local_reminders'), 0));

    // DUE EVENT SETTINGS.

    // Add days selection for due related events coming from activities in a course.
    $settings->add(new admin_setting_heading('local_reminders_due_heading',
            get_string('dueheading', 'local_reminders'), ''));

    // Settings regarding activity completion reminders.
    $settings->add(new admin_setting_configcheckbox('local_reminders_noremindersforcompleted',
            get_string('activityignoreincompletes', 'local_reminders'),
            get_string('activityignoreincompletesdetails', 'local_reminders'), 1));

    $settings->add(new admin_setting_configcheckbox('local_reminders_enableoverdueactivityreminders',
            get_string('overdueactivityreminders', 'local_reminders'),
            get_string('overdueactivityremindersdescription', 'local_reminders'), 1));

    $settings->add(new admin_setting_configtext('local_reminders_overduewarnprefix',
            get_string('overduewarnprefix', 'local_reminders'),
            get_string('overduewarnprefixdescription', 'local_reminders'), 'OVERDUE'));

    $settings->add(new admin_setting_configtext('local_reminders_overduewarnmessage',
            get_string('overduewarnmessage', 'local_reminders'),
            get_string('overduewarnmessagedescription', 'local_reminders'), 'This activity is overdue!'));

    $activitychoices = [
        REMINDERS_ACTIVITY_BOTH => get_string('activityremindersboth', 'local_reminders'),
        REMINDERS_ACTIVITY_ONLY_OPENINGS => get_string('activityremindersonlyopenings', 'local_reminders'),
        REMINDERS_ACTIVITY_ONLY_CLOSINGS => get_string('activityremindersonlyclosings', 'local_reminders'),
    ];

    $settings->add(new admin_setting_configselect('local_reminders_duesend',
            get_string('sendactivityreminders', 'local_reminders'),
            get_string('explainsendactivityreminders', 'local_reminders'),
            REMINDERS_ACTIVITY_BOTH, $activitychoices));

    $settings->add(new admin_setting_configcheckbox('local_reminders_separateactivityopenings',
            get_string('activityopeningseparation', 'local_reminders'),
            get_string('activityopeningseparationdesc', 'local_reminders'), 0));

    $settings->add(new admin_setting_configmulticheckbox2('local_reminders_dueopenrdays',
            get_string('activitydueopenahead', 'local_reminders'),
            get_string('activitydueopenaheaddesc', 'local_reminders'),
            $defaultdueopen, $daysarray));

    $settings->add(new admin_setting_configcheckbox('local_reminders_explicitenable',
            get_string('activityconfexplicitenable', 'local_reminders'),
            get_string('activityconfexplicitenabledesc', 'local_reminders'), 0));

    $settings->add(new admin_setting_configmulticheckbox2('local_reminders_duerdays',
            get_string('reminderdaysahead', 'local_reminders'),
            get_string('explaindueheading', 'local_reminders'),
            $defaultdue, $daysarray));

    // Added custom day selection for acivity events.
    $settings->add(new admin_setting_configduration('local_reminders_duecustom',
        get_string('reminderdaysaheadcustom', 'local_reminders'),
        get_string('reminderdaysaheadcustomdetails', 'local_reminders'),
        0));

    $settings->add(new admin_setting_configmulticheckbox2('local_reminders_activityroles',
            get_string('rolesallowedfor', 'local_reminders'),
            get_string('explainrolesallowedfor', 'local_reminders'),
            $defaultrolesforactivity, $rolesarray));

    $settings->add(new admin_setting_configcheckbox('local_reminders_enable_dueforcalevents',
            get_string('enabledforcalevents', 'local_reminders'),
            get_string('enabledforcaleventsdescription', 'local_reminders'), 0));

    $settings->add(new admin_setting_configcheckbox('local_reminders_showmodnameintitle',
            get_string('showmodnameintitle', 'local_reminders'),
            get_string('showmodnameintitledesc', 'local_reminders'), 1));

    // GROUP EVENT SETTINGS.

    // Add group related events.
    $settings->add(new admin_setting_heading('local_reminders_group_heading',
            get_string('groupheading', 'local_reminders'), ''));

    $settings->add(new admin_setting_configcheckbox('local_reminders_groupshowname',
            get_string('groupshowname', 'local_reminders'),
            get_string('explaingroupshowname', 'local_reminders'), 1));

    $settings->add(new admin_setting_configmulticheckbox2('local_reminders_grouprdays',
            get_string('reminderdaysahead', 'local_reminders'),
            get_string('explaingroupheading', 'local_reminders'),
            $defaultgroup, $daysarray));

    // Added custom day selection for group events.
    $settings->add(new admin_setting_configduration('local_reminders_groupcustom',
        get_string('reminderdaysaheadcustom', 'local_reminders'),
        get_string('reminderdaysaheadcustomdetails', 'local_reminders'),
        0));

    $settings->add(new admin_setting_configcheckbox('local_reminders_enable_groupforcalevents',
        get_string('enabledforcalevents', 'local_reminders'),
        get_string('enabledforcaleventsdescription', 'local_reminders'), 0));


    // COURSE CATEGORY EVENT SETTINGS.

    // Add days selection for category related events.
    $settings->add(new admin_setting_heading('local_reminders_category_heading',
            get_string('categoryheading', 'local_reminders'), ''));

    $settings->add(new admin_setting_configcheckbox('local_reminders_category_noforcompleted',
            get_string('categorynosendforended', 'local_reminders'),
            get_string('categorynosendforendeddescription', 'local_reminders'), 1));

    $settings->add(new admin_setting_configmulticheckbox2('local_reminders_categoryrdays',
            get_string('reminderdaysahead', 'local_reminders'),
            get_string('explaincategoryheading', 'local_reminders'),
            $defaultcategory, $daysarray));

    // Added custom day selection for category events.
    $settings->add(new admin_setting_configduration('local_reminders_categorycustom',
        get_string('reminderdaysaheadcustom', 'local_reminders'),
        get_string('reminderdaysaheadcustomdetails', 'local_reminders'),
        0));

    $settings->add(new admin_setting_configmulticheckbox2('local_reminders_categoryroles',
            get_string('rolesallowedfor', 'local_reminders'),
            get_string('explainrolesallowedfor', 'local_reminders'),
            $defaultrolesforcategory, $rolesarray));

    $settings->add(new admin_setting_configcheckbox('local_reminders_enable_categoryforcalevents',
            get_string('enabledforcalevents', 'local_reminders'),
            get_string('enabledforcaleventsdescription', 'local_reminders'), 0));
}
