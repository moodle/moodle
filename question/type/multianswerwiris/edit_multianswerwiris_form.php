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

require_once($CFG->dirroot . '/question/type/wq/edit_wq_form.php');
require_once($CFG->dirroot . '/question/type/multianswer/edit_multianswer_form.php');


class qtype_multianswerwiris_edit_form extends qtype_wq_edit_form {
    protected function definition_inner($mform) {
        global $CFG;
        parent::definition_inner($mform);

        $class = 'wirisauthoringfield wirisstudio wirismultichoice wirisvariables wirisvalidation wirisauxiliarcas ';
        $class .= 'wirisanswerfieldpopupeditor wirisanswerfieldplaintext wirisauxiliartextinput';
        $wirismultianswer = $mform->createElement('text', 'wirismultianswer',
                get_string('multianswerwiris_algorithm', 'qtype_multianswerwiris'), array('class' => $class));

        $wirishdr = $mform->createElement('header', 'wirishdr',
                get_string('multianswerwiris_wiris_variables', 'qtype_multianswerwiris'));

        if ($CFG->version >= 2013051400) { // 2.5+.
            $mform->_collapsibleElements['wirishdr'] = false;
        }

        $mform->insertElementBefore($wirishdr, 'multitriesheader');
        $mform->insertElementBefore($wirismultianswer, 'multitriesheader');

    }

    public function qtype() {
        return 'multianswerwiris';
    }
}

class qtype_multianswer_edit_form_helper extends qtype_multianswer_edit_form {
    protected function definition_inner($mform) {
        // Remove wiris particle from subquestion qtypes so the multianswer form does not think
        // we are changing the qtypes when we are not actually doing it.
        if (isset($this->savedquestiondisplay)) {
            foreach ($this->savedquestiondisplay->options->questions as $subq) {
                $qtype = $subq->qtype;
                if (substr($qtype, -5) == 'wiris') {
                    $subq->qtype = substr($qtype, 0, strlen($qtype) - 5);
                }
            }
        }

        parent::definition_inner($mform);
    }
}
