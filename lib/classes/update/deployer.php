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
 * Defines classes used for updates.
 *
 * @package    core
 * @copyright  2011 David Mudrak <david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core\update;

use coding_exception, core_component, moodle_url;

defined('MOODLE_INTERNAL') || die();

/**
 * Implements a communication bridge to the mdeploy.php utility
 */
class deployer {

    const HTTP_PARAM_PREFIX     = 'updteautodpldata_';  // Hey, even Google has not heard of such a prefix! So it MUST be safe :-p.
    const HTTP_PARAM_CHECKER    = 'datapackagesize';    // Name of the parameter that holds the number of items in the received data items.

    /** @var \core\update\deployer holds the singleton instance */
    protected static $singletoninstance;
    /** @var moodle_url URL of a page that includes the deployer UI */
    protected $callerurl;
    /** @var moodle_url URL to return after the deployment */
    protected $returnurl;

    /**
     * Direct instantiation not allowed, use the factory method {@link self::instance()}
     */
    protected function __construct() {
    }

    /**
     * Sorry, this is singleton
     */
    protected function __clone() {
    }

    /**
     * Factory method for this class
     *
     * @return \core\update\deployer the singleton instance
     */
    public static function instance() {
        if (is_null(self::$singletoninstance)) {
            self::$singletoninstance = new self();
        }
        return self::$singletoninstance;
    }

    /**
     * Reset caches used by this script
     *
     * @param bool $phpunitreset is this called as a part of PHPUnit reset?
     */
    public static function reset_caches($phpunitreset = false) {
        if ($phpunitreset) {
            self::$singletoninstance = null;
        }
    }

    /**
     * Is automatic deployment enabled?
     *
     * @return bool
     */
    public function enabled() {
        global $CFG;

        if (!empty($CFG->disableupdateautodeploy)) {
            // The feature is prohibited via config.php.
            return false;
        }

        return get_config('updateautodeploy');
    }

    /**
     * Sets some base properties of the class to make it usable.
     *
     * @param moodle_url $callerurl the base URL of a script that will handle the class'es form data
     * @param moodle_url $returnurl the final URL to return to when the deployment is finished
     */
    public function initialize(moodle_url $callerurl, moodle_url $returnurl) {

        if (!$this->enabled()) {
            throw new coding_exception('Unable to initialize the deployer, the feature is not enabled.');
        }

        $this->callerurl = $callerurl;
        $this->returnurl = $returnurl;
    }

    /**
     * Has the deployer been initialized?
     *
     * Initialized deployer means that the following properties were set:
     * callerurl, returnurl
     *
     * @return bool
     */
    public function initialized() {

        if (!$this->enabled()) {
            return false;
        }

        if (empty($this->callerurl)) {
            return false;
        }

        if (empty($this->returnurl)) {
            return false;
        }

        return true;
    }

    /**
     * Returns a list of reasons why the deployment can not happen
     *
     * If the returned array is empty, the deployment seems to be possible. The returned
     * structure is an associative array with keys representing individual impediments.
     * Possible keys are: missingdownloadurl, missingdownloadmd5, notwritable.
     *
     * @param \core\update\info $info
     * @return array
     */
    public function deployment_impediments(info $info) {

        $impediments = array();

        if (empty($info->download)) {
            $impediments['missingdownloadurl'] = true;
        }

        if (empty($info->downloadmd5)) {
            $impediments['missingdownloadmd5'] = true;
        }

        if (!empty($info->download) and !$this->update_downloadable($info->download)) {
            $impediments['notdownloadable'] = true;
        }

        if (!$this->component_writable($info->component)) {
            $impediments['notwritable'] = true;
        }

        return $impediments;
    }

    /**
     * Check to see if the current version of the plugin seems to be a checkout of an external repository.
     *
     * @see core_plugin_manager::plugin_external_source()
     * @param \core\update\info $info
     * @return false|string
     */
    public function plugin_external_source(info $info) {

        $paths = core_component::get_plugin_types();
        list($plugintype, $pluginname) = core_component::normalize_component($info->component);
        $pluginroot = $paths[$plugintype].'/'.$pluginname;

        if (is_dir($pluginroot.'/.git')) {
            return 'git';
        }

        if (is_dir($pluginroot.'/CVS')) {
            return 'cvs';
        }

        if (is_dir($pluginroot.'/.svn')) {
            return 'svn';
        }

        return false;
    }

    /**
     * Prepares a renderable widget to confirm installation of an available update.
     *
     * @param \core\update\info $info component version to deploy
     * @return \renderable
     */
    public function make_confirm_widget(info $info) {

        if (!$this->initialized()) {
            throw new coding_exception('Illegal method call - deployer not initialized.');
        }

        $params = $this->data_to_params(array(
            'updateinfo' => (array)$info,   // See http://www.php.net/manual/en/language.types.array.php#language.types.array.casting .
        ));

        $widget = new \single_button(
            new moodle_url($this->callerurl, $params),
            get_string('updateavailableinstall', 'core_admin'),
            'post'
        );

        return $widget;
    }

    /**
     * Prepares a renderable widget to execute installation of an available update.
     *
     * @param \core\update\info $info component version to deploy
     * @param moodle_url $returnurl URL to return after the installation execution
     * @return \renderable
     */
    public function make_execution_widget(info $info, moodle_url $returnurl = null) {
        global $CFG;

        if (!$this->initialized()) {
            throw new coding_exception('Illegal method call - deployer not initialized.');
        }

        $pluginrootpaths = core_component::get_plugin_types();

        list($plugintype, $pluginname) = core_component::normalize_component($info->component);

        if (empty($pluginrootpaths[$plugintype])) {
            throw new coding_exception('Unknown plugin type root location', $plugintype);
        }

        list($passfile, $password) = $this->prepare_authorization();

        if (is_null($returnurl)) {
            $returnurl = new moodle_url('/admin');
        } else {
            $returnurl = $returnurl;
        }

        $params = array(
            'upgrade' => true,
            'type' => $plugintype,
            'name' => $pluginname,
            'typeroot' => $pluginrootpaths[$plugintype],
            'package' => $info->download,
            'md5' => $info->downloadmd5,
            'dataroot' => $CFG->dataroot,
            'dirroot' => $CFG->dirroot,
            'passfile' => $passfile,
            'password' => $password,
            'returnurl' => $returnurl->out(false),
        );

        if (!empty($CFG->proxyhost)) {
            // MDL-36973 - Beware - we should call just !is_proxybypass() here. But currently, our
            // cURL wrapper class does not do it. So, to have consistent behaviour, we pass proxy
            // setting regardless the $CFG->proxybypass setting. Once the {@link curl} class is
            // fixed, the condition should be amended.
            if (true or !is_proxybypass($info->download)) {
                if (empty($CFG->proxyport)) {
                    $params['proxy'] = $CFG->proxyhost;
                } else {
                    $params['proxy'] = $CFG->proxyhost.':'.$CFG->proxyport;
                }

                if (!empty($CFG->proxyuser) and !empty($CFG->proxypassword)) {
                    $params['proxyuserpwd'] = $CFG->proxyuser.':'.$CFG->proxypassword;
                }

                if (!empty($CFG->proxytype)) {
                    $params['proxytype'] = $CFG->proxytype;
                }
            }
        }

        $widget = new \single_button(
            new moodle_url('/mdeploy.php', $params),
            get_string('updateavailableinstall', 'core_admin'),
            'post'
        );

        return $widget;
    }

    /**
     * Returns array of data objects passed to this tool.
     *
     * @return array
     */
    public function submitted_data() {

        $data = $this->params_to_data($_POST);

        if (empty($data) or empty($data[self::HTTP_PARAM_CHECKER])) {
            return false;
        }

        if (!empty($data['updateinfo']) and is_object($data['updateinfo'])) {
            $updateinfo = $data['updateinfo'];
            if (!empty($updateinfo->component) and !empty($updateinfo->version)) {
                $data['updateinfo'] = new info($updateinfo->component, (array)$updateinfo);
            }
        }

        if (!empty($data['callerurl'])) {
            $data['callerurl'] = new moodle_url($data['callerurl']);
        }

        if (!empty($data['returnurl'])) {
            $data['returnurl'] = new moodle_url($data['returnurl']);
        }

        return $data;
    }

    /**
     * Handles magic getters and setters for protected properties.
     *
     * @param string $name method name, e.g. set_returnurl()
     * @param array $arguments arguments to be passed to the array
     */
    public function __call($name, array $arguments = array()) {

        if (substr($name, 0, 4) === 'set_') {
            $property = substr($name, 4);
            if (empty($property)) {
                throw new coding_exception('Invalid property name (empty)');
            }
            if (empty($arguments)) {
                $arguments = array(true); // Default value for flag-like properties.
            }
            // Make sure it is a protected property.
            $isprotected = false;
            $reflection = new \ReflectionObject($this);
            foreach ($reflection->getProperties(\ReflectionProperty::IS_PROTECTED) as $reflectionproperty) {
                if ($reflectionproperty->getName() === $property) {
                    $isprotected = true;
                    break;
                }
            }
            if (!$isprotected) {
                throw new coding_exception('Unable to set property - it does not exist or it is not protected');
            }
            $value = reset($arguments);
            $this->$property = $value;
            return;
        }

        if (substr($name, 0, 4) === 'get_') {
            $property = substr($name, 4);
            if (empty($property)) {
                throw new coding_exception('Invalid property name (empty)');
            }
            if (!empty($arguments)) {
                throw new coding_exception('No parameter expected');
            }
            // Make sure it is a protected property.
            $isprotected = false;
            $reflection = new \ReflectionObject($this);
            foreach ($reflection->getProperties(\ReflectionProperty::IS_PROTECTED) as $reflectionproperty) {
                if ($reflectionproperty->getName() === $property) {
                    $isprotected = true;
                    break;
                }
            }
            if (!$isprotected) {
                throw new coding_exception('Unable to get property - it does not exist or it is not protected');
            }
            return $this->$property;
        }
    }

    /**
     * Generates a random token and stores it in a file in moodledata directory.
     *
     * @return array of the (string)filename and (string)password in this order
     */
    public function prepare_authorization() {
        global $CFG;

        make_upload_directory('mdeploy/auth/');

        $attempts = 0;
        $success = false;

        while (!$success and $attempts < 5) {
            $attempts++;

            $passfile = $this->generate_passfile();
            $password = $this->generate_password();
            $now = time();

            $filepath = $CFG->dataroot.'/mdeploy/auth/'.$passfile;

            if (!file_exists($filepath)) {
                $success = file_put_contents($filepath, $password . PHP_EOL . $now . PHP_EOL, LOCK_EX);
                chmod($filepath, $CFG->filepermissions);
            }
        }

        if ($success) {
            return array($passfile, $password);

        } else {
            throw new \moodle_exception('unable_prepare_authorization', 'core_plugin');
        }
    }

    /* === End of external API === */

    /**
     * Prepares an array of HTTP parameters that can be passed to another page.
     *
     * @param array|object $data associative array or an object holding the data, data JSON-able
     * @return array suitable as a param for moodle_url
     */
    protected function data_to_params($data) {

        // Append some our own data.
        if (!empty($this->callerurl)) {
            $data['callerurl'] = $this->callerurl->out(false);
        }
        if (!empty($this->returnurl)) {
            $data['returnurl'] = $this->returnurl->out(false);
        }

        // Finally append the count of items in the package.
        $data[self::HTTP_PARAM_CHECKER] = count($data);

        // Generate params.
        $params = array();
        foreach ($data as $name => $value) {
            $transname = self::HTTP_PARAM_PREFIX.$name;
            $transvalue = json_encode($value);
            $params[$transname] = $transvalue;
        }

        return $params;
    }

    /**
     * Converts HTTP parameters passed to the script into native PHP data
     *
     * @param array $params such as $_REQUEST or $_POST
     * @return array data passed for this class
     */
    protected function params_to_data(array $params) {

        if (empty($params)) {
            return array();
        }

        $data = array();
        foreach ($params as $name => $value) {
            if (strpos($name, self::HTTP_PARAM_PREFIX) === 0) {
                $realname = substr($name, strlen(self::HTTP_PARAM_PREFIX));
                $realvalue = json_decode($value);
                $data[$realname] = $realvalue;
            }
        }

        return $data;
    }

    /**
     * Returns a random string to be used as a filename of the password storage.
     *
     * @return string
     */
    protected function generate_passfile() {
        return clean_param(uniqid('mdeploy_', true), PARAM_FILE);
    }

    /**
     * Returns a random string to be used as the authorization token
     *
     * @return string
     */
    protected function generate_password() {
        return complex_random_string();
    }

    /**
     * Checks if the given component's directory is writable
     *
     * For the purpose of the deployment, the web server process has to have
     * write access to all files in the component's directory (recursively) and for the
     * directory itself.
     *
     * @see worker::move_directory_source_precheck()
     * @param string $component normalized component name
     * @return boolean
     */
    protected function component_writable($component) {

        list($plugintype, $pluginname) = core_component::normalize_component($component);

        $directory = core_component::get_plugin_directory($plugintype, $pluginname);

        if (is_null($directory)) {
            throw new coding_exception('Unknown component location', $component);
        }

        return $this->directory_writable($directory);
    }

    /**
     * Checks if the mdeploy.php will be able to fetch the ZIP from the given URL
     *
     * This is mainly supposed to check if the transmission over HTTPS would
     * work. That is, if the CA certificates are present at the server.
     *
     * @param string $downloadurl the URL of the ZIP package to download
     * @return bool
     */
    protected function update_downloadable($downloadurl) {
        global $CFG;

        $curloptions = array(
            'CURLOPT_SSL_VERIFYHOST' => 2,      // This is the default in {@link curl} class but just in case.
            'CURLOPT_SSL_VERIFYPEER' => true,
        );

        $curl = new \curl(array('proxy' => true));
        $result = $curl->head($downloadurl, $curloptions);
        $errno = $curl->get_errno();
        if (empty($errno)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Checks if the directory and all its contents (recursively) is writable
     *
     * @param string $path full path to a directory
     * @return boolean
     */
    private function directory_writable($path) {

        if (!is_writable($path)) {
            return false;
        }

        if (is_dir($path)) {
            $handle = opendir($path);
        } else {
            return false;
        }

        $result = true;

        while ($filename = readdir($handle)) {
            $filepath = $path.'/'.$filename;

            if ($filename === '.' or $filename === '..') {
                continue;
            }

            if (is_dir($filepath)) {
                $result = $result && $this->directory_writable($filepath);

            } else {
                $result = $result && is_writable($filepath);
            }
        }

        closedir($handle);

        return $result;
    }
}
