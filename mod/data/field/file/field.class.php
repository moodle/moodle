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
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

class data_field_file extends data_field_base {
    var $type = 'file';

    function display_add_field($recordid=0) {
        global $CFG, $DB, $OUTPUT;

        $file        = false;
        $content     = false;
        $displayname = '';
        $fs = get_file_storage();
        if ($recordid){
            if ($content = $DB->get_record('data_content', array('fieldid'=>$this->field->id, 'recordid'=>$recordid))) {
                if (!empty($content->content)) {
                    if ($file = $fs->get_file($this->context->id, 'data_content', $content->id, '/', $content->content)) {
                        if (empty($content->content1)) {
                            $displayname = $file->get_filename();
                        } else {
                            $displayname = $content->content1;
                        }
                    }
                }
            }
        }

        $str = '<div title="'.s($this->field->description).'">';
        $str .= '<fieldset><legend><span class="accesshide">'.$this->field->name.'</span></legend>';
        $str .= '<input type="hidden" name ="field_'.$this->field->id.'_file" value="fakevalue" />';
        $str .= get_string('file','data').' <input type="file" name ="field_'.$this->field->id.'" id="field_'.
                            $this->field->id.'" title="'.s($this->field->description).'" /><br />';
        $str .= get_string('optionalfilename','data').' <input type="text" name="field_'.$this->field->id.'_filename"
                            id="field_'.$this->field->id.'_filename" value="'.s($displayname).'" /><br />';
        //$str .= '<input type="hidden" name="MAX_FILE_SIZE" value="'.s($this->field->param3).'" />';
        $str .= '</fieldset>';
        $str .= '</div>';
        if ($file) {
            // Print icon if file already exists
            $browser = get_file_browser();
            $src     = file_encode_url($CFG->wwwroot.'/pluginfile.php', $this->context->id.'/data_content/'.$content->id.'/'.$file->get_filename());
            $str .= '<img src="'.$OUTPUT->pix_url(file_mimetype_icon($file->get_mimetype())).'" class="icon" alt="'.$file->get_mimetype().'" />'.
                    '<a href="'.$src.'" >'.s($file->get_filename()).'</a>';
        }
        return $str;
    }

    function display_search_field($value = '') {
        return '<input type="text" size="16" name="f_'.$this->field->id.'" value="'.$value.'" />';
    }

    function generate_sql($tablealias, $value) {
        global $DB;

        $ILIKE = $DB->sql_ilike();

        static $i=0;
        $i++;
        $name = "df_file_$i";
        return array(" ({$tablealias}.fieldid = {$this->field->id} AND {$tablealias}.content $ILIKE :$name) ", array($name=>"%$value%"));
    }

    function parse_search_field() {
        return optional_param('f_'.$this->field->id, '', PARAM_NOTAGS);
    }

    function get_file($recordid, $content=null) {
        global $DB;
        if (empty($content)) {
            if (!$content = $DB->get_record('data_content', array('fieldid'=>$this->field->id, 'recordid'=>$recordid))) {
                return null;
            }
        }
        $fs = get_file_storage();
        if (!$file = $fs->get_file($this->context->id, 'data_content', $content->id, '/', $content->content)) {
            return null;
        }

        return $file;
    }

    function display_browse_field($recordid, $template) {
        global $CFG, $DB;

        if (!$content = $DB->get_record('data_content', array('fieldid'=>$this->field->id, 'recordid'=>$recordid))) {
            return '';
        }

        if (empty($content->content)) {
            return '';
        }

        $browser = get_file_browser();
        if (!$file = $this->get_file($recordid, $content)) {
            return '';
        }

        $name   = empty($content->content1) ? $file->get_filename() : $content->content1;
        $src    = file_encode_url($CFG->wwwroot.'/pluginfile.php', '/'.$this->context->id.'/data_content/'.$content->id.'/'.$file->get_filename());
        $width  = $this->field->param1 ? ' width  = "'.s($this->field->param1).'" ':' ';
        $height = $this->field->param2 ? ' height = "'.s($this->field->param2).'" ':' ';

        $str = '<img src="'.$OUTPUT->pix_url(file_mimetype_icon($file->get_mimetype())).'" height="16" width="16" alt="'.$file->get_mimetype().'" />&nbsp;'.
               '<a href="'.$src.'" >'.s($name).'</a>';
        return $str;
    }


    // content: "a##b" where a is the file name, b is the display name
    function update_content($recordid, $value, $name) {
        global $CFG, $DB;

        if (!$content = $DB->get_record('data_content', array('fieldid'=>$this->field->id, 'recordid'=>$recordid))) {
        // Quickly make one now!
            $content = new object();
            $content->fieldid  = $this->field->id;
            $content->recordid = $recordid;
            $id = $DB->insert_record('data_content', $content);
            $content = $DB->get_record('data_content', array('id'=>$id));
        }

        $names = explode('_', $name);
        switch ($names[2]) {
            case 'file':
                // file just uploaded
                $tmpfile = $_FILES[$names[0].'_'.$names[1]];
                $filename = $tmpfile['name'];
                $pathanme = $tmpfile['tmp_name'];
                if ($filename){
                    $fs = get_file_storage();
                    // TODO: uploaded file processing will be in file picker ;-)
                    $fs->delete_area_files($this->context->id, 'data_content', $content->id);
                    $file_record = array('contextid'=>$this->context->id, 'filearea'=>'data_content', 'itemid'=>$content->id, 'filepath'=>'/', 'filename'=>$filename);
                    if ($file = $fs->create_file_from_pathname($file_record, $pathanme)) {
                        $content->content = $file->get_filename();
                        $DB->update_record('data_content', $content);
                    }
                }
                break;

            case 'filename':
                // only changing alt tag
                $content->content1 = clean_param($value, PARAM_NOTAGS);
                $DB->update_record('data_content', $content);
                break;

            default:
                break;
        }
    }

    function notemptyfield($value, $name) {
        $names = explode('_',$name);
        if ($names[2] == 'file') {
            $filename = $_FILES[$names[0].'_'.$names[1]];
            return !empty($filename['name']);
            // if there's a file in $_FILES, not empty
        }
        return false;
    }

    function text_export_supported() {
        return false;
    }

    function file_ok($path) {
        return true;
    }

}


