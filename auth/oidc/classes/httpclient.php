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
 * HTTP clinet.
 *
 * @package auth_oidc
 * @author James McQuillan <james.mcquillan@remote-learner.net>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2014 onwards Microsoft, Inc. (http://microsoft.com/)
 */

namespace auth_oidc;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/lib/filelib.php');

/**
 * Implementation of \auth_oidc\httpclientinterface using Moodle CURL.
 */
class httpclient extends \curl implements \auth_oidc\httpclientinterface {
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
        require_once($CFG->dirroot.'/auth/oidc/version.php');
        return (isset($plugin->release)) ? $plugin->release : 'unknown';
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
     * Single HTTP Request
     *
     * @param string $url The URL to request
     * @param array $options
     * @return bool
     */
    protected function request($url, $options = array()) {
        $this->setHeader($this->get_clienttag_headers());
        $result = parent::request($url, $options);
        $this->resetHeader();
        return $result;
    }

    /**
     * HTTP POST method.
     *
     * @param string $url
     * @param array|string $params
     * @param array $options
     * @return bool
     */
    public function post($url, $params = '', $options = array()) {
        // Encode data to disable uploading files when values are prefixed @.
        if (is_array($params)) {
            $params = http_build_query($params, '', '&');
        }
        return parent::post($url, $params, $options);
    }
}
