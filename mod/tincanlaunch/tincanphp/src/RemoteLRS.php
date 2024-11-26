<?php
/*
    Copyright 2014 Rustici Software

    Licensed under the Apache License, Version 2.0 (the "License");
    you may not use this file except in compliance with the License.
    You may obtain a copy of the License at

    http://www.apache.org/licenses/LICENSE-2.0

    Unless required by applicable law or agreed to in writing, software
    distributed under the License is distributed on an "AS IS" BASIS,
    WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
    See the License for the specific language governing permissions and
    limitations under the License.
*/

namespace TinCan;

class RemoteLRS implements LRSInterface
{
    use ArraySetterTrait;

    private static $whitelistedHeaders = array(
        'Content-Type'                        => 'contentType',
        'Date'                                => 'date',
        'Last-Modified'                       => 'lastModified',
        'Etag'                                => 'etag',
        'X-Experience-API-Consistent-Through' => 'apiConsistentThrough',
        'X-Experience-API-Version'            => 'apiVersion',
    );
    protected $endpoint;
    protected $version;
    protected $auth;
    protected $proxy;
    protected $headers;
    protected $extended;

    public function __construct() {
        $_num_args = func_num_args();
        if ($_num_args == 1) {
            $arg = func_get_arg(0);

            $this->_fromArray($arg);

            if (! isset($this->version)) {
                $this->setVersion(Version::latest());
            }
            if (! isset($this->auth) && isset($arg['username']) && isset($arg['password'])) {
                $this->setAuth($arg['username'], $arg['password']);
            }
        }
        elseif ($_num_args === 3) {
            $this->setEndpoint(func_get_arg(0));
            $this->setVersion(func_get_arg(1));
            $this->setAuth(func_get_arg(2));
        }
        elseif ($_num_args === 4) {
            $this->setEndpoint(func_get_arg(0));
            $this->setVersion(func_get_arg(1));
            $this->setAuth(func_get_arg(2), func_get_arg(3));
        }
        else {
            $this->setVersion(Version::latest());
        }
    }

    protected function sendRequest($method, $resource) {
        $options = func_num_args() === 3 ? func_get_arg(2) : array();

        //
        // allow for full path requests, for instance as used by the
        // moreStatements method which is based on server root rather
        // than the stored endpoint
        //
        $url = $resource;
        if (! preg_match('/^http/', $resource)) {
            $url = $this->endpoint . $resource;
        }
        $http = array(
            //
            // redirects are not part of the spec so LRSs shouldn't be returning them
            //
            'max_redirects' => 0,

            //
            // this is here for some proxy handling
            //
            'request_fulluri' => 1,

            //
            // switching this to false causes non-2xx/3xx status codes to throw exceptions
            // but we need to handle the "error" status codes ourselves in some cases
            //
            'ignore_errors' => true,

            'method' => $method,
            'header' => array(
                'X-Experience-API-Version: ' . $this->version
            ),
        );
        if (isset($this->auth)) {
            array_push($http['header'], 'Authorization: ' . $this->auth);
        }
        if (isset($this->proxy)) {
            $http['proxy'] = $this->proxy;
        }

        if (isset($this->headers) && count($this->headers) > 0) {
            foreach ($this->headers as $k => $v) {
                array_push($http['header'], "$k: $v");
            }
        }

        if (isset($options['headers'])) {
            foreach ($options['headers'] as $k => $v) {
                array_push($http['header'], "$k: $v");
            }
        }
        if (isset($options['params']) && count($options['params']) > 0) {
            $url .= '?' . http_build_query($options['params'], null, '&', PHP_QUERY_RFC3986);
        }

        if (($method === 'PUT' || $method === 'POST') && isset($options['content'])) {
            $http['content'] = $options['content'];
            if (is_string($options['content'])) {
                array_push($http['header'], 'Content-length: ' . strlen($options['content']));
            }
        }

        $success = false;

        //
        // errors from fopen are reported to PHP as E_WARNING which prevents us
        // from getting a reasonable message, so set an error handler here for
        // the immediate call to turn it into an exception, and then restore
        // normal handling
        //
        set_error_handler(
            function ($errno, $errstr, $errfile, $errline, array $errcontext) {
                // "!== false" is intentional. strpos() can return 0, which is falsey, but returning
                // 0 matches our "true" condition. Using strict equality to avoid that confusion.
                if ($errno == E_NOTICE && strpos($errstr, 'Array to string conversion') !== false) {
                    // The way HHVM handles array comparison results in a Notice being raised in fopen(),
                    // but that's expected here and won't affect functionality. We don't want to throw
                    // those Notices as Errors. Checking if this is a Notice before looking at the
                    // contents of the string to hopefully minimize any performance impact here.
                    // See https://github.com/facebook/hhvm/issues/1561 for the "won't fix" from HHVM.

                    return true;
                }

                throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
            }
        );

        $fp = null;
        $response = null;

        try {
            $context = stream_context_create(array( 'http' => $http ));
            $fp = fopen($url, 'rb', false, $context);

            if (! $fp) {
                $content = "Request failed: $php_errormsg";
            }
        }
        catch (\ErrorException $ex) {
            $content = "Request failed: $ex";
        }

        restore_error_handler();

        if ($fp) {
            $metadata = stream_get_meta_data($fp);
            $content  = stream_get_contents($fp);

            $response = $this->_parseMetadata($metadata, $options);

            //
            // keep a copy of the raw content, the methods expecting
            // an LRS response may handle the content, for instance
            // querying statements takes the returned value and converts
            // it to Statement objects (really StatementsResult but who
            // is counting), etc. but a user may want the original raw
            // returned content untouched, do the same with the metadata
            // because it feels like a good practice
            //
            $response['_content']  = $content;
            $response['_metadata'] = $metadata;

            //
            // Content-Type won't be set in the case of a 204 (and potentially others)
            //
            if (isset($response['headers']['contentType']) && $response['headers']['contentType'] === "multipart/mixed") {
                $content = $this->_parseMultipart($response['headers']['contentTypeBoundary'], $content);
            }

            if (($response['status'] >= 200 && $response['status'] < 300) || ($response['status'] === 404 && isset($options['ignore404']) && $options['ignore404'])) {
                $success = true;
            }
            elseif ($response['status'] >= 300 && $response['status'] < 400) {
                $content = "Unsupported status code: " . $response['status'] . " (LRS should not redirect)";
            }
        }

        return new LRSResponse($success, $content, $response);
    }

    private function _parseMetadata($metadata) {
        $result = array();

        // simulate a 100 Continue to cause our loop
        // to run until it sets something other than a 100
        $result['status'] = 100;

        while ($result['status'] == 100) {
            $status_line = array_shift($metadata['wrapper_data']);
            $status_parts = explode(' ', $status_line);
            $result['status'] = intval($status_parts[1]);
        }

        //
        // pull out whitelisted headers
        //
        foreach (self::$whitelistedHeaders as $header => $prop) {
            foreach ($metadata['wrapper_data'] as $line) {
                if (stripos($line, $header . ':') === 0) {
                    $result['headers'][$prop] = ltrim(substr($line, (strlen($header . ':'))));
                    break;
                }
            }
        }

        if (isset($result['headers']['contentType'])) {
            $contentType_parts = array_map('trim', explode(';', $result['headers']['contentType']));

            $result['headers']['contentType'] = $contentType_parts[0];
            for ($i = 1; $i < count($contentType_parts); $i++) {
                $pair = array_map('trim', explode("=", $contentType_parts[$i], 2));
                if ($pair[0] === 'charset') {
                    $result['headers']['contentTypeCharset'] = $pair[1];
                }
                elseif ($pair[0] === 'boundary') {
                    $result['headers']['contentTypeBoundary'] = $pair[1];
                }
            }
        }

        return $result;
    }

    private function _parseMultipart($boundary, $content) {
        $parts = array();

        foreach (explode("--$boundary", $content) as $part) {
            $part = ltrim($part, "\r\n");
            if ($part === '') {
                continue;
            }
            elseif ($part === '--') {
                break;
            }
            list($header, $body) = explode("\r\n\r\n", $part, 2);

            //
            // the body has a CRLF on it before the boundary per the RFC
            // so we need to remove it, but we only want to remove one
            // because the body itself may include a trailing CRLF so
            // PHP's rtrim function won't work in this case because it
            // removes all of them
            //
            $body = preg_replace('/\r\n$/', '', $body, 1);

            array_push(
                $parts,
                array(
                    'headers' => $this->_parseHeaders($header),
                    'body'    => $body
                )
            );
        }

        return $parts;
    }

    //
    // Taken from http://www.php.net/manual/en/function.http-parse-headers.php#112917
    // and modified to: make folded work too, return status in first key.
    //
    // as suggested here: http://php.net/manual/en/function.http-parse-headers.php#112986
    //
    // adapted to private method, and force headers to lowercase for easy detection
    //
    private function _parseHeaders($raw_headers) {
        $headers = array();
        $key = ''; // [+]

        foreach(explode("\n", $raw_headers) as $i => $h) {
            $h = explode(':', $h, 2);
            $h[0] = strtolower($h[0]);

            if (isset($h[1])) {
                if (! isset($headers[$h[0]])) {
                    $headers[$h[0]] = trim($h[1]);
                }
                elseif (is_array($headers[$h[0]])) {
                    // $tmp = array_merge($headers[$h[0]], array(trim($h[1]))); // [-]
                    // $headers[$h[0]] = $tmp; // [-]
                    $headers[$h[0]] = array_merge($headers[$h[0]], array(trim($h[1]))); // [+]
                }
                else {
                    // $tmp = array_merge(array($headers[$h[0]]), array(trim($h[1]))); // [-]
                    // $headers[$h[0]] = $tmp; // [-]
                    $headers[$h[0]] = array_merge(array($headers[$h[0]]), array(trim($h[1]))); // [+]
                }

                $key = $h[0]; // [+]
            }
            else { // [+]
                if (substr($h[0], 0, 1) == "\t") {// [+]
                    $headers[$key] .= "\r\n\t".trim($h[0]); // [+]
                }
                elseif (! $key) {// [+]
                    $headers[0] = trim($h[0]);trim($h[0]); // [+]
                }
            } // [+]
        }

        return $headers;
    }

    private function _buildAttachmentContent(&$requestCfg, $attachments) {
        $boundary = Util::getUUID();
        $origContent = $requestCfg['content'];

        $requestCfg['content'] = '--' . $boundary . "\r\n";
        $requestCfg['content'] .= "Content-Type: application/json\r\n";
        $requestCfg['content'] .= "\r\n";
        $requestCfg['content'] .= $origContent;

        $attachmentContent = '';
        foreach ($attachments as $attachment) {
            $attachmentContent .= '--' . $boundary . "\r\n";
            $attachmentContent .= 'Content-Type: ' . $attachment->getContentType() . "\r\n";
            $attachmentContent .= "Content-Transfer-Encoding: binary\r\n";
            $attachmentContent .= "X-Experience-API-Hash: " . $attachment->getSha2() . "\r\n";
            $attachmentContent .= "\r\n";
            $attachmentContent .= $attachment->getContent();
            $attachmentContent .= "\r\n";
        }
        $attachmentContent .= '--' . $boundary . '--';

        $requestCfg['headers']['Content-Type'] = 'multipart/mixed; boundary=' . $boundary;
        $requestCfg['content'] .= "\r\n" . $attachmentContent;
    }

    public function about() {
        $response = $this->sendRequest('GET', 'about');

        if ($response->success) {
            $response->content = About::FromJSON($response->content);
        }

        return $response;
    }

    public function saveStatement($statement) {
        if (! $statement instanceof Statement) {
            $statement = new Statement($statement);
        }

        $requestCfg = array(
            'headers' => array(
                'Content-Type' => 'application/json'
            ),
            'content' => json_encode($statement->asVersion($this->version), JSON_UNESCAPED_SLASHES)
        );

        if ($statement->hasAttachmentsWithContent()) {
            $this->_buildAttachmentContent($requestCfg, $statement->getAttachments());
        }

        $method = 'POST';
        if ($statement->hasId()) {
            $method = 'PUT';
            $requestCfg['params'] = array('statementId' => $statement->getId());
        }

        $response = $this->sendRequest($method, 'statements', $requestCfg);

        if ($response->success) {
            if (! $statement->hasId()) {
                $parsed_content = json_decode($response->content, true);

                $statement->setId($parsed_content[0]);
            }

            //
            // save statement either returns no content when there is an id
            // or returns the id when there wasn't, either way the caller
            // may have called us with a statement configuration rather than
            // a Statement object, so provide them back the Statement object
            // as the content in either case on success
            //
            $response->content = $statement;
        }

        return $response;
    }

    public function saveStatements($statements) {
        $versioned_statements = array();
        $attachments_map = array();
        foreach ($statements as $i => $st) {
            if (! $st instanceof Statement) {
                $st = new Statement($st);
                $statements[$i] = $st;
            }
            $versioned_statements[$i] = $st->asVersion($this->version);

            if ($st->hasAttachmentsWithContent()) {
                foreach ($st->getAttachments() as $attachment) {
                    if (! isset($attachments_map[$attachment->getSha2()])) {
                        $attachments_map[$attachment->getSha2()] = $attachment;
                    }
                }
            }
        }

        $requestCfg = array(
            'headers' => array(
                'Content-Type' => 'application/json'
            ),
            'content' => json_encode($versioned_statements, JSON_UNESCAPED_SLASHES),
        );
        if (! empty($attachments_map)) {
            $this->_buildAttachmentContent($requestCfg, array_values($attachments_map));
        }

        $response = $this->sendRequest('POST', 'statements', $requestCfg);

        if ($response->success) {
            $parsed_content = json_decode($response->content, true);
            foreach ($parsed_content as $i => $stId) {
                $statements[$i]->setId($stId);
            }

            $response->content = $statements;
        }

        return $response;
    }

    public function retrieveStatement($id, $options = array()) {
        if (! isset($options['voided'])) {
            $options['voided'] = false;
        }
        if (! isset($options['attachments'])) {
            $options['attachments'] = false;
        }

        $params = array();
        if ($options['voided']) {
            $params['voidedStatementId'] = $id;
        }
        else {
            $params['statementId'] = $id;
        }
        if ($options['attachments']) {
            $params['attachments'] = 'true';
        }

        $response = $this->sendRequest(
            'GET',
            'statements',
            array(
                'params' => $params
            )
        );

        if ($response->success) {
            if (is_array($response->content)) {
                $orig = $response->httpResponse['_multipartContent'] = $response->content;

                $response->content = Statement::FromJSON($orig[0]['body']);

                $attachmentsByHash = array();
                for ($i = 1; $i < count($orig); $i++) {
                    $attachmentsByHash[$orig[$i]['headers']['x-experience-api-hash']] = $orig[$i];
                }

                foreach ($response->content->getAttachments() as $attachment) {
                    if (array_key_exists($attachment->getSha2(), $attachmentsByHash)) {
                        $attachment->setContent($attachmentsByHash[$attachment->getSha2()]['body']);
                    }
                }
            }
            else {
                $response->content = Statement::FromJSON($response->content);
            }
        }

        return $response;
    }

    public function retrieveVoidedStatement($id, $options = array()) {
        $options['voided'] = true;
        return $this->retrieveStatement($id, $options);
    }

    private function _queryStatementsRequestParams($query) {
        $result = array();

        foreach (array('agent') as $k) {
            if (isset($query[$k])) {
                $result[$k] = json_encode($query[$k]->asVersion($this->version));
            }
        }
        foreach (
            array(
                'verb',
                'activity',
            ) as $k
        ) {
            if (isset($query[$k])) {
                if (is_string($query[$k])) {
                    $result[$k] = $query[$k];
                }
                else {
                    $result[$k] = $query[$k]->getId();
                }
            }
        }
        foreach (
            array(
                'ascending',
                'related_activities',
                'related_agents',
                'attachments',
            ) as $k
        ) {
            if (isset($query[$k])) {
                $result[$k] = $query[$k] ? 'true' : 'false';
            }
        }
        foreach (
            array(
                'registration',
                'since',
                'until',
                'limit',
                'format',
            ) as $k
        ) {
            if (isset($query[$k])) {
                $result[$k] = $query[$k];
            }
        }

        return $result;
    }

    private function _queryStatementsResult(&$response) {
        if (is_array($response->content)) {
            $orig = $response->httpResponse['_multipartContent'] = $response->content;

            $response->content = StatementsResult::FromJSON($orig[0]['body']);

            $attachmentsByHash = array();
            for ($i = 1; $i < count($orig); $i++) {
                $attachmentsByHash[$orig[$i]['headers']['x-experience-api-hash']] = $orig[$i];
            }

            foreach ($response->content->getStatements() as $st) {
                foreach ($st->getAttachments() as $attachment) {
                    if (array_key_exists($attachment->getSha2(), $attachmentsByHash)) {
                        $attachment->setContent($attachmentsByHash[$attachment->getSha2()]['body']);
                    }
                }
            }

            return;
        }

        $response->content = StatementsResult::fromJSON($response->content);

        return;
    }

    public function queryStatements($query) {
        $requestCfg = array(
            'params' => $this->_queryStatementsRequestParams($query),
        );
        if (func_num_args() > 1) {
            $options = func_get_arg(1);

            if (isset($options)) {
                if (isset($options['headers'])) {
                    $requestCfg['headers'] = $options['headers'];
                }
            }
        }

        $response = $this->sendRequest('GET', 'statements', $requestCfg);

        if ($response->success) {
            $this->_queryStatementsResult($response);
        }

        return $response;
    }

    public function moreStatements($moreUrl) {
        if ($moreUrl instanceof StatementsResult) {
            $moreUrl = $moreUrl->getMore();
        }
        $moreUrl = $this->getEndpointServerRoot() . $moreUrl;

        $response = $this->sendRequest('GET', $moreUrl);

        if ($response->success) {
            $this->_queryStatementsResult($response);
        }

        return $response;
    }

    public function retrieveStateIds($activity, $agent) {
        if (! $activity instanceof Activity) {
            $activity = new Activity($activity);
        }
        if (! $agent instanceof Agent) {
            $agent = new Agent($agent);
        }

        $requestCfg = array(
            'params' => array(
                'activityId' => $activity->getId(),
                'agent'      => json_encode($agent->asVersion($this->version)),
            ),
        );
        if (func_num_args() > 2) {
            $options = func_get_arg(2);
            if (isset($options)) {
                if (isset($options['registration'])) {
                    $requestCfg['params']['registration'] = $options['registration'];
                }
                if (isset($options['since'])) {
                    $requestCfg['params']['since'] = $options['since'];
                }
            }
        }

        $response = $this->sendRequest('GET', 'activities/state', $requestCfg);

        if ($response->success) {
            $response->content = json_decode($response->content);
        }

        return $response;
    }

    public function retrieveState($activity, $agent, $id) {
        if (! $activity instanceof Activity) {
            $activity = new Activity($activity);
        }
        if (! $agent instanceof Agent) {
            $agent = new Agent($agent);
        }
        $registration = null;

        $requestCfg = array(
            'params' => array(
                'activityId' => $activity->getId(),
                'agent'      => json_encode($agent->asVersion($this->version)),
                'stateId'    => $id,
            ),
            'ignore404' => true,
        );
        if (func_num_args() > 3) {
            $options = func_get_arg(3);
            if (isset($options)) {
                if (isset($options['registration'])) {
                    $requestCfg['params']['registration'] = $registration = $options['registration'];
                }
            }
        }

        $response = $this->sendRequest('GET', 'activities/state', $requestCfg);

        if ($response->success) {
            $doc = new State(
                array(
                    'id'       => $id,
                    'content'  => $response->content,
                    'activity' => $activity,
                    'agent'    => $agent,
                )
            );
            if (isset($registration)) {
                $doc->setRegistration($registration);
            }
            if (isset($response->httpResponse['headers']['lastModified'])) {
                $doc->setTimestamp($response->httpResponse['headers']['lastModified']);
            }
            if (isset($response->httpResponse['headers']['contentType'])) {
                $doc->setContentType($response->httpResponse['headers']['contentType']);
            }
            if (isset($response->httpResponse['headers']['etag'])) {
                $doc->setEtag($response->httpResponse['headers']['etag']);
            }

            $response->content = $doc;
        }

        return $response;
    }

    public function saveState($activity, $agent, $id, $content) {
        if (! $activity instanceof Activity) {
            $activity = new Activity($activity);
        }
        if (! $agent instanceof Agent) {
            $agent = new Agent($agent);
        }

        $contentType = 'application/octet-stream';

        $requestCfg = array(
            'headers' => array(
                'Content-Type' => $contentType,
            ),
            'params' => array(
                'activityId' => $activity->getId(),
                'agent'      => json_encode($agent->asVersion($this->version)),
                'stateId'    => $id,
            ),
            'content' => $content,
        );
        $registration = null;
        if (func_num_args() > 4) {
            $options = func_get_arg(4);
            if (isset($options)) {
                if (isset($options['contentType'])) {
                    $requestCfg['headers']['Content-Type'] = $contentType = $options['contentType'];
                }
                if (isset($options['etag'])) {
                    $requestCfg['headers']['If-Match'] = $options['etag'];
                }
                if (isset($options['registration'])) {
                    $requestCfg['params']['registration'] = $registration = $options['registration'];
                }
            }
        }

        $response = $this->sendRequest('PUT', 'activities/state', $requestCfg);

        if ($response->success) {
            $doc = new State(
                array(
                    'id'          => $id,
                    'content'     => $content,
                    'contentType' => $contentType,
                    'etag'        => sha1($content),
                    'activity'    => $activity,
                    'agent'       => $agent,
                )
            );
            if (isset($registration)) {
                $doc->setRegistration($registration);
            }
            if (isset($response->httpResponse['headers']['date'])) {
                $doc->setTimestamp($response->httpResponse['headers']['date']);
            }

            $response->content = $doc;
        }

        return $response;
    }

    //
    // this is a separate private method because the implementation
    // of deleteState and clearState are essentially identical but
    // I didn't want to make it easy to call deleteState accidentally
    // without an id therefore clearing all of the state when only
    // one id was desired to be deleted, so clearState is an explicit
    // separate method signature
    //
    // TODO: Etag?
    private function _deleteState($activity, $agent, $id) {
        if (! $activity instanceof Activity) {
            $activity = new Activity($activity);
        }
        if (! $agent instanceof Agent) {
            $agent = new Agent($agent);
        }

        $requestCfg = array(
            'params' => array(
                'activityId' => $activity->getId(),
                'agent'      => json_encode($agent->asVersion($this->version)),
            )
        );
        if (isset($id)) {
            $requestCfg['params']['stateId'] = $id;
        }

        if (func_num_args() > 3) {
            $options = func_get_arg(3);
            if (isset($options)) {
                if (isset($options['registration'])) {
                    $requestCfg['params']['registration'] = $options['registration'];
                }
            }
        }

        $response = $this->sendRequest('DELETE', 'activities/state', $requestCfg);

        return $response;
    }

    public function deleteState($activity, $agent, $id) {
        return call_user_func_array(array($this, '_deleteState'), func_get_args());
    }

    public function clearState($activity, $agent) {
        $args = array($activity, $agent, null);

        $numArgs = func_num_args();
        if ($numArgs > 2) {
            $args = array_merge($args, array_slice(func_get_args(), 2));
        }

        return call_user_func_array(array($this, '_deleteState'), $args);
    }

    public function retrieveActivityProfileIds($activity) {
        if (! $activity instanceof Activity) {
            $activity = new Activity($activity);
        }

        $requestCfg = array(
            'params' => array(
                'activityId' => $activity->getId()
            )
        );
        if (func_num_args() > 1) {
            $options = func_get_arg(1);
            if (isset($options)) {
                if (isset($options['since'])) {
                    $requestCfg['params']['since'] = $options['since'];
                }
            }
        }

        $response = $this->sendRequest('GET', 'activities/profile', $requestCfg);

        if ($response->success) {
            $response->content = json_decode($response->content);
        }

        return $response;
    }

    public function retrieveActivityProfile($activity, $id) {
        if (! $activity instanceof Activity) {
            $activity = new Activity($activity);
        }
        $response = $this->sendRequest(
            'GET',
            'activities/profile',
            array(
                'params' => array(
                    'activityId' => $activity->getId(),
                    'profileId'  => $id,
                ),
                'ignore404' => true,
            )
        );

        if ($response->success) {
            $doc = new ActivityProfile(
                array(
                    'id'       => $id,
                    'content'  => $response->content,
                    'activity' => $activity,
                )
            );
            if (isset($response->httpResponse['headers']['lastModified'])) {
                $doc->setTimestamp($response->httpResponse['headers']['lastModified']);
            }
            if (isset($response->httpResponse['headers']['contentType'])) {
                $doc->setContentType($response->httpResponse['headers']['contentType']);
            }
            if (isset($response->httpResponse['headers']['etag'])) {
                $doc->setEtag($response->httpResponse['headers']['etag']);
            }

            $response->content = $doc;
        }

        return $response;
    }

    public function saveActivityProfile($activity, $id, $content) {
        if (! $activity instanceof Activity) {
            $activity = new Activity($activity);
        }

        $contentType = 'application/octet-stream';

        $requestCfg = array(
            'headers' => array(
                'Content-Type' => $contentType,
            ),
            'params' => array(
                'activityId' => $activity->getId(),
                'profileId'  => $id,
            ),
            'content' => $content,
        );
        if (func_num_args() > 3) {
            $options = func_get_arg(3);
            if (isset($options)) {
                if (isset($options['contentType'])) {
                    $requestCfg['headers']['Content-Type'] = $contentType = $options['contentType'];
                }
                if (isset($options['etag'])) {
                    $requestCfg['headers']['If-Match'] = $options['etag'];
                }
                else {
                    $requestCfg['headers']['If-None-Match'] = '*';
                }
            }
        }

        $response = $this->sendRequest('PUT', 'activities/profile', $requestCfg);

        if ($response->success) {
            $doc = new ActivityProfile(
                array(
                    'id'          => $id,
                    'content'     => $content,
                    'contentType' => $contentType,
                    'etag'        => sha1($content),
                    'activity'    => $activity,
                )
            );
            if (isset($response->httpResponse['headers']['date'])) {
                $doc->setTimestamp($response->httpResponse['headers']['date']);
            }

            $response->content = $doc;
        }

        return $response;
    }

    // TODO: Etag?
    public function deleteActivityProfile($activity, $id) {
        if (! $activity instanceof Activity) {
            $activity = new Activity($activity);
        }
        $response = $this->sendRequest(
            'DELETE',
            'activities/profile',
            array(
                'params' => array(
                    'activityId' => $activity->getId(),
                    'profileId'  => $id,
                ),
            )
        );

        return $response;
    }

    public function retrieveActivity($activityid) {
        $headers = array('Accept-language: *');
        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $headers = array('Accept-language: ' . $_SERVER['HTTP_ACCEPT_LANGUAGE'] . ', *');
        }

        $response = $this->sendRequest(
            'GET',
            'activities',
            array(
                'params' => array(
                    'activityId' => $activityid,
                ),
                'headers' => $headers
            )
        );

        if ($response->success) {
            $response->content = new Activity(json_decode($response->content, true));
        }

        return $response;
    }

    // TODO: groups?
    public function retrieveAgentProfileIds($agent) {
        if (! $agent instanceof Agent) {
            $agent = new Agent($agent);
        }

        $requestCfg = array(
            'params' => array(
                'agent' => json_encode($agent->asVersion($this->version))
            )
        );
        if (func_num_args() > 1) {
            $options = func_get_arg(1);
            if (isset($options)) {
                if (isset($options['since'])) {
                    $requestCfg['params']['since'] = $options['since'];
                }
            }
        }

        $response = $this->sendRequest('GET', 'agents/profile', $requestCfg);

        if ($response->success) {
            $response->content = json_decode($response->content);
        }

        return $response;
    }

    public function retrieveAgentProfile($agent, $id) {
        if (! $agent instanceof Agent) {
            $agent = new Agent($agent);
        }
        $response = $this->sendRequest(
            'GET',
            'agents/profile',
            array(
                'params' => array(
                    'agent'     => json_encode($agent->asVersion($this->version)),
                    'profileId' => $id,
                ),
                'ignore404' => true,
            )
        );

        if ($response->success) {
            $doc = new AgentProfile(
                array(
                    'id'      => $id,
                    'content' => $response->content,
                    'agent'   => $agent,
                )
            );
            if (isset($response->httpResponse['headers']['lastModified'])) {
                $doc->setTimestamp($response->httpResponse['headers']['lastModified']);
            }
            if (isset($response->httpResponse['headers']['contentType'])) {
                $doc->setContentType($response->httpResponse['headers']['contentType']);
            }
            if (isset($response->httpResponse['headers']['etag'])) {
                $doc->setEtag($response->httpResponse['headers']['etag']);
            }

            $response->content = $doc;
        }

        return $response;
    }

    public function saveAgentProfile($agent, $id, $content) {
        if (! $agent instanceof Agent) {
            $agent = new Agent($agent);
        }

        $contentType = 'application/octet-stream';

        $requestCfg = array(
            'headers' => array(
                'Content-Type' => $contentType,
            ),
            'params' => array(
                'agent'     => json_encode($agent->asVersion($this->version)),
                'profileId' => $id,
            ),
            'content' => $content,
        );
        if (func_num_args() > 3) {
            $options = func_get_arg(3);
            if (isset($options)) {
                if (isset($options['contentType'])) {
                    $requestCfg['headers']['Content-Type'] = $contentType = $options['contentType'];
                }
                if (isset($options['etag'])) {
                    $requestCfg['headers']['If-Match'] = $options['etag'];
                }
                else {
                    $requestCfg['headers']['If-None-Match'] = '*';
                }
            }
        }

        $response = $this->sendRequest('PUT', 'agents/profile', $requestCfg);

        if ($response->success) {
            $doc = new AgentProfile(
                array(
                    'id' => $id,
                    'content' => $content,
                    'contentType' => $contentType,
                    'etag' => sha1($content),
                    'agent' => $agent,
                )
            );
            if (isset($response->httpResponse['headers']['date'])) {
                $doc->setTimestamp($response->httpResponse['headers']['date']);
            }

            $response->content = $doc;
        }

        return $response;
    }

    // TODO: Etag?
    public function deleteAgentProfile($agent, $id) {
        if (! $agent instanceof Agent) {
            $agent = new Agent($agent);
        }
        $response = $this->sendRequest(
            'DELETE',
            'agents/profile',
            array(
                'params' => array(
                    'agent'     => json_encode($agent->asVersion($this->version)),
                    'profileId' => $id,
                ),
            )
        );

        return $response;
    }

    public function retrievePerson($agent) {
        if (! $agent instanceof Agent) {
            $agent = new Agent($agent);
        }
        $response = $this->sendRequest(
            'GET',
            'agents',
            array(
                'params' => array(
                    'agent' => json_encode($agent->asVersion($this->version)),
                )
            )
        );

        if ($response->success) {
            $response->content = new Person(json_decode($response->content, true));
        }

        return $response;
    }

    // FEATURE: check is URL
    public function setEndpoint($value) {
        if (substr($value, -1) != "/") {
            $value .= "/";
        }
        $this->endpoint = $value;
        return $this;
    }
    public function getEndpoint() { return $this->endpoint; }
    public function getEndpointServerRoot() {
        $parsed = parse_url($this->endpoint);

        $root = $parsed['scheme'] . '://' . $parsed['host'];
        if (isset($parsed['port'])) {
            $root .= ":" . $parsed['port'];
        }

        return $root;
    }

    public function setVersion($value) {
        if (! in_array($value, Version::supported(), true)) {
            throw new \InvalidArgumentException("Unsupported version: $value");
        }
        $this->version = $value;
        return $this;
    }
    public function getVersion() { return $this->version; }

    public function setAuth() {
        $_num_args = func_num_args();
        if ($_num_args == 1) {
            $this->auth = func_get_arg(0);
        }
        elseif ($_num_args == 2) {
            $this->auth = 'Basic ' . base64_encode(func_get_arg(0) . ':' . func_get_arg(1));
        }
        else {
            throw new \BadMethodCallException('setAuth requires 1 or 2 arguments');
        }
        return $this;
    }
    public function getAuth() { return $this->auth; }

    public function setProxy($value) {
        $this->proxy = $value;
        return $this;
    }
    public function getProxy() { return $this->proxy; }

    public function setHeaders($value) {
        $this->headers = $value;
        return $this;
    }
    public function getHeaders() { return $this->headers; }
}
