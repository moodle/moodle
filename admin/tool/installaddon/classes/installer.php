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
 * Provides tool_installaddon_installer class
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
        return new self();
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
            'major_version' => $this->get_site_major_version(),
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

        $action = new moodle_url('/admin/tool/installaddon/index.php');
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

    //// End of external API ///////////////////////////////////////////////////

    /**
     * @return string this site full name
     */
    protected function get_site_fullname() {
        global $SITE;

        return $SITE->fullname;
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
}
