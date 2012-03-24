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
 * Delete wiki pages or versions
 *
 * This will show options for deleting wiki pages or purging page versions
 * If user have wiki:managewiki ability then only this page will show delete
 * options
 *
 * @package mod-wiki-2.0
 * @copyright 2011 Rajesh Taneja
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->dirroot . '/mod/wiki/lib.php');
require_once($CFG->dirroot . '/mod/wiki/locallib.php');
require_once($CFG->dirroot . '/mod/wiki/pagelib.php');

$pageid = required_param('pageid', PARAM_INT); // Page ID
$delete = optional_param('delete', 0, PARAM_INT); // ID of the page to be deleted.
$option = optional_param('option', 1, PARAM_INT); // Option ID
$listall = optional_param('listall', 0, PARAM_INT); // list all pages
$toversion = optional_param('toversion', 0, PARAM_INT); // max version to be deleted
$fromversion = optional_param('fromversion', 0, PARAM_INT); // min version to be deleted

if (!$page = wiki_get_page($pageid)) {
    print_error('incorrectpageid', 'wiki');
}
if (!$subwiki = wiki_get_subwiki($page->subwikiid)) {
    print_error('incorrectsubwikiid', 'wiki');
}
if (!$cm = get_coursemodule_from_instance("wiki", $subwiki->wikiid)) {
    print_error('invalidcoursemodule');
}
$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
if (!$wiki = wiki_get_wiki($subwiki->wikiid)) {
    print_error('incorrectwikiid', 'wiki');
}

require_login($course->id, true, $cm);


$context = get_context_instance(CONTEXT_MODULE, $cm->id);
require_capability('mod/wiki:managewiki', $context);

add_to_log($course->id, "wiki", "admin", "admin.php?pageid=".$page->id, $page->id, $cm->id);

//Delete page if a page ID to delete was supplied
if (!empty($delete) && confirm_sesskey()) {
    wiki_delete_pages($context, $delete, $page->subwikiid);
    //when current wiki page is deleted, then redirect user to create that page, as
    //current pageid is invalid after deletion.
    if ($pageid == $delete) {
        $params = array('swid' => $page->subwikiid, 'title' => $page->title);
        $url = new moodle_url('/mod/wiki/create.php', $params);
        redirect($url);
    }
}

//delete version if toversion and fromversion are set.
if (!empty($toversion) && !empty($fromversion) && confirm_sesskey()) {
    //make sure all versions should not be deleted...
    $versioncount = wiki_count_wiki_page_versions($pageid);
    $versioncount -= 1; //ignore version 0
    $totalversionstodelete = $toversion - $fromversion;
    $totalversionstodelete += 1; //added 1 as toversion should be included

    if (($totalversionstodelete >= $versioncount) || ($versioncount <= 1)) {
        print_error('incorrectdeleteversions', 'wiki');
    } else {
        $versions = array();
        for ($i = $fromversion; $i <= $toversion; $i++) {
            //Add all version to deletion list which exist
            if (wiki_get_wiki_page_version($pageid, $i)) {
                array_push($versions, $i);
            }
        }
        $purgeversions[$pageid] = $versions;
        wiki_delete_page_versions($purgeversions);
    }
}

//show actual page
$wikipage = new page_wiki_admin($wiki, $subwiki, $cm);

$wikipage->set_page($page);
$wikipage->print_header();
$wikipage->set_view($option, empty($listall)?true:false);
$wikipage->print_content();

$wikipage->print_footer();