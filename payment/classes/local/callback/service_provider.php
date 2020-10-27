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
 * This file contains the \core_payment\local\local\callback\service_provider interface.
 *
 * Plugins should implement this if they use payment subsystem.
 *
 * @package core_payment
 * @copyright 2020 Shamim Rezaie <shamim@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_payment\local\callback;

/**
 * The service_provider interface for plugins to provide callbacks which are needed by the payment subsystem.
 *
 * @copyright  2020 Shamim Rezaie <shamim@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface service_provider {

    /**
     * @param string $paymentarea
     * @param int $itemid An identifier that is known to the plugin
     * @return \core_payment\local\entities\payable
     */
    public static function get_payable(string $paymentarea, int $itemid): \core_payment\local\entities\payable;

    /**
     * @param string $paymentarea
     * @param int $itemid An identifier that is known to the plugin
     * @param int $paymentid payment id as inserted into the 'payments' table, if needed for reference
     * @param int $userid The userid the order is going to deliver to
     *
     * @return bool Whether successful or not
     */
    public static function deliver_order(string $paymentarea, int $itemid, int $paymentid, int $userid): bool;
}
