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
 * Implementation of .tar.gz packer.
 *
 * A limited subset of the .tar format is supported. This packer can open files
 * that it wrote, but may not be able to open files from other sources,
 * especially if they use extensions. There are restrictions on file
 * length and character set of filenames.
 *
 * We generate POSIX-compliant ustar files. As a result, the following
 * restrictions apply to archive paths:
 *
 * - Filename may not be more than 100 characters.
 * - Total of path + filename may not be more than 256 characters.
 * - For path more than 155 characters it may or may not work.
 * - May not contain non-ASCII characters.
 *
 * @package core_files
 * @copyright 2013 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/filestorage/file_packer.php");
require_once("$CFG->libdir/filestorage/tgz_extractor.php");

/**
 * Utility class - handles all packing/unpacking of .tar.gz files.
 *
 * @package core_files
 * @category files
 * @copyright 2013 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tgz_packer extends file_packer {
    /**
     * @var int Default timestamp used where unknown (Jan 1st 2013 00:00)
     */
    const DEFAULT_TIMESTAMP = 1356998400;

    /**
     * @var string Name of special archive index file added by Moodle.
     */
    const ARCHIVE_INDEX_FILE = '.ARCHIVE_INDEX';

    /**
     * @var string Required text at start of archive index file before file count.
     */
    const ARCHIVE_INDEX_COUNT_PREFIX = 'Moodle archive file index. Count: ';

    /**
     * @var bool If true, includes .ARCHIVE_INDEX file in root of tar file.
     */
    protected $includeindex = true;

    /**
     * @var int Max value for total progress.
     */
    const PROGRESS_MAX = 1000000;

    /**
     * @var int Tar files have a fixed block size of 512 bytes.
     */
    const TAR_BLOCK_SIZE = 512;

    /**
     * Archive files and store the result in file storage.
     *
     * Any existing file at that location will be overwritten.
     *
     * @param array $files array from archive path => pathname or stored_file
     * @param int $contextid context ID
     * @param string $component component
     * @param string $filearea file area
     * @param int $itemid item ID
     * @param string $filepath file path
     * @param string $filename file name
     * @param int $userid user ID
     * @param bool $ignoreinvalidfiles true means ignore missing or invalid files, false means abort on any error
     * @param file_progress $progress Progress indicator callback or null if not required
     * @return stored_file|bool false if error stored_file instance if ok
     * @throws file_exception If file operations fail
     * @throws coding_exception If any archive paths do not meet the restrictions
     */
    public function archive_to_storage(array $files, $contextid,
            $component, $filearea, $itemid, $filepath, $filename,
            $userid = null, $ignoreinvalidfiles = true, ?file_progress $progress = null) {
        global $CFG;

        // Set up a temporary location for the file.
        $tempfolder = $CFG->tempdir . '/core_files';
        check_dir_exists($tempfolder);
        $tempfile = tempnam($tempfolder, '.tgz');

        // Archive to the given path.
        if ($result = $this->archive_to_pathname($files, $tempfile, $ignoreinvalidfiles, $progress)) {
            // If there is an existing file, delete it.
            $fs = get_file_storage();
            if ($existing = $fs->get_file($contextid, $component, $filearea, $itemid, $filepath, $filename)) {
                $existing->delete();
            }
            $filerecord = array('contextid' => $contextid, 'component' => $component,
                    'filearea' => $filearea, 'itemid' => $itemid, 'filepath' => $filepath,
                    'filename' => $filename, 'userid' => $userid, 'mimetype' => 'application/x-tgz');
            self::delete_existing_file_record($fs, $filerecord);
            $result = $fs->create_file_from_pathname($filerecord, $tempfile);
        }

        // Delete the temporary file (if created) and return.
        @unlink($tempfile);
        return $result;
    }

    /**
     * Wrapper function useful for deleting an existing file (if present) just
     * before creating a new one.
     *
     * @param file_storage $fs File storage
     * @param array $filerecord File record in same format used to create file
     */
    public static function delete_existing_file_record(file_storage $fs, array $filerecord) {
        if ($existing = $fs->get_file($filerecord['contextid'], $filerecord['component'],
                $filerecord['filearea'], $filerecord['itemid'], $filerecord['filepath'],
                $filerecord['filename'])) {
            $existing->delete();
        }
    }

    /**
     * By default, the .tar file includes a .ARCHIVE_INDEX file as its first
     * entry. This makes list_files much faster and allows for better progress
     * reporting.
     *
     * If you need to disable the inclusion of this file, use this function
     * before calling one of the archive_xx functions.
     *
     * @param bool $includeindex If true, includes index
     */
    public function set_include_index($includeindex) {
        $this->includeindex = $includeindex;
    }

    /**
     * Archive files and store the result in an OS file.
     *
     * @param array $files array from archive path => pathname or stored_file
     * @param string $archivefile path to target zip file
     * @param bool $ignoreinvalidfiles true means ignore missing or invalid files, false means abort on any error
     * @param file_progress $progress Progress indicator callback or null if not required
     * @return bool true if file created, false if not
     * @throws coding_exception If any archive paths do not meet the restrictions
     */
    public function archive_to_pathname(array $files, $archivefile,
            $ignoreinvalidfiles=true, ?file_progress $progress = null) {
        // Open .gz file.
        if (!($gz = gzopen($archivefile, 'wb'))) {
            return false;
        }
        try {
            // Because we update how we calculate progress after we already
            // analyse the directory list, we can't just use a number of files
            // as progress. Instead, progress always goes to PROGRESS_MAX
            // and we do estimates as a proportion of that. To begin with,
            // assume that counting files will be 10% of the work, so allocate
            // one-tenth of PROGRESS_MAX to the total of all files.
            if ($files) {
                $progressperfile = (int)(self::PROGRESS_MAX / (count($files) * 10));
            } else {
                // If there are no files, avoid divide by zero.
                $progressperfile = 1;
            }
            $done = 0;

            // Expand the provided files into a complete list of single files.
            $expandedfiles = array();
            foreach ($files as $archivepath => $file) {
                // Update progress if required.
                if ($progress) {
                    $progress->progress($done, self::PROGRESS_MAX);
                }
                $done += $progressperfile;

                if (is_null($file)) {
                    // Empty directory record. Ensure it ends in a /.
                    if (!preg_match('~/$~', $archivepath)) {
                        $archivepath .= '/';
                    }
                    $expandedfiles[$archivepath] = null;
                } else if (is_string($file)) {
                    // File specified as path on disk.
                    if (!$this->list_files_path($expandedfiles, $archivepath, $file,
                            $progress, $done)) {
                        gzclose($gz);
                        unlink($archivefile);
                        return false;
                    }
                } else if (is_array($file)) {
                    // File specified as raw content in array.
                    $expandedfiles[$archivepath] = $file;
                } else {
                    // File specified as stored_file object.
                    $this->list_files_stored($expandedfiles, $archivepath, $file);
                }
            }

            // Store the list of files as a special file that is first in the
            // archive. This contains enough information to implement list_files
            // if required later.
            $list = self::ARCHIVE_INDEX_COUNT_PREFIX . count($expandedfiles) . "\n";
            $sizes = array();
            $mtimes = array();
            foreach ($expandedfiles as $archivepath => $file) {
                // Check archivepath doesn't contain any non-ASCII characters.
                if (!preg_match('~^[\x00-\xff]*$~', $archivepath)) {
                    throw new coding_exception(
                            'Non-ASCII paths not supported: ' . $archivepath);
                }

                // Build up the details.
                $type = 'f';
                $mtime = '?';
                if (is_null($file)) {
                    $type = 'd';
                    $size = 0;
                } else if (is_string($file)) {
                    $stat = stat($file);
                    $mtime = (int)$stat['mtime'];
                    $size = (int)$stat['size'];
                } else if (is_array($file)) {
                    $size = (int)strlen(reset($file));
                } else {
                    $mtime = (int)$file->get_timemodified();
                    $size = (int)$file->get_filesize();
                }
                $sizes[$archivepath] = $size;
                $mtimes[$archivepath] = $mtime;

                // Write a line in the index.
                $list .= "$archivepath\t$type\t$size\t$mtime\n";
            }

            // The index file is optional; only write into archive if needed.
            if ($this->includeindex) {
                // Put the index file into the archive.
                $this->write_tar_entry($gz, self::ARCHIVE_INDEX_FILE, null, strlen($list), '?', $list);
            }

            // Update progress ready for main stage.
            $done = (int)(self::PROGRESS_MAX / 10);
            if ($progress) {
                $progress->progress($done, self::PROGRESS_MAX);
            }
            if ($expandedfiles) {
                // The remaining 9/10ths of progress represents these files.
                $progressperfile = (int)((9 * self::PROGRESS_MAX) / (10 * count($expandedfiles)));
            } else {
                $progressperfile = 1;
            }

            // Actually write entries for each file/directory.
            foreach ($expandedfiles as $archivepath => $file) {
                if (is_null($file)) {
                    // Null entry indicates a directory.
                    $this->write_tar_entry($gz, $archivepath, null,
                            $sizes[$archivepath], $mtimes[$archivepath]);
                } else if (is_string($file)) {
                    // String indicates an OS file.
                    $this->write_tar_entry($gz, $archivepath, $file,
                            $sizes[$archivepath], $mtimes[$archivepath], null, $progress, $done);
                } else if (is_array($file)) {
                    // Array indicates in-memory data.
                    $data = reset($file);
                    $this->write_tar_entry($gz, $archivepath, null,
                            $sizes[$archivepath], $mtimes[$archivepath], $data, $progress, $done);
                } else {
                    // Stored_file object.
                    $this->write_tar_entry($gz, $archivepath, $file->get_content_file_handle(),
                            $sizes[$archivepath], $mtimes[$archivepath], null, $progress, $done);
                }
                $done += $progressperfile;
                if ($progress) {
                    $progress->progress($done, self::PROGRESS_MAX);
                }
            }

            // Finish tar file with two empty 512-byte records.
            gzwrite($gz, str_pad('', 2 * self::TAR_BLOCK_SIZE, "\x00"));
            gzclose($gz);
            return true;
        } catch (Exception $e) {
            // If there is an exception, delete the in-progress file.
            gzclose($gz);
            unlink($archivefile);
            throw $e;
        }
    }

    /**
     * Writes a single tar file to the archive, including its header record and
     * then the file contents.
     *
     * @param resource $gz Gzip file
     * @param string $archivepath Full path of file within archive
     * @param string|resource $file Full path of file on disk or file handle or null if none
     * @param int $size Size or 0 for directories
     * @param int|string $mtime Time or ? if unknown
     * @param string $content Actual content of file to write (null if using $filepath)
     * @param file_progress $progress Progress indicator or null if none
     * @param int $done Value for progress indicator
     * @return bool True if OK
     * @throws coding_exception If names aren't valid
     */
    protected function write_tar_entry($gz, $archivepath, $file, $size, $mtime, $content = null,
            ?file_progress $progress = null, $done = 0) {
        // Header based on documentation of POSIX ustar format from:
        // http://www.freebsd.org/cgi/man.cgi?query=tar&sektion=5&manpath=FreeBSD+8-current .

        // For directories, ensure name ends in a slash.
        $directory = false;
        if ($size === 0 && is_null($file)) {
            $directory = true;
            if (!preg_match('~/$~', $archivepath)) {
                $archivepath .= '/';
            }
            $mode = '755';
        } else {
            $mode = '644';
        }

        // Split archivepath into name and prefix.
        $name = $archivepath;
        $prefix = '';
        while (strlen($name) > 100) {
            $slash = strpos($name, '/');
            if ($slash === false) {
                throw new coding_exception(
                        'Name cannot fit length restrictions (> 100 characters): ' . $archivepath);
            }

            if ($prefix !== '') {
                $prefix .= '/';
            }
            $prefix .= substr($name, 0, $slash);
            $name = substr($name, $slash + 1);
            if (strlen($prefix) > 155) {
                throw new coding_exception(
                        'Name cannot fit length restrictions (path too long): ' . $archivepath);
            }
        }

        // Checksum performance is a bit slow because of having to call 'ord'
        // lots of times (it takes about 1/3 the time of the actual gzwrite
        // call). To improve performance of checksum calculation, we will
        // store all the non-zero, non-fixed bytes that need adding to the
        // checksum, and checksum only those bytes.
        $forchecksum = $name;

        // struct header_posix_ustar {
        //    char name[100];
        $header = str_pad($name, 100, "\x00");

        //    char mode[8];
        //    char uid[8];
        //    char gid[8];
        $header .= '0000' . $mode . "\x000000000\x000000000\x00";
        $forchecksum .= $mode;

        //    char size[12];
        $octalsize = decoct($size);
        if (strlen($octalsize) > 11) {
            throw new coding_exception(
                    'File too large for .tar file: ' . $archivepath . ' (' . $size . ' bytes)');
        }
        $paddedsize = str_pad($octalsize, 11, '0', STR_PAD_LEFT);
        $forchecksum .= $paddedsize;
        $header .= $paddedsize . "\x00";

        //    char mtime[12];
        if ($mtime === '?') {
            // Use a default timestamp rather than zero; GNU tar outputs
            // warnings about zeroes here.
            $mtime = self::DEFAULT_TIMESTAMP;
        }
        $octaltime = decoct($mtime);
        $paddedtime = str_pad($octaltime, 11, '0', STR_PAD_LEFT);
        $forchecksum .= $paddedtime;
        $header .= $paddedtime . "\x00";

        //    char checksum[8];
        // Checksum needs to be completed later.
        $header .= '        ';

        //    char typeflag[1];
        $typeflag = $directory ? '5' : '0';
        $forchecksum .= $typeflag;
        $header .= $typeflag;

        //    char linkname[100];
        $header .= str_pad('', 100, "\x00");

        //    char magic[6];
        //    char version[2];
        $header .= "ustar\x0000";

        //    char uname[32];
        //    char gname[32];
        //    char devmajor[8];
        //    char devminor[8];
        $header .= str_pad('', 80, "\x00");

        //    char prefix[155];
        //    char pad[12];
        $header .= str_pad($prefix, 167, "\x00");
        $forchecksum .= $prefix;

        // };

        // We have now calculated the header, but without the checksum. To work
        // out the checksum, sum all the bytes that aren't fixed or zero, and add
        // to a standard value that contains all the fixed bytes.

        // The fixed non-zero bytes are:
        //
        // '000000000000000000        ustar00'
        // mode (except 3 digits), uid, gid, checksum space, magic number, version
        //
        // To calculate the number, call the calculate_checksum function on the
        // above string. The result is 1775.
        $checksum = 1775 + self::calculate_checksum($forchecksum);

        $octalchecksum = str_pad(decoct($checksum), 6, '0', STR_PAD_LEFT) . "\x00 ";

        // Slot it into place in the header.
        $header = substr($header, 0, 148) . $octalchecksum . substr($header, 156);

        if (strlen($header) != self::TAR_BLOCK_SIZE) {
            throw new coding_exception('Header block wrong size!!!!!');
        }

        // Awesome, now write out the header.
        gzwrite($gz, $header);

        // Special pre-handler for OS filename.
        if (is_string($file)) {
            $file = fopen($file, 'rb');
            if (!$file) {
                return false;
            }
        }

        if ($content !== null) {
            // Write in-memory content if any.
            if (strlen($content) !== $size) {
                throw new coding_exception('Mismatch between provided sizes: ' . $archivepath);
            }
            gzwrite($gz, $content);
        } else if ($file !== null) {
            // Write file content if any, using a 64KB buffer.
            $written = 0;
            $chunks = 0;
            while (true) {
                $data = fread($file, 65536);
                if ($data === false || strlen($data) == 0) {
                    break;
                }
                $written += gzwrite($gz, $data);

                // After every megabyte of large files, update the progress
                // tracker (so there are no long gaps without progress).
                $chunks++;
                if ($chunks == 16) {
                    $chunks = 0;
                    if ($progress) {
                        // This call always has the same values, but that gives
                        // the tracker a chance to indicate indeterminate
                        // progress and output something to avoid timeouts.
                        $progress->progress($done, self::PROGRESS_MAX);
                    }
                }
            }
            fclose($file);

            if ($written !== $size) {
                throw new coding_exception('Mismatch between provided sizes: ' . $archivepath .
                        ' (was ' . $written . ', expected ' . $size . ')');
            }
        } else if ($size != 0) {
            throw new coding_exception('Missing data file handle for non-empty file');
        }

        // Pad out final 512-byte block in file, if applicable.
        $leftover = self::TAR_BLOCK_SIZE - ($size % self::TAR_BLOCK_SIZE);
        if ($leftover == 512) {
            $leftover = 0;
        } else {
            gzwrite($gz, str_pad('', $leftover, "\x00"));
        }

        return true;
    }

    /**
     * Calculates a checksum by summing all characters of the binary string
     * (treating them as unsigned numbers).
     *
     * @param string $str Input string
     * @return int Checksum
     */
    protected static function calculate_checksum($str) {
        $checksum = 0;
        $checklength = strlen($str);
        for ($i = 0; $i < $checklength; $i++) {
            $checksum += ord($str[$i]);
        }
        return $checksum;
    }

    /**
     * Based on an OS path, adds either that path (if it's a file) or
     * all its children (if it's a directory) into the list of files to
     * archive.
     *
     * If a progress indicator is supplied and if this corresponds to a
     * directory, then it will be repeatedly called with the same values. This
     * allows the progress handler to respond in some way to avoid timeouts
     * if required.
     *
     * @param array $expandedfiles List of all files to archive (output)
     * @param string $archivepath Current path within archive
     * @param string $path OS path on disk
     * @param file_progress|null $progress Progress indicator or null if none
     * @param int $done Value for progress indicator
     * @return bool True if successful
     */
    protected function list_files_path(array &$expandedfiles, $archivepath, $path,
            ?file_progress $progress , $done) {
        if (is_dir($path)) {
            // Unless we're using this directory as archive root, add a
            // directory entry.
            if ($archivepath != '') {
                // Add directory-creation record.
                $expandedfiles[$archivepath . '/'] = null;
            }

            // Loop through directory contents and recurse.
            if (!$handle = opendir($path)) {
                return false;
            }
            while (false !== ($entry = readdir($handle))) {
                if ($entry === '.' || $entry === '..') {
                    continue;
                }
                $result = $this->list_files_path($expandedfiles,
                        $archivepath . '/' . $entry, $path . '/' . $entry,
                        $progress, $done);
                if (!$result) {
                    return false;
                }
                if ($progress) {
                    $progress->progress($done, self::PROGRESS_MAX);
                }
            }
            closedir($handle);
        } else {
            // Just add it to list.
            $expandedfiles[$archivepath] = $path;
        }
        return true;
    }

    /**
     * Based on a stored_file objects, adds either that file (if it's a file) or
     * all its children (if it's a directory) into the list of files to
     * archive.
     *
     * If a progress indicator is supplied and if this corresponds to a
     * directory, then it will be repeatedly called with the same values. This
     * allows the progress handler to respond in some way to avoid timeouts
     * if required.
     *
     * @param array $expandedfiles List of all files to archive (output)
     * @param string $archivepath Current path within archive
     * @param stored_file $file File object
     */
    protected function list_files_stored(array &$expandedfiles, $archivepath, stored_file $file) {
        if ($file->is_directory()) {
            // Add a directory-creation record.
            $expandedfiles[$archivepath . '/'] = null;

            // Loop through directory contents (this is a recursive collection
            // of all children not just one directory).
            $fs = get_file_storage();
            $baselength = strlen($file->get_filepath());
            $files = $fs->get_directory_files(
                    $file->get_contextid(), $file->get_component(), $file->get_filearea(), $file->get_itemid(),
                    $file->get_filepath(), true, true);
            foreach ($files as $childfile) {
                // Get full pathname after original part.
                $path = $childfile->get_filepath();
                $path = substr($path, $baselength);
                $path = $archivepath . '/' . $path;
                if ($childfile->is_directory()) {
                    $childfile = null;
                } else {
                    $path .= $childfile->get_filename();
                }
                $expandedfiles[$path] = $childfile;
            }
        } else {
            // Just add it to list.
            $expandedfiles[$archivepath] = $file;
        }
    }

    /**
     * Extract file to given file path (real OS filesystem), existing files are overwritten.
     *
     * @param stored_file|string $archivefile full pathname of zip file or stored_file instance
     * @param string $pathname target directory
     * @param array $onlyfiles only extract files present in the array
     * @param file_progress $progress Progress indicator callback or null if not required
     * @param bool $returnbool Whether to return a basic true/false indicating error state, or full per-file error
     * details.
     * @return array list of processed files (name=>true)
     * @throws moodle_exception If error
     */
    public function extract_to_pathname($archivefile, $pathname,
            ?array $onlyfiles = null, ?file_progress $progress = null, $returnbool = false) {
        $extractor = new tgz_extractor($archivefile);
        try {
            $result = $extractor->extract(
                    new tgz_packer_extract_to_pathname($pathname, $onlyfiles), $progress);
            if ($returnbool) {
                if (!is_array($result)) {
                    return false;
                }
                foreach ($result as $status) {
                    if ($status !== true) {
                        return false;
                    }
                }
                return true;
            } else {
                return $result;
            }
        } catch (moodle_exception $e) {
            if ($returnbool) {
                return false;
            } else {
                throw $e;
            }
        }
    }

    /**
     * Extract file to given file path (real OS filesystem), existing files are overwritten.
     *
     * @param string|stored_file $archivefile full pathname of zip file or stored_file instance
     * @param int $contextid context ID
     * @param string $component component
     * @param string $filearea file area
     * @param int $itemid item ID
     * @param string $pathbase file path
     * @param int $userid user ID
     * @param file_progress $progress Progress indicator callback or null if not required
     * @return array list of processed files (name=>true)
     * @throws moodle_exception If error
     */
    public function extract_to_storage($archivefile, $contextid,
            $component, $filearea, $itemid, $pathbase, $userid = null,
            ?file_progress $progress = null) {
        $extractor = new tgz_extractor($archivefile);
        return $extractor->extract(
                new tgz_packer_extract_to_storage($contextid, $component,
                    $filearea, $itemid, $pathbase, $userid), $progress);
    }

    /**
     * Returns array of info about all files in archive.
     *
     * @param string|stored_file $archivefile
     * @return array of file infos
     */
    public function list_files($archivefile) {
        $extractor = new tgz_extractor($archivefile);
        return $extractor->list_files();
    }

    /**
     * Checks whether a file appears to be a .tar.gz file.
     *
     * @param string|stored_file $archivefile
     * @return bool True if file contains the gzip magic number
     */
    public static function is_tgz_file($archivefile) {
        if (is_a($archivefile, 'stored_file')) {
            $fp = $archivefile->get_content_file_handle();
        } else {
            $fp = fopen($archivefile, 'rb');
        }
        $firstbytes = fread($fp, 2);
        fclose($fp);
        return ($firstbytes[0] == "\x1f" && $firstbytes[1] == "\x8b");
    }

    /**
     * The zlib extension is required for this packer to work. This is a single
     * location for the code to check whether the extension is available.
     *
     * @deprecated since 2.7 Always true because zlib extension is now required.
     *
     * @return bool True if the zlib extension is available OK
     */
    public static function has_required_extension() {
        return extension_loaded('zlib');
    }
}


/**
 * Handles extraction to pathname.
 */
class tgz_packer_extract_to_pathname implements tgz_extractor_handler {
    /**
     * @var string Target directory for extract.
     */
    protected $pathname;
    /**
     * @var array Array of files to extract (other files are skipped).
     */
    protected $onlyfiles;

    /**
     * Constructor.
     *
     * @param string $pathname target directory
     * @param array $onlyfiles only extract files present in the array
     */
    public function __construct($pathname, ?array $onlyfiles = null) {
        $this->pathname = $pathname;
        $this->onlyfiles = $onlyfiles;
    }

    /**
     * @see tgz_extractor_handler::tgz_start_file()
     */
    public function tgz_start_file($archivepath) {
        // Check file restriction.
        if ($this->onlyfiles !== null && !in_array($archivepath, $this->onlyfiles)) {
            return null;
        }
        // Ensure directory exists and prepare filename.
        $fullpath = $this->pathname . '/' . $archivepath;
        check_dir_exists(dirname($fullpath));
        return $fullpath;
    }

    /**
     * @see tgz_extractor_handler::tgz_end_file()
     */
    public function tgz_end_file($archivepath, $realpath) {
        // Do nothing.
    }

    /**
     * @see tgz_extractor_handler::tgz_directory()
     */
    public function tgz_directory($archivepath, $mtime) {
        // Check file restriction.
        if ($this->onlyfiles !== null && !in_array($archivepath, $this->onlyfiles)) {
            return false;
        }
        // Ensure directory exists.
        $fullpath = $this->pathname . '/' . $archivepath;
        check_dir_exists($fullpath);
        return true;
    }
}


/**
 * Handles extraction to file storage.
 */
class tgz_packer_extract_to_storage implements tgz_extractor_handler {
    /**
     * @var string Path to temp file.
     */
    protected $tempfile;

    /**
     * @var int Context id for files.
     */
    protected $contextid;
    /**
     * @var string Component name for files.
     */
    protected $component;
    /**
     * @var string File area for files.
     */
    protected $filearea;
    /**
     * @var int Item ID for files.
     */
    protected $itemid;
    /**
     * @var string Base path for files (subfolders will go inside this).
     */
    protected $pathbase;
    /**
     * @var int User id for files or null if none.
     */
    protected $userid;

    /**
     * Constructor.
     *
     * @param int $contextid Context id for files.
     * @param string $component Component name for files.
     * @param string $filearea File area for files.
     * @param int $itemid Item ID for files.
     * @param string $pathbase Base path for files (subfolders will go inside this).
     * @param int $userid User id for files or null if none.
     */
    public function __construct($contextid, $component, $filearea, $itemid, $pathbase, $userid) {
        global $CFG;

        // Store all data.
        $this->contextid = $contextid;
        $this->component = $component;
        $this->filearea = $filearea;
        $this->itemid = $itemid;
        $this->pathbase = $pathbase;
        $this->userid = $userid;

        // Obtain temp filename.
        $tempfolder = $CFG->tempdir . '/core_files';
        check_dir_exists($tempfolder);
        $this->tempfile = tempnam($tempfolder, '.dat');
    }

    /**
     * @see tgz_extractor_handler::tgz_start_file()
     */
    public function tgz_start_file($archivepath) {
        // All files are stored in the same filename.
        return $this->tempfile;
    }

    /**
     * @see tgz_extractor_handler::tgz_end_file()
     */
    public function tgz_end_file($archivepath, $realpath) {
        // Place temp file into storage.
        $fs = get_file_storage();
        $filerecord = array('contextid' => $this->contextid, 'component' => $this->component,
                'filearea' => $this->filearea, 'itemid' => $this->itemid);
        $filerecord['filepath'] = $this->pathbase . dirname($archivepath) . '/';
        $filerecord['filename'] = basename($archivepath);
        if ($this->userid) {
            $filerecord['userid'] = $this->userid;
        }
        // Delete existing file (if any) and create new one.
        tgz_packer::delete_existing_file_record($fs, $filerecord);
        $fs->create_file_from_pathname($filerecord, $this->tempfile);
        unlink($this->tempfile);
    }

    /**
     * @see tgz_extractor_handler::tgz_directory()
     */
    public function tgz_directory($archivepath, $mtime) {
        // Standardise path.
        if (!preg_match('~/$~', $archivepath)) {
            $archivepath .= '/';
        }
        // Create directory if it doesn't already exist.
        $fs = get_file_storage();
        if (!$fs->file_exists($this->contextid, $this->component, $this->filearea, $this->itemid,
                $this->pathbase . $archivepath, '.')) {
            $fs->create_directory($this->contextid, $this->component, $this->filearea, $this->itemid,
                    $this->pathbase . $archivepath);
        }
        return true;
    }
}
