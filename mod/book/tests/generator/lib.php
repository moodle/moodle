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
 * mod_book data generator.
 *
 * @package    mod_book
 * @category   test
 * @copyright  2013 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * mod_book data generator class.
 *
 * @package    mod_book
 * @category   test
 * @copyright  2013 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_book_generator extends testing_module_generator {

    /**
     * @var int keep track of how many chapters have been created.
     */
    protected $chaptercount = 0;

    /**
     * To be called from data reset code only,
     * do not use in tests.
     * @return void
     */
    public function reset() {
        $this->chaptercount = 0;
        parent::reset();
    }

    public function create_instance($record = null, array $options = null) {
        global $CFG;
        require_once("$CFG->dirroot/mod/book/locallib.php");

        $record = (object)(array)$record;

        if (!isset($record->numbering)) {
            $record->numbering = BOOK_NUM_NUMBERS;
        }
        if (!isset($record->customtitles)) {
            $record->customtitles = 0;
        }

        return parent::create_instance($record, (array)$options);
    }

    public function create_chapter($record = null, array $options = null) {
        global $DB;

        $record = (object) (array) $record;
        $options = (array) $options;
        $this->chaptercount++;

        if (empty($record->bookid)) {
            throw new coding_exception('Chapter generator requires $record->bookid');
        }

        if (empty($record->title)) {
            $record->title = "Chapter {$this->chaptercount}";
        }
        if (empty($record->pagenum)) {
            $record->pagenum = 1;
        }
        if (!isset($record->subchapter)) {
            $record->subchapter = 0;
        }
        if (!isset($record->hidden)) {
            $record->hidden = 0;
        }
        if (!isset($record->importsrc)) {
            $record->importsrc = '';
        }
        if (!isset($record->content)) {
            $record->content = "Chapter {$this->chaptercount} content";
        }
        if (!isset($record->contentformat)) {
            $record->contentformat = FORMAT_MOODLE;
        }
        if (!isset($record->timecreated)) {
            $record->timecreated = time();
        }
        if (!isset($record->timemodified)) {
            $record->timemodified = time();
        }

        // Make room for new page.
        $sql = "UPDATE {book_chapters}
                   SET pagenum = pagenum + 1
                 WHERE bookid = ? AND pagenum >= ?";
        $DB->execute($sql, array($record->bookid, $record->pagenum));
        $record->id = $DB->insert_record('book_chapters', $record);

        $sql = "UPDATE {book}
                   SET revision = revision + 1
                 WHERE id = ?";
        $DB->execute($sql, array($record->bookid));

        if (property_exists($record, 'tags')) {
            $cm = get_coursemodule_from_instance('book', $record->bookid);
            $tags = is_array($record->tags) ? $record->tags : preg_split('/,/', $record->tags);

            core_tag_tag::set_item_tags('mod_book', 'book_chapters', $record->id,
                context_module::instance($cm->id), $tags);
        }

        return $record;
    }

    public function create_content($instance, $record = array()) {
        $record = (array)$record + array(
            'bookid' => $instance->id
        );
        return $this->create_chapter($record);
    }

}
