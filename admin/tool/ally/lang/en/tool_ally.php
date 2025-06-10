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
 * Language definitions.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2016 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['adminurl'] = 'Launch URL';
$string['adminurldesc'] = 'The LTI launch URL used to access the Accessibility report.';
$string['allyclientconfig'] = 'Ally configuration';
$string['ally:clientconfig'] = 'Access and update client configuration';
$string['ally:viewlogs'] = 'Ally logs viewer';
$string['clientid'] = 'Client id';
$string['clientiddesc'] = 'The Ally client id';
$string['code'] = 'Code';
$string['contentauthors'] = 'Content authors';
$string['contentauthorsdesc'] = 'Administrators and users assigned to these selected roles will have their uploaded course files evaluated for accessibility. The files are given an accessibility rating. Low ratings mean that the file needs changes to be more accessible.';
$string['contentupdatestask'] = 'Content updates task';
$string['curlerror'] = 'cURL error: {$a}';
$string['curlinvalidhttpcode'] = 'Invalid HTTP status code: {$a}';
$string['curlnohttpcode'] = 'Unable to verify HTTP status code';
$string['error:invalidcomponentident'] = 'Invalid component identifier {$a}';
$string['error:pluginfilequestiononly'] = 'Only question components are supported for this url';
$string['error:componentcontentnotfound'] = 'Content not found for {$a}';
$string['error:wstokenmissing'] = 'Web service token is missing. Maybe an admin user needs to run auto configuration?';
$string['excludeunused'] = 'Exclude unused files';
$string['excludeunuseddesc'] = 'Omit files that are attached to HTML content, but linked/references in the HTML.';
$string['filecoursenotfound'] = 'The passed in file does not belong to any course';
$string['fileupdatestask'] = 'Push file updates to Ally';
$string['id'] = 'Id';
$string['key'] = 'Key';
$string['keydesc'] = 'The LTI consumer key.';
$string['level'] = 'Level';
$string['message'] = 'Message';
$string['pluginname'] = 'Ally';
$string['pushurl'] = 'File updates URL';
$string['pushurldesc'] = 'Push notifications about file updates to this URL.';
$string['queuesendmessagesfailure'] = 'An error occurred while sending messages to the AWS SQS. Error data: $a';
$string['secret'] = 'Secret';
$string['secretdesc'] = 'The LTI secret.';
$string['showdata'] = 'Show data';
$string['hidedata'] = 'Hide data';
$string['showexplanation'] = 'Show explanation';
$string['hideexplanation'] = 'Hide explanation';
$string['showexception'] = 'Show exception';
$string['hideexception'] = 'Hide exception';
$string['usercapabilitymissing'] = 'The supplied user does not have the capability to delete this file.';
$string['autoconfigure'] = 'Auto configure Ally web service';
$string['autoconfiguredesc'] = 'Automatically create web service role and user for ally.';
$string['autoconfigureconfirmation'] = 'Automatically create web service role and user for ally and enable web service. The following actions will be carried out: <ul><li>create a role entitled \'ally_webservice\' and a user with the username \'ally_webuser\'</li><li>add the \'ally_webuser\' user to the \'ally_webservice\' role</li><li>enable web services</li><li>enable the rest web service protocol</li><li>enable the ally web service</li><li>create a token for the \'ally_webuser\' account</li></ul>';
$string['autoconfigsuccess'] = 'Success - the Ally web service has been automatically configured.';
$string['autoconfigtoken'] = 'The web service token is as follows:';
$string['autoconfigapicall'] = 'You can test that the webservice is working via the following url:';
$string['privacy:metadata:files:action'] = 'The action taken on the file, EG: created, updated or deleted.';
$string['privacy:metadata:files:contenthash'] = 'The file\'s content hash in order to determine uniqueness.';
$string['privacy:metadata:files:courseid'] = 'The course ID that the file belongs to.';
$string['privacy:metadata:files:externalpurpose'] = 'In order to integrate with Ally, files need to be exchanged with Ally.';
$string['privacy:metadata:files:filecontents'] = 'The actual file\'s content is sent to Ally in order to evaluate it for accessibility.';
$string['privacy:metadata:files:mimetype'] = 'The file MIME type, EG: text/plain, image/jpeg, etc.';
$string['privacy:metadata:files:pathnamehash'] = 'The file\'s path name hash to uniquely identify it.';
$string['privacy:metadata:files:timemodified'] = 'The time when the field was last modified.';
$string['cachedef_annotationmaps'] = 'Store annotation data for courses';
$string['cachedef_fileinusecache'] = 'Ally files in use cache';
$string['cachedef_pluginfilesinhtml'] = 'Ally files in HTML cache';
$string['cachedef_request'] = 'Ally filter request cache';
$string['pushfilessummary'] = 'Ally file updates summary.';
$string['pushfilessummary:explanation'] = 'Summary of file updates sent to Ally.';

$string['section'] = 'Section {$a}';
$string['lessonanswertitle'] = 'Answer for lesson "{$a}"';
$string['lessonresponsetitle'] = 'Response for lesson "{$a}"';
$string['logs'] = 'Ally logs';

$string['logrange'] = 'Log range';
$string['loglevel:none'] = 'None';
$string['loglevel:light'] = 'Light';
$string['loglevel:medium'] = 'Medium';
$string['loglevel:all'] = 'All';

$string['logcleanuptask'] = 'Ally log cleanup task';
$string['loglifetimedays'] = 'Keep logs for this many days';
$string['loglifetimedaysdesc'] = 'Retain Ally logs for this many days. Set to 0 to never delete logs. A scheduled task is (by default) set to run daily, and will remove log entries that are more than this many days old.';

$string['logger:filtersetupdebugger'] = 'Ally filter setup log';

$string['logger:pushtoallysuccess'] = 'Successful push to ally end point';
$string['logger:pushtoallyfail'] = 'Unsuccessful push to ally end point';

$string['logger:pushfilesuccess'] = 'Successful push of file(s) to ally end point';
$string['logger:pushfileliveskip'] = 'Live file push failure';
$string['logger:pushfileliveskip_exp'] = 'Skipping live file(s) push due to communication problems. Live file push will be restored when file updates task is successful. Please, review your configuration.';
$string['logger:pushfileserror'] = 'Unsuccessful push to ally end point';
$string['logger:pushfileserror_exp'] = 'Errors associated with content updates push to Ally services.';

$string['logger:pushcontentsuccess'] = 'Successful push of content to ally end point';
$string['logger:pushcontentliveskip'] = 'Live content push failure';
$string['logger:pushcontentliveskip_exp'] = 'Skipping live content push due to communication problems. Live content push will be restored when content updates task is successful. Please, review your configuration.';
$string['logger:pushcontentserror'] = 'Unsuccessful push to ally end point';
$string['logger:pushcontentserror_exp'] = 'Errors associated with content updates push to Ally services.';

$string['logger:addingconenttoqueue'] = 'Adding content to push queue';
$string['logger:annotationmoderror'] = 'Ally module content annotation failed.';
$string['logger:annotationmoderror_exp'] = 'Module was not correctly identified.';

$string['logger:failedtogetcoursesectionname'] = 'Failed to get course section name';

$string['logger:moduleidresolutionfailure'] = 'Failed to resolve module id';
$string['logger:cmidresolutionfailure'] = 'Failed to resolve course module id';
$string['logger:cmvisibilityresolutionfailure'] = 'Failed to resolve course module visibility';

$string['courseupdatestask'] = 'Push course events to ally';
$string['logger:pushcoursesuccess'] = 'Successful push of course event(s) to ally end point';
$string['logger:pushcourseliveskip'] = 'Live course event push failure';
$string['logger:pushcourseerror'] = 'Live course event push failure';
$string['logger:pushcourseliveskip_exp'] = 'Skipping live course event(s) push due to communication problems. Live course event push will be restored when course event updates task is successful. Please, review your configuration.';
$string['logger:pushcourseserror'] = 'Unsuccessful push to ally end point';
$string['logger:pushcourseserror_exp'] = 'Errors associated with course updates push to Ally services.';
$string['logger:addingcourseevttoqueue'] = 'Adding course event to push queue';

$string['logger:cmiderraticpremoddelete'] = 'Course module id has problems pre-deleting it.';
$string['logger:cmiderraticpremoddelete_exp'] = 'Module was not correctly identified, either it does not exist due to section deletion or there is other factor which triggered the deletion hook and it not being found.';

$string['logger:servicefailure'] = 'Failed when consuming service.';
$string['logger:servicefailure_exp'] = '<br>Class: {$a->class}<br>Params: {$a->params}';

$string['logger:autoconfigfailureteachercap'] = 'Failed when assigning a teacher archetype capability to the ally_webservice role.';
$string['logger:autoconfigfailureteachercap_exp'] = '<br>Capability: {$a->cap}<br>Permission: {$a->permission}';

$string['deferredcourseevents'] = 'Send deferred course events';
$string['deferredcourseeventsdesc'] = 'Allow sending of stored course events, which were accumulated during communication failure with Ally';
