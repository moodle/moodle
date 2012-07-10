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
 * @package   core_files
 * @copyright 2008 Petr Skoda (http://skodak.org)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/filestorage/file_archive.php");

/**
 * zip file archive class.
 *
 * @package   core_files
 * @category  files
 * @copyright 2008 Petr Skoda (http://skodak.org)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class zip_archive extends file_archive {

    /** @var string Pathname of archive */
    protected $archivepathname = null;

    /** @var int Used memory tracking */
    protected $usedmem = 0;

    /** @var int Iteration position */
    protected $pos = 0;

    /** @var ZipArchive instance */
    protected $za;

    /** @var bool was this archive modified? */
    protected $modified = false;

    /**
     * Open or create archive (depending on $mode)
     *
     * @todo MDL-31048 return error message
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
            $this->encoding        = 'utf-8';
            // TODO: maybe we should return some error info
            return false;
        }
    }

    /**
     * Close archive
     *
     * @return bool success
     */
    public function close() {
        if (!isset($this->za)) {
            return false;
        }

        $res = $this->za->close();
        $this->za = null;

        if ($this->modified) {
            $this->fix_utf8_flags();
            $this->modified = false;
        }

        return $res;
    }

    /**
     * Returns file stream for reading of content
     *
     * @param int $index index of file
     * @return resource|bool file handle or false if error
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
     *
     * @param int $index index of file
     * @return stdClass info object or false if error
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
     *
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
     *
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
     *
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
                    print_error('cannotopenzip');
                }
            }
        }

        if (!$this->za->addFile($pathname, $localname)) {
            return false;
        }
        $this->modified = true;
        return true;
    }

    /**
     * Add content of string into archive
     *
     * @param string $localname name of file in archive
     * @param string $contents contents
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
            // this prevents running out of memory when adding many large files using strings
            $this->close();
            $res = $this->open($this->archivepathname, file_archive::OPEN, $this->encoding);
            if ($res !== true) {
                print_error('cannotopenzip');
            }
        }
        $this->usedmem += strlen($contents);

        if (!$this->za->addFromString($localname, $contents)) {
            return false;
        }
        $this->modified = true;
        return true;
    }

    /**
     * Add empty directory into archive
     *
     * @param string $localname name of file in archive
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

        if (!$this->za->addEmptyDir($localname)) {
            return false;
        }
        $this->modified = true;
        return true;
    }

    /**
     * Returns current file info
     *
     * @return stdClass
     */
    public function current() {
        if (!isset($this->za)) {
            return false;
        }

        return $this->get_info($this->pos);
    }

    /**
     * Returns the index of current file
     *
     * @return int current file index
     */
    public function key() {
        return $this->pos;
    }

    /**
     * Moves forward to next file
     */
    public function next() {
        $this->pos++;
    }

    /**
     * Rewinds back to the first file
     */
    public function rewind() {
        $this->pos = 0;
    }

    /**
     * Did we reach the end?
     *
     * @return bool
     */
    public function valid() {
        if (!isset($this->za)) {
            return false;
        }

        return ($this->pos < $this->count());
    }

    /**
     * Add unicode flag to all files in archive.
     *
     * NOTE: single disk archives only, no ZIP64 support.
     *
     * @return bool success, modifies the file contents
     */
    protected function fix_utf8_flags() {
        if ($this->encoding !== 'utf-8') {
            return true;
        }

        if (!file_exists($this->archivepathname)) {
            return true;
        }

        // Note: the ZIP structure is described at http://www.pkware.com/documents/casestudies/APPNOTE.TXT
        if (!$fp = fopen($this->archivepathname, 'rb+')) {
            return false;
        }
        if (!$filesize = filesize($this->archivepathname)) {
            return false;
        }

        // Find end of central directory record.
        fseek($fp, $filesize - 22);
        $info = unpack('Vsig', fread($fp, 4));
        if ($info['sig'] === 0x06054b50) {
            // There is no comment.
            fseek($fp, $filesize - 22);
            $data = fread($fp, 22);
        } else {
            // There is some comment with 0xFF max size - that is 65557.
            fseek($fp, $filesize - 65557);
            $data = fread($fp, 65557);
        }

        $pos = strpos($data, pack('V', 0x06054b50));
        if ($pos === false) {
            // Borked ZIP structure!
            fclose($fp);
            return false;
        }
        $centralend = unpack('Vsig/vdisk/vdisk_start/vdisk_entries/ventries/Vsize/Voffset/vcomment_length', substr($data, $pos, 22));

        if ($centralend['disk'] !== 0 or $centralend['disk_start'] !== 0) {
            // Single disk archives only, sorry.
            fclose($fp);
            return false;
        }

        if ($centralend['offset'] === 0xFFFFFFFF) {
            // No support for ZIP64, sorry!
            fclose($fp);
            return false;
        }

        fseek($fp, $centralend['offset']);
        $data = fread($fp, $centralend['size']);
        $pos = 0;
        $files = array();
        for($i=0; $i<$centralend['entries']; $i++) {
            $file = unpack('Vsig/vversion/vversion_req/vgeneral/vmethod/vmtime/vmdate/Vcrc/Vsize_compressed/Vsize/vname_length/vextra_length/vcomment_length/vdisk/vattr/Vattrext/Vlocal_offset', substr($data, $pos, 46));
            $file['central_offset'] = $centralend['offset'] + $pos;
            $pos = $pos + 46;
            if ($file['sig'] !== 0x02014b50) {
                // Borked file!
                fclose($fp);
                return false;
            }
            $file['name'] = substr($data, $pos, $file['name_length']);
            $pos = $pos + $file['name_length'];
            if ($file['extra_length']) {
                $file['extra'] = substr($data, $pos, $file['extra_length']);
                $pos = $pos + $file['extra_length'];
            } else {
                $file['extra'] = '';
            }
            if ($file['comment_length']) {
                $file['comment'] = substr($data, $pos, $file['comment_length']);
                $pos = $pos + $file['comment_length'];
            } else {
                $file['comment'] = '';
            }

            $newgeneral = $file['general'] | pow(2, 11);
            if ($newgeneral === $file['general']) {
                // Nothing to do with this file.
                continue;
            }

            if (preg_match('/^[a-zA-Z0-9_\-\.]*$/', $file['name'])) {
                // ASCII file names are always ok.
                continue;
            }
            if ($file['extra'] !== '') {
                // Most probably not created by php zip ext, better to skip it.
                continue;
            }
            if (fix_utf8($file['name']) !== $file['name']) {
                // Does not look like a valid utf-8 encoded file name, skip it.
                continue;
            }

            // Read local file header.
            fseek($fp, $file['local_offset']);
            $localfile = unpack('Vsig/vversion_req/vgeneral/vmethod/vmtime/vmdate/Vcrc/Vsize_compressed/Vsize/vname_length/vextra_length', fread($fp, 30));
            if ($localfile['sig'] !== 0x04034b50) {
                // Borked file!
                fclose($fp);
                return false;
            }

            $file['local'] = $localfile;
            $files[] = $file;
        }

        foreach ($files as $file) {
            $localfile = $file['local'];
            // Add the unicode flag in central file header.
            fseek($fp, $file['central_offset'] + 8);
            if (ftell($fp) === $file['central_offset'] + 8) {
                $newgeneral = $file['general'] | pow(2, 11);
                fwrite($fp, pack('v', $newgeneral));
            }
            // Modify local file header too.
            fseek($fp, $file['local_offset'] + 6);
            if (ftell($fp) === $file['local_offset'] + 6) {
                $newgeneral = $localfile['general'] | pow(2, 11);
                fwrite($fp, pack('v', $newgeneral));
            }
        }

        fclose($fp);
        return true;
    }
}
