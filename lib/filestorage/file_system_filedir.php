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
 * Core file system class definition.
 *
 * @package   core_files
 * @copyright 2017 Andrew Nicols <andrew@nicols.co.uk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * File system class used for low level access to real files in filedir.
 *
 * @package   core_files
 * @category  files
 * @copyright 2017 Andrew Nicols <andrew@nicols.co.uk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class file_system_filedir extends file_system {

    /**
     * @var string The path to the local copy of the filedir.
     */
    protected $filedir = null;

    /**
     * @var string The path to the trashdir.
     */
    protected $trashdir = null;

    /**
     * @var string Default directory permissions for new dirs.
     */
    protected $dirpermissions = null;

    /**
     * @var string Default file permissions for new files.
     */
    protected $filepermissions = null;


    /**
     * Perform any custom setup for this type of file_system.
     */
    public function __construct() {
        global $CFG;

        if (isset($CFG->filedir)) {
            $this->filedir = $CFG->filedir;
        } else {
            $this->filedir = $CFG->dataroot.'/filedir';
        }

        if (isset($CFG->trashdir)) {
            $this->trashdir = $CFG->trashdir;
        } else {
            $this->trashdir = $CFG->dataroot.'/trashdir';
        }

        $this->dirpermissions = $CFG->directorypermissions;
        $this->filepermissions = $CFG->filepermissions;

        // Make sure the file pool directory exists.
        if (!is_dir($this->filedir)) {
            if (!mkdir($this->filedir, $this->dirpermissions, true)) {
                // Permission trouble.
                throw new file_exception('storedfilecannotcreatefiledirs');
            }

            // Place warning file in file pool root.
            if (!file_exists($this->filedir.'/warning.txt')) {
                file_put_contents($this->filedir.'/warning.txt',
                        'This directory contains the content of uploaded files and is controlled by Moodle code. ' .
                        'Do not manually move, change or rename any of the files and subdirectories here.');
                chmod($this->filedir . '/warning.txt', $this->filepermissions);
            }
        }

        // Make sure the trashdir directory exists too.
        if (!is_dir($this->trashdir)) {
            if (!mkdir($this->trashdir, $this->dirpermissions, true)) {
                // Permission trouble.
                throw new file_exception('storedfilecannotcreatefiledirs');
            }
        }
    }

    /**
     * Get the full path for the specified hash, including the path to the filedir.
     *
     * @param string $contenthash The content hash
     * @param bool $fetchifnotfound Whether to attempt to fetch from the remote path if not found.
     * @return string The full path to the content file
     */
    protected function get_local_path_from_hash($contenthash, $fetchifnotfound = false) {
        return $this->get_fulldir_from_hash($contenthash) . '/' .$contenthash;
    }

    /**
     * Get a remote filepath for the specified stored file.
     *
     * @param stored_file $file The file to fetch the path for
     * @param bool $fetchifnotfound Whether to attempt to fetch from the remote path if not found.
     * @return string The full path to the content file
     */
    public function get_local_path_from_storedfile(stored_file $file, $fetchifnotfound = false) {
        $filepath = $this->get_local_path_from_hash($file->get_contenthash(), $fetchifnotfound);

        // Try content recovery.
        if ($fetchifnotfound && !is_readable($filepath)) {
            $this->recover_file($file);
        }

        return $filepath;
    }

    /**
     * Get a remote filepath for the specified stored file.
     *
     * @param stored_file $file The file to serve.
     * @return string full path to pool file with file content
     */
    public function get_remote_path_from_storedfile(stored_file $file) {
        return $this->get_local_path_from_storedfile($file, false);
    }

    /**
     * Get the full path for the specified hash, including the path to the filedir.
     *
     * @param string $contenthash The content hash
     * @return string The full path to the content file
     */
    protected function get_remote_path_from_hash($contenthash) {
        return $this->get_local_path_from_hash($contenthash, false);
    }

    /**
     * Get the full directory to the stored file, including the path to the
     * filedir, and the directory which the file is actually in.
     *
     * Note: This function does not ensure that the file is present on disk.
     *
     * @param stored_file $file The file to fetch details for.
     * @return string The full path to the content directory
     */
    protected function get_fulldir_from_storedfile(stored_file $file) {
        return $this->get_fulldir_from_hash($file->get_contenthash());
    }

    /**
     * Get the full directory to the stored file, including the path to the
     * filedir, and the directory which the file is actually in.
     *
     * @param string $contenthash The content hash
     * @return string The full path to the content directory
     */
    protected function get_fulldir_from_hash($contenthash) {
        return $this->filedir . '/' . $this->get_contentdir_from_hash($contenthash);
    }

    /**
     * Get the content directory for the specified content hash.
     * This is the directory that the file will be in, but without the
     * fulldir.
     *
     * @param string $contenthash The content hash
     * @return string The directory within filedir
     */
    protected function get_contentdir_from_hash($contenthash) {
        $l1 = $contenthash[0] . $contenthash[1];
        $l2 = $contenthash[2] . $contenthash[3];
        return "$l1/$l2";
    }

    /**
     * Get the content path for the specified content hash within filedir.
     *
     * This does not include the filedir, and is often used by file systems
     * as the object key for storage and retrieval.
     *
     * @param string $contenthash The content hash
     * @return string The filepath within filedir
     */
    protected function get_contentpath_from_hash($contenthash) {
        return $this->get_contentdir_from_hash($contenthash) . '/' . $contenthash;
    }

    /**
     * Get the full directory for the specified hash in the trash, including the path to the
     * trashdir, and the directory which the file is actually in.
     *
     * @param string $contenthash The content hash
     * @return string The full path to the trash directory
     */
    protected function get_trash_fulldir_from_hash($contenthash) {
        return $this->trashdir . '/' . $this->get_contentdir_from_hash($contenthash);
    }

    /**
     * Get the full path for the specified hash in the trash, including the path to the trashdir.
     *
     * @param string $contenthash The content hash
     * @return string The full path to the trash file
     */
    protected function get_trash_fullpath_from_hash($contenthash) {
        return $this->trashdir . '/' . $this->get_contentpath_from_hash($contenthash);
    }

    /**
     * Copy content of file to given pathname.
     *
     * @param stored_file $file The file to be copied
     * @param string $target real path to the new file
     * @return bool success
     */
    public function copy_content_from_storedfile(stored_file $file, $target) {
        $source = $this->get_local_path_from_storedfile($file, true);
        return copy($source, $target);
    }

    /**
     * Tries to recover missing content of file from trash.
     *
     * @param stored_file $file stored_file instance
     * @return bool success
     */
    protected function recover_file(stored_file $file) {
        $contentfile = $this->get_local_path_from_storedfile($file, false);

        if (file_exists($contentfile)) {
            // The file already exists on the file system. No need to recover.
            return true;
        }

        $contenthash = $file->get_contenthash();
        $contentdir = $this->get_fulldir_from_storedfile($file);
        $trashfile = $this->get_trash_fullpath_from_hash($contenthash);
        $alttrashfile = "{$this->trashdir}/{$contenthash}";

        if (!is_readable($trashfile)) {
            // The trash file was not found. Check the alternative trash file too just in case.
            if (!is_readable($alttrashfile)) {
                return false;
            }
            // The alternative trash file in trash root exists.
            $trashfile = $alttrashfile;
        }

        if (filesize($trashfile) != $file->get_filesize() or file_storage::hash_from_path($trashfile) != $contenthash) {
            // The files are different. Leave this one in trash - something seems to be wrong with it.
            return false;
        }

        if (!is_dir($contentdir)) {
            if (!mkdir($contentdir, $this->dirpermissions, true)) {
                // Unable to create the target directory.
                return false;
            }
        }

        // Perform a rename - these are generally atomic which gives us big
        // performance wins, especially for large files.
        return rename($trashfile, $contentfile);
    }

    /**
     * Marks pool file as candidate for deleting.
     *
     * @param string $contenthash
     */
    public function remove_file($contenthash) {
        if (!self::is_file_removable($contenthash)) {
            // Don't remove the file - it's still in use.
            return;
        }

        if (!$this->is_file_readable_remotely_by_hash($contenthash)) {
            // The file wasn't found in the first place. Just ignore it.
            return;
        }

        $trashpath  = $this->get_trash_fulldir_from_hash($contenthash);
        $trashfile  = $this->get_trash_fullpath_from_hash($contenthash);
        $contentfile = $this->get_local_path_from_hash($contenthash, true);

        if (!is_dir($trashpath)) {
            mkdir($trashpath, $this->dirpermissions, true);
        }

        if (file_exists($trashfile)) {
            // A copy of this file is already in the trash.
            // Remove the old version.
            unlink($contentfile);
            return;
        }

        // Move the contentfile to the trash, and fix permissions as required.
        rename($contentfile, $trashfile);

        // Fix permissions, only if needed.
        $currentperms = octdec(substr(decoct(fileperms($trashfile)), -4));
        if ((int)$this->filepermissions !== $currentperms) {
            chmod($trashfile, $this->filepermissions);
        }
    }

    /**
     * Cleanup the trash directory.
     */
    public function cron() {
        $this->empty_trash();
    }

    protected function empty_trash() {
        fulldelete($this->trashdir);
        set_config('fileslastcleanup', time());
    }

    /**
     * Add the supplied file to the file system.
     *
     * Note: If overriding this function, it is advisable to store the file
     * in the path returned by get_local_path_from_hash as there may be
     * subsequent uses of the file in the same request.
     *
     * @param string $pathname Path to file currently on disk
     * @param string $contenthash SHA1 hash of content if known (performance only)
     * @return array (contenthash, filesize, newfile)
     */
    public function add_file_from_path($pathname, $contenthash = null) {

        list($contenthash, $filesize) = $this->validate_hash_and_file_size($contenthash, $pathname);

        $hashpath = $this->get_fulldir_from_hash($contenthash);
        $hashfile = $this->get_local_path_from_hash($contenthash, false);

        $newfile = true;

        if (file_exists($hashfile)) {
            if (filesize($hashfile) === $filesize) {
                return array($contenthash, $filesize, false);
            }
            if (file_storage::hash_from_path($hashfile) === $contenthash) {
                // Jackpot! We have a hash collision.
                mkdir("$this->filedir/jackpot/", $this->dirpermissions, true);
                copy($pathname, "$this->filedir/jackpot/{$contenthash}_1");
                copy($hashfile, "$this->filedir/jackpot/{$contenthash}_2");
                throw new file_pool_content_exception($contenthash);
            }
            debugging("Replacing invalid content file $contenthash");
            unlink($hashfile);
            $newfile = false;
        }

        if (!is_dir($hashpath)) {
            if (!mkdir($hashpath, $this->dirpermissions, true)) {
                // Permission trouble.
                throw new file_exception('storedfilecannotcreatefiledirs');
            }
        }

        // Let's try to prevent some race conditions.

        $prev = ignore_user_abort(true);
        @unlink($hashfile.'.tmp');
        if (!copy($pathname, $hashfile.'.tmp')) {
            // Borked permissions or out of disk space.
            @unlink($hashfile.'.tmp');
            ignore_user_abort($prev);
            throw new file_exception('storedfilecannotcreatefile');
        }
        if (file_storage::hash_from_path($hashfile.'.tmp') !== $contenthash) {
            // Highly unlikely edge case, but this can happen on an NFS volume with no space remaining.
            @unlink($hashfile.'.tmp');
            ignore_user_abort($prev);
            throw new file_exception('storedfilecannotcreatefile');
        }
        rename($hashfile.'.tmp', $hashfile);
        chmod($hashfile, $this->filepermissions); // Fix permissions if needed.
        @unlink($hashfile.'.tmp'); // Just in case anything fails in a weird way.
        ignore_user_abort($prev);

        return array($contenthash, $filesize, $newfile);
    }

    /**
     * Add a file with the supplied content to the file system.
     *
     * Note: If overriding this function, it is advisable to store the file
     * in the path returned by get_local_path_from_hash as there may be
     * subsequent uses of the file in the same request.
     *
     * @param string $content file content - binary string
     * @return array (contenthash, filesize, newfile)
     */
    public function add_file_from_string($content) {
        global $CFG;

        $contenthash = file_storage::hash_from_string($content);
        // Binary length.
        $filesize = strlen($content);

        $hashpath = $this->get_fulldir_from_hash($contenthash);
        $hashfile = $this->get_local_path_from_hash($contenthash, false);

        $newfile = true;

        if (file_exists($hashfile)) {
            if (filesize($hashfile) === $filesize) {
                return array($contenthash, $filesize, false);
            }
            if (file_storage::hash_from_path($hashfile) === $contenthash) {
                // Jackpot! We have a hash collision.
                mkdir("$this->filedir/jackpot/", $this->dirpermissions, true);
                copy($hashfile, "$this->filedir/jackpot/{$contenthash}_1");
                file_put_contents("$this->filedir/jackpot/{$contenthash}_2", $content);
                throw new file_pool_content_exception($contenthash);
            }
            debugging("Replacing invalid content file $contenthash");
            unlink($hashfile);
            $newfile = false;
        }

        if (!is_dir($hashpath)) {
            if (!mkdir($hashpath, $this->dirpermissions, true)) {
                // Permission trouble.
                throw new file_exception('storedfilecannotcreatefiledirs');
            }
        }

        // Hopefully this works around most potential race conditions.

        $prev = ignore_user_abort(true);

        if (!empty($CFG->preventfilelocking)) {
            $newsize = file_put_contents($hashfile.'.tmp', $content);
        } else {
            $newsize = file_put_contents($hashfile.'.tmp', $content, LOCK_EX);
        }

        if ($newsize === false) {
            // Borked permissions most likely.
            ignore_user_abort($prev);
            throw new file_exception('storedfilecannotcreatefile');
        }
        if (filesize($hashfile.'.tmp') !== $filesize) {
            // Out of disk space?
            unlink($hashfile.'.tmp');
            ignore_user_abort($prev);
            throw new file_exception('storedfilecannotcreatefile');
        }
        rename($hashfile.'.tmp', $hashfile);
        chmod($hashfile, $this->filepermissions); // Fix permissions if needed.
        @unlink($hashfile.'.tmp'); // Just in case anything fails in a weird way.
        ignore_user_abort($prev);

        return array($contenthash, $filesize, $newfile);
    }

}
