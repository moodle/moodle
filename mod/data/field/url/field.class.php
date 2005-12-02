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

require_once($CFG->dirroot.'/mod/data/lib.php');

class data_field_url extends data_field_base {// extends

    function data_field_url($fid=0){
        parent::data_field_base($fid);
    }

    var $type = 'url';
    var $id;    //field id

    function insert_field($dataid, $type='text', $name, $des=''){
        $newfield = new object;
        $newfield->dataid = $dataid;
        $newfield->type = $type;
        $newfield->name = $name;
        $newfield->description = $des;
        if (!insert_record('data_fields',$newfield)){
            notify('Insertion of new field failed!');
        }
    }

    function display_add_field($id, $rid=0){
        global $CFG;
        if (!$field = get_record('data_fields','id',$id)){
            notify("that is not a valid field id!");
            exit;
        }

        //look for that record and pull it out
        if ($rid){
            $datacontent = get_record('data_content','fieldid',$id,'recordid',$rid);
            if (isset($datacontent->content)){
                $content = $datacontent->content;
                $contents = explode('##',$content);
            }else {
                $contents = array();
                $contents[0]='';
                $contents[1]='';
            }
        }
        $url = empty($contents[0])? 'http://':$contents[0];
        $text = empty($contents[1])? '':$contents[1];

        $str = '<table><tr><td align="right">';
        if ($field->description){
            $str .= '<img src="'.$CFG->pixpath.'/help.gif" alt="'.$field->description.'" title="'.$field->description.'">&nbsp;';
        }
        $str .= get_string('url','data').':</td><td><input type="text" name="field_'.$field->id.'_0" id="field_'.$field->id.'_0" value="'.$url.'" /></td></tr>';
        $str .= '<tr><td align="right">'.get_string('text','data').':</td><td><input type="text" name="field_'.$field->id.'_1" id="field_'.$field->id.'_1" value="'.$text.'" /></td></tr>';
        $str .= '</table>';
        
        return $str;
    }

    function display_edit_field($id, $mode=0){
        parent::display_edit_field($id, $mode);
    }

    function display_browse_field($fieldid, $recordid){
        if ($content = get_record('data_content', 'fieldid', $fieldid, 'recordid', $recordid)){
            if (isset($content->content)){
                $str = $content->content;
                $contents = explode('##',$str);
            }
            $url = empty($contents[0])? '':$contents[0];
            $text = empty($contents[1])? '':$contents[1];
            if (empty($text)){
                $text = $url;
            }
            return '<a href = "'.$url.'">'.$text.'</a>';
        }
        return false;
        
    }

    function update($fieldobject){
        if (!update_record('data_fields',$fieldobject)){
            notify ('upate failed');
        }
    }

    function store_data_content($fieldid, $recordid, $value, $name=''){
        $content = new object;
        $content->fieldid = $fieldid;
        $content->recordid = $recordid;
        $content->content = $value;
        $names = explode('_',$name);
        switch ($names[2]){
            case 1:    //add text
                if ($oldcontent = get_record('data_content','fieldid', $fieldid, 'recordid', $recordid)){
                    if ($value){
                        $content->id = $oldcontent->id;
                        $contents = explode('##',$oldcontent->content);
                        $content->content = $contents[0].'##'.clean_param($value, PARAM_NOTAGS);
                        update_record('data_content',$content);
                    }
                }
                break;
            case 0:    //add link
                if ($value){
                    $content->content = clean_param($value, PARAM_URL).'##';
                    if ($content->content != 'http://##' && $content->content != '##'){
                        insert_record('data_content',$content);    //after trim if content is still there
                    }    
                    else {
                        notify(get_string('invalidurl','data'));
                    }
                }    //no point adding if it's empty
                break;
            default:
                break;
        }
    }

    function update_data_content($fieldid, $recordid, $value, $name){
        //if data_content already exit, we update
        if ($oldcontent = get_record('data_content','fieldid', $fieldid, 'recordid', $recordid)){
            $content = new object;
            $content->fieldid = $fieldid;
            $content->recordid = $recordid;
            $content->id = $oldcontent->id;
            
            $contents = explode('##',$oldcontent->content);
            $names = explode('_',$name);
            switch ($names[2]){
            case 1:    //add text
                $content->content = $contents[0].'##'.clean_param($value, PARAM_NOTAGS);
                update_record('data_content',$content);
                break;
            case 0:    //update link
                $content->content = clean_param($value, PARAM_URL).'##'.$contents[1];
                update_record('data_content',$content);
                break;
            default:
                break;
            }
        }
        else {    //make 1 if there isn't one already
            $this->store_data_content($fieldid, $recordid, $value, $name);
        }
    }
    
    function notemptyfield($value, $name){
        $names = explode('_',$name);
        $value = clean_param($value, PARAM_URL);    //clean first
        if ($names[2] == '0'){
            return ($value!='http://' && !empty($value));
        }
        return false;
    }

}

?>
