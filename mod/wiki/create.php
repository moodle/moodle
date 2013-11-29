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
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle. If not, see <http://www.gnu.org/licenses/>.

require_once('../../config.php');
require_once(dirname(__FILE__) . '/create_form.php');
require_once($CFG->dirroot . '/mod/wiki/lib.php');
require_once($CFG->dirroot . '/mod/wiki/locallib.php');
require_once($CFG->dirroot . '/mod/wiki/pagelib.php');

// this page accepts two actions: new and create
// 'new' action will display a form contains page title and page format
// selections
// 'create' action will create a new page in db, and redirect to
// page editing page.
$action = optional_param('action', 'new', PARAM_TEXT);
// The title of the new page, can be empty
$title = optional_param('title', get_string('newpage', 'wiki'), PARAM_TEXT);
$wid = optional_param('wid', 0, PARAM_INT);
$swid = optional_param('swid', 0, PARAM_INT);
$group = optional_param('group', 0, PARAM_INT);
$uid = optional_param('uid', 0, PARAM_INT);

// 'create' action must be submitted by moodle form
// so sesskey must be checked
if ($action == 'create') {
    if (!confirm_sesskey()) {
        print_error('invalidsesskey');
    }
}

if (!empty($swid)) {
    $subwiki = wiki_get_subwiki($swid);

    if (!$wiki = wiki_get_wiki($subwiki->wikiid)) {
        print_error('invalidwikiid', 'wiki');
    }

} else {
    $subwiki = wiki_get_subwiki_by_group($wid, $group, $uid);

    if (!$wiki = wiki_get_wiki($wid)) {
        print_error('invalidwikiid', 'wiki');
    }

}

if (!$cm = get_coursemodule_from_instance('wiki', $wiki->id)) {
    print_error('invalidcoursemoduleid', 'wiki');
}

$groups = new stdClass();
if (groups_get_activity_groupmode($cm)) {
    $modulecontext = context_module::instance($cm->id);
    $canaccessgroups = has_capability('moodle/site:accessallgroups', $modulecontext);
    if ($canaccessgroups) {
        $groups->availablegroups = groups_get_all_groups($cm->course);
        $allpart = new stdClass();
        $allpart->id = '0';
        $allpart->name = get_string('allparticipants');
        array_unshift($groups->availablegroups, $allpart);
    } else {
        $groups->availablegroups = groups_get_all_groups($cm->course, $USER->id);
    }
    if (!empty($group)) {
        $groups->currentgroup = $group;
    } else {
        $groups->currentgroup = groups_get_activity_group($cm);
    }
}

$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);

require_login($course, true, $cm);

$wikipage = new page_wiki_create($wiki, $subwiki, $cm);

if (!empty($swid)) {
    $wikipage->set_gid($subwiki->groupid);
    $wikipage->set_uid($subwiki->userid);
    $wikipage->set_swid($swid);
} else {
    $wikipage->set_wid($wid);
    $wikipage->set_gid($group);
    $wikipage->set_uid($uid);
}

$wikipage->set_availablegroups($groups);
$wikipage->set_title($title);

// set page action, and initialise moodle form
$wikipage->set_action($action);

switch ($action) {
case 'create':
    $newpageid = $wikipage->create_page($title);
    redirect($CFG->wwwroot . '/mod/wiki/edit.php?pageid='.$newpageid);
    break;
case 'new':
    // Go straight to editing if we know the page title and we're in force format mode.
    if ((int)$wiki->forceformat == 1 && $title != get_string('newpage', 'wiki')) {
        $newpageid = $wikipage->create_page($title);
        redirect($CFG->wwwroot . '/mod/wiki/edit.php?pageid='.$newpageid);
    } else {
        $wikipage->print_header();
        // Create a new page.
        $wikipage->print_content($title);
    }
    $wikipage->print_footer();
    break;
}
