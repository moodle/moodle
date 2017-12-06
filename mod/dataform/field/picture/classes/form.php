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
 * @package dataformfield
 * @subpackage picture
 * @copyright 2011 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class dataformfield_picture_form extends dataformfield_file_form {

    /**
     *
     */
    protected function field_definition() {
        $field = &$this->_field;
        $mform = &$this->_form;

        // Files separator (param4).
        $options = array(
            '' => get_string('none'),
            '<br />' => get_string('newline', 'dataformfield_file')
        );
        $mform->addElement('select', 'separator', get_string('filesseparator', 'dataformfield_file'), $options);
        $mform->addHelpButton('separator', 'filesseparator', 'dataformfield_file');
        $mform->setDefault('separator', $field->appearance->separator);

        // Display WxH.
        $grp = array();
        $grp[] = &$mform->createElement('text', 'dispw', null, array('size' => '4'));
        $grp[] = &$mform->createElement('text', 'disph', null, array('size' => '4'));
        $grp[] = &$mform->createElement('select', 'dispu', null, array('px' => 'px', 'em' => 'em', '%' => '%'));
        $mform->addGroup($grp, 'dispdim', get_string('displaydimensions', 'dataformfield_picture'), array(' x ', ' '), false);
        $mform->setType('dispw', PARAM_INT);
        $mform->setType('disph', PARAM_INT);
        $mform->addGroupRule('dispdim', array('dispw' => array(array(null, 'numeric', null, 'client'))));
        $mform->addGroupRule('dispdim', array('disph' => array(array(null, 'numeric', null, 'client'))));
        $mform->setDefault('dispw', $field->appearance->dispw);
        $mform->setDefault('disph', $field->appearance->disph);
        $mform->setDefault('dispu', $field->appearance->dispu);

        // Max pic dimensions (crop if needed).
        $grp = array();
        $grp[] = &$mform->createElement('text', 'maxw', null, array('size' => '4'));
        $grp[] = &$mform->createElement('text', 'maxh', null, array('size' => '4'));
        $mform->addGroup($grp, 'maxpicdim', get_string('maxdimensions', 'dataformfield_picture'), ' x ', false);
        $mform->setType('maxw', PARAM_INT);
        $mform->setType('maxh', PARAM_INT);
        $mform->addGroupRule('maxpicdim', array('maxw' => array(array(null, 'numeric', null, 'client'))));
        $mform->addGroupRule('maxpicdim', array('maxh' => array(array(null, 'numeric', null, 'client'))));
        $mform->setDefault('maxw', $field->appearance->maxw);
        $mform->setDefault('maxh', $field->appearance->maxh);

        // Thumbnail dimensions (crop if needed).
        $grp = array();
        $grp[] = &$mform->createElement('text', 'thumbw', null, array('size' => '4'));
        $grp[] = &$mform->createElement('text', 'thumbh', null, array('size' => '4'));
        $mform->addGroup($grp, 'thumbgrp', get_string('thumbdimensions', 'dataformfield_picture'), ' x ', false);
        $mform->setType('thumbw', PARAM_INT);
        $mform->setType('thumbh', PARAM_INT);
        $mform->addGroupRule('thumbgrp', array('thumbw' => array(array(null, 'numeric', null, 'client'))));
        $mform->addGroupRule('thumbgrp', array('thumbh' => array(array(null, 'numeric', null, 'client'))));
        $mform->setDefault('thumbw', $field->appearance->thumbw);
        $mform->setDefault('thumbh', $field->appearance->thumbw);

        // File settings.
        $this->definition_file_settings();
    }

    /**
     *
     */
    public function get_data() {
        if ($data = parent::get_data()) {
            // Set appearance (param4).
            $appearance = array();

            // Separator.
            if (!empty($data->separator)) {
                $appearance['separator'] = $data->separator;
            }

            // Display dimensions.
            if (!empty($data->dispw) or !empty($data->disph)) {
                if (!empty($data->dispw)) {
                    $appearance['dispw'] = $data->dispw;
                }
                if (!empty($data->disph)) {
                    $appearance['disph'] = $data->disph;
                }
                if ($data->dispu != 'px') {
                    $appearance['dispu'] = $data->dispu;
                }
            }

            // Max size.
            if (!empty($data->maxw)) {
                $appearance['maxw'] = $data->maxw;
            }
            if (!empty($data->maxh)) {
                $appearance['maxh'] = $data->maxh;
            }

            // Thumb size.
            if (!empty($data->thumbw)) {
                $appearance['thumbw'] = $data->thumbw;
            }
            if (!empty($data->thumbh)) {
                $appearance['thumbh'] = $data->thumbh;
            }

            // Set param4.
            $data->param4 = $appearance ? base64_encode(serialize((object) $appearance)) : null;
        }
        return $data;
    }

    /**
     *
     */
    protected function definition_filetypes() {

        $mform =& $this->_form;

        // Accetped types.
        $options = array();
        $options['*.jpg,*.gif,*.png'] = get_string('filetypeimage', 'dataform');
        $options['*.jpg'] = get_string('filetypejpg', 'dataform');
        $options['*.gif'] = get_string('filetypegif', 'dataform');
        $options['*.png'] = get_string('filetypepng', 'dataform');
        $mform->addElement('select', 'param3', get_string('filetypes', 'dataform'), $options);

    }

}
