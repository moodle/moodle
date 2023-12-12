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
 * View the progress of MoodleNet shares.
 *
 * @package   core
 * @copyright 2023 David Woloszyn <david.woloszyn@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core\moodlenet\utilities;

require_once('../config.php');

require_login();
if (isguestuser()) {
    throw new \moodle_exception('noguest');
}

// Capability was not found.
if (utilities::does_user_have_capability_in_any_course($USER->id) === 'no') {
    throw new \moodle_exception('nocapabilitytousethisservice');
}

$pageurl = $CFG->wwwroot . '/moodlenet/shareprogress.php';
$PAGE->set_url($pageurl);
$PAGE->set_context(context_system::instance());
$PAGE->set_title(get_string('moodlenet:shareprogress'));
$PAGE->set_heading(get_string('moodlenet:shareprogress'));
$PAGE->set_pagelayout('standard');

echo $OUTPUT->header();

// Intro paragraph.
echo html_writer::div(get_string('moodlenet:shareprogressinfo'), 'mb-4');

// Build table.
$table = new core\moodlenet\share_progress_table('moodlenet-share-progress', $pageurl, $USER->id);
$perpage = $table->get_default_per_page();
$table->out($perpage, true);

echo $OUTPUT->footer();
