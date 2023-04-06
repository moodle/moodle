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
 * Management page for Iomad Learning Paths
 *
 * @package    local_qubitsuser
 * @author     Qubits Dev Team
 * @copyright  2023 <https://www.yardstickedu.com/>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../config.php');
require_once(__DIR__ . '/renderer.php');

// Security
$context = context_system::instance();
require_login();
$qbituserrenderer = $PAGE->get_renderer('local_qubitsuser');
$url = new moodle_url('/local/qubitsuser/index.php');
$PAGE->set_context($context);
$PAGE->set_url($url);
$PAGE->set_pagelayout('base');
$PAGE->set_title("Manage Users");
$PAGE->set_heading("Qubits User Management");

echo $OUTPUT->header();

echo $qbituserrenderer->manage_users();

echo $OUTPUT->footer();