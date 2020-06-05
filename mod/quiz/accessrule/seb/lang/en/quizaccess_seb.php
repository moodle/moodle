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
 * Strings for the quizaccess_seb plugin.
 *
 * @package    quizaccess_seb
 * @author     Luca Bösch <luca.boesch@bfh.ch>
 * @author     Andrew Madden <andrewmadden@catalyst-au.net>
 * @author     Dmitrii Metelkin <dmitriim@catalyst-au.net>
 * @copyright  2019 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['addtemplate'] = 'Add new template';
$string['allowedbrowserkeysdistinct'] = 'The keys must all be different.';
$string['allowedbrowserkeyssyntax'] = 'A key should be a 64-character hex string.';
$string['cachedef_config'] = 'SEB config cache';
$string['cachedef_configkey'] = 'SEB config key cache';
$string['cachedef_quizsettings'] = 'SEB quiz settings cache';
$string['cantdelete'] = 'The template can\'t be deleted as it has been used for one or more quizzes.';
$string['cantedit'] = 'The template can\'t be edited as it has been used for one or more quizzes.';
$string['clientrequiresseb'] = 'This quiz has been configured to use the Safe Exam Browser with client configuration.';
$string['confirmtemplateremovalquestion'] = 'Are you sure you want to remove this template?';
$string['confirmtemplateremovaltitle'] = 'Confirm template removal?';
$string['conflictingsettings'] = 'You don\'t have permission to update existing Safe Exam Browser settings.';
$string['content'] = 'Template';
$string['description'] = 'Description';
$string['disabledsettings'] = 'Disabled settings.';
$string['disabledsettings_help'] = 'Safe Exam Browser quiz settings can\'t be changed if the quiz has been attempted. To change a setting, all quiz attempts must first be deleted.';
$string['downloadsebconfig'] = 'Download SEB config file';
$string['duplicatetemplate'] = 'A template with the same name already exists.';
$string['edittemplate'] = 'Edit template';
$string['enabled'] = 'Enabled';
$string['event:accessprevented'] = "Quiz access was prevented";
$string['event:templatecreated'] = 'SEB template was created';
$string['event:templatedeleted'] = 'SEB template was deleted';
$string['event:templatedisabled'] = 'SEB template was disabled';
$string['event:templateenabled'] = 'SEB template was enabled';
$string['event:templateupdated'] = 'SEB template was updated';
$string['exitsebbutton'] = 'Exit Safe Exam Browser';
$string['filemanager_sebconfigfile'] = 'Upload Safe Exam Browser config file';
$string['filemanager_sebconfigfile_help'] = 'Please upload your own Safe Exam Browser config file for this quiz.';
$string['filenotpresent'] = 'Please upload a SEB config file.';
$string['fileparsefailed'] = 'The uploaded file could not be saved as a SEB config file.';
$string['httplinkbutton'] = 'Download configuration';
$string['invalid_browser_key'] = "Invalid SEB browser key";
$string['invalid_config_key'] = "Invalid SEB config key";
$string['invalidkeys'] = "The config key or browser exam keys could not be validated. Please ensure you are using the Safe Exam Browser with correct configuration file.";
$string['invalidtemplate'] = "Invalid SEB config template";
$string['manage_templates'] = 'Safe Exam Browser templates';
$string['managetemplates'] = 'Manage templates';
$string['missingrequiredsettings'] = 'Config settings are missing some required values.';
$string['name'] = 'Name';
$string['newtemplate'] = 'New template';
$string['noconfigfilefound'] = 'No uploaded SEB config file could be found for quiz with cmid: {$a}';
$string['noconfigfound'] = 'No SEB config could be found for quiz with cmid: {$a}';
$string['not_seb'] = 'No Safe Exam Browser is being used.';
$string['notemplate'] = 'No template';
$string['passwordnotset'] = 'Current settings require quizzes using the Safe Exam Browser to have a quiz password set.';
$string['pluginname'] = 'Safe Exam Browser access rules';
$string['privacy:metadata:quizaccess_seb_quizsettings'] = 'Safe Exam Browser settings for a quiz. This includes the ID of the last user to create or modify the settings.';
$string['privacy:metadata:quizaccess_seb_quizsettings:quizid'] = 'ID of the quiz the settings exist for.';
$string['privacy:metadata:quizaccess_seb_quizsettings:timecreated'] = 'Unix time that the settings were created.';
$string['privacy:metadata:quizaccess_seb_quizsettings:timemodified'] = 'Unix time that the settings were last modified.';
$string['privacy:metadata:quizaccess_seb_quizsettings:usermodified'] = 'ID of user who last created or modified the settings.';
$string['privacy:metadata:quizaccess_seb_template'] = 'Safe Exam Browser template settings. This includes the ID of the last user to create or modify the template.';
$string['privacy:metadata:quizaccess_seb_template:timecreated'] = 'Unix time that the template was created.';
$string['privacy:metadata:quizaccess_seb_template:timemodified'] = 'Unix time that the template was last modified.';
$string['privacy:metadata:quizaccess_seb_template:usermodified'] = 'ID of user who last created or modified the template.';
$string['quizsettings'] = 'Quiz settings';
$string['restoredfrom'] = '{$a->name} (restored via cmid {$a->cmid})';
$string['seb'] = 'Safe Exam Browser';
$string['seb:bypassseb'] = 'Bypass the requirement to view quiz in Safe Exam Browser.';
$string['seb:manage_filemanager_sebconfigfile'] = 'Change SEB quiz setting: Select SEB config file';
$string['seb:manage_seb_activateurlfiltering'] = 'Change SEB quiz setting: Activate URL filtering';
$string['seb:manage_seb_allowedbrowserexamkeys'] = 'Change SEB quiz setting: Allowed browser exam keys';
$string['seb:manage_seb_allowreloadinexam'] = 'Change SEB quiz setting: Allow reload';
$string['seb:manage_seb_allowspellchecking'] = 'Change SEB quiz setting: Enable spell checking';
$string['seb:manage_seb_allowuserquitseb'] = 'Change SEB quiz setting: Allow quit';
$string['seb:manage_seb_enableaudiocontrol'] = 'Change SEB quiz setting: Enable audio control';
$string['seb:manage_seb_expressionsallowed'] = 'Change SEB quiz setting: Simple expressions allowed';
$string['seb:manage_seb_expressionsblocked'] = 'Change SEB quiz setting: Simple expressions blocked';
$string['seb:manage_seb_filterembeddedcontent'] = 'Change SEB quiz setting: Filter embedded content';
$string['seb:manage_seb_linkquitseb'] = 'Change SEB quiz setting: Quit link';
$string['seb:manage_seb_muteonstartup'] = 'Change SEB quiz setting: Mute on startup';
$string['seb:manage_seb_quitpassword'] = 'Change SEB quiz setting: Quit password';
$string['seb:manage_seb_regexallowed'] = 'Change SEB quiz setting: Regex expressions allowed';
$string['seb:manage_seb_regexblocked'] = 'Change SEB quiz setting: Regex expressions blocked';
$string['seb:manage_seb_requiresafeexambrowser'] = 'Change SEB quiz setting: Require Safe Exam Browser';
$string['seb:manage_seb_showkeyboardlayout'] = 'Change SEB quiz setting: Show keyboard layout';
$string['seb:manage_seb_showreloadbutton'] = 'Change SEB quiz setting: Show reload button';
$string['seb:manage_seb_showsebtaskbar'] = 'Change SEB quiz setting: Show task bar';
$string['seb:manage_seb_showtime'] = 'Change SEB quiz setting: Show time';
$string['seb:manage_seb_showwificontrol'] = 'Change SEB quiz setting: Show Wi-Fi control';
$string['seb:manage_seb_showsebdownloadlink'] = 'Change SEB quiz setting: Show download link';
$string['seb:manage_seb_templateid'] = 'Change SEB quiz setting: Select SEB template';
$string['seb:manage_seb_userconfirmquit'] = 'Change SEB quiz setting: Confirm on quit';
$string['seb:managetemplates'] = 'Manage SEB configuration templates';
$string['seb_activateurlfiltering'] = 'Enable URL filtering';
$string['seb_activateurlfiltering_help'] = 'If enabled, URLs will be filtered when loading web pages. The filter set has to be defined below.';
$string['seb_allowedbrowserexamkeys'] = 'Allowed browser exam keys';
$string['seb_allowedbrowserexamkeys_help'] = 'In this field you can enter the allowed browser exam keys for versions of Safe Exam Browser that are permitted to access this quiz. If no keys are entered, then browser exam keys are not checked.';
$string['seb_allowreloadinexam'] = 'Enable reload in exam';
$string['seb_allowreloadinexam_help'] = 'If enabled, page reload is allowed (reload button in SEB task bar, browser tool bar, iOS side slider menu, keyboard shortcut F5/cmd+R). Note that offline caching may break if a user tries to reload a page without an internet connection.';
$string['seb_allowspellchecking'] = 'Enable spell checking';
$string['seb_allowspellchecking_help'] = 'If enabled, spell checking in the SEB browser is allowed.';
$string['seb_allowuserquitseb'] = 'Enable quitting of SEB';
$string['seb_allowuserquitseb_help'] = 'If enabled, users can quit SEB with the "Quit" button in the SEB task bar or by pressing the keys Ctrl-Q or by clicking the main browser window close button.';
$string['seb_enableaudiocontrol'] = 'Enable audio controls';
$string['seb_enableaudiocontrol_help'] = 'If enabled, the audio control icon is shown in the SEB task bar.';
$string['seb_expressionsallowed'] = 'Expressions allowed';
$string['seb_expressionsallowed_help'] = 'A text field which contains the allowed filtering expressions for the allowed URLs. Use of the wildcard char \'\*\' is possible. Examples for expressions: \'example.com\' or \'example.com/stuff/\*\'. \'example.com\' matches \'example.com\', \'www.example.com\' and \'www.mail.example.com\'. \'example.com/stuff/\*\' matches all requests to any subdomain of \'example.com\' that have \'stuff\' as the first segment of the path.';
$string['seb_expressionsblocked'] = 'Expressions blocked';
$string['seb_expressionsblocked_help'] = 'A text field which contains the filtering expressions for the blocked URLs. Use of the wildcard char \'\*\' is possible. Examples for expressions: \'example.com\' or \'example.com/stuff/\*\'. \'example.com\' matches \'example.com\', \'www.example.com\' and \'www.mail.example.com\'. \'example.com/stuff/\*\' matches all requests to any subdomain of \'example.com\' that have \'stuff\' as the first segment of the path.';
$string['seb_filterembeddedcontent'] = 'Filter also embedded content';
$string['seb_filterembeddedcontent_help'] = 'If enabled, embedded resources will also be filtered using the filter set.';
$string['seb_help'] = 'Setup quiz to use the Safe Exam Browser.';
$string['seb_linkquitseb'] = 'Show Exit Safe Exam Browser button, configured with this quit link';
$string['seb_linkquitseb_help'] = 'In this field you can enter the link to quit SEB. It will be used on an "Exit Safe Exam Browser" button on the page that appears after the exam is submitted. When clicking the button or the link placed wherever you want to put it, it is possible to quit SEB without having to enter a quit password. If no link is entered, then the "Exit Safe Exam Browser" button does not appear and there is no link set to quit SEB.';
$string['seb_managetemplates'] = 'Manage Safe Exam Browser templates';
$string['seb_muteonstartup'] = 'Mute on startup';
$string['seb_muteonstartup_help'] = 'If enabled, audio is initially muted when starting SEB.';
$string['seb_quitpassword'] = 'Quit password';
$string['seb_quitpassword_help'] = 'This password is prompted when users try to quit SEB with the "Quit" button, Ctrl-Q or the close button in the main browser window. If no quit password is set, then SEB just prompts "Are you sure you want to quit SEB?".';
$string['seb_regexallowed'] = 'Regex allowed';
$string['seb_regexallowed_help'] = 'A text field which contains the filtering expressions for allowed URLs in a regular expression (Regex) format.';
$string['seb_regexblocked'] = 'Regex blocked';
$string['seb_regexblocked_help'] = 'A text field which contains the filtering expressions for blocked URLs in a regular expression (Regex) format.';
$string['seb_requiresafeexambrowser'] = 'Require the use of Safe Exam Browser';
$string['seb_requiresafeexambrowser_help'] = 'If enabled, students can only attempt the quiz using the Safe Exam Browser.
The available options are:

* No
<br/>Safe Exam Browser is not required to attempt the quiz.
* Yes – Use an existing template
<br/>A template for the configuration of Safe Exam Browser can be used. Templates are managed in the site administration. Your manual settings overwrite the settings in the template.
* Yes – Configure manually
<br/>No template for the configuration of Safe Exam Browser will be used. You can configure Safe Exam Browser manually.
* Yes – Upload my own config
<br/>You can upload your own Safe Exam Browser configuration file. All manual settings and the use of templates will be disabled.
* Yes – Use SEB client config
<br/>No configurations of Safe Exam Browser are on the Moodle side. The quiz can be attempted with any configuration of Safe Exam Browser.';
$string['seb_showkeyboardlayout'] = 'Show keyboard layout';
$string['seb_showkeyboardlayout_help'] = 'If enabled, the current keyboard layout is shown in the SEB task bar. It allows you to switch to other keyboard layouts, which have been enabled in the operating system.';
$string['seb_showreloadbutton'] = 'Show reload button';
$string['seb_showreloadbutton_help'] = 'If enabled, a reload button is displayed in the SEB task bar, allowing the current web page to be reloaded.';
$string['seb_showsebtaskbar'] = 'Show SEB task bar';
$string['seb_showsebtaskbar_help'] = 'If enabled, a task bar appears at the bottom of the SEB browser window. The task bar is required to display items such as Wi-Fi control, reload button, time and keyboard layout.';
$string['seb_showtime'] = 'Show time';
$string['seb_showtime_help'] = 'If enabled, the current time is displayed in the SEB task bar.';
$string['seb_showwificontrol'] = 'Show Wi-Fi control';
$string['seb_showwificontrol_help'] = 'If enabled, a Wi-Fi control button appears in the SEB task bar. The button allows users to reconnect to Wi-Fi networks which have previously been connected to.';
$string['seb_showsebdownloadlink'] = 'Show Safe Exam Browser download button';
$string['seb_showsebdownloadlink_help'] = 'If enabled, a button for Safe Exam Browser download will be shown on the quiz start page.';
$string['seb_templateid'] = 'Safe Exam Browser config template';
$string['seb_templateid_help'] = 'The settings in the selected config template will be used for the configuration of the Safe Exam Browser while attempting the quiz. You may overwrite the settings in the template with your manual settings.';
$string['seb_use_client'] = 'Yes – Use SEB client config';
$string['seb_use_manually'] = 'Yes – Configure manually';
$string['seb_use_template'] = 'Yes – Use an existing template';
$string['seb_use_upload'] = 'Yes – Upload my own config';
$string['seb_userconfirmquit'] = 'Ask user to confirm quitting';
$string['seb_userconfirmquit_help'] = 'If enabled, users have to confirm quitting of SEB when a quit link is detected.';
$string['sebdownloadbutton'] = 'Download Safe Exam Browser';
$string['seblinkbutton'] = 'Launch Safe Exam Browser';
$string['sebrequired'] = "This quiz has been configured so that students may only attempt it using the Safe Exam Browser.";
$string['setting:autoreconfigureseb'] = 'Auto-configure SEB';
$string['setting:autoreconfigureseb_desc'] = 'If enabled, users who navigate to the quiz using the Safe Exam Browser will be automatically forced to reconfigure their Safe Exam Browser.';
$string['setting:displayblocksbeforestart'] = 'Display blocks before starting quiz';
$string['setting:displayblocksbeforestart_desc'] = 'If enabled, blocks will be displayed before a user attempts the quiz.';
$string['setting:displayblockswhenfinished'] = 'Display blocks after finishing quiz';
$string['setting:displayblockswhenfinished_desc'] = 'If enabled, blocks will be displayed after a user has finished their quiz attempt.';
$string['setting:downloadlink'] = 'Safe Exam Browser download link';
$string['setting:downloadlink_desc'] = 'URL for downloading the Safe Exam Browser application.';
$string['setting:quizpasswordrequired'] = 'Quiz password required';
$string['setting:quizpasswordrequired_desc'] = 'If enabled, all quizzes that require the Safe Exam Browser must have a quiz password set.';
$string['setting:showhttplink'] = 'Show http:// link';
$string['setting:showseblink'] = 'Show seb:// link';
$string['setting:showseblinks'] = 'Show Safe Exam Browser config links';
$string['setting:showseblinks_desc'] = 'Whether to show links for a user to access the Safe Exam Browser configuration file when access to the quiz is prevented. Note that seb:// links may not work in every browser.';
$string['setting:supportedversions'] = 'Please note that the following minimum versions of the Safe Exam Browser client are required to use the config key feature: macOS - 2.1.5pre2, Windows - 3.0, iOS - 2.1.14.';
$string['settingsfrozen'] = 'Due to there being at least one quiz attempt, the Safe Exam Browser settings can no longer be updated.';
$string['unknown_reason'] = "Unknown reason";
$string['used'] = 'In use';
