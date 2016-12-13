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
 * Lists the course categories
 *
 * @copyright 1999 Martin Dougiamas  http://dougiamas.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package course
 */

require_once("../config.php");
require_once($CFG->dirroot. '/course/lib.php');
require_once($CFG->libdir. '/coursecatlib.php');

$categoryid = optional_param('categoryid', 0, PARAM_INT); // Category id
$site = get_site();

if ($categoryid) {
    $PAGE->set_category_by_id($categoryid);
    $PAGE->set_url(new moodle_url('/course/index.php', array('categoryid' => $categoryid)));
    $PAGE->set_pagetype('course-index-category');
    // And the object has been loaded for us no need for another DB call
    $category = $PAGE->category;
} else {
    // Check if there is only one category, if so use that.
    if (coursecat::count_all() == 1) {
        $category = coursecat::get_default();

        $categoryid = $category->id;
        $PAGE->set_category_by_id($categoryid);
        $PAGE->set_pagetype('course-index-category');
    } else {
        $PAGE->set_context(context_system::instance());
    }

    $PAGE->set_url('/course/index.php');
}

$PAGE->set_pagelayout('coursecategory');
$courserenderer = $PAGE->get_renderer('core', 'course');

if ($CFG->forcelogin) {
    require_login();
}

if ($categoryid && !$category->visible && !has_capability('moodle/category:viewhiddencategories', $PAGE->context)) {
    throw new moodle_exception('unknowncategory');
}

$PAGE->set_heading($site->fullname);
$content = $courserenderer->course_category($categoryid);

echo $OUTPUT->header();
echo $OUTPUT->skip_link_target();
echo $content;

echo $OUTPUT->footer();
