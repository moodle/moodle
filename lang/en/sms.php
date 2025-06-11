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
 * Strings for component 'sms', language 'en'
 *
 * @package    core
 * @category   string
 * @copyright  2024 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
$string['actions'] = 'Actions';
$string['countrycode'] = 'Default country code';
$string['countrycode_help'] = 'Country code to be added to phone numbers if users don\'t enter their own country code. Enter the number without the leading \'+\' symbol.';
$string['createnewgateway'] = 'Create new SMS gateway';
$string['delete_sms_gateway'] = 'Delete SMS gateway';
$string['delete_sms_gateway_confirmation'] = 'This will delete the {$a->gateway} SMS gateway.';
$string['edit_sms_gateway'] = 'Edit {$a->gateway} SMS gateway';
$string['gateway'] = 'Gateway';
$string['gateway_name'] = 'Gateway name';
$string['manage_sms_gateways'] = 'Manage SMS gateways';
$string['phonenumbernotvalid'] = 'Format of phone number not recognised: {$a->message}';
$string['privacy:metadata:sms_messages'] = 'Stores messages sent via SMS';
$string['privacy:metadata:sms_messages:content'] = 'The message text';
$string['privacy:metadata:sms_messages:id'] = 'The ID of the message';
$string['privacy:metadata:sms_messages:recipient'] = 'The phone number that the message was sent to';
$string['privacy:metadata:sms_messages:recipientuserid'] = 'The user who the message was sent to, if known';
$string['privacy:metadata:sms_messages:status'] = 'The status of the message';
$string['privacy:metadata:sms_messages:timecreated'] = 'The time the message was created';
$string['privacy:sms:sensitive_not_shown'] = 'The content of this message was not stored as it was marked as containing sensitive content.';
$string['select_sms_gateways'] = 'SMS gateway provider';
$string['sms'] = 'SMS';
$string['status:gateway_failed'] = 'The gateway has failed to send the message';
$string['status:gateway_not_available'] = 'The gateway is not available to send the message';
$string['status:gateway_queued'] = 'The message is queued to be sent by the gateway';
$string['status:gateway_rejected'] = 'The gateway has rejected the message';
$string['status:gateway_sent'] = 'The message has been sent by the gateway';
$string['status:message_over_size'] = 'The message is too large to be sent by the gateway';
$string['status:unknown'] = 'Unable to determine the status of the message';
$string['sms_gateway_deleted'] = '{$a->gateway} SMS gateway deleted';
$string['sms_gateway_delete_failed'] = 'Cannot delete the {$a->gateway} SMS gateway. The gateway is either in use or there\'s a database issue. Check if the gateway is active or contact your database administrator for help.';
$string['sms_gateway_disable_failed'] = 'Cannot disable the SMS gateway. The gateway is either in use or there\'s a database issue. Check if the gateway is active or contact your database administrator for help.';
$string['sms_gateways'] = 'SMS gateways';
$string['sms_gateways_info'] = 'Create and manage SMS gateways to send SMS messages from your site.';
