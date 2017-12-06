<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @package dataformfield_textarea
 * @copyright 2016 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->dirroot.'/lib/filelib.php');
require_once($CFG->dirroot.'/repository/lib.php');

class dataformfield_textarea_textarea extends mod_dataform\pluginbase\dataformfield {
    /**
     * @return array List of the field file areas
     */
    public static function get_file_areas() {
        return array('contentdefault');
    }

    /**
     *
     */
    public function is_editor() {
        return $this->param1;
    }

    /**
     *
     */
    public function get_editoroptions() {
        $trust = $this->param4 ? $this->param4 : 0;
        $maxbytes = $this->param5 ? $this->param5 : 0;
        $maxfiles = $this->param6 ? $this->param6 : -1;

        $editoroptions = array();
        $editoroptions['context'] = $this->df->context;
        $editoroptions['trusttext'] = $trust;
        $editoroptions['maxbytes'] = $maxbytes;
        $editoroptions['maxfiles'] = $maxfiles;
        $editoroptions['subdirs'] = false;
        $editoroptions['changeformat'] = 0;
        $editoroptions['forcehttps'] = false;
        $editoroptions['noclean'] = false;

        return $editoroptions;
    }

    /**
     *
     */
    public function update_content($entry, array $values = null, $savenew = false) {
        global $DB;

        $entryid = $entry->id;
        $fieldid = $this->id;

        $contentid = isset($entry->{"c{$fieldid}_id"}) ? $entry->{"c{$fieldid}_id"} : null;

        // Delete if old content but not new.
        if ($contentid and empty($values)) {
            return $this->delete_content($entry->id);
        }

        $rec = new stdClass;
        $rec->fieldid = $fieldid;
        $rec->entryid = $entryid;

        if (!$rec->id = $contentid or $savenew) {
            $rec->id = $DB->insert_record('dataform_contents', $rec);
        }

        if ($this->is_editor()) {
            // Editor content.
            $data = (object) $values;
            $data->{'editor_editor'} = $data->editor;

            $data = file_postupdate_standard_editor(
                $data,
                'editor',
                $this->editoroptions,
                $this->df->context,
                'mod_dataform',
                'content',
                $rec->id
            );

            $rec->content = $data->editor;

        } else {
            // Text area content.
            $value = reset($values);
            if (is_array($value)) {
                // Import: One value as array of text,format,trust, so take the text.
                $value = reset($value);
            }
            $rec->content = clean_param($value, PARAM_NOTAGS);
        }

        return $DB->update_record('dataform_contents', $rec);
    }

    /**
     *
     */
    public function prepare_import_content($data, $importsettings, $csvrecord = null, $entryid = null) {
        $fieldid = $this->id;

        $data = parent::prepare_import_content($data, $importsettings, $csvrecord, $entryid);

        // There is only one import pattern for this field.
        $importsetting = reset($importsettings);

        if (isset($data->{"field_{$fieldid}_{$entryid}"})) {
            $contenttext = $data->{"field_{$fieldid}_{$entryid}"};

            // Replace new lines.
            if (!empty($importsetting['newline'])) {
                $contenttext = str_replace($importsetting['newline'], "\n", $contenttext);
            }

            $iseditor = $this->is_editor();

            // For editors reformat in editor structure.
            if ($iseditor) {
                $content = array();
                $content['text'] = $contenttext;
                $content['format'] = $this->text_format;
                $content['trust'] = $this->text_trusted;
                $data->{"field_{$fieldid}_{$entryid}_editor"} = $content;

                unset($data->{"field_{$fieldid}_{$entryid}"});
            } else {
               $data->{"field_{$fieldid}_{$entryid}"} = $contenttext;
            }
        }

        return $data;
    }

    /**
     *
     */
    public function content_names() {
        if ($this->is_editor()) {
            return array('editor');
        } else {
            return array('');
        }
    }

    /**
     *
     */
    public function get_text_trusted() {
        $value = $this->param4 ? $this->param4 : 0;
        return $value;
    }

    /**
     *
     */
    public function get_text_format() {
        $value = (int) $this->param7;
        return $value;
    }
}
