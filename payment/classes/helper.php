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
 * Contains helper class for the payment subsystem.
 *
 * @package    core_payment
 * @copyright  2019 Shamim Rezaie <shamim@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_payment;

use core_payment\event\account_created;
use core_payment\event\account_deleted;
use core_payment\event\account_updated;

/**
 * Helper class for the payment subsystem.
 *
 * @copyright  2019 Shamim Rezaie <shamim@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class helper {

    /**
     * Returns an accumulated list of supported currencies by all payment gateways.
     *
     * @return string[] An array of the currency codes in the three-character ISO-4217 format
     */
    public static function get_supported_currencies(): array {
        $currencies = [];

        $plugins = \core_plugin_manager::instance()->get_enabled_plugins('pg');
        foreach ($plugins as $plugin) {
            /** @var \pg_paypal\gateway $classname */
            $classname = '\pg_' . $plugin . '\gateway';

            $currencies += component_class_callback($classname, 'get_supported_currencies', [], []);
        }

        $currencies = array_unique($currencies);

        return $currencies;
    }

    /**
     * Returns the list of gateways that can process payments in the given currency.
     *
     * @param string $component
     * @param string $paymentarea
     * @param int $componentid
     * @return string[]
     */
    public static function get_available_gateways(string $component, string $paymentarea, int $componentid): array {
        $gateways = [];

        [
            'amount' => $amount,
            'currency' => $currency,
            'accountid' => $accountid,
        ] = self::get_cost($component, $paymentarea, $componentid);
        $account = new account($accountid);
        if (!$account->get('id') || !$account->get('enabled')) {
            return $gateways;
        }

        foreach ($account->get_gateways() as $plugin => $gateway) {
            if (!$gateway->get('enabled')) {
                continue;
            }
            /** @var gateway $classname */
            $classname = '\pg_' . $plugin . '\gateway';

            $currencies = component_class_callback($classname, 'get_supported_currencies', [], []);
            if (in_array($currency, $currencies)) {
                $gateways[] = $plugin;
            }
        }

        return $gateways;
    }

    /**
     * Rounds the cost based on the currency fractional digits, can also apply surcharge
     *
     * @param float $amount amount in the currency units
     * @param string $currency currency, used for calculating the number of fractional digits
     * @param float $surcharge surcharge in percents
     * @return float
     */
    public static function get_rounded_cost(float $amount, string $currency, float $surcharge = 0): float {
        $amount = $amount * (100 + $surcharge) / 100;

        $locale = get_string('localecldr', 'langconfig');
        $fmt = \NumberFormatter::create($locale, \NumberFormatter::CURRENCY);
        $localisedcost = numfmt_format_currency($fmt, $amount, $currency);

        return numfmt_parse_currency($fmt, $localisedcost, $currency);
    }

    /**
     * Returns human-readable amount with correct number of fractional digits and currency indicator, can also apply surcharge
     *
     * @param float $amount amount in the currency units
     * @param string $currency The currency
     * @param float $surcharge surcharge in percents
     * @return string
     */
    public static function get_cost_as_string(float $amount, string $currency, float $surcharge = 0): string {
        $amount = $amount * (100 + $surcharge) / 100;

        $locale = get_string('localecldr', 'langconfig');
        $fmt = \NumberFormatter::create($locale, \NumberFormatter::CURRENCY);
        $localisedcost = numfmt_format_currency($fmt, $amount, $currency);

        return $localisedcost;
    }

    /**
     * Returns the percentage of surcharge that is applied when using a gateway
     *
     * @param string $gateway Name of the gateway
     * @return float
     */
    public static function get_gateway_surcharge(string $gateway): float {
        return (float)get_config('pg_' . $gateway, 'surcharge');
    }

    /**
     * Returns the attributes to place on a pay button.
     *
     * @param string $component Name of the component that the componentid belongs to
     * @param string $paymentarea
     * @param int $componentid An internal identifier that is used by the component
     * @param string $description Description of the payment
     * @return array
     */
    public static function gateways_modal_link_params(string $component, string $paymentarea, int $componentid, string $description): array {
        [
            'amount' => $amount,
            'currency' => $currency
        ] = self::get_cost($component, $paymentarea, $componentid);

        return [
            'id' => 'gateways-modal-trigger',
            'role' => 'button',
            'data-component' => $component,
            'data-paymentarea' => $paymentarea,
            'data-componentid' => $componentid,
            'data-cost' => self::get_cost_as_string($amount, $currency),
            'data-description' => $description,
        ];
    }

    /**
     * Asks the cost from the related component.
     *
     * @param string $component Name of the component that the componentid belongs to
     * @param string $paymentarea
     * @param int $componentid An internal identifier that is used by the component
     * @return array['amount' => float, 'currency' => string, 'accountid' => int]
     * @throws \moodle_exception
     */
    public static function get_cost(string $component, string $paymentarea, int $componentid): array {
        $cost = component_class_callback("$component\\payment\\provider", 'get_cost', [$paymentarea, $componentid]);

        if ($cost === null || !is_array($cost) || !array_key_exists('amount', $cost)
                || !array_key_exists('currency', $cost) || !array_key_exists('accountid', $cost) ) {
            throw new \moodle_exception('callbacknotimplemented', 'core_payment', '', $component);
        }

        return $cost;
    }

    /**
     * Returns the gateway configuration for given component and gateway
     *
     * @param string $component
     * @param string $paymentarea
     * @param int $componentid
     * @param string $gatewayname
     * @return array
     * @throws \moodle_exception
     */
    public static function get_gateway_configuration(string $component, string $paymentarea, int $componentid,
            string $gatewayname): array {
        $x = self::get_cost($component, $paymentarea, $componentid);
        $gateway = null;
        $account = new account($x['accountid']);
        if ($account && $account->get('enabled')) {
            $gateway = $account->get_gateways()[$gatewayname] ?? null;
        }
        if (!$gateway) {
            throw new \moodle_exception('gatewaynotfound', 'payment');
        }
        return $gateway->get_configuration();
    }

    /**
     * Delivers what the user paid for.
     *
     * @uses \core_payment\local\callback\provider::deliver_order()
     *
     * @param string $component Name of the component that the componentid belongs to
     * @param string $paymentarea
     * @param int $componentid An internal identifier that is used by the component
     * @param int $paymentid payment id as inserted into the 'payments' table, if needed for reference
     * @return bool Whether successful or not
     */
    public static function deliver_order(string $component, string $paymentarea, int $componentid, int $paymentid): bool {
        $result = component_class_callback("$component\\payment\\provider", 'deliver_order',
                [$paymentarea, $componentid, $paymentid]);

        if ($result === null) {
            throw new \moodle_exception('callbacknotimplemented', 'core_payment', '', $component);
        }

        return $result;
    }

    /**
     * Stores essential information about the payment and returns the "id" field of the payment record in DB.
     * Each payment gateway may then store the additional information their way.
     *
     * @param int $accountid Account id
     * @param string $component Name of the component that the componentid belongs to
     * @param string $paymentarea
     * @param int $componentid An internal identifier that is used by the component
     * @param int $userid Id of the user who is paying
     * @param float $amount Amount of payment
     * @param string $currency Currency of payment
     * @param string $gateway The gateway that is used for the payment
     * @return int
     */
    public static function save_payment(int $accountid, string $component, string $paymentarea, int $componentid, int $userid,
            float $amount, string $currency, string $gateway): int {
        global $DB;

        $record = new \stdClass();
        $record->component = $component;
        $record->paymentarea = $paymentarea;
        $record->componentid = $componentid;
        $record->userid = $userid;
        $record->amount = $amount;
        $record->currency = $currency;
        $record->gateway = $gateway;
        $record->accountid = $accountid;
        $record->timecreated = $record->timemodified = time();

        $id = $DB->insert_record('payments', $record);

        return $id;
    }

    /**
     * This functions adds the settings that are common for all payment gateways.
     *
     * @param \admin_settingpage $settings The settings object
     * @param string $gateway The gateway name prefic with pg_
     */
    public static function add_common_gateway_settings(\admin_settingpage $settings, string $gateway): void {
        $settings->add(new \admin_setting_configtext($gateway . '/surcharge', get_string('surcharge', 'core_payment'),
                get_string('surcharge_desc', 'core_payment'), 0, PARAM_INT));

    }

    /**
     * Save a new or edited payment account (used in management interface)
     *
     * @param \stdClass $data
     * @return account
     */
    public static function save_payment_account(\stdClass $data): account {

        if (empty($data->id)) {
            $account = new account(0, $data);
            $account->save();
            account_created::create_from_account($account)->trigger();
        } else {
            $account = new account($data->id);
            $account->from_record($data);
            $account->save();
            account_updated::create_from_account($account)->trigger();
        }

        return $account;
    }

    /**
     * Delete a payment account (used in management interface)
     *
     * @param account $account
     */
    public static function delete_payment_account(account $account): void {
        global $DB;
        if ($DB->record_exists('payments', ['accountid' => $account->get('id')])) {
            $account->set('archived', 1);
            $account->save();
            account_updated::create_from_account($account, ['archived' => 1])->trigger();
            return;
        }

        foreach ($account->get_gateways(false) as $gateway) {
            if ($gateway->get('id')) {
                $gateway->delete();
            }
        }
        $event = account_deleted::create_from_account($account);
        $account->delete();
        $event->trigger();
    }

    /**
     * Restore archived payment account (used in management interface)
     *
     * @param account $account
     */
    public static function restore_payment_account(account $account): void {
        $account->set('archived', 0);
        $account->save();
        account_updated::create_from_account($account, ['restored' => 1])->trigger();
    }

    /**
     * Save a payment gateway linked to an existing account (used in management interface)
     *
     * @param \stdClass $data
     * @return account_gateway
     */
    public static function save_payment_gateway(\stdClass $data): account_gateway {
        if (empty($data->id)) {
            $records = account_gateway::get_records(['accountid' => $data->accountid, 'gateway' => $data->gateway]);
            if ($records) {
                $gateway = reset($records);
            } else {
                $gateway = new account_gateway(0, $data);
            }
        } else {
            $gateway = new account_gateway($data->id);
        }
        unset($data->accountid, $data->gateway, $data->id);
        $gateway->from_record($data);

        $account = $gateway->get_account();
        $gateway->save();
        account_updated::create_from_account($account)->trigger();
        return $gateway;
    }

    /**
     * Returns the list of payment accounts in the given context (used in management interface)
     *
     * @param \context $context
     * @return account[]
     */
    public static function get_payment_accounts_to_manage(\context $context, bool $showarchived = false): array {
        $records = account::get_records(['contextid' => $context->id] + ($showarchived ? [] : ['archived' => 0]));
        \core_collator::asort_objects_by_method($records, 'get_formatted_name');
        return $records;
    }

    /**
     * Get list of accounts available in the given context
     *
     * @param \context $context
     * @return array
     */
    public static function get_payment_accounts_menu(\context $context): array {
        global $DB;
        [$sql, $params] = $DB->get_in_or_equal($context->get_parent_context_ids(true));
        $accounts = array_filter(account::get_records_select('contextid '.$sql, $params), function($account) {
            return $account->is_available() && !$account->get('archived');
        });
        return array_map(function($account) {
            return $account->get_formatted_name();
        }, $accounts);
    }
}
