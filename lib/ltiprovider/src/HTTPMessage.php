<?php

namespace IMSGlobal\LTI;

/**
 * Class to represent an HTTP message
 *
 * @author  Stephen P Vickers <svickers@imsglobal.org>
 * @copyright  IMS Global Learning Consortium Inc
 * @date  2016
 * @version 3.0.0
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 */
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
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->url);
            if (!empty($this->requestHeaders)) {
                curl_setopt($ch, CURLOPT_HTTPHEADER, $this->requestHeaders);
            } else {
                curl_setopt($ch, CURLOPT_HEADER, 0);
            }
            if ($this->method === 'POST') {
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $this->request);
            } else if ($this->method !== 'GET') {
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $this->method);
                if (!is_null($this->request)) {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $this->request);
                }
            }
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLINFO_HEADER_OUT, true);
            curl_setopt($ch, CURLOPT_HEADER, true);
            $chResp = curl_exec($ch);
            $this->ok = $chResp !== false;
            if ($this->ok) {
                $chResp = str_replace("\r\n", "\n", $chResp);
                $chRespSplit = explode("\n\n", $chResp, 2);
                if ((count($chRespSplit) > 1) && (substr($chRespSplit[1], 0, 5) === 'HTTP/')) {
                    $chRespSplit = explode("\n\n", $chRespSplit[1], 2);
                }
                $this->responseHeaders = $chRespSplit[0];
                $resp = $chRespSplit[1];
                $this->status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $this->ok = $this->status < 400;
                if (!$this->ok) {
                    $this->error = curl_error($ch);
                }
            }
            $this->requestHeaders = str_replace("\r\n", "\n", curl_getinfo($ch, CURLINFO_HEADER_OUT));
            curl_close($ch);
            $this->response = $resp;
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
