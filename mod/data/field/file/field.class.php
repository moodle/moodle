<?php  // $Id$
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

    function data_field_file($field=0, $data=0) {
        parent::data_field_base($field, $data);
    }

    function display_add_field($recordid=0) {
        global $CFG;
        if ($recordid){
            if ($content = get_record('data_content', 'fieldid', $this->field->id, 'recordid', $recordid)) {
                $contents[0] = $content->content;
                $contents[1] = $content->content1;
            } else {
                $contents[0] = '';
                $contents[1] = '';
            }
            $src         = empty($contents[0]) ? '' : $contents[0];
            $name        = empty($contents[1]) ? $src : $contents[1];
            $displayname = empty($contents[1]) ? '' : $contents[1];
            require_once($CFG->libdir.'/filelib.php');
            $source = get_file_url($this->data->course.'/'.$CFG->moddata.'/data/'.$this->data->id.'/'.$this->field->id.'/'.$recordid);
        } else {
            $src = '';
            $name = '';
            $displayname = '';
            $source = '';
        }
        $str = '<div title="' . s($this->field->description) . '">';
        $str .= '<fieldset><legend><span class="accesshide">'.$this->field->name.'</span></legend>';
        $str .= '<input type="hidden" name ="field_'.$this->field->id.'_file" value="fakevalue" />';
        $str .= get_string('file','data'). ' <input type="file" name ="field_'.$this->field->id.'" id="field_'.
                            $this->field->id.'" title="'.s($this->field->description).'" /><br />';
        $str .= get_string('optionalfilename','data').' <input type="text" name="field_' .$this->field->id.'_filename"
                            id="field_'.$this->field->id.'_filename" value="'.s($displayname).'" /><br />';
        $str .= '<input type="hidden" name="MAX_FILE_SIZE" value="'.s($this->field->param3).'" />';
        $str .= '</fieldset>';
        $str .= '</div>';
        if ($recordid and isset($content) and !empty($content->content)) {
            // Print icon
            require_once($CFG->libdir.'/filelib.php');
            $icon = mimeinfo('icon', $src);
            $str .= '<img src="'.$CFG->pixpath.'/f/'.$icon.'" class="icon" alt="'.$icon.'" />'.
                    '<a href="'.$source.'/'.$src.'" >'.$name.'</a>';
        }
        return $str;
    }

    function display_search_field($value = '') {
        return '<input type="text" size="16" name="f_'.$this->field->id.'" value="'.$value.'" />';
    }

    function generate_sql($tablealias, $value) {
        return " ({$tablealias}.fieldid = {$this->field->id} AND {$tablealias}.content LIKE '%{$value}%') ";
    }

    function parse_search_field() {
        return optional_param('f_'.$this->field->id, '', PARAM_NOTAGS);
    }

    function display_browse_field($recordid, $template) {
        global $CFG;
        if (!$content = get_record('data_content', 'fieldid', $this->field->id, 'recordid', $recordid)) {
            return false;
        }
        $width = $this->field->param1 ? ' width = "'.s($this->field->param1).'" ':' ';
        $height = $this->field->param2 ? ' height = "'.s($this->field->param2).'" ':' ';
        if (empty($content->content)) {
            return '';
        }
        require_once($CFG->libdir.'/filelib.php');
        $src  = $content->content;
        $name = empty($content->content1) ? $src : $content->content1;
        $source = get_file_url($this->data->course.'/'.$CFG->moddata.'/data/'.$this->data->id.'/'.$this->field->id.'/'.$recordid);
        $icon = mimeinfo('icon', $src);
        $str = '<img src="'.$CFG->pixpath.'/f/'.$icon.'" height="16" width="16" alt="'.$icon.'" />&nbsp;'.
                        '<a href="'.$source.'/'.$src.'" >'.$name.'</a>';
        return $str;
    }


    // content: "a##b" where a is the file name, b is the display name
    function update_content($recordid, $value, $name) {
        global $CFG;
        if (!$oldcontent = get_record('data_content','fieldid', $this->field->id, 'recordid', $recordid)) {
        // Quickly make one now!
            $oldcontent = new object;
            $oldcontent->fieldid = $this->field->id;
            $oldcontent->recordid = $recordid;
            if (!$oldcontent->id = insert_record('data_content', $oldcontent)) {
                error('Could not make an empty record!');
            }
        }
        $content = new object;
        $content->id = $oldcontent->id;
        $names = explode('_',$name);
        switch ($names[2]) {
            case 'file':
                // file just uploaded
#                $course = get_course('course', 'id', $this->data->course);
                $filename = $_FILES[$names[0].'_'.$names[1]];
                $filename = $filename['name'];
                $dir = $this->data->course.'/'.$CFG->moddata.'/data/'.$this->data->id.'/'.$this->field->id.'/'.$recordid;
                // only use the manager if file is present, to avoid "are you sure you selected a file to upload" msg
                if ($filename){
                    require_once($CFG->libdir.'/uploadlib.php');
                    // FIX ME: $course not defined here
                    $um = new upload_manager($names[0].'_'.$names[1],true,false,$this->data->course,false,$this->field->param3);
                    if ($um->process_file_uploads($dir)) {
                        $newfile_name = $um->get_new_filename();
                        $content->content = $newfile_name;
                        update_record('data_content',$content);
                    }
                }
                break;

            case 'filename':
                // only changing alt tag
                $content->content1 = clean_param($value, PARAM_NOTAGS);
                update_record('data_content', $content);
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

}

?>
