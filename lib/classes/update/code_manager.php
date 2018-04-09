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

use core_component;
use coding_exception;
use moodle_exception;
use SplFileInfo;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

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

        if (is_readable($distfile) and md5_file($distfile) === $md5) {
            return $distfile;
        } else {
            @unlink($distfile);
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
     * @return array list of extracted files as returned by {@link zip_packer::extract_to_pathname()}
     */
    public function unzip_plugin_file($zipfilepath, $targetdir, $rootdir = '') {

        // Extract the package into a temporary location.
        $fp = get_file_packer('application/zip');
        $tempdir = make_request_directory();
        $files = $fp->extract_to_pathname($zipfilepath, $tempdir);

        if (!$files) {
            return array();
        }

        // If requested, rename the root directory of the plugin.
        if (!empty($rootdir)) {
            $files = $this->rename_extracted_rootdir($tempdir, $rootdir, $files);
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

        // Move the extracted files into the target location.
        $this->move_extracted_plugin_files($tempdir, $targetdir, $files);

        // Set the permissions of extracted subdirs and files.
        $this->set_plugin_files_permissions($targetdir, $files);

        return $files;
    }

    /**
     * Make an archive backup of the existing plugin folder.
     *
     * @param string $folderpath full path to the plugin folder
     * @param string $targetzip full path to the zip file to be created
     * @return bool true if file created, false if not
     */
    public function zip_plugin_folder($folderpath, $targetzip) {

        if (file_exists($targetzip)) {
            throw new coding_exception('Attempting to create already existing ZIP file', $targetzip);
        }

        if (!is_writable(dirname($targetzip))) {
            throw new coding_exception('Target ZIP location not writable', dirname($targetzip));
        }

        if (!is_dir($folderpath)) {
            throw new coding_exception('Attempting to ZIP non-existing source directory', $folderpath);
        }

        $files = $this->list_plugin_folder_files($folderpath);
        $fp = get_file_packer('application/zip');
        return $fp->archive_to_pathname($files, $targetzip, false);
    }

    /**
     * Archive the current plugin on-disk version.
     *
     * @param string $folderpath full path to the plugin folder
     * @param string $component
     * @param int $version
     * @param bool $overwrite overwrite existing archive if found
     * @return bool
     */
    public function archive_plugin_version($folderpath, $component, $version, $overwrite=false) {

        if ($component !== clean_param($component, PARAM_SAFEDIR)) {
            // This should never happen, but just in case.
            throw new moodle_exception('unexpected_plugin_component_format', 'core_plugin', '', null, $component);
        }

        if ((string)$version !== clean_param((string)$version, PARAM_FILE)) {
            // Prevent some nasty injections via $plugin->version tricks.
            throw new moodle_exception('unexpected_plugin_version_format', 'core_plugin', '', null, $version);
        }

        if (empty($component) or empty($version)) {
            return false;
        }

        if (!is_dir($folderpath)) {
            return false;
        }

        $archzip = $this->temproot.'/archive/'.$component.'/'.$version.'.zip';

        if (file_exists($archzip) and !$overwrite) {
            return true;
        }

        $tmpzip = make_request_directory().'/'.$version.'.zip';
        $zipped = $this->zip_plugin_folder($folderpath, $tmpzip);

        if (!$zipped) {
            return false;
        }

        // Assert that the file looks like a valid one.
        list($expectedtype, $expectedname) = core_component::normalize_component($component);
        $actualname = $this->get_plugin_zip_root_dir($tmpzip);
        if ($actualname !== $expectedname) {
            // This should not happen.
            throw new moodle_exception('unexpected_archive_structure', 'core_plugin');
        }

        make_writable_directory(dirname($archzip));
        return rename($tmpzip, $archzip);
    }

    /**
     * Return the path to the ZIP file with the archive of the given plugin version.
     *
     * @param string $component
     * @param int $version
     * @return string|bool false if not found, full path otherwise
     */
    public function get_archived_plugin_version($component, $version) {

        if (empty($component) or empty($version)) {
            return false;
        }

        $archzip = $this->temproot.'/archive/'.$component.'/'.$version.'.zip';

        if (file_exists($archzip)) {
            return $archzip;
        }

        return false;
    }

    /**
     * Returns list of all files in the given directory.
     *
     * Given a path like /full/path/to/mod/workshop, it returns array like
     *
     *  [workshop/] => /full/path/to/mod/workshop
     *  [workshop/lang/] => /full/path/to/mod/workshop/lang
     *  [workshop/lang/workshop.php] => /full/path/to/mod/workshop/lang/workshop.php
     *  ...
     *
     * Which mathes the format used by Moodle file packers.
     *
     * @param string $folderpath full path to the plugin directory
     * @return array (string)relpath => (string)fullpath
     */
    public function list_plugin_folder_files($folderpath) {

        $folder = new RecursiveDirectoryIterator($folderpath);
        $iterator = new RecursiveIteratorIterator($folder);
        $folderpathinfo = new SplFileInfo($folderpath);
        $strip = strlen($folderpathinfo->getPathInfo()->getRealPath()) + 1;
        $files = array();
        foreach ($iterator as $fileinfo) {
            if ($fileinfo->getFilename() === '..') {
                continue;
            }
            if (strpos($fileinfo->getRealPath(), $folderpathinfo->getRealPath() !== 0)) {
                throw new moodle_exception('unexpected_filepath_mismatch', 'core_plugin');
            }
            $key = substr($fileinfo->getRealPath(), $strip);
            if ($fileinfo->isDir() and substr($key, -1) !== '/') {
                $key .= '/';
            }
            $files[str_replace(DIRECTORY_SEPARATOR, '/', $key)] = str_replace(DIRECTORY_SEPARATOR, '/', $fileinfo->getRealPath());
        }
        return $files;
    }

    /**
     * Detects the plugin's name from its ZIP file.
     *
     * Plugin ZIP packages are expected to contain a single directory and the
     * directory name would become the plugin name once extracted to the Moodle
     * dirroot.
     *
     * @param string $zipfilepath full path to the ZIP files
     * @return string|bool false on error
     */
    public function get_plugin_zip_root_dir($zipfilepath) {

        $fp = get_file_packer('application/zip');
        $files = $fp->list_files($zipfilepath);

        if (empty($files)) {
            return false;
        }

        $rootdirname = null;
        foreach ($files as $file) {
            $pathnameitems = explode('/', $file->pathname);
            if (empty($pathnameitems)) {
                return false;
            }
            // Set the expected name of the root directory in the first
            // iteration of the loop.
            if ($rootdirname === null) {
                $rootdirname = $pathnameitems[0];
            }
            // Require the same root directory for all files in the ZIP
            // package.
            if ($rootdirname !== $pathnameitems[0]) {
                return false;
            }
        }

        return $rootdirname;
    }

    // This is the end, my only friend, the end ... of external public API.

    /**
     * Makes sure all temp directories exist and are writable.
     */
    protected function init_temp_directories() {
        make_writable_directory($this->temproot.'/distfiles');
        make_writable_directory($this->temproot.'/archive');
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
     * Renames the root directory of the extracted ZIP package.
     *
     * This internal helper method assumes that the plugin ZIP package has been
     * extracted into a temporary empty directory so the plugin folder is the
     * only folder there. The ZIP package is supposed to be validated so that
     * it contains just a single root folder.
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
                if ($found !== null and $found !== $item) {
                    // Multiple directories found.
                    throw new moodle_exception('unexpected_archive_structure', 'core_plugin');
                }
                $found = $item;
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

    /**
     * Sets the permissions of extracted subdirs and files
     *
     * As a result of unzipping, the subdirs and files are created with
     * permissions set to $CFG->directorypermissions and $CFG->filepermissions.
     * These are too benevolent by default (777 and 666 respectively) for PHP
     * scripts and may lead to HTTP 500 errors in some environments.
     *
     * To fix this behaviour, we inherit the permissions of the plugin root
     * directory itself.
     *
     * @param string $targetdir full path to the directory the ZIP file was extracted to
     * @param array $files list of extracted files
     */
    protected function set_plugin_files_permissions($targetdir, array $files) {

        $dirpermissions = fileperms($targetdir);
        $filepermissions = ($dirpermissions & 0666);

        foreach ($files as $subpath => $notusedhere) {
            $path = $targetdir.'/'.$subpath;
            if (is_dir($path)) {
                @chmod($path, $dirpermissions);
            } else {
                @chmod($path, $filepermissions);
            }
        }
    }

    /**
     * Moves the extracted contents of the plugin ZIP into the target location.
     *
     * @param string $sourcedir full path to the directory the ZIP file was extracted to
     * @param mixed $targetdir full path to the directory where the files should be moved to
     * @param array $files list of extracted files
     */
    protected function move_extracted_plugin_files($sourcedir, $targetdir, array $files) {
        global $CFG;

        foreach ($files as $file => $status) {
            if ($status !== true) {
                throw new moodle_exception('corrupted_archive_structure', 'core_plugin', '', $file, $status);
            }

            $source = $sourcedir.'/'.$file;
            $target = $targetdir.'/'.$file;

            if (is_dir($source)) {
                continue;

            } else {
                if (!is_dir(dirname($target))) {
                    mkdir(dirname($target), $CFG->directorypermissions, true);
                }
                rename($source, $target);
            }
        }
    }
}
