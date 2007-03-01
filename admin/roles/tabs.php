<?php // $Id$

// Handles headers and tabs for the roles control at any level apart from SYSTEM level

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
            print_header("$SITE->shortname: $category->name", "$SITE->fullname: $strcourses",
                    "<a href=\"$CFG->wwwroot/course/index.php\">$strcategories</a> -> <a href=\"$CFG->wwwroot/course/category.php?id=$category->id\">$category->name</a> -> $straction", "", "", true);
            break;

        case CONTEXT_COURSE:
            if ($context->instanceid != SITEID) {
                $streditcoursesettings = get_string("editcoursesettings");
    
                $course = get_record('course', 'id', $context->instanceid);
                print_header($streditcoursesettings, $course->fullname,
                        "<a href=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</a> -> $straction");
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

            $strnav = "<a href=\"$CFG->wwwroot/mod/$module->name/view.php?id=$cm->id\">$instance->name</a> ->";
            $fullmodulename = get_string("modulename", $module->name);
            $streditinga = get_string("editinga", "moodle", $fullmodulename);
            $strmodulenameplural = get_string("modulenameplural", $module->name);

            if ($module->name == "label") {
                $focuscursor = "";
            } else {
                $focuscursor = "form.name";
            }

            $COURSE = $course;
            print_header_simple($streditinga, '',
                    "<a href=\"$CFG->wwwroot/mod/$module->name/index.php?id=$course->id\">$strmodulenameplural</a> ->
                    $strnav <a href=\"$CFG->wwwroot/course/mod.php?update=$cm->id&amp;sesskey=".sesskey()."\">$streditinga</a> -> $straction", $focuscursor, "", false);

            break;

        case CONTEXT_BLOCK:
            if ($blockinstance = get_record('block_instance', 'id', $context->instanceid)) {
                if ($block = get_record('block', 'id', $blockinstance->blockid)) {
                    $blockname = print_context_name($context);
                    $navigation = $blockname. ' -> '.$straction;

                    switch ($blockinstance->pagetype) {
                        case 'course-view':
                            if ($course = get_record('course', 'id', $blockinstance->pageid)) {
                                if ($course->id != SITEID) {
                                    $navigation = "<a href=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</a> -> $navigation";
                                }
                                print_header("$straction: $blockname", $course->fullname, $navigation);
                            }
                            break;

                        case 'blog-view':
                            $strblogs = get_string('blogs','blog');
                            $navigation = '<a href="'.$CFG->wwwroot.'/blog/index.php">'.
                                $strblogs.'</a> -> '.$navigation;
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

    if (isset($tabsmode)) {

        if (!isset($assignableroles)) {
            $assignableroles = get_assignable_roles($context);
        }
        if (!isset($overridableroles)) {
            $overridableroles = get_overridable_roles($context);
        }

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

    if (!empty($secondrow)) {
        $tabs = array($toprow, $secondrow);
    } else {
        $tabs = array($toprow);
    }

    print_tabs($tabs, $currenttab, $inactive, $activetwo);
}

?>
