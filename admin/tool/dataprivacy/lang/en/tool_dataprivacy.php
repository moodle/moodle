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
$string['addnewdefaults'] = 'Add a new module default';
$string['addpurpose'] = 'Add purpose';
$string['approve'] = 'Approve';
$string['approverequest'] = 'Approve request';
$string['bulkapproverequests'] = 'Approve requests';
$string['bulkdenyrequests'] = 'Deny requests';
$string['cachedef_purpose'] = 'Data purposes';
$string['cachedef_contextlevel'] = 'Context levels purpose and category';
$string['cancelrequest'] = 'Cancel request';
$string['cancelrequestconfirmation'] = 'Do you really want cancel this data request?';
$string['categories'] = 'Categories';
$string['category'] = 'Category';
$string['category_help'] = 'A category in the data registry describes a type of data. A new category may be added, or if Inherit is selected, the data category from a higher context is applied. Contexts are (from low to high): Blocks > Activity modules > Courses > Course categories > Site.';
$string['categorycreated'] = 'Category created';
$string['categorydefault'] = 'Default category';
$string['categorydefault_help'] = 'The default category is the data category applied to any new instances. If Inherit is selected, the data category from a higher context is applied. Contexts are (from low to high): Blocks > Activity modules > Courses > Course categories > User > Site.';
$string['categorieslist'] = 'List of data categories';
$string['categoryupdated'] = 'Category updated';
$string['close'] = 'Close';
$string['compliant'] = 'Compliant';
$string['confirmapproval'] = 'Do you really want to approve this data request?';
$string['confirmbulkapproval'] = 'Do you really want to bulk approve the selected data requests?';
$string['confirmcompletion'] = 'Do you really want to mark this user enquiry as complete?';
$string['confirmcontextdeletion'] = 'Do you really want to confirm the deletion of the selected contexts? This will also delete all of the user data for their respective sub-contexts.';
$string['confirmdenial'] = 'Do you really want deny this data request?';
$string['confirmbulkdenial'] = 'Do you really want to bulk deny the selected data requests?';
$string['contactdataprotectionofficer'] = 'Contact the privacy officer';
$string['contactdataprotectionofficer_desc'] = 'If enabled, users will be able to contact the privacy officer and make a data request via a link on their profile page.';
$string['contextlevelname10'] = 'Site';
$string['contextlevelname30'] = 'Users';
$string['contextlevelname40'] = 'Course categories';
$string['contextlevelname50'] = 'Courses';
$string['contextlevelname70'] = 'Activity modules';
$string['contextlevelname80'] = 'Blocks';
$string['contextpurposecategorysaved'] = 'Purpose and category saved.';
$string['contactdpoviaprivacypolicy'] = 'Please contact the privacy officer as described in the privacy policy.';
$string['createcategory'] = 'Create data category';
$string['createnewdatarequest'] = 'Create a new data request';
$string['createpurpose'] = 'Create data purpose';
$string['datadeletion'] = 'Data deletion';
$string['datadeletionpagehelp'] = 'Data for which the retention period has expired are listed here. Please review and confirm data deletion, which will then be executed by the "Delete expired contexts" scheduled task.';
$string['dataprivacy:makedatarequestsforchildren'] = 'Make data requests for minors';
$string['dataprivacy:managedatarequests'] = 'Manage data requests';
$string['dataprivacy:managedataregistry'] = 'Manage data registry';
$string['dataprivacy:downloadownrequest'] = 'Download your own exported data';
$string['dataprivacy:downloadallrequests'] = 'Download exported data for everyone';
$string['dataregistry'] = 'Data registry';
$string['dataregistryinfo'] = 'The data registry enables categories (types of data) and purposes (the reasons for processing data) to be set for all content on the site - from users and courses down to activities and blocks. For each purpose, a retention period may be set. When a retention period has expired, the data is flagged and listed for deletion, awaiting admin confirmation.';
$string['datarequestcreatedforuser'] = 'Data request created for {$a}';
$string['datarequestemailsubject'] = 'Data request: {$a}';
$string['datarequests'] = 'Data requests';
$string['datecomment'] = '[{$a->date}]: ' . PHP_EOL . ' {$a->comment}';
$string['daterequested'] = 'Date requested';
$string['daterequesteddetail'] = 'Date requested:';
$string['defaultsinfo'] = 'Default categories and purposes are applied to all new and existing instances where a value is not set.';
$string['defaultswarninginfo'] = 'Warning: Changing these defaults may affect the retention period of existing instances.';
$string['deletecategory'] = 'Delete category';
$string['deletecategorytext'] = 'Are you sure you want to delete the category \'{$a}\'?';
$string['deletedefaults'] = 'Delete defaults: {$a}';
$string['deletedefaultsconfirmation'] = 'Are you sure you want to delete the default category and purpose for {$a} modules?';
$string['deleteexpiredcontextstask'] = 'Delete expired contexts';
$string['deleteexpireddatarequeststask'] = 'Delete files from completed data requests that have expired';
$string['deletepurpose'] = 'Delete purpose';
$string['deletepurposetext'] = 'Are you sure you want to delete the purpose \'{$a}\'?';
$string['defaultssaved'] = 'Defaults saved';
$string['deny'] = 'Deny';
$string['denyrequest'] = 'Deny request';
$string['download'] = 'Download';
$string['downloadexpireduser'] = 'Download has expired. Submit a new request if you wish to export your personal data.';
$string['dporolemapping'] = 'Privacy officer role mapping';
$string['dporolemapping_desc'] = 'The privacy officer can manage data requests. The capability tool/dataprivacy:managedatarequests must be allowed for a role to be listed as a privacy officer role mapping option.';
$string['editcategories'] = 'Edit categories';
$string['editcategory'] = 'Edit category';
$string['editcategories'] = 'Edit categories';
$string['editdefaults'] = 'Edit defaults: {$a}';
$string['editmoduledefaults'] = 'Edit module defaults';
$string['editpurpose'] = 'Edit purpose';
$string['editpurposes'] = 'Edit purposes';
$string['effectiveretentionperiodcourse'] = '{$a} (after the course end date)';
$string['effectiveretentionperioduser'] = '{$a} (since the last time the user accessed the site)';
$string['emailsalutation'] = 'Dear {$a},';
$string['errorinvalidrequeststatus'] = 'Invalid request status!';
$string['errorinvalidrequesttype'] = 'Invalid request type!';
$string['errornocapabilitytorequestforothers'] = 'User {$a->requestedby} doesn\'t have the capability to make a data request on behalf of user {$a->userid}';
$string['errornoexpiredcontexts'] = 'There are no expired contexts to process';
$string['errorcontexthasunexpiredchildren'] = 'The context "{$a}" still has sub-contexts that have not yet expired. No contexts have been flagged for deletion.';
$string['errorrequestalreadyexists'] = 'You already have an ongoing request.';
$string['errorrequestnotfound'] = 'Request not found';
$string['errorrequestnotwaitingforapproval'] = 'The request is not awaiting approval. Either it is not yet ready or it has already been processed.';
$string['errorsendingmessagetodpo'] = 'An error was encountered while trying to send a message to {$a}.';
$string['exceptionnotificationsubject'] = 'Exception occurred while processing privacy data';
$string['exceptionnotificationbody'] = '<p>Exception occurred while calling <b>{$a->fullmethodname}</b>.<br>This means that plugin <b>{$a->component}</b> did not complete the processing of data. The following exception information may be passed on to the plugin developer:</p><pre>{$a->message}<br>

{$a->backtrace}</pre>';
$string['expiredretentionperiodtask'] = 'Expired retention period';
$string['expiry'] = 'Expiry';
$string['expandplugin'] = 'Expand and collapse plugin.';
$string['expandplugintype'] = 'Expand and collapse plugin type.';
$string['explanationtitle'] = 'Icons used on this page and what they mean.';
$string['external'] = 'Additional';
$string['externalexplanation'] = 'An additional plugin installed on this site.';
$string['filteroption'] = '{$a->category}: {$a->name}';
$string['frontpagecourse'] = 'Front page course';
$string['gdpr_art_6_1_a_description'] = 'The data subject has given consent to the processing of his or her personal data for one or more specific purposes';
$string['gdpr_art_6_1_a_name'] = 'Consent (GDPR Art. 6.1(a))';
$string['gdpr_art_6_1_b_description'] = 'Processing is necessary for the performance of a contract to which the data subject is party or in order to take steps at the request of the data subject prior to entering into a contract';
$string['gdpr_art_6_1_b_name'] = 'Contract (GDPR Art. 6.1(b))';
$string['gdpr_art_6_1_c_description'] = 'Processing is necessary for compliance with a legal obligation to which the controller is subject';
$string['gdpr_art_6_1_c_name'] = 'Legal obligation (GDPR Art 6.1(c))';
$string['gdpr_art_6_1_d_description'] = 'Processing is necessary in order to protect the vital interests of the data subject or of another natural person';
$string['gdpr_art_6_1_d_name'] = 'Vital interests (GDPR Art. 6.1(d))';
$string['gdpr_art_6_1_e_description'] = 'Processing is necessary for the performance of a task carried out in the public interest or in the exercise of official authority vested in the controller';
$string['gdpr_art_6_1_e_name'] = 'Public task (GDPR Art. 6.1(e))';
$string['gdpr_art_6_1_f_description'] = 'Processing is necessary for the purposes of the legitimate interests pursued by the controller or  by a third party, except where such interests are overridden by the interests or fundamental rights and freedoms of the data subject which require protection of personal data, in particular where the data subject is a child';
$string['gdpr_art_6_1_f_name'] = 'Legitimate interests (GDPR Art. 6.1(f))';
$string['gdpr_art_9_2_a_description'] = 'The data subject has given explicit consent to the processing of those personal data for one or more specified purposes, except where Union or Member State law provide that the prohibition referred to in paragraph 1 of GDPR Article 9 may not be lifted by the data subject';
$string['gdpr_art_9_2_a_name'] = 'Explicit consent (GDPR Art. 9.2(a))';
$string['gdpr_art_9_2_b_description'] = 'Processing is necessary for the purposes of carrying out the obligations and exercising specific rights of the controller or of the data subject in the field of employment and social security and social protection law in so far as it is authorised by Union or Member State law or a collective agreement pursuant to Member State law providing for appropriate safeguards for the fundamental rights and the interests of the data subject';
$string['gdpr_art_9_2_b_name'] = 'Employment and social security/protection law (GDPR Art. 9.2(b))';
$string['gdpr_art_9_2_c_description'] = 'Processing is necessary to protect the vital interests of the data subject or of another natural person where the data subject is physically or legally incapable of giving consent';
$string['gdpr_art_9_2_c_name'] = 'Protection of vital interests (GDPR Art. 9.2(c))';
$string['gdpr_art_9_2_d_description'] = 'Processing is carried out in the course of its legitimate activities with appropriate safeguards by a foundation, association or any other not-for-profit body with a political, philosophical, religious or trade-union aim and on condition that the processing relates solely to the members or to former members of the body or to persons who have regular contact with it in connection with its purposes and that the personal data are not disclosed outside that body without the consent of the data subjects';
$string['gdpr_art_9_2_d_name'] = 'Legitimate activities regarding the members/close contacts  of a foundation, association or other not-for-profit body (GDPR Art. 9.2(d))';
$string['gdpr_art_9_2_e_description'] = 'Processing relates to personal data which are manifestly made public by the data subject';
$string['gdpr_art_9_2_e_name'] = 'Data made public by the data subject (GDPR Art. 9.2(e))';
$string['gdpr_art_9_2_f_description'] = 'Processing is necessary for the establishment, exercise or defence of legal claims or whenever courts are acting in their judicial capacity';
$string['gdpr_art_9_2_f_name'] = 'Legal claims and court actions (GDPR Art. 9.2(f))';
$string['gdpr_art_9_2_g_description'] = 'Processing is necessary for reasons of substantial public interest, on the basis of Union or Member State law which shall be proportionate to the aim pursued, respect the essence of the right to data protection and provide for suitable and specific measures to safeguard the fundamental rights and the interests of the data subject';
$string['gdpr_art_9_2_g_name'] = 'Substantial public interest (GDPR Art. 9.2(g))';
$string['gdpr_art_9_2_h_description'] = 'Processing is necessary for the purposes of preventive or occupational medicine, for the assessment of the working capacity of the employee, medical diagnosis, the provision of health or social care or treatment or the management of health or social care systems and services on the basis of Union or Member State law or pursuant to contract with a health professional and subject to the conditions and safeguards referred to in paragraph 3 of GDPR Article 9';
$string['gdpr_art_9_2_h_name'] = 'Medical purposes (GDPR Art. 9.2(h))';
$string['gdpr_art_9_2_i_description'] = 'Processing is necessary for reasons of public interest in the area of public health, such as protecting against serious cross-border threats to health or ensuring high standards of quality and safety of health care and of medicinal products or medical devices, on the basis of Union or Member State law which provides for suitable and specific measures to safeguard the rights and freedoms of the data subject, in particular professional secrecy';
$string['gdpr_art_9_2_i_name'] = 'Public health (GDPR Art. 9.2(i))';
$string['gdpr_art_9_2_j_description'] = 'Processing is necessary for archiving purposes in the public interest, scientific or historical research purposes or statistical purposes in accordance with Article 89(1) based on Union or Member State law which shall be proportionate to the aim pursued, respect the essence of the right to data protection and provide for suitable and specific measures to safeguard the fundamental rights and the interests of the data subject';
$string['gdpr_art_9_2_j_name'] = 'Public interest, or scientific/historical/statistical research (GDPR Art. 9.2(j))';
$string['hide'] = 'Collapse all';
$string['httpwarning'] = 'Any data downloaded from this site may not be encrypted. Please contact your system administrator and request that they install SSL on this site.';
$string['inherit'] = 'Inherit';
$string['lawfulbases'] = 'Lawful bases';
$string['lawfulbases_help'] = 'Select at least one option that will serve as the lawful basis for processing personal data. For details on these lawful bases, please see <a href="https://gdpr-info.eu/art-6-gdpr/" target="_blank">GDPR Art. 6.1</a>';
$string['markcomplete'] = 'Mark as complete';
$string['markedcomplete'] = 'Your enquiry has been marked as complete by the privacy officer.';
$string['messageprovider:contactdataprotectionofficer'] = 'Data requests';
$string['messageprovider:datarequestprocessingresults'] = 'Data request processing results';
$string['messageprovider:notifyexceptions'] = 'Data requests exceptions notifications';
$string['message'] = 'Message';
$string['messagelabel'] = 'Message:';
$string['moduleinstancename'] = '{$a->instancename} ({$a->modulename})';
$string['mypersonaldatarequests'] = 'My personal data requests';
$string['nameandparent'] = '{$a->parent} / {$a->name}';
$string['nameemail'] = '{$a->name} ({$a->email})';
$string['nchildren'] = '{$a} children';
$string['newrequest'] = 'New request';
$string['nodatarequests'] = 'There are no data requests';
$string['nodatarequestsmatchingfilter'] = 'There are no data requests matching the given filter';
$string['noactivitiestoload'] = 'No activities';
$string['noassignedroles'] = 'No assigned roles in this context';
$string['noblockstoload'] = 'No blocks';
$string['nocategories'] = 'There are no categories yet';
$string['nocoursestoload'] = 'No activities';
$string['noexpiredcontexts'] = 'This context level has no data for which the retention period has expired.';
$string['nopersonaldatarequests'] = 'You don\'t have any personal data requests';
$string['nopurposes'] = 'There are no purposes yet';
$string['nosubjectaccessrequests'] = 'There are no data requests that you need to act on';
$string['nosystemdefaults'] = 'Site purpose and category have not yet been defined.';
$string['notset'] = 'Not set (use the default value)';
$string['overrideinstances'] = 'Reset instances with custom values';
$string['pluginregistry'] = 'Plugin privacy registry';
$string['pluginregistrytitle'] = 'Plugin privacy compliance registry';
$string['privacy'] = 'Privacy';
$string['privacyofficeronly'] = 'Only users who are assigned a privacy officer role ({$a}) have access to this content';
$string['privacy:metadata:preference:tool_dataprivacy_request-filters'] = 'The filters currently applied to the data requests page.';
$string['privacy:metadata:preference:tool_dataprivacy_request-perpage'] = 'The number of data requests the user prefers to see on one page';
$string['privacy:metadata:request'] = 'Information from personal data requests (subject access and deletion requests) made for this site.';
$string['privacy:metadata:request:comments'] = 'Any user comments accompanying the request.';
$string['privacy:metadata:request:userid'] = 'The ID of the user to whom the request belongs';
$string['privacy:metadata:request:requestedby'] = 'The ID of the user making the request, if made on behalf of another user.';
$string['privacy:metadata:request:dpocomment'] = 'Any comments made by the site\'s privacy officer regarding the request.';
$string['privacy:metadata:request:timecreated'] = 'The timestamp indicating when the request was made by the user.';
$string['privacyrequestexpiry'] = 'Data request expiry';
$string['privacyrequestexpiry_desc'] = 'The time that approved data requests will be available for download before expiring. If set to zero, then there is no time limit.';
$string['protected'] = 'Protected';
$string['protectedlabel'] = 'The retention of this data has a higher legal precedent over a user\'s request to be forgotten. This data will only be deleted after the retention period has expired.';
$string['purpose'] = 'Purpose';
$string['purpose_help'] = 'The purpose describes the reason for processing the data. A new purpose may be added, or if Inherit is selected, the purpose from a higher context is applied. Contexts are (from low to high): Blocks > Activity modules > Courses > Course categories > User > Site.';
$string['purposecreated'] = 'Purpose created';
$string['purposedefault'] = 'Default purpose';
$string['purposedefault_help'] = 'The default purpose is the purpose which is applied to any new instances. If Inherit is selected, the purpose from a higher context is applied. Contexts are (from low to high): Blocks > Activity modules > Courses > Course categories > User > Site.';
$string['purposes'] = 'Purposes';
$string['purposeslist'] = 'List of data purposes';
$string['purposeupdated'] = 'Purpose updated';
$string['replyto'] = 'Reply to';
$string['requestactions'] = 'Actions';
$string['requestapproved'] = 'The request has been approved';
$string['requestby'] = 'Requested by';
$string['requestbydetail'] = 'Requested by:';
$string['requestcomments'] = 'Comments';
$string['requestcomments_help'] = 'This box enables you to enter any further details about your data request.';
$string['requestdenied'] = 'The request has been denied';
$string['requestemailintro'] = 'You have received a data request:';
$string['requestfor'] = 'Requesting for';
$string['requestmarkedcomplete'] = 'The request has been marked as complete';
$string['requestorigin'] = 'Request origin';
$string['requestsapproved'] = 'The requests have been approved';
$string['requestsdenied'] = 'The requests have been denied';
$string['requeststatus'] = 'Status';
$string['requestsubmitted'] = 'Your request has been submitted to the privacy officer';
$string['requesttype'] = 'Type';
$string['requesttypeuser'] = '{$a->typename} ({$a->user})';
$string['requesttype_help'] = 'Select the reason for contacting the privacy officer. Be aware that deletion of all personal  data will result in you no longer being able to log in to the site.';
$string['requesttypedelete'] = 'Delete all of my personal data';
$string['requesttypedeleteshort'] = 'Delete';
$string['requesttypeexport'] = 'Export all of my personal data';
$string['requesttypeexportshort'] = 'Export';
$string['requesttypeothers'] = 'General inquiry';
$string['requesttypeothersshort'] = 'Message';
$string['requiresattention'] = 'Requires attention.';
$string['requiresattentionexplanation'] = 'This plugin does not implement the Moodle privacy API. If this plugin stores any personal data it will not be able to be exported or deleted through Moodle\'s privacy system.';
$string['resultdeleted'] = 'You recently requested to have your account and personal data in {$a} to be deleted. This process has been completed and you will no longer be able to log in.';
$string['resultdownloadready'] = 'Your copy of your personal data in {$a} that you recently requested is now available for download. Please click on the link below to go to the download page.';
$string['reviewdata'] = 'Review data';
$string['retentionperiod'] = 'Retention period';
$string['retentionperiod_help'] = 'The retention period specifies the length of time that data should be kept for. When the retention period has expired, the data is flagged and listed for deletion, awaiting admin confirmation.';
$string['retentionperiodnotdefined'] = 'No retention period was defined';
$string['retentionperiodzero'] = 'No retention period';
$string['selectbulkaction'] = 'Please select a bulk action.';
$string['selectdatarequests'] = 'Please select data requests.';
$string['selectuserdatarequest'] = 'Select {$a->username}\'s {$a->requesttype} data request.';
$string['send'] = 'Send';
$string['sensitivedatareasons'] = 'Sensitive personal data processing reasons';
$string['sensitivedatareasons_help'] = 'Select one or more applicable reasons that exempts the prohibition of processing sensitive personal data tied to this purpose. For more information, please see  <a href="https://gdpr-info.eu/art-9-gdpr/" target="_blank">GDPR Art. 9.2</a>';
$string['setdefaults'] = 'Set defaults';
$string['statusapproved'] = 'Approved';
$string['statusawaitingapproval'] = 'Awaiting approval';
$string['statuscancelled'] = 'Cancelled';
$string['statuscomplete'] = 'Complete';
$string['statusready'] = 'Download ready';
$string['statusdeleted'] = 'Deleted';
$string['statusdetail'] = 'Status:';
$string['statusexpired'] = 'Expired';
$string['statuspreprocessing'] = 'Pre-processing';
$string['statusprocessing'] = 'Processing';
$string['statuspending'] = 'Pending';
$string['statusrejected'] = 'Rejected';
$string['subjectscope'] = 'Subject scope';
$string['subjectscope_help'] = 'The subject scope lists the roles which may be assigned in this context.';
$string['user'] = 'User';
$string['viewrequest'] = 'View the request';
$string['visible'] = 'Expand all';
