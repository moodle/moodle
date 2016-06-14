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
 * Based off of a template @ http://docs.moodle.org/dev/Backup_1.9_conversion_for_developers
 *
 * @package    mod_quiz
 * @copyright  2011 Aparup Banerjee <aparup@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Quiz conversion handler
 */
class moodle1_mod_quiz_handler extends moodle1_mod_handler {

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
     * Note that the path /MOODLE_BACKUP/COURSE/MODULES/MOD/QUIZ does not
     * actually exist in the file. The last element with the module name was
     * appended by the moodle1_converter class.
     *
     * @return array of {@link convert_path} instances
     */
    public function get_paths() {
        return array(
            new convert_path(
                'quiz', '/MOODLE_BACKUP/COURSE/MODULES/MOD/QUIZ',
                array(
                    'newfields' => array(
                        'showuserpicture'       => 0,
                        'questiondecimalpoints' => -1,
                        'introformat'           => 0,
                        'showblocks'            => 0,
                    ),
                )
            ),
            new convert_path('quiz_question_instances',
                    '/MOODLE_BACKUP/COURSE/MODULES/MOD/QUIZ/QUESTION_INSTANCES'),
            new convert_path('quiz_question_instance',
                    '/MOODLE_BACKUP/COURSE/MODULES/MOD/QUIZ/QUESTION_INSTANCES/QUESTION_INSTANCE',
                array(
                    'renamefields' => array(
                        'question' => 'questionid',
                        'grade'    => 'maxmark',
                    ),
                )
            ),
            new convert_path('quiz_feedbacks',
                    '/MOODLE_BACKUP/COURSE/MODULES/MOD/QUIZ/FEEDBACKS'),
            new convert_path('quiz_feedback',
                    '/MOODLE_BACKUP/COURSE/MODULES/MOD/QUIZ/FEEDBACKS/FEEDBACK',
                array(
                    'newfields' => array(
                        'feedbacktextformat' => FORMAT_HTML,
                    )
                )
            )
        );
    }

    /**
     * This is executed every time we have one /MOODLE_BACKUP/COURSE/MODULES/MOD/QUIZ
     * data available
     */
    public function process_quiz($data) {
        global $CFG;

        // Replay the upgrade step 2008081501.
        if (is_null($data['sumgrades'])) {
            $data['sumgrades'] = 0;
            // TODO for user data: quiz_attempts SET sumgrades=0 WHERE sumgrades IS NULL.
            // TODO for user data: quiz_grades.grade should be not be null , convert to default 0.
        }

        // Replay the upgrade step 2009042000.
        if ($CFG->texteditors !== 'textarea') {
            $data['intro']       = text_to_html($data['intro'], false, false, true);
            $data['introformat'] = FORMAT_HTML;
        }

        // Replay the upgrade step 2009031001.
        $data['timelimit'] *= 60;

        // Get the course module id and context id.
        $instanceid     = $data['id'];
        $cminfo         = $this->get_cminfo($instanceid);
        $this->moduleid = $cminfo['id'];
        $contextid      = $this->converter->get_contextid(CONTEXT_MODULE, $this->moduleid);

        // Get a fresh new file manager for this instance.
        $this->fileman = $this->converter->get_file_manager($contextid, 'mod_quiz');

        // Convert course files embedded into the intro.
        $this->fileman->filearea = 'intro';
        $this->fileman->itemid   = 0;
        $data['intro'] = moodle1_converter::migrate_referenced_files(
                $data['intro'], $this->fileman);

        // Start writing quiz.xml.
        $this->open_xml_writer("activities/quiz_{$this->moduleid}/quiz.xml");
        $this->xmlwriter->begin_tag('activity', array('id' => $instanceid,
                'moduleid' => $this->moduleid, 'modulename' => 'quiz',
                'contextid' => $contextid));
        $this->xmlwriter->begin_tag('quiz', array('id' => $instanceid));

        foreach ($data as $field => $value) {
            if ($field <> 'id') {
                $this->xmlwriter->full_tag($field, $value);
            }
        }

        return $data;
    }

    public function on_quiz_question_instances_start() {
        $this->xmlwriter->begin_tag('question_instances');
    }

    public function on_quiz_question_instances_end() {
        $this->xmlwriter->end_tag('question_instances');
    }

    public function process_quiz_question_instance($data) {
        $this->write_xml('question_instance', $data, array('/question_instance/id'));
    }

    public function on_quiz_feedbacks_start() {
        $this->xmlwriter->begin_tag('feedbacks');
    }

    public function on_quiz_feedbacks_end() {
        $this->xmlwriter->end_tag('feedbacks');
    }

    public function process_quiz_feedback($data) {
        // Replay the upgrade step 2010122302.
        if (is_null($data['mingrade'])) {
            $data['mingrade'] = 0;
        }
        if (is_null($data['maxgrade'])) {
            $data['maxgrade'] = 0;
        }

        $this->write_xml('feedback', $data, array('/feedback/id'));
    }

    /**
     * This is executed when we reach the closing </MOD> tag of our 'quiz' path
     */
    public function on_quiz_end() {

        // Append empty <overrides> subpath element.
        $this->write_xml('overrides', array());

        // Finish writing quiz.xml.
        $this->xmlwriter->end_tag('quiz');
        $this->xmlwriter->end_tag('activity');
        $this->close_xml_writer();

        // Write inforef.xml.
        $this->open_xml_writer("activities/quiz_{$this->moduleid}/inforef.xml");
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
