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
 * Strings for component 'payment', language 'en'
 *
 * @package   core_payment
 * @copyright 2019 Shamim Rezaie <shamim@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['accountarchived'] = 'Archived';
$string['accountdeleteconfirm'] = 'If this account has previous payments, it will be archived, otherwise its configuration data will be permanently deleted. Are you sure you want to continue?';
$string['accountconfignote'] = 'Payment gateways for this account will be configured separately';
$string['accountidnumber'] = 'ID number';
$string['accountidnumber_help'] = 'The ID number is only used when matching the account against external systems and is not displayed anywhere on the site. If the account has an official code name it may be entered, otherwise the field can be left blank.';
$string['accountname'] = 'Account name';
$string['accountname_help'] = 'How this account will be identified for teachers or managers who set up payments (for example in the course enrolment plugin).';
$string['accountnotavailable'] = 'Not available';
$string['paymentaccountsexplained'] = 'Create one or multiple payment accounts for this site. Each account includes configuration for available payment gateways. The person who configures payments on the site (for example, payment for the course enrolment) will be able to choose from the available accounts.';
$string['createaccount'] = 'Create payment account';
$string['deleteorarchive'] = 'Delete or archive';
$string['editpaymentaccount'] = 'Edit payment account';
$string['eventaccountcreated'] = 'Payment account created';
$string['eventaccountdeleted'] = 'Payment account deleted';
$string['eventaccountupdated'] = 'Payment account updated';
$string['feeincludesurcharge'] = '{$a->fee} (includes {$a->surcharge}% surcharge for using this payment type)';
$string['gatewaycannotbeenabled'] = 'The payment gateway cannot be enabled because the configuration is incomplete.';
$string['gatewaydisabled'] = 'Disabled';
$string['gatewayenabled'] = 'Enabled';
$string['gatewaynotfound'] = 'Gateway not found';
$string['gotomanageplugins'] = 'Enable and disable payment gateways and set surcharges via {$a}.';
$string['gotopaymentaccounts'] = 'You can create multiple payment accounts using any of these gateways on the {$a} page';
$string['hidearchived'] = 'Hide archived';
$string['noaccountsavilable'] = 'No payment accounts are available.';
$string['nocurrencysupported'] = 'No payment in any currency is supported. Please make sure that at least one payment gateway is enabled.';
$string['nogateway'] = 'There is no payment gateway that can be used.';
$string['nogatewayselected'] = 'You first need to select a payment gateway.';
$string['payments'] = 'Payments';
$string['paymentaccount'] = 'Payment account';
$string['paymentaccounts'] = 'Payment accounts';
$string['privacy:metadata:database:payments'] = 'Information about the payments.';
$string['privacy:metadata:database:payments:amount'] = 'The amount for the payment.';
$string['privacy:metadata:database:payments:currency'] = 'The currency of the payment.';
$string['privacy:metadata:database:payments:gateway'] = 'The payment gateway that is used for the payment.';
$string['privacy:metadata:database:payments:timecreated'] = 'The time when the payment was made.';
$string['privacy:metadata:database:payments:timemodified'] = 'The time when the payment record was last updated.';
$string['privacy:metadata:database:payments:userid'] = 'The user who made the payment.';
$string['restoreaccount'] = 'Restore';
$string['selectpaymenttype'] = 'Select payment type';
$string['showarchived'] = 'Show archived';
$string['supportedcurrencies'] = 'Supported currencies';
$string['surcharge'] = 'Surcharge (percentage)';
$string['surcharge_desc'] = 'The surcharge is an additional percentage charged to users who choose to pay using this payment gateway.';
