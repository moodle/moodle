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
 * @package    mod_survey
 * @subpackage backup-moodle2
 * @copyright  2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Define all the backup steps that will be used by the backup_survey_activity_task
 */

/**
 * Define the complete survey structure for backup, with file and id annotations
 */
class backup_survey_activity_structure_step extends backup_activity_structure_step {

    protected function define_structure() {

        // To know if we are including userinfo
        $userinfo = $this->get_setting_value('userinfo');

        // Define each element separated
        $survey = new backup_nested_element('survey', array('id'), array(
            'name', 'intro', 'introformat', 'template',
            'questions', 'days', 'timecreated', 'timemodified', 'completionsubmit'));

        $answers = new backup_nested_element('answers');

        $answer = new backup_nested_element('answer', array('id'), array(
            'userid', 'question', 'time', 'answer1',
            'answer2'));

        $analysis = new backup_nested_element('analysis');

        $analys = new backup_nested_element('analys', array('id'), array(
            'userid', 'notes'));

        // Build the tree
        $survey->add_child($answers);
        $answers->add_child($answer);

        $survey->add_child($analysis);
        $analysis->add_child($analys);

        // Define sources
        $survey->set_source_table('survey', array('id' => backup::VAR_ACTIVITYID));

        $answer->set_source_table('survey_answers', array('survey' => backup::VAR_PARENTID));

        $analys->set_source_table('survey_analysis', array('survey' => backup::VAR_PARENTID));

        // Define id annotations
        $answer->annotate_ids('user', 'userid');
        $analys->annotate_ids('user', 'userid');

        // Define file annotations
        $survey->annotate_files('mod_survey', 'intro', null); // This file area hasn't itemid

        // Return the root element (survey), wrapped into standard activity structure
        return $this->prepare_activity_structure($survey);
    }
}
