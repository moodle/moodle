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

$string['acceptanceacknowledgement'] = 'I acknowledge that consents to these policies have been acquired';
$string['acceptancecount'] = '{$a->agreedcount} of {$a->policiescount}';
$string['acceptancenote'] = 'Remarks';
$string['acceptancepolicies'] = 'Policies';
$string['acceptancessavedsucessfully'] = 'The agreements has been saved successfully.';
$string['acceptancestatusoverall'] = 'Overall';
$string['acceptanceusers'] = 'Users';
$string['actions'] = 'Actions';
$string['activate'] = 'Set status to "Active"';
$string['activating'] = 'Activating a policy';
$string['activateconfirm'] = '<p>You are about to activate policy <em>\'{$a->name}\'</em> and make the version <em>\'{$a->revision}\'</em> the current one.</p><p>All users will be required to accept this new policy version to be able to use the site.</p>';
$string['activateconfirmyes'] = 'Activate';
$string['agreed'] = 'Agreed';
$string['agreedby'] = 'Agreed by';
$string['agreedon'] = 'Agreed on';
$string['agreepolicies'] = 'Please agree to the following policies';
$string['backtotop'] = 'Back to top';
$string['consentbulk'] = 'Consent';
$string['consentdetails'] = 'Consent details';
$string['consentpagetitle'] = 'Consent';
$string['dataproc'] = 'Personal data processing';
$string['deleting'] = 'Deleting a version';
$string['deleteconfirm'] = '<p>Are you sure you want to delete policy <em>\'{$a->name}\'</em>?</p><p>This operation can not be undone.</p>';
$string['editingpolicydocument'] = 'Editing policy';
$string['errorpolicyversionnotfound'] = 'There isn\'t any policy version with this identifier.';
$string['errorsaveasdraft'] = 'Minor change can not be saved as draft';
$string['errorusercantviewpolicyversion'] = 'The user hasn\'t access to this policy version.';
$string['event_acceptance_created'] = 'User policy agreement created';
$string['event_acceptance_updated'] = 'User policy agreement updated';
$string['filtercapabilityno'] = 'Permission: Can not agree';
$string['filtercapabilityyes'] = 'Permission: Can agree';
$string['filterrevision'] = 'Version: {$a}';
$string['filterrevisionstatus'] = 'Version: {$a->name} ({$a->status})';
$string['filterrole'] = 'Role: {$a}';
$string['filters'] = 'Filters';
$string['filterstatusno'] = 'Status: Not agreed';
$string['filterstatusyes'] = 'Status: Agreed';
$string['filterplaceholder'] = 'Search keyword or select filter';
$string['filterpolicy'] = 'Policy: {$a}';
$string['guestconsent:continue'] = 'Continue';
$string['guestconsentmessage'] = 'If you continue browsing this website, you agree to our policies:';
$string['iagree'] = 'I agree to the {$a}';
$string['iagreetothepolicy'] = 'I agree to the policy';
$string['inactivate'] = 'Set status to "Inactive"';
$string['inactivating'] = 'Inactivating a policy';
$string['inactivatingconfirm'] = '<p>You are about to inactivate policy <em>\'{$a->name}\'</em> version <em>\'{$a->revision}\'</em>.</p><p>The policy will not apply until some version is made the current one.</p>';
$string['inactivatingconfirmyes'] = 'Inactivate';
$string['invalidversionid'] = 'There is no policy with this identifier!';
$string['minorchange'] = 'Minor change';
$string['minorchangeinfo'] = 'Minor changes do not amend the meaning of the policy text, terms or conditions. Users do not need to reconfirm their consent.';
$string['managepolicies'] = 'Manage policies';
$string['movedown'] = 'Move down';
$string['moveup'] = 'Move up';
$string['mustagreetocontinue'] = 'Before continuing you must agree to all these policies.';
$string['newpolicy'] = 'New policy';
$string['newversion'] = 'New version';
$string['nofiltersapplied'] = 'No filters applied';
$string['nopermissiontoagreedocs'] = 'No permission to agree to the policies';
$string['nopermissiontoagreedocs_desc'] = 'Sorry, you do not have the required permissions to agree to the policies.<br />You will not be able to use this site until the following policies are agreed:';
$string['nopermissiontoagreedocsbehalf'] = 'No permission to agree to the policies on behalf of this user';
$string['nopermissiontoagreedocsbehalf_desc'] = 'Sorry, you do not have the required permission to agree to the following policies on behalf of {$a}:';
$string['nopermissiontoagreedocscontact'] = 'For further assistance, please contact the following person:';
$string['nopermissiontoviewpolicyversion'] = 'You do not have permissions to view this policy version.';
$string['nopolicies'] = 'There are no policies for registered users with an active version.';
$string['selectpolicyandversion'] = 'Use the filter above to select policy and/or version';
$string['steppolicies'] = 'Policy {$a->numpolicy} out of {$a->totalpolicies}';
$string['pluginname'] = 'Policies';
$string['policiesagreements'] = 'Policies and agreements';
$string['policy:accept'] = 'Agree to the policies';
$string['policy:acceptbehalf'] = 'Agree to the policies on someone else\'s behalf';
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
$string['policydocrevision'] = 'Version';
$string['policydocsummary'] = 'Summary';
$string['policydocsummary_help'] = 'This text should provide a summary of the policy, potentially in a simplified and easily accessible form, using clear and plain language.';
$string['policydoctype'] = 'Type';
$string['policydoctype0'] = 'Site policy';
$string['policydoctype1'] = 'Privacy policy';
$string['policydoctype2'] = 'Third parties policy';
$string['policydoctype99'] = 'Other policy';
$string['policyversionacceptedinbehalf'] = 'This policy version has been agreed to by another user on behalf of you.';
$string['policyversionacceptedinotherlang'] = 'This policy version has been agreed to in a different language.';
$string['previousversions'] = '{$a} previous versions';
$string['privacy:metadata:acceptances'] = 'Information from policies agreements made by the users of this site.';
$string['privacy:metadata:acceptances:policyversionid'] = 'The ID of the accepted version policy.';
$string['privacy:metadata:acceptances:userid'] = 'The ID of the user who has agreed to the policy.';
$string['privacy:metadata:acceptances:status'] = 'The status of the agreement: 0 if not accepted; 1 otherwise.';
$string['privacy:metadata:acceptances:lang'] = 'The current language displayed when the policy is accepted.';
$string['privacy:metadata:acceptances:usermodified'] = 'The ID of the user accepting the policy, if made on behalf of another user.';
$string['privacy:metadata:acceptances:timecreated'] = 'The timestamp indicating when the agreement was made by the user.';
$string['privacy:metadata:acceptances:timemodified'] = 'The timestamp indicating when the agreement was modified by the user.';
$string['privacy:metadata:acceptances:note'] = 'Any comments added by the user who agree to policies on behalf of another.';
$string['privacysettings'] = 'Privacy settings';
$string['readpolicy'] = 'Please read our {$a}';
$string['refertofullpolicytext'] = 'Please refer to the full {$a} text if you would like to review.';
$string['save'] = 'Save';
$string['saveasdraft'] = 'Save as draft';
$string['selectuser'] = 'Select user {$a}';
$string['selectusersforconsent'] = 'Select users to give consent for';
$string['settodraft'] = 'Create a new "Draft"';
$string['status'] = 'Policy status';
$string['statusinfo'] = 'An active policy will require consent from new users. Existing users will need to consent to this policy on their next logged in session.';
$string['status0'] = 'Draft';
$string['status1'] = 'Active';
$string['status2'] = 'Inactive';
$string['useracceptancecount'] = '{$a->agreedcount} of {$a->userscount} ({$a->percent}%)';
$string['useracceptancecountna'] = 'N/A';
$string['useracceptances'] = 'User agreements';
$string['userpoliciesagreements'] = 'User agreements to policies';
$string['userpolicysettings'] = 'Policies';
$string['usersaccepted'] = 'Agreements';
$string['viewarchived'] = 'View previous versions';
$string['viewconsentpageforuser'] = 'Viewing this page on behalf of {$a}';
$string['agreedno'] = 'Not agreed';
$string['agreednowithlink'] = 'Not agreed, click to agree to "{$a}"';
$string['agreednowithlinkall'] = 'Not agreed, click to agree to all policies';
$string['agreedyes'] = 'Agreed';
$string['agreedyesonbehalf'] = 'Agreed on behalf of';
