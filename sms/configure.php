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
 * Configure sms gateway for a given instance.
 *
 * @package    core_sms
 * @copyright  2024 Safat Shahin <safat.shahin@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../config.php');

require_login();
$context = context_system::instance();
require_capability('moodle/site:config', $context);

$id = optional_param('id', null, PARAM_INT);
$gateway = optional_param('smsgateway', null, PARAM_PLUGIN);
$returnurl = optional_param('returnurl', null, PARAM_LOCALURL);

$title = get_string('createnewgateway', 'sms');
$data = [];
$urlparams = [];
if ($id) {
    $urlparams['id'] = $id;
}
if ($gateway) {
    $urlparams['gateway'] = $gateway;
}

if (!empty($gateway)) {
    $configs = new stdClass();
    $configs->smsgateway = $gateway;
    $data = [
        'gatewayconfigs' => $configs,
    ];
}

if (!empty($id)) {
    $manager = \core\di::get(\core_sms\manager::class);
    $gatewayrecord = $manager->get_gateway_records(['id' => $id]);
    $gatewayrecord = reset($gatewayrecord);
    $plugin = explode('\\', $gatewayrecord->gateway);
    $plugin = $plugin[0];
    $configs = json_decode($gatewayrecord->config, true, 512, JSON_THROW_ON_ERROR);
    $configs = (object) $configs;
    $configs->smsgateway = $plugin;
    $configs->id = $gatewayrecord->id;
    $configs->name = $gatewayrecord->name;
    $data = [
        'gatewayconfigs' => $configs,
    ];

    $a = ['gateway' => $gatewayrecord->name];
    $title = get_string('edit_sms_gateway', 'sms', $a);
}

$PAGE->set_context($context);
$PAGE->set_url('/sms/configure.php', $urlparams);
$PAGE->set_title($title);
$PAGE->set_heading($title);

if (empty($returnurl)) {
    $returnurl = new moodle_url('/sms/sms_gateways.php');
} else {
    $returnurl = new moodle_url($returnurl);
}
$data['returnurl'] = $returnurl;

$mform = new \core_sms\form\sms_gateway_form(customdata: $data);

if ($mform->is_cancelled()) {
    $data = $mform->get_data();
    if (isset($data->returnurl)) {
        redirect($data->returnurl);
    } else {
        redirect($returnurl);
    }
}

if ($data = $mform->get_data()) {
    $manager = \core\di::get(\core_sms\manager::class);
    $smsgateway = $data->smsgateway;
    $gatewayname = $data->name;
    // The $data will go into the database config column. If any data is not needed, unset it here.
    unset($data->smsgateway, $data->name, $data->id, $data->saveandreturn, $data->returnurl);
    if (!empty($id)) {
        $gatewayinstance = $manager->get_gateway_instances(['id' => $id]);
        $gatewayinstance = reset($gatewayinstance);
        $gatewayinstance->name = $gatewayname;

        $manager->update_gateway_instance($gatewayinstance, $data);
    } else {
        $classname = $smsgateway . '\\' . 'gateway';
        $manager->create_gateway_instance(
            classname: $classname,
            name: $gatewayname,
            enabled: true,
            config: $data,
        );
    }
    redirect($returnurl);
}

echo $OUTPUT->header();
$mform->display();
echo $OUTPUT->footer();
