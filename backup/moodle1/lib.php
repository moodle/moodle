<?php
// This file is part of Book module for Moodle - http://moodle.org/
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
 * Provides support for the conversion of moodle1 backup to the moodle2 format
 *
 * @package    mod
 * @subpackage book
 * @copyright  2011 Tõnis Tartes <t6nis20@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Book conversion handler
 */
class moodle1_mod_book_handler extends moodle1_mod_handler {

    /** @var array in-memory cache for the course module information for the current book  */
    protected $currentcminfo = null;

    /**
     * Declare the paths in moodle.xml we are able to convert
     *
     * The method returns list of {@link convert_path} instances. For each path returned,
     * at least one of on_xxx_start(), process_xxx() and on_xxx_end() methods must be
     * defined. The method process_xxx() is not executed if the associated path element is
     * empty (i.e. it contains none elements or sub-paths only).
     *
     * Note that the path /MOODLE_BACKUP/COURSE/MODULES/MOD/BOOK does not
     * actually exist in the file. The last element with the module name was
     * appended by the moodle1_converter class.
     *
     * @return array of {@link convert_path} instances
     */
    public function get_paths() {
        return array(
            new convert_path('book', '/MOODLE_BACKUP/COURSE/MODULES/MOD/BOOK',
                    array(
                        'renamefields' => array(
                            'summary' => 'intro',
                        ),
                        'newfields' => array(
                            'introformat' => 0,
                        ),
                        'dropfields' => array(
                            'disableprinting'
                        ),
                    )
                ),
            new convert_path('book_chapters', '/MOODLE_BACKUP/COURSE/MODULES/MOD/BOOK/CHAPTERS/CHAPTER',
                    array(
                        'newfields' => array(
                            'contentformat' => 1,
                        ),
                    )
                ),
        );
    }

    /**
     * This is executed every time we have one /MOODLE_BACKUP/COURSE/MODULES/MOD/BOOK
     * data available
     */
    public function process_book($data) {

        // get the course module id and context id
        $instanceid = $data['id'];
        $this->currentcminfo = $this->get_cminfo($instanceid);
        $moduleid   = $this->currentcminfo['id'];
        $contextid  = $this->converter->get_contextid(CONTEXT_MODULE, $moduleid);

        // we now have all information needed to start writing into the file
        $this->open_xml_writer("activities/book_{$moduleid}/book.xml");
        $this->xmlwriter->begin_tag('activity', array('id' => $instanceid, 'moduleid' => $moduleid,
            'modulename' => 'book', 'contextid' => $contextid));
        $this->xmlwriter->begin_tag('book', array('id' => $instanceid));

        unset($data['id']); // we already write it as attribute, do not repeat it as child element
        foreach ($data as $field => $value) {
            $this->xmlwriter->full_tag($field, $value);
        }

    }

    /**
     * This is executed every time we have one /MOODLE_BACKUP/COURSE/MODULES/MOD/BOOK/CHAPTERS/CHAPTER
     * data available
     */
    public function process_book_chapters($data) {

        $this->write_xml('chapter', $data, array('/chapter/id'));

    }

    /**
     * This is executed when the parser reaches the <OPTIONS> opening element
     */
    public function on_book_chapters_start() {

        $this->xmlwriter->begin_tag('chapters');

    }

    /**
     * This is executed when the parser reaches the closing </OPTIONS> element
     */
    public function on_book_chapters_end() {

        $this->xmlwriter->end_tag('chapters');

    }

    /**
     * This is executed when we reach the closing </MOD> tag of our 'book' path
     */
    public function on_book_end() {

        // close book.xml
        $this->xmlwriter->end_tag('book');
        $this->xmlwriter->end_tag('activity');
        $this->close_xml_writer();

    }

}
