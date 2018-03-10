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
 * Development data generator.
 *
 * @package    tool_iomadsite
 * @copyright  2018 Howard Miller
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/../../../config.php');

require_once($CFG->libdir . '/adminlib.php');

// Initialise page and check permissions.
admin_externalpage_setup('toolgeneratorcourse');

// params
$action = optional_param('action', '', PARAM_ALPHA);

// Start page.
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('makesite', 'tool_iomadsite'));

// Information message.
$context = context_system::instance();
echo $OUTPUT->box(format_text(get_string('explanation', 'tool_iomadsite'),
        FORMAT_MARKDOWN, array('context' => $context)));

// Check debugging is set to DEVELOPER.
if (!debugging('', DEBUG_DEVELOPER)) {
    echo $OUTPUT->notification(get_string('notdebugging', 'tool_iomadsite'));
    echo $OUTPUT->footer();
    exit;
}

// Do stuffs
if ($action == 'generate') {

    $generate = new tool_iomadsite\generate();
    $generate->companies();

} else {

    // Go ahead button
    $url = new moodle_url('/admin/tool/iomadsite/index.php', ['action' => 'generate']);
    echo $OUTPUT->single_button($url, get_string('doit', 'tool_iomadsite'));
}

echo $OUTPUT->footer();


