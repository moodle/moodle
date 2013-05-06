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
 * Provides tool_installaddon_installer related classes
 *
 * @package     tool_installaddon
 * @subpackage  classes
 * @copyright   2013 David Mudrak <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Implements main plugin features.
 *
 * @copyright 2013 David Mudrak <david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_installaddon_installer {

    /** @var tool_installaddon_installfromzip */
    protected $installfromzipform = null;

    /**
     * Factory method returning an instance of this class.
     *
     * @return tool_installaddon_installer
     */
    public static function instance() {
        return new static();
    }

    /**
     * Returns the URL to the main page of this admin tool
     *
     * @param array optional parameters
     * @return moodle_url
     */
    public function index_url(array $params = null) {
        return new moodle_url('/admin/tool/installaddon/index.php', $params);
    }

    /**
     * Returns URL to the repository that addons can be searched in and installed from
     *
     * @return moodle_url
     */
    public function get_addons_repository_url() {
        global $CFG;

        if (!empty($CFG->config_php_settings['alternativeaddonsrepositoryurl'])) {
            $url = $CFG->config_php_settings['alternativeaddonsrepositoryurl'];
        } else {
            $url = 'https://moodle.org/plugins/get.php';
        }

        if (!$this->should_send_site_info()) {
            return new moodle_url($url);
        }

        // Append the basic information about our site.
        $site = array(
            'fullname' => $this->get_site_fullname(),
            'url' => $this->get_site_url(),
            'majorversion' => $this->get_site_major_version(),
        );

        $site = $this->encode_site_information($site);

        return new moodle_url($url, array('site' => $site));
    }

    /**
     * @return tool_installaddon_installfromzip
     */
    public function get_installfromzip_form() {
        global $CFG;
        require_once(dirname(__FILE__).'/installfromzip_form.php');

        if (!is_null($this->installfromzipform)) {
            return $this->installfromzipform;
        }

        $action = $this->index_url();
        $customdata = array('installer' => $this);

        $this->installfromzipform = new tool_installaddon_installfromzip($action, $customdata);

        return $this->installfromzipform;
    }

    /**
     * Saves the ZIP file from the {@link tool_installaddon_installfromzip} form
     *
     * The file is saved into the given temporary location for inspection and eventual
     * deployment. The form is expected to be submitted and validated.
     *
     * @param tool_installaddon_installfromzip $form
     * @param string $targetdir full path to the directory where the ZIP should be stored to
     * @return string filename of the saved file relative to the given target
     */
    public function save_installfromzip_file(tool_installaddon_installfromzip $form, $targetdir) {

        $filename = clean_param($form->get_new_filename('zipfile'), PARAM_FILE);
        $form->save_file('zipfile', $targetdir.'/'.$filename);

        return $filename;
    }

    /**
     * Extracts the saved file previously saved by {self::save_installfromzip_file()}
     *
     * The list of files found in the ZIP is returned via $zipcontentfiles parameter
     * by reference. The format of that list is array of (string)filerelpath => (bool|string)
     * where the array value is either true or a string describing the problematic file.
     *
     * @see zip_packer::extract_to_pathname()
     * @param string $zipfilepath full path to the saved ZIP file
     * @param string $targetdir full path to the directory to extract the ZIP file to
     * @param string $rootdir explicitly rename the root directory of the ZIP into this non-empty value
     * @param array list of extracted files as returned by {@link zip_packer::extract_to_pathname()}
     */
    public function extract_installfromzip_file($zipfilepath, $targetdir, $rootdir = '') {
        global $CFG;
        require_once($CFG->libdir.'/filelib.php');

        $fp = get_file_packer('application/zip');
        $files = $fp->extract_to_pathname($zipfilepath, $targetdir);

        if ($files) {
            if (!empty($rootdir)) {
                $files = $this->rename_extracted_rootdir($targetdir, $rootdir, $files);
            }
            return $files;

        } else {
            return array();
        }
    }

    /**
     * Returns localised list of available plugin types
     *
     * @return array (string)plugintype => (string)plugin name
     */
    public function get_plugin_types_menu() {
        global $CFG;
        require_once($CFG->libdir.'/pluginlib.php');

        $pluginman = plugin_manager::instance();

        $menu = array('' => get_string('choosedots'));
        foreach (array_keys($pluginman->get_plugin_types()) as $plugintype) {
            $menu[$plugintype] = $pluginman->plugintype_name($plugintype).' ('.$plugintype.')';
        }

        return $menu;
    }

    /**
     * Returns the full path of the root of the given plugin type
     *
     * Null is returned if the plugin type is not known. False is returned if the plugin type
     * root is expected but not found. Otherwise, string is returned.
     *
     * @param string $plugintype
     * @return string|bool|null
     */
    public function get_plugintype_root($plugintype) {

        $plugintypepath = null;
        foreach (get_plugin_types() as $type => $fullpath) {
            if ($type === $plugintype) {
                $plugintypepath = $fullpath;
                break;
            }
        }
        if (is_null($plugintypepath)) {
            return null;
        }

        if (!is_dir($plugintypepath)) {
            return false;
        }

        return $plugintypepath;
    }

    /**
     * Is it possible to create a new plugin directory for the given plugin type?
     *
     * @throws coding_exception for invalid plugin types or non-existing plugin type locations
     * @param string $plugintype
     * @return boolean
     */
    public function is_plugintype_writable($plugintype) {

        $plugintypepath = $this->get_plugintype_root($plugintype);

        if (is_null($plugintypepath)) {
            throw new coding_exception('Unknown plugin type!');
        }

        if ($plugintypepath === false) {
            throw new coding_exception('Plugin type location does not exist!');
        }

        return is_writable($plugintypepath);
    }

    /**
     * Hook method to handle the remote request to install an add-on
     *
     * This is used as a callback when the admin picks a plugin version in the
     * Moodle Plugins directory and is redirected back to their site to install
     * it.
     *
     * This hook is called early from admin/tool/installaddon/index.php page so that
     * it has opportunity to take over the UI.
     *
     * @param tool_installaddon_renderer $output
     * @param string|null $request
     * @param bool $confirmed
     */
    public function handle_remote_request(tool_installaddon_renderer $output, $request, $confirmed = false) {
        global $CFG;
        require_once(dirname(__FILE__).'/pluginfo_client.php');

        if (is_null($request)) {
            return;
        }

        $data = $this->decode_remote_request($request);

        if ($data === false) {
            echo $output->remote_request_invalid_page($this->index_url());
            exit();
        }

        list($plugintype, $pluginname) = normalize_component($data->component);

        $plugintypepath = $this->get_plugintype_root($plugintype);

        if (file_exists($plugintypepath.'/'.$pluginname)) {
            echo $output->remote_request_alreadyinstalled_page($data, $this->index_url());
            exit();
        }

        if (!$this->is_plugintype_writable($plugintype)) {
            $continueurl = $this->index_url(array('installaddonrequest' => $request));
            echo $output->remote_request_permcheck_page($data, $plugintypepath, $continueurl, $this->index_url());
            exit();
        }

        $continueurl = $this->index_url(array(
            'installaddonrequest' => $request,
            'confirm' => 1,
            'sesskey' => sesskey()));

        if (!$confirmed) {
            echo $output->remote_request_confirm_page($data, $continueurl, $this->index_url());
            exit();
        }

        // The admin has confirmed their intention to install the add-on.
        require_sesskey();

        // Fetch the plugin info. The essential information is the URL to download the ZIP
        // and the MD5 hash of the ZIP, obtained via HTTPS.
        $client = tool_installaddon_pluginfo_client::instance();

        try {
            $pluginfo = $client->get_pluginfo($data->component, $data->version);

        } catch (tool_installaddon_pluginfo_exception $e) {
            if (debugging()) {
                throw $e;
            } else {
                echo $output->remote_request_pluginfo_exception($data, $e, $this->index_url());
                exit();
            }
        }

        // Fetch the ZIP with the plugin version
        $jobid = md5(rand().uniqid('', true));
        $sourcedir = make_temp_directory('tool_installaddon/'.$jobid.'/source');
        $zipfilename = 'downloaded.zip';

        try {
            $this->download_file($pluginfo->downloadurl, $sourcedir.'/'.$zipfilename);

        } catch (tool_installaddon_installer_exception $e) {
            if (debugging()) {
                throw $e;
            } else {
                echo $output->installer_exception($e, $this->index_url());
                exit();
            }
        }

        // Check the MD5 checksum
        $md5expected = $pluginfo->downloadmd5;
        $md5actual = md5_file($sourcedir.'/'.$zipfilename);
        if ($md5expected !== $md5actual) {
            $e = new tool_installaddon_installer_exception('err_zip_md5', array('expected' => $md5expected, 'actual' => $md5actual));
            if (debugging()) {
                throw $e;
            } else {
                echo $output->installer_exception($e, $this->index_url());
                exit();
            }
        }

        // Redirect to the validation page.
        $nexturl = new moodle_url('/admin/tool/installaddon/validate.php', array(
            'sesskey' => sesskey(),
            'jobid' => $jobid,
            'zip' => $zipfilename,
            'type' => $plugintype));
        redirect($nexturl);
    }

    /**
     * Download the given file into the given destination.
     *
     * This is basically a simplified version of {@link download_file_content()} from
     * Moodle itself, tuned for fetching files from moodle.org servers. Same code is used
     * in mdeploy.php for fetching available updates.
     *
     * @param string $source file url starting with http(s)://
     * @param string $target store the downloaded content to this file (full path)
     * @throws tool_installaddon_installer_exception
     */
    public function download_file($source, $target) {
        global $CFG;
        require_once($CFG->libdir.'/filelib.php');

        $targetfile = fopen($target, 'w');

        if (!$targetfile) {
            throw new tool_installaddon_installer_exception('err_download_write_file', $target);
        }

        $options = array(
            'file' => $targetfile,
            'timeout' => 300,
            'followlocation' => true,
            'maxredirs' => 3,
            'ssl_verifypeer' => true,
            'ssl_verifyhost' => 2,
        );

        $curl = new curl(array('proxy' => true));

        $result = $curl->download_one($source, null, $options);

        $curlinfo = $curl->get_info();

        fclose($targetfile);

        if ($result !== true) {
            throw new tool_installaddon_installer_exception('err_curl_exec', array(
                'url' => $source, 'errorno' => $curl->get_errno(), 'error' => $result));

        } else if (empty($curlinfo['http_code']) or $curlinfo['http_code'] != 200) {
            throw new tool_installaddon_installer_exception('err_curl_http_code', array(
                'url' => $source, 'http_code' => $curlinfo['http_code']));

        } else if (isset($curlinfo['ssl_verify_result']) and $curlinfo['ssl_verify_result'] != 0) {
            throw new tool_installaddon_installer_exception('err_curl_ssl_verify', array(
                'url' => $source, 'ssl_verify_result' => $curlinfo['ssl_verify_result']));
        }
    }

    /**
     * Moves the given source into a new location recursively
     *
     * This is cross-device safe implementation to be used instead of the native rename() function.
     * See https://bugs.php.net/bug.php?id=54097 for more details.
     *
     * @param string $source full path to the existing directory
     * @param string $target full path to the new location of the directory
     */
    public function move_directory($source, $target) {

        if (file_exists($target)) {
            throw new tool_installaddon_installer_exception('err_folder_already_exists', array('path' => $target));
        }

        if (is_dir($source)) {
            $handle = opendir($source);
        } else {
            throw new tool_installaddon_installer_exception('err_no_such_folder', array('path' => $source));
        }

        make_writable_directory($target);

        while ($filename = readdir($handle)) {
            $sourcepath = $source.'/'.$filename;
            $targetpath = $target.'/'.$filename;

            if ($filename === '.' or $filename === '..') {
                continue;
            }

            if (is_dir($sourcepath)) {
                $this->move_directory($sourcepath, $targetpath);

            } else {
                rename($sourcepath, $targetpath);
            }
        }

        closedir($handle);

        rmdir($source);

        clearstatcache();
    }

    //// End of external API ///////////////////////////////////////////////////

    /**
     * @see self::instance()
     */
    protected function __construct() {
    }

    /**
     * @return string this site full name
     */
    protected function get_site_fullname() {
        global $SITE;

        return strip_tags($SITE->fullname);
    }

    /**
     * @return string this site URL
     */
    protected function get_site_url() {
        global $CFG;

        return $CFG->wwwroot;
    }

    /**
     * @return string major version like 2.5, 2.6 etc.
     */
    protected function get_site_major_version() {
        return moodle_major_version();
    }

    /**
     * Encodes the given array in a way that can be safely appended as HTTP GET param
     *
     * Be ware! The recipient may rely on the exact way how the site information is encoded.
     * Do not change anything here unless you know what you are doing and understand all
     * consequences! (Don't you love warnings like that, too? :-p)
     *
     * @param array $info
     * @return string
     */
    protected function encode_site_information(array $info) {
        return base64_encode(json_encode($info));
    }

    /**
     * Decide if the encoded site information should be sent to the add-ons repository site
     *
     * For now, we just return true. In the future, we may want to implement some
     * privacy aware logic (based on site/user preferences for example).
     *
     * @return bool
     */
    protected function should_send_site_info() {
        return true;
    }

    /**
     * Renames the root directory of the extracted ZIP package.
     *
     * This method does not validate the presence of the single root directory
     * (the validator does it later). It just searches for the first directory
     * under the given location and renames it.
     *
     * The method will not rename the root if the requested location already
     * exists.
     *
     * @param string $dirname the location of the extracted ZIP package
     * @param string $rootdir the requested name of the root directory
     * @param array $files list of extracted files
     * @return array eventually amended list of extracted files
     */
    protected function rename_extracted_rootdir($dirname, $rootdir, array $files) {

        if (!is_dir($dirname)) {
            debugging('Unable to rename rootdir of non-existing content', DEBUG_DEVELOPER);
            return $files;
        }

        if (file_exists($dirname.'/'.$rootdir)) {
            debugging('Unable to rename rootdir to already existing folder', DEBUG_DEVELOPER);
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

    /**
     * Decode the request from the Moodle Plugins directory
     *
     * @param string $request submitted via 'installaddonrequest' HTTP parameter
     * @return stdClass|bool false on error, object otherwise
     */
    protected function decode_remote_request($request) {

        $data = base64_decode($request, true);

        if ($data === false) {
            return false;
        }

        $data = json_decode($data);

        if (is_null($data)) {
            return false;
        }

        if (!isset($data->name) or !isset($data->component) or !isset($data->version)) {
            return false;
        }

        $data->name = s(strip_tags($data->name));

        if ($data->component !== clean_param($data->component, PARAM_COMPONENT)) {
            return false;
        }

        list($plugintype, $pluginname) = normalize_component($data->component);

        if ($plugintype === 'core') {
            return false;
        }

        if ($data->component !== $plugintype.'_'.$pluginname) {
            return false;
        }

        // Keep this regex in sync with the one used by the download.moodle.org/api/x.y/pluginfo.php
        if (!preg_match('/^[0-9]+$/', $data->version)) {
            return false;
        }

        return $data;
    }
}


/**
 * General exception thrown by {@link tool_installaddon_installer} class
 *
 * @copyright 2013 David Mudrak <david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_installaddon_installer_exception extends moodle_exception {

    /**
     * @param string $errorcode exception description identifier
     * @param mixed $debuginfo debugging data to display
     */
    public function __construct($errorcode, $a=null, $debuginfo=null) {
        parent::__construct($errorcode, 'tool_installaddon', '', $a, print_r($debuginfo, true));
    }
}
