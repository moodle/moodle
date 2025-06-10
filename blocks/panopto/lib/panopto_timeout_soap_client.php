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
 * The Panopto soap client that uses timeouts
 *
 * @package block_panopto
 * @copyright Panopto 2020
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// This can't be defined Moodle internal because it is called from Panopto to authorize login.
// @codingStandardsIgnoreStart
/**
 * Panopto timeout soap client class.
 *
 * @package block_panopto
 * @copyright  Panopto 2009 - 2016
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class PanoptoTimeoutSoapClient extends SoapClient {
    /**
     * @var int $socket_timeout socket timeout
     */
    private $socket_timeout;

    /**
     * @var int $connect_timeout connection timeout
     */
    private $connect_timeout;

    /**
     * @var string $proxy_host proxy host
     */
    private $proxy_host;

    /**
     * @var int $proxy_port proxy port
     */
    private $proxy_port;

    /**
     * @var array $panoptocookies Panopto cookies
     */
    private $panoptocookies;

    /**
     * Set connection timeout
     *
     * @param int $connect_timeout
     */
    public function __setConnectionTimeout($connect_timeout) {
        $connect_timeout = intval($connect_timeout);

        if (!is_null($connect_timeout) && !is_int($connect_timeout)) {
            throw new Exception("Invalid connection timeout value");
        }

        $this->connect_timeout = $connect_timeout;
    }

    /**
     * Set socket timeout
     *
     * @param int $socket_timeout
     */
    public function __setSocketTimeout($socket_timeout) {
        $socket_timeout = intval($socket_timeout);

        if (!is_null($socket_timeout) && !is_int($socket_timeout)) {
            throw new Exception("Invalid socket timeout value");
        }

        $this->socket_timeout = $socket_timeout;
    }

    /**
     * Set proxy host
     *
     * @param string $proxy_host
     */
    public function __setProxyHost($proxy_host) {
        $this->proxy_host = $proxy_host;
    }

    /**
     * Set proxy port
     *
     * @param int $proxy_port
     */
    public function __setProxyPort($proxy_port) {
        $this->proxy_port = $proxy_port;
    }

    /**
     * Set Panopto cookies
     */
    public function getpanoptocookies() {
        return $this->panoptocookies;
    }

    /**
     * Create a SOAP request
     *
     * @param string $request XML SOAP request
     * @param string $location the URL to request
     * @param string $action the SOAP action
     * @param int $version the SOAP version
     * @param bool $one_way determine if response is expected or not
     */
    public function __doRequest($request, $location, $action, $version, $one_way = false): ?string {
        if (empty($this->socket_timeout) && empty($this->connect_timeout)) {
            // Call via parent because we require no timeout.
            $response = parent::__doRequest($request, $location, $action, $version, $one_way);

            $lastresponseheaders = $this->__getLastResponseHeaders();
            preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $lastresponseheaders, $matches);
            $this->panoptocookies = [];
            foreach ($matches[1] as $item) {
                parse_str($item, $cookie);
                $this->panoptocookies = array_merge($this->panoptocookies, $cookie);
            }
        } else {

            $curl = new \curl();
            $options = [
                'CURLOPT_VERBOSE' => false,
                'CURLOPT_RETURNTRANSFER' => true,
                'CURLOPT_HEADER' => true,
                'CURLOPT_HTTPHEADER' => ['Content-Type: text/xml',
                                              'SoapAction: ' . $action],
            ];

            if (!is_null($this->socket_timeout)) {
                $options['CURLOPT_TIMEOUT'] = $this->socket_timeout;
            }

            if (!is_null($this->connect_timeout)) {
                $options['CURLOPT_CONNECTTIMEOUT'] = $this->connect_timeout;
            }

            if (!empty($this->proxy_host)) {
                $options['CURLOPT_PROXY'] = $this->proxy_host;
            }

            if (!empty($this->proxy_port)) {
                $options['CURLOPT_PROXYPORT'] = $this->proxy_port;
            }

            // Depending on Moodle settings Moodle will not include  connect headers in the header size. This will break all curl calls from here.
            if (defined('CURLOPT_SUPPRESS_CONNECT_HEADERS')) {
                $options['CURLOPT_SUPPRESS_CONNECT_HEADERS'] = 0;
            }

            $response = $curl->post($location, $request, $options);

            // Get cookies.
            $actualresponseheaders = (isset($curl->info["header_size"])) ? substr($response, 0, $curl->info["header_size"]) : "";
            preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $actualresponseheaders, $matches);
            $this->panoptocookies = [];
            foreach ($matches[1] as $item) {
                parse_str($item, $cookie);
                $this->panoptocookies = array_merge($this->panoptocookies, $cookie);
            }

            $actualresponse = (isset($curl->info["header_size"])) ? substr($response, $curl->info["header_size"]) : "";

            if ($curl->get_errno()) {
                throw new Exception($response);
            }

            $response = $actualresponse;
        }

        // Return?
        if (!$one_way) {
            return $response;
        }
    }
}
// @codingStandardsIgnoreEnd
/* End of file panopto_timeout_soap_client.php */
