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
 * Behat data generator for core_payment.
 *
 * @package    core_payment
 * @category   test
 * @copyright  2020 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


/**
 * Behat data generator for core_payment.
 *
 * @package    core_payment
 * @category   test
 * @copyright  2020 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_core_payment_generator extends behat_generator_base {

    protected function get_creatable_entities(): array {
        return [
            'payment accounts' => [
                'singular' => 'payment account',
                'datagenerator' => 'payment_account',
                'required' => ['name'],
            ],
            'payments' => [
                'singular' => 'payment',
                'datagenerator' => 'payment',
                'required' => ['account', 'amount', 'user'],
                'switchids' => ['account' => 'accountid', 'user' => 'userid'],
            ],
        ];
    }

    /**
     * Look up the id of a account from its name.
     *
     * @param string $accountname
     * @return int corresponding id.
     */
    protected function get_account_id(string $accountname): int {
        global $DB;

        if (!$id = $DB->get_field('payment_accounts', 'id', ['name' => $accountname])) {
            throw new Exception('There is no account with name "' . $accountname . '".');
        }
        return $id;
    }
}
