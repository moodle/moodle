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

namespace mod_questionnaire\question;

use mod_questionnaire\feedback\section;

/**
 * This file contains the parent class for sectiontext question types.
 *
 * @author Mike Churchward
 * @copyright  2016 onward Mike Churchward (mike.churchward@poetopensource.org)
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package mod_questionnaire
 */
class sectiontext extends question {

    /**
     * Each question type must define its response class.
     * @return object The response object based off of questionnaire_response_base.
     */
    protected function responseclass() {
        return '';
    }

    /**
     * Short name for this question type - no spaces, etc..
     * @return string
     */
    public function helpname() {
        return 'sectiontext';
    }

    /**
     * Return true if this question has been marked as required.
     * @return boolean
     */
    public function required() {
        return true;
    }

    /**
     * True if question type supports feedback options. False by default.
     */
    public function supports_feedback() {
        return false;
    }

    /**
     * True if question provides mobile support.
     * @return bool
     */
    public function supports_mobile() {
        return true;
    }

    /**
     * Display on mobile.
     *
     * @param int $qnum
     * @param bool $autonum
     */
    public function mobile_question_display($qnum, $autonum = false) {
        $options = ['noclean' => true, 'para' => false, 'filter' => true,
            'context' => $this->context, 'overflowdiv' => true];
        $mobiledata = (object)[
            'id' => $this->id,
            'name' => $this->name,
            'type_id' => $this->type_id,
            'length' => $this->length,
            'content' => format_text(file_rewrite_pluginfile_urls($this->content, 'pluginfile.php', $this->context->id,
                'mod_questionnaire', 'question', $this->id), FORMAT_HTML, $options),
            'content_stripped' => strip_tags($this->content),
            'required' => false,
            'deleted' => $this->deleted,
            'response_table' => $this->responsetable,
            'fieldkey' => $this->mobile_fieldkey(),
            'precise' => $this->precise,
            'qnum' => '',
            'errormessage' => get_string('required') . ': ' . $this->name
        ];

        $mobiledata->issectiontext = true;
        return $mobiledata;
    }

    /**
     * True if question type supports feedback scores and weights. Same as supports_feedback() by default.
     */
    public function supports_feedback_scores() {
        return false;
    }

    /**
     * True if the question supports feedback and has valid settings for feedback. Override if the default logic is not enough.
     */
    public function valid_feedback() {
        return false;
    }

    /**
     * Override and return a form template if provided. Output of question_survey_display is iterpreted based on this.
     * @return boolean | string
     */
    public function question_template() {
        return 'mod_questionnaire/question_sectionfb';
    }

    /**
     * Override and return false if a number should not be rendered for this question in any context.
     * @return bool
     */
    public function is_numbered() {
        return false;
    }

    /**
     * Return the context tags for the check question template.
     * @param \mod_questionnaire\responsetype\response\response $response
     * @param array $descendantsdata Array of all questions/choices depending on this question.
     * @param boolean $blankquestionnaire
     * @return object The check question context tags.
     *
     */
    protected function question_survey_display($response, $descendantsdata, $blankquestionnaire=false) {
        global $DB, $CFG, $PAGE;
        require_once($CFG->dirroot.'/mod/questionnaire/questionnaire.class.php');

        // If !isset then normal behavior as sectiontext question.
        if (!isset($response->questionnaireid)) {
            return '';
        }

        $filteredsections = [];

        // In which section(s) is this question?
        if ($fbsections = $DB->get_records('questionnaire_fb_sections', ['surveyid' => $this->surveyid])) {
            foreach ($fbsections as $key => $fbsection) {
                if ($scorecalculation = section::decode_scorecalculation($fbsection->scorecalculation)) {
                    if (array_key_exists($this->id, $scorecalculation)) {
                        array_push($filteredsections, $fbsection->section);
                    }
                }
            }
        }

        // If empty then normal behavior as sectiontext question.
        if (empty($filteredsections)) {
            return '';
        }

        list($cm, $course, $questionnaire) = questionnaire_get_standard_page_items(null, $response->questionnaireid);
        $questionnaire = new \questionnaire($course, $cm, 0, $questionnaire);
        $questionnaire->add_renderer($PAGE->get_renderer('mod_questionnaire'));
        $questionnaire->add_page(new \mod_questionnaire\output\reportpage());

        $compare = false;
        $allresponses = false;
        $currentgroupid = 0;
        $isgroupmember = false;
        $rid = (isset($response->id) && !empty($response->id)) ? $response->id : 0;
        $resps = [$rid => null];
        // For $filteredsections -> get the feedback messages only for this sections!
        $feedbackmessages = $questionnaire->response_analysis($rid, $resps, $compare, $isgroupmember, $allresponses,
            $currentgroupid, $filteredsections);

        // Output.
        $questiontags = new \stdClass();
        $questiontags->qelements = new \stdClass();
        $choice = new \stdClass();

        $choice->fb = implode($feedbackmessages);

        $questiontags->qelements->choice = $choice;
        return $questiontags;
    }

    /**
     * Question specific response display method.
     * @param \stdClass $data
     *
     */
    protected function response_survey_display($data) {
        return '';
    }

    /**
     * Check question's form data for complete response.
     *
     * @param object $responsedata The data entered into the response.
     * @return boolean
     */
    public function response_complete($responsedata) {
        return true;
    }

    /**
     * Add the form required field.
     * @param \MoodleQuickForm $mform
     * @return \MoodleQuickForm
     */
    protected function form_required(\MoodleQuickForm $mform) {
        return $mform;
    }

    /**
     * Return the length form element.
     * @param \MoodleQuickForm $mform
     * @param string $helpname
     */
    protected function form_length(\MoodleQuickForm $mform, $helpname = '') {
        return question::form_length_hidden($mform);
    }

    /**
     * Return the precision form element.
     * @param \MoodleQuickForm $mform
     * @param string $helpname
     */
    protected function form_precise(\MoodleQuickForm $mform, $helpname = '') {
        return question::form_precise_hidden($mform);
    }
}
