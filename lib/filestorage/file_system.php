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
abstract class file_system {

    /**
     * Output the content of the specified stored file.
     *
     * Note, this is different to get_content() as it uses the built-in php
     * readfile function which is more efficient.
     *
     * @param stored_file $file The file to serve.
     * @return void
     */
    public function readfile(stored_file $file) {
        if ($this->is_file_readable_locally_by_storedfile($file, false)) {
            $path = $this->get_local_path_from_storedfile($file, false);
        } else {
            $path = $this->get_remote_path_from_storedfile($file);
        }
        if (readfile_allow_large($path, $file->get_filesize()) === false) {
            throw new file_exception('storedfilecannotreadfile', $file->get_filename());
        }
    }

    /**
     * Get the full path on disk for the specified stored file.
     *
     * Note: This must return a consistent path for the file's contenthash
     * and the path _will_ be in a standard local format.
     * Streamable paths will not work.
     * A local copy of the file _will_ be fetched if $fetchifnotfound is tree.
     *
     * The $fetchifnotfound allows you to determine the expected path of the file.
     *
     * @param stored_file $file The file to serve.
     * @param bool $fetchifnotfound Whether to attempt to fetch from the remote path if not found.
     * @return string full path to pool file with file content
     */
    public function get_local_path_from_storedfile(stored_file $file, $fetchifnotfound = false) {
        return $this->get_local_path_from_hash($file->get_contenthash(), $fetchifnotfound);
    }

    /**
     * Get a remote filepath for the specified stored file.
     *
     * This is typically either the same as the local filepath, or it is a streamable resource.
     *
     * See https://secure.php.net/manual/en/wrappers.php for further information on valid wrappers.
     *
     * @param stored_file $file The file to serve.
     * @return string full path to pool file with file content
     */
    public function get_remote_path_from_storedfile(stored_file $file) {
        return $this->get_remote_path_from_hash($file->get_contenthash(), false);
    }

    /**
     * Get the full path for the specified hash, including the path to the filedir.
     *
     * Note: This must return a consistent path for the file's contenthash
     * and the path _will_ be in a standard local format.
     * Streamable paths will not work.
     * A local copy of the file _will_ be fetched if $fetchifnotfound is tree.
     *
     * The $fetchifnotfound allows you to determine the expected path of the file.
     *
     * @param string $contenthash The content hash
     * @param bool $fetchifnotfound Whether to attempt to fetch from the remote path if not found.
     * @return string The full path to the content file
     */
    abstract protected function get_local_path_from_hash($contenthash, $fetchifnotfound = false);

    /**
     * Get the full path for the specified hash, including the path to the filedir.
     *
     * This is typically either the same as the local filepath, or it is a streamable resource.
     *
     * See https://secure.php.net/manual/en/wrappers.php for further information on valid wrappers.
     *
     * @param string $contenthash The content hash
     * @return string The full path to the content file
     */
    abstract protected function get_remote_path_from_hash($contenthash);

    /**
     * Determine whether the file is present on the file system somewhere.
     * A local copy of the file _will_ be fetched if $fetchifnotfound is tree.
     *
     * The $fetchifnotfound allows you to determine the expected path of the file.
     *
     * @param stored_file $file The file to ensure is available.
     * @param bool $fetchifnotfound Whether to attempt to fetch from the remote path if not found.
     * @return bool
     */
    public function is_file_readable_locally_by_storedfile(stored_file $file, $fetchifnotfound = false) {
        if (!$file->get_filesize()) {
            // Files with empty size are either directories or empty.
            // We handle these virtually.
            return true;
        }

        // Check to see if the file is currently readable.
        $path = $this->get_local_path_from_storedfile($file, $fetchifnotfound);
        if (is_readable($path)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the file is present on the local file system somewhere.
     *
     * @param stored_file $file The file to ensure is available.
     * @return bool
     */
    public function is_file_readable_remotely_by_storedfile(stored_file $file) {
        if (!$file->get_filesize()) {
            // Files with empty size are either directories or empty.
            // We handle these virtually.
            return true;
        }

        $path = $this->get_remote_path_from_storedfile($file, false);
        if (is_readable($path)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the file is present on the file system somewhere given
     * the contenthash.
     *
     * @param string $contenthash The contenthash of the file to check.
     * @param bool $fetchifnotfound Whether to attempt to fetch from the remote path if not found.
     * @return bool
     */
    public function is_file_readable_locally_by_hash($contenthash, $fetchifnotfound = false) {
        if ($contenthash === file_storage::hash_from_string('')) {
            // Files with empty size are either directories or empty.
            // We handle these virtually.
            return true;
        }

        // This is called by file_storage::content_exists(), and in turn by the repository system.
        $path = $this->get_local_path_from_hash($contenthash, $fetchifnotfound);

        // Note - it is not possible to perform a content recovery safely from a hash alone.
        return is_readable($path);
    }

    /**
     * Determine whether the file is present locally on the file system somewhere given
     * the contenthash.
     *
     * @param string $contenthash The contenthash of the file to check.
     * @return bool
     */
    public function is_file_readable_remotely_by_hash($contenthash) {
        if ($contenthash === file_storage::hash_from_string('')) {
            // Files with empty size are either directories or empty.
            // We handle these virtually.
            return true;
        }

        $path = $this->get_remote_path_from_hash($contenthash, false);

        // Note - it is not possible to perform a content recovery safely from a hash alone.
        return is_readable($path);
    }

    /**
     * Copy content of file to given pathname.
     *
     * @param stored_file $file The file to be copied
     * @param string $target real path to the new file
     * @return bool success
     */
    abstract public function copy_content_from_storedfile(stored_file $file, $target);

    /**
     * Remove the file with the specified contenthash.
     *
     * Note, if overriding this function, you _must_ check that the file is
     * no longer in use - see {check_file_usage}.
     *
     * DO NOT call directly - reserved for core!!
     *
     * @param string $contenthash
     */
    abstract public function remove_file($contenthash);

    /**
     * Check whether a file is removable.
     *
     * This must be called prior to file removal.
     *
     * @param string $contenthash
     * @return bool
     */
    protected static function is_file_removable($contenthash) {
        global $DB;

        if ($contenthash === file_storage::hash_from_string('')) {
            // No need to delete files without content.
            return false;
        }

        // Note: This section is critical - in theory file could be reused at the same time, if this
        // happens we can still recover the file from trash.
        // Technically this is the responsibility of the file_storage API, but as this method is public, we go belt-and-braces.
        if ($DB->record_exists('files', array('contenthash' => $contenthash))) {
            // File content is still used.
            return false;
        }

        return true;
    }

    /**
     * Get the content of the specified stored file.
     *
     * Generally you will probably want to use readfile() to serve content,
     * and where possible you should see if you can use
     * get_content_file_handle and work with the file stream instead.
     *
     * @param stored_file $file The file to retrieve
     * @return string The full file content
     */
    public function get_content(stored_file $file) {
        if (!$file->get_filesize()) {
            // Directories are empty. Empty files are not worth fetching.
            return '';
        }

        $source = $this->get_remote_path_from_storedfile($file);
        return file_get_contents($source);
    }

    /**
     * List contents of archive.
     *
     * @param stored_file $file The archive to inspect
     * @param file_packer $packer file packer instance
     * @return array of file infos
     */
    public function list_files($file, file_packer $packer) {
        $archivefile = $this->get_local_path_from_storedfile($file, true);
        return $packer->list_files($archivefile);
    }

    /**
     * Extract file to given file path (real OS filesystem), existing files are overwritten.
     *
     * @param stored_file $file The archive to inspect
     * @param file_packer $packer File packer instance
     * @param string $pathname Target directory
     * @param file_progress $progress progress indicator callback or null if not required
     * @return array|bool List of processed files; false if error
     */
    public function extract_to_pathname(stored_file $file, file_packer $packer, $pathname, file_progress $progress = null) {
        $archivefile = $this->get_local_path_from_storedfile($file, true);
        return $packer->extract_to_pathname($archivefile, $pathname, null, $progress);
    }

    /**
     * Extract file to given file path (real OS filesystem), existing files are overwritten.
     *
     * @param stored_file $file The archive to inspect
     * @param file_packer $packer file packer instance
     * @param int $contextid context ID
     * @param string $component component
     * @param string $filearea file area
     * @param int $itemid item ID
     * @param string $pathbase path base
     * @param int $userid user ID
     * @param file_progress $progress Progress indicator callback or null if not required
     * @return array|bool list of processed files; false if error
     */
    public function extract_to_storage(stored_file $file, file_packer $packer, $contextid,
            $component, $filearea, $itemid, $pathbase, $userid = null, file_progress $progress = null) {

        // Since we do not know which extractor we have, and whether it supports remote paths, use a local path here.
        $archivefile = $this->get_local_path_from_storedfile($file, true);
        return $packer->extract_to_storage($archivefile, $contextid,
                $component, $filearea, $itemid, $pathbase, $userid, $progress);
    }

    /**
     * Add file/directory into archive.
     *
     * @param stored_file $file The file to archive
     * @param file_archive $filearch file archive instance
     * @param string $archivepath pathname in archive
     * @return bool success
     */
    public function add_storedfile_to_archive(stored_file $file, file_archive $filearch, $archivepath) {
        if ($file->is_directory()) {
            return $filearch->add_directory($archivepath);
        } else {
            // Since we do not know which extractor we have, and whether it supports remote paths, use a local path here.
            return $filearch->add_file_from_pathname($archivepath, $this->get_local_path_from_storedfile($file, true));
        }
    }

    /**
     * Adds this file path to a curl request (POST only).
     *
     * @param stored_file $file The file to add to the curl request
     * @param curl $curlrequest The curl request object
     * @param string $key What key to use in the POST request
     * @return void
     * This needs the fullpath for the storedfile :/
     * Can this be achieved in some other fashion?
     */
    public function add_to_curl_request(stored_file $file, &$curlrequest, $key) {
        // Note: curl_file_create does not work with remote paths.
        $path = $this->get_local_path_from_storedfile($file, true);
        $curlrequest->_tmp_file_post_params[$key] = curl_file_create($path, null, $file->get_filename());
    }

    /**
     * Returns information about image.
     * Information is determined from the file content
     *
     * @param stored_file $file The file to inspect
     * @return mixed array with width, height and mimetype; false if not an image
     */
    public function get_imageinfo(stored_file $file) {
        if (!$this->is_image_from_storedfile($file)) {
            return false;
        }

        // Whilst get_imageinfo_from_path can use remote paths, it must download the entire file first.
        // It is more efficient to use a local file when possible.
        return $this->get_imageinfo_from_path($this->get_local_path_from_storedfile($file, true));
    }

    /**
     * Attempt to determine whether the specified file is likely to be an
     * image.
     * Since this relies upon the mimetype stored in the files table, there
     * may be times when this information is not 100% accurate.
     *
     * @param stored_file $file The file to check
     * @return bool
     */
    public function is_image_from_storedfile(stored_file $file) {
        if (!$file->get_filesize()) {
            // An empty file cannot be an image.
            return false;
        }

        $mimetype = $file->get_mimetype();
        if (!preg_match('|^image/|', $mimetype)) {
            // The mimetype does not include image.
            return false;
        }

        // If it looks like an image, and it smells like an image, perhaps it's an image!
        return true;
    }

    /**
     * Returns image information relating to the specified path or URL.
     *
     * @param string $path The full path of the image file.
     * @return array|bool array that containing width, height, and mimetype or false if cannot get the image info.
     */
    protected function get_imageinfo_from_path($path) {
        $imagemimetype = file_storage::mimetype_from_file($path);
        $issvgimage = file_is_svg_image_from_mimetype($imagemimetype);

        if (!$issvgimage) {
            $imageinfo = getimagesize($path);
            if (!is_array($imageinfo)) {
                return false; // Nothing to process, the file was not recognised as image by GD.
            }
            $image = [
                    'width' => $imageinfo[0],
                    'height' => $imageinfo[1],
                    'mimetype' => image_type_to_mime_type($imageinfo[2]),
            ];
        } else {
            // Since SVG file is actually an XML file, GD cannot handle.
            $svgcontent = @simplexml_load_file($path);
            if (!$svgcontent) {
                // Cannot parse the file.
                return false;
            }
            $svgattrs = $svgcontent->attributes();

            if (!empty($svgattrs->viewBox)) {
                // We have viewBox.
                $viewboxval = explode(' ', $svgattrs->viewBox);
                $width = intval($viewboxval[2]);
                $height = intval($viewboxval[3]);
            } else {
                // Get the width.
                if (!empty($svgattrs->width) && intval($svgattrs->width) > 0) {
                    $width = intval($svgattrs->width);
                } else {
                    // Default width.
                    $width = 800;
                }
                // Get the height.
                if (!empty($svgattrs->height) && intval($svgattrs->height) > 0) {
                    $height = intval($svgattrs->height);
                } else {
                    // Default width.
                    $height = 600;
                }
            }

            $image = [
                    'width' => $width,
                    'height' => $height,
                    'mimetype' => $imagemimetype,
            ];
        }

        if (empty($image['width']) or empty($image['height']) or empty($image['mimetype'])) {
            // GD can not parse it, sorry.
            return false;
        }
        return $image;
    }

    /**
     * Serve file content using X-Sendfile header.
     * Please make sure that all headers are already sent and the all
     * access control checks passed.
     *
     * This alternate method to xsendfile() allows an alternate file system
     * to use the full file metadata and avoid extra lookups.
     *
     * @param stored_file $file The file to send
     * @return bool success
     */
    public function xsendfile_file(stored_file $file): bool {
        return $this->xsendfile($file->get_contenthash());
    }

    /**
     * Serve file content using X-Sendfile header.
     * Please make sure that all headers are already sent and the all
     * access control checks passed.
     *
     * @param string $contenthash The content hash of the file to be served
     * @return bool success
     */
    public function xsendfile($contenthash) {
        global $CFG;
        require_once($CFG->libdir . "/xsendfilelib.php");

        return xsendfile($this->get_remote_path_from_hash($contenthash));
    }

    /**
     * Returns true if filesystem is configured to support xsendfile.
     *
     * @return bool
     */
    public function supports_xsendfile() {
        global $CFG;
        return !empty($CFG->xsendfile);
    }

    /**
     * Validate that the content hash matches the content hash of the file on disk.
     *
     * @param string $contenthash The current content hash to validate
     * @param string $pathname The path to the file on disk
     * @return array The content hash (it might change) and file size
     */
    protected function validate_hash_and_file_size($contenthash, $pathname) {
        global $CFG;

        if (!is_readable($pathname)) {
            throw new file_exception('storedfilecannotread', '', $pathname);
        }

        $filesize = filesize($pathname);
        if ($filesize === false) {
            throw new file_exception('storedfilecannotread', '', $pathname);
        }

        if (is_null($contenthash)) {
            $contenthash = file_storage::hash_from_path($pathname);
        } else if ($CFG->debugdeveloper) {
            $filehash = file_storage::hash_from_path($pathname);
            if ($filehash === false) {
                throw new file_exception('storedfilecannotread', '', $pathname);
            }
            if ($filehash !== $contenthash) {
                // Hopefully this never happens, if yes we need to fix calling code.
                debugging("Invalid contenthash submitted for file $pathname", DEBUG_DEVELOPER);
                $contenthash = $filehash;
            }
        }
        if ($contenthash === false) {
            throw new file_exception('storedfilecannotread', '', $pathname);
        }

        if ($filesize > 0 and $contenthash === file_storage::hash_from_string('')) {
            // Did the file change or is file_storage::hash_from_path() borked for this file?
            clearstatcache();
            $contenthash = file_storage::hash_from_path($pathname);
            $filesize    = filesize($pathname);

            if ($contenthash === false or $filesize === false) {
                throw new file_exception('storedfilecannotread', '', $pathname);
            }
            if ($filesize > 0 and $contenthash === file_storage::hash_from_string('')) {
                // This is very weird...
                throw new file_exception('storedfilecannotread', '', $pathname);
            }
        }

        return [$contenthash, $filesize];
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
    abstract public function add_file_from_path($pathname, $contenthash = null);

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
    abstract public function add_file_from_string($content);

    /**
     * Returns file handle - read only mode, no writing allowed into pool files!
     *
     * When you want to modify a file, create a new file and delete the old one.
     *
     * @param stored_file $file The file to retrieve a handle for
     * @param int $type Type of file handle (FILE_HANDLE_xx constant)
     * @return resource file handle
     */
    public function get_content_file_handle(stored_file $file, $type = stored_file::FILE_HANDLE_FOPEN) {
        if ($type === stored_file::FILE_HANDLE_GZOPEN) {
            // Local file required for gzopen.
            $path = $this->get_local_path_from_storedfile($file, true);
        } else {
            $path = $this->get_remote_path_from_storedfile($file);
        }

        return self::get_file_handle_for_path($path, $type);
    }

    /**
     * Return a file handle for the specified path.
     *
     * This abstraction should be used when overriding get_content_file_handle in a new file system.
     *
     * @param string $path The path to the file. This shoudl be any type of path that fopen and gzopen accept.
     * @param int $type Type of file handle (FILE_HANDLE_xx constant)
     * @return resource
     * @throws coding_exception When an unexpected type of file handle is requested
     */
    protected static function get_file_handle_for_path($path, $type = stored_file::FILE_HANDLE_FOPEN) {
        switch ($type) {
            case stored_file::FILE_HANDLE_FOPEN:
                // Binary reading.
                return fopen($path, 'rb');
            case stored_file::FILE_HANDLE_GZOPEN:
                // Binary reading of file in gz format.
                return gzopen($path, 'rb');
            default:
                throw new coding_exception('Unexpected file handle type');
        }
    }

    /**
     * Retrieve the mime information for the specified stored file.
     *
     * @param string $contenthash
     * @param string $filename
     * @return string The MIME type.
     */
    public function mimetype_from_hash($contenthash, $filename) {
        $pathname = $this->get_local_path_from_hash($contenthash);
        $mimetype = file_storage::mimetype($pathname, $filename);

        if ($mimetype === 'document/unknown' && !$this->is_file_readable_locally_by_hash($contenthash)) {
            // The type is unknown, but the full checks weren't completed because the file isn't locally available.
            // Ensure we have a local copy and try again.
            $pathname = $this->get_local_path_from_hash($contenthash, true);
            $mimetype = file_storage::mimetype_from_file($pathname);
        }

        return $mimetype;
    }

    /**
     * Retrieve the mime information for the specified stored file.
     *
     * @param stored_file $file The stored file to retrieve mime information for
     * @return string The MIME type.
     */
    public function mimetype_from_storedfile($file) {
        if (!$file->get_filesize()) {
            // Files with an empty filesize are treated as directories and have no mimetype.
            return null;
        }
        return $this->mimetype_from_hash($file->get_contenthash(), $file->get_filename());
    }

    /**
     * Run any periodic tasks which must be performed.
     */
    public function cron() {
    }
}
