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
 *
 * @package    enrol_workdaystudent
 * @copyright  2023 onwards LSU Online & Continuing Education
 * @copyright  2023 Robert Russo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Set the string for use later.
$fn = new lang_string('wds:sshortname', 'enrol_workdaystudent');

// Create the folder / submenu.
$ADMIN->add('enrolments', new admin_category('enrollwdsfolder', $fn));

// Create the settings page.
$settings = new admin_settingpage($section, get_string('wds:cshortname', 'enrol_workdaystudent'));

// Only for admins.
if ($ADMIN->fulltree) {

    // Build the studenroles array.
    $studentroles = [];

    // Make sure they are set 1st.
    if (isset($CFG->gradebookroles)) {

        // Get the "student" roles.
        $roles = explode(',', $CFG->gradebookroles);
    } else {
        // Build an empty array…for…reasons.
        $roles = [];
    }

    // Loop through those roles and do stuff.
    foreach ($roles as $role) {

        // Grab the role names from the DB.
        $rname = $DB->get_record('role', array("id" => $role));

        // Set the studentroles array for the dropdown.
        $studentroles[$role] = $rname->name === "" ? $rname->shortname : $rname->name;
    }

    // Build the faculty roles array.
    $facultyroles = [];

    // Make sure we have course contacts.
    if (isset($CFG->coursecontact)) {

        // Get the "teacher" roles.
        $froles = explode(',', $CFG->coursecontact);
    } else {
        // Build the empty array for…reasons.
        $froles = [];
    }

    // Loop through those roles and do stuff.
    foreach ($froles as $frole) {

        // Grab the role names from the DB.
        $frname = $DB->get_record('role', array("id" => $frole));

        // Set the studentroles array for the dropdown.
        $facultyroles[$frole] = $frname->name === "" ? $frname->shortname : $frname->name;
    }

    // Get all roles from the DB.
    $allroles = $DB->get_records('role');

    foreach ($allroles as $nprole) {
        // Use fullname if set, otherwise fallback to shortname.
        $nonprimaryroles[$nprole->id] = trim($nprole->name) !== '' ? $nprole->name : $nprole->shortname;
    }

    // Grab the course categories.
    $ccategories = $DB->get_records('course_categories', null, 'name', 'id,name');

    // Force this in.
    $categories[0] = 'Top';

    // Loop through those roles and do stuff.
    foreach ($ccategories as $category) {

        // Set the studentroles array for the dropdown.
        $categories[$category->id] = $category->name;
    }

    // Add a heading.
    $settings->add(
        new admin_setting_heading(
            'enrol_workdaystudent/pluginsettings',
            '',
            get_string('workdaystudent:pluginsettings', 'enrol_workdaystudent')
        )
    );

    // Workday Student Websevice username.
    $settings->add(
        new admin_setting_configtext(
            'enrol_workdaystudent/username',
            get_string('workdaystudent:username', 'enrol_workdaystudent'),
            get_string('workdaystudent:username_desc', 'enrol_workdaystudent'),
            'Moodle_ISU', PARAM_TEXT
        )
    );

    // Workday Student Webservice Token.
    $settings->add(
        new admin_setting_configpasswordunmask(
            'enrol_workdaystudent/password',
            get_string('workdaystudent:password', 'enrol_workdaystudent'),
            get_string('workdaystudent:password_desc', 'enrol_workdaystudent'),
            '', PARAM_RAW
        )
    );

    // Workday Student API Version.
    $settings->add(
        new admin_setting_configtext(
            'enrol_workdaystudent/apiversion',
            get_string('workdaystudent:apiversion', 'enrol_workdaystudent'),
            get_string('workdaystudent:apiversion_desc', 'enrol_workdaystudent'),
            '43.0', PARAM_TEXT
        )
    );

    // Workday Student campus code.
    $settings->add(
        new admin_setting_configtext(
            'enrol_workdaystudent/campus',
            get_string('workdaystudent:campus', 'enrol_workdaystudent'),
            get_string('workdaystudent:campus_desc', 'enrol_workdaystudent'),
            'AU00000079', PARAM_TEXT
        )
    );

    // Workday Student campus name.
    $settings->add(
        new admin_setting_configtext(
            'enrol_workdaystudent/campusname',
            get_string('workdaystudent:campusname', 'enrol_workdaystudent'),
            get_string('workdaystudent:campusname_desc', 'enrol_workdaystudent'),
            'LSUAM', PARAM_TEXT
        )
    );

    // Workday semester range.
    $settings->add(
        new admin_setting_configtext(
            'enrol_workdaystudent/brange',
            get_string('workdaystudent:brange', 'enrol_workdaystudent'),
            get_string('workdaystudent:brange_desc', 'enrol_workdaystudent'),
            '60', PARAM_INT
        )
    );

    $settings->add(
        new admin_setting_configtext(
            'enrol_workdaystudent/erange',
            get_string('workdaystudent:erange', 'enrol_workdaystudent'),
            get_string('workdaystudent:erange_desc', 'enrol_workdaystudent'),
            '6', PARAM_INT
        )
    );


    // Workday student metadata fields.
    $settings->add(
        new admin_setting_configtext(
            'enrol_workdaystudent/metafields',
            get_string('workdaystudent:metafields', 'enrol_workdaystudent'),
            get_string('workdaystudent:metafields_desc', 'enrol_workdaystudent'),
            '', PARAM_TEXT
        )
    );

    // Workday student metadata fields.
    $settings->add(
        new admin_setting_configtext(
            'enrol_workdaystudent/sportfield',
            get_string('workdaystudent:sportfield', 'enrol_workdaystudent'),
            get_string('workdaystudent:sportfield_desc', 'enrol_workdaystudent'),
            '', PARAM_TEXT
        )
    );

    $settings->add(
        new admin_setting_configcheckbox(
            'enrol_workdaystudent/autoparent',
            get_string('workdaystudent:autoparent', 'enrol_workdaystudent'),
            get_string('workdaystudent:autoparent_desc', 'enrol_workdaystudent'),
            0
        )
    );

    // Parent category.
    $settings->add(
        new admin_setting_configselect(
            'enrol_workdaystudent/parentcat',
            get_string('workdaystudent:parentcat', 'enrol_workdaystudent'),
            get_string('workdaystudent:parentcat_desc', 'enrol_workdaystudent'),
            'Top',  // Default.
            $categories
        )
    );

    $settings->hide_if('enrol_workdaystudent/parentcat', 'enrol_workdaystudent/autoparent', 'eq', '1');

    // Primary role.
    $settings->add(
        new admin_setting_configselect(
            'enrol_workdaystudent/primaryrole',
            get_string('workdaystudent:primaryrole', 'enrol_workdaystudent'),
            get_string('workdaystudent:primaryrole_desc', 'enrol_workdaystudent'),
            'None',  // Default.
            $facultyroles
        )
    );

    // Non-Primary role.
    $settings->add(
        new admin_setting_configselect(
            'enrol_workdaystudent/nonprimaryrole',
            get_string('workdaystudent:nonprimaryrole', 'enrol_workdaystudent'),
            get_string('workdaystudent:nonprimaryrole_desc', 'enrol_workdaystudent'),
            'None',  // Default.
            $nonprimaryroles
        )
    );

    // Student role.
    $settings->add(
        new admin_setting_configselect(
            'enrol_workdaystudent/studentrole',
            get_string('workdaystudent:studentrole', 'enrol_workdaystudent'),
            get_string('workdaystudent:studentrole_desc', 'enrol_workdaystudent'),
            'Student',  // Default.
            $studentroles
        )
    );

    // Suspend or Unenroll.
    $settings->add(
        new admin_setting_configselect(
            'enrol_workdaystudent/unenroll',
            get_string('workdaystudent:suspend_unenroll', 'enrol_workdaystudent'),
            get_string('workdaystudent:suspend_unenroll_desc', 'enrol_workdaystudent'),
            0,  // Default.
            [0 => 'suspend', 1 => 'unenroll']
        )
    );

    // Add the course number creation threshold.
    $settings->add(
        new admin_setting_configtext(
            'enrol_workdaystudent/numberthreshold',
            get_string('workdaystudent:numberthreshold', 'enrol_workdaystudent'),
            get_string('workdaystudent:numberthreshold_desc', 'enrol_workdaystudent'),
            9000,
            PARAM_INT
        )
    );

    // Add the wdspref_daysprior setting.
    $settings->add(
        new admin_setting_configtext(
            'enrol_workdaystudent/createprior',
            get_string('workdaystudent:createprior', 'enrol_workdaystudent'),
            get_string('workdaystudent:createprior_desc', 'enrol_workdaystudent'),
            30,
            PARAM_INT
        )
    );

    // Add the wdspref_enrollprior setting.
    $settings->add(
        new admin_setting_configtext(
            'enrol_workdaystudent/enrollprior',
            get_string('workdaystudent:enrollprior', 'enrol_workdaystudent'),
            get_string('workdaystudent:enrollprior_desc', 'enrol_workdaystudent'),
            14,
            PARAM_INT
        )
    );


    // Course visibility defaults.
    $settings->add(
        new admin_setting_configcheckbox(
            'enrol_workdaystudent/visible',
            get_string('workdaystudent:visible', 'enrol_workdaystudent'),
            get_string('workdaystudent:visible_desc', 'enrol_workdaystudent'),
            0
        )
    );

    // Course grouping defaults.
    $settings->add(
        new admin_setting_configcheckbox(
            'enrol_workdaystudent/course_grouping',
            get_string('workdaystudent:course_grouping', 'enrol_workdaystudent'),
            get_string('workdaystudent:course_grouping_desc', 'enrol_workdaystudent'),
            1
        )
    );

    // Course unenrollment/suspension defaults.
    $settings->add(
        new admin_setting_configcheckbox(
            'enrol_workdaystudent/suspend',
            get_string('workdaystudent:suspend', 'enrol_workdaystudent'),
            get_string('workdaystudent:suspend_desc', 'enrol_workdaystudent'),
            0
        )
    );

    // Add a heading.
    $settings->add(
        new admin_setting_heading(
            'enrol_workdaystudent/webservices',
            '',
            get_string('workdaystudent:webservices', 'enrol_workdaystudent')
        )
    );

    // Workday Student Websevice URL.
    $settings->add(
        new admin_setting_configtext(
            'enrol_workdaystudent/wsurl',
            get_string('workdaystudent:wsurl', 'enrol_workdaystudent'),
            get_string('workdaystudent:wsurl_desc', 'enrol_workdaystudent'),
            'https://someurl.net', PARAM_URL
        )
    );

    // Workday student webservice endpoints.
    $settings->add(
        new admin_setting_configtext(
            'enrol_workdaystudent/units',
            get_string('workdaystudent:units', 'enrol_workdaystudent'),
            get_string('workdaystudent:units_desc', 'enrol_workdaystudent'),
            '', PARAM_TEXT
        )
    );

    // Workday student webservice endpoints.
    $settings->add(
        new admin_setting_configtext(
            'enrol_workdaystudent/periods',
            get_string('workdaystudent:periods', 'enrol_workdaystudent'),
            get_string('workdaystudent:periods_desc', 'enrol_workdaystudent'),
            '', PARAM_TEXT
        )
    );

    // Workday student webservice endpoints.
    $settings->add(
        new admin_setting_configtext(
            'enrol_workdaystudent/programs',
            get_string('workdaystudent:programs', 'enrol_workdaystudent'),
            get_string('workdaystudent:programs_desc', 'enrol_workdaystudent'),
            '', PARAM_TEXT
        )
    );

    // Workday student webservice endpoints.
    $settings->add(
        new admin_setting_configtext(
            'enrol_workdaystudent/grading_schemes',
            get_string('workdaystudent:grading_schemes', 'enrol_workdaystudent'),
            get_string('workdaystudent:grading_schemes_desc', 'enrol_workdaystudent'),
            '', PARAM_TEXT
        )
    );

    // Workday student webservice endpoints.
    $settings->add(
        new admin_setting_configtext(
            'enrol_workdaystudent/courses',
            get_string('workdaystudent:courses', 'enrol_workdaystudent'),
            get_string('workdaystudent:courses_desc', 'enrol_workdaystudent'),
            '', PARAM_TEXT
        )
    );

    // Workday student webservice endpoints.
    $settings->add(
        new admin_setting_configtext(
            'enrol_workdaystudent/sections',
            get_string('workdaystudent:sections', 'enrol_workdaystudent'),
            get_string('workdaystudent:sections_desc', 'enrol_workdaystudent'),
            '', PARAM_TEXT
        )
    );

    // Workday student webservice endpoints.
    $settings->add(
        new admin_setting_configtext(
            'enrol_workdaystudent/dates',
            get_string('workdaystudent:dates', 'enrol_workdaystudent'),
            get_string('workdaystudent:dates_desc', 'enrol_workdaystudent'),
            '', PARAM_TEXT
        )
    );

    // Workday student webservice endpoints.
    $settings->add(
        new admin_setting_configtext(
            'enrol_workdaystudent/students',
            get_string('workdaystudent:students', 'enrol_workdaystudent'),
            get_string('workdaystudent:students_desc', 'enrol_workdaystudent'),
            '', PARAM_TEXT
        )
    );

    // Workday student webservice endpoints.
    $settings->add(
        new admin_setting_configtext(
            'enrol_workdaystudent/registrations',
            get_string('workdaystudent:registrations', 'enrol_workdaystudent'),
            get_string('workdaystudent:registrations_desc', 'enrol_workdaystudent'),
            '', PARAM_TEXT
        )
    );

    // Workday student webservice endpoints.
    $settings->add(
        new admin_setting_configtext(
            'enrol_workdaystudent/guild',
            get_string('workdaystudent:guild', 'enrol_workdaystudent'),
            get_string('workdaystudent:guild_desc', 'enrol_workdaystudent'),
            '', PARAM_TEXT
        )
    );

    // Add a heading.
    $settings->add(
        new admin_setting_heading(
            'enrol_workdaystudent/coursedefs',
            '',
            get_string('workdaystudent:coursedefs', 'enrol_workdaystudent')
        )
    );

    // Add the course name seting.
    $settings->add(
        new admin_setting_configtext(
            'enrol_workdaystudent/namingformat',
            get_string('workdaystudent:coursenamingformat', 'enrol_workdaystudent'),
            get_string('workdaystudent:coursenamingformat_desc', 'enrol_workdaystudent'),
            '{period_year} {period_type} {course_subject_abbreviation} {course_number} for {firstname} {lastname} {delivery_mode}',
            PARAM_TEXT
        )
    );

    // Add a heading.
    $settings->add(
        new admin_setting_heading(
            'enrol_workdaystudent/emails',
            '',
            get_string('workdaystudent:emails', 'enrol_workdaystudent')
        )
    );

    // Workday Student Administrative contacts.
    $settings->add(
        new admin_setting_configtext(
            'enrol_workdaystudent/contacts',
            get_string('workdaystudent:contacts', 'enrol_workdaystudent'),
            get_string('workdaystudent:contacts_desc', 'enrol_workdaystudent'),
            'admin,student', PARAM_TEXT
        )
    );
}

// Add the folder.
$ADMIN->add('enrollwdsfolder', $settings);

// Prevent Moodle from adding settings block in standard location.
$settings = null;

// Set the url for the period config page.
$wdsperiods = new admin_externalpage(
    'period_config',
    new lang_string('workdaystudent:periodconfig', 'enrol_workdaystudent'),
    new moodle_url('/enrol/workdaystudent/periodconfig.php')
);

// Set the url for the update students page.
$wdsstupdates = new admin_externalpage(
    'update_students',
    new lang_string('wds:updateusers', 'enrol_workdaystudent'),
    new moodle_url('/enrol/workdaystudent/updatestudents.php')
);

// Set the context for later use.
$context = \context_system::instance();

// Add the links for those who have access (admins for now).
if (has_capability('moodle/site:config', $context)) {
    $ADMIN->add('enrollwdsfolder', $wdsperiods);
    $ADMIN->add('enrollwdsfolder', $wdsstupdates);
}
