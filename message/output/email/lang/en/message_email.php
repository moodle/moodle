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
 * Strings for component 'message_email', language 'en'
 *
 * @package    message_email
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['allowattachments'] = 'Allow attachments';
$string['allowusermailcharset'] = 'Allow user to select character set';
$string['configallowattachments'] = 'If enabled, emails sent from the site can have attachments, such as badges.';
$string['configallowusermailcharset'] = 'If enabled, users can choose an email charset in their profile settings.';
$string['configmailnewline'] = 'Newline characters used in mail messages. CRLF is required according to RFC 822bis, some mail servers do automatic conversion from LF to CRLF, other mail servers do incorrect conversion from CRLF to CRCRLF, yet others reject mails with bare LF (qmail for example). Try changing this setting if you are having problems with undelivered emails or double newlines.';
$string['confignoreplyaddress'] = 'Emails are sometimes sent out on behalf of a user (eg forum posts). The email address you specify here will be used as the "From" address in those cases when the recipients should not be able to reply directly to the user (eg when a user chooses to keep their address private).';
$string['configemailonlyfromnoreplyaddress'] = 'If enabled, all email will be sent using the no-reply address as the "from" address. This can be used to stop anti-spoofing controls in external mail systems blocking emails.';
$string['configsitemailcharset'] = 'This setting specifies the default charset for all emails sent from the site.';
$string['configsmtphosts'] = 'Give the full name of one or more local SMTP servers that Moodle should use to send mail (eg \'mail.a.com\' or \'mail.a.com;mail.b.com\'). To specify a non-default port (i.e other than port 25), you can use the [server]:[port] syntax (eg \'mail.a.com:587\'). For secure connections, port 465 is usually used with SSL, port 587 is usually used with TLS, specify security protocol below if required. If you leave this field blank, Moodle will use the PHP default method of sending mail.';
$string['configsmtpmaxbulk'] = 'Maximum number of messages sent per SMTP session. Grouping messages may speed up the sending of emails. Values lower than 2 force creation of new SMTP session for each email.';
$string['configsmtpsecure'] = 'If SMTP server requires secure connection, specify the correct protocol type.';
$string['configsmtpuser'] = 'If you have specified an SMTP server above, and the server requires authentication, then enter the username and password here.';
$string['email'] = 'Send email notifications to';
$string['emailonlyfromnoreplyaddress'] = 'Always send email from the no-reply address?';
$string['ifemailleftempty'] = 'Leave empty to send notifications to {$a}';
$string['mailnewline'] = 'Newline characters in mail';
$string['none'] = 'None';
$string['noreplyaddress'] = 'No-reply address';
$string['pluginname'] = 'Email';
$string['sitemailcharset'] = 'Character set';
$string['smtphosts'] = 'SMTP hosts';
$string['smtpmaxbulk'] = 'SMTP session limit';
$string['smtppass'] = 'SMTP password';
$string['smtpsecure'] = 'SMTP security';
$string['smtpuser'] = 'SMTP username';