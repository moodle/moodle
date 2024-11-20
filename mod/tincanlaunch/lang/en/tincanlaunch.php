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
 * English strings for tincanlaunch
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package mod_tincanlaunch
 * @copyright  2013 Andrew Downes
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['modulename'] = 'xAPI Launch Link';
$string['modulenameplural'] = 'xAPI Launch Links';
$string['modulename_help'] = 'A plug in for Moodle that allows the launch of xAPI (TinCan) content which is then tracked to a separate LRS.';

// Start Default LRS Admin Settings.
$string['tincanlaunchlrsfieldset'] = 'Default values for xAPI Launch Link activity settings';
$string['tincanlaunchlrsfieldset_help'] = 'These are site-wide, default values used when creating a new activity. Each activity has the ability to override and provide alternative values.';

$string['tincanlaunchlrsendpoint'] = 'Endpoint';
$string['tincanlaunchlrsendpoint_help'] = 'The LRS endpoint (e.g. http://lrs.example.com/endpoint/). Must include trailing forward slash.';
$string['tincanlaunchlrsendpoint_default'] = '';

$string['tincanlaunchlrslogin'] = 'Basic Login';
$string['tincanlaunchlrslogin_help'] = 'Your LRS login key.';
$string['tincanlaunchlrslogin_default'] = '';

$string['tincanlaunchlrspass'] = 'Basic Password';
$string['tincanlaunchlrspass_help'] = 'Your LRS password (secret).';
$string['tincanlaunchlrspass_default'] = '';

$string['tincanlaunchlrsduration'] = 'Duration';
$string['tincanlaunchlrsduration_help'] = 'Used with \'LRS integrated basic authentication\'. Requests the LRS to keep credentials valid for this number of minutes.';
$string['tincanlaunchlrsduration_default'] = '9000';

$string['tincanlaunchlrsauthentication'] = 'LRS integration';
$string['tincanlaunchlrsauthentication_help'] = 'Use additional integration features to create new authentication credentials for each launch for supported LRSs.';
$string['tincanlaunchlrsauthentication_watershedhelp'] = 'Note: for Watershed integration, the Activity Provider does not require API access enabled.';
$string['tincanlaunchlrsauthentication_watershedhelp_label'] = 'Watershed integration';
$string['tincanlaunchlrsauthentication_option_0'] = 'None';
$string['tincanlaunchlrsauthentication_option_1'] = 'Watershed';
$string['tincanlaunchlrsauthentication_option_2'] = 'Learning Locker 1';

$string['tincanlaunchuseactoremail'] = 'Identify by email';
$string['tincanlaunchuseactoremail_help'] = 'If selected, learners will be identified by their email address if they have one recorded in Moodle.';

$string['tincanlaunchcustomacchp'] = 'Custom account homepage';
$string['tincanlaunchcustomacchp_help'] = 'If entered, Moodle will use this homePage in conjunction with the ID number user profile field to identify the learner. If the ID number is not entered for a learner, they will instead be identified by email or Moodle ID number. Note: If a learner\'s id changes, they will lose access to registrations associated with former ids and completion data may be reset. Reports in your LRS may also be affected.';
$string['tincanlaunchcustomacchp_default'] = '';

// Start Activity Settings.
$string['tincanlaunchname'] = 'Launch link name';
$string['tincanlaunchname_help'] = 'The name of the launch link as it will appear to the user.';

$string['tincanlaunchurl'] = 'Launch URL';
$string['tincanlaunchurl_help'] = 'The full URL of the xAPI activity you want to launch.';

$string['tincanactivityid'] = 'Activity ID';
$string['tincanactivityid_help'] = 'The identifying IRI for the primary activity being launched. It <b>MUST</b> match the identifying IRI in the tincan.xml.';

$string['tincanpackage'] = 'Zip package';
$string['tincanpackage_help'] = 'If you have a packaged xAPI course, you can upload it here. If you upload a package, the Launch URL and Activity ID fields above will be automatically populated when you save using data from the tincan.xml file contained in the zip. You can edit these settings at any time, but should not change the Activity ID (either directly or by file upload) unless you understand the consequences.';

$string['tincanpackagetitle'] = 'Launch settings';
$string['tincanpackagetext'] = 'You can provide the Launch URL and Activity ID settings directly, or upload a zip package containing a tincan.xml file. The Activity ID must always be a full URL (or other IRI) AND it MUST match the Activity ID included in the tincan.xml or course.';

$string['lrsheading'] = 'Override default LRS settings';
$string['lrsdefaults'] = 'LRS Default Settings';
$string['lrssettingdescription'] = 'By default, this activity uses the global LRS settings found in Site administration > Plugins > Activity modules > xAPI Launch Link. To change the settings for this specific activity, select Unlock Defaults.';
$string['overridedefaults'] = 'Unlock Defaults';
$string['overridedefaults_help'] = 'Allows activity to have different LRS settings than the site-wide, default LRS settings.';

$string['appearanceheading'] = 'Appearance';

$string['tincanmultipleregs'] = 'Allow multiple registrations.';
$string['tincanmultipleregs_help'] = 'If selected, allow the learner to start more than one registration for the activity. If unchecked, only the most recent registration will be displayed. <b>This setting cannot be used when simplified launch is enabled.</b>';

$string['apCreationFailed'] = 'Failed to create Watershed Activity Provider.';

// Zip errors.
$string['badmanifest'] = 'Some manifest errors: see errors log';
$string['badimsmanifestlocation'] = 'A tincan.xml file was found but it was not in the root of your zip file, please re-package your course';
$string['badarchive'] = 'You must provide a valid zip file';
$string['nomanifest'] = 'Incorrect file package - missing tincan.xml';

$string['tincanlaunch'] = 'xAPI Launch Link';
$string['pluginadministration'] = 'xAPI Launch Link administration';
$string['pluginname'] = 'xAPI Launch Link';

// Verb completion settings.
$string['completionverb'] = 'Verb';
$string['completionverbgroup'] = 'Completion by verb';
$string['completionverbgroup_help'] = 'Moodle will look for statements where the actor is the current user, the object is the specified activity id, and the verb is the one set here. If it finds a matching statement, the activity will be marked complete.';

// Expiry completion settings.
$string['completionexpiry'] = 'Expiry';
$string['completionexpirygroup'] = 'Completion expires after (days)';
$string['completionexpirygroup_help'] = 'This setting requires the "Completion by verb" setting enabled. Moodle will determine completion by filtering LRS statements stored within the last X days. It will unset completion for learners who had previously completed but whose completion has now expired.';

// Completion Description Details.
$string['completiondetail:completionbyverb'] = 'Receive a "{$a}" statement';
$string['completiondetail:completionexpiry'] = 'Completed within the last {$a} days';
$string['completiondetail:completionbyverbdesc'] = 'Student required to receive a <b>{$a}</b> statement.';
$string['completiondetail:completionexpirydesc'] = 'Student must have completed within the last <b>{$a}</b> days.';

// View settings.
$string['tincanlaunchviewfirstlaunched'] = 'First launched';
$string['tincanlaunchviewlastlaunched'] = 'Last launched';
$string['tincanlaunchviewlaunchlinkheader'] = 'Launch link';
$string['tincanlaunchviewlaunchlink'] = 'Launch Existing Registration';
$string['tincanlaunch:view'] = 'View xAPI activity';

$string['tincanlaunch_completed'] = 'Experience complete!';
$string['tincanlaunch_progress'] = 'Attempt launched in a new window. If you have closed that window, you can safely navigate away from this page.';
$string['tincanlaunch_attempt'] = 'Start New Registration';
$string['tincanlaunch_notavailable'] = 'The Learning Record Store is not available. Please contact a system administrator. If you are the system administrator, go to Site admin / Development / Debugging and set Debug messages to DEVELOPER. Set it back to NONE or MINIMAL once the error details have been recorded.';
$string['tincanlaunch_regidempty'] = 'Registration id not found. Please close this window.';

$string['idmissing'] = 'You must specify a course_module ID or an instance ID';

// Events.
$string['eventactivitylaunched'] = 'Activity launched';
$string['eventactivitycompleted'] = 'Activity completed';

$string['tincanlaunch:addinstance'] = 'Add a new xAPI activity to a course';

$string['expirecredentials'] = 'Expire credentials';
$string['checkcompletion'] = 'Check Completion';

// Custom user profile fields.
$string['profilefields'] = 'User profile fields to sync to Agent Profile';
$string['profilefields_desc'] = 'If selected, the custom user profile fields selected will be sent to the LRS under the actors agent profile.';

$string['returntocourse'] = 'Return to course';
$string['returntoregistrations'] = 'Return to registrations table';

// Simplified Navigation.
$string['tincansimplelaunchnav'] = 'Enable simplified launch';
$string['tincansimplelaunchnav_help'] = 'If selected, the user will bypass the registration screen and the course will be automatically launched using the most recent registration. If no prior registration is found, one will be created. <b>Enabling this setting will disable the multiple registrations setting.</b>';
