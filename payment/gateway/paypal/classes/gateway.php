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
 * Contains class for PayPal payment gateway.
 *
 * @package    pg_paypal
 * @copyright  2019 Shamim Rezaie <shamim@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace pg_paypal;

defined('MOODLE_INTERNAL') || die();

/**
 * The gateway class for PayPal payment gateway.
 *
 * @copyright  2019 Shamim Rezaie <shamim@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class gateway extends \core_payment\gateway {
    public static function get_supported_currencies(): array {
        // See https://developer.paypal.com/docs/api/reference/currency-codes/,
        // 3-character ISO-4217: https://en.wikipedia.org/wiki/ISO_4217#Active_codes.
        return [
            'AUD', 'BRL', 'CAD', 'CHF', 'CZK', 'DKK', 'EUR', 'GBP', 'HKD', 'HUF', 'ILS', 'INR', 'JPY',
            'MXN', 'MYR', 'NOK', 'NZD', 'PHP', 'PLN', 'RUB', 'SEK', 'SGD', 'THB', 'TRY', 'TWD', 'USD'
        ];
    }

    /**
     * Configuration form for the gateway instance
     *
     * Use $form->get_mform() to access the \MoodleQuickForm instance
     *
     * @param \core_payment\form\account_gateway $form
     */
    public static function add_configuration_to_gateway_form(\core_payment\form\account_gateway $form): void {
        $mform = $form->get_mform();

        $mform->addElement('text', 'brandname', get_string('brandname', 'pg_paypal'));
        $mform->setType('brandname', PARAM_TEXT);
        $mform->addHelpButton('brandname', 'brandname', 'pg_paypal');

        $mform->addElement('text', 'clientid', get_string('clientid', 'pg_paypal'));
        $mform->setType('clientid', PARAM_TEXT);
        $mform->addHelpButton('clientid', 'clientid', 'pg_paypal');

        $mform->addElement('text', 'secret', get_string('secret', 'pg_paypal'));
        $mform->setType('secret', PARAM_TEXT);
        $mform->addHelpButton('secret', 'secret', 'pg_paypal');

        $options = [
            'live' => get_string('live', 'pg_paypal'),
            'sandbox'  => get_string('sandbox', 'pg_paypal'),
        ];

        $mform->addElement('select', 'environment', get_string('environment', 'pg_paypal'), $options);
        $mform->addHelpButton('environment', 'environment', 'pg_paypal');
    }

    /**
     * Validates the gateway configuration form.
     *
     * @param \core_payment\form\account_gateway $form
     * @param \stdClass $data
     * @param array $files
     * @param array $errors form errors (passed by reference)
     */
    public static function validate_gateway_form(\core_payment\form\account_gateway $form,
                                                 \stdClass $data, array $files, array &$errors): void {
        if ($data->enabled &&
                (empty($data->brandname) || empty($data->clientid) || empty($data->secret))) {
            $errors['enabled'] = get_string('gatewaycannotbeenabled', 'payment');
        }
    }
}
