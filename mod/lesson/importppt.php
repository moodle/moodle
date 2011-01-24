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
 * This is a very rough importer for powerpoint slides
 * Export a powerpoint presentation with powerpoint as html pages
 * Do it with office 2002 (I think?) and no special settings
 * Then zip the directory with all of the html pages
 * and the zip file is what you want to upload
 *
 * The script supports book and lesson.
 *
 * @package    mod
 * @subpackage lesson
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 **/

/** include required files */
require_once("../../config.php");
require_once($CFG->dirroot.'/mod/lesson/locallib.php');
require_once($CFG->dirroot.'/mod/lesson/importpptlib.php');

$id     = required_param('id', PARAM_INT);         // Course Module ID
$pageid = optional_param('pageid', '', PARAM_INT); // Page ID

$url = new moodle_url('/mod/lesson/importppt.php', array('id'=>$id));
if ($pageid !== '') {
    $url->param('pageid', $pageid);
}
$PAGE->set_url($url);

$cm = get_coursemodule_from_id('lesson', $id, 0, false, MUST_EXIST);;
$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
$lesson = new lesson($DB->get_record('lesson', array('id' => $cm->instance), '*', MUST_EXIST));

$modname = 'lesson';
$mod = $cm;
require_login($course, false, $cm);

require_login($course->id, false, $cm);
$context = get_context_instance(CONTEXT_MODULE, $cm->id);
require_capability('mod/lesson:edit', $context);

$strimportppt = get_string("importppt", "lesson");
$strlessons = get_string("modulenameplural", "lesson");

$data = new stdClass;
$data->id = $cm->id;
$data->pageid = $pageid;
$mform = new lesson_importppt_form();
$mform->set_data($data);

if ($data = $mform->get_data()) {
    $manager = lesson_page_type_manager::get($lesson);
    if (!$filename = $mform->get_new_filename('pptzip')) {
        print_error('invalidfile', 'lesson');
    }
    if (!$package = $mform->save_stored_file('pptzip', $context->id, 'mod_lesson', 'ppt_imports', $lesson->id, '/', $filename, true)) {
        print_error('unabletosavefile', 'lesson');
    }
    // extract package content
    $packer = get_file_packer('application/zip');
    $package->extract_to_storage($packer, $context->id, 'mod_lesson', 'imported_files', $lesson->id, '/');

    $fs = get_file_storage();
    if ($files = $fs->get_area_files($context->id, 'mod_lesson', 'imported_files', $lesson->id)) {

        $pages = array();
        foreach ($files as $key=>$file) {
            if ($file->get_mimetype() != 'text/html') {
                continue;
            }
            $filenameinfo = pathinfo($file->get_filepath().$file->get_filename());

            $page = new stdClass;
            $page->title = '';
            $page->contents = array();
            $page->images = array();
            $page->source = $filenameinfo['basename'];

            $string = strip_tags($file->get_content(),'<div><img>');
            $imgs = array();
            preg_match_all("/<img[^>]*(src\=\"(".$filenameinfo['filename']."\_image[^>^\"]*)\"[^>]*)>/i", $string, $imgs);
            foreach ($imgs[2] as $img) {
                $imagename = basename($img);
                foreach ($files as $file) {
                    if ($imagename === $file->get_filename()) {
                        $page->images[] = clone($file);
                    }
                }
            }

            $matches = array();
            // this will look for a non nested tag that is closed
            // want to allow <b><i>(maybe more) tags but when we do that
            // the preg_match messes up.
            preg_match_all("/(<([\w]+)[^>]*>)([^<\\2>]*)(<\/\\2>)/", $string, $matches);
            $countmatches = count($matches[1]);
            for($i = 0; $i < $countmatches; $i++) { // go through all of our div matches

                $class = lesson_importppt_isolate_class($matches[1][$i]); // first step in isolating the class

                // check for any static classes
                switch ($class) {
                    case 'T':  // class T is used for Titles
                        $page->title = $matches[3][$i];
                        break;
                    case 'B':  // I would guess that all bullet lists would start with B then go to B1, B2, etc
                    case 'B1': // B1-B4 are just insurance, should just hit B and all be taken care of
                    case 'B2':
                    case 'B3':
                    case 'B4':
                        $page->contents[] = lesson_importppt_build_list($matches, '<ul>', $i, 0);  // this is a recursive function that will grab all the bullets and rebuild the list in html
                        break;
                    default:
                        if ($matches[3][$i] != '&#13;') {  // odd crap generated... sigh
                            if (substr($matches[3][$i], 0, 1) == ':') {  // check for leading :    ... hate MS ...
                                $page->contents[] = substr($matches[3][$i], 1);  // get rid of :
                            } else {
                                $page->contents[] = $matches[3][$i];
                            }
                        }
                        break;
                }
            }
            $pages[] = $page;
        }

        $branchtables = lesson_create_objects($pages, $lesson->id);

        // first set up the prevpageid and nextpageid
        if (empty($pageid)) { // adding it to the top of the lesson
            $prevpageid = 0;
            // get the id of the first page.  If not found, then no pages in the lesson
            if (!$nextpageid = $DB->get_field('lesson_pages', 'id', array('prevpageid' => 0, 'lessonid' => $lesson->id))) {
                $nextpageid = 0;
            }
        } else {
            // going after an actual page
            $prevpageid = $pageid;
            $nextpageid = $DB->get_field('lesson_pages', 'nextpageid', array('id' => $pageid));
        }

        foreach ($branchtables as $branchtable) {

            // set the doubly linked list
            $branchtable->page->nextpageid = $nextpageid;
            $branchtable->page->prevpageid = $prevpageid;

            // insert the page
            $id = $DB->insert_record('lesson_pages', $branchtable->page);

            if (!empty($branchtable->page->images)) {
                $changes = array('contextid'=>$context->id, 'component'=>'mod_lesson', 'filearea'=>'page_contents', 'itemid'=>$id, 'timemodified'=>time());
                foreach ($branchtable->page->images as $image) {
                    $fs->create_file_from_storedfile($changes, $image);
                }
            }

            // update the link of the page previous to the one we just updated
            if ($prevpageid != 0) {  // if not the first page
                $DB->set_field("lesson_pages", "nextpageid", $id, array("id" => $prevpageid));
            }

            // insert the answers
            foreach ($branchtable->answers as $answer) {
                $answer->pageid = $id;
                $DB->insert_record('lesson_answers', $answer);
            }

            $prevpageid = $id;
        }

        // all done with inserts.  Now check to update our last page (this is when we import between two lesson pages)
        if ($nextpageid != 0) {  // if the next page is not the end of lesson
            $DB->set_field("lesson_pages", "prevpageid", $id, array("id" => $nextpageid));
        }
    }

    // Remove all unzipped files!
    $fs->delete_area_files($context->id, 'mod_lesson', 'imported_files', $lesson->id);

    redirect("$CFG->wwwroot/mod/$modname/view.php?id=$cm->id", get_string('pptsuccessfullimport', 'lesson'), 5);
}

$PAGE->navbar->add($strimportppt);
$PAGE->set_title($strimportppt);
$PAGE->set_heading($strimportppt);
echo $OUTPUT->header();

/// Print upload form
echo $OUTPUT->heading_with_help($strimportppt, 'importppt', 'lesson');
echo $OUTPUT->box_start('generalbox boxaligncenter');
$mform->display();
echo $OUTPUT->box_end();
echo $OUTPUT->footer();
