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
require_once($CFG->dirroot . '/question/type/essay/edit_essay_form.php');


class qtype_essaywiris_edit_form extends qtype_wq_edit_form {

    protected $base;

    protected function definition_inner($mform) {
        global $CFG;
        parent::definition_inner($mform);

        $wirisessay = $mform->createElement('text', 'wirisessay', get_string('essaywiris_algorithm', 'qtype_essaywiris'),
                array('class' => 'wirisauthoringfield wirisstudio wirisessay wirisvariables wirisauxiliarcas' .
                ' wirisauxiliartextinput'));

        $wirishdr = $mform->createElement('header', 'wirishdr', get_string('essaywiris_wiris_variables', 'qtype_essaywiris'));

        if ($CFG->version >= 2013051400) { // 2.5+.
            $mform->_collapsibleElements['wirishdr'] = false;
        }

        if ($mform->elementExists('tagsheader')) {
            $mform->insertElementBefore($wirishdr, 'tagsheader');
            $mform->insertElementBefore($wirisessay, 'tagsheader');
        } else {
            $mform->insertElementBefore($wirishdr, 'buttonar');
            $mform->insertElementBefore($wirisessay, 'buttonar');
        }

    }

    public function qtype() {
        return 'essaywiris';
    }

}
