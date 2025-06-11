<?php

namespace IMSGlobal\LTI;

global $CFG;
require_once($CFG->libdir . '/filelib.php');

/**
 * Class to represent an HTTP message
 *
 * @author  Stephen P Vickers <svickers@imsglobal.org>
 * @copyright  IMS Global Learning Consortium Inc
 * @date  2016
 * @version 3.0.0
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 */
#[\AllowDynamicProperties]
class HTTPMessage
{

/**
 * True if message was sent successfully.
 *
 * @var boolean $ok
 */
    public $ok = false;

/**
 * Request body.
 *
 * @var request $request
 */
    public $request = null;

/**
 * Request headers.
 *
 * @var request_headers $requestHeaders
 */
    public $requestHeaders = '';

/**
 * Response body.
 *
 * @var response $response
 */
    public $response = null;

/**
 * Response headers.
 *
 * @var response_headers $responseHeaders
 */
    public $responseHeaders = '';

/**
 * Status of response (0 if undetermined).
 *
 * @var status $status
 */
    public $status = 0;

/**
 * Error message
 *
 * @var error $error
 */
    public $error = '';

/**
 * Request URL.
 *
 * @var url $url
 */
    private $url = null;

/**
 * Request method.
 *
 * @var method $method
 */
    private $method = null;

/**
 * Class constructor.
 *
 * @param string $url     URL to send request to
 * @param string $method  Request method to use (optional, default is GET)
 * @param mixed  $params  Associative array of parameter values to be passed or message body (optional, default is none)
 * @param string $header  Values to include in the request header (optional, default is none)
 */
    function __construct($url, $method = 'GET', $params = null, $header = null)
    {

        $this->url = $url;
        $this->method = strtoupper($method);
        if (is_array($params)) {
            $this->request = http_build_query($params);
        } else {
            $this->request = $params;
        }
        if (!empty($header)) {
            $this->requestHeaders = explode("\n", $header);
        }

    }

/**
 * Send the request to the target URL.
 *
 * @return boolean True if the request was successful
 */
    public function send()
    {

        $this->ok = false;
        // Try using curl if available
        if (function_exists('curl_init')) {
            $resp = '';
            $chResp = '';

            $curl = new \curl();
            $options = [
                'CURLOPT_HEADER' => true,
                'CURLINFO_HEADER_OUT' => true,
            ];
            if (!empty($this->requestHeaders)) {
                $options['CURLOPT_HTTPHEADER'] = $this->requestHeaders;
            } else {
                $options['CURLOPT_HEADER'] = 0;
            }
            if ($this->method === 'POST') {
                $chResp = $curl->post($this->url, $this->request, $options);
            } else if ($this->method !== 'GET') {
                if (!is_null($this->request)) {
                    $chResp = $curl->post($this->url, $this->request, $options);
                }
            } else {
                $chResp = $curl->get($this->url, null, $options);
            }
            $info = $curl->get_info();

            if (!$curl->get_errno() && !$curl->error) {
                $chResp = str_replace("\r\n", "\n", $chResp);
                $chRespSplit = explode("\n\n", $chResp, 2);
                if ((count($chRespSplit) > 1) && (substr($chRespSplit[1], 0, 5) === 'HTTP/')) {
                    $chRespSplit = explode("\n\n", $chRespSplit[1], 2);
                }
                $this->responseHeaders = $chRespSplit[0];
                $resp = $chRespSplit[1];
                $this->status = $info['http_code'];
                $this->ok = $this->status < 400;
                if (!$this->ok) {
                    $this->error = $curl->error;
                }
            } else {
                $this->error = $curl->error;
                $resp = $chResp;
            }

            $this->response = $resp;
            $this->requestHeaders = !empty($info['request_header']) ? str_replace("\r\n", "\n", $info['request_header']) : '';
        } else {
            // Try using fopen if curl was not available
            $opts = array('method' => $this->method,
                          'content' => $this->request
                         );
            if (!empty($this->requestHeaders)) {
                $opts['header'] = $this->requestHeaders;
            }
            try {
                $ctx = stream_context_create(array('http' => $opts));
                $fp = @fopen($this->url, 'rb', false, $ctx);
                if ($fp) {
                    $resp = @stream_get_contents($fp);
                    $this->ok = $resp !== false;
                }
            } catch (\Exception $e) {
                $this->ok = false;
            }
        }

        return $this->ok;

    }

}
