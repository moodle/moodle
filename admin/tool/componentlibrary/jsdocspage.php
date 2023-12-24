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
 * Moodle Component Library.
 *
 * Redirect helper for js documentation.
 *
 * @package    tool_componentlibrary
 * @copyright  2021 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->dirroot . '/lib/filelib.php');

require_login();
require_capability('moodle/site:configview', context_system::instance());

$jsdocdir = "{$CFG->dirroot}/jsdoc";
if (file_exists($jsdocdir) && is_dir($jsdocdir)) {
    $relativepath = get_file_argument();
    redirect(new moodle_url("/jsdoc/{$relativepath}"));
}

$PAGE->set_pagelayout('base');
$PAGE->set_url(new moodle_url('/admin/tool/componentlibrary/jsdocspage.php'));
$PAGE->set_context(context_system::instance());
$title = get_string('pluginname', 'tool_componentlibrary');
$PAGE->set_heading($title);
$PAGE->set_title($title);

echo $OUTPUT->header();
echo $OUTPUT->box(get_string('runjsdoc', 'tool_componentlibrary'));
echo $OUTPUT->footer();
