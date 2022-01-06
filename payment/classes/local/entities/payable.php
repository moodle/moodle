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
 * The payable class.
 *
 * @package    core_payment
 * @copyright  2020 Shamim Rezaie <shamim@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_payment\local\entities;

/**
 * The payable class.
 *
 * @copyright  2020 Shamim Rezaie <shamim@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class payable {
    private $amount;
    private $currency;
    private $accountid;

    public function __construct(float $amount, string $currency, int $accountid) {
        $this->amount = $amount;
        $this->currency = $currency;
        $this->accountid = $accountid;
    }

    /**
     * Get the amount of the payable cost.
     *
     * @return float
     */
    public function get_amount(): float {
        return $this->amount;
    }

    /**
     * Get the currency of the payable cost.
     *
     * @return string
     */
    public function get_currency(): string {
        return $this->currency;
    }

    /**
     * Get the id of the payment account the cost is payable to.
     *
     * @return int
     */
    public function get_account_id(): int {
        return $this->accountid;
    }
}
