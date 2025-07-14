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
 * Configure provider instances.
 *
 * @package    core_ai
 * @copyright  2024 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../config.php');

require_login();
$context = context_system::instance();
require_capability('moodle/site:config', $context);

$id = optional_param('id', 0, PARAM_INT);  // If we have an id we have existing settings.
$provider = optional_param('aiprovider', null, PARAM_PLUGIN);
$returnurl = optional_param('returnurl', null, PARAM_LOCALURL);
$title = get_string('createnewprovider', 'core_ai');
$data = [];

// Handle return URL.
if (empty($returnurl)) {
    $returnurl = new moodle_url(
        url: '/admin/settings.php',
        params: ['section' => 'aiprovider']
    );
} else {
    $returnurl = new moodle_url($returnurl);
}
$data['returnurl'] = $returnurl;

if ($provider) {
    $configs = ['aiprovider' => $provider];
    $data['providerconfigs'] = $configs;
}

if ($id !== 0) { // If we have an id we are updating an existing provider instance.
    $manager = \core\di::get(\core_ai\manager::class);
    $providerrecord = $manager->get_provider_record(['id' => $id], MUST_EXIST);
    $plugin = explode('\\', $providerrecord->provider);
    $plugin = $plugin[0];

    $configs = json_decode($providerrecord->config, true, 512, JSON_THROW_ON_ERROR);
    $configs['aiprovider'] = $plugin;
    $configs['id'] = $providerrecord->id;
    $configs['name'] = $providerrecord->name;

    $data['providerconfigs'] = $configs;
    $title = get_string('configureprovider', 'core_ai');
}

// Initial Page setup.
$PAGE->set_context($context);
$PAGE->set_url('/ai/configure.php', ['id' => $id]);
$PAGE->set_pagelayout('admin');
$PAGE->set_title($title);
$PAGE->set_heading($title);

// Provider instance form processing.
$mform = new \core_ai\form\ai_provider_form(customdata: $data);
if ($mform->is_cancelled()) {
    $data = $mform->get_data();
    if (isset($data->returnurl)) {
        redirect($data->returnurl);
    } else {
        redirect($returnurl);
    }
}

if ($data = $mform->get_data()) {
    $data = (array)$data;
    $manager = \core\di::get(\core_ai\manager::class);
    $aiprovider = $data['aiprovider'];
    $providername = $data['name'];
    unset($data->aiprovider, $data->name, $data->id, $data->returnurl, $data->updateandreturn);
    if ($id !== 0) {
        $providerinstance = $manager->get_provider_instances(['id' => $id]);
        $providerinstance = reset($providerinstance);
        $providerinstance->name = $providername;

        $manager->update_provider_instance(
            provider:$providerinstance,
            config: $data
        );
        \core\notification::add(
            get_string('providerinstanceupdated', 'core_ai', $providername),
            \core\notification::SUCCESS
        );
    } else {
        $classname = $aiprovider . '\\' . 'provider';
        $manager->create_provider_instance(
            classname: $classname,
            name: $providername,
            config: $data,
        );
        \core\notification::add(
            get_string('providerinstancecreated', 'core_ai', $providername),
            \core\notification::SUCCESS
        );
    }
    redirect($returnurl);
}

// Page output.
echo $OUTPUT->header();

// Add the provider instance form.
$mform->display();

// Add the per provider action settings only if we have an existing provider.
if ($id !== 0) {
    echo $OUTPUT->render_from_template('core_ai/admin_action_settings', []);
    $provider = $mform->get_customdata()['aiprovider'];
    $tableid = $provider . '-' . $id; // This is the table id for the action settings table.
    $actiontable = new \core_ai\table\aiprovider_action_management_table($tableid);
    $actiontable->out();
}
echo $OUTPUT->footer();
