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
 * This page lets users manage purposes.
 *
 * @package    tool_dataprivacy
 * @copyright  2018 David Monllao
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');

$id = optional_param('id', 0, PARAM_INT);

$url = new \moodle_url('/admin/tool/dataprivacy/editpurpose.php', array('id' => $id));
if ($id) {
    $title = get_string('editpurpose', 'tool_dataprivacy');
} else {
    $title = get_string('addpurpose', 'tool_dataprivacy');
}

\tool_dataprivacy\page_helper::setup($url, $title, 'dataregistry');

$purpose = new \tool_dataprivacy\purpose($id);
$form = new \tool_dataprivacy\form\purpose($PAGE->url->out(false),
    array('persistent' => $purpose, 'showbuttons' => true));

$returnurl = new \moodle_url('/admin/tool/dataprivacy/purposes.php');
if ($form->is_cancelled()) {
    redirect($returnurl);
} else if ($data = $form->get_data()) {
    if (empty($data->id)) {
        \tool_dataprivacy\api::create_purpose($data);
        $messagesuccess = get_string('purposecreated', 'tool_dataprivacy');
    } else {
        \tool_dataprivacy\api::update_purpose($data);
        $messagesuccess = get_string('purposeupdated', 'tool_dataprivacy');
    }
    redirect($returnurl, $messagesuccess, 0, \core\output\notification::NOTIFY_SUCCESS);
}

$output = $PAGE->get_renderer('tool_dataprivacy');
echo $output->header();
$form->display();
echo $output->footer();
