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
 * Provides tool_installaddon_installer class.
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

    /** @var tool_installaddon_installfromzip_form */
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
     * @return tool_installaddon_installfromzip_form
     */
    public function get_installfromzip_form() {
        if (!is_null($this->installfromzipform)) {
            return $this->installfromzipform;
        }

        $action = $this->index_url();
        $customdata = array('installer' => $this);

        $this->installfromzipform = new tool_installaddon_installfromzip_form($action, $customdata);

        return $this->installfromzipform;
    }

    /**
     * Makes a unique writable storage for uploaded ZIP packages.
     *
     * We need the saved ZIP to survive across multiple requests so that it can
     * be used by the plugin manager after the installation is confirmed. In
     * other words, we cannot use make_request_directory() here.
     *
     * @return string full path to the directory
     */
    public function make_installfromzip_storage() {
        return make_unique_writable_directory(make_temp_directory('tool_installaddon'));
    }

    /**
     * Returns localised list of available plugin types
     *
     * @return array (string)plugintype => (string)plugin name
     */
    public function get_plugin_types_menu() {
        global $CFG;

        $pluginman = core_plugin_manager::instance();

        $menu = array('' => get_string('choosedots'));
        foreach (array_keys($pluginman->get_plugin_types()) as $plugintype) {
            $menu[$plugintype] = $pluginman->plugintype_name($plugintype).' ('.$plugintype.')';
        }

        return $menu;
    }

    /**
     * Hook method to handle the remote request to install an add-on
     *
     * This is used as a callback when the admin picks a plugin version in the
     * Moodle Plugins directory and is redirected back to their site to install
     * it.
     *
     * This hook is called early from admin/tool/installaddon/index.php page so that
     * it has opportunity to take over the UI and display the first confirmation screen.
     *
     * @param tool_installaddon_renderer $output
     * @param string|null $request
     */
    public function handle_remote_request(tool_installaddon_renderer $output, $request) {

        if (is_null($request)) {
            return;
        }

        $data = $this->decode_remote_request($request);

        if ($data === false) {
            echo $output->remote_request_invalid_page($this->index_url());
            exit();
        }

        list($plugintype, $pluginname) = core_component::normalize_component($data->component);
        $pluginman = core_plugin_manager::instance();

        $plugintypepath = $pluginman->get_plugintype_root($plugintype);

        if (file_exists($plugintypepath.'/'.$pluginname)) {
            echo $output->remote_request_alreadyinstalled_page($data, $this->index_url());
            exit();
        }

        if (!$pluginman->is_plugintype_writable($plugintype)) {
            $continueurl = $this->index_url(array('installaddonrequest' => $request));
            echo $output->remote_request_permcheck_page($data, $plugintypepath, $continueurl, $this->index_url());
            exit();
        }

        if (!$pluginman->is_remote_plugin_installable($data->component, $data->version, $reason)) {
            $data->reason = $reason;
            echo $output->remote_request_non_installable_page($data, $this->index_url());
            exit();
        }

        $continueurl = $this->index_url(array(
            'installremote' => $data->component,
            'installremoteversion' => $data->version
        ));

        echo $output->remote_request_confirm_page($data, $continueurl, $this->index_url());
        exit();
    }

    /**
     * Detect the given plugin's component name
     *
     * Only plugins that declare valid $plugin->component value in the version.php
     * are supported.
     *
     * @param string $zipfilepath full path to the saved ZIP file
     * @return string|bool declared component name or false if unable to detect
     */
    public function detect_plugin_component($zipfilepath) {

        $workdir = make_request_directory();
        $versionphp = $this->extract_versionphp_file($zipfilepath, $workdir);

        if (empty($versionphp)) {
            return false;
        }

        return $this->detect_plugin_component_from_versionphp(file_get_contents($workdir.'/'.$versionphp));
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

        list($plugintype, $pluginname) = core_component::normalize_component($data->component);

        if ($plugintype === 'core') {
            return false;
        }

        if ($data->component !== $plugintype.'_'.$pluginname) {
            return false;
        }

        if (!core_component::is_valid_plugin_name($plugintype, $pluginname)) {
            return false;
        }

        $plugintypes = core_component::get_plugin_types();
        if (!isset($plugintypes[$plugintype])) {
            return false;
        }

        // Keep this regex in sync with the one used by the download.moodle.org/api/x.y/pluginfo.php
        if (!preg_match('/^[0-9]+$/', $data->version)) {
            return false;
        }

        return $data;
    }

    /**
     * Extracts the version.php from the given plugin ZIP file into the target directory
     *
     * @param string $zipfilepath full path to the saved ZIP file
     * @param string $targetdir full path to extract the file to
     * @return string|bool path to the version.php within the $targetpath; false on error (e.g. not found)
     */
    protected function extract_versionphp_file($zipfilepath, $targetdir) {
        global $CFG;
        require_once($CFG->libdir.'/filelib.php');

        $fp = get_file_packer('application/zip');
        $files = $fp->list_files($zipfilepath);

        if (empty($files)) {
            return false;
        }

        $rootdirname = null;
        $found = null;

        foreach ($files as $file) {
            // Valid plugin ZIP package has just one root directory with all
            // files in it.
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

            // If we reached the valid version.php file, remember it.
            if ($pathnameitems[1] === 'version.php' and !$file->is_directory and $file->size > 0) {
                $found = $file->pathname;
            }
        }

        if (empty($found)) {
            return false;
        }

        $extracted = $fp->extract_to_pathname($zipfilepath, $targetdir, array($found));

        if (empty($extracted)) {
            return false;
        }

        // The following syntax uses function array dereferencing, added in PHP 5.4.0.
        return array_keys($extracted)[0];
    }

    /**
     * Return the plugin component declared in its version.php file
     *
     * @param string $code the contents of the version.php file
     * @return string|bool declared plugin component or false if unable to detect
     */
    protected function detect_plugin_component_from_versionphp($code) {

        $result = preg_match_all('#^\s*\$plugin\->component\s*=\s*([\'"])(.+?_.+?)\1\s*;#m', $code, $matches);

        // Return if and only if the single match was detected.
        if ($result === 1 and !empty($matches[2][0])) {
            return $matches[2][0];
        }

        return false;
    }
}
