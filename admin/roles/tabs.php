<?php // $Id$

// Handles headers and tabs for the roles control at any level apart from SYSTEM level
// We also assume that $currenttab, $assignableroles and $overridableroles are defined

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

$navlinks = array();
if ($currenttab != 'update') {
    switch ($context->contextlevel) {

        case CONTEXT_SYSTEM:
            $stradministration = get_string('administration');
            print_header($SITE->fullname, "$SITE->fullname","<a href=\"../index.php\">$stradministration</a> -> $straction");
            break;

        case CONTEXT_PERSONAL:
            break;

        case CONTEXT_USER:
            print_header();
            break;

        case CONTEXT_COURSECAT:
            $category = get_record('course_categories', 'id', $context->instanceid);
            $strcategories = get_string("categories");
            $strcategory = get_string("category");
            $strcourses = get_string("courses");

            $navlinks[] = array('name' => $strcategories,
                                'link' => "$CFG->wwwroot/course/index.php",
                                'type' => 'misc');
            $navlinks[] = array('name' => $category->name,
                                'link' => "$CFG->wwwroot/course/category.php?id=$category->id",
                                'type' => 'misc');
            $navigation = build_navigation($navlinks);

            print_header("$SITE->shortname: $category->name", "$SITE->fullname: $strcourses", $navigation, "", "", true);
            break;

        case CONTEXT_COURSE:
            if ($context->instanceid != SITEID) {
                $streditcoursesettings = get_string("editcoursesettings");

                $course = get_record('course', 'id', $context->instanceid);

                require_login($course);
                $navlinks[] = array('name' => $course->shortname,
                                    'link' => "$CFG->wwwroot/course/view.php?id=$course->id",
                                    'type' => 'misc');
                $navigation = build_navigation($navlinks);
                print_header($streditcoursesettings, $course->fullname, $navigation);
            }
            break;

        case CONTEXT_GROUP:
            break;

        case CONTEXT_MODULE:
            // get module type?
            if (!$cm = get_record('course_modules','id',$context->instanceid)) {
                error('Bad course module ID');
            }
            if (!$module = get_record('modules','id',$cm->module)) {  //$module->name;
                error('Bad module ID');
            }
            if (!$course = get_record('course','id',$cm->course)) {
                error('Bad course ID');
            }
            if (!$instance = get_record($module->name, 'id', $cm->instance)) {
                error("The required instance of this module doesn't exist");
            }

            require_login($course);

            $fullmodulename      = get_string("modulename", $module->name);
            $streditinga         = get_string("editinga", "moodle", $fullmodulename);
            $strmodulenameplural = get_string("modulenameplural", $module->name);

            if ($module->name == "label") {
                $focuscursor = "";
            } else {
                $focuscursor = "form.name";
            }

            $navlinks[] = array('name' => $strmodulenameplural,
                                'link' => "$CFG->wwwroot/mod/$module->name/index.php?id=$course->id",
                                'type' => 'misc');

            $navlinks[] = array('name' => $instance->name,
                                'link' => "$CFG->wwwroot/mod/$module->name/view.php?id=$cm->id";
                                'type' => 'misc');

            $navlinks[] = array('name' => $streditinga,
                                'link' => "$CFG->wwwroot/course/mod.php?update=$cm->id&amp;sesskey=".sesskey(),
                                'type' => 'misc');

            $navigation = build_navigation($navlinks);

            print_header_simple($streditinga, '', $navigation, $focuscursor, "", false);

            break;

        case CONTEXT_BLOCK:
            if ($blockinstance = get_record('block_instance', 'id', $context->instanceid)) {
                if ($block = get_record('block', 'id', $blockinstance->blockid)) {
                    $blockname = print_context_name($context);

                    // Prepare the last part of the breadcrumbs first
                    $navlinks[98] = array('name' => $blockname, 'link' => null, 'type' => 'misc');
                    $navlinks[99] = array('name' => $straction, 'link' => null, 'type' => 'misc');

                    switch ($blockinstance->pagetype) {
                        case 'course-view':
                            if ($course = get_record('course', 'id', $blockinstance->pageid)) {

                                require_login($course);

                                if ($course->id != SITEID) {
                                    $navlinks[0] = array('name' => $course->shortname,
                                                        'link' => "$CFG->wwwroot/course/view.php?id=$course->id",
                                                        'type' => 'misc');
                                }
                                $navigation = build_navigation($navlinks);
                                print_header("$straction: $blockname", $course->fullname, $navigation);
                            }
                            break;

                        case 'blog-view':
                            $strblogs = get_string('blogs','blog');
                            $navlinks[0] = array('name' => $strblogs,
                                                 'link' => $CFG->wwwroot.'/blog/index.php',
                                                 'type' => 'misc');
                            $navigation = build_navigation($navlinks);
                            print_header("$straction: $strblogs", $SITE->fullname, $navigation);
                            break;

                        default:
                            print_header("$straction: $blockname", $SITE->fullname, $navigation);
                            break;
                    }
                }
            }
            break;

        default:
            error ('This is an unknown context (' . $context->contextlevel . ') in admin/roles/tabs.php!');
            return false;

    }
}


if ($context->contextlevel != CONTEXT_SYSTEM) {    // Print tabs for anything except SYSTEM context

    if ($context->contextlevel == CONTEXT_MODULE) { // only show update button if module?

        $toprow[] = new tabobject('update', $CFG->wwwroot.'/course/mod.php?update='.
                       $context->instanceid.'&amp;return=true&amp;sesskey='.sesskey(), get_string('update'));

    }

    $toprow[] = new tabobject('roles', $CFG->wwwroot.'/'.$CFG->admin.'/roles/assign.php?contextid='.
                               $context->id, get_string('roles'));

    if (!empty($tabsmode)) {

        if (!empty($assignableroles)) {
            $secondrow[] = new tabobject('assign',
                                         $CFG->wwwroot.'/'.$CFG->admin.'/roles/assign.php?contextid='.$context->id,
                                         get_string('assignroles', 'role'),
                                         get_string('showallroles', 'role'),
                                         true);
        }

        if (!empty($overridableroles)) {
            $secondrow[] = new tabobject('override',
                               $CFG->wwwroot.'/'.$CFG->admin.'/roles/override.php?contextid='.$context->id,
                               get_string('overrideroles', 'role'),
                               get_string('showallroles', 'role'),
                               true);
        }

        $inactive[] = 'roles';
        $activetwo = array('roles');
        $currenttab = $tabsmode;

    } else {
        $inactive[] = '';
        $activetwo = array();
    }

/// Here other core tabs should go (always calling tabs.php files)
/// All the logic to decide what to show must be self-cointained in the tabs file
/// ej.:
/// include_once($CFG->dirroot . '/grades/tabs.php');

/// Finally, we support adding some 'on-the-fly' tabs here
/// All the logic to decide what to show must be self-cointained in the tabs file
    if (isset($CFG->extratabs) && !empty($CFG->extratabs)) {
        if ($extratabs = explode(',', $CFG->extratabs)) {
            asort($extratabs);
            foreach($extratabs as $extratab) {
            /// Each extra tab mus be one $CFG->dirroot relative file
                if (file_exists($CFG->dirroot . '/' . $extratab)) {
                    include_once($CFG->dirroot . '/' . $extratab);
                }
            }
        }
    }

    if (!empty($secondrow)) {
        $tabs = array($toprow, $secondrow);
    } else {
        $tabs = array($toprow);
    }

    print_tabs($tabs, $currenttab, $inactive, $activetwo);
}

?>
