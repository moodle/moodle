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
 * View H5P Content
 *
 * @package    mod_hvp
 * @copyright  2016 Joubel AS <contact@joubel.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once("../../config.php");
require_once("locallib.php");

global $PAGE, $DB, $CFG, $OUTPUT;

$id = required_param('id', PARAM_INT);

// Verify course context.
$cm = get_coursemodule_from_id('hvp', $id);
if (!$cm) {
    print_error('invalidcoursemodule');
}
$course = $DB->get_record('course', array('id' => $cm->course));
if (!$course) {
    print_error('coursemisconf');
}
require_course_login($course, true, $cm);
$context = context_module::instance($cm->id);
require_capability('mod/hvp:view', $context);

// Set up view assets.
$view    = new \mod_hvp\view_assets($cm, $course);
$content = $view->getcontent();
$view->validatecontent();

// Configure page.
$PAGE->set_url(new \moodle_url('/mod/hvp/view.php', array('id' => $id)));
$PAGE->set_title(format_string($content['title']));
$PAGE->set_heading($course->fullname);

// Add H5P assets to page.
$view->addassetstopage();
$view->logviewed();

// Print page HTML.
echo $OUTPUT->header();
echo $OUTPUT->heading(format_string($content['title']));
echo '<div class="clearer"></div>';

// Output introduction.
if (trim(strip_tags($content['intro']))) {
    echo $OUTPUT->box_start('mod_introbox', 'hvpintro');
    echo format_module_intro('hvp', (object) array(
        'intro'       => $content['intro'],
        'introformat' => $content['introformat'],
    ), $cm->id);
    echo $OUTPUT->box_end();
}

// Print any messages.
\mod_hvp\framework::printMessages('info', \mod_hvp\framework::messages('info'));
\mod_hvp\framework::printMessages('error', \mod_hvp\framework::messages('error'));

$view->outputview();
echo $OUTPUT->footer();
