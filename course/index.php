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
 * For most people, just lists the course categories
 * Allows the admin to create, delete and rename course categories
 *
 * @copyright 1999 Martin Dougiamas  http://dougiamas.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package course
 */

require_once("../config.php");
require_once("lib.php");

$categoryedit = optional_param('categoryedit', -1,PARAM_BOOL);
$delete   = optional_param('delete',0,PARAM_INT);
$hide     = optional_param('hide',0,PARAM_INT);
$show     = optional_param('show',0,PARAM_INT);
$move     = optional_param('move',0,PARAM_INT);
$moveto   = optional_param('moveto',-1,PARAM_INT);
$moveup   = optional_param('moveup',0,PARAM_INT);
$movedown = optional_param('movedown',0,PARAM_INT);

$site = get_site();

$systemcontext = context_system::instance();

$PAGE->set_url('/course/index.php');
$PAGE->set_context($systemcontext);
$PAGE->set_pagelayout('admin');

if (can_edit_in_category()) {
    if ($categoryedit !== -1) {
        $USER->editing = $categoryedit;
    }
    require_login();
    $adminediting = $PAGE->user_is_editing();
} else {
    if ($CFG->forcelogin) {
        require_login();
    }
    $adminediting = false;
}

$stradministration = get_string('administration');
$strcategories = get_string('categories');
$strcategory = get_string('category');
$strcourses = get_string('courses');
$stredit = get_string('edit');
$strdelete = get_string('delete');
$straction = get_string('action');
$strfulllistofcourses = get_string('fulllistofcourses');


/// Unless it's an editing admin, just print the regular listing of courses/categories
if (!$adminediting) {

/// Print form for creating new categories
    $countcategories = $DB->count_records('course_categories');

    if ($countcategories > 1 || ($countcategories == 1 && $DB->count_records('course') > 200)) {
        $strcourses = get_string('courses');
        $strcategories = get_string('categories');

        $PAGE->navbar->add($strcategories);
        $PAGE->set_title("$site->shortname: $strcategories");
        $PAGE->set_heading($COURSE->fullname);
        $PAGE->set_button(update_category_button());
        echo $OUTPUT->header();
        echo $OUTPUT->heading($strcategories);
        echo $OUTPUT->skip_link_target();
        echo $OUTPUT->box_start('categorybox');
        print_whole_category_list();
        echo $OUTPUT->box_end();
        print_course_search();
    } else {
        $PAGE->navbar->add($strfulllistofcourses);
        $PAGE->set_title("$site->shortname: $strfulllistofcourses");
        $PAGE->set_heading($COURSE->fullname);
        $PAGE->set_button(update_category_button());
        echo $OUTPUT->header();
        echo $OUTPUT->skip_link_target();
        echo $OUTPUT->box_start('courseboxes');
        print_courses(0);
        echo $OUTPUT->box_end();
    }

    echo $OUTPUT->container_start('buttons');
    if (has_capability('moodle/course:create', $systemcontext)) {
    /// Print link to create a new course
    /// Get the 1st available category
        $options = array('category' => $CFG->defaultrequestcategory);
        echo $OUTPUT->single_button(new moodle_url('edit.php', $options), get_string('addnewcourse'), 'get');
    }
    print_course_request_buttons($systemcontext);
    echo $OUTPUT->container_end();
    echo $OUTPUT->footer();
    exit;
}
/// Everything else is editing on mode.
require_once($CFG->libdir.'/adminlib.php');
admin_externalpage_setup('coursemgmt');

/// Delete a category.
if (!empty($delete) and confirm_sesskey()) {
    if (!$deletecat = $DB->get_record('course_categories', array('id'=>$delete))) {
        print_error('invalidcategoryid');
    }
    $context = context_coursecat::instance($delete);
    require_capability('moodle/category:manage', $context);
    require_capability('moodle/category:manage', get_category_or_system_context($deletecat->parent));

    $heading = get_string('deletecategory', 'moodle', format_string($deletecat->name, true, array('context' => $context)));
    require_once('delete_category_form.php');
    $mform = new delete_category_form(null, $deletecat);
    $mform->set_data(array('delete'=>$delete));

    if ($mform->is_cancelled()) {
        redirect('index.php');

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
        $deletedcourses = category_delete_full($deletecat, true);

        foreach($deletedcourses as $course) {
            echo $OUTPUT->notification(get_string('coursedeleted', '', $course->shortname), 'notifysuccess');
        }
        echo $OUTPUT->notification(get_string('coursecategorydeleted', '', format_string($deletecat->name, true, array('context' => $context))), 'notifysuccess');

    } else {
        category_delete_move($deletecat, $data->newparent, true);
    }

    // If we deleted $CFG->defaultrequestcategory, make it point somewhere else.
    if ($delete == $CFG->defaultrequestcategory) {
        set_config('defaultrequestcategory', $DB->get_field('course_categories', 'MIN(id)', array('parent'=>0)));
    }

    echo $OUTPUT->continue_button('index.php');

    echo $OUTPUT->footer();
    die;
}

/// Create a default category if necessary
if (!$categories = get_categories()) {    /// No category yet!
    // Try and make one
    $tempcat = new stdClass();
    $tempcat->name = get_string('miscellaneous');
    $tempcat->id = $DB->insert_record('course_categories', $tempcat);
    $tempcat->context = context_coursecat::instance($tempcat->id);
    mark_context_dirty('/'.SYSCONTEXTID);
    fix_course_sortorder(); // Required to build course_categories.depth and .path.
    set_config('defaultrequestcategory', $tempcat->id);
}

/// Move a category to a new parent if required
if (!empty($move) and ($moveto >= 0) and confirm_sesskey()) {
    if ($cattomove = $DB->get_record('course_categories', array('id'=>$move))) {
        require_capability('moodle/category:manage', get_category_or_system_context($cattomove->parent));
        if ($cattomove->parent != $moveto) {
            $newparent = $DB->get_record('course_categories', array('id'=>$moveto));
            require_capability('moodle/category:manage', get_category_or_system_context($moveto));
            move_category($cattomove, $newparent);
        }
    }
}

/// Hide or show a category
if ($hide and confirm_sesskey()) {
    if ($tempcat = $DB->get_record('course_categories', array('id'=>$hide))) {
        require_capability('moodle/category:manage', get_category_or_system_context($tempcat->parent));
        if ($tempcat->visible == 1) {
            course_category_hide($tempcat);
        }
    }
} else if ($show and confirm_sesskey()) {
    if ($tempcat = $DB->get_record('course_categories', array('id'=>$show))) {
        require_capability('moodle/category:manage', get_category_or_system_context($tempcat->parent));
        if ($tempcat->visible == 0) {
            course_category_show($tempcat);
        }
    }
}

/// Move a category up or down
if ((!empty($moveup) or !empty($movedown)) and confirm_sesskey()) {
    fix_course_sortorder();
    $swapcategory = NULL;

    if (!empty($moveup)) {
        require_capability('moodle/category:manage', context_coursecat::instance($moveup));
        if ($movecategory = $DB->get_record('course_categories', array('id'=>$moveup))) {
            if ($swapcategory = $DB->get_records_select('course_categories', "sortorder<? AND parent=?", array($movecategory->sortorder, $movecategory->parent), 'sortorder DESC', '*', 0, 1)) {
                $swapcategory = reset($swapcategory);
            }
        }
    } else {
        require_capability('moodle/category:manage', context_coursecat::instance($movedown));
        if ($movecategory = $DB->get_record('course_categories', array('id'=>$movedown))) {
            if ($swapcategory = $DB->get_records_select('course_categories', "sortorder>? AND parent=?", array($movecategory->sortorder, $movecategory->parent), 'sortorder ASC', '*', 0, 1)) {
                $swapcategory = reset($swapcategory);
            }
        }
    }
    if ($swapcategory and $movecategory) {
        $DB->set_field('course_categories', 'sortorder', $swapcategory->sortorder, array('id'=>$movecategory->id));
        $DB->set_field('course_categories', 'sortorder', $movecategory->sortorder, array('id'=>$swapcategory->id));
    }

    // finally reorder courses
    fix_course_sortorder();
}

/// Print headings
echo $OUTPUT->header();
echo $OUTPUT->heading($strcategories);

/// Print out the categories with all the knobs
$strcategories = get_string('categories');
$strcourses = get_string('courses');
$strmovecategoryto = get_string('movecategoryto');
$stredit = get_string('edit');

$displaylist = array();
$parentlist = array();

$displaylist[0] = get_string('top');
make_categories_list($displaylist, $parentlist);

echo '<table class="generaltable editcourse boxaligncenter"><tr class="header">';
echo '<th class="header" scope="col">'.$strcategories.'</th>';
echo '<th class="header" scope="col">'.$strcourses.'</th>';
echo '<th class="header" scope="col">'.$stredit.'</th>';
echo '<th class="header" scope="col">'.$strmovecategoryto.'</th>';
echo '</tr>';

print_category_edit(NULL, $displaylist, $parentlist);
echo '</table>';

echo '<div class="buttons">';
if (has_capability('moodle/course:create', $systemcontext)) {
    // print create course link to first category
    $options = array('category' => $CFG->defaultrequestcategory);
    $options['returnto'] = 'topcat';
    echo $OUTPUT->single_button(new moodle_url('edit.php', $options), get_string('addnewcourse'), 'get');
}

// Print button for creating new categories
if (has_capability('moodle/category:manage', $systemcontext)) {
    $options = array('parent'=>0);
    echo $OUTPUT->single_button(new moodle_url('editcategory.php', $options), get_string('addnewcategory'), 'get');
}

print_course_request_buttons($systemcontext);
echo '</div>';

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

        echo '<tr><td align="left" class="name">';
        for ($i=0; $i<$depth;$i++) {
            echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
        }
        $linkcss = $category->visible ? '' : ' class="dimmed" ';
        echo '<a '.$linkcss.' title="'.$str->edit.'" '.
             ' href="category.php?id='.$category->id.'&amp;categoryedit=on&amp;sesskey='.sesskey().'">'.
             format_string($category->name, true, array('context' => $category->context)).'</a>';
        echo '</td>';

        echo '<td class="count">'.$category->coursecount.'</td>';

        echo '<td class="icons">';    /// Print little icons

        if (has_capability('moodle/category:manage', $category->context)) {
            echo '<a title="'.$str->edit.'" href="editcategory.php?id='.$category->id.'"><img'.
                 ' src="'.$OUTPUT->pix_url('t/edit') . '" class="iconsmall" alt="'.$str->edit.'" /></a> ';

            echo '<a title="'.$str->delete.'" href="index.php?delete='.$category->id.'&amp;sesskey='.sesskey().'"><img'.
                 ' src="'.$OUTPUT->pix_url('t/delete') . '" class="iconsmall" alt="'.$str->delete.'" /></a> ';

            if (!empty($category->visible)) {
                echo '<a title="'.$str->hide.'" href="index.php?hide='.$category->id.'&amp;sesskey='.sesskey().'"><img'.
                     ' src="'.$OUTPUT->pix_url('t/hide') . '" class="iconsmall" alt="'.$str->hide.'" /></a> ';
            } else {
                echo '<a title="'.$str->show.'" href="index.php?show='.$category->id.'&amp;sesskey='.sesskey().'"><img'.
                     ' src="'.$OUTPUT->pix_url('t/show') . '" class="iconsmall" alt="'.$str->show.'" /></a> ';
            }

            if (has_capability('moodle/cohort:manage', $category->context) or has_capability('moodle/cohort:view', $category->context)) {
                echo '<a title="'.$str->cohorts.'" href="'.$CFG->wwwroot.'/cohort/index.php?contextid='.$category->context->id.'"><img'.
                     ' src="'.$OUTPUT->pix_url('t/cohort') . '" class="iconsmall" alt="'.$str->cohorts.'" /></a> ';
            }

            if ($up) {
                echo '<a title="'.$str->moveup.'" href="index.php?moveup='.$category->id.'&amp;sesskey='.sesskey().'"><img'.
                     ' src="'.$OUTPUT->pix_url('t/up') . '" class="iconsmall" alt="'.$str->moveup.'" /></a> ';
            } else {
                echo $str->spacer;
            }
            if ($down) {
                echo '<a title="'.$str->movedown.'" href="index.php?movedown='.$category->id.'&amp;sesskey='.sesskey().'"><img'.
                     ' src="'.$OUTPUT->pix_url('t/down') . '" class="iconsmall" alt="'.$str->movedown.'" /></a> ';
            } else {
                echo $str->spacer;
            }
        }
        echo '</td>';

        echo '<td align="left">';
        if (has_capability('moodle/category:manage', $category->context)) {
            $tempdisplaylist = $displaylist;
            unset($tempdisplaylist[$category->id]);
            foreach ($parentslist as $key => $parents) {
                if (in_array($category->id, $parents)) {
                    unset($tempdisplaylist[$key]);
                }
            }
            $popupurl = new moodle_url("index.php?move=$category->id&sesskey=".sesskey());
            $select = new single_select($popupurl, 'moveto', $tempdisplaylist, $category->parent, null, "moveform$category->id");
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
