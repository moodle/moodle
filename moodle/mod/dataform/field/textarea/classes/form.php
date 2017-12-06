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
 * @package dataformfield_textarea
 * @copyright 2016 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class dataformfield_textarea_form extends \mod_dataform\pluginbase\dataformfieldform {

    /**
     * Field settings.
     */
    protected function field_definition() {
        global $CFG;

        $mform =& $this->_form;

        // Enable editor.
        $mform->addElement('selectyesno', 'param1', get_string('editorenable', 'dataform'));

        // Size units.
        $sizeunits = array(
            'px' => 'px',
            'em' => 'em',
            '%' => '%'
        );

        // Field width (param2).
        $colsunit = array('cols' => get_string('cols', 'editor'));
        $options = array(
            'units' => $colsunit + $sizeunits,
        );
        $this->add_field_size_elements('width', get_string('width', 'editor'), $options);

        // Field height (param3).
        $rowsunit = array('rows' => get_string('rows', 'editor'));
        $options = array(
            'units' => $rowsunit + $sizeunits,
        );
        $this->add_field_size_elements('height', get_string('height', 'editor'), $options);

        // Text format.
        $mform->addElement('select', 'param7', get_string('format'), format_text_menu());

        // Trust text.
        $mform->addElement('selectyesno', 'param4', get_string('trusttext', 'dataformfield_textarea'));

        // Editor file settings.
        $mform->addElement('header', 'filesettingshdr', get_string('filesettings', 'dataform'));

        // Max bytes.
        $options = get_max_upload_sizes($CFG->maxbytes, $this->_field->df->course->maxbytes);
        $mform->addElement('select', 'param5', get_string('filemaxsize', 'dataform'), $options);
        $mform->disabledIf('param5', 'param1', 'eq', 0);

        // Max files.
        $range = range(1, 100);
        $options = array(-1 => get_string('unlimited')) + array_combine($range, $range);
        $mform->addElement('select', 'param6', get_string('filesmax', 'dataform'), $options);
        $mform->disabledIf('param6', 'param1', 'eq', 0);

    }

    /**
     *
     */
    public function definition_default_content() {
        $mform = &$this->_form;
        $field = &$this->_field;

        $label = get_string('fielddefaultvalue', 'dataform');
        if (!$field->is_editor()) {
            // TEXTAREA.
            $mform->addElement('textarea', 'contentdefault', $label);
        } else {
            // EDITOR.
            $editoroptions = $field->editoroptions + array('collapsed' => true);
            $mform->addElement('editor', 'contentdefault_editor', $label, null, $editoroptions);
        }
    }

    /**
     *
     */
    public function data_preprocessing(&$data) {
        $field = &$this->_field;

        // Width.
        if (!empty($data->param2)) {
            $sizeandunit = array_merge(explode(' ', $data->param2), array(null));
            list($data->width, $data->widthunit) = $sizeandunit;
        }
        // Height.
        if (!empty($data->param3)) {
            $sizeandunit = array_merge(explode(' ', $data->param3), array(null));
            list($data->height, $data->heightunit) = $sizeandunit;
        }

        // Default content.
        if ($data->contentdefault = $field->defaultcontent) {
            // Adjust content for editor mode.
            if ($field->is_editor()) {
                $data->contentdefaultformat = FORMAT_HTML;

                $data = file_prepare_standard_editor(
                    $data,
                    'contentdefault',
                    $field->editoroptions,
                    $field->df->context,
                    'dataformfield_textarea',
                    'content',
                    $field->id
                );
            }
        }

    }

    /**
     * Returns the default content data.
     *
     * @param stdClass $data
     * @return mix|null
     */
    protected function get_data_default_content(\stdClass $data) {
        $field = &$this->_field;

        if (!$field->is_editor()) {
            // TEXTAREA.
            if (!empty($data->contentdefault)) {
                return $data->contentdefault;
            }
        } else {
            // EDITOR.
            $data = file_postupdate_standard_editor(
                $data,
                'contentdefault',
                $field->editoroptions,
                $field->df->context,
                'dataformfield_textarea',
                'content',
                $field->id
            );
            if (!empty($data->contentdefault)) {
                return $data->contentdefault;
            }
        }
        return null;
    }

    /**
     *
     */
    public function get_data() {
        if ($data = parent::get_data()) {
            // Width.
            $data->param2 = null;
            if (!empty($data->width)) {
                $data->param2 = $data->width;
                if ($data->widthunit != 'cols') {
                    $data->param2 .= ' '. $data->widthunit;
                }
            }

            // Height.
            $data->param3 = null;
            if (!empty($data->height)) {
                $data->param3 = $data->height;
                if ($data->heightunit != 'rows') {
                    $data->param3 .= ' '. $data->heightunit;
                }
            }
        }
        return $data;
    }

}
