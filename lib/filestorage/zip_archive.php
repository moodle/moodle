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
 * Implementation of zip file archive.
 *
 * @package    core
 * @subpackage filestorage
 * @copyright  2008 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/filestorage/file_archive.php");

/**
 * zip file archive class.
 *
 * @package    core
 * @subpackage filestorage
 * @copyright  2008 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class zip_archive extends file_archive {

    /** Pathname of archive */
    protected $archivepathname = null;

    /** Used memory tracking */
    protected $usedmem = 0;

    /** Iteration position */
    protected $pos = 0;

    /** TipArchive instance */
    protected $za;

    /**
     * Open or create archive (depending on $mode)
     * @param string $archivepathname
     * @param int $mode OPEN, CREATE or OVERWRITE constant
     * @param string $encoding archive local paths encoding
     * @return bool success
     */
    public function open($archivepathname, $mode=file_archive::CREATE, $encoding='utf-8') {
        $this->close();

        $this->usedmem = 0;
        $this->pos     = 0;

        $this->za = new ZipArchive();

        switch($mode) {
            case file_archive::OPEN:      $flags = 0; break;
            case file_archive::OVERWRITE: $flags = ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE; break; //changed in PHP 5.2.8
            case file_archive::CREATE:
            default :                     $flags = ZIPARCHIVE::CREATE; break;
        }

        $result = $this->za->open($archivepathname, $flags);

        if ($result === true) {
            $this->encoding    = $encoding;
            if (file_exists($archivepathname)) {
                $this->archivepathname = realpath($archivepathname);
            } else {
                $this->archivepathname = $archivepathname;
            }
            return true;

        } else {
            $this->za = null;
            $this->archivepathname = null;
            $this->encooding       = 'utf-8';
            // TODO: maybe we should return some error info
            return false;
        }
    }

    /**
     * Close archive
     * @return bool success
     */
    public function close() {
        if (!isset($this->za)) {
            return false;
        }

        $res = $this->za->close();
        $this->za = null;

        return $res;
    }

    /**
     * Returns file stream for reading of content
     * @param int $index of file
     * @return resource or false if error
     */
    public function get_stream($index) {
        if (!isset($this->za)) {
            return false;
        }

        $name = $this->za->getNameIndex($index);
        if ($name === false) {
            return false;
        }

        return $this->za->getStream($name);
    }

    /**
     * Returns file information
     * @param int $index of file
     * @return info object or false if error
     */
    public function get_info($index) {
        if (!isset($this->za)) {
            return false;
        }

        if ($index < 0 or $index >=$this->count()) {
            return false;
        }

        $result = $this->za->statIndex($index);

        if ($result === false) {
            return false;
        }

        $info = new stdClass();
        $info->index             = $index;
        $info->original_pathname = $result['name'];
        $info->pathname          = $this->unmangle_pathname($result['name']);
        $info->mtime             = (int)$result['mtime'];

        if ($info->pathname[strlen($info->pathname)-1] === '/') {
            $info->is_directory = true;
            $info->size         = 0;
        } else {
            $info->is_directory = false;
            $info->size         = (int)$result['size'];
        }

        return $info;
    }

    /**
     * Returns array of info about all files in archive
     * @return array of file infos
     */
    public function list_files() {
        if (!isset($this->za)) {
            return false;
        }

        $infos = array();

        for ($i=0; $i<$this->count(); $i++) {
            $info = $this->get_info($i);
            if ($info === false) {
                continue;
            }
            $infos[$i] = $info;
        }

        return $infos;
    }

    /**
     * Returns number of files in archive
     * @return int number of files
     */
    public function count() {
        if (!isset($this->za)) {
            return false;
        }

        return $this->za->numFiles;
    }

    /**
     * Add file into archive
     * @param string $localname name of file in archive
     * @param string $pathname location of file
     * @return bool success
     */
    public function add_file_from_pathname($localname, $pathname) {
        if (!isset($this->za)) {
            return false;
        }

        if ($this->archivepathname === realpath($pathname)) {
            // do not add self into archive
            return false;
        }

        if (is_null($localname)) {
            $localname = clean_param($pathname, PARAM_PATH);
        }
        $localname = trim($localname, '/'); // no leading slashes in archives
        $localname = $this->mangle_pathname($localname);

        if ($localname === '') {
            //sorry - conversion failed badly
            return false;
        }

        if (!check_php_version('5.2.8')) {
            // workaround for open file handles problem, ZipArchive uses file locking in order to prevent file modifications before the close() (strange, eh?)
            if ($this->count() > 0 and $this->count() % 500 === 0) {
                $this->close();
                $res = $this->open($this->archivepathname, file_archive::OPEN, $this->encoding);
                if ($res !== true) {
                    print_error('cannotopenzip'); //TODO ??
                }
            }
        }

        return $this->za->addFile($pathname, $localname);
    }

    /**
     * Add content of string into archive
     * @param string $localname name of file in archive
     * @param string $contents
     * @return bool success
     */
    public function add_file_from_string($localname, $contents) {
        if (!isset($this->za)) {
            return false;
        }

        $localname = trim($localname, '/'); // no leading slashes in archives
        $localname = $this->mangle_pathname($localname);

        if ($localname === '') {
            //sorry - conversion failed badly
            return false;
        }

        if ($this->usedmem > 2097151) {
        /// this prevents running out of memory when adding many large files using strings
            $this->close();
            $res = $this->open($this->archivepathname, file_archive::OPEN, $this->encoding);
            if ($res !== true) {
                print_error('cannotopenzip'); //TODO ??
            }
        }
        $this->usedmem += strlen($contents);

        return $this->za->addFromString($localname, $contents);

    }

    /**
     * Add empty directory into archive
     * @param string $local
     * @return bool success
     */
    public function add_directory($localname) {
        if (!isset($this->za)) {
            return false;
        }
        $localname = ltrim($localname, '/'). '/';
        $localname = $this->mangle_pathname($localname);

        if ($localname === '/') {
            //sorry - conversion failed badly
            return false;
        }

        return $this->za->addEmptyDir($localname);
    }

    /**
     * Returns current file info
     * @return object
     */
    public function current() {
        if (!isset($this->za)) {
            return false;
        }

        return $this->get_info($this->pos);
    }

    /**
     * Returns the index of current file
     * @return int current file index
     */
    public function key() {
        return $this->pos;
    }

    /**
     * Moves forward to next file
     * @return void
     */
    public function next() {
        $this->pos++;
    }

    /**
     * Rewinds back to the first file
     * @return void
     */
    public function rewind() {
        $this->pos = 0;
    }

    /**
     * Did we reach the end?
     * @return boolean
     */
    public function valid() {
        if (!isset($this->za)) {
            return false;
        }

        return ($this->pos < $this->count());
    }
}
