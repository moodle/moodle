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
 * Plugin strings are defined here.
 *
 * @package     tool_iomadpolicy
 * @category    string
 * @copyright   2018 David Mudrák <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['acceptanceacknowledgement'] = 'I acknowledge that I have received a request to give consent on behalf of the above user(s).';
$string['acceptancenote'] = 'Remarks';
$string['acceptancepolicies'] = 'Policies';
$string['acceptancessavedsucessfully'] = 'The agreements have been saved successfully.';
$string['acceptancestatusaccepted'] = 'Accepted';
$string['acceptancestatusacceptedbehalf'] = 'Accepted on user\'s behalf';
$string['acceptancestatusdeclined'] = 'Declined';
$string['acceptancestatusdeclinedbehalf'] = 'Declined on user\'s behalf';
$string['acceptancestatusoverall'] = 'Overall';
$string['acceptancestatuspartial'] = 'Partially accepted';
$string['acceptancestatuspending'] = 'Pending';
$string['acceptanceusers'] = 'Users';
$string['actions'] = 'Actions';
$string['activate'] = 'Set status to "Active"';
$string['activating'] = 'Activating a iomadpolicy';
$string['activateconfirm'] = '<p>You are about to activate iomadpolicy <em>\'{$a->name}\'</em> and make the version <em>\'{$a->revision}\'</em> the current one.</p><p>All users will be required to agree to this new iomadpolicy version to be able to use the site.</p>';
$string['activateconfirmyes'] = 'Activate';
$string['agreepolicies'] = 'Please agree to the following policies';
$string['backtoprevious'] = 'Go back to previous page';
$string['backtotop'] = 'Back to top';
$string['cachedef_iomadpolicy_optional'] = 'Cache of the optional/compulsory flag for iomadpolicy versions';
$string['consentbulk'] = 'Consent';
$string['consentpagetitle'] = 'Consent';
$string['contactdpo'] = 'For any questions about the policies please contact the privacy officer.';
$string['dataproc'] = 'Personal data processing';
$string['declineacknowledgement'] = 'I acknowledge that I have received a request to decline consent on behalf of the above user(s).';
$string['declinetheiomadpolicy'] = 'Decline user consent';
$string['deleting'] = 'Deleting a version';
$string['deleteconfirm'] = '<p>Are you sure you want to delete iomadpolicy <em>\'{$a->name}\'</em>?</p><p>This operation can not be undone.</p>';
$string['editingiomadpolicydocument'] = 'Editing iomadpolicy';
$string['erroriomadpolicyversioncompulsory'] = 'Compulsory policies cannot be declined!';
$string['erroriomadpolicyversionnotfound'] = 'There isn\'t any iomadpolicy version with this identifier.';
$string['errorsaveasdraft'] = 'Minor change can not be saved as draft';
$string['errorusercantviewiomadpolicyversion'] = 'The user doesn\'t have access to this iomadpolicy version.';
$string['event_acceptance_created'] = 'User iomadpolicy agreement created';
$string['event_acceptance_updated'] = 'User iomadpolicy agreement updated';
$string['filtercapabilityno'] = 'Permission: Can not agree';
$string['filtercapabilityyes'] = 'Permission: Can agree';
$string['filterrevision'] = 'Version: {$a}';
$string['filterrevisionstatus'] = 'Version: {$a->name} ({$a->status})';
$string['filterrole'] = 'Role: {$a}';
$string['filters'] = 'Filters';
$string['filterstatusdeclined'] = 'Status: Declined';
$string['filterstatuspending'] = 'Status: Pending';
$string['filterstatusyes'] = 'Status: Agreed';
$string['filterplaceholder'] = 'Search keyword or select filter';
$string['filteriomadpolicy'] = 'Policy: {$a}';
$string['guestconsent:continue'] = 'Continue';
$string['guestconsentmessage'] = 'If you continue browsing this website, you agree to our policies:';
$string['iagree'] = 'I agree to the {$a}';
$string['idontagree'] = 'No thanks, I decline {$a}';
$string['iagreetotheiomadpolicy'] = 'Give consent';
$string['inactivate'] = 'Set status to "Inactive"';
$string['inactivating'] = 'Inactivating a iomadpolicy';
$string['inactivatingconfirm'] = '<p>You are about to inactivate iomadpolicy <em>\'{$a->name}\'</em> version <em>\'{$a->revision}\'</em>.</p>';
$string['inactivatingconfirmyes'] = 'Inactivate';
$string['invalidversionid'] = 'There is no iomadpolicy with this identifier!';
$string['irevoketheiomadpolicy'] = 'Withdraw user consent';
$string['listactivepolicies'] = 'List of active policies';
$string['minorchange'] = 'Minor change';
$string['minorchangeinfo'] = 'A minor change does not alter the meaning of the iomadpolicy. Users are not required to agree to the iomadpolicy again if the edit is marked as a minor change.';
$string['managepolicies'] = 'Manage policies';
$string['movedown'] = 'Move down';
$string['moveup'] = 'Move up';
$string['mustagreetocontinue'] = 'Before continuing you need to acknowledge all these policies.';
$string['newiomadpolicy'] = 'New iomadpolicy';
$string['newversion'] = 'New version';
$string['noactivepolicies'] = 'There are no policies with an active version.';
$string['nofiltersapplied'] = 'No filters applied';
$string['nopermissiontoagreedocs'] = 'No permission to agree to the policies';
$string['nopermissiontoagreedocs_desc'] = 'Sorry, you do not have the required permissions to agree to the policies.<br />You will not be able to use this site until the following policies are agreed:';
$string['nopermissiontoagreedocsbehalf'] = 'No permission to agree to the policies on behalf of this user';
$string['nopermissiontoagreedocsbehalf_desc'] = 'Sorry, you do not have the required permission to agree to the following policies on behalf of {$a}:';
$string['nopermissiontoagreedocscontact'] = 'For further assistance, please contact';
$string['nopermissiontoviewiomadpolicyversion'] = 'You do not have permissions to view this iomadpolicy version.';
$string['nopolicies'] = 'There are no policies for registered users with an active version.';
$string['selectiomadpolicyandversion'] = 'Use the filter above to select iomadpolicy and/or version';
$string['steppolicies'] = 'Policy {$a->numiomadpolicy} out of {$a->totalpolicies}';
$string['pluginname'] = 'IOMAD Policies';
$string['policiesagreements'] = 'Policies and agreements';
$string['iomadpolicy:accept'] = 'Agree to the policies';
$string['iomadpolicy:acceptbehalf'] = 'Give consent for policies on someone else\'s behalf';
$string['iomadpolicy:managedocs'] = 'Manage policies';
$string['iomadpolicy:viewacceptances'] = 'View user agreement reports';
$string['iomadpolicydocaudience'] = 'User consent';
$string['iomadpolicydocaudience0'] = 'All users';
$string['iomadpolicydocaudience1'] = 'Authenticated users';
$string['iomadpolicydocaudience2'] = 'Guests';
$string['iomadpolicydoccontent'] = 'Full iomadpolicy';
$string['iomadpolicydochdriomadpolicy'] = 'Policy';
$string['iomadpolicydochdrversion'] = 'Document version';
$string['iomadpolicydocname'] = 'Name';
$string['iomadpolicydocoptional'] = 'Agreement optional';
$string['iomadpolicydocoptionalyes'] = 'Optional';
$string['iomadpolicydocoptionalno'] = 'Compulsory';
$string['iomadpolicydocrevision'] = 'Version';
$string['iomadpolicydocsummary'] = 'Summary';
$string['iomadpolicydocsummary_help'] = 'This text should provide a summary of the iomadpolicy, potentially in a simplified and easily accessible form, using clear and plain language.';
$string['iomadpolicydoctype'] = 'Type';
$string['iomadpolicydoctype0'] = 'Site iomadpolicy';
$string['iomadpolicydoctype1'] = 'Privacy iomadpolicy';
$string['iomadpolicydoctype2'] = 'Third parties iomadpolicy';
$string['iomadpolicydoctype99'] = 'Other iomadpolicy';
$string['iomadpolicydocuments'] = 'Policy documents';
$string['iomadpolicynamedversion'] = 'Policy {$a->name} (version {$a->revision} - {$a->id})';
$string['iomadpolicypriorityagreement'] = 'Show iomadpolicy before showing other policies';
$string['iomadpolicyversionacceptedinbehalf'] = 'Consent for this iomadpolicy has been given on your behalf.';
$string['iomadpolicyversionacceptedinotherlang'] = 'Consent for this iomadpolicy version has been given in a different language.';
$string['previousversions'] = '{$a} previous versions';
$string['privacy:metadata:acceptances'] = 'Information about iomadpolicy agreements made by users.';
$string['privacy:metadata:acceptances:iomadpolicyversionid'] = 'The version of the iomadpolicy for which consent was given.';
$string['privacy:metadata:acceptances:userid'] = 'The user for whom this iomadpolicy agreement relates to.';
$string['privacy:metadata:acceptances:status'] = 'The status of the agreement.';
$string['privacy:metadata:acceptances:lang'] = 'The language used to display the iomadpolicy when consent was given.';
$string['privacy:metadata:acceptances:usermodified'] = 'The user who gave consent for the iomadpolicy, if made on behalf of another user.';
$string['privacy:metadata:acceptances:timecreated'] = 'The time when the user agreed to the iomadpolicy.';
$string['privacy:metadata:acceptances:timemodified'] = 'The time when the user updated their agreement.';
$string['privacy:metadata:acceptances:note'] = 'Any comments added by a user when giving consent on behalf of another user.';
$string['privacy:metadata:subsystem:corefiles'] = 'The iomadpolicy tool stores files included in the summary and full iomadpolicy.';
$string['privacy:metadata:versions'] = 'Policy version information.';
$string['privacy:metadata:versions:name'] = 'The name of the iomadpolicy.';
$string['privacy:metadata:versions:type'] = 'Policy type.';
$string['privacy:metadata:versions:audience'] = 'The type of users required to give their consent.';
$string['privacy:metadata:versions:archived'] = 'The iomadpolicy status (active or inactive).';
$string['privacy:metadata:versions:usermodified'] = 'The user who modified the iomadpolicy.';
$string['privacy:metadata:versions:timecreated'] = 'The time that this version of the iomadpolicy was created.';
$string['privacy:metadata:versions:timemodified'] = 'The time that this version of the iomadpolicy was updated.';
$string['privacy:metadata:versions:iomadpolicyid'] = 'The iomadpolicy that this version is associated with.';
$string['privacy:metadata:versions:revision'] = 'The revision name of this version of the iomadpolicy.';
$string['privacy:metadata:versions:summary'] = 'The summary of this version of the iomadpolicy.';
$string['privacy:metadata:versions:summaryformat'] = 'The format of the summary field.';
$string['privacy:metadata:versions:content'] = 'The content of this version of the iomadpolicy.';
$string['privacy:metadata:versions:contentformat'] = 'The format of the content field.';
$string['privacysettings'] = 'Privacy settings';
$string['readiomadpolicy'] = 'Please read our {$a}';
$string['refertofulliomadpolicytext'] = 'Please refer to the full {$a} if you would like to review the text.';
$string['response'] = 'Response';
$string['responseby'] = 'Respondent';
$string['responseon'] = 'Date';
$string['revokeacknowledgement'] = 'I acknowledge that I have received a request to withdraw consent on behalf of the above user(s).';
$string['save'] = 'Save';
$string['saveasdraft'] = 'Save as draft';
$string['selectuser'] = 'Select user {$a}';
$string['selectusersforconsent'] = 'Select users to give consent on behalf of.';
$string['settodraft'] = 'Create a new draft';
$string['status'] = 'Policy status';
$string['statusformtitleaccept'] = 'Accepting iomadpolicy';
$string['statusformtitledecline'] = 'Declining iomadpolicy';
$string['statusformtitlerevoke'] = 'Withdrawing iomadpolicy';
$string['statusinfo'] = 'A iomadpolicy with \'Active\' status requires users to give their consent, either when they first log in, or in the case of existing users when they next log in.';
$string['status0'] = 'Draft';
$string['status1'] = 'Active';
$string['status2'] = 'Inactive';
$string['useracceptanceactionaccept'] = 'Accept';
$string['useracceptanceactionacceptone'] = 'Accept {$a}';
$string['useracceptanceactionacceptpending'] = 'Accept pending policies';
$string['useracceptanceactiondecline'] = 'Decline';
$string['useracceptanceactiondeclineone'] = 'Decline {$a}';
$string['useracceptanceactiondeclinepending'] = 'Decline pending policies';
$string['useracceptanceactiondetails'] = 'Details';
$string['useracceptanceactionrevoke'] = 'Withdraw';
$string['useracceptanceactionrevokeall'] = 'Withdraw accepted policies';
$string['useracceptanceactionrevokeone'] = 'Withdraw acceptance of {$a}';
$string['useracceptancecount'] = '{$a->agreedcount} of {$a->userscount} ({$a->percent}%)';
$string['useracceptancecountna'] = 'N/A';
$string['useracceptances'] = 'User agreements';
$string['useriomadpolicysettings'] = 'Policies';
$string['usersaccepted'] = 'Agreements';
$string['viewarchived'] = 'View previous versions';
$string['viewconsentpageforuser'] = 'Viewing this page on behalf of {$a}';
