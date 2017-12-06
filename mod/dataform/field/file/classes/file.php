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
 * @copyright 2012 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 *
 */
class dataformfield_file_file extends mod_dataform\pluginbase\dataformfield {
    // Content - file manager
    // content1 - alt name
    // content2 - download counter.

    /**
     * Returns appearance settings from param4
     *
     * @return stdClass
     */
    public function get_appearance() {
        $appearance = new stdClass;

        if ($this->param4) {
            $appearance->separator = $this->param4;
        } else {
            $appearance->separator = "\n";
        }

        return $appearance;
    }

    /**
     *
     */
    public function content_names() {
        return array('filemanager', 'alttext');
    }

    /**
     *
     */
    public function update_content($entry, array $values = null, $savenew = false) {
        global $DB, $USER;

        $entryid = $entry->id;
        $fieldid = $this->id;

        $filemanager = $alttext = null;
        if (!empty($values)) {
            foreach ($values as $name => $value) {
                if (!empty($name) and !empty($value)) {
                    ${$name} = $value;
                }
            }
        }

        // Store uploaded files.
        $contentid = isset($entry->{"c{$this->id}_id"}) ? $entry->{"c{$this->id}_id"} : null;
        $draftarea = $filemanager;
        $usercontext = context_user::instance($USER->id);

        $fs = get_file_storage();
        if ($files = $fs->get_area_files($usercontext->id, 'user', 'draft', $draftarea, 'sortorder', false)) {
            // There are files to upload so add/update content record.
            $rec = new \stdClass;
            $rec->fieldid = $fieldid;
            $rec->entryid = $entryid;
            $rec->content = 1;
            $rec->content1 = $alttext;

            if (empty($contentid) or $savenew) {
                $contentid = $DB->insert_record('dataform_contents', $rec);
            } else {
                $rec->id = $contentid;
                $DB->update_record('dataform_contents', $rec);
            }

            // Now save files.
            $options = array('subdirs' => 0,
                                'maxbytes' => $this->param1,
                                'maxfiles' => $this->param2,
                                'accepted_types' => $this->param3);
            $contextid = $this->df->context->id;
            file_save_draft_area_files($filemanager, $contextid, 'mod_dataform', 'content', $contentid, $options);

            $this->update_content_files($contentid);

        } else if (!empty($contentid) and !$savenew) {
            // User cleared files from the field.
            $this->delete_content($entryid);
        }
        return true;
    }

    /**
     *
     */
    protected function format_content($entry, array $values = null) {
        return array(null, null);
    }

    /**
     *
     */
    public function get_content_parts() {
        return array('content', 'content1', 'content2');
    }

    /**
     * Overrides {@link dataformfield::prepare_import_content()}
     * to set import of files.
     *
     * @return stdClass
     */
    public function prepare_import_content($data, $importsettings, $csvrecord = null, $entryid = null) {
        global $USER;

        $fieldid = $this->id;

        // Only one imported pattern ''.
        $settings = reset($importsettings);

        static $draftid;
        $fs = get_file_storage();
        $usercontext = context_user::instance($USER->id);

        // Upload files done only once.
        if (!empty($settings['filepicker'])) {
            if (!$draftid) {
                $draftid = file_get_unused_draft_itemid();
            }
            $zipdraftid = $settings['filepicker'];

            if ($files = $fs->get_area_files($usercontext->id, 'user', 'draft', $zipdraftid, 'sortorder', false)) {
                $zipfile = reset($files);
                // Extract files to the draft area.
                $zipper = get_file_packer('application/zip');
                $zipfile->extract_to_storage($zipper, $usercontext->id, 'user', 'draft', $draftid, '/');
                // $zipfile->delete();.
            }
        }

        if (!empty($settings['name'])) {
            $csvname = $settings['name'];

            if (isset($csvrecord[$csvname]) and $csvrecord[$csvname] !== '') {
                $filenames = explode('#', $csvrecord[$csvname]);

                $varname = "field_{$fieldid}_$entryid";
                $itemid = file_get_unused_draft_itemid();
                $data->{"{$varname}_filemanager"} = $itemid;

                foreach ($filenames as $filename) {
                    if ($file = $fs->get_file($usercontext->id, 'user', 'draft', $draftid, '/', $filename)) {
                        $rec = new stdClass;
                        $rec->contextid = $usercontext->id;
                        $rec->component = 'user';
                        $rec->filearea = 'draft';
                        $rec->itemid = $itemid;
                        $fs->create_file_from_storedfile($rec, $file);
                    }
                }
            }
        }

        return $data;
    }

    /**
     *
     */
    protected function update_content_files($contentid, $params = null) {
        return true;
    }

    /**
     * Overriding parent to return no sort/search options.
     *
     * @return array
     */
    public function get_sort_options_menu() {
        return array();
    }
}
