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
//
/**
 * Strings for component 'tool_mfa', language 'en'.
 *
 * @package     tool_mfa
 * @author      Mikhail Golenkov <golenkovm@gmail.com>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['achievedweight'] = 'Achieved weight';
$string['added'] = 'Added';
$string['alltime'] = 'All time';
$string['areyousure'] = 'Are you sure you want to remove this factor?';
$string['cancellogin'] = 'Cancel login';
$string['combination'] = 'Combination';
$string['confirmationreplace'] = 'You will be immediately required to set up another \'{$a}\'. Please make sure you are ready to complete the setup process.';
$string['confirmationrevoke'] = 'You will no longer be able to use \'{$a}\' to log in to this site.';
$string['connector'] = 'AND';
$string['debugmode:heading'] = 'Debug mode';
$string['devicename'] = 'Device';
$string['editfactor'] = 'Edit settings for the {$a} factor';
$string['entercode'] = 'Enter code';
$string['email:subject'] = 'Unable to log in to {$a}';
$string['enablefactor'] = 'Enable factor';
$string['error:actionnotfound'] = 'Action \'{$a}\' not supported';
$string['error:couldnotreplace'] = 'Could not replace this factor.';
$string['error:directaccess'] = 'This page shouldn\'t be accessed directly';
$string['error:factornotenabled'] = 'Multi-factor authentication factor \'{$a}\' not enabled';
$string['error:factornotfound'] = 'Multi-factor authentication factor \'{$a}\' not found';
$string['error:isguestuser'] = 'Guests are not allowed here.';
$string['error:notenoughfactors'] = 'Unable to authenticate';
$string['error:reauth'] = 'We couldn\'t confirm your identity sufficiently to meet the site authentication security policy.<br>This may be due to: <br> 1) Steps being locked - please wait a few minutes and try again.
     <br> 2) Steps being failed - please double check the details for each step. <br> 3) Steps were skipped - please reload this page or try logging in again.';
$string['error:revoke'] = 'Can\'t remove factor';
$string['error:setupfactor'] = 'Can\'t set up factor';
$string['error:support'] = 'If you are still unable to log in, or believe you are seeing this in error, please email:';
$string['error:wrongfactorid'] = 'Factor ID \'{$a}\' is incorrect';
$string['event:failfactor'] = 'Multi-factor authentication failed due to a failed factor.';
$string['event:faillockout'] = 'Multi-factor authentication failed due to too many attempts.';
$string['event:failnotenoughfactors'] = 'Multi-factor authentication failed due to not enough satisfied factors.';
$string['event:userdeletedfactor'] = 'Factor deleted';
$string['event:userfailedmfa'] = 'User failed multi-factor authentication';
$string['event:userpassedmfa'] = 'Verification passed';
$string['event:userrevokedfactor'] = 'Factor revocation';
$string['event:usersetupfactor'] = 'Factor setup';
$string['factor'] = 'Factor';
$string['factorreplace'] = 'Factor \'{$a}\' successfully replaced.';
$string['factorreport'] = 'All factor report';
$string['factorreset'] = 'Your multi-factor authentication \'{$a->factor}\' has been reset by a site administrator. You may need to set up this factor again. {$a->url}';
$string['factorresetall'] = 'All your multi-factor authentication factors have been reset by a site administrator. You may need to set up these factors again. {$a}';
$string['factorrevoked'] = '\'{$a}\' successfully removed.';
$string['factorsetup'] = '\'{$a}\' successfully set up.';
$string['fallback'] = 'Fallback factor';
$string['fallback_info'] = 'This factor is a fallback if no other factors are configured. This factor will always fail.';
$string['guidance'] = 'Multi-factor authentication user guide';
$string['ipatcreation'] = 'IP address when factor created';
$string['lastused'] = 'Last used';
$string['locked'] = '{$a} (Unavailable)';
$string['lockedusersforallfactors'] = 'Locked users: All factors';
$string['lockedusersforfactor'] = 'Locked users: {$a}';
$string['lockoutnotification'] = 'You have {$a} attempts left.';
$string['managefactor'] = 'Manage factor';
$string['mfa'] = 'Multi-factor authentication';
$string['mfa:intro'] = 'Make your account safer by requiring an additional verification method when you log in.';
$string['mfa:mfaaccess'] = 'Interact with MFA';
$string['mfareports'] = 'MFA reports';
$string['mfasettings'] = 'Manage multi-factor authentication';
$string['na'] = 'n/a';
$string['needhelp'] = 'Need help?';
$string['nologinusers'] = 'Not logged in';
$string['nonauthusers'] = 'Pending MFA';
$string['overall'] = 'Overall';
$string['pending'] = 'Pending';
$string['performbulk'] = 'Bulk action';
$string['pluginname'] = 'Multi-factor authentication';
$string['preferences:activefactors'] = 'Active factors';
$string['preferences:availablefactors'] = 'Available factors';
$string['preferences:header'] = 'Multi-factor authentication preferences';
$string['preferenceslink'] = 'Click here to go to user preferences.';
$string['privacy:metadata:tool_mfa'] = 'Data with configured MFA factors';
$string['privacy:metadata:tool_mfa:createdfromip'] = 'IP that the factor was set up from.';
$string['privacy:metadata:tool_mfa:factor'] = 'Factor type';
$string['privacy:metadata:tool_mfa:id'] = 'Record ID';
$string['privacy:metadata:tool_mfa:label'] = 'Label for factor instance, e.g. device or email.';
$string['privacy:metadata:tool_mfa:lastverified'] = 'Time user was last verified with this factor';
$string['privacy:metadata:tool_mfa:secret'] = 'Any secret data for factor';
$string['privacy:metadata:tool_mfa:timecreated'] = 'Time the factor instance was set up.';
$string['privacy:metadata:tool_mfa:timemodified'] = 'Time factor was last modified';
$string['privacy:metadata:tool_mfa:userid'] = 'The ID of the user that factor belongs to';
$string['privacy:metadata:tool_mfa_auth'] = 'The last time a successful multi-factor authentication was registered for a user ID.';
$string['privacy:metadata:tool_mfa_auth:lastverified'] = 'Time user was last authenticated with';
$string['privacy:metadata:tool_mfa_auth:userid'] = 'The user this timestamp is associated with.';
$string['privacy:metadata:tool_mfa_secrets'] = 'Temporary secrets for user authentication.';
$string['privacy:metadata:tool_mfa_secrets:factor'] = 'The factor this secret is associated with.';
$string['privacy:metadata:tool_mfa_secrets:secret'] = 'The secret security code.';
$string['privacy:metadata:tool_mfa_secrets:sessionid'] = 'The session ID this secret is associated with.';
$string['privacy:metadata:tool_mfa_secrets:userid'] = 'The user this secret is associated with.';
$string['redirecterrordetected'] = 'Unsupported redirect detected, script execution terminated. Redirection error occured between MFA and {$a}.';
$string['remove'] = 'Remove';
$string['replace'] = 'Replace';
$string['replacefactor'] = 'Replace factor';
$string['resetconfirm'] = 'Reset user factor';
$string['resetfactor'] = 'Reset user authentication factors';
$string['resetfactorconfirm'] = 'Are you sure you wish to reset this factor for {$a}?';
$string['resetfactorplaceholder'] = 'Username or email';
$string['resetsuccess'] = 'Factor \'{$a->factor}\' successfully reset for user \'{$a->username}\'.';
$string['resetsuccessbulk'] = 'Factor \'{$a}\' successfully reset for provided users.';
$string['resetuser'] = 'User:';
$string['revokefactor'] = 'Remove factor';
$string['selectfactor'] = 'Select factor to reset:';
$string['selectperiod'] = 'Select a lookback period for the report:';
$string['settings:combinations'] = 'Summary of good conditions for login';
$string['settings:debugmode'] = 'Enable debug mode';
$string['settings:debugmode_help'] = 'Debug mode will display a small notification banner on MFA admin pages, as well as the user preferences page
         with information on the currently enabled factors.';
$string['settings:duration'] = 'Secret validity duration';
$string['settings:duration_help'] = 'The duration that generated secrets are valid.';
$string['settings:enabled'] = 'MFA plugin enabled';
$string['settings:enablefactor'] = 'Enable factor';
$string['settings:enablefactor_help'] = 'Check this control to allow the factor to be used for MFA authentication.';
$string['settings:general'] = 'General MFA settings';
$string['settings:guidancecheck'] = 'Use guidance page';
$string['settings:guidancecheck_help'] = 'Add a link to the guidance page on the MFA authentication pages, and MFA preferences page.';
$string['settings:guidancefiles'] = 'Guidance page files';
$string['settings:guidancefiles_help'] = 'Add any files here to use in the guidance page, and embed them into the page using {{filename}} (resolved path) or {{{filename}}} (html link) in the editor';
$string['settings:guidancepage'] = 'Guidance page content';
$string['settings:guidancepage_help'] = 'HTML here will be displayed on the guidance page. Enter filenames from the filearea to embed the file with the resolved path {{filename}} or as a html link using {{{filename}}}.';
$string['settings:lockout'] = 'Lockout threshold';
$string['settings:lockout_help'] = 'Number of attempts a user can answer input factors before they are prevented from logging in.';
$string['settings:redir_exclusions'] = 'URLS which should not redirect the MFA check.';
$string['settings:redir_exclusions_help'] = 'Each new line is a relative URL from the siteroot for which the MFA check will not redirect from';
$string['settings:weight'] = 'Factor weight';
$string['settings:weight_help'] = 'The weight of this factor if passed. A user needs at least 100 points to log in.';
$string['setupfactor'] = 'Set up factor';
$string['setupfactorbuttonadditional'] = 'Add additional factor';
$string['state:fail'] = 'Fail';
$string['state:locked'] = 'Locked';
$string['state:neutral'] = 'Neutral';
$string['state:pass'] = 'Pass';
$string['state:unknown'] = 'Unknown';
$string['subplugintype_factor'] = 'Factor type';
$string['subplugintype_factor_plural'] = 'Factor types';
$string['totalusers'] = 'Total users';
$string['totalweight'] = 'Total weight';
$string['userempty'] = 'User cannot be empty.';
$string['userlogs'] = 'User logs';
$string['usernotfound'] = 'Unable to locate user.';
$string['usersauthedinperiod'] = 'Logged in';
$string['verification'] = '2-step verification';
$string['verification_desc'] = 'To keep your account safe, we need to check that this is really you.';
$string['verificationcode'] = 'Verification code';
$string['verificationcode_help'] = 'The verification code provided by the current authentication factor.';
$string['verifyalt'] = 'Try another way to verify:';
$string['weight'] = 'Weight';
$string['yesremove'] = 'Yes, remove';
$string['yesreplace'] = 'Yes, replace';

// Deprecated since Moodle 5.0.
$string['inputrequired'] = 'User input';
$string['setuprequired'] = 'User setup';
