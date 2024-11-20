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
 * Utility class for fetching IDP metadata.
 *
 * @package    auth_iomadsaml2
 * @author     Sam Chaffee
 * @copyright  Copyright (c) 2017 Blackboard Inc. (http://www.blackboard.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace auth_iomadsaml2;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../../../lib/filelib.php');

/**
 * Utility class for fetching IDP metadata.
 *
 * @package    auth_iomadsaml2
 * @copyright  Copyright (c) 2017 Blackboard Inc. (http://www.blackboard.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class metadata_fetcher {
    /**
     * @var array
     */
    private $curlinfo = [];

    /**
     * @var string
     */
    private $curlerror = '';

    /**
     * @var int
     */
    private $curlerrorno = 0;

    /**
     * Fetch metadata
     *
     * @param string $url
     * @param \curl $curl
     * @return bool
     * @throws \moodle_exception
     */
    public function fetch($url, $curl = null) {
        if (!$curl instanceof \curl) {
            $curl = new \curl();
        }
        $options = [
            'CURLOPT_SSL_VERIFYPEER' => true,
            'CURLOPT_SSL_VERIFYHOST' => true,
            'CURLOPT_CONNECTTIMEOUT' => 20,
            'CURLOPT_FOLLOWLOCATION' => 1,
            'CURLOPT_MAXREDIRS'      => 5,
            'CURLOPT_TIMEOUT'        => 300,
            'CURLOPT_RETURNTRANSFER' => true,
            'CURLOPT_NOBODY'         => false,
        ];
        $verifypeeroverride = get_config('auth_iomadsaml2', 'CURLOPT_SSL_VERIFYPEER');
        if ($verifypeeroverride === '0') {
            $options['CURLOPT_SSL_VERIFYPEER'] = false;
        }
        $verifyhostoverride = get_config('auth_iomadsaml2', 'CURLOPT_SSL_VERIFYHOST');
        if ($verifyhostoverride === '0') {
            $options['CURLOPT_SSL_VERIFYHOST'] = false;
        }
        $xml = $curl->get($url, $options);
        $this->curlinfo = $curl->get_info();
        $this->curlerrorno = $curl->get_errno();

        // If there is a curl errorno from curl_errno().
        if (!empty($this->curlerrorno)) {
            $this->curlerror = $xml;
            throw new \moodle_exception('metadatafetchfailed', 'auth_iomadsaml2', '', $xml);
        }
        // If http status code is empty something is wrong.
        if (empty($this->curlinfo['http_code'])) {
            throw new \moodle_exception('metadatafetchfailedunknown', 'auth_iomadsaml2');
        }
        // If http status code is not 200 then throw an exception.
        if ($this->curlinfo['http_code'] != 200) {
            throw new \moodle_exception('metadatafetchfailedstatus', 'auth_iomadsaml2', '', $this->curlinfo['http_code']);
        }
        return $xml;
    }

    /**
     * Get curl info
     *
     * @return array
     */
    public function get_curlinfo() {
        return $this->curlinfo;
    }

    /**
     * Get curl error no
     *
     * @return int
     */
    public function get_curlerrorno() {
        return $this->curlerrorno;
    }

    /**
     * Get curl error
     *
     * @return string
     */
    public function get_curlerror() {
        return $this->curlerror;
    }
}
