<?php
///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.org                                            //
//                                                                       //
// Copyright (C) 1999-onwards Moodle Pty Ltd  http://moodle.com          //
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   // //                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

class data_field_textarea extends data_field_base {

    var $type = 'textarea';

    function display_add_field($recordid=0) {
        global $CFG, $DB, $OUTPUT;

        $text   = '';
        $format = 0;

        if ($recordid){
            if ($content = $DB->get_record('data_content', array('fieldid'=>$this->field->id, 'recordid'=>$recordid))) {
                $text   = $content->content;
                $format = $content->content1;
            }
        }

        $str = '<div title="'.$this->field->description.'">';

        if (can_use_html_editor()) {
            // Show a rich text html editor.
            $str .= $this->gen_textarea(true, $text);
            $str .= $OUTPUT->help_icon(moodle_help_icon::make("richtext2", get_string("helprichtext"), 'moodle', true));
            $str .= '<input type="hidden" name="field_' . $this->field->id . '_content1' . '" value="' . FORMAT_HTML . '" />';

        } else {
            // Show a normal textarea. Also let the user specify the format to be used.
            $str .= $this->gen_textarea(false, $text);

            // Get the available text formats for this field.
            $formatsForField = format_text_menu();
            $str .= '<br />';
            $select = html_select( $formatsForField, 'field_' . $this->field->id . '_content1', $format);
            $select->nothingvalue = '';
            $str .= $OUTPUT->select($select);

            $str .= $OUTPUT->help_icon(moodle_help_icon::make('textformat', get_string('helpformatting'), 'moodle'));
        }
        $str .= '</div>';
        return $str;
    }


    function display_search_field($value = '') {
        return '<input type="text" size="16" name="f_'.$this->field->id.'" value="'.$value.'" />';
    }

    function parse_search_field() {
        return optional_param('f_'.$this->field->id, '', PARAM_NOTAGS);
    }

    function generate_sql($tablealias, $value) {
        global $DB;

        $ILIKE = $DB->sql_ilike();

        static $i=0;
        $i++;
        $name = "df_picture_$i";
        return array(" ({$tablealias}.fieldid = {$this->field->id} AND {$tablealias}.content $ILIKE :$name) ", array($name=>"%$value%"));
    }

    function gen_textarea($usehtmleditor, $text='') {
        return print_textarea($usehtmleditor, $this->field->param3, $this->field->param2,
                              '', '', 'field_'.$this->field->id, $text, '', true, 'field_' . $this->field->id);
    }


    function print_after_form() {
    }


    function update_content($recordid, $value, $name='') {
        global $DB;

        $content = new object;
        $content->fieldid = $this->field->id;
        $content->recordid = $recordid;

        $names = explode('_', $name);
        if (!empty($names[2])) {
            $content->$names[2] = clean_param($value, PARAM_NOTAGS);  // content[1-4]
        } else {
            $content->content = clean_param($value, PARAM_CLEAN);
        }

        if ($oldcontent = $DB->get_record('data_content', array('fieldid'=>$this->field->id, 'recordid'=>$recordid))) {
            $content->id = $oldcontent->id;
            return $DB->update_record('data_content', $content);
        } else {
            return $DB->insert_record('data_content', $content);
        }
    }
}

