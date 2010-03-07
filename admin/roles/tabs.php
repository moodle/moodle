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
 * Handles headers and tabs for the roles control at any level apart from SYSTEM level
 * We assume that $currenttab, $assignableroles and $overridableroles are defined
 *
 * @package    moodlecore
 * @subpackage role
 * @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

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

if ($currenttab != 'update') {
    switch ($context->contextlevel) {

        case CONTEXT_SYSTEM:
            $stradministration = get_string('administration');
            if (empty($title)) {
                $title = $SITE->fullname;
            }
            $PAGE->navbar->add($stradministration, new moodle_url('/admin/'), navigation_node::TYPE_SETTING);
            $PAGE->navbar->add($straction);
            $PAGE->set_title($title);
            $PAGE->set_heading($SITE->fullname);
            break;

        case CONTEXT_USER:
            break;

        case CONTEXT_COURSECAT:
            $category = $DB->get_record('course_categories', array('id'=>$context->instanceid));
            $strcategories = get_string("categories");
            $strcategory = get_string("category");
            $strcourses = get_string("courses");

            if (empty($title)) {
                $title = "$SITE->shortname: $category->name";
            }

            $PAGE->navbar->add($strcategories, new moodle_url('/course/index.php'), navigation_node::TYPE_SETTING);
            $PAGE->navbar->add($category->name, new moodle_url('/course/category.php', array('id'=>$category->id)), navigation_node::TYPE_SETTING);
            $PAGE->navbar->add(get_string("roles"));
            $PAGE->set_title($title);
            $PAGE->set_heading("$SITE->fullname: $strcourses");
            break;

        case CONTEXT_COURSE:
            if ($context->instanceid != SITEID) {
                $course = $DB->get_record('course', array('id'=>$context->instanceid));

                if (empty($title)) {
                    $title = get_string("editcoursesettings");
                }
                $roleslink = new moodle_url("$CFG->wwwroot/$CFG->admin/roles/assign.php", array('contextid'=>$context->id));
                $PAGE->navbar->add(get_string('roles'), $roleslink, navigation_node::TYPE_SETTING);
                $PAGE->set_title($title);
                $PAGE->set_heading($course->fullname);
            }
            break;

        case CONTEXT_MODULE:
            if (!$cm = get_coursemodule_from_id('', $context->instanceid)) {
                print_error('invalidcoursemodule', 'error');
            }
            if (!$course = $DB->get_record('course', array('id' => $cm->course))) {
                print_error('invalidcourse');
            }

            $PAGE->navigation->add(get_string('roles'));

            if (empty($title)) {
                $title = get_string("editinga", "moodle", $fullmodulename);
            }
            $PAGE->set_title($title);
            $PAGE->set_cacheable(false);
            break;

        case CONTEXT_BLOCK:
            if ($blockinstance = $DB->get_record('block_instances', array('id' => $context->instanceid))) {
                $blockname = print_context_name($context);

                $PAGE->navbar->add($blockname);
                $PAGE->navbar->add($straction);
                $PAGE->set_title("$straction: $blockname");
                $PAGE->set_heading($PAGE->course->fullname);
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
$secondrow = array();

$permissionsrow = array();

if ($context->contextlevel != CONTEXT_SYSTEM) {    // Print tabs for anything except SYSTEM context

    if ($context->contextlevel == CONTEXT_MODULE) {  // Only show update button if module
        $toprow[] = new tabobject('update', $CFG->wwwroot.'/course/mod.php?update='.
                        $context->instanceid.'&amp;return=true&amp;sesskey='.sesskey(), get_string('settings'));
    }

    if (!empty($assignableroles) || $currenttab=='assign') {
        $toprow[] = new tabobject('assign',
                new moodle_url('/admin/roles/assign.php', array('contextid'=>$context->id)),
                get_string('localroles', 'role'), '', true);
    }

    if (has_capability('moodle/role:review', $context) or !empty($overridableroles)) {
        $permissionsrow['permissions'] = new tabobject('permissions',
                new moodle_url('/admin/roles/permissions.php', array('contextid'=>$context->id)),
                get_string('permissions', 'role'), '', true);
    }

    if (has_any_capability(array('moodle/role:assign', 'moodle/role:safeoverride',
            'moodle/role:override', 'moodle/role:assign'), $context)) {
        $permissionsrow['check'] = new tabobject('check',
                new moodle_url('/admin/roles/check.php', array('contextid'=>$context->id)),
                get_string('checkpermissions', 'role'));
    }

    if ($permissionsrow) {
        $firstpermissionrow = reset($permissionsrow);
        $toprow[] = new tabobject('toppermissions', $firstpermissionrow->link, get_string('permissions', 'role'), '', true);
        if (!empty($permissionsrow[$currenttab])) {
            $secondrow = array_values($permissionsrow);
            $inactive  = array('toppermissions');
            $activetwo = array('toppermissions');
        }
    }

    if (!empty($availablefilters)) {
        $toprow[] = new tabobject('filters',
                new moodle_url('/filter/manage.php', array('contextid'=>$context->id)),
                get_string('filters', 'admin'));
    }
}
unset($permissionsrow);

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
                include($CFG->dirroot . '/' . $extratab);
            }
        }
    }
}

$inactive[] = $currenttab;

$tabs = array($toprow);

/// If there are any secondrow defined, let's introduce it
if (!empty($secondrow)) {
    $tabs[] = $secondrow;
}

print_tabs($tabs, $currenttab, $inactive, $activetwo);

