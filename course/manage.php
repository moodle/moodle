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
 * Allows the admin to create, delete and rename course categories,
 * rearrange courses
 *
 * @package   core
 * @copyright 2013 Marina Glancy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("../config.php");
require_once($CFG->dirroot.'/course/lib.php');

$id = optional_param('id', 0, PARAM_INT); // Category id
$page = optional_param('page', 0, PARAM_INT); // which page to show
$sesskey = optional_param('sesskey', '', PARAM_RAW);
$perpage = optional_param('perpage', $CFG->coursesperpage, PARAM_INT); // how many per page

// actions to manage courses
$hide = optional_param('hide', 0, PARAM_INT);
$show = optional_param('show', 0, PARAM_INT);
$moveup = optional_param('moveup', 0, PARAM_INT);
$movedown = optional_param('movedown', 0, PARAM_INT);
$moveto = optional_param('moveto', 0, PARAM_INT);
$resort = optional_param('resort', 0, PARAM_BOOL);

// actions to manage categories
$deletecat   = optional_param('deletecat',0,PARAM_INT);
$hidecat     = optional_param('hidecat',0,PARAM_INT);
$showcat     = optional_param('showcat',0,PARAM_INT);
$movecat     = optional_param('movecat',0,PARAM_INT);
$movetocat   = optional_param('movetocat',-1,PARAM_INT);
$moveupcat   = optional_param('moveupcat',0,PARAM_INT);
$movedowncat = optional_param('movedowncat',0,PARAM_INT);

require_login();
$PAGE->set_url(new moodle_url('/course/manage.php', array('id' => $id)));
if ($id) {
    $PAGE->set_category_by_id($id);
    // This is sure to be the category context
    $context = $PAGE->context;
    // And the object has been loaded for us no need for another DB call
    $category = $PAGE->category;
} else {
    $context = context_system::instance();
    $PAGE->set_context($context);
    // create fake object for 0-category
    $category = (object)array(
        'id' => 0,
        'sortorder' => 0,
        'visible' => true
    );
}

if (!can_edit_in_category($category->id)) {
    if ($category->id) {
        redirect(new moodle_url('/course/category.php', array('id' => $category->id)));
    } else {
        redirect(new moodle_url('/course/index.php'));
    }
}

if (!$category->visible) {
    require_capability('moodle/category:viewhiddencategories', $context);
}

$canmanage = has_capability('moodle/category:manage', $context);
$sesskeyprovided = !empty($sesskey) && confirm_sesskey($sesskey);

/// Create a default category if necessary
if (!$id && (!$categories = get_categories())) {    /// No category yet!
    // Try and make one
    $tempcat = new stdClass();
    $tempcat->name = get_string('miscellaneous');
    $tempcat->id = $DB->insert_record('course_categories', $tempcat);
    $tempcat->context = context_coursecat::instance($tempcat->id);
    mark_context_dirty('/'.SYSCONTEXTID);
    fix_course_sortorder(); // Required to build course_categories.depth and .path.
    set_config('defaultrequestcategory', $tempcat->id);
}

// Process any category actions.

/// Delete a category.
if (!empty($deletecat) and confirm_sesskey()) {
    if (!$cattodelete = $DB->get_record('course_categories', array('id'=>$deletecat))) {
        print_error('invalidcategoryid');
    }
    $context = context_coursecat::instance($deletecat);
    require_capability('moodle/category:manage', $context);
    require_capability('moodle/category:manage', get_category_or_system_context($cattodelete->parent));

    $heading = get_string('deletecategory', 'moodle', format_string($cattodelete->name, true, array('context' => $context)));
    require_once('delete_category_form.php');
    $mform = new delete_category_form(null, $cattodelete);
    $mform->set_data(array('deletecat'=>$deletecat));

    if ($mform->is_cancelled()) {
        redirect('manage.php');

    } else if (!$data= $mform->get_data()) {
        require_once($CFG->libdir . '/questionlib.php');
        echo $OUTPUT->header();
        echo $OUTPUT->heading($heading);
        $mform->display();
        echo $OUTPUT->footer();
        exit();
    }

    echo $OUTPUT->header();
    echo $OUTPUT->heading($heading);

    if ($data->fulldelete) {
        $deletedcourses = category_delete_full($cattodelete, true);

        foreach($deletedcourses as $course) {
            echo $OUTPUT->notification(get_string('coursedeleted', '', $course->shortname), 'notifysuccess');
        }
        echo $OUTPUT->notification(get_string('coursecategorydeleted', '', format_string($cattodelete->name, true, array('context' => $context))), 'notifysuccess');

    } else {
        category_delete_move($cattodelete, $data->newparent, true);
    }

    // If we deleted $CFG->defaultrequestcategory, make it point somewhere else.
    if ($deletecat == $CFG->defaultrequestcategory) {
        set_config('defaultrequestcategory', $DB->get_field('course_categories', 'MIN(id)', array('parent'=>0)));
    }

    echo $OUTPUT->continue_button('manage.php');

    echo $OUTPUT->footer();
    die;
}

/// Move a category to a new parent if required
if (!empty($movecat) and ($movetocat >= 0) and confirm_sesskey()) {
    if ($cattomove = $DB->get_record('course_categories', array('id'=>$movecat))) {
        require_capability('moodle/category:manage', get_category_or_system_context($cattomove->parent));
        if ($cattomove->parent != $movetocat) {
            $newparent = $DB->get_record('course_categories', array('id'=>$movetocat));
            require_capability('moodle/category:manage', get_category_or_system_context($movetocat));
            move_category($cattomove, $newparent);
        }
    }
}

/// Hide or show a category
if ($hidecat and confirm_sesskey()) {
    if ($tempcat = $DB->get_record('course_categories', array('id'=>$hidecat))) {
        require_capability('moodle/category:manage', get_category_or_system_context($tempcat->parent));
        if ($tempcat->visible == 1) {
            course_category_hide($tempcat);
        }
    }
} else if ($showcat and confirm_sesskey()) {
    if ($tempcat = $DB->get_record('course_categories', array('id'=>$showcat))) {
        require_capability('moodle/category:manage', get_category_or_system_context($tempcat->parent));
        if ($tempcat->visible == 0) {
            course_category_show($tempcat);
        }
    }
}

/// Move a category up or down
if ((!empty($moveupcat) or !empty($movedowncat)) and confirm_sesskey()) {
    fix_course_sortorder();
    $swapcategory = NULL;

    if (!empty($moveupcat)) {
        require_capability('moodle/category:manage', context_coursecat::instance($moveupcat));
        if ($movecategory = $DB->get_record('course_categories', array('id'=>$moveupcat))) {
            if ($swapcategory = $DB->get_records_select('course_categories', "sortorder<? AND parent=?", array($movecategory->sortorder, $movecategory->parent), 'sortorder DESC', '*', 0, 1)) {
                $swapcategory = reset($swapcategory);
            }
        }
    } else {
        require_capability('moodle/category:manage', context_coursecat::instance($movedowncat));
        if ($movecategory = $DB->get_record('course_categories', array('id'=>$movedowncat))) {
            if ($swapcategory = $DB->get_records_select('course_categories', "sortorder>? AND parent=?", array($movecategory->sortorder, $movecategory->parent), 'sortorder ASC', '*', 0, 1)) {
                $swapcategory = reset($swapcategory);
            }
        }
    }
    if ($swapcategory and $movecategory) {
        $DB->set_field('course_categories', 'sortorder', $swapcategory->sortorder, array('id'=>$movecategory->id));
        $DB->set_field('course_categories', 'sortorder', $movecategory->sortorder, array('id'=>$swapcategory->id));
        add_to_log(SITEID, "category", "move", "editcategory.php?id=$movecategory->id", $movecategory->id);
    }

    // finally reorder courses
    fix_course_sortorder();
}

// Resort courses
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
if ($sesskeyprovided) {

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
            add_to_log($course->id, "course", ($visible ? 'show' : 'hide'), "edit.php?id=$course->id", $course->id);
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
            add_to_log($movecourse->id, "course", "move", "edit.php?id=$movecourse->id", $movecourse->id);
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
$PAGE->set_pagelayout('coursecategory');
if (can_edit_in_category()) {
    // Integrate into the admin tree only if the user can edit categories at the top level,
    // otherwise the admin block does not appear to this user, and you get an error.
    require_once($CFG->libdir . '/adminlib.php');
    if ($id) {
        navigation_node::override_active_url(new moodle_url('/course/category.php', array('id' => $id)));
    }
    admin_externalpage_setup('coursemgmt', '', $urlparams, $CFG->wwwroot . '/course/manage.php');

    $settingsnode = $PAGE->settingsnav->find_active_node();
    if ($id && $settingsnode) {
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
    echo $OUTPUT->header();
}

if (!$category->id) {
    /// Print out the categories with all the knobs
    $strcategories = get_string('categories');
    $strcourses = get_string('courses');
    $strmovecategoryto = get_string('movecategoryto');
    $stredit = get_string('edit');

    $displaylist = array();
    $parentlist = array();

    $displaylist[0] = get_string('top');
    make_categories_list($displaylist, $parentlist);

    echo '<table id="coursecategories" class="admintable generaltable editcourse"><tr class="header">';
    echo '<th class="header" scope="col">'.$strcategories.'</th>';
    echo '<th class="header" scope="col">'.$strcourses.'</th>';
    echo '<th class="header" scope="col">'.$stredit.'</th>';
    echo '<th class="header" scope="col">'.$strmovecategoryto.'</th>';
    echo '</tr>';

    print_category_edit(NULL, $displaylist, $parentlist);
    echo '</table>';
} else {
    /// Print the category selector
    $displaylist = array();
    $notused = array();
    make_categories_list($displaylist, $notused);

    echo '<div class="categorypicker">';
    $select = new single_select(new moodle_url('/course/manage.php'), 'id',
            // TODO 'Top' => string
            array(0 => 'Top') + $displaylist, $category->id, null, 'switchcategory');
    $select->set_label(get_string('categories').':');
    echo $OUTPUT->render($select);
    echo '</div>';
}

if ($canmanage) {
    echo $OUTPUT->container_start('buttons');

    // Print button to update this category
    if ($category->id) {
        $url = new moodle_url('/course/editcategory.php', array('id' => $category->id));
        echo $OUTPUT->single_button($url, get_string('editcategorythis'), 'get');
    }

    // Print button for creating new categories
    $url = new moodle_url('/course/editcategory.php', array('parent' => $category->id));
    echo $OUTPUT->single_button($url, get_string('addsubcategory'), 'get');

    echo $OUTPUT->container_end();
}

if ($category->id) {
    // Print out all the sub-categories (plain mode)
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
    $baseurl = new moodle_url('/course/manage.php');
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

    $courses = get_courses_page($category->id, 'c.sortorder ASC',
            'c.id,c.sortorder,c.shortname,c.fullname,c.summary,c.visible',
            $totalcount, $page*$perpage, $perpage);
    $numcourses = count($courses);
} else {
    $subcategorieswereshown = true;
    $courses = array();
    $numcourses = $totalcount = 0;
}

if (!$courses) {
    // There is no course to display.
    if (empty($subcategorieswereshown)) {
        echo $OUTPUT->heading(get_string("nocoursesyet"));
    }
} else {
    // display a basic list of courses with paging/editing options.
    echo $OUTPUT->paging_bar($totalcount, $page, $perpage, "/course/manage.php?id=$category->id&perpage=$perpage");

    echo '<form id="movecourses" action="manage.php" method="post"><div>';
    echo '<input type="hidden" name="sesskey" value="'.sesskey().'" />';
    echo '<table border="0" cellspacing="2" cellpadding="4" class="generalbox boxaligncenter"><tr>';
    echo '<th class="header" scope="col">'.get_string('courses').'</th>';
    echo '<th class="header" scope="col">'.get_string('edit').'</th>';
    echo '<th class="header" scope="col">'.get_string('select').'</th>';
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

    $baseurl = new moodle_url('/course/manage.php', $urlparams + array('sesskey' => sesskey()));
    foreach ($courses as $acourse) {
        $coursecontext = context_course::instance($acourse->id);

        $count++;
        $up = ($count > 1 || !$atfirstpage);
        $down = ($count < $numcourses || !$atlastpage);

        $linkcss = $acourse->visible ? '' : ' class="dimmed" ';
        echo '<tr>';
        $coursename = get_course_display_name_for_list($acourse);
        echo '<td><a '.$linkcss.' href="view.php?id='.$acourse->id.'">'. format_string($coursename) .'</a></td>';
        echo '<td>';
        if (has_capability('moodle/course:update', $coursecontext)) {
            $url = new moodle_url('/course/edit.php', array('id' => $acourse->id, 'category' => $id, 'returnto' => 'catmanage'));
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
    $url = new moodle_url('/course/manage.php', array('id' => $category->id, 'resort' => 'name', 'sesskey' => sesskey()));
    echo $OUTPUT->single_button($url, get_string('resortcoursesbyname'), 'get');
}

if (has_capability('moodle/course:create', $context)) {
    // Print button to create a new course
    $url = new moodle_url('/course/edit.php');
    if (!$category->id) {
        $url->params(array('category' => $CFG->defaultrequestcategory,
                'returnto' => 'topcatmanage'));
    } else {
        $url->params(array('category' => $category->id,
                'returnto' => 'catmanage'));
    }
    echo $OUTPUT->single_button($url, get_string('addnewcourse'), 'get');
}

if (!empty($CFG->enablecourserequests) && $category->id == $CFG->defaultrequestcategory) {
    print_course_request_buttons(context_system::instance());
}
echo '</div>';

print_course_search();

echo $OUTPUT->footer();

function print_category_edit($category, $displaylist, $parentslist, $depth=-1, $up=false, $down=false) {
/// Recursive function to print all the categories ready for editing

    global $CFG, $USER, $OUTPUT;

    static $str = NULL;

    if (is_null($str)) {
        $str = new stdClass;
        $str->edit     = get_string('edit');
        $str->delete   = get_string('delete');
        $str->moveup   = get_string('moveup');
        $str->movedown = get_string('movedown');
        $str->edit     = get_string('editthiscategory');
        $str->hide     = get_string('hide');
        $str->show     = get_string('show');
        $str->cohorts  = get_string('cohorts', 'cohort');
        $str->spacer = $OUTPUT->spacer().' ';
    }

    if (!empty($category)) {

        if (!isset($category->context)) {
            $category->context = context_coursecat::instance($category->id);
        }

        echo '<tr><td class="leftalign name">';
        for ($i=0; $i<$depth;$i++) {
            echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
        }
        $linkcss = $category->visible ? '' : ' class="dimmed" ';
        echo '<a '.$linkcss.' title="'.$str->edit.'" '.
             ' href="manage.php?id='.$category->id.'&amp;sesskey='.sesskey().'">'.
             format_string($category->name, true, array('context' => $category->context)).'</a>';
        echo '</td>';

        echo '<td class="centeralign count">'.$category->coursecount.'</td>';

        echo '<td class="centeralign icons">';  /// Print little icons

        if (has_capability('moodle/category:manage', $category->context)) {
            echo '<a title="'.$str->edit.'" href="editcategory.php?id='.$category->id.'"><img'.
                 ' src="'.$OUTPUT->pix_url('t/edit') . '" class="iconsmall" alt="'.$str->edit.'" /></a> ';

            echo '<a title="'.$str->delete.'" href="manage.php?deletecat='.$category->id.'&amp;sesskey='.sesskey().'"><img'.
                 ' src="'.$OUTPUT->pix_url('t/delete') . '" class="iconsmall" alt="'.$str->delete.'" /></a> ';

            if (!empty($category->visible)) {
                echo '<a title="'.$str->hide.'" href="manage.php?hidecat='.$category->id.'&amp;sesskey='.sesskey().'"><img'.
                     ' src="'.$OUTPUT->pix_url('t/hide') . '" class="iconsmall" alt="'.$str->hide.'" /></a> ';
            } else {
                echo '<a title="'.$str->show.'" href="manage.php?showcat='.$category->id.'&amp;sesskey='.sesskey().'"><img'.
                     ' src="'.$OUTPUT->pix_url('t/show') . '" class="iconsmall" alt="'.$str->show.'" /></a> ';
            }

            if (has_capability('moodle/cohort:manage', $category->context) or has_capability('moodle/cohort:view', $category->context)) {
                echo '<a title="'.$str->cohorts.'" href="'.$CFG->wwwroot.'/cohort/index.php?contextid='.$category->context->id.'"><img'.
                     ' src="'.$OUTPUT->pix_url('t/cohort') . '" class="iconsmall" alt="'.$str->cohorts.'" /></a> ';
            }

            if ($up) {
                echo '<a title="'.$str->moveup.'" href="manage.php?moveupcat='.$category->id.'&amp;sesskey='.sesskey().'"><img'.
                     ' src="'.$OUTPUT->pix_url('t/up') . '" class="iconsmall" alt="'.$str->moveup.'" /></a> ';
            } else {
                echo $str->spacer;
            }
            if ($down) {
                echo '<a title="'.$str->movedown.'" href="manage.php?movedowncat='.$category->id.'&amp;sesskey='.sesskey().'"><img'.
                     ' src="'.$OUTPUT->pix_url('t/down') . '" class="iconsmall" alt="'.$str->movedown.'" /></a> ';
            } else {
                echo $str->spacer;
            }
        }
        echo '</td>';

        echo '<td class="leftalign">';
        if (has_capability('moodle/category:manage', $category->context)) {
            $tempdisplaylist = $displaylist;
            unset($tempdisplaylist[$category->id]);
            foreach ($parentslist as $key => $parents) {
                if (in_array($category->id, $parents)) {
                    unset($tempdisplaylist[$key]);
                }
            }
            $popupurl = new moodle_url("manage.php?movecat=$category->id&sesskey=".sesskey());
            $select = new single_select($popupurl, 'movetocat', $tempdisplaylist, $category->parent, null, "moveform$category->id");
            $select->set_label(get_string('frontpagecategorynames'), array('class' => 'accesshide'));
            echo $OUTPUT->render($select);
        }
        echo '</td>';
        echo '</tr>';
    } else {
        $category = new stdClass();
        $category->id = '0';
    }

    if ($categories = get_categories($category->id)) {   // Print all the children recursively
        $countcats = count($categories);
        $count = 0;
        $first = true;
        $last = false;
        foreach ($categories as $cat) {
            $count++;
            if ($count == $countcats) {
                $last = true;
            }
            $up = $first ? false : true;
            $down = $last ? false : true;
            $first = false;

            print_category_edit($cat, $displaylist, $parentslist, $depth+1, $up, $down);
        }
    }
}
