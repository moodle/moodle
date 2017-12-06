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
 * @package dataformfield
 * @subpackage file
 * @copyright 2011 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') or die();

/**
 *
 */
class dataformfield_file_renderer extends mod_dataform\pluginbase\dataformfieldrenderer {

    /**
     *
     */
    protected function replacements(array $patterns, $entry, array $options = null) {
        $field = $this->_field;
        $fieldname = $field->name;

        $edit = !empty($options['edit']);
        $haseditreplacement = false;
        $editablepatterns = array("[[$fieldname]]");

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

            // Browse mode.
            $displaybrowse = '';
            if ($cleanpattern == "[[$fieldname]]") {
                $displaybrowse = $this->display_browse($entry);
            } else if ($cleanpattern == "[[{$fieldname}:url]]") {
                // Url.
                $displaybrowse = $this->display_browse($entry, array('url' => 1));
            } else if ($cleanpattern == "[[{$fieldname}:alt]]") {
                // Alt.
                $displaybrowse = $this->display_browse($entry, array('alt' => 1));
            } else if ($cleanpattern == "[[{$fieldname}:size]]") {
                // Size.
                $displaybrowse = $this->display_browse($entry, array('size' => 1));
            } else if ($cleanpattern == "[[{$fieldname}:download]]") {
                // Download.
                $displaybrowse = $this->display_browse($entry, array('download' => 1));
            } else if ($cleanpattern == "[[{$fieldname}:downloadcount]]") {
                // Download count.
                $displaybrowse = $this->display_browse($entry, array('downloadcount' => 1));
            }

            if (!empty($displaybrowse)) {
                $replacements[$pattern] = $displaybrowse;
            } else {
                $replacements[$pattern] = '';
            }
        }

        return $replacements;
    }

    /**
     *
     */
    public function display_edit(&$mform, $entry, array $options = null) {
        global $PAGE;

        $field = $this->_field;
        $fieldid = $field->id;

        $entryid = $entry->id;
        $contentid = isset($entry->{"c{$fieldid}_id"}) ? $entry->{"c{$fieldid}_id"} : null;
        $content = isset($entry->{"c{$fieldid}_content"}) ? $entry->{"c{$fieldid}_content"} : null;
        $content1 = isset($entry->{"c{$fieldid}_content1"}) ? $entry->{"c{$fieldid}_content1"} : null;

        $fieldname = "field_{$fieldid}_{$entryid}";
        $fmoptions = array('subdirs' => 0,
                            'maxbytes' => $field->param1,
                            'maxfiles' => $field->param2,
                            'accepted_types' => explode(',', $field->param3));

        $draftitemid = file_get_submitted_draft_itemid("{$fieldname}_filemanager");
        file_prepare_draft_area($draftitemid, $field->get_df()->context->id, 'mod_dataform', 'content', $contentid, $fmoptions);

        // File manager.
        $mform->addElement('filemanager', "{$fieldname}_filemanager", $field->name, null, $fmoptions);
        $mform->setDefault("{$fieldname}_filemanager", $draftitemid);
        $required = !empty($options['required']);
        if ($required) {
            $mform->addRule("{$fieldname}_filemanager", null, 'required', null, 'client');
        }

        // Alt text
        // $altoptions = array();
        // $mform->addElement('text', "{$fieldname}_alttext", get_string('alttext', 'dataformfield_file'), $altoptions);
        // $mform->setDefault("{$fieldname}_alttext", s($content1));.
    }

    /**
     *
     */
    public function display_browse($entry, $params = null, $hidden = false) {

        $field = $this->_field;
        $fieldid = $field->id;
        $entryid = $entry->id;
        $df = $field->df;

        $content = isset($entry->{"c{$fieldid}_content"}) ? $entry->{"c{$fieldid}_content"} : null;
        $content1 = isset($entry->{"c{$fieldid}_content1"}) ? $entry->{"c{$fieldid}_content1"} : null;
        $content2 = isset($entry->{"c{$fieldid}_content2"}) ? $entry->{"c{$fieldid}_content2"} : null;
        $contentid = isset($entry->{"c{$fieldid}_id"}) ? $entry->{"c{$fieldid}_id"} : null;

        if (empty($content)) {
            return '';
        }

        if (!empty($params['downloadcount'])) {
            return $content2;
        }

        $contextid = $df->context->id;

        $fs = get_file_storage();
        $files = $fs->get_area_files($contextid, 'mod_dataform', 'content', $contentid, 'sortorder', false);
        if (!$files) {
            return '';
        }

        $altname = empty($content1) ? '' : s($content1);

        if (!empty($params['alt'])) {
            return $altname;
        }

        $strfiles = array();
        foreach ($files as $file) {
            if (!$file->is_directory()) {

                $filename = $file->get_filename();
                $filenameinfo = pathinfo($filename);
                $contentidhash = $df->get_content_id_hash($contentid);
                $path = "/$contextid/mod_dataform/content/$contentidhash";

                $strfiles[] = $this->display_file($file, $path, $altname, $params);
            }
        }
        return implode($field->appearance->separator, $strfiles);
    }

    /**
     *
     */
    protected function display_file($file, $path, $altname, $params = null) {
        global $CFG, $OUTPUT;

        $filename = $file->get_filename();
        $pluginfileurl = '/pluginfile.php';

        if (!empty($params['url'])) {
            return moodle_url::make_file_url($pluginfileurl, "$path/$filename");

        } else if (!empty($params['size'])) {
            $bsize = $file->get_filesize();
            if ($bsize < 1000000) {
                $size = round($bsize / 1000, 1). 'KB';
            } else {
                $size = round($bsize / 1000000, 1). 'MB';
            }
            return $size;

        } else {
            return $this->display_link($file, $path, $altname, $params);
        }
    }

    /**
     *
     */
    protected function display_link($file, $path, $altname, $params = null) {
        global $OUTPUT;

        $filename = $file->get_filename();
        $displayname = $altname ? $altname : $filename;

        $fileicon = html_writer::empty_tag('img', array(
            'src' => $OUTPUT->pix_url(file_mimetype_icon($file->get_mimetype())),
            'alt' => $file->get_mimetype(),
            'height' => 16,
            'width' => 16)
        );
        if (!empty($params['download'])) {
            list(, $context, , , $contentid) = explode('/', $path);
            $url = new moodle_url("/mod/dataform/field/file/download.php", array('cid' => $contentid, 'context' => $context, 'file' => $filename));
        } else {
            $url = moodle_url::make_file_url('/pluginfile.php', "$path/$filename");
        }

        return html_writer::link($url, "$fileicon&nbsp;$displayname");
    }

    /**
     *
     */
    public function pluginfile_patterns() {
        return array("[[{$this->_field->name}]]");
    }

    /**
     * Returns import settings for the specified field pattern
     * that consist of a list of mform elements to group, and
     * a list of corresponding labels.
     * By default adds the specified pattern name. Subclasses can override
     * to exclude patterns from import or deny import at all.
     *
     * @param moodleform $mform
     * @param string $patternname
     * @param string $header The default value of the name element
     * @return array
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

        // File picker.
        $fmoptions = array(
            'subdirs' => 0,
            'maxbytes' => $field->get_df()->course->maxbytes,
            'maxfiles' => 1,
            'accepted_types' => array('*.zip'),
        );

        $grp[] = &$mform->createElement('filepicker', "{$name}_filepicker", null, null, $fmoptions);
        $labels[] = '';

        return array($grp, $labels);
    }

    /**
     * Array of patterns this field supports
     */
    protected function patterns() {
        $fieldname = $this->_field->name;

        $patterns = parent::patterns();
        $patterns["[[$fieldname]]"] = array(true, $fieldname);
        $patterns["[[$fieldname:url]]"] = array(true, $fieldname);
        $patterns["[[$fieldname:alt]]"] = array(true, $fieldname);
        $patterns["[[$fieldname:size]]"] = array(false);
        $patterns["[[$fieldname:download]]"] = array(false);
        $patterns["[[$fieldname:downloadcount]]"] = array(false);

        return $patterns;
    }
}
