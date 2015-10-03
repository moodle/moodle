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
 * Provides core\update\code_manager class.
 *
 * @package     core_plugin
 * @copyright   2012, 2013, 2015 David Mudrak <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\update;

use coding_exception;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/filelib.php');

/**
 * General purpose class managing the plugins source code files deployment
 *
 * The class is able and supposed to
 * - fetch and cache ZIP files distributed via the Moodle Plugins directory
 * - unpack the ZIP files in a temporary storage
 * - archive existing version of the plugin source code
 * - move (deploy) the plugin source code into the $CFG->dirroot
 *
 * @copyright 2015 David Mudrak <david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class code_manager {

    /** @var string full path to the Moodle app directory root */
    protected $dirroot;
    /** @var string full path to the temp directory root */
    protected $temproot;

    /**
     * Instantiate the class instance
     *
     * @param string $dirroot full path to the moodle app directory root
     * @param string $temproot full path to our temp directory
     */
    public function __construct($dirroot=null, $temproot=null) {
        global $CFG;

        if (empty($dirroot)) {
            $dirroot = $CFG->dirroot;
        }

        if (empty($temproot)) {
            // Note we are using core_plugin here as that is the valid core
            // subsystem we are part of. The namespace of this class (core\update)
            // does not match it for legacy reasons.  The data stored in the
            // temp directory are expected to survive multiple requests and
            // purging caches during the upgrade, so we make use of
            // make_temp_directory(). The contents of it can be removed if needed,
            // given the site is in the maintenance mode (so that cron is not
            // executed) and the site is not being upgraded.
            $temproot = make_temp_directory('core_plugin/code_manager');
        }

        $this->dirroot = $dirroot;
        $this->temproot = $temproot;

        $this->init_temp_directories();
    }

    /**
     * Obtain the plugin ZIP file from the given URL
     *
     * The caller is supposed to know both downloads URL and the MD5 hash of
     * the ZIP contents in advance, typically by using the API requests against
     * the plugins directory.
     *
     * @param string $url
     * @param string $md5
     * @return string|bool full path to the file, false on error
     */
    public function get_remote_plugin_zip($url, $md5) {

        // Sanitize and validate the URL.
        $url = str_replace(array("\r", "\n"), '', $url);

        if (!preg_match('|^https?://|i', $url)) {
            $this->debug('Error fetching plugin ZIP: unsupported transport protocol: '.$url);
            return false;
        }

        // The cache location for the file.
        $distfile = $this->temproot.'/distfiles/'.$md5.'.zip';

        if (is_readable($distfile)) {
            return $distfile;
        }

        // Download the file into a temporary location.
        $tempdir = make_request_directory();
        $tempfile = $tempdir.'/plugin.zip';
        $result = $this->download_plugin_zip_file($url, $tempfile);

        if (!$result) {
            return false;
        }

        $actualmd5 = md5_file($tempfile);

        // Make sure the actual md5 hash matches the expected one.
        if ($actualmd5 !== $md5) {
            $this->debug('Error fetching plugin ZIP: md5 mismatch.');
            return false;
        }

        // If the file is empty, something went wrong.
        if ($actualmd5 === 'd41d8cd98f00b204e9800998ecf8427e') {
            return false;
        }

        // Store the file in our cache.
        if (!rename($tempfile, $distfile)) {
            return false;
        }

        return $distfile;
    }

    /**
     * Move a folder with the plugin code from the source to the target location
     *
     * This can be used to move plugin folders to and from the dirroot/dataroot
     * as needed. It is assumed that the caller checked that both locations are
     * writable.
     *
     * Permissions in the target location are set to the same values that the
     * parent directory has (see MDL-42110 for details).
     *
     * @param string $source full path to the current plugin code folder
     * @param string $target full path to the new plugin code folder
     */
    public function move_plugin_directory($source, $target) {

        $targetparent = dirname($target);

        if ($targetparent === '.') {
            // No directory separator in $target..
            throw new coding_exception('Invalid target path', $target);
        }

        if (!is_writable($targetparent)) {
            throw new coding_exception('Attempting to move into non-writable parent directory', $targetparent);
        }

        // Use parent directory's permissions for us, too.
        $dirpermissions = fileperms($targetparent);
        // Strip execute flags and use that for files.
        $filepermissions = ($dirpermissions & 0666);

        $this->move_directory($source, $target, $dirpermissions, $filepermissions);
    }

    /**
     * Extracts the saved plugin ZIP file.
     *
     * Returns the list of files found in the ZIP. The format of that list is
     * array of (string)filerelpath => (bool|string) where the array value is
     * either true or a string describing the problematic file.
     *
     * @see zip_packer::extract_to_pathname()
     * @param string $zipfilepath full path to the saved ZIP file
     * @param string $targetdir full path to the directory to extract the ZIP file to
     * @param string $rootdir explicitly rename the root directory of the ZIP into this non-empty value
     * @param array list of extracted files as returned by {@link zip_packer::extract_to_pathname()}
     */
    public function unzip_plugin_file($zipfilepath, $targetdir, $rootdir = '') {

        $fp = get_file_packer('application/zip');
        $files = $fp->extract_to_pathname($zipfilepath, $targetdir);

        if (!$files) {
            return array();
        }

        if (!empty($rootdir)) {
            $files = $this->rename_extracted_rootdir($targetdir, $rootdir, $files);
        }

        // Sometimes zip may not contain all parent directories, add them to make it consistent.
        foreach ($files as $path => $status) {
            if ($status !== true) {
                continue;
            }
            $parts = explode('/', trim($path, '/'));
            while (array_pop($parts)) {
                if (empty($parts)) {
                    break;
                }
                $dir = implode('/', $parts).'/';
                if (!isset($files[$dir])) {
                    $files[$dir] = true;
                }
            }
        }

        return $files;
    }

    // This is the end, my only friend, the end ... of external public API.

    /**
     * Makes sure all temp directories exist and are writable.
     */
    protected function init_temp_directories() {
        make_writable_directory($this->temproot.'/distfiles');
    }

    /**
     * Raise developer debugging level message.
     *
     * @param string $msg
     */
    protected function debug($msg) {
        debugging($msg, DEBUG_DEVELOPER);
    }

    /**
     * Download the ZIP file with the plugin package from the given location
     *
     * @param string $url URL to the file
     * @param string $tofile full path to where to store the downloaded file
     * @return bool false on error
     */
    protected function download_plugin_zip_file($url, $tofile) {

        if (file_exists($tofile)) {
            $this->debug('Error fetching plugin ZIP: target location exists.');
            return false;
        }

        $status = $this->download_file_content($url, $tofile);

        if (!$status) {
            $this->debug('Error fetching plugin ZIP.');
            @unlink($tofile);
            return false;
        }

        return true;
    }

    /**
     * Thin wrapper for the core's download_file_content() function.
     *
     * @param string $url URL to the file
     * @param string $tofile full path to where to store the downloaded file
     * @return bool
     */
    protected function download_file_content($url, $tofile) {

        // Prepare the parameters for the download_file_content() function.
        $headers = null;
        $postdata = null;
        $fullresponse = false;
        $timeout = 300;
        $connecttimeout = 20;
        $skipcertverify = false;
        $tofile = $tofile;
        $calctimeout = false;

        return download_file_content($url, $headers, $postdata, $fullresponse, $timeout,
            $connecttimeout, $skipcertverify, $tofile, $calctimeout);
    }

    /**
     * Internal helper method supposed to be called by self::move_plugin_directory() only.
     *
     * Moves the given source into a new location recursively.
     * This is cross-device safe implementation to be used instead of the native rename() function.
     * See https://bugs.php.net/bug.php?id=54097 for more details.
     *
     * @param string $source full path to the existing directory
     * @param string $target full path to the new location of the directory
     * @param int $dirpermissions
     * @param int $filepermissions
     */
    protected function move_directory($source, $target, $dirpermissions, $filepermissions) {

        if (file_exists($target)) {
            throw new coding_exception('Attempting to overwrite existing directory', $target);
        }

        if (is_dir($source)) {
            $handle = opendir($source);
        } else {
            throw new coding_exception('Attempting to move non-existing source directory', $source);
        }

        if (!file_exists($target)) {
            // Do not use make_writable_directory() here - it is intended for dataroot only.
            mkdir($target, true);
            @chmod($target, $dirpermissions);
        }

        if (!is_writable($target)) {
            closedir($handle);
            throw new coding_exception('Created folder not writable', $target);
        }

        while ($filename = readdir($handle)) {
            $sourcepath = $source.'/'.$filename;
            $targetpath = $target.'/'.$filename;

            if ($filename === '.' or $filename === '..') {
                continue;
            }

            if (is_dir($sourcepath)) {
                $this->move_directory($sourcepath, $targetpath, $dirpermissions, $filepermissions);

            } else {
                rename($sourcepath, $targetpath);
                @chmod($targetpath, $filepermissions);
            }
        }

        closedir($handle);
        rmdir($source);
        clearstatcache();
    }

    /**
     * Renames the root directory of the extracted ZIP package.
     *
     * This method does not validate the presence of the single root directory
     * (it is the validator's duty). It just searches for the first directory
     * under the given location and renames it.
     *
     * The method will not rename the root if the requested location already
     * exists.
     *
     * @param string $dirname fullpath location of the extracted ZIP package
     * @param string $rootdir the requested name of the root directory
     * @param array $files list of extracted files
     * @return array eventually amended list of extracted files
     */
    protected function rename_extracted_rootdir($dirname, $rootdir, array $files) {

        if (!is_dir($dirname)) {
            $this->debug('Unable to rename rootdir of non-existing content');
            return $files;
        }

        if (file_exists($dirname.'/'.$rootdir)) {
            // This typically means the real root dir already has the $rootdir name.
            return $files;
        }

        $found = null; // The name of the first subdirectory under the $dirname.
        foreach (scandir($dirname) as $item) {
            if (substr($item, 0, 1) === '.') {
                continue;
            }
            if (is_dir($dirname.'/'.$item)) {
                $found = $item;
                break;
            }
        }

        if (!is_null($found)) {
            if (rename($dirname.'/'.$found, $dirname.'/'.$rootdir)) {
                $newfiles = array();
                foreach ($files as $filepath => $status) {
                    $newpath = preg_replace('~^'.preg_quote($found.'/').'~', preg_quote($rootdir.'/'), $filepath);
                    $newfiles[$newpath] = $status;
                }
                return $newfiles;
            }
        }

        return $files;
    }

}
