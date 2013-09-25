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
 * Email configuration page
 *
 * @package   message_email
 * @copyright 2011 Lancaster University Network Services Limited
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    $settings->add(new admin_setting_configtext('smtphosts', get_string('smtphosts', 'message_email'), get_string('configsmtphosts', 'message_email'), '', PARAM_RAW));
    $options = array('' => get_string('none', 'message_email'), 'ssl' => 'SSL', 'tls' => 'TLS');
    $settings->add(new admin_setting_configselect('smtpsecure', get_string('smtpsecure', 'message_email'), get_string('configsmtpsecure', 'message_email'), '', $options));
    $settings->add(new admin_setting_configtext('smtpuser', get_string('smtpuser', 'message_email'), get_string('configsmtpuser', 'message_email'), '', PARAM_NOTAGS));
    $settings->add(new admin_setting_configpasswordunmask('smtppass', get_string('smtppass', 'message_email'), get_string('configsmtpuser', 'message_email'), ''));
    $settings->add(new admin_setting_configtext('smtpmaxbulk', get_string('smtpmaxbulk', 'message_email'), get_string('configsmtpmaxbulk', 'message_email'), 1, PARAM_INT));
    $settings->add(new admin_setting_configtext('noreplyaddress', get_string('noreplyaddress', 'message_email'), get_string('confignoreplyaddress', 'message_email'), 'noreply@' . get_host_from_url($CFG->wwwroot), PARAM_NOTAGS));

    $charsets = get_list_of_charsets();
    unset($charsets['UTF-8']); // not needed here
    $options = array();
    $options['0'] = 'UTF-8';
    $options = array_merge($options, $charsets);
    $settings->add(new admin_setting_configselect('sitemailcharset', get_string('sitemailcharset', 'message_email'), get_string('configsitemailcharset','message_email'), '0', $options));
    $settings->add(new admin_setting_configcheckbox('allowusermailcharset', get_string('allowusermailcharset', 'message_email'), get_string('configallowusermailcharset', 'message_email'), 0));
    $settings->add(new admin_setting_configcheckbox('allowattachments', get_string('allowattachments', 'message_email'), get_string('configallowattachments', 'message_email'), 1));
    $options = array('LF'=>'LF', 'CRLF'=>'CRLF');
    $settings->add(new admin_setting_configselect('mailnewline', get_string('mailnewline', 'message_email'), get_string('configmailnewline','message_email'), 'LF', $options));
}
