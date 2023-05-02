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
 * An httpclientinterface implementation, using curl class as backend and adding patch and merge methods.
 *
 * @package local_o365
 * @author Dongsheng Cai <dongsheng@moodle.com>
 * @author James McQuillan <james.mcquillan@remote-learner.net>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright Dongsheng Cai <dongsheng@moodle.com>; modified 2015 by Microsoft, Inc.
 */

namespace local_o365;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot.'/lib/filelib.php');

/**
 * An httpclientinterface implementation, using curl class as backend and adding patch and merge methods.
 */
class httpclient extends \curl implements \local_o365\httpclientinterface {
    /**
     * Generate a client tag.
     *
     * @return string A client tag.
     */
    protected function get_clienttag_headers() {
        global $CFG;

        $iid = sha1($CFG->wwwroot);
        $mdlver = $this->get_moodle_version();
        $ostype = php_uname('s');
        $osver = php_uname('r');
        $arch = php_uname('m');
        $ver = $this->get_plugin_version();

        $params = "lang=PHP; os={$ostype}; os_version={$osver}; arch={$arch}; version={$ver}; MoodleInstallId={$iid}";
        $clienttag = "Moodle/{$mdlver} ({$params})";
        return [
            'User-Agent: '.$clienttag,
            'X-ClientService-ClientTag: '.$clienttag,
        ];
    }

    /**
     * Get the current plugin version.
     *
     * @return string The current plugin version.
     */
    protected function get_plugin_version() {
        global $CFG;
        $plugin = new \stdClass;
        require_once($CFG->dirroot.'/local/o365/version.php');
        return $plugin->release;
    }

    /**
     * Get the current Moodle version.
     *
     * @return string The current Moodle version.
     */
    protected function get_moodle_version() {
        global $CFG;
        return $CFG->release;
    }

    /**
     * HTTP PATCH method
     *
     * @param string $url
     * @param array|string $params
     * @param array $options
     * @return bool
     */
    public function patch($url, $params = '', $options = array()) {
        $options['CURLOPT_CUSTOMREQUEST'] = 'PATCH';

        if (is_array($params)) {
            $this->_tmp_file_post_params = array();
            foreach ($params as $key => $value) {
                if ($value instanceof stored_file) {
                    $value->add_to_curl_request($this, $key);
                } else {
                    $this->_tmp_file_post_params[$key] = $value;
                }
            }
            $options['CURLOPT_POSTFIELDS'] = $this->_tmp_file_post_params;
            unset($this->_tmp_file_post_params);
        } else {
            // Var $params is the raw post data.
            $options['CURLOPT_POSTFIELDS'] = $params;
        }
        return $this->request($url, $options);
    }

    /**
     * HTTP MERGE method
     *
     * @param string $url
     * @param array|string $params
     * @param array $options
     * @return bool
     */
    public function merge($url, $params = '', $options = array()) {
        $options['CURLOPT_CUSTOMREQUEST'] = 'MERGE';

        if (is_array($params)) {
            $this->_tmp_file_post_params = array();
            foreach ($params as $key => $value) {
                if ($value instanceof stored_file) {
                    $value->add_to_curl_request($this, $key);
                } else {
                    $this->_tmp_file_post_params[$key] = $value;
                }
            }
            $options['CURLOPT_POSTFIELDS'] = $this->_tmp_file_post_params;
            unset($this->_tmp_file_post_params);
        } else {
            // Var $params is the raw post data.
            $options['CURLOPT_POSTFIELDS'] = $params;
        }
        return $this->request($url, $options);
    }

    /**
     * HTTP PUT method
     *
     * @param string $url
     * @param array $params
     * @param array $options
     * @return bool
     */
    public function put($url, $params = array(), $options = array()) {
        if (!isset($params['file'])) {
            throw new \moodle_exception('errorhttpclientnofileinput', 'local_o365');
        }
        if (is_file($params['file'])) {
            $fp = fopen($params['file'], 'r');
            $size = filesize($params['file']);
        } else {
            $fp = fopen('php://temp', 'w+');
            $size = strlen($params['file']);
            if (!$fp) {
                throw new \moodle_exception('errorhttpclientbadtempfileloc', 'local_o365');
            }
            fwrite($fp, $params['file']);
            fseek($fp, 0);
        }
        $options['CURLOPT_PUT'] = 1;
        $options['CURLOPT_INFILESIZE'] = $size;
        $options['CURLOPT_INFILE'] = $fp;
        if (!isset($this->options['CURLOPT_USERPWD'])) {
            $this->setopt(array('CURLOPT_USERPWD' => 'anonymous: noreply@moodle.org'));
        }

        $ret = $this->request($url, $options);
        fclose($fp);
        return $ret;
    }

    /**
     * HTTP download file method
     *
     * @param string $url
     * @param array $options
     * @return bool
     */
    public function download_file($url, $options = array()) {
        $url = str_replace(array('+', ' '), '%20', $url);
        return $this->request($url, $options);
    }
}
