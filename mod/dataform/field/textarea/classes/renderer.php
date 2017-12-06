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
defined('MOODLE_INTERNAL') or die();

/**
 *
 */
class dataformfield_textarea_renderer extends mod_dataform\pluginbase\dataformfieldrenderer {

    /**
     *
     */
    protected function replacements(array $patterns, $entry, array $options = null) {
        $field = $this->_field;
        $fieldname = $field->name;

        $edit = !empty($options['edit']);
        $haseditreplacement = false;
        $editablepatterns = array(
            "[[$fieldname]]",
            "[[$fieldname:text]]",
            "[[$fieldname:textlinks]]",
        );

        $replacements = array_fill_keys(array_keys($patterns), '');

        foreach ($patterns as $pattern => $cleanpattern) {
            if ($edit and !$haseditreplacement) {
                $patterneditable = in_array($cleanpattern, $editablepatterns);
                if ($patterneditable and !$noedit = $this->is_noedit($pattern)) {
                    $required = $this->is_required($pattern);
                    $editparams = array($entry, array('required' => $required));
                    $replacements[$pattern] = array(array($this, 'display_edit'), $editparams);
                    $haseditreplacement = true;
                    continue;
                }
            }

            switch ($cleanpattern) {
                case "[[$fieldname]]":
                    $replacements[$pattern] = $this->display_browse($entry);
                    break;

                // Plain text, no links.
                case "[[$fieldname:text]]":
                    $replacements[$pattern] = html_to_text($this->display_browse($entry, array('text' => true)));
                    break;

                // Plain text, with links.
                case "[[$fieldname:textlinks]]":
                    $replacements[$pattern] = $this->display_browse($entry, array('text' => true, 'links' => true));
                    break;

                case "[[{$fieldname}:wordcount]]":
                    $replacements[$pattern] = $this->word_count($entry);
                    break;
            }
        }

        return $replacements;
    }

    /**
     *
     */
    public function validate_data($entryid, $patterns, $data) {
        $field = $this->_field;
        $fieldid = $field->id;
        $fieldname = $field->name;

        $patterns = $this->add_clean_pattern_keys($patterns);
        $editablepatterns = array(
            "[[$fieldname]]",
            "[[$fieldname:text]]",
            "[[$fieldname:textlinks]]"
        );

        if (!$field->is_editor()) {
            $formfieldname = "field_{$fieldid}_{$entryid}";
            $cleanformat = PARAM_NOTAGS;
        } else {
            $formfieldname = "field_{$fieldid}_{$entryid}_editor";
            $cleanformat = PARAM_CLEANHTML;
        }

        foreach ($editablepatterns as $cleanpattern) {
            $pattern = array_search($cleanpattern, $patterns);
            if ($pattern !== false and $this->is_required($pattern)) {
                if (empty($data->$formfieldname)) {
                    return array($formfieldname, get_string('fieldrequired', 'dataform'));
                }
                if (!$field->is_editor()) {
                    if (!$content = clean_param($data->$formfieldname, $cleanformat)) {
                        return array($formfieldname, get_string('fieldrequired', 'dataform'));
                    }
                } else {
                    $editorobj = $data->$formfieldname;
                    if (!$content = clean_param($editorobj['text'], $cleanformat)) {
                        return array($formfieldname, get_string('fieldrequired', 'dataform'));
                    }
                }
            }
        }
        return null;
    }

    /**
     *
     */
    public function display_edit(&$mform, $entry, array $options = null) {
        global $PAGE, $CFG;

        $field = $this->_field;
        $fieldid = $field->id;
        $entryid = $entry->id;
        $fieldname = "field_{$fieldid}_{$entryid}";

        $attr = array();
        $styles = array();
        // Width.
        if ($field->param2) {
            $sizeandunit = array_merge(explode(' ', $field->param2), array('cols'));
            list($size, $unit) = $sizeandunit;
            if ($unit == 'cols') {
                $attr['cols'] = $size;
            } else {
                $styles[] = "width: $size$unit;";
            }
        }
        // Height.
        if ($field->param3) {
            $sizeandunit = array_merge(explode(' ', $field->param3), array('rows'));
            list($size, $unit) = $sizeandunit;
            if ($unit == 'rows') {
                $attr['rows'] = $size;
            } else {
                $styles[] = "height: $size$unit;";
            }
        }
        if ($styles) {
            $attr['style'] = implode('', $styles);
        }

        // Content.
        $defaultcontent = $field->defaultcontent;
        $entrycontent = isset($entry->{"c{$fieldid}_content"}) ? $entry->{"c{$fieldid}_content"} : '';

        $required = !empty($options['required']);

        if (!$field->is_editor()) {
            // TEXTAREA.
            $mform->addElement('textarea', $fieldname, null, $attr);
            $content = $entrycontent ? $entrycontent : $defaultcontent;
            $mform->setDefault($fieldname, $content);
            if ($required) {
                $mform->addRule($fieldname, null, 'required', null, 'client');
            }
        } else {
            // EDITOR.
            $component = 'mod_dataform';
            $contentid = isset($entry->{"c{$fieldid}_id"}) ? $entry->{"c{$fieldid}_id"} : null;

            $data = new \stdClass;
            $data->$fieldname = $entrycontent ? $entrycontent : $defaultcontent;
            $data->{"{$fieldname}format"} = $field->text_format;

            // If we are using default content, adjust component and content id.
            if ($defaultcontent and !$contentid) {
                $component = 'dataformfield_textarea';
                $contentid = $field->id;
            }

            $data = file_prepare_standard_editor(
                $data,
                $fieldname,
                $field->editoroptions,
                $field->df->context,
                $component,
                'content',
                $contentid
            );

            $editoroptions = $field->get_editoroptions() + array('collapsed' => true);
            $mform->addElement('editor', "{$fieldname}_editor", null, $attr, $editoroptions);
            $mform->setDefault("{$fieldname}_editor", $data->{"{$fieldname}_editor"});
            $mform->setDefault("{$fieldname}[text]", $data->$fieldname);
            $mform->setDefault("{$fieldname}[format]", $data->{"{$fieldname}format"});
            if ($required) {
                $mform->addRule("{$fieldname}_editor", null, 'required', null, 'client');
            }
        }
    }

    /**
     *
     */
    public function word_count($entry) {

        $fieldid = $this->_field->id;

        if (isset($entry->{"c{$fieldid}_content"})) {
            $text = $entry->{"c{$fieldid}_content"};
            return str_word_count(strip_tags($text));
        } else {
            return '';
        }
    }

    /**
     * Print the content for browsing the entry
     */
    public function display_browse($entry, $params = null) {

        $field = $this->_field;
        $fieldid = $field->id;
        $df = $field->df;

        if (isset($entry->{"c{$fieldid}_content"})) {
            $contentid = $entry->{"c{$fieldid}_id"};
            $text = $entry->{"c{$fieldid}_content"};
            $format = $field->text_format;
            $contextid = $df->context->id;
            $contentidhash = $df->get_content_id_hash($contentid);

            $text = file_rewrite_pluginfile_urls(
                $text,
                'pluginfile.php',
                $contextid,
                'mod_dataform',
                'content',
                $contentidhash
            );

            $options = new \stdClass;
            $options->para = false;
            $str = format_text($text, $format, $options);
            return $str;
        } else {
            return '';
        }
    }

    /**
     * Overriding {@link dataformfieldrenderer::get_pattern_import_settings()}
     * to allow only the base pattern and add setting for new line conversion.
     */
    public function get_pattern_import_settings(&$mform, $patternname, $header) {
        $field = $this->_field;
        $fieldid = $field->id;
        $fieldname = $field->name;

        // Only [[fieldname]] can be imported.
        if ($patternname != $fieldname) {
            return array(array(), array());
        }

        $name = "f_{$fieldid}_";

        list($grp, $labels) = parent::get_pattern_import_settings($mform, $patternname, $header);

        $grp[] = &$mform->createElement('text', "{$name}_newline", null);
        $mform->setType("{$name}_newline", PARAM_TEXT);
        $labels = array_merge($labels, array(' '. get_string('newline', 'dataformfield_textarea'). ': '));

        return array($grp, $labels);
    }

    /**
     *
     */
    public function pluginfile_patterns() {
        return array("[[{$this->_field->name}]]");
    }

    /**
     * Array of patterns this field supports.
     */
    protected function patterns() {
        $fieldname = $this->_field->name;

        $patterns = parent::patterns();
        $patterns["[[$fieldname]]"] = array(true, $fieldname);
        $patterns["[[$fieldname:text]]"] = array(false);
        $patterns["[[$fieldname:wordcount]]"] = array(false);

        return $patterns;
    }
}
