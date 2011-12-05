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
 * This is a very rough importer for openoffice impress slides
 * Export a openoffice impress presentation with openoffice impress as html pages
 * Do it with openoffice 3.x (I think?) and no special settings
 * Then zip the directory with all of the html pages
 * and the zip file is what you want to upload
 *
 * The script supports book and lesson.
 *
 * @package    mod
 * @subpackage lesson
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}, Wojciech Wierchoła
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 **/

/** include required files */
require_once("../../config.php");
require_once($CFG->dirroot.'/mod/lesson/locallib.php');
require_once($CFG->dirroot.'/mod/lesson/importodplib.php');

$id     = required_param('id', PARAM_INT);         // Course Module ID
$pageid = optional_param('pageid', '', PARAM_INT); // Page ID

$url = new moodle_url('/mod/lesson/importodp.php', array('id'=>$id));
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

$strimportodp = get_string("importodp", "lesson");
$strlessons = get_string("modulenameplural", "lesson");

$data = new stdClass;
$data->id = $cm->id;
$data->pageid = $pageid;
$mform = new lesson_importodp_form();
$mform->set_data($data);

if ($data = $mform->get_data()) {
    $manager = lesson_page_type_manager::get($lesson);
    if (!$filename = $mform->get_new_filename('odpzip')) {
        print_error('invalidfile', 'lesson');
    }
    if (!$package = $mform->save_stored_file('odpzip', $context->id, 'mod_lesson', 'odp_imports', $lesson->id, '/', $filename, true)) {
        print_error('unabletosavefile', 'lesson');
    }
    // extract package content
    $packer = get_file_packer('application/zip');
    $package->extract_to_storage($packer, $context->id, 'mod_lesson', 'imported_files', $lesson->id, '/');

    $fs = get_file_storage();
    if ($files = $fs->get_area_files($context->id, 'mod_lesson', 'imported_files', $lesson->id)) {

        $pages = array();
        foreach ($files as $key=>$file) {
            if ($file->get_mimetype() != 'text/html'){
                continue;
            }
            $filenameinfo = pathinfo($file->get_filepath().$file->get_filename());
            $matches = array();
            if(!preg_match("/^(img[0-9]+)+\.html?$/i",$file->get_filename(),$matches)) {
                continue;
            }
            $fileID = $matches[1];
            $page = new stdClass;
            $page->title = '';
            $page->contents = array();
            $page->images = array();
            $page->source = $filenameinfo['basename'];

            $string = strip_tags($file->get_content(),'<img><title>');
            $imgs = array();
            preg_match_all("/<img[^>]*(src\=\"($fileID\.[^>^\"]*)\"[^>]*)>/i", $string, $imgs);
            foreach ($imgs[2] as $img) {
                $imagename = basename($img);
                foreach ($files as $file) {
                    if ($imagename === $file->get_filename()) {
                        $page->images[] = clone($file);
                    }
                }
            }
            $matches = array();
            preg_match("/<title>([^<]*)<\/title>/i",$string,$matches);
            $page->title = $matches[1];
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

    redirect("$CFG->wwwroot/mod/$modname/view.php?id=$cm->id", get_string('odpsuccessfullimport', 'lesson'), 5);
}

$PAGE->navbar->add($strimportodp);
$PAGE->set_title($strimportodp);
$PAGE->set_heading($strimportodp);
echo $OUTPUT->header();

/// Print upload form
echo $OUTPUT->heading_with_help($strimportodp, 'importodp', 'lesson');
echo $OUTPUT->box_start('generalbox boxaligncenter');
$mform->display();
echo $OUTPUT->box_end();
echo $OUTPUT->footer();
?>