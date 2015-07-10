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
 * Book external API
 *
 * @package    mod_book
 * @category   external
 * @copyright  2015 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.0
 */

defined('MOODLE_INTERNAL') || die;

require_once("$CFG->libdir/externallib.php");

/**
 * Book external functions
 *
 * @package    mod_book
 * @category   external
 * @copyright  2015 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.0
 */
class mod_book_external extends external_api {

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 3.0
     */
    public static function view_book_parameters() {
        return new external_function_parameters(
            array(
                'bookid' => new external_value(PARAM_INT, 'book instance id'),
                'chapterid' => new external_value(PARAM_INT, 'chapter id', VALUE_DEFAULT, 0)
            )
        );
    }

    /**
     * Simulate the book/view.php web interface page: trigger events, completion, etc...
     *
     * @param int $bookid the book instance id
     * @param int $chapterid the book chapter id
     * @return array of warnings and status result
     * @since Moodle 3.0
     * @throws moodle_exception
     */
    public static function view_book($bookid, $chapterid = 0) {
        global $DB, $CFG;
        require_once($CFG->dirroot . "/mod/book/lib.php");
        require_once($CFG->dirroot . "/mod/book/locallib.php");

        $params = self::validate_parameters(self::view_book_parameters(),
                                            array(
                                                'bookid' => $bookid,
                                                'chapterid' => $chapterid
                                            ));
        $bookid = $params['bookid'];
        $chapterid = $params['chapterid'];

        $warnings = array();

        // Request and permission validation.
        $book = $DB->get_record('book', array('id' => $bookid), '*', MUST_EXIST);
        list($course, $cm) = get_course_and_cm_from_instance($book, 'book');

        $context = context_module::instance($cm->id);
        self::validate_context($context);

        require_capability('mod/book:read', $context);

        $chapters = book_preload_chapters($book);
        $firstchapterid = 0;
        $lastchapterid = 0;

        foreach ($chapters as $ch) {
            if ($ch->hidden) {
                continue;
            }
            if (!$firstchapterid) {
                $firstchapterid = $ch->id;
            }
            $lastchapterid = $ch->id;
        }

        if (!$chapterid) {
            // Trigger the module viewed events since we are displaying the book.
            book_view($book, null, false, $course, $cm, $context);
            $chapterid = $firstchapterid;
        }

        // Check if book is empty (warning).
        if (!$chapterid) {
            $warnings[] = array(
                'item' => 'book',
                'itemid' => $book->id,
                'warningcode' => '1',
                'message' => get_string('nocontent', 'mod_book')
            );
        } else {
            $chapter = $DB->get_record('book_chapters', array('id' => $chapterid, 'bookid' => $book->id));
            $viewhidden = has_capability('mod/book:viewhiddenchapters', $context);

            if (!$chapter or ($chapter->hidden and !$viewhidden)) {
                throw new moodle_exception('errorchapter', 'mod_book');
            }

            // Trigger the chapter viewed event.
            $islastchapter = ($chapter->id == $lastchapterid) ? true : false;
            book_view($book, $chapter, $islastchapter, $course, $cm, $context);
        }

        $result = array();
        $result['status'] = true;
        $result['warnings'] = $warnings;
        return $result;
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 3.0
     */
    public static function view_book_returns() {
        return new external_single_structure(
            array(
                'status' => new external_value(PARAM_BOOL, 'status: true if success'),
                'warnings' => new external_warnings()
            )
        );
    }

}
