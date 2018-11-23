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

require_login(null, false);

$url = new moodle_url("/admin/tool/dataprivacy/purposes.php");
$title = get_string('editpurposes', 'tool_dataprivacy');

\tool_dataprivacy\page_helper::setup($url, $title, 'dataregistry');

$output = $PAGE->get_renderer('tool_dataprivacy');
echo $output->header();
echo $output->heading($title);

$purposes = \tool_dataprivacy\api::get_purposes();
$renderable = new \tool_dataprivacy\output\purposes($purposes);

echo $output->render($renderable);
echo $output->footer();
