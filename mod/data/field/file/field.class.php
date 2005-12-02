<?php ///Class file for text field, extends field_picture
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
require_once($CFG->dirroot.'/mod/data/lib.php');

class data_field_file extends data_field_base {// extends
   
    function data_field_file($fid=0){
        parent::data_field_base($fid);
    }

    /* the content field is store as this
     * content = a ## b where a is the filename,
     * b is the display file name
     */

    var $type = 'file';
    var $id;

    function insert_field($dataid, $type='file', $name, $des){
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
        global $CFG, $course;
        if (!$field = get_record('data_fields','id',$id)){
            notify("that is not a valid field id!");
            exit;
        }

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

            $src = empty($contents[0])? '':$contents[0];
            $name = empty($contents[1])? $src:$contents[1];
            $displayname = empty($contents[1])? '':$contents[1];

            if ($CFG->slasharguments) {
                $source = $CFG->wwwroot.'/file.php/'.$course->id.'/'.$CFG->moddata.'/data/'.$field->dataid.'/'.$field->id.'/'.$rid;
            } else {
                $source = $CFG->wwwroot.'/file.php?file=/'.$course->id.'/'.$CFG->moddata.'/data/'.$field->dataid.'/'.$field->id.'/'.$rid;
            }
        }
        else {
            $displayname = '';
            $name = '';
            $src = '';
            $source = '';
        }
        
        $str = '';

        if ($field->description){
            $str .= '<img src="'.$CFG->pixpath.'/help.gif" alt="'.$field->description.'" title="'.$field->description.'">&nbsp;';
        }

        $str .= '<input type="hidden" name ="field_'.$field->id.'_0" id="field_'.$field->id.'"_0  value="fakevalue" />';
        $str .= get_string('file','data'). ': <input type="file" name ="field_'.$field->id.'" id="field_'.$field->id.'" /><br />';
        $str .= get_string('optionalfilename','data').': <input type="text" name="field_'
                .$field->id.'_1" id="field_'.$field->id.'_1" value="'.$displayname.'" /><br />';
        $str .= '<input type="hidden" name="MAX_FILE_SIZE" value="'.$field->param4.'" />';

        //print icon
        if ($rid and isset($content)){
            require_once($CFG->libdir.'/filelib.php');
            $icon = mimeinfo('icon', $src);
            $str .= '<img align="absmiddle" src="'.$CFG->pixpath.'/f/'.$icon.'" height="16" width="16" alt="'.$icon.'" />&nbsp;'.
                    '<a href="'.$source.'/'.$src.'" >'.$name.'</a>';
        }
        return $str;
    }

    function display_edit_field($id, $mode=0){
        parent::display_edit_field($id, $mode);
    }

    function display_browse_field($fieldid, $recordid){

        global $CFG, $USER, $course;

        $field = get_record('data_fields', 'id', $fieldid);

        if ($content = get_record('data_content', 'fieldid', $fieldid, 'recordid', $recordid)){
            if (isset($content->content)){
                $contents = explode('##',$content->content);
            }

            $src = empty($contents[0])? '':$contents[0];
            $name = empty($contents[1])? $src:$contents[1];

            
            if ($CFG->slasharguments) {
                $source = $CFG->wwwroot.'/file.php/'.$course->id.'/'.$CFG->moddata.'/data/'.$field->dataid.'/'.$field->id.'/'.$recordid;
            } else {
                $source = $CFG->wwwroot.'/file.php?file=/'.$course->id.'/'.$CFG->moddata.'/data/'.$field->dataid.'/'.$field->id.'/'.$recordid;
            }

            $width = $field->param1 ? ' width = "'.$field->param1.'" ':' ';
            $height = $field->param2 ? ' height = "'.$field->param2.'" ':' ';
            
            require_once($CFG->libdir.'/filelib.php');
            $icon = mimeinfo('icon', $src);
            $str = '<img align="absmiddle" src="'.$CFG->pixpath.'/f/'.$icon.'" height="16" width="16" alt="'.$icon.'" />&nbsp;'.
                            '<a href="'.$source.'/'.$src.'" >'.$name.'</a>';
            return $str;
        }
        return false;
    }


     function update($fieldobject){
        if (!update_record('data_fields',$fieldobject)){
            notify ('upate failed');
        }
    }

    function store_data_content($fieldid, $recordid, $value, $name=''){
        global $CFG, $course;

        $content = new object;
        $content->fieldid = $fieldid;
        $content->recordid = $recordid;

        $field = get_record('data_fields','id',$fieldid);

        $names = explode('_',$name);
        switch ($names[2]){
            case 0:    //file just uploaded
                $filename = $_FILES[$names[0].'_'.$names[1]];
                $filename = $filename['name'];
                $dir = $course->id.'/'.$CFG->moddata.'/data/'.$field->dataid.'/'.$field->id.'/'.$recordid;

                /************ FILE MANAGER HERE ***************/
                require_once('../../lib/uploadlib.php');

                $um = new upload_manager($names[0].'_'.$names[1],true,false,$course,false,$field->param3);
                if ($um->process_file_uploads($dir)) {    //write to content
                    $newfile_name = $um->get_new_filename();
                    $content->content = $newfile_name.'##';
                    insert_record('data_content',$content);
                }
                break;
            case 1:    //only changing alt tag
                if ($oldcontent = get_record('data_content','fieldid', $fieldid, 'recordid', $recordid)){
                    $content->id = $oldcontent ->id;
                    $contents = explode('##',$oldcontent->content);
                    $content->content = $contents[0].'##'.clean_param($value, PARAM_NOTAGS);
                    update_record('data_content',$content);
                }
                break;
            default:
                break;
        }    //close switch
    }

    function update_data_content($fieldid, $recordid, $value, $name){
        //if data_content already exit, we update
        global $CFG,$course;
        if ($oldcontent = get_record('data_content','fieldid', $fieldid, 'recordid', $recordid)){

            $content = new object;
            $content->fieldid = $fieldid;
            $content->recordid = $recordid;
            $content->id = $oldcontent ->id;
            $names = explode('_',$name);

            $field = get_record('data_fields','id',$fieldid);

            $contents = explode('##',$oldcontent->content);
            switch ($names[2]){
                case 0:    //file just uploaded
                    $filename = $_FILES[$names[0].'_'.$names[1]];
                    $filename = $filename['name'];

                    $dir = $course->id.'/'.$CFG->moddata.'/data/'.$field->dataid.'/'.$field->id.'/'.$recordid;

                    //only use the manager if file is present, to avoid "are you sure you selected a file to upload" msg
                    if ($filename){
                    /************ FILE MANAGER HERE ***************/
                        require_once('../../lib/uploadlib.php');
                        $um = new upload_manager($names[0].'_'.$names[1],true,false,$course,false,$field->param3);
                        if ($um->process_file_uploads($dir)) {
                        //write to content
                            $newfile_name = $um->get_new_filename();
                            $content->content = $newfile_name.'##'.$contents[1];
                            update_record('data_content',$content);
                        }
                    }
                    break;
                case 1:    //only changing alt tag
                    $content->content = $contents[0].'##'.clean_param($value, PARAM_NOTAGS);
                    update_record('data_content',$content);
                    break;
                default:
                    break;
            }    //close switch
        }
        else {    //make 1 if there isn't one already
            $this->store_data_content($fieldid, $recordid, $value, $name);
        }
    }

    function notemptyfield($value, $name){
        $names = explode('_',$name);
        if ($names[2] == '0'){
            $filename = $_FILES[$names[0].'_'.$names[1]];
            return !empty($filename['name']);    //if there's a file in $_FILES, not empty
        }
        return false;
    }

    function delete_data_content_files($dataid, $recordid, $content){
        global $CFG, $course;
        $fileinfo = split('##',$content);
        $filepath = $CFG->dataroot.'/'.$course->id.'/'.$CFG->moddata.'/data/'.$dataid.'/'.$this->id.'/'.$recordid;
        unlink($filepath.'/'.$fileinfo[0]);
        rmdir($filepath);
        notify ($fileinfo[0].' deleted');
    }
    
}

?>
