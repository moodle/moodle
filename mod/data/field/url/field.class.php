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

class data_field_url extends data_field_base {
    var $type = 'url';
    /**
     * priority for globalsearch indexing
     *
     * @var int
     */
    protected static $priority = self::MIN_PRIORITY;

    public function supports_preview(): bool {
        return true;
    }

    public function get_data_content_preview(int $recordid): stdClass {
        return (object)[
            'id' => 0,
            'fieldid' => $this->field->id,
            'recordid' => $recordid,
            'content' => 'https://example.com',
            'content1' => null,
            'content2' => null,
            'content3' => null,
            'content4' => null,
        ];
    }

    function display_add_field($recordid = 0, $formdata = null) {
        global $CFG, $DB, $OUTPUT, $PAGE;

        require_once($CFG->dirroot. '/repository/lib.php'); // necessary for the constants used in args

        $args = new stdClass();
        $args->accepted_types = '*';
        $args->return_types = FILE_EXTERNAL;
        $args->context = $this->context;
        $args->env = 'url';
        $fp = new file_picker($args);
        $options = $fp->options;

        $fieldid = 'field_url_'.$options->client_id;

        $straddlink = get_string('choosealink', 'repository');
        $url = '';
        $text = '';
        if ($formdata) {
            $fieldname = 'field_' . $this->field->id . '_0';
            $url = $formdata->$fieldname;
            $fieldname = 'field_' . $this->field->id . '_1';
            if (isset($formdata->$fieldname)) {
                $text = $formdata->$fieldname;
            }
        } else if ($recordid) {
            if ($content = $DB->get_record('data_content', array('fieldid'=>$this->field->id, 'recordid'=>$recordid))) {
                $url  = $content->content;
                $text = $content->content1;
            }
        }

        $autolinkable = !empty($this->field->param1) && empty($this->field->param2);

        $str = '<div title="' . s($this->field->description) . '" class="d-flex flex-wrap align-items-center">';

        $label = '<label for="' . $fieldid . '"><span class="accesshide">' . $this->field->name . '</span>';
        if ($this->field->required) {
            $image = $OUTPUT->pix_icon('req', get_string('requiredelement', 'form'));
            if ($autolinkable) {
                $label .= html_writer::div(get_string('requiredelement', 'form'), 'accesshide');
            } else {
                $label .= html_writer::div($image, 'inline-req');
            }
        }
        $label .= '</label>';

        if ($autolinkable) {
            $str .= '<table><tr><td align="right">';
            $str .= '<span class="mod-data-input">' . get_string('url', 'data') . ':</span>';
            if (!empty($image)) {
                $str .= $image;
            }
            $str .= '</td><td>';
            $str .= $label;
            $str .= '<input type="text" name="field_' . $this->field->id . '_0" id="' . $fieldid . '" value="' . s($url) . '" ' .
                    'size="40" class="form-control d-inline"/>';
            $str .= '<button class="btn btn-secondary ml-1" id="filepicker-button-' . $options->client_id . '" ' .
                    'style="display:none">' . $straddlink . '</button></td></tr>';
            $str .= '<tr><td align="right"><span class="mod-data-input">' . get_string('text', 'data') . ':</span></td><td>';
            $str .= '<input type="text" name="field_' . $this->field->id . '_1" id="field_' . $this->field->id . '_1" ' .
                    'value="' . s($text) . '" size="40" class="form-control d-inline"/></td></tr>';
            $str .= '</table>';
        } else {
            // Just the URL field
            $str .= $label;
            $str .= '<input type="text" name="field_'.$this->field->id.'_0" id="'.$fieldid.'" value="'.s($url).'"';
            $str .= ' size="40" class="mod-data-input form-control d-inline" />';
            if (count($options->repositories) > 0) {
                $str .= '<button id="filepicker-button-' . $options->client_id . '" class="visibleifjs btn btn-secondary ml-1">' .
                        $straddlink . '</button>';
            }
        }

        // print out file picker
        //$str .= $OUTPUT->render($fp);

        $module = array('name'=>'data_urlpicker', 'fullpath'=>'/mod/data/data.js', 'requires'=>array('core_filepicker'));
        $PAGE->requires->js_init_call('M.data_urlpicker.init', array($options), true, $module);
        $str .= '</div>';
        return $str;
    }

    function display_search_field($value = '') {
        return '<label class="accesshide" for="f_' . $this->field->id . '">' . get_string('fieldname', 'data') . '</label>' .
               '<input type="text" size="16" id="f_' . $this->field->id . '" '.
               ' name="f_' . $this->field->id . '" value="' . s($value) . '" class="form-control d-inline"/>';
    }

    public function parse_search_field($defaults = null) {
        $param = 'f_'.$this->field->id;
        if (empty($defaults[$param])) {
            $defaults = array($param => '');
        }
        return optional_param($param, $defaults[$param], PARAM_NOTAGS);
    }

    function generate_sql($tablealias, $value) {
        global $DB;

        static $i=0;
        $i++;
        $name = "df_url_$i";
        return array(" ({$tablealias}.fieldid = {$this->field->id} AND ".$DB->sql_like("{$tablealias}.content", ":$name", false).") ", array($name=>"%$value%"));
    }

    function display_browse_field($recordid, $template) {

        $content = $this->get_data_content($recordid);
        if (!$content) {
            return '';
        }

        $url = empty($content->content) ? '' : $content->content;
        $text = empty($content->content1) ? '' : $content->content1;
        if (empty($url) || ($url == 'http://')) {
            return '';
        }
        if (!empty($this->field->param2)) {
            // Param2 forces the text to something.
            $text = $this->field->param2;
        }
        if ($this->field->param1) {
            // Param1 defines whether we want to autolink the url.
            $attributes = ['class' => 'data-field-link'];
            if ($this->field->param3) {
                // Param3 defines whether this URL should open in a new window.
                $attributes['target'] = '_blank';
                $attributes['rel'] = 'noreferrer';
            }

            if (empty($text)) {
                $text = $url;
            }

            $str = html_writer::link($url, $text, $attributes);
        } else {
            $str = $url;
        }
        return $str;
    }

    function update_content_import($recordid, $value, $name='') {
        $values = explode(" ", $value, 2);

        foreach ($values as $index => $value) {
            $this->update_content($recordid, $value, $name . '_' . $index);
        }
    }

    function update_content($recordid, $value, $name='') {
        global $DB;

        $content = new stdClass();
        $content->fieldid = $this->field->id;
        $content->recordid = $recordid;
        $names = explode('_', $name);

        switch ($names[2]) {
            case 0:
                // update link
                $content->content = clean_param($value, PARAM_URL);
                break;
            case 1:
                // add text
                $content->content1 = clean_param($value, PARAM_NOTAGS);
                break;
            default:
                break;
        }

        if (!empty($content->content) && (strpos($content->content, '://') === false)
                && (strpos($content->content, '/') !== 0)) {
            $content->content = 'http://' . $content->content;
        }

        if ($oldcontent = $DB->get_record('data_content', array('fieldid'=>$this->field->id, 'recordid'=>$recordid))) {
            $content->id = $oldcontent->id;
            return $DB->update_record('data_content', $content);
        } else {
            return $DB->insert_record('data_content', $content);
        }
    }

    function notemptyfield($value, $name) {
        $names = explode('_',$name);
        $value = clean_param($value, PARAM_URL);
        //clean first
        if ($names[2] == '0') {
            return ($value!='http://' && !empty($value));
        }
        return false;
    }

    function export_text_value($record) {
        return $record->content . " " . $record->content1;
    }

    /**
     * Return the plugin configs for external functions.
     *
     * @return array the list of config parameters
     * @since Moodle 3.3
     */
    public function get_config_for_external() {
        // Return all the config parameters.
        $configs = [];
        for ($i = 1; $i <= 10; $i++) {
            $configs["param$i"] = $this->field->{"param$i"};
        }
        return $configs;
    }
}
