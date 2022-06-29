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
 * Manage one payment gateway
 *
 * @package    core_payment
 * @copyright  2020 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../config.php');
require_once($CFG->libdir . '/adminlib.php');

$id = optional_param('id', 0, PARAM_INT);
$accountid = optional_param('accountid', 0, PARAM_INT);
$gatewayname = optional_param('gateway', null, PARAM_COMPONENT);

$pageurl = new moodle_url('/payment/manage_gateway.php');
admin_externalpage_setup('paymentaccounts', '', [], $pageurl);

$enabledplugins = \core\plugininfo\paygw::get_enabled_plugins();

if ($id) {
    $gateway = new \core_payment\account_gateway($id);
    $account = new \core_payment\account($gateway->get('accountid'));
} else if ($accountid) {
    $account = new \core_payment\account($accountid);
    $gateway = $account->get_gateways()[$gatewayname] ?? null;
}

if (empty($account) || empty($gateway)) {
    throw new moodle_exception('gatewaynotfound', 'payment');
}
require_capability('moodle/payment:manageaccounts', $account->get_context());

$PAGE->set_secondary_active_tab('siteadminnode');
$PAGE->set_primary_active_tab('siteadminnode');
$PAGE->navbar->add(get_string('createaccount', 'payment'), $PAGE->url);

$PAGE->set_heading($id ? format_string($account->get('name')) : get_string('createaccount', 'payment'));

$form = new \core_payment\form\account_gateway($pageurl->out(false), ['persistent' => $gateway]);

if ($form->is_cancelled()) {
    redirect(new moodle_url('/payment/accounts.php'));
} else if ($data = $form->get_data()) {
    \core_payment\helper::save_payment_gateway($data);
    redirect(new moodle_url('/payment/accounts.php'));
}

echo $OUTPUT->header();
$form->display();
echo $OUTPUT->footer();
