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
 * @subpackage picture
 * @copyright 2011 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class dataformfield_picture_picture extends dataformfield_file_file {

    /**
     * Returns appearance settings from param4
     *
     * @return stdClass|null
     */
    public function get_appearance() {
        if (!empty($this->_field->param4)) {
            if (!is_object($this->_field->param4)) {
                $appearance = unserialize(base64_decode($this->_field->param4));

                // Add defaults.
                foreach ($this->appearance_defaults as $key => $default) {
                    if (!isset($appearance->$key)) {
                        $appearance->$key = $default;
                    }
                }
                $this->_field->param4 = $appearance;
            }
            return $this->_field->param4;
        }

        return (object) $this->appearance_defaults;
    }

    /**
     * Returns list of appearance defaults.
     *
     * @return array Associative attribute => default
     */
    public function get_appearance_defaults() {
        return array(
            'separator' => "\n",
            'dispw' => '',
            'disph' => '',
            'dispu' => 'px',
            'maxw' => '',
            'maxh' => '',
            'thumbw' => '',
            'thumbh' => '',
        );
    }

    /**
     *
     */
    public function update($data) {
        global $DB, $OUTPUT;

        // Get the old field data so that we can check whether the thumbnail dimensions have changed.
        $oldappearance = $this->appearance;
        if (!parent::update($data)) {
            echo $OUTPUT->notification('updating of new field failed!');
            return false;
        }

        // Have the dimensions changed?
        $updatefile = ($oldappearance->maxw != $this->appearance->maxw or $oldappearance->maxh != $this->appearance->maxh);
        $updatethumb = ($oldappearance->thumbw != $this->appearance->thumbw or $oldappearance->thumbh != $this->appearance->thumbh);
        if ($oldappearance and ($updatefile or $updatethumb)) {
            // Check through all existing records and update the thumbnail.
            if ($contents = $DB->get_records('dataform_contents', array('fieldid' => $this->id))) {
                if (count($contents) > 20) {
                    echo $OUTPUT->notification(get_string('resizingimages', 'dataformfield_picture'), 'notifysuccess');
                    echo "\n\n";
                    // To make sure that ob_flush() has the desired effect.
                    ob_flush();
                }
                foreach ($contents as $content) {
                    @set_time_limit(300);
                    // Might be slow!
                    $this->update_content_files($content->id, array('updatefile' => $updatefile, 'updatethumb' => $updatethumb));
                }
            }
        }
        return true;
    }

    /**
     * (Re)generate pic and thumbnail images according to the dimensions specified in the field settings.
     */
    protected function update_content_files($contentid, $params = null) {

        $updatefile = isset($params['updatefile']) ? $params['updatefile'] : true;
        $updatethumb = isset($params['updatethumb']) ? $params['updatethumb'] : true;

        $fs = get_file_storage();
        if (!$files = $fs->get_area_files($this->df->context->id, 'mod_dataform', 'content', $contentid)) {
            return;
        }

        // Update dimensions and regenerate thumbs.
        foreach ($files as $file) {

            if ($file->is_valid_image() and strpos($file->get_filename(), 'thumb_') === false) {
                // Original first.
                if ($updatefile) {
                    $maxwidth  = $this->appearance->maxw ? $this->appearance->maxw : 0;
                    $maxheight = $this->appearance->maxh ? $this->appearance->maxh : 0;

                    // If either width or height try to (re)generate.
                    if ($maxwidth or $maxheight) {
                        // This may fail for various reasons.
                        try {
                            $filerec = array(
                                'contextid' => $file->get_contextid(),
                                'component' => $file->get_component(),
                                'filearea' => 'conversion',
                                'itemid' => $contentid,
                                'filepath' => $file->get_filepath(),
                                'userid' => $file->get_userid()
                            );
                            $tempfile = $fs->convert_image($filerec, $file, $maxwidth, $maxheight, true);
                            // Delete the original file.
                            $file->delete();
                            // Regenerate from tempfile.
                            $filerec['filearea'] = 'content';
                            $file = $fs->create_file_from_storedfile($filerec, $tempfile);
                            $tempfile->delete();
                        } catch (Exception $e) {
                            return false;
                        }
                    }
                }

                // Thumbnail next.
                if ($updatethumb) {
                    $thumbwidth  = $this->appearance->thumbw ? $this->appearance->thumbw : '';
                    $thumbheight = $this->appearance->thumbh ? $this->appearance->thumbh : '';
                    $thumbname = 'thumb_'.$file->get_filename();

                    if ($thumbfile = $fs->get_file($this->df->context->id, 'mod_dataform', 'content', $contentid, '/', $thumbname)) {
                        $thumbfile->delete();
                    }

                    // If either width or height try to (re)generate, otherwise delete what exists.
                    if ($thumbwidth or $thumbheight) {

                        $filerec = array(
                            'contextid' => $this->df->context->id,
                            'component' => 'mod_dataform',
                            'filearea' => 'content',
                            'itemid' => $contentid,
                            'filepath' => '/',
                            'filename' => $thumbname,
                            'userid' => $file->get_userid()
                        );

                        try {
                            $fs->convert_image($filerec, $file, $thumbwidth, $thumbheight, true);
                        } catch (Exception $e) {
                            return false;
                        }
                    }
                }
            }
        }
        return true;
    }

}
