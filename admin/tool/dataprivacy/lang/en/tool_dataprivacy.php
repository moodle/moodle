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
 * Strings for component 'tool_dataprivacy'
 *
 * @package    tool_dataprivacy
 * @copyright  2018 onwards Jun Pataleta
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Data privacy';
$string['pluginname_help'] = 'Data privacy plugin';
$string['activitiesandresources'] = 'Activities and resources';
$string['addcategory'] = 'Add category';
$string['addpurpose'] = 'Add purpose';
$string['approve'] = 'Approve';
$string['approverequest'] = 'Approve request';
$string['cachedef_purpose'] = 'Data purposes';
$string['cachedef_contextlevel'] = 'Context levels purpose and category';
$string['cancelrequest'] = 'Cancel request';
$string['cancelrequestconfirmation'] = 'Do you really want cancel this data request?';
$string['categories'] = 'Categories';
$string['category'] = 'Category';
$string['categorycreated'] = 'Category created';
$string['categorieslist'] = 'List of data categories';
$string['categoryupdated'] = 'Category updated';
$string['close'] = 'Close';
$string['compliant'] = 'Compliant';
$string['confirmapproval'] = 'Do you really want to approve this data request?';
$string['confirmcontextdeletion'] = 'Do you really want to confirm the deletion of the selected contexts? This will also delete all of the user data for their respective sub-contexts.';
$string['confirmdenial'] = 'Do you really want deny this data request?';
$string['contactdataprotectionofficer'] = 'Contact Data Protection Officer';
$string['contactdataprotectionofficer_desc'] = 'Enabling this feature will provide a link for users to contact the site\'s Data Protection Officer through this site. This link will be shown on their profile page, and on the site\'s privacy policy page, as well. The link leads to a form in which the user can make a data request to the Data Protection Officer.';
$string['contextlevelname10'] = 'Site';
$string['contextlevelname30'] = 'Users';
$string['contextlevelname40'] = 'Course categories';
$string['contextlevelname50'] = 'Courses';
$string['contextlevelname70'] = 'Activity modules';
$string['contextlevelname80'] = 'Blocks';
$string['contextpurposecategorysaved'] = 'Purpose and category saved.';
$string['contactdpoviaprivacypolicy'] = 'Please contact the site\'s Data Protection Officer as described in the Privacy Policy';
$string['createcategory'] = 'Create data category';
$string['createpurpose'] = 'Create data purpose';
$string['datadeletion'] = 'Data deletion';
$string['datadeletionpagehelp'] = 'This page lists the contexts that are already past their retention period and need to be confirmed for user data deletion. Once the selected contexts have been confirmed for deletion, the user data related to these contexts will be deleted on the next execution of the "Delete expired contexts" scheduled task.';
$string['dataprivacy:makedatarequestsforchildren'] = 'Make data requests for children';
$string['dataprivacy:managedatarequests'] = 'Manage data requests';
$string['dataprivacy:managedataregistry'] = 'Manage data registry';
$string['dataregistry'] = 'Data registry';
$string['datarequestemailsubject'] = 'Data request: {$a}';
$string['datarequests'] = 'Data requests';
$string['daterequested'] = 'Date requested';
$string['daterequesteddetail'] = 'Date requested:';
$string['defaultsinfo'] = 'Default categories and purposes are applied to all newly created instances.';
$string['deletecategory'] = 'Delete "{$a}" category';
$string['deletecategorytext'] = 'Are you sure you want to delete "{$a}" category?';
$string['deleteexpiredcontextstask'] = 'Delete expired contexts';
$string['deletepurpose'] = 'Delete "{$a}" purpose';
$string['deletepurposetext'] = 'Are you sure you want to delete "{$a}" purpose?';
$string['defaultssaved'] = 'Defaults saved';
$string['deny'] = 'Deny';
$string['denyrequest'] = 'Deny request';
$string['download'] = 'Download';
$string['dporolemapping'] = 'Data Protection Officer role mapping';
$string['dporolemapping_desc'] = 'Select one or more roles that map to the Data Protection Officer role. Users with these roles will be able to manage data requests. This requires the selected role(s) to have the capability \'tool/dataprivacy:managedatarequests\'';
$string['editcategories'] = 'Edit categories';
$string['editcategory'] = 'Edit category';
$string['editcategories'] = 'Edit categories';
$string['editpurpose'] = 'Edit purpose';
$string['editpurposes'] = 'Edit purposes';
$string['effectiveretentionperiodcourse'] = '{$a} (after the course end date)';
$string['effectiveretentionperioduser'] = '{$a} (since the last time the user accessed the site)';
$string['emailsalutation'] = 'Dear {$a},';
$string['errorinvalidrequeststatus'] = 'Invalid request status!';
$string['errorinvalidrequesttype'] = 'Invalid request type!';
$string['errornoexpiredcontexts'] = 'There are no expired contexts to process';
$string['errorcontexthasunexpiredchildren'] = 'The context "{$a}" still has sub-contexts that have not yet expired. No contexts have been flagged for deletion.';
$string['errorrequestalreadyexists'] = 'You already have an ongoing request.';
$string['errorrequestnotfound'] = 'Request not found';
$string['errorrequestnotwaitingforapproval'] = 'The request is not awaiting approval. Either it is not yet ready or it has already been processed.';
$string['errorsendingmessagetodpo'] = 'An error was encountered while trying to send a message to {$a}.';
$string['expiredretentionperiodtask'] = 'Expired retention period';
$string['expiry'] = 'Expiry';
$string['frontpagecourse'] = 'Front page course';
$string['expandplugin'] = 'Expand and collapse plugin.';
$string['expandplugintype'] = 'Expand and collapse plugin type.';
$string['explanationtitle'] = 'Icons used on this page and what they mean.';
$string['external'] = 'External';
$string['externalexplanation'] = 'An additional plugin installed on this site.';
$string['hide'] = 'Collapse all';
$string['inherit'] = 'Inherit';
$string['messageprovider:contactdataprotectionofficer'] = 'Data requests';
$string['messageprovider:datarequestprocessingresults'] = 'Data request processing results';
$string['message'] = 'Message';
$string['messagelabel'] = 'Message:';
$string['moduleinstancename'] = '{$a->instancename} ({$a->modulename})';
$string['mypersonaldatarequests'] = 'My personal data requests';
$string['nameandparent'] = '{$a->parent} / {$a->name}';
$string['nameemail'] = '{$a->name} ({$a->email})';
$string['nchildren'] = '{$a} children';
$string['newrequest'] = 'New request';
$string['nodatarequests'] = 'There are no data requests';
$string['noactivitiestoload'] = 'No activities';
$string['noassignedroles'] = 'No assigned roles in this context';
$string['noblockstoload'] = 'No blocks';
$string['nocategories'] = 'There are no categories yet';
$string['nocoursestoload'] = 'No activities';
$string['noexpiredcontexts'] = 'There are no expired contexts in this level that need to be confirmed for deletion.';
$string['nopersonaldatarequests'] = 'You don\'t have any personal data requests';
$string['nopurposes'] = 'There are no purposes yet';
$string['nosubjectaccessrequests'] = 'There are no data requests that you need to act on';
$string['nosystemdefaults'] = 'Site purpose and category have not yet been defined.';
$string['notset'] = 'Not set (use the default value)';
$string['pluginregistry'] = 'Plugin privacy registry';
$string['pluginregistrytitle'] = 'Plugin privacy compliance registry';
$string['privacy'] = 'Privacy';
$string['privacy:metadata:request'] = 'Information from personal data requests (subject access and deletion requests) made for this site.';
$string['privacy:metadata:request:comments'] = 'Any user comments accompanying the request.';
$string['privacy:metadata:request:userid'] = 'The ID of the user to whom the request belongs';
$string['privacy:metadata:request:requestedby'] = 'The ID of the user making the request, if made on behalf of another user.';
$string['privacy:metadata:request:dpocomment'] = 'Any comments made by the site\'s respective privacy officer regarding the request.';
$string['privacy:metadata:request:timecreated'] = 'The timestamp indicating when the request was made by the user.';
$string['protected'] = 'Protected';
$string['protectedlabel'] = 'The retention of this data has a higher legal precedent over a user\'s request to be forgotten. This data will only be deleted after the retention period has expired.';
$string['purpose'] = 'Purpose';
$string['purposecreated'] = 'Purpose created';
$string['purposes'] = 'Purposes';
$string['purposeslist'] = 'List of data purposes';
$string['purposeupdated'] = 'Purpose updated';
$string['replyto'] = 'Reply to';
$string['requestactions'] = 'Actions';
$string['requestby'] = 'Requested by';
$string['requestcomments'] = 'Comments';
$string['requestcomments_help'] = 'Please feel free to provide more details about your request';
$string['requestemailintro'] = 'You have received a data request:';
$string['requestfor'] = 'Requesting for';
$string['requeststatus'] = 'Status';
$string['requestsubmitted'] = 'Your request has been submitted to the site\'s Data Protection Officer';
$string['requesttype'] = 'Type';
$string['requesttype_help'] = 'Select the reason why you would like to contact the site\'s Data Protection Officer';
$string['requesttypedelete'] = 'Delete all of my personal data';
$string['requesttypedeleteshort'] = 'Delete';
$string['requesttypeexport'] = 'Export all of my personal data';
$string['requesttypeexportshort'] = 'Export';
$string['requesttypeothers'] = 'General inquiry';
$string['requesttypeothersshort'] = 'Others';
$string['requiresattention'] = 'Requires attention.';
$string['requiresattentionexplanation'] = 'This plugin does not implement the Moodle privacy API. If this plugin stores any personal data it will not be able to be exported or deleted through Moodle\'s privacy system.';
$string['resultdeleted'] = 'You recently requested to have your account and personal data in {$a} to be deleted. This process has been completed and you will no longer be able to log in.';
$string['resultdownloadready'] = 'Your copy of your personal data in {$a} that you recently requested is now available for download. Please click on the link below to go to the download page.';
$string['reviewdata'] = 'Review data';
$string['retentionperiod'] = 'Retention period';
$string['retentionperiodnotdefined'] = 'No retention period was defined';
$string['retentionperiodzero'] = 'No retention period';
$string['send'] = 'Send';
$string['setdefaults'] = 'Set defaults';
$string['statusapproved'] = 'Approved';
$string['statusawaitingapproval'] = 'Awaiting approval';
$string['statuscancelled'] = 'Cancelled';
$string['statuscomplete'] = 'Complete';
$string['statusdetail'] = 'Status:';
$string['statuspreprocessing'] = 'Pre-processing';
$string['statusprocessing'] = 'Processing';
$string['statuspending'] = 'Pending';
$string['statusrejected'] = 'Rejected';
$string['subjectscope'] = 'Subject scope';
$string['user'] = 'User';
$string['viewrequest'] = 'View the request';
$string['visible'] = 'Expand all';
