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
 * Utility class for browsing of user files.
 *
 * @package    core
 * @subpackage filebrowser
 * @copyright  2008 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Represents a user context in the tree navigated by @see{file_browser}.
 *
 * @package    core
 * @subpackage filebrowser
 * @copyright  2008 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class file_info_context_user extends file_info {
    protected $user;

    public function __construct($browser, $context, $user) {
        parent::__construct($browser, $context);
        $this->user = $user;
    }

    /**
     * Return information about this specific context level
     *
     * @param $component
     * @param $filearea
     * @param $itemid
     * @param $filepath
     * @param $filename
     */
    public function get_file_info($component, $filearea, $itemid, $filepath, $filename) {
        global $USER;

        if (!isloggedin() or isguestuser()) {
            return null;
        }

        if (empty($component)) {
            // access control: list areas only for myself
            if ($this->user->id != $USER->id) {
                // no list of areas for other users
                return null;
            }
            return $this;
        }

        $methodname = "get_area_{$component}_{$filearea}";
        if (method_exists($this, $methodname)) {
            return $this->$methodname($itemid, $filepath, $filename);
        }

        return null;
    }

    protected function get_area_user_private($itemid, $filepath, $filename) {
        global $USER, $CFG;

        // access control: only my files, nobody else
        if ($this->user->id != $USER->id) {
            return null;
        }

        if (is_null($itemid)) {
            // go to parent, we do not use itemids here in private area
            return $this;
        }

        $fs = get_file_storage();

        $filepath = is_null($filepath) ? '/' : $filepath;
        $filename = is_null($filename) ? '.' : $filename;

        if (!$storedfile = $fs->get_file($this->context->id, 'user', 'private', 0, $filepath, $filename)) {
            if ($filepath === '/' and $filename === '.') {
                // root dir does not exist yet
                $storedfile = new virtual_root_file($this->context->id, 'user', 'private', 0);
            } else {
                // not found
                return null;
            }
        }
        $urlbase = $CFG->wwwroot.'/pluginfile.php';

        //TODO: user quota from $CFG->userquota

        return new file_info_stored($this->browser, $this->context, $storedfile, $urlbase, get_string('areauserpersonal', 'repository'), false, true, true, false);
    }

    protected function get_area_user_profile($itemid, $filepath, $filename) {
        global $CFG;

        $readaccess = has_capability('moodle/user:update', $this->context);
        $writeaccess = has_capability('moodle/user:viewalldetails', $this->context);

        if (!$readaccess and !$writeaccess) {
            // the idea here is that only admins should be able to list/modify files in user profile, the rest has to use profile page
            return null;
        }

        if (is_null($itemid)) {
            // go to parent, we do not use itemids here in profile area
            return $this;
        }

        $fs = get_file_storage();

        $filepath = is_null($filepath) ? '/' : $filepath;
        $filename = is_null($filename) ? '.' : $filename;

        if (!$storedfile = $fs->get_file($this->context->id, 'user', 'profile', 0, $filepath, $filename)) {
            if ($filepath === '/' and $filename === '.') {
                $storedfile = new virtual_root_file($this->context->id, 'user', 'profile', 0);
            } else {
                // not found
                return null;
            }
        }
        $urlbase = $CFG->wwwroot.'/pluginfile.php';
        return new file_info_stored($this->browser, $this->context, $storedfile, $urlbase, 
                get_string('areauserprofile', 'repository'), false, $readaccess, $writeaccess, false);
    }

    protected function get_area_user_draft($itemid, $filepath, $filename) {
        global $USER, $CFG;

        // access control: only my files
        if ($this->user->id != $USER->id) {
            return null;
        }

        if (empty($itemid)) {
            // do not browse itemids - you must know the draftid to see what is there
            return null;
        }

        $fs = get_file_storage();

        $filepath = is_null($filepath) ? '/' : $filepath;
        $filename = is_null($filename) ? '.' : $filename;

        if (!$storedfile = $fs->get_file($this->context->id, 'user', 'draft', $itemid, $filepath, $filename)) {
            if ($filepath === '/' and $filename === '.') {
                $storedfile = new virtual_root_file($this->context->id, 'user', 'draft', $itemid);
            } else {
                // not found
                return null;
            }
        }
        $urlbase = $CFG->wwwroot.'/pluginfile.php';
        return new file_info_stored($this->browser, $this->context, $storedfile, $urlbase, get_string('areauserdraft', 'repository'), true, true, true, true);
    }

    protected function get_area_user_backup($itemid, $filepath, $filename) {
        global $USER, $CFG;

        // access control: only my files, nobody else - TODO: maybe we need new capability here
        if ($this->context->instanceid != $USER->id) {
            return null;
        }

        if (is_null($itemid)) {
            // go to parent, we do not use itemids here in profile area
            return $this;
        }

        $fs = get_file_storage();

        $filepath = is_null($filepath) ? '/' : $filepath;
        $filename = is_null($filename) ? '.' : $filename;

        if (!$storedfile = $fs->get_file($this->context->id, 'user', 'backup', $itemid, $filepath, $filename)) {
            if ($filepath === '/' and $filename === '.') {
                $storedfile = new virtual_root_file($this->context->id, 'user', 'backup', 0);
            } else {
                // not found
                return null;
            }
        }
        $urlbase = $CFG->wwwroot.'/pluginfile.php';
        return new file_info_stored($this->browser, $this->context, $storedfile, $urlbase, get_string('areauserbackup', 'repository'), false, true, true, false);
    }

    /**
     * Returns localised visible name.
     * @return string
     */
    public function get_visible_name() {
        return fullname($this->user, true);
    }

    /**
     * Can I add new files or directories?
     * @return bool
     */
    public function is_writable() {
        return false;
    }

    /**
     * Is directory?
     * @return bool
     */
    public function is_directory() {
        return true;
    }

    /**
     * Returns list of children.
     * @return array of file_info instances
     */
    public function get_children() {
        $children = array();

        if ($child = $this->get_area_user_private(0, '/', '.')) {
            $children[] = $child;
        }
/*
        if ($child = $this->get_area_user_profile(0, '/', '.')) {
            $children[] = $child;
        }
*/
        if ($child = $this->get_area_user_backup(0, '/', '.')) {
            $children[] = $child;
        }
        // do not list draft area here - it is browsable only if you know the draft itemid ;-)

        return $children;
    }

    /**
     * Returns parent file_info instance
     * @return file_info or null for root
     */
    public function get_parent() {
        return $this->browser->get_file_info();
    }
}
