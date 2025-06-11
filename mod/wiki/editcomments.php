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
 *
 * @package mod_wiki
 * @copyright 2009 Marc Alier, Jordi Piguillem marc.alier@upc.edu
 * @copyright 2009 Universitat Politecnica de Catalunya http://www.upc.edu
 *
 * @author Jordi Piguillem
 * @author Marc Alier
 * @author David Jimenez
 * @author Josep Arus
 * @author Daniel Serrano
 * @author Kenneth Riba

 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once('../../config.php');
require_once($CFG->dirroot . '/mod/wiki/locallib.php');
require_once($CFG->dirroot . '/mod/wiki/pagelib.php');

$pageid = required_param('pageid', PARAM_INT);
$action = optional_param('action', '', PARAM_ALPHANUMEXT);
$commentid = optional_param('commentid', 0, PARAM_INT);

if (!$page = wiki_get_page($pageid)) {
    throw new \moodle_exception('incorrectpageid', 'wiki');
}
if (!$subwiki = wiki_get_subwiki($page->subwikiid)) {
    throw new \moodle_exception('incorrectsubwikiid', 'wiki');
}
if (!$cm = get_coursemodule_from_instance("wiki", $subwiki->wikiid)) {
    throw new \moodle_exception('invalidcoursemodule');
}
$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
if (!$wiki = wiki_get_wiki($subwiki->wikiid)) {
    throw new \moodle_exception('incorrectwikiid', 'wiki');
}
require_login($course, true, $cm);

if (!wiki_user_can_view($subwiki, $wiki)) {
    throw new \moodle_exception('cannotviewpage', 'wiki');
}

$editcomments = new page_wiki_editcomment($wiki, $subwiki, $cm, 'modulepage');
$comment = new stdClass();
if ($action == 'edit') {
    if (!$comment = $DB->get_record('comments', array('id' => $commentid))) {
        throw new \moodle_exception('invalidcomment');
    }
    if ($USER->id != $comment->userid) {
        throw new \moodle_exception('cannotviewpage', 'wiki');
    }
}

$editcomments->set_page($page);
$editcomments->set_action($action, $comment);

$editcomments->print_header();
$editcomments->print_content();
$editcomments->print_footer();
