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

defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot . '/question/type/edit_question_form.php');

class qtype_wq_edit_form extends question_edit_form {
    protected $base;

    public function __construct($base, $submiturl, $question, $category, $contexts, $formeditable) {
        // TODO: remove all but $base function parameters.

        // We don't call the parent constructor because we will use the form in
        // $base. So we don't have to build another one. Just reference some
        // public properties that may be used and call the definition_inner from
        // this class to add Wiris Quizzes elements.

        $this->base = $base;
        $this->question = &$this->base->question;
        $this->contexts = &$this->base->contexts;
        $this->category = &$this->base->category;
        $this->categorycontext = &$this->base->categorycontext;
        $this->context = &$this->base->context;
        $this->editoroptions = &$this->base->editoroptions;
        $this->fileoptions = &$this->base->fileoptions;
        $this->instance = &$this->base->instance;
        $this->_formname = &$this->base->_formname;
        $this->_form = &$this->base->_form;
        $this->_customdata = &$this->base->_customdata;
        $this->_definition_finalized = &$this->base->_definition_finalized;

        $this->definition_inner($this->_form);
    }

    protected function definition_inner($mform) {
        global $DB, $PAGE;
        // We don't call base's definition_inner because it has been arleady
        // called during its construction.

        $mform->addElement('hidden', 'wirisquestion', '', array('class' => 'wirisquestion'));
        $mform->setType('wirisquestion', PARAM_RAW);
        $mform->addElement('hidden', 'wirislang', current_language(), array('class' => 'wirislang'));
        $mform->setType('wirislang', PARAM_TEXT);

        if (isset($this->question->wirisquestion)) {
            $program = $this->question->wirisquestion->serialize();
        } else {
            // If the wirisquestion is not already loaded in memory, load it from the DB directly.
            if (empty($this->question->id)) {
                // New question.
                $program = '<question/>';
            } else {
                // Existing question.
                $wiris = $DB->get_record('qtype_wq', array('question' => $this->question->id));
                if (empty($wiris)) {
                    // Corrupted question.
                    $corruptwarning =
                        $mform->createElement(
                            'html',
                            '<div class="wiriscorruptquestionedit">' . get_string('corruptquestion_edit', 'qtype_wq') . '</div>'
                        );
                    $mform->insertElementBefore($corruptwarning, 'generalheader');
                    $program = '<question/>';
                } else {
                    // Question found in the DB.
                    $program = $wiris->xml;
                }
            }
        }

        if (isset($this->customfieldpluginenabled)) {
            // Reference: https://docs.moodle.org/dev/Custom_fields_API .
            if ($this->customfieldpluginenabled) {
                // Add custom fields to the form.
                $this->customfieldhandler = qbank_customfields\customfield\question_handler::create();
                $this->customfieldhandler->set_parent_context($this->categorycontext); // For question handler only.
                $this->customfieldhandler->instance_form_definition($mform, empty($this->question->id) ? 0 : $this->question->id);
            }
        }

        $defaultvalues = array();
        $defaultvalues['wirisquestion'] = $program;
        $mform->setDefaults($defaultvalues);
    }
    public function set_data($question) {
        $this->base->set_data($question);
    }
    public function validation($data, $files) {
        return $this->base->validation($data, $files);
    }
    public function data_preprocessing($question) {
        return $this->base->data_preprocessing($question);
    }
    public function qtype() {
        return 'wq';
    }
}
