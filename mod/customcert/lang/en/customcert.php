<?php
// This file is part of the customcert module for Moodle - http://moodle.org/
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
 * Language strings for the customcert module.
 *
 * @package    mod_customcert
 * @copyright  2013 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['activity'] = 'Activity';
$string['addcertpage'] = 'Add page';
$string['addelement'] = 'Add element';
$string['aligncenter'] = 'Centered';
$string['alignleft'] = 'Left alignment';
$string['alignment'] = 'Alignment';
$string['alignment_help'] = 'This property sets the horizontal alignment of the element. Some elements may not support this, while the behaviour of others may differ.';
$string['alignright'] = 'Right alignment';
$string['awardedto'] = 'Awarded to';
$string['cannotverifyallcertificates'] = 'You do not have the permission to verify all certificates on the site.';
$string['certificate'] = 'Certificate';
$string['certificateexecutionperiod'] = 'Certificate execution period';
$string['certificateexecutionperiod_desc'] = 'If not 0, the task will not process certificates whose course has been inactive or the last issue is older than the configured time. This may help to improve the performance of the scheduled task.';
$string['certificatesperrun'] = 'Certificates per run';
$string['certificatesperrun_desc'] = 'Enter the number of certificates to process per scheduled task run where 0 means it will process all certificates.';
$string['code'] = 'Code';
$string['copy'] = 'Copy';
$string['coursetimereq'] = 'Required minutes in course';
$string['coursetimereq_help'] = 'Enter here the minimum amount of time, in minutes, that a student must be logged into the course before they will be able to receive
the certificate.';
$string['createtemplate'] = 'Create template';
$string['customcert:addinstance'] = 'Add a new custom certificate instance';
$string['customcert:manage'] = 'Manage a custom certificate';
$string['customcert:manageemailothers'] = 'Manage email others setting';
$string['customcert:manageemailstudents'] = 'Manage email students setting';
$string['customcert:manageemailteachers'] = 'Manage email teachers setting';
$string['customcert:managelanguages'] = 'Manage language on edit form';
$string['customcert:manageprotection'] = 'Manage protection setting';
$string['customcert:managerequiredtime'] = 'Manage time required setting';
$string['customcert:manageverifyany'] = 'Manage verification setting';
$string['customcert:receiveissue'] = 'Receive a certificate';
$string['customcert:verifyallcertificates'] = 'Verify all certificates on the site';
$string['customcert:verifycertificate'] = 'Verify a certificate';
$string['customcert:view'] = 'View a custom certificate';
$string['customcert:viewallcertificates'] = 'View all certificates';
$string['customcert:viewreport'] = 'View course report';
$string['customcertsettings'] = 'Custom certificate settings';
$string['deletecertpage'] = 'Delete page';
$string['deleteconfirm'] = 'Delete confirmation';
$string['deleteelement'] = 'Delete element';
$string['deleteelementconfirm'] = 'Are you sure you want to delete this element?';
$string['deleteissueconfirm'] = 'Are you sure you want to delete this certificate issue?';
$string['deleteissuedcertificates'] = 'Delete issued certificates';
$string['deletepageconfirm'] = 'Are you sure you want to delete this certificate page?';
$string['deletetemplateconfirm'] = 'Are you sure you want to delete this certificate template?';
$string['deliveryoptiondownload'] = 'Send to the browser and force a file download';
$string['deliveryoptioninline'] = 'Send the file inline to the browser';
$string['deliveryoptions'] = 'Delivery options';
$string['description'] = 'Description';
$string['downloadallissuedcertificates'] = 'Download all issued certificates';
$string['downloadallsitecertificates'] = 'Download all site certificates';
$string['downloadallsitecertificatesdesc'] = 'This will download all the certificates on the site in a zip file.';
$string['duplicate'] = 'Duplicate';
$string['duplicateconfirm'] = 'Duplicate confirmation';
$string['duplicatetemplateconfirm'] = 'Are you sure you want to duplicate this certificate template?';
$string['editcustomcert'] = 'Edit certificate';
$string['editelement'] = 'Edit element';
$string['edittemplate'] = 'Edit template';
$string['elementheight'] = 'Height';
$string['elementheight_help'] = 'Specify the height of the element. If \'0\' is allowed it is automatically calculated.';
$string['elementname'] = 'Element name';
$string['elementname_help'] = 'This will be the name used to identify this element when editing a certificate. Note: this will not displayed on the PDF.';
$string['elementplugins'] = 'Element plugins';
$string['elements'] = 'Elements';
$string['elements_help'] = 'This is the list of elements that will be displayed on the certificate.

Please note: The elements are rendered in this order. The order can be changed by using the arrows next to each element.';
$string['elementwidth'] = 'Width';
$string['elementwidth_help'] = 'Specify the width of the element. If \'0\' is allowed it is automatically calculated.';
$string['emailnonstudentbody'] = 'Attached is the certificate \'{$a->certificatename}\' for \'{$a->userfullname}\' for the course \'{$a->coursefullname}\'.';
$string['emailnonstudentbodyplaintext'] = 'Attached is the certificate \'{$a->certificatename}\' for \'{$a->userfullname}\' for the course \'{$a->coursefullname}\'.';
$string['emailnonstudentcertificatelinktext'] = 'View certificate report';
$string['emailnonstudentgreeting'] = 'Hi';
$string['emailnonstudentsubject'] = '{$a->coursefullname}: {$a->certificatename}';
$string['emailothers'] = 'Email others';
$string['emailothers_help'] = 'If set this will email the email addresses listed here (separated by a comma) with a copy of the certificate when it becomes available. <strong>Warning:</strong> Setting this field before you have finished creating the certificate will email the addresses an incomplete certificate.';
$string['emailstudentbody'] = 'Attached is your certificate \'{$a->certificatename}\' for the course \'{$a->coursefullname}\'.';
$string['emailstudentbodyplaintext'] = 'Attached is your certificate \'{$a->certificatename}\' for the course \'{$a->coursefullname}\'.';
$string['emailstudentcertificatelinktext'] = 'View certificate';
$string['emailstudentgreeting'] = 'Dear {$a}';
$string['emailstudents'] = 'Email students';
$string['emailstudents_help'] = 'If set this will email the students a copy of the certificate when it becomes available. <strong>Warning:</strong> Setting this to \'Yes\' before you have finished creating the certificate will email the student an incomplete certificate.';
$string['emailstudentsubject'] = '{$a->coursefullname}: {$a->certificatename}';
$string['emailteachers'] = 'Email teachers';
$string['emailteachers_help'] = 'If set this will email the teachers a copy of the certificate when it becomes available. <strong>Warning:</strong> Setting this to \'Yes\' before you have finished creating the certificate will email the teacher an incomplete certificate.';
$string['eventelementcreated'] = 'Custom certificate element created';
$string['eventelementdeleted'] = 'Custom certificate element deleted';
$string['eventelementupdated'] = 'Custom certificate element updated';
$string['eventpagecreated'] = 'Custom certificate page created';
$string['eventpagedeleted'] = 'Custom certificate page deleted';
$string['eventpageupdated'] = 'Custom certificate page updated';
$string['eventtemplatecreated'] = 'Custom certificate template created';
$string['eventtemplatedeleted'] = 'Custom certificate template deleted';
$string['eventtemplateupdated'] = 'Custom certificate template updated';
$string['exampledatawarning'] = 'Some of these values may just be an example to ensure positioning of the elements is possible.';
$string['font'] = 'Font';
$string['font_help'] = 'The font used when generating this element.';
$string['fontcolour'] = 'Colour';
$string['fontcolour_help'] = 'The colour of the font.';
$string['fontsize'] = 'Size';
$string['fontsize_help'] = 'The size of the font in points.';
$string['getcustomcert'] = 'View certificate';
$string['gradeoutcome'] = 'Outcome';
$string['height'] = 'Height';
$string['height_help'] = 'This is the height of the certificate PDF in mm. For reference an A4 piece of paper is 297mm high and a letter is 279mm high.';
$string['includeinnotvisiblecourses'] = 'Include certificates in hidden courses';
$string['includeinnotvisiblecourses_desc'] = 'Certificates from hidden courses are not proccesed by default. If you want to include them, enable this setting.';
$string['invalidcode'] = 'Invalid code supplied.';
$string['invalidcolour'] = 'Invalid colour chosen, please enter a valid HTML colour name, or a six-digit, or three-digit hexadecimal colour.';
$string['invalidelementwidthorheightnotnumber'] = 'Please enter a valid number.';
$string['invalidelementwidthorheightzeroallowed'] = 'Please enter a number greater than or equal to 0.';
$string['invalidelementwidthorheightzeronotallowed'] = 'Please enter a number greater than 0.';
$string['invalidheight'] = 'The height has to be a valid number greater than 0.';
$string['invalidmargin'] = 'The margin has to be a valid number greater than 0.';
$string['invalidposition'] = 'Please select a positive number for position {$a}.';
$string['invalidwidth'] = 'The width has to be a valid number greater than 0.';
$string['landscape'] = 'Landscape';
$string['languageoptions'] = 'Force Certificate Language';
$string['leftmargin'] = 'Left margin';
$string['leftmargin_help'] = 'This is the left margin of the certificate PDF in mm.';
$string['listofissues'] = 'Recipients: {$a}';
$string['load'] = 'Load';
$string['loadtemplate'] = 'Load template';
$string['loadtemplatemsg'] = 'Are you sure you wish to load this template? This will remove any existing pages and elements for this certificate.';
$string['managetemplates'] = 'Manage templates';
$string['managetemplatesdesc'] = 'This link will take you to a new screen where you will be able to manage templates used by Custom certificate activities in courses.';
$string['modify'] = 'Modify';
$string['modulename'] = 'Custom certificate';
$string['modulename_help'] = 'This module allows for the dynamic generation of PDF certificates.';
$string['modulename_link'] = 'Custom_certificate_module';
$string['modulenameplural'] = 'Custom certificates';
$string['mycertificates'] = 'My certificates';
$string['mycertificatesdescription'] = 'These are the certificates you have been issued by either email or downloading manually.';
$string['name'] = 'Name';
$string['nametoolong'] = 'You have exceeded the maximum length allowed for the name';
$string['nocustomcerts'] = 'There are no certificates for this course';
$string['noimage'] = 'No image';
$string['norecipients'] = 'No recipients';
$string['notemplates'] = 'No templates';
$string['notissued'] = 'Not awarded';
$string['notverified'] = 'Not verified';
$string['numbersuffix_nd_as_in_second'] = 'nd';
$string['numbersuffix_rd_as_in_third'] = 'rd';
$string['numbersuffix_st_as_in_first'] = 'st';
$string['options'] = 'Options';
$string['page'] = 'Page {$a}';
$string['pluginadministration'] = 'Custom certificate administration';
$string['pluginname'] = 'Custom certificate';
$string['portrait'] = 'Portrait';
$string['posx'] = 'Position X';
$string['posx_help'] = 'This is the position in mm from the top left corner you wish the element\'s reference point to locate in the x direction.';
$string['posy'] = 'Position Y';
$string['posy_help'] = 'This is the position in mm from the top left corner you wish the element\'s reference point to locate in the y direction.';
$string['preventcopy'] = 'Prevent copy';
$string['preventcopy_desc'] = 'Enable protection from copy action.';
$string['preventmodify'] = 'Prevent modify';
$string['preventmodify_desc'] = 'Enable protection from modify action.';
$string['preventprint'] = 'Prevent print';
$string['preventprint_desc'] = 'Enable protection from print action.';
$string['print'] = 'Print';
$string['privacy:metadata:customcert_issues'] = 'The list of issued certificates';
$string['privacy:metadata:customcert_issues:code'] = 'The code that belongs to the certificate';
$string['privacy:metadata:customcert_issues:customcertid'] = 'The ID of the certificate';
$string['privacy:metadata:customcert_issues:emailed'] = 'Whether or not the certificate was emailed';
$string['privacy:metadata:customcert_issues:timecreated'] = 'The time the certificate was issued';
$string['privacy:metadata:customcert_issues:userid'] = 'The ID of the user who was issued the certificate';
$string['rearrangeelements'] = 'Reposition elements';
$string['rearrangeelementsheading'] = 'Drag and drop elements to change where they are positioned on the certificate.';
$string['receiveddate'] = 'Awarded on';
$string['refpoint'] = 'Reference point location';
$string['refpoint_help'] = 'The reference point is the location of an element from which its x and y coordinates are determined. It is indicated by the \'+\' that appears in the centre or corners of the element.';
$string['replacetemplate'] = 'Replace';
$string['requiredtimenotmet'] = 'You must spend at least a minimum of {$a->requiredtime} minutes in the course before you can access this certificate.';
$string['rightmargin'] = 'Right margin';
$string['rightmargin_help'] = 'This is the right margin of the certificate PDF in mm.';
$string['save'] = 'Save';
$string['saveandclose'] = 'Save and close';
$string['saveandcontinue'] = 'Save and continue';
$string['savechanges'] = 'Save changes';
$string['savechangespreview'] = 'Save changes and preview';
$string['savetemplate'] = 'Save template';
$string['scheduledtaskconfigdesc'] = 'Configure the settings for the scheduled task that processes certificates.';
$string['scheduledtaskconfigheading'] = 'Scheduled task configuration';
$string['search:activity'] = 'Custom certificate - activity information';
$string['setprotection'] = 'Set protection';
$string['setprotection_help'] = 'Choose the actions you wish to prevent users from performing on this certificate.';
$string['showposxy'] = 'Show position X and Y';
$string['showposxy_desc'] = 'This will show the X and Y position when editing of an element, allowing the user to accurately specify the location.

This isn\'t required if you plan on solely using the drag and drop interface for this purpose.';
$string['subplugintype_customcertelement'] = 'Element';
$string['subplugintype_customcertelement_plural'] = 'Elements';
$string['taskemailcertificate'] = 'Handles emailing certificates.';
$string['taskissuecertificate'] = 'Issue certificates task';
$string['templatename'] = 'Template name';
$string['templatenameexists'] = 'That template name is currently in use, please choose another.';
$string['topcenter'] = 'Center';
$string['topleft'] = 'Top left';
$string['topright'] = 'Top right';
$string['type'] = 'Type';
$string['uploadimage'] = 'Upload image';
$string['uploadimagedesc'] = 'This link will take you to a new screen where you will be able to upload images. Images uploaded using
this method will be available throughout your site to all users who are able to create a certificate.';
$string['useadhoc'] = 'Use Email Certificate Ad-hoc Task';
$string['useadhoc_desc'] = 'When enabled, emails related to certificates will be handled immediately through an ad-hoc task created for each issue. When disabled, emails will be managed by the regular scheduled task.

Enabling this option may improve the performance of the scheduled task by offloading email processing to ad-hoc tasks.';
$string['userlanguage'] = 'Use user preferences';
$string['userlanguage_help'] = 'You can force the language of the certificate to override the user\'s language preferences.';
$string['verified'] = 'Verified';
$string['verify'] = 'Verify';
$string['verifyallcertificates'] = 'Allow verification of all certificates';
$string['verifyallcertificates_desc'] = 'When this setting is enabled any person (including users not logged in) can visit the link \'{$a}\' in order to verify any certificate on the site, rather than having to go to the verification link for each certificate.

Note - this only applies to certificates where \'Allow anyone to verify a certificate\' has been set to \'Yes\' in the certificate settings.';
$string['verifycertificate'] = 'Verify certificate';
$string['verifycertificateanyone'] = 'Allow anyone to verify a certificate';
$string['verifycertificateanyone_help'] = 'This setting enables anyone with the certificate verification link (including users not logged in) to verify a certificate.';
$string['verifycertificatedesc'] = 'This link will take you to a new screen where you will be able to verify certificates on the site';
$string['width'] = 'Width';
$string['width_help'] = 'This is the width of the certificate PDF in mm. For reference an A4 piece of paper is 210mm wide and a letter is 216mm wide.';
