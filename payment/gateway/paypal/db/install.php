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
 * pg_paypal installer script.
 *
 * @package    pg_paypal
 * @copyright  2020 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

function xmldb_pg_paypal_install() {
    // Enable the Paypal payment gateway on installation. It still needs to be configured and enabled for accounts.
    $order = (!empty($CFG->pg_plugins_sortorder)) ? explode(',', $CFG->pg_plugins_sortorder) : [];
    set_config('pg_plugins_sortorder', join(',', array_merge($order, ['paypal'])));
}
