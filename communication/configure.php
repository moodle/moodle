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
 * Configure communication for a given instance.
 *
 * @package    core_communication
 * @copyright  2023 David Woloszyn <david.woloszyn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../config.php');
require_once('lib.php');

require_login();

$contextid = required_param('contextid', PARAM_INT);
$instanceid = required_param('instanceid', PARAM_INT);
$instancetype = required_param('instancetype', PARAM_TEXT);
$component = required_param('component', PARAM_COMPONENT);
$selectedcommunication = optional_param('selectedcommunication', null, PARAM_PLUGIN);

$context = \core\context::instance_by_id($contextid);
$instanceinfo = [
    'contextid' => $context->id,
    'instanceid' => $instanceid,
    'instancetype' => $instancetype,
    'component' => $component,
];

// Requires communication to be enabled.
if (!core_communication\api::is_available()) {
    throw new \moodle_exception('communicationdisabled', 'communication');
}

// Attempt to load the communication instance with the provided params.
$communication = \core_communication\api::load_by_instance(
    context: $context,
    component: $component,
    instancetype: $instancetype,
    instanceid: $instanceid,
    provider: $selectedcommunication,
);

// No communication, no way this form can be used.
if (!$communication) {
    throw new \moodle_exception('nocommunicationinstance', 'communication');
}

// Set variables according to the component callback and use them on the page.
[$instance, $context, $heading, $returnurl] = component_callback(
    $component,
    'get_communication_instance_data',
    [$instanceid]
);

// Set up the page.
$PAGE->set_context($context);
$PAGE->set_url('/communication/configure.php', $instanceinfo);
$PAGE->set_title(get_string('communication', 'communication'));
$PAGE->set_heading($heading);
$PAGE->add_body_class('limitedwidth');

// Append the instance data before passing to form object.
$instanceinfo['instancedata'] = $instance;

// Get our form definitions.
$form = new \core_communication\form\configure_form(
    context: $context,
    instanceid: $instanceinfo['instanceid'],
    instancetype: $instanceinfo['instancetype'],
    component: $instanceinfo['component'],
    selectedcommunication: $selectedcommunication,
    instancedata: $instanceinfo['instancedata'],
);


if ($form->is_cancelled()) {
    redirect($returnurl);
} else if ($data = $form->get_data()) {
    component_callback($component, 'update_communication_instance_data', [$data]);
    redirect($returnurl);
}

// Display the page contents.
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('communication', 'communication'), 2);
$form->display();
echo $OUTPUT->footer();
