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
 * @package     tool_policy
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
$string['activating'] = 'Activating a policy';
$string['activateconfirm'] = '<p>You are about to activate policy <em>\'{$a->name}\'</em> and make the version <em>\'{$a->revision}\'</em> the current one.</p><p>All users will be required to agree to this new policy version to be able to use the site.</p>';
$string['activateconfirmyes'] = 'Activate';
$string['agreepolicies'] = 'Please agree to the following policies';
$string['backtoprevious'] = 'Go back to previous page';
$string['backtotop'] = 'Back to top';
$string['cachedef_policy_optional'] = 'Cache of the optional/compulsory flag for policy versions';
$string['consentbulk'] = 'Consent';
$string['consentpagetitle'] = 'Consent';
$string['contactdpo'] = 'For any questions about the policies please contact the privacy officer.';
$string['dataproc'] = 'Personal data processing';
$string['declineacknowledgement'] = 'I acknowledge that I have received a request to decline consent on behalf of the above user(s).';
$string['declinethepolicy'] = 'Decline user consent';
$string['deleting'] = 'Deleting a version';
$string['deleteconfirm'] = '<p>Are you sure you want to delete policy <em>\'{$a->name}\'</em>?</p><p>This operation can not be undone.</p>';
$string['editingpolicydocument'] = 'Editing policy';
$string['errorpolicyversioncompulsory'] = 'Compulsory policies cannot be declined!';
$string['errorpolicyversionnotfound'] = 'There isn\'t any policy version with this identifier.';
$string['errorsaveasdraft'] = 'Minor change can not be saved as draft';
$string['errorusercantviewpolicyversion'] = 'The user doesn\'t have access to this policy version.';
$string['event_acceptance_created'] = 'User policy agreement created';
$string['event_acceptance_updated'] = 'User policy agreement updated';
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
$string['filterpolicy'] = 'Policy: {$a}';
$string['guestconsent:continue'] = 'Continue';
$string['guestconsentmessage'] = 'If you continue browsing this website, you agree to our policies:';
$string['iagree'] = 'I agree to the {$a}';
$string['idontagree'] = 'No thanks, I decline {$a}';
$string['iagreetothepolicy'] = 'Give consent';
$string['inactivate'] = 'Set status to "Inactive"';
$string['inactivating'] = 'Inactivating a policy';
$string['inactivatingconfirm'] = '<p>You are about to inactivate policy <em>\'{$a->name}\'</em> version <em>\'{$a->revision}\'</em>.</p>';
$string['inactivatingconfirmyes'] = 'Inactivate';
$string['invalidversionid'] = 'There is no policy with this identifier!';
$string['irevokethepolicy'] = 'Withdraw user consent';
$string['listactivepolicies'] = 'List of active policies';
$string['minorchange'] = 'Minor change';
$string['minorchangeinfo'] = 'A minor change does not alter the meaning of the policy. Users are not required to agree to the policy again if the edit is marked as a minor change.';
$string['managepolicies'] = 'Manage policies';
$string['movedown'] = 'Move down';
$string['moveup'] = 'Move up';
$string['mustagreetocontinue'] = 'Before continuing you need to acknowledge all these policies.';
$string['newpolicy'] = 'New policy';
$string['newversion'] = 'New version';
$string['noactivepolicies'] = 'There are no policies with an active version.';
$string['nofiltersapplied'] = 'No filters applied';
$string['nopermissiontoagreedocs'] = 'No permission to agree to the policies';
$string['nopermissiontoagreedocs_desc'] = 'Sorry, you do not have the required permissions to agree to the policies.<br />You will not be able to use this site until the following policies are agreed:';
$string['nopermissiontoagreedocsbehalf'] = 'No permission to agree to the policies on behalf of this user';
$string['nopermissiontoagreedocsbehalf_desc'] = 'Sorry, you do not have the required permission to agree to the following policies on behalf of {$a}:';
$string['nopermissiontoagreedocscontact'] = 'For more help:';
$string['nopermissiontoviewpolicyversion'] = 'You do not have permissions to view this policy version.';
$string['nopolicies'] = 'There are no policies for registered users with an active version.';
$string['selectpolicyandversion'] = 'Use the filter above to select policy and/or version';
$string['steppolicies'] = 'Policy {$a->numpolicy} out of {$a->totalpolicies}';
$string['pluginname'] = 'Policies';
$string['policiesagreements'] = 'Policies and agreements';
$string['policy:accept'] = 'Agree to the policies';
$string['policy:acceptbehalf'] = 'Give consent for policies on someone else\'s behalf';
$string['policy:managedocs'] = 'Manage policies';
$string['policy:viewacceptances'] = 'View user agreement reports';
$string['policydocaudience'] = 'User consent';
$string['policydocaudience0'] = 'All users';
$string['policydocaudience1'] = 'Authenticated users';
$string['policydocaudience2'] = 'Guests';
$string['policydoccontent'] = 'Full policy';
$string['policydochdrpolicy'] = 'Policy';
$string['policydochdrversion'] = 'Document version';
$string['policydocname'] = 'Name';
$string['policydocoptional'] = 'Agreement optional';
$string['policydocoptionalyes'] = 'Optional';
$string['policydocoptionalno'] = 'Compulsory';
$string['policydocrevision'] = 'Version';
$string['policydocsummary'] = 'Summary';
$string['policydocsummary_help'] = 'This text should provide a summary of the policy, potentially in a simplified and easily accessible form, using clear and plain language.';
$string['policydoctype'] = 'Type';
$string['policydoctype0'] = 'Site policy';
$string['policydoctype1'] = 'Privacy policy';
$string['policydoctype2'] = 'Third parties policy';
$string['policydoctype99'] = 'Other policy';
$string['policydocuments'] = 'Policy documents';
$string['policynamedversion'] = 'Policy {$a->name} (version {$a->revision} - {$a->id})';
$string['policypriorityagreement'] = 'Show policy before showing other policies';
$string['policyversionacceptedinbehalf'] = 'Consent for this policy has been given on your behalf.';
$string['policyversionacceptedinotherlang'] = 'Consent for this policy version has been given in a different language.';
$string['previousversions'] = '{$a} previous versions';
$string['privacy:metadata:acceptances'] = 'Information about policy agreements made by users.';
$string['privacy:metadata:acceptances:policyversionid'] = 'The version of the policy for which consent was given.';
$string['privacy:metadata:acceptances:userid'] = 'The user for whom this policy agreement relates to.';
$string['privacy:metadata:acceptances:status'] = 'The status of the agreement.';
$string['privacy:metadata:acceptances:lang'] = 'The language used to display the policy when consent was given.';
$string['privacy:metadata:acceptances:usermodified'] = 'The user who gave consent for the policy, if made on behalf of another user.';
$string['privacy:metadata:acceptances:timecreated'] = 'The time when the user agreed to the policy.';
$string['privacy:metadata:acceptances:timemodified'] = 'The time when the user updated their agreement.';
$string['privacy:metadata:acceptances:note'] = 'Any comments added by a user when giving consent on behalf of another user.';
$string['privacy:metadata:subsystem:corefiles'] = 'The policy tool stores files included in the summary and full policy.';
$string['privacy:metadata:versions'] = 'Policy version information.';
$string['privacy:metadata:versions:name'] = 'The name of the policy.';
$string['privacy:metadata:versions:type'] = 'Policy type.';
$string['privacy:metadata:versions:audience'] = 'The type of users required to give their consent.';
$string['privacy:metadata:versions:archived'] = 'The policy status (active or inactive).';
$string['privacy:metadata:versions:usermodified'] = 'The user who modified the policy.';
$string['privacy:metadata:versions:timecreated'] = 'The time that this version of the policy was created.';
$string['privacy:metadata:versions:timemodified'] = 'The time that this version of the policy was updated.';
$string['privacy:metadata:versions:policyid'] = 'The policy that this version is associated with.';
$string['privacy:metadata:versions:revision'] = 'The revision name of this version of the policy.';
$string['privacy:metadata:versions:summary'] = 'The summary of this version of the policy.';
$string['privacy:metadata:versions:summaryformat'] = 'The format of the summary field.';
$string['privacy:metadata:versions:content'] = 'The content of this version of the policy.';
$string['privacy:metadata:versions:contentformat'] = 'The format of the content field.';
$string['privacysettings'] = 'Privacy settings';
$string['readpolicy'] = 'Please read our {$a}';
$string['refertofullpolicytext'] = 'Please refer to the full {$a} if you would like to review the text.';
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
$string['statusformtitleaccept'] = 'Accepting policy';
$string['statusformtitledecline'] = 'Declining policy';
$string['statusformtitlerevoke'] = 'Withdrawing policy';
$string['statusinfo'] = 'A policy with \'Active\' status requires users to give their consent, either when they first log in, or in the case of existing users when they next log in.';
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
$string['userpolicysettings'] = 'Policies';
$string['usersaccepted'] = 'Agreements';
$string['viewarchived'] = 'View previous versions';
$string['viewconsentpageforuser'] = 'Viewing this page on behalf of {$a}';
