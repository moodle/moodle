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
 * Blackboard V5 and V6 question importer.
 *
 * @package    qformat_blackboard_six
 * @copyright  2012 Jean-Michel Vedrine
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Base class question import format for zip files with images
 *
 * @package    qformat_blackboard_six
 * @copyright  2012 Jean-Michel Vedrine
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qformat_blackboard_six_base extends qformat_based_on_xml {
    /** @var string path to path to root of image tree in unzipped archive. */
    public $filebase = '';
    /** @var string path to the temporary directory. */
    public $tempdir = '';

    /**
     * This plugin provide import
     * @return bool true
     */
    public function provide_import() {
        return true;
    }

    /**
     * Check if the given file is capable of being imported by this plugin.
     * As {@link file_storage::mimetype()} may use finfo PHP extension if available,
     * the value returned by $file->get_mimetype for a .dat file is not the same on all servers.
     * So we must made 2 checks to verify if the plugin can import the file.
     * @param stored_file $file the file to check
     * @return bool whether this plugin can import the file
     */
    public function can_import_file($file) {
        $mimetypes = array(
            mimeinfo('type', '.dat'),
            mimeinfo('type', '.zip')
        );
        return in_array($file->get_mimetype(), $mimetypes) || in_array(mimeinfo('type', $file->get_filename()), $mimetypes);
    }

    public function mime_type() {
        return mimeinfo('type', '.zip');
    }

    /**
     * Does any post-processing that may be desired
     * Clean the temporary directory if a zip file was imported
     * @return bool success
     */
    public function importpostprocess() {
        if ($this->tempdir != '') {
            fulldelete($this->tempdir);
        }
        return true;
    }
    /**
     * Set the path to the root of images tree
     * @param string $path path to images root
     */
    public function set_filebase($path) {
        $this->filebase = $path;
    }

    /**
     * Store an image file in a draft filearea
     * @param array $text, if itemid element don't exists it will be created
     * @param string $tempdir path to root of image tree
     * @param string $filepathinsidetempdir path to image in the tree
     * @param string $filename image's name
     * @return string new name of the image as it was stored
     */
    protected function store_file_for_text_field(&$text, $tempdir, $filepathinsidetempdir, $filename) {
        global $USER;
        $fs = get_file_storage();
        if (empty($text['itemid'])) {
            $text['itemid'] = file_get_unused_draft_itemid();
        }
        // As question file areas don't support subdirs,
        // convert path to filename.
        // So that images with same name can be imported.
        $newfilename = clean_param(str_replace('/', '__', $filepathinsidetempdir . '__' . $filename), PARAM_FILE);
        $filerecord = array(
            'contextid' => context_user::instance($USER->id)->id,
            'component' => 'user',
            'filearea'  => 'draft',
            'itemid'    => $text['itemid'],
            'filepath'  => '/',
            'filename'  => $newfilename,
        );
        $fs->create_file_from_pathname($filerecord, $tempdir . '/' . $filepathinsidetempdir . '/' . $filename);
        return $newfilename;
    }

    /**
     * Given an HTML text with references to images files,
     * store all images in a draft filearea,
     * and return an array with all urls in text recoded,
     * format set to FORMAT_HTML, and itemid set to filearea itemid
     * @param string $text text to parse and recode
     * @return array with keys text, format, itemid.
     */
    public function text_field($text) {
        $data = array();
        // Step one, find all file refs then add to array.
        preg_match_all('|<img[^>]+src="([^"]*)"|i', $text, $out); // Find all src refs.
        $filepaths = array();
        foreach ($out[1] as $path) {
            $fullpath = $this->filebase . '/' . $path;

            if (is_readable($fullpath) && !in_array($path, $filepaths)) {
                $dirpath = dirname($path);
                $filename = basename($path);
                $newfilename = $this->store_file_for_text_field($data, $this->filebase, $dirpath, $filename);
                $text = preg_replace("|{$path}|", "@@PLUGINFILE@@/" . $newfilename, $text);
                $filepaths[] = $path;
            }

        }
        $data['text'] = $text;
        $data['format'] = FORMAT_HTML;
        return $data;
    }

    /**
     * Same as text_field but text is cleaned.
     * @param string $text text to parse and recode
     * @return array with keys text, format, itemid.
     */
    public function cleaned_text_field($text) {
        return $this->text_field($this->cleaninput($text));
    }
}
