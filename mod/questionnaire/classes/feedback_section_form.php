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

namespace mod_questionnaire;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');
require_once($CFG->dirroot.'/mod/questionnaire/lib.php');

/**
 * Print the form to add or edit a questionnaire-instance
 *
 * @package mod_questionnaire
 * @copyright  2016 Mike Churchward (mike.churchward@poetgroup.org)
 * @author Joseph Rezeau (based on Quiz by Tim Hunt)
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */
class feedback_section_form extends \moodleform {

    /** @var mixed $_feedbacks */
    protected $_feedbacks;
    /**
     * @var \context $context The used context.
     */
    public $context;

    /**
     * Form definition.
     */
    public function definition() {
        global $questionnaire;

        $feedbacksection = $this->_customdata->feedbacksection;
        $validquestions = $this->_customdata->validquestions;
        $survey = $this->_customdata->survey;
        $feedbacksections = $questionnaire->survey->feedbacksections;
        $this->_feedbacks = $feedbacksection->sectionfeedback;
        $this->context = $questionnaire->context;
        $mform    =& $this->_form;

        if ($survey->feedbacksections > 1) {
            $mform->addElement('header', 'feedbacksectionsheader', get_string('feedbacksections', 'questionnaire'));
            $sselectgroup = [];
            $sselect = $mform->createElement('select', 'navigatesections', get_string('navigatetosection', 'questionnaire'),
                $this->_customdata->sectionselect);
            $sselect->setSelected($feedbacksection->id);
            $sselectgroup[] = $sselect;
            $sselectgroup[] = $mform->createElement('submit', 'gotosection', get_string('navigatetosection', 'questionnaire'));
            $mform->addGroup($sselectgroup, '');
            $addnewsectionarray = [];
            $addnewsectionarray[] = $mform->createElement('text', 'newsectionlabel',
                get_string('feedbacksectionlabel', 'questionnaire'));
            $mform->setType('newsectionlabel', PARAM_TEXT);
            $addnewsectionarray[] = $mform->createElement('submit', 'addnewsection', get_string('addnewsection', 'questionnaire'));
            $mform->addGroup($addnewsectionarray, '', get_string('feedbacksectionlabel', 'questionnaire'));
        }

        if ($feedbacksections == 1) {
            $label = get_string('feedbackglobal', 'questionnaire');
            $feedbackheading = get_string('feedbackglobalheading', 'questionnaire');
            $feedbackmessages = get_string('feedbackglobalmessages', 'questionnaire');
        } else {
            $label = $feedbacksection->sectionlabel;
            $feedbackheading = get_string('feedbacksectionheading', 'questionnaire', $label);
            $feedbackmessages = get_string('feedbackmessages', 'questionnaire', $label);
        }

        $mform->addElement('header', 'contenthdr', $feedbackheading);
        $mform->addElement('text', 'sectionlabel', get_string('feedbacksectionlabel', 'questionnaire'),
            ['size' => '50', 'maxlength' => '50']);
        $mform->setType('sectionlabel', PARAM_TEXT);
        $mform->addRule('sectionlabel', null, 'required', null, 'client');
        $mform->addHelpButton('sectionlabel', 'feedbacksectionlabel', 'questionnaire');

        $editoroptions = ['maxfiles' => EDITOR_UNLIMITED_FILES, 'trusttext' => true];
        $mform->addElement('editor', 'sectionheading', get_string('feedbacksectionheadingtext', 'questionnaire'),
            null, $editoroptions);
        $mform->setType('sectionheading', PARAM_RAW);
        $mform->setDefault('feedbacknotes', $questionnaire->survey->feedbacknotes);
        $mform->addHelpButton('sectionheading', 'feedbackheading', 'questionnaire');

        if ($questionnaire->survey->feedbacksections > 0) {
            // Sections.
            if ($survey->feedbacksections > 1) {
                $mform->addElement('header', 'fbsection_' . $feedbacksection->id,
                    get_string('feedbacksectionquestions', 'questionnaire', $label));
                $qvalid = $validquestions;
                if (!empty($feedbacksection->scorecalculation)) {
                    $rsrc = $questionnaire->renderer->image_url('t/delete');
                    $strremove = get_string('remove', 'questionnaire');
                    $rextra = ['alt' => $strremove, 'title' => $strremove];
                    $counter = 1;
                    foreach ($feedbacksection->scorecalculation as $qid => $score) {
                        unset($qvalid[$qid]);
                        $questionactions = [];
                        if ((int)$score !== -1) {
                            $weight = '<input type="number" style="width: 4em;" id="weight' . $counter . '" ' .
                                'name="weight[' . $qid . ']" min="0.0" max="1.0" step="0.01" ' .
                                'value="' . $score . '">';
                        } else {
                            $weight = '<input type="hidden" id="weight' . $counter . '" name="weight[' . $qid . ']" '.
                                'value="' . $score . '">';
                        }
                        $questionactions[] = $mform->createElement('html', $weight);
                        $rextra['value'] = $qid;
                        unset($rextra['style']);
                        $questionactions[] = $mform->createElement('image', 'confirmremovequestion[' . $qid . ']', $rsrc, $rextra);

                        $mform->addGroup($questionactions, '', $questionnaire->questions[$qid]->name);
                        $counter++;
                    }
                }
                if (!empty($qvalid)) {
                    // Merge arrays maintaining keys.
                    $qselect = [];
                    $qselect[] = $mform->createElement('select', 'addquestionselect',
                        get_string('addquestiontosection', 'questionnaire'), $qvalid);
                    $qselect[] = $mform->createElement('submit', 'addquestion', get_string('addquestion', 'questionnaire'));
                    $mform->addGroup($qselect, '', get_string('addquestiontosection', 'questionnaire'));
                }
            }
        }

        // FEEDBACK FIELDS.

        $mform->addElement('header', 'feedbackhdr', $feedbackmessages);
        $mform->addHelpButton('feedbackhdr', 'feedback', 'questionnaire');

        $mform->addElement('static', 'scoreboundarystatic1', get_string('feedbackscoreboundary', 'questionnaire'), '100%');

        $repeatarray = [];
        $repeatedoptions = [];

        $repeatarray[] = $mform->createElement('editor', 'feedbacktext', get_string('feedback', 'questionnaire'), null,
            ['maxfiles' => EDITOR_UNLIMITED_FILES, 'noclean' => true, 'context' => $questionnaire->context]);
        $repeatarray[] = $mform->createElement(
            'text', 'feedbackboundaries', get_string('feedbackscoreboundary', 'questionnaire'), ['size' => 10]);
        $repeatedoptions['feedbacklabel']['type'] = PARAM_RAW;
        $repeatedoptions['feedbacktext']['type'] = PARAM_RAW;
        $repeatedoptions['feedbackboundaries']['type'] = PARAM_RAW;

        $numfeedbacks = max(count($this->_feedbacks) * 1, 3);

        $nextel = $this->repeat_elements($repeatarray, $numfeedbacks - 1, $repeatedoptions, 'boundary_repeats',
            'boundary_add_fields', 2, get_string('feedbackaddmorefeedbacks', 'questionnaire'), true);

        // Put some extra elements in before the button.
        $mform->insertElementBefore(
            $mform->createElement('editor', "feedbacktext[$nextel]", get_string('feedback', 'questionnaire'), null,
                ['maxfiles' => EDITOR_UNLIMITED_FILES, 'noclean' => true, 'context' => $questionnaire->context]),
            'boundary_add_fields');
        $mform->insertElementBefore(
            $mform->createElement('static', 'scoreboundarystatic2', get_string('feedbackscoreboundary', 'questionnaire'), '0%'),
            'boundary_add_fields');

        // Hidden fields.
        $mform->addElement('hidden', 'id', 0);
        $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'sectionid', 0);
        $mform->setType('sectionid', PARAM_INT);

        // Buttons.
        $buttonarray = [];
        $buttonarray[] = $mform->createElement('submit', 'submitbutton', get_string('savechanges'));
        $buttonarray[] = $mform->createElement('submit', 'confirmdeletesection', get_string('deletesection', 'questionnaire'));
        $buttonarray[] = $mform->createElement('cancel');
        $mform->addGroup($buttonarray, 'buttonar', '', ' ', false);
        $mform->closeHeaderBefore('buttonar');
    }

    /**
     * Form preprocessing.
     * @param array $toform
     */
    public function data_preprocessing(&$toform) {
        if (count($this->_feedbacks)) {
            $key = 0;
            foreach ($this->_feedbacks as $feedback) {
                $draftid = file_get_submitted_draft_itemid('feedbacktext['.$key.']');
                $toform['feedbacktext['.$key.']']['text'] = file_prepare_draft_area(
                    $draftid,               // Draftid.
                    $this->context->id,     // Context.
                    'mod_questionnaire',    // Component.
                    'feedback',             // Filarea.
                    !empty($feedback->id) ? (int)$feedback->id : null, // Itemid.
                    null,
                    $feedback->feedbacktext // Text.
                );
                $toform['feedbacktext['.$key.']']['format'] = 1;
                $toform['feedbacklabel['.$key.']'] = $feedback->feedbacklabel;
                $toform['feedbacktext['.$key.']']['itemid'] = $draftid;

                if ($feedback->minscore > 0) {
                    $toform['feedbackboundaries['.$key.']'] = (100.0 * $feedback->minscore / 100 ) . '%';
                }
                $key++;
            }
        }
    }

    /**
     * Form validation.
     * @param array $data
     * @param array $files
     * @return array
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        // Check the boundary value is a number or a percentage, and in range.
        $i = 0;
        while (!empty($data['feedbackboundaries'][$i])) {
            $boundary = trim($data['feedbackboundaries'][$i]);
            if (strlen($boundary) > 0 && $boundary[strlen($boundary) - 1] == '%') {
                $boundary = trim(substr($boundary, 0, -1));
                if (is_numeric($boundary)) {
                    $boundary = $boundary * 100 / 100.0;
                } else {
                    $errors["feedbackboundaries[$i]"] = get_string('feedbackerrorboundaryformat', 'quiz', $i + 1);
                }
            }
            if (is_numeric($boundary) && $boundary <= 0) {
                $errors["feedbackboundaries[$i]"] = get_string('feedbackerrorboundaryoutofrange', 'questionnaire', $i + 1);
            }
            if (is_numeric($boundary) && $i > 0 &&
                    $boundary >= $data['feedbackboundaries'][$i - 1]) {
                $errors["feedbackboundaries[$i]"] = get_string('feedbackerrororder', 'questionnaire', $i + 1);
            }
            $data['feedbackboundaries'][$i] = $boundary;
            $i += 1;
        }
        $numboundaries = $i;

        // Check there is nothing in the remaining unused fields.
        if (!empty($data['feedbackboundaries'])) {
            for ($i = $numboundaries; $i < count($data['feedbackboundaries']); $i += 1) {
                if (!empty($data['feedbackboundaries'][$i] ) &&
                        trim($data['feedbackboundaries'][$i] ) != '') {
                    $errors["feedbackboundaries[$i]"] = get_string('feedbackerrorjunkinboundary', 'questionnaire', $i + 1);
                }
            }
        }
        for ($i = $numboundaries + 1; $i < count($data['feedbacktext']); $i += 1) {
            if (!empty($data['feedbacktext'][$i]['text']) &&
                    trim($data['feedbacktext'][$i]['text'] ) != '') {
                $errors["feedbacktext[$i]"] = get_string('feedbackerrorjunkinfeedback', 'questionnaire', $i + 1);
            }
        }
        return $errors;
    }

    /**
     * Load in existing data as form defaults. Usually new entry defaults are stored directly in
     * form definition (new entry form); this function is used to load in data where values
     * already exist and data is being edited (edit entry form).
     *
     * @param array $defaultvalues
     */
    public function set_data($defaultvalues) {
        if (is_object($defaultvalues)) {
            $defaultvalues = (array)$defaultvalues;
        }
        $this->data_preprocessing($defaultvalues);
        parent::set_data($defaultvalues);
    }
}
