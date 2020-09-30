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

$string['callbacknotimplemented'] = 'The callback is not implemented for component {$a}.';
$string['feeincludesurcharge'] = '{$a->fee} (includes {$a->surcharge}% surcharge for using this payment type)';
$string['nocurrencysupported'] = 'No payment in any currency is supported. Please make sure that at least one payment gateway is enabled.';
$string['nogateway'] = 'There is no payment gateway that can be used.';
$string['nogatewayselected'] = 'You first need to select a payment gateway.';
$string['selectpaymenttype'] = 'Select payment type';
$string['supportedcurrencies'] = 'Supported currencies';
$string['surcharge'] = 'Surcharge (percentage)';
$string['surcharge_desc'] = 'The surcharge is an additional percentage charged to users who choose to pay using this payment gateway.';
