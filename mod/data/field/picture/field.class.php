<?php
///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.org                                            //
//                                                                       //
// Copyright (C) 2005 Martin Dougiamas  http://dougiamas.com             //
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

/// Please refer to lib.php for method comments

global $CFG;

//load base class
require_once($CFG->dirroot.'/mod/data/field/file/field.class.php');

class data_field_picture extends data_field_file {// extends

    /* the content field is store as this
     * content = a ## b where a is the filename,
     * b is the alt tag text
     */
    function data_field_picture($fid=0){
        parent::data_field_base($fid);
    }

    var $type = 'picture';
    var $id;

    function insert_field($dataid, $type='picture', $name, $des, $width='', $height='', $maxsize='', 
                                                                 $widthlist='', $heightlist=''){
        $newfield = new object;
        $newfield->dataid = $dataid;
        $newfield->type = $type;
        $newfield->name = $name;
        $newfield->description = $des;
        $newfield->param1 = $width;
        $newfield->param2 = $height;
        $newfield->param3 = $maxsize;
        $newfield->param4 = $widthlist;
        $newfield->param5 = $heightlist;
        if (!insert_record('data_fields',$newfield)){
            notify('Insertion of new field failed!');
        }
    }

    function display_add_field($id, $rid=0){
        global $CFG, $course;
        if (!$field = get_record('data_fields','id',$id)){
            notify("that is not a valid field id!");
            exit;
        }

        if ($rid){
            $datacontent = get_record('data_content','fieldid',$id,'recordid',$rid);
            if (isset($datacontent->content)){
                $content = $datacontent->content;
                $contents[0] = $datacontent->content;
                $contents[1] = $datacontent->content1;
            }else {
                $contents = array();
                $contents[0]='';
                $contents[1]='';
            }
            $des = empty($contents[1])? '':$contents[1];
            if ($CFG->slasharguments) {
                $source = $CFG->wwwroot.'/file.php/'.$course->id.'/'.$CFG->moddata.'/data/'.$field->dataid.'/'.$field->id.'/'.$rid;
            } else {
                $source = $CFG->wwwroot.'/file.php?file=/'.$course->id.'/'.$CFG->moddata.'/data/'.$field->dataid.'/'.$field->id.'/'.$rid;
            }
        }
        else {
            $des = '';
        }

        $str = '';
        /*
        if ($field->description){
            $str .= '<img src="'.$CFG->pixpath.'/help.gif" alt="'.$field->description.'" title="'.$field->description.'">&nbsp;';
        }
        */
        $str .= '<div title="'.$field->description.'">';
        $str .= '<input type="hidden" name ="field_'.$field->id.'_0" id="field_'.$field->id.'_0"  value="fakevalue" />';
        $str .= get_string('picture','data'). ': <input type="file" name ="field_'.$field->id.'" id="field_'.$field->id.'" /><br />';
        $str .= get_string('optionaldescription','data') .': <input type="text" name="field_'
                .$field->id.'_1" id="field_'.$field->id.'_1" value="'.$des.'" /><br />';
        $str .= '<input type="hidden" name="MAX_FILE_SIZE" value="'.$field->param3.'" />';
        if ($rid and $contents[0]){
            $str .= '<img width="50" height="50" src="'.$source.'/'.$contents[0].'">';
        }
        $str .= '</div>';
        return $str;
    }

    function display_edit_field($id, $mode=0){
        parent::display_edit_field($id, $mode);
    }

    function display_browse_field($fieldid, $recordid, $template) {
        global $CFG, $USER;
        
        $field = get_record('data_fields', 'id', $fieldid);
        $data = get_record('data', 'id', $field->dataid);
        
        if ($content = get_record('data_content', 'fieldid', $fieldid, 'recordid', $recordid)){
            if (isset($content->content)){
                $contents[0] = $content->content;
                $contents[1] = $content->content1;
            }

            $alt = empty($contents[1])? '':$contents[1];
            $title = empty($contents[1])? '':$contents[1];
            $src = empty($contents[0])? '':$contents[0];

            if ($CFG->slasharguments) {
                $source = $CFG->wwwroot.'/file.php/'.$data->course.'/'.$CFG->moddata.'/data/'.$field->dataid.'/'.$field->id.'/'.$recordid;
            } else {
                $source = $CFG->wwwroot.'/file.php?file=/'.$data->course.'/'.$CFG->moddata.'/data/'.$field->dataid.'/'.$field->id.'/'.$recordid;
            }

            if ($template == 'listtemplate') {
                $width = $field->param4 ? ' width="'.$field->param4.'" ':' ';
                $height = $field->param5 ? ' height="'.$field->param5.'" ':' ';
                $str = '<a href="view.php?d='.$field->dataid.'&amp;rid='.$recordid.'"><img '.$width.$height.' src="'.$source.'/'.$src.'" alt="'.$alt.'" title="'.$title.'" border="0" /></a>';
            } else {
                $width = $field->param1 ? ' width="'.$field->param1.'" ':' ';
                $height = $field->param2 ? ' height="'.$field->param2.'" ':' ';
                $str = '<img '.$width.$height.' src="'.$source.'/'.$src.'" alt="'.$alt.'" title="'.$title.'" />';
            }
            return $str;
        }
        return false;
    }

    function update($fieldobject){
        parent::update($fieldobject);
    }

    function store_data_content($fieldid, $recordid, $value, $name=''){
        parent::store_data_content($fieldid, $recordid, $value, $name);
    }

    function update_data_content($fieldid, $recordid, $value, $name){
        parent::update_data_content($fieldid, $recordid, $value, $name);
    }

    function notemptyfield($value, $name){
        return (parent::notemptyfield($value, $name));
    }

}

?>
