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
        global $CFG, $DB, $OUTPUT, $PAGE, $USER;

        $file        = false;
        $content     = false;
        $displayname = '';
        $fs = get_file_storage();
        $context = $PAGE->context;
        $itemid = null;

        // editing an existing database entry
        if ($recordid){
            if ($content = $DB->get_record('data_content', array('fieldid'=>$this->field->id, 'recordid'=>$recordid))) {

                file_prepare_draft_area($itemid, $this->context->id, 'mod_data', 'content', $content->id);

                if (!empty($content->content)) {
                    if ($file = $fs->get_file($this->context->id, 'mod_data', 'content', $content->id, '/', $content->content)) {
                        $usercontext = get_context_instance(CONTEXT_USER, $USER->id);
                        if (!$files = $fs->get_area_files($usercontext->id, 'user', 'draft', $itemid, 'id DESC', false)) {
                            return false;
                        }
                        if (empty($content->content1)) {
                            // Print icon if file already exists
                            $src = moodle_url::make_draftfile_url($itemid, '/', $file->get_filename());
                            $displayname = '<img src="'.$OUTPUT->pix_url(file_mimetype_icon($file->get_mimetype())).'" class="icon" alt="'.$file->get_mimetype().'" />'. '<a href="'.$src.'" >'.s($file->get_filename()).'</a>';

                        } else {
                            $displayname = 'no file added';
                        }
                    }
                }
            }
        } else {
            $itemid = file_get_unused_draft_itemid();
        }

        $html = '';
        // database entry label
        $html .= '<div title="'.s($this->field->description).'">';
        $html .= '<fieldset><legend><span class="accesshide">'.$this->field->name.'</span></legend>';

        // itemid element
        $html .= '<input type="hidden" name="field_'.$this->field->id.'_file" value="'.$itemid.'" />';

        $options = new stdClass();
        $options->maxbytes  = $this->field->param3;
        $options->itemid    = $itemid;
        $options->accepted_types = '*';
        $options->return_types = FILE_INTERNAL;
        $options->context = $PAGE->context;

        $fp = new file_picker($options);
        // print out file picker
        $html .= $OUTPUT->render($fp);

        $html .= '</fieldset>';
        $html .= '</div>';

        $module = array('name'=>'data_filepicker', 'fullpath'=>'/mod/data/data.js', 'requires'=>array('core_filepicker'));
        $PAGE->requires->js_init_call('M.data_filepicker.init', array($fp->options), true, $module);

        return $html;
    }

    function display_search_field($value = '') {
        return '<input type="text" size="16" name="f_'.$this->field->id.'" value="'.$value.'" />';
    }

    function generate_sql($tablealias, $value) {
        global $DB;

        static $i=0;
        $i++;
        $name = "df_file_$i";
        return array(" ({$tablealias}.fieldid = {$this->field->id} AND ".$DB->sql_like("{$tablealias}.content", ":$name", false).") ", array($name=>"%$value%"));
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
        if (!$file = $fs->get_file($this->context->id, 'mod_data', 'content', $content->id, '/', $content->content)) {
            return null;
        }

        return $file;
    }

    function display_browse_field($recordid, $template) {
        global $CFG, $DB, $OUTPUT;

        if (!$content = $DB->get_record('data_content', array('fieldid'=>$this->field->id, 'recordid'=>$recordid))) {
            return '';
        }

        if (empty($content->content)) {
            return '';
        }

        if (!$file = $this->get_file($recordid, $content)) {
            return '';
        }

        $name   = empty($content->content1) ? $file->get_filename() : $content->content1;
        $src    = file_encode_url($CFG->wwwroot.'/pluginfile.php', '/'.$this->context->id.'/mod_data/content/'.$content->id.'/'.$file->get_filename());
        $width  = $this->field->param1 ? ' width  = "'.s($this->field->param1).'" ':' ';
        $height = $this->field->param2 ? ' height = "'.s($this->field->param2).'" ':' ';

        $str = '<img src="'.$OUTPUT->pix_url(file_mimetype_icon($file->get_mimetype())).'" height="16" width="16" alt="'.$file->get_mimetype().'" />&nbsp;'.
               '<a href="'.$src.'" >'.s($name).'</a>';
        return $str;
    }


    // content: "a##b" where a is the file name, b is the display name
    function update_content($recordid, $value, $name) {
        global $CFG, $DB, $USER;
        $fs = get_file_storage();

        if (!$content = $DB->get_record('data_content', array('fieldid'=>$this->field->id, 'recordid'=>$recordid))) {

        // Quickly make one now!
            $content = new stdClass();
            $content->fieldid  = $this->field->id;
            $content->recordid = $recordid;
            $id = $DB->insert_record('data_content', $content);
            $content = $DB->get_record('data_content', array('id'=>$id));
        }

        // delete existing files
        $fs->delete_area_files($this->context->id, 'mod_data', 'content', $content->id);

        $usercontext = get_context_instance(CONTEXT_USER, $USER->id);
        $files = $fs->get_area_files($usercontext->id, 'user', 'draft', $value);

        if (count($files)<2) {
            // no file
        } else {
            $count = 0;
            foreach ($files as $draftfile) {
                $file_record = array('contextid'=>$this->context->id, 'component'=>'mod_data', 'filearea'=>'content', 'itemid'=>$content->id, 'filepath'=>'/');
                if (!$draftfile->is_directory()) {
                    $file_record['filename'] = $draftfile->get_filename();

                    $content->content = $draftfile->get_filename();

                    $fs->create_file_from_storedfile($file_record, $draftfile);
                    $DB->update_record('data_content', $content);

                    if ($count > 0) {
                        break;
                    } else {
                        $count++;
                    }
                }
            }
        }
    }

    function text_export_supported() {
        return false;
    }

    function file_ok($path) {
        return true;
    }

}


