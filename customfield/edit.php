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
 * Edit configuration of a custom field
 *
 * @package   core_customfield
 * @copyright 2018 David Matamoros <davidmc@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../config.php');
require_once($CFG->libdir . '/adminlib.php');

$id         = optional_param('id', 0, PARAM_INT);
$categoryid = optional_param('categoryid', 0, PARAM_INT);
$type       = optional_param('type', null, PARAM_COMPONENT);

if ($id) {
    $field = \core_customfield\field_controller::create($id);
} else if ($categoryid && $type) {
    $category = \core_customfield\category_controller::create($categoryid);
    $field = \core_customfield\field_controller::create(0, (object)['type' => $type], $category);
} else {
    print_error('fieldnotfound', 'core_customfield');
}

$handler = $field->get_handler();
require_login();
if (!$handler->can_configure()) {
    print_error('nopermissionconfigure', 'core_customfield');
}
$title = $handler->setup_edit_page($field);

$mform = $handler->get_field_config_form($field);
if ($mform->is_cancelled()) {
    redirect($handler->get_configuration_url());
} else if ($data = $mform->get_data()) {
    $handler->save_field_configuration($field, $data);
    redirect($handler->get_configuration_url());
}

echo $OUTPUT->header();
echo $OUTPUT->heading($title);

$mform->display();

echo $OUTPUT->footer();
