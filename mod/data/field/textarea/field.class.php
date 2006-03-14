<?php ///Class file for textarea field, extends base_field
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

class data_field_textarea extends data_field_base {

    var $type = 'textarea';
    var $id;

    
    function data_field_textarea($fid=0){
        parent::data_field_base($fid);
    }
    
    
    /***********************************************
     * Saves the field into the database           *
     ***********************************************/
    function insert_field($dataid, $type='textarea', $name, $desc='', $width='', $height='') {
        $newfield = new object;
        $newfield->dataid = $dataid;
        $newfield->type = $type;
        $newfield->name = $name;
        $newfield->description = $desc;
        $newfield->param1 = $width;
        $newfield->param2 = $height;
        
        if (!insert_record('data_fields', $newfield)) {
            notify('Insertion of new field failed!');
        }
    }
    
    
    /***********************************************
     * Prints the form element in the add template *
     ***********************************************/
    function display_add_field($id, $rid=0) {
        global $CFG;
        if (!$field = get_record('data_fields', 'id', $id)){
            notify('That is not a valid field id!');
            exit;
        }
        if ($rid) {
            $dataContent = get_record('data_content', 'fieldid', $id, 'recordid', $rid);
            $content = $dataContent->content;
        }
        else {
            $content = '';
        }
        $str = '';
        /*
        if ($field->description) {
            $str .= '<img src="'.$CFG->pixpath.'/help.gif" alt="'.$field->description.'" title="'.$field->description.'" />&nbsp;';
        }*/
        $str .= '<div title="'.$field->description.'">';
        
        if (can_use_richtext_editor()) {
            // Show a rich text html editor.
            $str .= helpbutton("richtext", get_string("helprichtext"), "moodle", true, true, '', true);
            $str .= data_field_textarea::gen_textarea(true, 'field_' . $field->id, $field->param2, $field->param3, $content);
            $str .= '<input type="hidden" name="field_' . $field->id . '_content1' . '" value="' . FORMAT_HTML . '" />';
        }
        else {
            // Show a normal textarea. Also let the user specify the format to be used.
            $str .= data_field_textarea::gen_textarea(false, 'field_' . $field->id, $field->param2, $field->param3, $content);
            // Get the available text formats for this field.
            $formatsForField = format_text_menu();
            $str .= '<br />';
            
            if (empty($dataContent->content1)) {
                $str .= choose_from_menu($formatsForField, 'field_' . $field->id . '_content1', '', 'choose', '', '', true);
            }
            else {
                $str .= choose_from_menu($formatsForField, 'field_' . $field->id . '_content1', $dataContent->content1, 'choose', '', '', true);
            }
            $str .= helpbutton("textformat", get_string("helpformatting"), 'moodle', true, false, '', true);
        }
        $str .= '</div>';
        return $str;
    }
    
    
    function gen_textarea($usehtmleditor, $name, $cols=65, $rows=10, $value='') {
        global $CFG, $course;
        static $scriptcount; // For loading the htmlarea script only once.
        
        if (empty($courseid)) {
            if (!empty($course->id)) {  // Search for it in global context.
                $courseid = $course->id;
            }
        }
        if (empty($scriptcount)) {
            $scriptcount = 0;
        }
        
        $output = '';
        
        if ($usehtmleditor) {
            if (!empty($courseid) and isteacher($courseid)) {
                $output .= ($scriptcount < 1) ? '<script type="text/javascript" src="'. $CFG->wwwroot .'/lib/editor/htmlarea/htmlarea.php?id='. $courseid .'"></script>'."\n" : '';
            }
            else {
                $output .= ($scriptcount < 1) ? '<script type="text/javascript" src="'. $CFG->wwwroot .'/lib/editor/htmlarea/htmlarea.php"></script>'."\n" : '';
            }
            $output .= ($scriptcount < 1) ? '<script type="text/javascript" src="'. $CFG->wwwroot .'/lib/editor/htmlarea/lang/en.php"></script>'."\n" : '';
            $scriptcount++;
        }

        $output .= '<textarea id="' . $name .'" name="'. $name .'" rows="'. $rows .'" cols="'. $cols .'">';
        
        if ($usehtmleditor) {
            $output .= htmlspecialchars(stripslashes_safe($value)); // needed for editing of cleaned text!
        }
        else {
            $output .= $value;
        }
        $output .= '</textarea>'."\n";
        
        return $output;
    }
    
    
    function print_after_form() {
        if (can_use_richtext_editor()) {
            $this->use_html_editor('field_' . $this->id);
        }
    }
    
    
    /**
     * Sets up the HTML editor on textareas in the current page.
     * If a field name is provided, then it will only be
     * applied to that field - otherwise it will be used
     * on every textarea in the page.
     *
     * For Moodle 1.6, this is nearly exactly the same as
     * use_html_editor() in /lib/weblib.php. For Moodle 1.5, this
     * function is different to that in /lib/weblib.php. The
     * reason is because we need the database module to be
     * compatible with 1.5.
     *
     * @param string $name Form element to replace with HTMl editor by name
     */
    function use_html_editor($name='', $editorhidebuttons='') {
        echo '<script language="javascript" type="text/javascript" defer="defer">'."\n";
        echo "editor = new HTMLArea('$name');\n";

        echo $this->print_editor_config($editorhidebuttons);

        if (empty($name)) {
            echo "\n".'HTMLArea.replaceAll(editor.config);'."\n";
        } else {
            echo "\neditor.generate();\n";
        }
        echo '</script>'."\n";
    }
    
    
    /**
     * Versioning issue same as above.
     */
     function print_editor_config($editorhidebuttons='', $return=false) {
         global $CFG;

         $str = "var config = editor.config;\n";
         $str .= "config.pageStyle = \"body {";

         if (!(empty($CFG->editorbackgroundcolor))) {
             $str .= " background-color: $CFG->editorbackgroundcolor;";
         }

         if (!(empty($CFG->editorfontfamily))) {
             $str .= " font-family: $CFG->editorfontfamily;";
         }

         if (!(empty($CFG->editorfontsize))) {
             $str .= " font-size: $CFG->editorfontsize;";
         }

         $str .= " }\";\n";
         $str .= "config.killWordOnPaste = ";
         $str .= (empty($CFG->editorkillword)) ? "false":"true";
         $str .= ';'."\n";
         $str .= 'config.fontname = {'."\n";

         $fontlist = isset($CFG->editorfontlist) ? explode(';', $CFG->editorfontlist) : array();
         $i = 1;                     // Counter is used to get rid of the last comma.

         foreach ($fontlist as $fontline) {
             if (!empty($fontline)) {
                 if ($i > 1) {
                     $str .= ','."\n";
                 }
                 list($fontkey, $fontvalue) = split(':', $fontline);
                 $str .= '"'. $fontkey ."\":\t'". $fontvalue ."'";

                 $i++;
             }
         }
         $str .= '};';

         if (!empty($editorhidebuttons)) {
             $str .= "\nconfig.hideSomeButtons(\" ". $editorhidebuttons ." \");\n";
         } else if (!empty($CFG->editorhidebuttons)) {
             $str .= "\nconfig.hideSomeButtons(\" ". $CFG->editorhidebuttons ." \");\n";
         }

         if (!empty($CFG->editorspelling) && !empty($CFG->aspellpath)) {
             $str .= print_speller_code($usehtmleditor=true, true);
         }

         if ($return) {
             return $str;
         }
         echo $str;
     }
    
    

    function display_edit_field($id, $mode=0) {
        parent::display_edit_field($id, $mode);
    }
        

    function update($fieldobject) {
        $fieldobject->param1 = trim($fieldobject->param1);
        $fieldobject->param2 = trim($fieldobject->param2);
        
        if (!update_record('data_fields',$fieldobject)){
            notify ('upate failed');
        }
    }
    
    
    /************************************
     * store content of this field type *
     ************************************/
    function store_data_content($fieldid, $recordid, $value, $name=''){
        if ($value) {
            $content = new object;
            $content->fieldid = $fieldid;
            $content->recordid = $recordid;
            
            if ($oldcontent = get_record('data_content','fieldid', $fieldid, 'recordid', $recordid)) {
                // This belongs to an existing data_content.
                $content->id = $oldcontent->id;
                $nameParts = explode('_', $name);
                $column = $nameParts[count($nameParts) - 1];  // Format is field_<fieldid>_content[1 to 4]
                
                $content->$column = clean_param($value, PARAM_INT);
                update_record('data_content', $content);
            }
            else {
                // First (and maybe only) data content for this field for this record.
                $content->content = clean_param($value, PARAM_CLEANHTML);
                insert_record('data_content', $content);
            }
        }
    }
    
    
    /*************************************
     * update content of this field type *
     *************************************/
    function update_data_content($fieldid, $recordid, $value, $name=''){
        // If data_content already exists, we update.
        if ($oldcontent = get_record('data_content', 'fieldid', $fieldid, 'recordid', $recordid)){
            $content = new object;
            $content->fieldid = $fieldid;
            $content->recordid = $recordid;
            
            $nameParts = explode('_', $name);
            if (!empty($nameParts[2])) {
                $content->$nameParts[2] = clean_param($value, PARAM_NOTAGS);
            }
            else {
                $content->content = clean_param($value, PARAM_NOTAGS);
            }
            $content->id = $oldcontent->id;
            update_record('data_content', $content);
        }
        else {    //make 1 if there isn't one already
            $this->store_data_content($fieldid, $recordid, $value, $name='');
        }
    }
}
?>
