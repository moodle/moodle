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
// (at your option) any later version.                                   // //                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

require_once($CFG->dirroot.'/lib/filelib.php');
require_once($CFG->dirroot.'/repository/lib.php');

class data_field_textarea extends data_field_base {

    var $type = 'textarea';

    /**
     * Returns options for embedded files
     *
     * @return array
     */
    private function get_options() {
        if (!isset($this->field->param5)) {
            $this->field->param5 = 0;
        }
        $options = array();
        $options['trusttext'] = false;
        $options['forcehttps'] = false;
        $options['subdirs'] = false;
        $options['maxfiles'] = -1;
        $options['context'] = $this->context;
        $options['maxbytes'] = $this->field->param5;
        $options['changeformat'] = 0;
        $options['noclean'] = false;
        return $options;
    }

    function display_add_field($recordid=0) {
        global $CFG, $DB, $OUTPUT, $PAGE;

        $text   = '';
        $format = 0;

        $str = '<div title="'.$this->field->description.'">';

        editors_head_setup();
        $options = $this->get_options();

        $itemid = $this->field->id;
        $field = 'field_'.$itemid;

        if ($recordid && $content = $DB->get_record('data_content', array('fieldid'=>$this->field->id, 'recordid'=>$recordid))){
            $format = $content->content1;
            $text = clean_text($content->content, $format);
            $text = file_prepare_draft_area($draftitemid, $this->context->id, 'mod_data', 'content', $content->id, $options, $text);
        } else {
            $draftitemid = file_get_unused_draft_itemid();
            if (can_use_html_editor()) {
                $format = FORMAT_HTML;
            } else {
                $format = FORMAT_PLAIN;
            }
        }

        // get filepicker info
        //
        $fpoptions = array();
        if ($options['maxfiles'] != 0 ) {
            $args = new stdClass();
            // need these three to filter repositories list
            $args->accepted_types = array('web_image');
            $args->return_types = (FILE_INTERNAL | FILE_EXTERNAL);
            $args->context = $this->context;
            $args->env = 'filepicker';
            // advimage plugin
            $image_options = initialise_filepicker($args);
            $image_options->context = $this->context;
            $image_options->client_id = uniqid();
            $image_options->maxbytes = $options['maxbytes'];
            $image_options->env = 'editor';
            $image_options->itemid = $draftitemid;

            // moodlemedia plugin
            $args->accepted_types = array('video', 'audio');
            $media_options = initialise_filepicker($args);
            $media_options->context = $this->context;
            $media_options->client_id = uniqid();
            $media_options->maxbytes  = $options['maxbytes'];
            $media_options->env = 'editor';
            $media_options->itemid = $draftitemid;

            // advlink plugin
            $args->accepted_types = '*';
            $link_options = initialise_filepicker($args);
            $link_options->context = $this->context;
            $link_options->client_id = uniqid();
            $link_options->maxbytes  = $options['maxbytes'];
            $link_options->env = 'editor';
            $link_options->itemid = $draftitemid;

            $fpoptions['image'] = $image_options;
            $fpoptions['media'] = $media_options;
            $fpoptions['link'] = $link_options;
        }

        $editor = editors_get_preferred_editor($format);
        $strformats = format_text_menu();
        $formats =  $editor->get_supported_formats();
        foreach ($formats as $fid) {
            $formats[$fid] = $strformats[$fid];
        }
        $editor->use_editor($field, $options, $fpoptions);
        $str .= '<input type="hidden" name="'.$field.'_itemid" value="'.$draftitemid.'" />';
        $str .= '<div><textarea id="'.$field.'" name="'.$field.'" rows="'.$this->field->param3.'" cols="'.$this->field->param2.'">'.s($text).'</textarea></div>';
        $str .= '<div><label class="accesshide" for="' . $field . '_content1">' . get_string('format') . '</label>';
        $str .= '<select id="' . $field . '_content1" name="'.$field.'_content1">';
        foreach ($formats as $key=>$desc) {
            $selected = ($format == $key) ? 'selected="selected"' : '';
            $str .= '<option value="'.s($key).'" '.$selected.'>'.$desc.'</option>';
        }
        $str .= '</select>';
        $str .= '</div>';

        $str .= '</div>';
        return $str;
    }


    function display_search_field($value = '') {
        return '<label class="accesshide" for="f_' . $this->field->id . '">' . $this->field->name . '</label>' .
               '<input type="text" size="16" id="f_'.$this->field->id.'" name="f_'.$this->field->id.'" value="'.$value.'" />';
    }

    function parse_search_field() {
        return optional_param('f_'.$this->field->id, '', PARAM_NOTAGS);
    }

    function generate_sql($tablealias, $value) {
        global $DB;

        static $i=0;
        $i++;
        $name = "df_textarea_$i";
        return array(" ({$tablealias}.fieldid = {$this->field->id} AND ".$DB->sql_like("{$tablealias}.content", ":$name", false).") ", array($name=>"%$value%"));
    }

    function print_after_form() {
    }


    function update_content($recordid, $value, $name='') {
        global $DB;

        $content = new stdClass();
        $content->fieldid = $this->field->id;
        $content->recordid = $recordid;

        $names = explode('_', $name);
        if (!empty($names[2])) {
            if ($names[2] == 'itemid') {
                // the value will be retrieved by file_get_submitted_draft_itemid, do not need to save in DB
                return true;
            } else {
                $content->$names[2] = clean_param($value, PARAM_NOTAGS);  // content[1-4]
            }
        } else {
            $content->content = clean_param($value, PARAM_CLEAN);
        }

        if ($oldcontent = $DB->get_record('data_content', array('fieldid'=>$this->field->id, 'recordid'=>$recordid))) {
            $content->id = $oldcontent->id;
        } else {
            $content->id = $DB->insert_record('data_content', $content);
            if (!$content->id) {
                return false;
            }
        }
        if (!empty($content->content)) {
            $draftitemid = file_get_submitted_draft_itemid('field_'. $this->field->id. '_itemid');
            $options = $this->get_options();
            $content->content = file_save_draft_area_files($draftitemid, $this->context->id, 'mod_data', 'content', $content->id, $options, $content->content);
        }
        $rv = $DB->update_record('data_content', $content);
        return $rv;
    }

    /**
     * Display the content of the field in browse mode
     *
     * @param int $recordid
     * @param object $template
     * @return bool|string
     */
    function display_browse_field($recordid, $template) {
        global $DB;

        if ($content = $DB->get_record('data_content', array('fieldid' => $this->field->id, 'recordid' => $recordid))) {
            if (isset($content->content)) {
                $options = new stdClass();
                if ($this->field->param1 == '1') {  // We are autolinking this field, so disable linking within us
                    $options->filter = false;
                }
                $options->para = false;
                $str = file_rewrite_pluginfile_urls($content->content, 'pluginfile.php', $this->context->id, 'mod_data', 'content', $content->id, $this->get_options());
                $str = format_text($str, $content->content1, $options);
            } else {
                $str = '';
            }
            return $str;
        }
        return false;
    }

    /**
     * Whether this module support files
     *
     * @param string $relativepath
     * @return bool
     */
    function file_ok($relativepath) {
        return true;
    }
}

