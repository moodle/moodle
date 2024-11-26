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
 * View all results for H5P Content
 *
 * @package    mod_hvp
 * @copyright  2016 Joubel AS <contact@joubel.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../config.php');
require_once("locallib.php");

global $DB, $PAGE, $USER, $COURSE;

$id = required_param('id', PARAM_INT);
$userid = optional_param('userid', 0, PARAM_INT);

if (! $cm = get_coursemodule_from_id('hvp', $id)) {
    print_error('invalidcoursemodule');
}
if (! $course = $DB->get_record('course', array('id' => $cm->course))) {
    print_error('coursemisconf');
}
require_course_login($course, false, $cm);

// Check permission.
$context = \context_module::instance($cm->id);

// Load H5P Content.
$hvp = $DB->get_record_sql(
        "SELECT h.id,
                h.name AS title,
                hl.machine_name,
                hl.major_version,
                hl.minor_version
           FROM {hvp} h
           JOIN {hvp_libraries} hl ON hl.id = h.main_library_id
          WHERE h.id = ?",
        array($cm->instance));

if ($hvp === false) {
    print_error('invalidhvp', 'mod_hvp');
}

// Redirect to report if a specific user is chosen.
if ($userid) {
    redirect(new moodle_url('/mod/hvp/review.php',
        array(
            'id'     => $hvp->id,
            'course' => $course->id,
            'user'   => $userid
        ))
    );
}
hvp_require_view_results_permission((int)$USER->id, $context, $cm->id);

// Log content result view.
new \mod_hvp\event(
        'results', 'content',
        $hvp->id, $hvp->title,
        $hvp->machine_name, "{$hvp->major_version}.{$hvp->minor_version}"
);

// Set page properties.
$pageurl = new moodle_url('/mod/hvp/grade.php', array('id' => $hvp->id));
$PAGE->set_url($pageurl);
$title = get_string('gradeheading', 'hvp', $hvp->title);
$PAGE->set_title($title);
$PAGE->set_heading($course->fullname);

// List all results for specific content.
$dataviewid = 'h5p-results';

// Add required assets for data views.
$root = \mod_hvp\view_assets::getsiteroot();
$PAGE->requires->js(new moodle_url($root . '/mod/hvp/library/js/jquery.js'), true);
$PAGE->requires->js(new moodle_url($root . '/mod/hvp/library/js/h5p-utils.js'), true);
$PAGE->requires->js(new moodle_url($root . '/mod/hvp/library/js/h5p-data-view.js'), true);
$PAGE->requires->js(new moodle_url($root . '/mod/hvp/dataviews.js'), true);
$PAGE->requires->css(new moodle_url($root . '/mod/hvp/styles.css'));

// Add JavaScript settings to data views.
$settings = array(
    'dataViews' => array(
        "{$dataviewid}" => array(
            'source' => "{$root}/mod/hvp/ajax.php?action=results&content_id={$hvp->id}",
            'headers' => array(
                (object) array(
                    'text' => get_string('user', 'hvp'),
                    'sortable' => true
                ),
                (object) array(
                    'text' => get_string('score', 'hvp'),
                    'sortable' => true
                ),
                (object) array(
                    'text' => get_string('maxscore', 'hvp'),
                    'sortable' => true
                ),
                (object) array(
                    'text' => get_string('finished', 'hvp'),
                    'sortable' => true
                ),
                (object) array(
                    'text' => get_string('dataviewreportlabel', 'hvp')
                )
            ),
            'filters' => array(true),
            'order' => (object) array(
                'by' => 3,
                'dir' => 0
            ),
            'l10n' => array(
                'loading' => get_string('loadingdata', 'hvp'),
                'ajaxFailed' => get_string('ajaxfailed', 'hvp'),
                'noData' => get_string('nodata', 'hvp'),
                'currentPage' => get_string('currentpage', 'hvp'),
                'nextPage' => get_string('nextpage', 'hvp'),
                'previousPage' => get_string('previouspage', 'hvp'),
                'search' => get_string('search', 'hvp'),
                'empty' => get_string('empty', 'hvp')
            )
        )
    )
);
$PAGE->requires->data_for_js('H5PIntegration', $settings, true);

// Print page HTML.
echo $OUTPUT->header();
echo '<div class="clearer"></div>';

// Print H5P Content.
echo "<h2>{$title}</h2>";
echo '<div id="h5p-results">' . get_string('javascriptloading', 'hvp') . '</div>';

echo $OUTPUT->footer();
