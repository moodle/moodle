<?php  // $Id$

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.org                                            //
//                                                                       //
// Copyright (C) 1999 onwards Martin Dougiamas  http://dougiamas.com     //
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

/**
 * Handles headers and tabs for the roles control at any level apart from SYSTEM level
 * We assume that $currenttab, $assignableroles and $overridableroles are defined
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package roles
 *//** */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.'); // It must be included from a Moodle page
}

if (!isset($availablefilters)) {
    $availablefilters  = array();
    if (in_array($context->contextlevel, array(CONTEXT_COURSECAT, CONTEXT_COURSE, CONTEXT_MODULE)) &&
            !($context->contextlevel == CONTEXT_COURSE && $context->instanceid == SITEID) &&
            has_capability('moodle/filter:manage', $context)) {
        $availablefilters = filter_get_available_in_context($context);
    }
}

$navlinks = array();
if ($currenttab != 'update') {
    switch ($context->contextlevel) {

        case CONTEXT_SYSTEM:
            $stradministration = get_string('administration');
            $navlinks[] = array('name' => $stradministration, 'link' => '../index.php', 'type' => 'misc');
            $navlinks[] = array('name' => $straction, 'link' => null, 'type' => 'misc');
            $navigation = build_navigation($navlinks);
            if (empty($title)) {
                $title = $SITE->fullname;
            }
            print_header($title, "$SITE->fullname", $navigation);
            break;

        case CONTEXT_USER:
            print_header();
            break;

        case CONTEXT_COURSECAT:
            $category = $DB->get_record('course_categories', array('id'=>$context->instanceid));
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

            if (empty($title)) {
                $title = "$SITE->shortname: $category->name";
            }
            print_header($title, "$SITE->fullname: $strcourses", $navigation, "", "", true);
            break;

        case CONTEXT_COURSE:
            if ($context->instanceid != SITEID) {
                $course = $DB->get_record('course', array('id'=>$context->instanceid));

                require_login($course);
                $navlinks[] = array('name' => get_string('roles'),
                                    'link' => "$CFG->wwwroot/$CFG->admin/roles/assign.php?contextid=$context->id",
                                    'type' => 'misc');
                $navigation = build_navigation($navlinks);
                if (empty($title)) {
                    $title = get_string("editcoursesettings");
                }
                print_header($title, $course->fullname, $navigation);
            }
            break;

        case CONTEXT_MODULE:
            if (!$cm = get_coursemodule_from_id('', $context->instanceid)) {
                print_error('invalidcoursemodule', 'error');
            }
            if (!$course = $DB->get_record('course', array('id' => $cm->course))) {
                print_error('invalidcourse');
            }

            require_login($course);
            $navigation = build_navigation(get_string('roles'), $cm);

            if (empty($title)) {
                $title = get_string("editinga", "moodle", $fullmodulename);
            }
            print_header_simple($title, '', $navigation, '', '', false);

            break;

        case CONTEXT_BLOCK:
            if ($blockinstance = $DB->get_record('block_instances', array('id' => $context->instanceid))) {
                $blockname = print_context_name($context);

                $parentcontext = get_context_instance_by_id($blockinstance->parentcontextid);
                $navlinks[] = array('name' => $blockname, 'link' => null, 'type' => 'misc');
                $navlinks[] = array('name' => $straction, 'link' => null, 'type' => 'misc');
                switch ($parentcontext->contextlevel) {
                    case CONTEXT_SYSTEM:
                        break;

                    case CONTEXT_COURSECAT:
                        $PAGE->set_category_by_id($parentcontext->instanceid);
                        break;

                    case CONTEXT_COURSE:
                        require_login($parentcontext->instanceid);
                        break;

                    case CONTEXT_MODULE:
                        $cm = get_coursemodule_from_id('', $parentcontext->instanceid);
                        require_login($parentcontext->instanceid, false, $cm);
                        break;

                    case CONTEXT_USER:
                        break;

                    default:
                        throw new invalid_state_exception('Block context ' . $blockname .
                                ' has parent context with an improper contextlevel ' . $parentcontext->contextlevel);


                }
                print_header("$straction: $blockname", $PAGE->course->fullname, build_navigation($navlinks));
            }
            break;

        default:
            print_error('unknowncontext');
            return false;

    }
}


$toprow = array();
$inactive = array();
$activetwo = array();


if ($context->contextlevel != CONTEXT_SYSTEM) {    // Print tabs for anything except SYSTEM context

    if ($context->contextlevel == CONTEXT_MODULE) {  // Only show update button if module
        $toprow[] = new tabobject('update', $CFG->wwwroot.'/course/mod.php?update='.
                        $context->instanceid.'&amp;return=true&amp;sesskey='.sesskey(), get_string('settings'));
    }

    if (!empty($assignableroles) || $currenttab=='assign') {
        $toprow[] = new tabobject('assign',
                $CFG->wwwroot.'/'.$CFG->admin.'/roles/assign.php?contextid='.$context->id,
                get_string('localroles', 'role'), '', true);
    }

    if (!empty($overridableroles)) {
        $toprow[] = new tabobject('override',
                $CFG->wwwroot.'/'.$CFG->admin.'/roles/override.php?contextid='.$context->id,
                get_string('overridepermissions', 'role'), '', true);
    }

    if (has_any_capability(array('moodle/role:assign', 'moodle/role:safeoverride',
            'moodle/role:override', 'moodle/role:assign'), $context)) {
        $toprow[] = new tabobject('check',
                $CFG->wwwroot.'/'.$CFG->admin.'/roles/check.php?contextid='.$context->id,
                get_string('checkpermissions', 'role'));
    }

    if (!empty($availablefilters)) {
        $toprow[] = new tabobject('filters',
                $CFG->wwwroot.'/filter/manage.php?contextid=' . $context->id,
                get_string('filters', 'admin'));
    }
}

/// Here other core tabs should go (always calling tabs.php files)
/// All the logic to decide what to show must be self-contained in the tabs file
/// eg:
/// include_once($CFG->dirroot . '/grades/tabs.php');

/// Finally, we support adding some 'on-the-fly' tabs here
/// All the logic to decide what to show must be self-cointained in the tabs file
    if (!empty($CFG->extratabs)) {
        if ($extratabs = explode(',', $CFG->extratabs)) {
            asort($extratabs);
            foreach($extratabs as $extratab) {
            /// Each extra tab must be one $CFG->dirroot relative file
                if (file_exists($CFG->dirroot . '/' . $extratab)) {
                    include_once($CFG->dirroot . '/' . $extratab);
                }
            }
        }
    }

    $inactive[] = $currenttab;

    $tabs = array($toprow);

/// If there are any secondrow defined, let's introduce it
    if (isset($secondrow) && is_array($secondrow) && !empty($secondrow)) {
        $tabs[] = $secondrow;
    }

    print_tabs($tabs, $currenttab, $inactive, $activetwo);


?>
