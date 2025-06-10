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
 * Block displaying information about whether or not there is an etextbook
 * for the course
 *
 * @package    block_etextbook
 * @copyright  2016 Lousiana State University - David Elliott, Robert Russo, Chad Mazilly
 * @author     David Elliott <delliott@lsu.edu> - Along with LSU Moodle Development Team (Robert Russo, Chad Mazily)
 *             and LSU Libraries Staff (Emily Frank, David Comeaux, and Jason Peak)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Block displaying information about whether or not there is an etextbook
 * for the course
 *
 * @package    block_etextbook
 */

defined('MOODLE_INTERNAL') || die();

class block_etextbook extends block_base {
    /**
     * Function creates block
     * @return void
     */
    public function init() {
        $this->title = get_string('etextbook', 'block_etextbook');
    }
    public function applicable_formats() {
        return array('site' => false, 'my' => false, 'course-view' => true);
    }
    /**
     * Function gets the xml from the library and populates the blocks content
     * @return string $this->content
     * @access public
     */
    public function get_content() {
        GLOBAL $COURSE, $DB;
        $etextbooktable = "block_etextbook";
        if ($DB->record_exists($etextbooktable, array('courseid' => $COURSE->id))) {
            $this->content = new \stdClass;
            $this->content->text = '';
            $arrayofbooks = $DB->get_records($etextbooktable, array("courseid" => $COURSE->id));
            $htmldiv = "";
            $arrayoftitles = [];
            foreach ($arrayofbooks as $book) {
                if (!in_array($book->title, $arrayoftitles)) {
                    $htmldiv .= '<a href = "' . $book->book_url . '">' . $book->title;
                    $htmldiv .= '<img class = "img-rounded img-responsive etextimg" src = "' . $book->img_url . '"></a>';
                    $arrayoftitles[] = $book->title;
                }
            }
            $htmldiv .= get_string('linktolsulibraries', 'block_etextbook');

            $this->content->text .= html_writer::div($htmldiv, 'lsulib-etext');
        } else {
            return $this->content; // No Book for this course.

        }
    }

    public function has_config() {
        return true;
    }
}
