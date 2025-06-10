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
require_once($CFG->dirroot . '/question/type/multichoice/edit_multichoice_form.php');


class qtype_multichoicewiris_edit_form extends qtype_wq_edit_form {

    protected $base;

    protected function definition_inner($mform) {
        global $CFG;
        parent::definition_inner($mform);

        $wirismultialgstring = get_string('multichoicewiris_algorithm', 'qtype_multichoicewiris');
        $wirismulti = $mform->createElement('text', 'wirismulti', $wirismultialgstring,
                array('class' => 'wirisauthoringfield wirisstudio wirismultichoice' .
                                 ' wirisvariables wirisauxiliarcas wirisauxiliartextinput'));

        $wirismultivarstring = get_string('multichoicewiris_wiris_variables', 'qtype_multichoicewiris');
        $wirishdr = $mform->createElement('header', 'wirishdr', $wirismultivarstring);

        if ($CFG->version >= 2013051400) { // 2.5+.
            $placetoinsert = 'answerhdr';
            $mform->_collapsibleElements['wirishdr'] = false;
        } else {
            $placetoinsert = 'answerhdr[0]';
        }
        $mform->insertElementBefore($wirishdr, $placetoinsert);
        $mform->insertElementBefore($wirismulti, $placetoinsert);
    }

    public function qtype() {
        return 'multichoicewiris';
    }

}
