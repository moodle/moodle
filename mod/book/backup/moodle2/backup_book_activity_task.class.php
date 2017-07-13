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
 * Description of book backup task
 *
 * @package    mod_book
 * @copyright  2010-2011 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot.'/mod/book/backup/moodle2/backup_book_stepslib.php');    // Because it exists (must)
require_once($CFG->dirroot.'/mod/book/backup/moodle2/backup_book_settingslib.php'); // Because it exists (optional)

class backup_book_activity_task extends backup_activity_task {

    /**
     * Define (add) particular settings this activity can have
     *
     * @return void
     */
    protected function define_my_settings() {
        // No particular settings for this activity
    }

    /**
     * Define (add) particular steps this activity can have
     *
     * @return void
     */
    protected function define_my_steps() {
        // book only has one structure step
        $this->add_step(new backup_book_activity_structure_step('book_structure', 'book.xml'));
    }

    /**
     * Code the transformations to perform in the activity in
     * order to get transportable (encoded) links
     *
     * @param string $content
     * @return string encoded content
     */
    static public function encode_content_links($content) {
        global $CFG;

        $base = preg_quote($CFG->wwwroot, "/");

        // Link to the list of books
        $search  = "/($base\/mod\/book\/index.php\?id=)([0-9]+)/";
        $content = preg_replace($search, '$@BOOKINDEX*$2@$', $content);

        // Link to book view by moduleid
        $search  = "/($base\/mod\/book\/view.php\?id=)([0-9]+)(&|&amp;)chapterid=([0-9]+)/";
        $content = preg_replace($search, '$@BOOKVIEWBYIDCH*$2*$4@$', $content);

        $search  = "/($base\/mod\/book\/view.php\?id=)([0-9]+)/";
        $content = preg_replace($search, '$@BOOKVIEWBYID*$2@$', $content);

        // Link to book view by bookid
        $search  = "/($base\/mod\/book\/view.php\?b=)([0-9]+)(&|&amp;)chapterid=([0-9]+)/";
        $content = preg_replace($search, '$@BOOKVIEWBYBCH*$2*$4@$', $content);

        $search  = "/($base\/mod\/book\/view.php\?b=)([0-9]+)/";
        $content = preg_replace($search, '$@BOOKVIEWBYB*$2@$', $content);

        return $content;
    }
}
