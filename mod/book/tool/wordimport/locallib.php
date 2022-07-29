<?php
// This file is part of Moodle - http://moodle.org/
//
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Import/Export Microsoft Word files library.
 *
 * @package    booktool_wordimport
 * @copyright  2016 Eoin Campbell
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once(dirname(__FILE__).'/lib.php');
require_once($CFG->dirroot.'/course/lib.php');
require_once($CFG->dirroot.'/mod/book/lib.php');
require_once($CFG->dirroot.'/mod/book/locallib.php');
require_once($CFG->dirroot.'/mod/book/tool/importhtml/locallib.php');

use \booktool_wordimport\wordconverter;

/**
 * Import HTML pages from a Word file
 *
 * @param string $wordfilename Word file to be imported
 * @param stdClass $book Book being imported into
 * @param context_module $context
 * @param bool $splitonsubheadings Split book into chapters and subchapters
 * @param bool $verbose Print extra progress messages
 * @return void
 */
function booktool_wordimport_import(string $wordfilename, stdClass $book, context_module $context,
                bool $splitonsubheadings = false, bool $verbose = false) {
    global $CFG;

    // Convert the Word file content into XHTML and an array of images.
    $imagesforzipping = array();
    $word2xml = new wordconverter();
    $htmlcontent = $word2xml->import($wordfilename, $imagesforzipping);

    // Store images in a Zip file and split the HTML file into sections.
    // Add the sections to the Zip file and store it in Moodles' file storage area.
    $zipfilename = tempnam($CFG->tempdir, "zip");
    $zipfile = $word2xml->zip_images($zipfilename, $imagesforzipping);
    $word2xml->split_html($htmlcontent, $zipfile, $splitonsubheadings, $verbose);
    $zipfile = $word2xml->store_html($zipfilename, $zipfile, $context);
    unlink($zipfilename);

    // Call the core HTML import function to really import the content.
    // Argument 2, value 2 = Each HTML file represents 1 chapter.
    toolbook_importhtml_import_chapters($zipfile, 2, $book, $context);
}

/**
 * Export Book chapters to a Word file
 *
 * @param stdClass $book Book to export
 * @param context_module $context Current course context
 * @param int $chapterid The chapter to export (optional)
 * @return string
 */
function booktool_wordimport_export(stdClass $book, context_module $context, int $chapterid = 0) {
    global $DB;

    // Export a single chapter or the whole book into Word.
    $allchapters = array();
    $booktext = '';
    $word2xml = new wordconverter();
    if ($chapterid == 0) {
        $allchapters = $DB->get_records('book_chapters', array('bookid' => $book->id), 'pagenum');
        // Read the title and introduction into a string, embedding images.
        $booktext .= '<p class="MsoTitle">' . $book->name . "</p>\n";
        // Grab the images, convert any GIFs to PNG, and return the list of converted images.
        $giffilenames = array();
        $imagestring = $word2xml->base64_images($context->id, 'mod_book', 'intro', null, $giffilenames);

        $introcontent = $book->intro;
        if (count($giffilenames) > 0) {
            $introcontent = str_replace($giffilenames['gif'], $giffilenames['png'], $introcontent);
        }
        $booktext .= '<div class="chapter" id="intro">' . $introcontent . $imagestring . "</div>\n";
    } else {
        $allchapters[0] = $DB->get_record('book_chapters', array('bookid' => $book->id, 'id' => $chapterid), '*', MUST_EXIST);
    }

    // Append all the chapters to the end of the string, again embedding images.
    foreach ($allchapters as $chapter) {
        // Make sure the chapter is visible to the current user.
        if (!$chapter->hidden || has_capability('mod/book:viewhiddenchapters', $context)) {
            $booktext .= '<div class="chapter" id="' . $chapter->id . '">';
            // Check if the chapter title is duplicated inside the content, and include it if not.
            if (!$chapter->subchapter and !strpos($chapter->content, "<h1")) {
                $booktext .= "<h1>" . $chapter->title . "</h1>\n";
            } else if ($chapter->subchapter and !strpos($chapter->content, "<h2")) {
                $booktext .= "<h2>" . $chapter->title . "</h2>\n";
            }

            // Grab the images, convert any GIFs to PNG, and return the list of converted images.
            $giffilenames = array();
            $imagestring = $word2xml->base64_images($context->id, 'mod_book', 'chapter', $chapter->id, $giffilenames);

            // Grab the chapter text content, and update any GIF image names to the new PNG name.
            $chaptercontent = $chapter->content;
            if (count($giffilenames) > 0) {
                $chaptercontent = str_replace($giffilenames['gif'], $giffilenames['png'], $chaptercontent);
            }
            $booktext .= $chaptercontent . $imagestring . "</div>\n";
        }
    }
    $moodlelabels = "<moodlelabels></moodlelabels>\n";

    // Convert the XHTML string into a Word-compatible version, with image data embedded in Word 365-compatible way.
    $booktext = $word2xml->export($booktext, 'booktool_wordimport', $moodlelabels, 'embedded');
    return $booktext;
}

/**
 * Delete previously unzipped Word file
 *
 * @param context_module $context
 */
function booktool_wordimport_delete_files($context) {
    $fs = get_file_storage();
    $fs->delete_area_files($context->id, 'mod_book', 'wordimporttemp', 0);
}
