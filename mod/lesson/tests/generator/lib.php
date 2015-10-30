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
 * mod_lesson data generator.
 *
 * @package    mod_lesson
 * @category   test
 * @copyright  2013 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * mod_lesson data generator class.
 *
 * @package    mod_lesson
 * @category   test
 * @copyright  2013 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_lesson_generator extends testing_module_generator {

    /**
     * @var int keep track of how many pages have been created.
     */
    protected $pagecount = 0;

    /**
     * To be called from data reset code only,
     * do not use in tests.
     * @return void
     */
    public function reset() {
        $this->pagecount = 0;
        parent::reset();
    }

    public function create_instance($record = null, array $options = null) {
        global $CFG;

        // Add default values for lesson.
        $record = (array)$record + array(
            'progressbar' => 0,
            'ongoing' => 0,
            'displayleft' => 0,
            'displayleftif' => 0,
            'slideshow' => 0,
            'maxanswers' => $CFG->lesson_maxanswers,
            'feedback' => 0,
            'activitylink' => 0,
            'available' => 0,
            'deadline' => 0,
            'usepassword' => 0,
            'password' => '',
            'dependency' => 0,
            'timespent' => 0,
            'completed' => 0,
            'gradebetterthan' => 0,
            'modattempts' => 0,
            'review' => 0,
            'maxattempts' => 1,
            'nextpagedefault' => $CFG->lesson_defaultnextpage,
            'maxpages' => 0,
            'practice' => 0,
            'custom' => 1,
            'retake' => 0,
            'usemaxgrade' => 0,
            'minquestions' => 0,
            'grade' => 100,
        );
        if (!isset($record['mediafile'])) {
            require_once($CFG->libdir.'/filelib.php');
            $record['mediafile'] = file_get_unused_draft_itemid();
        }

        return parent::create_instance($record, (array)$options);
    }

    public function create_content($lesson, $record = array()) {
        global $DB, $CFG;
        require_once($CFG->dirroot.'/mod/lesson/locallib.php');
        $now = time();
        $this->pagecount++;
        $record = (array)$record + array(
            'lessonid' => $lesson->id,
            'title' => 'Lesson page '.$this->pagecount,
            'timecreated' => $now,
            'qtype' => 20, // LESSON_PAGE_BRANCHTABLE
            'pageid' => 0, // By default insert in the beginning.
        );
        if (!isset($record['contents_editor'])) {
            $record['contents_editor'] = array(
                'text' => 'Contents of lesson page '.$this->pagecount,
                'format' => FORMAT_MOODLE,
                'itemid' => 0,
            );
        }
        $context = context_module::instance($lesson->cmid);
        $page = lesson_page::create((object)$record, new lesson($lesson), $context, $CFG->maxbytes);
        return $DB->get_record('lesson_pages', array('id' => $page->id), '*', MUST_EXIST);
    }
}
