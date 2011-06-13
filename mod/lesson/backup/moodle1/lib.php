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
    // @var array of answers, when there are more that 4 answers, we need to fix <jumpto>.
    protected $answers;

    // @var stdClass a page object of the current page
    protected $page;
    // @var array of page objects to store entire pages, to help generate nextpageid and prevpageid in data
    protected $pages;

    /** @var moodle1_file_manager */
    protected $fileman = null;

    /** @var int cmid */
    protected $moduleid = null;

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
                        'nextpageid' => 0, //set to default to the next sequencial page in process_lesson_page()
                        'prevpageid' => 0
                    ),
                )
            ),
            new convert_path(
                'lesson_pages', '/MOODLE_BACKUP/COURSE/MODULES/MOD/LESSON/PAGES'
            ),
            new convert_path(
                'lesson_answer', '/MOODLE_BACKUP/COURSE/MODULES/MOD/LESSON/PAGES/PAGE/ANSWERS/ANSWER',
                array(
                    'newfields' => array(
                        'answerformat' => 0,
                        'responseformat' => 0,
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
        $instanceid     = $data['id'];
        $cminfo         = $this->get_cminfo($instanceid);
        $this->moduleid = $cminfo['id'];
        $contextid      = $this->converter->get_contextid(CONTEXT_MODULE, $this->moduleid);

        // get a fresh new file manager for this instance
        $this->fileman = $this->converter->get_file_manager($contextid, 'mod_lesson');

        // migrate referenced local media files
        if (!empty($data['mediafile']) and strpos($data['mediafile'], '://') === false) {
            $this->fileman->filearea = 'mediafile';
            $this->fileman->itemid   = 0;
            try {
                $this->fileman->migrate_file('course_files/'.$data['mediafile']);
            } catch (moodle1_convert_exception $e) {
                // the file probably does not exist
                $this->log('error migrating lesson mediafile', backup::LOG_WARNING, 'course_files/'.$data['mediafile']);
            }
        }

        // start writing lesson.xml
        $this->open_xml_writer("activities/lesson_{$this->moduleid}/lesson.xml");
        $this->xmlwriter->begin_tag('activity', array('id' => $instanceid, 'moduleid' => $this->moduleid,
            'modulename' => 'lesson', 'contextid' => $contextid));
        $this->xmlwriter->begin_tag('lesson', array('id' => $instanceid));

        unset($data['id']);
        foreach ($data as $field => $value) {
            $this->xmlwriter->full_tag($field, $value);
        }

        return $data;
    }

    public function on_lesson_pages_start() {
        $this->xmlwriter->begin_tag('pages');
    }

    /**
     * This is executed every time we have one /MOODLE_BACKUP/COURSE/MODULES/MOD/LESSON/PAGES/PAGE
     * data available
     */
    public function process_lesson_page($data) {
        global $CFG;

        // replay the upgrade step 2009120801
        if ($CFG->texteditors !== 'textarea') {
            $data['contents'] = text_to_html($data['contents'], false, false, true);
            $data['contentsformat'] = FORMAT_HTML;
        }

        // store page in pages
        $this->page = new stdClass();
        $this->page->id = $data['pageid'];
        unset($data['pageid']);
        $this->page->data = $data;
    }

    /**
     * This is executed every time we have one /MOODLE_BACKUP/COURSE/MODULES/MOD/LESSON/PAGES/PAGE/ANSWERS/ANSWER
     * data available
     */
    public function process_lesson_answer($data) {

        // replay the upgrade step 2010072003
        $flags = intval($data['flags']);
        if ($flags & 1) {
            $data['answer_text']  = text_to_html($data['answer_text'], false, false, true);
            $data['answerformat'] = FORMAT_HTML;
        }
        if ($flags & 2) {
            $data['response']       = text_to_html($data['response'], false, false, true);
            $data['responseformat'] = FORMAT_HTML;
        }

        // buffer for conversion of <jumpto> in line with
        // upgrade step 2010121400 from mod/lesson/db/upgrade.php
        $this->answers[] = $data;
    }

    public function on_lesson_page_end() {
        $this->page->answers = $this->answers;
        unset($this->answers);
        $this->pages[] = $this->page;
        unset($this->page);
    }

    public function on_lesson_pages_end() {
        $prevpageid = 0;

        foreach ($this->pages as $page) {
            $curpageid = $page->id;
            // if there are more pages, set nextpageid
            if (isset($this->pages[1])) {
                $nextpage = $this->pages[1];
                $nextpageid = $nextpage->id;
            } else {
                //theres only one page left
                $nextpageid = 0;
            }
            //mince nextpageid and prevpageid
            $page->data['nextpageid'] = $nextpageid;
            $page->data['prevpageid'] = $prevpageid;

            array_shift($this->pages);

            // write out each page data
            $this->xmlwriter->begin_tag('page', array('id' => $page->id));

            foreach ($page->data as $field => $value) {
                $this->xmlwriter->full_tag($field, $value);
            }

            //effectively on_lesson_answers_end(), where we write out answers for current page.
            $answers = $page->answers;

            $this->xmlwriter->begin_tag('answers');
            if (count($answers) > 3) {
                if ($answers[0]['jumpto'] !== '0' || $answers[1]['jumpto'] !== '0') {
                    if ($answers[2]['jumpto'] !== '0') {
                        $answers[0]['jumpto'] = $answers[2]['jumpto'];
                        $answers[2]['jumpto'] = '0';
                    }
                    if ($answers[3]['jumpto'] !== '0') {
                        $answers[1]['jumpto'] = $answers[3]['jumpto'];
                        $answers[3]['jumpto'] = '0';
                    }
                }
            }

            foreach ($answers as $data) {
                $this->write_xml('answer', $data, array('/answer/id'));
            }
            $this->xmlwriter->end_tag('answers');

            // answers is now closed for current page. Ending the page.
            $this->xmlwriter->end_tag('page');

            // set prevpageid
            $prevpageid = $curpageid;
        }
        $this->xmlwriter->end_tag('pages');
    }

    /**
     * This is executed when we reach the closing </MOD> tag of our 'lesson' path
     */
    public function on_lesson_end() {
        // finish writing lesson.xml
        $this->xmlwriter->end_tag('lesson');
        $this->xmlwriter->end_tag('activity');
        $this->close_xml_writer();

        // write inforef.xml
        $this->open_xml_writer("activities/lesson_{$this->moduleid}/inforef.xml");
        $this->xmlwriter->begin_tag('inforef');
        $this->xmlwriter->begin_tag('fileref');
        foreach ($this->fileman->get_fileids() as $fileid) {
            $this->write_xml('file', array('id' => $fileid));
        }
        $this->xmlwriter->end_tag('fileref');
        $this->xmlwriter->end_tag('inforef');
        $this->close_xml_writer();
    }
}
