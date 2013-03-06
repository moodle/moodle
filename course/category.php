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
$PAGE->set_button($courserenderer->course_search_form('', 'navbar'));
echo $OUTPUT->header();

/// Print the category selector
$displaylist = coursecat::make_categories_list();

echo '<div class="categorypicker">';
$select = new single_select(new moodle_url('/course/category.php'), 'id', $displaylist, $category->id, null, 'switchcategory');
$select->set_label(get_string('categories').':');
echo $OUTPUT->render($select);
echo '</div>';

/// Print current category description
echo $OUTPUT->box_start();
$options = new stdClass;
$options->noclean = true;
$options->para = false;
$options->overflowdiv = true;
if (!isset($category->descriptionformat)) {
    $category->descriptionformat = FORMAT_MOODLE;
}
$text = file_rewrite_pluginfile_urls($category->description, 'pluginfile.php', $context->id, 'coursecat', 'description', null);
echo format_text($text, $category->descriptionformat, $options);
echo $OUTPUT->box_end();

// Print out all the sub-categories
// In order to view hidden subcategories the user must have the viewhiddencategories
// capability in the current category.
if (has_capability('moodle/category:viewhiddencategories', $context)) {
    $categorywhere = '';
} else {
    $categorywhere = 'AND cc.visible = 1';
}
// We're going to preload the context for the subcategory as we know that we
// need it later on for formatting.

$ctxselect = context_helper::get_preload_record_columns_sql('ctx');
$sql = "SELECT cc.*, $ctxselect
          FROM {course_categories} cc
          JOIN {context} ctx ON cc.id = ctx.instanceid
         WHERE cc.parent = :parentid AND
               ctx.contextlevel = :contextlevel
               $categorywhere
      ORDER BY cc.sortorder ASC";
$subcategories = $DB->get_recordset_sql($sql, array('parentid' => $category->id, 'contextlevel' => CONTEXT_COURSECAT));
// Prepare a table to display the sub categories.
$table = new html_table;
$table->attributes = array('border' => '0', 'cellspacing' => '2', 'cellpadding' => '4', 'class' => 'generalbox boxaligncenter category_subcategories');
$table->head = array(new lang_string('subcategories'));
$table->data = array();
$baseurl = new moodle_url('/course/category.php');
foreach ($subcategories as $subcategory) {
    // Preload the context we will need it to format the category name shortly.
    context_helper::preload_from_record($subcategory);
    $context = context_coursecat::instance($subcategory->id);
    // Prepare the things we need to create a link to the subcategory
    $attributes = $subcategory->visible ? array() : array('class' => 'dimmed');
    $text = format_string($subcategory->name, true, array('context' => $context));
    // Add the subcategory to the table
    $baseurl->param('id', $subcategory->id);
    $table->data[] = array(html_writer::link($baseurl, $text, $attributes));
}

$subcategorieswereshown = (count($table->data) > 0);
if ($subcategorieswereshown) {
    echo html_writer::table($table);
}

// Print out all the courses.
$courses = get_courses_page($category->id, 'c.sortorder ASC',
        'c.id,c.sortorder,c.shortname,c.fullname,c.summary,c.visible',
        $totalcount, $page*$perpage, $perpage);
$numcourses = count($courses);

// We can consider that we are using pagination when the total count of courses is different than the one returned.
$pagingmode = $totalcount != $numcourses;

if (!$courses) {
    // There is no course to display.
    if (empty($subcategorieswereshown)) {
        echo $OUTPUT->heading(get_string("nocoursesyet"));
    }
} else if ($numcourses <= $CFG->courseswithsummarieslimit and !$pagingmode) {
    // We display courses with their summaries as we have not reached the limit, also we are not
    // in paging mode and not allowed to edit either.
    echo $OUTPUT->box_start('courseboxes');
    print_courses($category);
    echo $OUTPUT->box_end();
} else {
    // The conditions above have failed, we display a basic list of courses with paging/editing options.
    echo $OUTPUT->paging_bar($totalcount, $page, $perpage, "/course/category.php?id=$category->id&perpage=$perpage");

    echo '<form id="movecourses" action="category.php" method="post"><div>';
    echo '<input type="hidden" name="sesskey" value="'.sesskey().'" />';
    echo '<table border="0" cellspacing="2" cellpadding="4" class="generaltable boxaligncenter"><tr>';
    echo '<th class="header" scope="col">'.get_string('courses').'</th>';
    echo '<th class="header" scope="col">&nbsp;</th>';
    echo '</tr>';

    $count = 0;

    // Checking if we are at the first or at the last page, to allow courses to
    // be moved up and down beyond the paging border
    if ($totalcount > $perpage) {
        $atfirstpage = ($page == 0);
        if ($perpage > 0) {
            $atlastpage = (($page + 1) == ceil($totalcount / $perpage));
        } else {
            $atlastpage = true;
        }
    } else {
        $atfirstpage = true;
        $atlastpage = true;
    }

    $baseurl = new moodle_url('/course/category.php', $urlparams + array('sesskey' => sesskey()));
    foreach ($courses as $acourse) {
        $coursecontext = context_course::instance($acourse->id);

        $count++;
        $up = ($count > 1 || !$atfirstpage);
        $down = ($count < $numcourses || !$atlastpage);

        $linkcss = $acourse->visible ? '' : ' class="dimmed" ';
        echo '<tr>';
        $coursename = get_course_display_name_for_list($acourse);
        echo '<td><a '.$linkcss.' href="view.php?id='.$acourse->id.'">'. format_string($coursename) .'</a></td>';
        echo '<td align="right">';
        // print enrol info
        if ($icons = enrol_get_course_info_icons($acourse)) {
            foreach ($icons as $pix_icon) {
                echo $OUTPUT->render($pix_icon);
            }
        }
        if (!empty($acourse->summary)) {
            $url = new moodle_url("/course/info.php?id=$acourse->id");
            echo $OUTPUT->action_link($url, '<img alt="'.get_string('info').'" class="icon" src="'.$OUTPUT->pix_url('i/info') . '" />',
                new popup_action('click', $url, 'courseinfo'), array('title'=>get_string('summary')));
        }
        echo "</td>";
        echo "</tr>";
    }

    echo '</table>';
    echo '</div></form>';
    echo '<br />';
}

echo '<div class="buttons">';
if (has_capability('moodle/course:create', $context)) {
    // Print button to create a new course
    $url = new moodle_url('/course/edit.php', array('category' => $category->id, 'returnto' => 'category'));
    echo $OUTPUT->single_button($url, get_string('addnewcourse'), 'get');
}

if (!empty($CFG->enablecourserequests) && $category->id == $CFG->defaultrequestcategory) {
    print_course_request_buttons(context_system::instance());
}
echo '</div>';

echo $OUTPUT->footer();
