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
 * Management of payment accounts
 *
 * @package    core_payment
 * @copyright  2020 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../config.php');
require_once($CFG->libdir . '/adminlib.php');

admin_externalpage_setup('paymentaccounts');
$PAGE->set_heading(get_string('paymentaccounts', 'payment'));

$enabledplugins = \core\plugininfo\pg::get_enabled_plugins();

echo $OUTPUT->header();

$accounts = \core_payment\helper::get_payment_accounts_to_manage(context_system::instance());
$table = new html_table();
$table->head = [get_string('accountname', 'payment'), get_string('type_pg', 'plugin'), ''];
$table->colclasses = ['', '', 'mdl-right'];
$table->data = [];
foreach ($accounts as $account) {
    $gateways = [];
    $canmanage = has_capability('moodle/payment:manageaccounts', $account->get_context());
    foreach ($account->get_gateways() as $gateway) {
        $status = $gateway->get('enabled') ? $OUTPUT->pix_icon('i/valid', get_string('gatewayenabled', 'payment')) :
            $OUTPUT->pix_icon('i/invalid', get_string('gatewaydisabled', 'payment'));
        $gateways[] = $status .
            ($canmanage ? html_writer::link($gateway->get_edit_url(), $gateway->get_display_name()) : $gateway->get_display_name());
    }
    $name = $account->get_formatted_name();
    if (!$account->is_available()) {
        $name .= ' ' . html_writer::span(get_string('accountnotavailable', 'payment'), 'badge badge-warning');
    }

    $menu = new action_menu();
    $menu->set_alignment(action_menu::TL, action_menu::BL);
    $menu->set_menu_trigger(get_string('edit'));
    if ($canmanage) {
        $menu->add(new action_menu_link_secondary($account->get_edit_url(), null, get_string('edit')));
        $deleteurl = $account->get_edit_url(['delete' => 1, 'sesskey' => sesskey()]);
        $deleteaction = new confirm_action(get_string('deleteconfirm', 'tool_recyclebin'));
        $menu->add(new action_menu_link_secondary($deleteurl, null, get_string('delete')));
    }

    $table->data[] = [$name, join(', ', $gateways), $OUTPUT->render($menu)];
}

echo html_writer::table($table);

echo $OUTPUT->single_button(new moodle_url('/payment/manage_account.php'), get_string('createaccount', 'payment'), 'get');

echo $OUTPUT->footer();
