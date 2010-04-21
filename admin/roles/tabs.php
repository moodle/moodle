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

$toprow = array();
$inactive = array();
$activetwo = array();
$secondrow = array();

$permissionsrow = array();

if ($context->contextlevel != CONTEXT_SYSTEM) {    // Print tabs for anything except SYSTEM context

    if ($context->contextlevel == CONTEXT_MODULE) {  // Only show update button if module
        $url = new moodle_url('/course/mod.php', array('update'=>$context->instanceid, 'return'=>'true', 'sesskey'=>sesskey()));
        $toprow[] = new tabobject('update', $url, get_string('settings'));
    }

    if (!empty($assignableroles) || $currenttab=='assign') {
        $url = new moodle_url('/admin/roles/assign.php', array('contextid'=>$context->id));
        $toprow[] = new tabobject('assign', $url, get_string('localroles', 'role'), '', true);
    }

    if (has_capability('moodle/role:review', $context) or !empty($overridableroles)) {
        $url = new moodle_url('/admin/roles/permissions.php', array('contextid'=>$context->id));
        $permissionsrow['permissions'] = new tabobject('permissions', $url, get_string('permissions', 'role'), '', true);
    }

    if (has_any_capability(array('moodle/role:assign', 'moodle/role:safeoverride', 'moodle/role:override', 'moodle/role:assign'), $context)) {
        $url = new moodle_url('/admin/roles/check.php', array('contextid'=>$context->id));
        $permissionsrow['check'] = new tabobject('check', $url, get_string('checkpermissions', 'role'));
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
        $url = new moodle_url('/filter/manage.php', array('contextid'=>$context->id));
        $toprow[] = new tabobject('filters', $url, get_string('filters', 'admin'));
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

