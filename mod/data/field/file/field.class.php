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

                file_prepare_draft_area($itemid, $this->context->id, 'data_content', $content->id);

                if (!empty($content->content)) {
                    if ($file = $fs->get_file($this->context->id, 'data_content', $content->id, '/', $content->content)) {
                        // move to draft


                        $fs = get_file_storage();
                        $usercontext = get_context_instance(CONTEXT_USER, $USER->id);
                        if (!$files = $fs->get_area_files($usercontext->id, 'user_draft', $itemid, 'id DESC', false)) {
                            return false;
                        }
                        $file = reset($files);
                        if (empty($content->content1)) {
                            // Print icon if file already exists
                            $browser = get_file_browser();
                            $src     = file_encode_url($CFG->wwwroot.'/draftfile.php/', $usercontext->id.'/user_draft/'.$itemid.'/'.$file->get_filename());
                            $displayname = '<img src="'.$OUTPUT->pix_url(file_mimetype_icon($file->get_mimetype())).'" class="icon" alt="'.$file->get_mimetype().'" />'. '<a href="'.$src.'" >'.s($file->get_filename()).'</a>';

                            $fs->delete_area_files($this->context->id, 'data_content', $content->id);
                        } else {
                            $displayname = 'no file added';
                        }
                    }
                }
            }
        } else {
            $itemid = file_get_unused_draft_itemid();
        }

        $draftareainfo = file_get_draft_area_info($itemid);
        $filecount = $draftareainfo['filecount'];

        // start to setup filemanager
        // loading filemanager language string
        $PAGE->requires->string_for_js('loading', 'repository');
        $PAGE->requires->string_for_js('nomorefiles', 'repository');
        $PAGE->requires->string_for_js('confirmdeletefile', 'repository');
        $PAGE->requires->string_for_js('add', 'repository');
        $PAGE->requires->string_for_js('accessiblefilepicker', 'repository');
        $PAGE->requires->string_for_js('move', 'moodle');
        $PAGE->requires->string_for_js('cancel', 'moodle');
        $PAGE->requires->string_for_js('download', 'moodle');
        $PAGE->requires->string_for_js('ok', 'moodle');
        $PAGE->requires->string_for_js('emptylist', 'repository');
        $PAGE->requires->string_for_js('entername', 'repository');
        $PAGE->requires->string_for_js('enternewname', 'repository');
        $PAGE->requires->string_for_js('zip', 'editor');
        $PAGE->requires->string_for_js('unzip', 'moodle');
        $PAGE->requires->string_for_js('rename', 'moodle');
        $PAGE->requires->string_for_js('delete', 'moodle');
        $PAGE->requires->string_for_js('setmainfile', 'resource');
        $PAGE->requires->string_for_js('cannotdeletefile', 'error');
        $PAGE->requires->string_for_js('confirmdeletefile', 'repository');
        $PAGE->requires->string_for_js('nopathselected', 'repository');
        $PAGE->requires->string_for_js('popupblockeddownload', 'repository');
        $PAGE->requires->string_for_js('path', 'moodle');

        // create a unique id for filemanager
        $client_id = uniqid();

        $str = '';
        // print out file entry template only one time
        if (empty($CFG->filemanagertemplateloaded)) {
            $CFG->filemanagertemplateloaded = true;
            $str .= <<<FMHTML
<div id="fm-template" style="display:none"><div class="fm-file-menu">___action___</div> <div class="fm-file-name">___fullname___</div></div>
FMHTML;
        }
        // database entry label
        $str .= '<div title="'.s($this->field->description).'">';
        $str .= '<fieldset><legend><span class="accesshide">'.$this->field->name.'</span></legend>';
        // itemid element
        $str .= '<input type="hidden" name="field_'.$this->field->id.'_file" value="'.$itemid.'" />';

        // language strings
        $straddfile  = get_string('add', 'repository') . '...';
        $strmakedir  = get_string('makeafolder', 'moodle');
        $strdownload  = get_string('downloadfolder', 'repository');

        $str .= <<<FMHTML
<div id="filemanager-wrapper-{$client_id}" style="display:none">
    <div class="fm-breadcrumb" id="fm-path-{$client_id}"></div>
    <div class="filemanager-toolbar">
        <button id="btnadd-{$client_id}" onclick="return false">{$straddfile}</button>
        <button id="btncrt-{$client_id}" onclick="return false">{$strmakedir}</button>
        <button id="btndwn-{$client_id}" onclick="return false">{$strdownload}</button>
    </div>
    <div class="filemanager-container" id="filemanager-{$client_id}">
        <ul id="draftfiles-{$client_id}">
            <li>Loading...</li>
        </ul>
    </div>
</div>
FMHTML;
        $editorurl = "$CFG->wwwroot/repository/filepicker.php?env=filemanager&amp;action=embedded&amp;itemid=$itemid&amp;subdirs=/&amp;maxbytes=$options->maxbytes&amp;ctx_id=".$PAGE->context->id.'&amp;course='.$PAGE->course->id;

        $str .= <<<NONJS
<div id="nonjs-filemanager-$client_id">
<object type="text/html" data="$editorurl" height="160" width="600" style="border:1px solid #000">Error</object>
</div>
NONJS;

        $str .= '</fieldset>';
        $str .= '</div>';

        // need these arguments to filter repositories list
        $args = new stdclass;
        $args->accepted_types = '*';
        $args->return_types = FILE_INTERNAL;
        $args->context = $PAGE->context;

        $filepicker_options = initialise_filepicker($args);

        $filepicker_options->client_id = $client_id;
        $filepicker_options->env      = 'filemanager';
        $filepicker_options->itemid   = $itemid;

        $options = file_get_draft_area_files($itemid);
        $options->client_id = $client_id;
        $options->filecount = $filecount;
        $options->itemid    = $itemid;
        $options->subdirs   = 0;
        $options->maxbytes = -1;
        $options->maxfiles = 1;
        // store filepicker options
        $options->filepicker = $filepicker_options;

        $module = array('name'=>'form_filemanager', 'fullpath'=>'/lib/form/filemanager.js', 'requires' => array('core_filepicker', 'base', 'io', 'node', 'json', 'yui2-button', 'yui2-container', 'yui2-layout', 'yui2-menu', 'yui2-treeview'));
        $PAGE->requires->js_module($module);
        $PAGE->requires->js_init_call('M.form_filemanager.init', array($options), true, $module);

        // destroy iframe, it is needed in non javascript only
        $str .= $PAGE->requires->js_function_call('destroy_item', array("nonjs-filepicker-{$client_id}"));
        // make filemanager buttons visible
        $str .= $PAGE->requires->js_function_call('show_item', array("filepicker-wrapper-{$client_id}"));

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
        global $CFG, $DB, $OUTPUT;

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
        global $CFG, $DB, $USER;

        if (!$content = $DB->get_record('data_content', array('fieldid'=>$this->field->id, 'recordid'=>$recordid))) {
        // Quickly make one now!
            $content = new object();
            $content->fieldid  = $this->field->id;
            $content->recordid = $recordid;
            $id = $DB->insert_record('data_content', $content);
            $content = $DB->get_record('data_content', array('id'=>$id));
        }

        $usercontext = get_context_instance(CONTEXT_USER, $USER->id);
        $fs = get_file_storage();
        $files = $fs->get_area_files($usercontext->id, 'user_draft', $value);

        if (count($files)<2) {
            // no file
        } else {
            $count = 0;
            foreach ($files as $draftfile) {
                $file_record = array('contextid'=>$this->context->id, 'itemid'=>$content->id, 'filepath'=>'/', 'filearea'=>'data_content');
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


