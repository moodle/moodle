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

namespace core_payment\event;

use core\event\base;
use core_payment\account;

/**
 * Class account_deleted
 *
 * @package     core_payment
 * @copyright   2020 Marina Glancy
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


/**
 * Class account_deleted
 *
 * @package     core_payment
 * @copyright   2020 Marina Glancy
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class account_deleted extends base {

    /**
     * Initialise event parameters.
     */
    protected function init() {
        $this->data['objecttable'] = 'payment_accounts';
        $this->data['crud'] = 'd';
        $this->data['edulevel'] = self::LEVEL_OTHER;
    }

    /**
     * Create an instance of the event and add a record snapshot
     *
     * @param account $account
     * @return base
     * @throws \coding_exception
     */
    public static function create_from_account(account $account) {
        $eventparams = [
            'objectid' => $account->get('id'),
            'context'  => $account->get_context(),
            'other'    => ['name' => $account->get('name')]
        ];
        $event = self::create($eventparams);
        $event->add_record_snapshot($event->objecttable, $account->to_record());
        return $event;
    }

    /**
     * Returns localised event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('eventaccountdeleted', 'payment');
    }

    /**
     * Returns non-localised event description with id's for admin use only.
     *
     * @return string
     */
    public function get_description() {
        $name = s($this->other['name']);
        return "The user with id '$this->userid' deleted payment account with id '$this->objectid' and the name '{$name}'.";
    }

    /**
     * Returns relevant URL.
     *
     * @return \moodle_url
     */
    public function get_url() {
        return new \moodle_url('/payment/accounts.php');
    }
}
