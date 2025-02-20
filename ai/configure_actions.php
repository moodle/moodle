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
 * Configure provider instance action settings.
 *
 * @package    core_ai
 * @copyright  2024 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../config.php');

require_login();
$context = context_system::instance();
require_capability('moodle/site:config', $context);

$provider = required_param('provider', PARAM_PLUGIN);
$action = required_param('action', PARAM_TEXT);
$id = required_param('providerid', PARAM_INT);
$returnurl = optional_param('returnurl', null, PARAM_LOCALURL);
$customdata = ['providerid' => $id];

// Handle return URL.
if (empty($returnurl)) {
    $returnurl = new moodle_url(
        url: '/ai/configure.php',
        params: ['id' => $id]
    );
} else {
    $returnurl = new moodle_url($returnurl);
}
$customdata['returnurl'] = $returnurl;

$manager = \core\di::get(\core_ai\manager::class);
$providerrecord = $manager->get_provider_record(['id' => $id], MUST_EXIST);

$actionconfig = json_decode($providerrecord->actionconfig, true, 512, JSON_THROW_ON_ERROR);
$actionconfig = $actionconfig[$action];

$customdata['actionconfig'] = $actionconfig;
$customdata['providername'] = $provider;

$urlparams = [
    'provider' => $provider,
    'action' => $action,
    'id' => $id,
];

// Page setup.
$title = get_string('actionsettingprovider', 'core_ai', $action::get_name());
$PAGE->set_context($context);
$PAGE->set_url('/ai/configure.php_actions', $urlparams);
$PAGE->set_pagelayout('admin');
$PAGE->set_title($title);
$PAGE->set_heading($title);

$providerclass = "\\$provider\\provider";
$mform = $providerclass::get_action_settings($action, $customdata);

if ($mform->is_cancelled()) {
    $data = $mform->get_data();
    if (isset($data->returnurl)) {
        redirect($data->returnurl);
    } else {
        redirect($returnurl);
    }
}

if ($data = $mform->get_data()) {
    $manager = \core\di::get(\core_ai\manager::class);
    $aiprovider = $data->provider;
    unset($data->provider, $data->id, $data->action, $data->returnurl, $data->submitbutton);
    $providerinstance = $manager->get_provider_instances(['id' => $id]);
    $providerinstance = reset($providerinstance);
    $actionconfig = $providerinstance->actionconfig;
    $actionconfig[$action]['settings'] = (array)$data;
    $actionconfig[$action]['enabled'] = $providerinstance->actionconfig[$action]['enabled'];

    $manager->update_provider_instance(
        provider: $providerinstance,
        actionconfig: $actionconfig,
    );

    \core\notification::add(
        get_string('providerinstanceactionupdated', 'core_ai', $action::get_name()),
        \core\notification::SUCCESS
    );

    redirect($returnurl);
}

echo $OUTPUT->header();
$mform->display();
echo $OUTPUT->footer();
