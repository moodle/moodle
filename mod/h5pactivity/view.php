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
 * Prints an instance of mod_h5pactivity.
 *
 * @package     mod_h5pactivity
 * @copyright   2020 Ferran Recio <ferran@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use mod_h5pactivity\local\manager;
use core_h5p\factory;
use core_h5p\player;
use core_h5p\helper;

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/lib.php');

$id = required_param('id', PARAM_INT);

list ($course, $cm) = get_course_and_cm_from_cmid($id, 'h5pactivity');

require_login($course, true, $cm);

$manager = manager::create_from_coursemodule($cm);

$moduleinstance = $manager->get_instance();

$context = $manager->get_context();

// Trigger module viewed event and completion.
$manager->set_module_viewed($course);

// Convert display options to a valid object.
$factory = new factory();
$core = $factory->get_core();
$config = core_h5p\helper::decode_display_options($core, $moduleinstance->displayoptions);

// Instantiate player.
$fs = get_file_storage();
$files = $fs->get_area_files($context->id, 'mod_h5pactivity', 'package', 0, 'id', false);
$file = reset($files);
$fileurl = moodle_url::make_pluginfile_url($file->get_contextid(), $file->get_component(),
                    $file->get_filearea(), $file->get_itemid(), $file->get_filepath(),
                    $file->get_filename(), false);

$PAGE->set_url('/mod/h5pactivity/view.php', ['id' => $cm->id]);

$shortname = format_string($course->shortname, true, ['context' => $context]);
$pagetitle = strip_tags($shortname.': '.format_string($moduleinstance->name));
$PAGE->set_title(format_string($pagetitle));

$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);

echo $OUTPUT->header();
echo $OUTPUT->heading(format_string($moduleinstance->name));

$instance = $manager->get_instance();
if (!empty($instance->intro)) {
    echo $OUTPUT->box(format_module_intro('h5pactivity', $instance, $cm->id), 'generalbox', 'intro');
}

// Attempts review.
if ($manager->can_view_all_attempts()) {
    $reviewurl = new moodle_url('report.php', ['a' => $cm->instance]);
    $reviewmessage = get_string('review_all_attempts', 'mod_h5pactivity', $manager->count_attempts());
} else if ($manager->can_view_own_attempts() && $manager->count_attempts($USER->id)) {
    $reviewurl = new moodle_url('report.php', ['a' => $cm->instance, 'userid' => $USER->id]);
    $reviewmessage = get_string('review_my_attempts', 'mod_h5pactivity');
}
if (isset($reviewurl)) {
    $widget = new mod_h5pactivity\output\reportlink($reviewurl, $reviewmessage);
    echo $OUTPUT->render($widget);
}

if ($manager->is_tracking_enabled()) {
    $trackcomponent = 'mod_h5pactivity';
} else {
    $trackcomponent = '';
    $message = get_string('previewmode', 'mod_h5pactivity');
    echo $OUTPUT->notification($message, \core\output\notification::NOTIFY_WARNING);
}

echo player::display($fileurl, $config, true, $trackcomponent);

echo $OUTPUT->footer();
