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
 * Define all the backup steps that will be used by the backup_adaptivequiz_activity_task.
 *
 * @package   mod_adaptivequiz
 * @copyright 2013 onwards Remote-Learner {@link http://www.remote-learner.ca/}
 * @copyright 2022 onwards Vitaly Potenko <potenkov@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backup_adaptivequiz_activity_structure_step extends backup_questions_activity_structure_step {

    /**
     * Define the backup structure.
     *
     * @return backup_nested_element The root element (adaptivequiz), wrapped into standard activity structure.
     */
    protected function define_structure() {
        // To know if we are including userinfo.
        $userinfo = $this->get_setting_value('userinfo');

        // Define each element separated.
        $nodes = ['name', 'intro', 'introformat', 'attempts', 'password', 'browsersecurity', 'attemptfeedback',
            'attemptfeedbackformat', 'showabilitymeasure', 'showattemptprogress', 'highestlevel', 'lowestlevel', 'minimumquestions',
            'maximumquestions', 'standarderror', 'startinglevel', 'timecreated', 'timemodified', 'completionattemptcompleted'];
        $adaptivequiz = new backup_nested_element('adaptivequiz', ['id'], $nodes);

        // Attempts.
        $adaptiveattempts = new backup_nested_element('adaptiveattempts');
        $nodes = ['userid', 'uniqueid', 'attemptstate', 'attemptstopcriteria', 'questionsattempted', 'difficultysum',
            'standarderror', 'measure', 'timecreated', 'timemodified'];
        $adaptiveattempt = new backup_nested_element('adaptiveattempt', ['id'], $nodes);

        // This module is using questions, so produce the related question states and sessions.
        // attaching them to the $attempt element based in 'uniqueid' matching.
        $this->add_question_usages($adaptiveattempt, 'uniqueid');

        // Activity to question categories reference.
        $adaptivequestioncats = new backup_nested_element('adatpivequestioncats');
        $adaptivequestioncat = new backup_nested_element('adatpivequestioncat', ['id'], ['questioncategory']);

        // Build the tree.
        $adaptivequiz->add_child($adaptiveattempts);
        $adaptiveattempts->add_child($adaptiveattempt);

        $adaptivequiz->add_child($adaptivequestioncats);
        $adaptivequestioncats->add_child($adaptivequestioncat);

        // Define sources.
        $adaptivequiz->set_source_table('adaptivequiz', ['id' => backup::VAR_ACTIVITYID]);
        $adaptivequestioncat->set_source_table('adaptivequiz_question', ['instance' => backup::VAR_PARENTID]);

        // All the rest of elements only happen if we are including user info.
        if ($userinfo) {
            $sql = 'SELECT *
                      FROM {adaptivequiz_attempt}
                     WHERE instance = :instance';
            $param = array('instance' => backup::VAR_PARENTID);
            $adaptiveattempt->set_source_sql($sql, $param);
        }

        // Define id annotations.
        $adaptivequestioncat->annotate_ids('question_categories', 'questioncategory');
        $adaptiveattempt->annotate_ids('user', 'userid');

        $adaptivequiz->annotate_files('mod_adaptivequiz', 'intro', null); // This file area hasn't itemid.

        return $this->prepare_activity_structure($adaptivequiz);
    }
}
