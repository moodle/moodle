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

class data_field_picture extends data_field_base {
    var $type = 'picture';
    var $previewwidth  = 50;
    var $previewheight = 50;

    function display_add_field($recordid=0) {
        global $CFG, $DB, $OUTPUT, $USER, $PAGE;

        $file        = false;
        $content     = false;
        $displayname = '';
        $alttext     = '';
        $itemid = null;
        $fs = get_file_storage();

        if ($recordid) {
            if ($content = $DB->get_record('data_content', array('fieldid'=>$this->field->id, 'recordid'=>$recordid))) {
                file_prepare_draft_area($itemid, $this->context->id, 'mod_data', 'content', $content->id);
                if (!empty($content->content)) {
                    if ($file = $fs->get_file($this->context->id, 'mod_data', 'content', $content->id, '/', $content->content)) {
                        $usercontext = context_user::instance($USER->id);
                        if (!$files = $fs->get_area_files($usercontext->id, 'user', 'draft', $itemid, 'id DESC', false)) {
                            return false;
                        }
                        if ($thumbfile = $fs->get_file($usercontext->id, 'user', 'draft', $itemid, '/', 'thumb_'.$content->content)) {
                            $thumbfile->delete();
                        }
                        if (empty($content->content1)) {
                            // Print icon if file already exists
                            $src = moodle_url::make_draftfile_url($itemid, '/', $file->get_filename());
                            $displayname = $OUTPUT->pix_icon(file_file_icon($file), get_mimetype_description($file), 'moodle', array('class' => 'icon')). '<a href="'.$src.'" >'.s($file->get_filename()).'</a>';

                        } else {
                            $displayname = get_string('nofilesattached', 'repository');
                        }
                    }
                }
                $alttext = $content->content1;
            }
        } else {
            $itemid = file_get_unused_draft_itemid();
        }

        $str = '<div title="'.s($this->field->description).'">';
        $str .= '<fieldset><legend><span class="accesshide">'.$this->field->name.'</span></legend>';
        $str .= '<noscript>';
        if ($file) {
            $src = file_encode_url($CFG->wwwroot.'/pluginfile.php/', $this->context->id.'/mod_data/content/'.$content->id.'/'.$file->get_filename());
            $str .= '<img width="'.s($this->previewwidth).'" height="'.s($this->previewheight).'" src="'.$src.'" alt="" />';
        }
        $str .= '</noscript>';

        $options = new stdClass();
        $options->maxbytes  = $this->field->param3;
        $options->maxfiles  = 1; // Only one picture permitted.
        $options->itemid    = $itemid;
        $options->accepted_types = array('web_image');
        $options->return_types = FILE_INTERNAL;
        $options->context = $PAGE->context;
        if (!empty($file)) {
            $options->filename = $file->get_filename();
            $options->filepath = '/';
        }

        $fm = new form_filemanager($options);
        // Print out file manager.

        $output = $PAGE->get_renderer('core', 'files');
        $str .= $output->render($fm);

        $str .= '<div class="mdl-left">';
        $str .= '<input type="hidden" name="field_'.$this->field->id.'_file" value="'.$itemid.'" />';
        $str .= '<label for="field_'.$this->field->id.'_alttext">'.get_string('alttext','data') .'</label>&nbsp;<input type="text" name="field_'
                .$this->field->id.'_alttext" id="field_'.$this->field->id.'_alttext" value="'.s($alttext).'" />';
        $str .= '</div>';

        $str .= '</fieldset>';
        $str .= '</div>';

        $module = array(
            'name'=>'form_filemanager',
            'fullpath'=>'/lib/form/filemanager.js',
            'requires' => array('core_filepicker', 'base', 'io-base', 'node',
                    'json', 'core_dndupload', 'panel', 'resize-plugin', 'dd-plugin'),
            'strings' => array(
                array('error', 'moodle'), array('info', 'moodle'), array('confirmdeletefile', 'repository'),
                array('draftareanofiles', 'repository'), array('entername', 'repository'), array('enternewname', 'repository'),
                array('invalidjson', 'repository'), array('popupblockeddownload', 'repository'),
                array('unknownoriginal', 'repository'), array('confirmdeletefolder', 'repository'),
                array('confirmdeletefilewithhref', 'repository'), array('confirmrenamefolder', 'repository'),
                array('confirmrenamefile', 'repository'), array('edit', 'moodle')
            )
        );

        $PAGE->requires->js_init_call('M.form_filemanager.init', array($fm->options), true, $module);

        return $str;
    }

    // TODO delete this function and instead subclass data_field_file - see MDL-16493

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

    function display_search_field($value = '') {
        return '<label class="accesshide" for="f_'.$this->field->id.'">' . get_string('fieldname', 'data') . '</label>' .
               '<input type="text" size="16" id="f_'.$this->field->id.'" name="f_'.$this->field->id.'" value="'.$value.'" />';
    }

    function parse_search_field() {
        return optional_param('f_'.$this->field->id, '', PARAM_NOTAGS);
    }

    function generate_sql($tablealias, $value) {
        global $DB;

        static $i=0;
        $i++;
        $name = "df_picture_$i";
        return array(" ({$tablealias}.fieldid = {$this->field->id} AND ".$DB->sql_like("{$tablealias}.content", ":$name", false).") ", array($name=>"%$value%"));
    }

    function display_browse_field($recordid, $template) {
        global $CFG, $DB;

        if (!$content = $DB->get_record('data_content', array('fieldid'=>$this->field->id, 'recordid'=>$recordid))) {
            return false;
        }

        if (empty($content->content)) {
            return '';
        }

        $alt   = $content->content1;
        $title = $alt;

        if ($template == 'listtemplate') {
            $src = file_encode_url($CFG->wwwroot.'/pluginfile.php', '/'.$this->context->id.'/mod_data/content/'.$content->id.'/'.'thumb_'.$content->content);
            // no need to add width/height, because the thumb is resized properly
            $str = '<a href="view.php?d='.$this->field->dataid.'&amp;rid='.$recordid.'"><img src="'.$src.'" alt="'.s($alt).'" title="'.s($title).'" class="list_picture"/></a>';

        } else {
            $src = file_encode_url($CFG->wwwroot.'/pluginfile.php', '/'.$this->context->id.'/mod_data/content/'.$content->id.'/'.$content->content);
            $width  = $this->field->param1 ? ' width="'.s($this->field->param1).'" ':' ';
            $height = $this->field->param2 ? ' height="'.s($this->field->param2).'" ':' ';
            $str = '<a href="'.$src.'"><img '.$width.$height.' src="'.$src.'" alt="'.s($alt).'" title="'.s($title).'" class="list_picture"/></a>';
        }

        return $str;
    }

    function update_field() {
        global $DB, $OUTPUT;

        // Get the old field data so that we can check whether the thumbnail dimensions have changed
        $oldfield = $DB->get_record('data_fields', array('id'=>$this->field->id));
        $DB->update_record('data_fields', $this->field);

        // Have the thumbnail dimensions changed?
        if ($oldfield && ($oldfield->param4 != $this->field->param4 || $oldfield->param5 != $this->field->param5)) {
            // Check through all existing records and update the thumbnail
            if ($contents = $DB->get_records('data_content', array('fieldid'=>$this->field->id))) {
                $fs = get_file_storage();
                if (count($contents) > 20) {
                    echo $OUTPUT->notification(get_string('resizingimages', 'data'), 'notifysuccess');
                    echo "\n\n";
                    // To make sure that ob_flush() has the desired effect
                    ob_flush();
                }
                foreach ($contents as $content) {
                    if (!$file = $fs->get_file($this->context->id, 'mod_data', 'content', $content->id, '/', $content->content)) {
                        continue;
                    }
                    if ($thumbfile = $fs->get_file($this->context->id, 'mod_data', 'content', $content->id, '/', 'thumb_'.$content->content)) {
                        $thumbfile->delete();
                    }
                    @set_time_limit(300);
                    // Might be slow!
                    $this->update_thumbnail($content, $file);
                }
            }
        }
        return true;
    }

    function update_content($recordid, $value, $name='') {
        global $CFG, $DB, $USER;

        if (!$content = $DB->get_record('data_content', array('fieldid'=>$this->field->id, 'recordid'=>$recordid))) {
        // Quickly make one now!
            $content = new stdClass();
            $content->fieldid  = $this->field->id;
            $content->recordid = $recordid;
            $id = $DB->insert_record('data_content', $content);
            $content = $DB->get_record('data_content', array('id'=>$id));
        }

        $names = explode('_', $name);
        switch ($names[2]) {
            case 'file':
                $fs = get_file_storage();
                $fs->delete_area_files($this->context->id, 'mod_data', 'content', $content->id);
                $usercontext = context_user::instance($USER->id);
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

                            $file = $fs->create_file_from_storedfile($file_record, $draftfile);
                            $DB->update_record('data_content', $content);
                            $this->update_thumbnail($content, $file);

                            if ($count > 0) {
                                break;
                            } else {
                                $count++;
                            }
                        }
                    }
                }

                break;

            case 'alttext':
                // only changing alt tag
                $content->content1 = clean_param($value, PARAM_NOTAGS);
                $DB->update_record('data_content', $content);
                break;

            default:
                break;
        }
    }

    function update_thumbnail($content, $file) {
        // (Re)generate thumbnail image according to the dimensions specified in the field settings.
        // If thumbnail width and height are BOTH not specified then no thumbnail is generated, and
        // additionally an attempted delete of the existing thumbnail takes place.
        $fs = get_file_storage();
        $file_record = array('contextid'=>$file->get_contextid(), 'component'=>$file->get_component(), 'filearea'=>$file->get_filearea(),
                             'itemid'=>$file->get_itemid(), 'filepath'=>$file->get_filepath(),
                             'filename'=>'thumb_'.$file->get_filename(), 'userid'=>$file->get_userid());
        try {
            // this may fail for various reasons
            $fs->convert_image($file_record, $file, $this->field->param4, $this->field->param5, true);
            return true;
        } catch (Exception $e) {
            debugging($e->getMessage());
            return false;
        }
    }

    function text_export_supported() {
        return false;
    }

    function file_ok($path) {
        return true;
    }
}


