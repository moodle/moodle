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
 * Provides support for the conversion of moodle1 backup to the moodle2 format
 *
 * @package    mod
 * @subpackage lesson
 * @copyright  2011 Rossiani Wijaya <rwijaya@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Lesson conversion handler
 */
class moodle1_mod_lesson_handler extends moodle1_mod_handler {

    /**
     * Declare the paths in moodle.xml we are able to convert
     *
     * The method returns list of {@link convert_path} instances.
     * For each path returned, the corresponding conversion method must be
     * defined.
     *
     * Note that the path /MOODLE_BACKUP/COURSE/MODULES/MOD/LESSON does not
     * actually exist in the file. The last element with the module name was
     * appended by the moodle1_converter class.
     *
     * @return array of {@link convert_path} instances
     */
    public function get_paths() {
        return array(
            new convert_path(
                'lesson', '/MOODLE_BACKUP/COURSE/MODULES/MOD/LESSON',
                array(
                    'renamefields' => array(
                        'usegrademax' => 'usemaxgrade',
                    ),
                )
            ),
            new convert_path(
                'lesson_page', '/MOODLE_BACKUP/COURSE/MODULES/MOD/LESSON/PAGES/PAGE',
                array(
                    'newfields' => array(
                        'contentsformat' => FORMAT_MOODLE,
                        'responseformat' => 0,
                    ),
                )
            ),
            new convert_path(
                'lesson_answer', '/MOODLE_BACKUP/COURSE/MODULES/MOD/LESSON/PAGES/PAGE/ANSWERS/ANSWER',
                array(
                    'newfields' => array(
                        'answerformat' => 0,
                    ),
                    'renamefields' => array(
                        'answertext' => 'answer_text',
                    ),
                )
            )
        );
    }

    /**
     * This is executed every time we have one /MOODLE_BACKUP/COURSE/MODULES/MOD/LESSON
     * data available
     */
    public function process_lesson($data) {
        // get the course module id and context id
        $instanceid = $data['id'];
        $cminfo     = $this->get_cminfo($instanceid);
        $moduleid   = $cminfo['id'];
        $contextid  = $this->converter->get_contextid(CONTEXT_MODULE, $moduleid);

        // we now have all information needed to start writing into the file
        $this->open_xml_writer("activities/lesson_{$moduleid}/lesson.xml");
        $this->xmlwriter->begin_tag('activity', array('id' => $instanceid, 'moduleid' => $moduleid,
            'modulename' => 'lesson', 'contextid' => $contextid));
        $this->xmlwriter->begin_tag('lesson', array('id' => $instanceid));

        unset($data['id']); // we already write it as attribute, do not repeat it as child element

        foreach ($data as $field => $value) {
            $this->xmlwriter->full_tag($field, $value);
        }

        $this->xmlwriter->begin_tag('pages');
    }

    /**
     * This is executed every time we have one /MOODLE_BACKUP/COURSE/MODULES/MOD/LESSON/PAGES/PAGE
     * data available
     */
    public function process_lesson_page($data) {
        $this->xmlwriter->begin_tag('page', array('id' => $data['pageid']));
        foreach ($data as $field => $value) {
            $this->xmlwriter->full_tag($field, $value);
        }

        $this->xmlwriter->begin_tag('answers');
    }

    /**
     * This is executed every time we have one /MOODLE_BACKUP/COURSE/MODULES/MOD/LESSON/PAGES/PAGE/ANSWERS/ANSWER
     * data available
     */
    public function process_lesson_answer($data) {
        $this->write_xml('answer', $data, array('/answer/id'));
    }

    /**
     * This is executed when we reach the closing </pages>tag of our 'page' path
     */
    public function on_lesson_page_end(){
        $this->xmlwriter->end_tag('answers');
        $this->xmlwriter->end_tag('page');
    }

    /**
     * This is executed when we reach the closing </MOD> tag of our 'lesson' path
     */
    public function on_lesson_end() {
        $this->xmlwriter->end_tag('pages');
        $this->xmlwriter->end_tag('lesson');
        $this->xmlwriter->end_tag('activity');
        $this->close_xml_writer();
    }
}