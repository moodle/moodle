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
 * Allows the admin to create, delete and rename course categories rearrange courses
 *
 * This script has been deprecated since Moodle 2.6.
 * Please update your links as
 *
 * @deprecated
 * @todo remove in 2.7 MDL-41502
 * @package   core_course
 * @copyright 2013 Marina Glancy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("../config.php");
require_once($CFG->dirroot.'/course/lib.php');
require_once($CFG->libdir.'/coursecatlib.php');

$id = optional_param('categoryid', 0, PARAM_INT); // Category id.
$page = optional_param('page', 0, PARAM_INT); // Which page to show.
$perpage = optional_param('perpage', $CFG->coursesperpage, PARAM_INT); // How many per page.
$search = optional_param('search', '', PARAM_RAW);  // Search words.
$blocklist = optional_param('blocklist', 0, PARAM_INT);
$modulelist = optional_param('modulelist', '', PARAM_PLUGIN);

debugging('This script has been deprecated and will be removed in the future. Please update any bookmarks you have.',
    DEBUG_DEVELOPER);

// Look for legacy actions.
// If there are any we're going to make and equivalent request to management.php.
$sesskey = optional_param('sesskey', null, PARAM_RAW);
if ($sesskey !== null && confirm_sesskey($sesskey)) {
    // Actions to manage categories.
    $deletecat = optional_param('deletecat', 0, PARAM_INT);
    $hidecat = optional_param('hidecat', 0, PARAM_INT);
    $showcat = optional_param('showcat', 0, PARAM_INT);
    $movecat = optional_param('movecat', 0, PARAM_INT);
    $movetocat = optional_param('movetocat', -1, PARAM_INT);
    $moveupcat = optional_param('moveupcat', 0, PARAM_INT);
    $movedowncat = optional_param('movedowncat', 0, PARAM_INT);

    // Actions to manage courses.
    $hide = optional_param('hide', 0, PARAM_INT);
    $show = optional_param('show', 0, PARAM_INT);
    $moveup = optional_param('moveup', 0, PARAM_INT);
    $movedown = optional_param('movedown', 0, PARAM_INT);
    $moveto = optional_param('moveto', 0, PARAM_INT);
    $resort = optional_param('resort', 0, PARAM_BOOL);

    $murl = new moodle_url('/course/management.php', array('sesskey' => sesskey()));

    // Process any category actions.
    if (!empty($deletecat)) {
        // Redirect to the new management script.
        redirect(new moodle_url($murl, array('categoryid' => $deletecat, 'action' => 'deletecategory')));
    }

    if (!empty($movecat) and $movetocat >= 0) {
        // Redirect to the new management script.
        redirect(new moodle_url($murl, array(
            'action' => 'bulkaction',
            'bulkmovecategories' => true,
            'movecategoriesto' => $movetocat,
            'bcat[]' => $movecat
        )));
    }

    // Hide or show a category.
    if ($hidecat) {
        // Redirect to the new management script.
        redirect(new moodle_url($murl, array('categoryid' => $hidecat, 'action' => 'hidecategory')));
    } else if ($showcat) {
        // Redirect to the new management script.
        redirect(new moodle_url($murl, array('categoryid' => $showcat, 'action' => 'showcategory')));
    }

    if (!empty($moveupcat) or !empty($movedowncat)) {
        // Redirect to the new management script.
        if (!empty($moveupcat)) {
            redirect(new moodle_url($murl, array('categoryid' => $moveupcat, 'action' => 'movecategoryup')));
        } else {
            redirect(new moodle_url($murl, array('categoryid' => $movedowncat, 'action' => 'movecategorydown')));
        }
    }

    if ($resort && $id) {
        // Redirect to the new management script.
        redirect(new moodle_url($murl, array('categoryid' => $id, 'action' => 'resortcategories', 'resort' => 'name')));
    }

    if (!empty($moveto) && ($data = data_submitted())) {
        // Redirect to the new management script.
        $courses = array();
        foreach ($data as $key => $value) {
            if (preg_match('/^c\d+$/', $key)) {
                $courses[] = substr($key, 1);
            }
        }
        redirect(new moodle_url($murl, array(
            'action' => 'bulkaction',
            'bulkmovecourses' => true,
            'movecoursesto' => $moveto,
            'bc' => $courses
        )));
    }

    if (!empty($hide) or !empty($show)) {
        // Redirect to the new management script.
        if (!empty($hide)) {
            redirect(new moodle_url($murl, array('courseid' => $hide, 'action' => 'hidecourse')));
        } else {
            redirect(new moodle_url($murl, array('courseid' => $show, 'action' => 'showcourse')));
        }
    }

    if (!empty($moveup) or !empty($movedown)) {
        // Redirect to the new management script.
        if (!empty($moveup)) {
            redirect(new moodle_url($murl, array('courseid' => $moveup, 'action' => 'movecourseup')));
        } else {
            redirect(new moodle_url($murl, array('courseid' => $movedown, 'action' => 'movecoursedown')));
        }
    }
}

// Now check if its a search or not. If its not we'll head to the new management page.
$url = new moodle_url('/course/management.php');
if ($id !== 0) {
    // We've got an ID it can't be a search.
    $url->param('categoryid', $id);
} else {
    // No $id, perhaps its a search.
    if ($search !== '') {
        $url->param('search', $search);
    }
    if ($blocklist !== 0) {
        $url->param('blocklist', $blocklist);
    }
    if ($modulelist !== '') {
        $url->param('modulelist', $modulelist);
    }
}
redirect($url);
