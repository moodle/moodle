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
 * @package mod_wiki
 * @copyright 2010 Dongsheng Cai <dongsheng@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->dirroot . '/mod/wiki/lib.php');
require_once($CFG->dirroot . '/mod/wiki/locallib.php');
require_once($CFG->dirroot . '/mod/wiki/pagelib.php');

$search = optional_param('searchstring', null, PARAM_TEXT);
$courseid = optional_param('courseid', 0, PARAM_INT);
$searchcontent = optional_param('searchwikicontent', 0, PARAM_INT);
$cmid = optional_param('cmid', 0, PARAM_INT);
$subwikiid = optional_param('subwikiid', 0, PARAM_INT);
$userid = optional_param('uid', 0, PARAM_INT);

if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    print_error('invalidcourseid');
}
if (!$cm = get_coursemodule_from_id('wiki', $cmid)) {
    print_error('invalidcoursemodule');
}

require_login($course, true, $cm);

// Checking wiki instance
if (!$wiki = wiki_get_wiki($cm->instance)) {
    print_error('incorrectwikiid', 'wiki');
}

if ($subwikiid) {
    // Subwiki id is specified.
    $subwiki = wiki_get_subwiki($subwikiid);
    if (!$subwiki || $subwiki->wikiid != $wiki->id) {
        print_error('incorrectsubwikiid', 'wiki');
    }
} else {
    // Getting current group id
    $gid = groups_get_activity_group($cm);

    // Getting current user id
    if ($wiki->wikimode == 'individual') {
        $userid = $userid ? $userid : $USER->id;
    } else {
        $userid = 0;
    }
    if (!$subwiki = wiki_get_subwiki_by_group($cm->instance, $gid, $userid)) {
        // Subwiki does not exist yet, redirect to the view page (which will redirect to create page if allowed).
        $params = array('wid' => $wiki->id, 'group' => $gid, 'uid' => $userid, 'title' => $wiki->firstpagetitle);
        $url = new moodle_url('/mod/wiki/view.php', $params);
        redirect($url);
    }
}

if ($subwiki && !wiki_user_can_view($subwiki, $wiki)) {
    print_error('cannotviewpage', 'wiki');
}

$wikipage = new page_wiki_search($wiki, $subwiki, $cm);

$wikipage->set_search_string($search, $searchcontent);

$wikipage->set_title(get_string('search'));

$wikipage->print_header();

$wikipage->print_content();

$wikipage->print_footer();
