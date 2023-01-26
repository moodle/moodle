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
 * Implementation of .tar.gz extractor. Handles extraction of .tar.gz files.
 * Do not call directly; use methods in tgz_packer.
 *
 * @see tgz_packer
 * @package core_files
 * @copyright 2013 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Extracts .tar.gz files (POSIX format).
 */
class tgz_extractor {
    /**
     * @var int When writing data, the system writes blocks of this size.
     */
    const WRITE_BLOCK_SIZE = 65536;
    /**
     * @var int When reading data, the system reads blocks of this size.
     */
    const READ_BLOCK_SIZE = 65536;
    /**
     * @var stored_file File object for archive.
     */
    protected $storedfile;
    /**
     * @var string OS path for archive.
     */
    protected $ospath;
    /**
     * @var int Number of files (-1 if not known).
     */
    protected $numfiles;
    /**
     * @var int Number of files processed so far.
     */
    protected $donefiles;
    /**
     * @var string Current file path within archive.
     */
    protected $currentarchivepath;
    /**
     * @var string Full path to current file.
     */
    protected $currentfile;
    /**
     * @var int Size of current file in bytes.
     */
    protected $currentfilesize;
    /**
     * @var int Number of bytes of current file already written into buffer.
     */
    protected $currentfileprocessed;
    /**
     * @var resource File handle to current file.
     */
    protected $currentfp;
    /**
     * @var int Modified time of current file.
     */
    protected $currentmtime;
    /**
     * @var string Buffer containing file data awaiting write.
     */
    protected $filebuffer;
    /**
     * @var int Current length of buffer in bytes.
     */
    protected $filebufferlength;
    /**
     * @var array Results array of all files processed.
     */
    protected $results;

    /**
     * @var array In list mode, content of the list; outside list mode, null.
     */
    protected $listresults = null;

    /**
     * @var int Whether listing or extracting.
     */
    protected $mode = self::MODE_EXTRACT;

    /**
     * @var int If extracting (default).
     */
    const MODE_EXTRACT = 0;

    /**
     * @var int Listing contents.
     */
    const MODE_LIST = 1;

    /**
     * @var int Listing contents; list now complete.
     */
    const MODE_LIST_COMPLETE = 2;

    /**
     * Constructor.
     *
     * @param stored_file|string $archivefile Moodle file or OS path to archive
     */
    public function __construct($archivefile) {
        if (is_a($archivefile, 'stored_file')) {
            $this->storedfile = $archivefile;
        } else {
            $this->ospath = $archivefile;
        }
    }

    /**
     * Extracts the archive.
     *
     * @param tgz_extractor_handler $handler Will be called for extracted files
     * @param file_progress $progress Optional progress reporting
     * @return array Array from archive path => true of processed files
     * @throws moodle_exception If there is any error processing the archive
     */
    public function extract(tgz_extractor_handler $handler, file_progress $progress = null) {
        $this->mode = self::MODE_EXTRACT;
        $this->extract_or_list($handler, $progress);
        $results = $this->results;
        unset($this->results);
        return $results;
    }

    /**
     * Extracts or lists the archive depending on $this->listmode.
     *
     * @param tgz_extractor_handler $handler Optional handler
     * @param file_progress $progress Optional progress reporting
     * @throws moodle_exception If there is any error processing the archive
     */
    protected function extract_or_list(tgz_extractor_handler $handler = null, file_progress $progress = null) {
        // Open archive.
        if ($this->storedfile) {
            $gz = $this->storedfile->get_content_file_handle(stored_file::FILE_HANDLE_GZOPEN);
            // Estimate number of read-buffers (64KB) in file. Guess that the
            // uncompressed size is 2x compressed size. Add one just to ensure
            // it's non-zero.
            $estimatedbuffers = ($this->storedfile->get_filesize() * 2 / self::READ_BLOCK_SIZE) + 1;
        } else {
            $gz = gzopen($this->ospath, 'rb');
            $estimatedbuffers = (filesize($this->ospath) * 2 / self::READ_BLOCK_SIZE) + 1;
        }
        if (!$gz) {
            throw new moodle_exception('errorprocessingarchive', '', '', null,
                    'Failed to open gzip file');
        }

        // Calculate how much progress to report per buffer read.
        $progressperbuffer = (int)(tgz_packer::PROGRESS_MAX / $estimatedbuffers);

        // Process archive in 512-byte blocks (but reading 64KB at a time).
        $buffer = '';
        $bufferpos = 0;
        $bufferlength = 0;
        $this->numfiles = -1;
        $read = 0;
        $done = 0;
        $beforeprogress = -1;
        while (true) {
            if ($bufferpos == $bufferlength) {
                $buffer = gzread($gz, self::READ_BLOCK_SIZE);
                $bufferpos = 0;
                $bufferlength = strlen($buffer);
                if ($bufferlength == 0) {
                    // EOF.
                    break;
                }

                // Report progress if enabled.
                if ($progress) {
                    if ($this->numfiles === -1) {
                        // If we don't know the number of files, do an estimate based
                        // on number of buffers read.
                        $done += $progressperbuffer;
                        if ($done >= tgz_packer::PROGRESS_MAX) {
                            $done = tgz_packer::PROGRESS_MAX - 1;
                        }
                        $progress->progress($done, tgz_packer::PROGRESS_MAX);
                    } else {
                        // Once we know the number of files, use this.
                        if ($beforeprogress === -1) {
                            $beforeprogress = $done;
                        }
                        // Calculate progress as whatever progress we reported
                        // before we knew how many files there were (might be 0)
                        // plus a proportion of the number of files out of the
                        // remaining progress value.
                        $done = $beforeprogress + (int)(($this->donefiles / $this->numfiles) *
                                (tgz_packer::PROGRESS_MAX - $beforeprogress));
                    }
                    $progress->progress($done, tgz_packer::PROGRESS_MAX);
                }
            }

            $block = substr($buffer, $bufferpos, tgz_packer::TAR_BLOCK_SIZE);
            if ($this->currentfile) {
                $this->process_file_block($block, $handler);
            } else {
                $this->process_header($block, $handler);
            }

            // When listing, if we read an index file, we abort archive processing.
            if ($this->mode === self::MODE_LIST_COMPLETE) {
                break;
            }

            $bufferpos += tgz_packer::TAR_BLOCK_SIZE;
            $read++;
        }

        // Close archive and finish.
        gzclose($gz);
    }

    /**
     * Lists files in the archive, either using the index file (if present),
     * or by basically extracting the whole thing if there isn't an index file.
     *
     * @return array Array of file listing results:
     */
    public function list_files() {
        $this->listresults = array();
        $this->mode = self::MODE_LIST;
        $this->extract_or_list();
        $listresults = $this->listresults;
        $this->listresults = null;
        return $listresults;
    }

    /**
     * Process 512-byte header block.
     *
     * @param string $block Tar block
     * @param tgz_extractor_handler $handler Will be called for extracted files
     */
    protected function process_header($block, $handler) {
        // If the block consists entirely of nulls, ignore it. (This happens
        // twice at end of archive.)
        if ($block === str_pad('', tgz_packer::TAR_BLOCK_SIZE, "\0")) {
            return;
        }

        // struct header_posix_ustar {
        //    char name[100];
        $name = rtrim(substr($block, 0, 100), "\0");

        //    char mode[8];
        //    char uid[8];
        //    char gid[8];
        //    char size[12];
        $filesize = octdec(substr($block, 124, 11));

        //    char mtime[12];
        $mtime = octdec(substr($block, 136, 11));

        //    char checksum[8];
        //    char typeflag[1];
        $typeflag = substr($block, 156, 1);

        //    char linkname[100];
        //    char magic[6];
        $magic = substr($block, 257, 6);
        if ($magic !== "ustar\0" && $magic !== "ustar ") {
            // There are two checks above; the first is the correct POSIX format
            // and the second is for GNU tar default format.
            throw new moodle_exception('errorprocessingarchive', '', '', null,
                    'Header does not have POSIX ustar magic string');
        }

        //    char version[2];
        //    char uname[32];
        //    char gname[32];
        //    char devmajor[8];
        //    char devminor[8];
        //    char prefix[155];
        $prefix = rtrim(substr($block, 345, 155), "\0");

        //    char pad[12];
        // };

        $archivepath = ltrim($prefix . '/' . $name, '/');

        // For security, ensure there is no .. folder in the archivepath.
        $archivepath = clean_param($archivepath, PARAM_PATH);

        // Handle file depending on the type.
        switch ($typeflag) {
            case '1' :
            case '2' :
            case '3' :
            case '4' :
            case '6' :
            case '7' :
                // Ignore these special cases.
                break;

            case '5' :
                // Directory.
                if ($this->mode === self::MODE_LIST) {
                    $this->listresults[] = (object)array(
                            'original_pathname' => $archivepath,
                            'pathname' => $archivepath,
                            'mtime' => $mtime,
                            'is_directory' => true,
                            'size' => 0);
                } else if ($handler->tgz_directory($archivepath, $mtime)) {
                    $this->results[$archivepath] = true;
                }
                break;

            default:
                // All other values treated as normal file.
                $this->start_current_file($archivepath, $filesize, $mtime, $handler);
                break;
        }
    }

    /**
     * Processes one 512-byte block of an existing file.
     *
     * @param string $block Data block
     * @param tgz_extractor_handler $handler Will be called for extracted files
     */
    protected function process_file_block($block, tgz_extractor_handler $handler = null) {
        // Write block into buffer.
        $blocksize = tgz_packer::TAR_BLOCK_SIZE;
        if ($this->currentfileprocessed + tgz_packer::TAR_BLOCK_SIZE > $this->currentfilesize) {
            // Partial block at end of file.
            $blocksize = $this->currentfilesize - $this->currentfileprocessed;
            $this->filebuffer .= substr($block, 0, $blocksize);
        } else {
            // Full-length block.
            $this->filebuffer .= $block;
        }
        $this->filebufferlength += $blocksize;
        $this->currentfileprocessed += $blocksize;

        // Write block to file if necessary.
        $eof = $this->currentfileprocessed == $this->currentfilesize;
        if ($this->filebufferlength >= self::WRITE_BLOCK_SIZE || $eof) {
            // Except when skipping the file, write it out.
            if ($this->currentfile !== true) {
                if (!fwrite($this->currentfp, $this->filebuffer)) {
                    throw new moodle_exception('errorprocessingarchive', '', '', null,
                            'Failed to write buffer to output file: ' . $this->currentfile);
                }
            }
            $this->filebuffer = '';
            $this->filebufferlength = 0;
        }

        // If file is finished, close it.
        if ($eof) {
            $this->close_current_file($handler);
        }
    }

    /**
     * Starts processing a file from archive.
     *
     * @param string $archivepath Path inside archive
     * @param int $filesize Size in bytes
     * @param int $mtime File-modified time
     * @param tgz_extractor_handler $handler Will be called for extracted files
     * @throws moodle_exception
     */
    protected function start_current_file($archivepath, $filesize, $mtime,
            tgz_extractor_handler $handler = null) {
        global $CFG;

        $this->currentarchivepath = $archivepath;
        $this->currentmtime = $mtime;
        $this->currentfilesize = $filesize;
        $this->currentfileprocessed = 0;

        if ($archivepath === tgz_packer::ARCHIVE_INDEX_FILE) {
            // For index file, store in temp directory.
            $tempfolder = $CFG->tempdir . '/core_files';
            check_dir_exists($tempfolder);
            $this->currentfile = tempnam($tempfolder, '.index');
        } else {
            if ($this->mode === self::MODE_LIST) {
                // If listing, add to list.
                $this->listresults[] = (object)array(
                        'original_pathname' => $archivepath,
                        'pathname' => $archivepath,
                        'mtime' => $mtime,
                        'is_directory' => false,
                        'size' => $filesize);

                // Discard file.
                $this->currentfile = true;
            } else {
                // For other files, ask handler for location.
                $this->currentfile = $handler->tgz_start_file($archivepath);
                if ($this->currentfile === null) {
                    // This indicates that we are discarding the current file.
                    $this->currentfile = true;
                }
            }
        }
        $this->filebuffer = '';
        $this->filebufferlength = 0;

        // Open file.
        if ($this->currentfile !== true) {
            $this->currentfp = fopen($this->currentfile, 'wb');
            if (!$this->currentfp) {
                throw new moodle_exception('errorprocessingarchive', '', '', null,
                        'Failed to open output file: ' . $this->currentfile);
            }
        } else {
            $this->currentfp = null;
        }

        // If it has no size, close it right away.
        if ($filesize == 0) {
            $this->close_current_file($handler);
        }
    }

    /**
     * Closes the current file, calls handler, and sets up data.
     *
     * @param tgz_extractor_handler $handler Will be called for extracted files
     * @throws moodle_exception If there is an error closing it
     */
    protected function close_current_file($handler) {
        if ($this->currentfp !== null) {
            if (!fclose($this->currentfp)) {
                throw new moodle_exception('errorprocessingarchive', '', '', null,
                        'Failed to close output file: ' .  $this->currentfile);
            }

            // At this point we should touch the file to set its modified
            // time to $this->currentmtime. However, when extracting to the
            // temp directory, cron will delete files more than a week old,
            // so to avoid problems we leave all files at their current time.
        }

        if ($this->currentarchivepath === tgz_packer::ARCHIVE_INDEX_FILE) {
            if ($this->mode === self::MODE_LIST) {
                // When listing array, use the archive index to produce the list.
                $index = file($this->currentfile);
                $ok = true;
                foreach ($index as $num => $value) {
                    // For first line (header), check it's valid then skip it.
                    if ($num == 0) {
                        if (preg_match('~^' . preg_quote(tgz_packer::ARCHIVE_INDEX_COUNT_PREFIX) . '~', $value)) {
                            continue;
                        } else {
                            // Not valid, better ignore the file.
                            $ok = false;
                            break;
                        }
                    }
                    // Split on tabs and store in results array.
                    $values = explode("\t", trim($value));
                    $this->listresults[] = (object)array(
                        'original_pathname' => $values[0],
                        'pathname' => $values[0],
                        'mtime' => ($values[3] === '?' ? tgz_packer::DEFAULT_TIMESTAMP : (int)$values[3]),
                        'is_directory' => $values[1] === 'd',
                        'size' => (int)$values[2]);
                }
                if ($ok) {
                    $this->mode = self::MODE_LIST_COMPLETE;
                }
                unlink($this->currentfile);
            } else {
                // For index file, get number of files and delete temp file.
                $contents = file_get_contents($this->currentfile, false, null, 0, 128);
                $matches = array();
                if (preg_match('~^' . preg_quote(tgz_packer::ARCHIVE_INDEX_COUNT_PREFIX) .
                        '([0-9]+)~', $contents, $matches)) {
                    $this->numfiles = (int)$matches[1];
                }
                unlink($this->currentfile);
            }
        } else {
            // Report to handler and put in results.
            if ($this->currentfp !== null) {
                $handler->tgz_end_file($this->currentarchivepath, $this->currentfile);
                $this->results[$this->currentarchivepath] = true;
            }
            $this->donefiles++;
        }

        // No longer have a current file.
        $this->currentfp = null;
        $this->currentfile = null;
        $this->currentarchivepath = null;
    }

}

/**
 * Interface for callback from tgz_extractor::extract.
 *
 * The file functions will be called (in pairs tgz_start_file, tgz_end_file) for
 * each file in the archive. (There is only one exception, the special
 * .ARCHIVE_INDEX file which is not reported to the handler.)
 *
 * The directory function is called whenever the archive contains a directory
 * entry.
 */
interface tgz_extractor_handler {
    /**
     * Called when the system begins to extract a file. At this point, the
     * handler must decide where on disk the extracted file should be located.
     * This can be a temporary location or final target, as preferred.
     *
     * The handler can request for files to be skipped, in which case no data
     * will be written and tgz_end_file will not be called.
     *
     * @param string $archivepath Path and name of file within archive
     * @return string Location for output file in filesystem, or null to skip file
     */
    public function tgz_start_file($archivepath);

    /**
     * Called when the system has finished extracting a file. The handler can
     * now process the extracted file if required.
     *
     * @param string $archivepath Path and name of file within archive
     * @param string $realpath Path in filesystem (from tgz_start_file return)
     * @return bool True to continue processing, false to abort archive extract
     */
    public function tgz_end_file($archivepath, $realpath);

    /**
     * Called when a directory entry is found in the archive.
     *
     * The handler can create a corresponding directory if required.
     *
     * @param string $archivepath Path and name of directory within archive
     * @param int $mtime Modified time of directory
     * @return bool True if directory was created, false if skipped
     */
    public function tgz_directory($archivepath, $mtime);
}
