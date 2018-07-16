<?php

// This file is part of the Iomad Certificate module for Moodle - http://moodle.org/
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
 * Language strings for the certificate module
 *
 * @package    mod_certificate
 * @copyright  Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['addlinklabel'] = 'Add another linked activity option';
$string['addlinktitle'] = 'Click to add another linked activity option';
$string['areaintro'] = 'Certificate introduction';
$string['awarded'] = 'Awarded';
$string['awardedto'] = 'Awarded To';
$string['back'] = 'Back';
$string['border'] = 'Border';
$string['borderblack'] = 'Black';
$string['borderblue'] = 'Blue';
$string['borderbrown'] = 'Brown';
$string['bordercolor'] = 'Border Lines';
$string['bordercolor_help'] = 'Since images can substantially increase the size of the pdf file, you may choose to print a border of lines instead of using a border image (be sure the Border Image option is set to No).  The Border Lines option will print a nice border of three lines of varying widths in the chosen color.';
$string['bordergreen'] = 'Green';
$string['borderlines'] = 'Lines';
$string['borderstyle'] = 'Border Image';
$string['borderstyle_help'] = 'The Border Image option allows you to choose a border image from the certificate/pix/borders folder.  Select the border image that you want around the certificate edges or select no border.';
$string['iomadcertificate'] = 'Verification for certificate code:';
$string['iomadcertificate:addinstance'] = 'Add a certificate instance';
$string['iomadcertificate:manage'] = 'Manage a certificate instance';
$string['iomadcertificate:printteacher'] = 'Be listed as a teacher on the certificate if the print teacher setting is on';
$string['iomadcertificate:student'] = 'Retrieve a certificate';
$string['iomadcertificate:view'] = 'View a certificate';
$string['iomadcertificate:viewother'] = 'View another users certificate';
$string['iomadcertificatename'] = 'Certificate Name';
$string['iomadcertificatereport'] = 'Certificates Report';
$string['iomadcertificatesfor'] = 'Certificates for';
$string['iomadcertificatetype'] = 'Certificate Type';
$string['iomadcertificatetype_help'] = 'This is where you determine the layout of the certificate. The certificate type folder includes four default certificates:
A4 Embedded prints on A4 size paper with embedded font.
A4 Non-Embedded prints on A4 size paper without embedded fonts.
Letter Embedded prints on letter size paper with embedded font.
Letter Non-Embedded prints on letter size paper without embedded fonts.

The non-embedded types use the Helvetica and Times fonts.  If you feel your users will not have these fonts on their computer, or if your language uses characters or symbols that are not accommodated by the Helvetica and Times fonts, then choose an embedded type.  The embedded types use the Dejavusans and Dejavuserif fonts.  This will make the pdf files rather large; thus it is not recommended to use an embedded type unless you must.

New type folders can be added to the certificate/type folder. The name of the folder and any new language strings for the new type must be added to the certificate language file.';
$string['certify'] = 'This is to certify that';
$string['code'] = 'Code';
$string['completiondate'] = 'Course Completion';
$string['course'] = 'For';
$string['coursegrade'] = 'Course Grade';
$string['coursename'] = 'Course';
$string['coursetimereq'] = 'Required minutes in course';
$string['coursetimereq_help'] = 'Enter here the minimum amount of time, in minutes, that a student must be logged into the course before they will be able to receive the certificate.';
$string['credithours'] = 'Credit Hours';
$string['customtext'] = 'Custom Text';
$string['customtext_help'] = 'If you want the certificate to print different names for the teacher than those who are assigned
the role of teacher, do not select Print Teacher or any signature image except for the line image.  Enter the teacher names in this text box as you would like them to appear.  By default, this text is placed in the lower left of the certificate. The following html tags are available: &lt;br&gt;, &lt;p&gt;, &lt;b&gt;, &lt;i&gt;, &lt;u&gt;, &lt;img&gt; (src and width (or height) are mandatory), &lt;a&gt; (href is mandatory), &lt;font&gt; (possible attributes are: color, (hex color code), face, (arial, times, courier, helvetica, symbol)).';
$string['date'] = 'On';
$string['datefmt'] = 'Date Format';
$string['datefmt_help'] = 'Choose a date format to print the date on the certificate. Or, choose the last option to have the date printed in the format of the user\'s chosen language.';
$string['datehelp'] = 'Date';
$string['deletissuediomadcertificates'] = 'Delete issued certificates';
$string['delivery'] = 'Delivery';
$string['delivery_help'] = 'Choose here how you would like your students to get their certificate.
Open in Browser: Opens the certificate in a new browser window.
Force Download: Opens the browser file download window.
Email Certificate: Choosing this option sends the certificate to the student as an email attachment.
After a user receives their certificate, if they click on the certificate link from the course homepage, they will see the date they received their certificate and will be able to review their received certificate.';
$string['designoptions'] = 'Design Options';
$string['download'] = 'Force download';
$string['emailiomadcertificate'] = 'Email';
$string['emailothers'] = 'Email Others';
$string['emailothers_help'] = 'Enter the email addresses here, separated by a comma, of those who should be alerted with an email whenever students receive a certificate.';
$string['emailstudenttext'] = 'Attached is your certificate for {$a->course}.';
$string['emailteachers'] = 'Email Teachers';
$string['emailteachers_help'] = 'If enabled, then teachers are alerted with an email whenever students receive a certificate.';
$string['emailteachermail'] = '
{$a->student} has received their certificate: \'{$a->certificate}\'
for {$a->course}.

You can review it here:

    {$a->url}';
$string['emailteachermailhtml'] = '
{$a->student} has received their certificate: \'<i>{$a->certificate}</i>\'
for {$a->course}.

You can review it here:

    <a href="{$a->url}">Certificate Report</a>.';
$string['entercode'] = 'Enter certificate code to verify:';
$string['fontsans'] = 'Sans-serif font family';
$string['fontsans_desc'] = 'Sans-serif font family for certificates with embedded fonts';
$string['fontserif'] = 'Serif font family';
$string['fontserif_desc'] = 'Serif font family for certificates with embedded fonts';
$string['getiomadcertificate'] = 'Get your certificate';
$string['grade'] = 'Grade';
$string['gradedate'] = 'Grade Date';
$string['gradefmt'] = 'Grade Format';
$string['gradefmt_help'] = 'There are three available formats if you choose to print a grade on the certificate:

Percentage Grade: Prints the grade as a percentage.
Points Grade: Prints the point value of the grade.
Letter Grade: Prints the percentage grade as a letter.';
$string['gradeletter'] = 'Letter Grade';
$string['gradepercent'] = 'Percentage Grade';
$string['gradepoints'] = 'Points Grade';
$string['imagetype'] = 'Image Type';
$string['incompletemessage'] = 'In order to download your certificate, you must first complete all required activities. Please return to the course to complete your coursework.';
$string['intro'] = 'Introduction';
$string['issueoptions'] = 'Issue Options';
$string['issued'] = 'Issued';
$string['issueddate'] = 'Date Issued';
$string['landscape'] = 'Landscape';
$string['lastviewed'] = 'You last received this certificate on:';
$string['letter'] = 'Letter';
$string['lockingoptions'] = 'Locking Options';
$string['modulename'] = 'Iomad Certificate';
$string['modulename_help'] = 'This module allows for the dynamic generation of certificates based on predefined conditions set by the teacher.';
$string['modulename_link'] = 'Certificate_module';
$string['modulenameplural'] = 'Iomad Certificates';
$string['myiomadcertificates'] = 'My Certificates';
$string['noiomadcertificates'] = 'There are no certificates';
$string['noiomadcertificatesissued'] = 'There are no certificates that have been issued';
$string['noiomadcertificatesreceived'] = 'has not received any course certificates.';
$string['nofileselected'] = 'Must choose a file to upload!';
$string['nogrades'] = 'No grades available';
$string['notapplicable'] = 'N/A';
$string['notfound'] = 'The certificate number could not be validated.';
$string['notissued'] = 'Not Issued';
$string['notissuedyet'] = 'Not issued yet';
$string['notreceived'] = 'You have not received this certificate';
$string['openbrowser'] = 'Open in new window';
$string['opendownload'] = 'Click the button below to save your certificate to your computer.';
$string['openemail'] = 'Click the button below and your certificate will be sent to you as an email attachment.';
$string['openwindow'] = 'Click the button below to open your certificate in a new browser window.';
$string['or'] = 'Or';
$string['orientation'] = 'Orientation';
$string['orientation_help'] = 'Choose whether you want your certificate orientation to be portrait or landscape.';
$string['pluginadministration'] = 'Iomad Certificate administration';
$string['pluginname'] = 'Iomad Certificate';
$string['portrait'] = 'Portrait';
$string['printdate'] = 'Print Date';
$string['printdate_help'] = 'This is the date that will be printed, if a print date is selected. If the course completion date is selected but the student has not completed the course, the date received will be printed. You can also choose to print the date based on when an activity was graded. If a certificate is issued before that activity is graded, the date received will be printed.';
$string['printerfriendly'] = 'Printer-friendly page';
$string['printhours'] = 'Print Credit Hours';
$string['printhours_help'] = 'Enter here the number of credit hours to be printed on the certificate.';
$string['printgrade'] = 'Print Grade';
$string['printgrade_help'] = 'You can choose any available course grade items from the gradebook to print the user\'s grade received for that item on the certificate.  The grade items are listed in the order in which they appear in the gradebook. Choose the format of the grade below.';
$string['printnumber'] = 'Print Code';
$string['printnumber_help'] = 'A unique 10-digit code of random letters and numbers can be printed on the certificate. This number can then be verified by comparing it to the code number displayed in the certificates report.';
$string['printoutcome'] = 'Print Outcome';
$string['printoutcome_help'] = 'You can choose any course outcome to print the name of the outcome and the user\'s received outcome on the certificate.  An example might be: Assignment Outcome: Proficient.';
$string['printseal'] = 'Seal or Logo Image';
$string['printseal_help'] = 'This option allows you to select a seal or logo to print on the certificate from the certificate/pix/seals folder. By default, this image is placed in the lower right corner of the certificate.';
$string['printsignature'] = 'Signature Image';
$string['printsignature_help'] = 'This option allows you to print a signature image from the certificate/pix/signatures folder.  You can print a graphic representation of a signature, or print a line for a written signature. By default, this image is placed in the lower left of the certificate.';
$string['printteacher'] = 'Print Teacher Name(s)';
$string['printteacher_help'] = 'For printing the teacher name on the certificate, set the role of teacher at the module level.  Do this if, for example, you have more than one teacher for the course or you have more than one certificate in the course and you want to print different teacher names on each certificate.  Click to edit the certificate, then click on the Locally assigned roles tab.  Then assign the role of Teacher (editing teacher) to the certificate (they do not HAVE to be a teacher in the course--you can assign that role to anyone).  Those names will be printed on the certificate for teacher.';
$string['printwmark'] = 'Watermark Image';
$string['printwmark_help'] = 'A watermark file can be placed in the background of the certificate. A watermark is a faded graphic. A watermark could be a logo, seal, crest, wording, or whatever you want to use as a graphic background.';
$string['receivedcerts'] = 'Received certificates';
$string['receiveddate'] = 'Date Received';
$string['removecert'] = 'Issued certificates removed';
$string['report'] = 'Report';
$string['reportcert'] = 'Report Certificates';
$string['reportcert_help'] = 'If you choose yes here, then this certificate\'s date received, code number, and the course name will be shown on the user certificate reports.  If you choose to print a grade on this certificate, then that grade will also be shown on the certificate report.';
$string['requiredtimenotmet'] = 'You must spend at least a minimum of {$a->requiredtime} minutes in the course before you can access this certificate';
$string['requiredtimenotvalid'] = 'The required time must be a valid number greater than 0';
$string['reviewiomadcertificate'] = 'Review your certificate';
$string['savecert'] = 'Save Certificates';
$string['savecert_help'] = 'If you choose this option, then a copy of each user\'s certificate pdf file is saved in the course files moddata folder for that certificate. A link to each user\'s saved certificate will be displayed in the certificate report.';
$string['seal'] = 'Seal';
$string['sigline'] = 'line';
$string['signature'] = 'Signature';
$string['statement'] = 'has completed the course';
$string['summaryofattempts'] = 'Summary of previously received certificates';
$string['textoptions'] = 'Text Options';
$string['title'] = 'CERTIFICATE of ACHIEVEMENT';
$string['to'] = 'Awarded to';
$string['typeA4_embedded'] = 'A4 Embedded';
$string['typeA4_non_embedded'] = 'A4 Non-Embedded';
$string['typeletter_embedded'] = 'Letter Embedded';
$string['typeletter_non_embedded'] = 'Letter Non-Embedded';
$string['unsupportedfiletype'] = 'File must be a jpeg or png file';
$string['uploadimage'] = 'Upload image';
$string['uploadimagedesc'] = 'This button will take you to a new screen where you will be able to upload images.';
$string['userdateformat'] = 'User\'s Language Date Format';
$string['validate'] = 'Verify';
$string['verifyiomadcertificate'] = 'Verify Certificate';
$string['viewiomadcertificateviews'] = 'View {$a} issued certificates';
$string['viewed'] = 'You received this certificate on:';
$string['viewtranscript'] = 'View Certificates';
$string['watermark'] = 'Watermark';
$string['companycertify'] = 'This Certificate of Completion recognises that';
$string['companydetails'] = 'has successfully completed the web-based training program entitled';
$string['companyscore'] = 'with an overall score of {$a}';
$string['companydate'] = 'on {$a}';
$string['companydatecap'] = 'On {$a}';

