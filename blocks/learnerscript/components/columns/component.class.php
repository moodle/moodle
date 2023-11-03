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
 * LearnerScript Reports
 * A Moodle block for creating customizable reports
 * @package blocks
 * @author: eAbyas Info Solutions
 * @date: 2017
 */
use block_learnerscript\local\componentbase;
use block_learnerscript\local\ls as ls;
class component_columns extends componentbase {

    public function init() {
        $this->plugins = true;
        $this->ordering = true;
        $this->form = true;
        $this->help = true;
        $this->reporttype = $this->config->type;
    }

    public function process_form() {
        if ($this->form) {
            return true;
        }
    }

    public function add_form_elements(&$mform, $fullform) {
        global $DB, $CFG;

        $mform->addElement('header', get_string('columnandcellproperties', 'block_learnerscript'), get_string('columnandcellproperties', 'block_learnerscript'));

        $mform->addElement('text', 'columname', get_string('name'));
        $mform->addRule('columname', get_string('required'), 'required',null,'client');
        $mform->addRule('columname', get_string('spacevalidation','block_learnerscript'), 'regex', "/\S{1}/", 'client');
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('columname', PARAM_TEXT);
        } else {
            $mform->setType('columname', PARAM_CLEAN);
        }
        if(($this->reporttype != 'userprofile') && ($this->reporttype != 'courseprofile')){
            $mform->addElement('select', 'align', get_string('cellalign', 'block_learnerscript'), array('center' => 'center', 'left' => 'left', 'right' => 'right'));
            $mform->setAdvanced('align');
             $options = array('maxlength' => '2');
            $mform->addElement('text', 'size', get_string('cellsize', 'block_learnerscript'), $options);
            $mform->setType('size', PARAM_CLEAN);
            $mform->setAdvanced('size');

            $mform->addElement('select', 'wrap', get_string('cellwrap', 'block_learnerscript'), array('' => 'Wrap', 'nowrap' => 'No Wrap'));
            $mform->setAdvanced('wrap');
        }


    }

    public function validate_form_elements($data, $errors) {
        if (!empty($data['size']) && !preg_match("/^\d+$/i", trim($data['size']))) {
            $errors['size'] = get_string('badsize', 'block_learnerscript');
        }
        return $errors;
    }

    public function form_process_data(&$cform) {
        global $DB;
        if ($this->form) {
            $data = $cform->get_data();
            // Function cr_serialize() will add slashes.
            $components = (new ls)->cr_unserialize($this->config->components);
            $components['columns']['config'] = $data;
            $this->config->components = (new ls)->cr_serialize($components);
            $DB->update_record('block_learnerscript', $this->config);
        }
    }

    public function form_set_data(&$cform) {
        if ($this->form) {
            $fdata = new stdclass;
            $components = (new ls)->cr_unserialize($this->config->components);
            $fdata = (isset($components['columns']['config'])) ? $components['columns']['config'] : $fdata;
            $cform->set_data($fdata);
        }
    }
}