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
 * Displays the top level category or all courses
 *
 * @package    core
 * @subpackage course
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("../config.php");
require_once($CFG->dirroot.'/course/lib.php');
require_once($CFG->libdir.'/textlib.class.php');
require_once($CFG->libdir. '/coursecatlib.php');

$id = required_param('id', PARAM_INT); // Category id
$page = optional_param('page', 0, PARAM_INT); // which page to show

$perpage = optional_param('perpage', $CFG->coursesperpage, PARAM_INT); // how many per page

if (empty($id)) {
    print_error("unknowcategory");
}

$PAGE->set_category_by_id($id);
$PAGE->set_url(new moodle_url('/course/category.php', array('id' => $id)));
// This is sure to be the category context
$context = $PAGE->context;
// And the object has been loaded for us no need for another DB call
$category = $PAGE->category;

if ($CFG->forcelogin) {
    require_login();
}

if (!$category->visible) {
    require_capability('moodle/category:viewhiddencategories', $context);
}

// Prepare the standard URL params for this page. We'll need them later.
$urlparams = array('id' => $id);
if ($page) {
    $urlparams['page'] = $page;
}
if ($perpage) {
    $urlparams['perpage'] = $perpage;
}

// Begin output
$PAGE->set_pagelayout('coursecategory');
$courserenderer = $PAGE->get_renderer('core', 'course');
$site = get_site();
$PAGE->set_title("$site->shortname: $category->name");
$PAGE->set_heading($site->fullname);

$content = $courserenderer->course_category($id);
echo $OUTPUT->header();


echo $content;

echo $OUTPUT->footer();
