<?php // $Id$
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

require_once($CFG->dirroot.'/mod/data/field/file/field.class.php'); // Base class is 'file'

class data_field_picture extends data_field_file { 

    var $type = 'picture';

    var $previewwidth  = 50;
    var $previewheight = 50;

    function data_field_picture($field=0, $data=0) {
        parent::data_field_base($field, $data);
    }

    function display_add_field($recordid=0){
        global $CFG;

        $filepath = '';
        $filename = '';
        $description = '';

        if ($recordid){
            if ($content = get_record('data_content', 'fieldid', $this->field->id, 'recordid', $recordid)) {
                $filename = $content->content;
                $description = $content->content1;
            }

            $path = $this->data->course.'/'.$CFG->moddata.'/data/'.$this->data->id.'/'.$this->field->id.'/'.$recordid;

            if ($CFG->slasharguments) {
                $filepath = $CFG->wwwroot.'/file.php/'.$path.'/'.$filename;
            } else {
                $filepath = $CFG->wwwroot.'/file.php?file=/'.$path.'/'.$filename;
            }
        }

        $str = '';
        $str .= '<div title="'.$this->field->description.'">';
        $str .= '<input type="hidden" name ="field_'.$this->field->id.'_0" id="field_'.$this->field->id.'_0"  value="fakevalue" />';
        $str .= get_string('picture','data'). ': <input type="file" name ="field_'.$this->field->id.'" id="field_'.$this->field->id.'" /><br />';
        $str .= get_string('optionaldescription','data') .': <input type="text" name="field_'
                .$this->field->id.'_1" id="field_'.$this->field->id.'_1" value="'.$description.'" /><br />';
        $str .= '<input type="hidden" name="MAX_FILE_SIZE" value="'.$this->field->param3.'" />';
        if ($filepath){
            $str .= '<img width="'.$this->previewwidth.'" height="'.$this->previewheight.'" src="'.$filepath.'" />';
        }
        $str .= '</div>';
        return $str;
    }

    function display_browse_field($recordid, $template) {
        global $CFG;
        
        if ($content = get_record('data_content', 'fieldid', $this->field->id, 'recordid', $recordid)){
            if (isset($content->content)){
                $contents[0] = $content->content;
                $contents[1] = $content->content1;
            }

            $alt = empty($contents[1])? '':$contents[1];
            $title = empty($contents[1])? '':$contents[1];
            $src = empty($contents[0])? '':$contents[0];

            $path = $this->data->course.'/'.$CFG->moddata.'/data/'.$this->data->id.'/'.$this->field->id.'/'.$recordid;

            if ($CFG->slasharguments) {
                $source = $CFG->wwwroot.'/file.php/'.$path;
            } else {
                $source = $CFG->wwwroot.'/file.php?file=/'.$path;
            }

            if ($template == 'listtemplate') {
                $width = $this->field->param4 ? ' width="'.$this->field->param4.'" ' : ' ';
                $height = $this->field->param5 ? ' height="'.$this->field->param5.'" ' : ' ';
                $str = '<a href="view.php?d='.$this->field->dataid.'&amp;rid='.$recordid.'"><img '.
                     $width.$height.' src="'.$source.'/'.$src.'" alt="'.$alt.'" title="'.$title.'" border="0" /></a>';
            } else {
                $width = $this->field->param1 ? ' width="'.$this->field->param1.'" ':' ';
                $height = $this->field->param2 ? ' height="'.$this->field->param2.'" ':' ';
                $str = '<img '.$width.$height.' src="'.$source.'/'.$src.'" alt="'.$alt.'" title="'.$title.'" />';
            }
            return $str;
        }
        return false;
    }

}

?>
