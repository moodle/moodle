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
 * Display the custom file type settings page.
 *
 * @package tool_filetypes
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/adminlib.php');

admin_externalpage_setup('tool_filetypes');

// Page settings.
$title = get_string('pluginname', 'tool_filetypes');

$context = context_system::instance();
$PAGE->set_url(new \moodle_url('/admin/tool/filetypes/index.php'));
$PAGE->set_context($context);
$PAGE->set_pagelayout('admin');
$PAGE->set_title($title);

$renderer = $PAGE->get_renderer('tool_filetypes');

// Is it restricted because set in config.php?
$restricted = array_key_exists('customfiletypes', $CFG->config_php_settings);

// Display the page.
echo $renderer->header();
echo $renderer->edit_table(get_mimetypes_array(), core_filetypes::get_deleted_types(),
        $restricted);
echo $renderer->footer();
