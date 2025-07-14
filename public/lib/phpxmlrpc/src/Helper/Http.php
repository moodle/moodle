<?php

namespace PhpXmlRpc\Helper;

use PhpXmlRpc\Exception\HttpException;
use PhpXmlRpc\PhpXmlRpc;
use PhpXmlRpc\Traits\LoggerAware;

class Http
{
    use LoggerAware;

    /**
     * Decode a string that is encoded with "chunked" transfer encoding as defined in RFC 2068 par. 19.4.6.
     * Code shamelessly stolen from nusoap library by Dietrich Ayala.
     * @internal this function will become protected in the future
     *
     * @param string $buffer the string to be decoded
     * @return string
     */
    public static function decodeChunked($buffer)
    {
        // length := 0
        $length = 0;
        $new = '';

        // read chunk-size, chunk-extension (if any) and crlf
        // get the position of the linebreak
        $chunkEnd = strpos($buffer, "\r\n") + 2;
        $temp = substr($buffer, 0, $chunkEnd);
        $chunkSize = hexdec(trim($temp));
        $chunkStart = $chunkEnd;
        while ($chunkSize > 0) {
            $chunkEnd = strpos($buffer, "\r\n", $chunkStart + $chunkSize);

            // just in case we got a broken connection
            if ($chunkEnd == false) {
                $chunk = substr($buffer, $chunkStart);
                // append chunk-data to entity-body
                $new .= $chunk;
                $length += strlen($chunk);
                break;
            }

            // read chunk-data and crlf
            $chunk = substr($buffer, $chunkStart, $chunkEnd - $chunkStart);
            // append chunk-data to entity-body
            $new .= $chunk;
            // length := length + chunk-size
            $length += strlen($chunk);
            // read chunk-size and crlf
            $chunkStart = $chunkEnd + 2;

            $chunkEnd = strpos($buffer, "\r\n", $chunkStart) + 2;
            if ($chunkEnd == false) {
                break; // just in case we got a broken connection
            }
            $temp = substr($buffer, $chunkStart, $chunkEnd - $chunkStart);
            $chunkSize = hexdec(trim($temp));
            $chunkStart = $chunkEnd;
        }

        return $new;
    }

    /**
     * Parses HTTP an http response's headers and separates them from the body.
     *
     * @param string $data the http response, headers and body. It will be stripped of headers
     * @param bool $headersProcessed when true, we assume that response inflating and dechunking has been already carried out
     * @param int $debug when > 0, logs to screen messages detailing info about the parsed data
     * @return array with keys 'headers', 'cookies', 'raw_data' and 'status_code'
     * @throws HttpException
     *
     * @todo if $debug is < 0, we could avoid populating 'raw_data' in the returned value - but that would
     *       be a weird API... (note that we still need to always have headers parsed for content charset)
     */
    public function parseResponseHeaders(&$data, $headersProcessed = false, $debug = 0)
    {
        $httpResponse = array('raw_data' => $data, 'headers'=> array(), 'cookies' => array(), 'status_code' => null);

        // Support "web-proxy-tunnelling" connections for https through proxies
        if (preg_match('/^HTTP\/1\.[0-1] 200 Connection established/', $data)) {
            // Look for CR/LF or simple LF as line separator (even though it is not valid http)
            $pos = strpos($data, "\r\n\r\n");
            if ($pos || is_int($pos)) {
                $bd = $pos + 4;
            } else {
                $pos = strpos($data, "\n\n");
                if ($pos || is_int($pos)) {
                    $bd = $pos + 2;
                } else {
                    // No separation between response headers and body: fault?
                    $bd = 0;
                }
            }
            if ($bd) {
                // this filters out all http headers from proxy. maybe we could take them into account, too?
                $data = substr($data, $bd);
            } else {
                $this->getLogger()->error('XML-RPC: ' . __METHOD__ . ': HTTPS via proxy error, tunnel connection possibly failed');
                throw new HttpException(PhpXmlRpc::$xmlrpcstr['http_error'] . ' (HTTPS via proxy error, tunnel connection possibly failed)', PhpXmlRpc::$xmlrpcerr['http_error']);
            }
        }

        // Strip HTTP 1.1 100 Continue header if present
        while (preg_match('/^HTTP\/1\.1 1[0-9]{2} /', $data)) {
            $pos = strpos($data, 'HTTP', 12);
            // server sent a Continue header without any (valid) content following...
            // give the client a chance to know it
            if (!$pos && !is_int($pos)) {
                /// @todo this construct works fine in php 3, 4 and 5 - 8; would it not be enough to have !== false now ?

                break;
            }
            $data = substr($data, $pos);
        }

        // When using Curl to query servers using Digest Auth, we get back a double set of http headers.
        // Same when following redirects
        // We strip out the 1st...
        /// @todo we should let the caller know that there was a redirect involved
        if ($headersProcessed && preg_match('/^HTTP\/[0-9](?:\.[0-9])? (?:401|30[1278]) /', $data)) {
            if (preg_match('/(\r?\n){2}HTTP\/[0-9](?:\.[0-9])? 200 /', $data)) {
                $data = preg_replace('/^HTTP\/[0-9](?:\.[0-9])? (?:401|30[1278]) .+?(?:\r?\n){2}(HTTP\/[0-9.]+ 200 )/s', '$1', $data, 1);
            }
        }

        if (preg_match('/^HTTP\/([0-9](?:\.[0-9])?) ([0-9]{3}) /', $data, $matches)) {
            $httpResponse['protocol_version'] = $matches[1];
            $httpResponse['status_code'] = $matches[2];
        }

        if ($httpResponse['status_code'] !== '200') {
            $errstr = substr($data, 0, strpos($data, "\n") - 1);
            $this->getLogger()->error('XML-RPC: ' . __METHOD__ . ': HTTP error, got response: ' . $errstr);
            throw new HttpException(PhpXmlRpc::$xmlrpcstr['http_error'] . ' (' . $errstr . ')', PhpXmlRpc::$xmlrpcerr['http_error'], null, $httpResponse['status_code']);
        }

        // be tolerant to usage of \n instead of \r\n to separate headers and data (even though it is not valid http)
        $pos = strpos($data, "\r\n\r\n");
        if ($pos || is_int($pos)) {
            $bd = $pos + 4;
        } else {
            $pos = strpos($data, "\n\n");
            if ($pos || is_int($pos)) {
                $bd = $pos + 2;
            } else {
                // No separation between response headers and body: fault?
                // we could take some action here instead of going on...
                $bd = 0;
            }
        }

        // be tolerant to line endings, and extra empty lines
        $ar = preg_split("/\r?\n/", trim(substr($data, 0, $pos)));

        foreach ($ar as $line) {
            // take care of (multi-line) headers and cookies
            $arr = explode(':', $line, 2);
            if (count($arr) > 1) {
                /// @todo according to https://www.rfc-editor.org/rfc/rfc7230#section-3.2.4, we should reject with error
                ///       400 any messages where a space is present between the header name and colon
                $headerName = strtolower(trim($arr[0]));
                if ($headerName == 'set-cookie') {
                    $cookie = $arr[1];
                    // glue together all received cookies, using a comma to separate them (same as php does with getallheaders())
                    if (isset($httpResponse['headers'][$headerName])) {
                        $httpResponse['headers'][$headerName] .= ', ' . trim($cookie);
                    } else {
                        $httpResponse['headers'][$headerName] = trim($cookie);
                    }
                    // parse cookie attributes, in case user wants to correctly honour them
                    // @todo support for server sending multiple time cookie with same name, but using different PATHs
                    $cookie = explode(';', $cookie);
                    foreach ($cookie as $pos => $val) {
                        $val = explode('=', $val, 2);
                        $tag = trim($val[0]);
                        $val = isset($val[1]) ? trim($val[1]) : '';
                        if ($pos === 0) {
                            $cookieName = $tag;
                            // if present, we have strip leading and trailing " chars from $val
                            if (preg_match('/^"(.*)"$/', $val, $matches)) {
                                $val = $matches[1];
                            }
                            $httpResponse['cookies'][$cookieName] = array('value' => urldecode($val));
                        } else {
                            $httpResponse['cookies'][$cookieName][$tag] = $val;
                        }
                    }
                } else {
                    /// @todo some other headers (the ones that allow a CSV list of values) do allow many values to be
                    ///       passed using multiple header lines.
                    ///       We should add content to $xmlrpc->_xh['headers'][$headerName] instead of replacing it for those...
                    $httpResponse['headers'][$headerName] = trim($arr[1]);
                }
            } elseif (isset($headerName)) {
                /// @todo improve this: 1. check that the line starts with a space or tab; 2. according to
                ///       https://www.rfc-editor.org/rfc/rfc7230#section-3.2.4, we should flat out refuse these messages
                $httpResponse['headers'][$headerName] .= ' ' . trim($line);
            }
        }

        $data = substr($data, $bd);

        if ($debug && count($httpResponse['headers'])) {
            $msg = '';
            foreach ($httpResponse['headers'] as $header => $value) {
                $msg .= "HEADER: $header: $value\n";
            }
            foreach ($httpResponse['cookies'] as $header => $value) {
                $msg .= "COOKIE: $header={$value['value']}\n";
            }
            $this->getLogger()->debug($msg);
        }

        // if CURL was used for the call, http headers have been processed, and dechunking + reinflating have been carried out
        if (!$headersProcessed) {

            // Decode chunked encoding sent by http 1.1 servers
            if (isset($httpResponse['headers']['transfer-encoding']) && $httpResponse['headers']['transfer-encoding'] == 'chunked') {
                if (!$data = static::decodeChunked($data)) {
                    $this->getLogger()->error('XML-RPC: ' . __METHOD__ . ': errors occurred when trying to rebuild the chunked data received from server');
                    throw new HttpException(PhpXmlRpc::$xmlrpcstr['dechunk_fail'], PhpXmlRpc::$xmlrpcerr['dechunk_fail'], null, $httpResponse['status_code']);
                }
            }

            // Decode gzip-compressed stuff
            // code shamelessly inspired from nusoap library by Dietrich Ayala
            if (isset($httpResponse['headers']['content-encoding'])) {
                $httpResponse['headers']['content-encoding'] = str_replace('x-', '', $httpResponse['headers']['content-encoding']);
                if ($httpResponse['headers']['content-encoding'] == 'deflate' || $httpResponse['headers']['content-encoding'] == 'gzip') {
                    // if decoding works, use it. else assume data wasn't gzencoded
                    if (function_exists('gzinflate')) {
                        if ($httpResponse['headers']['content-encoding'] == 'deflate' && $degzdata = @gzuncompress($data)) {
                            $data = $degzdata;
                            if ($debug) {
                                $this->getLogger()->debug("---INFLATED RESPONSE---[" . strlen($data) . " chars]---\n$data\n---END---");
                            }
                        } elseif ($httpResponse['headers']['content-encoding'] == 'gzip' && $degzdata = @gzinflate(substr($data, 10))) {
                            $data = $degzdata;
                            if ($debug) {
                                $this->getLogger()->debug("---INFLATED RESPONSE---[" . strlen($data) . " chars]---\n$data\n---END---");
                            }
                        } else {
                            $this->getLogger()->error('XML-RPC: ' . __METHOD__ . ': errors occurred when trying to decode the deflated data received from server');
                            throw new HttpException(PhpXmlRpc::$xmlrpcstr['decompress_fail'], PhpXmlRpc::$xmlrpcerr['decompress_fail'], null, $httpResponse['status_code']);
                        }
                    } else {
                        $this->getLogger()->error('XML-RPC: ' . __METHOD__ . ': the server sent deflated data. Your php install must have the Zlib extension compiled in to support this.');
                        throw new HttpException(PhpXmlRpc::$xmlrpcstr['cannot_decompress'], PhpXmlRpc::$xmlrpcerr['cannot_decompress'], null, $httpResponse['status_code']);
                    }
                }
            }
        } // end of 'if needed, de-chunk, re-inflate response'

        return $httpResponse;
    }

    /**
     * Parses one of the http headers which can have a list of values with quality param.
     * @see https://www.rfc-editor.org/rfc/rfc7231#section-5.3.1
     *
     * @param string $header
     * @return string[]
     */
    public function parseAcceptHeader($header)
    {
        $accepted = array();
        foreach(explode(',', $header) as $c) {
            if (preg_match('/^([^;]+); *q=([0-9.]+)/', $c, $matches)) {
                $c = $matches[1];
                $w = $matches[2];
            } else {
                $c = preg_replace('/;.*/', '', $c);
                $w = 1;
            }
            $accepted[(trim($c))] = $w;
        }
        arsort($accepted);
        return array_keys($accepted);
    }
}
