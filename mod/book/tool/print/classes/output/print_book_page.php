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
 * Class containing data for the view book page.
 *
 * @package    booktool_print
 * @copyright  2019 Mihail Geshoski
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace booktool_print\output;

defined('MOODLE_INTERNAL') || die();

use moodle_url;
use renderable;
use renderer_base;
use stdClass;
use templatable;
use context_module;

/**
 * Class containing data for the print book page.
 *
 * @copyright  2019 Mihail Geshoski
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class print_book_page implements renderable, templatable {

    /**
     * @var object $book The book object.
     */
    protected $book;

    /**
     * @var object $cm The course module object.
     */
    protected $cm;

    /**
     * Construct this renderable.
     *
     * @param object $book The book
     * @param object $cm The course module
     */
    public function __construct($book, $cm) {
        $this->book = $book;
        $this->cm = $cm;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        global $OUTPUT, $CFG, $SITE, $USER;

        $context = context_module::instance($this->cm->id);
        $chapters = book_preload_chapters($this->book);
        $course = get_course($this->book->course);

        $data = new stdClass();
        // Print dialog link.
        $data->printdialoglink = $output->render_print_book_dialog_link();
        $data->booktitle = $OUTPUT->heading(format_string($this->book->name, true,
                array('context' => $context)), 1);
        $introtext = file_rewrite_pluginfile_urls($this->book->intro, 'pluginfile.php', $context->id, 'mod_book', 'intro', null);
        $data->bookintro = format_text($introtext, $this->book->introformat,
                array('noclean' => true, 'context' => $context));
        $data->sitelink = \html_writer::link(new moodle_url($CFG->wwwroot),
                format_string($SITE->fullname, true, array('context' => $context)));
        $data->coursename = format_string($course->fullname, true, array('context' => $context));
        $data->modulename = format_string($this->book->name, true, array('context' => $context));
        $data->username = fullname($USER, true);
        $data->printdate = userdate(time());
        $data->toc = $output->render_print_book_toc($chapters, $this->book, $this->cm);
        foreach ($chapters as $ch) {
            list($chaptercontent, $chaptervisible) = $output->render_print_book_chapter($ch, $chapters, $this->book,
                    $this->cm);
            $chapter = new stdClass();
            $chapter->content = $chaptercontent;
            $chapter->visible = $chaptervisible;
            $data->chapters[] = $chapter;
        }

        return $data;
    }
}
