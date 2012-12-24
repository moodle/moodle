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
 * In editing mode, allows the admin to edit a category,
 * and rearrange courses
 *
 * @package    core
 * @subpackage course
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("../config.php");
require_once($CFG->dirroot.'/course/lib.php');
require_once($CFG->libdir.'/textlib.class.php');

$id = required_param('id', PARAM_INT); // Category id
$page = optional_param('page', 0, PARAM_INT); // which page to show
$categoryedit = optional_param('categoryedit', -1, PARAM_BOOL);
$hide = optional_param('hide', 0, PARAM_INT);
$show = optional_param('show', 0, PARAM_INT);
$moveup = optional_param('moveup', 0, PARAM_INT);
$movedown = optional_param('movedown', 0, PARAM_INT);
$moveto = optional_param('moveto', 0, PARAM_INT);
$resort = optional_param('resort', 0, PARAM_BOOL);
$sesskey = optional_param('sesskey', '', PARAM_RAW);

// MDL-27824 - This is a temporary fix until we have the proper
// way to check/initialize $CFG value.
// @todo MDL-35138 remove this temporary solution
if (!empty($CFG->coursesperpage)) {
    $defaultperpage =  $CFG->coursesperpage;
} else {
    $defaultperpage = 20;
}
$perpage = optional_param('perpage', $defaultperpage, PARAM_INT); // how many per page

if (empty($id)) {
    print_error("unknowcategory");
}

$PAGE->set_category_by_id($id);
$PAGE->set_url(new moodle_url('/course/category.php', array('id' => $id)));
// This is sure to be the category context
$context = $PAGE->context;
// And the object has been loaded for us no need for another DB call
$category = $PAGE->category;

$canedit = can_edit_in_category($category->id);
if ($canedit) {
    if ($categoryedit !== -1) {
        $USER->editing = $categoryedit;
    }
    require_login();
    $editingon = $PAGE->user_is_editing();
} else {
    if ($CFG->forcelogin) {
        require_login();
    }
    $editingon = false;
}

if (!$category->visible) {
    require_capability('moodle/category:viewhiddencategories', $context);
}

$canmanage = has_capability('moodle/category:manage', $context);
$sesskeyprovided = !empty($sesskey) && confirm_sesskey($sesskey);

// Process any category actions.
if ($canmanage && $resort && $sesskeyprovided) {
    // Resort the category if requested
    if ($courses = get_courses($category->id, '', 'c.id,c.fullname,c.sortorder')) {
        collatorlib::asort_objects_by_property($courses, 'fullname', collatorlib::SORT_NATURAL);
        $i = 1;
        foreach ($courses as $course) {
            $DB->set_field('course', 'sortorder', $category->sortorder+$i, array('id'=>$course->id));
            $i++;
        }
        fix_course_sortorder(); // should not be needed
    }
}

// Process any course actions.
if ($editingon && $sesskeyprovided) {

    // Move a specified course to a new category
    if (!empty($moveto) and $data = data_submitted()) {
        // Some courses are being moved
        // user must have category update in both cats to perform this
        require_capability('moodle/category:manage', $context);
        require_capability('moodle/category:manage', context_coursecat::instance($moveto));

        if (!$destcategory = $DB->get_record('course_categories', array('id' => $data->moveto))) {
            print_error('cannotfindcategory', '', '', $data->moveto);
        }

        $courses = array();
        foreach ($data as $key => $value) {
            if (preg_match('/^c\d+$/', $key)) {
                $courseid = substr($key, 1);
                array_push($courses, $courseid);

                // check this course's category
                if ($movingcourse = $DB->get_record('course', array('id'=>$courseid))) {
                    if ($movingcourse->category != $id ) {
                        print_error('coursedoesnotbelongtocategory');
                    }
                } else {
                    print_error('cannotfindcourse');
                }
            }
        }
        move_courses($courses, $data->moveto);
    }

    // Hide or show a course
    if (!empty($hide) or !empty($show)) {
        if (!empty($hide)) {
            $course = $DB->get_record('course', array('id' => $hide));
            $visible = 0;
        } else {
            $course = $DB->get_record('course', array('id' => $show));
            $visible = 1;
        }

        if ($course) {
            $coursecontext = context_course::instance($course->id);
            require_capability('moodle/course:visibility', $coursecontext);
            // Set the visibility of the course. we set the old flag when user manually changes visibility of course.
            $DB->update_record('course', array('id' => $course->id, 'visible' => $visible, 'visibleold' => $visible, 'timemodified' => time()));
        }
    }


    // Move a course up or down
    if (!empty($moveup) or !empty($movedown)) {
        require_capability('moodle/category:manage', $context);

        // Ensure the course order has continuous ordering
        fix_course_sortorder();
        $swapcourse = NULL;

        if (!empty($moveup)) {
            if ($movecourse = $DB->get_record('course', array('id' => $moveup))) {
                $swapcourse = $DB->get_record('course', array('sortorder' => $movecourse->sortorder - 1));
            }
        } else {
            if ($movecourse = $DB->get_record('course', array('id' => $movedown))) {
                $swapcourse = $DB->get_record('course', array('sortorder' => $movecourse->sortorder + 1));
            }
        }
        if ($swapcourse and $movecourse) {
            // check course's category
            if ($movecourse->category != $id) {
                print_error('coursedoesnotbelongtocategory');
            }
            $DB->set_field('course', 'sortorder', $swapcourse->sortorder, array('id' => $movecourse->id));
            $DB->set_field('course', 'sortorder', $movecourse->sortorder, array('id' => $swapcourse->id));
        }
    }

} // End of editing stuff

// Prepare the standard URL params for this page. We'll need them later.
$urlparams = array('id' => $id);
if ($page) {
    $urlparams['page'] = $page;
}
if ($perpage) {
    $urlparams['perpage'] = $perpage;
}

// Begin output
if ($editingon && can_edit_in_category()) {
    // Integrate into the admin tree only if the user can edit categories at the top level,
    // otherwise the admin block does not appear to this user, and you get an error.
    require_once($CFG->libdir . '/adminlib.php');
    navigation_node::override_active_url(new moodle_url('/course/category.php', array('id' => $id)));
    admin_externalpage_setup('coursemgmt', '', $urlparams, $CFG->wwwroot . '/course/category.php');
    $PAGE->set_context($context);   // Ensure that we are actually showing blocks etc for the cat context

    $settingsnode = $PAGE->settingsnav->find_active_node();
    if ($settingsnode) {
        $settingsnode->make_inactive();
        $settingsnode->force_open();
        $PAGE->navbar->add($settingsnode->text, $settingsnode->action);
    }
    echo $OUTPUT->header();
} else {
    $site = get_site();
    $PAGE->set_title("$site->shortname: $category->name");
    $PAGE->set_heading($site->fullname);
    $PAGE->set_button(print_course_search('', true, 'navbar'));
    $PAGE->set_pagelayout('coursecategory');
    echo $OUTPUT->header();
}

/// Print the category selector
$displaylist = array();
$notused = array();
make_categories_list($displaylist, $notused);

echo '<div class="categorypicker">';
$select = new single_select(new moodle_url('/course/category.php'), 'id', $displaylist, $category->id, null, 'switchcategory');
$select->set_label(get_string('categories').':');
echo $OUTPUT->render($select);
echo '</div>';

/// Print current category description
if (!$editingon && $category->description) {
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
}

if ($editingon && $canmanage) {
    echo $OUTPUT->container_start('buttons');

    // Print button to update this category
    $url = new moodle_url('/course/editcategory.php', array('id' => $category->id));
    echo $OUTPUT->single_button($url, get_string('editcategorythis'), 'get');

    // Print button for creating new categories
    $url = new moodle_url('/course/editcategory.php', array('parent' => $category->id));
    echo $OUTPUT->single_button($url, get_string('addsubcategory'), 'get');

    echo $OUTPUT->container_end();
}

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
} else if ($numcourses <= $CFG->courseswithsummarieslimit and !$pagingmode and !$editingon) {
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
    echo '<table border="0" cellspacing="2" cellpadding="4" class="generalbox boxaligncenter"><tr>';
    echo '<th class="header" scope="col">'.get_string('courses').'</th>';
    if ($editingon) {
        echo '<th class="header" scope="col">'.get_string('edit').'</th>';
        echo '<th class="header" scope="col">'.get_string('select').'</th>';
    } else {
        echo '<th class="header" scope="col">&nbsp;</th>';
    }
    echo '</tr>';

    $count = 0;
    $abletomovecourses = false;  // for now

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
        if ($editingon) {
            echo '<td>';
            if (has_capability('moodle/course:update', $coursecontext)) {
                $url = new moodle_url('/course/edit.php', array('id' => $acourse->id, 'category' => $id, 'returnto' => 'category'));
                echo $OUTPUT->action_icon($url, new pix_icon('t/edit', get_string('settings')));
            }

            // role assignment link
            if (has_capability('moodle/course:enrolreview', $coursecontext)) {
                $url = new moodle_url('/enrol/users.php', array('id' => $acourse->id));
                echo $OUTPUT->action_icon($url, new pix_icon('t/enrolusers', get_string('enrolledusers', 'enrol')));
            }

            if (can_delete_course($acourse->id)) {
                $url = new moodle_url('/course/delete.php', array('id' => $acourse->id));
                echo $OUTPUT->action_icon($url, new pix_icon('t/delete', get_string('delete')));
            }

            // MDL-8885, users with no capability to view hidden courses, should not be able to lock themselves out
            if (has_capability('moodle/course:visibility', $coursecontext) && has_capability('moodle/course:viewhiddencourses', $coursecontext)) {
                if (!empty($acourse->visible)) {
                    $url = new moodle_url($baseurl, array('hide' => $acourse->id));
                    echo $OUTPUT->action_icon($url, new pix_icon('t/hide', get_string('hide')));
                } else {
                    $url = new moodle_url($baseurl, array('show' => $acourse->id));
                    echo $OUTPUT->action_icon($url, new pix_icon('t/show', get_string('show')));
                }
            }

            if (has_capability('moodle/backup:backupcourse', $coursecontext)) {
                $url = new moodle_url('/backup/backup.php', array('id' => $acourse->id));
                echo $OUTPUT->action_icon($url, new pix_icon('t/backup', get_string('backup')));
            }

            if (has_capability('moodle/restore:restorecourse', $coursecontext)) {
                $url = new moodle_url('/backup/restorefile.php', array('contextid' => $coursecontext->id));
                echo $OUTPUT->action_icon($url, new pix_icon('t/restore', get_string('restore')));
            }

            if ($canmanage) {
                if ($up) {
                    $url = new moodle_url($baseurl, array('moveup' => $acourse->id));
                    echo $OUTPUT->action_icon($url, new pix_icon('t/up', get_string('moveup')));
                }

                if ($down) {
                    $url = new moodle_url($baseurl, array('movedown' => $acourse->id));
                    echo $OUTPUT->action_icon($url, new pix_icon('t/down', get_string('movedown')));
                }
                $abletomovecourses = true;
            }

            echo '</td>';
            echo '<td align="center">';
            echo '<input type="checkbox" name="c'.$acourse->id.'" />';
            echo '</td>';
        } else {
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
        }
        echo "</tr>";
    }

    if ($abletomovecourses) {
        $movetocategories = array();
        $notused = array();
        make_categories_list($movetocategories, $notused, 'moodle/category:manage');
        $movetocategories[$category->id] = get_string('moveselectedcoursesto');
        echo '<tr><td colspan="3" align="right">';
        echo html_writer::label(get_string('moveselectedcoursesto'), 'movetoid', false, array('class' => 'accesshide'));
        echo html_writer::select($movetocategories, 'moveto', $category->id, null, array('id'=>'movetoid', 'class' => 'autosubmit'));
        $PAGE->requires->yui_module('moodle-core-formautosubmit',
            'M.core.init_formautosubmit',
            array(array('selectid' => 'movetoid', 'nothing' => $category->id))
        );
        echo '<input type="hidden" name="id" value="'.$category->id.'" />';
        echo '</td></tr>';
    }

    echo '</table>';
    echo '</div></form>';
    echo '<br />';
}

echo '<div class="buttons">';
if ($canmanage and $numcourses > 1) {
    // Print button to re-sort courses by name
    $url = new moodle_url('/course/category.php', array('id' => $category->id, 'resort' => 'name', 'sesskey' => sesskey()));
    echo $OUTPUT->single_button($url, get_string('resortcoursesbyname'), 'get');
}

if (has_capability('moodle/course:create', $context)) {
    // Print button to create a new course
    $url = new moodle_url('/course/edit.php', array('category' => $category->id, 'returnto' => 'category'));
    echo $OUTPUT->single_button($url, get_string('addnewcourse'), 'get');
}

if (!empty($CFG->enablecourserequests) && $category->id == $CFG->defaultrequestcategory) {
    print_course_request_buttons(context_system::instance());
}
echo '</div>';

print_course_search();

echo $OUTPUT->footer();
