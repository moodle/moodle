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
 * @subpackage questionnaire
 * @copyright  2011 Robin de Vries <robin@celp.nl>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Choice conversion handler
 */
class moodle1_mod_questionnaire_handler extends moodle1_mod_handler {

    /**
     * Declare the paths in moodle.xml we are able to convert
     *
     * The method returns list of {@link convert_path} instances. For each path returned,
     * at least one of on_xxx_start(), process_xxx() and on_xxx_end() methods must be
     * defined. The method process_xxx() is not executed if the associated path element is
     * empty (i.e. it contains none elements or sub-paths only).
     *
     * Note that the path /MOODLE_BACKUP/COURSE/MODULES/MOD/CHOICE does not
     * actually exist in the file. The last element with the module name was
     * appended by the moodle1_converter class.
     *
     * @return array of {@link convert_path} instances
     */
    public function get_paths() {
        return array(
            new convert_path(
                'questionnaire', '/MOODLE_BACKUP/COURSE/MODULES/MOD/QUESTIONNAIRE',
                array(
                    'renamefields' => array(
                        'summary' => 'intro',
                    ),
                    'newfields' => array(
                        'introformat' => 0,
                    ),
                )
            ),
            new convert_path('survey', '/MOODLE_BACKUP/COURSE/MODULES/MOD/QUESTIONNAIRE/SURVEY'),
            new convert_path('question', '/MOODLE_BACKUP/COURSE/MODULES/MOD/QUESTIONNAIRE/SURVEY/QUESTION'),
            new convert_path('question_choice', '/MOODLE_BACKUP/COURSE/MODULES/MOD/QUESTIONNAIRE/SURVEY/QUESTION/QUESTION_CHOICE'),
        );
    }
    /**
     * This is executed every time we have one /MOODLE_BACKUP/COURSE/MODULES/MOD/QUESTIONNAIRE
     * data available
     */
    public function process_questionnaire($data) {
        // Get the course module id and context id.
        $instanceid = $data['id'];
        $cminfo     = $this->get_cminfo($instanceid);
        $moduleid   = $cminfo['id'];
        $contextid  = $this->converter->get_contextid(CONTEXT_MODULE, $moduleid);

        // We now have all information needed to start writing into the file.
        $this->open_xml_writer("activities/questionnaire_{$moduleid}/questionnaire.xml");
        $this->xmlwriter->begin_tag('activity', array('id' => $instanceid, 'moduleid' => $moduleid,
            'modulename' => 'questionnaire', 'contextid' => $contextid));
        $this->xmlwriter->begin_tag('questionnaire', array('id' => $instanceid));

        unset($data['id']); // We already write it as attribute, do not repeat it as child element.
        foreach ($data as $field => $value) {
            $this->xmlwriter->full_tag($field, $value);
        }
        $this->xmlwriter->begin_tag('surveys');
    }
    /**
     * This is executed when we reach the closing </MOD> tag of our 'questionnaire' path
     */

    public function on_questionnaire_end() {
        // Close questionnaire.xml.
        $this->xmlwriter->end_tag('surveys');
        $this->xmlwriter->end_tag('questionnaire');
        $this->xmlwriter->end_tag('activity');
        $this->close_xml_writer();
    }
    /**
     * This is executed every time we have one /MOODLE_BACKUP/COURSE/MODULES/MOD/QUESTIONNAIRE/SURVEY
     * data available
     */
    public function process_survey($data) {
        $this->xmlwriter->begin_tag('survey', array('id' => $data['id']));
        unset($data['id']); // We already write it as attribute, do not repeat it as child element.
        foreach ($data as $field => $value) {
            $this->xmlwriter->full_tag($field, $value);
        }
        $this->xmlwriter->begin_tag('questions');
    }

    /**
     * This is executed when we reach the closing </SURVEY> tag
     */
    public function on_survey_end() {
        $this->xmlwriter->end_tag('questions');
        $this->xmlwriter->end_tag('survey');
    }

    /**
     * This is executed every time we have one /MOODLE_BACKUP/COURSE/MODULES/MOD/QUESTIONNAIRE/SURVEY/QUESTION
     * data available
     */
    public function process_question($data) {

        $this->xmlwriter->begin_tag('question', array('id' => $data['id']));

        unset($data['id']); // We already write it as attribute, do not repeat it as child element.
        foreach ($data as $field => $value) {
            $this->xmlwriter->full_tag($field, $value);
        }

        $this->xmlwriter->begin_tag('quest_choices');
    }
    /**
     * This is executed when we reach the closing </QUESTION> tag
     */
    public function on_question_end() {
        $this->xmlwriter->end_tag('quest_choices');
        $this->xmlwriter->end_tag('question');

    }

    /**
     * This is executed every time we have one /MOODLE_BACKUP/COURSE/MODULES/MOD/QUESTIONNAIRE/SURVEY/QUESTION/QUESTION_CHOICE
     * data available
     */
    public function process_question_choice($data) {
        $this->write_xml('quest_choice', $data, array('/question_choice/id'));
    }

}