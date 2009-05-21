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
 * Base for all file browsing classes.
 *
 * @package    moodlecore
 * @subpackage file-browser
 * @copyright  2008 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Base class for things in the tree navigated by @see{file_browser}.
 */
abstract class file_info {

    protected $context;

    protected $browser;

    public function __construct($browser, $context) {
        $this->browser = $browser;
        $this->context = $context;
    }

    public abstract function get_params();

    public abstract function get_visible_name();

    public abstract function is_directory();

    public abstract function get_children();

    public abstract function get_parent();

    public function get_params_rawencoded() {
        $params = $this->get_params();
        $encoded = array();
        $encoded[] = 'contextid='.$params['contextid'];
        $encoded[] = 'filearea='.$params['filearea'];
        $encoded[] = 'itemid='.(is_null($params['itemid']) ? -1 : $params['itemid']);
        $encoded[] = 'filepath='.(is_null($params['filepath']) ? '' : rawurlencode($params['filepath']));
        $encoded[] = 'filename='.((is_null($params['filename']) or $params['filename'] === '.') ? '' : rawurlencode($params['filename']));

        return $encoded;
    }

    public function get_url($forcedownload=false, $https=false) {
        return null;
    }

    public function is_readable() {
        return true;
    }

    public function is_writable() {
        return true;
    }

    public function get_filesize() {
        return null;
    }

    public function get_mimetype() {
        // TODO: add some custom mime icons for courses, categories??
        return null;
    }

    public function get_timecreated() {
        return null;
    }

    public function get_timemodified() {
        return null;
    }

    public function create_directory($newdirname, $userid=null) {
        return null;
    }

    public function create_file_from_string($newfilename, $content, $userid=null) {
        return null;
    }

    public function create_file_from_pathname($newfilename, $pathname, $userid=null) {
        return null;
    }

    public function create_file_from_storedfile($newfilename, $fid, $userid=null) {
        return null;
    }

    public function delete() {
        return false;
    }

    /**
     * Copy content of this file to local storage, overriding current file if needed.
     * @param int $contextid
     * @param string $filearea
     * @param int $itemid
     * @param string $filepath
     * @param string $filename
     * @return boolean success
     */
    public function copy_to_storage($contextid, $filearea, $itemid, $filepath, $filename) {
        return false;
    }

    /**
     * Copy content of this file to local storage, overriding current file if needed.
     * @param string $pathname real local full file name
     * @return boolean success
     */
    public function copy_to_pathname($pathname) {
        return false;
    }


//TODO: following methods are not implemented yet ;-)
    //public abstract function move(location params);
    //public abstract function rename(new name);
    //public abstract function unzip(location params);
    //public abstract function zip(zip file, file info);
}
