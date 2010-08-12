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
 * This file contains all necessary code to edit a wiki page
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

$pageid = required_param('pageid', PARAM_INT);
$section = optional_param('section', '', PARAM_TEXT);

if (!$page = wiki_get_page($pageid)) {
    print_error('incorrectpageid', 'wiki');
}

if (!$subwiki = wiki_get_subwiki($page->subwikiid)) {
    print_error('incorrectsubwikiid', 'wiki');
}

if (!$wiki = wiki_get_wiki($subwiki->wikiid)) {
    print_error('incorrectwikiid', 'wiki');
}

if (!$cm = get_coursemodule_from_instance('wiki', $wiki->id)) {
    print_error('invalidcoursemodule');
}

$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);

if (!empty($section) && !$sectioncontent = wiki_get_section_page($page, $section)) {
    print_error('invalidsection', 'wiki');
}

require_login($course->id, true, $cm);

$context = get_context_instance(CONTEXT_MODULE, $cm->id);
require_capability('mod/wiki:overridelock', $context);

add_to_log($course->id, "wiki", "overridelocks", "overridelocks.php?id=$cm->id", "$wiki->id");

if (!confirm_sesskey()) {
    print_error(get_string('invalidsesskey', 'wiki'));
}

$wikipage = new page_wiki_overridelocks($wiki, $subwiki, $cm);
$wikipage->set_page($page);

if (!empty($section)) {
    $wikipage->set_section($sectioncontent, $section);
}

$wikipage->print_header();

$wikipage->print_content();

$wikipage->print_footer();
