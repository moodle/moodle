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
 * Zip file archive class.
 *
 * @package   core_files
 * @category  files
 * @copyright 2008 Petr Skoda (http://skodak.org)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class zip_archive extends file_archive {

    /** @var string Pathname of archive */
    protected $archivepathname = null;

    /** @var int archive open mode */
    protected $mode = null;

    /** @var int Used memory tracking */
    protected $usedmem = 0;

    /** @var int Iteration position */
    protected $pos = 0;

    /** @var ZipArchive instance */
    protected $za;

    /** @var bool was this archive modified? */
    protected $modified = false;

    /** @var array unicode decoding array, created by decoding zip file */
    protected $namelookup = null;

    /** @var string base64 encoded contents of empty zip file */
    protected static $emptyzipcontent = 'UEsFBgAAAAAAAAAAAAAAAAAAAAAAAA==';

    /** @var bool ugly hack for broken empty zip handling in < PHP 5.3.10 */
    protected $emptyziphack = false;

    /**
     * Create new zip_archive instance.
     */
    public function __construct() {
        $this->encoding = null; // Autodetects encoding by default.
    }

    /**
     * Open or create archive (depending on $mode).
     *
     * @todo MDL-31048 return error message
     * @param string $archivepathname
     * @param int $mode OPEN, CREATE or OVERWRITE constant
     * @param string $encoding archive local paths encoding, empty means autodetect
     * @return bool success
     */
    public function open($archivepathname, $mode=file_archive::CREATE, $encoding=null) {
        $this->close();

        $this->usedmem  = 0;
        $this->pos      = 0;
        $this->encoding = $encoding;
        $this->mode     = $mode;

        $this->za = new ZipArchive();

        switch($mode) {
            case file_archive::OPEN:      $flags = 0; break;
            case file_archive::OVERWRITE: $flags = ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE; break; //changed in PHP 5.2.8
            case file_archive::CREATE:
            default :                     $flags = ZIPARCHIVE::CREATE; break;
        }

        $result = $this->za->open($archivepathname, $flags);

        if ($flags == 0 and $result === ZIPARCHIVE::ER_NOZIP and filesize($archivepathname) === 22) {
            // Legacy PHP versions < 5.3.10 can not deal with empty zip archives.
            if (file_get_contents($archivepathname) === base64_decode(self::$emptyzipcontent)) {
                if ($temp = make_temp_directory('zip')) {
                    $this->emptyziphack = tempnam($temp, 'zip');
                    $this->za = new ZipArchive();
                    $result = $this->za->open($this->emptyziphack, ZIPARCHIVE::CREATE);
                }
            }
        }

        if ($result === true) {
            if (file_exists($archivepathname)) {
                $this->archivepathname = realpath($archivepathname);
            } else {
                $this->archivepathname = $archivepathname;
            }
            return true;

        } else {
            $message = 'Unknown error.';
            switch ($result) {
                case ZIPARCHIVE::ER_EXISTS: $message = 'File already exists.'; break;
                case ZIPARCHIVE::ER_INCONS: $message = 'Zip archive inconsistent.'; break;
                case ZIPARCHIVE::ER_INVAL: $message = 'Invalid argument.'; break;
                case ZIPARCHIVE::ER_MEMORY: $message = 'Malloc failure.'; break;
                case ZIPARCHIVE::ER_NOENT: $message = 'No such file.'; break;
                case ZIPARCHIVE::ER_NOZIP: $message = 'Not a zip archive.'; break;
                case ZIPARCHIVE::ER_OPEN: $message = 'Can\'t open file.'; break;
                case ZIPARCHIVE::ER_READ: $message = 'Read error.'; break;
                case ZIPARCHIVE::ER_SEEK: $message = 'Seek error.'; break;
            }
            debugging($message.': '.$archivepathname, DEBUG_DEVELOPER);
            $this->za = null;
            $this->archivepathname = null;
            return false;
        }
    }

    /**
     * Normalize $localname, always keep in utf-8 encoding.
     *
     * @param string $localname name of file in utf-8 encoding
     * @return string normalised compressed file or directory name
     */
    protected function mangle_pathname($localname) {
        $result = str_replace('\\', '/', $localname);   // no MS \ separators
        $result = preg_replace('/\.\.+/', '', $result); // prevent /.../
        $result = ltrim($result, '/');                  // no leading slash

        if ($result === '.') {
            $result = '';
        }

        return $result;
    }

    /**
     * Tries to convert $localname into utf-8
     * please note that it may fail really badly.
     * The resulting file name is cleaned.
     *
     * @param string $localname name (encoding is read from zip file or guessed)
     * @return string in utf-8
     */
    protected function unmangle_pathname($localname) {
        $this->init_namelookup();

        if (!isset($this->namelookup[$localname])) {
            $name = $localname;
            // This should not happen.
            if (!empty($this->encoding) and $this->encoding !== 'utf-8') {
                $name = @core_text::convert($name, $this->encoding, 'utf-8');
            }
            $name = str_replace('\\', '/', $name);   // no MS \ separators
            $name = clean_param($name, PARAM_PATH);  // only safe chars
            return ltrim($name, '/');                // no leading slash
        }

        return $this->namelookup[$localname];
    }

    /**
     * Close archive, write changes to disk.
     *
     * @return bool success
     */
    public function close() {
        if (!isset($this->za)) {
            return false;
        }

        if ($this->emptyziphack) {
            @$this->za->close();
            $this->za = null;
            $this->mode = null;
            $this->namelookup = null;
            $this->modified = false;
            @unlink($this->emptyziphack);
            $this->emptyziphack = false;
            return true;

        } else if ($this->za->numFiles == 0) {
            // PHP can not create empty archives, so let's fake it.
            @$this->za->close();
            $this->za = null;
            $this->mode = null;
            $this->namelookup = null;
            $this->modified = false;
            // If the existing archive is already empty, we didn't change it.  Don't bother completing a save.
            // This is important when we are inspecting archives that we might not have write permission to.
            if (@filesize($this->archivepathname) == 22 &&
                    @file_get_contents($this->archivepathname) === base64_decode(self::$emptyzipcontent)) {
                return true;
            }
            @unlink($this->archivepathname);
            $data = base64_decode(self::$emptyzipcontent);
            if (!file_put_contents($this->archivepathname, $data)) {
                return false;
            }
            return true;
        }

        $res = $this->za->close();
        $this->za = null;
        $this->mode = null;
        $this->namelookup = null;

        if ($this->modified) {
            $this->fix_utf8_flags();
            $this->modified = false;
        }

        return $res;
    }

    /**
     * Returns file stream for reading of content.
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
     * Returns file information.
     *
     * @param int $index index of file
     * @return stdClass|bool info object or false if error
     */
    public function get_info($index) {
        if (!isset($this->za)) {
            return false;
        }

        // Need to use the ZipArchive's numfiles, as $this->count() relies on this function to count actual files (skipping OSX junk).
        if ($index < 0 or $index >=$this->za->numFiles) {
            return false;
        }

        // PHP 5.6 introduced encoding guessing logic, we need to fall back
        // to raw ZIP_FL_ENC_RAW (== 64) to get consistent results as in PHP 5.5.
        $result = $this->za->statIndex($index, 64);

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

        if ($this->is_system_file($info)) {
            // Don't return system files.
            return false;
        }

        return $info;
    }

    /**
     * Returns array of info about all files in archive.
     *
     * @return array of file infos
     */
    public function list_files() {
        if (!isset($this->za)) {
            return false;
        }

        $infos = array();

        foreach ($this as $info) {
            // Simply iterating over $this will give us info only for files we're interested in.
            array_push($infos, $info);
        }

        return $infos;
    }

    public function is_system_file($fileinfo) {
        if (substr($fileinfo->pathname, 0, 8) === '__MACOSX' or substr($fileinfo->pathname, -9) === '.DS_Store') {
            // Mac OSX system files.
            return true;
        }
        if (substr($fileinfo->pathname, -9) === 'Thumbs.db') {
            $stream = $this->za->getStream($fileinfo->pathname);
            $info = base64_encode(fread($stream, 8));
            fclose($stream);
            if ($info === '0M8R4KGxGuE=') {
                // It's an OLE Compound File - so it's almost certainly a Windows thumbnail cache.
                return true;
            }
        }
        return false;
    }

    /**
     * Returns number of files in archive.
     *
     * @return int number of files
     */
    public function count() {
        if (!isset($this->za)) {
            return false;
        }

        return count($this->list_files());
    }

    /**
     * Returns approximate number of files in archive. This may be a slight
     * overestimate.
     *
     * @return int|bool Estimated number of files, or false if not opened
     */
    public function estimated_count() {
        if (!isset($this->za)) {
            return false;
        }

        return $this->za->numFiles;
    }

    /**
     * Add file into archive.
     *
     * @param string $localname name of file in archive
     * @param string $pathname location of file
     * @return bool success
     */
    public function add_file_from_pathname($localname, $pathname) {
        if ($this->emptyziphack) {
            $this->close();
            $this->open($this->archivepathname, file_archive::OVERWRITE, $this->encoding);
        }

        if (!isset($this->za)) {
            return false;
        }

        if ($this->archivepathname === realpath($pathname)) {
            // Do not add self into archive.
            return false;
        }

        if (!is_readable($pathname) or is_dir($pathname)) {
            return false;
        }

        if (is_null($localname)) {
            $localname = clean_param($pathname, PARAM_PATH);
        }
        $localname = trim($localname, '/'); // No leading slashes in archives!
        $localname = $this->mangle_pathname($localname);

        if ($localname === '') {
            // Sorry - conversion failed badly.
            return false;
        }

        if (!$this->za->addFile($pathname, $localname)) {
            return false;
        }
        $this->modified = true;
        return true;
    }

    /**
     * Add content of string into archive.
     *
     * @param string $localname name of file in archive
     * @param string $contents contents
     * @return bool success
     */
    public function add_file_from_string($localname, $contents) {
        if ($this->emptyziphack) {
            $this->close();
            $this->open($this->archivepathname, file_archive::OVERWRITE, $this->encoding);
        }

        if (!isset($this->za)) {
            return false;
        }

        $localname = trim($localname, '/'); // No leading slashes in archives!
        $localname = $this->mangle_pathname($localname);

        if ($localname === '') {
            // Sorry - conversion failed badly.
            return false;
        }

        if ($this->usedmem > 2097151) {
            // This prevents running out of memory when adding many large files using strings.
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
     * Add empty directory into archive.
     *
     * @param string $localname name of file in archive
     * @return bool success
     */
    public function add_directory($localname) {
        if ($this->emptyziphack) {
            $this->close();
            $this->open($this->archivepathname, file_archive::OVERWRITE, $this->encoding);
        }

        if (!isset($this->za)) {
            return false;
        }
        $localname = trim($localname, '/'). '/';
        $localname = $this->mangle_pathname($localname);

        if ($localname === '/') {
            // Sorry - conversion failed badly.
            return false;
        }

        if ($localname !== '') {
            if (!$this->za->addEmptyDir($localname)) {
                return false;
            }
            $this->modified = true;
        }
        return true;
    }

    /**
     * Returns current file info.
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
     * Returns the index of current file.
     *
     * @return int current file index
     */
    public function key() {
        return $this->pos;
    }

    /**
     * Moves forward to next file.
     */
    public function next() {
        $this->pos++;
    }

    /**
     * Rewinds back to the first file.
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

        // Skip over unwanted system files (get_info will return false).
        while (!$this->get_info($this->pos) && $this->pos < $this->za->numFiles) {
            $this->next();
        }

        // No files left - we're at the end.
        if ($this->pos >= $this->za->numFiles) {
            return false;
        }

        return true;
    }

    /**
     * Create a map of file names used in zip archive.
     * @return void
     */
    protected function init_namelookup() {
        if ($this->emptyziphack) {
            $this->namelookup = array();
            return;
        }

        if (!isset($this->za)) {
            return;
        }
        if (isset($this->namelookup)) {
            return;
        }

        $this->namelookup = array();

        if ($this->mode != file_archive::OPEN) {
            // No need to tweak existing names when creating zip file because there are none yet!
            return;
        }

        if (!file_exists($this->archivepathname)) {
            return;
        }

        if (!$fp = fopen($this->archivepathname, 'rb')) {
            return;
        }
        if (!$filesize = filesize($this->archivepathname)) {
            return;
        }

        $centralend = self::zip_get_central_end($fp, $filesize);

        if ($centralend === false or $centralend['disk'] !== 0 or $centralend['disk_start'] !== 0 or $centralend['offset'] === 0xFFFFFFFF) {
            // Single disk archives only and o support for ZIP64, sorry.
            fclose($fp);
            return;
        }

        fseek($fp, $centralend['offset']);
        $data = fread($fp, $centralend['size']);
        $pos = 0;
        $files = array();
        for($i=0; $i<$centralend['entries']; $i++) {
            $file = self::zip_parse_file_header($data, $centralend, $pos);
            if ($file === false) {
                // Wrong header, sorry.
                fclose($fp);
                return;
            }
            $files[] = $file;
        }
        fclose($fp);

        foreach ($files as $file) {
            $name = $file['name'];
            if (preg_match('/^[a-zA-Z0-9_\-\.]*$/', $file['name'])) {
                // No need to fix ASCII.
                $name = fix_utf8($name);

            } else if (!($file['general'] & pow(2, 11))) {
                // First look for unicode name alternatives.
                $found = false;
                foreach($file['extra'] as $extra) {
                    if ($extra['id'] === 0x7075) {
                        $data = unpack('cversion/Vcrc', substr($extra['data'], 0, 5));
                        if ($data['crc'] === crc32($name)) {
                            $found = true;
                            $name = substr($extra['data'], 5);
                        }
                    }
                }
                if (!$found and !empty($this->encoding) and $this->encoding !== 'utf-8') {
                    // Try the encoding from open().
                    $newname = @core_text::convert($name, $this->encoding, 'utf-8');
                    $original  = core_text::convert($newname, 'utf-8', $this->encoding);
                    if ($original === $name) {
                        $found = true;
                        $name = $newname;
                    }
                }
                if (!$found and $file['version'] === 0x315) {
                    // This looks like OS X build in zipper.
                    $newname = fix_utf8($name);
                    if ($newname === $name) {
                        $found = true;
                        $name = $newname;
                    }
                }
                if (!$found and $file['version'] === 0) {
                    // This looks like our old borked Moodle 2.2 file.
                    $newname = fix_utf8($name);
                    if ($newname === $name) {
                        $found = true;
                        $name = $newname;
                    }
                }
                if (!$found and $encoding = get_string('oldcharset', 'langconfig')) {
                    // Last attempt - try the dos/unix encoding from current language.
                    $windows = true;
                    foreach($file['extra'] as $extra) {
                        // In Windows archivers do not usually set any extras with the exception of NTFS flag in WinZip/WinRar.
                        $windows = false;
                        if ($extra['id'] === 0x000a) {
                            $windows = true;
                            break;
                        }
                    }

                    if ($windows === true) {
                        switch(strtoupper($encoding)) {
                            case 'ISO-8859-1': $encoding = 'CP850'; break;
                            case 'ISO-8859-2': $encoding = 'CP852'; break;
                            case 'ISO-8859-4': $encoding = 'CP775'; break;
                            case 'ISO-8859-5': $encoding = 'CP866'; break;
                            case 'ISO-8859-6': $encoding = 'CP720'; break;
                            case 'ISO-8859-7': $encoding = 'CP737'; break;
                            case 'ISO-8859-8': $encoding = 'CP862'; break;
                            case 'WINDOWS-1251': $encoding = 'CP866'; break;
                            case 'EUC-JP':
                            case 'UTF-8':
                                if ($winchar = get_string('localewincharset', 'langconfig')) {
                                    // Most probably works only for zh_cn,
                                    // if there are more problems we could add zipcharset to langconfig files.
                                    $encoding = $winchar;
                                }
                                break;
                        }
                    }
                    $newname = @core_text::convert($name, $encoding, 'utf-8');
                    $original  = core_text::convert($newname, 'utf-8', $encoding);

                    if ($original === $name) {
                        $name = $newname;
                    }
                }
            }
            $name = str_replace('\\', '/', $name);  // no MS \ separators
            $name = clean_param($name, PARAM_PATH); // only safe chars
            $name = ltrim($name, '/');              // no leading slash

            if (function_exists('normalizer_normalize')) {
                $name = normalizer_normalize($name, Normalizer::FORM_C);
            }

            $this->namelookup[$file['name']] = $name;
        }
    }

    /**
     * Add unicode flag to all files in archive.
     *
     * NOTE: single disk archives only, no ZIP64 support.
     *
     * @return bool success, modifies the file contents
     */
    protected function fix_utf8_flags() {
        if ($this->emptyziphack) {
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

        $centralend = self::zip_get_central_end($fp, $filesize);

        if ($centralend === false or $centralend['disk'] !== 0 or $centralend['disk_start'] !== 0 or $centralend['offset'] === 0xFFFFFFFF) {
            // Single disk archives only and o support for ZIP64, sorry.
            fclose($fp);
            return false;
        }

        fseek($fp, $centralend['offset']);
        $data = fread($fp, $centralend['size']);
        $pos = 0;
        $files = array();
        for($i=0; $i<$centralend['entries']; $i++) {
            $file = self::zip_parse_file_header($data, $centralend, $pos);
            if ($file === false) {
                // Wrong header, sorry.
                fclose($fp);
                return false;
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
            if ($file['extra']) {
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

    /**
     * Read end of central signature of ZIP file.
     * @internal
     * @static
     * @param resource $fp
     * @param int $filesize
     * @return array|bool
     */
    public static function zip_get_central_end($fp, $filesize) {
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
            return false;
        }
        $centralend = unpack('Vsig/vdisk/vdisk_start/vdisk_entries/ventries/Vsize/Voffset/vcomment_length', substr($data, $pos, 22));
        if ($centralend['comment_length']) {
            $centralend['comment'] = substr($data, 22, $centralend['comment_length']);
        } else {
            $centralend['comment'] = '';
        }

        return $centralend;
    }

    /**
     * Parse file header.
     * @internal
     * @param string $data
     * @param array $centralend
     * @param int $pos (modified)
     * @return array|bool file info
     */
    public static function zip_parse_file_header($data, $centralend, &$pos) {
        $file = unpack('Vsig/vversion/vversion_req/vgeneral/vmethod/Vmodified/Vcrc/Vsize_compressed/Vsize/vname_length/vextra_length/vcomment_length/vdisk/vattr/Vattrext/Vlocal_offset', substr($data, $pos, 46));
        $file['central_offset'] = $centralend['offset'] + $pos;
        $pos = $pos + 46;
        if ($file['sig'] !== 0x02014b50) {
            // Borked ZIP structure!
            return false;
        }
        $file['name'] = substr($data, $pos, $file['name_length']);
        $pos = $pos + $file['name_length'];
        $file['extra'] = array();
        $file['extra_data'] = '';
        if ($file['extra_length']) {
            $extradata = substr($data, $pos, $file['extra_length']);
            $file['extra_data'] = $extradata;
            while (strlen($extradata) > 4) {
                $extra = unpack('vid/vsize', substr($extradata, 0, 4));
                $extra['data'] = substr($extradata, 4, $extra['size']);
                $extradata = substr($extradata, 4+$extra['size']);
                $file['extra'][] = $extra;
            }
            $pos = $pos + $file['extra_length'];
        }
        if ($file['comment_length']) {
            $pos = $pos + $file['comment_length'];
            $file['comment'] = substr($data, $pos, $file['comment_length']);
        } else {
            $file['comment'] = '';
        }
        return $file;
    }
}
