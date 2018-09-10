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
 * @package mod_questionnaire
 * @copyright  2016 Mike Churchward (mike.churchward@poetgroup.org)
 * @author     Mike Churchward
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_questionnaire;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

class settings_form extends \moodleform {

    public function definition() {
        global $questionnaire, $questionnairerealms;

        $mform    =& $this->_form;

        $mform->addElement('header', 'contenthdr', get_string('contentoptions', 'questionnaire'));

        $capabilities = questionnaire_load_capabilities($questionnaire->cm->id);
        if (!$capabilities->createtemplates) {
            unset($questionnairerealms['template']);
        }
        if (!$capabilities->createpublic) {
            unset($questionnairerealms['public']);
        }
        if (isset($questionnairerealms['public']) || isset($questionnairerealms['template'])) {
            $mform->addElement('select', 'realm', get_string('realm', 'questionnaire'), $questionnairerealms);
            $mform->setDefault('realm', $questionnaire->survey->realm);
            $mform->addHelpButton('realm', 'realm', 'questionnaire');
        } else {
            $mform->addElement('hidden', 'realm', 'private');
        }
        $mform->setType('realm', PARAM_RAW);

        $mform->addElement('text', 'title', get_string('title', 'questionnaire'), array('size' => '60'));
        $mform->setDefault('title', $questionnaire->survey->title);
        $mform->setType('title', PARAM_TEXT);
        $mform->addRule('title', null, 'required', null, 'client');
        $mform->addHelpButton('title', 'title', 'questionnaire');

        $mform->addElement('text', 'subtitle', get_string('subtitle', 'questionnaire'), array('size' => '60'));
        $mform->setDefault('subtitle', $questionnaire->survey->subtitle);
        $mform->setType('subtitle', PARAM_TEXT);
        $mform->addHelpButton('subtitle', 'subtitle', 'questionnaire');

        $editoroptions = array('maxfiles' => EDITOR_UNLIMITED_FILES, 'trusttext' => true);
        $mform->addElement('editor', 'info', get_string('additionalinfo', 'questionnaire'), null, $editoroptions);
        $mform->setDefault('info', $questionnaire->survey->info);
        $mform->setType('info', PARAM_RAW);
        $mform->addHelpButton('info', 'additionalinfo', 'questionnaire');

        $mform->addElement('header', 'submithdr', get_string('submitoptions', 'questionnaire'));

        $mform->addElement('text', 'thanks_page', get_string('url', 'questionnaire'), array('size' => '60'));
        $mform->setType('thanks_page', PARAM_TEXT);
        $mform->setDefault('thanks_page', $questionnaire->survey->thanks_page);
        $mform->addHelpButton('thanks_page', 'url', 'questionnaire');

        $mform->addElement('static', 'confmes', get_string('confalts', 'questionnaire'));
        $mform->addHelpButton('confmes', 'confpage', 'questionnaire');

        $mform->addElement('text', 'thank_head', get_string('headingtext', 'questionnaire'), array('size' => '30'));
        $mform->setType('thank_head', PARAM_TEXT);
        $mform->setDefault('thank_head', $questionnaire->survey->thank_head);

        $editoroptions = array('maxfiles' => EDITOR_UNLIMITED_FILES, 'trusttext' => true);
        $mform->addElement('editor', 'thank_body', get_string('bodytext', 'questionnaire'), null, $editoroptions);
        $mform->setType('thank_body', PARAM_RAW);
        $mform->setDefault('thank_body', $questionnaire->survey->thank_body);

        $mform->addElement('text', 'email', get_string('email', 'questionnaire'), array('size' => '75'));
        $mform->setType('email', PARAM_TEXT);
        $mform->setDefault('email', $questionnaire->survey->email);
        $mform->addHelpButton('email', 'sendemail', 'questionnaire');

        $defaultsections = get_config('questionnaire', 'maxsections');

        // We cannot have more sections than available (required) questions with a choice value.
        $nbquestions = 0;
        foreach ($questionnaire->questions as $question) {
            $qtype = $question->type_id;
            $qname = $question->name;
            $required = $question->required;
            // Question types accepted for feedback; QUESRATE ok except noduplicates.
            if (($qtype == QUESRADIO || $qtype == QUESDROP || ($qtype == QUESRATE && $question->precise != 2))
                            && $required == 'y' && $qname != '') {
                foreach ($question->choices as $choice) {
                    if (isset($choice->value) && $choice->value != null && $choice->value != 'NULL') {
                        $nbquestions ++;
                        break;
                    }
                }
            }
            if ($qtype == QUESYESNO && $required == 'y' && $qname != '') {
                $nbquestions ++;
            }
        }

        // Questionnaire Feedback Sections and Messages.
        if ($nbquestions != 0) {
            $maxsections = min ($nbquestions, $defaultsections);
            $feedbackoptions = array();
            $feedbackoptions[0] = get_string('feedbacknone', 'questionnaire');
            $mform->addElement('header', 'submithdr', get_string('feedbackoptions', 'questionnaire'));
            $feedbackoptions[1] = get_string('feedbackglobal', 'questionnaire');
            for ($i = 2; $i <= $maxsections; ++$i) {
                $feedbackoptions[$i] = get_string('feedbacksections', 'questionnaire', $i);
            }
            $mform->addElement('select', 'feedbacksections', get_string('feedbackoptions', 'questionnaire'), $feedbackoptions);
            $mform->setDefault('feedbacksections', $questionnaire->survey->feedbacksections);
            $mform->addHelpButton('feedbacksections', 'feedbackoptions', 'questionnaire');

            $options = array('0' => get_string('no'), '1' => get_string('yes'));
            $mform->addElement('select', 'feedbackscores', get_string('feedbackscores', 'questionnaire'), $options);
            $mform->addHelpButton('feedbackscores', 'feedbackscores', 'questionnaire');

            // Is the RGraph library enabled at level site?
            $usergraph = get_config('questionnaire', 'usergraph');
            if ($usergraph) {
                $chartgroup = array();
                $charttypes = array (null => get_string('none'),
                        'bipolar' => get_string('chart:bipolar', 'questionnaire'),
                        'vprogress' => get_string('chart:vprogress', 'questionnaire'));
                $chartgroup[] = $mform->createElement('select', 'chart_type_global',
                        get_string('chart:type', 'questionnaire').' ('.
                                get_string('feedbackglobal', 'questionnaire').')', $charttypes);
                if ($questionnaire->survey->feedbacksections == 1) {
                    $mform->setDefault('chart_type_global', $questionnaire->survey->chart_type);
                }
                $mform->disabledIf('chart_type_global', 'feedbacksections', 'eq', 0);
                $mform->disabledIf('chart_type_global', 'feedbacksections', 'neq', 1);

                $charttypes = array (null => get_string('none'),
                        'bipolar' => get_string('chart:bipolar', 'questionnaire'),
                        'hbar' => get_string('chart:hbar', 'questionnaire'),
                        'rose' => get_string('chart:rose', 'questionnaire'));
                $chartgroup[] = $mform->createElement('select', 'chart_type_two_sections',
                        get_string('chart:type', 'questionnaire').' ('.
                                get_string('feedbackbysection', 'questionnaire').')', $charttypes);
                if ($questionnaire->survey->feedbacksections > 1) {
                    $mform->setDefault('chart_type_two_sections', $questionnaire->survey->chart_type);
                }
                $mform->disabledIf('chart_type_two_sections', 'feedbacksections', 'neq', 2);

                $charttypes = array (null => get_string('none'),
                        'bipolar' => get_string('chart:bipolar', 'questionnaire'),
                        'hbar' => get_string('chart:hbar', 'questionnaire'),
                        'radar' => get_string('chart:radar', 'questionnaire'),
                        'rose' => get_string('chart:rose', 'questionnaire'));
                $chartgroup[] = $mform->createElement('select', 'chart_type_sections',
                        get_string('chart:type', 'questionnaire').' ('.
                                get_string('feedbackbysection', 'questionnaire').')', $charttypes);
                if ($questionnaire->survey->feedbacksections > 1) {
                    $mform->setDefault('chart_type_sections', $questionnaire->survey->chart_type);
                }
                $mform->disabledIf('chart_type_sections', 'feedbacksections', 'eq', 0);
                $mform->disabledIf('chart_type_sections', 'feedbacksections', 'eq', 1);
                $mform->disabledIf('chart_type_sections', 'feedbacksections', 'eq', 2);

                $mform->addGroup($chartgroup, 'chartgroup',
                        get_string('chart:type', 'questionnaire'), null, false);
                $mform->addHelpButton('chartgroup', 'chart:type', 'questionnaire');
            }
            $editoroptions = array('maxfiles' => EDITOR_UNLIMITED_FILES, 'trusttext' => true);
            $mform->addElement('editor', 'feedbacknotes', get_string('feedbacknotes', 'questionnaire'), null, $editoroptions);
            $mform->setType('feedbacknotes', PARAM_RAW);
            $mform->setDefault('feedbacknotes', $questionnaire->survey->feedbacknotes);
            $mform->addHelpButton('feedbacknotes', 'feedbacknotes', 'questionnaire');

            $mform->addElement('submit', 'feedbackeditbutton', get_string('feedbackeditsections', 'questionnaire'));
            $mform->disabledIf('feedbackeditbutton', 'feedbacksections', 'eq', 0);
        }

        // Hidden fields.
        $mform->addElement('hidden', 'id', 0);
        $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'sid', 0);
        $mform->setType('sid', PARAM_INT);
        $mform->addElement('hidden', 'name', '');
        $mform->setType('name', PARAM_TEXT);
        $mform->addElement('hidden', 'courseid', '');
        $mform->setType('courseid', PARAM_RAW);

        // Buttons.

        $submitlabel = get_string('savechangesanddisplay');
        $submit2label = get_string('savechangesandreturntocourse');
        $mform = $this->_form;

        // Elements in a row need a group.
        $buttonarray = array();
        $buttonarray[] = &$mform->createElement('submit', 'submitbutton2', $submit2label);
        $buttonarray[] = &$mform->createElement('submit', 'submitbutton', $submitlabel);
        $buttonarray[] = &$mform->createElement('cancel');

        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
        $mform->setType('buttonar', PARAM_RAW);
        $mform->closeHeaderBefore('buttonar');

    }

    public function validation($data, $files) {
        $errors = parent::validation($data, $files);
        return $errors;
    }
}