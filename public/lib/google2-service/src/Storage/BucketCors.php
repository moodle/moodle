<?php
/*
 * Copyright 2014 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 */

namespace Google\Service\Storage;

class BucketCors extends \Google\Collection
{
  protected $collection_key = 'responseHeader';
  /**
   * The value, in seconds, to return in the  Access-Control-Max-Age header used
   * in preflight responses.
   *
   * @var int
   */
  public $maxAgeSeconds;
  /**
   * The list of HTTP methods on which to include CORS response headers, (GET,
   * OPTIONS, POST, etc) Note: "*" is permitted in the list of methods, and
   * means "any method".
   *
   * @var string[]
   */
  public $method;
  /**
   * The list of Origins eligible to receive CORS response headers. Note: "*" is
   * permitted in the list of origins, and means "any Origin".
   *
   * @var string[]
   */
  public $origin;
  /**
   * The list of HTTP headers other than the simple response headers to give
   * permission for the user-agent to share across domains.
   *
   * @var string[]
   */
  public $responseHeader;

  /**
   * The value, in seconds, to return in the  Access-Control-Max-Age header used
   * in preflight responses.
   *
   * @param int $maxAgeSeconds
   */
  public function setMaxAgeSeconds($maxAgeSeconds)
  {
    $this->maxAgeSeconds = $maxAgeSeconds;
  }
  /**
   * @return int
   */
  public function getMaxAgeSeconds()
  {
    return $this->maxAgeSeconds;
  }
  /**
   * The list of HTTP methods on which to include CORS response headers, (GET,
   * OPTIONS, POST, etc) Note: "*" is permitted in the list of methods, and
   * means "any method".
   *
   * @param string[] $method
   */
  public function setMethod($method)
  {
    $this->method = $method;
  }
  /**
   * @return string[]
   */
  public function getMethod()
  {
    return $this->method;
  }
  /**
   * The list of Origins eligible to receive CORS response headers. Note: "*" is
   * permitted in the list of origins, and means "any Origin".
   *
   * @param string[] $origin
   */
  public function setOrigin($origin)
  {
    $this->origin = $origin;
  }
  /**
   * @return string[]
   */
  public function getOrigin()
  {
    return $this->origin;
  }
  /**
   * The list of HTTP headers other than the simple response headers to give
   * permission for the user-agent to share across domains.
   *
   * @param string[] $responseHeader
   */
  public function setResponseHeader($responseHeader)
  {
    $this->responseHeader = $responseHeader;
  }
  /**
   * @return string[]
   */
  public function getResponseHeader()
  {
    return $this->responseHeader;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BucketCors::class, 'Google_Service_Storage_BucketCors');
