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
 * This file contains all necessary code to view a diff page
 *
 * @package mod_wiki
 * @copyright 2009 Marc Alier, Jordi Piguillem marc.alier@upc.edu
 * @copyright 2009 Universitat Politecnica de Catalunya http://www.upc.edu
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

require_once($CFG->dirroot . '/mod/wiki/diff/difflib.php');

$pageid = required_param('pageid', PARAM_TEXT);
$compare = required_param('compare', PARAM_INT);
$comparewith = required_param('comparewith', PARAM_INT);

if (!$page = wiki_get_page($pageid)) {
    throw new \moodle_exception('incorrectpageid', 'wiki');
}

if (!$subwiki = wiki_get_subwiki($page->subwikiid)) {
    throw new \moodle_exception('incorrectsubwikiid', 'wiki');
}

if (!$wiki = wiki_get_wiki($subwiki->wikiid)) {
    throw new \moodle_exception('incorrectwikiid', 'wiki');
}

if (!$cm = get_coursemodule_from_instance('wiki', $wiki->id)) {
    throw new \moodle_exception('invalidcoursemodule');
}

$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);

if ($compare >= $comparewith) {
    throw new \moodle_exception('cannotcomparenewerversion', 'wiki');
}

require_course_login($course, true, $cm);

if (!wiki_user_can_view($subwiki, $wiki)) {
    throw new \moodle_exception('cannotviewpage', 'wiki');
}

$wikipage = new page_wiki_diff($wiki, $subwiki, $cm, 'modulepage');

$wikipage->set_page($page);
$wikipage->set_comparison($compare, $comparewith);

$event = \mod_wiki\event\page_diff_viewed::create(
        array(
            'context' => context_module::instance($cm->id),
            'objectid' => $pageid,
            'other' => array(
                'comparewith' => $comparewith,
                'compare' => $compare
                )
            ));
$event->add_record_snapshot('wiki_pages', $page);
$event->add_record_snapshot('wiki', $wiki);
$event->add_record_snapshot('wiki_subwikis', $subwiki);
$event->trigger();

$wikipage->print_header();

$wikipage->print_content();

$wikipage->print_footer();
