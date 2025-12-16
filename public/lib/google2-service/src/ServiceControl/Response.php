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

namespace Google\Service\ServiceControl;

class Response extends \Google\Model
{
  /**
   * The amount of time it takes the backend service to fully respond to a
   * request. Measured from when the destination service starts to send the
   * request to the backend until when the destination service receives the
   * complete response from the backend.
   *
   * @var string
   */
  public $backendLatency;
  /**
   * The HTTP response status code, such as `200` and `404`.
   *
   * @var string
   */
  public $code;
  /**
   * The HTTP response headers. If multiple headers share the same key, they
   * must be merged according to HTTP spec. All header keys must be lowercased,
   * because HTTP header keys are case-insensitive.
   *
   * @var string[]
   */
  public $headers;
  /**
   * The HTTP response size in bytes. If unknown, it must be -1.
   *
   * @var string
   */
  public $size;
  /**
   * The timestamp when the `destination` service sends the last byte of the
   * response.
   *
   * @var string
   */
  public $time;

  /**
   * The amount of time it takes the backend service to fully respond to a
   * request. Measured from when the destination service starts to send the
   * request to the backend until when the destination service receives the
   * complete response from the backend.
   *
   * @param string $backendLatency
   */
  public function setBackendLatency($backendLatency)
  {
    $this->backendLatency = $backendLatency;
  }
  /**
   * @return string
   */
  public function getBackendLatency()
  {
    return $this->backendLatency;
  }
  /**
   * The HTTP response status code, such as `200` and `404`.
   *
   * @param string $code
   */
  public function setCode($code)
  {
    $this->code = $code;
  }
  /**
   * @return string
   */
  public function getCode()
  {
    return $this->code;
  }
  /**
   * The HTTP response headers. If multiple headers share the same key, they
   * must be merged according to HTTP spec. All header keys must be lowercased,
   * because HTTP header keys are case-insensitive.
   *
   * @param string[] $headers
   */
  public function setHeaders($headers)
  {
    $this->headers = $headers;
  }
  /**
   * @return string[]
   */
  public function getHeaders()
  {
    return $this->headers;
  }
  /**
   * The HTTP response size in bytes. If unknown, it must be -1.
   *
   * @param string $size
   */
  public function setSize($size)
  {
    $this->size = $size;
  }
  /**
   * @return string
   */
  public function getSize()
  {
    return $this->size;
  }
  /**
   * The timestamp when the `destination` service sends the last byte of the
   * response.
   *
   * @param string $time
   */
  public function setTime($time)
  {
    $this->time = $time;
  }
  /**
   * @return string
   */
  public function getTime()
  {
    return $this->time;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Response::class, 'Google_Service_ServiceControl_Response');
