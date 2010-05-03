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

$currentgroup = optional_param('group', 0, PARAM_INT); // Group ID
$userid = optional_param('userid', 0, PARAM_INT); // User ID
$title  = optional_param('title', '', PARAM_TEXT); // Page Title
$action = optional_param('action', '', PARAM_ALPHA);
$id     = optional_param('id', 0, PARAM_INT); // Course Module ID
$swid   = optional_param('swid', 0, PARAM_INT); // Subwiki ID
$pageid = optional_param('pageid', 0, PARAM_INT); // Page ID
$wid    = optional_param('wid', 0, PARAM_INT); // Wiki ID
$edit   = optional_param('edit', -1, PARAM_BOOL);

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
    if (!$course = get_course_by_id($cm->course)) {
        print_error('coursemisconf');
    }

    // Checking wiki instance
    if (!$wiki = wiki_get_wiki($cm->instance)) {
        print_error('incorrectwikiid', 'wiki');
    }

    // Getting the subwiki corresponding to that wiki, group and user.
    //
    // Also setting the page if it exists or getting the first page title form
    // that wiki

    // Getting current group id
    $currentgroup = groups_get_activity_group($cm);
    $currentgroup = !empty($currentgroup)?$currentgroup:0;
    // Getting current user id
    if ($wiki->wikimode == 'individual'){
        if (empty($userid)){
            $userid = $USER->id;
        }
    } else {
        $userid = 0;
    }
    $subwiki = wiki_get_subwiki_by_group($wiki->id, $currentgroup, $userid);
    $page = null;
    if (!empty($subwiki)){
        $page = wiki_get_first_page($subwiki->id, $wiki);
    }
    if (!empty($page)){
        $pageid = $page->id;
    } else {
        // the first page doesn't exist, create first page automatically
        // Then redirct to editing page
        $page = null;
        $title = $wiki->firstpagetitle;
        $default = $wiki->defaultformat;
        if (empty($subwiki)) {
            if (!$swid = wiki_add_subwiki($wiki->id, $currentgroup, $userid)) {
                print_error('invalidwikiid');
            }
        } else {
            $swid = $subwiki->id;
        }

        $id = wiki_create_page($swid, $title, $default, $USER->id);
        redirect($CFG->wwwroot . '/mod/wiki/edit.php?pageid=' . $id);
    }

    /*
     * Case 1:
     *
     * A user wants to see a page.
     *
     * If group is set, system must show the page with the same name from another group.
     * In this case, is probable that there is no version of that page for the
     * given group
     *
     * URL Params: pageid -> page id
     *             group -> group id (optional)
     *
     */
} elseif ($pageid) {

    // Checking page instance
    if (!$page = wiki_get_page($pageid)) {
        print_error('incorrectpageid', 'wiki');
    }
    if (!empty($swid)){
        // User wants to view another subwiki
        if ($subwiki = wiki_get_subwiki($swid)){
            // Trying to get the same page but from another subwiki
            if (!$page = wiki_get_page_by_title($swid, $page->title)) {
                // That page does not exists
                // Getting the first page of that wiki
                $wiki = wiki_get_wiki($subwiki->wikiid);
                if (!$page = wiki_get_page_by_title($swid, $wiki->firstpagetitle)){
                    $url = new moodle_url('/mod/wiki/view.php', array('id'=>$subwiki->id));
                    print_error('individualpagedoesnotexist', 'wiki', $url->out());
                }
            }
        } else {
            print_error('incorrectsubwikiid', 'wiki');
        }

    } else if (!$subwiki = wiki_get_subwiki($page->subwikiid)) {
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
    if (!$course = get_course_by_id($cm->course)) {
        print_error('coursemisconf');
    }

    // Switching to the correct page and subwiki if group param is present
    if ($currentgroup = groups_get_activity_group($cm)) {
        if ($subwiki->groupid != $currentgroup) {

            // Setting new subwiki instance
            // @TODO: Fix call to wiki_get_subwiki_by_group
            $subwiki = wiki_get_subwiki_by_group($wiki->id, $currentgroup);

            // Setting new page instance or page title
            $title = $page->title;
            if ($page = wiki_get_page_by_title($subwiki->id, $page->title)) {
                unset($title);
            }
        }
    }

    /*
     * Case 2:
     *
     * Trying to read a page by using subwiki->id and title.
     *
     * Page can exists or not.
     *  * If it exists, page must be shown
     *  * If it does not exists, system must ask for its creation
     *
     * URL params: swid -> subwiki id
     *             title -> a page title
     */
} elseif ($swid && $title) {

    // Getting subwiki instance
    if (!$subwiki = wiki_get_subwiki($swid)) {
        print_error('incorrectsubwikiid', 'wiki');
    }

    // Checking is there is a page with this title
    if ($page = wiki_get_page_by_title($swid, $title)) {
        unset($title);
    }

    // Setting wiki instance
    if (!$wiki = wiki_get_wiki($subwiki->wikiid)) {
        print_error('incorrectwikiid', 'wiki');
    }

    // Checking course module
    if (!$cm = get_coursemodule_from_instance("wiki", $subwiki->wikiid)) {
        print_error('invalidcoursemodule');
    }

    // Checking course instance
    if (!$course = $DB->get_record("course", array("id" => $cm->course))) {
        print_error('coursemisconf');
    }

    /*
     * Case 3:
     *
     * A user switches group when is 'reading' a non-existent page.
     *
     * URL Params: wid -> wiki id
     *             title -> page title
     *             currentgroup -> group id
     *
     */
} elseif ($wid && $title && $currentgroup) {

    // Checking wiki instance
    if (!$wiki = wiki_get_wiki($wid)) {
        print_error('incorrectwikiid', 'wiki');
    }

    // Checking subwiki instance
    // @TODO: Fix call to wiki_get_subwiki_by_group
    $currentgroup = groups_get_activity_group($cm);
    if (!$subwiki = wiki_get_subwiki_by_group($wid, $currentgroup)) {
        print_error('incorrectsubwikiid', 'wiki');
    }

    // Checking page instance
    if ($page = wiki_get_page_by_title($subwiki->id, $title)) {
        unset($title);
    }

    // Checking course instance
    if (!$course = get_course_by_id($wiki->course)) {
        print_error('coursemisconf');
    }

    // Checking course module instance
    if (!$cm = get_coursemodule_from_instance("wiki", $wiki->id, $course->id)) {
        print_error('invalidcoursemodule');
    }

    $subwiki = null;
    $page = null;

    /*
     * Case 4:
     *
     * Error. No more options
     */
} else {
    print_error('incorrectparameters');
}


require_course_login($course, true, $cm);

$context = get_context_instance(CONTEXT_MODULE, $cm->id);
require_capability('mod/wiki:viewpage', $context);

add_to_log($course->id, 'wiki', 'view', 'view.php?id='.$cm->id, $wiki->id);

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
