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
use mod_questionnaire\feedback\section;

/**
 * Define all the restore steps that will be used by the restore_questionnaire_activity_task.
 *
 * Structure step to restore one questionnaire activity.
 *
 * @package mod_questionnaire
 * @copyright  2016 Mike Churchward (mike.churchward@poetgroup.org)
 * @author     Mike Churchward
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_questionnaire_activity_structure_step extends restore_activity_structure_step {

    /**
     * @var array $olddependquestions Contains any question id's with dependencies.
     */
    protected $olddependquestions = [];

    /**
     * @var array $olddependchoices Contains any choice id's for questions with dependencies.
     */
    protected $olddependchoices = [];

    /**
     * @var array $olddependencies Contains the old id's from the dependencies array.
     */
    protected $olddependencies = [];

    /**
     * Implementation of define_structure.
     * @return mixed
     */
    protected function define_structure() {

        $paths = array();
        $userinfo = $this->get_setting_value('userinfo');

        $paths[] = new restore_path_element('questionnaire', '/activity/questionnaire');
        $paths[] = new restore_path_element('questionnaire_survey', '/activity/questionnaire/surveys/survey');
        $paths[] = new restore_path_element('questionnaire_fb_sections',
                        '/activity/questionnaire/surveys/survey/fb_sections/fb_section');
        $paths[] = new restore_path_element('questionnaire_feedback',
                        '/activity/questionnaire/surveys/survey/fb_sections/fb_section/feedbacks/feedback');
        $paths[] = new restore_path_element('questionnaire_question',
                        '/activity/questionnaire/surveys/survey/questions/question');
        $paths[] = new restore_path_element('questionnaire_quest_choice',
                        '/activity/questionnaire/surveys/survey/questions/question/quest_choices/quest_choice');
        $paths[] = new restore_path_element('questionnaire_dependency',
                '/activity/questionnaire/surveys/survey/questions/question/quest_dependencies/quest_dependency');

        if ($userinfo) {
            if ($this->task->get_old_moduleversion() < 2018050102) {
                // Old system.
                $paths[] = new restore_path_element('questionnaire_attempt', '/activity/questionnaire/attempts/attempt');
                $paths[] = new restore_path_element('questionnaire_response',
                    '/activity/questionnaire/attempts/attempt/responses/response');
                $paths[] = new restore_path_element('questionnaire_response_bool',
                    '/activity/questionnaire/attempts/attempt/responses/response/response_bools/response_bool');
                $paths[] = new restore_path_element('questionnaire_response_date',
                    '/activity/questionnaire/attempts/attempt/responses/response/response_dates/response_date');
                $paths[] = new restore_path_element('questionnaire_response_multiple',
                    '/activity/questionnaire/attempts/attempt/responses/response/response_multiples/response_multiple');
                $paths[] = new restore_path_element('questionnaire_response_other',
                    '/activity/questionnaire/attempts/attempt/responses/response/response_others/response_other');
                $paths[] = new restore_path_element('questionnaire_response_rank',
                    '/activity/questionnaire/attempts/attempt/responses/response/response_ranks/response_rank');
                $paths[] = new restore_path_element('questionnaire_response_single',
                    '/activity/questionnaire/attempts/attempt/responses/response/response_singles/response_single');
                $paths[] = new restore_path_element('questionnaire_response_text',
                    '/activity/questionnaire/attempts/attempt/responses/response/response_texts/response_text');

            } else {
                // New system.
                $paths[] = new restore_path_element('questionnaire_response', '/activity/questionnaire/responses/response');
                $paths[] = new restore_path_element('questionnaire_response_bool',
                    '/activity/questionnaire/responses/response/response_bools/response_bool');
                $paths[] = new restore_path_element('questionnaire_response_date',
                    '/activity/questionnaire/responses/response/response_dates/response_date');
                $paths[] = new restore_path_element('questionnaire_response_multiple',
                    '/activity/questionnaire/responses/response/response_multiples/response_multiple');
                $paths[] = new restore_path_element('questionnaire_response_other',
                    '/activity/questionnaire/responses/response/response_others/response_other');
                $paths[] = new restore_path_element('questionnaire_response_rank',
                    '/activity/questionnaire/responses/response/response_ranks/response_rank');
                $paths[] = new restore_path_element('questionnaire_response_single',
                    '/activity/questionnaire/responses/response/response_singles/response_single');
                $paths[] = new restore_path_element('questionnaire_response_text',
                    '/activity/questionnaire/responses/response/response_texts/response_text');
            }
        }

        // Return the paths wrapped into standard activity structure.
        return $this->prepare_activity_structure($paths);
    }

    /**
     * Implementation of process_questionnaire.
     * @param array $data
     */
    protected function process_questionnaire($data) {
        global $DB;

        $data = (object)$data;
        $data->course = $this->get_courseid();

        $data->opendate = $this->apply_date_offset($data->opendate);
        $data->closedate = $this->apply_date_offset($data->closedate);
        $data->timemodified = $this->apply_date_offset($data->timemodified);

        // Insert the questionnaire record.
        $newitemid = $DB->insert_record('questionnaire', $data);
        // Immediately after inserting "activity" record, call this.
        $this->apply_activity_instance($newitemid);
    }

    /**
     * Process the survey table.
     * @param array $data
     */
    protected function process_questionnaire_survey($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->courseid = $this->get_courseid();

        // Check for a 'feedbacksections' value larger than 2, and limit it to 2. As of 3.5.1 this has a different meaning.
        if ($data->feedbacksections > 2) {
            $data->feedbacksections = 2;
        }

        // Insert the questionnaire_survey record.
        $newitemid = $DB->insert_record('questionnaire_survey', $data);
        $this->set_mapping('questionnaire_survey', $oldid, $newitemid, true);

        // Update the questionnaire record we just created with the new survey id.
        $DB->set_field('questionnaire', 'sid', $newitemid, array('id' => $this->get_new_parentid('questionnaire')));
    }

    /**
     * Process the questions.
     * @param array $data
     */
    protected function process_questionnaire_question($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->surveyid = $this->get_new_parentid('questionnaire_survey');

        // Insert the questionnaire_question record.
        $newitemid = $DB->insert_record('questionnaire_question', $data);
        $this->set_mapping('questionnaire_question', $oldid, $newitemid, true);

        if (isset($data->dependquestion) && ($data->dependquestion > 0)) {
            // Questions using the old dependency system will need to be processed and restored using the new system.
            // See CONTRIB-6787.
            $this->olddependquestions[$newitemid] = $data->dependquestion;
            $this->olddependchoices[$newitemid] = $data->dependchoice;
        }
    }

    /**
     * Process the feedback sections.
     * $qid is unused, but is needed in order to get the $key elements of the array. Suppress PHPMD warning.
     * @param array $data
     */
    protected function process_questionnaire_fb_sections($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->surveyid = $this->get_new_parentid('questionnaire_survey');

        // If this questionnaire has separate sections feedbacks.
        if (isset($data->scorecalculation)) {
            $scorecalculation = section::decode_scorecalculation($data->scorecalculation);
            $newscorecalculation = array();
            foreach ($scorecalculation as $qid => $val) {
                $newqid = $this->get_mappingid('questionnaire_question', $qid);
                $newscorecalculation[$newqid] = $val;
            }
            $data->scorecalculation = serialize($newscorecalculation);
        }

        // Insert the questionnaire_fb_sections record.
        $newitemid = $DB->insert_record('questionnaire_fb_sections', $data);
        $this->set_mapping('questionnaire_fb_sections', $oldid, $newitemid, true);
    }

    /**
     * Process feedback.
     * @param array $data
     */
    protected function process_questionnaire_feedback($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->sectionid = $this->get_new_parentid('questionnaire_fb_sections');

        // Insert the questionnaire_feedback record.
        $newitemid = $DB->insert_record('questionnaire_feedback', $data);
        $this->set_mapping('questionnaire_feedback', $oldid, $newitemid, true);
    }

    /**
     * Process question choices.
     * @param array $data
     */
    protected function process_questionnaire_quest_choice($data) {
        global $CFG, $DB;

        $data = (object)$data;

        require_once($CFG->dirroot.'/mod/questionnaire/locallib.php');

        // Some old systems had '' instead of NULL. Change it to NULL.
        if ($data->value === '') {
            $data->value = null;
        }

        // Replace the = separator with :: separator in quest_choice content.
        // This fixes radio button options using old "value"="display" formats.
        if (($data->value == null || $data->value == 'NULL') && !preg_match("/^([0-9]{1,3}=.*|!other=.*)$/", $data->content)) {
            $content = questionnaire_choice_values($data->content);
            if (strpos($content->text, '=')) {
                $data->content = str_replace('=', '::', $content->text);
            }
        }

        $oldid = $data->id;
        $data->question_id = $this->get_new_parentid('questionnaire_question');

        // Insert the questionnaire_quest_choice record.
        $newitemid = $DB->insert_record('questionnaire_quest_choice', $data);
        $this->set_mapping('questionnaire_quest_choice', $oldid, $newitemid);
    }

    /**
     * Process dependencies.
     * @param array $data
     */
    protected function process_questionnaire_dependency($data) {
        $data = (object)$data;

        $data->questionid = $this->get_new_parentid('questionnaire_question');
        $data->surveyid = $this->get_new_parentid('questionnaire_survey');

        if (isset($data)) {
            $this->olddependencies[] = $data;
        }
    }

    /**
     * Process attempts (these are no longer used).
     * @param array $data
     * @return bool
     */
    protected function process_questionnaire_attempt($data) {
        // New structure will be completed in process_questionnaire_response. Nothing to do here any more.
        return true;
    }

    /**
     * Process responses.
     * @param array $data
     */
    protected function process_questionnaire_response($data) {
        global $DB;

        $data = (object)$data;

        // Older versions of questionnaire used 'username' instead of 'userid'. If 'username' exists, change it to 'userid'.
        if (isset($data->username) && !isset($data->userid)) {
            $data->userid = $data->username;
        }

        $oldid = $data->id;
        $data->questionnaireid = $this->get_new_parentid('questionnaire');
        $data->userid = $this->get_mappingid('user', $data->userid);

        // Insert the questionnaire_response record.
        $newitemid = $DB->insert_record('questionnaire_response', $data);
        $this->set_mapping('questionnaire_response', $oldid, $newitemid);
    }

    /**
     * Process boolean responses.
     * @param array $data
     * @throws dml_exception
     */
    protected function process_questionnaire_response_bool($data) {
        global $DB;

        $data = (object)$data;
        $data->response_id = $this->get_new_parentid('questionnaire_response');
        $data->question_id = $this->get_mappingid('questionnaire_question', $data->question_id);

        // Insert the questionnaire_response_bool record.
        $DB->insert_record('questionnaire_response_bool', $data);
    }

    /**
     * Process date responses.
     * @param array $data
     * @throws dml_exception
     */
    protected function process_questionnaire_response_date($data) {
        global $DB;

        $data = (object)$data;
        $data->response_id = $this->get_new_parentid('questionnaire_response');
        $data->question_id = $this->get_mappingid('questionnaire_question', $data->question_id);

        // Insert the questionnaire_response_date record.
        $DB->insert_record('questionnaire_response_date', $data);
    }

    /**
     * Process multiple responses.
     * @param array $data
     * @throws dml_exception
     */
    protected function process_questionnaire_response_multiple($data) {
        global $DB;

        $data = (object)$data;
        $data->response_id = $this->get_new_parentid('questionnaire_response');
        $data->question_id = $this->get_mappingid('questionnaire_question', $data->question_id);
        $data->choice_id = $this->get_mappingid('questionnaire_quest_choice', $data->choice_id);

        // Insert the questionnaire_resp_multiple record.
        $DB->insert_record('questionnaire_resp_multiple', $data);
    }

    /**
     * Process other responses.
     * @param array $data
     * @throws dml_exception
     */
    protected function process_questionnaire_response_other($data) {
        global $DB;

        $data = (object)$data;
        $data->response_id = $this->get_new_parentid('questionnaire_response');
        $data->question_id = $this->get_mappingid('questionnaire_question', $data->question_id);
        $data->choice_id = $this->get_mappingid('questionnaire_quest_choice', $data->choice_id);

        // Insert the questionnaire_response_other record.
        $DB->insert_record('questionnaire_response_other', $data);
    }

    /**
     * Process rank responses.
     * @param array $data
     * @throws dml_exception
     */
    protected function process_questionnaire_response_rank($data) {
        global $DB;

        $data = (object)$data;

        // Older versions of questionnaire used 'rank' instead of 'rankvalue'. If 'rank' exists, change it to 'rankvalue'.
        if (isset($data->rank) && !isset($data->rankvalue)) {
            $data->rankvalue = $data->rank;
        }

        $data->response_id = $this->get_new_parentid('questionnaire_response');
        $data->question_id = $this->get_mappingid('questionnaire_question', $data->question_id);
        $data->choice_id = $this->get_mappingid('questionnaire_quest_choice', $data->choice_id);

        // Insert the questionnaire_response_rank record.
        $DB->insert_record('questionnaire_response_rank', $data);
    }

    /**
     * Process single responses.
     * @param array $data
     * @throws dml_exception
     */
    protected function process_questionnaire_response_single($data) {
        global $DB;

        $data = (object)$data;
        $data->response_id = $this->get_new_parentid('questionnaire_response');
        $data->question_id = $this->get_mappingid('questionnaire_question', $data->question_id);
        $data->choice_id = $this->get_mappingid('questionnaire_quest_choice', $data->choice_id);

        // Insert the questionnaire_resp_single record.
        $DB->insert_record('questionnaire_resp_single', $data);
    }

    /**
     * Process text answers.
     * @param array $data
     */
    protected function process_questionnaire_response_text($data) {
        global $DB;

        $data = (object)$data;
        $data->response_id = $this->get_new_parentid('questionnaire_response');
        $data->question_id = $this->get_mappingid('questionnaire_question', $data->question_id);

        // Insert the questionnaire_response_text record.
        $DB->insert_record('questionnaire_response_text', $data);
    }

    /**
     * Stuff to do after execution.
     */
    protected function after_execute() {
        global $DB;

        // Process any question dependencies after all questions and choices have already been processed to ensure we have all of
        // the new id's.

        // First, process any old system question dependencies into the new system.
        foreach ($this->olddependquestions as $newid => $olddependid) {
            $newrec = new stdClass();
            $newrec->questionid = $newid;
            $newrec->surveyid = $this->get_new_parentid('questionnaire_survey');
            $newrec->dependquestionid = $this->get_mappingid('questionnaire_question', $olddependid);
            // Only change mapping for RADIO and DROP question types, not for YESNO question.
            $dependqtype = $DB->get_field('questionnaire_question', 'type_id', ['id' => $newrec->dependquestionid]);
            if (($dependqtype !== false) && ($dependqtype != 1)) {
                $newrec->dependchoiceid = $this->get_mappingid('questionnaire_quest_choice',
                    $this->olddependchoices[$newid]);
            } else {
                $newrec->dependchoiceid = $this->olddependchoices[$newid];
            }
            $newrec->dependlogic = 1; // Set to "answer given", previously the only option.
            $newrec->dependandor = 'and'; // Not used previously.
            $DB->insert_record('questionnaire_dependency', $newrec);
        }

        // Next process all new system dependencies.
        foreach ($this->olddependencies as $data) {
            $data->dependquestionid = $this->get_mappingid('questionnaire_question', $data->dependquestionid);

            // Only change mapping for RADIO and DROP question types, not for YESNO question.
            $dependqtype = $DB->get_field('questionnaire_question', 'type_id', ['id' => $data->dependquestionid]);
            if (($dependqtype !== false) && ($dependqtype != 1)) {
                $data->dependchoiceid = $this->get_mappingid('questionnaire_quest_choice', $data->dependchoiceid);
            }
            $DB->insert_record('questionnaire_dependency', $data);
        }

        // Add questionnaire related files, no need to match by itemname (just internally handled context).
        $this->add_related_files('mod_questionnaire', 'intro', null);
        $this->add_related_files('mod_questionnaire', 'info', 'questionnaire_survey');
        $this->add_related_files('mod_questionnaire', 'thankbody', 'questionnaire_survey');
        $this->add_related_files('mod_questionnaire', 'feedbacknotes', 'questionnaire_survey');
        $this->add_related_files('mod_questionnaire', 'question', 'questionnaire_question');
        $this->add_related_files('mod_questionnaire', 'sectionheading', 'questionnaire_fb_sections');
        $this->add_related_files('mod_questionnaire', 'feedback', 'questionnaire_feedback');

        // Process any old rate question named degree choices after all questions and choices have been restored.
        if ($this->task->get_old_moduleversion() < 2018110103) {
            \mod_questionnaire\question\rate::move_all_nameddegree_choices($this->get_new_parentid('questionnaire_survey'));
        }
    }
}
