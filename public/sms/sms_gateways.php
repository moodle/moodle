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
 * List sms gateway instances.
 *
 * @package    core_sms
 * @copyright  2024 Safat Shahin <safat.shahin@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core\output\notification;

require_once('../config.php');
require_once($CFG->dirroot . '/lib/adminlib.php');

require_login();
$context = context_system::instance();
require_capability('moodle/site:config', $context);

$id = optional_param('id', null, PARAM_INT);
$action = optional_param('action', '', PARAM_TEXT);
$confirm = optional_param('confirm', 0, PARAM_BOOL);

// Set up the page.
$title = get_string('sms_gateways', 'sms');
$returnurl = new moodle_url('/sms/sms_gateways.php');
admin_externalpage_setup('smsgateway');
$PAGE->set_primary_active_tab('siteadminnode');
$PAGE->navbar->add($title, $returnurl);
$PAGE->set_context($context);
$PAGE->set_url($returnurl);
$PAGE->set_title($title);
$PAGE->set_heading($title);

if (!empty($id) && !empty($action)) {
    $manager = \core\di::get(\core_sms\manager::class);
    $gatewayrecord = $manager->get_gateway_records(['id' => $id]);
    $gatewayrecord = reset($gatewayrecord);
    $pluginname = explode('\\', $gatewayrecord->gateway);
    $pluginname = $pluginname[0];
    $gateway = $manager->get_gateway_instances(['id' => $id]);
    $gateway = reset($gateway);
    $a = new stdClass();
    $a->gateway = $gateway->name;
}

if ($action === 'delete') {
    $PAGE->url->param('action', 'delete');
    if ($confirm && confirm_sesskey()) {
        if ($manager->delete_gateway($gateway)) {
            $message = get_string('sms_gateway_deleted', 'sms', $a);
            $messagestyle = notification::NOTIFY_SUCCESS;
        } else {
            $message = get_string('sms_gateway_delete_failed', 'sms', $a);
            $messagestyle = notification::NOTIFY_ERROR;
        }
        redirect($returnurl, $message, null, $messagestyle);
    }
    $strheading = get_string('delete_sms_gateway', 'sms');
    $PAGE->navbar->add($strheading);
    $PAGE->set_title($strheading);
    $PAGE->set_heading($strheading);

    echo $OUTPUT->header();
    $yesurl = new moodle_url($returnurl, ['id' => $id, 'action' => 'delete', 'confirm' => 1]);
    $deletedisplay = [
        'confirmtitle' => get_string('deletecheck', '', $a->gateway),
        'continuestr' => get_string('delete'),
    ];
    $message = get_string('delete_sms_gateway_confirmation', 'sms', $a);
    echo $OUTPUT->confirm($message, $yesurl, $returnurl, $deletedisplay);
    echo $OUTPUT->footer();
    die;
}

$table = new \core_sms\table\sms_gateway_table();
$templatecontext = new stdClass();
$templatecontext->tablehtml = $table->get_content();
$templatecontext->createurl = new moodle_url('/sms/configure.php');

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('core_sms/sms_gateways', $templatecontext);
echo $OUTPUT->footer();
