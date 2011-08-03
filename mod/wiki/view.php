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

/**
 * This file contains all necessary code to view a wiki page
 *
 * @package mod-wiki-2.0
 * @copyrigth 2009 Marc Alier, Jordi Piguillem marc.alier@upc.edu
 * @copyrigth 2009 Universitat Politecnica de Catalunya http://www.upc.edu
 *
 * @author Jordi Piguillem
 * @author Marc Alier
 * @author David Jimenez
 * @author Josep Arus
 * @author Kenneth Riba
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->dirroot . '/mod/wiki/lib.php');
require_once($CFG->dirroot . '/mod/wiki/locallib.php');
require_once($CFG->dirroot . '/mod/wiki/pagelib.php');

$id = optional_param('id', 0, PARAM_INT); // Course Module ID

$pageid = optional_param('pageid', 0, PARAM_INT); // Page ID

$wid = optional_param('wid', 0, PARAM_INT); // Wiki ID
$title = optional_param('title', '', PARAM_TEXT); // Page Title
$currentgroup = optional_param('group', 0, PARAM_INT); // Group ID
$userid = optional_param('uid', 0, PARAM_INT); // User ID
$groupanduser = optional_param('groupanduser', 0, PARAM_TEXT);

$edit = optional_param('edit', -1, PARAM_BOOL);

$action = optional_param('action', '', PARAM_ALPHA);
$swid = optional_param('swid', 0, PARAM_INT); // Subwiki ID

/*
 * Case 0:
 *
 * User that comes from a course. First wiki page must be shown
 *
 * URL params: id -> course module id
 *
 */
if ($id) {
    // Cheacking course module instance
    if (!$cm = get_coursemodule_from_id('wiki', $id)) {
        print_error('invalidcoursemodule');
    }

    // Checking course instance
    $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);

    // Checking wiki instance
    if (!$wiki = wiki_get_wiki($cm->instance)) {
        print_error('incorrectwikiid', 'wiki');
    }
    $PAGE->set_cm($cm);

    // Getting the subwiki corresponding to that wiki, group and user.
    //
    // Also setting the page if it exists or getting the first page title form
    // that wiki

    // Getting current group id
    $currentgroup = groups_get_activity_group($cm);
    $currentgroup = !empty($currentgroup) ? $currentgroup : 0;
    // Getting current user id
    if ($wiki->wikimode == 'individual') {
        $userid = $USER->id;
    } else {
        $userid = 0;
    }

    // Getting subwiki. If it does not exists, redirecting to create page
    if (!$subwiki = wiki_get_subwiki_by_group($wiki->id, $currentgroup, $userid)) {
        $params = array('wid' => $wiki->id, 'gid' => $currentgroup, 'uid' => $userid, 'title' => $wiki->firstpagetitle);
        $url = new moodle_url('/mod/wiki/create.php', $params);
        redirect($url);
    }

    // Getting first page. If it does not exists, redirecting to create page
    if (!$page = wiki_get_first_page($subwiki->id, $wiki)) {
        $params = array('swid'=>$subwiki->id, 'title'=>$wiki->firstpagetitle);
        $url = new moodle_url('/mod/wiki/create.php', $params);
        redirect($url);
    }

    /*
     * Case 1:
     *
     * A user wants to see a page.
     *
     * URL Params: pageid -> page id
     *
     */
} elseif ($pageid) {

    // Checking page instance
    if (!$page = wiki_get_page($pageid)) {
        print_error('incorrectpageid', 'wiki');
    }

    // Checking subwiki
    if (!$subwiki = wiki_get_subwiki($page->subwikiid)) {
        print_error('incorrectsubwikiid', 'wiki');
    }

    // Checking wiki instance of that subwiki
    if (!$wiki = wiki_get_wiki($subwiki->wikiid)) {
        print_error('incorrectwikiid', 'wiki');
    }

    // Checking course module instance
    if (!$cm = get_coursemodule_from_instance("wiki", $subwiki->wikiid)) {
        print_error('invalidcoursemodule');
    }

    // Checking course instance
    $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);

    /*
     * Case 2:
     *
     * Trying to read a page from another group or user
     *
     * Page can exists or not.
     *  * If it exists, page must be shown
     *  * If it does not exists, system must ask for its creation
     *
     * URL params: wid -> subwiki id (required)
     *             title -> a page title (required)
     *             group -> group id (optional)
     *             uid -> user id (optional)
     *             groupanduser -> (optional)
     */
} elseif ($wid && $title) {

    // Setting wiki instance
    if (!$wiki = wiki_get_wiki($wid)) {
        print_error('incorrectwikiid', 'wiki');
    }

    // Checking course module
    if (!$cm = get_coursemodule_from_instance("wiki", $wiki->id)) {
        print_error('invalidcoursemodule');
    }

    // Checking course instance
    if (!$course = $DB->get_record("course", array("id" => $cm->course))) {
        print_error('coursemisconf');
    }

    $groupmode = groups_get_activity_groupmode($cm);
    if (empty($currentgroup)) {
        $currentgroup = groups_get_activity_group($cm);
        $currentgroup = !empty($currentgroup) ? $currentgroup : 0;
    }

    if ($wiki->wikimode == 'individual' && ($groupmode == SEPARATEGROUPS || $groupmode == VISIBLEGROUPS)) {
        list($gid, $uid) = explode('-', $groupanduser);
    } else if ($wiki->wikimode == 'individual') {
        $gid = 0;
        $uid = $userid;
    } else if ($groupmode == NOGROUPS) {
        $gid = 0;
        $uid = 0;
    } else {
        $gid = $currentgroup;
        $uid = 0;
    }

    // Getting subwiki instance. If it does not exists, redirect to create page
    if (!$subwiki = wiki_get_subwiki_by_group($wiki->id, $gid, $uid)) {
        $params = array('wid' => $wiki->id, 'gid' => $gid, 'uid' => $uid, 'title' => $title);
        $url = new moodle_url('/mod/wiki/create.php', $params);
        redirect($url);
    }

    // Checking is there is a page with this title. If it does not exists, redirect to first page
    if (!$page = wiki_get_page_by_title($subwiki->id, $title)) {
        $params = array('wid' => $wiki->id, 'gid' => $gid, 'uid' => $uid, 'title' => $wiki->firstpagetitle);
        $url = new moodle_url('/mod/wiki/view.php', $params);
        redirect($url);
    }

    //    /*
    //     * Case 3:
    //     *
    //     * A user switches group when is 'reading' a non-existent page.
    //     *
    //     * URL Params: wid -> wiki id
    //     *             title -> page title
    //     *             currentgroup -> group id
    //     *
    //     */
    //} elseif ($wid && $title && $currentgroup) {
    //
    //    // Checking wiki instance
    //    if (!$wiki = wiki_get_wiki($wid)) {
    //        print_error('incorrectwikiid', 'wiki');
    //    }
    //
    //    // Checking subwiki instance
    //    // @TODO: Fix call to wiki_get_subwiki_by_group
    //    if (!$currentgroup = groups_get_activity_group($cm)){
    //        $currentgroup = 0;
    //    }
    //    if (!$subwiki = wiki_get_subwiki_by_group($wid, $currentgroup)) {
    //        print_error('incorrectsubwikiid', 'wiki');
    //    }
    //
    //    // Checking page instance
    //    if ($page = wiki_get_page_by_title($subwiki->id, $title)) {
    //        unset($title);
    //    }
    //
    //    // Checking course instance
    //    $course = $DB->get_record('course', array('id'=>$cm->course), '*', MUST_EXIST);
    //
    //    // Checking course module instance
    //    if (!$cm = get_coursemodule_from_instance("wiki", $wiki->id, $course->id)) {
    //        print_error('invalidcoursemodule');
    //    }
    //
    //    $subwiki = null;
    //    $page = null;
    //
    //    /*
    //     * Case 4:
    //     *
    //     * Error. No more options
    //     */
} else {
    print_error('incorrectparameters');
}
require_login($course, true, $cm);

$context = get_context_instance(CONTEXT_MODULE, $cm->id);
require_capability('mod/wiki:viewpage', $context);

add_to_log($course->id, 'wiki', 'view', 'view.php?id=' . $cm->id, $wiki->id);

// Update 'viewed' state if required by completion system
require_once($CFG->libdir . '/completionlib.php');
$completion = new completion_info($course);
$completion->set_module_viewed($cm);

if (($edit != - 1) and $PAGE->user_allowed_editing()) {
    $USER->editing = $edit;
}

$wikipage = new page_wiki_view($wiki, $subwiki, $cm);

/*The following piece of code is used in order
 * to perform set_url correctly. It is necessary in order
 * to make page_wiki_view class know that this page
 * has been called via its id.
 */
if ($id) {
    $wikipage->set_coursemodule($id);
}

$wikipage->set_gid($currentgroup);
$wikipage->set_page($page);

$wikipage->print_header();
$wikipage->print_content();

$wikipage->print_footer();
