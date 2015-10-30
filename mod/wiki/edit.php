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

$pageid = required_param('pageid', PARAM_INT);
$contentformat = optional_param('contentformat', '', PARAM_ALPHA);
$option = optional_param('editoption', '', PARAM_TEXT);
$section = optional_param('section', "", PARAM_TEXT);
$version = optional_param('version', -1, PARAM_INT);
$attachments = optional_param('attachments', 0, PARAM_INT);
$deleteuploads = optional_param('deleteuploads', 0, PARAM_RAW);

$newcontent = '';
if (!empty($newcontent) && is_array($newcontent)) {
    $newcontent = $newcontent['text'];
}

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

require_login($course, true, $cm);

$context = context_module::instance($cm->id);

if (!wiki_user_can_edit($subwiki)) {
    print_error('cannoteditpage', 'wiki');
}

if ($option == get_string('save', 'wiki')) {
    if (!confirm_sesskey()) {
        print_error(get_string('invalidsesskey', 'wiki'));
    }
    $wikipage = new page_wiki_save($wiki, $subwiki, $cm);
    $wikipage->set_page($page);
    $wikipage->set_newcontent($newcontent);
    $wikipage->set_upload(true);
} else {
    if ($option == get_string('preview')) {
        if (!confirm_sesskey()) {
            print_error(get_string('invalidsesskey', 'wiki'));
        }
        $wikipage = new page_wiki_preview($wiki, $subwiki, $cm);
        $wikipage->set_page($page);
    } else {
        if ($option == get_string('cancel')) {
            //delete lock
            wiki_delete_locks($page->id, $USER->id, $section);

            redirect($CFG->wwwroot . '/mod/wiki/view.php?pageid=' . $pageid);
        } else {
            $wikipage = new page_wiki_edit($wiki, $subwiki, $cm);
            $wikipage->set_page($page);
            $wikipage->set_upload($option == get_string('upload', 'wiki'));
        }
    }

    if (has_capability('mod/wiki:overridelock', $context)) {
        $wikipage->set_overridelock(true);
    }
}

if ($version >= 0) {
    $wikipage->set_versionnumber($version);
}

if (!empty($section)) {
    $wikipage->set_section($sectioncontent, $section);
}

if (!empty($attachments)) {
    $wikipage->set_attachments($attachments);
}

if (!empty($deleteuploads)) {
    $wikipage->set_deleteuploads($deleteuploads);
}

if (!empty($contentformat)) {
    $wikipage->set_format($contentformat);
}

$wikipage->print_header();

$wikipage->print_content();

$wikipage->print_footer();
