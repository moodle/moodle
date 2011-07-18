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
 * Strings for component 'scorm', language 'en', branch 'MOODLE_20_STABLE'
 *
 * @package   scorm
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
$string['toc'] = 'TOC';
$string['navigation'] = 'Navigation';

$string['activation'] = 'Activation';
$string['activityloading'] = 'You will be automatically redirected to the activity in';
$string['activitypleasewait'] = 'Activity loading, please wait ...';
$string['advanced'] = 'Parameters';
$string['allowapidebug'] = 'Activate API debug and tracing (set the capture mask with apidebugmask)';
$string['allowtypeexternal'] = 'Enable external package type';
$string['allowtypeimsrepository'] = 'Enable IMS package type';
$string['allowtypelocalsync'] = 'Enable downloaded package type';
$string['apidebugmask'] = 'API debug capture mask  - use a simple regex on &lt;username&gt;:&lt;activityname&gt; e.g. admin:.* will debug for admin user only';
$string['areacontent'] = 'Content files';
$string['areapackage'] = 'Package file';
$string['asset'] = 'Asset';
$string['assetlaunched'] = 'Asset - Viewed';
$string['attempt'] = 'attempt';
$string['attempts'] = 'Attempts';
$string['attemptsx'] = '{$a} attempts';
$string['attempt1'] = '1 attempt';
$string['attr_error'] = 'Bad value for attribute ({$a->attr}) in tag {$a->tag}.';
$string['autocontinue'] = 'Auto-continue';
$string['autocontinue_help'] = 'If enabled, subsequent learning objects are launched automatically, otherwise the Continue button must be used.';
$string['autocontinuedesc'] = 'This preference sets the default auto continue for the activity';
$string['averageattempt'] = 'Average attempts';
$string['badmanifest'] = 'Some manifest errors: see errors log';
$string['badpackage'] = 'The specified package/manifest is not valid. Check it and try again.';
$string['browse'] = 'Preview';
$string['browsed'] = 'Browsed';
$string['browsemode'] = 'Preview mode';
$string['browserepository'] = 'Browse repository';
$string['cannotfindsco'] = 'Could not find SCO';
$string['completed'] = 'Completed';
$string['confirmloosetracks'] = 'WARNING: The package seems to be changed or modified. If the package structure is changed, some users tracks may be lost during update process.';
$string['contents'] = 'Contents';
$string['coursepacket'] = 'Course package';
$string['coursestruct'] = 'Course structure';
$string['currentwindow'] = 'Current window';
$string['datadir'] = 'Filesystem error: Can\'t create course data directory';
$string['deleteattemptcheck'] = 'Are you absolutely sure you want to completely delete these attempts?';
$string['deleteallattempts'] = 'Delete all SCORM attempts';
$string['details'] = 'Track details';
$string['directories'] = 'Show the directory links';
$string['disabled'] = 'Disabled';
$string['display'] = 'Display package';
$string['displayattemptstatus'] = 'Display attempt status';
$string['displayattemptstatus_help'] = 'If enabled, scores and grades for attempts are displayed on the SCORM outline page.';
$string['displayattemptstatusdesc'] = 'This preference sets the default value for the display attempt status setting';
$string['displaycoursestructure'] = 'Display course structure on entry page';
$string['displaycoursestructure_help'] = 'If enabled, the table of contents is displayed on the SCORM outline page.';
$string['displaycoursestructuredesc'] = 'This preference sets the default value for the display course structure on entry page setting';
$string['displaydesc'] = 'This preference sets the default of whether to display the package or not for an activity';
$string['domxml'] = 'DOMXML external library';
$string['duedate'] = 'Due date';
$string['element'] = 'Element';
$string['enter'] = 'Enter';
$string['entercourse'] = 'Enter course';
$string['errorlogs'] = 'Errors log';
$string['everyday'] = 'Every day';
$string['everytime'] = 'Every time it\'s used';
$string['exceededmaxattempts'] = 'You have reached the maximum number of attempts.';
$string['exit'] = 'Exit course';
$string['exitactivity'] = 'Exit activity';
$string['expired'] = 'Sorry, this activity closed on {$a} and is no longer available';
$string['external'] = 'Update external packages timing';
$string['failed'] = 'Failed';
$string['finishscorm'] = 'If you have finished viewing this resource, {$a}';
$string['finishscormlinkname'] = 'click here to return to the course page';
$string['firstaccess'] = 'First access';
$string['firstattempt'] = 'First attempt';
$string['forcecompleted'] = 'Force completed';
$string['forcecompleted_help'] = 'If enabled, the status of the current attempt is forced to "completed". This setting is only applicable to SCORM 1.2 packages. It is useful if the SCORM package does not handle revisiting an attempt correctly, in review or browse mode, or otherwise incorrectly issues the completion status.';
$string['forcecompleteddesc'] = 'This preference sets the default value for the force completed setting';
$string['forcenewattempt'] = 'Force new attempt';
$string['forcenewattempt_help'] = 'If enabled, each time a SCORM package is accessed will be counted as a new attempt.';
$string['forcenewattemptdesc'] = 'This preference sets the default value for the force new attempt setting';
$string['forcejavascript'] = 'Force users to enable JavaScript';
$string['forcejavascript_desc'] = 'If enabled(recommended) this prevents access to SCORM objects when JavaScript is not supported/enabled in a users browser. If disabled the user may view the SCORM but API communication will fail and no grade information will be saved.';
$string['forcejavascriptmessage'] = 'JavaScript is required to view this object, please enable JavaScript in your browser and try again.';
$string['found'] = 'Manifest found';
$string['frameheight'] = 'This preference set the default height for stage frame or window';
$string['framewidth'] = 'This preference set the default width for stage frame or window';
$string['fullscreen'] = 'Fill the whole screen';
$string['general'] = 'General data';
$string['gradeaverage'] = 'Average grade';
$string['gradeforattempt'] = 'Grade for attempt';
$string['gradehighest'] = 'Highest grade';
$string['grademethod'] = 'Grading method';
$string['grademethod_help'] = 'The grading method defines how the grade for a single attempt of the activity is determined.

There are 4 grading methods:

* Learning objects - The number of completed/passed learning objects
* Highest grade - The highest score obtained in all passed learning objects
* Average grade - The mean of all the scores
* Sum grade - The sum of all the scores';
$string['grademethoddesc'] = 'This preference sets the default grade method for an activity';
$string['gradereported'] = 'Grade reported';
$string['gradescoes'] = 'Learning objects';
$string['gradesum'] = 'Sum grade';
$string['height'] = 'Height';
$string['hidden'] = 'Hidden';
$string['hidebrowse'] = 'Disable preview mode';
$string['hidebrowse_help'] = 'Preview mode allows a student to browse an activity before attempting it. If preview mode is disabled, the preview button is hidden.';
$string['hidebrowsedesc'] = 'This preference sets the default for whether to disable or enable the preview mode';
$string['hideexit'] = 'Hide exit link';
$string['hidenav'] = 'Hide navigation buttons';
$string['hidenavdesc'] = 'This preference sets the default for whether to show or hide the navigation buttons';
$string['hidereview'] = 'Hide review button';
$string['hidetoc'] = 'Display course structure in player';
$string['hidetoc_help'] = 'This setting specifies how the table of contents is displayed in the SCORM player.';
$string['hidetocdesc'] = 'This preference sets the default for whether to show or hide the course structure (TOC) in the SCORM player';
$string['highestattempt'] = 'Highest attempt';
$string['chooseapacket'] = 'Choose or update a package';
$string['identifier'] = 'Question identifier';
$string['incomplete'] = 'Incomplete';
$string['info'] = 'Info';
$string['interactions'] = 'Interactions';
$string['invalidactivity'] = 'Scorm activity is incorrect';
$string['last'] = 'Last accessed on';
$string['lastaccess'] = 'Last access';
$string['lastattempt'] = 'Last attempt';
$string['lastattemptlock'] = 'Lock after final attempt';
$string['lastattemptlock_help'] = 'If enabled, a student is prevented from launching the SCORM player after using up all their allocated attempts.';
$string['lastattemptlockdesc'] = 'This preference sets the default value for the lock after final attempt setting';
$string['location'] = 'Show the location bar';
$string['max'] = 'Max score';
$string['maximumattempts'] = 'Number of attempts';
$string['maximumattempts_help'] = 'This setting enables the number of attempts to be restricted. It is only applicable for SCORM 1.2 and AICC packages.';
$string['maximumattemptsdesc'] = 'This preference sets the default maximum attempts for an activity';
$string['maximumgradedesc'] = 'This preference sets the default maximum grade for an activity';
$string['menubar'] = 'Show the menu bar';
$string['min'] = 'Min score';
$string['missing_attribute'] = 'Missing attribute {$a->attr} in tag {$a->tag}';
$string['missingparam'] = 'A required is missing or wrong';
$string['missing_tag'] = 'Missing tag {$a->tag}';
$string['mode'] = 'Mode';
$string['modulename'] = 'SCORM package';
$string['modulename_help'] = 'SCORM and AICC are a collection of specifications that enable interoperability, accessibility and reusability of web-based learning content. The SCORM/AICC module allows for SCORM/AICC packages to be included in the course.';
$string['modulenameplural'] = 'SCORM packages';
$string['newattempt'] = 'Start a new attempt';
$string['next'] = 'Continue';
$string['noactivity'] = 'Nothing to report';
$string['noattemptsallowed'] = 'Number of attempts allowed';
$string['noattemptsmade'] = 'Number of attempts you have made';
$string['no_attributes'] = 'Tag {$a->tag} must have attributes';
$string['no_children'] = 'Tag {$a->tag} must have children';
$string['nolimit'] = 'Unlimited attempts';
$string['nomanifest'] = 'Manifest not found';
$string['noprerequisites'] = 'Sorry but you haven\'t reached enough prerequisites to access this learning object';
$string['noreports'] = 'No report to display';
$string['normal'] = 'Normal';
$string['noscriptnoscorm'] = 'Your browser does not support JavaScript or it has JavaScript support disabled. This SCORM package may not play or save data correctly.';
$string['notattempted'] = 'Not attempted';
$string['not_corr_type'] = 'Type mismatch for tag {$a->tag}';
$string['notopenyet'] = 'Sorry, this activity is not available until {$a}';
$string['objectives'] = 'Objectives';
$string['onchanges'] = 'Whenever it changes';
$string['optallstudents'] = 'all users';
$string['optattemptsonly'] = 'users with attempts only';
$string['optnoattemptsonly'] = 'users with no attempts only';
$string['options'] = 'Options (Prevented by some browsers)';
$string['organization'] = 'Organization';
$string['organizations'] = 'Organizations';
$string['othersettings'] = 'Additional settings';
$string['othertracks'] = 'Other tracks';
$string['page-mod-scorm-x'] = 'Any SCORM module page';
$string['pagesize'] = 'Page size';
$string['package'] = 'Package file';
$string['package_help'] = 'The package file is a zip (or pif) file containing SCORM/AICC course definition files.';
$string['packagedir'] = 'Filesystem error: Can\'t create package directory';
$string['packagefile'] = 'No package file specified';
$string['packageurl'] = 'URL';
$string['packageurl_help'] = 'This setting enables a URL for the SCORM package to be specified, rather than choosing a file via the file picker.';
$string['passed'] = 'Passed';
$string['php5'] = 'PHP 5 (DOMXML native library)';
$string['pluginadministration'] = 'SCORM/AICC administration';
$string['pluginname'] = 'SCORM package';
$string['popup'] = 'New window';
$string['popupmenu'] = 'In a drop down menu';
$string['popupopen'] = 'Open package in a new window';
$string['popupsblocked'] = 'It appears that popup windows are blocked, stopping this scorm module from playing. Please check your browser settings, before starting again.';
$string['position_error'] = 'The {$a->tag} tag can\'t be child of {$a->parent} tag';
$string['preferencesuser'] = 'Preferences for this report';
$string['preferencespage'] = 'Preferences just for this page';
$string['prev'] = 'Previous';
$string['raw'] = 'Raw score';
$string['regular'] = 'Regular manifest';
$string['report'] = 'Report';
$string['reportcountallattempts'] = '{$a->nbattempts} attempts for {$a->nbusers} users, out of {$a->nbresults} results';
$string['reportcountattempts'] = '{$a->nbresults} results ({$a->nbusers} users)';
$string['resizable'] = 'Allow the window to be resized';
$string['result'] = 'Result';
$string['results'] = 'Results';
$string['review'] = 'Review';
$string['reviewmode'] = 'Review mode';
$string['scoes'] = 'Learning objects';
$string['score'] = 'Score';
$string['scormclose'] = 'Until';
$string['scormcourse'] = 'Learning course';
$string['scorm:deleteresponses'] = 'Delete SCORM attempts';
$string['scormloggingoff'] = 'API logging is off';
$string['scormloggingon'] = 'API logging is on';
$string['scormopen'] = 'Open';
$string['scormresponsedeleted'] = 'Deleted user attempts';
$string['scorm:savetrack'] = 'Save tracks';
$string['scorm:skipview'] = 'Skip overview';
$string['scormtype'] = 'Type';
$string['scormtype_help'] = 'This setting determines how the package is included in the course. There are up to 4 options:

* Uploaded package - Enables a SCORM package to be chosen via the file picker
* External SCORM manifest - Enables an imsmanifest.xml URL to be specified. Note: If the URL has a different domain name than your site, then "Downloaded package" is a better option, since otherwise grades are not saved.
* Downloaded package - Enables a package URL to be specified. The package will be unzipped and saved locally, and updated when the external SCORM package is updated.
* Local IMS content repository - Enables a package to be selected from within an IMS repository';
$string['scorm:viewreport'] = 'View reports';
$string['scorm:viewscores'] = 'View scores';
$string['scrollbars'] = 'Allow the window to be scrolled';
$string['selectall'] = 'Select all';
$string['selectnone'] = 'Deselect all';
$string['show'] = 'Show';
$string['sided'] = 'To the side';
$string['skipview'] = 'Student skip content structure page';
$string['skipview_help'] = 'This setting specifies whether the content structure page should ever be skipped (not displayed). If the package contains only one learning object, the content structure page can always be skipped.';
$string['skipviewdesc'] = 'This preference sets the default for when to skip content structure for a page';
$string['slashargs'] = 'WARNING: slash arguments is disabled on this site and objects may not function as expected!';
$string['stagesize'] = 'Stage size';
$string['stagesize_help'] = 'These two settings specify the frame/window width and height for the learning objects.';
$string['started'] = 'Started on';
$string['status'] = 'Status';
$string['statusbar'] = 'Show the status bar';
$string['student_response'] = 'Response';
$string['suspended'] = 'Suspended';
$string['syntax'] = 'Syntax error';
$string['tag_error'] = 'Unknown tag ({$a->tag}) with this content: {$a->value}';
$string['time'] = 'Time';
$string['timerestrict'] = 'Restrict answering to this time period';
$string['title'] = 'Title';
$string['toolbar'] = 'Show the toolbar';
$string['too_many_attributes'] = 'Tag {$a->tag} has too many attributes';
$string['too_many_children'] = 'Tag {$a->tag} has too many children';
$string['totaltime'] = 'Time';
$string['trackingloose'] = 'WARNING: The tracking data of this package will be lost!';
$string['type'] = 'Type';
$string['typeexternal'] = 'External SCORM manifest';
$string['typeimsrepository'] = 'Local IMS content repository';
$string['typelocal'] = 'Uploaded package';
$string['typelocalsync'] = 'Downloaded package';
$string['unziperror'] = 'An error occurs during package unzip';
$string['updatefreq'] = 'Auto-update frequency';
$string['updatefreqdesc'] = 'This preference sets the default auto-update frequency of an activity';
$string['validateascorm'] = 'Validate a package';
$string['validation'] = 'Validation result';
$string['validationtype'] = 'This preference set the DOMXML library used for validating SCORM Manifest. If you don\'t know leave the selected choice.';
$string['value'] = 'Value';
$string['versionwarning'] = 'The manifest version is older than 1.3, warning at {$a->tag} tag';
$string['viewallreports'] = 'View reports for {$a} attempts';
$string['viewalluserreports'] = 'View reports for {$a} users';
$string['whatgrade'] = 'Attempts grading';
$string['whatgrade_help'] = 'If multiple attempts are allowed, this setting specifies whether the highest, average (mean), first or last attempt is recorded in the gradebook.

Handling of Multiple Attempts

* The option to start a new attempt is provided by a checkbox above the Enter button on the content structure page, so be sure you\'re providing access to that page if you want to allow more than one attempt.
* Some scorm packages are intelligent about new attempts, many are not. What this means is that if the learner re-enters an existing attempt, if the SCORM content does not have internal logic to avoid overwriting previous attempts they can be overwritten, even though the attempt was \'completed\' or \'passed\'.
* The settings "Force completed", "Force new attempt" and "Lock after final attempt" also provide further management of multiple attempts.';
$string['whatgradedesc'] = 'This preference sets the default attempts grading';
$string['width'] = 'Width';
$string['window'] = 'Window';
$string['zlibwarning'] = 'Warning: PHP Zlib compression has been enabled on this site, some users may experience issues loading SCORM objects in certain web browsers.';
