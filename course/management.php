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
 * Course and category management interfaces.
 *
 * @package    core_course
 * @copyright  2013 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../config.php');
require_once($CFG->dirroot.'/lib/coursecatlib.php');
require_once($CFG->dirroot.'/course/lib.php');

$categoryid = optional_param('categoryid', null, PARAM_INT);
$selectedcategoryid = optional_param('selectedcategoryid', null, PARAM_INT);
$courseid = optional_param('courseid', null, PARAM_INT);
$action = optional_param('action', false, PARAM_ALPHA);
$page = optional_param('page', 0, PARAM_INT);
$perpage = optional_param('perpage', null, PARAM_INT);
$viewmode = optional_param('view', 'default', PARAM_ALPHA); // Can be one of default, combined, courses, or categories.

// Search related params.
$search = optional_param('search', '', PARAM_RAW); // Search words. Shortname, fullname, idnumber and summary get searched.
$blocklist = optional_param('blocklist', 0, PARAM_INT); // Find courses containing this block.
$modulelist = optional_param('modulelist', '', PARAM_PLUGIN); // Find courses containing the given modules.

if (!in_array($viewmode, array('default', 'combined', 'courses', 'categories'))) {
    $viewmode = 'default';
}

$issearching = ($search !== '' || $blocklist !== 0 || $modulelist !== '');
if ($issearching) {
    $viewmode = 'courses';
}

$url = new moodle_url('/course/management.php');
$systemcontext = $context = context_system::instance();
if ($courseid) {
    $record = get_course($courseid);
    $course = new course_in_list($record);
    $category = coursecat::get($course->category);
    $categoryid = $category->id;
    $context = context_coursecat::instance($category->id);
    $url->param('categoryid', $categoryid);
    $url->param('courseid', $course->id);

} else if ($categoryid) {
    $courseid = null;
    $course = null;
    $category = coursecat::get($categoryid);
    $context = context_coursecat::instance($category->id);
    $url->param('categoryid', $category->id);

} else {
    $course = null;
    $courseid = null;
    $category = coursecat::get_default();
    $categoryid = $category->id;
    $context = context_coursecat::instance($category->id);
    $url->param('categoryid', $category->id);
}

// Check if there is a selected category param, and if there is apply it.
if ($course === null && $selectedcategoryid !== null && $selectedcategoryid !== $categoryid) {
    $url->param('categoryid', $selectedcategoryid);
}

if ($page !== 0) {
    $url->param('page', $page);
}
if ($viewmode !== 'default') {
    $url->param('view', $viewmode);
}
if ($search !== '') {
    $url->param('search', $search);
}
if ($blocklist !== 0) {
    $url->param('blocklist', $search);
}
if ($modulelist !== '') {
    $url->param('modulelist', $search);
}

$strmanagement = new lang_string('coursecatmanagement');
$pageheading = format_string($SITE->fullname, true, array('context' => $systemcontext));

$PAGE->set_context($context);
$PAGE->set_url($url);
$PAGE->set_pagelayout('admin');
$PAGE->set_title($strmanagement);
$PAGE->set_heading($pageheading);

// This is a system level page that operates on other contexts.
require_login();

if (!coursecat::has_capability_on_any(array('moodle/category:manage', 'moodle/course:create'))) {
    // The user isn't able to manage any categories. Lets redirect them to the relevant course/index.php page.
    $url = new moodle_url('/course/index.php');
    if ($categoryid) {
        $url->param('categoryid', $categoryid);
    }
    redirect($url);
}

// If the user poses any of these capabilities then they will be able to see the admin
// tree and the management link within it.
// This is the most accurate form of navigation.
$capabilities = array(
    'moodle/site:config',
    'moodle/backup:backupcourse',
    'moodle/category:manage',
    'moodle/course:create',
    'moodle/site:approvecourse'
);
if ($category && !has_any_capability($capabilities, $systemcontext)) {
    // If the user doesn't poses any of these system capabilities then we're going to mark the manage link in the settings block
    // as active, tell the page to ignore the active path and just build what the user would expect.
    // This will at least give the page some relevant navigation.
    navigation_node::override_active_url(new moodle_url('/course/management.php', array('categoryid' => $category->id)));
    $PAGE->set_category_by_id($category->id);
    $PAGE->navbar->ignore_active(true);
    $PAGE->navbar->add(get_string('coursemgmt', 'admin'), $PAGE->url->out_omit_querystring());
} else {
    // If user has system capabilities, make sure the "Manage courses and categories" item in Administration block is active.
    navigation_node::require_admin_tree();
    navigation_node::override_active_url(new moodle_url('/course/management.php'));
}
if (!$issearching && $category !== null) {
    $parents = coursecat::get_many($category->get_parents());
    $parents[] = $category;
    foreach ($parents as $parent) {
        $PAGE->navbar->add(
            $parent->get_formatted_name(),
            new moodle_url('/course/management.php', array('categoryid' => $parent->id))
        );
    }
    if ($course instanceof course_in_list) {
        // Use the list name so that it matches whats being displayed below.
        $PAGE->navbar->add($course->get_formatted_name());
    }
}

$notificationspass = array();
$notificationsfail = array();

if ($action !== false && confirm_sesskey()) {
    // Actions:
    // - resortcategories : Resort the courses in the given category.
    // - resortcourses : Resort courses
    // - showcourse : make a course visible.
    // - hidecourse : make a course hidden.
    // - movecourseup : move the selected course up one.
    // - movecoursedown : move the selected course down.
    // - showcategory : make a category visible.
    // - hidecategory : make a category hidden.
    // - movecategoryup : move category up.
    // - movecategorydown : move category down.
    // - deletecategory : delete the category either in full, or moving contents.
    // - bulkaction : performs bulk actions:
    //    - bulkmovecourses.
    //    - bulkmovecategories.
    //    - bulkresortcategories.
    $redirectback = false;
    $redirectmessage = false;
    switch ($action) {
        case 'resortcategories' :
            $sort = required_param('resort', PARAM_ALPHA);
            $cattosort = coursecat::get((int)optional_param('categoryid', 0, PARAM_INT));
            $redirectback = \core_course\management\helper::action_category_resort_subcategories($cattosort, $sort);
            break;
        case 'resortcourses' :
            // They must have specified a category.
            required_param('categoryid', PARAM_INT);
            $sort = required_param('resort', PARAM_ALPHA);
            \core_course\management\helper::action_category_resort_courses($category, $sort);
            break;
        case 'showcourse' :
            $redirectback = \core_course\management\helper::action_course_show($course);
            break;
        case 'hidecourse' :
            $redirectback = \core_course\management\helper::action_course_hide($course);
            break;
        case 'movecourseup' :
            // They must have specified a category and a course.
            required_param('categoryid', PARAM_INT);
            required_param('courseid', PARAM_INT);
            $redirectback = \core_course\management\helper::action_course_change_sortorder_up_one($course, $category);
            break;
        case 'movecoursedown' :
            // They must have specified a category and a course.
            required_param('categoryid', PARAM_INT);
            required_param('courseid', PARAM_INT);
            $redirectback = \core_course\management\helper::action_course_change_sortorder_down_one($course, $category);
            break;
        case 'showcategory' :
            // They must have specified a category.
            required_param('categoryid', PARAM_INT);
            $redirectback = \core_course\management\helper::action_category_show($category);
            break;
        case 'hidecategory' :
            // They must have specified a category.
            required_param('categoryid', PARAM_INT);
            $redirectback = \core_course\management\helper::action_category_hide($category);
            break;
        case 'movecategoryup' :
            // They must have specified a category.
            required_param('categoryid', PARAM_INT);
            $redirectback = \core_course\management\helper::action_category_change_sortorder_up_one($category);
            break;
        case 'movecategorydown' :
            // They must have specified a category.
            required_param('categoryid', PARAM_INT);
            $redirectback = \core_course\management\helper::action_category_change_sortorder_down_one($category);
            break;
        case 'deletecategory':
            // They must have specified a category.
            required_param('categoryid', PARAM_INT);
            if (!$category->can_delete()) {
                throw new moodle_exception('permissiondenied', 'error', '', null, 'coursecat::can_resort');
            }
            $mform = new core_course_deletecategory_form(null, $category);
            if ($mform->is_cancelled()) {
                redirect($PAGE->url);
            }
            // Start output.
            /* @var core_course_management_renderer|core_renderer $renderer */
            $renderer = $PAGE->get_renderer('core_course', 'management');
            echo $renderer->header();
            echo $renderer->heading(get_string('deletecategory', 'moodle', $category->get_formatted_name()));

            if ($data = $mform->get_data()) {
                // The form has been submit handle it.
                if ($data->fulldelete == 1 && $category->can_delete_full()) {
                    $continueurl = new moodle_url('/course/management.php');
                    if ($category->parent != '0') {
                        $continueurl->param('categoryid', $category->parent);
                    }
                    $notification = get_string('coursecategorydeleted', '', $category->get_formatted_name());
                    $deletedcourses = $category->delete_full(true);
                    foreach ($deletedcourses as $course) {
                        echo $renderer->notification(get_string('coursedeleted', '', $course->shortname), 'notifysuccess');
                    }
                    echo $renderer->notification($notification, 'notifysuccess');
                    echo $renderer->continue_button($continueurl);
                } else if ($data->fulldelete == 0 && $category->can_move_content_to($data->newparent)) {
                    $continueurl = new moodle_url('/course/management.php', array('categoryid' => $data->newparent));
                    $category->delete_move($data->newparent, true);
                    echo $renderer->continue_button($continueurl);
                } else {
                    // Some error in parameters (user is cheating?)
                    $mform->display();
                }
            } else {
                // Display the form.
                $mform->display();
            }
            // Finish output and exit.
            echo $renderer->footer();
            exit();
            break;
        case 'bulkaction':
            $bulkmovecourses = optional_param('bulkmovecourses', false, PARAM_BOOL);
            $bulkmovecategories = optional_param('bulkmovecategories', false, PARAM_BOOL);
            $bulkresortcategories = optional_param('bulksort', false, PARAM_BOOL);

            if ($bulkmovecourses) {
                // Move courses out of the current category and into a new category.
                // They must have specified a category.
                required_param('categoryid', PARAM_INT);
                $movetoid = required_param('movecoursesto', PARAM_INT);
                $courseids = optional_param_array('bc', false, PARAM_INT);
                if ($courseids === false) {
                    break;
                }
                $moveto = coursecat::get($movetoid);
                try {
                    // If this fails we want to catch the exception and report it.
                    $redirectback = \core_course\management\helper::move_courses_into_category($moveto,
                        $courseids);
                    if ($redirectback) {
                        $a = new stdClass;
                        $a->category = $moveto->get_formatted_name();
                        $a->courses = count($courseids);
                        $redirectmessage = get_string('bulkmovecoursessuccess', 'moodle', $a);
                    }
                } catch (moodle_exception $ex) {
                    $redirectback = false;
                    $notificationsfail[] = $ex->getMessage();
                }
            } else if ($bulkmovecategories) {
                $categoryids = optional_param_array('bcat', array(), PARAM_INT);
                $movetocatid = required_param('movecategoriesto', PARAM_INT);
                $movetocat = coursecat::get($movetocatid);
                $movecount = 0;
                foreach ($categoryids as $id) {
                    $cattomove = coursecat::get($id);
                    if ($id == $movetocatid) {
                        $notificationsfail[] = get_string('movecategoryownparent', 'error', $cattomove->get_formatted_name());
                        continue;
                    }
                    if (strpos($movetocat->path, $cattomove->path) === 0) {
                        $notificationsfail[] = get_string('movecategoryparentconflict', 'error', $cattomove->get_formatted_name());
                        continue;
                    }
                    if ($cattomove->parent != $movetocatid) {
                        if ($cattomove->can_change_parent($movetocatid)) {
                            $cattomove->change_parent($movetocatid);
                            $movecount++;
                        } else {
                            $notificationsfail[] = get_string('movecategorynotpossible', 'error', $cattomove->get_formatted_name());
                        }
                    }
                }
                if ($movecount > 1) {
                    $a = new stdClass;
                    $a->count = $movecount;
                    $a->to = $movetocat->get_formatted_name();
                    $movesuccessstrkey = 'movecategoriessuccess';
                    if ($movetocatid == 0) {
                        $movesuccessstrkey = 'movecategoriestotopsuccess';
                    }
                    $notificationspass[] = get_string($movesuccessstrkey, 'moodle', $a);
                } else if ($movecount === 1) {
                    $a = new stdClass;
                    $a->moved = $cattomove->get_formatted_name();
                    $a->to = $movetocat->get_formatted_name();
                    $movesuccessstrkey = 'movecategorysuccess';
                    if ($movetocatid == 0) {
                        $movesuccessstrkey = 'movecategorytotopsuccess';
                    }
                    $notificationspass[] = get_string($movesuccessstrkey, 'moodle', $a);
                }
            } else if ($bulkresortcategories) {
                $for = required_param('selectsortby', PARAM_ALPHA);
                $sortcategoriesby = required_param('resortcategoriesby', PARAM_ALPHA);
                $sortcoursesby = required_param('resortcoursesby', PARAM_ALPHA);

                if ($sortcategoriesby === 'none' && $sortcoursesby === 'none') {
                    // They're not sorting anything.
                    break;
                }
                if (!in_array($sortcategoriesby, array('idnumber', 'idnumberdesc',
                                                       'name', 'namedesc'))) {
                    $sortcategoriesby = false;
                }
                if (!in_array($sortcoursesby, array('timecreated', 'timecreateddesc',
                                                    'idnumber', 'idnumberdesc',
                                                    'fullname', 'fullnamedesc',
                                                    'shortname', 'shortnamedesc'))) {
                    $sortcoursesby = false;
                }

                if ($for === 'thiscategory') {
                    $categoryids = array(
                        required_param('currentcategoryid', PARAM_INT)
                    );
                    $categories = coursecat::get_many($categoryids);
                } else if ($for === 'selectedcategories') {
                    // Bulk resort selected categories.
                    $categoryids = optional_param_array('bcat', false, PARAM_INT);
                    $sort = required_param('resortcategoriesby', PARAM_ALPHA);
                    if ($categoryids === false) {
                        break;
                    }
                    $categories = coursecat::get_many($categoryids);
                } else if ($for === 'allcategories') {
                    if ($sortcategoriesby && coursecat::get(0)->can_resort_subcategories()) {
                        \core_course\management\helper::action_category_resort_subcategories(coursecat::get(0), $sortcategoriesby);
                    }
                    $categorieslist = coursecat::make_categories_list('moodle/category:manage');
                    $categoryids = array_keys($categorieslist);
                    $categories = coursecat::get_many($categoryids);
                    unset($categorieslist);
                } else {
                    break;
                }
                foreach ($categories as $cat) {
                    if ($sortcategoriesby && $cat->can_resort_subcategories()) {
                        // Don't clean up here, we'll do it once we're all done.
                        \core_course\management\helper::action_category_resort_subcategories($cat, $sortcategoriesby, false);
                    }
                    if ($sortcoursesby && $cat->can_resort_courses()) {
                        \core_course\management\helper::action_category_resort_courses($cat, $sortcoursesby, false);
                    }
                }
                coursecat::resort_categories_cleanup($sortcoursesby !== false);
                if ($category === null && count($categoryids) === 1) {
                    // They're bulk sorting just a single category and they've not selected a category.
                    // Lets for convenience sake auto-select the category that has been resorted for them.
                    redirect(new moodle_url($PAGE->url, array('categoryid' => reset($categoryids))));
                }
            }
    }
    if ($redirectback) {
        if ($redirectmessage) {
            redirect($PAGE->url, $redirectmessage, 5);
        } else {
            redirect($PAGE->url);
        }
    }
}

if (!is_null($perpage)) {
    set_user_preference('coursecat_management_perpage', $perpage);
} else {
    $perpage = get_user_preferences('coursecat_management_perpage', $CFG->coursesperpage);
}
if ((int)$perpage != $perpage || $perpage < 2) {
    $perpage = $CFG->coursesperpage;
}

$categorysize = 4;
$coursesize = 4;
$detailssize = 4;
if ($viewmode === 'default' || $viewmode === 'combined') {
    if (isset($courseid)) {
        $class = 'columns-3';
    } else {
        $categorysize = 5;
        $coursesize = 7;
        $class = 'columns-2';
    }
} else if ($viewmode === 'categories') {
    $categorysize = 12;
    $class = 'columns-1';
} else if ($viewmode === 'courses') {
    if (isset($courseid)) {
        $coursesize = 6;
        $detailssize = 6;
        $class = 'columns-2';
    } else {
        $coursesize = 12;
        $class = 'columns-1';
    }
}
if ($viewmode === 'default' || $viewmode === 'combined') {
    $class .= ' viewmode-cobmined';
} else {
    $class .= ' viewmode-'.$viewmode;
}
if (($viewmode === 'default' || $viewmode === 'combined' || $viewmode === 'courses') && !empty($courseid)) {
    $class .= ' course-selected';
}

/* @var core_course_management_renderer|core_renderer $renderer */
$renderer = $PAGE->get_renderer('core_course', 'management');
$renderer->enhance_management_interface();

$displaycategorylisting = ($viewmode === 'default' || $viewmode === 'combined' || $viewmode === 'categories');
$displaycourselisting = ($viewmode === 'default' || $viewmode === 'combined' || $viewmode === 'courses');
$displaycoursedetail = (isset($courseid));

echo $renderer->header();

if (!$issearching) {
    echo $renderer->management_heading($strmanagement, $viewmode, $categoryid);
} else {
    echo $renderer->management_heading(new lang_string('searchresults'));
}

if (count($notificationspass) > 0) {
    echo $renderer->notification(join('<br />', $notificationspass), 'notifysuccess');
}
if (count($notificationsfail) > 0) {
    echo $renderer->notification(join('<br />', $notificationsfail));
}

// Start the management form.
echo $renderer->management_form_start();

echo $renderer->accessible_skipto_links($displaycategorylisting, $displaycourselisting, $displaycoursedetail);

echo $renderer->grid_start('course-category-listings', $class);

if ($displaycategorylisting) {
    echo $renderer->grid_column_start($categorysize, 'category-listing');
    echo $renderer->category_listing($category);
    echo $renderer->grid_column_end();
}
if ($displaycourselisting) {
    echo $renderer->grid_column_start($coursesize, 'course-listing');
    if (!$issearching) {
        echo $renderer->course_listing($category, $course, $page, $perpage, $viewmode);
    } else {
        list($courses, $coursescount, $coursestotal) =
            \core_course\management\helper::search_courses($search, $blocklist, $modulelist, $page, $perpage);
        echo $renderer->search_listing($courses, $coursestotal, $course, $page, $perpage, $search);
    }
    echo $renderer->grid_column_end();
    if ($displaycoursedetail) {
        echo $renderer->grid_column_start($detailssize, 'course-detail');
        echo $renderer->course_detail($course);
        echo $renderer->grid_column_end();
    }
}
echo $renderer->grid_end();

// End of the management form.
echo $renderer->management_form_end();
echo $renderer->course_search_form($search);

echo $renderer->footer();
